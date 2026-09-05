<?php
require_once 'config.php';

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

if (isset($_POST['submit'])) {
    $judul    = trim($_POST['judul']);
    $kategori = trim($_POST['kategori']);
    $penulis  = trim($_POST['penulis']);
    $konten   = trim($_POST['konten']);
    $gambar   = $berita['gambar'];

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
                $tujuan = 'assets/img/' . $namaFileBaru;

                if (move_uploaded_file($tmpName, $tujuan)) {
                    if (!empty($berita['gambar']) && file_exists('assets/img/' . $berita['gambar'])) {
                        unlink('assets/img/' . $berita['gambar']);
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
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sunting Kabar - Ngabar Yuk!</title>

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
    </style>
</head>

<body class="bg-stone-50 text-stone-800 font-sans antialiased flex flex-col min-h-screen selection:bg-amber-200 selection:text-amber-900">

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
                            class="inline-flex items-center gap-2 text-stone-200 hover:text-amber-300 font-semibold text-sm px-3 py-2.5 rounded-xl hover:bg-white/5 transition">

                            <i class="fa-solid fa-house text-xs"></i>

                            <span class="hidden sm:inline">Beranda</span>

                        </a>

                        <a href="about.php"
                            class="hidden sm:inline-flex items-center gap-2 text-stone-200 hover:text-amber-300 font-semibold text-sm px-3 py-2.5 rounded-xl hover:bg-white/5 transition">

                            <i class="fa-solid fa-circle-info text-xs"></i>

                            Tentang

                        </a>

                        <a href="index.php"
                            class="inline-flex items-center gap-2 bg-white/10 hover:bg-white/15 border border-white/10 text-white font-semibold text-sm px-3.5 py-2.5 rounded-xl transition">

                            <i class="fa-solid fa-arrow-left text-xs"></i>

                            <span class="hidden sm:inline">Kembali</span>

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

                        Perbarui informasi atau koreksi warta yang sudah pernah
                        kamu bagikan.

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

                            <div id="alert-box"
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

                                <button type="button"
                                    id="close-alert"
                                    class="w-8 h-8 shrink-0 rounded-lg text-rose-500 hover:text-rose-800 hover:bg-rose-100 transition flex items-center justify-center">

                                    <i class="fa-solid fa-xmark"></i>

                                </button>

                            </div>

                        <?php endif; ?>

                        <form id="form-berita"
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

                                <input type="text"
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

                                    <span id="judul-counter"
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

                                    <?php $selectedKategori = isset($_POST['kategori']) ? $_POST['kategori'] : $berita['kategori']; ?>

                                    <div class="relative">

                                        <select name="kategori"
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

                                    <input type="text"
                                        name="penulis"
                                        value="<?= isset($_POST['penulis']) ? htmlspecialchars($_POST['penulis']) : htmlspecialchars($berita['penulis']); ?>"
                                        required
                                        class="form-field w-full px-4 py-3 bg-stone-50 border border-stone-200 rounded-xl text-sm text-stone-800 placeholder:text-stone-400 focus:outline-none focus:border-amber-600 focus:ring-4 focus:ring-amber-600/10">

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

                                <?php if (!empty($berita['gambar']) && file_exists('assets/img/' . $berita['gambar'])): ?>

                                    <div class="bg-stone-50 border border-stone-200 rounded-2xl p-4 mb-4">

                                        <div class="flex items-center gap-4">

                                            <div class="relative shrink-0">

                                                <img src="assets/img/<?= htmlspecialchars($berita['gambar']); ?>"
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

                                <label for="input-gambar"
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

                                    <input type="file"
                                        id="input-gambar"
                                        name="gambar"
                                        accept="image/jpeg,image/png,image/webp"
                                        class="hidden">

                                </label>

                                <div id="preview-container" class="mt-4 hidden">

                                    <div class="bg-amber-50 border border-amber-100 rounded-2xl p-4">

                                        <div class="flex items-center gap-4">

                                            <div class="relative shrink-0">

                                                <img id="image-preview"
                                                    src="#"
                                                    alt="Pratinjau Gambar Baru"
                                                    class="w-24 h-24 object-cover rounded-xl border border-amber-200 shadow-sm">

                                                <button type="button"
                                                    id="remove-image"
                                                    class="absolute -top-2 -right-2 bg-rose-600 text-white rounded-full w-6 h-6 flex items-center justify-center text-xs shadow hover:bg-rose-700 transition"
                                                    title="Hapus pratinjau">

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

                                    <span id="read-time-preview"
                                        class="inline-flex items-center gap-1.5 text-[10px] font-semibold text-stone-400 bg-stone-50 border border-stone-200 px-2.5 py-1 rounded-lg">

                                        <i class="fa-regular fa-clock"></i>

                                        1 menit baca

                                    </span>

                                </div>

                                <textarea name="konten"
                                    id="input-konten"
                                    rows="10"
                                    required
                                    class="form-field w-full px-4 py-3 bg-stone-50 border border-stone-200 rounded-xl text-sm text-stone-800 placeholder:text-stone-400 leading-relaxed resize-y focus:outline-none focus:border-amber-600 focus:ring-4 focus:ring-amber-600/10"><?= isset($_POST['konten']) ? htmlspecialchars($_POST['konten']) : htmlspecialchars($berita['konten']); ?></textarea>

                                <div class="flex items-center justify-between gap-3 mt-1.5">

                                    <p class="text-[11px] text-stone-400">

                                        Periksa kembali isi sebelum menyimpan perubahan.

                                    </p>

                                    <p id="word-counter"
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

                                    <a href="index.php"
                                        class="inline-flex items-center justify-center gap-2 px-4 py-2.5 border border-stone-200 bg-white text-stone-600 rounded-xl font-semibold text-sm hover:bg-stone-100 transition">

                                        <i class="fa-solid fa-xmark text-xs"></i>

                                        Batal

                                    </a>

                                    <button type="submit"
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

    <?php
    include 'assets/footer.php'
    ?>

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
        const inputKonten = document.getElementById('input-konten');
        const wordCounter = document.getElementById('word-counter');
        const readTimePreview = document.getElementById('read-time-preview');

        const updateReadTime = () => {
            if (!inputKonten || !wordCounter || !readTimePreview) {
                return;
            }

            const content = inputKonten.value.trim();

            if (!content) {
                wordCounter.textContent = '0 kata';
                readTimePreview.innerHTML = '<i class="fa-regular fa-clock"></i> 1 menit baca';
                return;
            }

            const words = content.split(/\s+/).filter(word => word.length > 0);
            const wordCount = words.length;
            const readTime = Math.max(1, Math.ceil(wordCount / 200));

            wordCounter.textContent = wordCount + ' kata';

            readTimePreview.innerHTML =
                '<i class="fa-regular fa-clock"></i> ' +
                readTime +
                ' menit baca';
        };

        if (inputKonten) {
            inputKonten.addEventListener('input', updateReadTime);
            updateReadTime();
        }

        if (formBerita) {
            formBerita.addEventListener('submit', function(e) {
                const judul = inputJudul.value.trim();
                const kategori = formBerita.querySelector('[name="kategori"]').value.trim();
                const penulis = formBerita.querySelector('[name="penulis"]').value.trim();
                const konten = inputKonten.value.trim();

                if (!judul || !kategori || !penulis || !konten) {
                    alert('Waduh, semua kolom wajib diisi ya, Lur!');
                    e.preventDefault();
                }
            });
        }
    </script>

</body>

</html>