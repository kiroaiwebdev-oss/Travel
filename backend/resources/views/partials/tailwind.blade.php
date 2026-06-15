{{-- Tailwind Play CDN + TravelCash design tokens.
     Semantic palette: brand/travel = teal-emerald, payment/CTA = blue, deals = orange. --}}
<script src="https://cdn.tailwindcss.com?plugins=forms,typography"></script>
<script>
    tailwind.config = {
        theme: {
            extend: {
                colors: {
                    // Travel brand (primary identity)
                    brand: { 50:'#ecfdf5', 100:'#d1fae5', 400:'#34d399', DEFAULT:'#0d9488', 500:'#10b981', 600:'#0d9488', 700:'#0f766e', 800:'#115e59' },
                    // Payment / primary CTA
                    pay: { DEFAULT:'#2563EB', 600:'#2563EB', 700:'#1d4ed8' },
                    primary: { DEFAULT:'#2563EB', 600:'#2563EB', 700:'#1d4ed8' },
                    // Deals / offers
                    deal: { DEFAULT:'#F97316', 600:'#ea580c' },
                    accent: '#10B981',
                    secondary: '#0B1220',
                    success: '#16A34A', warning: '#F59E0B', danger: '#EF4444',
                    bg: '#F7F9FC', card: '#FFFFFF', ink: '#0B1220', muted: '#64748B',
                },
                fontFamily: {
                    sans: ['Inter', 'ui-sans-serif', 'system-ui', 'sans-serif'],
                    display: ['"Plus Jakarta Sans"', 'Inter', 'sans-serif'],
                },
                boxShadow: {
                    soft: '0 1px 2px rgba(16,24,40,.05), 0 4px 16px -4px rgba(16,24,40,.10)',
                    lift: '0 24px 48px -16px rgba(13,148,136,.28)',
                },
                borderRadius: { xl2: '1.25rem', xl3: '1.75rem' },
            }
        }
    }
</script>
