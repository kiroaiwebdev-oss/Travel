{{-- Tailwind Play CDN with the TravelCash design tokens.
     PRODUCTION: replace this with a compiled Tailwind build (see docs/05-DEPLOYMENT.md);
     the same theme config lives in tailwind.config.js. --}}
<script src="https://cdn.tailwindcss.com?plugins=forms,typography"></script>
<script>
    tailwind.config = {
        theme: {
            extend: {
                colors: {
                    primary: { DEFAULT: '#2563EB', 600: '#2563EB', 700: '#1d4ed8' },
                    secondary: '#0F172A',
                    accent: '#10B981',
                    success: '#22C55E',
                    warning: '#F59E0B',
                    danger: '#EF4444',
                    bg: '#F8FAFC',
                    card: '#FFFFFF',
                    ink: '#111827',
                    muted: '#64748B',
                },
                fontFamily: {
                    sans: ['Inter', 'ui-sans-serif', 'system-ui', 'sans-serif'],
                    display: ['"Plus Jakarta Sans"', 'Inter', 'sans-serif'],
                },
                boxShadow: {
                    soft: '0 1px 2px rgba(16,24,40,.04), 0 4px 16px rgba(16,24,40,.06)',
                    lift: '0 10px 30px -10px rgba(37,99,235,.25)',
                },
                borderRadius: { xl2: '1.25rem' },
            }
        }
    }
</script>
