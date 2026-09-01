import { useState, useEffect } from 'react';
import { API_BASE, getAuthHeaders } from '../../context/AppContext';
import { CATEGORIES } from '../../components/modals/EvaluationDetailModal';
import PrintLayout from './PrintLayout';

function categoryLabel(id) {
  return CATEGORIES.find(c => c.id === id)?.label || id;
}

export default function PrintEvaluasiRekapitulasi({ tenderId, onBack }) {
  const [data, setData] = useState(null);
  const [isLoading, setIsLoading] = useState(true);
  const [error, setError] = useState(null);

  useEffect(() => {
    (async () => {
      try {
        const res = await fetch(`${API_BASE}/print/tenders/${tenderId}/evaluasi-rekapitulasi`, { headers: getAuthHeaders() });
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
    <PrintLayout title="Rekapitulasi Evaluasi Kualifikasi" onBack={onBack} isLoading={isLoading} error={error}>
      {data && (
        <>
          <div className="print-doc-subtitle">Direktorat Pengadaan Barang dan Jasa</div>
          <div className="print-doc-subtitle" style={{ marginBottom: 18 }}>Universitas Indonesia</div>
          <div className="print-doc-title">REKAPITULASI EVALUASI KUALIFIKASI</div>
          <p style={{ textAlign: 'center', fontWeight: 700, marginBottom: 14 }}>
            PEKERJAAN<br />{data.tender.title.toUpperCase()}
          </p>

          <table>
            <thead>
              <tr>
                <th style={{ width: 30 }}>No</th>
                <th>Nama Perusahaan</th>
                {data.categories.map((c, i) => <th key={i}>{categoryLabel(c)}</th>)}
                <th>Nilai Akhir</th>
                <th>Lulus</th>
                <th>Keterangan</th>
              </tr>
            </thead>
            <tbody>
              {data.rows.length ? data.rows.map((r, i) => (
                <tr key={i}>
                  <td style={{ textAlign: 'center' }}>{i + 1}</td>
                  <td>{r.company_name}</td>
                  {r.per_category.map((c, j) => <td key={j} style={{ textAlign: 'center' }}>{c.final_score}</td>)}
                  <td style={{ textAlign: 'center', fontWeight: 700 }}>{r.nilai_akhir}</td>
                  <td style={{ textAlign: 'center' }}>{r.lulus ? 'Lulus' : 'Tidak'}</td>
                  <td>{r.keterangan || '-'}</td>
                </tr>
              )) : (
                <tr><td colSpan={5 + data.categories.length} style={{ textAlign: 'center' }}>. : : Tidak ada data : : .</td></tr>
              )}
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
