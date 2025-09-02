CREATE TABLE transactions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    whatsapp VARCHAR(20) NOT NULL,
    email VARCHAR(100) NOT NULL,
    voucher_type VARCHAR(50) NOT NULL,
    amount INT NOT NULL,
    status ENUM('pending', 'paid', 'expired', 'failed') DEFAULT 'pending',
    tripay_reference VARCHAR(100),
    voucher_code VARCHAR(50),
    qr_url TEXT,
    expires_at DATETIME,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);