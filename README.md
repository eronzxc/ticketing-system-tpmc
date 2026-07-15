# TPMC IT Concern Desk

A web-based IT helpdesk ticketing system built for TPMC's hospital IT department. Departments can submit and track IT concerns; IT staff can update ticket status and reply to requesters.

## Features

- Account registration and login (any hospital department can create an account)
- Ticket submission with category, priority, and file/photo attachments
- Auto-computed due dates based on priority
- Dashboard with search, status, and category filters
- Ticket detail view with status history and IT reply thread
- Access control: only accounts under the IT Department can change ticket status or post replies
- Overdue ticket indicator

## Tech stack

- Frontend: HTML, CSS, vanilla JavaScript
- Backend: PHP (no framework)
- Database: MySQL
- Local dev environment: XAMPP (Apache + MySQL)

## Project structure

```
ticketing-system-tpmc/
├── auth/
│   ├── register.php
│   ├── login.php
│   ├── logout.php
│   └── me.php
├── config/
│   ├── db.php
│   └── session.php
├── tickets/
│   ├── create.php
│   ├── list.php
│   ├── get.php
│   ├── update_status.php
│   └── reply.php
├── schema.sql
├── migration_01_attachments.sql
└── index.html
```

## Setup

1. Install [XAMPP](https://www.apachefriends.org/) and start **Apache** and **MySQL**.
2. Clone this repo into `C:\xampp\htdocs\ticketing-system-tpmc`.
3. Open phpMyAdmin (`http://localhost/phpmyadmin`) and import `schema.sql`, then run `migration_01_attachments.sql`.
4. If your MySQL credentials differ from the XAMPP default (`root`, no password), update `config/db.php`.
5. Open `http://localhost/ticketing-system-tpmc/` in your browser.

## Notes

- Attachments are currently stored as base64 inside the database (`tickets.attachments_json`), not as separate files on disk.
- To make the system accessible to other devices on the same network, see the setup notes on configuring Apache and Windows Firewall for LAN access.

## Status

Work in progress — built as a portfolio/OJT project, with an eye toward eventual deployment for actual hospital use.

## Author

Made by: Aaron Ludwig A. Altar
GitHub: [@eronzxc](https://github.com/eronzxc)
