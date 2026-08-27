import { PieChart, Pie, Cell, ResponsiveContainer, AreaChart, Area, XAxis, YAxis, Tooltip, CartesianGrid, BarChart, Bar } from 'recharts';

const STATUS_LABELS = {
  draft: 'Draft', pengumuman: 'Pengumuman', pendaftaran: 'Pendaftaran',
  penawaran: 'Penawaran', evaluasi: 'Evaluasi', pemenang: 'Pemenang',
  masa_sanggah: 'Masa Sanggah', kontrak: 'Kontrak', selesai: 'Selesai', batal: 'Batal',
};

const STATUS_COLORS = ['#FFD400', '#1A3668', '#3A5B96', '#22c55e', '#a855f7', '#f97316', '#0ea5e9', '#ef4444', '#64748b'];

const MONTH_LABELS = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];

function GlassTooltip({ active, payload, label, formatter }) {
  if (!active || !payload?.length) return null;
  return (
    <div className="glass-card px-3 py-2 !bg-white/90 text-xs shadow-glass-lg">
      {label && <p className="font-bold text-dpbj-navy mb-1">{label}</p>}
      {payload.map((p, i) => (
        <p key={i} className="text-dpbj-navy/80">
          <span className="font-semibold" style={{ color: p.color || p.fill }}>{p.name}</span>
          {': '}{formatter ? formatter(p.value) : p.value}
        </p>
      ))}
    </div>
  );
}

export function TenderStatusDonut({ data }) {
  const chartData = (data || []).map(d => ({ name: STATUS_LABELS[d.status] || d.status, value: d.count }));
  const total = chartData.reduce((sum, d) => sum + d.value, 0);

  if (total === 0) {
    return <EmptyChartState label="Belum ada data paket tender untuk ditampilkan" />;
  }

  return (
    <div className="relative">
      <ResponsiveContainer width="100%" height={220}>
        <PieChart>
          <Pie
            data={chartData}
            dataKey="value"
            nameKey="name"
            cx="50%"
            cy="50%"
            innerRadius={62}
            outerRadius={88}
            paddingAngle={3}
            animationDuration={800}
            animationBegin={100}
          >
            {chartData.map((entry, i) => (
              <Cell key={entry.name} fill={STATUS_COLORS[i % STATUS_COLORS.length]} stroke="rgba(255,255,255,0.6)" strokeWidth={2} />
            ))}
          </Pie>
          <Tooltip content={<GlassTooltip />} />
        </PieChart>
      </ResponsiveContainer>
      <div className="absolute inset-0 flex flex-col items-center justify-center pointer-events-none">
        <span className="text-3xl font-extrabold text-dpbj-navy">{total}</span>
        <span className="text-[10px] font-semibold text-muted uppercase tracking-wide">Total Paket</span>
      </div>
      <div className="flex flex-wrap gap-x-3 gap-y-1.5 justify-center mt-3">
        {chartData.map((d, i) => (
          <span key={d.name} className="flex items-center gap-1.5 text-[11px] text-dpbj-navy/70">
            <span className="w-2 h-2 rounded-full inline-block" style={{ background: STATUS_COLORS[i % STATUS_COLORS.length] }} />
            {d.name} <span className="font-semibold text-dpbj-navy">{d.value}</span>
          </span>
        ))}
      </div>
    </div>
  );
}

export function MonthlyTrendArea({ data }) {
  const map = new Map((data || []).map(d => [d.month, d.count]));
  const now = new Date();
  const chartData = [];
  for (let i = 5; i >= 0; i--) {
    const d = new Date(now.getFullYear(), now.getMonth() - i, 1);
    const key = `${d.getFullYear()}-${String(d.getMonth() + 1).padStart(2, '0')}`;
    chartData.push({ month: MONTH_LABELS[d.getMonth()], count: map.get(key) || 0 });
  }
  const hasData = chartData.some(d => d.count > 0);

  if (!hasData) {
    return <EmptyChartState label="Belum ada pengajuan dalam 6 bulan terakhir" />;
  }

  return (
    <ResponsiveContainer width="100%" height={200}>
      <AreaChart data={chartData} margin={{ top: 10, right: 8, left: -20, bottom: 0 }}>
        <defs>
          <linearGradient id="trendGold" x1="0" y1="0" x2="0" y2="1">
            <stop offset="0%" stopColor="#FFD400" stopOpacity={0.5} />
            <stop offset="100%" stopColor="#FFD400" stopOpacity={0.02} />
          </linearGradient>
        </defs>
        <CartesianGrid strokeDasharray="3 6" stroke="#E5E9F0" vertical={false} />
        <XAxis dataKey="month" tick={{ fontSize: 11, fill: '#6B7280' }} axisLine={false} tickLine={false} />
        <YAxis tick={{ fontSize: 11, fill: '#6B7280' }} axisLine={false} tickLine={false} allowDecimals={false} width={24} />
        <Tooltip content={<GlassTooltip />} />
        <Area
          type="monotone"
          dataKey="count"
          name="Pengajuan"
          stroke="#CCAA00"
          strokeWidth={2.5}
          fill="url(#trendGold)"
          animationDuration={900}
          dot={{ r: 3, fill: '#FFD400', stroke: '#1A3668', strokeWidth: 1.5 }}
          activeDot={{ r: 5 }}
        />
      </AreaChart>
    </ResponsiveContainer>
  );
}

export function CategoryBar({ data }) {
  const chartData = (data || []).filter(d => d.count > 0);
  if (chartData.length === 0) {
    return <EmptyChartState label="Belum ada kategori paket untuk ditampilkan" />;
  }
  return (
    <ResponsiveContainer width="100%" height={Math.max(160, chartData.length * 36)}>
      <BarChart data={chartData} layout="vertical" margin={{ top: 0, right: 24, left: 0, bottom: 0 }}>
        <XAxis type="number" hide allowDecimals={false} />
        <YAxis
          type="category"
          dataKey="category"
          tick={{ fontSize: 11, fill: '#1A3668', fontWeight: 600 }}
          axisLine={false}
          tickLine={false}
          width={110}
        />
        <Tooltip content={<GlassTooltip />} cursor={{ fill: 'rgba(255,212,0,0.08)' }} />
        <Bar dataKey="count" name="Jumlah" radius={[0, 8, 8, 0]} animationDuration={800} barSize={16}>
          {chartData.map((entry, i) => (
            <Cell key={entry.category} fill={STATUS_COLORS[i % STATUS_COLORS.length]} />
          ))}
        </Bar>
      </BarChart>
    </ResponsiveContainer>
  );
}

function EmptyChartState({ label }) {
  return (
    <div className="h-[180px] flex flex-col items-center justify-center text-center gap-2 text-muted">
      <div className="w-12 h-12 rounded-2xl bg-dpbj-gold-faint flex items-center justify-center">
        <span className="text-xl">📊</span>
      </div>
      <p className="text-xs max-w-[220px]">{label}</p>
    </div>
  );
}
