import { useState, useEffect, useCallback } from 'react';
import { Newspaper, Plus, Trash2, HelpCircle } from 'lucide-react';
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
            <p className="text-xs text-muted">Berita/pengumuman dan FAQ yang tampil di halaman utama publik</p>
          </div>
        </div>

        <div className="flex gap-2 mb-5">
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
        </div>

        {activeTab === 'news' ? <NewsTab /> : <FaqTab />}
      </div>
    </div>
  );
}
