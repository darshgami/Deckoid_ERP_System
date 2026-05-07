<?php
/**
 * Global Navbar Component
 */
?>
<header class="h-20 bg-white/80 backdrop-blur-md border-b border-neutral-200 sticky top-0 z-40 px-8 flex items-center justify-between transition-all duration-300" id="mainNavbar">
    <div class="flex items-center gap-6">
        <!-- Mobile Toggle -->
        <button onclick="toggleMobileSidebar()" class="lg:hidden p-2.5 rounded-xl hover:bg-neutral-100 text-neutral-500 transition-all" aria-label="Open Sidebar">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
            </svg>
        </button>

        <!-- Search -->
        <div class="w-64 md:w-96 relative group hidden sm:block">
            <span class="absolute inset-y-0 left-4 flex items-center text-neutral-400 group-focus-within:text-primary-500 transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
            </span>
            <input type="text" placeholder="Search leads, tasks, reports..." 
                   class="w-full bg-neutral-100/50 border-transparent rounded-2xl py-2.5 pl-12 pr-4 focus:bg-white focus:border-primary-100 focus:ring-4 focus:ring-primary-50 transition-all outline-none text-sm font-medium">
        </div>
    </div>

    <!-- Right: Actions -->
    <div class="flex items-center gap-4 md:gap-6">
        <!-- Notifications -->
        <div class="relative">
            <button class="relative p-2.5 text-neutral-500 hover:bg-neutral-100 rounded-2xl transition-all" aria-label="Notifications">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                </svg>
                <span class="absolute top-2.5 right-2.5 w-2.5 h-2.5 bg-red-500 border-2 border-white rounded-full"></span>
            </button>
        </div>

        <div class="h-8 w-[1px] bg-neutral-200 mx-2 hidden sm:block"></div>

        <!-- User Profile Dropdown -->
        <div class="relative" id="userDropdown">
            <button onclick="toggleUserDropdown()" class="flex items-center gap-3 p-1.5 hover:bg-neutral-100 rounded-2xl transition-all border border-transparent hover:border-neutral-200" aria-haspopup="true">
                <div class="w-9 h-9 bg-primary-600 rounded-xl shadow-lg shadow-primary-200 flex items-center justify-center text-white text-sm font-bold">
                    <?php echo strtoupper(substr($_SESSION['username'] ?? 'A', 0, 1)); ?>
                </div>
                <div class="text-left hidden md:block pr-2">
                    <p class="text-sm font-bold text-neutral-900 leading-none"><?php echo $_SESSION['username'] ?? 'Admin'; ?></p>
                    <p class="text-[10px] font-bold text-neutral-400 uppercase tracking-widest mt-1">Online</p>
                </div>
                <svg class="w-4 h-4 text-neutral-400 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                </svg>
            </button>

            <!-- Dropdown Menu -->
            <div id="dropdownMenu" class="absolute right-0 mt-3 w-56 bg-white rounded-3xl shadow-2xl shadow-neutral-200/50 border border-neutral-100 py-3 hidden transform transition-all duration-200 scale-95 opacity-0 origin-top-right z-[60]">
                <div class="px-5 py-3 border-b border-neutral-50 mb-2">
                    <p class="text-xs font-bold text-neutral-400 uppercase tracking-widest mb-1">Signed in as</p>
                    <p class="text-sm font-bold text-neutral-900 truncate"><?php echo $_SESSION['username'] ?? 'admin@deckoid.com'; ?></p>
                </div>
                <a href="profile.php" class="flex items-center gap-3 px-5 py-2.5 text-sm font-semibold text-neutral-600 hover:bg-neutral-50 hover:text-primary-600 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                    My Profile
                </a>
                <a href="settings.php" class="flex items-center gap-3 px-5 py-2.5 text-sm font-semibold text-neutral-600 hover:bg-neutral-50 hover:text-primary-600 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path></svg>
                    Settings
                </a>
                <div class="h-[1px] bg-neutral-50 my-2"></div>
                <a href="javascript:void(0)" onclick="logout()" class="flex items-center gap-3 px-5 py-2.5 text-sm font-semibold text-red-600 hover:bg-red-50 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
                    Log Out
                </a>
            </div>
        </div>
    </div>
</header>
