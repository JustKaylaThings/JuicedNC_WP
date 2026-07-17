<!--
  DEPLOY-JUICED-TO-HOSTINGER.md
  Purpose:        Runbook for shipping the local JuicedNC WordPress theme + the
                  juiced-cpt plugin up to the Hostinger live site. Adapted from
                  DEPLOY-KAYJO-TO-HOSTINGER.md.
  Responsibility: Fast theme/code updates for the juiced theme. Ships BOTH the
                  theme folder AND wp-content/plugins/juiced-cpt (the Event /
                  Herb / Menu Item CPTs live there, outside the theme; the ACF
                  field groups are code-defined inside the theme's inc/).
  Used by:        Kayla, manually.
  Note:           Blocks are labeled LOCAL (your Mac) or SERVER (Hostinger SSH).
  ⚠ FILL IN the live URL below before running the purge/verify steps.
-->

# Deploy JUICED theme: Local → Hostinger

## Connection details

| Thing | Value |
|---|---|
| SSH host | `195.179.239.138` |
| SSH port | `65002` |
| SSH user | `u659751562` |
| Web root (server) | `~/domains/juicednc.com/public_html` (there is NO `~/public_html` on this box — pointing rsync there fails with a misleading "code 12" protocol error) |
| Live URL | `https://juicednc.com` |
| Local URL | `http://juicednc.local` |
| Local site path | `/Users/kayla/Local Sites/juicednc/app/public` |
| DB table prefix | `wp_` (must match on both ends) |

Connect:
```bash
ssh -p 65002 u659751562@195.179.239.138
```
Then on the server: `cd ~/domains/juicednc.com/public_html`

---

## Ship a theme/code change (fast update)

Two sync targets: the `juiced` theme **and** `plugins/juiced-cpt`. No DB
migration — content (pages, ACF values, menus, images) is entered on live.

### 1. Build CSS if you touched any Tailwind classes — LOCAL
The theme's CSS is a compiled Tailwind bundle (`assets/theme.css`). If you
edited template classes and skip this, the change WON'T show on live.
```bash
cd "/Users/kayla/Local Sites/juicednc/app/public/wp-content/themes/juiced"
npm run build
```

### 2a. Sync the theme folder — LOCAL
`--delete` makes the server theme an exact mirror of local (removes stale
files). Dev-only files (Tailwind source, npm packages) are excluded — the
compiled `assets/theme.css` is what live needs, and it ships.
```bash
cd "/Users/kayla/Local Sites/juicednc/app/public/wp-content/themes/juiced"
rsync -avz --delete --rsync-path=/usr/bin/rsync \
  --exclude 'node_modules' --exclude '.git' --exclude 'src' \
  --exclude 'package.json' --exclude 'package-lock.json' \
  -e "ssh -p 65002" \
  ./ u659751562@195.179.239.138:domains/juicednc.com/public_html/wp-content/themes/juiced/
```
> `--rsync-path=/usr/bin/rsync` works around the macOS built-in rsync (2.6.9)
> dying with "connection unexpectedly closed / error in rsync protocol data
> stream (code 12)". If it still fails, check the server has rsync at all
> (`ssh -p 65002 u659751562@195.179.239.138 "which rsync"`) — if not, use the
> tar fallback in the gotchas below.

### 2b. Sync the juiced-cpt plugin — LOCAL
A regular plugin (not mu-plugins), synced into its **own** folder, so
`--delete` is safe here — it only mirrors `juiced-cpt/`, never touching other
plugins.
```bash
cd "/Users/kayla/Local Sites/juicednc/app/public/wp-content/plugins/juiced-cpt"
rsync -avz --delete --rsync-path=/usr/bin/rsync \
  -e "ssh -p 65002" \
  ./ u659751562@195.179.239.138:domains/juicednc.com/public_html/wp-content/plugins/juiced-cpt/
```

### 3. Activate + flush rewrites — SERVER (first deploy / CPT changes)
The ACF field groups are code-defined in the theme (`inc/acf-*.php`), so they
appear automatically once the theme is active and **ACF (free) is installed and
active on live**. The CPTs (events, herbs, menu items) register rewrite rules —
flush permalinks after the first deploy or any CPT slug change:
```bash
cd ~/domains/juicednc.com/public_html
wp theme activate juiced
wp plugin activate juiced-cpt
wp rewrite flush
wp cache flush
```
> No WP-CLI on the server? Activate via wp-admin and re-save
> Settings → Permalinks (that's a rewrite flush).

### 4. Purge the LiteSpeed cache — SERVER  (CRITICAL)
Hostinger runs LiteSpeed cache. Logged-in you may see the change while
incognito still shows the old page. Purge:
```bash
wp litespeed-purge all
```
Verify it's not a stale cache:
```bash
curl -s  https://juicednc.com/events/ | grep -i "cached by litespeed"   # cache DATE
curl -sI https://juicednc.com/ | grep -i x-litespeed-cache              # "hit" = cached
```
If a stale entry survives, toggle caching off/on:
```bash
wp litespeed-option set cache false
# ...confirm the change shows in incognito...
wp litespeed-option set cache true
```

### 5. Verify — incognito
- `/` — homepage hero, sliders render.
- `/events/` — hero **search box under the subtext** filters the listing; Find
  Your Vibe pills + grid/list toggle work.
- `/locations/` — diagonal hero photos with pills; Order Ahead card shows both
  DoorDash buttons with "Opens DoorDash" captions.
- `/herbs/` and one herb detail page.
- **Mobile menu**: narrow the window (or phone) — hamburger opens the drawer,
  ✕ / tap-outside / Escape close it, Order Ahead button shows in it on phones.
- Edit a page in wp-admin — its ACF tabs (Hero / Find Your Juiced! / Order
  Ahead etc.) appear.

---

## Gotchas cheat-sheet
- **Two sync targets**: theme AND `plugins/juiced-cpt`. A theme-only sync
  silently drops the Event/Herb/Menu-Item CPTs → those pages 404.
- **Build Tailwind before syncing** if you changed classes, or CSS won't appear.
- **ACF free must be active on live** — without it every `juiced_page_field()`
  falls back to defaults and the admin field tabs vanish.
- **Flush permalinks after first deploy** or any CPT change, or CPT URLs 404.
- **Purge LiteSpeed after every deploy**; verify in incognito.
- **Code sync ships templates/fields, not content** — pages must exist on live
  with the right page template assigned, and ACF values (photos, addresses,
  DoorDash links…), menus, the logo, and media are entered in the live
  wp-admin (or migrate the DB + `uploads/` separately).
- **Alpine.js loads from a CDN** (`functions.php` TODO): the mobile menu,
  event filters, and herb search all need it — vendor it locally before launch
  or at least confirm the CDN loads on live.
- **rsync fallback** if the server has no rsync — tar over ssh (extracts into
  place; overwrites but doesn't delete stale files):
  ```bash
  cd "/Users/kayla/Local Sites/juicednc/app/public/wp-content/themes"
  tar -czf - \
    --exclude 'juiced/node_modules' --exclude 'juiced/src' \
    --exclude 'juiced/package.json' --exclude 'juiced/package-lock.json' \
    juiced | ssh -p 65002 u659751562@195.179.239.138 \
    "tar -xzf - -C ~/domains/juicednc.com/public_html/wp-content/themes"
  ```
