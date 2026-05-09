<?php
require_once '../includes/middleware.php';
require_once '../includes/components/layout_wrapper.php';
requireAuth();

layout_start('Add New Lead - Deckoid ERP');

// Fetch users for 'Assigned To' dropdown
require_once '../config/env.php';
require_once '../includes/database.php';
$db = Database::getInstance();
$usersStmt = $db->query("SELECT id, full_name FROM users ORDER BY full_name ASC");
$users = $usersStmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="mb-6">
    <h1 class="text-2xl font-black text-neutral-900 tracking-tight">Add New Lead</h1>
    <p class="text-neutral-500 text-sm mt-1 font-medium">Create a new potential business opportunity.</p>
</div>

<div class="bg-white rounded-xl shadow-sm border border-neutral-100 overflow-hidden glass-card">
    <!-- Form Tabs -->
    <div class="flex border-b border-neutral-100 bg-neutral-50/50">
        <button onclick="switchTab('basic')" id="tab-basic" class="flex-1 px-6 py-4 text-xs font-bold transition-all border-b-2 border-primary-600 text-primary-600 uppercase tracking-widest">
            1. Basic Information
        </button>
        <button onclick="switchTab('sales')" id="tab-sales" class="flex-1 px-6 py-4 text-xs font-bold transition-all border-b-2 border-transparent text-neutral-400 hover:text-neutral-600 uppercase tracking-widest">
            2. Sales Tracking
        </button>
        <button onclick="switchTab('project')" id="tab-project" class="flex-1 px-6 py-4 text-xs font-bold transition-all border-b-2 border-transparent text-neutral-400 hover:text-neutral-600 uppercase tracking-widest">
            3. Project & Others
        </button>
    </div>

    <form id="addLeadForm" class="p-6 lg:p-8">
        <!-- Basic Information Tab -->
        <div id="content-basic" class="tab-content space-y-6">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <div class="space-y-1.5">
                    <label class="text-[11px] font-bold text-neutral-700 ml-1 uppercase tracking-wider">Lead ID</label>
                    <input type="text" readonly value="Auto-generated" class="w-full bg-neutral-100 border-transparent rounded-xl py-2.5 px-4 text-neutral-500 transition-all outline-none text-sm font-bold cursor-not-allowed">
                </div>
                <div class="space-y-1.5">
                    <label class="text-[11px] font-bold text-neutral-700 ml-1 uppercase tracking-wider">Lead Date *</label>
                    <input type="date" name="lead_date" required class="w-full bg-neutral-50 border-transparent rounded-xl py-2.5 px-4 focus:bg-white focus:border-primary-100 focus:ring-4 focus:ring-primary-50 transition-all outline-none text-sm">
                </div>
                <div class="space-y-1.5">
                    <label class="text-[11px] font-bold text-neutral-700 ml-1 uppercase tracking-wider">Company/Client Name *</label>
                    <input type="text" name="company_client_name" required placeholder="Enter company name" class="w-full bg-neutral-50 border-transparent rounded-xl py-2.5 px-4 focus:bg-white focus:border-primary-100 focus:ring-4 focus:ring-primary-50 transition-all outline-none text-sm">
                </div>
                <div class="space-y-1.5">
                    <label class="text-[11px] font-bold text-neutral-700 ml-1 uppercase tracking-wider">Contact Person *</label>
                    <input type="text" name="contact_person" required placeholder="Full name" class="w-full bg-neutral-50 border-transparent rounded-xl py-2.5 px-4 focus:bg-white focus:border-primary-100 focus:ring-4 focus:ring-primary-50 transition-all outline-none text-sm">
                </div>
                <div class="space-y-1.5">
                    <label class="text-[11px] font-bold text-neutral-700 ml-1 uppercase tracking-wider">Mobile Number *</label>
                    <input type="text" name="mobile_number" required placeholder="Phone number" class="w-full bg-neutral-50 border-transparent rounded-xl py-2.5 px-4 focus:bg-white focus:border-primary-100 focus:ring-4 focus:ring-primary-50 transition-all outline-none text-sm">
                </div>
                <div class="space-y-1.5">
                    <label class="text-[11px] font-bold text-neutral-700 ml-1 uppercase tracking-wider">Email ID</label>
                    <input type="email" name="email_id" placeholder="email@example.com" class="w-full bg-neutral-50 border-transparent rounded-xl py-2.5 px-4 focus:bg-white focus:border-primary-100 focus:ring-4 focus:ring-primary-50 transition-all outline-none text-sm">
                </div>
                <div class="space-y-1.5">
                    <label class="text-[11px] font-bold text-neutral-700 ml-1 uppercase tracking-wider">Alternative Number</label>
                    <input type="text" name="alternative_number" placeholder="Other contact number" class="w-full bg-neutral-50 border-transparent rounded-xl py-2.5 px-4 focus:bg-white focus:border-primary-100 focus:ring-4 focus:ring-primary-50 transition-all outline-none text-sm">
                </div>
                <div class="space-y-1.5">
                    <label class="text-[11px] font-bold text-neutral-700 ml-1 uppercase tracking-wider">Source of Lead *</label>
                    <select name="source_of_lead" required class="w-full bg-neutral-50 border-transparent rounded-xl py-2.5 px-4 focus:bg-white focus:border-primary-100 focus:ring-4 focus:ring-primary-50 transition-all outline-none cursor-pointer text-sm">
                        <option value="Indiamart">IndiaMart</option>
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
                <div class="space-y-1.5">
                    <label class="text-[11px] font-bold text-neutral-700 ml-1 uppercase tracking-wider">Service Interested In</label>
                    <select name="service_interested_in" required class="w-full bg-neutral-50 border-transparent rounded-xl py-2.5 px-4 focus:bg-white focus:border-primary-100 focus:ring-4 focus:ring-primary-50 transition-all outline-none cursor-pointer text-sm">
                        <option value="Facebook & Google Ads">Facebook & Google Ads</option>
                        <option value="Website Design & Development">Website Design & Development</option>
                        <option value="Graphics Design">Graphics Design</option>
                        <option value="Search Engine Optimization">Search Engine Optimization</option>
                        <option value="Video Editing">Video Editing</option>
                        <option value="Social Media Management">Social Media Management</option>
                        <option value="AI Video Making">AI Video Making</option>
                    </select>
                </div>
                <div class="md:col-span-2 lg:col-span-3 space-y-1.5">
                    <label class="text-[11px] font-bold text-neutral-700 ml-1 uppercase tracking-wider">Requirement Details</label>
                    <textarea name="requirement_details" rows="2" placeholder="Specific requirements or project details..." class="w-full bg-neutral-50 border-transparent rounded-xl py-2.5 px-4 focus:bg-white focus:border-primary-100 focus:ring-4 focus:ring-primary-50 transition-all outline-none resize-none text-sm"></textarea>
                </div>
            </div>
        </div>

        <!-- Sales Tracking Tab -->
        <div id="content-sales" class="tab-content hidden space-y-6">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
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
                <div class="md:col-span-2 lg:col-span-3 space-y-3">
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
                <div class="md:col-span-2 lg:col-span-3 space-y-1.5">
                    <label class="text-[11px] font-bold text-neutral-700 ml-1 uppercase tracking-wider">Last Follow-up Notes</label>
                    <textarea name="last_followup_notes" rows="2" placeholder="Summary of last conversation..." class="w-full bg-neutral-50 border-transparent rounded-xl py-2.5 px-4 focus:bg-white focus:border-primary-100 focus:ring-4 focus:ring-primary-50 transition-all outline-none resize-none text-sm"></textarea>
                </div>
            </div>
        </div>

        <!-- Project & Others Tab -->
        <div id="content-project" class="tab-content hidden space-y-6">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <div class="space-y-1.5">
                    <label class="text-[11px] font-bold text-neutral-700 ml-1 uppercase tracking-wider">City</label>
                    <input type="text" name="city" placeholder="City name" class="w-full bg-neutral-50 border-transparent rounded-xl py-2.5 px-4 focus:bg-white focus:border-primary-100 focus:ring-4 focus:ring-primary-50 transition-all outline-none text-sm">
                </div>
                <div class="space-y-1.5">
                    <label class="text-[11px] font-bold text-neutral-700 ml-1 uppercase tracking-wider">State</label>
                    <input type="text" name="state" placeholder="State name" class="w-full bg-neutral-50 border-transparent rounded-xl py-2.5 px-4 focus:bg-white focus:border-primary-100 focus:ring-4 focus:ring-primary-50 transition-all outline-none text-sm">
                </div>
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
                <div class="md:col-span-2 lg:col-span-3 space-y-1.5">
                    <label class="text-[11px] font-bold text-neutral-700 ml-1 uppercase tracking-wider">Remarks/Notes</label>
                    <textarea name="remarks_notes" rows="2" placeholder="Additional notes..." class="w-full bg-neutral-50 border-transparent rounded-xl py-2.5 px-4 focus:bg-white focus:border-primary-100 focus:ring-4 focus:ring-primary-50 transition-all outline-none resize-none text-sm"></textarea>
                </div>
            </div>
        </div>

        <div class="flex items-center justify-between mt-6 pt-6 border-t border-neutral-100">
            <div class="flex gap-3">
                <button type="button" id="cancelBtn" onclick="window.location.href='leads.php'" class="px-6 py-2 bg-neutral-100 text-neutral-600 font-bold rounded-xl hover:bg-neutral-200 transition-all text-sm hidden">
                    Cancel
                </button>
                <button type="button" id="prevBtn" onclick="navigate('prev')" class="px-6 py-2 bg-neutral-100 text-neutral-600 font-bold rounded-xl hover:bg-neutral-200 transition-all text-sm hidden">
                    Previous
                </button>
            </div>
            
            <div class="flex gap-3">
                <button type="button" id="nextBtn" onclick="navigate('next')" class="px-6 py-2 bg-primary-600 text-white font-bold rounded-xl hover:bg-primary-700 shadow-lg shadow-primary-200 transition-all flex items-center gap-2 text-sm">
                    <span>Next Step</span>
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M9 5l7 7-7 7"></path></svg>
                </button>
                <button type="submit" id="saveLeadBtn" class="px-6 py-2 bg-primary-600 text-white font-bold rounded-xl hover:bg-primary-700 shadow-lg shadow-primary-200 transition-all flex items-center gap-2 text-sm hidden">
                    <span>Save Lead Details</span>
                    <svg id="loadingIcon" class="w-4 h-4 animate-spin hidden" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                </button>
            </div>
        </div>
    </form>
</div>

<script>
    let currentTab = 'basic';
    const tabs = ['basic', 'sales', 'project'];

    function switchTab(tab) {
        currentTab = tab;
        document.querySelectorAll('.tab-content').forEach(c => c.classList.add('hidden'));
        document.getElementById('content-' + tab).classList.remove('hidden');
        
        document.querySelectorAll('[id^="tab-"]').forEach(t => {
            t.classList.remove('border-primary-600', 'text-primary-600');
            t.classList.add('border-transparent', 'text-neutral-400');
        });
        
        const activeTab = document.getElementById('tab-' + tab);
        activeTab.classList.remove('border-transparent', 'text-neutral-400');
        activeTab.classList.add('border-primary-600', 'text-primary-600');

        // Update Buttons Visibility
        const prevBtn = document.getElementById('prevBtn');
        const nextBtn = document.getElementById('nextBtn');
        const saveBtn = document.getElementById('saveLeadBtn');
        const cancelBtn = document.getElementById('cancelBtn');

        if (tab === 'basic') {
            prevBtn.classList.add('hidden');
            cancelBtn.classList.add('hidden');
            nextBtn.classList.remove('hidden');
            saveBtn.classList.add('hidden');
        } else if (tab === 'sales') {
            prevBtn.classList.remove('hidden');
            cancelBtn.classList.add('hidden');
            nextBtn.classList.remove('hidden');
            saveBtn.classList.add('hidden');
        } else if (tab === 'project') {
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
                window.scrollTo({ top: 0, behavior: 'smooth' });
            }
        } else if (direction === 'prev' && currentIndex > 0) {
            switchTab(tabs[currentIndex - 1]);
            window.scrollTo({ top: 0, behavior: 'smooth' });
        }
    }

    document.getElementById('addLeadForm').onsubmit = async function(e) {
        e.preventDefault();
        const btn = document.getElementById('saveLeadBtn');
        const icon = document.getElementById('loadingIcon');
        
        btn.disabled = true;
        icon.classList.remove('hidden');

        const formData = new FormData(this);
        const data = Object.fromEntries(formData.entries());

        try {
            const response = await fetch('../api/leads.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(data)
            });

            const result = await response.json();

            if (response.ok) {
                showToast('Lead added successfully!', 'success');
                setTimeout(() => window.location.href = 'leads.php', 1500);
            } else {
                throw new Error(result.message || 'Failed to add lead');
            }
        } catch (error) {
            showToast(error.message, 'error');
            btn.disabled = false;
            icon.classList.add('hidden');
        }
    };
</script>

<?php layout_end(); ?>
