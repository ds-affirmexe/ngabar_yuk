<?php

session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: ../login.php');
    exit;
}

require_once '../../config.php';

$error = '';

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

function sanitizeEditorContent($html)
{
    $allowedTags = '<p><br><strong><b><em><i><u><h2><h3><h4><ul><ol><li><blockquote><a><div>';

    $html = strip_tags($html, $allowedTags);

    $html = preg_replace('/\s*on[a-z]+\s*=\s*(".*?"|\'.*?\'|[^\s>]+)/is', '', $html);

    $html = preg_replace(
        '/\sstyle\s*=\s*("|\')(.*?)\1/is',
        '',
        $html
    );

    $html = preg_replace_callback(
        '/<a\b([^>]*)>/i',
        function ($matches) {
            $attributes = $matches[1];

            preg_match('/href\s*=\s*("|\')(.*?)\1/i', $attributes, $hrefMatch);

            if (!empty($hrefMatch[2])) {
                $href = trim($hrefMatch[2]);

                if (
                    preg_match('/^(javascript|data|vbscript):/i', $href) ||
                    !preg_match('/^(https?:\/\/|mailto:|\/|#)/i', $href)
                ) {
                    return '<a>';
                }

                $safeHref = htmlspecialchars($href, ENT_QUOTES, 'UTF-8');

                return '<a href="' . $safeHref . '" target="_blank" rel="noopener noreferrer">';
            }

            return '<a>';
        },
        $html
    );

    return trim($html);
}

if (isset($_POST['submit'])) {

    $judul = trim($_POST['judul']);
    $kategori = trim($_POST['kategori']);
    $penulis = trim($_POST['penulis']);
    $konten = trim($_POST['konten']);

    $gambar = '';

    if (empty($judul) || empty($kategori) || empty($penulis) || empty($konten)) {
        $error = "Waduh, semua kolom wajib diisi ya, Lur!";
    } else {

        $konten = sanitizeEditorContent($konten);

        $kontenTeks = trim(strip_tags($konten));

        if (empty($kontenTeks)) {
            $error = "Isi kabar tidak boleh kosong, Lur!";
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

                    if (!is_dir('../../assets/img')) {
                        mkdir('../../assets/img', 0777, true);
                    }

                    $namaFileBaru = uniqid() . '.' . $ekstensiGambar;
                    $tujuan = '../../assets/img/' . $namaFileBaru;

                    if (move_uploaded_file($tmpName, $tujuan)) {

                        $gambar = $namaFileBaru;
                    } else {

                        $error = "Gagal mengunggah gambar.";
                    }
                }
            }

            if (empty($error)) {

                $jumlahKata = str_word_count(strip_tags($konten));
                $read_time = max(1, ceil($jumlahKata / 200));

                $stmt = mysqli_prepare(
                    $conn,
                    "INSERT INTO berita (judul, kategori, penulis, konten, gambar, read_time) VALUES (?, ?, ?, ?, ?, ?)"
                );

                mysqli_stmt_bind_param(
                    $stmt,
                    "sssssi",
                    $judul,
                    $kategori,
                    $penulis,
                    $konten,
                    $gambar,
                    $read_time
                );

                if (mysqli_stmt_execute($stmt)) {

                    header("Location: index.php?status=sukses");
                    exit;
                } else {

                    $error = "Gagal menyimpan kabar ke database: " . mysqli_error($conn);
                }

                mysqli_stmt_close($stmt);
            }
        }
    }
}

?>

<!DOCTYPE html>
<html lang="id">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0">

    <title>Tulis Kabar Anyar - Webmaster Ngabar Yuk!</title>

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
                linear-gradient(315deg, rgba(255, 255, 255, 0.035) 25%, #542f1b 25%);
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

        .editor-content {
            min-height: 320px;
            outline: none;
        }

        .editor-content:empty::before {
            content: attr(data-placeholder);
            color: rgb(168 162 158);
            pointer-events: none;
        }

        .editor-content p {
            margin: 0 0 0.85rem;
        }

        .editor-content h2 {
            font-size: 1.5rem;
            line-height: 1.3;
            font-weight: 800;
            color: #292524;
            margin: 1.2rem 0 0.7rem;
        }

        .editor-content h3 {
            font-size: 1.25rem;
            line-height: 1.35;
            font-weight: 800;
            color: #292524;
            margin: 1.1rem 0 0.6rem;
        }

        .editor-content h4 {
            font-size: 1.05rem;
            line-height: 1.4;
            font-weight: 800;
            color: #292524;
            margin: 1rem 0 0.5rem;
        }

        .editor-content ul,
        .editor-content ol {
            padding-left: 1.5rem;
            margin: 0.75rem 0;
        }

        .editor-content ul {
            list-style-type: disc;
        }

        .editor-content ol {
            list-style-type: decimal;
        }

        .editor-content li {
            margin: 0.35rem 0;
        }

        .editor-content blockquote {
            border-left: 3px solid #d97706;
            padding: 0.75rem 1rem;
            margin: 1rem 0;
            background: #fffbeb;
            color: #57534e;
            font-style: italic;
            border-radius: 0 0.75rem 0.75rem 0;
        }

        .editor-content a {
            color: #92400e;
            text-decoration: underline;
            text-underline-offset: 2px;
        }

        .toolbar-button {
            width: 34px;
            height: 34px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 8px;
            color: rgb(87 83 78);
            transition:
                background-color 150ms ease,
                color 150ms ease,
                box-shadow 150ms ease;
        }

        .toolbar-button:hover {
            background: rgb(245 245 244);
            color: #78350f;
        }

        .toolbar-button.active {
            background: rgb(254 243 199);
            color: #78350f;
            box-shadow: inset 0 0 0 1px rgb(245 158 11 / 0.35);
        }

        .toolbar-divider {
            width: 1px;
            height: 22px;
            background: rgb(231 229 228);
            margin: 0 3px;
        }

        .toolbar-select {
            height: 34px;
            border: 0;
            background: transparent;
            color: rgb(87 83 78);
            font-size: 11px;
            font-weight: 700;
            border-radius: 8px;
            padding: 0 8px;
            outline: none;
            cursor: pointer;
        }

        .toolbar-select:hover {
            background: rgb(245 245 244);
            color: #78350f;
        }

        .toolbar-select.active {
            background: rgb(254 243 199);
            color: #78350f;
            box-shadow: inset 0 0 0 1px rgb(245 158 11 / 0.35);
        }

        @media (max-width: 640px) {

            .editor-content {
                min-height: 280px;
            }

            .toolbar-button {
                width: 32px;
                height: 32px;
            }

            .toolbar-divider {
                display: none;
            }

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

    <main class="max-w-5xl w-full mx-auto px-5 py-8 md:py-10 flex-grow">

        <div class="grid grid-cols-1 lg:grid-cols-[1fr_300px] gap-6 items-start">

            <div>

                <div class="mb-6">

                    <div class="inline-flex items-center gap-2 text-amber-800 bg-amber-50 border border-amber-100 px-3 py-1.5 rounded-full text-[10px] font-bold uppercase tracking-[0.15em] mb-3">

                        <i class="fa-solid fa-feather-pointed"></i>

                        Ruang Reriungan

                    </div>

                    <h1 class="text-2xl md:text-3xl font-black text-[#542f1b] tracking-tight">

                        Rilis Kabar Anyar, Lur!

                    </h1>

                    <p class="text-sm text-stone-500 mt-2 leading-relaxed">

                        Bagikan informasi segar, sudut pandang menarik,
                        atau cerita yang layak dibicarakan bersama.

                    </p>

                </div>

                <div class="bg-white rounded-3xl border border-stone-200 shadow-sm overflow-hidden">

                    <div class="px-6 md:px-8 py-4 border-b border-stone-100 bg-stone-50/60 flex items-center justify-between">

                        <div class="flex items-center gap-3">

                            <div class="w-9 h-9 rounded-xl bg-amber-100 text-amber-800 flex items-center justify-center">

                                <i class="fa-solid fa-pen-nib text-sm"></i>

                            </div>

                            <div>

                                <p class="text-sm font-black text-stone-800">
                                    Tulis Kabar
                                </p>

                                <p class="text-[11px] text-stone-400">
                                    Isi informasi di bawah dengan lengkap
                                </p>

                            </div>

                        </div>

                        <span class="hidden sm:inline-flex items-center gap-1.5 text-[10px] font-semibold text-stone-400">

                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>

                            Siap diterbitkan

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

                                        <span
                                            id="alert-text"
                                            class="text-sm">

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
                            class="space-y-6"
                            novalidate>

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
                                    value="<?= isset($_POST['judul']) ? htmlspecialchars($_POST['judul']) : ''; ?>"
                                    required
                                    maxlength="255"
                                    placeholder="Contoh: Menelisik Filosofi Kopi di Sudut Kota..."
                                    class="form-field w-full px-4 py-3 bg-stone-50 border border-stone-200 rounded-xl text-sm text-stone-800 placeholder:text-stone-400 focus:outline-none focus:border-amber-600 focus:ring-4 focus:ring-amber-600/10">

                                <div class="flex items-center justify-between mt-1.5">

                                    <span class="text-[11px] text-stone-400">
                                        Buat judul yang singkat dan mudah dipahami.
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

                                            <option value="Insight" <?= (isset($_POST['kategori']) && $_POST['kategori'] === 'Insight') ? 'selected' : ''; ?>>
                                                Insight / Opini
                                            </option>

                                            <option value="Lokal" <?= (isset($_POST['kategori']) && $_POST['kategori'] === 'Lokal') ? 'selected' : ''; ?>>
                                                Warta Lokal
                                            </option>

                                            <option value="Budaya" <?= (isset($_POST['kategori']) && $_POST['kategori'] === 'Budaya') ? 'selected' : ''; ?>>
                                                Budaya & Tradisi
                                            </option>

                                            <option value="Gaya Urip" <?= (isset($_POST['kategori']) && $_POST['kategori'] === 'Gaya Urip') ? 'selected' : ''; ?>>
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
                                                    <?= (isset($_POST['penulis']) && $_POST['penulis'] === $user['nama']) ? 'selected' : ''; ?>>

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

                                    <p class="text-[11px] text-stone-400 mt-1.5">

                                        Pilih penulis dari akun Webmaster yang aktif.

                                    </p>

                                </div>

                            </div>

                            <div>

                                <label class="block text-sm font-bold text-stone-700 mb-2">

                                    <i class="fa-solid fa-image mr-1.5 text-amber-800"></i>

                                    Foto Utama

                                    <span class="text-[11px] font-normal text-stone-400 ml-1">
                                        Opsional
                                    </span>

                                </label>

                                <label
                                    for="input-gambar"
                                    class="upload-area block border-2 border-dashed border-stone-200 rounded-2xl bg-stone-50 px-5 py-6 cursor-pointer">

                                    <div class="flex flex-col sm:flex-row sm:items-center gap-4">

                                        <div class="w-12 h-12 shrink-0 rounded-xl bg-amber-100 text-amber-800 flex items-center justify-center">

                                            <i class="fa-solid fa-cloud-arrow-up text-lg"></i>

                                        </div>

                                        <div class="min-w-0">

                                            <p class="text-sm font-bold text-stone-700">
                                                Pilih foto untuk kabarmu
                                            </p>

                                            <p class="text-xs text-stone-400 mt-1 leading-relaxed">
                                                JPG, JPEG, PNG, atau WEBP. Maksimal 2MB.
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

                                <div
                                    id="preview-container"
                                    class="mt-4 hidden">

                                    <div class="flex items-center gap-3">

                                        <div class="relative">

                                            <img
                                                id="image-preview"
                                                src="#"
                                                alt="Pratinjau Gambar"
                                                class="w-24 h-24 object-cover rounded-xl border border-stone-200 shadow-sm">

                                            <button
                                                type="button"
                                                id="remove-image"
                                                class="absolute -top-2 -right-2 bg-rose-600 text-white rounded-full w-6 h-6 flex items-center justify-center text-xs shadow hover:bg-rose-700 transition"
                                                title="Hapus gambar">

                                                <i class="fa-solid fa-xmark"></i>

                                            </button>

                                        </div>

                                        <div>

                                            <p class="text-xs font-bold text-stone-700">
                                                Pratinjau foto
                                            </p>

                                            <p class="text-[11px] text-stone-400 mt-1">
                                                Foto ini akan digunakan sebagai gambar utama kabar.
                                            </p>

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

                                <div class="border border-stone-200 rounded-2xl overflow-hidden bg-stone-50 focus-within:border-amber-600 focus-within:ring-4 focus-within:ring-amber-600/10 transition">

                                    <div
                                        id="editor-toolbar"
                                        class="flex flex-wrap items-center gap-1 px-3 py-2 border-b border-stone-200 bg-white">

                                        <button
                                            type="button"
                                            class="toolbar-button"
                                            data-command="bold"
                                            title="Tebal">

                                            <i class="fa-solid fa-bold text-xs"></i>

                                        </button>

                                        <button
                                            type="button"
                                            class="toolbar-button"
                                            data-command="italic"
                                            title="Miring">

                                            <i class="fa-solid fa-italic text-xs"></i>

                                        </button>

                                        <button
                                            type="button"
                                            class="toolbar-button"
                                            data-command="underline"
                                            title="Garis bawah">

                                            <i class="fa-solid fa-underline text-xs"></i>

                                        </button>

                                        <span class="toolbar-divider"></span>

                                        <select
                                            id="formatSelect"
                                            class="toolbar-select"
                                            title="Format teks">

                                            <option value="p">
                                                Paragraf
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

                                        <span class="toolbar-divider"></span>

                                        <button
                                            type="button"
                                            class="toolbar-button"
                                            data-command="insertUnorderedList"
                                            title="Daftar poin">

                                            <i class="fa-solid fa-list-ul text-xs"></i>

                                        </button>

                                        <button
                                            type="button"
                                            class="toolbar-button"
                                            data-command="insertOrderedList"
                                            title="Daftar bernomor">

                                            <i class="fa-solid fa-list-ol text-xs"></i>

                                        </button>

                                        <button
                                            type="button"
                                            class="toolbar-button"
                                            data-command="formatBlock"
                                            data-value="blockquote"
                                            title="Kutipan">

                                            <i class="fa-solid fa-quote-left text-xs"></i>

                                        </button>

                                        <span class="toolbar-divider"></span>

                                        <button
                                            type="button"
                                            class="toolbar-button"
                                            data-command="justifyLeft"
                                            title="Rata kiri">

                                            <i class="fa-solid fa-align-left text-xs"></i>

                                        </button>

                                        <button
                                            type="button"
                                            class="toolbar-button"
                                            data-command="justifyCenter"
                                            title="Rata tengah">

                                            <i class="fa-solid fa-align-center text-xs"></i>

                                        </button>

                                        <button
                                            type="button"
                                            class="toolbar-button"
                                            data-command="justifyRight"
                                            title="Rata kanan">

                                            <i class="fa-solid fa-align-right text-xs"></i>

                                        </button>

                                        <button
                                            type="button"
                                            class="toolbar-button"
                                            data-command="justifyFull"
                                            title="Rata penuh">

                                            <i class="fa-solid fa-align-justify text-xs"></i>

                                        </button>

                                        <span class="toolbar-divider"></span>

                                        <button
                                            type="button"
                                            class="toolbar-button"
                                            id="link-button"
                                            title="Tambahkan tautan">

                                            <i class="fa-solid fa-link text-xs"></i>

                                        </button>

                                        <button
                                            type="button"
                                            class="toolbar-button"
                                            data-command="undo"
                                            title="Undo">

                                            <i class="fa-solid fa-rotate-left text-xs"></i>

                                        </button>

                                        <button
                                            type="button"
                                            class="toolbar-button"
                                            data-command="redo"
                                            title="Redo">

                                            <i class="fa-solid fa-rotate-right text-xs"></i>

                                        </button>

                                    </div>

                                    <div
                                        id="editor-content"
                                        class="editor-content px-4 py-4 text-sm text-stone-800 leading-7 bg-stone-50"
                                        contenteditable="true"
                                        data-placeholder="Tuliskan berita atau ulasan mendalammu di sini..."><?= isset($_POST['konten']) ? $_POST['konten'] : ''; ?></div>

                                    <textarea
                                        name="konten"
                                        id="input-konten"
                                        class="hidden"></textarea>

                                </div>

                                <div class="flex items-center justify-between gap-3 mt-1.5">

                                    <p class="text-[11px] text-stone-400">

                                        Gunakan toolbar untuk mengatur format tulisan agar lebih nyaman dibaca.

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

                                        <i class="fa-solid fa-paper-plane text-xs"></i>

                                        Sebarkan Kabar

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

                        <i class="fa-solid fa-mug-hot"></i>

                    </div>

                    <p class="text-[10px] uppercase tracking-[0.16em] font-bold text-amber-300">
                        Sebelum Ngabar
                    </p>

                    <h2 class="text-lg font-black mt-1">
                        Biar kabarnya enak dibaca.
                    </h2>

                    <p class="text-xs text-stone-300 leading-relaxed mt-3">

                        Sampaikan informasi dengan jelas, gunakan judul yang relevan, dan pilih foto yang mendukung isi kabar.

                    </p>

                </div>

                <div class="bg-white border border-stone-200 rounded-2xl p-5 shadow-sm">

                    <p class="text-xs font-black text-[#542f1b] mb-4">

                        <i class="fa-solid fa-list-check mr-1.5 text-amber-700"></i>

                        Checklist Kabar

                    </p>

                    <div class="space-y-3">

                        <div class="flex gap-3">

                            <span class="w-6 h-6 shrink-0 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center text-[10px]">

                                <i class="fa-solid fa-check"></i>

                            </span>

                            <p class="text-xs text-stone-500 leading-relaxed">

                                Judul menggambarkan isi kabar.

                            </p>

                        </div>

                        <div class="flex gap-3">

                            <span class="w-6 h-6 shrink-0 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center text-[10px]">

                                <i class="fa-solid fa-check"></i>

                            </span>

                            <p class="text-xs text-stone-500 leading-relaxed">

                                Kategori sudah sesuai dengan isi.

                            </p>

                        </div>

                        <div class="flex gap-3">

                            <span class="w-6 h-6 shrink-0 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center text-[10px]">

                                <i class="fa-solid fa-check"></i>

                            </span>

                            <p class="text-xs text-stone-500 leading-relaxed">

                                Isi kabar sudah lengkap dan jelas.

                            </p>

                        </div>

                        <div class="flex gap-3">

                            <span class="w-6 h-6 shrink-0 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center text-[10px]">

                                <i class="fa-solid fa-check"></i>

                            </span>

                            <p class="text-xs text-stone-500 leading-relaxed">

                                Foto tidak melebihi ukuran 2MB.

                            </p>

                        </div>

                    </div>

                </div>

                <div class="bg-amber-50 border border-amber-100 rounded-2xl p-5">

                    <div class="flex items-start gap-3">

                        <div class="w-9 h-9 shrink-0 rounded-lg bg-amber-100 text-amber-800 flex items-center justify-center">

                            <i class="fa-regular fa-clock text-sm"></i>

                        </div>

                        <div>

                            <p class="text-xs font-black text-amber-900">
                                Estimasi Waktu Baca
                            </p>

                            <p class="text-[11px] text-amber-800/70 mt-1 leading-relaxed">

                                Sistem menghitung estimasi waktu baca secara otomatis berdasarkan jumlah kata dalam kabar.

                            </p>

                        </div>

                    </div>

                </div>

                <div class="bg-white border border-stone-200 rounded-2xl p-5 shadow-sm">

                    <p class="text-xs font-black text-[#542f1b] mb-3">

                        <i class="fa-solid fa-wand-magic-sparkles mr-1.5 text-amber-700"></i>

                        Tips Formatting

                    </p>

                    <div class="space-y-2.5 text-[11px] text-stone-500 leading-relaxed">

                        <p>
                            <strong class="text-stone-700">Heading</strong> cocok untuk membagi bagian penting dalam tulisan.
                        </p>

                        <p>
                            <strong class="text-stone-700">Bold</strong> bisa digunakan untuk menekankan informasi penting.
                        </p>

                        <p>
                            <strong class="text-stone-700">Quote</strong> cocok untuk kutipan atau pernyataan yang ingin ditonjolkan.
                        </p>

                        <p>
                            Hindari terlalu banyak format agar tulisan tetap nyaman dibaca.
                        </p>

                    </div>

                </div>

                <div class="px-1 text-[11px] text-stone-400 leading-relaxed">

                    <i class="fa-solid fa-quote-left text-amber-700 mr-1"></i>

                    Satu kabar bisa jadi awal dari sebuah reriungan.

                </div>

            </aside>

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
        const toolbarButtons = document.querySelectorAll('.toolbar-button');
        const formatSelect = document.getElementById('formatSelect');
        const linkButton = document.getElementById('link-button');

        let savedRange = null;

        function saveSelection() {

            const selection = window.getSelection();

            if (!selection.rangeCount) {
                return;
            }

            const range = selection.getRangeAt(0);

            if (editorContent.contains(range.commonAncestorContainer)) {
                savedRange = range.cloneRange();
            }

        }

        function restoreSelection() {

            if (!savedRange) {
                editorContent.focus();
                return;
            }

            const selection = window.getSelection();

            selection.removeAllRanges();
            selection.addRange(savedRange);

            editorContent.focus();

        }

        function getSelectionElement() {

            const selection = window.getSelection();

            if (!selection || !selection.rangeCount) {
                return editorContent;
            }

            let node = selection.getRangeAt(0).startContainer;

            if (node.nodeType === Node.TEXT_NODE) {
                node = node.parentElement;
            }

            if (!node || !editorContent.contains(node)) {
                return editorContent;
            }

            return node;

        }

        function updateToolbarState() {

            toolbarButtons.forEach(button => {
                button.classList.remove('active');
            });

            formatSelect.classList.remove('active');

            const element = getSelectionElement();

            if (element === editorContent) {
                return;
            }

            const isBold = document.queryCommandState('bold');
            const isItalic = document.queryCommandState('italic');
            const isUnderline = document.queryCommandState('underline');
            const isUnorderedList = document.queryCommandState('insertUnorderedList');
            const isOrderedList = document.queryCommandState('insertOrderedList');
            const isJustifyLeft = document.queryCommandState('justifyLeft');
            const isJustifyCenter = document.queryCommandState('justifyCenter');
            const isJustifyRight = document.queryCommandState('justifyRight');
            const isJustifyFull = document.queryCommandState('justifyFull');

            const stateMap = {
                bold: isBold,
                italic: isItalic,
                underline: isUnderline,
                insertUnorderedList: isUnorderedList,
                insertOrderedList: isOrderedList,
                justifyLeft: isJustifyLeft,
                justifyCenter: isJustifyCenter,
                justifyRight: isJustifyRight,
                justifyFull: isJustifyFull
            };

            toolbarButtons.forEach(button => {

                const command = button.dataset.command;

                if (command && stateMap[command]) {
                    button.classList.add('active');
                }

            });

            let blockElement = element;

            while (
                blockElement &&
                blockElement !== editorContent &&
                !['P', 'H2', 'H3', 'H4', 'BLOCKQUOTE', 'LI'].includes(blockElement.tagName)
            ) {
                blockElement = blockElement.parentElement;
            }

            if (blockElement && blockElement !== editorContent) {

                const tagName = blockElement.tagName.toLowerCase();

                if (['h2', 'h3', 'h4'].includes(tagName)) {

                    formatSelect.value = tagName;
                    formatSelect.classList.add('active');

                } else {

                    formatSelect.value = 'p';

                }

                if (tagName === 'blockquote') {

                    const quoteButton = document.querySelector(
                        '[data-command="formatBlock"][data-value="blockquote"]'
                    );

                    if (quoteButton) {
                        quoteButton.classList.add('active');
                    }

                }

            } else {

                formatSelect.value = 'p';

            }

        }

        function updateEditorValue() {

            inputKonten.value = editorContent.innerHTML.trim();

            const text = editorContent.innerText
                .replace(/\u00a0/g, ' ')
                .trim();

            if (!text) {

                wordCounter.textContent = '0 kata';

                readTimePreview.innerHTML =
                    '<i class="fa-regular fa-clock"></i> 1 menit baca';

                updateToolbarState();

                return;

            }

            const words = text
                .split(/\s+/)
                .filter(word => word.length > 0);

            const wordCount = words.length;
            const readTime = Math.max(1, Math.ceil(wordCount / 200));

            wordCounter.textContent = wordCount + ' kata';

            readTimePreview.innerHTML =
                '<i class="fa-regular fa-clock"></i> ' +
                readTime +
                ' menit baca';

            updateToolbarState();

        }

        function executeCommand(command, value = null) {

            restoreSelection();

            document.execCommand(
                command,
                false,
                value
            );

            updateEditorValue();

            editorContent.focus();

            saveSelection();

            updateToolbarState();

        }

        toolbarButtons.forEach(button => {

            button.addEventListener('mousedown', function(event) {

                event.preventDefault();

                saveSelection();

            });

            button.addEventListener('click', function() {

                const command = this.dataset.command;
                const value = this.dataset.value || null;

                if (!command) {
                    return;
                }

                executeCommand(command, value);

            });

        });

        formatSelect.addEventListener('mousedown', function() {
            saveSelection();
        });

        formatSelect.addEventListener('change', function() {

            restoreSelection();

            const value = this.value;

            document.execCommand(
                'formatBlock',
                false,
                value
            );

            updateEditorValue();

            editorContent.focus();

            saveSelection();

            updateToolbarState();

        });

        linkButton.addEventListener('mousedown', function(event) {

            event.preventDefault();

            saveSelection();

        });

        linkButton.addEventListener('click', function() {

            restoreSelection();

            const selection = window.getSelection();

            if (!selection || selection.toString().trim() === '') {

                alert('Blok teks yang ingin dijadikan tautan terlebih dahulu.');

                return;
            }

            const url = prompt('Masukkan URL tautan:');

            if (!url) {
                return;
            }

            let safeUrl = url.trim();

            if (
                !/^https?:\/\//i.test(safeUrl) &&
                !/^mailto:/i.test(safeUrl) &&
                !/^\//.test(safeUrl) &&
                !/^#/i.test(safeUrl)
            ) {
                safeUrl = 'https://' + safeUrl;
            }

            if (/^(javascript|data|vbscript):/i.test(safeUrl)) {

                alert('URL tidak valid.');

                return;
            }

            document.execCommand(
                'createLink',
                false,
                safeUrl
            );

            updateEditorValue();

            editorContent.focus();

            saveSelection();

            updateToolbarState();

        });

        editorContent.addEventListener('mouseup', function() {

            saveSelection();
            updateToolbarState();

        });

        editorContent.addEventListener('keyup', function() {

            saveSelection();
            updateToolbarState();

        });

        editorContent.addEventListener('click', function() {

            updateToolbarState();

        });

        editorContent.addEventListener('input', function() {

            updateEditorValue();

        });

        document.addEventListener('selectionchange', function() {

            if (document.activeElement === editorContent || editorContent.contains(document.activeElement)) {
                updateToolbarState();
            }

        });

        editorContent.addEventListener('paste', function(event) {

            event.preventDefault();

            const text = (
                event.clipboardData ||
                window.clipboardData
            ).getData('text/plain');

            document.execCommand(
                'insertText',
                false,
                text
            );

            updateEditorValue();

            saveSelection();

            updateToolbarState();

        });

        editorContent.addEventListener('keydown', function(event) {

            if ((event.ctrlKey || event.metaKey) && event.key.toLowerCase() === 'b') {

                event.preventDefault();

                executeCommand('bold');

            }

            if ((event.ctrlKey || event.metaKey) && event.key.toLowerCase() === 'i') {

                event.preventDefault();

                executeCommand('italic');

            }

            if ((event.ctrlKey || event.metaKey) && event.key.toLowerCase() === 'u') {

                event.preventDefault();

                executeCommand('underline');

            }

        });

        if (formBerita) {

            formBerita.addEventListener('submit', function(e) {

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

                const kontenTeks = editorContent.innerText
                    .replace(/\u00a0/g, ' ')
                    .trim();

                if (!judul || !kategori || !penulis || !kontenTeks) {

                    alert('Waduh, semua kolom wajib diisi ya, Lur!');

                    e.preventDefault();

                    return;

                }

                inputKonten.value = editorContent.innerHTML.trim();

            });

        }

        updateEditorValue();
        updateToolbarState();
    </script>

</body>

</html>