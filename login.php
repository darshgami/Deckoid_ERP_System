<?php
/**
 * Centralized Login Page for Deckoid ERP
 * Handles both Admin and Staff authentication
 */
require_once 'includes/auth.php';

// Redirect if already logged in
if (AuthController::isLoggedIn()) {
    header('Location: admin/dashboard.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | Deckoid ERP System</title>
    <link rel="icon" type="image/png" href="<?php echo asset_url('assets/ERP.png'); ?>">
    <link rel="stylesheet" href="<?php echo asset_url('assets/css/output.css'); ?>">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <style>
        body { 
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
            background-color: #f8fafc;
        }
        .bg-pattern {
            background-color: #f8fafc;
            background-image: radial-gradient(#6366f1 0.5px, transparent 0.5px), radial-gradient(#6366f1 0.5px, #f8fafc 0.5px);
            background-size: 20px 20px;
            background-position: 0 0, 10px 10px;
            opacity: 0.05;
        }
    </style>
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
<body class="bg-neutral-50 min-h-screen flex items-center justify-center p-4 relative overflow-hidden">
    <div class="absolute inset-0 bg-pattern"></div>
    
    <div class="max-w-[400px] w-full relative z-10">
        <!-- Logo/Header -->
        <div class="text-center mb-8">
            <div class="inline-flex items-center justify-center mb-4">
                <img src="<?php echo asset_url('assets/ERP.png'); ?>" alt="Deckoid ERP Logo" class="w-16 h-16 object-contain">
            </div>
            <h1 class="text-2xl font-semibold text-neutral-900 tracking-tight">Deckoid<span class="text-primary">ERP</span></h1>
            <p class="text-sm text-neutral-500 mt-1">Sign in to manage your operations</p>
        </div>

        <!-- Login Card -->
        <div class="bg-white rounded-xl shadow-sm border border-neutral-200 p-8 lg:p-10 relative overflow-hidden">
            <form id="loginForm" class="space-y-5">
                <div class="space-y-1.5">
                    <label for="username" class="text-[11px] font-semibold text-neutral-500 uppercase tracking-wider ml-1">Username</label>
                    <div class="relative">
                        <span class="absolute left-4 top-1/2 -translate-y-1/2 text-neutral-400">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                        </span>
                        <input id="username" name="username" type="text" required
                                class="w-full bg-neutral-50 border border-neutral-200 rounded-xl py-2.5 pl-11 pr-4 focus:bg-white focus:border-primary focus:ring-4 focus:ring-primary/5 transition-all outline-none text-sm"
                                placeholder="Enter your username">
                    </div>
                </div>

                <div class="space-y-1.5">
                    <label for="password" class="text-[11px] font-semibold text-neutral-500 uppercase tracking-wider ml-1">Password</label>
                    <div class="relative">
                        <span class="absolute left-4 top-1/2 -translate-y-1/2 text-neutral-400">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                        </span>
                        <input id="password" name="password" type="password" required
                                class="w-full bg-neutral-50 border border-neutral-200 rounded-xl py-2.5 pl-11 pr-4 focus:bg-white focus:border-primary focus:ring-4 focus:ring-primary/5 transition-all outline-none text-sm"
                                placeholder="••••••••">
                    </div>
                </div>

                <div class="pt-2">
                    <button type="submit" id="loginButton"
                            class="w-full btn btn-primary py-3 rounded-xl active:scale-[0.98] flex items-center justify-center gap-2">
                        <span>Sign in to Dashboard</span>
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M13 7l5 5m0 0l-5 5m5-5H6"></path></svg>
                    </button>
                </div>
            </form>

            <div id="message" class="hidden mt-6 p-3 rounded-xl text-center text-xs font-semibold transition-all"></div>
        </div>  
    </div>

    <style>
        .input-error {
            border-color: #ef4444 !important;
            background-color: #fef2f2 !important;
        }
        .error-message {
            color: #ef4444;
            font-size: 10px;
            font-weight: 600;
            text-transform: uppercase;
            margin-top: 4px;
            margin-left: 4px;
        }
    </style>

    <script>
        document.getElementById('loginForm').addEventListener('submit', async function(e) {
            e.preventDefault();

            const loginButton = document.getElementById('loginButton');
            const messageDiv = document.getElementById('message');
            
            // Clear previous errors
            document.querySelectorAll('.input-error').forEach(el => el.classList.remove('input-error'));
            document.querySelectorAll('.error-message').forEach(el => el.remove());
            messageDiv.classList.add('hidden');

            const formData = new FormData(this);
            const data = Object.fromEntries(formData.entries());

            // Simple frontend validation
            let isValid = true;
            if (!data.username || data.username.trim() === '') {
                const userInp = document.getElementById('username');
                userInp.classList.add('input-error');
                const err = document.createElement('p');
                err.className = 'error-message';
                err.textContent = 'Username is required';
                userInp.parentNode.appendChild(err);
                isValid = false;
            }
            if (!data.password || data.password.trim() === '') {
                const passInp = document.getElementById('password');
                passInp.classList.add('input-error');
                const err = document.createElement('p');
                err.className = 'error-message';
                err.textContent = 'Password is required';
                passInp.parentNode.appendChild(err);
                isValid = false;
            }

            if (!isValid) return;

            loginButton.disabled = true;
            const originalBtnContent = loginButton.innerHTML;
            loginButton.innerHTML = `
                <svg class="animate-spin h-5 w-5 text-white" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                <span class="tracking-widest uppercase text-[10px] font-semibold">Login...</span>
            `;

            try {
                const response = await fetch('api/auth.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    credentials: 'same-origin',
                    body: JSON.stringify({
                        action: 'login',
                        ...data
                    })
                });

                const result = await response.json();

                if (response.ok && result.success) {
                    setTimeout(() => {
                        window.location.href = 'admin/dashboard.php';
                    }, 800);
                } else {
                    throw new Error(result.message || 'Invalid username or password');
                }
            } catch (error) {
                messageDiv.className = 'mt-6 p-4 rounded-2xl bg-red-50 text-red-600 block text-center text-xs font-bold';
                messageDiv.textContent = error.message;
                
                loginButton.disabled = false;
                loginButton.innerHTML = originalBtnContent;
            }
        });
    </script>
</body>
</html>
