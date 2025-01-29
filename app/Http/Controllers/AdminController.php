<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Card;
use Carbon\Carbon;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class AdminController extends Controller
{
    public function dashboard()
    {
        // Initial data for the first page load
        $totalUsers = User::count();
        $totalCards = Card::count();
        $todayNewCards = Card::whereDate('created_at', Carbon::today())->count();
        $recentActivity = $this->getRecentActivity();

        return view('admin_dashboard', compact(
            'totalUsers',
            'totalCards',
            'todayNewCards',
            'recentActivity'
        ));
    }

    public function getDashboardData()
    {
        try {
            $data = [
                'totalUsers' => User::count(),
                'totalCards' => Card::count(),
                'todayNewCards' => Card::whereDate('created_at', Carbon::today())->count(),
                'systemStatus' => 'Online',
                'recentActivity' => $this->getRecentActivity()
            ];

            return response()->json($data);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to fetch dashboard data',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    private function getRecentActivity()
    {
        // Get new users
        $newUsers = User::select('username', 'created_at')
            ->where('created_at', '>=', Carbon::now()->subDays(7))
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($user) {
                return [
                    'user' => $user->username,
                    'action' => 'joined the platform',
                    'timestamp' => $user->created_at->diffForHumans()
                ];
            });

        // Get new cards
        $newCards = Card::select('cards.*', 'users.username')
            ->join('users', 'cards.user_id', '=', 'users.id')
            ->where('cards.created_at', '>=', Carbon::now()->subDays(7))
            ->orderBy('cards.created_at', 'desc')
            ->get()
            ->map(function ($card) {
                return [
                    'user' => $card->username,
                    'action' => 'created a new card - ' . $card->card_name,
                    'timestamp' => $card->created_at->diffForHumans()
                ];
            });

        // Merge and sort activities
        return $newUsers->concat($newCards)
            ->sortByDesc(function ($activity) {
                return Carbon::parse($activity['timestamp']);
            })
            ->take(10)
            ->values()
            ->all();
    }

    public function userManagement()
    {
        $users = User::select('id', 'username', 'email', 'role', 'joined_date')->get();
        return view('admin_users', compact('users'));
    }

    public function getUsers(Request $request)
    {
        try {
            $search = $request->input('search', '');

            $users = User::select('id', 'username', 'email', 'role', 'joined_date', 'full_name', 'phone', 'location')
                ->when($search, function ($query, $search) {
                    return $query->where('username', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%")
                        ->orWhere('role', 'like', "%{$search}%");
                })
                ->orderBy('joined_date', 'desc')
                ->get();

            return response()->json([
                'success' => true,
                'users' => $users
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch users'
            ], 500);
        }
    }


    public function addUser(Request $request)
    {
        $validated = $request->validate([
            'username' => 'required|unique:users|max:50',
            'email' => 'required|email|unique:users|max:100',
            'password' => 'required|min:6',
            'role' => 'required|in:user,admin',
            'full_name' => 'required|max:100',
            'phone' => 'nullable|max:15',
            'location' => 'nullable|max:100'
        ]);

        $user = new User();
        $user->username = $validated['username'];
        $user->email = $validated['email'];
        $user->password_hash = Hash::make($validated['password']);
        $user->role = $validated['role'];
        $user->full_name = $validated['full_name'];
        $user->phone = $validated['phone'];
        $user->location = $validated['location'];
        $user->joined_date = now();
        $user->save();

        return response()->json([
            'message' => 'User created successfully',
            'user' => $user
        ]);
    }

    public function updateUser(Request $request, $id)
    {
        try {
            $user = User::findOrFail($id);

            $validated = $request->validate([
                'username' => ['required', 'max:50', Rule::unique('users')->ignore($id)],
                'email' => ['required', 'email', 'max:100', Rule::unique('users')->ignore($id)],
                'role' => ['required', Rule::in(['user', 'admin'])],
                'full_name' => 'required|max:100',
                'phone' => 'nullable|max:15',
                'location' => 'nullable|max:100'
            ]);

            $user->update($validated);

            return response()->json([
                'success' => true,
                'message' => 'User updated successfully',
                'user' => $user
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'User not found'
            ], 404);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while updating the user'
            ], 500);
        }
    }

    public function deleteUser($id)
    {
        $user = User::findOrFail($id);
        $user->delete();

        return response()->json([
            'message' => 'User deleted successfully'
        ]);
    }

    public function promoteUser($id)
    {
        $user = User::findOrFail($id);
        $user->role = 'admin';
        $user->save();

        return response()->json([
            'message' => 'User promoted to admin successfully',
            'user' => $user
        ]);
    }

    public function getUser($id)
    {
        try {
            $user = User::findOrFail($id);
            return response()->json([
                'success' => true,
                'user' => $user
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'User not found'
            ], 404);
        }
    }

    public function cardManagement()
    {
        $cards = Card::with('user')->get();
        return view('admin_cards', compact('cards'));
    }

    public function getCards(Request $request)
    {
        try {
            $search = $request->input('search', '');

            $cards = Card::select(
                'cards.card_id',
                'cards.card_name',
                'cards.full_name',
                'cards.created_at',
                'users.username as created_by'
            )
                ->join('users', 'cards.user_id', '=', 'users.id')
                ->when($search, function ($query, $search) {
                    return $query->where('cards.card_name', 'like', "%{$search}%")
                        ->orWhere('users.username', 'like', "%{$search}%")
                        ->orWhere('cards.full_name', 'like', "%{$search}%");
                })
                ->orderBy('cards.created_at', 'desc')
                ->get()
                ->map(function ($card) {
                    return [
                        'id' => $card->card_id,
                        'title' => $card->card_name,
                        'createdBy' => $card->created_by,
                        'fullName' => $card->full_name,
                        'creationDate' => $card->created_at->format('Y-m-d'),
                        'status' => 'Active' // You can add a status field to your cards table if needed
                    ];
                });

            return response()->json([
                'success' => true,
                'cards' => $cards
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch cards'
            ], 500);
        }
    }

    public function getCard($id)
    {
        try {
            $card = Card::with('user')
                ->where('card_id', $id)
                ->firstOrFail();

            return response()->json([
                'success' => true,
                'card' => $card
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Card not found'
            ], 404);
        }
    }

    public function deleteCard($id)
    {
        try {
            $card = Card::findOrFail($id);
            $card->delete();

            return response()->json([
                'success' => true,
                'message' => 'Card deleted successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete card'
            ], 500);
        }
    }

    public function adminProfile()
    {
        $user = Auth::user(); // Get the currently authenticated user
        return view('admin_profile', compact('user'));
    }

    public function updateProfile(Request $request)
    {
        try {
            $user = Auth::user();
            
            $validated = $request->validate([
                'full_name' => 'required|max:100',
                'phone' => 'nullable|max:15',
                'location' => 'nullable|max:100',
            ]);

            $user->update($validated);

            return response()->json([
                'success' => true,
                'message' => 'Profile updated successfully',
                'user' => $user
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update profile'
            ], 500);
        }
    }

    public function updateProfilePicture(Request $request)
    {
        try {
            $request->validate([
                'profile_picture' => 'required|image|max:2048' // Max 2MB
            ]);

            $user = Auth::user();
            
            if ($request->hasFile('profile_picture')) {
                $file = $request->file('profile_picture');
                $filename = time() . '.' . $file->getClientOriginalExtension();
                $file->storeAs('public/profile_pictures', $filename);
                
                $user->profile_picture = 'storage/profile_pictures/' . $filename;
                $user->save();
            }

            return response()->json([
                'success' => true,
                'message' => 'Profile picture updated successfully',
                'profile_picture_url' => asset($user->profile_picture)
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update profile picture'
            ], 500);
        }
    }

}
