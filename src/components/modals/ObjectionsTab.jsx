import { useState, useEffect } from 'react';
import { getAuthHeaders, useApp, API_BASE, SERVER_BASE } from '../../context/AppContext';
import { Download, AlertCircle, CheckCircle2, MessageSquare } from 'lucide-react';
import { format } from 'date-fns';

export default function ObjectionsTab({ tenderId, tenderStatus, participants, user }) {
  const [objections, setObjections] = useState([]);
  const [loading, setLoading] = useState(true);
  const [objectionText, setObjectionText] = useState('');
  const [attachment, setAttachment] = useState(null);
  const [isSubmitting, setIsSubmitting] = useState(false);
  const [replyText, setReplyText] = useState({});

  const fetchObjections = async () => {
    try {
      const res = await fetch(`${API_BASE}/tenders/${tenderId}/objections`, { headers: getAuthHeaders() });
      const json = await res.json();
      if (json.success) setObjections(json.data);
    } catch (err) {
      console.error(err);
    } finally {
      setLoading(false);
    }
  };

  useEffect(() => {
    fetchObjections();
  }, [tenderId]);

  const participantInfo = participants.find(p => p.vendor_id === user.id);
  const isLosingVendor = user.role === 'vendor' && participantInfo && !participantInfo.is_winner;
  const isWinner = user.role === 'vendor' && participantInfo && participantInfo.is_winner;

  const handleSubmitObjection = async (e) => {
    e.preventDefault();
    if (!objectionText) return alert('Teks sanggahan wajib diisi.');
    try {
      setIsSubmitting(true);
      const formData = new FormData();
      formData.append('vendor_id', user.id);
      formData.append('objection_text', objectionText);
      if (attachment) formData.append('attachment', attachment);

      const res = await fetch(`${API_BASE}/tenders/${tenderId}/objections`, {
        method: 'POST',
        headers: (() => { const h = getAuthHeaders(); delete h['Content-Type']; return h; })(),
        body: formData
      });
      const json = await res.json();
      if (json.success) {
        alert('Sanggahan berhasil dikirim.');
        setObjectionText('');
        setAttachment(null);
        fetchObjections();
      } else {
        alert(json.message);
      }
    } catch (err) {
      alert(err.message);
    } finally {
      setIsSubmitting(false);
    }
  };

  const handleReplyObjection = async (objId) => {
    const text = replyText[objId];
    if (!text) return alert('Balasan wajib diisi.');
    try {
      setIsSubmitting(true);
      const formData = new FormData();
      formData.append('response_text', text);

      const res = await fetch(`${API_BASE}/tenders/${tenderId}/objections/${objId}/reply`, {
        method: 'POST',
        headers: (() => { const h = getAuthHeaders(); delete h['Content-Type']; return h; })(),
        body: formData
      });
      const json = await res.json();
      if (json.success) {
        alert('Balasan terkirim.');
        setReplyText(prev => ({ ...prev, [objId]: '' }));
        fetchObjections();
      } else {
        alert(json.message);
      }
    } catch (err) {
      alert(err.message);
    } finally {
      setIsSubmitting(false);
    }
  };

  if (loading) return <div className="p-4 text-center text-sm">Loading...</div>;

  return (
    <div className="space-y-6">
      <div className="bg-amber-50 border border-amber-200 p-4 rounded-xl">
        <h3 className="font-bold text-amber-800 flex items-center gap-2"><AlertCircle size={18}/> Masa Sanggah</h3>
        <p className="text-xs text-amber-700 mt-1">Masa sanggah memberikan kesempatan bagi peserta yang tidak menang untuk mengajukan keberatan secara tertulis beserta bukti. Pokja wajib memberikan jawaban atas sanggahan tersebut.</p>
      </div>

      {user.role === 'vendor' && isLosingVendor && tenderStatus === 'masa_sanggah' && (
        <form onSubmit={handleSubmitObjection} className="bg-surface border border-border p-5 rounded-xl space-y-4">
          <h4 className="font-bold text-sm text-dpbj-navy">Ajukan Sanggahan</h4>
          <div>
            <label className="block text-xs font-semibold text-dpbj-navy mb-1">Alasan Sanggah</label>
            <textarea className="form-input w-full text-sm h-24" placeholder="Jelaskan alasan keberatan Anda..." required value={objectionText} onChange={e => setObjectionText(e.target.value)}></textarea>
          </div>
          <div>
            <label className="block text-xs font-semibold text-dpbj-navy mb-1">File Bukti (Opsional)</label>
            <input type="file" className="form-input w-full text-sm" onChange={e => setAttachment(e.target.files[0])} accept=".pdf,.zip,.rar" />
          </div>
          <button type="submit" disabled={isSubmitting} className="btn-primary w-full justify-center">Kirim Sanggahan</button>
        </form>
      )}

      {user.role === 'vendor' && isWinner && (
        <div className="p-4 text-center bg-gray-50 border border-border rounded-xl">
          <p className="text-sm font-medium text-gray-500">Anda adalah pemenang tender ini. Anda tidak dapat mengajukan sanggahan.</p>
        </div>
      )}

      <div className="space-y-4">
        <h4 className="font-bold text-sm text-dpbj-navy">Daftar Sanggahan Masuk</h4>
        {objections.length === 0 ? (
          <p className="text-xs text-muted">Belum ada sanggahan.</p>
        ) : (
          objections.map(obj => {
            // Vendor hanya boleh lihat sanggahannya sendiri. Pokja bisa lihat semua.
            if (user.role === 'vendor' && obj.vendor_id !== user.id) return null;

            return (
              <div key={obj.id} className="border border-border rounded-xl p-4 bg-white shadow-sm space-y-3">
                <div className="flex justify-between items-start border-b border-border pb-3">
                  <div>
                    <p className="text-xs font-bold text-dpbj-navy">{obj.company_name}</p>
                    <p className="text-[10px] text-muted">{format(new Date(obj.created_at), 'dd MMM yyyy HH:mm')}</p>
                  </div>
                  <span className={`text-[10px] font-bold px-2 py-1 rounded-full ${obj.status === 'responded' ? 'bg-emerald-100 text-emerald-700' : 'bg-amber-100 text-amber-700'}`}>
                    {obj.status === 'responded' ? 'Dijawab' : 'Menunggu Jawaban'}
                  </span>
                </div>
                
                <div>
                  <p className="text-xs font-semibold text-gray-600 mb-1">Alasan Sanggah:</p>
                  <p className="text-sm text-dpbj-navy bg-gray-50 p-3 rounded-lg border border-gray-100">{obj.objection_text}</p>
                  {obj.attachment_path && (
                    <a href={`${SERVER_BASE}${obj.attachment_path}`} target="_blank" rel="noreferrer" className="inline-flex items-center gap-1 text-[10px] text-blue-600 hover:underline mt-2">
                      <Download size={12}/> Unduh Lampiran Bukti
                    </a>
                  )}
                </div>

                {obj.status === 'responded' ? (
                  <div className="bg-emerald-50 p-3 rounded-lg border border-emerald-100 mt-2">
                    <p className="text-xs font-bold text-emerald-800 mb-1 flex items-center gap-1"><CheckCircle2 size={14}/> Jawaban Pokja:</p>
                    <p className="text-sm text-emerald-900">{obj.response_text}</p>
                  </div>
                ) : user.role === 'pokja' ? (
                  <div className="bg-surface p-3 rounded-lg border border-border mt-2 space-y-2">
                    <p className="text-xs font-bold text-dpbj-navy mb-1 flex items-center gap-1"><MessageSquare size={14}/> Beri Jawaban:</p>
                    <textarea className="form-input w-full text-sm h-20" placeholder="Jawaban dari Pokja..." value={replyText[obj.id] || ''} onChange={e => setReplyText({ ...replyText, [obj.id]: e.target.value })}></textarea>
                    <button disabled={isSubmitting} onClick={() => handleReplyObjection(obj.id)} className="btn-primary text-xs py-1.5 px-3">Kirim Jawaban</button>
                  </div>
                ) : null}
              </div>
            );
          })
        )}
      </div>
    </div>
  );
}
