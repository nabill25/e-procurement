import { useState, useEffect, useCallback } from 'react';
import { X, Package, Upload, Download, Trash2, Image as ImageIcon, Paperclip, History, Flag } from 'lucide-react';
import { getAuthHeaders, API_BASE, SERVER_BASE, resolveFileUrl, useApp } from '../../context/AppContext';
import { formatRupiah } from '../ui/shared';
import { toast } from '../../lib/toast';

export default function KatalogDetailModal({ isOpen, onClose, katalogId }) {
  const { user } = useApp();
  const [item, setItem] = useState(null);
  const [photoFile, setPhotoFile] = useState(null);
  const [attachFile, setAttachFile] = useState(null);
  const [attachName, setAttachName] = useState('');
  const [reportForm, setReportForm] = useState({ nama: '', email: '', telepon: '', alasan: '', jenis_laporan: '' });
  const [showReportForm, setShowReportForm] = useState(false);

  const isOwner = user?.role === 'vendor' && item?.vendor_id === user.id;
  const canManage = isOwner || user?.role === 'admin';

  const fetchItem = useCallback(async () => {
    if (!katalogId) return;
    const res = await fetch(`${API_BASE}/katalog/${katalogId}`, { headers: getAuthHeaders() });
    const json = await res.json();
    if (json.success) setItem(json.data);
  }, [katalogId]);

  useEffect(() => { if (isOpen) fetchItem(); }, [isOpen, fetchItem]);

  if (!isOpen || !item) return null;

  const uploadPhoto = async () => {
    if (!photoFile) return;
    const fd = new FormData();
    fd.append('file', photoFile);
    fd.append('created_by', user.id);
    await fetch(`${API_BASE}/katalog/${katalogId}/photos`, { method: 'POST', headers: { Authorization: `Bearer ${localStorage.getItem('dpbj_token')}` }, body: fd });
    setPhotoFile(null); fetchItem();
  };

  const deletePhoto = async (photoId) => {
    if (!confirm('Hapus foto ini?')) return;
    await fetch(`${API_BASE}/katalog/${katalogId}/photos/${photoId}`, { method: 'DELETE', headers: getAuthHeaders() });
    fetchItem();
  };

  const uploadAttachment = async () => {
    if (!attachFile) return;
    const fd = new FormData();
    fd.append('file', attachFile);
    fd.append('nama', attachName || attachFile.name);
    fd.append('created_by', user.id);
    await fetch(`${API_BASE}/katalog/${katalogId}/attachments`, { method: 'POST', headers: { Authorization: `Bearer ${localStorage.getItem('dpbj_token')}` }, body: fd });
    setAttachFile(null); setAttachName(''); fetchItem();
  };

  const deleteAttachment = async (attachmentId) => {
    if (!confirm('Hapus lampiran ini?')) return;
    await fetch(`${API_BASE}/katalog/${katalogId}/attachments/${attachmentId}`, { method: 'DELETE', headers: getAuthHeaders() });
    fetchItem();
  };

  const submitReport = async (e) => {
    e.preventDefault();
    if (!reportForm.alasan.trim()) return toast('Alasan laporan wajib diisi.');
    const res = await fetch(`${API_BASE}/katalog/reports`, {
      method: 'POST', headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ katalog_id: katalogId, ...reportForm }),
    });
    const json = await res.json();
    if (json.success) { toast('Laporan berhasil dikirim.'); setShowReportForm(false); setReportForm({ nama: '', email: '', telepon: '', alasan: '', jenis_laporan: '' }); }
    else toast('Gagal: ' + json.message);
  };

  return (
    <div className="fixed inset-0 z-[60] flex items-center justify-center p-4 bg-dpbj-navy/50 backdrop-blur-sm animate-fade-in">
      <div className="bg-white rounded-2xl shadow-xl w-full max-w-3xl overflow-hidden flex flex-col max-h-[90vh]">
        <div className="flex items-center justify-between p-5 border-b border-border bg-surface">
          <div>
            <h2 className="text-lg font-bold text-dpbj-navy">{item.item_name}</h2>
            <p className="text-xs text-muted">{item.company_name}</p>
          </div>
          <button onClick={onClose} className="p-2 text-muted hover:bg-white rounded-xl border border-transparent hover:border-border">
            <X size={18} />
          </button>
        </div>

        <div className="flex-1 overflow-y-auto p-6 space-y-5">
          <div className="grid grid-cols-2 gap-4">
            <div className="bg-surface p-3 rounded-xl border border-border">
              <p className="text-xs text-muted mb-1">Harga</p>
              <p className="font-bold text-dpbj-gold">{formatRupiah(item.price, true)} / {item.unit}</p>
            </div>
            <div className="bg-surface p-3 rounded-xl border border-border">
              <p className="text-xs text-muted mb-1">Stok</p>
              <p className="font-bold text-dpbj-navy">{item.jumlah_stock ?? '-'} {item.jumlah_stock_ready ? `(${item.jumlah_stock_ready})` : ''}</p>
            </div>
            <div className="bg-surface p-3 rounded-xl border border-border">
              <p className="text-xs text-muted mb-1">Merek / Model</p>
              <p className="font-semibold text-dpbj-navy text-sm">{item.brand || '-'} {item.model_type ? `/ ${item.model_type}` : ''}</p>
            </div>
            <div className="bg-surface p-3 rounded-xl border border-border">
              <p className="text-xs text-muted mb-1">TKDN</p>
              <p className="font-semibold text-dpbj-navy text-sm">{item.tkdn_persen != null ? `${item.tkdn_persen}%` : '-'}</p>
            </div>
            <div className="bg-surface p-3 rounded-xl border border-border">
              <p className="text-xs text-muted mb-1">Dimensi (P x L x T)</p>
              <p className="font-semibold text-dpbj-navy text-sm">{item.panjang || 0} x {item.lebar || 0} x {item.tinggi || 0} {item.unit_pengukuran || ''}</p>
            </div>
            <div className="bg-surface p-3 rounded-xl border border-border">
              <p className="text-xs text-muted mb-1">Garansi</p>
              <p className="font-semibold text-dpbj-navy text-sm">{item.lama_garansi ? `${item.lama_garansi} ${item.lama_garansi_satuan || ''}` : '-'}</p>
            </div>
          </div>

          {item.categories?.length > 0 && (
            <div>
              <p className="text-xs font-semibold text-muted uppercase tracking-wide mb-2">Kategori</p>
              <div className="flex flex-wrap gap-1.5">
                {item.categories.map(c => <span key={c.id} className="text-xs bg-surface border border-border px-2.5 py-1 rounded-full text-dpbj-navy">{c.nama}</span>)}
              </div>
            </div>
          )}

          {item.description && (
            <div>
              <p className="text-xs font-semibold text-muted uppercase tracking-wide mb-1">Deskripsi</p>
              <p className="text-sm text-dpbj-navy">{item.description}</p>
            </div>
          )}

          <div className="border-t border-border pt-4">
            <div className="flex items-center justify-between mb-2">
              <p className="text-xs font-semibold text-muted uppercase tracking-wide flex items-center gap-1.5"><ImageIcon size={13} /> Foto Produk</p>
            </div>
            {canManage && (
              <div className="flex flex-wrap items-center gap-2 mb-2">
                <input type="file" accept="image/*" onChange={e => setPhotoFile(e.target.files[0])} className="text-xs flex-1 min-w-[140px]" />
                <button onClick={uploadPhoto} className="btn-secondary text-xs flex items-center gap-1 whitespace-nowrap"><Upload size={11} /> Unggah</button>
              </div>
            )}
            {item.photos?.length > 0 ? (
              <div className="grid grid-cols-4 gap-2">
                {item.photos.map(p => (
                  <div key={p.id} className="relative group">
                    <img src={resolveFileUrl(p.file_path)} alt="" className="w-full h-20 object-cover rounded-lg border border-border" />
                    {canManage && (
                      <button onClick={() => deletePhoto(p.id)} className="absolute top-1 right-1 bg-white/90 p-1 rounded-full text-red-500 opacity-0 group-hover:opacity-100 transition-opacity">
                        <Trash2 size={11} />
                      </button>
                    )}
                  </div>
                ))}
              </div>
            ) : <p className="text-xs text-muted">Belum ada foto tambahan.</p>}
          </div>

          <div className="border-t border-border pt-4">
            <p className="text-xs font-semibold text-muted uppercase tracking-wide mb-2 flex items-center gap-1.5"><Paperclip size={13} /> Lampiran</p>
            {canManage && (
              <div className="flex flex-wrap items-center gap-2 mb-2">
                <input placeholder="Nama lampiran" value={attachName} onChange={e => setAttachName(e.target.value)} className="text-xs p-1.5 border border-gray-300 rounded-lg flex-1 min-w-[120px]" />
                <input type="file" onChange={e => setAttachFile(e.target.files[0])} className="text-xs max-w-full" />
                <button onClick={uploadAttachment} className="btn-secondary text-xs flex items-center gap-1 whitespace-nowrap"><Upload size={11} /> Unggah</button>
              </div>
            )}
            {item.attachments?.length > 0 ? (
              <div className="space-y-1">
                {item.attachments.map(a => (
                  <div key={a.id} className="flex items-center justify-between text-xs bg-surface p-2 rounded-lg">
                    <span>{a.nama}</span>
                    <div className="flex items-center gap-2">
                      <a href={resolveFileUrl(a.file_path)} target="_blank" rel="noreferrer" className="text-blue-600"><Download size={12} /></a>
                      {canManage && <button onClick={() => deleteAttachment(a.id)} className="text-red-400"><Trash2 size={11} /></button>}
                    </div>
                  </div>
                ))}
              </div>
            ) : <p className="text-xs text-muted">Belum ada lampiran.</p>}
          </div>

          {item.price_history?.length > 0 && (
            <div className="border-t border-border pt-4">
              <p className="text-xs font-semibold text-muted uppercase tracking-wide mb-2 flex items-center gap-1.5"><History size={13} /> Riwayat Harga</p>
              <div className="space-y-1">
                {item.price_history.map(h => (
                  <div key={h.id} className="text-xs bg-surface p-2 rounded-lg text-dpbj-navy">
                    {formatRupiah(h.harga_lama, true)} <span className="text-muted">→</span> {formatRupiah(h.harga_baru, true)}
                    <span className="text-muted ml-2">{new Date(h.created_at).toLocaleDateString('id-ID')}</span>
                  </div>
                ))}
              </div>
            </div>
          )}

          <div className="border-t border-border pt-4">
            {!showReportForm ? (
              <button onClick={() => setShowReportForm(true)} className="text-xs text-red-500 font-semibold flex items-center gap-1.5">
                <Flag size={12} /> Laporkan produk ini
              </button>
            ) : (
              <form onSubmit={submitReport} className="bg-red-50 border border-red-200 rounded-xl p-3 space-y-2">
                <p className="text-xs font-bold text-red-700">Laporkan Produk</p>
                <div className="grid grid-cols-2 gap-2">
                  <input placeholder="Nama Anda" value={reportForm.nama} onChange={e => setReportForm({ ...reportForm, nama: e.target.value })} className="text-xs p-1.5 border border-gray-300 rounded-lg" />
                  <input placeholder="Email" value={reportForm.email} onChange={e => setReportForm({ ...reportForm, email: e.target.value })} className="text-xs p-1.5 border border-gray-300 rounded-lg" />
                </div>
                <input placeholder="Jenis laporan (mis. Harga, Deskripsi)" value={reportForm.jenis_laporan} onChange={e => setReportForm({ ...reportForm, jenis_laporan: e.target.value })} className="w-full text-xs p-1.5 border border-gray-300 rounded-lg" />
                <textarea placeholder="Alasan laporan" required value={reportForm.alasan} onChange={e => setReportForm({ ...reportForm, alasan: e.target.value })} className="w-full text-xs p-1.5 border border-gray-300 rounded-lg h-16" />
                <div className="flex justify-end gap-2">
                  <button type="button" onClick={() => setShowReportForm(false)} className="btn-ghost text-xs">Batal</button>
                  <button type="submit" className="btn-primary text-xs bg-red-600 hover:bg-red-700">Kirim Laporan</button>
                </div>
              </form>
            )}
          </div>
        </div>
      </div>
    </div>
  );
}
