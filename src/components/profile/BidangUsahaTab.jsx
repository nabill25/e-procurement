import { useState, useEffect, useCallback } from 'react';
import { Search, Plus, Trash2, Briefcase } from 'lucide-react';
import { API_BASE } from '../../context/AppContext';

// Tab Bidang Usaha di Profil Vendor - vendor cari & pilih klasifikasi bidang usaha (KBLI/SBU)
// miliknya sendiri dari daftar 2794 kode resmi yang sudah diimpor dari sistem lama.
export default function BidangUsahaTab({ vendorId, getAuthHeaders }) {
  const [selected, setSelected] = useState([]);
  const [search, setSearch] = useState('');
  const [results, setResults] = useState([]);
  const [loading, setLoading] = useState(false);

  const fetchSelected = useCallback(async () => {
    try {
      const res = await fetch(`${API_BASE}/vendors/${vendorId}/bidang-usaha`, { headers: getAuthHeaders() });
      const json = await res.json();
      if (json.success) setSelected(json.data);
    } catch (err) { console.error(err); }
  }, [vendorId]);

  useEffect(() => { fetchSelected(); }, [fetchSelected]);

  useEffect(() => {
    if (search.trim().length < 3) { setResults([]); return; }
    const timeout = setTimeout(async () => {
      try {
        const res = await fetch(`${API_BASE}/vendors/bidang-usaha/tree?search=${encodeURIComponent(search)}`, { headers: getAuthHeaders() });
        const json = await res.json();
        if (json.success) setResults(json.data.filter(b => b.parent_id).slice(0, 30));
      } catch (err) { console.error(err); }
    }, 400);
    return () => clearTimeout(timeout);
  }, [search]);

  const handleAdd = async (bidangUsahaId) => {
    setLoading(true);
    try {
      const res = await fetch(`${API_BASE}/vendors/${vendorId}/bidang-usaha`, {
        method: 'POST', headers: getAuthHeaders(),
        body: JSON.stringify({ bidang_usaha_id: bidangUsahaId }),
      });
      const json = await res.json();
      if (json.success) { setSearch(''); setResults([]); fetchSelected(); } else alert('Gagal: ' + json.message);
    } catch { alert('Terjadi kesalahan saat menambah bidang usaha.'); } finally { setLoading(false); }
  };

  const handleRemove = async (linkId) => {
    if (!confirm('Hapus bidang usaha ini dari profil Anda?')) return;
    try {
      const res = await fetch(`${API_BASE}/vendors/${vendorId}/bidang-usaha/${linkId}`, { method: 'DELETE', headers: getAuthHeaders() });
      const json = await res.json();
      if (json.success) fetchSelected(); else alert('Gagal: ' + json.message);
    } catch { alert('Terjadi kesalahan saat menghapus.'); }
  };

  return (
    <div className="space-y-5 animate-fade-in">
      <div className="flex items-start gap-3">
        <div className="w-10 h-10 rounded-full bg-indigo-100 text-indigo-600 flex items-center justify-center shrink-0">
          <Briefcase size={18} />
        </div>
        <div>
          <h3 className="font-bold text-dpbj-navy text-sm">Bidang Usaha (KBLI / SBU)</h3>
          <p className="text-xs text-muted">Klasifikasi bidang usaha resmi perusahaan Anda, dipakai sebagai syarat kualifikasi ikut tender tertentu.</p>
        </div>
      </div>

      <div className="relative">
        <Search size={14} className="absolute left-3 top-1/2 -translate-y-1/2 text-muted" />
        <input
          value={search}
          onChange={e => setSearch(e.target.value)}
          placeholder="Cari nama atau kode bidang usaha (minimal 3 huruf)..."
          className="w-full text-sm pl-9 pr-3 py-2.5 border border-gray-300 rounded-xl"
        />
        {results.length > 0 && (
          <div className="absolute z-10 mt-1 w-full max-h-72 overflow-y-auto bg-white border border-border rounded-xl shadow-lg">
            {results.map(r => (
              <button
                key={r.id}
                onClick={() => handleAdd(r.id)}
                disabled={loading}
                className="w-full text-left px-4 py-2.5 text-xs hover:bg-surface border-b border-border last:border-0 flex items-center justify-between gap-2"
              >
                <span><span className="font-mono text-muted">{r.kode}</span> - {r.nama}</span>
                <Plus size={13} className="text-dpbj-navy shrink-0" />
              </button>
            ))}
          </div>
        )}
      </div>

      <div className="bg-white border border-border rounded-xl overflow-hidden">
        <div className="p-3 bg-surface border-b border-border">
          <p className="text-xs font-semibold text-dpbj-navy">Bidang Usaha Terdaftar ({selected.length})</p>
        </div>
        {selected.length === 0 ? (
          <p className="text-sm text-muted text-center py-8">Belum ada bidang usaha yang ditambahkan.</p>
        ) : (
          <div className="divide-y divide-border">
            {selected.map(s => (
              <div key={s.id} className="p-3 flex items-center justify-between gap-3">
                <p className="text-sm text-dpbj-navy"><span className="font-mono text-xs text-muted">{s.kode}</span> - {s.nama}</p>
                <button onClick={() => handleRemove(s.id)} className="p-1 text-red-400 hover:bg-red-50 rounded shrink-0"><Trash2 size={13} /></button>
              </div>
            ))}
          </div>
        )}
      </div>
    </div>
  );
}
