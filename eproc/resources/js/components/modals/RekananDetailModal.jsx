import { X, CheckCircle2, AlertCircle, FileText, Download } from 'lucide-react';

export default function RekananDetailModal({ isOpen, vendor, onClose, onVerify, onReject }) {
  // For now, we mock the user role to admin. In a real scenario, get from auth context.
  const user = { role: 'admin' }; 

  if (!isOpen || !vendor) return null;

  const docs = [
    { name: 'Nomor Induk Berusaha (NIB)', status: 'valid' },
    { name: 'Surat Izin Usaha Perdagangan (SIUP)', status: 'valid' },
    { name: 'Tanda Daftar Perusahaan (TDP)', status: 'valid' },
    { name: 'NPWP Perusahaan', status: 'valid' },
    { name: 'Akta Pendirian', status: 'valid' },
    { name: 'Bukti Setor Pajak Tahunan', status: 'warning' },
  ];

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
                  {vendor.company_name ? vendor.company_name.substring(0, 2) : 'NA'}
                </span>
              </div>
              <div>
                <h3 className="font-bold text-dpbj-navy text-lg leading-tight">{vendor.company_name}</h3>
                <p className="text-sm text-dpbj-gold font-semibold">{vendor.npwp}</p>
              </div>
              <div className="space-y-2 mt-4 text-sm">
                <div>
                  <p className="text-xs text-muted">Kategori Usaha</p>
                  <p className="font-medium text-dpbj-navy">{vendor.category || 'Barang / Jasa'}</p>
                </div>
                <div>
                  <p className="text-xs text-muted">Domisili</p>
                  <p className="font-medium text-dpbj-navy">{vendor.city || 'Belum diisi'}</p>
                </div>
                <div>
                  <p className="text-xs text-muted">Status Saat Ini</p>
                  <span className={`inline-block px-2 py-1 mt-1 rounded-md text-xs font-semibold uppercase ${vendor.status === 'terverifikasi' ? 'bg-emerald-100 text-emerald-700' : 'bg-amber-100 text-amber-700'}`}>
                    {vendor.status}
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

        {/* Footer Actions */}
        <div className="p-6 border-t border-border bg-surface flex justify-end gap-3">
          <button onClick={onClose} className="btn-ghost">Tutup</button>
          
          {user.role === 'admin' && vendor.status === 'pending' && (
            <>
              <button 
                onClick={() => onReject(vendor)}
                className="btn-danger"
              >
                Tolak & Minta Perbaikan
              </button>
              <button 
                onClick={() => onVerify(vendor)}
                className="btn-primary bg-emerald-500 hover:bg-emerald-600 text-white"
              >
                <CheckCircle2 size={16} /> Verifikasi Vendor
              </button>
            </>
          )}
        </div>
      </div>
    </div>
  );
}
