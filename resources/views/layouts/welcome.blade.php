<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>St. Paul University Philippines - Office of the Registrar</title>
    <link rel="stylesheet" href= {{ asset('css/style.css') }}>
</head>
<body>
    <!-- Header -->
    <header class="header">
        <div class="header-container">
            <div class="logo">
                <img src="{{ asset('images/5x5 ft_LOGO.png') }}" alt="St. Paul University Philippines Logo" class="logo-image">
                <div class="logo-text">St. Paul University Philippines<br><small>Office of the Registrar</small></div>
            </div>
            <nav>
                <ul class="nav">
                    <li><a href="#" class="nav-link active" data-section="home">Home</a></li>
                    <li><a href="#" class="nav-link" data-section="about">About Us</a></li>
                    <li><a href="#" class="nav-link" data-section="services">Services</a></li>
                    <li><a href="#" class="nav-link" data-section="contact">Contact Us</a></li>
                </ul>
            </nav>
        </div>
    </header>

    <!-- Hero Section -->
    <section class="hero">
        <div class="hero-container">
            <h1>Welcome to St. Paul University Philippines, Tuguegarao</h1>
            <h2>Office of the Registrar</h2>
            <p>Your gateway to academic records, credentials, and student services. We are committed to providing efficient and reliable registrar services to support your educational journey.</p>
        </div>
    </section>

    <!-- Main Content -->
    <main class="main-content">
        <!-- Home Section -->
        <section id="home" class="content-section active">
            <h2 class="section-title">Registrar Services</h2>
            <div class="cards-grid">
                <div class="card">
                    <div class="card-icon">📋</div>
                    <h3>Student Records</h3>
                    <p>Access and manage your academic records, transcripts, and enrollment history with our secure online system.</p>
                </div>
                <div class="card">
                    <div class="card-icon">🎓</div>
                    <h3>Credentials</h3>
                    <p>Request official transcripts, diplomas, certifications, and other academic credentials for your professional needs.</p>
                </div>
                <div class="card">
                    <div class="card-icon">💰</div>
                    <h3>Scholarships</h3>
                    <p>Explore scholarship opportunities and manage your financial aid applications through our comprehensive system.</p>
                </div>
                <div class="card">
                    <div class="card-icon">📅</div>
                    <h3>Enrollment</h3>
                    <p>Complete your enrollment process, course registration, and academic planning with our streamlined services.</p>
                </div>
            </div>
        </section>

        <!-- About Us Section -->
        <section id="about" class="content-section">
            <h2 class="section-title">About Our Office</h2>
            <div class="card">
                <h3>Mission</h3>
                <p>The Office of the Registrar is dedicated to maintaining accurate academic records, providing exceptional student services, and supporting the academic mission of St. Paul University Philippines through efficient and reliable registrar services.</p>
            </div>
            <div class="info-grid">
                <div class="info-item">
                    <h4>Our Services</h4>
                    <p>We provide comprehensive registrar services including student record management, credential processing, enrollment support, and academic documentation.</p>
                </div>
                <div class="info-item">
                    <h4>Office Hours</h4>
                    <p>Monday - Friday: 8:00 AM - 5:00 PM<br>Saturday: 8:00 AM - 12:00 PM<br>Sunday: Closed</p>
                </div>
                <div class="info-item">
                    <h4>Our Commitment</h4>
                    <p>We are committed to maintaining the highest standards of accuracy, confidentiality, and efficiency in all our services.</p>
                </div>
                <div class="info-item">
                    <h4>Student Support</h4>
                    <p>Our experienced staff is here to assist students with all their academic record needs and provide guidance throughout their educational journey.</p>
                </div>
            </div>
        </section>

        <!-- Services Section -->
        <section id="services" class="content-section">
            <h2 class="section-title">Our Services</h2>
            <div class="cards-grid">
                <div class="card">
                    <div class="card-icon">📜</div>
                    <h3>Academic Records</h3>
                    <p>Complete management of student academic records, grades, and transcripts with secure access and processing.</p>
                </div>
                <div class="card">
                    <div class="card-icon">🏆</div>
                    <h3>Certifications</h3>
                    <p>Official academic certifications, degree verifications, and document authentication services.</p>
                </div>
                <div class="card">
                    <div class="card-icon">📊</div>
                    <h3>Enrollment Management</h3>
                    <p>Course registration, schedule management, and enrollment verification services for all students.</p>
                </div>
            </div>

        </section>

        <!-- Contact Us Section -->
        <section id="contact" class="content-section">
            <h2 class="section-title">Contact Information</h2>
            <div class="cards-grid">
                <div class="card">
                    <div class="card-icon">📍</div>
                    <h3>Office Location</h3>
                    <p>St. Paul University Philippines<br>Tuguegarao, Cagayan<br>Philippines</p>
                </div>
                <div class="card">
                    <div class="card-icon">📞</div>
                    <h3>Phone & Email</h3>
                    <p>Phone: (078) 844-xxxx<br>Email: registrar@spup.edu.ph<br>Fax: (078) 844-xxxx</p>
                </div>
                <div class="card">
                    <div class="card-icon">🕒</div>
                    <h3>Office Hours</h3>
                    <p>Monday - Friday: 8:00 AM - 5:00 PM<br>Saturday: 8:00 AM - 12:00 PM<br>Sunday: Closed</p>
                </div>
            </div>

            <div class="card" style="margin-top: 2rem;">
                <h3>Get in Touch</h3>
                <p>For inquiries about academic records, credentials, or any registrar services, please visit our office during business hours or contact us through the information provided above. Our dedicated staff is ready to assist you with all your academic documentation needs.</p>
            </div>
        </section>
    </main>

    <!-- Bottom Action Buttons -->
    <section class="bottom-actions">
        <div class="container">
            <h2 class="section-title">Quick Access</h2>
            <div class="action-buttons">
                <button class="btn" id="bottomCredentialBtn">📋 Credential Request</button>
                <a href="{{ route('login') }}" class="btn btn-secondary">🎓 Scholarship Login</a>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="footer">
        <div class="footer-container">
            <p>&copy; 2025 St. Paul University Philippines - Office of the Registrar. All rights reserved.</p>
            <p>Committed to Excellence in Academic Services</p>
        </div>
    </footer>

    <script src="{{ asset('js/script 1.js') }}"></script>
</body>
</html>




