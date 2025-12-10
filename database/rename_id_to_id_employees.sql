-- =====================================================
-- RENAME COLUMN id TO id_employees
-- =====================================================

-- 1. Employees table
ALTER TABLE `employees` 
CHANGE COLUMN `id` `id_employees` INT UNSIGNED NOT NULL AUTO_INCREMENT;

-- 2. Update foreign keys di tabel attendances
-- (emp_id tetap merujuk ke employees.id_employees)

-- 3. Update foreign keys di tabel lain jika ada
-- Cek dulu foreign key yang ada:
-- SHOW CREATE TABLE attendances;
-- SHOW CREATE TABLE latetimes;
-- SHOW CREATE TABLE leaves;
-- SHOW CREATE TABLE overtimes;

-- Biasanya foreign key sudah benar (emp_id), tidak perlu diubah
-- Tapi pastikan merujuk ke kolom yang benar

-- =====================================================
-- SETELAH RENAME, STRUKTUR JADI:
-- employees.id_employees (PRIMARY KEY)
-- attendances.emp_id → employees.id_employees
-- =====================================================

