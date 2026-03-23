# Advanced File Import & Solr Architecture Plan

## 1. Deduplication Logic
**Current State:** 
Whenever a CSV is uploaded, `ProcessCsvBatch.php` automatically appends a random `uniqid()` to every row. If you upload the same CSV twice, you get 100% duplicate documents in Solr.

**Planned Implementation:**
Instead of a random ID, we need to generate a deterministic hash. We can check if a primary key column exists (like `SKU`, `id`, `part_number`) and hash it (e.g., `md5($sku)`). If no unique column exists, we can hash the entire row contents. 
When Solr receives a document with an ID it already has, it automatically overrides it, instantly fixing data duplication.

## 2. Partial Updates (Atomic Updates in Solr)
**Current State:**
When processing CSV data, we format rows as flat arrays. When Solr receives a flat array, it acts as a **full document replacement**. Any existing columns not included in the new CSV are erased.

**Planned Implementation:**
We must format the JSON fields specifically for Solr's Atomic Updates. In PHP, rather than `"price_f": 5.99`, we structure the array as `"price_f": {"set": 5.99}`. This allows us to upload a CSV containing only Prices and SKUs, updating the price for that SKU without accidentally wiping out the product's image URL or brand name in Solr.

## 3. Authentication Logic (Middleware & API Security)
**Current State:**
The PHP Backend (API Layer) currently processes all incoming search queries, filtering, and data export requests completely unauthenticated (open to the public).

**Planned Implementation:**
Implement an Authentication layer (e.g., Laravel Sanctum or simple Bearer Tokens) to secure the API. This will involve:
- Adding a simple login endpoint.
- Wrapping the `api.php` routes in the `auth:sanctum` (or equivalent) middleware.
- Modifying the Vue.js frontend to securely store the token and attach it as an `Authorization: Bearer <token>` header to all Axios requests.

## 4. Column Grouping in Table Selector
**Current State:**
The actual table column selector (`DataTable.vue`) lists every single dynamically loaded database field as one massive, alphabetical list of checkboxes making it hard to find specific fields.

**Planned Implementation:**
Port the brilliant semantic grouping logic (`getFieldGroup()`) built for our Chart parameters so the main UI column selector displays sections (`Pricing & Margins`, `Identity`, `Media & URLs`, etc.) to streamline column discovery.

## 5. Persist User Column Preferences
**Current State:**
Columns auto-reset exactly to their default view upon a hard page reload (F5) unless the user has actively loaded a formal "Saved View" from the database.

**Planned Implementation:**
Store real-time `selectedColumns` array choices instantly into `localStorage` so a refreshed page instantly remembers what columns you were looking at across sessions.

## 6. Filter Dropdowns (Single/Multi-Select)
**Current State:**
The Filter Builder UI currently uses generic `<input type="text">` fields for every column.

**Planned Implementation:**
Detect categorical columns (like Brand Name) and dynamically map them to standard single or multi-select dropdown menus within `FilterRow.vue`.

## 7. Date & Number Range Filtering (from-to)
**Current State:**
When selecting the `between` operator on Date or Number columns, the UI wrongly renders a single input box.

**Planned Implementation:**
Update `FilterRow.vue` to reactively render `rule.value.start` and `rule.value.end` sub-inputs specifically when `between` is chosen, securely piping that data into the Solr querying format `[start TO end]`.

## 8. Autocomplete Filters from Solr Facets
**Current State:**
When typing a text filter (like `starts with`), there are zero auto-suggestions natively. It requires exact manual typography.

**Planned Implementation:**
Tie the text-input boxes to the Solr API so it instantly queries the `json.facets` functionality under the hood, retrieving real-time autocomplete suggestions as the user types into the query builder.

## 9. Real-time Aggregation from Solr
**Current State:**
The frontend charts (Bar, Pie) currently sum up data purely on the client side using the 50 visible rows currently in the Data Table.

**Planned Implementation:**
Link the Chart generation straight to `ReportController.php`'s `facets()` endpoint. Instead of rendering a chart from the visible page data, the chart will query Solr's powerful aggregation engine across the entire database to render 100% accurate global statistics.

## 10. Chart Drill-down (Click -> Filter)
**Current State:**
The charts are currently visually static and unclickable.

**Planned Implementation:**
Add an `onClick` event listener inside the `Chart.js` configuration. If a user clicks the "Modway" bar in a Bar Chart, it instantly fires `store.addFilter()` to add "Brand = Modway", triggering a global data refresh across the whole dashboard.

## 11. Multi-axis Charts & Exporting
**Current State:**
Charts are single-Y-axis and there is no way to export the `<canvas>` as a PNG format.

**Planned Implementation:**
- Configure secondary Y-axes in `ChartRenderer.vue` for comparing metrics with completely different scales.
- Implement a small `[⬇ Export Image]` button next to the charts utilizing `canvas.toDataURL('image/png')` to download cleanly.

## 12. Compare Tool (% Change & Absolute Difference)
**Current State:**
The Comparison Tool currently displays two raw data tables side-by-side alongside a dual-line projection chart. The explicit mathematical calculations evaluating exactly how much Period B dropped or gained compared to Period A were removed.

**Planned Implementation:**
Add high-level "Summary Cards" strictly above or below the side-by-side data tables in `ComparisonTool.vue`. These cards will aggregate the chosen metrics across Period A and Period B and dynamically output:
- **Absolute Difference** (`Period B Total - Period A Total`)
- **Percentage Change** (`(Period B - Period A) / Period A * 100%`) displaying with distinct green/red UI coloring depending on trend polarity.

## 13. Column Width Adjustment (User-wise Database Save)
**Current State:**
Users can currently resize table columns beautifully with their mouse, but those dimensions drop on page reload. We have no users/authentication database structure built yet to anchor preferences to.

**Planned Implementation:**
Once Authentication (TODO #3) is finished, construct a MySQL database table for user dimensions. Add a generic throttled event listener to Vue's `stopResize()` so that when a user finishes dragging a column, the frontend silently fires a `POST /user/config` payload storing their explicit `user_id`, `report_id`, and exact `column_config` JSON mapping, persisting their layout eternally across any computer they log in with.

## 14. Default View per User
**Current State:**
When a session loads, it invariably starts with the generic, completely unfiltered raw data view.

**Planned Implementation:**
Post-Authentication logic (TODO #3): Add a `[⭐ Set as Default]` button tied to each Saved View. When clicked, it stores that `view_id` directly to the `users` MySQL table. Upon login, the Vue app fetches and instantly hydrates the UI using that custom view automatically.

## 15. Share Views with Team
**Current State:**
Saved views are isolated to whoever built them (or completely public depending on backend looseness). The code currently has zero concept of "Teams" or "Organizations".

**Planned Implementation:**
Implement a backend `team_id` RBAC (Role Based Access Control) architecture. Modify `SavedViews.vue` to allow the creator to toggle visibility flags (`Private` vs `Team`), enabling colleagues to instantly pull up curated team dashboards from their own dropdowns.

## 16. Versioning of Views
**Current State:**
When you click "Save" on an existing Saved View, it structurally overwrites the old JSON payload in the database. The previous filter state is destroyed.

**Planned Implementation:**
Instead of running an `UPDATE` SQL query, run a new `INSERT` query linking the same `view_id` to an auto-incrementing `version` integer. Add a small "History/Rollback" UI showing past edits so users can safely revert complex dashboards without fear of accidental overwrite.

## 17. API Caching Layer (Redis)
**Current State:**
Dashboard statistics hit the Solr instance live on every single click, reload, or metric toggle.

**Planned Implementation:**
Integrate Laravel's explicit `Redis` caching mechanism inside `ReportController.php`. Map an `md5()` signature combining the exact `$queryDetails`, `$facets`, and `$dateRange` parameters to store identical Solr query outputs into RAM for roughly 5 to 15 minutes, drastically lowering CPU spikes on your cluster.

## 18. Pagination & Sorting (Cursor-based Pagination)
**Current State:**
The tables currently rely on basic `$start` (Offset pagination), which triggers noticeable compute lag inside any database when a user paginates past 100,000+ deep records.

**Planned Implementation:**
Refactor the datatable logic and `SolrQueryBuilder.php` architecture to utilize Solr's advanced `cursorMark` parameter, securing instant, linear database execution times universally regardless of depth pagination.

## 19. Kafka Topic Partitioning Strategy
**Current State:**
When importing massive CSV documents, `ProcessCsvBatch.php` sprays data mindlessly across `RD_KAFKA_PARTITION_UA` (any available partition queue automatically). 

**Planned Implementation:**
Map specific rows to explicit partition integers by hashing their source filename or tenant identifier. This explicitly guarantees ordered chronological processing for atomic document updates arriving closely together.

## 20. Kafka Advanced Offset Management
**Current State:**
`KafkaConsumeCommand.php` operates identically using `{enable.auto.commit: 'true'}`, implicitly trusting that messages process without definitively verifying indexing success first.

**Planned Implementation:**
Disable Auto-commit. Implement manual offset tracking (`$consumer->commitAsync()`), selectively triggering the commit signal strictly *after* `flushToSolr()` confirms a successful 200 HTTP code. This achieves enterprise-grade "Exactly-Once" resilience against daemon crashes.

## 21. Solr Linguistic Search (`text_general`)
**Current State:**
The import script explicitly forces all text fields into `*_s` string properties in Solr, matching them as exact immutable strings rather than searchable language tokens.
**Planned Implementation:**
Detect long-form text fields and map them to `*_t` dynamically in `ImportController.php`, enabling tokenized, linguistic word-by-word searching.

## 22. Pivot Faceting
**Current State:**
The UI and API only support 1-dimensional categorical breakdowns.
**Planned Implementation:**
Augment `ReportController.php` to handle `facet.pivot` arrays, allowing multi-layer data tabulation (e.g., grouping by Brand, and then cleanly breaking down each brand by Category underneath).

## 23. Highlighted Snippets
**Current State:**
Searching in the data table successfully returns results, but does not visually emphasize exactly which string matched.
**Planned Implementation:**
Inject `hl=true` into the Solr API request, mapping the returned `<em>highlighted snippets</em>` directly into the Vue `DataTable.vue` DOM so users can clearly see the context of their search query.

## 24. Virtual Scrolling (VueJS)
**Current State:**
`DataTable.vue` simply renders `store.docs` in a massive continuous loop.
**Planned Implementation:**
Replace the standard `v-for` loop with a robust virtual scroller (e.g. `vue-virtual-scroller`), heavily optimizing the browser's DOM compute load when scrolling deeply through the page limits.

## 25. Debounced & Lazy Loading
**Current State:**
Filter interactions and scrolling can potentially spam the API synchronously.
**Planned Implementation:**
Implement Lodash `debounce` around the `store.fetchData()` triggers and add Intersection Observers for lazy loading dropdown filter fields until the user actually scrolls to them.

## 26. Scheduled Reports (Cron + Email)
**Current State:**
The exporting system is manual-trigger only via the frontend CSV button.
**Planned Implementation:**
Build a Laravel schedule (`Console/Kernel.php`) executing automated background sweeps to generate specific heavy Solr queries and dispatch the results via Laravel Mailable emails to executives every morning.

## 27. Role-Based Access Control (RBAC) & Audit Logs
**Current State:**
As documented in the Authentication task (TODO #3), the system is currently completely public without organizational access layers.
**Planned Implementation:**
Post-Authentication: Implement Spatie Laravel-Permissions to gate complex dashboard edits, whilst adding automated SQL Audit Logging (triggering whenever a user modifies a dashboard configuration).

## 28. Realtime Updates (WebSockets)
**Current State:**
If a Kafka daemon streams 100,000 new rows into Solr, the dashboard UI has no idea unless the user physically hits the Refresh button.
**Planned Implementation:**
Deploy Laravel Reverb / WebSockets tied dynamically to the VueJS dashboard. Whenever `KafkaConsumeCommand.php` finishes a Solr flush, emit a broadcaster event to universally instruct all active Vue clients to cleanly re-fetch their aggregation charts without any manual user intervention.
