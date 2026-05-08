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
            Export Excel
        </button>
        <a href="add_lead.php" class="px-5 py-2.5 bg-primary-600 text-white font-semibold rounded-2xl hover:bg-primary-700 transition-all shadow-lg shadow-primary-200 flex items-center gap-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M12 4v16m8-8H4"></path></svg>
            Add New Lead
        </a>
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
        <div class="bg-white rounded-[2.5rem] max-w-5xl w-full shadow-2xl overflow-hidden transform transition-all duration-300 scale-95 opacity-0" id="modalContent">
            <div class="p-8 border-b border-neutral-100 flex items-center justify-between bg-neutral-50/50">
                <div>
                    <h3 id="modalTitle" class="text-2xl font-bold text-neutral-900 tracking-tight">Add New Lead</h3>
                    <p class="text-sm text-neutral-500 mt-1">Please fill in all the required details below.</p>
                </div>
                <button onclick="closeLeadModal()" class="p-2 text-neutral-400 hover:text-neutral-900 hover:bg-neutral-100 rounded-xl transition-all">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>
            
            <!-- Modal Tabs -->
            <div class="flex border-b border-neutral-100 px-8 bg-white overflow-x-auto">
                <button onclick="switchTab('basic')" class="tab-btn active px-6 py-4 text-sm font-bold border-b-2 border-primary-600 text-primary-600 whitespace-nowrap" id="tab-basic">Basic Info</button>
                <button onclick="switchTab('details')" class="tab-btn px-6 py-4 text-sm font-bold border-b-2 border-transparent text-neutral-400 hover:text-neutral-600 whitespace-nowrap" id="tab-details">Location & Details</button>
                <button onclick="switchTab('sales')" class="tab-btn px-6 py-4 text-sm font-bold border-b-2 border-transparent text-neutral-400 hover:text-neutral-600 whitespace-nowrap" id="tab-sales">Sales Tracking</button>
                <button onclick="switchTab('project')" class="tab-btn px-6 py-4 text-sm font-bold border-b-2 border-transparent text-neutral-400 hover:text-neutral-600 whitespace-nowrap" id="tab-project">Project & Others</button>
            </div>

            <form id="leadForm" class="p-10 max-h-[70vh] overflow-y-auto">
                <input type="hidden" name="id" id="lead_id_input">
                
                <!-- Basic Info Tab -->
                <div id="content-basic" class="tab-content space-y-8">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
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
                            <label class="text-sm font-bold text-neutral-700 ml-1">Email ID</label>
                            <input type="email" name="email_id" placeholder="email@example.com" class="w-full bg-neutral-50 border-transparent rounded-2xl py-3 px-4 focus:bg-white focus:border-primary-100 focus:ring-4 focus:ring-primary-50 transition-all outline-none">
                        </div>
                        <div class="space-y-2">
                            <label class="text-sm font-bold text-neutral-700 ml-1">Source of Lead *</label>
                            <select name="source_of_lead" required class="w-full bg-neutral-50 border-transparent rounded-2xl py-3 px-4 focus:bg-white focus:border-primary-100 focus:ring-4 focus:ring-primary-50 transition-all outline-none cursor-pointer">
                                <option value="Website">Website</option>
                                <option value="Social Media">Social Media</option>
                                <option value="Referral">Referral</option>
                                <option value="Cold Call">Cold Call</option>
                                <option value="Exhibition">Exhibition</option>
                                <option value="Other">Other</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Location & Details Tab -->
                <div id="content-details" class="tab-content hidden space-y-8">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                        <div class="space-y-2">
                            <label class="text-sm font-bold text-neutral-700 ml-1">Alternative Number</label>
                            <input type="text" name="alternative_number" placeholder="Other contact number" class="w-full bg-neutral-50 border-transparent rounded-2xl py-3 px-4 focus:bg-white focus:border-primary-100 focus:ring-4 focus:ring-primary-50 transition-all outline-none">
                        </div>
                        <div class="space-y-2">
                            <label class="text-sm font-bold text-neutral-700 ml-1">City</label>
                            <input type="text" name="city" placeholder="City name" class="w-full bg-neutral-50 border-transparent rounded-2xl py-3 px-4 focus:bg-white focus:border-primary-100 focus:ring-4 focus:ring-primary-50 transition-all outline-none">
                        </div>
                        <div class="space-y-2">
                            <label class="text-sm font-bold text-neutral-700 ml-1">State</label>
                            <input type="text" name="state" placeholder="State name" class="w-full bg-neutral-50 border-transparent rounded-2xl py-3 px-4 focus:bg-white focus:border-primary-100 focus:ring-4 focus:ring-primary-50 transition-all outline-none">
                        </div>
                        <div class="space-y-2">
                            <label class="text-sm font-bold text-neutral-700 ml-1">Service Interested In</label>
                            <input type="text" name="service_interested_in" placeholder="Service name" class="w-full bg-neutral-50 border-transparent rounded-2xl py-3 px-4 focus:bg-white focus:border-primary-100 focus:ring-4 focus:ring-primary-50 transition-all outline-none">
                        </div>
                        <div class="md:col-span-2 space-y-2">
                            <label class="text-sm font-bold text-neutral-700 ml-1">Requirement Details</label>
                            <textarea name="requirement_details" rows="3" placeholder="Specific requirements..." class="w-full bg-neutral-50 border-transparent rounded-2xl py-3 px-4 focus:bg-white focus:border-primary-100 focus:ring-4 focus:ring-primary-50 transition-all outline-none resize-none"></textarea>
                        </div>
                    </div>
                </div>

                <!-- Sales Tracking Tab -->
                <div id="content-sales" class="tab-content hidden space-y-8">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                        <div class="space-y-2">
                            <label class="text-sm font-bold text-neutral-700 ml-1">Lead Category *</label>
                            <select name="lead_category" required class="w-full bg-neutral-50 border-transparent rounded-2xl py-3 px-4 focus:bg-white focus:border-primary-100 focus:ring-4 focus:ring-primary-50 transition-all outline-none cursor-pointer">
                                <option value="Hot">🔥 Hot</option>
                                <option value="Warm">☀️ Warm</option>
                                <option value="Cold">❄️ Cold</option>
                            </select>
                        </div>
                        <div class="space-y-2">
                            <label class="text-sm font-bold text-neutral-700 ml-1">Lead Status *</label>
                            <select name="lead_status" required class="w-full bg-neutral-50 border-transparent rounded-2xl py-3 px-4 focus:bg-white focus:border-primary-100 focus:ring-4 focus:ring-primary-50 transition-all outline-none cursor-pointer">
                                <option value="New">New</option>
                                <option value="Contacted">Contacted</option>
                                <option value="Qualified">Qualified</option>
                                <option value="Proposal Sent">Proposal Sent</option>
                                <option value="Negotiation">Negotiation</option>
                                <option value="Converted">Converted</option>
                                <option value="Lost">Lost</option>
                            </select>
                        </div>
                        <div class="space-y-2">
                            <label class="text-sm font-bold text-neutral-700 ml-1">Priority</label>
                            <select name="priority" class="w-full bg-neutral-50 border-transparent rounded-2xl py-3 px-4 focus:bg-white focus:border-primary-100 focus:ring-4 focus:ring-primary-50 transition-all outline-none cursor-pointer">
                                <option value="High">High</option>
                                <option value="Medium" selected>Medium</option>
                                <option value="Low">Low</option>
                            </select>
                        </div>
                        <div class="space-y-2">
                            <label class="text-sm font-bold text-neutral-700 ml-1">Deal Status *</label>
                            <select name="deal_status" required class="w-full bg-neutral-50 border-transparent rounded-2xl py-3 px-4 focus:bg-white focus:border-primary-100 focus:ring-4 focus:ring-primary-50 transition-all outline-none cursor-pointer">
                                <option value="Open">Open</option>
                                <option value="Won">Won</option>
                                <option value="Lost">Lost</option>
                                <option value="On Hold">On Hold</option>
                            </select>
                        </div>
                        <div class="space-y-2">
                            <label class="text-sm font-bold text-neutral-700 ml-1">Estimated Budget</label>
                            <input type="number" name="estimated_budget" placeholder="0.00" step="0.01" class="w-full bg-neutral-50 border-transparent rounded-2xl py-3 px-4 focus:bg-white focus:border-primary-100 focus:ring-4 focus:ring-primary-50 transition-all outline-none">
                        </div>
                        <div class="space-y-2">
                            <label class="text-sm font-bold text-neutral-700 ml-1">Next Follow-up Date</label>
                            <input type="date" name="next_followup_date" class="w-full bg-neutral-50 border-transparent rounded-2xl py-3 px-4 focus:bg-white focus:border-primary-100 focus:ring-4 focus:ring-primary-50 transition-all outline-none">
                        </div>
                        <div class="md:col-span-2 space-y-2">
                            <label class="text-sm font-bold text-neutral-700 ml-1">Last Follow-up Notes</label>
                            <textarea name="last_followup_notes" rows="2" placeholder="Summary of last conversation..." class="w-full bg-neutral-50 border-transparent rounded-2xl py-3 px-4 focus:bg-white focus:border-primary-100 focus:ring-4 focus:ring-primary-50 transition-all outline-none resize-none"></textarea>
                        </div>
                    </div>
                </div>

                <!-- Project & Others Tab -->
                <div id="content-project" class="tab-content hidden space-y-8">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                        <div class="space-y-2">
                            <label class="text-sm font-bold text-neutral-700 ml-1">Payment Status *</label>
                            <select name="payment_status" required class="w-full bg-neutral-50 border-transparent rounded-2xl py-3 px-4 focus:bg-white focus:border-primary-100 focus:ring-4 focus:ring-primary-50 transition-all outline-none cursor-pointer">
                                <option value="Pending">Pending</option>
                                <option value="Partial">Partial</option>
                                <option value="Paid">Paid</option>
                                <option value="Refunded">Refunded</option>
                            </select>
                        </div>
                        <div class="space-y-2">
                            <label class="text-sm font-bold text-neutral-700 ml-1">Project Status</label>
                            <select name="project_status" class="w-full bg-neutral-50 border-transparent rounded-2xl py-3 px-4 focus:bg-white focus:border-primary-100 focus:ring-4 focus:ring-primary-50 transition-all outline-none cursor-pointer">
                                <option value="">Select Status</option>
                                <option value="Not Started">Not Started</option>
                                <option value="In Progress">In Progress</option>
                                <option value="Completed">Completed</option>
                                <option value="Cancelled">Cancelled</option>
                            </select>
                        </div>
                        <div class="space-y-2">
                            <label class="text-sm font-bold text-neutral-700 ml-1">Expected Closing Date</label>
                            <input type="date" name="expected_closing_date" class="w-full bg-neutral-50 border-transparent rounded-2xl py-3 px-4 focus:bg-white focus:border-primary-100 focus:ring-4 focus:ring-primary-50 transition-all outline-none">
                        </div>
                        <div class="space-y-2">
                            <label class="text-sm font-bold text-neutral-700 ml-1">Client Onboard Date</label>
                            <input type="date" name="client_onboard_date" class="w-full bg-neutral-50 border-transparent rounded-2xl py-3 px-4 focus:bg-white focus:border-primary-100 focus:ring-4 focus:ring-primary-50 transition-all outline-none">
                        </div>
                        <div class="space-y-2">
                            <label class="text-sm font-bold text-neutral-700 ml-1">Reference By</label>
                            <input type="text" name="reference_by" placeholder="Referral name" class="w-full bg-neutral-50 border-transparent rounded-2xl py-3 px-4 focus:bg-white focus:border-primary-100 focus:ring-4 focus:ring-primary-50 transition-all outline-none">
                        </div>
                        <div class="space-y-2">
                            <label class="text-sm font-bold text-neutral-700 ml-1">Website/Social Link</label>
                            <input type="text" name="website_social_link" placeholder="https://..." class="w-full bg-neutral-50 border-transparent rounded-2xl py-3 px-4 focus:bg-white focus:border-primary-100 focus:ring-4 focus:ring-primary-50 transition-all outline-none">
                        </div>
                        <div class="md:col-span-2 space-y-2">
                            <label class="text-sm font-bold text-neutral-700 ml-1">Remarks/Notes</label>
                            <textarea name="remarks_notes" rows="3" placeholder="Additional notes..." class="w-full bg-neutral-50 border-transparent rounded-2xl py-3 px-4 focus:bg-white focus:border-primary-100 focus:ring-4 focus:ring-primary-50 transition-all outline-none resize-none"></textarea>
                        </div>
                    </div>
                </div>

                <div class="flex justify-end gap-4 mt-10 pt-8 border-t border-neutral-100">
                    <button type="button" onclick="closeLeadModal()" class="px-8 py-3 bg-neutral-100 text-neutral-600 font-bold rounded-2xl hover:bg-neutral-200 transition-all">
                        Cancel
                    </button>
                    <button type="submit" id="saveLeadBtn" class="px-8 py-3 bg-primary-600 text-white font-bold rounded-2xl hover:bg-primary-700 shadow-lg shadow-primary-200 transition-all flex items-center gap-2">
                        <span>Save Lead Details</span>
                        <svg id="loadingIcon" class="w-5 h-5 animate-spin hidden" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    let currentPage = 1;
    let leadsData = [];

    async function loadLeads(page = 1) {
        currentPage = page;
        const search = document.getElementById('search').value;
        const category = document.getElementById('categoryFilter').value;
        const status = document.getElementById('statusFilter').value;

        const params = new URLSearchParams({ page, limit: 10, search, category, status });

        try {
            const response = await fetch(`../api/leads.php?${params}`);
            const data = await response.json();
            leadsData = data.leads;

            const tbody = document.getElementById('leadsTableBody');
            if (leadsData.length === 0) {
                tbody.innerHTML = `<tr><td colspan="6" class="px-8 py-20 text-center text-neutral-400">No leads found. Try adjusting filters.</td></tr>`;
                updatePagination({ total: 0, page: 1, pages: 0, limit: 10 });
                return;
            }

            tbody.innerHTML = leadsData.map(lead => `
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
                        <div class="flex items-center justify-end gap-2">
                            <button onclick="editLead('${lead.id}')" class="p-2 text-neutral-400 hover:text-primary-600 hover:bg-primary-50 rounded-xl transition-all">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
                            </button>
                            <button onclick="deleteLead('${lead.id}')" class="p-2 text-neutral-400 hover:text-red-600 hover:bg-red-50 rounded-xl transition-all">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                            </button>
                        </div>
                    </td>
                </tr>
            `).join('');

            updatePagination(data.pagination);
        } catch (error) {
            console.error('Error:', error);
            showToast('Failed to load leads', 'error');
        }
    }

    async function deleteLead(id) {
        if (!confirm('Are you sure you want to delete this lead? This action cannot be undone.')) return;

        try {
            const response = await fetch(`../api/leads.php?id=${id}`, {
                method: 'DELETE'
            });

            const result = await response.json();

            if (response.ok) {
                showToast(result.message, 'success');
                loadLeads(currentPage);
            } else {
                throw new Error(result.error || 'Failed to delete lead');
            }
        } catch (error) {
            showToast(error.message, 'error');
        }
    }

    function updatePagination(pagination) {
        document.getElementById('paginationInfo').textContent = pagination.total > 0 
            ? `Showing ${((pagination.page - 1) * pagination.limit) + 1} to ${Math.min(pagination.page * pagination.limit, pagination.total)} of ${pagination.total} leads`
            : 'No leads to display';
        
        const container = document.getElementById('pagination');
        container.innerHTML = '';
        
        const maxPages = pagination.pages;
        const current = pagination.page;
        
        let start = Math.max(1, current - 2);
        let end = Math.min(maxPages, start + 4);
        if (end - start < 4) start = Math.max(1, end - 4);

        if (start > 1) {
            addPaginationBtn(1, false);
            if (start > 2) addPaginationEllipsis();
        }

        for (let i = start; i <= end; i++) {
            addPaginationBtn(i, i === current);
        }

        if (end < maxPages) {
            if (end < maxPages - 1) addPaginationEllipsis();
            addPaginationBtn(maxPages, false);
        }
    }

    function addPaginationBtn(page, isActive) {
        const btn = document.createElement('button');
        btn.textContent = page;
        btn.className = `w-10 h-10 rounded-xl font-bold text-sm transition-all ${isActive ? 'bg-primary-600 text-white shadow-lg shadow-primary-200' : 'text-neutral-500 hover:bg-neutral-100'}`;
        btn.onclick = () => loadLeads(page);
        document.getElementById('pagination').appendChild(btn);
    }

    function addPaginationEllipsis() {
        const span = document.createElement('span');
        span.textContent = '...';
        span.className = 'w-10 h-10 flex items-center justify-center text-neutral-400';
        document.getElementById('pagination').appendChild(span);
    }

    function switchTab(tabId) {
        // Update tab buttons
        document.querySelectorAll('.tab-btn').forEach(btn => {
            btn.classList.remove('active', 'text-primary-600', 'border-primary-600');
            btn.classList.add('text-neutral-400', 'border-transparent');
        });
        const activeTab = document.getElementById(`tab-${tabId}`);
        activeTab.classList.add('active', 'text-primary-600', 'border-primary-600');
        activeTab.classList.remove('text-neutral-400', 'border-transparent');

        // Update tab content
        document.querySelectorAll('.tab-content').forEach(content => {
            content.classList.add('hidden');
        });
        document.getElementById(`content-${tabId}`).classList.remove('hidden');
    }

    function openAddLeadModal() {
        document.getElementById('leadForm').reset();
        document.getElementById('lead_id_input').value = '';
        document.getElementById('modalTitle').textContent = 'Add New Lead';
        switchTab('basic');
        
        const modal = document.getElementById('leadModal');
        const content = document.getElementById('modalContent');
        modal.classList.remove('hidden');
        setTimeout(() => {
            content.classList.remove('scale-95', 'opacity-0');
        }, 10);
    }

    function editLead(id) {
        const lead = leadsData.find(l => l.id === id);
        if (!lead) return;

        const form = document.getElementById('leadForm');
        document.getElementById('lead_id_input').value = lead.id;
        document.getElementById('modalTitle').textContent = 'Edit Lead Details';

        // Fill form fields
        for (const key in lead) {
            const input = form.querySelector(`[name="${key}"]`);
            if (input) {
                if (input.type === 'date' && lead[key]) {
                    input.value = lead[key].split(' ')[0];
                } else if (input.type === 'checkbox') {
                    input.checked = !!lead[key];
                } else {
                    input.value = lead[key] || '';
                }
            }
        }

        switchTab('basic');
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

    document.getElementById('leadForm').addEventListener('submit', async function(e) {
        e.preventDefault();
        const saveBtn = document.getElementById('saveLeadBtn');
        const loadingIcon = document.getElementById('loadingIcon');
        const id = document.getElementById('lead_id_input').value;
        
        saveBtn.disabled = true;
        loadingIcon.classList.remove('hidden');

        const formData = new FormData(this);
        const data = Object.fromEntries(formData.entries());
        
        // Convert empty strings to null for optional fields
        for (const key in data) {
            if (data[key] === '') data[key] = null;
        }

        try {
            const url = id ? `../api/leads.php?id=${id}` : '../api/leads.php';
            const method = id ? 'PUT' : 'POST';
            
            const response = await fetch(url, {
                method: method,
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(data)
            });

            const result = await response.json();

            if (response.ok) {
                showToast(result.message, 'success');
                closeLeadModal();
                loadLeads(id ? currentPage : 1);
            } else {
                throw new Error(result.error || 'Something went wrong');
            }
        } catch (error) {
            showToast(error.message, 'error');
        } finally {
            saveBtn.disabled = false;
            loadingIcon.classList.add('hidden');
        }
    });

    async function exportLeads() {
        const search = document.getElementById('search').value;
        const category = document.getElementById('categoryFilter').value;
        const status = document.getElementById('statusFilter').value;
        const params = new URLSearchParams({ search, category, status });

        window.location.href = `../api/export.php?${params}`;
    }

    function showToast(message, type = 'success') {
        // Assuming toast component exists in the layout
        if (typeof window.toast === 'function') {
            window.toast(message, type);
        } else {
            alert(message);
        }
    }

    document.addEventListener('DOMContentLoaded', () => loadLeads());
</script>


<?php layout_end(); ?>