@extends('admin.layouts.admin')

@section('title', 'Create Task')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <a href="{{ route('admin.tasks.index') }}" class="text-sm text-indigo-600 hover:text-indigo-500 font-medium mb-4 inline-block">&larr; Back to Tasks</a>

    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-8">
        <h2 class="text-xl font-bold text-gray-900 mb-6">Create New Task</h2>

        <form action="{{ route('admin.tasks.store') }}" method="POST" class="space-y-6">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                {{-- Task Type -- First field, always visible --}}
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Task Type</label>
                    <select name="task_type" id="task_type" required class="w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        @foreach(\App\Models\Task::taskTypes() as $value => $label)
                            <option value="{{ $value }}" @if(old('task_type', 'text') === $value) selected @endif>{{ $label }}</option>
                        @endforeach
                    </select>
                    <p class="text-xs text-gray-400 mt-1">Choose the type of task. Type-specific fields will appear below.</p>
                </div>

                {{-- Rest of the form — hidden until a task type is selected --}}
                <div id="task-details" class="hidden md:col-span-2 space-y-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div id="title-group" class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Title</label>
                            <input type="text" name="title" value="{{ old('title') }}" id="input_title" class="w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        </div>
                        <div id="description-group" class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Description</label>
                            <textarea name="description" id="input_description" rows="4" class="w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">{{ old('description') }}</textarea>
                        </div>

                        <div id="instructions-group" class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Instructions</label>
                            <textarea name="instructions" rows="6" class="w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">{{ old('instructions') }}</textarea>
                        </div>

                        {{-- Type-specific meta fields --}}
                        <div id="type-meta-fields" class="md:col-span-2 space-y-6 bg-gray-50 rounded-xl p-6">
                            <div id="meta-url-group" class="meta-field hidden">
                                <label id="meta-url-label" class="block text-sm font-medium text-gray-700 mb-2">URL / Link</label>
                                <input type="url" name="meta_url" id="meta_url_input" value="{{ old('meta_url') }}" class="w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" placeholder="https://">
                            </div>
                            <div id="meta-platform-group" class="meta-field hidden">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Video Platform</label>
                                <input type="text" name="meta_platform" value="{{ old('meta_platform') }}" class="w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" placeholder="e.g. Vimeo, Dailymotion">
                            </div>
                            <div id="meta-file-group" class="meta-field hidden space-y-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Allowed File Types</label>
                                    <input type="text" name="meta_file_types" value="{{ old('meta_file_types') }}" class="w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" placeholder="e.g. jpg,png,pdf">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Max File Size (KB)</label>
                                    <input type="number" name="meta_max_size" value="{{ old('meta_max_size', 2048) }}" min="1" class="w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                </div>
                            </div>
                            <div id="meta-quiz-group" class="meta-field hidden space-y-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Question</label>
                                    <textarea name="meta_question" rows="3" class="w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" placeholder="Enter the question...">{{ old('meta_question') }}</textarea>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Answer Options (one per line)</label>
                                    <textarea name="meta_options" rows="4" class="w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" placeholder="Option A&#10;Option B&#10;Option C&#10;Option D">{{ old('meta_options') }}</textarea>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Correct Answer</label>
                                    <input type="text" name="meta_answer" value="{{ old('meta_answer') }}" class="w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                </div>
                            </div>
                            <div id="meta-code-group" class="meta-field hidden">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Programming Language</label>
                                <input type="text" name="meta_code_language" value="{{ old('meta_code_language') }}" class="w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" placeholder="e.g. Python, JavaScript, PHP">
                            </div>
                            <div id="meta-social-group" class="meta-field hidden">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Social Platform</label>
                                <input type="text" name="meta_social_platform" value="{{ old('meta_social_platform') }}" class="w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" placeholder="e.g. Facebook, Twitter, Instagram">
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Reward ({{ currency_symbol() }})</label>
                            <input type="number" name="reward" value="{{ old('reward') }}" step="0.01" min="0.01" required class="w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Est. Minutes</label>
                            <input type="number" name="estimated_minutes" value="{{ old('estimated_minutes', 10) }}" min="1" required class="w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Required Level</label>
                            <select name="level_id" class="w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                <option value="">All Levels</option>
                                @foreach($levels as $level)
                                    <option value="{{ $level->id }}" @if(old('level_id') == $level->id) selected @endif>Level {{ $level->level }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Total Slots (0 = Unlimited)</label>
                            <input type="number" name="total_slots" value="{{ old('total_slots', 0) }}" min="0" required class="w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                            <select name="status" required class="w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                <option value="active">Active</option>
                                <option value="inactive">Inactive</option>
                            </select>
                        </div>
                    </div>

                    <div class="flex justify-end space-x-3 pt-4 border-t border-gray-100">
                        <a href="{{ route('admin.tasks.index') }}" class="px-4 py-2 border border-gray-300 rounded-xl text-sm font-medium text-gray-700 hover:bg-gray-50">Cancel</a>
                        <button type="submit" class="px-6 py-2 bg-indigo-600 text-white rounded-xl hover:bg-indigo-700 text-sm font-medium">Create Task</button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const taskType = document.getElementById('task_type');
        const taskDetails = document.getElementById('task-details');
        const metaFields = document.querySelectorAll('.meta-field');

        // Start with everything hidden — only task type dropdown is visible
        taskDetails?.classList.add('hidden');

        function showMetaFields(type) {
            // Show the details section
            taskDetails?.classList.remove('hidden');

            // Hide all type-specific meta fields
            metaFields.forEach(f => f.classList.add('hidden'));

            // Show/hide standard fields
            const titleGroup = document.getElementById('title-group');
            const descGroup = document.getElementById('description-group');
            const instrGroup = document.getElementById('instructions-group');
            const inputTitle = document.getElementById('input_title');
            const inputDesc = document.getElementById('input_description');
            const urlLabel = document.getElementById('meta-url-label');
            const urlInput = document.getElementById('meta_url_input');

            // Default: show all standard fields
            titleGroup?.classList.remove('hidden');
            descGroup?.classList.remove('hidden');
            instrGroup?.classList.remove('hidden');
            inputTitle?.removeAttribute('data-optional');
            inputDesc?.removeAttribute('data-optional');

            // Reset URL label
            if (urlLabel) urlLabel.textContent = 'URL / Link';
            if (urlInput) urlInput.placeholder = 'https://';

            switch (type) {
                case 'url':
                    document.getElementById('meta-url-group')?.classList.remove('hidden');
                    if (urlLabel) urlLabel.textContent = 'URL / Link';
                    if (urlInput) urlInput.placeholder = 'https://example.com/resource';
                    break;
                case 'youtube':
                    // YouTube: only show URL, Reward, Est. Minutes, Level, Slots, Status
                    // Hide Title, Description, Instructions
                    titleGroup?.classList.add('hidden');
                    descGroup?.classList.add('hidden');
                    instrGroup?.classList.add('hidden');

                    document.getElementById('meta-url-group')?.classList.remove('hidden');
                    if (urlLabel) urlLabel.textContent = 'YouTube Video URL';
                    if (urlInput) urlInput.placeholder = 'https://www.youtube.com/watch?v=... or https://youtu.be/...';

                    // Auto-fill title from URL when user types
                    if (urlInput && inputTitle) {
                        urlInput.addEventListener('input', function() {
                            const url = this.value;
                            if (url && inputTitle.value === '') {
                                inputTitle.value = 'Watch YouTube Video';
                            }
                        });
                    }
                    break;
                case 'video':
                    document.getElementById('meta-url-group')?.classList.remove('hidden');
                    document.getElementById('meta-platform-group')?.classList.remove('hidden');
                    if (urlLabel) urlLabel.textContent = 'Video URL';
                    if (urlInput) urlInput.placeholder = 'https://vimeo.com/...';
                    break;
                case 'image':
                case 'file':
                    document.getElementById('meta-file-group')?.classList.remove('hidden');
                    break;
                case 'quiz':
                    document.getElementById('meta-quiz-group')?.classList.remove('hidden');
                    break;
                case 'social_share':
                    document.getElementById('meta-url-group')?.classList.remove('hidden');
                    document.getElementById('meta-social-group')?.classList.remove('hidden');
                    if (urlLabel) urlLabel.textContent = 'Link to Share';
                    break;
                case 'code':
                    document.getElementById('meta-url-group')?.classList.remove('hidden');
                    document.getElementById('meta-code-group')?.classList.remove('hidden');
                    break;
                case 'custom':
                    document.getElementById('meta-url-group')?.classList.remove('hidden');
                    break;
            }
        }

        taskType.addEventListener('change', function() {
            showMetaFields(this.value);
        });

        // If there's old input (validation error), show the form with the selected type
        @if(old('task_type'))
            showMetaFields('{{ old('task_type') }}');
        @else
            // Start clean — user must pick a type first
            taskType.selectedIndex = -1;
        @endif
    });
</script>
@endpush
