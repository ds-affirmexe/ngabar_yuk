<?php
require_once 'config.php';

$search   = isset($_GET['search']) ? trim($_GET['search']) : '';
$kategori = isset($_GET['kategori']) ? trim($_GET['kategori']) : '';

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

$query .= " ORDER BY id DESC";

$stmt = mysqli_prepare($conn, $query);

if (!empty($params)) {
    mysqli_stmt_bind_param($stmt, $types, ...$params);
}

mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
?>

<!DOCTYPE html>
<html lang="id">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Beranda - Ngabar Yuk!</title>

    <script src="https://cdn.tailwindcss.com"></script>

    <link rel="stylesheet"
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
    </style>

</head>

<body class="bg-stone-50 text-stone-800 font-sans antialiased min-h-screen flex flex-col selection:bg-amber-200 selection:text-amber-900">

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

                                Warta • Reriungan • Insight

                            </div>

                        </div>

                    </a>

                    <nav class="flex items-center gap-1.5 sm:gap-2">

                        <a href="index.php"
                            class="inline-flex items-center gap-2 text-amber-300 bg-white/5 font-semibold text-sm px-3 py-2.5 rounded-xl transition">

                            <i class="fa-solid fa-house text-xs"></i>

                            <span class="hidden sm:inline">Beranda</span>

                        </a>

                        <a href="about.php"
                            class="hidden sm:inline-flex items-center gap-2 text-stone-200 hover:text-amber-300 font-semibold text-sm px-3 py-2.5 rounded-xl hover:bg-white/5 transition">

                            <i class="fa-solid fa-circle-info text-xs"></i>

                            Tentang

                        </a>

                        <a href="create.php"
                            class="inline-flex items-center gap-2 bg-amber-400 hover:bg-amber-300 text-[#542f1b] font-bold text-sm px-3.5 py-2.5 rounded-xl transition shadow-sm">

                            <i class="fa-solid fa-pen text-xs"></i>

                            <span class="hidden sm:inline">Tulis Kabar</span>
                            <span class="sm:hidden">Tulis</span>

                        </a>

                    </nav>

                </div>

            </div>

        </div>

    </header>

    <main class="flex-1">

        <section class="relative overflow-hidden bg-[#542f1b] text-white">

            <div class="absolute inset-0 javanese-pattern opacity-70"></div>

            <div class="relative max-w-5xl mx-auto px-5 py-14 md:py-20">

                <div class="max-w-3xl">

                    <div class="inline-flex items-center gap-2 text-amber-300 text-xs font-bold uppercase tracking-[0.18em] mb-5">

                        <span class="w-8 h-px bg-amber-400"></span>

                        Warta & Reriungan

                    </div>

                    <h1 class="text-4xl md:text-5xl lg:text-6xl font-black tracking-tight leading-[1.05]">

                        Apa kabar hari ini,
                        <span class="text-amber-400">Lur?</span>

                    </h1>

                    <p class="text-stone-300 text-sm md:text-base leading-relaxed mt-5 max-w-2xl">

                        Ngabar Yuk! adalah ruang sederhana untuk berbagi kabar,
                        gagasan, dan cerita. Baca yang menarik, temukan sudut pandang
                        baru, atau ikut meramaikan reriungan dengan ceritamu sendiri.

                    </p>

                    <div class="flex flex-wrap items-center gap-3 mt-7">

                        <a href="create.php"
                            class="inline-flex items-center gap-2 bg-amber-400 hover:bg-amber-300 text-[#542f1b] font-bold text-sm px-5 py-3 rounded-xl transition shadow-sm">

                            <i class="fa-solid fa-pen-to-square"></i>

                            Tulis Kabar

                        </a>

                        <a href="#kabar"
                            class="inline-flex items-center gap-2 border border-white/15 hover:bg-white/10 text-stone-200 font-semibold text-sm px-5 py-3 rounded-xl transition">

                            Lihat Kabar

                            <i class="fa-solid fa-arrow-down text-xs"></i>

                        </a>

                    </div>

                </div>

            </div>

        </section>

        <section class="max-w-5xl mx-auto px-5 -mt-7 relative z-10">

            <form method="GET" action=""
                class="bg-white border border-stone-200 rounded-2xl shadow-xl shadow-stone-900/5 p-3 md:p-4">

                <div class="flex flex-col md:flex-row gap-3">

                    <div class="relative flex-1">

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

                    <div class="flex gap-2">

                        <button
                            type="submit"
                            class="flex-1 md:flex-none inline-flex items-center justify-center gap-2 bg-[#542f1b] hover:bg-[#432515] text-white font-bold text-sm px-5 py-3 rounded-xl transition">

                            <i class="fa-solid fa-filter text-xs"></i>

                            Saring

                        </button>

                        <?php if (!empty($search) || !empty($kategori)): ?>

                            <a href="index.php"
                                class="inline-flex items-center justify-center w-11 bg-stone-100 hover:bg-stone-200 border border-stone-200 text-stone-600 rounded-xl transition"
                                title="Reset Filter">

                                <i class="fa-solid fa-rotate-right text-sm"></i>

                            </a>

                        <?php endif; ?>

                    </div>

                </div>

            </form>

        </section>

        <section id="kabar" class="max-w-5xl mx-auto px-5 py-12 md:py-14">

            <div class="flex flex-col sm:flex-row sm:items-end justify-between gap-4 mb-7">

                <div>

                    <div class="flex items-center gap-2 text-amber-800 text-xs font-bold uppercase tracking-[0.16em] mb-2">

                        <span class="w-5 h-px bg-amber-600"></span>

                        Kumpulan Warta

                    </div>

                    <h2 class="text-2xl md:text-3xl font-black text-stone-900 tracking-tight">

                        Kabar Terbaru

                    </h2>

                    <p class="text-sm text-stone-500 mt-1.5">

                        Cerita dan gagasan yang baru saja dibagikan.

                    </p>

                </div>

                <div class="flex items-center gap-2">

                    <span class="inline-flex items-center gap-2 bg-amber-50 border border-amber-200/70 text-amber-900 px-3 py-2 rounded-xl text-xs font-semibold">

                        <i class="fa-solid fa-newspaper text-amber-700"></i>

                        <?= mysqli_num_rows($result); ?> kabar

                    </span>

                </div>

            </div>

            <?php if (mysqli_num_rows($result) > 0): ?>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">

                    <?php while ($row = mysqli_fetch_assoc($result)): ?>

                        <article class="group bg-white border border-stone-200 rounded-2xl overflow-hidden shadow-sm hover:shadow-lg hover:-translate-y-0.5 transition-all duration-300 flex flex-col">

                            <?php if (!empty($row['gambar']) && file_exists('assets/img/' . $row['gambar'])): ?>

                                <a href="detail.php?id=<?= $row['id']; ?>"
                                    class="block h-52 overflow-hidden bg-stone-100">

                                    <img
                                        src="assets/img/<?= htmlspecialchars($row['gambar']); ?>"
                                        alt="<?= htmlspecialchars($row['judul']); ?>"
                                        class="w-full h-full object-cover group-hover:scale-[1.03] transition duration-500">

                                </a>

                            <?php else: ?>

                                <a href="detail.php?id=<?= $row['id']; ?>"
                                    class="block h-32 bg-[#542f1b] relative overflow-hidden">

                                    <div class="absolute inset-0 javanese-pattern opacity-60"></div>

                                    <div class="relative h-full flex items-center justify-center">

                                        <div class="w-12 h-12 rounded-xl bg-amber-400/15 border border-amber-300/20 flex items-center justify-center">

                                            <i class="fa-solid fa-mug-hot text-amber-400 text-lg"></i>

                                        </div>

                                    </div>

                                </a>

                            <?php endif; ?>

                            <div class="p-5 flex-1 flex flex-col">

                                <div class="flex items-center justify-between gap-3 mb-3">

                                    <span class="inline-flex bg-amber-50 border border-amber-200/70 text-amber-900 text-[11px] font-bold px-2.5 py-1 rounded-lg">

                                        <?= htmlspecialchars($row['kategori']); ?>

                                    </span>

                                    <div class="flex items-center gap-3 text-[11px] text-stone-400 whitespace-nowrap">

                                        <span class="flex items-center gap-1.5">

                                            <i class="fa-regular fa-calendar"></i>

                                            <?= date('d M Y', strtotime($row['tanggal'])); ?>

                                        </span>

                                        <span class="flex items-center gap-1.5">

                                            <i class="fa-regular fa-clock"></i>

                                            <?= (int)$row['read_time']; ?> menit baca

                                        </span>

                                    </div>

                                </div>

                                <h3 class="text-xl font-black text-stone-900 leading-snug group-hover:text-[#542f1b] transition">

                                    <a href="detail.php?id=<?= $row['id']; ?>">

                                        <?= htmlspecialchars($row['judul']); ?>

                                    </a>

                                </h3>

                                <p class="text-sm text-stone-500 leading-relaxed mt-3 line-clamp-3">

                                    <?= htmlspecialchars($row['konten']); ?>

                                </p>

                                <div class="mt-auto pt-5">

                                    <div class="border-t border-stone-100 pt-4 flex items-center justify-between gap-3">

                                        <div class="flex items-center gap-2 min-w-0">

                                            <div class="w-8 h-8 rounded-lg bg-stone-100 text-stone-500 flex items-center justify-center flex-shrink-0">

                                                <i class="fa-solid fa-user-pen text-xs"></i>

                                            </div>

                                            <div class="min-w-0">

                                                <p class="text-[10px] text-stone-400 uppercase tracking-wider font-semibold">

                                                    Ditulis oleh

                                                </p>

                                                <p class="text-xs text-stone-700 font-bold truncate">

                                                    <?= htmlspecialchars($row['penulis']); ?>

                                                </p>

                                            </div>

                                        </div>

                                        <div class="flex items-center gap-1.5 flex-shrink-0">

                                            <a
                                                href="detail.php?id=<?= $row['id']; ?>"
                                                class="inline-flex items-center gap-1.5 bg-[#542f1b] hover:bg-[#432515] text-white font-bold text-xs px-3 py-2 rounded-lg transition">

                                                Baca

                                                <i class="fa-solid fa-arrow-right text-[10px]"></i>

                                            </a>

                                            <a
                                                href="update.php?id=<?= $row['id']; ?>"
                                                class="w-8 h-8 inline-flex items-center justify-center text-stone-500 hover:text-[#542f1b] hover:bg-stone-100 rounded-lg transition"
                                                title="Sunting">

                                                <i class="fa-solid fa-pen-to-square text-xs"></i>

                                            </a>

                                            <a
                                                href="delete.php?id=<?= $row['id']; ?>"
                                                onclick="return confirm('Yakin ingin menghapus kabar ini, Lur?');"
                                                class="w-8 h-8 inline-flex items-center justify-center text-stone-500 hover:text-rose-600 hover:bg-rose-50 rounded-lg transition"
                                                title="Hapus">

                                                <i class="fa-solid fa-trash-can text-xs"></i>

                                            </a>

                                        </div>

                                    </div>

                                </div>

                            </div>

                        </article>

                    <?php endwhile; ?>

                </div>

            <?php else: ?>

                <div class="bg-white border border-stone-200 rounded-2xl p-10 md:p-14 text-center shadow-sm">

                    <div class="w-16 h-16 mx-auto rounded-2xl bg-amber-50 border border-amber-200/70 text-amber-800 flex items-center justify-center mb-5">

                        <i class="fa-solid fa-magnifying-glass text-xl"></i>

                    </div>

                    <?php if (!empty($search) || !empty($kategori)): ?>

                        <p class="text-xs font-bold uppercase tracking-[0.16em] text-amber-800 mb-2">

                            Tidak Ada Hasil

                        </p>

                        <h3 class="text-xl font-black text-stone-900">

                            Kabar yang dicari belum ditemukan

                        </h3>

                        <p class="text-sm text-stone-500 mt-2 max-w-md mx-auto leading-relaxed">

                            Coba gunakan kata kunci lain atau pilih kategori yang berbeda untuk menemukan kabar yang sesuai.

                        </p>

                        <a
                            href="index.php"
                            class="inline-flex items-center gap-2 mt-6 bg-stone-100 hover:bg-stone-200 border border-stone-200 text-stone-700 font-bold text-sm px-5 py-2.5 rounded-xl transition">

                            <i class="fa-solid fa-rotate-right text-xs"></i>

                            Reset Pencarian

                        </a>

                    <?php else: ?>

                        <p class="text-xs font-bold uppercase tracking-[0.16em] text-amber-800 mb-2">

                            Belum Ada Warta

                        </p>

                        <h3 class="text-xl font-black text-stone-900">

                            Belum ada kabar yang dibagikan

                        </h3>

                        <p class="text-sm text-stone-500 mt-2 max-w-md mx-auto leading-relaxed">

                            Ruang ini masih kosong. Mulai reriungan dengan membagikan kabar, gagasan, atau cerita pertamamu.

                        </p>

                        <a
                            href="create.php"
                            class="inline-flex items-center gap-2 mt-6 bg-[#542f1b] hover:bg-[#432515] text-white font-bold text-sm px-5 py-2.5 rounded-xl transition shadow-sm">

                            <i class="fa-solid fa-pen-to-square text-xs"></i>

                            Tulis Kabar Pertama

                        </a>

                    <?php endif; ?>

                </div>

            <?php endif; ?>

        </section>

        <section class="max-w-5xl mx-auto px-5 pb-14">

            <div class="bg-amber-50 border border-amber-200/70 rounded-2xl px-6 py-5 flex flex-col md:flex-row md:items-center md:justify-between gap-4">

                <div class="flex items-start gap-3">

                    <div class="w-9 h-9 rounded-lg bg-amber-100 text-amber-800 flex items-center justify-center flex-shrink-0">

                        <i class="fa-solid fa-mug-hot text-sm"></i>

                    </div>

                    <div>

                        <p class="text-sm font-bold text-amber-950">

                            Punya cerita untuk dibagikan?

                        </p>

                        <p class="text-xs text-amber-900/70 mt-0.5">

                            Tidak harus besar. Yang penting layak untuk dibicarakan.

                        </p>

                    </div>

                </div>

                <a
                    href="create.php"
                    class="inline-flex items-center justify-center gap-2 bg-[#542f1b] hover:bg-[#432515] text-white font-bold text-xs px-4 py-2.5 rounded-lg transition">

                    Tulis Kabar

                    <i class="fa-solid fa-arrow-right text-[10px]"></i>

                </a>

            </div>

        </section>

    </main>

    <?php
    include 'assets/footer.php'
    ?>

    <?php if (isset($_GET['status'])): ?>

        <script>
            const urlParams = new URLSearchParams(window.location.search);
            const status = urlParams.get('status');

            if (status) {
                const messages = {
                    sukses: 'Kabar baru berhasil disebarkan, Lur.',
                    update: 'Perubahan kabar berhasil disimpan.',
                    hapus: 'Kabar berhasil dihapus dari beranda.'
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
                        <button type="button" class="text-stone-400 hover:text-white transition px-1" aria-label="Tutup">
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

                    window.history.replaceState({}, document.title, window.location.pathname);
                }
            }
        </script>

    <?php endif; ?>

</body>

</html>