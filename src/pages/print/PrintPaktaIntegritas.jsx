import { useState, useEffect } from 'react';
import { API_BASE, getAuthHeaders } from '../../context/AppContext';
import PrintLayout from './PrintLayout';

export default function PrintPaktaIntegritas({ tenderId, vendorId, onBack }) {
  const [data, setData] = useState(null);
  const [isLoading, setIsLoading] = useState(true);
  const [error, setError] = useState(null);

  useEffect(() => {
    (async () => {
      try {
        const url = vendorId
          ? `${API_BASE}/print/tenders/${tenderId}/pakta-integritas/${vendorId}`
          : `${API_BASE}/print/tenders/${tenderId}/pakta-integritas`;
        const res = await fetch(url, { headers: getAuthHeaders() });
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
    <PrintLayout title="Pakta Integritas" onBack={onBack} isLoading={isLoading} error={error}>
      {data && (
        <>
          <div className="print-doc-subtitle">Direktorat Pengadaan Barang dan Jasa</div>
          <div className="print-doc-subtitle" style={{ marginBottom: 18 }}>Universitas Indonesia</div>
          <div className="print-doc-title">PAKTA INTEGRITAS</div>
          <p style={{ textAlign: 'center', marginBottom: 14 }}>Nomor Paket: {data.tender.nomor} &mdash; {data.tender.title}</p>

          <p>{data.kalimat_tanggal}, pihak-pihak yang bertanda tangan di bawah ini menyatakan dengan sesungguhnya bahwa dalam proses pengadaan barang/jasa ini, kami berjanji dan akan bertindak sebagai berikut:</p>
          <ol style={{ margin: '10px 0', paddingLeft: 20 }}>
            <li>Akan melaksanakan tugas secara tertib, disertai rasa tanggung jawab untuk kelancaran dan ketepatan tercapainya tujuan pengadaan barang/jasa;</li>
            <li>Akan bekerja secara profesional, mandiri, dan menjaga kerahasiaan dokumen pengadaan barang/jasa;</li>
            <li>Tidak akan melakukan praktik Korupsi, Kolusi, dan Nepotisme (KKN) serta akan melaporkan kepada pihak berwajib/berwenang apabila mengetahui hal tersebut;</li>
            <li>Akan mematuhi seluruh ketentuan peraturan perundang-undangan yang berlaku dalam pengadaan barang/jasa.</li>
          </ol>

          <table>
            <thead><tr><th style={{ width: 30 }}>No</th><th>Nama Pihak</th><th>NPWP</th><th>Kode Validasi</th></tr></thead>
            <tbody>
              {data.pihak.map((p, i) => (
                <tr key={i}>
                  <td>{i + 1}</td>
                  <td>{p.nama}</td>
                  <td>{p.npwp || '-'}</td>
                  <td style={{ fontFamily: 'monospace' }}>{p.kode_validasi}</td>
                </tr>
              ))}
            </tbody>
          </table>

          <p className="print-doc-footer">
            Dokumen ini dihasilkan secara elektronik oleh Sistem e-Procurement Direktorat Pengadaan Barang dan Jasa, Universitas Indonesia, dan sah tanpa memerlukan tanda tangan basah. Validasi dilakukan lewat kode unik yang tercatat di sistem.
          </p>
        </>
      )}
    </PrintLayout>
  );
}
