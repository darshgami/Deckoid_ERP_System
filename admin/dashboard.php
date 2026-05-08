<?php
require_once '../includes/middleware.php';
require_once '../includes/components/layout_wrapper.php';
requireAuth();

layout_start('Dashboard - Deckoid ERP');
?>

<!-- Welcome Hero Section -->
<div class="mb-10 relative overflow-hidden bg-primary-600 rounded-[2.5rem] p-10 text-white shadow-2xl shadow-primary-200">
    <div class="relative z-10">
        <h1 class="text-4xl font-bold tracking-tight">Welcome back, <?php echo explode(' ', $_SESSION['full_name'] ?? 'Admin')[0]; ?>!</h1>
        <p class="text-primary-100 mt-2 text-lg max-w-md font-medium opacity-90">Manage your leads, track conversions, and grow your business with Deckoid ERP.</p>
        <div class="mt-8 flex gap-4">
            <a href="add_lead.php" class="px-6 py-3 bg-white text-primary-600 font-bold rounded-2xl hover:bg-primary-50 transition-all shadow-lg flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M12 4v16m8-8H4"></path></svg>
                Add New Lead
            </a>
            <button class="px-6 py-3 bg-primary-500 text-white font-bold rounded-2xl hover:bg-primary-400 transition-all border border-primary-400/30 flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M9 17v-2a4 4 0 014-4h4m0 0l-4-4m4 4l-4 4"></path></svg>
                View Analytics
            </button>
        </div>
    </div>
    <!-- Abstract Shapes -->
    <div class="absolute top-[-20%] right-[-10%] w-96 h-96 bg-primary-500 rounded-full blur-3xl opacity-50"></div>
    <div class="absolute bottom-[-20%] right-[10%] w-64 h-64 bg-primary-700 rounded-full blur-3xl opacity-30"></div>
</div>

<!-- Statistics Cards -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8 mb-12" id="statsContainer">
    <!-- Stat Item -->
    <div class="bg-white p-8 rounded-[2rem] shadow-sm border border-neutral-100 hover:shadow-xl hover:shadow-neutral-200/50 transition-all duration-300 group">
        <div class="flex items-start justify-between mb-4">
            <div class="w-14 h-14 bg-primary-50 rounded-2xl flex items-center justify-center text-primary-600 group-hover:bg-primary-600 group-hover:text-white transition-all duration-500">
                <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
            </div>
            <span class="text-green-600 bg-green-50 px-3 py-1 rounded-full text-xs font-bold">+12%</span>
        </div>
        <p class="text-neutral-500 font-bold text-sm uppercase tracking-widest">Total Leads</p>
        <h3 class="text-4xl font-black text-neutral-900 mt-1" id="totalLeads">...</h3>
    </div>

    <!-- Stat Item -->
    <div class="bg-white p-8 rounded-[2rem] shadow-sm border border-neutral-100 hover:shadow-xl hover:shadow-neutral-200/50 transition-all duration-300 group">
        <div class="flex items-start justify-between mb-4">
            <div class="w-14 h-14 bg-green-50 rounded-2xl flex items-center justify-center text-green-600 group-hover:bg-green-600 group-hover:text-white transition-all duration-500">
                <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            </div>
            <span class="text-green-600 bg-green-50 px-3 py-1 rounded-full text-xs font-bold">+5%</span>
        </div>
        <p class="text-neutral-500 font-bold text-sm uppercase tracking-widest">Won Deals</p>
        <h3 class="text-4xl font-black text-neutral-900 mt-1" id="wonLeads">...</h3>
    </div>

    <!-- Stat Item (Purple Highlight) -->
    <div class="bg-primary-600 p-8 rounded-[2rem] shadow-xl shadow-primary-200 text-white hover:scale-[1.02] transition-all duration-300 relative overflow-hidden group">
        <p class="text-primary-200 font-bold text-sm uppercase tracking-widest relative z-10">New Leads</p>
        <h3 class="text-5xl font-black mt-2 relative z-10" id="newLeads">...</h3>
        <p class="text-primary-100 text-sm mt-4 font-medium relative z-10">Updated 5m ago</p>
        <div class="absolute bottom-0 right-0 p-4 opacity-20 transform translate-y-4 translate-x-4 group-hover:translate-y-0 group-hover:translate-x-0 transition-transform duration-500">
            <svg class="w-32 h-32" fill="currentColor" viewBox="0 0 20 20"><path d="M2 11a1 1 0 011-1h2a1 1 0 011 1v5a1 1 0 01-1 1H3a1 1 0 01-1-1v-5zM8 7a1 1 0 011-1h2a1 1 0 011 1v9a1 1 0 01-1 1H9a1 1 0 01-1-1V7zM14 4a1 1 0 011-1h2a1 1 0 011 1v12a1 1 0 01-1 1h-2a1 1 0 01-1-1V4z"></path></svg>
        </div>
    </div>

    <!-- Stat Item -->
    <div class="bg-white p-8 rounded-[2rem] shadow-sm border border-neutral-100 hover:shadow-xl hover:shadow-neutral-200/50 transition-all duration-300 group">
        <div class="flex items-start justify-between mb-4">
            <div class="w-14 h-14 bg-red-50 rounded-2xl flex items-center justify-center text-red-600 group-hover:bg-red-600 group-hover:text-white transition-all duration-500">
                <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            </div>
            <span class="text-red-600 bg-red-50 px-3 py-1 rounded-full text-xs font-bold">-2%</span>
        </div>
        <p class="text-neutral-500 font-bold text-sm uppercase tracking-widest">Lost Leads</p>
        <h3 class="text-4xl font-black text-neutral-900 mt-1" id="lostLeads">...</h3>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-10">
    <!-- Main Content Left (Recent Leads) -->
    <div class="lg:col-span-2 space-y-10">
        <!-- Recent Leads Table -->
        <div class="bg-white rounded-[2.5rem] shadow-sm border border-neutral-100 overflow-hidden">
            <div class="p-10 border-b border-neutral-50 flex items-center justify-between">
                <div>
                    <h3 class="text-2xl font-bold text-neutral-900 tracking-tight">Recent Leads</h3>
                    <p class="text-neutral-400 font-medium text-sm mt-1">Latest potential customers registered.</p>
                </div>
                <a href="leads.php" class="p-3 bg-neutral-50 text-neutral-500 hover:text-primary-600 rounded-2xl transition-all">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M9 5l7 7-7 7"></path></svg>
                </a>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="bg-neutral-50/50">
                            <th class="px-10 py-5 text-left text-xs font-bold text-neutral-400 uppercase tracking-widest">Client / Company</th>
                            <th class="px-10 py-5 text-left text-xs font-bold text-neutral-400 uppercase tracking-widest">Category</th>
                            <th class="px-10 py-5 text-left text-xs font-bold text-neutral-400 uppercase tracking-widest">Status</th>
                            <th class="px-10 py-5 text-right text-xs font-bold text-neutral-400 uppercase tracking-widest">Action</th>
                        </tr>
                    </thead>
                    <tbody id="recentLeadsBody" class="divide-y divide-neutral-50">
                        <!-- Loading State -->
                        <tr>
                            <td colspan="4" class="px-10 py-10 text-center">
                                <div class="flex flex-col items-center gap-3 text-neutral-400">
                                    <div class="w-8 h-8 border-4 border-primary-200 border-t-primary-600 rounded-full animate-spin"></div>
                                    <span class="font-bold text-sm uppercase tracking-widest">Fetching Data...</span>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Right Sidebar Content -->
    <div class="space-y-10">
        <!-- Upcoming Followups -->
        <div class="bg-white rounded-[2.5rem] shadow-sm border border-neutral-100 p-10">
            <h3 class="text-xl font-bold text-neutral-900 tracking-tight mb-8">Upcoming Followups</h3>
            <div class="space-y-6" id="followupsList">
                <!-- Mock Followups -->
                <div class="flex items-center gap-4 p-4 bg-neutral-50 rounded-3xl group cursor-pointer hover:bg-white hover:shadow-xl hover:shadow-neutral-200/50 transition-all">
                    <div class="w-12 h-12 bg-orange-100 rounded-2xl flex-shrink-0 flex items-center justify-center text-orange-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                    <div class="flex-1">
                        <p class="font-bold text-neutral-900 text-sm">Design Proposal</p>
                        <p class="text-xs text-neutral-500">Tech Solutions • 2:00 PM</p>
                    </div>
                </div>
                <div class="flex items-center gap-4 p-4 bg-neutral-50 rounded-3xl group cursor-pointer hover:bg-white hover:shadow-xl hover:shadow-neutral-200/50 transition-all">
                    <div class="w-12 h-12 bg-primary-100 rounded-2xl flex-shrink-0 flex items-center justify-center text-primary-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path></svg>
                    </div>
                    <div class="flex-1">
                        <p class="font-bold text-neutral-900 text-sm">Call back John</p>
                        <p class="text-xs text-neutral-500">Marketing Lead • 4:30 PM</p>
                    </div>
                </div>
                <button class="w-full py-4 text-primary-600 font-bold text-sm bg-primary-50 rounded-3xl hover:bg-primary-100 transition-all">View All Schedule</button>
            </div>
        </div>

        <!-- Recent Activity Logs -->
        <div class="bg-white rounded-[2.5rem] shadow-sm border border-neutral-100 p-10">
            <h3 class="text-xl font-bold text-neutral-900 tracking-tight mb-8">Recent Activity</h3>
            <div class="space-y-8 relative before:absolute before:left-[23px] before:top-2 before:bottom-2 before:w-[2px] before:bg-neutral-100" id="activityLogs">
                <!-- Activity Item -->
                <div class="flex gap-4 relative">
                    <div class="w-12 h-12 bg-white border border-neutral-100 rounded-2xl flex-shrink-0 flex items-center justify-center text-primary-600 z-10 shadow-sm">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M12 4v16m8-8H4"></path></svg>
                    </div>
                    <div>
                        <p class="text-sm font-bold text-neutral-900">New lead added</p>
                        <p class="text-xs text-neutral-500 mt-0.5">by Admin • 10m ago</p>
                    </div>
                </div>
                <!-- Activity Item -->
                <div class="flex gap-4 relative">
                    <div class="w-12 h-12 bg-white border border-neutral-100 rounded-2xl flex-shrink-0 flex items-center justify-center text-green-600 z-10 shadow-sm">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M5 13l4 4L19 7"></path></svg>
                    </div>
                    <div>
                        <p class="text-sm font-bold text-neutral-900">Deal closed: Won</p>
                        <p class="text-xs text-neutral-500 mt-0.5">by Staff • 2h ago</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    async function loadDashboard() {
        try {
            const response = await fetch('../api/dashboard.php');
            const data = await response.json();

            if (data.error) throw new Error(data.error);

            // Update Stats
            document.getElementById('totalLeads').textContent = data.stats.total || 0;
            document.getElementById('wonLeads').textContent = data.stats.won || 0;
            document.getElementById('lostLeads').textContent = data.stats.lost || 0;
            document.getElementById('newLeads').textContent = data.stats.new || 0;

            // Update Recent Leads
            const tbody = document.getElementById('recentLeadsBody');
            if (data.recent_leads && data.recent_leads.length > 0) {
                tbody.innerHTML = data.recent_leads.map(lead => `
                    <tr class="hover:bg-neutral-50/50 transition-colors group">
                        <td class="px-10 py-6">
                            <div class="flex flex-col">
                                <span class="font-bold text-neutral-900 group-hover:text-primary-600 transition-colors">${lead.company_client_name}</span>
                                <span class="text-xs text-neutral-400 font-medium">${lead.contact_person}</span>
                            </div>
                        </td>
                        <td class="px-10 py-6">
                            <span class="px-4 py-1.5 text-[10px] font-black rounded-xl uppercase tracking-widest ${
                                lead.lead_category === 'Hot' ? 'bg-red-50 text-red-600' :
                                lead.lead_category === 'Warm' ? 'bg-orange-50 text-orange-600' :
                                'bg-blue-50 text-blue-600'
                            }">${lead.lead_category}</span>
                        </td>
                        <td class="px-10 py-6">
                            <div class="flex items-center gap-2">
                                <div class="w-2 h-2 rounded-full ${lead.lead_status === 'Converted' ? 'bg-green-500' : 'bg-primary-500'}"></div>
                                <span class="text-sm font-bold text-neutral-900">${lead.lead_status}</span>
                            </div>
                        </td>
                        <td class="px-10 py-6 text-right">
                            <a href="leads.php" class="p-3 bg-neutral-50 text-neutral-400 hover:text-primary-600 hover:bg-primary-50 rounded-2xl transition-all inline-block">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
                            </a>
                        </td>
                    </tr>
                `).join('');
            } else {
                tbody.innerHTML = `<tr><td colspan="4" class="px-10 py-20 text-center text-neutral-400">No recent leads found.</td></tr>`;
            }

            // Update Followups
            const followupsList = document.getElementById('followupsList');
            if (data.upcoming_followups && data.upcoming_followups.length > 0) {
                followupsList.innerHTML = data.upcoming_followups.map(f => `
                    <div class="flex items-center gap-4 p-4 bg-neutral-50 rounded-3xl group cursor-pointer hover:bg-white hover:shadow-xl hover:shadow-neutral-200/50 transition-all">
                        <div class="w-12 h-12 bg-orange-100 rounded-2xl flex-shrink-0 flex items-center justify-center text-orange-600">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        </div>
                        <div class="flex-1">
                            <p class="font-bold text-neutral-900 text-sm">${f.company_client_name}</p>
                            <p class="text-xs text-neutral-500">${f.next_followup_date}</p>
                        </div>
                    </div>
                `).join('') + '<a href="leads.php" class="block w-full py-4 text-center text-primary-600 font-bold text-sm bg-primary-50 rounded-3xl hover:bg-primary-100 transition-all">View All Schedule</a>';
            } else {
                followupsList.innerHTML = '<div class="p-8 text-center text-neutral-400 text-sm font-medium">No upcoming followups</div>';
            }

            // Update Activity
            const activityLogs = document.getElementById('activityLogs');
            if (data.recent_activity && data.recent_activity.length > 0) {
                activityLogs.innerHTML = data.recent_activity.map(a => `
                    <div class="flex gap-4 relative">
                        <div class="w-12 h-12 bg-white border border-neutral-100 rounded-2xl flex-shrink-0 flex items-center justify-center text-primary-600 z-10 shadow-sm">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M12 4v16m8-8H4"></path></svg>
                        </div>
                        <div>
                            <p class="text-sm font-bold text-neutral-900">${a.activity_type} lead for ${a.company_client_name}</p>
                            <p class="text-xs text-neutral-500 mt-0.5">by ${a.user_name || 'System'} • ${new Date(a.created_at).toLocaleDateString()}</p>
                        </div>
                    </div>
                `).join('');
            } else {
                activityLogs.innerHTML = '<div class="p-8 text-center text-neutral-400 text-sm font-medium">No recent activity</div>';
            }

        } catch (error) {
            console.error('Error:', error);
        }
    }

    document.addEventListener('DOMContentLoaded', loadDashboard);
</script>

<?php layout_end(); ?>