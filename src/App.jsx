import { useState } from 'react';
import { Menu, X } from 'lucide-react';
import { AppProvider, useApp } from './context/AppContext';
import logoUIFull from './assets/logo-ui-full.png';
import LiveClock from './components/common/LiveClock';
import Sidebar from './components/layout/Sidebar';
import TopBar from './components/layout/TopBar';
import Dashboard from './pages/Dashboard';
import Pengajuan from './pages/Pengajuan';
import Tender from './pages/Tender';
import Vendor from './pages/Vendor';
import VendorProfile from './pages/VendorProfile';
import Blacklist from './pages/Blacklist';
import AuditLog from './pages/AuditLog';
import Katalog from './pages/Katalog';
import Purchasing from './pages/Purchasing';
import DataMaster from './pages/DataMaster';
import MenuAccess from './pages/MenuAccess';
import Inbox from './pages/Inbox';
import ContentManagement from './pages/ContentManagement';
import UserManagement from './pages/UserManagement';
import LoginLogs from './pages/LoginLogs';
import ApiKeys from './pages/ApiKeys';
import DocumentExpiry from './pages/DocumentExpiry';
import PublicLandingPage from './pages/PublicLandingPage';
import PublicTenderPage from './pages/PublicTenderPage';
import KontakKami from './pages/KontakKami';
import RegistrasiVendor from './pages/RegistrasiVendor';
import QrVerify from './pages/QrVerify';
import PublicPolicyPage from './pages/PublicPolicyPage';
import PrintPembukaanPenawaran from './pages/print/PrintPembukaanPenawaran';
import PrintAanwijzing from './pages/print/PrintAanwijzing';
import PrintPaktaIntegritas from './pages/print/PrintPaktaIntegritas';
import PrintSppbj from './pages/print/PrintSppbj';
import NewProcurementModal from './components/modals/NewProcurementModal';
import SettingsModal from './components/modals/SettingsModal';
import DetailPengajuanModal from './components/modals/DetailPengajuanModal';
import DetailTenderModal from './components/modals/DetailTenderModal';
import LoginModal from './components/modals/LoginModal';
import RoleSwitcherModal from './components/modals/RoleSwitcherModal';

// Halaman yang menggunakan full public layout (tanpa sidebar/topbar)
const PUBLIC_PAGES = ['public_home', 'public_tender', 'kontak', 'registrasi', 'public_blacklist', 'public_qr_verify', 'public_policy'];

// Loading screen saat cek session awal
function AuthLoadingScreen() {
  return (
    <div style={{ display:'flex', alignItems:'center', justifyContent:'center', height:'100vh', fontFamily:'sans-serif', color:'#666' }}>
      <div style={{ textAlign:'center' }}>
        <div style={{ width:40, height:40, border:'4px solid #e2e8f0', borderTopColor:'#b8962e', borderRadius:'50%', animation:'spin 0.8s linear infinite', margin:'0 auto 16px' }} />
        <p style={{ fontSize:14 }}>Memuat Sistem e-Procurement...</p>
        <style>{`@keyframes spin{to{transform:rotate(360deg)}}`}</style>
      </div>
    </div>
  );
}

// Shared public navigation bar component
function PublicNav({ activePage, navigateTo, onLoginClick }) {
  const [isMobileMenuOpen, setIsMobileMenuOpen] = useState(false);
  const NAV = [
    { label: 'Home', id: 'public_home' },
    { label: 'Tender', id: 'public_tender' },
    { label: 'Daftar Hitam', id: 'public_blacklist' },
    { label: 'Cek Dokumen', id: 'public_qr_verify' },
    { label: 'Kontak Kami', id: 'kontak' },
    { label: 'Registrasi', id: 'registrasi' },
    { label: 'Login', id: 'login' },
  ]
  return (
    <>
      <div className="bg-dpbj-navy-dark text-white text-xs py-2 px-4 md:px-6 flex justify-between items-center font-semibold">
        <LiveClock />
        <span className="hidden sm:inline">Sistem Pengadaan Barang Jasa DPBJ Universitas Indonesia</span>
      </div>
      <header className="bg-dpbj-gold border-b border-dpbj-gold-dark px-4 md:px-6 py-3 flex items-center justify-between sticky top-0 z-40 transition-all shadow-sm">
        <div className="flex items-center gap-3">
          <button className="md:hidden p-1 text-dpbj-navy-dark" onClick={() => setIsMobileMenuOpen(true)}>
            <Menu size={24} />
          </button>
          <button className="flex items-center group" onClick={() => navigateTo('public_home')}>
            <img src={logoUIFull} alt="Logo Universitas Indonesia" className="h-10 w-auto group-hover:scale-105 transition-transform drop-shadow-sm brightness-0" style={{ filter: 'brightness(0) sepia(1) hue-rotate(180deg) saturate(3) hue-rotate(20deg) brightness(0.3)' }} />
          </button>
        </div>
        <nav className="hidden md:flex items-center gap-1.5">
          {NAV.map(item => (
            <button
              key={item.id}
              onClick={() => {
                if (item.id === 'login') onLoginClick();
                else navigateTo(item.id);
              }}
              className={`px-4 py-2 text-sm font-bold transition-all relative group rounded-full ${activePage === item.id ? 'text-white bg-dpbj-navy-dark shadow-sm' : 'text-dpbj-navy hover:text-dpbj-navy-dark hover:bg-white/40'}`}
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
                    else navigateTo(item.id);
                  }}
                  className={`w-full text-left px-6 py-3 text-sm font-bold ${activePage === item.id ? 'text-dpbj-navy-dark bg-dpbj-gold' : 'text-dpbj-navy hover:bg-gray-50'}`}
                >
                  {item.label}
                </button>
              ))}

            </nav>
          </div>
        </div>
      )}
    </>
  );
}

function PublicFooter() {
  return (
    <footer className="bg-dpbj-gold text-dpbj-navy-dark text-xs py-6 px-6 mt-auto">
      <div className="max-w-5xl mx-auto flex flex-col md:flex-row justify-between items-center gap-4">
        <div className="flex items-center gap-3">
          <img src={logoUIFull} alt="Logo UI" className="h-8 w-auto grayscale contrast-200 brightness-0" />
          <div className="text-left">
            <p className="font-bold text-sm">Sistem Pengadaan Barang Jasa</p>
            <p className="font-medium">Universitas Indonesia</p>
          </div>
        </div>
        <div className="text-right">
          <p className="font-semibold">© 2025 - 2026 | DPBJ UI</p>
          <p className="opacity-75">Version 3.1.0</p>
        </div>
      </div>
    </footer>
  );
}

function AppShell() {
  const {
    activePage, setActivePage,
    qrVerifyCode,
    printDeepLink,
    isAuthLoading,
    isAuthenticated, user,
    showNewProcurementModal, closeNewProcurementModal,
    showSettingsModal, closeSettingsModal,
    selectedPengajuan, setSelectedPengajuan,
    selectedTender, setSelectedTender,
  } = useApp();

  const [showLogin, setShowLogin] = useState(false);

  // Tampilkan loading saat cek session awal (mengikuti eProc: cek Auth::me())
  if (isAuthLoading) {
    return <AuthLoadingScreen />;
  }

  const navigateTo = (page) => {
    setActivePage(page);
    setSelectedTender(null);
    setSelectedPengajuan(null);
  };

  // ── PUBLIC LAYOUT ─────────────────────────────────────────────────
  if (PUBLIC_PAGES.includes(activePage)) {

    // Landing page has its OWN header built-in
    if (activePage === 'public_home') {
      return (
        <>
          <PublicLandingPage onNavigate={navigateTo} onLoginClick={() => setShowLogin(true)} />
          <LoginModal isOpen={showLogin} onClose={() => setShowLogin(false)} onNavigateRegister={navigateTo} />
        </>
      );
    }

    const publicContent = {
      public_tender: <PublicTenderPage onNavigateHome={() => navigateTo('public_home')} />,
      public_blacklist: <Blacklist onNavigateHome={() => navigateTo('public_home')} />,
      public_qr_verify: <QrVerify initialCode={qrVerifyCode} onNavigateHome={() => navigateTo('public_home')} />,
      kontak: <KontakKami onNavigateHome={() => navigateTo('public_home')} />,
      registrasi: <RegistrasiVendor onNavigateHome={() => navigateTo('public_home')} onLoginClick={() => setShowLogin(true)} />,
      public_policy: <PublicPolicyPage onNavigateHome={() => navigateTo('public_home')} />,
    };

    return (
      <div className="min-h-screen flex flex-col bg-gray-50">
        <PublicNav activePage={activePage} navigateTo={navigateTo} onLoginClick={() => setShowLogin(true)} />
        <main className="flex-1 p-6 max-w-5xl mx-auto w-full">
          <div key={activePage} className="page-transition">
            {publicContent[activePage]}
          </div>
        </main>
        <PublicFooter />
        <LoginModal isOpen={showLogin} onClose={() => setShowLogin(false)} onNavigateRegister={navigateTo} />
      </div>
    );
  }

  // ── INTERNAL (DASHBOARD) LAYOUT ───────────────────────────────────
  // Guard: jika belum login, redirect ke landing page (mengikuti eProc session check)
  if (!isAuthenticated || !user) {
    // Belum login tapi mencoba akses halaman internal → tampilkan landing page
    return (
      <>
        <PublicLandingPage onNavigate={navigateTo} onLoginClick={() => setShowLogin(true)} />
        <LoginModal isOpen={showLogin} onClose={() => setShowLogin(false)} onNavigateRegister={navigateTo} />
      </>
    );
  }

  // Halaman cetak dokumen resmi - full-screen tanpa sidebar/topbar, supaya hasil cetak/PDF
  // bersih (lihat src/pages/print/). Ditaruh di sini (sesudah guard login) karena butuh akun
  // yang sudah login, beda dari /verify/KODE yang publik.
  if (activePage === 'print_document' && printDeepLink) {
    const backToTender = () => { setActivePage('tender'); window.history.replaceState({}, '', '/'); };
    const printPages = {
      'pembukaan-penawaran': <PrintPembukaanPenawaran tenderId={printDeepLink.tenderId} onBack={backToTender} />,
      'aanwijzing': <PrintAanwijzing tenderId={printDeepLink.tenderId} onBack={backToTender} />,
      'pakta-integritas': <PrintPaktaIntegritas tenderId={printDeepLink.tenderId} vendorId={printDeepLink.vendorId} onBack={backToTender} />,
      'sppbj': <PrintSppbj tenderId={printDeepLink.tenderId} onBack={backToTender} />,
    };
    return printPages[printDeepLink.jenis] || <Dashboard />;
  }

  const pages = {
    dashboard: <Dashboard />,
    pengajuan: <Pengajuan />,
    tender: <Tender />,
    vendor: <Vendor />,
    vendor_profile: <VendorProfile />,
    blacklist: <Blacklist onNavigateHome={() => navigateTo('public_home')} />,
    audit: <AuditLog />,
    katalog: <Katalog />,
    purchasing: <Purchasing />,
    master_data: <DataMaster />,
    menu_access: <MenuAccess />,
    inbox: <Inbox />,
    content_management: <ContentManagement />,
    user_management: <UserManagement />,
    login_logs: <LoginLogs />,
    api_keys: <ApiKeys />,
    document_expiry: <DocumentExpiry />,
  };

  const renderPage = () => pages[activePage] || <Dashboard />;

  return (
    <div className="flex h-screen bg-background overflow-hidden text-dpbj-navy">
      <Sidebar />
      <div className="flex-1 flex flex-col min-w-0">
        <TopBar />
        <main className="flex-1 overflow-y-auto p-6 md:p-8">
          <div key={activePage} className="max-w-7xl mx-auto page-transition">
            {renderPage()}
          </div>
        </main>
      </div>
      <NewProcurementModal isOpen={showNewProcurementModal} onClose={closeNewProcurementModal} />
      <SettingsModal isOpen={showSettingsModal} onClose={closeSettingsModal} />
      <DetailPengajuanModal isOpen={!!selectedPengajuan} data={selectedPengajuan} onClose={() => setSelectedPengajuan(null)} />
      <DetailTenderModal isOpen={!!selectedTender} data={selectedTender} onClose={() => setSelectedTender(null)} />
      <RoleSwitcherModal />
    </div>
  );
}

export default function App() {
  return (
    <AppProvider>
      <AppShell />
    </AppProvider>
  );
}
