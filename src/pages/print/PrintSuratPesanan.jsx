import { useState, useEffect } from 'react';
import { API_BASE, getAuthHeaders } from '../../context/AppContext';
import PrintLayout from './PrintLayout';

function formatRupiah(n) {
  if (n === null || n === undefined) return '-';
  return `Rp ${Number(n).toLocaleString('id-ID')}`;
}

export default function PrintSuratPesanan({ tenderId, spId, onBack }) {
  const [data, setData] = useState(null);
  const [isLoading, setIsLoading] = useState(true);
  const [error, setError] = useState(null);

  useEffect(() => {
    (async () => {
      try {
        const res = await fetch(`${API_BASE}/print/tenders/${tenderId}/surat-pesanan/${spId}`, { headers: getAuthHeaders() });
        const json = await res.json();
        if (!json.success) { setError(json.message || 'Gagal memuat dokumen.'); return; }
        setData(json.data);
      } catch {
        setError('Tidak bisa terhubung ke server.');
      } finally {
        setIsLoading(false);
      }
    })();
  }, [tenderId, spId]);

  return (
    <PrintLayout title="Surat Pesanan" onBack={onBack} isLoading={isLoading} error={error}>
      {data && (
        <>
          <div className="print-doc-subtitle">Direktorat Pengadaan Barang dan Jasa</div>
          <div className="print-doc-subtitle" style={{ marginBottom: 4 }}>Universitas Indonesia</div>
          <p style={{ textAlign: 'center', marginBottom: 18 }}>Nomor: {data.surat_pesanan.nomor_surat}</p>
          <div className="print-doc-title">SURAT PESANAN</div>
          <p style={{ textAlign: 'center', fontWeight: 700, marginBottom: 14 }}>
            {data.tender.title.toUpperCase()}<br />(Kontrak Payung)
          </p>

          <p>{data.kalimat_tanggal}, dengan ini kami memesan barang/jasa kepada:</p>
          <table>
            <tbody>
              <tr><td style={{ width: 180, fontWeight: 700 }}>Nama Perusahaan</td><td>: {data.vendor.company_name}</td></tr>
              <tr><td style={{ fontWeight: 700 }}>NPWP</td><td>: {data.vendor.npwp || '-'}</td></tr>
            </tbody>
          </table>

          <p style={{ marginTop: 12, fontWeight: 700 }}>Rincian Pesanan:</p>
          <table>
            <thead>
              <tr>
                <th style={{ width: 32 }}>No</th>
                <th>Nama Barang/Jasa</th>
                <th style={{ width: 50 }}>Qty</th>
                <th style={{ width: 60 }}>Satuan</th>
                <th style={{ width: 100 }}>Harga Satuan</th>
                <th style={{ width: 110 }}>Total</th>
              </tr>
            </thead>
            <tbody>
              {data.items.length === 0 ? (
                <tr><td colSpan={6} style={{ textAlign: 'center' }}>Belum ada item.</td></tr>
              ) : data.items.map((it, i) => (
                <tr key={i}>
                  <td style={{ textAlign: 'center' }}>{i + 1}</td>
                  <td>{it.nama}</td>
                  <td style={{ textAlign: 'center' }}>{it.qty}</td>
                  <td style={{ textAlign: 'center' }}>{it.satuan}</td>
                  <td style={{ textAlign: 'right' }}>{formatRupiah(it.harga_satuan)}</td>
                  <td style={{ textAlign: 'right' }}>{formatRupiah(it.total)}</td>
                </tr>
              ))}
            </tbody>
            {data.items.length > 0 && (
              <tfoot>
                <tr>
                  <td colSpan={5} style={{ textAlign: 'right', fontWeight: 700 }}>TOTAL</td>
                  <td style={{ textAlign: 'right', fontWeight: 700 }}>{formatRupiah(data.total)}</td>
                </tr>
              </tfoot>
            )}
          </table>
          {data.total_terbilang && <p style={{ fontStyle: 'italic', fontSize: '11px' }}>Terbilang: {data.total_terbilang} rupiah</p>}

          <div style={{ marginTop: 40, display: 'flex', justifyContent: 'flex-end' }}>
            <div style={{ textAlign: 'center', width: 220 }}>
              <p>{data.surat_pesanan.tanggal}</p>
              <div style={{ height: 60 }} />
              <p style={{ fontWeight: 700, textDecoration: 'underline' }}>Pejabat Pembuat Komitmen</p>
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
