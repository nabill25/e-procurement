import { useState, useEffect } from 'react';
import { getAuthHeaders, useApp, API_BASE } from '../context/AppContext';
import { Search, ShoppingCart, Filter, Plus, Package } from 'lucide-react';
import { formatRupiah, StatusBadge } from '../components/ui/shared';
import KatalogDetailModal from '../components/modals/KatalogDetailModal';

const EMPTY_FORM = {
  item_name: '', description: '', price: '', unit: 'Pcs',
  brand: '', model_type: '', item_code: '',
  diameter: '', panjang: '', lebar: '', tinggi: '', unit_pengukuran: '',
  tkdn_persen: '', jenis_produk: '', lama_garansi: '', lama_garansi_satuan: 'Bulan',
  jumlah_stock: '', jumlah_stock_ready: '', kemasan: '', keterangan_tambahan: '',
  category_ids: [],
};

export default function Katalog() {
  const { user, navigateTo } = useApp();
  const [items, setItems] = useState([]);
  const [search, setSearch] = useState('');
  const [loading, setLoading] = useState(true);
  const [categories, setCategories] = useState([]);
  const [detailId, setDetailId] = useState(null);
  const [editingId, setEditingId] = useState(null);

  // Modal Tambah Item (Untuk Vendor)
  const [showAddModal, setShowAddModal] = useState(false);
  const [formData, setFormData] = useState(EMPTY_FORM);

  // Keranjang Belanja (Untuk PPK)
  const [cart, setCart] = useState([]);

  useEffect(() => {
    fetchItems();
  }, [search]);

  useEffect(() => {
    fetch(`${API_BASE}/katalog/categories/tree`).then(r => r.json()).then(j => { if (j.success) setCategories(j.data); }).catch(() => {});
  }, []);

  const fetchItems = async () => {
    setLoading(true);
    try {
      const url = user.role === 'vendor' 
        ? `${API_BASE}/katalog?vendor_id=${user.id}&search=${search}` 
        : `${API_BASE}/katalog?search=${search}`;
      const res = await fetch(url, { headers: getAuthHeaders() });
      const json = await res.json();
      if (json.success) setItems(json.data);
    } catch (err) {
      console.error(err);
    } finally {
      setLoading(false);
    }
  };

  const handleAddItem = async (e) => {
    e.preventDefault();
    try {
      const payload = { ...formData, vendor_id: user.id, created_by: user.id };
      const url = editingId ? `${API_BASE}/katalog/${editingId}` : `${API_BASE}/katalog`;
      const method = editingId ? 'PUT' : 'POST';
      const res = await fetch(url, {
        method,
        headers: { 'Content-Type': 'application/json', ...getAuthHeaders() },
        body: JSON.stringify(payload)
      });
      const json = await res.json();
      if (json.success) {
        alert(editingId ? 'Produk berhasil diperbarui!' : 'Produk berhasil ditambahkan ke katalog!');
        setShowAddModal(false);
        setEditingId(null);
        setFormData(EMPTY_FORM);
        fetchItems();
      } else {
        alert('Gagal: ' + json.message);
      }
    } catch (err) {
      alert('Error: ' + err.message);
    }
  };

  const openEdit = async (item) => {
    const res = await fetch(`${API_BASE}/katalog/${item.id}`, { headers: getAuthHeaders() });
    const json = await res.json();
    if (!json.success) return alert('Gagal memuat data produk.');
    const d = json.data;
    setFormData({
      item_name: d.item_name || '', description: d.description || '', price: d.price || '', unit: d.unit || 'Pcs',
      brand: d.brand || '', model_type: d.model_type || '', item_code: d.item_code || '',
      diameter: d.diameter || '', panjang: d.panjang || '', lebar: d.lebar || '', tinggi: d.tinggi || '', unit_pengukuran: d.unit_pengukuran || '',
      tkdn_persen: d.tkdn_persen || '', jenis_produk: d.jenis_produk || '', lama_garansi: d.lama_garansi || '', lama_garansi_satuan: d.lama_garansi_satuan || 'Bulan',
      jumlah_stock: d.jumlah_stock || '', jumlah_stock_ready: d.jumlah_stock_ready || '', kemasan: d.kemasan || '', keterangan_tambahan: d.keterangan_tambahan || '',
      category_ids: (d.categories || []).map(c => c.id),
    });
    setEditingId(item.id);
    setShowAddModal(true);
  };

  const toggleCategory = (catId) => {
    setFormData(f => ({
      ...f,
      category_ids: f.category_ids.includes(catId) ? f.category_ids.filter(id => id !== catId) : [...f.category_ids, catId],
    }));
  };

  const addToCart = (item) => {
    if (user.role !== 'ppk') return alert('Hanya PPK yang dapat melakukan purchasing langsung.');
    const existing = cart.find(c => c.id === item.id);
    if (existing) {
      setCart(cart.map(c => c.id === item.id ? { ...c, quantity: c.quantity + 1 } : c));
    } else {
      setCart([...cart, { ...item, quantity: 1 }]);
    }
    alert(`${item.item_name} ditambahkan ke keranjang.`);
  };

  const handleCheckout = async () => {
    if (cart.length === 0) return;
    
    // Group by vendor (e-purchasing assumes 1 order = 1 vendor, but for simplicity here we assume the cart items are from the same vendor, or we group them)
    // Untuk purwarupa ini, asumsikan checkout 1 per 1 vendor atau group otomatis.
    const vendorGroups = cart.reduce((acc, item) => {
      acc[item.vendor_id] = acc[item.vendor_id] || [];
      acc[item.vendor_id].push(item);
      return acc;
    }, {});

    for (const [vendorId, vendorItems] of Object.entries(vendorGroups)) {
      const totalAmount = vendorItems.reduce((sum, item) => sum + (item.price * item.quantity), 0);
      
      const payload = {
        buyer_id: user.id,
        vendor_id: vendorId,
        total_amount: totalAmount,
        delivery_address: 'Gedung Rektorat Kampus UI Depok',
        notes: 'Mohon segera diproses.',
        items: vendorItems.map(i => ({ id: i.id, quantity: i.quantity, price: i.price }))
      };

      try {
        await fetch(`${API_BASE}/purchasing`, {
          method: 'POST',
          headers: { 'Content-Type': 'application/json', ...getAuthHeaders() },
          body: JSON.stringify(payload)
        });
      } catch (err) {
        console.error('Checkout error', err);
      }
    }

    alert('Checkout berhasil! Purchase Order telah dibuat.');
    setCart([]);
    navigateTo('purchasing'); // Arahkan ke halaman riwayat order
  };

  return (
    <div className="space-y-6 animate-fade-in">
      <div className="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
          <h1 className="text-2xl font-bold text-dpbj-navy">E-Katalog / Purchasing</h1>
          <p className="text-sm text-muted mt-1">
            {user.role === 'vendor' ? 'Kelola produk katalog Anda.' : 'Pesan langsung barang & jasa dari e-katalog terverifikasi.'}
          </p>
        </div>

        <div className="flex items-center gap-3">
          {user.role === 'vendor' && (
            <button onClick={() => setShowAddModal(true)} className="btn-primary flex items-center gap-2">
              <Plus size={18} /> Tambah Produk
            </button>
          )}
          {user.role === 'ppk' && (
            <button 
              onClick={() => cart.length > 0 && navigateTo('purchasing')} // Bisa arahkan ke modal checkout
              className="btn-secondary flex items-center gap-2 relative bg-surface"
            >
              <ShoppingCart size={18} />
              Keranjang ({cart.length})
            </button>
          )}
        </div>
      </div>

      <div className="bg-white p-4 rounded-xl shadow-sm border border-border flex gap-4 items-center">
        <div className="relative flex-1">
          <Search className="absolute left-3 top-1/2 -translate-y-1/2 text-muted" size={18} />
          <input 
            type="text" 
            placeholder="Cari nama produk atau vendor..." 
            className="form-input w-full pl-10"
            value={search}
            onChange={e => setSearch(e.target.value)}
          />
        </div>
        <button className="btn-secondary flex items-center gap-2 px-4"><Filter size={18}/> Filter</button>
      </div>

      {loading ? (
        <div className="text-center py-10 text-muted">Memuat katalog...</div>
      ) : items.length === 0 ? (
        <div className="text-center py-12 bg-surface rounded-xl border border-dashed border-border text-muted">
          <Package size={48} className="mx-auto mb-3 opacity-50" />
          <p>Belum ada produk di e-katalog.</p>
        </div>
      ) : (
        <div className="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
          {items.map(item => (
            <div key={item.id} className="bg-white border border-border rounded-xl overflow-hidden hover:shadow-lg transition-all flex flex-col group">
              <div className="h-40 bg-gray-100 flex items-center justify-center text-gray-400 group-hover:bg-gray-200 transition-colors">
                {item.image_url ? <img src={item.image_url} alt={item.item_name} className="w-full h-full object-cover" /> : <Package size={40} />}
              </div>
              <div className="p-4 flex flex-col flex-1">
                <button onClick={() => setDetailId(item.id)} className="text-left">
                  <h3 className="font-bold text-dpbj-navy mb-1 line-clamp-2 hover:text-dpbj-gold transition-colors">{item.item_name}</h3>
                </button>
                <p className="text-xs text-muted mb-3 flex-1">{item.description}</p>
                <div className="mt-auto">
                  <p className="text-xs text-muted mb-1">Vendor: <span className="font-semibold text-dpbj-navy">{item.company_name}</span></p>
                  <p className="font-bold text-dpbj-gold text-lg mb-4">{formatRupiah(item.price)} <span className="text-xs text-muted font-normal">/ {item.unit}</span></p>

                  <div className="flex gap-2">
                    <button onClick={() => setDetailId(item.id)} className="flex-1 py-2 bg-surface text-dpbj-navy hover:bg-gray-200 font-semibold text-sm rounded-lg transition-colors border border-border">
                      Detail
                    </button>
                    {user.role === 'ppk' && (
                      <button
                        onClick={() => addToCart(item)}
                        className="flex-1 py-2 bg-blue-50 text-blue-600 hover:bg-blue-600 hover:text-white font-semibold text-sm rounded-lg transition-colors border border-blue-200 flex items-center justify-center gap-2"
                      >
                        <ShoppingCart size={16} /> Tambah
                      </button>
                    )}
                    {(user.role === 'vendor' && item.vendor_id === user.id) && (
                      <button onClick={() => openEdit(item)} className="flex-1 py-2 bg-surface text-dpbj-navy hover:bg-gray-200 font-semibold text-sm rounded-lg transition-colors border border-border">
                        Edit
                      </button>
                    )}
                  </div>
                </div>
              </div>
            </div>
          ))}
        </div>
      )}

      {/* Tampilan Checkout Sementara / Floating Bar */}
      {user.role === 'ppk' && cart.length > 0 && (
        <div className="fixed bottom-0 left-0 right-0 bg-white border-t border-border shadow-[0_-10px_30px_rgba(0,0,0,0.05)] p-4 z-40 flex items-center justify-between px-8 md:pl-[280px]">
          <div>
            <p className="text-sm font-bold text-dpbj-navy">{cart.length} Jenis Produk Terpilih</p>
            <p className="text-xs text-muted">Total Belanja: <span className="font-bold text-blue-600">{formatRupiah(cart.reduce((s,i) => s + (i.price * i.quantity), 0))}</span></p>
          </div>
          <button onClick={handleCheckout} className="btn-primary bg-blue-600 hover:bg-blue-700">Checkout Sekarang</button>
        </div>
      )}

      {/* Modal Tambah/Edit Produk */}
      {showAddModal && (
        <div className="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50 backdrop-blur-sm">
          <div className="bg-white rounded-xl shadow-2xl w-full max-w-2xl overflow-hidden flex flex-col max-h-[90vh]">
            <div className="flex items-center justify-between p-4 border-b border-border bg-surface">
              <h2 className="font-bold text-dpbj-navy">{editingId ? 'Edit Produk Katalog' : 'Tambah Produk Katalog'}</h2>
              <button onClick={() => { setShowAddModal(false); setEditingId(null); setFormData(EMPTY_FORM); }} className="p-1 hover:bg-white rounded"><Package size={18}/></button>
            </div>
            <form onSubmit={handleAddItem} className="p-6 space-y-4 overflow-y-auto">
              <div>
                <label className="block text-sm font-semibold text-dpbj-navy mb-1">Nama Produk/Jasa *</label>
                <input type="text" required className="form-input w-full" value={formData.item_name} onChange={e => setFormData({...formData, item_name: e.target.value})} />
              </div>
              <div>
                <label className="block text-sm font-semibold text-dpbj-navy mb-1">Deskripsi Singkat</label>
                <textarea className="form-input w-full" rows="2" value={formData.description} onChange={e => setFormData({...formData, description: e.target.value})}></textarea>
              </div>
              <div className="grid grid-cols-3 gap-3">
                <div>
                  <label className="block text-xs font-semibold text-dpbj-navy mb-1">Harga Satuan (Rp) *</label>
                  <input type="number" required className="form-input w-full text-sm" value={formData.price} onChange={e => setFormData({...formData, price: e.target.value})} />
                </div>
                <div>
                  <label className="block text-xs font-semibold text-dpbj-navy mb-1">Satuan *</label>
                  <input type="text" required className="form-input w-full text-sm" value={formData.unit} onChange={e => setFormData({...formData, unit: e.target.value})} />
                </div>
                <div>
                  <label className="block text-xs font-semibold text-dpbj-navy mb-1">Kode Produk</label>
                  <input type="text" className="form-input w-full text-sm" value={formData.item_code} onChange={e => setFormData({...formData, item_code: e.target.value})} />
                </div>
              </div>
              <div className="grid grid-cols-3 gap-3">
                <div>
                  <label className="block text-xs font-semibold text-dpbj-navy mb-1">Merek</label>
                  <input type="text" className="form-input w-full text-sm" value={formData.brand} onChange={e => setFormData({...formData, brand: e.target.value})} />
                </div>
                <div>
                  <label className="block text-xs font-semibold text-dpbj-navy mb-1">Model/Tipe</label>
                  <input type="text" className="form-input w-full text-sm" value={formData.model_type} onChange={e => setFormData({...formData, model_type: e.target.value})} />
                </div>
                <div>
                  <label className="block text-xs font-semibold text-dpbj-navy mb-1">Jenis Produk</label>
                  <input type="text" className="form-input w-full text-sm" value={formData.jenis_produk} onChange={e => setFormData({...formData, jenis_produk: e.target.value})} />
                </div>
              </div>
              <div className="grid grid-cols-4 gap-3">
                <div>
                  <label className="block text-xs font-semibold text-dpbj-navy mb-1">Panjang</label>
                  <input type="number" className="form-input w-full text-sm" value={formData.panjang} onChange={e => setFormData({...formData, panjang: e.target.value})} />
                </div>
                <div>
                  <label className="block text-xs font-semibold text-dpbj-navy mb-1">Lebar</label>
                  <input type="number" className="form-input w-full text-sm" value={formData.lebar} onChange={e => setFormData({...formData, lebar: e.target.value})} />
                </div>
                <div>
                  <label className="block text-xs font-semibold text-dpbj-navy mb-1">Tinggi</label>
                  <input type="number" className="form-input w-full text-sm" value={formData.tinggi} onChange={e => setFormData({...formData, tinggi: e.target.value})} />
                </div>
                <div>
                  <label className="block text-xs font-semibold text-dpbj-navy mb-1">Satuan Ukuran</label>
                  <input type="text" placeholder="cm" className="form-input w-full text-sm" value={formData.unit_pengukuran} onChange={e => setFormData({...formData, unit_pengukuran: e.target.value})} />
                </div>
              </div>
              <div className="grid grid-cols-4 gap-3">
                <div>
                  <label className="block text-xs font-semibold text-dpbj-navy mb-1">TKDN (%)</label>
                  <input type="number" className="form-input w-full text-sm" value={formData.tkdn_persen} onChange={e => setFormData({...formData, tkdn_persen: e.target.value})} />
                </div>
                <div>
                  <label className="block text-xs font-semibold text-dpbj-navy mb-1">Lama Garansi</label>
                  <input type="number" className="form-input w-full text-sm" value={formData.lama_garansi} onChange={e => setFormData({...formData, lama_garansi: e.target.value})} />
                </div>
                <div>
                  <label className="block text-xs font-semibold text-dpbj-navy mb-1">Satuan Garansi</label>
                  <select className="form-input w-full text-sm" value={formData.lama_garansi_satuan} onChange={e => setFormData({...formData, lama_garansi_satuan: e.target.value})}>
                    <option value="Bulan">Bulan</option>
                    <option value="Tahun">Tahun</option>
                  </select>
                </div>
                <div>
                  <label className="block text-xs font-semibold text-dpbj-navy mb-1">Kemasan</label>
                  <input type="text" className="form-input w-full text-sm" value={formData.kemasan} onChange={e => setFormData({...formData, kemasan: e.target.value})} />
                </div>
              </div>
              <div className="grid grid-cols-2 gap-3">
                <div>
                  <label className="block text-xs font-semibold text-dpbj-navy mb-1">Jumlah Stok</label>
                  <input type="number" className="form-input w-full text-sm" value={formData.jumlah_stock} onChange={e => setFormData({...formData, jumlah_stock: e.target.value})} />
                </div>
                <div>
                  <label className="block text-xs font-semibold text-dpbj-navy mb-1">Ketersediaan Stok</label>
                  <input type="text" placeholder="Ready/Indent" className="form-input w-full text-sm" value={formData.jumlah_stock_ready} onChange={e => setFormData({...formData, jumlah_stock_ready: e.target.value})} />
                </div>
              </div>
              <div>
                <label className="block text-sm font-semibold text-dpbj-navy mb-1">Kategori</label>
                <div className="flex flex-wrap gap-1.5 border border-gray-300 rounded-lg p-2 max-h-28 overflow-y-auto">
                  {categories.length === 0 ? <p className="text-xs text-muted">Belum ada kategori. Tambahkan lewat Data Master.</p> : categories.map(cat => (
                    <button
                      type="button"
                      key={cat.id}
                      onClick={() => toggleCategory(cat.id)}
                      className={`text-xs px-2.5 py-1 rounded-full border ${formData.category_ids.includes(cat.id) ? 'bg-dpbj-navy text-white border-dpbj-navy' : 'bg-surface text-dpbj-navy border-border'}`}
                    >
                      {cat.nama}
                    </button>
                  ))}
                </div>
              </div>
              <div>
                <label className="block text-sm font-semibold text-dpbj-navy mb-1">Keterangan Tambahan</label>
                <textarea className="form-input w-full" rows="2" value={formData.keterangan_tambahan} onChange={e => setFormData({...formData, keterangan_tambahan: e.target.value})}></textarea>
              </div>
              <div className="pt-4 flex justify-end gap-3 border-t border-border">
                <button type="button" onClick={() => { setShowAddModal(false); setEditingId(null); setFormData(EMPTY_FORM); }} className="btn-secondary">Batal</button>
                <button type="submit" className="btn-primary">{editingId ? 'Simpan Perubahan' : 'Simpan Produk'}</button>
              </div>
            </form>
          </div>
        </div>
      )}

      <KatalogDetailModal isOpen={!!detailId} onClose={() => setDetailId(null)} katalogId={detailId} />
    </div>
  );
}
