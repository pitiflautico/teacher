import preset from './vendor/filament/support/tailwind.config.preset'

export default {
    presets: [preset],
    content: [
        './app/Filament/**/*.php',
        './resources/views/filament/**/*.blade.php',
        './vendor/filament/**/*.blade.php',
    ],
    theme: {
        extend: {
            colors: {
                // Design system colors from inspiration
                primary: {
                    50: '#F5F3FF',
                    100: '#EDE9FE',
                    200: '#DDD6FE',
                    300: '#C4B5FD',
                    400: '#A78BFA',
                    500: '#8B5CF6', // Main purple
                    600: '#7C3AED',
                    700: '#6D28D9',
                    800: '#5B21B6',
                    900: '#4C1D95',
                },
                accent: {
                    yellow: '#FBBF24',
                    gold: '#FCD34D',
                    mint: '#34D399',
                    coral: '#F87171',
                    sky: '#60A5FA',
                },
                gradient: {
                    from: {
                        yellow: '#FEF3C7',
                        blue: '#DBEAFE',
                        purple: '#EDE9FE',
                    },
                    to: {
                        white: '#FFFFFF',
                        yellow: '#FEF3C7',
                    }
                }
            },
            backgroundImage: {
                'gradient-yellow-white': 'linear-gradient(135deg, #FEF3C7 0%, #FFFFFF 100%)',
                'gradient-blue-white': 'linear-gradient(135deg, #DBEAFE 0%, #FFFFFF 100%)',
                'gradient-purple-yellow': 'linear-gradient(135deg, #EDE9FE 0%, #FEF3C7 100%)',
                'gradient-yellow-blue': 'linear-gradient(135deg, #FEF3C7 0%, #DBEAFE 100%)',
            },
            boxShadow: {
                'card': '0 1px 3px 0 rgba(0, 0, 0, 0.05), 0 1px 2px 0 rgba(0, 0, 0, 0.03)',
                'card-hover': '0 10px 15px -3px rgba(0, 0, 0, 0.07), 0 4px 6px -2px rgba(0, 0, 0, 0.03)',
            },
            borderRadius: {
                'card': '1rem',
                'badge': '9999px',
            }
        },
    },
}
