-- migration_08_it_staff.sql
-- Patakbuhin sa phpMyAdmin (SQL tab, tpmc_ticketing database).
--
-- Layunin: hiwalay na listahan ng PANGALAN ng IT technicians (hindi
-- naka-tali sa login accounts), para magamit sa "Resolved by" dropdown
-- kapag nilagyan ng specific na tao (hal. "Jonard F. Mujer") sa halip
-- na generic na "IT Department (You)". Editable ito sa app mismo via
-- "Manage IT staff", hindi na kailangan mag-code ulit para magdagdag
-- o magtanggal ng pangalan.

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
