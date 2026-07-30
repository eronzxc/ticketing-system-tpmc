-- migration_11_assigned_it_staff.sql
-- Patakbuhin sa phpMyAdmin (SQL tab, tpmc_ticketing database).
--
-- Layunin: yung dating "Assigned to" ay naka-tali sa users.id (login
-- account) — pero shared account na lang ang IT Department login ngayon,
-- kaya iisa lang talaga ang puwedeng "ma-assign" doon (walang paraan
-- malaman kung sinong TALAGANG technician ang humahawak). Kapareho ito
-- ng problemang inayos na natin sa "Resolved by" gamit ang hiwalay na
-- it_staff table (pangalan lang, walang sariling login).
--
-- Ganito na ngayon ang "Assigned to":
--   - NULL / walang laman  = "IT Department" pa lang sa pangkalahatan,
--                            walang partikular na taong nakatalaga
--   - may laman (text)     = pangalan ng specific na technician mula sa
--                            "Manage IT staff" list (hal. "Jonard F. Mujer")
--
-- Ang lumang column na `assigned_to` (INT, tumuturo sa users.id) ay
-- IIWAN LANG natin, hindi gagalawin/tatanggalin — hindi na ito gagamitin
-- ng bagong code, pero mababa ang cost na panatilihin ito ngayon (kagaya
-- ng naunang desisyon natin sa departments table vs users.department).

USE tpmc_ticketing;

ALTER TABLE tickets ADD COLUMN assigned_it_staff VARCHAR(120) NULL AFTER assigned_to;
