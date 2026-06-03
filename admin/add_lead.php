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
    <h1 class="text-xl lg:text-2xl font-semibold text-neutral-900 tracking-tight">Add New Lead</h1>
    <p class="text-neutral-500 text-sm mt-0.5">Create a new potential business opportunity.</p>
</div>

<div class="bg-white rounded-xl shadow-sm border border-neutral-100 overflow-hidden glass-card">
    <!-- Form Tabs -->
    <div class="flex border-b border-neutral-100 bg-neutral-50/50">
        <button id="tab-basic" class="flex-1 px-6 py-4 text-[11px] font-semibold transition-all border-b-2 border-primary text-primary uppercase tracking-wider cursor-default" type="button">
            1. Basic Information
        </button>
        <button id="tab-sales" class="flex-1 px-6 py-4 text-[11px] font-semibold transition-all border-b-2 border-transparent text-neutral-400 uppercase tracking-wider cursor-default" type="button">
            2. Sales Tracking
        </button>
        <button id="tab-project" class="flex-1 px-6 py-4 text-[11px] font-semibold transition-all border-b-2 border-transparent text-neutral-400 uppercase tracking-wider cursor-default" type="button">
            3. Project & Others
        </button>
    </div>

    <form id="addLeadForm" class="p-6 lg:p-8" novalidate>
        <!-- Basic Information Tab -->
        <div id="content-basic" class="tab-content space-y-6">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <div class="space-y-1.5">
                    <label class="text-[11px] font-semibold text-neutral-700 ml-1 uppercase tracking-wider">Lead ID</label>
                    <input type="text" readonly value="Auto-generated" class="w-full bg-neutral-100 border-transparent rounded-xl py-2.5 px-4 text-neutral-500 transition-all outline-none text-sm font-medium cursor-not-allowed">
                </div>
                <div class="space-y-1.5">
                    <label class="text-[11px] font-semibold text-neutral-700 ml-1 uppercase tracking-wider">Lead Date *</label>
                    <input type="date" name="lead_date" required class="w-full bg-neutral-50 border-transparent rounded-xl py-2.5 px-4 focus:bg-white focus:border-primary focus:ring-4 focus:ring-primary/5 transition-all outline-none text-sm">
                </div>
                <div class="space-y-1.5">
                    <label class="text-[11px] font-semibold text-neutral-700 ml-1 uppercase tracking-wider">Company *</label>
                    <input type="text" name="company" required placeholder="Enter company name" class="w-full bg-neutral-50 border-transparent rounded-xl py-2.5 px-4 focus:bg-white focus:border-primary focus:ring-4 focus:ring-primary/5 transition-all outline-none text-sm">
                </div>
                <div class="space-y-1.5">
                    <label class="text-[11px] font-semibold text-neutral-700 ml-1 uppercase tracking-wider">Contact Person *</label>
                    <input type="text" name="contact_person" required placeholder="Full name" class="w-full bg-neutral-50 border-transparent rounded-xl py-2.5 px-4 focus:bg-white focus:border-primary focus:ring-4 focus:ring-primary/5 transition-all outline-none text-sm">
                </div>
                <div class="space-y-1.5">
                    <label class="text-[11px] font-semibold text-neutral-700 ml-1 uppercase tracking-wider">Mobile Number *</label>
                    <input type="text" name="mobile_number" required placeholder="Phone number" class="w-full bg-neutral-50 border-transparent rounded-xl py-2.5 px-4 focus:bg-white focus:border-primary focus:ring-4 focus:ring-primary/5 transition-all outline-none text-sm">
                </div>
                <div class="space-y-1.5">
                    <label class="text-[11px] font-semibold text-neutral-700 ml-1 uppercase tracking-wider">email_id ID</label>
                    <input type="email_id" name="email_id" placeholder="email_id@example.com" class="w-full bg-neutral-50 border-transparent rounded-xl py-2.5 px-4 focus:bg-white focus:border-primary focus:ring-4 focus:ring-primary/5 transition-all outline-none text-sm">
                </div>

            </div>
        </div>

        <!-- Sales Tracking Tab -->
        <div id="content-sales" class="tab-content hidden space-y-6">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <div class="space-y-1.5">
                    <label class="text-[11px] font-semibold text-neutral-700 ml-1 uppercase tracking-wider">Lead Category *</label>
                    <select name="lead_category" required class="w-full bg-neutral-50 border-transparent rounded-xl py-2.5 px-4 focus:bg-white focus:border-primary-100 focus:ring-4 focus:ring-primary-50 transition-all outline-none cursor-pointer text-sm">
                        <option value="Hot">🔥 Hot</option>
                        <option value="Warm">☀️ Warm</option>
                        <option value="Cold">❄️ Cold</option>
                    </select>
                </div>
                <div class="space-y-1.5">
                    <label class="text-[11px] font-semibold text-neutral-700 ml-1 uppercase tracking-wider">Lead lead_status *</label>
                    <select name="lead_status" required class="w-full bg-neutral-50 border-transparent rounded-xl py-2.5 px-4 focus:bg-white focus:border-primary-100 focus:ring-4 focus:ring-primary-50 transition-all outline-none cursor-pointer text-sm">
                        <option value="New">New</option>
                        <option value="Next Follow Up">Next Follow Up</option>
                        <option value="Convert">Convert</option>
                        <option value="Lost">Lost</option>
                    </select>
                </div>
                <div class="space-y-1.5">
                    <label class="text-[11px] font-semibold text-neutral-700 ml-1 uppercase tracking-wider">Assigned To *</label>
                    <select name="assigned_to" required class="w-full bg-neutral-50 border-transparent rounded-xl py-2.5 px-4 focus:bg-white focus:border-primary-100 focus:ring-4 focus:ring-primary-50 transition-all outline-none cursor-pointer text-sm">
                        <option value="">Select Staff</option>
                        <?php foreach ($users as $user): ?>
                            <option value="<?= $user['id'] ?>"><?= htmlspecialchars($user['full_name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="space-y-1.5">
                    <label class="text-[11px] font-semibold text-neutral-700 ml-1 uppercase tracking-wider">Estimated Budget</label>
                    <input type="number" name="estimated_budget" placeholder="0.00" step="0.01" class="w-full bg-neutral-50 border-transparent rounded-xl py-2.5 px-4 focus:bg-white focus:border-primary-100 focus:ring-4 focus:ring-primary-50 transition-all outline-none text-sm">
                </div>
                <div class="space-y-1.5">
                    <label class="text-[11px] font-semibold text-neutral-700 ml-1 uppercase tracking-wider">Next Follow-up Date</label>
                    <input type="date" name="next_followup_date" class="w-full bg-neutral-50 border-transparent rounded-xl py-2.5 px-4 focus:bg-white focus:border-primary-100 focus:ring-4 focus:ring-primary-50 transition-all outline-none text-sm">
                </div>

        </div>
        </div>

        <!-- Project & Others Tab -->
        <div id="content-project" class="tab-content hidden space-y-6">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <div class="space-y-1.5">
                    <label class="text-[11px] font-semibold text-neutral-700 ml-1 uppercase tracking-wider">City</label>
                    <input type="text" name="city" placeholder="City name" class="w-full bg-neutral-50 border-transparent rounded-xl py-2.5 px-4 focus:bg-white focus:border-primary-100 focus:ring-4 focus:ring-primary-50 transition-all outline-none text-sm">
                </div>
                <div class="space-y-1.5">
                    <label class="text-[11px] font-semibold text-neutral-700 ml-1 uppercase tracking-wider">State</label>
                    <input type="text" name="state" placeholder="State name" class="w-full bg-neutral-50 border-transparent rounded-xl py-2.5 px-4 focus:bg-white focus:border-primary-100 focus:ring-4 focus:ring-primary-50 transition-all outline-none text-sm">
                </div>
                <div class="space-y-1.5">
                    <label class="text-[11px] font-semibold text-neutral-700 ml-1 uppercase tracking-wider">Payment lead_status *</label>
                    <select name="payment_status" required class="w-full bg-neutral-50 border-transparent rounded-xl py-2.5 px-4 focus:bg-white focus:border-primary-100 focus:ring-4 focus:ring-primary-50 transition-all outline-none cursor-pointer text-sm">
                        <option value="Pending">Pending</option>
                        <option value="Partial">Partial</option>
                        <option value="Completed">Completed</option>
                    </select>
                </div>
                <div class="space-y-1.5">
                    <label class="text-[11px] font-semibold text-neutral-700 ml-1 uppercase tracking-wider">Reference By</label>
                    <input type="text" name="reference_by" placeholder="Referral name" class="w-full bg-neutral-50 border-transparent rounded-xl py-2.5 px-4 focus:bg-white focus:border-primary-100 focus:ring-4 focus:ring-primary-50 transition-all outline-none text-sm">
                </div>
                <div class="md:col-span-2 lg:col-span-3 space-y-1.5">
                    <label class="text-[11px] font-semibold text-neutral-700 ml-1 uppercase tracking-wider">Remarks</label>
                    <textarea name="remarks" rows="2" placeholder="Additional notes..." class="w-full bg-neutral-50 border-transparent rounded-xl py-2.5 px-4 focus:bg-white focus:border-primary-100 focus:ring-4 focus:ring-primary-50 transition-all outline-none resize-none text-sm"></textarea>
                </div>
            </div>
        </div>

        <div class="flex items-center justify-between mt-6 pt-6 border-t border-neutral-100">
            <div class="flex gap-3">
                <button type="button" id="prevBtn" onclick="navigate('prev')" class="btn btn-secondary px-6 py-2.5 rounded-xl hidden text-sm font-semibold">
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

<script>
    let currentTab = 'basic';
    const tabs = ['basic', 'sales', 'project'];

    function switchTab(tab) {
        currentTab = tab;
        document.querySelectorAll('.tab-content').forEach(c => c.classList.add('hidden'));
        document.getElementById('content-' + tab).classList.remove('hidden');
        
        document.querySelectorAll('[id^="tab-"]').forEach(t => {
            t.classList.remove('border-primary', 'text-primary');
            t.classList.add('border-transparent', 'text-neutral-400');
        });
        
        const activeTab = document.getElementById('tab-' + tab);
        activeTab.classList.remove('border-transparent', 'text-neutral-400');
        activeTab.classList.add('border-primary', 'text-primary');

        // Update Buttons Visibility
        const prevBtn = document.getElementById('prevBtn');
        const nextBtn = document.getElementById('nextBtn');
        const saveBtn = document.getElementById('saveLeadBtn');

        if (tab === 'basic') {
            prevBtn.classList.add('hidden');
            nextBtn.classList.remove('hidden');
            saveBtn.classList.add('hidden');
        } else if (tab === 'sales') {
            prevBtn.classList.remove('hidden');
            nextBtn.classList.remove('hidden');
            saveBtn.classList.add('hidden');
        } else if (tab === 'project') {
            prevBtn.classList.remove('hidden');
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
        
        // Clear previous validation states
        this.querySelectorAll('.input-error').forEach(el => el.classList.remove('input-error'));
        this.querySelectorAll('.error-message').forEach(el => el.remove());

        const formData = new FormData(this);
        const data = Object.fromEntries(formData.entries());

        // Final validation before submission
        let isValid = true;
        const setError = (name, msg) => {
            const input = this.querySelector(`[name="${name}"]`);
            if (input) {
                input.classList.add('input-error');
                const err = document.createElement('p');
                err.className = 'error-message';
                err.innerHTML = `<svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg> ${msg}`;
                input.closest('.space-y-1\\.5')?.appendChild(err);
            }
            isValid = false;
        };

        if (!data.lead_date) setError('lead_date', 'Lead date is required');
        if (!data.company || data.company.length < 3) setError('company', 'Company name must be at least 3 characters');
        if (!data.contact_person || data.contact_person.length < 3) setError('contact_person', 'Contact person required');
        if (!data.mobile_number || !/^[0-9]{10}$/.test(data.mobile_number)) setError('mobile_number', 'Valid mobile number required (10 digits)');

        if (!isValid) {
            showToast('Please fix errors in the form', 'error');
            // If error is in another tab, switch to it
            const firstErr = this.querySelector('.input-error');
            if (firstErr) {
                const tabContent = firstErr.closest('.tab-content');
                if (tabContent) {
                    const tabId = tabContent.id.replace('content-', '');
                    switchTab(tabId);
                    firstErr.focus();
                }
            }
            return;
        }

        btn.disabled = true;
        icon.classList.remove('hidden');

        try {
            const response = await fetch('../api/leads.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(data)
            });

            const result = await response.json();

            if (result.success) {
                showToast('Lead added successfully!', 'success');
                setTimeout(() => window.location.href = 'leads.php', 1000);
            } else {
                showToast(result.message || 'Failed to add lead', 'error');
                // Check if it's a mobile duplicate error
                if (result.message && result.message.toLowerCase().includes('mobile')) {
                    switchTab('basic');
                    setError('mobile_number', result.message);
                }
            }
        } catch (error) {
            showToast('System error occurred', 'error');
        } finally {
            btn.disabled = false;
            icon.classList.add('hidden');
        }
    };
    document.addEventListener('DOMContentLoaded', () => {
        switchTab('basic');
    });
</script>

<?php layout_end(); ?>
