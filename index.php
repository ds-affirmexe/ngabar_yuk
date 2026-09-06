<?php

require_once 'config.php';

$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$kategori = isset($_GET['kategori']) ? trim($_GET['kategori']) : '';
$read_time = isset($_GET['read_time']) ? trim($_GET['read_time']) : '';

$query = "SELECT * FROM berita WHERE 1=1";

if (!empty($search)) {
    $searchEscaped = mysqli_real_escape_string($conn, $search);

    $query .= " AND (
        judul LIKE '%$searchEscaped%'
        OR penulis LIKE '%$searchEscaped%'
        OR konten LIKE '%$searchEscaped%'
    )";
}

if (!empty($kategori)) {
    $kategoriEscaped = mysqli_real_escape_string($conn, $kategori);

    $query .= " AND kategori = '$kategoriEscaped'";
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

$result = mysqli_query($conn, $query);

if (!$result) {
    die("Gagal mengambil data berita: " . mysqli_error($conn));
}

$articles = mysqli_fetch_all($result, MYSQLI_ASSOC);

$latestQuery = mysqli_query(
    $conn,
    "SELECT * FROM berita ORDER BY id DESC LIMIT 3"
);

if (!$latestQuery) {
    die("Gagal mengambil kabar terbaru: " . mysqli_error($conn));
}

$latestArticles = mysqli_fetch_all($latestQuery, MYSQLI_ASSOC);

$totalKabarQuery = mysqli_query(
    $conn,
    "SELECT COUNT(*) AS total FROM berita"
);

$totalKabar = 0;

if ($totalKabarQuery) {
    $totalKabarRow = mysqli_fetch_assoc($totalKabarQuery);
    $totalKabar = (int) $totalKabarRow['total'];
}

$totalKategoriQuery = mysqli_query(
    $conn,
    "SELECT COUNT(DISTINCT kategori) AS total FROM berita"
);

$totalKategori = 0;

if ($totalKategoriQuery) {
    $totalKategoriRow = mysqli_fetch_assoc($totalKategoriQuery);
    $totalKategori = (int) $totalKategoriRow['total'];
}

$totalPenulisQuery = mysqli_query(
    $conn,
    "SELECT COUNT(DISTINCT penulis) AS total FROM berita"
);

$totalPenulis = 0;

if ($totalPenulisQuery) {
    $totalPenulisRow = mysqli_fetch_assoc($totalPenulisQuery);
    $totalPenulis = (int) $totalPenulisRow['total'];
}

$categoryLabels = [
    'Insight' => 'Insight / Opini',
    'Lokal' => 'Warta Lokal',
    'Budaya' => 'Budaya & Tradisi',
    'Gaya Urip' => 'Gaya Urip'
];

?>

<!DOCTYPE html>
<html lang="id">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Ngabar Yuk! — Warta • Reriungan • Insight</title>

    <script src="https://cdn.tailwindcss.com"></script>

    <link
        rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        html {
            scroll-behavior: smooth;
        }

        body {
            overflow-x: hidden;
        }

        .javanese-pattern {
            background-image:
                linear-gradient(30deg, rgba(255, 255, 255, .025) 12%, transparent 12.5%, transparent 87%, rgba(255, 255, 255, .025) 87.5%, rgba(255, 255, 255, .025)),
                linear-gradient(150deg, rgba(255, 255, 255, .025) 12%, transparent 12.5%, transparent 87%, rgba(255, 255, 255, .025) 87.5%, rgba(255, 255, 255, .025)),
                linear-gradient(30deg, rgba(255, 255, 255, .025) 12%, transparent 12.5%, transparent 87%, rgba(255, 255, 255, .025) 87.5%, rgba(255, 255, 255, .025)),
                linear-gradient(150deg, rgba(255, 255, 255, .025) 12%, transparent 12.5%, transparent 87%, rgba(255, 255, 255, .025) 87.5%, rgba(255, 255, 255, .025));
            background-position:
                0 0,
                0 0,
                8px 14px,
                8px 14px;
            background-size: 16px 28px;
        }

        .article-preview {
            overflow: hidden;
        }

        .article-preview p {
            margin-bottom: .75rem;
        }

        .article-preview strong {
            font-weight: 800;
        }

        .article-preview em {
            font-style: italic;
        }

        .article-preview u {
            text-decoration: underline;
            text-underline-offset: 2px;
        }

        .article-preview h2,
        .article-preview h3,
        .article-preview h4 {
            font-weight: 800;
            color: #292524;
            line-height: 1.4;
            margin: .75rem 0 .5rem;
        }

        .article-preview h2 {
            font-size: 1.125rem;
        }

        .article-preview h3 {
            font-size: 1rem;
        }

        .article-preview h4 {
            font-size: .95rem;
        }

        .article-preview ul {
            list-style: disc;
            padding-left: 1.25rem;
            margin: .5rem 0;
        }

        .article-preview ol {
            list-style: decimal;
            padding-left: 1.25rem;
            margin: .5rem 0;
        }

        .article-preview li {
            margin-bottom: .25rem;
        }

        .article-preview blockquote {
            border-left: 3px solid #d97706;
            padding-left: .875rem;
            margin: .75rem 0;
            color: #78716c;
            font-style: italic;
        }

        .article-preview a {
            color: #92400e;
            text-decoration: underline;
            text-underline-offset: 2px;
        }

        .latest-card {
            transition:
                transform .2s ease,
                box-shadow .2s ease,
                border-color .2s ease;
        }

        .latest-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 18px 35px rgba(68, 44, 28, .09);
            border-color: #d6d3d1;
        }

        .latest-image {
            transition: transform .5s ease;
        }

        .latest-card:hover .latest-image {
            transform: scale(1.045);
        }

        .category-pill {
            transition:
                background-color .2s ease,
                border-color .2s ease,
                color .2s ease,
                transform .2s ease;
        }

        .category-pill:hover {
            transform: translateY(-1px);
        }

        .stat-card {
            transition:
                transform .2s ease,
                border-color .2s ease,
                box-shadow .2s ease;
        }

        .stat-card:hover {
            transform: translateY(-2px);
            border-color: #d6d3d1;
            box-shadow: 0 12px 25px rgba(68, 44, 28, .06);
        }

        .latest-title {
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }
    </style>

</head>

<body class="bg-stone-50 text-stone-800 antialiased min-h-screen flex flex-col selection:bg-amber-200 selection:text-amber-900">

    <?php include 'assets/header.php'; ?>

    <main class="flex-1">

        <section class="relative overflow-hidden bg-[#542f1b] text-white">

            <div class="absolute inset-0 javanese-pattern"></div>

            <div class="absolute -right-20 -top-20 w-72 h-72 rounded-full border border-amber-300/10"></div>

            <div class="absolute -right-8 -top-8 w-48 h-48 rounded-full border border-amber-300/10"></div>

            <div class="relative max-w-5xl mx-auto px-5 py-14 md:py-20">

                <div class="max-w-3xl">

                    <div class="inline-flex items-center gap-2 text-amber-300 text-xs font-bold uppercase tracking-[.18em] mb-5">

                        <span class="w-8 h-px bg-amber-400"></span>

                        Warta & Reriungan

                    </div>

                    <h1 class="text-4xl md:text-5xl lg:text-6xl font-black tracking-tight leading-[1.04]">

                        Apa kabar hari ini,

                        <span class="text-amber-400">
                            Lur?
                        </span>

                    </h1>

                    <p class="text-stone-300 text-sm md:text-base leading-relaxed mt-5 max-w-2xl">

                        Tempat sederhana untuk berbagi kabar, gagasan,
                        dan cerita yang layak direriungkan.
                        Baca yang menarik, temukan sudut pandang baru,
                        lalu ikut ngabar.

                    </p>

                    <div class="flex flex-wrap items-center gap-3 mt-7">

                        <a
                            href="#kabar"
                            class="inline-flex items-center gap-2 bg-amber-400 hover:bg-amber-300 text-[#542f1b] font-bold text-sm px-5 py-3 rounded-xl transition">

                            Jelajahi Kabar

                            <i class="fa-solid fa-arrow-down text-xs"></i>

                        </a>

                        <a
                            href="article.php"
                            class="inline-flex items-center gap-2 bg-white/5 hover:bg-white/10 border border-white/10 text-stone-200 font-semibold text-sm px-5 py-3 rounded-xl transition">

                            Semua Artikel

                            <i class="fa-solid fa-arrow-right text-xs"></i>

                        </a>

                    </div>

                </div>

            </div>

        </section>

        <section class="max-w-5xl mx-auto px-5 -mt-7 relative z-10">

            <form
                method="GET"
                action=""
                class="bg-white border border-stone-200 rounded-2xl shadow-xl shadow-stone-900/5 p-3 md:p-4">

                <div class="grid grid-cols-1 md:grid-cols-[1fr_12rem_12rem_auto] gap-3">

                    <div class="relative">

                        <span class="absolute inset-y-0 left-0 flex items-center pl-4 text-stone-400">

                            <i class="fa-solid fa-magnifying-glass"></i>

                        </span>

                        <input
                            type="text"
                            name="search"
                            value="<?= htmlspecialchars($search); ?>"
                            placeholder="Cari judul, penulis, atau isi kabar..."
                            class="w-full pl-11 pr-4 py-3 bg-stone-50 border border-stone-200 rounded-xl text-sm text-stone-800 placeholder:text-stone-400 focus:outline-none focus:ring-2 focus:ring-amber-600/20 focus:border-amber-600 transition">

                    </div>

                    <select
                        name="kategori"
                        class="w-full px-4 py-3 bg-stone-50 border border-stone-200 rounded-xl text-sm text-stone-700 focus:outline-none focus:ring-2 focus:ring-amber-600/20 focus:border-amber-600 transition">

                        <option value="">
                            Semua Kategori
                        </option>

                        <option value="Insight" <?= $kategori === 'Insight' ? 'selected' : ''; ?>>
                            Insight / Opini
                        </option>

                        <option value="Lokal" <?= $kategori === 'Lokal' ? 'selected' : ''; ?>>
                            Warta Lokal
                        </option>

                        <option value="Budaya" <?= $kategori === 'Budaya' ? 'selected' : ''; ?>>
                            Budaya & Tradisi
                        </option>

                        <option value="Gaya Urip" <?= $kategori === 'Gaya Urip' ? 'selected' : ''; ?>>
                            Gaya Urip
                        </option>

                    </select>

                    <select
                        name="read_time"
                        class="w-full px-4 py-3 bg-stone-50 border border-stone-200 rounded-xl text-sm text-stone-700 focus:outline-none focus:ring-2 focus:ring-amber-600/20 focus:border-amber-600 transition">

                        <option value="">
                            Semua Waktu Baca
                        </option>

                        <option value="1-3" <?= $read_time === '1-3' ? 'selected' : ''; ?>>
                            1–3 menit
                        </option>

                        <option value="4-6" <?= $read_time === '4-6' ? 'selected' : ''; ?>>
                            4–6 menit
                        </option>

                        <option value="7-9" <?= $read_time === '7-9' ? 'selected' : ''; ?>>
                            7–9 menit
                        </option>

                        <option value="10-12" <?= $read_time === '10-12' ? 'selected' : ''; ?>>
                            10–12 menit
                        </option>

                        <option value="13-15" <?= $read_time === '13-15' ? 'selected' : ''; ?>>
                            13–15 menit
                        </option>

                        <option value="15-plus" <?= $read_time === '15-plus' ? 'selected' : ''; ?>>
                            Lebih dari 15 menit
                        </option>

                    </select>

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
                                title="Reset filter"
                                class="inline-flex items-center justify-center w-11 bg-stone-100 hover:bg-stone-200 border border-stone-200 text-stone-600 rounded-xl transition">

                                <i class="fa-solid fa-rotate-right text-sm"></i>

                            </a>

                        <?php endif; ?>

                    </div>

                </div>

            </form>

        </section>

        <section class="max-w-5xl mx-auto px-5 pt-12 md:pt-14">

            <div class="relative overflow-hidden rounded-2xl bg-[#3a2113] text-stone-100 px-6 py-7 md:px-9 md:py-8">

                <div class="absolute inset-0 javanese-pattern opacity-70"></div>

                <div class="relative flex flex-col md:flex-row md:items-center gap-5 md:gap-8">

                    <div class="w-12 h-12 rounded-xl bg-amber-400/10 border border-amber-300/15 flex items-center justify-center flex-shrink-0">

                        <i class="fa-solid fa-quote-left text-amber-400"></i>

                    </div>

                    <div class="flex-1">

                        <p class="text-lg md:text-xl font-bold leading-relaxed">

                            “Saben kabar, ana critane.
                            Saben crita, ana maknane.”

                        </p>

                        <p class="text-xs text-stone-400 mt-2">

                            Setiap kabar punya cerita. Setiap cerita punya makna.

                        </p>

                    </div>

                    <div class="hidden md:block w-px h-12 bg-white/10"></div>

                    <div class="text-xs text-stone-400 md:max-w-[180px] leading-relaxed">

                        Baca pelan-pelan.
                        Siapa tahu ada yang bisa dibawa pulang.

                    </div>

                </div>

            </div>

        </section>

        <section
            id="kabar"
            class="max-w-5xl mx-auto px-5 pt-12 md:pt-14">

            <div class="flex flex-col sm:flex-row sm:items-end sm:justify-between gap-4 mb-7">

                <div>

                    <div class="flex items-center gap-2 text-amber-800 text-xs font-bold uppercase tracking-[.16em] mb-2">

                        <span class="w-5 h-px bg-amber-600"></span>

                        Baru Dibagikan

                    </div>

                    <h2 class="text-2xl md:text-3xl font-black text-stone-900 tracking-tight">
                        Kabar Terbaru
                    </h2>

                    <p class="text-sm text-stone-500 mt-1.5">
                        Tiga kabar yang paling baru dibagikan.
                    </p>

                </div>

                <a
                    href="article.php"
                    class="self-start sm:self-auto inline-flex items-center gap-2 text-xs font-bold text-stone-600 hover:text-amber-800 transition">

                    Lihat semua

                    <i class="fa-solid fa-arrow-right text-[10px]"></i>

                </a>

            </div>

            <?php if (count($latestArticles) > 0): ?>

                <div class="grid grid-cols-1 lg:grid-cols-5 gap-4">

                    <?php $featured = $latestArticles[0]; ?>

                    <article class="latest-card lg:col-span-3 bg-white border border-stone-200 rounded-2xl overflow-hidden shadow-sm">

                        <div class="grid grid-cols-1 sm:grid-cols-2 h-full">

                            <div class="relative min-h-[230px] sm:min-h-full bg-stone-100 overflow-hidden">

                                <?php if (!empty($featured['gambar']) && file_exists('assets/img/' . $featured['gambar'])): ?>

                                    <a href="detail.php?id=<?= $featured['id']; ?>">

                                        <img
                                            src="assets/img/<?= htmlspecialchars($featured['gambar']); ?>"
                                            alt="<?= htmlspecialchars($featured['judul']); ?>"
                                            class="latest-image absolute inset-0 w-full h-full object-cover">

                                    </a>

                                <?php else: ?>

                                    <a
                                        href="detail.php?id=<?= $featured['id']; ?>"
                                        class="absolute inset-0 bg-[#542f1b] flex items-center justify-center">

                                        <div class="absolute inset-0 javanese-pattern"></div>

                                        <div class="relative w-14 h-14 rounded-2xl bg-amber-400/10 border border-amber-300/20 flex items-center justify-center">

                                            <i class="fa-solid fa-mug-hot text-amber-400 text-xl"></i>

                                        </div>

                                    </a>

                                <?php endif; ?>

                            </div>

                            <div class="p-5 md:p-6 flex flex-col justify-between">

                                <div>

                                    <div class="flex items-center gap-2 mb-3">

                                        <span class="inline-flex bg-amber-50 border border-amber-200/70 text-amber-900 text-[10px] font-bold px-2.5 py-1.5 rounded-lg">

                                            <?= htmlspecialchars($categoryLabels[$featured['kategori']] ?? $featured['kategori']); ?>

                                        </span>

                                    </div>

                                    <h3 class="text-xl md:text-2xl font-black text-stone-900 leading-tight hover:text-[#542f1b] transition">

                                        <a href="detail.php?id=<?= $featured['id']; ?>">

                                            <?= htmlspecialchars($featured['judul']); ?>

                                        </a>

                                    </h3>

                                    <div class="article-preview text-xs text-stone-500 leading-relaxed mt-3 line-clamp-3">

                                        <?= $featured['konten']; ?>

                                    </div>

                                </div>

                                <div class="mt-5 pt-4 border-t border-stone-100">

                                    <div class="flex items-center justify-between gap-3">

                                        <div class="flex items-center gap-2 min-w-0">

                                            <div class="w-8 h-8 rounded-lg bg-stone-100 text-stone-500 flex items-center justify-center flex-shrink-0">

                                                <i class="fa-solid fa-user-pen text-[10px]"></i>

                                            </div>

                                            <div class="min-w-0">

                                                <p class="text-[9px] text-stone-400 uppercase tracking-wider">
                                                    Penulis
                                                </p>

                                                <a
                                                    href="author.php?nama=<?= urlencode($featured['penulis']); ?>"
                                                    class="text-xs font-bold text-stone-700 hover:text-amber-800 truncate block">

                                                    <?= htmlspecialchars($featured['penulis']); ?>

                                                </a>

                                            </div>

                                        </div>

                                        <div class="text-right flex-shrink-0">

                                            <p class="text-[10px] text-stone-400">

                                                <?= date('d M Y', strtotime($featured['tanggal'])); ?>

                                            </p>

                                            <p class="text-[10px] text-stone-500 font-semibold mt-0.5">

                                                <?= (int)$featured['read_time']; ?> menit baca

                                            </p>

                                        </div>

                                    </div>

                                </div>

                            </div>

                        </div>

                    </article>

                    <div class="lg:col-span-2 grid grid-cols-1 gap-4">

                        <?php for ($i = 1; $i < count($latestArticles); $i++): ?>

                            <?php $row = $latestArticles[$i]; ?>

                            <article class="latest-card bg-white border border-stone-200 rounded-2xl overflow-hidden shadow-sm">

                                <div class="flex h-full min-h-[145px]">

                                    <div class="w-32 sm:w-36 flex-shrink-0 bg-stone-100 overflow-hidden">

                                        <?php if (!empty($row['gambar']) && file_exists('assets/img/' . $row['gambar'])): ?>

                                            <a href="detail.php?id=<?= $row['id']; ?>">

                                                <img
                                                    src="assets/img/<?= htmlspecialchars($row['gambar']); ?>"
                                                    alt="<?= htmlspecialchars($row['judul']); ?>"
                                                    class="latest-image w-full h-full object-cover">

                                            </a>

                                        <?php else: ?>

                                            <a
                                                href="detail.php?id=<?= $row['id']; ?>"
                                                class="w-full h-full bg-[#542f1b] flex items-center justify-center relative">

                                                <div class="absolute inset-0 javanese-pattern"></div>

                                                <i class="relative fa-solid fa-mug-hot text-amber-400 text-lg"></i>

                                            </a>

                                        <?php endif; ?>

                                    </div>

                                    <div class="p-4 flex-1 min-w-0 flex flex-col">

                                        <div class="flex items-center justify-between gap-2">

                                            <span class="inline-flex max-w-[70%] truncate bg-amber-50 border border-amber-200/70 text-amber-900 text-[9px] font-bold px-2 py-1 rounded-md">

                                                <?= htmlspecialchars($categoryLabels[$row['kategori']] ?? $row['kategori']); ?>

                                            </span>

                                            <span class="text-[9px] text-stone-400 whitespace-nowrap">

                                                <?= (int)$row['read_time']; ?> mnt

                                            </span>

                                        </div>

                                        <h3 class="latest-title text-sm md:text-base font-black text-stone-900 leading-snug mt-2 hover:text-[#542f1b] transition">

                                            <a href="detail.php?id=<?= $row['id']; ?>">

                                                <?= htmlspecialchars($row['judul']); ?>

                                            </a>

                                        </h3>

                                        <div class="mt-auto pt-3 flex items-center justify-between gap-2">

                                            <a
                                                href="author.php?nama=<?= urlencode($row['penulis']); ?>"
                                                class="text-[10px] font-semibold text-stone-500 hover:text-amber-800 truncate">

                                                <?= htmlspecialchars($row['penulis']); ?>

                                            </a>

                                            <a
                                                href="detail.php?id=<?= $row['id']; ?>"
                                                class="w-7 h-7 rounded-lg bg-stone-100 hover:bg-[#542f1b] text-stone-500 hover:text-white flex items-center justify-center transition flex-shrink-0">

                                                <i class="fa-solid fa-arrow-right text-[9px]"></i>

                                            </a>

                                        </div>

                                    </div>

                                </div>

                            </article>

                        <?php endfor; ?>

                    </div>

                </div>

            <?php else: ?>

                <div class="bg-white border border-stone-200 rounded-2xl p-10 text-center">

                    <div class="w-14 h-14 mx-auto rounded-2xl bg-amber-50 text-amber-800 flex items-center justify-center mb-4">

                        <i class="fa-solid fa-mug-hot text-xl"></i>

                    </div>

                    <h3 class="text-lg font-black text-stone-900">
                        Belum ada kabar, Lur.
                    </h3>

                    <p class="text-sm text-stone-500 mt-1">
                        Sepertinya reriungan hari ini masih sepi.
                    </p>

                </div>

            <?php endif; ?>

        </section>

        <section class="max-w-5xl mx-auto px-5 pt-12 md:pt-14">

            <div class="flex flex-col md:flex-row md:items-end md:justify-between gap-4 mb-5">

                <div>

                    <div class="text-xs text-amber-800 font-bold uppercase tracking-[.16em] mb-2">
                        Eksplorasi
                    </div>

                    <h2 class="text-2xl font-black text-stone-900">
                        Lagi pengen baca apa, Lur?
                    </h2>

                </div>

                <p class="text-xs text-stone-400 max-w-xs md:text-right">
                    Pilih topik yang paling menarik perhatianmu.
                </p>

            </div>

            <div class="flex flex-wrap gap-2">

                <a
                    href="article.php"
                    class="category-pill inline-flex items-center gap-2 bg-[#542f1b] border border-[#542f1b] text-white text-xs font-bold px-4 py-2.5 rounded-xl">

                    <i class="fa-solid fa-layer-group text-amber-300"></i>

                    Semua

                </a>

                <a
                    href="article.php?kategori=Lokal"
                    class="category-pill inline-flex items-center gap-2 bg-white hover:bg-amber-50 border border-stone-200 hover:border-amber-200 text-stone-700 hover:text-amber-900 text-xs font-bold px-4 py-2.5 rounded-xl">

                    <i class="fa-solid fa-location-dot text-amber-700"></i>

                    Warta Lokal

                </a>

                <a
                    href="article.php?kategori=Budaya"
                    class="category-pill inline-flex items-center gap-2 bg-white hover:bg-amber-50 border border-stone-200 hover:border-amber-200 text-stone-700 hover:text-amber-900 text-xs font-bold px-4 py-2.5 rounded-xl">

                    <i class="fa-solid fa-landmark text-amber-700"></i>

                    Budaya & Tradisi

                </a>

                <a
                    href="article.php?kategori=Insight"
                    class="category-pill inline-flex items-center gap-2 bg-white hover:bg-amber-50 border border-stone-200 hover:border-amber-200 text-stone-700 hover:text-amber-900 text-xs font-bold px-4 py-2.5 rounded-xl">

                    <i class="fa-solid fa-lightbulb text-amber-700"></i>

                    Insight / Opini

                </a>

                <a
                    href="article.php?kategori=Gaya Urip"
                    class="category-pill inline-flex items-center gap-2 bg-white hover:bg-amber-50 border border-stone-200 hover:border-amber-200 text-stone-700 hover:text-amber-900 text-xs font-bold px-4 py-2.5 rounded-xl">

                    <i class="fa-solid fa-mug-hot text-amber-700"></i>

                    Gaya Urip

                </a>

            </div>

        </section>

        <section class="max-w-5xl mx-auto px-5 pt-12 md:pt-14">

            <div class="relative overflow-hidden rounded-2xl bg-white border border-stone-200 shadow-sm">

                <div class="absolute top-0 left-0 w-1 h-full bg-amber-500"></div>

                <div class="p-6 md:p-8">

                    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-6">

                        <div class="flex items-start gap-4">

                            <div class="w-11 h-11 rounded-xl bg-amber-50 border border-amber-200/70 text-amber-800 flex items-center justify-center flex-shrink-0">

                                <i class="fa-solid fa-circle-info"></i>

                            </div>

                            <div>

                                <div class="text-xs text-amber-800 font-bold uppercase tracking-[.16em] mb-2">
                                    Sekilas Ngabar Yuk!
                                </div>

                                <h2 class="text-2xl md:text-3xl font-black text-stone-900 leading-tight">
                                    Ruang kecil untuk kabar, cerita, dan gagasan.
                                </h2>

                                <p class="text-sm text-stone-500 leading-relaxed mt-2 max-w-2xl">
                                    Ngabar Yuk! adalah wadah sederhana untuk berbagi kabar,
                                    gagasan, dan cerita yang layak dibicarakan. Di sini,
                                    berbagai tulisan dipertemukan dalam suasana yang santai,
                                    dekat, dan tetap punya ruang untuk direriungkan.
                                </p>

                            </div>

                        </div>

                        <a
                            href="about.php"
                            class="inline-flex items-center justify-center gap-2 bg-[#542f1b] hover:bg-[#432515] text-white font-bold text-sm px-5 py-3 rounded-xl transition flex-shrink-0">

                            Kenal Lebih Dekat

                            <i class="fa-solid fa-arrow-right text-xs"></i>

                        </a>

                    </div>

                    <div class="flex flex-wrap items-center gap-x-5 gap-y-2 mt-5 pt-4 border-t border-stone-100 text-[11px] text-stone-400">

                        <span class="inline-flex items-center gap-1.5">
                            <i class="fa-solid fa-newspaper text-amber-700"></i>
                            Warta
                        </span>

                        <span class="inline-flex items-center gap-1.5">
                            <i class="fa-solid fa-comments text-amber-700"></i>
                            Reriungan
                        </span>

                        <span class="inline-flex items-center gap-1.5">
                            <i class="fa-solid fa-lightbulb text-amber-700"></i>
                            Insight
                        </span>

                    </div>

                </div>

            </div>

        </section>

        <section class="max-w-5xl mx-auto px-5 pt-12 md:pt-14">

            <div class="bg-white border border-stone-200 rounded-2xl overflow-hidden shadow-sm">

                <div class="p-6 md:p-7">

                    <div class="flex flex-col md:flex-row md:items-end md:justify-between gap-4">

                        <div>

                            <div class="flex items-center gap-2 text-amber-800 text-xs font-bold uppercase tracking-[.16em] mb-2">

                                <span class="w-5 h-px bg-amber-600"></span>

                                Sekilas Ngabar

                            </div>

                            <h2 class="text-2xl font-black text-stone-900">
                                Sedikit angka, sekadar gambaran.
                            </h2>

                            <p class="text-sm text-stone-500 mt-1.5 max-w-xl">

                                Bukan kompetisi. Cuma cara sederhana untuk melihat
                                seberapa ramai ruang reriungan ini.

                            </p>

                        </div>

                        <a
                            href="article.php"
                            class="inline-flex items-center gap-2 text-xs font-bold text-stone-600 hover:text-amber-800 transition">

                            Jelajahi semuanya

                            <i class="fa-solid fa-arrow-right text-[10px]"></i>

                        </a>

                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 mt-6">

                        <div class="stat-card border border-stone-200 rounded-xl p-4 bg-stone-50">

                            <div class="flex items-center justify-between">

                                <div class="w-9 h-9 rounded-lg bg-amber-50 text-amber-800 flex items-center justify-center">

                                    <i class="fa-solid fa-newspaper text-xs"></i>

                                </div>

                                <span class="text-[9px] text-stone-400 uppercase tracking-wider font-bold">
                                    Kabar
                                </span>

                            </div>

                            <p class="text-3xl font-black text-stone-900 mt-4">
                                <?= $totalKabar; ?>
                            </p>

                            <p class="text-xs text-stone-500 mt-1">
                                cerita telah dibagikan
                            </p>

                        </div>

                        <div class="stat-card border border-stone-200 rounded-xl p-4 bg-stone-50">

                            <div class="flex items-center justify-between">

                                <div class="w-9 h-9 rounded-lg bg-amber-50 text-amber-800 flex items-center justify-center">

                                    <i class="fa-solid fa-shapes text-xs"></i>

                                </div>

                                <span class="text-[9px] text-stone-400 uppercase tracking-wider font-bold">
                                    Topik
                                </span>

                            </div>

                            <p class="text-3xl font-black text-stone-900 mt-4">
                                <?= $totalKategori; ?>
                            </p>

                            <p class="text-xs text-stone-500 mt-1">
                                kategori untuk dijelajahi
                            </p>

                        </div>

                        <div class="stat-card border border-stone-200 rounded-xl p-4 bg-stone-50">

                            <div class="flex items-center justify-between">

                                <div class="w-9 h-9 rounded-lg bg-amber-50 text-amber-800 flex items-center justify-center">

                                    <i class="fa-solid fa-users text-xs"></i>

                                </div>

                                <span class="text-[9px] text-stone-400 uppercase tracking-wider font-bold">
                                    Penulis
                                </span>

                            </div>

                            <p class="text-3xl font-black text-stone-900 mt-4">
                                <?= $totalPenulis; ?>
                            </p>

                            <p class="text-xs text-stone-500 mt-1">
                                orang ikut ngabar
                            </p>

                        </div>

                    </div>

                </div>

            </div>

        </section>

        <section class="max-w-5xl mx-auto px-5 py-12 md:py-14">

            <div class="relative overflow-hidden rounded-2xl bg-amber-50 border border-amber-200/70">

                <div class="absolute -right-10 -bottom-16 w-48 h-48 rounded-full border border-amber-200"></div>

                <div class="absolute right-10 -bottom-10 w-24 h-24 rounded-full border border-amber-200"></div>

                <div class="relative p-6 md:p-8 flex flex-col md:flex-row md:items-center md:justify-between gap-6">

                    <div class="max-w-xl">

                        <div class="flex items-center gap-2 text-amber-800 text-xs font-bold uppercase tracking-[.16em] mb-2">

                            <i class="fa-solid fa-comments"></i>

                            Masih pengen ngabar?

                        </div>

                        <h2 class="text-2xl md:text-3xl font-black text-stone-900 leading-tight">

                            Masih banyak cerita
                            buat direriungkan.

                        </h2>

                        <p class="text-sm text-stone-600 leading-relaxed mt-2">

                            Telusuri kumpulan artikel lainnya dan temukan
                            kabar yang mungkin belum sempat kamu baca.

                        </p>

                    </div>

                    <a
                        href="article.php"
                        class="inline-flex items-center justify-center gap-2 bg-[#542f1b] hover:bg-[#432515] text-white font-bold text-sm px-5 py-3 rounded-xl transition flex-shrink-0">

                        Buka Semua Artikel

                        <i class="fa-solid fa-arrow-right text-xs"></i>

                    </a>

                </div>

            </div>

        </section>

    </main>

    <?php include 'assets/footer.php'; ?>

    <?php if (isset($_GET['status'])): ?>

        <script>
            const urlParams = new URLSearchParams(window.location.search);
            const status = urlParams.get('status');

            const messages = {
                sukses: 'Kabar baru berhasil disebarkan, Lur.',
                update: 'Perubahan kabar berhasil disimpan.',
                hapus: 'Kabar berhasil dihapus dari beranda.'
            };

            if (status && messages[status]) {

                const notification = document.createElement('div');

                notification.className =
                    'fixed bottom-5 right-5 left-5 sm:left-auto sm:max-w-sm bg-[#3a2113] text-white border border-white/10 rounded-xl shadow-2xl px-4 py-3 z-[100] flex items-start gap-3';

                notification.innerHTML = `
                    <div class="w-8 h-8 rounded-lg bg-emerald-500/15 text-emerald-400 flex items-center justify-center flex-shrink-0">
                        <i class="fa-solid fa-circle-check text-sm"></i>
                    </div>

                    <div class="flex-1">
                        <p class="text-sm font-semibold">
                            ${messages[status]}
                        </p>
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