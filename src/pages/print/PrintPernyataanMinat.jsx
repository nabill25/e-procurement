import { useState, useEffect } from 'react';
import { API_BASE, getAuthHeaders } from '../../context/AppContext';
import PrintLayout from './PrintLayout';

export default function PrintPernyataanMinat({ tenderId, vendorId, onBack }) {
  const [data, setData] = useState(null);
  const [isLoading, setIsLoading] = useState(true);
  const [error, setError] = useState(null);

  useEffect(() => {
    (async () => {
      try {
        const res = await fetch(`${API_BASE}/print/tenders/${tenderId}/pernyataan-minat/${vendorId}`, { headers: getAuthHeaders() });
        const json = await res.json();
        if (!json.success) { setError(json.message || 'Gagal memuat dokumen.'); return; }
        setData(json.data);
      } catch {
        setError('Tidak bisa terhubung ke server.');
      } finally {
        setIsLoading(false);
      }
    })();
  }, [tenderId, vendorId]);

  return (
    <PrintLayout title="Pernyataan Minat" onBack={onBack} isLoading={isLoading} error={error}>
      {data && (
        <>
          <div className="print-doc-title">SURAT PERNYATAAN MINAT</div>
          <p style={{ textAlign: 'center', marginBottom: 18 }}>Nomor Paket: {data.tender.nomor}</p>

          <p>Yang bertanda tangan di bawah ini:</p>
          <table>
            <tbody>
              <tr><td style={{ width: 200, fontWeight: 700 }}>Nama</td><td>: {data.pernyataan.nama}</td></tr>
              <tr><td style={{ fontWeight: 700 }}>Jabatan</td><td>: {data.pernyataan.jabatan}</td></tr>
              <tr><td style={{ fontWeight: 700 }}>Alamat</td><td>: {data.pernyataan.alamat}</td></tr>
              <tr><td style={{ fontWeight: 700 }}>Telepon</td><td>: {data.pernyataan.telepon || '-'}</td></tr>
              <tr><td style={{ fontWeight: 700 }}>Email</td><td>: {data.pernyataan.email || '-'}</td></tr>
              <tr><td style={{ fontWeight: 700 }}>Perusahaan</td><td>: {data.vendor.company_name} (NPWP {data.vendor.npwp || '-'})</td></tr>
            </tbody>
          </table>

          <p style={{ marginTop: 14 }}>
            Dengan ini menyatakan MINAT untuk mengikuti paket pengadaan <strong>{data.tender.title}</strong>,
            dan sanggup mematuhi seluruh ketentuan yang berlaku dalam proses pengadaan ini.
          </p>

          {data.pernyataan.penerima_kuasa && (
            <>
              <p style={{ marginTop: 14 }}>Apabila diperlukan, kuasa untuk mewakili dalam proses pengadaan ini diberikan kepada:</p>
              <table>
                <tbody>
                  <tr><td style={{ width: 200, fontWeight: 700 }}>Nama Penerima Kuasa</td><td>: {data.pernyataan.penerima_kuasa}</td></tr>
                  <tr><td style={{ fontWeight: 700 }}>Jabatan</td><td>: {data.pernyataan.penerima_kuasa_jabatan || '-'}</td></tr>
                  <tr><td style={{ fontWeight: 700 }}>No. KTP</td><td>: {data.pernyataan.penerima_kuasa_ktp || '-'}</td></tr>
                </tbody>
              </table>
            </>
          )}

          <div style={{ marginTop: 40, display: 'flex', justifyContent: 'flex-end' }}>
            <div style={{ textAlign: 'center', width: 220 }}>
              <p>{data.kalimat_tanggal}</p>
              <div style={{ height: 60 }} />
              <p style={{ fontWeight: 700, textDecoration: 'underline' }}>{data.pernyataan.nama}</p>
              <p>{data.pernyataan.jabatan}</p>
            </div>
          </div>

          <p className="print-doc-footer">
            Dokumen ini dihasilkan secara elektronik oleh Sistem e-Procurement Direktorat Pengadaan Barang dan Jasa, Universitas Indonesia.
          </p>
        </>
      )}
    </PrintLayout>
  );
}
