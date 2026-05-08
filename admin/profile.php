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
        
        <form class="space-y-5">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="space-y-1.5">
                    <label class="text-[10px] font-bold text-neutral-400 uppercase tracking-widest ml-1">Full Name</label>
                    <input type="text" value="<?php echo $_SESSION['full_name'] ?? ''; ?>" class="w-full px-4 py-2.5 bg-neutral-50 border border-neutral-100 rounded-xl focus:ring-4 focus:ring-primary-100 focus:border-primary-600 transition-all font-medium text-sm">
                </div>
                <div class="space-y-1.5">
                    <label class="text-[10px] font-bold text-neutral-400 uppercase tracking-widest ml-1">Username</label>
                    <input type="text" value="<?php echo $_SESSION['username'] ?? ''; ?>" readonly class="w-full px-4 py-2.5 bg-neutral-50 border border-neutral-100 rounded-xl opacity-60 cursor-not-allowed font-medium text-sm">
                </div>
            </div>

            <div class="space-y-1.5">
                <label class="text-[10px] font-bold text-neutral-400 uppercase tracking-widest ml-1">Email Address</label>
                <input type="email" placeholder="Enter your email" class="w-full px-4 py-2.5 bg-neutral-50 border border-neutral-100 rounded-xl focus:ring-4 focus:ring-primary-100 focus:border-primary-600 transition-all font-medium text-sm">
            </div>

            <div class="pt-2">
                <button type="button" onclick="showToast('Profile updated successfully!', 'success')" class="px-6 py-2.5 bg-primary-600 text-white font-bold rounded-xl hover:bg-primary-700 transition-all shadow-lg shadow-primary-200 text-sm">
                    Save Profile Changes
                </button>
            </div>
        </form>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-neutral-100 p-6 lg:p-8 glass-card">
        <h3 class="text-lg font-bold text-neutral-900 mb-6">Security</h3>
        
        <form class="space-y-5">
            <div class="space-y-1.5">
                <label class="text-[10px] font-bold text-neutral-400 uppercase tracking-widest ml-1">Current Password</label>
                <input type="password" class="w-full px-4 py-2.5 bg-neutral-50 border border-neutral-100 rounded-xl focus:ring-4 focus:ring-primary-100 focus:border-primary-600 transition-all font-medium text-sm">
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="space-y-1.5">
                    <label class="text-[10px] font-bold text-neutral-400 uppercase tracking-widest ml-1">New Password</label>
                    <input type="password" class="w-full px-4 py-2.5 bg-neutral-50 border border-neutral-100 rounded-xl focus:ring-4 focus:ring-primary-100 focus:border-primary-600 transition-all font-medium text-sm">
                </div>
                <div class="space-y-1.5">
                    <label class="text-[10px] font-bold text-neutral-400 uppercase tracking-widest ml-1">Confirm New Password</label>
                    <input type="password" class="w-full px-4 py-2.5 bg-neutral-50 border border-neutral-100 rounded-xl focus:ring-4 focus:ring-primary-100 focus:border-primary-600 transition-all font-medium text-sm">
                </div>
            </div>

            <div class="pt-2">
                <button type="button" onclick="showToast('Password changed successfully!', 'success')" class="px-6 py-2.5 bg-neutral-900 text-white font-bold rounded-xl hover:bg-black transition-all shadow-lg text-sm">
                    Update Password
                </button>
            </div>
        </form>
    </div>
</div>

<?php layout_end(); ?>
