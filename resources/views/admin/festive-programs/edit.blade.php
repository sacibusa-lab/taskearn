@extends('admin.layouts.admin')

@section('title', 'Edit Festive Program')

@section('content')
<div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <a href="{{ route('admin.festive-programs.index') }}" class="text-sm text-indigo-600 hover:text-indigo-500 font-medium mb-4 inline-block">&larr; Back</a>

    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-8">
        <h2 class="text-xl font-bold text-gray-900 mb-6">Edit: {{ $festiveProgram->title }}</h2>

        <form action="{{ route('admin.festive-programs.update', $festiveProgram) }}" method="POST" enctype="multipart/form-data" class="space-y-6">
            @csrf
            @method('PUT')

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Title</label>
                <input type="text" name="title" value="{{ old('title', $festiveProgram->title) }}" required class="w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Description</label>
                <textarea name="description" rows="3" class="w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">{{ old('description', $festiveProgram->description) }}</textarea>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Banner Image</label>
                @if($festiveProgram->banner)
                    <div class="mb-2">
                        <img src="{{ asset('storage/' . $festiveProgram->banner) }}" alt="Current banner" class="w-full max-h-32 object-cover rounded-xl">
                        <p class="text-xs text-gray-400 mt-1">Current banner. Upload a new one to replace it.</p>
                    </div>
                @endif
                <input type="file" name="banner" accept="image/*" class="w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 file:py-2 file:px-4 file:border-0 file:rounded-xl file:bg-indigo-50 file:text-indigo-700 file:font-medium hover:file:bg-indigo-100">
                <p class="text-xs text-gray-400 mt-1">Optional. Recommended size: 1200x400px. Max 2MB.</p>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Start Date</label>
                    <input type="datetime-local" name="start_date" value="{{ old('start_date', $festiveProgram->start_date?->format('Y-m-d\TH:i')) }}" required class="w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">End Date</label>
                    <input type="datetime-local" name="end_date" value="{{ old('end_date', $festiveProgram->end_date?->format('Y-m-d\TH:i')) }}" required class="w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Bonus Type</label>
                    <select name="bonus_type" required class="w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        <option value="fixed" @if($festiveProgram->bonus_type === 'fixed') selected @endif>Fixed Amount</option>
                        <option value="percentage" @if($festiveProgram->bonus_type === 'percentage') selected @endif>Percentage of Earnings</option>
                        <option value="task_bonus" @if($festiveProgram->bonus_type === 'task_bonus') selected @endif>Task Bonus</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Bonus Value</label>
                    <input type="number" name="bonus_value" value="{{ old('bonus_value', $festiveProgram->bonus_value) }}" step="0.01" min="0.01" required class="w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                    <select name="status" required class="w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        <option value="upcoming" @if($festiveProgram->status === 'upcoming') selected @endif>Upcoming</option>
                        <option value="active" @if($festiveProgram->status === 'active') selected @endif>Active</option>
                        <option value="completed" @if($festiveProgram->status === 'completed') selected @endif>Completed</option>
                        <option value="cancelled" @if($festiveProgram->status === 'cancelled') selected @endif>Cancelled</option>
                    </select>
                </div>
            </div>

            <div class="flex justify-end space-x-3 pt-4 border-t border-gray-100">
                <a href="{{ route('admin.festive-programs.index') }}" class="px-4 py-2 border border-gray-300 rounded-xl text-sm font-medium text-gray-700 hover:bg-gray-50">Cancel</a>
                <button type="submit" class="px-6 py-2 bg-indigo-600 text-white rounded-xl hover:bg-indigo-700 text-sm font-medium">Update Program</button>
            </div>
        </form>
    </div>
</div>
@endsection
