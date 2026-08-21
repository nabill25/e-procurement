import { useState, useEffect, useRef } from 'react';
import { getAuthHeaders, useApp, API_BASE } from '../context/AppContext';
import { FileText, Briefcase, Plus, Upload, CheckCircle2, AlertCircle } from 'lucide-react';
import { formatRupiah } from '../components/ui/shared';
import clsx from 'clsx';
import { PajakTab, TenagaAhliTab, PeralatanTab, PengurusTab, BankTab, NeracaTab } from '../components/profile/SikapTabs';
import BidangUsahaTab from '../components/profile/BidangUsahaTab';
import RekeningKoranTab from '../components/profile/RekeningKoranTab';

function IdentityTab({ vendor }) {
  if (!vendor) return null;
  return (
    <div className="space-y-4 animate-fade-in">
      <div className="grid grid-cols-2 gap-4">
        <div className="bg-surface p-4 border border-border rounded-xl">
          <p className="text-xs text-muted font-medium mb-1">Nama Perusahaan</p>
          <p className="font-bold text-dpbj-navy">{vendor.company_name}</p>
        </div>
        <div className="bg-surface p-4 border border-border rounded-xl">
          <p className="text-xs text-muted font-medium mb-1">Bentuk Badan Usaha</p>
          <p className="font-bold text-dpbj-navy">{vendor.company_type || '-'}</p>
        </div>
        <div className="bg-surface p-4 border border-border rounded-xl">
          <p className="text-xs text-muted font-medium mb-1">NPWP</p>
          <p className="font-bold text-dpbj-navy">{vendor.npwp}</p>
        </div>
        <div className="bg-surface p-4 border border-border rounded-xl">
          <p className="text-xs text-muted font-medium mb-1">NIB</p>
          <p className="font-bold text-dpbj-navy">{vendor.nib || '-'}</p>
        </div>
        <div className="bg-surface p-4 border border-border rounded-xl">
          <p className="text-xs text-muted font-medium mb-1">Kota / Provinsi</p>
          <p className="font-bold text-dpbj-navy">{vendor.city || '-'} / {vendor.province || '-'}</p>
        </div>
        <div className="bg-surface p-4 border border-border rounded-xl">
          <p className="text-xs text-muted font-medium mb-1">Email / Telepon</p>
          <p className="font-bold text-dpbj-navy">{vendor.email} <br/> {vendor.phone}</p>
        </div>
      </div>
    </div>
  );
}

function DocumentsTab({ documents, vendorId, fetchQualifications }) {
  const [isUploading, setIsUploading] = useState(false);
  const [formData, setFormData] = useState({ doc_type: 'akta', doc_number: '', issue_date: '', expiry_date: '' });
  const [file, setFile] = useState(null);

  const handleSubmit = async (e) => {
    e.preventDefault();
    if (!file || !formData.doc_number) return alert('Lengkapi dokumen dan nomor dokumen!');
    
    setIsUploading(true);
    const data = new FormData();
    data.append('doc_type', formData.doc_type);
    data.append('doc_number', formData.doc_number);
    data.append('issue_date', formData.issue_date);
    data.append('expiry_date', formData.expiry_date);
    data.append('document', file);

    try {
      const res = await fetch(`${API_BASE}/vendors/${vendorId}/documents`, {
        method: 'POST',
        headers: { Authorization: `Bearer ${localStorage.getItem('eproc_token')}` },
        body: data
      });
      const json = await res.json();
      if (json.success) {
        alert('Dokumen berhasil diunggah!');
        setFormData({ doc_type: 'akta', doc_number: '', issue_date: '', expiry_date: '' });
        setFile(null);
        fetchQualifications();
      } else {
        alert('Gagal: ' + json.message);
      }
    } catch (err) {
      alert('Error: ' + err.message);
    } finally {
      setIsUploading(false);
    }
  };

  return (
    <div className="space-y-6 animate-fade-in">
      <div className="bg-white border border-border rounded-xl p-5 shadow-sm">
        <h3 className="font-bold text-dpbj-navy text-sm mb-4 flex items-center gap-2">
          <Upload size={16} className="text-blue-600" /> Tambah Dokumen Legalitas Baru
        </h3>
        <form onSubmit={handleSubmit} className="grid grid-cols-2 gap-4">
          <div>
            <label className="block text-xs font-semibold text-muted mb-1">Jenis Dokumen</label>
            <select className="form-input w-full" value={formData.doc_type} onChange={e => setFormData({...formData, doc_type: e.target.value})}>
              <option value="akta">Akta Pendirian/Perubahan</option>
              <option value="nib">NIB (Nomor Induk Berusaha)</option>
              <option value="npwp">NPWP</option>
              <option value="skt">Surat Keterangan Terdaftar (SKT)</option>
              <option value="spt">SPT Tahunan</option>
              <option value="sertifikat">Sertifikat (ISO, SNI, dll)</option>
              <option value="ijin_usaha">Ijin Usaha (SIUP, IUJK, dll)</option>
            </select>
          </div>
          <div>
            <label className="block text-xs font-semibold text-muted mb-1">Nomor Dokumen *</label>
            <input type="text" className="form-input w-full" required value={formData.doc_number} onChange={e => setFormData({...formData, doc_number: e.target.value})} />
          </div>
          <div>
            <label className="block text-xs font-semibold text-muted mb-1">Tanggal Terbit</label>
            <input type="date" className="form-input w-full" value={formData.issue_date} onChange={e => setFormData({...formData, issue_date: e.target.value})} />
          </div>
          <div>
            <label className="block text-xs font-semibold text-muted mb-1">Masa Berlaku (Opsional)</label>
            <input type="date" className="form-input w-full" value={formData.expiry_date} onChange={e => setFormData({...formData, expiry_date: e.target.value})} />
          </div>
          <div className="col-span-2">
            <label className="block text-xs font-semibold text-muted mb-1">File Dokumen (PDF/JPG) *</label>
            <input type="file" className="form-input w-full bg-surface" required accept=".pdf,.jpg,.jpeg,.png" onChange={e => setFile(e.target.files[0])} />
          </div>
          <div className="col-span-2 flex justify-end mt-2">
            <button type="submit" disabled={isUploading} className="btn-primary bg-blue-600 hover:bg-blue-700 text-white">
              {isUploading ? 'Mengunggah...' : 'Unggah Dokumen'}
            </button>
          </div>
        </form>
      </div>

      <h3 className="font-bold text-dpbj-navy text-sm mt-8">Daftar Dokumen Legalitas</h3>
      {documents.length === 0 ? (
        <p className="text-sm text-muted">Belum ada dokumen yang diunggah.</p>
      ) : (
        <div className="border border-border rounded-xl overflow-hidden bg-white">
          <table className="w-full text-left text-sm">
            <thead className="bg-surface border-b border-border">
              <tr>
                <th className="px-4 py-3 font-semibold text-dpbj-navy text-xs uppercase">Jenis Dokumen</th>
                <th className="px-4 py-3 font-semibold text-dpbj-navy text-xs uppercase">Nomor Dokumen</th>
                <th className="px-4 py-3 font-semibold text-dpbj-navy text-xs uppercase">Tanggal</th>
                <th className="px-4 py-3 font-semibold text-dpbj-navy text-xs uppercase">Status</th>
                <th className="px-4 py-3 font-semibold text-dpbj-navy text-xs uppercase">Aksi</th>
              </tr>
            </thead>
            <tbody className="divide-y divide-border">
              {documents.map(d => (
                <tr key={d.id}>
                  <td className="px-4 py-3 font-bold uppercase">{d.doc_type}</td>
                  <td className="px-4 py-3">{d.doc_number}</td>
                  <td className="px-4 py-3 text-xs text-muted">
                    Terbit: {d.issue_date ? new Date(d.issue_date).toLocaleDateString('id-ID') : '-'}<br/>
                    Berlaku: {d.expiry_date ? new Date(d.expiry_date).toLocaleDateString('id-ID') : '-'}
                  </td>
                  <td className="px-4 py-3">
                    {d.status === 'verified' ? (
                      <span className="inline-flex items-center gap-1 text-[10px] font-bold px-2 py-0.5 rounded-full bg-emerald-100 text-emerald-700"><CheckCircle2 size={12}/> Verified</span>
                    ) : (
                      <span className="inline-flex items-center gap-1 text-[10px] font-bold px-2 py-0.5 rounded-full bg-amber-100 text-amber-700"><AlertCircle size={12}/> Pending</span>
                    )}
                  </td>
                  <td className="px-4 py-3">
                    <a href={`http://localhost:3001${d.file_path}`} target="_blank" rel="noreferrer" className="text-blue-600 font-semibold hover:underline text-xs">Lihat Dokumen</a>
                  </td>
                </tr>
              ))}
            </tbody>
          </table>
        </div>
      )}
    </div>
  );
}

function ExperiencesTab({ experiences, vendorId, fetchQualifications }) {
  const [isSubmitting, setIsSubmitting] = useState(false);
  const [formData, setFormData] = useState({ project_name: '', client_name: '', contract_value: '', start_date: '', end_date: '' });

  const handleSubmit = async (e) => {
    e.preventDefault();
    setIsSubmitting(true);
    
    try {
      const res = await fetch(`${API_BASE}/vendors/${vendorId}/experiences`, {
        method: 'POST',
        headers: getAuthHeaders(),
        body: JSON.stringify({
          ...formData,
          contract_value: formData.contract_value.replace(/\D/g, '')
        })
      });
      const json = await res.json();
      if (json.success) {
        alert('Pengalaman kerja berhasil ditambahkan!');
        setFormData({ project_name: '', client_name: '', contract_value: '', start_date: '', end_date: '' });
        fetchQualifications();
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
    <div className="space-y-6 animate-fade-in">
      <div className="bg-white border border-border rounded-xl p-5 shadow-sm">
        <h3 className="font-bold text-dpbj-navy text-sm mb-4 flex items-center gap-2">
          <Plus size={16} className="text-emerald-600" /> Tambah Pengalaman Pekerjaan
        </h3>
        <form onSubmit={handleSubmit} className="grid grid-cols-2 gap-4">
          <div className="col-span-2">
            <label className="block text-xs font-semibold text-muted mb-1">Nama Paket Pekerjaan *</label>
            <input type="text" className="form-input w-full" required value={formData.project_name} onChange={e => setFormData({...formData, project_name: e.target.value})} />
          </div>
          <div className="col-span-2">
            <label className="block text-xs font-semibold text-muted mb-1">Nama Pemberi Tugas / Klien *</label>
            <input type="text" className="form-input w-full" required value={formData.client_name} onChange={e => setFormData({...formData, client_name: e.target.value})} />
          </div>
          <div>
            <label className="block text-xs font-semibold text-muted mb-1">Nilai Kontrak (Rp) *</label>
            <div className="relative">
              <span className="absolute left-3 top-1/2 -translate-y-1/2 text-gray-500 font-bold">Rp</span>
              <input 
                type="text" 
                className="form-input w-full pl-9" 
                required 
                value={formData.contract_value}
                onChange={e => {
                  const val = e.target.value.replace(/\D/g, '');
                  setFormData({...formData, contract_value: val ? parseInt(val).toLocaleString('id-ID') : ''});
                }} 
              />
            </div>
          </div>
          <div>
            <label className="block text-xs font-semibold text-muted mb-1">Tanggal Mulai Kontrak</label>
            <input type="date" className="form-input w-full" value={formData.start_date} onChange={e => setFormData({...formData, start_date: e.target.value})} />
          </div>
          <div>
            <label className="block text-xs font-semibold text-muted mb-1">Tanggal Selesai Kontrak</label>
            <input type="date" className="form-input w-full" value={formData.end_date} onChange={e => setFormData({...formData, end_date: e.target.value})} />
          </div>
          <div className="col-span-2 flex justify-end mt-2">
            <button type="submit" disabled={isSubmitting} className="btn-primary bg-emerald-600 hover:bg-emerald-700 text-white">
              {isSubmitting ? 'Menyimpan...' : 'Simpan Pekerjaan'}
            </button>
          </div>
        </form>
      </div>

      <h3 className="font-bold text-dpbj-navy text-sm mt-8">Daftar Pengalaman Pekerjaan</h3>
      {experiences.length === 0 ? (
        <p className="text-sm text-muted">Belum ada pengalaman pekerjaan yang ditambahkan.</p>
      ) : (
        <div className="border border-border rounded-xl overflow-hidden bg-white">
          <table className="w-full text-left text-sm">
            <thead className="bg-surface border-b border-border">
              <tr>
                <th className="px-4 py-3 font-semibold text-dpbj-navy text-xs uppercase">Nama Pekerjaan</th>
                <th className="px-4 py-3 font-semibold text-dpbj-navy text-xs uppercase">Pemberi Tugas</th>
                <th className="px-4 py-3 font-semibold text-dpbj-navy text-xs uppercase">Nilai Kontrak</th>
                <th className="px-4 py-3 font-semibold text-dpbj-navy text-xs uppercase">Pelaksanaan</th>
              </tr>
            </thead>
            <tbody className="divide-y divide-border">
              {experiences.map(e => (
                <tr key={e.id}>
                  <td className="px-4 py-3 font-bold text-dpbj-navy">{e.project_name}</td>
                  <td className="px-4 py-3">{e.client_name}</td>
                  <td className="px-4 py-3 font-semibold">{formatRupiah(e.contract_value, true)}</td>
                  <td className="px-4 py-3 text-xs text-muted">
                    {e.start_date ? new Date(e.start_date).toLocaleDateString('id-ID') : '?'} - <br/>
                    {e.end_date ? new Date(e.end_date).toLocaleDateString('id-ID') : '?'}
                  </td>
                </tr>
              ))}
            </tbody>
          </table>
        </div>
      )}
    </div>
  );
}

export default function VendorProfile() {
  const { user } = useApp();
  const [activeTab, setActiveTab] = useState('identitas');
  const [vendorData, setVendorData] = useState(null);
  const [qualifications, setQualifications] = useState({ documents: [], experiences: [] });
  const [isLoading, setIsLoading] = useState(true);

  const fetchVendorInfo = async () => {
    try {
      const res = await fetch(`${API_BASE}/vendors/${user.id}`, { headers: getAuthHeaders() });
      const json = await res.json();
      if (json.success) setVendorData(json.data);
    } catch (err) {
      console.error(err);
    } finally {
      setIsLoading(false);
    }
  };

  const fetchQualifications = async () => {
    try {
      const res = await fetch(`${API_BASE}/vendors/${user.id}/qualifications`, { headers: getAuthHeaders() });
      const json = await res.json();
      if (json.success) setQualifications(json.data);
    } catch (err) {
      console.error(err);
    }
  };

  useEffect(() => {
    fetchVendorInfo();
    fetchQualifications();
  }, []);

  if (isLoading) {
    return (
      <div className="bg-white p-10 rounded-2xl shadow-sm border border-border text-center text-sm text-muted animate-fade-in">
        Memuat profil vendor...
      </div>
    );
  }

  if (!vendorData) {
    return (
      <div className="bg-white p-10 rounded-2xl shadow-sm border border-border text-center animate-fade-in">
        <h2 className="text-lg font-bold text-dpbj-navy mb-2">Data Vendor Tidak Ditemukan</h2>
        <p className="text-sm text-muted max-w-md mx-auto">
          Akun Anda tidak terdaftar sebagai vendor/penyedia, jadi halaman ini tidak dapat menampilkan profil kualifikasi.
          Halaman ini hanya berlaku untuk akun dengan peran Vendor.
        </p>
      </div>
    );
  }

  return (
    <div className="space-y-6 animate-fade-in">
      <div className="bg-white p-6 rounded-2xl shadow-sm border border-border">
        <h1 className="text-2xl font-bold text-dpbj-navy mb-2">Profil & Kualifikasi Vendor</h1>
        <p className="text-sm text-muted">Kelengkapan data kualifikasi merupakan syarat wajib untuk mengikuti pengadaan.</p>
      </div>

      <div className="bg-white rounded-2xl shadow-sm border border-border overflow-hidden">
        <div className="flex border-b border-border">
          <button
            onClick={() => setActiveTab('identitas')}
            className={clsx("flex-1 py-4 text-sm font-bold transition-colors whitespace-nowrap px-4", activeTab === 'identitas' ? "border-b-2 border-dpbj-gold text-dpbj-navy bg-surface" : "text-muted hover:text-dpbj-navy")}
          >
            Identitas
          </button>
          <button
            onClick={() => setActiveTab('dokumen')}
            className={clsx("flex-1 py-4 text-sm font-bold transition-colors whitespace-nowrap px-4", activeTab === 'dokumen' ? "border-b-2 border-dpbj-gold text-dpbj-navy bg-surface" : "text-muted hover:text-dpbj-navy")}
          >
            Legalitas
          </button>
            <button
              onClick={() => setActiveTab('pengalaman')}
              className={clsx("flex-1 py-4 text-sm font-bold transition-colors whitespace-nowrap px-4", activeTab === 'pengalaman' ? "border-b-2 border-dpbj-gold text-dpbj-navy bg-surface" : "text-muted hover:text-dpbj-navy")}
            >
              Pengalaman
            </button>
            <button
              onClick={() => setActiveTab('pajak')}
              className={clsx("flex-1 py-4 text-sm font-bold transition-colors whitespace-nowrap px-4", activeTab === 'pajak' ? "border-b-2 border-dpbj-gold text-dpbj-navy bg-surface" : "text-muted hover:text-dpbj-navy")}
            >
              Pajak
            </button>
            <button
              onClick={() => setActiveTab('pengurus')}
              className={clsx("flex-1 py-4 text-sm font-bold transition-colors whitespace-nowrap px-4", activeTab === 'pengurus' ? "border-b-2 border-dpbj-gold text-dpbj-navy bg-surface" : "text-muted hover:text-dpbj-navy")}
            >
              Pengurus
            </button>
            <button
              onClick={() => setActiveTab('ahli')}
              className={clsx("flex-1 py-4 text-sm font-bold transition-colors whitespace-nowrap px-4", activeTab === 'ahli' ? "border-b-2 border-dpbj-gold text-dpbj-navy bg-surface" : "text-muted hover:text-dpbj-navy")}
            >
              Tenaga Ahli
            </button>
            <button
              onClick={() => setActiveTab('alat')}
              className={clsx("flex-1 py-4 text-sm font-bold transition-colors whitespace-nowrap px-4", activeTab === 'alat' ? "border-b-2 border-dpbj-gold text-dpbj-navy bg-surface" : "text-muted hover:text-dpbj-navy")}
            >
              Peralatan
            </button>
            <button
              onClick={() => setActiveTab('bank')}
              className={clsx("flex-1 py-4 text-sm font-bold transition-colors whitespace-nowrap px-4", activeTab === 'bank' ? "border-b-2 border-dpbj-gold text-dpbj-navy bg-surface" : "text-muted hover:text-dpbj-navy")}
            >
              Bank
            </button>
            <button
              onClick={() => setActiveTab('neraca')}
              className={clsx("flex-1 py-4 text-sm font-bold transition-colors whitespace-nowrap px-4", activeTab === 'neraca' ? "border-b-2 border-dpbj-gold text-dpbj-navy bg-surface" : "text-muted hover:text-dpbj-navy")}
            >
              Neraca
            </button>
            <button
              onClick={() => setActiveTab('bidang_usaha')}
              className={clsx("flex-1 py-4 text-sm font-bold transition-colors whitespace-nowrap px-4", activeTab === 'bidang_usaha' ? "border-b-2 border-dpbj-gold text-dpbj-navy bg-surface" : "text-muted hover:text-dpbj-navy")}
            >
              Bidang Usaha
            </button>
            <button
              onClick={() => setActiveTab('rekening_koran')}
              className={clsx("flex-1 py-4 text-sm font-bold transition-colors whitespace-nowrap px-4", activeTab === 'rekening_koran' ? "border-b-2 border-dpbj-gold text-dpbj-navy bg-surface" : "text-muted hover:text-dpbj-navy")}
            >
              Rekening Koran
            </button>
          </div>

        <div className="p-6">
          {activeTab === 'identitas' && <IdentityTab vendor={vendorData} />}
          {activeTab === 'dokumen' && <DocumentsTab documents={qualifications.documents} vendorId={user.id} fetchQualifications={fetchQualifications} />}
          {activeTab === 'pengalaman' && <ExperiencesTab experiences={qualifications.experiences} vendorId={user.id} fetchQualifications={fetchQualifications} />}
          {activeTab === 'pajak' && <PajakTab vendor={vendorData} getAuthHeaders={getAuthHeaders} refreshData={fetchVendorInfo} />}
          {activeTab === 'pengurus' && <PengurusTab vendor={vendorData} getAuthHeaders={getAuthHeaders} refreshData={fetchVendorInfo} />}
          {activeTab === 'ahli' && <TenagaAhliTab vendor={vendorData} getAuthHeaders={getAuthHeaders} refreshData={fetchVendorInfo} />}
          {activeTab === 'alat' && <PeralatanTab vendor={vendorData} getAuthHeaders={getAuthHeaders} refreshData={fetchVendorInfo} />}
          {activeTab === 'bank' && <BankTab vendor={vendorData} getAuthHeaders={getAuthHeaders} refreshData={fetchVendorInfo} />}
          {activeTab === 'neraca' && <NeracaTab vendor={vendorData} getAuthHeaders={getAuthHeaders} refreshData={fetchVendorInfo} />}
          {activeTab === 'bidang_usaha' && <BidangUsahaTab vendorId={user.id} getAuthHeaders={getAuthHeaders} />}
          {activeTab === 'rekening_koran' && <RekeningKoranTab vendorId={user.id} getAuthHeaders={getAuthHeaders} />}
        </div>
      </div>
    </div>
  );
}
