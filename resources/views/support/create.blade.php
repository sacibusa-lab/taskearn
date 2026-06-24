@extends('layouts.app')

@section('title', 'Create Support Ticket')

@section('header')
<div>
    <a href="{{ route('support.index') }}" class="text-sm text-indigo-600 hover:text-indigo-500 font-medium mb-2 inline-block">&larr; Back to Tickets</a>
    <h1 class="text-2xl font-bold text-gray-900">Create Support Ticket</h1>
</div>
@endsection

@section('content')
<div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-8">
        <form action="{{ route('support.store') }}" method="POST" class="space-y-6">
            @csrf

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Subject</label>
                <input type="text" name="subject" value="{{ old('subject') }}" required class="w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" placeholder="Brief summary of your issue">
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Category</label>
                    <select name="category" required class="w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        @foreach(\App\Models\SupportTicket::categories() as $value => $label)
                            <option value="{{ $value }}" @if(old('category') === $value) selected @endif>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Priority</label>
                    <select name="priority" required class="w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        <option value="low" @if(old('priority') === 'low') selected @endif>Low</option>
                        <option value="medium" @if(old('priority') === 'medium') selected @endif>Medium</option>
                        <option value="high" @if(old('priority') === 'high') selected @endif>High</option>
                    </select>
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Message</label>
                <textarea name="message" rows="8" required class="w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" placeholder="Describe your issue in detail...">{{ old('message') }}</textarea>
                <p class="text-xs text-gray-400 mt-1">Be as detailed as possible so we can help you faster.</p>
            </div>

            <div class="flex justify-end space-x-3 pt-4 border-t border-gray-100">
                <a href="{{ route('support.index') }}" class="px-4 py-2 border border-gray-300 rounded-xl text-sm font-medium text-gray-700 hover:bg-gray-50">Cancel</a>
                <button type="submit" class="px-6 py-2 bg-indigo-600 text-white rounded-xl hover:bg-indigo-700 text-sm font-medium">Submit Ticket</button>
            </div>
        </form>
    </div>
</div>
@endsection
