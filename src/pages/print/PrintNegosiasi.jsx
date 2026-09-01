import { useState, useEffect } from 'react';
import { API_BASE, getAuthHeaders } from '../../context/AppContext';
import PrintLayout from './PrintLayout';

function formatRupiah(n) {
  if (n === null || n === undefined) return '-';
  return `Rp ${Number(n).toLocaleString('id-ID')}`;
}

export default function PrintNegosiasi({ tenderId, vendorId, onBack }) {
  const [data, setData] = useState(null);
  const [isLoading, setIsLoading] = useState(true);
  const [error, setError] = useState(null);

  useEffect(() => {
    (async () => {
      try {
        const res = await fetch(`${API_BASE}/print/tenders/${tenderId}/negosiasi/${vendorId}`, { headers: getAuthHeaders() });
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
    <PrintLayout title="Berita Acara Negosiasi" onBack={onBack} isLoading={isLoading} error={error}>
      {data && (
        <>
          <div className="print-doc-title">BERITA ACARA NEGOSIASI HARGA</div>
          <p style={{ textAlign: 'center', fontWeight: 700, marginBottom: 4 }}>{data.tender.title.toUpperCase()}</p>
          <p style={{ textAlign: 'center', marginBottom: 18 }}>No. Paket: {data.tender.nomor}</p>

          <p>{data.kalimat_tanggal}, telah diadakan negosiasi harga penawaran pekerjaan tersebut di atas dengan penyedia:</p>
          <table>
            <tbody>
              <tr><td style={{ width: 200, fontWeight: 700 }}>Nama Perusahaan</td><td>: {data.vendor.company_name}</td></tr>
              <tr><td style={{ fontWeight: 700 }}>NPWP</td><td>: {data.vendor.npwp || '-'}</td></tr>
              <tr><td style={{ fontWeight: 700 }}>Harga Penawaran</td><td>: {formatRupiah(data.harga_penawaran)}</td></tr>
              <tr><td style={{ fontWeight: 700 }}>Harga Hasil Negosiasi</td><td>: <strong>{formatRupiah(data.harga_final)}</strong></td></tr>
              <tr><td style={{ fontWeight: 700 }}>Status</td><td>: {data.status === 'sepakat' ? 'Sepakat' : data.status === 'gagal' ? 'Gagal' : 'Berlangsung'}</td></tr>
            </tbody>
          </table>

          <p style={{ marginTop: 14, fontWeight: 700 }}>Riwayat Percakapan Negosiasi:</p>
          <table>
            <thead>
              <tr>
                <th style={{ width: 130 }}>Waktu</th>
                <th style={{ width: 80 }}>Pihak</th>
                <th>Pesan</th>
                <th style={{ width: 110 }}>Harga Tawar</th>
              </tr>
            </thead>
            <tbody>
              {data.chats.length === 0 ? (
                <tr><td colSpan={4} style={{ textAlign: 'center' }}>Belum ada percakapan negosiasi.</td></tr>
              ) : data.chats.map((c, i) => (
                <tr key={i}>
                  <td>{c.waktu}</td>
                  <td>{c.pihak}</td>
                  <td>{c.pesan || '-'}</td>
                  <td>{formatRupiah(c.harga_tawar)}</td>
                </tr>
              ))}
            </tbody>
          </table>

          <p className="print-doc-footer">
            Dokumen ini dihasilkan secara elektronik oleh Sistem e-Procurement Direktorat Pengadaan Barang dan Jasa, Universitas Indonesia.
          </p>
        </>
      )}
    </PrintLayout>
  );
}
