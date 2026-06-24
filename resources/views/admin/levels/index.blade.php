@extends('admin.layouts.admin')

@section('title', 'Levels')

@section('content')
<div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100">
        <div class="px-6 py-4 border-b border-gray-100">
            <h2 class="text-lg font-semibold text-gray-900">Configure Levels</h2>
        </div>
        <div class="p-6">
            <form action="{{ route('admin.settings.update') }}" method="POST" class="mb-8">
                @csrf
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="text-left text-gray-500 border-b border-gray-200">
                                <th class="pb-3 font-medium">Level</th>
                                <th class="pb-3 font-medium">Description</th>
                                <th class="pb-3 font-medium">Deposit Amount ($)</th>
                                <th class="pb-3 font-medium">Weekly Payout ($)</th>
                                <th class="pb-3 font-medium">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @foreach($levels as $level)
                                <tr>
                                    <td class="py-4 font-bold text-gray-900 text-lg">Level {{ $level->level }}</td>
                                    <td class="py-4 text-gray-500">{{ $level->description }}</td>
                                    <td class="py-4">
                                        <form id="level-form-{{ $level->id }}" action="{{ route('admin.levels.update', $level) }}" method="POST" class="flex items-center space-x-2">
                                            @csrf
                                            @method('PUT')
                                            <input type="number" name="deposit_amount" value="{{ $level->deposit_amount }}" step="0.01" min="0" class="w-28 rounded-lg border-gray-300 text-sm">
                                    </td>
                                    <td class="py-4">
                                            <input type="number" name="weekly_payout" value="{{ $level->weekly_payout }}" step="0.01" min="0" class="w-28 rounded-lg border-gray-300 text-sm">
                                    </td>
                                    <td class="py-4">
                                            <input type="hidden" name="description" value="{{ $level->description }}">
                                            <button type="submit" class="px-3 py-1.5 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 text-xs font-medium">Save</button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
