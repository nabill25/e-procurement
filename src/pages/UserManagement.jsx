import { useState, useEffect, useCallback } from 'react';
import { Users2, Plus, Trash2, ShieldPlus, MoveHorizontal } from 'lucide-react';
import { getAuthHeaders, API_BASE, useApp } from '../context/AppContext';

export default function UserManagement() {
  const [users, setUsers] = useState([]);
  const [roleOptions, setRoleOptions] = useState([]);
  const [isLoading, setIsLoading] = useState(true);
  const [showAddForm, setShowAddForm] = useState(false);
  const [form, setForm] = useState({ username: '', password: '', full_name: '', email: '', role_key: '' });
  const [saving, setSaving] = useState(false);
  const [addingRoleFor, setAddingRoleFor] = useState(null);
  const [newRoleKey, setNewRoleKey] = useState('');

  const fetchUsers = useCallback(async () => {
    setIsLoading(true);
    try {
      const res = await fetch(`${API_BASE}/users`, { headers: getAuthHeaders() });
      const json = await res.json();
      if (json.success) setUsers(json.data);
    } catch (err) {
      console.error(err);
    } finally {
      setIsLoading(false);
    }
  }, []);

  const fetchRoleOptions = useCallback(async () => {
    try {
      const res = await fetch(`${API_BASE}/users/roles`, { headers: getAuthHeaders() });
      const json = await res.json();
      if (json.success) setRoleOptions(json.data);
    } catch (err) {
      console.error(err);
    }
  }, []);

  useEffect(() => { fetchUsers(); fetchRoleOptions(); }, [fetchUsers, fetchRoleOptions]);

  const handleCreate = async (e) => {
    e.preventDefault();
    if (!form.username || !form.password || !form.full_name || !form.role_key) return;
    setSaving(true);
    try {
      const res = await fetch(`${API_BASE}/users`, {
        method: 'POST', headers: getAuthHeaders(), body: JSON.stringify(form),
      });
      const json = await res.json();
      if (json.success) {
        setForm({ username: '', password: '', full_name: '', email: '', role_key: '' });
        setShowAddForm(false);
        fetchUsers();
      } else {
        alert('Gagal: ' + json.message);
      }
    } catch {
      alert('Terjadi kesalahan saat membuat akun.');
    } finally {
      setSaving(false);
    }
  };

  const handleAddRole = async (userId) => {
    if (!newRoleKey) return;
    try {
      const res = await fetch(`${API_BASE}/users/${userId}/roles`, {
        method: 'POST', headers: getAuthHeaders(), body: JSON.stringify({ role_key: newRoleKey }),
      });
      const json = await res.json();
      if (json.success) { setAddingRoleFor(null); setNewRoleKey(''); fetchUsers(); }
      else alert('Gagal: ' + json.message);
    } catch {
      alert('Terjadi kesalahan saat menambah role.');
    }
  };

  const handleRemoveRole = async (userId, roleKey) => {
    if (!confirm('Cabut role ini dari akun?')) return;
    try {
      const res = await fetch(`${API_BASE}/users/${userId}/roles/${roleKey}`, {
        method: 'DELETE', headers: getAuthHeaders(),
      });
      const json = await res.json();
      if (json.success) fetchUsers();
      else alert('Gagal: ' + json.message);
    } catch {
      alert('Terjadi kesalahan saat mencabut role.');
    }
  };

  return (
    <div className="space-y-6 animate-fade-in">
      <div className="section-card">
        <div className="flex items-center justify-between mb-5">
          <div className="flex items-center gap-3">
            <div className="w-10 h-10 rounded-xl bg-surface flex items-center justify-center">
              <Users2 size={20} className="text-dpbj-navy" />
            </div>
            <div>
              <h2 className="text-base font-bold text-dpbj-navy">Manajemen User Internal</h2>
              <p className="text-xs text-muted">Kelola akun staff (bukan vendor) dan role yang dimiliki tiap akun</p>
            </div>
          </div>
          <button onClick={() => setShowAddForm(!showAddForm)} className="btn-primary flex items-center gap-2">
            <Plus size={16} /> Akun Baru
          </button>
        </div>

        {showAddForm && (
          <form onSubmit={handleCreate} className="grid grid-cols-2 gap-3 bg-surface p-4 rounded-xl border border-border mb-5">
            <input placeholder="Username / Email login" value={form.username} onChange={e => setForm({ ...form, username: e.target.value })} required className="text-sm p-2 border border-gray-300 rounded-lg" />
            <input type="password" placeholder="Password" value={form.password} onChange={e => setForm({ ...form, password: e.target.value })} required className="text-sm p-2 border border-gray-300 rounded-lg" />
            <input placeholder="Nama Lengkap" value={form.full_name} onChange={e => setForm({ ...form, full_name: e.target.value })} required className="text-sm p-2 border border-gray-300 rounded-lg" />
            <input placeholder="Email (opsional)" value={form.email} onChange={e => setForm({ ...form, email: e.target.value })} className="text-sm p-2 border border-gray-300 rounded-lg" />
            <select value={form.role_key} onChange={e => setForm({ ...form, role_key: e.target.value })} required className="text-sm p-2 border border-gray-300 rounded-lg col-span-2">
              <option value="">Pilih Role Utama...</option>
              {roleOptions.map(r => <option key={r.role_key} value={r.role_key}>{r.label}</option>)}
            </select>
            <button type="submit" disabled={saving} className="btn-secondary col-span-2 disabled:opacity-50">
              {saving ? 'Menyimpan...' : 'Buat Akun'}
            </button>
          </form>
        )}

        <p className="table-scroll-hint">
          <MoveHorizontal size={13} /> Geser tabel ke kiri/kanan untuk lihat kolom lainnya
        </p>
        <div className="table-scroll">
          <table className="data-table">
            <thead>
              <tr>
                <th>Nama</th>
                <th>Username</th>
                <th>Role Aktif</th>
                <th>Semua Role</th>
                <th className="text-right">Aksi</th>
              </tr>
            </thead>
            <tbody>
              {isLoading ? (
                <tr><td colSpan={5} className="py-10 text-center text-muted text-sm">Memuat data...</td></tr>
              ) : users.length === 0 ? (
                <tr><td colSpan={5} className="py-10 text-center text-muted text-sm">Belum ada akun staff.</td></tr>
              ) : users.map(u => (
                <tr key={u.id}>
                  <td className="text-sm font-medium text-dpbj-navy">{u.full_name}</td>
                  <td className="text-xs text-muted">{u.username}</td>
                  <td><span className="badge text-[10px] bg-blue-100 text-blue-700">{u.role_label || u.active_role}</span></td>
                  <td>
                    <div className="flex flex-wrap gap-1">
                      {u.roles.map(r => (
                        <span key={r.role_key} className="badge text-[10px] bg-surface text-dpbj-navy flex items-center gap-1">
                          {r.label}
                          {u.roles.length > 1 && (
                            <button onClick={() => handleRemoveRole(u.id, r.role_key)} className="text-red-500 hover:text-red-700">
                              <Trash2 size={10} />
                            </button>
                          )}
                        </span>
                      ))}
                    </div>
                    {addingRoleFor === u.id ? (
                      <div className="flex items-center gap-1 mt-2">
                        <select value={newRoleKey} onChange={e => setNewRoleKey(e.target.value)} className="text-[10px] p-1 border border-gray-300 rounded">
                          <option value="">Pilih role...</option>
                          {roleOptions.filter(r => !u.roles.some(ur => ur.role_key === r.role_key)).map(r => (
                            <option key={r.role_key} value={r.role_key}>{r.label}</option>
                          ))}
                        </select>
                        <button onClick={() => handleAddRole(u.id)} className="text-[10px] font-bold text-emerald-600">Simpan</button>
                        <button onClick={() => setAddingRoleFor(null)} className="text-[10px] text-muted">Batal</button>
                      </div>
                    ) : (
                      <button onClick={() => setAddingRoleFor(u.id)} className="text-[10px] text-blue-600 hover:underline flex items-center gap-1 mt-1.5">
                        <ShieldPlus size={11} /> Tambah role
                      </button>
                    )}
                  </td>
                  <td className="text-right text-xs text-muted">{u.status}</td>
                </tr>
              ))}
            </tbody>
          </table>
        </div>
      </div>
    </div>
  );
}
