import {
  Calendar, MapPin, Briefcase, Tag, List,
  ArrowLeft, Wallet, CalendarClock
} from 'lucide-react';
import { formatRupiah } from '../ui/shared';

// Catatan (ditemukan 2026-09-03 lewat laporan pengguna): komponen ini sebelumnya menampilkan
// SATU set data contoh yang sama persis ("Pengadaan AC Kamar Asrama...", jadwal tahapan
// palsu, riwayat reschedule palsu) untuk paket TENDER APAPUN yang dibuka - bukan cuma belum
// lengkap, tapi aktif menyesatkan (pengunjung publik melihat info yang salah soal paket yang
// sedang mereka lihat). Sudah dibersihkan: field yang datanya memang tersedia dari API publik
// (GET /api/tenders/:id, tanpa perlu login) ditampilkan apa adanya; field yang TIDAK ADA
// sumber data publiknya (jadwal per tahap, riwayat reschedule, bidang usaha, metode evaluasi
// dst - semuanya butuh endpoint yang sengaja diproteksi login) dihapus daripada dikarang.
// Kalau nanti memang dibutuhkan tampil ke publik, itu keputusan tersendiri (perlu endpoint
// publik baru yang sengaja dibuka, bukan sekadar ditambal di sini).
export default function TenderDetailView({ tender, onBack }) {
  if (!tender) return null;

  const tahunAnggaran = tender.created_at ? new Date(tender.created_at).getFullYear() : '-';
  const formatTgl = (iso) => iso ? new Date(iso).toLocaleDateString('id-ID', { day: 'numeric', month: 'long', year: 'numeric' }) : '-';

  return (
    <div className="animate-fade-in pb-10">
      <div className="bg-white rounded-xl border border-border shadow-card overflow-hidden">
        {/* Header Title */}
        <div className="p-6 md:p-8 border-b border-gray-100 relative">
          {onBack && (
            <button
              onClick={onBack}
              className="absolute top-6 right-6 md:top-8 md:right-8 flex items-center gap-1 text-sm font-medium text-gray-500 hover:text-dpbj-gold bg-gray-50 hover:bg-orange-50 px-3 py-1.5 rounded-md transition-colors"
            >
              <ArrowLeft size={16} /> Kembali
            </button>
          )}
          <h1 className="text-xl md:text-2xl font-bold text-dpbj-navy leading-snug pr-24">
            {tender.title}
          </h1>
          <p className="text-xs text-muted font-mono mt-1">{tender.tender_number}</p>
        </div>

        {/* Meta Grid - semua field di sini diambil langsung dari data tender sungguhan */}
        <div className="p-6 md:p-8 grid grid-cols-1 md:grid-cols-2 gap-y-6 gap-x-8">

          <div className="space-y-1">
            <div className="flex items-center gap-1.5 text-xs text-gray-500 font-medium mb-1">
              <Calendar size={14} /> Tahun Anggaran
            </div>
            <p className="text-sm text-gray-800">{tahunAnggaran}</p>
          </div>

          <div className="space-y-1">
            <div className="flex items-center gap-1.5 text-xs text-gray-500 font-medium mb-1">
              <MapPin size={14} /> Lokasi Pekerjaan
            </div>
            <p className="text-sm text-gray-800">{tender.work_location || '-'}</p>
          </div>

          <div className="space-y-1">
            <div className="flex items-center gap-1.5 text-xs text-gray-500 font-medium mb-1">
              <Briefcase size={14} /> Jenis Pengadaan
            </div>
            <p className="text-sm text-gray-800">{tender.category || '-'}</p>
          </div>

          <div className="space-y-1">
            <div className="flex items-center gap-1.5 text-xs text-gray-500 font-medium mb-1">
              <Tag size={14} /> Metode Pengadaan
            </div>
            <p className="text-sm text-gray-800 capitalize">{(tender.method || '-').replaceAll('_', ' ')}</p>
          </div>

          <div className="space-y-1">
            <div className="flex items-center gap-1.5 text-xs text-gray-500 font-medium mb-1">
              <Wallet size={14} /> Pagu Anggaran
            </div>
            <p className="text-sm text-gray-800">{tender.pagu_anggaran ? formatRupiah(tender.pagu_anggaran) : '-'}</p>
          </div>

          <div className="space-y-1">
            <div className="flex items-center gap-1.5 text-xs text-gray-500 font-medium mb-1">
              <CalendarClock size={14} /> Batas Akhir Pengumpulan Penawaran
            </div>
            <p className="text-sm text-gray-800">{formatTgl(tender.submission_deadline)}</p>
          </div>

          {tender.description && (
            <div className="space-y-1 md:col-span-2">
              <div className="flex items-center gap-1.5 text-xs text-gray-500 font-medium mb-1">
                <List size={14} /> Keterangan Paket
              </div>
              <p className="text-sm text-gray-800 leading-relaxed whitespace-pre-line">{tender.description}</p>
            </div>
          )}

        </div>

        {/* Action Buttons */}
        <div className="p-6 md:p-8 pt-0 flex flex-wrap gap-3 border-t border-gray-100 pt-6">
          <button
            onClick={onBack}
            className="flex items-center gap-2 px-5 py-2.5 bg-[#de4454] hover:bg-red-600 text-white text-sm font-semibold rounded-full transition-colors shadow-sm"
          >
            <ArrowLeft size={16} /> Kembali
          </button>
        </div>
      </div>
    </div>
  );
}
