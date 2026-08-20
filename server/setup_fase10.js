const { Pool } = require('pg');
require('dotenv').config();

const pool = new Pool({
  user: 'postgres',
  host: 'localhost',
  database: 'dpbj_ui',
  password: 'nabil2507',
  port: 5432,
});

async function setup() {
  try {
    console.log('Connecting to database for Phase 10 Setup (E-Purchasing / Katalog)...');
    await pool.connect();
    
    console.log('Creating katalog_items table...');
    await pool.query(`
      CREATE TABLE IF NOT EXISTS katalog_items (
        id UUID PRIMARY KEY DEFAULT gen_random_uuid(),
        vendor_id UUID REFERENCES users(id) ON DELETE CASCADE,
        item_name VARCHAR(255) NOT NULL,
        description TEXT,
        price NUMERIC(15,2) NOT NULL,
        unit VARCHAR(50) DEFAULT 'Pcs',
        image_url TEXT,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
      );
    `);

    console.log('Creating purchasing_orders table...');
    await pool.query(`
      CREATE TABLE IF NOT EXISTS purchasing_orders (
        id UUID PRIMARY KEY DEFAULT gen_random_uuid(),
        buyer_id UUID REFERENCES users(id),
        vendor_id UUID REFERENCES users(id),
        total_amount NUMERIC(15,2) NOT NULL,
        status VARCHAR(50) DEFAULT 'pending', -- pending, approved, completed, rejected
        delivery_address TEXT,
        notes TEXT,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
      );
    `);

    console.log('Creating purchasing_order_items table...');
    await pool.query(`
      CREATE TABLE IF NOT EXISTS purchasing_order_items (
        id UUID PRIMARY KEY DEFAULT gen_random_uuid(),
        order_id UUID REFERENCES purchasing_orders(id) ON DELETE CASCADE,
        item_id UUID REFERENCES katalog_items(id),
        quantity INTEGER NOT NULL CHECK (quantity > 0),
        price_at_purchase NUMERIC(15,2) NOT NULL
      );
    `);
    
    console.log('Phase 10 Setup completed successfully!');
  } catch (err) {
    console.error('Error in Phase 10 setup:', err);
  } finally {
    await pool.end();
  }
}

setup();
