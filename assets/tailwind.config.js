const colors = require('tailwindcss/colors');

module.exports = {
  content: ["../src/**/*.phtml", "../templates/**/*.phtml"],
  theme: {
    extend: {
        colors: {
            mwop: {
                bg: colors.neutral[800],
                nav: colors.neutral[700],
                dark: colors.neutral[500],
                soft: colors.neutral[400],
                light: colors.neutral[300],
                fg: colors.neutral[100],
            },
            action: {
                bg: colors.emerald[500],
                border: colors.emerald[800],
                active: colors.emerald[200],
                highlight: colors.emerald[900],
            },
        },
    },
  },
  plugins: [],
}
