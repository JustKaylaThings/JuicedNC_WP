/**
 * Juiced! theme JS — progressive enhancement helpers.
 *
 * Most interactivity is declared inline with Alpine.js (x-data) directly in the
 * PHP templates. This file holds shared, non-Alpine helpers and the Alpine
 * stores that more than one template-part needs to read.
 */
(function () {
  'use strict';

  document.addEventListener('alpine:init', function () {
    /**
     * Site-wide email sign-up popup (template-parts/newsletter-popup.php).
     *
     * Opens once, after OPEN_DELAY or when the visitor scrolls past
     * OPEN_SCROLL of the page, whichever comes first. Closing it snoozes it
     * for SNOOZE_DAYS; signing up (here or in the footer band, which comes
     * back with ?joined=yes) hides it for good. The memory lives in
     * localStorage, so with storage blocked the popup shows each visit.
     */
    var POPUP_KEY = 'juiced_signup';
    var OPEN_DELAY = 12000;
    var OPEN_SCROLL = 0.4;
    var SNOOZE_DAYS = 14;

    function popupMemory(value) {
      try {
        if (value === undefined) {
          return window.localStorage.getItem(POPUP_KEY);
        }
        window.localStorage.setItem(POPUP_KEY, value);
      } catch (e) {
        return null;
      }
    }

    window.Alpine.data('signupPopup', function () {
      return {
        open: false,
        state: 'idle', // idle | sending | error | done
        lastFocus: null,

        init: function () {
          var self = this;

          if (/[?&]joined=yes\b/.test(window.location.search)) {
            popupMemory('joined');
          }
          var seen = popupMemory();
          if (seen === 'joined' || (seen && Date.now() < Number(seen))) {
            return;
          }

          var timer;
          var onScroll = function () {
            var max = document.documentElement.scrollHeight - window.innerHeight;
            if (max > 0 && window.scrollY / max >= OPEN_SCROLL) {
              show();
            }
          };
          var show = function () {
            clearTimeout(timer);
            window.removeEventListener('scroll', onScroll);
            self.show();
          };
          timer = setTimeout(show, OPEN_DELAY);
          window.addEventListener('scroll', onScroll, { passive: true });
        },

        show: function () {
          var self = this;
          this.lastFocus = document.activeElement;
          this.open = true;
          this.$nextTick(function () {
            if (self.$refs.email) {
              self.$refs.email.focus();
            }
          });
        },

        dismiss: function () {
          if (!this.open) {
            return;
          }
          this.open = false;
          if (this.state !== 'done') {
            popupMemory(String(Date.now() + SNOOZE_DAYS * 864e5));
          }
          if (this.lastFocus && this.lastFocus.focus) {
            this.lastFocus.focus();
          }
        },

        /** Keep Tab inside the dialog while it is open. */
        trap: function (e) {
          var items = Array.prototype.filter.call(
            this.$refs.dialog.querySelectorAll('a[href], button:not([disabled]), input:not([type="hidden"]):not([tabindex="-1"])'),
            function (el) { return el.offsetParent !== null; }
          );
          if (!items.length) {
            return;
          }
          var first = items[0];
          var last = items[items.length - 1];
          if (e.shiftKey && document.activeElement === first) {
            e.preventDefault();
            last.focus();
          } else if (!e.shiftKey && document.activeElement === last) {
            e.preventDefault();
            first.focus();
          }
        },

        submit: function (form) {
          var self = this;
          var body = new FormData(form);
          body.append('ajax', '1');
          this.state = 'sending';

          // getAttribute, not form.action: the hidden <input name="action"> shadows
          // the property, which would post to "[object HTMLInputElement]".
          fetch(form.getAttribute('action'), { method: 'POST', body: body, credentials: 'same-origin' })
            .then(function (res) { return res.json(); })
            .then(function (json) {
              if (!json || !json.success) {
                throw new Error('signup failed');
              }
              self.state = 'done';
              popupMemory('joined');
            })
            .catch(function () {
              self.state = 'error';
              // Turnstile tokens are single-use — fetch a fresh one for the retry.
              var widget = form.querySelector('.cf-turnstile');
              if (widget && window.turnstile) {
                window.turnstile.reset(widget);
              }
            });
        },
      };
    });

    /**
     * Events page — the planner (template-parts/events/planner.php): a month
     * grid where clicking a day lists what's on it.
     *
     * `data` is juiced_event_planner_data() output. `events` is a table of every
     * event; each month's `ev` maps a day number to indexes into it, so a
     * weekly event sits in the JSON once however many days it dots. Building
     * the cells here rather than shipping them keeps the payload small.
     *
     * `i` is the month on screen; `sel` the picked day, which can sit in a
     * different month once the visitor pages away. Navigation is clamped to the
     * months PHP sent — there is no endpoint to fetch more.
     */
    window.Alpine.data('eventsPlanner', function (data) {
      var lang = document.documentElement.lang || undefined;

      return {
        months: data.months,
        events: data.events,
        i: 0,
        sel: { i: 0, d: data.months[0].today || 1 },

        get month() {
          return this.months[this.i];
        },

        get canPrev() {
          return this.i > 0;
        },

        get canNext() {
          return this.i < this.months.length - 1;
        },

        /** Event indexes on day `d` of month `mi`. */
        on: function (mi, d) {
          return this.months[mi].ev[d] || [];
        },

        /** "Monday, October 5" for day `d` of month `mi`. */
        dateLabel: function (mi, d) {
          var m = this.months[mi];
          return new Date(m.y, m.m - 1, d).toLocaleDateString(lang, {
            weekday: 'long', month: 'long', day: 'numeric',
          });
        },

        /**
         * Full weeks: trailing days of the previous month, this month, then
         * leading days of the next. Each real day carries one dot per event
         * (capped at three) — true for a one-off, false for a recurring event.
         */
        get cells() {
          var self = this;
          var m = this.month;
          var out = [];
          var d;

          for (d = m.lead; d > 0; d--) {
            out.push({ n: m.prevDays - d + 1, muted: true });
          }
          for (d = 1; d <= m.days; d++) {
            var ids = this.on(this.i, d);
            var titles = ids.map(function (k) { return self.events[k].t; });
            out.push({
              n: d,
              muted: false,
              today: d === m.today,
              on: this.sel.i === this.i && this.sel.d === d,
              dots: ids.slice(0, 3).map(function (k) { return self.events[k].s; }),
              label: this.dateLabel(this.i, d) + (titles.length ? ': ' + titles.join(', ') : ''),
            });
          }
          for (d = 1; out.length % 7 !== 0; d++) {
            out.push({ n: d, muted: true });
          }
          return out;
        },

        pick: function (d) {
          this.sel = { i: this.i, d: d };
        },

        get selLabel() {
          return this.dateLabel(this.sel.i, this.sel.d);
        },

        get selEvents() {
          var self = this;
          return this.on(this.sel.i, this.sel.d).map(function (k) { return self.events[k]; });
        },

        /** The first day after the picked one that has an event, or null. */
        get nextDay() {
          for (var mi = this.sel.i; mi < this.months.length; mi++) {
            var start = mi === this.sel.i ? this.sel.d + 1 : 1;
            for (var d = start; d <= this.months[mi].days; d++) {
              if (this.on(mi, d).length) {
                return { i: mi, d: d, label: this.dateLabel(mi, d) };
              }
            }
          }
          return null;
        },

        jump: function () {
          var next = this.nextDay;
          if (next) {
            this.i = next.i;
            this.sel = { i: next.i, d: next.d };
          }
        },

        /** "7:00 PM – 11:00 PM · 434 Hill Street · Free" */
        meta: function (ev) {
          return [ev.h, ev.l, ev.p].filter(Boolean).join(' · ');
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

    /**
     * Careers page — the three-step job application
     * (template-parts/careers/apply.php).
     *
     * Each step is a fieldset with x-ref="stepN". Next only moves on once the
     * step's own fields pass the browser's validation, so a missing required
     * field is flagged where it is rather than on the last step. Moving focuses
     * the new step's legend, which also scrolls it into view on phones.
     */
    window.Alpine.data('jobApplication', function () {
      return {
        step: 1,

        next: function () {
          var stepEl = this.$refs['step' + this.step];
          // Checkbox groups marked data-required-group need at least one tick;
          // an empty one shows its inline message and takes focus.
          var groups = stepEl.querySelectorAll('[data-required-group]');
          for (var g = 0; g < groups.length; g++) {
            var empty = !groups[g].querySelector('input:checked');
            groups[g].querySelector('[data-group-error]').hidden = !empty;
            if (empty) {
              groups[g].querySelector('input').focus();
              return;
            }
          }
          var fields = stepEl.querySelectorAll('input, select, textarea');
          for (var i = 0; i < fields.length; i++) {
            if (!fields[i].reportValidity()) {
              return;
            }
          }
          this.go(this.step + 1);
        },

        back: function () {
          this.go(this.step - 1);
        },

        go: function (n) {
          var self = this;
          this.step = n;
          this.$nextTick(function () {
            self.$refs['step' + n].querySelector('legend').focus();
          });
        },
      };
    });
  });
})();

/**
 * Menu page: keep the section bar (.menu-section-bar) pinned just under the
 * sticky site header, whose height changes as it compacts on scroll.
 */
(function () {
  var header = document.querySelector('.site-header');
  if (!header || !document.querySelector('.menu-section-bar') || !window.ResizeObserver) {
    return;
  }
  new ResizeObserver(function () {
    document.documentElement.style.setProperty('--site-header-h', header.offsetHeight + 'px');
  }).observe(header);
})();
