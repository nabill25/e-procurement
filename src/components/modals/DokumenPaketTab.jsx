import { useState, useEffect } from 'react';
import { FileText, Upload, Download, Trash2, MessageSquare, Mail, ShieldCheck, UserPlus, PackageOpen, Trophy, Briefcase, Search, CalendarClock, Unlock, Printer } from 'lucide-react';
import { API_BASE, SERVER_BASE } from '../../context/AppContext';
import { toast } from '../../lib/toast';

const DOC_TYPES = [
  { value: 'lelang', label: 'Dokumen Lelang' },
  { value: 'kualifikasi', label: 'Dokumen Kualifikasi' },
  { value: 'aritmatika', label: 'BA Koreksi Aritmatika' },
  { value: 'laporan', label: 'Laporan Paket' },
];

function Section({ icon: Icon, title, desc, action, children }) {
  return (
    <div className="bg-white border border-border rounded-xl overflow-hidden">
      <div className="flex items-center gap-3 p-4 bg-surface border-b border-border">
        <div className="w-8 h-8 rounded-lg bg-dpbj-navy/10 text-dpbj-navy flex items-center justify-center shrink-0">
          <Icon size={16} />
        </div>
        <div className="flex-1 min-w-0">
          <h4 className="font-bold text-dpbj-navy text-xs">{title}</h4>
          {desc && <p className="text-[10px] text-muted">{desc}</p>}
        </div>
        {action}
      </div>
      <div className="p-4">{children}</div>
    </div>
  );
}

export default function DokumenPaketTab({ tenderId, tenderStatus, participants, user, getAuthHeaders }) {
  const [documents, setDocuments] = useState([]);
  const [docType, setDocType] = useState('lelang');
  const [docFile, setDocFile] = useState(null);
  const [docName, setDocName] = useState('');

  const [klarifikasi, setKlarifikasi] = useState([]);
  const [klarFile, setKlarFile] = useState(null);

  const [pakta, setPakta] = useState([]);
  const [pihakLain, setPihakLain] = useState([]);
  const [peringkat, setPeringkat] = useState([]);
  const [newPeringkat, setNewPeringkat] = useState({ vendor_id: '', peringkat: '', keterangan: '' });

  const [bidangUsaha, setBidangUsaha] = useState([]);
  const [buSearch, setBuSearch] = useState('');
  const [buResults, setBuResults] = useState([]);

  const [pembukaan1, setPembukaan1] = useState([]);
  const [pembukaan2, setPembukaan2] = useState([]);
  const [pembukaanKode, setPembukaanKode] = useState('');

  const [undangan, setUndangan] = useState([]);
  const [newUndangan, setNewUndangan] = useState({ vendor_id: '', tanggal_undangan: '', jam: '', tempat: '', pelaksanaan: 'Tatap Muka', keterangan: '' });

  // Sebelumnya backend (dari Kelompok A) sudah punya endpoint pernyataan minat, tapi belum
  // ada satupun UI untuk mengisi/melihatnya - ditambahkan di sini sekalian waktu bikin halaman
  // cetaknya, supaya fiturnya benar-benar bisa dipakai ujung ke ujung, bukan cuma bisa dicetak
  // dokumen yang tidak pernah bisa diisi.
  const [myPernyataan, setMyPernyataan] = useState(undefined); // undefined = belum dicek, null = belum diisi
  const [pmForm, setPmForm] = useState({ nama: '', jabatan: '', alamat: '', telepon: '', email: '' });
  const [pmSaving, setPmSaving] = useState(false);

  const canManage = ['pokja', 'admin', 'ppk'].includes(user.role);
  const isVendor = user.role === 'vendor';

  const fetchAll = async () => {
    try {
      const [d, k, p, pl, pr, bu, p1, p2, uk] = await Promise.all([
        fetch(`${API_BASE}/tenders/${tenderId}/documents`, { headers: getAuthHeaders() }),
        fetch(`${API_BASE}/tenders/${tenderId}/klarifikasi-dokumen`, { headers: getAuthHeaders() }),
        fetch(`${API_BASE}/tenders/${tenderId}/pakta-integritas`, { headers: getAuthHeaders() }),
        fetch(`${API_BASE}/tenders/${tenderId}/pihak-lain`, { headers: getAuthHeaders() }),
        fetch(`${API_BASE}/tenders/${tenderId}/peringkat-pemenang`, { headers: getAuthHeaders() }),
        fetch(`${API_BASE}/tenders/${tenderId}/bidang-usaha`, { headers: getAuthHeaders() }),
        fetch(`${API_BASE}/tenders/${tenderId}/pembukaan/1`, { headers: getAuthHeaders() }),
        fetch(`${API_BASE}/tenders/${tenderId}/pembukaan/2`, { headers: getAuthHeaders() }),
        fetch(`${API_BASE}/tenders/${tenderId}/undangan-klarifikasi`, { headers: getAuthHeaders() }),
      ]);
      const [dj, kj, pj, plj, prj, buj, p1j, p2j, ukj] = await Promise.all([d.json(), k.json(), p.json(), pl.json(), pr.json(), bu.json(), p1.json(), p2.json(), uk.json()]);
      if (dj.success) setDocuments(dj.data);
      if (kj.success) setKlarifikasi(kj.data);
      if (pj.success) setPakta(pj.data);
      if (plj.success) setPihakLain(plj.data);
      if (prj.success) setPeringkat(prj.data);
      if (buj.success) setBidangUsaha(buj.data);
      if (p1j.success) setPembukaan1(p1j.data);
      if (p2j.success) setPembukaan2(p2j.data);
      if (ukj.success) setUndangan(ukj.data);

      if (isVendor) {
        const pmRes = await fetch(`${API_BASE}/tenders/${tenderId}/pernyataan-minat/${user.id}`, { headers: getAuthHeaders() });
        const pmJson = await pmRes.json();
        setMyPernyataan(pmJson.success && pmJson.data ? pmJson.data : null);
      }
    } catch (err) { console.error(err); }
  };

  const handleSubmitPernyataanMinat = async (e) => {
    e.preventDefault();
    if (!pmForm.nama.trim() || !pmForm.jabatan.trim() || !pmForm.alamat.trim()) {
      return toast('Nama, jabatan, dan alamat wajib diisi.');
    }
    setPmSaving(true);
    try {
      // Endpoint ini pakai multer (menerima file kuasa opsional), jadi harus FormData -
      // bukan JSON.stringify seperti kebanyakan endpoint lain.
      const fd = new FormData();
      fd.append('vendor_id', user.id);
      Object.entries(pmForm).forEach(([k, v]) => fd.append(k, v));
      const res = await fetch(`${API_BASE}/tenders/${tenderId}/pernyataan-minat`, {
        method: 'POST', headers: { Authorization: `Bearer ${localStorage.getItem('dpbj_token')}` },
        body: fd,
      });
      const json = await res.json();
      if (json.success) { toast('Pernyataan minat berhasil dikirim.'); fetchAll(); }
      else toast('Gagal: ' + json.message);
    } catch { toast('Terjadi kesalahan saat mengirim pernyataan minat.'); }
    finally { setPmSaving(false); }
  };

  useEffect(() => {
    if (buSearch.trim().length < 3) { setBuResults([]); return; }
    const timeout = setTimeout(async () => {
      try {
        const res = await fetch(`${API_BASE}/vendors/bidang-usaha/tree?search=${encodeURIComponent(buSearch)}`, { headers: getAuthHeaders() });
        const json = await res.json();
        if (json.success) setBuResults(json.data.filter(b => b.parent_id).slice(0, 20));
      } catch (err) { console.error(err); }
    }, 400);
    return () => clearTimeout(timeout);
  }, [buSearch]);

  const handleAddBidangUsaha = async (bidangUsahaId) => {
    try {
      const res = await fetch(`${API_BASE}/tenders/${tenderId}/bidang-usaha`, {
        method: 'POST', headers: getAuthHeaders(),
        body: JSON.stringify({ bidang_usaha_id: bidangUsahaId }),
      });
      const json = await res.json();
      if (json.success) { setBuSearch(''); setBuResults([]); fetchAll(); } else toast('Gagal: ' + json.message);
    } catch { toast('Terjadi kesalahan saat menambah bidang usaha.'); }
  };

  const handleRemoveBidangUsaha = async (linkId) => {
    if (!confirm('Hapus syarat bidang usaha ini?')) return;
    const res = await fetch(`${API_BASE}/tenders/${tenderId}/bidang-usaha/${linkId}`, { method: 'DELETE', headers: getAuthHeaders() });
    const json = await res.json();
    if (json.success) fetchAll(); else toast('Gagal: ' + json.message);
  };

  useEffect(() => { fetchAll(); }, [tenderId]);

  const handleUploadDoc = async (e) => {
    e.preventDefault();
    if (!docFile) return toast('Pilih file terlebih dahulu.');
    const formData = new FormData();
    formData.append('document_type', docType);
    formData.append('name', docName || docFile.name);
    formData.append('uploaded_by', user.id);
    formData.append('file', docFile);
    try {
      const res = await fetch(`${API_BASE}/tenders/${tenderId}/documents`, {
        method: 'POST', headers: { Authorization: `Bearer ${localStorage.getItem('dpbj_token')}` }, body: formData,
      });
      const json = await res.json();
      if (json.success) { setDocFile(null); setDocName(''); fetchAll(); } else toast('Gagal: ' + json.message);
    } catch { toast('Terjadi kesalahan saat upload.'); }
  };

  const handleDeleteDoc = async (id) => {
    if (!confirm('Hapus dokumen ini?')) return;
    const res = await fetch(`${API_BASE}/tenders/${tenderId}/documents/${id}`, { method: 'DELETE', headers: getAuthHeaders() });
    const json = await res.json();
    if (json.success) fetchAll(); else toast('Gagal: ' + json.message);
  };

  const handleUploadKlarifikasi = async (e) => {
    e.preventDefault();
    if (!klarFile) return toast('Pilih file terlebih dahulu.');
    const formData = new FormData();
    formData.append('nama', klarFile.name);
    formData.append('created_by', user.id);
    formData.append('file', klarFile);
    if (isVendor) formData.append('vendor_id', user.id);
    try {
      const res = await fetch(`${API_BASE}/tenders/${tenderId}/klarifikasi-dokumen`, {
        method: 'POST', headers: { Authorization: `Bearer ${localStorage.getItem('dpbj_token')}` }, body: formData,
      });
      const json = await res.json();
      if (json.success) { setKlarFile(null); fetchAll(); } else toast('Gagal: ' + json.message);
    } catch { toast('Terjadi kesalahan saat upload.'); }
  };

  const handleTanggapi = async (docId, file) => {
    if (!file) return;
    const formData = new FormData();
    formData.append('notes', 'Tanggapan dari panitia');
    formData.append('created_by', user.id);
    formData.append('file', file);
    try {
      const res = await fetch(`${API_BASE}/tenders/${tenderId}/klarifikasi-dokumen/${docId}/tanggapan`, {
        method: 'POST', headers: { Authorization: `Bearer ${localStorage.getItem('dpbj_token')}` }, body: formData,
      });
      const json = await res.json();
      if (json.success) fetchAll(); else toast('Gagal: ' + json.message);
    } catch { toast('Terjadi kesalahan saat mengirim tanggapan.'); }
  };

  const handleValidasiPakta = async () => {
    if (!confirm('Konfirmasi validasi pakta integritas ini?')) return;
    try {
      const res = await fetch(`${API_BASE}/tenders/${tenderId}/pakta-integritas`, {
        method: 'POST', headers: getAuthHeaders(),
        body: JSON.stringify({ user_id: user.id, kode: user.username || user.id, jenis: isVendor ? 'REKANAN' : 'PANITIA', created_by: user.id }),
      });
      const json = await res.json();
      if (json.success) fetchAll(); else toast('Gagal: ' + json.message);
    } catch { toast('Terjadi kesalahan saat validasi.'); }
  };

  const myPaktaDone = pakta.some(p => p.user_id === user.id);

  const handleAddPeringkat = async () => {
    if (!newPeringkat.vendor_id || !newPeringkat.peringkat) return toast('Lengkapi vendor dan peringkat.');
    try {
      const res = await fetch(`${API_BASE}/tenders/${tenderId}/peringkat-pemenang`, {
        method: 'POST', headers: getAuthHeaders(),
        body: JSON.stringify({ ...newPeringkat, peringkat: Number(newPeringkat.peringkat), created_by: user.id }),
      });
      const json = await res.json();
      if (json.success) { setNewPeringkat({ vendor_id: '', peringkat: '', keterangan: '' }); fetchAll(); } else toast('Gagal: ' + json.message);
    } catch { toast('Terjadi kesalahan saat menyimpan peringkat.'); }
  };

  const handleDeletePeringkat = async (id) => {
    if (!confirm('Hapus data peringkat ini?')) return;
    const res = await fetch(`${API_BASE}/tenders/${tenderId}/peringkat-pemenang/${id}`, { method: 'DELETE', headers: getAuthHeaders() });
    const json = await res.json();
    if (json.success) fetchAll(); else toast('Gagal: ' + json.message);
  };

  const handleValidasiPembukaan = async (tahap) => {
    try {
      const res = await fetch(`${API_BASE}/tenders/${tenderId}/pembukaan`, {
        method: 'POST', headers: getAuthHeaders(),
        body: JSON.stringify({ user_id: user.id, kode: pembukaanKode || null, jenis: user.role, tahap }),
      });
      const json = await res.json();
      if (json.success) { setPembukaanKode(''); fetchAll(); } else toast('Gagal: ' + json.message);
    } catch { toast('Terjadi kesalahan saat validasi pembukaan.'); }
  };

  const handleAddUndangan = async () => {
    if (!newUndangan.vendor_id || !newUndangan.tanggal_undangan) return toast('Lengkapi vendor dan tanggal undangan.');
    try {
      const res = await fetch(`${API_BASE}/tenders/${tenderId}/undangan-klarifikasi`, {
        method: 'POST', headers: getAuthHeaders(),
        body: JSON.stringify({ ...newUndangan, created_by: user.id }),
      });
      const json = await res.json();
      if (json.success) {
        setNewUndangan({ vendor_id: '', tanggal_undangan: '', jam: '', tempat: '', pelaksanaan: 'Tatap Muka', keterangan: '' });
        fetchAll();
      } else toast('Gagal: ' + json.message);
    } catch { toast('Terjadi kesalahan saat menyimpan undangan.'); }
  };

  const bukaCetak = (jenis, vendorId) => {
    const url = `/cetak/${jenis}/${tenderId}${vendorId ? `/${vendorId}` : ''}`;
    window.open(url, '_blank');
  };

  return (
    <div className="space-y-5 animate-fade-in">

      <Section icon={Printer} title="Cetak Dokumen Resmi" desc="Berita acara dan dokumen resmi, siap dicetak atau disimpan sebagai PDF.">
        <div className="flex flex-wrap gap-2">
          <button onClick={() => bukaCetak('pembukaan-penawaran')} className="btn-secondary text-xs">
            <Printer size={13} /> Berita Acara Pembukaan Penawaran
          </button>
          <button onClick={() => bukaCetak('aanwijzing')} className="btn-secondary text-xs">
            <Printer size={13} /> Berita Acara Aanwijzing
          </button>
          {user?.role === 'vendor'
            ? <button onClick={() => bukaCetak('pakta-integritas', user.id)} className="btn-secondary text-xs">
                <Printer size={13} /> Pakta Integritas Saya
              </button>
            : <button onClick={() => bukaCetak('pakta-integritas')} className="btn-secondary text-xs">
                <Printer size={13} /> Pakta Integritas Panitia
              </button>
          }
        </div>
      </Section>

      <Section icon={FileText} title="Dokumen Tender" desc="Dokumen resmi paket: lelang, kualifikasi, koreksi aritmatika, laporan.">
        {canManage && (
          <form onSubmit={handleUploadDoc} className="flex flex-wrap items-end gap-2 mb-4 bg-surface p-3 rounded-lg border border-border">
            <select value={docType} onChange={e => setDocType(e.target.value)} className="text-xs p-2 border border-gray-300 rounded-lg">
              {DOC_TYPES.map(t => <option key={t.value} value={t.value}>{t.label}</option>)}
            </select>
            <input placeholder="Nama dokumen (opsional)" value={docName} onChange={e => setDocName(e.target.value)} className="flex-1 min-w-[140px] text-xs p-2 border border-gray-300 rounded-lg" />
            <input type="file" onChange={e => setDocFile(e.target.files[0])} className="text-xs" />
            <button type="submit" className="btn-secondary text-xs flex items-center gap-1"><Upload size={12} /> Upload</button>
          </form>
        )}
        {documents.length === 0 ? (
          <p className="text-xs text-muted text-center py-3">Belum ada dokumen.</p>
        ) : (
          <div className="space-y-1.5">
            {documents.map(d => (
              <div key={d.id} className="flex items-center justify-between text-xs bg-surface p-2 rounded-lg">
                <div className="min-w-0">
                  <p className="font-semibold text-dpbj-navy truncate">{d.name}</p>
                  <p className="text-[10px] text-muted uppercase">{d.document_type} • {d.uploaded_by_name || '-'}</p>
                </div>
                <div className="flex items-center gap-2 shrink-0">
                  <a href={`${SERVER_BASE}/uploads/${d.file_path}`} target="_blank" rel="noreferrer" className="text-blue-600"><Download size={13} /></a>
                  {canManage && <button onClick={() => handleDeleteDoc(d.id)} className="text-red-400"><Trash2 size={13} /></button>}
                </div>
              </div>
            ))}
          </div>
        )}
      </Section>

      <Section icon={Briefcase} title="Bidang Usaha yang Disyaratkan" desc="Klasifikasi bidang usaha (KBLI/SBU) yang wajib dimiliki vendor untuk ikut tender ini.">
        {canManage && (
          <div className="relative mb-3">
            <Search size={13} className="absolute left-3 top-1/2 -translate-y-1/2 text-muted" />
            <input
              value={buSearch}
              onChange={e => setBuSearch(e.target.value)}
              placeholder="Cari bidang usaha (minimal 3 huruf)..."
              className="w-full text-xs pl-8 pr-3 py-2 border border-gray-300 rounded-lg"
            />
            {buResults.length > 0 && (
              <div className="absolute z-10 mt-1 w-full max-h-56 overflow-y-auto bg-white border border-border rounded-lg shadow-lg">
                {buResults.map(r => (
                  <button key={r.id} onClick={() => handleAddBidangUsaha(r.id)} className="w-full text-left px-3 py-2 text-[11px] hover:bg-surface border-b border-border last:border-0">
                    <span className="font-mono text-muted">{r.kode}</span> - {r.nama}
                  </button>
                ))}
              </div>
            )}
          </div>
        )}
        {bidangUsaha.length === 0 ? (
          <p className="text-xs text-muted text-center py-3">Belum ada syarat bidang usaha untuk tender ini.</p>
        ) : (
          <div className="space-y-1">
            {bidangUsaha.map(b => (
              <div key={b.id} className="flex items-center justify-between text-xs bg-surface p-2 rounded-lg">
                <span><span className="font-mono text-muted">{b.kode}</span> - {b.nama}</span>
                {canManage && <button onClick={() => handleRemoveBidangUsaha(b.id)} className="text-red-400"><Trash2 size={13} /></button>}
              </div>
            ))}
          </div>
        )}
      </Section>

      <Section icon={FileText} title="Pernyataan Minat" desc="Surat pernyataan minat mengikuti tender (dari penyedia yang mendaftar).">
        {isVendor ? (
          myPernyataan === undefined ? (
            <p className="text-xs text-muted text-center py-3">Memuat...</p>
          ) : myPernyataan ? (
            <div className="flex items-center justify-between bg-emerald-50 border border-emerald-200 rounded-lg p-3">
              <p className="text-xs text-emerald-700 font-semibold">Pernyataan minat Anda sudah tersimpan.</p>
              <button onClick={() => window.open(`/cetak/pernyataan-minat/${tenderId}/${user.id}`, '_blank')} className="btn-ghost text-xs py-1.5 px-3">Cetak</button>
            </div>
          ) : (
            <form onSubmit={handleSubmitPernyataanMinat} className="grid grid-cols-1 sm:grid-cols-2 gap-2">
              <input placeholder="Nama *" value={pmForm.nama} onChange={e => setPmForm({ ...pmForm, nama: e.target.value })} className="text-xs p-2 border border-gray-300 rounded-lg" />
              <input placeholder="Jabatan *" value={pmForm.jabatan} onChange={e => setPmForm({ ...pmForm, jabatan: e.target.value })} className="text-xs p-2 border border-gray-300 rounded-lg" />
              <input placeholder="Alamat *" value={pmForm.alamat} onChange={e => setPmForm({ ...pmForm, alamat: e.target.value })} className="text-xs p-2 border border-gray-300 rounded-lg sm:col-span-2" />
              <input placeholder="Telepon" value={pmForm.telepon} onChange={e => setPmForm({ ...pmForm, telepon: e.target.value })} className="text-xs p-2 border border-gray-300 rounded-lg" />
              <input placeholder="Email" value={pmForm.email} onChange={e => setPmForm({ ...pmForm, email: e.target.value })} className="text-xs p-2 border border-gray-300 rounded-lg" />
              <button type="submit" disabled={pmSaving} className="btn-primary text-xs sm:col-span-2 justify-center disabled:opacity-50">
                {pmSaving ? 'Mengirim...' : 'Kirim Pernyataan Minat'}
              </button>
            </form>
          )
        ) : (
          participants.length === 0 ? (
            <p className="text-xs text-muted text-center py-3">Belum ada vendor yang mendaftar.</p>
          ) : (
            <div className="space-y-1">
              {participants.map(p => (
                <div key={p.vendor_id} className="flex items-center justify-between text-xs bg-surface p-2 rounded-lg">
                  <span>{p.company_name}</span>
                  <button onClick={() => window.open(`/cetak/pernyataan-minat/${tenderId}/${p.vendor_id}`, '_blank')} className="text-dpbj-gold-dark font-semibold hover:underline">Cetak</button>
                </div>
              ))}
            </div>
          )
        )}
      </Section>

      <Section
        icon={MessageSquare}
        title="Klarifikasi & Tanggapan Aanwijzing"
        desc="Dokumen formal, berbeda dari chat aanwijzing."
        action={klarifikasi.length > 0 && (
          <button onClick={() => window.open(`/cetak/klarifikasi/${tenderId}`, '_blank')} className="text-[10px] text-dpbj-gold-dark font-semibold hover:underline shrink-0">
            Cetak
          </button>
        )}
      >
        <form onSubmit={handleUploadKlarifikasi} className="flex items-center gap-2 mb-4 bg-surface p-3 rounded-lg border border-border">
          <input type="file" onChange={e => setKlarFile(e.target.files[0])} className="text-xs flex-1" />
          <button type="submit" className="btn-secondary text-xs flex items-center gap-1"><Upload size={12} /> Kirim</button>
        </form>
        {klarifikasi.filter(k => !k.parent_id).length === 0 ? (
          <p className="text-xs text-muted text-center py-3">Belum ada dokumen klarifikasi.</p>
        ) : (
          <div className="space-y-2">
            {klarifikasi.filter(k => !k.parent_id).map(k => (
              <div key={k.id} className="bg-surface p-2.5 rounded-lg">
                <div className="flex items-center justify-between text-xs">
                  <div className="min-w-0">
                    <p className="font-semibold text-dpbj-navy truncate">{k.nama} <span className="text-[10px] text-muted font-normal">dari {k.vendor_name || 'Panitia'}</span></p>
                  </div>
                  <a href={`${SERVER_BASE}/uploads/${k.file_path}`} target="_blank" rel="noreferrer" className="text-blue-600 shrink-0"><Download size={13} /></a>
                </div>
                {klarifikasi.filter(t => t.parent_id === k.id).map(t => (
                  <div key={t.id} className="mt-1.5 ml-3 pl-2 border-l-2 border-dpbj-gold/40 flex items-center justify-between text-[11px]">
                    <span className="text-dpbj-navy font-medium">↳ Tanggapan: {t.notes || t.nama}</span>
                    <a href={`${SERVER_BASE}/uploads/${t.file_path}`} target="_blank" rel="noreferrer" className="text-blue-600"><Download size={12} /></a>
                  </div>
                ))}
                {canManage && !klarifikasi.some(t => t.parent_id === k.id) && (
                  <label className="mt-1.5 inline-block text-[10px] text-dpbj-gold-dark font-semibold cursor-pointer">
                    + Beri tanggapan
                    <input type="file" className="hidden" onChange={e => handleTanggapi(k.id, e.target.files[0])} />
                  </label>
                )}
              </div>
            ))}
          </div>
        )}
      </Section>

      <Section icon={ShieldCheck} title="Pakta Integritas" desc="Validasi pakta integritas oleh rekanan/panitia untuk paket ini.">
        {(isVendor || canManage) && (
          <button onClick={handleValidasiPakta} disabled={myPaktaDone} className="btn-secondary text-xs mb-3 disabled:opacity-50">
            {myPaktaDone ? 'Anda Sudah Validasi' : 'Validasi Pakta Integritas Saya'}
          </button>
        )}
        {pakta.length === 0 ? (
          <p className="text-xs text-muted text-center py-3">Belum ada validasi pakta integritas.</p>
        ) : (
          <div className="space-y-1">
            {pakta.map(p => (
              <div key={p.id} className="flex items-center justify-between text-xs bg-surface p-2 rounded-lg">
                <span className="font-semibold text-dpbj-navy">{p.user_name}</span>
                <span className="text-[10px] text-muted uppercase">{p.jenis}</span>
              </div>
            ))}
          </div>
        )}
      </Section>

      {canManage && (
        <Section icon={Trophy} title="Peringkat Pemenang & Cadangan" desc="Urutan peringkat vendor pemenang dan cadangan.">
          <div className="flex flex-wrap items-end gap-2 mb-3 bg-surface p-3 rounded-lg border border-border">
            <select value={newPeringkat.vendor_id} onChange={e => setNewPeringkat({ ...newPeringkat, vendor_id: e.target.value })} className="text-xs p-2 border border-gray-300 rounded-lg flex-1 min-w-[140px]">
              <option value="">Pilih vendor...</option>
              {participants.map(p => <option key={p.vendor_id} value={p.vendor_id}>{p.company_name}</option>)}
            </select>
            <input type="number" placeholder="Peringkat (1=utama)" value={newPeringkat.peringkat} onChange={e => setNewPeringkat({ ...newPeringkat, peringkat: e.target.value })} className="w-24 text-xs p-2 border border-gray-300 rounded-lg" />
            <input placeholder="Keterangan" value={newPeringkat.keterangan} onChange={e => setNewPeringkat({ ...newPeringkat, keterangan: e.target.value })} className="flex-1 min-w-[100px] text-xs p-2 border border-gray-300 rounded-lg" />
            <button onClick={handleAddPeringkat} className="btn-primary text-xs">Simpan</button>
          </div>
          {peringkat.length === 0 ? (
            <p className="text-xs text-muted text-center py-3">Belum ada data peringkat.</p>
          ) : (
            <div className="space-y-1">
              {peringkat.map(p => (
                <div key={p.id} className="flex items-center justify-between text-xs bg-surface p-2 rounded-lg">
                  <span><span className="font-bold text-dpbj-navy">#{p.peringkat}</span> {p.vendor_name} <span className="text-muted">{p.keterangan}</span></span>
                  <button onClick={() => handleDeletePeringkat(p.id)} className="text-red-400"><Trash2 size={13} /></button>
                </div>
              ))}
            </div>
          )}
        </Section>
      )}

      {canManage && (
        <Section icon={UserPlus} title="Pihak Lain" desc="User internal lain yang diberi akses lihat ke paket ini.">
          {pihakLain.length === 0 ? (
            <p className="text-xs text-muted text-center py-3">Belum ada pihak lain yang ditambahkan.</p>
          ) : (
            <div className="space-y-1">
              {pihakLain.map(p => (
                <div key={p.id} className="flex items-center justify-between text-xs bg-surface p-2 rounded-lg">
                  <span className="font-semibold text-dpbj-navy">{p.full_name} <span className="text-muted font-normal">({p.role_label})</span></span>
                </div>
              ))}
            </div>
          )}
        </Section>
      )}

      <Section icon={Unlock} title="Pembukaan Penawaran" desc="Validasi kehadiran/persetujuan saat pembukaan sampul penawaran, sampul 1 (administrasi & teknis) dan sampul 2 (harga) untuk metode 2 tahap.">
        {(canManage || isVendor) && (
          <div className="flex flex-wrap items-end gap-2 mb-3 bg-surface p-3 rounded-lg border border-border">
            <input placeholder="Kode/catatan (opsional)" value={pembukaanKode} onChange={e => setPembukaanKode(e.target.value)} className="flex-1 min-w-[140px] text-xs p-2 border border-gray-300 rounded-lg" />
            <button onClick={() => handleValidasiPembukaan(1)} className="btn-secondary text-xs">Validasi Sampul 1</button>
            <button onClick={() => handleValidasiPembukaan(2)} className="btn-secondary text-xs">Validasi Sampul 2</button>
          </div>
        )}
        <div className="grid grid-cols-1 sm:grid-cols-2 gap-3">
          <div>
            <p className="text-[10px] font-bold text-muted uppercase mb-1.5">Sampul 1</p>
            {pembukaan1.length === 0 ? (
              <p className="text-xs text-muted text-center py-3">Belum ada validasi.</p>
            ) : (
              <div className="space-y-1">
                {pembukaan1.map(v => (
                  <div key={v.id} className="text-xs bg-surface p-2 rounded-lg">
                    <span className="font-semibold text-dpbj-navy">{v.user_name}</span>
                    {v.kode && <span className="text-muted"> · {v.kode}</span>}
                  </div>
                ))}
              </div>
            )}
          </div>
          <div>
            <p className="text-[10px] font-bold text-muted uppercase mb-1.5">Sampul 2</p>
            {pembukaan2.length === 0 ? (
              <p className="text-xs text-muted text-center py-3">Belum ada validasi.</p>
            ) : (
              <div className="space-y-1">
                {pembukaan2.map(v => (
                  <div key={v.id} className="text-xs bg-surface p-2 rounded-lg">
                    <span className="font-semibold text-dpbj-navy">{v.user_name}</span>
                    {v.kode && <span className="text-muted"> · {v.kode}</span>}
                  </div>
                ))}
              </div>
            )}
          </div>
        </div>
      </Section>

      {canManage && (
        <Section icon={CalendarClock} title="Undangan Klarifikasi" desc="Jadwal pertemuan klarifikasi resmi ke vendor (beda dari chat aanwijzing).">
          <div className="grid grid-cols-1 sm:grid-cols-2 gap-2 mb-3 bg-surface p-3 rounded-lg border border-border">
            <select value={newUndangan.vendor_id} onChange={e => setNewUndangan({ ...newUndangan, vendor_id: e.target.value })} className="text-xs p-2 border border-gray-300 rounded-lg">
              <option value="">Pilih vendor...</option>
              {participants.map(p => <option key={p.vendor_id} value={p.vendor_id}>{p.company_name}</option>)}
            </select>
            <input type="date" value={newUndangan.tanggal_undangan} onChange={e => setNewUndangan({ ...newUndangan, tanggal_undangan: e.target.value })} className="text-xs p-2 border border-gray-300 rounded-lg" />
            <input type="time" value={newUndangan.jam} onChange={e => setNewUndangan({ ...newUndangan, jam: e.target.value })} className="text-xs p-2 border border-gray-300 rounded-lg" />
            <select value={newUndangan.pelaksanaan} onChange={e => setNewUndangan({ ...newUndangan, pelaksanaan: e.target.value })} className="text-xs p-2 border border-gray-300 rounded-lg">
              <option value="Tatap Muka">Tatap Muka</option>
              <option value="Daring">Daring</option>
            </select>
            <input placeholder="Tempat/Link" value={newUndangan.tempat} onChange={e => setNewUndangan({ ...newUndangan, tempat: e.target.value })} className="text-xs p-2 border border-gray-300 rounded-lg" />
            <input placeholder="Keterangan" value={newUndangan.keterangan} onChange={e => setNewUndangan({ ...newUndangan, keterangan: e.target.value })} className="text-xs p-2 border border-gray-300 rounded-lg" />
            <button onClick={handleAddUndangan} className="btn-primary text-xs sm:col-span-2">Kirim Undangan</button>
          </div>
          {undangan.length === 0 ? (
            <p className="text-xs text-muted text-center py-3">Belum ada undangan klarifikasi.</p>
          ) : (
            <div className="space-y-1.5">
              {undangan.map(u => (
                <div key={u.id} className="text-xs bg-surface p-2.5 rounded-lg">
                  <p className="font-semibold text-dpbj-navy">{u.vendor_name} <span className="text-muted font-normal">· {u.vendor_email}</span></p>
                  <p className="text-muted mt-0.5">{new Date(u.tanggal_undangan).toLocaleDateString('id-ID')} {u.jam || ''} · {u.pelaksanaan} {u.tempat ? `· ${u.tempat}` : ''}</p>
                  {u.keterangan && <p className="text-muted">{u.keterangan}</p>}
                </div>
              ))}
            </div>
          )}
        </Section>
      )}
    </div>
  );
}
