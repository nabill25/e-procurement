import { useState, useEffect } from 'react';
import { Menu, X } from 'lucide-react';
import { motion, AnimatePresence } from 'framer-motion';
import logoUIFull from '../../assets/logo-ui-full.png';
import LiveClock from '../common/LiveClock';

const NAV = [
  { label: 'Home', id: 'public_home' },
  { label: 'Tender', id: 'public_tender' },
  { label: 'Daftar Hitam', id: 'public_blacklist' },
  { label: 'Cek Dokumen', id: 'public_qr_verify' },
  { label: 'Kontak Kami', id: 'kontak' },
  { label: 'Registrasi', id: 'registrasi' },
  { label: 'Login', id: 'login' },
];

// Navbar bersama untuk SELURUH halaman portal publik (dulunya ada 2 salinan hampir
// identik: satu di App.jsx untuk halaman publik non-home, satu lagi inline di dalam
// PublicLandingPage.jsx khusus halaman home - disatukan di sini supaya perubahan
// tampilan/perilaku navbar cukup dilakukan di satu tempat).
//
// Perilaku baru dibanding versi lama: header sedikit "menyusut" (padding vertikal
// mengecil + bayangan menguat) begitu halaman discroll ke bawah, memberi kesan lebih
// hidup/reaktif alih-alih navbar statis. Menu mobile sekarang benar-benar beranimasi
// keluar juga saat ditutup (Framer Motion AnimatePresence), bukan cuma langsung hilang
// seperti sebelumnya, dan item menunya muncul bertahap (stagger) saat drawer dibuka.
export default function PublicNav({ activePage, navigateTo, onLoginClick }) {
  const [isMobileMenuOpen, setIsMobileMenuOpen] = useState(false);
  const [scrolled, setScrolled] = useState(false);

  useEffect(() => {
    const onScroll = () => setScrolled(window.scrollY > 12);
    onScroll();
    window.addEventListener('scroll', onScroll, { passive: true });
    return () => window.removeEventListener('scroll', onScroll);
  }, []);

  return (
    <>
      <div className="bg-dpbj-navy-dark text-white text-xs py-2 px-4 md:px-6 flex justify-between items-center font-semibold">
        <LiveClock />
        <span className="hidden sm:inline">Sistem Pengadaan Barang Jasa DPBJ Universitas Indonesia</span>
      </div>

      <header
        className={`bg-dpbj-gold border-b border-dpbj-gold-dark px-4 md:px-6 flex items-center justify-between sticky top-0 z-40 transition-all duration-300 ${
          scrolled ? 'py-2 shadow-lg' : 'py-3 shadow-sm'
        }`}
      >
        <div className="flex items-center gap-3">
          <button className="md:hidden p-1 text-dpbj-navy-dark" onClick={() => setIsMobileMenuOpen(true)} aria-label="Buka menu">
            <Menu size={24} />
          </button>
          <button className="flex items-center group" onClick={() => navigateTo('public_home')}>
            <img
              src={logoUIFull}
              alt="Logo Universitas Indonesia"
              className={`w-auto group-hover:scale-105 transition-all duration-300 drop-shadow-sm ${scrolled ? 'h-8' : 'h-10'}`}
              style={{ filter: 'brightness(0) sepia(1) hue-rotate(180deg) saturate(3) hue-rotate(20deg) brightness(0.3)' }}
            />
          </button>
        </div>
        <nav className="hidden md:flex items-center gap-1">
          {NAV.map(item => {
            const isActive = activePage === item.id;
            return (
              <button
                key={item.id}
                onClick={() => { if (item.id === 'login') onLoginClick(); else navigateTo(item.id); }}
                className="relative px-4 py-2 text-sm font-bold transition-colors rounded-full text-dpbj-navy hover:text-dpbj-navy-dark"
              >
                {isActive && (
                  <motion.span
                    layoutId="public-nav-pill"
                    className="absolute inset-0 bg-dpbj-navy-dark rounded-full shadow-sm"
                    transition={{ type: 'spring', stiffness: 380, damping: 32 }}
                  />
                )}
                <span className={`relative z-10 ${isActive ? 'text-white' : ''}`}>{item.label}</span>
              </button>
            );
          })}
        </nav>
      </header>

      {/* Mobile Menu Drawer - AnimatePresence memastikan animasi KELUAR juga jalan
          (versi CSS lama cuma beranimasi masuk, langsung hilang begitu ditutup) */}
      <AnimatePresence>
        {isMobileMenuOpen && (
          <motion.div
            className="fixed inset-0 z-50 flex md:hidden"
            initial={{ opacity: 0 }}
            animate={{ opacity: 1 }}
            exit={{ opacity: 0 }}
            transition={{ duration: 0.2 }}
          >
            <motion.div
              className="fixed inset-0 bg-dpbj-navy-dark/60 backdrop-blur-sm"
              onClick={() => setIsMobileMenuOpen(false)}
            />
            <motion.div
              className="relative w-[280px] bg-white h-full flex flex-col shadow-2xl"
              initial={{ x: '-100%' }}
              animate={{ x: 0 }}
              exit={{ x: '-100%' }}
              transition={{ type: 'spring', stiffness: 320, damping: 34 }}
            >
              <div className="p-4 border-b border-gray-100 flex items-center justify-between">
                <img src={logoUIFull} alt="Logo Universitas Indonesia" className="h-8 w-auto" />
                <button className="p-1 text-gray-500 hover:text-dpbj-navy transition-colors" onClick={() => setIsMobileMenuOpen(false)} aria-label="Tutup menu">
                  <X size={20} />
                </button>
              </div>
              <nav className="flex-1 overflow-y-auto py-4">
                {NAV.map((item, i) => (
                  <motion.button
                    key={item.id}
                    initial={{ opacity: 0, x: -16 }}
                    animate={{ opacity: 1, x: 0 }}
                    transition={{ delay: 0.05 + i * 0.04, duration: 0.25, ease: [0.16, 1, 0.3, 1] }}
                    onClick={() => {
                      setIsMobileMenuOpen(false);
                      if (item.id === 'login') onLoginClick();
                      else navigateTo(item.id);
                    }}
                    className={`w-full text-left px-6 py-3 text-sm font-bold transition-colors ${
                      activePage === item.id ? 'text-dpbj-navy-dark bg-dpbj-gold' : 'text-dpbj-navy hover:bg-gray-50'
                    }`}
                  >
                    {item.label}
                  </motion.button>
                ))}
              </nav>
            </motion.div>
          </motion.div>
        )}
      </AnimatePresence>
    </>
  );
}
