-- create_test_assignments.sql
-- Run this AFTER migration_11_assigned_it_staff.sql and AFTER
-- create_test_tickets.sql (needs the existing IT-2026-9001 to 9020 test
-- tickets, and the 3 default names in it_staff from
-- migration_08_it_staff.sql: "Jonard F. Mujer", "Rustom V. Lajara",
-- "John Atuz R. Acar").
--
-- Purpose: gives some test tickets sample "Assigned to" values, so the
-- new dropdown and "Claim"-style flow can be seen/tested — some with a
-- specific technician, and one left deliberately blank (general, no
-- specific person yet) to confirm both cases work correctly.

USE tpmc_ticketing;

UPDATE tickets SET assigned_it_staff = 'Jonard F. Mujer'  WHERE id = 'IT-2026-9001';
UPDATE tickets SET assigned_it_staff = 'Rustom V. Lajara' WHERE id = 'IT-2026-9002';
UPDATE tickets SET assigned_it_staff = 'John Atuz R. Acar' WHERE id = 'IT-2026-9003';
-- Deliberately left with no assigned_it_staff (NULL) — should show up
-- in the app as "— Unassigned —" even though the status is already
-- "In progress", to confirm the "general, no specific person yet" case
-- works correctly.
UPDATE tickets SET assigned_it_staff = NULL WHERE id = 'IT-2026-9005';
