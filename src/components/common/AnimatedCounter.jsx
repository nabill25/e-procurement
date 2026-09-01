import { useEffect, useRef, useState } from 'react';
import { motion, useInView, useMotionValue, useSpring } from 'framer-motion';

// Angka yang "menghitung naik" dari 0 ke nilai aslinya begitu elemen ini masuk ke
// area layar (sekali saja, tidak diulang tiap scroll bolak-balik) - dipakai di strip
// statistik landing page supaya angka terasa hidup/real-time, bukan cuma teks statis.
// Pakai useSpring dari Framer Motion (bukan setInterval manual) supaya animasinya
// punya physics yang halus (sedikit "settle" di akhir), bukan linear kaku.
export default function AnimatedCounter({ value, duration = 1.4, className = '' }) {
  const ref = useRef(null);
  const isInView = useInView(ref, { once: true, margin: '-40px' });
  const motionValue = useMotionValue(0);
  const spring = useSpring(motionValue, { duration: duration * 1000, bounce: 0 });
  const [display, setDisplay] = useState(0);

  useEffect(() => {
    if (isInView) motionValue.set(value);
  }, [isInView, value, motionValue]);

  useEffect(() => {
    const unsubscribe = spring.on('change', (latest) => setDisplay(Math.round(latest)));
    return unsubscribe;
  }, [spring]);

  return (
    <motion.span ref={ref} className={className}>
      {display.toLocaleString('id-ID')}
    </motion.span>
  );
}
