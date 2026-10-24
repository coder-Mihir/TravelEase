<?php
session_start();
require_once 'database.php';

// ===== FETCH ALL PACKAGES FROM DB =====
$packages_result = $conn->query("SELECT * FROM packages WHERE available = 1 ORDER BY id ASC");
$all_packages = [];
while ($row = $packages_result->fetch_assoc()) {
    $all_packages[] = $row;
}

// ===== EXTRA DISPLAY METADATA (not stored in DB) =====
$display_meta = [
    'Goa' => [
        'rating'=>4.5, 'reviews'=>450, 'badge'=>'💚 Best Value',
        'badge_color'=>'#28a745',
        'includes'=>['Hotel','Transfer','Beach Tour','Fort Visit'],
        'discount'=>5
    ],
    'Statue of Unity, Gujarat' => [
        'rating'=>4.8, 'reviews'=>312, 'badge'=>'Trending',
        'badge_color'=>'#0077b6',
        'includes'=>['Hotel','Transfer','Statue Entry','Valley of Flowers'],
        'discount'=>8
    ],
    'Mumbai, Maharashtra' => [
        'rating'=>4.6, 'reviews'=>520, 'badge'=>'Popular',
        'badge_color'=>'#0077b6',
        'includes'=>['Hotel','City Tour','Marine Drive','Gateway of India'],
        'discount'=>5
    ],
    'Delhi' => [
        'rating'=>4.7, 'reviews'=>680, 'badge'=>'Heritage',
        'badge_color'=>'#6f42c1',
        'includes'=>['Hotel','Transfer','Red Fort','India Gate Tour'],
        'discount'=>0
    ],
    'Dubai' => [
        'rating'=>4.9, 'reviews'=>910, 'badge'=>'⭐ Best Seller',
        'badge_color'=>'#dc3545',
        'includes'=>['Hotel','Transfer','Desert Safari','Burj Khalifa'],
        'discount'=>0
    ],
    'Jaipur, Rajasthan' => [
        'rating'=>4.8, 'reviews'=>420, 'badge'=>'⭐ Popular',
        'badge_color'=>'#f77f00',
        'includes'=>['Hotel','Amer Fort','City Palace','Local Shopping'],
        'discount'=>5
    ],
    'Udaipur, Rajasthan' => [
        'rating'=>4.9, 'reviews'=>380, 'badge'=>'Romantic',
        'badge_color'=>'#dc3545',
        'includes'=>['Hotel','Lake Pichola Boat Ride','City Palace','Local Tour'],
        'discount'=>0
    ],
    'Jaisalmer, Rajasthan' => [
        'rating'=>4.7, 'reviews'=>290, 'badge'=>'Adventure',
        'badge_color'=>'#f77f00',
        'includes'=>['Hotel','Desert Camp','Camel Safari','Folk Dance'],
        'discount'=>8
    ],
    'Manali, Himachal Pradesh' => [
        'rating'=>4.8, 'reviews'=>760, 'badge'=>'⭐ Popular',
        'badge_color'=>'#0077b6',
        'includes'=>['Hotel','Transfer','Solang Valley','Mountain Tour'],
        'discount'=>5
    ],
    'Srinagar, Jammu and Kashmir' => [
        'rating'=>4.9, 'reviews'=>610, 'badge'=>'Scenic Escape',
        'badge_color'=>'#0077b6',
        'includes'=>['Hotel','Dal Lake Ride','Mughal Gardens','Local Transfer'],
        'discount'=>0
    ],
    'Bali' => [
        'rating'=>4.7, 'reviews'=>830, 'badge'=>'💚 Best Value',
        'badge_color'=>'#28a745',
        'includes'=>['Hotel','Transfer','Temple Tour','Beach Day'],
        'discount'=>10
    ],
    'Leh Ladakh' => [
        'rating'=>4.9, 'reviews'=>540, 'badge'=>'Adventure',
        'badge_color'=>'#6f42c1',
        'includes'=>['Hotel','Local Transfer','Pangong Lake','Monastery Tour'],
        'discount'=>0
    ],
    'Agra, Uttar Pradesh' => [
        'rating'=>4.8, 'reviews'=>720, 'badge'=>'Heritage',
        'badge_color'=>'#6f42c1',
        'includes'=>['Hotel','Taj Mahal Visit','Agra Fort','Local Guide'],
        'discount'=>5
    ],
    'Varanasi, Uttar Pradesh' => [
        'rating'=>4.8, 'reviews'=>390, 'badge'=>'Cultural Pick',
        'badge_color'=>'#6f42c1',
        'includes'=>['Hotel','Ganga Aarti','Boat Ride','Temple Tour'],
        'discount'=>0
    ],
    'Paris' => [
        'rating'=>4.9, 'reviews'=>620, 'badge'=>'⭐ Popular',
        'badge_color'=>'#f77f00',
        'includes'=>['Hotel','Eiffel Tour','Seine Cruise','Louvre Pass'],
        'discount'=>0
    ],
    'Rishikesh, Uttarakhand' => [
        'rating'=>4.6, 'reviews'=>340, 'badge'=>'Adventure',
        'badge_color'=>'#0077b6',
        'includes'=>['Hotel','River Rafting','Laxman Jhula','Yoga Session'],
        'discount'=>8
    ],
    'Nainital, Uttarakhand' => [
        'rating'=>4.7, 'reviews'=>310, 'badge'=>'Family Favorite',
        'badge_color'=>'#28a745',
        'includes'=>['Hotel','Naini Lake','Cable Car','Snow View Point'],
        'discount'=>5
    ],
    'Munnar, Kerala' => [
        'rating'=>4.9, 'reviews'=>460, 'badge'=>'💚 Best Value',
        'badge_color'=>'#28a745',
        'includes'=>['Hotel','Tea Garden Tour','Waterfalls','Hill Sightseeing'],
        'discount'=>5
    ],
    'Alleppey, Kerala' => [
        'rating'=>4.7, 'reviews'=>410, 'badge'=>'Relaxation',
        'badge_color'=>'#0077b6',
        'includes'=>['Hotel','Houseboat Cruise','Backwater Tour','Local Meals'],
        'discount'=>10
    ],
    'Ooty, Tamil Nadu' => [
        'rating'=>4.6, 'reviews'=>360, 'badge'=>'Hill Escape',
        'badge_color'=>'#28a745',
        'includes'=>['Hotel','Botanical Garden','Ooty Lake','Tea Estate'],
        'discount'=>5
    ],
    'Kodaikanal, Tamil Nadu' => [
        'rating'=>4.7, 'reviews'=>280, 'badge'=>'Nature Pick',
        'badge_color'=>'#28a745',
        'includes'=>['Hotel','Pillar Rocks','Coakers Walk','Lake Visit'],
        'discount'=>8
    ],
    'Mysore, Karnataka' => [
        'rating'=>4.8, 'reviews'=>330, 'badge'=>'Heritage',
        'badge_color'=>'#6f42c1',
        'includes'=>['Hotel','Mysore Palace','Chamundi Hills','City Tour'],
        'discount'=>0
    ],
    'Coorg, Karnataka' => [
        'rating'=>4.8, 'reviews'=>440, 'badge'=>'💚 Best Value',
        'badge_color'=>'#28a745',
        'includes'=>['Hotel','Coffee Plantation','Waterfalls','Nature Walk'],
        'discount'=>5
    ],
    'Tokyo' => [
        'rating'=>4.8, 'reviews'=>750, 'badge'=>'Trending',
        'badge_color'=>'#0077b6',
        'includes'=>['Hotel','Transfer','Mt Fuji Tour','Shibuya Walk'],
        'discount'=>5
    ],
    'Hampi, Karnataka' => [
        'rating'=>4.7, 'reviews'=>260, 'badge'=>'Heritage',
        'badge_color'=>'#6f42c1',
        'includes'=>['Hotel','Temple Tour','Ancient Ruins','Local Guide'],
        'discount'=>5
    ],
    'Darjeeling, West Bengal' => [
        'rating'=>4.8, 'reviews'=>390, 'badge'=>'Scenic Escape',
        'badge_color'=>'#0077b6',
        'includes'=>['Hotel','Toy Train Experience','Tea Garden','Tiger Hill'],
        'discount'=>0
    ],
    'Shillong, Meghalaya' => [
        'rating'=>4.7, 'reviews'=>320, 'badge'=>'Adventure',
        'badge_color'=>'#0077b6',
        'includes'=>['Hotel','Waterfalls','Cave Tour','Local Sightseeing'],
        'discount'=>8
    ],
    'Puri, Odisha' => [
        'rating'=>4.6, 'reviews'=>350, 'badge'=>'Cultural Pick',
        'badge_color'=>'#6f42c1',
        'includes'=>['Hotel','Jagannath Temple','Puri Beach','Konark Excursion'],
        'discount'=>5
    ],
];


// Enrich DB packages with display metadata
$all_display_packages = [];
foreach ($all_packages as $pkg) {
    $meta = $display_meta[$pkg['destination']] ?? [
        'rating' => 4.5, 'reviews' => 100, 'badge' => 'Popular',
        'badge_color' => '#0077b6', 'includes' => ['Hotel','Transfer','Tour'], 'discount' => 0
    ];
    $pkg['rating']      = $meta['rating'];
    $pkg['reviews']     = $meta['reviews'];
    $pkg['badge']       = $meta['badge'];
    $pkg['badge_color'] = $meta['badge_color'];
    $pkg['includes']    = $meta['includes'];
    $pkg['discount']    = $meta['discount'];
    $all_display_packages[] = $pkg;
}

// Fetch reviews from DB
$reviews_result = $conn->query("SELECT * FROM reviews ORDER BY id DESC LIMIT 6");
$db_reviews = [];
if ($reviews_result && $reviews_result->num_rows > 0) {
    while ($row = $reviews_result->fetch_assoc()) {
        $db_reviews[] = $row;
    }
}

// ===== FILTER & SORT (GET params) =====
$filter_sort   = $_GET['sort']   ?? 'default';
$filter_budget = $_GET['budget'] ?? 'all';
$search_query  = trim($_GET['search'] ?? '');

// Apply filters
$filtered = array_filter($all_display_packages, function ($pkg) use ($filter_budget, $search_query) {
    $pass_budget = true;
    if ($filter_budget === 'budget')  $pass_budget = $pkg['price'] <= 70000;
    if ($filter_budget === 'mid')     $pass_budget = $pkg['price'] > 70000 && $pkg['price'] <= 110000;
    if ($filter_budget === 'luxury')  $pass_budget = $pkg['price'] > 110000;
    $pass_search = true;
    if ($search_query) {
        $pass_search = stripos($pkg['destination'], $search_query) !== false
                    || stripos($pkg['description'], $search_query) !== false;
    }
    return $pass_budget && $pass_search;
});

// Apply sort
usort($filtered, function ($a, $b) use ($filter_sort) {
    if ($filter_sort === 'price_asc')   return $a['price'] <=> $b['price'];
    if ($filter_sort === 'price_desc')  return $b['price'] <=> $a['price'];
    if ($filter_sort === 'rating')      return $b['rating'] <=> $a['rating'];
    if ($filter_sort === 'reviews')     return $b['reviews'] <=> $a['reviews'];
    return 0;
});

$total_count    = count($all_display_packages);
$filtered_count = count($filtered);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Browse all TravelEase travel packages – Paris, Tokyo, Bali, Dubai, Maldives, Singapore and more.">
    <title>Travel Packages – TravelEase</title>
    <link rel="stylesheet" href="style.css">
    <style>
        /* ===== PACKAGES PAGE SPECIFIC CSS ===== */

        /* Deals Banner */
        .deals-banner {
            background: linear-gradient(90deg, #dc3545, #f77f00, #dc3545);
            background-size: 200% auto;
            animation: shimmer 3s linear infinite;
            color: white;
            text-align: center;
            padding: 11px 20px;
            font-size: 13px;
            font-weight: 700;
            letter-spacing: 0.3px;
        }
        .deals-banner a { color: white; text-decoration: underline; margin-left: 8px; }
        @keyframes shimmer {
            0%   { background-position: 0% center; }
            100% { background-position: 200% center; }
        }

        .logo-accent {
            color: #e06b00;
        }

        /* Search + Filter Bar */
        .filter-bar {
            background: white;
            box-shadow: 0 4px 20px rgba(0,0,0,0.08);
            padding: 20px 40px;
            position: sticky;
            top: 65px;
            z-index: 100;
        }
        .filter-bar-inner {
            max-width: 1300px;
            margin: 0 auto;
            display: flex;
            gap: 14px;
            align-items: center;
            flex-wrap: wrap;
        }
        .search-box {
            flex: 1;
            min-width: 220px;
            display: flex;
            align-items: center;
            border: 1.5px solid #dde;
            border-radius: 30px;
            overflow: hidden;
            background: #fafafa;
            transition: all 0.3s;
        }
        .search-box:focus-within {
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(0,119,182,0.1);
        }
        .search-box span {
            padding: 0 14px;
            color: var(--gray);
            font-size: 16px;
        }
        .search-box input {
            flex: 1;
            padding: 11px 0;
            border: none;
            outline: none;
            font-size: 14px;
            background: transparent;
            color: #333;
        }
        .filter-select {
            padding: 11px 16px;
            border: 1.5px solid #dde;
            border-radius: 25px;
            font-size: 13px;
            font-weight: 600;
            color: var(--dark);
            background: #fafafa;
            cursor: pointer;
            outline: none;
            transition: all 0.3s;
        }
        .filter-select:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(0,119,182,0.1);
        }
        .filter-count {
            font-size: 13px;
            color: var(--gray);
            white-space: nowrap;
            font-weight: 500;
            margin-left: auto;
        }
        .filter-count strong { color: var(--primary); }

        /* Package Cards Enhanced */
        .packages-section {
            max-width: 1300px;
            margin: 0 auto;
        }
        .pkg-card-wrap {
            background: white;
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 4px 20px rgba(0,0,0,0.1);
            transition: all 0.35s ease;
            position: relative;
            display: flex;
            flex-direction: column;
        }
        .pkg-card-wrap:hover {
            transform: translateY(-10px);
            box-shadow: 0 16px 45px rgba(0,0,0,0.18);
        }
        .pkg-img-wrap {
            position: relative;
            overflow: hidden;
            width: 100%;
            aspect-ratio: 2/1.3; /* Forces a consistent rectangle ratio */
            flex-shrink: 0;       /* Prevents the image container from shrinking */
            background: #f0f0f0;  /* Fallback background */
        }
        .pkg-img-wrap img {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            object-fit: cover;    /* Ensures the image fills the area without stretching */
            object-position: center;
            transition: transform 0.55s ease;
        }
        .pkg-card-wrap:hover .pkg-img-wrap img {
            transform: scale(1.1);
        }
        .pkg-badge {
            position: absolute;
            top: 14px;
            left: 14px;
            color: white;
            font-size: 11px;
            font-weight: 700;
            padding: 5px 13px;
            border-radius: 20px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            backdrop-filter: blur(4px);
        }
        .pkg-discount-tag {
            position: absolute;
            top: 14px;
            right: 14px;
            background: #dc3545;
            color: white;
            font-size: 12px;
            font-weight: 800;
            padding: 5px 11px;
            border-radius: 20px;
        }
        .pkg-body {
            padding: 20px;
            flex: 1;
            display: flex;
            flex-direction: column;
        }
        .pkg-rating-row {
            display: flex;
            align-items: center;
            gap: 8px;
            margin-bottom: 8px;
        }
        .pkg-stars { color: #f7b731; font-size: 13px; }
        .pkg-rating-num {
            font-size: 13px;
            font-weight: 700;
            color: var(--dark);
        }
        .pkg-reviews {
            font-size: 12px;
            color: var(--gray);
        }
        .pkg-body h3 {
            font-size: 19px;
            color: var(--dark);
            font-weight: 700;
            margin-bottom: 8px;
        }
        .pkg-body p {
            font-size: 13px;
            color: var(--gray);
            line-height: 1.65;
            margin-bottom: 14px;
            flex: 1;
        }
        .pkg-includes {
            display: flex;
            gap: 6px;
            flex-wrap: wrap;
            margin-bottom: 15px;
        }
        .pkg-tag {
            background: var(--light);
            color: var(--primary);
            font-size: 11px;
            padding: 3px 10px;
            border-radius: 12px;
            font-weight: 600;
            border: 1px solid rgba(0,119,182,0.15);
        }
        .pkg-price-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 15px;
            flex-wrap: wrap;
            gap: 8px;
        }
        .pkg-price-original {
            font-size: 12px;
            color: var(--gray);
            text-decoration: line-through;
        }
        .pkg-price {
            font-size: 22px;
            font-weight: 800;
            color: var(--accent);
            line-height: 1;
        }
        .pkg-price-per {
            font-size: 11px;
            color: var(--gray);
        }
        .pkg-duration {
            background: var(--light-gray);
            padding: 5px 13px;
            border-radius: 20px;
            font-size: 12px;
            color: var(--gray);
            font-weight: 600;
        }
        .pkg-btn-row {
            display: flex;
            gap: 10px;
        }
        .pkg-btn-row .btn {
            flex: 1;
            text-align: center;
            font-size: 13px;
            padding: 11px 14px;
        }
        .btn-wish {
            background: white;
            border: 2px solid var(--primary);
            color: var(--primary);
            border-radius: 30px;
            padding: 10px 15px;
            cursor: pointer;
            font-size: 16px;
            transition: all 0.3s;
        }
        .btn-wish:hover {
            background: var(--primary);
            color: white;
        }

        /* No Results */
        .no-results {
            text-align: center;
            padding: 60px 20px;
            color: var(--gray);
        }
        .no-results h3 { font-size: 22px; margin-bottom: 10px; color: var(--dark); }
        .no-results p  { font-size: 15px; margin-bottom: 22px; }

        /* Why Book Section */
        .why-book-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 22px;
            max-width: 800px;
            margin: 0 auto;
        }
        .why-book-card {
            background: white;
            border-radius: 12px;
            padding: 26px 20px;
            text-align: center;
            box-shadow: 0 3px 14px rgba(0,0,0,0.07);
            transition: all 0.3s;
        }
        .why-book-card:hover { transform: translateY(-5px); box-shadow: var(--shadow-lg); }
        .why-book-card .icon { font-size: 38px; margin-bottom: 12px; }
        .why-book-card h4 { font-size: 15px; color: var(--dark); font-weight: 700; margin-bottom: 7px; }
        .why-book-card p { font-size: 13px; color: var(--gray); line-height: 1.6; }

        /* Reviews */
        .testimonials-section { background: var(--light); }
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
        .testimonial-card:hover { transform: translateY(-5px); }
        .testimonial-card::before {
            content: '"';
            font-size: 80px;
            color: var(--primary);
            opacity: 0.12;
            position: absolute;
            top: 10px; left: 20px;
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
        .author-info h4 { font-size: 15px; color: var(--dark); margin-bottom: 2px; }
        .author-info span { font-size: 12px; color: var(--gray); }
        .star-rating { color: #f7b731; font-size: 13px; margin-bottom: 12px; }
        
        /* Responsive */
        @media (max-width: 768px) {
            .filter-bar { padding: 14px 16px; top: 60px; }
            .filter-bar-inner { gap: 10px; }
            .search-box { min-width: 100%; }
            .mini-testimonials { padding: 45px 20px; }
        }
        @media (max-width: 480px) {
            .pkg-btn-row { flex-direction: column; }
        }
    </style>
</head>
<body>

<!-- ===== DEALS BANNER ===== -->
<div class="deals-banner">
    🔥 Limited Time: Up to 12% OFF on select packages!
    <a href="#packages-grid">View Deals →</a>
</div>

<!-- ===== NAVBAR ===== -->
<nav class="navbar">
    <div class="logo"><a href="index.php">✈Travel<span class="logo-accent">Ease</span></a></div>
    <ul class="nav-links">
        <li><a href="index.php">Home</a></li>
        <li><a href="packages.php" class="active">Packages</a></li>
        <?php if (isLoggedIn()): ?>
            <li><a href="my_bookings.php">My Bookings</a></li>
            <li><a href="logout.php" class="btn-nav">Logout</a></li>
        <?php else: ?>
            <li><a href="login.php">Login</a></li>
            <li><a href="register.php" class="btn-nav">Register</a></li>
        <?php endif; ?>
    </ul>
</nav>

<!-- ===== PAGE HEADER ===== -->
<div class="page-header">
    <h1>✈ Travel Packages</h1>
    <p>Explore <?= $total_count ?>+ handcrafted packages to the world's most beautiful destinations</p>
</div>

<!-- ===== FILTER BAR ===== -->
<div class="filter-bar">
    <form method="GET" id="filter-form">
        <div class="filter-bar-inner">
            <!-- Search -->
            <div class="search-box">
                <span>🔍</span>
                <input
                    type="text"
                    name="search"
                    placeholder="Search destinations..."
                    value="<?= htmlspecialchars($search_query) ?>"
                    id="search-input"
                    autocomplete="off"
                >
            </div>

            <!-- Budget -->
            <select name="budget" class="filter-select" onchange="this.form.submit()">
                <option value="all"    <?= $filter_budget==='all'    ?'selected':'' ?>>💰 All Budgets</option>
                <option value="budget" <?= $filter_budget==='budget' ?'selected':'' ?>>🟢 Under ₹70,000</option>
                <option value="mid"    <?= $filter_budget==='mid'    ?'selected':'' ?>>🟡 ₹70K – ₹1.1L</option>
                <option value="luxury" <?= $filter_budget==='luxury' ?'selected':'' ?>>🔴 Above ₹1.1L</option>
            </select>

            <!-- Sort -->
            <select name="sort" class="filter-select" onchange="this.form.submit()">
                <option value="default"    <?= $filter_sort==='default'    ?'selected':'' ?>>📌 Default</option>
                <option value="price_asc"  <?= $filter_sort==='price_asc'  ?'selected':'' ?>>⬆ Price: Low to High</option>
                <option value="price_desc" <?= $filter_sort==='price_desc' ?'selected':'' ?>>⬇ Price: High to Low</option>
                <option value="rating"     <?= $filter_sort==='rating'     ?'selected':'' ?>>⭐ Top Rated</option>
                <option value="reviews"    <?= $filter_sort==='reviews'    ?'selected':'' ?>>💬 Most Reviewed</option>
            </select>

            <!-- Search Button -->
            <button type="submit" class="btn btn-primary btn-sm">Search</button>

            <?php if ($search_query || $filter_budget !== 'all' || $filter_sort !== 'default'): ?>
                <a href="packages.php" class="btn btn-danger btn-sm">✕ Clear</a>
            <?php endif; ?>

            <span class="filter-count">
                Showing <strong><?= $filtered_count ?></strong> of <strong><?= $total_count ?></strong> packages
            </span>
        </div>
    </form>
</div>

<!-- ===== MAIN PACKAGES SECTION ===== -->
<section class="section" id="packages-grid" style="background: var(--light-gray);">
    <div class="packages-section">

        <?php if (empty($filtered)): ?>
        <div class="no-results">
            <div style="font-size:60px; margin-bottom:16px;">🔍</div>
            <h3>No packages found</h3>
            <p>Try adjusting your search or filters to find your perfect destination.</p>
            <a href="packages.php" class="btn btn-primary">Clear Filters</a>
        </div>
        <?php else: ?>
        <div class="packages-grid" id="packages-grid-items">
            <?php foreach ($filtered as $pkg):
                $discounted_price = $pkg['discount'] > 0
                    ? $pkg['price'] * (1 - $pkg['discount'] / 100)
                    : $pkg['price'];
                $original_price = $pkg['price'];
                $stars = str_repeat('★', floor($pkg['rating'])) . ($pkg['rating'] - floor($pkg['rating']) >= 0.5 ? '½' : '');
                $img_src = packageImageUrl($pkg['destination'], $pkg['image'] ?? '');
                $book_url = "booking.php?id={$pkg['id']}";
            ?>
            <div class="pkg-card-wrap" data-id="<?= $pkg['id'] ?>" data-name="<?= htmlspecialchars($pkg['destination']) ?>">

                <!-- Image -->
                <div class="pkg-img-wrap">
                    <img src="<?= $img_src ?>" alt="<?= htmlspecialchars($pkg['destination']) ?>" loading="lazy">
                    <!-- Badge -->
                    <span class="pkg-badge" style="background:<?= $pkg['badge_color'] ?>;">
                        <?= htmlspecialchars($pkg['badge']) ?>
                    </span>
                    <!-- Discount Tag -->
                    <?php if ($pkg['discount'] > 0): ?>
                        <span class="pkg-discount-tag">-<?= $pkg['discount'] ?>% OFF</span>
                    <?php endif; ?>
                </div>

                <!-- Body -->
                <div class="pkg-body">
                    <!-- Rating -->
                    <div class="pkg-rating-row">
                        <span class="pkg-stars"><?= $stars ?></span>
                        <span class="pkg-rating-num"><?= $pkg['rating'] ?></span>
                        <span class="pkg-reviews">(<?= number_format($pkg['reviews']) ?> reviews)</span>
                    </div>

                    <h3>📍 <?= htmlspecialchars($pkg['destination']) ?></h3>
                    <p><?= htmlspecialchars(substr($pkg['description'], 0, 110)) ?>...</p>

                    <!-- Includes -->
                    <div class="pkg-includes">
                        <?php foreach ($pkg['includes'] as $inc): ?>
                            <span class="pkg-tag">✔ <?= htmlspecialchars($inc) ?></span>
                        <?php endforeach; ?>
                    </div>

                    <!-- Price Row (INR only) -->
                    <div class="pkg-price-row">
                        <div class="pkg-price-block">
                            <?php if ($pkg['discount'] > 0): ?>
                                <div class="pkg-price-original">&#8377;<?= number_format($original_price, 0) ?></div>
                            <?php endif; ?>
                            <div class="pkg-price">&#8377;<?= number_format($discounted_price, 0) ?></div>
                            <div class="pkg-price-per">per person</div>
                        </div>
                        <span class="pkg-duration">⏱ <?= htmlspecialchars($pkg['duration']) ?></span>
                    </div>

                    <!-- Buttons -->
                    <div class="pkg-btn-row">
                        <?php if (isLoggedIn()): ?>
                            <a href="<?= $book_url ?>" class="btn btn-primary">Book Now</a>
                        <?php else: ?>
                            <a href="login.php" class="btn btn-secondary">Login to Book</a>
                        <?php endif; ?>
                        <button class="btn-wish" onclick="toggleWishlist(this, <?= $pkg['id'] ?>)" title="Add to Wishlist">♡</button>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>

    </div>
</section>

<!-- ===== WHY BOOK WITH US ===== -->
<section class="section" style="background: white;">
    <div class="section-title">
        <h2>Why Book with <span>TravelEase</span></h2>
        <div class="divider"></div>
        <p>Everything you need for a stress-free booking experience</p>
    </div>
    <div class="why-book-grid">
        <div class="why-book-card">
            <div class="icon">🔒</div>
            <h4>100% Secure Payments</h4>
            <p>All transactions are encrypted and protected with multi-layer security systems.</p>
        </div>
        <div class="why-book-card">
            <div class="icon">✅</div>
            <h4>Instant Confirmation</h4>
            <p>Receive your booking confirmation and unique transaction ID immediately after payment.</p>
        </div>
        <div class="why-book-card">
            <div class="icon">💰</div>
            <h4>Best Price Guarantee</h4>
            <p>We promise the best rates. Found cheaper? We'll match it no questions asked.</p>
        </div>
        
        <div class="why-book-card">
            <div class="icon">🔄</div>
            <h4>Free Cancellation</h4>
            <p>Plans changed? Cancel or reschedule your booking up to 48 hours before departure.</p>
        </div>
        <div class="why-book-card">
            <div class="icon">🛡️</div>
            <h4>Trusted & Secure</h4>
            <p>Your data and payments are protected with industry-standard encryption.</p>
        </div>
        <div class="why-book-card">
            <div class="icon">🎧</div>
            <h4>Expert Team</h4>
            <p>Our travel experts are available Mon–Sat, 9AM–6PM IST to answer all your questions.</p>
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

<!-- ===== CTA SECTION ===== -->
<section class="section" style="background: linear-gradient(135deg, var(--dark), var(--primary)); text-align:center; color:white;">
    <h2 style="font-size:32px; font-weight:800; margin-bottom:14px;">Ready for Your Next Adventure?</h2>
    <p style="opacity:0.88; margin-bottom:32px; font-size:16px; max-width:520px; margin-left:auto; margin-right:auto;">
        Browse our curated packages and book your dream trip today. Your perfect journey is just a click away.
    </p>
    <div style="display:flex; gap:15px; justify-content:center; flex-wrap:wrap;">
        <?php if (!isLoggedIn()): ?>
            <a href="register.php" class="btn btn-primary">📝 Create Account</a>
        <?php else: ?>
            <a href="my_bookings.php" class="btn btn-primary">📋 My Bookings</a>
        <?php endif; ?>
    </div>
</section>

<!-- ===== FOOTER ===== -->
<footer>
    <p>© 2025 <span>TravelEase</span> – All rights reserved. Built with ❤️ for travelers.</p>
</footer>

<!-- ===== SCRIPTS ===== -->
<script src="script.js"></script>
<script>
// ===== WISHLIST TOGGLE =====
const wishlist = JSON.parse(localStorage.getItem('intele_wishlist') || '[]');

function toggleWishlist(btn, id) {
    const idx = wishlist.indexOf(id);
    if (idx === -1) {
        wishlist.push(id);
        btn.textContent = '♥';
        btn.style.color = '#dc3545';
        btn.style.borderColor = '#dc3545';
        showToast('Added to wishlist ♥');
    } else {
        wishlist.splice(idx, 1);
        btn.textContent = '♡';
        btn.style.color = '';
        btn.style.borderColor = '';
        showToast('Removed from wishlist');
    }
    localStorage.setItem('intele_wishlist', JSON.stringify(wishlist));
}

// Restore wishlist state on load
document.querySelectorAll('.btn-wish').forEach(btn => {
    const id = parseInt(btn.closest('.pkg-card-wrap').getAttribute('data-id'));
    if (wishlist.includes(id)) {
        btn.textContent = '♥';
        btn.style.color = '#dc3545';
        btn.style.borderColor = '#dc3545';
    }
});

// ===== TOAST NOTIFICATION =====
function showToast(message) {
    const existing = document.getElementById('toast-msg');
    if (existing) existing.remove();
    const toast = document.createElement('div');
    toast.id = 'toast-msg';
    toast.textContent = message;
    toast.style.cssText = `
        position: fixed; bottom: 80px; left: 50%; transform: translateX(-50%);
        background: var(--dark); color: white; padding: 12px 24px;
        border-radius: 30px; font-size: 14px; font-weight: 600;
        box-shadow: 0 6px 22px rgba(0,0,0,0.25); z-index: 99999;
        animation: fadeIn 0.3s ease;
    `;
    document.body.appendChild(toast);
    setTimeout(() => {
        toast.style.opacity = '0';
        toast.style.transition = 'opacity 0.4s';
        setTimeout(() => toast.remove(), 400);
    }, 2500);
}

// ===== LIVE SEARCH (client-side filter as you type) =====
const searchInput = document.getElementById('search-input');
if (searchInput && <?= json_encode(empty($search_query)) ?>) {
    searchInput.addEventListener('input', function () {
        const q = this.value.toLowerCase();
        document.querySelectorAll('.pkg-card-wrap').forEach(card => {
            const name = card.getAttribute('data-name').toLowerCase();
            card.style.display = name.includes(q) ? 'flex' : 'none';
        });
    });
}

// ===== SCROLL REVEAL FOR CARDS =====
const pkgCards = document.querySelectorAll('.pkg-card-wrap');
const revealObserver = new IntersectionObserver(entries => {
    entries.forEach(entry => {
        if (entry.isIntersecting) {
            entry.target.style.opacity = '1';
            entry.target.style.transform = 'translateY(0)';
        }
    });
}, { threshold: 0.08 });

pkgCards.forEach((card, i) => {
    card.style.opacity = '0';
    card.style.transform = 'translateY(30px)';
    card.style.transition = `opacity 0.5s ease ${i * 0.07}s, transform 0.5s ease ${i * 0.07}s`;
    revealObserver.observe(card);
});

// ===== AUTO SUBMIT SEARCH ON ENTER =====
if (searchInput) {
    searchInput.addEventListener('keydown', function (e) {
        if (e.key === 'Enter') {
            document.getElementById('filter-form').submit();
        }
    });
}
</script>
</body>
</html>