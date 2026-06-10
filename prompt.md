@agency-project-auditor
@agency-frontend-engineer
@agency-backend-engineer
@agency-database-engineer
@agency-qa-engineer

TASK: REMOVE BROWSER AUTOFILL / AUTOCOMPLETE SUGGESTIONS FROM ALL FORMS

PROJECT:
Deckoid ERP System

PROBLEM:

Input fields show browser suggestions such as:

* Previous emails
* Previous names
* Previous mobile numbers
* Previous company names
* Stored browser autofill values

Example:

[abc@gmail.com](mailto:abc@gmail.com)
[abc1@gmail.com](mailto:abc1@gmail.com)
AAA@

These suggestions appear when user clicks or types in form fields.

REQUIREMENT:

Remove these suggestions from ALL forms across the entire project.

DO NOT modify business logic.

DO NOT modify database.

DO NOT modify APIs.

ONLY remove browser autofill/autocomplete behavior.

====================================================

AUDIT ENTIRE PROJECT

Search all files for:

input

textarea

select

form

autocomplete

autofill

email fields

mobile fields

name fields

search fields

company fields

login fields

lead forms

followup forms

onboarding forms

staff forms

profile forms

====================================================

FRONTEND FIX

For every form:

Add:

autocomplete="off"

Example:

<form autocomplete="off">

For sensitive fields:

autocomplete="new-password"

Example:

<input
type="text"
autocomplete="off">

<input
type="email"
autocomplete="off">

<input
type="tel"
autocomplete="off">

<textarea
autocomplete="off"></textarea>

====================================================

CHROME AUTOFILL FIX

Where browser still ignores autocomplete:

Apply:

autocomplete="new-password"

or

autocomplete="nope"

and unique field names if required.

Example:

<input
name="company_input_unique"
autocomplete="off">

====================================================

CSS AUTOFILL RESET

Add project-wide autofill styling fix.

Prevent yellow autofill background.

Prevent autofill visual artifacts.

====================================================

FILES TO CHECK

admin/add_lead.php

admin/leads.php

admin/followups.php

admin/onboarding.php

admin/staff_management.php

admin/profile.php

admin/login.php

all modal forms

all edit forms

all search forms

all import/export forms

all dynamically generated forms

====================================================

VALIDATION

After fix:

Click Company

No suggestions

PASS

---

Click Email

No suggestions

PASS

---

Click Mobile

No suggestions

PASS

---

Click Contact Person

No suggestions

PASS

---

Click Search

No previous values

PASS

---

Lead Form

PASS

---

Followup Form

PASS

---

Onboarding Form

PASS

---

Staff Form

PASS

====================================================

PROOF REQUIRED

1. List of modified files

2. Forms updated

3. Inputs updated

4. Autocomplete disabled

5. Autofill disabled

6. No console errors

7. No functionality broken

8. Frontend tested

9. Production tested

10. Local tested

IMPORTANT:

Do not change database.

Do not change APIs.

Do not change validations.

Do not add features.

Only remove browser autofill/autocomplete suggestions project-wide.
