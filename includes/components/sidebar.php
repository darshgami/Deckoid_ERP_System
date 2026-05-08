<?php
/**
 * Global Sidebar Component
 */
$current_page = basename($_SERVER['PHP_SELF']);

$menuItems = [
    ['label' => 'Dashboard', 'url' => 'dashboard.php', 'icon' => 'M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6'],
    ['label' => 'Add Lead', 'url' => 'add_lead.php', 'icon' => 'M12 4v16m8-8H4'],
    ['label' => 'Lead List', 'url' => 'leads.php', 'icon' => 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2'],
    ['label' => 'Followups', 'url' => 'followups.php', 'icon' => 'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z'],
    ['label' => 'Activity Logs', 'url' => 'logs.php', 'icon' => 'M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z'],
    ['label' => 'Settings', 'url' => 'settings.php', 'icon' => 'M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z'],
];
?>
<aside class="fixed inset-y-0 left-0 w-72 bg-white border-r border-neutral-200 z-50 transition-all duration-300 transform lg:translate-x-0 -translate-x-full overflow-hidden" id="sidebar" role="navigation" aria-label="Main Navigation">
    <div class="flex flex-col h-full">
        <!-- Logo Area -->
        <div class="p-8 flex items-center justify-between">
            <div class="flex items-center gap-3 overflow-hidden">
                <div class="w-10 h-10 bg-primary-600 rounded-xl flex-shrink-0 flex items-center justify-center shadow-lg shadow-primary-200">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                    </svg>
                </div>
                <span class="text-xl font-bold tracking-tight text-neutral-900 sidebar-text transition-opacity duration-300">Deckoid<span class="text-primary-600">ERP</span></span>
            </div>
            <!-- Toggle Button (Desktop Collapsible) -->
            <button onclick="toggleSidebarCollapse()" class="hidden lg:flex p-1.5 rounded-lg hover:bg-neutral-100 text-neutral-400 transition-colors" aria-label="Toggle Sidebar">
                <svg class="w-5 h-5 transition-transform duration-300" id="collapseIcon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 19l-7-7 7-7m8 14l-7-7 7-7" />
                </svg>
            </button>
        </div>

        <!-- Navigation -->
        <nav class="flex-1 px-4 py-4 space-y-1 overflow-y-auto custom-scrollbar">
            <?php foreach ($menuItems as $item): ?>
                <?php 
                $isActive = false;
                if ($item['url'] == $current_page) {
                    // Direct match and no action in URL (e.g. Dashboard)
                    if (!isset($_GET['action'])) $isActive = true;
                } elseif (strpos($item['url'], '?') !== false) {
                    // Match with query params (e.g. Add Lead)
                    $parts = parse_url($item['url']);
                    if ($parts['path'] == $current_page) {
                        parse_str($parts['query'], $query);
                        $match = true;
                        foreach ($query as $k => $v) {
                            if (!isset($_GET[$k]) || $_GET[$k] != $v) {
                                $match = false;
                                break;
                            }
                        }
                        if ($match) $isActive = true;
                    }
                } elseif ($item['url'] == $current_page && !isset($_GET['action'])) {
                    $isActive = true;
                }
                
                // Fallback for Lead List which is leads.php
                if ($item['label'] == 'Lead List' && $current_page == 'leads.php' && !isset($_GET['action'])) {
                    $isActive = true;
                }
                ?>
                <a href="<?php echo $item['url']; ?>" 
                   class="group flex items-center gap-4 px-4 py-3.5 rounded-2xl transition-all duration-200 <?php echo $isActive ? 'bg-primary-50 text-primary-600 shadow-sm shadow-primary-50' : 'text-neutral-500 hover:bg-neutral-50 hover:text-neutral-900'; ?>"
                   aria-current="<?php echo $isActive ? 'page' : 'false'; ?>">
                    <div class="flex-shrink-0">
                        <svg class="w-6 h-6 <?php echo $isActive ? 'text-primary-600' : 'text-neutral-400 group-hover:text-neutral-900'; ?> transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="<?php echo $item['icon']; ?>" />
                        </svg>
                    </div>
                    <span class="font-semibold text-sm sidebar-text transition-all duration-300 opacity-100 whitespace-nowrap">
                        <?php echo $item['label']; ?>
                    </span>
                    <?php if ($isActive): ?>
                        <div class="absolute left-0 w-1.5 h-6 bg-primary-600 rounded-r-full"></div>
                    <?php endif; ?>
                </a>
            <?php endforeach; ?>
        </nav>

        <!-- User Profile Area -->
        <div class="p-4 border-t border-neutral-100 mt-auto bg-white">
            <div class="flex items-center gap-3 p-2 rounded-2xl hover:bg-neutral-50 transition-all group relative">
                <div class="w-12 h-12 bg-primary-100 rounded-2xl flex-shrink-0 flex items-center justify-center text-primary-700 font-bold shadow-sm group-hover:scale-105 transition-transform">
                    <?php echo strtoupper(substr($_SESSION['username'] ?? 'A', 0, 1)); ?>
                </div>
                <div class="flex-1 min-w-0 sidebar-text transition-all duration-300">
                    <p class="text-sm font-bold text-neutral-900 truncate"><?php echo $_SESSION['full_name'] ?? 'Admin User'; ?></p>
                    <p class="text-xs font-semibold text-neutral-400 truncate uppercase tracking-wider"><?php echo $_SESSION['role'] ?? 'Administrator'; ?></p>
                </div>
                <button onclick="logout()" class="sidebar-text p-2 text-neutral-400 hover:text-red-600 hover:bg-red-50 rounded-xl transition-all" title="Logout">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                    </svg>
                </button>
            </div>
        </div>
    </div>
</aside>

<style>
    /* Collapsed Sidebar Styles */
    .sidebar-collapsed {
        width: 88px !important;
    }
    .sidebar-collapsed .sidebar-text {
        display: none !important;
    }
    .sidebar-collapsed .p-8 {
        padding: 1.5rem !important;
        justify-content: center !important;
    }
    .sidebar-collapsed nav {
        padding-left: 0.75rem !important;
        padding-right: 0.75rem !important;
    }
    .sidebar-collapsed nav a {
        justify-content: center !important;
        padding-left: 0 !important;
        padding-right: 0 !important;
    }
    .sidebar-collapsed .p-4 {
        padding: 1rem 0.75rem !important;
    }
    .sidebar-collapsed .group {
        justify-content: center !important;
        gap: 0 !important;
    }
    .sidebar-collapsed .absolute.left-0 {
        width: 4px !important;
    }
    
    .custom-scrollbar::-webkit-scrollbar {
        width: 4px;
    }
</style>
