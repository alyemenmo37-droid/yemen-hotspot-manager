<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Subscription;
use Illuminate\Http\Request;

class SubscriptionController extends Controller
{
    public function index(Request $request)
    {
        $subs = Subscription::where('user_id', $request->user()->id)
            ->with('speedLevel')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $subs,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'speed_level_id' => 'required|exists:speed_levels,id',
        ]);

        $subscription = Subscription::create([
            'user_id' => $request->user()->id,
            'speed_level_id' => $validated['speed_level_id'],
            'status' => 'active',
            'starts_at' => now(),
            'ends_at' => now()->addMonth(),
        ]);

        return response()->json([
            'success' => true,
            'data' => $subscription,
        ], 201);
    }
}
