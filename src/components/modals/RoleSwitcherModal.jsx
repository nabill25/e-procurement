import { useState } from 'react';
import { X, Repeat, Check } from 'lucide-react';
import { useApp } from '../../context/AppContext';
import { toast } from '../../lib/toast';

export default function RoleSwitcherModal() {
  const { showRoleSwitcher, setShowRoleSwitcher, availableRoles, switchRole, user } = useApp();
  const [switching, setSwitching] = useState(null);

  if (!showRoleSwitcher) return null;

  const handleChoose = async (role_key) => {
    setSwitching(role_key);
    const result = await switchRole(role_key);
    if (!result.success) toast('Gagal: ' + result.message);
    setSwitching(null);
  };

  return (
    <div className="fixed inset-0 z-[70] flex items-center justify-center p-4 bg-dpbj-navy/50 backdrop-blur-sm animate-fade-in">
      <div className="bg-white rounded-2xl shadow-xl w-full max-w-md max-h-[85vh] flex flex-col">
        <div className="flex items-center justify-between p-5 border-b border-border bg-surface shrink-0">
          <div className="flex items-center gap-2">
            <Repeat size={18} className="text-dpbj-navy" />
            <h2 className="text-base font-bold text-dpbj-navy">Pilih Role Aktif</h2>
          </div>
          <button onClick={() => setShowRoleSwitcher(false)} className="p-2 text-muted hover:bg-white rounded-xl transition-colors">
            <X size={18} />
          </button>
        </div>

        <div className="p-5 overflow-y-auto flex-1">
          <p className="text-xs text-muted mb-4">
            Akun Anda terdaftar dengan lebih dari satu role. Pilih role yang ingin dipakai untuk sesi ini.
          </p>
          <div className="space-y-2">
            {availableRoles.map(r => (
              <button
                key={r.role_key}
                onClick={() => handleChoose(r.role_key)}
                disabled={switching !== null}
                className={`w-full flex items-center justify-between gap-3 p-3 rounded-xl border transition-colors text-left disabled:opacity-50 ${
                  user?.role === r.role_key ? 'border-dpbj-gold bg-dpbj-gold-faint' : 'border-border hover:bg-surface'
                }`}
              >
                <div>
                  <p className="font-semibold text-sm text-dpbj-navy">{r.label}</p>
                  {r.level && <p className="text-[10px] text-muted uppercase">{r.level}</p>}
                </div>
                {switching === r.role_key ? (
                  <span className="w-4 h-4 border-2 border-dpbj-navy/30 border-t-dpbj-navy rounded-full animate-spin" />
                ) : user?.role === r.role_key ? (
                  <Check size={16} className="text-dpbj-gold-dark" />
                ) : null}
              </button>
            ))}
          </div>
        </div>
      </div>
    </div>
  );
}
