import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
        './resources/js/**/*.vue',
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ['Figtree', ...defaultTheme.fontFamily.sans],
            },
            animation: {
                wiggle: 'wiggle 1.5s ease-in-out 2',
                flyToTrash: 'flyToTrash 0.6s ease-in-out forwards',
              },
              keyframes: {
                wiggle: {
                  '0%, 100%': {
                    transform: 'rotate(0deg) scale(1)',
                  },
                  '25%': {
                    transform: 'rotate(10deg) scale(1.2)',
                  },
                  '75%': {
                    transform: 'rotate(-10deg) scale(1.2)',
                  },
                },
                flyToTrash: {
                  '0%': {
                    opacity: 1,
                    transform: 'translateY(0px) scale(1)',
                  },
                  '100%': {
                    opacity: 0,
                    transform: 'translateY(-150px) scale(0.5)',
                  },
                },
            },
        },
    },

    plugins: [forms],
};
