import { useState, useEffect, useRef } from 'react';
import { X, Calendar, CheckCircle2, CircleDot, Users, FileText, Upload, Award, DollarSign, Download, Save, MessageCircle, AlertCircle, HandCoins } from 'lucide-react';
import { formatRupiah, StatusBadge } from '../ui/shared';
import { statusConfig, methodConfig } from '../../data/mockData';
import { procurementPhases, getTenderPhaseIndex } from '../../data/procurementPhases';
import { useApp, API_BASE } from '../../context/AppContext';
import clsx from 'clsx';
import ObjectionsTab from './ObjectionsTab';
import ContractTab from './ContractTab';
import AanwijzingTab from './AanwijzingTab';
import NegotiationTab from './NegotiationTab';

function VendorQualModal({ vendorId, vendorName, onClose }) {
  const { getAuthHeaders } = useApp();
  const [qual, setQual] = useState(null);
  
  useEffect(() => {
    fetch(`${API_BASE}/vendors/${vendorId}/qualifications`, { headers: getAuthHeaders() })
      .then(res => res.json())
      .then(json => { if (json.success) setQual(json.data); })
      .catch(console.error);
  }, [vendorId]);

  if (!qual) return null;

  return (
    <div className="fixed inset-0 z-[60] flex items-center justify-center p-4 bg-black/50 backdrop-blur-sm">
      <div className="bg-white rounded-xl shadow-2xl w-full max-w-3xl flex flex-col max-h-[85vh]">
        <div className="flex items-center justify-between p-4 border-b border-border bg-surface">
          <h2 className="font-bold text-dpbj-navy">Profil Kualifikasi: {vendorName}</h2>
          <button onClick={onClose} className="p-1 hover:bg-white rounded"><X size={18}/></button>
        </div>
        <div className="overflow-y-auto p-5 space-y-6">
          <div>
            <h3 className="font-bold text-sm mb-3">Dokumen Legalitas</h3>
            <table className="w-full text-left text-xs border border-border">
              <thead className="bg-surface border-b border-border">
                <tr><th>Jenis</th><th>Nomor</th><th>Tgl Terbit</th><th>Status</th><th>File</th></tr>
              </thead>
              <tbody className="divide-y divide-border">
                {qual.documents.map(d => (
                  <tr key={d.id}>
                    <td className="p-2 font-bold uppercase">{d.doc_type}</td>
                    <td className="p-2">{d.doc_number}</td>
                    <td className="p-2">{d.issue_date?.split('T')[0]}</td>
                    <td className="p-2">{d.status}</td>
                    <td className="p-2"><a href={`http://localhost:3001${d.file_path}`} target="_blank" rel="noreferrer" className="text-blue-600 underline">Lihat</a></td>
                  </tr>
                ))}
              </tbody>
            </table>
          </div>
          <div>
            <h3 className="font-bold text-sm mb-3">Pengalaman Kerja</h3>
            <table className="w-full text-left text-xs border border-border">
              <thead className="bg-surface border-b border-border">
                <tr><th>Pekerjaan</th><th>Klien</th><th>Nilai</th><th>Pelaksanaan</th></tr>
              </thead>
              <tbody className="divide-y divide-border">
                {qual.experiences.map(e => (
                  <tr key={e.id}>
                    <td className="p-2 font-semibold">{e.project_name}</td>
                    <td className="p-2">{e.client_name}</td>
                    <td className="p-2">{formatRupiah(e.contract_value, true)}</td>
                    <td className="p-2">{e.start_date?.split('T')[0]} - {e.end_date?.split('T')[0]}</td>
                  </tr>
                ))}
              </tbody>
            </table>
          </div>
          
          {qual.pajak && qual.pajak.length > 0 && (
            <div>
              <h3 className="font-bold text-sm mb-3">Data Pajak (SPT/Tahunan)</h3>
              <table className="w-full text-left text-xs border border-border">
                <thead className="bg-surface border-b border-border">
                  <tr><th className="p-2">Jenis Pajak</th><th className="p-2">Masa Pajak</th><th className="p-2">No BPS</th><th className="p-2">Tgl BPS</th></tr>
                </thead>
                <tbody className="divide-y divide-border">
                  {qual.pajak.map((p, i) => (
                    <tr key={i}>
                      <td className="p-2 font-semibold">{p.jenis_pajak}</td>
                      <td className="p-2">{p.masa_pajak}</td>
                      <td className="p-2">{p.no_bukti}</td>
                      <td className="p-2">{p.tanggal_bukti}</td>
                    </tr>
                  ))}
                </tbody>
              </table>
            </div>
          )}

          {qual.tenaga_ahli && qual.tenaga_ahli.length > 0 && (
            <div>
              <h3 className="font-bold text-sm mb-3">Tenaga Ahli</h3>
              <table className="w-full text-left text-xs border border-border">
                <thead className="bg-surface border-b border-border">
                  <tr><th className="p-2">Nama</th><th className="p-2">Pendidikan</th><th className="p-2">Profesi</th><th className="p-2">Pengalaman</th></tr>
                </thead>
                <tbody className="divide-y divide-border">
                  {qual.tenaga_ahli.map((a, i) => (
                    <tr key={i}>
                      <td className="p-2 font-semibold">{a.nama}</td>
                      <td className="p-2">{a.pendidikan}</td>
                      <td className="p-2">{a.profesi}</td>
                      <td className="p-2">{a.pengalaman_tahun} Tahun</td>
                    </tr>
                  ))}
                </tbody>
              </table>
            </div>
          )}

          {qual.peralatan && qual.peralatan.length > 0 && (
            <div>
              <h3 className="font-bold text-sm mb-3">Alat Berat / Peralatan Utama</h3>
              <table className="w-full text-left text-xs border border-border">
                <thead className="bg-surface border-b border-border">
                  <tr><th className="p-2">Alat</th><th className="p-2">Merk</th><th className="p-2">Jumlah / Kapasitas</th><th className="p-2">Kondisi / Milik</th></tr>
                </thead>
                <tbody className="divide-y divide-border">
                  {qual.peralatan.map((p, i) => (
                    <tr key={i}>
                      <td className="p-2 font-semibold">{p.nama_alat}</td>
                      <td className="p-2">{p.merk}</td>
                      <td className="p-2">{p.jumlah} ({p.kapasitas})</td>
                      <td className="p-2">{p.kondisi}% - {p.kepemilikan}</td>
                    </tr>
                  ))}
                </tbody>
              </table>
            </div>
          )}

          {qual.pengurus && qual.pengurus.length > 0 && (
            <div>
              <h3 className="font-bold text-sm mb-3">Susunan Pengurus & Pemegang Saham</h3>
              <table className="w-full text-left text-xs border border-border">
                <thead className="bg-surface border-b border-border">
                  <tr><th className="p-2">Nama</th><th className="p-2">No Identitas</th><th className="p-2">Jabatan</th><th className="p-2">Saham</th></tr>
                </thead>
                <tbody className="divide-y divide-border">
                  {qual.pengurus.map((p, i) => (
                    <tr key={i}>
                      <td className="p-2 font-semibold">{p.nama}</td>
                      <td className="p-2">{p.no_ktp}</td>
                      <td className="p-2">{p.jabatan}</td>
                      <td className="p-2">{p.saham_persen}%</td>
                    </tr>
                  ))}
                </tbody>
              </table>
            </div>
          )}

        </div>
      </div>
    </div>
  );
}

function VendorBidForm({ tenderId, onClose, refreshData }) {
  const { user, getAuthHeaders } = useApp();
  const [bidPrice, setBidPrice] = useState('');
  const [file, setFile] = useState(null);
  const [isSubmitting, setIsSubmitting] = useState(false);
  const fileInputRef = useRef(null);

  const handleSubmit = async (e) => {
    e.preventDefault();
    if (!bidPrice || !file) return alert('Lengkapi harga penawaran dan dokumen!');
    
    setIsSubmitting(true);
    const formData = new FormData();
    formData.append('vendor_id', user.id);
    formData.append('bid_price', bidPrice.replace(/\D/g, ''));
    formData.append('document', file);

    try {
      const res = await fetch(`${API_BASE}/tenders/${tenderId}/bids`, {
        method: 'POST',
        headers: { Authorization: `Bearer ${localStorage.getItem('eproc_token')}` },
        body: formData
      });
      const json = await res.json();
      if (json.success) {
        alert('Penawaran berhasil dikirim!');
        refreshData();
        onClose();
      } else {
        alert('Gagal: ' + json.message);
      }
    } catch (err) {
      alert('Error: ' + err.message);
    } finally {
      setIsSubmitting(false);
    }
  };

  return (
    <div className="bg-surface border border-border p-5 rounded-xl mt-4">
      <h3 className="font-bold text-dpbj-navy text-sm mb-4 flex items-center gap-2">
        <Upload size={16} className="text-purple-600" /> Form Pemasukan Dokumen Penawaran
      </h3>
      <form onSubmit={handleSubmit} className="space-y-4">
        <div>
          <label className="block text-xs font-semibold text-muted mb-1">Nilai Penawaran (Rp)</label>
          <div className="relative">
            <span className="absolute left-3 top-1/2 -translate-y-1/2 text-gray-500 font-bold">Rp</span>
            <input 
              type="text" 
              className="form-input w-full pl-9 font-semibold text-dpbj-navy" 
              placeholder="0"
              value={bidPrice}
              onChange={(e) => {
                const val = e.target.value.replace(/\D/g, '');
                setBidPrice(val ? parseInt(val).toLocaleString('id-ID') : '');
              }}
            />
          </div>
        </div>
        <div>
          <label className="block text-xs font-semibold text-muted mb-1">Dokumen Penawaran Teknis & Harga (PDF/ZIP)</label>
          <input 
            type="file" 
            ref={fileInputRef}
            className="form-input w-full text-sm text-dpbj-navy p-2 bg-white" 
            accept=".pdf,.zip,.rar"
            onChange={(e) => setFile(e.target.files[0])}
          />
          <p className="text-[10px] text-gray-400 mt-1">*Maksimal 10MB.</p>
        </div>
        <button type="submit" disabled={isSubmitting} className="btn-primary w-full justify-center bg-purple-600 hover:bg-purple-700 text-white shadow-md shadow-purple-600/20">
          {isSubmitting ? 'Mengirim...' : 'Kirim Penawaran Sekarang'}
        </button>
      </form>
    </div>
  );
}

function PokjaEvaluationTable({ tenderId, participants, tenderStatus, refreshData }) {
  const { getAuthHeaders } = useApp();
  const [evaluating, setEvaluating] = useState(null);
  
  const handleSaveEvaluation = async (vendorId, isPassed) => {
    const data = evaluating[vendorId];
    if (!data || !data.technical_score) return alert('Isi skor teknis terlebih dahulu!');
    
    try {
      const res = await fetch(`${API_BASE}/tenders/${tenderId}/participants/${vendorId}/evaluate`, {
        method: 'PATCH',
        headers: getAuthHeaders(),
        body: JSON.stringify({ 
          technical_score: data.technical_score,
          evaluation_notes: data.notes || '',
          is_passed: isPassed
        })
      });
      const json = await res.json();
      if (json.success) {
        alert('Evaluasi tersimpan!');
        refreshData();
      } else {
        alert('Gagal: ' + json.message);
      }
    } catch(err) {
      alert('Error: ' + err.message);
    }
  };

  const handleSetWinner = async (vendorId) => {
    if(!confirm('Tetapkan vendor ini sebagai pemenang lelang?')) return;
    try {
      const res = await fetch(`${API_BASE}/tenders/${tenderId}/winner`, {
        method: 'POST',
        headers: getAuthHeaders(),
        body: JSON.stringify({ vendor_id: vendorId })
      });
      const json = await res.json();
      if (json.success) {
        alert('Pemenang ditetapkan!');
        refreshData();
      } else {
        alert('Gagal: ' + json.message);
      }
    } catch(err) {
      alert('Error: ' + err.message);
    }
  };

  return (
    <div className="space-y-4 animate-fade-in">
      <h3 className="font-bold text-dpbj-navy text-sm">Peserta & Evaluasi Penawaran</h3>
      {participants.length === 0 ? (
        <p className="text-sm text-muted">Belum ada vendor yang mendaftar pada tender ini.</p>
      ) : (
        <div className="border border-border rounded-xl overflow-hidden bg-white">
          <table className="w-full text-left text-sm">
            <thead className="bg-surface border-b border-border">
              <tr>
                <th className="px-4 py-3 font-semibold text-dpbj-navy text-xs">Peserta</th>
                <th className="px-4 py-3 font-semibold text-dpbj-navy text-xs">Nilai Penawaran</th>
                <th className="px-4 py-3 font-semibold text-dpbj-navy text-xs">Dokumen</th>
                <th className="px-4 py-3 font-semibold text-dpbj-navy text-xs">Evaluasi & Pemenang</th>
              </tr>
            </thead>
            <tbody className="divide-y divide-border">
              {participants.map(p => (
                <tr key={p.id} className={clsx(p.is_winner && "bg-emerald-50/50")}>
                  <td className="px-4 py-4">
                    <p className="font-bold text-dpbj-navy">{p.company_name}</p>
                    <p className="text-[10px] text-muted mb-1">{p.city || '-'}</p>
                    <button onClick={() => setEvaluating({ ...evaluating, viewQual: p })} className="text-[10px] text-blue-600 hover:underline font-semibold flex items-center gap-1">
                      <FileText size={10} /> Lihat Kualifikasi
                    </button>
                    {p.is_winner && (
                      <span className="inline-flex items-center gap-1 mt-2 text-[10px] font-bold px-2 py-0.5 rounded-full bg-emerald-100 text-emerald-700">
                        <Award size={10} /> PEMENANG
                      </span>
                    )}
                  </td>
                  <td className="px-4 py-4">
                    {p.bid_price ? (
                      <p className="font-bold text-dpbj-navy">{formatRupiah(p.bid_price, true)}</p>
                    ) : (
                      <span className="text-xs text-gray-400">Belum menawar</span>
                    )}
                  </td>
                  <td className="px-4 py-4">
                    {p.document_path ? (
                      <a href={`http://localhost:3001${p.document_path}`} target="_blank" rel="noreferrer" className="inline-flex items-center gap-1.5 text-xs font-semibold text-blue-600 bg-blue-50 hover:bg-blue-100 px-2.5 py-1.5 rounded-lg transition-colors">
                        <Download size={14} /> Unduh
                      </a>
                    ) : (
                      <span className="text-xs text-gray-400">-</span>
                    )}
                  </td>
                  <td className="px-4 py-4">
                    {tenderStatus === 'evaluasi' || tenderStatus === 'pemenang' ? (
                      p.is_evaluated ? (
                        <div className="text-xs">
                          <p className="font-semibold text-dpbj-navy">Skor: {p.technical_score}</p>
                          <p className={clsx("font-bold mt-0.5", p.status === 'passed' || p.status === 'winner' ? "text-emerald-600" : "text-red-600")}>
                            {p.status === 'passed' || p.status === 'winner' ? 'LULUS EVALUASI' : 'GUGUR'}
                          </p>
                          {tenderStatus === 'pemenang' && (p.status === 'passed' || p.status === 'winner') && !p.is_winner && (
                            <button onClick={() => handleSetWinner(p.vendor_id)} className="mt-2 text-[10px] font-bold bg-amber-100 text-amber-700 px-2 py-1 rounded hover:bg-amber-200 transition-colors">
                              JADIKAN PEMENANG
                            </button>
                          )}
                        </div>
                      ) : (
                        tenderStatus === 'evaluasi' && p.bid_price ? (
                          <div className="space-y-2 bg-surface p-2 rounded-lg border border-border">
                            <input 
                              type="number" 
                              placeholder="Skor (0-100)" 
                              className="w-full text-xs p-1.5 border border-border rounded"
                              onChange={(e) => setEvaluating({...evaluating, [p.vendor_id]: { ...evaluating?.[p.vendor_id], technical_score: e.target.value }})}
                            />
                            <div className="flex gap-2">
                              <button onClick={() => handleSaveEvaluation(p.vendor_id, true)} className="flex-1 text-[10px] font-bold bg-emerald-600 text-white py-1 rounded hover:bg-emerald-700">LULUS</button>
                              <button onClick={() => handleSaveEvaluation(p.vendor_id, false)} className="flex-1 text-[10px] font-bold bg-red-600 text-white py-1 rounded hover:bg-red-700">GUGUR</button>
                            </div>
                          </div>
                        ) : (
                          <span className="text-xs text-muted">Menunggu evaluasi</span>
                        )
                      )
                    ) : (
                      <span className="text-xs text-gray-400">Tahap evaluasi belum dimulai</span>
                    )}
                  </td>
                </tr>
              ))}
            </tbody>
          </table>
        </div>
      )}
      {evaluating?.viewQual && (
        <VendorQualModal vendorId={evaluating.viewQual.vendor_id} vendorName={evaluating.viewQual.company_name} onClose={() => setEvaluating({ ...evaluating, viewQual: null })} />
      )}
    </div>
  );
}

export default function DetailTenderModal({ isOpen, onClose, data }) {
  const { user, getAuthHeaders, refreshData } = useApp();
  const [activeTab, setActiveTab] = useState('detail');
  const [participants, setParticipants] = useState([]);
  const [isRegistering, setIsRegistering] = useState(false);
  const [isUpdatingStage, setIsUpdatingStage] = useState(false);
  const [showBidForm, setShowBidForm] = useState(false);

  useEffect(() => {
    if (isOpen && data && (user.role === 'pokja' || user.role === 'admin' || user.role === 'ppk')) {
      fetch(`${API_BASE}/tenders/${data.id}/participants`, { headers: getAuthHeaders() })
        .then(res => res.json())
        .then(json => {
          if (json.success) setParticipants(json.data);
        })
        .catch(console.error);
    }
  }, [isOpen, data, user.role, activeTab]);

  if (!isOpen || !data) return null;

  const currentPhaseIndex = getTenderPhaseIndex(data.status);

  const handleUpdateStage = async (newStatus) => {
    if (!confirm(`Ubah tahapan tender menjadi: ${newStatus}?`)) return;
    setIsUpdatingStage(true);
    try {
      const res = await fetch(`${API_BASE}/tenders/${data.id}/stage`, {
        method: 'PATCH',
        headers: getAuthHeaders(),
        body: JSON.stringify({ status: newStatus })
      });
      const json = await res.json();
      if (json.success) {
        alert(json.message);
        refreshData();
        onClose();
      } else {
        alert('Gagal: ' + json.message);
      }
    } catch (err) {
      alert('Error: ' + err.message);
    } finally {
      setIsUpdatingStage(false);
    }
  };

  const handleRegister = async () => {
    if (!confirm('Anda yakin ingin mendaftar ke tender ini?')) return;
    setIsRegistering(true);
    try {
      const res = await fetch(`${API_BASE}/tenders/${data.id}/register`, {
        method: 'POST',
        headers: getAuthHeaders(),
        body: JSON.stringify({ vendor_id: user.id })
      });
      const json = await res.json();
      if (json.success) {
        alert(json.message);
        refreshData();
        onClose();
      } else {
        alert('Gagal: ' + json.message);
      }
    } catch (err) {
      alert('Error: ' + err.message);
    } finally {
      setIsRegistering(false);
    }
  };

  return (
    <div className="fixed inset-0 z-50 flex items-center justify-center p-4 bg-dpbj-navy/50 backdrop-blur-sm animate-fade-in">
      <div className="bg-white rounded-2xl shadow-xl w-full max-w-6xl overflow-hidden flex flex-col max-h-[90vh]">
        
        {/* Header */}
        <div className="flex items-center justify-between p-5 border-b border-border bg-surface">
          <div>
            <h2 className="text-lg font-bold text-dpbj-navy">Detail Tender & Jadwal Pelaksanaan</h2>
            <p className="text-xs text-muted font-mono">{data.tender_number}</p>
          </div>
          <button onClick={onClose} className="p-2 text-muted hover:bg-white rounded-xl transition-colors border border-transparent hover:border-border">
            <X size={18} />
          </button>
        </div>

        {/* Tabs (Pokja/Admin/PPK) */}
        {['pokja', 'admin', 'ppk', 'vendor'].includes(user.role) && (
          <div className="flex px-6 pt-3 border-b border-border bg-white gap-6 overflow-x-auto">
            <button onClick={() => setActiveTab('detail')} className={clsx("pb-3 text-sm font-bold border-b-2 transition-colors flex items-center gap-2 whitespace-nowrap", activeTab === 'detail' ? "border-dpbj-gold text-dpbj-navy" : "border-transparent text-muted hover:text-dpbj-navy")}>
              <FileText size={16} /> Detail Tender
            </button>
            <button onClick={() => setActiveTab('peserta')} className={clsx("pb-3 text-sm font-bold border-b-2 transition-colors flex items-center gap-2 whitespace-nowrap", activeTab === 'peserta' ? "border-dpbj-gold text-dpbj-navy" : "border-transparent text-muted hover:text-dpbj-navy")}>
              <Users size={16} /> Peserta & Penawaran
              <span className="bg-surface border border-border text-xs px-2 py-0.5 rounded-full text-dpbj-slate">{participants.length}</span>
            </button>
            {getTenderPhaseIndex(data.status) >= 1 && (
              <button onClick={() => setActiveTab('aanwijzing')} className={clsx("pb-3 text-sm font-bold border-b-2 transition-colors flex items-center gap-2 whitespace-nowrap", activeTab === 'aanwijzing' ? "border-dpbj-gold text-dpbj-navy" : "border-transparent text-muted hover:text-dpbj-navy")}>
                <MessageCircle size={16} /> Aanwijzing (Q&A)
              </button>
            )}
            {getTenderPhaseIndex(data.status) >= 5 && (
              <button onClick={() => setActiveTab('sanggah')} className={clsx("pb-3 text-sm font-bold border-b-2 transition-colors flex items-center gap-2 whitespace-nowrap", activeTab === 'sanggah' ? "border-dpbj-gold text-dpbj-navy" : "border-transparent text-muted hover:text-dpbj-navy")}>
                <AlertCircle size={16} /> Sanggahan
              </button>
            )}
            {getTenderPhaseIndex(data.status) >= 4 && (
              <button onClick={() => setActiveTab('negosiasi')} className={clsx("pb-3 text-sm font-bold border-b-2 transition-colors flex items-center gap-2 whitespace-nowrap", activeTab === 'negosiasi' ? "border-dpbj-gold text-dpbj-navy" : "border-transparent text-muted hover:text-dpbj-navy")}>
                <HandCoins size={16} /> Negosiasi
              </button>
            )}
            {getTenderPhaseIndex(data.status) >= 6 && (
              <button onClick={() => setActiveTab('kontrak')} className={clsx("pb-3 text-sm font-bold border-b-2 transition-colors flex items-center gap-2 whitespace-nowrap", activeTab === 'kontrak' ? "border-dpbj-gold text-dpbj-navy" : "border-transparent text-muted hover:text-dpbj-navy")}>
                <Award size={16} /> Kontrak & BAST
              </button>
            )}
          </div>
        )}

        <div className="flex-1 overflow-y-auto p-6 bg-white flex flex-col lg:flex-row gap-8">
          
          {/* Left Column: Content */}
          <div className="flex-1 min-w-0">
            {activeTab === 'detail' && (
              <div className="space-y-5">
                <div>
                  <p className="text-xs text-muted font-medium mb-1">Judul Tender</p>
                  <p className="font-bold text-base text-dpbj-navy leading-snug">{data.title}</p>
                </div>
                
                <div className="grid grid-cols-2 gap-4">
                  <div>
                    <p className="text-xs text-muted font-medium mb-1">Status</p>
                    <StatusBadge status={data.status} config={statusConfig} />
                  </div>
                  <div>
                    <p className="text-xs text-muted font-medium mb-1">Metode Pemilihan</p>
                    <div className="flex items-center gap-2 text-xs">
                      {methodConfig[data.method]?.icon && <span className="text-dpbj-gold">{methodConfig[data.method].icon({size: 14})}</span>}
                      <span className="font-semibold text-dpbj-navy">{methodConfig[data.method]?.label || data.method}</span>
                    </div>
                  </div>
                  <div>
                    <p className="text-xs text-muted font-medium mb-1">Kategori</p>
                    <p className="font-semibold text-sm text-dpbj-navy">{data.category || '-'}</p>
                  </div>
                  <div>
                    <p className="text-xs text-muted font-medium mb-1">Unit Kerja</p>
                    <p className="font-semibold text-sm text-dpbj-navy truncate">{data.unit_kerja || 'Universitas Indonesia'}</p>
                  </div>
                  <div className="col-span-2 bg-surface p-4 rounded-xl border border-border">
                    <div className="grid grid-cols-2 gap-4">
                      <div>
                        <p className="text-xs text-muted font-medium mb-1">Nilai Pagu Paket</p>
                        <p className="font-bold text-sm text-dpbj-navy">{formatRupiah(data.pagu_anggaran, true)}</p>
                      </div>
                      <div>
                        <p className="text-xs text-muted font-medium mb-1">Nilai HPS Paket</p>
                        <p className="font-bold text-sm text-dpbj-navy">{data.hps ? formatRupiah(data.hps, true) : '-'}</p>
                      </div>
                    </div>
                  </div>
                </div>

                <div className="border-t border-border pt-4">
                  <p className="text-xs text-muted font-medium mb-2">Syarat Kualifikasi & Dokumen</p>
                  <div className="bg-surface rounded-xl p-4 text-sm text-dpbj-navy leading-relaxed min-h-[100px] border border-border">
                    {data.description || 'Sesuai dengan Dokumen Pemilihan. Silakan download dokumen pengadaan untuk detail syarat kualifikasi, KAK, dan rancangan kontrak.'}
                  </div>
                </div>

                {/* Form Pemasukan Penawaran untuk Vendor */}
                {user.role === 'vendor' && showBidForm && (
                  <VendorBidForm tenderId={data.id} onClose={onClose} refreshData={refreshData} />
                )}
              </div>
            )}
            {activeTab === 'peserta' && (
              <PokjaEvaluationTable tenderId={data.id} tenderStatus={data.status} participants={participants} refreshData={refreshData} />
            )}
            {activeTab === 'aanwijzing' && (
              <AanwijzingTab tenderId={data.id} user={user} getAuthHeaders={getAuthHeaders} />
            )}
            {activeTab === 'sanggah' && (
              <ObjectionsTab tenderId={data.id} tenderStatus={data.status} participants={participants} user={user} />
            )}
            {activeTab === 'negosiasi' && (
              <NegotiationTab
                tenderId={data.id}
                vendorId={participants.find(p => p.is_winner)?.vendor_id}
                user={user}
                getAuthHeaders={getAuthHeaders}
                refreshData={refreshData}
              />
            )}
            {activeTab === 'kontrak' && (
              <ContractTab tenderId={data.id} tenderStatus={data.status} participants={participants} user={user} />
            )}
          </div>

          {/* Right Column: Timeline / Stepper */}
          <div className="w-full lg:w-80 flex-shrink-0 bg-surface border border-border rounded-2xl p-5 flex flex-col">
            <h3 className="font-bold text-dpbj-navy text-sm mb-5">Jadwal Tahapan Tender</h3>
            <div className="relative border-l border-gray-200 ml-3 space-y-6 flex-1">
              {procurementPhases.map((phase, idx) => {
                const isCompleted = currentPhaseIndex > idx;
                const isCurrent = currentPhaseIndex === idx;
                
                return (
                  <div key={phase.id} className="relative pl-6">
                    <span className="absolute -left-[11px] top-1 bg-surface">
                      {isCompleted ? (
                        <CheckCircle2 size={20} className="text-emerald-500 bg-white rounded-full" />
                      ) : isCurrent ? (
                        <CircleDot size={20} className="text-dpbj-gold bg-white rounded-full animate-pulse" />
                      ) : (
                        <div className="w-5 h-5 rounded-full border-2 border-gray-300 bg-white ml-[1px]" />
                      )}
                    </span>
                    <h4 className={`text-xs font-bold leading-tight ${isCurrent ? 'text-dpbj-navy' : isCompleted ? 'text-gray-600' : 'text-gray-400'}`}>
                      {phase.label}
                    </h4>
                    {isCurrent && (
                      <p className="text-[10px] text-dpbj-gold-dark font-medium mt-1">Tahap Aktif Saat Ini</p>
                    )}
                  </div>
                );
              })}
            </div>
            
            {/* Contextual Actions based on Role & Stage */}
            <div className="mt-6 pt-5 border-t border-border space-y-3">
              {/* Pokja Actions */}
              {user.role === 'pokja' && data.status === 'draft' && (
                <button onClick={() => handleUpdateStage('pengumuman')} disabled={isUpdatingStage} className="btn-primary w-full justify-center text-xs shadow-md">Umumkan Tender</button>
              )}
              {user.role === 'pokja' && data.status === 'pengumuman' && (
                <button onClick={() => handleUpdateStage('pendaftaran')} disabled={isUpdatingStage} className="btn-primary w-full justify-center text-xs shadow-md">Buka Tahap Pendaftaran</button>
              )}
              {user.role === 'pokja' && data.status === 'pendaftaran' && (
                <button onClick={() => handleUpdateStage('penawaran')} disabled={isUpdatingStage} className="btn-primary w-full justify-center text-xs shadow-md">Mulai Tahap Penawaran</button>
              )}
              {user.role === 'pokja' && data.status === 'penawaran' && (
                <button onClick={() => handleUpdateStage('evaluasi')} disabled={isUpdatingStage} className="btn-primary w-full justify-center text-xs shadow-md">Tutup & Mulai Evaluasi</button>
              )}
              {user.role === 'pokja' && data.status === 'evaluasi' && (
                <button onClick={() => handleUpdateStage('pemenang')} disabled={isUpdatingStage} className="btn-primary w-full justify-center text-xs bg-amber-600 hover:bg-amber-700 text-white shadow-md">Masuk Tahap Pemenang</button>
              )}
              {user.role === 'pokja' && data.status === 'pemenang' && (
                <button onClick={() => handleUpdateStage('selesai')} disabled={isUpdatingStage} className="btn-primary w-full justify-center text-xs bg-emerald-600 hover:bg-emerald-700 text-white shadow-md">Selesaikan Tender</button>
              )}

              {/* Vendor Actions */}
              {user.role === 'vendor' && data.status === 'pendaftaran' && (
                <button onClick={handleRegister} disabled={isRegistering} className="btn-primary w-full justify-center bg-blue-600 hover:bg-blue-700 text-white shadow-md">Daftar Tender Ini</button>
              )}
              {user.role === 'vendor' && data.status === 'penawaran' && !showBidForm && (
                <button onClick={() => setShowBidForm(true)} className="btn-primary w-full justify-center bg-purple-600 hover:bg-purple-700 text-white shadow-md">Kirim Dokumen Penawaran</button>
              )}
            </div>
          </div>

        </div>

        <div className="p-5 border-t border-border bg-surface flex justify-end gap-3">
          <button onClick={onClose} className="btn-ghost">Tutup</button>
        </div>
      </div>
    </div>
  );
}
