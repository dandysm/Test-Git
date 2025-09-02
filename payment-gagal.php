<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bantuan Voucher Tidak Terkirim</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .header {
            background: linear-gradient(135deg, #6a11cb 0%, #2575fc 100%);
            color: white;
            padding: 40px 0;
            margin-bottom: 30px;
            border-radius: 0 0 20px 20px;
        }
        .card {
            border-radius: 15px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
        }
        .step-card {
            transition: transform 0.3s;
            height: 100%;
        }
        .step-card:hover {
            transform: translateY(-5px);
        }
        .step-number {
            width: 40px;
            height: 40px;
            background: linear-gradient(135deg, #6a11cb 0%, #2575fc 100%);
            color: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            margin-right: 15px;
        }
        .btn-primary {
            background: linear-gradient(135deg, #6a11cb 0%, #2575fc 100%);
            border: none;
        }
        .btn-primary:hover {
            background: linear-gradient(135deg, #2575fc 0%, #6a11cb 100%);
        }
        .status-badge {
            padding: 8px 15px;
            border-radius: 20px;
            font-weight: bold;
        }
        .status-success {
            background-color: #d4edda;
            color: #155724;
        }
        .status-warning {
            background-color: #fff3cd;
            color: #856404;
        }
        .contact-box {
            background-color: #f8f9fa;
            border-left: 4px solid #6a11cb;
            padding: 20px;
            border-radius: 8px;
        }
        .help-section {
            background-color: #e9ecef;
            padding: 30px;
            border-radius: 15px;
        }
    </style>
</head>
<body>
    <!-- Header -->
    <div class="header text-center">
        <div class="container">
            <h1 class="display-5 fw-bold">Bantuan Voucher Tidak Terkirim</h1>
            <p class="lead">Jika pembayaran Anda berhasil tetapi voucher tidak diterima, ikuti panduan berikut</p>
        </div>
    </div>

    <div class="container">
        <div class="row">
            <div class="col-lg-8 mx-auto">
                <!-- Status Alert -->
                <div class="alert alert-info d-flex align-items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" class="bi bi-info-circle-fill flex-shrink-0 me-2" viewBox="0 0 16 16">
                        <path d="M8 16A8 8 0 1 0 8 0a8 8 0 0 0 0 16zm.93-9.412-1 4.705c-.07.34.029.533.304.533.194 0 .487-.07.686-.246l-.088.416c-.287.346-.92.598-1.465.598-.703 0-1.002-.422-.808-1.319l.738-3.468c.064-.293.006-.399-.287-.47l-.451-.081.082-.381 2.29-.287zM8 5.5a1 1 0 1 1 0-2 1 1 0 0 1 0 2z"/>
                    </svg>
                    <div>Halaman ini membantu Anda mendapatkan voucher yang sudah dibayar tetapi belum diterima</div>
                </div>

                <!-- Verification Form -->
                <div class="card">
                    <div class="card-header bg-white">
                        <h5 class="card-title mb-0">Verifikasi Pembayaran</h5>
                    </div>
                    <div class="card-body">
                        <p class="text-muted">Masukkan detail berikut untuk memverifikasi pembayaran dan mengirim ulang voucher</p>
                        
                        <form id="verificationForm">
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="whatsappVerify" class="form-label">Nomor WhatsApp</label>
                                    <div class="input-group">
                                        <span class="input-group-text">+62</span>
                                        <input type="tel" class="form-control" id="whatsappVerify" placeholder="8123456789" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label for="emailVerify" class="form-label">Alamat Email</label>
                                    <input type="email" class="form-control" id="emailVerify" placeholder="nama@contoh.com" required>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label for="referenceCode" class="form-label">Kode Referensi (jika ada)</label>
                                <input type="text" class="form-control" id="referenceCode" placeholder="TRX-123456">
                            </div>
                            <div class="text-center">
                                <button type="submit" class="btn btn-primary btn-lg">Verifikasi & Cari Pembayaran</button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Result Section -->
                <div class="card d-none" id="resultSection">
                    <div class="card-header bg-white">
                        <h5 class="card-title mb-0">Hasil Verifikasi</h5>
                    </div>
                    <div class="card-body">
                        <div class="text-center mb-4">
                            <div class="spinner-border text-primary mb-3" role="status" id="verificationSpinner">
                                <span class="visually-hidden">Loading...</span>
                            </div>
                            <h5 id="verificationStatus">Memverifikasi pembayaran...</h5>
                            <p class="text-muted" id="verificationDescription">Sedang mencari data pembayaran Anda</p>
                        </div>

                        <div class="d-none" id="successResult">
                            <div class="alert alert-success">
                                <h4>Pembayaran Ditemukan!</h4>
                                <p>Pembayaran Anda telah diverifikasi dan voucher akan dikirim ulang</p>
                            </div>
                            
                            <div class="row mb-4">
                                <div class="col-md-6">
                                    <div class="card step-card">
                                        <div class="card-body">
                                            <h5 class="card-title">Voucher Dikirim via WhatsApp</h5>
                                            <p class="card-text">Kode voucher akan dikirim ke nomor WhatsApp berikut:</p>
                                            <div class="contact-box">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-whatsapp text-success me-2" viewBox="0 0 16 16">
                                                    <path d="M13.601 2.326A7.854 7.854 0 0 0 7.994 0C3.627 0 .068 3.558.064 7.926c0 1.399.366 2.76 1.057 3.965L0 16l4.204-1.102a7.933 7.933 0 0 0 3.79.965h.004c4.368 0 7.926-3.558 7.93-7.93A7.898 7.898 0 0 0 13.6 2.326zM7.994 14.521a6.573 6.573 0 0 1-3.356-.92l-.24-.144-2.494.654.666-2.433-.156-.251a6.56 6.56 0 0 1-1.007-3.505c0-3.626 2.957-6.584 6.591-6.584a6.56 6.56 0 0 1 4.66 1.931 6.557 6.557 0 0 1 1.928 4.66c-.004 3.639-2.961 6.592-6.592 6.592zm3.615-4.934c-.197-.099-1.17-.578-1.353-.646-.182-.065-.315-.099-.445.099-.133.197-.513.646-.627.775-.114.133-.232.148-.43.05-.197-.1-.836-.308-1.592-.985-.59-.525-.985-1.175-1.103-1.372-.114-.198-.011-.304.088-.403.087-.088.197-.232.296-.346.1-.114.133-.198.198-.33.065-.134.034-.248-.015-.347-.05-.099-.445-1.076-.612-1.47-.16-.389-.323-.335-.445-.34-.114-.007-.247-.007-.38-.007a.729.729 0 0 0-.529.247c-.182.198-.691.677-.691 1.654 0 .977.71 1.916.81 2.049.098.133 1.394 2.132 3.383 2.992.47.205.84.326 1.129.418.475.152.904.129 1.246.08.38-.058 1.171-.48 1.338-.943.164-.464.164-.86.114-.943-.049-.084-.182-.133-.38-.232z"/>
                                                </svg>
                                                <span id="resultWhatsApp">+628123456789</span>
                                            </div>
                                            <div class="text-center mt-3">
                                                <button class="btn btn-success" id="sendWhatsApp">Kirim Ulang ke WhatsApp</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="card step-card">
                                        <div class="card-body">
                                            <h5 class="card-title">Voucher Dikirim via Email</h5>
                                            <p class="card-text">Kode voucher akan dikirim ke alamat email berikut:</p>
                                            <div class="contact-box">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-envelope text-primary me-2" viewBox="0 0 16 16">
                                                    <path d="M0 4a2 2 0 0 1 2-2h12a2 2 0 0 1 2 2v8a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2V4Zm2-1a1 1 0 0 0-1 1v.217l7 4.2 7-4.2V4a1 1 0 0 0-1-1H2Zm13 2.383-4.708 2.825L15 11.105V5.383Zm-.034 6.876-5.64-3.471L8 9.583l-1.326-.795-5.64 3.47A1 1 0 0 0 2 13h12a1 1 0 0 0 .966-.741ZM1 11.105l4.708-2.897L1 5.383v5.722Z"/>
                                                </svg>
                                                <span id="resultEmail">nama@contoh.com</span>
                                            </div>
                                            <div class="text-center mt-3">
                                                <button class="btn btn-primary" id="sendEmail">Kirim Ulang ke Email</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="card">
                                <div class="card-body text-center">
                                    <h5>Atau Tampilkan Voucher Langsung</h5>
                                    <p>Jika Anda ingin melihat kode voucher langsung di halaman ini</p>
                                    <button class="btn btn-outline-primary" id="showVoucher">Tampilkan Kode Voucher</button>
                                    
                                    <div class="mt-4 d-none" id="voucherDisplay">
                                        <div class="bg-light p-4 rounded mb-4">
                                            <h3 class="text-primary" id="voucherCodeDisplay">XYZ123-ABC456-DEF789</h3>
                                            <p class="mb-0">Masa aktif: <span id="voucherDurationDisplay">1 jam</span></p>
                                        </div>
                                        <button class="btn btn-primary" id="copyVoucherBtn">Salin Voucher</button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="d-none" id="notFoundResult">
                            <div class="alert alert-warning">
                                <h4>Pembayaran Tidak Ditemukan</h4>
                                <p>Kami tidak dapat menemukan data pembayaran dengan informasi yang diberikan</p>
                            </div>
                            
                            <div class="help-section">
                                <h5>Langkah Selanjutnya:</h5>
                                <ol>
                                    <li>Pastikan nomor WhatsApp dan email yang dimasukkan sudah benar</li>
                                    <li>Periksa kembali inbox email dan WhatsApp Anda</li>
                                    <li>Jika Anda memiliki kode referensi, pastikan untuk memasukkannya</li>
                                    <li>Hubungi layanan pelanggan jika masalah berlanjut</li>
                                </ol>
                                
                                <div class="text-center mt-4">
                                    <button class="btn btn-warning" id="tryAgain">Coba Lagi</button>
                                    <a href="contact.html" class="btn btn-outline-primary ms-2">Hubungi Layanan Pelanggan</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Help Steps -->
                <div class="card">
                    <div class="card-header bg-white">
                        <h5 class="card-title mb-0">Apa yang Harus Dilakukan Jika Voucher Tidak Dikirim?</h5>
                    </div>
                    <div class="card-body">
                        <div class="d-flex align-items-start mb-4">
                            <div class="step-number">1</div>
                            <div>
                                <h5>Verifikasi Pembayaran</h5>
                                <p class="mb-0">Gunakan form di atas untuk memverifikasi bahwa pembayaran Anda sudah berhasil. Sistem akan mencari transaksi berdasarkan nomor WhatsApp dan email yang Anda gunakan.</p>
                            </div>
                        </div>
                        
                        <div class="d-flex align-items-start mb-4">
                            <div class="step-number">2</div>
                            <div>
                                <h5>Kirim Ulang Voucher</h5>
                                <p class="mb-0">Setelah pembayaran terverifikasi, Anda dapat mengirim ulang voucher melalui WhatsApp atau email. Pastikan informasi kontak yang Anda berikan sudah benar.</p>
                            </div>
                        </div>
                        
                        <div class="d-flex align-items-start mb-4">
                            <div class="step-number">3</div>
                            <div>
                                <h5>Tampilkan Voucher Langsung</h5>
                                <p class="mb-0">Jika prefer, Anda dapat menampilkan kode voucher langsung di halaman ini dan menggunakannya untuk mengakses internet.</p>
                            </div>
                        </div>
                        
                        <div class="d-flex align-items-start">
                            <div class="step-number">4</div>
                            <div>
                                <h5>Hubungi Layanan Pelanggan</h5>
                                <p class="mb-0">Jika Anda masih mengalami masalah, hubungi layanan pelanggan kami untuk bantuan lebih lanjut.</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- FAQ Section -->
                <div class="card">
                    <div class="card-header bg-white">
                        <h5 class="card-title mb-0">Pertanyaan Umum</h5>
                    </div>
                    <div class="card-body">
                        <div class="accordion" id="faqAccordion">
                            <div class="accordion-item">
                                <h2 class="accordion-header">
                                    <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#faq1">
                                        Berapa lama waktu yang dibutuhkan untuk menerima voucher?
                                    </button>
                                </h2>
                                <div id="faq1" class="accordion-collapse collapse show" data-bs-parent="#faqAccordion">
                                    <div class="accordion-body">
                                        Biasanya voucher dikirim dalam waktu 1-5 menit setelah pembayaran berhasil. Jika sudah lewat dari 10 menit dan Anda belum menerima voucher, gunakan halaman ini untuk mengirim ulang.
                                    </div>
                                </div>
                            </div>
                            <div class="accordion-item">
                                <h2 class="accordion-header">
                                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq2">
                                        Apa yang harus dilakukan jika saya salah memasukkan nomor WhatsApp atau email?
                                    </button>
                                </h2>
                                <div id="faq2" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                                    <div class="accordion-body">
                                        Jika Anda salah memasukkan kontak informasi, hubungi layanan pelanggan dengan menyertakan bukti pembayaran. Tim kami akan membantu memperbaiki informasi dan mengirimkan voucher ke kontak yang benar.
                                    </div>
                                </div>
                            </div>
                            <div class="accordion-item">
                                <h2 class="accordion-header">
                                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq3">
                                        Bagaimana jika voucher sudah dikirim tetapi tidak bisa digunakan?
                                    </button>
                                </h2>
                                <div id="faq3" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                                    <div class="accordion-body">
                                        Jika voucher tidak bisa digunakan, hubungi layanan pelanggan dengan menyertakan kode voucher yang Anda terima. Tim kami akan memeriksa status voucher dan membantu menyelesaikan masalahnya.
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <footer class="bg-light text-center p-4 mt-5">
        <p class="mb-0">© 2023 Layanan Voucher Internet. All rights reserved.</p>
        <p class="mb-0">Need help? <a href="contact.html">Contact our support team</a></p>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const verificationForm = document.getElementById('verificationForm');
            const resultSection = document.getElementById('resultSection');
            const verificationSpinner = document.getElementById('verificationSpinner');
            const verificationStatus = document.getElementById('verificationStatus');
            const verificationDescription = document.getElementById('verificationDescription');
            const success