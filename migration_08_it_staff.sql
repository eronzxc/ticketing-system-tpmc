-- migration_08_it_staff.sql
-- Run this in phpMyAdmin (SQL tab, tpmc_ticketing database selected).
--
-- Purpose: a separate list of IT technician NAMES (not tied to login
-- accounts), used to populate the "Resolved by" dropdown when a specific
-- person (e.g. "Jonard F. Mujer") should be credited instead of the
-- generic "IT Department (You)". Editable from within the app itself via
-- "Manage IT staff" — no code change needed to add or remove a name.

USE tpmc_ticketing;

CREATE TABLE IF NOT EXISTS it_staff (
  id INT AUTO_INCREMENT PRIMARY KEY,
  full_name VARCHAR(255) NOT NULL,
  is_active TINYINT(1) NOT NULL DEFAULT 1,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
);

INSERT INTO it_staff (full_name) VALUES
  ('Jonard F. Mujer'),
  ('Rustom V. Lajara'),
  ('John Atuz R. Acar');
