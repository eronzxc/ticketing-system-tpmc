-- migration_10_departments.sql
-- Run this in phpMyAdmin (SQL tab, tpmc_ticketing database selected).
-- AFTER migration_09 has already been applied.
--
-- Purpose: moves the department list out of hardcoded HTML/JS and into
-- the database, so IT can add new departments (hospital is growing) or
-- retire old ones without needing a code change.

USE tpmc_ticketing;

CREATE TABLE IF NOT EXISTS departments (
  id         INT AUTO_INCREMENT PRIMARY KEY,
  name       VARCHAR(80)  NOT NULL UNIQUE,
  is_active  TINYINT(1)   NOT NULL DEFAULT 1,
  created_at DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

INSERT INTO departments (name) VALUES
  ('Accounting'),('Admin'),('Admitting'),('Audiometry'),('Audit'),('Billing'),
  ('Biomedical'),('Cashier'),('CSR'),('Dietary'),('Emergency Room'),
  ('Engineering'),('Eye Center'),('Finance'),('Heart Station'),('Hemodialysis'),
  ('HMO'),('Human Resources'),('ICU'),('Industrial'),('IT Department'),
  ('Laboratory'),('Medical Records'),('NICU'),('Outpatient Department'),
  ('Pharmacy'),('PhilHealth'),('PhilHealth Yakap'),('Physical Rehab'),
  ('Pulmonology'),('Purchasing'),('Radiology'),('Station 3A'),('Station 3B'),
  ('Station 3C');