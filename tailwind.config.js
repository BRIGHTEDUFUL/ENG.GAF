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
                'gaf-gradient':        'linear-gradient(135deg, #0C2D6B 0%, #0B4FA3 45%, #0EA5E9 100%)',
                'gaf-gradient-r':      'linear-gradient(to right, #0B4FA3, #0EA5E9)',
                'gaf-card':            'linear-gradient(145deg, rgba(14,165,233,0.06) 0%, rgba(11,79,163,0.03) 100%)',
                'shimmer':             'linear-gradient(90deg, #e8f4fd 25%, #cbe8fb 50%, #e8f4fd 75%)',
            },
            animation: {
                'fade-in':       'fadeIn 0.45s ease-out both',
                'slide-up':      'slideUp 0.4s ease-out both',
                'slide-in-left': 'slideInLeft 0.35s ease-out both',
                'modal-up':      'modalUp 0.35s cubic-bezier(0.34,1.56,0.64,1) both',
                'scale-in':      'scaleIn 0.3s cubic-bezier(0.34,1.56,0.64,1) both',
                'pulse-slow':    'pulse 3s cubic-bezier(0.4,0,0.6,1) infinite',
                'float':         'float 6s ease-in-out infinite',
                'shimmer':       'shimmer 1.5s linear infinite',
                'spin-slow':     'spin 8s linear infinite',
                'ping-once':     'ping-once 2s ease-in-out 3',
            },
            keyframes: {
                fadeIn:      { '0%': { opacity: '0' },                                                  '100%': { opacity: '1' } },
                slideUp:     { '0%': { opacity: '0', transform: 'translateY(18px)' },                   '100%': { opacity: '1', transform: 'translateY(0)' } },
                slideInLeft: { '0%': { opacity: '0', transform: 'translateX(24px)' },                   '100%': { opacity: '1', transform: 'translateX(0)' } },
                modalUp:     { '0%': { opacity: '0', transform: 'translateY(32px) scale(0.97)' },       '100%': { opacity: '1', transform: 'translateY(0) scale(1)' } },
                scaleIn:     { '0%': { opacity: '0', transform: 'scale(0.9)' },                         '100%': { opacity: '1', transform: 'scale(1)' } },
                float:       { '0%,100%': { transform: 'translateY(0px)' },                             '50%':  { transform: 'translateY(-8px)' } },
                shimmer:     { '0%':   { backgroundPosition: '-200% 0' },                               '100%': { backgroundPosition: '200% 0' } },
                'ping-once': {
                    '0%':   { transform: 'scale(1)', opacity: '1' },
                    '50%':  { transform: 'scale(1.5)', opacity: '0.4' },
                    '100%': { transform: 'scale(1)', opacity: '1' },
                },
            },
            boxShadow: {
                'gaf':       '0 4px 24px rgba(11,79,163,0.15)',
                'gaf-lg':    '0 8px 48px rgba(11,79,163,0.22)',
                'gaf-xl':    '0 16px 64px rgba(11,79,163,0.28)',
                'gaf-glow':  '0 0 24px rgba(14,165,233,0.45)',
                'card':      '0 1px 3px rgba(0,0,0,0.06), 0 1px 2px rgba(0,0,0,0.04)',
                'card-lg':   '0 4px 16px rgba(0,0,0,0.08), 0 1px 4px rgba(0,0,0,0.04)',
                'inner-sm':  'inset 0 1px 0 rgba(255,255,255,0.6)',
            },
            borderRadius: {
                '4xl': '2rem',
            },
            spacing: {
                '18': '4.5rem',
                '22': '5.5rem',
            },
            transitionTimingFunction: {
                'bounce-out': 'cubic-bezier(0.34, 1.56, 0.64, 1)',
            },
        },
    },

    plugins: [forms],
};
