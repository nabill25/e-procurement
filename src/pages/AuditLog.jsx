import { useState, useEffect, useCallback } from 'react';
import { Shield, CheckCircle2, XCircle, Download } from 'lucide-react';
import { API_BASE, useApp, getAuthHeaders } from '../context/AppContext';
import clsx from 'clsx';

const ACTION_STYLE = {
  CREATE: 'bg-emerald-100 text-emerald-700',
  UPDATE: 'bg-blue-100 text-blue-700',
  DELETE: 'bg-red-100 text-red-700',
  VIEW:   'bg-gray-100 text-gray-600',
  LOGIN:  'bg-purple-100 text-purple-700',
};

export default function AuditLog() {
  const { refreshTrigger } = useApp();
  const [logs, setLogs] = useState([]);
  const [isLoading, setIsLoading] = useState(true);

  const fetchLogs = useCallback(async () => {
    setIsLoading(true);
    try {
      const res = await fetch(`${API_BASE}/audit`, { headers: getAuthHeaders() });
      const json = await res.json();
      if (json.success) {
        setLogs(json.data);
      }
    } catch (err) {
      console.error('Failed to fetch audit logs:', err);
    } finally {
      setIsLoading(false);
    }
  }, []);

  useEffect(() => {
    fetchLogs();
  }, [fetchLogs, refreshTrigger]);

  return (
    <div className="space-y-6 animate-fade-in">
      {/* Header stats */}
      <div className="grid grid-cols-3 gap-4 stagger-grid">
        {[
          { label: 'Total Log Hari Ini',  value: logs.length, icon: Shield,       color: 'text-dpbj-navy'    },
          { label: 'Aktivitas Berhasil',  value: logs.filter(l => l.is_success).length,  icon: CheckCircle2, color: 'text-emerald-600' },
          { label: 'Aktivitas Gagal',     value: logs.filter(l => !l.is_success).length, icon: XCircle,     color: 'text-red-500'     },
        ].map(({ label, value, icon: Icon, color }) => (
          <div key={label} className="stagger-item section-card flex items-center gap-4">
            <div className="w-10 h-10 rounded-xl bg-surface flex items-center justify-center">
              <Icon size={20} className={color} />
            </div>
            <div>
              <p className={`text-2xl font-extrabold ${color}`}>{value}</p>
              <p className="text-xs text-muted">{label}</p>
            </div>
          </div>
        ))}
      </div>

      <div className="section-card">
        <div className="flex items-center justify-between mb-4">
          <div>
            <h2 className="text-base font-bold text-dpbj-navy">Log Aktivitas Sistem</h2>
            <p className="text-xs text-muted">Riwayat aktivitas untuk keperluan audit SPI</p>
          </div>
          <button
            onClick={() => {
              import('../utils/export').then(({ exportToCSV }) => {
                exportToCSV(logs, 'Log_Audit', {
                  created_at: 'Waktu',
                  user_name: 'Pengguna',
                  action: 'Aksi',
                  entity_type: 'Entitas',
                  description: 'Deskripsi',
                  ip_address: 'IP Address',
                  is_success: 'Berhasil',
                });
              });
            }}
            className="btn-ghost text-xs"
          >
            <Download size={13} />
            Export Log
          </button>
        </div>

        {/* Tabel biasa - cuma di layar >= sm, tempat 7 kolom masih muat wajar */}
        <div className="hidden sm:block table-scroll">
          <table className="data-table">
            <thead>
              <tr>
                <th>Waktu</th>
                <th>Pengguna</th>
                <th>Aksi</th>
                <th>Entitas</th>
                <th>Deskripsi</th>
                <th>IP Address</th>
                <th>Status</th>
              </tr>
            </thead>
            <tbody className="stagger-list">
              {isLoading ? (
                <tr><td colSpan={7} className="py-12 text-center text-muted text-sm">Memuat data...</td></tr>
              ) : logs.length === 0 ? (
                <tr><td colSpan={7} className="py-12 text-center text-muted text-sm">Tidak ada log.</td></tr>
              ) : logs.map(log => (
                <tr key={log.id} className="stagger-item">
                  <td><span className="font-mono text-xs text-muted">{new Date(log.created_at).toLocaleString('id-ID')}</span></td>
                  <td><span className="text-xs font-semibold text-dpbj-navy">{log.user_name || 'Sistem'}</span></td>
                  <td>
                    <span className={clsx('badge text-[10px]', ACTION_STYLE[log.action] || ACTION_STYLE.VIEW)}>
                      {log.action}
                    </span>
                  </td>
                  <td><span className="font-mono text-xs text-dpbj-slate">{log.entity_type}</span></td>
                  <td><p className="text-xs text-dpbj-navy max-w-xs">{log.description}</p></td>
                  <td><span className="font-mono text-xs text-muted">{log.ip_address}</span></td>
                  <td>
                    {log.is_success
                      ? <span className="flex items-center gap-1 text-xs text-emerald-600 font-semibold"><CheckCircle2 size={12} />Berhasil</span>
                      : <span className="flex items-center gap-1 text-xs text-red-500 font-semibold"><XCircle size={12} />Gagal</span>
                    }
                  </td>
                </tr>
              ))}
            </tbody>
          </table>
        </div>

        {/* Tampilan kartu - khusus mobile, supaya tidak perlu geser tabel 7 kolom di layar sempit */}
        <div className="sm:hidden space-y-2.5 stagger-list">
          {isLoading ? (
            <p className="py-12 text-center text-muted text-sm">Memuat data...</p>
          ) : logs.length === 0 ? (
            <p className="py-12 text-center text-muted text-sm">Tidak ada log.</p>
          ) : logs.map(log => (
            <div key={log.id} className="stagger-item rounded-xl border border-border p-3.5 bg-surface">
              <div className="flex items-start justify-between gap-2 mb-2">
                <span className="font-mono text-[11px] text-muted">{new Date(log.created_at).toLocaleString('id-ID')}</span>
                {log.is_success
                  ? <span className="flex items-center gap-1 text-[11px] text-emerald-600 font-semibold flex-none"><CheckCircle2 size={12} />Berhasil</span>
                  : <span className="flex items-center gap-1 text-[11px] text-red-500 font-semibold flex-none"><XCircle size={12} />Gagal</span>
                }
              </div>
              <div className="flex items-center gap-2 mb-1.5">
                <span className={clsx('badge text-[10px]', ACTION_STYLE[log.action] || ACTION_STYLE.VIEW)}>
                  {log.action}
                </span>
                <span className="text-xs font-semibold text-dpbj-navy">{log.user_name || 'Sistem'}</span>
              </div>
              <p className="text-xs text-dpbj-navy mb-1.5">{log.description}</p>
              <div className="flex items-center justify-between text-[11px] text-dpbj-slate">
                <span className="font-mono">{log.entity_type}</span>
                <span className="font-mono text-muted">{log.ip_address}</span>
              </div>
            </div>
          ))}
        </div>
      </div>
    </div>
  );
}
