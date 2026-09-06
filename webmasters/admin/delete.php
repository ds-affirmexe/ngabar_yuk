<?php

session_start();
require_once '../../config.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: ../login.php');
    exit;
}

if ($_SESSION['role'] !== 'super_admin') {
    header('Location: ../dashboard.php');
    exit;
}

$error = '';
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($id <= 0) {
    header('Location: index.php');
    exit;
}

$stmt = mysqli_prepare(
    $conn,
    "SELECT id, nama, username, role, status, created_at FROM users WHERE id = ? AND role = 'admin' LIMIT 1"
);

mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);

$result = mysqli_stmt_get_result($stmt);
$admin = mysqli_fetch_assoc($result);

mysqli_stmt_close($stmt);

if (!$admin) {
    header('Location: index.php');
    exit;
}

if (isset($_POST['delete'])) {

    $stmt = mysqli_prepare(
        $conn,
        "DELETE FROM users WHERE id = ? AND role = 'admin'"
    );

    mysqli_stmt_bind_param(
        $stmt,
        "i",
        $id
    );

    if (mysqli_stmt_execute($stmt)) {

        mysqli_stmt_close($stmt);

        header('Location: index.php?status=delete');
        exit;
    } else {

        $error = 'Gagal menghapus Admin. Silakan coba lagi.';

        mysqli_stmt_close($stmt);
    }
}

?>

<!DOCTYPE html>
<html lang="id">

<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Hapus Admin - Ngabar Yuk! Webmaster</title>

    <script src="https://cdn.tailwindcss.com"></script>

    <link rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        html {
            scroll-behavior: smooth;
        }

        body {
            background-image:
                radial-gradient(circle at 8% 10%, rgba(180, 83, 9, 0.04) 0, transparent 25%),
                radial-gradient(circle at 92% 88%, rgba(120, 53, 15, 0.04) 0, transparent 25%);
        }

        .javanese-pattern {
            background-image:
                linear-gradient(135deg, rgba(255, 255, 255, 0.035) 25%, transparent 25%),
                linear-gradient(225deg, rgba(255, 255, 255, 0.035) 25%, transparent 25%),
                linear-gradient(45deg, rgba(255, 255, 255, 0.035) 25%, transparent 25%),
                linear-gradient(315deg, rgba(255, 255, 255, 0.035) 25%, transparent 25%);
            background-position: 12px 0, 12px 0, 0 0, 0 0;
            background-size: 24px 24px;
        }

        .fade-up {
            animation: fadeUp 0.45s ease both;
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

<body class="bg-stone-50 text-stone-800 font-sans antialiased min-h-screen flex flex-col">

    <header class="sticky top-0 z-50">

        <div class="bg-[#542f1b] text-stone-100 shadow-lg shadow-stone-900/10 javanese-pattern">

            <div class="max-w-5xl mx-auto px-5">

                <div class="h-[72px] flex items-center justify-between">

                    <a href="../dashboard.php" class="group flex items-center gap-3">

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
                                Webmaster • Panel Pengelola
                            </div>

                        </div>

                    </a>

                    <nav class="flex items-center gap-1.5 sm:gap-2">

                        <a href="../dashboard.php"
                            class="hidden sm:inline-flex items-center gap-2 text-stone-200 hover:text-amber-300 font-semibold text-sm px-3 py-2.5 rounded-xl hover:bg-white/5 transition">

                            <i class="fa-solid fa-gauge-high text-xs"></i>

                            Dashboard

                        </a>

                        <a href="index.php"
                            class="hidden sm:inline-flex items-center gap-2 text-stone-200 hover:text-amber-300 font-semibold text-sm px-3 py-2.5 rounded-xl hover:bg-white/5 transition">

                            <i class="fa-solid fa-users text-xs"></i>

                            Admin

                        </a>

                        <a href="../logout.php"
                            class="inline-flex items-center gap-2 bg-white/10 hover:bg-rose-500/20 border border-white/10 text-white font-semibold text-sm px-3.5 py-2.5 rounded-xl transition">

                            <i class="fa-solid fa-right-from-bracket text-xs"></i>

                            <span class="hidden sm:inline">
                                Keluar
                            </span>

                        </a>

                    </nav>

                </div>

            </div>

        </div>

    </header>

    <main class="max-w-5xl w-full mx-auto px-5 py-8 md:py-12 flex-grow">

        <div class="max-w-2xl mx-auto">

            <a href="index.php"
                class="inline-flex items-center gap-2 text-xs font-semibold text-stone-500 hover:text-[#542f1b] transition mb-6">

                <i class="fa-solid fa-arrow-left text-[10px]"></i>

                Kembali ke Kelola Admin

            </a>

            <?php if (!empty($error)): ?>

                <div class="fade-up mb-5 bg-rose-50 border border-rose-200 text-rose-800 rounded-2xl px-5 py-4 flex items-start gap-3">

                    <div class="w-9 h-9 rounded-xl bg-rose-100 text-rose-600 flex items-center justify-center shrink-0">

                        <i class="fa-solid fa-triangle-exclamation text-sm"></i>

                    </div>

                    <div>

                        <p class="text-sm font-bold">
                            Penghapusan gagal
                        </p>

                        <p class="text-xs mt-1 leading-relaxed">
                            <?= htmlspecialchars($error); ?>
                        </p>

                    </div>

                </div>

            <?php endif; ?>

            <div class="bg-white border border-stone-200 rounded-3xl shadow-sm overflow-hidden">

                <div class="bg-rose-50 border-b border-rose-100 px-6 md:px-8 py-7">

                    <div class="flex items-start gap-4">

                        <div class="w-12 h-12 rounded-2xl bg-rose-100 text-rose-600 flex items-center justify-center shrink-0">

                            <i class="fa-solid fa-trash-can text-lg"></i>

                        </div>

                        <div>

                            <p class="text-[10px] uppercase tracking-[0.16em] font-bold text-rose-700">
                                Tindakan Permanen
                            </p>

                            <h1 class="text-2xl font-black text-rose-950 tracking-tight mt-1">
                                Hapus Admin
                            </h1>

                            <p class="text-sm text-rose-900/60 mt-2 leading-relaxed">
                                Kamu akan menghapus akun Admin ini secara permanen dari sistem.
                            </p>

                        </div>

                    </div>

                </div>

                <div class="p-6 md:p-8">

                    <div class="rounded-2xl border border-stone-200 bg-stone-50 overflow-hidden">

                        <div class="px-5 py-4 border-b border-stone-200 flex items-center justify-between gap-3">

                            <div>

                                <p class="text-[10px] uppercase tracking-[0.14em] text-stone-400 font-bold">
                                    Akun yang akan dihapus
                                </p>

                                <p class="text-lg font-black text-[#542f1b] mt-1">
                                    <?= htmlspecialchars($admin['nama']); ?>
                                </p>

                            </div>

                            <?php if ($admin['status'] === 'aktif'): ?>

                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-emerald-50 border border-emerald-100 text-emerald-700 text-[10px] font-bold shrink-0">

                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>

                                    Aktif

                                </span>

                            <?php else: ?>

                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-stone-100 border border-stone-200 text-stone-500 text-[10px] font-bold shrink-0">

                                    <span class="w-1.5 h-1.5 rounded-full bg-stone-400"></span>

                                    Nonaktif

                                </span>

                            <?php endif; ?>

                        </div>

                        <div class="divide-y divide-stone-200">

                            <div class="px-5 py-3.5 flex items-center justify-between gap-4">

                                <span class="text-xs text-stone-400">
                                    Username
                                </span>

                                <span class="text-sm font-semibold text-stone-700">
                                    <?= htmlspecialchars($admin['username']); ?>
                                </span>

                            </div>

                            <div class="px-5 py-3.5 flex items-center justify-between gap-4">

                                <span class="text-xs text-stone-400">
                                    Role
                                </span>

                                <span class="text-sm font-semibold text-stone-700">
                                    Admin
                                </span>

                            </div>

                            <div class="px-5 py-3.5 flex items-center justify-between gap-4">

                                <span class="text-xs text-stone-400">
                                    Dibuat
                                </span>

                                <span class="text-sm font-semibold text-stone-700">
                                    <?= date('d M Y', strtotime($admin['created_at'])); ?>
                                </span>

                            </div>

                        </div>

                    </div>

                    <div class="mt-5 rounded-2xl bg-amber-50 border border-amber-100 px-5 py-4">

                        <div class="flex items-start gap-3">

                            <div class="w-8 h-8 rounded-lg bg-amber-100 text-amber-800 flex items-center justify-center shrink-0">

                                <i class="fa-solid fa-circle-info text-xs"></i>

                            </div>

                            <div>

                                <p class="text-xs font-bold text-amber-900">
                                    Berbeda dengan menonaktifkan
                                </p>

                                <p class="text-[11px] text-amber-800/70 leading-relaxed mt-1">
                                    Menonaktifkan akun hanya menghentikan akses login. Menghapus akun akan menghilangkan data akun Admin ini dari database dan tidak dapat dibatalkan melalui panel.
                                </p>

                            </div>

                        </div>

                    </div>

                    <div class="mt-7 flex flex-col sm:flex-row sm:items-center sm:justify-end gap-3">

                        <a href="index.php"
                            class="inline-flex items-center justify-center gap-2 px-5 py-3 border border-stone-200 bg-white text-stone-600 rounded-xl font-semibold text-sm hover:bg-stone-100 transition">

                            <i class="fa-solid fa-arrow-left text-xs"></i>

                            Batal

                        </a>

                        <form method="POST"
                            action="">

                            <button
                                type="submit"
                                name="delete"
                                onclick="return confirm('Yakin ingin menghapus Admin <?= htmlspecialchars($admin['nama'], ENT_QUOTES); ?> secara permanen? Akun ini akan dihapus dari sistem dan tindakan ini tidak dapat dibatalkan.');"
                                class="w-full sm:w-auto inline-flex items-center justify-center gap-2 bg-rose-600 hover:bg-rose-700 text-white font-bold text-sm px-5 py-3 rounded-xl transition shadow-sm hover:shadow-md">

                                <i class="fa-solid fa-trash-can text-xs"></i>

                                Hapus Permanen

                            </button>

                        </form>

                    </div>

                </div>

            </div>

        </div>

    </main>

    <footer class="bg-[#3a2113] text-stone-300 mt-8">

        <div class="max-w-5xl mx-auto px-5">

            <div class="py-7 flex flex-col md:flex-row md:items-center md:justify-between gap-5">

                <div>

                    <a href="../dashboard.php"
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
                        Panel pengelolaan kabar dan administrasi Webmaster Ngabar Yuk!
                    </p>

                </div>

                <div class="flex items-center gap-5 text-xs">

                    <a href="../dashboard.php"
                        class="hover:text-amber-300 transition">
                        Dashboard
                    </a>

                    <a href="index.php"
                        class="hover:text-amber-300 transition">
                        Kelola Admin
                    </a>

                    <a href="../../index.php"
                        class="hover:text-amber-300 transition">
                        Situs Publik
                    </a>

                </div>

            </div>

            <div class="border-t border-white/10 py-4 flex flex-col sm:flex-row justify-between items-center gap-2 text-center sm:text-left">

                <p class="text-[11px] text-stone-500">
                    © 2026 Ngabar Yuk! • Webmaster Panel
                </p>

                <p class="text-[11px] text-rose-400 font-medium">
                    Hapus Admin
                </p>

            </div>

        </div>

    </footer>

</body>

</html>