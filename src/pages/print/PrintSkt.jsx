import { useState, useEffect } from 'react';
import { API_BASE, getAuthHeaders } from '../../context/AppContext';
import PrintLayout from './PrintLayout';

export default function PrintSkt({ vendorId, onBack }) {
  const [data, setData] = useState(null);
  const [isLoading, setIsLoading] = useState(true);
  const [error, setError] = useState(null);

  useEffect(() => {
    (async () => {
      try {
        const res = await fetch(`${API_BASE}/print/vendors/${vendorId}/skt`, { headers: getAuthHeaders() });
        const json = await res.json();
        if (!json.success) { setError(json.message || 'Gagal memuat dokumen.'); return; }
        setData(json.data);
      } catch {
        setError('Tidak bisa terhubung ke server.');
      } finally {
        setIsLoading(false);
      }
    })();
  }, [vendorId]);

  return (
    <PrintLayout title="Surat Keterangan Terdaftar (SKT)" onBack={onBack} isLoading={isLoading} error={error}>
      {data && (
        <>
          <div className="print-doc-title">SURAT KETERANGAN TERDAFTAR (SKT)</div>
          <div className="print-doc-subtitle" style={{ marginBottom: 18 }}>Penyedia Direktorat Pengadaan Barang dan Jasa, Universitas Indonesia</div>

          <p>Berdasarkan hasil proses verifikasi, dengan ini dinyatakan sebagai berikut:</p>

          <table>
            <tbody>
              <tr><td style={{ width: 200 }}>Nama Perusahaan</td><td>: {data.vendor.nama}</td></tr>
              <tr><td>Alamat</td><td>: {data.vendor.alamat}</td></tr>
              <tr><td>No. Telepon</td><td>: {data.vendor.telepon || '-'}</td></tr>
              <tr><td>Kontak Person</td><td>: {data.vendor.kontak_person || '-'}</td></tr>
              <tr><td>Email</td><td>: {data.vendor.email || '-'}</td></tr>
              <tr><td>Kualifikasi Usaha</td><td>: {data.vendor.kualifikasi || '-'}</td></tr>
              <tr><td>NPWP</td><td>: {data.vendor.npwp || '-'}</td></tr>
              <tr><td>NIB</td><td>: {data.vendor.nib || '-'}</td></tr>
            </tbody>
          </table>

          <p style={{ textAlign: 'justify', marginTop: 16 }}>
            Dengan ketentuan bahwa data vendor tersebut adalah benar perusahaan Saudara dalam Sistem e-Procurement Direktorat Pengadaan Barang dan Jasa, Universitas Indonesia, setelah proses verifikasi data perusahaan Saudara dapat mengikuti kegiatan pengadaan barang/jasa selama tidak ada satupun dari dokumen di atas dan pendukungnya yang habis masa berlakunya dan/atau perusahaan Saudara tidak masuk dalam daftar hitam serta perusahaan Saudara memiliki penilaian kinerja terhadap kegiatan pengadaan barang/jasa yang tidak masuk dalam kategori penilaian buruk.
          </p>
          <p style={{ textAlign: 'justify', marginTop: 10 }}>
            Segala perubahan data setelah disahkan sebagai mitra kami akan mempengaruhi proses kualifikasi kegiatan pengadaan barang/jasa. Pelaksana pengadaan barang/jasa berhak menolak bilamana terdapat data perusahaan Saudara yang tidak sesuai.
          </p>
          <p style={{ textAlign: 'justify', marginTop: 10 }}>
            Surat Keterangan Terdaftar ini tidak mempunyai masa berlaku dan menjadi tidak berlaku bila ada dokumen yang sudah kedaluwarsa dan tidak diperbarui oleh Penyedia Barang/Jasa.
          </p>

          <p style={{ marginTop: 16 }}>{data.kalimat_tanggal}</p>

          <p className="print-doc-footer">
            Dokumen ini dihasilkan secara elektronik oleh Sistem e-Procurement Direktorat Pengadaan Barang dan Jasa, Universitas Indonesia.
          </p>
        </>
      )}
    </PrintLayout>
  );
}
