import { useState, useEffect } from 'react';

export default function LiveClock() {
  const [now, setNow] = useState(new Date());
  
  useEffect(() => {
    const t = setInterval(() => setNow(new Date()), 1000);
    return () => clearInterval(t);
  }, []);
  
  const days = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
  const months = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
  
  return (
    <span>
      {days[now.getDay()]}, {now.getDate()} {months[now.getMonth()]} {now.getFullYear()}{' '}
      {String(now.getHours()).padStart(2, '0')} : {String(now.getMinutes()).padStart(2, '0')} : {String(now.getSeconds()).padStart(2, '0')} WIB
    </span>
  );
}
