import { useState, useEffect } from 'react';
import { API_BASE, getAuthHeaders } from '../../context/AppContext';
import PrintLayout from './PrintLayout';

export default function PrintKlarifikasi({ tenderId, onBack }) {
  const [data, setData] = useState(null);
  const [isLoading, setIsLoading] = useState(true);
  const [error, setError] = useState(null);

  useEffect(() => {
    (async () => {
      try {
        const res = await fetch(`${API_BASE}/print/tenders/${tenderId}/klarifikasi`, { headers: getAuthHeaders() });
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
    <PrintLayout title="Klarifikasi" onBack={onBack} isLoading={isLoading} error={error}>
      {data && (
        <>
          <div className="print-doc-title">DOKUMEN KLARIFIKASI</div>
          <p style={{ textAlign: 'center', fontWeight: 700, marginBottom: 4 }}>{data.tender.title.toUpperCase()}</p>
          <p style={{ textAlign: 'center', marginBottom: 18 }}>No. Paket: {data.tender.nomor}</p>

          {data.klarifikasi.length === 0 ? (
            <p style={{ textAlign: 'center' }}>Belum ada dokumen klarifikasi.</p>
          ) : data.klarifikasi.map((k, i) => (
            <div key={i} style={{ marginBottom: 16, borderBottom: '1px solid #d1d5db', paddingBottom: 12 }}>
              <p style={{ fontWeight: 700 }}>{i + 1}. {k.nama}</p>
              <p style={{ fontSize: '11px', color: '#4b5563' }}>Dari: {k.vendor} &middot; {k.waktu}</p>
              {k.notes && <p style={{ marginTop: 4 }}>{k.notes}</p>}
              {k.tanggapan.length > 0 && (
                <div style={{ marginTop: 8, paddingLeft: 16, borderLeft: '2px solid #d1d5db' }}>
                  <p style={{ fontWeight: 700, fontSize: '11px' }}>Tanggapan Panitia:</p>
                  {k.tanggapan.map((t, j) => (
                    <p key={j} style={{ fontSize: '11px', marginTop: 2 }}>{t.notes} <span style={{ color: '#6b7280' }}>({t.waktu})</span></p>
                  ))}
                </div>
              )}
            </div>
          ))}

          <p className="print-doc-footer">
            Dokumen ini dihasilkan secara elektronik oleh Sistem e-Procurement Direktorat Pengadaan Barang dan Jasa, Universitas Indonesia.
          </p>
        </>
      )}
    </PrintLayout>
  );
}
