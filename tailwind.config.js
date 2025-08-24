module.exports = {
    content: [
        "./resources/**/*.blade.php",
        "./resources/**/*.js",
        "./resources/**/*.jsx",
        "./resources/**/*.vue",
    ],
    theme: {
        extend: {
            fontFamily: {
                playfair: ['"Playfair Display"', "serif"],
                sf: ['"SF Pro Display"', 'sans-serif'],
            },
            zIndex: {
                '1000': '1000',
                '2000': '2000',
                '3000': '3000',
                '4000': '4000',
            },
        },
    },
    plugins: [],
    darkMode: 'class',
};
