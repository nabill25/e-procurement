import { TrendingUp, TrendingDown, Minus } from 'lucide-react';
import clsx from 'clsx';

/** Format Indonesian Rupiah */
export function formatRupiah(value, compact = false) {
  if (value === null || value === undefined || value === '') return '-';
  value = Number(value);
  if (Number.isNaN(value)) return '-';
  if (compact) {
    // Pakai Math.abs() buat nentuin ambang batas (M/Jt) - sebelumnya nilai negatif (misal
    // -35 juta, kasus "defisit" di dashboard efisiensi) selalu lolos ke baris angka mentah
    // "Rp -35.000.000" karena "value >= 1_000_000" tidak akan pernah benar untuk angka minus.
    const abs = Math.abs(value);
    if (abs >= 1_000_000_000) return `Rp ${(value / 1_000_000_000).toFixed(1)} M`;
    if (abs >= 1_000_000)     return `Rp ${(value / 1_000_000).toFixed(1)} Jt`;
    return `Rp ${value.toLocaleString('id-ID')}`;
  }
  return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(value);
}

/** Status Badge */
// `config = {}` sebagai jaga-jaga: sempat ketemu pemanggilan tanpa prop `config` sama sekali
// (Purchasing.jsx) yang bikin baris ini crash (`Cannot read properties of undefined`) begitu
// ada data order sungguhan untuk dirender, bukan cuma soal gaya kalau config-nya kosong.
export function StatusBadge({ status, config = {} }) {
  const cfg = config[status];
  if (!cfg) return <span className="badge badge-draft">{status}</span>;
  return (
    <span className={clsx('badge', cfg.className)}>
      <span className="w-1.5 h-1.5 rounded-full flex-shrink-0" style={{ backgroundColor: cfg.dot }} />
      {cfg.label}
    </span>
  );
}

/** Trend indicator */
export function Trend({ value, label }) {
  const isPositive = value > 0;
  const isNeutral  = value === 0;
  const Icon = isNeutral ? Minus : isPositive ? TrendingUp : TrendingDown;
  return (
    <span className={clsx('inline-flex items-center gap-1 text-xs font-medium',
      isNeutral ? 'text-gray-500' : isPositive ? 'text-emerald-600' : 'text-red-500')}>
      <Icon size={12} />
      {Math.abs(value)}% {label}
    </span>
  );
}

/** Skeleton loader */
export function Skeleton({ className = '' }) {
  return <div className={clsx('shimmer rounded-lg', className)} />;
}

/** Empty state */
export function EmptyState({ icon: Icon, title, description, action }) {
  return (
    <div className="flex flex-col items-center justify-center py-16 text-center">
      {Icon && (
        <div className="w-14 h-14 rounded-2xl bg-dpbj-gold-faint flex items-center justify-center mb-4">
          <Icon size={24} className="text-dpbj-gold" />
        </div>
      )}
      <p className="text-dpbj-navy font-semibold mb-1">{title}</p>
      {description && <p className="text-muted text-sm mb-4 max-w-xs">{description}</p>}
      {action}
    </div>
  );
}

/** Divider with label */
export function Divider({ label }) {
  return (
    <div className="flex items-center gap-3 my-4">
      <div className="flex-1 h-px bg-border" />
      {label && <span className="text-[10px] font-semibold text-muted uppercase tracking-widest">{label}</span>}
      <div className="flex-1 h-px bg-border" />
    </div>
  );
}
