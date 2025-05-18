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
        },
    },

    plugins: [],
};
