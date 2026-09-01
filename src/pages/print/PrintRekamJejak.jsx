import { useState, useEffect } from 'react';
import { API_BASE, getAuthHeaders } from '../../context/AppContext';
import PrintLayout from './PrintLayout';

export default function PrintRekamJejak({ tenderId, onBack }) {
  const [data, setData] = useState(null);
  const [isLoading, setIsLoading] = useState(true);
  const [error, setError] = useState(null);

  useEffect(() => {
    (async () => {
      try {
        const res = await fetch(`${API_BASE}/print/tenders/${tenderId}/rekam-jejak`, { headers: getAuthHeaders() });
        const json = await res.json();
        if (!json.success) { setError(json.message || 'Gagal memuat dokumen.'); return; }
        setData(json.data);
      } catch {
        setError('Tidak bisa terhubung ke server.');
      } finally {
        setIsLoading(false);
      }
    })();
  }, [tenderId]);

  return (
    <PrintLayout title="Rekam Jejak" onBack={onBack} isLoading={isLoading} error={error}>
      {data && (
        <>
          <div className="print-doc-title">REKAM JEJAK</div>
          <p style={{ textAlign: 'center', fontWeight: 700, marginBottom: 4 }}>{data.tender.title.toUpperCase()}</p>
          <p style={{ textAlign: 'center', marginBottom: 18 }}>No. Paket: {data.tender.nomor}</p>

          <table>
            <thead>
              <tr>
                <th style={{ width: 32 }}>No</th>
                <th style={{ width: 140 }}>Waktu</th>
                <th>Aktivitas</th>
                <th style={{ width: 140 }}>Oleh</th>
              </tr>
            </thead>
            <tbody>
              {data.logs.length === 0 ? (
                <tr><td colSpan={4} style={{ textAlign: 'center' }}>Belum ada aktivitas tercatat.</td></tr>
              ) : data.logs.map((l, i) => (
                <tr key={i}>
                  <td style={{ textAlign: 'center' }}>{i + 1}</td>
                  <td>{l.waktu}</td>
                  <td>{l.posisi}{l.keterangan ? <><br /><span style={{ fontSize: '10px' }}>{l.keterangan}</span></> : null}</td>
                  <td>{l.user_name}</td>
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
