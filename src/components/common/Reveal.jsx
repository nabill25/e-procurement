import { useEffect, useRef, useState } from 'react';

// Membungkus section supaya animasi masuknya baru terpicu saat section itu
// benar-benar mulai kelihatan di layar (scroll-reveal), bukan langsung semua
// section beranimasi bersamaan saat halaman pertama dibuka - lebih terasa
// hidup untuk halaman panjang seperti portal publik yang banyak scroll.
// Sekali terlihat, animasinya tidak diulang lagi walau di-scroll bolak-balik.
export default function Reveal({ children, className = '', as: Tag = 'div', delay = 0 }) {
  const ref = useRef(null);
  const [visible, setVisible] = useState(false);

  useEffect(() => {
    const el = ref.current;
    if (!el) return;
    const observer = new IntersectionObserver(
      ([entry]) => {
        if (entry.isIntersecting) {
          setVisible(true);
          observer.disconnect();
        }
      },
      { threshold: 0.12, rootMargin: '0px 0px -60px 0px' }
    );
    observer.observe(el);
    return () => observer.disconnect();
  }, []);

  return (
    <Tag
      ref={ref}
      className={`${className} ${visible ? 'public-section-enter' : 'opacity-0'}`}
      style={visible ? { animationDelay: `${delay}ms` } : undefined}
    >
      {children}
    </Tag>
  );
}
