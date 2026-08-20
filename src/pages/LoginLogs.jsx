import { useState, useEffect, useCallback } from 'react';
import { History, CheckCircle2, XCircle } from 'lucide-react';
import { API_BASE, useApp } from '../context/AppContext';

function formatTanggal(iso) {
  if (!iso) return '-';
  return new Date(iso).toLocaleString('id-ID', { dateStyle: 'medium', timeStyle: 'short' });
}

export default function LoginLogs() {
  const { getAuthHeaders } = useApp();
  const [logs, setLogs] = useState([]);
  const [isLoading, setIsLoading] = useState(true);

  const fetchLogs = useCallback(async () => {
    setIsLoading(true);
    try {
      const res = await fetch(`${API_BASE}/users/login-logs`, { headers: getAuthHeaders() });
      const json = await res.json();
      if (json.success) setLogs(json.data);
    } catch (err) {
      console.error(err);
    } finally {
      setIsLoading(false);
    }
  }, []);

  useEffect(() => { fetchLogs(); }, [fetchLogs]);

  return (
    <div className="space-y-6 animate-fade-in">
      <div className="section-card">
        <div className="flex items-center gap-3 mb-5">
          <div className="w-10 h-10 rounded-xl bg-surface flex items-center justify-center">
            <History size={20} className="text-dpbj-navy" />
          </div>
          <div>
            <h2 className="text-base font-bold text-dpbj-navy">Riwayat Login</h2>
            <p className="text-xs text-muted">Catatan setiap kali ada yang login ke sistem, dari akun mana dan perangkat apa. 200 catatan terbaru.</p>
          </div>
        </div>

        <div className="overflow-x-auto rounded-xl border border-border">
          <table className="data-table">
            <thead>
              <tr>
                <th>Nama</th>
                <th>Username</th>
                <th>Alamat IP</th>
                <th>Perangkat / Browser</th>
                <th>Waktu Login</th>
                <th>Waktu Logout</th>
                <th>Status</th>
              </tr>
            </thead>
            <tbody>
              {isLoading ? (
                <tr><td colSpan={7} className="py-10 text-center text-muted text-sm">Memuat data...</td></tr>
              ) : logs.length === 0 ? (
                <tr><td colSpan={7} className="py-10 text-center text-muted text-sm">Belum ada catatan login.</td></tr>
              ) : logs.map(l => (
                <tr key={l.id}>
                  <td className="text-sm font-medium text-dpbj-navy">{l.full_name || '-'}</td>
                  <td className="text-xs text-muted">{l.username}</td>
                  <td className="text-xs text-muted">{l.ip_address || '-'}</td>
                  <td className="text-xs text-muted max-w-xs truncate" title={l.user_agent}>{l.user_agent || '-'}</td>
                  <td className="text-xs text-muted">{formatTanggal(l.login_at)}</td>
                  <td className="text-xs text-muted">{formatTanggal(l.logout_at)}</td>
                  <td>
                    {l.is_active ? (
                      <span className="badge text-[10px] bg-emerald-100 text-emerald-700 flex items-center gap-1 w-fit"><CheckCircle2 size={11} /> Aktif</span>
                    ) : (
                      <span className="badge text-[10px] bg-gray-100 text-gray-500 flex items-center gap-1 w-fit"><XCircle size={11} /> Selesai</span>
                    )}
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
