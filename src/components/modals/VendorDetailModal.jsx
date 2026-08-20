import { X, CheckCircle2, AlertCircle, FileText, Download, Ban } from 'lucide-react';
import { useApp } from '../../context/AppContext';

export default function VendorDetailModal({ isOpen, vendor, onClose, onVerify, onReject, onSuspend, onBlock }) {
  const { user } = useApp();

  if (!isOpen || !vendor) return null;

  const docs = [
    { name: 'Nomor Induk Berusaha (NIB)', status: 'valid' },
    { name: 'Surat Izin Usaha Perdagangan (SIUP)', status: 'valid' },
    { name: 'Tanda Daftar Perusahaan (TDP)', status: 'valid' },
    { name: 'NPWP Perusahaan', status: 'valid' },
    { name: 'Akta Pendirian', status: 'valid' },
    { name: 'Bukti Setor Pajak Tahunan', status: 'warning' },
  ];

  // Status badge config (mengikuti mapping status rekanan eProc)
  const statusCfg = {
    terverifikasi: { label: 'Terverifikasi',  className: 'bg-emerald-100 text-emerald-700' },
    pending:       { label: 'Menunggu',        className: 'bg-amber-100 text-amber-700' },
    ditangguhkan:  { label: 'Ditangguhkan',    className: 'bg-orange-100 text-orange-700' },
    diblokir:      { label: 'Diblokir',        className: 'bg-red-100 text-red-700' },
  };
  const sc = statusCfg[vendor.status] || statusCfg.pending;

  return (
    <div className="modal-overlay">
      <div className="modal-container w-full max-w-4xl max-h-[90vh] flex flex-col">
        {/* Header */}
        <div className="flex items-center justify-between p-6 border-b border-border bg-surface">
          <div>
            <h2 className="text-xl font-bold text-dpbj-navy">Verifikasi Vendor (SIKAP)</h2>
            <p className="text-xs text-muted mt-1">Audit dokumen kualifikasi penyedia barang/jasa</p>
          </div>
          <button onClick={onClose} className="p-2 hover:bg-white rounded-xl transition-colors">
            <X size={20} className="text-gray-500" />
          </button>
        </div>

        {/* Content */}
        <div className="flex-1 overflow-y-auto p-6 bg-white">
          <div className="grid grid-cols-1 md:grid-cols-3 gap-6">
            
            {/* Left Col: Info */}
            <div className="md:col-span-1 space-y-4">
              <div className="w-20 h-20 bg-dpbj-navy/5 rounded-2xl flex items-center justify-center border border-border">
                <span className="text-2xl font-bold text-dpbj-navy">
                  {vendor.company_name.substring(0, 2)}
                </span>
              </div>
              <div>
                <h3 className="font-bold text-dpbj-navy text-lg leading-tight">{vendor.company_name}</h3>
                <p className="text-sm text-dpbj-gold font-semibold">{vendor.npwp}</p>
              </div>
              <div className="space-y-2 mt-4 text-sm">
                <div>
                  <p className="text-xs text-muted">Kategori Usaha</p>
                  <p className="font-medium text-dpbj-navy">{vendor.category || vendor.company_type || 'Barang / Jasa'}</p>
                </div>
                <div>
                  <p className="text-xs text-muted">Domisili</p>
                  <p className="font-medium text-dpbj-navy">{vendor.city || 'Belum diisi'}</p>
                </div>
                {vendor.email && (
                  <div>
                    <p className="text-xs text-muted">Email</p>
                    <p className="font-medium text-dpbj-navy truncate">{vendor.email}</p>
                  </div>
                )}
                {vendor.phone && (
                  <div>
                    <p className="text-xs text-muted">Telepon</p>
                    <p className="font-medium text-dpbj-navy">{vendor.phone}</p>
                  </div>
                )}
                <div>
                  <p className="text-xs text-muted">Status Saat Ini</p>
                  {/* Mapping status mengikuti eProc: pending/terverifikasi/ditangguhkan/diblokir */}
                  <span className={`inline-block px-2 py-1 mt-1 rounded-md text-xs font-semibold uppercase ${sc.className}`}>
                    {sc.label}
                  </span>
                </div>
              </div>
            </div>

            {/* Right Col: Documents */}
            <div className="md:col-span-2">
              <h3 className="font-bold text-dpbj-navy mb-4">Dokumen Kualifikasi Elektronik</h3>
              <div className="space-y-3">
                {docs.map((doc, i) => (
                  <div key={i} className="flex items-center justify-between p-4 border border-border rounded-xl hover:shadow-sm transition-all bg-surface">
                    <div className="flex items-center gap-3">
                      <div className="w-10 h-10 bg-white rounded-lg flex items-center justify-center border border-border shadow-sm">
                        <FileText size={18} className="text-dpbj-slate" />
                      </div>
                      <div>
                        <p className="text-sm font-semibold text-dpbj-navy">{doc.name}</p>
                        <div className="flex items-center gap-1 mt-1">
                          {doc.status === 'valid' ? (
                            <CheckCircle2 size={12} className="text-emerald-500" />
                          ) : (
                            <AlertCircle size={12} className="text-amber-500" />
                          )}
                          <span className={`text-[10px] font-medium ${doc.status === 'valid' ? 'text-emerald-600' : 'text-amber-600'}`}>
                            {doc.status === 'valid' ? 'Dokumen Valid' : 'Butuh Perhatian'}
                          </span>
                        </div>
                      </div>
                    </div>
                    <button className="p-2 text-dpbj-navy hover:text-dpbj-gold transition-colors hover:bg-white rounded-lg border border-transparent hover:border-border">
                      <Download size={16} />
                    </button>
                  </div>
                ))}
              </div>
            </div>

          </div>
        </div>

        {/* Footer Actions — mengikuti alur eProc status rekanan */}
        <div className="p-6 border-t border-border bg-surface flex flex-wrap justify-end gap-3">
          <button onClick={onClose} className="btn-ghost">Tutup</button>
          
          {user?.role === 'admin' && (
            <>
              {/* Blokir — eProc: status_validasi = 4 */}
              {vendor.status !== 'diblokir' && (
                <button
                  onClick={() => onBlock && onBlock(vendor)}
                  className="flex items-center gap-2 px-4 py-2 text-sm font-semibold rounded-xl border-2 border-red-500 text-red-500 hover:bg-red-500 hover:text-white transition-all"
                >
                  <Ban size={14} />
                  Blokir / Blacklist
                </button>
              )}

              {/* Tangguhkan — eProc: status_validasi = 3 */}
              {(vendor.status === 'terverifikasi' || vendor.status === 'pending') && (
                <button
                  onClick={() => onSuspend && onSuspend(vendor)}
                  className="flex items-center gap-2 px-4 py-2 text-sm font-semibold rounded-xl border-2 border-orange-400 text-orange-600 hover:bg-orange-400 hover:text-white transition-all"
                >
                  <AlertCircle size={14} />
                  Tangguhkan
                </button>
              )}

              {/* Tolak — kembalikan ke pending */}
              {vendor.status === 'pending' && (
                <button
                  onClick={() => onReject && onReject(vendor)}
                  className="btn-danger"
                >
                  Tolak & Minta Perbaikan
                </button>
              )}

              {/* Verifikasi — eProc: status_validasi = 2 */}
              {vendor.status === 'pending' && (
                <button
                  onClick={() => onVerify && onVerify(vendor)}
                  className="btn-primary bg-emerald-500 hover:bg-emerald-600 text-white"
                >
                  <CheckCircle2 size={16} /> Verifikasi Vendor
                </button>
              )}
            </>
          )}
        </div>
      </div>
    </div>
  );
}
