// Ikon custom bergaya duotone/gradient untuk kartu "Layanan Utama" di landing page.
// Beda dari ikon Lucide biasa (garis tunggal seragam) - ikon ini punya 2 lapis warna
// (base pudar + aksen solid) supaya terasa lebih premium dan representatif ke tema
// masing-masing (lelang/palu untuk Tender, gedung+centang untuk Registrasi).

export function TenderIcon({ size = 48, className = '' }) {
  return (
    <svg width={size} height={size} viewBox="0 0 48 48" fill="none" className={className}>
      <defs>
        <linearGradient id="tenderGradBase" x1="4" y1="4" x2="30" y2="40" gradientUnits="userSpaceOnUse">
          <stop stopColor="#FDBA74" stopOpacity="0.55" />
          <stop offset="1" stopColor="#FB923C" stopOpacity="0.25" />
        </linearGradient>
        <linearGradient id="tenderGradAccent" x1="18" y1="6" x2="42" y2="34" gradientUnits="userSpaceOnUse">
          <stop stopColor="#FB923C" />
          <stop offset="1" stopColor="#EA580C" />
        </linearGradient>
      </defs>

      {/* Dokumen tender (lapis dasar, pudar) - miring sedikit ke kiri-belakang supaya
          palu di depan punya ruang dan komposisinya jelas terbaca sebagai 2 elemen */}
      <g transform="rotate(-8 18 22)">
        <rect x="6" y="7" width="22" height="28" rx="2.5" fill="url(#tenderGradBase)" stroke="#FB923C" strokeOpacity="0.3" />
        <rect x="10.5" y="13.5" width="13" height="2.2" rx="1.1" fill="#EA580C" fillOpacity="0.55" />
        <rect x="10.5" y="18.5" width="13" height="2.2" rx="1.1" fill="#EA580C" fillOpacity="0.55" />
        <rect x="10.5" y="23.5" width="8" height="2.2" rx="1.1" fill="#EA580C" fillOpacity="0.55" />
      </g>

      {/* Palu lelang/gavel (aksen solid, elemen utama, di depan) */}
      <g transform="translate(30 15) rotate(35)">
        {/* Kepala palu */}
        <rect x="-9" y="-5.5" width="18" height="9" rx="2.5" fill="url(#tenderGradAccent)" />
        {/* Gagang */}
        <rect x="-2.4" y="2" width="4.8" height="16" rx="2.2" fill="url(#tenderGradAccent)" />
      </g>
      {/* Alas/meja palu */}
      <rect x="24" y="34" width="18" height="4.5" rx="2.2" fill="url(#tenderGradAccent)" />
    </svg>
  );
}

export function RegistrasiIcon({ size = 48, className = '' }) {
  return (
    <svg width={size} height={size} viewBox="0 0 48 48" fill="none" className={className}>
      <defs>
        <linearGradient id="regGradBase" x1="0" y1="0" x2="48" y2="48" gradientUnits="userSpaceOnUse">
          <stop stopColor="#60A5FA" stopOpacity="0.35" />
          <stop offset="1" stopColor="#3B82F6" stopOpacity="0.15" />
        </linearGradient>
        <linearGradient id="regGradAccent" x1="10" y1="8" x2="38" y2="40" gradientUnits="userSpaceOnUse">
          <stop stopColor="#60A5FA" />
          <stop offset="1" stopColor="#2563EB" />
        </linearGradient>
      </defs>

      {/* Gedung/perusahaan (lapis dasar, pudar) */}
      <rect x="8" y="10" width="20" height="26" rx="2.5" fill="url(#regGradBase)" />
      <rect x="12.5" y="15" width="3.4" height="3.4" rx="0.8" fill="#60A5FA" fillOpacity="0.55" />
      <rect x="18.3" y="15" width="3.4" height="3.4" rx="0.8" fill="#60A5FA" fillOpacity="0.55" />
      <rect x="12.5" y="21" width="3.4" height="3.4" rx="0.8" fill="#60A5FA" fillOpacity="0.55" />
      <rect x="18.3" y="21" width="3.4" height="3.4" rx="0.8" fill="#60A5FA" fillOpacity="0.55" />
      <rect x="14.5" y="28" width="7.4" height="8" rx="1" fill="#60A5FA" fillOpacity="0.4" />

      {/* Badge centang (aksen solid, elemen utama - menandakan pendaftaran berhasil/terverifikasi) */}
      <circle cx="32" cy="30" r="11" fill="white" />
      <circle cx="32" cy="30" r="11" fill="url(#regGradAccent)" fillOpacity="0.15" />
      <circle cx="32" cy="30" r="9.5" fill="url(#regGradAccent)" />
      <path d="M27.5 30.2l2.8 2.8 5.5-5.8" stroke="white" strokeWidth="2.4" strokeLinecap="round" strokeLinejoin="round" fill="none" />
    </svg>
  );
}
