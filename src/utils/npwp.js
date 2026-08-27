// Format NPWP otomatis saat mengetik, mendukung 2 format resmi yang berlaku:
// - Format lama (15 digit): XX.XXX.XXX.X-XXX.XXX
// - Format baru (16 digit, sejak 2024 mengikuti NIK): ditulis polos tanpa titik/strip
// Deteksi format otomatis dari jumlah digit yang diketik user - begitu digit ke-16 masuk,
// otomatis dianggap format baru dan berhenti menambahkan titik/strip.
export function formatNPWP(rawValue) {
  const digits = rawValue.replace(/\D/g, '').slice(0, 16);

  if (digits.length <= 15) {
    // Format lama: 2.3.3.1-3.3 digit dipisah titik/strip
    const parts = [
      digits.slice(0, 2),
      digits.slice(2, 5),
      digits.slice(5, 8),
      digits.slice(8, 9),
      digits.slice(9, 12),
      digits.slice(12, 15),
    ].filter(Boolean);

    let result = parts[0] || '';
    if (parts[1]) result += '.' + parts[1];
    if (parts[2]) result += '.' + parts[2];
    if (parts[3]) result += '.' + parts[3];
    if (parts[4]) result += '-' + parts[4];
    if (parts[5]) result += '.' + parts[5];
    return result;
  }

  // Format baru: 16 digit polos tanpa pemisah
  return digits;
}

// Hasil format yang valid: 15 digit (format lama) atau 16 digit (format baru).
export function isValidNPWP(value) {
  const digits = value.replace(/\D/g, '');
  return digits.length === 15 || digits.length === 16;
}

export function npwpErrorMessage(value) {
  const digits = value.replace(/\D/g, '');
  if (digits.length === 0) return 'Wajib diisi';
  if (digits.length < 15) return `NPWP harus 15 digit (format lama) atau 16 digit (format baru), baru ${digits.length} digit`;
  if (digits.length > 16) return 'NPWP maksimal 16 digit';
  return null;
}
