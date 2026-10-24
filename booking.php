<?php
session_start();
require_once 'database.php';

if (!isLoggedIn()) redirect('login.php?msg=Please login to book a package.');

$package_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($package_id <= 0) {
    $firstPkgResult = $conn->query("SELECT id FROM packages WHERE available = 1 ORDER BY id ASC LIMIT 1");
    $firstPkg = $firstPkgResult ? $firstPkgResult->fetch_assoc() : null;
    if ($firstPkg && isset($firstPkg['id'])) {
        redirect('booking.php?id=' . (int)$firstPkg['id']);
    }
    redirect('packages.php');
}

$stmt = $conn->prepare("SELECT * FROM packages WHERE id = ? AND available = 1");
$stmt->bind_param("i", $package_id);
$stmt->execute();
$package = $stmt->get_result()->fetch_assoc();

if (!$package) redirect('packages.php');

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // $service_type = trim($_POST['service_type'] ?? 'flight');
    $service_type = trim($_POST['service_type'] ?? 'tour_package');
    $valid_services = ['flight', 'train', 'hotel', 'cab'];
    if (!in_array($service_type, $valid_services, true)) {
        $service_type = 'flight';
    }

    $passenger_name  = trim($_POST['passenger_name'] ?? '');
    $passenger_email = trim($_POST['passenger_email'] ?? '');
    $user_id         = (int)$_SESSION['user_id'];
    $booking_date    = '';
    $check_out_date  = null;
    $travelers       = 0;

    if ($passenger_name === '') {
        $error = 'Please enter passenger name.';
    } elseif (!filter_var($passenger_email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Please enter a valid email address.';
    }

    if ($error === '') {
        if ($service_type === 'flight') {
            $from_city       = trim($_POST['flight_from_city'] ?? '');
            $to_city         = trim($_POST['flight_to_city'] ?? '');
            $travel_class    = trim($_POST['flight_travel_class'] ?? '');
            $booking_date    = clean($conn, $_POST['flight_departure_on'] ?? '');
            $adults          = max(1, (int)($_POST['flight_adults'] ?? 1));
            $children        = max(0, (int)($_POST['flight_children'] ?? 0));
            $travelers       = $adults + $children;

            if ($from_city === '' || $to_city === '' || $travel_class === '') {
                $error = 'Please complete all required flight fields.';
            }
        } elseif ($service_type === 'train') {
            $from_station    = trim($_POST['train_from_station'] ?? '');
            $to_station      = trim($_POST['train_to_station'] ?? '');
            $train_class     = trim($_POST['train_class'] ?? '');
            $booking_date    = clean($conn, $_POST['train_journey_date'] ?? '');
            $adults          = max(1, (int)($_POST['train_adults'] ?? 1));
            $children        = max(0, (int)($_POST['train_children'] ?? 0));
            $travelers       = $adults + $children;

            if ($from_station === '' || $to_station === '' || $train_class === '') {
                $error = 'Please complete all required train fields.';
            }
        } elseif ($service_type === 'hotel') {
            $city            = trim($_POST['hotel_city'] ?? '');
            $booking_date    = clean($conn, $_POST['hotel_check_in'] ?? '');
            $check_out_date  = clean($conn, $_POST['hotel_check_out'] ?? '');
            $room_type       = trim($_POST['hotel_room_type'] ?? '');
            $adults          = max(1, (int)($_POST['hotel_adults'] ?? 1));
            $children        = max(0, (int)($_POST['hotel_children'] ?? 0));
            $travelers       = $adults + $children;

            if ($city === '' || $room_type === '' || $check_out_date === '') {
                $error = 'Please complete all required hotel fields (including check-out).';
            } elseif (strtotime($check_out_date) <= strtotime($booking_date)) { // FIXED here too
                $error = 'Check-out date must be after check-in date.';
            }
        } else {
            $pickup_location = trim($_POST['cab_pickup_location'] ?? '');
            $drop_location   = trim($_POST['cab_drop_location'] ?? '');
            $booking_date    = clean($conn, $_POST['cab_pickup_date'] ?? '');
            $cab_type        = trim($_POST['cab_type'] ?? '');
            $travelers       = max(1, (int)($_POST['cab_passengers'] ?? 1));

            if ($pickup_location === '' || $drop_location === '' || $cab_type === '') {
                $error = 'Please complete all required cab fields.';
            }
        }
    }

    if ($error === '') {
        if (empty($booking_date)) {
            $error = 'Please select a valid date.';
        } elseif (strtotime($booking_date) < strtotime('today')) {
            $error = 'Date must be today or in the future.';
        } elseif ($travelers < 1 || $travelers > 20) {
            $error = 'Travelers must be between 1 and 20.';
        }
    }

    if ($error === '') {
        $total_price = (float)$package['price'] * $travelers;
        $ins = $conn->prepare("INSERT INTO bookings (user_id, package_id, service_type, booking_date, check_out_date, travelers, total_price, status) VALUES (?, ?, ?, ?, ?, ?, ?, 'pending')");
        $ins->bind_param("iisssid", $user_id, $package_id, $service_type, $booking_date, $check_out_date, $travelers, $total_price);

        if ($ins->execute()) {
            $booking_id = $conn->insert_id;
            redirect("payment.php?booking_id=$booking_id");
        } else {
            $error = 'Booking failed. Please try again.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Book Your Trip – TravelEase</title>
    <link rel="stylesheet" href="style.css">
    <style>
        :root {
            --teal: #0d9488;
            --teal-hover: #0f766e;
        }
        
        .booking-wrapper {
            max-width: 1200px;
            margin: 40px auto;
            padding: 0 20px;
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 30px;
            align-items: start;
        }

        /* Left Side - Form */
        .booking-form-container {
            background: white;
            border-radius: 16px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.05);
            padding: 30px;
        }

        .booking-header {
            margin-bottom: 25px;
            padding-bottom: 15px;
            border-bottom: 1px solid #eee;
        }
        .booking-header h2 { font-size: 24px; color: var(--dark); margin-bottom: 5px; }
        .booking-header p { color: var(--gray); font-size: 14px; }

        .service-tabs {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 10px;
            margin-bottom: 30px;
            background: #f8fafc;
            padding: 8px;
            border-radius: 12px;
        }
        .service-tab {
            border: none;
            background: transparent;
            padding: 12px 10px;
            border-radius: 8px;
            font-weight: 600;
            font-size: 14px;
            color: var(--gray);
            cursor: pointer;
            transition: all 0.3s;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 6px;
        }
        .service-tab .icon { font-size: 20px; }
        .service-tab:hover { background: #e2e8f0; color: var(--dark); }
        .service-tab.active {
            background: white;
            color: var(--teal);
            box-shadow: 0 2px 10px rgba(0,0,0,0.08);
        }

        .service-panel { display: none; animation: fadeIn 0.4s ease; }
        .service-panel.active { display: block; }

        .form-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
        }
        .form-grid .span-2 { grid-column: 1 / -1; }
        
        .form-group label {
            display: block;
            font-size: 13px;
            font-weight: 600;
            color: var(--dark);
            margin-bottom: 6px;
        }
        .form-group input, .form-group select {
            width: 100%;
            padding: 12px 16px;
            border: 1.5px solid #cbd5e1;
            border-radius: 8px;
            font-size: 14px;
            color: var(--dark);
            background: white;
            transition: border-color 0.3s;
        }
        .form-group input:focus, .form-group select:focus {
            outline: none;
            border-color: var(--teal);
            box-shadow: 0 0 0 3px rgba(13, 148, 136, 0.1);
        }

        /* Right Side - Summary */
        .booking-summary {
            background: white;
            border-radius: 16px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.05);
            padding: 30px;
            position: sticky;
            top: 90px;
        }
        .summary-img {
            width: 100%;
            height: 160px;
            object-fit: cover;
            border-radius: 12px;
            margin-bottom: 20px;
        }
        .summary-title { font-size: 20px; font-weight: 700; color: var(--dark); margin-bottom: 10px; }
        .summary-meta { 
            font-size: 13px; 
            color: var(--gray); 
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .price-breakdown {
            background: #f8fafc;
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 20px;
        }
        .price-row {
            display: flex;
            justify-content: space-between;
            font-size: 14px;
            color: var(--gray);
            margin-bottom: 12px;
        }
        .price-row.total {
            border-top: 1px dashed #cbd5e1;
            padding-top: 15px;
            margin-bottom: 0;
            font-size: 18px;
            font-weight: 800;
            color: var(--dark);
        }
        .price-row.total span:last-child { color: var(--teal); }

        .btn-teal {
            background: var(--teal);
            color: white;
            width: 100%;
            padding: 16px;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 700;
            border: none;
            cursor: pointer;
            transition: all 0.3s;
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 10px;
        }
        .btn-teal:hover { background: var(--teal-hover); transform: translateY(-2px); }
        
        .alert-danger {
            background: #fef2f2;
            color: #dc2626;
            border: 1px solid #f87171;
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 25px;
            font-size: 14px;
            font-weight: 500;
        }

        @media (max-width: 900px) {
            .booking-wrapper { grid-template-columns: 1fr; }
            .booking-summary { position: relative; top: 0; }
        }
        @media (max-width: 600px) {
            .form-grid { grid-template-columns: 1fr; }
            .form-grid .span-2 { grid-column: auto; }
            .service-tabs { grid-template-columns: 1fr 1fr; }
        }
    </style>
</head>
<body style="background: #f1f5f9;">

<nav class="navbar" style="background: white; box-shadow: 0 2px 10px rgba(0,0,0,0.05);">
    <div class="logo"><a href="index.php">✈Travel<span class="logo-accent">Ease</span></a></div>
    <ul class="nav-links">
        <li><a href="index.php">Home</a></li>
        <li><a href="packages.php">Packages</a></li>
        <li><a href="my_bookings.php">My Bookings</a></li>
        <li><a href="logout.php" class="btn-nav">Logout</a></li>
    </ul>
</nav>

<div class="booking-wrapper">
    <!-- Form Section -->
    <div class="booking-form-container">
        <div class="booking-header">
            <h2>Complete Your Booking</h2>
            <p>Please fill in the details below to secure your trip.</p>
        </div>

        <?php if ($error): ?>
            <div class="alert-danger">⚠️ <?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <?php $serviceType = $_POST['service_type'] ?? 'flight'; ?>
        
        <form method="POST" id="booking_form">
            <input type="hidden" name="service_type" id="service_type" value="<?= htmlspecialchars($serviceType) ?>">
            <input type="hidden" id="package_price" value="<?= htmlspecialchars((string)$package['price']) ?>">

            <!-- Tabs -->
            <div class="service-tabs">
                <button type="button" class="service-tab" data-service="flight">
                    <span class="icon">✈️</span> Flight
                </button>
                <button type="button" class="service-tab" data-service="train">
                    <span class="icon">🚆</span> Train
                </button>
                <button type="button" class="service-tab" data-service="hotel">
                    <span class="icon">🏨</span> Hotel
                </button>
                <button type="button" class="service-tab" data-service="cab">
                    <span class="icon">🚕</span> Cab
                </button>
            </div>

            <!-- Common Fields -->
            <div class="form-grid" style="margin-bottom: 25px;">
                <div class="form-group span-2">
                    <label>Full Name *</label>
                    <input type="text" name="passenger_name" placeholder="e.g. John Doe" value="<?= htmlspecialchars($_POST['passenger_name'] ?? '') ?>" required>
                </div>
                <div class="form-group span-2">
                    <label>Email Address *</label>
                    <input type="email" name="passenger_email" placeholder="e.g. john@example.com" value="<?= htmlspecialchars($_POST['passenger_email'] ?? '') ?>" required>
                </div>
            </div>

            <!-- Flight Panel -->
            <div class="service-panel" id="panel-flight">
                <div class="form-grid">
                    <div class="form-group">
                        <label>From City *</label>
                        <?php $flightFrom = $_POST['flight_from_city'] ?? 'India'; ?>
                        <select name="flight_from_city">
                            <option value="India" <?= $flightFrom === 'India' ? 'selected' : '' ?>>India</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>To City *</label>
                        <?php $flightTo = $_POST['flight_to_city'] ?? $package['destination']; ?>
                        <select name="flight_to_city">
                            <option value="<?= htmlspecialchars($package['destination']) ?>" <?= $flightTo === $package['destination'] ? 'selected' : '' ?>><?= htmlspecialchars($package['destination']) ?></option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Adults (12+ yrs) *</label>
                        <?php $flightAdults = (int)($_POST['flight_adults'] ?? 1); ?>
                        <select name="flight_adults" id="flight_adults">
                            <?php for ($i = 1; $i <= 10; $i++): ?>
                                <option value="<?= $i ?>" <?= $flightAdults === $i ? 'selected' : '' ?>><?= str_pad((string)$i, 2, '0', STR_PAD_LEFT) ?></option>
                            <?php endfor; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Children (2-11 yrs) *</label>
                        <?php $flightChildren = (int)($_POST['flight_children'] ?? 0); ?>
                        <select name="flight_children" id="flight_children">
                            <?php for ($i = 0; $i <= 10; $i++): ?>
                                <option value="<?= $i ?>" <?= $flightChildren === $i ? 'selected' : '' ?>><?= str_pad((string)$i, 2, '0', STR_PAD_LEFT) ?></option>
                            <?php endfor; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Travel Class *</label>
                        <?php $flightClass = $_POST['flight_travel_class'] ?? 'Economy Class'; ?>
                        <select name="flight_travel_class">
                            <option value="Economy Class" <?= $flightClass === 'Economy Class' ? 'selected' : '' ?>>Economy Class</option>
                            <option value="Premium Economy" <?= $flightClass === 'Premium Economy' ? 'selected' : '' ?>>Premium Economy</option>
                            <option value="Business Class" <?= $flightClass === 'Business Class' ? 'selected' : '' ?>>Business Class</option>
                            <option value="First Class" <?= $flightClass === 'First Class' ? 'selected' : '' ?>>First Class</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Departure Date *</label>
                        <input type="date" name="flight_departure_on" min="<?= date('Y-m-d') ?>" value="<?= htmlspecialchars($_POST['flight_departure_on'] ?? '') ?>">
                    </div>
                </div>
            </div>

            <!-- Train Panel -->
            <div class="service-panel" id="panel-train">
                <div class="form-grid">
                    <div class="form-group">
                        <label>From Station *</label>
                        <input type="text" name="train_from_station" value="<?= htmlspecialchars($_POST['train_from_station'] ?? '') ?>" placeholder="e.g. New Delhi (NDLS)">
                    </div>
                    <div class="form-group">
                        <label>To Station *</label>
                        <input type="text" name="train_to_station" value="<?= htmlspecialchars($_POST['train_to_station'] ?? '') ?>" placeholder="e.g. Mumbai Central (MMCT)">
                    </div>
                    <div class="form-group">
                        <label>Adults *</label>
                        <?php $trainAdults = (int)($_POST['train_adults'] ?? 1); ?>
                        <select name="train_adults" id="train_adults">
                            <?php for ($i = 1; $i <= 10; $i++): ?>
                                <option value="<?= $i ?>" <?= $trainAdults === $i ? 'selected' : '' ?>><?= str_pad((string)$i, 2, '0', STR_PAD_LEFT) ?></option>
                            <?php endfor; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Children *</label>
                        <?php $trainChildren = (int)($_POST['train_children'] ?? 0); ?>
                        <select name="train_children" id="train_children">
                            <?php for ($i = 0; $i <= 10; $i++): ?>
                                <option value="<?= $i ?>" <?= $trainChildren === $i ? 'selected' : '' ?>><?= str_pad((string)$i, 2, '0', STR_PAD_LEFT) ?></option>
                            <?php endfor; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Class *</label>
                        <?php $trainClass = $_POST['train_class'] ?? 'Sleeper'; ?>
                        <select name="train_class">
                            <option value="Sleeper" <?= $trainClass === 'Sleeper' ? 'selected' : '' ?>>Sleeper</option>
                            <option value="3A" <?= $trainClass === '3A' ? 'selected' : '' ?>>AC 3 Tier (3A)</option>
                            <option value="2A" <?= $trainClass === '2A' ? 'selected' : '' ?>>AC 2 Tier (2A)</option>
                            <option value="1A" <?= $trainClass === '1A' ? 'selected' : '' ?>>First AC (1A)</option>
                            <option value="CC" <?= $trainClass === 'CC' ? 'selected' : '' ?>>Chair Car (CC)</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Journey Date *</label>
                        <input type="date" name="train_journey_date" min="<?= date('Y-m-d') ?>" value="<?= htmlspecialchars($_POST['train_journey_date'] ?? '') ?>">
                    </div>
                </div>
            </div>

            <!-- Hotel Panel -->
            <div class="service-panel" id="panel-hotel">
                <div class="form-grid">
                    <div class="form-group span-2">
                        <label>City / Hotel Area *</label>
                        <input type="text" name="hotel_city" value="<?= htmlspecialchars($_POST['hotel_city'] ?? $package['destination']) ?>" placeholder="e.g. Connaught Place, New Delhi">
                    </div>
                    <div class="form-group">
                        <label>Check-In Date *</label>
                        <input type="date" name="hotel_check_in" min="<?= date('Y-m-d') ?>" value="<?= htmlspecialchars($_POST['hotel_check_in'] ?? '') ?>">
                    </div>
                    <div class="form-group">
                        <label>Check-Out Date *</label>
                        <input type="date" name="hotel_check_out" min="<?= date('Y-m-d') ?>" value="<?= htmlspecialchars($_POST['hotel_check_out'] ?? '') ?>">
                    </div>
                    <div class="form-group">
                        <label>Adults *</label>
                        <?php $hotelAdults = (int)($_POST['hotel_adults'] ?? 2); ?>
                        <select name="hotel_adults" id="hotel_adults">
                            <?php for ($i = 1; $i <= 10; $i++): ?>
                                <option value="<?= $i ?>" <?= $hotelAdults === $i ? 'selected' : '' ?>><?= str_pad((string)$i, 2, '0', STR_PAD_LEFT) ?></option>
                            <?php endfor; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Children *</label>
                        <?php $hotelChildren = (int)($_POST['hotel_children'] ?? 0); ?>
                        <select name="hotel_children" id="hotel_children">
                            <?php for ($i = 0; $i <= 10; $i++): ?>
                                <option value="<?= $i ?>" <?= $hotelChildren === $i ? 'selected' : '' ?>><?= str_pad((string)$i, 2, '0', STR_PAD_LEFT) ?></option>
                            <?php endfor; ?>
                        </select>
                    </div>
                    <div class="form-group span-2">
                        <label>Room Type *</label>
                        <?php $hotelRoom = $_POST['hotel_room_type'] ?? 'Deluxe'; ?>
                        <select name="hotel_room_type">
                            <option value="Standard" <?= $hotelRoom === 'Standard' ? 'selected' : '' ?>>Standard</option>
                            <option value="Deluxe" <?= $hotelRoom === 'Deluxe' ? 'selected' : '' ?>>Deluxe</option>
                            <option value="Suite" <?= $hotelRoom === 'Suite' ? 'selected' : '' ?>>Suite</option>
                            <option value="Family Room" <?= $hotelRoom === 'Family Room' ? 'selected' : '' ?>>Family Room</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Cab Panel -->
            <div class="service-panel" id="panel-cab">
                <div class="form-grid">
                    <div class="form-group">
                        <label>Pickup Location *</label>
                        <input type="text" name="cab_pickup_location" value="<?= htmlspecialchars($_POST['cab_pickup_location'] ?? '') ?>" placeholder="e.g. Airport Terminal 3">
                    </div>
                    <div class="form-group">
                        <label>Drop Location *</label>
                        <input type="text" name="cab_drop_location" value="<?= htmlspecialchars($_POST['cab_drop_location'] ?? '') ?>" placeholder="e.g. Downtown Hotel">
                    </div>
                    <div class="form-group">
                        <label>Pickup Date *</label>
                        <input type="date" name="cab_pickup_date" min="<?= date('Y-m-d') ?>" value="<?= htmlspecialchars($_POST['cab_pickup_date'] ?? '') ?>">
                    </div>
                    <div class="form-group">
                        <label>Pickup Time</label>
                        <input type="time" name="cab_pickup_time" value="<?= htmlspecialchars($_POST['cab_pickup_time'] ?? '') ?>">
                    </div>
                    <div class="form-group">
                        <label>Cab Type *</label>
                        <?php $cabType = $_POST['cab_type'] ?? 'Sedan'; ?>
                        <select name="cab_type">
                            <option value="Hatchback" <?= $cabType === 'Hatchback' ? 'selected' : '' ?>>Hatchback</option>
                            <option value="Sedan" <?= $cabType === 'Sedan' ? 'selected' : '' ?>>Sedan</option>
                            <option value="SUV" <?= $cabType === 'SUV' ? 'selected' : '' ?>>SUV</option>
                            <option value="Premium" <?= $cabType === 'Premium' ? 'selected' : '' ?>>Premium</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Passengers *</label>
                        <?php $cabPassengers = (int)($_POST['cab_passengers'] ?? 1); ?>
                        <select name="cab_passengers" id="cab_passengers">
                            <?php for ($i = 1; $i <= 10; $i++): ?>
                                <option value="<?= $i ?>" <?= $cabPassengers === $i ? 'selected' : '' ?>><?= str_pad((string)$i, 2, '0', STR_PAD_LEFT) ?></option>
                            <?php endfor; ?>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Submit Button (Hidden on Desktop, shown on Mobile) -->
            <button type="submit" class="btn-teal" id="submit_btn_mobile" style="display: none; margin-top: 20px;">
                Confirm Booking
            </button>
        </form>
    </div>

    <!-- Summary Section (Sticky Right) -->
    <div class="booking-summary">
        <img src="<?= htmlspecialchars(packageImageUrl($package['destination'], $package['image'] ?? '')) ?>" alt="<?= htmlspecialchars($package['destination']) ?>" class="summary-img">
        
        <h3 class="summary-title"><?= htmlspecialchars($package['destination']) ?></h3>
        <div class="summary-meta">
            <span>⏱ <?= htmlspecialchars($package['duration']) ?></span>
            <span>•</span>
            <span>⭐ 4.9/5</span>
        </div>

        <div class="price-breakdown">
            <div class="price-row">
                <span>Base Price (per person)</span>
                <span>₹ <?= number_format($package['price'], 2) ?></span>
            </div>
            <div class="price-row">
                <span>Travelers</span>
                <span id="display_travelers">x 1</span>
            </div>
            <div class="price-row total">
                <span>Total Amount</span>
                <span id="total_price_display">₹ 0.00</span>
            </div>
        </div>

        <button type="submit" form="booking_form" class="btn-teal" id="submit_btn_desktop">
            Confirm Booking →
        </button>
        <p style="text-align: center; font-size: 12px; color: var(--gray); margin-top: 15px;">
            🔒 Secure 256-bit encrypted payment
        </p>
    </div>
</div>

<script>
(() => {
    const serviceInput = document.getElementById('service_type');
    const tabs = document.querySelectorAll('.service-tab');
    const panels = {
        flight: document.getElementById('panel-flight'),
        train: document.getElementById('panel-train'),
        hotel: document.getElementById('panel-hotel'),
        cab: document.getElementById('panel-cab')
    };

    const totalDisplay = document.getElementById('total_price_display');
    const travelersDisplay = document.getElementById('display_travelers');
    const priceInput = document.getElementById('package_price');
    const submitBtnDesktop = document.getElementById('submit_btn_desktop');
    const submitBtnMobile = document.getElementById('submit_btn_mobile');

    const labels = {
        flight: 'Confirm Flight Booking',
        train: 'Confirm Train Booking',
        hotel: 'Confirm Hotel Booking',
        cab: 'Confirm Cab Booking'
    };

    function selectedService() {
        const value = (serviceInput.value || 'flight').toLowerCase();
        return panels[value] ? value : 'flight';
    }

    function travelerCountFor(service) {
        if (service === 'flight') {
            const a = parseInt(document.getElementById('flight_adults')?.value || '1', 10);
            const c = parseInt(document.getElementById('flight_children')?.value || '0', 10);
            return Math.max(1, a + c);
        }
        if (service === 'train') {
            const a = parseInt(document.getElementById('train_adults')?.value || '1', 10);
            const c = parseInt(document.getElementById('train_children')?.value || '0', 10);
            return Math.max(1, a + c);
        }
        if (service === 'hotel') {
            const a = parseInt(document.getElementById('hotel_adults')?.value || '1', 10);
            const c = parseInt(document.getElementById('hotel_children')?.value || '0', 10);
            return Math.max(1, a + c);
        }
        return Math.max(1, parseInt(document.getElementById('cab_passengers')?.value || '1', 10));
    }

    function updateTotal() {
        const service = selectedService();
        const travelers = travelerCountFor(service);
        const price = parseFloat(priceInput?.value || '0');
        const total = travelers * price;
        
        totalDisplay.textContent = '₹ ' + total.toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
        travelersDisplay.textContent = 'x ' + travelers;
    }

    function applyService(service) {
        serviceInput.value = service;

        tabs.forEach((tab) => {
            tab.classList.toggle('active', tab.dataset.service === service);
        });

        Object.entries(panels).forEach(([key, panel]) => {
            if (!panel) return;
            panel.classList.toggle('active', key === service);
        });

        const btnText = labels[service] || labels.flight;
        if(submitBtnDesktop) submitBtnDesktop.textContent = btnText + ' →';
        if(submitBtnMobile) submitBtnMobile.textContent = btnText;
        
        updateTotal();
    }

    tabs.forEach((tab) => {
        tab.addEventListener('click', () => {
            applyService(tab.dataset.service || 'flight');
        });
    });

    // Listen to all traveler count inputs to update price in real-time
    const inputsToWatch = ['flight_adults','flight_children','train_adults','train_children','hotel_adults','hotel_children','cab_passengers'];
    inputsToWatch.forEach((id) => {
        const el = document.getElementById(id);
        if (el) el.addEventListener('change', updateTotal);
    });

    // Handle mobile button visibility on resize
    function toggleMobileBtn() {
        if (window.innerWidth <= 900) {
            submitBtnDesktop.style.display = 'none';
            submitBtnMobile.style.display = 'flex';
        } else {
            submitBtnDesktop.style.display = 'flex';
            submitBtnMobile.style.display = 'none';
        }
    }
    
    window.addEventListener('resize', toggleMobileBtn);

    // Initialize
    applyService(selectedService());
    toggleMobileBtn();
})();
</script>
</body>
</html>