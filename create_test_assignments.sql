-- create_test_assignments.sql
-- Run this AFTER migration_11_assigned_it_staff.sql and AFTER
-- create_test_tickets.sql (kailangan existing na yung IT-2026-9001 to 9020
-- test tickets, at yung 3 default na pangalan sa it_staff mula sa
-- migration_08_it_staff.sql: "Jonard F. Mujer", "Rustom V. Lajara",
-- "John Atuz R. Acar").
--
-- Layunin: bigyan ng sample "Assigned to" values yung ilan sa test
-- tickets, para makita/ma-test yung bagong dropdown at "Claim"-style
-- flow — may specific na technician, at may sadyang iniwang blangko
-- (general / walang partikular na tao pa) para makumpirma na parehong
-- nagagana ang dalawang case.

USE tpmc_ticketing;

UPDATE tickets SET assigned_it_staff = 'Jonard F. Mujer'  WHERE id = 'IT-2026-9001';
UPDATE tickets SET assigned_it_staff = 'Rustom V. Lajara' WHERE id = 'IT-2026-9002';
UPDATE tickets SET assigned_it_staff = 'John Atuz R. Acar' WHERE id = 'IT-2026-9003';
-- Sadyang iniwan itong walang assigned_it_staff (NULL) — dapat lumabas
-- sa app bilang "— Unassigned —" kahit "In progress" na ang status,
-- para makita nating gumagana yung "general, walang specific na tao pa" case.
UPDATE tickets SET assigned_it_staff = NULL WHERE id = 'IT-2026-9005';
