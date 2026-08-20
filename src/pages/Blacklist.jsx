import { useState } from 'react';
import { ChevronUp, ChevronDown, ChevronLeft, ChevronRight, Home } from 'lucide-react';
import { useApp } from '../context/AppContext';

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

// Static data matching the real blacklist structure
const BLACKLIST_DATA = [
  // Currently empty as in the real site - but we can pre-populate for demo:
  // { id: 1, nama: 'PT Pembangunan Semesta', npwp: '09.876.543.2-109.000', tanggal: '2024-01-15', sk: 'SK/001/DPBJ/2024' },
];

function SortIcon() {
  return (
    <span className="inline-flex flex-col ml-1 opacity-40">
      <ChevronUp size={10} />
      <ChevronDown size={10} className="-mt-1" />
    </span>
  );
}

export default function Blacklist({ onNavigateHome }) {
  const { user } = useApp();
  const [search, setSearch] = useState('');
  const [perPage, setPerPage] = useState(10);
  const [page, setPage] = useState(1);

  const filtered = BLACKLIST_DATA.filter(row =>
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
        <div className="flex items-center justify-between px-5 py-3 bg-gray-50 border-b border-border">
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
          <div className="flex items-center gap-2 text-xs">
            <span className="text-muted">Pencarian :</span>
            <input
              value={search}
              onChange={e => { setSearch(e.target.value); setPage(1); }}
              className="border border-border rounded px-2 py-1 text-xs text-dpbj-navy focus:outline-none focus:ring-1 focus:ring-dpbj-gold/40 w-36"
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
              {paginated.length === 0 ? (
                <tr>
                  <td colSpan={4} className="py-10 text-center text-muted text-sm">
                    Data tidak ditemukan.
                  </td>
                </tr>
              ) : (
                paginated.map((row, idx) => (
                  <tr key={idx} className="border-b border-gray-100 hover:bg-blue-50/20 transition-colors">
                    <td className="px-4 py-3 font-medium text-dpbj-navy text-sm">{row.nama}</td>
                    <td className="px-4 py-3 font-mono text-xs text-dpbj-slate">{row.npwp}</td>
                    <td className="px-4 py-3 text-xs text-muted">{row.tanggal}</td>
                    <td className="px-4 py-3 text-xs text-blue-600 hover:underline cursor-pointer">{row.sk}</td>
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
