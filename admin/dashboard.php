<?php
require_once '../includes/middleware.php';
require_once '../includes/components/layout_wrapper.php';
requireAuth();

layout_start('Dashboard - Deckoid ERP');
?>

<div class="grid grid-cols-1 gap-4 lg:gap-6">
    <!-- Main Content -->
    <div class="space-y-4 lg:space-y-6">
        
        <!-- Welcome Hero Section -->
        <div class="relative overflow-hidden">
            <div class="relative z-10">
                <h1 class="text-2xl lg:text-3xl font-black tracking-tight mb-2">Hello, <?php echo explode(' ', $_SESSION['full_name'] ?? 'Admin')[0]; ?>!</h1>
            </div>
        </div>

        <!-- Statistics Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 xl:grid-cols-6 gap-4 lg:gap-5">
            <!-- Stat Card -->
            <div class="bg-white p-4 lg:p-5 rounded-xl shadow-sm border border-neutral-100 hover:shadow-lg hover:shadow-neutral-200/50 transition-all duration-300 group overflow-hidden relative">
                <div class="flex items-center justify-between mb-3">
                    <div class="w-10 h-10 bg-primary-50 rounded-xl flex items-center justify-center text-primary-600 group-hover:bg-primary-600 group-hover:text-white transition-all duration-500">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                    </div>
                </div>
                <div>
                    <p class="text-neutral-400 font-bold text-[10px] uppercase tracking-widest">Total Leads</p>
                    <h3 class="text-2xl font-black text-neutral-900 mt-0.5" id="statTotalLeads">0</h3>
                </div>
                <div class="absolute bottom-0 right-0 w-24 h-24 bg-primary-50/50 rounded-tl-full -mr-8 -mb-8 transition-all group-hover:scale-150"></div>
            </div>

            <!-- Stat Card -->
            <div class="bg-white p-4 lg:p-5 rounded-xl shadow-sm border border-neutral-100 hover:shadow-lg hover:shadow-neutral-200/50 transition-all duration-300 group">
                <div class="flex items-center justify-between mb-3">
                    <div class="w-10 h-10 bg-blue-50 rounded-xl flex items-center justify-center text-blue-600 group-hover:bg-blue-600 group-hover:text-white transition-all duration-500">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v3m0 0v3m0-3h3m-3 0H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                </div>
                <div>
                    <p class="text-neutral-400 font-bold text-[10px] uppercase tracking-widest">New Leads</p>
                    <h3 class="text-2xl font-black text-neutral-900 mt-0.5" id="statNewLeads">0</h3>
                </div>
            </div>

            <!-- Stat Card -->
            <div class="bg-white p-6 rounded-xl shadow-sm border border-neutral-100 hover:shadow-xl hover:shadow-neutral-200/50 transition-all duration-300 group">
                <div class="flex items-center justify-between mb-4">
                    <div class="w-12 h-12 bg-orange-50 rounded-xl flex items-center justify-center text-orange-600 group-hover:bg-orange-600 group-hover:text-white transition-all duration-500">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                    </div>
                </div>
                <div>
                    <p class="text-neutral-400 font-bold text-xs uppercase tracking-widest">Followups</p>
                    <h3 class="text-3xl font-black text-neutral-900 mt-1" id="statFollowupLeads">0</h3>
                </div>
            </div>

            <!-- Stat Card -->
            <div class="bg-white p-6 rounded-xl shadow-sm border border-neutral-100 hover:shadow-xl hover:shadow-neutral-200/50 transition-all duration-300 group">
                <div class="flex items-center justify-between mb-4">
                    <div class="w-12 h-12 bg-green-50 rounded-xl flex items-center justify-center text-green-600 group-hover:bg-green-600 group-hover:text-white transition-all duration-500">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                </div>
                <div>
                    <p class="text-neutral-400 font-bold text-xs uppercase tracking-widest">Converted</p>
                    <h3 class="text-3xl font-black text-neutral-900 mt-1" id="statConvertedLeads">0</h3>
                </div>
            </div>

            <!-- Stat Card -->
            <div class="bg-white p-6 rounded-xl shadow-sm border border-neutral-100 hover:shadow-xl hover:shadow-neutral-200/50 transition-all duration-300 group">
                <div class="flex items-center justify-between mb-4">
                    <div class="w-12 h-12 bg-red-50 rounded-xl flex items-center justify-center text-red-600 group-hover:bg-red-600 group-hover:text-white transition-all duration-500">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                </div>
                <div>
                    <p class="text-neutral-400 font-bold text-xs uppercase tracking-widest">Lost Leads</p>
                    <h3 class="text-3xl font-black text-neutral-900 mt-1" id="statLostLeads">0</h3>
                </div>
            </div>

            <!-- Stat Card -->
            <div class="bg-white p-6 rounded-xl shadow-sm border border-neutral-100 hover:shadow-xl hover:shadow-neutral-200/50 transition-all duration-300 group">
                <div class="flex items-center justify-between mb-4">
                    <div class="w-12 h-12 bg-purple-50 rounded-xl flex items-center justify-center text-purple-600 group-hover:bg-purple-600 group-hover:text-white transition-all duration-500">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                    </div>
                </div>
                <div>
                    <p class="text-neutral-400 font-bold text-xs uppercase tracking-widest">Pending Payments</p>
                    <h3 class="text-3xl font-black text-neutral-900 mt-1" id="statPendingPayments">0</h3>
                </div>
            </div>
        </div>

        <!-- Charts Section -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 lg:gap-6" id="analytics">
            <!-- Monthly Growth Chart -->
            <div class="bg-white p-5 lg:p-6 rounded-xl shadow-sm border border-neutral-100">
                <div class="flex items-center justify-between mb-6">
                    <div>
                        <h3 class="text-lg font-bold text-neutral-900">Lead Growth</h3>
                        <p class="text-neutral-400 text-xs font-medium">Monthly lead registration trend</p>
                    </div>
                    <select class="bg-neutral-50 border-none text-[10px] font-bold rounded-lg px-3 py-1.5 outline-none">
                        <option>Last 6 Months</option>
                        <option>Last Year</option>
                    </select>
                </div>
                <div class="h-[250px]">
                    <canvas id="monthlyChart"></canvas>
                </div>
            </div>

            <!-- Source Distribution -->
            <div class="bg-white p-5 lg:p-6 rounded-xl shadow-sm border border-neutral-100">
                <div class="flex items-center justify-between mb-6">
                    <div>
                        <h3 class="text-lg font-bold text-neutral-900">Lead Sources</h3>
                        <p class="text-neutral-400 text-xs font-medium">Where your leads come from</p>
                    </div>
                </div>
                <div class="h-[250px] flex items-center justify-center">
                    <canvas id="sourceChart"></canvas>
                </div>
            </div>
        </div>

        <!-- Recent Leads Table -->
        <div class="bg-white rounded-xl shadow-sm border border-neutral-100 overflow-hidden">
            <div class="px-5 py-4 lg:px-6 lg:py-4 border-b border-neutral-50 flex items-center justify-between bg-neutral-50/30">
                <div>
                    <h3 class="text-lg font-bold text-neutral-900">Latest Registered Leads</h3>
                    <p class="text-neutral-400 text-xs font-medium">Manage your most recent opportunities</p>
                </div>
                <a href="leads.php" class="px-4 py-2 bg-white border border-neutral-200 text-neutral-600 font-bold rounded-lg hover:bg-primary-600 hover:text-white hover:border-primary-600 transition-all text-xs shadow-sm">
                    View All Leads
                </a>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="bg-neutral-50/50">
                            <th class="px-5 py-3 lg:px-6 lg:py-3 text-left text-[10px] font-black text-neutral-400 uppercase tracking-widest">Client Name</th>
                            <th class="px-5 py-3 lg:px-6 lg:py-3 text-left text-[10px] font-black text-neutral-400 uppercase tracking-widest">Category</th>
                            <th class="px-5 py-3 lg:px-6 lg:py-3 text-left text-[10px] font-black text-neutral-400 uppercase tracking-widest">Status</th>
                            <th class="px-5 py-3 lg:px-6 lg:py-3 text-right text-[10px] font-black text-neutral-400 uppercase tracking-widest">Action</th>
                        </tr>
                    </thead>
                    <tbody id="recentLeadsTable" class="divide-y divide-neutral-50">
                        <!-- Loading State -->
                        <tr>
                            <td colspan="4" class="px-8 py-12 text-center">
                                <div class="flex flex-col items-center gap-2">
                                    <div class="w-6 h-6 border-2 border-primary-600 border-t-transparent rounded-full animate-spin"></div>
                                    <span class="text-xs font-bold text-neutral-400 uppercase tracking-widest">Loading Data...</span>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

    </div>
</div>
</div>

<script>
    let monthlyChart, sourceChart;

    async function loadDashboard() {
        try {
            const response = await fetch('../api/dashboard.php');
            const res = await response.json();

            if (!res.success) throw new Error(res.message);
            
            const data = res.data;

            // Update Stats
            document.getElementById('statTotalLeads').textContent = data.stats.total || 0;
            document.getElementById('statNewLeads').textContent = data.stats.new || 0;
            document.getElementById('statFollowupLeads').textContent = data.stats.followup || 0;
            document.getElementById('statConvertedLeads').textContent = data.stats.converted || 0;
            document.getElementById('statLostLeads').textContent = data.stats.lost || 0;
            document.getElementById('statPendingPayments').textContent = data.stats.pending_payments || 0;
            
            const followupsBadge = document.getElementById('statTodayFollowups');
            if (followupsBadge) {
                followupsBadge.textContent = data.stats.today_followups || 0;
            }


            // Render Monthly Chart
            renderMonthlyChart(data.monthly_stats);

            // Render Source Chart
            renderSourceChart(data.source_stats);

            // Update Recent Leads
            const tbody = document.getElementById('recentLeadsTable');
            if (data.recent_leads && data.recent_leads.length > 0) {
                tbody.innerHTML = data.recent_leads.map(lead => `
                    <tr class="hover:bg-neutral-50/50 transition-colors group">
                        <td class="px-5 py-3 lg:px-6 lg:py-3">
                            <div class="flex items-center gap-2.5">
                                <div class="w-8 h-8 bg-neutral-100 rounded-lg flex-shrink-0 flex items-center justify-center text-neutral-600 font-bold text-[10px] group-hover:bg-primary-600 group-hover:text-white transition-all">
                                    ${lead.company_client_name.charAt(0)}
                                </div>
                                <div class="flex flex-col">
                                    <span class="font-bold text-neutral-900 text-[13px] group-hover:text-primary-600 transition-colors">${lead.company_client_name}</span>
                                    <span class="text-[9px] text-neutral-400 font-bold uppercase tracking-tight">${lead.contact_person}</span>
                                </div>
                            </div>
                        </td>
                        <td class="px-5 py-3 lg:px-6 lg:py-3">
                            ${renderCategoryBadge(lead.lead_category)}
                        </td>
                        <td class="px-5 py-3 lg:px-6 lg:py-3">
                            <div class="flex items-center gap-2">
                                <div class="w-1.5 h-1.5 rounded-full ${lead.lead_status === 'Converted' ? 'bg-green-500' : 'bg-primary-500'}"></div>
                                <span class="text-[11px] font-bold text-neutral-700">${lead.lead_status}</span>
                            </div>
                        </td>
                        <td class="px-5 py-3 lg:px-6 lg:py-3 text-right">
                            <a href="leads.php" class="p-2 bg-neutral-50 text-neutral-400 hover:text-primary-600 hover:bg-primary-50 rounded-lg transition-all inline-block shadow-sm">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
                            </a>
                        </td>
                    </tr>
                `).join('');
            } else {
                tbody.innerHTML = `<tr><td colspan="4" class="px-8 py-16 text-center text-neutral-400 font-bold text-sm">No recent leads found.</td></tr>`;
            }


        } catch (error) {
            console.error('Error:', error);
        }
    }

    function renderCategoryBadge(category) {
        if (!category) return '<span class="text-neutral-400 font-bold text-[10px]">-</span>';
        
        let styles = '';
        let label = category;
        
        switch(category.toLowerCase()) {
            case 'hot':
                styles = 'background-color: #FF9D3D; color: #7A3E00;';
                label = '🔥 Hot';
                break;
            case 'warm':
                styles = 'background-color: #FEEE91; color: #7A6500;';
                label = '☀️ Warm';
                break;
            case 'cold':
                styles = 'background-color: #B0DEFF; color: #004A7A;';
                label = '❄️ Cold';
                break;
            default:
                styles = 'background-color: #f3f4f6; color: #4b5563;';
        }
        
        return `<span class="px-2.5 py-1 text-[10px] font-black rounded-lg uppercase tracking-wider shadow-sm hover:brightness-95 transition-all cursor-default whitespace-nowrap inline-block" style="${styles}">${label}</span>`;
    }

    function renderMonthlyChart(stats) {
        const ctx = document.getElementById('monthlyChart').getContext('2d');
        if (monthlyChart) monthlyChart.destroy();

        const gradient = ctx.createLinearGradient(0, 0, 0, 300);
        gradient.addColorStop(0, 'rgba(109, 93, 252, 0.2)');
        gradient.addColorStop(1, 'rgba(109, 93, 252, 0)');

        monthlyChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: stats.map(s => s.month),
                datasets: [{
                    label: 'New Leads',
                    data: stats.map(s => s.count),
                    borderColor: '#6D5DFC',
                    borderWidth: 4,
                    pointBackgroundColor: '#fff',
                    pointBorderColor: '#6D5DFC',
                    pointBorderWidth: 2,
                    pointRadius: 6,
                    pointHoverRadius: 8,
                    backgroundColor: gradient,
                    fill: true,
                    tension: 0.4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: { color: '#f5f7fb' },
                        ticks: { font: { weight: 'bold', size: 10 }, color: '#9ca3af' }
                    },
                    x: {
                        grid: { display: false },
                        ticks: { font: { weight: 'bold', size: 10 }, color: '#9ca3af' }
                    }
                }
            }
        });
    }

    function renderSourceChart(stats) {
        const ctx = document.getElementById('sourceChart').getContext('2d');
        if (sourceChart) sourceChart.destroy();

        sourceChart = new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: stats.map(s => s.source || 'Unknown'),
                datasets: [{
                    data: stats.map(s => s.count),
                    backgroundColor: ['#6D5DFC', '#8b5cf6', '#a78bfa', '#c4b5fd', '#ddd6fe'],
                    borderWidth: 0,
                    hoverOffset: 20
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '75%',
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            padding: 20,
                            usePointStyle: true,
                            font: { weight: 'bold', size: 11, family: 'Inter' },
                            color: '#4b5563'
                        }
                    }
                }
            }
        });
    }

    function formatTimeAgo(dateString) {
        const date = new Date(dateString);
        const now = new Date();
        const diffInSeconds = Math.floor((now - date) / 1000);
        
        if (diffInSeconds < 60) return 'Just now';
        if (diffInSeconds < 3600) return Math.floor(diffInSeconds / 60) + 'm ago';
        if (diffInSeconds < 86400) return Math.floor(diffInSeconds / 3600) + 'h ago';
        return date.toLocaleDateString();
    }

    function scrollToAnalytics() {
        document.getElementById('analytics').scrollIntoView({ behavior: 'smooth' });
    }

    document.addEventListener('DOMContentLoaded', loadDashboard);
</script>

<style>
    body {
        background-color: #F5F7FB;
    }
</style>

<?php layout_end(); ?>