-- create_test_dept_accounts.sql
-- Test accounts for every department, password = Password123 for all.
-- Email left NULL (not required for department accounts, only IT).

USE tpmc_ticketing;

INSERT INTO users (fullname, username, email, password_hash, department, is_active) VALUES
  ('Accounting', 'accounting', NULL, '$2y$10$KZlFFCcA8P7hrpYNTy5qJeMh0f/JKhq11Y/36sT7NyHAHvLcSxiBy', 'Accounting', 1),
  ('Admin', 'admin.dept', NULL, '$2y$10$KZlFFCcA8P7hrpYNTy5qJeMh0f/JKhq11Y/36sT7NyHAHvLcSxiBy', 'Admin', 1),
  ('Admitting', 'admitting', NULL, '$2y$10$KZlFFCcA8P7hrpYNTy5qJeMh0f/JKhq11Y/36sT7NyHAHvLcSxiBy', 'Admitting', 1),
  ('Audiometry', 'audiometry', NULL, '$2y$10$KZlFFCcA8P7hrpYNTy5qJeMh0f/JKhq11Y/36sT7NyHAHvLcSxiBy', 'Audiometry', 1),
  ('Audit', 'audit', NULL, '$2y$10$KZlFFCcA8P7hrpYNTy5qJeMh0f/JKhq11Y/36sT7NyHAHvLcSxiBy', 'Audit', 1),
  ('Billing', 'billing', NULL, '$2y$10$KZlFFCcA8P7hrpYNTy5qJeMh0f/JKhq11Y/36sT7NyHAHvLcSxiBy', 'Billing', 1),
  ('Biomedical', 'biomedical', NULL, '$2y$10$KZlFFCcA8P7hrpYNTy5qJeMh0f/JKhq11Y/36sT7NyHAHvLcSxiBy', 'Biomedical', 1),
  ('Cashier', 'cashier', NULL, '$2y$10$KZlFFCcA8P7hrpYNTy5qJeMh0f/JKhq11Y/36sT7NyHAHvLcSxiBy', 'Cashier', 1),
  ('CSR', 'csr', NULL, '$2y$10$KZlFFCcA8P7hrpYNTy5qJeMh0f/JKhq11Y/36sT7NyHAHvLcSxiBy', 'CSR', 1),
  ('Dietary', 'dietary', NULL, '$2y$10$KZlFFCcA8P7hrpYNTy5qJeMh0f/JKhq11Y/36sT7NyHAHvLcSxiBy', 'Dietary', 1),
  ('Emergency Room', 'emergency.room', NULL, '$2y$10$KZlFFCcA8P7hrpYNTy5qJeMh0f/JKhq11Y/36sT7NyHAHvLcSxiBy', 'Emergency Room', 1),
  ('Engineering', 'engineering', NULL, '$2y$10$KZlFFCcA8P7hrpYNTy5qJeMh0f/JKhq11Y/36sT7NyHAHvLcSxiBy', 'Engineering', 1),
  ('Eye Center', 'eye.center', NULL, '$2y$10$KZlFFCcA8P7hrpYNTy5qJeMh0f/JKhq11Y/36sT7NyHAHvLcSxiBy', 'Eye Center', 1),
  ('Finance', 'finance', NULL, '$2y$10$KZlFFCcA8P7hrpYNTy5qJeMh0f/JKhq11Y/36sT7NyHAHvLcSxiBy', 'Finance', 1),
  ('Heart Station', 'heart.station', NULL, '$2y$10$KZlFFCcA8P7hrpYNTy5qJeMh0f/JKhq11Y/36sT7NyHAHvLcSxiBy', 'Heart Station', 1),
  ('Hemodialysis', 'hemodialysis', NULL, '$2y$10$KZlFFCcA8P7hrpYNTy5qJeMh0f/JKhq11Y/36sT7NyHAHvLcSxiBy', 'Hemodialysis', 1),
  ('HMO', 'hmo', NULL, '$2y$10$KZlFFCcA8P7hrpYNTy5qJeMh0f/JKhq11Y/36sT7NyHAHvLcSxiBy', 'HMO', 1),
  ('Human Resources', 'human.resources', NULL, '$2y$10$KZlFFCcA8P7hrpYNTy5qJeMh0f/JKhq11Y/36sT7NyHAHvLcSxiBy', 'Human Resources', 1),
  ('ICU', 'icu', NULL, '$2y$10$KZlFFCcA8P7hrpYNTy5qJeMh0f/JKhq11Y/36sT7NyHAHvLcSxiBy', 'ICU', 1),
  ('Industrial', 'industrial', NULL, '$2y$10$KZlFFCcA8P7hrpYNTy5qJeMh0f/JKhq11Y/36sT7NyHAHvLcSxiBy', 'Industrial', 1),
  ('Laboratory', 'laboratory', NULL, '$2y$10$KZlFFCcA8P7hrpYNTy5qJeMh0f/JKhq11Y/36sT7NyHAHvLcSxiBy', 'Laboratory', 1),
  ('Marketing', 'marketing', NULL, '$2y$10$KZlFFCcA8P7hrpYNTy5qJeMh0f/JKhq11Y/36sT7NyHAHvLcSxiBy', 'Marketing', 1),
  ('Medical Records', 'medical.records', NULL, '$2y$10$KZlFFCcA8P7hrpYNTy5qJeMh0f/JKhq11Y/36sT7NyHAHvLcSxiBy', 'Medical Records', 1),
  ('NICU', 'nicu', NULL, '$2y$10$KZlFFCcA8P7hrpYNTy5qJeMh0f/JKhq11Y/36sT7NyHAHvLcSxiBy', 'NICU', 1),
  ('Outpatient Department', 'outpatient.department', NULL, '$2y$10$KZlFFCcA8P7hrpYNTy5qJeMh0f/JKhq11Y/36sT7NyHAHvLcSxiBy', 'Outpatient Department', 1),
  ('Pharmacy', 'pharmacy', NULL, '$2y$10$KZlFFCcA8P7hrpYNTy5qJeMh0f/JKhq11Y/36sT7NyHAHvLcSxiBy', 'Pharmacy', 1),
  ('PhilHealth', 'philhealth', NULL, '$2y$10$KZlFFCcA8P7hrpYNTy5qJeMh0f/JKhq11Y/36sT7NyHAHvLcSxiBy', 'PhilHealth', 1),
  ('PhilHealth Yakap', 'philhealth.yakap', NULL, '$2y$10$KZlFFCcA8P7hrpYNTy5qJeMh0f/JKhq11Y/36sT7NyHAHvLcSxiBy', 'PhilHealth Yakap', 1),
  ('Physical Rehab', 'physical.rehab', NULL, '$2y$10$KZlFFCcA8P7hrpYNTy5qJeMh0f/JKhq11Y/36sT7NyHAHvLcSxiBy', 'Physical Rehab', 1),
  ('Pulmonology', 'pulmonology', NULL, '$2y$10$KZlFFCcA8P7hrpYNTy5qJeMh0f/JKhq11Y/36sT7NyHAHvLcSxiBy', 'Pulmonology', 1),
  ('Purchasing', 'purchasing', NULL, '$2y$10$KZlFFCcA8P7hrpYNTy5qJeMh0f/JKhq11Y/36sT7NyHAHvLcSxiBy', 'Purchasing', 1),
  ('Radiology', 'radiology', NULL, '$2y$10$KZlFFCcA8P7hrpYNTy5qJeMh0f/JKhq11Y/36sT7NyHAHvLcSxiBy', 'Radiology', 1),
  ('Station 3A', 'station.3a', NULL, '$2y$10$KZlFFCcA8P7hrpYNTy5qJeMh0f/JKhq11Y/36sT7NyHAHvLcSxiBy', 'Station 3A', 1),
  ('Station 3B', 'station.3b', NULL, '$2y$10$KZlFFCcA8P7hrpYNTy5qJeMh0f/JKhq11Y/36sT7NyHAHvLcSxiBy', 'Station 3B', 1),
  ('Station 3C', 'station.3c', NULL, '$2y$10$KZlFFCcA8P7hrpYNTy5qJeMh0f/JKhq11Y/36sT7NyHAHvLcSxiBy', 'Station 3C', 1);
