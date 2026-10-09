<?php
$api_url = "https://script.google.com/macros/s/AKfycbzu7Tgc08vcgnfeyIkm22agok33ufbntZWr7UTrZcsCo3-Nmp8l7mM-qermliaRUq3s/exec";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $params = [
        'action' => $_POST['action'],
        'row'    => $_POST['row'] ?? '', 
        'nama'   => $_POST['nama'] ?? '',
        'kelas'  => $_POST['kelas'] ?? '',
        'wa'     => $_POST['wa'] ?? '',
        'email'  => $_POST['email'] ?? ''
    ];

    $request_url = $api_url . "?" . http_build_query($params);
    
    $ch = curl_init($request_url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_exec($ch);
    curl_close($ch);

    header("Location: index.php");
    exit;
}

$read_url = $api_url . "?t=" . time();

$ch = curl_init($read_url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
$response = curl_exec($ch);
curl_close($ch);

$mahasiswa = [];
if ($response) {
    $decoded = json_decode($response, true);
    if (isset($decoded['data'])) {
        $mahasiswa = $decoded['data'];
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manajemen Mahasiswa</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style> 
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&display=swap');
        body { font-family: 'Inter', sans-serif; background-color: #fafafa; color: #18181b; } 
    </style>
</head>
<body class="p-6 md:p-12">

<div class="max-w-6xl mx-auto grid grid-cols-1 lg:grid-cols-3 gap-8">
    
    <div class="lg:col-span-1 space-y-8">
        <div>
            <h1 class="text-2xl font-semibold tracking-tight text-zinc-900">Mahasiswa</h1>
            <p class="text-sm text-zinc-500 mt-1">Kelola data mahasiswa Anda.</p>
        </div>

        <div class="bg-white p-6 border border-zinc-200 shadow-sm rounded-md">
            <h2 class="text-base font-medium text-zinc-900 mb-5" id="form-title">Tambah Data</h2>
            <form action="index.php" method="POST" class="space-y-4">
                <input type="hidden" name="action" id="input-action" value="insert">
                <input type="hidden" name="row" id="input-row">
                
                <div>
                    <label class="block text-sm font-medium text-zinc-700 mb-1">Nama Lengkap</label>
                    <input type="text" name="nama" id="input-nama" required class="w-full border border-zinc-300 rounded px-3 py-2 text-sm focus:ring-1 focus:ring-zinc-900 focus:border-zinc-900 focus:outline-none">
                </div>
                <div>
                    <label class="block text-sm font-medium text-zinc-700 mb-1">Kelas</label>
                    <input type="text" name="kelas" id="input-kelas" required class="w-full border border-zinc-300 rounded px-3 py-2 text-sm focus:ring-1 focus:ring-zinc-900 focus:border-zinc-900 focus:outline-none">
                </div>
                <div>
                    <label class="block text-sm font-medium text-zinc-700 mb-1">No. WhatsApp</label>
                    <input type="text" name="wa" id="input-wa" required class="w-full border border-zinc-300 rounded px-3 py-2 text-sm focus:ring-1 focus:ring-zinc-900 focus:border-zinc-900 focus:outline-none">
                </div>
                <div>
                    <label class="block text-sm font-medium text-zinc-700 mb-1">Email</label>
                    <input type="email" name="email" id="input-email" required class="w-full border border-zinc-300 rounded px-3 py-2 text-sm focus:ring-1 focus:ring-zinc-900 focus:border-zinc-900 focus:outline-none">
                </div>
                <div class="pt-2 flex gap-3">
                    <button type="submit" class="bg-zinc-900 hover:bg-zinc-800 text-white text-sm px-4 py-2 rounded font-medium transition-colors w-full">Simpan</button>
                    <button type="button" onclick="resetForm()" class="bg-white border border-zinc-300 hover:bg-zinc-50 text-zinc-700 text-sm px-4 py-2 rounded font-medium transition-colors w-full">Batal</button>
                </div>
            </form>
        </div>
    </div>

    <div class="lg:col-span-2">
        <div class="bg-white border border-zinc-200 shadow-sm rounded-md overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead>
                        <tr class="bg-zinc-50 border-b border-zinc-200 text-zinc-600">
                            <th class="py-3 px-4 font-medium">Nama</th>
                            <th class="py-3 px-4 font-medium">Kelas</th>
                            <th class="py-3 px-4 font-medium">WhatsApp</th>
                            <th class="py-3 px-4 font-medium">Email</th>
                            <th class="py-3 px-4 font-medium text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="text-zinc-700 divide-y divide-zinc-100">
                        <?php if (empty($mahasiswa)): ?>
                        <tr><td colspan="5" class="py-8 text-center text-zinc-400">Belum ada data</td></tr>
                        <?php else: ?>
                            <?php foreach ($mahasiswa as $mhs): ?>
                            <tr class="hover:bg-zinc-50 transition-colors">
                                <td class="py-3 px-4 font-medium text-zinc-900"><?= htmlspecialchars($mhs['nama'] ?? '') ?></td>
                                <td class="py-3 px-4"><?= htmlspecialchars($mhs['kelas'] ?? '') ?></td>
                                <td class="py-3 px-4"><?= htmlspecialchars($mhs['wa'] ?? '') ?></td>
                                <td class="py-3 px-4"><?= htmlspecialchars($mhs['email'] ?? '') ?></td>
                                <td class="py-3 px-4 text-right space-x-3 whitespace-nowrap">
                                    <button onclick="editData('<?= $mhs['row'] ?>', '<?= addslashes($mhs['nama']) ?>', '<?= addslashes($mhs['kelas']) ?>', '<?= addslashes($mhs['wa']) ?>', '<?= addslashes($mhs['email']) ?>')" class="text-zinc-500 hover:text-zinc-900 font-medium">Edit</button>
                                    
                                    <form action="index.php" method="POST" class="inline-block" onsubmit="return confirm('Hapus data ini?');">
                                        <input type="hidden" name="action" value="delete">
                                        <input type="hidden" name="row" value="<?= $mhs['row'] ?>">
                                        <button type="submit" class="text-red-500 hover:text-red-700 font-medium">Hapus</button>
                                    </form>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>

<script>
    function editData(row, nama, kelas, wa, email) {
        document.getElementById('input-action').value = 'update';
        document.getElementById('input-row').value = row;
        document.getElementById('input-nama').value = nama;
        document.getElementById('input-kelas').value = kelas;
        document.getElementById('input-wa').value = wa;
        document.getElementById('input-email').value = email;
        document.getElementById('form-title').innerText = 'Edit Data';
        window.scrollTo({ top: 0, behavior: 'smooth' });
    }

    function resetForm() {
        document.getElementById('input-action').value = 'insert';
        document.getElementById('input-row').value = '';
        document.getElementById('input-nama').value = '';
        document.getElementById('input-kelas').value = '';
        document.getElementById('input-wa').value = '';
        document.getElementById('input-email').value = '';
        document.getElementById('form-title').innerText = 'Tambah Data';
    }
</script>

</body>
</html>