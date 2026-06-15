{{-- Tailwind Play CDN + TripCash tokens. Trust Blue primary · Travel Teal secondary. --}}
<script src="https://cdn.tailwindcss.com?plugins=forms,typography"></script>
<script>
    tailwind.config = {
        theme: {
            extend: {
                colors: {
                    primary: { DEFAULT:'#0F62FE', 600:'#0F62FE', 700:'#0a4fd6' }, // Trust Blue
                    pay: { DEFAULT:'#0F62FE', 700:'#0a4fd6' },
                    brand: { 50:'#e6fffb', 400:'#2dd4cb', DEFAULT:'#00B8A9', 600:'#00B8A9', 700:'#009688' }, // Travel Teal
                    deal: { DEFAULT:'#FF8A00', 600:'#ea580c' }, // Deals orange
                    accent: '#22C55E',
                    secondary: '#0B1220',
                    success: '#22C55E', warning: '#F59E0B', danger: '#EF4444',
                    bg: '#F8FAFC', card: '#FFFFFF', ink: '#1E293B', muted: '#64748B',
                },
                fontFamily: {
                    sans: ['Inter', 'ui-sans-serif', 'system-ui', 'sans-serif'],
                    display: ['"Plus Jakarta Sans"', 'Inter', 'sans-serif'],
                },
                boxShadow: {
                    soft: '0 1px 2px rgba(16,24,40,.05), 0 4px 16px -4px rgba(16,24,40,.10)',
                    lift: '0 24px 48px -16px rgba(15,98,254,.28)',
                },
                borderRadius: { xl2: '1.25rem', xl3: '1.75rem' },
            }
        }
    }
</script>
