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

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

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

                            <div class="w-11 h-11 rounded-2xl bg-amber-800 text-white flex items-center justify-center shadow-sm">

                                <i class="fa-solid fa-user-pen"></i>

                            </div>

                            <div>

                                <p class="text-[11px] uppercase tracking-wider font-semibold text-stone-400">
                                    Ditulis oleh
                                </p>

                                <p class="text-sm font-bold text-stone-800 mt-0.5">
                                    <?= htmlspecialchars($berita['penulis']); ?>
                                </p>

                            </div>

                        </div>

                        <div class="mt-8 text-[16px] sm:text-[17px] leading-8 text-stone-700">

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

                                <div class="flex items-center gap-2">

                                    <a
                                        href="update.php?id=<?= $berita['id']; ?>"
                                        class="inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl bg-amber-50 border border-amber-200/70 text-amber-900 text-sm font-bold hover:bg-amber-100 transition">

                                        <i class="fa-solid fa-pen-to-square"></i>

                                        Sunting

                                    </a>

                                    <a
                                        href="delete.php?id=<?= $berita['id']; ?>"
                                        class="inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl bg-rose-50 border border-rose-200/70 text-rose-700 text-sm font-bold hover:bg-rose-100 transition">

                                        <i class="fa-solid fa-trash-can"></i>

                                        Hapus

                                    </a>

                                </div>

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

                                        <span>
                                            <?= htmlspecialchars($terkait['penulis']); ?>
                                        </span>

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

</body>

</html>