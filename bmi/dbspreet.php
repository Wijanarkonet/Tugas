<?php
$id="AKfycbyp2FAsxiwTECoqQJBT7b5-iwsDe9tZ8Oa0SIvfIWAOpfuAmE4r-Vld27fPM3Mas5Cq";
$url="https://docs.google.com/spreadsheets/d/e/2PACX-1vTYAdxJPdJicSwf2jJ9GP9N5zoZpgYCcHhqibVKYXeNr_4Ghcpl0KCZAV0W-qFLJmBQ29NjM4ZB6ijg/pub?gid=0&single=true&output=csv";
$data=file_get_contents($url);

$row=array_map("str_getcsv", explode("\n", $data));
?>

<html>
<head>
    <title>Tampil Data 07Tplp032</title>
</head>
<body>
    <h1>Data Kelas</h1>
    <table border="1" cellspacing="0" width="600">
        <tr>
            <th>Nim</th>
            <th>Nama</th>
            <th>Poin ke 1</th>
            <th>Aksi</th>
        </tr>

        <?php
        for($i=1;$i<count($row);$i++)
        {
            if(empty($row[$i][0]))
            {
                continue;
            } 
?>

    <tr>
        <td><?= htmlspecialchars($row[$i][0]) ?></td>
        <td><?= htmlspecialchars($row[$i][1]) ?></td>
        <td><?= htmlspecialchars($row[$i][2]) ?></td>
        <td> Hapus | Ubah</td>
    </tr>

    <?php
    }
    ?>

</table>
</body>
</html>

