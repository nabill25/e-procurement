import { useState, useEffect, useCallback } from 'react';
import { Search, Eye, Download, Calendar, MoveHorizontal } from 'lucide-react';
import { useApp, API_BASE } from '../../context/AppContext';
import { methodConfig } from '../../data/mockData';
import { tenderStatusConfig } from '../../data/procurementPhases';
import { StatusBadge, formatRupiah } from '../ui/shared';
import clsx from 'clsx';

// Nilai & urutan ini mengikuti PERSIS status tender asli (lihat tenderStatusConfig / alur
// tahapan di procurementPhases.js) - versi sebelumnya pakai nilai peninggalan mock data lama
// (draft/proses_review/tender_buka/evaluasi/selesai) yang TIDAK PERNAH cocok dengan status
// asli tersimpan di database, jadi filter status di tabel ini sebelumnya nyaris selalu
// mengembalikan hasil kosong untuk pilihan manapun selain "Semua Status".
const STATUS_OPTIONS = [
  { value: '',             label: 'Semua Status' },
  { value: 'draft',        label: 'Draft' },
  { value: 'pengumuman',   label: 'Pengumuman' },
  { value: 'pendaftaran',  label: 'Pendaftaran' },
  { value: 'penawaran',    label: 'Upload Penawaran' },
  { value: 'evaluasi',     label: 'Evaluasi' },
  { value: 'pemenang',     label: 'Penetapan Pemenang' },
  { value: 'masa_sanggah', label: 'Masa Sanggah' },
  { value: 'kontrak',      label: 'Kontrak & BAST' },
  { value: 'dibatalkan',   label: 'Dibatalkan' },
];

const METHOD_OPTIONS = [
  { value: '', label: 'Semua Metode' },
  { value: 'tender',              label: 'Tender' },
  { value: 'tender_cepat',        label: 'Tender Cepat' },
  { value: 'seleksi',             label: 'Seleksi' },
  { value: 'pengadaan_langsung',  label: 'Pengadaan Langsung' },
  { value: 'penunjukan_langsung', label: 'Penunjukan Langsung' },
  { value: 'e_purchasing',        label: 'E-Purchasing' },
];

export default function TenderTable({ compact = false }) {
  const { refreshTrigger, setSelectedTender } = useApp();
  const [tenders, setTenders]       = useState([]);
  const [search, setSearch]         = useState('');
  const [statusFilter, setStatus]   = useState('');
  const [methodFilter, setMethod]   = useState('');
  const [page, setPage]             = useState(1);
  const [totalPages, setTotalPages] = useState(1);
  const [totalItems, setTotalItems] = useState(0);
  const [isLoading, setIsLoading]   = useState(true);
  const perPage = compact ? 5 : 7;

  const fetchTenders = useCallback(async () => {
    setIsLoading(true);
    try {
      let url = `${API_BASE}/tenders?page=${page}&limit=${perPage}`;
      if (statusFilter) url += `&status=${statusFilter}`;
      if (methodFilter) url += `&method=${methodFilter}`;
      if (search)       url += `&search=${search}`;

      const res = await fetch(url);
      const json = await res.json();
      if (json.success) {
        setTenders(json.data);
        setTotalPages(json.pagination.pages);
        setTotalItems(json.pagination.total);
      }
    } catch (err) {
      console.error('Error fetching tenders:', err);
    } finally {
      setIsLoading(false);
    }
  }, [page, perPage, statusFilter, methodFilter, search]);

  useEffect(() => {
    fetchTenders();
  }, [fetchTenders, refreshTrigger]);

  const handleSearch = (e) => {
    setSearch(e.target.value);
    setPage(1); // reset to page 1 on filter
  };

  return (
    <div className="section-card">
      {/* Table Header */}
      <div className="flex flex-wrap items-center gap-3 mb-4">
        <div>
          <h2 className="text-base font-bold text-dpbj-navy">Daftar Paket Pengadaan</h2>
          <p className="text-xs text-muted">{totalItems} paket ditemukan · TA 2025</p>
        </div>
        <div className="flex-1 flex flex-wrap items-center gap-2 justify-end">
          {/* Search */}
          <div className="flex items-center gap-2 bg-surface border border-border rounded-xl px-3 py-2 focus-within:border-dpbj-gold focus-within:ring-2 focus-within:ring-dpbj-gold/20 transition-all">
            <Search size={13} className="text-gray-400 flex-shrink-0" />
            <input
              value={search}
              onChange={handleSearch}
              placeholder="Cari paket..."
              className="bg-transparent text-sm text-dpbj-navy placeholder:text-gray-400 focus:outline-none w-40"
            />
          </div>
          {/* Status filter */}
          <select
            value={statusFilter}
            onChange={e => { setStatus(e.target.value); setPage(1); }}
            className="form-select text-xs py-2 w-40"
          >
            {STATUS_OPTIONS.map(o => <option key={o.value} value={o.value}>{o.label}</option>)}
          </select>
          {/* Method filter */}
          <select
            value={methodFilter}
            onChange={e => { setMethod(e.target.value); setPage(1); }}
            className="form-select text-xs py-2 w-44"
          >
            {METHOD_OPTIONS.map(o => <option key={o.value} value={o.value}>{o.label}</option>)}
          </select>
          <button onClick={() => {
            import('../../utils/export').then(({ exportToCSV }) => {
              const columnMapping = {
                tender_number: "No. Paket",
                title: "Nama Paket",
                category: "Kategori",
                method: "Metode",
                pagu_anggaran: "Pagu Anggaran",
                status: "Status",
                submission_deadline: "Deadline"
              };
              exportToCSV(tenders, 'Data_Tender', columnMapping);
            });
          }} className="btn-ghost py-2 px-3 text-xs hover:bg-surface transition-colors active:scale-95">
            <Download size={13} />
            Export
          </button>
        </div>
      </div>

      {/* Table */}
      <p className="table-scroll-hint">
        <MoveHorizontal size={13} /> Geser tabel ke kiri/kanan untuk lihat kolom lainnya
      </p>
      <div className="table-scroll">
        <table className="data-table">
          <thead>
            <tr>
              <th className="px-4 py-3 text-left">No. Paket</th>
              <th className="px-4 py-3 text-left">Nama Paket</th>
              <th className="px-4 py-3 text-left">Metode</th>
              <th className="px-4 py-3 text-left">Pagu Anggaran</th>
              <th className="px-4 py-3 text-left">Status</th>
              <th className="px-4 py-3 text-left">Deadline</th>
              <th className="px-4 py-3 text-left">Aksi</th>
            </tr>
          </thead>
          <tbody className="stagger-list">
            {isLoading ? (
              <tr><td colSpan={7} className="py-12 text-center text-muted text-sm">Memuat data...</td></tr>
            ) : tenders.length === 0 ? (
              <tr>
                <td colSpan={7} className="py-12 text-center text-muted text-sm">
                  Tidak ada paket yang sesuai filter.
                </td>
              </tr>
            ) : tenders.map(tender => (
              // BUG FIX (ditemukan 2026-09-03 lewat tes mobile): baris ini sebelumnya cuma
              // menyalakan highlight kuning tanpa tujuan (activeRow), padahal aksi
              // "Lihat Detail" sungguhan ada di tombol Eye di kolom paling kanan - di layar
              // mobile kolom itu tersembunyi di luar layar (harus geser tabel dulu). Sekarang
              // klik baris manapun langsung buka detail, sama seperti pola di Vendor.jsx.
              <tr
                key={tender.id}
                className="stagger-item cursor-pointer hover:bg-surface"
                onClick={() => setSelectedTender(tender)}
              >
                <td>
                  <span className="font-mono text-xs text-dpbj-slate font-semibold">{tender.tender_number}</span>
                </td>
                <td>
                  <div className="max-w-xs">
                    <p className="font-semibold text-dpbj-navy text-sm leading-snug line-clamp-2">{tender.title}</p>
                    <p className="text-xs text-muted mt-0.5">{tender.category || 'N/A'}</p>
                  </div>
                </td>
                <td>
                  <span className="text-xs bg-dpbj-navy/5 text-dpbj-slate font-medium px-2 py-1 rounded-lg">
                    {methodConfig[tender.method] || tender.method}
                  </span>
                </td>
                <td>
                  <p className="text-sm font-semibold text-dpbj-navy">{formatRupiah(tender.pagu_anggaran, true)}</p>
                  {tender.hps && <p className="text-xs text-muted">HPS: {formatRupiah(tender.hps, true)}</p>}
                </td>
                <td><StatusBadge status={tender.status} config={tenderStatusConfig} /></td>
                <td>
                  {tender.submission_deadline ? (
                    <div className="flex items-center gap-1.5 text-xs text-muted">
                      <Calendar size={11} />
                      {new Date(tender.submission_deadline).toLocaleDateString('id-ID')}
                    </div>
                  ) : <span className="text-xs text-gray-300">—</span>}
                </td>
                <td>
                  <div className="flex items-center gap-1">
                    {/* Tombol titik-tiga dekoratif (tanpa aksi apapun) yang sebelumnya ada di
                        sini sudah dihapus - dicatat sejak lama sebagai tombol tanpa fungsi,
                        sekarang dibereskan sekalian waktu memperbaiki bug status di file ini. */}
                    <button
                      onClick={(e) => { e.stopPropagation(); setSelectedTender(tender); }}
                      className="p-1.5 rounded-lg hover:bg-dpbj-gold-faint hover:text-dpbj-gold-dark transition-colors"
                      title="Lihat Detail Tender"
                    >
                      <Eye size={14} />
                    </button>
                  </div>
                </td>
              </tr>
            ))}
          </tbody>
        </table>
      </div>

      {/* Pagination */}
      {totalPages > 1 && (
        <div className="flex items-center justify-between mt-4">
          <p className="text-xs text-muted">
            Menampilkan halaman {page} dari {totalPages}
          </p>
          <div className="flex items-center gap-1">
            <button
              onClick={() => setPage(p => Math.max(1, p - 1))}
              disabled={page === 1}
              className="px-3 py-1.5 rounded-lg text-xs font-medium border border-border hover:bg-surface disabled:opacity-40 transition-colors"
            >Sebelumnya</button>
            {Array.from({ length: totalPages }, (_, i) => i + 1).map(p => (
              <button
                key={p}
                onClick={() => setPage(p)}
                className={clsx('w-8 h-8 rounded-lg text-xs font-semibold transition-colors',
                  p === page ? 'gold-gradient text-dpbj-navy-dark shadow-sm' : 'hover:bg-surface text-muted border border-border')}
              >{p}</button>
            ))}
            <button
              onClick={() => setPage(p => Math.min(totalPages, p + 1))}
              disabled={page === totalPages}
              className="px-3 py-1.5 rounded-lg text-xs font-medium border border-border hover:bg-surface disabled:opacity-40 transition-colors"
            >Berikutnya</button>
          </div>
        </div>
      )}
    </div>
  );
}
