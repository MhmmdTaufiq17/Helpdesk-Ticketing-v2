<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Ticket;
use App\Models\Category;
use App\Rules\RecaptchaValidation;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Mail\TicketCreated;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class TicketController extends Controller
{
    public function create()
    {
        $categories = Category::orderBy('category_name', 'asc')->get();
        return view('user.tickets.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'client_name'          => 'required|string|max:255',
            'client_email'         => 'required|email|max:255',
            'title'                => 'required|string|max:255',
            'category_id'          => 'nullable|exists:categories,id',
            'description'          => 'required|string|max:5000',
            // UPDATE: Validasi array untuk multiple images, maks 5 file, hanya gambar
            'attachment'           => 'nullable|array|max:5',
            'attachment.*'         => 'image|mimes:jpg,jpeg,png|max:5120',
            'g-recaptcha-response' => ['required', new RecaptchaValidation()],
        ], [
            'client_name.required'          => 'Nama lengkap wajib diisi.',
            'client_email.required'         => 'Email wajib diisi.',
            'client_email.email'            => 'Format email tidak valid.',
            'title.required'                => 'Judul laporan wajib diisi.',
            'category_id.exists'            => 'Kategori tidak valid.',
            'description.required'          => 'Deskripsi wajib diisi.',
            'attachment.max'                => 'Maksimal lampiran adalah 5 file.',
            'attachment.*.image'            => 'File harus berupa gambar.',
            'attachment.*.mimes'            => 'Format gambar tidak didukung. Gunakan: JPG, JPEG, atau PNG.',
            'attachment.*.max'              => 'Ukuran tiap file maksimal 5MB.',
            'g-recaptcha-response.required' => 'Verifikasi reCAPTCHA wajib diisi.',
        ]);

        // Generate Ticket Code
        do {
            $ticketCode = 'TKT-' . strtoupper(Str::random(8));
        } while (Ticket::where('ticket_code', $ticketCode)->exists());

        // Sanitasi Input
        $clientName  = strip_tags(trim($validated['client_name']));
        $title       = strip_tags(trim($validated['title']));
        $description = strip_tags(trim($validated['description']));

        // UPDATE: Handle Multiple File Upload
        $attachmentString = null;
        if ($request->hasFile('attachment')) {
            $paths = [];
            foreach ($request->file('attachment') as $file) {
                // Simpan file ke folder storage/app/public/attachments
                $paths[] = $file->store('attachments', 'public');
            }
            // Karena kolom DB adalah VARCHAR, gabungkan path dengan koma
            $attachmentString = implode(',', $paths);
        }

        // Simpan ke Database
        $ticket = Ticket::create([
            'ticket_code'  => $ticketCode,
            'client_name'  => $clientName,
            'client_email' => $validated['client_email'],
            'title'        => $title,
            'category_id'  => $validated['category_id'],
            'description'  => $description,
            'attachment'   => $attachmentString, // Menyimpan string path (misal: "path1.jpg,path2.jpg")
            'status'       => 'open',
            'priority'     => null,
        ]);

        // Kirim email notifikasi
        try {
            Mail::to($ticket->client_email)->send(new TicketCreated($ticket));

            $adminEmail = config('mail.admin_email');
            if ($adminEmail) {
                Mail::to($adminEmail)->send(new \App\Mail\TicketCreatedAdmin($ticket));
            }

            Log::info('Email sent successfully for ticket: ' . $ticketCode);
        } catch (\Exception $e) {
            Log::error('Failed to send email: ' . $e->getMessage());
        }

        return redirect()
            ->route('user.home')
            ->with('success', 'Tiket #' . $ticketCode . ' berhasil dibuat! Cek email Anda untuk detailnya.');
    }

    public function success($ticketCode)
    {
        $ticket = Ticket::where('ticket_code', $ticketCode)->firstOrFail();
        return view('user.tickets.success', compact('ticket'));
    }
}
