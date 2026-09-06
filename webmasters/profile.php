<?php

session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

require_once '../config.php';

$userId = (int) $_SESSION['user_id'];
$nama = $_SESSION['nama'];
$username = $_SESSION['username'];
$role = $_SESSION['role'];

$success = '';
$error = '';

$userQuery = mysqli_prepare(
    $conn,
    "SELECT id, nama, username, role, status, created_at FROM users WHERE id = ? LIMIT 1"
);

mysqli_stmt_bind_param($userQuery, "i", $userId);
mysqli_stmt_execute($userQuery);
$userResult = mysqli_stmt_get_result($userQuery);
$user = mysqli_fetch_assoc($userResult);

if (!$user) {
    session_destroy();
    header('Location: login.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $action = $_POST['action'] ?? '';

    if ($action === 'update_profile') {

        $namaBaru = trim($_POST['nama'] ?? '');

        if ($namaBaru === '') {
            $error = 'Nama tidak boleh kosong.';
        } elseif (strlen($namaBaru) < 3) {
            $error = 'Nama minimal terdiri dari 3 karakter.';
        } else {

            $updateQuery = mysqli_prepare(
                $conn,
                "UPDATE users SET nama = ? WHERE id = ?"
            );

            mysqli_stmt_bind_param(
                $updateQuery,
                "si",
                $namaBaru,
                $userId
            );

            if (mysqli_stmt_execute($updateQuery)) {

                $_SESSION['nama'] = $namaBaru;
                $nama = $namaBaru;
                $user['nama'] = $namaBaru;

                $success = 'Informasi profil berhasil diperbarui.';
            } else {
                $error = 'Gagal memperbarui informasi profil.';
            }
        }
    }

    if ($action === 'update_password') {

        $passwordLama = $_POST['password_lama'] ?? '';
        $passwordBaru = $_POST['password_baru'] ?? '';
        $konfirmasiPassword = $_POST['konfirmasi_password'] ?? '';

        if ($passwordLama === '' || $passwordBaru === '' || $konfirmasiPassword === '') {
            $error = 'Semua kolom password wajib diisi.';
        } elseif (strlen($passwordBaru) < 8) {
            $error = 'Password baru minimal terdiri dari 8 karakter.';
        } elseif ($passwordBaru !== $konfirmasiPassword) {
            $error = 'Konfirmasi password tidak sesuai.';
        } else {

            $passwordQuery = mysqli_prepare(
                $conn,
                "SELECT password FROM users WHERE id = ? LIMIT 1"
            );

            mysqli_stmt_bind_param(
                $passwordQuery,
                "i",
                $userId
            );

            mysqli_stmt_execute($passwordQuery);
            $passwordResult = mysqli_stmt_get_result($passwordQuery);
            $passwordData = mysqli_fetch_assoc($passwordResult);

            if (!$passwordData || !password_verify($passwordLama, $passwordData['password'])) {

                $error = 'Password lama tidak sesuai.';
            } else {

                $passwordHash = password_hash(
                    $passwordBaru,
                    PASSWORD_DEFAULT
                );

                $updatePasswordQuery = mysqli_prepare(
                    $conn,
                    "UPDATE users SET password = ? WHERE id = ?"
                );

                mysqli_stmt_bind_param(
                    $updatePasswordQuery,
                    "si",
                    $passwordHash,
                    $userId
                );

                if (mysqli_stmt_execute($updatePasswordQuery)) {
                    $success = 'Password berhasil diperbarui.';
                } else {
                    $error = 'Gagal memperbarui password.';
                }
            }
        }
    }
}

$roleLabel = $role === 'super_admin' ? 'Super Admin' : 'Admin';

?>

<!DOCTYPE html>
<html lang="id">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Profil - Webmaster Ngabar Yuk!</title>

    <script src="https://cdn.tailwindcss.com"></script>

    <link
        rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        .javanese-pattern {
            background-image:
                linear-gradient(30deg, rgba(255, 255, 255, 0.025) 12%, transparent 12.5%, transparent 87%, rgba(255, 255, 255, 0.025) 87.5%, rgba(255, 255, 255, 0.025)),
                linear-gradient(150deg, rgba(255, 255, 255, 0.025) 12%, transparent 12.5%, transparent 87%, rgba(255, 255, 255, 0.025) 87.5%, rgba(255, 255, 255, 0.025)),
                linear-gradient(30deg, rgba(255, 255, 255, 0.025) 12%, transparent 12.5%, transparent 87%, rgba(255, 255, 255, 0.025) 87.5%, rgba(255, 255, 255, 0.025)),
                linear-gradient(150deg, rgba(255, 255, 255, 0.025) 12%, transparent 12.5%, transparent 87%, rgba(255, 255, 255, 0.025) 87.5%, rgba(255, 255, 255, 0.025));
            background-position: 0 0, 0 0, 8px 14px;
            background-size: 16px 28px;
        }

        input:focus {
            outline: none;
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
                            href="dashboard.php"
                            class="inline-flex items-center gap-2 text-stone-200 hover:text-amber-300 font-semibold text-sm px-3 py-2.5 rounded-xl hover:bg-white/5 transition">

                            <i class="fa-solid fa-arrow-left text-xs"></i>

                            <span class="hidden sm:inline">Dashboard</span>

                        </a>

                        <a
                            href="logout.php"
                            onclick="return confirm('Yakin ingin keluar dari Webmaster?')"
                            class="inline-flex items-center gap-2 bg-white/10 hover:bg-white/15 border border-white/10 text-white font-semibold text-sm px-3.5 py-2.5 rounded-xl transition">

                            <i class="fa-solid fa-right-from-bracket text-xs"></i>

                            <span class="hidden sm:inline">Keluar</span>

                        </a>

                    </nav>

                </div>

            </div>

        </div>

    </header>

    <main class="flex-1">

        <section class="relative overflow-hidden bg-[#542f1b] text-white">

            <div class="absolute inset-0 javanese-pattern opacity-70"></div>

            <div class="relative max-w-5xl mx-auto px-5 py-12 md:py-14">

                <div class="max-w-3xl">

                    <div class="inline-flex items-center gap-2 text-amber-300 text-xs font-bold uppercase tracking-[0.18em] mb-4">

                        <span class="w-8 h-px bg-amber-400"></span>

                        Akun Webmaster

                    </div>

                    <h1 class="text-4xl md:text-5xl font-black tracking-tight leading-[1.05]">

                        Profil Saya

                    </h1>

                    <p class="text-stone-300 text-sm md:text-base leading-relaxed mt-4 max-w-2xl">

                        Kelola informasi akun dan keamanan akses Webmaster Ngabar Yuk!.

                    </p>

                </div>

            </div>

        </section>

        <section class="max-w-5xl mx-auto px-5 -mt-6 relative z-10">

            <div class="bg-white border border-stone-200 rounded-3xl shadow-xl shadow-stone-900/5 overflow-hidden">

                <div class="p-6 md:p-8">

                    <div class="flex flex-col md:flex-row md:items-center gap-5">

                        <div class="w-16 h-16 rounded-2xl bg-amber-50 border border-amber-200 text-amber-800 flex items-center justify-center flex-shrink-0">

                            <i class="fa-solid fa-user text-2xl"></i>

                        </div>

                        <div class="flex-1">

                            <div class="flex flex-wrap items-center gap-2">

                                <h2 class="text-xl font-black text-stone-900">

                                    <?= htmlspecialchars($user['nama']); ?>

                                </h2>

                                <?php if ($role === 'super_admin'): ?>

                                    <span class="inline-flex items-center gap-1.5 bg-amber-50 border border-amber-200 text-amber-900 px-2.5 py-1 rounded-lg text-[10px] font-bold">

                                        <i class="fa-solid fa-crown text-amber-700"></i>

                                        Super Admin

                                    </span>

                                <?php else: ?>

                                    <span class="inline-flex items-center gap-1.5 bg-stone-100 border border-stone-200 text-stone-700 px-2.5 py-1 rounded-lg text-[10px] font-bold">

                                        <i class="fa-solid fa-user-gear text-stone-500"></i>

                                        Admin

                                    </span>

                                <?php endif; ?>

                            </div>

                            <p class="text-sm text-stone-500 mt-1">

                                @<?= htmlspecialchars($user['username']); ?>

                            </p>

                        </div>

                        <div>

                            <?php if ($user['status'] === 'aktif'): ?>

                                <span class="inline-flex items-center gap-2 bg-emerald-50 border border-emerald-200 text-emerald-700 px-3 py-2 rounded-xl text-xs font-bold">

                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>

                                    Aktif

                                </span>

                            <?php else: ?>

                                <span class="inline-flex items-center gap-2 bg-stone-100 border border-stone-200 text-stone-600 px-3 py-2 rounded-xl text-xs font-bold">

                                    <span class="w-1.5 h-1.5 rounded-full bg-stone-400"></span>

                                    Nonaktif

                                </span>

                            <?php endif; ?>

                        </div>

                    </div>

                </div>

            </div>

        </section>

        <section class="max-w-5xl mx-auto px-5 py-10 md:py-12">

            <?php if ($success !== ''): ?>

                <div class="mb-6 flex items-start gap-3 bg-emerald-50 border border-emerald-200 rounded-2xl px-5 py-4">

                    <div class="w-8 h-8 rounded-lg bg-emerald-100 text-emerald-700 flex items-center justify-center flex-shrink-0">

                        <i class="fa-solid fa-check text-sm"></i>

                    </div>

                    <div>

                        <p class="text-sm font-bold text-emerald-900">

                            Berhasil

                        </p>

                        <p class="text-xs text-emerald-700 mt-0.5">

                            <?= htmlspecialchars($success); ?>

                        </p>

                    </div>

                </div>

            <?php endif; ?>

            <?php if ($error !== ''): ?>

                <div class="mb-6 flex items-start gap-3 bg-red-50 border border-red-200 rounded-2xl px-5 py-4">

                    <div class="w-8 h-8 rounded-lg bg-red-100 text-red-700 flex items-center justify-center flex-shrink-0">

                        <i class="fa-solid fa-triangle-exclamation text-sm"></i>

                    </div>

                    <div>

                        <p class="text-sm font-bold text-red-900">

                            Terjadi kesalahan

                        </p>

                        <p class="text-xs text-red-700 mt-0.5">

                            <?= htmlspecialchars($error); ?>

                        </p>

                    </div>

                </div>

            <?php endif; ?>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

                <div class="lg:col-span-2 bg-white border border-stone-200 rounded-3xl shadow-sm overflow-hidden">

                    <div class="px-6 md:px-7 py-5 border-b border-stone-100">

                        <div class="flex items-center gap-3">

                            <div class="w-10 h-10 rounded-xl bg-amber-50 border border-amber-200 text-amber-800 flex items-center justify-center">

                                <i class="fa-solid fa-id-card"></i>

                            </div>

                            <div>

                                <h2 class="font-black text-stone-900">

                                    Informasi Profil

                                </h2>

                                <p class="text-xs text-stone-500 mt-0.5">

                                    Perbarui informasi dasar akun Anda.

                                </p>

                            </div>

                        </div>

                    </div>

                    <form method="POST" class="p-6 md:p-7">

                        <input type="hidden" name="action" value="update_profile">

                        <div class="space-y-5">

                            <div>

                                <label
                                    for="nama"
                                    class="block text-sm font-bold text-stone-700 mb-2">

                                    Nama

                                </label>

                                <input
                                    type="text"
                                    id="nama"
                                    name="nama"
                                    value="<?= htmlspecialchars($user['nama']); ?>"
                                    minlength="3"
                                    required
                                    class="w-full px-4 py-3 rounded-xl border border-stone-200 bg-stone-50 text-sm text-stone-900 placeholder-stone-400 focus:border-amber-400 focus:ring-4 focus:ring-amber-100 transition">

                            </div>

                            <div>

                                <label
                                    for="username"
                                    class="block text-sm font-bold text-stone-700 mb-2">

                                    Username

                                </label>

                                <div class="relative">

                                    <span class="absolute left-4 top-1/2 -translate-y-1/2 text-stone-400 text-sm">

                                        @

                                    </span>

                                    <input
                                        type="text"
                                        id="username"
                                        value="<?= htmlspecialchars($user['username']); ?>"
                                        disabled
                                        class="w-full pl-9 pr-4 py-3 rounded-xl border border-stone-200 bg-stone-100 text-sm text-stone-500 cursor-not-allowed">

                                </div>

                                <p class="text-[11px] text-stone-400 mt-2">

                                    Username digunakan sebagai identitas login dan tidak dapat diubah.

                                </p>

                            </div>

                            <div class="pt-1">

                                <button
                                    type="submit"
                                    class="inline-flex items-center justify-center gap-2 bg-[#542f1b] hover:bg-[#432515] text-white font-bold text-sm px-5 py-3 rounded-xl transition shadow-sm">

                                    <i class="fa-solid fa-floppy-disk"></i>

                                    Simpan Perubahan

                                </button>

                            </div>

                        </div>

                    </form>

                </div>

                <div class="bg-white border border-stone-200 rounded-3xl shadow-sm overflow-hidden">

                    <div class="px-6 py-5 border-b border-stone-100">

                        <div class="flex items-center gap-3">

                            <div class="w-10 h-10 rounded-xl bg-stone-100 border border-stone-200 text-stone-700 flex items-center justify-center">

                                <i class="fa-solid fa-circle-info"></i>

                            </div>

                            <div>

                                <h2 class="font-black text-stone-900">

                                    Detail Akun

                                </h2>

                                <p class="text-xs text-stone-500 mt-0.5">

                                    Informasi akun saat ini.

                                </p>

                            </div>

                        </div>

                    </div>

                    <div class="p-6">

                        <div class="space-y-5">

                            <div>

                                <p class="text-[10px] uppercase tracking-[0.14em] font-bold text-stone-400">

                                    Role

                                </p>

                                <p class="text-sm font-bold text-stone-800 mt-1">

                                    <?= htmlspecialchars($roleLabel); ?>

                                </p>

                            </div>

                            <div>

                                <p class="text-[10px] uppercase tracking-[0.14em] font-bold text-stone-400">

                                    Status

                                </p>

                                <p class="text-sm font-bold text-stone-800 mt-1">

                                    <?= htmlspecialchars(ucfirst($user['status'])); ?>

                                </p>

                            </div>

                            <div>

                                <p class="text-[10px] uppercase tracking-[0.14em] font-bold text-stone-400">

                                    Akun dibuat

                                </p>

                                <p class="text-sm font-bold text-stone-800 mt-1">

                                    <?= date('d M Y, H:i', strtotime($user['created_at'])); ?>

                                </p>

                            </div>

                        </div>

                    </div>

                </div>

            </div>

            <div class="mt-6 bg-white border border-stone-200 rounded-3xl shadow-sm overflow-hidden">

                <div class="px-6 md:px-7 py-5 border-b border-stone-100">

                    <div class="flex items-center gap-3">

                        <div class="w-10 h-10 rounded-xl bg-red-50 border border-red-200 text-red-700 flex items-center justify-center">

                            <i class="fa-solid fa-lock"></i>

                        </div>

                        <div>

                            <h2 class="font-black text-stone-900">

                                Keamanan Akun

                            </h2>

                            <p class="text-xs text-stone-500 mt-0.5">

                                Gunakan password yang kuat dan jangan membagikannya kepada orang lain.

                            </p>

                        </div>

                    </div>

                </div>

                <form method="POST" class="p-6 md:p-7">

                    <input type="hidden" name="action" value="update_password">

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-5">

                        <div>

                            <label
                                for="password_lama"
                                class="block text-sm font-bold text-stone-700 mb-2">

                                Password Lama

                            </label>

                            <div class="relative">

                                <input
                                    type="password"
                                    id="password_lama"
                                    name="password_lama"
                                    required
                                    class="w-full px-4 pr-11 py-3 rounded-xl border border-stone-200 bg-stone-50 text-sm text-stone-900 focus:border-amber-400 focus:ring-4 focus:ring-amber-100 transition">

                                <button
                                    type="button"
                                    onclick="togglePassword('password_lama', this)"
                                    class="absolute right-3 top-1/2 -translate-y-1/2 text-stone-400 hover:text-stone-700">

                                    <i class="fa-regular fa-eye"></i>

                                </button>

                            </div>

                        </div>

                        <div>

                            <label
                                for="password_baru"
                                class="block text-sm font-bold text-stone-700 mb-2">

                                Password Baru

                            </label>

                            <div class="relative">

                                <input
                                    type="password"
                                    id="password_baru"
                                    name="password_baru"
                                    minlength="8"
                                    required
                                    class="w-full px-4 pr-11 py-3 rounded-xl border border-stone-200 bg-stone-50 text-sm text-stone-900 focus:border-amber-400 focus:ring-4 focus:ring-amber-100 transition">

                                <button
                                    type="button"
                                    onclick="togglePassword('password_baru', this)"
                                    class="absolute right-3 top-1/2 -translate-y-1/2 text-stone-400 hover:text-stone-700">

                                    <i class="fa-regular fa-eye"></i>

                                </button>

                            </div>

                            <p class="text-[11px] text-stone-400 mt-2">

                                Minimal 8 karakter.

                            </p>

                        </div>

                        <div>

                            <label
                                for="konfirmasi_password"
                                class="block text-sm font-bold text-stone-700 mb-2">

                                Konfirmasi Password

                            </label>

                            <div class="relative">

                                <input
                                    type="password"
                                    id="konfirmasi_password"
                                    name="konfirmasi_password"
                                    minlength="8"
                                    required
                                    class="w-full px-4 pr-11 py-3 rounded-xl border border-stone-200 bg-stone-50 text-sm text-stone-900 focus:border-amber-400 focus:ring-4 focus:ring-amber-100 transition">

                                <button
                                    type="button"
                                    onclick="togglePassword('konfirmasi_password', this)"
                                    class="absolute right-3 top-1/2 -translate-y-1/2 text-stone-400 hover:text-stone-700">

                                    <i class="fa-regular fa-eye"></i>

                                </button>

                            </div>

                        </div>

                    </div>

                    <div class="mt-6">

                        <button
                            type="submit"
                            class="inline-flex items-center justify-center gap-2 bg-[#542f1b] hover:bg-[#432515] text-white font-bold text-sm px-5 py-3 rounded-xl transition shadow-sm">

                            <i class="fa-solid fa-key"></i>

                            Ubah Password

                        </button>

                    </div>

                </form>

            </div>

            <div class="mt-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">

                <a
                    href="dashboard.php"
                    class="inline-flex items-center gap-2 text-sm font-semibold text-stone-600 hover:text-[#542f1b] transition">

                    <i class="fa-solid fa-arrow-left text-xs"></i>

                    Kembali ke Dashboard

                </a>

                <a
                    href="logout.php"
                    onclick="return confirm('Yakin ingin keluar dari Webmaster?')"
                    class="inline-flex items-center gap-2 text-sm font-semibold text-red-600 hover:text-red-700 transition">

                    <i class="fa-solid fa-right-from-bracket text-xs"></i>

                    Keluar dari Webmaster

                </a>

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
                        href="index.php"
                        class="hover:text-amber-300 transition">

                        Webmaster

                    </a>

                    <a
                        href="dashboard.php"
                        class="hover:text-amber-300 transition">

                        Dashboard

                    </a>

                    <a
                        href="profile.php"
                        class="text-amber-300">

                        Profil

                    </a>

                    <a
                        href="../index.php"
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

    <script>
        function togglePassword(id, button) {
            const input = document.getElementById(id);
            const icon = button.querySelector('i');

            if (input.type === 'password') {
                input.type = 'text';
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            } else {
                input.type = 'password';
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }
        }
    </script>

</body>

</html>