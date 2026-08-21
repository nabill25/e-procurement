const { pool } = require('../db');

// Catat satu baris rekam jejak (padanan REKAM_JEJAK di eProc lama) - timeline detail per
// tahap alur pengadaan, beda dari audit_logs yang generik. Gagal insert TIDAK BOLEH
// menggagalkan aksi utama pemanggilnya, jadi selalu dibungkus try/catch sendiri.
async function logActivity({ tenderId, procurementRequestId, contractId, posisi, keterangan, flow, userId, ip }) {
  try {
    await pool.query(
      `INSERT INTO tender_activity_logs
        (tender_id, procurement_request_id, contract_id, posisi, keterangan, flow, user_id, ip_address)
       VALUES ($1, $2, $3, $4, $5, $6, $7, $8)`,
      [tenderId || null, procurementRequestId || null, contractId || null, posisi, keterangan || null, flow || null, userId || null, ip || null]
    );
  } catch (err) {
    console.error('[ACTIVITY LOG]', err.message);
  }
}

module.exports = { logActivity };
