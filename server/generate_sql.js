const fs = require('fs');
const path = require('path');

const files = [
  'setup.js', 'setup_fase2.js', 'setup_fase3.js', 'setup_fase4.js', 
  'setup_fase5.js', 'setup_fase6.js', 'setup_fase7.js', 'setup_fase8.js',
  'setup_fase9.js', 'setup_fase10.js', 'setup_fase11.js'
];

let fullSql = `-- E-Procurement Supabase Migration Script\n-- Auto-generated from local Node.js setup scripts\n\n`;

for (const file of files) {
  const filePath = path.join(__dirname, file);
  if (fs.existsSync(filePath)) {
    const content = fs.readFileSync(filePath, 'utf-8');
    // Extract everything between await pool.query(` and `)
    // and await client.query(` and `)
    const regex = /pool\.query\(\s*`([\s\S]*?)`\s*\)/g;
    let match;
    while ((match = regex.exec(content)) !== null) {
      // Remove trailing/leading spaces and add semicolon if missing
      let query = match[1].trim();
      if (!query.endsWith(';')) query += ';';
      fullSql += `${query}\n\n`;
    }
  }
}

// Add necessary extensions for Supabase (UUID)
const preamble = `
CREATE EXTENSION IF NOT EXISTS "uuid-ossp";
CREATE EXTENSION IF NOT EXISTS pgcrypto;

`;

// Tweak for Supabase RLS (Disable it globally or configure as needed for this project)
const postamble = `
-- By default, Supabase creates tables with RLS enabled if done via UI.
-- Since this is an Express app, RLS is handled in backend.
-- We can disable RLS for all tables if needed, or leave it.
`;

fs.writeFileSync(path.join(__dirname, '..', 'supabase_migration.sql'), preamble + fullSql + postamble);
console.log('supabase_migration.sql generated successfully.');
