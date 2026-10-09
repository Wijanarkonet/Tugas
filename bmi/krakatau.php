<?php
// ==========================================
// BACKEND LOGIC (PHP API ENDPOINT)
// ==========================================
if (isset($_GET['action']) && $_GET['action'] == 'fetch_apify') {
    // Set header response sebagai JSON
    header('Content-Type: application/json');

    // Mengambil data yang dikirim via POST dari Frontend
    $inputJSON = file_get_contents('php://input');
    $input = json_decode($inputJSON, true);

    $token = $input['token'] ?? '';
    $datasetId = $input['dataset_id'] ?? '';

    if (empty($token) || empty($datasetId)) {
        echo json_encode(['error' => 'Token dan Dataset ID tidak boleh kosong.']);
        exit;
    }

    // URL Endpoint Dataset Apify
    $url = "https://api.apify.com/v2/datasets/{$datasetId}/items?token={$token}";

    // Menggunakan cURL untuk request ke Apify (lebih stabil dari file_get_contents)
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_TIMEOUT, 30); // Batas waktu request 30 detik
    $result = curl_exec($ch);
    $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($httpcode !== 200 || $result === false) {
        echo json_encode(['error' => 'Gagal terhubung ke Apify. Pastikan Dataset ID dan Token valid.']);
        exit;
    }

    $apifyData = json_decode($result, true);
    if (!is_array($apifyData)) {
        echo json_encode(['error' => 'Format data dari Apify tidak valid.']);
        exit;
    }

    $processedData = [];

    // Memproses setiap baris data dari Apify
    foreach ($apifyData as $item) {
        // Coba mencari kolom teks komentar (setiap scraper sosmed bisa berbeda nama key-nya)
        $text = $item['text'] ?? $item['comment'] ?? $item['textMessage'] ?? $item['caption'] ?? '';
        if (trim($text) === '') continue; // Skip jika tidak ada teks komentar

        // Mencari nama user
        $user = $item['ownerUsername'] ?? $item['username'] ?? $item['authorName'] ?? $item['author'] ?? 'anonim';

        // Mencari waktu komentar
        $timeStr = $item['timestamp'] ?? $item['takenAt'] ?? $item['date'] ?? date('c');

        // ==========================================
        // PHP SENTIMENT ANALYZER (KEYWORD-BASED)
        // ==========================================
        $lowerText = strtolower($text);
        $sentiment = 'neutral';

        // Pola Regex untuk sentimen negatif (Kepanikan, Bencana, Keluhan)
        if (preg_match('/(abu|vulkanik|sesak|napas|masker|bahaya|takut|korban|evakuasi|darurat|meletus|gempa|rusak|hancur|tolong|panik|waspada|siaga)/i', $lowerText)) {
            $sentiment = 'negative';
        }
        // Pola Regex untuk sentimen positif (Bantuan, Syukur, Doa)
        elseif (preg_match('/(bantuan|aman|terkendali|alhamdulillah|berdoa|doa|donasi|terima kasih|salut|semangat|peduli|selamat)/i', $lowerText)) {
            $sentiment = 'positive';
        }

        // Susun data bersih untuk dikirim ke frontend
        $processedData[] = [
            'id' => $item['id'] ?? uniqid(),
            'source' => 'Komentar Sosmed',
            'time' => date('d M Y, H:i', strtotime($timeStr)),
            'user' => '@' . $user,
            // Potong teks jika terlalu panjang
            'text' => mb_strimwidth($text, 0, 180, '...'),
            'sentiment' => $sentiment
        ];
    }

    echo json_encode(['success' => true, 'data' => $processedData]);
    exit;
}
// Jika bukan request AJAX, PHP akan melanjutkan merender HTML di bawah ini
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SIM Sentimen Komentar Anak Krakatau</title>

    <!-- External Libraries -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f8fafc;
        }

        /* Custom scrollbar untuk tabel */
        .table-container::-webkit-scrollbar {
            width: 6px;
            height: 6px;
        }

        .table-container::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 4px;
        }

        .table-container::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 4px;
        }

        .table-container::-webkit-scrollbar-thumb:hover {
            background: #94a3b8;
        }

        .glass-panel {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
        }
    </style>
</head>

<body class="text-slate-800 h-screen flex overflow-hidden">

    <!-- Sidebar -->
    <aside class="w-64 bg-slate-900 text-white flex flex-col transition-all duration-300 z-20 hidden md:flex shadow-2xl">
        <div class="p-6 flex items-center justify-center border-b border-slate-700/50 bg-slate-950/50">
            <i class="fa-solid fa-volcano text-orange-500 text-3xl mr-3 filter drop-shadow-md"></i>
            <h1 class="text-lg font-bold leading-tight tracking-wide">SIM Sentimen<br><span class="text-xs font-normal text-orange-400">Anak Krakatau (PHP)</span></h1>
        </div>
        <nav class="flex-1 p-4 space-y-2 overflow-y-auto mt-2">
            <a href="#" class="flex items-center space-x-3 bg-blue-600 text-white px-4 py-3 rounded-lg shadow-md shadow-blue-900/20 transition-all">
                <i class="fa-solid fa-chart-pie w-5 text-center"></i>
                <span class="font-medium text-sm">Dashboard</span>
            </a>
            <a href="#" class="flex items-center space-x-3 text-slate-400 hover:bg-slate-800 hover:text-white px-4 py-3 rounded-lg transition-colors">
                <i class="fa-solid fa-database w-5 text-center"></i>
                <span class="font-medium text-sm">Log Komentar</span>
            </a>
            <a href="#" onclick="openApiModal()" class="flex items-center space-x-3 text-slate-400 hover:bg-slate-800 hover:text-white px-4 py-3 rounded-lg transition-colors">
                <i class="fa-solid fa-plug w-5 text-center"></i>
                <span class="font-medium text-sm">Koneksi Apify</span>
            </a>
        </nav>
        <div class="p-4 border-t border-slate-700/50 bg-slate-950/30">
            <div class="flex items-center space-x-3">
                <div class="w-10 h-10 rounded-full bg-gradient-to-tr from-blue-500 to-indigo-500 flex items-center justify-center text-white font-bold border-2 border-slate-700">AD</div>
                <div>
                    <p class="text-sm font-medium text-slate-200">Admin Bencana</p>
                    <p class="text-xs text-slate-500">Pusdalops BPBD</p>
                </div>
            </div>
        </div>
    </aside>

    <!-- Main Content -->
    <div class="flex-1 flex flex-col h-screen overflow-hidden relative">
        <!-- Header -->
        <header class="glass-panel shadow-sm border-b border-slate-200 z-10 p-4 flex justify-between items-center">
            <div class="flex items-center">
                <button class="md:hidden text-slate-500 hover:text-slate-900 focus:outline-none mr-4">
                    <i class="fa-solid fa-bars text-xl"></i>
                </button>
                <div>
                    <h2 class="text-lg font-bold text-slate-800">Analisis Komentar Sosial Media</h2>
                    <p class="text-xs text-slate-500">Memproses teks menggunakan PHP Regex Engine</p>
                </div>
            </div>
            <div class="flex items-center space-x-4">
                <span class="text-sm font-medium text-slate-500 hidden sm:inline-block bg-slate-100 px-3 py-1 rounded-full" id="current-date"></span>
                <button onclick="fetchDataFromPHP()" id="btn-refresh" class="bg-blue-600 text-white shadow-md shadow-blue-200 hover:bg-blue-700 hover:shadow-lg px-4 py-2 rounded-lg text-sm font-semibold transition-all flex items-center">
                    <i class="fa-solid fa-cloud-arrow-down mr-2" id="spinner-icon"></i> Tarik Data Apify
                </button>
            </div>
        </header>

        <!-- Scrollable Content Area -->
        <main class="flex-1 overflow-x-hidden overflow-y-auto p-4 md:p-6 lg:p-8">

            <!-- Alert Status -->
            <div id="status-alert" class="hidden mb-6 p-4 rounded-lg flex items-center text-sm font-medium">
                <i class="fa-solid fa-circle-info mr-2 text-lg"></i>
                <span id="status-text">Pesan status...</span>
            </div>

            <!-- KPI Cards -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 md:gap-6 mb-8">
                <div class="bg-white rounded-xl shadow-sm p-6 border border-slate-200 flex items-center justify-between hover:shadow-md transition-shadow">
                    <div>
                        <p class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-1">Total Komentar</p>
                        <h3 class="text-3xl font-extrabold text-slate-800" id="kpi-total">0</h3>
                    </div>
                    <div class="w-12 h-12 bg-blue-50 border border-blue-100 rounded-full flex items-center justify-center text-blue-600">
                        <i class="fa-solid fa-comments text-xl"></i>
                    </div>
                </div>

                <div class="bg-white rounded-xl shadow-sm p-6 border border-slate-200 flex items-center justify-between hover:shadow-md transition-shadow">
                    <div>
                        <p class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-1">Indikasi Negatif</p>
                        <h3 class="text-3xl font-extrabold text-red-600" id="kpi-negative">0</h3>
                        <p class="text-[10px] text-slate-400 mt-1">Kepanikan/Keluhan</p>
                    </div>
                    <div class="w-12 h-12 bg-red-50 border border-red-100 rounded-full flex items-center justify-center text-red-600">
                        <i class="fa-solid fa-triangle-exclamation text-xl"></i>
                    </div>
                </div>

                <div class="bg-white rounded-xl shadow-sm p-6 border border-slate-200 flex items-center justify-between hover:shadow-md transition-shadow">
                    <div>
                        <p class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-1">Indikasi Netral</p>
                        <h3 class="text-3xl font-extrabold text-slate-600" id="kpi-neutral">0</h3>
                        <p class="text-[10px] text-slate-400 mt-1">Berita/Diskusi Biasa</p>
                    </div>
                    <div class="w-12 h-12 bg-slate-50 border border-slate-200 rounded-full flex items-center justify-center text-slate-600">
                        <i class="fa-solid fa-minus text-xl"></i>
                    </div>
                </div>

                <div class="bg-white rounded-xl shadow-sm p-6 border border-slate-200 flex items-center justify-between hover:shadow-md transition-shadow">
                    <div>
                        <p class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-1">Indikasi Positif</p>
                        <h3 class="text-3xl font-extrabold text-emerald-600" id="kpi-positive">0</h3>
                        <p class="text-[10px] text-slate-400 mt-1">Bantuan/Kondusif</p>
                    </div>
                    <div class="w-12 h-12 bg-emerald-50 border border-emerald-100 rounded-full flex items-center justify-center text-emerald-600">
                        <i class="fa-solid fa-hand-holding-heart text-xl"></i>
                    </div>
                </div>
            </div>

            <!-- Charts Section -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
                <!-- Trend Chart -->
                <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6 lg:col-span-2">
                    <div class="flex justify-between items-center mb-6">
                        <h3 class="text-md font-bold text-slate-800">Tren Sentimen Harian</h3>
                        <div class="bg-slate-100 p-1 rounded-lg flex space-x-1">
                            <button class="px-3 py-1 text-xs font-semibold bg-white shadow-sm rounded-md text-slate-700">7 Hari</button>
                            <button class="px-3 py-1 text-xs font-semibold text-slate-500 hover:text-slate-700">30 Hari</button>
                        </div>
                    </div>
                    <div class="relative h-64 w-full">
                        <canvas id="trendChart"></canvas>
                    </div>
                </div>

                <!-- Distribution Chart -->
                <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6">
                    <h3 class="text-md font-bold text-slate-800 mb-6">Komposisi Sentimen</h3>
                    <div class="relative h-56 w-full flex justify-center">
                        <canvas id="donutChart"></canvas>
                    </div>
                </div>
            </div>

            <!-- Data Table Section -->
            <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden mb-8">
                <div class="p-5 border-b border-slate-200 bg-slate-50 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                    <h3 class="text-md font-bold text-slate-800"><i class="fa-solid fa-list-ul mr-2 text-blue-600"></i>Log Komentar Terbaru</h3>
                    <div class="relative w-full sm:w-72">
                        <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                            <i class="fa-solid fa-search text-slate-400"></i>
                        </div>
                        <input type="text" id="searchInput" class="bg-white border border-slate-300 text-slate-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full pl-10 p-2 shadow-sm" placeholder="Cari dalam komentar...">
                    </div>
                </div>
                <div class="overflow-x-auto table-container">
                    <table class="w-full text-sm text-left text-slate-600">
                        <thead class="text-xs text-slate-500 uppercase bg-slate-100 border-b border-slate-200">
                            <tr>
                                <th scope="col" class="px-6 py-4 font-bold">Waktu</th>
                                <th scope="col" class="px-6 py-4 font-bold">Pengguna</th>
                                <th scope="col" class="px-6 py-4 font-bold w-1/2">Isi Komentar</th>
                                <th scope="col" class="px-6 py-4 font-bold text-center">Analisis Sistem (PHP)</th>
                            </tr>
                        </thead>
                        <tbody id="table-body">
                            <!-- Data akan dimuat melalui PHP/JS -->
                        </tbody>
                    </table>
                </div>
                <div class="p-4 bg-slate-50 border-t border-slate-200 flex items-center justify-between">
                    <span class="text-xs font-medium text-slate-500" id="table-footer-info">Menampilkan 0 data</span>
                </div>
            </div>

        </main>
    </div>

    <!-- Modal Pengaturan API Apify -->
    <div id="api-modal" class="hidden fixed inset-0 bg-slate-900/60 z-50 flex items-center justify-center backdrop-blur-sm transition-opacity">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md mx-4 overflow-hidden transform transition-all">
            <div class="p-6 border-b border-slate-100 flex justify-between items-center">
                <h3 class="text-lg font-bold text-slate-800 flex items-center"><i class="fa-solid fa-key text-blue-600 mr-3"></i> Konfigurasi Apify</h3>
                <button onclick="closeApiModal()" class="text-slate-400 hover:text-red-500 bg-slate-100 hover:bg-red-50 w-8 h-8 rounded-full flex items-center justify-center transition-colors"><i class="fa-solid fa-xmark"></i></button>
            </div>
            <div class="p-6 space-y-5 bg-slate-50/50">
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">Apify API Token</label>
                    <input type="password" id="input-api-token" placeholder="apify_api_..." class="w-full border border-slate-300 rounded-lg p-3 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 shadow-sm transition-all">
                    <p class="text-xs text-slate-500 mt-2"><i class="fa-solid fa-circle-info mr-1"></i>Token digunakan oleh Backend PHP untuk akses API.</p>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">Dataset ID Komentar</label>
                    <input type="text" id="input-dataset-id" placeholder="Contoh: WNxyz123..." class="w-full border border-slate-300 rounded-lg p-3 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 shadow-sm transition-all">
                    <p class="text-xs text-slate-500 mt-2">ID dari Run Scraper Apify (IG/TikTok/FB Comments).</p>
                </div>
            </div>
            <div class="p-5 border-t border-slate-100 bg-white flex justify-end space-x-3">
                <button onclick="closeApiModal()" class="px-5 py-2.5 text-sm font-semibold text-slate-600 bg-white border border-slate-300 rounded-lg hover:bg-slate-50 transition-colors">Batal</button>
                <button onclick="saveApiSettings()" class="px-5 py-2.5 text-sm font-semibold text-white bg-blue-600 rounded-lg hover:bg-blue-700 shadow-md transition-colors">Simpan & Proses PHP</button>
            </div>
        </div>
    </div>

    <script>
        // Set tanggal hari ini
        const dateOptions = {
            weekday: 'long',
            year: 'numeric',
            month: 'long',
            day: 'numeric'
        };
        document.getElementById('current-date').textContent = new Date().toLocaleDateString('id-ID', dateOptions);

        // State variables
        let activeData = [];
        let trendChartObj = null;
        let donutChartObj = null;

        // Data Mockup awal sebelum user input token (agar dashboard tidak kosong)
        const mockData = [{
                id: 1,
                time: "09 Okt 2026, 08:15",
                user: "@warga_lokal",
                text: "Abu vulkanik anak krakatau makin parah pagi ini. Tolong BPBD segera turunkan masker, kami mulai sesak napas di luar rumah.",
                sentiment: "negative"
            },
            {
                id: 2,
                time: "09 Okt 2026, 07:30",
                user: "@berita_banten",
                text: "PVMBG menaikkan status waspada. Abu terpantau mengarah ke Barat Daya terbawa angin darat.",
                sentiment: "neutral"
            },
            {
                id: 3,
                time: "09 Okt 2026, 06:45",
                user: "@relawan_muda",
                text: "Alhamdulillah 500 paket masker dan sembako sudah sampai di titik evakuasi desa pesisir. Terima kasih para donatur!",
                sentiment: "positive"
            },
            {
                id: 4,
                time: "09 Okt 2026, 05:20",
                user: "@wisata_anyer",
                text: "Pantai sementara ditutup untuk umum karena kabut debu vulkanik sangat mengganggu jarak pandang.",
                sentiment: "negative"
            }
        ];

        function renderDashboard() {
            // Hitung KPI
            const total = activeData.length;
            const negativeCount = activeData.filter(d => d.sentiment === 'negative').length;
            const neutralCount = activeData.filter(d => d.sentiment === 'neutral').length;
            const positiveCount = activeData.filter(d => d.sentiment === 'positive').length;

            // Update UI Angka KPI
            // Jika data masih sedikit (mock), kita kalikan untuk visualisasi chart agar terlihat penuh
            const chartMultiplier = activeData === mockData ? 45 : 1;

            document.getElementById('kpi-total').textContent = total;
            document.getElementById('kpi-negative').textContent = negativeCount;
            document.getElementById('kpi-neutral').textContent = neutralCount;
            document.getElementById('kpi-positive').textContent = positiveCount;

            // Render Tabel
            const tableBody = document.getElementById('table-body');
            tableBody.innerHTML = '';

            activeData.forEach(item => {
                let badgeClass, badgeText, iconClass;

                if (item.sentiment === 'negative') {
                    badgeClass = 'bg-red-50 text-red-600 border-red-200';
                    badgeText = 'Negatif';
                    iconClass = 'fa-triangle-exclamation';
                } else if (item.sentiment === 'positive') {
                    badgeClass = 'bg-emerald-50 text-emerald-600 border-emerald-200';
                    badgeText = 'Positif';
                    iconClass = 'fa-check-circle';
                } else {
                    badgeClass = 'bg-slate-50 text-slate-600 border-slate-200';
                    badgeText = 'Netral';
                    iconClass = 'fa-minus';
                }

                const tr = document.createElement('tr');
                tr.className = 'bg-white border-b border-slate-100 hover:bg-slate-50/80 transition-colors';
                tr.innerHTML = `
                    <td class="px-6 py-4 text-xs font-medium text-slate-500 whitespace-nowrap">${item.time}</td>
                    <td class="px-6 py-4 font-semibold text-blue-600 break-words max-w-[120px]">${item.user}</td>
                    <td class="px-6 py-4 text-slate-700 leading-relaxed text-sm"><i class="fa-regular fa-comment-dots text-slate-400 mr-2"></i>${item.text}</td>
                    <td class="px-6 py-4 text-center">
                        <span class="inline-flex items-center px-2.5 py-1 text-xs font-bold rounded-md border ${badgeClass}">
                            <i class="fa-solid ${iconClass} mr-1.5"></i> ${badgeText}
                        </span>
                    </td>
                `;
                tableBody.appendChild(tr);
            });

            document.getElementById('table-footer-info').textContent = `Menampilkan ${total} komentar dari sistem`;

            // Init Charts
            initCharts(negativeCount * chartMultiplier, neutralCount * chartMultiplier, positiveCount * chartMultiplier);
        }

        function initCharts(neg, neu, pos) {
            if (trendChartObj) trendChartObj.destroy();
            if (donutChartObj) donutChartObj.destroy();

            // Donut Chart
            const ctxDonut = document.getElementById('donutChart').getContext('2d');
            donutChartObj = new Chart(ctxDonut, {
                type: 'doughnut',
                data: {
                    labels: ['Negatif (Kepanikan)', 'Netral (Info)', 'Positif (Bantuan)'],
                    datasets: [{
                        data: [neg, neu, pos],
                        backgroundColor: ['#ef4444', '#94a3b8', '#10b981'],
                        borderWidth: 2,
                        borderColor: '#ffffff',
                        hoverOffset: 5
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: {
                                usePointStyle: true,
                                padding: 20,
                                font: {
                                    family: 'Inter',
                                    size: 11
                                }
                            }
                        }
                    },
                    cutout: '70%'
                }
            });

            // Trend Chart (Garis Harian) - Disimulasikan karena data real-time biasanya tidak memiliki riwayat jauh
            const ctxTrend = document.getElementById('trendChart').getContext('2d');
            trendChartObj = new Chart(ctxTrend, {
                type: 'line',
                data: {
                    labels: ['H-6', 'H-5', 'H-4', 'H-3', 'H-2', 'Kemarin', 'Hari Ini'],
                    datasets: [{
                            label: 'Negatif',
                            data: [20, 30, 25, 40, 90, 150, neg],
                            borderColor: '#ef4444',
                            backgroundColor: 'rgba(239, 68, 68, 0.1)',
                            tension: 0.4,
                            fill: true,
                            borderWidth: 2,
                            pointRadius: 3
                        },
                        {
                            label: 'Positif',
                            data: [15, 10, 15, 20, 45, 60, pos],
                            borderColor: '#10b981',
                            backgroundColor: 'transparent',
                            tension: 0.4,
                            borderWidth: 2,
                            pointRadius: 3
                        },
                        {
                            label: 'Netral',
                            data: [50, 45, 60, 55, 80, 110, neu],
                            borderColor: '#94a3b8',
                            backgroundColor: 'transparent',
                            borderDash: [4, 4],
                            tension: 0.4,
                            borderWidth: 2,
                            pointRadius: 0
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    interaction: {
                        mode: 'index',
                        intersect: false
                    },
                    plugins: {
                        legend: {
                            display: false
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            grid: {
                                borderDash: [2, 4],
                                color: '#f1f5f9'
                            },
                            ticks: {
                                font: {
                                    family: 'Inter',
                                    size: 10
                                }
                            }
                        },
                        x: {
                            grid: {
                                display: false
                            },
                            ticks: {
                                font: {
                                    family: 'Inter',
                                    size: 10
                                }
                            }
                        }
                    }
                }
            });
        }

        // Menampilkan Notifikasi
        function showAlert(msg, isError = false) {
            const alertEl = document.getElementById('status-alert');
            const textEl = document.getElementById('status-text');
            alertEl.classList.remove('hidden', 'bg-red-100', 'text-red-700', 'bg-blue-100', 'text-blue-700');

            if (isError) {
                alertEl.classList.add('bg-red-100', 'text-red-700');
                textEl.innerHTML = `<strong>Error:</strong> ${msg}`;
            } else {
                alertEl.classList.add('bg-blue-100', 'text-blue-700');
                textEl.innerHTML = msg;
            }

            // Hilangkan alert setelah 5 detik
            setTimeout(() => {
                alertEl.classList.add('hidden');
            }, 5000);
        }

        // --- FETCH KE PHP ENDPOINT ---
        async function fetchDataFromPHP() {
            const token = localStorage.getItem('php_apify_token');
            const datasetId = localStorage.getItem('php_apify_dataset');

            if (!token || !datasetId) {
                showAlert("Anda menggunakan data simulasi. Silakan set API Token Apify terlebih dahulu via menu Koneksi Apify.");
                activeData = mockData;
                renderDashboard();
                return;
            }

            const btn = document.getElementById('btn-refresh');
            const icon = document.getElementById('spinner-icon');

            // Animasi Loading
            btn.classList.add('opacity-75', 'cursor-not-allowed');
            btn.innerHTML = `<i class="fa-solid fa-circle-notch fa-spin mr-2"></i> Memproses di Server PHP...`;

            try {
                // Request AJAX ke file index.php ini sendiri dengan query parameter action=fetch_apify
                const response = await fetch('?action=fetch_apify', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        token: token,
                        dataset_id: datasetId
                    })
                });

                const result = await response.json();

                if (result.error) {
                    throw new Error(result.error);
                }

                if (result.success && result.data) {
                    activeData = result.data;
                    showAlert(`Berhasil menganalisis ${activeData.length} komentar dari dataset Apify.`);
                }
            } catch (error) {
                showAlert(error.message, true);
                activeData = mockData; // Fallback ke mock data
            } finally {
                renderDashboard();
                // Kembalikan tombol ke semula
                btn.classList.remove('opacity-75', 'cursor-not-allowed');
                btn.innerHTML = `<i class="fa-solid fa-cloud-arrow-down mr-2" id="spinner-icon"></i> Tarik Data Apify`;
            }
        }

        // --- KONTROL MODAL ---
        function openApiModal() {
            document.getElementById('api-modal').classList.remove('hidden');
            document.getElementById('input-api-token').value = localStorage.getItem('php_apify_token') || '';
            document.getElementById('input-dataset-id').value = localStorage.getItem('php_apify_dataset') || '';
        }

        function closeApiModal() {
            document.getElementById('api-modal').classList.add('hidden');
        }

        function saveApiSettings() {
            const token = document.getElementById('input-api-token').value.trim();
            const datasetId = document.getElementById('input-dataset-id').value.trim();

            if (!token || !datasetId) {
                alert("Token dan Dataset ID harus diisi!");
                return;
            }

            localStorage.setItem('php_apify_token', token);
            localStorage.setItem('php_apify_dataset', datasetId);

            closeApiModal();
            fetchDataFromPHP();
        }

        // --- SEARCH BAR ---
        document.getElementById('searchInput').addEventListener('input', function(e) {
            const term = e.target.value.toLowerCase();
            const rows = document.querySelectorAll('#table-body tr');

            rows.forEach(row => {
                const text = row.innerText.toLowerCase();
                row.style.display = text.includes(term) ? '' : 'none';
            });
        });

        // Initialize App
        window.onload = () => {
            fetchDataFromPHP();
        };
    </script>
</body>

</html>