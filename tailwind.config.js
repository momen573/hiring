// tailwind.config.js
module.exports = {
  content: [
    './templates/**/*.html.twig', // Your Twig templates
    './assets/**/*.js', // Your JS files
  ],
  theme: {
    extend: {
      fontFamily: {
        sans: ['Poppins', 'sans-serif'], // Adding Poppins font
      },
    },
  },
  plugins: [],
};
