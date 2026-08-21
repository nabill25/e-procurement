import { useState, useEffect } from 'react';
import { X, FileText, Upload, ChevronRight, CheckCircle2, AlertCircle } from 'lucide-react';
import { useApp, API_BASE } from '../../context/AppContext';

const CATEGORIES = ['Barang', 'Jasa Konsultansi', 'Jasa Konstruksi', 'Jasa Lainnya', 'Barang/Jasa TIK'];
const BUDGET_SOURCES = ['DIPA', 'BLU', 'PNBP', 'Hibah', 'Lainnya'];
const UNITS = [
  'Direktorat TIK UI', 'Direktorat Keuangan', 'Direktorat Pengembangan Fasilitas',
  'FMIPA UI', 'FKUI', 'FEB UI', 'Fakultas Hukum UI', 'FT UI', 'Perpustakaan Pusat UI',
  'Direktorat Operasional', 'DPBJ Universitas Indonesia',
];
const STEPS = ['Informasi Dasar', 'Detail Anggaran', 'Spesifikasi', 'Analisa Kebutuhan & Pasar', 'Dokumen'];

const INITIAL = {
  title: '', unit_kerja: '', category: '', estimated_value: '',
  budget_source: '', budget_code: '', fiscal_year: '2025',
  quantity: '', unit_of_measure: 'Unit', needed_by_date: '',
  technical_spec: '', description: '',
  komoditas: '', analisa_kebutuhan: '', analisa_pasar: '',
  risiko_teridentifikasi: false, risiko_keterangan: '',
};

function StepIndicator({ steps, current }) {
  return (
    <div className="flex items-center gap-0 mb-8">
      {steps.map((step, i) => {
        const isActive   = i === current;
        const isComplete = i < current;
        return (
          <div key={step} className="flex items-center flex-1 last:flex-none">
            <div className="flex flex-col items-center">
              <div className={`w-8 h-8 rounded-full flex items-center justify-center text-xs font-bold transition-all duration-300 ${
                isComplete ? 'gold-gradient text-dpbj-navy-dark' :
                isActive   ? 'bg-dpbj-navy text-white shadow-lg ring-4 ring-dpbj-navy/20' :
                             'bg-border text-muted'
              }`}>
                {isComplete ? <CheckCircle2 size={14} /> : i + 1}
              </div>
              <p className={`text-[10px] font-semibold mt-1 whitespace-nowrap ${
                isActive ? 'text-dpbj-navy' : isComplete ? 'text-dpbj-gold-dark' : 'text-muted'
              }`}>{step}</p>
            </div>
            {i < steps.length - 1 && (
              <div className={`flex-1 h-0.5 mx-2 mb-4 rounded-full transition-all duration-500 ${isComplete ? 'bg-dpbj-gold' : 'bg-border'}`} />
            )}
          </div>
        );
      })}
    </div>
  );
}

function FormField({ label, required, children, hint }) {
  return (
    <div>
      <label className="form-label">
        {label}{required && <span className="text-red-500 ml-0.5">*</span>}
      </label>
      {children}
      {hint && <p className="text-[11px] text-muted mt-1">{hint}</p>}
    </div>
  );
}

export default function NewProcurementModal({ isOpen, onClose }) {
  const { addRequest, setActivePage } = useApp();
  const [step, setStep]     = useState(0);
  const [form, setForm]     = useState(INITIAL);
  const [files, setFiles]   = useState({ kak: null, rab: null, nota: null });
  const [errors, setErrors] = useState({});
  const [submitted, setSubmitted] = useState(false);
  const [analisaKebutuhanOptions, setAnalisaKebutuhanOptions] = useState([]);
  const [analisaPasarOptions, setAnalisaPasarOptions] = useState([]);

  useEffect(() => {
    fetch(`${API_BASE}/master/analisa_kebutuhan`).then(r => r.json()).then(j => { if (j.success) setAnalisaKebutuhanOptions(j.data); }).catch(() => {});
    fetch(`${API_BASE}/master/analisa_pasar`).then(r => r.json()).then(j => { if (j.success) setAnalisaPasarOptions(j.data); }).catch(() => {});
  }, []);

  const set = (key, val) => {
    setForm(f => ({ ...f, [key]: val }));
    if (errors[key]) setErrors(e => ({ ...e, [key]: '' }));
  };

  const validateStep = () => {
    const e = {};
    if (step === 0) {
      if (!form.title.trim())     e.title     = 'Judul pengajuan wajib diisi';
      if (!form.unit_kerja)       e.unit_kerja = 'Unit kerja wajib dipilih';
      if (!form.category)         e.category  = 'Kategori wajib dipilih';
    }
    if (step === 1) {
      if (!form.estimated_value || isNaN(Number(form.estimated_value.replace(/\./g, '')))) e.estimated_value = 'Nilai estimasi harus berupa angka';
      if (!form.budget_source)    e.budget_source = 'Sumber anggaran wajib dipilih';
    }
    setErrors(e);
    return Object.keys(e).length === 0;
  };

  const next = () => { if (validateStep()) setStep(s => s + 1); };
  const prev = () => setStep(s => s - 1);

  const handleSubmit = async () => {
    try {
      const value = Number(String(form.estimated_value).replace(/\./g, ''));
      
      const formData = new FormData();
      Object.keys(form).forEach(k => {
        if (k === 'estimated_value') formData.append(k, value);
        else formData.append(k, form[k]);
      });
      if (files.kak) formData.append('kak', files.kak);
      if (files.rab) formData.append('rab', files.rab);
      if (files.nota) formData.append('nota', files.nota);

      await addRequest(formData);
      setSubmitted(true);
    } catch (err) {
      alert('Gagal mengirim pengajuan: ' + err.message);
    }
  };

  const handleClose = () => {
    if (submitted) setActivePage('pengajuan');
    if (onClose) onClose();
  };

  if (!isOpen) return null;

  return (
    <div className="modal-overlay" onClick={e => e.target === e.currentTarget && handleClose()}>
      <div className="modal-container">
        {/* Modal Header */}
        <div className="sticky top-0 bg-white rounded-t-2xl border-b border-border px-6 py-4 flex items-center justify-between z-10">
          <div className="flex items-center gap-3">
            <div className="w-9 h-9 gold-gradient rounded-xl flex items-center justify-center">
              <FileText size={17} className="text-dpbj-navy-dark" />
            </div>
            <div>
              <h2 className="text-base font-bold text-dpbj-navy">Pengajuan Pengadaan Baru</h2>
              <p className="text-xs text-muted">Formulir Pengajuan — DPBJ UI · TA 2025</p>
            </div>
          </div>
          <button onClick={handleClose} className="p-2 rounded-xl hover:bg-surface transition-colors text-muted hover:text-dpbj-navy">
            <X size={18} />
          </button>
        </div>

        <div className="px-6 py-6">
          {!submitted ? (
            <>
              <StepIndicator steps={STEPS} current={step} />

              {/* Step 0: Basic Info */}
              {step === 0 && (
                <div className="space-y-4 animate-fade-in">
                  <FormField label="Judul Pengajuan" required hint="Deskripsikan kebutuhan secara ringkas dan jelas">
                    <input className={`form-input ${errors.title ? 'border-red-400 ring-2 ring-red-100' : ''}`}
                      value={form.title} onChange={e => set('title', e.target.value)}
                      placeholder="Contoh: Pengadaan Laptop untuk Dosen FMIPA UI" />
                    {errors.title && <p className="text-xs text-red-500 mt-1 flex items-center gap-1"><AlertCircle size={11} />{errors.title}</p>}
                  </FormField>

                  <div className="grid grid-cols-2 gap-4">
                    <FormField label="Unit Kerja / Satker" required>
                      <select className={`form-select ${errors.unit_kerja ? 'border-red-400' : ''}`}
                        value={form.unit_kerja} onChange={e => set('unit_kerja', e.target.value)}>
                        <option value="">Pilih Unit Kerja...</option>
                        {UNITS.map(u => <option key={u} value={u}>{u}</option>)}
                      </select>
                      {errors.unit_kerja && <p className="text-xs text-red-500 mt-1 flex items-center gap-1"><AlertCircle size={11} />{errors.unit_kerja}</p>}
                    </FormField>

                    <FormField label="Kategori Pengadaan" required>
                      <select className={`form-select ${errors.category ? 'border-red-400' : ''}`}
                        value={form.category} onChange={e => set('category', e.target.value)}>
                        <option value="">Pilih Kategori...</option>
                        {CATEGORIES.map(c => <option key={c} value={c}>{c}</option>)}
                      </select>
                      {errors.category && <p className="text-xs text-red-500 mt-1 flex items-center gap-1"><AlertCircle size={11} />{errors.category}</p>}
                    </FormField>
                  </div>

                  <FormField label="Deskripsi Kebutuhan">
                    <textarea className="form-input h-24 resize-none" value={form.description}
                      onChange={e => set('description', e.target.value)}
                      placeholder="Jelaskan latar belakang dan tujuan pengadaan ini..." />
                  </FormField>

                  <div className="grid grid-cols-2 gap-4">
                    <FormField label="Dibutuhkan Sebelum">
                      <input type="date" className="form-input" value={form.needed_by_date}
                        onChange={e => set('needed_by_date', e.target.value)} />
                    </FormField>
                    <FormField label="Tahun Anggaran">
                      <select className="form-select" value={form.fiscal_year} onChange={e => set('fiscal_year', e.target.value)}>
                        <option value="2025">2025</option>
                        <option value="2026">2026</option>
                      </select>
                    </FormField>
                  </div>
                </div>
              )}

              {/* Step 1: Budget */}
              {step === 1 && (
                <div className="space-y-4 animate-fade-in">
                  <FormField label="Nilai Estimasi (RAB)" required hint="Isi dalam Rupiah tanpa simbol, misal: 500000000">
                    <div className="relative">
                      <span className="absolute left-3.5 top-1/2 -translate-y-1/2 text-sm font-semibold text-muted">Rp</span>
                      <input className={`form-input pl-10 ${errors.estimated_value ? 'border-red-400 ring-2 ring-red-100' : ''}`}
                        value={form.estimated_value} onChange={e => set('estimated_value', e.target.value)}
                        placeholder="0" inputMode="numeric" />
                    </div>
                    {errors.estimated_value && <p className="text-xs text-red-500 mt-1 flex items-center gap-1"><AlertCircle size={11} />{errors.estimated_value}</p>}
                  </FormField>

                  <div className="grid grid-cols-2 gap-4">
                    <FormField label="Sumber Anggaran" required>
                      <select className={`form-select ${errors.budget_source ? 'border-red-400' : ''}`}
                        value={form.budget_source} onChange={e => set('budget_source', e.target.value)}>
                        <option value="">Pilih Sumber...</option>
                        {BUDGET_SOURCES.map(b => <option key={b} value={b}>{b}</option>)}
                      </select>
                      {errors.budget_source && <p className="text-xs text-red-500 mt-1 flex items-center gap-1"><AlertCircle size={11} />{errors.budget_source}</p>}
                    </FormField>
                    <FormField label="Kode Akun / MAK" hint="Contoh: 526211">
                      <input className="form-input" value={form.budget_code}
                        onChange={e => set('budget_code', e.target.value)} placeholder="526211" />
                    </FormField>
                  </div>

                  <div className="p-4 bg-dpbj-gold-faint rounded-xl border border-dpbj-gold/30">
                    <p className="text-xs font-semibold text-dpbj-gold-dark mb-1">Informasi Penting</p>
                    <p className="text-xs text-dpbj-navy/70">
                      Nilai estimasi harus disertai RAB (Rincian Anggaran Biaya) yang akan diunggah pada langkah terakhir.
                      Nilai HPS akan ditetapkan oleh PPK setelah pengajuan disetujui.
                    </p>
                  </div>
                </div>
              )}

              {/* Step 2: Spec */}
              {step === 2 && (
                <div className="space-y-4 animate-fade-in">
                  <div className="grid grid-cols-2 gap-4">
                    <FormField label="Volume / Kuantitas">
                      <input type="number" className="form-input" value={form.quantity}
                        onChange={e => set('quantity', e.target.value)} placeholder="1" min="1" />
                    </FormField>
                    <FormField label="Satuan">
                      <select className="form-select" value={form.unit_of_measure}
                        onChange={e => set('unit_of_measure', e.target.value)}>
                        {['Unit', 'Set', 'Paket', 'Orang', 'Bulan', 'M²', 'Meter', 'Liter', 'Kg', 'Hari'].map(u =>
                          <option key={u} value={u}>{u}</option>)}
                      </select>
                    </FormField>
                  </div>

                  <FormField label="Spesifikasi Teknis" hint="Uraikan spesifikasi minimum yang dibutuhkan">
                    <textarea className="form-input h-36 resize-none" value={form.technical_spec}
                      onChange={e => set('technical_spec', e.target.value)}
                      placeholder="Contoh:&#10;- Processor: Intel Core i7 Gen 12 atau setara&#10;- RAM: minimum 16GB DDR5&#10;- Storage: SSD NVMe 512GB&#10;- Display: 15.6 inci FHD IPS&#10;- OS: Windows 11 Pro" />
                  </FormField>
                </div>
              )}

              {/* Step 3: Analisa Kebutuhan & Pasar */}
              {step === 3 && (
                <div className="space-y-4 animate-fade-in">
                  <p className="text-xs text-dpbj-navy/70 bg-surface p-3 rounded-lg border border-border">
                    Analisa ini membantu menentukan strategi pengadaan yang tepat berdasarkan sifat kebutuhan dan kondisi pasar penyedia.
                  </p>

                  <FormField label="Komoditas / Jenis Barang-Jasa" hint="Sebutkan komoditas spesifik yang dibutuhkan">
                    <input className="form-input" value={form.komoditas}
                      onChange={e => set('komoditas', e.target.value)}
                      placeholder="Contoh: Laptop, Jasa Konsultan IT, dst" />
                  </FormField>

                  <div className="grid grid-cols-2 gap-4">
                    <FormField label="Analisa Kebutuhan">
                      <select className="form-select" value={form.analisa_kebutuhan} onChange={e => set('analisa_kebutuhan', e.target.value)}>
                        <option value="">Pilih...</option>
                        {analisaKebutuhanOptions.map(o => <option key={o.id} value={o.nama}>{o.nama}</option>)}
                      </select>
                    </FormField>
                    <FormField label="Analisa Pasar">
                      <select className="form-select" value={form.analisa_pasar} onChange={e => set('analisa_pasar', e.target.value)}>
                        <option value="">Pilih...</option>
                        {analisaPasarOptions.map(o => <option key={o.id} value={o.nama}>{o.nama}</option>)}
                      </select>
                    </FormField>
                  </div>

                  <FormField label="Identifikasi Risiko">
                    <label className="flex items-center gap-2 text-sm text-dpbj-navy">
                      <input type="checkbox" checked={form.risiko_teridentifikasi}
                        onChange={e => set('risiko_teridentifikasi', e.target.checked)} />
                      Ada risiko yang teridentifikasi dalam pengadaan ini
                    </label>
                  </FormField>

                  {form.risiko_teridentifikasi && (
                    <FormField label="Keterangan Risiko" hint="Jelaskan risiko yang mungkin timbul dan mitigasinya">
                      <textarea className="form-input h-24 resize-none" value={form.risiko_keterangan}
                        onChange={e => set('risiko_keterangan', e.target.value)}
                        placeholder="Contoh: Keterlambatan pengiriman karena barang impor, mitigasi: pilih vendor dengan stok lokal" />
                    </FormField>
                  )}
                </div>
              )}

              {/* Step 4: Documents */}
              {step === 4 && (
                <div className="space-y-5 animate-fade-in">
                  <p className="text-sm text-dpbj-navy/80">Unggah dokumen pendukung pengajuan (format PDF, max 10MB per file).</p>

                  {[
                    { key: 'nota', label: 'Nota Dinas Pengajuan', hint: 'Nota dinas persetujuan pimpinan unit', required: true },
                    { key: 'kak', label: 'Kerangka Acuan Kerja (KAK / TOR)', hint: 'Dokumen KAK wajib dilampirkan', required: true },
                    { key: 'rab', label: 'Rincian Anggaran Estimasi (RAE/RAB)', hint: 'Excel atau PDF RAB', required: true },
                  ].map(({ key, label, hint, required }) => (
                    <FormField key={label} label={label} required={required} hint={hint}>
                      <div className="border-2 border-dashed border-border rounded-xl p-4 flex flex-col items-center gap-2 bg-surface">
                        <input type="file" className="form-input w-full text-xs" 
                          onChange={e => setFiles({ ...files, [key]: e.target.files[0] })} 
                          accept=".pdf,.doc,.docx,.xls,.xlsx" />
                      </div>
                    </FormField>
                  ))}

                  {/* Review Summary */}
                  <div className="bg-surface rounded-xl p-4 border border-border">
                    <p className="text-xs font-bold text-dpbj-navy mb-3 uppercase tracking-wide">Ringkasan Pengajuan</p>
                    <div className="space-y-2 text-xs">
                      {[
                        ['Judul', form.title || '—'],
                        ['Unit Kerja', form.unit_kerja || '—'],
                        ['Kategori', form.category || '—'],
                        ['Estimasi Nilai', form.estimated_value ? `Rp ${Number(form.estimated_value).toLocaleString('id-ID')}` : '—'],
                        ['Sumber Anggaran', form.budget_source || '—'],
                        ['Tahun Anggaran', form.fiscal_year],
                      ].map(([k, v]) => (
                        <div key={k} className="flex justify-between">
                          <span className="text-muted">{k}</span>
                          <span className="font-semibold text-dpbj-navy max-w-[60%] text-right">{v}</span>
                        </div>
                      ))}
                    </div>
                  </div>
                </div>
              )}

              {/* Navigation Buttons */}
              <div className="flex justify-between mt-6 pt-4 border-t border-border">
                <button onClick={prev} disabled={step === 0} className="btn-ghost disabled:opacity-40">
                  ← Sebelumnya
                </button>
                {step < STEPS.length - 1 ? (
                  <button onClick={next} className="btn-primary">
                    Selanjutnya <ChevronRight size={15} />
                  </button>
                ) : (
                  <button onClick={handleSubmit} className="btn-secondary">
                    <CheckCircle2 size={15} />
                    Kirim Pengajuan
                  </button>
                )}
              </div>
            </>
          ) : (
            /* Success State */
            <div className="flex flex-col items-center py-8 text-center animate-slide-up">
              <div className="w-20 h-20 gold-gradient rounded-3xl flex items-center justify-center mb-5 shadow-glow">
                <CheckCircle2 size={40} className="text-dpbj-navy-dark" />
              </div>
              <h3 className="text-xl font-bold text-dpbj-navy mb-2">Pengajuan Berhasil Dikirim!</h3>
              <p className="text-sm text-muted max-w-sm mb-1">
                Pengajuan Anda telah masuk ke sistem dan akan diproses oleh PPK & DPBJ UI.
              </p>
              <p className="text-xs text-muted mb-6">
                Status: <span className="font-semibold text-amber-600">Draft — Menunggu Review PPK</span>
              </p>
              <div className="flex gap-3">
                <button onClick={handleClose} className="btn-primary">
                  Lihat Daftar Pengajuan
                </button>
                <button onClick={() => { setForm(INITIAL); setStep(0); setSubmitted(false); }} className="btn-ghost">
                  Buat Pengajuan Lain
                </button>
              </div>
            </div>
          )}
        </div>
      </div>
    </div>
  );
}
