const { pool } = require('./db');

async function run() {
  try {
    const [rows] = await pool.query('SELECT * FROM procurement_requests WHERE status = "draft" LIMIT 1');
    if (rows.length === 0) {
      console.log('No draft pengajuan found.');
      process.exit();
    }
    const p = rows[0];
    console.log('Trying to approve:', p.id);

    const conn = await pool.getConnection();
    await conn.beginTransaction();
    try {
      await conn.query('UPDATE procurement_requests SET status = "approved" WHERE id = ?', [p.id]);
      
      const tenderNumber = `TENDER/${p.fiscal_year}/${p.request_number.split('/').pop()}`;
      await conn.query(`
        INSERT INTO tenders 
          (id, procurement_request_id, tender_number, title, unit_kerja, category, 
           method, status, pagu_anggaran, submission_deadline)
        VALUES (UUID(), ?, ?, ?, ?, ?, 'tender', 'draft', ?, DATE_ADD(NOW(), INTERVAL 14 DAY))
      `, [p.id, tenderNumber, p.title, p.unit_kerja, p.category, p.estimated_value]);
      
      await conn.query(`
        INSERT INTO audit_logs (action, entity_type, description, is_success)
        VALUES ('UPDATE', 'Pengajuan', ?, 1)
      `, [`Pengajuan di-ACC menjadi Tender: ${p.title}`]);
      
      await conn.commit();
      console.log('Success!');
    } catch (e) {
      await conn.rollback();
      console.error('Error during transaction:', e);
    } finally {
      conn.release();
    }
  } catch (err) {
    console.error('Error fetching:', err);
  }
  process.exit();
}
run();
