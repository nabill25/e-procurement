import { useState, useEffect } from 'react';
import { QrCode, CheckCircle2, XCircle, Home, Search } from 'lucide-react';
import { API_BASE } from '../context/AppContext';

const SOURCE_LABEL = {
  kontrak: 'Kontrak / SPK',
  pengumuman_tender: 'Pengumuman Tender',
  hasil_evaluasi: 'Hasil Evaluasi',
  lainnya: 'Dokumen Lainnya',
};

function Breadcrumb({ onHome }) {
  return (
    <nav className="flex items-center gap-2 text-xs text-muted mb-4">
      <button onClick={onHome} className="text-dpbj-gold hover:underline flex items-center gap-1">
        <Home size={11} /> Home
      </button>
      <span>/</span>
      <span className="text-dpbj-navy font-medium">Verifikasi Dokumen</span>
    </nav>
  );
}

export default function QrVerify({ initialCode, onNavigateHome }) {
  const [code, setCode] = useState(initialCode || '');
  const [result, setResult] = useState(null);
  const [isLoading, setIsLoading] = useState(false);
  const [checked, setChecked] = useState(false);

  const verify = async (c) => {
    const target = (c || code).trim();
    if (!target) return;
    setIsLoading(true);
    setChecked(true);
    try {
      const res = await fetch(`${API_BASE}/qr/verify/${encodeURIComponent(target)}`);
      const json = await res.json();
      setResult(json);
    } catch {
      setResult({ success: false, valid: false, message: 'Terjadi kesalahan saat menghubungi server.' });
    } finally {
      setIsLoading(false);
    }
  };

  useEffect(() => {
    if (initialCode) verify(initialCode);
    // eslint-disable-next-line react-hooks/exhaustive-deps
  }, [initialCode]);

  return (
    <div className="animate-fade-in space-y-4">
      <Breadcrumb onHome={onNavigateHome} />

      <div className="bg-white rounded-xl border border-border shadow-card p-6 max-w-xl mx-auto">
        <div className="flex items-center gap-2 mb-6 pb-4 border-b border-border">
          <QrCode size={18} className="text-dpbj-navy" />
          <h2 className="font-bold text-dpbj-navy text-base">Verifikasi <span className="font-light">Keaslian Dokumen</span></h2>
        </div>

        <p className="text-sm text-muted mb-4">
          Masukkan kode yang tertera di dokumen atau pindai kode QR yang ada di dokumen pengadaan untuk memastikan keasliannya.
        </p>

        <form onSubmit={e => { e.preventDefault(); verify(); }} className="flex gap-2 mb-6">
          <input
            value={code}
            onChange={e => setCode(e.target.value.toUpperCase())}
            placeholder="Contoh: AB12CD34EF"
            className="form-input flex-1 font-mono tracking-wider"
          />
          <button type="submit" disabled={isLoading || !code.trim()} className="btn-primary flex items-center gap-2 disabled:opacity-50">
            <Search size={16} /> Cek
          </button>
        </form>

        {isLoading && <p className="text-sm text-muted text-center py-6">Memeriksa...</p>}

        {!isLoading && checked && result && (
          result.valid ? (
            <div className="p-5 bg-emerald-50 border border-emerald-200 rounded-xl">
              <div className="flex items-center gap-2 text-emerald-700 font-bold mb-3">
                <CheckCircle2 size={22} /> Dokumen Terverifikasi Asli
              </div>
              <div className="space-y-1.5 text-sm text-dpbj-navy">
                <p><span className="text-muted">Jenis Dokumen:</span> {SOURCE_LABEL[result.data.source_type] || result.data.source_type}</p>
                {result.data.tender_title && <p><span className="text-muted">Tender:</span> {result.data.tender_title} ({result.data.tender_number})</p>}
                {result.data.vendor_name && <p><span className="text-muted">Vendor:</span> {result.data.vendor_name}</p>}
                {result.data.info && <p><span className="text-muted">Keterangan:</span> {result.data.info}</p>}
                <p><span className="text-muted">Diterbitkan:</span> {new Date(result.data.created_at).toLocaleString('id-ID')}</p>
              </div>
            </div>
          ) : (
            <div className="p-5 bg-red-50 border border-red-200 rounded-xl flex items-center gap-3">
              <XCircle size={22} className="text-red-600 shrink-0" />
              <p className="text-sm text-red-700">{result.message || 'Kode tidak ditemukan. Dokumen ini tidak dapat diverifikasi keasliannya.'}</p>
            </div>
          )
        )}
      </div>
    </div>
  );
}
