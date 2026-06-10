@agency-backend-architect
@agency-frontend-developer
@agency-database-optimizer
@agency-qa-engineer

TASK: ADD DIRECT PAYMENT STATUS UPDATE FROM SALES ACTION MENU

PROJECT:
Deckoid ERP System

MODULE:
Sales → Invoice List

OBJECTIVE:

Currently when user clicks the 3-dot action menu, only:

* Edit
* Print
* Delete

are available.

I want a new Payment Status section inside the same dropdown.

NO new page.

NO modal.

NO extra screens.

Direct status update from the action menu.

==================================================

CURRENT FLOW

Sales List

↓

3 Dot Menu

↓

Edit
Print
Delete

==================================================

REQUIRED FLOW

Sales List

↓

3 Dot Menu

↓

Edit

Print

---

Payment Status

✓ Pending

✓ Partial

✓ Paid

✓ Cancelled

---

Delete

==================================================

UI REQUIREMENTS

Inside action dropdown add:

Payment Status

When user clicks:

Pending

Partial

Paid

Cancelled

Immediately update invoice status.

No page refresh.

Use AJAX.

Show success toast.

Example:

Payment Status Updated Successfully

==================================================

DATABASE

Audit invoice table.

Find actual status column.

Examples:

payment_status

invoice_status

status

Use existing column.

DO NOT create duplicate columns.

If column missing:

Add proper migration.

Update schema.sql.

==================================================

BACKEND

Create secure API endpoint.

Example:

api/update_invoice_status.php

Requirements:

Validate login session.

Validate invoice id.

Validate status value.

Allowed values only:

Pending

Partial

Paid

Cancelled

Return JSON only.

Success:

{
"success": true,
"message": "Payment status updated."
}

Failure:

{
"success": false,
"message": "Invalid status."
}

==================================================

FRONTEND

Update:

admin/sales.php

Locate 3-dot action dropdown.

Add:

Pending

Partial

Paid

Cancelled

Click action:

AJAX request

↓

Backend update

↓

Update badge instantly

↓

Show toast

No reload.

==================================================

STATUS BADGE COLORS

Pending

Orange

Partial

Blue

Paid

Green

Cancelled

Red

==================================================

LIVE TABLE UPDATE

After successful update:

Invoice row badge updates immediately.

Example:

PENDING

↓

User clicks PAID

↓

Badge changes to:

PAID

Without refresh.

==================================================

STAFF PERMISSIONS

Admin:

Can change status.

Staff:

Can view status.

Staff cannot change status.

Hide payment status actions for staff users.

==================================================

FILES TO AUDIT

admin/sales.php

api/sales.php

api/update_invoice_status.php

schema.sql

invoice related models

invoice related queries

==================================================

TEST CASES

TEST 1

Pending → Paid

PASS

---

TEST 2

Pending → Partial

PASS

---

TEST 3

Partial → Paid

PASS

---

TEST 4

Paid → Pending

PASS

---

TEST 5

Invalid invoice id

Proper JSON error

PASS

---

TEST 6

Staff login

Cannot update

PASS

---

TEST 7

Admin login

Can update

PASS

==================================================

PROOF REQUIRED

1. Files changed

2. Database column used

3. API endpoint created

4. AJAX implementation proof

5. Status dropdown screenshot

6. Status update query proof

7. Admin permission proof

8. Staff restriction proof

9. No console errors

10. No page refresh required

IMPORTANT

Do NOT modify invoice creation flow.

Do NOT change invoice design.

Do NOT add new pages.

Do NOT add modals.

Only add direct Payment Status update inside existing 3-dot action menu and update status instantly.
