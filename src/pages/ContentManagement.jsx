import { useState, useEffect, useCallback } from 'react';
import { Newspaper, Plus, Trash2, HelpCircle, Image, Power, FileText, Pencil, X } from 'lucide-react';
import { getAuthHeaders, API_BASE, useApp } from '../context/AppContext';
import clsx from 'clsx';

function NewsTab() {
  const { user } = useApp();
  const [items, setItems] = useState([]);
  const [isLoading, setIsLoading] = useState(true);
  const [form, setForm] = useState({ title: '', content: '', image_url: '' });
  const [saving, setSaving] = useState(false);

  const fetchItems = useCallback(async () => {
    setIsLoading(true);
    try {
      const res = await fetch(`${API_BASE}/cms/news/all`, { headers: getAuthHeaders() });
      const json = await res.json();
      if (json.success) setItems(json.data);
    } catch (err) {
      console.error(err);
    } finally {
      setIsLoading(false);
    }
  }, []);

  useEffect(() => { fetchItems(); }, [fetchItems]);

  const handleAdd = async (e) => {
    e.preventDefault();
    if (!form.title.trim() || !form.content.trim()) return;
    setSaving(true);
    try {
      const res = await fetch(`${API_BASE}/cms/news`, {
        method: 'POST',
        headers: getAuthHeaders(),
        body: JSON.stringify({ ...form, created_by: user.id }),
      });
      const json = await res.json();
      if (json.success) {
        setForm({ title: '', content: '', image_url: '' });
        fetchItems();
      } else {
        alert('Gagal: ' + json.message);
      }
    } catch {
      alert('Terjadi kesalahan saat menyimpan berita.');
    } finally {
      setSaving(false);
    }
  };

  const handleDelete = async (id) => {
    if (!confirm('Hapus berita ini?')) return;
    try {
      const res = await fetch(`${API_BASE}/cms/news/${id}`, { method: 'DELETE', headers: getAuthHeaders() });
      const json = await res.json();
      if (json.success) fetchItems();
      else alert('Gagal: ' + json.message);
    } catch {
      alert('Terjadi kesalahan saat menghapus berita.');
    }
  };

  return (
    <div className="space-y-4">
      <form onSubmit={handleAdd} className="space-y-3 bg-surface p-4 rounded-xl border border-border">
        <div>
          <label className="text-xs text-muted font-medium">Judul</label>
          <input value={form.title} onChange={e => setForm({ ...form, title: e.target.value })} required className="w-full text-sm p-2 border border-gray-300 rounded-lg outline-none focus:ring-2 focus:ring-dpbj-gold/40" />
        </div>
        <div>
          <label className="text-xs text-muted font-medium">Isi Berita</label>
          <textarea rows={3} value={form.content} onChange={e => setForm({ ...form, content: e.target.value })} required className="w-full text-sm p-2 border border-gray-300 rounded-lg outline-none focus:ring-2 focus:ring-dpbj-gold/40 resize-none" />
        </div>
        <div>
          <label className="text-xs text-muted font-medium">URL Gambar (opsional)</label>
          <input value={form.image_url} onChange={e => setForm({ ...form, image_url: e.target.value })} className="w-full text-sm p-2 border border-gray-300 rounded-lg outline-none focus:ring-2 focus:ring-dpbj-gold/40" />
        </div>
        <button type="submit" disabled={saving} className="btn-primary flex items-center gap-2 disabled:opacity-50">
          <Plus size={16} /> Tambah Berita
        </button>
      </form>

      <div className="space-y-2">
        {isLoading ? (
          <p className="text-sm text-muted text-center py-6">Memuat data...</p>
        ) : items.length === 0 ? (
          <p className="text-sm text-muted text-center py-6">Belum ada berita.</p>
        ) : items.map(item => (
          <div key={item.id} className="flex items-start justify-between gap-3 p-3 rounded-xl border border-border">
            <div>
              <p className="text-sm font-semibold text-dpbj-navy">{item.title}</p>
              <p className="text-xs text-muted mt-1 line-clamp-2">{item.content}</p>
              <p className="text-[10px] text-muted mt-1">{new Date(item.created_at).toLocaleString('id-ID')}</p>
            </div>
            <button onClick={() => handleDelete(item.id)} className="p-1.5 text-red-500 hover:bg-red-50 rounded-lg transition-colors shrink-0">
              <Trash2 size={14} />
            </button>
          </div>
        ))}
      </div>
    </div>
  );
}

function FaqTab() {
  const [items, setItems] = useState([]);
  const [isLoading, setIsLoading] = useState(true);
  const [form, setForm] = useState({ question: '', answer: '' });
  const [saving, setSaving] = useState(false);

  const fetchItems = useCallback(async () => {
    setIsLoading(true);
    try {
      const res = await fetch(`${API_BASE}/cms/faq/all`, { headers: getAuthHeaders() });
      const json = await res.json();
      if (json.success) setItems(json.data);
    } catch (err) {
      console.error(err);
    } finally {
      setIsLoading(false);
    }
  }, []);

  useEffect(() => { fetchItems(); }, [fetchItems]);

  const handleAdd = async (e) => {
    e.preventDefault();
    if (!form.question.trim() || !form.answer.trim()) return;
    setSaving(true);
    try {
      const res = await fetch(`${API_BASE}/cms/faq`, {
        method: 'POST',
        headers: getAuthHeaders(),
        body: JSON.stringify(form),
      });
      const json = await res.json();
      if (json.success) {
        setForm({ question: '', answer: '' });
        fetchItems();
      } else {
        alert('Gagal: ' + json.message);
      }
    } catch {
      alert('Terjadi kesalahan saat menyimpan FAQ.');
    } finally {
      setSaving(false);
    }
  };

  const handleDelete = async (id) => {
    if (!confirm('Hapus FAQ ini?')) return;
    try {
      const res = await fetch(`${API_BASE}/cms/faq/${id}`, { method: 'DELETE', headers: getAuthHeaders() });
      const json = await res.json();
      if (json.success) fetchItems();
      else alert('Gagal: ' + json.message);
    } catch {
      alert('Terjadi kesalahan saat menghapus FAQ.');
    }
  };

  return (
    <div className="space-y-4">
      <form onSubmit={handleAdd} className="space-y-3 bg-surface p-4 rounded-xl border border-border">
        <div>
          <label className="text-xs text-muted font-medium">Pertanyaan</label>
          <input value={form.question} onChange={e => setForm({ ...form, question: e.target.value })} required className="w-full text-sm p-2 border border-gray-300 rounded-lg outline-none focus:ring-2 focus:ring-dpbj-gold/40" />
        </div>
        <div>
          <label className="text-xs text-muted font-medium">Jawaban</label>
          <textarea rows={3} value={form.answer} onChange={e => setForm({ ...form, answer: e.target.value })} required className="w-full text-sm p-2 border border-gray-300 rounded-lg outline-none focus:ring-2 focus:ring-dpbj-gold/40 resize-none" />
        </div>
        <button type="submit" disabled={saving} className="btn-primary flex items-center gap-2 disabled:opacity-50">
          <Plus size={16} /> Tambah FAQ
        </button>
      </form>

      <div className="space-y-2">
        {isLoading ? (
          <p className="text-sm text-muted text-center py-6">Memuat data...</p>
        ) : items.length === 0 ? (
          <p className="text-sm text-muted text-center py-6">Belum ada FAQ.</p>
        ) : items.map(item => (
          <div key={item.id} className="flex items-start justify-between gap-3 p-3 rounded-xl border border-border">
            <div>
              <p className="text-sm font-semibold text-dpbj-navy">{item.question}</p>
              <p className="text-xs text-muted mt-1">{item.answer}</p>
            </div>
            <button onClick={() => handleDelete(item.id)} className="p-1.5 text-red-500 hover:bg-red-50 rounded-lg transition-colors shrink-0">
              <Trash2 size={14} />
            </button>
          </div>
        ))}
      </div>
    </div>
  );
}

function BannerTab() {
  const { user } = useApp();
  const [items, setItems] = useState([]);
  const [isLoading, setIsLoading] = useState(true);
  const [nama, setNama] = useState('');
  const [linkUrl, setLinkUrl] = useState('');
  const [file, setFile] = useState(null);
  const [saving, setSaving] = useState(false);

  const fetchItems = useCallback(async () => {
    setIsLoading(true);
    try {
      const res = await fetch(`${API_BASE}/cms/banners/all`, { headers: getAuthHeaders() });
      const json = await res.json();
      if (json.success) setItems(json.data);
    } catch (err) {
      console.error(err);
    } finally {
      setIsLoading(false);
    }
  }, []);

  useEffect(() => { fetchItems(); }, [fetchItems]);

  const handleAdd = async (e) => {
    e.preventDefault();
    if (!nama.trim() || !file) return;
    setSaving(true);
    try {
      const formData = new FormData();
      formData.append('nama', nama);
      formData.append('link_url', linkUrl);
      formData.append('created_by', user.id);
      formData.append('gambar', file);

      const headers = getAuthHeaders();
      delete headers['Content-Type']; // biarkan browser set boundary multipart otomatis

      const res = await fetch(`${API_BASE}/cms/banners`, { method: 'POST', headers, body: formData });
      const json = await res.json();
      if (json.success) {
        setNama(''); setLinkUrl(''); setFile(null);
        fetchItems();
      } else {
        alert('Gagal: ' + json.message);
      }
    } catch {
      alert('Terjadi kesalahan saat menyimpan banner.');
    } finally {
      setSaving(false);
    }
  };

  const handleToggle = async (id) => {
    try {
      const res = await fetch(`${API_BASE}/cms/banners/${id}/toggle`, { method: 'PATCH', headers: getAuthHeaders() });
      const json = await res.json();
      if (json.success) fetchItems();
      else alert('Gagal: ' + json.message);
    } catch {
      alert('Terjadi kesalahan saat mengubah status banner.');
    }
  };

  const handleDelete = async (id) => {
    if (!confirm('Hapus banner ini secara permanen?')) return;
    try {
      const res = await fetch(`${API_BASE}/cms/banners/${id}`, { method: 'DELETE', headers: getAuthHeaders() });
      const json = await res.json();
      if (json.success) fetchItems();
      else alert('Gagal: ' + json.message);
    } catch {
      alert('Terjadi kesalahan saat menghapus banner.');
    }
  };

  return (
    <div className="space-y-4">
      <form onSubmit={handleAdd} className="space-y-3 bg-surface p-4 rounded-xl border border-border">
        <div>
          <label className="text-xs text-muted font-medium">Nama Banner</label>
          <input value={nama} onChange={e => setNama(e.target.value)} required className="w-full text-sm p-2 border border-gray-300 rounded-lg outline-none focus:ring-2 focus:ring-dpbj-gold/40" />
        </div>
        <div>
          <label className="text-xs text-muted font-medium">Link Tujuan (opsional, saat banner diklik)</label>
          <input value={linkUrl} onChange={e => setLinkUrl(e.target.value)} placeholder="https://..." className="w-full text-sm p-2 border border-gray-300 rounded-lg outline-none focus:ring-2 focus:ring-dpbj-gold/40" />
        </div>
        <div>
          <label className="text-xs text-muted font-medium">Gambar Banner (disarankan 1300 x 350 px)</label>
          <input type="file" accept="image/*" onChange={e => setFile(e.target.files[0])} required className="w-full text-sm p-2 border border-gray-300 rounded-lg outline-none" />
        </div>
        <button type="submit" disabled={saving} className="btn-primary flex items-center gap-2 disabled:opacity-50">
          <Plus size={16} /> Tambah Banner
        </button>
      </form>

      <div className="space-y-2">
        {isLoading ? (
          <p className="text-sm text-muted text-center py-6">Memuat data...</p>
        ) : items.length === 0 ? (
          <p className="text-sm text-muted text-center py-6">Belum ada banner.</p>
        ) : items.map(item => (
          <div key={item.id} className="flex items-center justify-between gap-3 p-3 rounded-xl border border-border">
            <div className="flex items-center gap-3">
              <img src={`http://localhost:3001${item.gambar_path}`} alt={item.nama} className="w-24 h-14 object-cover rounded-lg border border-border" />
              <div>
                <p className="text-sm font-semibold text-dpbj-navy">{item.nama}</p>
                {item.link_url && <p className="text-[10px] text-blue-600 truncate max-w-xs">{item.link_url}</p>}
                <span className={clsx('badge text-[10px] mt-1 inline-block', item.is_active ? 'bg-emerald-100 text-emerald-700' : 'bg-gray-100 text-gray-500')}>
                  {item.is_active ? 'Aktif' : 'Nonaktif'}
                </span>
              </div>
            </div>
            <div className="flex items-center gap-1 shrink-0">
              <button onClick={() => handleToggle(item.id)} title={item.is_active ? 'Nonaktifkan' : 'Aktifkan'} className="p-1.5 text-amber-600 hover:bg-amber-50 rounded-lg transition-colors">
                <Power size={14} />
              </button>
              <button onClick={() => handleDelete(item.id)} className="p-1.5 text-red-500 hover:bg-red-50 rounded-lg transition-colors">
                <Trash2 size={14} />
              </button>
            </div>
          </div>
        ))}
      </div>
    </div>
  );
}

function PolicyTab() {
  const { user } = useApp();
  const [items, setItems] = useState([]);
  const [isLoading, setIsLoading] = useState(true);
  const [form, setForm] = useState({ title: '', content: '', jenis: 'umum' });
  const [editingId, setEditingId] = useState(null);
  const [saving, setSaving] = useState(false);

  const fetchItems = useCallback(async () => {
    setIsLoading(true);
    try {
      const res = await fetch(`${API_BASE}/cms/policies/all`, { headers: getAuthHeaders() });
      const json = await res.json();
      if (json.success) setItems(json.data);
    } catch (err) {
      console.error(err);
    } finally {
      setIsLoading(false);
    }
  }, []);

  useEffect(() => { fetchItems(); }, [fetchItems]);

  const resetForm = () => { setForm({ title: '', content: '', jenis: 'umum' }); setEditingId(null); };

  const handleSubmit = async (e) => {
    e.preventDefault();
    if (!form.title.trim() || !form.content.trim()) return;
    setSaving(true);
    try {
      const url = editingId ? `${API_BASE}/cms/policies/${editingId}` : `${API_BASE}/cms/policies`;
      const method = editingId ? 'PUT' : 'POST';
      const res = await fetch(url, {
        method, headers: getAuthHeaders(),
        body: JSON.stringify({ ...form, created_by: user.id }),
      });
      const json = await res.json();
      if (json.success) { resetForm(); fetchItems(); }
      else alert('Gagal: ' + json.message);
    } catch {
      alert('Terjadi kesalahan saat menyimpan kebijakan.');
    } finally {
      setSaving(false);
    }
  };

  const handleTogglePublish = async (item) => {
    try {
      const res = await fetch(`${API_BASE}/cms/policies/${item.id}`, {
        method: 'PUT', headers: getAuthHeaders(), body: JSON.stringify({ is_published: !item.is_published }),
      });
      const json = await res.json();
      if (json.success) fetchItems();
      else alert('Gagal: ' + json.message);
    } catch {
      alert('Terjadi kesalahan saat mengubah status.');
    }
  };

  const handleDelete = async (id) => {
    if (!confirm('Hapus kebijakan ini?')) return;
    try {
      const res = await fetch(`${API_BASE}/cms/policies/${id}`, { method: 'DELETE', headers: getAuthHeaders() });
      const json = await res.json();
      if (json.success) fetchItems();
      else alert('Gagal: ' + json.message);
    } catch {
      alert('Terjadi kesalahan saat menghapus kebijakan.');
    }
  };

  return (
    <div className="space-y-4">
      <form onSubmit={handleSubmit} className="space-y-3 bg-surface p-4 rounded-xl border border-border">
        {editingId && (
          <div className="flex items-center justify-between text-xs text-amber-700 bg-amber-50 px-3 py-1.5 rounded-lg">
            <span>Sedang mengedit kebijakan.</span>
            <button type="button" onClick={resetForm} className="font-bold flex items-center gap-1"><X size={12} /> Batal</button>
          </div>
        )}
        <div>
          <label className="text-xs text-muted font-medium">Judul</label>
          <input value={form.title} onChange={e => setForm({ ...form, title: e.target.value })} required className="w-full text-sm p-2 border border-gray-300 rounded-lg outline-none focus:ring-2 focus:ring-dpbj-gold/40" />
        </div>
        <div>
          <label className="text-xs text-muted font-medium">Jenis (mis. privasi, syarat_ketentuan, umum)</label>
          <input value={form.jenis} onChange={e => setForm({ ...form, jenis: e.target.value })} className="w-full text-sm p-2 border border-gray-300 rounded-lg outline-none focus:ring-2 focus:ring-dpbj-gold/40" />
        </div>
        <div>
          <label className="text-xs text-muted font-medium">Isi Kebijakan (boleh HTML sederhana)</label>
          <textarea rows={6} value={form.content} onChange={e => setForm({ ...form, content: e.target.value })} required className="w-full text-sm p-2 border border-gray-300 rounded-lg outline-none focus:ring-2 focus:ring-dpbj-gold/40 resize-y font-mono" />
        </div>
        <button type="submit" disabled={saving} className="btn-primary flex items-center gap-2 disabled:opacity-50">
          {editingId ? <><Pencil size={16} /> Simpan Perubahan</> : <><Plus size={16} /> Tambah Kebijakan</>}
        </button>
      </form>

      <div className="space-y-2">
        {isLoading ? (
          <p className="text-sm text-muted text-center py-6">Memuat data...</p>
        ) : items.length === 0 ? (
          <p className="text-sm text-muted text-center py-6">Belum ada kebijakan.</p>
        ) : items.map(item => (
          <div key={item.id} className="flex items-start justify-between gap-3 p-3 rounded-xl border border-border">
            <div>
              <div className="flex items-center gap-2">
                <p className="text-sm font-semibold text-dpbj-navy">{item.title}</p>
                <span className="badge text-[10px] bg-surface text-dpbj-navy">{item.jenis}</span>
                <span className={clsx('badge text-[10px]', item.is_published ? 'bg-emerald-100 text-emerald-700' : 'bg-gray-100 text-gray-500')}>
                  {item.is_published ? 'Terbit' : 'Draft'}
                </span>
              </div>
              <p className="text-xs text-muted mt-1 line-clamp-2">{item.content.replace(/<[^>]+>/g, ' ')}</p>
            </div>
            <div className="flex items-center gap-1 shrink-0">
              <button onClick={() => { setForm({ title: item.title, content: item.content, jenis: item.jenis }); setEditingId(item.id); }} className="p-1.5 text-blue-600 hover:bg-blue-50 rounded-lg transition-colors">
                <Pencil size={14} />
              </button>
              <button onClick={() => handleTogglePublish(item)} title={item.is_published ? 'Jadikan draft' : 'Terbitkan'} className="p-1.5 text-amber-600 hover:bg-amber-50 rounded-lg transition-colors">
                <Power size={14} />
              </button>
              <button onClick={() => handleDelete(item.id)} className="p-1.5 text-red-500 hover:bg-red-50 rounded-lg transition-colors">
                <Trash2 size={14} />
              </button>
            </div>
          </div>
        ))}
      </div>
    </div>
  );
}

export default function ContentManagement() {
  const [activeTab, setActiveTab] = useState('news');

  return (
    <div className="space-y-6 animate-fade-in">
      <div className="section-card">
        <div className="flex items-center gap-3 mb-5">
          <div className="w-10 h-10 rounded-xl bg-surface flex items-center justify-center">
            <Newspaper size={20} className="text-dpbj-navy" />
          </div>
          <div>
            <h2 className="text-base font-bold text-dpbj-navy">Kelola Konten</h2>
            <p className="text-xs text-muted">Berita, FAQ, banner, dan kebijakan yang tampil di halaman utama publik</p>
          </div>
        </div>

        <div className="flex gap-2 mb-5 flex-wrap">
          <button
            onClick={() => setActiveTab('news')}
            className={clsx('px-4 py-2 text-xs font-bold rounded-full flex items-center gap-1.5 transition-colors', activeTab === 'news' ? 'bg-dpbj-navy text-white' : 'bg-surface text-dpbj-navy hover:bg-gray-200')}
          >
            <Newspaper size={13} /> Berita & Pengumuman
          </button>
          <button
            onClick={() => setActiveTab('faq')}
            className={clsx('px-4 py-2 text-xs font-bold rounded-full flex items-center gap-1.5 transition-colors', activeTab === 'faq' ? 'bg-dpbj-navy text-white' : 'bg-surface text-dpbj-navy hover:bg-gray-200')}
          >
            <HelpCircle size={13} /> FAQ
          </button>
          <button
            onClick={() => setActiveTab('banner')}
            className={clsx('px-4 py-2 text-xs font-bold rounded-full flex items-center gap-1.5 transition-colors', activeTab === 'banner' ? 'bg-dpbj-navy text-white' : 'bg-surface text-dpbj-navy hover:bg-gray-200')}
          >
            <Image size={13} /> Banner
          </button>
          <button
            onClick={() => setActiveTab('policy')}
            className={clsx('px-4 py-2 text-xs font-bold rounded-full flex items-center gap-1.5 transition-colors', activeTab === 'policy' ? 'bg-dpbj-navy text-white' : 'bg-surface text-dpbj-navy hover:bg-gray-200')}
          >
            <FileText size={13} /> Kebijakan
          </button>
        </div>

        {activeTab === 'news' ? <NewsTab /> : activeTab === 'faq' ? <FaqTab /> : activeTab === 'banner' ? <BannerTab /> : <PolicyTab />}
      </div>
    </div>
  );
}
