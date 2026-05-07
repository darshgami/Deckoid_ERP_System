BACKEND_STRUCTURE.md
Lead Management ERP System
1. Architecture Overview
Backend Architecture Pattern
Pattern

Layered Monolithic Architecture

Client (HTML/JS)
        │
        ▼
Apache Server
        │
        ▼
PHP Controllers
        │
        ▼
Service Layer
        │
        ▼
PDO Database Layer
        │
        ▼
MySQL Database
Architecture Principles
Thin controllers
Business logic inside services
Database access isolated via repository layer
Standardized JSON responses
Stateless API responses
Session-based authentication
Authentication Strategy
Type

Session Authentication + JWT API Support

Access Flow
Login → Validate Credentials
      → Create Session
      → Generate Access Token
      → Store Refresh Token
Data Flow
Frontend Form
    ↓
Validation Layer
    ↓
Business Service
    ↓
Database Transaction
    ↓
API Response
Caching Strategy
MVP

No distributed caching.

Local Cache
Session cache
Browser cache headers
Query optimization via indexes
2. Database Schema
Table: users
Column	Type	Constraints	Description
id	CHAR(36)	PK, NOT NULL	UUID primary key
full_name	VARCHAR(150)	NOT NULL	User full name
email	VARCHAR(255)	UNIQUE, NOT NULL	Login email
username	VARCHAR(100)	UNIQUE, NOT NULL	Username
password_hash	VARCHAR(255)	NOT NULL	bcrypt password
role	ENUM('admin','manager','staff')	DEFAULT 'staff'	Access level
status	ENUM('active','inactive')	DEFAULT 'active'	Account status
last_login_at	TIMESTAMP	NULL	Last login
created_at	TIMESTAMP	DEFAULT CURRENT_TIMESTAMP	Created date
updated_at	TIMESTAMP	DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP	Updated date
Indexes
Index	Columns
PRIMARY	id
UNIQUE	email
UNIQUE	username
INDEX	role
INDEX	status
Relationships
One user → many sessions
One user → many leads
Table: sessions
Column	Type	Constraints	Description
id	CHAR(36)	PK, NOT NULL	UUID
user_id	CHAR(36)	FK → users.id ON DELETE CASCADE	Session owner
refresh_token	VARCHAR(512)	NOT NULL	JWT refresh token
ip_address	VARCHAR(45)	NOT NULL	User IP
user_agent	VARCHAR(500)	NOT NULL	Browser info
expires_at	TIMESTAMP	NOT NULL	Expiry
created_at	TIMESTAMP	DEFAULT CURRENT_TIMESTAMP	Created
updated_at	TIMESTAMP	DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP	Updated
Indexes
Index	Columns
PRIMARY	id
INDEX	user_id
INDEX	expires_at
Relationships
Many sessions → one user
Table: leads
Column	Type	Constraints	Description
id	CHAR(36)	PK, NOT NULL	UUID
lead_id	VARCHAR(50)	UNIQUE, NOT NULL	Business lead ID
lead_date	DATE	NOT NULL	Lead creation date
company_client_name	VARCHAR(255)	NOT NULL	Client/company
contact_person	VARCHAR(150)	NOT NULL	Contact name
mobile_number	VARCHAR(20)	NOT NULL	Mobile
alternative_number	VARCHAR(20)	NULL	Secondary number
email_id	VARCHAR(255)	NULL	Email
city	VARCHAR(100)	NULL	City
state	VARCHAR(100)	NULL	State
source_of_lead	VARCHAR(100)	NOT NULL	Lead source
service_interested_in	VARCHAR(255)	NULL	Services
lead_category	ENUM('Hot','Warm','Cold')	NOT NULL	Category
lead_status	VARCHAR(100)	NOT NULL	Lead status
priority	ENUM('High','Medium','Low')	DEFAULT 'Medium'	Priority
assigned_to	CHAR(36)	FK → users.id ON DELETE SET NULL	Assigned user
next_followup_date	DATE	NULL	Follow-up
last_followup_notes	TEXT	NULL	Notes
requirement_details	TEXT	NULL	Requirements
estimated_budget	DECIMAL(12,2)	NULL	Budget
proposal_sent	BOOLEAN	DEFAULT FALSE	Proposal
meeting_scheduled	BOOLEAN	DEFAULT FALSE	Meeting
quotation_sent	BOOLEAN	DEFAULT FALSE	Quotation
deal_status	VARCHAR(100)	NOT NULL	Deal status
expected_closing_date	DATE	NULL	Closing
payment_status	VARCHAR(100)	NOT NULL	Payment
client_onboard_date	DATE	NULL	Onboard
project_start_date	DATE	NULL	Project start
project_status	VARCHAR(100)	NULL	Project
reference_by	VARCHAR(255)	NULL	Reference
website_social_link	VARCHAR(500)	NULL	Website
remarks_notes	TEXT	NULL	Remarks
created_by	CHAR(36)	FK → users.id ON DELETE SET NULL	Creator
created_at	TIMESTAMP	DEFAULT CURRENT_TIMESTAMP	Created
updated_at	TIMESTAMP	DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP	Updated
Indexes
Index	Columns
PRIMARY	id
UNIQUE	lead_id
INDEX	mobile_number
INDEX	email_id
INDEX	lead_status
INDEX	priority
INDEX	city
INDEX	deal_status
INDEX	payment_status
INDEX	assigned_to
Relationships
Many leads → one user (assigned_to)
Many leads → one user (created_by)
Table: lead_activity_logs
Column	Type	Constraints	Description
id	CHAR(36)	PK	UUID
lead_id	CHAR(36)	FK → leads.id ON DELETE CASCADE	Lead
user_id	CHAR(36)	FK → users.id ON DELETE SET NULL	User
activity_type	VARCHAR(100)	NOT NULL	Action type
old_value	TEXT	NULL	Previous value
new_value	TEXT	NULL	Updated value
notes	TEXT	NULL	Notes
created_at	TIMESTAMP	DEFAULT CURRENT_TIMESTAMP	Created
updated_at	TIMESTAMP	DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP	Updated
Relationships
Many activity logs → one lead
Many activity logs → one user
3. API Endpoints
Authentication APIs
POST /api/auth/register
Authentication Required

No

Request Body
{
  "fullName": "John Doe",
  "email": "john@example.com",
  "username": "johndoe",
  "password": "SecurePass123!"
}
Validation Rules
Field	Rules
fullName	Required, max 150 chars
email	Required, valid email
username	Required, min 4 chars
password	Required, min 8 chars
Success Response
201 Created
{
  "success": true,
  "message": "User registered successfully"
}
Error Cases
Code	Reason
400	Validation failed
409	Email exists
500	Internal error
Side Effects
Create user record
Hash password
POST /api/auth/login
Request Body
{
  "username": "johndoe",
  "password": "SecurePass123!"
}
Success Response
200 OK
{
  "success": true,
  "accessToken": "jwt-token",
  "refreshToken": "refresh-token",
  "user": {
    "id": "uuid",
    "fullName": "John Doe",
    "role": "admin"
  }
}
Error Cases
Code	Reason
401	Invalid credentials
403	Account inactive
429	Too many attempts
500	Internal error
Side Effects
Create session
Update last login
POST /api/auth/logout
Authentication Required

Yes

Success Response
{
  "success": true,
  "message": "Logged out successfully"
}
Side Effects
Delete session
Invalidate refresh token
POST /api/auth/refresh
Request Body
{
  "refreshToken": "jwt-refresh-token"
}
Success Response
{
  "accessToken": "new-access-token"
}
Lead APIs
GET /api/leads
Authentication Required

Yes

Query Parameters
?page=1
&limit=20
&search=abc
&leadStatus=Interested
&priority=High
Success Response
{
  "success": true,
  "data": [
    {
      "id": "uuid",
      "companyClientName": "ABC Pvt Ltd",
      "mobileNumber": "9876543210",
      "leadStatus": "Interested"
    }
  ]
}
Error Cases
Code	Reason
401	Unauthorized
500	Database failure
Side Effects
None
POST /api/leads
Request Body
{
  "leadDate": "2026-05-07",
  "companyClientName": "ABC Pvt Ltd",
  "contactPerson": "Rahul",
  "mobileNumber": "9876543210",
  "emailId": "rahul@example.com",
  "sourceOfLead": "Google",
  "leadCategory": "Hot",
  "leadStatus": "Interested",
  "priority": "High"
}
Validation Rules
Field	Rules
companyClientName	Required
mobileNumber	Required, unique
emailId	Valid email
leadCategory	Hot/Warm/Cold
priority	High/Medium/Low
Success Response
201 Created
{
  "success": true,
  "message": "Lead created successfully",
  "leadId": "uuid"
}
Error Cases
Code	Reason
400	Validation failed
409	Duplicate mobile
401	Unauthorized
500	Database error
Side Effects
Create lead
Create activity log
GET /api/leads/{id}
Success Response
{
  "success": true,
  "data": {
    "id": "uuid",
    "companyClientName": "ABC Pvt Ltd"
  }
}
Error Cases
Code	Reason
404	Lead not found
401	Unauthorized
PUT /api/leads/{id}
Side Effects
Update lead
Store activity log
DELETE /api/leads/{id}
Authorization Required

Admin only

Success Response
{
  "success": true,
  "message": "Lead deleted successfully"
}
Error Cases
Code	Reason
403	Forbidden
404	Lead missing
Side Effects
Delete lead
Delete activity logs
GET /api/leads/export
Response

Excel file download

Error Cases
Code	Reason
204	No records
500	Export failed
4. Authentication
JWT Access Token Payload
{
  "sub": "user-uuid",
  "role": "admin",
  "email": "john@example.com",
  "iat": 1715060000,
  "exp": 1715063600
}
Access Token
Property	Value
Expiry	1 hour
Refresh Token
Property	Value
Expiry	30 days
Authorization Levels
Role	Permissions
admin	Full access
manager	Manage leads
staff	Assigned leads only
Protected Routes
Route	Roles
DELETE /api/leads	admin
POST /api/leads	admin, manager, staff
GET /api/leads	authenticated users
Password Hashing
Setting	Value
Algorithm	bcrypt
Rounds	12
5. Error Response Format
Standard Error Structure
{
  "success": false,
  "error": {
    "code": "VALIDATION_ERROR",
    "message": "Validation failed",
    "details": [
      {
        "field": "email",
        "message": "Invalid email format"
      }
    ]
  }
}
Error Code Mapping
Error Code	HTTP Status
VALIDATION_ERROR	400
UNAUTHORIZED	401
FORBIDDEN	403
NOT_FOUND	404
CONFLICT	409
RATE_LIMITED	429
INTERNAL_ERROR	500
6. Caching Strategy
Cache Targets
Resource	TTL
Dashboard statistics	5 minutes
Dropdown values	24 hours
User session data	Session lifetime
Cache Key Formats
dashboard_stats_{userId}
dropdown_values
lead_search_{query}
Cache Invalidation Rules
Action	Invalidate
Lead created	Dashboard cache
Lead updated	Lead cache
Lead deleted	Dashboard + lead cache
7. Rate Limiting
Endpoint Type	Limit
Login	5 requests / 15 min
Register	3 requests / hour
Lead Search	100 requests / minute
Export