import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
        './resources/js/**/*.jsx',
        './node_modules/flowbite/**/*.js',
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ['JetBrains Mono', 'monospace', ...defaultTheme.fontFamily.sans],
                mono: ['JetBrains Mono', 'monospace'],
            },
            colors: {
                // Couleurs spécifiques pour les graphes de présence
                'presence-vert-fonce': '#15803d', // >= 70%
                'presence-vert-clair': '#16a34a', // 50.1% - 69.9%
                'presence-orange': '#ea580c', // 30.1% - 50%
                'presence-rouge': '#dc2626', // <= 30%
                // Couleurs IFRAN
                'ifran-primary': '#1e40af',
                'ifran-secondary': '#3b82f6',
                'ifran-accent': '#f59e0b',
            },
            animation: {
                'fade-in': 'fadeIn 0.5s ease-in-out',
                'slide-up': 'slideUp 0.3s ease-out',
                'bounce-in': 'bounceIn 0.6s ease-out',
            },
            keyframes: {
                fadeIn: {
                    '0%': { opacity: '0' },
                    '100%': { opacity: '1' },
                },
                slideUp: {
                    '0%': { transform: 'translateY(20px)', opacity: '0' },
                    '100%': { transform: 'translateY(0)', opacity: '1' },
                },
                bounceIn: {
                    '0%': { transform: 'scale(0.3)', opacity: '0' },
                    '50%': { transform: 'scale(1.05)' },
                    '70%': { transform: 'scale(0.9)' },
                    '100%': { transform: 'scale(1)', opacity: '1' },
                },
            },
        },
    },

    plugins: [
        forms,
        require('daisyui'),
        require('flowbite/plugin'),
    ],

    daisyui: {
        themes: [
            {
                ifran: {
                    "primary": "#1e40af",
                    "secondary": "#3b82f6",
                    "accent": "#f59e0b",
                    "neutral": "#191d24",
                    "base-100": "#ffffff",
                    "info": "#3abff8",
                    "success": "#36d399",
                    "warning": "#fbbd23",
                    "error": "#f87272",
                },
            },
        ],
    },
};
