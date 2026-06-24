@extends('layouts.app')

@section('title', 'Notification Preferences')

@section('header')
<div>
    <h1 class="text-2xl font-bold text-gray-900">Notification Preferences</h1>
    <p class="text-sm text-gray-500 mt-1">Choose how you want to be notified</p>
</div>
@endsection

@section('content')
<div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-8">
        <form action="{{ route('notifications.preferences.update') }}" method="POST">
            @csrf

            <div class="space-y-6">
                @foreach($preferences as $type => $pref)
                    <div class="flex items-center justify-between p-4 bg-gray-50 rounded-xl">
                        <div class="flex-1">
                            <p class="text-sm font-medium text-gray-900">{{ \App\Models\NotificationPreference::getTypeLabel($type) }}</p>
                            <p class="text-xs text-gray-500 mt-0.5">
                                {{ match($type) {
                                    'task_approved' => 'When your task submission is approved',
                                    'task_rejected' => 'When your task submission is rejected',
                                    'deposit_received' => 'When a deposit is credited to your account',
                                    'referral_bonus' => 'When you earn a referral commission',
                                    'withdrawal_status' => 'When your withdrawal request is processed',
                                    'festive_reward' => 'When you receive a festive program reward',
                                    'festive_status' => 'When a festive program is created or changes status',
                                    default => '',
                                } }}
                            </p>
                        </div>
                        <div class="flex items-center space-x-4">
                            <label class="inline-flex items-center">
                                <input type="hidden" name="preferences[{{ $type }}][in_app]" value="0">
                                <input type="checkbox" name="preferences[{{ $type }}][in_app]" value="1" {{ $pref->in_app ? 'checked' : '' }} class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500">
                                <span class="ms-1.5 text-xs text-gray-600">In-app</span>
                            </label>
                            <label class="inline-flex items-center">
                                <input type="hidden" name="preferences[{{ $type }}][email]" value="0">
                                <input type="checkbox" name="preferences[{{ $type }}][email]" value="1" {{ $pref->email ? 'checked' : '' }} class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500">
                                <span class="ms-1.5 text-xs text-gray-600">Email</span>
                            </label>
                            <label class="inline-flex items-center">
                                <input type="hidden" name="preferences[{{ $type }}][sms]" value="0">
                                <input type="checkbox" name="preferences[{{ $type }}][sms]" value="1" {{ $pref->sms ? 'checked' : '' }} class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500">
                                <span class="ms-1.5 text-xs text-gray-600">SMS</span>
                            </label>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="flex justify-end mt-8 pt-6 border-t border-gray-100">
                <button type="submit" class="px-6 py-2.5 bg-indigo-600 text-white rounded-xl hover:bg-indigo-700 text-sm font-medium transition-colors">
                    Save Preferences
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
