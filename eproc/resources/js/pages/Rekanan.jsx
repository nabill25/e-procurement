import { useState, useEffect, useCallback } from 'react';
import { Star, MapPin, CheckCircle2, Clock, XCircle, Search, Eye } from 'lucide-react';
import clsx from 'clsx';
import api from '@api/client';
import RekananDetailModal from '@components/modals/RekananDetailModal';

function VendorStatusBadge({ status }) {
  const cfg = {
    terverifikasi: { label: 'Terverifikasi', className: 'badge-done',   icon: CheckCircle2 },
    pending:       { label: 'Pending',        className: 'badge-review', icon: Clock },
    ditangguhkan:  { label: 'Ditangguhkan',   className: 'badge-cancel', icon: XCircle },
    diblokir:      { label: 'Diblokir',       className: 'badge-cancel', icon: XCircle },
  };
  const c = cfg[status] || cfg.pending;
  const Icon = c.icon;
  return (
    <span className={clsx('badge', c.className)}>
      <Icon size={11} />
      {c.label}
    </span>
  );
}

function StarRating({ score }) {
  const numScore = parseFloat(score);
  if (isNaN(numScore) || numScore === 0) return <span className="text-xs text-muted">Belum dinilai</span>;
  return (
    <div className="flex items-center gap-1">
      {[1,2,3,4,5].map(i => (
        <Star key={i} size={12} className={i <= Math.round(numScore) ? 'fill-dpbj-gold text-dpbj-gold' : 'text-border'} />
      ))}
      <span className="text-xs font-semibold text-dpbj-navy ml-1">{numScore.toFixed(1)}</span>
    </div>
  );
}

export default function Rekanan() {
  const [vendors, setVendors] = useState([]);
  const [isLoading, setIsLoading] = useState(true);
  const [selectedVendor, setSelectedVendor] = useState(null);
  const [searchQuery, setSearchQuery] = useState('');

  const fetchVendors = useCallback(async () => {
    setIsLoading(true);
    try {
      const res = await api.get('/rekanan');
      if (res.data && res.data.success) {
        setVendors(res.data.data);
      }
    } catch (err) {
      console.error('Failed to fetch vendors:', err);
    } finally {
      setIsLoading(false);
    }
  }, []);

  useEffect(() => {
    fetchVendors();
  }, [fetchVendors]);

  const filteredVendors = vendors.filter(v => 
    v.company_name?.toLowerCase().includes(searchQuery.toLowerCase()) ||
    v.npwp?.toLowerCase().includes(searchQuery.toLowerCase())
  );

  const totalVerified = vendors.filter(v => v.status === 'terverifikasi').length;
  const totalPending  = vendors.filter(v => v.status === 'pending').length;

  const handleUpdateStatus = async (vendor, newStatus) => {
    // In Fase 5, we only do frontend visual mock for the verify action
    // To implement the actual API for status update, we'd add another endpoint.
    if (!confirm(`Ubah status rekanan ${vendor.company_name} menjadi ${newStatus}?`)) return;
    alert(`Status berhasil diubah menjadi ${newStatus} (Mock UI)`);
    setSelectedVendor(null);
  };

  return (
    <div className="space-y-6 animate-fade-in">
      {/* Stats row */}
      <div className="grid grid-cols-3 gap-4">
        {[
          { label: 'Total Vendor Terdaftar', value: vendors.length,   color: 'text-dpbj-navy' },
          { label: 'Vendor Terverifikasi',   value: totalVerified,    color: 'text-emerald-600' },
          { label: 'Menunggu Verifikasi',    value: totalPending,     color: 'text-amber-600' },
        ].map(({ label, value, color }) => (
          <div key={label} className="section-card text-center py-5">
            <p className={`text-3xl font-extrabold ${color}`}>{value}</p>
            <p className="text-xs text-muted mt-1">{label}</p>
          </div>
        ))}
      </div>

      <div className="section-card">
        <div className="flex items-center gap-3 mb-4">
          <div className="flex-1">
            <h2 className="text-base font-bold text-dpbj-navy">Daftar Penyedia Barang & Jasa (Rekanan)</h2>
            <p className="text-xs text-muted">{vendors.length} vendor terdaftar dalam sistem</p>
          </div>
          <div className="flex items-center gap-2 bg-surface border border-border rounded-xl px-3 py-2 focus-within:border-dpbj-gold transition-all">
            <Search size={13} className="text-gray-400" />
            <input 
              className="bg-transparent text-sm text-dpbj-navy placeholder:text-gray-400 focus:outline-none w-40" 
              placeholder="Cari vendor..." 
              value={searchQuery}
              onChange={e => setSearchQuery(e.target.value)}
            />
          </div>
        </div>

        <div className="overflow-x-auto rounded-xl border border-border">
          <table className="data-table">
            <thead>
              <tr>
                <th>Nama Perusahaan</th>
                <th>NPWP</th>
                <th>Bidang Usaha</th>
                <th>Lokasi</th>
                <th>Kinerja</th>
                <th>Status</th>
                <th>Aksi</th>
              </tr>
            </thead>
            <tbody>
              {isLoading ? (
                <tr><td colSpan={7} className="py-12 text-center text-muted text-sm">Memuat data rekanan...</td></tr>
              ) : filteredVendors.length === 0 ? (
                <tr><td colSpan={7} className="py-12 text-center text-muted text-sm">Tidak ada vendor ditemukan.</td></tr>
              ) : filteredVendors.map(v => (
                <tr key={v.id} className="cursor-pointer hover:bg-surface" onClick={() => setSelectedVendor(v)}>
                  <td>
                    <div className="flex items-center gap-3">
                      <div className="w-8 h-8 rounded-xl bg-dpbj-navy/5 flex items-center justify-center flex-shrink-0">
                        <span className="text-xs font-bold text-dpbj-navy">
                          {v.company_name ? v.company_name.substring(0, 2).toUpperCase() : 'NA'}
                        </span>
                      </div>
                      <div>
                        <p className="font-semibold text-sm text-dpbj-navy">{v.company_name}</p>
                        <p className="text-xs text-muted truncate w-24" title={v.id}>ID: {v.id}</p>
                      </div>
                    </div>
                  </td>
                  <td><span className="font-mono text-xs text-dpbj-slate">{v.npwp}</span></td>
                  <td><span className="text-xs bg-dpbj-navy/5 text-dpbj-slate px-2 py-1 rounded-lg font-medium">{v.category || 'Belum diisi'}</span></td>
                  <td>
                    <div className="flex items-center gap-1 text-xs text-muted">
                      <MapPin size={11} />
                      {v.city || 'Belum diisi'}
                    </div>
                  </td>
                  <td><StarRating score={v.score} /></td>
                  <td><VendorStatusBadge status={v.status} /></td>
                  <td>
                    <button 
                      onClick={(e) => { e.stopPropagation(); setSelectedVendor(v); }}
                      className="p-1.5 rounded-lg hover:bg-dpbj-gold-faint hover:text-dpbj-gold-dark transition-colors"
                      title="Lihat Profil Vendor"
                    >
                      <Eye size={14} />
                    </button>
                  </td>
                </tr>
              ))}
            </tbody>
          </table>
        </div>
      </div>

      <RekananDetailModal 
        isOpen={!!selectedVendor} 
        vendor={selectedVendor} 
        onClose={() => setSelectedVendor(null)}
        onVerify={(v) => handleUpdateStatus(v, 'terverifikasi')}
        onReject={(v) => handleUpdateStatus(v, 'ditangguhkan')}
      />
    </div>
  );
}
