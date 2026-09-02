export const procurementPhases = [
  { id: 'pengumuman', label: 'Pengumuman Pascakualifikasi' },
  { id: 'pendaftaran', label: 'Pendaftaran & Download Dokumen' },
  { id: 'penawaran', label: 'Upload Dokumen Penawaran' },
  { id: 'evaluasi', label: 'Evaluasi Penawaran' },
  { id: 'pemenang', label: 'Penetapan & Pengumuman Pemenang' },
  { id: 'masa_sanggah', label: 'Masa Sanggah' },
  { id: 'kontrak', label: 'Kontrak & BAST' }
];

export const getTenderPhaseIndex = (status) => {
  switch (status) {
    case 'draft': return -1;
    case 'pengumuman': return 0;
    case 'pendaftaran': return 1;
    case 'penawaran': return 2;
    case 'evaluasi': return 3;
    case 'pemenang': return 4;
    case 'masa_sanggah': return 5;
    case 'kontrak': return 6;
    // 'selesai' (tender yang kontraknya sudah tuntas) sengaja disamakan indeksnya dengan
    // 'kontrak', bukan jatuh ke default -1 - supaya semua tab yang butuh tahap kontrak
    // (Evaluasi, Negosiasi, Kontrak & BAST, dst) TETAP bisa dibuka untuk ditinjau ulang
    // setelah tender benar-benar selesai, bukan mendadak hilang semua.
    case 'selesai': return 6;
    case 'dibatalkan': return -2;
    default: return -1;
  }
};

// Konfigurasi badge status untuk StatusBadge (label + warna), khusus tender - dipakai di
// TenderTable.jsx dan DetailTenderModal.jsx. Sebelumnya kedua tempat itu salah pakai
// `statusConfig` dari data/mockData.js (isinya cuma untuk status Pengajuan: draft/diajukan/
// proses_review/disetujui/ditolak/revisi/dibatalkan), yang TIDAK PUNYA satupun entri untuk
// status tender asli (pengumuman/pendaftaran/penawaran/evaluasi/pemenang/masa_sanggah/kontrak)
// - akibatnya badge status tender selama ini tampil sebagai teks mentah bahasa Inggris/kode
// tanpa warna sama sekali (StatusBadge jatuh ke tampilan fallback), bukan cuma soal gaya.
export const tenderStatusConfig = {
  draft:        { label: 'Draft',              className: 'badge-draft',  dot: '#9CA3AF' },
  pengumuman:   { label: 'Pengumuman',          className: 'badge-open',   dot: '#2563EB' },
  pendaftaran:  { label: 'Pendaftaran',         className: 'badge-open',   dot: '#2563EB' },
  penawaran:    { label: 'Upload Penawaran',    className: 'badge-review', dot: '#D97706' },
  evaluasi:     { label: 'Evaluasi',            className: 'badge-eval',   dot: '#7C3AED' },
  pemenang:     { label: 'Penetapan Pemenang',  className: 'badge-eval',   dot: '#7C3AED' },
  masa_sanggah: { label: 'Masa Sanggah',        className: 'badge-review', dot: '#D97706' },
  kontrak:      { label: 'Kontrak & BAST',      className: 'badge-done',   dot: '#059669' },
  selesai:      { label: 'Selesai',             className: 'badge-done',   dot: '#059669' },
  dibatalkan:   { label: 'Dibatalkan',          className: 'badge-cancel', dot: '#DC2626' },
};
