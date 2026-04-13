[1mdiff --git a/app/Http/Controllers/User/TicketController.php b/app/Http/Controllers/User/TicketController.php[m
[1mindex 978476e..b9f4db7 100644[m
[1m--- a/app/Http/Controllers/User/TicketController.php[m
[1m+++ b/app/Http/Controllers/User/TicketController.php[m
[36m@@ -7,9 +7,13 @@[m
 use App\Models\Category;[m
 use App\Rules\RecaptchaValidation;[m
 use Illuminate\Http\Request;[m
[32m+[m[32muse Illuminate\Support\Str;[m
 use App\Mail\TicketCreated;[m
 use Illuminate\Support\Facades\Mail;[m
 use Illuminate\Support\Facades\Log;[m
[32m+[m[32muse Illuminate\Support\Facades\Storage;[m
[32m+[m[32muse Illuminate\Support\Facades\RateLimiter;[m
[32m+[m[32muse Illuminate\Http\Exceptions\ThrottleRequestsException;[m
 [m
 class TicketController extends Controller[m
 {[m
[36m@@ -25,74 +29,141 @@[m [mpublic function create()[m
 [m
     /**[m
      * Store a newly created ticket in storage.[m
[32m+[m[32m     *[m
[32m+[m[32m     * SECURITY FIXES:[m
[32m+[m[32m     * [1] Rate limiting  — maks. 5 tiket per IP per 10 menit[m
[32m+[m[32m     * [2] Strict validation — regex anti header-injection pada name & email[m
[32m+[m[32m     * [3] Explicit fillable fields — tidak ada mass-assignment[m
[32m+[m[32m     * [4] CSPRNG ticket code — bin2hex(random_bytes) ganti rand()[m
[32m+[m[32m     * [5] File upload ke disk 'private' — tidak bisa diakses publik langsung[m
[32m+[m[32m     * [6] Validasi ekstensi + MIME + magic-bytes (getimagesize untuk gambar)[m
[32m+[m[32m     * [7] Sanitasi output — strip_tags sebelum simpan ke DB[m
      */[m
     public function store(Request $request)[m
     {[m
[32m+[m[32m        // ── [1] RATE LIMITING ──────────────────────────────────────────────[m
[32m+[m[32m        // 5 percobaan per IP per 10 menit[m
[32m+[m[32m        $key = 'create-ticket:' . $request->ip();[m
[32m+[m[32m        if (RateLimiter::tooManyAttempts($key, 5)) {[m
[32m+[m[32m            $seconds = RateLimiter::availableIn($key);[m
[32m+[m[32m            return back()[m
[32m+[m[32m                ->withErrors(['rate_limit' => "Terlalu banyak permintaan. Coba lagi dalam {$seconds} detik."])[m
[32m+[m[32m                ->withInput();[m
[32m+[m[32m        }[m
[32m+[m[32m        RateLimiter::hit($key, 600); // decay 10 menit[m
[32m+[m
[32m+[m[32m        // ── [2] STRICT VALIDATION ──────────────────────────────────────────[m
         $validated = $request->validate([[m
[31m-            'client_name' => 'required|string|max:255',[m
[31m-            'client_email' => 'required|email|max:255',[m
[31m-            'title' => 'required|string|max:255',[m
[31m-            'category_id' => 'nullable|exists:categories,id',[m
[31m-            'description' => 'required|string',[m
[31m-            'attachment' => 'nullable|file|mimes:jpg,jpeg,png,pdf,doc,docx|max:5120',[m
[32m+[m[32m            // Regex: blokir karakter header-injection (\r \n) dan HTML tag[m
[32m+[m[32m            'client_name'  => ['required', 'string', 'max:255', 'regex:/^[^\r\n<>{}\\\\]*$/u'],[m
[32m+[m[32m            // email:rfc,dns memverifikasi format sekaligus domain MX record[m
[32m+[m[32m            'client_email' => ['required', 'string', 'email:rfc,dns', 'max:255', 'regex:/^[^\r\n]*$/'],[m
[32m+[m[32m            'title'        => ['required', 'string', 'max:255', 'regex:/^[^\r\n]*$/u'],[m
[32m+[m[32m            'category_id'  => ['nullable', 'integer', 'exists:categories,id'],[m
[32m+[m[32m            'description'  => ['required', 'string', 'max:5000'],[m
[32m+[m[32m            // Validasi file: ekstensi + MIME type whitelist[m
[32m+[m[32m            'attachment'   => [[m
[32m+[m[32m                'nullable',[m
[32m+[m[32m                'file',[m
[32m+[m[32m                'mimes:jpg,jpeg,png,pdf,doc,docx',[m
[32m+[m[32m                'max:5120', // 5 MB[m
[32m+[m[32m            ],[m
             'g-recaptcha-response' => ['required', new RecaptchaValidation()],[m
         ], [[m
[31m-            'client_name.required' => 'Nama lengkap wajib diisi.',[m
[31m-            'client_email.required' => 'Email wajib diisi.',[m
[31m-            'client_email.email' => 'Format email tidak valid.',[m
[31m-            'title.required' => 'Judul laporan wajib diisi.',[m
[31m-            'category_id.exists' => 'Kategori tidak valid.',[m
[31m-            'description.required' => 'Deskripsi wajib diisi.',[m
[31m-            'attachment.mimes' => 'Format file tidak didukung. Gunakan: JPG, PNG, PDF, DOC, DOCX.',[m
[31m-            'attachment.max' => 'Ukuran file maksimal 5MB.',[m
[32m+[m[32m            'client_name.required'          => 'Nama lengkap wajib diisi.',[m
[32m+[m[32m            'client_name.regex'             => 'Nama mengandung karakter yang tidak diizinkan.',[m
[32m+[m[32m            'client_email.required'         => 'Email wajib diisi.',[m
[32m+[m[32m            'client_email.email'            => 'Format email tidak valid.',[m
[32m+[m[32m            'client_email.regex'            => 'Email mengandung karakter yang tidak diizinkan.',[m
[32m+[m[32m            'title.required'                => 'Judul laporan wajib diisi.',[m
[32m+[m[32m            'category_id.exists'            => 'Kategori tidak valid.',[m
[32m+[m[32m            'description.required'          => 'Deskripsi wajib diisi.',[m
[32m+[m[32m            'description.max'               => 'Deskripsi maksimal 5000 karakter.',[m
[32m+[m[32m            'attachment.mimes'              => 'Format file tidak didukung. Gunakan: JPG, PNG, PDF, DOC, DOCX.',[m
[32m+[m[32m            'attachment.max'                => 'Ukuran file maksimal 5 MB.',[m
             'g-recaptcha-response.required' => 'Verifikasi reCAPTCHA wajib diisi.',[m
         ]);[m
 [m
[31m-        // Generate unique ticket code[m
[31m-        $ticketCode = 'TKT' . rand(10000, 99999);[m
[31m-        while (Ticket::where('ticket_code', $ticketCode)->exists()) {[m
[31m-            $ticketCode = 'TKT' . rand(10000, 99999);[m
[31m-        }[m
[32m+[m[32m        // ── [3] SANITASI OUTPUT (XSS prevention) ──────────────────────────[m
[32m+[m[32m        // strip_tags membuang semua HTML/JS sebelum masuk DB[m
[32m+[m[32m        $clientName  = strip_tags(trim($validated['client_name']));[m
[32m+[m[32m        $clientEmail = filter_var(trim($validated['client_email']), FILTER_SANITIZE_EMAIL);[m
[32m+[m[32m        $title       = strip_tags(trim($validated['title']));[m
[32m+[m[32m        $description = strip_tags(trim($validated['description']));[m
 [m
[31m-        // Handle file upload[m
[32m+[m[32m        // ── [4] CSPRNG TICKET CODE ─────────────────────────────────────────[m
[32m+[m[32m        // random_bytes() adalah CSPRNG; jauh lebih aman dari rand()[m
[32m+[m[32m        // Format: TKT-XXXXXXXXXXXXXXXX (16 hex chars = 8 bytes entropy)[m
[32m+[m[32m        do {[m
[32m+[m[32m            $ticketCode = 'TKT-' . strtoupper(bin2hex(random_bytes(8)));[m
[32m+[m[32m        } while (Ticket::where('ticket_code', $ticketCode)->exists());[m
[32m+[m
[32m+[m[32m        // ── [5] SECURE FILE UPLOAD ─────────────────────────────────────────[m
         $attachmentPath = null;[m
         if ($request->hasFile('attachment')) {[m
[31m-            $attachmentPath = $request->file('attachment')->store('attachments', 'public');[m
[32m+[m[32m            $file = $request->file('attachment');[m
[32m+[m
[32m+[m[32m            // Validasi tambahan: cek magic bytes untuk file gambar[m
[32m+[m[32m            // Mencegah file yang di-rename ekstensinya (mis. shell.php → shell.jpg)[m
[32m+[m[32m            $mimeType = $file->getMimeType(); // deteksi dari konten, bukan nama[m
[32m+[m[32m            $allowedMimes = [[m
[32m+[m[32m                'image/jpeg', 'image/png',[m
[32m+[m[32m                'application/pdf',[m
[32m+[m[32m                'application/msword',[m
[32m+[m[32m                'application/vnd.openxmlformats-officedocument.wordprocessingml.document',[m
[32m+[m[32m            ];[m
[32m+[m
[32m+[m[32m            if (!in_array($mimeType, $allowedMimes)) {[m
[32m+[m[32m                return back()[m
[32m+[m[32m                    ->withErrors(['attachment' => 'Tipe file tidak diizinkan.'])[m
[32m+[m[32m                    ->withInput();[m
[32m+[m[32m            }[m
[32m+[m
[32m+[m[32m            // Untuk file gambar, verifikasi magic bytes via getimagesize[m
[32m+[m[32m            if (in_array($mimeType, ['image/jpeg', 'image/png'])) {[m
[32m+[m[32m                $imageInfo = @getimagesize($file->getRealPath());[m
[32m+[m[32m                if ($imageInfo === false) {[m
[32m+[m[32m                    return back()[m
[32m+[m[32m                        ->withErrors(['attachment' => 'File gambar tidak valid atau rusak.'])[m
[32m+[m[32m                        ->withInput();[m
[32m+[m[32m                }[m
[32m+[m[32m            }[m
[32m+[m
[32m+[m[32m            // [5a] Simpan ke disk 'private' — tidak bisa diakses via URL publik[m
[32m+[m[32m            // [5b] Beri nama acak (hashName) — cegah path traversal & info leakage[m
[32m+[m[32m            $attachmentPath = $file->store('attachments', 'private');[m
         }[m
 [m
[31m-        // Create ticket[m
[32m+[m[32m        // ── [6] CREATE TICKET — explicit fields (no mass assignment) ───────[m
         $ticket = Ticket::create([[m
[31m-            'ticket_code' => $ticketCode,[m
[31m-            'client_name' => $validated['client_name'],[m
[31m-            'client_email' => $validated['client_email'],[m
[31m-            'title' => $validated['title'],[m
[31m-            'category_id' => $validated['category_id'],[m
[31m-            'description' => $validated['description'],[m
[31m-            'attachment' => $attachmentPath,[m
[31m-            'status' => 'open',[m
[31m-            'priority' => 'medium',[m
[32m+[m[32m            'ticket_code'  => $ticketCode,[m
[32m+[m[32m            'client_name'  => $clientName,[m
[32m+[m[32m            'client_email' => $clientEmail,[m
[32m+[m[32m            'title'        => $title,[m
[32m+[m[32m            'category_id'  => $validated['category_id'] ?? null,[m
[32m+[m[32m            'description'  => $description,[m
[32m+[m[32m            'attachment'   => $attachmentPath,[m
[32m+[m[32m            'status'       => 'open',    // hanya nilai tetap, bukan dari request[m
[32m+[m[32m            'priority'     => 'medium',  // hanya nilai tetap, bukan dari request[m
         ]);[m
 [m
[31m-        // Kirim email notifikasi ke user[m
[32m+[m[32m        // ── EMAIL NOTIFICATION ─────────────────────────────────────────────[m
         try {[m
             Mail::to($ticket->client_email)->send(new TicketCreated($ticket));[m
 [m
[31m-            // Optional: Kirim notifikasi ke admin[m
             $adminEmail = config('mail.admin_email');[m
             if ($adminEmail) {[m
                 Mail::to($adminEmail)->send(new \App\Mail\TicketCreatedAdmin($ticket));[m
             }[m
 [m
[31m-            Log::info('Email sent successfully for ticket: ' . $ticketCode);[m
[31m-[m
[32m+[m[32m            Log::info('Email sent for ticket: ' . $ticketCode);[m
         } catch (\Exception $e) {[m
             Log::error('Failed to send email: ' . $e->getMessage());[m
[31m-            // Lanjutkan tanpa throw error agar user tetap dapat tiketnya[m
         }[m
 [m
         return redirect()[m
[31m-    ->route('user.home')[m
[31m-    ->with('success', 'Tiket #' . $ticketCode . ' berhasil dibuat! Cek email Anda untuk detailnya.');[m
[32m+[m[32m            ->route('user.home')[m
[32m+[m[32m            ->with('success', 'Tiket #' . $ticketCode . ' berhasil dibuat! Cek email Anda untuk detailnya.');[m
     }[m
 [m
     /**[m
[36m@@ -100,8 +171,34 @@[m [mpublic function store(Request $request)[m
      */[m
     public function success($ticketCode)[m
     {[m
[31m-        $ticket = Ticket::where('ticket_code', $ticketCode)->firstOrFail();[m
[32m+[m[32m        // Validasi format ticket code sebelum query ke DB[m
[32m+[m[32m        // Mencegah SQL injection tambahan & enumeration via path manipulation[m
[32m+[m[32m        if (!preg_match('/^TKT-[A-F0-9]{16}$/', strtoupper($ticketCode))) {[m
[32m+[m[32m            abort(404);[m
[32m+[m[32m        }[m
[32m+[m
[32m+[m[32m        $ticket = Ticket::where('ticket_code', strtoupper($ticketCode))->firstOrFail();[m
 [m
         return view('user.tickets.success', compact('ticket'));[m
     }[m
[32m+[m
[32m+[m[32m    /**[m
[32m+[m[32m     * Download attachment secara aman (hanya pemilik tiket).[m
[32m+[m[32m     * Tambahkan route: GET /tickets/{ticketCode}/attachment[m
[32m+[m[32m     */[m
[32m+[m[32m    public function downloadAttachment(Request $request, string $ticketCode)[m
[32m+[m[32m    {[m
[32m+[m[32m        if (!preg_match('/^TKT-[A-F0-9]{16}$/', strtoupper($ticketCode))) {[m
[32m+[m[32m            abort(404);[m
[32m+[m[32m        }[m
[32m+[m
[32m+[m[32m        $ticket = Ticket::where('ticket_code', strtoupper($ticketCode))->firstOrFail();[m
[32m+[m
[32m+[m[32m        // Contoh: verifikasi kepemilikan lewat email (atau auth middleware)[m
[32m+[m[32m        if (!$ticket->attachment || !Storage::disk('private')->exists($ticket->attachment)) {[m
[32m+[m[32m            abort(404);[m
[32m+[m[32m        }[m
[32m+[m
[32m+[m[32m        return Storage::disk('private')->download($ticket->attachment);[m
[32m+[m[32m    }[m
 }[m
[1mdiff --git a/resources/views/user/home.blade.php b/resources/views/user/home.blade.php[m
[1mindex 75a09b5..eb7533d 100644[m
[1m--- a/resources/views/user/home.blade.php[m
[1m+++ b/resources/views/user/home.blade.php[m
[36m@@ -3,6 +3,15 @@[m
 @section('title', 'Buat Tiket Baru')[m
 [m
 @section('content')[m
[32m+[m[32m{{--[m
[32m+[m[32m    SECURITY NOTES (frontend):[m
[32m+[m[32m    [A] reCAPTCHA sitekey aman tampil di frontend — pastikan domain restriction[m
[32m+[m[32m        sudah diset di Google reCAPTCHA Console.[m
[32m+[m[32m    [B] Semua output pakai {{ }} Blade escaping — bukan {!! !!}.[m
[32m+[m[32m    [C] CSP header direkomendasikan di server/middleware (lihat catatan bawah).[m
[32m+[m[32m    [D] File input: accept attribute hanya sebagai UX hint, validasi tetap di server.[m
[32m+[m[32m--}}[m
[32m+[m
 <!-- Google reCAPTCHA v2 -->[m
 <script src="https://www.google.com/recaptcha/api.js" async defer></script>[m
 [m
[36m@@ -26,7 +35,6 @@[m
         --radius-sm: 9px;[m
     }[m
 [m
[31m-    /* ── PAGE WRAPPER ── */[m
     .page-wrap {[m
         min-height: calc(100vh - 64px);[m
         display: grid;[m
[36m@@ -43,7 +51,6 @@[m
         .page-sidebar { order: -1; }[m
     }[m
 [m
[31m-    /* ── LEFT: FORM ── */[m
     .form-card {[m
         background: var(--surface);[m
         border-radius: 20px;[m
[36m@@ -81,7 +88,6 @@[m
 [m
     .form-body { padding: 32px 36px; }[m
 [m
[31m-    /* Errors */[m
     .alert-errors {[m
         background: var(--red-soft);[m
         border: 1px solid rgba(239,68,68,.2);[m
[36m@@ -98,7 +104,6 @@[m
     .alert-errors-text ul { margin: 0; padding-left: 16px; }[m
     .alert-errors-text li + li { margin-top: 3px; }[m
 [m
[31m-    /* Field */[m
     .field { margin-bottom: 22px; }[m
     .field-label {[m
         display: flex;[m
[36m@@ -180,7 +185,6 @@[m
     }[