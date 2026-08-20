const { pool } = require('./db');

async function run() {
  try {
    await pool.query('ALTER TABLE audit_logs ADD COLUMN description VARCHAR(500) AFTER entity_id');
    console.log('Column added successfully.');
  } catch (err) {
    if (err.code === 'ER_DUP_FIELDNAME') {
      console.log('Column already exists.');
    } else {
      console.error(err);
    }
  }
  process.exit();
}
run();
