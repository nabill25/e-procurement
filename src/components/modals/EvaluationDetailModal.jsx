import { useState, useEffect, useCallback } from 'react';
import { X, ClipboardCheck, Plus, Trash2, CheckCircle2, XCircle } from 'lucide-react';
import { useApp, API_BASE } from '../../context/AppContext';
import FormulaCategorySection from './FormulaCategorySection';
import clsx from 'clsx';

const FORMULA_CATEGORIES = ['personil', 'peralatan', 'sertifikat_lain'];

const CATEGORIES = [
  { id: 'administrasi',     label: 'Administrasi' },
  { id: 'teknis',           label: 'Teknis' },
  { id: 'harga',            label: 'Harga' },
  { id: 'kualifikasi',      label: 'Kualifikasi' },
  { id: 'personil',         label: 'Personil' },
  { id: 'peralatan',        label: 'Peralatan' },
  { id: 'sertifikat_lain',  label: 'Sertifikat Lain' },
  { id: 'pengalaman',       label: 'Pengalaman' },
  { id: 'syarat_daftar',    label: 'Syarat Pendaftaran' },
];

export default function EvaluationDetailModal({ isOpen, onClose, tenderId, vendor }) {
  const { user, getAuthHeaders } = useApp();
  const [criteria, setCriteria] = useState([]);
  const [scores, setScores] = useState({});
  const [isLoading, setIsLoading] = useState(true);
  const [newCriteria, setNewCriteria] = useState({ category: 'administrasi', name: '', is_mandatory: true, weight: '' });
  const [adding, setAdding] = useState(false);

  const fetchData = useCallback(async () => {
    if (!tenderId || !vendor) return;
    setIsLoading(true);
    try {
      const res = await fetch(`${API_BASE}/tenders/${tenderId}/eval-scores/${vendor.vendor_id}`, { headers: getAuthHeaders() });
      const json = await res.json();
      if (json.success) {
        setCriteria(json.data);
        const scoreMap = {};
        json.data.forEach(c => {
          scoreMap[c.criteria_id] = { meets_requirement: c.meets_requirement, score: c.score ?? '', notes: c.notes || '' };
        });
        setScores(scoreMap);
      }
    } catch (err) {
      console.error(err);
    } finally {
      setIsLoading(false);
    }
  }, [tenderId, vendor]);

  useEffect(() => { if (isOpen) fetchData(); }, [isOpen, fetchData]);

  if (!isOpen || !vendor) return null;

  const handleAddCriteria = async (e) => {
    e.preventDefault();
    if (!newCriteria.name.trim()) return;
    setAdding(true);
    try {
      const res = await fetch(`${API_BASE}/tenders/${tenderId}/eval-criteria`, {
        method: 'POST',
        headers: getAuthHeaders(),
        body: JSON.stringify({ ...newCriteria, weight: newCriteria.weight ? Number(newCriteria.weight) : null }),
      });
      const json = await res.json();
      if (json.success) {
        setNewCriteria({ category: 'administrasi', name: '', is_mandatory: true, weight: '' });
        fetchData();
      } else {
        alert('Gagal: ' + json.message);
      }
    } catch {
      alert('Terjadi kesalahan saat menambah kriteria.');
    } finally {
      setAdding(false);
    }
  };

  const handleDeleteCriteria = async (criteriaId) => {
    if (!confirm('Hapus kriteria ini? Ini akan berlaku untuk semua vendor di tender ini.')) return;
    try {
      const res = await fetch(`${API_BASE}/tenders/${tenderId}/eval-criteria/${criteriaId}`, {
        method: 'DELETE', headers: getAuthHeaders(),
      });
      const json = await res.json();
      if (json.success) fetchData();
      else alert('Gagal: ' + json.message);
    } catch {
      alert('Terjadi kesalahan saat menghapus kriteria.');
    }
  };

  const handleSaveScore = async (criteriaId, meetsRequirement) => {
    const s = scores[criteriaId] || {};
    try {
      const res = await fetch(`${API_BASE}/tenders/${tenderId}/eval-scores`, {
        method: 'POST',
        headers: getAuthHeaders(),
        body: JSON.stringify({
          criteria_id: criteriaId,
          vendor_id: vendor.vendor_id,
          meets_requirement: meetsRequirement,
          score: s.score !== '' ? Number(s.score) : null,
          notes: s.notes,
          scored_by: user.id,
        }),
      });
      const json = await res.json();
      if (json.success) fetchData();
      else alert('Gagal: ' + json.message);
    } catch {
      alert('Terjadi kesalahan saat menyimpan skor.');
    }
  };

  const grouped = CATEGORIES.map(cat => ({
    ...cat,
    items: criteria.filter(c => c.category === cat.id),
  })).filter(cat => cat.items.length > 0);

  return (
    <div className="fixed inset-0 z-[60] flex items-center justify-center p-4 bg-dpbj-navy/50 backdrop-blur-sm animate-fade-in">
      <div className="bg-white rounded-2xl shadow-xl w-full max-w-3xl overflow-hidden flex flex-col max-h-[90vh]">
        <div className="flex items-center justify-between p-5 border-b border-border bg-surface">
          <div>
            <h2 className="text-lg font-bold text-dpbj-navy flex items-center gap-2">
              <ClipboardCheck size={18} /> Evaluasi Detail per Kategori
            </h2>
            <p className="text-xs text-muted font-mono">{vendor.company_name}</p>
          </div>
          <button onClick={onClose} className="p-2 text-muted hover:bg-white rounded-xl transition-colors border border-transparent hover:border-border">
            <X size={18} />
          </button>
        </div>

        <div className="flex-1 overflow-y-auto p-6 space-y-6">
          {isLoading ? (
            <p className="text-sm text-muted text-center py-10">Memuat data...</p>
          ) : (
            <>
              {grouped.length === 0 ? (
                <p className="text-sm text-muted text-center py-6">Belum ada kriteria evaluasi untuk tender ini. Tambahkan di bawah.</p>
              ) : grouped.map(cat => (
                <div key={cat.id}>
                  <h3 className="font-bold text-dpbj-navy text-sm mb-3">{cat.label}</h3>
                  {FORMULA_CATEGORIES.includes(cat.id) ? (
                    <FormulaCategorySection tenderId={tenderId} vendorId={vendor.vendor_id} category={cat.id} criteriaList={cat.items} />
                  ) : (
                  <div className="space-y-2">
                    {cat.items.map(item => {
                      const s = scores[item.criteria_id] || { score: '', notes: '' };
                      return (
                        <div key={item.criteria_id} className="border border-border rounded-xl p-3">
                          <div className="flex items-center justify-between gap-2 mb-2">
                            <p className="text-sm font-semibold text-dpbj-navy">
                              {item.name} {item.is_mandatory && <span className="text-red-500 text-xs">*wajib</span>}
                            </p>
                            <button onClick={() => handleDeleteCriteria(item.criteria_id)} className="p-1 text-red-400 hover:bg-red-50 rounded">
                              <Trash2 size={12} />
                            </button>
                          </div>
                          <div className="flex items-center gap-2">
                            <input
                              type="number"
                              placeholder="Skor"
                              value={s.score}
                              onChange={e => setScores({ ...scores, [item.criteria_id]: { ...s, score: e.target.value } })}
                              className="w-24 text-xs p-1.5 border border-gray-300 rounded-lg"
                            />
                            <input
                              type="text"
                              placeholder="Catatan (opsional)"
                              value={s.notes}
                              onChange={e => setScores({ ...scores, [item.criteria_id]: { ...s, notes: e.target.value } })}
                              className="flex-1 text-xs p-1.5 border border-gray-300 rounded-lg"
                            />
                            <button onClick={() => handleSaveScore(item.criteria_id, true)} className={clsx('p-1.5 rounded-lg', item.meets_requirement === true ? 'bg-emerald-500 text-white' : 'bg-emerald-50 text-emerald-600 hover:bg-emerald-100')}>
                              <CheckCircle2 size={14} />
                            </button>
                            <button onClick={() => handleSaveScore(item.criteria_id, false)} className={clsx('p-1.5 rounded-lg', item.meets_requirement === false ? 'bg-red-500 text-white' : 'bg-red-50 text-red-600 hover:bg-red-100')}>
                              <XCircle size={14} />
                            </button>
                          </div>
                        </div>
                      );
                    })}
                  </div>
                  )}
                </div>
              ))}

              <form onSubmit={handleAddCriteria} className="border-t border-border pt-4 space-y-3">
                <p className="text-xs font-semibold text-muted uppercase tracking-wide">Tambah Kriteria Baru</p>
                <div className="grid grid-cols-2 gap-3">
                  <select
                    value={newCriteria.category}
                    onChange={e => setNewCriteria({ ...newCriteria, category: e.target.value })}
                    className="text-sm p-2 border border-gray-300 rounded-lg"
                  >
                    {CATEGORIES.map(c => <option key={c.id} value={c.id}>{c.label}</option>)}
                  </select>
                  <input
                    type="text"
                    placeholder="Nama kriteria"
                    value={newCriteria.name}
                    onChange={e => setNewCriteria({ ...newCriteria, name: e.target.value })}
                    required
                    className="text-sm p-2 border border-gray-300 rounded-lg"
                  />
                </div>
                <div className="flex items-center gap-3">
                  <label className="flex items-center gap-1.5 text-xs text-dpbj-navy">
                    <input type="checkbox" checked={newCriteria.is_mandatory} onChange={e => setNewCriteria({ ...newCriteria, is_mandatory: e.target.checked })} />
                    Wajib
                  </label>
                  <input
                    type="number"
                    placeholder="Bobot % (opsional)"
                    value={newCriteria.weight}
                    onChange={e => setNewCriteria({ ...newCriteria, weight: e.target.value })}
                    className="w-32 text-xs p-1.5 border border-gray-300 rounded-lg"
                  />
                  {newCriteria.category === 'personil' && (
                    <input
                      type="number"
                      placeholder="Jml orang dibutuhkan"
                      value={newCriteria.required_count || ''}
                      onChange={e => setNewCriteria({ ...newCriteria, required_count: e.target.value })}
                      className="w-36 text-xs p-1.5 border border-gray-300 rounded-lg"
                    />
                  )}
                  <button type="submit" disabled={adding} className="btn-secondary text-xs flex items-center gap-1.5 ml-auto disabled:opacity-50">
                    <Plus size={13} /> Tambah
                  </button>
                </div>
              </form>
            </>
          )}
        </div>
      </div>
    </div>
  );
}
