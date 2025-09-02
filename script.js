// Fungsi untuk memproses pembayaran
async function processPayment() {
    const whatsapp = document.getElementById('whatsapp').value;
    const email = document.getElementById('email').value;
    
    if (!whatsapp || !email) {
        alert('Harap isi nomor WhatsApp dan email terlebih dahulu');
        return;
    }
    
    // Validasi format email
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if (!emailRegex.test(email)) {
        alert('Format email tidak valid');
        return;
    }
    
    // Validasi format WhatsApp
    const waRegex = /^[0-9]{9,13}$/;
    if (!waRegex.test(whatsapp)) {
        alert('Format nomor WhatsApp tidak valid. Harap masukkan 9-13 digit angka');
        return;
    }
    
    try {
        const response = await fetch('/process-payment.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                whatsapp: whatsapp,
                email: email,
                voucher_type: selectedProduct.duration + ' Jam',
                amount: selectedProduct.amount
            })
        });
        
        const result = await response.json();
        
        if (result.success) {
            // Tampilkan QR code
            document.getElementById('qrCodeContainer').innerHTML = `
                <div class="bg-white p-3 rounded d-inline-block">
                    <img src="${result.data.qr_url}" alt="QR Code Pembayaran" width="200">
                </div>
            `;
            
            // Simpan reference untuk pengecekan status
            currentReference = result.data.reference;
            
            // Mulai pengecekan status pembayaran
            checkPaymentStatus();
        } else {
            alert('Error: ' + result.message);
        }
    } catch (error) {
        console.error('Error:', error);
        alert('Terjadi kesalahan saat memproses pembayaran');
    }
}

// Fungsi untuk mengecek status pembayaran
async function checkPaymentStatus() {
    try {
        const response = await fetch('/check-status.php?reference=' + currentReference);
        const result = await response.json();
        
        if (result.success) {
            if (result.data.status === 'PAID') {
                // Pembayaran berhasil
                paymentStatus.textContent = 'Pembayaran Berhasil';
                paymentDescription.textContent = 'Pembayaran Anda telah berhasil diverifikasi';
                paymentSpinner.classList.add('d-none');
                
                // Tampilkan icon sukses
                document.getElementById('qrCodeContainer').innerHTML = `
                    <div class="text-success mb-3">
                        <svg xmlns="http://www.w3.org/2000/svg" width="60" height="60" fill="currentColor" class="bi bi-check-circle-fill" viewBox="0 0 16 16">
                            <path d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0zm-3.97-3.03a.75.75 0 0 0-1.08.022L7.477 9.417 5.384 7.323a.75.75 0 0 0-1.06 1.06L6.97 11.03a.75.75 0 0 0 1.079-.02l3.992-4.99a.75.75 0 0 0-.01-1.05z"/>
                        </svg>
                    </div>
                `;
                
                // Dapatkan informasi voucher
                getVoucherInfo();
            } else if (result.data.status === 'EXPIRED' || result.data.status === 'FAILED') {
                // Pembayaran gagal
                paymentStatus.textContent = 'Pembayaran Gagal';
                paymentDescription.textContent = 'Pembayaran Anda ' + result.data.status.toLowerCase();
                paymentSpinner.classList.add('d-none');
            } else {
                // Masih menunggu, cek lagi setelah 5 detik
                setTimeout(checkPaymentStatus, 5000);
            }
        }
    } catch (error) {
        console.error('Error checking status:', error);
        // Coba lagi setelah 5 detik jika error
        setTimeout(checkPaymentStatus, 5000);
    }
}

// Fungsi untuk mendapatkan informasi voucher
async function getVoucherInfo() {
    try {
        const response = await fetch('/get-voucher.php?reference=' + currentReference);
        const result = await response.json();
        
        if (result.success) {
            // Tampilkan voucher
            showVoucher(result.data.voucher_code, result.data.voucher_type);
        } else {
            // Coba lagi jika belum ready
            setTimeout(getVoucherInfo, 2000);
        }
    } catch (error) {
        console.error('Error getting voucher:', error);
        setTimeout(getVoucherInfo, 2000);
    }
}