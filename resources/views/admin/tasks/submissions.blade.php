@extends('admin.layouts.admin')

@section('title', 'Submissions')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100">
        <div class="px-6 py-4 border-b border-gray-100">
            <h2 class="text-lg font-semibold text-gray-900">Task Submissions</h2>
        </div>
        <div class="divide-y divide-gray-100">
            @forelse($submissions as $submission)
                <div class="px-6 py-4">
                    <div class="flex items-start justify-between">
                        <div class="flex-1">
                            <div class="flex items-center space-x-2 mb-2">
                                <span class="px-2 py-0.5 rounded-full text-xs font-medium
                                    @if($submission->status === 'approved') bg-green-100 text-green-700
                                    @elseif($submission->status === 'rejected') bg-red-100 text-red-700
                                    @else bg-amber-100 text-amber-700 @endif">
                                    {{ ucfirst($submission->status) }}
                                </span>
                                <span class="text-sm text-gray-500">{{ $submission->created_at->format('M d, Y g:i A') }}</span>
                            </div>
                            <p class="text-sm font-medium text-gray-900">{{ $submission->user?->name }}</p>
                            <p class="text-sm text-gray-600">Task: <span class="font-medium">{{ $submission->task?->title }}</span></p>
                            @if($submission->notes)
                                <p class="text-sm text-gray-500 mt-2 bg-gray-50 rounded-lg p-3">{{ $submission->notes }}</p>
                            @endif
                            <p class="text-sm font-semibold text-green-600 mt-2">Reward: {{ currency($submission->task?->reward ?? 0) }}</p>
                        </div>

                        @if($submission->status === 'pending')
                            <div class="flex items-center space-x-2 ms-4">
                                <form action="{{ route('admin.submissions.approve', $submission) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 text-sm font-medium">Approve</button>
                                </form>
                                <button onclick="showRejectModal('{{ $submission->id }}')" class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 text-sm font-medium">Reject</button>
                            </div>
                        @endif
                    </div>
                </div>
            @empty
                <div class="px-6 py-12 text-center text-gray-500">
                    <p>No submissions found</p>
                </div>
            @endforelse
        </div>
        @if($submissions->hasPages())
            <div class="px-6 py-3 border-t border-gray-100">
                {{ $submissions->links() }}
            </div>
        @endif
    </div>
</div>

<!-- Reject Modal -->
<div id="rejectModal" class="fixed inset-0 z-50 hidden overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"></div>
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>
        <div class="inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
            <form id="rejectForm" method="POST" class="p-6">
                @csrf
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Reject Submission</h3>
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Reason for Rejection</label>
                    <textarea name="admin_notes" rows="4" required class="w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" placeholder="Explain why the submission was rejected..."></textarea>
                </div>
                <div class="flex justify-end space-x-3">
                    <button type="button" onclick="closeRejectModal()" class="px-4 py-2 border border-gray-300 rounded-xl text-sm font-medium text-gray-700 hover:bg-gray-50">Cancel</button>
                    <button type="submit" class="px-6 py-2 bg-red-600 text-white rounded-xl hover:bg-red-700 text-sm font-medium">Reject</button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
function showRejectModal(id) {
    document.getElementById('rejectForm').action = '{{ url("admin/submissions") }}/' + id + '/reject';
    document.getElementById('rejectModal').classList.remove('hidden');
}
function closeRejectModal() {
    document.getElementById('rejectModal').classList.add('hidden');
}
</script>
@endpush
@endsection
