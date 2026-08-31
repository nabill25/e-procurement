import { useState, useEffect } from 'react';
import { API_BASE, getAuthHeaders } from '../../context/AppContext';
import PrintLayout from './PrintLayout';

export default function PrintAanwijzing({ tenderId, onBack }) {
  const [data, setData] = useState(null);
  const [isLoading, setIsLoading] = useState(true);
  const [error, setError] = useState(null);

  useEffect(() => {
    (async () => {
      try {
        const res = await fetch(`${API_BASE}/print/tenders/${tenderId}/aanwijzing`, { headers: getAuthHeaders() });
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
    <PrintLayout title="Berita Acara Aanwijzing" onBack={onBack} isLoading={isLoading} error={error}>
      {data && (
        <>
          <div className="print-doc-subtitle">Direktorat Pengadaan Barang dan Jasa</div>
          <div className="print-doc-subtitle" style={{ marginBottom: 18 }}>Universitas Indonesia</div>
          <div className="print-doc-title">HASIL RAPAT PENJELASAN (AANWIJZING)</div>
          <p style={{ textAlign: 'center', fontWeight: 700, marginBottom: 14 }}>
            PEKERJAAN<br />{data.tender.title.toUpperCase()}
          </p>
          <p>{data.kalimat_tanggal}, telah diadakan rapat pemberian penjelasan / aanwijzing untuk pekerjaan dimaksud di atas.</p>
          <p style={{ marginTop: 6 }}>Rapat aanwijzing dilaksanakan secara daring melalui Sistem e-Procurement DPBJ Universitas Indonesia dengan risalah penjelasan terlampir. Hasil ini mengikat dan merupakan bagian yang tidak terpisahkan dari Dokumen Lelang.</p>
          <p style={{ marginTop: 6 }}>Jumlah peserta yang mengonfirmasi kehadiran: <b>{data.konfirmasi_hadir}</b> vendor.</p>

          <div style={{ marginTop: 16 }}>
            <p style={{ fontWeight: 700 }}>Pokja Pemilihan:</p>
            <table>
              <thead><tr><th style={{ width: 30 }}>No</th><th>Nama</th><th>Jabatan</th></tr></thead>
              <tbody>
                {data.panitia.map((p, i) => <tr key={i}><td>{i + 1}</td><td>{p.nama}</td><td>{p.jabatan}</td></tr>)}
              </tbody>
            </table>
          </div>

          <div style={{ marginTop: 14 }}>
            <p style={{ fontWeight: 700 }}>Penyedia:</p>
            <table>
              <thead><tr><th style={{ width: 30 }}>No</th><th>Nama Perusahaan</th></tr></thead>
              <tbody>
                {data.peserta.length === 0
                  ? <tr><td colSpan={2}>. : Tidak ada data : .</td></tr>
                  : data.peserta.map((p, i) => <tr key={i}><td>{i + 1}</td><td>{p.company_name}</td></tr>)
                }
              </tbody>
            </table>
          </div>

          <div style={{ marginTop: 14 }}>
            <p style={{ fontWeight: 700 }}>Lampiran Tanya Jawab:</p>
            <table>
              <thead><tr><th style={{ width: 30 }}>No</th><th style={{ width: 90 }}>Dari</th><th>Pesan</th><th style={{ width: 110 }}>Tanggal</th></tr></thead>
              <tbody>
                {data.tanya_jawab.length === 0
                  ? <tr><td colSpan={4}>. : Tidak ada data : .</td></tr>
                  : data.tanya_jawab.map((c, i) => (
                    <tr key={i}>
                      <td>{i + 1}</td>
                      <td>{c.role === 'vendor' ? 'Penyedia' : 'Pokja'}</td>
                      <td>{c.pesan}</td>
                      <td>{new Date(c.tanggal).toLocaleDateString('id-ID')}</td>
                    </tr>
                  ))
                }
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
