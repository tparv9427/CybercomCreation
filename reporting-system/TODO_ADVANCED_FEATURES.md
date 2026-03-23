# Advanced File Import & Solr Architecture Plan (Dependency Sorted)

## PHASE 1: Data & Infrastructure Foundations
*These tasks ensure data integrity and reliability. Implementing these first prevents data corruption and ensures a solid base for all reporting.*

### 1. Deduplication Logic (Old #1)
**Current State:** Whenever a CSV is uploaded, `ProcessCsvBatch.php` automatically appends a random `uniqid()`. Re-uploading the same file causes 100% duplication.
**Planned Implementation:** Generate a deterministic hash (e.g., `md5($sku)` or a hash of the entire row). Solr will automatically override documents with existing IDs, fixing duplication at the source.

### 2. Partial Updates / Atomic Updates in Solr (Old #2)
**Current State:** CSV processing acts as a full document replacement, erasing any columns not present in the new file.
**Planned Implementation:** Format JSON fields for Solr Atomic Updates (e.g., `"price_f": {"set": 5.99}`). This allows updating specific columns without wiping existing document data.

### 3. Kafka Topic Partitioning Strategy (Old #19)
**Current State:** Data is sprayed across partitions mindlessly using `RD_KAFKA_PARTITION_UA`.
**Planned Implementation:** Map specific rows to partitions by hashing their source filename or tenant ID. This guarantees ordered chronological processing for atomic updates.

### 4. Kafka Advanced Offset Management (Old #20)
**Current State:** Operates with `auto.commit: true`, trusting processing succeeds before verification.
**Planned Implementation:** Disable Auto-commit. Implement manual offset tracking (`$consumer->commitAsync()`) strictly *after* a successful `flushToSolr()` (200 OK), achieving "Exactly-Once" resilience.

### 5. Pagination & Sorting (Cursor-based) (Old #18)
**Current State:** Basic Offset pagination causes lag when moving past 100,000+ records.
**Planned Implementation:** Refactor `SolrQueryBuilder.php` to utilize Solr's `cursorMark` parameter for instant, linear execution times regardless of depth.

---

## PHASE 2: Core User & Security Infrastructure
*Authentication is the primary blocker for all user-specific features. These must be completed before any personalization or sharing can occur.*

### 6. Authentication Logic (Middleware & API Security) (Old #3)
**Current State:** The API layer is completely public.
**Planned Implementation:** Implement Laravel Sanctum / Bearer Tokens. Secure `api.php` routes and modify the Vue.js frontend to handle and attach tokens to Axios requests.

### 7. Role-Based Access Control (RBAC) & Audit Logs (Old #27)
**Current State:** No concept of organizational access / permissions.
**Planned Implementation:** (Depends on #6) Use `spatie/laravel-permission` to gate dashboard edits and implement SQL Audit Logging to track changes made by specific users.

---

## PHASE 3: Core Reporting & Search Intelligence
*These features improve the core utility of the dashboard and search engine.*

### 8. Real-time Aggregation from Solr (Old #9)
**Current State:** Frontend charts aggregate only the visible 50 rows.
**Planned Implementation:** Link charts to `ReportController.php`'s `facets()` endpoint. Solr will compute statistics across the entire database for 100% accurate global data.

### 9. Pivot Faceting (Old #22)
**Current State:** Only supports 1-dimensional categorical breakdowns.
**Planned Implementation:** Use `facet.pivot` arrays to allow multi-layer tabulation (e.g., Grouping by Brand, then breaking down each brand by Category).

### 10. Solr Linguistic Search (Old #21)
**Current State:** All text fields are treated as immutable strings (`*_s`).
**Planned Implementation:** Map long-form text to `*_t` dynamically in `ImportController.php` to enable tokenized, word-by-word language searching.

### 11. Filter Dropdowns (Single/Multi-Select) (Old #6)
**Current State:** Generic text boxes for every column.
**Planned Implementation:** Detect categorical columns (like Brand) and map them to dynamic menus within `FilterRow.vue`.

### 12. Date & Number Range Filtering (from-to) (Old #7)
**Current State:** `between` operator wrongly renders a single input box.
**Planned Implementation:** Update `FilterRow.vue` to render `start` and `end` sub-inputs, piping them into Solr's `[start TO end]` format.

### 13. Autocomplete Filters from Solr Facets (Old #8)
**Current State:** No auto-suggestions during typing.
**Planned Implementation:** Connect text-inputs to `json.facets` to provide real-time suggestions as the user types.

### 14. Highlighted Snippets (Old #23)
**Current State:** Matches are returned but not visually emphasized.
**Planned Implementation:** Inject `hl=true` into Solr requests and map `<em>` snippets into the `DataTable.vue` DOM.

---

## PHASE 4: User Personalization & Team Features
*Features that rely directly on the Authentication and RBAC infrastructure created in Phase 2.*

### 15. Column Width Adjustment (User-wise Save) (Old #13)
**Current State:** Dimensions drop on page reload.
**Planned Implementation:** (Depends on #6) Save a `column_config` JSON mapping to a MySQL table for each `user_id`, persisting layout across devices.

### 16. Default View per User (Old #14)
**Current State:** Sessions always start with an unfiltered raw view.
**Planned Implementation:** (Depends on #6) Add a `[⭐ Set as Default]` button to Saved Views, storing the `view_id` in the user record.

### 17. Versioning of Views (Old #16)
**Current State:** Saving a view overwrites the previous state forever.
**Planned Implementation:** (Depends on #6) Use an `INSERT` rather than `UPDATE` strategy with a `version` integer, creating a rollback history for complex dashboards.

### 18. Share Views with Team (Old #15)
**Current State:** Saved views are isolated or fully public.
**Planned Implementation:** (Depends on #6 & #7) Implement `team_id` architecture with visibility flags (`Private` vs `Team`).

### 19. Scheduled Reports (Cron + Email) (Old #26)
**Current State:** Manual export only.
**Planned Implementation:** (Depends on #6) Use Laravel Scheduler to run background sweeps and email results to specific user accounts.

---

## PHASE 5: Advanced Analytics & UI Structure
*User-facing tools that build upon the reporting foundations.*

### 20. Chart Drill-down (Click -> Filter) (Old #10)
**Current State:** Charts are static.
**Planned Implementation:** (Depends on #8) Add `onClick` listeners in `Chart.js` to fire `store.addFilter()`, allowing users to "zoom in" on data points.

### 21. Multi-axis Charts & Exporting (Old #11)
**Current State:** Single Y-axis only, no export.
**Planned Implementation:** Configure secondary Y-axes and implement a `canvas.toDataURL()` export button.

### 22. Compare Tool (% Change & Abs. Difference) (Old #12)
**Current State:** Basic side-by-side tables without calculations.
**Planned Implementation:** Add Summary Cards to `ComparisonTool.vue` calculating absolute and percentage differences with green/red trend coloring.

### 23. Column Grouping in Table Selector (Old #4)
**Current State:** One massive alphabetical list of checkboxes.
**Planned Implementation:** Use the semantic `getFieldGroup()` logic to organize columns into sections like `Pricing`, `Identity`, and `Media`.

### 24. Persist User Column Preferences (Old #5)
**Current State:** Columns auto-reset on reload.
**Planned Implementation:** Use `localStorage` to instantly remember column choices across sessions.

---

## PHASE 6: Optimization & Performance
*Final stage polishing for high-traffic or large-data environments.*

### 25. API Caching Layer (Redis) (Old #17)
**Current State:** Every click hits Solr live.
**Planned Implementation:** Integrate Redis to cache identical query outputs for 5-15 minutes, reducing CPU spikes.

### 26. Virtual Scrolling (VueJS) (Old #24)
**Current State:** Standard `v-for` loop on massive datasets causes browser lag.
**Planned Implementation:** Replace with `vue-virtual-scroller` to optimize DOM compute load.

### 27. Debounced & Lazy Loading (Old #25)
**Current State:** Interactions can spam the API.
**Planned Implementation:** Implement Lodash `debounce` and Intersection Observers for lazy loading filter fields.

### 28. Realtime Updates (WebSockets) (Old #28)
**Current State:** Dashboard requires manual refresh to see new data.
**Planned Implementation:** Deploy Laravel Reverb / WebSockets to broadcast "Solr Finished Flushing" events to all active clients.
