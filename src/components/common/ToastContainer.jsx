import { useEffect, useState, useCallback } from 'react';
import { CheckCircle2, XCircle, Info, X } from 'lucide-react';
import { motion, AnimatePresence } from 'framer-motion';
import { subscribeToast } from '../../lib/toast';

const STYLES = {
  success: { icon: CheckCircle2, className: 'bg-emerald-50 border-emerald-200 text-emerald-800', iconClass: 'text-emerald-500' },
  error:   { icon: XCircle,      className: 'bg-red-50 border-red-200 text-red-800',           iconClass: 'text-red-500' },
  info:    { icon: Info,         className: 'bg-white border-border text-dpbj-navy',           iconClass: 'text-dpbj-gold-dark' },
};

const AUTO_DISMISS_MS = 4500;

// Dipasang SEKALI di root App.jsx - dengar event dari src/lib/toast.js dan tampilkan sebagai
// kartu melayang di pojok kanan atas, menumpuk kalau lebih dari satu, hilang otomatis setelah
// beberapa detik (atau bisa ditutup manual). Ini pengganti alert() bawaan browser yang dulu
// dipakai di 32 file berbeda - lihat catatan lengkap di src/lib/toast.js.
export default function ToastContainer() {
  const [items, setItems] = useState([]);

  const remove = useCallback((id) => {
    setItems(prev => prev.filter(t => t.id !== id));
  }, []);

  useEffect(() => {
    return subscribeToast((item) => {
      setItems(prev => [...prev, item]);
      setTimeout(() => remove(item.id), AUTO_DISMISS_MS);
    });
  }, [remove]);

  return (
    <div className="fixed top-4 right-4 z-[100] flex flex-col gap-2 w-[calc(100%-2rem)] max-w-sm pointer-events-none">
      <AnimatePresence>
        {items.map(item => {
          const style = STYLES[item.type] || STYLES.info;
          const Icon = style.icon;
          return (
            <motion.div
              key={item.id}
              layout
              initial={{ opacity: 0, x: 40, scale: 0.95 }}
              animate={{ opacity: 1, x: 0, scale: 1 }}
              exit={{ opacity: 0, x: 40, scale: 0.95, transition: { duration: 0.15 } }}
              transition={{ type: 'spring', stiffness: 340, damping: 28 }}
              className={`pointer-events-auto flex items-start gap-3 p-4 rounded-2xl border shadow-card-lg backdrop-blur-md ${style.className}`}
            >
              <Icon size={18} className={`shrink-0 mt-0.5 ${style.iconClass}`} />
              <p className="text-sm font-medium leading-snug flex-1 min-w-0 break-words">{item.message}</p>
              <button onClick={() => remove(item.id)} className="shrink-0 opacity-50 hover:opacity-100 transition-opacity">
                <X size={14} />
              </button>
            </motion.div>
          );
        })}
      </AnimatePresence>
    </div>
  );
}
