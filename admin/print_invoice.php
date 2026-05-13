<?php
require_once '../includes/middleware.php';
require_once '../includes/database.php';
require_once '../includes/utils.php';

requireAuth();

$db = Database::getInstance();
$id = $_GET['id'] ?? null;

if (!$id) {
    die("Missing Invoice ID");
}

// Fetch invoice
$stmt = $db->prepare("SELECT * FROM invoices WHERE id = ?");
$stmt->execute([$id]);
$invoice = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$invoice) {
    die("Invoice not found");
}

// Fetch items
$stmt = $db->prepare("SELECT * FROM invoice_items WHERE invoice_id = ?");
$stmt->execute([$id]);
$items = $stmt->fetchAll(PDO::FETCH_ASSOC);

$isGST = $invoice['invoice_type'] === 'With GST';

function numberToWordsIndian($number) {
    if ($number == 0) return "Zero Only";
    $no = round($number);
    $decimal = round($number - ($no = floor($number)), 2) * 100;
    $digits_length = strlen($no);
    $i = 0;
    $str = array();
    $words = array(
        0 => '', 1 => 'One', 2 => 'Two', 3 => 'Three', 4 => 'Four', 5 => 'Five', 
        6 => 'Six', 7 => 'Seven', 8 => 'Eight', 9 => 'Nine', 10 => 'Ten', 
        11 => 'Eleven', 12 => 'Twelve', 13 => 'Thirteen', 14 => 'Fourteen', 
        15 => 'Fifteen', 16 => 'Sixteen', 17 => 'Seventeen', 18 => 'Eighteen', 
        19 => 'Nineteen', 20 => 'Twenty', 30 => 'Thirty', 40 => 'Forty', 
        50 => 'Fifty', 60 => 'Sixty', 70 => 'Seventy', 80 => 'Eighty', 90 => 'Ninety'
    );
    $here_digits = array('', 'Hundred', 'Thousand', 'Lakh', 'Crore');
    while ($i < $digits_length) {
        $divider = ($i == 2) ? 10 : 100;
        $number = floor($no % $divider);
        $no = floor($no / $divider);
        $i += ($divider == 10) ? 1 : 2;
        if ($number) {
            $plural = (($counter = count($str)) && $number > 9) ? 's' : null;
            $hundred = ($counter == 1 && $str[0]) ? ' and ' : null;
            $str [] = ($number < 21) ? $words[$number] . ' ' . $here_digits[$counter] . $plural . ' ' . $hundred : $words[floor($number / 10) * 10] . ' ' . $words[$number % 10] . ' ' . $here_digits[$counter] . $plural . ' ' . $hundred;
        } else $str[] = null;
    }
    $Rupees = implode('', array_reverse($str));
    $paise = ($decimal) ? "and " . ($words[$decimal / 10] . " " . $words[$decimal % 10]) . ' Paise' : '';
    return ($Rupees ? $Rupees . 'Rupees ' : '') . $paise . "Only";
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $isGST ? 'Tax Invoice' : 'Invoice' ?> - <?= h($invoice['invoice_number']) ?></title>
    <link rel="icon" type="image/png" href="<?= asset_url('assets/ERP.png') ?>">
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; background-color: #f1f5f9; margin: 0; padding: 0; }
        @media print {
            .no-print { display: none !important; }
            body { background: white; }
            .invoice-box { 
                border: 2px solid #000 !important;
                width: 210mm !important; 
                height: 297mm !important; 
                margin: 0 !important; 
                padding: 8mm !important; 
                background-color: #f4e3ff !important; 
                -webkit-print-color-adjust: exact;
                box-sizing: border-box;
            }
            .content-wrapper { height: 100%; border: 2px solid #000; display: flex; flex-direction: column; background-color: #f4e3ff !important; position: relative; }
        }
        @page { size: A4; margin: 0; }
        .invoice-box { background-color: #f4e3ff; width: 210mm; height: 297mm; margin: 20px auto; padding: 8mm; box-shadow: 0 4px 30px rgba(0,0,0,0.1); box-sizing: border-box; border: 2px solid #000; }
        .content-wrapper { border: 2px solid #000; height: 100%; display: flex; flex-direction: column; background-color: #f4e3ff; }
        .border-b-black { border-bottom: 2px solid #000; }
        .border-t-black { border-top: 2px solid #000; }
        .border-r-black { border-right: 2px solid #000; }
        .table-bordered td, .table-bordered th { border: 2px solid #000; }
        .font-black { font-weight: 800; }
    </style>
</head>
<body class="flex flex-col items-center">
    <div class="w-[210mm] mt-4 mb-4 no-print flex justify-between items-center px-4">
        <a href="sales.php" class="text-xs font-bold text-neutral-500 hover:text-neutral-900 flex items-center gap-2 uppercase">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M15 19l-7-7 7-7" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"></path></svg>
            Back
        </a>
        <button onclick="window.print()" class="px-6 py-2 bg-neutral-900 text-white font-black rounded-lg hover:bg-black text-xs tracking-widest">
            PRINT INVOICE
        </button>
    </div>

    <div class="invoice-box">
        <div class="content-wrapper">
            <!-- COMPANY HEADER -->
            <div class="p-4 border-b-black text-center flex flex-col items-center">
                <img src="<?= asset_url('assets/ERP.png') ?>" alt="Logo" class="w-16 h-16 mb-2 object-contain">
                <h1 class="text-2xl font-black tracking-widest uppercase py-1 border-b-2 border-black mb-1">DECKOID SOLUTION</h1>
                <p class="text-[10px] font-bold text-neutral-600 uppercase">C/O; PARSOTTAM INDUSTRIES</p>
                <p class="text-[10px] font-bold text-neutral-600 uppercase">6, BHAKTINAGAR STATION PLOT, RAJKOT-360002</p>
                <p class="text-[10px] font-bold text-neutral-600">9426225742/9586536724</p>
                <p class="text-[10px] font-bold text-neutral-600 underline">jpipalia123@gmail.com / www.deckoidsolution.com</p>
            </div>

            <!-- TITLE BAR -->
            <div class="flex justify-between items-center px-2 py-0.5 border-b-black font-bold text-[9px] uppercase tracking-tighter">
                <div class="w-1/4">Debit Memo</div>
                <div class="w-1/2 text-center text-xs font-black tracking-widest"><?= $isGST ? 'TAX INVOICE' : 'INVOICE' ?></div>
                <div class="w-1/4 text-right">Original</div>
            </div>

            <!-- PARTY & INVOICE META -->
            <div class="flex border-b-black">
                <div class="w-3/5 p-2 border-r-black text-[10px] font-black space-y-1">
                    <div class="flex gap-1">
                        <span class="w-10">M/s. :</span>
                        <div class="flex-grow">
                            <h3 class="font-black text-xs underline uppercase"><?= h($invoice['party_name']) ?></h3>
                            <p class="font-bold leading-tight mt-1 uppercase"><?= nl2br(h($invoice['address'])) ?></p>
                            <p class="mt-1">Mobile: <?= h($invoice['mobile_number']) ?></p>
                        </div>
                    </div>
                    <div class="mt-2 text-[9px] space-y-0.5">
                        <p>Place of Supply : <?= h($invoice['place_of_supply'] ?: '07-Delhi') ?></p>
                        <?php if ($isGST && $invoice['gstin']): ?><p>GSTIN No. : <span class="tracking-widest"><?= h($invoice['gstin']) ?></span></p><?php endif; ?>
                    </div>
                </div>
                <div class="w-2/5 p-2 text-[10px] font-black space-y-2">
                    <div class="flex">
                        <span class="w-24">Invoice No.</span>
                        <span>: <?= h($invoice['invoice_number']) ?></span>
                    </div>
                    <div class="flex">
                        <span class="w-24">Date</span>
                        <span>: <?= date('d/m/Y', strtotime($invoice['invoice_date'])) ?></span>
                    </div>
                </div>
            </div>

            <!-- SERVICE TABLE -->
            <div class="overflow-hidden">
                <table class="w-full border-collapse table-bordered text-center text-[10px] font-black table-fixed">
                    <thead class="bg-neutral-50/10">
                        <tr class="uppercase text-[9px]">
                            <th class="py-1 px-1 w-10">SrNo</th>
                            <th class="py-1 px-2 text-left overflow-hidden">Product Name</th>
                            <th class="py-1 px-1 w-20">HSN/SAC</th>
                            <th class="py-1 px-1 w-14">Qty</th>
                            <th class="py-1 px-1 w-20">Rate</th>
                            <?php if ($isGST): ?><th class="py-1 px-1 w-16">IGST %</th><?php endif; ?>
                            <th class="py-1 px-1 w-28">Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($items as $index => $item): ?>
                        <tr class="border-b-2 border-black">
                            <td class="py-1.5"><?= $index + 1 ?></td>
                            <td class="py-1.5 px-2 text-left uppercase leading-tight font-black text-[9px]">
                                <?= h($item['service_name']) ?>
                            </td>
                            <td class="py-1.5"><?= h($item['hsn_sac']) ?></td>
                            <td class="py-1.5"><?= number_format($item['qty'], 3) ?></td>
                            <td class="py-1.5"><?= number_format($item['rate'], 2) ?></td>
                            <?php if ($isGST): ?><td class="py-1.5">18.00</td><?php endif; ?>
                            <td class="py-1.5 text-right pr-2"><?= number_format($item['amount'], 2) ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <!-- EXTENDED COLUMN LINES (Flexible Spacer) -->
            <div class="flex-grow flex border-b-black">
                <div class="w-10 border-r-black"></div>
                <div class="flex-grow border-r-black"></div>
                <div class="w-20 border-r-black"></div>
                <div class="w-14 border-r-black"></div>
                <div class="w-20 border-r-black"></div>
                <?php if ($isGST): ?><div class="w-16 border-r-black"></div><?php endif; ?>
                <div class="w-28"></div>
            </div>

            <!-- STICKY FOOTER / TOTALS -->
            <div class="mt-auto">
                <div class="flex border-t-black font-black text-[10px] uppercase">
                    <div class="w-2/3 p-1 px-3 border-r-black">GSTIN No.: 24ACGPP7146N1Z4</div>
                    <div class="w-1/3 p-1 px-3 flex justify-between bg-neutral-50/20">
                        <span>Sub Total</span>
                        <span><?= number_format($invoice['sub_total'], 2) ?></span>
                    </div>
                </div>

                <div class="flex border-t-black">
                    <div class="w-2/3 p-2 border-r-black space-y-0.5 text-[9px] font-black">
                        <p>Bank Name : ICICI BANK</p>
                        <p>Bank A/c. No. : 072805502516</p>
                        <p>RTGS/IFSC Code : ICIC0000728</p>
                    </div>
                    <div class="w-1/3 text-[10px] font-black uppercase">
                        <?php if ($isGST): ?>
                        <div class="flex justify-between px-3 py-2">
                            <span>Integrated Tax <span class="text-[8px] opacity-40">18.00%</span></span>
                            <span><?= number_format($invoice['gst_total'], 2) ?></span>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="flex border-t-black">
                    <div class="w-2/3 p-2 border-r-black text-[9px] font-black uppercase space-y-2">
                        <div class="flex gap-2"><span class="w-24 shrink-0">Total GST :</span><span class="italic font-bold text-[10px]"><?= numberToWordsIndian($invoice['gst_total']) ?></span></div>
                        <div class="flex gap-2"><span class="w-24 shrink-0">Bill Amount :</span><span class="italic font-bold text-[10px]"><?= numberToWordsIndian($invoice['grand_total']) ?></span></div>
                    </div>
                    <div class="w-1/3 flex flex-col">
                        <div class="flex justify-between px-3 py-2 border-b-2 border-black bg-neutral-100/30">
                            <span class="text-[9px] uppercase font-bold">Note :</span>
                        </div>
                        <div class="flex justify-between px-3 py-2 bg-neutral-900 text-white items-center h-full">
                            <span class="text-xs font-black uppercase tracking-tighter">Grand Total</span>
                            <span class="text-lg font-black">₹<?= number_format($invoice['grand_total'], 2) ?></span>
                        </div>
                    </div>
                </div>

                <div class="p-3 text-[8px] font-bold uppercase border-t-black">
                    <div class="flex justify-between items-start">
                        <div class="w-2/3">
                            <p class="font-black underline mb-1 text-[9px]">Terms & Condition :</p>
                            <ol class="list-decimal pl-4 space-y-0.5 opacity-80 font-black tracking-tight">
                                <li>Any payment made is covered under "Advertising contract u/s 194/c, if applicable</li>
                                <li>Tenure of service and payment terms would be governed as per agreement.</li>
                                <li>Interest will be charged @ 24% P.a. if not received on due date.</li>
                                <li>If cheque is bounced Rs.500/- will be debited to your account.</li>
                                <li>Subject to 'RAJKOT' Jurisdiction only. E.&.O.E</li>
                            </ol>
                        </div>
                        <div class="text-right flex flex-col justify-between h-24 w-1/3">
                            <p class="font-black text-[9px]">For, DECKOID SOLUTION</p>
                            <div class="mt-auto"><p class="font-black text-[8px] border-t-2 border-black pt-1 w-full text-center tracking-tighter">(Authorised Signatory)</p></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
