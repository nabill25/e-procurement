import { useState } from 'react';
import { 
  Calendar, MapPin, Briefcase, Tag, FileText, CheckSquare, 
  Building2, List, LayoutGrid, Clock, ArrowLeft, Footprints
} from 'lucide-react';
import RescheduleHistoryModal from '../modals/RescheduleHistoryModal';

export default function TenderDetailView({ tender, onBack }) {
  const [showRescheduleModal, setShowRescheduleModal] = useState(false);

  // Fallbacks if tender object is incomplete
  const title = tender?.title || 'Pengadaan AC Kamar Asrama Gd. E1, E2 dan F1 Universitas Indonesia Kampus Depok';
  const tahunAnggaran = tender?.created_at ? new Date(tender.created_at).getFullYear() : '2026';
  const lokasi = tender?.unit_kerja || 'Kampus UI Depok';
  const jenis = tender?.category || 'Barang';
  const metode = tender?.method || 'Tender Cepat';

  const scheduleMock = [
    { name: 'Pengumuman Tender', time: '31 Juli 2026, 15:30 WIB s.d 07 Agustus 2026, 10:00 WIB' },
    { name: 'Pendaftaran dan Download Dokumen Pengadaan', time: '31 Juli 2026, 15:30 WIB s.d 07 Agustus 2026, 10:00 WIB' },
    { name: 'Upload Dokumen Penawaran dan Enkripsi', time: '31 Juli 2026, 15:30 WIB s.d 07 Agustus 2026, 10:00 WIB' },
    { name: 'Pembukaan Penawaran', time: '07 Agustus 2026, 10:01 WIB s.d 07 Agustus 2026, 10:15 WIB' },
    { name: 'Evaluasi Penawaran', time: '07 Agustus 2026, 10:16 WIB s.d 12 Agustus 2026, 14:00 WIB' },
    { 
      name: 'Pembuktian & Negosiasi', 
      time: '12 Agustus 2026, 14:01 WIB s.d 13 Agustus 2026, 16:00 WIB',
      badge: '1 kali perubahan'
    },
    { 
      name: 'Pengumuman Pemenang', 
      time: '14 Agustus 2026, 08:00 WIB s.d 14 Agustus 2026, 16:00 WIB',
      isHighlight: true
    },
  ];

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
            {title}
          </h1>
        </div>

        {/* Meta Grid */}
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
            <p className="text-sm text-gray-800">{lokasi}</p>
          </div>

          <div className="space-y-1">
            <div className="flex items-center gap-1.5 text-xs text-gray-500 font-medium mb-1">
              <Briefcase size={14} /> Jenis Pengadaan
            </div>
            <p className="text-sm text-gray-800">{jenis}</p>
          </div>

          <div className="space-y-1">
            <div className="flex items-center gap-1.5 text-xs text-gray-500 font-medium mb-1">
              <Tag size={14} /> Metode Pengadaan
            </div>
            <p className="text-sm text-gray-800">{metode}</p>
          </div>

          <div className="space-y-1">
            <div className="flex items-center gap-1.5 text-xs text-gray-500 font-medium mb-1">
              <FileText size={14} /> Metode Penyampaian Penawaran
            </div>
            <p className="text-sm text-gray-800">1 File</p>
          </div>

          <div className="space-y-1">
            <div className="flex items-center gap-1.5 text-xs text-gray-500 font-medium mb-1">
              <CheckSquare size={14} /> Metode Evaluasi
            </div>
            <p className="text-sm text-gray-800">Sistem Harga Terendah</p>
          </div>

          <div className="space-y-1 md:col-span-2">
            <div className="flex items-center gap-1.5 text-xs text-gray-500 font-medium mb-1">
              <Building2 size={14} /> Kualifikasi Usaha
            </div>
            <p className="text-sm text-gray-800">Kecil / Non-Kecil</p>
          </div>

          <div className="space-y-2 md:col-span-2">
            <div className="flex items-center gap-1.5 text-xs text-gray-500 font-medium mb-1">
              <Briefcase size={14} /> Bidang / Sub Bidang<span className="text-gray-400">*^</span>
            </div>
            <p className="text-sm text-gray-800">43224 - INSTALASI PENDINGIN DAN VENTILASI UDARA</p>
            <span className="inline-block px-2 py-1 bg-cyan-500 text-white text-[10px] font-bold rounded">
              * * Salah satu terpenuhi
            </span>
          </div>

          <div className="space-y-1 md:col-span-2">
            <div className="flex items-center gap-1.5 text-xs text-gray-500 font-medium mb-1">
              <List size={14} /> Persyaratan Peserta
            </div>
            <p className="text-sm text-gray-800 leading-relaxed">
              Sesuai persyaratan pada Dokumen Pemilihan Pengadaan AC Kamar Asrama Gedung E1, E2, dan F1 Universitas Indonesia Kampus UI Depok.
            </p>
          </div>

        </div>

        {/* Schedule Table Section */}
        <div className="px-6 md:px-8 pb-8">
          <div className="border border-border rounded-lg overflow-hidden">
            {/* Table Header */}
            <div className="bg-[#435b6b] px-4 py-3 flex items-center gap-3 text-white">
              <LayoutGrid size={18} />
              <h2 className="font-bold text-sm">Jadwal {metode}</h2>
            </div>
            
            {/* Table Body */}
            <div className="divide-y divide-gray-100">
              {scheduleMock.map((item, idx) => (
                <div key={idx} className="flex flex-col sm:flex-row">
                  <div className="sm:w-2/5 p-4 bg-white flex flex-wrap items-center gap-2">
                    <span className={`text-sm ${item.isHighlight ? 'text-red-600 font-bold' : 'text-gray-600'}`}>
                      {item.name}
                    </span>
                    {item.badge && (
                      <span className="inline-block px-2 py-0.5 bg-purple-500 text-white text-[10px] font-bold rounded">
                        {item.badge}
                      </span>
                    )}
                  </div>
                  <div className="sm:w-3/5 p-4 bg-white sm:border-l border-gray-100 flex items-center gap-2">
                    <Clock size={14} className={item.isHighlight ? 'text-red-500' : 'text-gray-900'} />
                    <span className={`text-xs ${item.isHighlight ? 'text-red-600 font-bold' : 'text-gray-900 font-medium'}`}>
                      {item.time}
                    </span>
                  </div>
                </div>
              ))}
            </div>
          </div>
        </div>

        {/* Action Buttons */}
        <div className="p-6 md:p-8 pt-0 flex flex-wrap gap-3 border-t border-gray-100 pt-6">
          <button 
            onClick={onBack}
            className="flex items-center gap-2 px-5 py-2.5 bg-[#de4454] hover:bg-red-600 text-white text-sm font-semibold rounded-full transition-colors shadow-sm"
          >
            <ArrowLeft size={16} /> Kembali
          </button>
          
          <button className="px-5 py-2.5 bg-[#9d7cf0] hover:bg-purple-500 text-white text-sm font-semibold rounded-full transition-colors shadow-sm">
            Pengumuman Pemenang
          </button>
          
          <button 
            onClick={() => setShowRescheduleModal(true)}
            className="flex items-center gap-2 px-5 py-2.5 bg-[#3a434b] hover:bg-gray-800 text-white text-sm font-semibold rounded-full transition-colors shadow-sm"
          >
            <Footprints size={16} /> Rekam Jejak Reschedule Jadwal
          </button>
        </div>
      </div>

      {/* Modal */}
      <RescheduleHistoryModal 
        isOpen={showRescheduleModal} 
        onClose={() => setShowRescheduleModal(false)} 
        tenderTitle={title}
      />
    </div>
  );
}
