# Embedded Email System for Instroom

## Overview

The Embedded Email system transforms Instroom into a complete email workspace where teams can:

- ✅ Connect Gmail or Outlook in one click
- ✅ Send and receive emails without leaving Instroom
- ✅ Every email auto-tagged to campaign and pipeline stage
- ✅ Use personalized templates with creator variables
- ✅ Set follow-up reminders tied to conversations
- ✅ View full thread history on every creator profile

---

## Architecture

```
┌─────────────────────────────────────────────────────────────────────────┐
│                         EMBEDDED EMAIL SYSTEM                             │
├─────────────────────────────────────────────────────────────────────────┤
│                                                                         │
│  ┌─────────────────┐    ┌─────────────────┐    ┌─────────────────┐     │
│  │   GMAIL API     │    │  OUTLOOK API    │    │   SYNC ENGINE   │     │
│  │   (OAuth2)      │    │  (Microsoft)    │    │   (Background)  │     │
│  └────────┬────────┘    └────────┬────────┘    └─────────────────┘     │
│           │                      │                                      │
│           └──────────────────────┘                                      │
│                      │                                                  │
│  ┌───────────────────┴─────────────────────────────────────────────┐   │
│  │                      EMAIL CONTROLLER                            │   │
│  │  ┌──────────┐ ┌──────────┐ ┌──────────┐ ┌──────────┐          │   │
│  │  │  Send    │ │  Sync    │ │ Templates│ │Follow-ups│          │   │
│  │  └──────────┘ └──────────┘ └──────────┘ └──────────┘          │   │
│  └─────────────────────────────────────────────────────────────────┘   │
│                      │                                                  │
│  ┌───────────────────┴─────────────────────────────────────────────┐   │
│  │                      EMAIL MODEL                                 │   │
│  │  ┌──────────┐ ┌──────────┐ ┌──────────┐ ┌──────────┐ ┌─────────┐│   │
│  │  │ Accounts │ │ Threads  │ │  Emails  │ │Templates │ │ Follow- ││   │
│  │  └──────────┘ └──────────┘ └──────────┘ └──────────┘ │  ups   ││   │
│  └─────────────────────────────────────────────────────────────────┘   │
│                                                                         │
│  ┌─────────────────────────────────────────────────────────────────┐   │
│  │                      DATABASE TABLES                             │   │
│  │  • email_accounts  • email_threads  • emails                     │   │
│  │  • email_templates • email_follow_ups • email_sync_logs          │   │
│  └─────────────────────────────────────────────────────────────────┘   │
│                                                                         │
└─────────────────────────────────────────────────────────────────────────┘
```

---

## Database Schema

### 1. email_accounts
Stores OAuth-connected email accounts (Gmail/Outlook).

| Field | Type | Description |
|-------|------|-------------|
| id | INT PK | Auto-increment ID |
| user_id | INT FK | Owner user ID |
| brand_id | INT FK | Associated brand |
| provider | ENUM | 'gmail' or 'outlook' |
| email_address | VARCHAR | Email address |
| display_name | VARCHAR | Sender name |
| access_token | TEXT | OAuth access token |
| refresh_token | TEXT | OAuth refresh token |
| token_expires_at | TIMESTAMP | Token expiry |
| is_primary | BOOLEAN | Default account |
| is_active | BOOLEAN | Active status |
| sync_enabled | BOOLEAN | Auto-sync enabled |
| last_sync_at | TIMESTAMP | Last sync time |

### 2. email_threads
Groups related emails into conversations.

| Field | Type | Description |
|-------|------|-------------|
| id | INT PK | Auto-increment ID |
| thread_id | VARCHAR | Unique thread identifier |
| account_id | INT FK | Email account |
| subject | VARCHAR | Email subject |
| participants | JSON | Email addresses array |
| influencer_id | INT FK | Linked influencer |
| campaign_id | INT FK | Linked campaign |
| pipeline_stage | VARCHAR | Current pipeline stage |
| tags | JSON | Array of tags |
| is_read | BOOLEAN | Read status |
| is_starred | BOOLEAN | Starred status |
| is_archived | BOOLEAN | Archived status |
| last_message_at | TIMESTAMP | Latest activity |

### 3. emails
Individual email messages.

| Field | Type | Description |
|-------|------|-------------|
| id | INT PK | Auto-increment ID |
| message_id | VARCHAR | Provider message ID |
| thread_id | VARCHAR | Parent thread |
| account_id | INT FK | Email account |
| from_address | VARCHAR | Sender email |
| from_name | VARCHAR | Sender name |
| to_addresses | JSON | Recipients |
| cc_addresses | JSON | CC recipients |
| bcc_addresses | JSON | BCC recipients |
| subject | VARCHAR | Subject line |
| body_plain | TEXT | Plain text content |
| body_html | LONGTEXT | HTML content |
| has_attachments | BOOLEAN | Has files attached |
| attachments | JSON | Attachment metadata |
| direction | ENUM | 'incoming' or 'outgoing' |
| is_read | BOOLEAN | Read status |
| is_starred | BOOLEAN | Starred status |
| is_draft | BOOLEAN | Draft status |
| sent_at | TIMESTAMP | Send time |
| received_at | TIMESTAMP | Receive time |
| influencer_id | INT FK | Linked influencer |
| campaign_id | INT FK | Linked campaign |
| pipeline_stage_before | VARCHAR | Previous stage |
| pipeline_stage_after | VARCHAR | New stage |
| follow_up_reminder_at | TIMESTAMP | Reminder time |

### 4. email_templates
Reusable email templates.

| Field | Type | Description |
|-------|------|-------------|
| id | INT PK | Auto-increment ID |
| user_id | INT FK | Creator |
| brand_id | INT FK | Associated brand |
| name | VARCHAR | Template name |
| description | TEXT | Description |
| subject | VARCHAR | Email subject |
| body_plain | TEXT | Plain text version |
| body_html | LONGTEXT | HTML version |
| variables | JSON | Available variables |
| category | VARCHAR | Template category |
| is_shared | BOOLEAN | Shared with team |
| is_favorite | BOOLEAN | Marked as favorite |
| usage_count | INT | Times used |
| last_used_at | TIMESTAMP | Last usage time |

### 5. email_follow_ups
Follow-up reminders.

| Field | Type | Description |
|-------|------|-------------|
| id | INT PK | Auto-increment ID |
| thread_id | VARCHAR | Associated thread |
| email_id | INT FK | Related email |
| user_id | INT FK | Reminder owner |
| influencer_id | INT FK | Linked influencer |
| campaign_id | INT FK | Linked campaign |
| reminder_at | TIMESTAMP | When to remind |
| message | TEXT | Reminder note |
| status | ENUM | 'pending', 'completed', 'snoozed', 'cancelled' |
| snoozed_until | TIMESTAMP | Snooze end time |
| completed_at | TIMESTAMP | Completion time |

---

## API Endpoints

### Authentication
| Endpoint | Method | Description |
|----------|--------|-------------|
| `/email/connect/gmail` | GET | Start Gmail OAuth |
| `/email/oauth/gmail/callback` | GET | Gmail OAuth callback |
| `/email/connect/outlook` | GET | Start Outlook OAuth |
| `/email/oauth/outlook/callback` | GET | Outlook OAuth callback |

### Inbox & Threads
| Endpoint | Method | Description |
|----------|--------|-------------|
| `/email` | GET | Main inbox view |
| `/email/thread/{id}` | GET | View thread details |
| `/email/compose` | GET | Compose new email |
| `/email/send` | POST | Send email (AJAX) |
| `/email/get_unread_count` | GET | Get unread count |
| `/email/mark_read/{id}` | POST | Mark thread read |
| `/email/star_thread/{id}` | POST | Toggle star status |
| `/email/archive_thread/{id}` | POST | Archive thread |

### Templates
| Endpoint | Method | Description |
|----------|--------|-------------|
| `/email/get_templates` | GET | Get templates list |
| `/email/get_template` | GET | Get single template |
| `/email/save_template` | POST | Save template |
| `/email/preview_template` | POST | Preview with variables |
| `/email/delete_template/{id}` | POST | Delete template |

### Follow-ups
| Endpoint | Method | Description |
|----------|--------|-------------|
| `/email/create_follow_up` | POST | Create reminder |
| `/email/get_follow_ups` | GET | Get user's reminders |
| `/email/complete_follow_up/{id}` | POST | Mark complete |
| `/email/snooze_follow_up/{id}` | POST | Snooze reminder |

---

## Default Templates

### 1. Initial Outreach
**Subject:** Partnership Opportunity with {{brand_name}}

**Variables:**
- `{{influencer_name}}`
- `{{platform}}`
- `{{brand_name}}`
- `{{sender_name}}`
- `{{sender_title}}`
- `{{contact_email}}`
- `{{contact_phone}}`

### 2. Follow-up: No Response
**Subject:** Re: Partnership Opportunity with {{brand_name}}

**Variables:**
- `{{influencer_name}}`
- `{{brand_name}}`
- `{{sender_name}}`
- `{{collaboration_details}}`

### 3. Partnership Confirmed
**Subject:** Welcome to {{brand_name}} Family! 🎉

**Variables:**
- `{{influencer_name}}`
- `{{brand_name}}`
- `{{sender_name}}`
- `{{discount_code}}`
- `{{discount_amount}}`
- `{{valid_until}}`
- `{{affiliate_link}}`
- `{{shipping_address}}`
- `{{brand_hashtag}}`
- `{{brand_handle}}`
- `{{tracking_link}}`

---

## Configuration

### 1. Config File (`application/config/email.php`)

```php
<?php
defined('BASEPATH') OR exit('No direct script access allowed');

// Google OAuth
$config['google_client_id'] = 'YOUR_GOOGLE_CLIENT_ID';
$config['google_client_secret'] = 'YOUR_GOOGLE_CLIENT_SECRET';

// Microsoft OAuth
$config['microsoft_client_id'] = 'YOUR_MICROSOFT_CLIENT_ID';
$config['microsoft_client_secret'] = 'YOUR_MICROSOFT_CLIENT_SECRET';

// Email Settings
$config['default_signature'] = '';
$config['track_opens'] = TRUE;
$config['track_clicks'] = TRUE;
```

### 2. Routes (`application/config/routes.php`)

```php
// Email routes
$route['email'] = 'email/index';
$route['email/compose'] = 'email/compose';
$route['email/thread/(:any)'] = 'email/thread/$1';
$route['email/send'] = 'email/send';
$route['email/connect/gmail'] = 'email/connect_gmail';
$route['email/oauth/gmail/callback'] = 'email/gmail_callback';
$route['email/connect/outlook'] = 'email/connect_outlook';
```

---

## Installation Steps

### 1. Run Migration

```bash
# Navigate to project directory
cd c:\xampp\htdocs\instroom\Instroom_System

# Run the migration via your database tool
# Import: application/migrations/20250119_add_email_tables.sql
```

### 2. Configure OAuth Credentials

**Google Cloud Console:**
1. Go to https://console.cloud.google.com/
2. Create a new project
3. Enable Gmail API
4. Create OAuth 2.0 credentials
5. Add authorized redirect URI: `http://localhost:8080/email/oauth/gmail/callback`
6. Copy Client ID and Secret to config

**Azure Active Directory:**
1. Go to https://portal.azure.com/
2. Register new application
3. Add Microsoft Graph API permissions (Mail.Send, Mail.Read)
4. Add redirect URI: `http://localhost:8080/email/oauth/outlook/callback`
5. Copy Client ID and Secret to config

### 3. Access the Email System

Navigate to: `http://localhost:8080/email`

---

## Usage Flow

```
1. FIRST TIME SETUP
   └── User clicks "Connect Gmail/Outlook"
       └── OAuth authentication flow
           └── Account added to email_accounts table
               └── Initial sync triggered

2. DAILY USAGE
   └── User visits /email
       └── Sees unified inbox from all connected accounts
           └── Can filter by: account, campaign, pipeline stage, read status
               └── Click thread to view conversation

3. SENDING EMAILS
   └── Compose new or reply
       └── Select from templates or write custom
           └── Variables auto-populated from influencer data
               └── Send via Gmail/Outlook API
                   └── Email saved to database with all metadata

4. AUTOMATION
   └── Incoming email detected
       └── Thread linked to influencer/campaign if matched
           └── Pipeline stage updated (e.g., "Replied" → "In Progress")
               └── Follow-up reminder created if needed
```

---

## Security Considerations

1. **OAuth Tokens**: Stored encrypted in database, never exposed to frontend
2. **Refresh Tokens**: Used to get new access tokens automatically
3. **HTTPS Required**: All OAuth flows require HTTPS in production
4. **CSRF Protection**: State parameter used in OAuth flows
5. **Permission Checks**: All endpoints verify user owns the resource

---

## Performance Optimization

1. **Pagination**: All lists paginated (50 items default)
2. **Indexing**: Database indexes on all query fields
3. **Lazy Loading**: Thread content loaded on demand
4. **Background Sync**: Email sync runs via cron/job queue
5. **Caching**: Token validity cached to minimize refreshes

---

## Future Enhancements

1. **AI-Powered Features**:
   - Smart reply suggestions
   - Sentiment analysis
   - Best time to send recommendations

2. **Advanced Automation**:
   - Drip campaigns
   - Auto-responders
   - Trigger-based workflows

3. **Collaboration**:
   - Shared drafts
   - Email assignments
   - Internal notes on threads

4. **Analytics**:
   - Response rate tracking
   - A/B testing for templates
   - Campaign performance reports

---

## Support & Troubleshooting

### Common Issues

**"Token expired" Error:**
- Token auto-refreshes on next action
- If persistent, reconnect account in Settings

**Emails not syncing:**
- Check sync is enabled in account settings
- Verify account connection is active
- Check sync logs for errors

**Template variables not working:**
- Verify variable names match exactly
- Check influencer data exists for the field
- Use preview function to test

---

## Development Team

**Feature Owner:** Instroom Product Team  
**Lead Developer:** [Your Name]  
**Created:** January 2025  
**Version:** 1.0.0

---

**For questions or support, contact:** support@instroom.com