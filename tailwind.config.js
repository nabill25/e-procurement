/** @type {import('tailwindcss').Config} */
export default {
  content: [
    "./index.html",
    "./src/**/*.{js,ts,jsx,tsx}",
  ],
  theme: {
    extend: {
      colors: {
        // DPBJ UI Brand Identity (Updated to reference UI theme)
        'dpbj': {
          gold:    '#FFD400',
          'gold-light': '#FFE55C',
          'gold-dark':  '#CCAA00',
          'gold-faint': '#FFFBE6',
          navy:    '#1A3668',
          'navy-light': '#3A5B96',
          'navy-dark':  '#0E2041',
          slate:   '#3E5275',
          'slate-light': '#5A729A',
        },
        // UI System Colors
        surface:  '#F8F9FC',
        'surface-card': '#FFFFFF',
        muted:    '#6B7280',
        border:   '#E5E9F0',
      },
      fontFamily: {
        sans: ['Inter', 'system-ui', '-apple-system', 'sans-serif'],
        serif: ['Lora', 'Merriweather', 'serif'],
      },
      boxShadow: {
        card:     '0 1px 3px 0 rgba(26, 39, 68, 0.08), 0 1px 2px -1px rgba(26, 39, 68, 0.04)',
        'card-lg': '0 4px 16px 0 rgba(26, 39, 68, 0.12), 0 2px 4px -1px rgba(26, 39, 68, 0.06)',
        modal:    '0 20px 60px -10px rgba(26, 39, 68, 0.30)',
        glow:     '0 0 20px rgba(245, 166, 35, 0.25)',
        glass:    '0 8px 32px 0 rgba(14, 32, 65, 0.18), inset 0 1px 0 0 rgba(255, 255, 255, 0.4)',
        'glass-lg': '0 20px 50px -12px rgba(14, 32, 65, 0.35), inset 0 1px 0 0 rgba(255, 255, 255, 0.3)',
        'glow-gold': '0 0 32px rgba(255, 212, 0, 0.35)',
      },
      animation: {
        'fade-in':    'fadeIn 0.2s ease-out',
        'slide-up':   'slideUp 0.3s ease-out',
        'slide-in':   'slideIn 0.25s ease-out',
        'pulse-soft': 'pulseSoft 2s ease-in-out infinite',
        'float':      'float 6s ease-in-out infinite',
        'float-delay': 'float 7s ease-in-out 1.5s infinite',
        'shimmer':    'shimmer 2.5s linear infinite',
        'count-up':   'countUp 0.5s ease-out',
        'spin-slow':  'spin 20s linear infinite',
      },
      keyframes: {
        fadeIn:    { '0%': { opacity: 0 }, '100%': { opacity: 1 } },
        slideUp:   { '0%': { transform: 'translateY(16px)', opacity: 0 }, '100%': { transform: 'translateY(0)', opacity: 1 } },
        slideIn:   { '0%': { transform: 'translateX(-12px)', opacity: 0 }, '100%': { transform: 'translateX(0)', opacity: 1 } },
        pulseSoft: { '0%, 100%': { opacity: 1 }, '50%': { opacity: 0.6 } },
        float:     { '0%, 100%': { transform: 'translate(0, 0)' }, '50%': { transform: 'translate(10px, -16px)' } },
        shimmer:   { '0%': { backgroundPosition: '-200% 0' }, '100%': { backgroundPosition: '200% 0' } },
        countUp:   { '0%': { opacity: 0, transform: 'translateY(6px)' }, '100%': { opacity: 1, transform: 'translateY(0)' } },
      },
      backdropBlur: {
        xs: '2px',
      },
    },
  },
  plugins: [],
}
