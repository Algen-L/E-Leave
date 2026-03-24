# Leave Management System -- UI Design System

A complete **UI design system** for the Leave Management System.\
This guide ensures **visual consistency, usability, and professional UI
standards** across the entire web application.

Primary theme: **Royal Blue + Orange**

------------------------------------------------------------------------

# 1. Color System

## Primary (Royal Blue)

Used for: - Primary buttons - Navigation highlights - Links - Icons -
Active elements

  Token           Hex
  --------------- ---------
  primary         #2563EB
  primary-dark    #1E40AF
  primary-light   #60A5FA
  primary-soft    #EFF6FF

------------------------------------------------------------------------

## Accent (Orange)

Used for: - Call-to-action buttons - Important highlights - Attention
messages

  Token          Hex
  -------------- ---------
  accent         #F97316
  accent-dark    #EA580C
  accent-light   #FDBA74
  accent-soft    #FFF7ED

------------------------------------------------------------------------

## Neutral Colors

Used for layout structure.

  Token        Hex
  ------------ ---------
  background   #F8FAFC
  card         #FFFFFF
  border       #E5E7EB
  divider      #F1F5F9

------------------------------------------------------------------------

## Text Colors

  Token            Hex
  ---------------- ---------
  text-primary     #1E293B
  text-secondary   #64748B
  text-muted       #94A3B8

------------------------------------------------------------------------

## Status Colors

  Status    Hex
  --------- ---------
  success   #22C55E
  warning   #F59E0B
  error     #EF4444
  info      #0EA5E9

------------------------------------------------------------------------

# 2. Typography

Recommended font:

-   **Inter**
-   **Roboto**
-   **System UI fonts**

  Element         Size   Weight
  --------------- ------ --------
  Page Title      28px   700
  Section Title   18px   600
  Card Title      16px   600
  Body Text       14px   400
  Small Text      12px   400

------------------------------------------------------------------------

# 3. Spacing System

Consistent spacing ensures visual harmony.

  Size   Use
  ------ ----------------------
  4px    micro spacing
  8px    small gaps
  16px   form spacing
  24px   section spacing
  32px   container spacing
  48px   major layout spacing

------------------------------------------------------------------------

# 4. Cards

Cards contain main content.

Example:\
Leave Application sections, Leave Policies, Dashboard widgets.

### Card Style

    background: #FFFFFF
    border: 1px solid #E5E7EB
    border-radius: 12px
    padding: 24px
    box-shadow: 0 4px 12px rgba(0,0,0,0.05)

### Hover State

    box-shadow: 0 8px 20px rgba(0,0,0,0.08)
    border-color: #2563EB

------------------------------------------------------------------------

# 5. Containers

Containers wrap sections or multiple cards.

    background: #F8FAFC
    padding: 32px
    gap between cards: 16px

------------------------------------------------------------------------

# 6. Section Boxes

Used for grouped settings.

Example: - Accrual Settings - Expiration Rules - Approval Workflow

    background: #F9FAFB
    border: 1px solid #E5E7EB
    border-radius: 10px
    padding: 16px

------------------------------------------------------------------------

# 7. Buttons

## Primary Button

Used for main actions.

    background: #2563EB
    color: white
    padding: 10px 18px
    border-radius: 8px
    font-weight: 600

Hover:

    background: #1E40AF

------------------------------------------------------------------------

## Secondary Button

    border: 1px solid #2563EB
    color: #2563EB
    background: transparent

------------------------------------------------------------------------

## Accent Button

    background: #F97316
    color: white

Hover:

    background: #EA580C

------------------------------------------------------------------------

# 8. Form Inputs

Used for:

-   Leave type dropdown
-   Date selection
-   Text inputs

### Input Style

    border: 1px solid #E5E7EB
    border-radius: 8px
    padding: 10px 12px
    background: white

### Focus

    border-color: #2563EB
    outline: none
    box-shadow: 0 0 0 2px #EFF6FF

------------------------------------------------------------------------

# 9. Badges

Used for statuses like:

-   Configured
-   Approved
-   Pending

Example:

    padding: 4px 10px
    border-radius: 20px
    font-size: 12px
    font-weight: 500

### Success Badge

    background: #DCFCE7
    color: #166534

### Warning Badge

    background: #FEF3C7
    color: #92400E

### Error Badge

    background: #FEE2E2
    color: #991B1B

------------------------------------------------------------------------

# 10. Tables

Used for:

-   Leave history
-   Requests
-   Logs

```{=html}
<!-- -->
```
    background: white
    border: 1px solid #E5E7EB
    border-radius: 10px

### Header

    background: #F9FAFB
    font-weight: 600

### Row Hover

    background: #F1F5F9

------------------------------------------------------------------------

# 11. Navigation

Sidebar or top navigation.

    background: white
    border-right: 1px solid #E5E7EB

Active item:

    background: #EFF6FF
    color: #2563EB

------------------------------------------------------------------------

# 12. Approval Workflow UI

Display approval routing visually.

Example:

    Step 1 → Recommending Officer
    Step 2 → Final Approver

Design:

    icon circle
    step label
    officer name
    role

------------------------------------------------------------------------

# 13. Alerts

### Info

    background: #EFF6FF
    border: 1px solid #BFDBFE
    color: #1E40AF

### Warning

    background: #FFF7ED
    border: 1px solid #FDBA74
    color: #9A3412

### Error

    background: #FEE2E2
    border: 1px solid #FCA5A5
    color: #991B1B

------------------------------------------------------------------------

# 14. Dashboard Widgets

Example widgets:

-   Leave Balance
-   Pending Requests
-   Approved Leaves

Widget style:

    background: white
    border-radius: 12px
    padding: 20px
    box-shadow: 0 4px 12px rgba(0,0,0,0.05)

------------------------------------------------------------------------

# 15. UI Principles

1.  Keep interfaces **clean and minimal**
2.  Use **Royal Blue for primary actions**
3.  Use **Orange sparingly for highlights**
4.  Maintain consistent spacing
5.  Use cards for structure
6.  Ensure accessibility and readability

------------------------------------------------------------------------

# End of Design System
