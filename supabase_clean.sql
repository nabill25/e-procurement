-- E-Procurement Supabase Migration Script
-- Auto-generated Clean SQL

CREATE EXTENSION IF NOT EXISTS "uuid-ossp";
CREATE EXTENSION IF NOT EXISTS pgcrypto;

-- From setup_db_pg.js
CREATE TABLE IF NOT EXISTS users (
        id UUID DEFAULT gen_random_uuid() PRIMARY KEY,
        username VARCHAR(100) UNIQUE NOT NULL,
        password VARCHAR(255) NOT NULL,
        full_name VARCHAR(100) NOT NULL,
        role VARCHAR(50) NOT NULL,
        role_label VARCHAR(100),
        status VARCHAR(20) DEFAULT 'aktif',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
      );

      CREATE TABLE IF NOT EXISTS vendors (
        id UUID DEFAULT gen_random_uuid() PRIMARY KEY,
        user_id UUID REFERENCES users(id) ON DELETE CASCADE,
        company_name VARCHAR(255) NOT NULL,
        npwp VARCHAR(50) UNIQUE,
        company_type VARCHAR(100),
        city VARCHAR(100),
        performance_score DECIMAL(3,2) DEFAULT 0,
        status VARCHAR(50) DEFAULT 'pending',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
      );

      CREATE TABLE IF NOT EXISTS procurement_requests (
        id UUID DEFAULT gen_random_uuid() PRIMARY KEY,
        request_number VARCHAR(100) UNIQUE NOT NULL,
        title VARCHAR(255) NOT NULL,
        unit_kerja VARCHAR(100),
        category VARCHAR(100),
        estimated_value DECIMAL(15,2),
        budget_source VARCHAR(50),
        fiscal_year INT,
        status VARCHAR(50) DEFAULT 'draft',
        requester_id UUID REFERENCES users(id) ON DELETE SET NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
      );

      CREATE TABLE IF NOT EXISTS tenders (
        id UUID DEFAULT gen_random_uuid() PRIMARY KEY,
        procurement_request_id UUID REFERENCES procurement_requests(id) ON DELETE SET NULL,
        tender_number VARCHAR(100) UNIQUE NOT NULL,
        title VARCHAR(255) NOT NULL,
        category VARCHAR(100),
        method VARCHAR(50) DEFAULT 'tender',
        status VARCHAR(50) DEFAULT 'draft',
        pagu_anggaran DECIMAL(15,2),
        hps DECIMAL(15,2),
        ppk_id UUID REFERENCES users(id) ON DELETE SET NULL,
        pokja_lead_id UUID REFERENCES users(id) ON DELETE SET NULL,
        work_location VARCHAR(255),
        description TEXT,
        submission_deadline TIMESTAMP,
        winner_announcement TIMESTAMP,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
      );

      CREATE TABLE IF NOT EXISTS tender_participants (
        id UUID DEFAULT gen_random_uuid() PRIMARY KEY,
        tender_id UUID REFERENCES tenders(id) ON DELETE CASCADE,
        vendor_id UUID REFERENCES users(id) ON DELETE CASCADE,
        status VARCHAR(50) DEFAULT 'registered',
        bid_price DECIMAL(15,2),
        registered_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        UNIQUE(tender_id, vendor_id)
      );

      CREATE TABLE IF NOT EXISTS audit_logs (
        id UUID DEFAULT gen_random_uuid() PRIMARY KEY,
        user_id UUID REFERENCES users(id) ON DELETE SET NULL,
        action VARCHAR(255) NOT NULL,
        entity_type VARCHAR(100),
        entity_id UUID,
        description VARCHAR(500),
        ip_address VARCHAR(50),
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
      );

-- From setup_fase3.js
CREATE TABLE IF NOT EXISTS vendor_documents (
        id UUID PRIMARY KEY DEFAULT gen_random_uuid(),
        vendor_id UUID REFERENCES users(id) ON DELETE CASCADE,
        doc_type VARCHAR(50),
        doc_number VARCHAR(100),
        issue_date DATE,
        expiry_date DATE,
        file_path VARCHAR(255),
        status VARCHAR(50) DEFAULT 'verified',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
      );

-- From setup_fase3.js
CREATE TABLE IF NOT EXISTS vendor_experiences (
        id UUID PRIMARY KEY DEFAULT gen_random_uuid(),
        vendor_id UUID REFERENCES users(id) ON DELETE CASCADE,
        project_name VARCHAR(255),
        client_name VARCHAR(255),
        contract_value DECIMAL(15,2),
        start_date DATE,
        end_date DATE,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
      );

-- From setup_fase4.js
ALTER TABLE procurement_requests 
      ADD COLUMN IF NOT EXISTS kak_path VARCHAR(255),
      ADD COLUMN IF NOT EXISTS rab_path VARCHAR(255),
      ADD COLUMN IF NOT EXISTS nota_dinas_path VARCHAR(255),
      ADD COLUMN IF NOT EXISTS admin_notes TEXT,
      ADD COLUMN IF NOT EXISTS is_docs_complete BOOLEAN DEFAULT FALSE;

-- From setup_fase5.js
CREATE TABLE IF NOT EXISTS tender_objections (
        id UUID PRIMARY KEY DEFAULT gen_random_uuid(),
        tender_id UUID REFERENCES tenders(id) ON DELETE CASCADE,
        vendor_id UUID REFERENCES users(id) ON DELETE CASCADE,
        objection_text TEXT NOT NULL,
        attachment_path VARCHAR(255),
        response_text TEXT,
        response_attachment_path VARCHAR(255),
        status VARCHAR(50) DEFAULT 'submitted', -- 'submitted', 'responded'
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
      );

      CREATE TABLE IF NOT EXISTS contracts (
        id UUID PRIMARY KEY DEFAULT gen_random_uuid(),
        tender_id UUID REFERENCES tenders(id) ON DELETE CASCADE UNIQUE,
        vendor_id UUID REFERENCES users(id) ON DELETE CASCADE,
        contract_number VARCHAR(100) NOT NULL,
        contract_date DATE,
        contract_value NUMERIC(15, 2),
        spk_path VARCHAR(255),
        bast_path VARCHAR(255),
        status VARCHAR(50) DEFAULT 'draft', -- 'draft', 'signed', 'completed'
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
      );

-- From setup_fase6.js
ALTER TABLE users
      ADD COLUMN IF NOT EXISTS rating_avg NUMERIC(3, 2) DEFAULT 0.00,
      ADD COLUMN IF NOT EXISTS rating_count INT DEFAULT 0;

-- From setup_fase6.js
CREATE TABLE IF NOT EXISTS vendor_ratings (
        id UUID PRIMARY KEY DEFAULT gen_random_uuid(),
        vendor_id UUID REFERENCES users(id) ON DELETE CASCADE,
        tender_id UUID REFERENCES tenders(id) ON DELETE CASCADE,
        ppk_id UUID REFERENCES users(id) ON DELETE CASCADE,
        rating_score INT CHECK (rating_score >= 1 AND rating_score <= 5) NOT NULL,
        review_notes TEXT,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        UNIQUE(vendor_id, tender_id)
      );

-- From setup_fase8.js
CREATE TABLE IF NOT EXISTS tender_aanwijzing_chats (
        id UUID PRIMARY KEY DEFAULT gen_random_uuid(),
        tender_id UUID REFERENCES tenders(id) ON DELETE CASCADE,
        user_id UUID REFERENCES users(id) ON DELETE CASCADE,
        message TEXT NOT NULL,
        created_at TIMESTAMP WITH TIME ZONE DEFAULT CURRENT_TIMESTAMP
      );

-- From setup_fase9.js
ALTER TABLE vendors 
      ADD COLUMN IF NOT EXISTS pajak JSONB DEFAULT '[]'::jsonb,
      ADD COLUMN IF NOT EXISTS tenaga_ahli JSONB DEFAULT '[]'::jsonb,
      ADD COLUMN IF NOT EXISTS peralatan JSONB DEFAULT '[]'::jsonb,
      ADD COLUMN IF NOT EXISTS pengurus JSONB DEFAULT '[]'::jsonb;

-- From setup_fase10.js
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

-- From setup_fase10.js
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

-- From setup_fase10.js
CREATE TABLE IF NOT EXISTS purchasing_order_items (
        id UUID PRIMARY KEY DEFAULT gen_random_uuid(),
        order_id UUID REFERENCES purchasing_orders(id) ON DELETE CASCADE,
        item_id UUID REFERENCES katalog_items(id),
        quantity INTEGER NOT NULL CHECK (quantity > 0),
        price_at_purchase NUMERIC(15,2) NOT NULL
      );

-- From setup_fase11.js
ALTER TABLE procurement_requests 
      ADD COLUMN IF NOT EXISTS is_from_sap BOOLEAN DEFAULT false,
      ADD COLUMN IF NOT EXISTS sap_pr_number VARCHAR(100) UNIQUE;

-- From setup_participants.js
CREATE TABLE IF NOT EXISTS tender_participants (
        id UUID DEFAULT gen_random_uuid() PRIMARY KEY,
        tender_id UUID REFERENCES tenders(id) ON DELETE CASCADE,
        vendor_id UUID REFERENCES users(id) ON DELETE CASCADE,
        status VARCHAR(50) DEFAULT 'registered',
        registered_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        UNIQUE(tender_id, vendor_id)
      );

