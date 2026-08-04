-- migration_11_assigned_it_staff.sql
-- Run this in phpMyAdmin (SQL tab, tpmc_ticketing database selected).
--
-- Purpose: "Assigned to" used to be tied to users.id (a login account) —
-- but IT Department is now a single shared login, so there was really
-- only ever one account that could be "assigned" (no way to know which
-- ACTUAL technician was handling it). This is the same problem already
-- solved for "Resolved by" using the separate it_staff table (just
-- names, no login of their own).
--
-- "Assigned to" now works like this:
--   - NULL / empty   = still generically "IT Department", no specific
--                       person assigned yet
--   - text value      = name of a specific technician from the
--                       "Manage IT staff" list (e.g. "Jonard F. Mujer")
--
-- The old `assigned_to` column (INT, referencing users.id) is left in
-- place, not touched/dropped — new code no longer uses it, but the cost
-- of keeping it around is low (same call made earlier for the
-- departments table vs. users.department).

USE tpmc_ticketing;

ALTER TABLE tickets ADD COLUMN assigned_it_staff VARCHAR(120) NULL AFTER assigned_to;
