import { useState, useEffect, useCallback } from 'react';
import { RefreshCw, Upload, Download, FileSpreadsheet, ClipboardList, History, AlertCircle, CheckCircle2, XCircle, Loader2, CloudOff } from 'lucide-react';
import { API_BASE, getAuthHeaders } from '../context/AppContext';
import { toast } from '../lib/toast';

function formatTanggal(iso) {
  if (!iso) return '-';
  return new Date(iso).toLocaleString('id-ID', { dateStyle: 'medium', timeStyle: 'short' });
}
function formatRupiah(n) {
  if (n === null || n === undefined) return '-';
  return `Rp ${Number(n).toLocaleString('id-ID')}`;
}

const TABS = [
  { key: 'rka', label: 'RKA', icon: FileSpreadsheet },
  { key: 'pr', label: 'Purchase Requisition', icon: ClipboardList },
  { key: 'export', label: 'Ekspor Supplier & PO', icon: Download },
  { key: 'logs', label: 'Log Aktivitas', icon: History },
];

// ── Kotak upload manual - dipakai sama untuk tab RKA dan PR ──
function UploadBox({ label, uploadUrl, onDone }) {
  const [uploading, setUploading] = useState(false);
  const [message, setMessage] = useState(null); // { ok, text }

  const handleFile = async (e) => {
    const file = e.target.files[0];
    if (!file) return;
    setUploading(true);
    setMessage(null);
    try {
      const formData = new FormData();
      formData.append('file', file);
      const headers = getAuthHeaders();
      delete headers['Content-Type']; // biar browser set boundary multipart sendiri
      const res = await fetch(uploadUrl, { method: 'POST', headers, body: formData });
      const json = await res.json();
      setMessage({ ok: json.success, text: json.message });
      if (json.success) onDone();
    } catch {
      setMessage({ ok: false, text: 'Tidak bisa terhubung ke server.' });
    } finally {
      setUploading(false);
      e.target.value = '';
    }
  };

  return (
    <div className="bg-surface border border-dashed border-border rounded-xl p-4">
      <label className="flex items-center gap-2 text-xs font-semibold text-dpbj-navy cursor-pointer">
        <Upload size={14} />
        {label}
        <input type="file" accept=".xlsx" className="hidden" onChange={handleFile} disabled={uploading} />
        {uploading && <Loader2 size={13} className="animate-spin text-muted" />}
      </label>
      {message && (
        <p className={`text-xs mt-2 flex items-center gap-1.5 ${message.ok ? 'text-emerald-600' : 'text-red-500'}`}>
          {message.ok ? <CheckCircle2 size={12} /> : <XCircle size={12} />} {message.text}
        </p>
      )}
    </div>
  );
}

// ── Panel "Ambil dari Server SFTP" - kalau belum dikonfigurasi, tampilkan pesan jelas ──
function RemoteFetchPanel({ jenis, sftpConfigured }) {
  const [loading, setLoading] = useState(false);
  const [result, setResult] = useState(null);

  const check = async () => {
    setLoading(true);
    try {
      const res = await fetch(`${API_BASE}/integration/${jenis}/remote-list`, { headers: getAuthHeaders() });
      setResult(await res.json());
    } catch {
      setResult({ success: false, message: 'Tidak bisa terhubung ke server.' });
    } finally {
      setLoading(false);
    }
  };

  if (!sftpConfigured) {
    return (
      <div className="bg-amber-50 border border-amber-200 rounded-xl p-4 flex items-start gap-3">
        <CloudOff size={16} className="text-amber-600 flex-shrink-0 mt-0.5" />
        <div>
          <p className="text-xs font-semibold text-amber-800">Koneksi SFTP ke Oracle belum dikonfigurasi</p>
          <p className="text-xs text-amber-700 mt-1">Impor otomatis dari server belum bisa dipakai sampai kredensial SFTP diisi oleh tim IT. Sementara itu, gunakan tombol unggah file manual di atas.</p>
        </div>
      </div>
    );
  }

  return (
    <div className="bg-surface border border-border rounded-xl p-4">
      <button onClick={check} disabled={loading} className="btn-secondary text-xs">
        {loading ? <Loader2 size={13} className="animate-spin" /> : <RefreshCw size={13} />} Cek File di Server SFTP
      </button>
      {result && !result.success && <p className="text-xs text-red-500 mt-2">{result.message}</p>}
      {result?.success && (
        <ul className="text-xs text-dpbj-navy mt-2 space-y-1">
          {result.data.length === 0
            ? <li className="text-muted">Tidak ada file baru di server.</li>
            : result.data.map(f => <li key={f.name} className="font-mono">{f.name} ({Math.round(f.size / 1024)} Kb)</li>)
          }
        </ul>
      )}
    </div>
  );
}

export default function Integration() {
  const [activeTab, setActiveTab] = useState('rka');
  const [status, setStatus] = useState({ sftp_configured: false });
  const [rka, setRka] = useState([]);
  const [pr, setPr] = useState([]);
  const [logs, setLogs] = useState([]);
  const [isLoading, setIsLoading] = useState(true);

  const fetchStatus = useCallback(async () => {
    const res = await fetch(`${API_BASE}/integration/status`, { headers: getAuthHeaders() });
    const json = await res.json();
    if (json.success) setStatus(json.data);
  }, []);

  const fetchRka = useCallback(async () => {
    const res = await fetch(`${API_BASE}/integration/rka`, { headers: getAuthHeaders() });
    const json = await res.json();
    if (json.success) setRka(json.data);
  }, []);

  const fetchPr = useCallback(async () => {
    const res = await fetch(`${API_BASE}/integration/pr`, { headers: getAuthHeaders() });
    const json = await res.json();
    if (json.success) setPr(json.data);
  }, []);

  const fetchLogs = useCallback(async () => {
    const res = await fetch(`${API_BASE}/integration/logs`, { headers: getAuthHeaders() });
    const json = await res.json();
    if (json.success) setLogs(json.data);
  }, []);

  useEffect(() => {
    setIsLoading(true);
    Promise.all([fetchStatus(), fetchRka(), fetchPr(), fetchLogs()]).finally(() => setIsLoading(false));
  }, [fetchStatus, fetchRka, fetchPr, fetchLogs]);

  const handleExport = async (jenis) => {
    try {
      const res = await fetch(`${API_BASE}/integration/${jenis}/export`, { headers: getAuthHeaders() });
      if (!res.ok) { const j = await res.json().catch(() => ({})); toast('Gagal: ' + (j.message || res.statusText)); return; }
      const blob = await res.blob();
      const url = window.URL.createObjectURL(blob);
      const a = document.createElement('a');
      a.href = url;
      a.download = `${jenis}_export.xlsx`;
      document.body.appendChild(a);
      a.click();
      a.remove();
      window.URL.revokeObjectURL(url);
      fetchLogs();
    } catch {
      toast('Tidak bisa terhubung ke server.');
    }
  };

  return (
    <div className="space-y-5 animate-fade-in">
      <div className="section-card">
        <div className="flex items-center justify-between mb-1">
          <div>
            <h2 className="text-base font-bold text-dpbj-navy flex items-center gap-2"><RefreshCw size={16} /> Integrasi Oracle ERP</h2>
            <p className="text-xs text-muted">Sinkronisasi data anggaran (RKA), permintaan pembelian (PR), dan pengiriman data Supplier/PO ke sistem keuangan kampus.</p>
          </div>
          <span className={`badge text-[10px] ${status.sftp_configured ? 'bg-emerald-100 text-emerald-700' : 'bg-amber-100 text-amber-700'}`}>
            {status.sftp_configured ? 'SFTP Terhubung' : 'SFTP Belum Dikonfigurasi'}
          </span>
        </div>
      </div>

      <div className="section-card !p-0 overflow-hidden">
        <div className="tab-scroll-fade flex overflow-x-auto border-b border-border">
          {TABS.map(t => (
            <button
              key={t.key}
              onClick={() => setActiveTab(t.key)}
              className={`flex items-center gap-1.5 px-4 py-3 text-xs font-semibold whitespace-nowrap border-b-2 transition-colors ${activeTab === t.key ? 'border-dpbj-gold text-dpbj-navy' : 'border-transparent text-muted hover:text-dpbj-navy'}`}
            >
              <t.icon size={13} /> {t.label}
            </button>
          ))}
        </div>

        <div className="p-4">
          {isLoading ? (
            <p className="text-sm text-muted text-center py-8">Memuat data...</p>
          ) : activeTab === 'rka' ? (
            <div className="space-y-4">
              <div className="grid sm:grid-cols-2 gap-3">
                <UploadBox label="Unggah File RKA (.xlsx)" uploadUrl={`${API_BASE}/integration/rka/upload`} onDone={() => { fetchRka(); fetchLogs(); }} />
                <RemoteFetchPanel jenis="rka" sftpConfigured={status.sftp_configured} />
              </div>
              <div className="table-scroll">
                <table className="data-table">
                  <thead><tr><th>RKA Key</th><th>Tahun</th><th>Segmen 1</th><th>Segmen 2</th><th>Anggaran</th><th>Sisa</th><th>Diimpor</th></tr></thead>
                  <tbody className="stagger-list">
                    {rka.length === 0
                      ? <tr><td colSpan={7} className="py-8 text-center text-muted text-sm">Belum ada data RKA yang diimpor.</td></tr>
                      : rka.map(r => (
                        <tr key={r.id} className="stagger-item">
                          <td className="font-mono text-xs">{r.rka_key}</td>
                          <td>{r.start_date_year || '-'}</td>
                          <td className="text-xs">{r.segment1_desc || r.segment1 || '-'}</td>
                          <td className="text-xs">{r.segment2_desc || r.segment2 || '-'}</td>
                          <td className="text-xs">{formatRupiah(r.budget_amt)}</td>
                          <td className="text-xs">{formatRupiah(r.remain_amt)}</td>
                          <td className="text-xs text-muted">{formatTanggal(r.imported_at)}</td>
                        </tr>
                      ))
                    }
                  </tbody>
                </table>
              </div>
            </div>
          ) : activeTab === 'pr' ? (
            <div className="space-y-4">
              <div className="grid sm:grid-cols-2 gap-3">
                <UploadBox label="Unggah File PR (.xlsx)" uploadUrl={`${API_BASE}/integration/pr/upload`} onDone={() => { fetchPr(); fetchLogs(); }} />
                <RemoteFetchPanel jenis="pr" sftpConfigured={status.sftp_configured} />
              </div>
              <div className="table-scroll">
                <table className="data-table">
                  <thead><tr><th>No. Requisition</th><th>Deskripsi</th><th>Unit Kerja</th><th>Status</th><th>No. RUP</th><th>Jml Item</th><th>Diimpor</th></tr></thead>
                  <tbody className="stagger-list">
                    {pr.length === 0
                      ? <tr><td colSpan={7} className="py-8 text-center text-muted text-sm">Belum ada data PR yang diimpor.</td></tr>
                      : pr.map(p => (
                        <tr key={p.id} className="stagger-item">
                          <td className="font-mono text-xs">{p.requisition_number}</td>
                          <td className="text-xs max-w-xs truncate">{p.description || '-'}</td>
                          <td className="text-xs">{p.bu_name || '-'}</td>
                          <td className="text-xs">{p.document_status || '-'}</td>
                          <td className="text-xs">{p.nomor_rup || '-'}</td>
                          <td className="text-xs">{Array.isArray(p.lines) ? p.lines.length : 0}</td>
                          <td className="text-xs text-muted">{formatTanggal(p.imported_at)}</td>
                        </tr>
                      ))
                    }
                  </tbody>
                </table>
              </div>
            </div>
          ) : activeTab === 'export' ? (
            <div className="grid sm:grid-cols-2 gap-4">
              <div className="bg-surface border border-border rounded-xl p-5 text-center">
                <FileSpreadsheet size={28} className="text-dpbj-navy mx-auto mb-2" />
                <p className="text-sm font-semibold text-dpbj-navy mb-1">Data Supplier</p>
                <p className="text-xs text-muted mb-3">Ekspor seluruh vendor yang sudah terverifikasi ke format Excel, siap dikirim ke tim Oracle.</p>
                <button onClick={() => handleExport('supplier')} className="btn-secondary text-xs mx-auto">
                  <Download size={13} /> Unduh Excel Supplier
                </button>
              </div>
              <div className="bg-surface border border-border rounded-xl p-5 text-center">
                <FileSpreadsheet size={28} className="text-dpbj-navy mx-auto mb-2" />
                <p className="text-sm font-semibold text-dpbj-navy mb-1">Data PO (Purchase Order)</p>
                <p className="text-xs text-muted mb-3">Ekspor kontrak aktif/selesai ke format Excel, siap dikirim ke tim Oracle.</p>
                <button onClick={() => handleExport('po')} className="btn-secondary text-xs mx-auto">
                  <Download size={13} /> Unduh Excel PO
                </button>
              </div>
              <div className="sm:col-span-2 flex items-start gap-2 bg-blue-50 border border-blue-100 rounded-lg px-3 py-2.5 text-xs text-blue-700">
                <AlertCircle size={14} className="flex-shrink-0 mt-0.5" />
                File yang diunduh belum otomatis terkirim ke Oracle (butuh koneksi SFTP yang belum dikonfigurasi) - unduh lalu kirimkan manual ke tim Oracle/keuangan untuk saat ini.
              </div>
            </div>
          ) : (
            <div className="table-scroll">
              <table className="data-table">
                <thead><tr><th>Waktu</th><th>Jenis</th><th>Arah</th><th>File</th><th>Jml Baris</th><th>Status</th><th>Oleh</th></tr></thead>
                <tbody className="stagger-list">
                  {logs.length === 0
                    ? <tr><td colSpan={7} className="py-8 text-center text-muted text-sm">Belum ada aktivitas integrasi.</td></tr>
                    : logs.map(l => (
                      <tr key={l.id} className="stagger-item">
                        <td className="text-xs text-muted">{formatTanggal(l.created_at)}</td>
                        <td className="text-xs">{l.jenis}</td>
                        <td className="text-xs">{l.arah === 'masuk' ? 'Masuk (dari Oracle)' : 'Keluar (ke Oracle)'}</td>
                        <td className="font-mono text-xs">{l.file_name || '-'}</td>
                        <td className="text-xs">{l.jumlah_baris ?? '-'}</td>
                        <td>
                          {l.status === 'sukses'
                            ? <span className="flex items-center gap-1 text-xs text-emerald-600 font-semibold"><CheckCircle2 size={12} />Sukses</span>
                            : <span className="flex items-center gap-1 text-xs text-red-500 font-semibold"><XCircle size={12} />Gagal</span>
                          }
                        </td>
                        <td className="text-xs">{l.created_by_name || '-'}</td>
                      </tr>
                    ))
                  }
                </tbody>
              </table>
            </div>
          )}
        </div>
      </div>
    </div>
  );
}
