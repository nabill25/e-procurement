import { useState, useEffect, useCallback } from 'react';
import { Lock, Save } from 'lucide-react';
import { getAuthHeaders, API_BASE, useApp } from '../context/AppContext';

const ROLES = [
  { id: 'admin',  label: 'Admin' },
  { id: 'ppk',    label: 'PPK' },
  { id: 'pokja',  label: 'Pokja' },
  { id: 'vendor', label: 'Vendor' },
];

export default function MenuAccess() {
  const [menus, setMenus] = useState([]);
  const [isLoading, setIsLoading] = useState(true);
  const [savingId, setSavingId] = useState(null);

  const fetchMatrix = useCallback(async () => {
    setIsLoading(true);
    try {
      const res = await fetch(`${API_BASE}/menu/access-matrix`, { headers: getAuthHeaders() });
      const json = await res.json();
      if (json.success) setMenus(json.data);
    } catch (err) {
      console.error('Failed to fetch menu access matrix:', err);
    } finally {
      setIsLoading(false);
    }
  }, []);

  useEffect(() => { fetchMatrix(); }, [fetchMatrix]);

  const toggleRole = (menuId, role) => {
    setMenus(prev => prev.map(m => {
      if (m.id !== menuId) return m;
      const hasRole = m.roles.includes(role);
      return { ...m, roles: hasRole ? m.roles.filter(r => r !== role) : [...m.roles, role] };
    }));
  };

  const saveMenu = async (menu) => {
    setSavingId(menu.id);
    try {
      const res = await fetch(`${API_BASE}/menu/${menu.id}/access`, {
        method: 'PUT',
        headers: getAuthHeaders(),
        body: JSON.stringify({ roles: menu.roles }),
      });
      const json = await res.json();
      if (!json.success) alert('Gagal: ' + json.message);
    } catch {
      alert('Terjadi kesalahan saat menyimpan hak akses.');
    } finally {
      setSavingId(null);
    }
  };

  return (
    <div className="space-y-6 animate-fade-in">
      <div className="section-card">
        <div className="flex items-center gap-3 mb-2">
          <div className="w-10 h-10 rounded-xl bg-surface flex items-center justify-center">
            <Lock size={20} className="text-dpbj-navy" />
          </div>
          <div>
            <h2 className="text-base font-bold text-dpbj-navy">Hak Akses Menu</h2>
            <p className="text-xs text-muted">Atur menu mana yang bisa dilihat tiap peran pengguna</p>
          </div>
        </div>
        <p className="text-xs text-amber-700 bg-amber-50 border border-amber-200 rounded-lg px-3 py-2 mt-4">
          Perubahan di sini langsung berlaku untuk semua pengguna dengan peran terkait. Pastikan minimal role Admin tetap punya akses ke halaman ini supaya tidak terkunci sendiri.
        </p>

        <div className="table-scroll mt-5">
          <table className="data-table">
            <thead>
              <tr>
                <th>Menu</th>
                {ROLES.map(r => <th key={r.id} className="text-center">{r.label}</th>)}
                <th className="text-right">Aksi</th>
              </tr>
            </thead>
            <tbody>
              {isLoading ? (
                <tr><td colSpan={ROLES.length + 2} className="py-10 text-center text-muted text-sm">Memuat data...</td></tr>
              ) : menus.length === 0 ? (
                <tr><td colSpan={ROLES.length + 2} className="py-10 text-center text-muted text-sm">Belum ada data menu.</td></tr>
              ) : menus.map(menu => (
                <tr key={menu.id}>
                  <td className="text-sm font-medium text-dpbj-navy">{menu.label}</td>
                  {ROLES.map(r => (
                    <td key={r.id} className="text-center">
                      <input
                        type="checkbox"
                        checked={menu.roles.includes(r.id)}
                        onChange={() => toggleRole(menu.id, r.id)}
                        className="w-4 h-4 accent-dpbj-navy cursor-pointer"
                      />
                    </td>
                  ))}
                  <td className="text-right">
                    <button
                      onClick={() => saveMenu(menu)}
                      disabled={savingId === menu.id}
                      className="btn-secondary text-xs flex items-center gap-1.5 ml-auto disabled:opacity-50"
                    >
                      <Save size={13} /> {savingId === menu.id ? 'Menyimpan...' : 'Simpan'}
                    </button>
                  </td>
                </tr>
              ))}
            </tbody>
          </table>
        </div>
      </div>
    </div>
  );
}
