const colors = require('tailwindcss/colors')

module.exports = {
  mode: 'jit',
  important: true,
  purge: [
    './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
    './vendor/laravel/jetstream/**/*.blade.php',
    './storage/framework/views/*.php',
    './resources/views/**/*.blade.php',
  ],
  theme: {
    extend: {
    },
    container: {
      center: false,
    },
    fontFamily: {
      sans: ['"Poppins"', 'sans-serif']
    },
    colors: {
      transparent: 'transparent',
      current: 'currentColor',
      white: colors.white,
      gray: colors.gray,
      red: colors.red,
      green: colors.green,
      rose: colors.rose,
      blue: colors.blue,
      indigo: colors.indigo,
    },
  },
  plugins: [
    require('@tailwindcss/typography'),
  ]
}
