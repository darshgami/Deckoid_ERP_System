<?php
/**
 * Global Navbar Component
 */
?>
<header class="h-20 bg-white/80 backdrop-blur-md border-b border-neutral-200 sticky top-0 z-40 px-8 flex items-center justify-between transition-all duration-300" id="mainNavbar">
    <div class="flex items-center gap-6">
        <!-- Mobile Toggle -->
        <button onclick="toggleMobileSidebar()" class="lg:hidden p-2.5 rounded-2xl hover:bg-neutral-100 text-neutral-500 transition-all border border-neutral-100" aria-label="Open Sidebar">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M4 6h16M4 12h16M4 18h16" />
            </svg>
        </button>

        <!-- Search -->
        <!-- <div class="w-64 md:w-96 relative group hidden sm:block">
            <span class="absolute inset-y-0 left-5 flex items-center text-neutral-400 group-focus-within:text-primary-600 transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
            </span>
            <input type="text" placeholder="Search anything..." autocomplete="off"
                   class="w-full bg-neutral-100/50 border-transparent rounded-3xl py-3 pl-14 pr-6 focus:bg-white focus:border-primary-100 focus:ring-8 focus:ring-primary-50 transition-all outline-none text-sm font-bold placeholder:text-neutral-400">
        </div> -->
    </div>

    <!-- Right: Actions -->
    <div class="flex items-center gap-4 md:gap-6">

        <!-- User Profile Dropdown -->
        <div class="relative" id="userDropdown">
            <button onclick="toggleUserDropdown()" class="flex items-center gap-4 p-1.5 hover:bg-neutral-50 rounded-xl transition-all border border-transparent hover:border-neutral-100 group" aria-haspopup="true">
                <div class="w-10 h-10 bg-primary rounded-xl shadow-sm flex items-center justify-center text-white text-sm font-semibold transition-transform group-hover:scale-105">
                    <?php echo strtoupper(substr($_SESSION['username'] ?? 'A', 0, 1)); ?>
                </div>
                <div class="text-left hidden md:block pr-1">
                    <p class="text-sm font-semibold text-neutral-900 leading-none"><?php echo $_SESSION['username'] ?? 'Admin'; ?></p>
                    <div class="flex items-center gap-1.5 mt-1">
                        <span class="w-1.5 h-1.5 rounded-full bg-green-500"></span>
                        <span class="text-[10px] font-semibold text-neutral-400 uppercase tracking-widest">Active</span>
                    </div>
                </div>
                <svg class="w-4 h-4 text-neutral-400 mr-1 transition-transform group-hover:translate-y-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                </svg>
            </button>

            <!-- Dropdown Menu -->
            <div id="dropdownMenu" class="absolute right-0 mt-4 w-64 bg-white rounded-xl shadow-md border border-neutral-100 py-4 hidden transform transition-all duration-300 scale-95 opacity-0 origin-top-right z-[60]">
                <div class="px-6 py-4 border-b border-neutral-50 mb-3">
                    <p class="text-[10px] font-semibold text-neutral-400 uppercase tracking-widest mb-1.5">Authenticated User</p>
                    <p class="text-sm font-semibold text-neutral-900 truncate"><?php echo $_SESSION['full_name'] ?? 'Administrator'; ?></p>
                    <p class="text-xs font-medium text-neutral-400 mt-0.5"><?php echo $_SESSION['username'] ?? 'admin@deckoid.com'; ?></p>
                </div>
                <div class="px-2 space-y-1">
                    <a href="profile.php" class="flex items-center gap-3 px-4 py-3 text-sm font-medium text-neutral-600 hover:bg-primary-light hover:text-primary rounded-xl transition-all">
                        <div class="w-8 h-8 bg-neutral-50 rounded-lg flex items-center justify-center">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                        </div>
                        My Profile
                    </a>
                    <a href="javascript:void(0)" onclick="logout()" class="flex items-center gap-3 px-4 py-3 text-sm font-medium text-red-600 hover:bg-red-50 rounded-xl transition-all">
                        <div class="w-8 h-8 bg-red-50 rounded-lg flex items-center justify-center">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
                        </div>
                        Log Out Account
                    </a>
                </div>
            </div>
        </div>
    </div>
</header>
