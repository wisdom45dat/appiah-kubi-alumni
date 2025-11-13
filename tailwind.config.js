/** @type {import('tailwindcss').Config} */
module.exports = {
    content: [
        "./resources/**/*.blade.php",
        "./resources/**/*.js",
        "./resources/**/*.vue",
    ],
    darkMode: 'class',
    theme: {
        extend: {
            colors: {
                // Custom colors for dark mode
                dark: {
                    100: '#1f2937',
                    200: '#374151', 
                    300: '#4b5563',
                    400: '#6b7280',
                    500: '#9ca3af',
                    600: '#d1d5db',
                    700: '#e5e7eb',
                    800: '#f3f4f6',
                    900: '#f9fafb'
                }
            }
        },
    },
    plugins: [],
}
