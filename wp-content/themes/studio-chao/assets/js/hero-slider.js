document.addEventListener('DOMContentLoaded', function() {
    const slides = document.querySelectorAll('.hero-slide');
    if (!slides.length) return;

    let current = 0;
    let autoplay;

    // cria dots
    const dotsContainer = document.createElement('div');
    dotsContainer.className = 'hero-dots';
    slides.forEach((_, i) => {
        const dot = document.createElement('button');
        dot.className = 'hero-dot' + (i === 0 ? ' active' : '');
        dot.setAttribute('aria-label', 'Slide ' + (i + 1));
        dot.addEventListener('click', () => {
            goTo(i);
            resetAutoplay();
        });
        dotsContainer.appendChild(dot);
    });
    document.querySelector('.hero-section').appendChild(dotsContainer);

    function goTo(index) {
        slides[current].classList.remove('active');
        document.querySelectorAll('.hero-dot')[current].classList.remove('active');
        current = index;
        slides[current].classList.add('active');
        document.querySelectorAll('.hero-dot')[current].classList.add('active');
    }

    function nextSlide() {
        goTo((current + 1) % slides.length);
    }

    function resetAutoplay() {
        clearInterval(autoplay);
        autoplay = setInterval(nextSlide, 4000);
    }

    goTo(0);
    resetAutoplay();
});