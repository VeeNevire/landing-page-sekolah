import defaultTheme from "tailwindcss/defaultTheme";
import forms from "@tailwindcss/forms";

/** @type {import('tailwindcss').Config} */
export default {
    corePlugins: {
        preflight: false,
    },

    content: [
        "./vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php",
        "./storage/framework/views/*.php",
        "./resources/views/**/*.blade.php",
    ],

    theme: {
        container: false,
        extend: {
            fontFamily: {
                sans: ["Figtree", ...defaultTheme.fontFamily.sans],
            },
            colors: {
                ocean: {
                    50: '#e8f4f8',
                    100: '#d4eaf2',
                    150: '#b5dce8',
                    200: '#95ccdd',
                    300: '#6bb5ce',
                    400: '#5c9dbf',
                    500: '#3d8baf',
                    600: '#2a7a9e',
                    700: '#1a5f7a',
                    800: '#0f4c6b',
                    900: '#0a3a54',
                },
                coral: {
                    50: '#fdf2ed',
                    100: '#fae1d0',
                    200: '#f5c4a8',
                    300: '#f2b07c',
                    400: '#e8895c',
                    500: '#d97a4a',
                    600: '#c46538',
                    700: '#a5502a',
                    800: '#86401f',
                    900: '#6a3115',
                },
                fresh: {
                    primary: '#0d9488',
                    secondary: '#6366f1',
                    coral: '#f43f5e',
                    violet: '#a855f7',
                    amber: '#f59e0b',
                },
            },
            animation: {
                'fade-in': 'fadeIn 0.5s ease-out',
                'slide-up': 'slideUp 0.5s ease-out',
                'count-up': 'countUp 1.5s ease-out',
                'gradient': 'gradient 4s ease infinite',
            },
            keyframes: {
                fadeIn: {
                    '0%': { opacity: '0' },
                    '100%': { opacity: '1' },
                },
                slideUp: {
                    '0%': { opacity: '0', transform: 'translateY(12px)' },
                    '100%': { opacity: '1', transform: 'translateY(0)' },
                },
                countUp: {
                    '0%': { opacity: '0' },
                    '100%': { opacity: '1' },
                },
                gradient: {
                    '0%, 100%': { backgroundPosition: '0% 50%' },
                    '50%': { backgroundPosition: '100% 50%' },
                },
            },
        },
    },

    plugins: [forms({ strategy: 'class' })],
};
