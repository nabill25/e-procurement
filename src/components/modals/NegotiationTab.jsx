import { useState, useEffect, useRef } from 'react';
import { Send, User, HandCoins, CheckCircle2, XCircle } from 'lucide-react';
import { API_BASE } from '../../context/AppContext';
import { toast } from '../../lib/toast';

function formatRupiah(value) {
  if (value === null || value === undefined) return '-';
  return 'Rp ' + Number(value).toLocaleString('id-ID');
}

export default function NegotiationTab({ tenderId, vendorId, user, getAuthHeaders, refreshData }) {
  const [info, setInfo] = useState(null);
  const [message, setMessage] = useState('');
  const [offeredPrice, setOfferedPrice] = useState('');
  const [loading, setLoading] = useState(false);
  const [finalPrice, setFinalPrice] = useState('');
  const scrollRef = useRef(null);

  const isPokja = user.role === 'pokja' || user.role === 'admin' || user.role === 'ppk';

  const fetchInfo = async () => {
    try {
      const res = await fetch(`${API_BASE}/tenders/${tenderId}/negotiation/${vendorId}`, {
        headers: getAuthHeaders()
      });
      const data = await res.json();
      if (data.success) setInfo(data.data);
    } catch (err) {
      console.error(err);
    }
  };

  useEffect(() => {
    fetchInfo();
    const interval = setInterval(fetchInfo, 5000);
    return () => clearInterval(interval);
  }, [tenderId, vendorId]);

  useEffect(() => {
    if (scrollRef.current) {
      scrollRef.current.scrollTop = scrollRef.current.scrollHeight;
    }
  }, [info]);

  const handleSend = async (e) => {
    e.preventDefault();
    if (!message.trim()) return;

    setLoading(true);
    try {
      const res = await fetch(`${API_BASE}/tenders/${tenderId}/negotiation/${vendorId}`, {
        method: 'POST',
        headers: getAuthHeaders(),
        body: JSON.stringify({
          user_id: user.id,
          message,
          offered_price: offeredPrice ? Number(offeredPrice) : null,
        })
      });
      const data = await res.json();
      if (data.success) {
        setMessage('');
        setOfferedPrice('');
        fetchInfo();
      } else {
        toast(data.message);
      }
    } catch (err) {
      toast('Gagal mengirim pesan negosiasi.');
    } finally {
      setLoading(false);
    }
  };

  const handleFinalize = async (agreed) => {
    if (agreed && !finalPrice) {
      toast('Isi dulu harga final yang disepakati.');
      return;
    }
    if (!confirm(agreed ? `Sepakati negosiasi dengan harga final ${formatRupiah(finalPrice)}?` : 'Tandai negosiasi ini gagal/tidak disepakati?')) return;

    try {
      const res = await fetch(`${API_BASE}/tenders/${tenderId}/negotiation/${vendorId}/finalize`, {
        method: 'POST',
        headers: getAuthHeaders(),
        body: JSON.stringify({ agreed, final_price: finalPrice ? Number(finalPrice) : null })
      });
      const data = await res.json();
      if (data.success) {
        toast(data.message);
        fetchInfo();
        if (refreshData) refreshData();
      } else {
        toast('Gagal: ' + data.message);
      }
    } catch (err) {
      toast('Terjadi kesalahan saat menyimpan hasil negosiasi.');
    }
  };

  if (!vendorId) {
    return (
      <div className="h-[300px] flex items-center justify-center text-sm text-gray-400 italic bg-gray-50 rounded-xl border border-border">
        Tetapkan pemenang tender terlebih dahulu sebelum memulai negosiasi.
      </div>
    );
  }

  const statusLabel = {
    belum: { label: 'Belum Dimulai', className: 'bg-gray-100 text-gray-600' },
    berlangsung: { label: 'Sedang Berlangsung', className: 'bg-amber-100 text-amber-700' },
    sepakat: { label: 'Sepakat', className: 'bg-emerald-100 text-emerald-700' },
    gagal: { label: 'Gagal', className: 'bg-red-100 text-red-700' },
  };
  const sc = info ? (statusLabel[info.negotiation_status] || statusLabel.belum) : statusLabel.belum;

  return (
    <div className="flex flex-col h-[500px] bg-gray-50 rounded-xl overflow-hidden border border-border animate-fade-in">
      <div className="p-4 bg-white border-b border-border flex items-center justify-between gap-3">
        <div className="flex items-center gap-3">
          <div className="w-10 h-10 rounded-full bg-amber-100 text-amber-600 flex items-center justify-center shrink-0">
            <HandCoins size={20} />
          </div>
          <div>
            <h3 className="font-bold text-dpbj-navy text-sm">Negosiasi Harga dengan Pemenang</h3>
            <p className="text-xs text-muted">
              Harga penawaran awal: <span className="font-semibold">{info ? formatRupiah(info.bid_price) : '...'}</span>
              {info?.negotiated_price ? <> &middot; Harga final: <span className="font-semibold text-emerald-600">{formatRupiah(info.negotiated_price)}</span></> : null}
            </p>
          </div>
        </div>
        <span className={`px-2 py-1 rounded-md text-xs font-semibold ${sc.className}`}>{sc.label}</span>
      </div>

      <div className="flex-1 overflow-y-auto p-4 space-y-4" ref={scrollRef}>
        {!info || info.chats.length === 0 ? (
          <div className="h-full flex items-center justify-center text-sm text-gray-400 italic">
            Belum ada percakapan negosiasi. Mulai penawaran pertama!
          </div>
        ) : (
          info.chats.map(chat => {
            const isMe = chat.user_id === user.id;
            const chatIsPokja = chat.role === 'pokja' || chat.role === 'admin' || chat.role === 'ppk';

            return (
              <div key={chat.id} className={`flex flex-col ${isMe ? 'items-end' : 'items-start'}`}>
                <div className="flex items-center gap-1.5 mb-1 text-[10px] text-gray-500 font-medium">
                  {!isMe && <User size={10} />}
                  <span>{isMe ? 'Anda' : chat.user_name} {chatIsPokja ? '(POKJA/PPK)' : '(VENDOR)'}</span>
                  <span className="text-gray-300">•</span>
                  <span>{new Date(chat.created_at).toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' })}</span>
                </div>
                <div className={`px-4 py-2.5 rounded-2xl max-w-[85%] text-sm ${isMe ? 'bg-dpbj-navy text-white rounded-tr-sm' : chatIsPokja ? 'bg-amber-100 text-amber-900 rounded-tl-sm' : 'bg-white border border-gray-200 text-gray-800 rounded-tl-sm shadow-sm'}`}>
                  {chat.offered_price && (
                    <p className="font-bold mb-1">Tawaran: {formatRupiah(chat.offered_price)}</p>
                  )}
                  {chat.message}
                </div>
              </div>
            );
          })
        )}
      </div>

      {info?.negotiation_status !== 'sepakat' && info?.negotiation_status !== 'gagal' && (
        <div className="p-4 bg-white border-t border-border space-y-3">
          <form onSubmit={handleSend} className="flex items-end gap-3">
            <div className="w-40">
              <label className="text-xs text-muted font-medium">Tawaran Harga (opsional)</label>
              <input
                type="number"
                placeholder="Rp"
                className="w-full text-sm p-2.5 border border-gray-300 rounded-xl focus:ring-2 focus:ring-dpbj-gold/50 focus:border-dpbj-gold outline-none"
                value={offeredPrice}
                onChange={e => setOfferedPrice(e.target.value)}
              />
            </div>
            <div className="flex-1">
              <label className="text-xs text-muted font-medium">Pesan</label>
              <textarea
                rows="1"
                placeholder="Tulis pesan negosiasi..."
                className="w-full text-sm p-2.5 border border-gray-300 rounded-xl focus:ring-2 focus:ring-dpbj-gold/50 focus:border-dpbj-gold resize-none outline-none"
                value={message}
                onChange={e => setMessage(e.target.value)}
              />
            </div>
            <button
              type="submit"
              disabled={!message.trim() || loading}
              className="h-[42px] px-6 bg-dpbj-navy text-white rounded-xl hover:bg-blue-900 transition-colors flex items-center justify-center gap-2 disabled:opacity-50"
            >
              <Send size={18} />
            </button>
          </form>

          {isPokja && (
            <div className="flex items-end gap-3 border-t border-border pt-3">
              <div className="w-48">
                <label className="text-xs text-muted font-medium">Harga Final Disepakati</label>
                <input
                  type="number"
                  placeholder="Rp"
                  className="w-full text-sm p-2.5 border border-gray-300 rounded-xl focus:ring-2 focus:ring-dpbj-gold/50 focus:border-dpbj-gold outline-none"
                  value={finalPrice}
                  onChange={e => setFinalPrice(e.target.value)}
                />
              </div>
              <button
                onClick={() => handleFinalize(true)}
                className="px-4 py-2.5 text-sm font-semibold rounded-xl bg-emerald-500 text-white hover:bg-emerald-600 transition-colors flex items-center gap-2"
              >
                <CheckCircle2 size={16} /> Sepakati
              </button>
              <button
                onClick={() => handleFinalize(false)}
                className="px-4 py-2.5 text-sm font-semibold rounded-xl border-2 border-red-400 text-red-500 hover:bg-red-500 hover:text-white transition-colors flex items-center gap-2"
              >
                <XCircle size={16} /> Gagalkan
              </button>
            </div>
          )}
        </div>
      )}
    </div>
  );
}
