import { useState, useEffect } from 'react';
import { useApp, API_BASE } from '../../context/AppContext';
import { Download, Award, ShieldCheck, Star, CheckCircle2, QrCode } from 'lucide-react';
import { PaymentTermsSection, PenaltiesSection, DeliverablesSection } from './ContractDetailSections';
import { formatRupiah } from '../ui/shared';
import { format } from 'date-fns';

export default function ContractTab({ tenderId, tenderStatus, participants, user }) {
  const { getAuthHeaders } = useApp();
  const [contract, setContract] = useState(null);
  const [loading, setLoading] = useState(true);
  const [existingRating, setExistingRating] = useState(null);
  
  const [form, setForm] = useState({
    contract_number: '',
    contract_date: '',
    contract_value: '',
  });
  const [files, setFiles] = useState({ spk: null, bast: null });
  const [isSubmitting, setIsSubmitting] = useState(false);
  const [qrData, setQrData] = useState(null);
  const [generatingQr, setGeneratingQr] = useState(false);

  const fetchContract = async () => {
    try {
      const res = await fetch(`${API_BASE}/tenders/${tenderId}/contract`, { headers: getAuthHeaders() });
      const json = await res.json();
      if (json.success && json.data) {
        setContract(json.data);
        setForm({
          contract_number: json.data.contract_number || '',
          contract_date: json.data.contract_date ? format(new Date(json.data.contract_date), 'yyyy-MM-dd') : '',
          contract_value: json.data.contract_value || '',
        });
      }

      // Cek rating jika PPK
      if (user.role === 'ppk' && participants.find(p => p.is_winner)) {
        const vendorId = participants.find(p => p.is_winner).vendor_id;
        const ratingRes = await fetch(`${API_BASE}/vendors/${vendorId}/rating/${tenderId}`, { headers: getAuthHeaders() });
        const ratingJson = await ratingRes.json();
        if (ratingJson.success && ratingJson.data) {
          setExistingRating(ratingJson.data);
        }
      }
    } catch (err) {
      console.error(err);
    } finally {
      setLoading(false);
    }
  };

  useEffect(() => {
    fetchContract();
  }, [tenderId]);

  const winner = participants.find(p => p.is_winner);

  const handleSubmit = async (e) => {
    e.preventDefault();
    if (!winner) return alert('Pemenang belum ditetapkan!');
    try {
      setIsSubmitting(true);
      const valueNum = Number(String(form.contract_value).replace(/\./g, ''));

      const formData = new FormData();
      formData.append('vendor_id', winner.vendor_id);
      formData.append('contract_number', form.contract_number);
      formData.append('contract_date', form.contract_date);
      formData.append('contract_value', valueNum);
      if (files.spk) formData.append('spk', files.spk);
      if (files.bast) formData.append('bast', files.bast);

      // Jika BAST diupload, kita asumsikan status completed
      if (files.bast || contract?.bast_path) {
        formData.append('status', 'completed');
      } else if (files.spk || contract?.spk_path) {
        formData.append('status', 'signed');
      }

      const res = await fetch(`${API_BASE}/tenders/${tenderId}/contract`, {
        method: 'POST',
        headers: (() => { const h = getAuthHeaders(); delete h['Content-Type']; return h; })(),
        body: formData
      });
      const json = await res.json();
      if (json.success) {
        alert('Data kontrak berhasil disimpan.');
        fetchContract();
      } else {
        alert(json.message);
      }
    } catch (err) {
      alert(err.message);
    } finally {
      setIsSubmitting(false);
    }
  };

  const handleGenerateQr = async () => {
    setGeneratingQr(true);
    try {
      const res = await fetch(`${API_BASE}/qr/generate`, {
        method: 'POST',
        headers: getAuthHeaders(),
        body: JSON.stringify({
          source_type: 'kontrak',
          tender_id: tenderId,
          vendor_id: winner?.vendor_id,
          info: `Kontrak ${form.contract_number || ''} - ${winner?.company_name || ''}`,
          created_by: user.id,
        }),
      });
      const json = await res.json();
      if (json.success) setQrData(json.data);
      else alert('Gagal membuat kode QR: ' + json.message);
    } catch {
      alert('Terjadi kesalahan saat membuat kode QR.');
    } finally {
      setGeneratingQr(false);
    }
  };

  if (loading) return <div className="p-4 text-center text-sm">Loading...</div>;

  if (!winner) {
    return (
      <div className="p-8 text-center border border-border rounded-xl bg-surface">
        <ShieldCheck size={40} className="mx-auto text-gray-400 mb-3" />
        <p className="text-sm font-semibold text-gray-600">Pemenang Belum Ditetapkan</p>
        <p className="text-xs text-gray-500 mt-1">Modul kontrak baru bisa diakses setelah Pokja menetapkan pemenang tender.</p>
      </div>
    );
  }

  const isVendorWinner = user.role === 'vendor' && user.id === winner.vendor_id;

  return (
    <div className="space-y-6">
      <div className="bg-emerald-50 border border-emerald-200 p-4 rounded-xl flex items-start gap-3">
        <Award className="text-emerald-600 mt-0.5 shrink-0" size={20} />
        <div>
          <h3 className="font-bold text-emerald-800 text-sm">Informasi Pemenang & Kontrak</h3>
          <p className="text-xs text-emerald-700 mt-1">Pemenang Tender: <strong>{winner.company_name}</strong>. Nilai Penawaran: <strong>{formatRupiah(winner.bid_price, true)}</strong></p>
        </div>
      </div>

      {user.role === 'ppk' && (
        <form onSubmit={handleSubmit} className="border border-border rounded-xl p-5 bg-white shadow-sm space-y-4">
          <h4 className="font-bold text-sm text-dpbj-navy border-b border-border pb-2">Formulir Kontrak (PPK)</h4>
          <div className="grid grid-cols-2 gap-4">
            <div>
              <label className="block text-xs font-semibold text-dpbj-navy mb-1">Nomor SPK / Kontrak</label>
              <input type="text" className="form-input w-full text-sm" required value={form.contract_number} onChange={e => setForm({...form, contract_number: e.target.value})} placeholder="Contoh: SPK/001/DPBJ/2026"/>
            </div>
            <div>
              <label className="block text-xs font-semibold text-dpbj-navy mb-1">Tanggal Kontrak</label>
              <input type="date" className="form-input w-full text-sm" required value={form.contract_date} onChange={e => setForm({...form, contract_date: e.target.value})}/>
            </div>
            <div className="col-span-2">
              <label className="block text-xs font-semibold text-dpbj-navy mb-1">Nilai Final Kontrak (Rp)</label>
              <input type="text" className="form-input w-full text-sm" required value={form.contract_value} onChange={e => setForm({...form, contract_value: e.target.value.replace(/\D/g, '')})} placeholder="Contoh: 150000000"/>
              <p className="text-[10px] text-muted mt-1">Nilai kontrak riil hasil negosiasi akhir dengan pemenang.</p>
            </div>
          </div>

          <div className="grid grid-cols-2 gap-4 border-t border-border pt-4 mt-2">
            <div>
              <label className="block text-xs font-semibold text-dpbj-navy mb-1">Upload Draft / Final SPK</label>
              {contract?.spk_path && <p className="text-[10px] text-emerald-600 mb-1 flex items-center gap-1"><CheckCircle2 size={12}/> Sudah ada file SPK tersimpan.</p>}
              <input type="file" className="form-input w-full text-xs" onChange={e => setFiles({...files, spk: e.target.files[0]})} accept=".pdf,.doc,.docx" />
            </div>
            <div>
              <label className="block text-xs font-semibold text-dpbj-navy mb-1">Upload Dokumen BAST</label>
              {contract?.bast_path && <p className="text-[10px] text-emerald-600 mb-1 flex items-center gap-1"><CheckCircle2 size={12}/> Sudah ada file BAST tersimpan.</p>}
              <input type="file" className="form-input w-full text-xs" onChange={e => setFiles({...files, bast: e.target.files[0]})} accept=".pdf" />
              <p className="text-[10px] text-muted mt-1">Upload jika pekerjaan telah selesai.</p>
            </div>
          </div>

          <div className="flex justify-end pt-2">
            <button type="submit" disabled={isSubmitting} className="btn-primary">Simpan Data Kontrak</button>
          </div>
        </form>
      )}

      {/* Tampilan Read-Only untuk Pokja / Admin / Vendor Pemenang */}
      {contract && (user.role !== 'ppk') && (
        <div className="border border-border rounded-xl bg-surface p-5 space-y-4">
          <h4 className="font-bold text-sm text-dpbj-navy border-b border-border pb-2">Detail Kontrak</h4>
          <div className="grid grid-cols-2 gap-4">
            <div>
              <p className="text-xs text-muted font-medium mb-1">Nomor Kontrak / SPK</p>
              <p className="font-semibold text-sm text-dpbj-navy">{contract.contract_number}</p>
            </div>
            <div>
              <p className="text-xs text-muted font-medium mb-1">Tanggal Kontrak</p>
              <p className="font-semibold text-sm text-dpbj-navy">{contract.contract_date ? format(new Date(contract.contract_date), 'dd MMMM yyyy') : '-'}</p>
            </div>
            <div>
              <p className="text-xs text-muted font-medium mb-1">Nilai Final Kontrak</p>
              <p className="font-semibold text-sm text-dpbj-navy">{formatRupiah(contract.contract_value, true)}</p>
            </div>
            <div>
              <p className="text-xs text-muted font-medium mb-1">Status Pekerjaan</p>
              <span className={`text-xs font-bold px-2 py-1 rounded-full ${contract.status === 'completed' ? 'bg-emerald-100 text-emerald-700' : 'bg-blue-100 text-blue-700'}`}>
                {contract.status === 'completed' ? 'Selesai (BAST)' : 'Tanda Tangan Kontrak'}
              </span>
            </div>
          </div>

          {(contract.spk_path || contract.bast_path) && (
            <div className="border-t border-border pt-4 mt-2">
              <h4 className="font-bold text-xs text-dpbj-navy mb-3">Dokumen Tersedia:</h4>
              <div className="flex gap-4">
                {contract.spk_path && (
                  <a href={`http://localhost:3001${contract.spk_path}`} target="_blank" rel="noreferrer" className="flex items-center gap-2 bg-white border border-border px-3 py-2 rounded-lg text-sm font-semibold text-dpbj-navy hover:bg-gray-50 transition-colors">
                    <Download size={16} className="text-dpbj-gold"/> Unduh SPK
                  </a>
                )}
                {contract.bast_path && (
                  <a href={`http://localhost:3001${contract.bast_path}`} target="_blank" rel="noreferrer" className="flex items-center gap-2 bg-white border border-border px-3 py-2 rounded-lg text-sm font-semibold text-dpbj-navy hover:bg-gray-50 transition-colors">
                    <Download size={16} className="text-dpbj-gold"/> Unduh BAST
                  </a>
                )}
              </div>
            </div>
          )}
        </div>
      )}

      {!contract && user.role !== 'ppk' && (
        <div className="p-6 text-center text-sm text-muted bg-surface rounded-xl border border-border">
          Belum ada data kontrak yang diunggah oleh PPK.
        </div>
      )}

      {/* Termin pembayaran, sanksi keterlambatan, dan progres pekerjaan */}
      {contract && (
        <>
          <PaymentTermsSection tenderId={tenderId} canEdit={user.role === 'ppk' || user.role === 'admin'} />
          <DeliverablesSection tenderId={tenderId} canEdit={user.role === 'ppk' || user.role === 'admin'} />
          <PenaltiesSection tenderId={tenderId} canEdit={user.role === 'ppk' || user.role === 'admin'} />
        </>
      )}

      {/* Kode QR verifikasi keaslian dokumen kontrak (Admin/PPK) */}
      {contract && (user.role === 'ppk' || user.role === 'admin') && (
        <div className="border border-border rounded-xl p-5 bg-white shadow-sm space-y-3">
          <h4 className="font-bold text-sm text-dpbj-navy border-b border-border pb-2 flex items-center gap-2">
            <QrCode size={16} className="text-dpbj-gold" /> Kode QR Verifikasi Dokumen
          </h4>
          <p className="text-xs text-muted">Buat kode QR supaya siapapun bisa memindai dan memastikan dokumen kontrak ini asli.</p>
          {qrData ? (
            <div className="flex items-center gap-4 bg-surface p-4 rounded-xl">
              <img src={qrData.qr_image} alt="Kode QR" className="w-28 h-28 rounded-lg border border-border bg-white" />
              <div>
                <p className="text-xs text-muted">Kode:</p>
                <p className="font-mono font-bold text-dpbj-navy text-sm tracking-wider">{qrData.qr_code}</p>
                <p className="text-[10px] text-muted mt-2 break-all">{qrData.verify_url}</p>
              </div>
            </div>
          ) : (
            <button onClick={handleGenerateQr} disabled={generatingQr} className="btn-secondary flex items-center gap-2 disabled:opacity-50">
              <QrCode size={14} /> {generatingQr ? 'Membuat...' : 'Buat Kode QR'}
            </button>
          )}
        </div>
      )}

      {/* Form Penilaian Kinerja Vendor untuk PPK */}
      {user.role === 'ppk' && contract?.status === 'completed' && (
        <div className="border border-border rounded-xl p-5 bg-blue-50/50 shadow-sm space-y-4">
          <h4 className="font-bold text-sm text-blue-900 border-b border-blue-100 pb-2 flex items-center gap-2"><Star size={16} className="text-blue-600"/> Penilaian Kinerja Vendor</h4>
          {existingRating ? (
            <div className="space-y-2">
              <p className="text-xs text-blue-800 font-medium">Anda telah memberikan penilaian untuk vendor ini.</p>
              <div className="flex gap-1">
                {[1, 2, 3, 4, 5].map(star => (
                  <Star key={star} size={20} className={star <= existingRating.rating_score ? 'text-yellow-400 fill-yellow-400' : 'text-gray-300'} />
                ))}
              </div>
              <div className="bg-white p-3 rounded-lg border border-blue-100 text-sm text-blue-900 mt-2">
                "{existingRating.review_notes}"
              </div>
            </div>
          ) : (
            <form onSubmit={async (e) => {
              e.preventDefault();
              const score = parseInt(e.target.score.value);
              const notes = e.target.notes.value;
              try {
                setIsSubmitting(true);
                const res = await fetch(`${API_BASE}/vendors/${winner.vendor_id}/rating`, {
                  method: 'POST',
                  headers: { ...getAuthHeaders(), 'Content-Type': 'application/json' },
                  body: JSON.stringify({ tender_id: tenderId, ppk_id: user.id, rating_score: score, review_notes: notes })
                });
                const json = await res.json();
                if (json.success) {
                  alert('Penilaian berhasil dikirim.');
                  fetchContract();
                } else alert(json.message);
              } catch (err) {
                alert(err.message);
              } finally {
                setIsSubmitting(false);
              }
            }}>
              <div className="space-y-4">
                <div>
                  <label className="block text-xs font-semibold text-blue-900 mb-2">Bintang Penilaian (1-5)</label>
                  <div className="flex items-center gap-4">
                    {[1, 2, 3, 4, 5].map(num => (
                      <label key={num} className="flex flex-col items-center gap-1 cursor-pointer">
                        <input type="radio" name="score" value={num} required className="w-4 h-4 text-blue-600 border-gray-300 focus:ring-blue-500" />
                        <span className="text-xs font-bold text-gray-700">{num} <Star size={12} className="inline text-yellow-400 fill-yellow-400 -mt-1"/></span>
                      </label>
                    ))}
                  </div>
                </div>
                <div>
                  <label className="block text-xs font-semibold text-blue-900 mb-1">Catatan Kinerja / Testimoni</label>
                  <textarea name="notes" required className="form-input w-full text-sm h-20 bg-white" placeholder="Bagaimana hasil pekerjaan vendor ini?"></textarea>
                </div>
                <button type="submit" disabled={isSubmitting} className="btn-primary bg-blue-600 hover:bg-blue-700">Kirim Penilaian</button>
              </div>
            </form>
          )}
        </div>
      )}

    </div>
  );
}
