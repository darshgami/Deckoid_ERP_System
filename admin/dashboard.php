<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Lead Management ERP</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="../assets/css/style.css" rel="stylesheet">
</head>
<body class="bg-neutral-50">
    <div class="min-h-screen flex">
        <!-- Sidebar -->
        <div class="w-64 bg-white shadow-sm">
            <div class="p-6">
                <h1 class="text-xl font-bold text-neutral-900">Lead ERP</h1>
            </div>
            <nav class="mt-6">
                <a href="dashboard.php" class="block px-6 py-3 text-neutral-700 hover:bg-neutral-100 bg-neutral-100">Dashboard</a>
                <a href="leads.php" class="block px-6 py-3 text-neutral-700 hover:bg-neutral-100">Leads</a>
                <a href="#" class="block px-6 py-3 text-neutral-700 hover:bg-neutral-100">Reports</a>
                <a href="#" onclick="logout()" class="block px-6 py-3 text-neutral-700 hover:bg-neutral-100">Logout</a>
            </nav>
        </div>

        <!-- Main content -->
        <div class="flex-1 p-8">
            <div class="mb-8">
                <h2 class="text-2xl font-bold text-neutral-900">Dashboard</h2>
            </div>

            <!-- Statistics Cards -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8" id="stats">
                <div class="bg-white p-6 rounded-lg shadow-sm">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-8 h-8 bg-blue-500 rounded-full flex items-center justify-center">
                                <span class="text-white text-sm font-medium">L</span>
                            </div>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-neutral-600">Total Leads</p>
                            <p class="text-2xl font-bold text-neutral-900" id="totalLeads">0</p>
                        </div>
                    </div>
                </div>

                <div class="bg-white p-6 rounded-lg shadow-sm">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-8 h-8 bg-green-500 rounded-full flex items-center justify-center">
                                <span class="text-white text-sm font-medium">H</span>
                            </div>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-neutral-600">Hot Leads</p>
                            <p class="text-2xl font-bold text-neutral-900" id="hotLeads">0</p>
                        </div>
                    </div>
                </div>

                <div class="bg-white p-6 rounded-lg shadow-sm">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-8 h-8 bg-yellow-500 rounded-full flex items-center justify-center">
                                <span class="text-white text-sm font-medium">W</span>
                            </div>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-neutral-600">Warm Leads</p>
                            <p class="text-2xl font-bold text-neutral-900" id="warmLeads">0</p>
                        </div>
                    </div>
                </div>

                <div class="bg-white p-6 rounded-lg shadow-sm">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-8 h-8 bg-red-500 rounded-full flex items-center justify-center">
                                <span class="text-white text-sm font-medium">C</span>
                            </div>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-neutral-600">Cold Leads</p>
                            <p class="text-2xl font-bold text-neutral-900" id="coldLeads">0</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recent Leads -->
            <div class="bg-white rounded-lg shadow-sm">
                <div class="p-6 border-b border-neutral-200">
                    <h3 class="text-lg font-medium text-neutral-900">Recent Leads</h3>
                </div>
                <div class="p-6">
                    <div id="recentLeads" class="space-y-4">
                        <!-- Leads will be loaded here -->
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Check authentication
        const token = localStorage.getItem('access_token');
        if (!token) {
            window.location.href = 'login.php';
        }

        // Load dashboard data
        async function loadDashboard() {
            try {
                const response = await fetch('../api/dashboard.php', {
                    headers: {
                        'Authorization': 'Bearer ' + token
                    }
                });

                if (response.status === 401) {
                    // Token expired, try refresh
                    await refreshToken();
                    return loadDashboard();
                }

                const data = await response.json();

                document.getElementById('totalLeads').textContent = data.stats.total || 0;
                document.getElementById('hotLeads').textContent = data.stats.hot || 0;
                document.getElementById('warmLeads').textContent = data.stats.warm || 0;
                document.getElementById('coldLeads').textContent = data.stats.cold || 0;

                const recentLeadsDiv = document.getElementById('recentLeads');
                if (data.recent_leads && data.recent_leads.length > 0) {
                    recentLeadsDiv.innerHTML = data.recent_leads.map(lead => `
                        <div class="flex items-center justify-between p-4 border border-neutral-200 rounded-lg">
                            <div>
                                <p class="font-medium text-neutral-900">${lead.company_client_name}</p>
                                <p class="text-sm text-neutral-600">${lead.contact_person} • ${lead.mobile_number}</p>
                            </div>
                            <span class="px-2 py-1 text-xs font-medium rounded-full ${
                                lead.lead_category === 'Hot' ? 'bg-red-100 text-red-800' :
                                lead.lead_category === 'Warm' ? 'bg-yellow-100 text-yellow-800' :
                                'bg-blue-100 text-blue-800'
                            }">${lead.lead_category}</span>
                        </div>
                    `).join('');
                } else {
                    recentLeadsDiv.innerHTML = '<p class="text-neutral-500">No recent leads</p>';
                }
            } catch (error) {
                console.error('Error loading dashboard:', error);
            }
        }

        async function refreshToken() {
            const refreshToken = localStorage.getItem('refresh_token');
            if (!refreshToken) {
                window.location.href = 'login.php';
                return;
            }

            try {
                const response = await fetch('../api/auth.php/refresh', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({
                        refresh_token: refreshToken
                    })
                });

                if (response.ok) {
                    const data = await response.json();
                    localStorage.setItem('access_token', data.access_token);
                } else {
                    window.location.href = 'login.php';
                }
            } catch (error) {
                window.location.href = 'login.php';
            }
        }

        function logout() {
            localStorage.removeItem('access_token');
            localStorage.removeItem('refresh_token');
            window.location.href = 'login.php';
        }

        // Load data on page load
        loadDashboard();
    </script>
</body>
</html>