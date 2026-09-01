// Rumus evaluasi resmi (Personil, Peralatan, Sertifikat Lain), meniru persis fungsi
// hitungPersonil()/hitungPeralatan()/hitungSertifikat() di eproc/lib/eproc/allfunc.js (kode
// yang benar-benar dipakai sistem produksi lama). Dipindahkan ke sini (dari server/routes/
// tenders.js) supaya bisa dipakai bersama oleh endpoint cetak rekapitulasi evaluasi kualifikasi
// (server/routes/print.js) tanpa duplikasi logika.

const FORMULA_CATEGORIES = ['personil', 'peralatan', 'sertifikat_lain'];

// Nilai kesesuaian efektif: S=100, TS=0, selain itu pakai nilai manual (tapi kalau manual
// diisi persis 0 atau 100, dipaksa jadi 50 - meniru validasi di allfunc.js).
function resolveSuitabilityValue(suitability, manualValue) {
  if (suitability === 'S') return 100;
  if (suitability === 'TS') return 0;
  const v = Number(manualValue);
  if (isNaN(v)) return 0;
  if (v === 0 || v === 100) return 50;
  return v;
}

function round2(n) {
  return Math.round(n * 100) / 100;
}

// Hitung rasio (0-1) untuk SATU kriteria (mis. satu peran personil / satu jenis alat) berdasarkan
// item-item yang diajukan vendor untuk kriteria itu.
function calcCriteriaRatio(category, criteria, items) {
  const values = items.map(it => resolveSuitabilityValue(it.suitability, it.suitability_value));

  if (category === 'personil') {
    const requiredCount = Number(criteria.required_count) || 0;
    const filledCount = items.length;
    const totalKebutuhan = requiredCount * 100;
    const totalNilai = values.reduce((a, b) => a + b, 0);
    if (totalKebutuhan === 0) return 0;
    if (requiredCount > filledCount) return totalNilai / totalKebutuhan;
    return totalKebutuhan <= totalNilai ? 1 : totalNilai / totalKebutuhan;
  }

  if (category === 'peralatan') {
    const totalNilai = items.reduce((sum, it, i) => {
      const ownership = it.ownership_factor != null ? Number(it.ownership_factor) : 100;
      return sum + (values[i] * ownership) / 100;
    }, 0);
    return totalNilai >= 100 ? 1 : totalNilai / 100;
  }

  // sertifikat_lain
  const totalNilai = values.reduce((a, b) => a + b, 0);
  return totalNilai >= 100 ? 1 : totalNilai / 100;
}

// Hitung nilai akhir SATU kategori evaluasi untuk satu vendor, dari daftar kriteria kategori itu.
// Dipakai untuk cetak rekapitulasi (server/routes/print.js) supaya semua kategori - baik yang
// pakai rumus resmi (personil/peralatan/sertifikat_lain, dari item_count) maupun kategori manual
// biasa (administrasi/pengalaman/dst, dari skor yang diisi Pokja langsung) - dihitung dengan cara
// yang konsisten: rasio 0-1 per kriteria dikali bobotnya = kontribusi, dijumlah (dibatasi maks 100),
// dikali nilai maksimal kategori.
function computeCategoryFinalScore(category, criteriaRows, itemsByCriteria, scoreByCriteria, maxScore) {
  const isFormula = FORMULA_CATEGORIES.includes(category);
  const breakdown = criteriaRows.map(criteria => {
    let ratio;
    if (isFormula) {
      const items = itemsByCriteria[criteria.id] || [];
      ratio = calcCriteriaRatio(category, criteria, items);
    } else {
      const score = scoreByCriteria[criteria.id];
      ratio = score === undefined || score === null ? 0 : Number(score) / 100;
    }
    const weight = Number(criteria.weight) || 0;
    return { criteria_id: criteria.id, weight, ratio, contribution: round2(weight * ratio) };
  });
  const totalProsentase = Math.min(100, round2(breakdown.reduce((sum, b) => sum + b.contribution, 0)));
  const finalScore = round2(((maxScore ?? 100) * totalProsentase) / 100);
  return { breakdown, total_prosentase: totalProsentase, final_score: finalScore };
}

module.exports = { FORMULA_CATEGORIES, resolveSuitabilityValue, round2, calcCriteriaRatio, computeCategoryFinalScore };
