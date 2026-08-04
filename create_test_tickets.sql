-- create_test_tickets.sql
-- Test tickets across various departments, categories, priorities, and statuses.
-- Requester/department linked to each department's test account via subquery.
-- Run this AFTER create_test_dept_accounts.sql.
--
-- NOTE: if you already have old test tickets IT-2026-9001 through
-- IT-2026-9007 (from an earlier batch), uncomment and run this DELETE
-- first, before the INSERTs below, to avoid a duplicate ID error:
--
-- DELETE FROM tickets WHERE id BETWEEN 'IT-2026-9001' AND 'IT-2026-9020';

USE tpmc_ticketing;

INSERT INTO tickets (id, requester, department, category, priority, description, status, created_at, due_date, resolved_at, resolved_by, remarks, created_by)
SELECT 'IT-2026-9001', u.department, u.department, 'Printer', 'Urgent', '[TEST DATA] Label printer sa Pharmacy hindi na gumagana, paulit-ulit na paper jam.', 'Open', '2026-07-27 09:00:00', '2026-07-29 17:00:00', NULL, NULL, NULL, u.id
FROM users u WHERE u.username = 'pharmacy';

INSERT INTO tickets (id, requester, department, category, priority, description, status, created_at, due_date, resolved_at, resolved_by, remarks, created_by)
SELECT 'IT-2026-9002', u.department, u.department, 'Software', 'High', '[TEST DATA] LIS system nagfe-freeze pag nag-eencode ng results.', 'In progress', '2026-07-26 09:00:00', '2026-07-28 17:00:00', NULL, NULL, 'Naka-schedule na si vendor support ngayong araw.', u.id
FROM users u WHERE u.username = 'laboratory';

INSERT INTO tickets (id, requester, department, category, priority, description, status, created_at, due_date, resolved_at, resolved_by, remarks, created_by)
SELECT 'IT-2026-9003', u.department, u.department, 'Hardware', 'Urgent', '[TEST DATA] Vital signs monitor sa ER bed 3 hindi nag-o-on.', 'Open', '2026-07-28 09:00:00', '2026-07-29 17:00:00', NULL, NULL, NULL, u.id
FROM users u WHERE u.username = 'emergency.room';

INSERT INTO tickets (id, requester, department, category, priority, description, status, created_at, due_date, resolved_at, resolved_by, remarks, created_by)
SELECT 'IT-2026-9004', u.department, u.department, 'Software', 'Medium', '[TEST DATA] Excel keeps crashing pag nagbubukas ng malaking payroll file.', 'Resolved', '2026-07-23 09:00:00', '2026-07-26 17:00:00', '2026-07-27 15:00:00', 'IT Department', 'Na-increase ang RAM allocation, na-reinstall din ang Excel.', u.id
FROM users u WHERE u.username = 'accounting';

INSERT INTO tickets (id, requester, department, category, priority, description, status, created_at, due_date, resolved_at, resolved_by, remarks, created_by)
SELECT 'IT-2026-9005', u.department, u.department, 'Account / Password', 'Low', '[TEST DATA] Hindi makapag-login sa HRIS portal, nakalimutan ang password.', 'Resolved', '2026-07-22 09:00:00', '2026-07-24 17:00:00', '2026-07-23 15:00:00', 'IT Department', 'Na-reset ang password, na-verify na ang access.', u.id
FROM users u WHERE u.username = 'human.resources';

INSERT INTO tickets (id, requester, department, category, priority, description, status, created_at, due_date, resolved_at, resolved_by, remarks, created_by)
SELECT 'IT-2026-9006', u.department, u.department, 'Network / Internet', 'High', '[TEST DATA] Walang internet connection sa X-ray workstation 2.', 'Open', '2026-07-27 09:00:00', '2026-07-28 17:00:00', NULL, NULL, NULL, u.id
FROM users u WHERE u.username = 'radiology';

INSERT INTO tickets (id, requester, department, category, priority, description, status, created_at, due_date, resolved_at, resolved_by, remarks, created_by)
SELECT 'IT-2026-9007', u.department, u.department, 'Hardware', 'Urgent', '[TEST DATA] Nurse call button sa ICU bed 5 hindi gumagana.', 'In progress', '2026-07-27 09:00:00', '2026-07-28 17:00:00', NULL, NULL, 'Nasuri na, kailangan palitan ang button unit.', u.id
FROM users u WHERE u.username = 'icu';

INSERT INTO tickets (id, requester, department, category, priority, description, status, created_at, due_date, resolved_at, resolved_by, remarks, created_by)
SELECT 'IT-2026-9008', u.department, u.department, 'Printer', 'Medium', '[TEST DATA] OPD reception printer laging low toner warning kahit bago pa lang palitan.', 'Open', '2026-07-25 09:00:00', '2026-07-29 17:00:00', NULL, NULL, NULL, u.id
FROM users u WHERE u.username = 'outpatient.department';

INSERT INTO tickets (id, requester, department, category, priority, description, status, created_at, due_date, resolved_at, resolved_by, remarks, created_by)
SELECT 'IT-2026-9009', u.department, u.department, 'Software', 'Medium', '[TEST DATA] Billing system hindi ma-generate ang official receipt.', 'Open', '2026-07-28 09:00:00', '2026-07-30 17:00:00', NULL, NULL, NULL, u.id
FROM users u WHERE u.username = 'billing';

INSERT INTO tickets (id, requester, department, category, priority, description, status, created_at, due_date, resolved_at, resolved_by, remarks, created_by)
SELECT 'IT-2026-9010', u.department, u.department, 'Hardware', 'Urgent', '[TEST DATA] Incubator temperature display sa NICU nagpapakita ng maling reading.', 'In progress', '2026-07-26 09:00:00', '2026-07-27 17:00:00', NULL, NULL, 'Naka-request na ng biomed check.', u.id
FROM users u WHERE u.username = 'nicu';

INSERT INTO tickets (id, requester, department, category, priority, description, status, created_at, due_date, resolved_at, resolved_by, remarks, created_by)
SELECT 'IT-2026-9011', u.department, u.department, 'Others', 'Low', '[TEST DATA] Request para sa bagong email account ng bagong empleyado.', 'Resolved', '2026-07-24 09:00:00', '2026-07-27 17:00:00', '2026-07-26 15:00:00', 'IT Department', 'Nagawa na ang bagong email account.', u.id
FROM users u WHERE u.username = 'human.resources';

INSERT INTO tickets (id, requester, department, category, priority, description, status, created_at, due_date, resolved_at, resolved_by, remarks, created_by)
SELECT 'IT-2026-9012', u.department, u.department, 'Network / Internet', 'Medium', '[TEST DATA] Hindi ma-access ang shared drive mula sa Engineering office.', 'Open', '2026-07-27 09:00:00', '2026-07-29 17:00:00', NULL, NULL, NULL, u.id
FROM users u WHERE u.username = 'engineering';

INSERT INTO tickets (id, requester, department, category, priority, description, status, created_at, due_date, resolved_at, resolved_by, remarks, created_by)
SELECT 'IT-2026-9013', u.department, u.department, 'Account / Password', 'Medium', '[TEST DATA] Locked out ang account matapos ang maraming failed login attempts.', 'Resolved', '2026-07-26 09:00:00', '2026-07-28 17:00:00', '2026-07-27 15:00:00', 'IT Department', 'Na-unlock na ang account.', u.id
FROM users u WHERE u.username = 'purchasing';

INSERT INTO tickets (id, requester, department, category, priority, description, status, created_at, due_date, resolved_at, resolved_by, remarks, created_by)
SELECT 'IT-2026-9014', u.department, u.department, 'Hardware', 'Low', '[TEST DATA] Hindi gumagana ang barcode scanner sa dietary kitchen.', 'Open', '2026-07-27 09:00:00', '2026-07-30 17:00:00', NULL, NULL, NULL, u.id
FROM users u WHERE u.username = 'dietary';

INSERT INTO tickets (id, requester, department, category, priority, description, status, created_at, due_date, resolved_at, resolved_by, remarks, created_by)
SELECT 'IT-2026-9015', u.department, u.department, 'Software', 'High', '[TEST DATA] Records system nagbibigay ng error pag nagsse-search ng old patient records.', 'In progress', '2026-07-27 09:00:00', '2026-07-28 17:00:00', NULL, NULL, 'On-going troubleshooting kasama ang vendor.', u.id
FROM users u WHERE u.username = 'medical.records';

INSERT INTO tickets (id, requester, department, category, priority, description, status, created_at, due_date, resolved_at, resolved_by, remarks, created_by)
SELECT 'IT-2026-9016', u.department, u.department, 'Hardware', 'Urgent', '[TEST DATA] Dialysis machine sa station 4 nag-a-alarm nang paulit-ulit.', 'Resolved', '2026-07-25 09:00:00', '2026-07-27 17:00:00', '2026-07-26 15:00:00', 'IT Department', 'Na-recalibrate na ang machine, tested na ok.', u.id
FROM users u WHERE u.username = 'hemodialysis';

INSERT INTO tickets (id, requester, department, category, priority, description, status, created_at, due_date, resolved_at, resolved_by, remarks, created_by)
SELECT 'IT-2026-9017', u.department, u.department, 'Account / Password', 'Low', '[TEST DATA] Hiling na i-reset ang password ng pharmacy inventory system.', 'Resolved', '2026-07-21 09:00:00', '2026-07-23 17:00:00', '2026-07-22 15:00:00', 'IT Department', 'Na-reset na ang password.', u.id
FROM users u WHERE u.username = 'pharmacy';

INSERT INTO tickets (id, requester, department, category, priority, description, status, created_at, due_date, resolved_at, resolved_by, remarks, created_by)
SELECT 'IT-2026-9018', u.department, u.department, 'Printer', 'Medium', '[TEST DATA] Check printer sa Finance office paulit-ulit na naninigas ang paper.', 'Open', '2026-07-28 09:00:00', '2026-07-30 17:00:00', NULL, NULL, NULL, u.id
FROM users u WHERE u.username = 'finance';

INSERT INTO tickets (id, requester, department, category, priority, description, status, created_at, due_date, resolved_at, resolved_by, remarks, created_by)
SELECT 'IT-2026-9019', u.department, u.department, 'Hardware', 'High', '[TEST DATA] Centrifuge machine sa laboratory gumagawa ng malakas na ingay.', 'Open', '2026-07-27 09:00:00', '2026-07-29 17:00:00', NULL, NULL, NULL, u.id
FROM users u WHERE u.username = 'laboratory';

INSERT INTO tickets (id, requester, department, category, priority, description, status, created_at, due_date, resolved_at, resolved_by, remarks, created_by)
SELECT 'IT-2026-9020', u.department, u.department, 'Software', 'Medium', '[TEST DATA] Admitting system mabagal mag-load pag oras ng maraming pasyente.', 'In progress', '2026-07-27 09:00:00', '2026-07-28 17:00:00', NULL, NULL, 'Nireview ang server load, on-going optimization.', u.id
FROM users u WHERE u.username = 'admitting';

