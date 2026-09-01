// Sistem toast global sederhana (pub-sub module-level, BUKAN React Context) supaya bisa
// dipanggil dari mana saja - termasuk dari dalam fungsi handler biasa di file manapun -
// tanpa perlu tiap file import/pasang useApp() cuma untuk menampilkan notifikasi.
//
// Kenapa ini dibuat: ditemukan 274 pemanggilan alert() bawaan browser tersebar di 32 file
// di seluruh sistem internal (form admin, modal tender/kontrak/vendor, dst). alert() bawaan
// browser memblokir seluruh halaman, tidak bisa distyle, dan terasa sangat kuno dibanding
// UI yang sudah dipoles di tempat lain - jadi diganti semua ke toast ini supaya konsisten.
//
// Jenis toast (success/error/info) dideteksi OTOMATIS dari kata kunci di pesannya (lihat
// detectType di bawah), supaya penggantian alert(pesan) -> toast(pesan) di 32 file itu bisa
// dilakukan tanpa perlu menilai manual satu-satu jenis pesannya - polanya di seluruh
// aplikasi sudah sangat konsisten (nyaris selalu diawali "Gagal"/"Terjadi kesalahan" untuk
// error, atau mengandung "berhasil"/"terkirim" untuk sukses).

let listeners = [];
let idCounter = 0;

function detectType(message) {
  const m = String(message).toLowerCase();
  if (/gagal|error|kesalahan|tidak (valid|boleh|bisa|ditemukan)|salah|wajib/.test(m)) return 'error';
  if (/berhasil|sukses|terkirim|tersimpan/.test(m)) return 'success';
  return 'info';
}

export function toast(message, type) {
  const item = { id: ++idCounter, message: String(message), type: type || detectType(message) };
  listeners.forEach(fn => fn(item));
  return undefined; // supaya pola lama "return alert(...)" tetap berfungsi sama persis
}

export function subscribeToast(fn) {
  listeners.push(fn);
  return () => { listeners = listeners.filter(l => l !== fn); };
}
