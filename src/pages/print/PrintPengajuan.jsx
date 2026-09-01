import { useState, useEffect } from 'react';
import { API_BASE, getAuthHeaders } from '../../context/AppContext';
import PrintLayout from './PrintLayout';

function formatRupiah(n) {
  if (n === null || n === undefined) return '-';
  return `Rp ${Number(n).toLocaleString('id-ID')}`;
}

export default function PrintPengajuan({ pengajuanId, onBack }) {
  const [data, setData] = useState(null);
  const [isLoading, setIsLoading] = useState(true);
  const [error, setError] = useState(null);

  useEffect(() => {
    (async () => {
      try {
        const res = await fetch(`${API_BASE}/print/pengajuan/${pengajuanId}`, { headers: getAuthHeaders() });
        const json = await res.json();
        if (!json.success) { setError(json.message || 'Gagal memuat dokumen.'); return; }
        setData(json.data);
      } catch {
        setError('Tidak bisa terhubung ke server.');
      } finally {
        setIsLoading(false);
      }
    })();
  }, [pengajuanId]);

  return (
    <PrintLayout title="Permohonan Paket / RUP" onBack={onBack} isLoading={isLoading} error={error}>
      {data && (
        <>
          <div className="print-doc-subtitle">Direktorat Pengadaan Barang dan Jasa</div>
          <div className="print-doc-subtitle" style={{ marginBottom: 18 }}>Universitas Indonesia</div>
          <div className="print-doc-title">PERMOHONAN PAKET USULAN DAN ANALISA KEBUTUHAN</div>
          <p style={{ textAlign: 'center', marginBottom: 14 }}>Nomor: {data.pengajuan.nomor}</p>

          <table>
            <tbody>
              <tr><td style={{ width: 220 }}>Nama Kebutuhan</td><td>: {data.pengajuan.title}</td></tr>
              <tr><td>Unit Kerja</td><td>: {data.pengajuan.unit_kerja || '-'}</td></tr>
              <tr><td>Tahun Anggaran</td><td>: {data.pengajuan.fiscal_year || '-'}</td></tr>
              <tr><td>Kategori</td><td>: {data.pengajuan.category || '-'}</td></tr>
              <tr><td>Perkiraan Biaya</td><td>: {formatRupiah(data.pengajuan.estimated_value)}</td></tr>
              <tr><td>Sumber Anggaran</td><td>: {data.pengajuan.budget_source || '-'}</td></tr>
              <tr><td>Kuantitas</td><td>: {data.pengajuan.quantity || '-'} {data.pengajuan.unit_of_measure || ''}</td></tr>
              <tr><td>Waktu Dibutuhkan</td><td>: {data.pengajuan.needed_by_date || '-'}</td></tr>
              <tr><td>Diajukan Oleh</td><td>: {data.pengajuan.requester_name || '-'}</td></tr>
              <tr><td>Status Saat Ini</td><td>: {data.pengajuan.status || '-'}</td></tr>
            </tbody>
          </table>

          {data.pengajuan.description && (
            <div style={{ marginTop: 16 }}>
              <p style={{ fontWeight: 700 }}>URAIAN KEBUTUHAN</p>
              <p>{data.pengajuan.description}</p>
            </div>
          )}
          {data.pengajuan.technical_spec && (
            <div style={{ marginTop: 12 }}>
              <p style={{ fontWeight: 700 }}>SPESIFIKASI TEKNIS</p>
              <p>{data.pengajuan.technical_spec}</p>
            </div>
          )}

          <div style={{ marginTop: 16 }}>
            <p style={{ fontWeight: 700 }}>ANALISA KEBUTUHAN &amp; PASAR</p>
            <table>
              <tbody>
                <tr><td style={{ width: 220 }}>Komoditas</td><td>: {data.pengajuan.komoditas || '-'}</td></tr>
                <tr><td>Analisa Kebutuhan</td><td>: {data.pengajuan.analisa_kebutuhan || '-'}</td></tr>
                <tr><td>Analisa Pasar</td><td>: {data.pengajuan.analisa_pasar || '-'}</td></tr>
                <tr><td>Risiko Teridentifikasi</td><td>: {data.pengajuan.risiko_teridentifikasi ? 'Ya' : 'Tidak'}</td></tr>
                {data.pengajuan.risiko_teridentifikasi && (
                  <tr><td>Keterangan Risiko</td><td>: {data.pengajuan.risiko_keterangan || '-'}</td></tr>
                )}
              </tbody>
            </table>
          </div>

          <div style={{ marginTop: 16 }}>
            <p style={{ fontWeight: 700 }}>CHECKLIST KELENGKAPAN</p>
            <p>{data.checklist.terpenuhi} dari {data.checklist.total} item terpenuhi</p>
          </div>

          {data.approvals.length > 0 && (
            <div style={{ marginTop: 16 }}>
              <p style={{ fontWeight: 700 }}>RIWAYAT PERSETUJUAN</p>
              <table>
                <thead><tr><th>Approver</th><th style={{ width: 100 }}>Status</th><th style={{ width: 140 }}>Tanggal</th></tr></thead>
                <tbody>
                  {data.approvals.map((a, i) => (
                    <tr key={i}>
                      <td>{a.approved_by_name || '-'}</td>
                      <td style={{ textAlign: 'center' }}>{a.approved ? 'Disetujui' : 'Ditolak'}</td>
                      <td>{new Date(a.created_at).toLocaleDateString('id-ID')}</td>
                    </tr>
                  ))}
                </tbody>
              </table>
            </div>
          )}

          <p style={{ marginTop: 16 }}>{data.kalimat_tanggal}</p>

          <p className="print-doc-footer">
            Dokumen ini dihasilkan secara elektronik oleh Sistem e-Procurement Direktorat Pengadaan Barang dan Jasa, Universitas Indonesia.
          </p>
        </>
      )}
    </PrintLayout>
  );
}
