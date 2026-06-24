@extends('admin.layouts.admin')
@section('title', 'Edit Announcement')
@section('content')
<div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <a href="{{ route('admin.announcements.index') }}" class="text-sm text-indigo-600 font-medium mb-4 inline-block">&larr; Back</a>
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-8">
        <h2 class="text-xl font-bold text-gray-900 mb-6">Edit: {{ $announcement->title }}</h2>
        <form action="{{ route('admin.announcements.update', $announcement) }}" method="POST" enctype="multipart/form-data" class="space-y-6">
            @csrf @method('PUT')
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Title</label>
                    <input type="text" name="title" value="{{ old('title', $announcement->title) }}" required class="w-full rounded-xl border-gray-300">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Banner Image</label>
                    @if($announcement->image)
                        <img src="{{ asset('storage/' . $announcement->image) }}" class="w-full max-h-32 object-cover rounded-xl mb-2">
                    @endif
                    <input type="file" name="image" accept="image/*" class="w-full rounded-xl border-gray-300 file:py-2 file:px-4 file:border-0 file:rounded-xl file:bg-indigo-50 file:text-indigo-700">
                </div> class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Message</label>
                    <textarea name="message" rows="5" required class="w-full rounded-xl border-gray-300">{{ old('message', $announcement->message) }}</textarea>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Type</label>
                    <select name="type" class="w-full rounded-xl border-gray-300">
                        @foreach(['info' => '📘 Info', 'warning' => '⚠️ Warning', 'success' => '✅ Success', 'danger' => '❌ Danger', 'promo' => '🎉 Promo'] as $v => $l)
                            <option value="{{ $v }}" @if($announcement->type === $v) selected @endif>{{ $l }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Action Label</label>
                    <input type="text" name="action_label" value="{{ old('action_label', $announcement->action_label) }}" class="w-full rounded-xl border-gray-300">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Action URL</label>
                    <input type="text" name="action_url" value="{{ old('action_url', $announcement->action_url) }}" class="w-full rounded-xl border-gray-300">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Start At</label>
                    <input type="datetime-local" name="starts_at" value="{{ old('starts_at', $announcement->starts_at?->format('Y-m-d\TH:i')) }}" class="w-full rounded-xl border-gray-300">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">End At</label>
                    <input type="datetime-local" name="ends_at" value="{{ old('ends_at', $announcement->ends_at?->format('Y-m-d\TH:i')) }}" class="w-full rounded-xl border-gray-300">
                </div>
            </div>
            <div class="flex justify-end space-x-3 pt-4 border-t border-gray-100">
                <a href="{{ route('admin.announcements.index') }}" class="px-4 py-2 border border-gray-300 rounded-xl text-sm">Cancel</a>
                <button type="submit" class="px-6 py-2 bg-indigo-600 text-white rounded-xl text-sm font-medium">Update</button>
            </div>
        </form>
    </div>
</div>
@endsection
