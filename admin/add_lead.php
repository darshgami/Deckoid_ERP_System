<?php
require_once '../includes/middleware.php';
require_once '../includes/components/layout_wrapper.php';
requireAuth();

layout_start('Add New Lead - Deckoid ERP');
?>

<div class="mb-10">
    <h1 class="text-4xl font-black text-neutral-900 tracking-tight">Add New Lead</h1>
    <p class="text-neutral-500 mt-2 font-medium">Create a new potential business opportunity.</p>
</div>

<div class="bg-white rounded-[2.5rem] shadow-sm border border-neutral-100 overflow-hidden glass-card">
    <!-- Form Tabs -->
    <div class="flex border-b border-neutral-100 bg-neutral-50/50">
        <button onclick="switchTab('basic')" id="tab-basic" class="flex-1 px-8 py-5 text-sm font-bold transition-all border-b-2 border-primary-600 text-primary-600">
            1. Basic Information
        </button>
        <button onclick="switchTab('sales')" id="tab-sales" class="flex-1 px-8 py-5 text-sm font-bold transition-all border-b-2 border-transparent text-neutral-400 hover:text-neutral-600">
            2. Sales Tracking
        </button>
        <button onclick="switchTab('project')" id="tab-project" class="flex-1 px-8 py-5 text-sm font-bold transition-all border-b-2 border-transparent text-neutral-400 hover:text-neutral-600">
            3. Project & Others
        </button>
    </div>

    <form id="addLeadForm" class="p-10">
        <!-- Basic Information Tab -->
        <div id="content-basic" class="tab-content space-y-8">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <div class="space-y-2">
                    <label class="text-sm font-bold text-neutral-700 ml-1">Company/Client Name *</label>
                    <input type="text" name="company_client_name" required placeholder="Enter company name" class="w-full bg-neutral-50 border-transparent rounded-2xl py-3 px-4 focus:bg-white focus:border-primary-100 focus:ring-4 focus:ring-primary-50 transition-all outline-none">
                </div>
                <div class="space-y-2">
                    <label class="text-sm font-bold text-neutral-700 ml-1">Contact Person *</label>
                    <input type="text" name="contact_person" required placeholder="Full name" class="w-full bg-neutral-50 border-transparent rounded-2xl py-3 px-4 focus:bg-white focus:border-primary-100 focus:ring-4 focus:ring-primary-50 transition-all outline-none">
                </div>
                <div class="space-y-2">
                    <label class="text-sm font-bold text-neutral-700 ml-1">Mobile Number *</label>
                    <input type="text" name="mobile_number" required placeholder="Phone number" class="w-full bg-neutral-50 border-transparent rounded-2xl py-3 px-4 focus:bg-white focus:border-primary-100 focus:ring-4 focus:ring-primary-50 transition-all outline-none">
                </div>
                <div class="space-y-2">
                    <label class="text-sm font-bold text-neutral-700 ml-1">Email ID</label>
                    <input type="email" name="email_id" placeholder="email@example.com" class="w-full bg-neutral-50 border-transparent rounded-2xl py-3 px-4 focus:bg-white focus:border-primary-100 focus:ring-4 focus:ring-primary-50 transition-all outline-none">
                </div>
                <div class="space-y-2">
                    <label class="text-sm font-bold text-neutral-700 ml-1">City</label>
                    <input type="text" name="city" placeholder="City name" class="w-full bg-neutral-50 border-transparent rounded-2xl py-3 px-4 focus:bg-white focus:border-primary-100 focus:ring-4 focus:ring-primary-50 transition-all outline-none">
                </div>
                <div class="space-y-2">
                    <label class="text-sm font-bold text-neutral-700 ml-1">State</label>
                    <input type="text" name="state" placeholder="State name" class="w-full bg-neutral-50 border-transparent rounded-2xl py-3 px-4 focus:bg-white focus:border-primary-100 focus:ring-4 focus:ring-primary-50 transition-all outline-none">
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
                    <label class="text-sm font-bold text-neutral-700 ml-1">Estimated Budget</label>
                    <input type="number" name="estimated_budget" placeholder="0.00" step="0.01" class="w-full bg-neutral-50 border-transparent rounded-2xl py-3 px-4 focus:bg-white focus:border-primary-100 focus:ring-4 focus:ring-primary-50 transition-all outline-none">
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
                    </select>
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
            <button type="button" onclick="window.location.href='leads.php'" class="px-8 py-3 bg-neutral-100 text-neutral-600 font-bold rounded-2xl hover:bg-neutral-200 transition-all">
                Cancel
            </button>
            <button type="submit" id="saveLeadBtn" class="px-8 py-3 bg-primary-600 text-white font-bold rounded-2xl hover:bg-primary-700 shadow-lg shadow-primary-200 transition-all flex items-center gap-2">
                <span>Save Lead Details</span>
                <svg id="loadingIcon" class="w-5 h-5 animate-spin hidden" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
            </button>
        </div>
    </form>
</div>

<script>
    function switchTab(tab) {
        document.querySelectorAll('.tab-content').forEach(c => c.classList.add('hidden'));
        document.getElementById('content-' + tab).classList.remove('hidden');
        
        document.querySelectorAll('[id^="tab-"]').forEach(t => {
            t.classList.remove('border-primary-600', 'text-primary-600');
            t.classList.add('border-transparent', 'text-neutral-400');
        });
        
        const activeTab = document.getElementById('tab-' + tab);
        activeTab.classList.remove('border-transparent', 'text-neutral-400');
        activeTab.classList.add('border-primary-600', 'text-primary-600');
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
                throw new Error(result.error || 'Failed to add lead');
            }
        } catch (error) {
            showToast(error.message, 'error');
            btn.disabled = false;
            icon.classList.add('hidden');
        }
    };
</script>

<?php layout_end(); ?>
