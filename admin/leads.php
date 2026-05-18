<?php
require_once '../includes/middleware.php';
require_once '../includes/components/layout_wrapper.php';
requireAuth();

layout_start('Lead Management - Deckoid ERP');

// Fetch users for 'Assigned To' dropdown
require_once '../config/env.php';
require_once '../includes/database.php';
$db = Database::getInstance();
$usersStmt = $db->query("SELECT id, full_name FROM users ORDER BY full_name ASC");
$users = $usersStmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="mb-6 flex flex-col md:flex-row md:items-center justify-between gap-4">
    <div>
        <h1 class="text-xl lg:text-2xl font-semibold text-neutral-900">Lead Management</h1>
        <p class="text-neutral-500 text-sm mt-0.5">Manage and track your sales opportunities efficiently.</p>
    </div>
    <div class="flex items-center gap-2">
        <a href="quick_list.php" class="btn btn-primary text-sm">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M4 6h16M4 10h16M4 14h16M4 18h16" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path></svg>
            Quick List
        </a>
        <?php if ($_SESSION['role'] === 'admin'): ?>
        <button onclick="exportLeads()" class="btn btn-secondary text-sm">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
            Export Excel
        </button>
        <?php endif; ?>
    </div>
</div>

<!-- Filters Bar -->
<div class="bg-white p-4 lg:p-5 rounded-xl shadow-sm border border-neutral-100 mb-6">
    <div class="grid grid-cols-1 md:grid-cols-4 lg:grid-cols-4 gap-4">
        <div class="relative group">
            <span class="absolute inset-y-0 left-4 flex items-center text-neutral-400 group-focus-within:text-primary">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
            </span>
            <input type="text" id="search" placeholder="Search by name, company..." 
                   class="w-full bg-neutral-50 border-transparent rounded-xl py-2.5 pl-11 pr-4 focus:bg-white focus:border-primary/20 focus:ring-4 focus:ring-primary/5 transition-all outline-none text-sm">
        </div>
        <select id="statusFilter" class="w-full bg-neutral-50 border-transparent rounded-xl py-2.5 px-4 focus:bg-white focus:border-primary/20 focus:ring-4 focus:ring-primary/5 transition-all outline-none text-sm cursor-pointer">
            <option value="">All Status</option>
            <option value="New">New</option>
            <option value="Interested">Interested</option>
            <option value="Follow-up">Follow-up</option>
            <option value="Meeting Done">Meeting Done</option>
            <option value="Proposal Sent">Proposal Sent</option>
            <option value="Converted">Converted</option>
            <option value="Not Interested">Not Interested</option>
            <option value="Lost">Lost</option>
        </select>
        <select id="serviceFilter" class="w-full bg-neutral-50 border-transparent rounded-xl py-2.5 px-4 focus:bg-white focus:border-primary/20 focus:ring-4 focus:ring-primary/5 transition-all outline-none text-sm cursor-pointer">
            <option value="">All Services</option>
            <option value="Facebook Ads">Facebook Ads</option>
            <option value="Google Ads">Google Ads</option>
            <option value="Website Design & Development">Website Design & Development</option>
            <option value="Graphics Design">Graphics Design</option>
            <option value="Search Engine Optimization">Search Engine Optimization</option>
            <option value="Video Editing">Video Editing</option>
            <option value="Social Media Management">Social Media Management</option>
            <option value="AI Video Making">AI Video Making</option>
        </select>
        <button onclick="loadLeads(1)" class="btn btn-primary text-sm">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"></path></svg>
            Apply Filters
        </button>
    </div>
</div>

<!-- Table Section -->
<div class="bg-white rounded-xl shadow-sm border border-neutral-100 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead>
                <tr class="bg-neutral-50/50">
                    <th class="px-4 py-3 text-left text-[11px] font-medium text-neutral-500 uppercase tracking-wider whitespace-nowrap">Lead ID</th>
                    <th class="px-4 py-3 text-left text-[11px] font-medium text-neutral-500 uppercase tracking-wider whitespace-nowrap">Lead Date</th>
                    <th class="px-4 py-3 text-left text-[11px] font-medium text-neutral-500 uppercase tracking-wider whitespace-nowrap">Company / Client Name</th>
                    <th class="px-4 py-3 text-left text-[11px] font-medium text-neutral-500 uppercase tracking-wider whitespace-nowrap">Contact Person</th>
                    <th class="px-4 py-3 text-left text-[11px] font-medium text-neutral-500 uppercase tracking-wider whitespace-nowrap">Mobile Number</th>
                    <th class="px-4 py-3 text-left text-[11px] font-medium text-neutral-500 uppercase tracking-wider whitespace-nowrap">Alternative Number</th>
                    <th class="px-4 py-3 text-left text-[11px] font-medium text-neutral-500 uppercase tracking-wider whitespace-nowrap">Email ID</th>
                    <th class="px-4 py-3 text-left text-[11px] font-medium text-neutral-500 uppercase tracking-wider whitespace-nowrap">City</th>
                    <th class="px-4 py-3 text-left text-[11px] font-medium text-neutral-500 uppercase tracking-wider whitespace-nowrap">State</th>
                    <th class="px-4 py-3 text-left text-[11px] font-medium text-neutral-500 uppercase tracking-wider whitespace-nowrap">Source of Lead</th>
                    <th class="px-4 py-3 text-left text-[11px] font-medium text-neutral-500 uppercase tracking-wider whitespace-nowrap">Service Interested In</th>
                    <th class="px-4 py-3 text-left text-[11px] font-medium text-neutral-500 uppercase tracking-wider whitespace-nowrap">Lead Category</th>
                    <th class="px-4 py-3 text-left text-[11px] font-medium text-neutral-500 uppercase tracking-wider whitespace-nowrap">Lead Status</th>
                    <th class="px-4 py-3 text-left text-[11px] font-medium text-neutral-500 uppercase tracking-wider whitespace-nowrap">Priority</th>
                    <th class="px-4 py-3 text-left text-[11px] font-medium text-neutral-500 uppercase tracking-wider whitespace-nowrap">Assigned To</th>
                    <th class="px-4 py-3 text-left text-[11px] font-medium text-neutral-500 uppercase tracking-wider whitespace-nowrap">Next Follow-up Date</th>
                    <th class="px-4 py-3 text-left text-[11px] font-medium text-neutral-500 uppercase tracking-wider whitespace-nowrap">Last Follow-up Notes</th>
                    <th class="px-4 py-3 text-left text-[11px] font-medium text-neutral-500 uppercase tracking-wider whitespace-nowrap">Requirement Details</th>
                    <th class="px-4 py-3 text-left text-[11px] font-medium text-neutral-500 uppercase tracking-wider whitespace-nowrap">Estimated Budget</th>
                    <th class="px-4 py-3 text-left text-[11px] font-medium text-neutral-500 uppercase tracking-wider whitespace-nowrap">Proposal Sent</th>
                    <th class="px-4 py-3 text-left text-[11px] font-medium text-neutral-500 uppercase tracking-wider whitespace-nowrap">Meeting Scheduled</th>
                    <th class="px-4 py-3 text-left text-[11px] font-medium text-neutral-500 uppercase tracking-wider whitespace-nowrap">Quotation Sent</th>
                    <th class="px-4 py-3 text-left text-[11px] font-medium text-neutral-500 uppercase tracking-wider whitespace-nowrap">Deal Status</th>
                    <th class="px-4 py-3 text-left text-[11px] font-medium text-neutral-500 uppercase tracking-wider whitespace-nowrap">Expected Closing Date</th>
                    <th class="px-4 py-3 text-left text-[11px] font-medium text-neutral-500 uppercase tracking-wider whitespace-nowrap">Payment Status</th>
                    <th class="px-4 py-3 text-left text-[11px] font-medium text-neutral-500 uppercase tracking-wider whitespace-nowrap">Client Onboard Date</th>
                    <th class="px-4 py-3 text-left text-[11px] font-medium text-neutral-500 uppercase tracking-wider whitespace-nowrap">Project Start Date</th>
                    <th class="px-4 py-3 text-left text-[11px] font-medium text-neutral-500 uppercase tracking-wider whitespace-nowrap">Project Status</th>
                    <th class="px-4 py-3 text-left text-[11px] font-medium text-neutral-500 uppercase tracking-wider whitespace-nowrap">Reference By</th>
                    <th class="px-4 py-3 text-left text-[11px] font-medium text-neutral-500 uppercase tracking-wider whitespace-nowrap">Website / Social Link</th>
                    <th class="px-4 py-3 text-left text-[11px] font-medium text-neutral-500 uppercase tracking-wider whitespace-nowrap">Remarks / Notes</th>
                    <th class="px-4 py-3 text-right text-[11px] font-medium text-neutral-500 uppercase tracking-wider whitespace-nowrap">Actions</th>
                </tr>
            </thead>
            <tbody id="leadsTableBody" class="divide-y divide-neutral-50">
                <!-- Loaded via JS -->
            </tbody>
        </table>
    </div>
    
    <!-- Pagination -->
    <div class="px-6 py-4 border-t border-neutral-50 flex items-center justify-between bg-neutral-50/30">
        <div class="flex items-center gap-4">
            <p id="paginationInfo" class="text-xs text-neutral-500 font-medium"></p>
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
        <div id="pagination" class="flex items-center gap-1.5">
            <!-- Buttons via JS -->
        </div>
    </div>
</div>

<!-- Modal Container -->
<div id="leadModal" class="fixed inset-0 bg-neutral-900/40 backdrop-blur-sm hidden z-[100] transition-all duration-300">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="bg-white rounded-xl max-w-5xl w-full shadow-2xl overflow-hidden transform transition-all duration-300 scale-95 opacity-0" id="modalContent">
            <div class="px-6 py-4 lg:px-8 lg:py-5 border-b border-neutral-100 flex items-center justify-between bg-white">
                <div>
                    <h3 id="modalTitle" class="text-lg font-semibold text-neutral-900 tracking-tight">Add New Lead</h3>
                    <p class="text-xs text-neutral-500 mt-0.5">Please fill in all the required details below.</p>
                </div>
                <button onclick="closeLeadModal()" class="p-2 text-neutral-400 hover:text-neutral-900 hover:bg-neutral-50 rounded-lg transition-all">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>
            
            <!-- Modal Tabs -->
            <div class="flex border-b border-neutral-100 px-6 bg-white overflow-x-auto">
                <button onclick="switchTab('basic')" class="tab-btn active px-4 py-3 text-xs font-semibold border-b-2 border-primary text-primary whitespace-nowrap" id="tab-basic">Basic Info</button>
                <button onclick="switchTab('details')" class="tab-btn px-4 py-3 text-xs font-semibold border-b-2 border-transparent text-neutral-400 hover:text-neutral-600 whitespace-nowrap" id="tab-details">Location & Details</button>
                <button onclick="switchTab('sales')" class="tab-btn px-4 py-3 text-xs font-semibold border-b-2 border-transparent text-neutral-400 hover:text-neutral-600 whitespace-nowrap" id="tab-sales">Sales Tracking</button>
                <button onclick="switchTab('project')" class="tab-btn px-4 py-3 text-xs font-semibold border-b-2 border-transparent text-neutral-400 hover:text-neutral-600 whitespace-nowrap" id="tab-project">Project & Others</button>
            </div>

            <form id="leadForm" class="p-6 lg:p-8 max-h-[70vh] overflow-y-auto" novalidate>
                <input type="hidden" name="id" id="lead_id_input">
                
                <!-- Basic Info Tab -->
                <div id="content-basic" class="tab-content space-y-5">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
                        <div class="space-y-1.5">
                            <label class="text-[11px] font-semibold text-neutral-700 ml-1 uppercase tracking-wider">Lead ID</label>
                            <input type="text" id="display_lead_id" readonly placeholder="DKXXXX" class="w-full bg-neutral-100 border-transparent rounded-xl py-2.5 px-4 text-neutral-500 transition-all outline-none text-sm font-semibold cursor-not-allowed">
                        </div>
                        <div class="space-y-1.5">
                            <label class="text-[11px] font-semibold text-neutral-700 ml-1 uppercase tracking-wider">Lead Date *</label>
                            <input type="date" name="lead_date" required class="w-full bg-neutral-50 border-transparent rounded-xl py-2.5 px-4 focus:bg-white focus:border-primary-100 focus:ring-4 focus:ring-primary-50 transition-all outline-none text-sm">
                        </div>
                        <div class="space-y-1.5">
                            <label class="text-[11px] font-semibold text-neutral-700 ml-1 uppercase tracking-wider">Company/Client Name *</label>
                            <input type="text" name="company_client_name" required placeholder="e.g. Acme Corp" class="w-full bg-neutral-50 border-transparent rounded-xl py-2.5 px-4 focus:bg-white focus:border-primary-100 focus:ring-4 focus:ring-primary-50 transition-all outline-none text-sm">
                        </div>
                        <div class="space-y-1.5">
                            <label class="text-[11px] font-semibold text-neutral-700 ml-1 uppercase tracking-wider">Contact Person *</label>
                            <input type="text" name="contact_person" required placeholder="Full Name" class="w-full bg-neutral-50 border-transparent rounded-xl py-2.5 px-4 focus:bg-white focus:border-primary-100 focus:ring-4 focus:ring-primary-50 transition-all outline-none text-sm">
                        </div>
                        <div class="space-y-1.5">
                            <label class="text-[11px] font-semibold text-neutral-700 ml-1 uppercase tracking-wider">Mobile Number *</label>
                            <input type="text" name="mobile_number" required placeholder="+1 234 567 890" class="w-full bg-neutral-50 border-transparent rounded-xl py-2.5 px-4 focus:bg-white focus:border-primary-100 focus:ring-4 focus:ring-primary-50 transition-all outline-none text-sm">
                        </div>
                        <div class="space-y-1.5">
                            <label class="text-[11px] font-semibold text-neutral-700 ml-1 uppercase tracking-wider">Email ID</label>
                            <input type="email" name="email_id" placeholder="email@example.com" class="w-full bg-neutral-50 border-transparent rounded-xl py-2.5 px-4 focus:bg-white focus:border-primary-100 focus:ring-4 focus:ring-primary-50 transition-all outline-none text-sm">
                        </div>
                        <div class="space-y-1.5">
                            <label class="text-[11px] font-semibold text-neutral-700 ml-1 uppercase tracking-wider">Source of Lead *</label>
                            <select name="source_of_lead" required class="w-full bg-neutral-50 border-transparent rounded-xl py-2.5 px-4 focus:bg-white focus:border-primary-100 focus:ring-4 focus:ring-primary-50 transition-all outline-none cursor-pointer text-sm">
                                <option value="IndiaMART">IndiaMART</option>
                                <option value="Facebook">Facebook</option>
                                <option value="Instagram">Instagram</option>
                                <option value="Google">Google</option>
                                <option value="Reference">Reference</option>
                                <option value="WhatsApp">WhatsApp</option>
                                <option value="Website">Website</option>
                                <option value="LinkedIn">LinkedIn</option>
                                <option value="Cold Calling">Cold Calling</option>
                                <option value="Other">Other</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Location & Details Tab -->
                <div id="content-details" class="tab-content hidden space-y-5">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
                        <div class="space-y-1.5">
                            <label class="text-[11px] font-bold text-neutral-700 ml-1 uppercase tracking-wider">Alternative Number</label>
                            <input type="text" name="alternative_number" placeholder="Other contact number" class="w-full bg-neutral-50 border-transparent rounded-xl py-2.5 px-4 focus:bg-white focus:border-primary-100 focus:ring-4 focus:ring-primary-50 transition-all outline-none text-sm">
                        </div>
                        <div class="space-y-1.5">
                            <label class="text-[11px] font-bold text-neutral-700 ml-1 uppercase tracking-wider">City</label>
                            <input type="text" name="city" placeholder="City name" class="w-full bg-neutral-50 border-transparent rounded-xl py-2.5 px-4 focus:bg-white focus:border-primary-100 focus:ring-4 focus:ring-primary-50 transition-all outline-none text-sm">
                        </div>
                        <div class="space-y-1.5">
                            <label class="text-[11px] font-bold text-neutral-700 ml-1 uppercase tracking-wider">State</label>
                            <input type="text" name="state" placeholder="State name" class="w-full bg-neutral-50 border-transparent rounded-xl py-2.5 px-4 focus:bg-white focus:border-primary-100 focus:ring-4 focus:ring-primary-50 transition-all outline-none text-sm">
                        </div>
                        <div class="space-y-1.5">
                            <label class="text-[11px] font-bold text-neutral-700 ml-1 uppercase tracking-wider">Country</label>
                            <input type="text" name="country" placeholder="Country" value="India" class="w-full bg-neutral-50 border-transparent rounded-xl py-2.5 px-4 focus:bg-white focus:border-primary-100 focus:ring-4 focus:ring-primary-50 transition-all outline-none text-sm">
                        </div>
                        <div class="space-y-1.5">
                            <label class="text-[11px] font-bold text-neutral-700 ml-1 uppercase tracking-wider">Zip Code</label>
                            <input type="text" name="zip_code" placeholder="Postal Code" class="w-full bg-neutral-50 border-transparent rounded-xl py-2.5 px-4 focus:bg-white focus:border-primary-100 focus:ring-4 focus:ring-primary-50 transition-all outline-none text-sm">
                        </div>
                        <div class="space-y-1.5">
                            <label class="text-[11px] font-bold text-neutral-700 ml-1 uppercase tracking-wider">Service Interested In *</label>
                            <select name="service_interested_in" required class="w-full bg-neutral-50 border-transparent rounded-xl py-2.5 px-4 focus:bg-white focus:border-primary-100 focus:ring-4 focus:ring-primary-50 transition-all outline-none cursor-pointer text-sm">
                                <option value="Facebook Ads">Facebook Ads</option>
                                <option value="Google Ads">Google Ads</option>
                                <option value="Website Design & Development">Website Design & Development</option>
                                <option value="Graphics Design">Graphics Design</option>
                                <option value="Search Engine Optimization">Search Engine Optimization</option>
                                <option value="Video Editing">Video Editing</option>
                                <option value="Social Media Management">Social Media Management</option>
                                <option value="AI Video Making">AI Video Making</option>
                            </select>
                        </div>
                        <div class="md:col-span-3 space-y-1.5">
                            <label class="text-[11px] font-bold text-neutral-700 ml-1 uppercase tracking-wider">Requirement Details</label>
                            <textarea name="requirement_details" rows="2" placeholder="Specific requirements..." class="w-full bg-neutral-50 border-transparent rounded-xl py-2.5 px-4 focus:bg-white focus:border-primary-100 focus:ring-4 focus:ring-primary-50 transition-all outline-none resize-none text-sm"></textarea>
                        </div>
                    </div>
                </div>

                <!-- Sales Tracking Tab -->
                <div id="content-sales" class="tab-content hidden space-y-5">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
                        <div class="space-y-1.5">
                            <label class="text-[11px] font-bold text-neutral-700 ml-1 uppercase tracking-wider">Lead Category *</label>
                            <select name="lead_category" required class="w-full bg-neutral-50 border-transparent rounded-xl py-2.5 px-4 focus:bg-white focus:border-primary-100 focus:ring-4 focus:ring-primary-50 transition-all outline-none cursor-pointer text-sm">
                                <option value="Hot">🔥 Hot</option>
                                <option value="Warm">☀️ Warm</option>
                                <option value="Cold">❄️ Cold</option>
                            </select>
                        </div>
                        <div class="space-y-1.5">
                            <label class="text-[11px] font-bold text-neutral-700 ml-1 uppercase tracking-wider">Lead Status *</label>
                            <select name="lead_status" required class="w-full bg-neutral-50 border-transparent rounded-xl py-2.5 px-4 focus:bg-white focus:border-primary-100 focus:ring-4 focus:ring-primary-50 transition-all outline-none cursor-pointer text-sm">
                                <option value="New">New</option>
                                <option value="Interested">Interested</option>
                                <option value="Follow-up">Follow-up</option>
                                <option value="Meeting Done">Meeting Done</option>
                                <option value="Proposal Sent">Proposal Sent</option>
                                <option value="Converted">Converted</option>
                                <option value="Not Interested">Not Interested</option>
                                <option value="Lost">Lost</option>
                            </select>
                        </div>
                        <div class="space-y-1.5">
                            <label class="text-[11px] font-bold text-neutral-700 ml-1 uppercase tracking-wider">Priority</label>
                            <select name="priority" class="w-full bg-neutral-50 border-transparent rounded-xl py-2.5 px-4 focus:bg-white focus:border-primary-100 focus:ring-4 focus:ring-primary-50 transition-all outline-none cursor-pointer text-sm">
                                <option value="High">High</option>
                                <option value="Medium" selected>Medium</option>
                                <option value="Low">Low</option>
                            </select>
                        </div>
                        <div class="space-y-1.5">
                            <label class="text-[11px] font-bold text-neutral-700 ml-1 uppercase tracking-wider">Assigned To</label>
                            <select name="assigned_to" class="w-full bg-neutral-50 border-transparent rounded-xl py-2.5 px-4 focus:bg-white focus:border-primary-100 focus:ring-4 focus:ring-primary-50 transition-all outline-none cursor-pointer text-sm">
                                <option value="">Select Staff</option>
                                <?php foreach ($users as $user): ?>
                                    <option value="<?= $user['id'] ?>"><?= htmlspecialchars($user['full_name']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="space-y-1.5">
                            <label class="text-[11px] font-bold text-neutral-700 ml-1 uppercase tracking-wider">Deal Status *</label>
                            <select name="deal_status" required class="w-full bg-neutral-50 border-transparent rounded-xl py-2.5 px-4 focus:bg-white focus:border-primary-100 focus:ring-4 focus:ring-primary-50 transition-all outline-none cursor-pointer text-sm">
                                <option value="Open">Open</option>
                                <option value="Won">Won</option>
                                <option value="Lost">Lost</option>
                                <option value="Pending">Pending</option>
                            </select>
                        </div>
                        <div class="space-y-1.5">
                            <label class="text-[11px] font-bold text-neutral-700 ml-1 uppercase tracking-wider">Estimated Budget</label>
                            <input type="number" name="estimated_budget" placeholder="0.00" step="0.01" class="w-full bg-neutral-50 border-transparent rounded-xl py-2.5 px-4 focus:bg-white focus:border-primary-100 focus:ring-4 focus:ring-primary-50 transition-all outline-none text-sm">
                        </div>
                        <div class="space-y-1.5">
                            <label class="text-[11px] font-bold text-neutral-700 ml-1 uppercase tracking-wider">Next Follow-up Date</label>
                            <input type="date" name="next_followup_date" class="w-full bg-neutral-50 border-transparent rounded-xl py-2.5 px-4 focus:bg-white focus:border-primary-100 focus:ring-4 focus:ring-primary-50 transition-all outline-none text-sm">
                        </div>
                        <div class="md:col-span-3 space-y-3">
                            <label class="text-[11px] font-bold text-neutral-700 ml-1 uppercase tracking-wider">Process Checklist</label>
                            <div class="flex flex-wrap gap-4">
                                <label class="flex items-center gap-2 cursor-pointer group">
                                    <input type="checkbox" name="proposal_sent" value="1" class="w-4 h-4 rounded border-neutral-300 text-primary-600 focus:ring-primary-500 transition-all">
                                    <span class="text-xs font-medium text-neutral-600 group-hover:text-neutral-900">Proposal Sent</span>
                                </label>
                                <label class="flex items-center gap-2 cursor-pointer group">
                                    <input type="checkbox" name="meeting_scheduled" value="1" class="w-4 h-4 rounded border-neutral-300 text-primary-600 focus:ring-primary-500 transition-all">
                                    <span class="text-xs font-medium text-neutral-600 group-hover:text-neutral-900">Meeting Scheduled</span>
                                </label>
                                <label class="flex items-center gap-2 cursor-pointer group">
                                    <input type="checkbox" name="quotation_sent" value="1" class="w-4 h-4 rounded border-neutral-300 text-primary-600 focus:ring-primary-500 transition-all">
                                    <span class="text-xs font-medium text-neutral-600 group-hover:text-neutral-900">Quotation Sent</span>
                                </label>
                            </div>
                        </div>
                        <div class="md:col-span-3 space-y-1.5">
                            <label class="text-[11px] font-bold text-neutral-700 ml-1 uppercase tracking-wider">Last Follow-up Notes</label>
                            <textarea name="last_followup_notes" rows="2" placeholder="Summary of last conversation..." class="w-full bg-neutral-50 border-transparent rounded-xl py-2.5 px-4 focus:bg-white focus:border-primary-100 focus:ring-4 focus:ring-primary-50 transition-all outline-none resize-none text-sm"></textarea>
                        </div>
                    </div>
                </div>

                <!-- Project & Others Tab -->
                <div id="content-project" class="tab-content hidden space-y-5">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
                        <div class="space-y-1.5">
                            <label class="text-[11px] font-bold text-neutral-700 ml-1 uppercase tracking-wider">Payment Status *</label>
                            <select name="payment_status" required class="w-full bg-neutral-50 border-transparent rounded-xl py-2.5 px-4 focus:bg-white focus:border-primary-100 focus:ring-4 focus:ring-primary-50 transition-all outline-none cursor-pointer text-sm">
                                <option value="Pending">Pending</option>
                                <option value="Partial">Partial</option>
                                <option value="Completed">Completed</option>
                            </select>
                        </div>
                        <div class="space-y-1.5">
                            <label class="text-[11px] font-bold text-neutral-700 ml-1 uppercase tracking-wider">Project Status</label>
                            <select name="project_status" class="w-full bg-neutral-50 border-transparent rounded-xl py-2.5 px-4 focus:bg-white focus:border-primary-100 focus:ring-4 focus:ring-primary-50 transition-all outline-none cursor-pointer text-sm">
                                <option value="">Select Status</option>
                                <option value="Not Started">Not Started</option>
                                <option value="In Progress">In Progress</option>
                                <option value="Completed">Completed</option>
                                <option value="On Hold">On Hold</option>
                            </select>
                        </div>
                        <div class="space-y-1.5">
                            <label class="text-[11px] font-bold text-neutral-700 ml-1 uppercase tracking-wider">Expected Closing Date</label>
                            <input type="date" name="expected_closing_date" class="w-full bg-neutral-50 border-transparent rounded-xl py-2.5 px-4 focus:bg-white focus:border-primary-100 focus:ring-4 focus:ring-primary-50 transition-all outline-none text-sm">
                        </div>
                        <div class="space-y-1.5">
                            <label class="text-[11px] font-bold text-neutral-700 ml-1 uppercase tracking-wider">Client Onboard Date</label>
                            <input type="date" name="client_onboard_date" class="w-full bg-neutral-50 border-transparent rounded-xl py-2.5 px-4 focus:bg-white focus:border-primary-100 focus:ring-4 focus:ring-primary-50 transition-all outline-none text-sm">
                        </div>
                        <div class="space-y-1.5">
                            <label class="text-[11px] font-bold text-neutral-700 ml-1 uppercase tracking-wider">Project Start Date</label>
                            <input type="date" name="project_start_date" class="w-full bg-neutral-50 border-transparent rounded-xl py-2.5 px-4 focus:bg-white focus:border-primary-100 focus:ring-4 focus:ring-primary-50 transition-all outline-none text-sm">
                        </div>
                        <div class="space-y-1.5">
                            <label class="text-[11px] font-bold text-neutral-700 ml-1 uppercase tracking-wider">Reference By</label>
                            <input type="text" name="reference_by" placeholder="Referral name" class="w-full bg-neutral-50 border-transparent rounded-xl py-2.5 px-4 focus:bg-white focus:border-primary-100 focus:ring-4 focus:ring-primary-50 transition-all outline-none text-sm">
                        </div>
                        <div class="space-y-1.5">
                            <label class="text-[11px] font-bold text-neutral-700 ml-1 uppercase tracking-wider">Website/Social Link</label>
                            <input type="text" name="website_social_link" placeholder="https://..." class="w-full bg-neutral-50 border-transparent rounded-xl py-2.5 px-4 focus:bg-white focus:border-primary-100 focus:ring-4 focus:ring-primary-50 transition-all outline-none text-sm">
                        </div>
                        <div class="md:col-span-3 space-y-1.5">
                            <label class="text-[11px] font-bold text-neutral-700 ml-1 uppercase tracking-wider">Remarks/Notes</label>
                            <textarea name="remarks_notes" rows="2" placeholder="Additional notes..." class="w-full bg-neutral-50 border-transparent rounded-xl py-2.5 px-4 focus:bg-white focus:border-primary-100 focus:ring-4 focus:ring-primary-50 transition-all outline-none resize-none text-sm"></textarea>
                        </div>
                    </div>
                </div>

                <div class="flex items-center justify-between mt-6 pt-6 border-t border-neutral-100">
                    <div class="flex gap-3">
                        <button type="button" id="cancelBtn" onclick="closeLeadModal()" class="px-6 py-2 bg-neutral-100 text-neutral-600 font-semibold rounded-xl hover:bg-neutral-200 transition-all text-sm hidden">
                            Cancel
                        </button>
                        <button type="button" id="prevBtn" onclick="navigate('prev')" class="px-6 py-2 bg-neutral-100 text-neutral-600 font-semibold rounded-xl hover:bg-neutral-200 transition-all text-sm hidden">
                            Previous
                        </button>
                    </div>
                    
                    <div class="flex gap-3">
                        <button type="button" id="nextBtn" onclick="navigate('next')" class="btn btn-primary px-8 py-2.5 rounded-xl flex items-center gap-2 text-sm shadow-lg shadow-primary/20">
                            <span>Next Step</span>
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M9 5l7 7-7 7" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"></path></svg>
                        </button>
                        <button type="submit" id="saveLeadBtn" class="btn btn-primary px-8 py-2.5 rounded-xl flex items-center gap-2 text-sm shadow-lg shadow-primary/20 hidden">
                            <span>Save Lead Details</span>
                            <svg id="loadingIcon" class="w-4 h-4 animate-spin hidden" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    let currentPage = 1;
    let currentLimit = 10;
    let leadsData = [];
    const currentRole = '<?= $_SESSION['role'] ?>';

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
        loadLeads(1);
    }

    async function loadLeads(page = 1) {
        currentPage = page;
        const search = document.getElementById('search').value;
        const status = document.getElementById('statusFilter').value;
        const service = document.getElementById('serviceFilter').value;

        const tbody = document.getElementById('leadsTableBody');
        tbody.innerHTML = `<tr><td colspan="15" class="px-6 py-20 text-center"><div class="flex flex-col items-center gap-2"><div class="w-6 h-6 border-2 border-primary-600 border-t-transparent rounded-full animate-spin"></div><span class="text-xs font-bold text-neutral-400 uppercase tracking-widest">Loading...</span></div></td></tr>`;

        try {
            const params = new URLSearchParams({
                page,
                limit: currentLimit,
                search,
                status,
                service
            });

            const response = await fetch(`../api/leads.php?${params}`);
            const res = await response.json();
            
            if (!res.success) throw new Error(res.message);
            
            const data = res.data;
            leadsData = data?.leads || [];

            const tbody = document.getElementById('leadsTableBody');
            if (!leadsData || leadsData.length === 0) {
                tbody.innerHTML = `<tr><td colspan="32" class="px-8 py-20 text-center text-neutral-400">No leads found. Try adjusting filters.</td></tr>`;
                updatePagination({ total: 0, page: 1, pages: 0, limit: 10 });
                return;
            }

            tbody.innerHTML = leadsData.map(lead => `
                <tr class="hover:bg-neutral-50/50 transition-colors group">
                    <td class="px-4 py-3 text-[11px] text-neutral-500 whitespace-nowrap font-medium">${lead.lead_id || '-'}</td>
                    <td class="px-4 py-3 text-[11px] text-neutral-500 whitespace-nowrap">${formatDate(lead.lead_date)}</td>
                    <td class="px-4 py-3 text-[13px] text-neutral-900 whitespace-nowrap font-semibold group-hover:text-primary transition-colors">${lead.company_client_name || '-'}</td>
                    <td class="px-4 py-3 text-[12px] text-neutral-600 whitespace-nowrap">${lead.contact_person || '-'}</td>
                    <td class="px-4 py-3 text-[12px] text-neutral-600 whitespace-nowrap">${lead.mobile_number || '-'}</td>
                    <td class="px-4 py-3 text-[12px] text-neutral-600 whitespace-nowrap">${lead.alternative_number || '-'}</td>
                    <td class="px-4 py-3 text-[12px] text-neutral-600 whitespace-nowrap">${lead.email_id || '-'}</td>
                    <td class="px-4 py-3 text-[12px] text-neutral-600 whitespace-nowrap">${lead.city || '-'}</td>
                    <td class="px-4 py-3 text-[12px] text-neutral-600 whitespace-nowrap">${lead.state || '-'}</td>
                    <td class="px-4 py-3 text-[12px] text-neutral-600 whitespace-nowrap">${lead.source_of_lead || '-'}</td>
                    <td class="px-4 py-3 text-[12px] text-neutral-600 whitespace-nowrap">${lead.service_interested_in || '-'}</td>
                    <td class="px-4 py-3 text-[11px] whitespace-nowrap">
                        ${renderCategoryBadge(lead.lead_category)}
                    </td>
                    <td class="px-4 py-3 text-[12px] text-neutral-600 whitespace-nowrap">${lead.lead_status || '-'}</td>
                    <td class="px-4 py-3 text-[11px] whitespace-nowrap">
                        <span class="flex items-center gap-1.5 text-[11px] font-semibold ${lead.priority === 'High' ? 'text-red-500' : 'text-neutral-500'}">
                            <div class="w-1.5 h-1.5 rounded-full ${lead.priority === 'High' ? 'bg-red-500' : 'bg-neutral-300'}"></div>
                            ${lead.priority || 'Medium'}
                        </span>
                    </td>
                    <td class="px-4 py-3 text-[12px] text-neutral-600 whitespace-nowrap">${lead.assigned_to_name || '-'}</td>
                    <td class="px-4 py-3 text-[12px] text-neutral-600 whitespace-nowrap">${formatDate(lead.next_followup_date)}</td>
                    <td class="px-4 py-3 text-[12px] text-neutral-600 whitespace-nowrap max-w-xs truncate">${lead.last_followup_notes || '-'}</td>
                    <td class="px-4 py-3 text-[12px] text-neutral-600 whitespace-nowrap max-w-xs truncate">${lead.requirement_details || '-'}</td>
                    <td class="px-4 py-3 text-[12px] text-neutral-600 whitespace-nowrap font-medium">${lead.estimated_budget || '-'}</td>
                    <td class="px-4 py-3 text-[11px] text-neutral-600 whitespace-nowrap">${lead.proposal_sent == 1 ? '✅ Yes' : '❌ No'}</td>
                    <td class="px-4 py-3 text-[11px] text-neutral-600 whitespace-nowrap">${lead.meeting_scheduled == 1 ? '✅ Yes' : '❌ No'}</td>
                    <td class="px-4 py-3 text-[11px] text-neutral-600 whitespace-nowrap">${lead.quotation_sent == 1 ? '✅ Yes' : '❌ No'}</td>
                    <td class="px-4 py-3 text-[12px] text-neutral-600 whitespace-nowrap">${lead.deal_status || '-'}</td>
                    <td class="px-4 py-3 text-[12px] text-neutral-600 whitespace-nowrap">${formatDate(lead.expected_closing_date)}</td>
                    <td class="px-4 py-3 text-[12px] text-neutral-600 whitespace-nowrap">${lead.payment_status || '-'}</td>
                    <td class="px-4 py-3 text-[12px] text-neutral-600 whitespace-nowrap">${formatDate(lead.client_onboard_date)}</td>
                    <td class="px-4 py-3 text-[12px] text-neutral-600 whitespace-nowrap">${formatDate(lead.project_start_date)}</td>
                    <td class="px-4 py-3 text-[12px] text-neutral-600 whitespace-nowrap">${lead.project_status || '-'}</td>
                    <td class="px-4 py-3 text-[12px] text-neutral-600 whitespace-nowrap">${lead.reference_by || '-'}</td>
                    <td class="px-4 py-3 text-[12px] text-neutral-600 whitespace-nowrap">${lead.website_social_link || '-'}</td>
                    <td class="px-4 py-3 text-[12px] text-neutral-600 whitespace-nowrap max-w-xs truncate">${lead.remarks_notes || '-'}</td>
                    <td class="px-4 py-3 text-right whitespace-nowrap">
                        <div class="flex items-center justify-end gap-1.5">
                            <button onclick="editLead('${lead.id}')" class="p-1.5 text-neutral-400 hover:text-primary hover:bg-primary-light rounded-lg transition-all">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
                            </button>
                            ${currentRole === 'admin' ? `
                            <button onclick="deleteLead('${lead.id}')" class="p-1.5 text-neutral-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition-all">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                            </button>
                            ` : ''}
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

    function renderCategoryBadge(category) {
        if (!category) return '<span class="text-neutral-400">-</span>';
        
        let styles = '';
        let label = category;
        
        switch(category.toLowerCase()) {
            case 'hot':
                styles = 'background-color: #FF9D3D; color: #7A3E00;';
                label = '🔥 Hot';
                break;
            case 'warm':
                styles = 'background-color: #FEEE91; color: #7A6500;';
                label = '☀️ Warm';
                break;
            case 'cold':
                styles = 'background-color: #B0DEFF; color: #004A7A;';
                label = '❄️ Cold';
                break;
            default:
                styles = 'background-color: #f3f4f6; color: #4b5563;';
        }
        
        return `<span class="px-2.5 py-1 text-[10px] font-semibold rounded-lg uppercase tracking-wider shadow-sm hover:brightness-95 transition-all cursor-default whitespace-nowrap inline-block" style="${styles}">${label}</span>`;
    }

    async function deleteLead(id) {
        if (!confirm('Are you sure you want to delete this lead? This action cannot be undone.')) return;

        try {
            const response = await fetch(`../api/leads.php?id=${id}`, {
                method: 'DELETE'
            });

            const res = await response.json();

            if (res.success) {
                showToast(res.message, 'success');
                loadLeads(currentPage);
            } else {
                throw new Error(res.message || 'Failed to delete lead');
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
        if (pagination.pages <= 1) return;

        // Previous Button
        const prevBtn = document.createElement('button');
        prevBtn.innerHTML = '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M15 19l-7-7 7-7" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"></path></svg>';
        prevBtn.className = `w-9 h-9 rounded-xl flex items-center justify-center transition-all ${pagination.page > 1 ? 'text-neutral-500 hover:bg-neutral-100' : 'text-neutral-200 cursor-not-allowed'}`;
        prevBtn.onclick = () => pagination.page > 1 && loadLeads(pagination.page - 1);
        container.appendChild(prevBtn);
        
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

        // Next Button
        const nextBtn = document.createElement('button');
        nextBtn.innerHTML = '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M9 5l7 7-7 7" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"></path></svg>';
        nextBtn.className = `w-9 h-9 rounded-xl flex items-center justify-center transition-all ${pagination.page < maxPages ? 'text-neutral-500 hover:bg-neutral-100' : 'text-neutral-200 cursor-not-allowed'}`;
        nextBtn.onclick = () => pagination.page < maxPages && loadLeads(pagination.page + 1);
        container.appendChild(nextBtn);
    }

    function addPaginationBtn(page, isActive) {
        const btn = document.createElement('button');
        btn.textContent = page;
        btn.className = `w-9 h-9 rounded-xl font-bold text-xs transition-all ${isActive ? 'bg-primary text-white shadow-lg shadow-primary/25 scale-105' : 'text-neutral-500 hover:bg-neutral-100'}`;
        btn.onclick = () => loadLeads(page);
        document.getElementById('pagination').appendChild(btn);
    }

    function addPaginationEllipsis() {
        const span = document.createElement('span');
        span.textContent = '...';
        span.className = 'w-9 h-9 flex items-center justify-center text-neutral-400 font-bold';
        document.getElementById('pagination').appendChild(span);
    }

    let currentTab = 'basic';
    const tabs = ['basic', 'sales', 'project'];

    function switchTab(tabId) {
        currentTab = tabId;
        // Update tab buttons
        document.querySelectorAll('.tab-btn').forEach(btn => {
            btn.classList.remove('active', 'text-primary-600', 'border-primary-600');
            btn.classList.add('text-neutral-400', 'border-transparent');
        });
        const activeTab = document.getElementById(`tab-${tabId}`);
        activeTab.classList.add('active', 'text-primary', 'border-primary');
        activeTab.classList.remove('text-neutral-400', 'border-transparent');

        // Update tab content
        document.querySelectorAll('.tab-content').forEach(content => {
            content.classList.add('hidden');
        });
        document.getElementById(`content-${tabId}`).classList.remove('hidden');

        // Update Buttons Visibility
        const prevBtn = document.getElementById('prevBtn');
        const nextBtn = document.getElementById('nextBtn');
        const saveBtn = document.getElementById('saveLeadBtn');
        const cancelBtn = document.getElementById('cancelBtn');

        if (tabId === 'basic') {
            prevBtn.classList.add('hidden');
            cancelBtn.classList.add('hidden');
            nextBtn.classList.remove('hidden');
            saveBtn.classList.add('hidden');
        } else if (tabId === 'sales') {
            prevBtn.classList.remove('hidden');
            cancelBtn.classList.add('hidden');
            nextBtn.classList.remove('hidden');
            saveBtn.classList.add('hidden');
        } else if (tabId === 'project') {
            prevBtn.classList.remove('hidden');
            cancelBtn.classList.remove('hidden');
            nextBtn.classList.add('hidden');
            saveBtn.classList.remove('hidden');
        }
    }

    function navigate(direction) {
        const currentIndex = tabs.indexOf(currentTab);
        if (direction === 'next' && currentIndex < tabs.length - 1) {
            // Optional: Basic validation before moving next
            const currentContent = document.getElementById('content-' + currentTab);
            const inputs = currentContent.querySelectorAll('input[required], select[required], textarea[required]');
            let isValid = true;
            inputs.forEach(input => {
                if (!input.checkValidity()) {
                    input.reportValidity();
                    isValid = false;
                }
            });
            
            if (isValid) {
                switchTab(tabs[currentIndex + 1]);
            }
        } else if (direction === 'prev' && currentIndex > 0) {
            switchTab(tabs[currentIndex - 1]);
        }
    }

    function openAddLeadModal() {
        document.getElementById('leadForm').reset();
        document.getElementById('lead_id_input').value = '';
        document.getElementById('display_lead_id').value = 'Auto-generated';
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
        document.getElementById('display_lead_id').value = lead.lead_id || 'N/A';
        document.getElementById('modalTitle').textContent = 'Edit Lead Details';

        // Fill form fields
        for (const key in lead) {
            const inputs = form.querySelectorAll(`[name="${key}"]`);
            inputs.forEach(input => {
                if (input.type === 'date' && lead[key]) {
                    // Handle both ISO date and full timestamp
                    input.value = lead[key].includes(' ') ? lead[key].split(' ')[0] : lead[key];
                } else if (input.type === 'checkbox') {
                    // Handle numeric 1/0 or boolean from database
                    input.checked = lead[key] == 1 || lead[key] === true;
                } else if (input.tagName === 'SELECT') {
                    // Ensure the value exists before setting it, or set to empty
                    input.value = lead[key] !== null ? lead[key] : '';
                } else {
                    input.value = lead[key] !== null ? lead[key] : '';
                }
            });
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
        
        // Clear previous validation states
        this.querySelectorAll('.input-error').forEach(el => el.classList.remove('input-error'));
        this.querySelectorAll('.error-message').forEach(el => el.remove());

        const formData = new FormData(this);
        const data = Object.fromEntries(formData.entries());
        
        // Handle checkboxes: include them even if unchecked (FormData skips unchecked)
        const checkboxes = this.querySelectorAll('input[type="checkbox"]');
        checkboxes.forEach(cb => {
            data[cb.name] = cb.checked ? 1 : 0;
        });

        // Convert empty strings to null for optional fields (except lead_id)
        for (const key in data) {
            if (data[key] === '' && key !== 'id') data[key] = null;
        }

        // Frontend Validation
        let isValid = true;
        const setError = (name, msg) => {
            const input = this.querySelector(`[name="${name}"]`);
            if (input) {
                input.classList.add('input-error');
                const err = document.createElement('p');
                err.className = 'error-message';
                err.innerHTML = `<svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path></svg> ${msg}`;
                input.closest('.space-y-1\\.5')?.appendChild(err);
            }
            isValid = false;
        };

        if (!data.lead_date) setError('lead_date', 'Lead date is required');
        if (!data.company_client_name || data.company_client_name.length < 3) setError('company_client_name', 'Company name must be at least 3 characters');
        if (!data.contact_person || data.contact_person.length < 3) setError('contact_person', 'Contact person required');
        if (!data.mobile_number || !/^[0-9]{10}$/.test(data.mobile_number)) setError('mobile_number', 'Valid mobile number required (10 digits)');
        if (!data.source_of_lead) setError('source_of_lead', 'Source is required');

        if (!isValid) {
            showToast('Please fix the errors before saving', 'error');
            // If error is in another tab, switch to it
            const firstErr = this.querySelector('.input-error');
            if (firstErr) {
                const tabContent = firstErr.closest('.tab-content');
                if (tabContent) {
                    const tabId = tabContent.id.replace('content-', '');
                    switchTab(tabId);
                    setTimeout(() => firstErr.focus(), 100);
                }
            }
            return;
        }

        saveBtn.disabled = true;
        loadingIcon.classList.remove('hidden');

        try {
            const url = id ? `../api/leads.php?id=${id}` : '../api/leads.php';
            const method = id ? 'PUT' : 'POST';
            
            const response = await fetch(url, {
                method: method,
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(data)
            });

            const res = await response.json();

            if (res.success) {
                showToast(res.message, 'success');
                closeLeadModal();
                loadLeads(id ? currentPage : 1);
            } else {
                showToast(res.message || 'Validation failed', 'error');
                // Highlight mobile number if it's a duplicate error
                if (res.message && res.message.toLowerCase().includes('mobile')) {
                    setError('mobile_number', res.message);
                }
            }
        } catch (error) {
            showToast('System error: ' + error.message, 'error');
        } finally {
            saveBtn.disabled = false;
            loadingIcon.classList.add('hidden');
        }
    });

    async function exportLeads() {
        const search = document.getElementById('search').value;
        const status = document.getElementById('statusFilter').value;
        const service = document.getElementById('serviceFilter').value;

        const params = new URLSearchParams({ 
            search, 
            status, 
            service
        });

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