import { useState, useEffect } from 'react';
import { FileText, Upload, Download, Trash2, MessageSquare, Mail, ShieldCheck, UserPlus, PackageOpen, Trophy } from 'lucide-react';
import { API_BASE } from '../../context/AppContext';

const DOC_TYPES = [
  { value: 'lelang', label: 'Dokumen Lelang' },
  { value: 'kualifikasi', label: 'Dokumen Kualifikasi' },
  { value: 'aritmatika', label: 'BA Koreksi Aritmatika' },
  { value: 'laporan', label: 'Laporan Paket' },
];

function Section({ icon: Icon, title, desc, children }) {
  return (
    <div className="bg-white border border-border rounded-xl overflow-hidden">
      <div className="flex items-center gap-3 p-4 bg-surface border-b border-border">
        <div className="w-8 h-8 rounded-lg bg-dpbj-navy/10 text-dpbj-navy flex items-center justify-center shrink-0">
          <Icon size={16} />
        </div>
        <div>
          <h4 className="font-bold text-dpbj-navy text-xs">{title}</h4>
          {desc && <p className="text-[10px] text-muted">{desc}</p>}
        </div>
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

  const canManage = ['pokja', 'admin', 'ppk'].includes(user.role);
  const isVendor = user.role === 'vendor';

  const fetchAll = async () => {
    try {
      const [d, k, p, pl, pr] = await Promise.all([
        fetch(`${API_BASE}/tenders/${tenderId}/documents`, { headers: getAuthHeaders() }),
        fetch(`${API_BASE}/tenders/${tenderId}/klarifikasi-dokumen`, { headers: getAuthHeaders() }),
        fetch(`${API_BASE}/tenders/${tenderId}/pakta-integritas`, { headers: getAuthHeaders() }),
        fetch(`${API_BASE}/tenders/${tenderId}/pihak-lain`, { headers: getAuthHeaders() }),
        fetch(`${API_BASE}/tenders/${tenderId}/peringkat-pemenang`, { headers: getAuthHeaders() }),
      ]);
      const [dj, kj, pj, plj, prj] = await Promise.all([d.json(), k.json(), p.json(), pl.json(), pr.json()]);
      if (dj.success) setDocuments(dj.data);
      if (kj.success) setKlarifikasi(kj.data);
      if (pj.success) setPakta(pj.data);
      if (plj.success) setPihakLain(plj.data);
      if (prj.success) setPeringkat(prj.data);
    } catch (err) { console.error(err); }
  };

  useEffect(() => { fetchAll(); }, [tenderId]);

  const handleUploadDoc = async (e) => {
    e.preventDefault();
    if (!docFile) return alert('Pilih file terlebih dahulu.');
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
      if (json.success) { setDocFile(null); setDocName(''); fetchAll(); } else alert('Gagal: ' + json.message);
    } catch { alert('Terjadi kesalahan saat upload.'); }
  };

  const handleDeleteDoc = async (id) => {
    if (!confirm('Hapus dokumen ini?')) return;
    const res = await fetch(`${API_BASE}/tenders/${tenderId}/documents/${id}`, { method: 'DELETE', headers: getAuthHeaders() });
    const json = await res.json();
    if (json.success) fetchAll(); else alert('Gagal: ' + json.message);
  };

  const handleUploadKlarifikasi = async (e) => {
    e.preventDefault();
    if (!klarFile) return alert('Pilih file terlebih dahulu.');
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
      if (json.success) { setKlarFile(null); fetchAll(); } else alert('Gagal: ' + json.message);
    } catch { alert('Terjadi kesalahan saat upload.'); }
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
      if (json.success) fetchAll(); else alert('Gagal: ' + json.message);
    } catch { alert('Terjadi kesalahan saat mengirim tanggapan.'); }
  };

  const handleValidasiPakta = async () => {
    if (!confirm('Konfirmasi validasi pakta integritas ini?')) return;
    try {
      const res = await fetch(`${API_BASE}/tenders/${tenderId}/pakta-integritas`, {
        method: 'POST', headers: getAuthHeaders(),
        body: JSON.stringify({ user_id: user.id, kode: user.username || user.id, jenis: isVendor ? 'REKANAN' : 'PANITIA', created_by: user.id }),
      });
      const json = await res.json();
      if (json.success) fetchAll(); else alert('Gagal: ' + json.message);
    } catch { alert('Terjadi kesalahan saat validasi.'); }
  };

  const myPaktaDone = pakta.some(p => p.user_id === user.id);

  const handleAddPeringkat = async () => {
    if (!newPeringkat.vendor_id || !newPeringkat.peringkat) return alert('Lengkapi vendor dan peringkat.');
    try {
      const res = await fetch(`${API_BASE}/tenders/${tenderId}/peringkat-pemenang`, {
        method: 'POST', headers: getAuthHeaders(),
        body: JSON.stringify({ ...newPeringkat, peringkat: Number(newPeringkat.peringkat), created_by: user.id }),
      });
      const json = await res.json();
      if (json.success) { setNewPeringkat({ vendor_id: '', peringkat: '', keterangan: '' }); fetchAll(); } else alert('Gagal: ' + json.message);
    } catch { alert('Terjadi kesalahan saat menyimpan peringkat.'); }
  };

  const handleDeletePeringkat = async (id) => {
    if (!confirm('Hapus data peringkat ini?')) return;
    const res = await fetch(`${API_BASE}/tenders/${tenderId}/peringkat-pemenang/${id}`, { method: 'DELETE', headers: getAuthHeaders() });
    const json = await res.json();
    if (json.success) fetchAll(); else alert('Gagal: ' + json.message);
  };

  return (
    <div className="space-y-5 animate-fade-in">

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
                  <a href={`http://localhost:3001/uploads/${d.file_path}`} target="_blank" rel="noreferrer" className="text-blue-600"><Download size={13} /></a>
                  {canManage && <button onClick={() => handleDeleteDoc(d.id)} className="text-red-400"><Trash2 size={13} /></button>}
                </div>
              </div>
            ))}
          </div>
        )}
      </Section>

      <Section icon={MessageSquare} title="Klarifikasi & Tanggapan Aanwijzing" desc="Dokumen formal, berbeda dari chat aanwijzing.">
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
                  <a href={`http://localhost:3001/uploads/${k.file_path}`} target="_blank" rel="noreferrer" className="text-blue-600 shrink-0"><Download size={13} /></a>
                </div>
                {klarifikasi.filter(t => t.parent_id === k.id).map(t => (
                  <div key={t.id} className="mt-1.5 ml-3 pl-2 border-l-2 border-dpbj-gold/40 flex items-center justify-between text-[11px]">
                    <span className="text-dpbj-navy font-medium">↳ Tanggapan: {t.notes || t.nama}</span>
                    <a href={`http://localhost:3001/uploads/${t.file_path}`} target="_blank" rel="noreferrer" className="text-blue-600"><Download size={12} /></a>
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
    </div>
  );
}
