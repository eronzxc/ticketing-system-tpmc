CREATE TABLE notifications (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT NOT NULL,
  ticket_id VARCHAR(20) NOT NULL,
  type ENUM('new_ticket', 'due_soon', 'in_progress', 'resolved') NOT NULL,
  message VARCHAR(255) NOT NULL,
  is_read TINYINT(1) NOT NULL DEFAULT 0,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  INDEX idx_user_unread (user_id, is_read),
  INDEX idx_ticket (ticket_id)
);

-- Prevents duplicate "due soon" notifications from being created every
-- time the dashboard is polled/refreshed.
ALTER TABLE tickets ADD COLUMN due_soon_notified TINYINT(1) NOT NULL DEFAULT 0;
