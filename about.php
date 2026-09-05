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
                            class="hidden sm:inline-flex items-center gap-2 bg-white/10 border border-white/10 text-white font-semibold text-sm px-3 py-2.5 rounded-xl transition">

                            <i class="fa-solid fa-circle-info text-xs"></i>

                            Tentang

                        </a>

                        <a href="create.php"
                            class="inline-flex items-center gap-2 bg-amber-400 hover:bg-amber-300 text-[#542f1b] font-bold text-sm px-3.5 py-2.5 rounded-xl transition shadow-sm">

                            <i class="fa-solid fa-plus text-xs"></i>

                            <span class="hidden sm:inline">Tulis Kabar</span>

                        </a>

                    </nav>

                </div>

            </div>

        </div>

    </header>

    <main class="max-w-5xl w-full mx-auto px-5 py-8 md:py-10 flex-grow">

        <div class="flex items-center gap-2 mb-6">

            <a href="index.php"
                class="text-xs font-semibold text-stone-500 hover:text-amber-800 transition">

                Beranda

            </a>

            <i class="fa-solid fa-chevron-right text-[8px] text-stone-300"></i>

            <span class="text-xs font-semibold text-amber-800">

                Tentang

            </span>

        </div>

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
                        gagasan, dan cerita. Karena kadang, sebuah kabar memang
                        lebih enak dibicarakan sambil ngopi.

                    </p>

                </div>

            </div>

        </section>

        <section class="grid grid-cols-1 md:grid-cols-5 gap-5 mb-7">

            <div class="md:col-span-3 bg-white border border-stone-200 rounded-2xl shadow-sm p-6 md:p-7">

                <div class="flex items-center gap-3 mb-5">

                    <div class="w-10 h-10 rounded-xl bg-amber-100 text-amber-800 flex items-center justify-center">

                        <i class="fa-solid fa-mug-hot"></i>

                    </div>

                    <div>

                        <p class="text-[10px] uppercase tracking-[0.15em] font-bold text-amber-800">

                            Filosofi Nama

                        </p>

                        <h2 class="text-xl font-black text-[#542f1b]">

                            Kenapa "Ngabar Yuk!"?

                        </h2>

                    </div>

                </div>

                <div class="space-y-4 text-sm text-stone-600 leading-relaxed">

                    <p>

                        <strong class="text-[#542f1b]">Ngabar</strong> mengambil
                        nuansa dari aktivitas berbagi kabar dan berbincang.
                        Kata <em>"Yuk!"</em> menambahkan ajakan yang santai,
                        terbuka, dan tidak berjarak.

                    </p>

                    <p>

                        Konsep tersebut menjadi dasar dari website ini:
                        menghadirkan tempat yang sederhana untuk menyampaikan
                        sesuatu, membaca kabar orang lain, dan membuka ruang
                        untuk reriungan.

                    </p>

                    <p>

                        Nuansa Jawa digunakan sebagai identitas visual untuk
                        memberikan karakter yang dekat dengan budaya lokal,
                        tetapi tetap dikemas dengan antarmuka yang modern
                        dan mudah digunakan.

                    </p>

                </div>

            </div>

            <div class="md:col-span-2 bg-amber-50 border border-amber-100 rounded-2xl p-6 md:p-7 flex flex-col justify-between">

                <div>

                    <p class="text-[10px] uppercase tracking-[0.15em] font-bold text-amber-800 mb-4">

                        Nilai Utama

                    </p>

                    <div class="space-y-4">

                        <div class="flex gap-3">

                            <div class="w-9 h-9 shrink-0 rounded-lg bg-white text-amber-800 flex items-center justify-center shadow-sm">

                                <i class="fa-solid fa-newspaper text-sm"></i>

                            </div>

                            <div>

                                <h3 class="text-sm font-black text-[#542f1b]">

                                    Warta

                                </h3>

                                <p class="text-xs text-stone-500 mt-1 leading-relaxed">

                                    Berbagi informasi dan kabar yang menarik untuk diketahui.

                                </p>

                            </div>

                        </div>

                        <div class="flex gap-3">

                            <div class="w-9 h-9 shrink-0 rounded-lg bg-white text-amber-800 flex items-center justify-center shadow-sm">

                                <i class="fa-solid fa-comments text-sm"></i>

                            </div>

                            <div>

                                <h3 class="text-sm font-black text-[#542f1b]">

                                    Reriungan

                                </h3>

                                <p class="text-xs text-stone-500 mt-1 leading-relaxed">

                                    Membuka ruang untuk berbagi cerita dan gagasan.

                                </p>

                            </div>

                        </div>

                        <div class="flex gap-3">

                            <div class="w-9 h-9 shrink-0 rounded-lg bg-white text-amber-800 flex items-center justify-center shadow-sm">

                                <i class="fa-solid fa-lightbulb text-sm"></i>

                            </div>

                            <div>

                                <h3 class="text-sm font-black text-[#542f1b]">

                                    Insight

                                </h3>

                                <p class="text-xs text-stone-500 mt-1 leading-relaxed">

                                    Menyajikan sudut pandang yang dapat menjadi bahan pemikiran.

                                </p>

                            </div>

                        </div>

                    </div>

                </div>

            </div>

        </section>

        <section class="mb-7">

            <div class="flex items-end justify-between gap-4 mb-5">

                <div>

                    <div class="flex items-center gap-2 mb-1">

                        <span class="w-2 h-2 rounded-full bg-amber-500"></span>

                        <p class="text-[10px] uppercase tracking-[0.16em] font-bold text-amber-800">

                            Di Balik Website

                        </p>

                    </div>

                    <h2 class="text-2xl font-black text-[#542f1b] tracking-tight">

                        Dibuat dengan sederhana

                    </h2>

                </div>

            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">

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

                        Styling antarmuka dengan sistem utility-first.

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

                        Interaksi kecil untuk pengalaman pengguna.

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

            </div>

        </section>

        <section class="grid grid-cols-1 md:grid-cols-2 gap-5 mb-7">

            <div class="bg-white border border-stone-200 rounded-2xl shadow-sm p-6">

                <div class="flex items-center gap-3 mb-5">

                    <div class="w-10 h-10 rounded-xl bg-stone-100 text-stone-700 flex items-center justify-center">

                        <i class="fa-solid fa-list-check"></i>

                    </div>

                    <div>

                        <p class="text-[10px] uppercase tracking-[0.15em] font-bold text-stone-500">

                            Fitur

                        </p>

                        <h2 class="text-xl font-black text-[#542f1b]">

                            Apa yang bisa dilakukan?

                        </h2>

                    </div>

                </div>

                <div class="space-y-3">

                    <div class="flex items-center gap-3 text-sm text-stone-600">

                        <span class="w-7 h-7 rounded-lg bg-emerald-50 text-emerald-700 flex items-center justify-center shrink-0">

                            <i class="fa-solid fa-plus text-xs"></i>

                        </span>

                        Membuat kabar baru

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

                            <i class="fa-solid fa-pen-to-square text-xs"></i>

                        </span>

                        Menyunting kabar yang sudah dibuat

                    </div>

                    <div class="flex items-center gap-3 text-sm text-stone-600">

                        <span class="w-7 h-7 rounded-lg bg-rose-50 text-rose-700 flex items-center justify-center shrink-0">

                            <i class="fa-solid fa-trash-can text-xs"></i>

                        </span>

                        Menghapus kabar

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

                    dengan pendekatan sederhana, fungsional, dan tetap memperhatikan pengalaman pengguna.

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

                        Sudah siap ngabar?

                    </p>

                    <h2 class="text-xl font-black text-[#542f1b] mt-1">

                        Punya cerita untuk dibagikan?

                    </h2>

                    <p class="text-xs text-stone-500 mt-1 leading-relaxed">

                        Tulis kabarmu dan biarkan menjadi bagian dari reriungan di sini.

                    </p>

                </div>

            </div>

            <a href="create.php"
                class="inline-flex items-center justify-center gap-2 bg-[#542f1b] hover:bg-[#452515] text-white font-bold text-sm px-5 py-3 rounded-xl transition shadow-sm shrink-0">

                <i class="fa-solid fa-pen-to-square text-xs"></i>

                Tulis Kabar

            </a>

        </section>

    </main>

    <?php
    include 'assets/footer.php'
    ?>

</body>

</html>