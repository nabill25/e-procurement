import { useState, useEffect } from 'react';
import { API_BASE, getAuthHeaders } from '../../context/AppContext';
import PrintLayout from './PrintLayout';

function formatRupiah(n) {
  if (n === null || n === undefined) return '-';
  return `Rp ${Number(n).toLocaleString('id-ID')}`;
}

function passLabel(v) {
  if (v === null) return '-';
  return v ? 'MEMENUHI SYARAT' : 'TIDAK MEMENUHI SYARAT';
}

function kesimpulanLabel(k) {
  switch (k) {
    case 'gugur': return 'GUGUR';
    case 'lulus': return 'LULUS';
    case 'lulus_diatas_hps': return 'LULUS (Di atas HPS)';
    default: return 'Belum Lengkap';
  }
}

// Padanan "evaluasi_penawaran_rekapitulasi_excel.php" - rekap status lulus/gugur evaluasi
// penawaran berjenjang (Administrasi -> Teknis -> Harga) untuk semua vendor peserta tender,
// beda dari halaman "Rekapitulasi Evaluasi Kualifikasi" yang sudah ada (itu rekap nilai
// kualifikasi teknis, ini rekap kelulusan evaluasi penawaran/harga).
export default function PrintRekapitulasiPenawaran({ tenderId, onBack }) {
  const [data, setData] = useState(null);
  const [isLoading, setIsLoading] = useState(true);
  const [error, setError] = useState(null);

  useEffect(() => {
    (async () => {
      try {
        const res = await fetch(`${API_BASE}/print/tenders/${tenderId}/evaluasi-penawaran-rekapitulasi`, { headers: getAuthHeaders() });
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
    <PrintLayout title="Rekapitulasi Evaluasi Penawaran" onBack={onBack} isLoading={isLoading} error={error}>
      {data && (
        <>
          <div className="print-doc-subtitle">Direktorat Pengadaan Barang dan Jasa</div>
          <div className="print-doc-subtitle" style={{ marginBottom: 18 }}>Universitas Indonesia</div>
          <div className="print-doc-title">REKAPITULASI EVALUASI PENAWARAN</div>
          <p style={{ textAlign: 'center', fontWeight: 700, marginBottom: 4 }}>
            PEKERJAAN<br />{data.tender.title.toUpperCase()}
          </p>
          <p style={{ textAlign: 'center', marginBottom: 14 }}>
            HPS: {formatRupiah(data.hps)}
          </p>

          <table>
            <thead>
              <tr>
                <th style={{ width: 30 }}>No</th>
                <th>Nama Perusahaan</th>
                <th>Penawaran</th>
                <th>% thd HPS</th>
                <th>Administrasi</th>
                <th>Teknis</th>
                <th>Harga</th>
                <th>Kesimpulan</th>
              </tr>
            </thead>
            <tbody>
              {data.rows.length ? data.rows.map((r, i) => (
                <tr key={i}>
                  <td style={{ textAlign: 'center' }}>{i + 1}</td>
                  <td>{r.company_name}{r.is_winner ? ' (Pemenang)' : ''}</td>
                  <td style={{ textAlign: 'right' }}>{formatRupiah(r.bid_price)}</td>
                  <td style={{ textAlign: 'center' }}>{r.persentase_hps !== null ? `${r.persentase_hps}%` : '-'}</td>
                  <td style={{ textAlign: 'center' }}>{passLabel(r.admin)}</td>
                  <td style={{ textAlign: 'center' }}>{passLabel(r.teknis)}</td>
                  <td style={{ textAlign: 'center' }}>{passLabel(r.harga)}</td>
                  <td style={{ textAlign: 'center', fontWeight: 700 }}>{kesimpulanLabel(r.kesimpulan)}</td>
                </tr>
              )) : (
                <tr><td colSpan={8} style={{ textAlign: 'center' }}>. : : Tidak ada data : : .</td></tr>
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
