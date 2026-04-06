<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class SuperAdminWebController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');
        $users = User::with('role')
            ->when($search, fn($q) => $q->where(function ($query) use ($search) {
                $query->where('name', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%")
                      ->orWhere('nip', 'like', "%{$search}%");
            }))
            ->paginate(10)
            ->appends(['search' => $search]);

        $breadcrumbs = [['label' => 'Home', 'url' => route('users.index')], ['label' => 'Manajemen Pengguna', 'url' => '#']];

        return view('General.users', compact('users', 'search', 'breadcrumbs'));
    }

    public function add()
    {
        $roles = Role::where('name', '!=', 'Super Admin')->get();
        $breadcrumbs = [['label' => 'Home', 'url' => route('users.index')],
                        ['label' => 'Tambah Pengguna', 'url' => '#']];
        return view('General.users-add', compact('roles', 'breadcrumbs'));
    }

    public function edit($id)
    {
        $user = User::with('role')->findOrFail($id);
        $roles = Role::where('name', '!=', 'Super Admin')->get();
        $breadcrumbs = [['label' => 'Home', 'url' => route('users.index')],
                        ['label' => 'Edit Pengguna', 'url' => '#']];
        return view('General.users-edit', compact('user', 'roles', 'breadcrumbs'));
    }

    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'fullname' => 'required|string|max:255',
            'nip' => 'required|string|unique:users,nip',
            'email' => 'required|email|unique:users,email',
            'telephone' => 'required|string|max:20',
            'role' => 'required|exists:role,id',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }


        User::create([
            'name' => $request->fullname,
            'email' => $request->email,
            'password' => Hash::make('password123'), // Default password
            'nip' => $request->nip,
            'phone_number' => $request->telephone,
            'role_id' => $request->role,
        ]);

        return redirect()->route('users.index')->with('success', 'Pengguna berhasil ditambahkan. Password default: password123');
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'fullname' => 'required|string|max:255',
            'nip' => 'required|string|unique:users,nip,' . $id,
            'email' => 'required|email|unique:users,email,' . $id,
            'telephone' => 'required|string|max:20',
            'role' => 'required|exists:role,id',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $user = User::findOrFail($id);
        $user->update([
            'name' => $request->fullname,
            'email' => $request->email,
            'nip' => $request->nip,
            'phone_number' => $request->telephone,
            'role_id' => $request->role,
        ]);

        return redirect()->route('users.index')->with('success', 'Pengguna berhasil diperbarui.');
    }

    public function delete($id)
    {
        $user = User::with('role')->findOrFail($id);

        if ($user->role->name === 'Super Admin') {
            return redirect()->route('users.index')->with('error', 'Super Admin tidak dapat dihapus.');
        }

        // Optional: cek relasi (sesuaikan dengan model Anda)
        // if ($user->travelDocuments()->count() > 0) {
        //     return redirect()->route('users.index')->with('error', 'Pengguna ini terkait dengan data pengiriman.');
        // }

        $user->delete();

        return redirect()->route('users.index')->with('success', 'Pengguna berhasil dihapus.');
    }
}
