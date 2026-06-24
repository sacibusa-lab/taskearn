<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Level;
use Illuminate\Http\Request;

class LevelController extends Controller
{
    public function index()
    {
        $levels = Level::orderBy('level')->get();
        return view('admin.levels.index', compact('levels'));
    }

    public function update(Request $request, Level $level)
    {
        $request->validate([
            'deposit_amount' => ['required', 'numeric', 'min:0'],
            'weekly_payout' => ['required', 'numeric', 'min:0'],
            'description' => ['nullable', 'string', 'max:500'],
        ]);

        $level->update([
            'deposit_amount' => $request->deposit_amount,
            'weekly_payout' => $request->weekly_payout,
            'description' => $request->description,
        ]);

        return redirect()->route('admin.levels.index')->with('success', "Level {$level->level} updated successfully.");
    }
}
