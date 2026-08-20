/** @type {import('tailwindcss').Config} */
export default {
  content: [
    "./resources/**/*.{jsx,js}",
    "./app/Views/**/*.php"
  ],
  theme: {
    extend: {
      colors: {
        primary: '#0ea5e9',
        secondary: '#64748b',
        // DPBJ UI Brand Identity
        'dpbj': {
          gold:    '#F5A623',
          'gold-light': '#FFC85A',
          'gold-dark':  '#D4881A',
          'gold-faint': '#FEF3DC',
          navy:    '#1A2744',
          'navy-light': '#243460',
          'navy-dark':  '#0F1929',
          slate:   '#2D3F6B',
          'slate-light': '#3D5080',
        },
        // UI System Colors
        surface:  '#F8F9FC',
        'surface-card': '#FFFFFF',
        muted:    '#6B7280',
        border:   '#E5E9F0',
      },
      boxShadow: {
        card:     '0 1px 3px 0 rgba(26, 39, 68, 0.08), 0 1px 2px -1px rgba(26, 39, 68, 0.04)',
        'card-lg': '0 4px 16px 0 rgba(26, 39, 68, 0.12), 0 2px 4px -1px rgba(26, 39, 68, 0.06)',
        modal:    '0 20px 60px -10px rgba(26, 39, 68, 0.30)',
        glow:     '0 0 20px rgba(245, 166, 35, 0.25)',
      },
      animation: {
        blob: "blob 7s infinite",
        'fade-in':    'fadeIn 0.2s ease-out',
        'slide-up':   'slideUp 0.3s ease-out',
        'slide-in':   'slideIn 0.25s ease-out',
        'pulse-soft': 'pulseSoft 2s ease-in-out infinite',
      },
      keyframes: {
        fadeIn:    { '0%': { opacity: 0 }, '100%': { opacity: 1 } },
        slideUp:   { '0%': { transform: 'translateY(16px)', opacity: 0 }, '100%': { transform: 'translateY(0)', opacity: 1 } },
        slideIn:   { '0%': { transform: 'translateX(-12px)', opacity: 0 }, '100%': { transform: 'translateX(0)', opacity: 1 } },
        pulseSoft: { '0%, 100%': { opacity: 1 }, '50%': { opacity: 0.6 } },
        blob: {
          "0%": {
            transform: "translate(0px, 0px) scale(1)",
          },
          "33%": {
            transform: "translate(30px, -50px) scale(1.1)",
          },
          "66%": {
            transform: "translate(-20px, 20px) scale(0.9)",
          },
          "100%": {
            transform: "translate(0px, 0px) scale(1)",
          },
        },
      },
    },
  },
  plugins: [],
}
