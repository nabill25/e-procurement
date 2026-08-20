import { useState, useEffect, useCallback } from 'react';
import { Plus, Search, Eye, Calendar, CheckCircle } from 'lucide-react';
import api from '@api/client';
import { StatusBadge, formatRupiah } from '@components/ui/shared';

// Mapping status configurations for Permohonan
const statusConfig = {
  draft: { label: 'Draft / Belum Posting', className: 'badge-draft', dot: '#9ca3af' },
  diajukan: { label: 'Diajukan (Menunggu ACC)', className: 'badge-review', dot: '#f59e0b' },
  disetujui: { label: 'Disetujui / Tender', className: 'badge-done', dot: '#10b981' },
};

export default function PermohonanPaket() {
  const [requests, setRequests] = useState([]);
  const [isLoading, setIsLoading] = useState(true);
  const [searchQuery, setSearchQuery] = useState('');

  const fetchRequests = useCallback(async () => {
    setIsLoading(true);
    try {
      const res = await api.get('/permohonan');
      if (res.data && res.data.success) {
        setRequests(res.data.data);
      }
    } catch (err) {
      console.error('Failed to fetch permohonan:', err);
    } finally {
      setIsLoading(false);
    }
  }, []);

  useEffect(() => {
    fetchRequests();
  }, [fetchRequests]);

  const handleApprove = async (id, title) => {
    // For Fase 6, this is a mock UI action. 
    if (!confirm(`ACC pengajuan "${title}" dan jadikan Paket Tender?`)) return;
    alert(`Status pengajuan berhasil disetujui (Mock UI)`);
    fetchRequests();
  };

  const filteredRequests = requests.filter(req => 
    req.title?.toLowerCase().includes(searchQuery.toLowerCase()) ||
    req.request_number?.toLowerCase().includes(searchQuery.toLowerCase())
  );

  return (
    <div className="space-y-6 animate-fade-in">
      <div className="flex items-center justify-between">
        <div>
          <h2 className="text-lg font-bold text-dpbj-navy">Daftar Permohonan Pengadaan</h2>
          <p className="text-xs text-muted">{requests.length} permohonan pengadaan terdaftar</p>
        </div>
        <button className="btn-primary" onClick={() => alert('Fitur tambah pengajuan sedang dikembangkan')}>
          <Plus size={15} />
          Pengajuan Baru
        </button>
      </div>

      <div className="section-card">
        {/* Search */}
        <div className="flex items-center gap-2 bg-surface border border-border rounded-xl px-3 py-2 mb-4 w-64 focus-within:border-dpbj-gold transition-all">
          <Search size={13} className="text-gray-400" />
          <input 
            className="bg-transparent text-sm text-dpbj-navy placeholder:text-gray-400 focus:outline-none w-full" 
            placeholder="Cari permohonan..." 
            value={searchQuery}
            onChange={e => setSearchQuery(e.target.value)}
          />
        </div>

        <div className="overflow-x-auto rounded-xl border border-border">
          <table className="data-table">
            <thead>
              <tr>
                <th>No. Permohonan</th>
                <th>Judul / Paket</th>
                <th>Unit Kerja</th>
                <th>Estimasi Nilai</th>
                <th>Status</th>
                <th>Tanggal</th>
                <th>Aksi</th>
              </tr>
            </thead>
            <tbody>
              {isLoading ? (
                <tr><td colSpan={7} className="py-12 text-center text-muted text-sm">Memuat data...</td></tr>
              ) : filteredRequests.length === 0 ? (
                <tr><td colSpan={7} className="py-12 text-center text-muted text-sm">Tidak ada data ditemukan.</td></tr>
              ) : filteredRequests.map(req => (
                <tr key={req.id}>
                  <td><span className="font-mono text-xs font-semibold text-dpbj-slate">{req.request_number}</span></td>
                  <td>
                    <div className="max-w-xs">
                      <p className="font-semibold text-sm text-dpbj-navy leading-snug">{req.title}</p>
                      <p className="text-xs text-muted">TA {req.fiscal_year}</p>
                    </div>
                  </td>
                  <td><span className="text-xs text-dpbj-navy">{req.unit_kerja}</span></td>
                  <td><span className="text-sm font-semibold text-dpbj-navy">{formatRupiah(req.estimated_value, true)}</span></td>
                  <td><StatusBadge status={req.status} config={statusConfig} /></td>
                  <td>
                    <div className="flex items-center gap-1.5 text-xs text-muted">
                      <Calendar size={11} />
                      {new Date(req.created_at).toLocaleDateString('id-ID')}
                    </div>
                  </td>
                  <td>
                    <div className="flex items-center gap-1">
                      {req.status === 'diajukan' && (
                        <button 
                          onClick={() => handleApprove(req.id, req.title)}
                          className="p-1.5 rounded-lg text-emerald-600 hover:bg-emerald-50 transition-colors"
                          title="ACC (Setujui menjadi Tender)"
                        >
                          <CheckCircle size={14} />
                        </button>
                      )}
                      <button 
                        onClick={() => alert(`Melihat detail: ${req.title}`)}
                        className="p-1.5 rounded-lg hover:bg-dpbj-gold-faint hover:text-dpbj-gold-dark transition-colors"
                        title="Lihat Detail"
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
      </div>
    </div>
  );
}
