@extends('admin.layouts.admin')

@section('title', 'Edit Task')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <a href="{{ route('admin.tasks.index') }}" class="text-sm text-indigo-600 hover:text-indigo-500 font-medium mb-4 inline-block">&larr; Back to Tasks</a>

    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-8">
        <h2 class="text-xl font-bold text-gray-900 mb-6">Edit Task: {{ $task->title }}</h2>

        <form action="{{ route('admin.tasks.update', $task) }}" method="POST" class="space-y-6">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                {{-- Task Type -- First field --}}
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Task Type</label>
                    <select name="task_type" id="task_type" required class="w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        @foreach(\App\Models\Task::taskTypes() as $value => $label)
                            <option value="{{ $value }}" @if(old('task_type', $task->task_type ?? 'text') === $value) selected @endif>{{ $label }}</option>
                        @endforeach
                    </select>
                    <p class="text-xs text-gray-400 mt-1">Choose the type of task. Type-specific fields appear below.</p>
                </div>

                <div class="md:col-span-2">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div id="title-group" class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Title</label>
                            <input type="text" name="title" id="input_title" value="{{ old('title', $task->title) }}" class="w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        </div>
                        <div id="description-group" class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Description</label>
                            <textarea name="description" id="input_description" rows="4" class="w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">{{ old('description', $task->description) }}</textarea>
                        </div>

                        <div id="instructions-group" class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Instructions</label>
                            <textarea name="instructions" rows="6" class="w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">{{ old('instructions', $task->instructions) }}</textarea>
                        </div>

                        {{-- Type-specific meta fields --}}
                        <div id="type-meta-fields" class="md:col-span-2 space-y-6 bg-gray-50 rounded-xl p-6">
                            @php $meta = $task->task_meta ?? []; @endphp
                            <div id="meta-url-group" class="meta-field hidden">
                                <label id="meta-url-label" class="block text-sm font-medium text-gray-700 mb-2">URL / Link</label>
                                <input type="text" name="meta_url" id="meta_url_input" value="{{ old('meta_url', $meta['url'] ?? '') }}" class="w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" placeholder="https://">
                            </div>

                            <div id="meta-platform-group" class="meta-field hidden">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Video Platform</label>
                                <input type="text" name="meta_platform" value="{{ old('meta_platform', $meta['platform'] ?? '') }}" class="w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" placeholder="e.g. Vimeo, Dailymotion">
                            </div>

                            <div id="meta-file-group" class="meta-field hidden space-y-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Allowed File Types</label>
                                    <input type="text" name="meta_file_types" value="{{ old('meta_file_types', $meta['file_types'] ?? '') }}" class="w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" placeholder="e.g. jpg,png,pdf">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Max File Size (KB)</label>
                                    <input type="number" name="meta_max_size" value="{{ old('meta_max_size', $meta['max_size'] ?? 2048) }}" min="1" class="w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                </div>
                            </div>

                            <div id="meta-quiz-group" class="meta-field hidden space-y-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Question</label>
                                    <textarea name="meta_question" rows="3" class="w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">{{ old('meta_question', $meta['question'] ?? '') }}</textarea>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Answer Options (one per line)</label>
                                    <textarea name="meta_options" rows="4" class="w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">{{ old('meta_options', isset($meta['options']) ? implode("\n", $meta['options']) : '') }}</textarea>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Correct Answer</label>
                                    <input type="text" name="meta_answer" value="{{ old('meta_answer', $meta['answer'] ?? '') }}" class="w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                </div>
                            </div>

                            <div id="meta-code-group" class="meta-field hidden">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Programming Language</label>
                                <input type="text" name="meta_code_language" value="{{ old('meta_code_language', $meta['code_language'] ?? '') }}" class="w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" placeholder="e.g. Python, JavaScript, PHP">
                            </div>

                            <div id="meta-social-group" class="meta-field hidden">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Social Platform</label>
                                <input type="text" name="meta_social_platform" value="{{ old('meta_social_platform', $meta['social_platform'] ?? '') }}" class="w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" placeholder="e.g. Facebook, Twitter, Instagram">
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Reward ({{ currency_symbol() }})</label>
                            <input type="number" name="reward" value="{{ old('reward', $task->reward) }}" step="0.01" min="0.01" required class="w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Est. Minutes</label>
                            <input type="number" name="estimated_minutes" value="{{ old('estimated_minutes', $task->estimated_minutes) }}" min="1" required class="w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Required Level</label>
                            <select name="level_id" class="w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                <option value="">All Levels</option>
                                @foreach($levels as $level)
                                    <option value="{{ $level->id }}" @if($task->level_id == $level->id) selected @endif>Level {{ $level->level }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Total Slots</label>
                            <input type="number" name="total_slots" value="{{ old('total_slots', $task->total_slots) }}" min="0" required class="w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Remaining Slots</label>
                            <input type="number" name="remaining_slots" value="{{ old('remaining_slots', $task->remaining_slots) }}" min="0" required class="w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Category</label>
                            <select name="category" class="w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                <option value="general" @if($task->category === 'general') selected @endif>General</option>
                                <option value="daily" @if($task->category === 'daily') selected @endif>🔥 Daily</option>
                                <option value="premium" @if($task->category === 'premium') selected @endif>💎 Premium</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Featured</label>
                            <select name="is_featured" class="w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                <option value="0" @if(!$task->is_featured) selected @endif>No</option>
                                <option value="1" @if($task->is_featured) selected @endif>⭐ Yes</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                            <select name="status" required class="w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                <option value="active" @if($task->status === 'active') selected @endif>Active</option>
                                <option value="inactive" @if($task->status === 'inactive') selected @endif>Inactive</option>
                            </select>
                        </div>
                    </div>

                    <div class="flex justify-end space-x-3 pt-4 border-t border-gray-100 mt-6">
                        <a href="{{ route('admin.tasks.index') }}" class="px-4 py-2 border border-gray-300 rounded-xl text-sm font-medium text-gray-700 hover:bg-gray-50">Cancel</a>
                        <button type="submit" class="px-6 py-2 bg-indigo-600 text-white rounded-xl hover:bg-indigo-700 text-sm font-medium">Update Task</button>
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
        const metaFields = document.querySelectorAll('.meta-field');
        const titleGroup = document.getElementById('title-group');
        const descGroup = document.getElementById('description-group');
        const instrGroup = document.getElementById('instructions-group');
        const urlLabel = document.getElementById('meta-url-label');
        const urlInput = document.getElementById('meta_url_input');

        function showMetaFields(type) {
            metaFields.forEach(f => f.classList.add('hidden'));

            // Default: show all standard fields
            titleGroup?.classList.remove('hidden');
            descGroup?.classList.remove('hidden');
            instrGroup?.classList.remove('hidden');

            if (urlLabel) urlLabel.textContent = 'URL / Link';
            if (urlInput) urlInput.placeholder = 'https://';

            switch (type) {
                case 'url':
                    document.getElementById('meta-url-group')?.classList.remove('hidden');
                    if (urlLabel) urlLabel.textContent = 'URL / Link';
                    if (urlInput) urlInput.placeholder = 'https://example.com/resource';
                    break;
                case 'youtube':
                    titleGroup?.classList.add('hidden');
                    descGroup?.classList.add('hidden');
                    instrGroup?.classList.add('hidden');
                    document.getElementById('meta-url-group')?.classList.remove('hidden');
                    if (urlLabel) urlLabel.textContent = 'YouTube Video URL';
                    if (urlInput) urlInput.placeholder = 'https://www.youtube.com/watch?v=... or https://youtu.be/...';
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
        showMetaFields(taskType.value);
    });
</script>
@endpush
