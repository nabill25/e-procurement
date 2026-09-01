import { useState, useEffect } from 'react';
import { API_BASE, getAuthHeaders } from '../../context/AppContext';
import { CATEGORIES } from '../../components/modals/EvaluationDetailModal';
import PrintLayout from './PrintLayout';

function categoryLabel(id) {
  return CATEGORIES.find(c => c.id === id)?.label || id;
}

export default function PrintEvaluasiKualifikasi({ tenderId, category, onBack }) {
  const [data, setData] = useState(null);
  const [isLoading, setIsLoading] = useState(true);
  const [error, setError] = useState(null);

  useEffect(() => {
    (async () => {
      try {
        const res = await fetch(`${API_BASE}/print/tenders/${tenderId}/evaluasi-kualifikasi/${category}`, { headers: getAuthHeaders() });
        const json = await res.json();
        if (!json.success) { setError(json.message || 'Gagal memuat dokumen.'); return; }
        setData(json.data);
      } catch {
        setError('Tidak bisa terhubung ke server.');
      } finally {
        setIsLoading(false);
      }
    })();
  }, [tenderId, category]);

  return (
    <PrintLayout title={`Evaluasi Kualifikasi - ${categoryLabel(category)}`} onBack={onBack} isLoading={isLoading} error={error}>
      {data && (
        <>
          <div className="print-doc-subtitle">Direktorat Pengadaan Barang dan Jasa</div>
          <div className="print-doc-subtitle" style={{ marginBottom: 18 }}>Universitas Indonesia</div>
          <div className="print-doc-title">EVALUASI KUALIFIKASI - {categoryLabel(data.category).toUpperCase()}</div>
          <p style={{ textAlign: 'center', fontWeight: 700, marginBottom: 14 }}>
            PEKERJAAN<br />{data.tender.title.toUpperCase()}
          </p>

          <table>
            <thead>
              <tr>
                <th style={{ width: 30 }}>No</th>
                <th>Nama Perusahaan</th>
                {data.criteria.map((c, i) => <th key={i}>{c}</th>)}
              </tr>
            </thead>
            <tbody>
              {data.rows.length ? data.rows.map((r, i) => (
                <tr key={i}>
                  <td style={{ textAlign: 'center' }}>{i + 1}</td>
                  <td>{r.company_name}</td>
                  {r.cells.map((cell, j) => (
                    <td key={j} style={{ textAlign: 'center' }}>
                      {data.is_formula
                        ? (cell.items.length ? cell.items.join(', ') : '-')
                        : (cell.score !== null ? cell.score : (cell.meets === false ? 'Tidak Memenuhi' : cell.meets === true ? 'Memenuhi' : '-'))}
                      {!data.is_formula && cell.notes && <div style={{ fontSize: 10, color: '#666' }}>{cell.notes}</div>}
                    </td>
                  ))}
                </tr>
              )) : (
                <tr><td colSpan={2 + data.criteria.length} style={{ textAlign: 'center' }}>. : : Tidak ada data : : .</td></tr>
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
