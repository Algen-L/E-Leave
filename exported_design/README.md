# Extracted LDP Dashboard Components

This directory contains the core responsive UI components and styling required to port the LDPVER2 dashboard design over to your new system. Let's get started.

## Directory Structure

* **`css/`**
  * `variables.css`: The master root file containing all color palettes, layout tokens, shadows, sizing, and fonts.
  * `layout.css`: Styles for main container structures (`.app-layout`, `.main-content`, headers, cards).
  * `sidebar.css`: Styles specific to the sidebar, mobile toggle, navigation tabs, typography, and logout button.
  * `components.css`: Styles for smaller widgets like the `current-date-box` and custom `filter dropdown`.
* **`js/`**
  * `ui-core.js`: Single Javascript file handling the Real-Time Clock, Sidebar animations/collapse state, and Filter dropdown interaction.
* **`components/`**
  * Raw HTML snippets that you can copy to your new system's View or Template Engine layout files.
* **`index.html`**
  * A live working preview demonstrating all components running in tandem. 

---

## Integration Guide

### 1. Requirements (Head Tags)
Ensure your new system's HTML `<head>` tag includes Google Fonts and Bootstrap Icons for the typography and iconography to function correctly:

```html
<!-- Google Fonts -->
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">

<!-- Bootstrap Icons -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
```

### 2. Linking the CSS and JS
Drop the `css` and `js` folders into your new system's `public` or `assets` directory. Then link them in your global layout/head file. Make sure they cascade in this correct order so layout values don't get overwritten:

```html
<link rel="stylesheet" href="path/to/css/variables.css">
<link rel="stylesheet" href="path/to/css/layout.css">
<link rel="stylesheet" href="path/to/css/sidebar.css">
<link rel="stylesheet" href="path/to/css/components.css">

<script src="path/to/js/ui-core.js"></script>
```

### 3. Setting Up the Base Layout Skeleton
Your main layout view (e.g., `layout.blade.php`, `App.js`, `base.html`) must use this exact wrapper structure so the CSS can seamlessly shift the page left/right during side menu toggles.

```html
<body>
    <div class="app-layout">
        <!-- 1. Include Sidebar Component Here -->
        <!-- (Copy from components/sidebar.html) -->
        
        <div class="main-content">
            <!-- 2. Include Header Component Here -->
            <!-- (Copy from components/header.html and filter.html) -->
            
            <div class="content-wrapper">
                <!-- 3. Your Dynamic Page Content Goes Here -->
            </div>
        </div>
    </div>
</body>
```

### 4. Component Details
- **Sidebar & Navigation Tab**:
  Notice in `components/sidebar.html`, the `.nav-item` class defines an unselected tab. When a tab is clicked/active, add the `.active` class to trigger the beautiful gradient border, light bleed effect, and indentation.
- **Header Date Format**:
  The real-time clock runs automatically off `ui-core.js`. Make sure the `<span>` with the ID `real-time-clock` remains unchanged. You will pass the raw date (Month, Day, Year) via PHP/backend into the `#current-date` span.
- **Filter**:
  The filter behaves entirely via `ui-core.js` by swapping active classes on the dropdown elements. When an element is clicked, JS populates a hidden input (`#filterInput`) with the raw data-value (`today`, `week`, `custom`). 

If building an SPA (Single Page Application), modify the Event Listeners in `js/ui-core.js` accordingly to suit your framework (React/Vue).
