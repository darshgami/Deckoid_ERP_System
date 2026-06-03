CSV IMPORT FOREIGN KEY ERROR – PERMANENT FIX MASTER PROMPT

PROJECT:
Deckoid ERP System

ISSUE:

CSV Import fails with:

SQLSTATE[23000]
Integrity constraint violation: 1452

Cannot add or update child row

FOREIGN KEY:
leads.created_by
REFERENCES users.id

ERROR:

Failed Row:
Row 2

Failed Row:
Row 3

Failed Row:
Row 4

...

All rows fail during import.

ROOT CAUSE ANALYSIS:

The leads table contains:

created_by

This column has a foreign key:

leads.created_by
→ users.id

During CSV import the system inserts:

created_by = 0

or

created_by = ''

or

created_by = NULL

or

created_by = invalid user id

That user id does not exist inside users table.

MySQL correctly rejects every insert.

REQUIRED FIX:

Perform complete end-to-end investigation.

Check:

database/schema.sql

api/import_csv.php

api/leads.php

all lead creation APIs

all onboarding APIs

all followup APIs

all CSV import logic

all CSV export logic

Verify created_by handling everywhere.

DATABASE TASKS:

Check users table.

Check leads table.

Check foreign key:

leads_ibfk_2

Verify:

created_by references users.id

If foreign key exists:

Ensure imported rows use a valid user id.

BACKEND TASKS:

Find every insert into:

leads

followups

onboarding

customer_onboarding

Replace any hardcoded value:

created_by = 0

created_by = ''

created_by = NULL

created_by = csv value

with:

logged-in user id

Example:

$_SESSION['user_id']

or authenticated user id.

Never use invalid ids.

CSV IMPORT TASKS:

When importing CSV:

Automatically assign:

created_by = current logged-in user

Do NOT expect created_by column from CSV.

Do NOT require created_by in CSV.

Do NOT map created_by from uploaded file.

Import must work even if CSV has no created_by field.

VALIDATION:

Before insert:

Verify user exists.

Example:

SELECT id
FROM users
WHERE id = current_user

If user not found:

Return JSON:

{
"success": false,
"message": "Invalid user account."
}

Do not continue.

ERROR HANDLING:

Never return:

HTML

PHP warnings

PHP notices

Fatal error output

Always return JSON.

Example:

{
"success": false,
"message": "Database insert failed.",
"row": 15,
"reason": "Invalid created_by reference."
}

CSV IMPORT SUCCESS:

Example:

{
"success": true,
"message": "Imported 127 records successfully."
}

TESTS REQUIRED:

TEST 1

Check users table.

Result:
Valid user exists.

====================================================

TEST 2

Import CSV with 100 records.

Result:
100 inserted.

====================================================

TEST 3

Import CSV with duplicate records.

Result:
All inserted.

====================================================

TEST 4

Verify created_by.

Result:
Every imported row contains valid user id.

====================================================

TEST 5

Open Lead List.

Result:
Imported records visible.

====================================================

TEST 6

Open Followups.

Result:
No errors.

====================================================

TEST 7

Open Onboarding.

Result:
No errors.

====================================================

TEST 8

Export CSV.

Result:
Records exported successfully.

====================================================

PROOF REQUIRED:

1. Foreign key definition found.

2. Source of invalid created_by identified.

3. Fixed import_csv.php.

4. Fixed leads insert API.

5. Fixed onboarding insert API.

6. Fixed followup insert API.

7. Validation added.

8. JSON response proof.

9. Successful CSV import proof.

10. No SQLSTATE[23000] errors.

IMPORTANT:

Do NOT remove foreign key.

Do NOT disable foreign key checks.

Do NOT use SET FOREIGN_KEY_CHECKS=0.

Fix correctly using valid users.id values.

Implement permanently in:

Database
Backend
Frontend
CSV Import
CSV Export
Schema

No temporary workaround.
No silent failures.
No skipped rows.
No broken references.
