import { useState, useEffect, useCallback } from 'react';
import { Plus, Trash2, Database } from 'lucide-react';
import { getAuthHeaders, API_BASE, useApp } from '../context/AppContext';
import clsx from 'clsx';

const CATEGORIES = [
  { id: 'bank',           label: 'Bank' },
  { id: 'mata_uang',      label: 'Mata Uang' },
  { id: 'negara',         label: 'Negara' },
  { id: 'satuan',         label: 'Satuan' },
  { id: 'incoterm',       label: 'Incoterm' },
  { id: 'payment_method', label: 'Metode Pembayaran' },
  { id: 'unit_kerja',     label: 'Unit Kerja' },
  { id: 'analisa_kebutuhan', label: 'Analisa Kebutuhan' },
  { id: 'analisa_pasar',     label: 'Analisa Pasar' },
  { id: 'rekanan_tipe',      label: 'Tipe Vendor' },
  { id: 'sertifikat_jenis',  label: 'Jenis Sertifikat' },
  { id: 'vendor_retail',     label: 'Vendor Retail' },
  { id: 'katalog_kategori',  label: 'Kategori Katalog' },
  { id: 'jenis_belanja',     label: 'Jenis Belanja' },
  { id: 'analisa_kategori',  label: 'Kategori Analisa' },
  { id: 'master_checklist',  label: 'Checklist Pengajuan' },
  { id: 'ijin_usaha',        label: 'Jenis Ijin Usaha' },
  { id: 'pendidikan',        label: 'Jenjang Pendidikan' },
  { id: 'document_templates', label: 'Template Dokumen' },
  { id: 'holidays',          label: 'Hari Libur' },
  { id: 'regions',           label: 'Wilayah' },
  { id: 'complain_types',      label: 'Subjek Komplain' },
  { id: 'complain_recipients', label: 'Penerima Komplain' },
];

function SimpleMasterTable({ category }) {
  const [data, setData] = useState([]);
  const [isLoading, setIsLoading] = useState(true);
  const [kode, setKode] = useState('');
  const [nama, setNama] = useState('');
  const [saving, setSaving] = useState(false);

  const fetchData = useCallback(async () => {
    setIsLoading(true);
    try {
      const res = await fetch(`${API_BASE}/master/${category}`);
      const json = await res.json();
      if (json.success) setData(json.data);
    } catch (err) {
      console.error('Failed to fetch master data:', err);
    } finally {
      setIsLoading(false);
    }
  }, [category]);

  useEffect(() => { fetchData(); }, [fetchData]);

  const handleAdd = async (e) => {
    e.preventDefault();
    if (!nama.trim()) return;
    setSaving(true);
    try {
      const res = await fetch(`${API_BASE}/master/${category}`, {
        method: 'POST',
        headers: getAuthHeaders(),
        body: JSON.stringify({ kode: kode || null, nama }),
      });
      const json = await res.json();
      if (json.success) {
        setKode('');
        setNama('');
        fetchData();
      } else {
        alert('Gagal: ' + json.message);
      }
    } catch {
      alert('Terjadi kesalahan saat menyimpan data.');
    } finally {
      setSaving(false);
    }
  };

  const handleDelete = async (id) => {
    if (!confirm('Hapus data ini?')) return;
    try {
      const res = await fetch(`${API_BASE}/master/${category}/${id}`, {
        method: 'DELETE',
        headers: getAuthHeaders(),
      });
      const json = await res.json();
      if (json.success) fetchData();
      else alert('Gagal: ' + json.message);
    } catch {
      alert('Terjadi kesalahan saat menghapus data.');
    }
  };

  return (
    <div className="space-y-4">
      <form onSubmit={handleAdd} className="flex items-end gap-3 bg-surface p-4 rounded-xl border border-border">
        <div className="w-32">
          <label className="text-xs text-muted font-medium">Kode</label>
          <input value={kode} onChange={e => setKode(e.target.value)} className="w-full text-sm p-2 border border-gray-300 rounded-lg outline-none focus:ring-2 focus:ring-dpbj-gold/40" />
        </div>
        <div className="flex-1">
          <label className="text-xs text-muted font-medium">Nama</label>
          <input value={nama} onChange={e => setNama(e.target.value)} required className="w-full text-sm p-2 border border-gray-300 rounded-lg outline-none focus:ring-2 focus:ring-dpbj-gold/40" />
        </div>
        <button type="submit" disabled={saving} className="btn-primary flex items-center gap-2 disabled:opacity-50">
          <Plus size={16} /> Tambah
        </button>
      </form>

      <div className="overflow-x-auto rounded-xl border border-border">
        <table className="data-table">
          <thead>
            <tr>
              <th>Kode</th>
              <th>Nama</th>
              <th className="text-right">Aksi</th>
            </tr>
          </thead>
          <tbody>
            {isLoading ? (
              <tr><td colSpan={3} className="py-10 text-center text-muted text-sm">Memuat data...</td></tr>
            ) : data.length === 0 ? (
              <tr><td colSpan={3} className="py-10 text-center text-muted text-sm">Belum ada data.</td></tr>
            ) : data.map(row => (
              <tr key={row.id}>
                <td className="font-mono text-xs">{row.kode || '-'}</td>
                <td className="text-sm font-medium text-dpbj-navy">{row.nama}</td>
                <td className="text-right">
                  <button onClick={() => handleDelete(row.id)} className="p-1.5 text-red-500 hover:bg-red-50 rounded-lg transition-colors">
                    <Trash2 size={14} />
                  </button>
                </td>
              </tr>
            ))}
          </tbody>
        </table>
      </div>
    </div>
  );
}

function UnitKerjaTable() {
  const [data, setData] = useState([]);
  const [isLoading, setIsLoading] = useState(true);
  const [form, setForm] = useState({ kode: '', nama: '', alamat: '', telepon: '', email: '' });
  const [saving, setSaving] = useState(false);

  const fetchData = useCallback(async () => {
    setIsLoading(true);
    try {
      const res = await fetch(`${API_BASE}/master/unit-kerja`);
      const json = await res.json();
      if (json.success) setData(json.data);
    } catch (err) {
      console.error('Failed to fetch unit kerja:', err);
    } finally {
      setIsLoading(false);
    }
  }, []);

  useEffect(() => { fetchData(); }, [fetchData]);

  const handleAdd = async (e) => {
    e.preventDefault();
    if (!form.nama.trim()) return;
    setSaving(true);
    try {
      const res = await fetch(`${API_BASE}/master/unit-kerja`, {
        method: 'POST',
        headers: getAuthHeaders(),
        body: JSON.stringify(form),
      });
      const json = await res.json();
      if (json.success) {
        setForm({ kode: '', nama: '', alamat: '', telepon: '', email: '' });
        fetchData();
      } else {
        alert('Gagal: ' + json.message);
      }
    } catch {
      alert('Terjadi kesalahan saat menyimpan data.');
    } finally {
      setSaving(false);
    }
  };

  const handleDelete = async (id) => {
    if (!confirm('Hapus unit kerja ini?')) return;
    try {
      const res = await fetch(`${API_BASE}/master/unit-kerja/${id}`, {
        method: 'DELETE',
        headers: getAuthHeaders(),
      });
      const json = await res.json();
      if (json.success) fetchData();
      else alert('Gagal: ' + json.message);
    } catch {
      alert('Terjadi kesalahan saat menghapus data.');
    }
  };

  return (
    <div className="space-y-4">
      <form onSubmit={handleAdd} className="grid grid-cols-2 md:grid-cols-5 gap-3 bg-surface p-4 rounded-xl border border-border items-end">
        <div>
          <label className="text-xs text-muted font-medium">Kode</label>
          <input value={form.kode} onChange={e => setForm({ ...form, kode: e.target.value })} className="w-full text-sm p-2 border border-gray-300 rounded-lg outline-none focus:ring-2 focus:ring-dpbj-gold/40" />
        </div>
        <div>
          <label className="text-xs text-muted font-medium">Nama</label>
          <input value={form.nama} onChange={e => setForm({ ...form, nama: e.target.value })} required className="w-full text-sm p-2 border border-gray-300 rounded-lg outline-none focus:ring-2 focus:ring-dpbj-gold/40" />
        </div>
        <div>
          <label className="text-xs text-muted font-medium">Telepon</label>
          <input value={form.telepon} onChange={e => setForm({ ...form, telepon: e.target.value })} className="w-full text-sm p-2 border border-gray-300 rounded-lg outline-none focus:ring-2 focus:ring-dpbj-gold/40" />
        </div>
        <div>
          <label className="text-xs text-muted font-medium">Email</label>
          <input value={form.email} onChange={e => setForm({ ...form, email: e.target.value })} className="w-full text-sm p-2 border border-gray-300 rounded-lg outline-none focus:ring-2 focus:ring-dpbj-gold/40" />
        </div>
        <button type="submit" disabled={saving} className="btn-primary flex items-center justify-center gap-2 disabled:opacity-50">
          <Plus size={16} /> Tambah
        </button>
        <div className="col-span-2 md:col-span-5">
          <label className="text-xs text-muted font-medium">Alamat</label>
          <input value={form.alamat} onChange={e => setForm({ ...form, alamat: e.target.value })} className="w-full text-sm p-2 border border-gray-300 rounded-lg outline-none focus:ring-2 focus:ring-dpbj-gold/40" />
        </div>
      </form>

      <div className="overflow-x-auto rounded-xl border border-border">
        <table className="data-table">
          <thead>
            <tr>
              <th>Kode</th>
              <th>Nama</th>
              <th>Telepon</th>
              <th>Email</th>
              <th className="text-right">Aksi</th>
            </tr>
          </thead>
          <tbody>
            {isLoading ? (
              <tr><td colSpan={5} className="py-10 text-center text-muted text-sm">Memuat data...</td></tr>
            ) : data.length === 0 ? (
              <tr><td colSpan={5} className="py-10 text-center text-muted text-sm">Belum ada data.</td></tr>
            ) : data.map(row => (
              <tr key={row.id}>
                <td className="font-mono text-xs">{row.kode || '-'}</td>
                <td className="text-sm font-medium text-dpbj-navy">{row.nama}</td>
                <td className="text-xs text-muted">{row.telepon || '-'}</td>
                <td className="text-xs text-muted">{row.email || '-'}</td>
                <td className="text-right">
                  <button onClick={() => handleDelete(row.id)} className="p-1.5 text-red-500 hover:bg-red-50 rounded-lg transition-colors">
                    <Trash2 size={14} />
                  </button>
                </td>
              </tr>
            ))}
          </tbody>
        </table>
      </div>
    </div>
  );
}

function VendorRetailTable() {
  const [data, setData] = useState([]);
  const [isLoading, setIsLoading] = useState(true);
  const [form, setForm] = useState({ nama: '', npwp: '', telepon: '', kota: '', kontak_person: '', kontak_person_hp: '', alamat: '' });
  const [saving, setSaving] = useState(false);

  const fetchData = useCallback(async () => {
    setIsLoading(true);
    try {
      const res = await fetch(`${API_BASE}/vendors/retail`);
      const json = await res.json();
      if (json.success) setData(json.data);
    } catch (err) {
      console.error('Failed to fetch vendor retail:', err);
    } finally {
      setIsLoading(false);
    }
  }, []);

  useEffect(() => { fetchData(); }, [fetchData]);

  const handleAdd = async (e) => {
    e.preventDefault();
    if (!form.nama.trim()) return;
    setSaving(true);
    try {
      const res = await fetch(`${API_BASE}/vendors/retail`, {
        method: 'POST',
        headers: getAuthHeaders(),
        body: JSON.stringify(form),
      });
      const json = await res.json();
      if (json.success) {
        setForm({ nama: '', npwp: '', telepon: '', kota: '', kontak_person: '', kontak_person_hp: '', alamat: '' });
        fetchData();
      } else {
        alert('Gagal: ' + json.message);
      }
    } catch {
      alert('Terjadi kesalahan saat menyimpan data.');
    } finally {
      setSaving(false);
    }
  };

  const handleDelete = async (id) => {
    if (!confirm('Hapus vendor retail ini?')) return;
    try {
      const res = await fetch(`${API_BASE}/vendors/retail/${id}`, {
        method: 'DELETE',
        headers: getAuthHeaders(),
      });
      const json = await res.json();
      if (json.success) fetchData();
      else alert('Gagal: ' + json.message);
    } catch {
      alert('Terjadi kesalahan saat menghapus data.');
    }
  };

  return (
    <div className="space-y-4">
      <form onSubmit={handleAdd} className="grid grid-cols-2 md:grid-cols-4 gap-3 bg-surface p-4 rounded-xl border border-border items-end">
        <div>
          <label className="text-xs text-muted font-medium">Nama Toko/Vendor</label>
          <input value={form.nama} onChange={e => setForm({ ...form, nama: e.target.value })} required className="w-full text-sm p-2 border border-gray-300 rounded-lg outline-none focus:ring-2 focus:ring-dpbj-gold/40" />
        </div>
        <div>
          <label className="text-xs text-muted font-medium">NPWP</label>
          <input value={form.npwp} onChange={e => setForm({ ...form, npwp: e.target.value })} className="w-full text-sm p-2 border border-gray-300 rounded-lg outline-none focus:ring-2 focus:ring-dpbj-gold/40" />
        </div>
        <div>
          <label className="text-xs text-muted font-medium">Telepon</label>
          <input value={form.telepon} onChange={e => setForm({ ...form, telepon: e.target.value })} className="w-full text-sm p-2 border border-gray-300 rounded-lg outline-none focus:ring-2 focus:ring-dpbj-gold/40" />
        </div>
        <div>
          <label className="text-xs text-muted font-medium">Kota</label>
          <input value={form.kota} onChange={e => setForm({ ...form, kota: e.target.value })} className="w-full text-sm p-2 border border-gray-300 rounded-lg outline-none focus:ring-2 focus:ring-dpbj-gold/40" />
        </div>
        <div>
          <label className="text-xs text-muted font-medium">Kontak Person</label>
          <input value={form.kontak_person} onChange={e => setForm({ ...form, kontak_person: e.target.value })} className="w-full text-sm p-2 border border-gray-300 rounded-lg outline-none focus:ring-2 focus:ring-dpbj-gold/40" />
        </div>
        <div>
          <label className="text-xs text-muted font-medium">No. HP Kontak</label>
          <input value={form.kontak_person_hp} onChange={e => setForm({ ...form, kontak_person_hp: e.target.value })} className="w-full text-sm p-2 border border-gray-300 rounded-lg outline-none focus:ring-2 focus:ring-dpbj-gold/40" />
        </div>
        <div className="col-span-2">
          <label className="text-xs text-muted font-medium">Alamat</label>
          <input value={form.alamat} onChange={e => setForm({ ...form, alamat: e.target.value })} className="w-full text-sm p-2 border border-gray-300 rounded-lg outline-none focus:ring-2 focus:ring-dpbj-gold/40" />
        </div>
        <button type="submit" disabled={saving} className="btn-primary flex items-center justify-center gap-2 disabled:opacity-50">
          <Plus size={16} /> Tambah
        </button>
      </form>

      <div className="overflow-x-auto rounded-xl border border-border">
        <table className="data-table">
          <thead>
            <tr>
              <th>Nama</th>
              <th>NPWP</th>
              <th>Telepon</th>
              <th>Kota</th>
              <th>Kontak Person</th>
              <th className="text-right">Aksi</th>
            </tr>
          </thead>
          <tbody>
            {isLoading ? (
              <tr><td colSpan={6} className="py-10 text-center text-muted text-sm">Memuat data...</td></tr>
            ) : data.length === 0 ? (
              <tr><td colSpan={6} className="py-10 text-center text-muted text-sm">Belum ada data.</td></tr>
            ) : data.map(row => (
              <tr key={row.id}>
                <td className="text-sm font-medium text-dpbj-navy">{row.nama}</td>
                <td className="font-mono text-xs">{row.npwp || '-'}</td>
                <td className="text-xs text-muted">{row.telepon || '-'}</td>
                <td className="text-xs text-muted">{row.kota || '-'}</td>
                <td className="text-xs text-muted">{row.kontak_person || '-'} {row.kontak_person_hp ? `(${row.kontak_person_hp})` : ''}</td>
                <td className="text-right">
                  <button onClick={() => handleDelete(row.id)} className="p-1.5 text-red-500 hover:bg-red-50 rounded-lg transition-colors">
                    <Trash2 size={14} />
                  </button>
                </td>
              </tr>
            ))}
          </tbody>
        </table>
      </div>
    </div>
  );
}

function KatalogCategoryTable() {
  const [data, setData] = useState([]);
  const [isLoading, setIsLoading] = useState(true);
  const [nama, setNama] = useState('');
  const [kode, setKode] = useState('');
  const [parentId, setParentId] = useState('');
  const [saving, setSaving] = useState(false);

  const fetchData = useCallback(async () => {
    setIsLoading(true);
    try {
      const res = await fetch(`${API_BASE}/katalog/categories/tree`);
      const json = await res.json();
      if (json.success) setData(json.data);
    } catch (err) {
      console.error('Failed to fetch katalog categories:', err);
    } finally {
      setIsLoading(false);
    }
  }, []);

  useEffect(() => { fetchData(); }, [fetchData]);

  const handleAdd = async (e) => {
    e.preventDefault();
    if (!nama.trim()) return;
    setSaving(true);
    try {
      const res = await fetch(`${API_BASE}/katalog/categories`, {
        method: 'POST',
        headers: getAuthHeaders(),
        body: JSON.stringify({ nama, kode, parent_id: parentId || null }),
      });
      const json = await res.json();
      if (json.success) {
        setNama(''); setKode(''); setParentId('');
        fetchData();
      } else {
        alert('Gagal: ' + json.message);
      }
    } catch {
      alert('Terjadi kesalahan saat menyimpan data.');
    } finally {
      setSaving(false);
    }
  };

  const handleDelete = async (id) => {
    if (!confirm('Hapus kategori ini?')) return;
    try {
      const res = await fetch(`${API_BASE}/katalog/categories/${id}`, {
        method: 'DELETE',
        headers: getAuthHeaders(),
      });
      const json = await res.json();
      if (json.success) fetchData();
      else alert('Gagal: ' + json.message);
    } catch {
      alert('Terjadi kesalahan saat menghapus data.');
    }
  };

  return (
    <div className="space-y-4">
      <form onSubmit={handleAdd} className="grid grid-cols-2 md:grid-cols-4 gap-3 bg-surface p-4 rounded-xl border border-border items-end">
        <div>
          <label className="text-xs text-muted font-medium">Nama Kategori</label>
          <input value={nama} onChange={e => setNama(e.target.value)} required className="w-full text-sm p-2 border border-gray-300 rounded-lg outline-none focus:ring-2 focus:ring-dpbj-gold/40" />
        </div>
        <div>
          <label className="text-xs text-muted font-medium">Kode</label>
          <input value={kode} onChange={e => setKode(e.target.value)} className="w-full text-sm p-2 border border-gray-300 rounded-lg outline-none focus:ring-2 focus:ring-dpbj-gold/40" />
        </div>
        <div>
          <label className="text-xs text-muted font-medium">Induk Kategori (opsional)</label>
          <select value={parentId} onChange={e => setParentId(e.target.value)} className="w-full text-sm p-2 border border-gray-300 rounded-lg outline-none focus:ring-2 focus:ring-dpbj-gold/40">
            <option value="">- Kategori Utama -</option>
            {data.map(d => <option key={d.id} value={d.id}>{d.nama}</option>)}
          </select>
        </div>
        <button type="submit" disabled={saving} className="btn-primary flex items-center justify-center gap-2 disabled:opacity-50">
          <Plus size={16} /> Tambah
        </button>
      </form>

      <div className="overflow-x-auto rounded-xl border border-border">
        <table className="data-table">
          <thead>
            <tr>
              <th>Kode</th>
              <th>Nama</th>
              <th>Induk</th>
              <th className="text-right">Aksi</th>
            </tr>
          </thead>
          <tbody>
            {isLoading ? (
              <tr><td colSpan={4} className="py-10 text-center text-muted text-sm">Memuat data...</td></tr>
            ) : data.length === 0 ? (
              <tr><td colSpan={4} className="py-10 text-center text-muted text-sm">Belum ada data.</td></tr>
            ) : data.map(row => (
              <tr key={row.id}>
                <td className="font-mono text-xs">{row.kode || '-'}</td>
                <td className="text-sm font-medium text-dpbj-navy">{row.nama}</td>
                <td className="text-xs text-muted">{data.find(d => d.id === row.parent_id)?.nama || '-'}</td>
                <td className="text-right">
                  <button onClick={() => handleDelete(row.id)} className="p-1.5 text-red-500 hover:bg-red-50 rounded-lg transition-colors">
                    <Trash2 size={14} />
                  </button>
                </td>
              </tr>
            ))}
          </tbody>
        </table>
      </div>
    </div>
  );
}

function MasterChecklistTable() {
  const { user } = useApp();
  const [data, setData] = useState([]);
  const [isLoading, setIsLoading] = useState(true);
  const [nama, setNama] = useState('');
  const [paketJenis, setPaketJenis] = useState('');
  const [wajib, setWajib] = useState(false);
  const [saving, setSaving] = useState(false);

  const fetchData = useCallback(async () => {
    setIsLoading(true);
    try {
      const res = await fetch(`${API_BASE}/pengajuan/master/checklist`, { headers: getAuthHeaders() });
      const json = await res.json();
      if (json.success) setData(json.data);
    } catch (err) {
      console.error('Failed to fetch master checklist:', err);
    } finally {
      setIsLoading(false);
    }
  }, []);

  useEffect(() => { fetchData(); }, [fetchData]);

  const handleAdd = async (e) => {
    e.preventDefault();
    if (!nama.trim()) return;
    setSaving(true);
    try {
      const res = await fetch(`${API_BASE}/pengajuan/master/checklist`, {
        method: 'POST',
        headers: getAuthHeaders(),
        body: JSON.stringify({ nama, paket_jenis: paketJenis || null, wajib, created_by: user.id }),
      });
      const json = await res.json();
      if (json.success) {
        setNama(''); setPaketJenis(''); setWajib(false);
        fetchData();
      } else {
        alert('Gagal: ' + json.message);
      }
    } catch {
      alert('Terjadi kesalahan saat menyimpan data.');
    } finally {
      setSaving(false);
    }
  };

  const handleDelete = async (id) => {
    if (!confirm('Hapus item checklist ini?')) return;
    try {
      const res = await fetch(`${API_BASE}/pengajuan/master/checklist/${id}`, {
        method: 'DELETE',
        headers: getAuthHeaders(),
      });
      const json = await res.json();
      if (json.success) fetchData();
      else alert('Gagal: ' + json.message);
    } catch {
      alert('Terjadi kesalahan saat menghapus data.');
    }
  };

  return (
    <div className="space-y-4">
      <form onSubmit={handleAdd} className="grid grid-cols-2 md:grid-cols-4 gap-3 bg-surface p-4 rounded-xl border border-border items-end">
        <div>
          <label className="text-xs text-muted font-medium">Nama Item Checklist</label>
          <input value={nama} onChange={e => setNama(e.target.value)} required className="w-full text-sm p-2 border border-gray-300 rounded-lg outline-none focus:ring-2 focus:ring-dpbj-gold/40" />
        </div>
        <div>
          <label className="text-xs text-muted font-medium">Jenis Paket (opsional)</label>
          <input value={paketJenis} onChange={e => setPaketJenis(e.target.value)} placeholder="mis. barang, jasa" className="w-full text-sm p-2 border border-gray-300 rounded-lg outline-none focus:ring-2 focus:ring-dpbj-gold/40" />
        </div>
        <label className="flex items-center gap-2 text-sm text-dpbj-navy">
          <input type="checkbox" checked={wajib} onChange={e => setWajib(e.target.checked)} /> Wajib
        </label>
        <button type="submit" disabled={saving} className="btn-primary flex items-center justify-center gap-2 disabled:opacity-50">
          <Plus size={16} /> Tambah
        </button>
      </form>

      <div className="overflow-x-auto rounded-xl border border-border">
        <table className="data-table">
          <thead>
            <tr>
              <th>Nama</th>
              <th>Jenis Paket</th>
              <th>Wajib</th>
              <th className="text-right">Aksi</th>
            </tr>
          </thead>
          <tbody>
            {isLoading ? (
              <tr><td colSpan={4} className="py-10 text-center text-muted text-sm">Memuat data...</td></tr>
            ) : data.length === 0 ? (
              <tr><td colSpan={4} className="py-10 text-center text-muted text-sm">Belum ada data.</td></tr>
            ) : data.map(row => (
              <tr key={row.id}>
                <td className="text-sm font-medium text-dpbj-navy">{row.nama}</td>
                <td className="text-xs text-muted">{row.paket_jenis || 'Semua'}</td>
                <td className="text-xs">{row.wajib ? <span className="text-red-500 font-semibold">Wajib</span> : '-'}</td>
                <td className="text-right">
                  <button onClick={() => handleDelete(row.id)} className="p-1.5 text-red-500 hover:bg-red-50 rounded-lg transition-colors">
                    <Trash2 size={14} />
                  </button>
                </td>
              </tr>
            ))}
          </tbody>
        </table>
      </div>
    </div>
  );
}

function DocumentTemplateTable() {
  const { user } = useApp();
  const [data, setData] = useState([]);
  const [isLoading, setIsLoading] = useState(true);
  const [nama, setNama] = useState('');
  const [target, setTarget] = useState('internal');
  const [file, setFile] = useState(null);
  const [saving, setSaving] = useState(false);

  const fetchData = useCallback(async () => {
    setIsLoading(true);
    try {
      const res = await fetch(`${API_BASE}/master/document-templates`);
      const json = await res.json();
      if (json.success) setData(json.data);
    } catch (err) {
      console.error('Failed to fetch document templates:', err);
    } finally {
      setIsLoading(false);
    }
  }, []);

  useEffect(() => { fetchData(); }, [fetchData]);

  const handleAdd = async (e) => {
    e.preventDefault();
    if (!nama.trim()) return;
    setSaving(true);
    try {
      const fd = new FormData();
      fd.append('nama', nama);
      fd.append('target', target);
      fd.append('created_by', user.id);
      if (file) fd.append('file', file);
      const res = await fetch(`${API_BASE}/master/document-templates`, {
        method: 'POST',
        headers: { Authorization: `Bearer ${localStorage.getItem('dpbj_token')}` },
        body: fd,
      });
      const json = await res.json();
      if (json.success) {
        setNama(''); setFile(null);
        fetchData();
      } else {
        alert('Gagal: ' + json.message);
      }
    } catch {
      alert('Terjadi kesalahan saat menyimpan data.');
    } finally {
      setSaving(false);
    }
  };

  const handleDelete = async (id) => {
    if (!confirm('Hapus template ini?')) return;
    try {
      const res = await fetch(`${API_BASE}/master/document-templates/${id}`, {
        method: 'DELETE',
        headers: getAuthHeaders(),
      });
      const json = await res.json();
      if (json.success) fetchData();
      else alert('Gagal: ' + json.message);
    } catch {
      alert('Terjadi kesalahan saat menghapus data.');
    }
  };

  return (
    <div className="space-y-4">
      <form onSubmit={handleAdd} className="grid grid-cols-2 md:grid-cols-4 gap-3 bg-surface p-4 rounded-xl border border-border items-end">
        <div>
          <label className="text-xs text-muted font-medium">Nama Template</label>
          <input value={nama} onChange={e => setNama(e.target.value)} required className="w-full text-sm p-2 border border-gray-300 rounded-lg outline-none focus:ring-2 focus:ring-dpbj-gold/40" />
        </div>
        <div>
          <label className="text-xs text-muted font-medium">Untuk</label>
          <select value={target} onChange={e => setTarget(e.target.value)} className="w-full text-sm p-2 border border-gray-300 rounded-lg outline-none focus:ring-2 focus:ring-dpbj-gold/40">
            <option value="internal">Internal</option>
            <option value="rekanan">Rekanan/Vendor</option>
          </select>
        </div>
        <div>
          <label className="text-xs text-muted font-medium">File</label>
          <input type="file" onChange={e => setFile(e.target.files[0])} className="w-full text-xs" />
        </div>
        <button type="submit" disabled={saving} className="btn-primary flex items-center justify-center gap-2 disabled:opacity-50">
          <Plus size={16} /> Tambah
        </button>
      </form>

      <div className="overflow-x-auto rounded-xl border border-border">
        <table className="data-table">
          <thead>
            <tr>
              <th>Nama</th>
              <th>Untuk</th>
              <th>File</th>
              <th className="text-right">Aksi</th>
            </tr>
          </thead>
          <tbody>
            {isLoading ? (
              <tr><td colSpan={4} className="py-10 text-center text-muted text-sm">Memuat data...</td></tr>
            ) : data.length === 0 ? (
              <tr><td colSpan={4} className="py-10 text-center text-muted text-sm">Belum ada data.</td></tr>
            ) : data.map(row => (
              <tr key={row.id}>
                <td className="text-sm font-medium text-dpbj-navy">{row.nama}</td>
                <td className="text-xs text-muted capitalize">{row.target}</td>
                <td className="text-xs">
                  {row.file_path ? <a href={`http://localhost:3001${row.file_path}`} target="_blank" rel="noreferrer" className="text-blue-600 underline">Unduh</a> : '-'}
                </td>
                <td className="text-right">
                  <button onClick={() => handleDelete(row.id)} className="p-1.5 text-red-500 hover:bg-red-50 rounded-lg transition-colors">
                    <Trash2 size={14} />
                  </button>
                </td>
              </tr>
            ))}
          </tbody>
        </table>
      </div>
    </div>
  );
}

function HolidayTable() {
  const { user } = useApp();
  const [data, setData] = useState([]);
  const [isLoading, setIsLoading] = useState(true);
  const [tanggal, setTanggal] = useState('');
  const [keterangan, setKeterangan] = useState('');
  const [saving, setSaving] = useState(false);

  const fetchData = useCallback(async () => {
    setIsLoading(true);
    try {
      const res = await fetch(`${API_BASE}/master/holidays`);
      const json = await res.json();
      if (json.success) setData(json.data);
    } catch (err) {
      console.error('Failed to fetch holidays:', err);
    } finally {
      setIsLoading(false);
    }
  }, []);

  useEffect(() => { fetchData(); }, [fetchData]);

  const handleAdd = async (e) => {
    e.preventDefault();
    if (!tanggal) return;
    setSaving(true);
    try {
      const res = await fetch(`${API_BASE}/master/holidays`, {
        method: 'POST',
        headers: getAuthHeaders(),
        body: JSON.stringify({ holidays: [{ tanggal, keterangan }], created_by: user.id }),
      });
      const json = await res.json();
      if (json.success) {
        setTanggal(''); setKeterangan('');
        fetchData();
      } else {
        alert('Gagal: ' + json.message);
      }
    } catch {
      alert('Terjadi kesalahan saat menyimpan data.');
    } finally {
      setSaving(false);
    }
  };

  const handleDelete = async (id) => {
    if (!confirm('Hapus hari libur ini?')) return;
    try {
      const res = await fetch(`${API_BASE}/master/holidays/${id}`, {
        method: 'DELETE',
        headers: getAuthHeaders(),
      });
      const json = await res.json();
      if (json.success) fetchData();
      else alert('Gagal: ' + json.message);
    } catch {
      alert('Terjadi kesalahan saat menghapus data.');
    }
  };

  return (
    <div className="space-y-4">
      <form onSubmit={handleAdd} className="grid grid-cols-2 md:grid-cols-3 gap-3 bg-surface p-4 rounded-xl border border-border items-end">
        <div>
          <label className="text-xs text-muted font-medium">Tanggal</label>
          <input type="date" value={tanggal} onChange={e => setTanggal(e.target.value)} required className="w-full text-sm p-2 border border-gray-300 rounded-lg outline-none focus:ring-2 focus:ring-dpbj-gold/40" />
        </div>
        <div>
          <label className="text-xs text-muted font-medium">Keterangan</label>
          <input value={keterangan} onChange={e => setKeterangan(e.target.value)} placeholder="mis. Hari Kemerdekaan" className="w-full text-sm p-2 border border-gray-300 rounded-lg outline-none focus:ring-2 focus:ring-dpbj-gold/40" />
        </div>
        <button type="submit" disabled={saving} className="btn-primary flex items-center justify-center gap-2 disabled:opacity-50">
          <Plus size={16} /> Tambah
        </button>
      </form>

      <div className="overflow-x-auto rounded-xl border border-border">
        <table className="data-table">
          <thead>
            <tr>
              <th>Tanggal</th>
              <th>Keterangan</th>
              <th className="text-right">Aksi</th>
            </tr>
          </thead>
          <tbody>
            {isLoading ? (
              <tr><td colSpan={3} className="py-10 text-center text-muted text-sm">Memuat data...</td></tr>
            ) : data.length === 0 ? (
              <tr><td colSpan={3} className="py-10 text-center text-muted text-sm">Belum ada data.</td></tr>
            ) : data.map(row => (
              <tr key={row.id}>
                <td className="text-sm font-medium text-dpbj-navy">{new Date(row.tanggal).toLocaleDateString('id-ID', { day: 'numeric', month: 'long', year: 'numeric' })}</td>
                <td className="text-xs text-muted">{row.keterangan || '-'}</td>
                <td className="text-right">
                  <button onClick={() => handleDelete(row.id)} className="p-1.5 text-red-500 hover:bg-red-50 rounded-lg transition-colors">
                    <Trash2 size={14} />
                  </button>
                </td>
              </tr>
            ))}
          </tbody>
        </table>
      </div>
    </div>
  );
}

function RegionTable() {
  const [provinces, setProvinces] = useState([]);
  const [selectedProvince, setSelectedProvince] = useState('');
  const [children, setChildren] = useState([]);
  const [nama, setNama] = useState('');
  const [level, setLevel] = useState('kabkota');
  const [saving, setSaving] = useState(false);

  const fetchProvinces = useCallback(async () => {
    const res = await fetch(`${API_BASE}/master/regions?level=provinsi`);
    const json = await res.json();
    if (json.success) setProvinces(json.data);
  }, []);

  const fetchChildren = useCallback(async () => {
    if (!selectedProvince) { setChildren([]); return; }
    const res = await fetch(`${API_BASE}/master/regions?parent_id=${selectedProvince}`);
    const json = await res.json();
    if (json.success) setChildren(json.data);
  }, [selectedProvince]);

  useEffect(() => { fetchProvinces(); }, [fetchProvinces]);
  useEffect(() => { fetchChildren(); }, [fetchChildren]);

  const handleAdd = async (e) => {
    e.preventDefault();
    if (!nama.trim() || !selectedProvince) return;
    setSaving(true);
    try {
      const res = await fetch(`${API_BASE}/master/regions`, {
        method: 'POST',
        headers: getAuthHeaders(),
        body: JSON.stringify({ level, nama, parent_id: selectedProvince }),
      });
      const json = await res.json();
      if (json.success) { setNama(''); fetchChildren(); }
      else alert('Gagal: ' + json.message);
    } catch {
      alert('Terjadi kesalahan saat menyimpan data.');
    } finally {
      setSaving(false);
    }
  };

  const handleDelete = async (id) => {
    if (!confirm('Hapus wilayah ini?')) return;
    try {
      const res = await fetch(`${API_BASE}/master/regions/${id}`, { method: 'DELETE', headers: getAuthHeaders() });
      const json = await res.json();
      if (json.success) fetchChildren();
      else alert('Gagal: ' + json.message);
    } catch {
      alert('Terjadi kesalahan saat menghapus data.');
    }
  };

  return (
    <div className="space-y-4">
      <div className="bg-amber-50 border border-amber-200 rounded-xl p-3 text-xs text-amber-800">
        Data 38 provinsi resmi Indonesia sudah tersedia. Kabupaten/kota, kecamatan, dan kelurahan belum ada datanya (butuh sumber data resmi terpisah) - bisa ditambahkan manual di sini kalau dibutuhkan.
      </div>
      <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
          <label className="text-xs text-muted font-medium">Pilih Provinsi</label>
          <select value={selectedProvince} onChange={e => setSelectedProvince(e.target.value)} className="w-full text-sm p-2 border border-gray-300 rounded-lg outline-none focus:ring-2 focus:ring-dpbj-gold/40">
            <option value="">- Pilih Provinsi -</option>
            {provinces.map(p => <option key={p.id} value={p.id}>{p.nama}</option>)}
          </select>
        </div>
      </div>

      {selectedProvince && (
        <>
          <form onSubmit={handleAdd} className="grid grid-cols-2 md:grid-cols-3 gap-3 bg-surface p-4 rounded-xl border border-border items-end">
            <div>
              <label className="text-xs text-muted font-medium">Level</label>
              <select value={level} onChange={e => setLevel(e.target.value)} className="w-full text-sm p-2 border border-gray-300 rounded-lg outline-none focus:ring-2 focus:ring-dpbj-gold/40">
                <option value="kabkota">Kabupaten/Kota</option>
                <option value="kecamatan">Kecamatan</option>
                <option value="kelurahan">Kelurahan</option>
              </select>
            </div>
            <div>
              <label className="text-xs text-muted font-medium">Nama</label>
              <input value={nama} onChange={e => setNama(e.target.value)} required className="w-full text-sm p-2 border border-gray-300 rounded-lg outline-none focus:ring-2 focus:ring-dpbj-gold/40" />
            </div>
            <button type="submit" disabled={saving} className="btn-primary flex items-center justify-center gap-2 disabled:opacity-50">
              <Plus size={16} /> Tambah
            </button>
          </form>

          <div className="overflow-x-auto rounded-xl border border-border">
            <table className="data-table">
              <thead>
                <tr>
                  <th>Nama</th>
                  <th>Level</th>
                  <th className="text-right">Aksi</th>
                </tr>
              </thead>
              <tbody>
                {children.length === 0 ? (
                  <tr><td colSpan={3} className="py-10 text-center text-muted text-sm">Belum ada data di bawah provinsi ini.</td></tr>
                ) : children.map(row => (
                  <tr key={row.id}>
                    <td className="text-sm font-medium text-dpbj-navy">{row.nama}</td>
                    <td className="text-xs text-muted capitalize">{row.level}</td>
                    <td className="text-right">
                      <button onClick={() => handleDelete(row.id)} className="p-1.5 text-red-500 hover:bg-red-50 rounded-lg transition-colors">
                        <Trash2 size={14} />
                      </button>
                    </td>
                  </tr>
                ))}
              </tbody>
            </table>
          </div>
        </>
      )}
    </div>
  );
}

function ComplainTypeTable() {
  const [data, setData] = useState([]);
  const [isLoading, setIsLoading] = useState(true);
  const [name, setName] = useState('');
  const [description, setDescription] = useState('');
  const [saving, setSaving] = useState(false);

  const fetchData = useCallback(async () => {
    setIsLoading(true);
    try {
      const res = await fetch(`${API_BASE}/inbox/meta/complain-types`);
      const json = await res.json();
      if (json.success) setData(json.data);
    } catch (err) { console.error(err); } finally { setIsLoading(false); }
  }, []);

  useEffect(() => { fetchData(); }, [fetchData]);

  const handleAdd = async (e) => {
    e.preventDefault();
    if (!name.trim()) return;
    setSaving(true);
    try {
      const res = await fetch(`${API_BASE}/inbox/meta/complain-types`, {
        method: 'POST', headers: getAuthHeaders(), body: JSON.stringify({ name, description }),
      });
      const json = await res.json();
      if (json.success) { setName(''); setDescription(''); fetchData(); }
      else alert('Gagal: ' + json.message);
    } catch { alert('Terjadi kesalahan saat menyimpan data.'); } finally { setSaving(false); }
  };

  const handleDelete = async (id) => {
    if (!confirm('Nonaktifkan subjek komplain ini?')) return;
    try {
      const res = await fetch(`${API_BASE}/inbox/meta/complain-types/${id}`, { method: 'DELETE', headers: getAuthHeaders() });
      const json = await res.json();
      if (json.success) fetchData();
      else alert('Gagal: ' + json.message);
    } catch { alert('Terjadi kesalahan saat menghapus data.'); }
  };

  return (
    <div className="space-y-4">
      <form onSubmit={handleAdd} className="flex flex-col gap-2 sm:flex-row sm:items-end bg-surface p-3 rounded-xl">
        <div className="flex-1">
          <label className="block text-[11px] font-semibold text-muted mb-1">Nama Subjek</label>
          <input value={name} onChange={e => setName(e.target.value)} className="w-full text-sm p-2 border border-gray-300 rounded-lg" placeholder="mis. Keterlambatan Proses Tender" />
        </div>
        <div className="flex-1">
          <label className="block text-[11px] font-semibold text-muted mb-1">Keterangan</label>
          <input value={description} onChange={e => setDescription(e.target.value)} className="w-full text-sm p-2 border border-gray-300 rounded-lg" placeholder="Opsional" />
        </div>
        <button type="submit" disabled={saving} className="btn-primary flex items-center gap-1 disabled:opacity-50">
          <Plus size={14} /> Tambah
        </button>
      </form>

      <div className="overflow-x-auto rounded-xl border border-border">
        <table className="data-table">
          <thead><tr><th>Nama</th><th>Keterangan</th><th className="text-right">Aksi</th></tr></thead>
          <tbody>
            {isLoading ? (
              <tr><td colSpan={3} className="py-8 text-center text-muted text-sm">Memuat...</td></tr>
            ) : data.length === 0 ? (
              <tr><td colSpan={3} className="py-8 text-center text-muted text-sm">Belum ada data.</td></tr>
            ) : data.map(d => (
              <tr key={d.id}>
                <td className="text-sm font-medium text-dpbj-navy">{d.name}</td>
                <td className="text-xs text-muted">{d.description || '-'}</td>
                <td className="text-right">
                  <button onClick={() => handleDelete(d.id)} className="text-red-500 hover:text-red-700"><Trash2 size={14} /></button>
                </td>
              </tr>
            ))}
          </tbody>
        </table>
      </div>
    </div>
  );
}

function ComplainRecipientTable() {
  const [data, setData] = useState([]);
  const [isLoading, setIsLoading] = useState(true);
  const [email, setEmail] = useState('');
  const [keterangan, setKeterangan] = useState('');
  const [saving, setSaving] = useState(false);

  const fetchData = useCallback(async () => {
    setIsLoading(true);
    try {
      const res = await fetch(`${API_BASE}/inbox/meta/complain-recipients`);
      const json = await res.json();
      if (json.success) setData(json.data);
    } catch (err) { console.error(err); } finally { setIsLoading(false); }
  }, []);

  useEffect(() => { fetchData(); }, [fetchData]);

  const handleAdd = async (e) => {
    e.preventDefault();
    if (!email.trim()) return;
    setSaving(true);
    try {
      const res = await fetch(`${API_BASE}/inbox/meta/complain-recipients`, {
        method: 'POST', headers: getAuthHeaders(), body: JSON.stringify({ email, keterangan }),
      });
      const json = await res.json();
      if (json.success) { setEmail(''); setKeterangan(''); fetchData(); }
      else alert('Gagal: ' + json.message);
    } catch { alert('Terjadi kesalahan saat menyimpan data.'); } finally { setSaving(false); }
  };

  const handleDelete = async (id) => {
    if (!confirm('Nonaktifkan penerima komplain ini?')) return;
    try {
      const res = await fetch(`${API_BASE}/inbox/meta/complain-recipients/${id}`, { method: 'DELETE', headers: getAuthHeaders() });
      const json = await res.json();
      if (json.success) fetchData();
      else alert('Gagal: ' + json.message);
    } catch { alert('Terjadi kesalahan saat menghapus data.'); }
  };

  return (
    <div className="space-y-4">
      <form onSubmit={handleAdd} className="flex flex-col gap-2 sm:flex-row sm:items-end bg-surface p-3 rounded-xl">
        <div className="flex-1">
          <label className="block text-[11px] font-semibold text-muted mb-1">Email Penerima</label>
          <input type="email" value={email} onChange={e => setEmail(e.target.value)} className="w-full text-sm p-2 border border-gray-300 rounded-lg" placeholder="komplain@dpbj.ui.ac.id" />
        </div>
        <div className="flex-1">
          <label className="block text-[11px] font-semibold text-muted mb-1">Keterangan</label>
          <input value={keterangan} onChange={e => setKeterangan(e.target.value)} className="w-full text-sm p-2 border border-gray-300 rounded-lg" placeholder="Opsional" />
        </div>
        <button type="submit" disabled={saving} className="btn-primary flex items-center gap-1 disabled:opacity-50">
          <Plus size={14} /> Tambah
        </button>
      </form>

      <div className="overflow-x-auto rounded-xl border border-border">
        <table className="data-table">
          <thead><tr><th>Email</th><th>Keterangan</th><th className="text-right">Aksi</th></tr></thead>
          <tbody>
            {isLoading ? (
              <tr><td colSpan={3} className="py-8 text-center text-muted text-sm">Memuat...</td></tr>
            ) : data.length === 0 ? (
              <tr><td colSpan={3} className="py-8 text-center text-muted text-sm">Belum ada data.</td></tr>
            ) : data.map(d => (
              <tr key={d.id}>
                <td className="text-sm font-medium text-dpbj-navy">{d.email}</td>
                <td className="text-xs text-muted">{d.keterangan || '-'}</td>
                <td className="text-right">
                  <button onClick={() => handleDelete(d.id)} className="text-red-500 hover:text-red-700"><Trash2 size={14} /></button>
                </td>
              </tr>
            ))}
          </tbody>
        </table>
      </div>
    </div>
  );
}

export default function DataMaster() {
  const [activeCategory, setActiveCategory] = useState('bank');

  return (
    <div className="space-y-6 animate-fade-in">
      <div className="section-card">
        <div className="flex items-center gap-3 mb-5">
          <div className="w-10 h-10 rounded-xl bg-surface flex items-center justify-center">
            <Database size={20} className="text-dpbj-navy" />
          </div>
          <div>
            <h2 className="text-base font-bold text-dpbj-navy">Data Master</h2>
            <p className="text-xs text-muted">Kelola data referensi yang dipakai di berbagai form pengadaan</p>
          </div>
        </div>

        <div className="flex gap-2 mb-5 overflow-x-auto pb-1">
          {CATEGORIES.map(cat => (
            <button
              key={cat.id}
              onClick={() => setActiveCategory(cat.id)}
              className={clsx(
                'px-4 py-2 text-xs font-bold rounded-full whitespace-nowrap transition-colors',
                activeCategory === cat.id ? 'bg-dpbj-navy text-white' : 'bg-surface text-dpbj-navy hover:bg-gray-200'
              )}
            >
              {cat.label}
            </button>
          ))}
        </div>

        {activeCategory === 'unit_kerja' ? (
          <UnitKerjaTable />
        ) : activeCategory === 'vendor_retail' ? (
          <VendorRetailTable />
        ) : activeCategory === 'katalog_kategori' ? (
          <KatalogCategoryTable />
        ) : activeCategory === 'master_checklist' ? (
          <MasterChecklistTable />
        ) : activeCategory === 'document_templates' ? (
          <DocumentTemplateTable />
        ) : activeCategory === 'holidays' ? (
          <HolidayTable />
        ) : activeCategory === 'regions' ? (
          <RegionTable />
        ) : activeCategory === 'complain_types' ? (
          <ComplainTypeTable />
        ) : activeCategory === 'complain_recipients' ? (
          <ComplainRecipientTable />
        ) : (
          <SimpleMasterTable category={activeCategory} />
        )}
      </div>
    </div>
  );
}
