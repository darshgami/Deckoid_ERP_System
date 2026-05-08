<?php
require_once '../includes/middleware.php';
require_once '../includes/components/layout_wrapper.php';
requireAuth();

layout_start('Upcoming Followups - Deckoid ERP');
?>

<div class="mb-10 flex items-center justify-between">
    <div>
        <h1 class="text-4xl font-black text-neutral-900 tracking-tight">Upcoming Followups</h1>
        <p class="text-neutral-500 mt-2 font-medium">Manage and track your scheduled client interactions.</p>
    </div>
    <div class="flex gap-4">
        <div class="bg-white p-1 rounded-2xl shadow-sm border border-neutral-100 flex">
            <button onclick="setFilter('all')" id="filterAll" class="px-6 py-2.5 rounded-xl text-sm font-bold transition-all bg-primary-600 text-white shadow-lg shadow-primary-200">All</button>
            <button onclick="setFilter('today')" id="filterToday" class="px-6 py-2.5 rounded-xl text-sm font-bold transition-all text-neutral-500 hover:text-neutral-900">Today</button>
            <button onclick="setFilter('upcoming')" id="filterUpcoming" class="px-6 py-2.5 rounded-xl text-sm font-bold transition-all text-neutral-500 hover:text-neutral-900">Upcoming</button>
        </div>
    </div>
</div>

<!-- Followups Table -->
<div class="bg-white rounded-[2.5rem] shadow-sm border border-neutral-100 overflow-hidden glass-card">
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead>
                <tr class="bg-neutral-50/50">
                    <th class="px-10 py-6 text-left text-xs font-bold text-neutral-400 uppercase tracking-widest">Client / Company</th>
                    <th class="px-10 py-6 text-left text-xs font-bold text-neutral-400 uppercase tracking-widest">Followup Date</th>
                    <th class="px-10 py-6 text-left text-xs font-bold text-neutral-400 uppercase tracking-widest">Last Notes</th>
                    <th class="px-10 py-6 text-left text-xs font-bold text-neutral-400 uppercase tracking-widest">Status</th>
                    <th class="px-10 py-6 text-right text-xs font-bold text-neutral-400 uppercase tracking-widest">Actions</th>
                </tr>
            </thead>
            <tbody id="followupsBody" class="divide-y divide-neutral-50">
                <!-- Data will be loaded here -->
            </tbody>
        </table>
    </div>
    
    <!-- Pagination -->
    <div class="p-8 border-t border-neutral-50 flex items-center justify-between bg-neutral-50/30">
        <p class="text-sm text-neutral-500 font-medium" id="paginationInfo">Showing 0 to 0 of 0 followups</p>
        <div class="flex gap-2" id="paginationButtons">
            <!-- Pagination buttons will be loaded here -->
        </div>
    </div>
</div>

<script>
    let currentPage = 1;
    let currentFilter = 'all';

    async function loadFollowups(page = 1) {
        currentPage = page;
        const tbody = document.getElementById('followupsBody');
        tbody.innerHTML = `
            <tr>
                <td colspan="5" class="px-10 py-20 text-center">
                    <div class="flex flex-col items-center gap-3 text-neutral-400">
                        <div class="w-10 h-10 border-4 border-primary-200 border-t-primary-600 rounded-full animate-spin"></div>
                        <span class="font-bold text-sm uppercase tracking-widest">Loading Followups...</span>
                    </div>
                </td>
            </tr>
        `;

        try {
            // We reuse the leads API with a filter for followups
            const response = await fetch(`../api/leads.php?page=${page}&has_followup=true&followup_filter=${currentFilter}`);
            const data = await response.json();

            if (data.error) throw new Error(data.error);

            if (data.data.length === 0) {
                tbody.innerHTML = `<tr><td colspan="5" class="px-10 py-20 text-center text-neutral-400 font-medium">No followups scheduled.</td></tr>`;
                return;
            }

            tbody.innerHTML = data.data.map(lead => `
                <tr class="hover:bg-neutral-50/50 transition-all group">
                    <td class="px-10 py-7">
                        <div class="flex flex-col">
                            <span class="font-bold text-neutral-900 group-hover:text-primary-600 transition-colors">${lead.company_client_name}</span>
                            <span class="text-xs text-neutral-400 font-medium">${lead.contact_person}</span>
                        </div>
                    </td>
                    <td class="px-10 py-7">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 bg-orange-50 rounded-xl flex items-center justify-center text-orange-600">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                            </div>
                            <span class="font-bold text-neutral-700">${lead.next_followup_date || 'N/A'}</span>
                        </div>
                    </td>
                    <td class="px-10 py-7">
                        <p class="text-sm text-neutral-500 max-w-xs truncate font-medium">${lead.last_followup_notes || 'No notes available'}</p>
                    </td>
                    <td class="px-10 py-7">
                        <span class="px-4 py-1.5 text-[10px] font-black rounded-xl uppercase tracking-widest bg-blue-50 text-blue-600">Active</span>
                    </td>
                    <td class="px-10 py-7 text-right">
                        <a href="leads.php?id=${lead.id}" class="p-3 bg-neutral-50 text-neutral-400 hover:text-primary-600 hover:bg-primary-50 rounded-2xl transition-all inline-block">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                        </a>
                    </td>
                </tr>
            `).join('');

            updatePagination(data.pagination);
        } catch (error) {
            console.error('Error:', error);
            tbody.innerHTML = `<tr><td colspan="5" class="px-10 py-20 text-center text-red-500 font-bold">${error.message}</td></tr>`;
        }
    }

    function setFilter(filter) {
        currentFilter = filter;
        
        // Update UI
        ['All', 'Today', 'Upcoming'].forEach(f => {
            const btn = document.getElementById('filter' + f);
            if (f.toLowerCase() === filter) {
                btn.className = "px-6 py-2.5 rounded-xl text-sm font-bold transition-all bg-primary-600 text-white shadow-lg shadow-primary-200";
            } else {
                btn.className = "px-6 py-2.5 rounded-xl text-sm font-bold transition-all text-neutral-500 hover:text-neutral-900";
            }
        });

        loadFollowups(1);
    }

    function updatePagination(pagination) {
        const info = document.getElementById('paginationInfo');
        info.textContent = `Showing ${((pagination.page - 1) * pagination.limit) + 1} to ${Math.min(pagination.page * pagination.limit, pagination.total)} of ${pagination.total} followups`;

        const container = document.getElementById('paginationButtons');
        container.innerHTML = '';

        for (let i = 1; i <= pagination.pages; i++) {
            const btn = document.createElement('button');
            btn.className = `w-10 h-10 rounded-xl font-bold text-sm transition-all ${i === pagination.page ? 'bg-primary-600 text-white shadow-lg shadow-primary-200' : 'bg-white text-neutral-500 hover:bg-neutral-50'}`;
            btn.textContent = i;
            btn.onclick = () => loadFollowups(i);
            container.appendChild(btn);
        }
    }

    document.addEventListener('DOMContentLoaded', () => loadFollowups(1));
</script>

<?php layout_end(); ?>
