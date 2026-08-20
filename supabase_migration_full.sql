--
-- PostgreSQL database dump
--

\restrict yEJPrDXMbssl8dKcnqavJKXG7bSYDZ59cugDh0FhC4H97Ata386oRfTtXNAU1Fh

-- Dumped from database version 18.4
-- Dumped by pg_dump version 18.4

SET statement_timeout = 0;
SET lock_timeout = 0;
SET idle_in_transaction_session_timeout = 0;
SET transaction_timeout = 0;
SET client_encoding = 'UTF8';
SET standard_conforming_strings = on;
SELECT pg_catalog.set_config('search_path', '', false);
SET check_function_bodies = false;
SET xmloption = content;
SET client_min_messages = warning;
SET row_security = off;

--
-- Name: public; Type: SCHEMA; Schema: -; Owner: -
--

CREATE SCHEMA public;


--
-- Name: SCHEMA public; Type: COMMENT; Schema: -; Owner: -
--

COMMENT ON SCHEMA public IS 'standard public schema';


SET default_tablespace = '';

SET default_table_access_method = heap;

--
-- Name: audit_logs; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.audit_logs (
    id uuid DEFAULT gen_random_uuid() NOT NULL,
    user_id uuid,
    action character varying(255) NOT NULL,
    entity_type character varying(100),
    entity_id uuid,
    description character varying(500),
    ip_address character varying(50),
    created_at timestamp without time zone DEFAULT CURRENT_TIMESTAMP
);


--
-- Name: contracts; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.contracts (
    id uuid DEFAULT gen_random_uuid() NOT NULL,
    tender_id uuid,
    vendor_id uuid,
    contract_number character varying(100) NOT NULL,
    contract_date date,
    contract_value numeric(15,2),
    spk_path character varying(255),
    bast_path character varying(255),
    status character varying(50) DEFAULT 'draft'::character varying,
    created_at timestamp without time zone DEFAULT CURRENT_TIMESTAMP,
    updated_at timestamp without time zone DEFAULT CURRENT_TIMESTAMP
);


--
-- Name: katalog_items; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.katalog_items (
    id uuid DEFAULT gen_random_uuid() NOT NULL,
    vendor_id uuid,
    item_name character varying(255) NOT NULL,
    description text,
    price numeric(15,2) NOT NULL,
    unit character varying(50) DEFAULT 'Pcs'::character varying,
    image_url text,
    created_at timestamp without time zone DEFAULT CURRENT_TIMESTAMP,
    updated_at timestamp without time zone DEFAULT CURRENT_TIMESTAMP
);


--
-- Name: procurement_requests; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.procurement_requests (
    id uuid DEFAULT gen_random_uuid() NOT NULL,
    request_number character varying(100) NOT NULL,
    title character varying(255) NOT NULL,
    unit_kerja character varying(100),
    category character varying(100),
    estimated_value numeric(15,2),
    budget_source character varying(50),
    fiscal_year integer,
    status character varying(50) DEFAULT 'draft'::character varying,
    requester_id uuid,
    created_at timestamp without time zone DEFAULT CURRENT_TIMESTAMP,
    kak_path character varying(255),
    rab_path character varying(255),
    nota_dinas_path character varying(255),
    admin_notes text,
    is_docs_complete boolean DEFAULT false,
    is_from_sap boolean DEFAULT false,
    sap_pr_number character varying(100)
);


--
-- Name: purchasing_order_items; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.purchasing_order_items (
    id uuid DEFAULT gen_random_uuid() NOT NULL,
    order_id uuid,
    item_id uuid,
    quantity integer NOT NULL,
    price_at_purchase numeric(15,2) NOT NULL,
    CONSTRAINT purchasing_order_items_quantity_check CHECK ((quantity > 0))
);


--
-- Name: purchasing_orders; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.purchasing_orders (
    id uuid DEFAULT gen_random_uuid() NOT NULL,
    buyer_id uuid,
    vendor_id uuid,
    total_amount numeric(15,2) NOT NULL,
    status character varying(50) DEFAULT 'pending'::character varying,
    delivery_address text,
    notes text,
    created_at timestamp without time zone DEFAULT CURRENT_TIMESTAMP,
    updated_at timestamp without time zone DEFAULT CURRENT_TIMESTAMP
);


--
-- Name: tender_aanwijzing_chats; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.tender_aanwijzing_chats (
    id uuid DEFAULT gen_random_uuid() NOT NULL,
    tender_id uuid,
    user_id uuid,
    message text NOT NULL,
    created_at timestamp with time zone DEFAULT CURRENT_TIMESTAMP
);


--
-- Name: tender_objections; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.tender_objections (
    id uuid DEFAULT gen_random_uuid() NOT NULL,
    tender_id uuid,
    vendor_id uuid,
    objection_text text NOT NULL,
    attachment_path character varying(255),
    response_text text,
    response_attachment_path character varying(255),
    status character varying(50) DEFAULT 'submitted'::character varying,
    created_at timestamp without time zone DEFAULT CURRENT_TIMESTAMP,
    updated_at timestamp without time zone DEFAULT CURRENT_TIMESTAMP
);


--
-- Name: tender_participants; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.tender_participants (
    id uuid DEFAULT gen_random_uuid() NOT NULL,
    tender_id uuid,
    vendor_id uuid,
    status character varying(50) DEFAULT 'registered'::character varying,
    bid_price numeric(15,2),
    registered_at timestamp without time zone DEFAULT CURRENT_TIMESTAMP,
    document_path character varying(500),
    is_evaluated boolean DEFAULT false,
    technical_score numeric(5,2),
    is_winner boolean DEFAULT false,
    evaluation_notes text
);


--
-- Name: tenders; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.tenders (
    id uuid DEFAULT gen_random_uuid() NOT NULL,
    procurement_request_id uuid,
    tender_number character varying(100) NOT NULL,
    title character varying(255) NOT NULL,
    category character varying(100),
    method character varying(50) DEFAULT 'tender'::character varying,
    status character varying(50) DEFAULT 'draft'::character varying,
    pagu_anggaran numeric(15,2),
    hps numeric(15,2),
    ppk_id uuid,
    pokja_lead_id uuid,
    work_location character varying(255),
    description text,
    submission_deadline timestamp without time zone,
    winner_announcement timestamp without time zone,
    created_at timestamp without time zone DEFAULT CURRENT_TIMESTAMP
);


--
-- Name: users; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.users (
    id uuid DEFAULT gen_random_uuid() NOT NULL,
    username character varying(100) NOT NULL,
    password character varying(255) NOT NULL,
    full_name character varying(100) NOT NULL,
    role character varying(50) NOT NULL,
    role_label character varying(100),
    status character varying(20) DEFAULT 'aktif'::character varying,
    created_at timestamp without time zone DEFAULT CURRENT_TIMESTAMP,
    rating_avg numeric(3,2) DEFAULT 0.00,
    rating_count integer DEFAULT 0
);


--
-- Name: vendor_documents; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.vendor_documents (
    id uuid DEFAULT gen_random_uuid() NOT NULL,
    vendor_id uuid,
    doc_type character varying(50),
    doc_number character varying(100),
    issue_date date,
    expiry_date date,
    file_path character varying(255),
    status character varying(50) DEFAULT 'verified'::character varying,
    created_at timestamp without time zone DEFAULT CURRENT_TIMESTAMP
);


--
-- Name: vendor_experiences; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.vendor_experiences (
    id uuid DEFAULT gen_random_uuid() NOT NULL,
    vendor_id uuid,
    project_name character varying(255),
    client_name character varying(255),
    contract_value numeric(15,2),
    start_date date,
    end_date date,
    created_at timestamp without time zone DEFAULT CURRENT_TIMESTAMP
);


--
-- Name: vendor_ratings; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.vendor_ratings (
    id uuid DEFAULT gen_random_uuid() NOT NULL,
    vendor_id uuid,
    tender_id uuid,
    ppk_id uuid,
    rating_score integer NOT NULL,
    review_notes text,
    created_at timestamp without time zone DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT vendor_ratings_rating_score_check CHECK (((rating_score >= 1) AND (rating_score <= 5)))
);


--
-- Name: vendors; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.vendors (
    id uuid DEFAULT gen_random_uuid() NOT NULL,
    user_id uuid,
    company_name character varying(255) NOT NULL,
    npwp character varying(50),
    company_type character varying(100),
    city character varying(100),
    performance_score numeric(3,2) DEFAULT 0,
    status character varying(50) DEFAULT 'pending'::character varying,
    created_at timestamp without time zone DEFAULT CURRENT_TIMESTAMP,
    pajak jsonb DEFAULT '[]'::jsonb,
    tenaga_ahli jsonb DEFAULT '[]'::jsonb,
    peralatan jsonb DEFAULT '[]'::jsonb,
    pengurus jsonb DEFAULT '[]'::jsonb
);


--
-- Data for Name: audit_logs; Type: TABLE DATA; Schema: public; Owner: -
--

COPY public.audit_logs (id, user_id, action, entity_type, entity_id, description, ip_address, created_at) FROM stdin;
\.


--
-- Data for Name: contracts; Type: TABLE DATA; Schema: public; Owner: -
--

COPY public.contracts (id, tender_id, vendor_id, contract_number, contract_date, contract_value, spk_path, bast_path, status, created_at, updated_at) FROM stdin;
\.


--
-- Data for Name: katalog_items; Type: TABLE DATA; Schema: public; Owner: -
--

COPY public.katalog_items (id, vendor_id, item_name, description, price, unit, image_url, created_at, updated_at) FROM stdin;
\.


--
-- Data for Name: procurement_requests; Type: TABLE DATA; Schema: public; Owner: -
--

COPY public.procurement_requests (id, request_number, title, unit_kerja, category, estimated_value, budget_source, fiscal_year, status, requester_id, created_at, kak_path, rab_path, nota_dinas_path, admin_notes, is_docs_complete, is_from_sap, sap_pr_number) FROM stdin;
063b0e1e-3c92-4e4c-ad65-d7027b8af911	PENGAJUAN/2025/090	Pengadaan PC Desktop Lab Komputer	Fasilkom UI	Barang	250000000.00	DIPA	2025	disetujui	a5ee671d-30b6-4d3e-982c-1374ec04b853	2026-08-20 12:45:22.38607	\N	\N	\N	\N	f	f	\N
\.


--
-- Data for Name: purchasing_order_items; Type: TABLE DATA; Schema: public; Owner: -
--

COPY public.purchasing_order_items (id, order_id, item_id, quantity, price_at_purchase) FROM stdin;
\.


--
-- Data for Name: purchasing_orders; Type: TABLE DATA; Schema: public; Owner: -
--

COPY public.purchasing_orders (id, buyer_id, vendor_id, total_amount, status, delivery_address, notes, created_at, updated_at) FROM stdin;
\.


--
-- Data for Name: tender_aanwijzing_chats; Type: TABLE DATA; Schema: public; Owner: -
--

COPY public.tender_aanwijzing_chats (id, tender_id, user_id, message, created_at) FROM stdin;
\.


--
-- Data for Name: tender_objections; Type: TABLE DATA; Schema: public; Owner: -
--

COPY public.tender_objections (id, tender_id, vendor_id, objection_text, attachment_path, response_text, response_attachment_path, status, created_at, updated_at) FROM stdin;
\.


--
-- Data for Name: tender_participants; Type: TABLE DATA; Schema: public; Owner: -
--

COPY public.tender_participants (id, tender_id, vendor_id, status, bid_price, registered_at, document_path, is_evaluated, technical_score, is_winner, evaluation_notes) FROM stdin;
\.


--
-- Data for Name: tenders; Type: TABLE DATA; Schema: public; Owner: -
--

COPY public.tenders (id, procurement_request_id, tender_number, title, category, method, status, pagu_anggaran, hps, ppk_id, pokja_lead_id, work_location, description, submission_deadline, winner_announcement, created_at) FROM stdin;
7dc1d0de-cdc3-40d7-803c-a316e3fc0c76	063b0e1e-3c92-4e4c-ad65-d7027b8af911	TENDER/2025/090	Pengadaan PC Desktop Lab Komputer	Barang	tender	draft	250000000.00	\N	54224a85-950f-4fd3-a8d9-303e2d6e27d4	604ac51f-a074-428b-b852-11d449f8b262	\N	\N	2026-08-25 12:45:22.390829	\N	2026-08-20 12:45:22.390829
\.


--
-- Data for Name: users; Type: TABLE DATA; Schema: public; Owner: -
--

COPY public.users (id, username, password, full_name, role, role_label, status, created_at, rating_avg, rating_count) FROM stdin;
a5ee671d-30b6-4d3e-982c-1374ec04b853	admin	$2b$10$W8UuIh9tngQE/vb6vZItGOws4KaG7y8v2JWPycV93YEVYba2IKd/u	Super Admin DPBJ	admin	Super Administrator	aktif	2026-08-20 12:45:22.370101	0.00	0
54224a85-950f-4fd3-a8d9-303e2d6e27d4	ppk	$2b$10$.I/xUkDdM6fdCeWnET3kqO3b3fJF6Uec8V2kjQ0FoLEyhJMUJ.lZi	Dr. Budi Santoso	ppk	Pejabat Pembuat Komitmen	aktif	2026-08-20 12:45:22.372779	0.00	0
604ac51f-a074-428b-b852-11d449f8b262	pokja	$2b$10$HMIheN2sTQqIrFPhpKaXcOYF4qNR4TlQw4uM0eTXE/4uLgxUvUGj6	Tim Pokja Pemilihan 1	pokja	Pokja Pemilihan	aktif	2026-08-20 12:45:22.373865	0.00	0
29be2cdf-b4c1-4368-a410-594a4aa154bc	vendor	$2b$10$mpfQ2zVaZxUNKw7sMGa2i.QOZ63o81cldpVj4EtV6aLKkxp.kj.Ga	PT Jaya Abadi	vendor	Penyedia/Vendor	aktif	2026-08-20 12:45:22.374693	0.00	0
\.


--
-- Data for Name: vendor_documents; Type: TABLE DATA; Schema: public; Owner: -
--

COPY public.vendor_documents (id, vendor_id, doc_type, doc_number, issue_date, expiry_date, file_path, status, created_at) FROM stdin;
\.


--
-- Data for Name: vendor_experiences; Type: TABLE DATA; Schema: public; Owner: -
--

COPY public.vendor_experiences (id, vendor_id, project_name, client_name, contract_value, start_date, end_date, created_at) FROM stdin;
\.


--
-- Data for Name: vendor_ratings; Type: TABLE DATA; Schema: public; Owner: -
--

COPY public.vendor_ratings (id, vendor_id, tender_id, ppk_id, rating_score, review_notes, created_at) FROM stdin;
\.


--
-- Data for Name: vendors; Type: TABLE DATA; Schema: public; Owner: -
--

COPY public.vendors (id, user_id, company_name, npwp, company_type, city, performance_score, status, created_at, pajak, tenaga_ahli, peralatan, pengurus) FROM stdin;
55bf17f7-770e-4fda-8675-f9d2b4e30fe2	29be2cdf-b4c1-4368-a410-594a4aa154bc	PT Jaya Abadi	01.234.567.8-901.000	Barang	Jakarta Selatan	0.00	terverifikasi	2026-08-20 12:45:22.375697	[]	[]	[]	[]
\.


--
-- Name: audit_logs audit_logs_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.audit_logs
    ADD CONSTRAINT audit_logs_pkey PRIMARY KEY (id);


--
-- Name: contracts contracts_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.contracts
    ADD CONSTRAINT contracts_pkey PRIMARY KEY (id);


--
-- Name: contracts contracts_tender_id_key; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.contracts
    ADD CONSTRAINT contracts_tender_id_key UNIQUE (tender_id);


--
-- Name: katalog_items katalog_items_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.katalog_items
    ADD CONSTRAINT katalog_items_pkey PRIMARY KEY (id);


--
-- Name: procurement_requests procurement_requests_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.procurement_requests
    ADD CONSTRAINT procurement_requests_pkey PRIMARY KEY (id);


--
-- Name: procurement_requests procurement_requests_request_number_key; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.procurement_requests
    ADD CONSTRAINT procurement_requests_request_number_key UNIQUE (request_number);


--
-- Name: procurement_requests procurement_requests_sap_pr_number_key; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.procurement_requests
    ADD CONSTRAINT procurement_requests_sap_pr_number_key UNIQUE (sap_pr_number);


--
-- Name: purchasing_order_items purchasing_order_items_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.purchasing_order_items
    ADD CONSTRAINT purchasing_order_items_pkey PRIMARY KEY (id);


--
-- Name: purchasing_orders purchasing_orders_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.purchasing_orders
    ADD CONSTRAINT purchasing_orders_pkey PRIMARY KEY (id);


--
-- Name: tender_aanwijzing_chats tender_aanwijzing_chats_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.tender_aanwijzing_chats
    ADD CONSTRAINT tender_aanwijzing_chats_pkey PRIMARY KEY (id);


--
-- Name: tender_objections tender_objections_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.tender_objections
    ADD CONSTRAINT tender_objections_pkey PRIMARY KEY (id);


--
-- Name: tender_participants tender_participants_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.tender_participants
    ADD CONSTRAINT tender_participants_pkey PRIMARY KEY (id);


--
-- Name: tender_participants tender_participants_tender_id_vendor_id_key; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.tender_participants
    ADD CONSTRAINT tender_participants_tender_id_vendor_id_key UNIQUE (tender_id, vendor_id);


--
-- Name: tenders tenders_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.tenders
    ADD CONSTRAINT tenders_pkey PRIMARY KEY (id);


--
-- Name: tenders tenders_tender_number_key; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.tenders
    ADD CONSTRAINT tenders_tender_number_key UNIQUE (tender_number);


--
-- Name: users users_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.users
    ADD CONSTRAINT users_pkey PRIMARY KEY (id);


--
-- Name: users users_username_key; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.users
    ADD CONSTRAINT users_username_key UNIQUE (username);


--
-- Name: vendor_documents vendor_documents_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.vendor_documents
    ADD CONSTRAINT vendor_documents_pkey PRIMARY KEY (id);


--
-- Name: vendor_experiences vendor_experiences_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.vendor_experiences
    ADD CONSTRAINT vendor_experiences_pkey PRIMARY KEY (id);


--
-- Name: vendor_ratings vendor_ratings_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.vendor_ratings
    ADD CONSTRAINT vendor_ratings_pkey PRIMARY KEY (id);


--
-- Name: vendor_ratings vendor_ratings_vendor_id_tender_id_key; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.vendor_ratings
    ADD CONSTRAINT vendor_ratings_vendor_id_tender_id_key UNIQUE (vendor_id, tender_id);


--
-- Name: vendors vendors_npwp_key; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.vendors
    ADD CONSTRAINT vendors_npwp_key UNIQUE (npwp);


--
-- Name: vendors vendors_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.vendors
    ADD CONSTRAINT vendors_pkey PRIMARY KEY (id);


--
-- Name: audit_logs audit_logs_user_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.audit_logs
    ADD CONSTRAINT audit_logs_user_id_fkey FOREIGN KEY (user_id) REFERENCES public.users(id) ON DELETE SET NULL;


--
-- Name: contracts contracts_tender_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.contracts
    ADD CONSTRAINT contracts_tender_id_fkey FOREIGN KEY (tender_id) REFERENCES public.tenders(id) ON DELETE CASCADE;


--
-- Name: contracts contracts_vendor_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.contracts
    ADD CONSTRAINT contracts_vendor_id_fkey FOREIGN KEY (vendor_id) REFERENCES public.users(id) ON DELETE CASCADE;


--
-- Name: katalog_items katalog_items_vendor_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.katalog_items
    ADD CONSTRAINT katalog_items_vendor_id_fkey FOREIGN KEY (vendor_id) REFERENCES public.users(id) ON DELETE CASCADE;


--
-- Name: procurement_requests procurement_requests_requester_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.procurement_requests
    ADD CONSTRAINT procurement_requests_requester_id_fkey FOREIGN KEY (requester_id) REFERENCES public.users(id) ON DELETE SET NULL;


--
-- Name: purchasing_order_items purchasing_order_items_item_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.purchasing_order_items
    ADD CONSTRAINT purchasing_order_items_item_id_fkey FOREIGN KEY (item_id) REFERENCES public.katalog_items(id);


--
-- Name: purchasing_order_items purchasing_order_items_order_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.purchasing_order_items
    ADD CONSTRAINT purchasing_order_items_order_id_fkey FOREIGN KEY (order_id) REFERENCES public.purchasing_orders(id) ON DELETE CASCADE;


--
-- Name: purchasing_orders purchasing_orders_buyer_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.purchasing_orders
    ADD CONSTRAINT purchasing_orders_buyer_id_fkey FOREIGN KEY (buyer_id) REFERENCES public.users(id);


--
-- Name: purchasing_orders purchasing_orders_vendor_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.purchasing_orders
    ADD CONSTRAINT purchasing_orders_vendor_id_fkey FOREIGN KEY (vendor_id) REFERENCES public.users(id);


--
-- Name: tender_aanwijzing_chats tender_aanwijzing_chats_tender_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.tender_aanwijzing_chats
    ADD CONSTRAINT tender_aanwijzing_chats_tender_id_fkey FOREIGN KEY (tender_id) REFERENCES public.tenders(id) ON DELETE CASCADE;


--
-- Name: tender_aanwijzing_chats tender_aanwijzing_chats_user_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.tender_aanwijzing_chats
    ADD CONSTRAINT tender_aanwijzing_chats_user_id_fkey FOREIGN KEY (user_id) REFERENCES public.users(id) ON DELETE CASCADE;


--
-- Name: tender_objections tender_objections_tender_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.tender_objections
    ADD CONSTRAINT tender_objections_tender_id_fkey FOREIGN KEY (tender_id) REFERENCES public.tenders(id) ON DELETE CASCADE;


--
-- Name: tender_objections tender_objections_vendor_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.tender_objections
    ADD CONSTRAINT tender_objections_vendor_id_fkey FOREIGN KEY (vendor_id) REFERENCES public.users(id) ON DELETE CASCADE;


--
-- Name: tender_participants tender_participants_tender_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.tender_participants
    ADD CONSTRAINT tender_participants_tender_id_fkey FOREIGN KEY (tender_id) REFERENCES public.tenders(id) ON DELETE CASCADE;


--
-- Name: tender_participants tender_participants_vendor_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.tender_participants
    ADD CONSTRAINT tender_participants_vendor_id_fkey FOREIGN KEY (vendor_id) REFERENCES public.users(id) ON DELETE CASCADE;


--
-- Name: tenders tenders_pokja_lead_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.tenders
    ADD CONSTRAINT tenders_pokja_lead_id_fkey FOREIGN KEY (pokja_lead_id) REFERENCES public.users(id) ON DELETE SET NULL;


--
-- Name: tenders tenders_ppk_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.tenders
    ADD CONSTRAINT tenders_ppk_id_fkey FOREIGN KEY (ppk_id) REFERENCES public.users(id) ON DELETE SET NULL;


--
-- Name: tenders tenders_procurement_request_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.tenders
    ADD CONSTRAINT tenders_procurement_request_id_fkey FOREIGN KEY (procurement_request_id) REFERENCES public.procurement_requests(id) ON DELETE SET NULL;


--
-- Name: vendor_documents vendor_documents_vendor_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.vendor_documents
    ADD CONSTRAINT vendor_documents_vendor_id_fkey FOREIGN KEY (vendor_id) REFERENCES public.users(id) ON DELETE CASCADE;


--
-- Name: vendor_experiences vendor_experiences_vendor_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.vendor_experiences
    ADD CONSTRAINT vendor_experiences_vendor_id_fkey FOREIGN KEY (vendor_id) REFERENCES public.users(id) ON DELETE CASCADE;


--
-- Name: vendor_ratings vendor_ratings_ppk_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.vendor_ratings
    ADD CONSTRAINT vendor_ratings_ppk_id_fkey FOREIGN KEY (ppk_id) REFERENCES public.users(id) ON DELETE CASCADE;


--
-- Name: vendor_ratings vendor_ratings_tender_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.vendor_ratings
    ADD CONSTRAINT vendor_ratings_tender_id_fkey FOREIGN KEY (tender_id) REFERENCES public.tenders(id) ON DELETE CASCADE;


--
-- Name: vendor_ratings vendor_ratings_vendor_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.vendor_ratings
    ADD CONSTRAINT vendor_ratings_vendor_id_fkey FOREIGN KEY (vendor_id) REFERENCES public.users(id) ON DELETE CASCADE;


--
-- Name: vendors vendors_user_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.vendors
    ADD CONSTRAINT vendors_user_id_fkey FOREIGN KEY (user_id) REFERENCES public.users(id) ON DELETE CASCADE;


--
-- PostgreSQL database dump complete
--

\unrestrict yEJPrDXMbssl8dKcnqavJKXG7bSYDZ59cugDh0FhC4H97Ata386oRfTtXNAU1Fh

