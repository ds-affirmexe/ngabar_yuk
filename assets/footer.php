<footer class="bg-[#3a2113] text-stone-300 mt-12">

    <div class="max-w-5xl mx-auto px-5">

        <div class="py-8 flex flex-col md:flex-row md:items-center md:justify-between gap-7">

            <div>

                <a
                    href="index.php"
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

                    Wadah sederhana untuk berbagi kabar,
                    gagasan, dan cerita yang layak dibicarakan.

                </p>

            </div>

            <div class="flex flex-col sm:flex-row sm:items-center gap-5">

                <div class="flex flex-wrap items-center gap-x-5 gap-y-2 text-xs">

                    <a
                        href="index.php"
                        class="hover:text-amber-300 transition">

                        Beranda

                    </a>

                    <a
                        href="about.php"
                        class="hover:text-amber-300 transition">

                        Tentang

                    </a>

                </div>

                <a
                    href="https://ent.pens.ac.id/#home"
                    target="_blank"
                    rel="noopener noreferrer"
                    class="inline-flex items-center justify-center gap-2 px-3.5 py-2 rounded-xl bg-white/5 border border-white/10 text-stone-200 text-xs font-semibold hover:bg-white/10 hover:text-amber-300 transition">

                    <i class="fa-solid fa-arrow-up-right-from-square text-[10px]"></i>

                    Kunjungi ENT Crews

                </a>

            </div>

        </div>

        <div class="border-t border-white/10 py-4 flex flex-col sm:flex-row justify-between items-center gap-2 text-center sm:text-left">

            <p class="text-[11px] text-stone-500">

                © 2026 Ngabar Yuk! • Dibuat oleh

                <span class="text-stone-300 font-semibold">

                    Aqeela Fazle Mawla Ramadhan

                </span>

            </p>

            <p class="text-[11px] text-amber-500 font-medium">

                Tugas Seleksi Divisi Webmaster

            </p>

        </div>

    </div>

</footer>

<button
    id="scrollToTop"
    type="button"
    aria-label="Kembali ke atas"
    onclick="window.scrollTo({ top: 0, behavior: 'smooth' });"
    class="fixed right-5 bottom-5 z-50 hidden items-center gap-2 px-4 py-3 rounded-xl bg-[#542f1b] text-white border border-white/10 shadow-lg shadow-stone-900/20 hover:bg-[#6a3b21] hover:-translate-y-0.5 transition-all duration-300">

    <i class="fa-solid fa-arrow-up text-amber-400 text-xs"></i>

    <span class="text-xs font-bold">
        Ke Atas
    </span>

</button>

<script>
    const scrollToTop = document.getElementById('scrollToTop');

    function toggleScrollToTop() {
        if (window.scrollY > 400) {
            scrollToTop.classList.remove('hidden');
            scrollToTop.classList.add('flex');
        } else {
            scrollToTop.classList.add('hidden');
            scrollToTop.classList.remove('flex');
        }
    }

    window.addEventListener('scroll', toggleScrollToTop, {
        passive: true
    });

    toggleScrollToTop();
</script>