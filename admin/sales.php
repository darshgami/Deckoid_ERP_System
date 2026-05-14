<?php
require_once '../includes/middleware.php';
require_once '../includes/components/layout_wrapper.php';
requireAuth();

layout_start('Sales - Deckoid ERP');

require_once '../config/env.php';
require_once '../includes/database.php';
$db = Database::getInstance();
?>

<div class="mb-8 flex flex-col md:flex-row md:items-center justify-between gap-4">
    <h1 class="text-2xl font-black text-neutral-900 tracking-tight">Sales Invoices</h1>
    <a href="create_invoice.php" class="px-6 py-3 bg-primary-600 text-white font-bold rounded-2xl hover:bg-primary-700 transition-all shadow-lg shadow-primary-100 flex items-center gap-2 text-sm">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M12 4v16m8-8H4" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"></path></svg>
        New Invoice
    </a>
</div>

<div class="bg-white p-4 rounded-[2rem] shadow-sm border border-neutral-100 mb-8">
    <div class="flex flex-col md:flex-row items-center gap-4">
        <!-- Search -->
        <div class="relative flex-[2] w-full md:w-auto group">
            <span class="absolute inset-y-0 left-5 flex items-center text-neutral-400 group-focus-within:text-primary-500 transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"></path></svg>
            </span>
            <input type="text" id="search" placeholder="Search party or invoice number..." 
                   class="w-full bg-neutral-50/80 border-transparent rounded-2xl py-3.5 pl-14 pr-6 focus:bg-white focus:border-primary-100 focus:ring-4 focus:ring-primary-50 transition-all outline-none text-sm font-bold text-neutral-700 placeholder:text-neutral-400 placeholder:font-medium">
        </div>
        <!-- Type Filter -->
        <div class="w-full md:flex-1">
            <select id="typeFilter" class="w-full bg-neutral-50/80 border-transparent rounded-2xl py-3.5 px-6 focus:bg-white focus:border-primary-100 focus:ring-4 focus:ring-primary-50 transition-all outline-none text-sm font-black text-neutral-900 cursor-pointer appearance-none">
                <option value="">All Types</option>
                <option value="With GST">With GST</option>
                <option value="Without GST">Without GST</option>
            </select>
        </div>
        <!-- Filter Button -->
        <div class="w-full md:w-64">
            <button onclick="loadInvoices()" class="w-full py-3.5 bg-primary-600 text-white font-black rounded-2xl hover:bg-primary-700 shadow-xl shadow-primary-200 transition-all text-xs uppercase tracking-[0.15em] flex items-center justify-center">
                Filter
            </button>
        </div>
    </div>
</div>

<!-- Table -->
<div class="bg-white rounded-2xl shadow-sm border border-neutral-100 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full border-collapse">
            <thead>
                <tr class="bg-neutral-50/50 border-b border-neutral-100">
                    <th class="px-6 py-4 text-left text-[10px] font-black text-neutral-400 uppercase tracking-widest">Invoice</th>
                    <th class="px-6 py-4 text-left text-[10px] font-black text-neutral-400 uppercase tracking-widest">Party Name</th>
                    <th class="px-6 py-4 text-center text-[10px] font-black text-neutral-400 uppercase tracking-widest">Type</th>
                    <th class="px-6 py-4 text-right text-[10px] font-black text-neutral-400 uppercase tracking-widest">Grand Total</th>
                    <th class="px-6 py-4 text-right text-[10px] font-black text-neutral-400 uppercase tracking-widest">Actions</th>
                </tr>
            </thead>
            <tbody id="invoicesTableBody" class="divide-y divide-neutral-50 text-sm">
                <tr><td colspan="5" class="px-6 py-20 text-center text-neutral-400">Loading...</td></tr>
            </tbody>
        </table>
    </div>
    
    <div class="px-6 py-4 border-t border-neutral-50 flex items-center justify-between">
        <p id="paginationInfo" class="text-xs text-neutral-500 font-bold uppercase tracking-tight"></p>
        <div id="pagination" class="flex items-center gap-1"></div>
    </div>
</div>

<script>
    let currentPage = 1;

    async function loadInvoices(page = 1) {
        currentPage = page;
        const search = document.getElementById('search').value;
        const type = document.getElementById('typeFilter').value;

        const params = new URLSearchParams({ page, limit: 10, search, type });

        try {
            const response = await fetch(`../api/sales.php?${params}`);
            const res = await response.json();
            
            const tbody = document.getElementById('invoicesTableBody');
            if (!res.success || !res.data.invoices.length) {
                tbody.innerHTML = `<tr><td colspan="5" class="px-6 py-20 text-center text-neutral-400">No data found.</td></tr>`;
                return;
            }

            tbody.innerHTML = res.data.invoices.map(inv => `
                <tr class="hover:bg-neutral-50 transition-colors">
                    <td class="px-6 py-4">
                        <p class="font-black text-neutral-900">${inv.invoice_number}</p>
                        <p class="text-[10px] text-neutral-400 font-bold uppercase">${inv.invoice_date}</p>
                    </td>
                    <td class="px-6 py-4 font-bold text-neutral-700">${inv.party_name}</td>
                    <td class="px-6 py-4 text-center">
                        <span class="text-[10px] font-black uppercase tracking-widest ${inv.invoice_type === 'With GST' ? 'text-indigo-600' : 'text-neutral-400'}">
                            ${inv.invoice_type}
                        </span>
                    </td>
                    <td class="px-6 py-4 text-right font-black text-neutral-900">₹${parseFloat(inv.grand_total).toLocaleString('en-IN', {minimumFractionDigits: 2})}</td>
                    <td class="px-6 py-4 text-right">
                        <div class="relative inline-block text-left">
                            <button onclick="toggleActions(event, '${inv.id}')" class="p-2 text-neutral-400 hover:text-neutral-900 transition-all rounded-lg hover:bg-neutral-100">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 16 16"><path d="M9.5 13a1.5 1.5 0 1 1-3 0 1.5 1.5 0 0 1 3 0zm0-5a1.5 1.5 0 1 1-3 0 1.5 1.5 0 0 1 3 0zm0-5a1.5 1.5 0 1 1-3 0 1.5 1.5 0 0 1 3 0z"/></svg>
                            </button>
                            <div id="dropdown-${inv.id}" class="hidden absolute right-0 mt-1 w-40 bg-white rounded-xl shadow-xl border border-neutral-100 z-50 py-1.5 overflow-hidden">
                                <a href="create_invoice.php?id=${inv.id}" class="flex items-center gap-2.5 px-4 py-2 text-[11px] font-black uppercase tracking-widest text-neutral-600 hover:bg-neutral-50 hover:text-primary-600 transition-all">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"></path></svg>
                                    Edit
                                </a>
                                <button onclick="printInvoice('${inv.id}')" class="w-full flex items-center gap-2.5 px-4 py-2 text-[11px] font-black uppercase tracking-widest text-neutral-600 hover:bg-neutral-50 hover:text-primary-600 transition-all">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path></svg>
                                    Print
                                </button>
                                <div class="h-px bg-neutral-100 my-1"></div>
                                <button onclick="deleteInvoice('${inv.id}')" class="w-full flex items-center gap-2.5 px-4 py-2 text-[11px] font-black uppercase tracking-widest text-red-500 hover:bg-red-50 transition-all">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"></path></svg>
                                    Delete
                                </button>
                            </div>
                        </div>
                    </td>
                </tr>
            `).join('');

            updatePagination(res.data.pagination);
        } catch (error) {
            document.getElementById('invoicesTableBody').innerHTML = `<tr><td colspan="5" class="px-6 py-20 text-center text-red-500">Error loading data.</td></tr>`;
        }
    }

    function updatePagination(pagination) {
        document.getElementById('paginationInfo').textContent = pagination.total > 0 ? `Total ${pagination.total} Invoices` : '';
        const container = document.getElementById('pagination');
        container.innerHTML = '';
        if (pagination.pages <= 1) return;

        for (let i = 1; i <= pagination.pages; i++) {
            const btn = document.createElement('button');
            btn.textContent = i;
            btn.className = `w-8 h-8 rounded-lg font-bold text-xs ${i === pagination.page ? 'bg-primary-600 text-white' : 'text-neutral-400 hover:bg-neutral-100'}`;
            btn.onclick = () => loadInvoices(i);
            container.appendChild(btn);
        }
    }

    function printInvoice(id) { window.open(`print_invoice.php?id=${id}`, '_blank'); }

    function toggleActions(event, id) {
        event.stopPropagation();
        const dropdown = document.getElementById(`dropdown-${id}`);
        const allDropdowns = document.querySelectorAll('[id^="dropdown-"]');
        
        allDropdowns.forEach(d => {
            if (d.id !== `dropdown-${id}`) d.classList.add('hidden');
        });
        
        dropdown.classList.toggle('hidden');
    }

    document.addEventListener('click', () => {
        document.querySelectorAll('[id^="dropdown-"]').forEach(d => d.classList.add('hidden'));
    });

    async function deleteInvoice(id) {
        if (!confirm('Delete this record?')) return;
        try {
            const response = await fetch(`../api/sales.php?id=${id}`, { method: 'DELETE' });
            if ((await response.json()).success) loadInvoices(currentPage);
        } catch (error) { alert('Error deleting'); }
    }

    document.addEventListener('DOMContentLoaded', () => loadInvoices());
</script>

<?php layout_end(); ?>
