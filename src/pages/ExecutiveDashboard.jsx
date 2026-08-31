import { useState, useEffect, useCallback } from 'react';
import { LayoutGrid, TrendingUp, TrendingDown, Building2, Filter } from 'lucide-react';
import { API_BASE, getAuthHeaders } from '../context/AppContext';
import { formatRupiah } from '../components/ui/shared';

const STATUS_LABEL = {
  diajukan: 'Diajukan', diverifikasi: 'Diverifikasi', revisi: 'Revisi', disetujui: 'Disetujui', ditolak: 'Ditolak',
  pengumuman: 'Pengumuman', pendaftaran: 'Pendaftaran', penawaran: 'Penawaran', evaluasi: 'Evaluasi',
  pemenang: 'Pemenang', masa_sanggah: 'Masa Sanggah', kontrak: 'Kontrak',
  aktif: 'Aktif', selesai: 'Selesai',
};

function statusGabungan(row) {
  // Padanan "status gabungan perencanaan dan pengadaan" di executive_report.php sistem lama -
  // satu label ringkas yang menunjukkan paket ini sudah sampai tahap mana dari RUP sampai kontrak.
  if (row.status_kontrak) return { label: `Kontrak: ${STATUS_LABEL[row.status_kontrak] || row.status_kontrak}`, tone: 'ok' };
  if (row.status_tender) return { label: `Tender: ${STATUS_LABEL[row.status_tender] || row.status_tender}`, tone: 'progress' };
  if (row.status_rup === 'ditolak') return { label: 'RUP Ditolak', tone: 'bad' };
  return { label: `RUP: ${STATUS_LABEL[row.status_rup] || row.status_rup}`, tone: 'progress' };
}

function StatusPill({ row }) {
  const s = statusGabungan(row);
  const toneClass = s.tone === 'ok' ? 'bg-emerald-100 text-emerald-700' : s.tone === 'bad' ? 'bg-red-100 text-red-700' : 'bg-blue-100 text-blue-700';
  return <span className={`badge text-[10px] ${toneClass}`}>{s.label}</span>;
}

export default function ExecutiveDashboard() {
  const [rows, setRows] = useState([]);
  const [efficiency, setEfficiency] = useState(null);
  const [isLoading, setIsLoading] = useState(true);
  const [tahun, setTahun] = useState('');
  const [page, setPage] = useState(1);
  const [pagination, setPagination] = useState({ total: 0 });

  const fetchSummary = useCallback(async () => {
    setIsLoading(true);
    try {
      const params = new URLSearchParams({ page, limit: 20 });
      if (tahun) params.set('tahun', tahun);
      const [summaryRes, effRes] = await Promise.all([
        fetch(`${API_BASE}/dashboard/executive-summary?${params}`, { headers: getAuthHeaders() }),
        fetch(`${API_BASE}/dashboard/efficiency${tahun ? `?tahun=${tahun}` : ''}`, { headers: getAuthHeaders() }),
      ]);
      const summaryJson = await summaryRes.json();
      const effJson = await effRes.json();
      if (summaryJson.success) { setRows(summaryJson.data); setPagination(summaryJson.pagination); }
      if (effJson.success) setEfficiency(effJson.data);
    } catch (err) {
      console.error(err);
    } finally {
      setIsLoading(false);
    }
  }, [page, tahun]);

  useEffect(() => { fetchSummary(); }, [fetchSummary]);

  const overall = efficiency?.overall;
  const efisiensiPositif = overall?.total_efisiensi !== null && overall?.total_efisiensi >= 0;

  return (
    <div className="space-y-5 animate-fade-in">
      <div className="section-card">
        <h2 className="text-base font-bold text-dpbj-navy flex items-center gap-2"><LayoutGrid size={16} /> Dashboard Pimpinan</h2>
        <p className="text-xs text-muted">Ringkasan portofolio pengadaan lintas tahap (RUP &rarr; Tender &rarr; Kontrak) dan efisiensi anggaran, untuk peninjauan cepat tanpa membuka detail satu per satu.</p>
      </div>

      {/* Kartu efisiensi anggaran - padanan setHPSVal() di dashboard_json.php sistem lama */}
      <div className="grid grid-cols-1 sm:grid-cols-4 gap-4">
        <div className="section-card">
          <p className="text-[10px] uppercase tracking-wide text-muted font-semibold mb-1">Jumlah Kontrak</p>
          <p className="text-xl font-extrabold text-dpbj-navy">{overall?.jumlah_paket ?? 0}</p>
        </div>
        <div className="section-card">
          <p className="text-[10px] uppercase tracking-wide text-muted font-semibold mb-1">Total HPS</p>
          <p className="text-lg font-extrabold text-dpbj-navy">{formatRupiah(overall?.total_hps || 0, true)}</p>
        </div>
        <div className="section-card">
          <p className="text-[10px] uppercase tracking-wide text-muted font-semibold mb-1">Total Nilai Kontrak</p>
          <p className="text-lg font-extrabold text-dpbj-navy">{formatRupiah(overall?.total_kontrak || 0, true)}</p>
        </div>
        <div className="section-card">
          <p className="text-[10px] uppercase tracking-wide text-muted font-semibold mb-1 flex items-center gap-1">
            {efisiensiPositif ? <TrendingUp size={11} className="text-emerald-600" /> : <TrendingDown size={11} className="text-red-500" />} Efisiensi Pengadaan
          </p>
          <p className={`text-lg font-extrabold ${efisiensiPositif ? 'text-emerald-600' : 'text-red-500'}`}>{formatRupiah(overall?.total_efisiensi || 0, true)}</p>
        </div>
      </div>

      {/* Efisiensi per unit kerja */}
      {efficiency?.by_unit?.length > 0 && (
        <div className="section-card">
          <h3 className="text-sm font-bold text-dpbj-navy mb-3 flex items-center gap-2"><Building2 size={14} /> Efisiensi per Unit Kerja</h3>
          <div className="table-scroll">
            <table className="data-table">
              <thead><tr><th>Unit Kerja</th><th>Jml Paket</th><th>Total HPS</th><th>Total Kontrak</th><th>Efisiensi</th></tr></thead>
              <tbody className="stagger-list">
                {efficiency.by_unit.map((u, i) => (
                  <tr key={i} className="stagger-item">
                    <td className="text-xs font-semibold text-dpbj-navy">{u.unit_kerja}</td>
                    <td className="text-xs">{u.jumlah_paket}</td>
                    <td className="text-xs">{formatRupiah(u.total_hps)}</td>
                    <td className="text-xs">{formatRupiah(u.total_kontrak)}</td>
                    <td className={`text-xs font-semibold ${u.total_efisiensi >= 0 ? 'text-emerald-600' : 'text-red-500'}`}>{formatRupiah(u.total_efisiensi)}</td>
                  </tr>
                ))}
              </tbody>
            </table>
          </div>
        </div>
      )}

      {/* Rekap portofolio per paket */}
      <div className="section-card">
        <div className="flex items-center justify-between mb-3 flex-wrap gap-2">
          <h3 className="text-sm font-bold text-dpbj-navy">Portofolio Pengadaan</h3>
          <div className="flex items-center gap-2">
            <Filter size={13} className="text-muted" />
            <select value={tahun} onChange={e => { setTahun(e.target.value); setPage(1); }} className="text-xs p-1.5 border border-gray-300 rounded-lg">
              <option value="">Semua Tahun</option>
              {[2024, 2025, 2026].map(y => <option key={y} value={y}>{y}</option>)}
            </select>
          </div>
        </div>
        <div className="table-scroll">
          <table className="data-table">
            <thead>
              <tr><th>No. Pengajuan</th><th>Judul</th><th>Unit Kerja</th><th>Pagu RUP</th><th>HPS</th><th>Nilai Kontrak</th><th>Status</th></tr>
            </thead>
            <tbody className="stagger-list">
              {isLoading ? (
                <tr><td colSpan={7} className="py-10 text-center text-muted text-sm">Memuat data...</td></tr>
              ) : rows.length === 0 ? (
                <tr><td colSpan={7} className="py-10 text-center text-muted text-sm">Tidak ada data pengajuan.</td></tr>
              ) : rows.map(row => (
                <tr key={row.request_id} className="stagger-item">
                  <td className="font-mono text-xs">{row.request_number}</td>
                  <td className="text-xs max-w-xs truncate">{row.title}</td>
                  <td className="text-xs">{row.unit_kerja || '-'}</td>
                  <td className="text-xs">{formatRupiah(row.pagu_rup)}</td>
                  <td className="text-xs">{row.hps ? formatRupiah(row.hps) : '-'}</td>
                  <td className="text-xs">{row.contract_value ? formatRupiah(row.contract_value) : '-'}</td>
                  <td><StatusPill row={row} /></td>
                </tr>
              ))}
            </tbody>
          </table>
        </div>
        {pagination.total > 20 && (
          <div className="flex items-center justify-between mt-3 text-xs text-muted">
            <span>Halaman {page} dari {Math.ceil(pagination.total / 20)} ({pagination.total} total)</span>
            <div className="flex gap-2">
              <button disabled={page <= 1} onClick={() => setPage(p => p - 1)} className="btn-secondary text-xs disabled:opacity-40">Sebelumnya</button>
              <button disabled={page >= Math.ceil(pagination.total / 20)} onClick={() => setPage(p => p + 1)} className="btn-secondary text-xs disabled:opacity-40">Berikutnya</button>
            </div>
          </div>
        )}
      </div>
    </div>
  );
}
