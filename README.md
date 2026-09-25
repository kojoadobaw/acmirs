# ACMIRS PHP/MySQL CMS

This folder is a dynamic, database-backed version of the ACMIRS website. It is
isolated from the original static files. Brand assets and photography were
copied into this folder so the existing site does not need to be changed.

## Requirements

- PHP 7.0 or newer with PDO MySQL
- MySQL 5.6+ or MariaDB 10.1+
- A web server whose document root can serve this folder

## Setup

1. Copy `.env.example` to `.env` and enter the real MySQL connection details.
   (`config/local.example.php` → `config/local.php` also works as an
   alternative to `.env`.) There are no built-in defaults — the app fails to
   start if the database configuration is incomplete.
2. Ensure the database user can create the configured database, or create the
   database manually and grant the user access.
3. Open `/ACMIRSPHP/install.php` in a browser and run the migration.
4. Sign in at `/ACMIRSPHP/admin/` with username `admin` and temporary password
   `admin`.
5. The first sign-in forces the administrator to set a private password of at
   least 12 characters.

After installation, restrict or remove public access to `install.php` at the
web-server level. Running it again does not overwrite populated tables, but it
should not remain publicly reachable in production.

## CMS coverage

- Services and their capabilities, challenge/response/outcome cards, and
  relevant sector relationships
- Sectors
- Infrastructure in Action videos (YouTube, Vimeo, direct MP4, or public URL)
- Experience/projects and project detail pages
- Insights/articles and insight detail pages
- The six-item `The ACMIRS Mandate` menu in the fixed hero
- Section headings, introductions, metrics, CTA, and contact details
- Newsletter email capture

The main hero copy, imagery, and animation remain fixed for this phase.

## Security notes

- Passwords use PHP's `password_hash`/`password_verify`.
- All CMS writes use CSRF tokens and prepared database statements.
- Admin sessions use strict, HTTP-only, SameSite cookies; secure cookies are
  enabled automatically under HTTPS.
- Public output is escaped before rendering.
