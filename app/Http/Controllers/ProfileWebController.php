<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Hash;

class ProfileWebController extends Controller
{
    public function update(Request $request)
    {
        // Logging awal untuk debugging
        Log::info("Profile update request received for user ID: " . Auth::id());
        Log::info("Has avatar file: " . ($request->hasFile('avatar') ? 'YES' : 'NO'));

        $user = Auth::user();
        $id = $user->id;

        // Validasi data profil
        $rules = [
            'fullname'  => 'required|string|max:255',
            'nip'       => 'required|string|unique:users,nip,' . $id,
            'email'     => 'required|email|unique:users,email,' . $id,
            'telephone' => 'required|string|max:20',
        ];

        // Validasi avatar jika ada
        if ($request->hasFile('avatar')) {
            $rules['avatar'] = 'required|image|mimes:jpg,jpeg,png|max:2048';
        }

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            Log::warning("Validation failed: " . json_encode($validator->errors()));
            return redirect()->back()->withErrors($validator)->withInput();
        }

        // Update data profil
        $user->name = $request->fullname;
        $user->email = $request->email;
        $user->nip = $request->nip;
        $user->phone_number = $request->telephone;

        // Handle avatar upload
        if ($request->hasFile('avatar')) {
            $file = $request->file('avatar');
            $extension = $file->getClientOriginalExtension();
            $filename = 'avatar_' . $user->id . '_' . time() . '.' . $extension;
            $path = public_path('images/user');

            // Buat folder jika belum ada
            if (!File::exists($path)) {
                File::makeDirectory($path, 0755, true);
                Log::info("Created folder: " . $path);
            }

            // Hapus avatar lama
            if ($user->avatar) {
                $oldPath = public_path($user->avatar);
                if (File::exists($oldPath)) {
                    File::delete($oldPath);
                    Log::info("Deleted old avatar: " . $user->avatar);
                }
            }

            // Simpan file baru
            if ($file->move($path, $filename)) {
                $user->avatar = 'images/user/' . $filename;
                Log::info("New avatar saved: " . $user->avatar);
            } else {
                Log::error("Failed to move avatar file for user ID: " . $user->id);
            }
        } else {
            Log::info("No avatar file in request");
        }

        // Simpan ke database
        $user->save();
        Log::info("Profile saved for user ID: " . $user->id . " | Avatar: " . ($user->avatar ?? 'NULL'));

        return redirect()->route('profile')->with('success', 'Profil berhasil diperbarui!');
    }

    public function updateAvatar(Request $request)
    {
        try {
            $request->validate([
                'avatar' => 'required|image|mimes:jpg,jpeg,png|max:2048'
            ]);

            $user = Auth::user();
            $file = $request->file('avatar');
            $extension = $file->getClientOriginalExtension();
            $filename = 'avatar_' . $user->id . '_' . time() . '.' . $extension;
            $path = public_path('images/user');

            if (!File::exists($path)) {
                File::makeDirectory($path, 0755, true);
            }

            // Hapus avatar lama
            if ($user->avatar) {
                $oldPath = public_path($user->avatar);
                if (File::exists($oldPath)) {
                    File::delete($oldPath);
                }
            }

            if ($file->move($path, $filename)) {
                $user->avatar = 'images/user/' . $filename;
                $user->save();

                return response()->json([
                    'success' => true,
                    'avatar_url' => asset($user->avatar),
                    'message' => 'Avatar berhasil diupload'
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => 'Gagal memindahkan file'
            ], 500);

        } catch (\Exception $e) {
            Log::error('Avatar upload error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $user = Auth::user();

        if (!Hash::check($request->current_password, $user->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Password lama tidak sesuai.'
            ], 422);
        }

        $user->password = Hash::make($request->password);
        $user->save();

        return response()->json([
            'success' => true,
            'message' => 'Password berhasil diperbarui!'
        ]);
    }
}
