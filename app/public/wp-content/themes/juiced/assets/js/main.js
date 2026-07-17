/**
 * Juiced! theme JS — progressive enhancement helpers.
 *
 * Most interactivity is declared inline with Alpine.js (x-data) directly in the
 * PHP templates. This file holds shared, non-Alpine helpers and the Alpine
 * stores that more than one template-part needs to read.
 */
(function () {
  'use strict';

  /**
   * Events page — filter + layout state.
   *
   * A store rather than an x-data component because two sibling sections use
   * it: the "Find Your Vibe" filter bar (template-parts/events/filters.php)
   * writes it, and the listing below reacts to it. A shared store keeps them
   * independent partials instead of forcing one to nest inside the other's
   * scope just to share three fields.
   *
   * category: 'all' or an event_category term slug.
   * layout:   'grid' or 'list'.
   * query:    the filter bar's search box, matched against each card's haystack.
   *
   * Both sections render fully server-side, so with JS off every event stays
   * visible in the default layout — the filter just stops narrowing.
   */
  document.addEventListener('alpine:init', function () {
    window.Alpine.store('events', {
      category: 'all',
      layout: 'grid',
      query: '',

      /**
       * Should a card show under the current filter? `card` is one entry of
       * the listing's positional array: s = its term slugs, t = its lowercased
       * search haystack.
       */
      matches: function (card) {
        var q = this.query.toLowerCase().trim();
        var okCategory = this.category === 'all' || card.s.indexOf(this.category) !== -1;
        var okQuery = !q || card.t.indexOf(q) !== -1;
        return okCategory && okQuery;
      },
    });

    /**
     * Events page — the "Upcoming Events" listing (template-parts/events/listing.php).
     *
     * The section renders every upcoming event server-side and this component
     * decides which are on screen: those matching the store's category filter,
     * capped at `perPage` until "View More Events" is pressed.
     *
     * `cards` is a positional array — cards[i] describes card i, carrying `s`
     * (its term slugs) and `t` (a lowercased haystack for the search box) — so
     * the markup only has to pass each card its own index.
     *
     * Paging counts filtered cards, not all cards, so "View More" appears when
     * the *current* filter has more than a page worth. Changing filter collapses
     * back to one page: an expanded list from a previous category would open the
     * new one mid-scroll with no way to tell how much had been skipped.
     */
    window.Alpine.data('eventsListing', function (cards, perPage) {
      return {
        expanded: false,

        init: function () {
          this.$watch('$store.events.category', function () {
            this.expanded = false;
          }.bind(this));
          this.$watch('$store.events.query', function () {
            this.expanded = false;
          }.bind(this));
        },

        /** Indices of cards passing the filter, in render order. */
        get matching() {
          var store = this.$store.events;
          return cards.reduce(function (out, card, i) {
            if (store.matches(card)) {
              out.push(i);
            }
            return out;
          }, []);
        },

        get visible() {
          return this.expanded ? this.matching : this.matching.slice(0, perPage);
        },

        isVisible: function (i) {
          return this.visible.indexOf(i) !== -1;
        },

        get hasMore() {
          return this.matching.length > perPage;
        },

        get isEmpty() {
          return this.matching.length === 0;
        },
      };
    });

    /**
     * Events page — the sidebar month calendar (template-parts/events/calendar.php).
     *
     * `months` is juiced_event_calendar_months() output: a run of months from
     * the current one, each carrying its label, length, starting weekday and
     * dots, but not its cells. Building the 42 cells here rather than shipping
     * them keeps the JSON to a fraction of the size for the same grid.
     *
     * Navigation is clamped to the months PHP sent — there is no endpoint to
     * fetch more, so the arrows disable at both ends rather than paging into
     * empty months.
     */
    window.Alpine.data('eventsCalendar', function (months) {
      return {
        months: months,
        i: 0,

        get month() {
          return this.months[this.i];
        },

        get canPrev() {
          return this.i > 0;
        },

        get canNext() {
          return this.i < this.months.length - 1;
        },

        /**
         * The 6x7 grid: trailing days of the previous month, this month, then
         * leading days of the next, so every week row is full.
         */
        get cells() {
          var m = this.month;
          var out = [];
          var d;

          for (d = m.lead; d > 0; d--) {
            out.push({ n: m.prevDays - d + 1, muted: true });
          }
          for (d = 1; d <= m.days; d++) {
            out.push({
              n: d,
              muted: false,
              today: d === m.today,
              dot: m.dots[d] || null,
            });
          }
          for (d = 1; out.length % 7 !== 0; d++) {
            out.push({ n: d, muted: true });
          }
          return out;
        },

        /**
         * Tooltip/screen-reader text for a day: every event on it by name, not
         * just the one the dot links to. The badge says how many there are; this
         * is what says which.
         */
        label: function (cell) {
          if (!cell.dot) {
            return '';
          }
          return [cell.dot.t].concat(cell.dot.m || []).join(', ');
        },
      };
    });

    /**
     * Herb Library page — filter + search state.
     *
     * A store because two sibling sections share it: the hero's search box
     * (template-parts/herbs/hero.php) writes `query` as the visitor types, and
     * the listing below narrows to match. `category` is written by the
     * "Explore Herbs" sidebar inside the listing itself.
     *
     * Both sections render fully server-side, so with JS off every herb stays
     * visible — the hero search then works as a plain GET form instead, and
     * ?herb_search seeds `query` here on load (via x-init on the input).
     */
    window.Alpine.store('herbs', {
      category: 'all',
      query: '',
    });

    /**
     * Herb Library page — the herb grid (template-parts/herbs/listing.php).
     *
     * `cards` is positional — cards[i] belongs to grid item i — with each entry
     * carrying `s` (its herb_category slugs) and `t` (a lowercased haystack of
     * name, description, benefits and term names for the search box to match
     * against). The markup only has to pass each card its own index.
     *
     * Sort is A–Z / Z–A over the server's alphabetical render order, applied
     * with CSS `order` so the DOM never moves.
     */
    window.Alpine.data('herbsLibrary', function (cards, labels) {
      return {
        sort: 'az',

        /** Indices of cards passing the category filter and search, in render order. */
        get matching() {
          var store = this.$store.herbs;
          var q = store.query.toLowerCase().trim();
          return cards.reduce(function (out, card, i) {
            var okCategory = store.category === 'all' || card.s.indexOf(store.category) !== -1;
            var okQuery = !q || card.t.indexOf(q) !== -1;
            if (okCategory && okQuery) {
              out.push(i);
            }
            return out;
          }, []);
        },

        isVisible: function (i) {
          return this.matching.indexOf(i) !== -1;
        },

        orderOf: function (i) {
          return this.sort === 'za' ? cards.length - i : i;
        },

        get isEmpty() {
          return this.matching.length === 0;
        },

        /** "N herbs found" under the heading, pluralized. */
        get countLabel() {
          var n = this.matching.length;
          return n + ' ' + (n === 1 ? labels.one : labels.many);
        },

        /** Section heading: the active category's name, or the "all" label. */
        get heading() {
          var slug = this.$store.herbs.category;
          return slug === 'all' ? labels.all : labels.terms[slug] || labels.all;
        },

        reset: function () {
          this.$store.herbs.category = 'all';
          this.$store.herbs.query = '';
        },
      };
    });
  });
})();
