// Modul integrasi Oracle ERP (RKA & Purchase Requisition masuk dari Oracle, Supplier & PO
// keluar ke Oracle). Mengikuti mekanisme sistem lama (lihat catatan lengkap di migrasi
// 030_integrasi_oracle.sql): file Excel (.xlsx) dititipkan tim Oracle/keuangan ke folder di
// server SFTP, diambil dan diimpor lewat sistem ini.
//
// PENTING soal kredensial: TIDAK PERNAH di-hardcode di kode (beda dari sistem lama yang
// ditemukan menyimpan kredensial SFTP asli langsung di kode - lihat catatan project).
// Sama seperti pola SMTP_* di mailer.js: kalau ORACLE_SFTP_HOST kosong di .env, semua fungsi
// koneksi remote di sini mengembalikan status "belum dikonfigurasi" dengan aman, TIDAK
// melempar error - supaya sisi upload manual (yang tidak butuh koneksi SFTP sama sekali)
// tetap berfungsi penuh walau kredensial belum diisi.

const SftpClient = require('ssh2-sftp-client');
const ExcelJS = require('exceljs');

const SFTP_HOST = process.env.ORACLE_SFTP_HOST || '';
const isConfigured = !!SFTP_HOST;

function getConfig() {
  return {
    host: SFTP_HOST,
    port: parseInt(process.env.ORACLE_SFTP_PORT || '22', 10),
    username: process.env.ORACLE_SFTP_USER || '',
    password: process.env.ORACLE_SFTP_PASS || '',
  };
}

const PATH_RKA = process.env.ORACLE_SFTP_PATH_RKA || 'titip/rka';
const PATH_PR = process.env.ORACLE_SFTP_PATH_PR || 'titip/pr';

// ── Daftar file .xlsx di folder remote (RKA atau PR) ──
async function listRemoteFiles(jenis) {
  if (!isConfigured) {
    return { success: false, configured: false, message: 'Koneksi SFTP ke Oracle belum dikonfigurasi. Hubungi Admin untuk mengisi kredensial di server/.env (ORACLE_SFTP_HOST dst).' };
  }
  const sftp = new SftpClient();
  try {
    await sftp.connect(getConfig());
    const remotePath = jenis === 'rka' ? PATH_RKA : PATH_PR;
    const files = await sftp.list(remotePath);
    const xlsxFiles = files.filter(f => f.type === '-' && f.name.toLowerCase().endsWith('.xlsx'));
    return { success: true, configured: true, data: xlsxFiles.map(f => ({ name: f.name, size: f.size, modified: f.modifyTime })) };
  } catch (err) {
    return { success: false, configured: true, message: `Gagal terhubung ke server SFTP: ${err.message}` };
  } finally {
    try { await sftp.end(); } catch (_) {}
  }
}

// ── Unduh 1 file dari server remote, kembalikan sebagai Buffer ──
async function downloadRemoteFile(jenis, fileName) {
  if (!isConfigured) {
    return { success: false, configured: false, message: 'Koneksi SFTP ke Oracle belum dikonfigurasi.' };
  }
  const sftp = new SftpClient();
  try {
    await sftp.connect(getConfig());
    const remotePath = jenis === 'rka' ? PATH_RKA : PATH_PR;
    const buffer = await sftp.get(`${remotePath}/${fileName}`);
    return { success: true, buffer };
  } catch (err) {
    return { success: false, configured: true, message: `Gagal mengunduh file: ${err.message}` };
  } finally {
    try { await sftp.end(); } catch (_) {}
  }
}

// ── Parsing file Excel RKA (kolom: RKA KEY, START DATE YEAR, SEGMENT1, SEGMENT1 DESC,
//     SEGMENT2, SEGMENT2 DESC, BUDGET AMT, REMAIN AMT - meniru kolom tabel yang ditampilkan
//     di halaman "Monitoring Integrasi RKA" sistem lama) ──
async function parseRkaExcel(buffer) {
  const workbook = new ExcelJS.Workbook();
  await workbook.xlsx.load(buffer);
  const sheet = workbook.worksheets[0];
  if (!sheet) throw new Error('File Excel tidak punya sheet apapun.');

  const rows = [];
  let headerRow = null;
  sheet.eachRow((row, rowNumber) => {
    const values = row.values.slice(1).map(v => (v === null || v === undefined) ? '' : String(v).trim());
    if (rowNumber === 1) { headerRow = values.map(h => h.toUpperCase()); return; }
    if (values.every(v => v === '')) return; // baris kosong dilewati
    const get = (label) => {
      const idx = headerRow.findIndex(h => h.includes(label));
      return idx >= 0 ? values[idx] : '';
    };
    rows.push({
      rka_key: get('RKA KEY'),
      start_date_year: parseInt(get('YEAR')) || null,
      segment1: get('SEGMENT1'), segment1_desc: get('SEGMENT1 DESC'),
      segment2: get('SEGMENT2'), segment2_desc: get('SEGMENT2 DESC'),
      budget_amt: parseFloat(get('BUDGET AMT').replace(/,/g, '')) || 0,
      remain_amt: parseFloat(get('REMAIN AMT').replace(/,/g, '')) || 0,
      raw_data: Object.fromEntries(headerRow.map((h, i) => [h, values[i]])),
    });
  });
  return rows;
}

// ── Parsing file Excel PR (kolom header sesuai INTEGRATION_IMPORT_PR_HEADER, dengan baris
//     item digabung berdasarkan REQUISITION_NUMBER yang sama) ──
async function parsePrExcel(buffer) {
  const workbook = new ExcelJS.Workbook();
  await workbook.xlsx.load(buffer);
  const sheet = workbook.worksheets[0];
  if (!sheet) throw new Error('File Excel tidak punya sheet apapun.');

  const byRequisition = {};
  let headerRow = null;
  sheet.eachRow((row, rowNumber) => {
    const values = row.values.slice(1).map(v => (v === null || v === undefined) ? '' : String(v).trim());
    if (rowNumber === 1) { headerRow = values.map(h => h.toUpperCase()); return; }
    if (values.every(v => v === '')) return;
    const get = (label) => {
      const idx = headerRow.findIndex(h => h.includes(label));
      return idx >= 0 ? values[idx] : '';
    };
    const reqNumber = get('REQUISITION_NUMBER') || get('REQUISITION NUMBER');
    if (!reqNumber) return;
    if (!byRequisition[reqNumber]) {
      byRequisition[reqNumber] = {
        requisition_number: reqNumber,
        description: get('DESCRIPTION'),
        bu_name: get('BU_NAME') || get('BU NAME'),
        document_status: get('DOCUMENT_STATUS') || get('DOCUMENT STATUS'),
        pr_type: get('PR_TYPE') || get('PR TYPE'),
        metode_pengadaan: get('METODE_PENGADAAN') || get('METODE PENGADAAN'),
        jenis_anggaran: get('JENIS_ANGGARAN') || get('JENIS ANGGARAN'),
        nomor_rup: get('NOMOR_RUP') || get('NOMOR RUP'),
        subdivisi: get('SUBDIVISI'),
        lines: [],
      };
    }
    byRequisition[reqNumber].lines.push({
      item_description: get('ITEM_DESCRIPTION') || get('ITEM DESCRIPTION'),
      quantity: parseFloat(get('QUANTITY')) || 0,
      uom_code: get('UOM_CODE') || get('UOM CODE'),
      unit_price: parseFloat(get('UNIT_PRICE').replace(/,/g, '')) || 0,
      amount: parseFloat(get('AMOUNT').replace(/,/g, '')) || 0,
    });
  });
  return Object.values(byRequisition);
}

// ── Generate Excel Supplier (data vendor terverifikasi) untuk dikirim ke Oracle ──
async function generateSupplierExcel(vendors) {
  const workbook = new ExcelJS.Workbook();
  const sheet = workbook.addWorksheet('Supplier');
  sheet.columns = [
    { header: 'Nama Perusahaan', key: 'company_name', width: 35 },
    { header: 'NPWP', key: 'npwp', width: 22 },
    { header: 'Email', key: 'email', width: 28 },
    { header: 'Telepon', key: 'phone', width: 18 },
    { header: 'Provinsi', key: 'province', width: 20 },
    { header: 'Kota', key: 'city', width: 20 },
    { header: 'Kelas Kualifikasi', key: 'qualification_class', width: 18 },
    { header: 'Status', key: 'status', width: 16 },
  ];
  sheet.getRow(1).font = { bold: true };
  vendors.forEach(v => sheet.addRow(v));
  return workbook.xlsx.writeBuffer();
}

// ── Generate Excel PO (kontrak yang sudah selesai) untuk dikirim ke Oracle ──
async function generatePoExcel(contracts) {
  const workbook = new ExcelJS.Workbook();
  const sheet = workbook.addWorksheet('PO');
  sheet.columns = [
    { header: 'Nomor Kontrak', key: 'contract_number', width: 22 },
    { header: 'Nama Paket', key: 'title', width: 40 },
    { header: 'Vendor', key: 'company_name', width: 32 },
    { header: 'Nilai Kontrak', key: 'contract_value', width: 18 },
    { header: 'Tanggal Kontrak', key: 'contract_date', width: 18 },
    { header: 'Status', key: 'status', width: 16 },
  ];
  sheet.getRow(1).font = { bold: true };
  contracts.forEach(c => sheet.addRow(c));
  return workbook.xlsx.writeBuffer();
}

module.exports = {
  isConfigured,
  listRemoteFiles,
  downloadRemoteFile,
  parseRkaExcel,
  parsePrExcel,
  generateSupplierExcel,
  generatePoExcel,
};
