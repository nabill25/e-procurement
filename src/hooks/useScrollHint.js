import { useState, useEffect, useCallback } from 'react';

// Ditemukan 2026-09-03 (laporan pengguna, screenshot desktop): petunjuk "geser tabel/tab"
// yang sudah baku di sistem ini (class CSS `table-scroll-hint`) pakai `sm:hidden` - asumsinya
// cuma layar HP yang butuh petunjuk itu, karena TABEL BIASA (kolom sedikit) memang biasanya
// muat penuh di layar desktop. Asumsi itu SALAH untuk BARIS TAB yang isinya banyak (sampai 9
// tab di modal Detail Tender, atau 27 kategori di Data Master) - ini bisa tetap kepotong
// walau sudah di layar desktop lebar, dan waktu itu terjadi petunjuknya malah tersembunyi
// (karena `sm:hidden`), jadi terkesan seperti bug lagi padahal cuma variasi lebar layar.
//
// Hook ini menggantikan pendekatan berbasis lebar-layar itu dengan deteksi LANGSUNG: benar-
// benar cek apakah kontennya melebihi lebar kontainer atau tidak (dicek ulang tiap kali ukuran
// berubah lewat ResizeObserver, dan tiap kali daftar tab berubah lewat dependency `deps`),
// jadi petunjuknya muncul kapanpun dia sungguhan dibutuhkan - baik di HP maupun desktop sempit
// - dan otomatis sembunyi begitu tidak lagi relevan (layar dilebarkan, atau tab-nya berkurang).
//
// Pemakaian:
//   const tabBarRef = useRef(null);
//   const showHint = useHorizontalScrollHint(tabBarRef, [daftarTab.length]);
//   ...
//   {showHint && <p className="table-scroll-hint !hidden sm:!flex">...</p>}
//   <div ref={tabBarRef} className="overflow-x-auto ...">...</div>
export function useHorizontalScrollHint(ref, deps = []) {
  const [showHint, setShowHint] = useState(false);

  const check = useCallback(() => {
    const el = ref.current;
    if (!el) return;
    setShowHint(el.scrollWidth > el.clientWidth + 2);
  }, [ref]);

  useEffect(() => {
    check();
    const el = ref.current;
    if (!el) return;
    const ro = new ResizeObserver(check);
    ro.observe(el);
    window.addEventListener('resize', check);
    return () => {
      ro.disconnect();
      window.removeEventListener('resize', check);
    };
    // eslint-disable-next-line react-hooks/exhaustive-deps
  }, [check, ...deps]);

  return showHint;
}
