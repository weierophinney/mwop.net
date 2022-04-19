module.exports = {
  plugins: {
    'postcss-import': {},
    tailwindcss: {},
    autoprefixer: {},
    ...(process.env.IS_PROD ? { cssnano: {} } : {})
  }
}
