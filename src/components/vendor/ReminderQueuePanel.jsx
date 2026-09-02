import { useState, useEffect, useCallback } from 'react';
import { ChevronDown, ChevronUp, BellRing, Send } from 'lucide-react';
import { API_BASE, getAuthHeaders, useApp } from '../../context/AppContext';
import { toast } from '../../lib/toast';

// Padanan "cron pengingat kelengkapan dokumen" dari rancangan fitur Tindak Lanjut untuk sistem
// lama (lihat komentar lengkap di server/routes/vendors.js). Sistem baru ini tidak punya cron
// OS aktif, jadi pengingat dipicu manual dari sini oleh admin/approval_vms, satu-satu atau
// sekaligus, ke vendor yang statusnya masih "perlu dilengkapi" dan sudah diam beberapa hari.
// Cuma dirender kalau yang login admin/approval_vms (dicek di Vendor.jsx sebelum memanggil ini).
export default function ReminderQueuePanel() {
  const { user } = useApp();
  const [expanded, setExpanded] = useState(false);
  const [queue, setQueue] = useState([]);
  const [setting, setSetting] = useState(null);
  const [loading, setLoading] = useState(false);
  const [sendingId, setSendingId] = useState(null);

  const fetchAll = useCallback(async () => {
    setLoading(true);
    try {
      const [qRes, sRes] = await Promise.all([
        fetch(`${API_BASE}/vendors/followup-reminder-queue`, { headers: getAuthHeaders() }),
        fetch(`${API_BASE}/master/settings/reminder_tindak_lanjut_vendor`, { headers: getAuthHeaders() }),
      ]);
      const qJson = await qRes.json();
      const sJson = await sRes.json();
      if (qJson.success) setQueue(qJson.data);
      if (sJson.success) setSetting(sJson.data);
    } catch (err) {
      console.error('Gagal memuat antrian pengingat:', err);
    } finally {
      setLoading(false);
    }
  }, []);

  useEffect(() => { if (expanded) fetchAll(); }, [expanded, fetchAll]);

  const toggleSetting = async () => {
    try {
      const res = await fetch(`${API_BASE}/master/settings/reminder_tindak_lanjut_vendor`, {
        method: 'PATCH',
        headers: getAuthHeaders(),
        body: JSON.stringify({ aktif: !setting?.aktif, updated_by: user?.id }),
      });
      const json = await res.json();
      if (json.success) { toast(json.message); setSetting(json.data); }
      else toast('Gagal: ' + json.message);
    } catch {
      toast('Terjadi kesalahan saat menghubungi server.');
    }
  };

  const kirimSatu = async (vendorId) => {
    setSendingId(vendorId);
    try {
      const res = await fetch(`${API_BASE}/vendors/${vendorId}/followup/remind`, {
        method: 'POST', headers: getAuthHeaders(),
      });
      const json = await res.json();
      if (json.success) { toast(json.message); fetchAll(); }
      else toast('Gagal: ' + json.message);
    } catch {
      toast('Terjadi kesalahan saat menghubungi server.');
    } finally {
      setSendingId(null);
    }
  };

  return (
    <div className="section-card">
      <button
        type="button"
        onClick={() => setExpanded(v => !v)}
        className="w-full flex items-center justify-between gap-3 text-left"
      >
        <div className="flex items-center gap-2">
          <BellRing size={16} className="text-dpbj-gold-dark" />
          <h2 className="text-base font-bold text-dpbj-navy">Pengingat Kelengkapan Dokumen</h2>
          {queue.length > 0 && <span className="badge badge-cancel">{queue.length} menunggu</span>}
        </div>
        {expanded ? <ChevronUp size={16} className="text-muted" /> : <ChevronDown size={16} className="text-muted" />}
      </button>

      {expanded && (
        <div className="mt-4 animate-fade-in">
          <div className="flex items-center justify-between gap-3 flex-wrap p-3 bg-surface rounded-xl border border-border mb-4">
            <p className="text-xs text-muted max-w-md">
              Daftar vendor berstatus "Perlu Dilengkapi" yang belum menanggapi permintaan verifikator
              selama lebih dari 7 hari (maksimal 3x pengingat per vendor).
            </p>
            {setting && (
              <label className="flex items-center gap-2 text-xs font-semibold text-dpbj-navy cursor-pointer shrink-0">
                <input type="checkbox" checked={!!setting.aktif} onChange={toggleSetting} className="w-4 h-4 accent-dpbj-gold" />
                Fitur pengingat otomatis
              </label>
            )}
          </div>

          {loading ? (
            <p className="text-sm text-muted text-center py-6">Memuat...</p>
          ) : queue.length === 0 ? (
            <p className="text-sm text-muted text-center py-6">Tidak ada vendor yang perlu diingatkan saat ini.</p>
          ) : (
            <div className="space-y-2">
              {queue.map(row => (
                <div key={row.vendor_id} className="flex items-center justify-between gap-3 p-3 border border-border rounded-xl">
                  <div className="min-w-0">
                    <p className="font-semibold text-sm text-dpbj-navy truncate">{row.company_name}</p>
                    <p className="text-xs text-muted truncate">
                      Diam {row.hari_diam} hari &middot; sudah {row.jumlah_reminder}x diingatkan &middot; {row.catatan}
                    </p>
                  </div>
                  <button
                    onClick={() => kirimSatu(row.vendor_id)}
                    disabled={sendingId === row.vendor_id}
                    className="btn-secondary shrink-0 text-xs py-1.5 px-3 disabled:opacity-50"
                  >
                    <Send size={12} /> {sendingId === row.vendor_id ? 'Mengirim...' : 'Kirim Pengingat'}
                  </button>
                </div>
              ))}
            </div>
          )}
        </div>
      )}
    </div>
  );
}
