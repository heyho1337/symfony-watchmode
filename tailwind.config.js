/** @type {import('tailwindcss').Config} */
module.exports = {
	content: [
		"./assets/**/*.js",
		"./templates/**/*.html.twig",
	],
  	theme: {
		extend: {
			fontFamily: { 
				"ox": ['Oxanium', 'sans-serif'],
				"audio": ['Audiowide', 'sans-serif']
			} 
		},
  	},
  	plugins: [
		require('tailwind-fontawesome'),
		require('@tailwindcss/aspect-ratio')
	],
	corePlugins: {
		aspectRatio: false,
	},
}

