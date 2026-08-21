import { useState } from 'react';
import { Save, Plus, Trash2 } from 'lucide-react';
import { API_BASE } from '../../context/AppContext';

function GenericArrayTab({ title, fields, dataArray, onSave, onUpdateItem, onAddItem, onRemoveItem }) {
  const [loading, setLoading] = useState(false);

  const handleSave = async () => {
    setLoading(true);
    await onSave();
    setLoading(false);
  };

  return (
    <div className="space-y-6 animate-fade-in">
      <div className="flex justify-between items-center">
        <h3 className="font-bold text-dpbj-navy text-sm">{title}</h3>
        <button onClick={onAddItem} className="btn-secondary text-xs px-3 py-1 flex items-center gap-1">
          <Plus size={14} /> Tambah Data
        </button>
      </div>

      {dataArray.length === 0 ? (
        <div className="bg-surface border border-border rounded-xl p-8 text-center text-muted text-sm">
          Belum ada data {title.toLowerCase()}. Silakan tambah data baru.
        </div>
      ) : (
        <div className="space-y-4">
          {dataArray.map((item, index) => (
            <div key={index} className="bg-white border border-border rounded-xl p-4 shadow-sm relative">
              <button 
                onClick={() => onRemoveItem(index)}
                className="absolute top-4 right-4 text-red-500 hover:bg-red-50 p-1.5 rounded-lg transition-colors"
              >
                <Trash2 size={16} />
              </button>
              <div className="grid grid-cols-2 gap-4 pr-10">
                {fields.map(field => (
                  <div key={field.key} className={field.fullWidth ? "col-span-2" : ""}>
                    <label className="block text-xs font-semibold text-muted mb-1">{field.label}</label>
                    <input 
                      type={field.type || "text"}
                      className="form-input w-full text-sm"
                      value={item[field.key] || ''}
                      onChange={e => onUpdateItem(index, field.key, e.target.value)}
                      placeholder={field.placeholder || ''}
                    />
                  </div>
                ))}
              </div>
            </div>
          ))}
          <div className="flex justify-end pt-2">
            <button onClick={handleSave} disabled={loading} className="btn-primary px-6 flex items-center gap-2">
              <Save size={16} /> {loading ? 'Menyimpan...' : 'Simpan Perubahan'}
            </button>
          </div>
        </div>
      )}
    </div>
  );
}

export function PajakTab({ vendor, getAuthHeaders, refreshData }) {
  const [data, setData] = useState(vendor?.pajak || []);

  const fields = [
    { key: 'jenis_pajak', label: 'Jenis Pajak (Misal: SPT Tahunan 2023)' },
    { key: 'masa_pajak', label: 'Masa Pajak / Tahun' },
    { key: 'no_bukti', label: 'Nomor Bukti Penerimaan Surat (BPS)' },
    { key: 'tanggal_bukti', label: 'Tanggal BPS', type: 'date' }
  ];

  const handleSave = async () => {
    try {
      await fetch(`${API_BASE}/vendors/${vendor.user_id}/profile`, {
        method: 'PUT', headers: getAuthHeaders(), body: JSON.stringify({ pajak: data })
      });
      alert('Data pajak berhasil disimpan.');
      refreshData();
    } catch (e) {}
  };

  return <GenericArrayTab title="Data Pajak" fields={fields} dataArray={data} onSave={handleSave} 
    onUpdateItem={(idx, key, val) => { const d = [...data]; d[idx][key] = val; setData(d); }}
    onAddItem={() => setData([...data, {}])} onRemoveItem={(idx) => setData(data.filter((_, i) => i !== idx))}
  />;
}

export function TenagaAhliTab({ vendor, getAuthHeaders, refreshData }) {
  const [data, setData] = useState(vendor?.tenaga_ahli || []);

  const fields = [
    { key: 'nama', label: 'Nama Tenaga Ahli' },
    { key: 'tgl_lahir', label: 'Tanggal Lahir', type: 'date' },
    { key: 'pendidikan', label: 'Pendidikan Terakhir' },
    { key: 'pengalaman_tahun', label: 'Pengalaman (Tahun)', type: 'number' },
    { key: 'profesi', label: 'Profesi / Jabatan' },
    { key: 'keahlian', label: 'Keahlian Spesifik' }
  ];

  const handleSave = async () => {
    try {
      await fetch(`${API_BASE}/vendors/${vendor.user_id}/profile`, {
        method: 'PUT', headers: getAuthHeaders(), body: JSON.stringify({ tenaga_ahli: data })
      });
      alert('Data Tenaga Ahli berhasil disimpan.');
      refreshData();
    } catch (e) {}
  };

  return <GenericArrayTab title="Daftar Tenaga Ahli" fields={fields} dataArray={data} onSave={handleSave} 
    onUpdateItem={(idx, key, val) => { const d = [...data]; d[idx][key] = val; setData(d); }}
    onAddItem={() => setData([...data, {}])} onRemoveItem={(idx) => setData(data.filter((_, i) => i !== idx))}
  />;
}

export function PeralatanTab({ vendor, getAuthHeaders, refreshData }) {
  const [data, setData] = useState(vendor?.peralatan || []);

  const fields = [
    { key: 'nama_alat', label: 'Nama Peralatan / Fasilitas' },
    { key: 'jumlah', label: 'Jumlah', type: 'number' },
    { key: 'kapasitas', label: 'Kapasitas / Output' },
    { key: 'merk', label: 'Merk / Tipe' },
    { key: 'tahun_pembuatan', label: 'Tahun Pembuatan' },
    { key: 'kondisi', label: 'Kondisi (%)', type: 'number' },
    { key: 'lokasi', label: 'Lokasi Sekarang' },
    { key: 'kepemilikan', label: 'Status Kepemilikan (Milik Sendiri / Sewa)' }
  ];

  const handleSave = async () => {
    try {
      await fetch(`${API_BASE}/vendors/${vendor.user_id}/profile`, {
        method: 'PUT', headers: getAuthHeaders(), body: JSON.stringify({ peralatan: data })
      });
      alert('Data Peralatan berhasil disimpan.');
      refreshData();
    } catch (e) {}
  };

  return <GenericArrayTab title="Daftar Peralatan & Fasilitas" fields={fields} dataArray={data} onSave={handleSave} 
    onUpdateItem={(idx, key, val) => { const d = [...data]; d[idx][key] = val; setData(d); }}
    onAddItem={() => setData([...data, {}])} onRemoveItem={(idx) => setData(data.filter((_, i) => i !== idx))}
  />;
}

export function PengurusTab({ vendor, getAuthHeaders, refreshData }) {
  const [data, setData] = useState(vendor?.pengurus || []);

  const fields = [
    { key: 'nama', label: 'Nama Pengurus / Direksi' },
    { key: 'no_ktp', label: 'Nomor KTP / Paspor' },
    { key: 'jabatan', label: 'Jabatan dalam Perusahaan' },
    { key: 'saham_persen', label: 'Persentase Saham (%)', type: 'number' },
    { key: 'alamat', label: 'Alamat', fullWidth: true }
  ];

  const handleSave = async () => {
    try {
      await fetch(`${API_BASE}/vendors/${vendor.user_id}/profile`, {
        method: 'PUT', headers: getAuthHeaders(), body: JSON.stringify({ pengurus: data })
      });
      alert('Susunan Pengurus berhasil disimpan.');
      refreshData();
    } catch (e) {}
  };

  return <GenericArrayTab title="Susunan Pengurus & Pemilik Saham" fields={fields} dataArray={data} onSave={handleSave}
    onUpdateItem={(idx, key, val) => { const d = [...data]; d[idx][key] = val; setData(d); }}
    onAddItem={() => setData([...data, {}])} onRemoveItem={(idx) => setData(data.filter((_, i) => i !== idx))}
  />;
}

export function BankTab({ vendor, getAuthHeaders, refreshData }) {
  const [data, setData] = useState(vendor?.bank || []);

  const fields = [
    { key: 'nama_bank', label: 'Nama Bank' },
    { key: 'cabang', label: 'Cabang' },
    { key: 'no_rekening', label: 'Nomor Rekening' },
    { key: 'pemilik_rekening', label: 'Nama Pemilik Rekening' }
  ];

  const handleSave = async () => {
    try {
      await fetch(`${API_BASE}/vendors/${vendor.user_id}/profile`, {
        method: 'PUT', headers: getAuthHeaders(), body: JSON.stringify({ bank: data })
      });
      alert('Data Rekening Bank berhasil disimpan.');
      refreshData();
    } catch (e) {}
  };

  return <GenericArrayTab title="Rekening Bank" fields={fields} dataArray={data} onSave={handleSave}
    onUpdateItem={(idx, key, val) => { const d = [...data]; d[idx][key] = val; setData(d); }}
    onAddItem={() => setData([...data, {}])} onRemoveItem={(idx) => setData(data.filter((_, i) => i !== idx))}
  />;
}

export function NeracaTab({ vendor, getAuthHeaders, refreshData }) {
  const [data, setData] = useState(vendor?.neraca || []);

  const fields = [
    { key: 'tahun', label: 'Tahun Buku', type: 'number' },
    { key: 'aktiva', label: 'Total Aktiva (Rp)', type: 'number' },
    { key: 'pasiva', label: 'Total Pasiva (Rp)', type: 'number' },
    { key: 'modal', label: 'Modal (Rp)', type: 'number' },
    { key: 'nama_auditor', label: 'Nama Akuntan Publik / Auditor' },
    { key: 'kesimpulan_audit', label: 'Kesimpulan Audit', fullWidth: true }
  ];

  const handleSave = async () => {
    try {
      await fetch(`${API_BASE}/vendors/${vendor.user_id}/profile`, {
        method: 'PUT', headers: getAuthHeaders(), body: JSON.stringify({ neraca: data })
      });
      alert('Data Neraca Keuangan berhasil disimpan.');
      refreshData();
    } catch (e) {}
  };

  return <GenericArrayTab title="Neraca Keuangan" fields={fields} dataArray={data} onSave={handleSave}
    onUpdateItem={(idx, key, val) => { const d = [...data]; d[idx][key] = val; setData(d); }}
    onAddItem={() => setData([...data, {}])} onRemoveItem={(idx) => setData(data.filter((_, i) => i !== idx))}
  />;
}
