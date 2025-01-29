<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class ProfileController extends Controller
{
    public function showProfile()
    {
        return view('profile');
    }

    public function update(Request $request)
    {
        Log::info('Update request received', $request->all());
        $user = Auth::user();

        // Handle image-only updates
        if ($request->hasFile('profile_image') && !$request->has('full_name')) {
            try {
                $request->validate([
                    'profile_image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048'
                ]);

                if ($user->profile_picture) {
                    Storage::disk('public')->delete($user->profile_picture);
                }

                $imagePath = $request->file('profile_image')->store('profile-images', 'public');
                $user->profile_picture = $imagePath;
                $user->save();

                return response()->json([
                    'success' => true,
                    'message' => 'Profile picture updated successfully',
                    'image_path' => Storage::url($imagePath)
                ]);
            } catch (\Exception $e) {
                Log::error('Profile image update error: ' . $e->getMessage());
                return response()->json([
                    'success' => false,
                    'message' => 'Error updating profile picture: ' . $e->getMessage()
                ], 422);
            }
        }

        // Handle full profile updates
        try {
            $validated = $request->validate([
                'full_name' => 'required|string|max:255',
                'email' => 'required|email|unique:users,email,' . $user->id,
                'phone' => 'nullable|string|max:20',
                'location' => 'nullable|string|max:255',
                'profile_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
            ]);

            // Update basic info
            $user->fill($validated);

            // Handle profile image if included in full update
            if ($request->hasFile('profile_image')) {
                if ($user->profile_picture) {
                    Storage::disk('public')->delete($user->profile_picture);
                }
                $imagePath = $request->file('profile_image')->store('profile-images', 'public');
                $user->profile_picture = $imagePath;
            }

            $user->save();

            return response()->json([
                'success' => true,
                'message' => 'Profile updated successfully',
                'image_path' => $user->profile_picture ? Storage::url($user->profile_picture) : null
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error('Validation error: ', $e->errors());
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            Log::error('Profile update error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error updating profile: ' . $e->getMessage()
            ], 500);
        }
    }
}
