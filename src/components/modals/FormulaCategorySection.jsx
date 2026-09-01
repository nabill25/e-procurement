import { useState, useEffect, useCallback } from 'react';
import { Plus, Trash2, Calculator } from 'lucide-react';
import { getAuthHeaders, useApp, API_BASE } from '../../context/AppContext';
import { toast } from '../../lib/toast';

const SUITABILITY_OPTIONS = [
  { value: '', label: '-' },
  { value: 'S', label: 'S (Sesuai)' },
  { value: 'R', label: 'R (Relevan)' },
  { value: 'TS', label: 'TS (Tidak Sesuai)' },
];

// Bagian evaluasi khusus untuk kategori yang punya rumus otomatis (Personil, Peralatan,
// Sertifikat Lain) - meniru persis hitungPersonil()/hitungPeralatan()/hitungSertifikat()
// di eproc/lib/eproc/allfunc.js (sistem lama).
export default function FormulaCategorySection({ tenderId, vendorId, category, criteriaList }) {
  const [maxScore, setMaxScore] = useState('');
  const [items, setItems] = useState([]);
  const [result, setResult] = useState(null);
  const [newItem, setNewItem] = useState({});

  const fetchAll = useCallback(async () => {
    try {
      const [configRes, itemsRes, formulaRes] = await Promise.all([
        fetch(`${API_BASE}/tenders/${tenderId}/eval-category-config`, { headers: getAuthHeaders() }),
        fetch(`${API_BASE}/tenders/${tenderId}/eval-score-items/${vendorId}`, { headers: getAuthHeaders() }),
        fetch(`${API_BASE}/tenders/${tenderId}/eval-formula-score/${vendorId}/${category}`, { headers: getAuthHeaders() }),
      ]);
      const configJson = await configRes.json();
      const itemsJson = await itemsRes.json();
      const formulaJson = await formulaRes.json();

      if (configJson.success) {
        const cfg = configJson.data.find(c => c.category === category);
        setMaxScore(cfg ? cfg.max_score : '');
      }
      if (itemsJson.success) setItems(itemsJson.data.filter(it => it.category === category));
      if (formulaJson.success) setResult(formulaJson.data);
    } catch (err) {
      console.error(err);
    }
  }, [tenderId, vendorId, category]);

  useEffect(() => { fetchAll(); }, [fetchAll]);

  const handleSaveMaxScore = async () => {
    if (maxScore === '') return;
    try {
      await fetch(`${API_BASE}/tenders/${tenderId}/eval-category-config`, {
        method: 'POST', headers: getAuthHeaders(),
        body: JSON.stringify({ category, max_score: Number(maxScore) }),
      });
      fetchAll();
    } catch { toast('Gagal menyimpan nilai maksimal.'); }
  };

  const handleAddItem = async (criteriaId) => {
    const draft = newItem[criteriaId];
    if (!draft?.item_name?.trim()) return;
    try {
      const res = await fetch(`${API_BASE}/tenders/${tenderId}/eval-score-items`, {
        method: 'POST', headers: getAuthHeaders(),
        body: JSON.stringify({
          criteria_id: criteriaId,
          vendor_id: vendorId,
          item_name: draft.item_name,
          suitability: draft.suitability || null,
          suitability_value: draft.suitability_value ? Number(draft.suitability_value) : null,
          ownership_factor: category === 'peralatan' && draft.ownership_factor ? Number(draft.ownership_factor) : null,
        }),
      });
      const json = await res.json();
      if (json.success) {
        setNewItem({ ...newItem, [criteriaId]: {} });
        fetchAll();
      } else {
        toast('Gagal: ' + json.message);
      }
    } catch { toast('Terjadi kesalahan saat menambah item.'); }
  };

  const handleDeleteItem = async (itemId) => {
    if (!confirm('Hapus item ini?')) return;
    try {
      const res = await fetch(`${API_BASE}/tenders/${tenderId}/eval-score-items/${itemId}`, {
        method: 'DELETE', headers: getAuthHeaders(),
      });
      const json = await res.json();
      if (json.success) fetchAll();
      else toast('Gagal: ' + json.message);
    } catch { toast('Terjadi kesalahan saat menghapus item.'); }
  };

  return (
    <div className="space-y-3 bg-amber-50/50 border border-amber-200 rounded-xl p-4">
      <div className="flex items-center justify-between">
        <p className="text-xs font-semibold text-amber-800 flex items-center gap-1.5">
          <Calculator size={13} /> Kategori ini pakai rumus otomatis (mengikuti sistem lama)
        </p>
        <div className="flex items-center gap-1.5">
          <label className="text-[10px] text-muted">Nilai Maks Kategori:</label>
          <input type="number" value={maxScore} onChange={e => setMaxScore(e.target.value)} onBlur={handleSaveMaxScore}
            className="w-16 text-xs p-1 border border-gray-300 rounded" />
        </div>
      </div>

      {criteriaList.map(crit => {
        const critItems = items.filter(it => it.criteria_id === crit.criteria_id);
        const draft = newItem[crit.criteria_id] || {};
        return (
          <div key={crit.criteria_id} className="bg-white border border-border rounded-lg p-3">
            <p className="text-xs font-bold text-dpbj-navy mb-2">
              {crit.name} (bobot {crit.weight}%{category === 'personil' && crit.required_count ? `, butuh ${crit.required_count} orang` : ''})
            </p>
            <div className="space-y-1.5">
              {critItems.map(it => (
                <div key={it.id} className="flex items-center gap-2 text-xs bg-surface p-1.5 rounded">
                  <span className="flex-1">{it.item_name}</span>
                  <span className="text-muted">{it.suitability || '-'}{it.suitability_value != null ? ` (${it.suitability_value})` : ''}</span>
                  {category === 'peralatan' && it.ownership_factor != null && <span className="text-muted">Milik: {it.ownership_factor}%</span>}
                  <button onClick={() => handleDeleteItem(it.id)} className="text-red-400 hover:text-red-600"><Trash2 size={11} /></button>
                </div>
              ))}
            </div>
            <div className="flex items-center gap-1.5 mt-2">
              <input
                placeholder="Nama item"
                value={draft.item_name || ''}
                onChange={e => setNewItem({ ...newItem, [crit.criteria_id]: { ...draft, item_name: e.target.value } })}
                className="flex-1 text-[11px] p-1 border border-gray-300 rounded"
              />
              <select
                value={draft.suitability || ''}
                onChange={e => setNewItem({ ...newItem, [crit.criteria_id]: { ...draft, suitability: e.target.value } })}
                className="text-[11px] p-1 border border-gray-300 rounded"
              >
                {SUITABILITY_OPTIONS.map(o => <option key={o.value} value={o.value}>{o.label}</option>)}
              </select>
              {(!draft.suitability || draft.suitability === 'R') && (
                <input
                  type="number" placeholder="Nilai"
                  value={draft.suitability_value || ''}
                  onChange={e => setNewItem({ ...newItem, [crit.criteria_id]: { ...draft, suitability_value: e.target.value } })}
                  className="w-14 text-[11px] p-1 border border-gray-300 rounded"
                />
              )}
              {category === 'peralatan' && (
                <input
                  type="number" placeholder="% Milik"
                  value={draft.ownership_factor || ''}
                  onChange={e => setNewItem({ ...newItem, [crit.criteria_id]: { ...draft, ownership_factor: e.target.value } })}
                  className="w-16 text-[11px] p-1 border border-gray-300 rounded"
                />
              )}
              <button onClick={() => handleAddItem(crit.criteria_id)} className="p-1 bg-dpbj-navy text-white rounded">
                <Plus size={12} />
              </button>
            </div>
          </div>
        );
      })}

      {result && (
        <div className="bg-emerald-50 border border-emerald-200 rounded-lg p-3 text-xs">
          <p className="font-bold text-emerald-800">Nilai Akhir Kategori Ini: {result.final_score} (dari maksimal {result.max_score})</p>
          <p className="text-emerald-700 mt-1">Total pemenuhan: {result.total_prosentase}%</p>
        </div>
      )}
    </div>
  );
}
