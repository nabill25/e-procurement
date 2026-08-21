import { useState, useEffect, useRef } from 'react';
import { Send, User, MessageCircle, UserCheck, CheckCircle2 } from 'lucide-react';
import { API_BASE } from '../../context/AppContext';

// Panel konfirmasi kehadiran sesi aanwijzing (meniru fitur PESAN='CONFIRMED' di PHPSHOUTBOX eProc lama)
function KonfirmasiKehadiran({ tenderId, user, getAuthHeaders }) {
  const [confirmations, setConfirmations] = useState([]);
  const [confirming, setConfirming] = useState(false);
  const isVendor = user.role === 'vendor';
  const alreadyConfirmed = confirmations.some(c => c.user_id === user.id);

  const fetchConfirmations = async () => {
    try {
      const res = await fetch(`${API_BASE}/tenders/${tenderId}/aanwijzing/confirmations`, { headers: getAuthHeaders() });
      const data = await res.json();
      if (data.success) setConfirmations(data.data);
    } catch (err) { console.error(err); }
  };

  useEffect(() => { fetchConfirmations(); }, [tenderId]);

  const handleConfirm = async () => {
    setConfirming(true);
    try {
      const res = await fetch(`${API_BASE}/tenders/${tenderId}/aanwijzing/confirm`, {
        method: 'POST', headers: getAuthHeaders(), body: JSON.stringify({ user_id: user.id }),
      });
      const data = await res.json();
      if (data.success) fetchConfirmations();
      else alert(data.message);
    } catch { alert('Gagal konfirmasi kehadiran.'); } finally { setConfirming(false); }
  };

  return (
    <div className="p-3 bg-emerald-50 border-b border-emerald-100 flex items-center justify-between gap-3">
      <div className="flex items-center gap-2 text-xs text-emerald-800">
        <UserCheck size={15} />
        <span className="font-semibold">{confirmations.length} peserta konfirmasi hadir</span>
      </div>
      {isVendor && (
        alreadyConfirmed ? (
          <span className="text-[11px] font-semibold text-emerald-700 flex items-center gap-1">
            <CheckCircle2 size={13} /> Anda sudah konfirmasi hadir
          </span>
        ) : (
          <button onClick={handleConfirm} disabled={confirming} className="text-[11px] font-bold bg-emerald-600 text-white px-3 py-1.5 rounded-full hover:bg-emerald-700 disabled:opacity-50">
            {confirming ? 'Memproses...' : 'Konfirmasi Hadir'}
          </button>
        )
      )}
    </div>
  );
}

export default function AanwijzingTab({ tenderId, user, getAuthHeaders }) {
  const [chats, setChats] = useState([]);
  const [message, setMessage] = useState('');
  const [loading, setLoading] = useState(false);
  const scrollRef = useRef(null);

  const fetchChats = async () => {
    try {
      const res = await fetch(`${API_BASE}/tenders/${tenderId}/aanwijzing`, {
        headers: getAuthHeaders()
      });
      const data = await res.json();
      if (data.success) {
        setChats(data.data.filter(c => !c.is_confirmation));
      }
    } catch (err) {
      console.error(err);
    }
  };

  useEffect(() => {
    fetchChats();
    // Opsional: Polling setiap 5 detik agar real-time (karena tidak pakai WebSocket)
    const interval = setInterval(fetchChats, 5000);
    return () => clearInterval(interval);
  }, [tenderId]);

  useEffect(() => {
    if (scrollRef.current) {
      scrollRef.current.scrollTop = scrollRef.current.scrollHeight;
    }
  }, [chats]);

  const handleSend = async (e) => {
    e.preventDefault();
    if (!message.trim()) return;

    setLoading(true);
    try {
      const res = await fetch(`${API_BASE}/tenders/${tenderId}/aanwijzing`, {
        method: 'POST',
        headers: getAuthHeaders(),
        body: JSON.stringify({ user_id: user.id, message })
      });
      const data = await res.json();
      if (data.success) {
        setMessage('');
        fetchChats();
      } else {
        alert(data.message);
      }
    } catch (err) {
      alert('Gagal mengirim pesan');
    } finally {
      setLoading(false);
    }
  };

  return (
    <div className="flex flex-col h-[500px] bg-gray-50 rounded-xl overflow-hidden border border-border animate-fade-in">
      <div className="p-4 bg-white border-b border-border flex items-center gap-3">
        <div className="w-10 h-10 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center shrink-0">
          <MessageCircle size={20} />
        </div>
        <div>
          <h3 className="font-bold text-dpbj-navy text-sm">Forum Pemberian Penjelasan (Aanwijzing)</h3>
          <p className="text-xs text-muted">Forum tanya jawab antara Pokja dan Vendor Terdaftar.</p>
        </div>
      </div>

      <KonfirmasiKehadiran tenderId={tenderId} user={user} getAuthHeaders={getAuthHeaders} />

      <div className="flex-1 overflow-y-auto p-4 space-y-4" ref={scrollRef}>
        {chats.length === 0 ? (
          <div className="h-full flex items-center justify-center text-sm text-gray-400 italic">
            Belum ada pertanyaan. Mulai percakapan pertama!
          </div>
        ) : (
          chats.map(chat => {
            const isMe = chat.user_id === user.id;
            const isPokja = chat.role === 'pokja' || chat.role === 'admin';
            
            return (
              <div key={chat.id} className={`flex flex-col ${isMe ? 'items-end' : 'items-start'}`}>
                <div className="flex items-center gap-1.5 mb-1 text-[10px] text-gray-500 font-medium">
                  {!isMe && <User size={10} />}
                  <span>{isMe ? 'Anda' : chat.user_name} {isPokja ? '(POKJA)' : ''}</span>
                  <span className="text-gray-300">•</span>
                  <span>{new Date(chat.created_at).toLocaleTimeString('id-ID', { hour: '2-digit', minute:'2-digit' })}</span>
                </div>
                <div className={`px-4 py-2.5 rounded-2xl max-w-[85%] text-sm ${isMe ? 'bg-dpbj-navy text-white rounded-tr-sm' : isPokja ? 'bg-emerald-100 text-emerald-900 rounded-tl-sm' : 'bg-white border border-gray-200 text-gray-800 rounded-tl-sm shadow-sm'}`}>
                  {chat.message}
                </div>
              </div>
            );
          })
        )}
      </div>

      <div className="p-4 bg-white border-t border-border">
        <form onSubmit={handleSend} className="flex items-end gap-3">
          <div className="flex-1">
            <textarea
              rows="2"
              placeholder="Ketik pertanyaan atau penjelasan Anda..."
              className="w-full text-sm p-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-dpbj-gold/50 focus:border-dpbj-gold resize-none outline-none"
              value={message}
              onChange={e => setMessage(e.target.value)}
              onKeyDown={e => {
                if (e.key === 'Enter' && !e.shiftKey) {
                  e.preventDefault();
                  handleSend(e);
                }
              }}
            />
          </div>
          <button 
            type="submit" 
            disabled={!message.trim() || loading}
            className="h-[52px] px-6 bg-dpbj-navy text-white rounded-xl hover:bg-blue-900 transition-colors flex items-center justify-center gap-2 disabled:opacity-50"
          >
            <Send size={18} />
          </button>
        </form>
      </div>
    </div>
  );
}
