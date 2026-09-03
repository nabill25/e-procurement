import { useState, useEffect, useCallback } from 'react';
import { ChevronDown, ChevronUp, CheckCircle2, Circle, Save } from 'lucide-react';
import { API_BASE, getAuthHeaders, useApp } from '../../context/AppContext';
import { toast } from '../../lib/toast';

// Panel "Checklist Verifikasi Kelengkapan Berkas" - padanan REKANAN_CHECKLIST di eProc lama
// (dikonfirmasi aktif dipakai lewat rekanan_json.php fungsi updateChecklist()/updateChecklist2(),
// dipanggil dari 18 halaman verifikasi berbeda; data asli berisi catatan verifikator sungguhan).
// Di sistem baru disederhanakan mengikuti 10 tab yang sudah ada di halaman Profil & Kualifikasi
// Vendor (bukan 19 field terpisah seperti sistem lama). Dipakai di 2 tempat dengan mode berbeda:
//   mode="verifikator" - di VendorDetailModal, verifikator centang "Ya, Lengkap" + catatan per bagian
//   mode="penyedia"    - di VendorProfile, penyedia lihat status + catatan tiap bagian (baca saja)
// vendorId di sini SELALU users.id (vendor_id di tabel checklist ini FK ke users, sama seperti
// vendor_documents/qualifications - BUKAN vendors.id seperti FollowupPanel).

export default function VendorChecklistPanel({ vendorId, mode }) {
  const { user } = useApp();
  const [rows, setRows] = useState(null);
  const [expanded, setExpanded] = useState(mode === 'verifikator');
  const [draft, setDraft] = useState({}); // { [section]: { is_complete, catatan } } - perubahan belum disimpan
  const [saving, setSaving] = useState(null); // section yang sedang disimpan

  const fetchData = useCallback(async () => {
    if (!vendorId) return;
    try {
      const res = await fetch(`${API_BASE}/vendors/${vendorId}/checklist`, { headers: getAuthHeaders() });
      const json = await res.json();
      if (json.success) setRows(json.data);
    } catch (err) {
      console.error('Gagal memuat checklist:', err);
    }
  }, [vendorId]);

  useEffect(() => { fetchData(); }, [fetchData]);

  const isVerifier = ['admin', 'approval_vms'].includes(user?.role);

  const setField = (section, field, value) => {
    setDraft(d => ({ ...d, [section]: { ...(d[section] || {}), [field]: value } }));
  };

  const save = async (row) => {
    const d = draft[row.section] || {};
    const is_complete = d.is_complete !== undefined ? d.is_complete : row.is_complete;
    const catatan = d.catatan !== undefined ? d.catatan : (row.catatan || '');
    setSaving(row.section);
    try {
      const res = await fetch(`${API_BASE}/vendors/${vendorId}/checklist`, {
        method: 'POST',
        headers: getAuthHeaders(),
        body: JSON.stringify({ section: row.section, is_complete, catatan }),
      });
      const json = await res.json();
      if (json.success) {
        toast('Checklist bagian "' + row.label + '" tersimpan.');
        setDraft(d2 => { const n = { ...d2 }; delete n[row.section]; return n; });
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

  if (!rows) return null;

  const jumlahLengkap = rows.filter(r => r.is_complete).length;
  const sudahDireview = rows.some(r => r.checked_at);

  // Mode penyedia: baca saja, cuma tampilkan bagian yang sudah pernah dicatatkan verifikator
  // supaya tidak bingung kalau memang belum pernah direview sama sekali.
  if (mode === 'penyedia') {
    if (!sudahDireview) return null;
    return (
      <div className="border border-border rounded-xl mb-6 overflow-hidden animate-fade-in">
        <button
          type="button"
          onClick={() => setExpanded(v => !v)}
          className="w-full flex items-center justify-between gap-3 px-4 py-3 bg-surface hover:bg-dpbj-navy/5 transition-colors text-left"
        >
          <div className="flex items-center gap-2 flex-wrap">
            <span className="font-bold text-dpbj-navy text-sm">Checklist Verifikasi Kelengkapan Berkas</span>
            <span className={`px-2 py-0.5 rounded-md text-[10px] font-bold uppercase ${jumlahLengkap === rows.length ? 'bg-emerald-100 text-emerald-700' : 'bg-amber-100 text-amber-700'}`}>
              {jumlahLengkap}/{rows.length} Lengkap
            </span>
          </div>
          {expanded ? <ChevronUp size={16} className="text-muted shrink-0" /> : <ChevronDown size={16} className="text-muted shrink-0" />}
        </button>
        {expanded && (
          <div className="p-4 space-y-2 animate-fade-in">
            {rows.map(row => (
              <div key={row.section} className="flex items-start gap-2.5 py-1.5">
                {row.is_complete ? <CheckCircle2 size={16} className="text-emerald-500 shrink-0 mt-0.5" /> : <Circle size={16} className="text-gray-300 shrink-0 mt-0.5" />}
                <div>
                  <p className={`text-sm font-semibold ${row.is_complete ? 'text-dpbj-navy' : 'text-red-600'}`}>{row.label}</p>
                  {row.catatan && <p className="text-xs text-muted mt-0.5 italic">&ldquo;{row.catatan}&rdquo;</p>}
                </div>
              </div>
            ))}
          </div>
        )}
      </div>
    );
  }

  // Mode verifikator: 10 baris, tiap baris punya checkbox + catatan + tombol simpan sendiri
  // (mengikuti pola sistem lama - satu bagian disimpan terpisah dari bagian lain).
  return (
    <div className="border border-border rounded-xl mt-4 overflow-hidden">
      <button
        type="button"
        onClick={() => setExpanded(v => !v)}
        className="w-full flex items-center justify-between gap-3 px-4 py-3 bg-surface hover:bg-dpbj-navy/5 transition-colors text-left"
      >
        <div className="flex items-center gap-2 flex-wrap">
          <span className="font-bold text-dpbj-navy text-sm">Checklist Verifikasi Kelengkapan Berkas</span>
          <span className={`px-2 py-0.5 rounded-md text-[10px] font-bold uppercase ${jumlahLengkap === rows.length ? 'bg-emerald-100 text-emerald-700' : 'bg-gray-100 text-gray-500'}`}>
            {jumlahLengkap}/{rows.length} Lengkap
          </span>
        </div>
        {expanded ? <ChevronUp size={16} className="text-muted shrink-0" /> : <ChevronDown size={16} className="text-muted shrink-0" />}
      </button>

      {expanded && (
        <div className="p-4 space-y-3 animate-fade-in">
          {rows.map(row => {
            const d = draft[row.section] || {};
            const isComplete = d.is_complete !== undefined ? d.is_complete : row.is_complete;
            const catatan = d.catatan !== undefined ? d.catatan : (row.catatan || '');
            const dirty = draft[row.section] !== undefined;
            return (
              <div key={row.section} className="border border-border rounded-lg p-3 bg-surface">
                <div className="flex items-start justify-between gap-3">
                  <label className="flex items-center gap-2 cursor-pointer select-none">
                    <input
                      type="checkbox"
                      className="w-4 h-4 rounded accent-dpbj-gold"
                      checked={isComplete}
                      disabled={!isVerifier}
                      onChange={e => setField(row.section, 'is_complete', e.target.checked)}
                    />
                    <span className="text-sm font-semibold text-dpbj-navy">{row.label}</span>
                  </label>
                  {row.checked_by_name && (
                    <span className="text-[10px] text-muted shrink-0">
                      oleh {row.checked_by_name}{row.checked_at ? ' · ' + new Date(row.checked_at).toLocaleDateString('id-ID') : ''}
                    </span>
                  )}
                </div>
                {isVerifier ? (
                  <div className="flex flex-col sm:flex-row gap-2 mt-2">
                    <input
                      type="text"
                      className="form-input flex-1 text-xs"
                      placeholder="Catatan (opsional, mis. bagian mana yang perlu diperbaiki)"
                      value={catatan}
                      onChange={e => setField(row.section, 'catatan', e.target.value)}
                    />
                    <button
                      onClick={() => save(row)}
                      disabled={!dirty || saving === row.section}
                      className="btn-secondary text-xs shrink-0 disabled:opacity-40"
                    >
                      <Save size={12} /> {saving === row.section ? 'Menyimpan...' : 'Simpan'}
                    </button>
                  </div>
                ) : (
                  row.catatan && <p className="text-xs text-muted mt-1.5 italic ml-6">&ldquo;{row.catatan}&rdquo;</p>
                )}
              </div>
            );
          })}
        </div>
      )}
    </div>
  );
}
