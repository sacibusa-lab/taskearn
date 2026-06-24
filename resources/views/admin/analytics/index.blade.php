@extends('admin.layouts.admin')

@section('title', 'Analytics')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    {{-- Summary Cards --}}
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-8">
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
            <p class="text-xs text-gray-500 uppercase tracking-wider">Total Users</p>
            <p class="text-2xl font-extrabold text-gray-900 mt-1">{{ number_format($summary['total_users']) }}</p>
            <p class="text-xs text-green-600 mt-1">{{ number_format($summary['active_users']) }} active</p>
        </div>
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
            <p class="text-xs text-gray-500 uppercase tracking-wider">Deposits</p>
            <p class="text-2xl font-extrabold text-gray-900 mt-1">{{ currency($summary['total_deposits']) }}</p>
        </div>
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
            <p class="text-xs text-gray-500 uppercase tracking-wider">Completion Rate</p>
            <p class="text-2xl font-extrabold text-green-600 mt-1">{{ $summary['completion_rate'] }}%</p>
        </div>
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
            <p class="text-xs text-gray-500 uppercase tracking-wider">Pending Payouts</p>
            <p class="text-2xl font-extrabold text-amber-600 mt-1">{{ currency($summary['pending_withdrawals']) }}</p>
        </div>
    </div>

    {{-- Time Range --}}
    <div class="flex items-center justify-between mb-6">
        <h2 class="text-lg font-bold text-gray-900">📊 Performance Charts</h2>
        <div class="flex items-center space-x-2 text-sm">
            <a href="?range=7" class="px-3 py-1.5 rounded-lg {{ $days == 7 ? 'bg-indigo-600 text-white' : 'bg-gray-100 text-gray-600' }}">7d</a>
            <a href="?range=30" class="px-3 py-1.5 rounded-lg {{ $days == 30 ? 'bg-indigo-600 text-white' : 'bg-gray-100 text-gray-600' }}">30d</a>
            <a href="?range=90" class="px-3 py-1.5 rounded-lg {{ $days == 90 ? 'bg-indigo-600 text-white' : 'bg-gray-100 text-gray-600' }}">90d</a>
        </div>
    </div>

    {{-- Charts Grid --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
        {{-- Earnings Chart --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
            <h3 class="text-sm font-bold text-gray-900 mb-4">💰 Earnings Over Time</h3>
            <div class="relative h-72">
                <canvas id="earningsChart"></canvas>
            </div>
        </div>

        {{-- User Growth Chart --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
            <h3 class="text-sm font-bold text-gray-900 mb-4">👥 User Growth</h3>
            <div class="relative h-72">
                <canvas id="userGrowthChart"></canvas>
            </div>
        </div>

        {{-- Task Completion Chart --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 lg:col-span-2">
            <h3 class="text-sm font-bold text-gray-900 mb-4">✅ Task Completion Rate</h3>
            <div class="relative h-72">
                <canvas id="taskChart"></canvas>
            </div>
        </div>
    </div>

    {{-- Export Reports --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
        <h2 class="text-lg font-bold text-gray-900 mb-4">📥 Export Reports</h2>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <a href="{{ route('admin.analytics.export-users') }}" class="flex items-center justify-between p-4 bg-indigo-50 rounded-xl hover:bg-indigo-100 transition-colors">
                <div>
                    <p class="text-sm font-bold text-indigo-700">Users Report</p>
                    <p class="text-xs text-indigo-500">CSV · All user data</p>
                </div>
                <svg class="w-5 h-5 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
            </a>
            <a href="{{ route('admin.analytics.export-transactions') }}" class="flex items-center justify-between p-4 bg-green-50 rounded-xl hover:bg-green-100 transition-colors">
                <div>
                    <p class="text-sm font-bold text-green-700">Transactions Report</p>
                    <p class="text-xs text-green-500">CSV · All transactions</p>
                </div>
                <svg class="w-5 h-5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
            </a>
            <a href="{{ route('admin.analytics.export-withdrawals') }}" class="flex items-center justify-between p-4 bg-amber-50 rounded-xl hover:bg-amber-100 transition-colors">
                <div>
                    <p class="text-sm font-bold text-amber-700">Withdrawals Report</p>
                    <p class="text-xs text-amber-500">CSV · All payouts</p>
                </div>
                <svg class="w-5 h-5 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
            </a>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    var labels = {!! json_encode($chartLabels) !!};

    // Earnings Chart
    new Chart(document.getElementById('earningsChart'), {
        type: 'line',
        data: {
            labels: labels,
            datasets: [{
                label: 'Earnings (₦)',
                data: {!! json_encode($earningsData) !!},
                borderColor: '#4f46e5',
                backgroundColor: 'rgba(79,70,229,0.1)',
                fill: true,
                tension: 0.4,
                pointRadius: 2,
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            scales: { y: { beginAtZero: true, ticks: { callback: v => '₦' + v.toLocaleString() } } }
        }
    });

    // User Growth Chart
    new Chart(document.getElementById('userGrowthChart'), {
        type: 'bar',
        data: {
            labels: labels,
            datasets: [{
                label: 'New Users',
                data: {!! json_encode($userGrowthData) !!},
                backgroundColor: '#818cf8',
                borderRadius: 4,
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            scales: { y: { beginAtZero: true, ticks: { stepSize: 1 } } }
        }
    });

    // Task Completion Chart
    new Chart(document.getElementById('taskChart'), {
        type: 'bar',
        data: {
            labels: labels,
            datasets: [
                { label: 'Approved', data: {!! json_encode($taskApprovedData) !!}, backgroundColor: '#22c55e', borderRadius: 4 },
                { label: 'Rejected', data: {!! json_encode($taskRejectedData) !!}, backgroundColor: '#ef4444', borderRadius: 4 },
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: { x: { stacked: true }, y: { stacked: true, beginAtZero: true } }
        }
    });
});
</script>
@endpush
