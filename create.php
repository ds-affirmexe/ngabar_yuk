<?php
require_once 'config.php';

$error = '';

if (isset($_POST['submit'])) {
    $judul    = trim($_POST['judul']);
    $kategori = trim($_POST['kategori']);
    $penulis  = trim($_POST['penulis']);
    $konten   = trim($_POST['konten']);

    $gambar   = '';

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
                if (!is_dir('assets/img')) {
                    mkdir('assets/img', 0777, true);
                }

                $namaFileBaru = uniqid() . '.' . $ekstensiGambar;
                $tujuan = 'assets/img/' . $namaFileBaru;

                if (move_uploaded_file($tmpName, $tujuan)) {
                    $gambar = $namaFileBaru;
                } else {
                    $error = "Gagal mengunggah gambar.";
                }
            }
        }

        if (empty($error)) {
            $stmt = mysqli_prepare($conn, "INSERT INTO berita (judul, kategori, penulis, konten, gambar) VALUES (?, ?, ?, ?, ?)");
            mysqli_stmt_bind_param($stmt, "sssss", $judul, $kategori, $penulis, $konten, $gambar);

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
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tulis Kabar Anyar - Ngabar Yuk!</title>

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

    <?php
    include 'assets/header.php'
    ?>

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
                                    value="<?= isset($_POST['judul']) ? htmlspecialchars($_POST['judul']) : ''; ?>"
                                    required
                                    maxlength="255"
                                    placeholder="Contoh: Menelisik Filosofi Kopi di Sudut Kota..."
                                    class="form-field w-full px-4 py-3 bg-stone-50 border border-stone-200 rounded-xl text-sm text-stone-800 placeholder:text-stone-400 focus:outline-none focus:border-amber-600 focus:ring-4 focus:ring-amber-600/10">

                                <div class="flex items-center justify-between mt-1.5">

                                    <span class="text-[11px] text-stone-400">
                                        Buat judul yang singkat dan mudah dipahami.
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

                                    <div class="relative">

                                        <select name="kategori"
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

                                    <input type="text"
                                        name="penulis"
                                        value="<?= isset($_POST['penulis']) ? htmlspecialchars($_POST['penulis']) : ''; ?>"
                                        required
                                        placeholder="Contoh: Cak Mat"
                                        class="form-field w-full px-4 py-3 bg-stone-50 border border-stone-200 rounded-xl text-sm text-stone-800 placeholder:text-stone-400 focus:outline-none focus:border-amber-600 focus:ring-4 focus:ring-amber-600/10">

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

                                <label for="input-gambar"
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

                                    <input type="file"
                                        id="input-gambar"
                                        name="gambar"
                                        accept="image/jpeg,image/png,image/webp"
                                        class="hidden">

                                </label>

                                <div id="preview-container" class="mt-4 hidden">

                                    <div class="flex items-center gap-3">

                                        <div class="relative">

                                            <img id="image-preview"
                                                src="#"
                                                alt="Pratinjau Gambar"
                                                class="w-24 h-24 object-cover rounded-xl border border-stone-200 shadow-sm">

                                            <button type="button"
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

                                <label class="block text-sm font-bold text-stone-700 mb-2">

                                    <i class="fa-solid fa-align-left mr-1.5 text-amber-800"></i>

                                    Isi Berita / Insight

                                    <span class="text-rose-500">*</span>

                                </label>

                                <textarea name="konten"
                                    rows="10"
                                    required
                                    placeholder="Tuliskan berita atau ulasan mendalammu di sini..."
                                    class="form-field w-full px-4 py-3 bg-stone-50 border border-stone-200 rounded-xl text-sm text-stone-800 placeholder:text-stone-400 leading-relaxed resize-y focus:outline-none focus:border-amber-600 focus:ring-4 focus:ring-amber-600/10"><?= isset($_POST['konten']) ? htmlspecialchars($_POST['konten']) : ''; ?></textarea>

                                <p class="text-[11px] text-stone-400 mt-1.5">

                                    Tulis dengan jelas agar kabar mudah dipahami pembaca.

                                </p>

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
                        Sampaikan informasi dengan jelas, gunakan judul yang
                        relevan, dan pilih foto yang mendukung isi kabar.
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

                <div class="px-1 text-[11px] text-stone-400 leading-relaxed">

                    <i class="fa-solid fa-quote-left text-amber-700 mr-1"></i>

                    Satu kabar bisa jadi awal dari sebuah reriungan.

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
                    }

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

        if (formBerita) {
            formBerita.addEventListener('submit', function(e) {
                const judul = inputJudul.value.trim();
                const kategori = formBerita.querySelector('[name="kategori"]').value.trim();
                const penulis = formBerita.querySelector('[name="penulis"]').value.trim();
                const konten = formBerita.querySelector('[name="konten"]').value.trim();

                if (!judul || !kategori || !penulis || !konten) {
                    alert('Waduh, semua kolom wajib diisi ya, Lur!');
                    e.preventDefault();
                }
            });
        }
    </script>

</body>

</html>