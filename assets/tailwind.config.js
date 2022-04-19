module.exports = {
  content: ["../src/**/*.phtml", "../templates/**/*.phtml", "../data/**/*.phtml"],
  theme: {
    extend: {
      listStyleType: {
        none: 'none',
        disc: 'disc',
        decimal: 'decimal',
        dash: '"— "',
      }
    },
  },
  plugins: [],
}
