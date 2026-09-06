<?php

session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit;
}

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'super_admin') {
    header("Location: ../dashboard.php");
    exit;
}

require_once '../../config.php';

$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$status = isset($_GET['status']) ? trim($_GET['status']) : '';

$query = "SELECT id, nama, username, role, status, created_at FROM users WHERE role = 'admin'";

$params = [];
$types = "";

if (!empty($search)) {
    $query .= " AND (nama LIKE ? OR username LIKE ?)";
    $searchTerm = "%" . $search . "%";
    $params[] = $searchTerm;
    $params[] = $searchTerm;
    $types .= "ss";
}

if (!empty($status) && in_array($status, ['aktif', 'nonaktif'])) {
    $query .= " AND status = ?";
    $params[] = $status;
    $types .= "s";
}

$query .= " ORDER BY id DESC";

$stmt = mysqli_prepare($conn, $query);

if (!empty($params)) {
    mysqli_stmt_bind_param($stmt, $types, ...$params);
}

mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

$admins = [];

while ($row = mysqli_fetch_assoc($result)) {
    $admins[] = $row;
}

mysqli_stmt_close($stmt);

$totalAdminQuery = mysqli_query(
    $conn,
    "SELECT COUNT(*) AS total FROM users WHERE role = 'admin'"
);

$totalAdmin = 0;

if ($totalAdminQuery) {
    $totalAdminData = mysqli_fetch_assoc($totalAdminQuery);
    $totalAdmin = (int) $totalAdminData['total'];
}

$aktifAdminQuery = mysqli_query(
    $conn,
    "SELECT COUNT(*) AS total FROM users WHERE role = 'admin' AND status = 'aktif'"
);

$aktifAdmin = 0;

if ($aktifAdminQuery) {
    $aktifAdminData = mysqli_fetch_assoc($aktifAdminQuery);
    $aktifAdmin = (int) $aktifAdminData['total'];
}

$nonaktifAdmin = $totalAdmin - $aktifAdmin;

?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Kelola Admin - Ngabar Yuk! Webmaster</title>

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

        .table-row {
            transition:
                background-color 180ms ease,
                transform 180ms ease;
        }

        .table-row:hover {
            background-color: rgb(250 250 249);
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

<body class="bg-stone-50 text-stone-800 font-sans antialiased min-h-screen selection:bg-amber-200 selection:text-amber-900">

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
                            class="inline-flex items-center gap-2 text-stone-200 hover:text-amber-300 font-semibold text-sm px-3 py-2.5 rounded-xl hover:bg-white/5 transition">

                            <i class="fa-solid fa-gauge-high text-xs"></i>

                            <span class="hidden sm:inline">
                                Dashboard
                            </span>

                        </a>

                        <a href="../profile.php"
                            class="hidden sm:inline-flex items-center gap-2 text-stone-200 hover:text-amber-300 font-semibold text-sm px-3 py-2.5 rounded-xl hover:bg-white/5 transition">

                            <i class="fa-solid fa-user text-xs"></i>

                            Profil

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

    <main class="max-w-5xl w-full mx-auto px-5 py-8 md:py-10">

        <div class="mb-7">

            <div class="flex flex-col md:flex-row md:items-end md:justify-between gap-5">

                <div>

                    <div class="inline-flex items-center gap-2 text-amber-800 bg-amber-50 border border-amber-100 px-3 py-1.5 rounded-full text-[10px] font-bold uppercase tracking-[0.15em] mb-3">

                        <i class="fa-solid fa-users-gear"></i>

                        Manajemen Pengguna

                    </div>

                    <h1 class="text-2xl md:text-3xl font-black text-[#542f1b] tracking-tight">
                        Kelola Admin
                    </h1>

                    <p class="text-sm text-stone-500 mt-2 leading-relaxed max-w-2xl">
                        Kelola akun Admin yang memiliki akses untuk mengatur dan menerbitkan kabar di Ngabar Yuk!
                    </p>

                </div>

                <a href="create.php"
                    class="inline-flex items-center justify-center gap-2 bg-[#542f1b] hover:bg-[#452515] text-white font-bold text-sm px-4 py-3 rounded-xl transition shadow-sm hover:shadow-md">

                    <i class="fa-solid fa-user-plus text-xs"></i>

                    Tambah Admin

                </a>

            </div>

        </div>

        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-6">

            <div class="bg-white border border-stone-200 rounded-2xl p-5 shadow-sm">

                <div class="flex items-start justify-between gap-4">

                    <div>

                        <p class="text-[10px] uppercase tracking-[0.14em] font-bold text-stone-400">
                            Total Admin
                        </p>

                        <p class="text-2xl font-black text-[#542f1b] mt-1">
                            <?= $totalAdmin; ?>
                        </p>

                    </div>

                    <div class="w-10 h-10 rounded-xl bg-amber-50 text-amber-700 flex items-center justify-center">
                        <i class="fa-solid fa-users"></i>
                    </div>

                </div>

            </div>

            <div class="bg-white border border-stone-200 rounded-2xl p-5 shadow-sm">

                <div class="flex items-start justify-between gap-4">

                    <div>

                        <p class="text-[10px] uppercase tracking-[0.14em] font-bold text-stone-400">
                            Admin Aktif
                        </p>

                        <p class="text-2xl font-black text-emerald-700 mt-1">
                            <?= $aktifAdmin; ?>
                        </p>

                    </div>

                    <div class="w-10 h-10 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center">
                        <i class="fa-solid fa-user-check"></i>
                    </div>

                </div>

            </div>

            <div class="bg-white border border-stone-200 rounded-2xl p-5 shadow-sm">

                <div class="flex items-start justify-between gap-4">

                    <div>

                        <p class="text-[10px] uppercase tracking-[0.14em] font-bold text-stone-400">
                            Nonaktif
                        </p>

                        <p class="text-2xl font-black text-stone-600 mt-1">
                            <?= $nonaktifAdmin; ?>
                        </p>

                    </div>

                    <div class="w-10 h-10 rounded-xl bg-stone-100 text-stone-500 flex items-center justify-center">
                        <i class="fa-solid fa-user-slash"></i>
                    </div>

                </div>

            </div>

        </div>

        <div class="bg-white border border-stone-200 rounded-3xl shadow-sm overflow-hidden">

            <div class="px-5 md:px-6 py-5 border-b border-stone-100">

                <form method="GET"
                    class="flex flex-col md:flex-row gap-3">

                    <div class="relative flex-1">

                        <i class="fa-solid fa-magnifying-glass absolute left-4 top-1/2 -translate-y-1/2 text-xs text-stone-400 pointer-events-none"></i>

                        <input type="text"
                            name="search"
                            value="<?= htmlspecialchars($search); ?>"
                            placeholder="Cari nama atau username..."
                            class="w-full pl-10 pr-4 py-3 bg-stone-50 border border-stone-200 rounded-xl text-sm text-stone-800 placeholder:text-stone-400 focus:outline-none focus:border-amber-600 focus:ring-4 focus:ring-amber-600/10">

                    </div>

                    <div class="relative md:w-44">

                        <select name="status"
                            class="appearance-none w-full px-4 py-3 pr-10 bg-stone-50 border border-stone-200 rounded-xl text-sm text-stone-700 focus:outline-none focus:border-amber-600 focus:ring-4 focus:ring-amber-600/10 cursor-pointer">

                            <option value="">
                                Semua Status
                            </option>

                            <option value="aktif" <?= $status === 'aktif' ? 'selected' : ''; ?>>
                                Aktif
                            </option>

                            <option value="nonaktif" <?= $status === 'nonaktif' ? 'selected' : ''; ?>>
                                Nonaktif
                            </option>

                        </select>

                        <i class="fa-solid fa-chevron-down absolute right-4 top-1/2 -translate-y-1/2 text-[10px] text-stone-400 pointer-events-none"></i>

                    </div>

                    <button type="submit"
                        class="inline-flex items-center justify-center gap-2 px-5 py-3 bg-stone-800 hover:bg-stone-900 text-white rounded-xl font-semibold text-sm transition">

                        <i class="fa-solid fa-filter text-xs"></i>

                        Filter

                    </button>

                    <?php if (!empty($search) || !empty($status)): ?>

                        <a href="index.php"
                            class="inline-flex items-center justify-center gap-2 px-4 py-3 border border-stone-200 bg-white hover:bg-stone-50 text-stone-600 rounded-xl font-semibold text-sm transition">

                            <i class="fa-solid fa-rotate-left text-xs"></i>

                            Reset

                        </a>

                    <?php endif; ?>

                </form>

            </div>

            <?php if (empty($admins)): ?>

                <div class="px-6 py-16 text-center">

                    <div class="w-14 h-14 mx-auto rounded-2xl bg-stone-100 text-stone-400 flex items-center justify-center">

                        <i class="fa-solid fa-user-slash text-xl"></i>

                    </div>

                    <h2 class="text-base font-black text-stone-700 mt-4">
                        Tidak ada Admin ditemukan
                    </h2>

                    <p class="text-xs text-stone-400 mt-1 max-w-sm mx-auto leading-relaxed">

                        <?php if (!empty($search) || !empty($status)): ?>

                            Coba ubah kata pencarian atau filter status.

                        <?php else: ?>

                            Belum ada akun Admin yang tersedia.

                        <?php endif; ?>

                    </p>

                    <?php if (!empty($search) || !empty($status)): ?>

                        <a href="index.php"
                            class="inline-flex items-center gap-2 mt-5 px-4 py-2.5 bg-[#542f1b] hover:bg-[#452515] text-white rounded-xl text-xs font-bold transition">

                            <i class="fa-solid fa-rotate-left"></i>

                            Tampilkan Semua

                        </a>

                    <?php endif; ?>

                </div>

            <?php else: ?>

                <div class="hidden md:block overflow-x-auto">

                    <table class="w-full">

                        <thead>

                            <tr class="bg-stone-50/70 border-b border-stone-100">

                                <th class="text-left px-6 py-4 text-[10px] uppercase tracking-[0.14em] font-bold text-stone-400">
                                    Admin
                                </th>

                                <th class="text-left px-4 py-4 text-[10px] uppercase tracking-[0.14em] font-bold text-stone-400">
                                    Username
                                </th>

                                <th class="text-left px-4 py-4 text-[10px] uppercase tracking-[0.14em] font-bold text-stone-400">
                                    Status
                                </th>

                                <th class="text-left px-4 py-4 text-[10px] uppercase tracking-[0.14em] font-bold text-stone-400">
                                    Dibuat
                                </th>

                                <th class="text-right px-6 py-4 text-[10px] uppercase tracking-[0.14em] font-bold text-stone-400">
                                    Aksi
                                </th>

                            </tr>

                        </thead>

                        <tbody class="divide-y divide-stone-100">

                            <?php foreach ($admins as $admin): ?>

                                <tr class="table-row">

                                    <td class="px-6 py-4">

                                        <div class="flex items-center gap-3">

                                            <div class="w-10 h-10 shrink-0 rounded-xl bg-amber-50 text-amber-800 flex items-center justify-center font-black text-sm">

                                                <?= strtoupper(substr($admin['nama'], 0, 1)); ?>

                                            </div>

                                            <div class="min-w-0">

                                                <p class="text-sm font-bold text-stone-800 truncate">
                                                    <?= htmlspecialchars($admin['nama']); ?>
                                                </p>

                                                <p class="text-[11px] text-stone-400">
                                                    Admin
                                                </p>

                                            </div>

                                        </div>

                                    </td>

                                    <td class="px-4 py-4">

                                        <span class="text-sm text-stone-600">
                                            @<?= htmlspecialchars($admin['username']); ?>
                                        </span>

                                    </td>

                                    <td class="px-4 py-4">

                                        <?php if ($admin['status'] === 'aktif'): ?>

                                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1.5 rounded-lg bg-emerald-50 text-emerald-700 text-[10px] font-bold">

                                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>

                                                Aktif

                                            </span>

                                        <?php else: ?>

                                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1.5 rounded-lg bg-stone-100 text-stone-500 text-[10px] font-bold">

                                                <span class="w-1.5 h-1.5 rounded-full bg-stone-400"></span>

                                                Nonaktif

                                            </span>

                                        <?php endif; ?>

                                    </td>

                                    <td class="px-4 py-4">

                                        <span class="text-xs text-stone-500">
                                            <?= date('d M Y', strtotime($admin['created_at'])); ?>
                                        </span>

                                    </td>

                                    <td class="px-6 py-4">

                                        <div class="flex items-center justify-end gap-2">

                                            <a href="update.php?id=<?= (int)$admin['id']; ?>"
                                                class="w-9 h-9 rounded-xl border border-stone-200 bg-white text-stone-500 hover:text-amber-700 hover:border-amber-200 hover:bg-amber-50 flex items-center justify-center transition"
                                                title="Edit Admin">

                                                <i class="fa-solid fa-pen-to-square text-xs"></i>

                                            </a>

                                            <a href="delete.php?id=<?= (int)$admin['id']; ?>"
                                                class="w-9 h-9 rounded-xl border border-stone-200 bg-white text-stone-500 hover:text-rose-700 hover:border-rose-200 hover:bg-rose-50 flex items-center justify-center transition"
                                                title="Hapus Admin">

                                                <i class="fa-solid fa-trash text-xs"></i>

                                            </a>

                                        </div>

                                    </td>

                                </tr>

                            <?php endforeach; ?>

                        </tbody>

                    </table>

                </div>

                <div class="md:hidden divide-y divide-stone-100">

                    <?php foreach ($admins as $admin): ?>

                        <div class="p-5">

                            <div class="flex items-start gap-3">

                                <div class="w-11 h-11 shrink-0 rounded-xl bg-amber-50 text-amber-800 flex items-center justify-center font-black">

                                    <?= strtoupper(substr($admin['nama'], 0, 1)); ?>

                                </div>

                                <div class="min-w-0 flex-1">

                                    <div class="flex items-start justify-between gap-3">

                                        <div class="min-w-0">

                                            <p class="text-sm font-black text-stone-800 truncate">

                                                <?= htmlspecialchars($admin['nama']); ?>

                                            </p>

                                            <p class="text-xs text-stone-400 mt-0.5">

                                                @<?= htmlspecialchars($admin['username']); ?>

                                            </p>

                                        </div>

                                        <?php if ($admin['status'] === 'aktif'): ?>

                                            <span class="shrink-0 inline-flex items-center gap-1.5 px-2 py-1 rounded-lg bg-emerald-50 text-emerald-700 text-[9px] font-bold">

                                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>

                                                Aktif

                                            </span>

                                        <?php else: ?>

                                            <span class="shrink-0 inline-flex items-center gap-1.5 px-2 py-1 rounded-lg bg-stone-100 text-stone-500 text-[9px] font-bold">

                                                <span class="w-1.5 h-1.5 rounded-full bg-stone-400"></span>

                                                Nonaktif

                                            </span>

                                        <?php endif; ?>

                                    </div>

                                    <div class="flex items-center justify-between mt-4">

                                        <p class="text-[11px] text-stone-400">

                                            Dibuat <?= date('d M Y', strtotime($admin['created_at'])); ?>

                                        </p>

                                        <div class="flex items-center gap-2">

                                            <a href="update.php?id=<?= (int)$admin['id']; ?>"
                                                class="inline-flex items-center gap-1.5 px-3 py-2 rounded-lg border border-stone-200 text-stone-600 hover:text-amber-700 hover:bg-amber-50 hover:border-amber-200 text-[10px] font-bold transition">

                                                <i class="fa-solid fa-pen-to-square"></i>

                                                Edit

                                            </a>

                                            <a href="delete.php?id=<?= (int)$admin['id']; ?>"
                                                class="inline-flex items-center gap-1.5 px-3 py-2 rounded-lg border border-stone-200 text-stone-600 hover:text-rose-700 hover:bg-rose-50 hover:border-rose-200 text-[10px] font-bold transition">

                                                <i class="fa-solid fa-trash"></i>

                                                Hapus

                                            </a>

                                        </div>

                                    </div>

                                </div>

                            </div>

                        </div>

                    <?php endforeach; ?>

                </div>

            <?php endif; ?>

            <?php if (!empty($admins)): ?>

                <div class="px-5 md:px-6 py-4 border-t border-stone-100 bg-stone-50/50 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">

                    <p class="text-[11px] text-stone-400">

                        Menampilkan
                        <span class="font-bold text-stone-600">
                            <?= count($admins); ?>
                        </span>
                        akun Admin

                        <?php if (!empty($search) || !empty($status)): ?>
                            berdasarkan filter yang dipilih.
                        <?php else: ?>
                            terdaftar.
                        <?php endif; ?>

                    </p>

                    <a href="../../index.php"
                        class="inline-flex items-center gap-1.5 text-[11px] font-semibold text-stone-500 hover:text-amber-700 transition">

                        <i class="fa-solid fa-arrow-up-right-from-square text-[9px]"></i>

                        Lihat situs publik

                    </a>

                </div>

            <?php endif; ?>

        </div>

    </main>

    <footer class="bg-[#3a2113] text-stone-300 mt-8">

        <div class="max-w-5xl mx-auto px-5">

            <div class="py-8 flex flex-col md:flex-row md:items-center md:justify-between gap-7">

                <div>

                    <a href="../dashboard.php" class="inline-flex items-center gap-2.5 text-white group">

                        <span class="w-8 h-8 rounded-lg bg-amber-400 text-[#542f1b] flex items-center justify-center group-hover:-rotate-3 transition-transform">

                            <i class="fa-solid fa-mug-hot text-sm"></i>

                        </span>

                        <span class="font-black text-lg tracking-tight">

                            Ngabar
                            <span class="text-amber-400">Yuk!</span>

                        </span>

                    </a>

                    <p class="text-xs text-stone-400 mt-2 max-w-sm leading-relaxed">

                        Panel pengelolaan kabar dan administrasi
                        Webmaster Ngabar Yuk!

                    </p>

                </div>

                <div class="flex flex-col sm:flex-row sm:items-center gap-5">

                    <div class="flex flex-wrap items-center gap-x-5 gap-y-2 text-xs">

                        <a href="../dashboard.php"
                            class="hover:text-amber-300 transition">

                            Dashboard

                        </a>

                        <a href="../profile.php"
                            class="hover:text-amber-300 transition">

                            Profil

                        </a>

                        <a href="../berita/index.php"
                            class="hover:text-amber-300 transition">

                            Berita

                        </a>

                    </div>

                    <a href="../../index.php"
                        class="inline-flex items-center justify-center gap-2 px-3.5 py-2 rounded-xl bg-white/5 border border-white/10 text-stone-200 text-xs font-semibold hover:bg-white/10 hover:text-amber-300 transition">

                        <i class="fa-solid fa-arrow-up-right-from-square text-[10px]"></i>

                        Lihat Situs

                    </a>

                </div>

            </div>

            <div class="border-t border-white/10 py-4 flex flex-col sm:flex-row justify-between items-center gap-2 text-center sm:text-left">

                <p class="text-[11px] text-stone-500">

                    © 2026 Ngabar Yuk! • Webmaster Panel

                </p>

                <p class="text-[11px] text-amber-500 font-medium">

                    Tugas Seleksi Divisi Webmaster

                </p>

            </div>

        </div>

    </footer>

</body>

</html>