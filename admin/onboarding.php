<?php
require_once '../includes/middleware.php';
require_once '../includes/components/layout_wrapper.php';
requireAuth();

layout_start('Customer Onboarding - Deckoid ERP');
?>

<div class="mb-6 flex flex-col md:flex-row md:items-center justify-between gap-4">
    <div>
        <h1 class="text-xl lg:text-2xl font-semibold text-neutral-900 tracking-tight">Customer Onboarding</h1>
        <p class="text-neutral-500 text-sm mt-0.5">Manage newly converted projects and onboarding progress.</p>
    </div>
</div>

<!-- Filters Bar -->
<div class="bg-white p-4 rounded-xl shadow-sm border border-neutral-100 mb-6">
    <div class="flex flex-col md:flex-row items-center gap-4">
        <!-- Search -->
        <div class="relative flex-[2] w-full group">
            <span class="absolute inset-y-0 left-5 flex items-center text-neutral-400 group-focus-within:text-primary transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path></svg>
            </span>
            <input type="text" id="search" placeholder="Search project or company..." 
                   class="w-full bg-neutral-50/80 border border-neutral-200 rounded-xl py-2.5 pl-12 pr-6 focus:bg-white focus:border-primary focus:ring-4 focus:ring-primary/5 transition-all outline-none text-sm font-medium text-neutral-700"
                   oninput="debouncedSearch()">
        </div>
        <!-- Status Filter -->
        <div class="w-full md:flex-1">
            <select id="statusFilter" class="w-full bg-neutral-50/80 border border-neutral-200 rounded-xl py-2.5 px-6 focus:bg-white focus:border-primary focus:ring-4 focus:ring-primary/5 transition-all outline-none text-sm font-semibold text-neutral-900 cursor-pointer appearance-none" onchange="loadOnboardings(1)">
                <option value="">All Statuses</option>
                <option value="Pending">Pending</option>
                <option value="In Progress">In Progress</option>
                <option value="Completed">Completed</option>
                <option value="On Hold">On Hold</option>
            </select>
        </div>
    </div>
</div>

<!-- Table Section -->
<div class="bg-white rounded-xl shadow-sm border border-neutral-100 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead>
                <tr class="bg-neutral-50/50">
                    <th class="px-4 py-3 text-left text-[11px] font-medium text-neutral-500 uppercase tracking-wider">Project Name</th>
                    <th class="px-4 py-3 text-left text-[11px] font-medium text-neutral-500 uppercase tracking-wider">Company</th>
                    <th class="px-4 py-3 text-left text-[11px] font-medium text-neutral-500 uppercase tracking-wider">Add Work</th>
                    <th class="px-4 py-3 text-left text-[11px] font-medium text-neutral-500 uppercase tracking-wider">Onboarding Date</th>
                    <th class="px-4 py-3 text-left text-[11px] font-medium text-neutral-500 uppercase tracking-wider">Status</th>
                    <?php if ($_SESSION['role'] === 'admin'): ?>
                    <th class="px-4 py-3 text-right text-[11px] font-medium text-neutral-500 uppercase tracking-wider">Actions</th>
                    <?php endif; ?>
                </tr>
            </thead>
            <tbody id="onboardingTableBody" class="divide-y divide-neutral-100">
                <!-- Data loaded via JS -->
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <div class="px-6 py-4 border-t border-neutral-50 flex items-center justify-between bg-neutral-50/30">
        <div class="flex items-center gap-4">
            <p class="text-xs text-neutral-500 font-medium" id="paginationInfo">Showing 0 to 0 of 0 onboardings</p>
            <div class="h-4 w-px bg-neutral-200"></div>
            <div class="flex items-center gap-2">
                <span class="text-[10px] font-bold text-neutral-400 uppercase tracking-wider">Rows per page:</span>
                <select id="limitSelect" onchange="updateLimit(this.value)" class="bg-transparent text-xs font-bold text-neutral-700 outline-none cursor-pointer">
                    <option value="10">10</option>
                    <option value="25">25</option>
                    <option value="50">50</option>
                </select>
            </div>
        </div>
        <div class="flex gap-1.5" id="pagination">
            <!-- Pagination buttons -->
        </div>
    </div>
</div>


<script>
    let currentPage = 1;
    let currentLimit = 10;
    const currentRole = '<?= $_SESSION['role'] ?>';

    let searchTimeout;
    function debouncedSearch() {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(() => {
            loadOnboardings(1);
        }, 300);
    }

    function updateLimit(limit) {
        currentLimit = limit;
        loadOnboardings(1);
    }

    function getStatusBadge(status) {
        switch(status) {
            case 'Completed': return `<span class="px-2.5 py-1 text-[11px] font-bold rounded-lg uppercase tracking-wider bg-green-50 text-green-600 border border-green-200">Completed</span>`;
            case 'In Progress': return `<span class="px-2.5 py-1 text-[11px] font-bold rounded-lg uppercase tracking-wider bg-blue-50 text-blue-600 border border-blue-200">In Progress</span>`;
            case 'On Hold': return `<span class="px-2.5 py-1 text-[11px] font-bold rounded-lg uppercase tracking-wider bg-red-50 text-red-600 border border-red-200">On Hold</span>`;
            default: return `<span class="px-2.5 py-1 text-[11px] font-bold rounded-lg uppercase tracking-wider bg-yellow-50 text-yellow-600 border border-yellow-200">Pending</span>`;
        }
    }

    async function loadOnboardings(page = 1) {
        currentPage = page;
        const search = document.getElementById('search').value;
        const status = document.getElementById('statusFilter').value;
        const tbody = document.getElementById('onboardingTableBody');
        
        tbody.innerHTML = `<tr><td colspan="6" class="px-6 py-12 text-center text-neutral-400 font-semibold text-xs uppercase tracking-wider">Loading...</td></tr>`;

        try {
            const params = new URLSearchParams({ page, limit: currentLimit, search, status });
            const response = await fetch(`../api/onboarding.php?${params}`);
            const res = await response.json();
            
            if (!res.success) throw new Error(res.message);
            
            if (res.data.onboardings.length === 0) {
                tbody.innerHTML = `<tr><td colspan="6" class="px-6 py-12 text-center text-neutral-400 font-semibold text-sm">No onboardings found</td></tr>`;
                document.getElementById('paginationInfo').textContent = 'No onboardings to display';
                document.getElementById('pagination').innerHTML = '';
                return;
            }

            tbody.innerHTML = res.data.onboardings.map(ob => `
                <tr class="hover:bg-neutral-50/50 transition-colors group">
                    <td class="px-4 py-4 align-top">
                        <div class="text-sm font-medium text-neutral-900">${ob.project_name}</div>
                    </td>
                    <td class="px-4 py-4 align-top">
                        <div class="text-sm font-medium text-neutral-900">${ob.company || '-'}</div>
                        ${ob.contact_person ? `<div class="text-xs text-neutral-500">${ob.contact_person}</div>` : ''}
                    </td>
                    <td class="px-4 py-4 align-top">
                        <div class="text-sm text-neutral-600">${ob.add_work || '-'}</div>
                    </td>
                    <td class="px-4 py-4 text-neutral-600 text-sm align-top">${formatDate(ob.onboarding_date)}</td>
                    <td class="px-4 py-4 align-top">${getStatusBadge(ob.status)}</td>
                    ${currentRole === 'admin' ? `
                    <td class="px-4 py-3 text-right">
                        <div class="flex items-center justify-end gap-2">
                            <button onclick="deleteOnboarding('${ob.id}')" class="p-1.5 text-neutral-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition-all" title="Delete">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path></svg>
                            </button>
                        </div>
                    </td>
                    ` : ''}
                </tr>
            `).join('');

            updatePaginationInfo(res.data.pagination, 'paginationInfo', 'onboardings');
            renderPagination(res.data.pagination, loadOnboardings);
        } catch (error) {
            tbody.innerHTML = `<tr><td colspan="6" class="px-6 py-12 text-center text-red-500 font-semibold text-sm">Error loading data.</td></tr>`;
        }
    }

    async function deleteOnboarding(id) {
        if (!confirm('Are you sure you want to delete this onboarding record?')) return;
        try {
            const response = await fetch(`../api/onboarding.php?id=${id}`, { method: 'DELETE' });
            const res = await response.json();
            if (res.success) {
                if (window.toast) toast('Record deleted successfully.', 'success');
                loadOnboardings(currentPage);
            } else {
                if (window.toast) toast(res.message, 'error');
                else alert(res.message);
            }
        } catch (e) {
            console.error(e);
            alert('An error occurred while deleting.');
        }
    }

    document.addEventListener('DOMContentLoaded', () => loadOnboardings(1));
</script>

<?php layout_end(); ?>
