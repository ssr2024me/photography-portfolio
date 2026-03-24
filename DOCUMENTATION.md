# Photography-life4me — Complete Website Documentation
**Version:** 2.0 | **Author:** Shubham Sagar | **Hosting:** InfinityFree

---

## 📁 File Structure (Organized)

```
htdocs/
│
├── 📄 index.html          ← Home page (hero, featured photos, categories)
├── 📄 portfolio.html      ← Photo gallery with filters & masonry layout
├── 📄 about.html          ← About me, gear, timeline
├── 📄 contact_page.html   ← Contact form page
│
├── 📄 admin.php           ← Admin panel (upload, manage, settings)
├── 📄 admin_remote.html   ← Remote upload tool (use from any device)
│
├── 📄 api.php             ← All backend API actions (PHP)
├── 📄 config.php          ← DB credentials & constants
├── 📄 contact.php         ← Contact form handler (saves to DB + email)
├── 📄 bulk_upload.php     ← Bulk upload helper (PHP backend)
│
├── 📄 cursor-particles.js ← Custom cursor + particle animation engine
│
├── 📂 css/
│   └── style.css          ← Shared styles (nav, footer, responsive, transitions)
│
└── 📂 js/
    └── page-transitions.js ← Page transition fix + hamburger menu
```

---

## 🌐 Pages

### 1. `index.html` — Home Page
- **Intro animation:** Camera lens iris opens on first load (3 seconds)
- **Hero section:** Split layout — text left, photo right
- **Featured Work:** Shows photos marked as "Hero" in admin
- **Categories:** Auto-loads from DB, shows photo count per category
- **Lightbox:** Click any featured photo to open full screen
- **Scripts needed at bottom:**
  ```html
  <script src="cursor-particles.js"></script>
  <script src="js/page-transitions.js"></script>
  ```

### 2. `portfolio.html` — Gallery
- **Filter bar:** Tabs auto-generated from photo categories in DB
- **Masonry layout:** 3-column on desktop, 2 on tablet, 1 on mobile
- **Infinite scroll / load more:** Button at bottom to load more photos
- **Lightbox:** Full screen view with title + category

### 3. `about.html` — About Me
- Profile image, bio, stats (years, photos, cities)
- Gear section (camera equipment)
- Timeline / experience

### 4. `contact_page.html` — Contact
- Form fields: Name, Email, Subject, Message
- Sends to `contact.php` → saves to DB → sends email notification
- Success/error message shown to user

---

## ⚙️ Backend Files

### `config.php` — Database Settings
```php
DB_HOST = /..........
DB_USER = /..........
DB_PASS = /passss   
DB_NAME = /.........
ADMIN_KEY = '*******'      // ⚠️ Change this — admin authentication
MAX_FILE_SIZE = 15MB
ALLOWED_TYPES = jpg, png, webp, gif
```
> **⚠️ Security:** Config file mein password save hai. InfinityFree pe `.htaccess` se protect karo.

### `api.php` — All API Actions
| Action (GET `?action=`) | Method | Description |
|---|---|---|
| `get_list` | GET | Saari photos ki list |
| `get_image&id=N` | GET | Photo bytes serve karta hai |
| `upload` | POST | Single photo upload (admin) |
| `upload_base64` | POST | Base64 upload (remote tool ke liye) |
| `update` | POST | Photo title/category/description edit |
| `delete` | POST | Photo delete |
| `set_hero` | POST | Photo ko hero/featured banao |
| `set_featured_batch` | POST | Multiple photos featured set karo |
| `get_messages` | GET | Saare contact messages |
| `mark_read` | POST | Message read mark karo |
| `reply_message` | POST | Message ka reply bhejo |
| `delete_message` | POST | Message delete karo |
| `get_settings` | GET | Website settings load karo |
| `save_setting` | POST | Ek setting save karo |

---

## 🎛️ Admin Panel (`admin.php`)

### Login
Admin key: `********` (change in `config.php` → `ADMIN_KEY`)

### Tabs

#### 📷 Photos Tab
- Saari uploaded photos grid mein dikhti hain
- Search bar se filter karo
- Har photo pe:
  - ✏️ **Edit** — Title, category, description change karo
  - ⭐ **Hero** — Is photo ko home page hero banao
  - 🗑️ **Delete** — Photo permanently delete karo

#### ⬆️ Upload Tab
**Single Upload:**
1. Photo select karo (JPG/PNG/WEBP, max 15MB)
2. Title bharo
3. Categories select karo (multiple allowed)
4. Description optional
5. Watermark options: on/off, text, style, position
6. "Upload →" click karo

**Bulk Upload + AI:**
1. Gemini API key daalo (test button se verify karo)
2. Photos drag karo ya click karke select karo (Ctrl+A se saari)
3. Har photo ke box mein 🤖 button → 3 AI suggestions aate hain
4. Jo suggestion pasand aaye click karo → auto-fill
5. "🤖 AI All" → saari photos ke liye ek saath AI generate
6. "Upload All Photos →" → saari ek saath upload

#### ⭐ Featured Work Tab
- Left panel: Available photos
- Right panel: Featured photos (home page pe dikhenge)
- "+ Add" → left se right mein
- "✕ Remove" → featured se nikalo
- "Save Changes →" → DB mein save

#### ✉️ Messages Tab
- Contact form se aaye messages dikhte hain
- Filter: All / Unread / Replied
- Click karo message pe → full message + reply box
- Reply likhke "Send Reply →" → email bhejta hai aur DB mein save
- 🗑️ se delete

#### ⚙️ Settings Tab
**Background Style:** 9 options — Dark Grain, Navy, Warm, Forest, Vignette, Purple, Geometric, Red, Concrete

**Loading Screen (Lens):**
- Lens color (dark/gold/blue/red/green)
- Iris open speed (normal/slow/fast)
- Flare color (gold/white/blue/none)
- Intro duration (2-5 seconds)

**Site Title:** Nav bar aur footer mein dikhne wala naam

**🖱️ Cursor Style:** ← **NEW**
- Golden Ring (default)
- Magnetic Blob
- Camera Shutter
- Crosshair
- Neon Trail
- None

**✨ Particle Effect:** ← **NEW**
- None (off)
- Constellation — dots + connecting lines
- Firefly — glowing orbs that follow cursor
- Dark Matter — fast particles with trails
- Starfield — slow twinkling stars
- Lens Flare — cross-shaped stars, click burst

---

## 🖱️ `cursor-particles.js` — Cursor + Particle Engine

Ye file automatically `api.php?action=get_settings` se settings load karti hai.

**Include kaise karein (har HTML page ke `</body>` se pehle):**
```html
<script src="cursor-particles.js"></script>
```

**Settings DB mein save hoti hain, admin panel se control:**
| Setting Key | Values |
|---|---|
| `cursor_style` | golden-ring / magnetic-blob / camera-shutter / crosshair / neon-trail / none |
| `cursor_color` | hex color, e.g. `#d4a853` |
| `particle_style` | none / constellation / firefly / dark-matter / starfield / lens-flare |
| `particle_color` | hex color |
| `particle_count` | 20–150 (default 80) |

---

## 🔧 Known Issues & Fixes (Applied in v2.0)

### ✅ Fix 1: Blank Page on Back Button
**Problem:** Page transition animation overlay page ko blank kar deta tha jab browser back karta tha.

**Fix:** `page-transitions.js` mein `window.addEventListener('pageshow')` add kiya. Ye browser cache se page load hone pe overlay ko instantly hide karta hai.

**File:** `js/page-transitions.js`

### ✅ Fix 2: Cursor/Particles Settings Missing from Admin
**Problem:** `cursor-particles.js` tha lekin admin panel mein settings ka koi section nahi tha.

**Fix:** Admin panel ke Settings tab mein "Cursor Style" aur "Particle Effect" sections add kiye — DB mein save hote hain, website pe turant apply hote hain.

**Files:** `admin.php` (Settings tab)

### ✅ Fix 3: cursor-particles.js Pages pe Include Nahi Tha
**Problem:** `cursor-particles.js` sirf banaya gaya tha, pages mein include nahi kiya gaya tha.

**Fix:** `index.html`, `portfolio.html`, `about.html`, `contact_page.html` mein `</body>` se pehle script tag add kiya.

### ✅ Fix 4: Missing API Actions
**Problem:** Admin panel ke functions kuch API actions call karte the jo `api.php` mein exist nahi karte the:
- `mark_read`
- `reply_message`
- `delete_message`
- `set_featured_batch`

**Fix:** Ye saare actions `api.php` mein add kiye.

### ⚠️ Upload Fail Issue
**Common causes:**
1. **File too large:** InfinityFree pe PHP `upload_max_filesize` aur `post_max_size` limited hoti hai. 2MB se chhoti photos try karo.
2. **PHP memory limit:** Large images fail ho jaati hain. Solution: Image ko pahle compress karo (TinyPNG.com).
3. **DB column size:** `image_data` column LONGTEXT honi chahiye. `api.php` mein ye auto-run hota hai: `ALTER TABLE photos MODIFY COLUMN image_data LONGTEXT`
4. **InfinityFree timeout:** Ek saath bahut saari photos mat upload karo. 5-10 ek baar mein karo.

---

## 🗄️ Database Structure

### Table: `photos`
| Column | Type | Description |
|---|---|---|
| `id` | INT AUTO_INCREMENT | Primary key |
| `title` | VARCHAR(255) | Photo title |
| `category` | VARCHAR(255) | Comma-separated categories |
| `mime_type` | VARCHAR(50) | image/jpeg, image/png etc |
| `image_data` | LONGTEXT | Base64 encoded image |
| `description` | TEXT | Photo description |
| `is_featured` | TINYINT(1) | 1 = home page pe dikhe |
| `sort_order` | INT | Manual ordering |
| `has_watermark` | TINYINT(1) | 1 = watermark applied |
| `watermark_text` | VARCHAR(100) | Watermark text |
| `watermark_font` | VARCHAR(50) | bold / subtle / strong / italic |
| `created_at` | TIMESTAMP | Upload time |

### Table: `messages`
| Column | Type | Description |
|---|---|---|
| `id` | INT | Primary key |
| `name` | VARCHAR(255) | Sender name |
| `email` | VARCHAR(255) | Sender email |
| `subject` | VARCHAR(255) | Message subject |
| `message` | TEXT | Message content |
| `reply` | TEXT | Admin reply |
| `is_read` | TINYINT(1) | 0=unread, 1=read |
| `is_replied` | TINYINT(1) | 0=not replied, 1=replied |
| `created_at` | TIMESTAMP | Received time |

### Table: `settings`
| Key | Example Value | Description |
|---|---|---|
| `background` | dark-grain | Website background style |
| `cursor_style` | golden-ring | Custom cursor type |
| `cursor_color` | #d4a853 | Cursor color hex |
| `particle_style` | constellation | Background particle style |
| `particle_color` | #d4a853 | Particle color hex |
| `particle_count` | 80 | Number of particles |
| `lens_color` | dark | Intro lens color |
| `lens_speed` | normal | Intro iris speed |
| `lens_flare` | gold | Intro flare color |
| `intro_duration` | 3000 | Intro screen duration (ms) |
| `site_title` | PHOTOGRAPHY-LIFE4ME | Site name |

---

## 📱 Responsive Breakpoints

| Breakpoint | Layout |
|---|---|
| `> 1024px` | Full desktop layout |
| `769px–1024px` | Tablet — 2 columns, reduced padding |
| `481px–768px` | Mobile — hamburger menu, single column |
| `≤ 480px` | Small mobile — minimal padding |

**Mobile features:**
- Hamburger menu (☰) → full screen nav overlay
- Hero stacks vertically (photo on top, text below)
- Gallery goes to 1-2 columns
- Footer stacks vertically

---

## 🚀 Deployment Checklist (InfinityFree)

1. ☐ Saari files htdocs/ folder mein upload karo via FTP
2. ☐ `config.php` mein DB credentials verify karo
3. ☐ `ADMIN_KEY` change karo (default: *****)
4. ☐ DB password change karo
5. ☐ Admin panel open karo: `https://your-domain/admin.php`
6. ☐ Settings tab mein cursor + particles customize karo
7. ☐ Test: photo upload karo
8. ☐ Test: contact form bharo
9. ☐ Test: mobile pe website open karo

---

## 🔗 LinkedIn & GitHub ke liye Description

**LinkedIn Project Description:**
> A full-stack photography portfolio website built with vanilla HTML/CSS/JS and PHP/MySQL. Features include a custom cursor + particle animation engine, AI-powered (Gemini API) bulk photo metadata generation, responsive masonry gallery, admin panel with real-time settings, contact form with DB storage, and smooth page transitions. Hosted on InfinityFree with MySQL backend.

**GitHub README tags:**
`photography` `portfolio` `php` `mysql` `vanilla-js` `responsive` `gemini-ai` `admin-panel` `canvas-animation`

---

## ✏️ How to Edit — Quick Reference

| Want to change | Edit this |
|---|---|
| Nav links | Each HTML file → `<ul class="nav-links">` |
| Hero text / subtitle | `index.html` → `.hero-left` section |
| About me content | `about.html` |
| Contact email | `contact.php` → `$to` variable |
| Admin password | `config.php` → `ADMIN_KEY` |
| DB credentials | `config.php` |
| Cursor default | `cursor-particles.js` → `cursorStyle` fallback value |
| Particle default | `cursor-particles.js` → `particleStyle` fallback value |
| Accent color (gold) | `css/style.css` → `:root { --accent: #d4a853; }` |
| Font | Each HTML file → Google Fonts `<link>` tag |
| Footer text | Each HTML file → `<footer>` section |

---

*Documentation generated for Photography-life4me v2.0*
