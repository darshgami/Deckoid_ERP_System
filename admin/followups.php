<?php
require_once '../includes/middleware.php';
require_once '../includes/components/layout_wrapper.php';
requireAuth();

layout_start('Upcoming Followups - Deckoid ERP');
?>

<div class="mb-6 flex flex-col md:flex-row md:items-center justify-between gap-4">
    <div>
        <h1 class="text-xl lg:text-2xl font-semibold text-neutral-900 tracking-tight">Upcoming Followups</h1>
        <p class="text-neutral-500 text-sm mt-0.5">Manage and track your scheduled client interactions.</p>
    </div>
    <div class="flex gap-4">
        <div class="bg-white p-1 rounded-xl shadow-sm border border-neutral-100 flex">
            <button onclick="setFilter('all')" id="filterAll" class="px-5 py-2 rounded-lg text-xs font-semibold transition-all bg-primary text-white shadow-sm">All</button>
            <button onclick="setFilter('today')" id="filterToday" class="px-5 py-2 rounded-lg text-xs font-semibold transition-all text-neutral-400 hover:text-neutral-600">Today</button>
            <button onclick="setFilter('upcoming')" id="filterUpcoming" class="px-5 py-2 rounded-lg text-xs font-semibold transition-all text-neutral-400 hover:text-neutral-600">Upcoming</button>
        </div>
    </div>
</div>

<!-- Followups Table -->
<div class="bg-white rounded-xl shadow-sm border border-neutral-100 overflow-hidden glass-card">
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead class="sticky top-0 z-10 bg-neutral-50/95 backdrop-blur-sm border-b border-neutral-100 shadow-sm">
                <tr>
                    <th class="px-6 py-3 text-left text-[11px] font-bold text-neutral-500 uppercase tracking-wider whitespace-nowrap">Client / Company</th>
                    <th class="px-6 py-3 text-left text-[11px] font-bold text-neutral-500 uppercase tracking-wider whitespace-nowrap">Followup Date</th>
                    <th class="px-6 py-3 text-left text-[11px] font-bold text-neutral-500 uppercase tracking-wider whitespace-nowrap">Last Notes</th>
                    <th class="px-6 py-3 text-left text-[11px] font-bold text-neutral-500 uppercase tracking-wider whitespace-nowrap">Status</th>
                    <th class="px-6 py-3 text-right text-[11px] font-bold text-neutral-500 uppercase tracking-wider whitespace-nowrap">Actions</th>
                </tr>
            </thead>
            <tbody id="followupsBody" class="divide-y divide-neutral-50 text-sm">
                <!-- Data will be loaded here -->
            </tbody>
        </table>
    </div>
    
    <!-- Pagination -->
    <div class="px-6 py-4 border-t border-neutral-50 flex items-center justify-between bg-neutral-50/30">
        <div class="flex items-center gap-4">
            <p class="text-xs text-neutral-500 font-medium" id="paginationInfo">Showing 0 to 0 of 0 followups</p>
            <div class="h-4 w-px bg-neutral-200"></div>
            <div class="flex items-center gap-2">
                <span class="text-[10px] font-bold text-neutral-400 uppercase tracking-wider">Rows per page:</span>
                <select onchange="updateLimit(this.value)" class="bg-transparent text-xs font-bold text-neutral-700 outline-none cursor-pointer">
                    <option value="10">10</option>
                    <option value="25">25</option>
                    <option value="50">50</option>
                    <option value="100">100</option>
                </select>
            </div>
        </div>
        <div class="flex gap-1.5" id="paginationButtons">
            <!-- Pagination buttons will be loaded here -->
        </div>
    </div>
</div>

<script>
    let currentPage = 1;
    let currentLimit = 10;
    let currentFilter = 'all';

    function updateLimit(limit) {
        currentLimit = limit;
        loadFollowups(1);
    }

    async function loadFollowups(page = 1) {
        currentPage = page;
        const tbody = document.getElementById('followupsBody');
        tbody.innerHTML = `
            <tr>
                <td colspan="5" class="px-6 py-12 text-center">
                    <div class="flex flex-col items-center gap-2 text-neutral-400">
                        <div class="w-8 h-8 border-2 border-primary-light border-t-primary rounded-full animate-spin"></div>
                        <span class="font-semibold text-[11px] uppercase tracking-wider">Loading Followups...</span>
                    </div>
                </td>
            </tr>
        `;

        try {
            // We reuse the leads API with a filter for followups
            const response = await fetch(`../api/leads.php?page=${page}&limit=${currentLimit}&has_followup=true&followup_filter=${currentFilter}`);
            const res = await response.json();

            if (!res.success) throw new Error(res.message);

            const data = res.data;

            if (!data.leads || data.leads.length === 0) {
                tbody.innerHTML = `<tr><td colspan="5" class="px-6 py-12 text-center text-neutral-400 text-sm font-medium">No followups scheduled.</td></tr>`;
                return;
            }

            tbody.innerHTML = data.leads.map(lead => `
                <tr class="hover:bg-neutral-50/50 transition-all group">
                    <td class="px-6 py-4">
                        <div class="flex flex-col">
                            <span class="text-sm font-semibold text-neutral-900 group-hover:text-primary transition-colors">${lead.company_client_name}</span>
                            <span class="text-[11px] text-neutral-400 font-medium">${lead.contact_person}</span>
                        </div>
                    </td>
                    <td class="px-6 py-4">
                        <div class="flex items-center gap-2.5">
                            <div class="w-8 h-8 bg-orange-50 rounded-lg flex items-center justify-center text-orange-600">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                            </div>
                            <span class="text-sm font-semibold text-neutral-700">${formatDate(lead.next_followup_date)}</span>
                        </div>
                    </td>
                    <td class="px-6 py-4">
                        <p class="text-xs text-neutral-500 max-w-xs truncate font-medium">${lead.last_followup_notes || 'No notes available'}</p>
                    </td>
                    <td class="px-6 py-4">
                        <span class="px-2 py-0.5 text-[11px] font-semibold rounded-md uppercase tracking-wider bg-blue-50 text-blue-600">Active</span>
                    </td>
                    <td class="px-6 py-4 text-right">
                        <a href="leads.php?id=${lead.id}" class="p-2 text-neutral-400 hover:text-primary-600 hover:bg-primary-50 rounded-lg transition-all inline-block">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                        </a>
                    </td>
                </tr>
            `).join('');

            updatePagination(data.pagination);
        } catch (error) {
            console.error('Error:', error);
            tbody.innerHTML = `<tr><td colspan="5" class="px-6 py-12 text-center text-red-500 text-sm font-semibold">${error.message}</td></tr>`;
        }
    }

    function setFilter(filter) {
        currentFilter = filter;
        
        // Update UI
        ['All', 'Today', 'Upcoming'].forEach(f => {
            const btn = document.getElementById('filter' + f);
            if (f.toLowerCase() === filter) {
                btn.className = "px-5 py-2 rounded-lg text-xs font-semibold transition-all bg-primary text-white shadow-sm";
            } else {
                btn.className = "px-5 py-2 rounded-lg text-xs font-semibold transition-all text-neutral-400 hover:text-neutral-600";
            }
        });

        loadFollowups(1);
    }

    function updatePagination(pagination) {
        const info = document.getElementById('paginationInfo');
        info.textContent = `Showing ${((pagination.page - 1) * pagination.limit) + 1} to ${Math.min(pagination.page * pagination.limit, pagination.total)} of ${pagination.total} followups`;

        const container = document.getElementById('paginationButtons');
        container.innerHTML = '';
        if (pagination.pages <= 1) return;

        // Previous
        const prevBtn = document.createElement('button');
        prevBtn.innerHTML = '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M15 19l-7-7 7-7" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"></path></svg>';
        prevBtn.className = `w-9 h-9 rounded-xl flex items-center justify-center transition-all ${pagination.page > 1 ? 'text-neutral-500 hover:bg-neutral-100' : 'text-neutral-200 cursor-not-allowed'}`;
        prevBtn.onclick = () => pagination.page > 1 && loadFollowups(pagination.page - 1);
        container.appendChild(prevBtn);

        for (let i = 1; i <= pagination.pages; i++) {
            const btn = document.createElement('button');
            btn.textContent = i;
            btn.className = `w-9 h-9 rounded-xl font-bold text-xs transition-all ${i === pagination.page ? 'bg-primary text-white shadow-lg shadow-primary/25 scale-105' : 'text-neutral-500 hover:bg-neutral-100'}`;
            btn.onclick = () => loadFollowups(i);
            container.appendChild(btn);
        }

        // Next
        const nextBtn = document.createElement('button');
        nextBtn.innerHTML = '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M9 5l7 7-7 7" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"></path></svg>';
        nextBtn.className = `w-9 h-9 rounded-xl flex items-center justify-center transition-all ${pagination.page < pagination.pages ? 'text-neutral-500 hover:bg-neutral-100' : 'text-neutral-200 cursor-not-allowed'}`;
        nextBtn.onclick = () => pagination.page < pagination.pages && loadFollowups(pagination.page + 1);
        container.appendChild(nextBtn);
    }

    document.addEventListener('DOMContentLoaded', () => loadFollowups(1));
</script>

<?php layout_end(); ?>
