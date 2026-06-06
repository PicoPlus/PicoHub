# PicoPlus PHP (Plain PHP)

Persian RTL CRM customer portal backed by HubSpot — **no Laravel, no Docker, no Composer required**.

Runs on any shared PHP 8.2+ host with `mod_rewrite` (or nginx equivalent).

## Requirements

- PHP 8.2+ with `curl` and `json` extensions
- Writable `storage/cache` and `storage/logs` directories
- Apache `mod_rewrite` (document root = `public/`)

## Deploy on shared hosting

**Option A (recommended):** Point document root to the `public/` folder.

**Option B (subdirectory):** If your URL is `https://example.com/public/`, set in `.env`:

```
APP_URL=https://example.com/public
APP_BASE_PATH=public
```

1. Upload the project to your host.
2. Copy `.env.example` to `.env` (one level above `public/`) and set your API keys.
3. Ensure `storage/` is writable by PHP.

```
your-site/
  config/
  public/          ← document root
  src/
  storage/
  .env
```

## Environment

| Variable | Purpose |
|----------|---------|
| `HUBSPOT_TOKEN` | HubSpot private app token |
| `ZOHAL_TOKEN` | Zohal API bearer token |
| `IPPANEL_API_KEY` | IPPanel API key |
| `IPPANEL_FROM_NUMBER` | Sender line in E.164 |
| `IPPANEL_PATTERN_OTP` | Pattern code for OTP |
| `ADMIN_PASSWORD_*` | Admin login passwords |

## HubSpot contact properties

This app uses these HubSpot custom/contact fields:

| App field | HubSpot property |
|-----------|------------------|
| National code | `ncode` |
| Birth date (Persian) | `date_of_birth` |
| Father name | `father_name` |

Legacy aliases (`natcode`, `dateofbirth`) are still read for backward compatibility when loading contacts.

## HubSpot logging

Detailed JSON logs are written to `storage/logs/hubspot.log`:

- Every API request (method, endpoint, duration, status)
- Contact operations (search, create, update, login, register)
- Property mappings with masked PII
- Failed requests with response body preview and exception details

Set `APP_DEBUG=true` in `.env` to enable debug-level property mapping logs.

## Local development

Document root = `public/`:

```bash
cd public
php -S localhost:8000 router.php
```

App in a subdirectory (URL like `/public/`):

```bash
php -S localhost:8000 router.php
```

Open http://localhost:8000 (or http://localhost:8000/public/ for subdirectory mode)

## Routes

| Route | Description |
|-------|-------------|
| `/` | Redirect by session |
| `/auth/login` | User login (national code → HubSpot `ncode`) |
| `/auth/register` | Multi-step registration |
| `/user/panel` | User dashboard |
| `/Deal/Search` | Deal search |
| `/admin/login` | Admin login |
| `/admin/owner-select` | HubSpot owner picker |
| `/admin/dashboard` | Admin dashboard |

## Original project

Ported from PicoPlus Laravel / Blazor (.NET 9).
