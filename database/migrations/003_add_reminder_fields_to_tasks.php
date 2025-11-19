<?php

return "
ALTER TABLE tasks 
ADD COLUMN reminder_type ENUM('none', 'one_time', 'daily', 'weekly', 'monthly') DEFAULT 'none',
ADD COLUMN reminder_date DATE NULL,
ADD COLUMN reminder_time TIME NULL,
ADD COLUMN last_reminder_sent TIMESTAMP NULL,
ADD INDEX idx_reminder_type (reminder_type),
ADD INDEX idx_reminder_datetime (reminder_date, reminder_time),
ADD INDEX idx_last_reminder_sent (last_reminder_sent);
";