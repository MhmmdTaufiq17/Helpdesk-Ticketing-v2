<?php

namespace App\Services;

use App\Models\Ticket;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GroqAIService
{
    protected $apiKey;
    protected $apiUrl = 'https://api.groq.com/openai/v1/chat/completions';

    public function __construct()
    {
        // ✅ Ambil API key dari environment variable
        $this->apiKey = env('GROQ_API_KEY');

        // Optional: Log warning jika API key kosong
        if (empty($this->apiKey)) {
            Log::warning('GROQ_API_KEY is not set in .env file');
        }
    }

    /**
     * Analisis prioritas tiket berdasarkan title & description
     */
    public function analyzePriority(string $title, string $description, ?string $category = null): string
    {
        $prompt = "Analisis tiket support berikut dan tentukan prioritasnya (HIGH/MEDIUM/LOW).

Judul: {$title}
Deskripsi: {$description}" . ($category ? "\nKategori: {$category}" : '') . "

Aturan:
- HIGH: Masalah kritis, sistem down, error fatal, kehilangan data, tidak bisa bekerja
- MEDIUM: Mengganggu tapi ada solusi sementara, fitur error tapi masih bisa pakai
- LOW: Pertanyaan umum, saran, masalah minor, typo

JAWAB hanya satu kata: HIGH, MEDIUM, atau LOW.";

        $response = $this->callGroq($prompt);
        $priority = strtoupper(trim($response));

        if (!in_array($priority, ['HIGH', 'MEDIUM', 'LOW'])) {
            Log::warning('Groq priority invalid: ' . $response);
            return 'MEDIUM';
        }

        return $priority;
    }

    /**
     * Buat rangkuman dari laporan pelapor (title + description)
     */
    public function generateSummary(string $title, string $description, ?string $category = null): string
    {
        $prompt = "Buat rangkuman SINGKAT (maksimal 3 kalimat) dari MASALAH yang dilaporkan pelapor. Jangan sebutkan prioritas atau status. Fokus pada INTI MASALAH saja.

JUDUL LAPORAN: {$title}
DESKRIPSI LAPORAN: {$description}" . ($category ? "\nKATEGORI: {$category}" : '') . "

RANGKUMAN (3 kalimat maksimal, bahasa Indonesia):";

        $response = $this->callGroq($prompt);

        // Validasi: jika response aneh (MEDIUM/HIGH/LOW atau terlalu pendek)
        $invalidKeywords = ['HIGH', 'MEDIUM', 'LOW', 'high', 'medium', 'low'];
        $isInvalid = false;
        foreach ($invalidKeywords as $keyword) {
            if (trim($response) === $keyword || strpos($response, $keyword) === 0) {
                $isInvalid = true;
                break;
            }
        }

        if ($isInvalid || strlen(trim($response)) < 20) {
            // Fallback: buat rangkuman manual
            $shortDesc = strlen($description) > 150 ? substr($description, 0, 150) . '...' : $description;
            return "📌 {$title}. {$shortDesc}";
        }

        return $response;
    }

    /**
     * Generate balasan AI untuk admin
     */
    public function generateReply(Ticket $ticket, $recentMessages = []): string
    {
        $chatHistory = '';
        if (!empty($recentMessages)) {
            $chatHistory = "\n\nRIWAYAT PERCAKAPAN TERAKHIR:\n";
            foreach ($recentMessages as $msg) {
                $sender = $msg['sender_type'] === 'admin' ? 'Admin' : 'Pelapor';
                $chatHistory .= "- {$sender}: {$msg['message']}\n";
            }
        }

        $prompt = "Anda adalah customer support. Buatkan BALASAN yang profesional dan membantu untuk tiket berikut.

JUDUL: {$ticket->title}
DESKRIPSI: {$ticket->description}
PRIORITAS: " . ($ticket->priority ?? 'Belum ditentukan') . "
STATUS: {$ticket->status}" . $chatHistory . "

INSTRUKSI: Balas dalam bahasa Indonesia, tunjukkan empati, berikan solusi konkret. Maksimal 5 kalimat. Jangan sebut 'sebagai AI'.

BALASAN:";

        $response = $this->callGroq($prompt);

        if (strlen(trim($response)) < 20) {
            return "Terima kasih atas laporan Anda. Tim kami akan segera menindaklanjuti masalah ini. Kami akan menginformasikan perkembangan lebih lanjut.";
        }

        return $response;
    }

    /**
     * Panggil API Groq dengan model yang aktif
     */
    protected function callGroq(string $prompt): string
    {
        // Cek apakah API key tersedia
        if (empty($this->apiKey)) {
            Log::error('GROQ_API_KEY is missing. Please add it to your .env file');
            return $this->getFallbackResponse($prompt);
        }

        try {
            // ✅ Model yang masih aktif per April 2026
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->apiKey,
                'Content-Type' => 'application/json',
            ])->timeout(30)->post($this->apiUrl, [
                'model' => 'llama-3.3-70b-versatile', // ✅ Ganti dengan model aktif
                'messages' => [
                    [
                        'role' => 'system',
                        'content' => 'Anda adalah asisten customer support yang membantu dan profesional.'
                    ],
                    [
                        'role' => 'user',
                        'content' => $prompt
                    ]
                ],
                'temperature' => 0.3,
                'max_tokens' => 500,
            ]);

            if ($response->successful()) {
                $result = $response->json()['choices'][0]['message']['content'] ?? '';
                if (!empty($result)) {
                    return $result;
                }
            }

            // Log error detail
            Log::error('Groq API Error: ' . $response->body());
            return $this->getFallbackResponse($prompt);

        } catch (\Exception $e) {
            Log::error('Groq Exception: ' . $e->getMessage());
            return $this->getFallbackResponse($prompt);
        }
    }

    /**
     * Fallback response ketika API error
     */
    protected function getFallbackResponse(string $prompt): string
    {
        // Deteksi jenis prompt
        if (strpos($prompt, 'rangkuman') !== false || strpos($prompt, 'RANGKUMAN') !== false) {
            // Extract title dan description dari prompt
            preg_match('/JUDUL LAPORAN: (.*?)\n/', $prompt, $titleMatch);
            preg_match('/DESKRIPSI LAPORAN: (.*?)(\n|$)/', $prompt, $descMatch);

            $title = $titleMatch[1] ?? 'Laporan';
            $description = $descMatch[1] ?? '';
            $shortDesc = strlen($description) > 100 ? substr($description, 0, 100) . '...' : $description;

            return "📌 {$title}. {$shortDesc}";
        }

        if (strpos($prompt, 'prioritas') !== false || strpos($prompt, 'HIGH/MEDIUM/LOW') !== false) {
            return 'MEDIUM';
        }

        if (strpos($prompt, 'BALASAN') !== false || strpos($prompt, 'balasan') !== false) {
            return "Terima kasih atas laporan Anda. Tim support kami akan segera menindaklanjuti dan menginformasikan perkembangan lebih lanjut.";
        }

        return "Terima kasih atas laporan Anda. Tim support kami akan segera menindaklanjuti.";
    }
}
