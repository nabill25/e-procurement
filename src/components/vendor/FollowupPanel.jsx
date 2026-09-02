import { useState, useEffect, useCallback } from 'react';
import { ChevronDown, ChevronUp, Send, CheckCircle2, AlertCircle, Clock } from 'lucide-react';
import { API_BASE, getAuthHeaders, useApp } from '../../context/AppContext';
import { toast } from '../../lib/toast';

// Panel "Tindak Lanjut Kelengkapan Dokumen" - padanan fitur yang dirancang untuk sistem lama
// (folder root project 2026-09-01_pencatatan-tindak-lanjut-verifikasi-penyedia/, belum pernah
// dipasang di eproc production). Melacak bolak-balik verifikator <-> penyedia soal kelengkapan
// dokumen registrasi. Dipakai di 2 tempat dengan mode berbeda:
//   mode="verifikator" - di VendorDetailModal, verifikator kirim catatan / tandai selesai
//   mode="penyedia"    - di VendorProfile, penyedia lihat catatan + konfirmasi sudah lengkapi
// vendorId di sini SELALU vendors.id (bukan users.id), konsisten dengan endpoint backend-nya.

const STATUS_META = {
  perlu_dilengkapi: { label: 'Perlu Dilengkapi', className: 'bg-red-100 text-red-700' },
  sudah_dilengkapi: { label: 'Sudah Dilengkapi', className: 'bg-blue-100 text-blue-700' },
  terverifikasi:    { label: 'Terverifikasi',    className: 'bg-emerald-100 text-emerald-700' },
};

const JENIS_LABEL = {
  permintaan: 'Permintaan kelengkapan',
  konfirmasi: 'Konfirmasi penyedia',
  reminder:   'Pengingat',
  selesai:    'Dinyatakan selesai',
};

const PIHAK_LABEL = { verifikator: 'Verifikator', penyedia: 'Penyedia', sistem: 'Sistem' };

function umurSingkat(tanggal) {
  if (!tanggal) return '';
  const selisih = Date.now() - new Date(tanggal).getTime();
  if (selisih < 3600000) return Math.floor(selisih / 60000) + ' menit lalu';
  if (selisih < 86400000) return Math.floor(selisih / 3600000) + ' jam lalu';
  return Math.floor(selisih / 86400000) + ' hari lalu';
}

function Timeline({ items }) {
  if (!items.length) {
    return <p className="text-sm text-muted text-center py-6">Belum ada riwayat tindak lanjut untuk penyedia ini.</p>;
  }
  return (
    <div className="space-y-3 max-h-72 overflow-y-auto pr-1">
      {items.map(row => {
        const meta = STATUS_META[row.status] || { label: row.status, className: 'bg-gray-100 text-gray-600' };
        const pelaku = row.pihak === 'verifikator' && row.created_by_name ? row.created_by_name : (PIHAK_LABEL[row.pihak] || row.pihak);
        return (
          <div key={row.id} className="border-b border-border pb-3 last:border-b-0 last:pb-0">
            <div className="flex items-center flex-wrap gap-2">
              <span className={`px-2 py-0.5 rounded-md text-[10px] font-bold uppercase ${meta.className}`}>{meta.label}</span>
              <span className="text-[11px] text-muted">
                {JENIS_LABEL[row.jenis] || row.jenis} &middot; oleh {pelaku} &middot; {new Date(row.created_at).toLocaleString('id-ID')}
              </span>
            </div>
            {row.catatan && <p className="text-sm text-dpbj-navy mt-1.5 italic">&ldquo;{row.catatan}&rdquo;</p>}
            {row.email_tujuan && (
              row.email_terkirim
                ? <p className="text-[10px] text-emerald-600 mt-1">Email terkirim ke {row.email_tujuan}</p>
                : <p className="text-[10px] text-red-500 mt-1">Email ke {row.email_tujuan} belum terkirim (catatan tetap tersimpan)</p>
            )}
          </div>
        );
      })}
    </div>
  );
}

export default function FollowupPanel({ vendorId, mode }) {
  const { user } = useApp();
  const [data, setData] = useState(null);
  const [expanded, setExpanded] = useState(mode === 'penyedia');
  const [catatan, setCatatan] = useState('');
  const [saving, setSaving] = useState(null); // 'kirim' | 'selesai' | 'konfirmasi' | null

  const fetchData = useCallback(async () => {
    if (!vendorId) return;
    try {
      const res = await fetch(`${API_BASE}/vendors/${vendorId}/followup`, { headers: getAuthHeaders() });
      const json = await res.json();
      if (json.success) setData(json.data);
    } catch (err) {
      console.error('Gagal memuat tindak lanjut:', err);
    }
  }, [vendorId]);

  useEffect(() => { fetchData(); }, [fetchData]);

  const isVerifier = ['admin', 'approval_vms'].includes(user?.role);

  const kirim = async (url, successMsg, needCatatan) => {
    if (needCatatan && !catatan.trim()) { toast('Catatan wajib diisi.'); return; }
    setSaving(url);
    try {
      const res = await fetch(`${API_BASE}/vendors/${vendorId}${url}`, {
        method: 'POST',
        headers: getAuthHeaders(),
        body: JSON.stringify({ catatan: catatan.trim() }),
      });
      const json = await res.json();
      if (json.success) {
        toast(json.message || successMsg);
        setCatatan('');
        fetchData();
      } else {
        toast('Gagal: ' + json.message);
      }
    } catch {
      toast('Terjadi kesalahan saat menghubungi server.');
    } finally {
      setSaving(null);
    }
  };

  if (!data) return null;

  // Mode penyedia: cuma tampil kalau memang ada permintaan yang belum dijawab.
  if (mode === 'penyedia') {
    if (data.status !== 'perlu_dilengkapi') return null;
    return (
      <div className="bg-white border-2 border-red-200 rounded-2xl overflow-hidden mb-6 animate-fade-in">
        <div className="px-5 py-3.5 bg-red-50 flex items-center gap-2">
          <AlertCircle size={18} className="text-red-600 shrink-0" />
          <h3 className="font-bold text-red-800 text-sm">Verifikator meminta Anda melengkapi dokumen</h3>
        </div>
        <div className="p-5 space-y-4">
          <div className="bg-amber-50 border border-amber-200 rounded-xl px-4 py-3 text-sm text-amber-900">
            Catatan verifikator: <span className="italic">&ldquo;{data.catatan_terakhir}&rdquo;</span>
          </div>
          <Timeline items={data.timeline} />
          <div>
            <label className="block text-xs font-semibold text-muted mb-1">Keterangan singkat (opsional)</label>
            <textarea
              rows={2}
              className="form-input w-full"
              placeholder="Contoh: NPWP terbaru dan akta perubahan sudah saya upload ulang."
              value={catatan}
              onChange={e => setCatatan(e.target.value)}
            />
          </div>
          <button
            onClick={() => { if (confirm('Kirim konfirmasi bahwa dokumen sudah Anda lengkapi?')) kirim('/followup/confirm', 'Konfirmasi tersimpan.', false); }}
            disabled={saving === '/followup/confirm'}
            className="btn-primary w-full sm:w-auto justify-center disabled:opacity-50"
          >
            <CheckCircle2 size={16} /> {saving === '/followup/confirm' ? 'Mengirim...' : 'Sudah Saya Lengkapi'}
          </button>
          <p className="text-[11px] text-muted">Setelah diklik, verifikator akan menerima pemberitahuan untuk memeriksa ulang dokumen Anda.</p>
        </div>
      </div>
    );
  }

  // Mode verifikator: panel bisa dibuka/tutup, cuma tampil kalau memang ada peran untuk lihat
  // (semua staf yang bisa membuka modal ini), tombol aksi cuma untuk admin/approval_vms.
  const meta = data.ada ? (STATUS_META[data.status] || { label: data.status, className: 'bg-gray-100 text-gray-600' }) : null;

  return (
    <div className="border border-border rounded-xl mt-4 overflow-hidden">
      <button
        type="button"
        onClick={() => setExpanded(v => !v)}
        className="w-full flex items-center justify-between gap-3 px-4 py-3 bg-surface hover:bg-dpbj-navy/5 transition-colors text-left"
      >
        <div className="flex items-center gap-2 flex-wrap">
          <span className="font-bold text-dpbj-navy text-sm">Tindak Lanjut Kelengkapan Dokumen</span>
          {data.ada ? (
            <span className={`px-2 py-0.5 rounded-md text-[10px] font-bold uppercase ${meta.className}`}>{meta.label}</span>
          ) : (
            <span className="px-2 py-0.5 rounded-md text-[10px] font-bold uppercase bg-gray-100 text-gray-500">Belum Ada Tindak Lanjut</span>
          )}
          {data.ada && data.sejak && (
            <span className="text-[11px] text-muted flex items-center gap-1">
              <Clock size={11} /> sejak {umurSingkat(data.sejak)}, sudah {data.follow_up_count}x diingatkan
            </span>
          )}
        </div>
        {expanded ? <ChevronUp size={16} className="text-muted shrink-0" /> : <ChevronDown size={16} className="text-muted shrink-0" />}
      </button>

      {expanded && (
        <div className="p-4 space-y-4 animate-fade-in">
          <Timeline items={data.timeline} />

          {isVerifier && (
            <>
              <hr className="border-border" />
              <div>
                <label className="block text-xs font-semibold text-muted mb-1">Catatan untuk penyedia (jelaskan dokumen apa yang kurang)</label>
                <textarea
                  rows={3}
                  className="form-input w-full"
                  placeholder="Contoh: NPWP yang diupload sudah kedaluwarsa, mohon upload NPWP terbaru. Akta perubahan terakhir juga belum ada."
                  value={catatan}
                  onChange={e => setCatatan(e.target.value)}
                />
              </div>
              <div className="flex flex-col sm:flex-row gap-2">
                <button
                  onClick={() => kirim('/followup/request', 'Catatan tersimpan.', true)}
                  disabled={!!saving}
                  className="btn-primary justify-center disabled:opacity-50"
                >
                  <Send size={14} /> {saving === '/followup/request' ? 'Mengirim...' : 'Kirim Catatan & Email ke Penyedia'}
                </button>
                <button
                  onClick={() => { if (confirm('Yakin dokumen penyedia ini sudah lengkap?')) kirim('/followup/complete', 'Dokumen ditandai terverifikasi.', false); }}
                  disabled={!!saving}
                  className="flex items-center justify-center gap-2 px-4 py-2 text-sm font-semibold rounded-xl bg-emerald-500 text-white hover:bg-emerald-600 transition-colors disabled:opacity-50"
                >
                  <CheckCircle2 size={14} /> {saving === '/followup/complete' ? 'Menyimpan...' : 'Tandai Dokumen Sudah Lengkap'}
                </button>
              </div>
              <p className="text-[11px] text-muted">
                "Kirim Catatan" mengubah status jadi <b>Perlu Dilengkapi</b> dan mengirim email otomatis ke penyedia.
                "Tandai Dokumen Sudah Lengkap" hanya menutup catatan tindak lanjut, tidak menggantikan tombol Verifikasi/Blokir/Tangguhkan.
              </p>
            </>
          )}
        </div>
      )}
    </div>
  );
}
