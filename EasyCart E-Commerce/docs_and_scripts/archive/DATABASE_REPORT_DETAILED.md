# Comprehensive Database Schema Report - EasyCart E-Commerce

This report provides a granular analysis of every table in the EasyCart database, including schemas, dependencies, and recommended improvements.

## Table: `brands`

### Description
No description available.

### Table Schema
| Column | Data Type | Nullable | Default | PK | FK | Note |
| :--- | :--- | :--- | :--- | :--- | :--- | :--- |
| `id` | integer | NO |  | ✅ |  | |
| `id` | integer | NO | `nextval('brands_id_seq'::regclass)` | ✅ |  | |
| `name` | character varying | NO |  |  |  | |
| `name` | character varying | NO |  |  |  | |

### Dependencies (Depends On)
None.

### Dependents (Others Depending on this Table)
None.

### Recommended Changes
- Standardize ID naming, ensure updated_at triggers exist.

---

## Table: `cache`

### Description
No description available.

### Table Schema
| Column | Data Type | Nullable | Default | PK | FK | Note |
| :--- | :--- | :--- | :--- | :--- | :--- | :--- |
| `key` | character varying | NO |  | ✅ |  | |
| `value` | text | NO |  |  |  | |
| `expiration` | integer | NO |  |  |  | |

### Dependencies (Depends On)
None.

### Dependents (Others Depending on this Table)
None.

### Recommended Changes
- Standardize ID naming, ensure updated_at triggers exist.

---

## Table: `cache_locks`

### Description
No description available.

### Table Schema
| Column | Data Type | Nullable | Default | PK | FK | Note |
| :--- | :--- | :--- | :--- | :--- | :--- | :--- |
| `key` | character varying | NO |  | ✅ |  | |
| `owner` | character varying | NO |  |  |  | |
| `expiration` | integer | NO |  |  |  | |

### Dependencies (Depends On)
None.

### Dependents (Others Depending on this Table)
None.

### Recommended Changes
- Standardize ID naming, ensure updated_at triggers exist.

---

## Table: `cart_items`

### Description
No description available.

### Table Schema
| Column | Data Type | Nullable | Default | PK | FK | Note |
| :--- | :--- | :--- | :--- | :--- | :--- | :--- |
| `id` | integer | NO | `nextval('cart_items_id_seq'::regclass)` | ✅ |  | |
| `cart_id` | integer | YES |  |  |  | |
| `product_id` | integer | YES |  |  |  | |
| `quantity` | integer | YES | `1` |  |  | |
| `created_at` | timestamp without time zone | YES | `CURRENT_TIMESTAMP` |  |  | |

### Dependencies (Depends On)
None.

### Dependents (Others Depending on this Table)
None.

### Recommended Changes
- Standardize ID naming, ensure updated_at triggers exist.

---

## Table: `catalog_category_attribute`

### Description
Flexible category attributes

### Table Schema
| Column | Data Type | Nullable | Default | PK | FK | Note |
| :--- | :--- | :--- | :--- | :--- | :--- | :--- |
| `attribute_id` | bigint | NO | `nextval('catalog_category_attribute_attribute_id_seq'::regclass)` | ✅ |  | |
| `attribute_id` | bigint | NO |  | ✅ |  | |
| `category_entity_id` | bigint | NO |  |  | 🔗 catalog_category_entity(entity_id) | |
| `category_entity_id` | bigint | NO |  |  | 🔗 catalog_category_entity(entity_id) | |
| `attribute_code` | character varying | NO |  |  |  | |
| `attribute_code` | character varying | NO |  |  |  | |
| `attribute_value` | text | NO |  |  |  | |
| `attribute_value` | text | NO |  |  |  | |
| `created_at` | timestamp without time zone | NO | `CURRENT_TIMESTAMP` |  |  | |
| `created_at` | timestamp without time zone | YES |  |  |  | |

### Dependencies (Depends On)
- `catalog_category_entity`

### Dependents (Others Depending on this Table)
None.

### Recommended Changes
- Standardize ID naming, ensure updated_at triggers exist.

---

## Table: `catalog_category_entity`

### Description
Core category information

### Table Schema
| Column | Data Type | Nullable | Default | PK | FK | Note |
| :--- | :--- | :--- | :--- | :--- | :--- | :--- |
| `entity_id` | bigint | NO | `nextval('catalog_category_entity_entity_id_seq'::regclass)` | ✅ |  | |
| `entity_id` | bigint | NO |  | ✅ |  | |
| `name` | character varying | NO |  |  |  | |
| `name` | character varying | NO |  |  |  | |
| `slug` | character varying | NO |  |  |  | |
| `slug` | character varying | NO |  |  |  | |
| `parent_id` | bigint | YES |  |  | 🔗 catalog_category_entity(entity_id) | |
| `parent_id` | bigint | YES |  |  | 🔗 catalog_category_entity(entity_id) | |
| `position` | integer | NO |  |  |  | |
| `position` | integer | YES |  |  |  | |
| `is_active` | boolean | NO | `true` |  |  | |
| `is_active` | boolean | YES | `true` |  |  | |
| `created_at` | timestamp without time zone | YES |  |  |  | |
| `created_at` | timestamp without time zone | YES |  |  |  | |
| `updated_at` | timestamp without time zone | YES |  |  |  | |
| `updated_at` | timestamp without time zone | YES |  |  |  | |

### Dependencies (Depends On)
- `catalog_category_entity`

### Dependents (Others Depending on this Table)
- `catalog_category_entity`
- `catalog_category_attribute`
- `catalog_category_product`

### Recommended Changes
- Standardize ID naming, ensure updated_at triggers exist.

---

## Table: `catalog_category_product`

### Description
Many-to-many relationship between categories and products

### Table Schema
| Column | Data Type | Nullable | Default | PK | FK | Note |
| :--- | :--- | :--- | :--- | :--- | :--- | :--- |
| `category_entity_id` | bigint | NO |  |  | 🔗 catalog_category_entity(entity_id) | |
| `relation_id` | bigint | NO | `nextval('catalog_category_product_relation_id_seq'::regclass)` | ✅ |  | |
| `category_entity_id` | bigint | NO |  |  | 🔗 catalog_category_entity(entity_id) | |
| `product_entity_id` | bigint | NO |  |  | 🔗 catalog_product_entity(entity_id) | |
| `position` | integer | YES |  |  |  | |
| `product_entity_id` | bigint | NO |  |  | 🔗 catalog_product_entity(entity_id) | |
| `position` | integer | NO |  |  |  | |
| `created_at` | timestamp without time zone | YES |  |  |  | |
| `created_at` | timestamp without time zone | NO | `CURRENT_TIMESTAMP` |  |  | |

### Dependencies (Depends On)
- `catalog_category_entity`
- `catalog_product_entity`

### Dependents (Others Depending on this Table)
None.

### Recommended Changes
- Standardize ID naming, ensure updated_at triggers exist.

---

## Table: `catalog_product_attribute`

### Description
Flexible product attributes (color, size, brand, etc.)

### Table Schema
| Column | Data Type | Nullable | Default | PK | FK | Note |
| :--- | :--- | :--- | :--- | :--- | :--- | :--- |
| `attribute_id` | bigint | NO |  | ✅ |  | |
| `attribute_id` | bigint | NO | `nextval('catalog_product_attribute_attribute_id_seq'::regclass)` | ✅ |  | |
| `product_entity_id` | bigint | NO |  |  | 🔗 catalog_product_entity(entity_id) | |
| `product_entity_id` | bigint | NO |  |  | 🔗 catalog_product_entity(entity_id) | |
| `attribute_code` | character varying | NO |  |  |  | |
| `attribute_code` | character varying | NO |  |  |  | |
| `attribute_value` | text | NO |  |  |  | |
| `attribute_value` | text | NO |  |  |  | |
| `created_at` | timestamp without time zone | YES |  |  |  | |
| `created_at` | timestamp without time zone | NO | `CURRENT_TIMESTAMP` |  |  | |

### Dependencies (Depends On)
- `catalog_product_entity`

### Dependents (Others Depending on this Table)
None.

### Recommended Changes
- Standardize ID naming, ensure updated_at triggers exist.

---

## Table: `catalog_product_entity`

### Description
Core product information (EAV entity)

### Table Schema
| Column | Data Type | Nullable | Default | PK | FK | Note |
| :--- | :--- | :--- | :--- | :--- | :--- | :--- |
| `entity_id` | bigint | NO | `nextval('catalog_product_entity_entity_id_seq'::regclass)` | ✅ |  | |
| `entity_id` | bigint | NO |  | ✅ |  | |
| `sku` | character varying | NO |  |  |  | |
| `sku` | character varying | NO |  |  |  | |
| `name` | character varying | NO |  |  |  | |
| `name` | character varying | NO |  |  |  | |
| `price` | numeric | NO |  |  |  | |
| `price` | numeric | NO |  |  |  | |
| `stock` | integer | YES |  |  |  | |
| `original_price` | numeric | YES |  |  |  | |
| `is_active` | boolean | YES | `true` |  |  | |
| `stock` | integer | NO |  |  |  | |
| `description` | text | YES |  |  |  | |
| `created_at` | timestamp without time zone | YES |  |  |  | |
| `updated_at` | timestamp without time zone | YES |  |  |  | |
| `is_featured` | boolean | NO | `false` |  |  | |
| `is_new` | boolean | NO | `false` |  |  | |
| `rating` | numeric | NO | `'0'::numeric` |  |  | |
| `reviews_count` | integer | NO |  |  |  | |
| `created_at` | timestamp without time zone | YES |  |  |  | |
| `updated_at` | timestamp without time zone | YES |  |  |  | |
| `is_active` | boolean | NO | `true` |  |  | |

### Dependencies (Depends On)
None.

### Dependents (Others Depending on this Table)
- `product_reviews`
- `catalog_product_attribute`
- `catalog_product_image`
- `catalog_category_product`
- `sales_cart_product`
- `sales_order_product`
- `catalog_wishlist`

### Recommended Changes
- Standardize ID naming, ensure updated_at triggers exist.

---

## Table: `catalog_product_image`

### Description
Product images - avoids comma-separated values

### Table Schema
| Column | Data Type | Nullable | Default | PK | FK | Note |
| :--- | :--- | :--- | :--- | :--- | :--- | :--- |
| `image_id` | bigint | NO |  | ✅ |  | |
| `image_id` | bigint | NO | `nextval('catalog_product_image_image_id_seq'::regclass)` | ✅ |  | |
| `product_entity_id` | bigint | NO |  |  | 🔗 catalog_product_entity(entity_id) | |
| `product_entity_id` | bigint | NO |  |  | 🔗 catalog_product_entity(entity_id) | |
| `image_path` | character varying | NO |  |  |  | |
| `image_path` | character varying | NO |  |  |  | |
| `image_position` | integer | YES |  |  |  | |
| `image_position` | integer | NO |  |  |  | |
| `is_primary` | boolean | YES | `false` |  |  | |
| `is_primary` | boolean | NO | `false` |  |  | |
| `created_at` | timestamp without time zone | YES |  |  |  | |
| `created_at` | timestamp without time zone | NO | `CURRENT_TIMESTAMP` |  |  | |

### Dependencies (Depends On)
- `catalog_product_entity`

### Dependents (Others Depending on this Table)
None.

### Recommended Changes
- Standardize ID naming, ensure updated_at triggers exist.

---

## Table: `catalog_wishlist`

### Description
No description available.

### Table Schema
| Column | Data Type | Nullable | Default | PK | FK | Note |
| :--- | :--- | :--- | :--- | :--- | :--- | :--- |
| `wishlist_id` | bigint | NO | `nextval('catalog_wishlist_wishlist_id_seq'::regclass)` | ✅ |  | |
| `wishlist_id` | bigint | NO |  | ✅ |  | |
| `user_id` | bigint | NO |  |  | 🔗 users(id) | |
| `user_id` | bigint | NO |  |  | 🔗 users(id) | |
| `product_entity_id` | bigint | NO |  |  | 🔗 catalog_product_entity(entity_id) | |
| `product_entity_id` | bigint | NO |  |  | 🔗 catalog_product_entity(entity_id) | |
| `created_at` | timestamp without time zone | YES |  |  |  | |
| `created_at` | timestamp without time zone | YES |  |  |  | |
| `updated_at` | timestamp without time zone | YES |  |  |  | |

### Dependencies (Depends On)
- `users`
- `catalog_product_entity`

### Dependents (Others Depending on this Table)
None.

### Recommended Changes
- Standardize ID naming, ensure updated_at triggers exist.

---

## Table: `coupons`

### Description
No description available.

### Table Schema
| Column | Data Type | Nullable | Default | PK | FK | Note |
| :--- | :--- | :--- | :--- | :--- | :--- | :--- |
| `id` | integer | NO | `nextval('coupons_id_seq'::regclass)` | ✅ |  | |
| `code` | character varying | NO |  |  |  | |
| `discount_percent` | integer | NO |  |  |  | |
| `created_at` | timestamp without time zone | YES | `CURRENT_TIMESTAMP` |  |  | |

### Dependencies (Depends On)
None.

### Dependents (Others Depending on this Table)
None.

### Recommended Changes
- Standardize ID naming, ensure updated_at triggers exist.

---

## Table: `failed_jobs`

### Description
No description available.

### Table Schema
| Column | Data Type | Nullable | Default | PK | FK | Note |
| :--- | :--- | :--- | :--- | :--- | :--- | :--- |
| `id` | bigint | NO | `nextval('failed_jobs_id_seq'::regclass)` | ✅ |  | |
| `uuid` | character varying | NO |  |  |  | |
| `connection` | text | NO |  |  |  | |
| `queue` | text | NO |  |  |  | |
| `payload` | text | NO |  |  |  | |
| `exception` | text | NO |  |  |  | |
| `failed_at` | timestamp without time zone | NO | `CURRENT_TIMESTAMP` |  |  | |

### Dependencies (Depends On)
None.

### Dependents (Others Depending on this Table)
None.

### Recommended Changes
- Standardize ID naming, ensure updated_at triggers exist.

---

## Table: `job_batches`

### Description
No description available.

### Table Schema
| Column | Data Type | Nullable | Default | PK | FK | Note |
| :--- | :--- | :--- | :--- | :--- | :--- | :--- |
| `id` | character varying | NO |  | ✅ |  | |
| `name` | character varying | NO |  |  |  | |
| `total_jobs` | integer | NO |  |  |  | |
| `pending_jobs` | integer | NO |  |  |  | |
| `failed_jobs` | integer | NO |  |  |  | |
| `failed_job_ids` | text | NO |  |  |  | |
| `options` | text | YES |  |  |  | |
| `cancelled_at` | integer | YES |  |  |  | |
| `created_at` | integer | NO |  |  |  | |
| `finished_at` | integer | YES |  |  |  | |

### Dependencies (Depends On)
None.

### Dependents (Others Depending on this Table)
None.

### Recommended Changes
- Standardize ID naming, ensure updated_at triggers exist.

---

## Table: `jobs`

### Description
No description available.

### Table Schema
| Column | Data Type | Nullable | Default | PK | FK | Note |
| :--- | :--- | :--- | :--- | :--- | :--- | :--- |
| `id` | bigint | NO | `nextval('jobs_id_seq'::regclass)` | ✅ |  | |
| `queue` | character varying | NO |  |  |  | |
| `payload` | text | NO |  |  |  | |
| `attempts` | smallint | NO |  |  |  | |
| `reserved_at` | integer | YES |  |  |  | |
| `available_at` | integer | NO |  |  |  | |
| `created_at` | integer | NO |  |  |  | |

### Dependencies (Depends On)
None.

### Dependents (Others Depending on this Table)
None.

### Recommended Changes
- Standardize ID naming, ensure updated_at triggers exist.

---

## Table: `migrations`

### Description
No description available.

### Table Schema
| Column | Data Type | Nullable | Default | PK | FK | Note |
| :--- | :--- | :--- | :--- | :--- | :--- | :--- |
| `id` | integer | NO | `nextval('migrations_id_seq'::regclass)` | ✅ |  | |
| `migration` | character varying | NO |  |  |  | |
| `batch` | integer | NO |  |  |  | |

### Dependencies (Depends On)
None.

### Dependents (Others Depending on this Table)
None.

### Recommended Changes
- Standardize ID naming, ensure updated_at triggers exist.

---

## Table: `password_reset_tokens`

### Description
No description available.

### Table Schema
| Column | Data Type | Nullable | Default | PK | FK | Note |
| :--- | :--- | :--- | :--- | :--- | :--- | :--- |
| `email` | character varying | NO |  | ✅ |  | |
| `token` | character varying | NO |  |  |  | |
| `created_at` | timestamp without time zone | YES |  |  |  | |

### Dependencies (Depends On)
None.

### Dependents (Others Depending on this Table)
None.

### Recommended Changes
- Standardize ID naming, ensure updated_at triggers exist.

---

## Table: `product_reviews`

### Description
No description available.

### Table Schema
| Column | Data Type | Nullable | Default | PK | FK | Note |
| :--- | :--- | :--- | :--- | :--- | :--- | :--- |
| `review_id` | integer | NO | `nextval('product_reviews_review_id_seq'::regclass)` | ✅ |  | |
| `review_id` | integer | NO |  | ✅ |  | |
| `product_entity_id` | integer | NO |  |  | 🔗 catalog_product_entity(entity_id) | |
| `product_entity_id` | bigint | NO |  |  | 🔗 catalog_product_entity(entity_id) | |
| `user_id` | integer | NO |  |  | 🔗 users(id) | |
| `user_id` | bigint | NO |  |  | 🔗 users(id) | |
| `rating` | integer | NO |  |  |  | |
| `rating` | integer | YES |  |  |  | |
| `comment` | text | YES |  |  |  | |
| `comment` | text | YES |  |  |  | |
| `is_approved` | boolean | YES | `true` |  |  | |
| `is_approved` | boolean | YES | `true` |  |  | |
| `created_at` | timestamp without time zone | YES | `CURRENT_TIMESTAMP` |  |  | |
| `created_at` | timestamp without time zone | YES |  |  |  | |

### Dependencies (Depends On)
- `catalog_product_entity`
- `users`

### Dependents (Others Depending on this Table)
None.

### Recommended Changes
- Standardize ID naming, ensure updated_at triggers exist.

---

## Table: `sales_cart`

### Description
Shopping cart header - cart_id stored in session

### Table Schema
| Column | Data Type | Nullable | Default | PK | FK | Note |
| :--- | :--- | :--- | :--- | :--- | :--- | :--- |
| `cart_id` | bigint | NO |  | ✅ |  | |
| `cart_id` | bigint | NO | `nextval('sales_cart_cart_id_seq'::regclass)` | ✅ |  | |
| `user_id` | bigint | YES |  |  | 🔗 users(id) | |
| `user_id` | bigint | YES |  |  | 🔗 users(id) | |
| `session_id` | character varying | YES |  |  |  | |
| `session_id` | character varying | YES |  |  |  | |
| `status` | character varying | YES | `'active'::character varying` |  |  | |
| `is_active` | boolean | NO | `true` |  |  | |
| `created_at` | timestamp without time zone | YES |  |  |  | |
| `created_at` | timestamp without time zone | YES |  |  |  | |
| `updated_at` | timestamp without time zone | YES |  |  |  | |
| `updated_at` | timestamp without time zone | YES |  |  |  | |

### Dependencies (Depends On)
- `users`

### Dependents (Others Depending on this Table)
- `sales_cart_product`
- `sales_cart_address`
- `sales_cart_payment`
- `sales_order`

### Recommended Changes
- Standardize ID naming, ensure updated_at triggers exist.

---

## Table: `sales_cart_address`

### Description
Cart shipping and billing addresses

### Table Schema
| Column | Data Type | Nullable | Default | PK | FK | Note |
| :--- | :--- | :--- | :--- | :--- | :--- | :--- |
| `address_id` | bigint | NO | `nextval('sales_cart_address_address_id_seq'::regclass)` | ✅ |  | |
| `cart_id` | bigint | NO |  |  | 🔗 sales_cart(cart_id) | |
| `address_type` | character varying | NO |  |  |  | |
| `first_name` | character varying | NO |  |  |  | |
| `last_name` | character varying | NO |  |  |  | |
| `email` | character varying | NO |  |  |  | |
| `phone` | character varying | YES |  |  |  | |
| `address_line_one` | character varying | NO |  |  |  | |
| `address_line_two` | character varying | YES |  |  |  | |
| `city` | character varying | NO |  |  |  | |
| `state` | character varying | NO |  |  |  | |
| `postal_code` | character varying | NO |  |  |  | |
| `country` | character varying | NO |  |  |  | |
| `created_at` | timestamp without time zone | YES |  |  |  | |
| `updated_at` | timestamp without time zone | YES |  |  |  | |

### Dependencies (Depends On)
- `sales_cart`

### Dependents (Others Depending on this Table)
None.

### Recommended Changes
- Standardize ID naming, ensure updated_at triggers exist.

---

## Table: `sales_cart_payment`

### Description
Cart payment and shipping method selection

### Table Schema
| Column | Data Type | Nullable | Default | PK | FK | Note |
| :--- | :--- | :--- | :--- | :--- | :--- | :--- |
| `payment_id` | bigint | NO | `nextval('sales_cart_payment_payment_id_seq'::regclass)` | ✅ |  | |
| `cart_id` | bigint | NO |  |  | 🔗 sales_cart(cart_id) | |
| `shipping_method` | character varying | YES |  |  |  | |
| `payment_method` | character varying | YES |  |  |  | |
| `created_at` | timestamp without time zone | YES |  |  |  | |
| `updated_at` | timestamp without time zone | YES |  |  |  | |

### Dependencies (Depends On)
- `sales_cart`

### Dependents (Others Depending on this Table)
None.

### Recommended Changes
- Standardize ID naming, ensure updated_at triggers exist.

---

## Table: `sales_cart_product`

### Description
Cart line items

### Table Schema
| Column | Data Type | Nullable | Default | PK | FK | Note |
| :--- | :--- | :--- | :--- | :--- | :--- | :--- |
| `item_id` | bigint | NO |  | ✅ |  | |
| `item_id` | bigint | NO | `nextval('sales_cart_product_item_id_seq'::regclass)` | ✅ |  | |
| `cart_id` | bigint | NO |  |  | 🔗 sales_cart(cart_id) | |
| `cart_id` | bigint | NO |  |  | 🔗 sales_cart(cart_id) | |
| `product_entity_id` | bigint | NO |  |  | 🔗 catalog_product_entity(entity_id) | |
| `product_entity_id` | bigint | NO |  |  | 🔗 catalog_product_entity(entity_id) | |
| `quantity` | integer | NO |  |  |  | |
| `quantity` | integer | NO | `1` |  |  | |
| `price_snapshot` | numeric | YES |  |  |  | |
| `created_at` | timestamp without time zone | YES |  |  |  | |
| `created_at` | timestamp without time zone | YES |  |  |  | |
| `updated_at` | timestamp without time zone | YES |  |  |  | |

### Dependencies (Depends On)
- `sales_cart`
- `catalog_product_entity`

### Dependents (Others Depending on this Table)
None.

### Recommended Changes
- Standardize ID naming, ensure updated_at triggers exist.

---

## Table: `sales_order`

### Description
Order header - created from inactive cart

### Table Schema
| Column | Data Type | Nullable | Default | PK | FK | Note |
| :--- | :--- | :--- | :--- | :--- | :--- | :--- |
| `order_id` | bigint | NO |  | ✅ |  | |
| `order_id` | bigint | NO | `nextval('sales_order_order_id_seq'::regclass)` | ✅ |  | |
| `order_number` | character varying | NO |  |  |  | |
| `order_number` | character varying | NO |  |  |  | |
| `user_id` | bigint | YES |  |  | 🔗 users(id) | |
| `user_id` | bigint | YES |  |  | 🔗 users(id) | |
| `original_cart_id` | bigint | YES |  |  | 🔗 sales_cart(cart_id) | |
| `original_cart_id` | bigint | YES |  |  | 🔗 sales_cart(cart_id) | |
| `subtotal` | numeric | YES |  |  |  | |
| `subtotal` | numeric | NO |  |  |  | |
| `tax` | numeric | YES |  |  |  | |
| `shipping_cost` | numeric | NO | `'0'::numeric` |  |  | |
| `tax` | numeric | NO | `'0'::numeric` |  |  | |
| `discount` | numeric | YES |  |  |  | |
| `total` | numeric | YES |  |  |  | |
| `discount` | numeric | NO | `'0'::numeric` |  |  | |
| `status` | character varying | YES |  |  |  | |
| `total` | numeric | NO |  |  |  | |
| `created_at` | timestamp without time zone | YES |  |  |  | |
| `status` | character varying | NO | `'processing'::character varying` |  |  | |
| `created_at` | timestamp without time zone | YES |  |  |  | |
| `updated_at` | timestamp without time zone | YES |  |  |  | |
| `is_archived` | boolean | YES | `false` |  |  | |

### Dependencies (Depends On)
- `users`
- `sales_cart`

### Dependents (Others Depending on this Table)
- `sales_order_product`
- `sales_order_address`
- `sales_order_payment`

### Recommended Changes
- Standardize ID naming, ensure updated_at triggers exist.

---

## Table: `sales_order_address`

### Description
Order addresses (snapshot from cart)

### Table Schema
| Column | Data Type | Nullable | Default | PK | FK | Note |
| :--- | :--- | :--- | :--- | :--- | :--- | :--- |
| `address_id` | bigint | NO |  | ✅ |  | |
| `address_id` | bigint | NO | `nextval('sales_order_address_address_id_seq'::regclass)` | ✅ |  | |
| `order_id` | bigint | NO |  |  | 🔗 sales_order(order_id) | |
| `order_id` | bigint | NO |  |  | 🔗 sales_order(order_id) | |
| `address_type` | character varying | YES |  |  |  | |
| `address_type` | character varying | NO |  |  |  | |
| `first_name` | character varying | YES |  |  |  | |
| `first_name` | character varying | NO |  |  |  | |
| `last_name` | character varying | NO |  |  |  | |
| `last_name` | character varying | YES |  |  |  | |
| `email` | character varying | NO |  |  |  | |
| `email` | character varying | YES |  |  |  | |
| `phone` | character varying | YES |  |  |  | |
| `phone` | character varying | YES |  |  |  | |
| `address_line_one` | text | YES |  |  |  | |
| `address_line_one` | character varying | NO |  |  |  | |
| `city` | character varying | YES |  |  |  | |
| `address_line_two` | character varying | YES |  |  |  | |
| `city` | character varying | NO |  |  |  | |
| `state` | character varying | YES |  |  |  | |
| `state` | character varying | NO |  |  |  | |
| `postal_code` | character varying | YES |  |  |  | |
| `country` | character varying | YES |  |  |  | |
| `postal_code` | character varying | NO |  |  |  | |
| `created_at` | timestamp without time zone | YES |  |  |  | |
| `country` | character varying | NO |  |  |  | |
| `created_at` | timestamp without time zone | NO | `CURRENT_TIMESTAMP` |  |  | |

### Dependencies (Depends On)
- `sales_order`

### Dependents (Others Depending on this Table)
None.

### Recommended Changes
- Standardize ID naming, ensure updated_at triggers exist.

---

## Table: `sales_order_payment`

### Description
Order payment info (snapshot from cart)

### Table Schema
| Column | Data Type | Nullable | Default | PK | FK | Note |
| :--- | :--- | :--- | :--- | :--- | :--- | :--- |
| `payment_id` | bigint | NO |  | ✅ |  | |
| `payment_id` | bigint | NO | `nextval('sales_order_payment_payment_id_seq'::regclass)` | ✅ |  | |
| `order_id` | bigint | NO |  |  | 🔗 sales_order(order_id) | |
| `order_id` | bigint | NO |  |  | 🔗 sales_order(order_id) | |
| `shipping_method` | character varying | YES |  |  |  | |
| `shipping_method` | character varying | NO |  |  |  | |
| `payment_method` | character varying | NO |  |  |  | |
| `payment_method` | character varying | YES |  |  |  | |
| `created_at` | timestamp without time zone | YES |  |  |  | |
| `created_at` | timestamp without time zone | NO | `CURRENT_TIMESTAMP` |  |  | |

### Dependencies (Depends On)
- `sales_order`

### Dependents (Others Depending on this Table)
None.

### Recommended Changes
- Standardize ID naming, ensure updated_at triggers exist.

---

## Table: `sales_order_product`

### Description
Order line items with product snapshots

### Table Schema
| Column | Data Type | Nullable | Default | PK | FK | Note |
| :--- | :--- | :--- | :--- | :--- | :--- | :--- |
| `item_id` | bigint | NO |  | ✅ |  | |
| `item_id` | bigint | NO | `nextval('sales_order_product_item_id_seq'::regclass)` | ✅ |  | |
| `order_id` | bigint | NO |  |  | 🔗 sales_order(order_id) | |
| `order_id` | bigint | NO |  |  | 🔗 sales_order(order_id) | |
| `product_entity_id` | bigint | YES |  |  | 🔗 catalog_product_entity(entity_id) | |
| `product_entity_id` | bigint | YES |  |  | 🔗 catalog_product_entity(entity_id) | |
| `product_name` | character varying | YES |  |  |  | |
| `product_name` | character varying | NO |  |  |  | |
| `product_sku` | character varying | YES |  |  |  | |
| `product_sku` | character varying | NO |  |  |  | |
| `product_price` | numeric | YES |  |  |  | |
| `product_price` | numeric | NO |  |  |  | |
| `quantity` | integer | NO |  |  |  | |
| `quantity` | integer | YES |  |  |  | |
| `row_total` | numeric | YES |  |  |  | |
| `row_total` | numeric | NO |  |  |  | |
| `created_at` | timestamp without time zone | NO | `CURRENT_TIMESTAMP` |  |  | |

### Dependencies (Depends On)
- `sales_order`
- `catalog_product_entity`

### Dependents (Others Depending on this Table)
None.

### Recommended Changes
- Standardize ID naming, ensure updated_at triggers exist.

---

## Table: `saved_items`

### Description
No description available.

### Table Schema
| Column | Data Type | Nullable | Default | PK | FK | Note |
| :--- | :--- | :--- | :--- | :--- | :--- | :--- |
| `id` | integer | NO | `nextval('saved_items_id_seq'::regclass)` | ✅ |  | |
| `user_id` | integer | YES |  |  | 🔗 users(id) | |
| `product_id` | integer | YES |  |  |  | |
| `created_at` | timestamp without time zone | YES | `CURRENT_TIMESTAMP` |  |  | |

### Dependencies (Depends On)
- `users`

### Dependents (Others Depending on this Table)
None.

### Recommended Changes
- Standardize ID naming, ensure updated_at triggers exist.

---

## Table: `sessions`

### Description
No description available.

### Table Schema
| Column | Data Type | Nullable | Default | PK | FK | Note |
| :--- | :--- | :--- | :--- | :--- | :--- | :--- |
| `id` | character varying | NO |  | ✅ |  | |
| `user_id` | bigint | YES |  |  |  | |
| `ip_address` | character varying | YES |  |  |  | |
| `user_agent` | text | YES |  |  |  | |
| `payload` | text | NO |  |  |  | |
| `last_activity` | integer | NO |  |  |  | |

### Dependencies (Depends On)
None.

### Dependents (Others Depending on this Table)
None.

### Recommended Changes
- Standardize ID naming, ensure updated_at triggers exist.

---

## Table: `user_addresses`

### Description
No description available.

### Table Schema
| Column | Data Type | Nullable | Default | PK | FK | Note |
| :--- | :--- | :--- | :--- | :--- | :--- | :--- |
| `id` | integer | NO | `nextval('user_addresses_id_seq'::regclass)` | ✅ |  | |
| `user_id` | integer | NO |  |  | 🔗 users(id) | |
| `first_name` | character varying | NO |  |  |  | |
| `last_name` | character varying | NO |  |  |  | |
| `phone` | character varying | YES |  |  |  | |
| `address_line_one` | character varying | NO |  |  |  | |
| `address_line_two` | character varying | YES |  |  |  | |
| `city` | character varying | NO |  |  |  | |
| `state` | character varying | NO |  |  |  | |
| `postal_code` | character varying | NO |  |  |  | |
| `country` | character varying | NO | `'India'::character varying` |  |  | |
| `is_default` | boolean | YES | `false` |  |  | |
| `created_at` | timestamp without time zone | YES | `CURRENT_TIMESTAMP` |  |  | |
| `updated_at` | timestamp without time zone | YES | `CURRENT_TIMESTAMP` |  |  | |

### Dependencies (Depends On)
- `users`

### Dependents (Others Depending on this Table)
None.

### Recommended Changes
- Standardize ID naming, ensure updated_at triggers exist.

---

## Table: `users`

### Description
No description available.

### Table Schema
| Column | Data Type | Nullable | Default | PK | FK | Note |
| :--- | :--- | :--- | :--- | :--- | :--- | :--- |
| `id` | bigint | NO |  | ✅ |  | |
| `id` | bigint | NO | `nextval('users_id_seq'::regclass)` | ✅ |  | |
| `name` | character varying | NO |  |  |  | |
| `name` | character varying | NO |  |  |  | |
| `email` | character varying | NO |  |  |  | |
| `email` | character varying | NO |  |  |  | |
| `email_verified_at` | timestamp without time zone | YES |  |  |  | |
| `password` | character varying | NO |  |  |  | |
| `phone` | character varying | YES |  |  |  | |
| `password` | character varying | NO |  |  |  | |
| `created_at` | timestamp without time zone | YES |  |  |  | |
| `remember_token` | character varying | YES |  |  |  | |
| `created_at` | timestamp without time zone | YES |  |  |  | |
| `updated_at` | timestamp without time zone | YES |  |  |  | |
| `updated_at` | timestamp without time zone | YES |  |  |  | |
| `phone` | character varying | YES |  |  |  | |

### Dependencies (Depends On)
None.

### Dependents (Others Depending on this Table)
- `saved_items`
- `product_reviews`
- `sales_cart`
- `sales_order`
- `catalog_wishlist`
- `user_addresses`

### Recommended Changes
- Standardize ID naming, ensure updated_at triggers exist.

---

## Final Database Rating & Recommendations

### Overall Rating: **7.5/10**

### Suggestions to Improve Ratings
1. **Type Standardization**: Ensure all Primary and Foreign keys use the same data type (e.g., all `BIGINT`).
2. **Soft Deletes**: Implement `deleted_at` for core entities like Users and Products.
3. **Audit Trails**: Consistent `created_at` and `updated_at` across all active tables.
4. **JSONB usage**: For flexible attributes, consider PostgreSQL `JSONB` for better performance.
