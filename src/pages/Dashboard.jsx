import { useState, useEffect, useCallback } from 'react';
import MetricCards from '../components/dashboard/MetricCards';
import TenderTable from '../components/dashboard/TenderTable';
import { useApp, API_BASE, getAuthHeaders } from '../context/AppContext';
import { Clock, Activity, CheckCircle2, AlertTriangle, FileText, Users, Handshake } from 'lucide-react';
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
    <div className="section-card">
      <div className="flex items-center gap-2 mb-4">
        <Activity size={16} className="text-dpbj-gold-dark" />
        <h3 className="text-sm font-bold text-dpbj-navy">Aktivitas Terkini</h3>
      </div>
      <div className="space-y-3">
        {recent.length === 0 ? (
          <p className="text-xs text-muted">Belum ada aktivitas.</p>
        ) : recent.map(log => (
          <div key={log.id} className="flex items-start gap-3 group">
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

function AdminPPKView({ dashboardStats, refreshTrigger }) {
  return (
    <>
      <MetricCards stats={dashboardStats} />
      <div className="grid grid-cols-1 xl:grid-cols-3 gap-6 mt-6">
        <div className="xl:col-span-2">
          <TenderTable compact />
        </div>
        <div className="space-y-4">
          <div className="section-card">
            <h3 className="text-sm font-bold text-dpbj-navy mb-4">Perhatian Segera</h3>
            <div className="space-y-3">
              <div className="flex items-center gap-3 p-3 rounded-xl hover:bg-surface transition-colors cursor-pointer border border-border">
                <div className="w-9 h-9 rounded-xl flex items-center justify-center bg-amber-50">
                  <Clock size={16} className="text-amber-600" />
                </div>
                <div className="flex-1"><p className="text-xs text-muted">Pengajuan pending review</p></div>
                <span className="text-sm font-extrabold text-amber-600">{dashboardStats?.pending_reviews || 0}</span>
              </div>
              <div className="flex items-center gap-3 p-3 rounded-xl hover:bg-surface transition-colors cursor-pointer border border-border">
                <div className="w-9 h-9 rounded-xl flex items-center justify-center bg-red-50">
                  <AlertTriangle size={16} className="text-red-600" />
                </div>
                <div className="flex-1"><p className="text-xs text-muted">Tender aktif</p></div>
                <span className="text-sm font-extrabold text-red-600">{dashboardStats?.active_tenders || 0}</span>
              </div>
            </div>
          </div>
          <RecentActivity refreshTrigger={refreshTrigger} />
        </div>
      </div>
    </>
  );
}

function PokjaView({ dashboardStats, refreshTrigger }) {
  return (
    <>
      <div className="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
        <div className="bg-white p-5 rounded-2xl shadow-sm border border-border flex items-center gap-4">
          <div className="w-12 h-12 rounded-xl bg-blue-50 flex items-center justify-center text-blue-600"><FileText size={24} /></div>
          <div><p className="text-xs font-semibold text-muted mb-1">Total Tender (Tahun Ini)</p><p className="text-2xl font-bold text-dpbj-navy">{dashboardStats?.total_tenders || 0}</p></div>
        </div>
        <div className="bg-white p-5 rounded-2xl shadow-sm border border-border flex items-center gap-4">
          <div className="w-12 h-12 rounded-xl bg-amber-50 flex items-center justify-center text-amber-600"><Activity size={24} /></div>
          <div><p className="text-xs font-semibold text-muted mb-1">Tender Berjalan</p><p className="text-2xl font-bold text-dpbj-navy">{dashboardStats?.active_tenders || 0}</p></div>
        </div>
        <div className="bg-white p-5 rounded-2xl shadow-sm border border-border flex items-center gap-4">
          <div className="w-12 h-12 rounded-xl bg-emerald-50 flex items-center justify-center text-emerald-600"><CheckCircle2 size={24} /></div>
          <div><p className="text-xs font-semibold text-muted mb-1">Tender Selesai</p><p className="text-2xl font-bold text-dpbj-navy">{dashboardStats?.completed_contracts || 0}</p></div>
        </div>
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
        <div className="bg-white p-5 rounded-2xl shadow-sm border border-border flex flex-col justify-center">
          <div className="flex items-center gap-3 mb-2">
            <CheckCircle2 size={20} className="text-emerald-500" />
            <p className="text-sm font-bold text-dpbj-navy">Status Verifikasi</p>
          </div>
          <p className="text-xs text-muted">Akun Anda telah terverifikasi dan memenuhi syarat mengikuti lelang.</p>
        </div>
        <div className="bg-white p-5 rounded-2xl shadow-sm border border-border flex items-center gap-4">
          <div className="w-12 h-12 rounded-xl bg-blue-50 flex items-center justify-center text-blue-600"><Handshake size={24} /></div>
          <div><p className="text-xs font-semibold text-muted mb-1">Tender Diikuti</p><p className="text-2xl font-bold text-dpbj-navy">0</p></div>
        </div>
        <div className="bg-white p-5 rounded-2xl shadow-sm border border-border flex items-center gap-4">
          <div className="w-12 h-12 rounded-xl bg-amber-50 flex items-center justify-center text-amber-600"><AlertTriangle size={24} /></div>
          <div><p className="text-xs font-semibold text-muted mb-1">Undangan Langsung</p><p className="text-2xl font-bold text-dpbj-navy">0</p></div>
        </div>
      </div>

      <div className="section-card border-l-4 border-l-dpbj-gold mb-6">
        <h3 className="font-bold text-dpbj-navy mb-2">Peluang Lelang</h3>
        <p className="text-sm text-muted">Terdapat paket lelang yang sedang masuk tahap pendaftaran. Silakan lihat daftar tender di bawah ini untuk mendaftar.</p>
      </div>

      <div className="bg-white rounded-2xl shadow-sm border border-border p-6">
        <h2 className="text-lg font-bold text-dpbj-navy mb-4">Daftar Paket Pengadaan Publik</h2>
        <TenderTable compact />
      </div>
    </>
  );
}

export default function Dashboard() {
  const { refreshTrigger, user } = useApp();
  const [dashboardStats, setDashboardStats] = useState(null);

  useEffect(() => {
    fetch(`${API_BASE}/dashboard`, { headers: getAuthHeaders() })
      .then(res => res.json())
      .then(json => {
        if (json.success) setDashboardStats(json.data);
      })
      .catch(err => console.error('Failed to fetch dashboard stats', err));
  }, [refreshTrigger]);

  return (
    <div className="space-y-6 animate-fade-in">
      {/* Welcome banner */}
      <div className="relative overflow-hidden rounded-2xl p-6 text-white sidebar-bg border border-white/10">
        <div className="relative z-10">
          <p className="text-dpbj-gold text-xs font-bold uppercase tracking-widest mb-1">
            {user.role === 'vendor' ? 'Portal Penyedia' : 'Selamat Datang Kembali'}
          </p>
          <h2 className="text-xl font-extrabold">
            {user.role === 'vendor' ? 'Sistem Informasi Kinerja Penyedia (SIKAP)' : 'Direktorat Pengadaan Barang dan Jasa'}
          </h2>
          <p className="text-slate-300 text-sm mt-1">Universitas Indonesia · Tahun Anggaran 2025</p>
        </div>
        <div className="absolute right-6 top-1/2 -translate-y-1/2 opacity-10">
          <div className="w-32 h-32 gold-gradient rounded-full" />
        </div>
        <div className="absolute right-20 top-0 opacity-5">
          <div className="w-24 h-24 bg-white rounded-full -mt-8" />
        </div>
      </div>

      {user.role === 'admin' || user.role === 'ppk' ? (
        <AdminPPKView dashboardStats={dashboardStats} refreshTrigger={refreshTrigger} />
      ) : user.role === 'pokja' ? (
        <PokjaView dashboardStats={dashboardStats} refreshTrigger={refreshTrigger} />
      ) : (
        <VendorView dashboardStats={dashboardStats} />
      )}
    </div>
  );
}
