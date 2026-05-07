<?php
/**
 * Global Footer Component
 */
?>
    <!-- Sidebar Overlay (Mobile) -->
    <div id="sidebarOverlay" class="fixed inset-0 bg-neutral-900/40 backdrop-blur-sm z-[45] hidden lg:hidden" onclick="toggleMobileSidebar()"></div>

    <script>
        // Sidebar Collapse (Desktop)
        function toggleSidebarCollapse() {
            const sidebar = document.getElementById('sidebar');
            const mainContent = document.querySelector('.lg\\:ml-72');
            const collapseIcon = document.getElementById('collapseIcon');
            
            sidebar.classList.toggle('sidebar-collapsed');
            
            if (sidebar.classList.contains('sidebar-collapsed')) {
                mainContent.style.marginLeft = '100px';
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
