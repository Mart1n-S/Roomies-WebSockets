/** @type {import('tailwindcss').Config} */
export default {
  content: ['./index.html', './src/**/*.{vue,js,ts,jsx,tsx}'],
  theme: {
    extend: {
      colors: {
        roomies: {
          blue: '#5865F2',
          hover: '#4752C4',
          active: '#3C45A5',
          gray1: '#424549',
          gray2: '#36393e',
          gray3: '#282b30',
          gray4: '#1e2124',
          gray5: '#1a1b1e',
        },
      },
    },
  },
  plugins: [],
}
