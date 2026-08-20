import { useEffect } from 'react';
import { createPortal } from 'react-dom';
import { X, LayoutGrid } from 'lucide-react';

export default function RescheduleHistoryModal({ isOpen, onClose }) {
  if (!isOpen) return null;

  useEffect(() => {
    if (isOpen) {
      document.body.style.overflow = 'hidden';
    } else {
      document.body.style.overflow = '';
    }
    return () => {
      document.body.style.overflow = '';
    };
  }, [isOpen]);

  const scheduleMock = [
    { no: 1, name: 'Pengumuman Tender', time: '31 Juli 2026, 15:30:00 WIB s.d 07 Agustus 2026, 10:00:00 WIB' },
    { no: 2, name: 'Pendaftaran dan Download Dokumen Pengadaan', time: '31 Juli 2026, 15:30:00 WIB s.d 07 Agustus 2026, 10:00:00 WIB' },
    { no: 3, name: 'Upload Dokumen Penawaran dan Enkripsi', time: '31 Juli 2026, 15:30:00 WIB s.d 07 Agustus 2026, 10:00:00 WIB' },
    { no: 4, name: 'Pembukaan Penawaran', time: '07 Agustus 2026, 10:01:00 WIB s.d 07 Agustus 2026, 10:15:00 WIB' },
    { no: 5, name: 'Evaluasi Penawaran', time: '07 Agustus 2026, 10:16:00 WIB s.d 12 Agustus 2026, 14:00:00 WIB' },
    { no: 6, name: 'Pembuktian & Negosiasi', time: '13 Agustus 2026, 08:00:00 WIB s.d 13 Agustus 2026, 16:00:00 WIB' },
    { no: 7, name: 'Pengumuman Pemenang', time: '14 Agustus 2026, 08:00:00 WIB s.d 14 Agustus 2026, 16:00:00 WIB' },
  ];

  return createPortal(
    (
      <div className="fixed inset-0 bg-black/50 backdrop-blur-sm z-[200] flex items-center justify-center p-4 animate-fade-in">
        <div className="bg-white rounded-xl shadow-2xl w-full max-w-4xl max-h-[90vh] flex flex-col animate-pop-in border border-gray-200">
          
          {/* Top minimal header */}
          <div className="flex items-center justify-between px-4 py-3 border-b border-gray-100">
            <p className="text-[13px] text-dpbj-navy">Sistem Pengadaan Barang Jasa DPBJ Universitas Indonesia</p>
            <button
              onClick={onClose}
              className="text-gray-400 hover:text-gray-700 hover:bg-gray-100 rounded p-1 transition-colors"
            >
              <X size={16} />
            </button>
          </div>

          <div className="flex-1 overflow-y-auto p-4 md:p-6 bg-gray-50/50">
            <div className="bg-white border border-border rounded-lg overflow-hidden shadow-sm">
              {/* Header Title */}
              <div className="bg-[#435b6b] px-4 py-3 flex items-center gap-3 text-white">
                <LayoutGrid size={18} />
                <h2 className="font-bold text-sm">Rekam Jejak Reschedule Jadwal</h2>
              </div>
              
              <div className="p-4 md:p-6 space-y-4">
                
                {/* Reschedule 1 Box */}
                <div className="border border-gray-200 rounded p-4 relative">
                  <div className="absolute -top-3 left-4 bg-gray-200 px-2 text-xs font-bold text-gray-700 border border-gray-300">
                    Reschedule 1
                  </div>
                  
                  {/* Alert reason */}
                  <div className="bg-[#e97880] text-white text-sm px-4 py-2 mt-2 mb-4 rounded-sm">
                    Alasan: Evaluasi penawaran sudah selesai dilaksanakan
                  </div>

                  {/* Table */}
                  <div className="overflow-x-auto">
                    <table className="w-full text-sm text-left border-collapse border border-gray-200">
                      <thead className="bg-[#b7bdc1] text-gray-800 text-xs text-center font-bold">
                        <tr>
                          <th className="border border-gray-300 px-4 py-3 w-16">No</th>
                          <th className="border border-gray-300 px-4 py-3">Tahapan</th>
                          <th className="border border-gray-300 px-4 py-3">Waktu Reschedule</th>
                        </tr>
                      </thead>
                      <tbody className="text-gray-600 text-[13px]">
                        {scheduleMock.map((item) => (
                          <tr key={item.no} className="hover:bg-gray-50">
                            <td className="border border-gray-200 px-4 py-3 text-center">{item.no}</td>
                            <td className="border border-gray-200 px-4 py-3">{item.name}</td>
                            <td className="border border-gray-200 px-4 py-3 text-center whitespace-nowrap">
                              {item.time.split(' s.d ').map((t, i) => (
                                <span key={i}>
                                  {i > 0 && <span className="mx-1">s.d</span>}
                                  <br className="sm:hidden" />
                                  {t}
                                </span>
                              ))}
                            </td>
                          </tr>
                        ))}
                      </tbody>
                    </table>
                  </div>

                </div>
              </div>

            </div>
          </div>
          
        </div>
      </div>
    ),
    document.body
  );
}
