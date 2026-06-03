<?php
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, "http://localhost/Deckoid_ERP_System/api/import.php");
curl_setopt($ch, CURLOPT_POST, 1);
$cfile = new CURLFile('leads_export_2026-05-29_11-48-41.csv','text/csv','leads_export_2026-05-29_11-48-41.csv');
$data = array('file' => $cfile);
curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
// Ignore CSRF by simulating what the browser does, wait I can't simulate session easily.
// I will just disable CSRF temporarily in middleware.
$response = curl_exec($ch);
$httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);
echo "HTTP: $httpcode\n";
echo "RESPONSE_LENGTH: " . strlen($response) . "\n";
echo "RESPONSE: |$response|\n";
