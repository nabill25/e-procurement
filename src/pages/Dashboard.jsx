import { useState, useEffect, useCallback } from 'react';
import MetricCards from '../components/dashboard/MetricCards';
import TenderTable from '../components/dashboard/TenderTable';
import { TenderStatusDonut, MonthlyTrendArea, CategoryBar } from '../components/dashboard/DashboardCharts';
import { useApp, API_BASE, getAuthHeaders } from '../context/AppContext';
import { Clock, Activity, CheckCircle2, AlertTriangle, FileText, Handshake, PieChart, TrendingUp, LayoutGrid, Star, Sparkles } from 'lucide-react';
import clsx from 'clsx';

function RecentActivity({ refreshTrigger }) {
  const [recent, setRecent] = useState([]);

  const fetchRecent = useCallback(async () => {
    try {
      const res = await fetch(`${API_BASE}/audit?limit=5`, { headers: getAuthHeaders() });
      const json = await res.json();
      if (json.success) setRecent(json.data);
    } catch (err) {
      console.error(err);
    }
  }, []);

  useEffect(() => {
    fetchRecent();
  }, [fetchRecent, refreshTrigger]);

  const actionColors = {
    CREATE: 'text-emerald-600 bg-emerald-50',
    UPDATE: 'text-blue-600 bg-blue-50',
    DELETE: 'text-red-600 bg-red-50',
    VIEW:   'text-gray-600 bg-gray-100',
    LOGIN:  'text-purple-600 bg-purple-50',
  };

  return (
    <div className="glass-card p-5 h-full">
      <div className="flex items-center gap-2 mb-4">
        <Activity size={16} className="text-dpbj-gold-dark" />
        <h3 className="text-sm font-bold text-dpbj-navy">Aktivitas Terkini</h3>
      </div>
      <div className="space-y-3">
        {recent.length === 0 ? (
          <p className="text-xs text-muted">Belum ada aktivitas.</p>
        ) : recent.map((log, i) => (
          <div key={log.id} className="flex items-start gap-3 group animate-fade-in" style={{ animationDelay: `${i * 60}ms`, animationFillMode: 'backwards' }}>
            <span className={clsx("text-[10px] font-bold px-1.5 py-0.5 rounded-md flex-shrink-0 mt-0.5", actionColors[log.action] || 'text-gray-500 bg-gray-100')}>
              {log.action}
            </span>
            <div className="flex-1 min-w-0">
              <p className="text-xs font-semibold text-dpbj-navy leading-snug">{log.description}</p>
              <p className="text-[10px] text-muted mt-0.5">{log.user_name || 'Sistem'} · {new Date(log.created_at).toLocaleTimeString('id-ID')}</p>
            </div>
          </div>
        ))}
      </div>
    </div>
  );
}

function ChartCard({ icon: Icon, title, subtitle, children, className }) {
  return (
    <div className={clsx('glass-card p-5', className)}>
      <div className="flex items-center gap-2 mb-1">
        <Icon size={16} className="text-dpbj-gold-dark" />
        <h3 className="text-sm font-bold text-dpbj-navy">{title}</h3>
      </div>
      {subtitle && <p className="text-[11px] text-muted mb-3">{subtitle}</p>}
      <div className={subtitle ? '' : 'mt-3'}>{children}</div>
    </div>
  );
}

function TopVendorsCard({ vendors }) {
  const medalColors = ['text-amber-500', 'text-slate-400', 'text-amber-700'];
  return (
    <div className="glass-card p-5 h-full">
      <div className="flex items-center gap-2 mb-4">
        <Star size={16} className="text-dpbj-gold-dark" />
        <h3 className="text-sm font-bold text-dpbj-navy">Vendor Kinerja Terbaik</h3>
      </div>
      {!vendors?.length ? (
        <p className="text-xs text-muted">Belum ada data penilaian kinerja vendor.</p>
      ) : (
        <div className="space-y-2.5">
          {vendors.map((v, i) => (
            <div key={v.company_name + i} className="flex items-center gap-3 p-2.5 rounded-xl hover:bg-white/60 transition-colors">
              <span className={clsx('w-6 text-sm font-extrabold text-center', medalColors[i] || 'text-gray-300')}>{i + 1}</span>
              <div className="flex-1 min-w-0">
                <p className="text-xs font-semibold text-dpbj-navy truncate">{v.company_name}</p>
                {v.qualification_class && <p className="text-[10px] text-muted">Kelas {v.qualification_class}</p>}
              </div>
              <span className="flex items-center gap-1 text-xs font-bold text-dpbj-gold-dark">
                <Star size={11} className="fill-dpbj-gold text-dpbj-gold" /> {parseFloat(v.rating).toFixed(1)}
              </span>
            </div>
          ))}
        </div>
      )}
    </div>
  );
}

function AdminPPKView({ dashboardStats, analytics, refreshTrigger }) {
  return (
    <>
      <MetricCards stats={dashboardStats} />
      <div className="grid grid-cols-1 lg:grid-cols-2 gap-6 mt-6">
        <ChartCard icon={PieChart} title="Sebaran Status Paket Tender" subtitle="Distribusi paket berdasarkan tahapan saat ini">
          <TenderStatusDonut data={analytics?.by_status} />
        </ChartCard>
        <ChartCard icon={TrendingUp} title="Tren Pengajuan Pengadaan" subtitle="Jumlah pengajuan baru per bulan, 6 bulan terakhir">
          <MonthlyTrendArea data={analytics?.monthly_trend} />
        </ChartCard>
      </div>
      <div className="grid grid-cols-1 xl:grid-cols-3 gap-6 mt-6">
        <div className="xl:col-span-2 space-y-6">
          <TenderTable compact />
          <ChartCard icon={LayoutGrid} title="Paket Berdasarkan Kategori">
            <CategoryBar data={analytics?.by_category} />
          </ChartCard>
        </div>
        <div className="space-y-6">
          <div className="glass-card p-5">
            <h3 className="text-sm font-bold text-dpbj-navy mb-4">Perhatian Segera</h3>
            <div className="space-y-3">
              <div className="flex items-center gap-3 p-3 rounded-xl hover:bg-white/60 transition-colors cursor-pointer border border-white/50">
                <div className="w-9 h-9 rounded-xl flex items-center justify-center bg-amber-50">
                  <Clock size={16} className="text-amber-600" />
                </div>
                <div className="flex-1"><p className="text-xs text-muted">Pengajuan pending review</p></div>
                <span className="text-sm font-extrabold text-amber-600">{dashboardStats?.pending_reviews || 0}</span>
              </div>
              <div className="flex items-center gap-3 p-3 rounded-xl hover:bg-white/60 transition-colors cursor-pointer border border-white/50">
                <div className="w-9 h-9 rounded-xl flex items-center justify-center bg-red-50">
                  <AlertTriangle size={16} className="text-red-600" />
                </div>
                <div className="flex-1"><p className="text-xs text-muted">Tender aktif</p></div>
                <span className="text-sm font-extrabold text-red-600">{dashboardStats?.active_tenders || 0}</span>
              </div>
            </div>
          </div>
          <TopVendorsCard vendors={analytics?.top_vendors} />
          <RecentActivity refreshTrigger={refreshTrigger} />
        </div>
      </div>
    </>
  );
}

function PokjaView({ dashboardStats, analytics, refreshTrigger }) {
  return (
    <>
      <div className="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
        <div className="glass-card p-5 flex items-center gap-4 animate-slide-up" style={{ animationFillMode: 'backwards' }}>
          <div className="w-12 h-12 rounded-2xl bg-blue-50 flex items-center justify-center text-blue-600"><FileText size={24} /></div>
          <div><p className="text-xs font-semibold text-muted mb-1">Total Tender (Tahun Ini)</p><p className="text-2xl font-bold text-dpbj-navy tabular-nums">{dashboardStats?.total_tenders || 0}</p></div>
        </div>
        <div className="glass-card p-5 flex items-center gap-4 animate-slide-up" style={{ animationDelay: '80ms', animationFillMode: 'backwards' }}>
          <div className="w-12 h-12 rounded-2xl bg-amber-50 flex items-center justify-center text-amber-600"><Activity size={24} /></div>
          <div><p className="text-xs font-semibold text-muted mb-1">Tender Berjalan</p><p className="text-2xl font-bold text-dpbj-navy tabular-nums">{dashboardStats?.active_tenders || 0}</p></div>
        </div>
        <div className="glass-card p-5 flex items-center gap-4 animate-slide-up" style={{ animationDelay: '160ms', animationFillMode: 'backwards' }}>
          <div className="w-12 h-12 rounded-2xl bg-emerald-50 flex items-center justify-center text-emerald-600"><CheckCircle2 size={24} /></div>
          <div><p className="text-xs font-semibold text-muted mb-1">Tender Selesai</p><p className="text-2xl font-bold text-dpbj-navy tabular-nums">{dashboardStats?.completed_contracts || 0}</p></div>
        </div>
      </div>
      <div className="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
        <ChartCard icon={PieChart} title="Sebaran Status Paket Tender">
          <TenderStatusDonut data={analytics?.by_status} />
        </ChartCard>
        <ChartCard icon={LayoutGrid} title="Paket Berdasarkan Kategori">
          <CategoryBar data={analytics?.by_category} />
        </ChartCard>
      </div>
      <div className="grid grid-cols-1 xl:grid-cols-3 gap-6">
        <div className="xl:col-span-2"><TenderTable compact /></div>
        <div><RecentActivity refreshTrigger={refreshTrigger} /></div>
      </div>
    </>
  );
}

function VendorView({ dashboardStats }) {
  return (
    <>
      <div className="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
        <div className="glass-card p-5 flex flex-col justify-center animate-slide-up" style={{ animationFillMode: 'backwards' }}>
          <div className="flex items-center gap-3 mb-2">
            <CheckCircle2 size={20} className="text-emerald-500" />
            <p className="text-sm font-bold text-dpbj-navy">Status Verifikasi</p>
          </div>
          <p className="text-xs text-muted">Akun Anda telah terverifikasi dan memenuhi syarat mengikuti lelang.</p>
        </div>
        <div className="glass-card p-5 flex items-center gap-4 animate-slide-up" style={{ animationDelay: '80ms', animationFillMode: 'backwards' }}>
          <div className="w-12 h-12 rounded-2xl bg-blue-50 flex items-center justify-center text-blue-600"><Handshake size={24} /></div>
          <div><p className="text-xs font-semibold text-muted mb-1">Tender Diikuti</p><p className="text-2xl font-bold text-dpbj-navy">0</p></div>
        </div>
        <div className="glass-card p-5 flex items-center gap-4 animate-slide-up" style={{ animationDelay: '160ms', animationFillMode: 'backwards' }}>
          <div className="w-12 h-12 rounded-2xl bg-amber-50 flex items-center justify-center text-amber-600"><AlertTriangle size={24} /></div>
          <div><p className="text-xs font-semibold text-muted mb-1">Undangan Langsung</p><p className="text-2xl font-bold text-dpbj-navy">0</p></div>
        </div>
      </div>

      <div className="glass-card border-l-4 border-l-dpbj-gold mb-6 p-5 relative overflow-hidden">
        <Sparkles size={72} className="absolute -right-3 -top-3 text-dpbj-gold/10" />
        <h3 className="font-bold text-dpbj-navy mb-2 relative">Peluang Lelang</h3>
        <p className="text-sm text-muted relative">Terdapat paket lelang yang sedang masuk tahap pendaftaran. Silakan lihat daftar tender di bawah ini untuk mendaftar.</p>
      </div>

      <div className="glass-card p-6">
        <h2 className="text-lg font-bold text-dpbj-navy mb-4">Daftar Paket Pengadaan Publik</h2>
        <TenderTable compact />
      </div>
    </>
  );
}

export default function Dashboard() {
  const { refreshTrigger, user } = useApp();
  const [dashboardStats, setDashboardStats] = useState(null);
  const [analytics, setAnalytics] = useState(null);

  useEffect(() => {
    fetch(`${API_BASE}/dashboard`, { headers: getAuthHeaders() })
      .then(res => res.json())
      .then(json => { if (json.success) setDashboardStats(json.data); })
      .catch(err => console.error('Failed to fetch dashboard stats', err));

    fetch(`${API_BASE}/dashboard/analytics`, { headers: getAuthHeaders() })
      .then(res => res.json())
      .then(json => { if (json.success) setAnalytics(json.data); })
      .catch(err => console.error('Failed to fetch dashboard analytics', err));
  }, [refreshTrigger]);

  return (
    <div className="space-y-6 animate-fade-in relative">
      {/* Welcome banner - liquid glass dengan mesh gradient ambient melayang */}
      <div className="relative overflow-hidden rounded-2xl p-6 text-white sidebar-bg border border-white/10 shadow-glass-lg">
        <div className="dashboard-mesh">
          <div className="absolute w-40 h-40 rounded-full bg-dpbj-gold/25 blur-3xl top-[-40px] right-10 animate-float" />
          <div className="absolute w-32 h-32 rounded-full bg-blue-400/20 blur-3xl bottom-[-30px] left-1/3 animate-float-delay" />
        </div>
        <div className="relative z-10">
          <p className="text-dpbj-gold text-xs font-bold uppercase tracking-widest mb-1 flex items-center gap-1.5">
            <Sparkles size={12} className="animate-pulse-soft" />
            {user.role === 'vendor' ? 'Portal Penyedia' : 'Selamat Datang Kembali'}
          </p>
          <h2 className="text-xl font-extrabold">
            {user.role === 'vendor' ? 'Sistem Informasi Kinerja Penyedia (SIKAP)' : 'Direktorat Pengadaan Barang dan Jasa'}
          </h2>
          <p className="text-slate-300 text-sm mt-1">Universitas Indonesia · Tahun Anggaran 2025</p>
        </div>
      </div>

      {user.role === 'admin' || user.role === 'ppk' ? (
        <AdminPPKView dashboardStats={dashboardStats} analytics={analytics} refreshTrigger={refreshTrigger} />
      ) : user.role === 'pokja' ? (
        <PokjaView dashboardStats={dashboardStats} analytics={analytics} refreshTrigger={refreshTrigger} />
      ) : (
        <VendorView dashboardStats={dashboardStats} />
      )}
    </div>
  );
}
