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

<!-- Search & Filters -->
<div class="bg-white p-5 rounded-2xl shadow-sm border border-neutral-100 mb-6">
    <div class="grid grid-cols-1 md:grid-cols-12 gap-4">
        <div class="md:col-span-6 relative group">
            <span class="absolute inset-y-0 left-4 flex items-center text-neutral-400">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path></svg>
            </span>
            <input type="text" id="search" placeholder="Search party or invoice number..." 
                   class="w-full bg-neutral-50 border-transparent rounded-xl py-2.5 pl-11 pr-4 focus:bg-white focus:border-primary-100 focus:ring-4 focus:ring-primary-50 transition-all outline-none text-sm font-medium">
        </div>
        <div class="md:col-span-3">
            <select id="typeFilter" class="w-full bg-neutral-50 border-transparent rounded-xl py-2.5 px-4 focus:bg-white focus:border-primary-100 focus:ring-4 focus:ring-primary-50 transition-all outline-none text-sm font-bold cursor-pointer">
                <option value="">All Types</option>
                <option value="With GST">GST</option>
                <option value="Without GST">Non-GST</option>
            </select>
        </div>
        <div class="md:col-span-3">
            <button onclick="loadInvoices()" class="w-full py-2.5 bg-neutral-900 text-white font-black rounded-xl hover:bg-neutral-800 transition-all text-sm">
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
                        <div class="flex items-center justify-end gap-2">
                            <button onclick="printInvoice('${inv.id}')" class="p-2 text-neutral-400 hover:text-primary-600 transition-all">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path></svg>
                            </button>
                            <button onclick="deleteInvoice('${inv.id}')" class="p-2 text-neutral-400 hover:text-red-600 transition-all">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path></svg>
                            </button>
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
