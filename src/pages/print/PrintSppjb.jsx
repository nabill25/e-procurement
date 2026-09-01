import { useState, useEffect } from 'react';
import { API_BASE, getAuthHeaders } from '../../context/AppContext';
import PrintLayout from './PrintLayout';

function formatRupiah(n) {
  if (n === null || n === undefined) return '-';
  return `Rp ${Number(n).toLocaleString('id-ID')}`;
}

export default function PrintSppjb({ tenderId, onBack }) {
  const [data, setData] = useState(null);
  const [isLoading, setIsLoading] = useState(true);
  const [error, setError] = useState(null);

  useEffect(() => {
    (async () => {
      try {
        const res = await fetch(`${API_BASE}/print/tenders/${tenderId}/sppjb`, { headers: getAuthHeaders() });
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
    <PrintLayout title="SPPJB" onBack={onBack} isLoading={isLoading} error={error}>
      {data && (
        <>
          <div className="print-doc-subtitle">Direktorat Pengadaan Barang dan Jasa</div>
          <div className="print-doc-subtitle" style={{ marginBottom: 4 }}>Universitas Indonesia</div>
          <p style={{ textAlign: 'right' }}>{data.sppjb.kota_dirut}, {data.sppjb.tanggal}</p>
          <p>Nomor: {data.sppjb.kode}</p>
          <p>Perihal: Penetapan Penyedia {data.tender.title}</p>

          <p style={{ marginTop: 14 }}>
            Yth.<br />
            <strong>Direktur {data.vendor.company_name}</strong><br />
            {data.sppjb.nama_dirut}<br />
            {data.sppjb.alamat_dirut}, {data.sppjb.kota_dirut}
          </p>

          <p style={{ marginTop: 14 }}>
            Berdasarkan hasil proses Pengadaan oleh Direktorat Pengadaan Barang dan Jasa, bahwa perusahaan Saudara
            telah ditunjuk sebagai Pemenang Penyedia <strong>{data.tender.title}</strong>.
          </p>

          <table style={{ marginTop: 12 }}>
            <tbody>
              <tr>
                <td style={{ width: 220, fontWeight: 700 }}>Nilai Pekerjaan</td>
                <td>: <strong>{formatRupiah(data.nilai)}</strong>{data.nilai_terbilang ? ` (${data.nilai_terbilang} rupiah)` : ''}{data.sppjb.ppn === 1 || data.sppjb.ppn === '1' ? ' termasuk PPN' : ''}</td>
              </tr>
              <tr><td style={{ fontWeight: 700 }}>Jaminan Pelaksanaan</td><td>: {data.sppjb.persen_jaminan}% dari nilai pekerjaan</td></tr>
              <tr>
                <td style={{ fontWeight: 700 }}>Jangka Waktu Jaminan</td>
                <td>: <strong>{data.sppjb.jangka_waktu_jaminan}{data.sppjb.jangka_waktu_jaminan_terbilang ? ` (${data.sppjb.jangka_waktu_jaminan_terbilang})` : ''}</strong> hari kalender terhitung mulai {data.sppjb.tmt_jaminan}</td>
              </tr>
              <tr>
                <td style={{ fontWeight: 700 }}>Jangka Waktu Pekerjaan</td>
                <td>: <strong>{data.sppjb.jangka_waktu}{data.sppjb.jangka_waktu_terbilang ? ` (${data.sppjb.jangka_waktu_terbilang})` : ''}</strong> hari kalender</td>
              </tr>
            </tbody>
          </table>

          <p style={{ marginTop: 14 }}>Demikian surat perjanjian ini dibuat untuk dilaksanakan sebagaimana mestinya.</p>

          <div style={{ marginTop: 40, display: 'flex', justifyContent: 'flex-end' }}>
            <div style={{ textAlign: 'center', width: 240 }}>
              <div style={{ height: 60 }} />
              <p style={{ fontWeight: 700, textDecoration: 'underline' }}>{data.sppjb.penanda_tangan}</p>
              <p>{data.sppjb.penanda_tangan_jabatan}</p>
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
