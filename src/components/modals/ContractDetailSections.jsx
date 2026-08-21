import { useState, useEffect, useCallback } from 'react';
import { CreditCard, AlertOctagon, ListChecks, Plus } from 'lucide-react';
import { useApp, API_BASE, getAuthHeaders } from '../../context/AppContext';
import { formatRupiah } from '../ui/shared';
import clsx from 'clsx';

const PAYMENT_STATUS = {
  belum_dibayar: { label: 'Belum Dibayar', className: 'bg-gray-100 text-gray-600' },
  diajukan:      { label: 'Diajukan',      className: 'bg-amber-100 text-amber-700' },
  dibayar:       { label: 'Dibayar',       className: 'bg-emerald-100 text-emerald-700' },
};

export function PaymentTermsSection({ tenderId, canEdit }) {
  const [terms, setTerms] = useState([]);
  const [showForm, setShowForm] = useState(false);
  const [form, setForm] = useState({ term_name: '', amount: '', progress_percent: '' });

  const fetchTerms = useCallback(async () => {
    try {
      const res = await fetch(`${API_BASE}/tenders/${tenderId}/contract/payment-terms`, { headers: getAuthHeaders() });
      const json = await res.json();
      if (json.success) setTerms(json.data);
    } catch (err) { console.error(err); }
  }, [tenderId]);

  useEffect(() => { fetchTerms(); }, [fetchTerms]);

  const handleAdd = async (e) => {
    e.preventDefault();
    if (!form.term_name.trim() || !form.amount) return;
    try {
      const res = await fetch(`${API_BASE}/tenders/${tenderId}/contract/payment-terms`, {
        method: 'POST', headers: getAuthHeaders(), body: JSON.stringify(form),
      });
      const json = await res.json();
      if (json.success) { setForm({ term_name: '', amount: '', progress_percent: '' }); setShowForm(false); fetchTerms(); }
      else alert('Gagal: ' + json.message);
    } catch { alert('Terjadi kesalahan.'); }
  };

  const handleMarkPaid = async (id) => {
    if (!confirm('Tandai termin ini sudah dibayar?')) return;
    try {
      const res = await fetch(`${API_BASE}/tenders/${tenderId}/contract/payment-terms/${id}`, {
        method: 'PATCH',
        headers: (() => { const h = getAuthHeaders(); delete h['Content-Type']; return h; })(),
        body: (() => { const f = new FormData(); f.append('status', 'dibayar'); f.append('payment_date', new Date().toISOString().slice(0, 10)); return f; })(),
      });
      const json = await res.json();
      if (json.success) fetchTerms(); else alert('Gagal: ' + json.message);
    } catch { alert('Terjadi kesalahan.'); }
  };

  return (
    <div className="border border-border rounded-xl p-5 bg-white shadow-sm space-y-3">
      <div className="flex items-center justify-between">
        <h4 className="font-bold text-sm text-dpbj-navy flex items-center gap-2">
          <CreditCard size={16} className="text-dpbj-gold" /> Termin Pembayaran
        </h4>
        {canEdit && (
          <button onClick={() => setShowForm(!showForm)} className="text-xs font-semibold text-dpbj-navy flex items-center gap-1">
            <Plus size={13} /> Tambah Termin
          </button>
        )}
      </div>

      {showForm && (
        <form onSubmit={handleAdd} className="flex items-end gap-2 bg-surface p-3 rounded-xl">
          <input placeholder="Nama Termin (mis. Termin 1)" value={form.term_name} onChange={e => setForm({ ...form, term_name: e.target.value })} className="flex-1 text-xs p-2 border border-gray-300 rounded-lg" />
          <input type="number" placeholder="Nilai (Rp)" value={form.amount} onChange={e => setForm({ ...form, amount: e.target.value })} className="w-32 text-xs p-2 border border-gray-300 rounded-lg" />
          <input type="number" placeholder="Progres (%)" value={form.progress_percent} onChange={e => setForm({ ...form, progress_percent: e.target.value })} className="w-24 text-xs p-2 border border-gray-300 rounded-lg" />
          <button type="submit" className="btn-secondary text-xs">Simpan</button>
        </form>
      )}

      {terms.length === 0 ? (
        <p className="text-xs text-muted text-center py-4">Belum ada termin pembayaran.</p>
      ) : (
        <div className="space-y-2">
          {terms.map(t => {
            const cfg = PAYMENT_STATUS[t.status] || PAYMENT_STATUS.belum_dibayar;
            return (
              <div key={t.id} className="flex items-center justify-between p-3 bg-surface rounded-lg text-sm">
                <div>
                  <p className="font-semibold text-dpbj-navy">{t.term_name} {t.progress_percent ? `(${t.progress_percent}%)` : ''}</p>
                  <p className="text-xs text-muted">{formatRupiah(t.amount, true)}</p>
                </div>
                <div className="flex items-center gap-2">
                  <span className={clsx('badge text-[10px]', cfg.className)}>{cfg.label}</span>
                  {canEdit && t.status !== 'dibayar' && (
                    <button onClick={() => handleMarkPaid(t.id)} className="text-[10px] font-bold text-emerald-600 hover:underline">Tandai Dibayar</button>
                  )}
                </div>
              </div>
            );
          })}
        </div>
      )}
    </div>
  );
}

export function PenaltiesSection({ tenderId, canEdit }) {
  const [penalties, setPenalties] = useState([]);
  const [showForm, setShowForm] = useState(false);
  const [form, setForm] = useState({ days_late: '', penalty_rate: '1 permil/hari', work_value: '', penalty_amount: '', notes: '' });

  const fetchPenalties = useCallback(async () => {
    try {
      const res = await fetch(`${API_BASE}/tenders/${tenderId}/contract/penalties`, { headers: getAuthHeaders() });
      const json = await res.json();
      if (json.success) setPenalties(json.data);
    } catch (err) { console.error(err); }
  }, [tenderId]);

  useEffect(() => { fetchPenalties(); }, [fetchPenalties]);

  const handleAdd = async (e) => {
    e.preventDefault();
    if (!form.days_late) return;
    try {
      const res = await fetch(`${API_BASE}/tenders/${tenderId}/contract/penalties`, {
        method: 'POST', headers: getAuthHeaders(), body: JSON.stringify(form),
      });
      const json = await res.json();
      if (json.success) { setForm({ days_late: '', penalty_rate: '1 permil/hari', work_value: '', penalty_amount: '', notes: '' }); setShowForm(false); fetchPenalties(); }
      else alert('Gagal: ' + json.message);
    } catch { alert('Terjadi kesalahan.'); }
  };

  return (
    <div className="border border-border rounded-xl p-5 bg-white shadow-sm space-y-3">
      <div className="flex items-center justify-between">
        <h4 className="font-bold text-sm text-dpbj-navy flex items-center gap-2">
          <AlertOctagon size={16} className="text-red-500" /> Sanksi Keterlambatan
        </h4>
        {canEdit && (
          <button onClick={() => setShowForm(!showForm)} className="text-xs font-semibold text-dpbj-navy flex items-center gap-1">
            <Plus size={13} /> Catat Sanksi
          </button>
        )}
      </div>

      {showForm && (
        <form onSubmit={handleAdd} className="grid grid-cols-2 gap-2 bg-surface p-3 rounded-xl">
          <input type="number" placeholder="Hari terlambat" value={form.days_late} onChange={e => setForm({ ...form, days_late: e.target.value })} className="text-xs p-2 border border-gray-300 rounded-lg" />
          <input placeholder="Tarif denda (mis. 1 permil/hari)" value={form.penalty_rate} onChange={e => setForm({ ...form, penalty_rate: e.target.value })} className="text-xs p-2 border border-gray-300 rounded-lg" />
          <input type="number" placeholder="Nilai pekerjaan (Rp)" value={form.work_value} onChange={e => setForm({ ...form, work_value: e.target.value })} className="text-xs p-2 border border-gray-300 rounded-lg" />
          <input type="number" placeholder="Nilai denda (Rp)" value={form.penalty_amount} onChange={e => setForm({ ...form, penalty_amount: e.target.value })} className="text-xs p-2 border border-gray-300 rounded-lg" />
          <input placeholder="Catatan" value={form.notes} onChange={e => setForm({ ...form, notes: e.target.value })} className="col-span-2 text-xs p-2 border border-gray-300 rounded-lg" />
          <button type="submit" className="btn-secondary text-xs col-span-2">Simpan</button>
        </form>
      )}

      {penalties.length === 0 ? (
        <p className="text-xs text-muted text-center py-4">Belum ada sanksi yang tercatat.</p>
      ) : (
        <div className="space-y-2">
          {penalties.map(p => (
            <div key={p.id} className="p-3 bg-red-50 border border-red-100 rounded-lg text-sm">
              <p className="font-semibold text-red-800">Terlambat {p.days_late} hari &middot; {p.penalty_rate}</p>
              {p.penalty_amount && <p className="text-xs text-red-600 mt-0.5">Denda: {formatRupiah(p.penalty_amount, true)}</p>}
              {p.notes && <p className="text-xs text-muted mt-1">{p.notes}</p>}
            </div>
          ))}
        </div>
      )}
    </div>
  );
}

const DELIVERABLE_STATUS = {
  proses:    { label: 'Proses',    className: 'bg-blue-100 text-blue-700' },
  selesai:   { label: 'Selesai',   className: 'bg-emerald-100 text-emerald-700' },
  terlambat: { label: 'Terlambat', className: 'bg-red-100 text-red-700' },
};

export function DeliverablesSection({ tenderId, canEdit }) {
  const [items, setItems] = useState([]);
  const [showForm, setShowForm] = useState(false);
  const [form, setForm] = useState({ scope: '', deliverable_name: '', target_date: '' });

  const fetchItems = useCallback(async () => {
    try {
      const res = await fetch(`${API_BASE}/tenders/${tenderId}/contract/deliverables`, { headers: getAuthHeaders() });
      const json = await res.json();
      if (json.success) setItems(json.data);
    } catch (err) { console.error(err); }
  }, [tenderId]);

  useEffect(() => { fetchItems(); }, [fetchItems]);

  const handleAdd = async (e) => {
    e.preventDefault();
    if (!form.deliverable_name.trim()) return;
    try {
      const res = await fetch(`${API_BASE}/tenders/${tenderId}/contract/deliverables`, {
        method: 'POST', headers: getAuthHeaders(), body: JSON.stringify(form),
      });
      const json = await res.json();
      if (json.success) { setForm({ scope: '', deliverable_name: '', target_date: '' }); setShowForm(false); fetchItems(); }
      else alert('Gagal: ' + json.message);
    } catch { alert('Terjadi kesalahan.'); }
  };

  const handleUpdateProgress = async (id, progress, status) => {
    try {
      const res = await fetch(`${API_BASE}/tenders/${tenderId}/contract/deliverables/${id}`, {
        method: 'PATCH',
        headers: (() => { const h = getAuthHeaders(); delete h['Content-Type']; return h; })(),
        body: (() => { const f = new FormData(); f.append('progress_percent', progress); f.append('status', status); return f; })(),
      });
      const json = await res.json();
      if (json.success) fetchItems(); else alert('Gagal: ' + json.message);
    } catch { alert('Terjadi kesalahan.'); }
  };

  return (
    <div className="border border-border rounded-xl p-5 bg-white shadow-sm space-y-3">
      <div className="flex items-center justify-between">
        <h4 className="font-bold text-sm text-dpbj-navy flex items-center gap-2">
          <ListChecks size={16} className="text-blue-600" /> Progres Pekerjaan
        </h4>
        {canEdit && (
          <button onClick={() => setShowForm(!showForm)} className="text-xs font-semibold text-dpbj-navy flex items-center gap-1">
            <Plus size={13} /> Tambah Item
          </button>
        )}
      </div>

      {showForm && (
        <form onSubmit={handleAdd} className="grid grid-cols-2 gap-2 bg-surface p-3 rounded-xl">
          <input placeholder="Lingkup Pekerjaan" value={form.scope} onChange={e => setForm({ ...form, scope: e.target.value })} className="text-xs p-2 border border-gray-300 rounded-lg" />
          <input placeholder="Nama Deliverable" value={form.deliverable_name} onChange={e => setForm({ ...form, deliverable_name: e.target.value })} className="text-xs p-2 border border-gray-300 rounded-lg" />
          <input type="date" value={form.target_date} onChange={e => setForm({ ...form, target_date: e.target.value })} className="text-xs p-2 border border-gray-300 rounded-lg col-span-2" />
          <button type="submit" className="btn-secondary text-xs col-span-2">Simpan</button>
        </form>
      )}

      {items.length === 0 ? (
        <p className="text-xs text-muted text-center py-4">Belum ada item progres pekerjaan.</p>
      ) : (
        <div className="space-y-2">
          {items.map(item => {
            const cfg = DELIVERABLE_STATUS[item.status] || DELIVERABLE_STATUS.proses;
            return (
              <div key={item.id} className="p-3 bg-surface rounded-lg text-sm">
                <div className="flex items-center justify-between">
                  <p className="font-semibold text-dpbj-navy">{item.deliverable_name}</p>
                  <span className={clsx('badge text-[10px]', cfg.className)}>{cfg.label}</span>
                </div>
                {item.scope && <p className="text-xs text-muted">{item.scope}</p>}
                <div className="flex items-center gap-2 mt-2">
                  <div className="flex-1 h-1.5 bg-gray-200 rounded-full overflow-hidden">
                    <div className="h-full bg-dpbj-gold" style={{ width: `${item.progress_percent || 0}%` }} />
                  </div>
                  <span className="text-[10px] text-muted">{item.progress_percent || 0}%</span>
                </div>
                {canEdit && item.status !== 'selesai' && (
                  <div className="flex items-center gap-2 mt-2">
                    <input
                      type="number" min="0" max="100" placeholder="Update %"
                      className="w-20 text-[10px] p-1 border border-gray-300 rounded"
                      onKeyDown={e => {
                        if (e.key === 'Enter') {
                          const val = Number(e.target.value);
                          handleUpdateProgress(item.id, val, val >= 100 ? 'selesai' : 'proses');
                        }
                      }}
                    />
                    <span className="text-[9px] text-muted">tekan Enter untuk simpan</span>
                  </div>
                )}
              </div>
            );
          })}
        </div>
      )}
    </div>
  );
}
