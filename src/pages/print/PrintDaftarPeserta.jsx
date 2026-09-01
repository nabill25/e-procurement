import { useState, useEffect } from 'react';
import { API_BASE, getAuthHeaders } from '../../context/AppContext';
import { methodConfig } from '../../data/mockData';
import PrintLayout from './PrintLayout';

export default function PrintDaftarPeserta({ tenderId, onBack }) {
  const [data, setData] = useState(null);
  const [isLoading, setIsLoading] = useState(true);
  const [error, setError] = useState(null);

  useEffect(() => {
    (async () => {
      try {
        const res = await fetch(`${API_BASE}/print/tenders/${tenderId}/daftar-peserta`, { headers: getAuthHeaders() });
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
    <PrintLayout title="Daftar Peserta" onBack={onBack} isLoading={isLoading} error={error}>
      {data && (
        <>
          <p style={{ fontWeight: 700, fontSize: '13px', marginBottom: 2 }}>
            Daftar Peserta {methodConfig[data.tender.method] || data.tender.method}
          </p>
          <p style={{ fontWeight: 700, fontSize: '13px', marginBottom: 18 }}>{data.tender.title}</p>

          <table>
            <thead>
              <tr>
                <th style={{ width: 40 }}>No</th>
                <th>Nama Perusahaan</th>
                <th style={{ width: 140 }}>Tanggal Daftar</th>
              </tr>
            </thead>
            <tbody>
              {data.peserta.length === 0 ? (
                <tr><td colSpan={3} style={{ textAlign: 'center' }}>Belum ada peserta terdaftar.</td></tr>
              ) : data.peserta.map((p, i) => (
                <tr key={i}>
                  <td style={{ textAlign: 'center' }}>{i + 1}</td>
                  <td style={{ fontWeight: 700 }}>{p.company_name}</td>
                  <td>{p.tanggal_daftar}</td>
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
