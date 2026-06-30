@extends('admin.layouts.admin')

@section('title', 'Settings')

@php
    $activeTab = request('tab', 'global');
    $tabLabels = [
        'global' => ['icon' => 'M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.066 2.573c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.573 1.066c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.066-2.573c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z', 'label' => 'Global'],

        'apis' => ['icon' => 'M13 10V3L4 14h7v7l9-11h-7z', 'label' => 'APIs'],
        'currency' => ['icon' => 'M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z', 'label' => 'Currency'],
        'theme' => ['icon' => 'M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.829 2.829a2 2 0 010 2.828l-8.486 8.485M7 17h.01', 'label' => 'Theme & Code'],
        'maintenance' => ['icon' => 'M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.066 2.573c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.573 1.066c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.066-2.573c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z', 'label' => 'Maintenance'],
        'legal' => ['icon' => 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z', 'label' => 'Legal'],
    ];
@endphp

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        {{-- Tab Navigation --}}
        <div class="border-b border-gray-200">
            <nav class="flex -mb-px" role="tablist">
                @foreach($tabLabels as $tabKey => $tabInfo)
                    <a href="{{ route('admin.settings.index', ['tab' => $tabKey]) }}"
                       class="group relative min-w-0 flex-1 overflow-hidden py-4 px-4 text-sm font-medium text-center
                              {{ $activeTab === $tabKey
                                  ? 'text-indigo-600 border-b-2 border-indigo-600'
                                  : 'text-gray-500 hover:text-gray-700 border-b-2 border-transparent hover:border-gray-300' }}
                              transition-all duration-200"
                       role="tab" aria-selected="{{ $activeTab === $tabKey ? 'true' : 'false' }}">
                        <div class="flex items-center justify-center space-x-2">
                            <svg class="w-5 h-5 {{ $activeTab === $tabKey ? 'text-indigo-600' : 'text-gray-400 group-hover:text-gray-500' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $tabInfo['icon'] }}"/>
                            </svg>
                            <span>{{ $tabInfo['label'] }}</span>
                        </div>
                    </a>
                @endforeach
            </nav>
        </div>

        {{-- Tab Content --}}
        <form action="{{ route('admin.settings.update', ['tab' => $activeTab]) }}" method="POST" enctype="multipart/form-data">
            @csrf

            <div class="p-6 sm:p-8">
                @php $tabSettings = $settings[$activeTab] ?? collect(); @endphp

                @if($activeTab === 'paystack')
                    {{-- ===== PAYSTACK TAB ===== --}}
                    <div class="space-y-8">
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900 mb-1">Paystack Integration</h3>
                            <p class="text-sm text-gray-500 mb-6">Configure Paystack for Dedicated Virtual Accounts (DVA) and Naira payment collection.</p>

                            <div class="bg-blue-50 border border-blue-200 rounded-xl p-4 mb-6">
                                <div class="flex items-start space-x-3">
                                    <svg class="w-5 h-5 text-blue-600 mt-0.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                    <div>
                                        <p class="text-sm font-medium text-blue-800">How to set up Paystack</p>
                                        <p class="text-xs text-blue-700 mt-1">
                                            <strong>1.</strong> Sign up at <strong>paystack.com</strong><br>
                                            <strong>2.</strong> Go to <strong>Settings → API Keys & Webhooks</strong><br>
                                            <strong>3.</strong> Copy your <strong>Live Public Key</strong> and <strong>Live Secret Key</strong><br>
                                            <strong>4.</strong> For DVA: Go to <strong>Plugins → Dedicated Virtual Account</strong> and enable it<br>
                                            <strong>5.</strong> Set the webhook URL to: <code class="bg-blue-100 px-1 rounded">{{ url('/api/paystack/webhook') }}</code>
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            @foreach($tabSettings as $setting)
                                <div>
                                    <label for="settings[{{ $setting->key }}]" class="block text-sm font-medium text-gray-700 mb-1">
                                        {{ ucwords(str_replace('_', ' ', $setting->key)) }}
                                    </label>
                                    @if($setting->type === 'boolean')
                                        <select name="settings[{{ $setting->key }}]" id="settings[{{ $setting->key }}]" class="w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                            <option value="true" @if($setting->value === 'true') selected @endif>Enabled</option>
                                            <option value="false" @if($setting->value === 'false') selected @endif>Disabled</option>
                                        </select>
                                    @elseif(Str::contains($setting->key, 'secret_key'))
                                        <div class="relative">
                                            <input type="password" name="settings[{{ $setting->key }}]" id="settings[{{ $setting->key }}]" value="{{ $setting->value }}" class="w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 pr-10" autocomplete="off">
                                            <button type="button" onclick="togglePw('{{ $setting->key }}')" class="absolute inset-y-0 right-0 pr-3 flex items-center">
                                                <svg class="w-5 h-5 text-gray-400 hover:text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                            </button>
                                        </div>
                                    @else
                                        <input type="text" name="settings[{{ $setting->key }}]" id="settings[{{ $setting->key }}]" value="{{ $setting->value }}" class="w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    @endif
                                    @if($setting->description)
                                        <p class="mt-1 text-xs text-gray-500">{{ $setting->description }}</p>
                                    @endif
                                </div>
                            @endforeach
                        </div>

                        {{-- Connection Status --}}
                        <div class="bg-gray-50 rounded-xl p-4 border border-gray-200">
                            <div class="flex items-center space-x-2">
                                <div class="w-2 h-2 rounded-full {{ !empty($tabSettings->firstWhere('key', 'paystack_secret_key')?->value) ? 'bg-green-500' : 'bg-red-500' }}"></div>
                                <p class="text-sm text-gray-600">
                                    <strong>Status:</strong>
                                    {{ !empty($tabSettings->firstWhere('key', 'paystack_secret_key')?->value) ? 'API keys configured' : 'API keys not set' }}
                                </p>
                            </div>
                            @if(!empty($tabSettings->firstWhere('key', 'paystack_dva_enabled')?->value) && $tabSettings->firstWhere('key', 'paystack_dva_enabled')->value === 'true')
                                <p class="text-xs text-green-600 mt-2">Dedicated Virtual Accounts are enabled</p>
                            @endif
                        </div>
                    </div>

                @elseif($activeTab === 'apis')
                    {{-- ===== APIS TAB ===== --}}
                    <div class="space-y-8">
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900 mb-1">🔌 API Integrations</h3>
                            <p class="text-sm text-gray-500 mb-6">Manage third-party API keys for payment processing and AI content generation.</p>
                        </div>

                        {{-- Paystack Section --}}
                        <div class="bg-white border border-gray-200 rounded-xl p-6">
                            <div class="flex items-center space-x-3 mb-4">
                                <div class="w-10 h-10 bg-green-100 text-green-600 rounded-xl flex items-center justify-center">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 11c0-1.933-1.567-3.5-3.5-3.5S5 9.067 5 11c0 1.933 1.567 3.5 3.5 3.5S12 12.933 12 11zm7 0c0-1.933-1.567-3.5-3.5-3.5S12 9.067 12 11c0 1.933 1.567 3.5 3.5 3.5S19 12.933 19 11zm-3.5 9.5c1.933 0 3.5-1.567 3.5-3.5s-1.567-3.5-3.5-3.5S12 15.067 12 17s1.567 3.5 3.5 3.5z"/></svg>
                                </div>
                                <div>
                                    <h4 class="text-base font-bold text-gray-900">Paystack</h4>
                                    <p class="text-xs text-gray-500">Payment processing, DVA, and bank verification</p>
                                </div>
                                <div class="ml-auto flex items-center space-x-1.5 text-xs">
                                    <div class="w-2 h-2 rounded-full {{ !empty($tabSettings->firstWhere('key', 'paystack_secret_key')?->value) ? 'bg-green-500' : 'bg-red-500' }}"></div>
                                    <span class="text-gray-500">{{ !empty($tabSettings->firstWhere('key', 'paystack_secret_key')?->value) ? 'Connected' : 'Not set' }}</span>
                                </div>
                            </div>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                @foreach($tabSettings->filter(fn($s) => str_starts_with($s->key, 'paystack')) as $setting)
                                    <div>
                                        <label for="settings[{{ $setting->key }}]" class="block text-sm font-medium text-gray-700 mb-1">
                                            {{ ucwords(str_replace('_', ' ', str_replace('paystack_', '', $setting->key))) }}
                                        </label>
                                        <div class="flex space-x-2">
                                            <input type="password" name="settings[{{ $setting->key }}]" id="{{ $setting->key }}" value="{{ $setting->value }}"
                                                   class="flex-1 rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 font-mono text-sm"
                                                   placeholder="Enter {{ str_replace('_', ' ', $setting->key) }}">
                                            <button type="button" onclick="togglePw('{{ $setting->key }}')" class="p-2 text-gray-400 hover:text-gray-600">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                            </button>
                                        </div>
                                        @if($setting->description)
                                            <p class="mt-1 text-xs text-gray-500">{{ $setting->description }}</p>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                            @if(!empty($tabSettings->firstWhere('key', 'paystack_dva_enabled')?->value) && $tabSettings->firstWhere('key', 'paystack_dva_enabled')->value === 'true')
                                <p class="text-xs text-green-600 mt-3 flex items-center">
                                    <svg class="w-3.5 h-3.5 me-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                    Dedicated Virtual Accounts enabled
                                </p>
                            @endif
                        </div>

                        {{-- OpenRouter Section --}}
                        @php
                            $orKeySetting = $tabSettings->firstWhere('key', 'openrouter_api_key');
                            $orKey = $orKeySetting->value ?? '';
                        @endphp
                        <div class="bg-white border border-gray-200 rounded-xl p-6">
                            <div class="flex items-center space-x-3 mb-5">
                                <div class="w-10 h-10 bg-purple-100 text-purple-600 rounded-xl flex items-center justify-center">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                                </div>
                                <div>
                                    <h4 class="text-base font-bold text-gray-900">OpenRouter</h4>
                                    <p class="text-xs text-gray-500">AI content generation for Read & Earn articles</p>
                                </div>
                                <div class="ml-auto flex items-center space-x-1.5 text-xs">
                                    <div class="w-2 h-2 rounded-full {{ !empty($orKey) ? 'bg-green-500' : 'bg-red-500' }}"></div>
                                    <span class="text-gray-500">{{ !empty($orKey) ? 'Connected' : 'Not set' }}</span>
                                </div>
                            </div>

                            <label for="settings[openrouter_api_key]" class="block text-sm font-medium text-gray-700 mb-1.5">
                                OpenRouter API Key
                            </label>
                            <div class="flex space-x-2">
                                <input type="password" name="settings[openrouter_api_key]" id="openrouter_api_key" value="{{ $orKey }}"
                                       class="flex-1 rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 font-mono text-sm h-12 px-4"
                                       placeholder="sk-or-v1-...">
                                <button type="button" onclick="togglePw('openrouter_api_key')" class="px-4 py-2 border border-gray-300 rounded-xl text-gray-500 hover:text-gray-700 hover:bg-gray-50 transition-colors" title="Show/Hide">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                </button>
                            </div>
                            <p class="mt-1.5 text-xs text-gray-400">
                                🔑 Get a free API key at <a href="https://openrouter.ai/keys" target="_blank" class="text-indigo-600 hover:underline font-medium">openrouter.ai/keys</a>
                            </p>
                        </div>
                    </div>

                @elseif($activeTab === 'global')
                    {{-- ===== GLOBAL SETTINGS TAB ===== --}}
                    <div class="space-y-8">
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900 mb-1">Global Settings</h3>
                            <p class="text-sm text-gray-500 mb-6">Manage your platform branding and core configuration.</p>
                        </div>

                        {{-- Logo & Favicon Preview --}}
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                            <div class="bg-gray-50 rounded-xl border-2 border-dashed border-gray-200 p-6 text-center">
                                <p class="text-sm font-medium text-gray-700 mb-3">Site Logo Preview</p>
                                @php $logo = $tabSettings->firstWhere('key', 'site_logo')?->value; @endphp
                                @if($logo)
                                    <img src="{{ $logo }}" alt="Logo" class="max-h-16 mx-auto mb-2">
                                @else
                                    <div class="w-24 h-12 bg-indigo-100 rounded-lg flex items-center justify-center mx-auto mb-2">
                                        <span class="text-lg font-bold text-indigo-600">{{ substr(App\Models\AdminSetting::getValue('site_name', 'TE'), 0, 2) }}</span>
                                    </div>
                                @endif
                                <p class="text-xs text-gray-400">180 x 60 px recommended</p>
                            </div>
                            <div class="bg-gray-50 rounded-xl border-2 border-dashed border-gray-200 p-6 text-center">
                                <p class="text-sm font-medium text-gray-700 mb-3">Favicon Preview</p>
                                @php $favicon = $tabSettings->firstWhere('key', 'site_favicon')?->value; @endphp
                                @if($favicon)
                                    <img src="{{ $favicon }}" alt="Favicon" class="w-8 h-8 mx-auto mb-2">
                                @else
                                    <div class="w-8 h-8 bg-indigo-600 rounded-lg flex items-center justify-center mx-auto mb-2">
                                        <span class="text-xs font-bold text-white">{{ substr(App\Models\AdminSetting::getValue('site_name', 'TE'), 0, 1) }}</span>
                                    </div>
                                @endif
                                <p class="text-xs text-gray-400">32 x 32 px .ico or .png</p>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            @foreach($tabSettings as $setting)
                                @if(in_array($setting->key, ['site_logo', 'site_favicon']))
                                    <div x-data="{ preview: '{{ $setting->value }}' }">
                                        <label for="uploads[{{ $setting->key }}]" class="block text-sm font-medium text-gray-700 mb-1">
                                            {{ ucwords(str_replace('_', ' ', $setting->key)) }}
                                        </label>
                                        <div class="flex items-center space-x-3">
                                            <label class="cursor-pointer flex-1">
                                                <div class="flex items-center space-x-2 px-4 py-2.5 border-2 border-dashed border-gray-300 rounded-xl hover:border-indigo-400 hover:bg-indigo-50/50 transition-all">
                                                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/></svg>
                                                    <span class="text-sm text-gray-500">Click to upload</span>
                                                </div>
                                                <input type="file" name="uploads[{{ $setting->key }}]" id="uploads[{{ $setting->key }}]" accept="image/png,image/jpeg,image/jpg,image/gif,image/svg+xml,image/webp,image/x-icon" class="hidden" @change="const file = $event.target.files[0]; if(file) { const reader = new FileReader(); reader.onload = e => preview = e.target.result; reader.readAsDataURL(file); }">
                                            </label>
                                            @if($setting->value)
                                                <button type="button" onclick="document.getElementById('uploads[{{ $setting->key }}]').value = ''; document.querySelector('[name=\'settings[{{ $setting->key }}]\']').value = ''; this.closest('[x-data]').__x.$data.preview = ''" class="p-2 text-gray-400 hover:text-red-500 transition-colors" title="Remove">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                                </button>
                                            @endif
                                        </div>
                                        <input type="hidden" name="settings[{{ $setting->key }}]" value="{{ $setting->value }}">
                                        <div class="mt-2 text-center">
                                            <img :src="preview" alt="Preview" class="max-h-12 mx-auto rounded" x-show="preview">
                                            <p class="text-xs text-gray-400 mt-1" x-show="!preview">No file uploaded</p>
                                        </div>
                                        @if($setting->description)
                                            <p class="mt-1 text-xs text-gray-500">{{ $setting->description }}</p>
                                        @endif
                                    </div>
                                @else
                                    <div>
                                        <label for="settings[{{ $setting->key }}]" class="block text-sm font-medium text-gray-700 mb-1">
                                            {{ ucwords(str_replace('_', ' ', $setting->key)) }}
                                        </label>
                                        @if($setting->type === 'boolean')
                                            <select name="settings[{{ $setting->key }}]" id="settings[{{ $setting->key }}]" class="w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                                <option value="true" @if($setting->value === 'true') selected @endif>Yes</option>
                                                <option value="false" @if($setting->value === 'false') selected @endif>No</option>
                                            </select>
                                        @elseif($setting->type === 'number')
                                            <input type="number" name="settings[{{ $setting->key }}]" id="settings[{{ $setting->key }}]" value="{{ $setting->value }}" step="0.01" class="w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                        @else
                                            <input type="text" name="settings[{{ $setting->key }}]" id="settings[{{ $setting->key }}]" value="{{ $setting->value }}" class="w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                        @endif
                                        @if($setting->description)
                                            <p class="mt-1 text-xs text-gray-500">{{ $setting->description }}</p>
                                        @endif
                                    </div>
                                @endif
                            @endforeach
                        </div>
                    </div>

                @elseif($activeTab === 'currency')
                    {{-- ===== CURRENCY & PAYMENTS TAB ===== --}}
                    <div class="space-y-8">
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900 mb-1">Currency & Payment Settings</h3>
                            <p class="text-sm text-gray-500 mb-6">Configure your local currency, exchange rate, and payment limits.</p>
                        </div>

                        {{-- Currency Preview Card --}}
                        <div class="bg-gradient-to-r from-indigo-500 to-purple-600 rounded-xl p-6 text-white mb-6">
                            <p class="text-sm text-indigo-100 mb-1">Current Display Format</p>
                            <div class="flex items-end space-x-4">
                                <span class="text-3xl font-bold">{{ currency_raw(1234.56) }}</span>
                                <span class="text-lg text-indigo-200">({{ currency_code() }})</span>
                            </div>
                            <p class="text-xs text-indigo-200 mt-2">
                                1 USD = {{ currency(1) }}
                            </p>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            @foreach($tabSettings as $setting)
                                <div>
                                    <label for="settings[{{ $setting->key }}]" class="block text-sm font-medium text-gray-700 mb-1">
                                        {{ ucwords(str_replace('_', ' ', $setting->key)) }}
                                    </label>
                                    @if($setting->type === 'boolean')
                                        <select name="settings[{{ $setting->key }}]" id="settings[{{ $setting->key }}]" class="w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                            <option value="true" @if($setting->value === 'true') selected @endif>Yes</option>
                                            <option value="false" @if($setting->value === 'false') selected @endif>No</option>
                                        </select>
                                    @elseif($setting->type === 'number')
                                        <input type="number" name="settings[{{ $setting->key }}]" id="settings[{{ $setting->key }}]" value="{{ $setting->value }}" step="0.01" class="w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    @else
                                        <input type="text" name="settings[{{ $setting->key }}]" id="settings[{{ $setting->key }}]" value="{{ $setting->value }}" class="w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    @endif
                                    @if($setting->description)
                                        <p class="mt-1 text-xs text-gray-500">{{ $setting->description }}</p>
                                    @endif
                                </div>
                            @endforeach
                        </div>

                        {{-- Quick Exchange Rate Calculator --}}
                        <div class="bg-gray-50 rounded-xl border border-gray-200 p-4">
                            <p class="text-sm font-medium text-gray-700 mb-2">Quick Rate Calculator</p>
                            <p class="text-xs text-gray-500 mb-3">See how amounts will display with current settings:</p>
                            <div class="grid grid-cols-3 gap-3">
                                <div class="bg-white rounded-lg p-3 text-center border border-gray-200">
                                    <p class="text-xs text-gray-500">$10 USD</p>
                                    <p class="text-sm font-bold text-gray-900">{{ currency(10) }}</p>
                                </div>
                                <div class="bg-white rounded-lg p-3 text-center border border-gray-200">
                                    <p class="text-xs text-gray-500">$50 USD</p>
                                    <p class="text-sm font-bold text-gray-900">{{ currency(50) }}</p>
                                </div>
                                <div class="bg-white rounded-lg p-3 text-center border border-gray-200">
                                    <p class="text-xs text-gray-500">$100 USD</p>
                                    <p class="text-sm font-bold text-gray-900">{{ currency(100) }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif

                @if($activeTab === 'theme')
                    <div class="space-y-8">
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900 mb-1">Theme & Custom Code</h3>
                            <p class="text-sm text-gray-500 mb-6">Customize the platform's appearance and inject custom CSS/JS.</p>
                        </div>
                        @php $primary = App\Models\AdminSetting::getValue('theme_primary_color', '#4f46e5'); @endphp
                        <div class="rounded-2xl p-6 text-white mb-6" style="background: {{ $primary }}">
                            <p class="text-sm opacity-80 mb-1">Primary Color Preview</p>
                            <p class="text-2xl font-extrabold">{{ $primary }}</p>
                            <div class="flex items-center space-x-3 mt-4">
                                <div class="px-4 py-2 rounded-xl font-bold text-sm shadow-md" style="background: {{ App\Models\AdminSetting::getValue('theme_primary_hover', '#4338ca') }}">Hover</div>
                                <div class="px-4 py-2 bg-white rounded-xl font-bold text-sm shadow-md" style="color: {{ $primary }}">Light</div>
                            </div>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            @foreach($tabSettings->where('key', '!=', 'custom_css')->where('key', '!=', 'custom_js') as $setting)
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">{{ ucwords(str_replace('_', ' ', $setting->key)) }}</label>
                                    @if(Str::contains($setting->key, 'color'))
                                        <div class="flex items-center space-x-3">
                                            <input type="color" name="settings[{{ $setting->key }}]" value="{{ $setting->value }}" class="w-12 h-10 rounded-lg border-gray-300 cursor-pointer">
                                            <input type="text" value="{{ $setting->value }}" class="flex-1 rounded-xl border-gray-300 bg-gray-50 text-sm" readonly>
                                        </div>
                                    @else
                                        <input type="text" name="settings[{{ $setting->key }}]" value="{{ $setting->value }}" class="w-full rounded-xl border-gray-300">
                                    @endif
                                </div>
                            @endforeach
                        </div>
                        @php $customCss = $tabSettings->firstWhere('key', 'custom_css'); @endphp
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Custom CSS</label>
                            <textarea name="settings[custom_css]" rows="8" class="w-full rounded-xl border-gray-300 font-mono text-sm" placeholder="/* Add custom CSS here */">{{ $customCss->value ?? '' }}</textarea>
                            <p class="mt-1 text-xs text-gray-500">Injected in &lt;head&gt; of every page.</p>
                        </div>
                        @php $customJs = $tabSettings->firstWhere('key', 'custom_js'); @endphp
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Custom JavaScript</label>
                            <textarea name="settings[custom_js]" rows="8" class="w-full rounded-xl border-gray-300 font-mono text-sm" placeholder="// Add custom JS here">{{ $customJs->value ?? '' }}</textarea>
                            <p class="mt-1 text-xs text-gray-500">Injected before &lt;/body&gt; of every page.</p>
                        </div>
                    </div>
                @endif

                @if($activeTab === 'maintenance')
                    <div class="space-y-8">
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900 mb-1">Maintenance Mode</h3>
                            <p class="text-sm text-gray-500 mb-6">Toggle maintenance mode and customize the message shown to users.</p>
                        </div>
                        @php $mm = App\Models\AdminSetting::getValue('maintenance_mode', 'false'); @endphp
                        <div class="p-6 rounded-2xl border-2 {{ $mm === 'true' ? 'bg-red-50 border-red-200' : 'bg-green-50 border-green-200' }}">
                            <p class="text-lg font-bold {{ $mm === 'true' ? 'text-red-800' : 'text-green-800' }}">{{ $mm === 'true' ? '🔴 Maintenance ON' : '🟢 Site is Live' }}</p>
                            <p class="text-sm {{ $mm === 'true' ? 'text-red-600' : 'text-green-600' }} mt-1">{{ $mm === 'true' ? 'Only admins can access the site.' : 'All users can access the platform.' }}</p>
                        </div>
                        @foreach($tabSettings as $setting)
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">{{ ucwords(str_replace('_', ' ', $setting->key)) }}</label>
                                @if($setting->type === 'boolean')
                                    <select name="settings[{{ $setting->key }}]" class="w-full rounded-xl border-gray-300">
                                        <option value="true" @if($setting->value === 'true') selected @endif>Enabled</option>
                                        <option value="false" @if($setting->value === 'false') selected @endif>Disabled</option>
                                    </select>
                                @else
                                    <textarea name="settings[{{ $setting->key }}]" rows="3" class="w-full rounded-xl border-gray-300">{{ $setting->value }}</textarea>
                                @endif
                            </div>
                        @endforeach
                    </div>
                @endif

                @if($activeTab === 'legal')
                    <div class="space-y-8">
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900 mb-1">Legal Pages</h3>
                            <p class="text-sm text-gray-500 mb-6">Edit the Terms of Service and Privacy Policy pages. HTML is supported.</p>
                        </div>
                        @foreach($tabSettings as $setting)
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">{{ ucwords(str_replace('_', ' ', $setting->key)) }}</label>
                                <p class="text-xs text-gray-400 mb-2">{{ $setting->description }}</p>
                                <textarea name="settings[{{ $setting->key }}]" rows="18" class="w-full rounded-xl border-gray-300 font-mono text-sm">{{ $setting->value }}</textarea>
                            </div>
                        @endforeach
                    </div>
                @endif

                {{-- Submit --}}
                <div class="flex items-center justify-between pt-6 mt-8 border-t border-gray-200">
                    <p class="text-xs text-gray-400">Changes are saved immediately</p>
                    <button type="submit" class="px-6 py-3 bg-indigo-600 text-white rounded-xl hover:bg-indigo-700 font-medium text-sm transition-colors shadow-sm">
                        <span class="flex items-center space-x-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"/></svg>
                            <span>Save {{ $tabLabels[$activeTab]['label'] }}</span>
                        </span>
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
function togglePw(id) {
    const input = document.getElementById(id);
    input.type = input.type === 'password' ? 'text' : 'password';
}
</script>
@endpush
@endsection
