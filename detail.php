<?php

require_once 'config.php';

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($id <= 0) {
    header("Location: index.php");
    exit;
}

$stmt = mysqli_prepare($conn, "SELECT * FROM berita WHERE id = ?");
mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$berita = mysqli_fetch_assoc($result);
mysqli_stmt_close($stmt);

if (!$berita) {
    header("Location: index.php");
    exit;
}

$stmtTerkait = mysqli_prepare(
    $conn,
    "SELECT * FROM berita
     WHERE kategori = ?
     AND id != ?
     ORDER BY id DESC
     LIMIT 3"
);

mysqli_stmt_bind_param(
    $stmtTerkait,
    "si",
    $berita['kategori'],
    $berita['id']
);

mysqli_stmt_execute($stmtTerkait);
$resultTerkait = mysqli_stmt_get_result($stmtTerkait);
$artikelTerkait = mysqli_fetch_all($resultTerkait, MYSQLI_ASSOC);
mysqli_stmt_close($stmtTerkait);

?>

<!DOCTYPE html>
<html lang="id">

<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title><?= htmlspecialchars($berita['judul']); ?> - Ngabar Yuk!</title>

    <script src="https://cdn.tailwindcss.com"></script>

    <link
        rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
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
    </style>

</head>

<body class="bg-stone-50 text-stone-800 font-sans antialiased flex flex-col min-h-screen selection:bg-amber-200 selection:text-amber-900">

    <?php
    include 'assets/header.php';
    ?>

    <div
        id="readingProgress"
        class="hidden lg:block fixed right-5 top-1/2 -translate-y-1/2 z-40 w-36 bg-white border border-stone-200 rounded-2xl shadow-lg shadow-stone-900/10 p-4 transition-all duration-300">

        <div class="flex items-center gap-2 mb-3">

            <div class="w-7 h-7 rounded-lg bg-amber-50 text-amber-800 flex items-center justify-center shrink-0">

                <i class="fa-solid fa-book-open text-xs"></i>

            </div>

            <p class="text-[10px] uppercase tracking-[0.16em] font-black text-stone-500">
                Progres Baca
            </p>

        </div>

        <div class="flex items-end gap-1 mb-2">

            <span
                id="progressPercent"
                class="text-2xl font-black tracking-tight text-stone-900">
                0%
            </span>

        </div>

        <div class="w-full h-2 rounded-full bg-stone-100 overflow-hidden">

            <div
                id="progressBar"
                class="h-full w-0 rounded-full bg-amber-400 transition-[width] duration-150">
            </div>

        </div>

        <p
            id="progressText"
            class="text-[10px] leading-relaxed text-stone-400 font-medium mt-2">
            Baru mulai, Lur.
        </p>

    </div>

    <div
        id="mobileReadingProgress"
        class="lg:hidden fixed right-4 bottom-5 z-40 bg-white border border-stone-200 rounded-xl shadow-lg shadow-stone-900/10 px-3 py-2.5">

        <div class="flex items-center gap-2.5">

            <div class="w-7 h-7 rounded-lg bg-amber-50 text-amber-800 flex items-center justify-center shrink-0">

                <i class="fa-solid fa-book-open text-xs"></i>

            </div>

            <div class="min-w-0">

                <div class="flex items-center justify-between gap-3">

                    <span class="text-[9px] uppercase tracking-[0.14em] font-black text-stone-400">
                        Progres Baca
                    </span>

                    <span
                        id="mobileProgressPercent"
                        class="text-xs font-black text-stone-900">
                        0%
                    </span>

                </div>

                <div class="w-24 h-1.5 rounded-full bg-stone-100 overflow-hidden mt-1">

                    <div
                        id="mobileProgressBar"
                        class="h-full w-0 rounded-full bg-amber-400 transition-[width] duration-150">
                    </div>

                </div>

            </div>

        </div>

    </div>

    <main class="flex-grow">

        <div class="max-w-5xl mx-auto px-4 sm:px-6 py-8 md:py-12">

            <div class="mb-6">

                <a href="index.php"
                    class="inline-flex items-center gap-2 text-sm font-semibold text-stone-500 hover:text-amber-900 transition">

                    <i class="fa-solid fa-arrow-left text-xs"></i>

                    Kembali ke Beranda

                </a>

            </div>

            <article class="bg-white border border-stone-200 rounded-3xl shadow-sm overflow-hidden">

                <?php if (!empty($berita['gambar']) && file_exists('assets/img/' . $berita['gambar'])): ?>

                    <div class="relative w-full h-64 sm:h-80 md:h-[28rem] bg-stone-100 overflow-hidden">

                        <img
                            src="assets/img/<?= htmlspecialchars($berita['gambar']); ?>"
                            alt="<?= htmlspecialchars($berita['judul']); ?>"
                            class="w-full h-full object-cover">

                        <div class="absolute inset-0 bg-gradient-to-t from-black/40 via-transparent to-transparent"></div>

                    </div>

                <?php else: ?>

                    <div class="javanese-pattern h-56 md:h-64 flex items-center justify-center">

                        <div class="text-center">

                            <div class="w-16 h-16 mx-auto rounded-2xl bg-white/10 border border-white/10 flex items-center justify-center mb-3">

                                <i class="fa-regular fa-image text-2xl text-amber-300"></i>

                            </div>

                            <p class="text-xs uppercase tracking-[0.18em] font-bold text-amber-300">
                                Ngabar Yuk!
                            </p>

                            <p class="text-sm text-stone-300 mt-1">
                                Kabar tanpa gambar
                            </p>

                        </div>

                    </div>

                <?php endif; ?>

                <div class="px-5 py-7 sm:px-8 md:px-12 md:py-10">

                    <div class="max-w-3xl mx-auto">

                        <div class="flex flex-wrap items-center gap-2 mb-5">

                            <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full bg-amber-50 border border-amber-100 text-amber-900 text-xs font-bold uppercase tracking-wider">

                                <i class="fa-solid fa-tag text-[10px]"></i>

                                <?= htmlspecialchars($berita['kategori']); ?>

                            </span>

                            <span class="text-stone-300">
                                •
                            </span>

                            <span class="inline-flex items-center gap-1.5 text-xs font-medium text-stone-500">

                                <i class="fa-regular fa-calendar text-amber-800"></i>

                                <?= date('d M Y, H:i', strtotime($berita['tanggal'])); ?> WIB

                            </span>

                            <span class="text-stone-300">
                                •
                            </span>

                            <span class="inline-flex items-center gap-1.5 text-xs font-medium text-stone-500">

                                <i class="fa-regular fa-clock text-amber-800"></i>

                                <?= (int)$berita['read_time']; ?> menit baca

                            </span>

                        </div>

                        <h1 class="text-3xl sm:text-4xl md:text-5xl font-black tracking-tight leading-[1.08] text-stone-900">

                            <?= htmlspecialchars($berita['judul']); ?>

                        </h1>

                        <div class="flex items-center gap-3 mt-7 pb-7 border-b border-stone-100">

                            <a
                                href="author.php?nama=<?= urlencode($berita['penulis']); ?>"
                                class="w-11 h-11 rounded-2xl bg-amber-800 text-white flex items-center justify-center shadow-sm hover:bg-amber-900 transition shrink-0">

                                <i class="fa-solid fa-user-pen"></i>

                            </a>

                            <div>

                                <p class="text-[11px] uppercase tracking-wider font-semibold text-stone-400">
                                    Ditulis oleh
                                </p>

                                <a
                                    href="author.php?nama=<?= urlencode($berita['penulis']); ?>"
                                    class="inline-block text-sm font-bold text-stone-800 mt-0.5 hover:text-amber-800 transition">

                                    <?= htmlspecialchars($berita['penulis']); ?>

                                </a>

                                <p class="text-[11px] text-stone-400 mt-0.5">
                                    Lihat profil penulis
                                </p>

                            </div>

                        </div>

                        <div
                            id="articleContent"
                            class="mt-8 text-[16px] sm:text-[17px] leading-8 text-stone-700">

                            <?= nl2br(htmlspecialchars($berita['konten'])); ?>

                        </div>

                        <div class="mt-10 pt-6 border-t border-stone-100">

                            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">

                                <a
                                    href="index.php"
                                    class="inline-flex items-center gap-2 text-sm font-semibold text-stone-500 hover:text-amber-900 transition">

                                    <i class="fa-solid fa-arrow-left"></i>

                                    Lihat Kabar Lainnya

                                </a>

                            </div>

                        </div>

                    </div>

                </div>

            </article>

            <?php if (!empty($artikelTerkait)): ?>

                <section class="mt-8">

                    <div class="flex items-end justify-between gap-4 mb-4">

                        <div>

                            <p class="text-[11px] uppercase tracking-[0.18em] text-amber-800 font-bold">
                                Lanjutan Ngabar
                            </p>

                            <h2 class="text-xl sm:text-2xl font-black text-stone-900 mt-1">
                                Mungkin Kamu Juga Tertarik
                            </h2>

                        </div>

                        <div class="hidden sm:flex items-center gap-2 text-xs text-stone-400">

                            <span class="w-2 h-2 rounded-full bg-amber-400"></span>

                            Kategori <?= htmlspecialchars($berita['kategori']); ?>

                        </div>

                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">

                        <?php foreach ($artikelTerkait as $terkait): ?>

                            <a
                                href="detail.php?id=<?= $terkait['id']; ?>"
                                class="group bg-white border border-stone-200 rounded-2xl overflow-hidden shadow-sm hover:shadow-md hover:-translate-y-0.5 transition-all duration-300">

                                <?php if (!empty($terkait['gambar']) && file_exists('assets/img/' . $terkait['gambar'])): ?>

                                    <div class="relative aspect-[16/9] bg-stone-100 overflow-hidden">

                                        <img
                                            src="assets/img/<?= htmlspecialchars($terkait['gambar']); ?>"
                                            alt="<?= htmlspecialchars($terkait['judul']); ?>"
                                            class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">

                                        <div class="absolute inset-0 bg-gradient-to-t from-black/20 to-transparent"></div>

                                    </div>

                                <?php else: ?>

                                    <div class="javanese-pattern aspect-[16/9] flex items-center justify-center">

                                        <div class="text-center">

                                            <i class="fa-regular fa-image text-xl text-amber-300"></i>

                                            <p class="text-[10px] uppercase tracking-wider font-bold text-stone-300 mt-1">
                                                Ngabar Yuk!
                                            </p>

                                        </div>

                                    </div>

                                <?php endif; ?>

                                <div class="p-5">

                                    <div class="flex items-center justify-between gap-3 mb-3">

                                        <span class="inline-flex items-center px-2.5 py-1 rounded-lg bg-amber-50 border border-amber-100 text-amber-900 text-[10px] font-bold uppercase tracking-wider">

                                            <?= htmlspecialchars($terkait['kategori']); ?>

                                        </span>

                                        <span class="inline-flex items-center gap-1 text-[11px] text-stone-400 shrink-0">

                                            <i class="fa-regular fa-clock"></i>

                                            <?= (int)$terkait['read_time']; ?> menit

                                        </span>

                                    </div>

                                    <h3 class="text-base sm:text-lg font-black leading-snug text-stone-900 group-hover:text-amber-900 transition">

                                        <?= htmlspecialchars($terkait['judul']); ?>

                                    </h3>

                                    <div class="flex items-center gap-2 mt-4 text-[11px] text-stone-400">

                                        <i class="fa-regular fa-calendar"></i>

                                        <?= date('d M Y', strtotime($terkait['tanggal'])); ?>

                                        <span class="text-stone-300">
                                            •
                                        </span>

                                        <a
                                            href="author.php?nama=<?= urlencode($terkait['penulis']); ?>"
                                            onclick="event.stopPropagation();"
                                            class="hover:text-amber-800 transition">

                                            <?= htmlspecialchars($terkait['penulis']); ?>

                                        </a>

                                    </div>

                                </div>

                            </a>

                        <?php endforeach; ?>

                    </div>

                </section>

            <?php endif; ?>

            <div class="mt-8 javanese-pattern rounded-3xl p-6 md:p-7 text-white">

                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-5">

                    <div class="flex items-start gap-4">

                        <div class="w-11 h-11 rounded-2xl bg-white/10 border border-white/10 flex items-center justify-center shrink-0">

                            <i class="fa-solid fa-mug-hot text-amber-300"></i>

                        </div>

                        <div>

                            <p class="text-sm font-black">
                                Sudah selesai membaca?
                            </p>

                            <p class="text-xs text-stone-300 mt-1 leading-relaxed">
                                Mampir lagi dan temukan kabar lainnya di Ngabar Yuk!
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

    <?php
    include 'assets/footer.php';
    ?>

    <script>
        const articleContent = document.getElementById('articleContent');
        const progressPercent = document.getElementById('progressPercent');
        const progressBar = document.getElementById('progressBar');
        const progressText = document.getElementById('progressText');

        const mobileProgressPercent = document.getElementById('mobileProgressPercent');
        const mobileProgressBar = document.getElementById('mobileProgressBar');

        function updateReadingProgress() {
            const articleTop = articleContent.getBoundingClientRect().top + window.scrollY;
            const articleHeight = articleContent.offsetHeight;
            const viewportHeight = window.innerHeight;

            const startPoint = articleTop;
            const endPoint = articleTop + articleHeight - viewportHeight;

            if (endPoint <= startPoint) {
                setReadingProgress(100);
                return;
            }

            const currentPosition = window.scrollY;
            const progress = ((currentPosition - startPoint) / (endPoint - startPoint)) * 100;

            setReadingProgress(Math.min(100, Math.max(0, progress)));
        }

        function setReadingProgress(progress) {
            const roundedProgress = Math.round(progress);

            progressPercent.textContent = `${roundedProgress}%`;
            progressBar.style.width = `${roundedProgress}%`;

            mobileProgressPercent.textContent = `${roundedProgress}%`;
            mobileProgressBar.style.width = `${roundedProgress}%`;

            if (roundedProgress >= 100) {
                progressText.textContent = 'Rampung. Matur nuwun sudah ngabar.';
            } else if (roundedProgress >= 75) {
                progressText.textContent = 'Dikit lagi, Lur.';
            } else if (roundedProgress >= 50) {
                progressText.textContent = 'Separuh lebih, lanjut.';
            } else if (roundedProgress >= 25) {
                progressText.textContent = 'Sudah lumayan jauh.';
            } else {
                progressText.textContent = 'Baru mulai, Lur.';
            }
        }

        window.addEventListener('scroll', updateReadingProgress, {
            passive: true
        });

        window.addEventListener('resize', updateReadingProgress);

        updateReadingProgress();
    </script>

</body>

</html>