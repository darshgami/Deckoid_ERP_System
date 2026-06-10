<?php
require_once '../includes/middleware.php';
require_once '../includes/components/layout_wrapper.php';
requireAdmin();

layout_start('Activity Logs - Deckoid ERP');
?>

<div class="mb-6">
    <h1 class="text-xl lg:text-2xl font-semibold text-neutral-900 tracking-tight">Activity Logs</h1>
    <p class="text-neutral-500 text-sm mt-0.5">Track all system changes and lead updates in real-time.</p>
</div>

<!-- Filters Bar -->
<div class="bg-white p-4 lg:p-5 rounded-xl shadow-sm border border-neutral-100 mb-6">
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div class="relative group md:col-span-2">
            <span class="absolute inset-y-0 left-4 flex items-center text-neutral-400 group-focus-within:text-primary">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
            </span>
            <input type="text" id="search" placeholder="Search logs by company or content..." autocomplete="off"
                   class="w-full bg-neutral-50 border-transparent rounded-xl py-2.5 pl-11 pr-4 focus:bg-white focus:border-primary/20 focus:ring-4 focus:ring-primary/5 transition-all outline-none text-sm">
        </div>
        <button onclick="loadLogs(1)" class="btn btn-primary text-sm h-full">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"></path></svg>
            Apply Filter
        </button>
    </div>
</div>

<!-- Logs Timeline -->
<div class="glass-card bg-white rounded-xl shadow-sm border border-neutral-100 p-6 lg:p-8 relative overflow-hidden">
    <div class="space-y-8 relative before:absolute before:left-[19px] before:top-4 before:bottom-4 before:w-[1.5px] before:bg-neutral-100" id="logsContainer">
        <!-- Logs will be loaded here -->
    </div>
    
    <!-- Load More -->
    <div class="mt-8 text-center">
        <button id="loadMoreBtn" onclick="loadLogs(nextPage)" class="btn btn-secondary text-xs px-6 py-2.5">Load More Activity</button>
    </div>
</div>

<script>
    let nextPage = 1;
    let isLoading = false;

    async function loadLogs(page = 1) {
        if (isLoading) return;
        isLoading = true;
        
        const container = document.getElementById('logsContainer');
        const loadMoreBtn = document.getElementById('loadMoreBtn');
        
        const search = document.getElementById('search')?.value || '';
        
        if (page === 1) container.innerHTML = '';
        loadMoreBtn.textContent = 'Fetching Activity...';

        try {
            const response = await fetch(`../api/logs.php?page=${page}&limit=20&search=${encodeURIComponent(search)}`);
            const res = await response.json();

            if (!res.success) throw new Error(res.message);
            
            const data = res.data;

            if (!data?.data || (data.data.length === 0 && page === 1)) {
                container.innerHTML = '<div class="text-center py-12 text-neutral-400 font-medium text-sm">No activity logs found.</div>';
                loadMoreBtn.classList.add('hidden');
                return;
            }

            const html = data.data.map(log => {
                const date = new Date(log.created_at);
                const timeStr = date.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
                const dateStr = date.toLocaleDateString([], { month: 'short', day: 'numeric', year: 'numeric' });
                
                let iconBg = 'bg-primary-100 text-primary-600';
                let icon = '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>';
                
                if (log.activity_type === 'created') {
                    iconBg = 'bg-green-100 text-green-600';
                    icon = '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M12 4v16m8-8H4"></path></svg>';
                } else if (log.activity_type === 'deleted') {
                    iconBg = 'bg-red-100 text-red-600';
                    icon = '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>';
                }

                return `
                    <div class="flex gap-6 relative group">
                        <div class="w-10 h-10 ${iconBg} rounded-xl flex-shrink-0 flex items-center justify-center z-10 shadow-sm border-2 border-white transition-transform group-hover:scale-105">
                            ${icon}
                        </div>
                        <div class="flex-1 pb-2">
                            <div class="flex items-center justify-between mb-0.5">
                                <h4 class="text-sm font-semibold text-neutral-900 capitalize">${log.activity_type} Lead</h4>
                                <span class="text-[11px] font-semibold text-neutral-400 uppercase tracking-wider">${dateStr} • ${timeStr}</span>
                            </div>
                            <p class="text-xs text-neutral-500 font-medium">
                                <span class="text-primary font-semibold">@${log.user_name || 'System'}</span> 
                                ${log.notes || `processed lead for`} 
                                <span class="text-neutral-900 font-semibold">${log.company}</span>
                            </p>
                        </div>
                    </div>
                `;
            }).join('');

            container.innerHTML += html;
            
            if (page >= data.pagination.pages) {
                loadMoreBtn.classList.add('hidden');
            } else {
                loadMoreBtn.classList.remove('hidden');
                loadMoreBtn.textContent = 'Load More Activity';
                nextPage = page + 1;
            }

        } catch (error) {
            console.error('Error:', error);
            showToast('Failed to load logs', 'error');
        } finally {
            isLoading = false;
        }
    }

    document.addEventListener('DOMContentLoaded', () => loadLogs(1));
</script>

<?php layout_end(); ?>
