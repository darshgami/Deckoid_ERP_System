FRONTEND_GUIDELINES.md
Lead Management ERP System Design System
1. Design Principles
1. Simplicity First

The interface must prioritize fast business operations over visual complexity.

Rules:

Avoid decorative UI
Minimize clicks
Use clear hierarchy
Keep layouts predictable
2. Data Readability

Lead information must always remain easy to scan.

Rules:

High contrast text
Consistent spacing
Structured tables
Clear form grouping
3. Speed & Efficiency

Users should complete tasks quickly.

Rules:

Large clickable targets
Inline validation
Minimal animations
Sticky actions where needed
4. Responsive Professionalism

The ERP must work reliably across desktop and mobile.

Rules:

Mobile-first responsiveness
No layout shifting
Stable component sizing
5. Consistency

All components must behave consistently.

Rules:

Same spacing system
Same button hierarchy
Same interaction states
Same typography scale
2. Design Tokens
Color System
Primary Color Scale
Primary-50:  #eff6ff
Primary-100: #dbeafe
Primary-200: #bfdbfe
Primary-300: #93c5fd
Primary-400: #60a5fa
Primary-500: #3b82f6
Primary-600: #2563eb
Primary-700: #1d4ed8
Primary-800: #1e40af
Primary-900: #1e3a8a

Usage:

Buttons
Active states
Links
Navigation highlights
Neutral Color Scale
Neutral-50:  #f9fafb
Neutral-100: #f3f4f6
Neutral-200: #e5e7eb
Neutral-300: #d1d5db
Neutral-400: #9ca3af
Neutral-500: #6b7280
Neutral-600: #4b5563
Neutral-700: #374151
Neutral-800: #1f2937
Neutral-900: #111827

Usage:

Backgrounds
Borders
Body text
Table rows
Semantic Colors
Success: #16a34a
Warning: #d97706
Error:   #dc2626
Info:    #0284c7

Usage Rules:

Color	Usage
Success	Save confirmation
Warning	Pending actions
Error	Validation/system errors
Info	Notifications
Typography
Font Families
Primary Font:
"Inter", sans-serif

Monospace:
"JetBrains Mono", monospace
Font Size Scale
xs:  0.75rem   (12px)
sm:  0.875rem  (14px)
base:1rem      (16px)
lg:  1.125rem  (18px)
xl:  1.25rem   (20px)
2xl: 1.5rem    (24px)
3xl: 1.875rem  (30px)
4xl: 2.25rem   (36px)
Font Weights
Regular:   400
Medium:    500
Semibold:  600
Bold:      700
Line Heights
tight:   1.25
normal:  1.5
relaxed: 1.75
Spacing Scale
0  = 0px
1  = 4px
2  = 8px
3  = 12px
4  = 16px
5  = 20px
6  = 24px
7  = 28px
8  = 32px
9  = 36px
10 = 40px
11 = 44px
12 = 48px
13 = 52px
14 = 56px
15 = 60px
16 = 64px
Border Radius
none: 0rem
sm:   0.125rem
md:   0.375rem
lg:   0.5rem
xl:   0.75rem
2xl:  1rem
full: 9999px
Shadow System
Shadow-1:
0 1px 2px rgba(0,0,0,0.05)

Shadow-2:
0 2px 6px rgba(0,0,0,0.08)

Shadow-3:
0 4px 12px rgba(0,0,0,0.10)

Shadow-4:
0 8px 20px rgba(0,0,0,0.12)

Shadow-5:
0 12px 32px rgba(0,0,0,0.15)
3. Component Library
Button Component
Primary Button
<button
className="
inline-flex items-center justify-center
rounded-lg
bg-blue-600
px-4 py-2
text-sm font-medium text-white
shadow-sm
transition-all duration-200
hover:bg-blue-700
focus:outline-none
focus:ring-2 focus:ring-blue-500 focus:ring-offset-2
disabled:cursor-not-allowed
disabled:bg-blue-300
"
>
Save Lead
</button>
Secondary Button
<button
className="
inline-flex items-center justify-center
rounded-lg
border border-gray-300
bg-white
px-4 py-2
text-sm font-medium text-gray-700
shadow-sm
transition-all duration-200
hover:bg-gray-50
focus:outline-none
focus:ring-2 focus:ring-blue-500 focus:ring-offset-2
disabled:cursor-not-allowed
disabled:opacity-50
"
>
Cancel
</button>
Danger Button
<button
className="
inline-flex items-center justify-center
rounded-lg
bg-red-600
px-4 py-2
text-sm font-medium text-white
transition-all duration-200
hover:bg-red-700
focus:outline-none
focus:ring-2 focus:ring-red-500 focus:ring-offset-2
disabled:cursor-not-allowed
disabled:bg-red-300
"
>
Delete
</button>
Button Sizes
Size	Classes
sm	px-3 py-1.5 text-xs
md	px-4 py-2 text-sm
lg	px-5 py-3 text-base
Loading Button
<button
disabled
className="
inline-flex items-center justify-center
rounded-lg
bg-blue-400
px-4 py-2
text-sm font-medium text-white
cursor-not-allowed
"
>
<svg className="mr-2 h-4 w-4 animate-spin" />
Saving...
</button>
Usage Rules
Variant	Usage
Primary	Main actions
Secondary	Optional actions
Danger	Destructive actions
Accessibility Requirements
Must support keyboard focus
Minimum 44px height
Visible focus ring
Proper button labels
Input Component
Default Input
<input
className="
w-full
rounded-lg
border border-gray-300
bg-white
px-4 py-2
text-sm text-gray-900
shadow-sm
transition-all duration-200
placeholder:text-gray-400
focus:border-blue-500
focus:outline-none
focus:ring-2 focus:ring-blue-500
disabled:bg-gray-100
disabled:cursor-not-allowed
"
/>
Error Input
<input
className="
w-full
rounded-lg
border border-red-500
bg-white
px-4 py-2
text-sm text-gray-900
focus:outline-none
focus:ring-2 focus:ring-red-500
"
/>
Input States
State	Behavior
Default	Neutral border
Hover	Slight border darkening
Focus	Blue ring
Error	Red border
Disabled	Gray background
Accessibility
Associate labels with inputs
Required fields use aria-required="true"
Error fields use aria-invalid="true"
Card Component
<div
className="
rounded-2xl
border border-gray-200
bg-white
p-6
shadow-sm
transition-all duration-200
hover:shadow-md
"
>
Card Content
</div>
Usage Rules

Use cards for:

Dashboard widgets
Forms
Statistics
Lead detail sections
Modal Component
<div
className="
fixed inset-0 z-50
flex items-center justify-center
bg-black/50
p-4
"
>
<div
className="
w-full max-w-lg
rounded-2xl
bg-white
p-6
shadow-2xl
"
>
Modal Content
</div>
</div>
Modal Accessibility

Required:

role="dialog"
aria-modal="true"
ESC closes modal
Focus trapped inside modal
Alert / Toast Component
Success Toast
<div
className="
flex items-center
rounded-lg
border border-green-200
bg-green-50
px-4 py-3
text-sm text-green-800
shadow-sm
"
>
Lead saved successfully.
</div>
Error Toast
<div
className="
flex items-center
rounded-lg
border border-red-200
bg-red-50
px-4 py-3
text-sm text-red-800
shadow-sm
"
>
Unable to save lead.
</div>
Loading States
Table Loading
<div className="animate-pulse space-y-3">
  <div className="h-10 rounded bg-gray-200"></div>
  <div className="h-10 rounded bg-gray-200"></div>
  <div className="h-10 rounded bg-gray-200"></div>
</div>
Spinner Loading
<div
className="
h-6 w-6
animate-spin
rounded-full
border-4 border-blue-500
border-t-transparent
"
></div>
Empty States
<div
className="
flex flex-col items-center justify-center
rounded-2xl
border border-dashed border-gray-300
bg-gray-50
p-10
text-center
"
>
<h3 className="text-lg font-semibold text-gray-900">
No Leads Found
</h3>

<p className="mt-2 text-sm text-gray-500">
Try changing filters or add a new lead.
</p>
</div>
4. Layout System
Container Widths
sm: 640px
md: 768px
lg: 1024px
xl: 1280px
2xl: 1536px
Responsive Breakpoints
sm: 640px
md: 768px
lg: 1024px
xl: 1280px
2xl: 1536px
Grid System
Columns: 12
Gutter: 24px
Max Width: 1280px
Centered Layout
<div className="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
Content
</div>
Two Column Layout
<div className="grid grid-cols-1 gap-6 lg:grid-cols-2">
  <div>Left</div>
  <div>Right</div>
</div>
Sidebar Layout
<div className="flex min-h-screen">
  
  <aside
  className="
  hidden w-64 border-r border-gray-200 bg-white lg:block
  "
  >
  Sidebar
  </aside>

  <main className="flex-1 bg-gray-50 p-6">
  Main Content
  </main>

</div>
5. Accessibility
WCAG 2.1 AA Requirements

Required:

Contrast ratio minimum 4.5:1
Keyboard accessibility
Screen-reader labels
Focus indicators visible
Error messages readable
Focus Indicators
focus:ring-2
focus:ring-blue-500
focus:ring-offset-2
Keyboard Navigation

Required:

TAB navigation
ESC closes modal
ENTER submits forms
SPACE activates buttons
Form Accessibility

Required:

Labels linked via for
Error messages linked using aria-describedby
6. Animation System
Duration Scale
Fast:   150ms
Normal: 200ms
Slow:   300ms
Easing
ease-in-out
Allowed Animations
Animation	Usage
Fade	Toasts
Scale	Modals
Pulse	Loading skeleton
Hover elevation	Cards
Avoid

Do NOT animate:

Large layout shifts
Table rendering
Form typing
Reduced Motion
@media (prefers-reduced-motion: reduce)

Rules:

Disable non-essential animations
Remove transitions >100ms
Final UI Direction

The Lead Management ERP UI must feel:

Professional
Fast
Lightweight
Real business-oriented
Easy for office staff
Minimal learning curve
Consistent across all screens

Avoid:

Fancy gradients
Over-animation
Glassmorphism
AI-generated aesthetics
Oversized components