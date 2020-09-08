module.exports = {
  important: true,
  purge: [
    './resources/views/**/*.blade.php',
    './resources/css/**/*.css',
  ],
  theme: {
    extend: {},
    container: {
      center: true,
    },
    fontFamily: {
      sans: ['"Poppins"', 'sans-serif']
    },
    typography: {
        default: {
            css: {
                h3: {
                    'margin-top': '0px',
                    'font-weight': 'normal',
                },
                h4: {
                    'color': '#62332C',
                    'margin-bottom': '1.3em',
                    'font-weight': 'normal',
                },
                a: {
                    color: '#3182ce',
                    '&:hover': {
                    color: '#2c5282',
                    },
                },
            },
        },
    },
  },
  variants: {},
  plugins: [
    require('@tailwindcss/ui'),
    require('@tailwindcss/typography'),
  ],
  purge: {
    mode: 'all',
    content: [
      // Paths to your templates here...
    ],
    options: {
      whitelist: ['h1', 'h2', 'h3', 'p', 'blockquote', 'strong' /* etc. */],
    },
  },
}
