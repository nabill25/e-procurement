import { useState, useEffect } from 'react';
import {
  LayoutDashboard, FileText, Briefcase, Building2, ShieldCheck,
  Settings, LogOut, ChevronRight, Sparkles, AlertTriangle, Globe, Database, Lock, Inbox, Newspaper, Repeat, Users2, History, KeyRound, X, RefreshCw, LayoutGrid
} from 'lucide-react';
import { useApp, API_BASE, getAuthHeaders } from '../../context/AppContext';
import { navItems } from '../../data/mockData';
import clsx from 'clsx';

const iconMap = { LayoutDashboard, FileText, Briefcase, Building2, ShieldCheck, AlertTriangle, Sparkles, Database, Lock, Inbox, Newspaper, Users2, History, KeyRound, RefreshCw, LayoutGrid };

// Aturan menu bawaan (dipakai kalau data hak akses menu dari server belum bisa diambil,
// misalnya saat database sedang tidak bisa dihubungi). Ini jaga-jaga supaya navigasi
// TIDAK PERNAH kosong/rusak hanya karena satu request API gagal.
function getDefaultAllowedMenus(role) {
  // "vendor_profile" (Profil & Kualifikasi Vendor) sengaja dikecualikan dari Admin:
  // halaman itu untuk vendor kelola profil perusahaan sendiri, bukan untuk Admin.
  if (role === 'admin') return navItems.map(item => item.id).filter(id => id !== 'vendor_profile');
  if (role === 'ppk') return ['dashboard', 'pengajuan', 'tender', 'katalog', 'purchasing', 'executive_dashboard'];
  if (role === 'pokja') return ['dashboard', 'tender', 'vendor', 'blacklist'];
  if (role === 'vendor') return ['dashboard', 'tender', 'blacklist', 'vendor_profile', 'katalog', 'purchasing'];
  // 10 role tambahan (fondasi multi-role) - sebagian sudah dipetakan ke modul yang cocok
  // (dicek langsung ke data menu sistem lama, lihat migrations/032), sisanya masih cuma
  // Dashboard sampai fitur khususnya dibangun.
  if (role === 'pengguna') return ['dashboard', 'pengajuan'];
  if (role === 'manager_pengadaan') return ['dashboard', 'executive_dashboard'];
  if (role === 'pelaksana_pengadaan') return ['dashboard', 'katalog', 'purchasing'];
  if (role === 'pengelola_kontrak') return ['dashboard', 'tender'];
  if (role === 'kasubdit_kontrak') return ['dashboard', 'tender'];
  if (role === 'approval_vms') return ['dashboard', 'vendor'];
  if (role === 'audit') return ['dashboard', 'audit'];
  if (role === 'admin_vms') return ['dashboard', 'blacklist', 'vendor', 'inbox'];
  if (role === 'administrator_approval') return ['dashboard', 'user_management'];
  if (role === 'perencanaan') return ['dashboard', 'pengajuan'];
  return ['dashboard'];
}

function UILogo() {
  return (
    <div className="flex items-center gap-3 px-1">
      {/* Stylized UI octagon logo mark */}
      <div className="relative w-10 h-10 flex-shrink-0">
        <div className="gold-gradient w-10 h-10 rounded-xl flex items-center justify-center shadow-glow">
          <span className="text-dpbj-navy-dark font-black text-lg leading-none select-none">UI</span>
        </div>
      </div>
      <div>
        <p className="text-white font-bold text-sm leading-tight">DPBJ UI</p>
        <p className="text-slate-400 text-[10px] leading-tight">E-Procurement</p>
      </div>
    </div>
  );
}

export default function Sidebar() {
  const { activePage, setActivePage, user, logout, openSettingsModal, availableRoles, setShowRoleSwitcher, isSidebarOpen, setIsSidebarOpen } = useApp();
  const [allowedMenus, setAllowedMenus] = useState(() => getDefaultAllowedMenus(user?.role));

  // Di layar mobile, sidebar adalah drawer overlay: pilih menu atau klik luar area = tutup.
  const navigateAndClose = (id) => {
    setActivePage(id);
    setIsSidebarOpen(false);
  };

  // BUG FIX: sebelumnya scroll body/halaman utama tidak pernah dikunci saat drawer mobile
  // ini terbuka, jadi scroll di layar utama dan scroll di dalam drawer "berebutan" (drawer
  // jadi susah/tidak bisa discroll sendiri). Pola ini sudah dipakai di 3 modal lain
  // (LoginModal, VendorPolicyModal, RescheduleHistoryModal), sekarang disamakan di sini.
  useEffect(() => {
    if (isSidebarOpen) {
      document.body.style.overflow = 'hidden';
    } else {
      document.body.style.overflow = '';
    }
    return () => { document.body.style.overflow = ''; };
  }, [isSidebarOpen]);

  useEffect(() => {
    let cancelled = false;
    // Mulai dari aturan bawaan dulu (supaya sidebar langsung terisi, tidak nunggu network)
    setAllowedMenus(getDefaultAllowedMenus(user?.role));

    if (!user?.role) return;

    fetch(`${API_BASE}/menu/${user.role}`, { headers: getAuthHeaders() })
      .then(res => res.json())
      .then(json => {
        // Kalau server kasih data hak akses menu, PAKAI itu. Kalau gagal/kosong, tetap
        // pakai aturan bawaan di atas (sudah ke-set duluan), jadi sidebar tidak pernah blank.
        if (!cancelled && json.success && json.data.length > 0) {
          setAllowedMenus(json.data.map(m => m.menu_key));
        }
      })
      .catch(() => { /* biarkan pakai aturan bawaan */ });

    return () => { cancelled = true; };
  }, [user?.role]);

  return (
    <>
      {/* Overlay gelap di belakang drawer, cuma tampil di mobile saat sidebar terbuka */}
      {isSidebarOpen && (
        <div
          className="fixed inset-0 bg-black/50 z-40 lg:hidden animate-fade-in touch-none"
          onClick={() => setIsSidebarOpen(false)}
        />
      )}

      <aside
        className={clsx(
          'sidebar-bg w-64 flex-shrink-0 flex flex-col h-screen border-r border-white/5',
          'fixed top-0 left-0 z-50 transition-transform duration-300 ease-out',
          'lg:sticky lg:translate-x-0',
          isSidebarOpen ? 'translate-x-0' : '-translate-x-full'
        )}
      >
        {/* Header */}
        <div className="px-5 py-5 border-b border-white/10 flex items-center justify-between">
          <UILogo />
          <button
            className="lg:hidden p-1.5 text-slate-400 hover:text-white hover:bg-white/10 rounded-lg transition-colors"
            onClick={() => setIsSidebarOpen(false)}
            aria-label="Tutup menu"
          >
            <X size={18} />
          </button>
        </div>

        {/* Navigation */}
        <nav className="flex-1 px-3 py-4 space-y-0.5 overflow-y-auto overscroll-contain">
          <p className="px-3 mb-2 text-[10px] font-bold text-slate-500 uppercase tracking-widest animate-fade-in">Menu Utama</p>
          {navItems
            .filter(item => allowedMenus.includes(item.id))
            .map(({ id, label, icon }, index) => {
              const Icon = iconMap[icon];
              const isActive = activePage === id;
              return (
                <button
                  key={id}
                  id={`nav-${id}`}
                  onClick={() => navigateAndClose(id)}
                  className={clsx(`sidebar-item w-full text-left animate-slide-in-right stagger-${index + 1}`, { active: isActive })}
                >
                  {Icon && <Icon size={17} className={clsx('shrink-0', isActive ? 'text-dpbj-navy-dark' : 'text-slate-400')} />}
                  <span className="truncate">{label}</span>
                  {isActive && <ChevronRight size={14} className="ml-auto opacity-60 shrink-0" />}
                </button>
              );
          })}

          <div className="mt-5 pt-5 border-t border-white/10 animate-fade-in-up stagger-5">
            <p className="px-3 mb-2 text-[10px] font-bold text-slate-500 uppercase tracking-widest">Sistem</p>
            {user?.role === 'admin' && (
              <button className="sidebar-item w-full text-left text-dpbj-gold/80 hover:text-dpbj-gold" onClick={() => navigateAndClose('public_home')}>
                <Globe size={17} />
                <span>Portal Publik</span>
              </button>
            )}
            {availableRoles.length > 1 && (
              <button className="sidebar-item w-full text-left" onClick={() => { setShowRoleSwitcher(true); setIsSidebarOpen(false); }}>
                <Repeat size={17} className="text-slate-400" />
                <span>Ganti Role</span>
              </button>
            )}
            <button className="sidebar-item w-full text-left" onClick={() => { openSettingsModal(); setIsSidebarOpen(false); }}>
              <Settings size={17} className="text-slate-400" />
              <span>Pengaturan</span>
            </button>
            <button className="sidebar-item w-full text-left text-red-400 hover:text-red-300" onClick={() => {
              if (confirm('Apakah Anda yakin ingin keluar?')) logout();
            }}>
              <LogOut size={17} />
              <span>Keluar</span>
            </button>
          </div>
        </nav>

        {/* User profile at bottom */}
        <div className="px-3 py-4 border-t border-white/10">
          <div className="flex items-center gap-3 px-3 py-2.5 rounded-xl hover:bg-white/5 transition-colors cursor-pointer">
            <div className="w-8 h-8 rounded-full gold-gradient flex items-center justify-center flex-shrink-0">
              <span className="text-dpbj-navy-dark font-bold text-xs">
                {user?.name?.split(' ').map(w => w[0]).slice(0, 2).join('') || 'U'}
              </span>
            </div>
            <div className="overflow-hidden">
              <p className="text-white text-xs font-semibold truncate">{user?.name || 'Pengguna'}</p>
              <p className="text-slate-400 text-[10px] truncate">{user?.roleLabel || user?.role || ''}</p>
            </div>
          </div>

          {/* Version badge */}
          <div className="mt-3 flex items-center gap-1.5 px-3">
            <Sparkles size={10} className="text-dpbj-gold" />
            <span className="text-[10px] text-slate-600">v1.0.0 · TA 2025</span>
          </div>
        </div>
      </aside>
    </>
  );
}
