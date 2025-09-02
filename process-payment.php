<?php
require_once 'config.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

// Ambil data dari request
$data = json_decode(file_get_contents('php://input'), true);
$whatsapp = $data['whatsapp'];
$email = $data['email'];
$voucher_type = $data['voucher_type'];
$amount = $data['amount'];

// Simpan transaksi ke database
try {
    $stmt = $pdo->prepare("INSERT INTO transactions (whatsapp, email, voucher_type, amount, status) VALUES (?, ?, ?, ?, 'pending')");
    $stmt->execute([$whatsapp, $email, $voucher_type, $amount]);
    $transaction_id = $pdo->lastInsertId();
    
    // Generate signature untuk Tripay
    $merchantRef = 'VCR' . time() . $transaction_id;
    $signature = hash_hmac('sha256', $merchantRef . $amount, TRIPAY_PRIVATE_KEY);
    
    // Data untuk request ke Tripay
    $tripayData = [
        'method' => 'QRIS',
        'merchant_ref' => $merchantRef,
        'amount' => $amount,
        'customer_name' => 'Customer Voucher',
        'customer_email' => $email,
        'customer_phone' => $whatsapp,
        'order_items' => [
            [
                'name' => 'Voucher Internet ' . $voucher_type,
                'price' => $amount,
                'quantity' => 1
            ]
        ],
        'return_url' => 'https://yourdomain.com/status.php?id=' . $transaction_id,
        'expired_time' => (time() + (24 * 60 * 60)), // 24 jam
        'signature' => $signature
    ];
    
    // Request ke API Tripay
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, TRIPAY_URL . 'transaction/create');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Authorization: Bearer ' . TRIPAY_API_KEY,
        'Content-Type: application/json'
    ]);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($tripayData));
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    if ($httpCode !== 200) {
        throw new Exception('Failed to create transaction: ' . $response);
    }
    
    $tripayResponse = json_decode($response, true);
    
    // Update transaction dengan reference Tripay
    $stmt = $pdo->prepare("UPDATE transactions SET tripay_reference = ?, qr_url = ?, expires_at = ? WHERE id = ?");
    $stmt->execute([
        $tripayResponse['data']['reference'],
        $tripayResponse['data']['qr_url'],
        date('Y-m-d H:i:s', $tripayResponse['data']['expired_time']),
        $transaction_id
    ]);
    
    echo json_encode([
        'success' => true,
        'data' => [
            'qr_url' => $tripayResponse['data']['qr_url'],
            'reference' => $tripayResponse['data']['reference'],
            'expires_at' => $tripayResponse['data']['expired_time']
        ]
    ]);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>