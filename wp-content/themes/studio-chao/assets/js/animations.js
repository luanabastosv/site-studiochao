// ══════════════════════════════
// LOADER — roda imediatamente
// ══════════════════════════════
document.addEventListener('DOMContentLoaded', function () {

    if (typeof gsap === 'undefined') return;

    const loader = document.getElementById('page-loader');
    const barraTopo = document.getElementById('logo-barra-topo');
    const barraBaixo = document.getElementById('logo-barra-baixo');

    // Hero começa oculto
    const eyebrow = document.querySelector('.hero-eyebrow');
    const title = document.querySelector('.hero-title');
    const link = document.querySelector('.hero-link');

    if (eyebrow) gsap.set(eyebrow, { opacity: 0, x: -30 });
    if (title) gsap.set(title, { opacity: 0, x: 30 });
    if (link) gsap.set(link, { opacity: 0, x: 30 });

    function animateHero() {
        if (eyebrow) gsap.to(eyebrow, { opacity: 1, x: 0, duration: 0.5, ease: 'power2.out' });
        if (title) gsap.to(title, { opacity: 1, x: 0, duration: 0.5, ease: 'power2.out', delay: 0.15 });
        if (link) gsap.to(link, { opacity: 1, x: 0, duration: 0.5, ease: 'power2.out', delay: 0.25 });
    }

    if (loader && barraTopo && barraBaixo) {
        const tl = gsap.timeline();

        tl.fromTo(barraTopo,
            { opacity: 0, x: -40 },
            { opacity: 1, x: 0, duration: 0.5, ease: 'power3.out' }
        )
            .fromTo(barraBaixo,
                { opacity: 0, x: 40 },
                { opacity: 1, x: 0, duration: 0.5, ease: 'power3.out' },
                '<'
            )
            .to({}, { duration: 0.4 })
            .to(barraTopo, { opacity: 0, x: -40, duration: 0.4, ease: 'power3.in' })
            .to(barraBaixo, { opacity: 0, x: 40, duration: 0.4, ease: 'power3.in' }, '<')
            .to(loader, {
                opacity: 0, duration: 0.4, ease: 'power2.out', onComplete: () => {
                    loader.style.display = 'none';
                    animateHero();
                }
            });
    } else {
        animateHero();
    }

    // ── SCROLL INDICATOR ──
    const scrollIndicator = document.getElementById('scroll-indicator');
    if (scrollIndicator) {
        const heroSection = document.querySelector('.hero-section');

        // Só mostra na home
        if (heroSection) {
            // Aparece depois do loader sumir — via classe
            const showIndicator = () => {
                scrollIndicator.classList.add('visible');
            };

            // Aguarda o loader terminar (2.2s = duração total do loader)
            setTimeout(showIndicator, 2200);

            // Some ao passar do hero
            window.addEventListener('scroll', function () {
                const heroBottom = heroSection.offsetTop + heroSection.offsetHeight;
                if (window.scrollY > heroBottom * 0.5) {
                    scrollIndicator.classList.remove('visible');
                } else {
                    scrollIndicator.classList.add('visible');
                }
            }, { passive: true });
        }
    }
});

// ══════════════════════════════
// SCROLL REVEAL — espera tudo carregar
// ══════════════════════════════
window.addEventListener('load', function () {

    if (typeof gsap === 'undefined' || typeof ScrollTrigger === 'undefined') return;
    gsap.registerPlugin(ScrollTrigger);

    const introLogo = document.querySelector('.intro-logo');
    const introH2 = document.querySelector('.intro-section h2');

    if (introLogo) {
        gsap.fromTo(introLogo,
            { opacity: 0, y: 30 },
            {
                opacity: 1, y: 0, duration: 0.6, ease: 'power2.out',
                scrollTrigger: { trigger: introLogo, start: 'top 90%', toggleActions: 'play none none none' }
            }
        );
    }

    if (introH2) {
        gsap.fromTo(introH2,
            { opacity: 0, y: 30 },
            {
                opacity: 1, y: 0, duration: 0.6, ease: 'power2.out',
                scrollTrigger: { trigger: introH2, start: 'top 90%', toggleActions: 'play none none none' }
            }
        );
    }

    const destaqueSection = document.querySelector('.destaque-section');
    if (destaqueSection) {
        gsap.fromTo(destaqueSection,
            { opacity: 0, y: 20 },
            {
                opacity: 1, y: 0, duration: 0.6, ease: 'power2.out',
                scrollTrigger: { trigger: destaqueSection, start: 'top 85%', toggleActions: 'play none none none' }
            }
        );
    }

    const catalogoSection = document.querySelector('.catalogo-section');
    if (catalogoSection) {
        gsap.fromTo(catalogoSection,
            { opacity: 0, y: 20 },
            {
                opacity: 1, y: 0, duration: 0.6, ease: 'power2.out',
                scrollTrigger: { trigger: catalogoSection, start: 'top 85%', toggleActions: 'play none none none' }
            }
        );
    }

    const quemsomosSection = document.querySelector('.quemsomos-section');
    if (quemsomosSection) {
        gsap.fromTo(quemsomosSection,
            { opacity: 0, y: 20 },
            {
                opacity: 1, y: 0, duration: 0.6, ease: 'power2.out',
                scrollTrigger: { trigger: quemsomosSection, start: 'top 85%', toggleActions: 'play none none none' }
            }
        );
    }

    const ctaSection = document.querySelector('.cta-section');
    if (ctaSection) {
        gsap.fromTo(ctaSection,
            { opacity: 0, y: 20 },
            {
                opacity: 1, y: 0, duration: 0.6, ease: 'power2.out',
                scrollTrigger: { trigger: ctaSection, start: 'top 85%', toggleActions: 'play none none none' }
            }
        );
    }

});