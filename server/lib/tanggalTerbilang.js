// Helper terbilang tanggal Indonesia, dipakai di dokumen cetak resmi (Berita Acara dst)
// meniru kalimat baku "Pada hari ini, SENIN tanggal DUA PULUH DELAPAN bulan AGUSTUS tahun
// DUA RIBU DUA PULUH ENAM" yang dipakai di sistem lama (fungsi getHari/getTerbilang/getNameMonth
// di eproc/application/functions/date.func.php dan string.func.php).

const HARI = ['MINGGU', 'SENIN', 'SELASA', 'RABU', 'KAMIS', 'JUMAT', 'SABTU'];
const BULAN = ['', 'JANUARI', 'FEBRUARI', 'MARET', 'APRIL', 'MEI', 'JUNI', 'JULI', 'AGUSTUS', 'SEPTEMBER', 'OKTOBER', 'NOVEMBER', 'DESEMBER'];

const SATUAN = ['', 'SATU', 'DUA', 'TIGA', 'EMPAT', 'LIMA', 'ENAM', 'TUJUH', 'DELAPAN', 'SEMBILAN'];

function terbilang(n) {
  n = Math.floor(Math.abs(n));
  if (n < 10) return SATUAN[n] || 'NOL';
  if (n < 20) return (n === 10 ? 'SEPULUH' : n === 11 ? 'SEBELAS' : `${SATUAN[n - 10]} BELAS`);
  if (n < 100) {
    const puluh = Math.floor(n / 10);
    const sisa = n % 10;
    return `${SATUAN[puluh]} PULUH${sisa ? ' ' + SATUAN[sisa] : ''}`;
  }
  if (n < 1000) {
    const ratus = Math.floor(n / 100);
    const sisa = n % 100;
    return `${ratus === 1 ? 'SERATUS' : SATUAN[ratus] + ' RATUS'}${sisa ? ' ' + terbilang(sisa) : ''}`;
  }
  if (n < 1000000) {
    const ribu = Math.floor(n / 1000);
    const sisa = n % 1000;
    return `${ribu === 1 ? 'SERIBU' : terbilang(ribu) + ' RIBU'}${sisa ? ' ' + terbilang(sisa) : ''}`;
  }
  // Tahun sampai jutaan (cukup untuk kebutuhan dokumen tahun 2000-an)
  const juta = Math.floor(n / 1000000);
  const sisa = n % 1000000;
  return `${terbilang(juta)} JUTA${sisa ? ' ' + terbilang(sisa) : ''}`;
}

// Terima Date object atau string tanggal, kembalikan kalimat lengkap terbilang
function kalimatTanggalTerbilang(dateInput) {
  const d = dateInput ? new Date(dateInput) : new Date();
  if (isNaN(d.getTime())) return '';
  const hari = HARI[d.getDay()];
  const tanggal = terbilang(d.getDate());
  const bulan = BULAN[d.getMonth() + 1];
  const tahun = terbilang(d.getFullYear());
  const dmy = `${String(d.getDate()).padStart(2, '0')}-${String(d.getMonth() + 1).padStart(2, '0')}-${d.getFullYear()}`;
  return `Pada hari ini, ${hari} tanggal ${tanggal} bulan ${bulan} tahun ${tahun} (${dmy})`;
}

function formatTanggalIndo(dateInput) {
  if (!dateInput) return '-';
  const d = new Date(dateInput);
  if (isNaN(d.getTime())) return '-';
  return `${d.getDate()} ${BULAN[d.getMonth() + 1].charAt(0) + BULAN[d.getMonth() + 1].slice(1).toLowerCase()} ${d.getFullYear()}`;
}

module.exports = { terbilang, kalimatTanggalTerbilang, formatTanggalIndo };
