import { useState } from 'react';
import { AnimatePresence, motion } from 'framer-motion';
import { AppProvider, useApp } from './context/AppContext';
import Sidebar from './components/layout/Sidebar';
import TopBar from './components/layout/TopBar';
import PublicNav from './components/layout/PublicNav';
import PublicFooter from './components/layout/PublicFooter';
import ToastContainer from './components/common/ToastContainer';
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
import Integration from './pages/Integration';
import ExecutiveDashboard from './pages/ExecutiveDashboard';
import OracleSupplierSetup from './pages/OracleSupplierSetup';
import PublicLandingPage from './pages/PublicLandingPage';
import PublicTenderPage from './pages/PublicTenderPage';
import KontakKami from './pages/KontakKami';
import RegistrasiVendor from './pages/RegistrasiVendor';
import QrVerify from './pages/QrVerify';
import PublicPolicyPage from './pages/PublicPolicyPage';
import ResetPasswordPage from './pages/ResetPasswordPage';
import PrintPembukaanPenawaran from './pages/print/PrintPembukaanPenawaran';
import PrintAanwijzing from './pages/print/PrintAanwijzing';
import PrintPaktaIntegritas from './pages/print/PrintPaktaIntegritas';
import PrintSppbj from './pages/print/PrintSppbj';
import PrintKontrak from './pages/print/PrintKontrak';
import PrintEvaluasiKualifikasi from './pages/print/PrintEvaluasiKualifikasi';
import PrintEvaluasiRekapitulasi from './pages/print/PrintEvaluasiRekapitulasi';
import PrintPengajuan from './pages/print/PrintPengajuan';
import PrintSkt from './pages/print/PrintSkt';
import PrintJadwal from './pages/print/PrintJadwal';
import PrintRekamJejak from './pages/print/PrintRekamJejak';
import PrintPernyataanMinat from './pages/print/PrintPernyataanMinat';
import PrintKlarifikasi from './pages/print/PrintKlarifikasi';
import PrintNegosiasi from './pages/print/PrintNegosiasi';
import PrintSpmk from './pages/print/PrintSpmk';
import PrintSppjb from './pages/print/PrintSppjb';
import PrintSuratPesanan from './pages/print/PrintSuratPesanan';
import PrintDaftarPeserta from './pages/print/PrintDaftarPeserta';
import NewProcurementModal from './components/modals/NewProcurementModal';
import SettingsModal from './components/modals/SettingsModal';
import DetailPengajuanModal from './components/modals/DetailPengajuanModal';
import DetailTenderModal from './components/modals/DetailTenderModal';
import LoginModal from './components/modals/LoginModal';
import RoleSwitcherModal from './components/modals/RoleSwitcherModal';

// Halaman yang menggunakan full public layout (tanpa sidebar/topbar)
const PUBLIC_PAGES = ['public_home', 'public_tender', 'kontak', 'registrasi', 'public_blacklist', 'public_qr_verify', 'public_policy', 'reset_password'];

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

function AppShell() {
  const {
    activePage, setActivePage,
    qrVerifyCode,
    printDeepLink,
    resetPasswordToken,
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
      reset_password: <ResetPasswordPage token={resetPasswordToken} onNavigateHome={() => navigateTo('public_home')} onLoginClick={() => setShowLogin(true)} />,
      kontak: <KontakKami onNavigateHome={() => navigateTo('public_home')} />,
      registrasi: <RegistrasiVendor onNavigateHome={() => navigateTo('public_home')} onLoginClick={() => setShowLogin(true)} />,
      public_policy: <PublicPolicyPage onNavigateHome={() => navigateTo('public_home')} />,
    };

    return (
      <div className="min-h-screen flex flex-col bg-gray-50">
        <PublicNav activePage={activePage} navigateTo={navigateTo} onLoginClick={() => setShowLogin(true)} />
        <main className="flex-1 p-6 max-w-5xl mx-auto w-full">
          <AnimatePresence mode="wait">
            <motion.div
              key={activePage}
              initial={{ opacity: 0, y: 12 }}
              animate={{ opacity: 1, y: 0 }}
              exit={{ opacity: 0, y: -8 }}
              transition={{ duration: 0.25, ease: [0.16, 1, 0.3, 1] }}
            >
              {publicContent[activePage]}
            </motion.div>
          </AnimatePresence>
        </main>
        <PublicFooter onNavigate={navigateTo} />
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
    const backToPengajuan = () => { setActivePage('pengajuan'); window.history.replaceState({}, '', '/'); };
    const backToVendor = () => { setActivePage('vendor'); window.history.replaceState({}, '', '/'); };
    const printPages = {
      'pembukaan-penawaran': <PrintPembukaanPenawaran tenderId={printDeepLink.tenderId} onBack={backToTender} />,
      'aanwijzing': <PrintAanwijzing tenderId={printDeepLink.tenderId} onBack={backToTender} />,
      'pakta-integritas': <PrintPaktaIntegritas tenderId={printDeepLink.tenderId} vendorId={printDeepLink.vendorId} onBack={backToTender} />,
      'sppbj': <PrintSppbj tenderId={printDeepLink.tenderId} onBack={backToTender} />,
      'kontrak': <PrintKontrak tenderId={printDeepLink.tenderId} onBack={backToTender} />,
      'evaluasi-kualifikasi': <PrintEvaluasiKualifikasi tenderId={printDeepLink.tenderId} category={printDeepLink.vendorId} onBack={backToTender} />,
      'evaluasi-rekapitulasi': <PrintEvaluasiRekapitulasi tenderId={printDeepLink.tenderId} onBack={backToTender} />,
      'pengajuan': <PrintPengajuan pengajuanId={printDeepLink.tenderId} onBack={backToPengajuan} />,
      'skt': <PrintSkt vendorId={printDeepLink.tenderId} onBack={backToVendor} />,
      'jadwal': <PrintJadwal tenderId={printDeepLink.tenderId} onBack={backToTender} />,
      'rekam-jejak': <PrintRekamJejak tenderId={printDeepLink.tenderId} onBack={backToTender} />,
      'pernyataan-minat': <PrintPernyataanMinat tenderId={printDeepLink.tenderId} vendorId={printDeepLink.vendorId} onBack={backToTender} />,
      'klarifikasi': <PrintKlarifikasi tenderId={printDeepLink.tenderId} onBack={backToTender} />,
      'negosiasi': <PrintNegosiasi tenderId={printDeepLink.tenderId} vendorId={printDeepLink.vendorId} onBack={backToTender} />,
      'spmk': <PrintSpmk tenderId={printDeepLink.tenderId} onBack={backToTender} />,
      'sppjb': <PrintSppjb tenderId={printDeepLink.tenderId} onBack={backToTender} />,
      'surat-pesanan': <PrintSuratPesanan tenderId={printDeepLink.tenderId} spId={printDeepLink.vendorId} onBack={backToTender} />,
      'daftar-peserta': <PrintDaftarPeserta tenderId={printDeepLink.tenderId} onBack={backToTender} />,
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
    integration_oracle: <Integration />,
    executive_dashboard: <ExecutiveDashboard />,
    oracle_supplier_setup: <OracleSupplierSetup />,
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
      <ToastContainer />
    </AppProvider>
  );
}
