import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ['Inter', ...defaultTheme.fontFamily.sans],
            },
            colors: {
                sky: {
                    50:  '#f0f9ff',
                    100: '#e0f2fe',
                    200: '#bae6fd',
                    300: '#7dd3fc',
                    400: '#38bdf8',
                    500: '#0ea5e9',
                    600: '#0284c7',
                    700: '#0369a1',
                    800: '#075985',
                    900: '#0c4a6e',
                    950: '#082f49',
                },
                gaf: {
                    blue:   '#0B4FA3',
                    sky:    '#0EA5E9',
                    navy:   '#0C2D6B',
                    gold:   '#F59E0B',
                    accent: '#06B6D4',
                    light:  '#E0F2FE',
                },
            },
            backgroundImage: {
                'gaf-gradient': 'linear-gradient(135deg, #0C2D6B 0%, #0B4FA3 45%, #0EA5E9 100%)',
                'gaf-card':     'linear-gradient(145deg, rgba(14,165,233,0.08) 0%, rgba(11,79,163,0.04) 100%)',
            },
            animation: {
                'fade-in':       'fadeIn 0.5s ease-out',
                'slide-up':      'slideUp 0.4s ease-out',
                'slide-in-left': 'slideInLeft 0.35s ease-out',
                'pulse-slow':    'pulse 3s cubic-bezier(0.4,0,0.6,1) infinite',
                'float':         'float 6s ease-in-out infinite',
                'shimmer':       'shimmer 2s linear infinite',
                'spin-slow':     'spin 8s linear infinite',
            },
            keyframes: {
                fadeIn:      { '0%': { opacity: '0' }, '100%': { opacity: '1' } },
                slideUp:     { '0%': { opacity: '0', transform: 'translateY(20px)' }, '100%': { opacity: '1', transform: 'translateY(0)' } },
                slideInLeft: { '0%': { opacity: '0', transform: 'translateX(-20px)' }, '100%': { opacity: '1', transform: 'translateX(0)' } },
                float:       { '0%,100%': { transform: 'translateY(0px)' }, '50%': { transform: 'translateY(-10px)' } },
                shimmer:     { '0%': { backgroundPosition: '-200% 0' }, '100%': { backgroundPosition: '200% 0' } },
            },
            boxShadow: {
                'gaf':       '0 4px 24px rgba(11,79,163,0.15)',
                'gaf-lg':    '0 8px 48px rgba(11,79,163,0.2)',
                'gaf-glow':  '0 0 20px rgba(14,165,233,0.4)',
                'card':      '0 2px 16px rgba(0,0,0,0.06), 0 1px 4px rgba(0,0,0,0.04)',
            },
        },
    },

    plugins: [forms],
};
