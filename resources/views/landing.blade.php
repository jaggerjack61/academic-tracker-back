<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Academic Tracker - Empowering Education Management</title>
    <meta name="description" content="Academic Tracker is a comprehensive education management platform for schools to track students, teachers, parents, classes, assignments, and grades.">
    
    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="/assets/img/favicon/favicon.ico">
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    
    <!-- Icons -->
    <link rel="stylesheet" href="/assets/vendor/fonts/boxicons.css">
    
    <!-- Landing Page CSS -->
    <link rel="stylesheet" href="/css/landing.css">
</head>
<body class="landing-page">

    <!-- Navigation -->
    <nav class="landing-nav" id="navbar">
        <a href="/" class="nav-logo">📚 Academic Tracker</a>
        <div class="nav-links">
            <a href="#features" class="nav-link">Features</a>
            <a href="#how-it-works" class="nav-link">How It Works</a>
            <a href="#stats" class="nav-link">Statistics</a>
            <a href="{{ route('login') }}" class="btn-login">Login</a>
        </div>
    </nav>

    <!-- Hero Section with Parallax -->
    <section class="hero-section" id="hero">
        <div class="hero-bg" id="parallax-bg"></div>
        <div class="hero-overlay"></div>
        
        <!-- Floating Particles -->
        <div class="hero-particles">
            <div class="particle"></div>
            <div class="particle"></div>
            <div class="particle"></div>
            <div class="particle"></div>
            <div class="particle"></div>
            <div class="particle"></div>
            <div class="particle"></div>
            <div class="particle"></div>
        </div>
        
        <div class="hero-content">
            <span class="hero-badge">🎓 Modern Education Platform</span>
            <h1 class="hero-title">Empowering Education Through Smart Tracking</h1>
            <p class="hero-subtitle">
                A comprehensive platform for schools to seamlessly manage students, teachers, 
                parents, classes, assignments, and grades — all in one place.
            </p>
            <div class="hero-cta">
                <a href="{{ route('login') }}" class="btn-primary">Get Started</a>
                <a href="#features" class="btn-secondary">Learn More</a>
            </div>
        </div>
        
        <div class="scroll-indicator">
            <span></span>
        </div>
    </section>

    <!-- Features Section -->
    <section class="features-section" id="features">
        <div class="section-header">
            <span class="section-label">Features</span>
            <h2 class="section-title">Everything You Need to Manage Education</h2>
            <p class="section-desc">
                Our platform provides powerful tools for every stakeholder in the education ecosystem.
            </p>
        </div>
        
        <div class="features-grid">
            <div class="feature-card fade-in">
                <div class="feature-icon student">
                    <i class='bx bxs-graduation'></i>
                </div>
                <h3 class="feature-title">Student Management</h3>
                <p class="feature-desc">
                    Track student enrollment, attendance, assignments, and academic progress. 
                    Students can view their own dashboard with classes and marks.
                </p>
            </div>
            
            <div class="feature-card fade-in">
                <div class="feature-icon teacher">
                    <i class='bx bxs-chalkboard'></i>
                </div>
                <h3 class="feature-title">Teacher Portal</h3>
                <p class="feature-desc">
                    Empower teachers to manage classes, create activities, grade assignments, 
                    and track student performance with powerful analytics.
                </p>
            </div>
            
            <div class="feature-card fade-in">
                <div class="feature-icon parent">
                    <i class='bx bxs-user-voice'></i>
                </div>
                <h3 class="feature-title">Parent Access</h3>
                <p class="feature-desc">
                    Keep parents informed with real-time access to their children's academic 
                    progress, assignments, and teacher communications.
                </p>
            </div>
            
            <div class="feature-card fade-in">
                <div class="feature-icon grade">
                    <i class='bx bxs-bar-chart-alt-2'></i>
                </div>
                <h3 class="feature-title">Grade Tracking</h3>
                <p class="feature-desc">
                    Comprehensive grade management with support for multiple terms, subjects, 
                    activity types, and customizable grading scales.
                </p>
            </div>
        </div>
    </section>

    <!-- Stats Section with Parallax -->
    <section class="stats-section" id="stats">
        <div class="stats-bg"></div>
        <div class="stats-overlay"></div>
        
        <div class="stats-content">
            <div class="stats-grid">
                <div class="stat-item fade-in">
                    <div class="stat-number">1000+</div>
                    <div class="stat-label">Students</div>
                </div>
                <div class="stat-item fade-in">
                    <div class="stat-number">50+</div>
                    <div class="stat-label">Teachers</div>
                </div>
                <div class="stat-item fade-in">
                    <div class="stat-number">100+</div>
                    <div class="stat-label">Classes</div>
                </div>
                <div class="stat-item fade-in">
                    <div class="stat-number">99%</div>
                    <div class="stat-label">Satisfaction</div>
                </div>
            </div>
        </div>
    </section>

    <!-- How It Works Section -->
    <section class="how-section" id="how-it-works">
        <div class="section-header">
            <span class="section-label">How It Works</span>
            <h2 class="section-title">Simple Steps to Get Started</h2>
            <p class="section-desc">
                Get up and running in minutes with our intuitive platform.
            </p>
        </div>
        
        <div class="steps-container">
            <div class="step slide-left">
                <div class="step-number">1</div>
                <div class="step-content">
                    <h3 class="step-title">Create Your Account</h3>
                    <p class="step-desc">
                        Administrators set up the school account, configure grades, subjects, 
                        and academic terms to match your institution's needs.
                    </p>
                </div>
            </div>
            
            <div class="step slide-right">
                <div class="step-number">2</div>
                <div class="step-content">
                    <h3 class="step-title">Add Users & Classes</h3>
                    <p class="step-desc">
                        Register teachers, students, and parents. Create classes and assign 
                        teachers to manage specific subjects and grades.
                    </p>
                </div>
            </div>
            
            <div class="step slide-left">
                <div class="step-number">3</div>
                <div class="step-content">
                    <h3 class="step-title">Track Everything</h3>
                    <p class="step-desc">
                        Teachers add activities and grades, students view their progress, 
                        and parents stay informed — all in real-time.
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="cta-section">
        <div class="cta-glow"></div>
        
        <div class="cta-content">
            <h2 class="cta-title">Ready to Transform Your School?</h2>
            <p class="cta-desc">
                Join schools already using Academic Tracker to streamline education management.
            </p>
            <a href="{{ route('login') }}" class="btn-primary">Login to Your Account</a>
        </div>
    </section>

    <!-- Footer -->
    <footer class="landing-footer">
        <p class="footer-text">
            &copy; {{ date('Y') }} Academic Tracker. Built with ❤️ by 
            <a href="#">Swole Devs</a>
        </p>
    </footer>

    <!-- JavaScript for Parallax & Animations -->
    <script>
        // Navbar scroll effect
        const navbar = document.getElementById('navbar');
        window.addEventListener('scroll', () => {
            if (window.scrollY > 50) {
                navbar.classList.add('scrolled');
            } else {
                navbar.classList.remove('scrolled');
            }
        });

        // Parallax effect on hero background
        const parallaxBg = document.getElementById('parallax-bg');
        window.addEventListener('scroll', () => {
            const scrolled = window.scrollY;
            if (parallaxBg) {
                parallaxBg.style.transform = `translateY(${scrolled * 0.4}px)`;
            }
        });

        // Smooth scroll for anchor links
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            });
        });

        // Intersection Observer for scroll animations
        const observerOptions = {
            root: null,
            rootMargin: '0px',
            threshold: 0.1
        };

        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('visible');
                }
            });
        }, observerOptions);

        // Observe all animated elements
        document.querySelectorAll('.fade-in, .slide-left, .slide-right').forEach(el => {
            observer.observe(el);
        });

        // Counter animation for stats
        const animateCounter = (element, target) => {
            let current = 0;
            const increment = target / 50;
            const timer = setInterval(() => {
                current += increment;
                if (current >= target) {
                    element.textContent = target + (element.textContent.includes('%') ? '%' : '+');
                    clearInterval(timer);
                } else {
                    element.textContent = Math.floor(current) + (element.textContent.includes('%') ? '%' : '+');
                }
            }, 30);
        };

        // Observe stats section
        const statsSection = document.getElementById('stats');
        let statsAnimated = false;

        const statsObserver = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting && !statsAnimated) {
                    statsAnimated = true;
                    document.querySelectorAll('.stat-number').forEach(stat => {
                        const text = stat.textContent;
                        const number = parseInt(text.replace(/\D/g, ''));
                        stat.textContent = '0';
                        animateCounter(stat, number);
                    });
                }
            });
        }, { threshold: 0.5 });

        if (statsSection) {
            statsObserver.observe(statsSection);
        }
    </script>

</body>
</html>
