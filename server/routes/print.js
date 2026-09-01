const express = require('express');
const router = express.Router();
const { pool } = require('../db');
const { requireAuth } = require('../lib/authMiddleware');
const { terbilang, kalimatTanggalTerbilang, formatTanggalIndo } = require('../lib/tanggalTerbilang');
const { FORMULA_CATEGORIES, computeCategoryFinalScore } = require('../lib/evalFormula');

router.use(requireAuth);

// Semua endpoint di sini murni AGREGASI DATA untuk halaman cetak dokumen resmi (BAPP, Berita
// Acara Aanwijzing, Pakta Integritas, SPPBJ). Sistem lama merender dokumen ini jadi PDF di
// server (mPDF); sistem baru sengaja memindahkan rendering ke frontend (halaman khusus cetak,
// lihat src/pages/print/*.jsx) supaya bisa dicetak/disimpan PDF lewat browser (window.print()),
// tanpa perlu library PDF berat/Chromium di backend - lihat catatan keputusan di CLAUDE.md.

async function getTenderBase(tenderId) {
  const r = await pool.query(`
    SELECT t.*, u_ppk.full_name AS ppk_name, u_pokja.full_name AS pokja_name
    FROM tenders t
    LEFT JOIN users u_ppk ON t.ppk_id = u_ppk.id
    LEFT JOIN users u_pokja ON t.pokja_lead_id = u_pokja.id
    WHERE t.id = $1
  `, [tenderId]);
  return r.rows[0] || null;
}

// ── GET /api/print/tenders/:id/pembukaan-penawaran ──
// Data untuk Berita Acara Pembukaan Penawaran (BAPP).
router.get('/tenders/:id/pembukaan-penawaran', async (req, res) => {
  try {
    const { id } = req.params;
    const tender = await getTenderBase(id);
    if (!tender) return res.status(404).json({ success: false, message: 'Tender tidak ditemukan.' });

    const peserta = await pool.query(`
      SELECT tp.id, tp.bid_price, tp.document_path, tp.registered_at, v.company_name
      FROM tender_participants tp
      JOIN vendors v ON tp.vendor_id = v.user_id
      WHERE tp.tender_id = $1 AND tp.bid_price IS NOT NULL
      ORDER BY v.company_name ASC
    `, [id]);

    const dokumen = await pool.query(`
      SELECT document_type, name, file_size, created_at, uploaded_by
      FROM tender_documents WHERE tender_id = $1 ORDER BY created_at ASC
    `, [id]);

    const panitiaResult = await pool.query(`
      SELECT nama, jabatan, is_ketua FROM tender_panitia WHERE tender_id = $1 ORDER BY is_ketua DESC, nama ASC
    `, [id]);

    res.json({
      success: true,
      data: {
        tender: {
          nomor: `${tender.tender_number}/BA.PEMBUKAAN/${new Date(tender.created_at).getFullYear()}`,
          title: tender.title,
          hps: tender.hps,
          method: tender.method,
        },
        kalimat_tanggal: kalimatTanggalTerbilang(new Date()),
        peserta: peserta.rows,
        dokumen: dokumen.rows,
        panitia: panitiaResult.rows.length ? panitiaResult.rows : [{ nama: tender.pokja_name || tender.ppk_name || '-', jabatan: 'Pokja Pemilihan', is_ketua: true }],
      },
    });
  } catch (err) {
    console.error('[GET /print/tenders/:id/pembukaan-penawaran]', err);
    res.status(500).json({ success: false, message: 'Gagal mengambil data cetak.' });
  }
});

// ── GET /api/print/tenders/:id/aanwijzing ──
// Data untuk Berita Acara Aanwijzing (rapat penjelasan).
router.get('/tenders/:id/aanwijzing', async (req, res) => {
  try {
    const { id } = req.params;
    const tender = await getTenderBase(id);
    if (!tender) return res.status(404).json({ success: false, message: 'Tender tidak ditemukan.' });

    const chats = await pool.query(`
      SELECT c.message, c.created_at, c.is_confirmation, u.full_name, u.role
      FROM tender_aanwijzing_chats c
      LEFT JOIN users u ON c.user_id = u.id
      WHERE c.tender_id = $1
      ORDER BY c.created_at ASC
    `, [id]);

    const konfirmasi = chats.rows.filter(c => c.is_confirmation);
    const tanyaJawab = chats.rows.filter(c => !c.is_confirmation);

    const panitiaResult = await pool.query(`
      SELECT nama, jabatan, is_ketua FROM tender_panitia WHERE tender_id = $1 ORDER BY is_ketua DESC, nama ASC
    `, [id]);

    const pesertaResult = await pool.query(`
      SELECT DISTINCT v.company_name
      FROM tender_participants tp
      JOIN vendors v ON tp.vendor_id = v.user_id
      WHERE tp.tender_id = $1
      ORDER BY v.company_name ASC
    `, [id]);

    res.json({
      success: true,
      data: {
        tender: {
          nomor: `${tender.tender_number}/BA.AANWIJZING/${new Date(tender.created_at).getFullYear()}`,
          title: tender.title,
        },
        kalimat_tanggal: kalimatTanggalTerbilang(new Date()),
        panitia: panitiaResult.rows.length ? panitiaResult.rows : [{ nama: tender.pokja_name || tender.ppk_name || '-', jabatan: 'Pokja Pemilihan', is_ketua: true }],
        peserta: pesertaResult.rows,
        konfirmasi_hadir: konfirmasi.length,
        tanya_jawab: tanyaJawab.map(c => ({ nama: c.full_name, role: c.role, pesan: c.message, tanggal: c.created_at })),
      },
    });
  } catch (err) {
    console.error('[GET /print/tenders/:id/aanwijzing]', err);
    res.status(500).json({ success: false, message: 'Gagal mengambil data cetak.' });
  }
});

// ── GET /api/print/tenders/:id/pakta-integritas dan /pakta-integritas/:vendorId ──
// Data untuk Pakta Integritas. Kalau :vendorId diberikan, khusus 1 vendor (versi rekanan);
// kalau tidak, versi panitia (semua yang sudah validasi jenis='panitia').
// (Express 5 / path-to-regexp v8 tidak lagi mendukung sintaks ":param?" - dipecah jadi 2 route.)
async function handlePaktaIntegritas(req, res) {
  try {
    const { id, vendorId } = req.params;
    const tender = await getTenderBase(id);
    if (!tender) return res.status(404).json({ success: false, message: 'Tender tidak ditemukan.' });

    let rows;
    if (vendorId) {
      const r = await pool.query(`
        SELECT pi.*, v.company_name, v.npwp
        FROM tender_pakta_integritas pi
        JOIN vendors v ON pi.user_id = v.user_id
        WHERE pi.tender_id = $1 AND pi.user_id = $2
      `, [id, vendorId]);
      rows = r.rows;
    } else {
      const r = await pool.query(`
        SELECT pi.*, u.full_name
        FROM tender_pakta_integritas pi
        LEFT JOIN users u ON pi.user_id = u.id
        WHERE pi.tender_id = $1 AND pi.jenis = 'panitia'
      `, [id]);
      rows = r.rows;
    }

    if (!rows.length) return res.status(404).json({ success: false, message: 'Pakta integritas belum divalidasi untuk pihak ini.' });

    res.json({
      success: true,
      data: {
        tender: { title: tender.title, nomor: tender.tender_number },
        kalimat_tanggal: kalimatTanggalTerbilang(rows[0].created_at),
        pihak: rows.map(r => ({
          nama: r.company_name || r.full_name || '-',
          npwp: r.npwp || null,
          kode_validasi: r.kode,
          tanggal: r.created_at,
        })),
      },
    });
  } catch (err) {
    console.error('[GET /print/tenders/:id/pakta-integritas]', err);
    res.status(500).json({ success: false, message: 'Gagal mengambil data cetak.' });
  }
}
router.get('/tenders/:id/pakta-integritas', handlePaktaIntegritas);
router.get('/tenders/:id/pakta-integritas/:vendorId', handlePaktaIntegritas);

// ── GET /api/print/tenders/:id/sppbj ──
// Data untuk SPPBJ (Surat Penunjukan Penyedia Barang/Jasa).
router.get('/tenders/:id/sppbj', async (req, res) => {
  try {
    const { id } = req.params;
    const tender = await getTenderBase(id);
    if (!tender) return res.status(404).json({ success: false, message: 'Tender tidak ditemukan.' });

    const c = await pool.query(`
      SELECT ct.*, v.company_name, v.npwp, v.province, v.city
      FROM contracts ct
      JOIN vendors v ON ct.vendor_id = v.user_id
      WHERE ct.tender_id = $1
    `, [id]);
    if (!c.rows.length) return res.status(404).json({ success: false, message: 'Kontrak/SPPBJ belum dibuat untuk tender ini.' });
    const contract = c.rows[0];

    if (!contract.sppbj_code) {
      return res.status(400).json({ success: false, message: 'SPPBJ belum diterbitkan untuk kontrak ini.' });
    }

    res.json({
      success: true,
      data: {
        tender: { title: tender.title, nomor: tender.tender_number, hps: tender.hps },
        kalimat_tanggal: kalimatTanggalTerbilang(contract.sppbj_date),
        sppbj: {
          kode: contract.sppbj_code,
          tanggal: formatTanggalIndo(contract.sppbj_date),
          nilai: contract.sppbj_nilai,
          nilai_terbilang: contract.sppbj_nilai ? terbilang(Math.floor(contract.sppbj_nilai)) : null,
          direktur_nama: contract.sppbj_direktur_nama,
          direktur_jabatan: contract.sppbj_direktur_jabatan,
          direktur_alamat: contract.sppbj_direktur_alamat,
          direktur_kota: contract.sppbj_direktur_kota,
          pejabat_berwenang: contract.sppbj_pejabat_berwenang,
          pejabat_nip: contract.sppbj_pejabat_nip,
          pejabat_jabatan: contract.sppbj_pejabat_jabatan,
          pelaksanaan_dari: formatTanggalIndo(contract.sppbj_pelaksanaan_dari),
          pelaksanaan_sampai: formatTanggalIndo(contract.sppbj_pelaksanaan_sampai),
          jaminan_pelaksana: contract.sppbj_jaminan_pelaksana,
          jaminan_persen: contract.sppbj_jaminan_persen,
          jaminan_nilai: contract.sppbj_jaminan_nilai,
        },
        vendor: {
          nama: contract.company_name,
          npwp: contract.npwp,
          alamat: [contract.city, contract.province].filter(Boolean).join(', ') || '-',
        },
      },
    });
  } catch (err) {
    console.error('[GET /print/tenders/:id/sppbj]', err);
    res.status(500).json({ success: false, message: 'Gagal mengambil data cetak.' });
  }
});

// ── GET /api/print/tenders/:id/kontrak ──
// Data Kontrak/SPK, padanan dokumen "DATA KONTRAK" (eproc/application/views/report/kontrak.php)
// di sistem lama: data pokok kontrak + deliverable pekerjaan + termin pembayaran + SLA (kalau ada).
router.get('/tenders/:id/kontrak', async (req, res) => {
  try {
    const { id } = req.params;
    const tender = await getTenderBase(id);
    if (!tender) return res.status(404).json({ success: false, message: 'Tender tidak ditemukan.' });

    const c = await pool.query(`
      SELECT ct.*, v.company_name, v.npwp, v.phone, v.email, v.province, v.city
      FROM contracts ct
      JOIN vendors v ON ct.vendor_id = v.user_id
      WHERE ct.tender_id = $1
    `, [id]);
    if (!c.rows.length) return res.status(404).json({ success: false, message: 'Kontrak belum dibuat untuk tender ini.' });
    const contract = c.rows[0];

    const nomorLegal = contract.legal_nomor_pks || contract.spk_code || contract.contract_number;
    if (!nomorLegal) {
      return res.status(400).json({ success: false, message: 'Nomor SPK/PKS belum diisi untuk kontrak ini.' });
    }

    const deliverables = await pool.query(
      `SELECT scope, deliverable_name, progress_percent, status FROM contract_deliverables WHERE contract_id = $1 ORDER BY created_at ASC`,
      [contract.id]
    );
    const paymentTerms = await pool.query(
      `SELECT term_name, amount, progress_percent, status FROM contract_payment_terms WHERE contract_id = $1 ORDER BY created_at ASC`,
      [contract.id]
    );
    const sla = await pool.query(
      `SELECT availability, waktu, denda, biaya_maintenance, nilai_denda FROM contract_sla WHERE contract_id = $1 ORDER BY created_at ASC`,
      [contract.id]
    );

    res.json({
      success: true,
      data: {
        tender: { title: tender.title, nomor: tender.tender_number },
        kalimat_tanggal: kalimatTanggalTerbilang(contract.legal_tanggal || contract.contract_date),
        kontrak: {
          nomor_legal: nomorLegal,
          jenis_dokumen: contract.dokumen_jenis === 'pks' ? 'PKS' : 'SPK',
          tanggal_legal: formatTanggalIndo(contract.legal_tanggal || contract.contract_date),
          nilai: contract.contract_value,
          metode_pembayaran: contract.metode_pembayaran,
          jenis_pengadaan: contract.jenis_pengadaan,
          jenis_pekerjaan: contract.jenis_pekerjaan,
          jenis_kontrak: contract.jenis_kontrak,
          waktu_pelaksanaan_dari: formatTanggalIndo(contract.waktu_pelaksanaan_dari),
          waktu_pelaksanaan_sampai: formatTanggalIndo(contract.waktu_pelaksanaan_sampai),
          lingkup_pekerjaan: contract.lingkup_pekerjaan,
          pihak1_nama: contract.pihak1_nama,
          pihak1_jabatan: contract.pihak1_jabatan,
          pihak2_nama: contract.pihak2_nama || contract.company_name,
          pihak2_jabatan: contract.pihak2_jabatan,
        },
        vendor: {
          nama: contract.company_name,
          npwp: contract.npwp,
          telepon: contract.phone,
          email: contract.email,
          alamat: [contract.city, contract.province].filter(Boolean).join(', ') || '-',
        },
        deliverables: deliverables.rows,
        payment_terms: paymentTerms.rows,
        sla: sla.rows,
      },
    });
  } catch (err) {
    console.error('[GET /print/tenders/:id/kontrak]', err);
    res.status(500).json({ success: false, message: 'Gagal mengambil data cetak.' });
  }
});

async function getEvaluatedPeserta(tenderId) {
  const r = await pool.query(`
    SELECT tp.vendor_id, tp.is_winner, tp.technical_score, tp.evaluation_notes, v.company_name
    FROM tender_participants tp
    JOIN vendors v ON tp.vendor_id = v.user_id
    WHERE tp.tender_id = $1 AND tp.bid_price IS NOT NULL
    ORDER BY v.company_name ASC
  `, [tenderId]);
  return r.rows;
}

// ── GET /api/print/tenders/:id/evaluasi-kualifikasi/:category ──
// Padanan salah satu dari 7 laporan "evaluasi_kualifikasi_*" di sistem lama (administrasi,
// rekening koran, pengalaman, personil, peralatan, sertifikat, SKK) - digabung jadi SATU endpoint
// generik yang menerima kategori apa saja (sesuai kriteria yang dibuat Pokja lewat modul Evaluasi
// Tender), karena sistem baru sudah memakai satu model data yang sama untuk semua kategori.
router.get('/tenders/:id/evaluasi-kualifikasi/:category', async (req, res) => {
  try {
    const { id, category } = req.params;
    const tender = await getTenderBase(id);
    if (!tender) return res.status(404).json({ success: false, message: 'Tender tidak ditemukan.' });

    const criteriaResult = await pool.query(
      'SELECT * FROM tender_eval_criteria WHERE tender_id = $1 AND category = $2 ORDER BY order_index ASC, created_at ASC',
      [id, category]
    );
    if (!criteriaResult.rows.length) {
      return res.status(404).json({ success: false, message: 'Belum ada kriteria evaluasi untuk kategori ini.' });
    }

    const peserta = await getEvaluatedPeserta(id);
    const isFormula = FORMULA_CATEGORIES.includes(category);

    let rows;
    if (isFormula) {
      const itemsResult = await pool.query(`
        SELECT si.* FROM tender_eval_score_items si
        JOIN tender_eval_criteria c ON c.id = si.criteria_id
        WHERE c.tender_id = $1 AND c.category = $2
      `, [id, category]);
      rows = peserta.map(p => ({
        company_name: p.company_name,
        cells: criteriaResult.rows.map(c => ({
          criteria_name: c.name,
          items: itemsResult.rows.filter(it => it.vendor_id === p.vendor_id && it.criteria_id === c.id)
            .map(it => `${it.item_name} (${it.suitability || it.suitability_value})`),
        })),
      }));
    } else {
      const scoresResult = await pool.query(`
        SELECT s.* FROM tender_eval_scores s
        JOIN tender_eval_criteria c ON c.id = s.criteria_id
        WHERE c.tender_id = $1 AND c.category = $2
      `, [id, category]);
      rows = peserta.map(p => ({
        company_name: p.company_name,
        cells: criteriaResult.rows.map(c => {
          const s = scoresResult.rows.find(x => x.vendor_id === p.vendor_id && x.criteria_id === c.id);
          return { criteria_name: c.name, score: s ? s.score : null, meets: s ? s.meets_requirement : null, notes: s ? s.notes : null };
        }),
      }));
    }

    res.json({
      success: true,
      data: {
        tender: { title: tender.title, nomor: tender.tender_number },
        category,
        is_formula: isFormula,
        criteria: criteriaResult.rows.map(c => c.name),
        rows,
      },
    });
  } catch (err) {
    console.error('[GET /print/tenders/:id/evaluasi-kualifikasi/:category]', err);
    res.status(500).json({ success: false, message: 'Gagal mengambil data cetak.' });
  }
});

// ── GET /api/print/tenders/:id/evaluasi-rekapitulasi ──
// Padanan "evaluasi_kualifikasi_rekapitulasi_excel.php" - rekap nilai akhir SEMUA kategori
// evaluasi kualifikasi per vendor jadi satu tabel, plus status lulus/tidak.
router.get('/tenders/:id/evaluasi-rekapitulasi', async (req, res) => {
  try {
    const { id } = req.params;
    const tender = await getTenderBase(id);
    if (!tender) return res.status(404).json({ success: false, message: 'Tender tidak ditemukan.' });

    const criteriaResult = await pool.query(
      'SELECT * FROM tender_eval_criteria WHERE tender_id = $1 ORDER BY category ASC, order_index ASC',
      [id]
    );
    if (!criteriaResult.rows.length) {
      return res.status(404).json({ success: false, message: 'Belum ada kriteria evaluasi untuk tender ini.' });
    }
    const categories = [...new Set(criteriaResult.rows.map(c => c.category))];

    const peserta = await getEvaluatedPeserta(id);
    const scoresResult = await pool.query(`
      SELECT s.* FROM tender_eval_scores s
      JOIN tender_eval_criteria c ON c.id = s.criteria_id WHERE c.tender_id = $1
    `, [id]);
    const itemsResult = await pool.query(`
      SELECT si.* FROM tender_eval_score_items si
      JOIN tender_eval_criteria c ON c.id = si.criteria_id WHERE c.tender_id = $1
    `, [id]);
    const configResult = await pool.query('SELECT * FROM tender_eval_category_config WHERE tender_id = $1', [id]);

    const rows = peserta.map(p => {
      const perCategory = categories.map(cat => {
        const criteriaOfCat = criteriaResult.rows.filter(c => c.category === cat);
        const itemsByCriteria = {};
        const scoreByCriteria = {};
        criteriaOfCat.forEach(c => {
          itemsByCriteria[c.id] = itemsResult.rows.filter(it => it.criteria_id === c.id && it.vendor_id === p.vendor_id);
          const s = scoresResult.rows.find(x => x.criteria_id === c.id && x.vendor_id === p.vendor_id);
          scoreByCriteria[c.id] = s ? s.score : null;
        });
        const cfg = configResult.rows.find(c => c.category === cat);
        const maxScore = cfg ? Number(cfg.max_score) : 100;
        const { final_score } = computeCategoryFinalScore(cat, criteriaOfCat, itemsByCriteria, scoreByCriteria, maxScore);
        return { category: cat, final_score };
      });
      const rata2 = perCategory.length ? Math.round(perCategory.reduce((s, c) => s + c.final_score, 0) / perCategory.length) : 0;
      return {
        company_name: p.company_name,
        per_category: perCategory,
        nilai_akhir: rata2,
        lulus: p.is_winner === true || (p.technical_score !== null && Number(p.technical_score) > 0),
        keterangan: p.evaluation_notes,
      };
    });

    res.json({
      success: true,
      data: {
        tender: { title: tender.title, nomor: tender.tender_number },
        categories,
        rows,
      },
    });
  } catch (err) {
    console.error('[GET /print/tenders/:id/evaluasi-rekapitulasi]', err);
    res.status(500).json({ success: false, message: 'Gagal mengambil data cetak.' });
  }
});

// ── GET /api/print/pengajuan/:id ──
// Padanan dokumen "PERMOHONAN PAKET USULAN DAN ANALISA KEBUTUHAN" (permohonan_paket_usulan_
// admin_cetak.php) - dibuat sebagai dokumen detail SATU pengajuan (bukan daftar/list seperti versi
// sistem lama, karena daftarnya sudah bisa dilihat & difilter langsung di halaman Pengajuan),
// mencakup data usulan, analisa kebutuhan & pasar, dan status persetujuan.
router.get('/pengajuan/:id', async (req, res) => {
  try {
    const { id } = req.params;
    const r = await pool.query(`
      SELECT pr.*, u.full_name AS requester_name
      FROM procurement_requests pr
      LEFT JOIN users u ON pr.requester_id = u.id
      WHERE pr.id = $1
    `, [id]);
    if (!r.rows.length) return res.status(404).json({ success: false, message: 'Pengajuan tidak ditemukan.' });
    const req_ = r.rows[0];

    const approvals = await pool.query(`
      SELECT a.approved, a.created_at, u.full_name AS approved_by_name
      FROM procurement_request_approvals a
      LEFT JOIN users u ON a.approved_by = u.id
      WHERE a.procurement_request_id = $1
      ORDER BY a.created_at ASC
    `, [id]);

    const checklist = await pool.query(`
      SELECT COUNT(*) FILTER (WHERE c.approved) AS terpenuhi, COUNT(*) AS total
      FROM procurement_request_checklist c WHERE c.procurement_request_id = $1
    `, [id]);

    res.json({
      success: true,
      data: {
        kalimat_tanggal: kalimatTanggalTerbilang(req_.created_at),
        pengajuan: {
          nomor: req_.request_number,
          title: req_.title,
          unit_kerja: req_.unit_kerja,
          category: req_.category,
          fiscal_year: req_.fiscal_year,
          estimated_value: req_.estimated_value,
          budget_source: req_.budget_source,
          status: req_.status,
          description: req_.description,
          technical_spec: req_.technical_spec,
          quantity: req_.quantity,
          unit_of_measure: req_.unit_of_measure,
          needed_by_date: formatTanggalIndo(req_.needed_by_date),
          requester_name: req_.requester_name,
          komoditas: req_.komoditas,
          analisa_kebutuhan: req_.analisa_kebutuhan,
          analisa_pasar: req_.analisa_pasar,
          risiko_teridentifikasi: req_.risiko_teridentifikasi,
          risiko_keterangan: req_.risiko_keterangan,
        },
        approvals: approvals.rows,
        checklist: checklist.rows[0],
      },
    });
  } catch (err) {
    console.error('[GET /print/pengajuan/:id]', err);
    res.status(500).json({ success: false, message: 'Gagal mengambil data cetak.' });
  }
});

// ── GET /api/print/vendors/:id/skt ──
// Padanan dokumen "Surat Keterangan Terdaftar (SKT)" (eproc/application/views/report/vms.php,
// halaman pertama - halaman kedua di sistem lama adalah checklist kelengkapan dokumen yang
// TIDAK ditiru karena field kelengkapannya tidak punya padanan 1:1 di sistem baru).
router.get('/vendors/:id/skt', async (req, res) => {
  try {
    const { id } = req.params;
    // :id di sini adalah vendors.id (bukan users.id) - konsisten dengan endpoint verify/suspend/
    // block di server/routes/vendors.js dan dengan src/pages/Vendor.jsx yang selalu meneruskan
    // vendors.id, bukan users.id, ke VendorDetailModal.
    const r = await pool.query(`
      SELECT v.*, u.full_name FROM vendors v LEFT JOIN users u ON v.user_id = u.id WHERE v.id = $1
    `, [id]);
    if (!r.rows.length) return res.status(404).json({ success: false, message: 'Vendor tidak ditemukan.' });
    const vendor = r.rows[0];
    if (vendor.status !== 'terverifikasi') {
      return res.status(400).json({ success: false, message: 'SKT cuma bisa dicetak untuk vendor yang sudah terverifikasi.' });
    }

    res.json({
      success: true,
      data: {
        kalimat_tanggal: kalimatTanggalTerbilang(new Date()),
        vendor: {
          nama: vendor.company_name,
          alamat: [vendor.city, vendor.province].filter(Boolean).join(', ') || '-',
          telepon: vendor.phone,
          kontak_person: vendor.contact_person,
          email: vendor.email,
          kualifikasi: vendor.qualification_class,
          npwp: vendor.npwp,
          nib: vendor.nib,
        },
      },
    });
  } catch (err) {
    console.error('[GET /print/vendors/:id/skt]', err);
    res.status(500).json({ success: false, message: 'Gagal mengambil data cetak.' });
  }
});

module.exports = router;
