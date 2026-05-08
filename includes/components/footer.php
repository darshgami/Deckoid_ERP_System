<?php
/**
 * Global Footer Component
 */
?>
    <!-- Sidebar Overlay (Mobile) -->
    <div id="sidebarOverlay" class="fixed inset-0 bg-neutral-900/40 backdrop-blur-sm z-[45] hidden lg:hidden" onclick="toggleMobileSidebar()"></div>

    <!-- Global Toast Container -->
    <div id="toastContainer" class="fixed bottom-10 right-10 z-[200] flex flex-col gap-4"></div>

    <script>
        // Global Toast System
        window.showToast = function(message, type = 'success') {
            const container = document.getElementById('toastContainer');
            const toast = document.createElement('div');
            
            const bgColor = type === 'success' ? 'bg-green-600' : 'bg-red-600';
            const icon = type === 'success' 
                ? '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M5 13l4 4L19 7"></path></svg>'
                : '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M6 18L18 6M6 6l12 12"></path></svg>';

            toast.className = `${bgColor} text-white px-6 py-4 rounded-2xl shadow-2xl flex items-center gap-3 transform transition-all duration-300 translate-x-full opacity-0`;
            toast.innerHTML = `
                <div class="w-8 h-8 bg-white/20 rounded-xl flex items-center justify-center">
                    ${icon}
                </div>
                <span class="font-bold text-sm">${message}</span>
            `;

            container.appendChild(toast);

            // Animate In
            setTimeout(() => {
                toast.classList.remove('translate-x-full', 'opacity-0');
            }, 10);

            // Auto Remove
            setTimeout(() => {
                toast.classList.add('translate-x-full', 'opacity-0');
                setTimeout(() => toast.remove(), 300);
            }, 4000);
        };

        // For backward compatibility with legacy calls
        window.toast = window.showToast;

        // Sidebar Collapse (Desktop)

        function toggleSidebarCollapse() {
            const sidebar = document.getElementById('sidebar');
            const mainContent = document.querySelector('.lg\\:ml-72');
            const collapseIcon = document.getElementById('collapseIcon');
            
            sidebar.classList.toggle('sidebar-collapsed');
            
            if (sidebar.classList.contains('sidebar-collapsed')) {
                mainContent.style.marginLeft = '88px';
                collapseIcon.style.transform = 'rotate(180deg)';
                localStorage.setItem('sidebarCollapsed', 'true');
            } else {
                mainContent.style.marginLeft = '18rem'; // 72 * 0.25rem
                collapseIcon.style.transform = 'rotate(0deg)';
                localStorage.setItem('sidebarCollapsed', 'false');
            }
        }

        // Mobile Sidebar Toggle
        function toggleMobileSidebar() {
            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('sidebarOverlay');
            
            sidebar.classList.toggle('-translate-x-full');
            overlay.classList.toggle('hidden');
        }

        // User Dropdown Toggle
        function toggleUserDropdown() {
            const menu = document.getElementById('dropdownMenu');
            menu.classList.toggle('hidden');
            setTimeout(() => {
                menu.classList.toggle('scale-95');
                menu.classList.toggle('opacity-0');
            }, 10);
        }

        // Close dropdown when clicking outside
        window.addEventListener('click', function(e) {
            const dropdown = document.getElementById('userDropdown');
            const menu = document.getElementById('dropdownMenu');
            if (!dropdown.contains(e.target)) {
                menu.classList.add('hidden', 'scale-95', 'opacity-0');
            }
        });

        // Restore sidebar state on load
        document.addEventListener('DOMContentLoaded', () => {
            if (localStorage.getItem('sidebarCollapsed') === 'true') {
                toggleSidebarCollapse();
            }
        });

        async function logout() {
            if (confirm('Are you sure you want to logout?')) {
                try {
                    await fetch('../api/auth.php/logout', { method: 'POST' });
                    window.location.href = 'login.php';
                } catch (error) {
                    console.error('Logout failed:', error);
                    window.location.href = 'login.php';
                }
            }
        }
    </script>
</body>
</html>
