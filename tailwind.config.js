/** @type {import('tailwindcss').Config} */
module.exports = {
  content: [
    "./*.php",
    "./includes/**/*.php",
    "./admin/**/*.php",
    "./api/**/*.php",
    "./assets/js/**/*.js",
  ],
  theme: {
    extend: {
      colors: {
        primary: {
          DEFAULT: 'var(--primary)',
          hover: 'var(--primary-hover)',
          light: 'var(--primary-light)',
          50: 'var(--primary-light)',
          600: 'var(--primary)',
          700: 'var(--primary-hover)',
        },
        neutral: {
          50: 'var(--bg)',
          100: 'var(--border)',
          200: '#e5e7eb',
          300: '#d1d5db',
          400: 'var(--text-muted)',
          500: 'var(--text-secondary)',
          600: 'var(--text-secondary)',
          700: 'var(--text-primary)',
          800: '#1f2937',
          900: 'var(--text-primary)',
        },
        bg: 'var(--bg)',
        card: 'var(--card)',
        border: 'var(--border)',
      },
      fontFamily: {
        sans: ['Inter', 'sans-serif'],
      },
      fontSize: {
        xs: 'var(--text-xs)',
        sm: 'var(--text-sm)',
        base: 'var(--text-base)',
        md: 'var(--text-md)',
        lg: 'var(--text-lg)',
        xl: 'var(--text-xl)',
        '2xl': 'var(--text-2xl)',
        '3xl': 'var(--text-3xl)',
      },
      spacing: {
        1: 'var(--space-1)',
        2: 'var(--space-2)',
        3: 'var(--space-3)',
        4: 'var(--space-4)',
        6: 'var(--space-6)',
        8: 'var(--space-8)',
        10: 'var(--space-10)',
        12: 'var(--space-12)',
      },
      borderRadius: {
        xl: 'var(--radius)',
      },
      boxShadow: {
        sm: 'var(--shadow-sm)',
        md: 'var(--shadow-md)',
      }
    },
  },
  plugins: [],
}
