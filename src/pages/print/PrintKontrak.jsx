import { useState, useEffect } from 'react';
import { API_BASE, getAuthHeaders } from '../../context/AppContext';
import PrintLayout from './PrintLayout';

function formatRupiah(n) {
  if (n === null || n === undefined) return '-';
  return `Rp ${Number(n).toLocaleString('id-ID')}`;
}

export default function PrintKontrak({ tenderId, onBack }) {
  const [data, setData] = useState(null);
  const [isLoading, setIsLoading] = useState(true);
  const [error, setError] = useState(null);

  useEffect(() => {
    (async () => {
      try {
        const res = await fetch(`${API_BASE}/print/tenders/${tenderId}/kontrak`, { headers: getAuthHeaders() });
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
    <PrintLayout title="Data Kontrak" onBack={onBack} isLoading={isLoading} error={error}>
      {data && (
        <>
          <div className="print-doc-subtitle">Direktorat Pengadaan Barang dan Jasa</div>
          <div className="print-doc-subtitle" style={{ marginBottom: 18 }}>Universitas Indonesia</div>
          <div className="print-doc-title">DATA KONTRAK</div>
          <p style={{ textAlign: 'center', fontWeight: 700, marginBottom: 14 }}>
            PEKERJAAN<br />{data.tender.title.toUpperCase()}
          </p>

          <table>
            <tbody>
              <tr><td style={{ width: 200 }}>Penyedia</td><td>: {data.vendor.nama}</td></tr>
              <tr><td>NPWP</td><td>: {data.vendor.npwp || '-'}</td></tr>
              <tr><td>Telepon</td><td>: {data.vendor.telepon || '-'}</td></tr>
              <tr><td>Email</td><td>: {data.vendor.email || '-'}</td></tr>
              <tr><td>Alamat</td><td>: {data.vendor.alamat}</td></tr>
              <tr><td>Nomor {data.kontrak.jenis_dokumen}</td><td>: {data.kontrak.nomor_legal}</td></tr>
              <tr><td>Tanggal {data.kontrak.jenis_dokumen}</td><td>: {data.kontrak.tanggal_legal}</td></tr>
              <tr><td>Nilai Pekerjaan</td><td>: {formatRupiah(data.kontrak.nilai)}</td></tr>
              <tr><td>Metode Pembayaran</td><td>: {data.kontrak.metode_pembayaran || '-'}</td></tr>
              <tr><td>Jenis Pengadaan</td><td>: {data.kontrak.jenis_pengadaan || '-'}</td></tr>
              <tr><td>Jenis Kontrak</td><td>: {data.kontrak.jenis_kontrak || '-'}</td></tr>
              <tr><td>Jangka Waktu Pelaksanaan</td><td>: {data.kontrak.waktu_pelaksanaan_dari} s/d {data.kontrak.waktu_pelaksanaan_sampai}</td></tr>
              <tr><td>Lingkup Pekerjaan</td><td>: {data.kontrak.lingkup_pekerjaan || '-'}</td></tr>
              <tr><td>PIHAK I</td><td>: {data.kontrak.pihak1_nama || '-'} {data.kontrak.pihak1_jabatan ? `(${data.kontrak.pihak1_jabatan})` : ''}</td></tr>
              <tr><td>PIHAK II</td><td>: {data.kontrak.pihak2_nama} {data.kontrak.pihak2_jabatan ? `(${data.kontrak.pihak2_jabatan})` : ''}</td></tr>
            </tbody>
          </table>

          <div style={{ marginTop: 20 }}>
            <p style={{ fontWeight: 700 }}>DELIVERABLE PEKERJAAN</p>
            <table>
              <thead><tr><th>Lingkup</th><th>Hasil Pekerjaan</th><th style={{ width: 100 }}>Status</th></tr></thead>
              <tbody>
                {data.deliverables.length ? data.deliverables.map((d, i) => (
                  <tr key={i}><td>{d.scope || '-'}</td><td>{d.deliverable_name}</td><td>{d.status}</td></tr>
                )) : <tr><td colSpan={3} style={{ textAlign: 'center' }}>. : : Tidak ada data : : .</td></tr>}
              </tbody>
            </table>
          </div>

          <div style={{ marginTop: 20 }}>
            <p style={{ fontWeight: 700 }}>TERMIN PEMBAYARAN</p>
            <table>
              <thead><tr><th>Keterangan</th><th>Nilai Pembayaran</th><th style={{ width: 100 }}>Progres</th></tr></thead>
              <tbody>
                {data.payment_terms.length ? data.payment_terms.map((p, i) => (
                  <tr key={i}><td>{p.term_name}</td><td>{formatRupiah(p.amount)}</td><td>{p.progress_percent || 0}%</td></tr>
                )) : <tr><td colSpan={3} style={{ textAlign: 'center' }}>. : : Tidak ada data : : .</td></tr>}
              </tbody>
            </table>
          </div>

          {data.sla.length > 0 && (
            <div style={{ marginTop: 20 }}>
              <p style={{ fontWeight: 700 }}>SERVICE LEVEL AGREEMENT (SLA)</p>
              <table>
                <thead><tr><th>Availability</th><th>Waktu (jam)</th><th>Denda Keterlambatan</th><th>Biaya Maintenance</th><th>Nilai Denda</th></tr></thead>
                <tbody>
                  {data.sla.map((s, i) => (
                    <tr key={i}>
                      <td>{s.availability}%</td>
                      <td>{s.waktu}</td>
                      <td>{s.denda}%</td>
                      <td>{formatRupiah(s.biaya_maintenance)}</td>
                      <td>{formatRupiah(s.nilai_denda)}</td>
                    </tr>
                  ))}
                </tbody>
              </table>
            </div>
          )}

          <p className="print-doc-footer">
            Dokumen ini dihasilkan secara elektronik oleh Sistem e-Procurement Direktorat Pengadaan Barang dan Jasa, Universitas Indonesia.
          </p>
        </>
      )}
    </PrintLayout>
  );
}
