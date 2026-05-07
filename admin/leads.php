<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Leads - Lead Management ERP</title>
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
                <a href="dashboard.php" class="block px-6 py-3 text-neutral-700 hover:bg-neutral-100">Dashboard</a>
                <a href="leads.php" class="block px-6 py-3 text-neutral-700 hover:bg-neutral-100 bg-neutral-100">Leads</a>
                <a href="#" class="block px-6 py-3 text-neutral-700 hover:bg-neutral-100">Reports</a>
                <a href="#" onclick="logout()" class="block px-6 py-3 text-neutral-700 hover:bg-neutral-100">Logout</a>
            </nav>
        </div>

        <!-- Main content -->
        <div class="flex-1 p-8">
            <div class="flex justify-between items-center mb-8">
                <h2 class="text-2xl font-bold text-neutral-900">Leads</h2>
                <div class="flex space-x-3">
                    <button onclick="exportLeads()" class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700">
                        Export to CSV
                    </button>
                    <button onclick="openAddLeadModal()" class="bg-primary-600 text-white px-4 py-2 rounded-lg hover:bg-primary-700">
                        Add New Lead
                    </button>
                </div>
            </div>

            <!-- Filters -->
            <div class="bg-white p-4 rounded-lg shadow-sm mb-6">
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <input type="text" id="search" placeholder="Search leads..."
                           class="border border-neutral-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary-500">
                    <select id="categoryFilter" class="border border-neutral-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary-500">
                        <option value="">All Categories</option>
                        <option value="Hot">Hot</option>
                        <option value="Warm">Warm</option>
                        <option value="Cold">Cold</option>
                    </select>
                    <select id="statusFilter" class="border border-neutral-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary-500">
                        <option value="">All Status</option>
                        <option value="New">New</option>
                        <option value="Contacted">Contacted</option>
                        <option value="Qualified">Qualified</option>
                        <option value="Proposal">Proposal</option>
                        <option value="Negotiation">Negotiation</option>
                        <option value="Closed">Closed</option>
                    </select>
                    <button onclick="loadLeads()" class="bg-neutral-600 text-white px-4 py-2 rounded-lg hover:bg-neutral-700">
                        Filter
                    </button>
                </div>
            </div>

            <!-- Leads Table -->
            <div class="bg-white rounded-lg shadow-sm overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-neutral-200">
                        <thead class="bg-neutral-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-neutral-500 uppercase tracking-wider">Lead ID</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-neutral-500 uppercase tracking-wider">Company</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-neutral-500 uppercase tracking-wider">Contact</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-neutral-500 uppercase tracking-wider">Mobile</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-neutral-500 uppercase tracking-wider">Category</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-neutral-500 uppercase tracking-wider">Status</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-neutral-500 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody id="leadsTableBody" class="bg-white divide-y divide-neutral-200">
                            <!-- Leads will be loaded here -->
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Pagination -->
            <div class="mt-6 flex justify-between items-center">
                <div id="paginationInfo" class="text-sm text-neutral-600"></div>
                <div id="pagination" class="flex space-x-2"></div>
            </div>
        </div>
    </div>

    <!-- Add/Edit Lead Modal -->
    <div id="leadModal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50">
        <div class="flex items-center justify-center min-h-screen p-4">
            <div class="bg-white rounded-lg max-w-4xl w-full max-h-screen overflow-y-auto">
                <div class="p-6 border-b border-neutral-200">
                    <h3 id="modalTitle" class="text-lg font-medium text-neutral-900">Add New Lead</h3>
                </div>
                <form id="leadForm" class="p-6 space-y-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-neutral-700">Lead Date *</label>
                            <input type="date" name="lead_date" required class="mt-1 block w-full border border-neutral-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-neutral-700">Company/Client Name *</label>
                            <input type="text" name="company_client_name" required class="mt-1 block w-full border border-neutral-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-neutral-700">Contact Person *</label>
                            <input type="text" name="contact_person" required class="mt-1 block w-full border border-neutral-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-neutral-700">Mobile Number *</label>
                            <input type="text" name="mobile_number" required class="mt-1 block w-full border border-neutral-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-neutral-700">Alternative Number</label>
                            <input type="text" name="alternative_number" class="mt-1 block w-full border border-neutral-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-neutral-700">Email ID</label>
                            <input type="email" name="email_id" class="mt-1 block w-full border border-neutral-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-neutral-700">City</label>
                            <input type="text" name="city" class="mt-1 block w-full border border-neutral-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-neutral-700">State</label>
                            <input type="text" name="state" class="mt-1 block w-full border border-neutral-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-neutral-700">Source of Lead *</label>
                            <select name="source_of_lead" required class="mt-1 block w-full border border-neutral-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary-500">
                                <option value="">Select Source</option>
                                <option value="Website">Website</option>
                                <option value="Social Media">Social Media</option>
                                <option value="Referral">Referral</option>
                                <option value="Cold Call">Cold Call</option>
                                <option value="Email">Email</option>
                                <option value="Other">Other</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-neutral-700">Service Interested In</label>
                            <input type="text" name="service_interested_in" class="mt-1 block w-full border border-neutral-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-neutral-700">Lead Category *</label>
                            <select name="lead_category" required class="mt-1 block w-full border border-neutral-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary-500">
                                <option value="Hot">Hot</option>
                                <option value="Warm">Warm</option>
                                <option value="Cold">Cold</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-neutral-700">Lead Status *</label>
                            <select name="lead_status" required class="mt-1 block w-full border border-neutral-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary-500">
                                <option value="New">New</option>
                                <option value="Contacted">Contacted</option>
                                <option value="Qualified">Qualified</option>
                                <option value="Proposal">Proposal</option>
                                <option value="Negotiation">Negotiation</option>
                                <option value="Closed">Closed</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-neutral-700">Priority</label>
                            <select name="priority" class="mt-1 block w-full border border-neutral-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary-500">
                                <option value="High">High</option>
                                <option value="Medium">Medium</option>
                                <option value="Low">Low</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-neutral-700">Deal Status *</label>
                            <select name="deal_status" required class="mt-1 block w-full border border-neutral-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary-500">
                                <option value="Open">Open</option>
                                <option value="Won">Won</option>
                                <option value="Lost">Lost</option>
                                <option value="On Hold">On Hold</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-neutral-700">Payment Status *</label>
                            <select name="payment_status" required class="mt-1 block w-full border border-neutral-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary-500">
                                <option value="Pending">Pending</option>
                                <option value="Partial">Partial</option>
                                <option value="Paid">Paid</option>
                                <option value="Overdue">Overdue</option>
                            </select>
                        </div>
                    </div>

                    <div class="flex justify-end space-x-3">
                        <button type="button" onclick="closeLeadModal()" class="bg-neutral-600 text-white px-4 py-2 rounded-lg hover:bg-neutral-700">
                            Cancel
                        </button>
                        <button type="submit" class="bg-primary-600 text-white px-4 py-2 rounded-lg hover:bg-primary-700">
                            Save Lead
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        let currentPage = 1;
        let currentLeadId = null;

        // Check authentication
        const token = localStorage.getItem('access_token');
        if (!token) {
            window.location.href = 'login.php';
        }

        function openAddLeadModal() {
            document.getElementById('modalTitle').textContent = 'Add New Lead';
            document.getElementById('leadForm').reset();
            document.getElementById('leadModal').classList.remove('hidden');
            currentLeadId = null;
        }

        function closeLeadModal() {
            document.getElementById('leadModal').classList.add('hidden');
        }

        async function loadLeads(page = 1) {
            currentPage = page;
            const search = document.getElementById('search').value;
            const category = document.getElementById('categoryFilter').value;
            const status = document.getElementById('statusFilter').value;

            const params = new URLSearchParams({
                page: page,
                limit: 10,
                search: search,
                category: category,
                status: status
            });

            try {
                const response = await fetch(`../api/leads.php?${params}`, {
                    headers: {
                        'Authorization': 'Bearer ' + token
                    }
                });

                if (response.status === 401) {
                    await refreshToken();
                    return loadLeads(page);
                }

                const data = await response.json();

                const tbody = document.getElementById('leadsTableBody');
                tbody.innerHTML = data.leads.map(lead => `
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-neutral-900">${lead.lead_id}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-neutral-900">${lead.company_client_name}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-neutral-900">${lead.contact_person}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-neutral-900">${lead.mobile_number}</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 py-1 text-xs font-medium rounded-full ${
                                lead.lead_category === 'Hot' ? 'bg-red-100 text-red-800' :
                                lead.lead_category === 'Warm' ? 'bg-yellow-100 text-yellow-800' :
                                'bg-blue-100 text-blue-800'
                            }">${lead.lead_category}</span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-neutral-900">${lead.lead_status}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <button onclick="editLead('${lead.id}')" class="text-primary-600 hover:text-primary-900 mr-2">Edit</button>
                        </td>
                    </tr>
                `).join('');

                updatePagination(data.pagination);
            } catch (error) {
                console.error('Error loading leads:', error);
            }
        }

        function updatePagination(pagination) {
            const info = document.getElementById('paginationInfo');
            const pages = document.getElementById('pagination');

            info.textContent = `Showing ${((pagination.page - 1) * pagination.limit) + 1} to ${Math.min(pagination.page * pagination.limit, pagination.total)} of ${pagination.total} leads`;

            pages.innerHTML = '';
            for (let i = 1; i <= pagination.pages; i++) {
                const button = document.createElement('button');
                button.textContent = i;
                button.className = `px-3 py-1 rounded ${i === pagination.page ? 'bg-primary-600 text-white' : 'bg-neutral-200 text-neutral-700 hover:bg-neutral-300'}`;
                button.onclick = () => loadLeads(i);
                pages.appendChild(button);
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

        async function exportLeads() {
            try {
                const response = await fetch('../api/export.php', {
                    headers: {
                        'Authorization': 'Bearer ' + token
                    }
                });

                if (response.status === 401) {
                    await refreshToken();
                    return exportLeads();
                }

                const data = await response.json();

                if (response.ok) {
                    // Download the file
                    const link = document.createElement('a');
                    link.href = data.file_url;
                    link.download = data.filename;
                    document.body.appendChild(link);
                    link.click();
                    document.body.removeChild(link);
                } else {
                    alert('Export failed: ' + data.error);
                }
            } catch (error) {
                console.error('Error exporting leads:', error);
                alert('Export failed');
            }
        }

        // Load leads on page load
        loadLeads();
    </script>
</body>
</html>