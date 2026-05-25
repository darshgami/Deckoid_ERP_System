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

<!-- Table Card -->
<div class="bg-white rounded-xl shadow-sm border border-neutral-100 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead>
                <tr class="bg-neutral-50/50 border-b border-neutral-100">
                    <th class="px-6 py-3 text-left text-[11px] font-bold text-neutral-500 uppercase tracking-wider">Lead ID</th>
                    <th class="px-6 py-3 text-left text-[11px] font-bold text-neutral-500 uppercase tracking-wider">Name</th>
                    <th class="px-6 py-3 text-left text-[11px] font-bold text-neutral-500 uppercase tracking-wider">Number</th>
                    <th class="px-6 py-3 text-left text-[11px] font-bold text-neutral-500 uppercase tracking-wider">Remarks</th>
                    <th class="px-6 py-3 text-left text-[11px] font-bold text-neutral-500 uppercase tracking-wider">Last Follow-up Notes</th>
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
        try {
            const response = await fetch(`../api/quick_list.php?page=${page}&limit=${currentLimit}`);
            const res = await response.json();
            
            const tbody = document.getElementById('quickListBody');
            if (!res.success || !res.data.leads.length) {
                tbody.innerHTML = `<tr><td colspan="6" class="px-6 py-20 text-center text-neutral-400">No leads found.</td></tr>`;
                return;
            }

            tbody.innerHTML = res.data.leads.map(lead => {
                const nextFollowup = lead.next_followup_date ? formatDate(lead.next_followup_date) : '-';
                const lastFollowup = lead.last_followup_notes || '-';
                return `
                <tr class="hover:bg-neutral-50/50 transition-colors">
                    <td class="px-6 py-4 font-semibold text-neutral-900">${lead.lead_id}</td>
                    <td class="px-6 py-4 font-medium text-neutral-700">${lead.company_client_name || 'N/A'}</td>
                    <td class="px-6 py-4 font-medium text-neutral-700">
                        ${lead.mobile_number ? `<a href="tel:${lead.mobile_number}" class="hover:text-primary transition-colors">${lead.mobile_number}</a>` : 'N/A'}
                    </td>
                    <td class="px-6 py-4 text-neutral-600 max-w-xs truncate" title="${lead.remarks_notes || ''}">
                        ${lead.remarks_notes || '-'}
                    </td>
                    <td class="px-6 py-4 text-neutral-600 max-w-xs truncate" title="${lead.last_followup_notes || ''}">
                        ${lastFollowup}
                    </td>
                    <td class="px-6 py-4 font-medium text-neutral-700 whitespace-nowrap">
                        ${nextFollowup}
                    </td>
                </tr>
            `}).join('');

            updatePagination(res.data.pagination);
        } catch (error) {
            document.getElementById('quickListBody').innerHTML = `<tr><td colspan="6" class="px-6 py-20 text-center text-red-500">Error loading data.</td></tr>`;
        }
    }

    function updatePagination(pagination) {
        document.getElementById('paginationInfo').textContent = `Total ${pagination.total} Leads`;
        const container = document.getElementById('pagination');
        container.innerHTML = '';
        if (pagination.pages <= 1) return;

        // Previous
        const prevBtn = document.createElement('button');
        prevBtn.innerHTML = '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M15 19l-7-7 7-7" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path></svg>';
        prevBtn.className = `w-9 h-9 rounded-xl flex items-center justify-center transition-all ${pagination.page > 1 ? 'text-neutral-500 hover:bg-neutral-100' : 'text-neutral-200 cursor-not-allowed'}`;
        prevBtn.onclick = () => pagination.page > 1 && loadQuickList(pagination.page - 1);
        container.appendChild(prevBtn);

        for (let i = 1; i <= pagination.pages; i++) {
            const btn = document.createElement('button');
            btn.textContent = i;
            btn.className = `w-9 h-9 rounded-xl flex items-center justify-center shrink-0 font-bold text-xs transition-all ${i === pagination.page ? 'bg-primary text-white shadow-lg shadow-primary/25 scale-105' : 'text-neutral-500 hover:bg-neutral-100'}`;
            btn.onclick = () => loadQuickList(i);
            container.appendChild(btn);
        }

        // Next
        const nextBtn = document.createElement('button');
        nextBtn.innerHTML = '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M9 5l7 7-7 7" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path></svg>';
        nextBtn.className = `w-9 h-9 rounded-xl flex items-center justify-center transition-all ${pagination.page < pagination.pages ? 'text-neutral-500 hover:bg-neutral-100' : 'text-neutral-200 cursor-not-allowed'}`;
        nextBtn.onclick = () => pagination.page < pagination.pages && loadQuickList(pagination.page + 1);
        container.appendChild(nextBtn);
    }

    document.addEventListener('DOMContentLoaded', () => loadQuickList());
</script>

<?php layout_end(); ?>
