import { useState, useEffect, useCallback } from 'react';
import { Shield, CheckCircle2, XCircle, Download, MoveHorizontal } from 'lucide-react';
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
      <div className="grid grid-cols-3 gap-4">
        {[
          { label: 'Total Log Hari Ini',  value: logs.length, icon: Shield,       color: 'text-dpbj-navy'    },
          { label: 'Aktivitas Berhasil',  value: logs.filter(l => l.is_success).length,  icon: CheckCircle2, color: 'text-emerald-600' },
          { label: 'Aktivitas Gagal',     value: logs.filter(l => !l.is_success).length, icon: XCircle,     color: 'text-red-500'     },
        ].map(({ label, value, icon: Icon, color }) => (
          <div key={label} className="section-card flex items-center gap-4">
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
          <button className="btn-ghost text-xs">
            <Download size={13} />
            Export Log
          </button>
        </div>

        <p className="table-scroll-hint">
          <MoveHorizontal size={13} /> Geser tabel ke kiri/kanan untuk lihat kolom lainnya
        </p>
        <div className="table-scroll">
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
            <tbody>
              {isLoading ? (
                <tr><td colSpan={7} className="py-12 text-center text-muted text-sm">Memuat data...</td></tr>
              ) : logs.length === 0 ? (
                <tr><td colSpan={7} className="py-12 text-center text-muted text-sm">Tidak ada log.</td></tr>
              ) : logs.map(log => (
                <tr key={log.id}>
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
      </div>
    </div>
  );
}
