---
name: Aces & Eights Pizza
description: Industrial ledger meets gilded furnace — an oxblood-and-gold ordering system for a real London pizza shop.
colors:
  oxblood-primary: "#690008"
  oxblood-deep: "#4d0006"
  oxblood-bright: "#8b1a1a"
  gilded-gold: "#D4AF37"
  gold-pale: "#F9E392"
  gold-dark: "#996515"
  gold-border: "#B8860B"
  carbon-ink: "#1b1c1c"
  parchment-surface: "#fcf9f8"
  parchment-container: "#f0eded"
  ledger-outline: "#8c716e"
  ledger-outline-soft: "#e0bfbc"
  error-red: "#ba1a1a"
typography:
  display:
    fontFamily: "'Source Serif 4', Georgia, serif"
    fontSize: "48px"
    fontWeight: 900
    lineHeight: "56px"
    letterSpacing: "-0.02em"
  headline:
    fontFamily: "'Source Serif 4', Georgia, serif"
    fontSize: "32px"
    fontWeight: 700
    lineHeight: "40px"
  body:
    fontFamily: "'Hanken Grotesk', system-ui, sans-serif"
    fontSize: "16px"
    fontWeight: 400
    lineHeight: "24px"
  label:
    fontFamily: "'JetBrains Mono', ui-monospace, monospace"
    fontSize: "14px"
    fontWeight: 700
    lineHeight: "20px"
    letterSpacing: "0.05em"
rounded:
  sm: "2px"
  md: "6px"
  lg: "8px"
  chip: "9999px"
spacing:
  base: "8px"
  gutter: "24px"
  margin-mobile: "16px"
  margin-desktop: "64px"
components:
  button-primary:
    backgroundColor: "{colors.gilded-gold}"
    textColor: "{colors.carbon-ink}"
    rounded: "{rounded.sm}"
    padding: "12px 24px"
  button-primary-hover:
    backgroundColor: "{colors.gold-dark}"
    textColor: "{colors.carbon-ink}"
    rounded: "{rounded.sm}"
  button-add:
    backgroundColor: "{colors.oxblood-primary}"
    textColor: "#ffffff"
    rounded: "{rounded.sm}"
    padding: "12px 24px"
  button-add-hover:
    backgroundColor: "{colors.oxblood-bright}"
    textColor: "#ffffff"
  button-secondary:
    backgroundColor: "transparent"
    textColor: "{colors.oxblood-primary}"
    rounded: "{rounded.sm}"
    padding: "12px 24px"
  card:
    backgroundColor: "{colors.parchment-surface}"
    rounded: "{rounded.sm}"
  chip:
    backgroundColor: "{colors.parchment-surface}"
    textColor: "{colors.ledger-outline}"
    rounded: "{rounded.chip}"
    padding: "4px 12px"
  input-field:
    backgroundColor: "transparent"
    textColor: "{colors.carbon-ink}"
    padding: "4px 0 8px"
---

# Design System: Aces & Eights Pizza

## 1. Overview

**Creative North Star: "The Gilded Furnace"**

A single London pizza shop's own system, not a food-delivery marketplace template. The furnace is the organizing image: oxblood red as the heat and structure, gilded gold as the glow it throws off — used deliberately, not decoratively. Depth comes from hard-edge stamped shadows and metallic gradients, never soft ambient blur, which keeps the whole system feeling ink-stamped and industrial rather than glossy-app-generic. Serif display headlines carry editorial authority; mono, uppercase, letter-spaced labels carry the ledger/ticket register for prices, tags, and admin navigation.

This system explicitly rejects the generic food-delivery marketplace look — stock Uber-Eats-style templates with no distinct identity. Aces & Eights is one shop with its own kitchen and character, and every surface should read that way, including the back-of-house admin and kitchen-display screens.

**Key Characteristics:**
- Oxblood + gilded gold as the dominant, deliberate identity — not a generic red/gold "restaurant" palette, a specific furnace-and-metal one.
- Hard-edge "stamped" shadows (solid offset, no blur) instead of soft Material-style elevation.
- Mono uppercase, letter-spaced labels for prices, tags, allergens, and admin nav — a ledger/ticket register.
- Serif display type for headlines, clean grotesk for body copy — editorial, not templated.
- Flat, bordered surfaces (parchment background, thin outline dividers) rather than card-shadow stacking.

## 2. Colors

Two committed accents on a warm, near-white parchment neutral — not a restrained single-accent system; oxblood and gold both carry real weight.

### Primary
- **Oxblood Primary** (#690008): The shop's own color — primary buttons ("add to order"), active nav states, brand chrome. Carries structural and trust weight; this is the color of the establishment itself.
- **Oxblood Deep** (#4d0006): Pressed/active state for oxblood elements, and hover-darken target.
- **Oxblood Bright** (#8b1a1a): Hover state for oxblood elements, lighter container variant.

### Secondary
- **Gilded Gold** (#D4AF37): The glow — glossy metallic CTA gradient (`#F9E392 → #D4AF37 → #996515`), gold-bordered accents, the auth sign-in button's metallic glow. Reserved for the moments that should feel like a reward or a highlight, not general-purpose accent.
- **Gold Pale** (#F9E392) / **Gold Dark** (#996515): Top and bottom stops of the glossy gold gradient. Always used together as a gradient, never as flat fills on their own.
- **Gold Border** (#B8860B): The hairline border that keeps the glossy gradient buttons from floating — always paired with the gradient, never alone.

### Neutral
- **Carbon Ink** (#1b1c1c): Primary text color, and the fill for hard-edge stamp shadows (`2px 2px 0 #1b1c1c`).
- **Parchment Surface** (#fcf9f8): Page background and card surface — warm near-white, not stark white.
- **Parchment Container** (#f0eded): Slightly recessed surface for grouped content within a page.
- **Ledger Outline** (#8c716e): Dividers, card borders, chip borders, input underlines.
- **Ledger Outline Soft** (#e0bfbc): Lighter divider/border variant, admin sidebar item text.
- **Error Red** (#ba1a1a): Form errors and destructive states only — distinct enough from Oxblood Primary to not be mistaken for a brand action.

### Named Rules
**The Furnace Ratio Rule.** Oxblood carries structure (buttons, active states, brand chrome) at higher frequency than gold. Gold is the glow, not the fuel — reserve the full metallic gradient treatment for primary CTAs and one or two signature moments (sign-in, hero) per screen, not every button.

## 3. Typography

**Display Font:** Source Serif 4 (Georgia, serif)
**Body Font:** Hanken Grotesk (system-ui, sans-serif)
**Label/Mono Font:** JetBrains Mono (ui-monospace, monospace)

**Character:** Editorial serif authority for headings paired with a clean, humanist grotesk for body copy — a magazine-menu pairing, not a tech-product one. Mono uppercase tracked type is reserved for the ledger register: prices, labels, tags, admin nav.

### Hierarchy
- **Display** (900, 48px / 56px line-height, -0.02em tracking): Hero and top-level page headings only.
- **Headline** (700, 32px / 40px): Section headings.
- **Title** (700, 24px / 32px, and 20px / 28px for the smaller step): Card titles, subsection headings, dish names.
- **Body** (400, 16px / 24px, or 18px / 28px for the larger reading size): Descriptions, menu item copy. Cap prose measure at 65–75ch.
- **Label** (700, 14px / 20px, 0.05em tracking, uppercase; 12px / 16px, 600 weight for the smaller step): Prices, allergen tags, filter chips, admin nav items — always uppercase, always mono, always tracked.

### Named Rules
**The Ledger Label Rule.** Anything that reads as metadata — a price, a tag, a nav item, an allergen marker — is JetBrains Mono, uppercase, letter-spaced. Anything that reads as content — a dish name, a description, a heading — never is. Mixing the two registers is the single fastest way to make this look like a generic template.

## 4. Elevation

Flat by default, with depth conveyed through hard-edge "stamped" offset shadows and metallic inset highlights — never soft ambient blur. This was a deliberate choice: the system should feel ink-stamped and industrial (a ticket, a deli receipt, a letterpress mark), not glossy-app-soft.

### Shadow Vocabulary
- **Stamp** (`box-shadow: 2px 2px 0 #1b1c1c`): The default "pressed into paper" offset for primary action buttons (gold gradient, oxblood "add to order"). Solid, no blur, no spread.
- **Metallic Highlight** (`box-shadow: inset 0 1px 0 rgba(255,255,255,0.45)`): Inner top highlight on glossy gold surfaces, simulating a curved metal edge.
- **Metallic Glow** (`box-shadow: inset 0 1px 0 rgba(255,255,255,0.4), 0 0 12px rgba(212,175,55,0.35)`): Reserved for one signature moment (the auth sign-in button) — a soft glow is allowed here specifically because it reads as light coming off metal, not as a generic elevation cue.
- **Pressed** (`box-shadow: inset 0 2px 4px rgba(0,0,0,0.2)` or `0 0 0`, with a `translate(2px, 2px)`): Active/pressed state — the stamp shadow collapses as the button physically depresses.

### Named Rules
**The Stamped Ledger Rule.** No soft, blurred `box-shadow` anywhere in the system except the one named Metallic Glow exception above. Depth comes from solid offset shadows, borders, and metallic gradients — never from ambient blur. If a shadow has a blur radius greater than 0px and isn't the Metallic Glow, it's wrong for this system.

## 5. Components

### Buttons
- **Shape:** 2px corner radius (`rounded: 2px`) — sharp, not soft.
- **Primary (glossy gold):** Gradient `#F9E392 → #D4AF37 → #996515`, `#1b1c1c` text, 1px `#B8860B` border, Stamp shadow. Mono uppercase tracked label, `12px 24px` padding.
- **Add-to-order (oxblood, industrial):** Solid `#690008` background, white text, 1px `#4d0006` border, Stamp shadow. This is distinct from the primary gold button — it's the transactional action (add to cart), not the aspirational CTA.
- **Secondary (oxblood outline):** Transparent background, 2px `#690008` border, oxblood text; fills solid oxblood with white text on hover.
- **Ghost:** Transparent, underlined, `on-surface-variant` text; turns oxblood on hover. Used for tertiary/low-emphasis actions.
- **Hover / Focus:** Gold buttons brighten via `filter: brightness()`, never via a shadow change. Oxblood buttons darken to Oxblood Bright/Deep. Active states collapse the Stamp shadow and nudge the button `2px, 2px` toward its shadow.

### Chips
- **Style:** Parchment background, `ledger-outline` border, full pill radius, mono uppercase tracked text — filter/tag chips for allergens and menu filters.
- **State:** Active/hover fills solid oxblood with white text and border.

### Cards / Containers
- **Corner Style:** 2px radius, matching buttons — the system doesn't soften cards relative to buttons.
- **Background:** Parchment Surface, occasionally Parchment Container for recessed grouping (e.g. admin stat cards).
- **Shadow Strategy:** None by default — cards are flat and bordered, not shadow-lifted. See Elevation.
- **Border:** 1px `ledger-outline`.
- **Internal Padding:** Admin stat cards use `24px` (spacing.gutter equivalent) internal padding.

### Inputs / Fields
- **Style:** No box at all — a bottom-border-only "ledger" input: transparent background, 2px bottom border in `ledger-outline`, no top/side borders.
- **Focus:** Bottom border shifts to Oxblood Primary; no glow or ring.
- **Placeholder:** `on-surface-variant`, same weight as filled text.

### Navigation
- **Admin sidebar/nav items:** Mono, uppercase, letter-spaced (0.1em), `ledger-outline-soft` text at rest, oxblood fill with white text when active or hovered. No icons-only state; label always visible.
- **Section headings (customer-facing menu):** Serif, uppercase, wide tracking (0.15em), with a 4px double-rule border beneath — the industrial double-line divider, distinct from a plain underline.

## 6. Do's and Don'ts

### Do:
- **Do** use hard-edge Stamp shadows (`2px 2px 0`, solid, no blur) for elevated interactive elements.
- **Do** reserve the full glossy gold gradient for primary/aspirational CTAs and signature moments (sign-in, hero) — not every button.
- **Do** keep all metadata (prices, tags, nav labels, allergens) in mono, uppercase, letter-spaced type — the Ledger Label Rule.
- **Do** use the 4px double-rule divider for menu section headings; it's this system's signature, not a generic underline.
- **Do** keep buttons and cards sharp-cornered (2px radius) — softness is not part of this system's vocabulary.

### Don't:
- **Don't** use soft, blurred `box-shadow` anywhere except the one named Metallic Glow exception on the auth sign-in button.
- **Don't** make this look like a generic food-delivery marketplace template (stock Uber-Eats-style, no distinct identity) — this is PRODUCT.md's explicit anti-reference, and it's the single biggest risk to this system's identity.
- **Don't** use gradient text (`background-clip: text`) — the gold gradient belongs on solid button surfaces only, never on typography.
- **Don't** soften card corners relative to buttons, or add card drop-shadows "for depth" — cards stay flat and bordered.
- **Don't** mix content type (dish names, descriptions, headings) into the mono/uppercase ledger register, or metadata (prices, tags) into serif/sans content type.
