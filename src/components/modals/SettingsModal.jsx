import { X, LogOut, Shield, User, Mail } from 'lucide-react';
import { useApp } from '../../context/AppContext';

export default function SettingsModal({ isOpen, onClose }) {
  const { user, logout } = useApp();

  if (!isOpen) return null;

  return (
    <div className="fixed inset-0 z-50 flex items-center justify-center p-4 bg-dpbj-navy/50 backdrop-blur-sm animate-fade-in">
      <div className="bg-white rounded-2xl shadow-xl w-full max-w-md overflow-hidden flex flex-col">
        <div className="flex items-center justify-between p-5 border-b border-border bg-surface">
          <h2 className="text-lg font-bold text-dpbj-navy">Pengaturan Profil</h2>
          <button onClick={onClose} className="p-2 text-muted hover:bg-gray-200 rounded-xl transition-colors">
            <X size={18} />
          </button>
        </div>

        <div className="p-6 space-y-6">
          <div className="flex items-center gap-4">
            <div className="w-16 h-16 rounded-full bg-dpbj-gold text-dpbj-navy flex items-center justify-center font-bold text-xl shadow-inner">
              {user?.name?.split(' ').map(n => n[0]).join('').substring(0, 2) || 'U'}
            </div>
            <div>
              <h3 className="font-bold text-dpbj-navy text-lg">{user?.name || 'Pengguna'}</h3>
              <p className="text-sm text-muted">{user?.roleLabel || user?.role || ''}</p>
            </div>
          </div>

          <div className="space-y-4">
            <div className="flex items-center gap-3 p-3 bg-surface rounded-xl border border-border">
              <User size={18} className="text-muted" />
              <div>
                <p className="text-xs text-muted font-medium">Username</p>
                <p className="text-sm font-semibold text-dpbj-navy">{user?.username || '-'}</p>
              </div>
            </div>

            {user?.email && (
              <div className="flex items-center gap-3 p-3 bg-surface rounded-xl border border-border">
                <Mail size={18} className="text-muted" />
                <div>
                  <p className="text-xs text-muted font-medium">Email</p>
                  <p className="text-sm font-semibold text-dpbj-navy">{user.email}</p>
                </div>
              </div>
            )}

            <div className="flex items-center gap-3 p-3 bg-surface rounded-xl border border-border">
              <Shield size={18} className="text-muted" />
              <div>
                <p className="text-xs text-muted font-medium">Hak Akses</p>
                <p className="text-sm font-semibold text-dpbj-navy">{user?.roleLabel || user?.role_label || user?.role || '-'}</p>
              </div>
            </div>
          </div>
        </div>

        <div className="p-5 border-t border-border bg-surface flex justify-between items-center">
          <button onClick={() => {
            onClose();
            logout();
          }} className="flex items-center gap-2 text-red-600 hover:bg-red-50 px-4 py-2 rounded-xl transition-colors font-semibold text-sm">
            <LogOut size={16} />
            Keluar Aplikasi
          </button>
          <button onClick={onClose} className="btn-secondary">Tutup</button>
        </div>
      </div>
    </div>
  );
}
