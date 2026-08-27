import { useState, useEffect, useCallback } from 'react';
import { AlertTriangle, Send, History, MoveHorizontal } from 'lucide-react';
import { API_BASE, getAuthHeaders } from '../context/AppContext';

function formatTanggal(iso) {
  if (!iso) return '-';
  return new Date(iso).toLocaleDateString('id-ID', { dateStyle: 'medium' });
}

export default function DocumentExpiry() {
  const [dokumen, setDokumen] = useState([]);
  const [logs, setLogs] = useState([]);
  const [hari, setHari] = useState(30);
  const [isLoading, setIsLoading] = useState(true);
  const [sendingId, setSendingId] = useState(null);
  const [showLogs, setShowLogs] = useState(false);

  const fetchDokumen = useCallback(async () => {
    setIsLoading(true);
    try {
      const res = await fetch(`${API_BASE}/master/dokumen-expired?hari=${hari}`, { headers: getAuthHeaders() });
      const json = await res.json();
      if (json.success) setDokumen(json.data);
    } catch (err) {
      console.error(err);
    } finally {
      setIsLoading(false);
    }
  }, [hari]);

  const fetchLogs = useCallback(async () => {
    try {
      const res = await fetch(`${API_BASE}/master/dokumen-expired/logs`, { headers: getAuthHeaders() });
      const json = await res.json();
      if (json.success) setLogs(json.data);
    } catch (err) {
      console.error(err);
    }
  }, []);

  useEffect(() => { fetchDokumen(); }, [fetchDokumen]);
  useEffect(() => { if (showLogs) fetchLogs(); }, [showLogs, fetchLogs]);

  const handleNotify = async (docId) => {
    setSendingId(docId);
    try {
      const res = await fetch(`${API_BASE}/master/dokumen-expired/${docId}/notify`, { method: 'POST', headers: getAuthHeaders() });
      const json = await res.json();
      if (json.success) alert(json.message);
      else alert('Gagal: ' + json.message);
    } catch {
      alert('Terjadi kesalahan saat mencatat notifikasi.');
    } finally {
      setSendingId(null);
    }
  };

  return (
    <div className="space-y-6 animate-fade-in">
      <div className="section-card">
        <div className="flex items-center justify-between mb-5 flex-wrap gap-3">
          <div className="flex items-center gap-3">
            <div className="w-10 h-10 rounded-xl bg-surface flex items-center justify-center">
              <AlertTriangle size={20} className="text-amber-600" />
            </div>
            <div>
              <h2 className="text-base font-bold text-dpbj-navy">Dokumen Vendor Akan Kedaluwarsa</h2>
              <p className="text-xs text-muted">Pemantauan dokumen legalitas vendor yang mendekati atau sudah lewat tanggal berlaku</p>
            </div>
          </div>
          <div className="flex items-center gap-2">
            <select value={hari} onChange={e => setHari(e.target.value)} className="text-xs p-2 border border-gray-300 rounded-lg">
              <option value={7}>7 hari ke depan</option>
              <option value={30}>30 hari ke depan</option>
              <option value={90}>90 hari ke depan</option>
            </select>
            <button onClick={() => setShowLogs(!showLogs)} className="btn-secondary text-xs flex items-center gap-1.5">
              <History size={13} /> Riwayat Notifikasi
            </button>
          </div>
        </div>

        <div className="text-[11px] text-amber-700 bg-amber-50 border border-amber-100 rounded-lg px-3 py-2 mb-4">
          Catatan: pengiriman email sungguhan belum aktif (belum ada konfigurasi SMTP di sistem ini). Tombol "Catat Notifikasi" hanya mencatat riwayatnya untuk sekarang.
        </div>

        {showLogs ? (
          <>
            <p className="table-scroll-hint">
              <MoveHorizontal size={13} /> Geser tabel ke kiri/kanan untuk lihat kolom lainnya
            </p>
            <div className="table-scroll">
              <table className="data-table">
                <thead><tr><th>Vendor</th><th>Jenis Dokumen</th><th>Jumlah Kirim</th><th>Terakhir Dikirim</th></tr></thead>
                <tbody>
                  {logs.length === 0 ? (
                    <tr><td colSpan={4} className="py-8 text-center text-muted text-sm">Belum ada riwayat notifikasi.</td></tr>
                  ) : logs.map(l => (
                    <tr key={l.id}>
                      <td className="text-sm font-medium text-dpbj-navy">{l.vendor_name}</td>
                      <td className="text-xs text-muted">{l.doc_type || '-'}</td>
                      <td className="text-xs text-muted">{l.sent_count}x</td>
                      <td className="text-xs text-muted">{formatTanggal(l.last_sent_at)}</td>
                    </tr>
                  ))}
                </tbody>
              </table>
            </div>
          </>
        ) : (
          <>
            <p className="table-scroll-hint">
              <MoveHorizontal size={13} /> Geser tabel ke kiri/kanan untuk lihat kolom lainnya
            </p>
            <div className="table-scroll">
            <table className="data-table">
              <thead>
                <tr>
                  <th>Vendor</th>
                  <th>Jenis Dokumen</th>
                  <th>Nomor</th>
                  <th>Tanggal Berlaku Sampai</th>
                  <th>Sisa Hari</th>
                  <th className="text-right">Aksi</th>
                </tr>
              </thead>
              <tbody>
                {isLoading ? (
                  <tr><td colSpan={6} className="py-10 text-center text-muted text-sm">Memuat data...</td></tr>
                ) : dokumen.length === 0 ? (
                  <tr><td colSpan={6} className="py-10 text-center text-muted text-sm">Tidak ada dokumen yang mendekati kedaluwarsa dalam rentang ini.</td></tr>
                ) : dokumen.map(d => (
                  <tr key={d.id}>
                    <td className="text-sm font-medium text-dpbj-navy">{d.vendor_name}</td>
                    <td className="text-xs text-muted">{d.doc_type}</td>
                    <td className="text-xs text-muted">{d.doc_number || '-'}</td>
                    <td className="text-xs text-muted">{formatTanggal(d.expiry_date)}</td>
                    <td>
                      <span className={`badge text-[10px] ${d.sisa_hari < 0 ? 'bg-red-100 text-red-700' : d.sisa_hari <= 7 ? 'bg-amber-100 text-amber-700' : 'bg-gray-100 text-gray-600'}`}>
                        {d.sisa_hari < 0 ? `Lewat ${Math.abs(d.sisa_hari)} hari` : `${d.sisa_hari} hari lagi`}
                      </span>
                    </td>
                    <td className="text-right">
                      <button onClick={() => handleNotify(d.id)} disabled={sendingId === d.id} className="text-xs font-semibold text-blue-600 hover:underline flex items-center gap-1 ml-auto disabled:opacity-50">
                        <Send size={12} /> {sendingId === d.id ? 'Mencatat...' : 'Catat Notifikasi'}
                      </button>
                    </td>
                  </tr>
                ))}
              </tbody>
            </table>
            </div>
          </>
        )}
      </div>
    </div>
  );
}
