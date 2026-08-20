const { pool } = require('./db');

async function run() {
  const [rows] = await pool.query('SELECT * FROM procurement_requests WHERE status = "draft" LIMIT 1');
  if (rows.length === 0) {
    console.log('No draft pengajuan found.');
    process.exit();
  }
  const id = rows[0].id;
  console.log('Testing approve on id:', id);
  
  const res = await fetch(`http://localhost:3001/api/pengajuan/${id}/approve`, {
    method: 'POST'
  });
  const json = await res.json();
  console.log(json);
  process.exit();
}
run();
