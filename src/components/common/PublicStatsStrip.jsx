import { useEffect, useState } from 'react';
import { Briefcase, FileSignature, CalendarClock } from 'lucide-react';
import { motion } from 'framer-motion';
import AnimatedCounter from './AnimatedCounter';
import { API_BASE } from '../../context/AppContext';

const STAT_DEFS = [
  { key: 'tender_aktif',      label: 'Tender Aktif Saat Ini',       icon: Briefcase,     accent: 'text-orange-500 bg-orange-50' },
  { key: 'kontrak_berjalan',  label: 'Kontrak Sedang Berjalan',     icon: FileSignature, accent: 'text-blue-500 bg-blue-50' },
  { key: 'total_tahun_ini',   label: 'Paket Diumumkan Tahun Ini',   icon: CalendarClock, accent: 'text-emerald-500 bg-emerald-50' },
];

// Strip 3 kartu statistik "hidup" (angka asli dari database, animasi count-up), sengaja
// ditaruh menumpang di tepi bawah hero slider (negative margin) supaya jadi jembatan
// visual antara hero dan konten di bawahnya - pola umum landing page modern.
// Kalau API belum bisa dihubungi, section ini diam-diam tidak tampil (bukan tampil "0"
// yang bisa disalahartikan sebagai fakta beneran tidak ada tender).
export default function PublicStatsStrip() {
  const [stats, setStats] = useState(null);

  useEffect(() => {
    fetch(`${API_BASE}/tenders/public-stats`)
      .then(res => res.json())
      .then(json => { if (json.success) setStats(json.data); })
      .catch(() => {});
  }, []);

  if (!stats) return null;

  return (
    <div className="relative z-20 px-6 -mt-8">
      <motion.div
        initial={{ opacity: 0, y: 24 }}
        animate={{ opacity: 1, y: 0 }}
        transition={{ duration: 0.6, ease: [0.16, 1, 0.3, 1], delay: 0.2 }}
        className="max-w-4xl mx-auto grid grid-cols-1 sm:grid-cols-3 gap-4"
      >
        {STAT_DEFS.map(({ key, label, icon: Icon, accent }) => (
          <div
            key={key}
            className="glass-card p-5 flex items-center gap-4 interactive-lift"
          >
            <div className={`w-12 h-12 rounded-2xl flex items-center justify-center shrink-0 ${accent}`}>
              <Icon size={22} />
            </div>
            <div className="min-w-0">
              <p className="text-2xl font-black text-dpbj-navy leading-none tabular-nums">
                <AnimatedCounter value={stats[key]} />
              </p>
              <p className="text-xs text-muted font-medium mt-1.5 truncate">{label}</p>
            </div>
          </div>
        ))}
      </motion.div>
    </div>
  );
}
