# photography-life4me

**🌐 Live → [photography-life4me.infinityfreeapp.com](https://photography-life4me.infinityfreeapp.com/)**

---

A personal photography portfolio I built from scratch to actually display my work the way I wanted — not just another template. Has a full admin panel so I can manage everything from my phone without touching code.

---

## What's in it

- Masonry photo gallery with category filters
- Admin panel — upload, edit, delete, feature photos
- AI-assisted bulk upload using Gemini API (auto-generates titles + categories)
- Contact form that saves to DB and sends email notification
- Custom cursor + particle background animations (controllable from admin)
- Intro lens animation on first load
- Fully responsive — works on mobile

## Stack

- **Frontend** — HTML, CSS, Vanilla JS (no frameworks)
- **Backend** — PHP
- **Database** — MySQL
- **Hosting** — InfinityFree
- **AI** — Google Gemini API (optional, for bulk upload metadata)

## File Structure

```
htdocs/
├── index.html              # Home — hero, featured photos, categories
├── portfolio.html          # Full gallery with masonry + filters
├── about.html              # About, gear, timeline
├── contact_page.html       # Contact form
├── admin.php               # Admin panel
├── admin_remote.html       # Upload from any device
├── api.php                 # All backend API endpoints
├── config.php              # DB credentials (not in repo — see below)
├── contact.php             # Contact form handler
├── bulk_upload.php         # Bulk upload backend
├── cursor-particles.js     # Cursor + particle engine
├── css/style.css           # Shared styles
└── js/page-transitions.js  # Page transitions + hamburger menu
```

## Setup

**1. Clone the repo**
```bash
git clone https://github.com/yourusername/photography-life4me.git
```

**2. Create `config.php`** from the example file
```bash
cp config.example.php config.php
```

Then fill in your DB credentials:
```php
define('DB_HOST', 'your-db-host');
define('DB_USER', 'your-db-user');
define('DB_PASS', 'your-db-password');
define('DB_NAME', 'your-db-name');
define('ADMIN_KEY', 'set-a-strong-password');
```

**3. Upload to your server** via FTP and open `admin.php` to get started.

> `config.php` is in `.gitignore` — never commit it.

## Admin Panel

Go to `/admin.php` on your hosted site. Login with the `ADMIN_KEY` you set in config.

From there you can:
- Upload single or bulk photos (with AI title generation)
- Mark photos as featured (shows on home page hero)
- Reply to contact messages
- Control cursor style, particle effects, background, intro animation — all from settings tab

## Notes

- Keep individual uploads under 2MB on InfinityFree (their PHP limit)
- For bulk uploads, do 5–10 at a time to avoid timeouts
- Gemini API key is optional — only needed if you want AI metadata generation

## License

Personal project. Feel free to use as reference or starting point, just don't copy it wholesale and call it yours.

---

*Built by Shubham Sagar*
