/** @type {import('tailwindcss').Config} */
const defaultTheme = require('tailwindcss/defaultTheme');

module.exports = {
    content: [
        './app/Domain/**/resources/**/*.blade.php',
        './app/Domain/**/resources/**/*.js',
        './app/Domain/**/resources/**/*.jsx',
        './app/Domain/**/resources/**/*.vue',
        './resources/**/*.blade.php',
        './resources/**/*.js',
        './resources/**/*.jsx',
        './resources/**/*.vue',
        './node_modules/tw-elements/dist/js/**/*.js',
    ],
    theme: {
        extend: {
            backgroundSize: {
                'size-200': '200% 200%',
            },
            fontFamily: {
                sans: ['Poppins', ...defaultTheme.fontFamily.sans],
            },
        },
    },
    plugins: [require('@tailwindcss/forms'), require('tw-elements/dist/plugin.cjs')],
};
