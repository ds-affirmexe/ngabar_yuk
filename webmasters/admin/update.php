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

if (isset($_POST['submit'])) {

    $nama = trim($_POST['nama']);
    $status = trim($_POST['status']);

    if (empty($nama) || empty($status)) {

        $error = 'Nama dan status wajib diisi.';
    } elseif (strlen($nama) < 3) {

        $error = 'Nama minimal 3 karakter.';
    } elseif (!in_array($status, ['aktif', 'nonaktif'])) {

        $error = 'Status akun tidak valid.';
    }

    if (empty($error)) {

        $stmt = mysqli_prepare(
            $conn,
            "UPDATE users SET nama = ?, status = ? WHERE id = ? AND role = 'admin'"
        );

        mysqli_stmt_bind_param(
            $stmt,
            "ssi",
            $nama,
            $status,
            $id
        );

        if (mysqli_stmt_execute($stmt)) {

            mysqli_stmt_close($stmt);

            header('Location: index.php?status=update');
            exit;
        } else {

            $error = 'Gagal memperbarui data Admin. Silakan coba lagi.';

            mysqli_stmt_close($stmt);
        }
    }

    $admin['nama'] = $nama;
    $admin['status'] = $status;
}

?>

<!DOCTYPE html>
<html lang="id">

<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Edit Admin - Ngabar Yuk! Webmaster</title>

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

        .form-field {
            transition:
                border-color 180ms ease,
                box-shadow 180ms ease,
                background-color 180ms ease;
        }

        .form-field:focus {
            background-color: white;
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

        <div class="max-w-3xl mx-auto">

            <div class="mb-7">

                <a href="index.php"
                    class="inline-flex items-center gap-2 text-xs font-semibold text-stone-500 hover:text-[#542f1b] transition mb-5">

                    <i class="fa-solid fa-arrow-left text-[10px]"></i>

                    Kembali ke Kelola Admin

                </a>

                <div class="flex items-start gap-4">

                    <div class="w-12 h-12 rounded-2xl bg-amber-50 border border-amber-200 text-amber-800 flex items-center justify-center shrink-0">

                        <i class="fa-solid fa-user-pen text-lg"></i>

                    </div>

                    <div>

                        <p class="text-xs font-bold uppercase tracking-[0.16em] text-amber-800 mb-1.5">
                            Manajemen Admin
                        </p>

                        <h1 class="text-2xl md:text-3xl font-black text-[#542f1b] tracking-tight">
                            Edit Admin
                        </h1>

                        <p class="text-sm text-stone-500 mt-2 leading-relaxed">
                            Perbarui informasi dan status akun Admin yang dipilih.
                        </p>

                    </div>

                </div>

            </div>

            <?php if (!empty($error)): ?>

                <div class="fade-up mb-6 bg-rose-50 border border-rose-200 text-rose-800 rounded-2xl px-5 py-4 flex items-start gap-3">

                    <div class="w-9 h-9 rounded-xl bg-rose-100 text-rose-600 flex items-center justify-center shrink-0">

                        <i class="fa-solid fa-triangle-exclamation text-sm"></i>

                    </div>

                    <div>

                        <p class="text-sm font-bold">
                            Gagal memperbarui akun
                        </p>

                        <p class="text-xs mt-1 leading-relaxed">
                            <?= htmlspecialchars($error); ?>
                        </p>

                    </div>

                </div>

            <?php endif; ?>

            <div class="grid grid-cols-1 lg:grid-cols-[1fr_260px] gap-5 items-start">

                <form method="POST"
                    action=""
                    class="bg-white border border-stone-200 rounded-3xl shadow-sm overflow-hidden">

                    <div class="px-6 md:px-7 py-4 border-b border-stone-100 bg-stone-50/60 flex items-center gap-3">

                        <div class="w-9 h-9 rounded-xl bg-amber-100 text-amber-800 flex items-center justify-center">

                            <i class="fa-solid fa-id-card text-sm"></i>

                        </div>

                        <div>

                            <p class="text-sm font-black text-stone-800">
                                Informasi Admin
                            </p>

                            <p class="text-[11px] text-stone-400">
                                Perbarui data akun
                            </p>

                        </div>

                    </div>

                    <div class="p-6 md:p-7 space-y-5">

                        <div>

                            <label for="nama"
                                class="block text-sm font-bold text-stone-700 mb-2">

                                <i class="fa-solid fa-user mr-1.5 text-amber-800"></i>

                                Nama Lengkap

                                <span class="text-rose-500">*</span>

                            </label>

                            <div class="relative">

                                <i class="fa-solid fa-user absolute left-4 top-1/2 -translate-y-1/2 text-xs text-stone-400 pointer-events-none"></i>

                                <input
                                    type="text"
                                    id="nama"
                                    name="nama"
                                    value="<?= htmlspecialchars($admin['nama']); ?>"
                                    placeholder="Masukkan nama lengkap"
                                    autocomplete="name"
                                    required
                                    class="form-field w-full pl-10 pr-4 py-3 bg-stone-50 border border-stone-200 rounded-xl text-sm text-stone-800 placeholder:text-stone-400 focus:outline-none focus:border-amber-600 focus:ring-4 focus:ring-amber-600/10">

                            </div>

                            <p class="text-[11px] text-stone-400 mt-1.5">
                                Minimal 3 karakter.
                            </p>

                        </div>

                        <div>

                            <label for="username"
                                class="block text-sm font-bold text-stone-700 mb-2">

                                <i class="fa-solid fa-at mr-1.5 text-amber-800"></i>

                                Username

                            </label>

                            <div class="relative">

                                <i class="fa-solid fa-at absolute left-4 top-1/2 -translate-y-1/2 text-xs text-stone-400 pointer-events-none"></i>

                                <input
                                    type="text"
                                    id="username"
                                    value="<?= htmlspecialchars($admin['username']); ?>"
                                    disabled
                                    class="w-full pl-10 pr-4 py-3 bg-stone-100 border border-stone-200 rounded-xl text-sm text-stone-500 cursor-not-allowed">

                            </div>

                            <p class="text-[11px] text-stone-400 mt-1.5">
                                Username tidak dapat diubah setelah akun dibuat.
                            </p>

                        </div>

                        <div>

                            <label for="status"
                                class="block text-sm font-bold text-stone-700 mb-2">

                                <i class="fa-solid fa-circle-half-stroke mr-1.5 text-amber-800"></i>

                                Status Akun

                                <span class="text-rose-500">*</span>

                            </label>

                            <div class="relative">

                                <i class="fa-solid fa-toggle-on absolute left-4 top-1/2 -translate-y-1/2 text-xs text-stone-400 pointer-events-none"></i>

                                <select
                                    id="status"
                                    name="status"
                                    required
                                    class="form-field appearance-none w-full pl-10 pr-10 py-3 bg-stone-50 border border-stone-200 rounded-xl text-sm text-stone-800 focus:outline-none focus:border-amber-600 focus:ring-4 focus:ring-amber-600/10">

                                    <option value="aktif" <?= $admin['status'] === 'aktif' ? 'selected' : ''; ?>>
                                        Aktif
                                    </option>

                                    <option value="nonaktif" <?= $admin['status'] === 'nonaktif' ? 'selected' : ''; ?>>
                                        Nonaktif
                                    </option>

                                </select>

                                <i class="fa-solid fa-chevron-down absolute right-4 top-1/2 -translate-y-1/2 text-[10px] text-stone-400 pointer-events-none"></i>

                            </div>

                            <p class="text-[11px] text-stone-400 mt-1.5">
                                Admin nonaktif tidak dapat digunakan untuk login.
                            </p>

                        </div>

                        <div class="border-t border-stone-100 pt-5">

                            <div class="flex items-center gap-2 mb-4">

                                <span class="w-7 h-7 rounded-lg bg-stone-100 text-stone-500 flex items-center justify-center">

                                    <i class="fa-solid fa-shield-halved text-[10px]"></i>

                                </span>

                                <div>

                                    <p class="text-sm font-black text-stone-800">
                                        Detail Akun
                                    </p>

                                    <p class="text-[11px] text-stone-400">
                                        Informasi yang tidak dapat diubah
                                    </p>

                                </div>

                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">

                                <div class="rounded-2xl bg-stone-50 border border-stone-100 p-4">

                                    <p class="text-[10px] uppercase tracking-[0.14em] text-stone-400 font-bold">
                                        Role
                                    </p>

                                    <div class="flex items-center gap-2 mt-2">

                                        <span class="w-7 h-7 rounded-lg bg-amber-100 text-amber-800 flex items-center justify-center">

                                            <i class="fa-solid fa-user-shield text-[10px]"></i>

                                        </span>

                                        <span class="text-sm font-bold text-stone-700">
                                            Admin
                                        </span>

                                    </div>

                                </div>

                                <div class="rounded-2xl bg-stone-50 border border-stone-100 p-4">

                                    <p class="text-[10px] uppercase tracking-[0.14em] text-stone-400 font-bold">
                                        Dibuat
                                    </p>

                                    <div class="flex items-center gap-2 mt-2">

                                        <span class="w-7 h-7 rounded-lg bg-stone-100 text-stone-500 flex items-center justify-center">

                                            <i class="fa-regular fa-calendar text-[10px]"></i>

                                        </span>

                                        <span class="text-sm font-bold text-stone-700">

                                            <?= date('d M Y', strtotime($admin['created_at'])); ?>

                                        </span>

                                    </div>

                                </div>

                            </div>

                        </div>

                    </div>

                    <div class="border-t border-stone-100 bg-stone-50/60 px-6 md:px-7 py-5 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">

                        <p class="text-[11px] text-stone-400">

                            <i class="fa-solid fa-circle-info mr-1"></i>

                            Periksa kembali perubahan sebelum menyimpan.

                        </p>

                        <div class="flex items-center justify-end gap-2">

                            <a href="index.php"
                                class="inline-flex items-center justify-center gap-2 px-4 py-2.5 border border-stone-200 bg-white text-stone-600 rounded-xl font-semibold text-sm hover:bg-stone-100 transition">

                                <i class="fa-solid fa-xmark text-xs"></i>

                                Batal

                            </a>

                            <button
                                type="submit"
                                name="submit"
                                onclick="return confirm('Yakin ingin menyimpan perubahan pada Admin ini?');"
                                class="inline-flex items-center justify-center gap-2 bg-[#542f1b] hover:bg-[#452515] text-white font-bold text-sm px-5 py-2.5 rounded-xl transition shadow-sm hover:shadow-md">

                                <i class="fa-solid fa-floppy-disk text-xs"></i>

                                Simpan Perubahan

                            </button>

                        </div>

                    </div>

                </form>

                <aside class="space-y-4">

                    <div class="bg-[#542f1b] text-white rounded-3xl p-5 shadow-lg shadow-stone-900/10 javanese-pattern">

                        <div class="w-10 h-10 rounded-xl bg-amber-400 text-[#542f1b] flex items-center justify-center mb-4">

                            <i class="fa-solid fa-user-gear"></i>

                        </div>

                        <p class="text-[10px] uppercase tracking-[0.16em] font-bold text-amber-300">
                            Pengaturan Admin
                        </p>

                        <h2 class="text-lg font-black mt-1">
                            <?= htmlspecialchars($admin['nama']); ?>
                        </h2>

                        <p class="text-xs text-stone-300 leading-relaxed mt-3">
                            Perubahan status akan langsung memengaruhi kemampuan akun ini untuk melakukan login.
                        </p>

                    </div>

                    <div class="bg-white border border-stone-200 rounded-2xl p-5 shadow-sm">

                        <p class="text-xs font-black text-[#542f1b] mb-4">

                            <i class="fa-solid fa-list-check mr-1.5 text-amber-700"></i>

                            Yang Dapat Diubah

                        </p>

                        <div class="space-y-3">

                            <div class="flex gap-3">

                                <span class="w-6 h-6 shrink-0 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center text-[10px]">

                                    <i class="fa-solid fa-check"></i>

                                </span>

                                <p class="text-xs text-stone-500 leading-relaxed">
                                    Nama lengkap Admin.
                                </p>

                            </div>

                            <div class="flex gap-3">

                                <span class="w-6 h-6 shrink-0 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center text-[10px]">

                                    <i class="fa-solid fa-check"></i>

                                </span>

                                <p class="text-xs text-stone-500 leading-relaxed">
                                    Status akun Aktif atau Nonaktif.
                                </p>

                            </div>

                            <div class="flex gap-3">

                                <span class="w-6 h-6 shrink-0 rounded-lg bg-stone-100 text-stone-400 flex items-center justify-center text-[10px]">

                                    <i class="fa-solid fa-lock"></i>

                                </span>

                                <p class="text-xs text-stone-500 leading-relaxed">
                                    Username dan role tidak dapat diubah.
                                </p>

                            </div>

                        </div>

                    </div>

                    <div class="bg-amber-50 border border-amber-100 rounded-2xl p-5">

                        <div class="flex gap-3">

                            <div class="w-8 h-8 shrink-0 rounded-lg bg-amber-100 text-amber-800 flex items-center justify-center">

                                <i class="fa-solid fa-circle-info text-xs"></i>

                            </div>

                            <div>

                                <p class="text-xs font-bold text-amber-900">
                                    Status Nonaktif
                                </p>

                                <p class="text-[11px] text-amber-800/70 leading-relaxed mt-1">
                                    Akun tetap tersimpan di sistem, tetapi Admin tidak dapat login sampai statusnya diaktifkan kembali.
                                </p>

                            </div>

                        </div>

                    </div>

                </aside>

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

                <p class="text-[11px] text-amber-500 font-medium">
                    Super Admin
                </p>

            </div>

        </div>

    </footer>

</body>

</html>