<?php
require_once '../includes/middleware.php';
require_once '../includes/components/layout_wrapper.php';
requireAuth();

layout_start('Quick List - Deckoid ERP');
?>

<div class="mb-6 flex flex-col md:flex-row md:items-center justify-between gap-4">
    <div>
        <h1 class="text-xl lg:text-2xl font-semibold text-neutral-900 tracking-tight">Quick List</h1>
        <p class="text-neutral-500 text-sm mt-0.5">Fast view of essential lead information.</p>
    </div>
    <a href="leads.php" class="text-[11px] font-semibold text-neutral-400 uppercase tracking-wider hover:text-primary transition-all flex items-center gap-2">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M15 19l-7-7 7-7" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path></svg>
        Back to Leads
    </a>
</div>

<!-- Filters Bar -->
<div class="bg-white p-4 lg:p-5 rounded-xl shadow-sm border border-neutral-100 mb-6">
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div class="relative group md:col-span-2">
            <span class="absolute inset-y-0 left-4 flex items-center text-neutral-400 group-focus-within:text-primary">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
            </span>
            <input type="text" id="search" placeholder="Search by name, company, mobile, email..." 
                   class="w-full bg-neutral-50 border-transparent rounded-xl py-2.5 pl-11 pr-4 focus:bg-white focus:border-primary/20 focus:ring-4 focus:ring-primary/5 transition-all outline-none text-sm">
        </div>
        <button onclick="loadQuickList(1)" class="btn btn-primary text-sm h-full">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"></path></svg>
            Apply Filter
        </button>
    </div>
</div>

<!-- Table Card -->
<div class="bg-white rounded-xl shadow-sm border border-neutral-100 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead>
                <tr class="bg-neutral-50/50 border-b border-neutral-100">
                    <th class="px-6 py-3 text-left text-[11px] font-bold text-neutral-500 uppercase tracking-wider">Lead ID</th>
                    <th class="px-6 py-3 text-left text-[11px] font-bold text-neutral-500 uppercase tracking-wider">Company</th>
                    <th class="px-6 py-3 text-left text-[11px] font-bold text-neutral-500 uppercase tracking-wider">Number</th>
                    <th class="px-6 py-3 text-left text-[11px] font-bold text-neutral-500 uppercase tracking-wider">Remarks</th>
                    <th class="px-6 py-3 text-left text-[11px] font-bold text-neutral-500 uppercase tracking-wider">Next Follow-up Date</th>
                </tr>
            </thead>
            <tbody id="quickListBody" class="divide-y divide-neutral-50 text-sm">
                <tr><td colspan="6" class="px-6 py-20 text-center text-neutral-400">Loading...</td></tr>
            </tbody>
        </table>
    </div>
    
    <!-- Pagination -->
    <div class="px-6 py-4 border-t border-neutral-50 flex items-center justify-between bg-neutral-50/30">
        <div class="flex items-center gap-4">
            <p id="paginationInfo" class="text-xs text-neutral-500 font-bold uppercase tracking-tight"></p>
            <div class="h-4 w-px bg-neutral-200"></div>
            <div class="flex items-center gap-2">
                <span class="text-[10px] font-bold text-neutral-400 uppercase tracking-wider">Rows per page:</span>
                <select onchange="updateLimit(this.value)" class="bg-transparent text-xs font-bold text-neutral-700 outline-none cursor-pointer">
                    <option value="10">10</option>
                    <option value="25" selected>25</option>
                    <option value="50">50</option>
                    <option value="100">100</option>
                </select>
            </div>
        </div>
        <div id="pagination" class="flex items-center gap-1.5"></div>
    </div>
</div>

<script>
    let currentPage = 1;
    let currentLimit = 25;

    function formatDate(dateStr) {
        if (!dateStr) return '-';
        const dateOnly = dateStr.split(' ')[0];
        const parts = dateOnly.split('-');
        if (parts.length === 3) {
            return `${parts[2]}/${parts[1]}/${parts[0]}`;
        }
        return dateStr;
    }

    function updateLimit(limit) {
        currentLimit = limit;
        loadQuickList(1);
    }

    async function loadQuickList(page = 1) {
        currentPage = page;
        const search = document.getElementById('search')?.value || '';
        try {
            const response = await fetch(`../api/quick_list.php?page=${page}&limit=${currentLimit}&search=${encodeURIComponent(search)}`);
            const res = await response.json();
            
            const tbody = document.getElementById('quickListBody');
            if (!res.success || !res.data.leads.length) {
                tbody.innerHTML = `<tr><td colspan="6" class="px-6 py-20 text-center text-neutral-400">No leads found.</td></tr>`;
                return;
            }

            tbody.innerHTML = res.data.leads.map(lead => {
                const nextFollowup = lead.next_followup_date ? formatDate(lead.next_followup_date) : '-';
                return `
                <tr class="hover:bg-neutral-50/50 transition-colors">
                    <td class="px-6 py-4 font-semibold text-neutral-900">${lead.lead_id}</td>
                    <td class="px-6 py-4 font-medium text-neutral-700">${lead.company || 'N/A'}</td>
                    <td class="px-6 py-4 font-medium text-neutral-700">
                        ${lead.mobile_number ? `<a href="tel:${lead.mobile_number}" class="hover:text-primary transition-colors">${lead.mobile_number}</a>` : 'N/A'}
                    </td>
                    <td class="px-6 py-4 text-neutral-600 max-w-xs truncate" title="${lead.remarks || ''}">
                        ${lead.remarks || '-'}
                    </td>
                    <td class="px-6 py-4 font-medium text-neutral-700 whitespace-nowrap">
                        ${nextFollowup}
                    </td>
                </tr>
            `}).join('');

            updatePaginationInfo(res.data.pagination, 'paginationInfo', 'leads');
            renderPagination(res.data.pagination, loadQuickList);
        } catch (error) {
            document.getElementById('quickListBody').innerHTML = `<tr><td colspan="6" class="px-6 py-20 text-center text-red-500">Error loading data.</td></tr>`;
        }
    }

    document.addEventListener('DOMContentLoaded', () => loadQuickList());
</script>

<?php layout_end(); ?>
