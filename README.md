# Lean Cookie Consent for PrestaShop

Lightweight PrestaShop module for the Lean Cookie Consent SaaS platform.

This first release installs as a classic PrestaShop module. It adds a single back office setting for the public Lean Cookie Consent Site Key and loads the hosted Lean runtime on the storefront.

## What it does

- Adds Lean Cookie Consent to PrestaShop storefront pages.
- Uses one public Site Key from the Lean Cookie Consent dashboard.
- Hardcodes the Lean runtime host: `https://api.leancookieconsent.com`.
- Exposes basic PrestaShop shop context through `window.LeanCookieConsentPrestaShop`.
- Keeps banner copy, languages, categories, services, policy links and evidence handling inside Lean Cookie Consent.

## Requirements

- PrestaShop 1.7.6 or later.
- PHP 7.2 or later.
- A Lean Cookie Consent account.
- A valid Lean Cookie Consent Site Key for the shop domain.

## Installation

1. Download `dist/leancookieconsent-prestashop-0.1.0.zip`.
2. In PrestaShop Admin, go to **Modules -> Module Manager**.
3. Click **Upload a module**.
4. Upload the zip file.
5. Open **Configure** for **Lean Cookie Consent**.
6. Paste the Site Key from the Lean Cookie Consent dashboard.
7. Save and test the storefront in an incognito browser window.

## Content Security Policy

If the shop or a reverse proxy adds a strict CSP, allow:

```text
script-src https://api.leancookieconsent.com
connect-src https://api.leancookieconsent.com
```

## Current limitations

- This module loads the Lean hosted runtime through `displayHeader`.
- It does not automatically reconfigure third-party modules that emit scripts before the hook runs.
- It does not yet integrate with a PrestaShop-specific consent API.
- For stronger control of marketing tags, use the Lean Google Tag Manager template and fire tags after consent.

## Repository Links

- Lean Cookie Consent: https://leancookieconsent.com/
- Lean app: https://app.leancookieconsent.com/
- GTM template: https://github.com/blacklotusconsulting/lean-cookie-consent-gtm-template

