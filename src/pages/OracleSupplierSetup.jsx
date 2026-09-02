import { useState, useEffect, useCallback } from 'react';
import { createPortal } from 'react-dom';
import {
  Landmark, Plus, X, Clock, Send, UserCheck, PlayCircle, Upload, CheckCircle2,
  Building2, ChevronRight, History,
} from 'lucide-react';
import { API_BASE, getAuthHeaders, useApp } from '../context/AppContext';
import { toast } from '../lib/toast';

// Modul "Setup Supplier Oracle" - alur tiket permintaan setup supplier baru di Oracle EBS,
// padanan aplikasi terpisah yang ditunjukkan pengguna (folder root project
// setup-supplier-request/). BUKAN bagian dari fitur "Integrasi Oracle" yang sudah ada (itu
// soal sinkronisasi RKA/PR lewat SFTP, lihat Integration.jsx) - ini modul terpisah, alur:
// Pengaju -> Verifikator -> Dispatcher -> Pelaksana.

const STATUS_META = {
  diajukan:    { label: 'Diajukan',           className: 'bg-gray-100 text-gray-600' },
  diverifikasi:{ label: 'Diverifikasi',       className: 'bg-blue-100 text-blue-700' },
  diteruskan:  { label: 'Diteruskan',         className: 'bg-blue-100 text-blue-700' },
  didispatch:  { label: 'Didispatch',         className: 'bg-amber-100 text-amber-700' },
  dikerjakan:  { label: 'Dikerjakan',         className: 'bg-amber-100 text-amber-700' },
  selesai:     { label: 'Selesai',            className: 'bg-emerald-100 text-emerald-700' },
};

function umur(tanggal) {
  if (!tanggal) return '';
  const selisih = Date.now() - new Date(tanggal).getTime();
  if (selisih < 3600000) return Math.max(0, Math.floor(selisih / 60000)) + ' menit lalu';
  if (selisih < 86400000) return Math.floor(selisih / 3600000) + ' jam lalu';
  return Math.floor(selisih / 86400000) + ' hari lalu';
}

const COLUMNS_BY_ROLE = {
  pengaju_oracle:     [{ label: 'Aktif', statuses: ['diajukan', 'diverifikasi', 'diteruskan', 'didispatch', 'dikerjakan'] }, { label: 'Selesai', statuses: ['selesai'] }],
  verifikator_oracle: [{ label: 'Menunggu Verifikasi', statuses: ['diajukan'] }, { label: 'Sudah Diproses', statuses: ['diverifikasi', 'diteruskan', 'didispatch', 'dikerjakan', 'selesai'] }],
  dispatcher_oracle:  [{ label: 'Perlu Di-dispatch', statuses: ['diteruskan'] }, { label: 'Sudah Di-dispatch', statuses: ['didispatch', 'dikerjakan', 'selesai'] }],
  pelaksana_oracle:   [{ label: 'Tugas Saya', statuses: ['didispatch', 'dikerjakan'], onlyMine: true }, { label: 'Selesai', statuses: ['selesai'], onlyMine: true }],
  admin: [
    { label: 'Diajukan', statuses: ['diajukan'] },
    { label: 'Diteruskan', statuses: ['diverifikasi', 'diteruskan'] },
    { label: 'Dikerjakan', statuses: ['didispatch', 'dikerjakan'] },
    { label: 'Selesai', statuses: ['selesai'] },
  ],
};

function RequestCard({ req, onClick }) {
  const meta = STATUS_META[req.status] || { label: req.status, className: 'bg-gray-100 text-gray-600' };
  return (
    <button onClick={onClick} className="w-full text-left bg-white border border-border rounded-xl p-3.5 hover:shadow-card-lg hover:border-dpbj-gold/40 transition-all">
      <div className="flex items-center justify-between gap-2 mb-1.5">
        <span className="font-mono text-[10px] text-muted">{req.kode}</span>
        <span className={`badge text-[10px] ${meta.className}`}>{meta.label}</span>
      </div>
      <p className="font-semibold text-sm text-dpbj-navy truncate">{req.nama_supplier}</p>
      <p className="text-xs text-muted truncate flex items-center gap-1"><Building2 size={11} /> {req.operating_unit}</p>
      <p className="text-[10px] text-muted mt-1.5 flex items-center gap-1"><Clock size={10} /> {umur(req.submitted_at)}</p>
    </button>
  );
}

function DetailModal({ req, user, onClose, onChanged }) {
  const [detail, setDetail] = useState(null);
  const [pelaksanaList, setPelaksanaList] = useState([]);
  const [form, setForm] = useState({ catatan_verifikator: '', aktivasi_dari: '', aktivasi_sampai: '' });
  const [assignedTo, setAssignedTo] = useState('');
  const [bukti, setBukti] = useState(null);
  const [saving, setSaving] = useState(false);

  const fetchDetail = useCallback(async () => {
    const res = await fetch(`${API_BASE}/oracle-supplier/${req.id}`, { headers: getAuthHeaders() });
    const json = await res.json();
    if (json.success) setDetail(json.data);
  }, [req.id]);

  useEffect(() => {
    fetchDetail();
    if (['admin', 'dispatcher_oracle'].includes(user.role)) {
      fetch(`${API_BASE}/oracle-supplier/pelaksana`, { headers: getAuthHeaders() })
        .then(r => r.json()).then(j => { if (j.success) setPelaksanaList(j.data); }).catch(() => {});
    }
  }, [fetchDetail, user.role]);

  const post = async (url, body, isMultipart) => {
    setSaving(true);
    try {
      const res = await fetch(`${API_BASE}/oracle-supplier/${req.id}${url}`, {
        method: 'POST',
        headers: isMultipart ? { Authorization: getAuthHeaders().Authorization } : getAuthHeaders(),
        body: isMultipart ? body : JSON.stringify(body),
      });
      const json = await res.json();
      if (json.success) {
        toast(json.message);
        fetchDetail();
        onChanged();
      } else {
        toast('Gagal: ' + json.message);
      }
    } catch {
      toast('Terjadi kesalahan saat menghubungi server.');
    } finally {
      setSaving(false);
    }
  };

  if (!detail) return createPortal(<div className="modal-overlay"><div className="modal-container w-full max-w-2xl p-8 text-center text-sm text-muted">Memuat...</div></div>, document.body);

  const canVerify = ['admin', 'verifikator_oracle'].includes(user.role) && detail.status === 'diajukan';
  const canDispatch = ['admin', 'dispatcher_oracle'].includes(user.role) && detail.status === 'diteruskan';
  const isMyTask = detail.assigned_to === user.id || ['admin', 'dispatcher_oracle'].includes(user.role);
  const canStart = isMyTask && detail.status === 'didispatch';
  const canComplete = isMyTask && detail.status === 'dikerjakan';

  const fields = [
    ['Operating Unit', detail.operating_unit], ['Nama Supplier', detail.nama_supplier],
    ['Alamat Kantor', detail.alamat_kantor], ['No. Telp', detail.no_telp],
    ['Nama Kontak', detail.nama_kontak], ['Jabatan', detail.jabatan],
    ['No. PKP', detail.no_pkp], ['No. NIB', detail.no_nib],
    ['Tgl NIB', detail.tgl_nib ? new Date(detail.tgl_nib).toLocaleDateString('id-ID') : '-'],
    ['Domisili', detail.domisili], ['NPWP', detail.npwp], ['Alamat NPWP', detail.alamat_npwp],
    ['Bank', detail.nama_bank], ['Cabang', detail.cabang_bank],
    ['Nama Rekening', detail.nama_rekening], ['No. Rekening', detail.nomor_rekening],
    ['Mata Uang', detail.mata_uang], ['Paket RUP', detail.nama_paket_rup || '-'], ['Kode RUP', detail.kode_rup || '-'],
  ];

  return createPortal(
    <div className="modal-overlay">
      <div className="modal-container w-full max-w-3xl max-h-[90vh] flex flex-col">
        <div className="flex items-center justify-between p-5 border-b border-border bg-surface">
          <div>
            <h2 className="text-lg font-bold text-dpbj-navy flex items-center gap-2"><Landmark size={18} /> {detail.kode}</h2>
            <p className="text-xs text-muted mt-0.5">{detail.nama_supplier} &middot; {detail.operating_unit}</p>
          </div>
          <button onClick={onClose} className="p-2 hover:bg-white rounded-xl"><X size={18} className="text-gray-500" /></button>
        </div>

        <div className="flex-1 overflow-y-auto p-5 space-y-5">
          <div className="grid grid-cols-2 sm:grid-cols-3 gap-3">
            {fields.map(([label, val]) => (
              <div key={label} className="bg-surface rounded-lg p-2.5">
                <p className="text-[10px] text-muted font-semibold uppercase">{label}</p>
                <p className="text-xs font-medium text-dpbj-navy truncate">{val || '-'}</p>
              </div>
            ))}
          </div>

          {detail.catatan_verifikator && (
            <div className="bg-blue-50 border border-blue-100 rounded-lg p-3 text-xs text-blue-800">
              <b>Catatan Verifikator:</b> {detail.catatan_verifikator}
            </div>
          )}

          {detail.bukti_screenshot && (
            <a href={`${API_BASE.replace('/api', '')}${detail.bukti_screenshot}`} target="_blank" rel="noreferrer" className="btn-secondary text-xs inline-flex">
              <Upload size={12} /> Lihat Bukti Penyelesaian
            </a>
          )}

          {/* Aksi sesuai role & status */}
          {canVerify && (
            <div className="border border-border rounded-xl p-4 space-y-3 bg-surface">
              <h3 className="text-sm font-bold text-dpbj-navy flex items-center gap-2"><Send size={14} /> Verifikasi & Teruskan ke Tim Support Oracle</h3>
              <textarea rows={2} className="form-input w-full text-sm" placeholder="Catatan untuk tim Oracle (opsional)"
                value={form.catatan_verifikator} onChange={e => setForm({ ...form, catatan_verifikator: e.target.value })} />
              <div className="grid grid-cols-2 gap-3">
                <div>
                  <label className="block text-[11px] font-semibold text-muted mb-1">Aktivasi Dari (opsional)</label>
                  <input type="date" className="form-input w-full text-sm" value={form.aktivasi_dari} onChange={e => setForm({ ...form, aktivasi_dari: e.target.value })} />
                </div>
                <div>
                  <label className="block text-[11px] font-semibold text-muted mb-1">Aktivasi Sampai (opsional)</label>
                  <input type="date" className="form-input w-full text-sm" value={form.aktivasi_sampai} onChange={e => setForm({ ...form, aktivasi_sampai: e.target.value })} />
                </div>
              </div>
              <button disabled={saving} onClick={() => post('/verify-and-forward', form, false)} className="btn-primary text-xs disabled:opacity-50">
                <Send size={13} /> Verifikasi & Teruskan
              </button>
            </div>
          )}

          {canDispatch && (
            <div className="border border-border rounded-xl p-4 space-y-3 bg-surface">
              <h3 className="text-sm font-bold text-dpbj-navy flex items-center gap-2"><UserCheck size={14} /> Dispatch ke Pelaksana</h3>
              <select className="form-input w-full text-sm" value={assignedTo} onChange={e => setAssignedTo(e.target.value)}>
                <option value="">-- Ambil sendiri (saya kerjakan) --</option>
                {pelaksanaList.map(p => <option key={p.id} value={p.id}>{p.full_name}</option>)}
              </select>
              <button disabled={saving} onClick={() => post('/dispatch', { assigned_to: assignedTo || null }, false)} className="btn-primary text-xs disabled:opacity-50">
                <UserCheck size={13} /> {assignedTo ? 'Dispatch ke Pelaksana Ini' : 'Ambil Sendiri'}
              </button>
            </div>
          )}

          {canStart && (
            <button disabled={saving} onClick={() => post('/start', {}, false)} className="btn-primary text-xs disabled:opacity-50">
              <PlayCircle size={13} /> Mulai Kerjakan di Oracle EBS
            </button>
          )}

          {canComplete && (
            <div className="border border-border rounded-xl p-4 space-y-3 bg-surface">
              <h3 className="text-sm font-bold text-dpbj-navy flex items-center gap-2"><CheckCircle2 size={14} /> Tandai Selesai</h3>
              <input type="file" accept=".pdf,.jpg,.jpeg,.png" className="form-input w-full text-sm bg-white" onChange={e => setBukti(e.target.files[0])} />
              <button
                disabled={saving || !bukti}
                onClick={() => { const fd = new FormData(); fd.append('bukti', bukti); post('/complete', fd, true); }}
                className="btn-primary bg-emerald-600 hover:bg-emerald-700 text-white text-xs disabled:opacity-50"
              >
                <CheckCircle2 size={13} /> Tandai Selesai (bukti wajib)
              </button>
            </div>
          )}

          {/* Riwayat status */}
          <div>
            <h3 className="text-sm font-bold text-dpbj-navy flex items-center gap-2 mb-2"><History size={14} /> Riwayat</h3>
            <div className="space-y-2">
              {detail.logs.map(l => {
                const m = STATUS_META[l.status] || { label: l.status, className: 'bg-gray-100 text-gray-600' };
                return (
                  <div key={l.id} className="flex items-start gap-2 text-xs border-b border-border pb-2 last:border-0">
                    <span className={`badge text-[10px] shrink-0 ${m.className}`}>{m.label}</span>
                    <div>
                      <p className="text-dpbj-navy">{l.catatan}</p>
                      <p className="text-muted text-[10px]">oleh {l.changed_by_name || 'Sistem'} &middot; {new Date(l.changed_at).toLocaleString('id-ID')}</p>
                    </div>
                  </div>
                );
              })}
            </div>
          </div>
        </div>
      </div>
    </div>,
    document.body
  );
}

function NewRequestModal({ isOpen, onClose, onCreated }) {
  const empty = { operating_unit: '', nama_supplier: '', alamat_kantor: '', no_telp: '', nama_kontak: '', jabatan: '', no_pkp: '', no_nib: '', tgl_nib: '', domisili: '', npwp: '', alamat_npwp: '', nama_bank: '', cabang_bank: '', nama_rekening: '', nomor_rekening: '', mata_uang: 'IDR', nama_paket_rup: '', kode_rup: '' };
  const [form, setForm] = useState(empty);
  const [saving, setSaving] = useState(false);

  if (!isOpen) return null;

  const set = (k) => (e) => setForm({ ...form, [k]: e.target.value });

  const submit = async (e) => {
    e.preventDefault();
    setSaving(true);
    try {
      const res = await fetch(`${API_BASE}/oracle-supplier`, { method: 'POST', headers: getAuthHeaders(), body: JSON.stringify(form) });
      const json = await res.json();
      if (json.success) {
        toast(json.message);
        setForm(empty);
        onCreated();
        onClose();
      } else {
        toast('Gagal: ' + json.message);
      }
    } catch {
      toast('Terjadi kesalahan saat menghubungi server.');
    } finally {
      setSaving(false);
    }
  };

  const REQUIRED = [
    ['operating_unit', 'Operating Unit *'], ['nama_supplier', 'Nama Supplier *'], ['alamat_kantor', 'Alamat Kantor'],
    ['no_telp', 'No. Telp'], ['nama_kontak', 'Nama Kontak'], ['jabatan', 'Jabatan'], ['no_pkp', 'No. PKP'],
    ['no_nib', 'No. NIB'], ['domisili', 'Domisili'], ['npwp', 'NPWP'], ['alamat_npwp', 'Alamat NPWP'],
    ['nama_bank', 'Nama Bank'], ['cabang_bank', 'Cabang Bank'], ['nama_rekening', 'Nama Rekening'], ['nomor_rekening', 'Nomor Rekening'],
  ];

  return createPortal(
    <div className="modal-overlay">
      <form onSubmit={submit} className="modal-container w-full max-w-2xl max-h-[90vh] flex flex-col">
        <div className="flex items-center justify-between p-5 border-b border-border bg-surface">
          <h2 className="text-lg font-bold text-dpbj-navy">Ajukan Setup Supplier Baru</h2>
          <button type="button" onClick={onClose} className="p-2 hover:bg-white rounded-xl"><X size={18} className="text-gray-500" /></button>
        </div>
        <div className="flex-1 overflow-y-auto p-5 grid grid-cols-2 gap-3">
          {REQUIRED.map(([key, label]) => (
            <div key={key}>
              <label className="block text-[11px] font-semibold text-muted mb-1">{label}</label>
              <input required={label.includes('*')} className="form-input w-full text-sm" value={form[key]} onChange={set(key)} />
            </div>
          ))}
          <div>
            <label className="block text-[11px] font-semibold text-muted mb-1">Tgl NIB</label>
            <input type="date" className="form-input w-full text-sm" value={form.tgl_nib} onChange={set('tgl_nib')} />
          </div>
          <div>
            <label className="block text-[11px] font-semibold text-muted mb-1">Mata Uang</label>
            <select className="form-input w-full text-sm" value={form.mata_uang} onChange={set('mata_uang')}>
              <option value="IDR">IDR</option><option value="USD">USD</option><option value="EUR">EUR</option>
            </select>
          </div>
          <div>
            <label className="block text-[11px] font-semibold text-muted mb-1">Nama Paket RUP (opsional)</label>
            <input className="form-input w-full text-sm" value={form.nama_paket_rup} onChange={set('nama_paket_rup')} />
          </div>
          <div>
            <label className="block text-[11px] font-semibold text-muted mb-1">Kode RUP (opsional)</label>
            <input className="form-input w-full text-sm" value={form.kode_rup} onChange={set('kode_rup')} />
          </div>
        </div>
        <div className="p-4 border-t border-border flex justify-end gap-2">
          <button type="button" onClick={onClose} className="btn-ghost text-sm">Batal</button>
          <button type="submit" disabled={saving} className="btn-primary text-sm disabled:opacity-50">{saving ? 'Mengirim...' : 'Ajukan Permintaan'}</button>
        </div>
      </form>
    </div>,
    document.body
  );
}

export default function OracleSupplierSetup() {
  const { user } = useApp();
  const [requests, setRequests] = useState([]);
  const [isLoading, setIsLoading] = useState(true);
  const [selected, setSelected] = useState(null);
  const [showNew, setShowNew] = useState(false);

  const fetchRequests = useCallback(async () => {
    setIsLoading(true);
    try {
      const res = await fetch(`${API_BASE}/oracle-supplier`, { headers: getAuthHeaders() });
      const json = await res.json();
      if (json.success) setRequests(json.data);
    } catch (err) {
      console.error(err);
    } finally {
      setIsLoading(false);
    }
  }, []);

  useEffect(() => { fetchRequests(); }, [fetchRequests]);

  const columns = COLUMNS_BY_ROLE[user?.role] || COLUMNS_BY_ROLE.admin;

  return (
    <div className="space-y-5 animate-fade-in">
      <div className="section-card">
        <div className="flex items-center justify-between flex-wrap gap-3">
          <div>
            <h2 className="text-base font-bold text-dpbj-navy flex items-center gap-2"><Landmark size={16} /> Setup Supplier Oracle</h2>
            <p className="text-xs text-muted">Alur permintaan setup supplier baru di Oracle EBS: Pengaju &rarr; Verifikator &rarr; Dispatcher &rarr; Pelaksana.</p>
          </div>
          {['admin', 'pengaju_oracle'].includes(user?.role) && (
            <button onClick={() => setShowNew(true)} className="btn-primary text-xs"><Plus size={14} /> Ajukan Baru</button>
          )}
        </div>
      </div>

      {isLoading ? (
        <div className="section-card text-center py-10 text-sm text-muted">Memuat data...</div>
      ) : (
        <div className={`grid grid-cols-1 gap-4 ${columns.length >= 4 ? 'md:grid-cols-4' : columns.length === 3 ? 'md:grid-cols-3' : 'md:grid-cols-2'}`}>
          {columns.map(col => {
            const items = requests.filter(r => col.statuses.includes(r.status) && (!col.onlyMine || r.assigned_to === user.id));
            return (
              <div key={col.label} className="section-card !p-3">
                <div className="flex items-center justify-between px-1 pb-2 mb-2 border-b border-border">
                  <h3 className="text-xs font-bold text-dpbj-navy uppercase tracking-wide">{col.label}</h3>
                  <span className="text-[10px] font-bold text-muted bg-surface px-1.5 py-0.5 rounded-full">{items.length}</span>
                </div>
                <div className="space-y-2 max-h-[560px] overflow-y-auto pr-1">
                  {items.length === 0 ? (
                    <p className="text-xs text-muted text-center py-6">Tidak ada permintaan.</p>
                  ) : items.map(r => <RequestCard key={r.id} req={r} onClick={() => setSelected(r)} />)}
                </div>
              </div>
            );
          })}
        </div>
      )}

      {selected && <DetailModal req={selected} user={user} onClose={() => setSelected(null)} onChanged={fetchRequests} />}
      <NewRequestModal isOpen={showNew} onClose={() => setShowNew(false)} onCreated={fetchRequests} />
    </div>
  );
}
