import { useState } from 'react';
import { User, Home } from 'lucide-react';
import { API_BASE } from '../context/AppContext';

function Breadcrumb({ onHome }) {
  return (
    <nav className="flex items-center gap-2 text-xs text-muted mb-4">
      <button onClick={onHome} className="text-dpbj-gold hover:underline flex items-center gap-1">
        <Home size={11} /> Home
      </button>
      <span>/</span>
      <span className="text-dpbj-navy font-medium">Kontak Kami</span>
    </nav>
  );
}

// Simple text CAPTCHA simulator
function CaptchaWidget({ onVerify }) {
  const [chars] = useState(() => {
    const pool = 'ABCDEFGHJKLMNPQRSTUVWXYZabcdefghjkmnpqrstuvwxyz23456789';
    return Array.from({ length: 4 }, () => pool[Math.floor(Math.random() * pool.length)]).join('');
  });
  const [input, setInput] = useState('');
  const [error, setError] = useState('');

  const check = () => {
    if (input.toLowerCase() === chars.toLowerCase()) {
      onVerify(true);
      setError('');
    } else {
      setError('Kode keamanan salah, coba lagi.');
      onVerify(false);
    }
  };

  return (
    <div className="space-y-2">
      <div className="flex items-center gap-4">
        <div className="bg-gray-100 border border-border rounded-lg px-4 py-2 select-none">
          <span className="font-bold text-2xl text-dpbj-navy-dark tracking-widest font-mono" style={{ letterSpacing: '0.2em' }}>
            {chars}
          </span>
        </div>
        <button
          type="button"
          onClick={() => window.location.reload()}
          className="text-muted hover:text-dpbj-gold transition-colors"
          title="Refresh CAPTCHA"
        >
          🔄
        </button>
      </div>
      <input
        value={input}
        onChange={e => { setInput(e.target.value); onVerify(false); }}
        onBlur={check}
        placeholder="Ketik kode security"
        className="form-input border-red-200 bg-red-50 focus:border-dpbj-gold focus:ring-dpbj-gold/20"
      />
      {error && <p className="text-xs text-red-500">{error}</p>}
    </div>
  );
}

export default function KontakKami({ onNavigateHome }) {
  const [form, setForm] = useState({ nama: '', email: '', telpon: '', subyek: '', pesan: '' });
  const [captchaOk, setCaptchaOk] = useState(false);
  const [submitted, setSubmitted] = useState(false);
  const [errors, setErrors] = useState({});
  const [sending, setSending] = useState(false);

  const validate = () => {
    const e = {};
    if (!form.nama.trim()) e.nama = 'Nama wajib diisi';
    if (!form.email.trim()) e.email = 'Email wajib diisi';
    if (!form.subyek.trim()) e.subyek = 'Subyek wajib diisi';
    if (!form.pesan.trim()) e.pesan = 'Pesan wajib diisi';
    if (!captchaOk) e.captcha = 'Kode keamanan belum terverifikasi';
    setErrors(e);
    return Object.keys(e).length === 0;
  };

  const handleSubmit = async (e) => {
    e.preventDefault();
    if (!validate()) return;
    setSending(true);
    try {
      const res = await fetch(`${API_BASE}/inbox`, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({
          sender_name: form.nama,
          sender_email: form.email,
          sender_phone: form.telpon,
          subject: form.subyek,
          content: form.pesan,
        }),
      });
      const json = await res.json();
      if (json.success) {
        setSubmitted(true);
      } else {
        alert('Gagal mengirim pesan: ' + json.message);
      }
    } catch {
      alert('Terjadi kesalahan saat menghubungi server. Coba lagi.');
    } finally {
      setSending(false);
    }
  };

  const handleReset = () => {
    setForm({ nama: '', email: '', telpon: '', subyek: '', pesan: '' });
    setCaptchaOk(false);
    setErrors({});
    setSubmitted(false);
  };

  return (
    <div className="animate-fade-in space-y-4">
      <Breadcrumb onHome={onNavigateHome} />

      <div className="bg-white rounded-xl border border-border shadow-card p-6 max-w-2xl">
        <div className="flex items-center gap-2 mb-6 pb-4 border-b border-border">
          <User size={18} className="text-dpbj-navy" />
          <h2 className="font-bold text-dpbj-navy text-base">Kontak <span className="font-light">kami</span></h2>
        </div>

        {submitted ? (
          <div className="py-10 text-center animate-pop-in">
            <div className="text-4xl mb-3">✅</div>
            <h3 className="font-bold text-dpbj-navy text-lg">Pesan Terkirim!</h3>
            <p className="text-sm text-muted mt-1">Terima kasih, pesan Anda telah kami terima. Kami akan merespons segera.</p>
            <button onClick={handleReset} className="btn-primary mt-4">Kirim Pesan Lagi</button>
          </div>
        ) : (
          <form onSubmit={handleSubmit} className="space-y-4">
            <div className="grid grid-cols-1 gap-4">
              <div>
                <label className="form-label">Nama <span className="text-red-500">*</span></label>
                <input
                  value={form.nama}
                  onChange={e => setForm(f => ({ ...f, nama: e.target.value }))}
                  className={`form-input ${errors.nama ? 'border-red-300 bg-red-50' : ''}`}
                />
                {errors.nama && <p className="text-xs text-red-500 mt-1">{errors.nama}</p>}
              </div>
              <div>
                <label className="form-label">Email <span className="text-red-500">*</span></label>
                <input
                  type="email"
                  value={form.email}
                  onChange={e => setForm(f => ({ ...f, email: e.target.value }))}
                  className={`form-input ${errors.email ? 'border-red-300 bg-red-50' : ''}`}
                />
                {errors.email && <p className="text-xs text-red-500 mt-1">{errors.email}</p>}
              </div>
              <div>
                <label className="form-label">Telpon/HP</label>
                <input
                  value={form.telpon}
                  onChange={e => setForm(f => ({ ...f, telpon: e.target.value }))}
                  className="form-input"
                  placeholder="+62..."
                />
              </div>
              <div>
                <label className="form-label">Subyek <span className="text-red-500">*</span></label>
                <input
                  value={form.subyek}
                  onChange={e => setForm(f => ({ ...f, subyek: e.target.value }))}
                  className={`form-input ${errors.subyek ? 'border-red-300 bg-red-50' : ''}`}
                />
                {errors.subyek && <p className="text-xs text-red-500 mt-1">{errors.subyek}</p>}
              </div>
              <div>
                <label className="form-label">Pesan <span className="text-red-500">*</span></label>
                <textarea
                  value={form.pesan}
                  onChange={e => setForm(f => ({ ...f, pesan: e.target.value }))}
                  rows={5}
                  className={`form-input resize-y ${errors.pesan ? 'border-red-300 bg-red-50' : ''}`}
                />
                {errors.pesan && <p className="text-xs text-red-500 mt-1">{errors.pesan}</p>}
              </div>

              {/* CAPTCHA */}
              <div>
                <CaptchaWidget onVerify={setCaptchaOk} />
                {errors.captcha && <p className="text-xs text-red-500 mt-1">{errors.captcha}</p>}
              </div>
            </div>

            <div className="flex items-center gap-3 pt-2 border-t border-border">
              <button type="submit" disabled={sending} className="px-5 py-2 bg-purple-600 hover:bg-purple-700 text-white text-sm font-semibold rounded-full flex items-center gap-2 transition-colors active:scale-95 disabled:opacity-50">
                ✓ {sending ? 'Mengirim...' : 'Kirim'}
              </button>
              <button type="button" onClick={handleReset} className="px-5 py-2 bg-red-500 hover:bg-red-600 text-white text-sm font-semibold rounded-full flex items-center gap-2 transition-colors active:scale-95">
                ↺ Reset
              </button>
            </div>
          </form>
        )}
      </div>
    </div>
  );
}
