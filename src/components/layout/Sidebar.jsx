import {
  LayoutDashboard, FileText, Briefcase, Building2, ShieldCheck,
  Settings, LogOut, ChevronRight, Sparkles, AlertTriangle, Globe
} from 'lucide-react';
import { useApp } from '../../context/AppContext';
import { navItems } from '../../data/mockData';
import clsx from 'clsx';

const iconMap = { LayoutDashboard, FileText, Briefcase, Building2, ShieldCheck, AlertTriangle, Sparkles };

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
  const { activePage, setActivePage, user, logout, openSettingsModal } = useApp();

  return (
    <aside className="sidebar-bg w-64 flex-shrink-0 flex flex-col h-screen sticky top-0 z-40 border-r border-white/5">
      {/* Header */}
      <div className="px-5 py-5 border-b border-white/10">
        <UILogo />
      </div>

      {/* Navigation */}
      <nav className="flex-1 px-3 py-4 space-y-0.5 overflow-y-auto">
        <p className="px-3 mb-2 text-[10px] font-bold text-slate-500 uppercase tracking-widest animate-fade-in">Menu Utama</p>
        {navItems
          .filter(item => {
            if (user?.role === 'admin') return true;
            if (user?.role === 'ppk') return ['dashboard', 'pengajuan', 'tender', 'katalog', 'purchasing'].includes(item.id);
            if (user?.role === 'pokja') return ['dashboard', 'tender', 'vendor', 'blacklist'].includes(item.id);
            if (user?.role === 'vendor') return ['dashboard', 'tender', 'blacklist', 'vendor_profile', 'katalog', 'purchasing'].includes(item.id);
            return ['dashboard'].includes(item.id); // fallback
          })
          .map(({ id, label, icon }, index) => {
            const Icon = iconMap[icon];
            const isActive = activePage === id;
            return (
              <button
                key={id}
                id={`nav-${id}`}
                onClick={() => setActivePage(id)}
                className={clsx(`sidebar-item w-full text-left animate-slide-in-right stagger-${index + 1}`, { active: isActive })}
              >
                {Icon && <Icon size={17} className={isActive ? 'text-dpbj-navy-dark' : 'text-slate-400'} />}
                <span>{label}</span>
                {isActive && <ChevronRight size={14} className="ml-auto opacity-60" />}
              </button>
            );
        })}

        <div className="mt-5 pt-5 border-t border-white/10 animate-fade-in-up stagger-5">
          <p className="px-3 mb-2 text-[10px] font-bold text-slate-500 uppercase tracking-widest">Sistem</p>
          <button className="sidebar-item w-full text-left text-dpbj-gold/80 hover:text-dpbj-gold" onClick={() => setActivePage('public_home')}>
            <Globe size={17} />
            <span>Portal Publik</span>
          </button>
          <button className="sidebar-item w-full text-left" onClick={openSettingsModal}>
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
  );
}
