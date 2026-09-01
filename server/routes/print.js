const express = require('express');
const router = express.Router();
const { pool } = require('../db');
const { requireAuth } = require('../lib/authMiddleware');
const { terbilang, kalimatTanggalTerbilang, formatTanggalIndo } = require('../lib/tanggalTerbilang');

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

module.exports = router;
