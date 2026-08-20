import { useState, useEffect, useCallback } from 'react';
import { Plus, Trash2, Database } from 'lucide-react';
import { API_BASE, useApp } from '../context/AppContext';
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
];

function SimpleMasterTable({ category }) {
  const { getAuthHeaders } = useApp();
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
  const { getAuthHeaders } = useApp();
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
        ) : (
          <SimpleMasterTable category={activeCategory} />
        )}
      </div>
    </div>
  );
}
