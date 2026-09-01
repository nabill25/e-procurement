import { useState, useEffect, useCallback } from 'react';
import { Calendar, DollarSign, MapPin, Tag, Search, ChevronLeft, ChevronRight, Home } from 'lucide-react';
import { motion } from 'framer-motion';
import { API_BASE, useApp } from '../context/AppContext';
import { methodConfig } from '../data/mockData';
import { procurementPhases, getTenderPhaseIndex } from '../data/procurementPhases';

import TenderDetailView from '../components/views/TenderDetailView';

// Badge tahap tender + mini progress dots - diselaraskan ke tema navy/gold DPBJ
// (versi lama pakai warna merah/ungu ala Bootstrap yang bentrok dengan tema di
// seluruh halaman lain).
function TahapBadge({ status }) {
  const phaseIndex = getTenderPhaseIndex(status);
  if (phaseIndex < 0) return null;
  const phase = procurementPhases[phaseIndex];
  if (!phase) return null;
  return (
    <div className="flex items-center gap-2.5 flex-wrap">
      <span className="inline-flex items-center gap-1.5 px-2.5 py-1 bg-dpbj-navy text-white text-[10px] font-bold rounded-md uppercase tracking-wide">
        Tahap: {phase.label}
      </span>
      <div className="flex items-center gap-1">
        {procurementPhases.map((_, i) => (
          <span
            key={i}
            className={`w-1.5 h-1.5 rounded-full transition-colors ${i <= phaseIndex ? 'bg-dpbj-gold' : 'bg-gray-200'}`}
          />
        ))}
      </div>
    </div>
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
      <div className="section-card !p-4">
        <div className="flex flex-col sm:flex-row gap-3">
          <div className="relative flex-1">
            <Search size={16} className="absolute left-4 top-1/2 -translate-y-1/2 text-gray-400" />
            <input
              value={searchInput}
              onChange={e => setSearchInput(e.target.value)}
              onKeyDown={e => e.key === 'Enter' && handleSearch()}
              placeholder="Cari nama paket atau nomor tender..."
              className="form-input !pl-11"
            />
          </div>
          <button onClick={handleSearch} className="btn-primary">
            Cari Tender
          </button>
        </div>
      </div>

      {/* Tender list */}
      <div className="space-y-4 stagger-list">
        {isLoading ? (
          <div className="section-card py-16 text-center text-gray-500 text-sm">Memuat data tender...</div>
        ) : tenders.length === 0 ? (
          <div className="section-card py-16 text-center text-gray-500 text-sm">Tidak ada paket tender yang ditemukan.</div>
        ) : (
          tenders.map((tender) => (
            <motion.button
              key={tender.id}
              onClick={() => setSelectedTender(tender)}
              whileHover={{ y: -3 }}
              whileTap={{ scale: 0.995 }}
              className="stagger-item section-card !p-5 w-full text-left block group"
            >
              <p className="text-xs text-muted font-mono mb-1">
                No. Paket: {tender.tender_number}
              </p>
              <h3 className="font-bold text-dpbj-navy text-sm mb-3 group-hover:text-dpbj-gold-dark transition-colors">
                {tender.title}
              </h3>

              <div className="flex flex-wrap items-center gap-x-6 gap-y-2 text-[11px] text-gray-600 font-medium mb-4">
                <span className="flex items-center gap-1.5">
                  <Calendar size={13} className="text-dpbj-navy/50" />
                  Tahun Anggaran: {new Date(tender.created_at || Date.now()).getFullYear()}
                </span>
                <span className="flex items-center gap-1.5">
                  <DollarSign size={13} className="text-dpbj-navy/50" />
                  Pagu Anggaran: IDR {Number(tender.pagu_anggaran || 0).toLocaleString('id-ID')}
                </span>
                <span className="flex items-center gap-1.5">
                  <Tag size={13} className="text-dpbj-navy/50" />
                  Metode: {methodConfig[tender.method] || tender.method}
                </span>
                <span className="flex items-center gap-1.5">
                  <MapPin size={13} className="text-dpbj-navy/50" />
                  {tender.unit_kerja || 'Kampus UI Depok'}
                </span>
              </div>

              <TahapBadge status={tender.status} />
            </motion.button>
          ))
        )}
      </div>

      {tenders.length > 0 && totalItems <= perPage && (
        <p className="text-xs text-muted text-center">Menampilkan {tenders.length} paket tender</p>
      )}

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
