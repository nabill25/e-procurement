import { useState, useEffect, useCallback } from 'react';
import { Calendar, DollarSign, MapPin, Tag, Search, ChevronLeft, ChevronRight, Home } from 'lucide-react';
import { API_BASE, useApp } from '../context/AppContext';
import { methodConfig } from '../data/mockData';
import { procurementPhases, getTenderPhaseIndex } from '../data/procurementPhases';

import TenderDetailView from '../components/views/TenderDetailView';

// Badge for tender phase
function TahapBadge({ status }) {
  const phaseIndex = getTenderPhaseIndex(status);
  if (phaseIndex < 0) return null;
  const phase = procurementPhases[phaseIndex];
  if (!phase) return null;
  return (
    <span className="inline-flex items-center gap-1.5 px-2.5 py-1 bg-red-500 text-white text-[10px] font-bold rounded-md">
      Tahap: {phase.label}
    </span>
  );
}

// Simple breadcrumb
function Breadcrumb({ onHome, onBack, tenderTitle }) {
  return (
    <nav className="flex items-center gap-2 text-xs text-muted mb-4">
      <button onClick={onHome} className="text-dpbj-gold hover:underline flex items-center gap-1">
        <Home size={11} /> Home
      </button>
      <span>/</span>
      {tenderTitle ? (
        <>
          <button onClick={onBack} className="text-dpbj-gold hover:underline">Tender</button>
          <span>/</span>
          <span className="text-dpbj-navy font-medium truncate max-w-[200px] md:max-w-md">Paket Detail</span>
        </>
      ) : (
        <span className="text-dpbj-navy font-medium">Tender</span>
      )}
    </nav>
  );
}

export default function PublicTenderPage({ onNavigateHome }) {
  const { selectedTender, setSelectedTender } = useApp();
  const [tenders, setTenders] = useState([]);
  const [isLoading, setIsLoading] = useState(true);
  const [search, setSearch] = useState('');
  const [searchInput, setSearchInput] = useState('');
  const [page, setPage] = useState(1);
  const [totalItems, setTotalItems] = useState(0);
  const perPage = 10;

  const fetchTenders = useCallback(async () => {
    setIsLoading(true);
    try {
      let url = `${API_BASE}/tenders?page=${page}&limit=${perPage}`;
      if (search) url += `&search=${search}`;
      const res = await fetch(url);
      const json = await res.json();
      if (json.success) {
        setTenders(json.data);
        setTotalItems(json.pagination?.total || json.data.length);
      }
    } catch (err) {
      console.error('Error fetching tenders:', err);
    } finally {
      setIsLoading(false);
    }
  }, [page, search]);

  useEffect(() => { fetchTenders(); }, [fetchTenders]);

  const handleSearch = () => {
    setSearch(searchInput);
    setPage(1);
  };

  if (selectedTender) {
    return (
      <div className="space-y-4">
        <Breadcrumb onHome={onNavigateHome} onBack={() => setSelectedTender(null)} tenderTitle={selectedTender.title} />
        <TenderDetailView tender={selectedTender} onBack={() => setSelectedTender(null)} />
      </div>
    );
  }

  return (
    <div className="space-y-4 animate-fade-in">
      <Breadcrumb onHome={onNavigateHome} />

      {/* Search bar */}
      <div className="bg-white rounded-xl shadow-sm p-4 border border-gray-200">
        <div className="flex flex-col sm:flex-row gap-3">
          <button className="h-[42px] px-4 bg-[#39b4d6] hover:bg-[#2b9ab8] text-white rounded-md flex items-center justify-center transition-colors">
            <span className="flex items-center gap-2 text-sm">⚙ <span>▼</span></span>
          </button>
          <input
            value={searchInput}
            onChange={e => setSearchInput(e.target.value)}
            onKeyDown={e => e.key === 'Enter' && handleSearch()}
            placeholder="Cari Tender . . ."
            className="flex-1 px-4 py-2 bg-white border border-gray-300 rounded-md text-sm text-dpbj-navy placeholder:text-gray-400 focus:outline-none focus:border-dpbj-gold"
          />
          <button
            onClick={handleSearch}
            className="h-[42px] px-8 bg-[#dc3545] hover:bg-[#c82333] text-white text-sm rounded-md transition-colors font-medium shadow-sm"
          >
            Cari
          </button>
        </div>
      </div>

      {/* Tender list */}
      <div className="bg-white shadow-sm border border-gray-200 rounded-lg overflow-hidden">
        <div className="p-4 space-y-4">
          {isLoading ? (
            <div className="py-16 text-center text-gray-500 text-sm">Memuat data tender...</div>
          ) : tenders.length === 0 ? (
            <div className="py-16 text-center text-gray-500 text-sm">Tidak ada paket tender yang ditemukan.</div>
          ) : (
            tenders.map((tender) => (
              <div
                key={tender.id}
                onClick={() => setSelectedTender(tender)}
                className="bg-white rounded-md border border-[#c4a4e8] p-4 cursor-pointer hover:shadow-md hover:border-[#a274db] transition-all"
              >
                <p className="text-xs text-gray-500 italic mb-1">
                  No. Paket: {tender.tender_number}
                </p>
                <h3 className="font-bold text-black text-sm uppercase mb-3 hover:text-dpbj-gold transition-colors">
                  {tender.title}
                </h3>
                
                <div className="flex flex-wrap items-center gap-x-6 gap-y-2 text-[11px] text-gray-800 font-medium mb-4">
                  <span className="flex items-center gap-1.5">
                    <Calendar size={13} className="text-gray-600" />
                    Tahun Anggaran: {new Date(tender.created_at || Date.now()).getFullYear()}
                  </span>
                  <span className="flex items-center gap-1.5">
                    <DollarSign size={13} className="text-gray-600" />
                    Harga Perkiraan Sendiri IDR {Number(tender.hps || tender.pagu_anggaran).toLocaleString('id-ID')}
                  </span>
                  <span className="flex items-center gap-1.5">
                    <Tag size={13} className="text-gray-600" />
                    Metode Pengadaan: {methodConfig[tender.method] || tender.method}
                  </span>
                  <span className="flex items-center gap-1.5">
                    <MapPin size={13} className="text-gray-600" />
                    Lokasi Pekerjaan: {tender.unit_kerja || 'Kampus UI Depok'}
                  </span>
                </div>

                <div className="mt-1">
                  <span className="inline-flex items-center gap-1 px-2 py-1 bg-[#d9534f] text-white text-[10px] rounded-l-sm">
                    Tahap:
                  </span>
                  <span className="inline-flex items-center px-2 py-1 bg-[#a981db] text-white text-[10px] rounded-r-sm">
                    {procurementPhases[getTenderPhaseIndex(tender.status)]?.label || tender.status}
                  </span>
                </div>
              </div>
            ))
          )}
        </div>

        {/* Footer count */}
        <div className="px-5 py-4 bg-gray-50 border-t border-gray-200 text-xs text-gray-600 font-medium">
          Menampilkan : {tenders.length}
        </div>
      </div>

      {/* Pagination */}
      {totalItems > perPage && (
        <div className="flex items-center justify-between text-xs text-muted">
          <span>Menampilkan {(page - 1) * perPage + 1} sampai {Math.min(page * perPage, totalItems)} dari {totalItems} data</span>
          <div className="flex gap-1">
            <button
              onClick={() => setPage(p => Math.max(1, p - 1))}
              disabled={page === 1}
              className="px-2 py-1 border border-border rounded hover:bg-surface disabled:opacity-40 transition-colors"
            >
              <ChevronLeft size={12} />
            </button>
            <button
              onClick={() => setPage(p => p + 1)}
              disabled={page * perPage >= totalItems}
              className="px-2 py-1 border border-border rounded hover:bg-surface disabled:opacity-40 transition-colors"
            >
              <ChevronRight size={12} />
            </button>
          </div>
        </div>
      )}
    </div>
  );
}
