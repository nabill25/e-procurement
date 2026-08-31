import { Printer, ArrowLeft, Loader2, AlertTriangle } from 'lucide-react';

// Bungkus bersama untuk semua halaman cetak dokumen resmi (BAPP, Berita Acara Aanwijzing,
// Pakta Integritas, SPPBJ). Tombol "Cetak / Simpan PDF" dan navigasi kembali disembunyikan
// otomatis saat benar-benar dicetak (class no-print + @media print di index.css), supaya hasil
// cetak/PDF cuma berisi dokumennya saja tanpa elemen UI aplikasi.
export default function PrintLayout({ title, onBack, isLoading, error, children }) {
  if (isLoading) {
    return (
      <div className="min-h-screen flex items-center justify-center bg-gray-100">
        <div className="flex items-center gap-2 text-dpbj-navy">
          <Loader2 size={20} className="animate-spin" />
          <span className="text-sm font-medium">Memuat dokumen...</span>
        </div>
      </div>
    );
  }

  if (error) {
    return (
      <div className="min-h-screen flex items-center justify-center bg-gray-100 px-4">
        <div className="bg-white rounded-2xl shadow-card p-8 max-w-md text-center">
          <AlertTriangle size={32} className="text-amber-500 mx-auto mb-3" />
          <p className="text-sm font-semibold text-dpbj-navy mb-1">Dokumen belum bisa ditampilkan</p>
          <p className="text-sm text-muted mb-5">{error}</p>
          <button onClick={onBack} className="btn-secondary text-sm">
            <ArrowLeft size={14} /> Kembali
          </button>
        </div>
      </div>
    );
  }

  return (
    <div className="min-h-screen bg-gray-200">
      <div className="no-print sticky top-0 z-10 bg-dpbj-navy text-white px-4 py-3 flex items-center justify-between shadow-md">
        <button onClick={onBack} className="flex items-center gap-1.5 text-sm font-medium hover:opacity-80">
          <ArrowLeft size={16} /> Kembali
        </button>
        <span className="text-sm font-semibold hidden sm:block">{title}</span>
        <button onClick={() => window.print()} className="flex items-center gap-1.5 bg-dpbj-gold text-dpbj-navy text-sm font-bold px-4 py-1.5 rounded-full hover:brightness-95">
          <Printer size={15} /> Cetak / Simpan PDF
        </button>
      </div>
      <div className="print-page-wrap py-6 px-3 sm:py-10">
        <div className="print-page mx-auto bg-white shadow-lg">
          {children}
        </div>
      </div>
    </div>
  );
}
