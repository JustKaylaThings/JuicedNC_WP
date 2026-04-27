# Scheduled Header Announcements via `site_announcement` CPT

## Context

The header announcement text in the utility strip is currently driven by a single string (`siteSettings.siteSettingsFields.headerAnnouncement`) plus a **hardcoded Thursday override** in [src/components/layout/Header.tsx](src/components/layout/Header.tsx) that replaces whatever the client set in WP with `"Open Mic Tonight at 8PM! · 434 Hill St"` every Thursday in Raleigh time.

The client wants to be able to schedule announcements from WP Admin directly — e.g., "On Thursdays between 4pm and close, show the Open Mic message" — without touching code. They don't have ACF Pro, so repeaters aren't available.

**Solution:** add a new `site_announcement` Custom Post Type (same pattern as `event`, `herb`, `menu_item`). Each post is one scheduled announcement. Store owner adds/edits/reorders them in WP admin like any other post type. The Next.js header fetches all of them, finds the first active one for the current day/time, and shows its title. The hardcoded Thursday override is removed.

---

## Part A — What the user needs to do in the `juiced-cpt.php` plugin repo

Hand this off verbatim to the PHP chat.

### A1. Register the CPT

Follow the same pattern as the existing `event` / `herb` / `menu_item` CPT blocks in `juiced-cpt.php`. Register a `site_announcement` post type:

- `name` = `site_announcement`
- `public` = `true` (or `false` — it's admin-facing, never shown on its own detail page)
- `show_in_rest` = `true`
- `show_in_graphql` = `true`
- `graphql_single_name` = `"siteAnnouncement"`
- `graphql_plural_name` = `"siteAnnouncements"`
- `supports` = `['title', 'page-attributes']` — title holds the announcement text; page-attributes enables `menu_order` for drag-reorder in admin
- `menu_icon` = `'dashicons-megaphone'` (matches the feature)
- `hierarchical` = `false`

### A2. Register the ACF field group (`siteAnnouncementFields`)

Attach to the new CPT. Name the group **"Announcement Schedule"**. Enable **Show in GraphQL** on the group with GraphQL field name `siteAnnouncementFields`. Add these fields (all with Show in GraphQL enabled):

| ACF field name   | Type         | Notes                                                                                  |
| ---------------- | ------------ | -------------------------------------------------------------------------------------- |
| `event_date`     | Date Picker  | Optional. Set for one-off announcements (e.g. a specific holiday).                     |
| `day_of_week`    | Select       | Optional. Choices: `Sunday,Monday,Tuesday,Wednesday,Thursday,Friday,Saturday`. Used when `event_date` is empty. |
| `start_time`     | Time Picker  | Optional. 24-hour display. Empty = announcement applies from start of day.             |
| `end_time`       | Time Picker  | Optional. Empty = announcement applies through end of day.                             |

All field names are snake_case in ACF; WPGraphQL automatically exposes them as camelCase (`eventDate`, `dayOfWeek`, `startTime`, `endTime`).

### A3. No custom resolver needed

Unlike the existing `siteSettings` options page (which uses a hand-written resolver reading `copacf_options_*` keys), a standard CPT + ACF field group is exposed automatically by WPGraphQL + WPGraphQL for ACF. No extra PHP resolver is required.

### A4. Leave existing `siteSettings.headerAnnouncement` alone

That field stays — it's used as a **fallback** when no scheduled announcement matches (and as the always-on default). Don't remove it from the Site Settings ACF group.

---

## Part B — What I will do in this Next.js repo

### B1. New query file: `src/lib/queries/site-announcements.ts`

Mirror the pattern in [src/lib/queries/events.ts](src/lib/queries/events.ts). Export:

```ts
export const GET_SITE_ANNOUNCEMENTS = gql`
  query GetSiteAnnouncements {
    siteAnnouncements(
      first: 50,
      where: { orderby: [{ field: MENU_ORDER, order: ASC }] }
    ) {
      nodes {
        id
        title
        menuOrder
        siteAnnouncementFields {
          eventDate
          dayOfWeek
          startTime
          endTime
        }
      }
    }
  }
`;
```

Include the "Required WP setup" comment block at top (mirroring the style of existing queries files).

### B2. New types: `src/types/site-announcement.ts`

```ts
export interface SiteAnnouncementFields {
  eventDate: string | null;      // "YYYY-MM-DD"
  dayOfWeek: string | null;      // "Sunday"…"Saturday"
  startTime: string | null;      // "HH:MM:SS" or "HH:MM"
  endTime: string | null;
}

export interface SiteAnnouncement {
  id: string;
  title: string;
  menuOrder: number | null;
  siteAnnouncementFields: SiteAnnouncementFields | null;
}

export interface SiteAnnouncementsQueryResult {
  siteAnnouncements: { nodes: SiteAnnouncement[] };
}
```

### B3. Selection helper: `src/lib/utils/site-announcements.ts`

New tiny utility — reuse the existing `parseTime` / `parseLocalDate` / `DAY_INDEX` helpers from [src/lib/utils/event-dates.ts](src/lib/utils/event-dates.ts) if it makes sense to export them; otherwise keep the new util self-contained.

Export `pickActiveAnnouncement(announcements, now)` that returns the first announcement whose schedule matches `now`. Matching rules:

1. If `eventDate` is set → must equal today's date (in `America/New_York`). Takes priority over recurring rules.
2. Else if `dayOfWeek` is set → today's weekday must match.
3. Else → matches any day.
4. If `startTime` set → `now >= startTime` (today, in Raleigh time).
5. If `endTime` set → `now < endTime`.
6. First match in menu-order order wins.
7. Returns `null` if nothing matches.

Use `Intl.DateTimeFormat` with `timeZone: 'America/New_York'` (same pattern as the existing `isThursdayInRaleigh` helper in Header.tsx) so behavior is correct regardless of the Vercel server's timezone.

### B4. Update `src/components/layout/Header.tsx`

- Query `GET_SITE_ANNOUNCEMENTS` in parallel with the existing `GET_SITE_SETTINGS` call (single `Promise.all`).
- Resolve announcement in this order:
  1. `pickActiveAnnouncement(announcements, new Date())?.title`
  2. `siteSettings.siteSettingsFields.headerAnnouncement`
  3. `DEFAULT_ANNOUNCEMENT` constant (final fallback for when WP is unreachable)
- **Remove** the `THURSDAY_ANNOUNCEMENT` constant and the `isThursdayInRaleigh()` function — they're replaced by the CPT-driven selection.

Graceful failure: if the announcements query errors, continue with the existing site-settings fallback. Wrap in the same `try/catch` pattern the file already uses.

### B5. Revalidation

Header runs in the root layout, so its freshness is bounded by each route's `revalidate` window (currently 30–60s across the site). No new `revalidate` setting needed.

---

## Critical files to modify

| File | Change |
| ---- | ------ |
| `src/lib/queries/site-announcements.ts` | **new** — query + WP setup doc block |
| `src/types/site-announcement.ts` | **new** — types |
| `src/lib/utils/site-announcements.ts` | **new** — `pickActiveAnnouncement` selector |
| `src/components/layout/Header.tsx` | wire new query, apply selector, drop Thursday hardcode |
| `CLAUDE.md` (optional) | add a short "Adding a new scheduled announcement" section under Site Settings |

## Reuse

- [src/lib/utils/event-dates.ts](src/lib/utils/event-dates.ts) — `parseTime`, `parseLocalDate`, `DAY_INDEX`. Consider exporting these from there and importing into the new selector to avoid duplication.
- Header's existing `Intl.DateTimeFormat(..., { timeZone: 'America/New_York' })` pattern for the weekday/time lookup.

## Verification

1. **WP side (user):**
   - Go to WP Admin → a new **Announcements** menu item should appear.
   - Add a test post titled `"Test all-day"` with no schedule fields filled — save.
   - Open `https://cms.juicednc.com/graphql` and run:
     ```graphql
     query {
       siteAnnouncements {
         nodes {
           title
           siteAnnouncementFields { eventDate dayOfWeek startTime endTime }
         }
       }
     }
     ```
     The test post should appear.

2. **Next.js side:**
   - `npm run dev` → visit `/` → the test announcement should appear in the utility strip.
   - Edit the post to set `day_of_week = <today>`, `start_time = 00:00`, `end_time = 23:59` — save, wait ~30s, reload → still shows.
   - Change `day_of_week` to a day that isn't today → reload → falls back to the Site Settings default.
   - Add a second announcement with a narrower window and a lower `menu_order` (drag it above the first) → confirm it takes priority while its window is active.
   - Confirm Thursday no longer shows hardcoded "Open Mic Tonight at 8PM!" unless a CPT post explicitly says so.
   - `npx tsc --noEmit` — must pass.

## Out of scope

- Styling changes to the utility strip itself.
- On-demand revalidation webhooks — ISR at 30–60s is acceptable for this feature.
- UI in WP admin for previewing the schedule — the standard CPT list is enough.
