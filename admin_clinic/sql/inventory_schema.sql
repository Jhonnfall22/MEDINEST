CREATE TABLE IF NOT EXISTS `clinic_inventory` (
  `id` INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `clinic_id` INT(10) UNSIGNED NOT NULL,
  `item_name` VARCHAR(255) NOT NULL,
  `category` VARCHAR(100) NOT NULL,
  `quantity` INT(11) NOT NULL DEFAULT 0,
  `unit` VARCHAR(50) NOT NULL,
  `expiration_date` DATE DEFAULT NULL,
  `status` VARCHAR(50) NOT NULL DEFAULT 'Available',
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP(),
  CONSTRAINT `fk_clinic_inventory_clinic` FOREIGN KEY (`clinic_id`) REFERENCES `clinics` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
