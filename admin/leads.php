<?php
require_once '../includes/middleware.php';
require_once '../includes/components/layout_wrapper.php';
requireAuth();

layout_start('Lead Management - Deckoid ERP');
?>

<div class="mb-10 flex flex-col md:flex-row md:items-center justify-between gap-4">
    <div>
        <h1 class="text-3xl font-bold text-neutral-900 tracking-tight">Lead Management</h1>
        <p class="text-neutral-500 mt-1">Manage and track your sales opportunities efficiently.</p>
    </div>
    <div class="flex items-center gap-3">
        <button onclick="exportLeads()" class="px-5 py-2.5 bg-white border border-neutral-200 text-neutral-700 font-semibold rounded-2xl hover:bg-neutral-50 transition-all shadow-sm flex items-center gap-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
            Export CSV
        </button>
        <button onclick="openAddLeadModal()" class="px-5 py-2.5 bg-primary-600 text-white font-semibold rounded-2xl hover:bg-primary-700 transition-all shadow-lg shadow-primary-200 flex items-center gap-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M12 4v16m8-8H4"></path></svg>
            Add New Lead
        </button>
    </div>
</div>

<!-- Filters Bar -->
<div class="bg-white p-6 rounded-3xl shadow-sm border border-neutral-100 mb-8">
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
        <div class="relative group">
            <span class="absolute inset-y-0 left-4 flex items-center text-neutral-400 group-focus-within:text-primary-500">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
            </span>
            <input type="text" id="search" placeholder="Search by name, company..." 
                   class="w-full bg-neutral-50 border-transparent rounded-2xl py-3 pl-12 pr-4 focus:bg-white focus:border-primary-100 focus:ring-4 focus:ring-primary-50 transition-all outline-none text-sm">
        </div>
        <select id="categoryFilter" class="w-full bg-neutral-50 border-transparent rounded-2xl py-3 px-4 focus:bg-white focus:border-primary-100 focus:ring-4 focus:ring-primary-50 transition-all outline-none text-sm cursor-pointer">
            <option value="">All Categories</option>
            <option value="Hot">🔥 Hot</option>
            <option value="Warm">☀️ Warm</option>
            <option value="Cold">❄️ Cold</option>
        </select>
        <select id="statusFilter" class="w-full bg-neutral-50 border-transparent rounded-2xl py-3 px-4 focus:bg-white focus:border-primary-100 focus:ring-4 focus:ring-primary-50 transition-all outline-none text-sm cursor-pointer">
            <option value="">All Status</option>
            <option value="New">New</option>
            <option value="Contacted">Contacted</option>
            <option value="Qualified">Qualified</option>
            <option value="Proposal">Proposal</option>
            <option value="Negotiation">Negotiation</option>
            <option value="Closed">Closed</option>
        </select>
        <button onclick="loadLeads()" class="w-full bg-neutral-900 text-white font-bold rounded-2xl py-3 hover:bg-neutral-800 transition-all">
            Apply Filters
        </button>
    </div>
</div>

<!-- Table Section -->
<div class="bg-white rounded-3xl shadow-sm border border-neutral-100 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead>
                <tr class="bg-neutral-50/50">
                    <th class="px-8 py-4 text-left text-xs font-bold text-neutral-400 uppercase tracking-wider">Lead Info</th>
                    <th class="px-8 py-4 text-left text-xs font-bold text-neutral-400 uppercase tracking-wider">Contact</th>
                    <th class="px-8 py-4 text-left text-xs font-bold text-neutral-400 uppercase tracking-wider">Category</th>
                    <th class="px-8 py-4 text-left text-xs font-bold text-neutral-400 uppercase tracking-wider">Status</th>
                    <th class="px-8 py-4 text-left text-xs font-bold text-neutral-400 uppercase tracking-wider">Priority</th>
                    <th class="px-8 py-4 text-right text-xs font-bold text-neutral-400 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody id="leadsTableBody" class="divide-y divide-neutral-50">
                <!-- Loaded via JS -->
            </tbody>
        </table>
    </div>
    
    <!-- Pagination -->
    <div class="p-8 border-t border-neutral-50 flex flex-col md:flex-row md:items-center justify-between gap-4">
        <p id="paginationInfo" class="text-sm text-neutral-500 font-medium"></p>
        <div id="pagination" class="flex items-center gap-2">
            <!-- Buttons via JS -->
        </div>
    </div>
</div>

<!-- Modal Container -->
<div id="leadModal" class="fixed inset-0 bg-neutral-900/40 backdrop-blur-sm hidden z-[100] transition-all duration-300">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="bg-white rounded-[2rem] max-w-4xl w-full shadow-2xl overflow-hidden transform transition-all duration-300 scale-95 opacity-0" id="modalContent">
            <div class="p-8 border-b border-neutral-100 flex items-center justify-between bg-neutral-50/50">
                <h3 id="modalTitle" class="text-2xl font-bold text-neutral-900 tracking-tight">Add New Lead</h3>
                <button onclick="closeLeadModal()" class="p-2 text-neutral-400 hover:text-neutral-900 hover:bg-neutral-100 rounded-xl transition-all">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>
            <form id="leadForm" class="p-10">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-10">
                    <div class="space-y-2">
                        <label class="text-sm font-bold text-neutral-700 ml-1">Lead Date *</label>
                        <input type="date" name="lead_date" required class="w-full bg-neutral-50 border-transparent rounded-2xl py-3 px-4 focus:bg-white focus:border-primary-100 focus:ring-4 focus:ring-primary-50 transition-all outline-none">
                    </div>
                    <div class="space-y-2">
                        <label class="text-sm font-bold text-neutral-700 ml-1">Company/Client Name *</label>
                        <input type="text" name="company_client_name" required placeholder="e.g. Acme Corp" class="w-full bg-neutral-50 border-transparent rounded-2xl py-3 px-4 focus:bg-white focus:border-primary-100 focus:ring-4 focus:ring-primary-50 transition-all outline-none">
                    </div>
                    <div class="space-y-2">
                        <label class="text-sm font-bold text-neutral-700 ml-1">Contact Person *</label>
                        <input type="text" name="contact_person" required placeholder="Full Name" class="w-full bg-neutral-50 border-transparent rounded-2xl py-3 px-4 focus:bg-white focus:border-primary-100 focus:ring-4 focus:ring-primary-50 transition-all outline-none">
                    </div>
                    <div class="space-y-2">
                        <label class="text-sm font-bold text-neutral-700 ml-1">Mobile Number *</label>
                        <input type="text" name="mobile_number" required placeholder="+1 234 567 890" class="w-full bg-neutral-50 border-transparent rounded-2xl py-3 px-4 focus:bg-white focus:border-primary-100 focus:ring-4 focus:ring-primary-50 transition-all outline-none">
                    </div>
                    <div class="space-y-2">
                        <label class="text-sm font-bold text-neutral-700 ml-1">Source of Lead *</label>
                        <select name="source_of_lead" required class="w-full bg-neutral-50 border-transparent rounded-2xl py-3 px-4 focus:bg-white focus:border-primary-100 focus:ring-4 focus:ring-primary-50 transition-all outline-none cursor-pointer">
                            <option value="Website">Website</option>
                            <option value="Social Media">Social Media</option>
                            <option value="Referral">Referral</option>
                            <option value="Cold Call">Cold Call</option>
                        </select>
                    </div>
                    <div class="space-y-2">
                        <label class="text-sm font-bold text-neutral-700 ml-1">Lead Category *</label>
                        <select name="lead_category" required class="w-full bg-neutral-50 border-transparent rounded-2xl py-3 px-4 focus:bg-white focus:border-primary-100 focus:ring-4 focus:ring-primary-50 transition-all outline-none cursor-pointer">
                            <option value="Hot">Hot</option>
                            <option value="Warm">Warm</option>
                            <option value="Cold">Cold</option>
                        </select>
                    </div>
                </div>

                <div class="flex justify-end gap-4">
                    <button type="button" onclick="closeLeadModal()" class="px-8 py-3 bg-neutral-100 text-neutral-600 font-bold rounded-2xl hover:bg-neutral-200 transition-all">
                        Cancel
                    </button>
                    <button type="submit" class="px-8 py-3 bg-primary-600 text-white font-bold rounded-2xl hover:bg-primary-700 shadow-lg shadow-primary-200 transition-all">
                        Save Lead Details
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    let currentPage = 1;

    async function loadLeads(page = 1) {
        currentPage = page;
        const search = document.getElementById('search').value;
        const category = document.getElementById('categoryFilter').value;
        const status = document.getElementById('statusFilter').value;

        const params = new URLSearchParams({ page, limit: 10, search, category, status });

        try {
            const response = await fetch(`../api/leads.php?${params}`);
            const data = await response.json();

            const tbody = document.getElementById('leadsTableBody');
            tbody.innerHTML = data.leads.map(lead => `
                <tr class="hover:bg-neutral-50/50 transition-colors group">
                    <td class="px-8 py-5">
                        <div class="flex flex-col">
                            <span class="font-bold text-neutral-900 group-hover:text-primary-600 transition-colors">${lead.company_client_name}</span>
                            <span class="text-xs text-neutral-400 font-medium mt-0.5">${lead.lead_id}</span>
                        </div>
                    </td>
                    <td class="px-8 py-5">
                        <div class="flex flex-col">
                            <span class="text-sm font-semibold text-neutral-700">${lead.contact_person}</span>
                            <span class="text-xs text-neutral-500">${lead.mobile_number}</span>
                        </div>
                    </td>
                    <td class="px-8 py-5">
                        <span class="px-3 py-1 text-[10px] font-bold rounded-lg uppercase tracking-wider ${
                            lead.lead_category === 'Hot' ? 'bg-red-50 text-red-600' :
                            lead.lead_category === 'Warm' ? 'bg-orange-50 text-orange-600' :
                            'bg-blue-50 text-blue-600'
                        }">${lead.lead_category}</span>
                    </td>
                    <td class="px-8 py-5 text-sm font-bold text-neutral-900">${lead.lead_status}</td>
                    <td class="px-8 py-5">
                        <span class="flex items-center gap-1.5 text-xs font-bold text-neutral-500">
                            <div class="w-1.5 h-1.5 rounded-full ${lead.priority === 'High' ? 'bg-red-500' : 'bg-neutral-300'}"></div>
                            ${lead.priority || 'Medium'}
                        </span>
                    </td>
                    <td class="px-8 py-5 text-right">
                        <button onclick="editLead('${lead.id}')" class="p-2 text-neutral-400 hover:text-primary-600 hover:bg-primary-50 rounded-xl transition-all">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
                        </button>
                    </td>
                </tr>
            `).join('');

            updatePagination(data.pagination);
        } catch (error) {
            console.error('Error:', error);
        }
    }

    function updatePagination(pagination) {
        document.getElementById('paginationInfo').textContent = `Showing ${((pagination.page - 1) * pagination.limit) + 1} to ${Math.min(pagination.page * pagination.limit, pagination.total)} of ${pagination.total} leads`;
        
        const container = document.getElementById('pagination');
        container.innerHTML = '';
        
        for (let i = 1; i <= pagination.pages; i++) {
            const btn = document.createElement('button');
            btn.textContent = i;
            btn.className = `w-10 h-10 rounded-xl font-bold text-sm transition-all ${i === pagination.page ? 'bg-primary-600 text-white shadow-lg shadow-primary-200' : 'text-neutral-500 hover:bg-neutral-100'}`;
            btn.onclick = () => loadLeads(i);
            container.appendChild(btn);
        }
    }

    function openAddLeadModal() {
        const modal = document.getElementById('leadModal');
        const content = document.getElementById('modalContent');
        modal.classList.remove('hidden');
        setTimeout(() => {
            content.classList.remove('scale-95', 'opacity-0');
        }, 10);
    }

    function closeLeadModal() {
        const modal = document.getElementById('leadModal');
        const content = document.getElementById('modalContent');
        content.classList.add('scale-95', 'opacity-0');
        setTimeout(() => {
            modal.classList.add('hidden');
        }, 300);
    }

    document.addEventListener('DOMContentLoaded', () => loadLeads());
</script>

<?php layout_end(); ?>