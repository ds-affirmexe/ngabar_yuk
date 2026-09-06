<?php

session_start();

$isLogin = isset($_SESSION['user_id']);
$nama = $isLogin ? $_SESSION['nama'] : '';
$username = $isLogin ? $_SESSION['username'] : '';
$role = $isLogin ? $_SESSION['role'] : '';

if ($role === 'super_admin') {
    $roleLabel = 'Super Admin';
} else {
    $roleLabel = 'Admin';
}

require_once '../config.php';

$artikelQuery = mysqli_query(
    $conn,
    "SELECT id, judul, kategori, penulis, gambar, tanggal FROM berita ORDER BY tanggal DESC LIMIT 6"
);

$artikelTerbaru = $artikelQuery ? mysqli_fetch_all($artikelQuery, MYSQLI_ASSOC) : [];

?>

<!DOCTYPE html>
<html lang="id">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Webmaster - Ngabar Yuk!</title>

    <script src="https://cdn.tailwindcss.com"></script>

    <link
        rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        .article-carousel {
            scrollbar-width: none;
            -ms-overflow-style: none;
        }

        .article-carousel::-webkit-scrollbar {
            display: none;
        }

        .javanese-pattern {
            background-image:
                linear-gradient(30deg, rgba(255, 255, 255, 0.025) 12%, transparent 12.5%, transparent 87%, rgba(255, 255, 255, 0.025) 87.5%, rgba(255, 255, 255, 0.025)),
                linear-gradient(150deg, rgba(255, 255, 255, 0.025) 12%, transparent 12.5%, transparent 87%, rgba(255, 255, 255, 0.025) 87.5%, rgba(255, 255, 255, 0.025)),
                linear-gradient(30deg, rgba(255, 255, 255, 0.025) 12%, transparent 12.5%, transparent 87%, rgba(255, 255, 255, 0.025) 87.5%, rgba(255, 255, 255, 0.025)),
                linear-gradient(150deg, rgba(255, 255, 255, 0.025) 12%, transparent 12.5%, transparent 87%, rgba(255, 255, 255, 0.025) 87.5%, rgba(255, 255, 255, 0.025));
            background-position: 0 0, 0 0, 8px 14px;
            background-size: 16px 28px;
        }
    </style>

</head>

<body class="bg-stone-50 text-stone-800 font-sans antialiased min-h-screen flex flex-col">

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

                                Webmaster

                            </div>

                        </div>

                    </a>

                    <nav class="flex items-center gap-1.5 sm:gap-2">

                        <a
                            href="../index.php"
                            class="inline-flex items-center gap-2 text-stone-200 hover:text-amber-300 font-semibold text-sm px-3 py-2.5 rounded-xl hover:bg-white/5 transition">

                            <i class="fa-solid fa-house text-xs"></i>

                            <span class="hidden sm:inline">Website</span>

                        </a>

                        <?php if ($isLogin): ?>

                            <a
                                href="dashboard.php"
                                class="inline-flex items-center gap-2 bg-amber-400 hover:bg-amber-300 text-[#542f1b] font-bold text-sm px-3.5 py-2.5 rounded-xl transition shadow-sm">

                                <i class="fa-solid fa-gauge-high text-xs"></i>

                                <span class="hidden sm:inline">Dashboard</span>

                            </a>

                        <?php else: ?>

                            <a
                                href="login.php"
                                class="inline-flex items-center gap-2 bg-amber-400 hover:bg-amber-300 text-[#542f1b] font-bold text-sm px-3.5 py-2.5 rounded-xl transition shadow-sm">

                                <i class="fa-solid fa-right-to-bracket text-xs"></i>

                                Masuk

                            </a>

                        <?php endif; ?>

                    </nav>

                </div>

            </div>

        </div>

    </header>

    <main class="flex-1">

        <section class="relative overflow-hidden bg-[#542f1b] text-white">

            <div class="absolute inset-0 javanese-pattern opacity-70"></div>

            <div class="relative max-w-5xl mx-auto px-5 py-16 md:py-20">

                <div class="max-w-3xl">

                    <div class="inline-flex items-center gap-2 text-amber-300 text-xs font-bold uppercase tracking-[0.18em] mb-5">

                        <span class="w-8 h-px bg-amber-400"></span>

                        Webmaster Area

                    </div>

                    <?php if ($isLogin): ?>

                        <h1 class="text-4xl md:text-5xl font-black tracking-tight leading-[1.05]">

                            Sugeng rawuh,

                            <span class="text-amber-400">

                                <?= htmlspecialchars($nama); ?>

                            </span>

                        </h1>

                        <p class="text-stone-300 text-sm md:text-base leading-relaxed mt-5 max-w-2xl">

                            Selamat datang kembali di ruang kerja Webmaster Ngabar Yuk!.
                            Kelola kabar, konten, dan kebutuhan administrasi dari sini.

                        </p>

                        <div class="flex flex-wrap items-center gap-3 mt-7">

                            <a
                                href="dashboard.php"
                                class="inline-flex items-center gap-2 bg-amber-400 hover:bg-amber-300 text-[#542f1b] font-bold text-sm px-5 py-3 rounded-xl transition shadow-sm">

                                <i class="fa-solid fa-gauge-high"></i>

                                Buka Dashboard

                            </a>

                            <a
                                href="profile.php"
                                class="inline-flex items-center gap-2 border border-white/15 hover:bg-white/10 text-stone-200 font-semibold text-sm px-5 py-3 rounded-xl transition">

                                <i class="fa-solid fa-user"></i>

                                Profil Saya

                            </a>

                        </div>

                    <?php else: ?>

                        <h1 class="text-4xl md:text-5xl lg:text-6xl font-black tracking-tight leading-[1.05]">

                            Ruang kerja

                            <span class="text-amber-400">

                                Ngabar Yuk!

                            </span>

                        </h1>

                        <p class="text-stone-300 text-sm md:text-base leading-relaxed mt-5 max-w-2xl">

                            Area khusus Webmaster untuk mengelola kabar,
                            mengatur konten, dan menjalankan kebutuhan administrasi
                            Ngabar Yuk!.

                        </p>

                        <div class="flex flex-wrap items-center gap-3 mt-7">

                            <a
                                href="login.php"
                                class="inline-flex items-center gap-2 bg-amber-400 hover:bg-amber-300 text-[#542f1b] font-bold text-sm px-5 py-3 rounded-xl transition shadow-sm">

                                <i class="fa-solid fa-right-to-bracket"></i>

                                Masuk ke Webmaster

                            </a>

                            <a
                                href="../index.php"
                                class="inline-flex items-center gap-2 border border-white/15 hover:bg-white/10 text-stone-200 font-semibold text-sm px-5 py-3 rounded-xl transition">

                                Lihat Website

                                <i class="fa-solid fa-arrow-up-right-from-square text-xs"></i>

                            </a>

                        </div>

                    <?php endif; ?>

                </div>

            </div>

        </section>

        <section class="max-w-5xl mx-auto px-5 -mt-8 relative z-10">

            <?php if ($isLogin): ?>

                <div class="bg-white border border-stone-200 rounded-3xl shadow-xl shadow-stone-900/5 overflow-hidden">

                    <div class="p-6 md:p-8">

                        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-5">

                            <div class="flex items-center gap-4">

                                <div class="w-14 h-14 rounded-2xl bg-amber-50 border border-amber-200 text-amber-800 flex items-center justify-center flex-shrink-0">

                                    <i class="fa-solid fa-user-shield text-xl"></i>

                                </div>

                                <div>

                                    <p class="text-xs font-bold uppercase tracking-[0.14em] text-stone-400">

                                        Akun aktif

                                    </p>

                                    <h2 class="text-lg font-black text-stone-900 mt-0.5">

                                        <?= htmlspecialchars($nama); ?>

                                    </h2>

                                    <p class="text-xs text-stone-500 mt-0.5">

                                        @<?= htmlspecialchars($username); ?>

                                    </p>

                                </div>

                            </div>

                            <div>

                                <?php if ($role === 'super_admin'): ?>

                                    <span class="inline-flex items-center gap-2 bg-amber-50 border border-amber-200 text-amber-900 px-3 py-2 rounded-xl text-xs font-bold">

                                        <i class="fa-solid fa-crown text-amber-700"></i>

                                        Super Admin

                                    </span>

                                <?php else: ?>

                                    <span class="inline-flex items-center gap-2 bg-stone-100 border border-stone-200 text-stone-700 px-3 py-2 rounded-xl text-xs font-bold">

                                        <i class="fa-solid fa-user-gear text-stone-500"></i>

                                        Admin

                                    </span>

                                <?php endif; ?>

                            </div>

                        </div>

                    </div>

                </div>

            <?php else: ?>

                <div class="bg-white border border-stone-200 rounded-3xl shadow-xl shadow-stone-900/5 p-6 md:p-8">

                    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-6">

                        <div class="flex items-start gap-4">

                            <div class="w-12 h-12 rounded-xl bg-amber-50 border border-amber-200 text-amber-800 flex items-center justify-center flex-shrink-0">

                                <i class="fa-solid fa-shield-halved"></i>

                            </div>

                            <div>

                                <h2 class="font-black text-stone-900">

                                    Area khusus Webmaster

                                </h2>

                                <p class="text-sm text-stone-500 mt-1 leading-relaxed max-w-xl">

                                    Halaman ini digunakan oleh administrator yang memiliki
                                    akses untuk mengelola sistem dan konten Ngabar Yuk!.

                                </p>

                            </div>

                        </div>

                        <a
                            href="login.php"
                            class="inline-flex items-center justify-center gap-2 bg-[#542f1b] hover:bg-[#432515] text-white font-bold text-sm px-5 py-3 rounded-xl transition shadow-sm flex-shrink-0">

                            Masuk

                            <i class="fa-solid fa-arrow-right text-xs"></i>

                        </a>

                    </div>

                </div>

            <?php endif; ?>

        </section>

        <section class="max-w-5xl mx-auto px-5 py-12 md:py-14">

            <div class="mb-7">

                <div class="flex items-center gap-2 text-amber-800 text-xs font-bold uppercase tracking-[0.16em] mb-2">

                    <span class="w-5 h-px bg-amber-600"></span>

                    Webmaster

                </div>

                <h2 class="text-2xl md:text-3xl font-black text-stone-900 tracking-tight">

                    Pengelolaan Sistem

                </h2>

                <p class="text-sm text-stone-500 mt-1.5">

                    Area kerja untuk menjaga konten dan administrasi Ngabar Yuk!.

                </p>

            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">

                <a
                    href="<?= $isLogin ? 'berita/index.php' : 'login.php'; ?>"
                    class="group bg-white border border-stone-200 rounded-2xl p-6 shadow-sm hover:shadow-lg hover:-translate-y-0.5 transition-all duration-300">

                    <div class="flex items-start justify-between gap-4">

                        <div class="w-11 h-11 rounded-xl bg-amber-50 border border-amber-200 text-amber-800 flex items-center justify-center">

                            <i class="fa-solid fa-newspaper"></i>

                        </div>

                        <i class="fa-solid fa-arrow-right text-xs text-stone-300 group-hover:text-[#542f1b] group-hover:translate-x-1 transition-all"></i>

                    </div>

                    <h3 class="text-lg font-black text-stone-900 mt-5 group-hover:text-[#542f1b] transition">

                        Kelola Berita

                    </h3>

                    <p class="text-sm text-stone-500 leading-relaxed mt-2">

                        Tambah, lihat, ubah, dan hapus kabar yang ditampilkan pada website.

                    </p>

                </a>

                <?php if ($isLogin && $role === 'super_admin'): ?>

                    <a
                        href="admin/index.php"
                        class="group bg-white border border-stone-200 rounded-2xl p-6 shadow-sm hover:shadow-lg hover:-translate-y-0.5 transition-all duration-300">

                        <div class="flex items-start justify-between gap-4">

                            <div class="w-11 h-11 rounded-xl bg-amber-50 border border-amber-200 text-amber-800 flex items-center justify-center">

                                <i class="fa-solid fa-users-gear"></i>

                            </div>

                            <i class="fa-solid fa-arrow-right text-xs text-stone-300 group-hover:text-[#542f1b] group-hover:translate-x-1 transition-all"></i>

                        </div>

                        <h3 class="text-lg font-black text-stone-900 mt-5 group-hover:text-[#542f1b] transition">

                            Kelola Admin

                        </h3>

                        <p class="text-sm text-stone-500 leading-relaxed mt-2">

                            Kelola akun administrator yang memiliki akses ke Webmaster Ngabar Yuk!.

                        </p>

                    </a>

                <?php else: ?>

                    <div class="bg-stone-100/70 border border-stone-200 rounded-2xl p-6">

                        <div class="flex items-start justify-between gap-4">

                            <div class="w-11 h-11 rounded-xl bg-stone-200 text-stone-500 flex items-center justify-center">

                                <i class="fa-solid fa-users-gear"></i>

                            </div>

                            <span class="text-[10px] uppercase tracking-wider font-bold text-stone-400 bg-white border border-stone-200 px-2.5 py-1.5 rounded-lg">

                                Super Admin

                            </span>

                        </div>

                        <h3 class="text-lg font-black text-stone-700 mt-5">

                            Kelola Admin

                        </h3>

                        <p class="text-sm text-stone-500 leading-relaxed mt-2">

                            Pengelolaan akun administrator hanya tersedia untuk Super Admin.

                        </p>

                    </div>

                <?php endif; ?>

            </div>

        </section>

        <section class="max-w-5xl mx-auto px-5 pb-14">

            <div class="flex flex-col sm:flex-row sm:items-end sm:justify-between gap-4 mb-6">

                <div>

                    <div class="flex items-center gap-2 text-amber-800 text-xs font-bold uppercase tracking-[0.16em] mb-2">

                        <span class="w-5 h-px bg-amber-600"></span>

                        Konten

                    </div>

                    <h2 class="text-2xl md:text-3xl font-black text-stone-900 tracking-tight">

                        Artikel Terbaru

                    </h2>

                    <p class="text-sm text-stone-500 mt-1.5">

                        Beberapa kabar terbaru yang sedang dikelola di Ngabar Yuk!.

                    </p>

                </div>

                <?php if (!empty($artikelTerbaru)): ?>

                    <a
                        href="<?= $isLogin ? 'berita/index.php' : 'login.php'; ?>"
                        class="inline-flex items-center gap-2 text-sm font-bold text-[#542f1b] hover:text-amber-700 transition">

                        Lihat semua

                        <i class="fa-solid fa-arrow-right text-xs"></i>

                    </a>

                <?php endif; ?>

            </div>

            <?php if (!empty($artikelTerbaru)): ?>

                <div class="relative">

                    <div
                        id="articleCarousel"
                        class="article-carousel flex gap-4 overflow-x-auto snap-x snap-mandatory scroll-smooth pb-2 pr-1">

                        <?php foreach ($artikelTerbaru as $artikel): ?>

                            <article
                                class="group min-w-[82%] sm:min-w-[48%] lg:min-w-[31.8%] snap-start bg-white border border-stone-200 rounded-2xl overflow-hidden shadow-sm hover:shadow-lg transition-all duration-300">

                                <a
                                    href="../detail.php?id=<?= (int)$artikel['id']; ?>"
                                    class="block">

                                    <div class="relative aspect-[16/9] bg-stone-100 overflow-hidden">

                                        <?php if (!empty($artikel['gambar'])): ?>

                                            <img
                                                src="../assets/img/<?= htmlspecialchars($artikel['gambar']); ?>"
                                                alt="<?= htmlspecialchars($artikel['judul']); ?>"
                                                class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">

                                        <?php else: ?>

                                            <div class="w-full h-full flex items-center justify-center text-stone-300">

                                                <i class="fa-solid fa-image text-3xl"></i>

                                            </div>

                                        <?php endif; ?>

                                        <span
                                            class="absolute top-3 left-3 inline-flex items-center bg-[#542f1b]/90 text-amber-200 px-2.5 py-1.5 rounded-lg text-[10px] font-bold uppercase tracking-wide">

                                            <?= htmlspecialchars($artikel['kategori']); ?>

                                        </span>

                                    </div>

                                    <div class="p-5">

                                        <h3
                                            class="font-black text-stone-900 leading-snug line-clamp-2 group-hover:text-[#542f1b] transition">

                                            <?= htmlspecialchars($artikel['judul']); ?>

                                        </h3>

                                        <div class="flex items-center gap-3 mt-4 text-[11px] text-stone-400">

                                            <span class="flex items-center gap-1.5">

                                                <i class="fa-regular fa-user"></i>

                                                <?= htmlspecialchars($artikel['penulis']); ?>

                                            </span>

                                            <span class="flex items-center gap-1.5">

                                                <i class="fa-regular fa-calendar"></i>

                                                <?= date('d M Y', strtotime($artikel['tanggal'])); ?>

                                            </span>

                                        </div>

                                    </div>

                                </a>

                            </article>

                        <?php endforeach; ?>

                    </div>

                    <?php if (count($artikelTerbaru) > 3): ?>

                        <button
                            type="button"
                            onclick="geserArtikel(-1)"
                            aria-label="Artikel sebelumnya"
                            class="hidden sm:flex absolute -left-4 top-1/2 -translate-y-1/2 w-10 h-10 items-center justify-center rounded-full bg-white border border-stone-200 text-stone-700 shadow-md hover:bg-[#542f1b] hover:text-white hover:border-[#542f1b] transition">

                            <i class="fa-solid fa-chevron-left text-xs"></i>

                        </button>

                        <button
                            type="button"
                            onclick="geserArtikel(1)"
                            aria-label="Artikel berikutnya"
                            class="hidden sm:flex absolute -right-4 top-1/2 -translate-y-1/2 w-10 h-10 items-center justify-center rounded-full bg-white border border-stone-200 text-stone-700 shadow-md hover:bg-[#542f1b] hover:text-white hover:border-[#542f1b] transition">

                            <i class="fa-solid fa-chevron-right text-xs"></i>

                        </button>

                    <?php endif; ?>

                </div>

            <?php else: ?>

                <div class="bg-white border border-stone-200 rounded-2xl p-8 text-center">

                    <div class="w-12 h-12 mx-auto rounded-xl bg-stone-100 text-stone-400 flex items-center justify-center">

                        <i class="fa-solid fa-newspaper"></i>

                    </div>

                    <h3 class="font-black text-stone-800 mt-4">

                        Belum ada artikel

                    </h3>

                    <p class="text-sm text-stone-500 mt-1">

                        Artikel yang sudah dibuat akan muncul di sini.

                    </p>

                </div>

            <?php endif; ?>

        </section>

        <section class="max-w-5xl mx-auto px-5 pb-14">

            <div class="bg-amber-50 border border-amber-200/70 rounded-2xl px-6 py-5">

                <div class="flex items-start gap-3">

                    <div class="w-9 h-9 rounded-lg bg-amber-100 text-amber-800 flex items-center justify-center flex-shrink-0">

                        <i class="fa-solid fa-circle-info text-sm"></i>

                    </div>

                    <div>

                        <p class="text-sm font-bold text-amber-950">

                            Area terbatas

                        </p>

                        <p class="text-xs text-amber-900/70 mt-1 leading-relaxed">

                            Webmaster hanya dapat digunakan oleh akun yang telah terdaftar.
                            Hak akses setiap akun ditentukan berdasarkan role yang diberikan.

                        </p>

                    </div>

                </div>

            </div>

        </section>

    </main>

    <footer class="bg-[#3a2113] text-stone-300 mt-auto">

        <div class="max-w-5xl mx-auto px-5">

            <div class="py-8 flex flex-col md:flex-row md:items-center md:justify-between gap-6">

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

                        Webmaster Area untuk pengelolaan konten dan administrasi Ngabar Yuk!.

                    </p>

                </div>

                <div class="flex flex-wrap items-center gap-x-5 gap-y-2 text-xs">

                    <a
                        href="../index.php"
                        class="hover:text-amber-300 transition">

                        Website

                    </a>

                    <?php if ($isLogin): ?>

                        <a
                            href="dashboard.php"
                            class="hover:text-amber-300 transition">

                            Dashboard

                        </a>

                        <a
                            href="profile.php"
                            class="hover:text-amber-300 transition">

                            Profil

                        </a>

                        <a
                            href="logout.php"
                            class="hover:text-amber-300 transition">

                            Keluar

                        </a>

                    <?php else: ?>

                        <a
                            href="login.php"
                            class="hover:text-amber-300 transition">

                            Masuk

                        </a>

                    <?php endif; ?>

                </div>

            </div>

            <div class="border-t border-white/10 py-4 flex flex-col sm:flex-row justify-between items-center gap-2 text-center sm:text-left">

                <p class="text-[11px] text-stone-500">

                    © 2026 Ngabar Yuk! • Webmaster

                </p>

                <p class="text-[11px] text-amber-500 font-medium">

                    Divisi Webmaster

                </p>

            </div>

        </div>

    </footer>

    <script>
        function geserArtikel(arah) {
            const carousel = document.getElementById('articleCarousel');

            if (!carousel) {
                return;
            }

            carousel.scrollBy({
                left: arah * carousel.clientWidth * 0.82,
                behavior: 'smooth'
            });
        }
    </script>

</body>

</html>