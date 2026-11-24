-- Embedded Email System Migration for Instroom
-- Created: 2025-01-19

-- ============================================
-- 1. Email Accounts (Gmail/Outlook connections)
-- ============================================
CREATE TABLE IF NOT EXISTS email_accounts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    brand_id INT DEFAULT NULL,
    provider ENUM('gmail', 'outlook', 'custom') NOT NULL DEFAULT 'gmail',
    email_address VARCHAR(255) NOT NULL,
    display_name VARCHAR(255) DEFAULT NULL,
    access_token TEXT,
    refresh_token TEXT,
    token_expires_at TIMESTAMP NULL,
    scope TEXT,
    is_primary BOOLEAN DEFAULT FALSE,
    is_active BOOLEAN DEFAULT TRUE,
    sync_enabled BOOLEAN DEFAULT TRUE,
    last_sync_at TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    INDEX idx_user_id (user_id),
    INDEX idx_brand_id (brand_id),
    INDEX idx_email_address (email_address),
    INDEX idx_is_active (is_active)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- 2. Email Threads (Conversation grouping)
-- ============================================
CREATE TABLE IF NOT EXISTS email_threads (
    id INT AUTO_INCREMENT PRIMARY KEY,
    thread_id VARCHAR(255) NOT NULL UNIQUE,
    account_id INT NOT NULL,
    subject VARCHAR(500) DEFAULT NULL,
    participants JSON,
    influencer_id INT DEFAULT NULL,
    campaign_id INT DEFAULT NULL,
    pipeline_stage VARCHAR(100) DEFAULT NULL,
    tags JSON,
    is_archived BOOLEAN DEFAULT FALSE,
    is_starred BOOLEAN DEFAULT FALSE,
    is_read BOOLEAN DEFAULT FALSE,
    last_message_at TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    INDEX idx_thread_id (thread_id),
    INDEX idx_account_id (account_id),
    INDEX idx_influencer_id (influencer_id),
    INDEX idx_campaign_id (campaign_id),
    INDEX idx_pipeline_stage (pipeline_stage),
    INDEX idx_last_message_at (last_message_at),
    INDEX idx_is_archived (is_archived)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- 3. Emails (Individual messages)
-- ============================================
CREATE TABLE IF NOT EXISTS emails (
    id INT AUTO_INCREMENT PRIMARY KEY,
    message_id VARCHAR(255) NOT NULL UNIQUE,
    thread_id VARCHAR(255) NOT NULL,
    account_id INT NOT NULL,
    
    -- Sender/Recipient Info
    from_address VARCHAR(255) NOT NULL,
    from_name VARCHAR(255) DEFAULT NULL,
    to_addresses JSON NOT NULL,
    cc_addresses JSON DEFAULT NULL,
    bcc_addresses JSON DEFAULT NULL,
    reply_to VARCHAR(255) DEFAULT NULL,
    
    -- Content
    subject VARCHAR(500) DEFAULT NULL,
    body_plain TEXT,
    body_html LONGTEXT,
    
    -- Attachments
    has_attachments BOOLEAN DEFAULT FALSE,
    attachments JSON DEFAULT NULL,
    
    -- Metadata
    direction ENUM('incoming', 'outgoing') NOT NULL,
    is_read BOOLEAN DEFAULT FALSE,
    is_starred BOOLEAN DEFAULT FALSE,
    is_important BOOLEAN DEFAULT FALSE,
    is_draft BOOLEAN DEFAULT FALSE,
    is_archived BOOLEAN DEFAULT FALSE,
    
    -- Instroom Integration
    influencer_id INT DEFAULT NULL,
    campaign_id INT DEFAULT NULL,
    pipeline_stage_before VARCHAR(100) DEFAULT NULL,
    pipeline_stage_after VARCHAR(100) DEFAULT NULL,
    auto_reply_detected BOOLEAN DEFAULT FALSE,
    follow_up_reminder_at TIMESTAMP NULL,
    
    -- Timestamps
    sent_at TIMESTAMP NULL,
    received_at TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    -- Indexes
    INDEX idx_message_id (message_id),
    INDEX idx_thread_id (thread_id),
    INDEX idx_account_id (account_id),
    INDEX idx_from_address (from_address),
    INDEX idx_direction (direction),
    INDEX idx_sent_at (sent_at),
    INDEX idx_received_at (received_at),
    INDEX idx_is_read (is_read),
    INDEX idx_is_draft (is_draft),
    INDEX idx_influencer_id (influencer_id),
    INDEX idx_campaign_id (campaign_id),
    INDEX idx_follow_up_reminder (follow_up_reminder_at),
    FULLTEXT idx_subject_body (subject, body_plain)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- 4. Email Templates
-- ============================================
CREATE TABLE IF NOT EXISTS email_templates (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    brand_id INT DEFAULT NULL,
    name VARCHAR(255) NOT NULL,
    description TEXT DEFAULT NULL,
    subject VARCHAR(500) NOT NULL,
    body_plain TEXT,
    body_html LONGTEXT,
    variables JSON DEFAULT NULL,
    category VARCHAR(100) DEFAULT 'general',
    is_shared BOOLEAN DEFAULT FALSE,
    is_favorite BOOLEAN DEFAULT FALSE,
    usage_count INT DEFAULT 0,
    last_used_at TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    INDEX idx_user_id (user_id),
    INDEX idx_brand_id (brand_id),
    INDEX idx_category (category),
    INDEX idx_is_shared (is_shared),
    INDEX idx_is_favorite (is_favorite),
    FULLTEXT idx_name_subject (name, subject, body_plain)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- 5. Follow-up Reminders
-- ============================================
CREATE TABLE IF NOT EXISTS email_follow_ups (
    id INT AUTO_INCREMENT PRIMARY KEY,
    thread_id VARCHAR(255) NOT NULL,
    email_id INT DEFAULT NULL,
    user_id INT NOT NULL,
    influencer_id INT DEFAULT NULL,
    campaign_id INT DEFAULT NULL,
    reminder_at TIMESTAMP NOT NULL,
    message TEXT DEFAULT NULL,
    status ENUM('pending', 'completed', 'snoozed', 'cancelled') DEFAULT 'pending',
    snoozed_until TIMESTAMP NULL,
    completed_at TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    INDEX idx_thread_id (thread_id),
    INDEX idx_user_id (user_id),
    INDEX idx_influencer_id (influencer_id),
    INDEX idx_reminder_at (reminder_at),
    INDEX idx_status (status),
    INDEX idx_snoozed_until (snoozed_until)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- 6. Email Sync Log (for debugging/sync status)
-- ============================================
CREATE TABLE IF NOT EXISTS email_sync_logs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    account_id INT NOT NULL,
    sync_type ENUM('full', 'delta', 'sent', 'draft') DEFAULT 'delta',
    status ENUM('running', 'completed', 'failed', 'partial') DEFAULT 'running',
    messages_synced INT DEFAULT 0,
    messages_failed INT DEFAULT 0,
    last_synced_message_id VARCHAR(255) DEFAULT NULL,
    error_message TEXT DEFAULT NULL,
    started_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    completed_at TIMESTAMP NULL,
    
    INDEX idx_account_id (account_id),
    INDEX idx_status (status),
    INDEX idx_started_at (started_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- Add email_count to influencers table
-- ============================================
ALTER TABLE influencers 
ADD COLUMN IF NOT EXISTS email_count INT DEFAULT 0,
ADD COLUMN IF NOT EXISTS last_email_at TIMESTAMP NULL,
ADD COLUMN IF NOT EXISTS email_thread_id VARCHAR(255) DEFAULT NULL;

-- ============================================
-- Add email preferences to users table
-- ============================================
ALTER TABLE users 
ADD COLUMN IF NOT EXISTS email_signature TEXT DEFAULT NULL,
ADD COLUMN IF NOT EXISTS email_timezone VARCHAR(50) DEFAULT 'UTC',
ADD COLUMN IF NOT EXISTS email_notifications BOOLEAN DEFAULT TRUE;

-- ============================================
-- Create index for faster email searches
-- ============================================
CREATE INDEX IF NOT EXISTS idx_emails_thread_lookup ON emails(thread_id, direction, created_at);
CREATE INDEX IF NOT EXISTS idx_threads_influencer ON email_threads(influencer_id, last_message_at);
