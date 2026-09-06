<?php

require_once 'config.php';

$namaPenulis = isset($_GET['nama']) ? trim($_GET['nama']) : '';

if (empty($namaPenulis)) {
    header("Location: index.php");
    exit;
}

$stmtUser = mysqli_prepare(
    $conn,
    "SELECT id, nama, username, role, status, created_at
     FROM users
     WHERE nama = ?
     AND status = 'aktif'
     LIMIT 1"
);

mysqli_stmt_bind_param(
    $stmtUser,
    "s",
    $namaPenulis
);

mysqli_stmt_execute($stmtUser);

$resultUser = mysqli_stmt_get_result($stmtUser);
$penulis = mysqli_fetch_assoc($resultUser);

mysqli_stmt_close($stmtUser);

if (!$penulis) {
    header("Location: index.php");
    exit;
}

$stmtStatistik = mysqli_prepare(
    $conn,
    "SELECT
        COUNT(*) AS total_artikel,
        COUNT(DISTINCT kategori) AS total_kategori,
        COALESCE(SUM(read_time), 0) AS total_waktu_baca,
        COALESCE(AVG(read_time), 0) AS rata_waktu_baca
     FROM berita
     WHERE penulis = ?"
);

mysqli_stmt_bind_param(
    $stmtStatistik,
    "s",
    $namaPenulis
);

mysqli_stmt_execute($stmtStatistik);

$resultStatistik = mysqli_stmt_get_result($stmtStatistik);
$statistik = mysqli_fetch_assoc($resultStatistik);

mysqli_stmt_close($stmtStatistik);

$stmtArtikel = mysqli_prepare(
    $conn,
    "SELECT *
     FROM berita
     WHERE penulis = ?
     ORDER BY id DESC"
);

mysqli_stmt_bind_param(
    $stmtArtikel,
    "s",
    $namaPenulis
);

mysqli_stmt_execute($stmtArtikel);

$resultArtikel = mysqli_stmt_get_result($stmtArtikel);
$artikelPenulis = mysqli_fetch_all($resultArtikel, MYSQLI_ASSOC);

mysqli_stmt_close($stmtArtikel);

$totalArtikel = (int)$statistik['total_artikel'];
$totalKategori = (int)$statistik['total_kategori'];
$rataWaktuBaca = $totalArtikel > 0
    ? round((float)$statistik['rata_waktu_baca'], 1)
    : 0;

$totalKata = 0;

foreach ($artikelPenulis as $artikel) {
    preg_match_all('/\S+/', $artikel['konten'], $matches);
    $totalKata += count($matches[0]);
}

?>

<!DOCTYPE html>
<html lang="id">

<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title><?= htmlspecialchars($penulis['nama']); ?> - Ngabar Yuk!</title>

    <script src="https://cdn.tailwindcss.com"></script>

    <link
        rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        html {
            scroll-behavior: smooth;
        }

        body {
            background-image:
                linear-gradient(rgba(84, 47, 27, 0.025) 1px, transparent 1px),
                linear-gradient(90deg, rgba(84, 47, 27, 0.025) 1px, transparent 1px);
            background-size: 28px 28px;
        }

        .javanese-pattern {
            background-color: #542f1b;
            background-image:
                linear-gradient(135deg, rgba(255, 255, 255, .035) 25%, transparent 25%),
                linear-gradient(225deg, rgba(255, 255, 255, .035) 25%, transparent 25%),
                linear-gradient(45deg, rgba(255, 255, 255, .035) 25%, transparent 25%),
                linear-gradient(315deg, rgba(255, 255, 255, .035) 25%, #542f1b 25%);
            background-position: 12px 0, 12px 0, 0 0, 0 0;
            background-size: 24px 24px;
        }

        .fade-up {
            animation: fadeUp .45s ease both;
        }

        @keyframes fadeUp {
            from {
                opacity: 0;
                transform: translateY(8px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
    </style>

</head>

<body class="bg-stone-50 text-stone-800 font-sans antialiased flex flex-col min-h-screen selection:bg-amber-200 selection:text-amber-900">

    <header class="sticky top-0 z-50">

        <div class="bg-[#542f1b] text-stone-100 shadow-lg shadow-stone-900/10 javanese-pattern">

            <div class="max-w-5xl mx-auto px-5">

                <div class="h-[72px] flex items-center justify-between">

                    <a href="index.php" class="group flex items-center gap-3">

                        <div class="relative w-10 h-10 rounded-xl bg-amber-400 text-[#542f1b] flex items-center justify-center shadow-sm group-hover:-rotate-3 transition-transform duration-300">

                            <i class="fa-solid fa-mug-hot text-lg"></i>

                            <span class="absolute -right-1 -bottom-1 w-3 h-3 bg-[#542f1b] border-2 border-amber-400 rounded-full"></span>

                        </div>

                        <div class="leading-none">

                            <div class="text-[20px] font-black tracking-tight">
                                Ngabar
                                <span class="text-amber-400">Yuk!</span>
                            </div>

                            <div class="text-[10px] uppercase tracking-[0.18em] text-stone-300 mt-1">
                                Warta • Reriungan • Insight
                            </div>

                        </div>

                    </a>

                    <nav class="flex items-center gap-1.5 sm:gap-2">

                        <a href="index.php"
                            class="inline-flex items-center gap-2 text-stone-200 hover:text-amber-300 font-semibold text-sm px-3 py-2.5 rounded-xl hover:bg-white/5 transition">

                            <i class="fa-solid fa-house text-xs"></i>

                            <span class="hidden sm:inline">
                                Beranda
                            </span>

                        </a>

                        <a href="about.php"
                            class="hidden sm:inline-flex items-center gap-2 text-stone-200 hover:text-amber-300 font-semibold text-sm px-3 py-2.5 rounded-xl hover:bg-white/5 transition">

                            <i class="fa-solid fa-circle-info text-xs"></i>

                            Tentang

                        </a>

                        <a href="create.php"
                            class="inline-flex items-center gap-2 bg-amber-400 hover:bg-amber-300 text-[#542f1b] font-bold text-sm px-3.5 py-2.5 rounded-xl transition shadow-sm">

                            <i class="fa-solid fa-plus text-xs"></i>

                            <span class="hidden sm:inline">
                                Tulis Kabar
                            </span>

                        </a>

                    </nav>

                </div>

            </div>

        </div>

    </header>

    <main class="flex-grow">

        <div class="max-w-5xl mx-auto px-4 sm:px-6 py-8 md:py-12">

            <div class="mb-6">

                <a href="index.php"
                    class="inline-flex items-center gap-2 text-sm font-semibold text-stone-500 hover:text-amber-900 transition">

                    <i class="fa-solid fa-arrow-left text-xs"></i>

                    Kembali ke Beranda

                </a>

            </div>

            <section class="fade-up bg-white border border-stone-200 rounded-3xl shadow-sm overflow-hidden">

                <div class="javanese-pattern px-6 py-8 md:px-10 md:py-10">

                    <div class="flex flex-col sm:flex-row sm:items-center gap-5">

                        <div class="w-20 h-20 rounded-3xl bg-amber-400 text-[#542f1b] flex items-center justify-center shadow-lg shrink-0">

                            <i class="fa-solid fa-user text-3xl"></i>

                        </div>

                        <div>

                            <p class="text-[10px] uppercase tracking-[0.18em] font-bold text-amber-300">
                                Profil Penulis
                            </p>

                            <h1 class="text-2xl sm:text-3xl md:text-4xl font-black text-white tracking-tight mt-1">
                                <?= htmlspecialchars($penulis['nama']); ?>
                            </h1>

                            <div class="flex flex-wrap items-center gap-2 mt-3">

                                <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full bg-white/10 border border-white/10 text-stone-200 text-xs font-semibold">

                                    <i class="fa-solid fa-user-pen text-amber-300 text-[10px]"></i>

                                    <?= $penulis['role'] === 'super_admin' ? 'Super Admin' : 'Admin'; ?>

                                </span>

                                <span class="inline-flex items-center gap-1.5 text-xs text-stone-300">

                                    <i class="fa-regular fa-calendar text-amber-300"></i>

                                    Bergabung <?= date('d M Y', strtotime($penulis['created_at'])); ?>

                                </span>

                            </div>

                        </div>

                    </div>

                </div>

                <div class="p-5 sm:p-6 md:p-8">

                    <div class="grid grid-cols-2 md:grid-cols-4 gap-3">

                        <div class="rounded-2xl bg-stone-50 border border-stone-100 p-4">

                            <div class="w-8 h-8 rounded-lg bg-amber-100 text-amber-800 flex items-center justify-center mb-3">

                                <i class="fa-regular fa-newspaper text-xs"></i>

                            </div>

                            <p class="text-2xl font-black text-[#542f1b]">
                                <?= $totalArtikel; ?>
                            </p>

                            <p class="text-[11px] text-stone-400 mt-1">
                                Total Artikel
                            </p>

                        </div>

                        <div class="rounded-2xl bg-stone-50 border border-stone-100 p-4">

                            <div class="w-8 h-8 rounded-lg bg-amber-100 text-amber-800 flex items-center justify-center mb-3">

                                <i class="fa-solid fa-tags text-xs"></i>

                            </div>

                            <p class="text-2xl font-black text-[#542f1b]">
                                <?= $totalKategori; ?>
                            </p>

                            <p class="text-[11px] text-stone-400 mt-1">
                                Kategori
                            </p>

                        </div>

                        <div class="rounded-2xl bg-stone-50 border border-stone-100 p-4">

                            <div class="w-8 h-8 rounded-lg bg-amber-100 text-amber-800 flex items-center justify-center mb-3">

                                <i class="fa-solid fa-font text-xs"></i>

                            </div>

                            <p class="text-2xl font-black text-[#542f1b]">
                                <?= number_format($totalKata, 0, ',', '.'); ?>
                            </p>

                            <p class="text-[11px] text-stone-400 mt-1">
                                Kata Ditulis
                            </p>

                        </div>

                        <div class="rounded-2xl bg-stone-50 border border-stone-100 p-4">

                            <div class="w-8 h-8 rounded-lg bg-amber-100 text-amber-800 flex items-center justify-center mb-3">

                                <i class="fa-regular fa-clock text-xs"></i>

                            </div>

                            <p class="text-2xl font-black text-[#542f1b]">
                                <?= $rataWaktuBaca; ?>
                            </p>

                            <p class="text-[11px] text-stone-400 mt-1">
                                Menit Rata-rata
                            </p>

                        </div>

                    </div>

                </div>

            </section>

            <section class="mt-8">

                <div class="mb-5">

                    <p class="text-[11px] uppercase tracking-[0.18em] text-amber-800 font-bold">
                        Arsip Penulis
                    </p>

                    <h2 class="text-2xl font-black text-stone-900 mt-1">
                        Kabar dari <?= htmlspecialchars($penulis['nama']); ?>
                    </h2>

                    <p class="text-sm text-stone-500 mt-2">
                        <?= $totalArtikel; ?> kabar telah ditulis di Ngabar Yuk!
                    </p>

                </div>

                <?php if (!empty($artikelPenulis)): ?>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

                        <?php foreach ($artikelPenulis as $artikel): ?>

                            <a
                                href="detail.php?id=<?= $artikel['id']; ?>"
                                class="group bg-white border border-stone-200 rounded-2xl overflow-hidden shadow-sm hover:shadow-md hover:-translate-y-0.5 transition-all duration-300">

                                <?php if (!empty($artikel['gambar']) && file_exists('assets/img/' . $artikel['gambar'])): ?>

                                    <div class="relative aspect-[16/9] bg-stone-100 overflow-hidden">

                                        <img
                                            src="assets/img/<?= htmlspecialchars($artikel['gambar']); ?>"
                                            alt="<?= htmlspecialchars($artikel['judul']); ?>"
                                            class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">

                                        <div class="absolute inset-0 bg-gradient-to-t from-black/20 to-transparent"></div>

                                    </div>

                                <?php else: ?>

                                    <div class="javanese-pattern aspect-[16/9] flex items-center justify-center">

                                        <div class="text-center">

                                            <div class="w-12 h-12 mx-auto rounded-xl bg-white/10 border border-white/10 flex items-center justify-center">

                                                <i class="fa-regular fa-image text-xl text-amber-300"></i>

                                            </div>

                                            <p class="text-[10px] uppercase tracking-wider font-bold text-stone-300 mt-2">
                                                Ngabar Yuk!
                                            </p>

                                        </div>

                                    </div>

                                <?php endif; ?>

                                <div class="p-5">

                                    <div class="flex items-center justify-between gap-3 mb-3">

                                        <span class="inline-flex items-center px-2.5 py-1 rounded-lg bg-amber-50 border border-amber-100 text-amber-900 text-[10px] font-bold uppercase tracking-wider">

                                            <?= htmlspecialchars($artikel['kategori']); ?>

                                        </span>

                                        <span class="inline-flex items-center gap-1 text-[11px] text-stone-400 shrink-0">

                                            <i class="fa-regular fa-clock"></i>

                                            <?= (int)$artikel['read_time']; ?> menit

                                        </span>

                                    </div>

                                    <h3 class="text-lg font-black leading-snug text-stone-900 group-hover:text-amber-900 transition">

                                        <?= htmlspecialchars($artikel['judul']); ?>

                                    </h3>

                                    <div class="flex items-center gap-2 mt-4 text-[11px] text-stone-400">

                                        <i class="fa-regular fa-calendar"></i>

                                        <?= date('d M Y', strtotime($artikel['tanggal'])); ?>

                                    </div>

                                </div>

                            </a>

                        <?php endforeach; ?>

                    </div>

                <?php else: ?>

                    <div class="bg-white border border-stone-200 rounded-3xl p-8 text-center shadow-sm">

                        <div class="w-14 h-14 mx-auto rounded-2xl bg-stone-100 text-stone-400 flex items-center justify-center">

                            <i class="fa-regular fa-newspaper text-xl"></i>

                        </div>

                        <h3 class="text-lg font-black text-stone-800 mt-4">
                            Belum ada kabar
                        </h3>

                        <p class="text-sm text-stone-500 mt-1">
                            Penulis ini belum menerbitkan kabar apa pun.
                        </p>

                    </div>

                <?php endif; ?>

            </section>

            <div class="mt-8 javanese-pattern rounded-3xl p-6 md:p-7 text-white">

                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-5">

                    <div class="flex items-start gap-4">

                        <div class="w-11 h-11 rounded-2xl bg-white/10 border border-white/10 flex items-center justify-center shrink-0">

                            <i class="fa-solid fa-mug-hot text-amber-300"></i>

                        </div>

                        <div>

                            <p class="text-sm font-black">
                                Mau baca kabar lainnya?
                            </p>

                            <p class="text-xs text-stone-300 mt-1 leading-relaxed">
                                Temukan berbagai kabar, gagasan, dan cerita lainnya di Ngabar Yuk!
                            </p>

                        </div>

                    </div>

                    <a
                        href="index.php"
                        class="inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl bg-amber-400 text-amber-950 text-sm font-bold hover:bg-amber-300 transition shrink-0">

                        Kembali ke Beranda

                        <i class="fa-solid fa-arrow-right text-xs"></i>

                    </a>

                </div>

            </div>

        </div>

    </main>

    <footer class="bg-[#3a2113] text-stone-300 mt-12">

        <div class="max-w-5xl mx-auto px-5">

            <div class="py-8 flex flex-col md:flex-row md:items-center md:justify-between gap-7">

                <div>

                    <a href="index.php" class="inline-flex items-center gap-2.5 text-white group">

                        <span class="w-8 h-8 rounded-lg bg-amber-400 text-[#542f1b] flex items-center justify-center group-hover:-rotate-3 transition-transform">

                            <i class="fa-solid fa-mug-hot text-sm"></i>

                        </span>

                        <span class="font-black text-lg tracking-tight">

                            Ngabar
                            <span class="text-amber-400">Yuk!</span>

                        </span>

                    </a>

                    <p class="text-xs text-stone-400 mt-2 max-w-sm leading-relaxed">
                        Wadah sederhana untuk berbagi kabar, gagasan, dan cerita yang layak dibicarakan.
                    </p>

                </div>

                <div class="flex flex-col sm:flex-row sm:items-center gap-5">

                    <div class="flex flex-wrap items-center gap-x-5 gap-y-2 text-xs">

                        <a href="index.php" class="hover:text-amber-300 transition">
                            Beranda
                        </a>

                        <a href="about.php" class="hover:text-amber-300 transition">
                            Tentang
                        </a>

                        <a href="create.php" class="hover:text-amber-300 transition">
                            Tulis Kabar
                        </a>

                    </div>

                    <a
                        href="https://ent.pens.ac.id/#home"
                        target="_blank"
                        rel="noopener noreferrer"
                        class="inline-flex items-center justify-center gap-2 px-3.5 py-2 rounded-xl bg-white/5 border border-white/10 text-stone-200 text-xs font-semibold hover:bg-white/10 hover:text-amber-300 transition">

                        <i class="fa-solid fa-arrow-up-right-from-square text-[10px]"></i>

                        Kunjungi ENT Crews

                    </a>

                </div>

            </div>

            <div class="border-t border-white/10 py-4 flex flex-col sm:flex-row justify-between items-center gap-2 text-center sm:text-left">

                <p class="text-[11px] text-stone-500">

                    © 2026 Ngabar Yuk! • Dibuat oleh

                    <span class="text-stone-300 font-semibold">
                        Aqeela Fazle Mawla Ramadhan
                    </span>

                </p>

                <p class="text-[11px] text-amber-500 font-medium">
                    Tugas Seleksi Divisi Webmaster
                </p>

            </div>

        </div>

    </footer>

</body>

</html>