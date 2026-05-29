import defaultTheme from 'tailwindcss/defaultTheme'

/** @type {import('tailwindcss').Config} */
export default {
  content: [
    './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
    './storage/framework/views/*.php',
    './resources/views/**/*.blade.php',
    './app/Livewire/**/*.php',
  ],
  theme: {
    extend: {
      colors: {
        primary: {
          DEFAULT: '#690008',
          container: '#8b1a1a',
          hover: '#4d0006',
        },
        gold: {
          light: '#F9E392',
          mid: '#D4AF37',
          dark: '#996515',
          border: '#B8860B',
        },
        surface: {
          DEFAULT: '#fcf9f8',
          dim: '#dcd9d9',
          low: '#f6f3f2',
          DEFAULT2: '#f0eded',
          high: '#eae7e7',
          highest: '#e4e2e1',
        },
        'on-surface': '#1b1c1c',
        'on-surface-variant': '#58413f',
        outline: {
          DEFAULT: '#8c716e',
          variant: '#e0bfbc',
        },
        'brand-error': '#ba1a1a',
      },
      fontFamily: {
        serif: ['"Source Serif 4"', ...defaultTheme.fontFamily.serif],
        sans: ['"Hanken Grotesk"', ...defaultTheme.fontFamily.sans],
        mono: ['"JetBrains Mono"', ...defaultTheme.fontFamily.mono],
      },
      fontSize: {
        'display': ['48px', { lineHeight: '56px', letterSpacing: '-0.02em', fontWeight: '900' }],
        'headline-lg': ['32px', { lineHeight: '40px', fontWeight: '700' }],
        'headline-md': ['24px', { lineHeight: '32px', fontWeight: '700' }],
        'headline-sm': ['20px', { lineHeight: '28px', fontWeight: '700' }],
        'price': ['20px', { lineHeight: '1', fontWeight: '700' }],
      },
      borderRadius: {
        DEFAULT: '4px',
        sm: '2px',
        md: '6px',
        lg: '8px',
        chip: '9999px',
      },
      maxWidth: {
        container: '1280px',
      },
      spacing: {
        'gutter': '24px',
        'margin-desktop': '64px',
      },
    },
  },
  plugins: [],
}
