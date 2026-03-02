# Mega Menu CSS Improvement Prompt — Light / White Panel

Use the following as your prompt to paste into your WordPress mega menu plugin's CSS / custom styling panel. Read the full prompt carefully and apply every rule as instructed.

---

## PROMPT TO IMPROVE THE MEGA MENU (LIGHT THEME)

```
You are improving the CSS of an existing WordPress mega menu plugin. Apply the following complete CSS rules to achieve a clean white/light mega menu with an orange accent brand colour. Do NOT remove any existing structural rules from the plugin — only add or override the visual/typographic properties listed below. Match every selector to the plugin's actual class names (swap the placeholder class names shown here with your plugin's real class names).

─────────────────────────────────────────────
DESIGN SYSTEM VARIABLES (add to :root)
─────────────────────────────────────────────

:root {
  --mm-brand-orange:       #E8500A;
  --mm-brand-orange-light: #FF6B2B;
  --mm-brand-orange-bg:    #FFF3EE;
  --mm-bg-panel:           #FFFFFF;
  --mm-bg-col-alt:         #F9FAFB;
  --mm-col-border:         #E4E7EB;
  --mm-divider:            #E4E7EB;
  --mm-l1-color:           #1A1A1A;
  --mm-l2-color:           #374151;
  --mm-l3-color:           #6B7280;
  --mm-l4-color:           #E8500A;
  --mm-footer-bg:          #F3F4F6;
  --mm-font-heading:       'Barlow Condensed', sans-serif;
  --mm-font-body:          'Barlow', sans-serif;
}


─────────────────────────────────────────────
GOOGLE FONTS IMPORT (add to <head> or enqueue)
─────────────────────────────────────────────

@import url('https://fonts.googleapis.com/css2?family=Barlow+Condensed:wght@400;600;700;800&family=Barlow:wght@400;500;600&display=swap');


─────────────────────────────────────────────
MEGA MENU PANEL / DROPDOWN WRAPPER
─────────────────────────────────────────────

Selector: .mega-menu-panel  (or your plugin's dropdown container class)

  background-color:   var(--mm-bg-panel);
  border:             1px solid var(--mm-col-border);
  border-top:         none;
  box-shadow:         0 16px 48px rgba(0, 0, 0, 0.12);
  border-radius:      0 0 6px 6px;
  overflow:           hidden;
  width:              100%;
  max-width:          1400px;

  /* Grid of 5 equal columns */
  display:            grid;
  grid-template-columns: repeat(5, 1fr);
  align-items:        start;


─────────────────────────────────────────────
PANEL HEADER STRIP (top accent bar)
─────────────────────────────────────────────

Selector: .mega-menu-panel-header

  background-color:   var(--mm-brand-orange);
  padding:            8px 24px;
  display:            flex;
  align-items:        center;
  gap:                10px;
  font-family:        var(--mm-font-heading);
  font-weight:        800;
  font-size:          13px;
  letter-spacing:     2px;
  text-transform:     uppercase;
  color:              #ffffff;


─────────────────────────────────────────────
EACH COLUMN
─────────────────────────────────────────────

Selector: .mega-menu-column

  padding:            20px 18px 24px;
  border-right:       1px solid var(--mm-col-border);
  vertical-align:     top;
  background-color:   var(--mm-bg-panel);

/* Alternate even columns with light grey bg */
.mega-menu-column:nth-child(even) {
  background-color:   var(--mm-bg-col-alt);
}
.mega-menu-column:last-child {
  border-right:       none;
}


─────────────────────────────────────────────
LEVEL 1 — COLUMN HEADING
─────────────────────────────────────────────

Selector: .menu-item-l1 > a

  font-family:        var(--mm-font-heading);
  font-weight:        800;
  font-size:          13px;
  letter-spacing:     1.5px;
  text-transform:     uppercase;
  color:              var(--mm-l1-color);
  display:            flex;
  align-items:        center;
  gap:                7px;
  padding:            6px 0 7px;
  border-bottom:      2px solid var(--mm-brand-orange);
  margin-bottom:      10px;
  cursor:             pointer;
  text-decoration:    none;
  transition:         color 0.2s ease;

/* Orange left-bar accent */
.menu-item-l1 > a::before {
  content:            '';
  display:            inline-block;
  width:              4px;
  height:             16px;
  background-color:   var(--mm-brand-orange);
  border-radius:      2px;
  flex-shrink:        0;
}

.menu-item-l1 > a:hover {
  color:              var(--mm-brand-orange);
  text-decoration:    none;
}

/* Separator when two L1 blocks share the same column */
.menu-item-l1 + .menu-item-l1 {
  margin-top:         20px;
  padding-top:        20px;
  border-top:         1px solid var(--mm-divider);
}


─────────────────────────────────────────────
LEVEL 2 — SECTION HEADING
─────────────────────────────────────────────

Selector: .menu-item-l2 > a

  font-family:        var(--mm-font-heading);
  font-weight:        700;
  font-size:          12px;
  letter-spacing:     0.6px;
  text-transform:     uppercase;
  color:              var(--mm-l2-color);
  padding:            8px 0 3px;
  margin-top:         4px;
  display:            flex;
  align-items:        center;
  gap:                5px;
  cursor:             pointer;
  text-decoration:    none;
  transition:         color 0.2s ease;

.menu-item-l2 > a::before {
  content:            '▸';
  font-size:          9px;
  color:              var(--mm-brand-orange);
  flex-shrink:        0;
}

.menu-item-l2 > a:hover {
  color:              #1A1A1A;
  text-decoration:    none;
}


─────────────────────────────────────────────
LEVEL 3 — REGULAR MENU ITEM
─────────────────────────────────────────────

Selector: .menu-item-l3 > a

  font-family:        var(--mm-font-body);
  font-size:          12px;
  font-weight:        400;
  line-height:        1.4;
  color:              var(--mm-l3-color);
  padding:            3px 0 3px 13px;
  display:            flex;
  align-items:        flex-start;
  gap:                5px;
  cursor:             pointer;
  text-decoration:    none;
  border-left:        1px solid transparent;
  transition:         color 0.15s ease,
                      padding-left 0.15s ease,
                      border-left-color 0.15s ease;

.menu-item-l3 > a::before {
  content:            '·';
  color:              #D1D5DB;
  flex-shrink:        0;
  line-height:        1.4;
}

.menu-item-l3 > a:hover {
  color:              #1A1A1A;
  padding-left:       16px;
  border-left-color:  var(--mm-brand-orange);
  text-decoration:    none;
}


─────────────────────────────────────────────
LEVEL 4 — DEEP NESTED ITEM
─────────────────────────────────────────────

Selector: .menu-item-l4 > a

  font-family:        var(--mm-font-body);
  font-size:          11.5px;
  font-weight:        500;
  color:              var(--mm-l4-color);
  padding:            2px 0 2px 22px;
  display:            flex;
  align-items:        center;
  gap:                5px;
  cursor:             pointer;
  text-decoration:    none;
  opacity:            0.8;
  transition:         color 0.15s ease, opacity 0.15s ease;

.menu-item-l4 > a::before {
  content:            '›';
  font-size:          13px;
  color:              var(--mm-brand-orange);
  flex-shrink:        0;
}

.menu-item-l4 > a:hover {
  color:              var(--mm-brand-orange-light);
  opacity:            1;
  text-decoration:    none;
}


─────────────────────────────────────────────
COLUMN CONTINUATION BADGE
─────────────────────────────────────────────

Selector: .mega-menu-cont-badge

  display:            inline-flex;
  align-items:        center;
  gap:                5px;
  background-color:   var(--mm-brand-orange-bg);
  border:             1px solid rgba(232, 80, 10, 0.25);
  border-radius:      3px;
  padding:            2px 8px;
  font-family:        var(--mm-font-heading);
  font-size:          10px;
  font-weight:        700;
  letter-spacing:     1px;
  text-transform:     uppercase;
  color:              var(--mm-brand-orange);
  margin-bottom:      14px;


─────────────────────────────────────────────
MEGA MENU FOOTER STRIP
─────────────────────────────────────────────

Selector: .mega-menu-footer

  background-color:   var(--mm-footer-bg);
  border-top:         1px solid var(--mm-col-border);
  padding:            10px 24px;
  display:            flex;
  align-items:        center;
  gap:                24px;
  grid-column:        1 / -1;

.mega-menu-footer a {
  font-family:        var(--mm-font-heading);
  font-size:          12px;
  font-weight:        600;
  letter-spacing:     1px;
  text-transform:     uppercase;
  color:              #9CA3AF;
  text-decoration:    none;
  display:            inline-flex;
  align-items:        center;
  gap:                5px;
  transition:         color 0.2s ease;
}
.mega-menu-footer a::after {
  content:            '→';
  font-size:          13px;
}
.mega-menu-footer a:hover {
  color:              var(--mm-brand-orange);
}


─────────────────────────────────────────────
COLUMN OVERFLOW / HEIGHT CONTROL
─────────────────────────────────────────────

• Set a maximum of 30 items (L2+L3 combined) per column.
  When an L1 group exceeds this, move remaining items to the next
  column and display the continuation badge at the top of that column.

• Two L1 groups may share a column only when their combined item
  count is ≤ 30. Separate them with:
  border-top: 1px solid var(--mm-divider);
  margin-top: 20px;
  padding-top: 20px;

• Control overall panel height:
  .mega-menu-panel {
    max-height:       82vh;
    overflow-y:       auto;
    overflow-x:       hidden;
  }
  .mega-menu-panel::-webkit-scrollbar { width: 4px; }
  .mega-menu-panel::-webkit-scrollbar-thumb {
    background:       #D1D5DB;
    border-radius:    2px;
  }


─────────────────────────────────────────────
TRIGGER / ACTIVE STATE ON NAV ITEM
─────────────────────────────────────────────

Selector: .nav-menu > li.current-menu-item > a,
          .nav-menu > li:hover > a

  color:              #1A1A1A;
  border-bottom:      3px solid var(--mm-brand-orange);
  background-color:   rgba(232, 80, 10, 0.04);
```

---

## SUMMARY OF DESIGN DECISIONS

| Element | Choice | Reason |
|---|---|---|
| Panel background | `#FFFFFF` white | Clean, professional — high contrast for readability |
| Alt column background | `#F9FAFB` light grey | Subtle rhythm between columns without competing with content |
| Accent colour | `#E8500A` orange | Consistent brand accent; pops strongly on white |
| L1 style | Near-black `#1A1A1A`, all-caps, condensed, orange underline + left bar | Strong visual anchor per column on white background |
| L2 style | Dark grey `#374151`, condensed, ▸ orange prefix | Clearly sub-ordinate to L1 but readable on white |
| L3 style | Medium grey `#6B7280`, body weight, dot bullet, orange left-border on hover | Easy to scan; hover cues are clear on light bg |
| L4 style | Orange `#E8500A`, deeper indent, › prefix | Visually distinct deep nesting with brand colour |
| Continuation badge | Light orange tint `#FFF3EE` with orange border | Matches brand on white without being heavy |
| Footer strip | `#F3F4F6` very light grey | Subtly separates footer from panel content |
| Box shadow | `0 16px 48px rgba(0,0,0,0.12)` | Softer shadow appropriate for light panel on white page |
