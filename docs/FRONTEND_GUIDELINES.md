FRONTEND_GUIDELINES.md
Lead Management ERP System — Refined Modern SaaS ERP Design System

UI DESIGN DIRECTION
The ERP UI must follow a modern SaaS dashboard style similar to the provided reference UI:


Soft minimal interface


Spacious layout


White card-based containers


Purple accent theme


Light gray application background


Rounded modern components


Business-focused UX


Fast operational workflow


Premium but realistic ERP appearance


The interface must NOT feel:


AI-generated


Over-designed


Over-animated


Gaming/dashboard flashy


Glassmorphism-heavy


The design must prioritize:


Productivity


Data visibility


Form efficiency


ERP readability


Mobile responsiveness



1. DESIGN PRINCIPLES

1.1 Simplicity First
The interface must prioritize operational speed over visual decoration.
Rules


Avoid unnecessary graphics


Minimize clicks


Predictable layouts


Consistent navigation


Use whitespace intentionally


Keep actions obvious



1.2 Data Readability
Lead information must remain easy to scan and process.
Rules


Strong typography hierarchy


High contrast text


Clear table spacing


Structured form grouping


Consistent row heights


Minimal visual noise



1.3 Workflow Efficiency
Users should complete business tasks rapidly.
Rules


Large clickable targets


Sticky save actions


Inline validation


Quick access navigation


Search-first workflows


Fast-loading UI



1.4 Modern SaaS Professionalism
The ERP should feel like premium modern business software.
Rules


Rounded modern cards


Soft shadows


Minimal borders


Spacious layouts


Consistent card hierarchy


Clean sidebar navigation



1.5 Consistency
All components must behave identically across screens.
Rules


Unified spacing system


Same focus states


Same hover behavior


Same button hierarchy


Same typography scale


Same radius system



2. DESIGN TOKENS

2.1 COLOR SYSTEM

Primary Purple Scale
Primary-50:  #F3F0FF;Primary-100: #E9E2FF;Primary-200: #D6CAFF;Primary-300: #B9A5FF;Primary-400: #9B7DFF;Primary-500: #7C5CFC;Primary-600: #6D5DFC;Primary-700: #5B4AE6;Primary-800: #4C3BC7;Primary-900: #3D2FA1;
Usage
ColorUsagePrimary-500Primary buttonsPrimary-600Active sidebarPrimary-100Selected statesPrimary-50Light backgroundsPrimary-700Hover states

Neutral Scale
Neutral-50:  #F8FAFC;Neutral-100: #F1F5F9;Neutral-200: #E2E8F0;Neutral-300: #CBD5E1;Neutral-400: #94A3B8;Neutral-500: #64748B;Neutral-600: #475569;Neutral-700: #334155;Neutral-800: #1E293B;Neutral-900: #0F172A;

Semantic Colors
Success: #16A34A;Warning: #D97706;Error:   #DC2626;Info:    #0284C7;

Background Colors
App Background:      #F5F7FB;Sidebar Background:  #FFFFFF;Card Background:     #FFFFFF;Input Background:    #FFFFFF;Table Hover:         #F8FAFC;

Border Colors
Border Light:  #E2E8F0;Border Medium: #CBD5E1;

Text Colors
Heading Text: #111827;Body Text:    #334155;Muted Text:   #64748B;Light Text:   #94A3B8;

2.2 TYPOGRAPHY

Font Families
Primary Font:"Inter", sans-serif;Monospace:"JetBrains Mono", monospace;

Font Size Scale
TokenSizexs0.75remsm0.875rembase1remlg1.125remxl1.25rem2xl1.5rem3xl1.875rem4xl2.25rem

Font Weights
Regular:   400;Medium:    500;Semibold:  600;Bold:      700;

Line Heights
tight:   1.25;normal:  1.5;relaxed: 1.75;

2.3 SPACING SYSTEM

TokenValue00px14px28px312px416px520px624px728px832px1040px1248px1456px1664px

Recommended ERP Spacing
AreaRecommendedCard Padding24pxSection Gap32pxForm Input Gap20pxTable Row Height56pxSidebar Item Height48px

2.4 BORDER RADIUS
sm:   0.375rem;md:   0.5rem;lg:   0.75rem;xl:   1rem;2xl:  1.25rem;3xl:  1.5rem;full: 9999px;

2.5 SHADOW SYSTEM
Shadow-1:0 1px 2px rgba(15, 23, 42, 0.04);Shadow-2:0 4px 10px rgba(15, 23, 42, 0.06);Shadow-3:0 8px 20px rgba(15, 23, 42, 0.08);Shadow-4:0 12px 30px rgba(15, 23, 42, 0.10);Shadow-5:0 20px 40px rgba(15, 23, 42, 0.12);

3. LAYOUT SYSTEM

Main ERP Layout
<div class="flex min-h-screen bg-[#F5F7FB]">  <!-- Sidebar -->  <aside class="hidden lg:flex w-[280px] flex-col bg-white border-r border-slate-200">  </aside>  <!-- Main Area -->  <div class="flex-1 flex flex-col">    <!-- Navbar -->    <header class="h-[80px] border-b border-slate-200 bg-white">    </header>    <!-- Content -->    <main class="flex-1 p-6 lg:p-8">    </main>  </div></div>

Container Width
Max Width: 1440px;

Grid System
Columns: 12;Gap: 24px;

Dashboard Layout
<div class="grid grid-cols-12 gap-6">  <div class="col-span-12 xl:col-span-8">    Main Content  </div>  <div class="col-span-12 xl:col-span-4">    Sidebar Widgets  </div></div>

4. SIDEBAR DESIGN

Sidebar Rules


White background


Soft separators


Rounded active item


Icon + label alignment


Minimal shadows


Sticky sidebar



Sidebar Item Style
<aclass="flex items-center gap-3rounded-2xlpx-4 py-3text-sm font-mediumtext-slate-600transition-all duration-200hover:bg-violet-50hover:text-violet-600"></a>

Active Sidebar Item
<aclass="bg-violet-100text-violet-700shadow-sm"></a>

5. NAVBAR DESIGN

Navbar Structure
Required:


Search bar


Notification icon


User profile


Mobile menu toggle



Navbar Style
<headerclass="sticky top-0 z-30flex items-center justify-betweenbg-whitepx-6h-20border-b border-slate-200"></header>

Search Bar Style
<divclass="flex items-centerw-full max-w-xlrounded-2xlborder border-slate-200bg-slate-50px-4 h-12"></div>

6. CARD SYSTEM

Default Card
<divclass="rounded-3xlbg-whiteborder border-slate-200shadow-smp-6"></div>

Statistics Card
<divclass="rounded-3xlbg-whitep-6shadow-smborder border-slate-200hover:shadow-mdtransition-all duration-200"></div>

Hero Card (Purple Dashboard Card)
<divclass="rounded-3xlbg-gradient-to-r from-violet-600 to-indigo-500p-8text-whiteshadow-lg"></div>

7. TABLE DESIGN SYSTEM

ERP Table Rules


Large readable rows


Sticky header


Soft hover


Minimal borders


Rounded container



Table Container
<divclass="overflow-hiddenrounded-3xlborder border-slate-200bg-whiteshadow-sm"></div>

Table Row
<trclass="border-b border-slate-100hover:bg-slate-50transition-colors"></tr>

Table Cell
<td class="px-6 py-4 text-sm text-slate-700"></td>

8. FORM DESIGN SYSTEM

Form Container
<divclass="rounded-3xlbg-whiteborder border-slate-200shadow-smp-8"></div>

Input Style
<inputclass="w-fullh-12rounded-2xlborder border-slate-200bg-whitepx-4text-smtext-slate-800placeholder:text-slate-400transition-all duration-200focus:border-violet-500focus:ring-4focus:ring-violet-100focus:outline-none"/>

Select Dropdown Style
<selectclass="w-fullh-12rounded-2xlborder border-slate-200bg-whitepx-4text-smfocus:border-violet-500focus:ring-4focus:ring-violet-100"></select>

Form Section Card
<divclass="rounded-2xlborder border-slate-200bg-slate-50p-6"></div>

Sticky Action Footer
<divclass="sticky bottom-0bg-white/90backdrop-blur-smborder-t border-slate-200p-4flex justify-end gap-4"></div>

9. BUTTON SYSTEM

Primary Button
<buttonclass="inline-flex items-center justify-centerh-12rounded-2xlbg-violet-600px-6text-sm font-mediumtext-whitetransition-all duration-200hover:bg-violet-700focus:ring-4focus:ring-violet-200">Save Lead</button>

Secondary Button
<buttonclass="inline-flex items-center justify-centerh-12rounded-2xlborder border-slate-200bg-whitepx-6text-sm font-mediumtext-slate-700hover:bg-slate-50">Cancel</button>

Danger Button
<buttonclass="inline-flex items-center justify-centerh-12rounded-2xlbg-red-600px-6text-sm font-mediumtext-whitehover:bg-red-700">Delete</button>

10. RESPONSIVE STRATEGY

Breakpoints
sm: 640px;md: 768px;lg: 1024px;xl: 1280px;2xl: 1536px;

Mobile Behavior
ComponentBehaviorSidebarOffcanvasTablesHorizontal scrollFormsSingle columnCardsStackedNavbarCompact

Tablet Behavior
ComponentBehaviorSidebarCollapsibleDashboard2-columnFormsMixed grid

Desktop Behavior
ComponentBehaviorSidebarFixedDashboardFull gridTablesFull-width

11. ACCESSIBILITY

Requirements


WCAG 2.1 AA


Keyboard navigation


Visible focus states


Proper labels


Accessible modals


High contrast ratios



Focus Ring
focus:ring-4focus:ring-violet-100

Keyboard Rules
KeyActionTABNavigateESCClose modalENTERSubmitSPACEActivate button

12. ANIMATION SYSTEM

Animation Philosophy
Animations must feel:


Fast


Minimal


Professional


Non-distracting



Durations
Fast:   150ms;Normal: 200ms;Slow:   300ms;

Allowed Animations
AnimationUsageFadeToastsElevationCard hoverScaleModal openPulseSkeleton loading

Avoid
Do NOT animate:


Tables


Layout shifts


Form typing


Sidebar resize aggressively



13. FINAL UI DIRECTION
The ERP must feel:
✅ Professional
✅ Modern SaaS-like
✅ Fast operationally
✅ Spacious and clean
✅ Easy for office staff
✅ Business-oriented
✅ Premium but realistic
✅ Mobile responsive
✅ Data-focused

STRICTLY AVOID
❌ Heavy gradients
❌ Neon colors
❌ Glassmorphism
❌ Over-animation
❌ Oversized UI
❌ Gaming dashboard style
❌ AI-generated aesthetics
❌ Crowded layouts
❌ Too many accent colors