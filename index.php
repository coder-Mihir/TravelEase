<?php
session_start();
require_once 'database.php';

// Fetch featured packages from DB (increased to 5)
$packages_result = $conn->query("SELECT * FROM packages WHERE available = 1 LIMIT 5");

// Fetch reviews from DB
$reviews_result = $conn->query("SELECT * FROM reviews ORDER BY id DESC LIMIT 6");
$db_reviews = [];
if ($reviews_result && $reviews_result->num_rows > 0) {
    while ($row = $reviews_result->fetch_assoc()) {
        $db_reviews[] = $row;
    }
}

// FAQ data
$faqs = [
    [
        'q' => 'How do I book a travel package on TravelEase?',
        'a' => 'Simply browse our Packages page, select your preferred destination, choose your travel date and number of travelers, then proceed to payment. You will receive an instant booking confirmation with a unique transaction ID.'
    ],
    [
        'q' => 'What payment methods are accepted?',
        'a' => 'We accept Credit Card, Debit Card, UPI (GPay, PhonePe, Paytm), and Net Banking. All transactions are secured with end-to-end encryption for your safety.'
    ],
    [
        'q' => 'Can I cancel or modify my booking?',
        'a' => 'Yes, you can cancel or modify your booking from the My Bookings section. Please note that cancellation policies vary depending on the destination and travel date.'
    ],
    [
        'q' => 'Are the package prices inclusive of all charges?',
        'a' => 'Yes! All our package prices are all-inclusive covering accommodation, guided tours, and local transfers. There are absolutely no hidden charges. What you see is what you pay.'
    ],
    [
        'q' => 'How do I contact customer support?',
        'a' => 'You can reach our support team via email at support@TravelEase.com or call +91 98765 43210. Support is available Monday to Saturday, 9 AM to 6 PM IST.'
    ],
    [
        'q' => 'Is it safe to book and pay online on TravelEase?',
        'a' => 'Absolutely. TravelEase uses secure HTTPS connections, encrypted payment gateways, and follows strict data protection standards to ensure your personal and financial information is completely safe.'
    ],
    [
        'q' => 'Can I book for a group of travelers?',
        'a' => 'Yes! When booking, you can specify up to 20 travelers for any package. The total price is automatically calculated based on the per-person rate multiplied by the number of travelers.'
    ]
];
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="TravelEase – Discover handcrafted travel packages to the world's most beautiful destinations. Best prices, secure payments.">
    <meta name="keywords" content="travel, tour booking, travel packages, Paris, Tokyo, Bali, Dubai">
    <title>TravelEase – Explore the World</title>
    <link rel="stylesheet" href="style.css">
    <style>
        /* ===== ANNOUNCEMENT BAR ===== */
        .announcement-bar {
            background: linear-gradient(90deg, var(--accent), #e06b00);
            color: white;
            text-align: center;
            padding: 9px 20px;
            font-size: 13px;
            font-weight: 600;
            letter-spacing: 0.3px;
        }

        .announcement-bar a {
            color: white;
            text-decoration: underline;
            margin-left: 8px;
        }

        .logo-accent {
            color: #e06b00;
        }

        /* ===== NAVBAR ===== */
        .nav-links>li>a:not(.btn-nav) {
            color: #111 !important;
        }

        /* Hover effect */
        .nav-links>li>a:not(.btn-nav):hover {
            color: var(--accent) !important;
            background: transparent !important;
        }

        /* Active Page Color (Home) */
        .nav-links>li>a:not(.btn-nav).active {
            color: var(--accent) !important;
            font-weight: 700;
            border-bottom: 2px solid var(--accent);
        }

        /* ===== HERO VIDEO OVERLAY ===== */
        .hero-badge {
            display: inline-block;
            background: rgba(255, 255, 255, 0.2);
            border: 1px solid rgba(255, 255, 255, 0.4);
            color: white;
            padding: 6px 16px;
            border-radius: 25px;
            font-size: 13px;
            font-weight: 600;
            margin-bottom: 18px;
            backdrop-filter: blur(4px);
        }

        .hero-stats {
            display: flex;
            gap: 40px;
            margin-top: 40px;
            justify-content: center;
            flex-wrap: wrap;
        }

        .hero-stat {
            text-align: center;
            color: white;
        }

        .hero-stat h3 {
            font-size: 28px;
            font-weight: 800;
            color: var(--accent);
        }

        .hero-stat p {
            font-size: 13px;
            opacity: 0.85;
            margin: 0;
        }

        /* ===== STATS COUNTER SECTION ===== */
        .stats-section {
            background: linear-gradient(135deg, var(--dark), var(--primary));
            padding: 60px 40px;
        }

        .stats-counter-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
            gap: 30px;
            max-width: 1000px;
            margin: 0 auto;
            text-align: center;
        }

        .counter-item {
            color: white;
        }

        .counter-item .counter-number {
            font-size: 48px;
            font-weight: 800;
            color: var(--accent);
            display: block;
            line-height: 1;
        }

        .counter-item .counter-label {
            font-size: 15px;
            opacity: 0.88;
            margin-top: 8px;
            font-weight: 500;
        }

        .counter-item .counter-icon {
            font-size: 36px;
            margin-bottom: 10px;
            display: block;
        }

        .package-card {
            display: flex;
            flex-direction: column;
        }

        .card-body {
            display: flex;
            flex-direction: column;
            flex: 1;
        }

        /* Targets the anchor tag (Book Now) to push it to the bottom */
        .card-body>.btn {
            margin-top: auto;
        }

        /* ===== PACKAGES SECTION ===== */
        .package-badge {
            background: var(--accent);
            color: white;
            font-size: 11px;
            font-weight: 700;
            padding: 3px 10px;
            border-radius: 20px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .card-rating {
            color: #f7b731;
            font-size: 13px;
            margin-bottom: 8px;
        }

        .card-includes {
            display: flex;
            gap: 8px;
            flex-wrap: wrap;
            margin-bottom: 14px;
        }

        .include-tag {
            background: var(--light);
            color: var(--primary);
            font-size: 11px;
            padding: 3px 9px;
            border-radius: 12px;
            font-weight: 600;
        }

        /* Ensure 5 columns for featured packages */
        .featured-grid-5 {
            display: grid;
            grid-template-columns: repeat(5, 1fr);
            gap: 20px;
            max-width: 1400px;
            margin: 0 auto;
        }

        @media (max-width: 1200px) {
            .featured-grid-5 {
                grid-template-columns: repeat(3, 1fr);
            }
        }

        @media (max-width: 768px) {
            .featured-grid-5 {
                grid-template-columns: repeat(2, 1fr);
            }
        }

        @media (max-width: 480px) {
            .featured-grid-5 {
                grid-template-columns: 1fr;
            }
        }

        /* ===== PROCESS SECTION ===== */
        .process-section {
            background: var(--white);
        }

        .process-steps {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 0;
            max-width: 1000px;
            margin: 0 auto;
            position: relative;
        }

        .process-steps::before {
            content: '';
            position: absolute;
            top: 40px;
            left: 15%;
            right: 15%;
            height: 3px;
            background: linear-gradient(90deg, var(--primary), var(--secondary));
            z-index: 0;
        }

        .process-step {
            text-align: center;
            padding: 20px 15px;
            position: relative;
            z-index: 1;
        }

        .step-circle {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            color: white;
            font-size: 28px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 16px;
            box-shadow: 0 6px 20px rgba(0, 119, 182, 0.35);
            font-weight: 800;
        }

        .process-step h3 {
            font-size: 16px;
            color: var(--dark);
            margin-bottom: 8px;
        }

        .process-step p {
            font-size: 13px;
            color: var(--gray);
        }

        /* Ensure 5 columns for featured packages */
        .features-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 20px;
            margin: 0 auto;
        }

        /* ===== Reviews ===== */
        .testimonials-section {
            background: var(--light);
        }

        .testimonials-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 25px;
            max-width: 1200px;
            margin: 0 auto;
        }

        .testimonial-card {
            background: white;
            padding: 28px;
            border-radius: var(--radius);
            box-shadow: var(--shadow);
            position: relative;
            transition: var(--transition);
        }

        .testimonial-card:hover {
            transform: translateY(-5px);
        }

        .testimonial-card::before {
            content: '"';
            font-size: 80px;
            color: var(--primary);
            opacity: 0.12;
            position: absolute;
            top: 10px;
            left: 20px;
            font-family: Georgia, serif;
            line-height: 1;
        }

        .testimonial-text {
            font-size: 14px;
            color: #555;
            line-height: 1.7;
            margin-bottom: 20px;
            font-style: italic;
        }

        .testimonial-author {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .author-avatar {
            width: 46px;
            height: 46px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            color: white;
            font-size: 15px;
            font-weight: 700;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .author-info h4 {
            font-size: 15px;
            color: var(--dark);
            margin-bottom: 2px;
        }

        .author-info span {
            font-size: 12px;
            color: var(--gray);
        }

        .star-rating {
            color: #f7b731;
            font-size: 13px;
            margin-bottom: 12px;
        }

        /* ===== FAQ SECTION ===== */
        .faq-section {
            background: var(--white);
        }

        .faq-container {
            max-width: 800px;
            margin: 0 auto;
        }

        .faq-item {
            background: white;
            border: 1px solid #eee;
            border-radius: 10px;
            margin-bottom: 12px;
            overflow: hidden;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
        }

        .faq-question {
            padding: 18px 22px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            cursor: pointer;
            font-weight: 600;
            color: var(--dark);
            font-size: 15px;
            transition: var(--transition);
        }

        .faq-question:hover {
            background: var(--light);
            color: var(--primary);
        }

        .faq-question.open {
            background: var(--primary);
            color: white;
        }

        .faq-icon {
            font-size: 20px;
            font-weight: 400;
            transition: transform 0.3s;
            flex-shrink: 0;
        }

        .faq-question.open .faq-icon {
            transform: rotate(45deg);
        }

        .faq-answer {
            display: none;
            padding: 16px 22px;
            font-size: 14px;
            color: #555;
            line-height: 1.7;
            border-top: 1px solid #eee;
        }

        .faq-answer.open {
            display: block;
        }

        /* ===== FOOTER ENHANCED ===== */
        .footer-main {
            background: var(--dark);
            color: rgba(255, 255, 255, 0.8);
            padding: 60px 40px 30px;
        }

        .footer-grid {
            display: grid;
            grid-template-columns: 2fr 1fr 1fr 1fr;
            gap: 40px;
            max-width: 1200px;
            margin: 0 auto 40px;
        }

        .footer-brand .logo {
            font-size: 22px;
            font-weight: 800;
            color: white;
            margin-bottom: 12px;
        }

        .footer-brand p {
            font-size: 13px;
            line-height: 1.7;
            opacity: 0.75;
        }

        .footer-col h4 {
            color: white;
            font-size: 15px;
            margin-bottom: 16px;
            font-weight: 700;
        }

        .footer-col ul {
            list-style: none;
        }

        .footer-col ul li {
            margin-bottom: 9px;
        }

        .footer-col ul li a {
            color: rgba(255, 255, 255, 0.65);
            font-size: 13px;
            transition: var(--transition);
        }

        .footer-col ul li a:hover {
            color: var(--accent);
            padding-left: 5px;
        }

        footer {
            background: var(--dark);
            padding: 0;
            text-align: initial;
        }

        .footer-bottom {
            border-top: 1px solid rgba(255, 255, 255, 0.1);
            padding: 22px 40px 26px;
            display: flex;
            justify-content: center;
            grid-template-columns: minmax(0, 1fr) minmax(0, 1fr);
            align-items: center;
            max-width: 1300px;
            margin: 0 auto;
            gap: 24px;
            box-sizing: border-box;
        }

        .footer-bottom p {
            align-items: center;
            text-align: center;
            right: 50%;
            font-size: 13px;
            opacity: 0.65;
            margin: 0;
            line-height: 1.5;
        }

        .footer-bottom p:first-child {
            text-align: left;
        }

        .footer-bottom p:last-child {
            text-align: right;
        }

        /* ===== BACK TO TOP ===== */
        .back-to-top {
            position: fixed;
            bottom: 88px;
            right: 28px;
            width: 46px;
            height: 46px;
            background: var(--primary);
            color: white;
            border-radius: 50%;
            display: none;
            align-items: center;
            justify-content: center;
            font-size: 20px;
            cursor: pointer;
            box-shadow: 0 4px 15px rgba(0, 119, 182, 0.4);
            z-index: 9999;
            transition: var(--transition);
            border: none;
            text-decoration: none;
        }

        .back-to-top:hover {
            background: var(--accent);
            transform: translateY(-3px);
        }

        .back-to-top.show {
            display: flex;
        }

        /* ===== RESPONSIVE EXTRAS ===== */
        @media (max-width: 900px) {
            .footer-grid {
                grid-template-columns: 1fr 1fr;
            }

            .process-steps::before {
                display: none;
            }

            .footer-bottom {
                grid-template-columns: 1fr;
                text-align: center;
                gap: 8px;
                padding: 18px 20px 22px;
            }

            .footer-bottom p:first-child {
                text-align: center;
            }

            .footer-bottom p:last-child {
                text-align: center;
            }
        }

        @media (max-width: 600px) {
            .footer-grid {
                grid-template-columns: 1fr;
            }

            .hero-stats {
                gap: 20px;
            }

            .counter-item .counter-number {
                font-size: 36px;
            }
        }
    </style>
</head>

<body>

    <!-- ===== ANNOUNCEMENT BAR ===== -->
    <div class="announcement-bar">
        🎉 Special Offer: Get 10% OFF on all bookings this month! Use code <strong>TravelEase10</strong>
        <a href="packages.php">Book Now →</a>
    </div>

    <!-- ===== NAVBAR ===== -->
    <nav class="navbar">
        <div class="logo"><a href="index.php">✈Travel<span class="logo-accent">Ease</span></a></div>
        <ul class="nav-links">
            <li><a href="index.php" class="active">Home</a></li>
            <li><a href="packages.php">Packages</a></li>
            <li><a href="#about">About</a></li>
            <?php if (isLoggedIn()): ?>
                <li><a href="my_bookings.php">My Bookings</a></li>
                <li><a href="logout.php" class="btn-nav">Logout</a></li>
            <?php else: ?>
                <li><a href="login.php">Login</a></li>
                <li><a href="register.php" class="btn-nav">Get Started</a></li>
            <?php endif; ?>
        </ul>
    </nav>

    <!-- ===== HERO SECTION ===== -->
    <section class="hero">
        <span class="hero-badge">✈ Your Journey Starts Here</span>
        <h1>Discover the World with <span>TravelEase</span></h1>
        <p>Handcrafted travel packages to the world's most beautiful destinations</p>
        <div class="hero-btns">
            <a href="packages.php" class="btn btn-primary">🌍 Explore Packages</a>
        </div>
        <div class="hero-stats">
            <div class="hero-stat">
                <h3>10K+</h3>
                <p>Happy Travelers</p>
            </div>
            <div class="hero-stat">
                <h3>50+</h3>
                <p>Destinations</p>
            </div>
            <div class="hero-stat">
                <h3>4.9★</h3>
                <p>Average Rating</p>
            </div>
            <div class="hero-stat">
                <h3>24/7</h3>
                <p>Support</p>
            </div>
        </div>
    </section>

    <!-- ===== FEATURED PACKAGES ===== -->
    <section class="section" style="background: var(--white);">
        <div class="section-title">
            <h2>Featured <span>Packages</span></h2>
            <div class="divider"></div>
            <p>Our most loved travel packages — curated just for you</p>
        </div>
        <div class="featured-grid-5">
            <?php if ($packages_result && $packages_result->num_rows > 0):
                while ($pkg = $packages_result->fetch_assoc()):
            ?>
                    <div class="package-card">
                        <div style="position:relative;">
                            <img src="<?= htmlspecialchars(packageImageUrl($pkg['destination'], $pkg['image'] ?? '')) ?>" alt="<?= htmlspecialchars($pkg['destination']) ?>">
                            <span class="package-badge" style="position:absolute; top:14px; left:14px;">⭐ Popular</span>
                        </div>
                        <div class="card-body">
                            <!-- <div class="card-rating">★★★★★ <span style="color:var(--gray); font-size:12px;">(4.9/5)</span></div> -->
                            <h3>📍 <?= htmlspecialchars($pkg['destination']) ?></h3>
                            <p><?= htmlspecialchars(substr($pkg['description'], 0, 80)) ?>...</p>
                            <div class="card-includes">
                                <span class="include-tag">🏨 Hotel</span>
                                <span class="include-tag">🚌 Transfer</span>
                            </div>
                            <div class="card-meta">
                                <span class="price">&#8377;<?= number_format($pkg['price'], 2) ?></span>
                                <span class="duration">⏱ <?= htmlspecialchars($pkg['duration']) ?></span>
                            </div>
                            <?php if (isLoggedIn()): ?>
                                <a href="booking.php?id=<?= $pkg['id'] ?>" class="btn btn-primary btn-full">Book Now →</a>
                            <?php else: ?>
                                <a href="login.php" class="btn btn-secondary btn-full">Login to Book</a>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endwhile;
            else: ?>
                <p style="text-align:center; color:var(--gray); grid-column: 1 / -1;">No packages available at the moment.</p>
            <?php endif; ?>
        </div>
        <div style="text-align:center; margin-top:35px;">
            <a href="packages.php" class="btn btn-secondary">View All Packages →</a>
        </div>
    </section>

    <!-- ===== HOW IT WORKS ===== -->
    <section class="section process-section" id="about">
        <div class="section-title">
            <h2>How It <span>Works</span></h2>
            <div class="divider"></div>
            <p>Book your dream vacation in just 4 simple steps</p>
        </div>
        <div class="process-steps">
            <div class="process-step">
                <div class="step-circle">1</div>
                <h3>Browse Packages</h3>
                <p>Explore our curated collection of global travel packages sorted by destination and budget.</p>
            </div>
            <div class="process-step">
                <div class="step-circle">2</div>
                <h3>Create Account</h3>
                <p>Register for free and unlock full access to booking, tracking, and personalized recommendations.</p>
            </div>
            <div class="process-step">
                <div class="step-circle">3</div>
                <h3>Book Your Trip</h3>
                <p>Select your travel date, number of travelers, and confirm your booking in just a few clicks.</p>
            </div>
            <div class="process-step">
                <div class="step-circle">4</div>
                <h3>Pay & Travel</h3>
                <p>Complete secure payment via UPI, card, or net banking and get ready for your adventure!</p>
            </div>
        </div>
    </section>

    <!-- ===== WHY CHOOSE US ===== -->
    <section class="section" style="background: var(--white);">
        <div class="section-title">
            <h2>Why Choose <span>Us</span></h2>
            <div class="divider"></div>
            <p>Everything you need for a perfect travel experience</p>
        </div>
        <div class="features-grid">
            <div class="feature-card">
                <div class="feature-icon">🌍</div>
                <h3>Global Destinations</h3>
                <p>Explore 50+ top destinations across Europe, Asia, Middle East, Americas, and more.</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon">💰</div>
                <h3>Best Prices</h3>
                <p>Competitive pricing with transparent costs and absolutely no hidden charges guaranteed.</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon">🔒</div>
                <h3>Secure Booking</h3>
                <p>End-to-end encrypted payments and robust data protection for peace of mind.</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon">📱</div>
                <h3>Easy Management</h3>
                <p>Manage, track, and update all your bookings from a clean, user-friendly dashboard.</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon">🎧</div>
                <h3>24/7 Support</h3>
                <p>Round-the-clock human support to assist you at every step of your journey.</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon">🔄</div>
                <h3>Flexible Cancellation</h3>
                <p>Worry-free booking with flexible cancellation and modification policies available.</p>
            </div>
        </div>
    </section>

    <!-- ===== Reviews ===== -->
    <section class="section testimonials-section">
        <div class="section-title">
            <h2>What Travelers <span>Say</span></h2>
            <div class="divider"></div>
            <p>Real reviews from real TravelEase travelers</p>
        </div>
        <div class="testimonials-grid">
            <?php if (!empty($db_reviews)): ?>
                <?php foreach ($db_reviews as $t): ?>
                    <div class="testimonial-card">
                        <div class="star-rating">
                            <?= str_repeat('★', (int)$t['rating']) . str_repeat('☆', 5 - (int)$t['rating']) ?>
                        </div>
                        <p class="testimonial-text"><?= htmlspecialchars($t['review']) ?></p>
                        <div class="testimonial-author">
                            <div class="author-avatar"><?= htmlspecialchars($t['avatar']) ?></div>
                            <div class="author-info">
                                <h4><?= htmlspecialchars($t['name']) ?></h4>
                                <span>📍 <?= htmlspecialchars($t['place']) ?></span>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p style="text-align:center; width: 100%; color:var(--gray);">No reviews available at the moment.</p>
            <?php endif; ?>
        </div>
    </section>

    <!-- ===== FAQ SECTION ===== -->
    <section class="section faq-section">
        <div class="section-title">
            <h2>Frequently Asked <span>Questions</span></h2>
            <div class="divider"></div>
            <p>Everything you need to know about booking with TravelEase</p>
        </div>
        <div class="faq-container">
            <?php foreach ($faqs as $i => $faq): ?>
                <div class="faq-item">
                    <div class="faq-question" onclick="toggleFaq(<?= $i ?>)" id="faq-q-<?= $i ?>">
                        <span><?= htmlspecialchars($faq['q']) ?></span>
                        <span class="faq-icon">+</span>
                    </div>
                    <div class="faq-answer" id="faq-a-<?= $i ?>">
                        <?= htmlspecialchars($faq['a']) ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </section>

    <!-- ===== CTA SECTION ===== -->
    <section class="section" style="background: linear-gradient(135deg, var(--dark), var(--primary)); text-align:center; color: white;">
        <h2 style="font-size:36px; font-weight:800; margin-bottom:15px;">Ready to Start Your Journey? 🌏</h2>
        <p style="opacity:0.88; margin-bottom:35px; font-size:17px; max-width:550px; margin-left:auto; margin-right:auto;">
            Join 10,000+ happy travelers who have already booked their dream vacations with TravelEase.
        </p>
        <div style="display:flex; gap:15px; justify-content:center; flex-wrap:wrap;">
            <?php if (!isLoggedIn()): ?>
                <a href="register.php" class="btn btn-primary">🚀 Get Started Free</a>
            <?php endif; ?>
            <a href="packages.php" class="btn btn-outline">📦 Browse Packages</a>
        </div>
    </section>

    <!-- ===== FOOTER ===== -->
    <footer>
        <div class="footer-bottom">
            <p>© 2026 <span style="color:var(--accent);">TravelEase</span> — All rights reserved. Built with ❤️ </p>
        </div>
    </footer>

    <!-- ===== BACK TO TOP BUTTON ===== -->
    <a href="#" class="back-to-top" id="backToTop" title="Back to top">↑</a>

    <!-- ===== SCRIPTS ===== -->
    <script src="script.js"></script>
    <script>
        // ===== FAQ TOGGLE =====
        function toggleFaq(index) {
            const question = document.getElementById('faq-q-' + index);
            const answer = document.getElementById('faq-a-' + index);
            const isOpen = answer.classList.contains('open');

            // Close all
            document.querySelectorAll('.faq-answer').forEach(a => a.classList.remove('open'));
            document.querySelectorAll('.faq-question').forEach(q => q.classList.remove('open'));

            // Open clicked if it was closed
            if (!isOpen) {
                answer.classList.add('open');
                question.classList.add('open');
            }
        }

        // ===== COUNTER ANIMATION =====
        function animateCounters() {
            document.querySelectorAll('.counter-number').forEach(counter => {
                const target = parseInt(counter.getAttribute('data-target'));
                const duration = 2000;
                const step = target / (duration / 16);
                let current = 0;
                const timer = setInterval(() => {
                    current += step;
                    if (current >= target) {
                        counter.textContent = target.toLocaleString('en-IN') + (target >= 1000 ? '' : '');
                        clearInterval(timer);
                    } else {
                        counter.textContent = Math.floor(current).toLocaleString('en-IN');
                    }
                }, 16);
            });
        }

        // Trigger counter when stats section is visible
        const statsSection = document.querySelector('.stats-section');
        let counterAnimated = false;
        if (statsSection) {
            const observer = new IntersectionObserver(entries => {
                entries.forEach(entry => {
                    if (entry.isIntersecting && !counterAnimated) {
                        counterAnimated = true;
                        animateCounters();
                    }
                });
            }, {
                threshold: 0.3
            });
            observer.observe(statsSection);
        }

        // ===== BACK TO TOP =====
        const backToTopBtn = document.getElementById('backToTop');
        window.addEventListener('scroll', () => {
            if (window.scrollY > 400) {
                backToTopBtn.classList.add('show');
            } else {
                backToTopBtn.classList.remove('show');
            }
        });
        backToTopBtn.addEventListener('click', function(e) {
            e.preventDefault();
            window.scrollTo({
                top: 0,
                behavior: 'smooth'
            });
        });

        // ===== SMOOTH SCROLL FOR ANCHOR LINKS =====
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function(e) {
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    e.preventDefault();
                    target.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            });
        });

        // ===== NAVBAR SCROLL EFFECT =====
        window.addEventListener('scroll', () => {
            const navbar = document.querySelector('.navbar');
            if (window.scrollY > 60) {
                navbar.style.boxShadow = '0 4px 25px rgba(0,0,0,0.25)';
            } else {
                navbar.style.boxShadow = 'none';
            }
        });

        // ===== ANIMATE CARDS ON SCROLL =====
        const cards = document.querySelectorAll('.package-card, .feature-card, .testimonial-card');
        const cardObserver = new IntersectionObserver(entries => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.style.opacity = '1';
                    entry.target.style.transform = 'translateY(0)';
                }
            });
        }, {
            threshold: 0.1
        });

        cards.forEach(card => {
            card.style.opacity = '0';
            card.style.transform = 'translateY(30px)';
            card.style.transition = 'opacity 0.5s ease, transform 0.5s ease';
            cardObserver.observe(card);
        });
    </script>

</body>

</html>