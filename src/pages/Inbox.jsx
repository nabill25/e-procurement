import { useState, useEffect, useCallback } from 'react';
import { Inbox as InboxIcon, Mail, MailOpen, CheckCircle2, Send } from 'lucide-react';
import { API_BASE, useApp } from '../context/AppContext';
import clsx from 'clsx';

const STATUS_CFG = {
  belum_dibaca: { label: 'Belum Dibaca', className: 'bg-blue-100 text-blue-700', icon: Mail },
  dibaca:       { label: 'Dibaca',       className: 'bg-gray-100 text-gray-600', icon: MailOpen },
  dibalas:      { label: 'Dibalas',      className: 'bg-emerald-100 text-emerald-700', icon: CheckCircle2 },
};

export default function Inbox() {
  const { user, getAuthHeaders } = useApp();
  const [messages, setMessages] = useState([]);
  const [isLoading, setIsLoading] = useState(true);
  const [selected, setSelected] = useState(null);
  const [replyText, setReplyText] = useState('');
  const [sending, setSending] = useState(false);

  const fetchMessages = useCallback(async () => {
    setIsLoading(true);
    try {
      const res = await fetch(`${API_BASE}/inbox`, { headers: getAuthHeaders() });
      const json = await res.json();
      if (json.success) setMessages(json.data);
    } catch (err) {
      console.error('Failed to fetch inbox:', err);
    } finally {
      setIsLoading(false);
    }
  }, []);

  useEffect(() => { fetchMessages(); }, [fetchMessages]);

  const openMessage = async (msg) => {
    try {
      const res = await fetch(`${API_BASE}/inbox/${msg.id}`, { headers: getAuthHeaders() });
      const json = await res.json();
      if (json.success) setSelected(json.data);

      if (msg.status === 'belum_dibaca') {
        await fetch(`${API_BASE}/inbox/${msg.id}/read`, {
          method: 'PATCH',
          headers: getAuthHeaders(),
          body: JSON.stringify({ read_by: user.id }),
        });
        fetchMessages();
      }
    } catch (err) {
      console.error(err);
    }
  };

  const handleReply = async (e) => {
    e.preventDefault();
    if (!replyText.trim() || !selected) return;
    setSending(true);
    try {
      const res = await fetch(`${API_BASE}/inbox/${selected.id}/reply`, {
        method: 'POST',
        headers: getAuthHeaders(),
        body: JSON.stringify({ content: replyText, replied_by: user.id }),
      });
      const json = await res.json();
      if (json.success) {
        setReplyText('');
        openMessage(selected);
        fetchMessages();
      } else {
        alert('Gagal: ' + json.message);
      }
    } catch {
      alert('Terjadi kesalahan saat mengirim balasan.');
    } finally {
      setSending(false);
    }
  };

  const unreadCount = messages.filter(m => m.status === 'belum_dibaca').length;

  return (
    <div className="space-y-6 animate-fade-in">
      <div className="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {/* List pesan */}
        <div className="section-card lg:col-span-1 flex flex-col max-h-[75vh]">
          <div className="flex items-center gap-3 mb-4">
            <div className="w-10 h-10 rounded-xl bg-surface flex items-center justify-center">
              <InboxIcon size={20} className="text-dpbj-navy" />
            </div>
            <div>
              <h2 className="text-base font-bold text-dpbj-navy">Pusat Pesan</h2>
              <p className="text-xs text-muted">{unreadCount} pesan belum dibaca</p>
            </div>
          </div>

          <div className="flex-1 overflow-y-auto space-y-2">
            {isLoading ? (
              <p className="text-sm text-muted text-center py-8">Memuat data...</p>
            ) : messages.length === 0 ? (
              <p className="text-sm text-muted text-center py-8">Belum ada pesan masuk.</p>
            ) : messages.map(msg => {
              const cfg = STATUS_CFG[msg.status] || STATUS_CFG.belum_dibaca;
              const Icon = cfg.icon;
              return (
                <button
                  key={msg.id}
                  onClick={() => openMessage(msg)}
                  className={clsx(
                    'w-full text-left p-3 rounded-xl border transition-colors',
                    selected?.id === msg.id ? 'border-dpbj-gold bg-dpbj-gold-faint' : 'border-border hover:bg-surface'
                  )}
                >
                  <div className="flex items-center justify-between gap-2 mb-1">
                    <span className="text-sm font-semibold text-dpbj-navy truncate">{msg.sender_name}</span>
                    <span className={clsx('badge text-[10px] flex items-center gap-1', cfg.className)}>
                      <Icon size={10} /> {cfg.label}
                    </span>
                  </div>
                  <p className="text-xs text-muted truncate">{msg.subject}</p>
                  <p className="text-[10px] text-muted mt-1">{new Date(msg.created_at).toLocaleString('id-ID')}</p>
                </button>
              );
            })}
          </div>
        </div>

        {/* Detail pesan */}
        <div className="section-card lg:col-span-2">
          {!selected ? (
            <div className="h-full flex items-center justify-center text-sm text-muted italic py-20">
              Pilih pesan di sebelah kiri untuk melihat detail.
            </div>
          ) : (
            <div className="space-y-5">
              <div className="border-b border-border pb-4">
                <h3 className="font-bold text-dpbj-navy text-lg">{selected.subject}</h3>
                <div className="flex items-center gap-3 mt-2 text-xs text-muted">
                  <span className="font-semibold text-dpbj-navy">{selected.sender_name}</span>
                  <span>&lt;{selected.sender_email}&gt;</span>
                  {selected.sender_phone && <span>{selected.sender_phone}</span>}
                </div>
                {selected.category_name && (
                  <span className="badge text-[10px] bg-surface text-dpbj-navy mt-2 inline-block">{selected.category_name}</span>
                )}
              </div>

              <p className="text-sm text-dpbj-navy leading-relaxed whitespace-pre-wrap">{selected.content}</p>

              {selected.attachment_path && (
                <a href={selected.attachment_path} target="_blank" rel="noreferrer" className="text-xs text-blue-600 hover:underline">
                  Lihat lampiran
                </a>
              )}

              {selected.replies?.length > 0 && (
                <div className="space-y-3 border-t border-border pt-4">
                  <p className="text-xs font-semibold text-muted uppercase tracking-wide">Riwayat Balasan</p>
                  {selected.replies.map(r => (
                    <div key={r.id} className="bg-surface rounded-xl p-3 text-sm text-dpbj-navy">
                      <p className="whitespace-pre-wrap">{r.content}</p>
                      <p className="text-[10px] text-muted mt-2">{new Date(r.created_at).toLocaleString('id-ID')}</p>
                    </div>
                  ))}
                </div>
              )}

              <form onSubmit={handleReply} className="border-t border-border pt-4 space-y-3">
                <label className="text-xs text-muted font-medium">Balas Pesan</label>
                <textarea
                  rows={4}
                  value={replyText}
                  onChange={e => setReplyText(e.target.value)}
                  className="w-full text-sm p-3 border border-gray-300 rounded-xl outline-none focus:ring-2 focus:ring-dpbj-gold/40 resize-none"
                  placeholder="Tulis balasan..."
                />
                <button type="submit" disabled={!replyText.trim() || sending} className="btn-primary flex items-center gap-2 disabled:opacity-50">
                  <Send size={16} /> {sending ? 'Mengirim...' : 'Kirim Balasan'}
                </button>
              </form>
            </div>
          )}
        </div>
      </div>
    </div>
  );
}
