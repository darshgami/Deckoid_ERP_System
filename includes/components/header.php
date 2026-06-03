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
    
    <!-- Favicon -->
    <link rel="icon" type="image/png" href="<?php echo asset_url('assets/ERP.png'); ?>">
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    
    <style>
        :root {
            /* Typography Tokens */
            --text-xs: 12px;
            --text-sm: 13px;
            --text-base: 14px;
            --text-md: 15px;
            --text-lg: 16px;
            --text-xl: 20px;
            --text-2xl: 24px;
            --text-3xl: 30px;

            /* Font Weights */
            --font-normal: 400;
            --font-medium: 500;
            --font-semibold: 600;

            /* Color System */
            --bg: #f8fafc;
            --card: #ffffff;
            --border: #e5e7eb;
            --text-primary: #111827;
            --text-secondary: #4b5563;
            --text-muted: #6b7280;
            --primary: #6366f1;
            --primary-hover: #4f46e5;
            --primary-light: #eef2ff;

            /* Radius & Shadows */
            --radius: 12px;
            --shadow-sm: 0 1px 2px rgba(0, 0, 0, 0.05);
            --transition: all 0.2s ease;
        }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
            font-size: var(--text-base);
            background-color: var(--bg);
            color: var(--text-primary);
            line-height: 1.5;
        }

        /* Sidebar Styles */
        .sidebar-item-active {
            background-color: var(--primary-light) !important;
            color: var(--primary) !important;
            font-weight: var(--font-medium) !important;
            border-right: 3px solid var(--primary);
        }

        /* Standardized Scrollbar */
        ::-webkit-scrollbar { width: 6px; }
        ::-webkit-scrollbar-track { background: var(--bg); }
        ::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 10px; }
        ::-webkit-scrollbar-thumb:hover { background: #94a3b8; }

        /* Form Validation */
        .error-message {
            color: #dc2626;
            font-size: 11px;
            font-weight: var(--font-medium);
            margin-top: 4px;
            display: flex;
            align-items: center;
            gap: 4px;
        }
        .input-error {
            border-color: #ef4444 !important;
            background-color: #fff1f2 !important;
            box-shadow: 0 0 0 4px rgba(239, 68, 68, 0.05) !important;
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
    <!-- CSRF Token & Fetch Interceptor -->
    <meta name="csrf-token" content="<?php echo csrf_token(); ?>">
    <script>
        const originalFetch = window.fetch;
        window.fetch = async function() {
            let [resource, config] = arguments;
            if(config === undefined) {
                config = {};
            }
            if(config.method && ['POST', 'PUT', 'DELETE'].includes(config.method.toUpperCase())) {
                const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
                if (csrfToken) {
                    config.headers = {
                        ...config.headers,
                        'X-CSRF-TOKEN': csrfToken
                    };
                }
            }
            return originalFetch(resource, config);
        };
    </script>
</head>

<body class="text-neutral-900 antialiased">
