import { useState, useEffect, useCallback, useRef, useMemo } from 'react';
import { motion, AnimatePresence, useInView, useMotionValue, useSpring } from 'framer-motion';
import {
  LayoutGrid, TrendingUp, TrendingDown, Building2, Filter, FileStack, Wallet, HandCoins,
  Sparkles, CheckCircle2, Clock3, XCircle, ArrowRight, Table2, BarChart3, ChevronLeft, ChevronRight,
} from 'lucide-react';
import { API_BASE, getAuthHeaders } from '../context/AppContext';
import { formatRupiah } from '../components/ui/shared';

const STATUS_LABEL = {
  diajukan: 'Diajukan', diverifikasi: 'Diverifikasi', revisi: 'Revisi', disetujui: 'Disetujui', ditolak: 'Ditolak',
  pengumuman: 'Pengumuman', pendaftaran: 'Pendaftaran', penawaran: 'Penawaran', evaluasi: 'Evaluasi',
  pemenang: 'Pemenang', masa_sanggah: 'Masa Sanggah', kontrak: 'Kontrak',
  aktif: 'Aktif', selesai: 'Selesai',
};

function statusGabungan(row) {
  // Padanan "status gabungan perencanaan dan pengadaan" di executive_report.php sistem lama -
  // satu label ringkas yang menunjukkan paket ini sudah sampai tahap mana dari RUP sampai kontrak.
  if (row.status_kontrak) return { label: `Kontrak: ${STATUS_LABEL[row.status_kontrak] || row.status_kontrak}`, tone: 'ok' };
  if (row.status_tender) return { label: `Tender: ${STATUS_LABEL[row.status_tender] || row.status_tender}`, tone: 'progress' };
  if (row.status_rup === 'ditolak') return { label: 'RUP Ditolak', tone: 'bad' };
  return { label: `RUP: ${STATUS_LABEL[row.status_rup] || row.status_rup}`, tone: 'progress' };
}

const TONE_META = {
  ok:       { className: 'bg-emerald-100 text-emerald-700', Icon: CheckCircle2 },
  bad:      { className: 'bg-red-100 text-red-700',         Icon: XCircle },
  progress: { className: 'bg-blue-100 text-blue-700',       Icon: Clock3 },
};

function StatusPill({ row }) {
  const s = statusGabungan(row);
  const meta = TONE_META[s.tone];
  return (
    <span className={`badge text-[10px] gap-1 ${meta.className}`}>
      <meta.Icon size={10} /> {s.label}
    </span>
  );
}

// ── Angka Rupiah yang "menghitung naik" begitu masuk layar - padanan AnimatedCounter yang
// sudah dipakai di landing page publik, versi ini format hasilnya lewat formatRupiah(compact). ──
function AnimatedRupiah({ value, className = '' }) {
  const ref = useRef(null);
  const isInView = useInView(ref, { once: true, margin: '-30px' });
  const motionValue = useMotionValue(0);
  const spring = useSpring(motionValue, { duration: 1100, bounce: 0 });
  const [display, setDisplay] = useState(0);

  useEffect(() => { if (isInView) motionValue.set(Number(value) || 0); }, [isInView, value, motionValue]);
  useEffect(() => {
    const unsub = spring.on('change', (latest) => setDisplay(latest));
    return unsub;
  }, [spring]);

  return <motion.span ref={ref} className={className}>{formatRupiah(display, true)}</motion.span>;
}

// ── Kartu KPI kaca (glassmorphism) dengan badge ikon berwarna + animasi masuk berjenjang ──
function KpiGlassCard({ icon: Icon, label, value, isRupiah, tone, delay, trend }) {
  const toneRing = {
    navy:  'from-dpbj-navy/15 to-dpbj-navy/5 text-dpbj-navy',
    gold:  'from-dpbj-gold/25 to-dpbj-gold/5 text-dpbj-gold-dark',
    good:  'from-emerald-400/25 to-emerald-400/5 text-emerald-600',
    bad:   'from-red-400/25 to-red-400/5 text-red-500',
  }[tone];

  return (
    <motion.div
      initial={{ opacity: 0, y: 16, scale: 0.97 }}
      animate={{ opacity: 1, y: 0, scale: 1 }}
      transition={{ duration: 0.45, delay, ease: [0.16, 1, 0.3, 1] }}
      whileHover={{ y: -3 }}
      className="relative overflow-hidden rounded-2xl border border-white/60 bg-white/55 backdrop-blur-md shadow-glass p-4 sm:p-5 group"
    >
      {/* Kilau lembut yang bergerak halus di belakang kartu saat hover */}
      <div className="pointer-events-none absolute -top-10 -right-10 w-28 h-28 rounded-full bg-gradient-to-br from-white/40 to-transparent blur-2xl opacity-0 group-hover:opacity-100 transition-opacity duration-500" />
      <div className={`w-9 h-9 rounded-xl bg-gradient-to-br ${toneRing} flex items-center justify-center mb-3 shadow-sm`}>
        <Icon size={17} strokeWidth={2.2} />
      </div>
      <p className="text-[10px] uppercase tracking-wide text-muted font-bold mb-1">{label}</p>
      <p className={`font-extrabold tabular-nums ${isRupiah ? 'text-lg text-dpbj-navy' : 'text-2xl text-dpbj-navy'} flex items-center gap-1.5`}>
        {isRupiah ? <AnimatedRupiah value={value} /> : Math.round(value).toLocaleString('id-ID')}
        {trend !== undefined && (trend >= 0
          ? <TrendingUp size={13} className="text-emerald-500" />
          : <TrendingDown size={13} className="text-red-500" />)}
      </p>
    </motion.div>
  );
}

// ── Diagram batang divergen (efisiensi per unit kerja): hijau = hemat, merah = defisit,
// dari garis nol di tengah. Identitas unit selalu label teks (bukan warna saja); nilai persis
// muncul saat hover (lapisan interaksi wajib per pedoman dataviz). Toggle ke tabel tersedia
// untuk aksesibilitas. ──
function EfficiencyBars({ data }) {
  const [view, setView] = useState('chart');
  const [hovered, setHovered] = useState(null);
  const maxAbs = Math.max(1, ...data.map(u => Math.abs(Number(u.total_efisiensi) || 0)));

  return (
    <div>
      <div className="flex items-center justify-between mb-4 flex-wrap gap-2">
        <div className="flex items-center gap-3 text-[11px]">
          <span className="flex items-center gap-1.5 font-semibold text-dpbj-navy">
            <span className="w-2.5 h-2.5 rounded-full bg-emerald-500 inline-block" /> Efisiensi (hemat)
          </span>
          <span className="flex items-center gap-1.5 font-semibold text-dpbj-navy">
            <span className="w-2.5 h-2.5 rounded-full bg-red-500 inline-block" /> Defisit (lebih dari HPS)
          </span>
        </div>
        <div className="flex items-center gap-1 bg-surface rounded-lg p-0.5 border border-border">
          <button
            onClick={() => setView('chart')}
            className={`flex items-center gap-1 px-2.5 py-1 rounded-md text-[11px] font-semibold transition-colors ${view === 'chart' ? 'bg-white shadow-sm text-dpbj-navy' : 'text-muted'}`}
          >
            <BarChart3 size={12} /> Grafik
          </button>
          <button
            onClick={() => setView('table')}
            className={`flex items-center gap-1 px-2.5 py-1 rounded-md text-[11px] font-semibold transition-colors ${view === 'table' ? 'bg-white shadow-sm text-dpbj-navy' : 'text-muted'}`}
          >
            <Table2 size={12} /> Tabel
          </button>
        </div>
      </div>

      <AnimatePresence mode="wait">
        {view === 'chart' ? (
          <motion.div key="chart" initial={{ opacity: 0 }} animate={{ opacity: 1 }} exit={{ opacity: 0 }} transition={{ duration: 0.2 }} className="space-y-2.5">
            {data.map((u, i) => {
              const val = Number(u.total_efisiensi) || 0;
              const positif = val >= 0;
              const pct = (Math.abs(val) / maxAbs) * 100;
              const isHover = hovered === i;
              return (
                <div
                  key={i}
                  onMouseEnter={() => setHovered(i)}
                  onMouseLeave={() => setHovered(null)}
                  className={`grid grid-cols-[7rem_1fr] sm:grid-cols-[10rem_1fr] items-center gap-2 rounded-lg px-2 py-1.5 transition-colors ${isHover ? 'bg-dpbj-navy/5' : ''}`}
                >
                  <span className="text-[11px] font-semibold text-dpbj-navy truncate" title={u.unit_kerja}>{u.unit_kerja}</span>
                  <div className="relative h-5">
                    {/* Garis nol di tengah */}
                    <div className="absolute left-1/2 top-0 bottom-0 w-px bg-border" />
                    {/* Animasi pakai scaleX (transform), BUKAN width - width memicu layout reflow tiap
                        frame di semua bar sekaligus, sempat bikin halaman ini berat sekali (ketahuan
                        dari test navigasi otomatis jadi 2x lebih lambat). transform-origin dipasang
                        di sisi yang menempel garis nol supaya animasinya tetap kelihatan "tumbuh
                        dari tengah" walau sekarang murni GPU transform. */}
                    <div className="absolute inset-0 flex items-center">
                      <div className="w-1/2 h-full flex justify-end pr-px">
                        {!positif && (
                          <motion.div
                            initial={{ scaleX: 0 }}
                            animate={{ scaleX: 1 }}
                            transition={{ duration: 0.5, delay: i * 0.03, ease: 'easeOut' }}
                            style={{ width: `${pct}%`, transformOrigin: 'right' }}
                            className="h-[10px] self-center rounded-l-full bg-gradient-to-l from-red-500 to-red-400"
                          />
                        )}
                      </div>
                      <div className="w-1/2 h-full flex justify-start pl-px">
                        {positif && (
                          <motion.div
                            initial={{ scaleX: 0 }}
                            animate={{ scaleX: 1 }}
                            transition={{ duration: 0.5, delay: i * 0.03, ease: 'easeOut' }}
                            style={{ width: `${pct}%`, transformOrigin: 'left' }}
                            className="h-[10px] self-center rounded-r-full bg-gradient-to-r from-emerald-500 to-emerald-400"
                          />
                        )}
                      </div>
                    </div>
                    <span
                      className={`absolute top-1/2 -translate-y-1/2 text-[10px] font-bold tabular-nums whitespace-nowrap ${positif ? 'text-emerald-600' : 'text-red-500'}`}
                      style={positif
                        ? { left: `calc(50% + ${pct / 2}% + 8px)` }
                        : { right: `calc(50% + ${pct / 2}% + 8px)` }}
                    >
                      {isHover ? formatRupiah(val) : formatRupiah(val, true)}
                    </span>
                  </div>
                </div>
              );
            })}
          </motion.div>
        ) : (
          <motion.div key="table" initial={{ opacity: 0 }} animate={{ opacity: 1 }} exit={{ opacity: 0 }} transition={{ duration: 0.2 }} className="table-scroll">
            <table className="data-table">
              <thead><tr><th>Unit Kerja</th><th>Jml Paket</th><th>Total HPS</th><th>Total Kontrak</th><th>Efisiensi</th></tr></thead>
              <tbody className="stagger-list">
                {data.map((u, i) => (
                  <tr key={i} className="stagger-item">
                    <td className="text-xs font-semibold text-dpbj-navy">{u.unit_kerja}</td>
                    <td className="text-xs">{u.jumlah_paket}</td>
                    <td className="text-xs">{formatRupiah(u.total_hps)}</td>
                    <td className="text-xs">{formatRupiah(u.total_kontrak)}</td>
                    <td className={`text-xs font-semibold ${u.total_efisiensi >= 0 ? 'text-emerald-600' : 'text-red-500'}`}>{formatRupiah(u.total_efisiensi)}</td>
                  </tr>
                ))}
              </tbody>
            </table>
          </motion.div>
        )}
      </AnimatePresence>
    </div>
  );
}

export default function ExecutiveDashboard() {
  const [rows, setRows] = useState([]);
  const [efficiency, setEfficiency] = useState(null);
  const [isLoading, setIsLoading] = useState(true);
  const [tahun, setTahun] = useState('');
  const [page, setPage] = useState(1);
  const [pagination, setPagination] = useState({ total: 0 });

  const fetchSummary = useCallback(async () => {
    setIsLoading(true);
    try {
      const params = new URLSearchParams({ page, limit: 20 });
      if (tahun) params.set('tahun', tahun);
      const [summaryRes, effRes] = await Promise.all([
        fetch(`${API_BASE}/dashboard/executive-summary?${params}`, { headers: getAuthHeaders() }),
        fetch(`${API_BASE}/dashboard/efficiency${tahun ? `?tahun=${tahun}` : ''}`, { headers: getAuthHeaders() }),
      ]);
      const summaryJson = await summaryRes.json();
      const effJson = await effRes.json();
      if (summaryJson.success) { setRows(summaryJson.data); setPagination(summaryJson.pagination); }
      if (effJson.success) setEfficiency(effJson.data);
    } catch (err) {
      console.error(err);
    } finally {
      setIsLoading(false);
    }
  }, [page, tahun]);

  useEffect(() => { fetchSummary(); }, [fetchSummary]);

  const overall = efficiency?.overall;
  const efisiensiPositif = overall?.total_efisiensi !== null && overall?.total_efisiensi >= 0;
  const totalPages = Math.max(1, Math.ceil(pagination.total / 20));

  const kpis = useMemo(() => ([
    { icon: FileStack, label: 'Jumlah Kontrak', value: overall?.jumlah_paket ?? 0, isRupiah: false, tone: 'navy' },
    { icon: Wallet, label: 'Total HPS', value: overall?.total_hps || 0, isRupiah: true, tone: 'navy' },
    { icon: HandCoins, label: 'Total Nilai Kontrak', value: overall?.total_kontrak || 0, isRupiah: true, tone: 'gold' },
    { icon: Sparkles, label: 'Efisiensi Pengadaan', value: overall?.total_efisiensi || 0, isRupiah: true, tone: efisiensiPositif ? 'good' : 'bad', trend: overall?.total_efisiensi },
  ]), [overall, efisiensiPositif]);

  return (
    <div className="relative -m-6 md:-m-8 p-6 md:p-8 space-y-5 animate-fade-in overflow-hidden">
      {/* Latar belakang lembut - gradien navy/emas + bola cahaya blur, dasar tema "glass" halaman ini.
          Statis (tidak animasi terus-menerus) supaya browser tidak perlu re-composite layer blur
          tiap frame - versi awal sempat pakai animate-float di elemen blur-3xl dan bikin
          render halaman ini berat sekali (ketahuan dari test navigasi otomatis yang jadi
          >2x lebih lambat cuma gara-gara halaman ini). */}
      <div className="pointer-events-none absolute inset-0 -z-10 bg-gradient-to-br from-dpbj-navy-dark/[0.04] via-transparent to-dpbj-gold/[0.06]" />
      <div className="pointer-events-none absolute -top-16 -left-10 w-72 h-72 rounded-full bg-dpbj-navy/10 blur-3xl -z-10" />
      <div className="pointer-events-none absolute top-40 -right-10 w-72 h-72 rounded-full bg-dpbj-gold/15 blur-3xl -z-10" />

      <motion.div
        initial={{ opacity: 0, y: -8 }} animate={{ opacity: 1, y: 0 }} transition={{ duration: 0.4 }}
        className="relative overflow-hidden rounded-2xl border border-white/60 bg-white/55 backdrop-blur-md shadow-glass p-5"
      >
        <div className="flex items-center gap-3">
          <div className="w-11 h-11 rounded-2xl gold-gradient flex items-center justify-center shadow-glow shrink-0">
            <LayoutGrid size={20} className="text-dpbj-navy-dark" />
          </div>
          <div>
            <h2 className="text-base font-bold text-dpbj-navy">Dashboard Pimpinan</h2>
            <p className="text-xs text-muted">Ringkasan portofolio pengadaan lintas tahap (RUP &rarr; Tender &rarr; Kontrak) dan efisiensi anggaran, untuk peninjauan cepat tanpa membuka detail satu per satu.</p>
          </div>
        </div>
      </motion.div>

      {/* Kartu KPI kaca */}
      <div className="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4">
        {kpis.map((k, i) => <KpiGlassCard key={k.label} {...k} delay={0.05 + i * 0.06} />)}
      </div>

      {/* Efisiensi per unit kerja */}
      {efficiency?.by_unit?.length > 0 && (
        <motion.div
          initial={{ opacity: 0, y: 12 }} animate={{ opacity: 1, y: 0 }} transition={{ duration: 0.4, delay: 0.15 }}
          className="rounded-2xl border border-white/60 bg-white/55 backdrop-blur-md shadow-glass p-4 sm:p-5"
        >
          <h3 className="text-sm font-bold text-dpbj-navy mb-1 flex items-center gap-2"><Building2 size={14} /> Efisiensi per Unit Kerja</h3>
          <p className="text-[11px] text-muted mb-4">Selisih HPS terhadap nilai kontrak final, per unit kerja pengaju.</p>
          <EfficiencyBars data={efficiency.by_unit} />
        </motion.div>
      )}

      {/* Rekap portofolio per paket */}
      <motion.div
        initial={{ opacity: 0, y: 12 }} animate={{ opacity: 1, y: 0 }} transition={{ duration: 0.4, delay: 0.22 }}
        className="rounded-2xl border border-white/60 bg-white/55 backdrop-blur-md shadow-glass p-4 sm:p-5"
      >
        <div className="flex items-center justify-between mb-3 flex-wrap gap-2">
          <h3 className="text-sm font-bold text-dpbj-navy flex items-center gap-2"><ArrowRight size={14} /> Portofolio Pengadaan</h3>
          <div className="flex items-center gap-2 bg-surface border border-border rounded-xl px-2.5 py-1.5">
            <Filter size={12} className="text-muted" />
            <select value={tahun} onChange={e => { setTahun(e.target.value); setPage(1); }} className="text-xs bg-transparent focus:outline-none text-dpbj-navy font-medium">
              <option value="">Semua Tahun</option>
              {[2024, 2025, 2026].map(y => <option key={y} value={y}>{y}</option>)}
            </select>
          </div>
        </div>
        <div className="table-scroll">
          <table className="data-table">
            <thead>
              <tr><th>No. Pengajuan</th><th>Judul</th><th>Unit Kerja</th><th>Pagu RUP</th><th>HPS</th><th>Nilai Kontrak</th><th>Status</th></tr>
            </thead>
            <AnimatePresence mode="wait">
              <motion.tbody
                key={`${page}-${tahun}-${isLoading}`}
                initial={{ opacity: 0 }} animate={{ opacity: 1 }} exit={{ opacity: 0 }} transition={{ duration: 0.15 }}
                className="stagger-list"
              >
                {isLoading ? (
                  <tr><td colSpan={7} className="py-10 text-center text-muted text-sm">Memuat data...</td></tr>
                ) : rows.length === 0 ? (
                  <tr><td colSpan={7} className="py-10 text-center text-muted text-sm">Tidak ada data pengajuan.</td></tr>
                ) : rows.map(row => (
                  <tr key={row.request_id} className="stagger-item hover:bg-dpbj-navy/[0.03] transition-colors">
                    <td className="font-mono text-xs">{row.request_number}</td>
                    <td className="text-xs max-w-xs truncate">{row.title}</td>
                    <td className="text-xs">{row.unit_kerja || '-'}</td>
                    <td className="text-xs">{formatRupiah(row.pagu_rup)}</td>
                    <td className="text-xs">{row.hps ? formatRupiah(row.hps) : '-'}</td>
                    <td className="text-xs">{row.contract_value ? formatRupiah(row.contract_value) : '-'}</td>
                    <td><StatusPill row={row} /></td>
                  </tr>
                ))}
              </motion.tbody>
            </AnimatePresence>
          </table>
        </div>
        {pagination.total > 20 && (
          <div className="flex items-center justify-between mt-3 text-xs text-muted">
            <span>Halaman {page} dari {totalPages} ({pagination.total} total)</span>
            <div className="flex gap-2">
              <button disabled={page <= 1} onClick={() => setPage(p => p - 1)} className="btn-secondary text-xs disabled:opacity-40"><ChevronLeft size={13} /> Sebelumnya</button>
              <button disabled={page >= totalPages} onClick={() => setPage(p => p + 1)} className="btn-secondary text-xs disabled:opacity-40">Berikutnya <ChevronRight size={13} /></button>
            </div>
          </div>
        )}
      </motion.div>
    </div>
  );
}
