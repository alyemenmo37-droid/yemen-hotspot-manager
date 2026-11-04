<?php

namespace App\Http\Controllers\Api;

use App\Models\SpeedLevel;
use App\Models\Subscription;
use Illuminate\Http\Request;

class SubscriptionWebController extends Controller
{
    public function index()
    {
        $subs = Subscription::where('user_id', auth()->id())
            ->with('speedLevel')
            ->get();

        return view('subscriptions.index', compact('subs'));
    }

    public function create()
    {
        $speedLevels = SpeedLevel::where('is_active', true)->get();

        return view('subscriptions.create', compact('speedLevels'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'speed_level_id' => 'required|exists:speed_levels,id',
        ]);

        Subscription::create([
            'user_id' => auth()->id(),
            'speed_level_id' => $validated['speed_level_id'],
            'status' => 'active',
            'starts_at' => now(),
            'ends_at' => now()->addMonth(),
        ]);

        return redirect()->route('subscriptions.index')->with('success', 'تم إنشاء الاشتراك');
    }
}
