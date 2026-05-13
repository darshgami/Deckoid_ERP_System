<?php
/**
 * Global Header Component
 */
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $pageTitle ?? 'Deckoid ERP'; ?></title>
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    
    <style>
        :root {
            --font-sans: 'Inter', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
        }
        body { 
            font-family: var(--font-sans);
        }
    </style>
    
    <!-- Production CSS -->
    <link href="<?php echo asset_url('assets/css/output.css'); ?>" rel="stylesheet">
    
    <!-- Scripts -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.1/chart.umd.js" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script>
        // Safety check for Chart.js
        if (typeof Chart === 'undefined') {
            console.warn('Chart.js failed to load from CDN. Dashboard analytics will be disabled.');
            window.Chart = class { 
                constructor() { console.error('Chart.js not loaded'); }
                static register() {}
                destroy() {}
            };
        }
    </script>
    
    <style>
        .sidebar-item-active {
            background-color: #f5f3ff;
            color: #7c3aed;
            border-right: 3px solid #7c3aed;
        }
        .glass-card {
            background: rgba(255, 255, 255, 0.8);
            backdrop-filter: blur(8px);
            border: 1px solid rgba(255, 255, 255, 0.3);
        }
        /* Custom Scrollbar */
        ::-webkit-scrollbar {
            width: 6px;
        }
        ::-webkit-scrollbar-track {
            background: #f1f1f1;
        }
        ::-webkit-scrollbar-thumb {
            background: #ddd;
            border-radius: 10px;
        }
        ::-webkit-scrollbar-thumb:hover {
            background: #ccc;
        }
    </style>
</head>
<body class="text-neutral-900 antialiased">
