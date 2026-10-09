<?php
// 1. MASUKKAN URL GOOGLE APPS SCRIPT ANDA DI SINI
$api_url = "https://script.google.com/macros/s/AKfycbzDuhzzoyLnsP_1RbZvJLABGlIpYrOiuXB1PH92MfXflr6ksbQN7d69yt3ffv_mIsYd5w/exec";

echo "<h3>Memulai pengiriman data...</h3>";

$data = [
    'action' => 'insert',
    'nama'   => 'Test Mahasiswa ' . rand(1, 100),
    'kelas'  => 'TI-A',
    'wa'     => '08123456789',
    'email'  => 'test@email.com'
];

$ch = curl_init($api_url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_POSTREDIR, 3); // Memaksa tetap POST saat redirect

$response = curl_exec($ch);
$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$curl_error = curl_error($ch);
curl_close($ch);

echo "<div style='background:#f4f4f4; padding:20px; border-radius:8px;'>";
echo "<b>HTTP Code:</b> " . $http_code . "<br><br>";
echo "<b>cURL Error:</b> " . ($curl_error ? $curl_error : "<i>Tidak ada error dari cURL</i>") . "<br><br>";
echo "<b>Balasan dari Google:</b><br>";

// Jika balasan berupa HTML panjang dari Google, kita render agar aman dibaca
echo "<div style='border:1px solid #ccc; padding:10px; background:#fff; overflow:auto; max-height:400px;'>";
echo htmlspecialchars($response);
echo "</div>";
echo "</div>";
?>