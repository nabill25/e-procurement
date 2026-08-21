import { useState, useEffect, useCallback } from 'react';
import { Save, Plus, Trash2, Upload, Download, FileSignature, ShieldCheck, Wrench, Package, FileEdit, MessageSquare, Bell, Paperclip, AlertTriangle, UserCog } from 'lucide-react';
import { getAuthHeaders, API_BASE, useApp } from '../../context/AppContext';
import { formatRupiah } from '../ui/shared';
import clsx from 'clsx';

function Section({ icon: Icon, title, children, tone = 'navy' }) {
  const toneClass = tone === 'amber' ? 'bg-amber-50 border-amber-200' : 'bg-white border-border';
  return (
    <div className={clsx('border rounded-xl overflow-hidden', toneClass)}>
      <div className="flex items-center gap-2 p-3 border-b border-border bg-surface">
        <Icon size={15} className="text-dpbj-navy" />
        <h4 className="font-bold text-dpbj-navy text-xs">{title}</h4>
      </div>
      <div className="p-4">{children}</div>
    </div>
  );
}

// ── SPPBJ + SPK/PKS detail form ──
export function SppbjSpkSection({ tenderId, contract, canEdit, refreshContract }) {
  const [sppbj, setSppbj] = useState({});
  const [spk, setSpk] = useState({});
  const [saving, setSaving] = useState(false);

  useEffect(() => {
    if (!contract) return;
    setSppbj({
      sppbj_code: contract.sppbj_code || '', sppbj_date: contract.sppbj_date?.split('T')[0] || '',
      sppbj_nilai: contract.sppbj_nilai || '', sppbj_direktur_nama: contract.sppbj_direktur_nama || '',
      sppbj_pejabat_berwenang: contract.sppbj_pejabat_berwenang || '', sppbj_jaminan_pelaksana: contract.sppbj_jaminan_pelaksana || '',
      sppbj_jaminan_persen: contract.sppbj_jaminan_persen || '', is_non_sppbj: contract.is_non_sppbj || false,
    });
    setSpk({
      spk_code: contract.spk_code || '', jenis_pekerjaan: contract.jenis_pekerjaan || '',
      pihak1_nama: contract.pihak1_nama || '', pihak2_nama: contract.pihak2_nama || '',
      lingkup_pekerjaan: contract.lingkup_pekerjaan || '', dokumen_jenis: contract.dokumen_jenis || 'spk',
      waktu_pelaksanaan_dari: contract.waktu_pelaksanaan_dari?.split('T')[0] || '',
      waktu_pelaksanaan_sampai: contract.waktu_pelaksanaan_sampai?.split('T')[0] || '',
    });
  }, [contract]);

  const saveSppbj = async () => {
    setSaving(true);
    try {
      await fetch(`${API_BASE}/tenders/${tenderId}/contract/sppbj`, { method: 'PATCH', headers: getAuthHeaders(), body: JSON.stringify(sppbj) });
      refreshContract();
    } catch { alert('Gagal menyimpan SPPBJ.'); } finally { setSaving(false); }
  };

  const saveSpk = async () => {
    setSaving(true);
    try {
      await fetch(`${API_BASE}/tenders/${tenderId}/contract/spk-detail`, { method: 'PATCH', headers: getAuthHeaders(), body: JSON.stringify(spk) });
      refreshContract();
    } catch { alert('Gagal menyimpan detail SPK.'); } finally { setSaving(false); }
  };

  return (
    <div className="space-y-4">
      <Section icon={FileSignature} title="SPPBJ (Surat Penunjukan Penyedia Barang/Jasa)">
        {canEdit ? (
          <div className="space-y-3">
            <label className="flex items-center gap-1.5 text-xs text-dpbj-navy">
              <input type="checkbox" checked={sppbj.is_non_sppbj || false} onChange={e => setSppbj({ ...sppbj, is_non_sppbj: e.target.checked })} /> Non-SPPBJ (pengadaan langsung)
            </label>
            <div className="grid grid-cols-2 gap-3">
              <input placeholder="Kode SPPBJ" value={sppbj.sppbj_code || ''} onChange={e => setSppbj({ ...sppbj, sppbj_code: e.target.value })} className="text-xs p-2 border border-gray-300 rounded-lg" />
              <input type="date" value={sppbj.sppbj_date || ''} onChange={e => setSppbj({ ...sppbj, sppbj_date: e.target.value })} className="text-xs p-2 border border-gray-300 rounded-lg" />
              <input type="number" placeholder="Nilai SPPBJ" value={sppbj.sppbj_nilai || ''} onChange={e => setSppbj({ ...sppbj, sppbj_nilai: e.target.value })} className="text-xs p-2 border border-gray-300 rounded-lg" />
              <input placeholder="Nama Direktur" value={sppbj.sppbj_direktur_nama || ''} onChange={e => setSppbj({ ...sppbj, sppbj_direktur_nama: e.target.value })} className="text-xs p-2 border border-gray-300 rounded-lg" />
              <input placeholder="Pejabat Berwenang" value={sppbj.sppbj_pejabat_berwenang || ''} onChange={e => setSppbj({ ...sppbj, sppbj_pejabat_berwenang: e.target.value })} className="text-xs p-2 border border-gray-300 rounded-lg" />
              <select value={sppbj.sppbj_jaminan_pelaksana || ''} onChange={e => setSppbj({ ...sppbj, sppbj_jaminan_pelaksana: e.target.value })} className="text-xs p-2 border border-gray-300 rounded-lg">
                <option value="">Jaminan Pelaksanaan?</option>
                <option value="Ya">Ya</option>
                <option value="Tidak">Tidak</option>
              </select>
              <input type="number" placeholder="Persen Jaminan (%)" value={sppbj.sppbj_jaminan_persen || ''} onChange={e => setSppbj({ ...sppbj, sppbj_jaminan_persen: e.target.value })} className="text-xs p-2 border border-gray-300 rounded-lg" />
            </div>
            <button onClick={saveSppbj} disabled={saving} className="btn-secondary text-xs flex items-center gap-1 disabled:opacity-50"><Save size={12} /> Simpan SPPBJ</button>
          </div>
        ) : (
          <div className="grid grid-cols-2 gap-2 text-xs">
            <p><span className="text-muted">Kode:</span> {contract?.sppbj_code || '-'}</p>
            <p><span className="text-muted">Nilai:</span> {contract?.sppbj_nilai ? formatRupiah(contract.sppbj_nilai, true) : '-'}</p>
          </div>
        )}
      </Section>

      <Section icon={FileSignature} title="SPK / PKS (Dokumen Kontrak)">
        {canEdit ? (
          <div className="space-y-3">
            <select value={spk.dokumen_jenis || 'spk'} onChange={e => setSpk({ ...spk, dokumen_jenis: e.target.value })} className="text-xs p-2 border border-gray-300 rounded-lg">
              <option value="spk">SPK</option>
              <option value="surat_perjanjian">Surat Perjanjian</option>
            </select>
            <div className="grid grid-cols-2 gap-3">
              <input placeholder="Kode SPK/PKS" value={spk.spk_code || ''} onChange={e => setSpk({ ...spk, spk_code: e.target.value })} className="text-xs p-2 border border-gray-300 rounded-lg" />
              <input placeholder="Jenis Pekerjaan" value={spk.jenis_pekerjaan || ''} onChange={e => setSpk({ ...spk, jenis_pekerjaan: e.target.value })} className="text-xs p-2 border border-gray-300 rounded-lg" />
              <input placeholder="Pihak 1 (Pembeli)" value={spk.pihak1_nama || ''} onChange={e => setSpk({ ...spk, pihak1_nama: e.target.value })} className="text-xs p-2 border border-gray-300 rounded-lg" />
              <input placeholder="Pihak 2 (Penyedia)" value={spk.pihak2_nama || ''} onChange={e => setSpk({ ...spk, pihak2_nama: e.target.value })} className="text-xs p-2 border border-gray-300 rounded-lg" />
              <input type="date" value={spk.waktu_pelaksanaan_dari || ''} onChange={e => setSpk({ ...spk, waktu_pelaksanaan_dari: e.target.value })} className="text-xs p-2 border border-gray-300 rounded-lg" />
              <input type="date" value={spk.waktu_pelaksanaan_sampai || ''} onChange={e => setSpk({ ...spk, waktu_pelaksanaan_sampai: e.target.value })} className="text-xs p-2 border border-gray-300 rounded-lg" />
            </div>
            <textarea placeholder="Lingkup pekerjaan" value={spk.lingkup_pekerjaan || ''} onChange={e => setSpk({ ...spk, lingkup_pekerjaan: e.target.value })} className="text-xs p-2 border border-gray-300 rounded-lg w-full h-16" />
            <button onClick={saveSpk} disabled={saving} className="btn-secondary text-xs flex items-center gap-1 disabled:opacity-50"><Save size={12} /> Simpan SPK/PKS</button>
          </div>
        ) : (
          <div className="grid grid-cols-2 gap-2 text-xs">
            <p><span className="text-muted">Kode:</span> {contract?.spk_code || '-'}</p>
            <p><span className="text-muted">Jenis:</span> {contract?.jenis_pekerjaan || '-'}</p>
          </div>
        )}
      </Section>
    </div>
  );
}

// ── SPMK ──
export function SpmkSection({ tenderId, canEdit, user }) {
  const [items, setItems] = useState([]);
  const [form, setForm] = useState({ nomor: '', spmk_dari: '', spmk_sampai: '', keterangan: '' });

  const fetchItems = useCallback(async () => {
    const res = await fetch(`${API_BASE}/tenders/${tenderId}/contract/spmk`, { headers: getAuthHeaders() });
    const json = await res.json();
    if (json.success) setItems(json.data);
  }, [tenderId]);

  useEffect(() => { fetchItems(); }, [fetchItems]);

  const handleAdd = async () => {
    if (!form.nomor) return alert('Nomor SPMK wajib diisi.');
    await fetch(`${API_BASE}/tenders/${tenderId}/contract/spmk`, { method: 'POST', headers: getAuthHeaders(), body: JSON.stringify({ ...form, created_by: user.id }) });
    setForm({ nomor: '', spmk_dari: '', spmk_sampai: '', keterangan: '' });
    fetchItems();
  };

  return (
    <Section icon={FileSignature} title="SPMK (Surat Perintah Mulai Kerja)">
      {canEdit && (
        <div className="flex flex-wrap items-end gap-2 mb-3 bg-surface p-3 rounded-lg">
          <input placeholder="Nomor" value={form.nomor} onChange={e => setForm({ ...form, nomor: e.target.value })} className="text-xs p-2 border border-gray-300 rounded-lg flex-1 min-w-[100px]" />
          <input type="date" value={form.spmk_dari} onChange={e => setForm({ ...form, spmk_dari: e.target.value })} className="text-xs p-2 border border-gray-300 rounded-lg" />
          <input type="date" value={form.spmk_sampai} onChange={e => setForm({ ...form, spmk_sampai: e.target.value })} className="text-xs p-2 border border-gray-300 rounded-lg" />
          <button onClick={handleAdd} className="btn-secondary text-xs">Simpan</button>
        </div>
      )}
      {items.length === 0 ? <p className="text-xs text-muted text-center py-2">Belum ada SPMK.</p> : (
        <div className="space-y-1">
          {items.map(it => (
            <div key={it.id} className="text-xs bg-surface p-2 rounded-lg">
              <span className="font-bold text-dpbj-navy">{it.nomor}</span> {it.spmk_dari?.split('T')[0]} s/d {it.spmk_sampai?.split('T')[0]}
            </div>
          ))}
        </div>
      )}
    </Section>
  );
}

// ── Jaminan Pelaksanaan & Pemeliharaan ──
export function JaminanSection({ tenderId, canEdit, user }) {
  const [jaminan, setJaminan] = useState([]);
  const [jampel, setJampel] = useState([]);
  const [jForm, setJForm] = useState({ nomor: '', tanggal_jaminan: '' });
  const [jFile, setJFile] = useState(null);
  const [pForm, setPForm] = useState({ nomor: '', nilai: '', masa: '', tanggal_mulai: '', tanggal_akhir: '' });
  const [pFile, setPFile] = useState(null);

  const fetchAll = useCallback(async () => {
    const [j, p] = await Promise.all([
      fetch(`${API_BASE}/tenders/${tenderId}/contract/jaminan`, { headers: getAuthHeaders() }),
      fetch(`${API_BASE}/tenders/${tenderId}/contract/jaminan-pemeliharaan`, { headers: getAuthHeaders() }),
    ]);
    const [jj, pj] = await Promise.all([j.json(), p.json()]);
    if (jj.success) setJaminan(jj.data);
    if (pj.success) setJampel(pj.data);
  }, [tenderId]);

  useEffect(() => { fetchAll(); }, [fetchAll]);

  const submitJaminan = async () => {
    const fd = new FormData();
    Object.entries(jForm).forEach(([k, v]) => fd.append(k, v));
    fd.append('created_by', user.id);
    if (jFile) fd.append('file_jaminan', jFile);
    await fetch(`${API_BASE}/tenders/${tenderId}/contract/jaminan`, { method: 'POST', headers: { Authorization: `Bearer ${localStorage.getItem('dpbj_token')}` }, body: fd });
    setJForm({ nomor: '', tanggal_jaminan: '' }); setJFile(null); fetchAll();
  };

  const submitJampel = async () => {
    const fd = new FormData();
    Object.entries(pForm).forEach(([k, v]) => fd.append(k, v));
    fd.append('created_by', user.id);
    if (pFile) fd.append('file_jaminan', pFile);
    await fetch(`${API_BASE}/tenders/${tenderId}/contract/jaminan-pemeliharaan`, { method: 'POST', headers: { Authorization: `Bearer ${localStorage.getItem('dpbj_token')}` }, body: fd });
    setPForm({ nomor: '', nilai: '', masa: '', tanggal_mulai: '', tanggal_akhir: '' }); setPFile(null); fetchAll();
  };

  return (
    <div className="space-y-4">
      <Section icon={ShieldCheck} title="Jaminan Pelaksanaan">
        {canEdit && (
          <div className="flex flex-wrap items-end gap-2 mb-3 bg-surface p-3 rounded-lg">
            <input placeholder="Nomor" value={jForm.nomor} onChange={e => setJForm({ ...jForm, nomor: e.target.value })} className="text-xs p-2 border border-gray-300 rounded-lg flex-1 min-w-[100px]" />
            <input type="date" value={jForm.tanggal_jaminan} onChange={e => setJForm({ ...jForm, tanggal_jaminan: e.target.value })} className="text-xs p-2 border border-gray-300 rounded-lg" />
            <input type="file" onChange={e => setJFile(e.target.files[0])} className="text-xs" />
            <button onClick={submitJaminan} className="btn-secondary text-xs">Simpan</button>
          </div>
        )}
        {jaminan.length === 0 ? <p className="text-xs text-muted text-center py-2">Belum ada jaminan pelaksanaan.</p> : (
          <div className="space-y-1">
            {jaminan.map(j => (
              <div key={j.id} className="flex items-center justify-between text-xs bg-surface p-2 rounded-lg">
                <span>{j.nomor} - {j.tanggal_jaminan?.split('T')[0]} {j.status_konfirmasi && <span className="text-emerald-600">({j.status_konfirmasi})</span>}</span>
                {j.file_jaminan && <a href={`http://localhost:3001${j.file_jaminan}`} target="_blank" rel="noreferrer" className="text-blue-600"><Download size={12} /></a>}
              </div>
            ))}
          </div>
        )}
      </Section>

      <Section icon={ShieldCheck} title="Jaminan Pemeliharaan (Garansi Purna Kontrak)">
        {canEdit && (
          <div className="flex flex-wrap items-end gap-2 mb-3 bg-surface p-3 rounded-lg">
            <input placeholder="Nomor" value={pForm.nomor} onChange={e => setPForm({ ...pForm, nomor: e.target.value })} className="text-xs p-2 border border-gray-300 rounded-lg flex-1 min-w-[80px]" />
            <input type="number" placeholder="Nilai" value={pForm.nilai} onChange={e => setPForm({ ...pForm, nilai: e.target.value })} className="w-24 text-xs p-2 border border-gray-300 rounded-lg" />
            <input type="number" placeholder="Masa (hari)" value={pForm.masa} onChange={e => setPForm({ ...pForm, masa: e.target.value })} className="w-24 text-xs p-2 border border-gray-300 rounded-lg" />
            <input type="date" value={pForm.tanggal_mulai} onChange={e => setPForm({ ...pForm, tanggal_mulai: e.target.value })} className="text-xs p-2 border border-gray-300 rounded-lg" />
            <input type="file" onChange={e => setPFile(e.target.files[0])} className="text-xs" />
            <button onClick={submitJampel} className="btn-secondary text-xs">Simpan</button>
          </div>
        )}
        {jampel.length === 0 ? <p className="text-xs text-muted text-center py-2">Belum ada jaminan pemeliharaan.</p> : (
          <div className="space-y-1">
            {jampel.map(j => (
              <div key={j.id} className="text-xs bg-surface p-2 rounded-lg">{j.nomor} - {formatRupiah(j.nilai, true)} ({j.masa} hari)</div>
            ))}
          </div>
        )}
      </Section>
    </div>
  );
}

// ── SLA ──
export function SlaSection({ tenderId, canEdit, user }) {
  const [items, setItems] = useState([]);
  const [form, setForm] = useState({ availability: '', waktu: '', denda: '', biaya_maintenance: '' });

  const fetchItems = useCallback(async () => {
    const res = await fetch(`${API_BASE}/tenders/${tenderId}/contract/sla`, { headers: getAuthHeaders() });
    const json = await res.json();
    if (json.success) setItems(json.data);
  }, [tenderId]);

  useEffect(() => { fetchItems(); }, [fetchItems]);

  const handleAdd = async () => {
    await fetch(`${API_BASE}/tenders/${tenderId}/contract/sla`, { method: 'POST', headers: getAuthHeaders(), body: JSON.stringify({ ...form, created_by: user.id }) });
    setForm({ availability: '', waktu: '', denda: '', biaya_maintenance: '' });
    fetchItems();
  };

  const handleDelete = async (id) => {
    if (!confirm('Hapus SLA ini?')) return;
    await fetch(`${API_BASE}/tenders/${tenderId}/contract/sla/${id}`, { method: 'DELETE', headers: getAuthHeaders() });
    fetchItems();
  };

  return (
    <Section icon={Wrench} title="SLA (Service Level Agreement) - untuk kontrak layanan/maintenance">
      {canEdit && (
        <div className="flex flex-wrap items-end gap-2 mb-3 bg-surface p-3 rounded-lg">
          <input placeholder="Availability (%)" value={form.availability} onChange={e => setForm({ ...form, availability: e.target.value })} className="text-xs p-2 border border-gray-300 rounded-lg" />
          <input placeholder="Waktu respons" value={form.waktu} onChange={e => setForm({ ...form, waktu: e.target.value })} className="text-xs p-2 border border-gray-300 rounded-lg" />
          <input placeholder="Denda" value={form.denda} onChange={e => setForm({ ...form, denda: e.target.value })} className="text-xs p-2 border border-gray-300 rounded-lg" />
          <input type="number" placeholder="Biaya maintenance" value={form.biaya_maintenance} onChange={e => setForm({ ...form, biaya_maintenance: e.target.value })} className="text-xs p-2 border border-gray-300 rounded-lg" />
          <button onClick={handleAdd} className="btn-secondary text-xs">Tambah</button>
        </div>
      )}
      {items.length === 0 ? <p className="text-xs text-muted text-center py-2">Belum ada SLA.</p> : (
        <div className="space-y-1">
          {items.map(it => (
            <div key={it.id} className="flex items-center justify-between text-xs bg-surface p-2 rounded-lg">
              <span>Availability {it.availability}, respons {it.waktu}, denda {it.denda}</span>
              {canEdit && <button onClick={() => handleDelete(it.id)} className="text-red-400"><Trash2 size={12} /></button>}
            </div>
          ))}
        </div>
      )}
    </Section>
  );
}

// ── Material & Surat Pesanan (Kontrak Payung) ──
export function MaterialSection({ tenderId, canEdit, user }) {
  const [materials, setMaterials] = useState([{ nama: '', qty: '', satuan: '', harga_satuan: '', sifat: 'Tetap' }]);
  const [existing, setExisting] = useState([]);
  const [suratPesanan, setSuratPesanan] = useState([]);
  const [spForm, setSpForm] = useState({ nomor_surat: '', tanggal: '' });
  const [spItems, setSpItems] = useState([{ material_id: '', nama: '', qty: '', harga_satuan: '', satuan: '' }]);

  const fetchAll = useCallback(async () => {
    const [m, s] = await Promise.all([
      fetch(`${API_BASE}/tenders/${tenderId}/contract/materials`, { headers: getAuthHeaders() }),
      fetch(`${API_BASE}/tenders/${tenderId}/contract/surat-pesanan`, { headers: getAuthHeaders() }),
    ]);
    const [mj, sj] = await Promise.all([m.json(), s.json()]);
    if (mj.success) setExisting(mj.data);
    if (sj.success) setSuratPesanan(sj.data);
  }, [tenderId]);

  useEffect(() => { fetchAll(); }, [fetchAll]);

  const saveMaterials = async () => {
    const valid = materials.filter(m => m.nama.trim());
    if (!valid.length) return alert('Isi minimal satu material.');
    await fetch(`${API_BASE}/tenders/${tenderId}/contract/materials`, { method: 'POST', headers: getAuthHeaders(), body: JSON.stringify({ materials: valid, created_by: user.id }) });
    fetchAll();
  };

  const createSuratPesanan = async () => {
    const items = spItems.filter(i => i.nama && i.qty && i.harga_satuan);
    if (!spForm.nomor_surat || !items.length) return alert('Nomor surat dan minimal satu item diperlukan.');
    const res = await fetch(`${API_BASE}/tenders/${tenderId}/contract/surat-pesanan`, {
      method: 'POST', headers: getAuthHeaders(),
      body: JSON.stringify({ ...spForm, items, created_by: user.id }),
    });
    const json = await res.json();
    if (json.success) { setSpForm({ nomor_surat: '', tanggal: '' }); setSpItems([{ material_id: '', nama: '', qty: '', harga_satuan: '', satuan: '' }]); fetchAll(); }
    else alert('Gagal: ' + json.message);
  };

  return (
    <div className="space-y-4">
      <Section icon={Package} title="Material (Kontrak Payung)">
        {canEdit && (
          <div className="space-y-2 mb-3">
            {materials.map((m, i) => (
              <div key={i} className="flex items-center gap-2">
                <input placeholder="Nama" value={m.nama} onChange={e => setMaterials(materials.map((x, xi) => xi === i ? { ...x, nama: e.target.value } : x))} className="flex-1 text-xs p-1.5 border border-gray-300 rounded-lg" />
                <input type="number" placeholder="Qty" value={m.qty} onChange={e => setMaterials(materials.map((x, xi) => xi === i ? { ...x, qty: e.target.value } : x))} className="w-16 text-xs p-1.5 border border-gray-300 rounded-lg" />
                <input placeholder="Satuan" value={m.satuan} onChange={e => setMaterials(materials.map((x, xi) => xi === i ? { ...x, satuan: e.target.value } : x))} className="w-20 text-xs p-1.5 border border-gray-300 rounded-lg" />
                <input type="number" placeholder="Harga" value={m.harga_satuan} onChange={e => setMaterials(materials.map((x, xi) => xi === i ? { ...x, harga_satuan: e.target.value } : x))} className="w-28 text-xs p-1.5 border border-gray-300 rounded-lg" />
              </div>
            ))}
            <div className="flex items-center gap-2">
              <button onClick={() => setMaterials([...materials, { nama: '', qty: '', satuan: '', harga_satuan: '', sifat: 'Tetap' }])} className="text-[10px] text-dpbj-navy font-semibold flex items-center gap-1"><Plus size={11} /> Tambah baris</button>
              <button onClick={saveMaterials} className="btn-secondary text-xs ml-auto">Simpan Daftar Material</button>
            </div>
          </div>
        )}
        {existing.length === 0 ? <p className="text-xs text-muted text-center py-2">Belum ada material.</p> : (
          <div className="space-y-1">
            {existing.map(m => (
              <div key={m.id} className="text-xs bg-surface p-2 rounded-lg">{m.nama} - {m.qty} {m.satuan} @ {formatRupiah(m.harga_satuan, true)}</div>
            ))}
          </div>
        )}
      </Section>

      <Section icon={Package} title="Surat Pesanan">
        {canEdit && (
          <div className="space-y-2 mb-3 bg-surface p-3 rounded-lg">
            <div className="flex items-center gap-2">
              <input placeholder="Nomor Surat" value={spForm.nomor_surat} onChange={e => setSpForm({ ...spForm, nomor_surat: e.target.value })} className="flex-1 text-xs p-1.5 border border-gray-300 rounded-lg" />
              <input type="date" value={spForm.tanggal} onChange={e => setSpForm({ ...spForm, tanggal: e.target.value })} className="text-xs p-1.5 border border-gray-300 rounded-lg" />
            </div>
            {spItems.map((it, i) => (
              <div key={i} className="flex items-center gap-2">
                <select value={it.material_id} onChange={e => {
                  const mat = existing.find(m => m.id === e.target.value);
                  setSpItems(spItems.map((x, xi) => xi === i ? { ...x, material_id: e.target.value, nama: mat?.nama || '', harga_satuan: mat?.harga_satuan || '', satuan: mat?.satuan || '' } : x));
                }} className="flex-1 text-xs p-1.5 border border-gray-300 rounded-lg">
                  <option value="">Pilih material...</option>
                  {existing.map(m => <option key={m.id} value={m.id}>{m.nama}</option>)}
                </select>
                <input type="number" placeholder="Qty" value={it.qty} onChange={e => setSpItems(spItems.map((x, xi) => xi === i ? { ...x, qty: e.target.value } : x))} className="w-16 text-xs p-1.5 border border-gray-300 rounded-lg" />
              </div>
            ))}
            <div className="flex items-center gap-2">
              <button onClick={() => setSpItems([...spItems, { material_id: '', nama: '', qty: '', harga_satuan: '', satuan: '' }])} className="text-[10px] text-dpbj-navy font-semibold flex items-center gap-1"><Plus size={11} /> Tambah item</button>
              <button onClick={createSuratPesanan} className="btn-secondary text-xs ml-auto">Buat Surat Pesanan</button>
            </div>
          </div>
        )}
        {suratPesanan.length === 0 ? <p className="text-xs text-muted text-center py-2">Belum ada surat pesanan.</p> : (
          <div className="space-y-2">
            {suratPesanan.map(sp => (
              <div key={sp.id} className="bg-surface p-2 rounded-lg text-xs">
                <p className="font-bold text-dpbj-navy">{sp.nomor_surat} - {sp.tanggal?.split('T')[0]}</p>
                {sp.items.map(it => <p key={it.id} className="ml-2 text-muted">{it.nama} x{it.qty} = {formatRupiah(it.total, true)} {it.status_terima && `(${it.status_terima})`}</p>)}
              </div>
            ))}
          </div>
        )}
      </Section>
    </div>
  );
}

// ── Addendum (2 tahap approval) ──
export function AddendumSection({ tenderId, canEdit, user }) {
  const [items, setItems] = useState([]);
  const [form, setForm] = useState({ nomor: '', jenis: '', tanggal: '', keterangan: '' });

  const fetchItems = useCallback(async () => {
    const res = await fetch(`${API_BASE}/tenders/${tenderId}/contract/addendum`, { headers: getAuthHeaders() });
    const json = await res.json();
    if (json.success) setItems(json.data);
  }, [tenderId]);

  useEffect(() => { fetchItems(); }, [fetchItems]);

  const handleAdd = async () => {
    if (!form.nomor) return alert('Nomor addendum wajib diisi.');
    const fd = new FormData();
    Object.entries(form).forEach(([k, v]) => fd.append(k, v));
    fd.append('created_by', user.id);
    await fetch(`${API_BASE}/tenders/${tenderId}/contract/addendum`, { method: 'POST', headers: { Authorization: `Bearer ${localStorage.getItem('dpbj_token')}` }, body: fd });
    setForm({ nomor: '', jenis: '', tanggal: '', keterangan: '' });
    fetchItems();
  };

  const handleApproval = async (id, field, value) => {
    await fetch(`${API_BASE}/tenders/${tenderId}/contract/addendum/${id}/approval`, { method: 'PATCH', headers: getAuthHeaders(), body: JSON.stringify({ field, value }) });
    fetchItems();
  };

  return (
    <Section icon={FileEdit} title="Addendum Kontrak">
      {canEdit && (
        <div className="flex flex-wrap items-end gap-2 mb-3 bg-surface p-3 rounded-lg">
          <input placeholder="Nomor" value={form.nomor} onChange={e => setForm({ ...form, nomor: e.target.value })} className="text-xs p-2 border border-gray-300 rounded-lg flex-1 min-w-[100px]" />
          <input placeholder="Jenis" value={form.jenis} onChange={e => setForm({ ...form, jenis: e.target.value })} className="text-xs p-2 border border-gray-300 rounded-lg" />
          <input type="date" value={form.tanggal} onChange={e => setForm({ ...form, tanggal: e.target.value })} className="text-xs p-2 border border-gray-300 rounded-lg" />
          <button onClick={handleAdd} className="btn-secondary text-xs">Ajukan</button>
        </div>
      )}
      {items.length === 0 ? <p className="text-xs text-muted text-center py-2">Belum ada addendum.</p> : (
        <div className="space-y-2">
          {items.map(a => (
            <div key={a.id} className="bg-surface p-2.5 rounded-lg text-xs">
              <div className="flex items-center justify-between">
                <span className="font-bold text-dpbj-navy">{a.nomor} - {a.jenis}</span>
                <span className={clsx('text-[10px] font-bold px-2 py-0.5 rounded-full', a.status === 'selesai' ? 'bg-emerald-100 text-emerald-700' : 'bg-amber-100 text-amber-700')}>{a.status}</span>
              </div>
              {canEdit && a.status !== 'selesai' && (
                <div className="flex gap-2 mt-1.5">
                  <button onClick={() => handleApproval(a.id, 'approved_kasubdit', !a.approved_kasubdit)} className={clsx('text-[10px] px-2 py-0.5 rounded-full', a.approved_kasubdit ? 'bg-emerald-100 text-emerald-700' : 'bg-gray-100 text-gray-600')}>Kasubdit {a.approved_kasubdit ? '✓' : ''}</button>
                  <button onClick={() => handleApproval(a.id, 'approved_penyedia', !a.approved_penyedia)} className={clsx('text-[10px] px-2 py-0.5 rounded-full', a.approved_penyedia ? 'bg-emerald-100 text-emerald-700' : 'bg-gray-100 text-gray-600')}>Penyedia {a.approved_penyedia ? '✓' : ''}</button>
                </div>
              )}
            </div>
          ))}
        </div>
      )}
    </Section>
  );
}

// ── Catatan, Pengingat, Dokumen tambahan ──
export function NotesRemindersDocsSection({ tenderId, canEdit, isVendor, user }) {
  const [notes, setNotes] = useState([]);
  const [reminders, setReminders] = useState([]);
  const [documents, setDocuments] = useState([]);
  const [noteText, setNoteText] = useState('');
  const [reminderForm, setReminderForm] = useState({ judul: '', tanggal_dari: '' });
  const [docFile, setDocFile] = useState(null);
  const [docName, setDocName] = useState('');

  const fetchAll = useCallback(async () => {
    const [n, r, d] = await Promise.all([
      fetch(`${API_BASE}/tenders/${tenderId}/contract/notes`, { headers: getAuthHeaders() }),
      fetch(`${API_BASE}/tenders/${tenderId}/contract/reminders`, { headers: getAuthHeaders() }),
      fetch(`${API_BASE}/tenders/${tenderId}/contract/documents`, { headers: getAuthHeaders() }),
    ]);
    const [nj, rj, dj] = await Promise.all([n.json(), r.json(), d.json()]);
    if (nj.success) setNotes(nj.data);
    if (rj.success) setReminders(rj.data);
    if (dj.success) setDocuments(dj.data);
  }, [tenderId]);

  useEffect(() => { fetchAll(); }, [fetchAll]);

  const submitNote = async () => {
    if (!noteText.trim()) return alert('Catatan tidak boleh kosong.');
    await fetch(`${API_BASE}/tenders/${tenderId}/contract/notes`, {
      method: 'POST', headers: getAuthHeaders(),
      body: JSON.stringify({ jenis: isVendor ? 'penyedia' : 'internal', pesan: noteText, created_by: user.id }),
    });
    setNoteText(''); fetchAll();
  };

  const submitReminder = async () => {
    if (!reminderForm.judul) return alert('Judul pengingat wajib diisi.');
    await fetch(`${API_BASE}/tenders/${tenderId}/contract/reminders`, { method: 'POST', headers: getAuthHeaders(), body: JSON.stringify({ ...reminderForm, created_by: user.id }) });
    setReminderForm({ judul: '', tanggal_dari: '' }); fetchAll();
  };

  const submitDoc = async () => {
    if (!docFile) return alert('Pilih file terlebih dahulu.');
    const fd = new FormData();
    fd.append('nama', docName || docFile.name);
    fd.append('created_by', user.id);
    fd.append('file', docFile);
    await fetch(`${API_BASE}/tenders/${tenderId}/contract/documents`, { method: 'POST', headers: { Authorization: `Bearer ${localStorage.getItem('dpbj_token')}` }, body: fd });
    setDocFile(null); setDocName(''); fetchAll();
  };

  const togglePublish = async (doc) => {
    await fetch(`${API_BASE}/tenders/${tenderId}/contract/documents/${doc.id}/publish`, { method: 'PATCH', headers: getAuthHeaders(), body: JSON.stringify({ publish: !doc.publish_ke_penyedia }) });
    fetchAll();
  };

  return (
    <div className="space-y-4">
      <Section icon={MessageSquare} title="Catatan">
        <div className="flex items-center gap-2 mb-3">
          <input placeholder="Tulis catatan..." value={noteText} onChange={e => setNoteText(e.target.value)} className="flex-1 text-xs p-2 border border-gray-300 rounded-lg" />
          <button onClick={submitNote} className="btn-secondary text-xs">Kirim</button>
        </div>
        {notes.length === 0 ? <p className="text-xs text-muted text-center py-2">Belum ada catatan.</p> : (
          <div className="space-y-1">
            {notes.map(n => (
              <div key={n.id} className="text-xs bg-surface p-2 rounded-lg">
                <span className="font-semibold text-dpbj-navy">{n.created_by_name || '-'}</span> ({n.jenis}): {n.pesan}
              </div>
            ))}
          </div>
        )}
      </Section>

      {canEdit && (
        <Section icon={Bell} title="Pengingat">
          <div className="flex items-center gap-2 mb-3">
            <input placeholder="Judul" value={reminderForm.judul} onChange={e => setReminderForm({ ...reminderForm, judul: e.target.value })} className="flex-1 text-xs p-2 border border-gray-300 rounded-lg" />
            <input type="date" value={reminderForm.tanggal_dari} onChange={e => setReminderForm({ ...reminderForm, tanggal_dari: e.target.value })} className="text-xs p-2 border border-gray-300 rounded-lg" />
            <button onClick={submitReminder} className="btn-secondary text-xs">Tambah</button>
          </div>
          {reminders.length === 0 ? <p className="text-xs text-muted text-center py-2">Belum ada pengingat.</p> : (
            <div className="space-y-1">
              {reminders.map(r => <div key={r.id} className="text-xs bg-surface p-2 rounded-lg">{r.judul} - {r.tanggal_dari?.split('T')[0]}</div>)}
            </div>
          )}
        </Section>
      )}

      <Section icon={Paperclip} title="Dokumen Tambahan">
        {canEdit && (
          <div className="flex items-center gap-2 mb-3">
            <input placeholder="Nama dokumen" value={docName} onChange={e => setDocName(e.target.value)} className="flex-1 text-xs p-2 border border-gray-300 rounded-lg" />
            <input type="file" onChange={e => setDocFile(e.target.files[0])} className="text-xs" />
            <button onClick={submitDoc} className="btn-secondary text-xs flex items-center gap-1"><Upload size={11} /> Unggah</button>
          </div>
        )}
        {documents.length === 0 ? <p className="text-xs text-muted text-center py-2">Belum ada dokumen tambahan.</p> : (
          <div className="space-y-1">
            {documents.map(d => (
              <div key={d.id} className="flex items-center justify-between text-xs bg-surface p-2 rounded-lg">
                <span>{d.nama}</span>
                <div className="flex items-center gap-2">
                  <a href={`http://localhost:3001${d.file_path}`} target="_blank" rel="noreferrer" className="text-blue-600"><Download size={12} /></a>
                  {canEdit && (
                    <button onClick={() => togglePublish(d)} className={clsx('text-[10px] px-2 py-0.5 rounded-full', d.publish_ke_penyedia ? 'bg-emerald-100 text-emerald-700' : 'bg-gray-100 text-gray-600')}>
                      {d.publish_ke_penyedia ? 'Terpublish' : 'Publish'}
                    </button>
                  )}
                </div>
              </div>
            ))}
          </div>
        )}
      </Section>
    </div>
  );
}

// ── Perubahan Status Kontrak ──
const STATUS_CHANGE_LABELS = {
  perubahan: 'Perubahan Kontrak', penyesuaian: 'Penyesuaian Harga', kahar: 'Keadaan Kahar',
  berakhir: 'Berakhir Kontrak', pemutusan: 'Pemutusan Kontrak', kesempatan: 'Pemberian Kesempatan', denda: 'Denda dan Ganti Rugi',
};

export function StatusChangeSection({ tenderId, canEdit, user }) {
  const [items, setItems] = useState([]);
  const [jenis, setJenis] = useState('perubahan');
  const [alasan, setAlasan] = useState('');
  const [file, setFile] = useState(null);

  const fetchItems = useCallback(async () => {
    const res = await fetch(`${API_BASE}/tenders/${tenderId}/contract/status-changes`, { headers: getAuthHeaders() });
    const json = await res.json();
    if (json.success) setItems(json.data);
  }, [tenderId]);

  useEffect(() => { fetchItems(); }, [fetchItems]);

  const handleSubmit = async () => {
    if (!alasan.trim()) return alert('Alasan wajib diisi.');
    const fd = new FormData();
    fd.append('jenis', jenis);
    fd.append('alasan', alasan);
    fd.append('created_by', user.id);
    if (file) fd.append('file', file);
    await fetch(`${API_BASE}/tenders/${tenderId}/contract/status-changes`, { method: 'POST', headers: { Authorization: `Bearer ${localStorage.getItem('dpbj_token')}` }, body: fd });
    setAlasan(''); setFile(null); fetchItems();
  };

  return (
    <Section icon={AlertTriangle} title="Perubahan Status Kontrak" tone="amber">
      {canEdit && (
        <div className="space-y-2 mb-3">
          <div className="flex items-center gap-2">
            <select value={jenis} onChange={e => setJenis(e.target.value)} className="text-xs p-2 border border-gray-300 rounded-lg">
              {Object.entries(STATUS_CHANGE_LABELS).map(([k, v]) => <option key={k} value={k}>{v}</option>)}
            </select>
            <input type="file" onChange={e => setFile(e.target.files[0])} className="text-xs" />
          </div>
          <textarea placeholder="Alasan" value={alasan} onChange={e => setAlasan(e.target.value)} className="w-full text-xs p-2 border border-gray-300 rounded-lg h-16" />
          <button onClick={handleSubmit} className="btn-secondary text-xs">Catat Perubahan</button>
        </div>
      )}
      {items.length === 0 ? <p className="text-xs text-muted text-center py-2">Belum ada perubahan status.</p> : (
        <div className="space-y-1">
          {items.map(s => (
            <div key={s.id} className="text-xs bg-white p-2 rounded-lg border border-amber-200">
              <span className="font-bold text-amber-800">{STATUS_CHANGE_LABELS[s.jenis] || s.jenis}</span>: {s.alasan}
            </div>
          ))}
        </div>
      )}
    </Section>
  );
}

// ── PIC & Tahap Kontrak ──
export function PicStageSection({ tenderId, contract, canEdit, refreshContract }) {
  const [staffList, setStaffList] = useState([]);

  useEffect(() => {
    fetch(`${API_BASE}/users`, { headers: getAuthHeaders() }).then(r => r.json()).then(j => { if (j.success) setStaffList(j.data); }).catch(() => {});
  }, []);

  const assignPic = async (tahap, userId) => {
    await fetch(`${API_BASE}/tenders/${tenderId}/contract/pic`, { method: 'PATCH', headers: getAuthHeaders(), body: JSON.stringify({ tahap, user_id: userId }) });
    refreshContract();
  };

  const changeStage = async (stage) => {
    await fetch(`${API_BASE}/tenders/${tenderId}/contract/stage`, { method: 'PATCH', headers: getAuthHeaders(), body: JSON.stringify({ stage }) });
    refreshContract();
  };

  const STAGES = ['persiapan', 'pengendalian', 'penyelesaian', 'selesai'];

  return (
    <Section icon={UserCog} title="PIC & Tahap Kontrak">
      <div className="mb-3">
        <p className="text-[10px] text-muted mb-1">Tahap saat ini</p>
        <div className="flex gap-1.5 flex-wrap">
          {STAGES.map(s => (
            <button key={s} disabled={!canEdit} onClick={() => changeStage(s)} className={clsx('text-[10px] font-bold px-2.5 py-1 rounded-full capitalize', contract?.stage === s ? 'bg-dpbj-navy text-white' : 'bg-gray-100 text-gray-600', canEdit && 'cursor-pointer')}>
              {s}
            </button>
          ))}
        </div>
      </div>
      {canEdit && (
        <div className="grid grid-cols-3 gap-2">
          {[{ key: 'persiapan', label: 'PIC Persiapan' }, { key: 'pengendali', label: 'PIC Pengendali' }, { key: 'penyelesai', label: 'PIC Penyelesai' }].map(p => (
            <div key={p.key}>
              <label className="text-[10px] text-muted">{p.label}</label>
              <select onChange={e => assignPic(p.key, e.target.value)} className="w-full text-xs p-1.5 border border-gray-300 rounded-lg" defaultValue="">
                <option value="">Pilih...</option>
                {staffList.map(s => <option key={s.id} value={s.id}>{s.full_name}</option>)}
              </select>
            </div>
          ))}
        </div>
      )}
    </Section>
  );
}
