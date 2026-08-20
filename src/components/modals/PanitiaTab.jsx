import { useState, useEffect } from 'react';
import { Users, Plus, Trash2, Lock, ShieldCheck, ShieldX, Crown } from 'lucide-react';
import { API_BASE } from '../../context/AppContext';
import clsx from 'clsx';

export default function PanitiaTab({ tenderId, user, getAuthHeaders }) {
  const [panitia, setPanitia] = useState([]);
  const [skList, setSkList] = useState([]);
  const [selectedSkId, setSelectedSkId] = useState('');
  const [manualMembers, setManualMembers] = useState([{ nip: '', nama: '', jabatan: '', is_ketua: false }]);
  const [loading, setLoading] = useState(false);
  const [rejectNote, setRejectNote] = useState({});

  const canManage = ['pokja', 'admin', 'ppk'].includes(user.role);
  const isLocked = panitia.length > 0 && panitia[0].locked;

  const fetchPanitia = async () => {
    try {
      const res = await fetch(`${API_BASE}/tenders/${tenderId}/panitia`, { headers: getAuthHeaders() });
      const json = await res.json();
      if (json.success) setPanitia(json.data);
    } catch (err) { console.error(err); }
  };

  const fetchSkList = async () => {
    try {
      const res = await fetch(`${API_BASE}/tenders/master/sk-panitia`, { headers: getAuthHeaders() });
      const json = await res.json();
      if (json.success) setSkList(json.data);
    } catch (err) { console.error(err); }
  };

  useEffect(() => { fetchPanitia(); fetchSkList(); }, [tenderId]);

  const handleImportFromSk = async () => {
    if (!selectedSkId) return alert('Pilih SK Panitia dulu.');
    try {
      const res = await fetch(`${API_BASE}/tenders/master/sk-panitia/${selectedSkId}`, { headers: getAuthHeaders() });
      const json = await res.json();
      if (!json.success || !json.data.members.length) return alert('SK ini tidak punya anggota.');
      await saveMembers(json.data.members.map(m => ({ nip: m.nip, nama: m.nama, jabatan: m.jabatan, is_ketua: m.is_ketua })));
    } catch (err) { alert('Gagal mengambil data SK.'); }
  };

  const saveMembers = async (members) => {
    setLoading(true);
    try {
      const res = await fetch(`${API_BASE}/tenders/${tenderId}/panitia`, {
        method: 'POST', headers: getAuthHeaders(),
        body: JSON.stringify({ members, created_by: user.id }),
      });
      const json = await res.json();
      if (json.success) { fetchPanitia(); } else { alert('Gagal: ' + json.message); }
    } catch { alert('Terjadi kesalahan saat menyimpan panitia.'); } finally { setLoading(false); }
  };

  const handleManualSave = () => {
    const valid = manualMembers.filter(m => m.nama.trim());
    if (!valid.length) return alert('Isi minimal satu nama anggota.');
    saveMembers(valid);
  };

  const handleDelete = async (id) => {
    if (!confirm('Hapus anggota panitia ini?')) return;
    try {
      const res = await fetch(`${API_BASE}/tenders/${tenderId}/panitia/${id}`, { method: 'DELETE', headers: getAuthHeaders() });
      const json = await res.json();
      if (json.success) fetchPanitia(); else alert('Gagal: ' + json.message);
    } catch { alert('Terjadi kesalahan saat menghapus.'); }
  };

  const handleLock = async () => {
    if (!confirm('Kunci tim panitia? Setelah dikunci, susunan panitia tidak bisa diubah lagi.')) return;
    try {
      const res = await fetch(`${API_BASE}/tenders/${tenderId}/panitia/lock`, { method: 'PATCH', headers: getAuthHeaders() });
      const json = await res.json();
      if (json.success) fetchPanitia(); else alert('Gagal: ' + json.message);
    } catch { alert('Terjadi kesalahan saat mengunci tim.'); }
  };

  const handleValidasiPemenang = async (id, validasi) => {
    const catatan = rejectNote[id] || '';
    if (validasi === 'tolak' && !catatan.trim()) return alert('Catatan wajib diisi jika menolak.');
    try {
      const res = await fetch(`${API_BASE}/tenders/${tenderId}/panitia/${id}/validasi-pemenang`, {
        method: 'PATCH', headers: getAuthHeaders(),
        body: JSON.stringify({ validasi, catatan }),
      });
      const json = await res.json();
      if (json.success) { fetchPanitia(); } else { alert('Gagal: ' + json.message); }
    } catch { alert('Terjadi kesalahan saat validasi.'); }
  };

  return (
    <div className="space-y-6 animate-fade-in">
      <div className="flex items-center gap-3">
        <div className="w-10 h-10 rounded-full bg-indigo-100 text-indigo-600 flex items-center justify-center shrink-0">
          <Users size={20} />
        </div>
        <div>
          <h3 className="font-bold text-dpbj-navy text-sm">Panitia / Pokja Paket</h3>
          <p className="text-xs text-muted">Penugasan anggota panitia untuk tender ini, termasuk validasi pemenang.</p>
        </div>
      </div>

      {canManage && !isLocked && (
        <div className="bg-surface border border-border rounded-xl p-4 space-y-4">
          <div>
            <p className="text-xs font-semibold text-muted uppercase tracking-wide mb-2">Ambil dari SK Panitia</p>
            <div className="flex items-center gap-2">
              <select value={selectedSkId} onChange={e => setSelectedSkId(e.target.value)} className="flex-1 text-sm p-2 border border-gray-300 rounded-lg">
                <option value="">Pilih SK Panitia...</option>
                {skList.map(sk => <option key={sk.id} value={sk.id}>{sk.unit_kerja} - {sk.nomor_sk || 'tanpa nomor'}</option>)}
              </select>
              <button onClick={handleImportFromSk} disabled={loading} className="btn-secondary text-xs whitespace-nowrap disabled:opacity-50">Terapkan</button>
            </div>
          </div>

          <div className="border-t border-border pt-4">
            <p className="text-xs font-semibold text-muted uppercase tracking-wide mb-2">Atau Input Manual</p>
            <div className="space-y-2">
              {manualMembers.map((m, i) => (
                <div key={i} className="flex items-center gap-2">
                  <input placeholder="NIP" value={m.nip} onChange={e => setManualMembers(manualMembers.map((x, xi) => xi === i ? { ...x, nip: e.target.value } : x))} className="w-28 text-xs p-1.5 border border-gray-300 rounded-lg" />
                  <input placeholder="Nama" value={m.nama} onChange={e => setManualMembers(manualMembers.map((x, xi) => xi === i ? { ...x, nama: e.target.value } : x))} className="flex-1 text-xs p-1.5 border border-gray-300 rounded-lg" />
                  <input placeholder="Jabatan" value={m.jabatan} onChange={e => setManualMembers(manualMembers.map((x, xi) => xi === i ? { ...x, jabatan: e.target.value } : x))} className="w-32 text-xs p-1.5 border border-gray-300 rounded-lg" />
                  <label className="flex items-center gap-1 text-[10px] text-dpbj-navy whitespace-nowrap">
                    <input type="checkbox" checked={m.is_ketua} onChange={e => setManualMembers(manualMembers.map((x, xi) => xi === i ? { ...x, is_ketua: e.target.checked } : x))} /> Ketua
                  </label>
                </div>
              ))}
              <div className="flex items-center gap-2">
                <button onClick={() => setManualMembers([...manualMembers, { nip: '', nama: '', jabatan: '', is_ketua: false }])} className="text-xs text-dpbj-navy font-semibold flex items-center gap-1">
                  <Plus size={12} /> Tambah baris
                </button>
                <button onClick={handleManualSave} disabled={loading} className="btn-primary text-xs ml-auto disabled:opacity-50">Simpan Panitia</button>
              </div>
            </div>
          </div>
        </div>
      )}

      <div className="bg-white border border-border rounded-xl overflow-hidden">
        <div className="flex items-center justify-between p-3 bg-surface border-b border-border">
          <p className="text-xs font-semibold text-dpbj-navy">Daftar Panitia ({panitia.length})</p>
          {isLocked ? (
            <span className="inline-flex items-center gap-1 text-[10px] font-bold text-red-600 bg-red-50 px-2 py-1 rounded-full"><Lock size={10} /> TERKUNCI</span>
          ) : canManage && panitia.length > 0 ? (
            <button onClick={handleLock} className="inline-flex items-center gap-1 text-[10px] font-bold text-white bg-dpbj-navy px-2.5 py-1 rounded-full hover:bg-blue-900">
              <Lock size={10} /> Kunci Tim
            </button>
          ) : null}
        </div>
        {panitia.length === 0 ? (
          <p className="text-sm text-muted text-center py-8">Belum ada panitia yang ditugaskan.</p>
        ) : (
          <div className="divide-y divide-border">
            {panitia.map(p => (
              <div key={p.id} className="p-3 flex items-center justify-between gap-3">
                <div className="flex items-center gap-2 min-w-0">
                  {p.is_ketua && <Crown size={14} className="text-amber-500 shrink-0" />}
                  <div className="min-w-0">
                    <p className="text-sm font-semibold text-dpbj-navy truncate">{p.nama}</p>
                    <p className="text-[10px] text-muted">{p.jabatan || '-'} {p.nip ? `• NIP ${p.nip}` : ''}</p>
                  </div>
                </div>
                <div className="flex items-center gap-2 shrink-0">
                  {p.validasi_pemenang && (
                    <span className={clsx('text-[10px] font-bold px-2 py-0.5 rounded-full', p.validasi_pemenang === 'setuju' ? 'bg-emerald-100 text-emerald-700' : 'bg-red-100 text-red-700')}>
                      {p.validasi_pemenang === 'setuju' ? 'SETUJU PEMENANG' : 'MENOLAK PEMENANG'}
                    </span>
                  )}
                  {p.is_ketua && !p.validasi_pemenang && (
                    <div className="flex items-center gap-1">
                      <input
                        placeholder="Catatan (jika tolak)"
                        value={rejectNote[p.id] || ''}
                        onChange={e => setRejectNote({ ...rejectNote, [p.id]: e.target.value })}
                        className="w-32 text-[10px] p-1 border border-gray-300 rounded"
                      />
                      <button onClick={() => handleValidasiPemenang(p.id, 'setuju')} className="p-1 bg-emerald-50 text-emerald-600 hover:bg-emerald-100 rounded" title="Setujui pemenang">
                        <ShieldCheck size={13} />
                      </button>
                      <button onClick={() => handleValidasiPemenang(p.id, 'tolak')} className="p-1 bg-red-50 text-red-600 hover:bg-red-100 rounded" title="Tolak pemenang">
                        <ShieldX size={13} />
                      </button>
                    </div>
                  )}
                  {canManage && !isLocked && (
                    <button onClick={() => handleDelete(p.id)} className="p-1 text-red-400 hover:bg-red-50 rounded"><Trash2 size={13} /></button>
                  )}
                </div>
              </div>
            ))}
          </div>
        )}
      </div>
    </div>
  );
}
