import { useState, useEffect, useCallback } from 'react';
import { ChevronUp, ChevronDown, ChevronLeft, ChevronRight, Home } from 'lucide-react';
import { API_BASE, useApp } from '../context/AppContext';

function Breadcrumb({ onHome }) {
  return (
    <nav className="flex items-center gap-2 text-xs text-muted mb-4">
      <button onClick={onHome} className="text-dpbj-gold hover:underline flex items-center gap-1">
        <Home size={11} /> Home
      </button>
      <span>/</span>
      <span className="text-dpbj-navy font-medium">Daftar Hitam</span>
    </nav>
  );
}

function SortIcon() {
  return (
    <span className="inline-flex flex-col ml-1 opacity-40">
      <ChevronUp size={10} />
      <ChevronDown size={10} className="-mt-1" />
    </span>
  );
}

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
    <div className="space-y-4 animate-fade-in">
      {onNavigateHome && <Breadcrumb onHome={onNavigateHome} />}

      <div className="bg-white rounded-xl border border-border shadow-card overflow-hidden">
        {/* Header */}
        <div className="flex items-center justify-between px-5 py-4 border-b border-border">
          <h2 className="font-bold text-dpbj-navy text-base">
            Daftar <span className="font-normal">Hitam</span>
          </h2>
          {/* Expand icon */}
          <button className="text-muted hover:text-dpbj-navy transition-colors text-xs border border-border rounded px-1.5 py-0.5">⤢</button>
        </div>

        {/* Controls row */}
        <div className="flex flex-wrap items-center justify-between gap-3 px-5 py-3 bg-gray-50 border-b border-border">
          <div className="flex items-center gap-2 text-xs text-muted">
            <span>Menampilkan</span>
            <select
              value={perPage}
              onChange={e => { setPerPage(Number(e.target.value)); setPage(1); }}
              className="border border-border rounded px-1.5 py-0.5 text-xs text-dpbj-navy bg-white"
            >
              {[10, 25, 50, 100].map(n => <option key={n} value={n}>{n}</option>)}
            </select>
            <span>data</span>
          </div>
          <div className="flex items-center gap-2 text-xs w-full sm:w-auto">
            <span className="text-muted shrink-0">Pencarian :</span>
            <input
              value={search}
              onChange={e => { setSearch(e.target.value); setPage(1); }}
              className="border border-border rounded px-2 py-1 text-xs text-dpbj-navy focus:outline-none focus:ring-1 focus:ring-dpbj-gold/40 w-full sm:w-36"
            />
          </div>
        </div>

        {/* Table */}
        <div className="overflow-x-auto">
          <table className="w-full text-sm">
            <thead>
              <tr className="bg-gray-50 border-b border-border">
                <th className="px-4 py-3 text-left text-xs font-semibold text-dpbj-navy/70">
                  Nama <SortIcon />
                </th>
                <th className="px-4 py-3 text-left text-xs font-semibold text-dpbj-navy/70">
                  NPWP <SortIcon />
                </th>
                <th className="px-4 py-3 text-left text-xs font-semibold text-dpbj-navy/70">
                  Tanggal <SortIcon />
                </th>
                <th className="px-4 py-3 text-left text-xs font-semibold text-dpbj-navy/70">
                  SK <SortIcon />
                </th>
              </tr>
            </thead>
            <tbody>
              {isLoading ? (
                <tr>
                  <td colSpan={4} className="py-10 text-center text-muted text-sm">
                    Memuat data...
                  </td>
                </tr>
              ) : paginated.length === 0 ? (
                <tr>
                  <td colSpan={4} className="py-10 text-center text-muted text-sm">
                    Data tidak ditemukan.
                  </td>
                </tr>
              ) : (
                paginated.map((row) => (
                  <tr key={row.id} className="border-b border-gray-100 hover:bg-blue-50/20 transition-colors">
                    <td className="px-4 py-3 font-medium text-dpbj-navy text-sm">{row.nama}</td>
                    <td className="px-4 py-3 font-mono text-xs text-dpbj-slate">{row.npwp}</td>
                    <td className="px-4 py-3 text-xs text-muted">{row.tanggal}</td>
                    {row.sk_file_path ? (
                      <td className="px-4 py-3 text-xs text-blue-600 hover:underline cursor-pointer">
                        <a href={row.sk_file_path} target="_blank" rel="noreferrer">{row.sk}</a>
                      </td>
                    ) : (
                      <td className="px-4 py-3 text-xs text-muted">{row.sk}</td>
                    )}
                  </tr>
                ))
              )}
            </tbody>
          </table>
        </div>

        {/* Footer */}
        <div className="flex items-center justify-between px-5 py-3 bg-gray-50 border-t border-border">
          <p className="text-xs text-muted">
            Menampilkan {filtered.length === 0 ? 0 : (page - 1) * perPage + 1} sampai {Math.min(page * perPage, filtered.length)} dari {filtered.length} data
          </p>
          <div className="flex items-center gap-1">
            <button
              onClick={() => setPage(p => Math.max(1, p - 1))}
              disabled={page === 1}
              className="w-7 h-7 border border-border rounded flex items-center justify-center hover:bg-surface disabled:opacity-40 transition-colors"
            >
              <ChevronLeft size={12} />
            </button>
            <button
              onClick={() => setPage(p => Math.min(totalPages, p + 1))}
              disabled={page >= totalPages}
              className="w-7 h-7 border border-border rounded flex items-center justify-center hover:bg-surface disabled:opacity-40 transition-colors"
            >
              <ChevronRight size={12} />
            </button>
          </div>
        </div>
      </div>
    </div>
  );
}
