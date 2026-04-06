<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\PersonalAccessToken;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Storage;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        // Validasi input
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        // Cari user berdasarkan email
        $user = User::where('email', $request->email)->first();

        // Jika user tidak ditemukan ATAU password salah
        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json(
                [
                    'message' => 'Unauthorized. Email or password not found.',
                ],
                401,
            );
        }

        // Hapus token lama (opsional)
        $user->tokens()->delete();

        // Buat token baru
        $token = $user->createToken('auth-token')->plainTextToken;

        // Load relasi
        $user->load('role.division');

        //dd($user,$token);
        return response()->json([
            'message' => 'Login Success!',
            'access_token' => $token,
            'token_type' => 'Bearer',
            'data' => [
                'id' => $user->id,
                'name' => $user->name,
                'role' => [
                    'id' => $user->role->id,
                    'name' => $user->role->name,
                    'division' => [
                        'id' => $user->role->division->id,
                        'name' => $user->role->division->name,
                    ],
                ],
            ],
        ]);
    }

    // jaga-jaga kalau mau implementasi refresh token
    // public function refreshToken(Request $request) {
    //     $authHeader = $request->header('Authorization');

    //     if (empty($authHeader) || !str_starts_with($authHeader, 'Bearer ')) {
    //         return response()->json(['message' => 'Token is invalid'], 422);
    //     }

    //     $tokenString = explode('Bearer ', $authHeader)[1] ?? null;
    //     $token = PersonalAccessToken::findToken($tokenString);

    //     if (!$token || !$token->tokenable instanceof User) {
    //         return response()->json(['message' => 'Token is invalid'], 422);
    //     }

    //     // Hapus token lama & buat token baru
    //     $token->delete();
    //     $newToken = $token->tokenable->createToken('token')->plainTextToken;

    //     return response()->json([
    //         'status' => 'success',
    //         'access_token' => $newToken,
    //         'token_type' => 'Bearer',
    //     ]);
    // }

    // public function user()
    // {
    //     return response()->json([
    //         'message' => 'Success!',
    //         'data' => Auth::user(),
    //     ]);
    // }

    public function user()
    {
        $user = Auth::user();

        // Load relasi jika perlu (misal role)
        $user->load('role.division');

        return response()->json([
            'message' => 'Success!',
            'data' => [
                'id' => $user->id,
                'nip' => $user->nip,
                'name' => $user->name,
                'email' => $user->email,
                'phone_number' => $user->phone_number,
                'avatar' => $user->avatar_url, // pakai accessor yang sudah kamu buat
                'role_id' => $user->role_id,
                'role' => $user->role ? [
                    'id' => $user->role->id,
                    'name' => $user->role->name,
                    'division' => $user->role->division ? [
                        'id' => $user->role->division->id,
                        'name' => $user->role->division->name,
                    ] : null,
                ] : null,
            ],
        ]);
    }

    public function logout()
    {
        Auth::user()->tokens()->delete();

        $user = Auth::user();

        if (!$user) {
            return response()->json(
                [
                    'message' => 'User not authenticated.',
                ],
                401,
            );
        }

        $user->tokens()->delete();

        return response()->json([
            'message' => 'Success!',
        ]);
    }

    // tambahan sementara untuk update profile user
    public function updateProfile(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => [
                'required',
                'email',
                Rule::unique('users', 'email')->ignore($user->id),
            ],
            'phone_number' => 'nullable|string|max:20',
        ]);

        $user->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Profil berhasil diperbarui',
            'data' => [
                'nip' => $user->nip,
                'name' => $user->name,
                'email' => $user->email,
                'phone_number' => $user->phone_number,
                'avatar' => $user->avatar,
            ]
        ]);
    }

    public function updateAvatar(Request $request)
{
    $request->validate([
        'avatar' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048', // max 2MB
    ]);

    $user = Auth::user();

    // Hapus avatar lama jika ada
    if ($user->avatar) {
        Storage::disk('public')->delete($user->avatar);
    }

    // Simpan avatar baru di storage/public/avatars
    $path = $request->file('avatar')->store('avatars', 'public');

    // Update field avatar di DB (simpan path relatif)
    $user->avatar = $path;
    $user->save();

    return response()->json([
        'message' => 'Avatar berhasil diperbarui',
        'data' => [
            'avatar' => asset('storage/' . $path), // URL lengkap
        ]
    ]);
}
}
