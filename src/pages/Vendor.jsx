import { useState, useEffect, useCallback } from 'react';
import { Star, MapPin, CheckCircle2, Clock, XCircle, Search, Eye, MoveHorizontal } from 'lucide-react';
import { API_BASE, useApp, getAuthHeaders } from '../context/AppContext';
import clsx from 'clsx';
import VendorDetailModal from '../components/modals/VendorDetailModal';

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

export default function Vendor() {
  const { user } = useApp();
  const [vendors, setVendors] = useState([]);
  const [isLoading, setIsLoading] = useState(true);
  const [selectedVendor, setSelectedVendor] = useState(null);

  const fetchVendors = useCallback(async () => {
    setIsLoading(true);
    try {
      const res = await fetch(`${API_BASE}/vendors`, { headers: getAuthHeaders() });
      const json = await res.json();
      if (json.success) {
        setVendors(json.data);
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

  const totalVerified = vendors.filter(v => v.status === 'terverifikasi').length;
  const totalPending  = vendors.filter(v => v.status === 'pending').length;

  // ── Update status vendor (general) — mengikuti mapping status_validasi eProc ──
  const handleUpdateStatus = async (vendor, newStatus) => {
    if (!confirm(`Ubah status vendor ${vendor.company_name} menjadi ${newStatus}?`)) return;
    try {
      const res = await fetch(`${API_BASE}/vendors/${vendor.id}/status`, {
        method: 'POST',
        headers: getAuthHeaders(),
        body: JSON.stringify({ status: newStatus })
      });
      const json = await res.json();
      if (json.success) {
        alert(json.message);
        setSelectedVendor(null);
        fetchVendors();
      } else {
        alert('Gagal: ' + json.message);
      }
    } catch {
      alert('Terjadi kesalahan saat menghubungi server.');
    }
  };

  // ── Tangguhkan vendor (status_validasi = 3 di eProc) ──
  const handleSuspend = async (vendor) => {
    const reason = prompt(`Alasan penangguhan untuk ${vendor.company_name}:`, '') ?? '';
    if (reason === null) return;
    try {
      const res = await fetch(`${API_BASE}/vendors/${vendor.id}/suspend`, {
        method: 'PATCH',
        headers: getAuthHeaders(),
        body: JSON.stringify({ reason }),
      });
      const json = await res.json();
      if (json.success) {
        alert(json.message);
        setSelectedVendor(null);
        fetchVendors();
      } else {
        alert('Gagal: ' + json.message);
      }
    } catch {
      alert('Terjadi kesalahan saat menghubungi server.');
    }
  };

  // ── Blokir vendor (status_validasi = 4 di eProc) ──
  const handleBlock = async (vendor) => {
    const reason = prompt(`Alasan pemblokiran/blacklist untuk ${vendor.company_name}:`, '') ?? '';
    if (reason === null) return;
    if (!confirm(`PERHATIAN: Vendor ${vendor.company_name} akan diblokir dan masuk Daftar Hitam. Lanjutkan?`)) return;
    try {
      const res = await fetch(`${API_BASE}/vendors/${vendor.id}/block`, {
        method: 'PATCH',
        headers: getAuthHeaders(),
        body: JSON.stringify({ reason }),
      });
      const json = await res.json();
      if (json.success) {
        alert(json.message);
        setSelectedVendor(null);
        fetchVendors();
      } else {
        alert('Gagal: ' + json.message);
      }
    } catch {
      alert('Terjadi kesalahan saat menghubungi server.');
    }
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
        <div className="flex flex-wrap items-center gap-3 mb-4">
          <div className="flex-1 min-w-[180px]">
            <h2 className="text-base font-bold text-dpbj-navy">Daftar Penyedia Barang & Jasa</h2>
            <p className="text-xs text-muted">{vendors.length} vendor terdaftar dalam sistem DPBJ UI</p>
          </div>
          <div className="flex items-center gap-2 bg-surface border border-border rounded-xl px-3 py-2 focus-within:border-dpbj-gold transition-all w-full sm:w-auto">
            <Search size={13} className="text-gray-400 shrink-0" />
            <input className="bg-transparent text-sm text-dpbj-navy placeholder:text-gray-400 focus:outline-none w-full sm:w-40" placeholder="Cari vendor..." />
          </div>
        </div>

        <p className="table-scroll-hint">
          <MoveHorizontal size={13} /> Geser tabel ke kiri/kanan untuk lihat kolom lainnya
        </p>
        <div className="table-scroll">
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
                <tr><td colSpan={7} className="py-12 text-center text-muted text-sm">Memuat data...</td></tr>
              ) : vendors.length === 0 ? (
                <tr><td colSpan={7} className="py-12 text-center text-muted text-sm">Tidak ada vendor terdaftar.</td></tr>
              ) : vendors.map(v => (
                <tr key={v.id} className="cursor-pointer hover:bg-surface" onClick={() => setSelectedVendor(v)}>
                  <td>
                    <div className="flex items-center gap-3">
                      <div className="w-8 h-8 rounded-xl bg-dpbj-navy/5 flex items-center justify-center flex-shrink-0">
                        <span className="text-xs font-bold text-dpbj-navy">
                          {v.company_name.split(' ').slice(-1)[0][0]}
                        </span>
                      </div>
                      <div>
                        <p className="font-semibold text-sm text-dpbj-navy">{v.company_name}</p>
                        <p className="text-xs text-muted truncate w-24" title={v.id}>{v.id}</p>
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
                  <td><StarRating score={v.rating_avg} /></td>
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

      <VendorDetailModal 
        isOpen={!!selectedVendor} 
        vendor={selectedVendor} 
        onClose={() => setSelectedVendor(null)}
        onVerify={(v) => handleUpdateStatus(v, 'terverifikasi')}
        onReject={(v) => handleUpdateStatus(v, 'pending')}
        onSuspend={handleSuspend}
        onBlock={handleBlock}
      />
    </div>
  );
}
