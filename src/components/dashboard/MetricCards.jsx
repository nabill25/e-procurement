import { Wallet, Briefcase, Building2, FileCheck2, Clock, BarChart3 } from 'lucide-react';
import { formatRupiah, Trend } from '../ui/shared';

function MetricCard({ id, icon: Icon, label, value, sub, trend, trendLabel, accentClass, bgClass, iconBg }) {
  return (
    <div id={id} className="metric-card group hover:-translate-y-1 hover:shadow-xl transition-all duration-300 cursor-default">
      {/* Background accent */}
      <div className={`absolute top-0 right-0 w-24 h-24 rounded-full opacity-5 -mr-8 -mt-8 ${bgClass} transition-transform duration-500 group-hover:scale-150`} />

      <div className="relative flex items-start justify-between mb-3">
        <div className={`w-11 h-11 rounded-xl flex items-center justify-center ${iconBg} transition-transform duration-300 group-hover:scale-110 group-hover:-rotate-3`}>
          <Icon size={20} className={accentClass} />
        </div>
        {trend !== undefined && (
          <Trend value={trend} label={trendLabel || 'vs bln lalu'} />
        )}
      </div>

      <div className="relative">
        <p className="text-2xl font-extrabold text-dpbj-navy tracking-tight">{value}</p>
        <p className="text-xs font-semibold text-muted mt-0.5">{label}</p>
        {sub && <p className="text-xs text-gray-400 mt-1">{sub}</p>}
      </div>
    </div>
  );
}

export default function MetricCards({ stats }) {
  // Safe defaults if stats is not yet loaded
  const s = stats || {
    total_budget_this_year: 0,
    active_tenders: 0,
    verified_vendors: 0,
    completed_contracts: 0,
    pending_reviews: 0
  };

  const totalAnggaran = parseFloat(s.total_budget_this_year || 0);
  const totalAnggaranUsed = parseFloat(s.total_budget_used || 0); 
  const budgetPct = totalAnggaran > 0 ? Math.round((totalAnggaranUsed / totalAnggaran) * 100) : 0;

  return (
    <div className="space-y-4">
      {/* Primary metric row */}
      <div className="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <MetricCard
          id="card-total-anggaran"
          icon={Wallet}
          label="Total Anggaran TA 2025"
          value={formatRupiah(totalAnggaran, true)}
          sub={`Terserap: ${budgetPct}% · ${formatRupiah(totalAnggaranUsed, true)}`}
          trend={s.trend_budget}
          trendLabel="vs TA 2024"
          accentClass="text-dpbj-gold-dark"
          bgClass="bg-dpbj-gold"
          iconBg="bg-dpbj-gold-faint"
        />
        <MetricCard
          id="card-paket-aktif"
          icon={Briefcase}
          label="Paket Aktif"
          value={s.active_tenders}
          sub={`${s.pending_reviews || 0} menunggu review`}
          trend={s.trend_active_tenders}
          accentClass="text-blue-600"
          bgClass="bg-blue-500"
          iconBg="bg-blue-50"
        />
        <MetricCard
          id="card-vendor-terverifikasi"
          icon={Building2}
          label="Vendor Terverifikasi"
          value={s.verified_vendors}
          sub={`Data per hari ini`}
          trend={s.trend_verified_vendors}
          accentClass="text-emerald-600"
          bgClass="bg-emerald-500"
          iconBg="bg-emerald-50"
        />
        <MetricCard
          id="card-bast-selesai"
          icon={FileCheck2}
          label="Kontrak Selesai"
          value={s.completed_contracts}
          sub={`Telah BAST`}
          trend={s.trend_completed_contracts}
          accentClass="text-purple-600"
          bgClass="bg-purple-500"
          iconBg="bg-purple-50"
        />
      </div>

      {/* Budget progress bar */}
      <div className="section-card">
        <div className="flex items-center justify-between mb-3">
          <div className="flex items-center gap-2">
            <BarChart3 size={16} className="text-dpbj-gold-dark" />
            <span className="text-sm font-semibold text-dpbj-navy">Realisasi Anggaran TA 2025</span>
          </div>
          <div className="flex items-center gap-4 text-xs text-muted">
            <span className="flex items-center gap-1.5">
              <span className="w-2.5 h-2.5 rounded-full gold-gradient inline-block" />
              Terserap: {formatRupiah(totalAnggaranUsed, true)}
            </span>
            <span className="flex items-center gap-1.5">
              <span className="w-2.5 h-2.5 rounded-full bg-border inline-block" />
              Sisa: {formatRupiah(totalAnggaran - totalAnggaranUsed, true)}
            </span>
          </div>
        </div>
        <div className="h-3 bg-border rounded-full overflow-hidden">
          <div
            className="h-full gold-gradient rounded-full transition-all duration-700 relative"
            style={{ width: `${budgetPct}%` }}
          >
            <div className="absolute inset-0 bg-white/20 rounded-full animate-pulse-soft" />
          </div>
        </div>
        <div className="flex justify-between mt-1.5">
          <span className="text-xs text-muted">0%</span>
          <span className="text-xs font-semibold text-dpbj-gold-dark">{budgetPct}% terserap</span>
          <span className="text-xs text-muted">100%</span>
        </div>
      </div>
    </div>
  );
}
