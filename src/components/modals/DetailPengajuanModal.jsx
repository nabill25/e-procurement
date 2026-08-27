import { useState, useEffect, useCallback } from 'react';
import { X, Download, CheckCircle2, CheckSquare, Square, AlertTriangle, Upload, FileEdit, History, ClipboardList } from 'lucide-react';
import { formatRupiah, StatusBadge } from '../ui/shared';
import { statusConfig } from '../../data/mockData';
import { getAuthHeaders, useApp, API_BASE, SERVER_BASE } from '../../context/AppContext';
import clsx from 'clsx';

function ChecklistSection({ pengajuanId, category, canEdit, user, getAuthHeaders }) {
  const [items, setItems] = useState([]);

  const fetchChecklist = useCallback(async () => {
    const res = await fetch(`${API_BASE}/pengajuan/${pengajuanId}/checklist?paket_jenis=${category || ''}`, { headers: getAuthHeaders() });
    const json = await res.json();
    if (json.success) setItems(json.data);
  }, [pengajuanId, category]);

  useEffect(() => { fetchChecklist(); }, [fetchChecklist]);

  const toggle = async (item) => {
    if (!canEdit) return;
    await fetch(`${API_BASE}/pengajuan/${pengajuanId}/checklist`, {
      method: 'POST', headers: getAuthHeaders(),
      body: JSON.stringify({ master_checklist_id: item.id, approved: !item.approved, created_by: user.id }),
    });
    fetchChecklist();
  };

  if (items.length === 0) return null;

  return (
    <div>
      <h3 className="text-sm font-bold text-dpbj-navy mb-3 flex items-center gap-2"><ClipboardList size={16} /> Checklist Kelengkapan</h3>
      <div className="space-y-1.5">
        {items.map(item => (
          <button
            key={item.id}
            onClick={() => toggle(item)}
            disabled={!canEdit}
            className={clsx('w-full flex items-center gap-2 text-left text-xs p-2 rounded-lg border', item.approved ? 'bg-emerald-50 border-emerald-200' : 'bg-surface border-border', canEdit && 'cursor-pointer hover:bg-gray-100')}
          >
            {item.approved ? <CheckSquare size={14} className="text-emerald-600 shrink-0" /> : <Square size={14} className="text-muted shrink-0" />}
            <span className="text-dpbj-navy">{item.nama} {item.wajib && <span className="text-red-500">*wajib</span>}</span>
          </button>
        ))}
      </div>
    </div>
  );
}

function FileAnalisaSection({ pengajuanId, canEdit, user, getAuthHeaders }) {
  const [files, setFiles] = useState([]);
  const [judul, setJudul] = useState('');
  const [file, setFile] = useState(null);

  const fetchFiles = useCallback(async () => {
    const res = await fetch(`${API_BASE}/pengajuan/${pengajuanId}/files`, { headers: getAuthHeaders() });
    const json = await res.json();
    if (json.success) setFiles(json.data);
  }, [pengajuanId]);

  useEffect(() => { fetchFiles(); }, [fetchFiles]);

  const handleUpload = async () => {
    if (!file) return alert('Pilih file terlebih dahulu.');
    const fd = new FormData();
    fd.append('judul', judul || file.name);
    fd.append('created_by', user.id);
    fd.append('file', file);
    await fetch(`${API_BASE}/pengajuan/${pengajuanId}/files`, { method: 'POST', headers: { Authorization: `Bearer ${localStorage.getItem('dpbj_token')}` }, body: fd });
    setJudul(''); setFile(null); fetchFiles();
  };

  return (
    <div>
      <h3 className="text-sm font-bold text-dpbj-navy mb-3">File Analisa Tambahan</h3>
      {canEdit && (
        <div className="flex items-center gap-2 mb-2">
          <input placeholder="Judul dokumen" value={judul} onChange={e => setJudul(e.target.value)} className="flex-1 text-xs p-2 border border-gray-300 rounded-lg" />
          <input type="file" onChange={e => setFile(e.target.files[0])} className="text-xs" />
          <button onClick={handleUpload} className="btn-secondary text-xs flex items-center gap-1"><Upload size={11} /> Unggah</button>
        </div>
      )}
      {files.length === 0 ? <p className="text-xs text-muted">Belum ada file analisa tambahan.</p> : (
        <div className="space-y-1">
          {files.map(f => (
            <div key={f.id} className="flex items-center justify-between text-xs bg-surface p-2 rounded-lg">
              <span>{f.judul} {f.esign_status && <span className="text-emerald-600 ml-1">({f.esign_status})</span>}</span>
              <a href={`${SERVER_BASE}${f.file_path}`} target="_blank" rel="noreferrer" className="text-blue-600"><Download size={12} /></a>
            </div>
          ))}
        </div>
      )}
    </div>
  );
}

function RevisionHistorySection({ pengajuanId, getAuthHeaders }) {
  const [revisions, setRevisions] = useState([]);

  useEffect(() => {
    fetch(`${API_BASE}/pengajuan/${pengajuanId}/revisions`, { headers: getAuthHeaders() })
      .then(r => r.json()).then(j => { if (j.success) setRevisions(j.data); }).catch(() => {});
  }, [pengajuanId]);

  if (revisions.length === 0) return null;

  return (
    <div>
      <h3 className="text-sm font-bold text-dpbj-navy mb-3 flex items-center gap-2"><History size={16} /> Riwayat Revisi</h3>
      <div className="space-y-2">
        {revisions.map(r => (
          <div key={r.id} className="bg-red-50 border border-red-200 rounded-lg p-3 text-xs">
            <p className="text-red-800">{r.catatan}</p>
            <div className="flex items-center justify-between mt-1.5">
              <span className="text-red-500">{new Date(r.created_at).toLocaleDateString('id-ID')}</span>
              {r.file_path && <a href={`${SERVER_BASE}${r.file_path}`} target="_blank" rel="noreferrer" className="text-blue-600"><Download size={12} /></a>}
            </div>
          </div>
        ))}
      </div>
    </div>
  );
}

function ActivityLogSection({ pengajuanId, getAuthHeaders }) {
  const [logs, setLogs] = useState([]);

  useEffect(() => {
    fetch(`${API_BASE}/pengajuan/${pengajuanId}/activity-log`, { headers: getAuthHeaders() })
      .then(r => r.json()).then(j => { if (j.success) setLogs(j.data); }).catch(() => {});
  }, [pengajuanId]);

  if (logs.length === 0) return null;

  return (
    <div>
      <h3 className="text-sm font-bold text-dpbj-navy mb-3 flex items-center gap-2"><History size={16} /> Rekam Jejak</h3>
      <div className="space-y-2 border-l-2 border-dpbj-gold/40 pl-4">
        {logs.map(l => (
          <div key={l.id} className="text-xs relative">
            <div className="absolute -left-[21px] top-1 w-2.5 h-2.5 rounded-full bg-dpbj-gold" />
            <p className="font-semibold text-dpbj-navy">{l.posisi}</p>
            {l.keterangan && <p className="text-muted">{l.keterangan}</p>}
            <p className="text-muted mt-0.5">{l.user_name || 'Sistem'} · {new Date(l.created_at).toLocaleString('id-ID')}</p>
          </div>
        ))}
      </div>
    </div>
  );
}

export default function DetailPengajuanModal({ isOpen, onClose, data }) {
  const { user, triggerRefresh } = useApp();
  const [isDocsComplete, setIsDocsComplete] = useState(false);
  const [adminNotes, setAdminNotes] = useState('');
  const [isSubmitting, setIsSubmitting] = useState(false);
  const [showRevisionForm, setShowRevisionForm] = useState(false);
  const [revisionCatatan, setRevisionCatatan] = useState('');
  const [revisionFile, setRevisionFile] = useState(null);

  if (!isOpen || !data) return null;

  const handleSendRevision = async () => {
    if (!revisionCatatan.trim()) return alert('Catatan revisi wajib diisi.');
    const fd = new FormData();
    fd.append('catatan', revisionCatatan);
    fd.append('created_by', user.id);
    if (revisionFile) fd.append('file', revisionFile);
    try {
      setIsSubmitting(true);
      await fetch(`${API_BASE}/pengajuan/${data.id}/revisions`, { method: 'POST', headers: { Authorization: `Bearer ${localStorage.getItem('dpbj_token')}` }, body: fd });
      alert('Catatan revisi berhasil dikirim ke pengaju.');
      setShowRevisionForm(false); setRevisionCatatan(''); setRevisionFile(null);
      triggerRefresh();
      onClose();
    } catch (err) {
      alert('Error: ' + err.message);
    } finally {
      setIsSubmitting(false);
    }
  };

  const handleAction = async (action) => {
    try {
      setIsSubmitting(true);
      
      let url = `${API_BASE}/pengajuan/${data.id}/${action}`;
      let body = { user_id: user.id };

      if (action === 'review') {
        body = { ...body, is_docs_complete: isDocsComplete, admin_notes: adminNotes };
      } else if (action === 'reject') {
        const reason = prompt('Alasan penolakan:');
        if (reason === null) return;
        body = { ...body, reason };
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

          {(data.komoditas || data.analisa_kebutuhan || data.analisa_pasar || data.risiko_teridentifikasi) && (
            <div className="bg-surface p-4 rounded-xl border border-border">
              <h3 className="text-sm font-bold text-dpbj-navy mb-3">Analisa Kebutuhan & Pasar</h3>
              <div className="grid grid-cols-2 gap-3 text-xs">
                {data.komoditas && (
                  <div>
                    <p className="text-muted mb-0.5">Komoditas</p>
                    <p className="font-semibold text-dpbj-navy">{data.komoditas}</p>
                  </div>
                )}
                {data.analisa_kebutuhan && (
                  <div>
                    <p className="text-muted mb-0.5">Analisa Kebutuhan</p>
                    <p className="font-semibold text-dpbj-navy">{data.analisa_kebutuhan}</p>
                  </div>
                )}
                {data.analisa_pasar && (
                  <div>
                    <p className="text-muted mb-0.5">Analisa Pasar</p>
                    <p className="font-semibold text-dpbj-navy">{data.analisa_pasar}</p>
                  </div>
                )}
              </div>
              {data.risiko_teridentifikasi && (
                <div className="mt-3 pt-3 border-t border-border">
                  <p className="text-xs font-bold text-amber-700 flex items-center gap-1">⚠ Risiko Teridentifikasi</p>
                  {data.risiko_keterangan && <p className="text-xs text-dpbj-navy mt-1">{data.risiko_keterangan}</p>}
                </div>
              )}
            </div>
          )}

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
                    <a href={`${SERVER_BASE}${doc.path}`} target="_blank" rel="noreferrer" className="inline-flex items-center gap-1 text-[10px] text-blue-600 font-semibold bg-blue-50 px-2 py-1 rounded">
                      <Download size={12}/> Unduh
                    </a>
                  ) : (
                    <span className="text-[10px] text-red-500 italic">Tidak dilampirkan</span>
                  )}
                </div>
              ))}
            </div>
          </div>

          <RevisionHistorySection pengajuanId={data.id} getAuthHeaders={getAuthHeaders} />

          <ActivityLogSection pengajuanId={data.id} getAuthHeaders={getAuthHeaders} />

          <ChecklistSection pengajuanId={data.id} category={data.category} canEdit={user?.role === 'admin'} user={user} getAuthHeaders={getAuthHeaders} />

          <FileAnalisaSection pengajuanId={data.id} canEdit={true} user={user} getAuthHeaders={getAuthHeaders} />

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
                {!showRevisionForm ? (
                  <div className="flex justify-end gap-2">
                    <button disabled={isSubmitting} onClick={() => setShowRevisionForm(true)} className="btn-secondary text-amber-700 bg-amber-100 hover:bg-amber-200 flex items-center gap-1.5"><FileEdit size={14} /> Minta Revisi</button>
                    <button disabled={isSubmitting} onClick={() => handleAction('reject')} className="btn-secondary text-red-600 bg-red-50 hover:bg-red-100">Tolak Berkas</button>
                    <button disabled={isSubmitting || !isDocsComplete} onClick={() => handleAction('review')} className="btn-primary bg-amber-600 hover:bg-amber-700">Terima & Lanjut ke Pimpinan</button>
                  </div>
                ) : (
                  <div className="bg-white border border-amber-300 rounded-lg p-3 space-y-2">
                    <p className="text-xs font-bold text-amber-800">Kirim Catatan Revisi ke Pengaju</p>
                    <textarea required value={revisionCatatan} onChange={e => setRevisionCatatan(e.target.value)} placeholder="Jelaskan apa yang perlu direvisi..." className="w-full text-xs p-2 border border-gray-300 rounded-lg h-16" />
                    <input type="file" onChange={e => setRevisionFile(e.target.files[0])} className="text-xs" />
                    <div className="flex justify-end gap-2">
                      <button onClick={() => setShowRevisionForm(false)} className="btn-ghost text-xs">Batal</button>
                      <button disabled={isSubmitting} onClick={handleSendRevision} className="btn-primary text-xs bg-amber-600 hover:bg-amber-700">Kirim Revisi</button>
                    </div>
                  </div>
                )}
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
