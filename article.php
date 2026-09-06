<?php

require_once 'config.php';

$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$kategori = isset($_GET['kategori']) ? trim($_GET['kategori']) : '';
$read_time = isset($_GET['read_time']) ? trim($_GET['read_time']) : '';

$perPage = 9;
$page = isset($_GET['page']) ? (int) $_GET['page'] : 1;

if ($page < 1) {
    $page = 1;
}

$query = "SELECT * FROM berita WHERE 1=1";
$countQuery = "SELECT COUNT(*) AS total FROM berita WHERE 1=1";

$params = [];
$types = "";

$countParams = [];
$countTypes = "";

if (!empty($search)) {
    $query .= " AND (judul LIKE ? OR penulis LIKE ? OR konten LIKE ?)";
    $countQuery .= " AND (judul LIKE ? OR penulis LIKE ? OR konten LIKE ?)";

    $searchTerm = "%" . $search . "%";

    $params[] = $searchTerm;
    $params[] = $searchTerm;
    $params[] = $searchTerm;
    $types .= "sss";

    $countParams[] = $searchTerm;
    $countParams[] = $searchTerm;
    $countParams[] = $searchTerm;
    $countTypes .= "sss";
}

if (!empty($kategori)) {
    $query .= " AND kategori = ?";
    $countQuery .= " AND kategori = ?";

    $params[] = $kategori;
    $types .= "s";

    $countParams[] = $kategori;
    $countTypes .= "s";
}

if (!empty($read_time)) {
    if ($read_time === '1-3') {
        $query .= " AND read_time BETWEEN 1 AND 3";
        $countQuery .= " AND read_time BETWEEN 1 AND 3";
    } elseif ($read_time === '4-6') {
        $query .= " AND read_time BETWEEN 4 AND 6";
        $countQuery .= " AND read_time BETWEEN 4 AND 6";
    } elseif ($read_time === '7-9') {
        $query .= " AND read_time BETWEEN 7 AND 9";
        $countQuery .= " AND read_time BETWEEN 7 AND 9";
    } elseif ($read_time === '10-12') {
        $query .= " AND read_time BETWEEN 10 AND 12";
        $countQuery .= " AND read_time BETWEEN 10 AND 12";
    } elseif ($read_time === '13-15') {
        $query .= " AND read_time BETWEEN 13 AND 15";
        $countQuery .= " AND read_time BETWEEN 13 AND 15";
    } elseif ($read_time === '15-plus') {
        $query .= " AND read_time > 15";
        $countQuery .= " AND read_time > 15";
    }
}

$countStmt = mysqli_prepare($conn, $countQuery);

if (!$countStmt) {
    die("Gagal menghitung artikel: " . mysqli_error($conn));
}

if (!empty($countParams)) {
    mysqli_stmt_bind_param($countStmt, $countTypes, ...$countParams);
}

mysqli_stmt_execute($countStmt);

$countResult = mysqli_stmt_get_result($countStmt);
$countData = mysqli_fetch_assoc($countResult);

$totalArticles = (int) $countData['total'];

mysqli_stmt_close($countStmt);

$totalPages = max(1, ceil($totalArticles / $perPage));

if ($page > $totalPages) {
    $page = $totalPages;
}

$offset = ($page - 1) * $perPage;

$query .= " ORDER BY id DESC LIMIT ? OFFSET ?";

$params[] = $perPage;
$params[] = $offset;
$types .= "ii";

$stmt = mysqli_prepare($conn, $query);

if (!$stmt) {
    die("Gagal mengambil artikel: " . mysqli_error($conn));
}

if (!empty($params)) {
    mysqli_stmt_bind_param($stmt, $types, ...$params);
}

mysqli_stmt_execute($stmt);

$result = mysqli_stmt_get_result($stmt);
$articles = mysqli_fetch_all($result, MYSQLI_ASSOC);

mysqli_stmt_close($stmt);

function excerptText($html, $length = 190)
{
    $text = trim(preg_replace('/\s+/', ' ', strip_tags($html)));

    if (mb_strlen($text) <= $length) {
        return $text;
    }

    return mb_substr($text, 0, $length) . '...';
}

function categoryLabel($kategori)
{
    $labels = [
        'Insight' => 'Insight / Opini',
        'Lokal' => 'Warta Lokal',
        'Budaya' => 'Budaya & Tradisi',
        'Gaya Urip' => 'Gaya Urip'
    ];

    return $labels[$kategori] ?? $kategori;
}

function categoryIcon($kategori)
{
    $icons = [
        'Insight' => 'fa-lightbulb',
        'Lokal' => 'fa-location-dot',
        'Budaya' => 'fa-landmark',
        'Gaya Urip' => 'fa-mug-hot'
    ];

    return $icons[$kategori] ?? 'fa-newspaper';
}

$queryString = [];

if (!empty($search)) {
    $queryString['search'] = $search;
}

if (!empty($kategori)) {
    $queryString['kategori'] = $kategori;
}

if (!empty($read_time)) {
    $queryString['read_time'] = $read_time;
}

function paginationUrl($page, $queryString)
{
    $queryString['page'] = $page;

    return 'article.php?' . http_build_query($queryString);
}

$hasFilter = !empty($search) || !empty($kategori) || !empty($read_time);

$firstResult = $totalArticles > 0 ? $offset + 1 : 0;
$lastResult = min($offset + $perPage, $totalArticles);

?>

<!DOCTYPE html>
<html lang="id">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0">

    <title>Semua Artikel — Ngabar Yuk!</title>

    <meta
        name="description"
        content="Jelajahi seluruh artikel, warta, gagasan, dan cerita di Ngabar Yuk!">

    <script src="https://cdn.tailwindcss.com"></script>

    <link
        rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        html {
            scroll-behavior: smooth;
        }

        body {
            background:
                radial-gradient(circle at 10% 0%,
                    rgba(146, 64, 14, 0.07),
                    transparent 28%),
                radial-gradient(circle at 90% 15%,
                    rgba(120, 53, 15, 0.06),
                    transparent 25%),
                #f7f5f1;
        }

        .javanese-pattern {
            background-image:
                linear-gradient(135deg,
                    rgba(255, 255, 255, 0.035) 25%,
                    transparent 25%),
                linear-gradient(225deg,
                    rgba(255, 255, 255, 0.035) 25%,
                    transparent 25%),
                linear-gradient(45deg,
                    rgba(255, 255, 255, 0.035) 25%,
                    transparent 25%),
                linear-gradient(315deg,
                    rgba(255, 255, 255, 0.035) 25%,
                    transparent 25%);

            background-position:
                10px 0,
                10px 0,
                0 0,
                0 0;

            background-size: 20px 20px;
            background-repeat: repeat;
        }

        .article-preview {
            color: #57534e;
            line-height: 1.75;
            font-size: 0.875rem;
            overflow: hidden;
        }

        .article-preview p {
            margin-bottom: 0.65rem;
        }

        .article-preview h2,
        .article-preview h3,
        .article-preview h4 {
            color: #292524;
            font-weight: 800;
            line-height: 1.35;
            margin-top: 0.9rem;
            margin-bottom: 0.5rem;
        }

        .article-preview h2 {
            font-size: 1.05rem;
        }

        .article-preview h3 {
            font-size: 0.98rem;
        }

        .article-preview h4 {
            font-size: 0.92rem;
        }

        .article-preview strong {
            color: #292524;
            font-weight: 800;
        }

        .article-preview em {
            font-style: italic;
        }

        .article-preview u {
            text-decoration: underline;
            text-underline-offset: 2px;
        }

        .article-preview ul,
        .article-preview ol {
            margin: 0.65rem 0;
            padding-left: 1.25rem;
        }

        .article-preview ul {
            list-style: disc;
        }

        .article-preview ol {
            list-style: decimal;
        }

        .article-preview li {
            margin-bottom: 0.25rem;
        }

        .article-preview blockquote {
            border-left: 3px solid #d97706;
            padding-left: 0.85rem;
            color: #57534e;
            font-style: italic;
            margin: 0.8rem 0;
        }

        .article-preview a {
            color: #92400e;
            text-decoration: underline;
            text-underline-offset: 2px;
        }

        .article-preview img {
            max-width: 100%;
            height: auto;
            border-radius: 0.75rem;
            margin: 0.8rem 0;
        }

        .article-card {
            transition:
                transform 180ms ease,
                box-shadow 180ms ease,
                border-color 180ms ease;
        }

        .article-card:hover {
            transform: translateY(-3px);
        }

        .line-clamp-3 {
            display: -webkit-box;
            -webkit-line-clamp: 3;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }
    </style>

</head>

<body class="bg-stone-50 text-stone-800 font-sans antialiased min-h-screen flex flex-col selection:bg-amber-200 selection:text-amber-900">

    <?php include 'assets/header.php'; ?>

    <main class="flex-1">

        <section class="relative overflow-hidden">

            <div class="max-w-5xl mx-auto px-5 pt-12 pb-8">

                <div class="max-w-2xl">

                    <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-amber-100 border border-amber-200 text-amber-800 text-xs font-bold uppercase tracking-[0.12em]">

                        <i class="fa-solid fa-book-open text-[10px]"></i>

                        Arsip Kabar

                    </div>

                    <h1 class="mt-5 text-4xl sm:text-5xl font-black tracking-tight text-[#542f1b] leading-[1.05]">

                        Semua Artikel

                    </h1>

                    <p class="mt-4 text-stone-600 leading-relaxed text-sm sm:text-base max-w-xl">

                        Jelajahi seluruh kabar, gagasan, cerita, dan insight
                        yang pernah dibagikan melalui Ngabar Yuk!

                    </p>

                </div>

            </div>

        </section>

        <section class="max-w-5xl mx-auto px-5 pb-12">

            <div class="bg-white border border-stone-200 rounded-3xl shadow-sm p-4 sm:p-5">

                <form
                    action="article.php"
                    method="GET"
                    class="grid grid-cols-1 md:grid-cols-[1.6fr_1fr_1fr_auto] gap-3">

                    <div class="relative">

                        <i class="fa-solid fa-magnifying-glass absolute left-4 top-1/2 -translate-y-1/2 text-stone-400 text-sm"></i>

                        <input
                            type="text"
                            name="search"
                            value="<?= htmlspecialchars($search); ?>"
                            placeholder="Cari artikel, penulis, atau isi..."
                            class="w-full h-11 pl-10 pr-4 rounded-xl border border-stone-200 bg-stone-50 text-sm text-stone-800 placeholder:text-stone-400 focus:outline-none focus:ring-2 focus:ring-amber-400/40 focus:border-amber-400 transition">

                    </div>

                    <select
                        name="kategori"
                        class="h-11 px-4 rounded-xl border border-stone-200 bg-stone-50 text-sm text-stone-700 focus:outline-none focus:ring-2 focus:ring-amber-400/40 focus:border-amber-400 transition">

                        <option value="">
                            Semua Kategori
                        </option>

                        <option
                            value="Insight"
                            <?= $kategori === 'Insight' ? 'selected' : ''; ?>>
                            Insight / Opini
                        </option>

                        <option
                            value="Lokal"
                            <?= $kategori === 'Lokal' ? 'selected' : ''; ?>>
                            Warta Lokal
                        </option>

                        <option
                            value="Budaya"
                            <?= $kategori === 'Budaya' ? 'selected' : ''; ?>>
                            Budaya & Tradisi
                        </option>

                        <option
                            value="Gaya Urip"
                            <?= $kategori === 'Gaya Urip' ? 'selected' : ''; ?>>
                            Gaya Urip
                        </option>

                    </select>

                    <select
                        name="read_time"
                        class="h-11 px-4 rounded-xl border border-stone-200 bg-stone-50 text-sm text-stone-700 focus:outline-none focus:ring-2 focus:ring-amber-400/40 focus:border-amber-400 transition">

                        <option value="">
                            Semua Waktu Baca
                        </option>

                        <option
                            value="1-3"
                            <?= $read_time === '1-3' ? 'selected' : ''; ?>>
                            1–3 menit
                        </option>

                        <option
                            value="4-6"
                            <?= $read_time === '4-6' ? 'selected' : ''; ?>>
                            4–6 menit
                        </option>

                        <option
                            value="7-9"
                            <?= $read_time === '7-9' ? 'selected' : ''; ?>>
                            7–9 menit
                        </option>

                        <option
                            value="10-12"
                            <?= $read_time === '10-12' ? 'selected' : ''; ?>>
                            10–12 menit
                        </option>

                        <option
                            value="13-15"
                            <?= $read_time === '13-15' ? 'selected' : ''; ?>>
                            13–15 menit
                        </option>

                        <option
                            value="15-plus"
                            <?= $read_time === '15-plus' ? 'selected' : ''; ?>>
                            Lebih dari 15 menit
                        </option>

                    </select>

                    <button
                        type="submit"
                        class="h-11 px-5 rounded-xl bg-[#542f1b] hover:bg-[#432515] text-white font-bold text-sm transition">

                        <i class="fa-solid fa-filter mr-1.5"></i>

                        Filter

                    </button>

                </form>

                <?php if ($hasFilter): ?>

                    <div class="mt-3 flex items-center justify-between gap-3 flex-wrap">

                        <p class="text-xs text-stone-500">

                            Filter sedang diterapkan pada daftar artikel.

                        </p>

                        <a
                            href="article.php"
                            class="inline-flex items-center gap-1.5 text-xs font-bold text-amber-800 hover:text-amber-900 transition">

                            <i class="fa-solid fa-xmark text-[10px]"></i>

                            Reset filter

                        </a>

                    </div>

                <?php endif; ?>

            </div>

            <div class="mt-8 flex items-end justify-between gap-4">

                <div>

                    <p class="text-xs uppercase tracking-[0.15em] text-stone-500 font-bold">

                        Koleksi Artikel

                    </p>

                    <h2 class="mt-1 text-xl sm:text-2xl font-black text-stone-900">

                        <?= number_format($totalArticles, 0, ',', '.'); ?> artikel

                    </h2>

                </div>

                <?php if ($totalArticles > 0): ?>

                    <p class="text-xs text-stone-500 text-right">

                        Menampilkan
                        <?= $firstResult; ?>–<?= $lastResult; ?>

                    </p>

                <?php endif; ?>

            </div>

            <?php if (!empty($articles)): ?>

                <div class="mt-5 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">

                    <?php foreach ($articles as $article): ?>

                        <?php
                        $category = categoryLabel($article['kategori']);
                        $icon = categoryIcon($article['kategori']);
                        ?>

                        <article
                            class="article-card group bg-white border border-stone-200 rounded-3xl overflow-hidden shadow-sm hover:shadow-xl hover:shadow-stone-900/5 hover:border-amber-200 flex flex-col">

                            <a
                                href="detail.php?id=<?= (int) $article['id']; ?>"
                                class="block">

                                <div class="relative h-48 bg-stone-100 overflow-hidden">

                                    <?php if (!empty($article['gambar'])): ?>

                                        <img
                                            src="assets/img/<?= htmlspecialchars($article['gambar']); ?>"
                                            alt="<?= htmlspecialchars($article['judul']); ?>"
                                            class="w-full h-full object-cover group-hover:scale-[1.03] transition-transform duration-500">

                                    <?php else: ?>

                                        <div class="w-full h-full flex items-center justify-center bg-[#542f1b] javanese-pattern">

                                            <div class="w-14 h-14 rounded-2xl bg-amber-400 text-[#542f1b] flex items-center justify-center">

                                                <i class="fa-solid <?= $icon; ?> text-xl"></i>

                                            </div>

                                        </div>

                                    <?php endif; ?>

                                    <div class="absolute top-3 left-3">

                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1.5 rounded-lg bg-white/95 backdrop-blur-sm text-[11px] font-bold text-[#542f1b] shadow-sm">

                                            <i class="fa-solid <?= $icon; ?> text-[9px]"></i>

                                            <?= htmlspecialchars($category); ?>

                                        </span>

                                    </div>

                                </div>

                            </a>

                            <div class="p-5 flex flex-col flex-1">

                                <div class="flex items-center gap-3 text-[11px] text-stone-500 mb-3">

                                    <span class="inline-flex items-center gap-1.5">

                                        <i class="fa-regular fa-calendar"></i>

                                        <?= date('d M Y', strtotime($article['tanggal'])); ?>

                                    </span>

                                    <span class="w-1 h-1 rounded-full bg-stone-300"></span>

                                    <span class="inline-flex items-center gap-1.5">

                                        <i class="fa-regular fa-clock"></i>

                                        <?= (int) $article['read_time']; ?> menit baca

                                    </span>

                                </div>

                                <a
                                    href="detail.php?id=<?= (int) $article['id']; ?>"
                                    class="group/title">

                                    <h3 class="text-lg font-black text-stone-900 leading-snug group-hover/title:text-[#542f1b] transition">

                                        <?= htmlspecialchars($article['judul']); ?>

                                    </h3>

                                </a>

                                <div class="article-preview line-clamp-3 mt-3">

                                    <?= $article['konten']; ?>

                                </div>

                                <div class="mt-auto pt-5">

                                    <div class="flex items-center justify-between gap-3 border-t border-stone-100 pt-4">

                                        <a
                                            href="author.php?nama=<?= urlencode($article['penulis']); ?>"
                                            class="flex items-center gap-2 min-w-0 group/author">

                                            <div class="w-8 h-8 rounded-full bg-stone-100 text-[#542f1b] flex items-center justify-center shrink-0">

                                                <i class="fa-solid fa-user text-xs"></i>

                                            </div>

                                            <div class="min-w-0">

                                                <p class="text-[10px] text-stone-400 uppercase tracking-wider font-semibold">

                                                    Penulis

                                                </p>

                                                <p class="text-xs text-stone-700 font-bold truncate group-hover/author:text-amber-800 transition">

                                                    <?= htmlspecialchars($article['penulis']); ?>

                                                </p>

                                            </div>

                                        </a>

                                        <a
                                            href="detail.php?id=<?= (int) $article['id']; ?>"
                                            class="inline-flex items-center gap-1.5 text-xs font-bold text-[#542f1b] group-hover:text-amber-700 transition shrink-0">

                                            Baca

                                            <i class="fa-solid fa-arrow-right text-[10px] group-hover:translate-x-0.5 transition-transform"></i>

                                        </a>

                                    </div>

                                </div>

                            </div>

                        </article>

                    <?php endforeach; ?>

                </div>

                <?php if ($totalPages > 1): ?>

                    <div class="mt-9 flex items-center justify-center gap-1.5 flex-wrap">

                        <?php if ($page > 1): ?>

                            <a
                                href="<?= paginationUrl($page - 1, $queryString); ?>"
                                class="w-10 h-10 rounded-xl border border-stone-200 bg-white hover:bg-stone-50 text-stone-600 flex items-center justify-center transition">

                                <i class="fa-solid fa-chevron-left text-xs"></i>

                            </a>

                        <?php endif; ?>

                        <?php
                        $startPage = max(1, $page - 2);
                        $endPage = min($totalPages, $page + 2);
                        ?>

                        <?php if ($startPage > 1): ?>

                            <a
                                href="<?= paginationUrl(1, $queryString); ?>"
                                class="w-10 h-10 rounded-xl border border-stone-200 bg-white hover:bg-stone-50 text-sm font-semibold text-stone-600 flex items-center justify-center transition">

                                1

                            </a>

                            <?php if ($startPage > 2): ?>

                                <span class="w-8 text-center text-stone-400">

                                    ...

                                </span>

                            <?php endif; ?>

                        <?php endif; ?>

                        <?php for ($i = $startPage; $i <= $endPage; $i++): ?>

                            <a
                                href="<?= paginationUrl($i, $queryString); ?>"
                                class="w-10 h-10 rounded-xl flex items-center justify-center text-sm font-bold transition <?= $i === $page ? 'bg-[#542f1b] text-white shadow-sm' : 'border border-stone-200 bg-white hover:bg-stone-50 text-stone-600'; ?>">

                                <?= $i; ?>

                            </a>

                        <?php endfor; ?>

                        <?php if ($endPage < $totalPages): ?>

                            <?php if ($endPage < $totalPages - 1): ?>

                                <span class="w-8 text-center text-stone-400">

                                    ...

                                </span>

                            <?php endif; ?>

                            <a
                                href="<?= paginationUrl($totalPages, $queryString); ?>"
                                class="w-10 h-10 rounded-xl border border-stone-200 bg-white hover:bg-stone-50 text-sm font-semibold text-stone-600 flex items-center justify-center transition">

                                <?= $totalPages; ?>

                            </a>

                        <?php endif; ?>

                        <?php if ($page < $totalPages): ?>

                            <a
                                href="<?= paginationUrl($page + 1, $queryString); ?>"
                                class="w-10 h-10 rounded-xl border border-stone-200 bg-white hover:bg-stone-50 text-stone-600 flex items-center justify-center transition">

                                <i class="fa-solid fa-chevron-right text-xs"></i>

                            </a>

                        <?php endif; ?>

                    </div>

                    <p class="text-center text-[11px] text-stone-400 mt-3">

                        Halaman <?= $page; ?> dari <?= $totalPages; ?>

                    </p>

                <?php endif; ?>

            <?php else: ?>

                <div class="mt-6 bg-white border border-stone-200 rounded-3xl p-10 sm:p-14 text-center shadow-sm">

                    <div class="w-16 h-16 mx-auto rounded-2xl bg-stone-100 text-stone-400 flex items-center justify-center">

                        <i class="fa-solid fa-newspaper text-2xl"></i>

                    </div>

                    <h3 class="mt-5 text-xl font-black text-stone-900">

                        Belum ada artikel

                    </h3>

                    <p class="mt-2 text-sm text-stone-500 max-w-md mx-auto leading-relaxed">

                        <?php if ($hasFilter): ?>

                            Tidak ada artikel yang sesuai dengan pencarian atau filter yang kamu pilih.

                        <?php else: ?>

                            Belum ada kabar yang dipublikasikan di Ngabar Yuk!

                        <?php endif; ?>

                    </p>

                    <?php if ($hasFilter): ?>

                        <a
                            href="article.php"
                            class="inline-flex items-center gap-2 mt-6 px-4 py-2.5 rounded-xl bg-[#542f1b] hover:bg-[#432515] text-white text-sm font-bold transition">

                            <i class="fa-solid fa-arrow-rotate-left text-xs"></i>

                            Tampilkan Semua

                        </a>

                    <?php endif; ?>

                </div>

            <?php endif; ?>

        </section>

    </main>

    <?php include 'assets/footer.php'; ?>

</body>

</html>