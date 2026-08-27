import { useState, useEffect } from 'react';
import { X, ShoppingCart, Trash2, Send, Truck, CheckCircle2, Package } from 'lucide-react';
import { API_BASE, getAuthHeaders } from '../../context/AppContext';
import { formatRupiah } from '../ui/shared';

// Alur pesanan katalog terikat ke satu pengajuan (padanan alur cartupdateNego/statusupdate eProc
// lama): Proses Pemilihan -> Negosiasi -> Penyedia Setuju -> Surat Pesanan -> Proses -> Dikirim -> Diterima
const STATUS_STEPS = ['0', '1', '2', '3', '4', '5', '6'];
const STATUS_LABELS = {
  0: 'Proses Pemilihan', 1: 'Negosiasi', 2: 'Penyedia Setuju',
  3: 'Surat Pesanan', 4: 'Proses', 5: 'Dikirim', 6: 'Diterima',
};

export default function CatalogCartPanel({ procurementRequestId, user, onClose }) {
  const [cart, setCart] = useState([]);
  const [logistik, setLogistik] = useState(null);
  const [ongkosKirim, setOngkosKirim] = useState('');
  const [negoValues, setNegoValues] = useState({});
  const [loading, setLoading] = useState(true);

  const canManage = ['ppk', 'admin'].includes(user.role);

  const fetchAll = async () => {
    setLoading(true);
    try {
      const [c, l] = await Promise.all([
        fetch(`${API_BASE}/katalog/cart/${procurementRequestId}`, { headers: getAuthHeaders() }),
        fetch(`${API_BASE}/katalog/logistik/${procurementRequestId}`, { headers: getAuthHeaders() }),
      ]);
      const [cj, lj] = await Promise.all([c.json(), l.json()]);
      if (cj.success) setCart(cj.data);
      if (lj.success && lj.data) { setLogistik(lj.data); setOngkosKirim(String(lj.data.ongkos_kirim || '')); }
    } catch (err) { console.error(err); } finally { setLoading(false); }
  };

  useEffect(() => { fetchAll(); }, [procurementRequestId]);

  const handleQtyChange = async (cartItemId, qty) => {
    if (qty < 1) return;
    try {
      await fetch(`${API_BASE}/katalog/cart/${cartItemId}/qty`, {
        method: 'PATCH', headers: getAuthHeaders(), body: JSON.stringify({ qty }),
      });
      fetchAll();
    } catch { alert('Gagal mengubah jumlah.'); }
  };

  const handleRemove = async (cartItemId) => {
    if (!confirm('Hapus produk ini dari keranjang?')) return;
    try {
      await fetch(`${API_BASE}/katalog/cart/${cartItemId}`, { method: 'DELETE', headers: getAuthHeaders() });
      fetchAll();
    } catch { alert('Gagal menghapus item.'); }
  };

  const handleSendNegotiation = async () => {
    try {
      const items = cart.map(c => ({ cart_item_id: c.id, harga_nego: negoValues[c.id] ?? c.harga_nego ?? c.harga }));
      const res = await fetch(`${API_BASE}/katalog/cart/negotiate`, {
        method: 'POST', headers: getAuthHeaders(),
        body: JSON.stringify({ procurement_request_id: procurementRequestId, ongkos_kirim: ongkosKirim || null, items, updated_by: user.id }),
      });
      const json = await res.json();
      if (json.success) { alert(json.message); fetchAll(); } else alert('Gagal: ' + json.message);
    } catch { alert('Terjadi kesalahan saat mengirim negosiasi.'); }
  };

  const handleAdvanceStatus = async (cartItemId, currentStatus) => {
    try {
      const res = await fetch(`${API_BASE}/katalog/cart/${cartItemId}/status`, {
        method: 'PATCH', headers: getAuthHeaders(), body: JSON.stringify({ status: currentStatus }),
      });
      const json = await res.json();
      if (json.success) { fetchAll(); } else alert('Gagal: ' + json.message);
    } catch { alert('Terjadi kesalahan saat mengubah status pesanan.'); }
  };

  const total = cart.reduce((s, c) => s + (parseFloat(c.harga_nego || c.harga) * c.qty), 0) + parseFloat(ongkosKirim || 0);

  return (
    <div className="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50 backdrop-blur-sm">
      <div className="bg-white rounded-2xl shadow-xl w-full max-w-3xl overflow-hidden flex flex-col max-h-[88vh]">
        <div className="flex items-center justify-between p-5 border-b border-border bg-surface">
          <div className="flex items-center gap-2">
            <ShoppingCart size={18} className="text-dpbj-navy" />
            <h2 className="font-bold text-dpbj-navy">Keranjang & Pesanan Katalog</h2>
          </div>
          <button onClick={onClose} className="p-2 hover:bg-white rounded-xl"><X size={18} /></button>
        </div>

        <div className="flex-1 overflow-y-auto p-5 space-y-4">
          {loading ? (
            <p className="text-sm text-muted text-center py-10">Memuat...</p>
          ) : cart.length === 0 ? (
            <div className="text-center py-14 text-muted">
              <Package size={40} className="mx-auto mb-3 opacity-50" />
              <p className="text-sm">Keranjang untuk pengajuan ini masih kosong.</p>
            </div>
          ) : (
            <div className="space-y-2">
              {cart.map(c => {
                const currentIdx = STATUS_STEPS.indexOf(String(c.status));
                return (
                  <div key={c.id} className="border border-border rounded-xl p-3">
                    <div className="flex items-center justify-between gap-3">
                      <div className="min-w-0">
                        <p className="font-semibold text-dpbj-navy text-sm truncate">{c.nama_produk}</p>
                        <p className="text-xs text-muted">{c.vendor_name} {c.merek ? `· ${c.merek}` : ''}</p>
                      </div>
                      <span className="badge text-[10px] bg-surface text-dpbj-navy shrink-0">{c.status_label}</span>
                    </div>

                    <div className="flex items-center justify-between mt-2 text-xs">
                      <div className="flex items-center gap-2">
                        <span className="text-muted">Qty:</span>
                        {canManage && c.status == 0 ? (
                          <input type="number" min={1} value={c.qty} onChange={e => handleQtyChange(c.id, parseInt(e.target.value) || 1)} className="w-16 p-1 border border-gray-300 rounded" />
                        ) : (
                          <span className="font-semibold text-dpbj-navy">{c.qty}</span>
                        )}
                      </div>
                      <div className="text-right">
                        {c.harga_nego ? (
                          <p><span className="line-through text-muted mr-1">{formatRupiah(c.harga, true)}</span><span className="font-bold text-blue-600">{formatRupiah(c.harga_nego, true)}</span></p>
                        ) : (
                          <p className="font-bold text-dpbj-navy">{formatRupiah(c.harga, true)}</p>
                        )}
                      </div>
                    </div>

                    {canManage && c.status == 0 && (
                      <div className="flex items-center gap-2 mt-2">
                        <input
                          type="number" placeholder="Harga nego (opsional)"
                          value={negoValues[c.id] ?? ''}
                          onChange={e => setNegoValues({ ...negoValues, [c.id]: e.target.value })}
                          className="flex-1 text-xs p-1.5 border border-gray-300 rounded-lg"
                        />
                        <button onClick={() => handleRemove(c.id)} className="text-red-400"><Trash2 size={14} /></button>
                      </div>
                    )}

                    {canManage && currentIdx >= 0 && currentIdx < STATUS_STEPS.length - 1 && c.status != 0 && (
                      <button onClick={() => handleAdvanceStatus(c.id, c.status)} className="mt-2 text-[11px] font-bold text-blue-600 flex items-center gap-1">
                        <CheckCircle2 size={12} /> Tandai: {STATUS_LABELS[STATUS_STEPS[currentIdx + 1]]}
                      </button>
                    )}

                    {c.no_invoice && <p className="text-[10px] text-muted mt-1 font-mono">Invoice: {c.no_invoice}</p>}
                  </div>
                );
              })}
            </div>
          )}
        </div>

        {cart.length > 0 && (
          <div className="p-5 border-t border-border bg-surface space-y-3">
            {canManage && (
              <div className="flex items-center gap-2">
                <Truck size={15} className="text-muted" />
                <input
                  type="number" placeholder="Ongkos kirim (Rp)"
                  value={ongkosKirim} onChange={e => setOngkosKirim(e.target.value)}
                  className="flex-1 text-xs p-2 border border-gray-300 rounded-lg"
                />
              </div>
            )}
            <div className="flex items-center justify-between">
              <p className="text-sm font-bold text-dpbj-navy">Total: <span className="text-blue-600">{formatRupiah(total, true)}</span></p>
              {canManage && (
                <button onClick={handleSendNegotiation} className="btn-primary bg-blue-600 hover:bg-blue-700 flex items-center gap-2 text-xs">
                  <Send size={14} /> Kirim Harga & Ongkir ke Penyedia
                </button>
              )}
            </div>
          </div>
        )}
      </div>
    </div>
  );
}
