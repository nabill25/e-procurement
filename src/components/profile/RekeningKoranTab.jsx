import { useState, useEffect, useCallback } from 'react';
import { Landmark, Upload, Download, Trash2 } from 'lucide-react';
import { API_BASE, SERVER_BASE } from '../../context/AppContext';
import { formatRupiah } from '../ui/shared';

const BULAN_LABEL = ['', 'Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];

// Tab Rekening Koran di Profil Vendor - bukti mutasi rekening bank per bulan, syarat
// kualifikasi keuangan. Beda dari tab "Bank" yang cuma menyimpan nomor rekening saja.
export default function RekeningKoranTab({ vendorId, getAuthHeaders }) {
  const [items, setItems] = useState([]);
  const [form, setForm] = useState({ nomor_rekening: '', nama_bank: '', bulan: '', tahun: new Date().getFullYear(), nilai: '' });
  const [file, setFile] = useState(null);
  const [saving, setSaving] = useState(false);

  const fetchItems = useCallback(async () => {
    try {
      const res = await fetch(`${API_BASE}/vendors/${vendorId}/rekening-koran`, { headers: getAuthHeaders() });
      const json = await res.json();
      if (json.success) setItems(json.data);
    } catch (err) { console.error(err); }
  }, [vendorId]);

  useEffect(() => { fetchItems(); }, [fetchItems]);

  const handleSubmit = async (e) => {
    e.preventDefault();
    if (!form.nomor_rekening || !form.bulan || !form.tahun || !file) {
      return alert('Lengkapi nomor rekening, bulan, tahun, dan file bukti mutasi.');
    }
    setSaving(true);
    const data = new FormData();
    Object.entries(form).forEach(([k, v]) => data.append(k, v));
    data.append('file', file);
    try {
      const res = await fetch(`${API_BASE}/vendors/${vendorId}/rekening-koran`, {
        method: 'POST', headers: { Authorization: `Bearer ${localStorage.getItem('dpbj_token')}` }, body: data,
      });
      const json = await res.json();
      if (json.success) {
        setForm({ nomor_rekening: '', nama_bank: '', bulan: '', tahun: new Date().getFullYear(), nilai: '' });
        setFile(null);
        fetchItems();
      } else alert('Gagal: ' + json.message);
    } catch { alert('Terjadi kesalahan saat mengunggah.'); } finally { setSaving(false); }
  };

  const handleDelete = async (id) => {
    if (!confirm('Hapus data rekening koran ini?')) return;
    try {
      const res = await fetch(`${API_BASE}/vendors/${vendorId}/rekening-koran/${id}`, { method: 'DELETE', headers: getAuthHeaders() });
      const json = await res.json();
      if (json.success) fetchItems(); else alert('Gagal: ' + json.message);
    } catch { alert('Terjadi kesalahan saat menghapus.'); }
  };

  return (
    <div className="space-y-5 animate-fade-in">
      <div className="flex items-start gap-3">
        <div className="w-10 h-10 rounded-full bg-emerald-100 text-emerald-600 flex items-center justify-center shrink-0">
          <Landmark size={18} />
        </div>
        <div>
          <h3 className="font-bold text-dpbj-navy text-sm">Rekening Koran</h3>
          <p className="text-xs text-muted">Bukti mutasi rekening bank per bulan, dipakai sebagai syarat kualifikasi keuangan saat mengikuti tender.</p>
        </div>
      </div>

      <form onSubmit={handleSubmit} className="bg-surface border border-border rounded-xl p-4 space-y-3">
        <div className="grid grid-cols-2 gap-3">
          <input placeholder="Nomor Rekening" value={form.nomor_rekening} onChange={e => setForm({ ...form, nomor_rekening: e.target.value })} className="text-sm p-2 border border-gray-300 rounded-lg" />
          <input placeholder="Nama Bank" value={form.nama_bank} onChange={e => setForm({ ...form, nama_bank: e.target.value })} className="text-sm p-2 border border-gray-300 rounded-lg" />
          <select value={form.bulan} onChange={e => setForm({ ...form, bulan: e.target.value })} className="text-sm p-2 border border-gray-300 rounded-lg">
            <option value="">Pilih Bulan</option>
            {BULAN_LABEL.slice(1).map((b, i) => <option key={i + 1} value={i + 1}>{b}</option>)}
          </select>
          <input type="number" placeholder="Tahun" value={form.tahun} onChange={e => setForm({ ...form, tahun: e.target.value })} className="text-sm p-2 border border-gray-300 rounded-lg" />
          <input type="number" placeholder="Saldo Akhir (Rp)" value={form.nilai} onChange={e => setForm({ ...form, nilai: e.target.value })} className="text-sm p-2 border border-gray-300 rounded-lg col-span-2" />
        </div>
        <div className="flex items-center gap-2">
          <input type="file" onChange={e => setFile(e.target.files[0])} className="text-xs flex-1" />
          <button type="submit" disabled={saving} className="btn-secondary text-xs flex items-center gap-1 disabled:opacity-50">
            <Upload size={12} /> {saving ? 'Mengunggah...' : 'Unggah'}
          </button>
        </div>
      </form>

      <div className="bg-white border border-border rounded-xl overflow-hidden">
        <div className="p-3 bg-surface border-b border-border">
          <p className="text-xs font-semibold text-dpbj-navy">Riwayat Rekening Koran ({items.length})</p>
        </div>
        {items.length === 0 ? (
          <p className="text-sm text-muted text-center py-8">Belum ada data rekening koran.</p>
        ) : (
          <div className="divide-y divide-border">
            {items.map(it => (
              <div key={it.id} className="p-3 flex items-center justify-between gap-3">
                <div className="min-w-0">
                  <p className="text-sm font-semibold text-dpbj-navy">{it.nama_bank || '-'} - {it.nomor_rekening}</p>
                  <p className="text-[10px] text-muted">{BULAN_LABEL[it.bulan]} {it.tahun} {it.nilai ? `• Saldo ${formatRupiah(it.nilai, true)}` : ''}</p>
                </div>
                <div className="flex items-center gap-2 shrink-0">
                  {it.file_path && (
                    <a href={`${SERVER_BASE}/uploads/${it.file_path}`} target="_blank" rel="noreferrer" className="text-blue-600"><Download size={14} /></a>
                  )}
                  <button onClick={() => handleDelete(it.id)} className="p-1 text-red-400 hover:bg-red-50 rounded"><Trash2 size={13} /></button>
                </div>
              </div>
            ))}
          </div>
        )}
      </div>
    </div>
  );
}
