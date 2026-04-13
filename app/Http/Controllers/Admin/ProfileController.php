<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Mail\EmailChangeVerification;
use App\Mail\PasswordResetVerification;  // ✅ Tambahkan ini
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class ProfileController extends Controller
{
    // Halaman profile (read-only)
    public function index()
    {
        $user = Auth::user();
        return view('admin.profile.index', compact('user'));
    }

    // Halaman edit profile
    public function edit()
    {
        $user = Auth::user();
        return view('admin.profile.edit', compact('user'));
    }

    // Update nama
    public function updateName(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        Auth::user()->update([
            'name' => $request->name,
        ]);

        return redirect()->route('admin.profile')
            ->with('success', 'Nama berhasil diperbarui.');
    }

    // Request change email - kirim verifikasi ke email lama
    public function requestEmailChange(Request $request)
    {
        // Validasi
        $request->validate([
            'email' => 'required|email|unique:users,email,' . Auth::id(),
        ], [
            'email.required' => 'Email baru wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'email.unique' => 'Email sudah digunakan oleh akun lain.',
        ]);

        $user = Auth::user();
        $newEmail = $request->email;
        $token = Str::random(60);

        // Simpan token dan email baru
        $user->update([
            'new_email' => $newEmail,
            'email_change_token' => $token,
            'email_change_token_expires_at' => now()->addMinutes(30),
        ]);

        try {
            // Kirim email verifikasi ke EMAIL LAMA
            Mail::to($user->email)->send(new EmailChangeVerification($user, $newEmail, $token));

            return back()->with('success', 'Link verifikasi telah dikirim ke ' . $user->email . '. Token akan kadaluarsa dalam 30 menit.');
        } catch (\Exception $e) {
            \Log::error('Email gagal dikirim: ' . $e->getMessage());
            return back()->with('error', 'Gagal mengirim email verifikasi. Silakan coba lagi.');
        }
    }

    // Konfirmasi perubahan email via token
    public function confirmEmailChange($token)
    {
        $user = User::where('email_change_token', $token)
            ->where('email_change_token_expires_at', '>', now())
            ->first();

        if (!$user) {
            return redirect()->route('admin.profile')
                ->with('error', 'Token verifikasi tidak valid atau sudah kadaluarsa.');
        }

        // Ubah email
        $oldEmail = $user->email;
        $newEmail = $user->new_email;

        $user->update([
            'email' => $newEmail,
            'new_email' => null,
            'email_change_token' => null,
            'email_change_token_expires_at' => null,
        ]);

        return redirect()->route('admin.profile')
            ->with('success', 'Alamat email berhasil diubah dari ' . $oldEmail . ' menjadi ' . $newEmail);
    }

    // Halaman ganti password
    public function passwordForm()
    {
        $user = Auth::user();
        return view('admin.profile.password', compact('user'));
    }

    // ✅ Request password reset - kirim verifikasi ke email
    public function requestPasswordReset(Request $request)
    {
        $request->validate([
            'current_password' => 'required|current_password',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $user = Auth::user();
        $token = Str::random(60);

        // Simpan token dan password baru sementara
        $user->update([
            'password_reset_token' => $token,
            'password_reset_token_expires_at' => now()->addMinutes(30),
        ]);

        // Simpan password baru ke session (sementara)
        session(['temp_new_password' => Hash::make($request->password)]);

        try {
            Mail::to($user->email)->send(new PasswordResetVerification($user, $token));
            return back()->with('info', 'Link verifikasi telah dikirim ke ' . $user->email . '. Klik link tersebut untuk mengkonfirmasi perubahan password. Token akan kadaluarsa dalam 30 menit.');
        } catch (\Exception $e) {
            \Log::error('Email gagal dikirim: ' . $e->getMessage());
            return back()->with('error', 'Gagal mengirim email verifikasi. Silakan coba lagi.');
        }
    }

    // ✅ Konfirmasi perubahan password via token
    public function confirmPasswordReset($token)
    {
        $user = User::where('password_reset_token', $token)
            ->where('password_reset_token_expires_at', '>', now())
            ->first();

        if (!$user) {
            return redirect()->route('admin.profile')
                ->with('error', 'Token verifikasi tidak valid atau sudah kadaluarsa.');
        }

        // Ambil password baru dari session
        $newPassword = session('temp_new_password');

        if (!$newPassword) {
            return redirect()->route('admin.profile')
                ->with('error', 'Sesi verifikasi kadaluarsa. Silakan ulangi proses ganti password.');
        }

        // Update password
        $user->update([
            'password' => $newPassword,
            'password_reset_token' => null,
            'password_reset_token_expires_at' => null,
        ]);

        // Hapus session
        session()->forget('temp_new_password');

        return redirect()->route('admin.profile')
            ->with('success', 'Password berhasil diubah.');
    }

    // Update password (tanpa verifikasi) - OPSIONAL, bisa dihapus atau diarahkan
    public function updatePassword(Request $request)
    {
        // Method ini sudah digantikan dengan requestPasswordReset
        return redirect()->route('admin.profile.password.form');
    }
}
