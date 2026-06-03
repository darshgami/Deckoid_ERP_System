<?php
require_once '../includes/middleware.php';
require_once '../includes/components/layout_wrapper.php';
requireAdmin();

layout_start('Staff Management - Deckoid ERP');
?>

<div class="mb-6 flex flex-col md:flex-row md:items-center justify-between gap-4">
    <div>
        <h1 class="text-xl lg:text-2xl font-semibold text-neutral-900 tracking-tight">Staff Management</h1>
        <p class="text-neutral-500 text-sm mt-0.5">Manage system users and their access levels.</p>
    </div>
    <button onclick="openAddStaffModal()" class="btn btn-primary text-sm">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M12 4v16m8-8H4"></path></svg>
        Add New Staff
    </button>
</div>
<!-- Filters Bar -->
<div class="bg-white p-4 lg:p-5 rounded-xl shadow-sm border border-neutral-100 mb-6">
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div class="relative group md:col-span-2">
            <span class="absolute inset-y-0 left-4 flex items-center text-neutral-400 group-focus-within:text-primary">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
            </span>
            <input type="text" id="search" placeholder="Search by name, username, or email..." 
                   class="w-full bg-neutral-50 border-transparent rounded-xl py-2.5 pl-11 pr-4 focus:bg-white focus:border-primary/20 focus:ring-4 focus:ring-primary/5 transition-all outline-none text-sm">
        </div>
        <button onclick="loadStaff(1)" class="btn btn-primary text-sm h-full">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"></path></svg>
            Apply Filter
        </button>
    </div>
</div>

<!-- Staff Table -->
<div class="bg-white rounded-xl shadow-sm border border-neutral-100 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead class="sticky top-0 z-10 bg-neutral-50/95 backdrop-blur-sm border-b border-neutral-100 shadow-sm">
                <tr>
                    <th class="px-6 py-3 text-left text-[11px] font-bold text-neutral-500 uppercase tracking-wider whitespace-nowrap">Full Name</th>
                    <th class="px-6 py-3 text-left text-[11px] font-bold text-neutral-500 uppercase tracking-wider whitespace-nowrap">Username</th>
                    <th class="px-6 py-3 text-left text-[11px] font-bold text-neutral-500 uppercase tracking-wider whitespace-nowrap">Role</th>
                    <th class="px-6 py-3 text-left text-[11px] font-bold text-neutral-500 uppercase tracking-wider whitespace-nowrap">Status</th>
                    <th class="px-6 py-3 text-left text-[11px] font-bold text-neutral-500 uppercase tracking-wider whitespace-nowrap">Last Login</th>
                    <th class="px-6 py-3 text-right text-[11px] font-bold text-neutral-500 uppercase tracking-wider whitespace-nowrap">Actions</th>
                </tr>
            </thead>
            <tbody id="staffTableBody" class="divide-y divide-neutral-50">
                <!-- Data loaded via JS -->
                <tr>
                    <td colspan="6" class="px-6 py-20 text-center">
                        <div class="flex flex-col items-center gap-2">
                            <div class="w-6 h-6 border-2 border-primary border-t-transparent rounded-full animate-spin"></div>
                            <span class="text-xs font-semibold text-neutral-400 uppercase tracking-widest">Loading Staff Data...</span>
                        </div>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <div class="px-6 py-4 border-t border-neutral-50 flex items-center justify-between bg-neutral-50/30">
        <div class="flex items-center gap-4">
            <p class="text-xs text-neutral-500 font-medium" id="paginationInfo">Showing 0 to 0 of 0 staff</p>
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
</div>

<!-- Add Staff Modal -->
<div id="staffModal" class="fixed inset-0 bg-neutral-900/40 backdrop-blur-sm hidden z-[100] transition-all duration-300">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="bg-white rounded-xl max-w-lg w-full shadow-2xl overflow-hidden transform transition-all duration-300 scale-95 opacity-0 flex flex-col max-h-[90vh]" id="modalContent">
            <div class="px-6 py-4 border-b border-neutral-100 flex items-center justify-between bg-white shrink-0">
                <div>
                    <h3 class="text-lg font-semibold text-neutral-900 tracking-tight">Add New Staff</h3>
                    <p class="text-xs text-neutral-500 mt-0.5">Create a new system user.</p>
                </div>
                <button onclick="closeStaffModal()" class="p-2 text-neutral-400 hover:text-neutral-900 hover:bg-neutral-50 rounded-lg transition-all">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>
            
            <div class="overflow-y-auto p-6">
                <form id="staffForm" class="space-y-4">
                    <input type="hidden" name="id" id="staff_id">
                    <div class="grid grid-cols-2 gap-4">
                        <div class="col-span-2 space-y-1.5">
                            <label class="text-[11px] font-semibold text-neutral-700 ml-1 uppercase tracking-wider">Full Name *</label>
                            <input type="text" name="full_name" id="staff_full_name" required placeholder="Enter full name" class="w-full bg-neutral-50 border-transparent rounded-xl py-2 px-4 focus:bg-white focus:border-primary-100 focus:ring-4 focus:ring-primary-50 transition-all outline-none text-sm">
                        </div>
                        
                        <div class="space-y-1.5">
                            <label class="text-[11px] font-semibold text-neutral-700 ml-1 uppercase tracking-wider">Username *</label>
                            <input type="text" name="username" id="staff_username" required placeholder="Choose a username" class="w-full bg-neutral-50 border-transparent rounded-xl py-2 px-4 focus:bg-white focus:border-primary-100 focus:ring-4 focus:ring-primary-50 transition-all outline-none text-sm">
                        </div>

                        <div class="space-y-1.5">
                            <label class="text-[11px] font-semibold text-neutral-700 ml-1 uppercase tracking-wider">Email Address *</label>
                            <input type="email" name="email" id="staff_email" required placeholder="staff@deckoid.com" class="w-full bg-neutral-50 border-transparent rounded-xl py-2 px-4 focus:bg-white focus:border-primary-100 focus:ring-4 focus:ring-primary-50 transition-all outline-none text-sm">
                        </div>

                        <div class="col-span-2 space-y-1.5">
                            <label class="text-[11px] font-semibold text-neutral-700 ml-1 uppercase tracking-wider">Role *</label>
                            <select name="role" id="staff_role" required class="w-full bg-neutral-50 border-transparent rounded-xl py-2 px-4 focus:bg-white focus:border-primary-100 focus:ring-4 focus:ring-primary-50 transition-all outline-none text-sm cursor-pointer">
                                <option value="staff">Staff</option>
                                <option value="admin">Admin</option>
                            </select>
                        </div>

                        <div class="space-y-1.5">
                            <label id="pass_label" class="text-[11px] font-semibold text-neutral-700 ml-1 uppercase tracking-wider">Password *</label>
                            <input type="password" name="password" id="staff_password" required placeholder="Min 8 chars" class="w-full bg-neutral-50 border-transparent rounded-xl py-2 px-4 focus:bg-white focus:border-primary-100 focus:ring-4 focus:ring-primary-50 transition-all outline-none text-sm">
                        </div>

                        <div class="space-y-1.5">
                            <label id="confirm_pass_label" class="text-[11px] font-semibold text-neutral-700 ml-1 uppercase tracking-wider">Confirm Password *</label>
                            <input type="password" name="confirm_password" id="staff_confirm_password" required placeholder="Repeat" class="w-full bg-neutral-50 border-transparent rounded-xl py-2 px-4 focus:bg-white focus:border-primary-100 focus:ring-4 focus:ring-primary-50 transition-all outline-none text-sm">
                        </div>
                    </div>

                    <div class="flex justify-end gap-3 mt-6 pt-2">
                        <button type="button" onclick="closeStaffModal()" class="btn btn-secondary text-sm">
                            Cancel
                        </button>
                        <button type="submit" id="saveStaffBtn" class="btn btn-primary px-8 py-2.5 rounded-xl shadow-lg shadow-primary/20 text-sm flex items-center gap-2">
                            <span>Create Staff Account</span>
                            <svg id="loadingIcon" class="w-4 h-4 animate-spin hidden" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    let currentPage = 1;
    let currentLimit = 10;

    function updateLimit(limit) {
        currentLimit = limit;
        loadStaff(1);
    }

    async function loadStaff(page = 1) {
        currentPage = page;
        const search = document.getElementById('search')?.value || '';
        try {
            const response = await fetch(`../api/staff.php?page=${page}&limit=${currentLimit}&search=${encodeURIComponent(search)}`);
            const res = await response.json();
            
            if (!res.success) throw new Error(res.message);
            
            const tbody = document.getElementById('staffTableBody');
            if (!res.data?.users || res.data.users.length === 0) {
                tbody.innerHTML = `<tr><td colspan="6" class="px-6 py-20 text-center text-neutral-400 font-medium">No staff accounts found.</td></tr>`;
                updatePaginationInfo({ total: 0 }, 'paginationInfo', 'staff');
                document.getElementById('pagination').innerHTML = '';
                return;
            }

            tbody.innerHTML = res.data.users.map(user => `
                <tr class="hover:bg-neutral-50/50 transition-colors group">
                    <td class="px-6 py-4">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 bg-primary-light text-primary rounded-lg flex-shrink-0 flex items-center justify-center font-semibold text-xs">
                                ${user.full_name.charAt(0)}
                            </div>
                            <div class="flex flex-col">
                                <span class="font-semibold text-neutral-900 text-sm">${user.full_name}</span>
                                <span class="text-[11px] text-neutral-400 font-medium">${user.email}</span>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4 text-sm text-neutral-600 font-medium">${user.username}</td>
                    <td class="px-6 py-4">
                        <span class="px-2 py-0.5 text-[11px] font-semibold rounded-md uppercase tracking-wider ${user.role === 'admin' ? 'bg-purple-50 text-purple-600' : 'bg-blue-50 text-blue-600'}">
                            ${user.role}
                        </span>
                    </td>
                    <td class="px-6 py-4">
                        <button onclick="toggleStatus('${user.id}', '${user.status}')" class="px-2.5 py-1 text-[11px] font-semibold rounded-lg uppercase tracking-wider transition-all ${user.status === 'active' ? 'bg-green-100 text-green-700 hover:bg-green-200' : 'bg-red-100 text-red-700 hover:bg-red-200'}">
                            ${user.status}
                        </button>
                    </td>
                    <td class="px-6 py-4 text-[11px] text-neutral-500 font-medium">
                        ${user.last_login_at ? formatDate(user.last_login_at) : 'Never'}
                    </td>
                    <td class="px-6 py-4 text-right whitespace-nowrap">
                        <div class="flex items-center justify-end gap-2">
                            <button onclick="editStaff('${user.id}', '${user.full_name}', '${user.email}', '${user.username}', '${user.role}')" class="p-1.5 text-neutral-400 hover:text-primary hover:bg-primary-light rounded-lg transition-all" title="Edit Staff">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
                            </button>
                            <button onclick="deleteStaff('${user.id}')" class="p-1.5 text-neutral-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition-all" title="Delete Staff">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                            </button>
                        </div>
                    </td>
                </tr>
            `).join('');

            updatePaginationInfo(res.data.pagination, 'paginationInfo', 'staff');
            renderPagination(res.data.pagination, loadStaff);
        } catch (error) {
            console.error('Error:', error);
            showToast(error.message || 'Failed to load staff data', 'error');
        }
    }

    function openAddStaffModal() {
        document.getElementById('staffForm').reset();
        document.getElementById('staff_id').value = '';
        document.querySelector('#staffModal h3').textContent = 'Add New Staff';
        document.querySelector('#staffModal p').textContent = 'Create a new system user.';
        document.querySelector('#saveStaffBtn span').textContent = 'Create Staff Account';
        
        // Password required for new staff
        document.getElementById('staff_password').required = true;
        document.getElementById('staff_confirm_password').required = true;
        document.getElementById('pass_label').textContent = 'Password *';
        document.getElementById('confirm_pass_label').textContent = 'Confirm Password *';

        const modal = document.getElementById('staffModal');
        const content = document.getElementById('modalContent');
        modal.classList.remove('hidden');
        setTimeout(() => {
            content.classList.remove('scale-95', 'opacity-0');
        }, 10);
    }

    function editStaff(id, fullName, email, username, role) {
        document.getElementById('staffForm').reset();
        document.getElementById('staff_id').value = id;
        document.getElementById('staff_full_name').value = fullName;
        document.getElementById('staff_email').value = email;
        document.getElementById('staff_username').value = username;
        document.getElementById('staff_role').value = role;
        
        document.querySelector('#staffModal h3').textContent = 'Edit Staff Member';
        document.querySelector('#staffModal p').textContent = 'Update staff account details.';
        document.querySelector('#saveStaffBtn span').textContent = 'Update Account';

        // Password optional for existing staff
        document.getElementById('staff_password').required = false;
        document.getElementById('staff_confirm_password').required = false;
        document.getElementById('pass_label').textContent = 'New Password (Optional)';
        document.getElementById('confirm_pass_label').textContent = 'Confirm New Password';

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
            const isEdit = data.id !== '';
            const response = await fetch('../api/staff.php', {
                method: isEdit ? 'PUT' : 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(data)
            });

            const res = await response.json();

            if (res.success) {
                showToast(res.message, 'success');
                closeStaffModal();
                loadStaff(currentPage);
                this.reset();
            } else {
                throw new Error(res.message || (isEdit ? 'Failed to update account' : 'Failed to create account'));
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

    async function toggleStatus(id, currentStatus) {
        const newStatus = currentStatus === 'active' ? 'inactive' : 'active';
        try {
            const response = await fetch('../api/staff.php', {
                method: 'PUT',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ id, status: newStatus })
            });
            const res = await response.json();
            if (res.message || res.success) {
                showToast(res.message || 'Status updated', 'success');
                loadStaff();
            } else {
                throw new Error(res.error || 'Failed to update status');
            }
        } catch (error) {
            showToast(error.message, 'error');
        }
    }

    function formatDate(dateStr) {
        if (!dateStr) return 'Never';
        const date = new Date(dateStr);
        return date.toLocaleDateString() + ' ' + date.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
    }
    
    document.addEventListener('DOMContentLoaded', () => loadStaff(1));
</script>

<?php layout_end(); ?>
