import { useState, useEffect } from 'react';
import { API_BASE, getAuthHeaders } from '../../context/AppContext';
import PrintLayout from './PrintLayout';

function formatRupiah(n) {
  if (n === null || n === undefined) return '-';
  return `Rp ${Number(n).toLocaleString('id-ID')}`;
}

export default function PrintSpmk({ tenderId, onBack }) {
  const [data, setData] = useState(null);
  const [isLoading, setIsLoading] = useState(true);
  const [error, setError] = useState(null);

  useEffect(() => {
    (async () => {
      try {
        const res = await fetch(`${API_BASE}/print/tenders/${tenderId}/spmk`, { headers: getAuthHeaders() });
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
    <PrintLayout title="SPMK" onBack={onBack} isLoading={isLoading} error={error}>
      {data && (
        <>
          <div className="print-doc-subtitle">Direktorat Pengadaan Barang dan Jasa</div>
          <div className="print-doc-subtitle" style={{ marginBottom: 4 }}>Universitas Indonesia</div>
          <p style={{ textAlign: 'center', marginBottom: 18 }}>Nomor: {data.spmk.nomor}</p>
          <div className="print-doc-title">SURAT PERINTAH MULAI KERJA (SPMK)</div>
          <p style={{ textAlign: 'center', fontWeight: 700, marginBottom: 14 }}>
            PEKERJAAN<br />{data.tender.title.toUpperCase()}
          </p>

          <p>{data.kalimat_tanggal}, berdasarkan Surat Perjanjian/Kontrak Nomor {data.contract.nomor_spk || '-'}, dengan ini memerintahkan:</p>

          <table>
            <tbody>
              <tr><td style={{ width: 180, fontWeight: 700 }}>Nama Perusahaan</td><td>: {data.vendor.company_name}</td></tr>
              <tr><td style={{ fontWeight: 700 }}>NPWP</td><td>: {data.vendor.npwp || '-'}</td></tr>
              <tr><td style={{ fontWeight: 700 }}>Alamat</td><td>: {data.vendor.alamat}</td></tr>
            </tbody>
          </table>

          <p style={{ marginTop: 12 }}>Untuk segera memulai pelaksanaan pekerjaan tersebut di atas, dengan ketentuan sebagai berikut:</p>
          <table>
            <tbody>
              <tr><td style={{ width: 220, fontWeight: 700 }}>Nilai Kontrak</td><td>: {formatRupiah(data.contract.nilai)}</td></tr>
              <tr><td style={{ fontWeight: 700 }}>Jangka Waktu Pelaksanaan</td><td>: {data.spmk.dari} s.d. {data.spmk.sampai}</td></tr>
            </tbody>
          </table>

          {data.spmk.keterangan && <p style={{ marginTop: 14 }}>{data.spmk.keterangan}</p>}

          <div style={{ marginTop: 40, display: 'flex', justifyContent: 'space-between' }}>
            <div style={{ textAlign: 'center', width: 220 }}>
              <p>Menerima,</p>
              <div style={{ height: 60 }} />
              <p style={{ fontWeight: 700, textDecoration: 'underline' }}>{data.contract.pihak2_nama || data.vendor.company_name}</p>
              <p>{data.contract.pihak2_jabatan || 'Penyedia'}</p>
            </div>
            <div style={{ textAlign: 'center', width: 220 }}>
              <p>Memerintahkan,</p>
              <div style={{ height: 60 }} />
              <p style={{ fontWeight: 700, textDecoration: 'underline' }}>{data.contract.pihak1_nama || '-'}</p>
              <p>{data.contract.pihak1_jabatan || 'PPK'}</p>
            </div>
          </div>

          <p className="print-doc-footer">
            Dokumen ini dihasilkan secara elektronik oleh Sistem e-Procurement Direktorat Pengadaan Barang dan Jasa, Universitas Indonesia.
          </p>
        </>
      )}
    </PrintLayout>
  );
}
