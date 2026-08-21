import { useState, useEffect } from 'react';
import { getAuthHeaders, useApp, API_BASE } from '../context/AppContext';
import { Package, FileText, CheckCircle, XCircle, Search, Clock } from 'lucide-react';
import { formatRupiah, StatusBadge } from '../components/ui/shared';

export default function Purchasing() {
  const { user, navigateTo } = useApp();
  const [orders, setOrders] = useState([]);
  const [loading, setLoading] = useState(true);
  const [selectedOrder, setSelectedOrder] = useState(null);

  useEffect(() => {
    fetchOrders();
  }, []);

  const fetchOrders = async () => {
    setLoading(true);
    try {
      const url = user.role === 'vendor' 
        ? `${API_BASE}/purchasing?vendor_id=${user.id}` 
        : `${API_BASE}/purchasing?buyer_id=${user.id}`;
      const res = await fetch(url, { headers: getAuthHeaders() });
      const json = await res.json();
      if (json.success) setOrders(json.data);
    } catch (err) {
      console.error(err);
    } finally {
      setLoading(false);
    }
  };

  const loadOrderDetail = async (orderId) => {
    try {
      const res = await fetch(`${API_BASE}/purchasing/${orderId}`, { headers: getAuthHeaders() });
      const json = await res.json();
      if (json.success) setSelectedOrder(json.data);
    } catch (err) {
      console.error(err);
    }
  };

  const handleUpdateStatus = async (status) => {
    if (!confirm(`Ubah status pesanan menjadi ${status}?`)) return;
    try {
      const res = await fetch(`${API_BASE}/purchasing/${selectedOrder.id}/status`, {
        method: 'PATCH',
        headers: { 'Content-Type': 'application/json', ...getAuthHeaders() },
        body: JSON.stringify({ status })
      });
      const json = await res.json();
      if (json.success) {
        alert('Status berhasil diupdate.');
        fetchOrders();
        loadOrderDetail(selectedOrder.id);
      }
    } catch (e) {
      console.error(e);
    }
  };

  return (
    <div className="space-y-6 animate-fade-in">
      <div className="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
          <h1 className="text-2xl font-bold text-dpbj-navy">Purchase Orders (E-Purchasing)</h1>
          <p className="text-sm text-muted mt-1">
            {user.role === 'vendor' ? 'Daftar pesanan masuk dari pembeli.' : 'Riwayat transaksi e-purchasing Anda.'}
          </p>
        </div>
        <button onClick={() => navigateTo('katalog')} className="btn-primary flex items-center gap-2">
          <Search size={18} /> {user.role === 'vendor' ? 'Lihat Katalog' : 'Cari Barang Lain'}
        </button>
      </div>

      {loading ? (
        <div className="text-center py-10 text-muted">Memuat daftar pesanan...</div>
      ) : orders.length === 0 ? (
        <div className="text-center py-12 bg-white rounded-xl border border-dashed border-border text-muted">
          <Package size={48} className="mx-auto mb-3 opacity-50 text-gray-400" />
          <p>Belum ada transaksi E-Purchasing.</p>
        </div>
      ) : (
        <div className="grid grid-cols-1 lg:grid-cols-3 gap-6">
          <div className="lg:col-span-1 space-y-4">
            {orders.map(order => (
              <div 
                key={order.id} 
                onClick={() => loadOrderDetail(order.id)}
                className={`bg-white border rounded-xl p-4 cursor-pointer hover:shadow-md transition-all ${selectedOrder?.id === order.id ? 'border-blue-500 shadow-md ring-1 ring-blue-500' : 'border-border'}`}
              >
                <div className="flex justify-between items-start mb-2">
                  <p className="font-bold text-sm text-dpbj-navy line-clamp-1">
                    {user.role === 'vendor' ? order.buyer_name : order.vendor_name}
                  </p>
                  <StatusBadge status={order.status} />
                </div>
                <p className="text-xs text-muted flex items-center gap-1 mb-2">
                  <Clock size={12} /> {new Date(order.created_at).toLocaleDateString('id-ID')}
                </p>
                <p className="font-bold text-dpbj-gold text-lg">{formatRupiah(order.total_amount)}</p>
              </div>
            ))}
          </div>

          <div className="lg:col-span-2">
            {selectedOrder ? (
              <div className="bg-white border border-border rounded-xl shadow-sm overflow-hidden animate-fade-in-up">
                <div className="p-6 border-b border-border bg-surface flex justify-between items-start">
                  <div>
                    <h2 className="font-bold text-xl text-dpbj-navy mb-1">Detail Purchase Order</h2>
                    <p className="text-sm text-muted">Order ID: <span className="font-mono text-xs">{selectedOrder.id}</span></p>
                  </div>
                  <StatusBadge status={selectedOrder.status} />
                </div>
                
                <div className="p-6 grid grid-cols-2 gap-6 border-b border-border text-sm">
                  <div>
                    <p className="text-xs font-semibold text-muted mb-1">Pembeli (PPK)</p>
                    <p className="font-bold text-dpbj-navy">{selectedOrder.buyer_name}</p>
                    <p className="text-xs text-muted mt-2">Alamat Pengiriman:</p>
                    <p className="text-dpbj-navy">{selectedOrder.delivery_address || '-'}</p>
                  </div>
                  <div>
                    <p className="text-xs font-semibold text-muted mb-1">Penyedia (Vendor)</p>
                    <p className="font-bold text-dpbj-navy">{selectedOrder.vendor_name}</p>
                    <p className="text-xs text-muted mt-2">Catatan:</p>
                    <p className="text-dpbj-navy italic">{selectedOrder.notes || '-'}</p>
                  </div>
                </div>

                <div className="p-6">
                  <h3 className="font-bold text-dpbj-navy mb-4 flex items-center gap-2"><Package size={16}/> Daftar Item Pesanan</h3>
                  <table className="w-full text-left text-sm border-collapse">
                    <thead className="bg-surface border-y border-border">
                      <tr>
                        <th className="py-2 px-3 font-semibold text-xs text-muted">Item</th>
                        <th className="py-2 px-3 font-semibold text-xs text-muted text-right">Harga</th>
                        <th className="py-2 px-3 font-semibold text-xs text-muted text-center">Qty</th>
                        <th className="py-2 px-3 font-semibold text-xs text-muted text-right">Subtotal</th>
                      </tr>
                    </thead>
                    <tbody className="divide-y divide-border">
                      {selectedOrder.items?.map(item => (
                        <tr key={item.id}>
                          <td className="py-3 px-3 font-semibold text-dpbj-navy">{item.item_name}</td>
                          <td className="py-3 px-3 text-right">{formatRupiah(item.price_at_purchase)}</td>
                          <td className="py-3 px-3 text-center">{item.quantity} {item.unit}</td>
                          <td className="py-3 px-3 text-right font-bold text-dpbj-navy">{formatRupiah(item.price_at_purchase * item.quantity)}</td>
                        </tr>
                      ))}
                    </tbody>
                    <tfoot>
                      <tr>
                        <td colSpan="3" className="py-4 px-3 text-right font-bold text-muted">Total Pembayaran</td>
                        <td className="py-4 px-3 text-right font-bold text-dpbj-gold text-xl">{formatRupiah(selectedOrder.total_amount)}</td>
                      </tr>
                    </tfoot>
                  </table>
                </div>

                {/* Aksi Vendor */}
                {user.role === 'vendor' && selectedOrder.status === 'pending' && (
                  <div className="p-6 bg-surface border-t border-border flex justify-end gap-3">
                    <button onClick={() => handleUpdateStatus('rejected')} className="btn-secondary text-red-600 border-red-200 hover:bg-red-50 flex items-center gap-2">
                      <XCircle size={16} /> Tolak Pesanan
                    </button>
                    <button onClick={() => handleUpdateStatus('approved')} className="btn-primary bg-green-600 hover:bg-green-700 text-white flex items-center gap-2">
                      <CheckCircle size={16} /> Proses Pesanan
                    </button>
                  </div>
                )}
                {user.role === 'vendor' && selectedOrder.status === 'approved' && (
                  <div className="p-6 bg-surface border-t border-border flex justify-end gap-3">
                    <button onClick={() => handleUpdateStatus('completed')} className="btn-primary bg-blue-600 hover:bg-blue-700 text-white flex items-center gap-2">
                      <CheckCircle size={16} /> Tandai Selesai Dikirim
                    </button>
                  </div>
                )}
              </div>
            ) : (
              <div className="h-full flex flex-col items-center justify-center text-muted bg-white border border-border rounded-xl p-10 min-h-[400px]">
                <FileText size={48} className="mb-4 opacity-30" />
                <p>Pilih pesanan di sebelah kiri untuk melihat detail.</p>
              </div>
            )}
          </div>
        </div>
      )}
    </div>
  );
}
