APP_FLOW.md
Lead Management ERP System
1. Entry Points

Users can enter the application through the following entry points.

Entry Point	URL	Access Type	Description
Login Page	/login	Public	Main authentication entry
Session Redirect	Any protected route	Automatic	Redirects unauthorized users to login
Dashboard	/dashboard	Authenticated	Main ERP home screen
Direct Lead URL	/leads/view/{id}	Authenticated	Opens specific lead details
Browser Refresh Recovery	Current route	Automatic	Restores session if active
Mobile Browser Shortcut	Saved browser shortcut	Authenticated	Opens previously used route
2. Core User Flows
Flow 1 — User Login (Onboarding / Authentication)
Happy Path
Step 1 — Open Login Page
Route: /login
Elements
Username field
Password field
Login button
User Action

User enters valid credentials.

System Response
Credentials validated
Session created
Redirect to /dashboard
Next Step

Dashboard loads successfully.

Validation Rules
Field	Rule
Username	Required
Password	Required
Password	Minimum 6 characters
Error States
Invalid Username/Password
System Response

Display message:

"Invalid username or password."

User Actions
Retry login
Reset fields
Empty Fields
System Response

Display inline validation:

"Username is required."
"Password is required."

Session Timeout
System Response

Redirect to login page.

Message:

"Your session has expired. Please log in again."

Server Error
System Response

Display:

"Unable to log in right now. Please try again later."

Edge Cases
Edge Case	Behavior
User refreshes login page after login	Redirect to dashboard
User presses browser back after login	Stay authenticated
Internet disconnects during login	Show offline message
Multiple rapid login attempts	Delay repeated requests
Flow 2 — Add New Lead
Happy Path
Step 1 — Open Add Lead Page
Route: /leads/add
Elements
All Excel-defined form fields
Dropdown selectors
Save button
Cancel button
Step 2 — Enter Lead Details
User Action

User fills form.

System Response
Real-time validation
Dropdown values loaded
Step 3 — Save Lead
User Action

Click "Save Lead"

System Response
Data validated
Lead stored in database
Success notification displayed

Message:

"Lead added successfully."

Next Step

Redirect to /leads

Validation Rules
Field	Rule
Company / Client Name	Required
Mobile Number	Required
Mobile Number	10–15 digits
Email ID	Valid email format
Lead Status	Must match dropdown value
Priority	Must match dropdown value
Next Follow-up Date	Cannot be past date
Error States
Duplicate Mobile Number
System Response

Display warning:

"Lead with this mobile number already exists."

Actions
Continue anyway
Edit existing lead
Cancel save
Invalid Email
System Response

Display:

"Please enter a valid email address."

Missing Required Fields
System Response

Highlight fields.

Message:

"Please complete all required fields."

Save Timeout
System Response

Display:

"Saving is taking longer than expected."

Actions
Retry
Cancel
Edge Cases
Edge Case	Behavior
User closes tab during save	Draft not stored
Session expires during form entry	Redirect after save attempt
User clicks save multiple times	Prevent duplicate submission
Very long notes entered	Textarea auto-scroll
Flow 3 — Lead Search & Filter
Happy Path
Step 1 — Open Lead List
Route: /leads
Elements
Search bar
Filter dropdowns
Lead table
Pagination
Step 2 — Search Lead
User Action

Enter:

Company name
Mobile number
Email
System Response

Filtered results appear instantly.

Step 3 — Apply Filters
User Action

Select:

Lead Status
Deal Status
Priority
City
System Response

Table updates dynamically.

Next Step

User opens lead details.

Error States
No Results Found
System Response

Display:

"No leads found matching your filters."

Actions
Clear filters
Search again
Invalid Filter Combination
System Response

Display:

"Selected filters returned no matching leads."

Server Timeout
System Response

Display:

"Unable to load leads. Please refresh the page."

Edge Cases
Edge Case	Behavior
Empty search input	Display all leads
Large dataset	Paginate automatically
Browser refresh	Preserve filters
Invalid URL parameters	Reset filters
Flow 4 — Edit Lead
Happy Path
Step 1 — Open Lead Details
Route: /leads/view/{id}
Step 2 — Click Edit
Step 3 — Update Fields
Step 4 — Save Changes
System Response

Display:

"Lead updated successfully."

Error States
Lead Not Found

Display:

"This lead no longer exists."

Validation Failure

Display field-level errors.

Simultaneous Edit Conflict

Display:

"This lead was updated by another user."

Edge Cases
Edge Case	Behavior
User refreshes during edit	Reload latest data
Browser back button	Preserve unsaved warning
Session expiry	Redirect to login
Flow 5 — Excel Export
Happy Path
Step 1 — Open Lead List
Step 2 — Apply Filters
Step 3 — Click Export
System Response
Generate Excel file
Start download

Message:

"Export completed successfully."

Error States
Empty Export

Display:

"No data available for export."

Export Failure

Display:

"Unable to generate export file."

Large File Timeout

Display:

"Export is taking longer than expected."

Edge Cases
Edge Case	Behavior
Export interrupted	Retry option
Special characters in notes	UTF-8 encoding
Large datasets	Chunked export
Flow 6 — Account Management
Happy Path
Step 1 — Open Profile Menu
Step 2 — Click Logout
System Response
Session destroyed
Redirect to /login

Message:

"Logged out successfully."

Error States
Logout Failure

Display:

"Unable to log out properly."

Edge Cases
Edge Case	Behavior
Session already expired	Redirect silently
Multiple tabs open	Logout all tabs
3. Navigation Map
Login
│
├── Dashboard
│   ├── Recent Leads
│   ├── Statistics Cards
│
├── Leads
│   ├── Lead List
│   │   ├── Search
│   │   ├── Filters
│   │   ├── Pagination
│   │
│   ├── Add Lead
│   │
│   ├── View Lead
│   │   ├── Edit Lead
│   │   ├── Delete Lead
│
├── Export Excel
│
├── Profile
│   ├── Logout
│
├── Error Pages
│   ├── 404
│   ├── 500
│   ├── Offline
4. Screen Inventory
Login Screen
Property	Value
Route	/login
Access	Public
Purpose	Authenticate users
Key Elements
Username
Password
Login button
Actions
Action	Result
Login	Dashboard
Invalid Login	Error message
States
Default
Loading
Validation Error
Server Error
Dashboard Screen
Property	Value
Route	/dashboard
Access	Authenticated
Purpose	Lead overview
Key Elements
Statistics cards
Recent leads
Navigation sidebar
Actions
Action	Result
Open Leads	/leads
Add Lead	/leads/add
States
Loading
Empty
Success
Error
Lead List Screen
Property	Value
Route	/leads
Access	Authenticated
Purpose	Search and manage leads
Key Elements
Search bar
Filters
Lead table
Export button
States
Loading
Empty
Filtered
Error
Add Lead Screen
Property	Value
Route	/leads/add
Access	Authenticated
Purpose	Create lead
States
Default
Validation Error
Saving
Success
Lead Details Screen
Property	Value
Route	/leads/view/{id}
Access	Authenticated
Purpose	View lead information
Actions
Action	Result
Edit	Edit mode
Delete	Confirmation
5. Decision Points
Authentication Logic
IF user session exists
THEN open requested page
ELSE redirect to login
Duplicate Lead Logic
IF mobile number already exists
THEN show duplicate warning
ELSE continue save
Export Logic
IF filtered results > 0
THEN generate Excel file
ELSE show empty export message
Session Timeout Logic
IF inactivity exceeds session limit
THEN destroy session and redirect to login
Search Logic
IF search input empty
THEN display all leads
ELSE display filtered leads
6. Error Handling
404 Page Not Found
Display
Error illustration
Message:

"Page not found."

Actions Available
Back to Dashboard
Retry URL
500 Internal Server Error
Display

Message:

"Something went wrong on our side."

Actions
Retry
Return dashboard
Offline State
Display

Message:

"No internet connection."

Actions
Retry connection
Continue offline viewing (cached pages only)
Database Failure
Display

Message:

"Unable to connect to the server."

Actions
Retry
Contact administrator
7. Responsive Behavior
Desktop Behavior
Feature	Behavior
Sidebar	Expanded
Tables	Full columns visible
Forms	Two-column layout
Filters	Inline horizontal
Mobile Behavior
Feature	Behavior
Sidebar	Collapsible menu
Tables	Horizontal scroll
Forms	Single-column layout
Filters	Stack vertically
Buttons	Full-width
Tablet Behavior
Feature	Behavior
Sidebar	Compact
Tables	Partial scroll
Forms	Mixed layout
Mobile Flow Differences
Add Lead

Desktop:

Two-column form

Mobile:

Single-column form
Sticky save button
Lead Table

Desktop:

Full table view

Mobile:

Scrollable cards/table hybrid
Navigation

Desktop:

Persistent sidebar

Mobile:

Hamburger navigation menu