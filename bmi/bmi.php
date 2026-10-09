<?php
// ====================================================================
// HealthMatrix BMI Pro - UI Styling (shadcn/ui + Tailwind CSS)
// ====================================================================

// Ambil input form POST atau nilai default
$nama      = isset($_POST['nama']) && trim($_POST['nama']) !== '' ? htmlspecialchars(trim($_POST['nama'])) : "Danang Wijanarko";
$gender    = isset($_POST['gender']) ? $_POST['gender'] : "pria";
$usia      = isset($_POST['usia']) ? max(10, min(120, (int)$_POST['usia'])) : 25;
$tinggi_cm = isset($_POST['tinggi']) ? max(50, min(250, (float)$_POST['tinggi'])) : 172;
$berat_kg  = isset($_POST['berat']) ? max(20, min(300, (float)$_POST['berat'])) : 69;

// Kalkulasi Biometrik
$tinggi_m   = $tinggi_cm / 100;
$bmi        = $tinggi_m > 0 ? ($berat_kg / ($tinggi_m * $tinggi_m)) : 0;
$bmi_bulat  = round($bmi, 1);

// Perhitungan Berat Ideal WHO (18.5 - 24.9)
$berat_ideal_min = round(18.5 * ($tinggi_m * $tinggi_m), 1);
$berat_ideal_max = round(24.9 * ($tinggi_m * $tinggi_m), 1);
$berat_ideal_mid = round(($berat_ideal_min + $berat_ideal_max) / 2, 1);
$selisih_berat   = round($berat_kg - $berat_ideal_mid, 1);

// Kebutuhan Air Harian (35ml / kg BB)
$kebutuhan_air = round(($berat_kg * 35) / 1000, 1);

// BMR & TDEE (Mifflin-St Jeor)
if ($gender === 'pria') {
    $bmr = round((10 * $berat_kg) + (6.25 * $tinggi_cm) - (5 * $usia) + 5);
} else {
    $bmr = round((10 * $berat_kg) + (6.25 * $tinggi_cm) - (5 * $usia) - 161);
}
$tdee = round($bmr * 1.375); // Aktivitas moderat-rendah

// Evaluasi Kategori Tanpa Celah Desimal
if ($bmi < 18.5) {
    $kategori         = "Kekurangan Berat Badan";
    $kategori_sub     = "Underweight";
    $theme_color      = "sky";
    $badge_class      = "bg-sky-500/10 text-sky-400 border-sky-500/30";
    $accent_hex       = "#0ea5e9";
    $status_desc      = "Berat badan berada di bawah rentang acuan sehat. Disarankan meningkatkan cadangan energi dan nutrisi seimbang.";
    $saran_makan      = "Surplus kalori sehat (+300 hingga 500 kcal). Utamakan protein berkualitas (telur, dada ayam, tempe), lemak sehat (alpukat, kacang-kacangan), dan karbohidrat kompleks.";
    $saran_olahraga   = "Fokus pada latihan beban (strength & resistance training) 3-4x seminggu untuk menstimulasi massa otot tanpa pembakaran kalori berlebih dari kardio panjang.";
    $risiko_kesehatan = "Potensi anemia, penurunan imunitas tubuh, kerapuhan struktur tulang (osteopenia/osteoporosis), dan defisiensi mikronutrien penting.";
} elseif ($bmi < 25.0) {
    $kategori         = "Berat Badan Ideal";
    $kategori_sub     = "Normal Weight";
    $theme_color      = "emerald";
    $badge_class      = "bg-emerald-500/10 text-emerald-400 border-emerald-500/30";
    $accent_hex       = "#10b981";
    $status_desc      = "Komposisi tubuh berada dalam rasio optimal. Risiko gangguan metabolik dan penyakit kardiovaskular tergolong paling rendah.";
    $saran_makan      = "Pertahankan pola gizi seimbang (Piring Sehat: 50% sayur/buah, 25% protein bebas lemak, 25% karbohidrat utuh). Cukupi hidrasi harian minimal $kebutuhan_air Liter.";
    $saran_olahraga   = "Kombinasikan latihan kardio moderat 150 menit/minggu (sepeda santai, jogging, renang) bersama latihan ketahanan fisik 2x seminggu.";
    $risiko_kesehatan = "Risiko kesehatan minimal. Pertahankan stabilitas berat badan dengan tidur cukup 7-8 jam dan manajemen stres yang teratur.";
} elseif ($bmi < 30.0) {
    $kategori         = "Kelebihan Berat Badan";
    $kategori_sub     = "Overweight";
    $theme_color      = "amber";
    $badge_class      = "bg-amber-500/10 text-amber-400 border-amber-500/30";
    $accent_hex       = "#f59e0b";
    $status_desc      = "Cadangan lemak tubuh melebihi ambang normal. Intervensi gaya hidup dini sangat efektif mengembalikan ke batas ideal.";
    $saran_makan      = "Terapkan defisit kalori teratur (-300 s/d 400 kcal). Hindari minuman manis kemasan, kurangi asupan karbo olahan/gorengan, dan perbanyak serat larut air.";
    $saran_olahraga   = "Targetkan 8.000 - 10.000 langkah harian. Tambahkan kardio HIIT terukur atau circuit training 3-4x seminggu untuk pembakaran kalori maksimal.";
    $risiko_kesehatan = "Peningkatan risiko resistensi insulin, hipertensi derajat awal, serta beban berlebih pada sendi penopang berat (lutut dan tumit).";
} else {
    $kategori         = "Kategori Obesitas";
    $kategori_sub     = "Obese (High Risk)";
    $theme_color      = "rose";
    $badge_class      = "bg-rose-500/10 text-rose-400 border-rose-500/30";
    $accent_hex       = "#f43f5e";
    $status_desc      = "Akumulasi jaringan adiposa tinggi. Sangat dianjurkan untuk berkonsultasi medis guna menyusun target penurunan berat bertahap.";
    $saran_makan      = "Terapkan diet rendah indeks glikemik dan batasi kalori harian terukur. Prioritaskan makanan utuh (*whole foods*) dan catat log nutrisi harian.";
    $saran_olahraga   = "Utamakan olahraga ramah sendi (*low-impact*) seperti berenang, jalan santai di permukaan rata, atau sepeda statis sebelum bertransisi ke intensitas tinggi.";
    $risiko_kesehatan = "Risiko tinggi penyakit jantung koroner, diabetes melitus tipe-2, dislipidemia, perlemakan hati (*fatty liver*), dan sleep apnea.";
}

// Visual speedometer needle angle (-90deg s/d +90deg)
$bmi_clamped = max(10, min(40, $bmi));
$needle_deg  = -90 + (($bmi_clamped - 10) / 30) * 180;
?>
<!DOCTYPE html>
<html lang="id" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>HealthMatrix &mdash; Clinical BMI &amp; Body Metrics</title>
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    colors: {
                        border: "hsl(240 3.7% 15.9%)",
                        input: "hsl(240 3.7% 15.9%)",
                        ring: "hsl(240 4.9% 83.9%)",
                        background: "hsl(240 10% 3.9%)",
                        foreground: "hsl(0 0% 98%)",
                        primary: {
                            DEFAULT: "hsl(0 0% 98%)",
                            foreground: "hsl(240 5.9% 10%)",
                        },
                        secondary: {
                            DEFAULT: "hsl(240 3.7% 15.9%)",
                            foreground: "hsl(0 0% 98%)",
                        },
                        muted: {
                            DEFAULT: "hsl(240 3.7% 15.9%)",
                            foreground: "hsl(240 5% 64.9%)",
                        },
                        card: {
                            DEFAULT: "hsl(240 10% 6.5%)",
                            foreground: "hsl(0 0% 98%)",
                        },
                    },
                    fontFamily: {
                        sans: ['Plus Jakarta Sans', '-apple-system', 'BlinkMacSystemFont', 'sans-serif'],
                        mono: ['Space Grotesk', 'monospace'],
                    },
                }
            }
        }
    </script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Space+Grotesk:wght@500;700;800&display=swap" rel="stylesheet">
    <style>
        /* Custom shadcn range slider */
        input[type="range"]::-webkit-slider-thumb {
            -webkit-appearance: none;
            width: 20px;
            height: 20px;
            border-radius: 50%;
            background: #ffffff;
            border: 2px solid hsl(240 3.7% 15.9%);
            box-shadow: 0 2px 8px rgba(0,0,0,0.4);
            cursor: pointer;
            transition: transform 0.15s ease;
        }
        input[type="range"]::-webkit-slider-thumb:hover {
            transform: scale(1.15);
        }
        @media print {
            body { background: white !important; color: black !important; }
            .no-print { display: none !important; }
            .print-card { box-shadow: none !important; border: 1px solid #ccc !important; background: white !important; color: black !important; }
            .print-text-dark { color: #000 !important; }
        }
    </style>
</head>
<body class="bg-background text-foreground min-h-screen font-sans antialiased selection:bg-zinc-700 selection:text-white">

    <!-- Top Navigation Bar (shadcn Navigation Style) -->
    <header class="border-b border-border bg-background/80 backdrop-blur-md sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-xl bg-zinc-100 text-zinc-900 flex items-center justify-center font-black">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                    </svg>
                </div>
                <div>
                    <span class="font-extrabold text-base tracking-tight text-white flex items-center gap-2">
                        HealthMatrix <span class="text-xs px-2 py-0.5 rounded-full border border-border bg-secondary text-zinc-400 font-mono font-medium">v3.0</span>
                    </span>
                </div>
            </div>

            <div class="flex items-center gap-3">
                <div class="hidden sm:inline-flex items-center gap-2 px-3 py-1 rounded-full border border-border bg-secondary/50 text-xs text-zinc-400 font-medium">
                    <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                    <span>WHO &amp; Kemenkes Standard</span>
                </div>
                <button type="button" onclick="window.print()" class="no-print inline-flex items-center gap-2 px-3.5 py-1.5 rounded-lg border border-border bg-secondary hover:bg-zinc-800 text-xs font-semibold text-zinc-200 transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                    </svg>
                    <span>Cetak PDF</span>
                </button>
            </div>
        </div>
    </header>

    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
        
        <!-- Hero Header -->
        <div class="mb-10 text-left sm:text-center max-w-3xl mx-auto">
            <h1 class="text-3xl sm:text-4xl font-extrabold tracking-tight text-white mb-3">
                Analisis Indeks Massa Tubuh &amp; Komposisi Fisik
            </h1>
            <p class="text-muted-foreground text-sm sm:text-base">
                Hitung skor BMI presisi, periksa rentang berat badan ideal, kebutuhan hidrasi air harian, serta panduan nutrisi &amp; aktivitas fisik terarah.
            </p>
        </div>

        <!-- 2-Column Responsive Dashboard Grid -->
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
            
            <!-- LEFT COLUMN: Parameter Input Card (shadcn Card - 5 Cols) -->
            <div class="lg:col-span-5 space-y-6">
                <div class="rounded-2xl border border-border bg-card p-6 sm:p-7 shadow-xl space-y-6">
                    
                    <!-- Card Title & Subtitle -->
                    <div class="border-b border-border pb-5">
                        <h2 class="text-lg font-bold text-white tracking-tight flex items-center gap-2">
                            <svg class="w-5 h-5 text-zinc-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                            </svg>
                            <span>Parameter Biometrik</span>
                        </h2>
                        <p class="text-xs text-muted-foreground mt-1">Masukkan data tubuh Anda untuk kalkulasi instan.</p>
                    </div>

                    <form method="POST" id="bmiForm" class="space-y-5">
                        
                        <!-- Nama Pemilik -->
                        <div class="space-y-2">
                            <label class="text-xs font-semibold text-zinc-300 uppercase tracking-wider">Nama Lengkap</label>
                            <input type="text" name="nama" id="inputNama" value="<?= $nama ?>" 
                                   class="w-full bg-secondary/60 border border-border rounded-xl px-4 py-2.5 text-sm text-white placeholder-zinc-500 focus:outline-none focus:ring-2 focus:ring-zinc-400 focus:border-zinc-400 transition"
                                   placeholder="Ketik nama Anda...">
                        </div>

                        <!-- Gender Selector (shadcn Toggle Group) -->
                        <div class="space-y-2">
                            <label class="text-xs font-semibold text-zinc-300 uppercase tracking-wider">Jenis Kelamin</label>
                            <input type="hidden" name="gender" id="inputGender" value="<?= $gender ?>">
                            <div class="grid grid-cols-2 p-1 bg-secondary/80 border border-border rounded-xl gap-1">
                                <button type="button" onclick="setGender('pria')" id="btnGenderPria"
                                        class="py-2 text-xs font-semibold rounded-lg flex items-center justify-center gap-2 transition duration-200 <?= $gender === 'pria' ? 'bg-zinc-100 text-zinc-950 shadow-sm font-bold' : 'text-zinc-400 hover:text-white' ?>">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <circle cx="10" cy="14" r="5"></circle>
                                        <path d="M19 5l-5.4 5.4M19 5h-5M19 5v5"></path>
                                    </svg>
                                    <span>Pria</span>
                                </button>
                                <button type="button" onclick="setGender('wanita')" id="btnGenderWanita"
                                        class="py-2 text-xs font-semibold rounded-lg flex items-center justify-center gap-2 transition duration-200 <?= $gender === 'wanita' ? 'bg-zinc-100 text-zinc-950 shadow-sm font-bold' : 'text-zinc-400 hover:text-white' ?>">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <circle cx="12" cy="9" r="5"></circle>
                                        <path d="M12 14v7M9 18h6"></path>
                                    </svg>
                                    <span>Wanita</span>
                                </button>
                            </div>
                        </div>

                        <!-- Usia Stepper & Slider -->
                        <div class="space-y-2">
                            <div class="flex justify-between items-center text-xs">
                                <span class="font-semibold text-zinc-300 uppercase tracking-wider">Usia Pengguna</span>
                                <span class="font-mono font-bold text-white"><span id="valUsia"><?= $usia ?></span> <small class="text-zinc-400 font-sans">tahun</small></span>
                            </div>
                            <input type="range" name="usia" id="sliderUsia" min="12" max="90" value="<?= $usia ?>"
                                   class="w-full h-2 bg-secondary rounded-lg appearance-none cursor-pointer outline-none"
                                   oninput="updateMetrics()">
                        </div>

                        <!-- Tinggi Badan -->
                        <div class="space-y-2">
                            <div class="flex justify-between items-center text-xs">
                                <span class="font-semibold text-zinc-300 uppercase tracking-wider">Tinggi Badan</span>
                                <span class="font-mono font-bold text-white"><span id="valTinggi"><?= $tinggi_cm ?></span> <small class="text-zinc-400 font-sans">cm</small></span>
                            </div>
                            <input type="range" name="tinggi" id="sliderTinggi" min="100" max="220" value="<?= $tinggi_cm ?>"
                                   class="w-full h-2 bg-secondary rounded-lg appearance-none cursor-pointer outline-none"
                                   oninput="updateMetrics()">
                        </div>

                        <!-- Berat Badan -->
                        <div class="space-y-2">
                            <div class="flex justify-between items-center text-xs">
                                <span class="font-semibold text-zinc-300 uppercase tracking-wider">Berat Badan</span>
                                <span class="font-mono font-bold text-white"><span id="valBerat"><?= $berat_kg ?></span> <small class="text-zinc-400 font-sans">kg</small></span>
                            </div>
                            <input type="range" name="berat" id="sliderBerat" min="30" max="180" step="0.5" value="<?= $berat_kg ?>"
                                   class="w-full h-2 bg-secondary rounded-lg appearance-none cursor-pointer outline-none"
                                   oninput="updateMetrics()">
                        </div>

                        <!-- Quick Presets -->
                        <div class="pt-2 border-t border-border space-y-2">
                            <span class="text-[11px] text-muted-foreground uppercase font-semibold tracking-wider block">Simulasi Cepat:</span>
                            <div class="flex flex-wrap gap-2">
                                <button type="button" onclick="applyPreset('Danang (Ideal)', 172, 69, 25, 'pria')"
                                        class="text-xs px-2.5 py-1 rounded-md border border-border bg-secondary/50 hover:bg-secondary text-zinc-300 transition">
                                    Danang (172cm/69kg)
                                </button>
                                <button type="button" onclick="applyPreset('Underweight', 170, 48, 22, 'wanita')"
                                        class="text-xs px-2.5 py-1 rounded-md border border-border bg-secondary/50 hover:bg-secondary text-zinc-300 transition">
                                    Underweight
                                </button>
                                <button type="button" onclick="applyPreset('Overweight', 168, 78, 30, 'pria')"
                                        class="text-xs px-2.5 py-1 rounded-md border border-border bg-secondary/50 hover:bg-secondary text-zinc-300 transition">
                                    Overweight
                                </button>
                                <button type="button" onclick="applyPreset('Obesitas', 165, 95, 35, 'pria')"
                                        class="text-xs px-2.5 py-1 rounded-md border border-border bg-secondary/50 hover:bg-secondary text-zinc-300 transition">
                                    Obesitas
                                </button>
                            </div>
                        </div>

                        <!-- Submit Button -->
                        <div class="pt-2">
                            <button type="submit" 
                                    class="w-full py-3.5 rounded-xl bg-zinc-100 hover:bg-white text-zinc-900 font-bold text-sm tracking-wide transition duration-200 shadow-md active:scale-[0.99] flex items-center justify-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                                </svg>
                                <span>Simpan &amp; Sinkronkan Data</span>
                            </button>
                        </div>

                    </form>
                </div>
            </div>

            <!-- RIGHT COLUMN: Visualizer & Health Analytics (7 Cols) -->
            <div class="lg:col-span-7 space-y-6">
                
                <!-- Main Result Card (Hero Visualizer) -->
                <div class="rounded-2xl border border-border bg-card p-6 sm:p-8 shadow-xl relative overflow-hidden print-card">
                    
                    <!-- Top Strip Info -->
                    <div class="flex items-center justify-between pb-6 border-b border-border">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-full bg-zinc-800 border border-zinc-700 flex items-center justify-center text-sm font-bold text-white uppercase font-mono" id="avatarLetter">
                                <?= mb_substr($nama, 0, 1) ?>
                            </div>
                            <div>
                                <h3 class="text-base font-bold text-white leading-tight" id="displayNama"><?= $nama ?></h3>
                                <p class="text-xs text-muted-foreground" id="displayMeta">
                                    <?= ucfirst($gender) ?> &bull; <?= $usia ?> Tahun &bull; <?= $tinggi_cm ?> cm / <?= $berat_kg ?> kg
                                </p>
                            </div>
                        </div>

                        <!-- Dynamic shadcn Badge -->
                        <div id="badgeContainer" class="px-3 py-1 rounded-full border text-xs font-bold font-mono tracking-wide uppercase <?= $badge_class ?>">
                            <span id="badgeText"><?= $kategori_sub ?></span>
                        </div>
                    </div>

                    <!-- Speedometer Radial Arc -->
                    <div class="py-6 flex flex-col items-center justify-center relative">
                        <div class="w-64 sm:w-72 relative">
                            <svg class="w-full" viewBox="0 0 300 160">
                                <defs>
                                    <linearGradient id="bmiArcGrad" x1="0%" y1="0%" x2="100%" y2="0%">
                                        <stop offset="0%" stop-color="#0ea5e9" />
                                        <stop offset="35%" stop-color="#10b981" />
                                        <stop offset="70%" stop-color="#f59e0b" />
                                        <stop offset="100%" stop-color="#f43f5e" />
                                    </linearGradient>
                                </defs>
                                <!-- Background Arc -->
                                <path d="M 30 135 A 120 120 0 0 1 270 135" fill="none" stroke="hsl(240 3.7% 15.9%)" stroke-width="20" stroke-linecap="round"/>
                                <!-- Gradient Track -->
                                <path d="M 30 135 A 120 120 0 0 1 270 135" fill="none" stroke="url(#bmiArcGrad)" stroke-width="14" stroke-linecap="round"/>
                                <!-- Needle -->
                                <g id="gaugeNeedle" style="transform-origin: 150px 135px; transform: rotate(<?= $needle_deg ?>deg); transition: transform 0.8s cubic-bezier(0.34, 1.56, 0.64, 1);">
                                    <polygon points="148,135 152,135 151,32 149,32" fill="#ffffff" />
                                    <circle cx="150" cy="135" r="9" fill="#ffffff" />
                                    <circle cx="150" cy="135" r="5" fill="#18181b" />
                                </g>
                            </svg>
                        </div>

                        <!-- Huge BMI Score Display -->
                        <div class="text-center -mt-6">
                            <div class="text-5xl sm:text-6xl font-extrabold font-mono text-white tracking-tight" id="bmiNumber">
                                <?= number_format($bmi_bulat, 1) ?>
                            </div>
                            <div class="text-sm font-semibold text-zinc-300 mt-1" id="categoryTitle">
                                <?= $kategori ?>
                            </div>
                            <p class="text-xs text-muted-foreground max-w-md mx-auto mt-2 px-4" id="categoryDescription">
                                <?= $status_desc ?>
                            </p>
                        </div>
                    </div>

                    <!-- Segmented Scale Indicator -->
                    <div class="pt-4 border-t border-border">
                        <div class="h-2 rounded-full flex overflow-hidden bg-secondary gap-1 p-0.5">
                            <div class="bg-sky-500 rounded-sm" style="flex: 18.5;" title="Underweight (&lt; 18.5)"></div>
                            <div class="bg-emerald-500 rounded-sm" style="flex: 6.4;" title="Ideal (18.5 - 24.9)"></div>
                            <div class="bg-amber-500 rounded-sm" style="flex: 5.0;" title="Overweight (25.0 - 29.9)"></div>
                            <div class="bg-rose-500 rounded-sm" style="flex: 10.0;" title="Obesitas (&ge; 30.0)"></div>
                        </div>
                        <div class="flex justify-between text-[10px] text-zinc-500 font-mono mt-1.5">
                            <span>&lt; 18.5</span>
                            <span>18.5 - 24.9</span>
                            <span>25.0 - 29.9</span>
                            <span>&ge; 30.0</span>
                        </div>
                    </div>

                </div>

                <!-- 3 Pillars Stat Cards (shadcn Style) -->
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    
                    <!-- Berat Ideal Card -->
                    <div class="rounded-xl border border-border bg-card p-5 shadow-sm space-y-1">
                        <span class="text-xs text-muted-foreground font-medium flex items-center gap-1.5">
                            <svg class="w-3.5 h-3.5 text-zinc-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M3 6l3 18h12l3-18H3z"></path></svg>
                            <span>Berat Ideal (WHO)</span>
                        </span>
                        <div class="text-xl font-bold font-mono text-white pt-1">
                            <span id="statBeratIdeal"><?= $berat_ideal_min ?> - <?= $berat_ideal_max ?></span> <small class="text-xs font-sans text-zinc-400">kg</small>
                        </div>
                        <p class="text-[11px] text-zinc-400 pt-1" id="statSelisih">
                            <?= $selisih_berat > 0 ? "Kelebihan +{$selisih_berat} kg" : ($selisih_berat < 0 ? "Kurang " . abs($selisih_berat) . " kg" : "Ideal sempurna") ?>
                        </p>
                    </div>

                    <!-- Kebutuhan Air Card -->
                    <div class="rounded-xl border border-border bg-card p-5 shadow-sm space-y-1">
                        <span class="text-xs text-muted-foreground font-medium flex items-center gap-1.5">
                            <svg class="w-3.5 h-3.5 text-zinc-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M12 2.69l5.66 5.66a8 8 0 11-11.31 0z"></path></svg>
                            <span>Hidrasi Harian</span>
                        </span>
                        <div class="text-xl font-bold font-mono text-white pt-1">
                            <span id="statAir"><?= $kebutuhan_air ?></span> <small class="text-xs font-sans text-zinc-400">Liter/hari</small>
                        </div>
                        <p class="text-[11px] text-zinc-400 pt-1" id="statGelas">
                            Setara &plusmn; <?= round($kebutuhan_air * 4) ?> gelas air
                        </p>
                    </div>

                    <!-- Energi BMR Card -->
                    <div class="rounded-xl border border-border bg-card p-5 shadow-sm space-y-1">
                        <span class="text-xs text-muted-foreground font-medium flex items-center gap-1.5">
                            <svg class="w-3.5 h-3.5 text-zinc-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                            <span>Energi Basal (BMR)</span>
                        </span>
                        <div class="text-xl font-bold font-mono text-white pt-1">
                            <span id="statBmr"><?= number_format($bmr) ?></span> <small class="text-xs font-sans text-zinc-400">kcal</small>
                        </div>
                        <p class="text-[11px] text-zinc-400 pt-1" id="statTdee">
                            TDEE: &plusmn;<?= number_format($tdee) ?> kcal/hari
                        </p>
                    </div>

                </div>

                <!-- shadcn Tabs Component (Panduan Medis & Gaya Hidup) -->
                <div class="rounded-2xl border border-border bg-card p-6 shadow-xl space-y-5">
                    
                    <!-- Tab Headers -->
                    <div class="flex items-center gap-2 p-1 bg-secondary/70 border border-border rounded-xl">
                        <button type="button" onclick="switchTab('nutrisi')" id="tabBtn-nutrisi"
                                class="tab-btn flex-1 py-2 text-xs font-bold rounded-lg transition duration-200 bg-zinc-100 text-zinc-950 shadow-sm">
                            🥗 Nutrisi &amp; Diet
                        </button>
                        <button type="button" onclick="switchTab('olahraga')" id="tabBtn-olahraga"
                                class="tab-btn flex-1 py-2 text-xs font-bold rounded-lg transition duration-200 text-zinc-400 hover:text-white">
                            🏃 Aktivitas Fisik
                        </button>
                        <button type="button" onclick="switchTab('risiko')" id="tabBtn-risiko"
                                class="tab-btn flex-1 py-2 text-xs font-bold rounded-lg transition duration-200 text-zinc-400 hover:text-white">
                            ⚠️ Faktor Risiko
                        </button>
                    </div>

                    <!-- Tab Contents -->
                    <div id="tabContent-nutrisi" class="tab-pane space-y-2">
                        <h4 class="text-xs font-bold text-zinc-200 uppercase tracking-wider">Rekomendasi Pola Nutrisi:</h4>
                        <p class="text-sm text-muted-foreground leading-relaxed" id="textSaranMakan">
                            <?= $saran_makan ?>
                        </p>
                    </div>

                    <div id="tabContent-olahraga" class="tab-pane hidden space-y-2">
                        <h4 class="text-xs font-bold text-zinc-200 uppercase tracking-wider">Jadwal &amp; Pola Olahraga:</h4>
                        <p class="text-sm text-muted-foreground leading-relaxed" id="textSaranOlahraga">
                            <?= $saran_olahraga ?>
                        </p>
                    </div>

                    <div id="tabContent-risiko" class="tab-pane hidden space-y-2">
                        <h4 class="text-xs font-bold text-zinc-200 uppercase tracking-wider">Potensi Faktor Risiko Medis:</h4>
                        <p class="text-sm text-muted-foreground leading-relaxed" id="textRisiko">
                            <?= $risiko_kesehatan ?>
                        </p>
                    </div>

                    <!-- Action Bar -->
                    <div class="pt-4 border-t border-border flex flex-col sm:flex-row gap-3 no-print">
                        <button type="button" onclick="copySummary()" 
                                class="flex-1 py-2.5 rounded-lg border border-border bg-secondary/60 hover:bg-secondary text-xs font-semibold text-zinc-200 transition flex items-center justify-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M8 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-1M8 5a2 2 0 002 2h2a2 2 0 002-2M8 5a2 2 0 012-2h2a2 2 0 012 2m0 0h2a2 2 0 012 2v3m2 4H10m0 0l3-3m-3 3l3 3" />
                            </svg>
                            <span id="copyTextBtn">Salin Ringkasan Medis</span>
                        </button>
                        <button type="button" onclick="window.print()" 
                                class="flex-1 py-2.5 rounded-lg bg-zinc-100 hover:bg-white text-zinc-950 text-xs font-bold transition flex items-center justify-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                            </svg>
                            <span>Cetak Laporan Pasien</span>
                        </button>
                    </div>

                </div>

            </div>

        </div>

    </main>

    <!-- Client-Side Reactive Logic (Zero Page Reload) -->
    <script>
        let currentGender = '<?= $gender ?>';

        function setGender(val) {
            currentGender = val;
            document.getElementById('inputGender').value = val;
            const btnPria = document.getElementById('btnGenderPria');
            const btnWanita = document.getElementById('btnGenderWanita');

            if (val === 'pria') {
                btnPria.className = 'py-2 text-xs font-semibold rounded-lg flex items-center justify-center gap-2 transition duration-200 bg-zinc-100 text-zinc-950 shadow-sm font-bold';
                btnWanita.className = 'py-2 text-xs font-semibold rounded-lg flex items-center justify-center gap-2 transition duration-200 text-zinc-400 hover:text-white';
            } else {
                btnWanita.className = 'py-2 text-xs font-semibold rounded-lg flex items-center justify-center gap-2 transition duration-200 bg-zinc-100 text-zinc-950 shadow-sm font-bold';
                btnPria.className = 'py-2 text-xs font-semibold rounded-lg flex items-center justify-center gap-2 transition duration-200 text-zinc-400 hover:text-white';
            }
            updateMetrics();
        }

        function switchTab(tabKey) {
            document.querySelectorAll('.tab-pane').forEach(el => el.classList.add('hidden'));
            document.querySelectorAll('.tab-btn').forEach(btn => {
                btn.className = 'tab-btn flex-1 py-2 text-xs font-bold rounded-lg transition duration-200 text-zinc-400 hover:text-white';
            });

            document.getElementById('tabContent-' + tabKey).classList.remove('hidden');
            document.getElementById('tabBtn-' + tabKey).className = 'tab-btn flex-1 py-2 text-xs font-bold rounded-lg transition duration-200 bg-zinc-100 text-zinc-950 shadow-sm';
        }

        function applyPreset(name, tinggi, berat, usia, gender) {
            document.getElementById('sliderTinggi').value = tinggi;
            document.getElementById('sliderBerat').value = berat;
            document.getElementById('sliderUsia').value = usia;
            setGender(gender);
        }

        function updateMetrics() {
            const nama = document.getElementById('inputNama').value || 'Pengguna';
            const tinggi = parseFloat(document.getElementById('sliderTinggi').value);
            const berat = parseFloat(document.getElementById('sliderBerat').value);
            const usia = parseInt(document.getElementById('sliderUsia').value);

            // Update Label slider
            document.getElementById('valTinggi').innerText = tinggi;
            document.getElementById('valBerat').innerText = berat;
            document.getElementById('valUsia').innerText = usia;

            // Profile Header
            document.getElementById('displayNama').innerText = nama;
            document.getElementById('avatarLetter').innerText = nama.charAt(0).toUpperCase() || 'U';
            document.getElementById('displayMeta').innerText = 
                `${currentGender === 'pria' ? 'Pria' : 'Wanita'} • ${usia} Tahun • ${tinggi} cm / ${berat} kg`;

            // Hitung BMI
            const tinggi_m = tinggi / 100;
            const bmi = berat / (tinggi_m * tinggi_m);
            const bmi_bulat = Math.round(bmi * 10) / 10;
            document.getElementById('bmiNumber').innerText = bmi_bulat.toFixed(1);

            // Needle Rotation (-90deg to +90deg)
            const clamped_bmi = Math.max(10, Math.min(40, bmi));
            const needle_deg = -90 + ((clamped_bmi - 10) / 30) * 180;
            document.getElementById('gaugeNeedle').style.transform = `rotate(${needle_deg}deg)`;

            // Evaluasi Kategori
            let kategori = "", sub = "", badgeClass = "", desc = "";
            let makan = "", olahraga = "", risiko = "";

            if (bmi < 18.5) {
                kategori = "Kekurangan Berat Badan";
                sub = "Underweight";
                badgeClass = "bg-sky-500/10 text-sky-400 border-sky-500/30";
                desc = "Berat badan berada di bawah rentang acuan sehat. Disarankan meningkatkan cadangan energi dan nutrisi seimbang.";
                makan = "Surplus kalori sehat (+300 hingga 500 kcal). Utamakan protein berkualitas (telur, dada ayam, tempe), lemak sehat (alpukat, kacang-kacangan), dan karbohidrat kompleks.";
                olahraga = "Fokus pada latihan beban (strength & resistance training) 3-4x seminggu untuk menstimulasi massa otot tanpa pembakaran kalori berlebih dari kardio panjang.";
                risiko = "Potensi anemia, penurunan imunitas tubuh, kerapuhan struktur tulang (osteopenia/osteoporosis), dan defisiensi mikronutrien penting.";
            } else if (bmi < 25.0) {
                kategori = "Berat Badan Ideal";
                sub = "Normal Weight";
                badgeClass = "bg-emerald-500/10 text-emerald-400 border-emerald-500/30";
                desc = "Komposisi tubuh berada dalam rasio optimal. Risiko gangguan metabolik dan penyakit kardiovaskular tergolong paling rendah.";
                const air = (berat * 35 / 1000).toFixed(1);
                makan = `Pertahankan pola gizi seimbang (Piring Sehat: 50% sayur/buah, 25% protein bebas lemak, 25% karbohidrat utuh). Cukupi hidrasi harian minimal ${air} Liter.`;
                olahraga = "Kombinasikan latihan kardio moderat 150 menit/minggu (sepeda santai, jogging, renang) bersama latihan ketahanan fisik 2x seminggu.";
                risiko = "Risiko kesehatan minimal. Pertahankan stabilitas berat badan dengan tidur cukup 7-8 jam dan manajemen stres yang teratur.";
            } else if (bmi < 30.0) {
                kategori = "Kelebihan Berat Badan";
                sub = "Overweight";
                badgeClass = "bg-amber-500/10 text-amber-400 border-amber-500/30";
                desc = "Cadangan lemak tubuh melebihi ambang normal. Intervensi gaya hidup dini sangat efektif mengembalikan ke batas ideal.";
                makan = "Terapkan defisit kalori teratur (-300 s/d 400 kcal). Hindari minuman manis kemasan, kurangi asupan karbo olahan/gorengan, dan perbanyak serat larut air.";
                olahraga = "Targetkan 8.000 - 10.000 langkah harian. Tambahkan kardio HIIT terukur atau circuit training 3-4x seminggu untuk pembakaran kalori maksimal.";
                risiko = "Peningkatan risiko resistensi insulin, hipertensi derajat awal, serta beban berlebih pada sendi penopang berat (lutut dan tumit).";
            } else {
                kategori = "Kategori Obesitas";
                sub = "Obese (High Risk)";
                badgeClass = "bg-rose-500/10 text-rose-400 border-rose-500/30";
                desc = "Akumulasi jaringan adiposa tinggi. Sangat dianjurkan untuk berkonsultasi medis guna menyusun target penurunan berat bertahap.";
                makan = "Terapkan diet rendah indeks glikemik dan batasi kalori harian terukur. Prioritaskan makanan utuh (whole foods) dan catat log nutrisi harian.";
                olahraga = "Utamakan olahraga ramah sendi (low-impact) seperti berenang, jalan santai di permukaan rata, atau sepeda statis sebelum bertransisi ke intensitas tinggi.";
                risiko = "Risiko tinggi penyakit jantung koroner, diabetes melitus tipe-2, dislipidemia, perlemakan hati (fatty liver), dan sleep apnea.";
            }

            // Update Elements
            document.getElementById('badgeText').innerText = sub;
            document.getElementById('badgeContainer').className = `px-3 py-1 rounded-full border text-xs font-bold font-mono tracking-wide uppercase ${badgeClass}`;
            document.getElementById('categoryTitle').innerText = kategori;
            document.getElementById('categoryDescription').innerText = desc;

            // Update Metrics Cards
            const berat_min = (18.5 * tinggi_m * tinggi_m).toFixed(1);
            const berat_max = (24.9 * tinggi_m * tinggi_m).toFixed(1);
            const berat_mid = ((parseFloat(berat_min) + parseFloat(berat_max)) / 2).toFixed(1);
            const selisih = (berat - berat_mid).toFixed(1);

            document.getElementById('statBeratIdeal').innerText = `${berat_min} - ${berat_max}`;
            if (selisih > 0) {
                document.getElementById('statSelisih').innerText = `Kelebihan +${selisih} kg dari median`;
            } else if (selisih < 0) {
                document.getElementById('statSelisih').innerText = `Kurang ${Math.abs(selisih)} kg dari median`;
            } else {
                document.getElementById('statSelisih').innerText = `Ideal sempurna`;
            }

            // Air & BMR
            const air = (berat * 35 / 1000).toFixed(1);
            document.getElementById('statAir').innerText = air;
            document.getElementById('statGelas').innerText = `Setara ± ${Math.round(air * 4)} gelas air`;

            let bmr = 0;
            if (currentGender === 'pria') {
                bmr = Math.round((10 * berat) + (6.25 * tinggi) - (5 * usia) + 5);
            } else {
                bmr = Math.round((10 * berat) + (6.25 * tinggi) - (5 * usia) - 161);
            }
            const tdee = Math.round(bmr * 1.375);
            document.getElementById('statBmr').innerText = bmr.toLocaleString();
            document.getElementById('statTdee').innerText = `TDEE: ±${tdee.toLocaleString()} kcal/hari`;

            // Tabs text
            document.getElementById('textSaranMakan').innerText = makan;
            document.getElementById('textSaranOlahraga').innerText = olahraga;
            document.getElementById('textRisiko').innerText = risiko;
        }

        function copySummary() {
            const nama = document.getElementById('displayNama').innerText;
            const bmi = document.getElementById('bmiNumber').innerText;
            const kat = document.getElementById('categoryTitle').innerText;
            const ideal = document.getElementById('statBeratIdeal').innerText;
            const air = document.getElementById('statAir').innerText;
            const bmr = document.getElementById('statBmr').innerText;

            const text = `📋 LAPORAN BIOMETRIK KESEHATAN (HealthMatrix)\n` +
                         `━━━━━━━━━━━━━━━━━━━━━━━━━━━\n` +
                         `👤 Pasien       : ${nama}\n` +
                         `⚖️ Skor BMI      : ${bmi} (${kat})\n` +
                         `🎯 Berat Ideal   : ${ideal} kg\n` +
                         `💧 Kebutuhan Air : ${air} Liter/hari\n` +
                         `⚡ Energi BMR    : ${bmr} kcal\n` +
                         `━━━━━━━━━━━━━━━━━━━━━━━━━━━\n` +
                         `Generated via HealthMatrix Pro`;

            navigator.clipboard.writeText(text).then(() => {
                const btn = document.getElementById('copyTextBtn');
                btn.innerText = 'Tersalin ke Clipboard! ✓';
                setTimeout(() => { btn.innerText = 'Salin Ringkasan Medis'; }, 2000);
            });
        }

        document.getElementById('inputNama').addEventListener('input', updateMetrics);
    </script>
</body>
</html>