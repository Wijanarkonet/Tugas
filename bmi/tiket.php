<?php
// ====================================================================
// Cinema Ticket Engine - UI Styling with shadcn/ui & Tailwind CSS
// ====================================================================

// Ambil input form atau gunakan default
$namaPemesan = isset($_POST['nama']) && trim($_POST['nama']) !== '' ? htmlspecialchars(trim($_POST['nama'])) : "Danang Wijanarko";
$filmJudul   = isset($_POST['film']) ? htmlspecialchars($_POST['film']) : "Interstellar: Beyond Time";
$hari        = isset($_POST['hari']) ? $_POST['hari'] : "Sabtu";
$kelasStudio = isset($_POST['kelas']) ? $_POST['kelas'] : "DELUXE";
$jumlahTiket = isset($_POST['jumlah']) ? max(1, min(10, (int)$_POST['jumlah'])) : 2;
$jamTayang   = isset($_POST['jam']) ? $_POST['jam'] : "19:30 WIB";

// Normalisasi kelas & penentuan harga dasar
$hariList = [
    'Senin'  => ['tipe' => 'Weekday', 'harga' => 35000],
    'Selasa' => ['tipe' => 'Weekday', 'harga' => 35000],
    'Rabu'   => ['tipe' => 'Weekday', 'harga' => 35000],
    'Kamis'  => ['tipe' => 'Weekday', 'harga' => 35000],
    'Jumat'  => ['tipe' => 'Weekend', 'harga' => 50000],
    'Sabtu'  => ['tipe' => 'Weekend', 'harga' => 50000],
    'Minggu' => ['tipe' => 'Weekend', 'harga' => 50000],
];

$error = "";
if (array_key_exists($hari, $hariList)) {
    $hargaDasar = $hariList[$hari]['harga'];
    $kategoriHari = $hariList[$hari]['tipe'];
} else {
    $error = "Nama hari pemutaran tidak valid.";
    $hargaDasar = 0;
    $kategoriHari = "-";
}

// Penentuan biaya kelas studio (fix case-insensitive)
$kelasKey = strtoupper($kelasStudio);
switch ($kelasKey) {
    case "REGULER":
    case "REGULAR":
        $kelasKey = "REGULER";
        $biayaTambahan = 0;
        $fasilitas = "Standar 2D Audio Dolby Surround 7.1";
        break;
    case "DELUXE":
        $biayaTambahan = 20000;
        $fasilitas = "Semi-Recliner Plush Seat + Dolby Atmos 3D";
        break;
    case "VIP":
        $biayaTambahan = 50000;
        $fasilitas = "Full Electric Leather Recliner + Selimut & Welcome Drink";
        break;
    default:
        $kelasKey = "REGULER";
        $biayaTambahan = 0;
        $fasilitas = "Standar 2D Audio Dolby Surround 7.1";
        break;
}

$hargaTotalPerTiket = $hargaDasar + $biayaTambahan;
$totalBayar         = $hargaTotalPerTiket * $jumlahTiket;

function rp($angka) {
    return "Rp " . number_format($angka, 0, ',', '.');
}
?>
<!DOCTYPE html>
<html lang="id" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CinePass Pro &mdash; Cinema Booking & E-Ticket</title>
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
                        accent: {
                            DEFAULT: "hsl(38 92% 50%)",
                            foreground: "hsl(240 5.9% 10%)",
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
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Space+Grotesk:wght@500;700&display=swap" rel="stylesheet">
    <style>
        /* shadcn Perforated Ticket Notches */
        .ticket-cutout-left {
            position: absolute;
            left: -14px;
            width: 28px;
            height: 28px;
            border-radius: 50%;
            background-color: hsl(240 10% 3.9%);
            box-shadow: inset -2px 0 3px rgba(0,0,0,0.5);
        }
        .ticket-cutout-right {
            position: absolute;
            right: -14px;
            width: 28px;
            height: 28px;
            border-radius: 50%;
            background-color: hsl(240 10% 3.9%);
            box-shadow: inset 2px 0 3px rgba(0,0,0,0.5);
        }
        @media print {
            body { background: white !important; color: black !important; }
            .no-print { display: none !important; }
            .ticket-card { box-shadow: none !important; border: 1px solid #ddd !important; background: white !important; color: black !important; }
            .ticket-cutout-left, .ticket-cutout-right { display: none !important; }
        }
    </style>
</head>
<body class="bg-background text-foreground min-h-screen font-sans antialiased selection:bg-amber-500 selection:text-black">

    <!-- Background Subtle Ambient Mesh -->
    <div class="fixed inset-0 pointer-events-none overflow-hidden z-0">
        <div class="absolute -top-40 left-1/4 w-[500px] h-[500px] bg-amber-500/10 rounded-full blur-[140px]"></div>
        <div class="absolute -bottom-40 right-1/4 w-[500px] h-[500px] bg-indigo-500/10 rounded-full blur-[140px]"></div>
    </div>

    <div class="relative z-10 max-w-6xl mx-auto px-4 py-10 sm:px-6 lg:px-8">
        
        <!-- Header shadcn Style -->
        <header class="text-center max-w-2xl mx-auto mb-12">
            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full border border-border bg-secondary/60 text-xs font-medium text-zinc-400 mb-4 backdrop-blur-sm">
                <span class="w-2 h-2 rounded-full bg-amber-400 animate-pulse"></span>
                <span>Powered by shadcn/ui &bull; Tailwind Utility Engine</span>
            </div>
            <h1 class="text-3xl sm:text-4xl font-extrabold tracking-tight text-white mb-3">
                CinePass Box Office
            </h1>
            <p class="text-muted-foreground text-sm sm:text-base">
                Konfigurasi tiket bioskop, pilih kelas studio eksklusif, dan cetak boarding pass digital instan.
            </p>
        </header>

        <!-- Main Content 2-Column Grid -->
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
            
            <!-- LEFT PANEL: shadcn Form Controls (7 Cols) -->
            <div class="lg:col-span-7 space-y-6">
                <div class="rounded-2xl border border-border bg-card/80 p-6 sm:p-8 backdrop-blur-xl shadow-2xl">
                    <div class="flex items-center gap-3 pb-6 mb-6 border-b border-border">
                        <div class="w-10 h-10 rounded-xl bg-secondary flex items-center justify-center text-amber-400 border border-border">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z" />
                            </svg>
                        </div>
                        <div>
                            <h2 class="text-lg font-semibold text-white">Parameter Pemesanan</h2>
                            <p class="text-xs text-muted-foreground">Pilih konfigurasi studio & hari penayangan</p>
                        </div>
                    </div>

                    <form method="POST" id="ticketForm" class="space-y-6">
                        
                        <!-- Input Nama Pemesan -->
                        <div class="space-y-2">
                            <label class="text-xs font-semibold text-zinc-300 uppercase tracking-wider">Nama Pemesan</label>
                            <input type="text" name="nama" id="inputNama" value="<?= $namaPemesan ?>" 
                                   class="w-full bg-secondary/50 border border-border rounded-xl px-4 py-3 text-sm text-white placeholder-zinc-500 focus:outline-none focus:ring-2 focus:ring-amber-500/50 focus:border-amber-500 transition duration-200">
                        </div>

                        <!-- Film Title Picker -->
                        <div class="space-y-2">
                            <label class="text-xs font-semibold text-zinc-300 uppercase tracking-wider">Judul Film</label>
                            <select name="film" id="selectFilm" onchange="syncTicket()" 
                                    class="w-full bg-secondary/50 border border-border rounded-xl px-4 py-3 text-sm text-white focus:outline-none focus:ring-2 focus:ring-amber-500/50 focus:border-amber-500 transition duration-200 cursor-pointer">
                                <option value="Interstellar: Beyond Time" <?= $filmJudul === 'Interstellar: Beyond Time' ? 'selected' : '' ?>>Interstellar: Beyond Time (Sci-Fi / Adventure)</option>
                                <option value="Oppenheimer: The Atomic Echo" <?= $filmJudul === 'Oppenheimer: The Atomic Echo' ? 'selected' : '' ?>>Oppenheimer: The Atomic Echo (Biography / Drama)</option>
                                <option value="Dune: Part Two" <?= $filmJudul === 'Dune: Part Two' ? 'selected' : '' ?>>Dune: Part Two (Action / Sci-Fi)</option>
                                <option value="Spirited Away: Collector's Cut" <?= $filmJudul === 'Spirited Away: Collector\'s Cut' ? 'selected' : '' ?>>Spirited Away: Collector's Cut (Animation / Fantasy)</option>
                            </select>
                        </div>

                        <!-- Pilih Hari Penayangan (Pills) -->
                        <div class="space-y-2">
                            <label class="text-xs font-semibold text-zinc-300 uppercase tracking-wider flex justify-between">
                                <span>Hari Penayangan</span>
                                <span class="text-amber-400 font-mono text-xs" id="labelTarifHari"><?= $kategoriHari ?> &bull; <?= rp($hargaDasar) ?></span>
                            </label>
                            <div class="grid grid-cols-4 sm:grid-cols-7 gap-2">
                                <?php foreach ($hariList as $h => $d): ?>
                                    <button type="button" onclick="selectHari('<?= $h ?>', <?= $d['harga'] ?>, '<?= $d['tipe'] ?>')"
                                            id="btnHari-<?= $h ?>"
                                            class="hari-btn py-2 px-1 text-xs font-semibold rounded-lg border transition-all duration-200 <?= $hari === $h ? 'bg-amber-500 text-black border-amber-400 font-bold shadow-lg shadow-amber-500/20' : 'bg-secondary/40 text-zinc-300 border-border hover:bg-secondary/80' ?>">
                                        <div><?= substr($h, 0, 3) ?></div>
                                        <div class="text-[10px] opacity-75 font-mono mt-0.5"><?= $d['harga'] / 1000 ?>k</div>
                                    </button>
                                <?php endforeach; ?>
                            </div>
                            <input type="hidden" name="hari" id="inputHari" value="<?= $hari ?>">
                        </div>

                        <!-- Kelas Studio Cards (Interactive Radio) -->
                        <div class="space-y-2">
                            <label class="text-xs font-semibold text-zinc-300 uppercase tracking-wider">Kelas Studio Bioskop</label>
                            <input type="hidden" name="kelas" id="inputKelas" value="<?= $kelasKey ?>">
                            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                                
                                <!-- Regular -->
                                <div onclick="selectKelas('REGULER', 0, 'Standar 2D Audio Dolby Surround 7.1')" 
                                     id="cardKelas-REGULER"
                                     class="kelas-card cursor-pointer p-4 rounded-xl border transition-all duration-200 <?= $kelasKey === 'REGULER' ? 'bg-amber-500/10 border-amber-500 shadow-md ring-1 ring-amber-500' : 'bg-secondary/30 border-border hover:border-zinc-700' ?>">
                                    <div class="flex justify-between items-center mb-1">
                                        <b class="text-sm text-white">Reguler</b>
                                        <span class="text-[11px] font-mono text-zinc-400">+Rp 0</span>
                                    </div>
                                    <p class="text-[11px] text-muted-foreground leading-tight">Dolby 7.1 & Standar Seat</p>
                                </div>

                                <!-- Deluxe -->
                                <div onclick="selectKelas('DELUXE', 20000, 'Semi-Recliner Plush Seat + Dolby Atmos 3D')" 
                                     id="cardKelas-DELUXE"
                                     class="kelas-card cursor-pointer p-4 rounded-xl border transition-all duration-200 <?= $kelasKey === 'DELUXE' ? 'bg-amber-500/10 border-amber-500 shadow-md ring-1 ring-amber-500' : 'bg-secondary/30 border-border hover:border-zinc-700' ?>">
                                    <div class="flex justify-between items-center mb-1">
                                        <b class="text-sm text-white">Deluxe</b>
                                        <span class="text-[11px] font-mono text-amber-400">+20k</span>
                                    </div>
                                    <p class="text-[11px] text-muted-foreground leading-tight">Semi-Recliner & Atmos 3D</p>
                                </div>

                                <!-- VIP -->
                                <div onclick="selectKelas('VIP', 50000, 'Full Electric Leather Recliner + Selimut & Welcome Drink')" 
                                     id="cardKelas-VIP"
                                     class="kelas-card cursor-pointer p-4 rounded-xl border transition-all duration-200 <?= $kelasKey === 'VIP' ? 'bg-amber-500/10 border-amber-500 shadow-md ring-1 ring-amber-500' : 'bg-secondary/30 border-border hover:border-zinc-700' ?>">
                                    <div class="flex justify-between items-center mb-1">
                                        <b class="text-sm text-white">VIP Suite</b>
                                        <span class="text-[11px] font-mono text-amber-400">+50k</span>
                                    </div>
                                    <p class="text-[11px] text-muted-foreground leading-tight">Leather Recliner & F&amp;B</p>
                                </div>

                            </div>
                        </div>

                        <!-- Stepper Jumlah Tiket & Jam Tayang -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div class="space-y-2">
                                <label class="text-xs font-semibold text-zinc-300 uppercase tracking-wider">Jumlah Tiket</label>
                                <div class="flex items-center border border-border rounded-xl bg-secondary/50 p-1">
                                    <button type="button" onclick="adjustTiket(-1)" class="w-10 h-10 rounded-lg bg-card hover:bg-secondary flex items-center justify-center text-white font-bold text-lg active:scale-95 transition">&minus;</button>
                                    <input type="number" name="jumlah" id="inputJumlah" value="<?= $jumlahTiket ?>" min="1" max="10" readonly
                                           class="w-full text-center bg-transparent text-white font-mono font-bold text-lg focus:outline-none">
                                    <button type="button" onclick="adjustTiket(1)" class="w-10 h-10 rounded-lg bg-card hover:bg-secondary flex items-center justify-center text-white font-bold text-lg active:scale-95 transition">&plus;</button>
                                </div>
                            </div>

                            <div class="space-y-2">
                                <label class="text-xs font-semibold text-zinc-300 uppercase tracking-wider">Jadwal Jam Tayang</label>
                                <select name="jam" id="selectJam" onchange="syncTicket()"
                                        class="w-full h-12 bg-secondary/50 border border-border rounded-xl px-4 text-sm text-white focus:outline-none focus:ring-2 focus:ring-amber-500/50 focus:border-amber-500 transition duration-200 cursor-pointer">
                                    <option value="13:00 WIB" <?= $jamTayang === '13:00 WIB' ? 'selected' : '' ?>>13:00 WIB (Siang)</option>
                                    <option value="16:15 WIB" <?= $jamTayang === '16:15 WIB' ? 'selected' : '' ?>>16:15 WIB (Sore)</option>
                                    <option value="19:30 WIB" <?= $jamTayang === '19:30 WIB' ? 'selected' : '' ?>>19:30 WIB (Prime Time)</option>
                                    <option value="22:15 WIB" <?= $jamTayang === '22:15 WIB' ? 'selected' : '' ?>>22:15 WIB (Late Night)</option>
                                </select>
                            </div>
                        </div>

                        <!-- Submit Button shadcn Style -->
                        <div class="pt-2">
                            <button type="submit" class="w-full py-4 rounded-xl bg-gradient-to-r from-amber-500 to-amber-600 hover:from-amber-400 hover:to-amber-500 text-black font-bold text-sm tracking-wide uppercase transition duration-200 shadow-xl shadow-amber-500/20 active:scale-[0.99] flex items-center justify-center gap-2">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                                </svg>
                                <span>Konfirmasi &amp; Bayar Pesanan</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- RIGHT PANEL: Digital Cinema Boarding Pass Ticket (5 Cols) -->
            <div class="lg:col-span-5">
                <div class="ticket-card relative rounded-3xl border border-border bg-gradient-to-b from-card via-zinc-900 to-card shadow-2xl overflow-hidden backdrop-blur-xl">
                    
                    <!-- Top Movie Banner Glow -->
                    <div class="relative bg-gradient-to-br from-amber-600/30 to-indigo-900/40 p-6 pb-8 border-b border-dashed border-border">
                        <div class="flex justify-between items-start mb-4">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-amber-400/20 text-amber-300 border border-amber-400/30 tracking-wide uppercase">
                                Verified Boarding Pass
                            </span>
                            <span class="font-mono text-xs text-zinc-400" id="ticketNo">TIX-<?= strtoupper(substr(md5($namaPemesan . time()), 0, 6)) ?></span>
                        </div>
                        <h3 class="text-xl sm:text-2xl font-black text-white leading-tight mb-1" id="ticketFilm"><?= $filmJudul ?></h3>
                        <p class="text-xs text-zinc-300 flex items-center gap-2">
                            <span id="ticketStudio"><?= $kelasKey ?> CINEMA</span> &bull; 
                            <span id="ticketJam"><?= $jamTayang ?></span>
                        </p>
                    </div>

                    <!-- Cutout Circles (Perforated ticket style) -->
                    <div class="relative flex items-center justify-center h-0">
                        <div class="ticket-cutout-left"></div>
                        <div class="ticket-cutout-right"></div>
                    </div>

                    <!-- Ticket Details Table -->
                    <div class="p-6 sm:p-7 space-y-4">
                        <div class="grid grid-cols-2 gap-4 pb-4 border-b border-border text-xs">
                            <div>
                                <span class="text-muted-foreground block text-[11px]">Nama Tamu</span>
                                <b class="text-white text-sm" id="ticketNama"><?= $namaPemesan ?></b>
                            </div>
                            <div>
                                <span class="text-muted-foreground block text-[11px]">Hari / Sesi</span>
                                <b class="text-white text-sm" id="ticketHari"><?= $hari ?> (<?= $kategoriHari ?>)</b>
                            </div>
                            <div>
                                <span class="text-muted-foreground block text-[11px]">Jumlah Kursi</span>
                                <b class="text-amber-400 font-mono text-sm" id="ticketKursi"><?= $jumlahTiket ?> Tiket</b>
                            </div>
                            <div>
                                <span class="text-muted-foreground block text-[11px]">Nomor Seat</span>
                                <b class="text-white font-mono text-sm" id="ticketSeat">A4, A5</b>
                            </div>
                        </div>

                        <!-- Price Breakdown -->
                        <div class="space-y-2 text-xs">
                            <div class="flex justify-between text-zinc-400">
                                <span>Harga Tiket Dasar</span>
                                <span class="font-mono text-zinc-200" id="ticketHargaDasar"><?= rp($hargaDasar) ?></span>
                            </div>
                            <div class="flex justify-between text-zinc-400">
                                <span>Add-on Studio (<span id="ticketKelasTag"><?= $kelasKey ?></span>)</span>
                                <span class="font-mono text-zinc-200" id="ticketBiayaTambahan"><?= rp($biayaTambahan) ?></span>
                            </div>
                            <div class="flex justify-between text-zinc-400 pb-2 border-b border-border">
                                <span>Subtotal per Tiket</span>
                                <span class="font-mono text-zinc-200 font-semibold" id="ticketHargaTotalPerTiket"><?= rp($hargaTotalPerTiket) ?></span>
                            </div>

                            <!-- Big Total -->
                            <div class="flex justify-between items-baseline pt-2">
                                <div>
                                    <span class="text-xs text-muted-foreground uppercase font-bold block">Total Bayar</span>
                                    <span class="text-[11px] text-zinc-400" id="ticketCalcFormula"><?= rp($hargaTotalPerTiket) ?> &times; <?= $jumlahTiket ?> tiket</span>
                                </div>
                                <span class="text-2xl font-extrabold font-mono text-amber-400" id="ticketTotalBayar"><?= rp($totalBayar) ?></span>
                            </div>
                        </div>

                        <!-- Barcode Simulation -->
                        <div class="pt-6 border-t border-dashed border-border text-center">
                            <div class="flex justify-center items-center gap-1 h-12 py-1 opacity-80">
                                <span class="w-1 h-full bg-white"></span>
                                <span class="w-2 h-full bg-white"></span>
                                <span class="w-0.5 h-full bg-white"></span>
                                <span class="w-1.5 h-full bg-white"></span>
                                <span class="w-3 h-full bg-white"></span>
                                <span class="w-1 h-full bg-white"></span>
                                <span class="w-2 h-full bg-white"></span>
                                <span class="w-0.5 h-full bg-white"></span>
                                <span class="w-1.5 h-full bg-white"></span>
                                <span class="w-2.5 h-full bg-white"></span>
                                <span class="w-1 h-full bg-white"></span>
                                <span class="w-2 h-full bg-white"></span>
                                <span class="w-0.5 h-full bg-white"></span>
                                <span class="w-3 h-full bg-white"></span>
                                <span class="w-1 h-full bg-white"></span>
                                <span class="w-1.5 h-full bg-white"></span>
                                <span class="w-2 h-full bg-white"></span>
                            </div>
                            <p class="font-mono text-[10px] text-zinc-500 tracking-widest mt-1">SCAN BOARDING PASS AT STUDIO GATE</p>
                        </div>

                        <!-- Print / Export Action -->
                        <div class="pt-2 no-print">
                            <button type="button" onclick="window.print()" 
                                    class="w-full py-2.5 rounded-lg border border-border bg-secondary/50 hover:bg-secondary text-zinc-300 hover:text-white text-xs font-semibold flex items-center justify-center gap-2 transition">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                                </svg>
                                <span>Cetak Tiket Bioskop</span>
                            </button>
                        </div>
                    </div>

                </div>
            </div>

        </div>
    </div>

    <!-- Client-Side Instant Reactivity -->
    <script>
        let currentHariHarga = <?= $hargaDasar ?>;
        let currentHariTipe  = '<?= $kategoriHari ?>';
        let currentKelasBiaya = <?= $biayaTambahan ?>;

        function formatRupiah(num) {
            return 'Rp ' + num.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
        }

        function selectHari(hari, harga, tipe) {
            document.getElementById('inputHari').value = hari;
            currentHariHarga = harga;
            currentHariTipe = tipe;

            document.querySelectorAll('.hari-btn').forEach(btn => {
                btn.className = 'hari-btn py-2 px-1 text-xs font-semibold rounded-lg border transition-all duration-200 bg-secondary/40 text-zinc-300 border-border hover:bg-secondary/80';
            });

            const activeBtn = document.getElementById('btnHari-' + hari);
            if (activeBtn) {
                activeBtn.className = 'hari-btn py-2 px-1 text-xs font-semibold rounded-lg border transition-all duration-200 bg-amber-500 text-black border-amber-400 font-bold shadow-lg shadow-amber-500/20';
            }

            document.getElementById('labelTarifHari').innerText = `${tipe} • ${formatRupiah(harga)}`;
            syncTicket();
        }

        function selectKelas(kelas, biaya, fasilitas) {
            document.getElementById('inputKelas').value = kelas;
            currentKelasBiaya = biaya;

            document.querySelectorAll('.kelas-card').forEach(card => {
                card.className = 'kelas-card cursor-pointer p-4 rounded-xl border transition-all duration-200 bg-secondary/30 border-border hover:border-zinc-700';
            });

            const activeCard = document.getElementById('cardKelas-' + kelas);
            if (activeCard) {
                activeCard.className = 'kelas-card cursor-pointer p-4 rounded-xl border transition-all duration-200 bg-amber-500/10 border-amber-500 shadow-md ring-1 ring-amber-500';
            }

            syncTicket();
        }

        function adjustTiket(delta) {
            const input = document.getElementById('inputJumlah');
            let val = parseInt(input.value) + delta;
            if (val < 1) val = 1;
            if (val > 10) val = 10;
            input.value = val;
            syncTicket();
        }

        function syncTicket() {
            const nama = document.getElementById('inputNama').value || 'Tamu Bioskop';
            const film = document.getElementById('selectFilm').value;
            const hari = document.getElementById('inputHari').value;
            const kelas = document.getElementById('inputKelas').value;
            const jumlah = parseInt(document.getElementById('inputJumlah').value);
            const jam = document.getElementById('selectJam').value;

            // Generate Seats
            const seats = [];
            for (let i = 1; i <= jumlah; i++) {
                seats.push('A' + (3 + i));
            }

            const totalPerTiket = currentHariHarga + currentKelasBiaya;
            const grandTotal = totalPerTiket * jumlah;

            // Update DOM Ticket Elements
            document.getElementById('ticketNama').innerText = nama;
            document.getElementById('ticketFilm').innerText = film;
            document.getElementById('ticketHari').innerText = `${hari} (${currentHariTipe})`;
            document.getElementById('ticketStudio').innerText = `${kelas} CINEMA`;
            document.getElementById('ticketJam').innerText = jam;
            document.getElementById('ticketKursi').innerText = `${jumlah} Tiket`;
            document.getElementById('ticketSeat').innerText = seats.join(', ');

            document.getElementById('ticketKelasTag').innerText = kelas;
            document.getElementById('ticketHargaDasar').innerText = formatRupiah(currentHariHarga);
            document.getElementById('ticketBiayaTambahan').innerText = formatRupiah(currentKelasBiaya);
            document.getElementById('ticketHargaTotalPerTiket').innerText = formatRupiah(totalPerTiket);
            document.getElementById('ticketCalcFormula').innerText = `${formatRupiah(totalPerTiket)} × ${jumlah} tiket`;
            document.getElementById('ticketTotalBayar').innerText = formatRupiah(grandTotal);
        }

        document.getElementById('inputNama').addEventListener('input', syncTicket);
    </script>
</body>
</html>