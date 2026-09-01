import { useState, useEffect, useRef } from 'react';
import { X, Send, MessageSquare } from 'lucide-react';
import { API_BASE, getAuthHeaders } from '../../context/AppContext';
import { toast } from '../../lib/toast';

// Chat umum per paket (padanan CHATSHOUTBOX di eProc lama) - chat 1-ke-1 antara panitia dan
// satu vendor tertentu, dipisah per konteks (jenis_chat), TERPISAH dari chat aanwijzing
// (broadcast satu ruang untuk semua vendor) dan chat negosiasi (sudah ada tab sendiri).
const JENIS_LABEL = {
  umum: 'Chat Umum',
  evaluasi_teknis: 'Chat Evaluasi Teknis',
  evaluasi_kualifikasi: 'Chat Evaluasi Kualifikasi',
  auction: 'Chat Sesi Lelang',
  kontrak: 'Chat Kontrak',
};

export default function GeneralChatModal({ tenderId, vendorId, vendorName, jenisChat = 'umum', user, onClose }) {
  const [chats, setChats] = useState([]);
  const [message, setMessage] = useState('');
  const [loading, setLoading] = useState(false);
  const scrollRef = useRef(null);

  const fetchChats = async () => {
    try {
      const res = await fetch(
        `${API_BASE}/tenders/${tenderId}/general-chat/${vendorId}?jenis=${jenisChat}&reader_id=${user.id}`,
        { headers: getAuthHeaders() }
      );
      const data = await res.json();
      if (data.success) setChats(data.data);
    } catch (err) { console.error(err); }
  };

  useEffect(() => {
    fetchChats();
    const interval = setInterval(fetchChats, 5000);
    return () => clearInterval(interval);
  }, [tenderId, vendorId, jenisChat]);

  useEffect(() => {
    if (scrollRef.current) scrollRef.current.scrollTop = scrollRef.current.scrollHeight;
  }, [chats]);

  const handleSend = async (e) => {
    e.preventDefault();
    if (!message.trim()) return;
    setLoading(true);
    try {
      const res = await fetch(`${API_BASE}/tenders/${tenderId}/general-chat/${vendorId}`, {
        method: 'POST', headers: getAuthHeaders(),
        body: JSON.stringify({ user_id: user.id, message, jenis_chat: jenisChat }),
      });
      const data = await res.json();
      if (data.success) { setMessage(''); fetchChats(); }
      else toast(data.message);
    } catch { toast('Gagal mengirim pesan.'); } finally { setLoading(false); }
  };

  return (
    <div className="fixed inset-0 z-[70] flex items-center justify-center p-4 bg-black/50 backdrop-blur-sm">
      <div className="bg-white rounded-xl shadow-2xl w-full max-w-lg flex flex-col h-[560px] max-h-[85vh]">
        <div className="p-4 border-b border-border flex items-center justify-between">
          <div className="flex items-center gap-2">
            <MessageSquare size={17} className="text-dpbj-navy" />
            <div>
              <h3 className="font-bold text-dpbj-navy text-sm">{JENIS_LABEL[jenisChat] || 'Chat'}</h3>
              <p className="text-xs text-muted">dengan {vendorName}</p>
            </div>
          </div>
          <button onClick={onClose}><X size={18} className="text-muted" /></button>
        </div>

        <div className="flex-1 overflow-y-auto p-4 space-y-3 bg-gray-50" ref={scrollRef}>
          {chats.length === 0 ? (
            <div className="h-full flex items-center justify-center text-sm text-gray-400 italic">
              Belum ada pesan. Mulai percakapan.
            </div>
          ) : (
            chats.map(c => {
              const isMe = c.user_id === user.id;
              return (
                <div key={c.id} className={`flex flex-col ${isMe ? 'items-end' : 'items-start'}`}>
                  <div className="text-[10px] text-gray-500 font-medium mb-1">
                    {isMe ? 'Anda' : c.user_name} · {new Date(c.created_at).toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' })}
                  </div>
                  <div className={`px-3 py-2 rounded-2xl max-w-[85%] text-sm ${isMe ? 'bg-dpbj-navy text-white rounded-tr-sm' : 'bg-white border border-gray-200 text-gray-800 rounded-tl-sm shadow-sm'}`}>
                    {c.message}
                  </div>
                </div>
              );
            })
          )}
        </div>

        <form onSubmit={handleSend} className="p-3 border-t border-border flex items-end gap-2">
          <textarea
            rows="2"
            placeholder="Ketik pesan..."
            className="flex-1 text-sm p-2.5 border border-gray-300 rounded-xl resize-none outline-none focus:ring-2 focus:ring-dpbj-gold/40"
            value={message}
            onChange={e => setMessage(e.target.value)}
            onKeyDown={e => { if (e.key === 'Enter' && !e.shiftKey) { e.preventDefault(); handleSend(e); } }}
          />
          <button type="submit" disabled={!message.trim() || loading} className="h-[44px] px-4 bg-dpbj-navy text-white rounded-xl hover:bg-blue-900 disabled:opacity-50">
            <Send size={16} />
          </button>
        </form>
      </div>
    </div>
  );
}
