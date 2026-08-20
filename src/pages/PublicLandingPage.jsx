import { useState, useEffect } from 'react';
import { ChevronLeft, ChevronRight, AlertTriangle, Briefcase, ClipboardList, ArrowRight, Phone, Mail, MapPin, Menu, X, Newspaper, HelpCircle, ChevronDown } from 'lucide-react';
import LiveClock from '../components/common/LiveClock';
import logoUIFull from '../assets/logo-ui-full.png';
import { API_BASE } from '../context/AppContext';

function NewsAndFaqSection() {
  const [news, setNews] = useState([]);
  const [faqs, setFaqs] = useState([]);
  const [openFaq, setOpenFaq] = useState(null);

  useEffect(() => {
    fetch(`${API_BASE}/cms/news`).then(res => res.json()).then(json => {
      if (json.success) setNews(json.data.slice(0, 3));
    }).catch(() => {});
    fetch(`${API_BASE}/cms/faq`).then(res => res.json()).then(json => {
      if (json.success) setFaqs(json.data);
    }).catch(() => {});
  }, []);

  if (news.length === 0 && faqs.length === 0) return null;

  return (
    <section className="py-12 px-6 bg-white">
      <div className="max-w-4xl mx-auto grid grid-cols-1 md:grid-cols-2 gap-10">
        {news.length > 0 && (
          <div>
            <h2 className="font-serif font-bold text-dpbj-navy text-2xl mb-6 flex items-center gap-2">
              <Newspaper size={22} className="text-dpbj-gold" /> Berita & Pengumuman
            </h2>
            <div className="space-y-4">
              {news.map(item => (
                <div key={item.id} className="p-4 bg-surface rounded-xl border border-gray-100">
                  <p className="font-bold text-dpbj-navy text-sm">{item.title}</p>
                  <p className="text-xs text-gray-600 mt-1 line-clamp-3">{item.content}</p>
                  <p className="text-[10px] text-gray-400 mt-2">{new Date(item.created_at).toLocaleDateString('id-ID')}</p>
                </div>
              ))}
            </div>
          </div>
        )}

        {faqs.length > 0 && (
          <div>
            <h2 className="font-serif font-bold text-dpbj-navy text-2xl mb-6 flex items-center gap-2">
              <HelpCircle size={22} className="text-dpbj-gold" /> Pertanyaan Umum
            </h2>
            <div className="space-y-2">
              {faqs.map(item => (
                <div key={item.id} className="border border-gray-100 rounded-xl overflow-hidden">
                  <button
                    onClick={() => setOpenFaq(openFaq === item.id ? null : item.id)}
                    className="w-full flex items-center justify-between gap-3 p-4 text-left bg-surface hover:bg-gray-100 transition-colors"
                  >
                    <span className="font-semibold text-dpbj-navy text-sm">{item.question}</span>
                    <ChevronDown size={16} className={`text-gray-400 transition-transform shrink-0 ${openFaq === item.id ? 'rotate-180' : ''}`} />
                  </button>
                  {openFaq === item.id && (
                    <p className="p-4 text-xs text-gray-600 leading-relaxed border-t border-gray-100">{item.answer}</p>
                  )}
                </div>
              ))}
            </div>
          </div>
        )}
      </div>
    </section>
  );
}

const slides = [
  {
    gradient: 'from-dpbj-navy-dark via-dpbj-navy to-slate-700',
    tagline: 'Transparansi & Integritas Pengadaan',
    desc: 'Platform resmi pengadaan barang dan jasa Universitas Indonesia berbasis prinsip Good Corporate Governance.',
  },
  {
    gradient: 'from-slate-800 via-dpbj-navy-dark to-dpbj-navy',
    tagline: 'Mendukung Vendor Management System',
    desc: 'Registrasi, verifikasi, dan kelola data penyedia secara digital, aman, dan akuntabel.',
  },
];

export default function PublicLandingPage({ onNavigate, onLoginClick }) {
  const [slide, setSlide] = useState(0);
  const [isMobileMenuOpen, setIsMobileMenuOpen] = useState(false);

  useEffect(() => {
    const t = setInterval(() => setSlide(s => (s + 1) % slides.length), 5000);
    return () => clearInterval(t);
  }, []);

  const NAV = [
    { label: 'Home', id: 'public_home' },
    { label: 'Tender', id: 'public_tender' },
    { label: 'Daftar Hitam', id: 'public_blacklist' },
    { label: 'Kontak Kami', id: 'kontak' },
    { label: 'Registrasi', id: 'registrasi' },
    { label: 'Login', id: 'login' },
  ];

  return (
    <div className="min-h-screen flex flex-col bg-gray-50">
      {/* Ticker */}
      <div className="bg-dpbj-navy-dark text-white text-xs py-2 px-6 flex justify-between items-center font-semibold">
        <LiveClock />
        <span className="hidden sm:block">Sistem Pengadaan Barang Jasa DPBJ Universitas Indonesia</span>
      </div>

      {/* Nav */}
      <header className="bg-dpbj-gold border-b border-dpbj-gold-dark px-4 md:px-6 py-3 flex items-center justify-between shadow-sm sticky top-0 z-40">
        <div className="flex items-center gap-3">
          <button className="md:hidden p-1 text-dpbj-navy-dark" onClick={() => setIsMobileMenuOpen(true)}>
            <Menu size={24} />
          </button>
          <button className="flex items-center group" onClick={() => onNavigate('public_home')}>
            <img src={logoUIFull} alt="Logo Universitas Indonesia" className="h-10 w-auto group-hover:scale-105 transition-transform drop-shadow-sm brightness-0" style={{ filter: 'brightness(0) sepia(1) hue-rotate(180deg) saturate(3) hue-rotate(20deg) brightness(0.3)' }} />
          </button>
        </div>
        <nav className="hidden md:flex items-center gap-1.5">
          {NAV.map(item => (
            <button
              key={item.id}
              onClick={() => {
                if (item.id === 'login') onLoginClick();
                else onNavigate(item.id);
              }}
              className={`px-4 py-2 text-sm font-bold transition-all relative group rounded-full ${item.id === 'public_home' ? 'text-white bg-dpbj-navy-dark shadow-sm' : 'text-dpbj-navy hover:text-dpbj-navy-dark hover:bg-white/40'}`}
            >
              {item.label}
            </button>
          ))}
        </nav>
      </header>

      {/* Mobile Menu Drawer */}
      {isMobileMenuOpen && (
        <div className="fixed inset-0 z-50 flex animate-fade-in md:hidden">
          {/* Overlay */}
          <div className="fixed inset-0 bg-black/50" onClick={() => setIsMobileMenuOpen(false)} />
          
          {/* Sidebar */}
          <div className="relative w-[280px] bg-white h-full flex flex-col animate-slide-in-right" style={{ animationDirection: 'normal', transformOrigin: 'left' }}>
            <div className="p-4 border-b border-gray-100 flex items-center justify-between">
              <div className="flex items-center">
                <img src={logoUIFull} alt="Logo Universitas Indonesia" className="h-8 w-auto" />
              </div>
              <button className="p-1 text-gray-500" onClick={() => setIsMobileMenuOpen(false)}>
                <X size={20} />
              </button>
            </div>
            
            <nav className="flex-1 overflow-y-auto py-4">
              {NAV.map(item => (
                <button
                  key={item.id}
                  onClick={() => { 
                    setIsMobileMenuOpen(false); 
                    if (item.id === 'login') onLoginClick();
                    else onNavigate(item.id); 
                  }}
                  className={`w-full text-left px-6 py-3 text-sm font-bold ${item.id === 'public_home' ? 'text-dpbj-navy-dark bg-dpbj-gold' : 'text-dpbj-navy hover:bg-gray-50'}`}
                >
                  {item.label}
                </button>
              ))}
            </nav>
          </div>
        </div>
      )}

      {/* Hero Slider */}
      <div className="relative overflow-hidden" style={{ height: '420px' }}>
        {slides.map((s, i) => (
          <div
            key={i}
            className={`absolute inset-0 transition-opacity duration-1000 ${i === slide ? 'opacity-100 z-0' : 'opacity-0 -z-10'}`}
          >
            {/* Background image with Ken Burns */}
            <div 
              className={`absolute inset-0 bg-cover bg-center ${i === slide ? 'animate-ken-burns' : ''}`}
              style={{ backgroundImage: "url('/images/rektorat-baru.jpg')" }}
            />
            {/* Dark overlay gradients for readability */}
            <div className="absolute inset-0 bg-dpbj-navy-dark/70" />
            <div className="absolute inset-0 bg-gradient-to-t from-dpbj-navy-dark via-transparent to-transparent opacity-80" />
            <div className="absolute inset-0 bg-gradient-to-b from-dpbj-navy-dark/50 via-transparent to-transparent opacity-60" />
          </div>
        ))}

        {/* Overlay pattern */}
        <div className="absolute inset-0 opacity-10 pointer-events-none" style={{ backgroundImage: 'radial-gradient(circle at 2px 2px, rgba(255,255,255,0.3) 1px, transparent 0)', backgroundSize: '32px 32px' }} />

        {/* Content */}
        <div className="relative z-10 h-full flex flex-col items-center justify-end text-center px-8 pb-10">
          {/* Dynamic tagline */}
          <div className="w-full max-w-2xl h-16 relative">
            {slides.map((s, i) => (
              <div key={i} className={`absolute inset-0 w-full flex flex-col items-center justify-center transition-all duration-700 transform ${i === slide ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-4 pointer-events-none'}`}>
                <p className="text-white text-lg md:text-2xl font-bold tracking-tight drop-shadow-lg">{s.tagline}</p>
                <p className="text-white/90 text-sm md:text-base mt-2 hidden md:block drop-shadow-md max-w-2xl">{s.desc}</p>
              </div>
            ))}
          </div>
        </div>

        {/* Controls */}
        <button onClick={() => setSlide(s => (s - 1 + slides.length) % slides.length)}
          className="absolute left-4 top-1/2 -translate-y-1/2 bg-black/30 hover:bg-black/60 backdrop-blur text-white w-10 h-10 rounded-full flex items-center justify-center transition-all hover:scale-110 z-20 shadow-lg border border-white/10">
          <ChevronLeft size={20} />
        </button>
        <button onClick={() => setSlide(s => (s + 1) % slides.length)}
          className="absolute right-4 top-1/2 -translate-y-1/2 bg-black/30 hover:bg-black/60 backdrop-blur text-white w-10 h-10 rounded-full flex items-center justify-center transition-all hover:scale-110 z-20 shadow-lg border border-white/10">
          <ChevronRight size={20} />
        </button>
        <div className="absolute bottom-6 left-1/2 -translate-x-1/2 flex gap-3 z-20">
          {slides.map((_, i) => (
            <button key={i} onClick={() => setSlide(i)}
              className={`rounded-full transition-all duration-500 shadow-md ${i === slide ? 'bg-dpbj-gold w-8 h-2.5' : 'bg-white/40 hover:bg-white/60 w-2.5 h-2.5'}`} />
          ))}
        </div>
      </div>

      {/* Quick Access Cards */}
      <section className="py-12 px-6 bg-white">
        <div className="max-w-3xl mx-auto">
          <h2 className="text-center font-serif text-3xl font-bold text-dpbj-navy mb-10">Layanan Utama</h2>
          <div className="grid grid-cols-1 sm:grid-cols-2 gap-8 max-w-2xl mx-auto">
            {/* TENDER CARD */}
            <button
              onClick={() => onNavigate('public_tender')}
              className="group relative overflow-hidden bg-gradient-to-br from-white to-gray-50 rounded-3xl shadow-md border border-gray-100 p-8 flex flex-col items-center gap-6 hover:shadow-2xl hover:-translate-y-3 transition-all duration-500"
            >
              <div className="absolute -top-12 -right-12 w-40 h-40 bg-orange-400/5 rounded-full blur-3xl group-hover:bg-orange-400/20 transition-all duration-500 pointer-events-none" />
              
              <div className="relative w-28 h-28 flex items-center justify-center group-hover:scale-110 transition-transform duration-500">
                <div className="absolute inset-0 bg-gradient-to-br from-orange-200 to-orange-100 rounded-2xl transform rotate-6 group-hover:rotate-12 transition-transform duration-500 shadow-sm" />
                <div className="absolute inset-0 bg-white rounded-2xl shadow-md transform -rotate-3 group-hover:rotate-0 transition-transform duration-500 flex items-center justify-center border border-orange-50">
                  <Briefcase size={48} strokeWidth={1.5} className="text-orange-500 drop-shadow-sm" />
                </div>
              </div>

              <div className="text-center relative z-10">
                <p className="text-dpbj-navy font-black text-lg tracking-widest mb-1.5 group-hover:text-orange-500 transition-colors">TENDER</p>
                <p className="text-sm text-gray-500">Lihat dan ikuti paket pengadaan aktif</p>
              </div>

              <div className="mt-2 w-12 h-12 rounded-full bg-orange-50 flex items-center justify-center group-hover:bg-orange-500 transition-colors duration-300 shadow-inner group-hover:shadow-lg">
                <ArrowRight size={20} className="text-orange-400 group-hover:text-white transition-colors" />
              </div>
            </button>

            {/* REGISTRASI CARD */}
            <button
              onClick={() => onNavigate('registrasi')}
              className="group relative overflow-hidden bg-gradient-to-br from-white to-gray-50 rounded-3xl shadow-md border border-gray-100 p-8 flex flex-col items-center gap-6 hover:shadow-2xl hover:-translate-y-3 transition-all duration-500"
            >
              <div className="absolute -top-12 -right-12 w-40 h-40 bg-blue-400/5 rounded-full blur-3xl group-hover:bg-blue-400/20 transition-all duration-500 pointer-events-none" />
              
              <div className="relative w-28 h-28 flex items-center justify-center group-hover:scale-110 transition-transform duration-500">
                <div className="absolute inset-0 bg-gradient-to-br from-blue-200 to-blue-100 rounded-2xl transform rotate-6 group-hover:rotate-12 transition-transform duration-500 shadow-sm" />
                <div className="absolute inset-0 bg-white rounded-2xl shadow-md transform -rotate-3 group-hover:rotate-0 transition-transform duration-500 flex items-center justify-center border border-blue-50">
                  <ClipboardList size={48} strokeWidth={1.5} className="text-blue-500 drop-shadow-sm" />
                </div>
              </div>

              <div className="text-center relative z-10">
                <p className="text-dpbj-navy font-black text-lg tracking-widest mb-1.5 group-hover:text-blue-500 transition-colors">REGISTRASI</p>
                <p className="text-sm text-gray-500">Daftarkan perusahaan Anda sebagai mitra</p>
              </div>

              <div className="mt-2 w-12 h-12 rounded-full bg-blue-50 flex items-center justify-center group-hover:bg-blue-500 transition-colors duration-300 shadow-inner group-hover:shadow-lg">
                <ArrowRight size={20} className="text-blue-400 group-hover:text-white transition-colors" />
              </div>
            </button>
          </div>
        </div>
      </section>


      {/* Pengumuman & Berita */}
      <section className="py-12 px-6 bg-white border-t border-gray-100">
        <div className="max-w-4xl mx-auto">
          <div className="flex items-center justify-between mb-8">
            <div>
              <h2 className="font-serif font-bold text-dpbj-navy text-3xl mb-1">Pengumuman & Berita</h2>
              <p className="text-sm font-medium text-muted uppercase tracking-widest">Informasi Terbaru</p>
            </div>
            <button onClick={() => onNavigate('public_tender')} className="text-sm text-dpbj-gold hover:text-dpbj-gold-dark font-semibold flex items-center gap-1 transition-colors">
              Lihat Semua <ArrowRight size={14} />
            </button>
          </div>
          <div className="bg-gradient-to-br from-gray-50 to-gray-100/50 border border-gray-200 rounded-3xl p-12 text-center shadow-inner">
            <div className="w-16 h-16 bg-white rounded-full flex items-center justify-center mx-auto mb-4 shadow-sm text-gray-400">
              <ClipboardList size={28} />
            </div>
            <p className="text-dpbj-navy font-semibold text-base mb-1">Belum ada pengumuman terbaru saat ini.</p>
            <p className="text-sm text-gray-500">Informasi akan ditampilkan di sini secara otomatis.</p>
          </div>
        </div>
      </section>

      {/* Anti-Fraud Warning */}
      <section className="py-10 px-6 bg-amber-50 border-t border-amber-100">
        <div className="max-w-3xl mx-auto flex items-start gap-6">
          <div className="w-16 h-16 flex-shrink-0 bg-yellow-100 rounded-2xl flex items-center justify-center">
            <AlertTriangle size={36} className="text-yellow-500" strokeWidth={1.5} />
          </div>
          <div>
            <h3 className="font-bold text-dpbj-navy text-base mb-2">⚠️ HATI-HATI DENGAN PENIPUAN!</h3>
            <p className="text-sm text-gray-700 leading-relaxed">
              <strong>DPBJ Universitas Indonesia tidak pernah memungut biaya atau meminta uang</strong> dari calon penyedia barang/jasa
              dalam proses pendaftaran Vendor Management System (VMS)
              maupun dalam proses pengadaan barang/jasa.
            </p>
            <p className="text-sm text-gray-600 mt-2 font-medium">Terima Kasih</p>
          </div>
        </div>
      </section>

      <NewsAndFaqSection />

      {/* Contact Quick Info */}
      <section className="py-12 px-6 bg-surface">
        <div className="max-w-4xl mx-auto">
          <h2 className="text-center font-serif font-bold text-dpbj-navy text-3xl mb-10">Kontak & Bantuan</h2>
          <div className="grid grid-cols-1 md:grid-cols-3 gap-6 text-center text-sm">
            {[
              { icon: Phone, label: 'Telepon', value: '(021) 786-7222' },
              { icon: Mail, label: 'Email', value: 'dpbj@ui.ac.id' },
              { icon: MapPin, label: 'Alamat', value: 'Kampus UI Depok, 16424' },
            ].map(({ icon: Icon, label, value }) => (
              <div key={label} className="flex flex-col items-center gap-3 p-6 bg-white rounded-2xl shadow-sm border border-gray-100 group hover:shadow-md transition-all">
                <div className="w-12 h-12 bg-dpbj-gold-faint rounded-xl flex items-center justify-center group-hover:bg-dpbj-gold transition-colors">
                  <Icon size={20} className="text-dpbj-navy" />
                </div>
                <div>
                  <p className="text-xs text-gray-500 font-medium uppercase tracking-wider mb-1">{label}</p>
                  <p className="text-dpbj-navy font-bold text-base">{value}</p>
                </div>
              </div>
            ))}
          </div>
        </div>
      </section>

      {/* Footer */}
      <footer className="bg-dpbj-gold text-dpbj-navy-dark text-xs py-8 px-6 mt-auto border-t-4 border-dpbj-navy-dark">
        <div className="max-w-5xl mx-auto flex flex-col md:flex-row justify-between items-center gap-6">
          <div className="flex items-center gap-4">
            <img src={logoUIFull} alt="Logo UI" className="h-10 w-auto grayscale contrast-200 brightness-0" />
            <div className="text-left">
              <p className="font-bold text-base">Sistem Pengadaan Barang Jasa</p>
              <p className="font-medium text-sm">Universitas Indonesia</p>
            </div>
          </div>
          <div className="text-center md:text-right">
            <p className="font-bold text-sm mb-1">© 2025 - 2026 | DPBJ UI</p>
            <p className="opacity-80 font-medium">Version 3.1.0</p>
          </div>
        </div>
      </footer>
    </div>
  );
}
