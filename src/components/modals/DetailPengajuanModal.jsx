import { useState } from 'react';
import { X, Download, CheckCircle2, CheckSquare, Square, AlertTriangle } from 'lucide-react';
import { formatRupiah, StatusBadge } from '../ui/shared';
import { statusConfig } from '../../data/mockData';
import { useApp, API_BASE } from '../../context/AppContext';

export default function DetailPengajuanModal({ isOpen, onClose, data }) {
  const { user, getAuthHeaders, triggerRefresh } = useApp();
  const [isDocsComplete, setIsDocsComplete] = useState(false);
  const [adminNotes, setAdminNotes] = useState('');
  const [isSubmitting, setIsSubmitting] = useState(false);

  if (!isOpen || !data) return null;

  const handleAction = async (action) => {
    try {
      setIsSubmitting(true);
      
      let url = `${API_BASE}/pengajuan/${data.id}/${action}`;
      let body = {};
      
      if (action === 'review') {
        body = { is_docs_complete: isDocsComplete, admin_notes: adminNotes };
      } else if (action === 'reject') {
        const reason = prompt('Alasan penolakan:');
        if (reason === null) return;
        body = { reason };
      }

      const res = await fetch(url, {
        method: 'POST',
        headers: { ...getAuthHeaders(), 'Content-Type': 'application/json' },
        body: JSON.stringify(body)
      });
      const json = await res.json();
      
      if (json.success) {
        alert(json.message);
        triggerRefresh();
        onClose();
      } else {
        alert('Gagal: ' + json.message);
      }
    } catch(err) {
      alert('Error: ' + err.message);
    } finally {
      setIsSubmitting(false);
    }
  };

  const statusFlow = ['diajukan', 'proses_review', 'disetujui'];
  const currentIndex = statusFlow.indexOf(data.status);

  return (
    <div className="fixed inset-0 z-50 flex items-center justify-center p-4 bg-dpbj-navy/50 backdrop-blur-sm animate-fade-in">
      <div className="bg-white rounded-2xl shadow-xl w-full max-w-3xl overflow-hidden flex flex-col max-h-[90vh]">
        <div className="flex items-center justify-between p-5 border-b border-border bg-surface">
          <div>
            <h2 className="text-lg font-bold text-dpbj-navy">Detail Pengajuan</h2>
            <p className="text-xs text-muted">{data.request_number}</p>
          </div>
          <button onClick={onClose} className="p-2 text-muted hover:bg-gray-200 rounded-xl transition-colors">
            <X size={18} />
          </button>
        </div>

        <div className="p-6 overflow-y-auto flex-1 space-y-6">
          {/* Timeline Status */}
          <div className="flex items-center justify-between mb-2">
            {['Diajukan PPK', 'Review Berkas', 'Disetujui'].map((step, idx) => (
              <div key={idx} className="flex flex-col items-center flex-1">
                <div className={`w-6 h-6 rounded-full flex items-center justify-center text-xs font-bold text-white ${currentIndex >= idx ? 'bg-emerald-500' : 'bg-gray-200'}`}>
                  {currentIndex >= idx ? <CheckCircle2 size={12}/> : idx + 1}
                </div>
                <span className="text-[10px] mt-1 font-semibold text-gray-500">{step}</span>
              </div>
            ))}
          </div>

          <div className="grid grid-cols-2 gap-4 bg-surface p-4 rounded-xl border border-border">
            <div>
              <p className="text-xs text-muted font-medium mb-1">Judul Pengajuan</p>
              <p className="font-semibold text-sm text-dpbj-navy">{data.title}</p>
            </div>
            <div>
              <p className="text-xs text-muted font-medium mb-1">Status</p>
              <StatusBadge status={data.status} config={statusConfig} />
            </div>
            <div>
              <p className="text-xs text-muted font-medium mb-1">Unit Kerja / Kategori</p>
              <p className="font-semibold text-sm text-dpbj-navy">{data.unit_kerja} / {data.category || '-'}</p>
            </div>
            <div>
              <p className="text-xs text-muted font-medium mb-1">Estimasi Nilai (RAB)</p>
              <p className="font-semibold text-sm text-dpbj-navy">{formatRupiah(data.estimated_value, true)}</p>
            </div>
          </div>

          {/* Dokumen Pendukung */}
          <div>
            <h3 className="text-sm font-bold text-dpbj-navy mb-3">Dokumen Pendukung</h3>
            <div className="grid grid-cols-3 gap-3">
              {[
                { label: 'Nota Dinas', path: data.nota_dinas_path },
                { label: 'KAK / TOR', path: data.kak_path },
                { label: 'RAB / RAE', path: data.rab_path }
              ].map(doc => (
                <div key={doc.label} className="border border-border rounded-xl p-3 flex flex-col items-center justify-center text-center bg-gray-50 hover:bg-gray-100 transition-colors">
                  <span className="text-xs font-bold text-dpbj-navy mb-2">{doc.label}</span>
                  {doc.path ? (
                    <a href={`http://localhost:3001${doc.path}`} target="_blank" rel="noreferrer" className="inline-flex items-center gap-1 text-[10px] text-blue-600 font-semibold bg-blue-50 px-2 py-1 rounded">
                      <Download size={12}/> Unduh
                    </a>
                  ) : (
                    <span className="text-[10px] text-red-500 italic">Tidak dilampirkan</span>
                  )}
                </div>
              ))}
            </div>
          </div>

          {/* Kolom Review Admin DPBJ */}
          {user?.role === 'admin' && data.status === 'diajukan' && (
            <div className="bg-amber-50/50 border border-amber-200 rounded-xl p-4">
              <h3 className="text-sm font-bold text-amber-800 mb-3 flex items-center gap-2"><AlertTriangle size={16}/> Tahap 1: Verifikasi Berkas (Admin DPBJ)</h3>
              <div className="space-y-4">
                <label className="flex items-center gap-2 cursor-pointer text-sm font-medium text-amber-900">
                  <input type="checkbox" className="w-4 h-4 rounded border-amber-300 text-amber-600 focus:ring-amber-500" 
                    checked={isDocsComplete} onChange={e => setIsDocsComplete(e.target.checked)} />
                  Semua dokumen wajib (Nota Dinas, KAK, RAB) sudah lengkap & sesuai format.
                </label>
                <div>
                  <p className="text-xs font-semibold text-amber-800 mb-1">Catatan Verifikasi</p>
                  <textarea className="form-input w-full h-20 text-sm bg-white" placeholder="Beri catatan jika ada yang kurang..." value={adminNotes} onChange={e => setAdminNotes(e.target.value)}></textarea>
                </div>
                <div className="flex justify-end gap-2">
                  <button disabled={isSubmitting} onClick={() => handleAction('reject')} className="btn-secondary text-red-600 bg-red-50 hover:bg-red-100">Tolak Berkas</button>
                  <button disabled={isSubmitting || !isDocsComplete} onClick={() => handleAction('review')} className="btn-primary bg-amber-600 hover:bg-amber-700">Terima & Lanjut ke Pimpinan</button>
                </div>
              </div>
            </div>
          )}

          {user?.role === 'admin' && data.status === 'proses_review' && (
            <div className="bg-emerald-50 border border-emerald-200 rounded-xl p-4">
              <h3 className="text-sm font-bold text-emerald-800 mb-3 flex items-center gap-2"><CheckCircle2 size={16}/> Tahap 2: Persetujuan Akhir (Admin DPBJ)</h3>
              <p className="text-xs text-emerald-700 mb-4">Berkas telah diverifikasi. Pengajuan ini siap untuk disetujui menjadi Paket Tender.</p>
              <div className="flex justify-end gap-2">
                <button disabled={isSubmitting} onClick={() => handleAction('reject')} className="btn-secondary text-red-600 bg-red-50 hover:bg-red-100">Tolak Pengajuan</button>
                <button disabled={isSubmitting} onClick={() => handleAction('approve')} className="btn-primary bg-emerald-600 hover:bg-emerald-700">Setujui & Buat Tender</button>
              </div>
            </div>
          )}
          
          {data.admin_notes && (
            <div className="border-t border-border pt-4">
              <p className="text-xs text-muted font-medium mb-2">Catatan Admin/Pimpinan</p>
              <div className="bg-red-50 rounded-xl p-4 text-sm text-red-800 font-medium">
                {data.admin_notes}
              </div>
            </div>
          )}

        </div>

        <div className="p-4 border-t border-border bg-surface flex justify-end">
          <button onClick={onClose} className="btn-secondary">Tutup</button>
        </div>
      </div>
    </div>
  );
}
