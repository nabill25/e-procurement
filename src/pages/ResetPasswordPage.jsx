import { useState } from 'react';
import { Lock, Eye, EyeOff, CheckCircle2, XCircle, Home } from 'lucide-react';
import { API_BASE } from '../context/AppContext';

// Halaman publik dibuka lewat link di email "Lupa Password" (/reset-password/TOKEN).
// Padanan main/index/reset_password di sistem lama, tapi tokennya sungguhan divalidasi
// server (hash + kadaluarsa), bukan md5(id."IKUN") yang bisa ditebak ulang kapan saja.
export default function ResetPasswordPage({ token, onNavigateHome, onLoginClick }) {
  const [password, setPassword] = useState('');
  const [confirmPassword, setConfirmPassword] = useState('');
  const [showPassword, setShowPassword] = useState(false);
  const [isLoading, setIsLoading] = useState(false);
  const [result, setResult] = useState(null); // { success, message }

  const handleSubmit = async (e) => {
    e.preventDefault();
    if (password.length < 8) {
      setResult({ success: false, message: 'Password minimal 8 karakter.' });
      return;
    }
    if (password !== confirmPassword) {
      setResult({ success: false, message: 'Konfirmasi password tidak cocok.' });
      return;
    }
    setIsLoading(true);
    setResult(null);
    try {
      const res = await fetch(`${API_BASE}/auth/reset-password`, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ token, password }),
      });
      const json = await res.json();
      setResult(json);
    } catch {
      setResult({ success: false, message: 'Tidak bisa terhubung ke server. Silakan coba lagi.' });
    } finally {
      setIsLoading(false);
    }
  };

  return (
    <div className="min-h-screen flex items-center justify-center bg-gray-50 px-4">
      <div className="w-full max-w-sm bg-white rounded-2xl shadow-card p-7">
        <button onClick={onNavigateHome} className="flex items-center gap-1 text-xs text-dpbj-gold hover:underline mb-5">
          <Home size={12} /> Kembali ke Beranda
        </button>

        {result?.success ? (
          <div className="flex flex-col items-center text-center gap-3 py-4">
            <CheckCircle2 size={40} className="text-emerald-500" />
            <h1 className="text-base font-bold text-dpbj-navy">Password Berhasil Diubah</h1>
            <p className="text-sm text-muted">{result.message}</p>
            <button onClick={onLoginClick} className="mt-2 px-5 py-2 bg-dpbj-navy text-white text-sm font-semibold rounded-full hover:bg-dpbj-navy-dark transition-colors">
              Login Sekarang
            </button>
          </div>
        ) : (
          <>
            <h1 className="text-base font-bold text-dpbj-navy mb-1">Buat Password Baru</h1>
            <p className="text-sm text-muted mb-5">Masukkan password baru untuk akun Anda.</p>

            {result && !result.success && (
              <div className="flex items-start gap-2 p-3 bg-red-50 border border-red-200 rounded-lg mb-4">
                <XCircle size={14} className="text-red-500 flex-shrink-0 mt-0.5" />
                <p className="text-xs text-red-600">{result.message}</p>
              </div>
            )}

            <form onSubmit={handleSubmit} className="space-y-4">
              <div>
                <label className="block text-sm text-gray-600 mb-1">Password Baru:</label>
                <div className="relative">
                  <Lock size={14} className="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400" />
                  <input
                    type={showPassword ? 'text' : 'password'}
                    required
                    value={password}
                    onChange={e => setPassword(e.target.value)}
                    placeholder="Minimal 8 karakter"
                    autoComplete="new-password"
                    className="w-full pl-9 pr-9 py-2.5 text-sm border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-dpbj-gold/30 focus:border-dpbj-gold transition-all"
                  />
                  <button type="button" onClick={() => setShowPassword(s => !s)} className="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-dpbj-navy">
                    {showPassword ? <EyeOff size={13} /> : <Eye size={13} />}
                  </button>
                </div>
              </div>
              <div>
                <label className="block text-sm text-gray-600 mb-1">Konfirmasi Password:</label>
                <div className="relative">
                  <Lock size={14} className="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400" />
                  <input
                    type={showPassword ? 'text' : 'password'}
                    required
                    value={confirmPassword}
                    onChange={e => setConfirmPassword(e.target.value)}
                    placeholder="Ulangi password baru"
                    autoComplete="new-password"
                    className="w-full pl-9 pr-3 py-2.5 text-sm border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-dpbj-gold/30 focus:border-dpbj-gold transition-all"
                  />
                </div>
              </div>
              <button
                type="submit"
                disabled={isLoading}
                className="w-full flex items-center justify-center gap-2 px-5 py-2.5 bg-dpbj-navy text-white text-sm font-semibold rounded-full hover:bg-dpbj-navy-dark transition-colors disabled:opacity-50"
              >
                {isLoading ? <span className="w-3 h-3 border-2 border-white/30 border-t-white rounded-full animate-spin" /> : 'Simpan Password Baru'}
              </button>
            </form>
          </>
        )}
      </div>
    </div>
  );
}
