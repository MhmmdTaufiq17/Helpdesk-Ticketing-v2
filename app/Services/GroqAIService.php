<?php

namespace App\Services;

use App\Models\Ticket;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GroqAIService
{
    protected $apiKey;
    protected $apiUrl = 'https://api.groq.com/openai/v1/chat/completions';

    protected string $systemPrompt =
        'Anda adalah agen customer support senior yang berpengalaman di bidang IT dan layanan pelanggan.' . "\n"
        . 'Tugas Anda adalah menganalisis tiket support dan memberikan respons yang:' . "\n"
        . '- SPESIFIK: sebutkan masalah konkret berdasarkan judul dan deskripsi tiket' . "\n"
        . '- ACTIONABLE: berikan langkah-langkah nyata yang bisa langsung dilakukan' . "\n"
        . '- PROFESIONAL: gunakan bahasa formal namun ramah' . "\n"
        . '- FOKUS: tidak berputar-putar, langsung ke inti masalah' . "\n\n"
        . 'Anda TIDAK boleh:' . "\n"
        . '- Menyebut diri sebagai "AI" atau "sistem otomatis"' . "\n"
        . '- Memberikan jawaban generik seperti "kami akan segera memproses"' . "\n"
        . '- Mengulang-ulang kalimat yang sama';

    public function __construct()
    {
        $this->apiKey = env('GROQ_API_KEY');

        if (empty($this->apiKey)) {
            Log::warning('GROQ_API_KEY is not set in .env file');
        }
    }

    // ─── Analisis Prioritas ────────────────────────────────────────────────────

    public function analyzePriority(string $title, string $description, ?string $category = null): string
    {
        $categoryLine = $category ? "\nKategori: {$category}" : '';

        $prompt =
            'Hei, saya punya tiket support ini nih. Bisa bantu analisis seberapa urgent ya?' . "\n\n"
            . "Judul: {$title}\n"
            . "Deskripsi: {$description}{$categoryLine}\n\n"
            . 'Berdasarkan pengalamanmu sebagai support, prioritasnya HIGH, MEDIUM, atau LOW?' . "\n"
            . 'HIGH kalau sistem down total, data hilang, atau berdampak kritis ke banyak orang.' . "\n"
            . 'MEDIUM kalau masih bisa workaround tapi perlu diperbaiki segera.' . "\n"
            . 'LOW kalau pertanyaan biasa atau masalah minor.' . "\n\n"
            . 'Jawab cuma satu kata: HIGH, MEDIUM, atau LOW.';

        $response = $this->callGroq($prompt, temperature: 0.1, maxTokens: 10);
        $priority  = strtoupper(trim($response));

        foreach (['HIGH', 'MEDIUM', 'LOW'] as $level) {
            if (str_contains($priority, $level)) {
                return $level;
            }
        }

        Log::warning('Groq priority tidak valid: ' . $response);
        return 'MEDIUM';
    }

    // ─── Generate Summary ─────────────────────────────────────────────────────

    public function generateSummary(string $title, string $description, ?string $category = null): string
    {
        $categoryLine = $category ? "\nKategori      : {$category}" : '';

        $prompt =
            'Bisa bantu buat rangkuman singkat dari tiket support ini? Buat kayak orang biasa nulis, jangan terlalu formal.' . "\n\n"
            . "Judul: {$title}\n"
            . "Deskripsi: {$description}{$categoryLine}\n\n"
            . 'Rangkum dalam 2-3 kalimat yang natural, seolah-olah kamu lagi jelasin ke temen. ' . "\n"
            . 'Sebutkan apa masalahnya, dampaknya ke user, dan detail penting lainnya. ' . "\n"
            . 'Gunakan bahasa Indonesia yang friendly tapi tetap profesional.';

        $response = $this->callGroq($prompt, temperature: 0.3, maxTokens: 200);

        $clean = trim($response);
        if ($this->looksLikePriorityWord($clean) || mb_strlen($clean) < 30) {
            $short = mb_strlen($description) > 200
                ? mb_substr($description, 0, 200) . '...'
                : $description;
            return "{$title}. {$short}";
        }

        return $clean;
    }

    // ─── Generate Reply ───────────────────────────────────────────────────────

    /**
     * Buat balasan admin yang kontekstual berdasarkan riwayat percakapan.
     *
     * @param  Ticket  $ticket
     * @param  array   $recentMessages  [['sender_type' => 'admin'|'user', 'message' => '...']]
     */
    public function generateReply(Ticket $ticket, array $recentMessages = []): string
    {
        // Ambil maksimal 10 pesan terakhir untuk konteks analisis
        $messages     = array_slice($recentMessages, -10);
        $messageCount = count($messages);
        $isFirstReply = $messageCount === 0;

        $priority = $ticket->priority ?? 'MEDIUM';
        $status   = $ticket->status   ?? 'open';

        $urgencyNote = match (strtoupper($priority)) {
            'HIGH'  => 'Ini tiket PRIORITAS TINGGI - tunjukkan rasa urgensi, tawarkan eskalasi atau jadwal penanganan konkret.',
            'LOW'   => 'Ini tiket prioritas rendah - tetap bantu dengan tulus, tapi tidak perlu terlalu formal.',
            default => 'Ini tiket prioritas sedang - seimbangkan empati dan solusi praktis.',
        };

        // ── Bangun blok riwayat chat ──────────────────────────────────────────
        $historyBlock = '';
        if (!$isFirstReply) {
            $lines = array_map(function ($msg) {
                $sender = $msg['sender_type'] === 'admin' ? 'Admin' : 'Pelapor';
                return "  [{$sender}] {$msg['message']}";
            }, $messages);
            $historyBlock = "\n\n=== RIWAYAT PERCAKAPAN ({$messageCount} pesan terakhir) ===\n"
                . implode("\n", $lines);
        }

        // ── Instruksi sapaan berbeda untuk balasan pertama vs lanjutan ────────
        if ($isFirstReply) {
            $greetingInstruction =
                'BALASAN PERTAMA - Ini adalah respons pertama untuk tiket ini.' . "\n"
                . 'Mulai dengan sapaan hangat seperti "Halo [nama jika ada]," atau "Selamat pagi/siang,"' . "\n"
                . 'kemudian langsung sampaikan pemahaman Anda tentang masalah yang dilaporkan.';
        } else {
            $greetingInstruction =
                'BALASAN LANJUTAN - Percakapan sudah berlangsung, jangan ulangi sapaan awal.' . "\n"
                . 'JANGAN mulai dengan "Halo", "Selamat pagi", atau sapaan pembuka apapun.' . "\n"
                . 'Analisis riwayat percakapan di atas, lalu langsung berikan respons yang relevan.' . "\n"
                . 'Fokus pada: apakah masalah berkembang? apakah langkah sebelumnya sudah dicoba? apa update terbaru?';
        }

        // ── Instruksi konteks untuk balasan lanjutan ──────────────────────────
        $contextInstruction = '';
        if (!$isFirstReply) {
            $contextInstruction =
                "\n\n=== ANALISIS KONTEKS CHAT ===" . "\n"
                . 'Sebelum menjawab, perhatikan:' . "\n"
                . '- Apa yang sudah disampaikan admin sebelumnya? Jangan ulangi hal yang sama.' . "\n"
                . '- Apakah pelapor sudah mencoba langkah yang disarankan?' . "\n"
                . '- Apakah ada informasi baru dari pelapor yang perlu ditindaklanjuti?' . "\n"
                . '- Sesuaikan jawaban dengan kondisi terkini dari percakapan.';
        }

        $prompt =
            'Bisa bantu buat balasan untuk tiket support ini? Buat kayak orang support yang friendly dan helpful.' . "\n\n"
            . "Judul: {$ticket->title}\n"
            . "Deskripsi: {$ticket->description}\n"
            . "Prioritas: {$priority}\n"
            . "Status: {$status}"
            . $historyBlock
            . $contextInstruction . "\n\n"
            . 'Tulis balasan yang natural, seolah-olah kamu lagi chat langsung dengan customer. ' . "\n"
            . 'Kalau balasan pertama, mulai dengan sapaan hangat. Kalau lanjutan, langsung ke inti masalah. ' . "\n"
            . 'Tunjukkan bahwa kamu paham masalahnya, jelaskan apa yang akan dilakukan, dan beri langkah konkret. ' . "\n"
            . 'Jaga agar tetap profesional tapi ramah, maksimal 4-5 kalimat.';

        $response = $this->callGroq($prompt, temperature: 0.5, maxTokens: 400);

        $clean = trim($response);
        if (mb_strlen($clean) < 40) {
            return $this->buildFallbackReply($ticket, $isFirstReply);
        }

        return $clean;
    }

    // ─── Core API Call ────────────────────────────────────────────────────────

    protected function callGroq(
        string $prompt,
        float  $temperature = 0.3,
        int    $maxTokens   = 500
    ): string {
        if (empty($this->apiKey)) {
            Log::error('GROQ_API_KEY kosong. Tambahkan ke file .env');
            return $this->getFallbackResponse($prompt);
        }

        try {
            $response = https::withHeaders([
                'Authorization' => 'Bearer ' . $this->apiKey,
                'Content-Type'  => 'application/json',
            ])->timeout(30)->post($this->apiUrl, [
                'model'       => 'llama-3.3-70b-versatile',
                'messages'    => [
                    ['role' => 'system', 'content' => $this->systemPrompt],
                    ['role' => 'user',   'content' => $prompt],
                ],
                'temperature' => $temperature,
                'max_tokens'  => $maxTokens,
            ]);

            if ($response->successful()) {
                $content = $response->json('choices.0.message.content', '');
                if (!empty(trim($content))) {
                    return trim($content);
                }
            }

            Log::error('Groq API Error - Status: ' . $response->status() . ' | Body: ' . $response->body());

        } catch (\Exception $e) {
            Log::error('Groq Exception: ' . $e->getMessage());
        }

        return $this->getFallbackResponse($prompt);
    }

    // ─── Fallback Responses ───────────────────────────────────────────────────

    protected function getFallbackResponse(string $prompt): string
    {
        $promptLower = strtolower($prompt);

        if (str_contains($promptLower, 'rangkuman') || str_contains($promptLower, 'summary')) {
            preg_match('/judul\s*:\s*(.*?)(?:\n|$)/i', $prompt, $m);
            $title = trim($m[1] ?? 'Laporan');
            return "Tiket ini melaporkan masalah terkait \"{$title}\". Detail lengkap tersedia pada deskripsi tiket.";
        }

        if (str_contains($promptLower, 'high/medium/low') || str_contains($promptLower, 'prioritas')) {
            return 'MEDIUM';
        }

        return $this->buildGenericFallbackReply();
    }

    protected function buildFallbackReply(Ticket $ticket, bool $isFirstReply = true): string
    {
        $priority = strtoupper($ticket->priority ?? 'MEDIUM');

        if ($isFirstReply) {
            $opening = $priority === 'HIGH'
                ? "Halo, kami memahami bahwa masalah \"{$ticket->title}\" berdampak signifikan dan menanganinya sebagai prioritas utama."
                : "Halo, terima kasih telah melaporkan masalah \"{$ticket->title}\" kepada kami.";
        } else {
            $opening = $priority === 'HIGH'
                ? "Kami masih menangani masalah \"{$ticket->title}\" ini dengan prioritas tinggi."
                : "Terkait laporan \"{$ticket->title}\" yang sedang kami proses,";
        }

        return $opening
            . ' Tim kami sedang melakukan pengecekan lebih lanjut dan akan segera menginformasikan hasilnya.'
            . ' Mohon tetap pantau notifikasi tiket ini untuk update terbaru.';
    }

    protected function buildGenericFallbackReply(): string
    {
        return 'Terima kasih atas laporan Anda. Tim support kami telah menerima tiket ini dan sedang melakukan pengecekan.'
            . ' Kami akan segera menindaklanjuti dan menginformasikan hasilnya kepada Anda.';
    }

    // ─── Helpers ──────────────────────────────────────────────────────────────

    protected function looksLikePriorityWord(string $text): bool
    {
        return in_array(strtoupper(trim($text)), ['HIGH', 'MEDIUM', 'LOW']);
    }
}
