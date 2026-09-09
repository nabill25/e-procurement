import { useRef, useState, useCallback, useEffect, Children } from 'react';
import { ChevronLeft, ChevronRight } from 'lucide-react';
import clsx from 'clsx';

/**
 * Baris tab/kategori yang bisa digeser mendatar, TIDAK bergantung sama sekali ke
 * scrollbar bawaan browser.
 *
 * Latar belakang (lihat CLAUDE.md, riwayat perbaikan 2026-09-09): sebelumnya baris
 * tab mengandalkan scrollbar bawaan browser (cukup `overflow-x-auto`) yang dicoba
 * dipercantik lewat CSS berkali-kali (warna, ketebalan, disembunyikan). Ternyata
 * rendering scrollbar native itu beda-beda tiap kombinasi browser/OS (ada yang
 * tipis nyaris tak kelihatan, ada yang model klasik tebal dengan tombol panah di
 * ujungnya) dan hasilnya di layar pengguna sungguhan tidak pernah bisa dipastikan
 * sama dengan yang dites di lingkungan pengembangan ini. Waktu track-nya akhirnya
 * disembunyikan total supaya tidak ada risiko tabrakan tampilan, efek sampingnya
 * pengguna mouse biasa jadi kehilangan SATU-SATUNYA cara menggeser baris tab
 * (drag scrollbar) - menu yang overflow jadi tidak terjangkau sama sekali.
 *
 * Solusinya: berhenti bergantung ke scrollbar browser SAMA SEKALI. Geser sekarang
 * disediakan lewat 3 jalur yang semuanya aktif bersamaan, konsisten di browser
 * manapun karena dirender sendiri (bukan elemen bawaan browser):
 * 1. Tombol panah kiri/kanan - cuma muncul kalau memang masih ada yang bisa
 *    digeser ke arah itu, hilang otomatis begitu sudah mentok.
 * 2. Roda mouse vertikal diterjemahkan jadi geser horizontal - mouse biasa yang
 *    tidak punya scroll-horizontal bawaan tetap bisa geser tanpa perlu tahu trik
 *    apapun (tahan Shift, dst).
 * 3. Drag/swipe langsung (trackpad, layar sentuh) tetap jalan seperti biasa lewat
 *    overflow-x bawaan - tidak diubah/dihalangi.
 *
 * Pemakaian: ganti `<div ref={...} className="flex ... overflow-x-auto ...">`
 * jadi `<ScrollableTabRow className="flex ...">` (buang `overflow-x-auto`, itu
 * sudah otomatis ditambahkan komponen ini).
 */
export default function ScrollableTabRow({ children, className }) {
  const scrollRef = useRef(null);
  const [canLeft, setCanLeft] = useState(false);
  const [canRight, setCanRight] = useState(false);
  const tabCount = Children.count(children);

  const update = useCallback(() => {
    const el = scrollRef.current;
    if (!el) return;
    setCanLeft(el.scrollLeft > 4);
    setCanRight(el.scrollLeft < el.scrollWidth - el.clientWidth - 4);
  }, []);

  useEffect(() => {
    const el = scrollRef.current;
    if (!el) return;
    update();
    const ro = new ResizeObserver(update);
    ro.observe(el);
    el.addEventListener('scroll', update, { passive: true });
    window.addEventListener('resize', update);
    return () => {
      ro.disconnect();
      el.removeEventListener('scroll', update);
      window.removeEventListener('resize', update);
    };
    // tabCount: supaya dicek ulang begitu jumlah tab berubah (mis. tab baru muncul
    // setelah data tender/role diketahui), bukan cuma saat ukuran elemen berubah.
  }, [update, tabCount]);

  // Roda mouse -> geser horizontal. Dipasang manual lewat addEventListener (bukan
  // prop onWheel React) supaya preventDefault() sungguhan berlaku - React memasang
  // listener wheel sebagai passive secara default demi performa scroll, yang diam-
  // diam menolak preventDefault kalau dipasang lewat JSX onWheel.
  useEffect(() => {
    const el = scrollRef.current;
    if (!el) return;
    const onWheel = (e) => {
      if (el.scrollWidth <= el.clientWidth) return; // tidak overflow, biarkan scroll halaman jalan normal
      if (Math.abs(e.deltaX) >= Math.abs(e.deltaY)) return; // sudah geser horizontal sendiri (trackpad), jangan dicampuri
      e.preventDefault();
      el.scrollLeft += e.deltaY;
    };
    el.addEventListener('wheel', onWheel, { passive: false });
    return () => el.removeEventListener('wheel', onWheel);
  }, []);

  const scrollByStep = (dir) => {
    const el = scrollRef.current;
    if (!el) return;
    el.scrollBy({ left: dir * Math.round(el.clientWidth * 0.65), behavior: 'smooth' });
  };

  return (
    <div className="relative w-full min-w-0">
      <div ref={scrollRef} className={clsx(className, 'overflow-x-auto scroll-tabs-viewport')}>
        {children}
      </div>

      {canLeft && (
        <div className="absolute inset-y-0 left-0 z-10 w-9 flex items-center justify-start bg-gradient-to-r from-white via-white/90 to-transparent pointer-events-none">
          <button
            type="button"
            aria-label="Geser ke kiri"
            onClick={() => scrollByStep(-1)}
            className="pointer-events-auto ml-0.5 w-6 h-6 rounded-full bg-white border border-border shadow-sm flex items-center justify-center text-dpbj-navy hover:border-dpbj-gold hover:text-dpbj-gold-dark transition-colors"
          >
            <ChevronLeft size={14} strokeWidth={3} />
          </button>
        </div>
      )}

      {canRight && (
        <div className="absolute inset-y-0 right-0 z-10 w-9 flex items-center justify-end bg-gradient-to-l from-white via-white/90 to-transparent pointer-events-none">
          <button
            type="button"
            aria-label="Geser ke kanan"
            onClick={() => scrollByStep(1)}
            className="pointer-events-auto mr-0.5 w-6 h-6 rounded-full bg-white border border-border shadow-sm flex items-center justify-center text-dpbj-navy hover:border-dpbj-gold hover:text-dpbj-gold-dark transition-colors"
          >
            <ChevronRight size={14} strokeWidth={3} />
          </button>
        </div>
      )}
    </div>
  );
}
