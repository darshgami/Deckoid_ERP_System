<?php
require_once '../includes/middleware.php';
require_once '../includes/components/layout_wrapper.php';
requireAdmin();

layout_start('Staff Management - Deckoid ERP');
?>

<div class="mb-6 flex flex-col md:flex-row md:items-center justify-between gap-4">
    <div>
        <h1 class="text-2xl font-bold text-neutral-900 tracking-tight">Staff Management</h1>
        <p class="text-neutral-500 text-sm mt-1">Manage system users and their access levels.</p>
    </div>
    <button onclick="openAddStaffModal()" class="px-4 py-2 bg-primary-600 text-white font-bold rounded-xl hover:bg-primary-700 shadow-lg shadow-primary-200 transition-all flex items-center gap-2 text-sm">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M12 4v16m8-8H4"></path></svg>
        Add New Staff
    </button>
</div>

<!-- Staff Table -->
<div class="bg-white rounded-xl shadow-sm border border-neutral-100 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead>
                <tr class="bg-neutral-50/50 text-left">
                    <th class="px-6 py-4 text-[10px] font-black text-neutral-400 uppercase tracking-widest whitespace-nowrap">Full Name</th>
                    <th class="px-6 py-4 text-[10px] font-black text-neutral-400 uppercase tracking-widest whitespace-nowrap">Username</th>
                    <th class="px-6 py-4 text-[10px] font-black text-neutral-400 uppercase tracking-widest whitespace-nowrap">Role</th>
                    <th class="px-6 py-4 text-[10px] font-black text-neutral-400 uppercase tracking-widest whitespace-nowrap">Last Login</th>
                    <th class="px-6 py-4 text-right text-[10px] font-black text-neutral-400 uppercase tracking-widest whitespace-nowrap">Actions</th>
                </tr>
            </thead>
            <tbody id="staffTableBody" class="divide-y divide-neutral-50">
                <!-- Data loaded via JS -->
                <tr>
                    <td colspan="6" class="px-6 py-20 text-center">
                        <div class="flex flex-col items-center gap-2">
                            <div class="w-6 h-6 border-2 border-primary-600 border-t-transparent rounded-full animate-spin"></div>
                            <span class="text-xs font-bold text-neutral-400 uppercase tracking-widest">Loading Staff Data...</span>
                        </div>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

<!-- Add Staff Modal -->
<div id="staffModal" class="fixed inset-0 bg-neutral-900/40 backdrop-blur-sm hidden z-[100] transition-all duration-300">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="bg-white rounded-2xl max-w-lg w-full shadow-2xl overflow-hidden transform transition-all duration-300 scale-95 opacity-0 flex flex-col max-h-[90vh]" id="modalContent">
            <div class="px-6 py-4 border-b border-neutral-100 flex items-center justify-between bg-neutral-50/50 shrink-0">
                <div>
                    <h3 class="text-xl font-bold text-neutral-900 tracking-tight">Add New Staff</h3>
                    <p class="text-xs text-neutral-500 mt-0.5">Create a new system user.</p>
                </div>
                <button onclick="closeStaffModal()" class="p-2 text-neutral-400 hover:text-neutral-900 hover:bg-neutral-100 rounded-lg transition-all">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>
            
            <div class="overflow-y-auto p-6">
                <form id="staffForm" class="space-y-4">
                    <div class="grid grid-cols-2 gap-4">
                        <div class="col-span-2 space-y-1.5">
                            <label class="text-[11px] font-bold text-neutral-700 ml-1 uppercase tracking-wider">Full Name *</label>
                            <input type="text" name="full_name" required placeholder="Enter full name" class="w-full bg-neutral-50 border-transparent rounded-xl py-2 px-4 focus:bg-white focus:border-primary-100 focus:ring-4 focus:ring-primary-50 transition-all outline-none text-sm">
                        </div>
                        
                        <div class="space-y-1.5">
                            <label class="text-[11px] font-bold text-neutral-700 ml-1 uppercase tracking-wider">Username *</label>
                            <input type="text" name="username" required placeholder="Choose a username" class="w-full bg-neutral-50 border-transparent rounded-xl py-2 px-4 focus:bg-white focus:border-primary-100 focus:ring-4 focus:ring-primary-50 transition-all outline-none text-sm">
                        </div>

                        <div class="space-y-1.5">
                            <label class="text-[11px] font-bold text-neutral-700 ml-1 uppercase tracking-wider">Email Address *</label>
                            <input type="email" name="email" required placeholder="staff@deckoid.com" class="w-full bg-neutral-50 border-transparent rounded-xl py-2 px-4 focus:bg-white focus:border-primary-100 focus:ring-4 focus:ring-primary-50 transition-all outline-none text-sm">
                        </div>

                        <div class="col-span-2 space-y-1.5">
                            <label class="text-[11px] font-bold text-neutral-700 ml-1 uppercase tracking-wider">Role *</label>
                            <select name="role" required class="w-full bg-neutral-50 border-transparent rounded-xl py-2 px-4 focus:bg-white focus:border-primary-100 focus:ring-4 focus:ring-primary-50 transition-all outline-none text-sm cursor-pointer">
                                <option value="staff">Staff</option>
                                <option value="admin">Admin</option>
                            </select>
                        </div>

                        <div class="space-y-1.5">
                            <label class="text-[11px] font-bold text-neutral-700 ml-1 uppercase tracking-wider">Password *</label>
                            <input type="password" name="password" required placeholder="Min 8 chars" class="w-full bg-neutral-50 border-transparent rounded-xl py-2 px-4 focus:bg-white focus:border-primary-100 focus:ring-4 focus:ring-primary-50 transition-all outline-none text-sm">
                        </div>

                        <div class="space-y-1.5">
                            <label class="text-[11px] font-bold text-neutral-700 ml-1 uppercase tracking-wider">Confirm Password *</label>
                            <input type="password" name="confirm_password" required placeholder="Repeat" class="w-full bg-neutral-50 border-transparent rounded-xl py-2 px-4 focus:bg-white focus:border-primary-100 focus:ring-4 focus:ring-primary-50 transition-all outline-none text-sm">
                        </div>
                    </div>

                    <div class="flex justify-end gap-3 mt-6 pt-2">
                        <button type="button" onclick="closeStaffModal()" class="px-6 py-2 bg-neutral-100 text-neutral-600 font-bold rounded-xl hover:bg-neutral-200 transition-all text-sm">
                            Cancel
                        </button>
                        <button type="submit" id="saveStaffBtn" class="px-6 py-2 bg-primary-600 text-white font-bold rounded-xl hover:bg-primary-700 shadow-lg shadow-primary-200 transition-all flex items-center gap-2 text-sm">
                            <span>Create Staff Account</span>
                            <svg id="loadingIcon" class="w-4 h-4 animate-spin hidden" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                        </button>
                    </div>
                </form>
            </div>
        </div>
        </div>
    </div>
</div>

<script>
    async function loadStaff() {
        try {
            const response = await fetch('../api/staff.php');
            const res = await response.json();
            
            if (!res.success) throw new Error(res.message);
            
            const tbody = document.getElementById('staffTableBody');
            if (!res.data?.users || res.data.users.length === 0) {
                tbody.innerHTML = `<tr><td colspan="6" class="px-6 py-20 text-center text-neutral-400">No staff accounts found.</td></tr>`;
                return;
            }

            tbody.innerHTML = res.data.users.map(user => `
                <tr class="hover:bg-neutral-50/50 transition-colors group">
                    <td class="px-6 py-4">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 bg-primary-100 text-primary-600 rounded-lg flex items-center justify-center font-black text-xs">
                                ${user.full_name.charAt(0)}
                            </div>
                            <div class="flex flex-col">
                                <span class="font-bold text-neutral-900 text-sm">${user.full_name}</span>
                                <span class="text-[10px] text-neutral-400 font-medium">${user.email}</span>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4 text-sm text-neutral-600 font-medium">${user.username}</td>
                    <td class="px-6 py-4">
                        <span class="px-2 py-0.5 text-[10px] font-black rounded-md uppercase tracking-widest ${user.role === 'admin' ? 'bg-purple-50 text-purple-600' : 'bg-blue-50 text-blue-600'}">
                            ${user.role}
                        </span>
                    </td>
                    <td class="px-6 py-4 text-right whitespace-nowrap">
                        <div class="flex items-center justify-end gap-2">
                            <button onclick="deleteStaff('${user.id}')" class="p-1.5 text-neutral-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition-all" title="Delete Staff">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                            </button>
                        </div>
                    </td>
                </tr>
            `).join('');
        } catch (error) {
            console.error('Error:', error);
            showToast(error.message || 'Failed to load staff data', 'error');
        }
    }

    function openAddStaffModal() {
        const modal = document.getElementById('staffModal');
        const content = document.getElementById('modalContent');
        modal.classList.remove('hidden');
        setTimeout(() => {
            content.classList.remove('scale-95', 'opacity-0');
        }, 10);
    }

    function closeStaffModal() {
        const modal = document.getElementById('staffModal');
        const content = document.getElementById('modalContent');
        content.classList.add('scale-95', 'opacity-0');
        setTimeout(() => {
            modal.classList.add('hidden');
        }, 300);
    }

    document.getElementById('staffForm').onsubmit = async function(e) {
        e.preventDefault();
        const btn = document.getElementById('saveStaffBtn');
        const icon = document.getElementById('loadingIcon');
        
        const formData = new FormData(this);
        const data = Object.fromEntries(formData.entries());

        if (data.password !== data.confirm_password) {
            showToast('Passwords do not match', 'error');
            return;
        }

        btn.disabled = true;
        icon.classList.remove('hidden');

        try {
            const response = await fetch('../api/staff.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(data)
            });

            const res = await response.json();

            if (res.success) {
                showToast(res.message, 'success');
                closeStaffModal();
                loadStaff();
                this.reset();
            } else {
                throw new Error(res.message || 'Failed to create account');
            }
        } catch (error) {
            showToast(error.message, 'error');
        } finally {
            btn.disabled = false;
            icon.classList.add('hidden');
        }
    };

    async function deleteStaff(id) {
        if (!confirm('Are you sure you want to delete this staff member? This action cannot be undone.')) return;

        try {
            const response = await fetch(`../api/staff.php?id=${id}`, {
                method: 'DELETE'
            });
            const res = await response.json();
            if (res.success) {
                showToast(res.message, 'success');
                loadStaff();
            } else {
                throw new Error(res.error || res.message || 'Failed to delete staff');
            }
        } catch (error) {
            showToast(error.message, 'error');
        }
    }

    document.addEventListener('DOMContentLoaded', loadStaff);
</script>

<?php layout_end(); ?>
