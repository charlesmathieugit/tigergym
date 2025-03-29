-- --------------------------------------------------------
-- Hôte:                         127.0.0.1
-- Version du serveur:           8.0.30 - MySQL Community Server - GPL
-- SE du serveur:                Win64
-- HeidiSQL Version:             12.1.0.6537
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

-- Listage des données de la table tigergym.articles : ~12 rows (environ)
DELETE FROM `articles`;
INSERT INTO `articles` (`id`, `name`, `description`, `price`, `image`, `category`, `stock`, `size_available`, `created_at`, `updated_at`) VALUES
	(1, 'T-Shirt Performance', 'T-shirt technique respirant pour l\'entraînement', 29.99, NULL, 'vetements-hommes', 50, 'S,M,L,XL', '2025-03-22 09:22:26', '2025-03-22 09:22:26'),
	(2, 'Short Training', 'Short léger et confortable pour la musculation', 24.99, NULL, 'vetements-hommes', 40, 'S,M,L,XL', '2025-03-22 09:22:26', '2025-03-22 09:22:26'),
	(3, 'Débardeur Muscle', 'Débardeur pour la musculation et le cardio', 19.99, NULL, 'vetements-hommes', 30, 'S,M,L,XL', '2025-03-22 09:22:26', '2025-03-22 09:22:26'),
	(4, 'Legging Fitness', 'Legging haute performance avec compression', 39.99, NULL, 'vetements-femmes', 45, 'S,M,L', '2025-03-22 09:22:26', '2025-03-22 09:22:26'),
	(5, 'Brassière Sport Pro', 'Brassière maintien maximum pour tous types d\'activités', 34.99, NULL, 'vetements-femmes', 35, 'S,M,L', '2025-03-22 09:22:26', '2025-03-22 09:22:26'),
	(6, 'Top Training', 'Top respirant pour le fitness et le yoga', 29.99, NULL, 'vetements-femmes', 40, 'S,M,L', '2025-03-22 09:22:26', '2025-03-22 09:22:26'),
	(7, 'Tapis de Course Pro', 'Tapis de course professionnel avec inclinaison automatique', 999.99, NULL, 'machines', 5, NULL, '2025-03-22 09:22:26', '2025-03-22 09:22:26'),
	(8, 'Vélo Elliptique Elite', 'Vélo elliptique silencieux avec 12 programmes', 799.99, NULL, 'machines', 3, NULL, '2025-03-22 09:22:26', '2025-03-22 09:22:26'),
	(9, 'Rameur Performance', 'Rameur professionnel avec résistance magnétique', 699.99, NULL, 'machines', 4, NULL, '2025-03-22 09:22:26', '2025-03-22 09:22:26'),
	(10, 'Whey Protein Gold', 'Protéine premium saveur vanille - 2kg', 39.99, NULL, 'complements', 100, NULL, '2025-03-22 09:22:26', '2025-03-22 09:22:26'),
	(11, 'BCAA 2:1:1', 'Acides aminés essentiels - 100 gélules', 29.99, NULL, 'complements', 80, NULL, '2025-03-22 09:22:26', '2025-03-22 09:22:26'),
	(12, 'Pre-Workout Boost', 'Booster d\'énergie saveur fruits rouges - 300g', 34.99, NULL, 'complements', 60, NULL, '2025-03-22 09:22:26', '2025-03-22 09:22:26');

-- Listage des données de la table tigergym.comments : ~0 rows (environ)
DELETE FROM `comments`;

-- Listage des données de la table tigergym.ratings : ~0 rows (environ)
DELETE FROM `ratings`;

-- Listage des données de la table tigergym.users : ~0 rows (environ)
DELETE FROM `users`;
INSERT INTO `users` (`id`, `email`, `password`, `firstname`, `lastname`, `address`, `phone`, `role`, `created_at`) VALUES
	(1, 'admin@tigergym.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Admin', 'TigerGym', NULL, NULL, 'admin', '2025-03-22 09:22:26');

/*!40103 SET TIME_ZONE=IFNULL(@OLD_TIME_ZONE, 'system') */;
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IFNULL(@OLD_FOREIGN_KEY_CHECKS, 1) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40111 SET SQL_NOTES=IFNULL(@OLD_SQL_NOTES, 1) */;
(1, 'T-Shirt Performance', '...', 'vetements-hommes', ...),
(2, 'Short Training', '...', 'vetements-hommes', ...),
(3, 'Débardeur Muscle', '...', 'vetements-hommes', ...),
(4, 'Legging Fitness', '...', 'vetements-femmes', ...),
(5, 'Brassière Sport Pro', '...', 'vetements-femmes', ...),
(6, 'Top Training', '...', 'vetements-femmes', ...)