<?php

session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: ../login.php');
    exit;
}

require_once '../../config.php';

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

$error = '';

if (isset($_POST['confirm_delete'])) {

    if (!empty($berita['gambar']) && file_exists('../../assets/img/' . $berita['gambar'])) {
        unlink('../../assets/img/' . $berita['gambar']);
    }

    $stmt = mysqli_prepare($conn, "DELETE FROM berita WHERE id = ?");
    mysqli_stmt_bind_param($stmt, "i", $id);

    if (mysqli_stmt_execute($stmt)) {
        header("Location: index.php?status=hapus");
        exit;
    } else {
        $error = "Gagal menghapus kabar dari database: " . mysqli_error($conn);
    }

    mysqli_stmt_close($stmt);
}

?>

<!DOCTYPE html>
<html lang="id">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0">

    <title>Hapus Kabar - Webmaster Ngabar Yuk!</title>

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
                linear-gradient(135deg, rgba(255, 255, 255, 0.035) 25%, transparent 25%),
                linear-gradient(225deg, rgba(255, 255, 255, 0.035) 25%, transparent 25%),
                linear-gradient(45deg, rgba(255, 255, 255, 0.035) 25%, transparent 25%),
                linear-gradient(315deg, rgba(255, 255, 255, 0.035) 25%, #542f1b 25%);
            background-position: 12px 0, 12px 0, 0 0, 0 0;
            background-size: 24px 24px;
        }
    </style>

</head>

<body class="bg-stone-50 text-stone-800 font-sans antialiased flex flex-col min-h-screen selection:bg-amber-200 selection:text-amber-900">

    <header class="sticky top-0 z-50">

        <div class="bg-[#542f1b] text-stone-100 shadow-lg shadow-stone-900/10 javanese-pattern">

            <div class="max-w-5xl mx-auto px-5">

                <div class="h-[72px] flex items-center justify-between">

                    <a
                        href="../index.php"
                        class="group flex items-center gap-3">

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

                                Webmaster • Berita

                            </div>

                        </div>

                    </a>

                    <nav class="flex items-center gap-1.5 sm:gap-2">

                        <a
                            href="index.php"
                            class="inline-flex items-center gap-2 bg-white/10 hover:bg-white/15 border border-white/10 text-white font-semibold text-sm px-3.5 py-2.5 rounded-xl transition">

                            <i class="fa-solid fa-arrow-left text-xs"></i>

                            <span class="hidden sm:inline">

                                Kembali

                            </span>

                        </a>

                    </nav>

                </div>

            </div>

        </div>

    </header>

    <main class="flex-grow">

        <div class="max-w-4xl mx-auto px-5 py-10 md:py-16">

            <div class="max-w-2xl mx-auto">

                <div class="text-center mb-8">

                    <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-rose-50 border border-rose-100 text-rose-700 text-xs font-bold uppercase tracking-wider mb-4">

                        <i class="fa-solid fa-triangle-exclamation"></i>

                        Konfirmasi Penghapusan

                    </div>

                    <h1 class="text-3xl md:text-4xl font-black tracking-tight text-stone-900">

                        Hapus Kabar Ini?

                    </h1>

                    <p class="mt-3 text-sm md:text-base text-stone-500 leading-relaxed max-w-xl mx-auto">

                        Tindakan ini bersifat permanen. Pastikan kamu sudah memeriksa kabar yang akan dihapus.

                    </p>

                </div>

                <?php if (!empty($error)): ?>

                    <div class="mb-6 bg-rose-50 border border-rose-200 rounded-2xl p-4 flex items-start gap-3">

                        <div class="w-9 h-9 rounded-xl bg-rose-100 text-rose-600 flex items-center justify-center shrink-0">

                            <i class="fa-solid fa-circle-exclamation"></i>

                        </div>

                        <div>

                            <p class="text-sm font-bold text-rose-800">

                                Penghapusan gagal

                            </p>

                            <p class="text-sm text-rose-700 mt-0.5">

                                <?= htmlspecialchars($error); ?>

                            </p>

                        </div>

                    </div>

                <?php endif; ?>

                <div class="bg-white border border-stone-200 rounded-3xl shadow-sm overflow-hidden">

                    <div class="javanese-pattern px-6 md:px-8 py-6 text-white">

                        <div class="flex items-center gap-3">

                            <div class="w-11 h-11 rounded-2xl bg-white/10 border border-white/10 flex items-center justify-center">

                                <i class="fa-solid fa-newspaper text-amber-300"></i>

                            </div>

                            <div>

                                <p class="text-[11px] uppercase tracking-[0.18em] text-amber-300 font-bold">

                                    Kabar yang dipilih

                                </p>

                                <p class="text-sm text-stone-300 mt-0.5">

                                    Periksa kembali sebelum melanjutkan

                                </p>

                            </div>

                        </div>

                    </div>

                    <div class="p-6 md:p-8">

                        <div class="flex flex-col sm:flex-row gap-5">

                            <?php if (!empty($berita['gambar']) && file_exists('../../assets/img/' . $berita['gambar'])): ?>

                                <div class="sm:w-44 shrink-0">

                                    <div class="aspect-[4/3] rounded-2xl overflow-hidden border border-stone-200 bg-stone-100">

                                        <img
                                            src="../../assets/img/<?= htmlspecialchars($berita['gambar']); ?>"
                                            alt="<?= htmlspecialchars($berita['judul']); ?>"
                                            class="w-full h-full object-cover">

                                    </div>

                                </div>

                            <?php else: ?>

                                <div class="sm:w-44 shrink-0">

                                    <div class="aspect-[4/3] rounded-2xl bg-stone-100 border border-stone-200 flex items-center justify-center">

                                        <div class="text-center text-stone-400">

                                            <i class="fa-regular fa-image text-3xl mb-2"></i>

                                            <p class="text-xs font-medium">

                                                Tanpa gambar

                                            </p>

                                        </div>

                                    </div>

                                </div>

                            <?php endif; ?>

                            <div class="min-w-0 flex-1">

                                <span class="inline-flex items-center px-2.5 py-1 rounded-lg bg-amber-50 border border-amber-100 text-amber-900 text-xs font-bold">

                                    <?= htmlspecialchars($berita['kategori']); ?>

                                </span>

                                <h2 class="text-xl md:text-2xl font-black text-stone-900 leading-tight mt-3">

                                    <?= htmlspecialchars($berita['judul']); ?>

                                </h2>

                                <div class="flex flex-wrap items-center gap-x-4 gap-y-2 mt-4 text-xs text-stone-500">

                                    <span class="inline-flex items-center gap-1.5">

                                        <i class="fa-regular fa-user text-amber-700"></i>

                                        <?= htmlspecialchars($berita['penulis']); ?>

                                    </span>

                                    <span class="inline-flex items-center gap-1.5">

                                        <i class="fa-solid fa-hashtag text-amber-700"></i>

                                        ID <?= htmlspecialchars($berita['id']); ?>

                                    </span>

                                    <span class="inline-flex items-center gap-1.5">

                                        <i class="fa-regular fa-clock text-amber-700"></i>

                                        <?= (int)$berita['read_time']; ?> menit baca

                                    </span>

                                </div>

                            </div>

                        </div>

                        <div class="mt-7 pt-6 border-t border-stone-100">

                            <div class="rounded-2xl bg-rose-50 border border-rose-100 p-4 md:p-5">

                                <div class="flex items-start gap-3">

                                    <div class="w-9 h-9 rounded-xl bg-rose-100 text-rose-600 flex items-center justify-center shrink-0">

                                        <i class="fa-solid fa-trash-can"></i>

                                    </div>

                                    <div>

                                        <p class="text-sm font-bold text-rose-900">

                                            Perlu diperhatikan

                                        </p>

                                        <p class="text-sm text-rose-700 mt-1 leading-relaxed">

                                            Kabar ini akan dihapus dari database dan tidak dapat dipulihkan melalui aplikasi.

                                            <?php if (!empty($berita['gambar'])): ?>

                                                File gambar yang terkait juga akan dihapus dari penyimpanan.

                                            <?php endif; ?>

                                        </p>

                                    </div>

                                </div>

                            </div>

                        </div>

                        <form
                            action=""
                            method="POST"
                            class="mt-7">

                            <div class="flex flex-col-reverse sm:flex-row sm:justify-end gap-3">

                                <a
                                    href="index.php"
                                    class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-5 py-3 rounded-xl bg-stone-100 border border-stone-200 text-stone-700 text-sm font-bold hover:bg-stone-200 transition">

                                    <i class="fa-solid fa-xmark"></i>

                                    Batal

                                </a>

                                <button
                                    type="submit"
                                    name="confirm_delete"
                                    onclick="return confirm('Yakin ingin menghapus kabar ini? Tindakan ini tidak dapat dibatalkan.');"
                                    class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-5 py-3 rounded-xl bg-rose-600 text-white text-sm font-bold shadow-sm hover:bg-rose-700 hover:shadow-md transition">

                                    <i class="fa-solid fa-trash-can"></i>

                                    Ya, Hapus Kabar

                                </button>

                            </div>

                        </form>

                    </div>

                </div>

                <div class="mt-5 text-center">

                    <p class="text-xs text-stone-400">

                        Tidak sengaja membuka halaman ini?

                        <a
                            href="index.php"
                            class="text-amber-800 font-semibold hover:text-amber-900 transition">

                            Kembali ke daftar berita

                        </a>

                    </p>

                </div>

            </div>

        </div>

    </main>

    <footer class="bg-[#3a2113] text-stone-300 mt-auto">

        <div class="max-w-5xl mx-auto px-5">

            <div class="py-8 flex flex-col md:flex-row md:items-center md:justify-between gap-6">

                <div>

                    <a
                        href="../index.php"
                        class="inline-flex items-center gap-2.5 text-white group">

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
                        href="../dashboard.php"
                        class="hover:text-amber-300 transition">

                        Dashboard

                    </a>

                    <a
                        href="index.php"
                        class="hover:text-amber-300 transition">

                        Berita

                    </a>

                    <a
                        href="../profile.php"
                        class="hover:text-amber-300 transition">

                        Profil

                    </a>

                    <a
                        href="../../index.php"
                        class="hover:text-amber-300 transition">

                        Website

                    </a>

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

</body>

</html>