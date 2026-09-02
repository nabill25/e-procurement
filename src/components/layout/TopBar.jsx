import { useState, useRef, useEffect } from 'react';
import { Bell, Search, Plus, ChevronDown, CheckCircle2, AlertCircle, Clock, LogOut, Menu } from 'lucide-react';
import { motion, AnimatePresence } from 'framer-motion';
import { useApp } from '../../context/AppContext';
import LiveClock from '../common/LiveClock';

const dropdownMotion = {
  initial: { opacity: 0, y: -8, scale: 0.97 },
  animate: { opacity: 1, y: 0, scale: 1 },
  exit: { opacity: 0, y: -8, scale: 0.97 },
  transition: { duration: 0.16, ease: [0.16, 1, 0.3, 1] },
};

const pageTitles = {
  dashboard:       { title: 'Dashboard', subtitle: 'Ringkasan aktivitas pengadaan TA 2025' },
  pengajuan:       { title: 'Pengajuan Pengadaan', subtitle: 'Kelola pengajuan dari unit kerja' },
  tender:          { title: 'Paket Pengadaan', subtitle: 'Manajemen tender dan proses pemilihan' },
  vendor:          { title: 'Manajemen Vendor', subtitle: 'Data penyedia barang dan jasa' },
  vendor_profile:  { title: 'Profil & Kualifikasi', subtitle: 'Profil perusahaan dan dokumen kualifikasi' },
  blacklist:       { title: 'Daftar Hitam', subtitle: 'Daftar penyedia yang dikenakan sanksi' },
  audit:           { title: 'Audit & Dokumen', subtitle: 'Log aktivitas dan arsip dokumen' },
  katalog:         { title: 'E-Purchasing', subtitle: 'Katalog produk dan belanja langsung' },
  purchasing:      { title: 'Purchase Orders', subtitle: 'Riwayat dan status pesanan pembelian' },
  master_data:     { title: 'Data Master', subtitle: 'Kelola data referensi sistem' },
  menu_access:     { title: 'Hak Akses Menu', subtitle: 'Atur menu yang tampil per role' },
  inbox:           { title: 'Pusat Pesan', subtitle: 'Pesan masuk dan pengaduan' },
  content_management: { title: 'Kelola Konten', subtitle: 'Berita, FAQ, banner, dan kebijakan publik' },
  user_management: { title: 'Manajemen User', subtitle: 'Kelola akun staff dan role' },
  login_logs:      { title: 'Riwayat Login', subtitle: 'Histori login per akun' },
  api_keys:        { title: 'API Key', subtitle: 'Kelola kunci akses integrasi pihak ketiga' },
  document_expiry: { title: 'Dokumen Kedaluwarsa', subtitle: 'Pemantauan dokumen vendor akan kedaluwarsa' },
  // Kedua ini sebelumnya belum terdaftar di sini sama sekali, jadi walau menu & halamannya
  // sudah ada dan berfungsi, judul di TopBar salah nampilin "Dashboard" begitu dibuka
  // (jatuh ke pageTitles.dashboard sebagai fallback).
  integration_oracle: { title: 'Integrasi Oracle', subtitle: 'Sinkronisasi RKA, Purchase Requisition, dan data Supplier/PO ke Oracle ERP' },
  executive_dashboard: { title: 'Dashboard Pimpinan', subtitle: 'Rekap portofolio dan efisiensi anggaran' },
  oracle_supplier_setup: { title: 'Setup Supplier Oracle', subtitle: 'Alur permintaan setup supplier baru di Oracle EBS' },
  public_home:     { title: 'Portal Publik', subtitle: 'Halaman publik e-Procurement UI' },
  public_tender:   { title: 'Daftar Tender', subtitle: 'Informasi paket tender yang sedang dibuka' },
  public_blacklist:{ title: 'Daftar Hitam Publik', subtitle: 'Informasi sanksi daftar hitam penyedia' },
  public_qr_verify:{ title: 'Cek Dokumen', subtitle: 'Verifikasi keaslian dokumen lewat kode QR' },
  public_policy:   { title: 'Kebijakan', subtitle: 'Kebijakan resmi DPBJ Universitas Indonesia' },
  kontak:          { title: 'Kontak Kami', subtitle: 'Hubungi DPBJ Universitas Indonesia' },
  registrasi:      { title: 'Registrasi Penyedia', subtitle: 'Pendaftaran vendor/penyedia baru' },
};


export default function TopBar() {
  const { activePage, user, setUser, logout, notifications, markAllAsRead, markOneAsRead, openNewProcurementModal, setIsSidebarOpen } = useApp();
  const { title, subtitle } = pageTitles[activePage] || pageTitles.dashboard;
  const [isNotifOpen, setIsNotifOpen] = useState(false);
  const [isRoleOpen, setIsRoleOpen] = useState(false);
  const notifRef = useRef(null);
  const roleRef = useRef(null);

  const unreadCount = notifications.filter(n => !n.read).length;

  useEffect(() => {
    function handleClickOutside(event) {
      if (notifRef.current && !notifRef.current.contains(event.target)) {
        setIsNotifOpen(false);
      }
      if (roleRef.current && !roleRef.current.contains(event.target)) {
        setIsRoleOpen(false);
      }
    }
    document.addEventListener("mousedown", handleClickOutside);
    return () => document.removeEventListener("mousedown", handleClickOutside);
  }, []);

  const toggleNotif = () => {
    setIsNotifOpen(!isNotifOpen);
    setIsRoleOpen(false);
  };
  
  const toggleRole = () => {
    setIsRoleOpen(!isRoleOpen);
    setIsNotifOpen(false);
  };

  const getIcon = (iconName) => {
    switch (iconName) {
      case 'CheckCircle2': return CheckCircle2;
      case 'AlertCircle': return AlertCircle;
      case 'Clock': return Clock;
      default: return Bell;
    }
  };

  return (
    <div className="flex flex-col sticky top-0 z-30">
      {/* LiveClock Top Strip */}
      <div className="bg-dpbj-navy-dark text-slate-300 text-xs py-1.5 px-6 flex justify-between items-center hidden sm:flex">
        <LiveClock />
        <span>Sistem Pengadaan Barang Jasa DPBJ Universitas Indonesia</span>
      </div>
      
      {/* Main TopBar */}
      <header className="bg-white/85 backdrop-blur-md border-b border-border px-4 sm:px-6 py-4 flex items-center gap-3 sm:gap-4">
      {/* Hamburger, cuma tampil di mobile untuk buka drawer sidebar */}
      <button
        className="lg:hidden p-2 -ml-1 rounded-xl hover:bg-surface transition-colors flex-shrink-0"
        onClick={() => setIsSidebarOpen(true)}
        aria-label="Buka menu"
      >
        <Menu size={20} className="text-dpbj-navy" />
      </button>

      {/* Page title */}
      <div className="flex-1 min-w-0">
        <h1 className="text-base sm:text-lg font-bold text-dpbj-navy leading-tight truncate">{title}</h1>
        <p className="text-xs text-muted truncate hidden sm:block">{subtitle}</p>
      </div>

      {/* Search bar */}
      <div className="hidden md:flex items-center gap-2 bg-surface border border-border rounded-xl px-3 py-2 w-64 focus-within:border-dpbj-gold focus-within:ring-2 focus-within:ring-dpbj-gold/20 transition-all">
        <Search size={14} className="text-gray-400 flex-shrink-0" />
        <input
          type="text"
          placeholder="Cari paket, vendor, dokumen..."
          className="bg-transparent text-sm text-dpbj-navy placeholder:text-gray-400 focus:outline-none w-full"
        />
      </div>

      {/* CTA: New Procurement (Only for PPK and Admin) */}
      {(user.role === 'admin' || user.role === 'ppk') && (
        <button
          id="btn-new-procurement"
          onClick={openNewProcurementModal}
          className="btn-primary hidden sm:flex active:scale-95 transition-transform duration-200"
        >
          <Plus size={15} />
          <span>Pengajuan Baru</span>
        </button>
      )}

      {/* Notifications */}
      <div className="relative" ref={notifRef}>
        <button 
          className="relative p-2 rounded-xl hover:bg-surface transition-all focus:outline-none hover:scale-105 active:scale-95" 
          title="Notifikasi"
          onClick={toggleNotif}
        >
          <Bell size={18} className="text-dpbj-navy" />
          {unreadCount > 0 && (
            <span className="absolute -top-0.5 -right-0.5 w-4 h-4 bg-dpbj-gold rounded-full
                             flex items-center justify-center text-[9px] font-black text-dpbj-navy-dark
                             border-2 border-white animate-pulse-soft">
              {unreadCount > 9 ? '9+' : unreadCount}
            </span>
          )}
        </button>

        {/* Dropdown - AnimatePresence supaya beranimasi keluar juga (versi lama pakai
            class "animate-in fade-in slide-in-from-top-2" yang butuh plugin tailwindcss-animate
            yang ternyata tidak terpasang di project ini - class itu diam-diam tidak berefek
            apapun, dropdown muncul/hilang tanpa animasi sama sekali). */}
        <AnimatePresence>
          {isNotifOpen && (
            <motion.div
              {...dropdownMotion}
              className="absolute right-0 mt-2 w-80 bg-white rounded-2xl shadow-xl border border-border overflow-hidden z-50 origin-top-right"
            >
              <div className="p-4 border-b border-border flex justify-between items-center bg-surface/50">
                <h3 className="font-bold text-dpbj-navy text-sm">Notifikasi</h3>
                {unreadCount > 0 && (
                  <button
                    onClick={markAllAsRead}
                    className="text-xs text-dpbj-gold hover:text-dpbj-navy transition-colors font-medium"
                  >
                    Tandai semua dibaca
                  </button>
                )}
              </div>
              <div className="max-h-80 overflow-y-auto">
                {notifications.length === 0 ? (
                  <div className="p-8 text-center text-gray-400 text-sm">
                    Belum ada notifikasi
                  </div>
                ) : (
                  notifications.map(notif => {
                    const IconComp = getIcon(notif.iconName);
                    return (
                      <button
                        key={notif.id}
                        onClick={() => markOneAsRead(notif.id)}
                        className={`w-full text-left p-4 border-b border-border/50 hover:bg-surface/50 transition-colors flex gap-3 ${!notif.read ? 'bg-blue-50/30' : ''}`}
                      >
                        <div className={`mt-1 flex-shrink-0 ${notif.color}`}>
                          <IconComp size={16} />
                        </div>
                        <div className="min-w-0">
                          <h4 className="text-sm font-semibold text-dpbj-navy">{notif.title}</h4>
                          <p className="text-xs text-muted mt-1 leading-relaxed">{notif.desc}</p>
                          <p className="text-[10px] text-gray-400 mt-2">{notif.time}</p>
                        </div>
                        {!notif.read && <span className="w-2 h-2 rounded-full bg-dpbj-gold shrink-0 mt-1.5" />}
                      </button>
                    );
                  })
                )}
              </div>
            </motion.div>
          )}
        </AnimatePresence>
      </div>

      {/* User Menu */}
      <div className="relative hidden lg:flex items-center gap-2 border-l border-border pl-4" ref={roleRef}>
        <div className="text-right cursor-pointer group" onClick={toggleRole}>
          <p className="text-xs font-semibold text-dpbj-navy leading-tight group-hover:text-dpbj-gold transition-colors">{user?.roleLabel || user?.role}</p>
          <p className="text-[10px] text-muted font-medium leading-tight flex items-center justify-end gap-1">
            {user?.unit || 'DPBJ UI'}
            <ChevronDown size={10} className={`transition-transform duration-200 ${isRoleOpen ? 'rotate-180' : ''}`} />
          </p>
        </div>
        <div className="w-8 h-8 bg-surface rounded-xl flex items-center justify-center border border-border">
          <span className="text-dpbj-navy font-bold text-xs">
            {user?.name?.split(' ').map(w => w[0]).slice(0, 2).join('') || 'U'}
          </span>
        </div>

        <AnimatePresence>
          {isRoleOpen && (
            <motion.div
              {...dropdownMotion}
              className="absolute right-0 top-full mt-2 w-64 bg-white rounded-2xl shadow-xl border border-border overflow-hidden z-50 origin-top-right"
            >
              <div className="px-4 py-3 border-b border-border bg-surface">
                <p className="text-xs font-bold text-dpbj-navy">{user?.name}</p>
                <p className="text-[10px] text-muted mt-0.5">{user?.roleLabel}</p>
                {user?.email && <p className="text-[10px] text-dpbj-gold mt-0.5 truncate">{user?.email}</p>}
              </div>
              {/* Logout */}
              <div className="border-t border-border">
                <button
                  onClick={() => { setIsRoleOpen(false); logout(); }}
                  className="w-full text-left px-4 py-3 text-sm font-semibold text-red-500 hover:bg-red-50 transition-colors flex items-center gap-2"
                >
                  <LogOut size={14} />
                  Logout
                </button>
              </div>
            </motion.div>
          )}
        </AnimatePresence>
      </div>
    </header>
  </div>
  );
}
