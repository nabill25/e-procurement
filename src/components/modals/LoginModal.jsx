import { useState, useEffect } from 'react';
import { createPortal } from 'react-dom';
import { X, Eye, EyeOff, User, Lock, Shield, AlertCircle } from 'lucide-react';
import { useApp } from '../../context/AppContext';

function CaptchaWidget({ onVerify }) {
  const [chars] = useState(() => {
    const pool = 'ABCDEFGHJKLMNPQRSTUVWXYZabcdefghjkmnpqrstuvwxyz23456789';
    return Array.from({ length: 4 }, () => pool[Math.floor(Math.random() * pool.length)]).join('') + 'sp';
  });
  const [input, setInput] = useState('');
  return (
    <div className="flex items-center gap-3">
      <div className="bg-white border border-gray-200 rounded px-3 py-2 select-none min-w-[90px]">
        <span className="font-black text-2xl text-dpbj-navy tracking-widest font-mono">{chars}</span>
      </div>
      <button type="button" className="text-gray-400 hover:text-dpbj-navy transition-colors text-lg" title="Refresh">↺</button>
      <div className="flex-1 relative">
        <input
          value={input}
          onChange={e => { setInput(e.target.value); onVerify(e.target.value.toLowerCase() === chars.toLowerCase()); }}
          placeholder="ketik kode"
          className="w-full px-3 py-2 pr-8 text-sm border border-red-200 bg-red-50 rounded-lg focus:outline-none focus:ring-2 focus:ring-dpbj-gold/30 focus:border-dpbj-gold transition-all"
        />
        <span className="absolute right-2 top-1/2 -translate-y-1/2 text-red-400 text-xs">ⓘ</span>
      </div>
    </div>
  );
}

export default function LoginModal({ isOpen, onClose, onNavigateRegister }) {
  // ── Gunakan fungsi login dari AppContext (mengikuti alur eProc Auth::login()) ──
  const { login, setActivePage } = useApp();

  const [form, setForm] = useState({ username: '', password: '' });
  const [showPassword, setShowPassword] = useState(false);
  const [captchaOk, setCaptchaOk] = useState(false);
  const [isLoading, setIsLoading] = useState(false);
  const [errors, setErrors] = useState({});
  const [serverError, setServerError] = useState(''); // Error dari backend

  useEffect(() => {
    if (isOpen) {
      document.body.style.overflow = 'hidden';
      // Reset form saat modal dibuka
      setForm({ username: '', password: '' });
      setErrors({});
      setServerError('');
      setCaptchaOk(false);
    } else {
      document.body.style.overflow = '';
    }
    return () => { document.body.style.overflow = ''; };
  }, [isOpen]);

  if (!isOpen) return null;

  const handleLogin = async (e) => {
    e.preventDefault();
    const errs = {};
    if (!form.username.trim()) errs.username = 'Wajib diisi';
    if (!form.password.trim()) errs.password = 'Wajib diisi';
    if (!captchaOk) errs.captcha = 'Kode keamanan salah';
    setErrors(errs);
    if (Object.keys(errs).length > 0) return;

    setIsLoading(true);
    setServerError('');

    try {
      // ── Panggil login nyata dari AppContext → /api/auth/login ──
      const result = await login(form.username.trim(), form.password);

      if (result.success) {
        onClose();
        // navigasi sudah dihandle di AppContext (setActivePage('dashboard'))
      } else {
        // Tampilkan error dari server (username/password salah, akun tidak aktif, dll)
        setServerError(result.message || 'Login gagal. Periksa kembali kredensial Anda.');
      }
    } catch (err) {
      setServerError('Terjadi kesalahan tidak terduga. Silakan coba lagi.');
    } finally {
      setIsLoading(false);
    }
  };

  const handleRegister = () => {
    onClose();
    if (onNavigateRegister) onNavigateRegister('registrasi');
    else setActivePage('registrasi');
  };

  return createPortal(
    <div className="fixed inset-0 bg-black/40 backdrop-blur-sm z-[200] flex items-center justify-center p-4 animate-fade-in">
      <div className="bg-white rounded-xl shadow-2xl w-full max-w-[400px] flex flex-col animate-pop-in relative overflow-hidden">
        {/* Header */}
        <div className="flex items-center justify-between px-6 pt-5 pb-4 border-b border-gray-100">
          <h2 className="text-base font-semibold text-dpbj-navy">Login</h2>
          <button
            onClick={onClose}
            className="w-6 h-6 flex items-center justify-center text-gray-400 hover:text-gray-700 hover:bg-gray-100 rounded transition-colors"
          >
            <X size={14} />
          </button>
        </div>

        {/* Form */}
        <form onSubmit={handleLogin} className="px-6 py-4 space-y-4">

          {/* Error dari server */}
          {serverError && (
            <div className="flex items-start gap-2 p-3 bg-red-50 border border-red-200 rounded-lg">
              <AlertCircle size={14} className="text-red-500 flex-shrink-0 mt-0.5" />
              <p className="text-xs text-red-600">{serverError}</p>
            </div>
          )}

          {/* Username */}
          <div>
            <label className="block text-sm text-gray-600 mb-1">Username / Email:</label>
            <div className="relative">
              <User size={14} className="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400" />
              <input
                type="text"
                value={form.username}
                onChange={e => { setForm(f => ({ ...f, username: e.target.value })); setServerError(''); }}
                placeholder="Username"
                autoComplete="username"
                className={`w-full pl-9 pr-8 py-2.5 text-sm border rounded-lg focus:outline-none focus:ring-2 focus:ring-dpbj-gold/30 focus:border-dpbj-gold transition-all
                  ${errors.username ? 'border-red-400 bg-red-50' : 'border-red-200 bg-red-50'}`}
              />
              <span className="absolute right-2.5 top-1/2 -translate-y-1/2 text-red-400 text-xs">ⓘ</span>
            </div>
            {errors.username && <p className="text-xs text-red-500 mt-1">{errors.username}</p>}
          </div>

          {/* Password */}
          <div>
            <label className="block text-sm text-gray-600 mb-1 flex items-center gap-2">
              Password:
              <button type="button" onClick={() => setShowPassword(s => !s)} className="text-gray-400 hover:text-dpbj-navy">
                {showPassword ? <EyeOff size={13} /> : <Eye size={13} />}
              </button>
            </label>
            <div className="relative">
              <Lock size={14} className="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400" />
              <input
                type={showPassword ? 'text' : 'password'}
                value={form.password}
                onChange={e => { setForm(f => ({ ...f, password: e.target.value })); setServerError(''); }}
                placeholder="Password"
                autoComplete="current-password"
                className={`w-full pl-9 pr-8 py-2.5 text-sm border rounded-lg focus:outline-none focus:ring-2 focus:ring-dpbj-gold/30 focus:border-dpbj-gold transition-all
                  ${errors.password ? 'border-red-400 bg-red-50' : 'border-red-200 bg-red-50'}`}
              />
              <span className="absolute right-2.5 top-1/2 -translate-y-1/2 text-red-400 text-xs">ⓘ</span>
            </div>
            {errors.password && <p className="text-xs text-red-500 mt-1">{errors.password}</p>}
          </div>

          {/* Lupa Password */}
          <div className="text-left -mt-2">
            <button type="button" className="text-xs text-dpbj-gold hover:text-dpbj-gold-dark underline transition-colors">
              Lupa Password ?
            </button>
          </div>

          {/* CAPTCHA */}
          <div>
            <CaptchaWidget onVerify={setCaptchaOk} />
            {errors.captcha && <p className="text-xs text-red-500 mt-1">{errors.captcha}</p>}
          </div>

          {/* Panduan Akun */}
          <div className="bg-blue-50 border border-blue-100 rounded-lg px-3 py-2 text-[10px] text-blue-600">
            <strong>Gunakan email dan password riil yang didaftarkan.</strong>
          </div>

          {/* Action buttons */}
          <div className="flex items-center justify-between pt-1">
            <button
              type="submit"
              disabled={isLoading}
              className="flex items-center gap-2 px-5 py-2 bg-dpbj-navy border border-dpbj-navy rounded-full text-sm text-white hover:bg-dpbj-navy-dark transition-colors disabled:opacity-50 active:scale-95 shadow-sm"
            >
              {isLoading ? <span className="w-3 h-3 border-2 border-white/30 border-t-white rounded-full animate-spin" /> : '➜'}
              Login
            </button>
            <button
              type="button"
              onClick={handleRegister}
              className="flex items-center gap-2 px-5 py-2 border border-gray-300 rounded-full text-sm text-dpbj-navy hover:bg-gray-50 transition-colors active:scale-95"
            >
              👤 Registrasi Penyedia
            </button>
          </div>


        </form>
      </div>
    </div>,
    document.body
  );
}
