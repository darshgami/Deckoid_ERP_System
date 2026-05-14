You are a senior SaaS frontend architect and backend engineer.

Add a production-grade reusable pagination system across the ENTIRE project.

IMPORTANT:

* Do NOT break existing functionality
* Do NOT hardcode values
* Use reusable architecture
* Use scalable enterprise patterns

---

## GLOBAL REQUIREMENTS

Add pagination to ALL:

* tables
* list views
* admin pages
* dashboard modules
* data grids

Examples:

* Projects
* Tasks
* Team Members
* KPI Management
* Leave Requests
* Users
* Reports
* Notifications
* Activity Logs

---

## FRONTEND REQUIREMENTS

Create ONE reusable pagination component.

Example:
components/common/TablePagination

The component must support:

* current page
* total pages
* total items
* rows per page
* next/previous
* responsive layout

Rows per page options:
10
25
50
100

Default:
10

---

## UI/UX REQUIREMENTS

The pagination must:

* look modern SaaS-level
* compact and clean
* responsive
* accessible
* keyboard-friendly

Desktop:

* show page numbers
* show next/prev
* show total item count

Mobile:

* compact mode
* touch-friendly

Use:

* subtle borders
* consistent spacing
* modern hover states
* no oversized controls

---

## BACKEND REQUIREMENTS

Implement SERVER-SIDE pagination.

Do NOT use frontend-only slicing.

Use:

* page
* limit
* offset

Support:

* search
* filters
* sorting

API Example:
GET /api/projects?page=1&limit=10

Response Example:
{
"data": [],
"pagination": {
"page": 1,
"limit": 10,
"totalItems": 250,
"totalPages": 25
}
}

---

## DATABASE REQUIREMENTS

Use optimized SQL queries.

Example:
LIMIT and OFFSET

Add proper indexes if needed.

Avoid loading unnecessary rows.

---

## TABLE IMPROVEMENTS

Improve all tables:

* reduce oversized row heights
* improve alignment
* smaller typography
* proper overflow handling
* sticky headers
* responsive layout

Use:
font-size: 13px

---

## FINAL REQUIREMENTS

Apply pagination consistently across the ENTIRE project.

Ensure:

* scalable architecture
* reusable components
* consistent UI
* mobile responsiveness
* high performance
* SaaS-level polish

The final experience should feel similar to:

* Linear
* Jira
* ClickUp
* Notion
* Asana
