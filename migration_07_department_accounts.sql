-- migration_07_department_accounts.sql
-- Run this in phpMyAdmin (SQL tab, with the tpmc_ticketing database selected)
-- AFTER migration_01 through migration_06 have already been applied.
--
-- Purpose: accounts are moving from "one account per person" to "one
-- shared account per department" (e.g. a single "Pharmacy" login used by
-- everyone in that department). This adds a UNIQUE constraint so the
-- system can never end up with two accounts for the same department again.
--
-- ⚠️ IMPORTANT — DO THIS FIRST, BEFORE RUNNING THIS FILE:
-- If you currently have more than one account per department (e.g. both
-- "Krizian Janna" and another Accounting account), this ALTER TABLE will
-- FAIL with a duplicate-key error. Before running this:
--   1. Open "System users" (IT-only, sidebar icon)
--   2. For each department, keep exactly ONE account and delete the rest
--      (or disable them if you want to keep their ticket history intact —
--      disabling does NOT free up the department for a duplicate, only
--      deleting does)
--   3. Once every department has at most one account, run this file.

USE tpmc_ticketing;

ALTER TABLE users
  ADD CONSTRAINT uq_users_department UNIQUE (department);
