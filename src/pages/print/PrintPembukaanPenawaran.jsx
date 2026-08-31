import { useState, useEffect } from 'react';
import { API_BASE, getAuthHeaders } from '../../context/AppContext';
import PrintLayout from './PrintLayout';

function formatRupiah(n) {
  if (n === null || n === undefined) return '-';
  return `Rp ${Number(n).toLocaleString('id-ID')}`;
}

export default function PrintPembukaanPenawaran({ tenderId, onBack }) {
  const [data, setData] = useState(null);
  const [isLoading, setIsLoading] = useState(true);
  const [error, setError] = useState(null);

  useEffect(() => {
    (async () => {
      try {
        const res = await fetch(`${API_BASE}/print/tenders/${tenderId}/pembukaan-penawaran`, { headers: getAuthHeaders() });
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
    <PrintLayout title="Berita Acara Pembukaan Penawaran" onBack={onBack} isLoading={isLoading} error={error}>
      {data && (
        <>
          <div className="print-doc-subtitle">Direktorat Pengadaan Barang dan Jasa</div>
          <div className="print-doc-subtitle" style={{ marginBottom: 18 }}>Universitas Indonesia</div>
          <div className="print-doc-title">HASIL PEMBUKAAN PENAWARAN</div>
          <p style={{ textAlign: 'center', fontWeight: 700, marginBottom: 14 }}>
            PEKERJAAN<br />{data.tender.title.toUpperCase()}
          </p>
          <p>{data.kalimat_tanggal}, telah dilaksanakan pembukaan penawaran dengan hasil sebagai berikut.</p>

          {data.peserta.map((p, idx) => (
            <div key={p.id} style={{ marginTop: 14 }}>
              <p style={{ fontWeight: 700 }}>{idx + 1}. {p.company_name}</p>
              <p style={{ margin: '4px 0' }}>
                Nilai Penawaran: {formatRupiah(p.bid_price)}
              </p>
              <table>
                <thead>
                  <tr><th style={{ width: 30 }}>No</th><th>Dokumen</th><th>Ukuran</th></tr>
                </thead>
                <tbody>
                  <tr><td>1</td><td>Dokumen Penawaran ({p.document_path ? p.document_path.split('/').pop() : '-'})</td><td>-</td></tr>
                </tbody>
              </table>
            </div>
          ))}

          {data.dokumen.length > 0 && (
            <div style={{ marginTop: 14 }}>
              <p style={{ fontWeight: 700 }}>Dokumen Tender:</p>
              <table>
                <thead><tr><th style={{ width: 30 }}>No</th><th>Nama Dokumen</th><th>Ukuran</th><th>Tanggal Upload</th></tr></thead>
                <tbody>
                  {data.dokumen.map((d, i) => (
                    <tr key={i}>
                      <td>{i + 1}</td>
                      <td>{d.name}</td>
                      <td>{d.file_size ? `${Math.round(d.file_size / 1024)} Kb` : '-'}</td>
                      <td>{new Date(d.created_at).toLocaleDateString('id-ID')}</td>
                    </tr>
                  ))}
                </tbody>
              </table>
            </div>
          )}

          <p style={{ marginTop: 14, fontWeight: 700 }}>Harga Perkiraan Sendiri (HPS): {formatRupiah(data.tender.hps)}</p>

          <div style={{ marginTop: 20 }}>
            <p style={{ fontWeight: 700 }}>Pokja Pemilihan:</p>
            <table>
              <thead><tr><th style={{ width: 30 }}>No</th><th>Nama</th><th>Jabatan</th></tr></thead>
              <tbody>
                {data.panitia.map((p, i) => (
                  <tr key={i}><td>{i + 1}</td><td>{p.nama}</td><td>{p.jabatan}</td></tr>
                ))}
              </tbody>
            </table>
          </div>

          <p className="print-doc-footer">
            Dokumen ini dihasilkan secara elektronik oleh Sistem e-Procurement Direktorat Pengadaan Barang dan Jasa, Universitas Indonesia, dan sah tanpa memerlukan tanda tangan basah.
          </p>
        </>
      )}
    </PrintLayout>
  );
}
