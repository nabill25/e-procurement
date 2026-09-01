import { useState, useEffect } from 'react';
import { API_BASE, getAuthHeaders } from '../../context/AppContext';
import { procurementPhases } from '../../data/procurementPhases';
import PrintLayout from './PrintLayout';

const phaseLabel = (key) => procurementPhases.find(p => p.id === key)?.label || key;

export default function PrintJadwal({ tenderId, onBack }) {
  const [data, setData] = useState(null);
  const [isLoading, setIsLoading] = useState(true);
  const [error, setError] = useState(null);

  useEffect(() => {
    (async () => {
      try {
        const res = await fetch(`${API_BASE}/print/tenders/${tenderId}/jadwal`, { headers: getAuthHeaders() });
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
    <PrintLayout title="Jadwal Tahapan" onBack={onBack} isLoading={isLoading} error={error}>
      {data && (
        <>
          <div className="print-doc-title">JADWAL PEKERJAAN</div>
          <p style={{ textAlign: 'center', fontWeight: 700, marginBottom: 4 }}>{data.tender.title.toUpperCase()}</p>
          <p style={{ textAlign: 'center', marginBottom: 18 }}>No. Paket: {data.tender.nomor}</p>

          <table>
            <thead>
              <tr>
                <th style={{ width: 32 }}>No</th>
                <th>Tahapan</th>
                <th>Tanggal Mulai</th>
                <th>Tanggal Selesai</th>
              </tr>
            </thead>
            <tbody>
              {data.stages.length === 0 ? (
                <tr><td colSpan={4} style={{ textAlign: 'center' }}>Jadwal belum diatur.</td></tr>
              ) : data.stages.map((s, i) => (
                <tr key={s.stage_key}>
                  <td style={{ textAlign: 'center' }}>{i + 1}</td>
                  <td>{phaseLabel(s.stage_key)}</td>
                  <td>{s.tanggal_mulai}</td>
                  <td>{s.tanggal_selesai}</td>
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
