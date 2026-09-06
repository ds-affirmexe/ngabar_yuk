<?php
require_once 'config.php';
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Tentang - Ngabar Yuk!</title>

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

        .feature-card {
            transition:
                transform 180ms ease,
                box-shadow 180ms ease,
                border-color 180ms ease;
        }

        .feature-card:hover {
            transform: translateY(-3px);
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
    include 'assets/header.php';
    ?>

    <main class="max-w-5xl w-full mx-auto px-5 py-8 md:py-10 flex-grow">

        <section class="relative overflow-hidden bg-[#542f1b] text-white rounded-3xl shadow-lg shadow-stone-900/10 javanese-pattern mb-7">

            <div class="absolute -right-16 -top-16 w-48 h-48 rounded-full border border-amber-300/10"></div>

            <div class="absolute -right-6 -bottom-24 w-60 h-60 rounded-full border border-amber-300/10"></div>

            <div class="relative px-6 py-9 md:px-9 md:py-11">

                <div class="max-w-3xl">

                    <div class="inline-flex items-center gap-2 text-amber-300 bg-white/10 border border-white/10 px-3 py-1.5 rounded-full text-[10px] font-bold uppercase tracking-[0.15em] mb-5">

                        <i class="fa-solid fa-circle-info"></i>

                        Tentang Ngabar Yuk!

                    </div>

                    <h1 class="text-3xl md:text-5xl font-black tracking-tight leading-tight">

                        Sebuah ruang untuk
                        <span class="text-amber-400">ngabar</span>
                        dan reriungan.

                    </h1>

                    <p class="text-sm md:text-[15px] text-stone-300 mt-4 leading-relaxed max-w-2xl">

                        Ngabar Yuk! adalah wadah sederhana untuk berbagi kabar,
                        gagasan, dan cerita. Tempat warta bertemu reriungan,
                        dengan nuansa lokal yang dikemas melalui antarmuka modern
                        dan mudah digunakan.

                    </p>

                    <div class="flex flex-wrap items-center gap-x-5 gap-y-2 mt-5 text-[10px] uppercase tracking-[0.14em] font-semibold text-stone-400">

                        <span class="inline-flex items-center gap-1.5">
                            <i class="fa-solid fa-newspaper text-amber-400"></i>
                            Warta
                        </span>

                        <span class="inline-flex items-center gap-1.5">
                            <i class="fa-solid fa-comments text-amber-400"></i>
                            Reriungan
                        </span>

                        <span class="inline-flex items-center gap-1.5">
                            <i class="fa-solid fa-lightbulb text-amber-400"></i>
                            Insight
                        </span>

                    </div>

                </div>

            </div>

        </section>

        <section class="grid grid-cols-1 md:grid-cols-5 gap-5 mb-7">

            <div class="md:col-span-3 bg-white border border-stone-200 rounded-2xl shadow-sm p-6 md:p-7">

                <div class="flex items-center gap-3 mb-5">

                    <div class="w-10 h-10 rounded-xl bg-amber-50 text-amber-700 flex items-center justify-center">

                        <i class="fa-solid fa-mug-hot"></i>

                    </div>

                    <div>

                        <p class="text-[10px] uppercase tracking-[0.15em] font-bold text-amber-800">
                            Filosofi Nama
                        </p>

                        <h2 class="text-xl font-black text-[#542f1b]">
                            Kenapa “Ngabar Yuk!”?
                        </h2>

                    </div>

                </div>

                <div class="space-y-4 text-sm text-stone-600 leading-relaxed">

                    <p>

                        <strong class="text-[#542f1b]">“Ngabar”</strong> diambil dari
                        kata <em>kabar</em>, sesuatu yang dibagikan dan dibicarakan
                        bersama. Sementara <strong class="text-[#542f1b]">“Yuk!”</strong>
                        memberi kesan ajakan yang ringan dan dekat.

                    </p>

                    <p>

                        Nama ini menjadi gambaran sederhana dari tujuan website:
                        bukan hanya menyampaikan informasi, tetapi juga mengajak
                        orang untuk membaca, berpikir, dan mereriung bersama.

                    </p>

                </div>

            </div>

            <div class="md:col-span-2 bg-amber-50 border border-amber-100 rounded-2xl p-6 md:p-7">

                <div class="flex items-center gap-3 mb-5">

                    <div class="w-10 h-10 rounded-xl bg-amber-100 text-amber-800 flex items-center justify-center">

                        <i class="fa-solid fa-palette"></i>

                    </div>

                    <div>

                        <p class="text-[10px] uppercase tracking-[0.15em] font-bold text-amber-800">
                            Identitas
                        </p>

                        <h2 class="text-xl font-black text-[#542f1b]">
                            Nuansa yang dipilih
                        </h2>

                    </div>

                </div>

                <div class="space-y-3">

                    <div class="flex items-center gap-3">

                        <span class="w-8 h-8 rounded-lg bg-[#542f1b] text-white flex items-center justify-center shrink-0">
                            <i class="fa-solid fa-brush text-xs"></i>
                        </span>

                        <div>
                            <p class="text-sm font-bold text-[#542f1b]">Jawa</p>
                            <p class="text-xs text-stone-500">Inspirasi visual dan suasana lokal.</p>
                        </div>

                    </div>

                    <div class="flex items-center gap-3">

                        <span class="w-8 h-8 rounded-lg bg-white text-amber-700 flex items-center justify-center shrink-0 border border-amber-100">
                            <i class="fa-solid fa-mug-hot text-xs"></i>
                        </span>

                        <div>
                            <p class="text-sm font-bold text-[#542f1b]">Hangat</p>
                            <p class="text-xs text-stone-500">Kesan dekat, santai, dan mudah didekati.</p>
                        </div>

                    </div>

                    <div class="flex items-center gap-3">

                        <span class="w-8 h-8 rounded-lg bg-white text-amber-700 flex items-center justify-center shrink-0 border border-amber-100">
                            <i class="fa-solid fa-comments text-xs"></i>
                        </span>

                        <div>
                            <p class="text-sm font-bold text-[#542f1b]">Reriungan</p>
                            <p class="text-xs text-stone-500">Membuka ruang untuk cerita dan gagasan.</p>
                        </div>

                    </div>

                </div>

            </div>

        </section>

        <section class="mb-7">

            <div class="flex items-center gap-2 mb-5">

                <span class="w-2 h-2 rounded-full bg-amber-500"></span>

                <div>

                    <p class="text-[10px] uppercase tracking-[0.16em] font-bold text-amber-800">
                        Prinsip
                    </p>

                    <h2 class="text-2xl font-black text-[#542f1b] tracking-tight">
                        Tiga nilai utama
                    </h2>

                </div>

            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">

                <div class="feature-card bg-white border border-stone-200 rounded-2xl shadow-sm p-5">

                    <div class="w-11 h-11 rounded-xl bg-amber-50 text-amber-700 flex items-center justify-center mb-4">
                        <i class="fa-solid fa-newspaper"></i>
                    </div>

                    <h3 class="text-base font-black text-[#542f1b]">
                        Warta
                    </h3>

                    <p class="text-xs text-stone-500 mt-2 leading-relaxed">
                        Menyampaikan kabar dan informasi dalam bentuk yang ringan dan mudah dibaca.
                    </p>

                </div>

                <div class="feature-card bg-white border border-stone-200 rounded-2xl shadow-sm p-5">

                    <div class="w-11 h-11 rounded-xl bg-stone-100 text-stone-700 flex items-center justify-center mb-4">
                        <i class="fa-solid fa-comments"></i>
                    </div>

                    <h3 class="text-base font-black text-[#542f1b]">
                        Reriungan
                    </h3>

                    <p class="text-xs text-stone-500 mt-2 leading-relaxed">
                        Menghadirkan suasana yang dekat agar cerita dan gagasan terasa lebih personal.
                    </p>

                </div>

                <div class="feature-card bg-white border border-stone-200 rounded-2xl shadow-sm p-5">

                    <div class="w-11 h-11 rounded-xl bg-violet-50 text-violet-700 flex items-center justify-center mb-4">
                        <i class="fa-solid fa-lightbulb"></i>
                    </div>

                    <h3 class="text-base font-black text-[#542f1b]">
                        Insight
                    </h3>

                    <p class="text-xs text-stone-500 mt-2 leading-relaxed">
                        Memberi ruang untuk sudut pandang, pemikiran, dan hal-hal yang layak direnungkan.
                    </p>

                </div>

            </div>

        </section>

        <section class="mb-7">

            <div class="flex flex-col sm:flex-row sm:items-end sm:justify-between gap-3 mb-5">

                <div>

                    <div class="flex items-center gap-2 mb-1">

                        <span class="w-2 h-2 rounded-full bg-amber-500"></span>

                        <p class="text-[10px] uppercase tracking-[0.16em] font-bold text-amber-800">
                            Pengalaman Pengunjung
                        </p>

                    </div>

                    <h2 class="text-2xl font-black text-[#542f1b] tracking-tight">
                        Cara Ngabar Yuk!
                    </h2>

                </div>

                <p class="text-xs text-stone-400 max-w-sm sm:text-right leading-relaxed">
                    Tiga langkah sederhana untuk menemukan kabar, membaca cerita, dan ikut dalam reriungan.
                </p>

            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">

                <div class="bg-white border border-stone-200 rounded-2xl p-5 shadow-sm">

                    <div class="flex items-center justify-between mb-5">

                        <span class="text-3xl font-black text-amber-200">
                            01
                        </span>

                        <span class="w-10 h-10 rounded-xl bg-amber-50 text-amber-700 flex items-center justify-center">
                            <i class="fa-solid fa-compass"></i>
                        </span>

                    </div>

                    <h3 class="text-base font-black text-[#542f1b]">
                        Temukan
                    </h3>

                    <p class="text-xs text-stone-500 leading-relaxed mt-2">
                        Jelajahi berbagai kabar dan temukan topik yang menarik perhatianmu.
                    </p>

                </div>

                <div class="bg-white border border-stone-200 rounded-2xl p-5 shadow-sm">

                    <div class="flex items-center justify-between mb-5">

                        <span class="text-3xl font-black text-amber-200">
                            02
                        </span>

                        <span class="w-10 h-10 rounded-xl bg-stone-100 text-stone-700 flex items-center justify-center">
                            <i class="fa-solid fa-book-open"></i>
                        </span>

                    </div>

                    <h3 class="text-base font-black text-[#542f1b]">
                        Baca
                    </h3>

                    <p class="text-xs text-stone-500 leading-relaxed mt-2">
                        Simak cerita, warta, opini, dan insight dari berbagai penulis.
                    </p>

                </div>

                <div class="bg-white border border-stone-200 rounded-2xl p-5 shadow-sm">

                    <div class="flex items-center justify-between mb-5">

                        <span class="text-3xl font-black text-amber-200">
                            03
                        </span>

                        <span class="w-10 h-10 rounded-xl bg-violet-50 text-violet-700 flex items-center justify-center">
                            <i class="fa-solid fa-comments"></i>
                        </span>

                    </div>

                    <h3 class="text-base font-black text-[#542f1b]">
                        Reriung
                    </h3>

                    <p class="text-xs text-stone-500 leading-relaxed mt-2">
                        Temukan sudut pandang baru dan biarkan setiap kabar membuka ruang untuk berpikir.
                    </p>

                </div>

            </div>

        </section>

        <section class="mb-7">

            <div class="flex flex-col sm:flex-row sm:items-end sm:justify-between gap-3 mb-5">

                <div>

                    <div class="flex items-center gap-2 mb-1">

                        <span class="w-2 h-2 rounded-full bg-amber-500"></span>

                        <p class="text-[10px] uppercase tracking-[0.16em] font-bold text-amber-800">
                            Di Balik Website
                        </p>

                    </div>

                    <h2 class="text-2xl font-black text-[#542f1b] tracking-tight">
                        Teknologi yang digunakan
                    </h2>

                </div>

                <p class="text-xs text-stone-400 max-w-sm sm:text-right leading-relaxed">
                    Teknologi yang digunakan untuk membangun tampilan, interaksi, dan pengelolaan data Ngabar Yuk!
                </p>

            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4">

                <div class="feature-card bg-white border border-stone-200 rounded-2xl shadow-sm p-5">

                    <div class="w-10 h-10 rounded-xl bg-orange-50 text-orange-700 flex items-center justify-center mb-4">
                        <i class="fa-brands fa-html5 text-lg"></i>
                    </div>

                    <h3 class="text-sm font-black text-[#542f1b]">
                        HTML
                    </h3>

                    <p class="text-xs text-stone-500 mt-1.5 leading-relaxed">
                        Struktur dasar halaman dan konten website.
                    </p>

                </div>

                <div class="feature-card bg-white border border-stone-200 rounded-2xl shadow-sm p-5">

                    <div class="w-10 h-10 rounded-xl bg-sky-50 text-sky-700 flex items-center justify-center mb-4">
                        <i class="fa-brands fa-css3-alt text-lg"></i>
                    </div>

                    <h3 class="text-sm font-black text-[#542f1b]">
                        Tailwind CSS
                    </h3>

                    <p class="text-xs text-stone-500 mt-1.5 leading-relaxed">
                        Styling antarmuka dengan pendekatan utility-first.
                    </p>

                </div>

                <div class="feature-card bg-white border border-stone-200 rounded-2xl shadow-sm p-5">

                    <div class="w-10 h-10 rounded-xl bg-yellow-50 text-yellow-700 flex items-center justify-center mb-4">
                        <i class="fa-brands fa-js text-lg"></i>
                    </div>

                    <h3 class="text-sm font-black text-[#542f1b]">
                        JavaScript
                    </h3>

                    <p class="text-xs text-stone-500 mt-1.5 leading-relaxed">
                        Menangani interaksi dan pengalaman pengguna.
                    </p>

                </div>

                <div class="feature-card bg-white border border-stone-200 rounded-2xl shadow-sm p-5">

                    <div class="w-10 h-10 rounded-xl bg-indigo-50 text-indigo-700 flex items-center justify-center mb-4">
                        <i class="fa-brands fa-php text-lg"></i>
                    </div>

                    <h3 class="text-sm font-black text-[#542f1b]">
                        PHP
                    </h3>

                    <p class="text-xs text-stone-500 mt-1.5 leading-relaxed">
                        Menangani proses CRUD dan koneksi dengan database.
                    </p>

                </div>

                <div class="feature-card bg-white border border-stone-200 rounded-2xl shadow-sm p-5">

                    <div class="w-10 h-10 rounded-xl bg-slate-50 text-slate-700 flex items-center justify-center mb-4">
                        <i class="fa-solid fa-icons text-lg"></i>
                    </div>

                    <h3 class="text-sm font-black text-[#542f1b]">
                        Font Awesome
                    </h3>

                    <p class="text-xs text-stone-500 mt-1.5 leading-relaxed">
                        Menambahkan ikon untuk memperjelas navigasi dan informasi.
                    </p>

                </div>

            </div>

        </section>

        <section class="grid grid-cols-1 md:grid-cols-2 gap-5 mb-7">

            <div class="bg-white border border-stone-200 rounded-2xl shadow-sm p-6">

                <div class="flex items-center gap-3 mb-5">

                    <div class="w-10 h-10 rounded-xl bg-stone-100 text-stone-700 flex items-center justify-center">
                        <i class="fa-solid fa-compass"></i>
                    </div>

                    <div>

                        <p class="text-[10px] uppercase tracking-[0.15em] font-bold text-stone-500">
                            Eksplorasi
                        </p>

                        <h2 class="text-xl font-black text-[#542f1b]">
                            Yang bisa kamu lakukan
                        </h2>

                    </div>

                </div>

                <div class="space-y-3">

                    <div class="flex items-center gap-3 text-sm text-stone-600">

                        <span class="w-7 h-7 rounded-lg bg-amber-50 text-amber-700 flex items-center justify-center shrink-0">
                            <i class="fa-solid fa-newspaper text-xs"></i>
                        </span>

                        Membaca berbagai kabar dan cerita

                    </div>

                    <div class="flex items-center gap-3 text-sm text-stone-600">

                        <span class="w-7 h-7 rounded-lg bg-sky-50 text-sky-700 flex items-center justify-center shrink-0">
                            <i class="fa-solid fa-magnifying-glass text-xs"></i>
                        </span>

                        Mencari kabar berdasarkan kata kunci

                    </div>

                    <div class="flex items-center gap-3 text-sm text-stone-600">

                        <span class="w-7 h-7 rounded-lg bg-amber-50 text-amber-700 flex items-center justify-center shrink-0">
                            <i class="fa-solid fa-filter text-xs"></i>
                        </span>

                        Menyaring kabar berdasarkan kategori

                    </div>

                    <div class="flex items-center gap-3 text-sm text-stone-600">

                        <span class="w-7 h-7 rounded-lg bg-violet-50 text-violet-700 flex items-center justify-center shrink-0">
                            <i class="fa-solid fa-user-pen text-xs"></i>
                        </span>

                        Mengenal penulis dan tulisan mereka

                    </div>

                    <div class="flex items-center gap-3 text-sm text-stone-600">

                        <span class="w-7 h-7 rounded-lg bg-emerald-50 text-emerald-700 flex items-center justify-center shrink-0">
                            <i class="fa-solid fa-shapes text-xs"></i>
                        </span>

                        Menjelajahi berbagai topik dan kategori

                    </div>

                </div>

            </div>

            <div class="bg-[#542f1b] text-white rounded-2xl shadow-sm p-6 javanese-pattern">

                <div class="w-11 h-11 rounded-xl bg-amber-400 text-[#542f1b] flex items-center justify-center mb-5">
                    <i class="fa-solid fa-code"></i>
                </div>

                <p class="text-[10px] uppercase tracking-[0.15em] font-bold text-amber-300">
                    Dibuat oleh
                </p>

                <h2 class="text-2xl font-black mt-1">
                    Aqeela Fazle Mawla Ramadhan
                </h2>

                <p class="text-sm text-stone-300 mt-3 leading-relaxed">

                    Website ini dibuat sebagai bagian dari
                    <strong class="text-white">
                        Tugas Seleksi Divisi Webmaster
                    </strong>
                    dengan pendekatan sederhana, fungsional,
                    dan tetap memperhatikan pengalaman pengguna.

                </p>

                <div class="flex flex-wrap gap-2 mt-5">

                    <span class="px-2.5 py-1.5 rounded-lg bg-white/10 border border-white/10 text-[10px] font-semibold text-stone-200">
                        CRUD
                    </span>

                    <span class="px-2.5 py-1.5 rounded-lg bg-white/10 border border-white/10 text-[10px] font-semibold text-stone-200">
                        Web Development
                    </span>

                    <span class="px-2.5 py-1.5 rounded-lg bg-white/10 border border-white/10 text-[10px] font-semibold text-stone-200">
                        UI/UX
                    </span>

                </div>

            </div>

        </section>

        <section class="bg-amber-50 border border-amber-100 rounded-2xl px-5 py-6 md:px-7 md:py-7 flex flex-col md:flex-row md:items-center md:justify-between gap-5">

            <div class="flex items-start gap-4">

                <div class="w-11 h-11 shrink-0 rounded-xl bg-amber-100 text-amber-800 flex items-center justify-center">
                    <i class="fa-solid fa-mug-hot"></i>
                </div>

                <div>

                    <p class="text-xs uppercase tracking-[0.14em] font-bold text-amber-800">
                        Mau ikut reriungan?
                    </p>

                    <h2 class="text-xl font-black text-[#542f1b] mt-1">
                        Masih banyak kabar untuk dijelajahi.
                    </h2>

                    <p class="text-xs text-stone-500 mt-1 leading-relaxed">
                        Lihat kumpulan artikel dan temukan cerita yang mungkin belum kamu baca.
                    </p>

                </div>

            </div>

            <a
                href="article.php"
                class="inline-flex items-center justify-center gap-2 bg-[#542f1b] hover:bg-[#452515] text-white font-bold text-sm px-5 py-3 rounded-xl transition shadow-sm shrink-0">

                Jelajahi Kabar

                <i class="fa-solid fa-arrow-right text-xs"></i>

            </a>

        </section>

    </main>

    <?php
    include 'assets/footer.php'
    ?>

</body>

</html>