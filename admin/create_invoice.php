<?php
require_once '../includes/middleware.php';
require_once '../includes/components/layout_wrapper.php';
requireAuth();

layout_start('Create Invoice - Deckoid ERP');

require_once '../config/env.php';
require_once '../includes/database.php';

$db = Database::getInstance();
$stmt = $db->query("SELECT invoice_number FROM invoices ORDER BY created_at DESC LIMIT 1");
$lastInvoice = $stmt->fetch();
$nextNumber = "DE0001";
if ($lastInvoice) {
    $lastNum = (int)preg_replace('/[^0-9]/', '', $lastInvoice['invoice_number']);
    $nextNumber = "DE" . str_pad($lastNum + 1, 4, '0', STR_PAD_LEFT);
}
?>

<div class="max-w-5xl mx-auto">
    <form id="invoiceForm" class="space-y-6 pb-20">
        <!-- Header -->
        <div class="flex items-center justify-between mb-8">
            <h1 class="text-2xl font-black text-neutral-900 tracking-tight">New Sales Invoice</h1>
            <a href="sales.php" class="text-xs font-black text-neutral-400 uppercase tracking-widest hover:text-primary-600 transition-all flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M15 19l-7-7 7-7" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"></path></svg>
                Back to List
            </a>
        </div>

        <div class="bg-white rounded-3xl shadow-xl shadow-neutral-200/50 border border-neutral-100 overflow-hidden">
            <!-- Business Header (Mirroring Screenshot) -->
            <div class="p-8 border-b border-neutral-50 text-center">
                <h2 class="text-3xl font-black text-neutral-900 uppercase tracking-tighter">DECKOID SOLUTION</h2>
                <p class="text-[10px] font-bold text-neutral-400 uppercase tracking-widest">6, Bhaktinagar Station Plot, Rajkot-360002</p>
                <p class="text-[10px] font-bold text-neutral-500 mt-1">9426225742/9586536724</p>
                <div class="flex justify-center items-center gap-4 mt-2 text-[11px] font-bold text-neutral-500">
                    <a href="https://www.deckoidsolution.com" target="_blank" class="hover:text-primary-600 uppercase">www.deckoidsolution.com</a>
                </div>
            </div>

            <div class="p-8">
                <!-- Basic Details -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-10">
                    <div class="space-y-1.5">
                        <label class="text-[10px] font-black text-neutral-400 uppercase tracking-widest">Invoice Type</label>
                        <select name="invoice_type" id="invoice_type" onchange="toggleGSTFields()" class="w-full bg-neutral-50 border-transparent rounded-xl py-3 px-4 text-sm font-bold outline-none focus:bg-white focus:ring-4 focus:ring-primary-50 transition-all cursor-pointer">
                            <option value="With GST">With GST (18%)</option>
                            <option value="Without GST">Without GST</option>
                        </select>
                    </div>
                    <div class="space-y-1.5">
                        <label class="text-[10px] font-black text-neutral-400 uppercase tracking-widest">Invoice Date</label>
                        <input type="date" name="invoice_date" value="<?= date('Y-m-d') ?>" class="w-full bg-neutral-50 border-transparent rounded-xl py-3 px-4 text-sm font-bold outline-none focus:bg-white focus:ring-4 focus:ring-primary-50 transition-all">
                    </div>
                    <div class="space-y-1.5">
                        <label class="text-[10px] font-black text-neutral-400 uppercase tracking-widest">Invoice Number</label>
                        <input type="text" name="invoice_number" id="invoice_number" value="<?= $nextNumber ?>" class="w-full bg-neutral-50 border-transparent rounded-xl py-3 px-4 text-sm font-black uppercase outline-none focus:bg-white focus:ring-4 focus:ring-primary-50 transition-all">
                    </div>
                </div>

                <!-- Party Details -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-10">
                    <div class="space-y-1.5">
                        <label class="text-[10px] font-black text-neutral-400 uppercase tracking-widest">Party Name</label>
                        <input type="text" name="party_name" placeholder="M/s. Party Name" class="w-full bg-neutral-50 border-transparent rounded-xl py-3 px-4 text-sm font-bold outline-none focus:bg-white focus:ring-4 focus:ring-primary-50 transition-all">
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div class="space-y-1.5">
                            <label class="text-[10px] font-black text-neutral-400 uppercase tracking-widest">Mobile Number</label>
                            <input type="text" name="mobile_number" placeholder="Number" class="w-full bg-neutral-50 border-transparent rounded-xl py-3 px-4 text-sm font-bold outline-none focus:bg-white focus:ring-4 focus:ring-primary-50 transition-all">
                        </div>
                        <div class="space-y-1.5">
                            <label class="text-[10px] font-black text-neutral-400 uppercase tracking-widest">Place of Supply</label>
                            <input type="text" name="place_of_supply" placeholder="State/City" class="w-full bg-neutral-50 border-transparent rounded-xl py-3 px-4 text-sm font-bold outline-none focus:bg-white focus:ring-4 focus:ring-primary-50 transition-all">
                        </div>
                    </div>
                    <div class="md:col-span-2 space-y-1.5">
                        <label class="text-[10px] font-black text-neutral-400 uppercase tracking-widest">Address</label>
                        <textarea name="address" rows="2" placeholder="Client's Address" class="w-full bg-neutral-50 border-transparent rounded-xl py-3 px-4 text-sm font-medium outline-none focus:bg-white focus:ring-4 focus:ring-primary-50 transition-all resize-none"></textarea>
                    </div>
                    <div id="gstinField" class="md:col-span-2 space-y-1.5">
                        <label class="text-[10px] font-black text-neutral-400 uppercase tracking-widest">Party GSTIN</label>
                        <input type="text" name="gstin" placeholder="GST Number" class="w-full bg-neutral-50 border-transparent rounded-xl py-3 px-4 text-sm font-bold outline-none focus:bg-white focus:ring-4 focus:ring-primary-50 transition-all uppercase tracking-widest">
                    </div>
                </div>

                <!-- Items Table -->
                <div class="mb-10 rounded-2xl border border-neutral-200 overflow-hidden shadow-sm">
                    <table class="w-full border-collapse border-spacing-0">
                        <thead>
                            <tr class="bg-neutral-900 text-white text-[9px] font-black uppercase tracking-widest">
                                <th class="px-4 py-4 text-left w-12 border-r border-white/10">Sr</th>
                                <th class="px-4 py-4 text-left border-r border-white/10">Service Name</th>
                                <th class="hsn-col px-4 py-4 text-center w-24 border-r border-white/10">HSN/SAC</th>
                                <th class="px-4 py-4 text-center w-20 border-r border-white/10">Qty</th>
                                <th class="px-4 py-4 text-right w-28 border-r border-white/10">Rate</th>
                                <th class="gst-col px-4 py-4 text-center w-20 border-r border-white/10">IGST %</th>
                                <th class="px-4 py-4 text-right w-28 border-r border-white/10">Amount</th>
                                <th class="px-4 py-4 w-12"></th>
                            </tr>
                        </thead>
                        <tbody id="invoiceItems" class="divide-y divide-neutral-200"></tbody>
                    </table>
                    <div class="p-4 bg-neutral-50/50">
                        <button type="button" onclick="addRow()" class="text-[10px] font-black text-primary-600 uppercase tracking-widest flex items-center gap-2 hover:text-primary-700">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M12 4v16m8-8H4" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"></path></svg>
                            Add Service Row
                        </button>
                    </div>
                </div>

                <!-- Summary -->
                <div class="flex justify-end">
                    <div class="w-full md:w-80 bg-neutral-900 rounded-3xl p-8 text-white space-y-6">
                        <div class="space-y-4 border-b border-white/10 pb-6">
                            <div class="flex justify-between text-xs">
                                <span class="text-neutral-400 font-bold uppercase tracking-widest">Sub Total</span>
                                <span class="font-black" id="subTotalDisplay">₹0.00</span>
                            </div>
                            <div id="gstAmountSection" class="flex justify-between text-xs">
                                <span class="text-neutral-400 font-bold uppercase tracking-widest">Integrated Tax (18%)</span>
                                <span class="font-black" id="gstTotalDisplay">₹0.00</span>
                            </div>
                        </div>
                        <div class="space-y-1">
                            <span class="text-[10px] text-neutral-500 font-black uppercase tracking-[0.2em]">Grand Total</span>
                            <div class="text-3xl font-black" id="grandTotalDisplay">₹0.00</div>
                        </div>
                        <div class="pt-2">
                            <p class="text-[9px] font-black text-neutral-500 uppercase tracking-widest mb-1">Amount In Words</p>
                            <p class="text-[10px] font-bold italic opacity-80 uppercase" id="amountInWordsDisplay">Zero Only</p>
                            <input type="hidden" name="amount_in_words" id="amountInWordsInput">
                        </div>
                        <button type="submit" class="w-full py-4 bg-primary-600 text-white font-black rounded-2xl hover:bg-primary-700 transition-all shadow-xl shadow-primary-900/20 uppercase tracking-widest text-xs">
                            Save & Print
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<script>
    const servicesList = ["Facebook & Google Ads", "Website Design & Development", "Graphics Design", "Search Engine Optimization", "Video Editing", "Social Media Management", "AI Video Making", "Other"];
    let rowCount = 0;

    function addRow() {
        rowCount++;
        const tr = document.createElement('tr');
        tr.className = 'text-sm font-bold hover:bg-neutral-50 transition-colors';
        tr.innerHTML = `
            <td class="px-4 py-4 text-xs text-neutral-400 font-black border-r border-neutral-100">${rowCount}</td>
            <td class="px-4 py-4 border-r border-neutral-100">
                <select name="service_name[]" onchange="handleServiceChange(this)" class="w-full bg-transparent rounded-lg py-1 px-1 text-xs font-bold outline-none">
                    <option value="">Select Service</option>
                    ${servicesList.map(s => `<option value="${s}">${s}</option>`).join('')}
                </select>
                <textarea name="custom_service[]" placeholder="Description" class="hidden w-full mt-2 bg-white border border-neutral-100 rounded-lg py-2 px-3 text-xs resize-none" rows="2"></textarea>
            </td>
            <td class="hsn-col px-4 py-4 border-r border-neutral-100"><input type="text" name="hsn_sac[]" value="9983" class="w-full bg-transparent border-transparent text-center text-xs font-black opacity-60"></td>
            <td class="px-4 py-4 border-r border-neutral-100"><input type="number" name="qty[]" value="1.000" step="0.001" oninput="calculateRow(this)" class="w-full bg-transparent text-center text-xs font-black outline-none"></td>
            <td class="px-4 py-4 border-r border-neutral-100"><input type="number" name="rate[]" value="0.00" step="0.01" oninput="calculateRow(this)" class="w-full bg-transparent text-right text-xs font-black outline-none"></td>
            <td class="gst-col px-4 py-4 border-r border-neutral-100"><input type="text" value="18.00" readonly class="w-full bg-transparent border-transparent text-center text-xs font-black opacity-40"></td>
            <td class="px-4 py-4 border-r border-neutral-100"><input type="text" name="amount[]" value="0.00" readonly class="w-full bg-transparent border-transparent text-right text-xs font-black outline-none"></td>
            <td class="px-4 py-4 text-center">
                <button type="button" onclick="this.closest('tr').remove(); calculateTotals(); updateRowNumbers();" class="text-neutral-300 hover:text-red-500 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"></path></svg>
                </button>
            </td>
        `;
        document.getElementById('invoiceItems').appendChild(tr);
        toggleGSTFields();
    }

    function updateRowNumbers() {
        document.querySelectorAll('#invoiceItems tr').forEach((tr, idx) => {
            tr.querySelector('td:first-child').textContent = idx + 1;
        });
    }

    function toggleGSTFields() {
        const isGST = document.getElementById('invoice_type').value === 'With GST';
        document.getElementById('gstinField').classList.toggle('hidden', !isGST);
        
        document.querySelectorAll('.hsn-col').forEach(col => col.classList.toggle('hidden', !isGST));
        document.querySelectorAll('.gst-col').forEach(col => col.classList.toggle('hidden', !isGST));
        
        calculateTotals();
    }

    function handleServiceChange(sel) {
        const inp = sel.nextElementSibling;
        inp.classList.toggle('hidden', sel.value !== 'Other');
        if (sel.value === 'Other') inp.focus();
    }

    function calculateRow(inp) {
        const tr = inp.closest('tr');
        const q = parseFloat(tr.querySelector('[name="qty[]"]').value) || 0;
        const r = parseFloat(tr.querySelector('[name="rate[]"]').value) || 0;
        tr.querySelector('[name="amount[]"]').value = (q * r).toFixed(2);
        calculateTotals();
    }

    function calculateTotals() {
        const sub = Array.from(document.querySelectorAll('[name="amount[]"]')).reduce((acc, el) => acc + (parseFloat(el.value) || 0), 0);
        const isGST = document.getElementById('invoice_type').value === 'With GST';
        const gst = isGST ? Math.round((sub * 0.18) * 100) / 100 : 0;
        const total = sub + gst;

        document.getElementById('subTotalDisplay').textContent = '₹' + sub.toLocaleString('en-IN', {minimumFractionDigits: 2});
        document.getElementById('gstTotalDisplay').textContent = '₹' + gst.toLocaleString('en-IN', {minimumFractionDigits: 2});
        document.getElementById('grandTotalDisplay').textContent = '₹' + total.toLocaleString('en-IN', {minimumFractionDigits: 2});
        
        document.getElementById('gstAmountSection').classList.toggle('hidden', !isGST);
        
        const w = numberToWords(Math.round(total));
        document.getElementById('amountInWordsDisplay').textContent = w + ' Only';
        document.getElementById('amountInWordsInput').value = w + ' Only';
    }

    function numberToWords(n) {
        if (n === 0) return 'Zero';
        const f = ['', 'One', 'Two', 'Three', 'Four', 'Five', 'Six', 'Seven', 'Eight', 'Nine', 'Ten', 'Eleven', 'Twelve', 'Thirteen', 'Fourteen', 'Fifteen', 'Sixteen', 'Seventeen', 'Eighteen', 'Nineteen'];
        const t = ['', '', 'Twenty', 'Thirty', 'Forty', 'Fifty', 'Sixty', 'Seventy', 'Eighty', 'Ninety'];
        const m = ['', 'Thousand', 'Million', 'Billion'];
        let w = '', i = 0;
        const h = (num) => {
            if (num < 20) return f[num];
            if (num < 100) return t[Math.floor(num/10)] + (num%10 ? ' ' + f[num%10] : '');
            return f[Math.floor(num/100)] + ' Hundred' + (num%100 ? ' ' + h(num%100) : '');
        };
        while (n > 0) {
            if (n % 1000) w = h(n % 1000) + ' ' + m[i] + ' ' + w;
            n = Math.floor(n / 1000); i++;
        }
        return w.trim();
    }

    document.getElementById('invoiceForm').addEventListener('submit', async function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        const data = {};
        formData.forEach((v, k) => {
            if (k.endsWith('[]')) {
                const ck = k.slice(0, -2);
                if (!data[ck]) data[ck] = [];
                data[ck].push(v);
            } else data[k] = v;
        });

        // Recalculate totals to ensure integrity
        const amounts = Array.from(document.querySelectorAll('[name="amount[]"]')).map(el => parseFloat(el.value) || 0);
        data.sub_total = amounts.reduce((a, b) => a + b, 0);
        data.gst_total = data.invoice_type === 'With GST' ? Math.round((data.sub_total * 0.18) * 100) / 100 : 0;
        data.grand_total = data.sub_total + data.gst_total;

        // Frontend Validation
        let isValid = true;
        document.querySelectorAll('.input-error').forEach(el => el.classList.remove('input-error'));
        document.querySelectorAll('.error-message').forEach(el => el.remove());

        const setError = (name, msg) => {
            const input = document.querySelector(`[name="${name}"]`);
            if (input) {
                input.classList.add('input-error');
                const err = document.createElement('p');
                err.className = 'error-message';
                err.textContent = msg;
                input.closest('.space-y-1.5')?.appendChild(err);
                input.focus();
            }
            isValid = false;
        };

        if (!data.invoice_number) setError('invoice_number', 'Invoice number is required');
        if (!data.invoice_date) setError('invoice_date', 'Invoice date is required');
        if (!data.party_name || data.party_name.length < 3) setError('party_name', 'Party name must be at least 3 characters');
        if (!data.mobile_number || !/^[0-9]{10,15}$/.test(data.mobile_number)) setError('mobile_number', 'Valid mobile number required (10-15 digits)');
        if (!data.address) setError('address', 'Address is required');
        
        if (data.invoice_type === 'With GST') {
            if (!data.gstin || !/^[0-9]{2}[A-Z]{5}[0-9]{4}[A-Z]{1}[1-9A-Z]{1}Z[0-9A-Z]{1}$/.test(data.gstin)) setError('gstin', 'Valid GSTIN required');
            if (!data.place_of_supply) setError('place_of_supply', 'Place of supply required');
        }

        if (data.grand_total <= 0) {
            showToast('Please add at least one service with a rate', 'error');
            isValid = false;
        }

        if (!isValid) return;

        // Submit
        try {
            const submitBtn = this.querySelector('button[type="submit"]');
            const originalText = submitBtn.textContent;
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<svg class="animate-spin h-5 w-5 mx-auto" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" fill="none"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>';

            const res = await fetch('../api/sales.php', { 
                method: 'POST', 
                headers: { 'Content-Type': 'application/json' }, 
                body: JSON.stringify(data) 
            });
            
            const r = await res.json();
            
            if (r.success) {
                showToast('Invoice created successfully!');
                setTimeout(() => window.location.href = `print_invoice.php?id=${r.id}`, 1000);
            } else {
                showToast(r.message || 'Validation failed', 'error');
                submitBtn.disabled = false;
                submitBtn.textContent = originalText;
                
                // If it's a duplicate invoice error, focus the field
                if (r.message && r.message.toLowerCase().includes('invoice number')) {
                    document.getElementById('invoice_number').classList.add('input-error');
                    document.getElementById('invoice_number').focus();
                }
            }
        } catch (e) { 
            showToast('System error occurred', 'error');
            submitBtn.disabled = false;
            submitBtn.textContent = originalText;
        }
    });

    addRow();
    toggleGSTFields();
</script>

<?php layout_end(); ?>
