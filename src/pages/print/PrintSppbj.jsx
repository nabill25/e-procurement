import { useState, useEffect } from 'react';
import { API_BASE, getAuthHeaders } from '../../context/AppContext';
import PrintLayout from './PrintLayout';

function formatRupiah(n) {
  if (n === null || n === undefined) return '-';
  return `Rp ${Number(n).toLocaleString('id-ID')}`;
}

export default function PrintSppbj({ tenderId, onBack }) {
  const [data, setData] = useState(null);
  const [isLoading, setIsLoading] = useState(true);
  const [error, setError] = useState(null);

  useEffect(() => {
    (async () => {
      try {
        const res = await fetch(`${API_BASE}/print/tenders/${tenderId}/sppbj`, { headers: getAuthHeaders() });
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
    <PrintLayout title="SPPBJ" onBack={onBack} isLoading={isLoading} error={error}>
      {data && (
        <>
          <div className="print-doc-subtitle">Direktorat Pengadaan Barang dan Jasa</div>
          <div className="print-doc-subtitle" style={{ marginBottom: 4 }}>Universitas Indonesia</div>
          <p style={{ textAlign: 'center', marginBottom: 18 }}>Nomor: {data.sppbj.kode}</p>
          <div className="print-doc-title">SURAT PENUNJUKAN PENYEDIA BARANG/JASA (SPPBJ)</div>
          <p style={{ textAlign: 'center', fontWeight: 700, marginBottom: 14 }}>
            PEKERJAAN<br />{data.tender.title.toUpperCase()}
          </p>

          <p>{data.kalimat_tanggal}, berdasarkan hasil evaluasi dan penetapan pemenang pengadaan {data.tender.nomor}, dengan ini {data.sppbj.pejabat_jabatan} menunjuk:</p>

          <table>
            <tbody>
              <tr><td style={{ width: 180, fontWeight: 700 }}>Nama Perusahaan</td><td>: {data.vendor.nama}</td></tr>
              <tr><td style={{ fontWeight: 700 }}>NPWP</td><td>: {data.vendor.npwp || '-'}</td></tr>
              <tr><td style={{ fontWeight: 700 }}>Alamat</td><td>: {data.vendor.alamat}</td></tr>
              <tr><td style={{ fontWeight: 700 }}>Direktur</td><td>: {data.sppbj.direktur_nama} ({data.sppbj.direktur_jabatan})</td></tr>
            </tbody>
          </table>

          <p style={{ marginTop: 12 }}>Sebagai penyedia barang/jasa untuk pekerjaan tersebut di atas, dengan ketentuan sebagai berikut:</p>
          <table>
            <tbody>
              <tr><td style={{ width: 220, fontWeight: 700 }}>Nilai Penunjukan</td><td>: {formatRupiah(data.sppbj.nilai)} ({data.sppbj.nilai_terbilang} RUPIAH)</td></tr>
              <tr><td style={{ fontWeight: 700 }}>Jangka Waktu Pelaksanaan</td><td>: {data.sppbj.pelaksanaan_dari} s.d. {data.sppbj.pelaksanaan_sampai}</td></tr>
              {data.sppbj.jaminan_pelaksana === 'Ya' ? (
                <tr><td style={{ fontWeight: 700 }}>Jaminan Pelaksanaan</td><td>: {data.sppbj.jaminan_persen}% dari nilai kontrak ({formatRupiah(data.sppbj.jaminan_nilai)})</td></tr>
              ) : null}
            </tbody>
          </table>

          <p style={{ marginTop: 14 }}>Saudara diharuskan untuk menyerahkan Jaminan Pelaksanaan dan menandatangani Surat Perjanjian/Kontrak paling lambat 14 (empat belas) hari kerja setelah diterbitkannya SPPBJ ini.</p>

          <div style={{ marginTop: 40, display: 'flex', justifyContent: 'flex-end' }}>
            <div style={{ textAlign: 'center', width: 220 }}>
              <p>{data.sppbj.tanggal}</p>
              <p style={{ fontWeight: 700 }}>{data.sppbj.pejabat_jabatan}</p>
              <div style={{ height: 60 }} />
              <p style={{ fontWeight: 700, textDecoration: 'underline' }}>{data.sppbj.pejabat_berwenang}</p>
              <p>NIP. {data.sppbj.pejabat_nip}</p>
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
