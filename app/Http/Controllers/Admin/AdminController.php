<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Laravolt\Avatar\Avatar;

class AdminController extends Controller
{
    public function index()
    {
        if (! auth()->user()->isSuperAdmin()) {
            abort(403, 'Only Super Admin can access this page.');
        }

        $admins = User::whereIn('role', ['admin', 'super_admin'])
            ->orderBy('role', 'desc')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('admin.manajemen.index', compact('admins'));
    }

    public function create()
    {
        if (! auth()->user()->isSuperAdmin()) {
            abort(403);
        }

        return view('admin.manajemen.create');
    }

    public function store(Request $request)
    {
        if (! auth()->user()->isSuperAdmin()) {
            abort(403);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:8|confirmed',
            'role' => 'required|in:admin,super_admin',
        ]);

        $validated['password'] = Hash::make($validated['password']);

        // Create user
        $user = User::create($validated);

        // Generate avatar otomatis berdasarkan nama - FIXED VERSION
        $this->generateAvatar($user);

        return redirect()->route('admin.manajemen.index')
            ->with('success', 'Admin baru berhasil ditambahkan! Avatar otomatis dibuat.');
    }

    public function edit($id)
    {
        if (! auth()->user()->isSuperAdmin()) {
            abort(403);
        }

        $admin = User::findOrFail($id);

        if ($admin->isSuperAdmin() && $admin->id !== auth()->id()) {
            return back()->with('error', 'Anda tidak dapat mengedit Super Admin lain!');
        }

        return view('admin.manajemen.edit', compact('admin'));
    }

    public function update(Request $request, $id)
    {
        if (! auth()->user()->isSuperAdmin()) {
            abort(403);
        }

        $admin = User::findOrFail($id);

        if ($admin->isSuperAdmin() && $admin->id !== auth()->id()) {
            return back()->with('error', 'Anda tidak dapat mengupdate Super Admin lain!');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => ['required', 'email', Rule::unique('users')->ignore($admin->id)],
            'password' => 'nullable|min:8|confirmed',
            'role' => 'required|in:admin,super_admin',
        ]);

        $superAdminCount = User::where('role', 'super_admin')->count();
        if ($admin->isSuperAdmin() && $validated['role'] === 'admin' && $superAdminCount <= 1) {
            return back()->with('error', 'Tidak dapat downgrade Super Admin terakhir!');
        }

        // Cek jika nama berubah, regenerate avatar
        $nameChanged = ($admin->name !== $validated['name']);

        if (! empty($validated['password'])) {
            $validated['password'] = Hash::make($validated['password']);
        } else {
            unset($validated['password']);
        }

        $admin->update($validated);

        // Regenerate avatar jika nama berubah
        if ($nameChanged) {
            $this->generateAvatar($admin);
        }

        return redirect()->route('admin.manajemen.index')
            ->with('success', 'Data admin berhasil diperbarui!');
    }

    public function destroy($id)
    {
        if (! auth()->user()->isSuperAdmin()) {
            abort(403);
        }

        $admin = User::findOrFail($id);

        if ($admin->id === auth()->id()) {
            return back()->with('error', 'Anda tidak dapat menghapus akun sendiri!');
        }

        $superAdminCount = User::where('role', 'super_admin')->count();
        if ($admin->isSuperAdmin() && $superAdminCount <= 1) {
            return back()->with('error', 'Tidak dapat menghapus Super Admin terakhir!');
        }

        // Hapus file avatar jika ada
        if ($admin->avatar && Storage::disk('public')->exists('avatars/'.$admin->avatar)) {
            Storage::disk('public')->delete('avatars/'.$admin->avatar);
        }

        $admin->delete();

        return redirect()->route('admin.manajemen.index')
            ->with('success', 'Admin berhasil dihapus!');
    }

    /**
     * Generate avatar untuk user - DENGAN BACKGROUND UNGU
     *
     * @param  User  $user
     * @return void
     */
    private function generateAvatar($user)
    {
        // Buat folder jika belum ada
        if (! Storage::disk('public')->exists('avatars')) {
            Storage::disk('public')->makeDirectory('avatars');
        }

        // Generate avatar dengan Laravolt Avatar - BACKGROUND UNGU
        $avatar = new Avatar;
        $avatar->create($user->name)
            ->setDimension(200, 200)
            ->setFontSize(48)
            ->setBackground('#5b5ef4')  // ✅ Tambahkan background ungu
            ->setForeground('#ffffff')   // ✅ Warna teks putih
            ->setShape('square')          // ✅ Bentuk kotak (rounded akan diatur di CSS)
            ->save(storage_path('app/public/avatars/'.$user->id.'.png'));

        // Update user dengan path avatar
        $user->avatar = $user->id.'.png';
        $user->save();
    }

    /**
     * Regenerate avatar untuk user tertentu
     *
     * @param  int  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function regenerateAvatar($id)
    {
        if (! auth()->user()->isSuperAdmin()) {
            abort(403);
        }

        $user = User::findOrFail($id);
        $this->generateAvatar($user);

        return back()->with('success', 'Avatar berhasil diregenerasi!');
    }

    /**
     * Display the specified admin profile.
     */
    public function show($id)
    {
        if (! auth()->user()->isSuperAdmin()) {
            abort(403, 'Only Super Admin can access this page.');
        }

        $user = User::findOrFail($id);  // Gunakan variabel $user agar sesuai dengan view profile

        return view('admin.profile.index', compact('user'));
    }
}
