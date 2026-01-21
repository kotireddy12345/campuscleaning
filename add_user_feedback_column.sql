-- Add user_feedback and feedback_comment columns to complaints table
-- user_feedback values: NULL (no feedback), 'ok', 'not_ok'
-- feedback_comment: optional user comment (max 500 chars)
ALTER TABLE complaints ADD COLUMN user_feedback VARCHAR(20) DEFAULT NULL;
ALTER TABLE complaints ADD COLUMN feedback_comment TEXT DEFAULT NULL;
