import { useEffect, useRef, useState } from 'react';

// Animasi angka dari 0 ke nilai akhir, dipakai di kartu statistik dashboard supaya
// terasa "hidup" tiap kali data baru masuk, bukan cuma sekedar menempel diam.
// isCurrency: format Rupiah singkat (Rp X,XX M/Jt), kalau tidak cuma format ribuan biasa.
export default function AnimatedNumber({ value, duration = 900, isCurrency = false, prefix = '', suffix = '' }) {
  const [display, setDisplay] = useState(0);
  const startRef = useRef(null);
  const fromRef = useRef(0);

  useEffect(() => {
    const target = Number(value) || 0;
    fromRef.current = display;
    startRef.current = null;

    let raf;
    const step = (ts) => {
      if (startRef.current === null) startRef.current = ts;
      const progress = Math.min((ts - startRef.current) / duration, 1);
      const eased = 1 - Math.pow(1 - progress, 3); // ease-out cubic
      const current = fromRef.current + (target - fromRef.current) * eased;
      setDisplay(current);
      if (progress < 1) raf = requestAnimationFrame(step);
    };
    raf = requestAnimationFrame(step);
    return () => cancelAnimationFrame(raf);
    // eslint-disable-next-line react-hooks/exhaustive-deps
  }, [value]);

  const formatted = isCurrency ? formatRupiahShort(display) : Math.round(display).toLocaleString('id-ID');

  return <span>{prefix}{formatted}{suffix}</span>;
}

function formatRupiahShort(n) {
  const abs = Math.abs(n);
  if (abs >= 1_000_000_000) return `Rp ${(n / 1_000_000_000).toFixed(1)} M`;
  if (abs >= 1_000_000) return `Rp ${(n / 1_000_000).toFixed(0)} Jt`;
  if (abs >= 1_000) return `Rp ${(n / 1_000).toFixed(0)} Rb`;
  return `Rp ${Math.round(n).toLocaleString('id-ID')}`;
}
