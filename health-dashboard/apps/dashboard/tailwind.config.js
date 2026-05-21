/** @type {import('tailwindcss').Config} */
module.exports = {
  content: ['./src/**/*.{js,ts,jsx,tsx,mdx}'],
  theme: {
    extend: {
      colors: {
        wordie: {
          'dark-teal':   '#0A3542',
          'accent-teal': '#116E6E',
          'light-green': '#B9DDDD',
          coral:         '#F5634D',
          'near-black':  '#062028',
          'off-white':   '#F6F9F9',
        },
      },
      fontFamily: {
        heading: ['Montserrat', 'sans-serif'],
        body:    ['"Source Sans Pro"', 'sans-serif'],
      },
      fontSize: {
        'h1': ['42px', { lineHeight: '120%', fontWeight: '700' }],
        'h2': ['28px', { lineHeight: '120%', fontWeight: '700' }],
        'h3': ['24px', { lineHeight: '120%', fontWeight: '600' }],
        'h4': ['20px', { lineHeight: '120%', fontWeight: '600' }],
      },
    },
  },
  plugins: [],
};
