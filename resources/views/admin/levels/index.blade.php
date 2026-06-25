@extends('admin.layouts.admin')

@section('title', 'Levels')

@section('content')
<div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100">
        <div class="px-6 py-4 border-b border-gray-100">
            <h2 class="text-lg font-semibold text-gray-900">Configure Levels</h2>
        </div>
        <div class="p-6">
            <form action="{{ route('admin.levels.update-all') }}" method="POST">
                @csrf
                @method('PUT')
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="text-left text-gray-500 border-b border-gray-200">
                                <th class="pb-3 font-medium">Level</th>
                                <th class="pb-3 font-medium">Description</th>
                                <th class="pb-3 font-medium">Deposit Amount ($)</th>
                                <th class="pb-3 font-medium">Weekly Payout ($)</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @foreach($levels as $level)
                                <tr>
                                    <td class="py-4 font-bold text-gray-900 text-lg">Level {{ $level->level }}</td>
                                    <td class="py-4 text-gray-500">{{ $level->description }}</td>
                                    <td class="py-4">
                                        <input type="number" name="levels[{{ $level->id }}][deposit_amount]" value="{{ $level->deposit_amount }}" step="0.01" min="0" class="w-28 rounded-lg border-gray-300 text-sm">
                                    </td>
                                    <td class="py-4">
                                        <input type="number" name="levels[{{ $level->id }}][weekly_payout]" value="{{ $level->weekly_payout }}" step="0.01" min="0" class="w-28 rounded-lg border-gray-300 text-sm">
                                        <input type="hidden" name="levels[{{ $level->id }}][description]" value="{{ $level->description }}">
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="mt-6 flex justify-end">
                    <button type="submit" class="px-6 py-2.5 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 text-sm font-medium">Save All Changes</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
