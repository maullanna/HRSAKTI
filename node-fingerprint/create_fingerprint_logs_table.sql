-- Create fingerprint_logs table for tracking fingerprint device logs
CREATE TABLE IF NOT EXISTS `fingerprint_logs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `employee_id` bigint(20) unsigned NOT NULL,
  `device_id` bigint(20) unsigned NOT NULL,
  `action` enum('check_in','check_out') NOT NULL,
  `timestamp` datetime NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fingerprint_logs_employee_id_foreign` (`employee_id`),
  KEY `fingerprint_logs_device_id_foreign` (`device_id`),
  KEY `idx_timestamp` (`timestamp`),
  KEY `idx_employee_timestamp` (`employee_id`, `timestamp`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Add foreign key constraints if tables exist
-- ALTER TABLE `fingerprint_logs` 
--   ADD CONSTRAINT `fingerprint_logs_employee_id_foreign` 
--   FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`) ON DELETE CASCADE;

-- ALTER TABLE `fingerprint_logs` 
--   ADD CONSTRAINT `fingerprint_logs_device_id_foreign` 
--   FOREIGN KEY (`device_id`) REFERENCES `finger_devices` (`id`) ON DELETE CASCADE;
