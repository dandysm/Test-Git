<?php
require_once 'config.php';

// Ambil callback data
$callbackData = json_decode(file_get_contents('php://input'), true);
$callbackSignature = isset($_SERVER['HTTP_X_CALLBACK_SIGNATURE']) ? $_SERVER['HTTP_X_CALLBACK_SIGNATURE'] : '';

// Verifikasi signature
$signature = hash_hmac('sha256', json_encode($callbackData), TRIPAY_PRIVATE_KEY);

if ($callbackSignature !== $signature) {
    http_response_code(403);
    die('Invalid signature');
}

// Pastikan status adalah payment
if ($_SERVER['HTTP_X_CALLBACK_EVENT'] !== 'payment_status') {
    http_response_code(403);
    die('Invalid callback event');
}

// Proses data callback
$reference = $callbackData['reference'];
$status = $callbackData['status'];

if ($status === 'PAID') {
    try {
        // Update status transaksi di database
        $stmt = $pdo->prepare("UPDATE transactions SET status = 'paid' WHERE tripay_reference = ?");
        $stmt->execute([$reference]);
        
        // Dapatkan data transaksi
        $stmt = $pdo->prepare("SELECT * FROM transactions WHERE tripay_reference = ?");
        $stmt->execute([$reference]);
        $transaction = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($transaction) {
            // Generate voucher di Mikrotik
            $voucherCode = generateMikrotikVoucher($transaction['voucher_type']);
            
            // Update transaksi dengan kode voucher
            $stmt = $pdo->prepare("UPDATE transactions SET voucher_code = ? WHERE tripay_reference = ?");
            $stmt->execute([$voucherCode, $reference]);
            
            // Kirim notifikasi ke WhatsApp
            sendWhatsAppNotification($transaction['whatsapp'], $voucherCode, $transaction['voucher_type']);
            
            // Kirim notifikasi email
            sendEmailNotification($transaction['email'], $voucherCode, $transaction['voucher_type']);
        }
        
        http_response_code(200);
        echo 'Success';
        
    } catch (Exception $e) {
        http_response_code(500);
        error_log('Callback error: ' . $e->getMessage());
        echo 'Error';
    }
} else if ($status === 'EXPIRED' || $status === 'FAILED') {
    // Update status transaksi jika gagal
    $stmt = $pdo->prepare("UPDATE transactions SET status = ? WHERE tripay_reference = ?");
    $stmt->execute([strtolower($status), $reference]);
    
    http_response_code(200);
    echo 'Success';
}
?>

<?php
// Fungsi generate voucher di Mikrotik
function generateMikrotikVoucher($voucherType) {
    // Tentukan durasi berdasarkan jenis voucher
    $durations = [
        '1 Jam' => '01:00:00',
        '3 Jam' => '03:00:00',
        '5 Jam' => '05:00:00',
        '24 Jam' => '24:00:00'
    ];
    
    $duration = $durations[$voucherType] ?? '01:00:00';
    
    // Generate kode voucher acak
    $voucherCode = generateRandomCode(8);
    
    // API Mikrotik untuk membuat voucher
    $apiUrl = 'https://' . MIKROTIK_IP . '/rest/ip/hotspot/user';
    
    $data = [
        'name' => $voucherCode,
        'password' => $voucherCode,
        'profile' => 'voucher', // Ganti dengan profile yang sesuai di Mikrotik
        'limit-uptime' => $duration,
        'comment' => 'Voucher ' . $voucherType . ' - ' . date('Y-m-d H:i:s')
    ];
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $apiUrl);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
    curl_setopt($ch, CURLOPT_USERPWD, MIKROTIK_USER . ':' . MIKROTIK_PASS);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    if ($httpCode !== 201) {
        throw new Exception('Failed to create Mikrotik voucher: ' . $response);
    }
    
    return $voucherCode;
}

// Fungsi generate kode acak
function generateRandomCode($length = 8) {
    $characters = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $code = '';
    for ($i = 0; $i < $length; $i++) {
        $code .= $characters[rand(0, strlen($characters) - 1)];
    }
    return $code;
}

// Fungsi kirim notifikasi WhatsApp
function sendWhatsAppNotification($whatsapp, $voucherCode, $voucherType) {
    $message = "Terima kasih telah membeli voucher internet!\n\n" .
               "Kode Voucher: *$voucherCode*\n" .
               "Jenis: $voucherType\n" .
               "Cara penggunaan:\n" .
               "1. Hubungkan ke WiFi kami\n" .
               "2. Buka browser dan akan redirect ke halaman login\n" .
               "3. Masukkan kode voucher di atas\n\n" .
               "Selamat menikmati!";
    
    $data = [
        'api_key' => WA_GATEWAY_KEY,
        'number' => $whatsapp,
        'message' => $message
    ];
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, WA_GATEWAY_URL);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
    
    $response = curl_exec($ch);
    curl_close($ch);
    
    return $response;
}

// Fungsi kirim notifikasi email
function sendEmailNotification($email, $voucherCode, $voucherType) {
    $subject = "Kode Voucher Internet Anda";
    $message = "
        <html>
        <head>
            <title>Kode Voucher Internet</title>
        </head>
        <body>
            <h2>Terima kasih telah membeli voucher internet!</h2>
            <p>Berikut adalah detail voucher Anda:</p>
            <table>
                <tr><td><strong>Kode Voucher</strong></td><td>: $voucherCode</td></tr>
                <tr><td><strong>Jenis Voucher</strong></td><td>: $voucherType</td></tr>
            </table>
            <p><strong>Cara penggunaan:</strong></p>
            <ol>
                <li>Hubungkan perangkat Anda ke WiFi kami</li>
                <li>Buka browser, Anda akan diarahkan ke halaman login</li>
                <li>Masukkan kode voucher di atas</li>
            </ol>
            <p>Selamat menikmati!</p>
        </body>
        </html>
    ";
    
    $headers = "MIME-Version: 1.0" . "\r\n";
    $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
    $headers .= "From: Voucher System <noreply@yourdomain.com>" . "\r\n";
    
    mail($email, $subject, $message, $headers);
}
?>