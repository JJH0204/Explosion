// Intersection Observer for fade-in and slide-in animations
const observer = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
        if (entry.isIntersecting) {
            entry.target.classList.add('visible');
        }
    });
}, {
    threshold: 0.1
});

// Observe all elements with animation classes
document.addEventListener('DOMContentLoaded', () => {
    // Animate hero section immediately
    document.querySelector('.hero h1').classList.add('visible');
    document.querySelector('.hero .subtitle').classList.add('visible');

    // Observe other animated elements
    const animatedElements = document.querySelectorAll('.fade-in, .slide-in');
    animatedElements.forEach(el => observer.observe(el));

    // Add hover effect to achievement cards
    const cards = document.querySelectorAll('.achievement-card');
    cards.forEach(card => {
        card.addEventListener('mouseover', () => {
            card.style.transform = 'translateY(-10px) scale(1.05)';
        });
        card.addEventListener('mouseout', () => {
            card.style.transform = 'translateY(0) scale(1)';
        });
    });
});

// Smooth scroll for navigation
document.querySelectorAll('a[href^="#"]').forEach(anchor => {
    anchor.addEventListener('click', function (e) {
        e.preventDefault();
        document.querySelector(this.getAttribute('href')).scrollIntoView({
            behavior: 'smooth'
        });
    });
});

// Parallax effect for hero section
window.addEventListener('scroll', () => {
    const hero = document.querySelector('.hero');
    const scrolled = window.pageYOffset;
    hero.style.backgroundPositionY = scrolled * 0.5 + 'px';
});
