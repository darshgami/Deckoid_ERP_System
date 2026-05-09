<?php
require_once '../includes/middleware.php';
require_once '../includes/components/layout_wrapper.php';
requireAuth();

layout_start('Settings - Deckoid ERP');
?>

<div class="mb-6">
    <h1 class="text-2xl font-black text-neutral-900 tracking-tight">Profile Settings</h1>
    <p class="text-neutral-500 text-sm mt-1 font-medium">Manage your profile and system preferences.</p>
</div>

<div class="space-y-6">
    <div class="bg-white rounded-xl shadow-sm border border-neutral-100 p-6 lg:p-8 glass-card">
        <h3 class="text-lg font-bold text-neutral-900 mb-6">Personal Profile</h3>
        
        <form id="profileForm" class="space-y-5">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="space-y-1.5">
                    <label class="text-[10px] font-bold text-neutral-400 uppercase tracking-widest ml-1">Full Name</label>
                    <input type="text" name="full_name" id="prof_full_name" required class="w-full px-4 py-2.5 bg-neutral-50 border border-neutral-100 rounded-xl focus:ring-4 focus:ring-primary-100 focus:border-primary-600 transition-all font-medium text-sm">
                </div>
                <div class="space-y-1.5">
                    <label class="text-[10px] font-bold text-neutral-400 uppercase tracking-widest ml-1">Username</label>
                    <input type="text" id="prof_username" readonly class="w-full px-4 py-2.5 bg-neutral-50 border border-neutral-100 rounded-xl opacity-60 cursor-not-allowed font-medium text-sm">
                </div>
            </div>

            <div class="space-y-1.5">
                <label class="text-[10px] font-bold text-neutral-400 uppercase tracking-widest ml-1">Email Address</label>
                <input type="email" name="email" id="prof_email" required placeholder="Enter your email" class="w-full px-4 py-2.5 bg-neutral-50 border border-neutral-100 rounded-xl focus:ring-4 focus:ring-primary-100 focus:border-primary-600 transition-all font-medium text-sm">
            </div>

            <div class="pt-2">
                <button type="submit" id="saveProfileBtn" class="px-6 py-2.5 bg-primary-600 text-white font-bold rounded-xl hover:bg-primary-700 transition-all shadow-lg shadow-primary-200 text-sm flex items-center gap-2">
                    <span>Save Profile Changes</span>
                    <svg id="profLoading" class="w-4 h-4 animate-spin hidden" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                </button>
            </div>
        </form>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-neutral-100 p-6 lg:p-8 glass-card">
        <h3 class="text-lg font-bold text-neutral-900 mb-6">Security</h3>
        
        <form id="passwordForm" class="space-y-5">
            <div class="space-y-1.5">
                <label class="text-[10px] font-bold text-neutral-400 uppercase tracking-widest ml-1">Current Password</label>
                <input type="password" name="current_password" required class="w-full px-4 py-2.5 bg-neutral-50 border border-neutral-100 rounded-xl focus:ring-4 focus:ring-primary-100 focus:border-primary-600 transition-all font-medium text-sm">
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="space-y-1.5">
                    <label class="text-[10px] font-bold text-neutral-400 uppercase tracking-widest ml-1">New Password</label>
                    <input type="password" name="new_password" required class="w-full px-4 py-2.5 bg-neutral-50 border border-neutral-100 rounded-xl focus:ring-4 focus:ring-primary-100 focus:border-primary-600 transition-all font-medium text-sm">
                </div>
                <div class="space-y-1.5">
                    <label class="text-[10px] font-bold text-neutral-400 uppercase tracking-widest ml-1">Confirm New Password</label>
                    <input type="password" name="confirm_password" required class="w-full px-4 py-2.5 bg-neutral-50 border border-neutral-100 rounded-xl focus:ring-4 focus:ring-primary-100 focus:border-primary-600 transition-all font-medium text-sm">
                </div>
            </div>

            <div class="pt-2">
                <button type="submit" id="savePasswordBtn" class="px-6 py-2.5 bg-neutral-900 text-white font-bold rounded-xl hover:bg-black transition-all shadow-lg text-sm flex items-center gap-2">
                    <span>Update Password</span>
                    <svg id="passLoading" class="w-4 h-4 animate-spin hidden" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    async function loadProfile() {
        try {
            const response = await fetch('../api/profile.php');
            const res = await response.json();
            if (!res.success) throw new Error(res.message);
            
            const user = res.data.user;
            document.getElementById('prof_full_name').value = user.full_name;
            document.getElementById('prof_username').value = user.username;
            document.getElementById('prof_email').value = user.email || '';
        } catch (error) {
            showToast(error.message, 'error');
        }
    }

    document.getElementById('profileForm').onsubmit = async function(e) {
        e.preventDefault();
        const btn = document.getElementById('saveProfileBtn');
        const loading = document.getElementById('profLoading');
        
        btn.disabled = true;
        loading.classList.remove('hidden');

        const formData = new FormData(this);
        const data = Object.fromEntries(formData.entries());

        try {
            const response = await fetch('../api/profile.php', {
                method: 'PUT',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(data)
            });
            const res = await response.json();
            if (res.success) {
                showToast(res.message, 'success');
                // Update header name if it exists
                const headerName = document.querySelector('.user-name-display');
                if (headerName) headerName.textContent = data.full_name;
            } else {
                throw new Error(res.message);
            }
        } catch (error) {
            showToast(error.message, 'error');
        } finally {
            btn.disabled = false;
            loading.classList.add('hidden');
        }
    };

    document.getElementById('passwordForm').onsubmit = async function(e) {
        e.preventDefault();
        const btn = document.getElementById('savePasswordBtn');
        const loading = document.getElementById('passLoading');
        
        const formData = new FormData(this);
        const data = Object.fromEntries(formData.entries());

        if (data.new_password !== data.confirm_password) {
            showToast('New passwords do not match', 'error');
            return;
        }

        btn.disabled = true;
        loading.classList.remove('hidden');

        try {
            const response = await fetch('../api/profile.php', {
                method: 'PATCH',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(data)
            });
            const res = await response.json();
            if (res.success) {
                showToast(res.message, 'success');
                this.reset();
            } else {
                throw new Error(res.message);
            }
        } catch (error) {
            showToast(error.message, 'error');
        } finally {
            btn.disabled = false;
            loading.classList.add('hidden');
        }
    };

    document.addEventListener('DOMContentLoaded', loadProfile);
</script>

<?php layout_end(); ?>
