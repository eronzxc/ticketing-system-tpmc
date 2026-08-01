-- create_bootstrap_it_account.sql
-- Kailangan ito kasi walang self-registration na ngayon at walang default
-- seeded na account — kaya kung walang IT account, walang makakapag-login
-- kahit sino para makagawa ng ibang accounts sa "System users".
--
-- Username: admin
-- Password: Password123   <-- test password lang, PALITAN pagkatapos makapag-login

USE tpmc_ticketing;

INSERT INTO users (fullname, username, email, password_hash, department, is_active, can_delete_tickets)
VALUES (
  'IT Department',
  'admin',
  NULL,
  '$2y$10$Eiz16IpWBOCOZiuG9pFix.xRw9IyWogIxp3jYiRTanW/KL5S0vsWq',
  'IT Department',
  1,
  1
);
