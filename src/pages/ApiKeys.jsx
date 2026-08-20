import { useState, useEffect, useCallback } from 'react';
import { KeyRound, Plus, Trash2, Power, Copy, Eye, X } from 'lucide-react';
import { API_BASE, useApp } from '../context/AppContext';

function formatTanggal(iso) {
  if (!iso) return '-';
  return new Date(iso).toLocaleString('id-ID', { dateStyle: 'medium', timeStyle: 'short' });
}

export default function ApiKeys() {
  const { getAuthHeaders, user } = useApp();
  const [keys, setKeys] = useState([]);
  const [isLoading, setIsLoading] = useState(true);
  const [showAddForm, setShowAddForm] = useState(false);
  const [clientName, setClientName] = useState('');
  const [saving, setSaving] = useState(false);
  const [viewingRequestsFor, setViewingRequestsFor] = useState(null);
  const [requests, setRequests] = useState([]);

  const fetchKeys = useCallback(async () => {
    setIsLoading(true);
    try {
      const res = await fetch(`${API_BASE}/users/api-keys`, { headers: getAuthHeaders() });
      const json = await res.json();
      if (json.success) setKeys(json.data);
    } catch (err) {
      console.error(err);
    } finally {
      setIsLoading(false);
    }
  }, []);

  useEffect(() => { fetchKeys(); }, [fetchKeys]);

  const handleCreate = async (e) => {
    e.preventDefault();
    if (!clientName.trim()) return;
    setSaving(true);
    try {
      const res = await fetch(`${API_BASE}/users/api-keys`, {
        method: 'POST', headers: getAuthHeaders(),
        body: JSON.stringify({ client_name: clientName, created_by: user?.id }),
      });
      const json = await res.json();
      if (json.success) {
        setClientName('');
        setShowAddForm(false);
        fetchKeys();
      } else {
        alert('Gagal: ' + json.message);
      }
    } catch {
      alert('Terjadi kesalahan saat membuat API key.');
    } finally {
      setSaving(false);
    }
  };

  const handleToggle = async (id) => {
    try {
      const res = await fetch(`${API_BASE}/users/api-keys/${id}/toggle`, { method: 'PATCH', headers: getAuthHeaders() });
      const json = await res.json();
      if (json.success) fetchKeys();
      else alert('Gagal: ' + json.message);
    } catch {
      alert('Terjadi kesalahan saat mengubah status key.');
    }
  };

  const handleDelete = async (id) => {
    if (!confirm('Hapus API key ini? Integrasi yang memakainya akan langsung berhenti bisa dipakai.')) return;
    try {
      const res = await fetch(`${API_BASE}/users/api-keys/${id}`, { method: 'DELETE', headers: getAuthHeaders() });
      const json = await res.json();
      if (json.success) fetchKeys();
      else alert('Gagal: ' + json.message);
    } catch {
      alert('Terjadi kesalahan saat menghapus key.');
    }
  };

  const handleCopy = (key) => {
    navigator.clipboard?.writeText(key);
  };

  const handleViewRequests = async (id) => {
    setViewingRequestsFor(id);
    try {
      const res = await fetch(`${API_BASE}/users/api-keys/${id}/requests`, { headers: getAuthHeaders() });
      const json = await res.json();
      if (json.success) setRequests(json.data);
    } catch (err) {
      console.error(err);
    }
  };

  return (
    <div className="space-y-6 animate-fade-in">
      <div className="section-card">
        <div className="flex items-center justify-between mb-5">
          <div className="flex items-center gap-3">
            <div className="w-10 h-10 rounded-xl bg-surface flex items-center justify-center">
              <KeyRound size={20} className="text-dpbj-navy" />
            </div>
            <div>
              <h2 className="text-base font-bold text-dpbj-navy">API Key Integrasi</h2>
              <p className="text-xs text-muted">Kelola kunci akses untuk sistem pihak ketiga yang mengambil data lewat API (misalnya integrasi RUP/paket)</p>
            </div>
          </div>
          <button onClick={() => setShowAddForm(!showAddForm)} className="btn-primary flex items-center gap-2">
            <Plus size={16} /> Key Baru
          </button>
        </div>

        {showAddForm && (
          <form onSubmit={handleCreate} className="flex items-center gap-3 bg-surface p-4 rounded-xl border border-border mb-5">
            <input
              placeholder="Nama klien/sistem yang akan pakai key ini"
              value={clientName}
              onChange={e => setClientName(e.target.value)}
              required
              className="text-sm p-2 border border-gray-300 rounded-lg flex-1"
            />
            <button type="submit" disabled={saving} className="btn-secondary disabled:opacity-50">
              {saving ? 'Membuat...' : 'Buat Key'}
            </button>
          </form>
        )}

        <div className="overflow-x-auto rounded-xl border border-border">
          <table className="data-table">
            <thead>
              <tr>
                <th>Nama Klien</th>
                <th>API Key</th>
                <th>Status</th>
                <th>Dibuat</th>
                <th className="text-right">Aksi</th>
              </tr>
            </thead>
            <tbody>
              {isLoading ? (
                <tr><td colSpan={5} className="py-10 text-center text-muted text-sm">Memuat data...</td></tr>
              ) : keys.length === 0 ? (
                <tr><td colSpan={5} className="py-10 text-center text-muted text-sm">Belum ada API key.</td></tr>
              ) : keys.map(k => (
                <tr key={k.id}>
                  <td className="text-sm font-medium text-dpbj-navy">{k.client_name}</td>
                  <td>
                    <div className="flex items-center gap-2">
                      <code className="text-[11px] bg-surface px-2 py-1 rounded font-mono">{k.api_key}</code>
                      <button onClick={() => handleCopy(k.api_key)} title="Salin" className="text-muted hover:text-dpbj-navy">
                        <Copy size={13} />
                      </button>
                    </div>
                  </td>
                  <td>
                    <span className={`badge text-[10px] ${k.is_active ? 'bg-emerald-100 text-emerald-700' : 'bg-gray-100 text-gray-500'}`}>
                      {k.is_active ? 'Aktif' : 'Nonaktif'}
                    </span>
                  </td>
                  <td className="text-xs text-muted">{formatTanggal(k.created_at)}</td>
                  <td className="text-right">
                    <div className="flex items-center justify-end gap-2">
                      <button onClick={() => handleViewRequests(k.id)} title="Lihat riwayat pemakaian" className="text-blue-600 hover:text-blue-800">
                        <Eye size={15} />
                      </button>
                      <button onClick={() => handleToggle(k.id)} title={k.is_active ? 'Nonaktifkan' : 'Aktifkan'} className="text-amber-600 hover:text-amber-800">
                        <Power size={15} />
                      </button>
                      <button onClick={() => handleDelete(k.id)} title="Hapus" className="text-red-600 hover:text-red-800">
                        <Trash2 size={15} />
                      </button>
                    </div>
                  </td>
                </tr>
              ))}
            </tbody>
          </table>
        </div>
      </div>

      {viewingRequestsFor && (
        <div className="fixed inset-0 bg-black/40 flex items-center justify-center z-50 p-4" onClick={() => setViewingRequestsFor(null)}>
          <div className="bg-white rounded-xl max-w-2xl w-full max-h-[80vh] overflow-y-auto p-5" onClick={e => e.stopPropagation()}>
            <div className="flex items-center justify-between mb-4">
              <h3 className="font-bold text-dpbj-navy text-sm">Riwayat Pemakaian Key</h3>
              <button onClick={() => setViewingRequestsFor(null)}><X size={18} className="text-muted" /></button>
            </div>
            {requests.length === 0 ? (
              <p className="text-xs text-muted text-center py-8">Key ini belum pernah dipakai memanggil API.</p>
            ) : (
              <div className="space-y-2">
                {requests.map(r => (
                  <div key={r.id} className="text-xs border border-border rounded-lg p-2 flex justify-between">
                    <div>
                      <p className="font-medium text-dpbj-navy">{r.endpoint || '-'}</p>
                      <p className="text-muted">{r.ip_address} · {formatTanggal(r.requested_at)}</p>
                    </div>
                    <span className={`badge text-[10px] h-fit ${r.is_valid_key ? 'bg-emerald-100 text-emerald-700' : 'bg-red-100 text-red-700'}`}>
                      {r.is_valid_key ? 'Berhasil' : 'Ditolak'}
                    </span>
                  </div>
                ))}
              </div>
            )}
          </div>
        </div>
      )}
    </div>
  );
}
