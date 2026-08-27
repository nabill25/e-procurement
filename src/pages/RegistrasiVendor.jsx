import { useState } from 'react';
import { User, Home, Eye, EyeOff } from 'lucide-react';
import VendorPolicyModal from '../components/modals/VendorPolicyModal';
import { API_BASE } from '../context/AppContext';

function Breadcrumb({ onHome }) {
  return (
    <nav className="flex items-center gap-2 text-xs text-muted mb-6">
      <button onClick={onHome} className="text-dpbj-gold hover:underline flex items-center gap-1">
        <Home size={11} /> Home
      </button>
      <span>/</span>
      <span className="text-dpbj-navy font-medium">Registrasi</span>
    </nav>
  );
}

function CaptchaWidget({ onVerify }) {
  const [chars] = useState(() => {
    const pool = 'ABCDEFGHJKLMNPQRSTUVWXYZabcdefghjkmnpqrstuvwxyz23456789';
    return Array.from({ length: 4 }, () => pool[Math.floor(Math.random() * pool.length)]).join('');
  });
  const [input, setInput] = useState('');
  
  const check = (val) => {
    onVerify(val.toLowerCase() === chars.toLowerCase());
  };

  return (
    <div className="flex flex-col sm:flex-row items-center gap-4">
      <div className="bg-gradient-to-br from-gray-100 to-gray-200 border border-gray-300 rounded-xl px-6 py-3 select-none shadow-inner flex items-center justify-center min-w-[140px]">
        <span className="font-bold text-3xl text-dpbj-navy-dark tracking-[0.2em] font-mono blur-[1px] opacity-80 mix-blend-multiply">{chars}</span>
      </div>
      <div className="flex-1 w-full relative">
        <input
          value={input}
          onChange={e => { setInput(e.target.value); check(e.target.value); }}
          placeholder="Ketik kode security di atas..."
          className="form-input w-full pr-10"
        />
        <button type="button" className="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-dpbj-gold transition-colors" title="Refresh">
          🔄
        </button>
      </div>
    </div>
  );
}

export default function RegistrasiVendor({ onNavigateHome, onLoginClick }) {
  const [form, setForm] = useState({
    bentukUsaha: '',
    namaPerusahaan: '',
    npwp: '',
    email: '',
    username: '',
    password: '',
    is16Digit: false,
  });
  const [showPassword, setShowPassword] = useState(false);
  const [agreed, setAgreed] = useState(false);
  const [captchaOk, setCaptchaOk] = useState(false);
  const [submitted, setSubmitted] = useState(false);
  const [loading, setLoading] = useState(false);
  const [errors, setErrors] = useState({});
  const [apiError, setApiError] = useState(null);
  const [showPolicy, setShowPolicy] = useState(false);

  const bentukUsahaOptions = ['PT (Perseroan Terbatas)', 'CV (Commanditaire Vennootschap)', 'Firma', 'UD (Usaha Dagang)', 'Koperasi', 'Yayasan', 'Badan Usaha Lainnya'];

  const validate = () => {
    const e = {};
    if (!form.bentukUsaha) e.bentukUsaha = 'Wajib dipilih';
    if (!form.namaPerusahaan.trim()) e.namaPerusahaan = 'Wajib diisi';
    if (!form.npwp.trim()) {
      e.npwp = 'Wajib diisi';
    } else {
      const digitsOnly = form.npwp.replace(/\D/g, '');
      if (form.is16Digit ? digitsOnly.length !== 16 : digitsOnly.length !== 15) {
        e.npwp = form.is16Digit ? 'NPWP 16 digit harus berupa 16 angka' : 'NPWP harus berupa 15 angka (format: XX.XXX.XXX.X-XXX.XXX)';
      }
    }
    if (!form.email.trim()) e.email = 'Wajib diisi';
    if (!form.username.trim()) e.username = 'Wajib diisi';
    if (!form.password.trim()) e.password = 'Wajib diisi';
    if (!agreed) e.agreed = 'Anda harus menyetujui pernyataan ini';
    if (!captchaOk) e.captcha = 'Kode keamanan belum terverifikasi';
    setErrors(e);
    return Object.keys(e).length === 0;
  };

  const handleSubmit = async (e) => {
    e.preventDefault();
    if (!validate()) return;
    
    setLoading(true);
    setApiError(null);

    try {
      const payload = {
        company_type: form.bentukUsaha,
        company_name: form.namaPerusahaan,
        npwp: form.npwp,
        email: form.email,
        username: form.username,
        password: form.password
      };

      const res = await fetch(`${API_BASE}/auth/register`, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(payload)
      });
      const data = await res.json();
      
      if (data.success) {
        setSubmitted(true);
      } else {
        setApiError(data.message);
      }
    } catch (err) {
      setApiError('Terjadi kesalahan saat menghubungi server.');
    } finally {
      setLoading(false);
    }
  };

  if (submitted) {
    return (
      <div className="animate-fade-in space-y-4">
        <Breadcrumb onHome={onNavigateHome} />
        <div className="bg-white rounded-3xl border border-gray-100 shadow-sm p-12 text-center animate-pop-in max-w-2xl mx-auto">
          <div className="text-6xl mb-6">🎉</div>
          <h2 className="font-bold text-dpbj-navy text-2xl mb-3">Registrasi Berhasil Dikirim!</h2>
          <p className="text-base text-gray-500 max-w-md mx-auto leading-relaxed">
            Akun Anda sedang dalam proses verifikasi oleh tim DPBJ UI. Sistem akan mengirimkan email aktivasi ke <strong className="text-dpbj-navy">{form.email}</strong>.
            Setelah aktivasi berhasil, silakan login dan lengkapi data identitas perusahaan Anda.
          </p>
          <button onClick={onNavigateHome} className="btn-primary px-8 py-3 mt-8">Kembali ke Beranda</button>
        </div>
      </div>
    );
  }

  return (
    <>
      <div className="animate-fade-in">
        <Breadcrumb onHome={onNavigateHome} />

        <div className="flex flex-col lg:flex-row gap-8 items-start">
          {/* Form Panel (Left) */}
          <div className="flex-1 bg-white rounded-3xl border border-gray-100 shadow-sm p-8 md:p-10">
            <div className="mb-8 border-b border-gray-100 pb-6">
              <h1 className="font-bold text-2xl text-dpbj-navy tracking-tight mb-2">Registrasi Vendor / Penyedia</h1>
              <p className="text-sm text-gray-500">Lengkapi formulir di bawah ini untuk mendaftarkan perusahaan Anda ke dalam Sistem Pengadaan Barang Jasa DPBJ Universitas Indonesia.</p>
            </div>

            {apiError && (
              <div className="mb-6 p-4 bg-red-50 border border-red-200 text-red-600 text-sm font-semibold rounded-xl flex items-center gap-2 animate-fade-in">
                <div className="w-1.5 h-1.5 rounded-full bg-red-600 shrink-0" />
                {apiError}
              </div>
            )}

            <form onSubmit={handleSubmit} className="space-y-6">
              <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
                {/* Bentuk Usaha */}
                <div>
                  <label className="form-label">Bentuk Usaha <span className="text-red-500">*</span></label>
                  <select
                    value={form.bentukUsaha}
                    onChange={e => setForm(f => ({ ...f, bentukUsaha: e.target.value }))}
                    className={`form-select ${errors.bentukUsaha ? 'border-red-300 bg-red-50' : ''}`}
                  >
                    <option value="">-- Pilih Bentuk Usaha --</option>
                    {bentukUsahaOptions.map(o => <option key={o} value={o}>{o}</option>)}
                  </select>
                  {errors.bentukUsaha && <p className="text-xs text-red-500 mt-1.5">{errors.bentukUsaha}</p>}
                </div>

                {/* Nama Perusahaan */}
                <div>
                  <label className="form-label">Nama Perusahaan <span className="text-red-500">*</span></label>
                  <input
                    value={form.namaPerusahaan}
                    onChange={e => setForm(f => ({ ...f, namaPerusahaan: e.target.value }))}
                    className={`form-input ${errors.namaPerusahaan ? 'border-red-300 bg-red-50' : ''}`}
                    placeholder="Contoh: PT Sukses Selalu"
                  />
                  {errors.namaPerusahaan && <p className="text-xs text-red-500 mt-1.5">{errors.namaPerusahaan}</p>}
                </div>

                {/* NPWP */}
                <div>
                  <label className="form-label">
                    NPWP <span className="text-dpbj-gold text-[10px] font-bold tracking-wider ml-1">PERUSAHAAN</span>
                    <span className="text-red-500"> *</span>
                  </label>
                  <input
                    value={form.npwp}
                    onChange={e => setForm(f => ({ ...f, npwp: e.target.value }))}
                    placeholder={form.is16Digit ? '16 digit NPWP...' : 'XX.XXX.XXX.X-XXX.XXX'}
                    className={`form-input ${errors.npwp ? 'border-red-300 bg-red-50' : ''}`}
                  />
                  <label className="flex items-center gap-2 mt-2 text-xs text-gray-500 cursor-pointer hover:text-dpbj-navy transition-colors">
                    <input
                      type="checkbox"
                      checked={form.is16Digit}
                      onChange={e => setForm(f => ({ ...f, is16Digit: e.target.checked }))}
                      className="rounded border-gray-300 text-dpbj-gold focus:ring-dpbj-gold"
                    />
                    Format 16 Digit
                  </label>
                  {errors.npwp && <p className="text-xs text-red-500 mt-1.5">{errors.npwp}</p>}
                </div>

                {/* Email */}
                <div>
                  <label className="form-label">
                    Email <span className="text-dpbj-gold text-[10px] font-bold tracking-wider ml-1">OFFICIAL PERUSAHAAN</span>
                    <span className="text-red-500"> *</span>
                  </label>
                  <input
                    type="email"
                    value={form.email}
                    onChange={e => setForm(f => ({ ...f, email: e.target.value }))}
                    className={`form-input ${errors.email ? 'border-red-300 bg-red-50' : ''}`}
                    placeholder="email@perusahaan.com"
                  />
                  {errors.email && <p className="text-xs text-red-500 mt-1.5">{errors.email}</p>}
                </div>

                {/* Username */}
                <div>
                  <label className="form-label">Username <span className="text-red-500">*</span></label>
                  <input
                    value={form.username}
                    onChange={e => setForm(f => ({ ...f, username: e.target.value }))}
                    className={`form-input ${errors.username ? 'border-red-300 bg-red-50' : ''}`}
                    placeholder="Pilih username unik"
                  />
                  {errors.username && <p className="text-xs text-red-500 mt-1.5">{errors.username}</p>}
                </div>

                {/* Password */}
                <div>
                  <label className="form-label">Password <span className="text-red-500">*</span></label>
                  <div className="relative">
                    <input
                      type={showPassword ? 'text' : 'password'}
                      value={form.password}
                      onChange={e => setForm(f => ({ ...f, password: e.target.value }))}
                      className={`form-input pr-11 ${errors.password ? 'border-red-300 bg-red-50' : ''}`}
                      placeholder="••••••••"
                    />
                    <button
                      type="button"
                      onClick={() => setShowPassword(s => !s)}
                      className="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-dpbj-navy p-1"
                    >
                      {showPassword ? <EyeOff size={16} /> : <Eye size={16} />}
                    </button>
                  </div>
                  {errors.password && <p className="text-xs text-red-500 mt-1.5">{errors.password}</p>}
                </div>
              </div>

              {/* CAPTCHA */}
              <div className="pt-2">
                <label className="form-label">Verifikasi Keamanan <span className="text-red-500">*</span></label>
                <CaptchaWidget onVerify={setCaptchaOk} />
                {errors.captcha && <p className="text-xs text-red-500 mt-1.5">{errors.captcha}</p>}
              </div>

              {/* Agreement */}
              <div className="border-t border-gray-100 pt-6 mt-4">
                <label className="flex items-start gap-3 cursor-pointer group">
                  <input
                    type="checkbox"
                    checked={agreed}
                    onChange={e => setAgreed(e.target.checked)}
                    className="mt-1 rounded border-gray-300 text-dpbj-gold focus:ring-dpbj-gold w-4 h-4 cursor-pointer"
                  />
                  <span className="text-sm text-gray-500 leading-relaxed group-hover:text-gray-700 transition-colors">
                    Dengan ini saya menyatakan bahwa data-data tersebut adalah data yang benar dan dapat dipertanggungjawabkan.
                    {' '}
                    <button type="button" onClick={(e) => { e.preventDefault(); setShowPolicy(true); }} className="text-blue-500 underline hover:text-blue-700 font-medium">Kebijakan Penyedia/Vendor</button>
                  </span>
                </label>
                {errors.agreed && <p className="text-xs text-red-500 mt-1.5">{errors.agreed}</p>}
              </div>

              {/* Submit Action */}
              <div className="pt-4 border-t border-gray-100 flex flex-col sm:flex-row items-center gap-4 justify-between">
                <p className="text-xs text-gray-500">
                  Sudah punya akun? <button type="button" onClick={onLoginClick} className="text-dpbj-gold hover:underline font-bold">Login di sini</button>
                </p>
                <button type="submit" disabled={loading} className="btn-primary w-full sm:w-auto px-8 py-3 shadow-lg disabled:opacity-70">
                  {loading ? 'Memproses...' : 'Kirim Pendaftaran'}
                </button>
              </div>
            </form>
          </div>

          {/* Explanation Panel (Right) */}
          <div className="w-full lg:w-[320px] flex-shrink-0 bg-gradient-to-b from-blue-50 to-white rounded-3xl border border-blue-100 shadow-sm p-6 sticky top-24">
            <div className="flex items-center gap-3 mb-5 pb-4 border-b border-blue-200/50">
              <div className="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center text-blue-600">
                <User size={20} />
              </div>
              <h3 className="font-bold text-dpbj-navy">Panduan Registrasi</h3>
            </div>
            <ul className="space-y-4 text-xs text-gray-600 leading-relaxed">
              {[
                'Silahkan isi form Registrasi dengan lengkap dan sesuai dengan data perusahaan.',
                'Kolom yang wajib diisi ditandai dengan bintang merah (*).',
                'Pastikan email dan NPWP yang dimasukkan valid.',
                'Centang persetujuan kebijakan penyedia setelah membaca ketentuannya.',
                'Klik tombol Daftar Sekarang yang akan muncul di akhir form.',
                'Sistem akan mengirim email untuk aktivasi akun Anda.',
                'Setelah aktivasi berhasil, silahkan login dan lengkapi data identitas.'
              ].map((step, i) => (
                <li key={i} className="flex items-start gap-3">
                  <span className="flex-shrink-0 w-5 h-5 rounded-full bg-white border border-blue-200 flex items-center justify-center text-blue-600 font-bold text-[10px]">
                    {i + 1}
                  </span>
                  <span className="mt-0.5">{step}</span>
                </li>
              ))}
            </ul>
          </div>
        </div>
      </div>

      {/* Vendor Policy Modal */}
      <VendorPolicyModal isOpen={showPolicy} onClose={() => setShowPolicy(false)} />
    </>
  );
}
