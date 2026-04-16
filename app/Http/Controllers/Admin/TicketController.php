<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Mail\TicketReplied;
use App\Mail\TicketStatusChanged;
use App\Models\Category;
use App\Models\Ticket;
use App\Models\TicketReply;
use App\Models\AiSuggestion;
use App\Services\GroqAIService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;

class TicketController extends Controller
{
    public function index(Request $request)
    {
        $query = Ticket::with('category')->latest();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('category')) {
            $query->where('category_id', $request->category);
        }

        if ($request->filled('priority')) {
            $query->where('priority', $request->priority);
        }

        if ($request->filled('q')) {
            $q = $request->q;
            $query->where(function ($sub) use ($q) {
                $sub->where('ticket_code', 'like', "%{$q}%")
                    ->orWhere('title', 'like', "%{$q}%")
                    ->orWhere('client_name', 'like', "%{$q}%")
                    ->orWhere('client_email', 'like', "%{$q}%");
            });
        }

        $tickets = $query->paginate(15)->withQueryString();

        $categories = Category::orderBy('category_name')->get();

        $counts = [
            'all' => Ticket::count(),
            'open' => Ticket::where('status', 'open')->count(),
            'in_progress' => Ticket::where('status', 'in_progress')->count(),
            'closed' => Ticket::where('status', 'closed')->count(),
        ];

        return view('admin.tickets.index', compact('tickets', 'categories', 'counts'));
    }

    public function show($id, GroqAIService $aiService)
    {
        $ticket = Ticket::with(['category', 'histories', 'replies.user', 'aiSuggestion'])->findOrFail($id);

        // ✅ AI ANALISIS (hanya jika priority masih null)
        if (is_null($ticket->priority)) {
            try {
                // Analisis prioritas
                $priority = $aiService->analyzePriority(
                    $ticket->title,
                    $ticket->description,
                    $ticket->category?->category_name
                );

                // Buat rangkuman
                $summary = $aiService->generateSummary(
                    $ticket->title,
                    $ticket->description,
                    $ticket->category?->category_name
                );

                // Simpan ke tabel ai_suggestions
                AiSuggestion::create([
                    'ticket_id' => $ticket->id,
                    'ai_summary' => $summary,
                    'ai_suggested_priority' => strtolower($priority),
                ]);

                // Simpan priority ke ticket
                $ticket->update([
                    'priority' => strtolower($priority),
                ]);

                // Refresh data
                $ticket->refresh();

                \Log::info('AI analyzed ticket #'.$ticket->ticket_code.' | Priority: '.$priority);

            } catch (\Exception $e) {
                \Log::error('AI analysis failed: '.$e->getMessage());
                // Fallback: set priority medium kalau error
                $ticket->update(['priority' => 'medium']);
            }
        }

        // Cooldown
        $statusCooldownKey = 'status_update_'.$id.'_'.auth()->id();
        $replyCooldownKey = 'reply_ticket_'.$id.'_'.auth()->id();

        $statusCooldownUntil = Cache::get($statusCooldownKey);
        $replyCooldownUntil = Cache::get($replyCooldownKey);

        return view('admin.tickets.show', compact(
            'ticket',
            'statusCooldownUntil',
            'replyCooldownUntil'
        ));
    }

    public function updateStatus(Request $request, $id)
    {
        $ticket = Ticket::findOrFail($id);

        if ($ticket->status === 'closed') {
            return back()->with('error', 'Tiket sudah ditutup, tidak dapat mengubah status lagi.');
        }

        // 🔥 CEK: Jika status saat ini adalah 'in_progress', tidak boleh kembali ke 'open'
        if ($ticket->status === 'in_progress' && $request->status === 'open') {
            return back()->with('error', 'Tiket yang sudah In Progress tidak dapat dikembalikan ke Open.');
        }

        $cooldownKey = 'status_update_'.$id.'_'.auth()->id();
        $cooldownTime = 300; // 5 menit

        // Cek cooldown dengan timestamp
        if (Cache::has($cooldownKey)) {
            $expiresAt = Cache::get($cooldownKey);
            $remaining = $expiresAt - time();

            if ($remaining > 0) {
                $minutes = floor($remaining / 60);
                $seconds = $remaining % 60;

                return back()->with('error', "Mohon tunggu {$minutes} menit {$seconds} detik sebelum mengubah status lagi.");
            }
        }

        $request->validate([
            'status' => 'required|in:open,in_progress,closed',
            'note' => 'nullable|string|max:500',
        ]);

        $oldStatus = $ticket->status;
        $newStatus = $request->status;

        $ticket->update(['status' => $newStatus]);

        if ($request->filled('note')) {
            $ticket->histories()->latest()->first()?->update(['note' => $request->note]);
        }

        if ($oldStatus !== $newStatus) {
            try {
                Mail::to($ticket->client_email)->send(new TicketStatusChanged($ticket, $oldStatus, $newStatus, $request->note));
            } catch (\Exception $e) {
                \Log::error('Gagal mengirim email notifikasi status tiket #'.$ticket->ticket_code.': '.$e->getMessage());
            }
        }

        // Simpan timestamp kapan cooldown berakhir
        Cache::put($cooldownKey, time() + $cooldownTime, $cooldownTime);

        return back()->with('success', 'Status tiket berhasil diperbarui.');
    }

    public function reply(Request $request, $id)
    {
        $ticket = Ticket::findOrFail($id);

        if ($ticket->status === 'closed') {
            return back()->with('error', 'Tiket sudah ditutup, tidak dapat mengirim balasan.');
        }

        $cooldownKey = 'reply_ticket_'.$id.'_'.auth()->id();
        $cooldownTime = 60; // 1 menit

        // Cek cooldown dengan timestamp
        if (Cache::has($cooldownKey)) {
            $expiresAt = Cache::get($cooldownKey);
            $remaining = $expiresAt - time();

            if ($remaining > 0) {
                $minutes = floor($remaining / 60);
                $seconds = $remaining % 60;

                return back()->with('error', "Mohon tunggu {$minutes} menit {$seconds} detik sebelum mengirim balasan lagi.");
            }
        }

        $request->validate([
            'message' => 'required|string|max:2000',
        ], [
            'message.required' => 'Pesan balasan wajib diisi.',
            'message.max' => 'Pesan maksimal 2000 karakter.',
        ]);

        // Simpan balasan
        $reply = TicketReply::create([
            'ticket_id' => $ticket->id,
            'user_id' => auth()->id(),
            'sender_type' => 'admin',
            'message' => $request->message,
        ]);

        // Update status jika masih open
        $statusChanged = false;
        if ($ticket->status === 'open') {
            $ticket->update(['status' => 'in_progress']);
            $statusChanged = true;
        }

        // Kirim email notifikasi balasan ke pelapor
        try {
            $adminName = auth()->user()->name ?? 'Admin';
            Mail::to($ticket->client_email)->send(new TicketReplied($ticket, $reply, $adminName));
        } catch (\Exception $e) {
            \Log::error('Gagal mengirim email notifikasi balasan tiket #'.$ticket->ticket_code.': '.$e->getMessage());
        }

        // Simpan timestamp kapan cooldown berakhir
        Cache::put($cooldownKey, time() + $cooldownTime, $cooldownTime);

        $message = 'Balasan berhasil dikirim.';
        if ($statusChanged) {
            $message .= ' Status tiket otomatis diubah menjadi In Progress.';
        }

        return back()->with('success', $message);
    }

    public function destroy($id)
    {
        $ticket = Ticket::findOrFail($id);

        if ($ticket->attachment && Storage::disk('public')->exists($ticket->attachment)) {
            Storage::disk('public')->delete($ticket->attachment);
        }

        $ticket->delete();

        return redirect()->route('admin.tickets.index')
            ->with('success', 'Tiket berhasil dihapus.');
    }

    public function search(Request $request)
    {
        return $this->index($request);
    }
}
