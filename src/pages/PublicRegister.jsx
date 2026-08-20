import { useState } from 'react';
import { useNavigate, Link } from 'react-router-dom';
import { Building2, Mail, Lock, FileText, ArrowRight, UserPlus } from 'lucide-react';
import { API_BASE } from '../context/AppContext';
import logoFull from '../assets/logo-ui-full.png';

export default function PublicRegister() {
  const navigate = useNavigate();
  const [form, setForm] = useState({
    company_name: '',
    npwp: '',
    email: '',
    password: '',
    confirm_password: ''
  });
  const [loading, setLoading] = useState(false);
  const [error, setError] = useState(null);

  const handleSubmit = async (e) => {
    e.preventDefault();
    if (form.password !== form.confirm_password) {
      return setError('Konfirmasi password tidak cocok.');
    }
    
    setError(null);
    setLoading(true);

    try {
      const res = await fetch(`${API_BASE}/auth/register`, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(form)
      });
      const data = await res.json();
      
      if (data.success) {
        alert(data.message);
        navigate('/login');
      } else {
        setError(data.message);
      }
    } catch (err) {
      setError('Terjadi kesalahan saat terhubung ke server.');
    } finally {
      setLoading(false);
    }
  };

  return (
    <div className="min-h-screen bg-surface flex relative overflow-hidden font-sans">
      
      {/* Background Ornamen */}
      <div className="absolute top-0 right-0 w-[800px] h-[800px] bg-gradient-to-br from-dpbj-gold/20 to-transparent rounded-full blur-3xl -translate-y-1/2 translate-x-1/3 pointer-events-none" />
      <div className="absolute bottom-0 left-0 w-[600px] h-[600px] bg-gradient-to-tr from-dpbj-navy/10 to-transparent rounded-full blur-3xl translate-y-1/3 -translate-x-1/3 pointer-events-none" />

      {/* Main Container */}
      <div className="w-full max-w-5xl mx-auto flex z-10 p-4 md:p-8 items-center justify-center">
        
        <div className="bg-white/80 backdrop-blur-xl w-full rounded-[2rem] shadow-2xl border border-white flex overflow-hidden min-h-[600px]">
          
          {/* Left: Branding (Hidden on Mobile) */}
          <div className="hidden lg:flex w-5/12 bg-dpbj-navy relative flex-col justify-between p-12 overflow-hidden text-white">
            <div className="absolute inset-0 bg-[url('https://www.transparenttextures.com/patterns/cubes.png')] opacity-10 mix-blend-overlay"></div>
            <div className="absolute -bottom-32 -left-32 w-96 h-96 bg-dpbj-gold/30 rounded-full blur-3xl"></div>
            
            <div className="relative z-10 space-y-6">
              <div className="bg-white p-4 rounded-2xl inline-block shadow-lg">
                <img src={logoFull} alt="UI Logo" className="h-10 object-contain" />
              </div>
              <div className="space-y-4">
                <h1 className="text-3xl font-bold leading-tight">
                  Bergabung Sebagai Penyedia <span className="text-dpbj-gold">Universitas Indonesia</span>
                </h1>
                <p className="text-blue-100/80 leading-relaxed text-sm">
                  Daftarkan perusahaan Anda untuk mendapatkan akses ke ribuan paket pengadaan barang dan jasa setiap tahunnya. Transparan, adil, dan akuntabel.
                </p>
              </div>
            </div>

            <div className="relative z-10 text-xs text-blue-200/60 font-medium">
              &copy; {new Date().getFullYear()} DPBJ Universitas Indonesia.<br/>
              Direktorat Pengadaan Barang dan Jasa
            </div>
          </div>

          {/* Right: Register Form */}
          <div className="w-full lg:w-7/12 p-8 md:p-12 flex flex-col justify-center">
            
            <div className="max-w-md mx-auto w-full">
              
              <div className="text-center lg:text-left mb-8">
                <div className="inline-flex items-center justify-center w-12 h-12 bg-dpbj-gold/10 rounded-xl mb-4 lg:hidden">
                  <UserPlus className="text-dpbj-gold" size={24} />
                </div>
                <h2 className="text-2xl font-bold text-dpbj-navy mb-2">Registrasi Penyedia (Vendor)</h2>
                <p className="text-muted text-sm">Lengkapi formulir di bawah ini untuk membuat akun baru.</p>
              </div>

              {error && (
                <div className="mb-6 p-4 bg-red-50 border border-red-200 text-red-600 text-sm font-semibold rounded-xl flex items-center gap-2 animate-fade-in">
                  <div className="w-1.5 h-1.5 rounded-full bg-red-600 shrink-0" />
                  {error}
                </div>
              )}

              <form onSubmit={handleSubmit} className="space-y-4">
                
                {/* Nama Perusahaan */}
                <div className="space-y-1.5">
                  <label className="block text-xs font-semibold text-dpbj-navy">Nama Perusahaan (Sesuai Akta)</label>
                  <div className="relative group">
                    <div className="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-gray-400 group-focus-within:text-dpbj-gold transition-colors">
                      <Building2 size={18} />
                    </div>
                    <input 
                      type="text" 
                      required
                      placeholder="PT. Maju Mundur"
                      className="form-input w-full pl-10 bg-surface border-transparent hover:border-gray-300 focus:border-dpbj-gold focus:bg-white"
                      value={form.company_name}
                      onChange={e => setForm({...form, company_name: e.target.value})}
                    />
                  </div>
                </div>

                {/* NPWP */}
                <div className="space-y-1.5">
                  <label className="block text-xs font-semibold text-dpbj-navy">NPWP Perusahaan</label>
                  <div className="relative group">
                    <div className="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-gray-400 group-focus-within:text-dpbj-gold transition-colors">
                      <FileText size={18} />
                    </div>
                    <input 
                      type="text" 
                      required
                      placeholder="XX.XXX.XXX.X-XXX.XXX"
                      className="form-input w-full pl-10 bg-surface border-transparent hover:border-gray-300 focus:border-dpbj-gold focus:bg-white"
                      value={form.npwp}
                      onChange={e => setForm({...form, npwp: e.target.value})}
                    />
                  </div>
                </div>

                {/* Email */}
                <div className="space-y-1.5">
                  <label className="block text-xs font-semibold text-dpbj-navy">Email Perusahaan</label>
                  <div className="relative group">
                    <div className="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-gray-400 group-focus-within:text-dpbj-gold transition-colors">
                      <Mail size={18} />
                    </div>
                    <input 
                      type="email" 
                      required
                      placeholder="admin@perusahaan.com"
                      className="form-input w-full pl-10 bg-surface border-transparent hover:border-gray-300 focus:border-dpbj-gold focus:bg-white"
                      value={form.email}
                      onChange={e => setForm({...form, email: e.target.value})}
                    />
                  </div>
                </div>

                {/* Password & Confirm */}
                <div className="grid grid-cols-2 gap-4">
                  <div className="space-y-1.5">
                    <label className="block text-xs font-semibold text-dpbj-navy">Kata Sandi</label>
                    <div className="relative group">
                      <div className="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-gray-400 group-focus-within:text-dpbj-gold transition-colors">
                        <Lock size={18} />
                      </div>
                      <input 
                        type="password" 
                        required
                        placeholder="••••••••"
                        className="form-input w-full pl-10 bg-surface border-transparent hover:border-gray-300 focus:border-dpbj-gold focus:bg-white"
                        value={form.password}
                        onChange={e => setForm({...form, password: e.target.value})}
                      />
                    </div>
                  </div>
                  <div className="space-y-1.5">
                    <label className="block text-xs font-semibold text-dpbj-navy">Konfirmasi Sandi</label>
                    <div className="relative group">
                      <div className="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-gray-400 group-focus-within:text-dpbj-gold transition-colors">
                        <Lock size={18} />
                      </div>
                      <input 
                        type="password" 
                        required
                        placeholder="••••••••"
                        className="form-input w-full pl-10 bg-surface border-transparent hover:border-gray-300 focus:border-dpbj-gold focus:bg-white"
                        value={form.confirm_password}
                        onChange={e => setForm({...form, confirm_password: e.target.value})}
                      />
                    </div>
                  </div>
                </div>

                <div className="pt-4">
                  <button 
                    type="submit" 
                    disabled={loading}
                    className="w-full bg-dpbj-navy hover:bg-blue-900 text-white font-semibold py-3 px-4 rounded-xl shadow-lg shadow-dpbj-navy/20 transition-all flex items-center justify-center gap-2 group active:scale-[0.98] disabled:opacity-70"
                  >
                    {loading ? 'Memproses...' : 'Daftar Sekarang'}
                    {!loading && <ArrowRight size={18} className="group-hover:translate-x-1 transition-transform" />}
                  </button>
                </div>
              </form>

              <div className="mt-8 pt-6 border-t border-gray-100 text-center">
                <p className="text-sm text-gray-500">
                  Sudah memiliki akun?{' '}
                  <Link to="/login" className="font-bold text-dpbj-navy hover:text-dpbj-gold transition-colors">
                    Login di sini
                  </Link>
                </p>
              </div>

            </div>
          </div>
        </div>
      </div>
    </div>
  );
}
