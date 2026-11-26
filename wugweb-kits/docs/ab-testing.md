# A/B testing

A small, consent-aware split-test framework built into the theme (`inc/ab-testing.php`). No plugin. Experiments are defined in one file, `inc/ab-experiments.json`, and every experiment ships **off** (`"enabled": false`). While none is enabled the theme prints nothing.

## How it works

- **Assignment.** A visitor is given one variant per experiment, weighted by `weight`, once. It is remembered in the first-party cookie `wugweb_ab` (`id:variant|id:variant`, 90 days, `SameSite=Lax`, `Secure` on https).
- **Consent first.** The cookie is written, and events are sent, **only after analytics consent** (the cookie banner stores `wugweb_cookie_consent_v1` with `analytics: true`). Without consent everyone sees variant A, no cookie is set and nothing is sent. If consent is given part-way through a visit, the variant is assigned and remembered for the *next* page; the visitor has only seen A so far, so no exposure is recorded for the current page.
- **Applying a variant.** A tiny inline script in `<head>` adds `ab-<id>-<variant>` to `<html>` before first paint (so CSS can restyle without flicker) and, if the experiment has a `selector` and `labels`, swaps the text of those elements on `DOMContentLoaded`. Variants are CSS or text only: no layout change, reduced-motion and accessibility are untouched. Keep both labels about the same length so nothing shifts.
- **Events (GA4, only with consent).** Sent through `gtag` when it exists, otherwise queued on the `dataLayer` Site Kit uses:
  - `ab_assign` `{experiment_id, variant}` once per page view where the visitor actually saw the variant.
  - `ab_goal` `{experiment_id, variant, goal}` when the experiment's `goal` selector, or any element carrying `data-ab-goal="name"`, is clicked.
- **QA.** Add `?ab_force=<id>:<variant>` (comma-separate several) to any URL to see a variant: no cookie, no events.

## Experiments defined (all off)

| id | where | A | B | conversion |
|---|---|---|---|---|
| `home-hero-cta` | home page only (`^/$`) | "Start a project" | "Book a call" | click on `.hero-ctas .btn-solid` |
| `nav-cta` | every page | "Let's Talk" (current) | "Start a project" | click on `.nav-cta` |

Note: the home hero button currently reads "Get in Touch". If A should keep today's text, set `"a": null` in `labels` (null leaves the page text alone) before switching it on.

## Switching one on

1. Set `"enabled": true` for the experiment in `inc/ab-experiments.json` and ship the theme.
2. In GA4 **Admin → Custom definitions**, register two **event-scoped** custom dimensions: `experiment_id` and `variant` (and `goal`).
3. Run it for whole weeks. Do not change copy, traffic sources or the page while it runs.

## Reading results in GA4

**Explore → Free form**: rows = `variant`, filter `experiment_id` = the experiment, values = **Users** (or Event count) split by event name. Conversion rate per variant = users with `ab_goal` ÷ users with `ab_assign`. Compare the two variants with a two-proportion test (any online calculator), not by eye.

## Minimum traffic

Only visitors who accept analytics cookies enter an experiment, so count that share, not total traffic. Rule of thumb per variant: `n ≈ 16 × p × (1 − p) / d²`, with `p` the baseline conversion rate and `d` the absolute lift you want to detect.

- Baseline 3% click rate, detect a 20% relative lift (+0.6 points): about **13,000** consenting visitors per variant.
- Baseline 10%, detect a 20% relative lift (+2 points): about **3,600** per variant.

Run at least 14 days even if the numbers arrive sooner (weekday/weekend mix). If the site cannot reach these numbers in a reasonable time, test a bigger change, or do not test.
