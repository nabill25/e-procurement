import { useState, useEffect, useCallback } from 'react';
import { ChevronLeft, ChevronRight, ShieldAlert, MoveHorizontal } from 'lucide-react';
import { API_BASE, useApp } from '../context/AppContext';

export default function Blacklist({ onNavigateHome }) {
  const { user, refreshTrigger } = useApp();
  const [search, setSearch] = useState('');
  const [perPage, setPerPage] = useState(10);
  const [page, setPage] = useState(1);
  const [data, setData] = useState([]);
  const [isLoading, setIsLoading] = useState(true);

  const fetchBlacklist = useCallback(async () => {
    setIsLoading(true);
    try {
      const res = await fetch(`${API_BASE}/blacklist`);
      const json = await res.json();
      if (json.success) {
        setData(json.data.map(row => ({
          id: row.id,
          nama: row.company_name,
          npwp: row.npwp,
          tanggal: row.start_date ? new Date(row.start_date).toLocaleDateString('id-ID') : '-',
          sk: row.sk_number || '-',
          sk_file_path: row.sk_file_path,
        })));
      }
    } catch (err) {
      console.error('Failed to fetch blacklist:', err);
    } finally {
      setIsLoading(false);
    }
  }, []);

  useEffect(() => {
    fetchBlacklist();
  }, [fetchBlacklist, refreshTrigger]);

  const filtered = data.filter(row =>
    row.nama?.toLowerCase().includes(search.toLowerCase()) ||
    row.npwp?.toLowerCase().includes(search.toLowerCase())
  );
  const totalPages = Math.max(1, Math.ceil(filtered.length / perPage));
  const paginated = filtered.slice((page - 1) * perPage, page * perPage);

  return (
    <div className="space-y-6 animate-fade-in">
      <div className="section-card">
        <div className="flex items-center gap-3 mb-5">
          <div className="w-10 h-10 rounded-xl bg-red-50 flex items-center justify-center">
            <ShieldAlert size={20} className="text-red-600" />
          </div>
          <div>
            <h2 className="text-base font-bold text-dpbj-navy">Daftar Hitam</h2>
            <p className="text-xs text-muted">Vendor yang sedang dikenai sanksi blacklist</p>
          </div>
        </div>

        {/* Controls row */}
        <div className="flex flex-col sm:flex-row sm:items-center justify-between gap-3 mb-4">
          <div className="flex items-center gap-2 text-xs text-muted">
            <span>Menampilkan</span>
            <select
              value={perPage}
              onChange={e => { setPerPage(Number(e.target.value)); setPage(1); }}
              className="border border-border rounded-lg px-2 py-1 text-xs text-dpbj-navy bg-white"
            >
              {[10, 25, 50, 100].map(n => <option key={n} value={n}>{n}</option>)}
            </select>
            <span>data</span>
          </div>
          <input
            value={search}
            onChange={e => { setSearch(e.target.value); setPage(1); }}
            placeholder="Cari nama vendor atau NPWP..."
            className="form-input text-sm w-full sm:w-64"
          />
        </div>

        <p className="table-scroll-hint">
          <MoveHorizontal size={13} /> Geser tabel ke kiri/kanan untuk lihat kolom lainnya
        </p>
        <div className="table-scroll">
          <table className="data-table">
            <thead>
              <tr>
                <th>Nama</th>
                <th>NPWP</th>
                <th>Tanggal</th>
                <th>SK</th>
              </tr>
            </thead>
            <tbody className="stagger-list">
              {isLoading ? (
                <tr>
                  <td colSpan={4} className="py-10 text-center text-muted text-sm">
                    Memuat data...
                  </td>
                </tr>
              ) : paginated.length === 0 ? (
                <tr>
                  <td colSpan={4} className="py-10 text-center text-muted text-sm">
                    Tidak ada vendor yang masuk daftar hitam.
                  </td>
                </tr>
              ) : (
                paginated.map((row) => (
                  <tr key={row.id} className="stagger-item">
                    <td className="font-medium text-dpbj-navy text-sm">{row.nama}</td>
                    <td className="font-mono text-xs text-dpbj-slate">{row.npwp}</td>
                    <td className="text-xs text-muted">{row.tanggal}</td>
                    {row.sk_file_path ? (
                      <td className="text-xs text-blue-600 hover:underline">
                        <a href={row.sk_file_path} target="_blank" rel="noreferrer">{row.sk}</a>
                      </td>
                    ) : (
                      <td className="text-xs text-muted">{row.sk}</td>
                    )}
                  </tr>
                ))
              )}
            </tbody>
          </table>
        </div>

        {/* Footer paginasi */}
        <div className="flex flex-col sm:flex-row sm:items-center justify-between gap-3 mt-4 pt-4 border-t border-border">
          <p className="text-xs text-muted">
            Menampilkan {filtered.length === 0 ? 0 : (page - 1) * perPage + 1} sampai {Math.min(page * perPage, filtered.length)} dari {filtered.length} data
          </p>
          <div className="flex items-center gap-1">
            <button
              onClick={() => setPage(p => Math.max(1, p - 1))}
              disabled={page === 1}
              className="w-8 h-8 border border-border rounded-lg flex items-center justify-center hover:bg-surface disabled:opacity-40 transition-colors"
            >
              <ChevronLeft size={14} />
            </button>
            <button
              onClick={() => setPage(p => Math.min(totalPages, p + 1))}
              disabled={page >= totalPages}
              className="w-8 h-8 border border-border rounded-lg flex items-center justify-center hover:bg-surface disabled:opacity-40 transition-colors"
            >
              <ChevronRight size={14} />
            </button>
          </div>
        </div>
      </div>
    </div>
  );
}
