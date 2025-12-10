-- =====================================================
-- ADD MINIMAL REQUIRED FIELDS ONLY
-- Field yang SUDAH DIPAKAI di form tapi BELUM ADA di DB
-- Run di phpMyAdmin: Database attendance_db
-- =====================================================

USE attendance_db;

-- 1. NIK (dari form input)
ALTER TABLE `employees` 
ADD COLUMN IF NOT EXISTS `nik` VARCHAR(16) NULL AFTER `employee_code`;

-- 2. Tanggal Lahir (dari form input)
ALTER TABLE `employees` 
ADD COLUMN IF NOT EXISTS `tanggal_lahir` DATE NULL AFTER `nik`;

-- 3. Pendidikan (dari form dropdown)
ALTER TABLE `employees` 
ADD COLUMN IF NOT EXISTS `pendidikan` ENUM('SD','SMP','SMA/SMK','D1','D2','D3','D4','S1','S2','S3') NULL AFTER `tanggal_lahir`;

-- 4. Kontrak Kerja (dari form dropdown)
ALTER TABLE `employees` 
ADD COLUMN IF NOT EXISTS `kontrak_kerja` ENUM('Tetap','Kontrak','Magang','PKL','Freelance') NULL DEFAULT 'Tetap' AFTER `pendidikan`;

-- 5. Kontrak Durasi (dari form input, untuk kontrak non-tetap)
ALTER TABLE `employees` 
ADD COLUMN IF NOT EXISTS `kontrak_durasi` INT NULL AFTER `kontrak_kerja`;

-- =====================================================
-- VERIFY
-- =====================================================
DESCRIBE employees;

-- Expected: nik, tanggal_lahir, pendidikan, kontrak_kerja, kontrak_durasi

