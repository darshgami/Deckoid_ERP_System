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
</div>

<!-- Filters Bar -->
<div class="bg-white p-4 lg:p-5 rounded-xl shadow-sm border border-neutral-100 mb-6">
    <div class="flex flex-col md:flex-row gap-4">
        <div class="relative group flex-1">
            <span class="absolute inset-y-0 left-4 flex items-center text-neutral-400 group-focus-within:text-primary">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
            </span>
            <input type="text" id="search" placeholder="Search by name, contact..." autocomplete="off"
                   class="w-full bg-neutral-50 border-transparent rounded-xl py-2.5 pl-11 pr-4 focus:bg-white focus:border-primary/20 focus:ring-4 focus:ring-primary/5 transition-all outline-none text-sm"
                   oninput="debouncedSearchFilter(this.value)">
        </div>
        <div class="bg-neutral-50 p-1 rounded-xl shadow-sm border border-transparent flex shrink-0">
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
                    <th class="px-6 py-3 text-left text-[11px] font-bold text-neutral-500 uppercase tracking-wider whitespace-nowrap">Company</th>
                    <th class="px-6 py-3 text-left text-[11px] font-bold text-neutral-500 uppercase tracking-wider whitespace-nowrap">Followup Date</th>
                    <th class="px-6 py-3 text-left text-[11px] font-bold text-neutral-500 uppercase tracking-wider whitespace-nowrap">Assigned To</th>
                    <th class="px-6 py-3 text-left text-[11px] font-bold text-neutral-500 uppercase tracking-wider whitespace-nowrap">Remarks</th>
                    <th class="px-6 py-3 text-left text-[11px] font-bold text-neutral-500 uppercase tracking-wider whitespace-nowrap">lead_status</th>
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
        <div class="flex gap-1.5" id="pagination">
            <!-- Pagination buttons will be loaded here -->
        </div>
</div>

<!-- Action Modals -->
<!-- Next Follow Up Modal -->
<div id="nextFollowUpModal" class="fixed inset-0 bg-neutral-900/40 backdrop-blur-sm hidden z-[100] transition-all duration-300 overflow-y-auto">
    <div class="flex items-center justify-center min-h-screen p-4">
        <form id="nextFollowUpForm" class="bg-white rounded-xl w-full max-w-md shadow-2xl p-6" onsubmit="handleActionSubmit(event)" autocomplete="off">
            <input type="hidden" id="action_lead_id_next">
            <h3 class="text-lg font-semibold text-neutral-900 mb-4">Schedule Next Follow Up</h3>
            <div class="space-y-4">
                <div class="space-y-1.5">
                    <label class="text-[11px] font-semibold text-neutral-700 ml-1 uppercase tracking-wider">Lead Name</label>
                    <input type="text" id="action_lead_name_next" readonly class="w-full bg-neutral-100 border-transparent rounded-xl py-2.5 px-4 text-neutral-500 transition-all outline-none text-sm cursor-not-allowed">
                </div>
                <div class="space-y-1.5">
                    <label class="text-[11px] font-semibold text-neutral-700 ml-1 uppercase tracking-wider">Next Follow-up Date *</label>
                    <input type="date" id="action_next_date" required class="w-full bg-neutral-100 border-transparent rounded-xl py-2.5 px-4 text-neutral-500 transition-all outline-none text-sm">
                </div>
                <div class="space-y-1.5">
                    <label class="text-[11px] font-semibold text-neutral-700 ml-1 uppercase tracking-wider">Remarks *</label>
                    <textarea id="action_remarks_next" required rows="3" class="w-full bg-neutral-100 border-transparent rounded-xl py-2.5 px-4 text-neutral-500 transition-all outline-none text-sm"></textarea>
                </div>
            </div>
            <div class="flex justify-end gap-3 mt-6">
                <button type="button" onclick="closeModals()" class="px-4 py-2 bg-neutral-100 text-neutral-600 font-semibold rounded-lg text-sm hover:bg-neutral-200">Cancel</button>
                <button type="submit" class="px-4 py-2 bg-primary text-white font-semibold rounded-lg text-sm shadow-md hover:bg-primary-600 flex items-center gap-2">
                    <span id="nextFollowUpBtnText">Save</span>
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Convert Modal -->
<div id="convertModal" class="fixed inset-0 bg-neutral-900/40 backdrop-blur-sm hidden z-[100] transition-all duration-300 overflow-y-auto">
    <div class="flex items-center justify-center min-h-screen p-4">
        <form id="convertForm" class="bg-white rounded-xl w-full max-w-md shadow-2xl p-6" onsubmit="handleActionSubmit(event, 'Convert')" autocomplete="off">
            <input type="hidden" id="action_lead_id_convert">
            <h3 class="text-lg font-semibold text-neutral-900 mb-4">Convert Lead to Project</h3>
            <div class="space-y-4">
                <div class="space-y-1.5">
                    <label class="text-[11px] font-semibold text-neutral-700 ml-1 uppercase tracking-wider">Company *</label>
                    <input type="text" id="action_client_name_convert" required class="w-full bg-neutral-100 border-transparent rounded-xl py-2.5 px-4 text-neutral-500 transition-all outline-none text-sm">
                </div>
                <div class="space-y-1.5">
                    <label class="text-[11px] font-semibold text-neutral-700 ml-1 uppercase tracking-wider">Add Work *</label>
                    <input type="text" id="action_add_work_convert" required class="w-full bg-neutral-100 border-transparent rounded-xl py-2.5 px-4 text-neutral-500 transition-all outline-none text-sm">
                </div>
                <div class="space-y-1.5">
                    <label class="text-[11px] font-semibold text-neutral-700 ml-1 uppercase tracking-wider">Conversion Date *</label>
                    <input type="date" id="action_conversion_date_convert" required class="w-20px bg-neutral-100 border-transparent rounded-xl py-2.5 px-4 text-neutral-500 transition-all outline-none text-sm">
                </div>
            </div>
            <div class="flex justify-end gap-3 mt-6">
                <button type="button" onclick="closeModals()" class="px-4 py-2 bg-neutral-100 text-neutral-600 font-semibold rounded-lg text-sm hover:bg-neutral-200">Cancel</button>
                <button type="submit" class="px-4 py-2 bg-green-600 text-white font-semibold rounded-lg text-sm shadow-md hover:bg-green-700">Convert</button>
            </div>
        </form>
    </div>
</div>

<!-- View Modal -->
<div id="viewModal" class="fixed inset-0 bg-neutral-900/40 backdrop-blur-sm hidden z-[100] transition-all duration-300 overflow-y-auto">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="bg-white rounded-xl w-full max-w-2xl shadow-2xl p-6">
            <h3 class="text-lg font-semibold text-neutral-900 mb-4">Lead Details</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="space-y-1">
                    <label class="text-[10px] font-semibold text-neutral-500 uppercase tracking-wider">Company</label>
                    <p id="view_company" class="text-sm font-medium text-neutral-900"></p>
                </div>
                <div class="space-y-1">
                    <label class="text-[10px] font-semibold text-neutral-500 uppercase tracking-wider">Contact Person</label>
                    <p id="view_contact" class="text-sm font-medium text-neutral-900"></p>
                </div>
                <div class="space-y-1">
                    <label class="text-[10px] font-semibold text-neutral-500 uppercase tracking-wider">Mobile Number</label>
                    <p id="view_mobile" class="text-sm font-medium text-neutral-900"></p>
                </div>
                <div class="space-y-1">
                    <label class="text-[10px] font-semibold text-neutral-500 uppercase tracking-wider">Email ID</label>
                    <p id="view_email" class="text-sm font-medium text-neutral-900"></p>
                </div>
                <div class="space-y-1">
                    <label class="text-[10px] font-semibold text-neutral-500 uppercase tracking-wider">Lead Category</label>
                    <p id="view_category" class="text-sm font-medium text-neutral-900"></p>
                </div>
                <div class="space-y-1">
                    <label class="text-[10px] font-semibold text-neutral-500 uppercase tracking-wider">lead_status</label>
                    <p id="view_status" class="text-sm font-medium text-neutral-900"></p>
                </div>
                <div class="space-y-1">
                    <label class="text-[10px] font-semibold text-neutral-500 uppercase tracking-wider">Next Follow-up</label>
                    <p id="view_next_followup" class="text-sm font-medium text-neutral-900"></p>
                </div>
                <div class="space-y-1 md:col-span-2">
                    <label class="text-[10px] font-semibold text-neutral-500 uppercase tracking-wider">Remarks</label>
                    <p id="view_remarks" class="text-sm text-neutral-600 bg-neutral-50 p-3 rounded-lg min-h-[3rem]"></p>
                </div>
            </div>
            <div class="flex justify-end gap-3 mt-6 pt-4 border-t border-neutral-100">
                <button type="button" onclick="closeModals()" class="px-4 py-2 bg-neutral-100 text-neutral-600 font-semibold rounded-lg text-sm hover:bg-neutral-200">Close</button>
            </div>
        </div>
    </div>
</div>

<script>
    const currentRole = '<?= $_SESSION['role'] ?>';
    let currentLeadsData = [];
    let currentPage = 1;
    let currentLimit = 10;
    let currentFilter = 'all';
    let currentSearch = '';

    let searchTimeout;
    function debouncedSearchFilter(val) {
        currentSearch = val;
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(() => {
            loadFollowups(1);
        }, 300);
    }

    function updateLimit(limit) {
        currentLimit = limit;
        loadFollowups(1);
    }

    async function loadFollowups(page = 1) {
        currentPage = page;
        const tbody = document.getElementById('followupsBody');
        tbody.innerHTML = `
            <tr>
                <td colspan="6" class="px-6 py-12 text-center">
                    <div class="flex flex-col items-center gap-2 text-neutral-400">
                        <div class="w-8 h-8 border-2 border-primary-light border-t-primary rounded-full animate-spin"></div>
                        <span class="font-semibold text-[11px] uppercase tracking-wider">Loading Followups...</span>
                    </div>
                </td>
            </tr>
        `;

        try {
            // Fetch from the new followups API endpoint
            const response = await fetch(`../api/followups.php?page=${page}&limit=${currentLimit}&followup_filter=${currentFilter}&search=${currentSearch}`);
            const res = await response.json();

            if (!res.success) throw new Error(res.message);

            const data = res.data;
            currentLeadsData = data.leads || [];

            if (!data.leads || data.leads.length === 0) {
                tbody.innerHTML = `<tr><td colspan="6" class="px-6 py-12 text-center text-neutral-400 text-sm font-medium">No followups scheduled.</td></tr>`;
                return;
            }

            tbody.innerHTML = data.leads.map(lead => `
                <tr class="hover:bg-neutral-50/50 transition-all group">
                    <td class="px-6 py-4">
                        <div class="flex flex-col">
                            <span class="text-sm font-semibold text-neutral-900 group-hover:text-primary transition-colors">${lead.company}</span>
                            <span class="text-[11px] text-neutral-400 font-medium">${lead.contact_person}</span>
                        </div>
                    </td>
                    <td class="px-6 py-4">
                        <div class="flex items-center gap-2.5">
                            <div class="w-8 h-8 bg-orange-50 rounded-lg flex items-center justify-center text-orange-600">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                            </div>
                            <span class="text-sm font-semibold text-neutral-700">${formatDate(lead.followup_date)}</span>
                        </div>
                    </td>
                    <td class="px-6 py-4">
                        <span class="text-sm text-neutral-700">${lead.assigned_to_name || 'Unassigned'}</span>
                    </td>
                    <td class="px-6 py-4">
                        <p class="text-xs text-neutral-500 max-w-xs truncate font-medium">${lead.remarks}</p>
                    </td>
                    <td class="px-6 py-4">
                        <span class="px-2 py-0.5 text-[11px] font-semibold rounded-md uppercase tracking-wider bg-blue-50 text-blue-600">Active</span>
                    </td>
                    <td class="px-6 py-4 text-right">
                        <div class="flex items-center justify-end gap-2">
                            <button onclick="openActionModal('Next Follow Up', '${lead.lead_id}')" title="Next Follow Up" class="p-1.5 bg-blue-50 text-blue-600 hover:bg-blue-100 rounded-lg transition-all border border-blue-100">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                            </button>
                            <button onclick="openActionModal('Convert', '${lead.lead_id}')" title="Convert to Project" class="p-1.5 bg-green-50 text-green-600 hover:bg-green-100 rounded-lg transition-all border border-green-100">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            </button>
                            <button onclick="performAction('${lead.lead_id}', 'Lost')" title="Mark as Lost" class="p-1.5 bg-red-50 text-red-600 hover:bg-red-100 rounded-lg transition-all border border-red-100">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            </button>
                            <button onclick="openEditFollowupModal('${lead.id}')" title="Edit Follow-up" class="p-1.5 bg-amber-50 text-amber-600 hover:bg-amber-100 rounded-lg transition-all border border-amber-100 ml-1">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                            </button>
                        </div>
                    </td>
                </tr>
            `).join('');

            updatePaginationInfo(data.pagination, 'paginationInfo', 'followups');
            renderPagination(data.pagination, loadFollowups);
        } catch (error) {
            console.error('Error:', error);
            tbody.innerHTML = `<tr><td colspan="6" class="px-6 py-12 text-center text-red-500 text-sm font-semibold">${error.message}</td></tr>`;
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

    function openActionModal(action, leadId) {
        // Because leadId passed here is now lead.lead_id (from the leads table), we need to find the lead using lead_id
        const lead = currentLeadsData.find(l => l.lead_id === leadId);
        if (action === 'Next Follow Up') {
            document.getElementById('action_lead_id_next').value = leadId;
            document.getElementById('nextFollowUpForm').reset();
            document.getElementById('nextFollowUpForm').dataset.mode = 'schedule';
            document.getElementById('nextFollowUpBtnText').textContent = 'Save';
            document.getElementById('action_lead_name_next').value = lead ? lead.company : '';
            if (lead && lead.followup_date) {
                document.getElementById('action_next_date').value = lead.followup_date;
            }
            document.getElementById('nextFollowUpModal').classList.remove('hidden');
        } else if (action === 'Convert') {
            document.getElementById('action_lead_id_convert').value = leadId;
            document.getElementById('convertForm').reset();
            document.getElementById('action_client_name_convert').value = lead ? lead.company : '';
            document.getElementById('convertModal').classList.remove('hidden');
        }
    }

    function openEditFollowupModal(followupId) {
        const followup = currentLeadsData.find(item => item.id === followupId);
        if (!followup) return;

        document.getElementById('action_lead_id_next').value = followup.lead_id;
        document.getElementById('nextFollowUpForm').reset();
        document.getElementById('action_lead_name_next').value = followup.company || '';
        document.getElementById('action_next_date').value = followup.followup_date || '';
        document.getElementById('action_remarks_next').value = followup.remarks || '';
        document.getElementById('nextFollowUpBtnText').textContent = 'Update';
        document.getElementById('nextFollowUpForm').dataset.mode = 'edit';
        document.getElementById('nextFollowUpForm').dataset.followupId = followupId;
        document.getElementById('nextFollowUpModal').classList.remove('hidden');
    }

    function closeModals() {
        document.getElementById('nextFollowUpModal').classList.add('hidden');
        document.getElementById('convertModal').classList.add('hidden');
        document.getElementById('viewModal').classList.add('hidden');
    }

    async function updateFollowup(followupId, extraData = {}) {
        try {
            const response = await fetch(`../api/followups.php?id=${followupId}`, {
                method: 'PUT',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(extraData)
            });
            const res = await response.json();
            if (res.success) {
                if (window.toast) toast('Follow-up updated successfully.', 'success');
                else alert('Follow-up updated successfully.');
                closeModals();
                loadFollowups(currentPage);
            } else {
                if (window.toast) toast(res.message, 'error');
                else alert(res.message);
            }
        } catch (e) {
            console.error(e);
            alert('Error updating follow-up.');
        }
    }

    async function performAction(leadId, actionType, extraData = {}) {
        if (actionType === 'Lost' && !confirm('Are you sure you want to mark this lead as Lost?')) return;
        
        try {
            const data = { lead_status: actionType, ...extraData };
            const response = await fetch(`../api/leads.php?id=${leadId}`, {
                method: 'PUT',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(data)
            });
            const res = await response.json();
            if (res.success) {
                if (window.toast) toast('Followup updated successfully.', 'success');
                else alert('Followup updated successfully.');
                closeModals();
                loadFollowups(currentPage);
            } else {
                if (window.toast) toast(res.message, 'error');
                else alert(res.message);
            }
        } catch (e) {
            console.error(e);
            alert('Error updating followup.');
        }
    }

    function handleActionSubmit(e, actionType = null) {
        e.preventDefault();
        const mode = actionType || document.getElementById('nextFollowUpForm').dataset.mode || 'schedule';
        let leadId, extraData = {};

        if (mode === 'schedule') {
            leadId = document.getElementById('action_lead_id_next').value;
            extraData.next_followup_date = document.getElementById('action_next_date').value;
            extraData.remarks = document.getElementById('action_remarks_next').value;
            performAction(leadId, 'Next Follow Up', extraData);
            return;
        }

        if (mode === 'edit') {
            const followupId = document.getElementById('nextFollowUpForm').dataset.followupId;
            extraData.followup_date = document.getElementById('action_next_date').value;
            extraData.remarks = document.getElementById('action_remarks_next').value;
            extraData.status = 'Active';
            updateFollowup(followupId, extraData);
            return;
        }

        if (mode === 'Convert') {
            leadId = document.getElementById('action_lead_id_convert').value;
            extraData.company = document.getElementById('action_client_name_convert').value;
            extraData.add_work = document.getElementById('action_add_work_convert').value;
            extraData.onboarding_date = document.getElementById('action_conversion_date_convert').value;
            performAction(leadId, actionType, extraData);
        }
    }

    document.addEventListener('DOMContentLoaded', () => {
        // Move modals to body to prevent layout container clipping/positioning bugs
        document.body.appendChild(document.getElementById('nextFollowUpModal'));
        document.body.appendChild(document.getElementById('convertModal'));
        document.body.appendChild(document.getElementById('viewModal'));
        
        loadFollowups(1);
    });
</script>

<?php layout_end(); ?>
