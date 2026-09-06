<?php

session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: ../login.php');
    exit;
}

require_once '../../config.php';

$nama = $_SESSION['nama'];
$username = $_SESSION['username'];
$role = $_SESSION['role'];

$roleLabel = $role === 'super_admin' ? 'Super Admin' : 'Admin';

$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$kategori = isset($_GET['kategori']) ? trim($_GET['kategori']) : '';
$read_time = isset($_GET['read_time']) ? trim($_GET['read_time']) : '';

$query = "SELECT * FROM berita WHERE 1=1";
$params = [];
$types = "";

if (!empty($search)) {
    $query .= " AND (judul LIKE ? OR penulis LIKE ? OR konten LIKE ?)";
    $searchTerm = "%" . $search . "%";
    $params[] = $searchTerm;
    $params[] = $searchTerm;
    $params[] = $searchTerm;
    $types .= "sss";
}

if (!empty($kategori)) {
    $query .= " AND kategori = ?";
    $params[] = $kategori;
    $types .= "s";
}

if (!empty($read_time)) {
    if ($read_time === '1-3') {
        $query .= " AND read_time BETWEEN 1 AND 3";
    } elseif ($read_time === '4-6') {
        $query .= " AND read_time BETWEEN 4 AND 6";
    } elseif ($read_time === '7-9') {
        $query .= " AND read_time BETWEEN 7 AND 9";
    } elseif ($read_time === '10-12') {
        $query .= " AND read_time BETWEEN 10 AND 12";
    } elseif ($read_time === '13-15') {
        $query .= " AND read_time BETWEEN 13 AND 15";
    } elseif ($read_time === '15-plus') {
        $query .= " AND read_time > 15";
    }
}

$query .= " ORDER BY id DESC";

$stmt = mysqli_prepare($conn, $query);

if (!empty($params)) {
    mysqli_stmt_bind_param($stmt, $types, ...$params);
}

mysqli_stmt_execute($stmt);

$result = mysqli_stmt_get_result($stmt);

$jumlahHasil = mysqli_num_rows($result);

$totalQuery = mysqli_query(
    $conn,
    "SELECT COUNT(*) AS total FROM berita"
);

$totalData = $totalQuery
    ? mysqli_fetch_assoc($totalQuery)
    : ['total' => 0];

$totalBerita = (int) $totalData['total'];

$status = isset($_GET['status']) ? $_GET['status'] : '';

?>

<!DOCTYPE html>
<html lang="id">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Kelola Berita - Webmaster Ngabar Yuk!</title>

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
            background-position: 0 0, 0 0, 8px 14px, 8px 14px;
            background-size: 16px 28px;
        }

        .line-clamp-2 {
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }
    </style>

</head>

<body class="bg-stone-50 text-stone-800 font-sans antialiased min-h-screen flex flex-col selection:bg-amber-200 selection:text-amber-900">

    <header class="sticky top-0 z-50">

        <div class="bg-[#542f1b] text-stone-100 shadow-lg shadow-stone-900/10 javanese-pattern">

            <div class="max-w-5xl mx-auto px-5">

                <div class="h-[72px] flex items-center justify-between">

                    <a href="../index.php" class="group flex items-center gap-3">

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
                            href="../dashboard.php"
                            class="inline-flex items-center gap-2 text-stone-200 hover:text-amber-300 font-semibold text-sm px-3 py-2.5 rounded-xl hover:bg-white/5 transition">

                            <i class="fa-solid fa-arrow-left text-xs"></i>

                            <span class="hidden sm:inline">Dashboard</span>

                        </a>

                        <a
                            href="create.php"
                            class="inline-flex items-center gap-2 bg-amber-400 hover:bg-amber-300 text-[#542f1b] font-bold text-sm px-3.5 py-2.5 rounded-xl transition shadow-sm">

                            <i class="fa-solid fa-pen-to-square text-xs"></i>

                            <span class="hidden sm:inline">Tulis Berita</span>

                        </a>

                    </nav>

                </div>

            </div>

        </div>

    </header>

    <main class="flex-1">

        <section class="relative overflow-hidden bg-[#542f1b] text-white">

            <div class="absolute inset-0 javanese-pattern opacity-70"></div>

            <div class="relative max-w-5xl mx-auto px-5 py-14 md:py-16">

                <div class="max-w-3xl">

                    <div class="inline-flex items-center gap-2 text-amber-300 text-xs font-bold uppercase tracking-[0.18em] mb-5">

                        <span class="w-8 h-px bg-amber-400"></span>

                        Manajemen Konten

                    </div>

                    <h1 class="text-4xl md:text-5xl font-black tracking-tight leading-[1.05]">

                        Kelola Berita

                    </h1>

                    <p class="text-stone-300 text-sm md:text-base leading-relaxed mt-5 max-w-2xl">

                        Kelola, cari, dan saring seluruh kabar yang diterbitkan melalui Ngabar Yuk!.

                    </p>

                </div>

            </div>

        </section>

        <section class="max-w-5xl mx-auto px-5 -mt-7 relative z-10">

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">

                <div class="bg-white border border-stone-200 rounded-2xl shadow-xl shadow-stone-900/5 p-5">

                    <div class="flex items-center justify-between gap-4">

                        <div>

                            <p class="text-[10px] uppercase tracking-[0.16em] text-stone-400 font-bold">

                                Total Berita

                            </p>

                            <p class="text-3xl font-black text-stone-900 mt-1">

                                <?= $totalBerita; ?>

                            </p>

                        </div>

                        <div class="w-11 h-11 rounded-xl bg-amber-50 border border-amber-200/70 text-amber-800 flex items-center justify-center">

                            <i class="fa-solid fa-newspaper"></i>

                        </div>

                    </div>

                </div>

                <div class="bg-white border border-stone-200 rounded-2xl shadow-xl shadow-stone-900/5 p-5">

                    <div class="flex items-center justify-between gap-4">

                        <div>

                            <p class="text-[10px] uppercase tracking-[0.16em] text-stone-400 font-bold">

                                Hasil Saat Ini

                            </p>

                            <p class="text-3xl font-black text-stone-900 mt-1">

                                <?= $jumlahHasil; ?>

                            </p>

                        </div>

                        <div class="w-11 h-11 rounded-xl bg-stone-100 border border-stone-200 text-stone-600 flex items-center justify-center">

                            <i class="fa-solid fa-filter"></i>

                        </div>

                    </div>

                </div>

            </div>

        </section>

        <section class="max-w-5xl mx-auto px-5 py-12 md:py-14">

            <form
                method="GET"
                action=""
                class="bg-white border border-stone-200 rounded-2xl shadow-sm p-3 md:p-4">

                <div class="flex flex-col md:flex-row gap-3">

                    <div class="relative flex-1">

                        <span class="absolute inset-y-0 left-0 flex items-center pl-4 text-stone-400">

                            <i class="fa-solid fa-magnifying-glass"></i>

                        </span>

                        <input
                            type="text"
                            name="search"
                            value="<?= htmlspecialchars($search); ?>"
                            placeholder="Cari judul, penulis, atau isi berita..."
                            class="w-full pl-11 pr-4 py-3 bg-stone-50 border border-stone-200 rounded-xl text-sm text-stone-800 placeholder:text-stone-400 focus:outline-none focus:ring-2 focus:ring-amber-600/20 focus:border-amber-600 transition">

                    </div>

                    <div class="w-full md:w-48">

                        <select
                            name="kategori"
                            class="w-full px-4 py-3 bg-stone-50 border border-stone-200 rounded-xl text-sm text-stone-700 focus:outline-none focus:ring-2 focus:ring-amber-600/20 focus:border-amber-600 transition">

                            <option value="">Semua Kategori</option>

                            <option value="Insight" <?= ($kategori === 'Insight') ? 'selected' : ''; ?>>

                                Insight / Opini

                            </option>

                            <option value="Lokal" <?= ($kategori === 'Lokal') ? 'selected' : ''; ?>>

                                Warta Lokal

                            </option>

                            <option value="Budaya" <?= ($kategori === 'Budaya') ? 'selected' : ''; ?>>

                                Budaya & Tradisi

                            </option>

                            <option value="Gaya Urip" <?= ($kategori === 'Gaya Urip') ? 'selected' : ''; ?>>

                                Gaya Urip

                            </option>

                        </select>

                    </div>

                    <div class="w-full md:w-48">

                        <select
                            name="read_time"
                            class="w-full px-4 py-3 bg-stone-50 border border-stone-200 rounded-xl text-sm text-stone-700 focus:outline-none focus:ring-2 focus:ring-amber-600/20 focus:border-amber-600 transition">

                            <option value="">Semua Durasi</option>

                            <option value="1-3" <?= ($read_time === '1-3') ? 'selected' : ''; ?>>

                                1–3 menit

                            </option>

                            <option value="4-6" <?= ($read_time === '4-6') ? 'selected' : ''; ?>>

                                4–6 menit

                            </option>

                            <option value="7-9" <?= ($read_time === '7-9') ? 'selected' : ''; ?>>

                                7–9 menit

                            </option>

                            <option value="10-12" <?= ($read_time === '10-12') ? 'selected' : ''; ?>>

                                10–12 menit

                            </option>

                            <option value="13-15" <?= ($read_time === '13-15') ? 'selected' : ''; ?>>

                                13–15 menit

                            </option>

                            <option value="15-plus" <?= ($read_time === '15-plus') ? 'selected' : ''; ?>>

                                Lebih dari 15 menit

                            </option>

                        </select>

                    </div>

                    <div class="flex gap-2">

                        <button
                            type="submit"
                            class="flex-1 md:flex-none inline-flex items-center justify-center gap-2 bg-[#542f1b] hover:bg-[#432515] text-white font-bold text-sm px-5 py-3 rounded-xl transition">

                            <i class="fa-solid fa-filter text-xs"></i>

                            Saring

                        </button>

                        <?php if (!empty($search) || !empty($kategori) || !empty($read_time)): ?>

                            <a
                                href="index.php"
                                class="inline-flex items-center justify-center w-11 bg-stone-100 hover:bg-stone-200 border border-stone-200 text-stone-600 rounded-xl transition"
                                title="Reset Filter">

                                <i class="fa-solid fa-rotate-right text-sm"></i>

                            </a>

                        <?php endif; ?>

                    </div>

                </div>

            </form>

        </section>

        <section class="max-w-5xl mx-auto px-5 pb-14">

            <div class="flex flex-col sm:flex-row sm:items-end justify-between gap-4 mb-7">

                <div>

                    <div class="flex items-center gap-2 text-amber-800 text-xs font-bold uppercase tracking-[0.16em] mb-2">

                        <span class="w-5 h-px bg-amber-600"></span>

                        Arsip Berita

                    </div>

                    <h2 class="text-2xl md:text-3xl font-black text-stone-900 tracking-tight">

                        Daftar Berita

                    </h2>

                    <p class="text-sm text-stone-500 mt-1.5">

                        Kelola berita yang tersedia di website.

                    </p>

                </div>

                <div class="flex items-center gap-2">

                    <span class="inline-flex items-center gap-2 bg-amber-50 border border-amber-200/70 text-amber-900 px-3 py-2 rounded-xl text-xs font-semibold">

                        <i class="fa-solid fa-newspaper text-amber-700"></i>

                        <?= $jumlahHasil; ?> berita

                    </span>

                </div>

            </div>

            <?php if ($jumlahHasil > 0): ?>

                <div class="bg-white border border-stone-200 rounded-2xl shadow-sm overflow-hidden">

                    <div class="hidden md:block overflow-x-auto">

                        <table class="w-full">

                            <thead>

                                <tr class="bg-stone-50 border-b border-stone-200">

                                    <th class="text-left px-6 py-4 text-[10px] uppercase tracking-[0.14em] font-bold text-stone-400">

                                        Berita

                                    </th>

                                    <th class="text-left px-4 py-4 text-[10px] uppercase tracking-[0.14em] font-bold text-stone-400">

                                        Kategori

                                    </th>

                                    <th class="text-left px-4 py-4 text-[10px] uppercase tracking-[0.14em] font-bold text-stone-400">

                                        Penulis

                                    </th>

                                    <th class="text-left px-4 py-4 text-[10px] uppercase tracking-[0.14em] font-bold text-stone-400">

                                        Durasi

                                    </th>

                                    <th class="text-left px-4 py-4 text-[10px] uppercase tracking-[0.14em] font-bold text-stone-400">

                                        Tanggal

                                    </th>

                                    <th class="text-right px-6 py-4 text-[10px] uppercase tracking-[0.14em] font-bold text-stone-400">

                                        Aksi

                                    </th>

                                </tr>

                            </thead>

                            <tbody class="divide-y divide-stone-100">

                                <?php while ($row = mysqli_fetch_assoc($result)): ?>

                                    <tr class="group hover:bg-stone-50/70 transition">

                                        <td class="px-6 py-4">

                                            <div class="flex items-center gap-4 min-w-[300px]">

                                                <div class="w-20 h-14 rounded-xl bg-stone-100 overflow-hidden flex-shrink-0 border border-stone-200">

                                                    <?php if (!empty($row['gambar'])): ?>

                                                        <img
                                                            src="../../assets/img/<?= htmlspecialchars($row['gambar']); ?>"
                                                            alt="<?= htmlspecialchars($row['judul']); ?>"
                                                            class="w-full h-full object-cover">

                                                    <?php else: ?>

                                                        <div class="w-full h-full flex items-center justify-center text-stone-300">

                                                            <i class="fa-solid fa-image"></i>

                                                        </div>

                                                    <?php endif; ?>

                                                </div>

                                                <div class="min-w-0">

                                                    <a
                                                        href="../../detail.php?id=<?= (int)$row['id']; ?>"
                                                        class="text-sm font-black text-stone-900 hover:text-[#542f1b] line-clamp-2 transition">

                                                        <?= htmlspecialchars($row['judul']); ?>

                                                    </a>

                                                </div>

                                            </div>

                                        </td>

                                        <td class="px-4 py-4">

                                            <span class="inline-flex items-center bg-amber-50 border border-amber-200/70 text-amber-900 px-2.5 py-1.5 rounded-lg text-[10px] font-bold whitespace-nowrap">

                                                <?= htmlspecialchars($row['kategori']); ?>

                                            </span>

                                        </td>

                                        <td class="px-4 py-4">

                                            <div class="flex items-center gap-2 min-w-[120px]">

                                                <div class="w-7 h-7 rounded-lg bg-stone-100 text-stone-500 flex items-center justify-center flex-shrink-0">

                                                    <i class="fa-solid fa-user-pen text-[10px]"></i>

                                                </div>

                                                <span class="text-xs font-semibold text-stone-600 truncate">

                                                    <?= htmlspecialchars($row['penulis']); ?>

                                                </span>

                                            </div>

                                        </td>

                                        <td class="px-4 py-4 whitespace-nowrap">

                                            <span class="inline-flex items-center gap-1.5 text-xs font-semibold text-stone-600">

                                                <i class="fa-regular fa-clock text-stone-400"></i>

                                                <?= (int)$row['read_time']; ?> menit

                                            </span>

                                        </td>

                                        <td class="px-4 py-4 whitespace-nowrap">

                                            <div>

                                                <p class="text-xs font-semibold text-stone-700">

                                                    <?= date('d M Y', strtotime($row['tanggal'])); ?>

                                                </p>

                                                <p class="text-[10px] text-stone-400 mt-0.5">

                                                    <?= date('H:i', strtotime($row['tanggal'])); ?>

                                                </p>

                                            </div>

                                        </td>

                                        <td class="px-6 py-4">

                                            <div class="flex items-center justify-end gap-1.5">

                                                <a
                                                    href="../../detail.php?id=<?= (int)$row['id']; ?>"
                                                    title="Lihat Berita"
                                                    class="w-9 h-9 inline-flex items-center justify-center bg-stone-100 hover:bg-stone-200 text-stone-600 rounded-lg transition">

                                                    <i class="fa-solid fa-eye text-xs"></i>

                                                </a>

                                                <a
                                                    href="update.php?id=<?= (int)$row['id']; ?>"
                                                    title="Ubah Berita"
                                                    class="w-9 h-9 inline-flex items-center justify-center bg-amber-50 hover:bg-amber-100 text-amber-700 rounded-lg transition">

                                                    <i class="fa-solid fa-pen-to-square text-xs"></i>

                                                </a>

                                                <a
                                                    href="delete.php?id=<?= (int)$row['id']; ?>"
                                                    title="Hapus Berita"
                                                    class="w-9 h-9 inline-flex items-center justify-center bg-red-50 hover:bg-red-100 text-red-600 rounded-lg transition">

                                                    <i class="fa-solid fa-trash-can text-xs"></i>

                                                </a>

                                            </div>

                                        </td>

                                    </tr>

                                <?php endwhile; ?>

                            </tbody>

                        </table>

                    </div>

                    <div class="md:hidden divide-y divide-stone-100">

                        <?php mysqli_data_seek($result, 0); ?>

                        <?php while ($row = mysqli_fetch_assoc($result)): ?>

                            <article class="p-5">

                                <div class="flex gap-4">

                                    <div class="w-24 h-20 rounded-xl bg-stone-100 overflow-hidden flex-shrink-0 border border-stone-200">

                                        <?php if (!empty($row['gambar'])): ?>

                                            <img
                                                src="../../assets/img/<?= htmlspecialchars($row['gambar']); ?>"
                                                alt="<?= htmlspecialchars($row['judul']); ?>"
                                                class="w-full h-full object-cover">

                                        <?php else: ?>

                                            <div class="w-full h-full flex items-center justify-center text-stone-300">

                                                <i class="fa-solid fa-image"></i>

                                            </div>

                                        <?php endif; ?>

                                    </div>

                                    <div class="flex-1 min-w-0">

                                        <span class="inline-flex bg-amber-50 border border-amber-200/70 text-amber-900 px-2 py-1 rounded-md text-[9px] font-bold mb-2">

                                            <?= htmlspecialchars($row['kategori']); ?>

                                        </span>

                                        <a
                                            href="../../detail.php?id=<?= (int)$row['id']; ?>"
                                            class="block text-sm font-black text-stone-900 line-clamp-2 hover:text-[#542f1b] transition">

                                            <?= htmlspecialchars($row['judul']); ?>

                                        </a>

                                    </div>

                                </div>

                                <div class="flex flex-wrap items-center gap-x-4 gap-y-2 mt-4 text-[11px] text-stone-400">

                                    <span class="flex items-center gap-1.5">

                                        <i class="fa-solid fa-user-pen"></i>

                                        <?= htmlspecialchars($row['penulis']); ?>

                                    </span>

                                    <span class="flex items-center gap-1.5">

                                        <i class="fa-regular fa-clock"></i>

                                        <?= (int)$row['read_time']; ?> menit baca

                                    </span>

                                    <span class="flex items-center gap-1.5">

                                        <i class="fa-regular fa-calendar"></i>

                                        <?= date('d M Y', strtotime($row['tanggal'])); ?>

                                    </span>

                                </div>

                                <div class="flex items-center gap-2 mt-4">

                                    <a
                                        href="../../detail.php?id=<?= (int)$row['id']; ?>"
                                        class="flex-1 inline-flex items-center justify-center gap-2 bg-stone-100 hover:bg-stone-200 text-stone-700 font-semibold text-xs py-2.5 rounded-lg transition">

                                        <i class="fa-solid fa-eye"></i>

                                        Lihat

                                    </a>

                                    <a
                                        href="update.php?id=<?= (int)$row['id']; ?>"
                                        class="flex-1 inline-flex items-center justify-center gap-2 bg-amber-50 hover:bg-amber-100 text-amber-700 font-semibold text-xs py-2.5 rounded-lg transition">

                                        <i class="fa-solid fa-pen-to-square"></i>

                                        Ubah

                                    </a>

                                    <a
                                        href="delete.php?id=<?= (int)$row['id']; ?>"
                                        class="flex-1 inline-flex items-center justify-center gap-2 bg-red-50 hover:bg-red-100 text-red-600 font-semibold text-xs py-2.5 rounded-lg transition">

                                        <i class="fa-solid fa-trash-can"></i>

                                        Hapus

                                    </a>

                                </div>

                            </article>

                        <?php endwhile; ?>

                    </div>

                </div>

            <?php else: ?>

                <div class="bg-white border border-stone-200 rounded-2xl p-10 md:p-14 text-center shadow-sm">

                    <div class="w-16 h-16 mx-auto rounded-2xl bg-amber-50 border border-amber-200/70 text-amber-800 flex items-center justify-center mb-5">

                        <i class="fa-solid fa-magnifying-glass text-xl"></i>

                    </div>

                    <?php if (!empty($search) || !empty($kategori) || !empty($read_time)): ?>

                        <p class="text-xs font-bold uppercase tracking-[0.16em] text-amber-800 mb-2">

                            Tidak Ada Hasil

                        </p>

                        <h3 class="text-xl font-black text-stone-900">

                            Berita yang dicari belum ditemukan

                        </h3>

                        <p class="text-sm text-stone-500 mt-2 max-w-md mx-auto leading-relaxed">

                            Coba gunakan kata kunci lain, pilih kategori yang berbeda, atau gunakan rentang waktu baca yang berbeda.

                        </p>

                        <a
                            href="index.php"
                            class="inline-flex items-center gap-2 mt-6 bg-stone-100 hover:bg-stone-200 border border-stone-200 text-stone-700 font-bold text-sm px-5 py-2.5 rounded-xl transition">

                            <i class="fa-solid fa-rotate-right text-xs"></i>

                            Reset Filter

                        </a>

                    <?php else: ?>

                        <p class="text-xs font-bold uppercase tracking-[0.16em] text-amber-800 mb-2">

                            Belum Ada Berita

                        </p>

                        <h3 class="text-xl font-black text-stone-900">

                            Belum ada berita yang tersedia

                        </h3>

                        <p class="text-sm text-stone-500 mt-2 max-w-md mx-auto leading-relaxed">

                            Ruang ini masih kosong. Mulai dengan membuat berita pertama.

                        </p>

                        <a
                            href="create.php"
                            class="inline-flex items-center gap-2 mt-6 bg-[#542f1b] hover:bg-[#432515] text-white font-bold text-sm px-5 py-2.5 rounded-xl transition shadow-sm">

                            <i class="fa-solid fa-pen-to-square text-xs"></i>

                            Tulis Berita

                        </a>

                    <?php endif; ?>

                </div>

            <?php endif; ?>

        </section>

    </main>

    <footer class="bg-[#3a2113] text-stone-300 mt-auto">

        <div class="max-w-5xl mx-auto px-5">

            <div class="py-8 flex flex-col md:flex-row md:items-center md:justify-between gap-6">

                <div>

                    <a href="../index.php" class="inline-flex items-center gap-2.5 text-white group">

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
                        class="text-amber-300">

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

    <?php if (!empty($status)): ?>

        <script>
            const urlParams = new URLSearchParams(window.location.search);
            const status = urlParams.get('status');

            const messages = {
                sukses: 'Berita baru berhasil dibuat.',
                update: 'Perubahan berita berhasil disimpan.',
                hapus: 'Berita berhasil dihapus.'
            };

            if (messages[status]) {

                const notification = document.createElement('div');

                notification.className = 'fixed bottom-5 right-5 left-5 sm:left-auto sm:max-w-sm bg-[#3a2113] text-white border border-white/10 rounded-xl shadow-2xl px-4 py-3 z-[100] flex items-start gap-3';

                notification.innerHTML = `
                    <div class="w-8 h-8 rounded-lg bg-emerald-500/15 text-emerald-400 flex items-center justify-center flex-shrink-0">
                        <i class="fa-solid fa-circle-check text-sm"></i>
                    </div>

                    <div class="flex-1">
                        <p class="text-sm font-semibold">${messages[status]}</p>
                    </div>

                    <button
                        type="button"
                        class="text-stone-400 hover:text-white transition px-1"
                        aria-label="Tutup">

                        <i class="fa-solid fa-xmark text-sm"></i>

                    </button>
                `;

                document.body.appendChild(notification);

                const closeButton = notification.querySelector('button');

                closeButton.addEventListener('click', function() {
                    notification.remove();
                });

                setTimeout(function() {

                    if (notification.parentNode) {
                        notification.remove();
                    }

                }, 4500);

                window.history.replaceState({},
                    document.title,
                    window.location.pathname
                );

            }
        </script>

    <?php endif; ?>

</body>

</html>