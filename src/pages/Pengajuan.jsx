import { useState, useEffect, useCallback } from 'react';
import { Plus, Search, Eye, Calendar, CheckCircle, SendHorizonal, XCircle, RefreshCw } from 'lucide-react';
import { useApp, API_BASE, getAuthHeaders } from '../context/AppContext';
import { statusConfig } from '../data/mockData';
import { StatusBadge, formatRupiah } from '../components/ui/shared';

export default function Pengajuan() {
  const { openNewProcurementModal, refreshTrigger, triggerRefresh, setSelectedPengajuan, user } = useApp();
  const [requests, setRequests] = useState([]);
  const [isLoading, setIsLoading] = useState(true);
  const [actionLoading, setActionLoading] = useState(null); // ID yang sedang diproses

  const fetchRequests = useCallback(async () => {
    setIsLoading(true);
    try {
      const res = await fetch(`${API_BASE}/pengajuan`, { headers: getAuthHeaders() });
      const json = await res.json();
      if (json.success) {
        setRequests(json.data);
      }
    } catch (err) {
      console.error('Failed to fetch pengajuan:', err);
    } finally {
      setIsLoading(false);
    }
  }, []);

  useEffect(() => {
    fetchRequests();
  }, [fetchRequests, refreshTrigger]);

  // ── Submit pengajuan: draft → proses_review ──────────────────────────────────
  // Mengikuti alur eProc: posting = 1 (pengajuan dikirim untuk direview)
  const handleSubmit = async (id, title) => {
    if (!confirm(`Submit pengajuan "${title}" untuk direview?`)) return;
    setActionLoading(id);
    try {
      const res = await fetch(`${API_BASE}/pengajuan/${id}/submit`, {
        method: 'POST',
        headers: getAuthHeaders(),
      });
      const json = await res.json();
      if (json.success) {
        alert(json.message);
        triggerRefresh();
      } else {
        alert('Gagal: ' + json.message);
      }
    } catch {
      alert('Terjadi kesalahan server.');
    } finally {
      setActionLoading(null);
    }
  };

  // ── ACC pengajuan: proses_review → disetujui + buat Tender ──────────────────
  // Mengikuti alur eProc: approval = 1 (persetujuan, buat paket tender)
  const handleApprove = async (id, title) => {
    if (!confirm(`ACC pengajuan "${title}" dan jadikan Tender?`)) return;
    setActionLoading(id);
    try {
      const res = await fetch(`${API_BASE}/pengajuan/${id}/approve`, {
        method: 'POST',
        headers: getAuthHeaders(),
      });
      const json = await res.json();
      if (json.success) {
        alert(json.message);
        triggerRefresh();
      } else {
        alert('Gagal: ' + json.message);
      }
    } catch {
      alert('Terjadi kesalahan server.');
    } finally {
      setActionLoading(null);
    }
  };

  // ── Tolak pengajuan: proses_review → ditolak ────────────────────────────────
  // Mengikuti alur eProc: approval = 0 atau kembali ke draft
  const handleReject = async (id, title) => {
    const reason = prompt(`Alasan penolakan untuk "${title}":`, '');
    if (reason === null) return; // user cancel
    setActionLoading(id);
    try {
      const res = await fetch(`${API_BASE}/pengajuan/${id}/reject`, {
        method: 'POST',
        headers: getAuthHeaders(),
        body: JSON.stringify({ reason }),
      });
      const json = await res.json();
      if (json.success) {
        alert(json.message);
        triggerRefresh();
      } else {
        alert('Gagal: ' + json.message);
      }
    } catch {
      alert('Terjadi kesalahan server.');
    } finally {
      setActionLoading(null);
    }
  };

  // ── Simulasi Integrasi SAP ERP ────────────────────────────────────────────
  const handleSAPSync = async () => {
    setActionLoading('sap-sync');
    try {
      const res = await fetch(`${API_BASE}/pengajuan/sap-sync`, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', ...getAuthHeaders() },
        body: JSON.stringify({ requester_id: user.id })
      });
      const json = await res.json();
      if (json.success) {
        alert(json.message);
        triggerRefresh();
      } else {
        alert('Gagal tarik data SAP: ' + json.message);
      }
    } catch {
      alert('Terjadi kesalahan server saat sinkronisasi SAP.');
    } finally {
      setActionLoading(null);
    }
  };

  // Tentukan aksi yang tersedia berdasarkan status dan role
  // Mengikuti alur eProc: hanya admin/ppk yang bisa ACC, pemilik yang bisa submit
  const isAdmin    = user?.role === 'admin';
  const isPPK      = user?.role === 'ppk';
  const canApprove = isAdmin; // hanya admin yang bisa ACC (seperti eProc)
  const canReject  = isAdmin;

  return (
    <div className="space-y-6 animate-fade-in">
      <div className="flex items-center justify-between">
        <div>
          <h2 className="text-lg font-bold text-dpbj-navy">Daftar Pengajuan Pengadaan</h2>
          <p className="text-xs text-muted">{requests.length} pengajuan terdaftar · TA 2025</p>
        </div>
        <div className="flex gap-2">
          {user?.role === 'ppk' && (
            <button 
              onClick={handleSAPSync} 
              disabled={actionLoading === 'sap-sync'}
              className="btn-secondary flex items-center gap-2 bg-emerald-50 text-emerald-700 border-emerald-200 hover:bg-emerald-100"
            >
              <RefreshCw size={15} className={actionLoading === 'sap-sync' ? "animate-spin" : ""} />
              Tarik Data SAP PR
            </button>
          )}
          <button onClick={openNewProcurementModal} className="btn-primary">
            <Plus size={15} />
            Pengajuan Baru
          </button>
        </div>
      </div>

      {/* Legenda status alur eProc */}
      <div className="flex flex-wrap gap-2 text-[10px]">
        {[
          { label: 'Draft', desc: '(posting=0)', color: 'bg-gray-100 text-gray-600' },
          { label: '→ Submit', desc: '(posting=1)', color: 'bg-amber-100 text-amber-700' },
          { label: '→ ACC', desc: '(approval=1)', color: 'bg-emerald-100 text-emerald-700' },
          { label: '→ Tender', desc: 'dibuat otomatis', color: 'bg-blue-100 text-blue-700' },
        ].map(s => (
          <span key={s.label} className={`px-2 py-0.5 rounded-full font-medium ${s.color}`}>
            {s.label} <span className="opacity-60">{s.desc}</span>
          </span>
        ))}
      </div>

      <div className="section-card">
        {/* Search */}
        <div className="flex items-center gap-2 bg-surface border border-border rounded-xl px-3 py-2 mb-4 w-64 focus-within:border-dpbj-gold transition-all">
          <Search size={13} className="text-gray-400" />
          <input className="bg-transparent text-sm text-dpbj-navy placeholder:text-gray-400 focus:outline-none w-full" placeholder="Cari pengajuan..." />
        </div>

        <div className="overflow-x-auto rounded-xl border border-border">
          <table className="data-table">
            <thead>
              <tr>
                <th>No. Pengajuan</th>
                <th>Judul Pengajuan</th>
                <th>Unit Kerja</th>
                <th>Estimasi Nilai</th>
                <th>Status</th>
                <th>Tanggal</th>
                <th>Aksi</th>
              </tr>
            </thead>
            <tbody>
              {isLoading ? (
                <tr><td colSpan={7} className="py-12 text-center text-muted text-sm">Memuat data...</td></tr>
              ) : requests.length === 0 ? (
                <tr><td colSpan={7} className="py-12 text-center text-muted text-sm">Tidak ada data.</td></tr>
              ) : requests.map(req => (
                <tr key={req.id} className={req.is_from_sap ? "bg-emerald-50/30" : ""}>
                  <td>
                    <span className="font-mono text-xs font-semibold text-dpbj-slate">{req.request_number}</span>
                    {req.is_from_sap && (
                      <div className="mt-1">
                        <span className="text-[10px] bg-emerald-100 text-emerald-800 px-1.5 py-0.5 rounded font-mono border border-emerald-200">
                          SAP: {req.sap_pr_number}
                        </span>
                      </div>
                    )}
                  </td>
                  <td>
                    <div className="max-w-xs">
                      <p className="font-semibold text-sm text-dpbj-navy leading-snug">{req.title}</p>
                      <p className="text-xs text-muted">TA {req.fiscal_year}</p>
                    </div>
                  </td>
                  <td><span className="text-xs text-dpbj-navy">{req.unit_kerja}</span></td>
                  <td><span className="text-sm font-semibold text-dpbj-navy">{formatRupiah(req.estimated_value, true)}</span></td>
                  <td><StatusBadge status={req.status} config={statusConfig} /></td>
                  <td>
                    <div className="flex items-center gap-1.5 text-xs text-muted">
                      <Calendar size={11} />
                      {req.created_at ? new Date(req.created_at).toLocaleDateString('id-ID') : '-'}
                    </div>
                  </td>
                  <td>
                    <div className="flex items-center gap-1">
                      {/* Submit: hanya untuk status draft (mengikuti eProc posting=1) */}
                      {req.status === 'draft' && (
                        <button
                          onClick={() => handleSubmit(req.id, req.title)}
                          disabled={actionLoading === req.id}
                          className="p-1.5 rounded-lg text-amber-600 hover:bg-amber-50 transition-colors disabled:opacity-50"
                          title="Submit untuk Review"
                        >
                          <SendHorizonal size={14} />
                        </button>
                      )}

                      {/* ACC: hanya admin, untuk status draft atau proses_review (mengikuti eProc approval=1) */}
                      {canApprove && (req.status === 'draft' || req.status === 'proses_review') && (
                        <button
                          onClick={() => handleApprove(req.id, req.title)}
                          disabled={actionLoading === req.id}
                          className="p-1.5 rounded-lg text-emerald-600 hover:bg-emerald-50 transition-colors disabled:opacity-50"
                          title="ACC (Setujui menjadi Tender)"
                        >
                          <CheckCircle size={14} />
                        </button>
                      )}

                      {/* Tolak: hanya admin, untuk status proses_review */}
                      {canReject && req.status === 'proses_review' && (
                        <button
                          onClick={() => handleReject(req.id, req.title)}
                          disabled={actionLoading === req.id}
                          className="p-1.5 rounded-lg text-red-500 hover:bg-red-50 transition-colors disabled:opacity-50"
                          title="Tolak Pengajuan"
                        >
                          <XCircle size={14} />
                        </button>
                      )}

                      {/* Lihat Detail */}
                      <button
                        onClick={() => setSelectedPengajuan(req)}
                        className="p-1.5 rounded-lg hover:bg-dpbj-gold-faint hover:text-dpbj-gold-dark transition-colors"
                        title="Lihat Detail"
                      >
                        <Eye size={14} />
                      </button>
                    </div>
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
