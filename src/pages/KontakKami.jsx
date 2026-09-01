import { useState, useEffect } from 'react';
import { User, Home, Send, RotateCcw } from 'lucide-react';
import { motion, AnimatePresence } from 'framer-motion';
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
const generateCaptcha = () => {
  const pool = 'ABCDEFGHJKLMNPQRSTUVWXYZabcdefghjkmnpqrstuvwxyz23456789';
  return Array.from({ length: 4 }, () => pool[Math.floor(Math.random() * pool.length)]).join('');
};

function CaptchaWidget({ onVerify }) {
  const [chars, setChars] = useState(generateCaptcha);
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

  // Ganti ke kode baru tanpa reload halaman penuh (versi lama pakai window.location.reload()
  // yang membuang seluruh isian form lain hanya untuk minta kode CAPTCHA baru).
  const refresh = () => {
    setChars(generateCaptcha());
    setInput('');
    setError('');
    onVerify(false);
  };

  return (
    <div className="space-y-2">
      <div className="flex items-center gap-4">
        <div className="bg-gray-100 border border-border rounded-lg px-4 py-2 select-none">
          <span className="font-bold text-2xl text-dpbj-navy-dark tracking-widest font-mono" style={{ letterSpacing: '0.2em' }}>
            {chars}
          </span>
        </div>
        <motion.button
          type="button"
          onClick={refresh}
          whileTap={{ rotate: 180 }}
          transition={{ duration: 0.3 }}
          className="text-muted hover:text-dpbj-gold transition-colors"
          title="Ganti kode CAPTCHA"
        >
          🔄
        </motion.button>
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
  const [isComplain, setIsComplain] = useState(false);
  const [complainTypes, setComplainTypes] = useState([]);
  const [complainTypeId, setComplainTypeId] = useState('');

  useEffect(() => {
    fetch(`${API_BASE}/inbox/meta/complain-types`)
      .then(res => res.json())
      .then(json => { if (json.success) setComplainTypes(json.data); })
      .catch(() => {});
  }, []);

  const validate = () => {
    const e = {};
    if (!form.nama.trim()) e.nama = 'Nama wajib diisi';
    if (!form.email.trim()) e.email = 'Email wajib diisi';
    if (isComplain) {
      if (!complainTypeId) e.subyek = 'Subjek komplain wajib dipilih';
    } else if (!form.subyek.trim()) {
      e.subyek = 'Subyek wajib diisi';
    }
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
      const subjectValue = isComplain
        ? (complainTypes.find(c => c.id === complainTypeId)?.name || 'Komplain')
        : form.subyek;
      const res = await fetch(`${API_BASE}/inbox`, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({
          sender_name: form.nama,
          sender_email: form.email,
          sender_phone: form.telpon,
          subject: subjectValue,
          content: form.pesan,
          complain_type_id: isComplain ? complainTypeId : undefined,
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
    setIsComplain(false);
    setComplainTypeId('');
  };

  return (
    <div className="animate-fade-in space-y-4">
      <Breadcrumb onHome={onNavigateHome} />

      <div className="section-card max-w-2xl">
        <div className="flex items-center gap-3 mb-6 pb-4 border-b border-border">
          <div className="w-10 h-10 rounded-xl bg-dpbj-gold-faint flex items-center justify-center shrink-0">
            <User size={18} className="text-dpbj-navy" />
          </div>
          <h2 className="font-bold text-dpbj-navy text-base">Kontak <span className="font-light">kami</span></h2>
        </div>

        {submitted ? (
          <motion.div
            initial={{ opacity: 0, scale: 0.95 }}
            animate={{ opacity: 1, scale: 1 }}
            transition={{ type: 'spring', stiffness: 300, damping: 22 }}
            className="py-10 text-center"
          >
            <motion.div
              className="text-4xl mb-3"
              initial={{ scale: 0 }}
              animate={{ scale: 1 }}
              transition={{ delay: 0.15, type: 'spring', stiffness: 400, damping: 14 }}
            >
              ✅
            </motion.div>
            <h3 className="font-bold text-dpbj-navy text-lg">Pesan Terkirim!</h3>
            <p className="text-sm text-muted mt-1">Terima kasih, pesan Anda telah kami terima. Kami akan merespons segera.</p>
            <button onClick={handleReset} className="btn-primary mt-4">Kirim Pesan Lagi</button>
          </motion.div>
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
                <label className="flex items-center gap-2 text-xs text-muted mb-2 cursor-pointer">
                  <input type="checkbox" checked={isComplain} onChange={e => setIsComplain(e.target.checked)} />
                  Ini adalah komplain/pengaduan resmi
                </label>

                {isComplain ? (
                  <>
                    <label className="form-label">Subjek Komplain <span className="text-red-500">*</span></label>
                    <select
                      value={complainTypeId}
                      onChange={e => setComplainTypeId(e.target.value)}
                      className={`form-input ${errors.subyek ? 'border-red-300 bg-red-50' : ''}`}
                    >
                      <option value="">Pilih subjek komplain...</option>
                      {complainTypes.map(c => (
                        <option key={c.id} value={c.id}>{c.name}</option>
                      ))}
                    </select>
                  </>
                ) : (
                  <>
                    <label className="form-label">Subyek <span className="text-red-500">*</span></label>
                    <input
                      value={form.subyek}
                      onChange={e => setForm(f => ({ ...f, subyek: e.target.value }))}
                      className={`form-input ${errors.subyek ? 'border-red-300 bg-red-50' : ''}`}
                    />
                  </>
                )}
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
              <button type="submit" disabled={sending} className="btn-primary disabled:opacity-50">
                <Send size={14} /> {sending ? 'Mengirim...' : 'Kirim'}
              </button>
              <button type="button" onClick={handleReset} className="btn-ghost">
                <RotateCcw size={14} /> Reset
              </button>
            </div>
          </form>
        )}
      </div>
    </div>
  );
}
