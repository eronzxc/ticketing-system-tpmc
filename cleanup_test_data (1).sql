-- cleanup_test_data.sql
-- Patakbuhin kapag tapos na sa testing ng TICKETS at gusto nang linisin
-- bago ang totoong paggamit. Test accounts ay HINDI kasama dito (sinadya
-- panatilihin base sa huling usapan natin).

USE tpmc_ticketing;

-- 1. Tanggalin ang mga reply/comment na naka-attach sa test tickets
--    (kailangan bago ang tickets mismo, dahil sa foreign key)
DELETE FROM ticket_comments WHERE ticket_id LIKE 'IT-2026-9%';

-- 2. Tanggalin ang mga notifications na naka-link sa test tickets
DELETE FROM notifications WHERE ticket_id LIKE 'IT-2026-9%';

-- 3. Tanggalin ang mismong test tickets (IT-2026-9001 hanggang 9999 range).
--    Kasama na rito ang "assigned_it_staff" values (create_test_assignments.sql)
--    dahil column lang ito sa parehong tickets row, hindi hiwalay na
--    table — awtomatiko itong matatanggal kasabay ng ticket.
DELETE FROM tickets WHERE id LIKE 'IT-2026-9%';
