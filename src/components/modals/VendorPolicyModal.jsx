import { useEffect } from 'react';
import { createPortal } from 'react-dom';
import { X } from 'lucide-react';

const VENDOR_POLICY = [
  'Vendor Management digunakan dalam upaya mendukung penerapan Good Corporate Governance (GCG).',
  'Meningkatkan transparansi, persaingan usaha yang sehat dan kompetitif dengan melakukan pengelolaan vendor management.',
  'Dalam melaksanakan vendor management maka perlu dibuat sebuah pedoman sebagai acuan di lingkungan DPBJ Universitas Indonesia.',
  'Seluruh vendor dan Divisi di lingkungan DPBJ Universitas Indonesia yang terlibat dalam kegiatan vendor management wajib mengikuti pedoman yang berlaku.',
  'Perusahaan yang berafiliasi wajib mencantumkan dan menginformasikan kepada DPBJ Universitas Indonesia.',
  {
    label: 'Masa Berlaku Sanksi Daftar Hitam:',
    items: [
      'Sanksi Daftar Hitam berlaku sejak tanggal Surat Keputusan ditetapkan dan tidak berlaku surut (nonretroaktif).',
      'Vendor yang terkena Sanksi Daftar Hitam dapat menyelesaikan pekerjaan lain, jika kontrak pekerjaan tersebut ditandatangani sebelum pengenaan sanksi.',
      'Vendor yang dikenakan Sanksi Daftar Hitam berlaku selama 2 (dua) tahun.',
    ],
  },
];

const DISCLAIMER = [
  'Surat Permohonan Rekanan adalah surat permohonan yang dibuat oleh calon vendor pada saat melaksanakan registrasi vendor.',
  'Surat Keterangan terdaftar/ Surat Pemberitahuan DPBJ Universitas Indonesia adalah keterangan dalam format sertifikat atau surat yang berisi penjelasan bahwa Perusahaan yang tercantum di dalamnya telah terdaftar sebagai Rekanan DPBJ Universitas Indonesia.',
  'Hak Perusahaan yang sudah menjadi Rekanan DPBJ Universitas Indonesia adalah mendapatkan kesempatan untuk mengikuti pengadaan barang dan jasa di DPBJ Universitas Indonesia, dengan syarat memenuhi kualifikasi dan persyaratan yang ditetapkan oleh DPBJ Universitas Indonesia.',
  {
    label: 'Kewajiban Perusahaan yang sudah menjadi Rekanan DPBJ Universitas Indonesia:',
    items: [
      'Memberikan data/dokumen perusahaan yang sah dan masih berlaku. Apabila terjadi perubahan data/dokumen, perusahaan wajib menginformasikannya kepada DPBJ Universitas Indonesia.',
      'Mematuhi ketentuan Pengadaan Barang/Jasa di DPBJ Universitas Indonesia serta menjunjung prinsip Good Corporate Governance (GCG).',
      'Tidak masuk dalam daftar hitam antara lain Daftar Hitam dari Bank Indonesia, Bank atau Instansi/lembaga lain yang berwenang.',
      'Tidak masuk dalam daftar kredit macet dari Bank Indonesia, Bank atau Instansi/lembaga lain yang berwenang.',
      'Tidak dalam pengawasan pengadilan, tidak pailit, kegiatan usahanya tidak sedang dihentikan, dan atau Direksi yang bertindak untuk dan atas nama perusahaan sedang tidak menjalani sanksi pidana yang dibuktikan dengan surat pernyataan yang ditandatangani oleh Direktur Perusahaan.',
    ],
  },
  'DPBJ Universitas Indonesia tidak berkewajiban memberikan pekerjaan kepada perusahaan yang tercatat sebagai rekanan.',
  'Pemutakhiran/pembaharuan Data vendor: rekaman daftar vendor yang sudah tidak melakukan perubahan data / passive selama 1 (satu) tahun. DPBJ Universitas Indonesia dapat meminta vendor tersebut untuk melakukan proses pemutakhiran terhadap data vendor.',
  'Perusahaan dapat dikeluarkan dari daftar Rekanan Terdaftar di DPBJ Universitas Indonesia apabila terdapat data vendor yang tidak melakukan perubahan data selama 1 (satu) tahun. DPBJ Universitas Indonesia akan meminta masing-masing vendor untuk melakukan pemutakhiran data. Vendor tersebut tidak akan direkomendasikan untuk proses pengadaan barang/jasa.',
  'Apabila DPBJ Universitas Indonesia menerima laporan atau aduan dari pihak luar perihal hal yang tidak baik mengenai perusahaan rekanan, maka DPBJ Universitas Indonesia akan melakukan konfirmasi dan klarifikasi kepada rekanan dimaksud, dan apabila terbukti benar maka DPBJ Universitas Indonesia akan mencabut Surat Terdaftar Rekanan DPBJ Universitas Indonesia atas nama perusahaan tersebut.',
];

export default function VendorPolicyModal({ isOpen, onClose }) {
  if (!isOpen) return null;

  const renderList = (items, prefix = '') => (
    <ol className="space-y-2">
      {items.map((item, idx) => (
        <li key={idx}>
          {typeof item === 'string' ? (
            <p className="text-sm text-gray-700 leading-relaxed">
              <span className="font-medium text-dpbj-navy mr-1">{prefix}{idx + 1}.</span>
              {item}
            </p>
          ) : (
            <div>
              <p className="text-sm text-gray-700 leading-relaxed">
                <span className="font-medium text-dpbj-navy mr-1">{prefix}{idx + 1}.</span>
                {item.label}
              </p>
              <ol className="ml-6 mt-2 space-y-1.5 list-none">
                {item.items.map((sub, subIdx) => (
                  <li key={subIdx} className="text-sm text-dpbj-gold-dark leading-relaxed flex gap-2">
                    <span className="flex-shrink-0 font-medium">{String.fromCharCode(97 + subIdx)}.</span>
                    <span className="text-gray-700">{sub}</span>
                  </li>
                ))}
              </ol>
            </div>
          )}
        </li>
      ))}
    </ol>
  );

  ;

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

  if (!isOpen) return null;

  return createPortal(
    <div className="fixed inset-0 bg-black/30 backdrop-blur-sm z-[200] flex items-center justify-center p-4 animate-fade-in">
      <div className="bg-white rounded-xl shadow-2xl w-full max-w-4xl max-h-[85vh] flex flex-col animate-pop-in">
        {/* Header */}
        <div className="flex items-center justify-between px-6 py-4 border-b border-gray-100">
          <p className="text-sm font-semibold text-dpbj-navy">Sistem Pengadaan Barang Jasa DPBJ Universitas Indonesia</p>
          <button
            onClick={onClose}
            className="w-7 h-7 flex items-center justify-center text-gray-400 hover:text-gray-700 hover:bg-gray-100 rounded-full transition-colors"
          >
            <X size={16} />
          </button>
        </div>

        {/* Content - scrollable */}
        <div className="flex-1 overflow-y-auto px-8 py-6 space-y-8">
          {/* VENDOR POLICY */}
          <section>
            <h2 className="text-center font-bold text-dpbj-navy text-xl tracking-widest uppercase mb-6">Vendor Policy</h2>
            {renderList(VENDOR_POLICY)}
          </section>

          <hr className="border-gray-200" />

          {/* DISCLAIMER */}
          <section>
            <h2 className="text-center font-bold text-dpbj-navy text-xl tracking-widest uppercase mb-6">Disclaimer</h2>
            {renderList(DISCLAIMER)}
          </section>
        </div>

        {/* Footer */}
        <div className="px-6 py-4 border-t border-gray-100 flex justify-end">
          <button
            onClick={onClose}
            className="px-6 py-2 bg-dpbj-navy text-white text-sm font-semibold rounded-lg hover:bg-dpbj-navy-light transition-colors active:scale-95"
          >
            Tutup
          </button>
        </div>
      </div>
    </div>,
    document.body
  );
}
