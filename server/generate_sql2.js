const fs = require('fs');
const path = require('path');

const files = [
  'setup_db_pg.js',
  'setup_fase3.js',
  'setup_fase4.js',
  'setup_fase5.js',
  'setup_fase6.js',
  'setup_fase8.js',
  'setup_fase9.js',
  'setup_fase10.js',
  'setup_fase11.js',
  'setup_participants.js',
  'seed.js'
];

let fullSql = `-- E-Procurement Supabase Migration Script\n-- Auto-generated Clean SQL\n\n`;

fullSql += `CREATE EXTENSION IF NOT EXISTS "uuid-ossp";\n`;
fullSql += `CREATE EXTENSION IF NOT EXISTS pgcrypto;\n\n`;

for (const file of files) {
  const filePath = path.join(__dirname, file);
  if (fs.existsSync(filePath)) {
    const content = fs.readFileSync(filePath, 'utf-8');
    
    // Support pool.query(`...`), client.query(`...`)
    const regex = /(?:pool|client)\.query\(\s*`([\s\S]*?)`\s*\)/g;
    let match;
    while ((match = regex.exec(content)) !== null) {
      let query = match[1].trim();
      if (!query.endsWith(';')) query += ';';
      fullSql += `-- From ${file}\n${query}\n\n`;
    }
  }
}

fs.writeFileSync(path.join(__dirname, '..', 'supabase_clean.sql'), fullSql);
console.log('supabase_clean.sql generated successfully.');
