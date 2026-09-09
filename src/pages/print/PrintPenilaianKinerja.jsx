import { useState, useEffect } from 'react';
import { API_BASE, getAuthHeaders } from '../../context/AppContext';
import PrintLayout from './PrintLayout';

// Padanan "paket_penilaian_pdf.php" - Formulir Penilaian Kinerja Penyedia Barang/Jasa.
// Sistem lama pakai skala tetap 5 pilihan (checklist Sangat Buruk..Sangat Baik), sistem baru
// pakai skor numerik bebas per kriteria (lihat catatan lengkap di server/routes/print.js),
// jadi ditampilkan sebagai tabel skor+bobot+kontribusi, bukan meniru kolom centang.
export default function PrintPenilaianKinerja({ tenderId, onBack }) {
  const [data, setData] = useState(null);
  const [isLoading, setIsLoading] = useState(true);
  const [error, setError] = useState(null);

  useEffect(() => {
    (async () => {
      try {
        const res = await fetch(`${API_BASE}/print/tenders/${tenderId}/penilaian-kinerja`, { headers: getAuthHeaders() });
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
    <PrintLayout title="Penilaian Kinerja Penyedia" onBack={onBack} isLoading={isLoading} error={error}>
      {data && (
        <>
          <div className="print-doc-subtitle">Direktorat Pengadaan Barang dan Jasa</div>
          <div className="print-doc-subtitle" style={{ marginBottom: 18 }}>Universitas Indonesia</div>
          <div className="print-doc-title">FORMULIR PENILAIAN KINERJA PENYEDIA BARANG/JASA</div>
          <p style={{ textAlign: 'center', marginBottom: 4 }}>
            PEKERJAAN<br />{data.tender.title.toUpperCase()}
          </p>
          <p style={{ textAlign: 'center', marginBottom: 14 }}>
            Nomor SPK/PKS: {data.kontrak.nomor || '-'} &mdash; Penyedia: {data.kontrak.penyedia}
          </p>

          {data.bab.map((b, bi) => (
            <div key={bi} style={{ marginBottom: 16 }}>
              <p style={{ fontWeight: 700, marginBottom: 6 }}>{b.kode ? `${b.kode}. ` : ''}{b.nama}</p>
              <table>
                <thead>
                  <tr>
                    <th style={{ width: 30 }}>No</th>
                    <th>Kriteria</th>
                    <th style={{ width: 70 }}>Bobot</th>
                    <th style={{ width: 70 }}>Skor</th>
                    <th style={{ width: 90 }}>Kontribusi</th>
                    <th>Catatan</th>
                  </tr>
                </thead>
                <tbody>
                  {b.pasal.map((p, pi) => (
                    <tr key={pi}>
                      <td style={{ textAlign: 'center' }}>{pi + 1}</td>
                      <td>{p.kode ? `${p.kode} ` : ''}{p.nama}</td>
                      <td style={{ textAlign: 'center' }}>{p.bobot_persen}%</td>
                      <td style={{ textAlign: 'center' }}>{p.skor !== null ? `${p.skor} / ${p.skor_maksimal}` : '-'}</td>
                      <td style={{ textAlign: 'center' }}>{p.kontribusi}</td>
                      <td>{p.catatan || '-'}</td>
                    </tr>
                  ))}
                </tbody>
              </table>
            </div>
          ))}

          <p style={{ textAlign: 'right', fontWeight: 700, fontSize: 14, marginTop: 10 }}>
            Total Nilai Tertimbang: {data.total_tertimbang}
          </p>

          <p className="print-doc-footer">
            Dokumen ini dihasilkan secara elektronik oleh Sistem e-Procurement Direktorat Pengadaan Barang dan Jasa, Universitas Indonesia.
          </p>
        </>
      )}
    </PrintLayout>
  );
}
