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
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }
        .bg-pattern {
            background-color: #f8fafc;
            background-image: radial-gradient(#6d5dfc 0.5px, transparent 0.5px), radial-gradient(#6d5dfc 0.5px, #f8fafc 0.5px);
            background-size: 20px 20px;
            background-position: 0 0, 10px 10px;
            opacity: 0.05;
        }
        :root {
            --primary: #6D5DFC;
        }
    </style>
</head>
<body class="bg-[#F8FAFC] min-h-screen flex items-center justify-center p-4 relative overflow-hidden">
    <div class="absolute inset-0 bg-pattern"></div>
    
    <!-- Background Glow -->
    <div class="absolute top-[-10%] left-[-10%] w-[40%] h-[40%] bg-blue-100 rounded-full blur-[120px] opacity-20"></div>
    <div class="absolute bottom-[-10%] right-[-10%] w-[40%] h-[40%] bg-purple-100 rounded-full blur-[120px] opacity-20"></div>

    <div class="max-w-[440px] w-full relative z-10">
        <!-- Logo/Header -->
        <div class="text-center mb-8">
            <div class="inline-flex items-center justify-center mb-4">
                <img src="assets/ERP.png" alt="Deckoid ERP Logo" class="w-20 h-20 object-contain">
            </div>
            <h1 class="text-3xl font-black text-neutral-900 tracking-tight">Deckoid<span class="text-[#6D5DFC]">ERP</span></h1>
        </div>

        <!-- Login Card -->
        <div class="bg-white rounded-[2rem] shadow-2xl shadow-primary-900/5 border border-white p-8 lg:p-10 relative overflow-hidden group">
            <div class="absolute top-0 left-0 w-full h-1.5 bg-[#6D5DFC]"></div>
            
            <form id="loginForm" class="space-y-6">
                <div class="space-y-2">
                    <label for="username" class="text-[11px] font-bold text-neutral-400 uppercase tracking-widest ml-1">Username / Email</label>
                    <div class="relative">
                        <span class="absolute left-4 top-1/2 -translate-y-1/2 text-neutral-400 transition-colors group-focus-within:text-[#6D5DFC]">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                        </span>
                        <input id="username" name="username" type="text" required
                                class="w-full bg-neutral-50 border border-neutral-100 rounded-2xl py-3.5 pl-12 pr-4 focus:bg-white focus:border-[#6D5DFC] focus:ring-4 focus:ring-primary-50 transition-all outline-none text-sm font-medium"
                                placeholder="Enter your username">
                    </div>
                </div>

                <div class="space-y-2">
                    <div class="flex items-center justify-between ml-1">
                        <label for="password" class="text-[11px] font-bold text-neutral-400 uppercase tracking-widest">Password</label>
                    </div>
                    <div class="relative">
                        <span class="absolute left-4 top-1/2 -translate-y-1/2 text-neutral-400 transition-colors group-focus-within:text-[#6D5DFC]">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                        </span>
                        <input id="password" name="password" type="password" required
                                class="w-full bg-neutral-50 border border-neutral-100 rounded-2xl py-3.5 pl-12 pr-4 focus:bg-white focus:border-[#6D5DFC] focus:ring-4 focus:ring-primary-50 transition-all outline-none text-sm font-medium"
                                placeholder="••••••••">
                    </div>
                </div>

                <div class="pt-2">
                    <button type="submit" id="loginButton"
                            class="w-full bg-[#6D5DFC] text-white font-black py-4 rounded-2xl shadow-xl shadow-primary-200 hover:shadow-primary-300 hover:bg-[#5b4dfa] active:scale-[0.98] transition-all flex items-center justify-center gap-3 group/btn">
                        <span class="tracking-wide">Login to System</span>
                        <svg class="w-5 h-5 group-hover/btn:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M13 7l5 5m0 0l-5 5m5-5H6"></path></svg>
                    </button>
                </div>
            </form>

            <!-- Error Message -->
            <div id="message" class="hidden mt-6 p-4 rounded-2xl text-center text-xs font-bold transition-all"></div>
        </div>
    </div>

    <script>
        document.getElementById('loginForm').addEventListener('submit', async function(e) {
            e.preventDefault();

            const loginButton = document.getElementById('loginButton');
            const messageDiv = document.getElementById('message');

            loginButton.disabled = true;
            loginButton.innerHTML = `
                <svg class="animate-spin h-5 w-5 text-white" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                <span>Authenticating...</span>
            `;

            const formData = new FormData(this);
            const data = Object.fromEntries(formData.entries());

            try {
                const response = await fetch('api/auth.php/login', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(data)
                });

                const result = await response.json();

                if (response.ok) {
                    messageDiv.className = 'mt-6 p-4 rounded-2xl bg-green-50 text-green-600 block';
                    messageDiv.textContent = 'Login successful! Redirecting...';
                    setTimeout(() => {
                        window.location.href = 'admin/dashboard.php';
                    }, 800);
                } else {
                    throw new Error(result.error || 'Invalid credentials provided');
                }
            } catch (error) {
                messageDiv.className = 'mt-6 p-4 rounded-2xl bg-red-50 text-red-600 block';
                messageDiv.textContent = error.message;
                
                loginButton.disabled = false;
                loginButton.innerHTML = `
                    <span class="tracking-wide">Login to System</span>
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M13 7l5 5m0 0l-5 5m5-5H6"></path></svg>
                `;
            }
        });
    </script>
</body>
</html>
