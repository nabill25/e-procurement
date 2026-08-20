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
    case 'dibatalkan': return -2;
    default: return -1;
  }
};
