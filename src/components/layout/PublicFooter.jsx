import logoUIFull from '../../assets/logo-ui-full.png';

// Footer bersama portal publik (disatukan dari 2 salinan hampir identik yang tadinya
// ada di App.jsx dan PublicLandingPage.jsx - versi App.jsx bahkan tidak punya tautan
// "Kebijakan" sama sekali, jadi halaman publik selain home sebelumnya tidak bisa
// menjangkau halaman Kebijakan lewat footer).
export default function PublicFooter({ onNavigate }) {
  return (
    <footer className="bg-dpbj-gold text-dpbj-navy-dark text-xs py-8 px-6 mt-auto border-t-4 border-dpbj-navy-dark">
      <div className="max-w-5xl mx-auto flex flex-col md:flex-row justify-between items-center gap-6">
        <div className="flex flex-wrap items-center justify-center md:justify-start gap-4">
          <img src={logoUIFull} alt="Logo UI" className="h-auto w-32 sm:h-10 sm:w-auto grayscale contrast-200 brightness-0" />
          <div className="text-left">
            <p className="font-bold text-base">Sistem Pengadaan Barang Jasa</p>
            <p className="font-medium text-sm">Universitas Indonesia</p>
          </div>
        </div>
        <div className="text-center md:text-right">
          {onNavigate && (
            <button onClick={() => onNavigate('public_policy')} className="text-xs font-semibold hover:underline mb-2 inline-block">
              Kebijakan
            </button>
          )}
          <p className="font-bold text-sm mb-1">© 2025 - 2026 | DPBJ UI</p>
          <p className="opacity-80 font-medium">Version 3.1.0</p>
        </div>
      </div>
    </footer>
  );
}
