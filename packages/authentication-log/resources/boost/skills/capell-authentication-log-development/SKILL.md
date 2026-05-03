---
name: capell-authentication-log-development
description: Use when editing Capell Authentication Log events, middleware, widgets, or settings.
---

# Capell Authentication Log

Login, failed login, logout, and admin/user activity metadata for Capell users.

## Look

- `packages/authentication-log/src`
- `packages/authentication-log/docs`
- `packages/authentication-log/README.md`

## Rules

- Treat auth records as audit data; avoid destructive defaults.
- Middleware must be lightweight and privacy-conscious.
- Widgets should summarize logs without leaking sensitive metadata.
- Run `vendor/bin/pest packages/authentication-log/tests`.
