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

    public function updateAll(Request $request)
    {
        $request->validate([
            'levels.*.deposit_amount' => ['required', 'numeric', 'min:0'],
            'levels.*.weekly_payout' => ['required', 'numeric', 'min:0'],
            'levels.*.description' => ['nullable', 'string', 'max:500'],
        ]);

        $levelsData = $request->input('levels', []);

        foreach ($levelsData as $id => $data) {
            $level = Level::findOrFail($id);
            $level->update([
                'deposit_amount' => $data['deposit_amount'],
                'weekly_payout' => $data['weekly_payout'],
                'description' => $data['description'] ?? $level->description,
            ]);
        }

        return redirect()->route('admin.levels.index')->with('success', 'All levels updated successfully.');
    }
}
