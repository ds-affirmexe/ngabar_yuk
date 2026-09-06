<?php

session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit;
}

require_once '../../config.php';

$error = '';
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

$usersQuery = mysqli_query(
    $conn,
    "SELECT id, nama, username, role FROM users WHERE status = 'aktif' ORDER BY nama ASC"
);

$users = [];

if ($usersQuery) {
    while ($user = mysqli_fetch_assoc($usersQuery)) {
        $users[] = $user;
    }
}

if (isset($_POST['submit'])) {
    $judul = trim($_POST['judul']);
    $kategori = trim($_POST['kategori']);
    $penulis = trim($_POST['penulis']);
    $konten = trim($_POST['konten']);
    $gambar = $berita['gambar'];

    if (empty($judul) || empty($kategori) || empty($penulis) || empty($konten)) {
        $error = "Waduh, semua kolom wajib diisi ya, Lur!";
    } else {
        if (isset($_FILES['gambar']) && $_FILES['gambar']['error'] === 0) {
            $namaFile = $_FILES['gambar']['name'];
            $ukuranFile = $_FILES['gambar']['size'];
            $tmpName = $_FILES['gambar']['tmp_name'];

            $ekstensiValid = ['jpg', 'jpeg', 'png', 'webp'];
            $ekstensiGambar = explode('.', $namaFile);
            $ekstensiGambar = strtolower(end($ekstensiGambar));

            if (!in_array($ekstensiGambar, $ekstensiValid)) {
                $error = "Ekstensi gambar tidak valid! Gunakan JPG, JPEG, PNG, atau WEBP.";
            } elseif ($ukuranFile > 2 * 1024 * 1024) {
                $error = "Ukuran gambar terlalu besar, Lur! Maksimal 2MB.";
            } else {
                $namaFileBaru = uniqid() . '.' . $ekstensiGambar;
                $tujuan = '../../assets/img/' . $namaFileBaru;

                if (move_uploaded_file($tmpName, $tujuan)) {
                    if (!empty($berita['gambar']) && file_exists('../../assets/img/' . $berita['gambar'])) {
                        unlink('../../assets/img/' . $berita['gambar']);
                    }

                    $gambar = $namaFileBaru;
                } else {
                    $error = "Gagal mengunggah gambar baru.";
                }
            }
        }

        if (empty($error)) {
            preg_match_all('/\S+/', $konten, $matches);
            $jumlahKata = count($matches[0]);
            $read_time = max(1, ceil($jumlahKata / 200));

            $stmt = mysqli_prepare(
                $conn,
                "UPDATE berita SET judul = ?, kategori = ?, penulis = ?, konten = ?, gambar = ?, read_time = ? WHERE id = ?"
            );

            mysqli_stmt_bind_param(
                $stmt,
                "ssssssi",
                $judul,
                $kategori,
                $penulis,
                $konten,
                $gambar,
                $read_time,
                $id
            );

            if (mysqli_stmt_execute($stmt)) {
                header("Location: index.php?status=update");
                exit;
            } else {
                $error = "Gagal memperbarui kabar ke database: " . mysqli_error($conn);
            }

            mysqli_stmt_close($stmt);
        }
    }
}

$selectedKategori = isset($_POST['kategori'])
    ? $_POST['kategori']
    : $berita['kategori'];

$selectedPenulis = isset($_POST['penulis'])
    ? $_POST['penulis']
    : $berita['penulis'];

$editorContent = isset($_POST['konten'])
    ? $_POST['konten']
    : $berita['konten'];

?>

<!DOCTYPE html>
<html lang="id">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Sunting Kabar - Ngabar Yuk! Webmaster</title>

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

        .upload-area {
            transition:
                border-color 180ms ease,
                background-color 180ms ease;
        }

        .upload-area:hover {
            border-color: rgb(217 119 6);
            background-color: rgb(255 251 235);
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

        .editor-shell {
            border: 1px solid rgb(231 229 228);
            border-radius: 1rem;
            overflow: hidden;
            background: white;
        }

        .editor-toolbar {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            gap: 0.25rem;
            padding: 0.65rem;
            background: rgb(250 250 249);
            border-bottom: 1px solid rgb(231 229 228);
        }

        .toolbar-button {
            width: 34px;
            height: 34px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border: 1px solid transparent;
            border-radius: 0.625rem;
            color: rgb(87 83 78);
            background: transparent;
            font-size: 0.8rem;
            transition: all 150ms ease;
        }

        .toolbar-button:hover {
            background: rgb(245 245 244);
            color: #542f1b;
        }

        .toolbar-button.active {
            background: rgb(254 243 199);
            color: #78350f;
            border-color: rgb(245 158 11);
            box-shadow: inset 0 0 0 1px rgba(245, 158, 11, 0.12);
        }

        .toolbar-divider {
            width: 1px;
            height: 24px;
            background: rgb(231 229 228);
            margin: 0 0.2rem;
        }

        .toolbar-select {
            height: 34px;
            border: 1px solid rgb(231 229 228);
            border-radius: 0.625rem;
            background: white;
            color: rgb(87 83 78);
            padding: 0 0.65rem;
            font-size: 0.75rem;
            font-weight: 600;
            outline: none;
        }

        .toolbar-select:focus {
            border-color: rgb(217 119 6);
            box-shadow: 0 0 0 3px rgba(217, 119, 6, 0.1);
        }

        .toolbar-select.active {
            background: rgb(255 251 235);
            border-color: rgb(245 158 11);
            color: #78350f;
        }

        .editor-content {
            min-height: 360px;
            max-height: 650px;
            overflow-y: auto;
            outline: none;
            font-size: 0.875rem;
            line-height: 1.75;
            color: rgb(41 37 36);
        }

        .editor-content:empty::before {
            content: attr(data-placeholder);
            color: rgb(168 162 158);
            pointer-events: none;
        }

        .editor-content p {
            margin: 0 0 0.75rem;
        }

        .editor-content h2 {
            font-size: 1.5rem;
            line-height: 1.35;
            font-weight: 800;
            margin: 1.25rem 0 0.75rem;
            color: rgb(41 37 36);
        }

        .editor-content h3 {
            font-size: 1.25rem;
            line-height: 1.4;
            font-weight: 800;
            margin: 1.1rem 0 0.65rem;
            color: rgb(41 37 36);
        }

        .editor-content h4 {
            font-size: 1.1rem;
            line-height: 1.45;
            font-weight: 800;
            margin: 1rem 0 0.6rem;
            color: rgb(41 37 36);
        }

        .editor-content ul {
            list-style: disc;
            padding-left: 1.5rem;
            margin: 0.75rem 0;
        }

        .editor-content ol {
            list-style: decimal;
            padding-left: 1.5rem;
            margin: 0.75rem 0;
        }

        .editor-content li {
            margin: 0.25rem 0;
        }

        .editor-content blockquote {
            border-left: 4px solid rgb(217 119 6);
            background: rgb(255 251 235);
            color: rgb(120 113 108);
            padding: 0.75rem 1rem;
            margin: 1rem 0;
            border-radius: 0 0.75rem 0.75rem 0;
            font-style: italic;
        }

        .editor-content a {
            color: rgb(146 64 14);
            text-decoration: underline;
            text-underline-offset: 2px;
        }

        .editor-content img {
            max-width: 100%;
            height: auto;
            border-radius: 0.75rem;
        }

        .editor-content strong {
            font-weight: 800;
        }

        .editor-content em {
            font-style: italic;
        }

        .editor-content u {
            text-decoration: underline;
            text-underline-offset: 2px;
        }
    </style>

</head>

<body class="bg-stone-50 text-stone-800 font-sans antialiased flex flex-col min-h-screen selection:bg-amber-200 selection:text-amber-900">

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

                        <a
                            href="../dashboard.php"
                            class="inline-flex items-center gap-2 text-stone-200 hover:text-amber-300 font-semibold text-sm px-3 py-2.5 rounded-xl hover:bg-white/5 transition">

                            <i class="fa-solid fa-gauge-high text-xs"></i>

                            <span class="hidden sm:inline">
                                Dashboard
                            </span>

                        </a>

                        <a
                            href="../profile.php"
                            class="hidden sm:inline-flex items-center gap-2 text-stone-200 hover:text-amber-300 font-semibold text-sm px-3 py-2.5 rounded-xl hover:bg-white/5 transition">

                            <i class="fa-solid fa-user text-xs"></i>

                            Profil

                        </a>

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

    <main class="max-w-5xl w-full mx-auto px-5 py-8 md:py-10 flex-grow">

        <div class="grid grid-cols-1 lg:grid-cols-[1fr_300px] gap-6 items-start">

            <div>

                <div class="mb-6">

                    <div class="inline-flex items-center gap-2 text-amber-800 bg-amber-50 border border-amber-100 px-3 py-1.5 rounded-full text-[10px] font-bold uppercase tracking-[0.15em] mb-3">

                        <i class="fa-solid fa-pen-to-square"></i>

                        Penyuntingan Kabar

                    </div>

                    <h1 class="text-2xl md:text-3xl font-black text-[#542f1b] tracking-tight">
                        Sunting Kabar, Lur!
                    </h1>

                    <p class="text-sm text-stone-500 mt-2 leading-relaxed">
                        Perbarui informasi atau koreksi warta yang sudah pernah dibagikan.
                    </p>

                </div>

                <div class="bg-white rounded-3xl border border-stone-200 shadow-sm overflow-hidden">

                    <div class="px-6 md:px-8 py-4 border-b border-stone-100 bg-stone-50/60 flex items-center justify-between">

                        <div class="flex items-center gap-3">

                            <div class="w-9 h-9 rounded-xl bg-amber-100 text-amber-800 flex items-center justify-center">

                                <i class="fa-solid fa-file-pen text-sm"></i>

                            </div>

                            <div>

                                <p class="text-sm font-black text-stone-800">
                                    Perbarui Kabar
                                </p>

                                <p class="text-[11px] text-stone-400">
                                    Perubahan akan disimpan pada kabar ini
                                </p>

                            </div>

                        </div>

                        <span class="hidden sm:inline-flex items-center gap-1.5 text-[10px] font-semibold text-stone-400">

                            <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>

                            Mode sunting

                        </span>

                    </div>

                    <div class="p-6 md:p-8">

                        <?php if (!empty($error)): ?>

                            <div
                                id="alert-box"
                                class="fade-up bg-rose-50 border border-rose-200 text-rose-700 p-4 mb-6 rounded-2xl text-sm flex items-center justify-between gap-4">

                                <div class="flex items-center gap-3">

                                    <div class="w-9 h-9 shrink-0 rounded-xl bg-rose-100 text-rose-600 flex items-center justify-center">

                                        <i class="fa-solid fa-triangle-exclamation"></i>

                                    </div>

                                    <div>

                                        <p class="text-[10px] uppercase tracking-[0.14em] font-bold text-rose-600">
                                            Perlu diperiksa
                                        </p>

                                        <span id="alert-text" class="text-sm">
                                            <?= htmlspecialchars($error); ?>
                                        </span>

                                    </div>

                                </div>

                                <button
                                    type="button"
                                    id="close-alert"
                                    class="w-8 h-8 shrink-0 rounded-lg text-rose-500 hover:text-rose-800 hover:bg-rose-100 transition flex items-center justify-center">

                                    <i class="fa-solid fa-xmark"></i>

                                </button>

                            </div>

                        <?php endif; ?>

                        <form
                            id="form-berita"
                            action=""
                            method="POST"
                            enctype="multipart/form-data"
                            class="space-y-6">

                            <div>

                                <label class="block text-sm font-bold text-stone-700 mb-2">

                                    <i class="fa-solid fa-heading mr-1.5 text-amber-800"></i>

                                    Judul Kabar

                                    <span class="text-rose-500">*</span>

                                </label>

                                <input
                                    type="text"
                                    id="input-judul"
                                    name="judul"
                                    value="<?= isset($_POST['judul']) ? htmlspecialchars($_POST['judul']) : htmlspecialchars($berita['judul']); ?>"
                                    required
                                    maxlength="255"
                                    class="form-field w-full px-4 py-3 bg-stone-50 border border-stone-200 rounded-xl text-sm text-stone-800 placeholder:text-stone-400 focus:outline-none focus:border-amber-600 focus:ring-4 focus:ring-amber-600/10">

                                <div class="flex items-center justify-between mt-1.5">

                                    <span class="text-[11px] text-stone-400">
                                        Pastikan judul tetap relevan dengan isi.
                                    </span>

                                    <span
                                        id="judul-counter"
                                        class="text-[11px] text-stone-400 shrink-0">
                                        Sisa karakter: 255
                                    </span>

                                </div>

                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">

                                <div>

                                    <label class="block text-sm font-bold text-stone-700 mb-2">

                                        <i class="fa-solid fa-tag mr-1.5 text-amber-800"></i>

                                        Kategori

                                        <span class="text-rose-500">*</span>

                                    </label>

                                    <div class="relative">

                                        <select
                                            name="kategori"
                                            required
                                            class="form-field appearance-none w-full px-4 py-3 pr-10 bg-stone-50 border border-stone-200 rounded-xl text-sm text-stone-700 focus:outline-none focus:border-amber-600 focus:ring-4 focus:ring-amber-600/10 cursor-pointer">

                                            <option value="Insight" <?= ($selectedKategori === 'Insight') ? 'selected' : ''; ?>>
                                                Insight / Opini
                                            </option>

                                            <option value="Lokal" <?= ($selectedKategori === 'Lokal') ? 'selected' : ''; ?>>
                                                Warta Lokal
                                            </option>

                                            <option value="Budaya" <?= ($selectedKategori === 'Budaya') ? 'selected' : ''; ?>>
                                                Budaya & Tradisi
                                            </option>

                                            <option value="Gaya Urip" <?= ($selectedKategori === 'Gaya Urip') ? 'selected' : ''; ?>>
                                                Gaya Urip
                                            </option>

                                        </select>

                                        <i class="fa-solid fa-chevron-down absolute right-4 top-1/2 -translate-y-1/2 text-[10px] text-stone-400 pointer-events-none"></i>

                                    </div>

                                </div>

                                <div>

                                    <label class="block text-sm font-bold text-stone-700 mb-2">

                                        <i class="fa-solid fa-user-pen mr-1.5 text-amber-800"></i>

                                        Nama Penulis / Pangarang

                                        <span class="text-rose-500">*</span>

                                    </label>

                                    <div class="relative">

                                        <select
                                            name="penulis"
                                            required
                                            class="form-field appearance-none w-full px-4 py-3 pr-10 bg-stone-50 border border-stone-200 rounded-xl text-sm text-stone-700 focus:outline-none focus:border-amber-600 focus:ring-4 focus:ring-amber-600/10 cursor-pointer">

                                            <option value="">
                                                Pilih penulis
                                            </option>

                                            <?php foreach ($users as $user): ?>

                                                <option
                                                    value="<?= htmlspecialchars($user['nama']); ?>"
                                                    <?= ($selectedPenulis === $user['nama']) ? 'selected' : ''; ?>>

                                                    <?= htmlspecialchars($user['nama']); ?>

                                                    <?php if ($user['role'] === 'super_admin'): ?>

                                                        — Super Admin

                                                    <?php else: ?>

                                                        — Admin

                                                    <?php endif; ?>

                                                </option>

                                            <?php endforeach; ?>

                                        </select>

                                        <i class="fa-solid fa-chevron-down absolute right-4 top-1/2 -translate-y-1/2 text-[10px] text-stone-400 pointer-events-none"></i>

                                    </div>

                                </div>

                            </div>

                            <div>

                                <div class="flex items-end justify-between gap-3 mb-2">

                                    <label class="block text-sm font-bold text-stone-700">

                                        <i class="fa-solid fa-image mr-1.5 text-amber-800"></i>

                                        Foto Utama

                                    </label>

                                    <span class="text-[10px] text-stone-400">
                                        Opsional
                                    </span>

                                </div>

                                <?php if (!empty($berita['gambar']) && file_exists('../../assets/img/' . $berita['gambar'])): ?>

                                    <div class="bg-stone-50 border border-stone-200 rounded-2xl p-4 mb-4">

                                        <div class="flex items-center gap-4">

                                            <div class="relative shrink-0">

                                                <img
                                                    src="../../assets/img/<?= htmlspecialchars($berita['gambar']); ?>"
                                                    alt="Foto Lama"
                                                    class="w-24 h-24 object-cover rounded-xl border border-stone-200 shadow-sm">

                                                <span class="absolute -bottom-2 left-1/2 -translate-x-1/2 whitespace-nowrap bg-stone-800 text-white text-[9px] font-bold uppercase tracking-wide px-2 py-1 rounded-md">
                                                    Saat Ini
                                                </span>

                                            </div>

                                            <div>

                                                <p class="text-xs font-bold text-stone-700">
                                                    Foto yang sedang digunakan
                                                </p>

                                                <p class="text-[11px] text-stone-400 mt-1 leading-relaxed">
                                                    Biarkan kosong jika foto ini tetap ingin digunakan.
                                                </p>

                                            </div>

                                        </div>

                                    </div>

                                <?php endif; ?>

                                <label
                                    for="input-gambar"
                                    class="upload-area block border-2 border-dashed border-stone-200 rounded-2xl bg-stone-50 px-5 py-6 cursor-pointer">

                                    <div class="flex flex-col sm:flex-row sm:items-center gap-4">

                                        <div class="w-12 h-12 shrink-0 rounded-xl bg-amber-100 text-amber-800 flex items-center justify-center">

                                            <i class="fa-solid fa-image text-lg"></i>

                                        </div>

                                        <div class="min-w-0">

                                            <p class="text-sm font-bold text-stone-700">
                                                Ganti dengan foto baru
                                            </p>

                                            <p class="text-xs text-stone-400 mt-1 leading-relaxed">
                                                Pilih foto jika ingin mengganti foto utama.
                                                JPG, JPEG, PNG, atau WEBP. Maks. 2MB.
                                            </p>

                                        </div>

                                        <span class="sm:ml-auto inline-flex items-center justify-center bg-white border border-stone-200 text-stone-600 text-xs font-semibold px-3 py-2 rounded-lg shadow-sm">
                                            Pilih File
                                        </span>

                                    </div>

                                    <input
                                        type="file"
                                        id="input-gambar"
                                        name="gambar"
                                        accept="image/jpeg,image/png,image/webp"
                                        class="hidden">

                                </label>

                                <div id="preview-container" class="mt-4 hidden">

                                    <div class="bg-amber-50 border border-amber-100 rounded-2xl p-4">

                                        <div class="flex items-center gap-4">

                                            <div class="relative shrink-0">

                                                <img
                                                    id="image-preview"
                                                    src="#"
                                                    alt="Pratinjau Gambar Baru"
                                                    class="w-24 h-24 object-cover rounded-xl border border-amber-200 shadow-sm">

                                                <button
                                                    type="button"
                                                    id="remove-image"
                                                    class="absolute -top-2 -right-2 bg-rose-600 text-white rounded-full w-6 h-6 flex items-center justify-center text-xs shadow hover:bg-rose-700 transition">

                                                    <i class="fa-solid fa-xmark"></i>

                                                </button>

                                            </div>

                                            <div>

                                                <p class="text-xs font-bold text-amber-900">
                                                    Foto pengganti
                                                </p>

                                                <p class="text-[11px] text-amber-800/70 mt-1 leading-relaxed">
                                                    Foto ini akan menggantikan foto utama yang sekarang.
                                                </p>

                                            </div>

                                        </div>

                                    </div>

                                </div>

                            </div>

                            <div>

                                <div class="flex items-center justify-between gap-3 mb-2">

                                    <label class="block text-sm font-bold text-stone-700">

                                        <i class="fa-solid fa-align-left mr-1.5 text-amber-800"></i>

                                        Isi Berita / Insight

                                        <span class="text-rose-500">*</span>

                                    </label>

                                    <span
                                        id="read-time-preview"
                                        class="inline-flex items-center gap-1.5 text-[10px] font-semibold text-stone-400 bg-stone-50 border border-stone-200 px-2.5 py-1 rounded-lg">

                                        <i class="fa-regular fa-clock"></i>

                                        1 menit baca

                                    </span>

                                </div>

                                <div class="editor-shell">

                                    <div
                                        class="editor-toolbar"
                                        id="editor-toolbar">

                                        <button
                                            type="button"
                                            class="toolbar-button"
                                            data-command="bold"
                                            title="Tebal">

                                            <i class="fa-solid fa-bold"></i>

                                        </button>

                                        <button
                                            type="button"
                                            class="toolbar-button"
                                            data-command="italic"
                                            title="Miring">

                                            <i class="fa-solid fa-italic"></i>

                                        </button>

                                        <button
                                            type="button"
                                            class="toolbar-button"
                                            data-command="underline"
                                            title="Garis bawah">

                                            <i class="fa-solid fa-underline"></i>

                                        </button>

                                        <div class="toolbar-divider"></div>

                                        <select
                                            id="format-block"
                                            class="toolbar-select"
                                            title="Format teks">

                                            <option value="p">
                                                Paragraph
                                            </option>

                                            <option value="h2">
                                                Heading 2
                                            </option>

                                            <option value="h3">
                                                Heading 3
                                            </option>

                                            <option value="h4">
                                                Heading 4
                                            </option>

                                        </select>

                                        <div class="toolbar-divider"></div>

                                        <button
                                            type="button"
                                            class="toolbar-button"
                                            data-command="insertUnorderedList"
                                            title="Daftar bullet">

                                            <i class="fa-solid fa-list-ul"></i>

                                        </button>

                                        <button
                                            type="button"
                                            class="toolbar-button"
                                            data-command="insertOrderedList"
                                            title="Daftar bernomor">

                                            <i class="fa-solid fa-list-ol"></i>

                                        </button>

                                        <button
                                            type="button"
                                            class="toolbar-button"
                                            data-command="formatBlock"
                                            data-value="blockquote"
                                            title="Kutipan">

                                            <i class="fa-solid fa-quote-left"></i>

                                        </button>

                                        <div class="toolbar-divider"></div>

                                        <button
                                            type="button"
                                            class="toolbar-button"
                                            data-command="justifyLeft"
                                            title="Rata kiri">

                                            <i class="fa-solid fa-align-left"></i>

                                        </button>

                                        <button
                                            type="button"
                                            class="toolbar-button"
                                            data-command="justifyCenter"
                                            title="Rata tengah">

                                            <i class="fa-solid fa-align-center"></i>

                                        </button>

                                        <button
                                            type="button"
                                            class="toolbar-button"
                                            data-command="justifyRight"
                                            title="Rata kanan">

                                            <i class="fa-solid fa-align-right"></i>

                                        </button>

                                        <button
                                            type="button"
                                            class="toolbar-button"
                                            data-command="justifyFull"
                                            title="Rata kiri-kanan">

                                            <i class="fa-solid fa-align-justify"></i>

                                        </button>

                                        <div class="toolbar-divider"></div>

                                        <button
                                            type="button"
                                            class="toolbar-button"
                                            id="create-link"
                                            title="Tambahkan link">

                                            <i class="fa-solid fa-link"></i>

                                        </button>

                                        <button
                                            type="button"
                                            class="toolbar-button"
                                            data-command="undo"
                                            title="Urungkan">

                                            <i class="fa-solid fa-rotate-left"></i>

                                        </button>

                                        <button
                                            type="button"
                                            class="toolbar-button"
                                            data-command="redo"
                                            title="Ulangi">

                                            <i class="fa-solid fa-rotate-right"></i>

                                        </button>

                                    </div>

                                    <div
                                        id="editor-content"
                                        class="editor-content px-4 py-4 bg-stone-50"
                                        contenteditable="true"
                                        data-placeholder="Tuliskan berita atau ulasan mendalammu di sini..."><?= $editorContent; ?></div>

                                </div>

                                <textarea
                                    name="konten"
                                    id="input-konten"
                                    class="hidden"></textarea>

                                <div class="flex items-center justify-between gap-3 mt-1.5">

                                    <p class="text-[11px] text-stone-400">
                                        Gunakan toolbar untuk mengatur tampilan isi kabar.
                                    </p>

                                    <p
                                        id="word-counter"
                                        class="text-[11px] text-stone-400 shrink-0">
                                        0 kata
                                    </p>

                                </div>

                            </div>

                            <div class="pt-4 border-t border-stone-100 flex flex-col-reverse sm:flex-row sm:items-center sm:justify-between gap-3">

                                <p class="text-[11px] text-stone-400">

                                    <i class="fa-solid fa-circle-info mr-1"></i>

                                    Kolom bertanda
                                    <span class="text-rose-500">*</span>
                                    wajib diisi.

                                </p>

                                <div class="flex items-center justify-end gap-2">

                                    <a
                                        href="index.php"
                                        class="inline-flex items-center justify-center gap-2 px-4 py-2.5 border border-stone-200 bg-white text-stone-600 rounded-xl font-semibold text-sm hover:bg-stone-100 transition">

                                        <i class="fa-solid fa-xmark text-xs"></i>

                                        Batal

                                    </a>

                                    <button
                                        type="submit"
                                        name="submit"
                                        class="inline-flex items-center justify-center gap-2 bg-[#542f1b] hover:bg-[#452515] text-white font-bold px-5 py-2.5 rounded-xl transition-all duration-200 shadow-sm hover:shadow-md hover:-translate-y-0.5 text-sm">

                                        <i class="fa-solid fa-floppy-disk text-xs"></i>

                                        Simpan Perubahan

                                    </button>

                                </div>

                            </div>

                        </form>

                    </div>

                </div>

            </div>

            <aside class="space-y-4 lg:sticky lg:top-24">

                <div class="bg-[#542f1b] text-white rounded-3xl p-6 shadow-lg shadow-stone-900/10 javanese-pattern">

                    <div class="w-10 h-10 rounded-xl bg-amber-400 text-[#542f1b] flex items-center justify-center mb-4">

                        <i class="fa-solid fa-file-pen"></i>

                    </div>

                    <p class="text-[10px] uppercase tracking-[0.16em] font-bold text-amber-300">
                        Mode Sunting
                    </p>

                    <h2 class="text-lg font-black mt-1">
                        Periksa sebelum disimpan.
                    </h2>

                    <p class="text-xs text-stone-300 leading-relaxed mt-3">
                        Pastikan perubahan judul, kategori, penulis, isi,
                        maupun foto sudah sesuai sebelum kabar diperbarui.
                    </p>

                </div>

                <div class="bg-white border border-stone-200 rounded-2xl p-5 shadow-sm">

                    <p class="text-xs font-black text-[#542f1b] mb-4">

                        <i class="fa-solid fa-clipboard-check mr-1.5 text-amber-700"></i>

                        Checklist Perubahan

                    </p>

                    <div class="space-y-3">

                        <div class="flex gap-3">

                            <span class="w-6 h-6 shrink-0 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center text-[10px]">

                                <i class="fa-solid fa-check"></i>

                            </span>

                            <p class="text-xs text-stone-500 leading-relaxed">
                                Judul tetap sesuai dengan isi kabar.
                            </p>

                        </div>

                        <div class="flex gap-3">

                            <span class="w-6 h-6 shrink-0 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center text-[10px]">

                                <i class="fa-solid fa-check"></i>

                            </span>

                            <p class="text-xs text-stone-500 leading-relaxed">
                                Kategori sudah tepat.
                            </p>

                        </div>

                        <div class="flex gap-3">

                            <span class="w-6 h-6 shrink-0 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center text-[10px]">

                                <i class="fa-solid fa-check"></i>

                            </span>

                            <p class="text-xs text-stone-500 leading-relaxed">
                                Penulis dipilih dari akun Webmaster aktif.
                            </p>

                        </div>

                        <div class="flex gap-3">

                            <span class="w-6 h-6 shrink-0 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center text-[10px]">

                                <i class="fa-solid fa-check"></i>

                            </span>

                            <p class="text-xs text-stone-500 leading-relaxed">
                                Isi sudah diperiksa kembali.
                            </p>

                        </div>

                        <div class="flex gap-3">

                            <span class="w-6 h-6 shrink-0 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center text-[10px]">

                                <i class="fa-solid fa-check"></i>

                            </span>

                            <p class="text-xs text-stone-500 leading-relaxed">
                                Foto baru maksimal 2MB jika diganti.
                            </p>

                        </div>

                    </div>

                </div>

                <div class="bg-amber-50 border border-amber-100 rounded-2xl p-5">

                    <div class="flex gap-3">

                        <div class="w-8 h-8 shrink-0 rounded-lg bg-amber-100 text-amber-800 flex items-center justify-center">

                            <i class="fa-solid fa-lightbulb text-xs"></i>

                        </div>

                        <div>

                            <p class="text-xs font-bold text-amber-900">
                                Estimasi Waktu Baca
                            </p>

                            <p class="text-[11px] text-amber-800/70 leading-relaxed mt-1">
                                Estimasi diperbarui otomatis berdasarkan jumlah kata
                                dalam isi kabar.
                            </p>

                        </div>

                    </div>

                </div>

                <div class="px-1 text-[11px] text-stone-400 leading-relaxed">

                    <i class="fa-solid fa-quote-left text-amber-700 mr-1"></i>

                    Perubahan kecil tetap perlu diperiksa sebelum dibagikan kembali.

                </div>

            </aside>

        </div>

    </main>

    <footer class="bg-[#3a2113] text-stone-300 mt-12">

        <div class="max-w-5xl mx-auto px-5">

            <div class="py-8 flex flex-col md:flex-row md:items-center md:justify-between gap-7">

                <div>

                    <a
                        href="../dashboard.php"
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
                        Panel pengelolaan kabar untuk mengatur,
                        menyunting, dan menerbitkan warta.
                    </p>

                </div>

                <div class="flex flex-col sm:flex-row sm:items-center gap-5">

                    <div class="flex flex-wrap items-center gap-x-5 gap-y-2 text-xs">

                        <a
                            href="../dashboard.php"
                            class="hover:text-amber-300 transition">
                            Dashboard
                        </a>

                        <a
                            href="../profile.php"
                            class="hover:text-amber-300 transition">
                            Profil
                        </a>

                        <a
                            href="index.php"
                            class="hover:text-amber-300 transition">
                            Kelola Berita
                        </a>

                    </div>

                    <a
                        href="../../index.php"
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

    <script>
        const alertBox = document.getElementById('alert-box');
        const closeAlert = document.getElementById('close-alert');

        if (closeAlert && alertBox) {
            closeAlert.addEventListener('click', function() {
                alertBox.style.opacity = '0';
                alertBox.style.transform = 'translateY(-5px)';
                alertBox.style.transition = 'opacity 250ms ease, transform 250ms ease';

                setTimeout(() => {
                    alertBox.style.display = 'none';
                }, 250);
            });
        }

        const inputJudul = document.getElementById('input-judul');
        const judulCounter = document.getElementById('judul-counter');
        const maxLength = 255;

        if (inputJudul) {
            const updateCounter = () => {
                const sisa = maxLength - inputJudul.value.length;

                judulCounter.textContent = 'Sisa karakter: ' + sisa;

                if (sisa < 20) {
                    judulCounter.classList.add('text-rose-600', 'font-semibold');
                    judulCounter.classList.remove('text-stone-400');
                } else {
                    judulCounter.classList.remove('text-rose-600', 'font-semibold');
                    judulCounter.classList.add('text-stone-400');
                }
            };

            inputJudul.addEventListener('input', updateCounter);
            updateCounter();
        }

        const inputGambar = document.getElementById('input-gambar');
        const previewContainer = document.getElementById('preview-container');
        const imagePreview = document.getElementById('image-preview');
        const removeImageBtn = document.getElementById('remove-image');

        if (inputGambar) {
            inputGambar.addEventListener('change', function(event) {
                const file = event.target.files[0];

                if (file) {
                    if (file.size > 2 * 1024 * 1024) {
                        alert('Ukuran file terlalu besar! Maksimal 2MB.');
                        inputGambar.value = '';
                        previewContainer.classList.add('hidden');
                        return;
                    }

                    const reader = new FileReader();

                    reader.onload = function(e) {
                        imagePreview.src = e.target.result;
                        previewContainer.classList.remove('hidden');
                    };

                    reader.readAsDataURL(file);
                } else {
                    previewContainer.classList.add('hidden');
                }
            });
        }

        if (removeImageBtn) {
            removeImageBtn.addEventListener('click', function() {
                inputGambar.value = '';
                imagePreview.src = '#';
                previewContainer.classList.add('hidden');
            });
        }

        const formBerita = document.getElementById('form-berita');
        const editorContent = document.getElementById('editor-content');
        const inputKonten = document.getElementById('input-konten');
        const wordCounter = document.getElementById('word-counter');
        const readTimePreview = document.getElementById('read-time-preview');
        const formatBlock = document.getElementById('format-block');
        const toolbar = document.getElementById('editor-toolbar');
        const toolbarButtons = toolbar ? toolbar.querySelectorAll('.toolbar-button') : [];

        let savedRange = null;
        let toolbarUpdateFrame = null;

        function isEditorSelection(selection) {
            if (!selection || selection.rangeCount === 0 || !editorContent) {
                return false;
            }

            const range = selection.getRangeAt(0);
            return editorContent.contains(range.commonAncestorContainer);
        }

        function saveSelection() {
            const selection = window.getSelection();

            if (!isEditorSelection(selection)) {
                return;
            }

            savedRange = selection.getRangeAt(0).cloneRange();
        }

        function restoreSelection() {
            if (!editorContent) {
                return false;
            }

            editorContent.focus({
                preventScroll: true
            });

            if (!savedRange) {
                return false;
            }

            const selection = window.getSelection();
            selection.removeAllRanges();
            selection.addRange(savedRange);

            return true;
        }

        function updateEditorValue() {
            if (editorContent && inputKonten) {
                inputKonten.value = editorContent.innerHTML;
            }
        }

        function getCurrentBlock() {
            const selection = window.getSelection();

            if (!isEditorSelection(selection)) {
                return null;
            }

            let node = selection.anchorNode;

            if (!node) {
                return null;
            }

            if (node.nodeType === Node.TEXT_NODE) {
                node = node.parentElement;
            }

            if (!node || !editorContent.contains(node)) {
                return null;
            }

            return node.closest('p, h2, h3, h4, blockquote, li');
        }

        function scheduleToolbarUpdate() {
            if (toolbarUpdateFrame) {
                cancelAnimationFrame(toolbarUpdateFrame);
            }

            toolbarUpdateFrame = requestAnimationFrame(function() {
                toolbarUpdateFrame = null;
                updateToolbarState();
            });
        }

        function updateToolbarState() {
            if (!editorContent || document.activeElement !== editorContent) {
                return;
            }

            toolbarButtons.forEach(function(button) {
                const command = button.dataset.command;

                const stateCommands = [
                    'bold',
                    'italic',
                    'underline',
                    'insertUnorderedList',
                    'insertOrderedList',
                    'justifyLeft',
                    'justifyCenter',
                    'justifyRight',
                    'justifyFull'
                ];

                if (!stateCommands.includes(command)) {
                    return;
                }

                try {
                    button.classList.toggle(
                        'active',
                        document.queryCommandState(command)
                    );
                } catch (error) {
                    button.classList.remove('active');
                }
            });

            const block = getCurrentBlock();
            const quoteButton = toolbar.querySelector(
                '[data-command="formatBlock"][data-value="blockquote"]'
            );

            if (!block) {
                formatBlock.value = 'p';
                formatBlock.classList.remove('active');

                if (quoteButton) {
                    quoteButton.classList.remove('active');
                }

                return;
            }

            const tag = block.tagName.toLowerCase();

            if (tag === 'h2' || tag === 'h3' || tag === 'h4') {
                formatBlock.value = tag;
                formatBlock.classList.add('active');
            } else {
                formatBlock.value = 'p';
                formatBlock.classList.toggle(
                    'active',
                    tag === 'blockquote'
                );
            }

            if (quoteButton) {
                quoteButton.classList.toggle(
                    'active',
                    tag === 'blockquote'
                );
            }
        }

        function executeCommand(command, value) {
            if (!editorContent) {
                return;
            }

            restoreSelection();
            editorContent.focus({
                preventScroll: true
            });

            try {
                document.execCommand(
                    command,
                    false,
                    value || null
                );
            } catch (error) {
                return;
            }

            saveSelection();
            updateEditorValue();
            updateReadTime();
            scheduleToolbarUpdate();
        }

        toolbarButtons.forEach(function(button) {
            button.addEventListener('mousedown', function(event) {
                event.preventDefault();
                saveSelection();
            });

            button.addEventListener('click', function(event) {
                event.preventDefault();

                const command = this.dataset.command;
                const value = this.dataset.value || null;

                if (this.id === 'create-link') {
                    restoreSelection();

                    const url = prompt('Masukkan URL tautan:');

                    if (!url) {
                        editorContent.focus({
                            preventScroll: true
                        });
                        return;
                    }

                    try {
                        document.execCommand(
                            'createLink',
                            false,
                            url.trim()
                        );
                    } catch (error) {
                        return;
                    }

                    saveSelection();
                    updateEditorValue();
                    scheduleToolbarUpdate();

                    return;
                }

                executeCommand(command, value);
            });
        });

        if (formatBlock) {
            formatBlock.addEventListener('mousedown', function() {
                saveSelection();
            });

            formatBlock.addEventListener('change', function() {
                const value = this.value;

                restoreSelection();
                editorContent.focus({
                    preventScroll: true
                });

                try {
                    document.execCommand(
                        'formatBlock',
                        false,
                        value
                    );
                } catch (error) {
                    return;
                }

                saveSelection();
                updateEditorValue();
                updateReadTime();
                scheduleToolbarUpdate();
            });
        }

        editorContent.addEventListener('mouseup', function() {
            saveSelection();
            scheduleToolbarUpdate();
        });

        editorContent.addEventListener('keyup', function() {
            saveSelection();
            updateEditorValue();
            updateReadTime();
            scheduleToolbarUpdate();
        });

        editorContent.addEventListener('input', function() {
            saveSelection();
            updateEditorValue();
            updateReadTime();
            scheduleToolbarUpdate();
        });

        editorContent.addEventListener('focus', function() {
            saveSelection();
            scheduleToolbarUpdate();
        });

        editorContent.addEventListener('blur', function() {
            saveSelection();
        });

        editorContent.addEventListener('keydown', function(event) {
            if (event.ctrlKey || event.metaKey) {
                const key = event.key.toLowerCase();

                if (key === 'b' || key === 'i' || key === 'u') {
                    event.preventDefault();

                    executeCommand(
                        key === 'b' ?
                        'bold' :
                        key === 'i' ?
                        'italic' :
                        'underline'
                    );
                }
            }
        });

        editorContent.addEventListener('paste', function(event) {
            event.preventDefault();

            const text = event.clipboardData.getData('text/plain');

            restoreSelection();

            document.execCommand(
                'insertText',
                false,
                text
            );

            saveSelection();
            updateEditorValue();
            updateReadTime();
            scheduleToolbarUpdate();
        });

        function updateReadTime() {
            if (!editorContent || !wordCounter || !readTimePreview) {
                return;
            }

            const text = editorContent.innerText.trim();

            if (!text) {
                wordCounter.textContent = '0 kata';

                readTimePreview.innerHTML =
                    '<i class="fa-regular fa-clock"></i> 1 menit baca';

                return;
            }

            const words = text
                .split(/\s+/)
                .filter(function(word) {
                    return word.length > 0;
                });

            const wordCount = words.length;

            const readTime = Math.max(
                1,
                Math.ceil(wordCount / 200)
            );

            wordCounter.textContent =
                wordCount + ' kata';

            readTimePreview.innerHTML =
                '<i class="fa-regular fa-clock"></i> ' +
                readTime +
                ' menit baca';
        }

        if (formBerita) {
            formBerita.addEventListener('submit', function(event) {
                updateEditorValue();

                const judul = inputJudul.value.trim();

                const kategori = formBerita
                    .querySelector('[name="kategori"]')
                    .value
                    .trim();

                const penulis = formBerita
                    .querySelector('[name="penulis"]')
                    .value
                    .trim();

                const konten = editorContent.innerText.trim();

                if (!judul || !kategori || !penulis || !konten) {
                    alert('Waduh, semua kolom wajib diisi ya, Lur!');
                    event.preventDefault();
                    return;
                }

                inputKonten.value =
                    editorContent.innerHTML;
            });
        }

        updateEditorValue();
        updateReadTime();
        saveSelection();
        scheduleToolbarUpdate();
    </script>

</body>

</html>