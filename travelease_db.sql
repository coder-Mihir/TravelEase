-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Oct 24, 2026 at 11:21 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `travelease_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `admins`
--

CREATE TABLE `admins` (
  `id` int(11) NOT NULL,
  `username` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admins`
--

INSERT INTO `admins` (`id`, `username`, `password`) VALUES
(1, 'admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi');

-- --------------------------------------------------------

--
-- Table structure for table `bookings`
--

CREATE TABLE `bookings` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `package_id` int(11) NOT NULL,
  `booking_date` date NOT NULL,
  `travelers` int(11) DEFAULT 1,
  `total_price` decimal(10,2) DEFAULT NULL,
  `status` enum('pending','confirmed','cancelled') DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `service_type` enum('tour_package','flight','train','hotel','cab') NOT NULL DEFAULT 'tour_package',
  `check_in_date` date DEFAULT NULL,
  `check_out_date` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `bookings`
--

INSERT INTO `bookings` (`id`, `user_id`, `package_id`, `booking_date`, `travelers`, `total_price`, `status`, `created_at`, `service_type`, `check_in_date`, `check_out_date`) VALUES
(1, 1, 1, '2026-11-05', 2, 15998.00, 'confirmed', '2026-10-21 17:45:15', 'tour_package', NULL, NULL),
(2, 1, 5, '2026-11-10', 2, 150000.00, 'confirmed', '2026-10-21 17:47:43', 'tour_package', NULL, NULL),
(3, 1, 13, '2026-11-01', 2, 8998.00, 'confirmed', '2026-10-22 05:49:21', 'tour_package', NULL, NULL),
(4, 1, 2, '2026-10-24', 1, 4999.00, 'confirmed', '2026-10-23 17:09:44', 'flight', NULL, NULL),
(5, 1, 3, '2026-11-11', 1, 5999.00, 'confirmed', '2026-10-23 17:11:03', 'flight', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `packages`
--

CREATE TABLE `packages` (
  `id` int(11) NOT NULL,
  `destination` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `price` decimal(10,2) NOT NULL,
  `duration` varchar(50) DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `available` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `packages`
--

INSERT INTO `packages` (`id`, `destination`, `description`, `price`, `duration`, `image`, `available`, `created_at`) VALUES
(1, 'Goa', 'Explore Baga Beach, Calangute Beach, Fort Aguada and vibrant nightlife.', 7999.00, '3 Days / 2 Nights', 'https://images.unsplash.com/photo-1512343879784-a960bf40e7f2?auto=format&fit=crop&w=900&q=80', 1, '2026-10-21 16:58:21'),
(2, 'Statue of Unity, Gujarat', 'Visit the Statue of Unity, Valley of Flowers and Sardar Sarovar Dam.', 4999.00, '2 Days / 1 Night', 'https://cdn.britannica.com/29/240229-050-ADA5E0E5/The-Statue-of-Unity-in-Gujarat-India.jpg', 1, '2026-10-21 16:58:21'),
(3, 'Mumbai, Maharashtra', 'Discover the Gateway of India, Marine Drive, Juhu Beach and Colaba.', 5999.00, '3 Days / 2 Nights', 'https://images.unsplash.com/photo-1570168007204-dfb528c6958f?auto=format&fit=crop&w=900&q=80', 1, '2026-10-21 16:58:21'),
(4, 'Delhi', 'Explore India Gate, Red Fort, Qutub Minar and historical Old Delhi.', 6999.00, '3 Days / 2 Nights', 'https://images.unsplash.com/photo-1587474260584-136574528ed5?auto=format&fit=crop&w=900&q=80', 1, '2026-10-21 16:58:21'),
(5, 'Burj Khalifa, Dubai', 'Discover the luxury of Dubai with desert safari, Burj Khalifa visit, and Dubai Mall shopping.', 75000.00, '5 Days / 4 Nights', 'https://images.unsplash.com/flagged/photo-1559717201-fbb671ff56b7?q=80&w=1171&auto=format&fit=crop&ixlib=rb-4.1.0&ixid=NHwxMjA3fDA%3D', 1, '2026-10-21 16:58:21'),
(6, 'Jaipur, Rajasthan', 'Experience Hawa Mahal, Amer Fort, City Palace and local markets.', 6499.00, '3 Days / 2 Nights', 'https://images.unsplash.com/photo-1599661046827-dacff0c0f09a?auto=format&fit=crop&w=900&q=80', 1, '2026-10-21 16:58:21'),
(7, 'Udaipur, Rajasthan', 'Enjoy Lake Pichola, City Palace, Jag Mandir and romantic lake views.', 7499.00, '3 Days / 2 Nights', 'https://images.unsplash.com/photo-1595658658481-d53d3f999875?auto=format&fit=crop&w=900&q=80', 1, '2026-10-21 16:58:21'),
(8, 'Jaisalmer, Rajasthan', 'Explore golden sandstone forts, desert dunes and camel safari experiences.', 8999.00, '3 Days / 2 Nights', 'https://images.unsplash.com/photo-1477587458883-47145ed94245?auto=format&fit=crop&w=900&q=80', 1, '2026-10-21 16:58:21'),
(9, 'Manali, Himachal Pradesh', 'Enjoy mountain views, Solang Valley, adventure activities and snow scenery.', 9999.00, '4 Days / 3 Nights', 'https://images.unsplash.com/photo-1626621341517-bbf3d9990a23?auto=format&fit=crop&w=900&q=80', 1, '2026-10-21 16:58:21'),
(10, 'Srinagar, Jammu and Kashmir', 'Experience Dal Lake, Mughal Gardens, houseboats and mountain scenery.', 11999.00, '4 Days / 3 Nights', 'https://images.unsplash.com/photo-1595815771614-ade9d652a65d?auto=format&fit=crop&w=900&q=80', 1, '2026-10-21 16:58:21'),
(11, 'Bali', 'Relax on the beautiful island of Bali with beach stays, temple tours, and rice terrace treks.', 65000.00, '6 Days / 5 Nights', 'https://media.cntravellerme.com/photos/68870cae16b3b95603955912/16:9/w_1920,c_limit/072425-Unwritten-Rules-Bali-GettyImages-1469116195.jpg', 1, '2026-10-21 16:58:21'),
(12, 'Leh Ladakh', 'Discover Pangong Lake, monasteries, high mountain passes and dramatic landscapes.', 15999.00, '5 Days / 4 Nights', 'https://images.unsplash.com/photo-1519681393784-d120267933ba?auto=format&fit=crop&w=900&q=80', 1, '2026-10-21 16:58:21'),
(13, 'Agra, Uttar Pradesh', 'Visit the Taj Mahal, Agra Fort and famous Mughal architectural landmarks.', 4499.00, '2 Days / 1 Night', 'https://images.unsplash.com/photo-1564507592333-c60657eea523?auto=format&fit=crop&w=900&q=80', 1, '2026-10-21 16:58:21'),
(14, 'Varanasi, Uttar Pradesh', 'Experience the Ganga Aarti, ancient ghats, temples and boat rides.', 5499.00, '2 Days / 1 Night', 'https://images.unsplash.com/photo-1561361513-2d000a50f0dc?auto=format&fit=crop&w=900&q=80', 1, '2026-10-21 16:58:21'),
(15, 'Paris', 'Experience the city of love with guided tours of the Eiffel Tower, Louvre Museum, and Seine River cruise.', 85000.00, '7 Days / 6 Nights', 'https://upload.wikimedia.org/wikipedia/commons/4/4b/La_Tour_Eiffel_vue_de_la_Tour_Saint-Jacques%2C_Paris_ao%C3%BBt_2014_%282%29.jpg?utm_source=en.wikipedia.org&utm_campaign=index&utm_content=original', 1, '2026-10-21 16:58:21'),
(16, 'Rishikesh, Uttarakhand', 'Enjoy river rafting, Laxman Jhula, yoga retreats and riverside scenery.', 5999.00, '3 Days / 2 Nights', 'https://images.unsplash.com/photo-1587922546307-776227941871?auto=format&fit=crop&w=900&q=80', 1, '2026-10-21 16:58:21'),
(17, 'Nainital, Uttarakhand', 'Relax beside Naini Lake and visit Snow View Point and scenic hill attractions.', 6999.00, '3 Days / 2 Nights', 'https://images.unsplash.com/photo-1597074866923-dc0589150358?auto=format&fit=crop&w=900&q=80', 1, '2026-10-21 16:58:21'),
(18, 'Munnar, Kerala', 'Explore tea plantations, misty hills, waterfalls and Eravikulam National Park.', 8999.00, '3 Days / 2 Nights', 'https://images.unsplash.com/photo-1602216056096-3b40cc0c9944?auto=format&fit=crop&w=900&q=80', 1, '2026-10-21 16:58:21'),
(19, 'Alleppey, Kerala', 'Enjoy Kerala backwaters, houseboat cruises, village life and palm-lined canals.', 9999.00, '3 Days / 2 Nights', 'https://upload.wikimedia.org/wikipedia/commons/e/e4/Alappuzha_Boat_Beauty_W.jpg?utm_source=en.wikipedia.org&utm_campaign=index&utm_content=original', 1, '2026-10-21 16:58:21'),
(20, 'Ooty, Tamil Nadu', 'Visit botanical gardens, tea estates, Ooty Lake and Nilgiri mountain viewpoints.', 7499.00, '3 Days / 2 Nights', 'https://images.unsplash.com/photo-1593693411515-c20261bcad6e?auto=format&fit=crop&w=900&q=80', 1, '2026-10-21 16:58:21'),
(21, 'Kodaikanal, Tamil Nadu', 'Explore Coakers Walk, Pillar Rocks, Kodaikanal Lake and forest viewpoints.', 6999.00, '3 Days / 2 Nights', 'https://images.unsplash.com/photo-1511497584788-876760111969?auto=format&fit=crop&w=900&q=80', 1, '2026-10-21 16:58:21'),
(22, 'Mysore, Karnataka', 'Discover Mysore Palace, Chamundi Hills, Brindavan Gardens and local culture.', 5499.00, '2 Days / 1 Night', 'https://karnatakatourism.org/_next/image/?url=https%3A%2F%2Fweb-cms.karnatakatourism.org%2Fwp-content%2Fuploads%2F2025%2F07%2FMysuru-Palace-banner-1920_1100.jpg&w=1920&q=75', 1, '2026-10-21 16:58:21'),
(23, 'Coorg, Karnataka', 'Experience coffee plantations, waterfalls, green hills and peaceful homestays.', 7999.00, '3 Days / 2 Nights', 'https://images.unsplash.com/photo-1441974231531-c6227db76b6e?auto=format&fit=crop&w=900&q=80', 1, '2026-10-21 16:58:21'),
(24, 'Tokyo', 'Explore the vibrant capital of Japan with visits to Shibuya, Mount Fuji, and authentic Japanese culture.', 95000.00, '8 Days / 7 Nights', 'https://upload.wikimedia.org/wikipedia/commons/c/c5/Tokyo_Shibuya_Scramble_Crossing_2018-10-09.jpg?utm_source=en.wikipedia.org&utm_campaign=index&utm_content=original', 1, '2026-10-21 16:58:21'),
(25, 'Hampi, Karnataka', 'Explore ancient Vijayanagara ruins, stone temples and fascinating rock landscapes.', 6499.00, '3 Days / 2 Nights', 'https://upload.wikimedia.org/wikipedia/commons/4/49/Hampi%2C_India%2C_View_of_Hampi_Bazaar_from_Matanga_Hill.jpg?utm_source=en.wikipedia.org&utm_campaign=index&utm_content=original', 1, '2026-10-21 16:58:21'),
(26, 'Darjeeling, West Bengal', 'Enjoy Himalayan mountain views, tea gardens, monasteries and the toy train.', 8999.00, '4 Days / 3 Nights', 'https://images.unsplash.com/photo-1544735716-392fe2489ffa?auto=format&fit=crop&w=900&q=80', 1, '2026-10-21 16:58:21'),
(27, 'Shillong, Meghalaya', 'Discover waterfalls, green hills, caves and the beautiful landscapes of Meghalaya.', 9999.00, '4 Days / 3 Nights', 'https://images.unsplash.com/photo-1472396961693-142e6e269027?auto=format&fit=crop&w=900&q=80', 1, '2026-10-21 16:58:21'),
(28, 'Puri, Odisha', 'Visit Jagannath Temple, Puri Beach and nearby Konark Sun Temple.', 5999.00, '3 Days / 2 Nights', 'https://upload.wikimedia.org/wikipedia/commons/6/6e/Shri_Jagannatha_Temple.jpg?utm_source=en.wikipedia.org&utm_campaign=index&utm_content=original', 1, '2026-10-21 16:58:21');

-- --------------------------------------------------------

--
-- Table structure for table `payments`
--

CREATE TABLE `payments` (
  `id` int(11) NOT NULL,
  `booking_id` int(11) NOT NULL,
  `payment_method` enum('credit_card','debit_card','upi','net_banking') NOT NULL,
  `payment_status` enum('pending','completed','failed') DEFAULT 'pending',
  `transaction_id` varchar(100) DEFAULT NULL,
  `amount` decimal(10,2) DEFAULT NULL,
  `paid_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `payments`
--

INSERT INTO `payments` (`id`, `booking_id`, `payment_method`, `payment_status`, `transaction_id`, `amount`, `paid_at`) VALUES
(1, 1, 'upi', 'completed', 'TXN6AD8FA32117A7', 15998.00, '2026-10-21 17:45:22'),
(2, 2, 'credit_card', 'completed', 'TXN6AD8FAC4C2F8A', 150000.00, '2026-10-21 17:47:48'),
(3, 3, 'upi', 'completed', 'TXN6AD9A3E66257F', 8998.00, '2026-10-22 05:49:26'),
(4, 4, 'upi', 'completed', 'TXN6ADB94E0E536C', 4999.00, '2026-10-23 17:09:52'),
(5, 5, 'upi', 'completed', 'TXN6ADB9D2EDFFAA', 5999.00, '2026-10-23 17:45:18');

-- --------------------------------------------------------

--
-- Table structure for table `reviews`
--

CREATE TABLE `reviews` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `place` varchar(150) NOT NULL,
  `review` text NOT NULL,
  `rating` tinyint(4) NOT NULL DEFAULT 5,
  `avatar` varchar(10) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `reviews`
--

INSERT INTO `reviews` (`id`, `name`, `place`, `review`, `rating`, `avatar`, `created_at`) VALUES
(1, 'Aisha Rahman', 'Dubai Trip', 'TravelEase made our Dubai vacation absolutely seamless. The AI assistant helped us pick the perfect itinerary and the booking process was effortless!', 5, 'AR', '2026-10-21 17:05:14'),
(2, 'Ravi Kumar', 'Delhi Trip', 'Best travel experience ever! The Delhi package was perfectly curated. Hotels, transfers, and guided tours — all handled brilliantly by TravelEase.', 5, 'RK', '2026-10-21 17:05:14'),
(3, 'Priya Menon', 'Paris Trip', 'I used the AI chatbot to plan my Paris trip and it recommended exactly what I was looking for. Highly recommend TravelEase to all travel lovers!', 5, 'PM', '2026-10-21 17:05:14'),
(4, 'James Wilson', 'Goa Trip', 'Goa was a dream come true. TravelEase package covered everything — beaches, nightlife, and local cuisine. The team was incredibly helpful.', 4, 'JW', '2026-10-21 17:05:14'),
(5, 'Meera Nair', 'Statue of Unity Trip', 'Smooth booking, great price, and the customer support was top-notch. The TravelEase AI assistant is truly a game-changer for travel planning!', 5, 'MN', '2026-10-21 17:05:14'),
(6, 'Carlos Rivera', 'Barcelona Trip', 'Visited Barcelona with my family and every detail was perfectly arranged. TravelEase truly delivers what they promise — unforgettable experiences!', 5, 'CR', '2026-10-21 17:05:14');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(100) NOT NULL,
  `email` varchar(150) NOT NULL,
  `password` varchar(255) NOT NULL,
  `mobile` varchar(15) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `email`, `password`, `mobile`, `address`, `created_at`) VALUES
(1, 'mmm@gmail.com', 'mmm@gmail.com', '$2y$10$O4inDDi/8P3SPDGbvnb4CeAvSqUA9fZYKKhrqR2dOZDmjzz7esP7K', '1234567894', 'INDIA', '2026-10-21 17:36:59');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admins`
--
ALTER TABLE `admins`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- Indexes for table `bookings`
--
ALTER TABLE `bookings`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `package_id` (`package_id`);

--
-- Indexes for table `packages`
--
ALTER TABLE `packages`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `payments`
--
ALTER TABLE `payments`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `transaction_id` (`transaction_id`),
  ADD KEY `booking_id` (`booking_id`);

--
-- Indexes for table `reviews`
--
ALTER TABLE `reviews`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admins`
--
ALTER TABLE `admins`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `bookings`
--
ALTER TABLE `bookings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `packages`
--
ALTER TABLE `packages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=29;

--
-- AUTO_INCREMENT for table `payments`
--
ALTER TABLE `payments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `reviews`
--
ALTER TABLE `reviews`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `bookings`
--
ALTER TABLE `bookings`
  ADD CONSTRAINT `bookings_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `bookings_ibfk_2` FOREIGN KEY (`package_id`) REFERENCES `packages` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `payments`
--
ALTER TABLE `payments`
  ADD CONSTRAINT `payments_ibfk_1` FOREIGN KEY (`booking_id`) REFERENCES `bookings` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
