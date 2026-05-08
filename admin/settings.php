<?php
require_once '../includes/middleware.php';
require_once '../includes/components/layout_wrapper.php';
requireAuth();

layout_start('Settings - Deckoid ERP');
?>

<div class="mb-10">
    <h1 class="text-4xl font-black text-neutral-900 tracking-tight">Settings</h1>
    <p class="text-neutral-500 mt-2 font-medium">Manage your profile and system preferences.</p>
</div>

<div class="space-y-10">
    <div class="bg-white rounded-[2.5rem] shadow-sm border border-neutral-100 p-10 glass-card">
        <h3 class="text-2xl font-bold text-neutral-900 mb-8">Personal Profile</h3>
        
        <form class="space-y-8">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <div class="space-y-4">
                    <label class="text-sm font-bold text-neutral-400 uppercase tracking-widest">Full Name</label>
                    <input type="text" value="<?php echo $_SESSION['full_name'] ?? ''; ?>" class="w-full px-6 py-4 bg-neutral-50 border border-neutral-100 rounded-2xl focus:ring-4 focus:ring-primary-100 focus:border-primary-600 transition-all font-medium">
                </div>
                <div class="space-y-4">
                    <label class="text-sm font-bold text-neutral-400 uppercase tracking-widest">Username</label>
                    <input type="text" value="<?php echo $_SESSION['username'] ?? ''; ?>" readonly class="w-full px-6 py-4 bg-neutral-50 border border-neutral-100 rounded-2xl opacity-60 cursor-not-allowed font-medium">
                </div>
            </div>

            <div class="space-y-4">
                <label class="text-sm font-bold text-neutral-400 uppercase tracking-widest">Email Address</label>
                <input type="email" placeholder="Enter your email" class="w-full px-6 py-4 bg-neutral-50 border border-neutral-100 rounded-2xl focus:ring-4 focus:ring-primary-100 focus:border-primary-600 transition-all font-medium">
            </div>

            <div class="pt-4">
                <button type="button" onclick="showToast('Profile updated successfully!', 'success')" class="px-10 py-5 bg-primary-600 text-white font-black rounded-2xl hover:bg-primary-700 transition-all shadow-xl shadow-primary-200">
                    Save Profile Changes
                </button>
            </div>
        </form>
    </div>

    <div class="bg-white rounded-[2.5rem] shadow-sm border border-neutral-100 p-10 glass-card">
        <h3 class="text-2xl font-bold text-neutral-900 mb-8">Security</h3>
        
        <form class="space-y-8">
            <div class="space-y-4">
                <label class="text-sm font-bold text-neutral-400 uppercase tracking-widest">Current Password</label>
                <input type="password" class="w-full px-6 py-4 bg-neutral-50 border border-neutral-100 rounded-2xl focus:ring-4 focus:ring-primary-100 focus:border-primary-600 transition-all font-medium">
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <div class="space-y-4">
                    <label class="text-sm font-bold text-neutral-400 uppercase tracking-widest">New Password</label>
                    <input type="password" class="w-full px-6 py-4 bg-neutral-50 border border-neutral-100 rounded-2xl focus:ring-4 focus:ring-primary-100 focus:border-primary-600 transition-all font-medium">
                </div>
                <div class="space-y-4">
                    <label class="text-sm font-bold text-neutral-400 uppercase tracking-widest">Confirm New Password</label>
                    <input type="password" class="w-full px-6 py-4 bg-neutral-50 border border-neutral-100 rounded-2xl focus:ring-4 focus:ring-primary-100 focus:border-primary-600 transition-all font-medium">
                </div>
            </div>

            <div class="pt-4">
                <button type="button" onclick="showToast('Password changed successfully!', 'success')" class="px-10 py-5 bg-neutral-900 text-white font-black rounded-2xl hover:bg-black transition-all shadow-xl">
                    Update Password
                </button>
            </div>
        </form>
    </div>
</div>

<?php layout_end(); ?>
