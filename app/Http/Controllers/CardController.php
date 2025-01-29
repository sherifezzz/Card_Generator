<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Card;
use Illuminate\Support\Facades\Auth;

class CardController extends Controller
{
    // Add this new method
    public function index()
    {
        try {
            $cards = Card::where('user_id', Auth::id())->get();
            return response()->json($cards);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to fetch cards'], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $card = Card::findOrFail($id);

            // Check if the authenticated user owns this card
            if ($card->user_id !== Auth::id()) {
                return response()->json(['error' => 'Unauthorized'], 403);
            }

            $card->delete();
            return response()->json(['message' => 'Card deleted successfully']);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to delete card'], 500);
        }
    }
    public function showCardCreation()
    {
        return view('card_creation');
    }


    public function store(Request $request)
    {
        try {
            // Validate the request data
            $validatedData = $request->validate([
                'cardName' => 'required|string|max:100',
                'fullName' => 'required|string|max:100',
                'jobTitle' => 'nullable|string|max:100',
                'email' => 'nullable|email|max:100',
                'phone' => 'nullable|string|max:15',
                'address' => 'nullable|string',
                'qrUrl' => 'nullable|url|max:255',
            ]);

            // Create new card
            $card = new Card();
            $card->user_id = Auth::id();
            $card->card_name = $validatedData['cardName'];
            $card->full_name = $validatedData['fullName'];
            $card->job_title = $validatedData['jobTitle'];
            $card->email = $validatedData['email'];
            $card->phone = $validatedData['phone'];
            $card->address = $validatedData['address'];
            $card->qr_url = $validatedData['qrUrl'];
            
            $card->save();

            return response()->json([
                'message' => 'Card saved successfully!',
                'card' => $card
            ], 200);
            
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'error' => 'Validation failed',
                'messages' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            \Log::error('Card creation error: ' . $e->getMessage());
            return response()->json([
                'error' => 'Failed to save card',
                'message' => $e->getMessage()
            ], 500);
        }
    }


    public function update(Request $request, $id)
    {
        $card = Card::findOrFail($id);

        // Check if the authenticated user owns this card
        if ($card->user_id !== Auth::id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        // Validate the request data
        $validatedData = $request->validate([
            'cardName' => 'required|string|max:100',
            'fullName' => 'required|string|max:100',
            'jobTitle' => 'nullable|string|max:100',
            'email' => 'nullable|email|max:100',
            'phone' => 'nullable|string|max:15',
            'address' => 'nullable|string',
            'qrUrl' => 'nullable|url|max:255',
        ]);

        // Map the validated data to the correct database column names
        $card->card_name = $validatedData['cardName'];
        $card->full_name = $validatedData['fullName'];
        $card->job_title = $validatedData['jobTitle'];
        $card->email = $validatedData['email'];
        $card->phone = $validatedData['phone'];
        $card->address = $validatedData['address'];
        $card->qr_url = $validatedData['qrUrl'];
        $card->save();

        // Return the updated card
        return response()->json($card);
    }

    public function show($id)
    {
        $card = Card::findOrFail($id);

        if ($card->user_id !== Auth::id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        return response()->json($card);
    }
}
