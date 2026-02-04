--
-- PostgreSQL database dump
--

\restrict 2QMHPmMxUF9NyEaG1JF3okCcTrfoZzfGOhRGNeHZU3XhhchPxeRfZ9ufi7ducmJ

-- Dumped from database version 18.1
-- Dumped by pg_dump version 18.1

-- Started on 2026-02-04 18:11:43

SET statement_timeout = 0;
SET lock_timeout = 0;
SET idle_in_transaction_session_timeout = 0;
SET transaction_timeout = 0;
SET client_encoding = 'UTF8';
SET standard_conforming_strings = on;
SELECT pg_catalog.set_config('search_path', '', false);
SET check_function_bodies = false;
SET xmloption = content;
SET client_min_messages = warning;
SET row_security = off;

SET default_tablespace = '';

SET default_table_access_method = heap;

--
-- TOC entry 265 (class 1259 OID 25632)
-- Name: brands; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.brands (
    id integer NOT NULL,
    name character varying(255) NOT NULL
);


ALTER TABLE public.brands OWNER TO postgres;

--
-- TOC entry 264 (class 1259 OID 25631)
-- Name: brands_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.brands_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.brands_id_seq OWNER TO postgres;

--
-- TOC entry 5538 (class 0 OID 0)
-- Dependencies: 264
-- Name: brands_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.brands_id_seq OWNED BY public.brands.id;


--
-- TOC entry 225 (class 1259 OID 25188)
-- Name: cache; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.cache (
    key character varying(255) NOT NULL,
    value text NOT NULL,
    expiration integer NOT NULL
);


ALTER TABLE public.cache OWNER TO postgres;

--
-- TOC entry 226 (class 1259 OID 25199)
-- Name: cache_locks; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.cache_locks (
    key character varying(255) NOT NULL,
    owner character varying(255) NOT NULL,
    expiration integer NOT NULL
);


ALTER TABLE public.cache_locks OWNER TO postgres;

--
-- TOC entry 275 (class 1259 OID 25729)
-- Name: cart_items; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.cart_items (
    id integer NOT NULL,
    cart_id integer,
    product_id integer,
    quantity integer DEFAULT 1,
    created_at timestamp without time zone DEFAULT CURRENT_TIMESTAMP
);


ALTER TABLE public.cart_items OWNER TO postgres;

--
-- TOC entry 274 (class 1259 OID 25728)
-- Name: cart_items_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.cart_items_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.cart_items_id_seq OWNER TO postgres;

--
-- TOC entry 5539 (class 0 OID 0)
-- Dependencies: 274
-- Name: cart_items_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.cart_items_id_seq OWNED BY public.cart_items.id;


--
-- TOC entry 273 (class 1259 OID 25714)
-- Name: carts; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.carts (
    id integer NOT NULL,
    user_id integer,
    session_id character varying(255),
    created_at timestamp without time zone DEFAULT CURRENT_TIMESTAMP,
    updated_at timestamp without time zone DEFAULT CURRENT_TIMESTAMP
);


ALTER TABLE public.carts OWNER TO postgres;

--
-- TOC entry 272 (class 1259 OID 25713)
-- Name: carts_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.carts_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.carts_id_seq OWNER TO postgres;

--
-- TOC entry 5540 (class 0 OID 0)
-- Dependencies: 272
-- Name: carts_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.carts_id_seq OWNED BY public.carts.id;


--
-- TOC entry 241 (class 1259 OID 25356)
-- Name: catalog_category_attribute; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.catalog_category_attribute (
    attribute_id bigint NOT NULL,
    category_entity_id bigint NOT NULL,
    attribute_code character varying(100) NOT NULL,
    attribute_value text NOT NULL,
    created_at timestamp(0) without time zone DEFAULT CURRENT_TIMESTAMP NOT NULL
);


ALTER TABLE public.catalog_category_attribute OWNER TO postgres;

--
-- TOC entry 5541 (class 0 OID 0)
-- Dependencies: 241
-- Name: TABLE catalog_category_attribute; Type: COMMENT; Schema: public; Owner: postgres
--

COMMENT ON TABLE public.catalog_category_attribute IS 'Flexible category attributes';


--
-- TOC entry 240 (class 1259 OID 25355)
-- Name: catalog_category_attribute_attribute_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.catalog_category_attribute_attribute_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.catalog_category_attribute_attribute_id_seq OWNER TO postgres;

--
-- TOC entry 5542 (class 0 OID 0)
-- Dependencies: 240
-- Name: catalog_category_attribute_attribute_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.catalog_category_attribute_attribute_id_seq OWNED BY public.catalog_category_attribute.attribute_id;


--
-- TOC entry 239 (class 1259 OID 25331)
-- Name: catalog_category_entity; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.catalog_category_entity (
    entity_id bigint NOT NULL,
    name character varying(255) NOT NULL,
    slug character varying(255) NOT NULL,
    parent_id bigint,
    "position" integer DEFAULT 0 NOT NULL,
    is_active boolean DEFAULT true NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


ALTER TABLE public.catalog_category_entity OWNER TO postgres;

--
-- TOC entry 5543 (class 0 OID 0)
-- Dependencies: 239
-- Name: TABLE catalog_category_entity; Type: COMMENT; Schema: public; Owner: postgres
--

COMMENT ON TABLE public.catalog_category_entity IS 'Core category information';


--
-- TOC entry 238 (class 1259 OID 25330)
-- Name: catalog_category_entity_entity_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.catalog_category_entity_entity_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.catalog_category_entity_entity_id_seq OWNER TO postgres;

--
-- TOC entry 5544 (class 0 OID 0)
-- Dependencies: 238
-- Name: catalog_category_entity_entity_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.catalog_category_entity_entity_id_seq OWNED BY public.catalog_category_entity.entity_id;


--
-- TOC entry 243 (class 1259 OID 25377)
-- Name: catalog_category_product; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.catalog_category_product (
    relation_id bigint NOT NULL,
    category_entity_id bigint NOT NULL,
    product_entity_id bigint NOT NULL,
    "position" integer DEFAULT 0 NOT NULL,
    created_at timestamp(0) without time zone DEFAULT CURRENT_TIMESTAMP NOT NULL
);


ALTER TABLE public.catalog_category_product OWNER TO postgres;

--
-- TOC entry 5545 (class 0 OID 0)
-- Dependencies: 243
-- Name: TABLE catalog_category_product; Type: COMMENT; Schema: public; Owner: postgres
--

COMMENT ON TABLE public.catalog_category_product IS 'Many-to-many relationship between categories and products';


--
-- TOC entry 242 (class 1259 OID 25376)
-- Name: catalog_category_product_relation_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.catalog_category_product_relation_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.catalog_category_product_relation_id_seq OWNER TO postgres;

--
-- TOC entry 5546 (class 0 OID 0)
-- Dependencies: 242
-- Name: catalog_category_product_relation_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.catalog_category_product_relation_id_seq OWNED BY public.catalog_category_product.relation_id;


--
-- TOC entry 235 (class 1259 OID 25288)
-- Name: catalog_product_attribute; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.catalog_product_attribute (
    attribute_id bigint NOT NULL,
    product_entity_id bigint NOT NULL,
    attribute_code character varying(100) NOT NULL,
    attribute_value text NOT NULL,
    created_at timestamp(0) without time zone DEFAULT CURRENT_TIMESTAMP NOT NULL
);


ALTER TABLE public.catalog_product_attribute OWNER TO postgres;

--
-- TOC entry 5547 (class 0 OID 0)
-- Dependencies: 235
-- Name: TABLE catalog_product_attribute; Type: COMMENT; Schema: public; Owner: postgres
--

COMMENT ON TABLE public.catalog_product_attribute IS 'Flexible product attributes (color, size, brand, etc.)';


--
-- TOC entry 234 (class 1259 OID 25287)
-- Name: catalog_product_attribute_attribute_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.catalog_product_attribute_attribute_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.catalog_product_attribute_attribute_id_seq OWNER TO postgres;

--
-- TOC entry 5548 (class 0 OID 0)
-- Dependencies: 234
-- Name: catalog_product_attribute_attribute_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.catalog_product_attribute_attribute_id_seq OWNED BY public.catalog_product_attribute.attribute_id;


--
-- TOC entry 233 (class 1259 OID 25260)
-- Name: catalog_product_entity; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.catalog_product_entity (
    entity_id bigint NOT NULL,
    sku character varying(100) NOT NULL,
    name character varying(255) NOT NULL,
    price numeric(10,2) NOT NULL,
    original_price numeric(10,2),
    stock integer DEFAULT 0 NOT NULL,
    description text,
    is_featured boolean DEFAULT false NOT NULL,
    is_new boolean DEFAULT false NOT NULL,
    rating numeric(3,2) DEFAULT '0'::numeric NOT NULL,
    reviews_count integer DEFAULT 0 NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone,
    is_active boolean DEFAULT true NOT NULL
);


ALTER TABLE public.catalog_product_entity OWNER TO postgres;

--
-- TOC entry 5549 (class 0 OID 0)
-- Dependencies: 233
-- Name: TABLE catalog_product_entity; Type: COMMENT; Schema: public; Owner: postgres
--

COMMENT ON TABLE public.catalog_product_entity IS 'Core product information (EAV entity)';


--
-- TOC entry 232 (class 1259 OID 25259)
-- Name: catalog_product_entity_entity_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.catalog_product_entity_entity_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.catalog_product_entity_entity_id_seq OWNER TO postgres;

--
-- TOC entry 5550 (class 0 OID 0)
-- Dependencies: 232
-- Name: catalog_product_entity_entity_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.catalog_product_entity_entity_id_seq OWNED BY public.catalog_product_entity.entity_id;


--
-- TOC entry 237 (class 1259 OID 25309)
-- Name: catalog_product_image; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.catalog_product_image (
    image_id bigint NOT NULL,
    product_entity_id bigint NOT NULL,
    image_path character varying(255) NOT NULL,
    image_position integer DEFAULT 0 NOT NULL,
    is_primary boolean DEFAULT false NOT NULL,
    created_at timestamp(0) without time zone DEFAULT CURRENT_TIMESTAMP NOT NULL
);


ALTER TABLE public.catalog_product_image OWNER TO postgres;

--
-- TOC entry 5551 (class 0 OID 0)
-- Dependencies: 237
-- Name: TABLE catalog_product_image; Type: COMMENT; Schema: public; Owner: postgres
--

COMMENT ON TABLE public.catalog_product_image IS 'Product images - avoids comma-separated values';


--
-- TOC entry 236 (class 1259 OID 25308)
-- Name: catalog_product_image_image_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.catalog_product_image_image_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.catalog_product_image_image_id_seq OWNER TO postgres;

--
-- TOC entry 5552 (class 0 OID 0)
-- Dependencies: 236
-- Name: catalog_product_image_image_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.catalog_product_image_image_id_seq OWNED BY public.catalog_product_image.image_id;


--
-- TOC entry 261 (class 1259 OID 25592)
-- Name: catalog_wishlist; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.catalog_wishlist (
    wishlist_id bigint NOT NULL,
    user_id bigint NOT NULL,
    product_entity_id bigint NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


ALTER TABLE public.catalog_wishlist OWNER TO postgres;

--
-- TOC entry 260 (class 1259 OID 25591)
-- Name: catalog_wishlist_wishlist_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.catalog_wishlist_wishlist_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.catalog_wishlist_wishlist_id_seq OWNER TO postgres;

--
-- TOC entry 5553 (class 0 OID 0)
-- Dependencies: 260
-- Name: catalog_wishlist_wishlist_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.catalog_wishlist_wishlist_id_seq OWNED BY public.catalog_wishlist.wishlist_id;


--
-- TOC entry 263 (class 1259 OID 25618)
-- Name: categories; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.categories (
    id integer NOT NULL,
    name character varying(255) NOT NULL,
    slug character varying(255) NOT NULL
);


ALTER TABLE public.categories OWNER TO postgres;

--
-- TOC entry 262 (class 1259 OID 25617)
-- Name: categories_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.categories_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.categories_id_seq OWNER TO postgres;

--
-- TOC entry 5554 (class 0 OID 0)
-- Dependencies: 262
-- Name: categories_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.categories_id_seq OWNED BY public.categories.id;


--
-- TOC entry 281 (class 1259 OID 25793)
-- Name: coupons; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.coupons (
    id integer NOT NULL,
    code character varying(50) NOT NULL,
    discount_percent integer NOT NULL,
    created_at timestamp without time zone DEFAULT CURRENT_TIMESTAMP
);


ALTER TABLE public.coupons OWNER TO postgres;

--
-- TOC entry 280 (class 1259 OID 25792)
-- Name: coupons_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.coupons_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.coupons_id_seq OWNER TO postgres;

--
-- TOC entry 5555 (class 0 OID 0)
-- Dependencies: 280
-- Name: coupons_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.coupons_id_seq OWNED BY public.coupons.id;


--
-- TOC entry 231 (class 1259 OID 25241)
-- Name: failed_jobs; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.failed_jobs (
    id bigint NOT NULL,
    uuid character varying(255) NOT NULL,
    connection text NOT NULL,
    queue text NOT NULL,
    payload text NOT NULL,
    exception text NOT NULL,
    failed_at timestamp(0) without time zone DEFAULT CURRENT_TIMESTAMP NOT NULL
);


ALTER TABLE public.failed_jobs OWNER TO postgres;

--
-- TOC entry 230 (class 1259 OID 25240)
-- Name: failed_jobs_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.failed_jobs_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.failed_jobs_id_seq OWNER TO postgres;

--
-- TOC entry 5556 (class 0 OID 0)
-- Dependencies: 230
-- Name: failed_jobs_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.failed_jobs_id_seq OWNED BY public.failed_jobs.id;


--
-- TOC entry 229 (class 1259 OID 25226)
-- Name: job_batches; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.job_batches (
    id character varying(255) NOT NULL,
    name character varying(255) NOT NULL,
    total_jobs integer NOT NULL,
    pending_jobs integer NOT NULL,
    failed_jobs integer NOT NULL,
    failed_job_ids text NOT NULL,
    options text,
    cancelled_at integer,
    created_at integer NOT NULL,
    finished_at integer
);


ALTER TABLE public.job_batches OWNER TO postgres;

--
-- TOC entry 228 (class 1259 OID 25211)
-- Name: jobs; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.jobs (
    id bigint NOT NULL,
    queue character varying(255) NOT NULL,
    payload text NOT NULL,
    attempts smallint NOT NULL,
    reserved_at integer,
    available_at integer NOT NULL,
    created_at integer NOT NULL
);


ALTER TABLE public.jobs OWNER TO postgres;

--
-- TOC entry 227 (class 1259 OID 25210)
-- Name: jobs_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.jobs_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.jobs_id_seq OWNER TO postgres;

--
-- TOC entry 5557 (class 0 OID 0)
-- Dependencies: 227
-- Name: jobs_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.jobs_id_seq OWNED BY public.jobs.id;


--
-- TOC entry 220 (class 1259 OID 25143)
-- Name: migrations; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.migrations (
    id integer NOT NULL,
    migration character varying(255) NOT NULL,
    batch integer NOT NULL
);


ALTER TABLE public.migrations OWNER TO postgres;

--
-- TOC entry 219 (class 1259 OID 25142)
-- Name: migrations_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.migrations_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.migrations_id_seq OWNER TO postgres;

--
-- TOC entry 5558 (class 0 OID 0)
-- Dependencies: 219
-- Name: migrations_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.migrations_id_seq OWNED BY public.migrations.id;


--
-- TOC entry 271 (class 1259 OID 25694)
-- Name: order_items; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.order_items (
    id integer NOT NULL,
    order_id integer,
    product_id integer,
    quantity integer NOT NULL,
    price numeric(10,2) NOT NULL
);


ALTER TABLE public.order_items OWNER TO postgres;

--
-- TOC entry 270 (class 1259 OID 25693)
-- Name: order_items_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.order_items_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.order_items_id_seq OWNER TO postgres;

--
-- TOC entry 5559 (class 0 OID 0)
-- Dependencies: 270
-- Name: order_items_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.order_items_id_seq OWNED BY public.order_items.id;


--
-- TOC entry 269 (class 1259 OID 25672)
-- Name: orders; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.orders (
    id integer NOT NULL,
    order_number character varying(50) NOT NULL,
    user_id integer,
    subtotal numeric(10,2) NOT NULL,
    shipping_cost numeric(10,2) DEFAULT 0,
    tax numeric(10,2) DEFAULT 0,
    total numeric(10,2) NOT NULL,
    status character varying(50) DEFAULT 'Processing'::character varying,
    shipping_method character varying(50),
    created_at timestamp without time zone DEFAULT CURRENT_TIMESTAMP
);


ALTER TABLE public.orders OWNER TO postgres;

--
-- TOC entry 268 (class 1259 OID 25671)
-- Name: orders_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.orders_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.orders_id_seq OWNER TO postgres;

--
-- TOC entry 5560 (class 0 OID 0)
-- Dependencies: 268
-- Name: orders_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.orders_id_seq OWNED BY public.orders.id;


--
-- TOC entry 223 (class 1259 OID 25167)
-- Name: password_reset_tokens; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.password_reset_tokens (
    email character varying(255) NOT NULL,
    token character varying(255) NOT NULL,
    created_at timestamp(0) without time zone
);


ALTER TABLE public.password_reset_tokens OWNER TO postgres;

--
-- TOC entry 285 (class 1259 OID 32972)
-- Name: product_reviews; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.product_reviews (
    review_id integer NOT NULL,
    product_entity_id integer NOT NULL,
    user_id integer NOT NULL,
    rating integer NOT NULL,
    comment text,
    is_approved boolean DEFAULT true,
    created_at timestamp without time zone DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT product_reviews_rating_check CHECK (((rating >= 1) AND (rating <= 5)))
);


ALTER TABLE public.product_reviews OWNER TO postgres;

--
-- TOC entry 284 (class 1259 OID 32971)
-- Name: product_reviews_review_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.product_reviews_review_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.product_reviews_review_id_seq OWNER TO postgres;

--
-- TOC entry 5561 (class 0 OID 0)
-- Dependencies: 284
-- Name: product_reviews_review_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.product_reviews_review_id_seq OWNED BY public.product_reviews.review_id;


--
-- TOC entry 267 (class 1259 OID 25641)
-- Name: products; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.products (
    id integer NOT NULL,
    category_id integer,
    brand_id integer,
    name character varying(255) NOT NULL,
    slug character varying(255) NOT NULL,
    price numeric(10,2) NOT NULL,
    original_price numeric(10,2),
    stock integer DEFAULT 0,
    description text,
    image character varying(255),
    is_featured boolean DEFAULT false,
    is_new boolean DEFAULT false,
    rating numeric(3,2) DEFAULT 0,
    reviews_count integer DEFAULT 0,
    created_at timestamp without time zone DEFAULT CURRENT_TIMESTAMP
);


ALTER TABLE public.products OWNER TO postgres;

--
-- TOC entry 266 (class 1259 OID 25640)
-- Name: products_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.products_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.products_id_seq OWNER TO postgres;

--
-- TOC entry 5562 (class 0 OID 0)
-- Dependencies: 266
-- Name: products_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.products_id_seq OWNED BY public.products.id;


--
-- TOC entry 245 (class 1259 OID 25403)
-- Name: sales_cart; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.sales_cart (
    cart_id bigint NOT NULL,
    user_id bigint,
    session_id character varying(255),
    is_active boolean DEFAULT true NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


ALTER TABLE public.sales_cart OWNER TO postgres;

--
-- TOC entry 5563 (class 0 OID 0)
-- Dependencies: 245
-- Name: TABLE sales_cart; Type: COMMENT; Schema: public; Owner: postgres
--

COMMENT ON TABLE public.sales_cart IS 'Shopping cart header - cart_id stored in session';


--
-- TOC entry 5564 (class 0 OID 0)
-- Dependencies: 245
-- Name: COLUMN sales_cart.is_active; Type: COMMENT; Schema: public; Owner: postgres
--

COMMENT ON COLUMN public.sales_cart.is_active IS 'Set to FALSE when order is placed - cart is not deleted';


--
-- TOC entry 249 (class 1259 OID 25444)
-- Name: sales_cart_address; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.sales_cart_address (
    address_id bigint NOT NULL,
    cart_id bigint NOT NULL,
    address_type character varying(20) NOT NULL,
    first_name character varying(100) NOT NULL,
    last_name character varying(100) NOT NULL,
    email character varying(255) NOT NULL,
    phone character varying(20),
    address_line_one character varying(255) NOT NULL,
    address_line_two character varying(255),
    city character varying(100) NOT NULL,
    state character varying(100) NOT NULL,
    postal_code character varying(20) NOT NULL,
    country character varying(100) NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


ALTER TABLE public.sales_cart_address OWNER TO postgres;

--
-- TOC entry 5565 (class 0 OID 0)
-- Dependencies: 249
-- Name: TABLE sales_cart_address; Type: COMMENT; Schema: public; Owner: postgres
--

COMMENT ON TABLE public.sales_cart_address IS 'Cart shipping and billing addresses';


--
-- TOC entry 248 (class 1259 OID 25443)
-- Name: sales_cart_address_address_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.sales_cart_address_address_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.sales_cart_address_address_id_seq OWNER TO postgres;

--
-- TOC entry 5566 (class 0 OID 0)
-- Dependencies: 248
-- Name: sales_cart_address_address_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.sales_cart_address_address_id_seq OWNED BY public.sales_cart_address.address_id;


--
-- TOC entry 244 (class 1259 OID 25402)
-- Name: sales_cart_cart_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.sales_cart_cart_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.sales_cart_cart_id_seq OWNER TO postgres;

--
-- TOC entry 5567 (class 0 OID 0)
-- Dependencies: 244
-- Name: sales_cart_cart_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.sales_cart_cart_id_seq OWNED BY public.sales_cart.cart_id;


--
-- TOC entry 251 (class 1259 OID 25470)
-- Name: sales_cart_payment; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.sales_cart_payment (
    payment_id bigint NOT NULL,
    cart_id bigint NOT NULL,
    shipping_method character varying(50),
    payment_method character varying(50),
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


ALTER TABLE public.sales_cart_payment OWNER TO postgres;

--
-- TOC entry 5568 (class 0 OID 0)
-- Dependencies: 251
-- Name: TABLE sales_cart_payment; Type: COMMENT; Schema: public; Owner: postgres
--

COMMENT ON TABLE public.sales_cart_payment IS 'Cart payment and shipping method selection';


--
-- TOC entry 250 (class 1259 OID 25469)
-- Name: sales_cart_payment_payment_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.sales_cart_payment_payment_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.sales_cart_payment_payment_id_seq OWNER TO postgres;

--
-- TOC entry 5569 (class 0 OID 0)
-- Dependencies: 250
-- Name: sales_cart_payment_payment_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.sales_cart_payment_payment_id_seq OWNED BY public.sales_cart_payment.payment_id;


--
-- TOC entry 247 (class 1259 OID 25420)
-- Name: sales_cart_product; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.sales_cart_product (
    item_id bigint NOT NULL,
    cart_id bigint NOT NULL,
    product_entity_id bigint NOT NULL,
    quantity integer DEFAULT 1 NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


ALTER TABLE public.sales_cart_product OWNER TO postgres;

--
-- TOC entry 5570 (class 0 OID 0)
-- Dependencies: 247
-- Name: TABLE sales_cart_product; Type: COMMENT; Schema: public; Owner: postgres
--

COMMENT ON TABLE public.sales_cart_product IS 'Cart line items';


--
-- TOC entry 246 (class 1259 OID 25419)
-- Name: sales_cart_product_item_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.sales_cart_product_item_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.sales_cart_product_item_id_seq OWNER TO postgres;

--
-- TOC entry 5571 (class 0 OID 0)
-- Dependencies: 246
-- Name: sales_cart_product_item_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.sales_cart_product_item_id_seq OWNED BY public.sales_cart_product.item_id;


--
-- TOC entry 253 (class 1259 OID 25486)
-- Name: sales_order; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.sales_order (
    order_id bigint NOT NULL,
    order_number character varying(50) NOT NULL,
    user_id bigint,
    original_cart_id bigint,
    subtotal numeric(10,2) NOT NULL,
    shipping_cost numeric(10,2) DEFAULT '0'::numeric NOT NULL,
    tax numeric(10,2) DEFAULT '0'::numeric NOT NULL,
    discount numeric(10,2) DEFAULT '0'::numeric NOT NULL,
    total numeric(10,2) NOT NULL,
    status character varying(50) DEFAULT 'processing'::character varying NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone,
    is_archived boolean DEFAULT false
);


ALTER TABLE public.sales_order OWNER TO postgres;

--
-- TOC entry 5572 (class 0 OID 0)
-- Dependencies: 253
-- Name: TABLE sales_order; Type: COMMENT; Schema: public; Owner: postgres
--

COMMENT ON TABLE public.sales_order IS 'Order header - created from inactive cart';


--
-- TOC entry 5573 (class 0 OID 0)
-- Dependencies: 253
-- Name: COLUMN sales_order.original_cart_id; Type: COMMENT; Schema: public; Owner: postgres
--

COMMENT ON COLUMN public.sales_order.original_cart_id IS 'Reference to the cart that created this order';


--
-- TOC entry 257 (class 1259 OID 25545)
-- Name: sales_order_address; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.sales_order_address (
    address_id bigint NOT NULL,
    order_id bigint NOT NULL,
    address_type character varying(20) NOT NULL,
    first_name character varying(100) NOT NULL,
    last_name character varying(100) NOT NULL,
    email character varying(255) NOT NULL,
    phone character varying(20),
    address_line_one character varying(255) NOT NULL,
    address_line_two character varying(255),
    city character varying(100) NOT NULL,
    state character varying(100) NOT NULL,
    postal_code character varying(20) NOT NULL,
    country character varying(100) NOT NULL,
    created_at timestamp(0) without time zone DEFAULT CURRENT_TIMESTAMP NOT NULL
);


ALTER TABLE public.sales_order_address OWNER TO postgres;

--
-- TOC entry 5574 (class 0 OID 0)
-- Dependencies: 257
-- Name: TABLE sales_order_address; Type: COMMENT; Schema: public; Owner: postgres
--

COMMENT ON TABLE public.sales_order_address IS 'Order addresses (snapshot from cart)';


--
-- TOC entry 256 (class 1259 OID 25544)
-- Name: sales_order_address_address_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.sales_order_address_address_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.sales_order_address_address_id_seq OWNER TO postgres;

--
-- TOC entry 5575 (class 0 OID 0)
-- Dependencies: 256
-- Name: sales_order_address_address_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.sales_order_address_address_id_seq OWNED BY public.sales_order_address.address_id;


--
-- TOC entry 252 (class 1259 OID 25485)
-- Name: sales_order_order_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.sales_order_order_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.sales_order_order_id_seq OWNER TO postgres;

--
-- TOC entry 5576 (class 0 OID 0)
-- Dependencies: 252
-- Name: sales_order_order_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.sales_order_order_id_seq OWNED BY public.sales_order.order_id;


--
-- TOC entry 259 (class 1259 OID 25572)
-- Name: sales_order_payment; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.sales_order_payment (
    payment_id bigint NOT NULL,
    order_id bigint NOT NULL,
    shipping_method character varying(50) NOT NULL,
    payment_method character varying(50) NOT NULL,
    created_at timestamp(0) without time zone DEFAULT CURRENT_TIMESTAMP NOT NULL
);


ALTER TABLE public.sales_order_payment OWNER TO postgres;

--
-- TOC entry 5577 (class 0 OID 0)
-- Dependencies: 259
-- Name: TABLE sales_order_payment; Type: COMMENT; Schema: public; Owner: postgres
--

COMMENT ON TABLE public.sales_order_payment IS 'Order payment info (snapshot from cart)';


--
-- TOC entry 258 (class 1259 OID 25571)
-- Name: sales_order_payment_payment_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.sales_order_payment_payment_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.sales_order_payment_payment_id_seq OWNER TO postgres;

--
-- TOC entry 5578 (class 0 OID 0)
-- Dependencies: 258
-- Name: sales_order_payment_payment_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.sales_order_payment_payment_id_seq OWNED BY public.sales_order_payment.payment_id;


--
-- TOC entry 255 (class 1259 OID 25519)
-- Name: sales_order_product; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.sales_order_product (
    item_id bigint NOT NULL,
    order_id bigint NOT NULL,
    product_entity_id bigint,
    product_name character varying(255) NOT NULL,
    product_sku character varying(100) NOT NULL,
    product_price numeric(10,2) NOT NULL,
    quantity integer NOT NULL,
    row_total numeric(10,2) NOT NULL,
    created_at timestamp(0) without time zone DEFAULT CURRENT_TIMESTAMP NOT NULL
);


ALTER TABLE public.sales_order_product OWNER TO postgres;

--
-- TOC entry 5579 (class 0 OID 0)
-- Dependencies: 255
-- Name: TABLE sales_order_product; Type: COMMENT; Schema: public; Owner: postgres
--

COMMENT ON TABLE public.sales_order_product IS 'Order line items with product snapshots';


--
-- TOC entry 254 (class 1259 OID 25518)
-- Name: sales_order_product_item_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.sales_order_product_item_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.sales_order_product_item_id_seq OWNER TO postgres;

--
-- TOC entry 5580 (class 0 OID 0)
-- Dependencies: 254
-- Name: sales_order_product_item_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.sales_order_product_item_id_seq OWNED BY public.sales_order_product.item_id;


--
-- TOC entry 279 (class 1259 OID 25772)
-- Name: saved_items; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.saved_items (
    id integer NOT NULL,
    user_id integer,
    product_id integer,
    created_at timestamp without time zone DEFAULT CURRENT_TIMESTAMP
);


ALTER TABLE public.saved_items OWNER TO postgres;

--
-- TOC entry 278 (class 1259 OID 25771)
-- Name: saved_items_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.saved_items_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.saved_items_id_seq OWNER TO postgres;

--
-- TOC entry 5581 (class 0 OID 0)
-- Dependencies: 278
-- Name: saved_items_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.saved_items_id_seq OWNED BY public.saved_items.id;


--
-- TOC entry 224 (class 1259 OID 25176)
-- Name: sessions; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.sessions (
    id character varying(255) NOT NULL,
    user_id bigint,
    ip_address character varying(45),
    user_agent text,
    payload text NOT NULL,
    last_activity integer NOT NULL
);


ALTER TABLE public.sessions OWNER TO postgres;

--
-- TOC entry 287 (class 1259 OID 33002)
-- Name: user_addresses; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.user_addresses (
    id integer NOT NULL,
    user_id integer NOT NULL,
    first_name character varying(100) NOT NULL,
    last_name character varying(100) NOT NULL,
    phone character varying(20),
    address_line_one character varying(255) NOT NULL,
    address_line_two character varying(255),
    city character varying(100) NOT NULL,
    state character varying(100) NOT NULL,
    postal_code character varying(100) NOT NULL,
    country character varying(100) DEFAULT 'India'::character varying NOT NULL,
    is_default boolean DEFAULT false,
    created_at timestamp without time zone DEFAULT CURRENT_TIMESTAMP,
    updated_at timestamp without time zone DEFAULT CURRENT_TIMESTAMP
);


ALTER TABLE public.user_addresses OWNER TO postgres;

--
-- TOC entry 286 (class 1259 OID 33001)
-- Name: user_addresses_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.user_addresses_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.user_addresses_id_seq OWNER TO postgres;

--
-- TOC entry 5582 (class 0 OID 0)
-- Dependencies: 286
-- Name: user_addresses_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.user_addresses_id_seq OWNED BY public.user_addresses.id;


--
-- TOC entry 222 (class 1259 OID 25153)
-- Name: users; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.users (
    id bigint NOT NULL,
    name character varying(255) NOT NULL,
    email character varying(255) NOT NULL,
    email_verified_at timestamp(0) without time zone,
    password character varying(255) NOT NULL,
    remember_token character varying(100),
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone,
    phone character varying(20)
);


ALTER TABLE public.users OWNER TO postgres;

--
-- TOC entry 221 (class 1259 OID 25152)
-- Name: users_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.users_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.users_id_seq OWNER TO postgres;

--
-- TOC entry 5583 (class 0 OID 0)
-- Dependencies: 221
-- Name: users_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.users_id_seq OWNED BY public.users.id;


--
-- TOC entry 283 (class 1259 OID 32947)
-- Name: wishlist; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.wishlist (
    wishlist_id integer NOT NULL,
    user_id integer NOT NULL,
    product_entity_id integer NOT NULL,
    created_at timestamp without time zone DEFAULT CURRENT_TIMESTAMP
);


ALTER TABLE public.wishlist OWNER TO postgres;

--
-- TOC entry 282 (class 1259 OID 32946)
-- Name: wishlist_wishlist_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.wishlist_wishlist_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.wishlist_wishlist_id_seq OWNER TO postgres;

--
-- TOC entry 5584 (class 0 OID 0)
-- Dependencies: 282
-- Name: wishlist_wishlist_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.wishlist_wishlist_id_seq OWNED BY public.wishlist.wishlist_id;


--
-- TOC entry 277 (class 1259 OID 25751)
-- Name: wishlists; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.wishlists (
    id integer NOT NULL,
    user_id integer,
    product_id integer,
    created_at timestamp without time zone DEFAULT CURRENT_TIMESTAMP
);


ALTER TABLE public.wishlists OWNER TO postgres;

--
-- TOC entry 276 (class 1259 OID 25750)
-- Name: wishlists_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.wishlists_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.wishlists_id_seq OWNER TO postgres;

--
-- TOC entry 5585 (class 0 OID 0)
-- Dependencies: 276
-- Name: wishlists_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.wishlists_id_seq OWNED BY public.wishlists.id;


--
-- TOC entry 5077 (class 2604 OID 25635)
-- Name: brands id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.brands ALTER COLUMN id SET DEFAULT nextval('public.brands_id_seq'::regclass);


--
-- TOC entry 5094 (class 2604 OID 25732)
-- Name: cart_items id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.cart_items ALTER COLUMN id SET DEFAULT nextval('public.cart_items_id_seq'::regclass);


--
-- TOC entry 5091 (class 2604 OID 25717)
-- Name: carts id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.carts ALTER COLUMN id SET DEFAULT nextval('public.carts_id_seq'::regclass);


--
-- TOC entry 5052 (class 2604 OID 25359)
-- Name: catalog_category_attribute attribute_id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.catalog_category_attribute ALTER COLUMN attribute_id SET DEFAULT nextval('public.catalog_category_attribute_attribute_id_seq'::regclass);


--
-- TOC entry 5049 (class 2604 OID 25334)
-- Name: catalog_category_entity entity_id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.catalog_category_entity ALTER COLUMN entity_id SET DEFAULT nextval('public.catalog_category_entity_entity_id_seq'::regclass);


--
-- TOC entry 5054 (class 2604 OID 25380)
-- Name: catalog_category_product relation_id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.catalog_category_product ALTER COLUMN relation_id SET DEFAULT nextval('public.catalog_category_product_relation_id_seq'::regclass);


--
-- TOC entry 5043 (class 2604 OID 25291)
-- Name: catalog_product_attribute attribute_id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.catalog_product_attribute ALTER COLUMN attribute_id SET DEFAULT nextval('public.catalog_product_attribute_attribute_id_seq'::regclass);


--
-- TOC entry 5036 (class 2604 OID 25263)
-- Name: catalog_product_entity entity_id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.catalog_product_entity ALTER COLUMN entity_id SET DEFAULT nextval('public.catalog_product_entity_entity_id_seq'::regclass);


--
-- TOC entry 5045 (class 2604 OID 25312)
-- Name: catalog_product_image image_id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.catalog_product_image ALTER COLUMN image_id SET DEFAULT nextval('public.catalog_product_image_image_id_seq'::regclass);


--
-- TOC entry 5075 (class 2604 OID 25595)
-- Name: catalog_wishlist wishlist_id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.catalog_wishlist ALTER COLUMN wishlist_id SET DEFAULT nextval('public.catalog_wishlist_wishlist_id_seq'::regclass);


--
-- TOC entry 5076 (class 2604 OID 25621)
-- Name: categories id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.categories ALTER COLUMN id SET DEFAULT nextval('public.categories_id_seq'::regclass);


--
-- TOC entry 5101 (class 2604 OID 25796)
-- Name: coupons id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.coupons ALTER COLUMN id SET DEFAULT nextval('public.coupons_id_seq'::regclass);


--
-- TOC entry 5034 (class 2604 OID 25244)
-- Name: failed_jobs id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.failed_jobs ALTER COLUMN id SET DEFAULT nextval('public.failed_jobs_id_seq'::regclass);


--
-- TOC entry 5033 (class 2604 OID 25214)
-- Name: jobs id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.jobs ALTER COLUMN id SET DEFAULT nextval('public.jobs_id_seq'::regclass);


--
-- TOC entry 5031 (class 2604 OID 25146)
-- Name: migrations id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.migrations ALTER COLUMN id SET DEFAULT nextval('public.migrations_id_seq'::regclass);


--
-- TOC entry 5090 (class 2604 OID 25697)
-- Name: order_items id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.order_items ALTER COLUMN id SET DEFAULT nextval('public.order_items_id_seq'::regclass);


--
-- TOC entry 5085 (class 2604 OID 25675)
-- Name: orders id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.orders ALTER COLUMN id SET DEFAULT nextval('public.orders_id_seq'::regclass);


--
-- TOC entry 5105 (class 2604 OID 32975)
-- Name: product_reviews review_id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.product_reviews ALTER COLUMN review_id SET DEFAULT nextval('public.product_reviews_review_id_seq'::regclass);


--
-- TOC entry 5078 (class 2604 OID 25644)
-- Name: products id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.products ALTER COLUMN id SET DEFAULT nextval('public.products_id_seq'::regclass);


--
-- TOC entry 5057 (class 2604 OID 25406)
-- Name: sales_cart cart_id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.sales_cart ALTER COLUMN cart_id SET DEFAULT nextval('public.sales_cart_cart_id_seq'::regclass);


--
-- TOC entry 5061 (class 2604 OID 25447)
-- Name: sales_cart_address address_id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.sales_cart_address ALTER COLUMN address_id SET DEFAULT nextval('public.sales_cart_address_address_id_seq'::regclass);


--
-- TOC entry 5062 (class 2604 OID 25473)
-- Name: sales_cart_payment payment_id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.sales_cart_payment ALTER COLUMN payment_id SET DEFAULT nextval('public.sales_cart_payment_payment_id_seq'::regclass);


--
-- TOC entry 5059 (class 2604 OID 25423)
-- Name: sales_cart_product item_id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.sales_cart_product ALTER COLUMN item_id SET DEFAULT nextval('public.sales_cart_product_item_id_seq'::regclass);


--
-- TOC entry 5063 (class 2604 OID 25489)
-- Name: sales_order order_id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.sales_order ALTER COLUMN order_id SET DEFAULT nextval('public.sales_order_order_id_seq'::regclass);


--
-- TOC entry 5071 (class 2604 OID 25548)
-- Name: sales_order_address address_id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.sales_order_address ALTER COLUMN address_id SET DEFAULT nextval('public.sales_order_address_address_id_seq'::regclass);


--
-- TOC entry 5073 (class 2604 OID 25575)
-- Name: sales_order_payment payment_id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.sales_order_payment ALTER COLUMN payment_id SET DEFAULT nextval('public.sales_order_payment_payment_id_seq'::regclass);


--
-- TOC entry 5069 (class 2604 OID 25522)
-- Name: sales_order_product item_id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.sales_order_product ALTER COLUMN item_id SET DEFAULT nextval('public.sales_order_product_item_id_seq'::regclass);


--
-- TOC entry 5099 (class 2604 OID 25775)
-- Name: saved_items id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.saved_items ALTER COLUMN id SET DEFAULT nextval('public.saved_items_id_seq'::regclass);


--
-- TOC entry 5108 (class 2604 OID 33005)
-- Name: user_addresses id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.user_addresses ALTER COLUMN id SET DEFAULT nextval('public.user_addresses_id_seq'::regclass);


--
-- TOC entry 5032 (class 2604 OID 25156)
-- Name: users id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.users ALTER COLUMN id SET DEFAULT nextval('public.users_id_seq'::regclass);


--
-- TOC entry 5103 (class 2604 OID 32950)
-- Name: wishlist wishlist_id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.wishlist ALTER COLUMN wishlist_id SET DEFAULT nextval('public.wishlist_wishlist_id_seq'::regclass);


--
-- TOC entry 5097 (class 2604 OID 25754)
-- Name: wishlists id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.wishlists ALTER COLUMN id SET DEFAULT nextval('public.wishlists_id_seq'::regclass);


--
-- TOC entry 5510 (class 0 OID 25632)
-- Dependencies: 265
-- Data for Name: brands; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.brands (id, name) FROM stdin;
1	TechPro
2	StyleMax
3	HomeComfort
4	SportFit
5	ReadMore
6	ElectroPlus
7	FashionHub
8	GadgetWorld
9	UrbanStyle
10	CozyHome
11	ActiveLife
12	BookNest
\.


--
-- TOC entry 5470 (class 0 OID 25188)
-- Dependencies: 225
-- Data for Name: cache; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.cache (key, value, expiration) FROM stdin;
\.


--
-- TOC entry 5471 (class 0 OID 25199)
-- Dependencies: 226
-- Data for Name: cache_locks; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.cache_locks (key, owner, expiration) FROM stdin;
\.


--
-- TOC entry 5520 (class 0 OID 25729)
-- Dependencies: 275
-- Data for Name: cart_items; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.cart_items (id, cart_id, product_id, quantity, created_at) FROM stdin;
\.


--
-- TOC entry 5518 (class 0 OID 25714)
-- Dependencies: 273
-- Data for Name: carts; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.carts (id, user_id, session_id, created_at, updated_at) FROM stdin;
\.


--
-- TOC entry 5486 (class 0 OID 25356)
-- Dependencies: 241
-- Data for Name: catalog_category_attribute; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.catalog_category_attribute (attribute_id, category_entity_id, attribute_code, attribute_value, created_at) FROM stdin;
\.


--
-- TOC entry 5484 (class 0 OID 25331)
-- Dependencies: 239
-- Data for Name: catalog_category_entity; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.catalog_category_entity (entity_id, name, slug, parent_id, "position", is_active, created_at, updated_at) FROM stdin;
1	Laptops	laptops	\N	1	t	2026-02-03 09:16:25	2026-02-03 09:16:25
2	Smartphones	smartphones	\N	2	t	2026-02-03 09:16:25	2026-02-03 09:16:25
3	Accessories	accessories	\N	3	t	2026-02-03 09:16:25	2026-02-03 09:16:25
4	Sports	sports	\N	0	t	\N	\N
5	Books	books	\N	0	t	\N	\N
\.


--
-- TOC entry 5488 (class 0 OID 25377)
-- Dependencies: 243
-- Data for Name: catalog_category_product; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.catalog_category_product (relation_id, category_entity_id, product_entity_id, "position", created_at) FROM stdin;
505	1	1	0	2026-02-04 09:24:10
506	1	2	0	2026-02-04 09:24:10
507	1	3	0	2026-02-04 09:24:10
508	1	4	0	2026-02-04 09:24:10
509	1	5	0	2026-02-04 09:24:10
510	1	6	0	2026-02-04 09:24:10
511	1	7	0	2026-02-04 09:24:10
512	1	8	0	2026-02-04 09:24:10
513	1	9	0	2026-02-04 09:24:10
514	1	10	0	2026-02-04 09:24:10
515	1	11	0	2026-02-04 09:24:10
516	1	12	0	2026-02-04 09:24:10
517	1	13	0	2026-02-04 09:24:10
518	1	14	0	2026-02-04 09:24:10
519	1	15	0	2026-02-04 09:24:10
520	1	16	0	2026-02-04 09:24:10
521	1	17	0	2026-02-04 09:24:10
522	1	18	0	2026-02-04 09:24:10
523	1	19	0	2026-02-04 09:24:10
524	1	20	0	2026-02-04 09:24:10
525	1	21	0	2026-02-04 09:24:10
526	1	22	0	2026-02-04 09:24:10
527	1	23	0	2026-02-04 09:24:10
528	1	24	0	2026-02-04 09:24:10
529	1	25	0	2026-02-04 09:24:10
530	1	26	0	2026-02-04 09:24:10
531	1	27	0	2026-02-04 09:24:10
532	1	28	0	2026-02-04 09:24:10
533	1	29	0	2026-02-04 09:24:10
534	1	30	0	2026-02-04 09:24:10
535	1	31	0	2026-02-04 09:24:10
536	1	32	0	2026-02-04 09:24:10
537	1	33	0	2026-02-04 09:24:10
538	1	34	0	2026-02-04 09:24:10
539	1	35	0	2026-02-04 09:24:10
540	1	36	0	2026-02-04 09:24:10
541	1	37	0	2026-02-04 09:24:10
542	1	38	0	2026-02-04 09:24:10
543	1	39	0	2026-02-04 09:24:10
544	1	40	0	2026-02-04 09:24:10
545	1	41	0	2026-02-04 09:24:10
546	1	42	0	2026-02-04 09:24:10
547	1	43	0	2026-02-04 09:24:10
548	1	44	0	2026-02-04 09:24:10
549	1	45	0	2026-02-04 09:24:10
550	1	46	0	2026-02-04 09:24:10
551	1	47	0	2026-02-04 09:24:10
552	1	48	0	2026-02-04 09:24:10
553	1	49	0	2026-02-04 09:24:10
554	1	50	0	2026-02-04 09:24:10
555	1	51	0	2026-02-04 09:24:10
556	1	52	0	2026-02-04 09:24:10
557	1	53	0	2026-02-04 09:24:10
558	1	54	0	2026-02-04 09:24:10
559	1	55	0	2026-02-04 09:24:10
560	1	56	0	2026-02-04 09:24:10
561	1	57	0	2026-02-04 09:24:10
562	1	58	0	2026-02-04 09:24:10
563	1	59	0	2026-02-04 09:24:10
564	1	60	0	2026-02-04 09:24:10
565	1	61	0	2026-02-04 09:24:10
566	1	62	0	2026-02-04 09:24:10
567	1	63	0	2026-02-04 09:24:10
568	1	64	0	2026-02-04 09:24:10
569	1	65	0	2026-02-04 09:24:10
570	1	66	0	2026-02-04 09:24:10
571	1	67	0	2026-02-04 09:24:10
572	1	68	0	2026-02-04 09:24:10
573	1	69	0	2026-02-04 09:24:10
574	1	70	0	2026-02-04 09:24:10
575	1	71	0	2026-02-04 09:24:10
576	1	72	0	2026-02-04 09:24:10
577	1	73	0	2026-02-04 09:24:10
578	1	74	0	2026-02-04 09:24:10
579	1	75	0	2026-02-04 09:24:10
580	1	76	0	2026-02-04 09:24:10
581	1	77	0	2026-02-04 09:24:10
582	1	78	0	2026-02-04 09:24:10
583	1	79	0	2026-02-04 09:24:10
584	1	80	0	2026-02-04 09:24:10
585	1	81	0	2026-02-04 09:24:10
586	1	82	0	2026-02-04 09:24:10
587	1	83	0	2026-02-04 09:24:10
588	1	84	0	2026-02-04 09:24:10
589	1	85	0	2026-02-04 09:24:10
590	1	86	0	2026-02-04 09:24:10
591	1	87	0	2026-02-04 09:24:10
592	1	88	0	2026-02-04 09:24:10
593	1	89	0	2026-02-04 09:24:10
594	1	90	0	2026-02-04 09:24:10
595	1	91	0	2026-02-04 09:24:10
596	1	92	0	2026-02-04 09:24:10
597	1	93	0	2026-02-04 09:24:10
598	1	94	0	2026-02-04 09:24:10
599	1	95	0	2026-02-04 09:24:10
600	1	96	0	2026-02-04 09:24:10
601	1	97	0	2026-02-04 09:24:10
602	1	98	0	2026-02-04 09:24:10
603	1	99	0	2026-02-04 09:24:10
604	1	100	0	2026-02-04 09:24:10
605	2	101	0	2026-02-04 09:24:10
606	2	102	0	2026-02-04 09:24:10
607	2	103	0	2026-02-04 09:24:10
608	2	104	0	2026-02-04 09:24:10
609	2	105	0	2026-02-04 09:24:10
610	2	106	0	2026-02-04 09:24:10
611	2	107	0	2026-02-04 09:24:10
612	2	108	0	2026-02-04 09:24:10
613	2	109	0	2026-02-04 09:24:10
614	2	110	0	2026-02-04 09:24:10
615	2	111	0	2026-02-04 09:24:10
616	2	112	0	2026-02-04 09:24:10
617	2	113	0	2026-02-04 09:24:10
618	2	114	0	2026-02-04 09:24:10
619	2	115	0	2026-02-04 09:24:10
620	2	116	0	2026-02-04 09:24:10
621	2	117	0	2026-02-04 09:24:10
622	2	118	0	2026-02-04 09:24:10
623	2	119	0	2026-02-04 09:24:10
624	2	120	0	2026-02-04 09:24:10
625	2	121	0	2026-02-04 09:24:10
626	2	122	0	2026-02-04 09:24:10
627	2	123	0	2026-02-04 09:24:10
628	2	124	0	2026-02-04 09:24:10
629	2	125	0	2026-02-04 09:24:10
630	2	126	0	2026-02-04 09:24:10
631	2	127	0	2026-02-04 09:24:10
632	2	128	0	2026-02-04 09:24:10
633	2	129	0	2026-02-04 09:24:10
634	2	130	0	2026-02-04 09:24:10
635	2	131	0	2026-02-04 09:24:10
636	2	132	0	2026-02-04 09:24:10
637	2	133	0	2026-02-04 09:24:10
638	2	134	0	2026-02-04 09:24:10
639	2	135	0	2026-02-04 09:24:10
640	2	136	0	2026-02-04 09:24:10
641	2	137	0	2026-02-04 09:24:10
642	2	138	0	2026-02-04 09:24:10
643	2	139	0	2026-02-04 09:24:10
644	2	140	0	2026-02-04 09:24:10
645	2	141	0	2026-02-04 09:24:10
646	2	142	0	2026-02-04 09:24:10
647	2	143	0	2026-02-04 09:24:10
648	2	144	0	2026-02-04 09:24:10
649	2	145	0	2026-02-04 09:24:10
650	2	146	0	2026-02-04 09:24:10
651	2	147	0	2026-02-04 09:24:10
652	2	148	0	2026-02-04 09:24:10
653	2	149	0	2026-02-04 09:24:10
654	2	150	0	2026-02-04 09:24:10
655	2	151	0	2026-02-04 09:24:10
656	2	152	0	2026-02-04 09:24:10
657	2	153	0	2026-02-04 09:24:10
658	2	154	0	2026-02-04 09:24:10
659	2	155	0	2026-02-04 09:24:10
660	2	156	0	2026-02-04 09:24:10
661	2	157	0	2026-02-04 09:24:10
662	2	158	0	2026-02-04 09:24:10
663	2	159	0	2026-02-04 09:24:10
664	2	160	0	2026-02-04 09:24:10
665	2	161	0	2026-02-04 09:24:10
666	2	162	0	2026-02-04 09:24:10
667	2	163	0	2026-02-04 09:24:10
668	2	164	0	2026-02-04 09:24:10
669	2	165	0	2026-02-04 09:24:10
670	2	166	0	2026-02-04 09:24:10
671	2	167	0	2026-02-04 09:24:10
672	2	168	0	2026-02-04 09:24:10
673	2	169	0	2026-02-04 09:24:10
674	2	170	0	2026-02-04 09:24:10
675	2	171	0	2026-02-04 09:24:10
676	2	172	0	2026-02-04 09:24:10
677	2	173	0	2026-02-04 09:24:10
678	2	174	0	2026-02-04 09:24:10
679	2	175	0	2026-02-04 09:24:10
680	2	176	0	2026-02-04 09:24:10
681	2	177	0	2026-02-04 09:24:10
682	2	178	0	2026-02-04 09:24:10
683	2	179	0	2026-02-04 09:24:10
684	2	180	0	2026-02-04 09:24:10
685	2	181	0	2026-02-04 09:24:10
686	2	182	0	2026-02-04 09:24:10
687	2	183	0	2026-02-04 09:24:10
688	2	184	0	2026-02-04 09:24:10
689	2	185	0	2026-02-04 09:24:10
690	2	186	0	2026-02-04 09:24:10
691	2	187	0	2026-02-04 09:24:10
692	2	188	0	2026-02-04 09:24:10
693	2	189	0	2026-02-04 09:24:10
694	2	190	0	2026-02-04 09:24:10
695	2	191	0	2026-02-04 09:24:10
696	2	192	0	2026-02-04 09:24:10
697	2	193	0	2026-02-04 09:24:10
698	2	194	0	2026-02-04 09:24:10
699	2	195	0	2026-02-04 09:24:10
700	2	196	0	2026-02-04 09:24:10
701	2	197	0	2026-02-04 09:24:10
702	2	198	0	2026-02-04 09:24:10
703	2	199	0	2026-02-04 09:24:10
704	2	200	0	2026-02-04 09:24:10
705	3	201	0	2026-02-04 09:24:10
706	3	202	0	2026-02-04 09:24:10
707	3	203	0	2026-02-04 09:24:10
708	3	204	0	2026-02-04 09:24:10
709	3	205	0	2026-02-04 09:24:10
710	3	206	0	2026-02-04 09:24:10
711	3	207	0	2026-02-04 09:24:10
712	3	208	0	2026-02-04 09:24:10
713	3	209	0	2026-02-04 09:24:10
714	3	210	0	2026-02-04 09:24:10
715	3	211	0	2026-02-04 09:24:10
716	3	212	0	2026-02-04 09:24:10
717	3	213	0	2026-02-04 09:24:10
718	3	214	0	2026-02-04 09:24:10
719	3	215	0	2026-02-04 09:24:10
720	3	216	0	2026-02-04 09:24:10
721	3	217	0	2026-02-04 09:24:10
722	3	218	0	2026-02-04 09:24:10
723	3	219	0	2026-02-04 09:24:10
724	3	220	0	2026-02-04 09:24:10
725	3	221	0	2026-02-04 09:24:10
726	3	222	0	2026-02-04 09:24:10
727	3	223	0	2026-02-04 09:24:10
728	3	224	0	2026-02-04 09:24:10
729	3	225	0	2026-02-04 09:24:10
730	3	226	0	2026-02-04 09:24:10
731	3	227	0	2026-02-04 09:24:10
732	3	228	0	2026-02-04 09:24:10
733	3	229	0	2026-02-04 09:24:10
734	3	230	0	2026-02-04 09:24:10
735	3	231	0	2026-02-04 09:24:10
736	3	232	0	2026-02-04 09:24:10
737	3	233	0	2026-02-04 09:24:10
738	3	234	0	2026-02-04 09:24:10
739	3	235	0	2026-02-04 09:24:10
740	3	236	0	2026-02-04 09:24:10
741	3	237	0	2026-02-04 09:24:10
742	3	238	0	2026-02-04 09:24:10
743	3	239	0	2026-02-04 09:24:10
744	3	240	0	2026-02-04 09:24:10
745	3	241	0	2026-02-04 09:24:10
746	3	242	0	2026-02-04 09:24:10
747	3	243	0	2026-02-04 09:24:10
748	3	244	0	2026-02-04 09:24:10
749	3	245	0	2026-02-04 09:24:10
750	3	246	0	2026-02-04 09:24:10
751	3	247	0	2026-02-04 09:24:10
752	3	248	0	2026-02-04 09:24:10
753	3	249	0	2026-02-04 09:24:10
754	3	250	0	2026-02-04 09:24:10
755	3	251	0	2026-02-04 09:24:10
756	3	252	0	2026-02-04 09:24:10
757	3	253	0	2026-02-04 09:24:10
758	3	254	0	2026-02-04 09:24:10
759	3	255	0	2026-02-04 09:24:10
760	3	256	0	2026-02-04 09:24:10
761	3	257	0	2026-02-04 09:24:10
762	3	258	0	2026-02-04 09:24:10
763	3	259	0	2026-02-04 09:24:10
764	3	260	0	2026-02-04 09:24:10
765	3	261	0	2026-02-04 09:24:10
766	3	262	0	2026-02-04 09:24:10
767	3	263	0	2026-02-04 09:24:10
768	3	264	0	2026-02-04 09:24:10
769	3	265	0	2026-02-04 09:24:10
770	3	266	0	2026-02-04 09:24:10
771	3	267	0	2026-02-04 09:24:10
772	3	268	0	2026-02-04 09:24:10
773	3	269	0	2026-02-04 09:24:10
774	3	270	0	2026-02-04 09:24:10
775	3	271	0	2026-02-04 09:24:10
776	3	272	0	2026-02-04 09:24:10
777	3	273	0	2026-02-04 09:24:10
778	3	274	0	2026-02-04 09:24:10
779	3	275	0	2026-02-04 09:24:10
780	3	276	0	2026-02-04 09:24:10
781	3	277	0	2026-02-04 09:24:10
782	3	278	0	2026-02-04 09:24:10
783	3	279	0	2026-02-04 09:24:10
784	3	280	0	2026-02-04 09:24:10
785	3	281	0	2026-02-04 09:24:10
786	3	282	0	2026-02-04 09:24:10
787	3	283	0	2026-02-04 09:24:10
788	3	284	0	2026-02-04 09:24:10
789	3	285	0	2026-02-04 09:24:10
790	3	286	0	2026-02-04 09:24:10
791	3	287	0	2026-02-04 09:24:10
792	3	288	0	2026-02-04 09:24:10
793	3	289	0	2026-02-04 09:24:10
794	3	290	0	2026-02-04 09:24:10
795	3	291	0	2026-02-04 09:24:10
796	3	292	0	2026-02-04 09:24:10
797	3	293	0	2026-02-04 09:24:10
798	3	294	0	2026-02-04 09:24:10
799	3	295	0	2026-02-04 09:24:10
800	3	296	0	2026-02-04 09:24:10
801	3	297	0	2026-02-04 09:24:10
802	3	298	0	2026-02-04 09:24:10
803	3	299	0	2026-02-04 09:24:10
804	3	300	0	2026-02-04 09:24:10
805	4	301	0	2026-02-04 09:24:10
806	4	302	0	2026-02-04 09:24:10
807	4	303	0	2026-02-04 09:24:10
808	4	304	0	2026-02-04 09:24:10
809	4	305	0	2026-02-04 09:24:10
810	4	306	0	2026-02-04 09:24:10
811	4	307	0	2026-02-04 09:24:10
812	4	308	0	2026-02-04 09:24:10
813	4	309	0	2026-02-04 09:24:10
814	4	310	0	2026-02-04 09:24:10
815	4	311	0	2026-02-04 09:24:10
816	4	312	0	2026-02-04 09:24:10
817	4	313	0	2026-02-04 09:24:10
818	4	314	0	2026-02-04 09:24:10
819	4	315	0	2026-02-04 09:24:10
820	4	316	0	2026-02-04 09:24:10
821	4	317	0	2026-02-04 09:24:10
822	4	318	0	2026-02-04 09:24:10
823	4	319	0	2026-02-04 09:24:10
824	4	320	0	2026-02-04 09:24:10
825	4	321	0	2026-02-04 09:24:10
826	4	322	0	2026-02-04 09:24:10
827	4	323	0	2026-02-04 09:24:10
828	4	324	0	2026-02-04 09:24:10
829	4	325	0	2026-02-04 09:24:10
830	4	326	0	2026-02-04 09:24:10
831	4	327	0	2026-02-04 09:24:10
832	4	328	0	2026-02-04 09:24:10
833	4	329	0	2026-02-04 09:24:10
834	4	330	0	2026-02-04 09:24:10
835	4	331	0	2026-02-04 09:24:10
836	4	332	0	2026-02-04 09:24:10
837	4	333	0	2026-02-04 09:24:10
838	4	334	0	2026-02-04 09:24:10
839	4	335	0	2026-02-04 09:24:10
840	4	336	0	2026-02-04 09:24:10
841	4	337	0	2026-02-04 09:24:10
842	4	338	0	2026-02-04 09:24:10
843	4	339	0	2026-02-04 09:24:10
844	4	340	0	2026-02-04 09:24:10
845	4	341	0	2026-02-04 09:24:10
846	4	342	0	2026-02-04 09:24:10
847	4	343	0	2026-02-04 09:24:10
848	4	344	0	2026-02-04 09:24:10
849	4	345	0	2026-02-04 09:24:10
850	4	346	0	2026-02-04 09:24:10
851	4	347	0	2026-02-04 09:24:10
852	4	348	0	2026-02-04 09:24:10
853	4	349	0	2026-02-04 09:24:10
854	4	350	0	2026-02-04 09:24:10
855	4	351	0	2026-02-04 09:24:10
856	4	352	0	2026-02-04 09:24:10
857	4	353	0	2026-02-04 09:24:10
858	4	354	0	2026-02-04 09:24:10
859	4	355	0	2026-02-04 09:24:10
860	4	356	0	2026-02-04 09:24:10
861	4	357	0	2026-02-04 09:24:10
862	4	358	0	2026-02-04 09:24:10
863	4	359	0	2026-02-04 09:24:10
864	4	360	0	2026-02-04 09:24:10
865	4	361	0	2026-02-04 09:24:10
866	4	362	0	2026-02-04 09:24:10
867	4	363	0	2026-02-04 09:24:10
868	4	364	0	2026-02-04 09:24:10
869	4	365	0	2026-02-04 09:24:10
870	4	366	0	2026-02-04 09:24:10
871	4	367	0	2026-02-04 09:24:10
872	4	368	0	2026-02-04 09:24:10
873	4	369	0	2026-02-04 09:24:10
874	4	370	0	2026-02-04 09:24:10
875	4	371	0	2026-02-04 09:24:10
876	4	372	0	2026-02-04 09:24:10
877	4	373	0	2026-02-04 09:24:10
878	4	374	0	2026-02-04 09:24:10
879	4	375	0	2026-02-04 09:24:10
880	4	376	0	2026-02-04 09:24:10
881	4	377	0	2026-02-04 09:24:10
882	4	378	0	2026-02-04 09:24:10
883	4	379	0	2026-02-04 09:24:10
884	4	380	0	2026-02-04 09:24:10
885	4	381	0	2026-02-04 09:24:10
886	4	382	0	2026-02-04 09:24:10
887	4	383	0	2026-02-04 09:24:10
888	4	384	0	2026-02-04 09:24:10
889	4	385	0	2026-02-04 09:24:10
890	4	386	0	2026-02-04 09:24:10
891	4	387	0	2026-02-04 09:24:10
892	4	388	0	2026-02-04 09:24:10
893	4	389	0	2026-02-04 09:24:10
894	4	390	0	2026-02-04 09:24:10
895	4	391	0	2026-02-04 09:24:10
896	4	392	0	2026-02-04 09:24:10
897	4	393	0	2026-02-04 09:24:10
898	4	394	0	2026-02-04 09:24:10
899	4	395	0	2026-02-04 09:24:10
900	4	396	0	2026-02-04 09:24:10
901	4	397	0	2026-02-04 09:24:10
902	4	398	0	2026-02-04 09:24:10
903	4	399	0	2026-02-04 09:24:10
904	4	400	0	2026-02-04 09:24:10
905	5	401	0	2026-02-04 09:24:10
906	5	402	0	2026-02-04 09:24:10
907	5	403	0	2026-02-04 09:24:10
908	5	404	0	2026-02-04 09:24:10
909	5	405	0	2026-02-04 09:24:10
910	5	406	0	2026-02-04 09:24:10
911	5	407	0	2026-02-04 09:24:10
912	5	408	0	2026-02-04 09:24:10
913	5	409	0	2026-02-04 09:24:10
914	5	410	0	2026-02-04 09:24:10
915	5	411	0	2026-02-04 09:24:10
916	5	412	0	2026-02-04 09:24:10
917	5	413	0	2026-02-04 09:24:10
918	5	414	0	2026-02-04 09:24:10
919	5	415	0	2026-02-04 09:24:10
920	5	416	0	2026-02-04 09:24:10
921	5	417	0	2026-02-04 09:24:10
922	5	418	0	2026-02-04 09:24:10
923	5	419	0	2026-02-04 09:24:10
924	5	420	0	2026-02-04 09:24:10
925	5	421	0	2026-02-04 09:24:10
926	5	422	0	2026-02-04 09:24:10
927	5	423	0	2026-02-04 09:24:10
928	5	424	0	2026-02-04 09:24:10
929	5	425	0	2026-02-04 09:24:10
930	5	426	0	2026-02-04 09:24:10
931	5	427	0	2026-02-04 09:24:10
932	5	428	0	2026-02-04 09:24:10
933	5	429	0	2026-02-04 09:24:10
934	5	430	0	2026-02-04 09:24:10
935	5	431	0	2026-02-04 09:24:10
936	5	432	0	2026-02-04 09:24:10
937	5	433	0	2026-02-04 09:24:10
938	5	434	0	2026-02-04 09:24:10
939	5	435	0	2026-02-04 09:24:10
940	5	436	0	2026-02-04 09:24:10
941	5	437	0	2026-02-04 09:24:10
942	5	438	0	2026-02-04 09:24:10
943	5	439	0	2026-02-04 09:24:10
944	5	440	0	2026-02-04 09:24:10
945	5	441	0	2026-02-04 09:24:10
946	5	442	0	2026-02-04 09:24:10
947	5	443	0	2026-02-04 09:24:10
948	5	444	0	2026-02-04 09:24:10
949	5	445	0	2026-02-04 09:24:10
950	5	446	0	2026-02-04 09:24:10
951	5	447	0	2026-02-04 09:24:10
952	5	448	0	2026-02-04 09:24:10
953	5	449	0	2026-02-04 09:24:10
954	5	450	0	2026-02-04 09:24:10
955	5	451	0	2026-02-04 09:24:10
956	5	452	0	2026-02-04 09:24:10
957	5	453	0	2026-02-04 09:24:10
958	5	454	0	2026-02-04 09:24:10
959	5	455	0	2026-02-04 09:24:10
960	5	456	0	2026-02-04 09:24:10
961	5	457	0	2026-02-04 09:24:10
962	5	458	0	2026-02-04 09:24:10
963	5	459	0	2026-02-04 09:24:10
964	5	460	0	2026-02-04 09:24:10
965	5	461	0	2026-02-04 09:24:10
966	5	462	0	2026-02-04 09:24:10
967	5	463	0	2026-02-04 09:24:10
968	5	464	0	2026-02-04 09:24:10
969	5	465	0	2026-02-04 09:24:10
970	5	466	0	2026-02-04 09:24:10
971	5	467	0	2026-02-04 09:24:10
972	5	468	0	2026-02-04 09:24:10
973	5	469	0	2026-02-04 09:24:10
974	5	470	0	2026-02-04 09:24:10
975	5	471	0	2026-02-04 09:24:10
976	5	472	0	2026-02-04 09:24:10
977	5	473	0	2026-02-04 09:24:10
978	5	474	0	2026-02-04 09:24:10
979	5	475	0	2026-02-04 09:24:10
980	5	476	0	2026-02-04 09:24:10
981	5	477	0	2026-02-04 09:24:10
982	5	478	0	2026-02-04 09:24:10
983	5	479	0	2026-02-04 09:24:10
984	5	480	0	2026-02-04 09:24:10
985	5	481	0	2026-02-04 09:24:10
986	5	482	0	2026-02-04 09:24:10
987	5	483	0	2026-02-04 09:24:10
988	5	484	0	2026-02-04 09:24:10
989	5	485	0	2026-02-04 09:24:10
990	5	486	0	2026-02-04 09:24:10
991	5	487	0	2026-02-04 09:24:10
992	5	488	0	2026-02-04 09:24:10
993	5	489	0	2026-02-04 09:24:10
994	5	490	0	2026-02-04 09:24:10
995	5	491	0	2026-02-04 09:24:10
996	5	492	0	2026-02-04 09:24:10
997	5	493	0	2026-02-04 09:24:10
998	5	494	0	2026-02-04 09:24:10
999	5	495	0	2026-02-04 09:24:10
1000	5	496	0	2026-02-04 09:24:10
1001	5	497	0	2026-02-04 09:24:10
1002	5	498	0	2026-02-04 09:24:10
1003	5	499	0	2026-02-04 09:24:10
1004	5	500	0	2026-02-04 09:24:10
\.


--
-- TOC entry 5480 (class 0 OID 25288)
-- Dependencies: 235
-- Data for Name: catalog_product_attribute; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.catalog_product_attribute (attribute_id, product_entity_id, attribute_code, attribute_value, created_at) FROM stdin;
505	1	brand	ElectroPlus	2026-02-04 09:24:10
506	2	brand	TechPro	2026-02-04 09:24:10
507	3	brand	TechPro	2026-02-04 09:24:10
508	4	brand	GadgetWorld	2026-02-04 09:24:10
509	5	brand	TechPro	2026-02-04 09:24:10
510	6	brand	ElectroPlus	2026-02-04 09:24:10
511	7	brand	TechPro	2026-02-04 09:24:10
512	8	brand	ElectroPlus	2026-02-04 09:24:10
513	9	brand	TechPro	2026-02-04 09:24:10
514	10	brand	TechPro	2026-02-04 09:24:10
515	11	brand	GadgetWorld	2026-02-04 09:24:10
516	12	brand	GadgetWorld	2026-02-04 09:24:10
517	13	brand	GadgetWorld	2026-02-04 09:24:10
518	14	brand	TechPro	2026-02-04 09:24:10
519	15	brand	ElectroPlus	2026-02-04 09:24:10
520	16	brand	GadgetWorld	2026-02-04 09:24:10
521	17	brand	ElectroPlus	2026-02-04 09:24:10
522	18	brand	TechPro	2026-02-04 09:24:10
523	19	brand	TechPro	2026-02-04 09:24:10
524	20	brand	GadgetWorld	2026-02-04 09:24:10
525	21	brand	TechPro	2026-02-04 09:24:10
526	22	brand	ElectroPlus	2026-02-04 09:24:10
527	23	brand	GadgetWorld	2026-02-04 09:24:10
528	24	brand	TechPro	2026-02-04 09:24:10
529	25	brand	TechPro	2026-02-04 09:24:10
530	26	brand	GadgetWorld	2026-02-04 09:24:10
531	27	brand	ElectroPlus	2026-02-04 09:24:10
532	28	brand	GadgetWorld	2026-02-04 09:24:10
533	29	brand	GadgetWorld	2026-02-04 09:24:10
534	30	brand	GadgetWorld	2026-02-04 09:24:10
535	31	brand	TechPro	2026-02-04 09:24:10
536	32	brand	TechPro	2026-02-04 09:24:10
537	33	brand	ElectroPlus	2026-02-04 09:24:10
538	34	brand	TechPro	2026-02-04 09:24:10
539	35	brand	ElectroPlus	2026-02-04 09:24:10
540	36	brand	GadgetWorld	2026-02-04 09:24:10
541	37	brand	TechPro	2026-02-04 09:24:10
542	38	brand	TechPro	2026-02-04 09:24:10
543	39	brand	GadgetWorld	2026-02-04 09:24:10
544	40	brand	ElectroPlus	2026-02-04 09:24:10
545	41	brand	GadgetWorld	2026-02-04 09:24:10
546	42	brand	TechPro	2026-02-04 09:24:10
547	43	brand	ElectroPlus	2026-02-04 09:24:10
548	44	brand	GadgetWorld	2026-02-04 09:24:10
549	45	brand	GadgetWorld	2026-02-04 09:24:10
550	46	brand	TechPro	2026-02-04 09:24:10
551	47	brand	GadgetWorld	2026-02-04 09:24:10
552	48	brand	TechPro	2026-02-04 09:24:10
553	49	brand	GadgetWorld	2026-02-04 09:24:10
554	50	brand	GadgetWorld	2026-02-04 09:24:10
555	51	brand	ElectroPlus	2026-02-04 09:24:10
556	52	brand	GadgetWorld	2026-02-04 09:24:10
557	53	brand	TechPro	2026-02-04 09:24:10
558	54	brand	TechPro	2026-02-04 09:24:10
559	55	brand	TechPro	2026-02-04 09:24:10
560	56	brand	GadgetWorld	2026-02-04 09:24:10
561	57	brand	GadgetWorld	2026-02-04 09:24:10
562	58	brand	TechPro	2026-02-04 09:24:10
563	59	brand	GadgetWorld	2026-02-04 09:24:10
564	60	brand	TechPro	2026-02-04 09:24:10
565	61	brand	TechPro	2026-02-04 09:24:10
566	62	brand	TechPro	2026-02-04 09:24:10
567	63	brand	TechPro	2026-02-04 09:24:10
568	64	brand	TechPro	2026-02-04 09:24:10
569	65	brand	TechPro	2026-02-04 09:24:10
570	66	brand	TechPro	2026-02-04 09:24:10
571	67	brand	TechPro	2026-02-04 09:24:10
572	68	brand	TechPro	2026-02-04 09:24:10
573	69	brand	GadgetWorld	2026-02-04 09:24:10
574	70	brand	TechPro	2026-02-04 09:24:10
575	71	brand	ElectroPlus	2026-02-04 09:24:10
576	72	brand	GadgetWorld	2026-02-04 09:24:10
577	73	brand	GadgetWorld	2026-02-04 09:24:10
578	74	brand	GadgetWorld	2026-02-04 09:24:10
579	75	brand	GadgetWorld	2026-02-04 09:24:10
580	76	brand	TechPro	2026-02-04 09:24:10
581	77	brand	GadgetWorld	2026-02-04 09:24:10
582	78	brand	GadgetWorld	2026-02-04 09:24:10
583	79	brand	ElectroPlus	2026-02-04 09:24:10
584	80	brand	ElectroPlus	2026-02-04 09:24:10
585	81	brand	GadgetWorld	2026-02-04 09:24:10
586	82	brand	ElectroPlus	2026-02-04 09:24:10
587	83	brand	ElectroPlus	2026-02-04 09:24:10
588	84	brand	ElectroPlus	2026-02-04 09:24:10
589	85	brand	ElectroPlus	2026-02-04 09:24:10
590	86	brand	ElectroPlus	2026-02-04 09:24:10
591	87	brand	TechPro	2026-02-04 09:24:10
592	88	brand	ElectroPlus	2026-02-04 09:24:10
593	89	brand	ElectroPlus	2026-02-04 09:24:10
594	90	brand	ElectroPlus	2026-02-04 09:24:10
595	91	brand	GadgetWorld	2026-02-04 09:24:10
596	92	brand	TechPro	2026-02-04 09:24:10
597	93	brand	GadgetWorld	2026-02-04 09:24:10
598	94	brand	TechPro	2026-02-04 09:24:10
599	95	brand	ElectroPlus	2026-02-04 09:24:10
600	96	brand	ElectroPlus	2026-02-04 09:24:10
601	97	brand	TechPro	2026-02-04 09:24:10
602	98	brand	GadgetWorld	2026-02-04 09:24:10
603	99	brand	TechPro	2026-02-04 09:24:10
604	100	brand	TechPro	2026-02-04 09:24:10
605	101	brand	UrbanStyle	2026-02-04 09:24:10
606	102	brand	UrbanStyle	2026-02-04 09:24:10
607	103	brand	StyleMax	2026-02-04 09:24:10
608	104	brand	StyleMax	2026-02-04 09:24:10
609	105	brand	StyleMax	2026-02-04 09:24:10
610	106	brand	FashionHub	2026-02-04 09:24:10
611	107	brand	UrbanStyle	2026-02-04 09:24:10
612	108	brand	UrbanStyle	2026-02-04 09:24:10
613	109	brand	FashionHub	2026-02-04 09:24:10
614	110	brand	StyleMax	2026-02-04 09:24:10
615	111	brand	UrbanStyle	2026-02-04 09:24:10
616	112	brand	StyleMax	2026-02-04 09:24:10
617	113	brand	FashionHub	2026-02-04 09:24:10
618	114	brand	StyleMax	2026-02-04 09:24:10
619	115	brand	FashionHub	2026-02-04 09:24:10
620	116	brand	StyleMax	2026-02-04 09:24:10
621	117	brand	UrbanStyle	2026-02-04 09:24:10
622	118	brand	StyleMax	2026-02-04 09:24:10
623	119	brand	StyleMax	2026-02-04 09:24:10
624	120	brand	UrbanStyle	2026-02-04 09:24:10
625	121	brand	StyleMax	2026-02-04 09:24:10
626	122	brand	StyleMax	2026-02-04 09:24:10
627	123	brand	UrbanStyle	2026-02-04 09:24:10
628	124	brand	UrbanStyle	2026-02-04 09:24:10
629	125	brand	StyleMax	2026-02-04 09:24:10
630	126	brand	UrbanStyle	2026-02-04 09:24:10
631	127	brand	StyleMax	2026-02-04 09:24:10
632	128	brand	FashionHub	2026-02-04 09:24:10
633	129	brand	FashionHub	2026-02-04 09:24:10
634	130	brand	FashionHub	2026-02-04 09:24:10
635	131	brand	UrbanStyle	2026-02-04 09:24:10
636	132	brand	UrbanStyle	2026-02-04 09:24:10
637	133	brand	StyleMax	2026-02-04 09:24:10
638	134	brand	UrbanStyle	2026-02-04 09:24:10
639	135	brand	UrbanStyle	2026-02-04 09:24:10
640	136	brand	UrbanStyle	2026-02-04 09:24:10
641	137	brand	StyleMax	2026-02-04 09:24:10
642	138	brand	UrbanStyle	2026-02-04 09:24:10
643	139	brand	UrbanStyle	2026-02-04 09:24:10
644	140	brand	StyleMax	2026-02-04 09:24:10
645	141	brand	UrbanStyle	2026-02-04 09:24:10
646	142	brand	UrbanStyle	2026-02-04 09:24:10
647	143	brand	UrbanStyle	2026-02-04 09:24:10
648	144	brand	FashionHub	2026-02-04 09:24:10
649	145	brand	StyleMax	2026-02-04 09:24:10
650	146	brand	FashionHub	2026-02-04 09:24:10
651	147	brand	StyleMax	2026-02-04 09:24:10
652	148	brand	StyleMax	2026-02-04 09:24:10
653	149	brand	StyleMax	2026-02-04 09:24:10
654	150	brand	UrbanStyle	2026-02-04 09:24:10
655	151	brand	FashionHub	2026-02-04 09:24:10
656	152	brand	StyleMax	2026-02-04 09:24:10
657	153	brand	StyleMax	2026-02-04 09:24:10
658	154	brand	UrbanStyle	2026-02-04 09:24:10
659	155	brand	FashionHub	2026-02-04 09:24:10
660	156	brand	FashionHub	2026-02-04 09:24:10
661	157	brand	FashionHub	2026-02-04 09:24:10
662	158	brand	StyleMax	2026-02-04 09:24:10
663	159	brand	UrbanStyle	2026-02-04 09:24:10
664	160	brand	UrbanStyle	2026-02-04 09:24:10
665	161	brand	UrbanStyle	2026-02-04 09:24:10
666	162	brand	UrbanStyle	2026-02-04 09:24:10
667	163	brand	UrbanStyle	2026-02-04 09:24:10
668	164	brand	StyleMax	2026-02-04 09:24:10
669	165	brand	UrbanStyle	2026-02-04 09:24:10
670	166	brand	StyleMax	2026-02-04 09:24:10
671	167	brand	FashionHub	2026-02-04 09:24:10
672	168	brand	StyleMax	2026-02-04 09:24:10
673	169	brand	FashionHub	2026-02-04 09:24:10
674	170	brand	UrbanStyle	2026-02-04 09:24:10
675	171	brand	UrbanStyle	2026-02-04 09:24:10
676	172	brand	StyleMax	2026-02-04 09:24:10
677	173	brand	UrbanStyle	2026-02-04 09:24:10
678	174	brand	FashionHub	2026-02-04 09:24:10
679	175	brand	UrbanStyle	2026-02-04 09:24:10
680	176	brand	FashionHub	2026-02-04 09:24:10
681	177	brand	UrbanStyle	2026-02-04 09:24:10
682	178	brand	UrbanStyle	2026-02-04 09:24:10
683	179	brand	FashionHub	2026-02-04 09:24:10
684	180	brand	UrbanStyle	2026-02-04 09:24:10
685	181	brand	StyleMax	2026-02-04 09:24:10
686	182	brand	StyleMax	2026-02-04 09:24:10
687	183	brand	UrbanStyle	2026-02-04 09:24:10
688	184	brand	UrbanStyle	2026-02-04 09:24:10
689	185	brand	FashionHub	2026-02-04 09:24:10
690	186	brand	UrbanStyle	2026-02-04 09:24:10
691	187	brand	UrbanStyle	2026-02-04 09:24:10
692	188	brand	StyleMax	2026-02-04 09:24:10
693	189	brand	StyleMax	2026-02-04 09:24:10
694	190	brand	StyleMax	2026-02-04 09:24:10
695	191	brand	StyleMax	2026-02-04 09:24:10
696	192	brand	UrbanStyle	2026-02-04 09:24:10
697	193	brand	FashionHub	2026-02-04 09:24:10
698	194	brand	StyleMax	2026-02-04 09:24:10
699	195	brand	StyleMax	2026-02-04 09:24:10
700	196	brand	UrbanStyle	2026-02-04 09:24:10
701	197	brand	UrbanStyle	2026-02-04 09:24:10
702	198	brand	StyleMax	2026-02-04 09:24:10
703	199	brand	FashionHub	2026-02-04 09:24:10
704	200	brand	UrbanStyle	2026-02-04 09:24:10
705	201	brand	CozyHome	2026-02-04 09:24:10
706	202	brand	CozyHome	2026-02-04 09:24:10
707	203	brand	HomeComfort	2026-02-04 09:24:10
708	204	brand	CozyHome	2026-02-04 09:24:10
709	205	brand	HomeComfort	2026-02-04 09:24:10
710	206	brand	HomeComfort	2026-02-04 09:24:10
711	207	brand	HomeComfort	2026-02-04 09:24:10
712	208	brand	CozyHome	2026-02-04 09:24:10
713	209	brand	HomeComfort	2026-02-04 09:24:10
714	210	brand	CozyHome	2026-02-04 09:24:10
715	211	brand	CozyHome	2026-02-04 09:24:10
716	212	brand	HomeComfort	2026-02-04 09:24:10
717	213	brand	HomeComfort	2026-02-04 09:24:10
718	214	brand	HomeComfort	2026-02-04 09:24:10
719	215	brand	HomeComfort	2026-02-04 09:24:10
720	216	brand	CozyHome	2026-02-04 09:24:10
721	217	brand	CozyHome	2026-02-04 09:24:10
722	218	brand	CozyHome	2026-02-04 09:24:10
723	219	brand	CozyHome	2026-02-04 09:24:10
724	220	brand	CozyHome	2026-02-04 09:24:10
725	221	brand	CozyHome	2026-02-04 09:24:10
726	222	brand	HomeComfort	2026-02-04 09:24:10
727	223	brand	HomeComfort	2026-02-04 09:24:10
728	224	brand	CozyHome	2026-02-04 09:24:10
729	225	brand	CozyHome	2026-02-04 09:24:10
730	226	brand	CozyHome	2026-02-04 09:24:10
731	227	brand	HomeComfort	2026-02-04 09:24:10
732	228	brand	CozyHome	2026-02-04 09:24:10
733	229	brand	HomeComfort	2026-02-04 09:24:10
734	230	brand	HomeComfort	2026-02-04 09:24:10
735	231	brand	HomeComfort	2026-02-04 09:24:10
736	232	brand	CozyHome	2026-02-04 09:24:10
737	233	brand	HomeComfort	2026-02-04 09:24:10
738	234	brand	HomeComfort	2026-02-04 09:24:10
739	235	brand	HomeComfort	2026-02-04 09:24:10
740	236	brand	CozyHome	2026-02-04 09:24:10
741	237	brand	CozyHome	2026-02-04 09:24:10
742	238	brand	HomeComfort	2026-02-04 09:24:10
743	239	brand	HomeComfort	2026-02-04 09:24:10
744	240	brand	HomeComfort	2026-02-04 09:24:10
745	241	brand	CozyHome	2026-02-04 09:24:10
746	242	brand	CozyHome	2026-02-04 09:24:10
747	243	brand	HomeComfort	2026-02-04 09:24:10
748	244	brand	CozyHome	2026-02-04 09:24:10
749	245	brand	CozyHome	2026-02-04 09:24:10
750	246	brand	HomeComfort	2026-02-04 09:24:10
751	247	brand	CozyHome	2026-02-04 09:24:10
752	248	brand	CozyHome	2026-02-04 09:24:10
753	249	brand	HomeComfort	2026-02-04 09:24:10
754	250	brand	CozyHome	2026-02-04 09:24:10
755	251	brand	HomeComfort	2026-02-04 09:24:10
756	252	brand	HomeComfort	2026-02-04 09:24:10
757	253	brand	CozyHome	2026-02-04 09:24:10
758	254	brand	CozyHome	2026-02-04 09:24:10
759	255	brand	HomeComfort	2026-02-04 09:24:10
760	256	brand	HomeComfort	2026-02-04 09:24:10
761	257	brand	HomeComfort	2026-02-04 09:24:10
762	258	brand	HomeComfort	2026-02-04 09:24:10
763	259	brand	CozyHome	2026-02-04 09:24:10
764	260	brand	CozyHome	2026-02-04 09:24:10
765	261	brand	HomeComfort	2026-02-04 09:24:10
766	262	brand	CozyHome	2026-02-04 09:24:10
767	263	brand	HomeComfort	2026-02-04 09:24:10
768	264	brand	HomeComfort	2026-02-04 09:24:10
769	265	brand	HomeComfort	2026-02-04 09:24:10
770	266	brand	HomeComfort	2026-02-04 09:24:10
771	267	brand	CozyHome	2026-02-04 09:24:10
772	268	brand	HomeComfort	2026-02-04 09:24:10
773	269	brand	CozyHome	2026-02-04 09:24:10
774	270	brand	CozyHome	2026-02-04 09:24:10
775	271	brand	HomeComfort	2026-02-04 09:24:10
776	272	brand	CozyHome	2026-02-04 09:24:10
777	273	brand	HomeComfort	2026-02-04 09:24:10
778	274	brand	CozyHome	2026-02-04 09:24:10
779	275	brand	CozyHome	2026-02-04 09:24:10
780	276	brand	HomeComfort	2026-02-04 09:24:10
781	277	brand	HomeComfort	2026-02-04 09:24:10
782	278	brand	HomeComfort	2026-02-04 09:24:10
783	279	brand	HomeComfort	2026-02-04 09:24:10
784	280	brand	HomeComfort	2026-02-04 09:24:10
785	281	brand	HomeComfort	2026-02-04 09:24:10
786	282	brand	HomeComfort	2026-02-04 09:24:10
787	283	brand	HomeComfort	2026-02-04 09:24:10
788	284	brand	CozyHome	2026-02-04 09:24:10
789	285	brand	HomeComfort	2026-02-04 09:24:10
790	286	brand	HomeComfort	2026-02-04 09:24:10
791	287	brand	CozyHome	2026-02-04 09:24:10
792	288	brand	HomeComfort	2026-02-04 09:24:10
793	289	brand	CozyHome	2026-02-04 09:24:10
794	290	brand	HomeComfort	2026-02-04 09:24:10
795	291	brand	CozyHome	2026-02-04 09:24:10
796	292	brand	HomeComfort	2026-02-04 09:24:10
797	293	brand	HomeComfort	2026-02-04 09:24:10
798	294	brand	CozyHome	2026-02-04 09:24:10
799	295	brand	CozyHome	2026-02-04 09:24:10
800	296	brand	HomeComfort	2026-02-04 09:24:10
801	297	brand	CozyHome	2026-02-04 09:24:10
802	298	brand	CozyHome	2026-02-04 09:24:10
803	299	brand	HomeComfort	2026-02-04 09:24:10
804	300	brand	CozyHome	2026-02-04 09:24:10
805	301	brand	ActiveLife	2026-02-04 09:24:10
806	302	brand	SportFit	2026-02-04 09:24:10
807	303	brand	ActiveLife	2026-02-04 09:24:10
808	304	brand	ActiveLife	2026-02-04 09:24:10
809	305	brand	ActiveLife	2026-02-04 09:24:10
810	306	brand	SportFit	2026-02-04 09:24:10
811	307	brand	SportFit	2026-02-04 09:24:10
812	308	brand	SportFit	2026-02-04 09:24:10
813	309	brand	ActiveLife	2026-02-04 09:24:10
814	310	brand	ActiveLife	2026-02-04 09:24:10
815	311	brand	ActiveLife	2026-02-04 09:24:10
816	312	brand	SportFit	2026-02-04 09:24:10
817	313	brand	SportFit	2026-02-04 09:24:10
818	314	brand	SportFit	2026-02-04 09:24:10
819	315	brand	ActiveLife	2026-02-04 09:24:10
820	316	brand	SportFit	2026-02-04 09:24:10
821	317	brand	ActiveLife	2026-02-04 09:24:10
822	318	brand	ActiveLife	2026-02-04 09:24:10
823	319	brand	SportFit	2026-02-04 09:24:10
824	320	brand	ActiveLife	2026-02-04 09:24:10
825	321	brand	ActiveLife	2026-02-04 09:24:10
826	322	brand	ActiveLife	2026-02-04 09:24:10
827	323	brand	SportFit	2026-02-04 09:24:10
828	324	brand	ActiveLife	2026-02-04 09:24:10
829	325	brand	SportFit	2026-02-04 09:24:10
830	326	brand	SportFit	2026-02-04 09:24:10
831	327	brand	SportFit	2026-02-04 09:24:10
832	328	brand	ActiveLife	2026-02-04 09:24:10
833	329	brand	SportFit	2026-02-04 09:24:10
834	330	brand	SportFit	2026-02-04 09:24:10
835	331	brand	SportFit	2026-02-04 09:24:10
836	332	brand	ActiveLife	2026-02-04 09:24:10
837	333	brand	SportFit	2026-02-04 09:24:10
838	334	brand	SportFit	2026-02-04 09:24:10
839	335	brand	ActiveLife	2026-02-04 09:24:10
840	336	brand	SportFit	2026-02-04 09:24:10
841	337	brand	SportFit	2026-02-04 09:24:10
842	338	brand	SportFit	2026-02-04 09:24:10
843	339	brand	SportFit	2026-02-04 09:24:10
844	340	brand	SportFit	2026-02-04 09:24:10
845	341	brand	ActiveLife	2026-02-04 09:24:10
846	342	brand	SportFit	2026-02-04 09:24:10
847	343	brand	ActiveLife	2026-02-04 09:24:10
848	344	brand	ActiveLife	2026-02-04 09:24:10
849	345	brand	ActiveLife	2026-02-04 09:24:10
850	346	brand	SportFit	2026-02-04 09:24:10
851	347	brand	SportFit	2026-02-04 09:24:10
852	348	brand	ActiveLife	2026-02-04 09:24:10
853	349	brand	SportFit	2026-02-04 09:24:10
854	350	brand	SportFit	2026-02-04 09:24:10
855	351	brand	ActiveLife	2026-02-04 09:24:10
856	352	brand	SportFit	2026-02-04 09:24:10
857	353	brand	ActiveLife	2026-02-04 09:24:10
858	354	brand	SportFit	2026-02-04 09:24:10
859	355	brand	ActiveLife	2026-02-04 09:24:10
860	356	brand	ActiveLife	2026-02-04 09:24:10
861	357	brand	SportFit	2026-02-04 09:24:10
862	358	brand	SportFit	2026-02-04 09:24:10
863	359	brand	ActiveLife	2026-02-04 09:24:10
864	360	brand	SportFit	2026-02-04 09:24:10
865	361	brand	ActiveLife	2026-02-04 09:24:10
866	362	brand	SportFit	2026-02-04 09:24:10
867	363	brand	ActiveLife	2026-02-04 09:24:10
868	364	brand	ActiveLife	2026-02-04 09:24:10
869	365	brand	SportFit	2026-02-04 09:24:10
870	366	brand	SportFit	2026-02-04 09:24:10
871	367	brand	SportFit	2026-02-04 09:24:10
872	368	brand	SportFit	2026-02-04 09:24:10
873	369	brand	ActiveLife	2026-02-04 09:24:10
874	370	brand	SportFit	2026-02-04 09:24:10
875	371	brand	SportFit	2026-02-04 09:24:10
876	372	brand	ActiveLife	2026-02-04 09:24:10
877	373	brand	SportFit	2026-02-04 09:24:10
878	374	brand	SportFit	2026-02-04 09:24:10
879	375	brand	ActiveLife	2026-02-04 09:24:10
880	376	brand	ActiveLife	2026-02-04 09:24:10
881	377	brand	SportFit	2026-02-04 09:24:10
882	378	brand	SportFit	2026-02-04 09:24:10
883	379	brand	SportFit	2026-02-04 09:24:10
884	380	brand	SportFit	2026-02-04 09:24:10
885	381	brand	SportFit	2026-02-04 09:24:10
886	382	brand	ActiveLife	2026-02-04 09:24:10
887	383	brand	SportFit	2026-02-04 09:24:10
888	384	brand	ActiveLife	2026-02-04 09:24:10
889	385	brand	ActiveLife	2026-02-04 09:24:10
890	386	brand	ActiveLife	2026-02-04 09:24:10
891	387	brand	ActiveLife	2026-02-04 09:24:10
892	388	brand	SportFit	2026-02-04 09:24:10
893	389	brand	SportFit	2026-02-04 09:24:10
894	390	brand	SportFit	2026-02-04 09:24:10
895	391	brand	ActiveLife	2026-02-04 09:24:10
896	392	brand	SportFit	2026-02-04 09:24:10
897	393	brand	SportFit	2026-02-04 09:24:10
898	394	brand	ActiveLife	2026-02-04 09:24:10
899	395	brand	SportFit	2026-02-04 09:24:10
900	396	brand	SportFit	2026-02-04 09:24:10
901	397	brand	SportFit	2026-02-04 09:24:10
902	398	brand	SportFit	2026-02-04 09:24:10
903	399	brand	SportFit	2026-02-04 09:24:10
904	400	brand	SportFit	2026-02-04 09:24:10
905	401	brand	ReadMore	2026-02-04 09:24:10
906	402	brand	BookNest	2026-02-04 09:24:10
907	403	brand	ReadMore	2026-02-04 09:24:10
908	404	brand	ReadMore	2026-02-04 09:24:10
909	405	brand	BookNest	2026-02-04 09:24:10
910	406	brand	BookNest	2026-02-04 09:24:10
911	407	brand	BookNest	2026-02-04 09:24:10
912	408	brand	BookNest	2026-02-04 09:24:10
913	409	brand	ReadMore	2026-02-04 09:24:10
914	410	brand	BookNest	2026-02-04 09:24:10
915	411	brand	ReadMore	2026-02-04 09:24:10
916	412	brand	ReadMore	2026-02-04 09:24:10
917	413	brand	BookNest	2026-02-04 09:24:10
918	414	brand	BookNest	2026-02-04 09:24:10
919	415	brand	BookNest	2026-02-04 09:24:10
920	416	brand	ReadMore	2026-02-04 09:24:10
921	417	brand	ReadMore	2026-02-04 09:24:10
922	418	brand	ReadMore	2026-02-04 09:24:10
923	419	brand	BookNest	2026-02-04 09:24:10
924	420	brand	ReadMore	2026-02-04 09:24:10
925	421	brand	ReadMore	2026-02-04 09:24:10
926	422	brand	BookNest	2026-02-04 09:24:10
927	423	brand	ReadMore	2026-02-04 09:24:10
928	424	brand	ReadMore	2026-02-04 09:24:10
929	425	brand	ReadMore	2026-02-04 09:24:10
930	426	brand	ReadMore	2026-02-04 09:24:10
931	427	brand	ReadMore	2026-02-04 09:24:10
932	428	brand	BookNest	2026-02-04 09:24:10
933	429	brand	ReadMore	2026-02-04 09:24:10
934	430	brand	BookNest	2026-02-04 09:24:10
935	431	brand	ReadMore	2026-02-04 09:24:10
936	432	brand	ReadMore	2026-02-04 09:24:10
937	433	brand	BookNest	2026-02-04 09:24:10
938	434	brand	BookNest	2026-02-04 09:24:10
939	435	brand	ReadMore	2026-02-04 09:24:10
940	436	brand	BookNest	2026-02-04 09:24:10
941	437	brand	BookNest	2026-02-04 09:24:10
942	438	brand	ReadMore	2026-02-04 09:24:10
943	439	brand	ReadMore	2026-02-04 09:24:10
944	440	brand	BookNest	2026-02-04 09:24:10
945	441	brand	BookNest	2026-02-04 09:24:10
946	442	brand	BookNest	2026-02-04 09:24:10
947	443	brand	BookNest	2026-02-04 09:24:10
948	444	brand	BookNest	2026-02-04 09:24:10
949	445	brand	ReadMore	2026-02-04 09:24:10
950	446	brand	BookNest	2026-02-04 09:24:10
951	447	brand	ReadMore	2026-02-04 09:24:10
952	448	brand	BookNest	2026-02-04 09:24:10
953	449	brand	ReadMore	2026-02-04 09:24:10
954	450	brand	ReadMore	2026-02-04 09:24:10
955	451	brand	ReadMore	2026-02-04 09:24:10
956	452	brand	ReadMore	2026-02-04 09:24:10
957	453	brand	ReadMore	2026-02-04 09:24:10
958	454	brand	BookNest	2026-02-04 09:24:10
959	455	brand	BookNest	2026-02-04 09:24:10
960	456	brand	ReadMore	2026-02-04 09:24:10
961	457	brand	BookNest	2026-02-04 09:24:10
962	458	brand	ReadMore	2026-02-04 09:24:10
963	459	brand	ReadMore	2026-02-04 09:24:10
964	460	brand	BookNest	2026-02-04 09:24:10
965	461	brand	BookNest	2026-02-04 09:24:10
966	462	brand	ReadMore	2026-02-04 09:24:10
967	463	brand	ReadMore	2026-02-04 09:24:10
968	464	brand	ReadMore	2026-02-04 09:24:10
969	465	brand	BookNest	2026-02-04 09:24:10
970	466	brand	BookNest	2026-02-04 09:24:10
971	467	brand	ReadMore	2026-02-04 09:24:10
972	468	brand	ReadMore	2026-02-04 09:24:10
973	469	brand	BookNest	2026-02-04 09:24:10
974	470	brand	BookNest	2026-02-04 09:24:10
975	471	brand	BookNest	2026-02-04 09:24:10
976	472	brand	ReadMore	2026-02-04 09:24:10
977	473	brand	BookNest	2026-02-04 09:24:10
978	474	brand	BookNest	2026-02-04 09:24:10
979	475	brand	BookNest	2026-02-04 09:24:10
980	476	brand	BookNest	2026-02-04 09:24:10
981	477	brand	ReadMore	2026-02-04 09:24:10
982	478	brand	BookNest	2026-02-04 09:24:10
983	479	brand	BookNest	2026-02-04 09:24:10
984	480	brand	BookNest	2026-02-04 09:24:10
985	481	brand	ReadMore	2026-02-04 09:24:10
986	482	brand	ReadMore	2026-02-04 09:24:10
987	483	brand	BookNest	2026-02-04 09:24:10
988	484	brand	BookNest	2026-02-04 09:24:10
989	485	brand	BookNest	2026-02-04 09:24:10
990	486	brand	BookNest	2026-02-04 09:24:10
991	487	brand	ReadMore	2026-02-04 09:24:10
992	488	brand	ReadMore	2026-02-04 09:24:10
993	489	brand	ReadMore	2026-02-04 09:24:10
994	490	brand	BookNest	2026-02-04 09:24:10
995	491	brand	BookNest	2026-02-04 09:24:10
996	492	brand	BookNest	2026-02-04 09:24:10
997	493	brand	ReadMore	2026-02-04 09:24:10
998	494	brand	BookNest	2026-02-04 09:24:10
999	495	brand	BookNest	2026-02-04 09:24:10
1000	496	brand	BookNest	2026-02-04 09:24:10
1001	497	brand	BookNest	2026-02-04 09:24:10
1002	498	brand	ReadMore	2026-02-04 09:24:10
1003	499	brand	ReadMore	2026-02-04 09:24:10
1004	500	brand	BookNest	2026-02-04 09:24:10
\.


--
-- TOC entry 5478 (class 0 OID 25260)
-- Dependencies: 233
-- Data for Name: catalog_product_entity; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.catalog_product_entity (entity_id, sku, name, price, original_price, stock, description, is_featured, is_new, rating, reviews_count, created_at, updated_at, is_active) FROM stdin;
1	perfect-smartphone-version-1	Perfect Smartphone Version	247.00	290.59	84	High-quality smartphone with excellent features and performance	f	f	4.50	459	\N	\N	t
2	pro-tablet-version-2	Pro Tablet Version	144.00	180.00	40	High-quality tablet with excellent features and performance	f	f	4.10	359	\N	\N	t
3	expert-laptop-performance-3	Expert Laptop Performance	55.00	78.57	31	High-quality laptop with excellent features and performance	f	f	4.50	248	\N	\N	t
4	supreme-desktop-computer-performance-4	Supreme Desktop Computer Performance	168.00	186.67	90	High-quality desktop computer with excellent features and performance	f	f	3.50	320	\N	\N	t
5	master-monitor-max-5	Master Monitor Max	267.09	296.77	71	High-quality monitor with excellent features and performance	f	f	4.30	44	\N	\N	t
6	essential-keyboard-edition-6	Essential Keyboard Edition	107.97	107.97	76	High-quality keyboard with excellent features and performance	f	t	4.10	154	\N	\N	t
7	essential-mouse-plus-7	Essential Mouse Plus	39.02	48.78	6	High-quality mouse with excellent features and performance	f	t	3.90	87	\N	\N	t
8	classic-headphones-collection-8	Classic Headphones Collection	88.06	103.60	55	High-quality headphones with excellent features and performance	t	f	3.50	113	\N	\N	t
9	premium-earbuds-version-9	Premium Earbuds Version	30.11	30.11	95	High-quality earbuds with excellent features and performance	f	f	3.90	152	\N	\N	t
10	supreme-speaker-model-10	Supreme Speaker Model	144.71	144.71	7	High-quality speaker with excellent features and performance	f	f	4.60	263	\N	\N	t
11	essential-smartwatch-plus-11	Essential Smartwatch Plus	234.33	246.66	56	High-quality smartwatch with excellent features and performance	f	f	3.80	261	\N	\N	t
12	deluxe-fitness-tracker-edition-12	Deluxe Fitness Tracker Edition	150.94	150.94	84	High-quality fitness tracker with excellent features and performance	f	t	3.60	492	\N	\N	t
13	advanced-camera-grade-13	Advanced Camera Grade	257.39	257.39	85	High-quality camera with excellent features and performance	t	f	4.70	73	\N	\N	t
14	perfect-webcam-model-14	Perfect Webcam Model	56.87	75.82	38	High-quality webcam with excellent features and performance	f	t	4.50	480	\N	\N	t
15	classic-microphone-series-15	Classic Microphone Series	121.32	173.31	24	High-quality microphone with excellent features and performance	t	f	4.50	35	\N	\N	t
16	ultra-router-plus-16	Ultra Router Plus	78.41	82.54	31	High-quality router with excellent features and performance	f	f	4.00	367	\N	\N	t
17	luxury-power-bank-model-17	Luxury Power Bank Model	43.19	43.19	50	High-quality power bank with excellent features and performance	f	f	4.10	493	\N	\N	t
18	advanced-charger-version-18	Advanced Charger Version	39.93	46.98	69	High-quality charger with excellent features and performance	f	t	4.90	223	\N	\N	t
19	deluxe-usb-cable-model-19	Deluxe USB Cable Model	11.71	13.78	72	High-quality usb cable with excellent features and performance	f	t	4.60	254	\N	\N	t
20	expert-hard-drive-max-20	Expert Hard Drive Max	122.52	163.36	45	High-quality hard drive with excellent features and performance	f	t	3.50	172	\N	\N	t
21	pro-smartphone-series-21	Pro Smartphone Series	226.94	252.16	69	High-quality smartphone with excellent features and performance	f	t	4.30	78	\N	\N	t
22	ultra-tablet-series-22	Ultra Tablet Series	190.71	211.90	14	High-quality tablet with excellent features and performance	t	t	5.00	265	\N	\N	t
23	classic-laptop-series-23	Classic Laptop Series	137.00	137.00	17	High-quality laptop with excellent features and performance	f	f	3.50	360	\N	\N	t
24	master-desktop-computer-quality-24	Master Desktop Computer Quality	152.00	217.14	88	High-quality desktop computer with excellent features and performance	f	f	4.90	238	\N	\N	t
25	modern-monitor-edition-25	Modern Monitor Edition	257.00	270.53	15	High-quality monitor with excellent features and performance	f	f	4.30	112	\N	\N	t
26	supreme-keyboard-performance-26	Supreme Keyboard Performance	79.77	113.95	53	High-quality keyboard with excellent features and performance	f	f	3.70	339	\N	\N	t
27	deluxe-mouse-performance-27	Deluxe Mouse Performance	20.07	23.61	51	High-quality mouse with excellent features and performance	f	f	4.20	425	\N	\N	t
28	classic-headphones-edition-28	Classic Headphones Edition	86.94	86.94	71	High-quality headphones with excellent features and performance	f	f	4.60	130	\N	\N	t
29	supreme-earbuds-version-29	Supreme Earbuds Version	195.68	205.98	29	High-quality earbuds with excellent features and performance	f	f	3.90	397	\N	\N	t
30	essential-speaker-plus-30	Essential Speaker Plus	84.52	84.52	81	High-quality speaker with excellent features and performance	t	f	4.40	472	\N	\N	t
31	perfect-smartwatch-series-31	Perfect Smartwatch Series	98.70	116.12	11	High-quality smartwatch with excellent features and performance	f	f	4.00	352	\N	\N	t
32	professional-fitness-tracker-performance-32	Professional Fitness Tracker Performance	133.36	177.81	99	High-quality fitness tracker with excellent features and performance	f	f	4.40	27	\N	\N	t
33	perfect-camera-collection-33	Perfect Camera Collection	171.00	171.00	11	High-quality camera with excellent features and performance	t	f	5.00	368	\N	\N	t
34	supreme-webcam-series-34	Supreme Webcam Series	89.73	94.45	90	High-quality webcam with excellent features and performance	f	t	4.90	45	\N	\N	t
35	essential-microphone-quality-35	Essential Microphone Quality	107.36	107.36	22	High-quality microphone with excellent features and performance	f	f	3.70	257	\N	\N	t
36	supreme-router-series-36	Supreme Router Series	155.78	155.78	82	High-quality router with excellent features and performance	f	t	3.60	361	\N	\N	t
37	classic-power-bank-performance-37	Classic Power Bank Performance	53.28	59.20	85	High-quality power bank with excellent features and performance	f	f	4.60	422	\N	\N	t
38	modern-charger-plus-38	Modern Charger Plus	22.19	29.59	77	High-quality charger with excellent features and performance	f	f	4.80	321	\N	\N	t
39	modern-usb-cable-series-39	Modern USB Cable Series	27.67	27.67	22	High-quality usb cable with excellent features and performance	f	t	4.50	144	\N	\N	t
40	deluxe-hard-drive-series-40	Deluxe Hard Drive Series	138.96	173.70	26	High-quality hard drive with excellent features and performance	f	f	5.00	327	\N	\N	t
41	perfect-smartphone-model-41	Perfect Smartphone Model	239.00	265.56	59	High-quality smartphone with excellent features and performance	f	t	4.70	356	\N	\N	t
42	expert-tablet-series-42	Expert Tablet Series	243.42	256.23	42	High-quality tablet with excellent features and performance	f	f	4.80	52	\N	\N	t
43	deluxe-laptop-grade-43	Deluxe Laptop Grade	125.00	125.00	88	High-quality laptop with excellent features and performance	f	f	4.30	154	\N	\N	t
44	advanced-desktop-computer-plus-44	Advanced Desktop Computer Plus	80.00	114.29	57	High-quality desktop computer with excellent features and performance	f	f	4.20	480	\N	\N	t
45	luxury-monitor-version-45	Luxury Monitor Version	143.00	204.29	80	High-quality monitor with excellent features and performance	t	f	4.10	54	\N	\N	t
46	professional-keyboard-series-46	Professional Keyboard Series	73.79	81.99	20	High-quality keyboard with excellent features and performance	f	t	4.50	273	\N	\N	t
47	premium-mouse-plus-47	Premium Mouse Plus	26.99	38.56	43	High-quality mouse with excellent features and performance	f	f	4.30	102	\N	\N	t
48	modern-headphones-series-48	Modern Headphones Series	45.06	56.33	53	High-quality headphones with excellent features and performance	f	t	3.60	331	\N	\N	t
49	professional-earbuds-quality-49	Professional Earbuds Quality	175.60	234.13	57	High-quality earbuds with excellent features and performance	f	f	4.40	285	\N	\N	t
50	ultra-speaker-performance-50	Ultra Speaker Performance	142.69	142.69	20	High-quality speaker with excellent features and performance	f	f	5.00	364	\N	\N	t
51	supreme-smartwatch-grade-51	Supreme Smartwatch Grade	179.00	179.00	37	High-quality smartwatch with excellent features and performance	f	f	4.30	55	\N	\N	t
52	supreme-fitness-tracker-model-52	Supreme Fitness Tracker Model	56.23	56.23	69	High-quality fitness tracker with excellent features and performance	f	f	4.70	170	\N	\N	t
53	premium-camera-edition-53	Premium Camera Edition	235.00	276.47	5	High-quality camera with excellent features and performance	f	f	4.10	202	\N	\N	t
54	perfect-webcam-performance-54	Perfect Webcam Performance	149.14	149.14	77	High-quality webcam with excellent features and performance	f	f	4.30	403	\N	\N	t
55	premium-microphone-version-55	Premium Microphone Version	180.28	180.28	70	High-quality microphone with excellent features and performance	f	t	3.50	441	\N	\N	t
56	expert-router-series-56	Expert Router Series	79.01	83.17	63	High-quality router with excellent features and performance	f	f	4.20	274	\N	\N	t
57	pro-power-bank-plus-57	Pro Power Bank Plus	45.32	47.71	20	High-quality power bank with excellent features and performance	f	f	4.50	138	\N	\N	t
58	elite-charger-model-58	Elite Charger Model	57.63	57.63	22	High-quality charger with excellent features and performance	f	t	4.70	135	\N	\N	t
59	essential-usb-cable-model-59	Essential USB Cable Model	28.06	29.54	81	High-quality usb cable with excellent features and performance	t	t	4.60	313	\N	\N	t
60	perfect-hard-drive-max-60	Perfect Hard Drive Max	136.16	143.33	92	High-quality hard drive with excellent features and performance	f	f	4.10	440	\N	\N	t
61	professional-smartphone-edition-61	Professional Smartphone Edition	78.00	78.00	66	High-quality smartphone with excellent features and performance	f	f	4.80	112	\N	\N	t
62	premium-tablet-series-62	Premium Tablet Series	168.60	198.35	6	High-quality tablet with excellent features and performance	f	f	4.60	359	\N	\N	t
63	ultra-laptop-grade-63	Ultra Laptop Grade	92.00	115.00	94	High-quality laptop with excellent features and performance	f	f	4.00	368	\N	\N	t
64	ultra-desktop-computer-performance-64	Ultra Desktop Computer Performance	186.00	195.79	91	High-quality desktop computer with excellent features and performance	t	f	4.80	332	\N	\N	t
65	premium-monitor-model-65	Premium Monitor Model	105.00	140.00	58	High-quality monitor with excellent features and performance	f	t	3.60	220	\N	\N	t
66	modern-keyboard-max-66	Modern Keyboard Max	77.99	86.66	56	High-quality keyboard with excellent features and performance	f	f	4.00	36	\N	\N	t
67	master-mouse-series-67	Master Mouse Series	19.62	24.53	4	High-quality mouse with excellent features and performance	f	f	4.60	18	\N	\N	t
68	supreme-headphones-collection-68	Supreme Headphones Collection	89.05	111.31	46	High-quality headphones with excellent features and performance	f	t	4.10	257	\N	\N	t
69	advanced-earbuds-edition-69	Advanced Earbuds Edition	215.57	239.52	72	High-quality earbuds with excellent features and performance	t	t	3.50	374	\N	\N	t
70	premium-speaker-series-70	Premium Speaker Series	52.29	65.36	58	High-quality speaker with excellent features and performance	f	f	4.30	429	\N	\N	t
71	deluxe-smartwatch-collection-71	Deluxe Smartwatch Collection	173.17	173.17	68	High-quality smartwatch with excellent features and performance	t	t	3.60	442	\N	\N	t
72	deluxe-fitness-tracker-version-72	Deluxe Fitness Tracker Version	139.57	174.46	63	High-quality fitness tracker with excellent features and performance	f	f	3.70	75	\N	\N	t
73	classic-camera-performance-73	Classic Camera Performance	217.14	271.43	49	High-quality camera with excellent features and performance	f	t	4.90	476	\N	\N	t
74	essential-webcam-plus-74	Essential Webcam Plus	85.64	122.34	13	High-quality webcam with excellent features and performance	t	f	3.60	10	\N	\N	t
75	perfect-microphone-plus-75	Perfect Microphone Plus	65.25	65.25	76	High-quality microphone with excellent features and performance	f	f	4.20	438	\N	\N	t
76	supreme-router-performance-76	Supreme Router Performance	81.46	101.82	5	High-quality router with excellent features and performance	f	f	3.60	300	\N	\N	t
77	perfect-power-bank-model-77	Perfect Power Bank Model	66.13	66.13	49	High-quality power bank with excellent features and performance	f	f	4.90	208	\N	\N	t
78	pro-charger-series-78	Pro Charger Series	55.54	55.54	77	High-quality charger with excellent features and performance	f	f	3.60	188	\N	\N	t
79	pro-usb-cable-collection-79	Pro USB Cable Collection	14.52	15.28	49	High-quality usb cable with excellent features and performance	f	f	5.00	498	\N	\N	t
80	master-hard-drive-collection-80	Master Hard Drive Collection	150.55	150.55	64	High-quality hard drive with excellent features and performance	f	f	3.90	404	\N	\N	t
81	premium-smartphone-plus-81	Premium Smartphone Plus	295.00	347.06	39	High-quality smartphone with excellent features and performance	f	f	3.50	382	\N	\N	t
82	expert-tablet-collection-82	Expert Tablet Collection	230.42	288.03	36	High-quality tablet with excellent features and performance	f	t	3.70	274	\N	\N	t
83	professional-laptop-series-83	Professional Laptop Series	124.00	137.78	33	High-quality laptop with excellent features and performance	f	f	3.50	381	\N	\N	t
84	deluxe-desktop-computer-quality-84	Deluxe Desktop Computer Quality	81.00	115.71	85	High-quality desktop computer with excellent features and performance	f	f	4.10	270	\N	\N	t
85	master-monitor-plus-85	Master Monitor Plus	271.00	271.00	26	High-quality monitor with excellent features and performance	f	f	3.50	263	\N	\N	t
86	deluxe-keyboard-model-86	Deluxe Keyboard Model	21.74	31.06	78	High-quality keyboard with excellent features and performance	t	f	4.20	76	\N	\N	t
87	professional-mouse-performance-87	Professional Mouse Performance	21.29	30.41	20	High-quality mouse with excellent features and performance	f	f	3.80	398	\N	\N	t
88	essential-headphones-collection-88	Essential Headphones Collection	113.20	150.93	6	High-quality headphones with excellent features and performance	f	f	4.40	94	\N	\N	t
89	advanced-earbuds-edition-89	Advanced Earbuds Edition	108.85	155.50	9	High-quality earbuds with excellent features and performance	t	f	3.60	97	\N	\N	t
90	modern-speaker-collection-90	Modern Speaker Collection	216.88	255.15	36	High-quality speaker with excellent features and performance	t	f	4.30	190	\N	\N	t
91	pro-smartwatch-performance-91	Pro Smartwatch Performance	220.01	314.30	65	High-quality smartwatch with excellent features and performance	f	f	3.60	55	\N	\N	t
92	luxury-fitness-tracker-collection-92	Luxury Fitness Tracker Collection	68.94	91.92	62	High-quality fitness tracker with excellent features and performance	t	f	3.50	397	\N	\N	t
93	ultra-camera-performance-93	Ultra Camera Performance	296.00	422.86	17	High-quality camera with excellent features and performance	f	f	5.00	304	\N	\N	t
94	elite-webcam-plus-94	Elite Webcam Plus	86.50	86.50	25	High-quality webcam with excellent features and performance	f	f	3.80	154	\N	\N	t
95	luxury-microphone-max-95	Luxury Microphone Max	240.48	282.92	89	High-quality microphone with excellent features and performance	f	f	4.50	122	\N	\N	t
96	ultra-router-model-96	Ultra Router Model	131.85	188.35	5	High-quality router with excellent features and performance	f	f	4.00	452	\N	\N	t
97	classic-power-bank-series-97	Classic Power Bank Series	32.69	32.69	84	High-quality power bank with excellent features and performance	f	f	4.70	116	\N	\N	t
98	premium-charger-series-98	Premium Charger Series	21.54	28.72	84	High-quality charger with excellent features and performance	f	t	3.50	283	\N	\N	t
99	pro-usb-cable-plus-99	Pro USB Cable Plus	14.19	18.92	78	High-quality usb cable with excellent features and performance	f	f	4.70	65	\N	\N	t
100	essential-hard-drive-version-100	Essential Hard Drive Version	162.69	232.41	53	High-quality hard drive with excellent features and performance	f	f	4.70	141	\N	\N	t
101	supreme-t-shirt-quality-101	Supreme T-Shirt Quality	47.80	47.80	80	High-quality t-shirt with excellent features and performance	t	t	4.60	385	\N	\N	t
102	deluxe-shirt-edition-102	Deluxe Shirt Edition	27.83	37.10	11	High-quality shirt with excellent features and performance	t	f	4.10	163	\N	\N	t
103	supreme-jeans-max-103	Supreme Jeans Max	53.18	62.57	31	High-quality jeans with excellent features and performance	f	t	3.70	68	\N	\N	t
104	supreme-pants-version-104	Supreme Pants Version	63.85	63.85	34	High-quality pants with excellent features and performance	f	f	4.30	332	\N	\N	t
105	elite-shorts-max-105	Elite Shorts Max	32.66	32.66	2	High-quality shorts with excellent features and performance	t	f	4.80	420	\N	\N	t
106	advanced-dress-edition-106	Advanced Dress Edition	49.90	58.70	53	High-quality dress with excellent features and performance	f	f	4.90	65	\N	\N	t
107	master-skirt-grade-107	Master Skirt Grade	39.53	46.50	95	High-quality skirt with excellent features and performance	t	f	5.00	463	\N	\N	t
108	elite-jacket-model-108	Elite Jacket Model	123.21	176.01	34	High-quality jacket with excellent features and performance	f	t	3.90	404	\N	\N	t
109	ultra-coat-grade-109	Ultra Coat Grade	272.79	272.79	33	High-quality coat with excellent features and performance	f	f	4.30	365	\N	\N	t
110	perfect-sweater-grade-110	Perfect Sweater Grade	58.18	58.18	43	High-quality sweater with excellent features and performance	f	f	4.20	47	\N	\N	t
111	deluxe-hoodie-max-111	Deluxe Hoodie Max	42.70	44.95	32	High-quality hoodie with excellent features and performance	f	f	4.40	99	\N	\N	t
112	essential-sneakers-performance-112	Essential Sneakers Performance	90.68	100.75	41	High-quality sneakers with excellent features and performance	f	t	4.20	475	\N	\N	t
113	luxury-boots-series-113	Luxury Boots Series	74.84	106.91	87	High-quality boots with excellent features and performance	f	t	4.60	248	\N	\N	t
114	essential-sandals-grade-114	Essential Sandals Grade	36.32	51.88	59	High-quality sandals with excellent features and performance	f	f	4.00	37	\N	\N	t
115	modern-heels-version-115	Modern Heels Version	63.31	66.64	82	High-quality heels with excellent features and performance	f	t	4.50	32	\N	\N	t
116	classic-watch-series-116	Classic Watch Series	68.26	80.30	4	High-quality watch with excellent features and performance	f	f	5.00	315	\N	\N	t
117	classic-sunglasses-max-117	Classic Sunglasses Max	35.22	37.07	25	High-quality sunglasses with excellent features and performance	f	f	3.60	423	\N	\N	t
118	master-hat-max-118	Master Hat Max	48.43	48.43	3	High-quality hat with excellent features and performance	f	f	4.00	426	\N	\N	t
119	elite-scarf-plus-119	Elite Scarf Plus	19.46	25.94	23	High-quality scarf with excellent features and performance	t	f	3.90	491	\N	\N	t
120	professional-handbag-max-120	Professional Handbag Max	66.91	74.34	92	High-quality handbag with excellent features and performance	t	t	5.00	43	\N	\N	t
121	elite-t-shirt-series-121	Elite T-Shirt Series	24.23	28.51	90	High-quality t-shirt with excellent features and performance	f	f	4.70	482	\N	\N	t
122	modern-shirt-series-122	Modern Shirt Series	53.39	59.32	19	High-quality shirt with excellent features and performance	f	f	4.20	316	\N	\N	t
123	essential-jeans-collection-123	Essential Jeans Collection	118.23	118.23	13	High-quality jeans with excellent features and performance	f	f	4.40	145	\N	\N	t
124	premium-pants-quality-124	Premium Pants Quality	76.65	76.65	62	High-quality pants with excellent features and performance	f	t	5.00	215	\N	\N	t
125	essential-shorts-version-125	Essential Shorts Version	40.39	40.39	10	High-quality shorts with excellent features and performance	f	f	3.80	430	\N	\N	t
126	professional-dress-series-126	Professional Dress Series	136.92	136.92	98	High-quality dress with excellent features and performance	f	f	3.60	150	\N	\N	t
127	professional-skirt-series-127	Professional Skirt Series	64.96	64.96	49	High-quality skirt with excellent features and performance	t	t	5.00	429	\N	\N	t
128	luxury-jacket-max-128	Luxury Jacket Max	77.45	86.05	88	High-quality jacket with excellent features and performance	f	f	4.40	160	\N	\N	t
129	ultra-coat-grade-129	Ultra Coat Grade	103.12	103.12	28	High-quality coat with excellent features and performance	f	f	4.80	278	\N	\N	t
130	master-sweater-performance-130	Master Sweater Performance	68.66	76.29	38	High-quality sweater with excellent features and performance	f	f	3.50	269	\N	\N	t
131	ultra-hoodie-performance-131	Ultra Hoodie Performance	45.34	60.45	84	High-quality hoodie with excellent features and performance	f	t	4.00	190	\N	\N	t
132	ultra-sneakers-quality-132	Ultra Sneakers Quality	80.79	107.72	21	High-quality sneakers with excellent features and performance	f	t	4.60	476	\N	\N	t
133	deluxe-boots-series-133	Deluxe Boots Series	141.42	141.42	77	High-quality boots with excellent features and performance	f	f	3.60	160	\N	\N	t
134	modern-sandals-performance-134	Modern Sandals Performance	37.29	37.29	78	High-quality sandals with excellent features and performance	t	f	3.90	124	\N	\N	t
135	master-heels-model-135	Master Heels Model	151.75	151.75	11	High-quality heels with excellent features and performance	f	f	4.10	486	\N	\N	t
136	master-watch-edition-136	Master Watch Edition	78.48	104.64	98	High-quality watch with excellent features and performance	f	f	4.80	191	\N	\N	t
137	ultra-sunglasses-model-137	Ultra Sunglasses Model	29.23	38.97	85	High-quality sunglasses with excellent features and performance	f	f	4.00	288	\N	\N	t
138	advanced-hat-grade-138	Advanced Hat Grade	50.92	50.92	3	High-quality hat with excellent features and performance	f	t	4.00	39	\N	\N	t
139	elite-scarf-quality-139	Elite Scarf Quality	15.84	19.80	27	High-quality scarf with excellent features and performance	t	f	3.90	407	\N	\N	t
140	luxury-handbag-edition-140	Luxury Handbag Edition	151.43	216.33	24	High-quality handbag with excellent features and performance	f	f	4.00	433	\N	\N	t
141	luxury-t-shirt-edition-141	Luxury T-Shirt Edition	29.94	29.94	37	High-quality t-shirt with excellent features and performance	f	t	4.30	426	\N	\N	t
142	essential-shirt-max-142	Essential Shirt Max	47.10	67.29	81	High-quality shirt with excellent features and performance	f	f	4.00	151	\N	\N	t
143	pro-jeans-model-143	Pro Jeans Model	34.80	46.40	61	High-quality jeans with excellent features and performance	t	f	4.30	183	\N	\N	t
144	professional-pants-max-144	Professional Pants Max	70.29	70.29	88	High-quality pants with excellent features and performance	f	f	3.80	200	\N	\N	t
145	classic-shorts-collection-145	Classic Shorts Collection	22.94	32.77	100	High-quality shorts with excellent features and performance	t	t	4.40	445	\N	\N	t
146	perfect-dress-performance-146	Perfect Dress Performance	121.08	142.45	10	High-quality dress with excellent features and performance	f	f	3.60	58	\N	\N	t
147	advanced-skirt-version-147	Advanced Skirt Version	34.60	34.60	50	High-quality skirt with excellent features and performance	f	f	4.50	240	\N	\N	t
148	pro-jacket-series-148	Pro Jacket Series	106.28	118.09	88	High-quality jacket with excellent features and performance	f	t	4.50	41	\N	\N	t
149	supreme-coat-quality-149	Supreme Coat Quality	81.48	116.40	49	High-quality coat with excellent features and performance	f	t	4.50	34	\N	\N	t
150	modern-sweater-version-150	Modern Sweater Version	34.89	49.84	24	High-quality sweater with excellent features and performance	f	f	3.50	234	\N	\N	t
151	pro-hoodie-series-151	Pro Hoodie Series	61.84	88.34	3	High-quality hoodie with excellent features and performance	f	t	4.70	386	\N	\N	t
152	supreme-sneakers-series-152	Supreme Sneakers Series	69.37	77.08	52	High-quality sneakers with excellent features and performance	f	f	3.90	46	\N	\N	t
153	premium-boots-collection-153	Premium Boots Collection	119.79	149.74	54	High-quality boots with excellent features and performance	t	f	4.00	274	\N	\N	t
154	deluxe-sandals-plus-154	Deluxe Sandals Plus	65.87	65.87	1	High-quality sandals with excellent features and performance	f	f	3.50	31	\N	\N	t
155	elite-heels-collection-155	Elite Heels Collection	106.34	132.93	1	High-quality heels with excellent features and performance	f	f	4.20	437	\N	\N	t
156	luxury-watch-quality-156	Luxury Watch Quality	208.20	219.16	79	High-quality watch with excellent features and performance	f	t	4.70	74	\N	\N	t
157	perfect-sunglasses-collection-157	Perfect Sunglasses Collection	108.44	127.58	13	High-quality sunglasses with excellent features and performance	f	f	4.70	180	\N	\N	t
158	premium-hat-collection-158	Premium Hat Collection	42.61	42.61	92	High-quality hat with excellent features and performance	t	f	3.70	308	\N	\N	t
159	elite-scarf-series-159	Elite Scarf Series	23.17	28.96	47	High-quality scarf with excellent features and performance	f	t	3.80	219	\N	\N	t
160	professional-handbag-max-160	Professional Handbag Max	48.87	65.16	31	High-quality handbag with excellent features and performance	t	f	4.30	326	\N	\N	t
161	modern-t-shirt-performance-161	Modern T-Shirt Performance	34.81	40.95	43	High-quality t-shirt with excellent features and performance	f	t	3.50	479	\N	\N	t
162	deluxe-shirt-series-162	Deluxe Shirt Series	37.87	37.87	71	High-quality shirt with excellent features and performance	f	f	3.50	361	\N	\N	t
163	classic-jeans-performance-163	Classic Jeans Performance	66.90	78.70	56	High-quality jeans with excellent features and performance	t	f	5.00	24	\N	\N	t
164	classic-pants-plus-164	Classic Pants Plus	48.83	61.04	65	High-quality pants with excellent features and performance	f	f	3.60	488	\N	\N	t
165	professional-shorts-plus-165	Professional Shorts Plus	17.60	20.70	21	High-quality shorts with excellent features and performance	f	f	5.00	305	\N	\N	t
166	professional-dress-collection-166	Professional Dress Collection	109.95	109.95	22	High-quality dress with excellent features and performance	f	f	3.90	313	\N	\N	t
167	modern-skirt-grade-167	Modern Skirt Grade	35.25	44.06	45	High-quality skirt with excellent features and performance	f	f	4.20	313	\N	\N	t
168	deluxe-jacket-edition-168	Deluxe Jacket Edition	193.35	193.35	93	High-quality jacket with excellent features and performance	f	f	4.30	488	\N	\N	t
169	perfect-coat-edition-169	Perfect Coat Edition	92.14	92.14	74	High-quality coat with excellent features and performance	t	f	3.80	275	\N	\N	t
170	elite-sweater-edition-170	Elite Sweater Edition	36.57	40.63	88	High-quality sweater with excellent features and performance	f	f	3.50	380	\N	\N	t
171	essential-hoodie-model-171	Essential Hoodie Model	57.82	57.82	52	High-quality hoodie with excellent features and performance	f	f	3.80	172	\N	\N	t
172	advanced-sneakers-plus-172	Advanced Sneakers Plus	104.59	104.59	88	High-quality sneakers with excellent features and performance	t	f	4.90	403	\N	\N	t
173	master-boots-edition-173	Master Boots Edition	114.14	142.67	79	High-quality boots with excellent features and performance	f	t	4.50	322	\N	\N	t
174	professional-sandals-version-174	Professional Sandals Version	27.48	27.48	32	High-quality sandals with excellent features and performance	f	f	4.10	469	\N	\N	t
175	luxury-heels-max-175	Luxury Heels Max	89.07	98.97	55	High-quality heels with excellent features and performance	f	f	4.80	423	\N	\N	t
176	professional-watch-series-176	Professional Watch Series	95.70	106.33	5	High-quality watch with excellent features and performance	f	f	4.10	159	\N	\N	t
177	supreme-sunglasses-plus-177	Supreme Sunglasses Plus	120.67	120.67	13	High-quality sunglasses with excellent features and performance	t	f	4.50	451	\N	\N	t
178	supreme-hat-collection-178	Supreme Hat Collection	37.09	49.45	69	High-quality hat with excellent features and performance	f	t	4.50	149	\N	\N	t
179	expert-scarf-plus-179	Expert Scarf Plus	21.31	30.44	2	High-quality scarf with excellent features and performance	f	f	4.30	90	\N	\N	t
180	master-handbag-collection-180	Master Handbag Collection	63.09	90.13	33	High-quality handbag with excellent features and performance	t	t	4.70	251	\N	\N	t
181	expert-t-shirt-model-181	Expert T-Shirt Model	14.42	20.60	67	High-quality t-shirt with excellent features and performance	f	f	3.60	136	\N	\N	t
182	pro-shirt-grade-182	Pro Shirt Grade	45.88	53.98	32	High-quality shirt with excellent features and performance	f	t	4.90	205	\N	\N	t
183	pro-jeans-model-183	Pro Jeans Model	97.31	108.12	58	High-quality jeans with excellent features and performance	f	f	4.70	202	\N	\N	t
184	luxury-pants-max-184	Luxury Pants Max	87.59	97.32	30	High-quality pants with excellent features and performance	f	f	4.80	37	\N	\N	t
185	deluxe-shorts-series-185	Deluxe Shorts Series	31.53	31.53	91	High-quality shorts with excellent features and performance	f	f	3.90	367	\N	\N	t
186	elite-dress-performance-186	Elite Dress Performance	117.32	117.32	33	High-quality dress with excellent features and performance	f	t	4.20	225	\N	\N	t
187	essential-skirt-edition-187	Essential Skirt Edition	54.76	60.84	70	High-quality skirt with excellent features and performance	f	f	4.60	131	\N	\N	t
188	deluxe-jacket-model-188	Deluxe Jacket Model	105.80	141.06	98	High-quality jacket with excellent features and performance	f	f	3.60	111	\N	\N	t
189	expert-coat-edition-189	Expert Coat Edition	108.84	108.84	68	High-quality coat with excellent features and performance	t	f	4.30	338	\N	\N	t
190	deluxe-sweater-quality-190	Deluxe Sweater Quality	77.92	86.58	30	High-quality sweater with excellent features and performance	f	f	4.10	96	\N	\N	t
191	luxury-hoodie-plus-191	Luxury Hoodie Plus	34.48	45.97	24	High-quality hoodie with excellent features and performance	f	t	3.60	51	\N	\N	t
192	professional-sneakers-max-192	Professional Sneakers Max	78.35	97.94	92	High-quality sneakers with excellent features and performance	f	t	5.00	343	\N	\N	t
193	deluxe-boots-plus-193	Deluxe Boots Plus	126.54	168.72	71	High-quality boots with excellent features and performance	f	f	4.30	177	\N	\N	t
194	master-sandals-grade-194	Master Sandals Grade	29.96	29.96	55	High-quality sandals with excellent features and performance	f	f	4.40	473	\N	\N	t
195	expert-heels-plus-195	Expert Heels Plus	168.14	176.99	64	High-quality heels with excellent features and performance	f	f	4.10	373	\N	\N	t
196	deluxe-watch-collection-196	Deluxe Watch Collection	181.83	259.75	89	High-quality watch with excellent features and performance	f	f	3.60	124	\N	\N	t
197	ultra-sunglasses-performance-197	Ultra Sunglasses Performance	30.06	31.64	43	High-quality sunglasses with excellent features and performance	t	f	4.20	286	\N	\N	t
198	essential-hat-series-198	Essential Hat Series	20.55	24.18	6	High-quality hat with excellent features and performance	f	f	3.80	233	\N	\N	t
199	master-scarf-quality-199	Master Scarf Quality	42.57	44.81	79	High-quality scarf with excellent features and performance	t	t	4.50	171	\N	\N	t
200	master-handbag-series-200	Master Handbag Series	109.06	155.80	7	High-quality handbag with excellent features and performance	f	f	3.50	402	\N	\N	t
201	supreme-sofa-collection-201	Supreme Sofa Collection	252.00	252.00	72	High-quality sofa with excellent features and performance	f	f	3.80	362	\N	\N	t
202	deluxe-chair-series-202	Deluxe Chair Series	199.18	234.33	73	High-quality chair with excellent features and performance	f	f	4.70	335	\N	\N	t
203	advanced-table-plus-203	Advanced Table Plus	278.00	292.63	37	High-quality table with excellent features and performance	f	f	4.80	446	\N	\N	t
204	professional-bed-performance-204	Professional Bed Performance	92.00	131.43	9	High-quality bed with excellent features and performance	t	t	4.60	91	\N	\N	t
205	master-mattress-quality-205	Master Mattress Quality	258.75	258.75	28	High-quality mattress with excellent features and performance	f	f	3.70	178	\N	\N	t
206	essential-pillow-collection-206	Essential Pillow Collection	63.09	70.10	12	High-quality pillow with excellent features and performance	f	f	4.30	453	\N	\N	t
207	perfect-blanket-edition-207	Perfect Blanket Edition	42.72	61.03	96	High-quality blanket with excellent features and performance	t	f	4.10	173	\N	\N	t
208	perfect-curtains-series-208	Perfect Curtains Series	84.48	105.60	80	High-quality curtains with excellent features and performance	f	f	3.70	227	\N	\N	t
209	advanced-rug-model-209	Advanced Rug Model	90.81	113.51	76	High-quality rug with excellent features and performance	t	f	4.40	358	\N	\N	t
210	professional-lamp-model-210	Professional Lamp Model	93.71	124.94	87	High-quality lamp with excellent features and performance	f	f	4.10	154	\N	\N	t
211	luxury-mirror-performance-211	Luxury Mirror Performance	133.02	177.36	84	High-quality mirror with excellent features and performance	f	f	4.30	396	\N	\N	t
212	premium-clock-model-212	Premium Clock Model	56.41	70.51	88	High-quality clock with excellent features and performance	f	f	3.70	28	\N	\N	t
213	pro-vase-collection-213	Pro Vase Collection	33.26	35.01	58	High-quality vase with excellent features and performance	t	f	4.00	305	\N	\N	t
214	elite-picture-frame-collection-214	Elite Picture Frame Collection	29.78	39.71	25	High-quality picture frame with excellent features and performance	t	t	4.20	292	\N	\N	t
215	premium-bookshelf-edition-215	Premium Bookshelf Edition	282.00	282.00	12	High-quality bookshelf with excellent features and performance	f	f	4.80	69	\N	\N	t
216	elite-cabinet-model-216	Elite Cabinet Model	82.00	82.00	84	High-quality cabinet with excellent features and performance	f	f	4.20	281	\N	\N	t
217	pro-coffee-maker-max-217	Pro Coffee Maker Max	188.44	235.55	47	High-quality coffee maker with excellent features and performance	t	f	3.50	299	\N	\N	t
218	luxury-blender-grade-218	Luxury Blender Grade	104.45	149.22	34	High-quality blender with excellent features and performance	f	f	3.90	158	\N	\N	t
219	elite-toaster-collection-219	Elite Toaster Collection	53.50	53.50	28	High-quality toaster with excellent features and performance	f	f	3.90	78	\N	\N	t
220	supreme-microwave-version-220	Supreme Microwave Version	234.47	293.09	40	High-quality microwave with excellent features and performance	f	f	4.60	300	\N	\N	t
221	expert-sofa-model-221	Expert Sofa Model	186.00	248.00	58	High-quality sofa with excellent features and performance	f	f	3.70	469	\N	\N	t
222	master-chair-series-222	Master Chair Series	235.89	294.86	72	High-quality chair with excellent features and performance	f	t	3.80	133	\N	\N	t
223	master-table-collection-223	Master Table Collection	183.00	183.00	22	High-quality table with excellent features and performance	f	t	3.90	195	\N	\N	t
224	modern-bed-edition-224	Modern Bed Edition	274.00	391.43	61	High-quality bed with excellent features and performance	f	f	4.00	492	\N	\N	t
225	essential-mattress-grade-225	Essential Mattress Grade	73.00	91.25	14	High-quality mattress with excellent features and performance	f	f	3.80	209	\N	\N	t
226	master-pillow-version-226	Master Pillow Version	74.04	74.04	19	High-quality pillow with excellent features and performance	f	f	3.60	317	\N	\N	t
227	perfect-blanket-model-227	Perfect Blanket Model	81.48	81.48	13	High-quality blanket with excellent features and performance	t	t	4.40	246	\N	\N	t
228	perfect-curtains-plus-228	Perfect Curtains Plus	45.65	50.72	53	High-quality curtains with excellent features and performance	t	f	3.70	270	\N	\N	t
229	luxury-rug-max-229	Luxury Rug Max	114.53	127.25	74	High-quality rug with excellent features and performance	f	f	4.80	310	\N	\N	t
230	luxury-lamp-performance-230	Luxury Lamp Performance	107.66	119.62	13	High-quality lamp with excellent features and performance	f	f	3.50	186	\N	\N	t
231	perfect-mirror-plus-231	Perfect Mirror Plus	70.33	78.14	21	High-quality mirror with excellent features and performance	f	f	4.40	154	\N	\N	t
232	modern-clock-performance-232	Modern Clock Performance	62.05	62.05	1	High-quality clock with excellent features and performance	f	t	4.30	413	\N	\N	t
233	classic-vase-collection-233	Classic Vase Collection	30.63	38.29	27	High-quality vase with excellent features and performance	f	t	3.50	444	\N	\N	t
234	perfect-picture-frame-plus-234	Perfect Picture Frame Plus	46.95	55.23	14	High-quality picture frame with excellent features and performance	f	t	4.10	396	\N	\N	t
235	modern-bookshelf-quality-235	Modern Bookshelf Quality	183.27	183.27	68	High-quality bookshelf with excellent features and performance	f	f	4.40	120	\N	\N	t
236	modern-cabinet-edition-236	Modern Cabinet Edition	256.00	320.00	1	High-quality cabinet with excellent features and performance	f	f	3.90	325	\N	\N	t
237	modern-coffee-maker-performance-237	Modern Coffee Maker Performance	98.90	141.29	25	High-quality coffee maker with excellent features and performance	f	f	3.60	383	\N	\N	t
238	essential-blender-edition-238	Essential Blender Edition	78.31	78.31	50	High-quality blender with excellent features and performance	f	f	4.80	62	\N	\N	t
239	premium-toaster-performance-239	Premium Toaster Performance	67.36	70.90	38	High-quality toaster with excellent features and performance	t	t	4.70	40	\N	\N	t
240	ultra-microwave-performance-240	Ultra Microwave Performance	244.06	287.13	97	High-quality microwave with excellent features and performance	f	f	4.90	95	\N	\N	t
241	professional-sofa-series-241	Professional Sofa Series	116.00	136.47	39	High-quality sofa with excellent features and performance	f	f	4.50	335	\N	\N	t
242	classic-chair-max-242	Classic Chair Max	78.78	98.47	69	High-quality chair with excellent features and performance	f	f	5.00	444	\N	\N	t
243	perfect-table-max-243	Perfect Table Max	71.00	83.53	80	High-quality table with excellent features and performance	f	f	4.80	420	\N	\N	t
244	deluxe-bed-grade-244	Deluxe Bed Grade	102.00	127.50	93	High-quality bed with excellent features and performance	f	f	4.10	338	\N	\N	t
245	premium-mattress-performance-245	Premium Mattress Performance	258.93	258.93	13	High-quality mattress with excellent features and performance	f	f	3.50	356	\N	\N	t
246	premium-pillow-series-246	Premium Pillow Series	37.56	46.95	21	High-quality pillow with excellent features and performance	f	t	4.90	360	\N	\N	t
247	master-blanket-plus-247	Master Blanket Plus	62.40	62.40	60	High-quality blanket with excellent features and performance	f	t	4.10	441	\N	\N	t
248	perfect-curtains-series-248	Perfect Curtains Series	96.61	96.61	87	High-quality curtains with excellent features and performance	f	t	3.70	498	\N	\N	t
249	elite-rug-plus-249	Elite Rug Plus	94.88	94.88	61	High-quality rug with excellent features and performance	f	t	5.00	92	\N	\N	t
250	essential-lamp-max-250	Essential Lamp Max	37.49	53.56	74	High-quality lamp with excellent features and performance	f	f	4.80	480	\N	\N	t
251	essential-mirror-edition-251	Essential Mirror Edition	655.00	655.00	56	High-quality mirror with excellent features and performance	f	f	4.90	452	\N	\N	t
252	perfect-clock-plus-252	Perfect Clock Plus	823.00	1028.75	93	High-quality clock with excellent features and performance	f	f	4.10	101	\N	\N	t
253	premium-vase-plus-253	Premium Vase Plus	302.00	302.00	99	High-quality vase with excellent features and performance	f	f	4.90	284	\N	\N	t
254	luxury-picture-frame-collection-254	Luxury Picture Frame Collection	498.00	622.50	74	High-quality picture frame with excellent features and performance	f	f	3.60	262	\N	\N	t
255	elite-bookshelf-model-255	Elite Bookshelf Model	646.00	646.00	71	High-quality bookshelf with excellent features and performance	t	f	3.80	492	\N	\N	t
256	essential-cabinet-series-256	Essential Cabinet Series	371.06	371.06	84	High-quality cabinet with excellent features and performance	f	f	4.70	31	\N	\N	t
257	supreme-coffee-maker-performance-257	Supreme Coffee Maker Performance	469.00	469.00	17	High-quality coffee maker with excellent features and performance	f	f	4.10	298	\N	\N	t
258	premium-blender-edition-258	Premium Blender Edition	501.00	501.00	14	High-quality blender with excellent features and performance	f	f	4.00	322	\N	\N	t
259	elite-toaster-performance-259	Elite Toaster Performance	902.00	949.47	28	High-quality toaster with excellent features and performance	f	t	4.80	129	\N	\N	t
260	master-microwave-plus-260	Master Microwave Plus	998.00	1174.12	77	High-quality microwave with excellent features and performance	f	t	4.90	135	\N	\N	t
261	master-sofa-series-261	Master Sofa Series	852.40	852.40	62	High-quality sofa with excellent features and performance	f	t	3.90	299	\N	\N	t
262	premium-chair-max-262	Premium Chair Max	687.00	981.43	91	High-quality chair with excellent features and performance	f	f	3.50	151	\N	\N	t
263	essential-table-performance-263	Essential Table Performance	408.75	480.88	61	High-quality table with excellent features and performance	f	t	4.30	241	\N	\N	t
264	advanced-bed-grade-264	Advanced Bed Grade	760.70	894.94	68	High-quality bed with excellent features and performance	f	f	4.00	34	\N	\N	t
265	professional-mattress-series-265	Professional Mattress Series	487.75	487.75	88	High-quality mattress with excellent features and performance	f	f	4.70	323	\N	\N	t
266	pro-pillow-collection-266	Pro Pillow Collection	634.00	634.00	64	High-quality pillow with excellent features and performance	f	f	4.30	286	\N	\N	t
267	professional-blanket-max-267	Professional Blanket Max	438.00	486.67	76	High-quality blanket with excellent features and performance	f	t	3.50	85	\N	\N	t
268	elite-curtains-plus-268	Elite Curtains Plus	476.00	560.00	20	High-quality curtains with excellent features and performance	t	f	3.80	40	\N	\N	t
269	supreme-rug-version-269	Supreme Rug Version	680.00	906.67	21	High-quality rug with excellent features and performance	f	f	4.70	176	\N	\N	t
270	ultra-lamp-max-270	Ultra Lamp Max	712.00	712.00	70	High-quality lamp with excellent features and performance	f	f	4.30	130	\N	\N	t
271	master-mirror-performance-271	Master Mirror Performance	319.00	455.71	29	High-quality mirror with excellent features and performance	f	f	4.90	85	\N	\N	t
272	deluxe-clock-version-272	Deluxe Clock Version	735.00	1050.00	50	High-quality clock with excellent features and performance	f	f	3.70	56	\N	\N	t
273	expert-vase-grade-273	Expert Vase Grade	951.00	951.00	19	High-quality vase with excellent features and performance	f	f	4.50	277	\N	\N	t
274	master-picture-frame-collection-274	Master Picture Frame Collection	840.00	988.24	47	High-quality picture frame with excellent features and performance	f	f	4.40	319	\N	\N	t
275	ultra-bookshelf-performance-275	Ultra Bookshelf Performance	513.00	513.00	49	High-quality bookshelf with excellent features and performance	f	f	3.90	282	\N	\N	t
276	elite-cabinet-quality-276	Elite Cabinet Quality	300.00	375.00	30	High-quality cabinet with excellent features and performance	f	f	4.80	196	\N	\N	t
277	premium-coffee-maker-series-277	Premium Coffee Maker Series	336.00	373.33	1	High-quality coffee maker with excellent features and performance	f	f	4.60	73	\N	\N	t
278	master-blender-series-278	Master Blender Series	961.00	961.00	62	High-quality blender with excellent features and performance	f	t	4.40	442	\N	\N	t
279	modern-toaster-performance-279	Modern Toaster Performance	883.00	883.00	44	High-quality toaster with excellent features and performance	f	f	5.00	341	\N	\N	t
280	pro-microwave-collection-280	Pro Microwave Collection	817.00	817.00	95	High-quality microwave with excellent features and performance	f	f	4.00	250	\N	\N	t
281	classic-sofa-quality-281	Classic Sofa Quality	1148.40	1148.40	45	High-quality sofa with excellent features and performance	f	f	5.00	366	\N	\N	t
282	premium-chair-max-282	Premium Chair Max	610.00	813.33	76	High-quality chair with excellent features and performance	f	f	4.00	290	\N	\N	t
283	deluxe-table-edition-283	Deluxe Table Edition	791.00	1054.67	23	High-quality table with excellent features and performance	t	f	4.70	135	\N	\N	t
284	elite-bed-performance-284	Elite Bed Performance	418.21	522.76	71	High-quality bed with excellent features and performance	f	f	3.50	56	\N	\N	t
285	elite-mattress-model-285	Elite Mattress Model	603.00	709.41	66	High-quality mattress with excellent features and performance	f	f	4.80	470	\N	\N	t
286	classic-pillow-max-286	Classic Pillow Max	487.00	695.71	31	High-quality pillow with excellent features and performance	f	f	4.60	163	\N	\N	t
287	perfect-blanket-plus-287	Perfect Blanket Plus	886.00	1042.35	8	High-quality blanket with excellent features and performance	f	f	5.00	414	\N	\N	t
288	modern-curtains-collection-288	Modern Curtains Collection	407.00	407.00	69	High-quality curtains with excellent features and performance	f	t	4.60	238	\N	\N	t
289	expert-rug-plus-289	Expert Rug Plus	750.00	750.00	43	High-quality rug with excellent features and performance	f	f	5.00	269	\N	\N	t
290	premium-lamp-performance-290	Premium Lamp Performance	979.00	1305.33	94	High-quality lamp with excellent features and performance	t	t	4.80	13	\N	\N	t
291	modern-mirror-quality-291	Modern Mirror Quality	393.00	561.43	35	High-quality mirror with excellent features and performance	f	f	3.60	112	\N	\N	t
292	ultra-clock-model-292	Ultra Clock Model	684.00	684.00	7	High-quality clock with excellent features and performance	t	f	4.80	162	\N	\N	t
293	perfect-vase-max-293	Perfect Vase Max	627.00	895.71	99	High-quality vase with excellent features and performance	t	t	4.10	325	\N	\N	t
294	supreme-picture-frame-version-294	Supreme Picture Frame Version	474.00	526.67	45	High-quality picture frame with excellent features and performance	t	t	3.60	178	\N	\N	t
295	professional-bookshelf-edition-295	Professional Bookshelf Edition	790.00	831.58	19	High-quality bookshelf with excellent features and performance	f	t	3.60	46	\N	\N	t
296	essential-cabinet-max-296	Essential Cabinet Max	631.00	631.00	17	High-quality cabinet with excellent features and performance	f	f	4.50	451	\N	\N	t
297	advanced-coffee-maker-collection-297	Advanced Coffee Maker Collection	384.00	384.00	78	High-quality coffee maker with excellent features and performance	f	f	4.70	281	\N	\N	t
298	essential-blender-performance-298	Essential Blender Performance	359.00	422.35	1	High-quality blender with excellent features and performance	f	t	4.40	493	\N	\N	t
299	premium-toaster-edition-299	Premium Toaster Edition	607.00	607.00	91	High-quality toaster with excellent features and performance	f	f	3.50	328	\N	\N	t
300	classic-microwave-edition-300	Classic Microwave Edition	396.00	416.84	79	High-quality microwave with excellent features and performance	f	f	3.70	57	\N	\N	t
301	classic-running-shoes-performance-301	Classic Running Shoes Performance	712.00	791.11	66	High-quality running shoes with excellent features and performance	f	t	5.00	316	\N	\N	t
302	premium-yoga-mat-version-302	Premium Yoga Mat Version	765.00	805.26	20	High-quality yoga mat with excellent features and performance	f	f	4.80	453	\N	\N	t
303	ultra-dumbbells-edition-303	Ultra Dumbbells Edition	928.00	1237.33	37	High-quality dumbbells with excellent features and performance	f	t	4.80	210	\N	\N	t
304	supreme-resistance-bands-model-304	Supreme Resistance Bands Model	617.00	649.47	21	High-quality resistance bands with excellent features and performance	t	t	4.50	325	\N	\N	t
305	modern-jump-rope-plus-305	Modern Jump Rope Plus	426.00	426.00	63	High-quality jump rope with excellent features and performance	t	f	3.60	372	\N	\N	t
306	professional-gym-bag-performance-306	Professional Gym Bag Performance	471.00	471.00	57	High-quality gym bag with excellent features and performance	f	t	4.90	199	\N	\N	t
307	modern-water-bottle-model-307	Modern Water Bottle Model	786.00	873.33	38	High-quality water bottle with excellent features and performance	f	f	4.20	380	\N	\N	t
308	modern-protein-shaker-edition-308	Modern Protein Shaker Edition	492.00	702.86	100	High-quality protein shaker with excellent features and performance	f	f	3.60	447	\N	\N	t
309	supreme-fitness-tracker-version-309	Supreme Fitness Tracker Version	958.00	1127.06	8	High-quality fitness tracker with excellent features and performance	f	t	3.70	437	\N	\N	t
310	ultra-bicycle-performance-310	Ultra Bicycle Performance	469.00	586.25	13	High-quality bicycle with excellent features and performance	t	f	3.80	331	\N	\N	t
311	deluxe-helmet-grade-311	Deluxe Helmet Grade	975.00	975.00	9	High-quality helmet with excellent features and performance	t	f	4.10	174	\N	\N	t
312	deluxe-tennis-racket-plus-312	Deluxe Tennis Racket Plus	334.00	392.94	44	High-quality tennis racket with excellent features and performance	f	t	4.00	479	\N	\N	t
313	expert-basketball-series-313	Expert Basketball Series	478.00	682.86	96	High-quality basketball with excellent features and performance	t	f	4.20	300	\N	\N	t
314	pro-football-collection-314	Pro Football Collection	844.00	844.00	93	High-quality football with excellent features and performance	f	f	3.80	368	\N	\N	t
315	modern-soccer-ball-max-315	Modern Soccer Ball Max	736.00	920.00	30	High-quality soccer ball with excellent features and performance	f	f	4.30	238	\N	\N	t
316	elite-baseball-bat-quality-316	Elite Baseball Bat Quality	362.00	517.14	68	High-quality baseball bat with excellent features and performance	f	f	3.80	68	\N	\N	t
317	deluxe-golf-clubs-grade-317	Deluxe Golf Clubs Grade	791.00	791.00	48	High-quality golf clubs with excellent features and performance	f	f	4.40	323	\N	\N	t
318	professional-swimming-goggles-collection-318	Professional Swimming Goggles Collection	390.00	390.00	60	High-quality swimming goggles with excellent features and performance	t	f	4.60	444	\N	\N	t
319	advanced-yoga-blocks-performance-319	Advanced Yoga Blocks Performance	542.00	602.22	44	High-quality yoga blocks with excellent features and performance	f	f	4.90	47	\N	\N	t
320	classic-foam-roller-series-320	Classic Foam Roller Series	988.00	1411.43	87	High-quality foam roller with excellent features and performance	f	f	3.70	146	\N	\N	t
321	classic-running-shoes-version-321	Classic Running Shoes Version	583.00	728.75	51	High-quality running shoes with excellent features and performance	t	t	4.50	420	\N	\N	t
322	supreme-yoga-mat-version-322	Supreme Yoga Mat Version	846.00	1057.50	74	High-quality yoga mat with excellent features and performance	f	t	4.20	290	\N	\N	t
323	pro-dumbbells-grade-323	Pro Dumbbells Grade	449.00	528.24	9	High-quality dumbbells with excellent features and performance	f	f	3.60	339	\N	\N	t
324	professional-resistance-bands-performance-324	Professional Resistance Bands Performance	588.00	588.00	82	High-quality resistance bands with excellent features and performance	f	t	4.50	104	\N	\N	t
325	luxury-jump-rope-edition-325	Luxury Jump Rope Edition	342.00	456.00	67	High-quality jump rope with excellent features and performance	f	f	3.60	245	\N	\N	t
326	premium-gym-bag-edition-326	Premium Gym Bag Edition	440.00	440.00	5	High-quality gym bag with excellent features and performance	f	f	4.80	141	\N	\N	t
327	master-water-bottle-version-327	Master Water Bottle Version	301.00	316.84	57	High-quality water bottle with excellent features and performance	f	f	5.00	227	\N	\N	t
328	deluxe-protein-shaker-model-328	Deluxe Protein Shaker Model	412.00	433.68	92	High-quality protein shaker with excellent features and performance	f	f	4.50	249	\N	\N	t
329	master-fitness-tracker-model-329	Master Fitness Tracker Model	506.00	632.50	92	High-quality fitness tracker with excellent features and performance	f	f	3.50	295	\N	\N	t
330	essential-bicycle-series-330	Essential Bicycle Series	341.76	455.68	40	High-quality bicycle with excellent features and performance	f	f	3.70	323	\N	\N	t
331	professional-helmet-model-331	Professional Helmet Model	376.00	376.00	40	High-quality helmet with excellent features and performance	f	f	4.90	177	\N	\N	t
332	perfect-tennis-racket-model-332	Perfect Tennis Racket Model	538.00	566.32	94	High-quality tennis racket with excellent features and performance	f	t	3.70	86	\N	\N	t
333	elite-basketball-max-333	Elite Basketball Max	966.00	1207.50	4	High-quality basketball with excellent features and performance	f	f	4.60	94	\N	\N	t
334	luxury-football-grade-334	Luxury Football Grade	507.00	563.33	3	High-quality football with excellent features and performance	f	t	5.00	235	\N	\N	t
335	essential-soccer-ball-series-335	Essential Soccer Ball Series	652.00	815.00	80	High-quality soccer ball with excellent features and performance	f	f	4.40	59	\N	\N	t
336	professional-baseball-bat-grade-336	Professional Baseball Bat Grade	629.00	740.00	74	High-quality baseball bat with excellent features and performance	f	t	4.70	475	\N	\N	t
337	modern-golf-clubs-version-337	Modern Golf Clubs Version	549.37	549.37	58	High-quality golf clubs with excellent features and performance	t	f	4.20	63	\N	\N	t
338	premium-swimming-goggles-model-338	Premium Swimming Goggles Model	784.00	784.00	62	High-quality swimming goggles with excellent features and performance	f	f	3.60	281	\N	\N	t
339	expert-yoga-blocks-model-339	Expert Yoga Blocks Model	795.00	836.84	34	High-quality yoga blocks with excellent features and performance	f	t	4.80	475	\N	\N	t
340	supreme-foam-roller-series-340	Supreme Foam Roller Series	797.00	1138.57	48	High-quality foam roller with excellent features and performance	f	f	4.60	211	\N	\N	t
341	supreme-running-shoes-quality-341	Supreme Running Shoes Quality	683.00	910.67	27	High-quality running shoes with excellent features and performance	f	t	3.50	126	\N	\N	t
342	professional-yoga-mat-max-342	Professional Yoga Mat Max	335.00	352.63	80	High-quality yoga mat with excellent features and performance	f	f	4.80	339	\N	\N	t
343	supreme-dumbbells-model-343	Supreme Dumbbells Model	880.00	880.00	49	High-quality dumbbells with excellent features and performance	t	t	4.40	440	\N	\N	t
344	pro-resistance-bands-quality-344	Pro Resistance Bands Quality	429.00	451.58	73	High-quality resistance bands with excellent features and performance	t	f	3.60	162	\N	\N	t
345	essential-jump-rope-model-345	Essential Jump Rope Model	327.00	408.75	93	High-quality jump rope with excellent features and performance	f	f	4.30	337	\N	\N	t
346	elite-gym-bag-plus-346	Elite Gym Bag Plus	428.00	428.00	28	High-quality gym bag with excellent features and performance	t	f	3.60	146	\N	\N	t
347	deluxe-water-bottle-edition-347	Deluxe Water Bottle Edition	858.00	858.00	69	High-quality water bottle with excellent features and performance	f	f	4.20	465	\N	\N	t
348	expert-protein-shaker-model-348	Expert Protein Shaker Model	890.00	890.00	84	High-quality protein shaker with excellent features and performance	f	f	4.30	362	\N	\N	t
349	master-fitness-tracker-version-349	Master Fitness Tracker Version	891.00	891.00	48	High-quality fitness tracker with excellent features and performance	f	f	4.20	10	\N	\N	t
350	supreme-bicycle-max-350	Supreme Bicycle Max	337.39	449.85	29	High-quality bicycle with excellent features and performance	t	f	4.70	333	\N	\N	t
351	essential-helmet-plus-351	Essential Helmet Plus	483.00	690.00	19	High-quality helmet with excellent features and performance	f	t	4.20	314	\N	\N	t
352	elite-tennis-racket-plus-352	Elite Tennis Racket Plus	443.00	521.18	42	High-quality tennis racket with excellent features and performance	f	f	4.50	98	\N	\N	t
353	supreme-basketball-model-353	Supreme Basketball Model	876.00	876.00	82	High-quality basketball with excellent features and performance	f	t	4.90	499	\N	\N	t
354	deluxe-football-series-354	Deluxe Football Series	302.00	317.89	32	High-quality football with excellent features and performance	f	f	4.40	34	\N	\N	t
355	luxury-soccer-ball-edition-355	Luxury Soccer Ball Edition	499.00	554.44	19	High-quality soccer ball with excellent features and performance	f	f	4.20	197	\N	\N	t
356	pro-baseball-bat-grade-356	Pro Baseball Bat Grade	690.00	985.71	33	High-quality baseball bat with excellent features and performance	f	f	5.00	15	\N	\N	t
357	classic-golf-clubs-max-357	Classic Golf Clubs Max	443.67	554.59	66	High-quality golf clubs with excellent features and performance	t	t	3.80	464	\N	\N	t
358	expert-swimming-goggles-quality-358	Expert Swimming Goggles Quality	395.00	395.00	94	High-quality swimming goggles with excellent features and performance	f	f	4.70	91	\N	\N	t
359	master-yoga-blocks-collection-359	Master Yoga Blocks Collection	775.00	775.00	56	High-quality yoga blocks with excellent features and performance	f	f	4.40	250	\N	\N	t
360	premium-foam-roller-quality-360	Premium Foam Roller Quality	855.00	1068.75	19	High-quality foam roller with excellent features and performance	f	t	3.60	147	\N	\N	t
361	luxury-running-shoes-grade-361	Luxury Running Shoes Grade	585.00	585.00	68	High-quality running shoes with excellent features and performance	f	f	4.20	244	\N	\N	t
362	professional-yoga-mat-performance-362	Professional Yoga Mat Performance	393.00	491.25	81	High-quality yoga mat with excellent features and performance	t	f	4.80	15	\N	\N	t
363	pro-dumbbells-plus-363	Pro Dumbbells Plus	958.00	1277.33	12	High-quality dumbbells with excellent features and performance	f	f	3.90	343	\N	\N	t
364	ultra-resistance-bands-performance-364	Ultra Resistance Bands Performance	853.00	1003.53	98	High-quality resistance bands with excellent features and performance	f	f	3.60	59	\N	\N	t
365	pro-jump-rope-version-365	Pro Jump Rope Version	973.00	1144.71	35	High-quality jump rope with excellent features and performance	f	f	4.10	380	\N	\N	t
366	deluxe-gym-bag-performance-366	Deluxe Gym Bag Performance	610.00	610.00	23	High-quality gym bag with excellent features and performance	t	f	4.90	222	\N	\N	t
367	professional-water-bottle-model-367	Professional Water Bottle Model	945.00	945.00	2	High-quality water bottle with excellent features and performance	t	f	3.70	392	\N	\N	t
368	professional-protein-shaker-model-368	Professional Protein Shaker Model	453.00	566.25	94	High-quality protein shaker with excellent features and performance	f	t	4.90	185	\N	\N	t
369	elite-fitness-tracker-grade-369	Elite Fitness Tracker Grade	547.00	547.00	93	High-quality fitness tracker with excellent features and performance	f	t	4.00	386	\N	\N	t
370	classic-bicycle-max-370	Classic Bicycle Max	304.84	381.05	60	High-quality bicycle with excellent features and performance	f	f	3.80	86	\N	\N	t
371	expert-helmet-max-371	Expert Helmet Max	886.00	1042.35	18	High-quality helmet with excellent features and performance	f	f	3.80	169	\N	\N	t
372	essential-tennis-racket-quality-372	Essential Tennis Racket Quality	608.00	608.00	93	High-quality tennis racket with excellent features and performance	f	f	4.10	274	\N	\N	t
373	professional-basketball-model-373	Professional Basketball Model	690.00	726.32	27	High-quality basketball with excellent features and performance	f	f	4.80	398	\N	\N	t
374	luxury-football-edition-374	Luxury Football Edition	326.00	383.53	90	High-quality football with excellent features and performance	f	f	4.90	82	\N	\N	t
375	ultra-soccer-ball-series-375	Ultra Soccer Ball Series	885.00	1106.25	98	High-quality soccer ball with excellent features and performance	t	t	4.40	404	\N	\N	t
376	advanced-baseball-bat-performance-376	Advanced Baseball Bat Performance	793.00	793.00	76	High-quality baseball bat with excellent features and performance	f	f	4.80	382	\N	\N	t
377	essential-golf-clubs-quality-377	Essential Golf Clubs Quality	419.58	524.47	71	High-quality golf clubs with excellent features and performance	f	f	4.00	173	\N	\N	t
378	expert-swimming-goggles-series-378	Expert Swimming Goggles Series	723.00	964.00	66	High-quality swimming goggles with excellent features and performance	t	f	4.20	317	\N	\N	t
379	perfect-yoga-blocks-performance-379	Perfect Yoga Blocks Performance	617.00	617.00	45	High-quality yoga blocks with excellent features and performance	f	t	3.60	184	\N	\N	t
380	advanced-foam-roller-series-380	Advanced Foam Roller Series	631.00	701.11	58	High-quality foam roller with excellent features and performance	f	f	3.90	336	\N	\N	t
381	essential-running-shoes-version-381	Essential Running Shoes Version	305.00	338.89	90	High-quality running shoes with excellent features and performance	f	f	4.10	315	\N	\N	t
382	ultra-yoga-mat-max-382	Ultra Yoga Mat Max	746.00	828.89	13	High-quality yoga mat with excellent features and performance	f	f	4.10	63	\N	\N	t
383	master-dumbbells-plus-383	Master Dumbbells Plus	419.00	419.00	29	High-quality dumbbells with excellent features and performance	t	f	3.90	115	\N	\N	t
384	modern-resistance-bands-series-384	Modern Resistance Bands Series	769.00	904.71	88	High-quality resistance bands with excellent features and performance	f	t	3.50	399	\N	\N	t
385	modern-jump-rope-edition-385	Modern Jump Rope Edition	735.00	735.00	33	High-quality jump rope with excellent features and performance	f	f	4.90	364	\N	\N	t
386	master-gym-bag-edition-386	Master Gym Bag Edition	464.00	515.56	87	High-quality gym bag with excellent features and performance	f	f	4.80	200	\N	\N	t
387	premium-water-bottle-series-387	Premium Water Bottle Series	629.00	698.89	79	High-quality water bottle with excellent features and performance	f	f	3.60	479	\N	\N	t
388	master-protein-shaker-edition-388	Master Protein Shaker Edition	771.00	771.00	93	High-quality protein shaker with excellent features and performance	f	f	3.90	261	\N	\N	t
389	ultra-fitness-tracker-version-389	Ultra Fitness Tracker Version	407.00	407.00	79	High-quality fitness tracker with excellent features and performance	f	f	4.00	457	\N	\N	t
390	master-bicycle-collection-390	Master Bicycle Collection	548.00	644.71	21	High-quality bicycle with excellent features and performance	t	f	3.90	94	\N	\N	t
391	professional-helmet-version-391	Professional Helmet Version	871.00	1161.33	83	High-quality helmet with excellent features and performance	f	f	4.60	262	\N	\N	t
392	modern-tennis-racket-version-392	Modern Tennis Racket Version	650.00	866.67	41	High-quality tennis racket with excellent features and performance	f	f	4.70	125	\N	\N	t
393	elite-basketball-model-393	Elite Basketball Model	505.00	505.00	36	High-quality basketball with excellent features and performance	f	f	5.00	274	\N	\N	t
394	classic-football-version-394	Classic Football Version	857.00	857.00	75	High-quality football with excellent features and performance	f	t	4.80	362	\N	\N	t
395	professional-soccer-ball-series-395	Professional Soccer Ball Series	351.00	351.00	75	High-quality soccer ball with excellent features and performance	t	f	3.80	146	\N	\N	t
396	expert-baseball-bat-edition-396	Expert Baseball Bat Edition	837.00	881.05	46	High-quality baseball bat with excellent features and performance	t	f	3.70	110	\N	\N	t
397	classic-golf-clubs-model-397	Classic Golf Clubs Model	496.94	496.94	50	High-quality golf clubs with excellent features and performance	f	f	4.80	484	\N	\N	t
398	deluxe-swimming-goggles-version-398	Deluxe Swimming Goggles Version	836.00	1045.00	25	High-quality swimming goggles with excellent features and performance	f	t	4.80	226	\N	\N	t
399	luxury-yoga-blocks-grade-399	Luxury Yoga Blocks Grade	381.00	381.00	37	High-quality yoga blocks with excellent features and performance	f	f	3.70	142	\N	\N	t
400	premium-foam-roller-model-400	Premium Foam Roller Model	784.00	784.00	85	High-quality foam roller with excellent features and performance	f	f	4.40	298	\N	\N	t
401	professional-fiction-novel-max-401	Professional Fiction Novel Max	960.00	1280.00	62	High-quality fiction novel with excellent features and performance	t	f	3.50	442	\N	\N	t
402	advanced-mystery-thriller-plus-402	Advanced Mystery Thriller Plus	559.00	657.65	40	High-quality mystery thriller with excellent features and performance	f	f	5.00	271	\N	\N	t
403	ultra-romance-book-quality-403	Ultra Romance Book Quality	358.00	511.43	30	High-quality romance book with excellent features and performance	f	t	5.00	191	\N	\N	t
404	expert-science-fiction-edition-404	Expert Science Fiction Edition	481.00	565.88	36	High-quality science fiction with excellent features and performance	f	f	4.80	151	\N	\N	t
405	luxury-fantasy-epic-performance-405	Luxury Fantasy Epic Performance	586.00	732.50	65	High-quality fantasy epic with excellent features and performance	f	f	3.70	141	\N	\N	t
406	advanced-biography-plus-406	Advanced Biography Plus	303.00	432.86	24	High-quality biography with excellent features and performance	f	f	4.20	66	\N	\N	t
407	supreme-self-help-guide-collection-407	Supreme Self-Help Guide Collection	974.00	974.00	50	High-quality self-help guide with excellent features and performance	f	f	4.70	92	\N	\N	t
408	modern-business-book-model-408	Modern Business Book Model	414.00	517.50	11	High-quality business book with excellent features and performance	f	f	4.80	417	\N	\N	t
409	supreme-cookbook-quality-409	Supreme Cookbook Quality	657.00	657.00	18	High-quality cookbook with excellent features and performance	f	t	4.40	145	\N	\N	t
410	expert-travel-guide-model-410	Expert Travel Guide Model	861.00	956.67	31	High-quality travel guide with excellent features and performance	f	f	4.80	374	\N	\N	t
411	classic-history-book-quality-411	Classic History Book Quality	319.00	425.33	19	High-quality history book with excellent features and performance	f	f	4.00	170	\N	\N	t
412	deluxe-poetry-collection-version-412	Deluxe Poetry Collection Version	583.00	583.00	11	High-quality poetry collection with excellent features and performance	f	f	4.60	443	\N	\N	t
413	perfect-art-book-max-413	Perfect Art Book Max	726.00	726.00	63	High-quality art book with excellent features and performance	t	f	3.60	161	\N	\N	t
414	expert-photography-book-performance-414	Expert Photography Book Performance	788.00	875.56	71	High-quality photography book with excellent features and performance	f	f	4.90	182	\N	\N	t
415	professional-programming-guide-version-415	Professional Programming Guide Version	703.00	703.00	82	High-quality programming guide with excellent features and performance	f	t	3.60	90	\N	\N	t
416	classic-marketing-book-version-416	Classic Marketing Book Version	402.00	472.94	6	High-quality marketing book with excellent features and performance	f	t	4.40	164	\N	\N	t
417	luxury-psychology-book-performance-417	Luxury Psychology Book Performance	513.00	684.00	28	High-quality psychology book with excellent features and performance	f	f	4.80	454	\N	\N	t
418	professional-philosophy-book-model-418	Professional Philosophy Book Model	819.00	963.53	5	High-quality philosophy book with excellent features and performance	f	f	3.50	302	\N	\N	t
419	professional-children's-book-plus-419	Professional Children's Book Plus	738.00	922.50	47	High-quality children's book with excellent features and performance	f	f	3.80	369	\N	\N	t
420	pro-comic-book-series-420	Pro Comic Book Series	696.00	732.63	14	High-quality comic book with excellent features and performance	f	f	4.00	324	\N	\N	t
421	modern-fiction-novel-series-421	Modern Fiction Novel Series	952.00	952.00	66	High-quality fiction novel with excellent features and performance	f	f	3.70	198	\N	\N	t
422	luxury-mystery-thriller-series-422	Luxury Mystery Thriller Series	857.00	857.00	43	High-quality mystery thriller with excellent features and performance	f	f	4.20	486	\N	\N	t
423	expert-romance-book-quality-423	Expert Romance Book Quality	869.00	1022.35	100	High-quality romance book with excellent features and performance	f	f	4.20	478	\N	\N	t
424	pro-science-fiction-max-424	Pro Science Fiction Max	324.00	381.18	58	High-quality science fiction with excellent features and performance	f	f	3.80	249	\N	\N	t
425	master-fantasy-epic-plus-425	Master Fantasy Epic Plus	505.00	721.43	43	High-quality fantasy epic with excellent features and performance	f	f	3.90	416	\N	\N	t
426	ultra-biography-quality-426	Ultra Biography Quality	779.00	916.47	10	High-quality biography with excellent features and performance	f	t	3.80	417	\N	\N	t
427	essential-self-help-guide-max-427	Essential Self-Help Guide Max	869.00	1022.35	8	High-quality self-help guide with excellent features and performance	f	f	4.10	172	\N	\N	t
428	ultra-business-book-series-428	Ultra Business Book Series	572.00	762.67	41	High-quality business book with excellent features and performance	t	t	3.80	225	\N	\N	t
429	professional-cookbook-quality-429	Professional Cookbook Quality	689.00	689.00	38	High-quality cookbook with excellent features and performance	f	f	4.50	375	\N	\N	t
430	professional-travel-guide-quality-430	Professional Travel Guide Quality	454.00	454.00	80	High-quality travel guide with excellent features and performance	f	f	4.30	126	\N	\N	t
431	classic-history-book-series-431	Classic History Book Series	648.00	762.35	39	High-quality history book with excellent features and performance	f	f	4.20	425	\N	\N	t
432	classic-poetry-collection-edition-432	Classic Poetry Collection Edition	546.00	574.74	57	High-quality poetry collection with excellent features and performance	f	t	4.30	402	\N	\N	t
433	professional-art-book-version-433	Professional Art Book Version	731.00	974.67	99	High-quality art book with excellent features and performance	f	t	4.80	239	\N	\N	t
434	advanced-photography-book-version-434	Advanced Photography Book Version	591.00	622.11	44	High-quality photography book with excellent features and performance	f	t	4.10	442	\N	\N	t
435	essential-programming-guide-edition-435	Essential Programming Guide Edition	669.00	955.71	2	High-quality programming guide with excellent features and performance	f	t	4.10	67	\N	\N	t
436	luxury-marketing-book-quality-436	Luxury Marketing Book Quality	451.00	451.00	40	High-quality marketing book with excellent features and performance	f	t	3.80	362	\N	\N	t
437	perfect-psychology-book-version-437	Perfect Psychology Book Version	388.00	554.29	25	High-quality psychology book with excellent features and performance	f	t	4.80	255	\N	\N	t
438	elite-philosophy-book-series-438	Elite Philosophy Book Series	467.00	622.67	20	High-quality philosophy book with excellent features and performance	f	f	3.50	302	\N	\N	t
439	premium-children's-book-max-439	Premium Children's Book Max	734.00	734.00	37	High-quality children's book with excellent features and performance	f	f	4.50	278	\N	\N	t
440	master-comic-book-model-440	Master Comic Book Model	445.00	556.25	56	High-quality comic book with excellent features and performance	f	t	4.70	164	\N	\N	t
441	essential-fiction-novel-series-441	Essential Fiction Novel Series	883.00	1038.82	58	High-quality fiction novel with excellent features and performance	f	f	4.00	13	\N	\N	t
442	elite-mystery-thriller-grade-442	Elite Mystery Thriller Grade	875.00	875.00	53	High-quality mystery thriller with excellent features and performance	f	f	3.70	372	\N	\N	t
443	modern-romance-book-max-443	Modern Romance Book Max	749.00	788.42	34	High-quality romance book with excellent features and performance	f	f	4.70	394	\N	\N	t
444	elite-science-fiction-max-444	Elite Science Fiction Max	884.00	884.00	89	High-quality science fiction with excellent features and performance	f	t	4.00	219	\N	\N	t
445	pro-fantasy-epic-quality-445	Pro Fantasy Epic Quality	594.00	698.82	12	High-quality fantasy epic with excellent features and performance	f	f	5.00	395	\N	\N	t
446	deluxe-biography-performance-446	Deluxe Biography Performance	814.00	904.44	40	High-quality biography with excellent features and performance	f	f	4.90	82	\N	\N	t
447	professional-self-help-guide-version-447	Professional Self-Help Guide Version	339.00	423.75	30	High-quality self-help guide with excellent features and performance	f	f	5.00	358	\N	\N	t
448	perfect-business-book-edition-448	Perfect Business Book Edition	650.00	650.00	4	High-quality business book with excellent features and performance	f	f	3.90	103	\N	\N	t
449	elite-cookbook-model-449	Elite Cookbook Model	480.00	480.00	57	High-quality cookbook with excellent features and performance	f	f	4.00	220	\N	\N	t
450	deluxe-travel-guide-max-450	Deluxe Travel Guide Max	915.00	1220.00	67	High-quality travel guide with excellent features and performance	f	f	4.00	275	\N	\N	t
451	advanced-history-book-collection-451	Advanced History Book Collection	563.00	563.00	80	High-quality history book with excellent features and performance	f	f	3.90	381	\N	\N	t
452	perfect-poetry-collection-version-452	Perfect Poetry Collection Version	519.00	519.00	84	High-quality poetry collection with excellent features and performance	f	f	4.50	186	\N	\N	t
453	elite-art-book-model-453	Elite Art Book Model	311.00	345.56	77	High-quality art book with excellent features and performance	t	f	4.80	110	\N	\N	t
454	master-photography-book-grade-454	Master Photography Book Grade	876.00	876.00	84	High-quality photography book with excellent features and performance	f	f	4.80	423	\N	\N	t
455	premium-programming-guide-max-455	Premium Programming Guide Max	731.00	860.00	4	High-quality programming guide with excellent features and performance	f	f	3.80	45	\N	\N	t
456	modern-marketing-book-plus-456	Modern Marketing Book Plus	521.00	521.00	88	High-quality marketing book with excellent features and performance	f	f	5.00	105	\N	\N	t
457	professional-psychology-book-collection-457	Professional Psychology Book Collection	582.00	646.67	59	High-quality psychology book with excellent features and performance	f	t	4.10	209	\N	\N	t
458	expert-philosophy-book-series-458	Expert Philosophy Book Series	583.00	832.86	77	High-quality philosophy book with excellent features and performance	t	t	4.50	308	\N	\N	t
459	modern-children's-book-max-459	Modern Children's Book Max	628.00	628.00	20	High-quality children's book with excellent features and performance	f	f	3.80	179	\N	\N	t
460	elite-comic-book-version-460	Elite Comic Book Version	406.00	406.00	92	High-quality comic book with excellent features and performance	f	f	3.60	51	\N	\N	t
461	advanced-fiction-novel-edition-461	Advanced Fiction Novel Edition	649.00	721.11	81	High-quality fiction novel with excellent features and performance	f	t	3.80	134	\N	\N	t
462	supreme-mystery-thriller-series-462	Supreme Mystery Thriller Series	865.00	910.53	38	High-quality mystery thriller with excellent features and performance	f	f	4.10	324	\N	\N	t
463	modern-romance-book-max-463	Modern Romance Book Max	964.00	1014.74	31	High-quality romance book with excellent features and performance	f	f	4.70	484	\N	\N	t
464	luxury-science-fiction-performance-464	Luxury Science Fiction Performance	923.00	1153.75	86	High-quality science fiction with excellent features and performance	t	t	3.60	320	\N	\N	t
465	pro-fantasy-epic-model-465	Pro Fantasy Epic Model	435.00	457.89	83	High-quality fantasy epic with excellent features and performance	f	f	3.80	38	\N	\N	t
466	advanced-biography-version-466	Advanced Biography Version	833.00	1110.67	2	High-quality biography with excellent features and performance	t	t	4.10	198	\N	\N	t
467	pro-self-help-guide-max-467	Pro Self-Help Guide Max	919.00	919.00	10	High-quality self-help guide with excellent features and performance	t	f	4.80	320	\N	\N	t
468	premium-business-book-plus-468	Premium Business Book Plus	586.00	837.14	95	High-quality business book with excellent features and performance	f	f	3.70	182	\N	\N	t
469	supreme-cookbook-plus-469	Supreme Cookbook Plus	514.00	514.00	85	High-quality cookbook with excellent features and performance	f	f	3.60	421	\N	\N	t
470	essential-travel-guide-quality-470	Essential Travel Guide Quality	565.00	753.33	25	High-quality travel guide with excellent features and performance	f	f	4.70	207	\N	\N	t
471	professional-history-book-series-471	Professional History Book Series	823.00	968.24	93	High-quality history book with excellent features and performance	t	f	3.80	204	\N	\N	t
472	essential-poetry-collection-performance-472	Essential Poetry Collection Performance	845.00	1207.14	97	High-quality poetry collection with excellent features and performance	t	f	4.30	331	\N	\N	t
473	elite-art-book-max-473	Elite Art Book Max	814.00	1162.86	83	High-quality art book with excellent features and performance	f	t	4.10	207	\N	\N	t
474	supreme-photography-book-plus-474	Supreme Photography Book Plus	980.00	980.00	92	High-quality photography book with excellent features and performance	t	f	3.60	395	\N	\N	t
475	essential-programming-guide-collection-475	Essential Programming Guide Collection	713.00	713.00	71	High-quality programming guide with excellent features and performance	f	f	4.70	406	\N	\N	t
476	ultra-marketing-book-max-476	Ultra Marketing Book Max	674.00	898.67	63	High-quality marketing book with excellent features and performance	f	t	4.10	315	\N	\N	t
477	luxury-psychology-book-model-477	Luxury Psychology Book Model	531.00	708.00	100	High-quality psychology book with excellent features and performance	f	f	4.10	10	\N	\N	t
478	expert-philosophy-book-series-478	Expert Philosophy Book Series	329.00	329.00	48	High-quality philosophy book with excellent features and performance	f	f	4.40	441	\N	\N	t
479	pro-children's-book-collection-479	Pro Children's Book Collection	859.00	1227.14	92	High-quality children's book with excellent features and performance	f	f	4.90	290	\N	\N	t
480	premium-comic-book-version-480	Premium Comic Book Version	326.00	465.71	4	High-quality comic book with excellent features and performance	f	f	4.50	382	\N	\N	t
481	professional-fiction-novel-quality-481	Professional Fiction Novel Quality	765.00	956.25	52	High-quality fiction novel with excellent features and performance	f	f	4.50	120	\N	\N	t
482	premium-mystery-thriller-series-482	Premium Mystery Thriller Series	886.00	1107.50	4	High-quality mystery thriller with excellent features and performance	f	f	3.70	432	\N	\N	t
483	advanced-romance-book-model-483	Advanced Romance Book Model	706.00	706.00	22	High-quality romance book with excellent features and performance	f	f	3.90	329	\N	\N	t
484	advanced-science-fiction-plus-484	Advanced Science Fiction Plus	988.00	988.00	26	High-quality science fiction with excellent features and performance	f	f	3.80	82	\N	\N	t
485	supreme-fantasy-epic-version-485	Supreme Fantasy Epic Version	363.00	427.06	88	High-quality fantasy epic with excellent features and performance	f	f	4.60	407	\N	\N	t
486	luxury-biography-quality-486	Luxury Biography Quality	508.00	508.00	40	High-quality biography with excellent features and performance	f	f	4.90	213	\N	\N	t
487	ultra-self-help-guide-max-487	Ultra Self-Help Guide Max	542.00	542.00	38	High-quality self-help guide with excellent features and performance	t	f	5.00	374	\N	\N	t
488	perfect-business-book-quality-488	Perfect Business Book Quality	546.00	682.50	67	High-quality business book with excellent features and performance	f	f	4.80	325	\N	\N	t
489	pro-cookbook-series-489	Pro Cookbook Series	337.00	481.43	32	High-quality cookbook with excellent features and performance	t	f	4.50	499	\N	\N	t
490	expert-travel-guide-edition-490	Expert Travel Guide Edition	577.00	769.33	30	High-quality travel guide with excellent features and performance	f	t	3.80	405	\N	\N	t
491	pro-history-book-plus-491	Pro History Book Plus	314.00	448.57	90	High-quality history book with excellent features and performance	f	f	4.00	348	\N	\N	t
492	pro-poetry-collection-max-492	Pro Poetry Collection Max	906.00	906.00	90	High-quality poetry collection with excellent features and performance	f	f	3.50	53	\N	\N	t
493	advanced-art-book-grade-493	Advanced Art Book Grade	680.00	800.00	60	High-quality art book with excellent features and performance	t	f	4.30	409	\N	\N	t
494	supreme-photography-book-version-494	Supreme Photography Book Version	472.00	496.84	33	High-quality photography book with excellent features and performance	f	f	3.90	148	\N	\N	t
495	master-programming-guide-version-495	Master Programming Guide Version	807.00	807.00	18	High-quality programming guide with excellent features and performance	f	t	5.00	282	\N	\N	t
496	advanced-marketing-book-performance-496	Advanced Marketing Book Performance	440.00	440.00	22	High-quality marketing book with excellent features and performance	f	f	4.80	434	\N	\N	t
497	perfect-psychology-book-series-497	Perfect Psychology Book Series	392.00	392.00	39	High-quality psychology book with excellent features and performance	f	f	4.90	470	\N	\N	t
498	supreme-philosophy-book-series-498	Supreme Philosophy Book Series	958.00	1197.50	97	High-quality philosophy book with excellent features and performance	f	f	4.60	252	\N	\N	t
499	luxury-children's-book-version-499	Luxury Children's Book Version	398.00	398.00	89	High-quality children's book with excellent features and performance	t	t	4.10	374	\N	\N	t
500	ultra-comic-book-series-500	Ultra Comic Book Series	776.00	776.00	51	High-quality comic book with excellent features and performance	f	f	5.00	46	\N	\N	t
\.


--
-- TOC entry 5482 (class 0 OID 25309)
-- Dependencies: 237
-- Data for Name: catalog_product_image; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.catalog_product_image (image_id, product_entity_id, image_path, image_position, is_primary, created_at) FROM stdin;
506	1	laptop-1.webp	0	t	2026-02-04 09:24:10
507	1	laptop-2.webp	1	f	2026-02-04 09:24:10
508	1	laptop-3.webp	2	f	2026-02-04 09:24:10
509	2	laptop-1.webp	0	t	2026-02-04 09:24:10
510	2	laptop-2.webp	1	f	2026-02-04 09:24:10
511	2	laptop-3.webp	2	f	2026-02-04 09:24:10
512	3	laptop-1.webp	0	t	2026-02-04 09:24:10
513	3	laptop-2.webp	1	f	2026-02-04 09:24:10
514	3	laptop-3.webp	2	f	2026-02-04 09:24:10
515	4	laptop-1.webp	0	t	2026-02-04 09:24:10
516	4	laptop-2.webp	1	f	2026-02-04 09:24:10
517	4	laptop-3.webp	2	f	2026-02-04 09:24:10
518	5	laptop-1.webp	0	t	2026-02-04 09:24:10
519	5	laptop-2.webp	1	f	2026-02-04 09:24:10
520	5	laptop-3.webp	2	f	2026-02-04 09:24:10
521	6	laptop-1.webp	0	t	2026-02-04 09:24:10
522	6	laptop-2.webp	1	f	2026-02-04 09:24:10
523	6	laptop-3.webp	2	f	2026-02-04 09:24:10
524	7	laptop-1.webp	0	t	2026-02-04 09:24:10
525	7	laptop-2.webp	1	f	2026-02-04 09:24:10
526	7	laptop-3.webp	2	f	2026-02-04 09:24:10
527	8	laptop-1.webp	0	t	2026-02-04 09:24:10
528	8	laptop-2.webp	1	f	2026-02-04 09:24:10
529	8	laptop-3.webp	2	f	2026-02-04 09:24:10
530	9	laptop-1.webp	0	t	2026-02-04 09:24:10
531	9	laptop-2.webp	1	f	2026-02-04 09:24:10
532	9	laptop-3.webp	2	f	2026-02-04 09:24:10
533	10	laptop-1.webp	0	t	2026-02-04 09:24:10
534	10	laptop-2.webp	1	f	2026-02-04 09:24:10
535	10	laptop-3.webp	2	f	2026-02-04 09:24:10
536	11	watch-1.webp	0	t	2026-02-04 09:24:10
537	11	watch-2.webp	1	f	2026-02-04 09:24:10
538	11	watch-3.webp	2	f	2026-02-04 09:24:10
539	12	laptop-1.webp	0	t	2026-02-04 09:24:10
540	12	laptop-2.webp	1	f	2026-02-04 09:24:10
541	12	laptop-3.webp	2	f	2026-02-04 09:24:10
542	13	laptop-1.webp	0	t	2026-02-04 09:24:10
543	13	laptop-2.webp	1	f	2026-02-04 09:24:10
544	13	laptop-3.webp	2	f	2026-02-04 09:24:10
545	14	laptop-1.webp	0	t	2026-02-04 09:24:10
546	14	laptop-2.webp	1	f	2026-02-04 09:24:10
547	14	laptop-3.webp	2	f	2026-02-04 09:24:10
548	15	laptop-1.webp	0	t	2026-02-04 09:24:10
549	15	laptop-2.webp	1	f	2026-02-04 09:24:10
550	15	laptop-3.webp	2	f	2026-02-04 09:24:10
551	16	laptop-1.webp	0	t	2026-02-04 09:24:10
552	16	laptop-2.webp	1	f	2026-02-04 09:24:10
553	16	laptop-3.webp	2	f	2026-02-04 09:24:10
554	17	laptop-1.webp	0	t	2026-02-04 09:24:10
555	17	laptop-2.webp	1	f	2026-02-04 09:24:10
556	17	laptop-3.webp	2	f	2026-02-04 09:24:10
557	18	laptop-1.webp	0	t	2026-02-04 09:24:10
558	18	laptop-2.webp	1	f	2026-02-04 09:24:10
559	18	laptop-3.webp	2	f	2026-02-04 09:24:10
560	19	laptop-1.webp	0	t	2026-02-04 09:24:10
561	19	laptop-2.webp	1	f	2026-02-04 09:24:10
562	19	laptop-3.webp	2	f	2026-02-04 09:24:10
563	20	laptop-1.webp	0	t	2026-02-04 09:24:10
564	20	laptop-2.webp	1	f	2026-02-04 09:24:10
565	20	laptop-3.webp	2	f	2026-02-04 09:24:10
566	21	laptop-1.webp	0	t	2026-02-04 09:24:10
567	21	laptop-2.webp	1	f	2026-02-04 09:24:10
568	21	laptop-3.webp	2	f	2026-02-04 09:24:10
569	22	laptop-1.webp	0	t	2026-02-04 09:24:10
570	22	laptop-2.webp	1	f	2026-02-04 09:24:10
571	22	laptop-3.webp	2	f	2026-02-04 09:24:10
572	23	laptop-1.webp	0	t	2026-02-04 09:24:10
573	23	laptop-2.webp	1	f	2026-02-04 09:24:10
574	23	laptop-3.webp	2	f	2026-02-04 09:24:10
575	24	laptop-1.webp	0	t	2026-02-04 09:24:10
576	24	laptop-2.webp	1	f	2026-02-04 09:24:10
577	24	laptop-3.webp	2	f	2026-02-04 09:24:10
578	25	laptop-1.webp	0	t	2026-02-04 09:24:10
579	25	laptop-2.webp	1	f	2026-02-04 09:24:10
580	25	laptop-3.webp	2	f	2026-02-04 09:24:10
581	26	laptop-1.webp	0	t	2026-02-04 09:24:10
582	26	laptop-2.webp	1	f	2026-02-04 09:24:10
583	26	laptop-3.webp	2	f	2026-02-04 09:24:10
584	27	laptop-1.webp	0	t	2026-02-04 09:24:10
585	27	laptop-2.webp	1	f	2026-02-04 09:24:10
586	27	laptop-3.webp	2	f	2026-02-04 09:24:10
587	28	laptop-1.webp	0	t	2026-02-04 09:24:10
588	28	laptop-2.webp	1	f	2026-02-04 09:24:10
589	28	laptop-3.webp	2	f	2026-02-04 09:24:10
590	29	laptop-1.webp	0	t	2026-02-04 09:24:10
591	29	laptop-2.webp	1	f	2026-02-04 09:24:10
592	29	laptop-3.webp	2	f	2026-02-04 09:24:10
593	30	laptop-1.webp	0	t	2026-02-04 09:24:10
594	30	laptop-2.webp	1	f	2026-02-04 09:24:10
595	30	laptop-3.webp	2	f	2026-02-04 09:24:10
596	31	watch-1.webp	0	t	2026-02-04 09:24:10
597	31	watch-2.webp	1	f	2026-02-04 09:24:10
598	31	watch-3.webp	2	f	2026-02-04 09:24:10
599	32	laptop-1.webp	0	t	2026-02-04 09:24:10
600	32	laptop-2.webp	1	f	2026-02-04 09:24:10
601	32	laptop-3.webp	2	f	2026-02-04 09:24:10
602	33	laptop-1.webp	0	t	2026-02-04 09:24:10
603	33	laptop-2.webp	1	f	2026-02-04 09:24:10
604	33	laptop-3.webp	2	f	2026-02-04 09:24:10
605	34	laptop-1.webp	0	t	2026-02-04 09:24:10
606	34	laptop-2.webp	1	f	2026-02-04 09:24:10
607	34	laptop-3.webp	2	f	2026-02-04 09:24:10
608	35	laptop-1.webp	0	t	2026-02-04 09:24:10
609	35	laptop-2.webp	1	f	2026-02-04 09:24:10
610	35	laptop-3.webp	2	f	2026-02-04 09:24:10
611	36	laptop-1.webp	0	t	2026-02-04 09:24:10
612	36	laptop-2.webp	1	f	2026-02-04 09:24:10
613	36	laptop-3.webp	2	f	2026-02-04 09:24:10
614	37	laptop-1.webp	0	t	2026-02-04 09:24:10
615	37	laptop-2.webp	1	f	2026-02-04 09:24:10
616	37	laptop-3.webp	2	f	2026-02-04 09:24:10
617	38	laptop-1.webp	0	t	2026-02-04 09:24:10
618	38	laptop-2.webp	1	f	2026-02-04 09:24:10
619	38	laptop-3.webp	2	f	2026-02-04 09:24:10
620	39	laptop-1.webp	0	t	2026-02-04 09:24:10
621	39	laptop-2.webp	1	f	2026-02-04 09:24:10
622	39	laptop-3.webp	2	f	2026-02-04 09:24:10
623	40	laptop-1.webp	0	t	2026-02-04 09:24:10
624	40	laptop-2.webp	1	f	2026-02-04 09:24:10
625	40	laptop-3.webp	2	f	2026-02-04 09:24:10
626	41	laptop-1.webp	0	t	2026-02-04 09:24:10
627	41	laptop-2.webp	1	f	2026-02-04 09:24:10
628	41	laptop-3.webp	2	f	2026-02-04 09:24:10
629	42	laptop-1.webp	0	t	2026-02-04 09:24:10
630	42	laptop-2.webp	1	f	2026-02-04 09:24:10
631	42	laptop-3.webp	2	f	2026-02-04 09:24:10
632	43	laptop-1.webp	0	t	2026-02-04 09:24:10
633	43	laptop-2.webp	1	f	2026-02-04 09:24:10
634	43	laptop-3.webp	2	f	2026-02-04 09:24:10
635	44	laptop-1.webp	0	t	2026-02-04 09:24:10
636	44	laptop-2.webp	1	f	2026-02-04 09:24:10
637	44	laptop-3.webp	2	f	2026-02-04 09:24:10
638	45	laptop-1.webp	0	t	2026-02-04 09:24:10
639	45	laptop-2.webp	1	f	2026-02-04 09:24:10
640	45	laptop-3.webp	2	f	2026-02-04 09:24:10
641	46	laptop-1.webp	0	t	2026-02-04 09:24:10
642	46	laptop-2.webp	1	f	2026-02-04 09:24:10
643	46	laptop-3.webp	2	f	2026-02-04 09:24:10
644	47	laptop-1.webp	0	t	2026-02-04 09:24:10
645	47	laptop-2.webp	1	f	2026-02-04 09:24:10
646	47	laptop-3.webp	2	f	2026-02-04 09:24:10
647	48	laptop-1.webp	0	t	2026-02-04 09:24:10
648	48	laptop-2.webp	1	f	2026-02-04 09:24:10
649	48	laptop-3.webp	2	f	2026-02-04 09:24:10
650	49	laptop-1.webp	0	t	2026-02-04 09:24:10
651	49	laptop-2.webp	1	f	2026-02-04 09:24:10
652	49	laptop-3.webp	2	f	2026-02-04 09:24:10
653	50	laptop-1.webp	0	t	2026-02-04 09:24:10
654	50	laptop-2.webp	1	f	2026-02-04 09:24:10
655	50	laptop-3.webp	2	f	2026-02-04 09:24:10
656	51	watch-1.webp	0	t	2026-02-04 09:24:10
657	51	watch-2.webp	1	f	2026-02-04 09:24:10
658	51	watch-3.webp	2	f	2026-02-04 09:24:10
659	52	laptop-1.webp	0	t	2026-02-04 09:24:10
660	52	laptop-2.webp	1	f	2026-02-04 09:24:10
661	52	laptop-3.webp	2	f	2026-02-04 09:24:10
662	53	laptop-1.webp	0	t	2026-02-04 09:24:10
663	53	laptop-2.webp	1	f	2026-02-04 09:24:10
664	53	laptop-3.webp	2	f	2026-02-04 09:24:10
665	54	laptop-1.webp	0	t	2026-02-04 09:24:10
666	54	laptop-2.webp	1	f	2026-02-04 09:24:10
667	54	laptop-3.webp	2	f	2026-02-04 09:24:10
668	55	laptop-1.webp	0	t	2026-02-04 09:24:10
669	55	laptop-2.webp	1	f	2026-02-04 09:24:10
670	55	laptop-3.webp	2	f	2026-02-04 09:24:10
671	56	laptop-1.webp	0	t	2026-02-04 09:24:10
672	56	laptop-2.webp	1	f	2026-02-04 09:24:10
673	56	laptop-3.webp	2	f	2026-02-04 09:24:10
674	57	laptop-1.webp	0	t	2026-02-04 09:24:10
675	57	laptop-2.webp	1	f	2026-02-04 09:24:10
676	57	laptop-3.webp	2	f	2026-02-04 09:24:10
677	58	laptop-1.webp	0	t	2026-02-04 09:24:10
678	58	laptop-2.webp	1	f	2026-02-04 09:24:10
679	58	laptop-3.webp	2	f	2026-02-04 09:24:10
680	59	laptop-1.webp	0	t	2026-02-04 09:24:10
681	59	laptop-2.webp	1	f	2026-02-04 09:24:10
682	59	laptop-3.webp	2	f	2026-02-04 09:24:10
683	60	laptop-1.webp	0	t	2026-02-04 09:24:10
684	60	laptop-2.webp	1	f	2026-02-04 09:24:10
685	60	laptop-3.webp	2	f	2026-02-04 09:24:10
686	61	laptop-1.webp	0	t	2026-02-04 09:24:10
687	61	laptop-2.webp	1	f	2026-02-04 09:24:10
688	61	laptop-3.webp	2	f	2026-02-04 09:24:10
689	62	laptop-1.webp	0	t	2026-02-04 09:24:10
690	62	laptop-2.webp	1	f	2026-02-04 09:24:10
691	62	laptop-3.webp	2	f	2026-02-04 09:24:10
692	63	laptop-1.webp	0	t	2026-02-04 09:24:10
693	63	laptop-2.webp	1	f	2026-02-04 09:24:10
694	63	laptop-3.webp	2	f	2026-02-04 09:24:10
695	64	laptop-1.webp	0	t	2026-02-04 09:24:10
696	64	laptop-2.webp	1	f	2026-02-04 09:24:10
697	64	laptop-3.webp	2	f	2026-02-04 09:24:10
698	65	laptop-1.webp	0	t	2026-02-04 09:24:10
699	65	laptop-2.webp	1	f	2026-02-04 09:24:10
700	65	laptop-3.webp	2	f	2026-02-04 09:24:10
701	66	laptop-1.webp	0	t	2026-02-04 09:24:10
702	66	laptop-2.webp	1	f	2026-02-04 09:24:10
703	66	laptop-3.webp	2	f	2026-02-04 09:24:10
704	67	laptop-1.webp	0	t	2026-02-04 09:24:10
705	67	laptop-2.webp	1	f	2026-02-04 09:24:10
706	67	laptop-3.webp	2	f	2026-02-04 09:24:10
707	68	laptop-1.webp	0	t	2026-02-04 09:24:10
708	68	laptop-2.webp	1	f	2026-02-04 09:24:10
709	68	laptop-3.webp	2	f	2026-02-04 09:24:10
710	69	laptop-1.webp	0	t	2026-02-04 09:24:10
711	69	laptop-2.webp	1	f	2026-02-04 09:24:10
712	69	laptop-3.webp	2	f	2026-02-04 09:24:10
713	70	laptop-1.webp	0	t	2026-02-04 09:24:10
714	70	laptop-2.webp	1	f	2026-02-04 09:24:10
715	70	laptop-3.webp	2	f	2026-02-04 09:24:10
716	71	watch-1.webp	0	t	2026-02-04 09:24:10
717	71	watch-2.webp	1	f	2026-02-04 09:24:10
718	71	watch-3.webp	2	f	2026-02-04 09:24:10
719	72	laptop-1.webp	0	t	2026-02-04 09:24:10
720	72	laptop-2.webp	1	f	2026-02-04 09:24:10
721	72	laptop-3.webp	2	f	2026-02-04 09:24:10
722	73	laptop-1.webp	0	t	2026-02-04 09:24:10
723	73	laptop-2.webp	1	f	2026-02-04 09:24:10
724	73	laptop-3.webp	2	f	2026-02-04 09:24:10
725	74	laptop-1.webp	0	t	2026-02-04 09:24:10
726	74	laptop-2.webp	1	f	2026-02-04 09:24:10
727	74	laptop-3.webp	2	f	2026-02-04 09:24:10
728	75	laptop-1.webp	0	t	2026-02-04 09:24:10
729	75	laptop-2.webp	1	f	2026-02-04 09:24:10
730	75	laptop-3.webp	2	f	2026-02-04 09:24:10
731	76	laptop-1.webp	0	t	2026-02-04 09:24:10
732	76	laptop-2.webp	1	f	2026-02-04 09:24:10
733	76	laptop-3.webp	2	f	2026-02-04 09:24:10
734	77	laptop-1.webp	0	t	2026-02-04 09:24:10
735	77	laptop-2.webp	1	f	2026-02-04 09:24:10
736	77	laptop-3.webp	2	f	2026-02-04 09:24:10
737	78	laptop-1.webp	0	t	2026-02-04 09:24:10
738	78	laptop-2.webp	1	f	2026-02-04 09:24:10
739	78	laptop-3.webp	2	f	2026-02-04 09:24:10
740	79	laptop-1.webp	0	t	2026-02-04 09:24:10
741	79	laptop-2.webp	1	f	2026-02-04 09:24:10
742	79	laptop-3.webp	2	f	2026-02-04 09:24:10
743	80	laptop-1.webp	0	t	2026-02-04 09:24:10
744	80	laptop-2.webp	1	f	2026-02-04 09:24:10
745	80	laptop-3.webp	2	f	2026-02-04 09:24:10
746	81	laptop-1.webp	0	t	2026-02-04 09:24:10
747	81	laptop-2.webp	1	f	2026-02-04 09:24:10
748	81	laptop-3.webp	2	f	2026-02-04 09:24:10
749	82	laptop-1.webp	0	t	2026-02-04 09:24:10
750	82	laptop-2.webp	1	f	2026-02-04 09:24:10
751	82	laptop-3.webp	2	f	2026-02-04 09:24:10
752	83	laptop-1.webp	0	t	2026-02-04 09:24:10
753	83	laptop-2.webp	1	f	2026-02-04 09:24:10
754	83	laptop-3.webp	2	f	2026-02-04 09:24:10
755	84	laptop-1.webp	0	t	2026-02-04 09:24:10
756	84	laptop-2.webp	1	f	2026-02-04 09:24:10
757	84	laptop-3.webp	2	f	2026-02-04 09:24:10
758	85	laptop-1.webp	0	t	2026-02-04 09:24:10
759	85	laptop-2.webp	1	f	2026-02-04 09:24:10
760	85	laptop-3.webp	2	f	2026-02-04 09:24:10
761	86	laptop-1.webp	0	t	2026-02-04 09:24:10
762	86	laptop-2.webp	1	f	2026-02-04 09:24:10
763	86	laptop-3.webp	2	f	2026-02-04 09:24:10
764	87	laptop-1.webp	0	t	2026-02-04 09:24:10
765	87	laptop-2.webp	1	f	2026-02-04 09:24:10
766	87	laptop-3.webp	2	f	2026-02-04 09:24:10
767	88	laptop-1.webp	0	t	2026-02-04 09:24:10
768	88	laptop-2.webp	1	f	2026-02-04 09:24:10
769	88	laptop-3.webp	2	f	2026-02-04 09:24:10
770	89	laptop-1.webp	0	t	2026-02-04 09:24:10
771	89	laptop-2.webp	1	f	2026-02-04 09:24:10
772	89	laptop-3.webp	2	f	2026-02-04 09:24:10
773	90	laptop-1.webp	0	t	2026-02-04 09:24:10
774	90	laptop-2.webp	1	f	2026-02-04 09:24:10
775	90	laptop-3.webp	2	f	2026-02-04 09:24:10
776	91	watch-1.webp	0	t	2026-02-04 09:24:10
777	91	watch-2.webp	1	f	2026-02-04 09:24:10
778	91	watch-3.webp	2	f	2026-02-04 09:24:10
779	92	laptop-1.webp	0	t	2026-02-04 09:24:10
780	92	laptop-2.webp	1	f	2026-02-04 09:24:10
781	92	laptop-3.webp	2	f	2026-02-04 09:24:10
782	93	laptop-1.webp	0	t	2026-02-04 09:24:10
783	93	laptop-2.webp	1	f	2026-02-04 09:24:10
784	93	laptop-3.webp	2	f	2026-02-04 09:24:10
785	94	laptop-1.webp	0	t	2026-02-04 09:24:10
786	94	laptop-2.webp	1	f	2026-02-04 09:24:10
787	94	laptop-3.webp	2	f	2026-02-04 09:24:10
788	95	laptop-1.webp	0	t	2026-02-04 09:24:10
789	95	laptop-2.webp	1	f	2026-02-04 09:24:10
790	95	laptop-3.webp	2	f	2026-02-04 09:24:10
791	96	laptop-1.webp	0	t	2026-02-04 09:24:10
792	96	laptop-2.webp	1	f	2026-02-04 09:24:10
793	96	laptop-3.webp	2	f	2026-02-04 09:24:10
794	97	laptop-1.webp	0	t	2026-02-04 09:24:10
795	97	laptop-2.webp	1	f	2026-02-04 09:24:10
796	97	laptop-3.webp	2	f	2026-02-04 09:24:10
797	98	laptop-1.webp	0	t	2026-02-04 09:24:10
798	98	laptop-2.webp	1	f	2026-02-04 09:24:10
799	98	laptop-3.webp	2	f	2026-02-04 09:24:10
800	99	laptop-1.webp	0	t	2026-02-04 09:24:10
801	99	laptop-2.webp	1	f	2026-02-04 09:24:10
802	99	laptop-3.webp	2	f	2026-02-04 09:24:10
803	100	laptop-1.webp	0	t	2026-02-04 09:24:10
804	100	laptop-2.webp	1	f	2026-02-04 09:24:10
805	100	laptop-3.webp	2	f	2026-02-04 09:24:10
806	101	tshirt-1.webp	0	t	2026-02-04 09:24:10
807	101	tshirt-2.webp	1	f	2026-02-04 09:24:10
808	101	tshirt-3.webp	2	f	2026-02-04 09:24:10
809	102	tshirt-1.webp	0	t	2026-02-04 09:24:10
810	102	tshirt-2.webp	1	f	2026-02-04 09:24:10
811	102	tshirt-3.webp	2	f	2026-02-04 09:24:10
812	103	tshirt-1.webp	0	t	2026-02-04 09:24:10
813	103	tshirt-2.webp	1	f	2026-02-04 09:24:10
814	103	tshirt-3.webp	2	f	2026-02-04 09:24:10
815	104	tshirt-1.webp	0	t	2026-02-04 09:24:10
816	104	tshirt-2.webp	1	f	2026-02-04 09:24:10
817	104	tshirt-3.webp	2	f	2026-02-04 09:24:10
818	105	tshirt-1.webp	0	t	2026-02-04 09:24:10
819	105	tshirt-2.webp	1	f	2026-02-04 09:24:10
820	105	tshirt-3.webp	2	f	2026-02-04 09:24:10
821	106	tshirt-1.webp	0	t	2026-02-04 09:24:10
822	106	tshirt-2.webp	1	f	2026-02-04 09:24:10
823	106	tshirt-3.webp	2	f	2026-02-04 09:24:10
824	107	tshirt-1.webp	0	t	2026-02-04 09:24:10
825	107	tshirt-2.webp	1	f	2026-02-04 09:24:10
826	107	tshirt-3.webp	2	f	2026-02-04 09:24:10
827	108	tshirt-1.webp	0	t	2026-02-04 09:24:10
828	108	tshirt-2.webp	1	f	2026-02-04 09:24:10
829	108	tshirt-3.webp	2	f	2026-02-04 09:24:10
830	109	tshirt-1.webp	0	t	2026-02-04 09:24:10
831	109	tshirt-2.webp	1	f	2026-02-04 09:24:10
832	109	tshirt-3.webp	2	f	2026-02-04 09:24:10
833	110	tshirt-1.webp	0	t	2026-02-04 09:24:10
834	110	tshirt-2.webp	1	f	2026-02-04 09:24:10
835	110	tshirt-3.webp	2	f	2026-02-04 09:24:10
836	111	tshirt-1.webp	0	t	2026-02-04 09:24:10
837	111	tshirt-2.webp	1	f	2026-02-04 09:24:10
838	111	tshirt-3.webp	2	f	2026-02-04 09:24:10
839	112	shoes-1.webp	0	t	2026-02-04 09:24:10
840	112	shoes-2.webp	1	f	2026-02-04 09:24:10
841	112	shoes-3.webp	2	f	2026-02-04 09:24:10
842	113	tshirt-1.webp	0	t	2026-02-04 09:24:10
843	113	tshirt-2.webp	1	f	2026-02-04 09:24:10
844	113	tshirt-3.webp	2	f	2026-02-04 09:24:10
845	114	tshirt-1.webp	0	t	2026-02-04 09:24:10
846	114	tshirt-2.webp	1	f	2026-02-04 09:24:10
847	114	tshirt-3.webp	2	f	2026-02-04 09:24:10
848	115	tshirt-1.webp	0	t	2026-02-04 09:24:10
849	115	tshirt-2.webp	1	f	2026-02-04 09:24:10
850	115	tshirt-3.webp	2	f	2026-02-04 09:24:10
851	116	tshirt-1.webp	0	t	2026-02-04 09:24:10
852	116	tshirt-2.webp	1	f	2026-02-04 09:24:10
853	116	tshirt-3.webp	2	f	2026-02-04 09:24:10
854	117	tshirt-1.webp	0	t	2026-02-04 09:24:10
855	117	tshirt-2.webp	1	f	2026-02-04 09:24:10
856	117	tshirt-3.webp	2	f	2026-02-04 09:24:10
857	118	tshirt-1.webp	0	t	2026-02-04 09:24:10
858	118	tshirt-2.webp	1	f	2026-02-04 09:24:10
859	118	tshirt-3.webp	2	f	2026-02-04 09:24:10
860	119	tshirt-1.webp	0	t	2026-02-04 09:24:10
861	119	tshirt-2.webp	1	f	2026-02-04 09:24:10
862	119	tshirt-3.webp	2	f	2026-02-04 09:24:10
863	120	tshirt-1.webp	0	t	2026-02-04 09:24:10
864	120	tshirt-2.webp	1	f	2026-02-04 09:24:10
865	120	tshirt-3.webp	2	f	2026-02-04 09:24:10
866	121	tshirt-1.webp	0	t	2026-02-04 09:24:10
867	121	tshirt-2.webp	1	f	2026-02-04 09:24:10
868	121	tshirt-3.webp	2	f	2026-02-04 09:24:10
869	122	tshirt-1.webp	0	t	2026-02-04 09:24:10
870	122	tshirt-2.webp	1	f	2026-02-04 09:24:10
871	122	tshirt-3.webp	2	f	2026-02-04 09:24:10
872	123	tshirt-1.webp	0	t	2026-02-04 09:24:10
873	123	tshirt-2.webp	1	f	2026-02-04 09:24:10
874	123	tshirt-3.webp	2	f	2026-02-04 09:24:10
875	124	tshirt-1.webp	0	t	2026-02-04 09:24:10
876	124	tshirt-2.webp	1	f	2026-02-04 09:24:10
877	124	tshirt-3.webp	2	f	2026-02-04 09:24:10
878	125	tshirt-1.webp	0	t	2026-02-04 09:24:10
879	125	tshirt-2.webp	1	f	2026-02-04 09:24:10
880	125	tshirt-3.webp	2	f	2026-02-04 09:24:10
881	126	tshirt-1.webp	0	t	2026-02-04 09:24:10
882	126	tshirt-2.webp	1	f	2026-02-04 09:24:10
883	126	tshirt-3.webp	2	f	2026-02-04 09:24:10
884	127	tshirt-1.webp	0	t	2026-02-04 09:24:10
885	127	tshirt-2.webp	1	f	2026-02-04 09:24:10
886	127	tshirt-3.webp	2	f	2026-02-04 09:24:10
887	128	tshirt-1.webp	0	t	2026-02-04 09:24:10
888	128	tshirt-2.webp	1	f	2026-02-04 09:24:10
889	128	tshirt-3.webp	2	f	2026-02-04 09:24:10
890	129	tshirt-1.webp	0	t	2026-02-04 09:24:10
891	129	tshirt-2.webp	1	f	2026-02-04 09:24:10
892	129	tshirt-3.webp	2	f	2026-02-04 09:24:10
893	130	tshirt-1.webp	0	t	2026-02-04 09:24:10
894	130	tshirt-2.webp	1	f	2026-02-04 09:24:10
895	130	tshirt-3.webp	2	f	2026-02-04 09:24:10
896	131	tshirt-1.webp	0	t	2026-02-04 09:24:10
897	131	tshirt-2.webp	1	f	2026-02-04 09:24:10
898	131	tshirt-3.webp	2	f	2026-02-04 09:24:10
899	132	shoes-1.webp	0	t	2026-02-04 09:24:10
900	132	shoes-2.webp	1	f	2026-02-04 09:24:10
901	132	shoes-3.webp	2	f	2026-02-04 09:24:10
902	133	tshirt-1.webp	0	t	2026-02-04 09:24:10
903	133	tshirt-2.webp	1	f	2026-02-04 09:24:10
904	133	tshirt-3.webp	2	f	2026-02-04 09:24:10
905	134	tshirt-1.webp	0	t	2026-02-04 09:24:10
906	134	tshirt-2.webp	1	f	2026-02-04 09:24:10
907	134	tshirt-3.webp	2	f	2026-02-04 09:24:10
908	135	tshirt-1.webp	0	t	2026-02-04 09:24:10
909	135	tshirt-2.webp	1	f	2026-02-04 09:24:10
910	135	tshirt-3.webp	2	f	2026-02-04 09:24:10
911	136	tshirt-1.webp	0	t	2026-02-04 09:24:10
912	136	tshirt-2.webp	1	f	2026-02-04 09:24:10
913	136	tshirt-3.webp	2	f	2026-02-04 09:24:10
914	137	tshirt-1.webp	0	t	2026-02-04 09:24:10
915	137	tshirt-2.webp	1	f	2026-02-04 09:24:10
916	137	tshirt-3.webp	2	f	2026-02-04 09:24:10
917	138	tshirt-1.webp	0	t	2026-02-04 09:24:10
918	138	tshirt-2.webp	1	f	2026-02-04 09:24:10
919	138	tshirt-3.webp	2	f	2026-02-04 09:24:10
920	139	tshirt-1.webp	0	t	2026-02-04 09:24:10
921	139	tshirt-2.webp	1	f	2026-02-04 09:24:10
922	139	tshirt-3.webp	2	f	2026-02-04 09:24:10
923	140	tshirt-1.webp	0	t	2026-02-04 09:24:10
924	140	tshirt-2.webp	1	f	2026-02-04 09:24:10
925	140	tshirt-3.webp	2	f	2026-02-04 09:24:10
926	141	tshirt-1.webp	0	t	2026-02-04 09:24:10
927	141	tshirt-2.webp	1	f	2026-02-04 09:24:10
928	141	tshirt-3.webp	2	f	2026-02-04 09:24:10
929	142	tshirt-1.webp	0	t	2026-02-04 09:24:10
930	142	tshirt-2.webp	1	f	2026-02-04 09:24:10
931	142	tshirt-3.webp	2	f	2026-02-04 09:24:10
932	143	tshirt-1.webp	0	t	2026-02-04 09:24:10
933	143	tshirt-2.webp	1	f	2026-02-04 09:24:10
934	143	tshirt-3.webp	2	f	2026-02-04 09:24:10
935	144	tshirt-1.webp	0	t	2026-02-04 09:24:10
936	144	tshirt-2.webp	1	f	2026-02-04 09:24:10
937	144	tshirt-3.webp	2	f	2026-02-04 09:24:10
938	145	tshirt-1.webp	0	t	2026-02-04 09:24:10
939	145	tshirt-2.webp	1	f	2026-02-04 09:24:10
940	145	tshirt-3.webp	2	f	2026-02-04 09:24:10
941	146	tshirt-1.webp	0	t	2026-02-04 09:24:10
942	146	tshirt-2.webp	1	f	2026-02-04 09:24:10
943	146	tshirt-3.webp	2	f	2026-02-04 09:24:10
944	147	tshirt-1.webp	0	t	2026-02-04 09:24:10
945	147	tshirt-2.webp	1	f	2026-02-04 09:24:10
946	147	tshirt-3.webp	2	f	2026-02-04 09:24:10
947	148	tshirt-1.webp	0	t	2026-02-04 09:24:10
948	148	tshirt-2.webp	1	f	2026-02-04 09:24:10
949	148	tshirt-3.webp	2	f	2026-02-04 09:24:10
950	149	tshirt-1.webp	0	t	2026-02-04 09:24:10
951	149	tshirt-2.webp	1	f	2026-02-04 09:24:10
952	149	tshirt-3.webp	2	f	2026-02-04 09:24:10
953	150	tshirt-1.webp	0	t	2026-02-04 09:24:10
954	150	tshirt-2.webp	1	f	2026-02-04 09:24:10
955	150	tshirt-3.webp	2	f	2026-02-04 09:24:10
956	151	tshirt-1.webp	0	t	2026-02-04 09:24:10
957	151	tshirt-2.webp	1	f	2026-02-04 09:24:10
958	151	tshirt-3.webp	2	f	2026-02-04 09:24:10
959	152	shoes-1.webp	0	t	2026-02-04 09:24:10
960	152	shoes-2.webp	1	f	2026-02-04 09:24:10
961	152	shoes-3.webp	2	f	2026-02-04 09:24:10
962	153	tshirt-1.webp	0	t	2026-02-04 09:24:10
963	153	tshirt-2.webp	1	f	2026-02-04 09:24:10
964	153	tshirt-3.webp	2	f	2026-02-04 09:24:10
965	154	tshirt-1.webp	0	t	2026-02-04 09:24:10
966	154	tshirt-2.webp	1	f	2026-02-04 09:24:10
967	154	tshirt-3.webp	2	f	2026-02-04 09:24:10
968	155	tshirt-1.webp	0	t	2026-02-04 09:24:10
969	155	tshirt-2.webp	1	f	2026-02-04 09:24:10
970	155	tshirt-3.webp	2	f	2026-02-04 09:24:10
971	156	tshirt-1.webp	0	t	2026-02-04 09:24:10
972	156	tshirt-2.webp	1	f	2026-02-04 09:24:10
973	156	tshirt-3.webp	2	f	2026-02-04 09:24:10
974	157	tshirt-1.webp	0	t	2026-02-04 09:24:10
975	157	tshirt-2.webp	1	f	2026-02-04 09:24:10
976	157	tshirt-3.webp	2	f	2026-02-04 09:24:10
977	158	tshirt-1.webp	0	t	2026-02-04 09:24:10
978	158	tshirt-2.webp	1	f	2026-02-04 09:24:10
979	158	tshirt-3.webp	2	f	2026-02-04 09:24:10
980	159	tshirt-1.webp	0	t	2026-02-04 09:24:10
981	159	tshirt-2.webp	1	f	2026-02-04 09:24:10
982	159	tshirt-3.webp	2	f	2026-02-04 09:24:10
983	160	tshirt-1.webp	0	t	2026-02-04 09:24:10
984	160	tshirt-2.webp	1	f	2026-02-04 09:24:10
985	160	tshirt-3.webp	2	f	2026-02-04 09:24:10
986	161	tshirt-1.webp	0	t	2026-02-04 09:24:10
987	161	tshirt-2.webp	1	f	2026-02-04 09:24:10
988	161	tshirt-3.webp	2	f	2026-02-04 09:24:10
989	162	tshirt-1.webp	0	t	2026-02-04 09:24:10
990	162	tshirt-2.webp	1	f	2026-02-04 09:24:10
991	162	tshirt-3.webp	2	f	2026-02-04 09:24:10
992	163	tshirt-1.webp	0	t	2026-02-04 09:24:10
993	163	tshirt-2.webp	1	f	2026-02-04 09:24:10
994	163	tshirt-3.webp	2	f	2026-02-04 09:24:10
995	164	tshirt-1.webp	0	t	2026-02-04 09:24:10
996	164	tshirt-2.webp	1	f	2026-02-04 09:24:10
997	164	tshirt-3.webp	2	f	2026-02-04 09:24:10
998	165	tshirt-1.webp	0	t	2026-02-04 09:24:10
999	165	tshirt-2.webp	1	f	2026-02-04 09:24:10
1000	165	tshirt-3.webp	2	f	2026-02-04 09:24:10
1001	166	tshirt-1.webp	0	t	2026-02-04 09:24:10
1002	166	tshirt-2.webp	1	f	2026-02-04 09:24:10
1003	166	tshirt-3.webp	2	f	2026-02-04 09:24:10
1004	167	tshirt-1.webp	0	t	2026-02-04 09:24:10
1005	167	tshirt-2.webp	1	f	2026-02-04 09:24:10
1006	167	tshirt-3.webp	2	f	2026-02-04 09:24:10
1007	168	tshirt-1.webp	0	t	2026-02-04 09:24:10
1008	168	tshirt-2.webp	1	f	2026-02-04 09:24:10
1009	168	tshirt-3.webp	2	f	2026-02-04 09:24:10
1010	169	tshirt-1.webp	0	t	2026-02-04 09:24:10
1011	169	tshirt-2.webp	1	f	2026-02-04 09:24:10
1012	169	tshirt-3.webp	2	f	2026-02-04 09:24:10
1013	170	tshirt-1.webp	0	t	2026-02-04 09:24:10
1014	170	tshirt-2.webp	1	f	2026-02-04 09:24:10
1015	170	tshirt-3.webp	2	f	2026-02-04 09:24:10
1016	171	tshirt-1.webp	0	t	2026-02-04 09:24:10
1017	171	tshirt-2.webp	1	f	2026-02-04 09:24:10
1018	171	tshirt-3.webp	2	f	2026-02-04 09:24:10
1019	172	shoes-1.webp	0	t	2026-02-04 09:24:10
1020	172	shoes-2.webp	1	f	2026-02-04 09:24:10
1021	172	shoes-3.webp	2	f	2026-02-04 09:24:10
1022	173	tshirt-1.webp	0	t	2026-02-04 09:24:10
1023	173	tshirt-2.webp	1	f	2026-02-04 09:24:10
1024	173	tshirt-3.webp	2	f	2026-02-04 09:24:10
1025	174	tshirt-1.webp	0	t	2026-02-04 09:24:10
1026	174	tshirt-2.webp	1	f	2026-02-04 09:24:10
1027	174	tshirt-3.webp	2	f	2026-02-04 09:24:10
1028	175	tshirt-1.webp	0	t	2026-02-04 09:24:10
1029	175	tshirt-2.webp	1	f	2026-02-04 09:24:10
1030	175	tshirt-3.webp	2	f	2026-02-04 09:24:10
1031	176	tshirt-1.webp	0	t	2026-02-04 09:24:10
1032	176	tshirt-2.webp	1	f	2026-02-04 09:24:10
1033	176	tshirt-3.webp	2	f	2026-02-04 09:24:10
1034	177	tshirt-1.webp	0	t	2026-02-04 09:24:10
1035	177	tshirt-2.webp	1	f	2026-02-04 09:24:10
1036	177	tshirt-3.webp	2	f	2026-02-04 09:24:10
1037	178	tshirt-1.webp	0	t	2026-02-04 09:24:10
1038	178	tshirt-2.webp	1	f	2026-02-04 09:24:10
1039	178	tshirt-3.webp	2	f	2026-02-04 09:24:10
1040	179	tshirt-1.webp	0	t	2026-02-04 09:24:10
1041	179	tshirt-2.webp	1	f	2026-02-04 09:24:10
1042	179	tshirt-3.webp	2	f	2026-02-04 09:24:10
1043	180	tshirt-1.webp	0	t	2026-02-04 09:24:10
1044	180	tshirt-2.webp	1	f	2026-02-04 09:24:10
1045	180	tshirt-3.webp	2	f	2026-02-04 09:24:10
1046	181	tshirt-1.webp	0	t	2026-02-04 09:24:10
1047	181	tshirt-2.webp	1	f	2026-02-04 09:24:10
1048	181	tshirt-3.webp	2	f	2026-02-04 09:24:10
1049	182	tshirt-1.webp	0	t	2026-02-04 09:24:10
1050	182	tshirt-2.webp	1	f	2026-02-04 09:24:10
1051	182	tshirt-3.webp	2	f	2026-02-04 09:24:10
1052	183	tshirt-1.webp	0	t	2026-02-04 09:24:10
1053	183	tshirt-2.webp	1	f	2026-02-04 09:24:10
1054	183	tshirt-3.webp	2	f	2026-02-04 09:24:10
1055	184	tshirt-1.webp	0	t	2026-02-04 09:24:10
1056	184	tshirt-2.webp	1	f	2026-02-04 09:24:10
1057	184	tshirt-3.webp	2	f	2026-02-04 09:24:10
1058	185	tshirt-1.webp	0	t	2026-02-04 09:24:10
1059	185	tshirt-2.webp	1	f	2026-02-04 09:24:10
1060	185	tshirt-3.webp	2	f	2026-02-04 09:24:10
1061	186	tshirt-1.webp	0	t	2026-02-04 09:24:10
1062	186	tshirt-2.webp	1	f	2026-02-04 09:24:10
1063	186	tshirt-3.webp	2	f	2026-02-04 09:24:10
1064	187	tshirt-1.webp	0	t	2026-02-04 09:24:10
1065	187	tshirt-2.webp	1	f	2026-02-04 09:24:10
1066	187	tshirt-3.webp	2	f	2026-02-04 09:24:10
1067	188	tshirt-1.webp	0	t	2026-02-04 09:24:10
1068	188	tshirt-2.webp	1	f	2026-02-04 09:24:10
1069	188	tshirt-3.webp	2	f	2026-02-04 09:24:10
1070	189	tshirt-1.webp	0	t	2026-02-04 09:24:10
1071	189	tshirt-2.webp	1	f	2026-02-04 09:24:10
1072	189	tshirt-3.webp	2	f	2026-02-04 09:24:10
1073	190	tshirt-1.webp	0	t	2026-02-04 09:24:10
1074	190	tshirt-2.webp	1	f	2026-02-04 09:24:10
1075	190	tshirt-3.webp	2	f	2026-02-04 09:24:10
1076	191	tshirt-1.webp	0	t	2026-02-04 09:24:10
1077	191	tshirt-2.webp	1	f	2026-02-04 09:24:10
1078	191	tshirt-3.webp	2	f	2026-02-04 09:24:10
1079	192	shoes-1.webp	0	t	2026-02-04 09:24:10
1080	192	shoes-2.webp	1	f	2026-02-04 09:24:10
1081	192	shoes-3.webp	2	f	2026-02-04 09:24:10
1082	193	tshirt-1.webp	0	t	2026-02-04 09:24:10
1083	193	tshirt-2.webp	1	f	2026-02-04 09:24:10
1084	193	tshirt-3.webp	2	f	2026-02-04 09:24:10
1085	194	tshirt-1.webp	0	t	2026-02-04 09:24:10
1086	194	tshirt-2.webp	1	f	2026-02-04 09:24:10
1087	194	tshirt-3.webp	2	f	2026-02-04 09:24:10
1088	195	tshirt-1.webp	0	t	2026-02-04 09:24:10
1089	195	tshirt-2.webp	1	f	2026-02-04 09:24:10
1090	195	tshirt-3.webp	2	f	2026-02-04 09:24:10
1091	196	tshirt-1.webp	0	t	2026-02-04 09:24:10
1092	196	tshirt-2.webp	1	f	2026-02-04 09:24:10
1093	196	tshirt-3.webp	2	f	2026-02-04 09:24:10
1094	197	tshirt-1.webp	0	t	2026-02-04 09:24:10
1095	197	tshirt-2.webp	1	f	2026-02-04 09:24:10
1096	197	tshirt-3.webp	2	f	2026-02-04 09:24:10
1097	198	tshirt-1.webp	0	t	2026-02-04 09:24:10
1098	198	tshirt-2.webp	1	f	2026-02-04 09:24:10
1099	198	tshirt-3.webp	2	f	2026-02-04 09:24:10
1100	199	tshirt-1.webp	0	t	2026-02-04 09:24:10
1101	199	tshirt-2.webp	1	f	2026-02-04 09:24:10
1102	199	tshirt-3.webp	2	f	2026-02-04 09:24:10
1103	200	tshirt-1.webp	0	t	2026-02-04 09:24:10
1104	200	tshirt-2.webp	1	f	2026-02-04 09:24:10
1105	200	tshirt-3.webp	2	f	2026-02-04 09:24:10
1106	201	sofa-1.webp	0	t	2026-02-04 09:24:10
1107	201	sofa-2.webp	1	f	2026-02-04 09:24:10
1108	201	sofa-3.webp	2	f	2026-02-04 09:24:10
1109	202	sofa-1.webp	0	t	2026-02-04 09:24:10
1110	202	sofa-2.webp	1	f	2026-02-04 09:24:10
1111	202	sofa-3.webp	2	f	2026-02-04 09:24:10
1112	203	sofa-1.webp	0	t	2026-02-04 09:24:10
1113	203	sofa-2.webp	1	f	2026-02-04 09:24:10
1114	203	sofa-3.webp	2	f	2026-02-04 09:24:10
1115	204	sofa-1.webp	0	t	2026-02-04 09:24:10
1116	204	sofa-2.webp	1	f	2026-02-04 09:24:10
1117	204	sofa-3.webp	2	f	2026-02-04 09:24:10
1118	205	sofa-1.webp	0	t	2026-02-04 09:24:10
1119	205	sofa-2.webp	1	f	2026-02-04 09:24:10
1120	205	sofa-3.webp	2	f	2026-02-04 09:24:10
1121	206	sofa-1.webp	0	t	2026-02-04 09:24:10
1122	206	sofa-2.webp	1	f	2026-02-04 09:24:10
1123	206	sofa-3.webp	2	f	2026-02-04 09:24:10
1124	207	sofa-1.webp	0	t	2026-02-04 09:24:10
1125	207	sofa-2.webp	1	f	2026-02-04 09:24:10
1126	207	sofa-3.webp	2	f	2026-02-04 09:24:10
1127	208	sofa-1.webp	0	t	2026-02-04 09:24:10
1128	208	sofa-2.webp	1	f	2026-02-04 09:24:10
1129	208	sofa-3.webp	2	f	2026-02-04 09:24:10
1130	209	sofa-1.webp	0	t	2026-02-04 09:24:10
1131	209	sofa-2.webp	1	f	2026-02-04 09:24:10
1132	209	sofa-3.webp	2	f	2026-02-04 09:24:10
1133	210	sofa-1.webp	0	t	2026-02-04 09:24:10
1134	210	sofa-2.webp	1	f	2026-02-04 09:24:10
1135	210	sofa-3.webp	2	f	2026-02-04 09:24:10
1136	211	sofa-1.webp	0	t	2026-02-04 09:24:10
1137	211	sofa-2.webp	1	f	2026-02-04 09:24:10
1138	211	sofa-3.webp	2	f	2026-02-04 09:24:10
1139	212	sofa-1.webp	0	t	2026-02-04 09:24:10
1140	212	sofa-2.webp	1	f	2026-02-04 09:24:10
1141	212	sofa-3.webp	2	f	2026-02-04 09:24:10
1142	213	sofa-1.webp	0	t	2026-02-04 09:24:10
1143	213	sofa-2.webp	1	f	2026-02-04 09:24:10
1144	213	sofa-3.webp	2	f	2026-02-04 09:24:10
1145	214	sofa-1.webp	0	t	2026-02-04 09:24:10
1146	214	sofa-2.webp	1	f	2026-02-04 09:24:10
1147	214	sofa-3.webp	2	f	2026-02-04 09:24:10
1148	215	sofa-1.webp	0	t	2026-02-04 09:24:10
1149	215	sofa-2.webp	1	f	2026-02-04 09:24:10
1150	215	sofa-3.webp	2	f	2026-02-04 09:24:10
1151	216	sofa-1.webp	0	t	2026-02-04 09:24:10
1152	216	sofa-2.webp	1	f	2026-02-04 09:24:10
1153	216	sofa-3.webp	2	f	2026-02-04 09:24:10
1154	217	sofa-1.webp	0	t	2026-02-04 09:24:10
1155	217	sofa-2.webp	1	f	2026-02-04 09:24:10
1156	217	sofa-3.webp	2	f	2026-02-04 09:24:10
1157	218	sofa-1.webp	0	t	2026-02-04 09:24:10
1158	218	sofa-2.webp	1	f	2026-02-04 09:24:10
1159	218	sofa-3.webp	2	f	2026-02-04 09:24:10
1160	219	sofa-1.webp	0	t	2026-02-04 09:24:10
1161	219	sofa-2.webp	1	f	2026-02-04 09:24:10
1162	219	sofa-3.webp	2	f	2026-02-04 09:24:10
1163	220	sofa-1.webp	0	t	2026-02-04 09:24:10
1164	220	sofa-2.webp	1	f	2026-02-04 09:24:10
1165	220	sofa-3.webp	2	f	2026-02-04 09:24:10
1166	221	sofa-1.webp	0	t	2026-02-04 09:24:10
1167	221	sofa-2.webp	1	f	2026-02-04 09:24:10
1168	221	sofa-3.webp	2	f	2026-02-04 09:24:10
1169	222	sofa-1.webp	0	t	2026-02-04 09:24:10
1170	222	sofa-2.webp	1	f	2026-02-04 09:24:10
1171	222	sofa-3.webp	2	f	2026-02-04 09:24:10
1172	223	sofa-1.webp	0	t	2026-02-04 09:24:10
1173	223	sofa-2.webp	1	f	2026-02-04 09:24:10
1174	223	sofa-3.webp	2	f	2026-02-04 09:24:10
1175	224	sofa-1.webp	0	t	2026-02-04 09:24:10
1176	224	sofa-2.webp	1	f	2026-02-04 09:24:10
1177	224	sofa-3.webp	2	f	2026-02-04 09:24:10
1178	225	sofa-1.webp	0	t	2026-02-04 09:24:10
1179	225	sofa-2.webp	1	f	2026-02-04 09:24:10
1180	225	sofa-3.webp	2	f	2026-02-04 09:24:10
1181	226	sofa-1.webp	0	t	2026-02-04 09:24:10
1182	226	sofa-2.webp	1	f	2026-02-04 09:24:10
1183	226	sofa-3.webp	2	f	2026-02-04 09:24:10
1184	227	sofa-1.webp	0	t	2026-02-04 09:24:10
1185	227	sofa-2.webp	1	f	2026-02-04 09:24:10
1186	227	sofa-3.webp	2	f	2026-02-04 09:24:10
1187	228	sofa-1.webp	0	t	2026-02-04 09:24:10
1188	228	sofa-2.webp	1	f	2026-02-04 09:24:10
1189	228	sofa-3.webp	2	f	2026-02-04 09:24:10
1190	229	sofa-1.webp	0	t	2026-02-04 09:24:10
1191	229	sofa-2.webp	1	f	2026-02-04 09:24:10
1192	229	sofa-3.webp	2	f	2026-02-04 09:24:10
1193	230	sofa-1.webp	0	t	2026-02-04 09:24:10
1194	230	sofa-2.webp	1	f	2026-02-04 09:24:10
1195	230	sofa-3.webp	2	f	2026-02-04 09:24:10
1196	231	sofa-1.webp	0	t	2026-02-04 09:24:10
1197	231	sofa-2.webp	1	f	2026-02-04 09:24:10
1198	231	sofa-3.webp	2	f	2026-02-04 09:24:10
1199	232	sofa-1.webp	0	t	2026-02-04 09:24:10
1200	232	sofa-2.webp	1	f	2026-02-04 09:24:10
1201	232	sofa-3.webp	2	f	2026-02-04 09:24:10
1202	233	sofa-1.webp	0	t	2026-02-04 09:24:10
1203	233	sofa-2.webp	1	f	2026-02-04 09:24:10
1204	233	sofa-3.webp	2	f	2026-02-04 09:24:10
1205	234	sofa-1.webp	0	t	2026-02-04 09:24:10
1206	234	sofa-2.webp	1	f	2026-02-04 09:24:10
1207	234	sofa-3.webp	2	f	2026-02-04 09:24:10
1208	235	sofa-1.webp	0	t	2026-02-04 09:24:10
1209	235	sofa-2.webp	1	f	2026-02-04 09:24:10
1210	235	sofa-3.webp	2	f	2026-02-04 09:24:10
1211	236	sofa-1.webp	0	t	2026-02-04 09:24:10
1212	236	sofa-2.webp	1	f	2026-02-04 09:24:10
1213	236	sofa-3.webp	2	f	2026-02-04 09:24:10
1214	237	sofa-1.webp	0	t	2026-02-04 09:24:10
1215	237	sofa-2.webp	1	f	2026-02-04 09:24:10
1216	237	sofa-3.webp	2	f	2026-02-04 09:24:10
1217	238	sofa-1.webp	0	t	2026-02-04 09:24:10
1218	238	sofa-2.webp	1	f	2026-02-04 09:24:10
1219	238	sofa-3.webp	2	f	2026-02-04 09:24:10
1220	239	sofa-1.webp	0	t	2026-02-04 09:24:10
1221	239	sofa-2.webp	1	f	2026-02-04 09:24:10
1222	239	sofa-3.webp	2	f	2026-02-04 09:24:10
1223	240	sofa-1.webp	0	t	2026-02-04 09:24:10
1224	240	sofa-2.webp	1	f	2026-02-04 09:24:10
1225	240	sofa-3.webp	2	f	2026-02-04 09:24:10
1226	241	sofa-1.webp	0	t	2026-02-04 09:24:10
1227	241	sofa-2.webp	1	f	2026-02-04 09:24:10
1228	241	sofa-3.webp	2	f	2026-02-04 09:24:10
1229	242	sofa-1.webp	0	t	2026-02-04 09:24:10
1230	242	sofa-2.webp	1	f	2026-02-04 09:24:10
1231	242	sofa-3.webp	2	f	2026-02-04 09:24:10
1232	243	sofa-1.webp	0	t	2026-02-04 09:24:10
1233	243	sofa-2.webp	1	f	2026-02-04 09:24:10
1234	243	sofa-3.webp	2	f	2026-02-04 09:24:10
1235	244	sofa-1.webp	0	t	2026-02-04 09:24:10
1236	244	sofa-2.webp	1	f	2026-02-04 09:24:10
1237	244	sofa-3.webp	2	f	2026-02-04 09:24:10
1238	245	sofa-1.webp	0	t	2026-02-04 09:24:10
1239	245	sofa-2.webp	1	f	2026-02-04 09:24:10
1240	245	sofa-3.webp	2	f	2026-02-04 09:24:10
1241	246	sofa-1.webp	0	t	2026-02-04 09:24:10
1242	246	sofa-2.webp	1	f	2026-02-04 09:24:10
1243	246	sofa-3.webp	2	f	2026-02-04 09:24:10
1244	247	sofa-1.webp	0	t	2026-02-04 09:24:10
1245	247	sofa-2.webp	1	f	2026-02-04 09:24:10
1246	247	sofa-3.webp	2	f	2026-02-04 09:24:10
1247	248	sofa-1.webp	0	t	2026-02-04 09:24:10
1248	248	sofa-2.webp	1	f	2026-02-04 09:24:10
1249	248	sofa-3.webp	2	f	2026-02-04 09:24:10
1250	249	sofa-1.webp	0	t	2026-02-04 09:24:10
1251	249	sofa-2.webp	1	f	2026-02-04 09:24:10
1252	249	sofa-3.webp	2	f	2026-02-04 09:24:10
1253	250	sofa-1.webp	0	t	2026-02-04 09:24:10
1254	250	sofa-2.webp	1	f	2026-02-04 09:24:10
1255	250	sofa-3.webp	2	f	2026-02-04 09:24:10
1256	251	sofa-1.webp	0	t	2026-02-04 09:24:10
1257	251	sofa-2.webp	1	f	2026-02-04 09:24:10
1258	251	sofa-3.webp	2	f	2026-02-04 09:24:10
1259	252	sofa-1.webp	0	t	2026-02-04 09:24:10
1260	252	sofa-2.webp	1	f	2026-02-04 09:24:10
1261	252	sofa-3.webp	2	f	2026-02-04 09:24:10
1262	253	sofa-1.webp	0	t	2026-02-04 09:24:10
1263	253	sofa-2.webp	1	f	2026-02-04 09:24:10
1264	253	sofa-3.webp	2	f	2026-02-04 09:24:10
1265	254	sofa-1.webp	0	t	2026-02-04 09:24:10
1266	254	sofa-2.webp	1	f	2026-02-04 09:24:10
1267	254	sofa-3.webp	2	f	2026-02-04 09:24:10
1268	255	sofa-1.webp	0	t	2026-02-04 09:24:10
1269	255	sofa-2.webp	1	f	2026-02-04 09:24:10
1270	255	sofa-3.webp	2	f	2026-02-04 09:24:10
1271	256	sofa-1.webp	0	t	2026-02-04 09:24:10
1272	256	sofa-2.webp	1	f	2026-02-04 09:24:10
1273	256	sofa-3.webp	2	f	2026-02-04 09:24:10
1274	257	sofa-1.webp	0	t	2026-02-04 09:24:10
1275	257	sofa-2.webp	1	f	2026-02-04 09:24:10
1276	257	sofa-3.webp	2	f	2026-02-04 09:24:10
1277	258	sofa-1.webp	0	t	2026-02-04 09:24:10
1278	258	sofa-2.webp	1	f	2026-02-04 09:24:10
1279	258	sofa-3.webp	2	f	2026-02-04 09:24:10
1280	259	sofa-1.webp	0	t	2026-02-04 09:24:10
1281	259	sofa-2.webp	1	f	2026-02-04 09:24:10
1282	259	sofa-3.webp	2	f	2026-02-04 09:24:10
1283	260	sofa-1.webp	0	t	2026-02-04 09:24:10
1284	260	sofa-2.webp	1	f	2026-02-04 09:24:10
1285	260	sofa-3.webp	2	f	2026-02-04 09:24:10
1286	261	sofa-1.webp	0	t	2026-02-04 09:24:10
1287	261	sofa-2.webp	1	f	2026-02-04 09:24:10
1288	261	sofa-3.webp	2	f	2026-02-04 09:24:10
1289	262	sofa-1.webp	0	t	2026-02-04 09:24:10
1290	262	sofa-2.webp	1	f	2026-02-04 09:24:10
1291	262	sofa-3.webp	2	f	2026-02-04 09:24:10
1292	263	sofa-1.webp	0	t	2026-02-04 09:24:10
1293	263	sofa-2.webp	1	f	2026-02-04 09:24:10
1294	263	sofa-3.webp	2	f	2026-02-04 09:24:10
1295	264	sofa-1.webp	0	t	2026-02-04 09:24:10
1296	264	sofa-2.webp	1	f	2026-02-04 09:24:10
1297	264	sofa-3.webp	2	f	2026-02-04 09:24:10
1298	265	sofa-1.webp	0	t	2026-02-04 09:24:10
1299	265	sofa-2.webp	1	f	2026-02-04 09:24:10
1300	265	sofa-3.webp	2	f	2026-02-04 09:24:10
1301	266	sofa-1.webp	0	t	2026-02-04 09:24:10
1302	266	sofa-2.webp	1	f	2026-02-04 09:24:10
1303	266	sofa-3.webp	2	f	2026-02-04 09:24:10
1304	267	sofa-1.webp	0	t	2026-02-04 09:24:10
1305	267	sofa-2.webp	1	f	2026-02-04 09:24:10
1306	267	sofa-3.webp	2	f	2026-02-04 09:24:10
1307	268	sofa-1.webp	0	t	2026-02-04 09:24:10
1308	268	sofa-2.webp	1	f	2026-02-04 09:24:10
1309	268	sofa-3.webp	2	f	2026-02-04 09:24:10
1310	269	sofa-1.webp	0	t	2026-02-04 09:24:10
1311	269	sofa-2.webp	1	f	2026-02-04 09:24:10
1312	269	sofa-3.webp	2	f	2026-02-04 09:24:10
1313	270	sofa-1.webp	0	t	2026-02-04 09:24:10
1314	270	sofa-2.webp	1	f	2026-02-04 09:24:10
1315	270	sofa-3.webp	2	f	2026-02-04 09:24:10
1316	271	sofa-1.webp	0	t	2026-02-04 09:24:10
1317	271	sofa-2.webp	1	f	2026-02-04 09:24:10
1318	271	sofa-3.webp	2	f	2026-02-04 09:24:10
1319	272	sofa-1.webp	0	t	2026-02-04 09:24:10
1320	272	sofa-2.webp	1	f	2026-02-04 09:24:10
1321	272	sofa-3.webp	2	f	2026-02-04 09:24:10
1322	273	sofa-1.webp	0	t	2026-02-04 09:24:10
1323	273	sofa-2.webp	1	f	2026-02-04 09:24:10
1324	273	sofa-3.webp	2	f	2026-02-04 09:24:10
1325	274	sofa-1.webp	0	t	2026-02-04 09:24:10
1326	274	sofa-2.webp	1	f	2026-02-04 09:24:10
1327	274	sofa-3.webp	2	f	2026-02-04 09:24:10
1328	275	sofa-1.webp	0	t	2026-02-04 09:24:10
1329	275	sofa-2.webp	1	f	2026-02-04 09:24:10
1330	275	sofa-3.webp	2	f	2026-02-04 09:24:10
1331	276	sofa-1.webp	0	t	2026-02-04 09:24:10
1332	276	sofa-2.webp	1	f	2026-02-04 09:24:10
1333	276	sofa-3.webp	2	f	2026-02-04 09:24:10
1334	277	sofa-1.webp	0	t	2026-02-04 09:24:10
1335	277	sofa-2.webp	1	f	2026-02-04 09:24:10
1336	277	sofa-3.webp	2	f	2026-02-04 09:24:10
1337	278	sofa-1.webp	0	t	2026-02-04 09:24:10
1338	278	sofa-2.webp	1	f	2026-02-04 09:24:10
1339	278	sofa-3.webp	2	f	2026-02-04 09:24:10
1340	279	sofa-1.webp	0	t	2026-02-04 09:24:10
1341	279	sofa-2.webp	1	f	2026-02-04 09:24:10
1342	279	sofa-3.webp	2	f	2026-02-04 09:24:10
1343	280	sofa-1.webp	0	t	2026-02-04 09:24:10
1344	280	sofa-2.webp	1	f	2026-02-04 09:24:10
1345	280	sofa-3.webp	2	f	2026-02-04 09:24:10
1346	281	sofa-1.webp	0	t	2026-02-04 09:24:10
1347	281	sofa-2.webp	1	f	2026-02-04 09:24:10
1348	281	sofa-3.webp	2	f	2026-02-04 09:24:10
1349	282	sofa-1.webp	0	t	2026-02-04 09:24:10
1350	282	sofa-2.webp	1	f	2026-02-04 09:24:10
1351	282	sofa-3.webp	2	f	2026-02-04 09:24:10
1352	283	sofa-1.webp	0	t	2026-02-04 09:24:10
1353	283	sofa-2.webp	1	f	2026-02-04 09:24:10
1354	283	sofa-3.webp	2	f	2026-02-04 09:24:10
1355	284	sofa-1.webp	0	t	2026-02-04 09:24:10
1356	284	sofa-2.webp	1	f	2026-02-04 09:24:10
1357	284	sofa-3.webp	2	f	2026-02-04 09:24:10
1358	285	sofa-1.webp	0	t	2026-02-04 09:24:10
1359	285	sofa-2.webp	1	f	2026-02-04 09:24:10
1360	285	sofa-3.webp	2	f	2026-02-04 09:24:10
1361	286	sofa-1.webp	0	t	2026-02-04 09:24:10
1362	286	sofa-2.webp	1	f	2026-02-04 09:24:10
1363	286	sofa-3.webp	2	f	2026-02-04 09:24:10
1364	287	sofa-1.webp	0	t	2026-02-04 09:24:10
1365	287	sofa-2.webp	1	f	2026-02-04 09:24:10
1366	287	sofa-3.webp	2	f	2026-02-04 09:24:10
1367	288	sofa-1.webp	0	t	2026-02-04 09:24:10
1368	288	sofa-2.webp	1	f	2026-02-04 09:24:10
1369	288	sofa-3.webp	2	f	2026-02-04 09:24:10
1370	289	sofa-1.webp	0	t	2026-02-04 09:24:10
1371	289	sofa-2.webp	1	f	2026-02-04 09:24:10
1372	289	sofa-3.webp	2	f	2026-02-04 09:24:10
1373	290	sofa-1.webp	0	t	2026-02-04 09:24:10
1374	290	sofa-2.webp	1	f	2026-02-04 09:24:10
1375	290	sofa-3.webp	2	f	2026-02-04 09:24:10
1376	291	sofa-1.webp	0	t	2026-02-04 09:24:10
1377	291	sofa-2.webp	1	f	2026-02-04 09:24:10
1378	291	sofa-3.webp	2	f	2026-02-04 09:24:10
1379	292	sofa-1.webp	0	t	2026-02-04 09:24:10
1380	292	sofa-2.webp	1	f	2026-02-04 09:24:10
1381	292	sofa-3.webp	2	f	2026-02-04 09:24:10
1382	293	sofa-1.webp	0	t	2026-02-04 09:24:10
1383	293	sofa-2.webp	1	f	2026-02-04 09:24:10
1384	293	sofa-3.webp	2	f	2026-02-04 09:24:10
1385	294	sofa-1.webp	0	t	2026-02-04 09:24:10
1386	294	sofa-2.webp	1	f	2026-02-04 09:24:10
1387	294	sofa-3.webp	2	f	2026-02-04 09:24:10
1388	295	sofa-1.webp	0	t	2026-02-04 09:24:10
1389	295	sofa-2.webp	1	f	2026-02-04 09:24:10
1390	295	sofa-3.webp	2	f	2026-02-04 09:24:10
1391	296	sofa-1.webp	0	t	2026-02-04 09:24:10
1392	296	sofa-2.webp	1	f	2026-02-04 09:24:10
1393	296	sofa-3.webp	2	f	2026-02-04 09:24:10
1394	297	sofa-1.webp	0	t	2026-02-04 09:24:10
1395	297	sofa-2.webp	1	f	2026-02-04 09:24:10
1396	297	sofa-3.webp	2	f	2026-02-04 09:24:10
1397	298	sofa-1.webp	0	t	2026-02-04 09:24:10
1398	298	sofa-2.webp	1	f	2026-02-04 09:24:10
1399	298	sofa-3.webp	2	f	2026-02-04 09:24:10
1400	299	sofa-1.webp	0	t	2026-02-04 09:24:10
1401	299	sofa-2.webp	1	f	2026-02-04 09:24:10
1402	299	sofa-3.webp	2	f	2026-02-04 09:24:10
1403	300	sofa-1.webp	0	t	2026-02-04 09:24:10
1404	300	sofa-2.webp	1	f	2026-02-04 09:24:10
1405	300	sofa-3.webp	2	f	2026-02-04 09:24:10
1406	301	shoes-1.webp	0	t	2026-02-04 09:24:10
1407	301	shoes-2.webp	1	f	2026-02-04 09:24:10
1408	301	shoes-3.webp	2	f	2026-02-04 09:24:10
1409	302	shoes-1.webp	0	t	2026-02-04 09:24:10
1410	302	shoes-2.webp	1	f	2026-02-04 09:24:10
1411	302	shoes-3.webp	2	f	2026-02-04 09:24:10
1412	303	shoes-1.webp	0	t	2026-02-04 09:24:10
1413	303	shoes-2.webp	1	f	2026-02-04 09:24:10
1414	303	shoes-3.webp	2	f	2026-02-04 09:24:10
1415	304	shoes-1.webp	0	t	2026-02-04 09:24:10
1416	304	shoes-2.webp	1	f	2026-02-04 09:24:10
1417	304	shoes-3.webp	2	f	2026-02-04 09:24:10
1418	305	shoes-1.webp	0	t	2026-02-04 09:24:10
1419	305	shoes-2.webp	1	f	2026-02-04 09:24:10
1420	305	shoes-3.webp	2	f	2026-02-04 09:24:10
1421	306	shoes-1.webp	0	t	2026-02-04 09:24:10
1422	306	shoes-2.webp	1	f	2026-02-04 09:24:10
1423	306	shoes-3.webp	2	f	2026-02-04 09:24:10
1424	307	shoes-1.webp	0	t	2026-02-04 09:24:10
1425	307	shoes-2.webp	1	f	2026-02-04 09:24:10
1426	307	shoes-3.webp	2	f	2026-02-04 09:24:10
1427	308	shoes-1.webp	0	t	2026-02-04 09:24:10
1428	308	shoes-2.webp	1	f	2026-02-04 09:24:10
1429	308	shoes-3.webp	2	f	2026-02-04 09:24:10
1430	309	shoes-1.webp	0	t	2026-02-04 09:24:10
1431	309	shoes-2.webp	1	f	2026-02-04 09:24:10
1432	309	shoes-3.webp	2	f	2026-02-04 09:24:10
1433	310	shoes-1.webp	0	t	2026-02-04 09:24:10
1434	310	shoes-2.webp	1	f	2026-02-04 09:24:10
1435	310	shoes-3.webp	2	f	2026-02-04 09:24:10
1436	311	shoes-1.webp	0	t	2026-02-04 09:24:10
1437	311	shoes-2.webp	1	f	2026-02-04 09:24:10
1438	311	shoes-3.webp	2	f	2026-02-04 09:24:10
1439	312	shoes-1.webp	0	t	2026-02-04 09:24:10
1440	312	shoes-2.webp	1	f	2026-02-04 09:24:10
1441	312	shoes-3.webp	2	f	2026-02-04 09:24:10
1442	313	shoes-1.webp	0	t	2026-02-04 09:24:10
1443	313	shoes-2.webp	1	f	2026-02-04 09:24:10
1444	313	shoes-3.webp	2	f	2026-02-04 09:24:10
1445	314	shoes-1.webp	0	t	2026-02-04 09:24:10
1446	314	shoes-2.webp	1	f	2026-02-04 09:24:10
1447	314	shoes-3.webp	2	f	2026-02-04 09:24:10
1448	315	shoes-1.webp	0	t	2026-02-04 09:24:10
1449	315	shoes-2.webp	1	f	2026-02-04 09:24:10
1450	315	shoes-3.webp	2	f	2026-02-04 09:24:10
1451	316	shoes-1.webp	0	t	2026-02-04 09:24:10
1452	316	shoes-2.webp	1	f	2026-02-04 09:24:10
1453	316	shoes-3.webp	2	f	2026-02-04 09:24:10
1454	317	shoes-1.webp	0	t	2026-02-04 09:24:10
1455	317	shoes-2.webp	1	f	2026-02-04 09:24:10
1456	317	shoes-3.webp	2	f	2026-02-04 09:24:10
1457	318	shoes-1.webp	0	t	2026-02-04 09:24:10
1458	318	shoes-2.webp	1	f	2026-02-04 09:24:10
1459	318	shoes-3.webp	2	f	2026-02-04 09:24:10
1460	319	shoes-1.webp	0	t	2026-02-04 09:24:10
1461	319	shoes-2.webp	1	f	2026-02-04 09:24:10
1462	319	shoes-3.webp	2	f	2026-02-04 09:24:10
1463	320	shoes-1.webp	0	t	2026-02-04 09:24:10
1464	320	shoes-2.webp	1	f	2026-02-04 09:24:10
1465	320	shoes-3.webp	2	f	2026-02-04 09:24:10
1466	321	shoes-1.webp	0	t	2026-02-04 09:24:10
1467	321	shoes-2.webp	1	f	2026-02-04 09:24:10
1468	321	shoes-3.webp	2	f	2026-02-04 09:24:10
1469	322	shoes-1.webp	0	t	2026-02-04 09:24:10
1470	322	shoes-2.webp	1	f	2026-02-04 09:24:10
1471	322	shoes-3.webp	2	f	2026-02-04 09:24:10
1472	323	shoes-1.webp	0	t	2026-02-04 09:24:10
1473	323	shoes-2.webp	1	f	2026-02-04 09:24:10
1474	323	shoes-3.webp	2	f	2026-02-04 09:24:10
1475	324	shoes-1.webp	0	t	2026-02-04 09:24:10
1476	324	shoes-2.webp	1	f	2026-02-04 09:24:10
1477	324	shoes-3.webp	2	f	2026-02-04 09:24:10
1478	325	shoes-1.webp	0	t	2026-02-04 09:24:10
1479	325	shoes-2.webp	1	f	2026-02-04 09:24:10
1480	325	shoes-3.webp	2	f	2026-02-04 09:24:10
1481	326	shoes-1.webp	0	t	2026-02-04 09:24:10
1482	326	shoes-2.webp	1	f	2026-02-04 09:24:10
1483	326	shoes-3.webp	2	f	2026-02-04 09:24:10
1484	327	shoes-1.webp	0	t	2026-02-04 09:24:10
1485	327	shoes-2.webp	1	f	2026-02-04 09:24:10
1486	327	shoes-3.webp	2	f	2026-02-04 09:24:10
1487	328	shoes-1.webp	0	t	2026-02-04 09:24:10
1488	328	shoes-2.webp	1	f	2026-02-04 09:24:10
1489	328	shoes-3.webp	2	f	2026-02-04 09:24:10
1490	329	shoes-1.webp	0	t	2026-02-04 09:24:10
1491	329	shoes-2.webp	1	f	2026-02-04 09:24:10
1492	329	shoes-3.webp	2	f	2026-02-04 09:24:10
1493	330	shoes-1.webp	0	t	2026-02-04 09:24:10
1494	330	shoes-2.webp	1	f	2026-02-04 09:24:10
1495	330	shoes-3.webp	2	f	2026-02-04 09:24:10
1496	331	shoes-1.webp	0	t	2026-02-04 09:24:10
1497	331	shoes-2.webp	1	f	2026-02-04 09:24:10
1498	331	shoes-3.webp	2	f	2026-02-04 09:24:10
1499	332	shoes-1.webp	0	t	2026-02-04 09:24:10
1500	332	shoes-2.webp	1	f	2026-02-04 09:24:10
1501	332	shoes-3.webp	2	f	2026-02-04 09:24:10
1502	333	shoes-1.webp	0	t	2026-02-04 09:24:10
1503	333	shoes-2.webp	1	f	2026-02-04 09:24:10
1504	333	shoes-3.webp	2	f	2026-02-04 09:24:10
1505	334	shoes-1.webp	0	t	2026-02-04 09:24:10
1506	334	shoes-2.webp	1	f	2026-02-04 09:24:10
1507	334	shoes-3.webp	2	f	2026-02-04 09:24:10
1508	335	shoes-1.webp	0	t	2026-02-04 09:24:10
1509	335	shoes-2.webp	1	f	2026-02-04 09:24:10
1510	335	shoes-3.webp	2	f	2026-02-04 09:24:10
1511	336	shoes-1.webp	0	t	2026-02-04 09:24:10
1512	336	shoes-2.webp	1	f	2026-02-04 09:24:10
1513	336	shoes-3.webp	2	f	2026-02-04 09:24:10
1514	337	shoes-1.webp	0	t	2026-02-04 09:24:10
1515	337	shoes-2.webp	1	f	2026-02-04 09:24:10
1516	337	shoes-3.webp	2	f	2026-02-04 09:24:10
1517	338	shoes-1.webp	0	t	2026-02-04 09:24:10
1518	338	shoes-2.webp	1	f	2026-02-04 09:24:10
1519	338	shoes-3.webp	2	f	2026-02-04 09:24:10
1520	339	shoes-1.webp	0	t	2026-02-04 09:24:10
1521	339	shoes-2.webp	1	f	2026-02-04 09:24:10
1522	339	shoes-3.webp	2	f	2026-02-04 09:24:10
1523	340	shoes-1.webp	0	t	2026-02-04 09:24:10
1524	340	shoes-2.webp	1	f	2026-02-04 09:24:10
1525	340	shoes-3.webp	2	f	2026-02-04 09:24:10
1526	341	shoes-1.webp	0	t	2026-02-04 09:24:10
1527	341	shoes-2.webp	1	f	2026-02-04 09:24:10
1528	341	shoes-3.webp	2	f	2026-02-04 09:24:10
1529	342	shoes-1.webp	0	t	2026-02-04 09:24:10
1530	342	shoes-2.webp	1	f	2026-02-04 09:24:10
1531	342	shoes-3.webp	2	f	2026-02-04 09:24:10
1532	343	shoes-1.webp	0	t	2026-02-04 09:24:10
1533	343	shoes-2.webp	1	f	2026-02-04 09:24:10
1534	343	shoes-3.webp	2	f	2026-02-04 09:24:10
1535	344	shoes-1.webp	0	t	2026-02-04 09:24:10
1536	344	shoes-2.webp	1	f	2026-02-04 09:24:10
1537	344	shoes-3.webp	2	f	2026-02-04 09:24:10
1538	345	shoes-1.webp	0	t	2026-02-04 09:24:10
1539	345	shoes-2.webp	1	f	2026-02-04 09:24:10
1540	345	shoes-3.webp	2	f	2026-02-04 09:24:10
1541	346	shoes-1.webp	0	t	2026-02-04 09:24:10
1542	346	shoes-2.webp	1	f	2026-02-04 09:24:10
1543	346	shoes-3.webp	2	f	2026-02-04 09:24:10
1544	347	shoes-1.webp	0	t	2026-02-04 09:24:10
1545	347	shoes-2.webp	1	f	2026-02-04 09:24:10
1546	347	shoes-3.webp	2	f	2026-02-04 09:24:10
1547	348	shoes-1.webp	0	t	2026-02-04 09:24:10
1548	348	shoes-2.webp	1	f	2026-02-04 09:24:10
1549	348	shoes-3.webp	2	f	2026-02-04 09:24:10
1550	349	shoes-1.webp	0	t	2026-02-04 09:24:10
1551	349	shoes-2.webp	1	f	2026-02-04 09:24:10
1552	349	shoes-3.webp	2	f	2026-02-04 09:24:10
1553	350	shoes-1.webp	0	t	2026-02-04 09:24:10
1554	350	shoes-2.webp	1	f	2026-02-04 09:24:10
1555	350	shoes-3.webp	2	f	2026-02-04 09:24:10
1556	351	shoes-1.webp	0	t	2026-02-04 09:24:10
1557	351	shoes-2.webp	1	f	2026-02-04 09:24:10
1558	351	shoes-3.webp	2	f	2026-02-04 09:24:10
1559	352	shoes-1.webp	0	t	2026-02-04 09:24:10
1560	352	shoes-2.webp	1	f	2026-02-04 09:24:10
1561	352	shoes-3.webp	2	f	2026-02-04 09:24:10
1562	353	shoes-1.webp	0	t	2026-02-04 09:24:10
1563	353	shoes-2.webp	1	f	2026-02-04 09:24:10
1564	353	shoes-3.webp	2	f	2026-02-04 09:24:10
1565	354	shoes-1.webp	0	t	2026-02-04 09:24:10
1566	354	shoes-2.webp	1	f	2026-02-04 09:24:10
1567	354	shoes-3.webp	2	f	2026-02-04 09:24:10
1568	355	shoes-1.webp	0	t	2026-02-04 09:24:10
1569	355	shoes-2.webp	1	f	2026-02-04 09:24:10
1570	355	shoes-3.webp	2	f	2026-02-04 09:24:10
1571	356	shoes-1.webp	0	t	2026-02-04 09:24:10
1572	356	shoes-2.webp	1	f	2026-02-04 09:24:10
1573	356	shoes-3.webp	2	f	2026-02-04 09:24:10
1574	357	shoes-1.webp	0	t	2026-02-04 09:24:10
1575	357	shoes-2.webp	1	f	2026-02-04 09:24:10
1576	357	shoes-3.webp	2	f	2026-02-04 09:24:10
1577	358	shoes-1.webp	0	t	2026-02-04 09:24:10
1578	358	shoes-2.webp	1	f	2026-02-04 09:24:10
1579	358	shoes-3.webp	2	f	2026-02-04 09:24:10
1580	359	shoes-1.webp	0	t	2026-02-04 09:24:10
1581	359	shoes-2.webp	1	f	2026-02-04 09:24:10
1582	359	shoes-3.webp	2	f	2026-02-04 09:24:10
1583	360	shoes-1.webp	0	t	2026-02-04 09:24:10
1584	360	shoes-2.webp	1	f	2026-02-04 09:24:10
1585	360	shoes-3.webp	2	f	2026-02-04 09:24:10
1586	361	shoes-1.webp	0	t	2026-02-04 09:24:10
1587	361	shoes-2.webp	1	f	2026-02-04 09:24:10
1588	361	shoes-3.webp	2	f	2026-02-04 09:24:10
1589	362	shoes-1.webp	0	t	2026-02-04 09:24:10
1590	362	shoes-2.webp	1	f	2026-02-04 09:24:10
1591	362	shoes-3.webp	2	f	2026-02-04 09:24:10
1592	363	shoes-1.webp	0	t	2026-02-04 09:24:10
1593	363	shoes-2.webp	1	f	2026-02-04 09:24:10
1594	363	shoes-3.webp	2	f	2026-02-04 09:24:10
1595	364	shoes-1.webp	0	t	2026-02-04 09:24:10
1596	364	shoes-2.webp	1	f	2026-02-04 09:24:10
1597	364	shoes-3.webp	2	f	2026-02-04 09:24:10
1598	365	shoes-1.webp	0	t	2026-02-04 09:24:10
1599	365	shoes-2.webp	1	f	2026-02-04 09:24:10
1600	365	shoes-3.webp	2	f	2026-02-04 09:24:10
1601	366	shoes-1.webp	0	t	2026-02-04 09:24:10
1602	366	shoes-2.webp	1	f	2026-02-04 09:24:10
1603	366	shoes-3.webp	2	f	2026-02-04 09:24:10
1604	367	shoes-1.webp	0	t	2026-02-04 09:24:10
1605	367	shoes-2.webp	1	f	2026-02-04 09:24:10
1606	367	shoes-3.webp	2	f	2026-02-04 09:24:10
1607	368	shoes-1.webp	0	t	2026-02-04 09:24:10
1608	368	shoes-2.webp	1	f	2026-02-04 09:24:10
1609	368	shoes-3.webp	2	f	2026-02-04 09:24:10
1610	369	shoes-1.webp	0	t	2026-02-04 09:24:10
1611	369	shoes-2.webp	1	f	2026-02-04 09:24:10
1612	369	shoes-3.webp	2	f	2026-02-04 09:24:10
1613	370	shoes-1.webp	0	t	2026-02-04 09:24:10
1614	370	shoes-2.webp	1	f	2026-02-04 09:24:10
1615	370	shoes-3.webp	2	f	2026-02-04 09:24:10
1616	371	shoes-1.webp	0	t	2026-02-04 09:24:10
1617	371	shoes-2.webp	1	f	2026-02-04 09:24:10
1618	371	shoes-3.webp	2	f	2026-02-04 09:24:10
1619	372	shoes-1.webp	0	t	2026-02-04 09:24:10
1620	372	shoes-2.webp	1	f	2026-02-04 09:24:10
1621	372	shoes-3.webp	2	f	2026-02-04 09:24:10
1622	373	shoes-1.webp	0	t	2026-02-04 09:24:10
1623	373	shoes-2.webp	1	f	2026-02-04 09:24:10
1624	373	shoes-3.webp	2	f	2026-02-04 09:24:10
1625	374	shoes-1.webp	0	t	2026-02-04 09:24:10
1626	374	shoes-2.webp	1	f	2026-02-04 09:24:10
1627	374	shoes-3.webp	2	f	2026-02-04 09:24:10
1628	375	shoes-1.webp	0	t	2026-02-04 09:24:10
1629	375	shoes-2.webp	1	f	2026-02-04 09:24:10
1630	375	shoes-3.webp	2	f	2026-02-04 09:24:10
1631	376	shoes-1.webp	0	t	2026-02-04 09:24:10
1632	376	shoes-2.webp	1	f	2026-02-04 09:24:10
1633	376	shoes-3.webp	2	f	2026-02-04 09:24:10
1634	377	shoes-1.webp	0	t	2026-02-04 09:24:10
1635	377	shoes-2.webp	1	f	2026-02-04 09:24:10
1636	377	shoes-3.webp	2	f	2026-02-04 09:24:10
1637	378	shoes-1.webp	0	t	2026-02-04 09:24:10
1638	378	shoes-2.webp	1	f	2026-02-04 09:24:10
1639	378	shoes-3.webp	2	f	2026-02-04 09:24:10
1640	379	shoes-1.webp	0	t	2026-02-04 09:24:10
1641	379	shoes-2.webp	1	f	2026-02-04 09:24:10
1642	379	shoes-3.webp	2	f	2026-02-04 09:24:10
1643	380	shoes-1.webp	0	t	2026-02-04 09:24:10
1644	380	shoes-2.webp	1	f	2026-02-04 09:24:10
1645	380	shoes-3.webp	2	f	2026-02-04 09:24:10
1646	381	shoes-1.webp	0	t	2026-02-04 09:24:10
1647	381	shoes-2.webp	1	f	2026-02-04 09:24:10
1648	381	shoes-3.webp	2	f	2026-02-04 09:24:10
1649	382	shoes-1.webp	0	t	2026-02-04 09:24:10
1650	382	shoes-2.webp	1	f	2026-02-04 09:24:10
1651	382	shoes-3.webp	2	f	2026-02-04 09:24:10
1652	383	shoes-1.webp	0	t	2026-02-04 09:24:10
1653	383	shoes-2.webp	1	f	2026-02-04 09:24:10
1654	383	shoes-3.webp	2	f	2026-02-04 09:24:10
1655	384	shoes-1.webp	0	t	2026-02-04 09:24:10
1656	384	shoes-2.webp	1	f	2026-02-04 09:24:10
1657	384	shoes-3.webp	2	f	2026-02-04 09:24:10
1658	385	shoes-1.webp	0	t	2026-02-04 09:24:10
1659	385	shoes-2.webp	1	f	2026-02-04 09:24:10
1660	385	shoes-3.webp	2	f	2026-02-04 09:24:10
1661	386	shoes-1.webp	0	t	2026-02-04 09:24:10
1662	386	shoes-2.webp	1	f	2026-02-04 09:24:10
1663	386	shoes-3.webp	2	f	2026-02-04 09:24:10
1664	387	shoes-1.webp	0	t	2026-02-04 09:24:10
1665	387	shoes-2.webp	1	f	2026-02-04 09:24:10
1666	387	shoes-3.webp	2	f	2026-02-04 09:24:10
1667	388	shoes-1.webp	0	t	2026-02-04 09:24:10
1668	388	shoes-2.webp	1	f	2026-02-04 09:24:10
1669	388	shoes-3.webp	2	f	2026-02-04 09:24:10
1670	389	shoes-1.webp	0	t	2026-02-04 09:24:10
1671	389	shoes-2.webp	1	f	2026-02-04 09:24:10
1672	389	shoes-3.webp	2	f	2026-02-04 09:24:10
1673	390	shoes-1.webp	0	t	2026-02-04 09:24:10
1674	390	shoes-2.webp	1	f	2026-02-04 09:24:10
1675	390	shoes-3.webp	2	f	2026-02-04 09:24:10
1676	391	shoes-1.webp	0	t	2026-02-04 09:24:10
1677	391	shoes-2.webp	1	f	2026-02-04 09:24:10
1678	391	shoes-3.webp	2	f	2026-02-04 09:24:10
1679	392	shoes-1.webp	0	t	2026-02-04 09:24:10
1680	392	shoes-2.webp	1	f	2026-02-04 09:24:10
1681	392	shoes-3.webp	2	f	2026-02-04 09:24:10
1682	393	shoes-1.webp	0	t	2026-02-04 09:24:10
1683	393	shoes-2.webp	1	f	2026-02-04 09:24:10
1684	393	shoes-3.webp	2	f	2026-02-04 09:24:10
1685	394	shoes-1.webp	0	t	2026-02-04 09:24:10
1686	394	shoes-2.webp	1	f	2026-02-04 09:24:10
1687	394	shoes-3.webp	2	f	2026-02-04 09:24:10
1688	395	shoes-1.webp	0	t	2026-02-04 09:24:10
1689	395	shoes-2.webp	1	f	2026-02-04 09:24:10
1690	395	shoes-3.webp	2	f	2026-02-04 09:24:10
1691	396	shoes-1.webp	0	t	2026-02-04 09:24:10
1692	396	shoes-2.webp	1	f	2026-02-04 09:24:10
1693	396	shoes-3.webp	2	f	2026-02-04 09:24:10
1694	397	shoes-1.webp	0	t	2026-02-04 09:24:10
1695	397	shoes-2.webp	1	f	2026-02-04 09:24:10
1696	397	shoes-3.webp	2	f	2026-02-04 09:24:10
1697	398	shoes-1.webp	0	t	2026-02-04 09:24:10
1698	398	shoes-2.webp	1	f	2026-02-04 09:24:10
1699	398	shoes-3.webp	2	f	2026-02-04 09:24:10
1700	399	shoes-1.webp	0	t	2026-02-04 09:24:10
1701	399	shoes-2.webp	1	f	2026-02-04 09:24:10
1702	399	shoes-3.webp	2	f	2026-02-04 09:24:10
1703	400	shoes-1.webp	0	t	2026-02-04 09:24:10
1704	400	shoes-2.webp	1	f	2026-02-04 09:24:10
1705	400	shoes-3.webp	2	f	2026-02-04 09:24:10
1706	401	book.png	0	t	2026-02-04 09:24:10
1707	402	book.png	0	t	2026-02-04 09:24:10
1708	403	book.png	0	t	2026-02-04 09:24:10
1709	404	book.png	0	t	2026-02-04 09:24:10
1710	405	book.png	0	t	2026-02-04 09:24:10
1711	406	book.png	0	t	2026-02-04 09:24:10
1712	407	book.png	0	t	2026-02-04 09:24:10
1713	408	book.png	0	t	2026-02-04 09:24:10
1714	409	book.png	0	t	2026-02-04 09:24:10
1715	410	book.png	0	t	2026-02-04 09:24:10
1716	411	book.png	0	t	2026-02-04 09:24:10
1717	412	book.png	0	t	2026-02-04 09:24:10
1718	413	book.png	0	t	2026-02-04 09:24:10
1719	414	book.png	0	t	2026-02-04 09:24:10
1720	415	book.png	0	t	2026-02-04 09:24:10
1721	416	book.png	0	t	2026-02-04 09:24:10
1722	417	book.png	0	t	2026-02-04 09:24:10
1723	418	book.png	0	t	2026-02-04 09:24:10
1724	419	book.png	0	t	2026-02-04 09:24:10
1725	420	book.png	0	t	2026-02-04 09:24:10
1726	421	book.png	0	t	2026-02-04 09:24:10
1727	422	book.png	0	t	2026-02-04 09:24:10
1728	423	book.png	0	t	2026-02-04 09:24:10
1729	424	book.png	0	t	2026-02-04 09:24:10
1730	425	book.png	0	t	2026-02-04 09:24:10
1731	426	book.png	0	t	2026-02-04 09:24:10
1732	427	book.png	0	t	2026-02-04 09:24:10
1733	428	book.png	0	t	2026-02-04 09:24:10
1734	429	book.png	0	t	2026-02-04 09:24:10
1735	430	book.png	0	t	2026-02-04 09:24:10
1736	431	book.png	0	t	2026-02-04 09:24:10
1737	432	book.png	0	t	2026-02-04 09:24:10
1738	433	book.png	0	t	2026-02-04 09:24:10
1739	434	book.png	0	t	2026-02-04 09:24:10
1740	435	book.png	0	t	2026-02-04 09:24:10
1741	436	book.png	0	t	2026-02-04 09:24:10
1742	437	book.png	0	t	2026-02-04 09:24:10
1743	438	book.png	0	t	2026-02-04 09:24:10
1744	439	book.png	0	t	2026-02-04 09:24:10
1745	440	book.png	0	t	2026-02-04 09:24:10
1746	441	book.png	0	t	2026-02-04 09:24:10
1747	442	book.png	0	t	2026-02-04 09:24:10
1748	443	book.png	0	t	2026-02-04 09:24:10
1749	444	book.png	0	t	2026-02-04 09:24:10
1750	445	book.png	0	t	2026-02-04 09:24:10
1751	446	book.png	0	t	2026-02-04 09:24:10
1752	447	book.png	0	t	2026-02-04 09:24:10
1753	448	book.png	0	t	2026-02-04 09:24:10
1754	449	book.png	0	t	2026-02-04 09:24:10
1755	450	book.png	0	t	2026-02-04 09:24:10
1756	451	book.png	0	t	2026-02-04 09:24:10
1757	452	book.png	0	t	2026-02-04 09:24:10
1758	453	book.png	0	t	2026-02-04 09:24:10
1759	454	book.png	0	t	2026-02-04 09:24:10
1760	455	book.png	0	t	2026-02-04 09:24:10
1761	456	book.png	0	t	2026-02-04 09:24:10
1762	457	book.png	0	t	2026-02-04 09:24:10
1763	458	book.png	0	t	2026-02-04 09:24:10
1764	459	book.png	0	t	2026-02-04 09:24:10
1765	460	book.png	0	t	2026-02-04 09:24:10
1766	461	book.png	0	t	2026-02-04 09:24:10
1767	462	book.png	0	t	2026-02-04 09:24:10
1768	463	book.png	0	t	2026-02-04 09:24:10
1769	464	book.png	0	t	2026-02-04 09:24:10
1770	465	book.png	0	t	2026-02-04 09:24:10
1771	466	book.png	0	t	2026-02-04 09:24:10
1772	467	book.png	0	t	2026-02-04 09:24:10
1773	468	book.png	0	t	2026-02-04 09:24:10
1774	469	book.png	0	t	2026-02-04 09:24:10
1775	470	book.png	0	t	2026-02-04 09:24:10
1776	471	book.png	0	t	2026-02-04 09:24:10
1777	472	book.png	0	t	2026-02-04 09:24:10
1778	473	book.png	0	t	2026-02-04 09:24:10
1779	474	book.png	0	t	2026-02-04 09:24:10
1780	475	book.png	0	t	2026-02-04 09:24:10
1781	476	book.png	0	t	2026-02-04 09:24:10
1782	477	book.png	0	t	2026-02-04 09:24:10
1783	478	book.png	0	t	2026-02-04 09:24:10
1784	479	book.png	0	t	2026-02-04 09:24:10
1785	480	book.png	0	t	2026-02-04 09:24:10
1786	481	book.png	0	t	2026-02-04 09:24:10
1787	482	book.png	0	t	2026-02-04 09:24:10
1788	483	book.png	0	t	2026-02-04 09:24:10
1789	484	book.png	0	t	2026-02-04 09:24:10
1790	485	book.png	0	t	2026-02-04 09:24:10
1791	486	book.png	0	t	2026-02-04 09:24:10
1792	487	book.png	0	t	2026-02-04 09:24:10
1793	488	book.png	0	t	2026-02-04 09:24:10
1794	489	book.png	0	t	2026-02-04 09:24:10
1795	490	book.png	0	t	2026-02-04 09:24:10
1796	491	book.png	0	t	2026-02-04 09:24:10
1797	492	book.png	0	t	2026-02-04 09:24:10
1798	493	book.png	0	t	2026-02-04 09:24:10
1799	494	book.png	0	t	2026-02-04 09:24:10
1800	495	book.png	0	t	2026-02-04 09:24:10
1801	496	book.png	0	t	2026-02-04 09:24:10
1802	497	book.png	0	t	2026-02-04 09:24:10
1803	498	book.png	0	t	2026-02-04 09:24:10
1804	499	book.png	0	t	2026-02-04 09:24:10
1805	500	book.png	0	t	2026-02-04 09:24:10
\.


--
-- TOC entry 5506 (class 0 OID 25592)
-- Dependencies: 261
-- Data for Name: catalog_wishlist; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.catalog_wishlist (wishlist_id, user_id, product_entity_id, created_at, updated_at) FROM stdin;
3	1	499	\N	\N
4	1	498	\N	\N
\.


--
-- TOC entry 5508 (class 0 OID 25618)
-- Dependencies: 263
-- Data for Name: categories; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.categories (id, name, slug) FROM stdin;
1	Electronics	electronics
2	Fashion	fashion
3	Home & Living	home-living
4	Sports	sports
5	Books	books
\.


--
-- TOC entry 5526 (class 0 OID 25793)
-- Dependencies: 281
-- Data for Name: coupons; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.coupons (id, code, discount_percent, created_at) FROM stdin;
1	GET5	5	2026-02-04 13:49:10.953213
2	GET10	10	2026-02-04 13:49:10.955435
3	GET15	15	2026-02-04 13:49:10.955898
4	GET25	25	2026-02-04 13:49:10.956285
\.


--
-- TOC entry 5476 (class 0 OID 25241)
-- Dependencies: 231
-- Data for Name: failed_jobs; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.failed_jobs (id, uuid, connection, queue, payload, exception, failed_at) FROM stdin;
\.


--
-- TOC entry 5474 (class 0 OID 25226)
-- Dependencies: 229
-- Data for Name: job_batches; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.job_batches (id, name, total_jobs, pending_jobs, failed_jobs, failed_job_ids, options, cancelled_at, created_at, finished_at) FROM stdin;
\.


--
-- TOC entry 5473 (class 0 OID 25211)
-- Dependencies: 228
-- Data for Name: jobs; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.jobs (id, queue, payload, attempts, reserved_at, available_at, created_at) FROM stdin;
\.


--
-- TOC entry 5465 (class 0 OID 25143)
-- Dependencies: 220
-- Data for Name: migrations; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.migrations (id, migration, batch) FROM stdin;
1	0001_01_01_000000_create_users_table	1
2	0001_01_01_000001_create_cache_table	1
3	0001_01_01_000002_create_jobs_table	1
4	2026_02_03_084508_create_catalog_tables	1
5	2026_02_03_084513_create_sales_tables	1
6	2026_02_03_085959_create_wishlist_table	1
7	2026_02_03_144000_add_is_active_to_catalog_product_entity_table	2
\.


--
-- TOC entry 5516 (class 0 OID 25694)
-- Dependencies: 271
-- Data for Name: order_items; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.order_items (id, order_id, product_id, quantity, price) FROM stdin;
\.


--
-- TOC entry 5514 (class 0 OID 25672)
-- Dependencies: 269
-- Data for Name: orders; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.orders (id, order_number, user_id, subtotal, shipping_cost, tax, total, status, shipping_method, created_at) FROM stdin;
\.


--
-- TOC entry 5468 (class 0 OID 25167)
-- Dependencies: 223
-- Data for Name: password_reset_tokens; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.password_reset_tokens (email, token, created_at) FROM stdin;
\.


--
-- TOC entry 5530 (class 0 OID 32972)
-- Dependencies: 285
-- Data for Name: product_reviews; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.product_reviews (review_id, product_entity_id, user_id, rating, comment, is_approved, created_at) FROM stdin;
\.


--
-- TOC entry 5512 (class 0 OID 25641)
-- Dependencies: 267
-- Data for Name: products; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.products (id, category_id, brand_id, name, slug, price, original_price, stock, description, image, is_featured, is_new, rating, reviews_count, created_at) FROM stdin;
1	1	6	Perfect Smartphone Version	perfect-smartphone-version-1	247.00	290.59	84	High-quality smartphone with excellent features and performance	📡	f	f	4.50	459	2026-02-03 15:22:02.038889
2	1	1	Pro Tablet Version	pro-tablet-version-2	144.00	180.00	40	High-quality tablet with excellent features and performance	📷	f	f	4.10	359	2026-02-03 15:22:02.041737
3	1	1	Expert Laptop Performance	expert-laptop-performance-3	55.00	78.57	31	High-quality laptop with excellent features and performance	🖱️	f	f	4.50	248	2026-02-03 15:22:02.04224
4	1	8	Supreme Desktop Computer Performance	supreme-desktop-computer-performance-4	168.00	186.67	90	High-quality desktop computer with excellent features and performance	🖥️	f	f	3.50	320	2026-02-03 15:22:02.042696
5	1	1	Master Monitor Max	master-monitor-max-5	267.09	296.77	71	High-quality monitor with excellent features and performance	⌨️	f	f	4.30	44	2026-02-03 15:22:02.043125
6	1	6	Essential Keyboard Edition	essential-keyboard-edition-6	107.97	107.97	76	High-quality keyboard with excellent features and performance	🎧	f	t	4.10	154	2026-02-03 15:22:02.043552
7	1	1	Essential Mouse Plus	essential-mouse-plus-7	39.02	48.78	6	High-quality mouse with excellent features and performance	🔊	f	t	3.90	87	2026-02-03 15:22:02.044187
8	1	6	Classic Headphones Collection	classic-headphones-collection-8	88.06	103.60	55	High-quality headphones with excellent features and performance	📷	t	f	3.50	113	2026-02-03 15:22:02.044652
9	1	1	Premium Earbuds Version	premium-earbuds-version-9	30.11	30.11	95	High-quality earbuds with excellent features and performance	🔊	f	f	3.90	152	2026-02-03 15:22:02.045036
10	1	1	Supreme Speaker Model	supreme-speaker-model-10	144.71	144.71	7	High-quality speaker with excellent features and performance	⌚	f	f	4.60	263	2026-02-03 15:22:02.045524
11	1	8	Essential Smartwatch Plus	essential-smartwatch-plus-11	234.33	246.66	56	High-quality smartwatch with excellent features and performance	📷	f	f	3.80	261	2026-02-03 15:22:02.04592
12	1	8	Deluxe Fitness Tracker Edition	deluxe-fitness-tracker-edition-12	150.94	150.94	84	High-quality fitness tracker with excellent features and performance	🎧	f	t	3.60	492	2026-02-03 15:22:02.046242
13	1	8	Advanced Camera Grade	advanced-camera-grade-13	257.39	257.39	85	High-quality camera with excellent features and performance	⌚	t	f	4.70	73	2026-02-03 15:22:02.046549
14	1	1	Perfect Webcam Model	perfect-webcam-model-14	56.87	75.82	38	High-quality webcam with excellent features and performance	🔊	f	t	4.50	480	2026-02-03 15:22:02.046836
15	1	6	Classic Microphone Series	classic-microphone-series-15	121.32	173.31	24	High-quality microphone with excellent features and performance	📷	t	f	4.50	35	2026-02-03 15:22:02.04708
16	1	8	Ultra Router Plus	ultra-router-plus-16	78.41	82.54	31	High-quality router with excellent features and performance	📡	f	f	4.00	367	2026-02-03 15:22:02.047352
17	1	6	Luxury Power Bank Model	luxury-power-bank-model-17	43.19	43.19	50	High-quality power bank with excellent features and performance	🖱️	f	f	4.10	493	2026-02-03 15:22:02.047619
18	1	1	Advanced Charger Version	advanced-charger-version-18	39.93	46.98	69	High-quality charger with excellent features and performance	📡	f	t	4.90	223	2026-02-03 15:22:02.047947
19	1	1	Deluxe USB Cable Model	deluxe-usb-cable-model-19	11.71	13.78	72	High-quality usb cable with excellent features and performance	💻	f	t	4.60	254	2026-02-03 15:22:02.048227
20	1	8	Expert Hard Drive Max	expert-hard-drive-max-20	122.52	163.36	45	High-quality hard drive with excellent features and performance	💻	f	t	3.50	172	2026-02-03 15:22:02.048522
21	1	1	Pro Smartphone Series	pro-smartphone-series-21	226.94	252.16	69	High-quality smartphone with excellent features and performance	📱	f	t	4.30	78	2026-02-03 15:22:02.048873
22	1	6	Ultra Tablet Series	ultra-tablet-series-22	190.71	211.90	14	High-quality tablet with excellent features and performance	🎧	t	t	5.00	265	2026-02-03 15:22:02.049427
23	1	8	Classic Laptop Series	classic-laptop-series-23	137.00	137.00	17	High-quality laptop with excellent features and performance	📷	f	f	3.50	360	2026-02-03 15:22:02.049724
24	1	1	Master Desktop Computer Quality	master-desktop-computer-quality-24	152.00	217.14	88	High-quality desktop computer with excellent features and performance	📱	f	f	4.90	238	2026-02-03 15:22:02.049991
25	1	1	Modern Monitor Edition	modern-monitor-edition-25	257.00	270.53	15	High-quality monitor with excellent features and performance	⌨️	f	f	4.30	112	2026-02-03 15:22:02.05033
26	1	8	Supreme Keyboard Performance	supreme-keyboard-performance-26	79.77	113.95	53	High-quality keyboard with excellent features and performance	💻	f	f	3.70	339	2026-02-03 15:22:02.050653
27	1	6	Deluxe Mouse Performance	deluxe-mouse-performance-27	20.07	23.61	51	High-quality mouse with excellent features and performance	🎧	f	f	4.20	425	2026-02-03 15:22:02.050951
28	1	8	Classic Headphones Edition	classic-headphones-edition-28	86.94	86.94	71	High-quality headphones with excellent features and performance	🔊	f	f	4.60	130	2026-02-03 15:22:02.051272
29	1	8	Supreme Earbuds Version	supreme-earbuds-version-29	195.68	205.98	29	High-quality earbuds with excellent features and performance	⌚	f	f	3.90	397	2026-02-03 15:22:02.051752
30	1	8	Essential Speaker Plus	essential-speaker-plus-30	84.52	84.52	81	High-quality speaker with excellent features and performance	🖱️	t	f	4.40	472	2026-02-03 15:22:02.05214
31	1	1	Perfect Smartwatch Series	perfect-smartwatch-series-31	98.70	116.12	11	High-quality smartwatch with excellent features and performance	⌨️	f	f	4.00	352	2026-02-03 15:22:02.052539
32	1	1	Professional Fitness Tracker Performance	professional-fitness-tracker-performance-32	133.36	177.81	99	High-quality fitness tracker with excellent features and performance	⌨️	f	f	4.40	27	2026-02-03 15:22:02.052951
33	1	6	Perfect Camera Collection	perfect-camera-collection-33	171.00	171.00	11	High-quality camera with excellent features and performance	🖱️	t	f	5.00	368	2026-02-03 15:22:02.053289
34	1	1	Supreme Webcam Series	supreme-webcam-series-34	89.73	94.45	90	High-quality webcam with excellent features and performance	🎧	f	t	4.90	45	2026-02-03 15:22:02.053654
35	1	6	Essential Microphone Quality	essential-microphone-quality-35	107.36	107.36	22	High-quality microphone with excellent features and performance	📡	f	f	3.70	257	2026-02-03 15:22:02.054125
36	1	8	Supreme Router Series	supreme-router-series-36	155.78	155.78	82	High-quality router with excellent features and performance	📡	f	t	3.60	361	2026-02-03 15:22:02.054539
37	1	1	Classic Power Bank Performance	classic-power-bank-performance-37	53.28	59.20	85	High-quality power bank with excellent features and performance	🖱️	f	f	4.60	422	2026-02-03 15:22:02.054934
38	1	1	Modern Charger Plus	modern-charger-plus-38	22.19	29.59	77	High-quality charger with excellent features and performance	💻	f	f	4.80	321	2026-02-03 15:22:02.055358
39	1	8	Modern USB Cable Series	modern-usb-cable-series-39	27.67	27.67	22	High-quality usb cable with excellent features and performance	⌨️	f	t	4.50	144	2026-02-03 15:22:02.055704
40	1	6	Deluxe Hard Drive Series	deluxe-hard-drive-series-40	138.96	173.70	26	High-quality hard drive with excellent features and performance	💻	f	f	5.00	327	2026-02-03 15:22:02.056017
41	1	8	Perfect Smartphone Model	perfect-smartphone-model-41	239.00	265.56	59	High-quality smartphone with excellent features and performance	💻	f	t	4.70	356	2026-02-03 15:22:02.057208
42	1	1	Expert Tablet Series	expert-tablet-series-42	243.42	256.23	42	High-quality tablet with excellent features and performance	💻	f	f	4.80	52	2026-02-03 15:22:02.057733
43	1	6	Deluxe Laptop Grade	deluxe-laptop-grade-43	125.00	125.00	88	High-quality laptop with excellent features and performance	🖥️	f	f	4.30	154	2026-02-03 15:22:02.058122
44	1	8	Advanced Desktop Computer Plus	advanced-desktop-computer-plus-44	80.00	114.29	57	High-quality desktop computer with excellent features and performance	🖥️	f	f	4.20	480	2026-02-03 15:22:02.05838
45	1	8	Luxury Monitor Version	luxury-monitor-version-45	143.00	204.29	80	High-quality monitor with excellent features and performance	🖥️	t	f	4.10	54	2026-02-03 15:22:02.058688
46	1	1	Professional Keyboard Series	professional-keyboard-series-46	73.79	81.99	20	High-quality keyboard with excellent features and performance	📷	f	t	4.50	273	2026-02-03 15:22:02.059027
47	1	8	Premium Mouse Plus	premium-mouse-plus-47	26.99	38.56	43	High-quality mouse with excellent features and performance	🖥️	f	f	4.30	102	2026-02-03 15:22:02.059484
48	1	1	Modern Headphones Series	modern-headphones-series-48	45.06	56.33	53	High-quality headphones with excellent features and performance	⌚	f	t	3.60	331	2026-02-03 15:22:02.059875
49	1	8	Professional Earbuds Quality	professional-earbuds-quality-49	175.60	234.13	57	High-quality earbuds with excellent features and performance	📡	f	f	4.40	285	2026-02-03 15:22:02.060348
50	1	8	Ultra Speaker Performance	ultra-speaker-performance-50	142.69	142.69	20	High-quality speaker with excellent features and performance	🖱️	f	f	5.00	364	2026-02-03 15:22:02.060654
51	1	6	Supreme Smartwatch Grade	supreme-smartwatch-grade-51	179.00	179.00	37	High-quality smartwatch with excellent features and performance	🖱️	f	f	4.30	55	2026-02-03 15:22:02.060918
52	1	8	Supreme Fitness Tracker Model	supreme-fitness-tracker-model-52	56.23	56.23	69	High-quality fitness tracker with excellent features and performance	🖱️	f	f	4.70	170	2026-02-03 15:22:02.061364
53	1	1	Premium Camera Edition	premium-camera-edition-53	235.00	276.47	5	High-quality camera with excellent features and performance	📱	f	f	4.10	202	2026-02-03 15:22:02.061784
54	1	1	Perfect Webcam Performance	perfect-webcam-performance-54	149.14	149.14	77	High-quality webcam with excellent features and performance	📷	f	f	4.30	403	2026-02-03 15:22:02.062191
55	1	1	Premium Microphone Version	premium-microphone-version-55	180.28	180.28	70	High-quality microphone with excellent features and performance	🖱️	f	t	3.50	441	2026-02-03 15:22:02.062724
56	1	8	Expert Router Series	expert-router-series-56	79.01	83.17	63	High-quality router with excellent features and performance	📡	f	f	4.20	274	2026-02-03 15:22:02.063142
57	1	8	Pro Power Bank Plus	pro-power-bank-plus-57	45.32	47.71	20	High-quality power bank with excellent features and performance	🖥️	f	f	4.50	138	2026-02-03 15:22:02.063469
58	1	1	Elite Charger Model	elite-charger-model-58	57.63	57.63	22	High-quality charger with excellent features and performance	🔊	f	t	4.70	135	2026-02-03 15:22:02.063839
59	1	8	Essential USB Cable Model	essential-usb-cable-model-59	28.06	29.54	81	High-quality usb cable with excellent features and performance	🔊	t	t	4.60	313	2026-02-03 15:22:02.064172
60	1	1	Perfect Hard Drive Max	perfect-hard-drive-max-60	136.16	143.33	92	High-quality hard drive with excellent features and performance	⌨️	f	f	4.10	440	2026-02-03 15:22:02.064533
61	1	1	Professional Smartphone Edition	professional-smartphone-edition-61	78.00	78.00	66	High-quality smartphone with excellent features and performance	📷	f	f	4.80	112	2026-02-03 15:22:02.064853
62	1	1	Premium Tablet Series	premium-tablet-series-62	168.60	198.35	6	High-quality tablet with excellent features and performance	🎧	f	f	4.60	359	2026-02-03 15:22:02.065289
63	1	1	Ultra Laptop Grade	ultra-laptop-grade-63	92.00	115.00	94	High-quality laptop with excellent features and performance	⌚	f	f	4.00	368	2026-02-03 15:22:02.06567
64	1	1	Ultra Desktop Computer Performance	ultra-desktop-computer-performance-64	186.00	195.79	91	High-quality desktop computer with excellent features and performance	📷	t	f	4.80	332	2026-02-03 15:22:02.066135
65	1	1	Premium Monitor Model	premium-monitor-model-65	105.00	140.00	58	High-quality monitor with excellent features and performance	⌚	f	t	3.60	220	2026-02-03 15:22:02.066542
66	1	1	Modern Keyboard Max	modern-keyboard-max-66	77.99	86.66	56	High-quality keyboard with excellent features and performance	📡	f	f	4.00	36	2026-02-03 15:22:02.067029
67	1	1	Master Mouse Series	master-mouse-series-67	19.62	24.53	4	High-quality mouse with excellent features and performance	🖱️	f	f	4.60	18	2026-02-03 15:22:02.067538
68	1	1	Supreme Headphones Collection	supreme-headphones-collection-68	89.05	111.31	46	High-quality headphones with excellent features and performance	🖥️	f	t	4.10	257	2026-02-03 15:22:02.067885
69	1	8	Advanced Earbuds Edition	advanced-earbuds-edition-69	215.57	239.52	72	High-quality earbuds with excellent features and performance	⌚	t	t	3.50	374	2026-02-03 15:22:02.068204
70	1	1	Premium Speaker Series	premium-speaker-series-70	52.29	65.36	58	High-quality speaker with excellent features and performance	📱	f	f	4.30	429	2026-02-03 15:22:02.06858
71	1	6	Deluxe Smartwatch Collection	deluxe-smartwatch-collection-71	173.17	173.17	68	High-quality smartwatch with excellent features and performance	🔊	t	t	3.60	442	2026-02-03 15:22:02.068947
72	1	8	Deluxe Fitness Tracker Version	deluxe-fitness-tracker-version-72	139.57	174.46	63	High-quality fitness tracker with excellent features and performance	📡	f	f	3.70	75	2026-02-03 15:22:02.06921
73	1	8	Classic Camera Performance	classic-camera-performance-73	217.14	271.43	49	High-quality camera with excellent features and performance	📱	f	t	4.90	476	2026-02-03 15:22:02.069464
74	1	8	Essential Webcam Plus	essential-webcam-plus-74	85.64	122.34	13	High-quality webcam with excellent features and performance	🎧	t	f	3.60	10	2026-02-03 15:22:02.069742
75	1	8	Perfect Microphone Plus	perfect-microphone-plus-75	65.25	65.25	76	High-quality microphone with excellent features and performance	📱	f	f	4.20	438	2026-02-03 15:22:02.069996
76	1	1	Supreme Router Performance	supreme-router-performance-76	81.46	101.82	5	High-quality router with excellent features and performance	⌚	f	f	3.60	300	2026-02-03 15:22:02.070284
77	1	8	Perfect Power Bank Model	perfect-power-bank-model-77	66.13	66.13	49	High-quality power bank with excellent features and performance	🔊	f	f	4.90	208	2026-02-03 15:22:02.070528
78	1	8	Pro Charger Series	pro-charger-series-78	55.54	55.54	77	High-quality charger with excellent features and performance	🖱️	f	f	3.60	188	2026-02-03 15:22:02.070777
79	1	6	Pro USB Cable Collection	pro-usb-cable-collection-79	14.52	15.28	49	High-quality usb cable with excellent features and performance	⌚	f	f	5.00	498	2026-02-03 15:22:02.071085
80	1	6	Master Hard Drive Collection	master-hard-drive-collection-80	150.55	150.55	64	High-quality hard drive with excellent features and performance	⌨️	f	f	3.90	404	2026-02-03 15:22:02.071429
81	1	8	Premium Smartphone Plus	premium-smartphone-plus-81	295.00	347.06	39	High-quality smartphone with excellent features and performance	🖥️	f	f	3.50	382	2026-02-03 15:22:02.071801
82	1	6	Expert Tablet Collection	expert-tablet-collection-82	230.42	288.03	36	High-quality tablet with excellent features and performance	🔊	f	t	3.70	274	2026-02-03 15:22:02.072233
83	1	6	Professional Laptop Series	professional-laptop-series-83	124.00	137.78	33	High-quality laptop with excellent features and performance	⌨️	f	f	3.50	381	2026-02-03 15:22:02.072623
84	1	6	Deluxe Desktop Computer Quality	deluxe-desktop-computer-quality-84	81.00	115.71	85	High-quality desktop computer with excellent features and performance	📷	f	f	4.10	270	2026-02-03 15:22:02.073017
85	1	6	Master Monitor Plus	master-monitor-plus-85	271.00	271.00	26	High-quality monitor with excellent features and performance	🔊	f	f	3.50	263	2026-02-03 15:22:02.073477
86	1	6	Deluxe Keyboard Model	deluxe-keyboard-model-86	21.74	31.06	78	High-quality keyboard with excellent features and performance	🖱️	t	f	4.20	76	2026-02-03 15:22:02.07385
87	1	1	Professional Mouse Performance	professional-mouse-performance-87	21.29	30.41	20	High-quality mouse with excellent features and performance	⌨️	f	f	3.80	398	2026-02-03 15:22:02.074269
88	1	6	Essential Headphones Collection	essential-headphones-collection-88	113.20	150.93	6	High-quality headphones with excellent features and performance	⌨️	f	f	4.40	94	2026-02-03 15:22:02.074669
89	1	6	Advanced Earbuds Edition	advanced-earbuds-edition-89	108.85	155.50	9	High-quality earbuds with excellent features and performance	🔊	t	f	3.60	97	2026-02-03 15:22:02.075023
90	1	6	Modern Speaker Collection	modern-speaker-collection-90	216.88	255.15	36	High-quality speaker with excellent features and performance	🖱️	t	f	4.30	190	2026-02-03 15:22:02.075297
91	1	8	Pro Smartwatch Performance	pro-smartwatch-performance-91	220.01	314.30	65	High-quality smartwatch with excellent features and performance	🖱️	f	f	3.60	55	2026-02-03 15:22:02.075593
92	1	1	Luxury Fitness Tracker Collection	luxury-fitness-tracker-collection-92	68.94	91.92	62	High-quality fitness tracker with excellent features and performance	🔊	t	f	3.50	397	2026-02-03 15:22:02.075931
93	1	8	Ultra Camera Performance	ultra-camera-performance-93	296.00	422.86	17	High-quality camera with excellent features and performance	📷	f	f	5.00	304	2026-02-03 15:22:02.076246
94	1	1	Elite Webcam Plus	elite-webcam-plus-94	86.50	86.50	25	High-quality webcam with excellent features and performance	📷	f	f	3.80	154	2026-02-03 15:22:02.076686
95	1	6	Luxury Microphone Max	luxury-microphone-max-95	240.48	282.92	89	High-quality microphone with excellent features and performance	⌨️	f	f	4.50	122	2026-02-03 15:22:02.077104
96	1	6	Ultra Router Model	ultra-router-model-96	131.85	188.35	5	High-quality router with excellent features and performance	🖥️	f	f	4.00	452	2026-02-03 15:22:02.077518
97	1	1	Classic Power Bank Series	classic-power-bank-series-97	32.69	32.69	84	High-quality power bank with excellent features and performance	⌚	f	f	4.70	116	2026-02-03 15:22:02.077878
98	1	8	Premium Charger Series	premium-charger-series-98	21.54	28.72	84	High-quality charger with excellent features and performance	📱	f	t	3.50	283	2026-02-03 15:22:02.07829
99	1	1	Pro USB Cable Plus	pro-usb-cable-plus-99	14.19	18.92	78	High-quality usb cable with excellent features and performance	🖱️	f	f	4.70	65	2026-02-03 15:22:02.078586
100	1	1	Essential Hard Drive Version	essential-hard-drive-version-100	162.69	232.41	53	High-quality hard drive with excellent features and performance	📡	f	f	4.70	141	2026-02-03 15:22:02.079162
101	2	9	Supreme T-Shirt Quality	supreme-t-shirt-quality-101	47.80	47.80	80	High-quality t-shirt with excellent features and performance	👗	t	t	4.60	385	2026-02-03 15:22:02.079488
102	2	9	Deluxe Shirt Edition	deluxe-shirt-edition-102	27.83	37.10	11	High-quality shirt with excellent features and performance	👔	t	f	4.10	163	2026-02-03 15:22:02.079942
103	2	2	Supreme Jeans Max	supreme-jeans-max-103	53.18	62.57	31	High-quality jeans with excellent features and performance	👠	f	t	3.70	68	2026-02-03 15:22:02.080424
104	2	2	Supreme Pants Version	supreme-pants-version-104	63.85	63.85	34	High-quality pants with excellent features and performance	👗	f	f	4.30	332	2026-02-03 15:22:02.08085
105	2	2	Elite Shorts Max	elite-shorts-max-105	32.66	32.66	2	High-quality shorts with excellent features and performance	👟	t	f	4.80	420	2026-02-03 15:22:02.081279
106	2	7	Advanced Dress Edition	advanced-dress-edition-106	49.90	58.70	53	High-quality dress with excellent features and performance	🕶️	f	f	4.90	65	2026-02-03 15:22:02.081784
107	2	9	Master Skirt Grade	master-skirt-grade-107	39.53	46.50	95	High-quality skirt with excellent features and performance	👜	t	f	5.00	463	2026-02-03 15:22:02.082208
108	2	9	Elite Jacket Model	elite-jacket-model-108	123.21	176.01	34	High-quality jacket with excellent features and performance	🧢	f	t	3.90	404	2026-02-03 15:22:02.08268
109	2	7	Ultra Coat Grade	ultra-coat-grade-109	272.79	272.79	33	High-quality coat with excellent features and performance	👕	f	f	4.30	365	2026-02-03 15:22:02.083116
110	2	2	Perfect Sweater Grade	perfect-sweater-grade-110	58.18	58.18	43	High-quality sweater with excellent features and performance	🕶️	f	f	4.20	47	2026-02-03 15:22:02.083578
111	2	9	Deluxe Hoodie Max	deluxe-hoodie-max-111	42.70	44.95	32	High-quality hoodie with excellent features and performance	👞	f	f	4.40	99	2026-02-03 15:22:02.083998
112	2	2	Essential Sneakers Performance	essential-sneakers-performance-112	90.68	100.75	41	High-quality sneakers with excellent features and performance	🎒	f	t	4.20	475	2026-02-03 15:22:02.084347
113	2	7	Luxury Boots Series	luxury-boots-series-113	74.84	106.91	87	High-quality boots with excellent features and performance	🎒	f	t	4.60	248	2026-02-03 15:22:02.084659
114	2	2	Essential Sandals Grade	essential-sandals-grade-114	36.32	51.88	59	High-quality sandals with excellent features and performance	👜	f	f	4.00	37	2026-02-03 15:22:02.085103
115	2	7	Modern Heels Version	modern-heels-version-115	63.31	66.64	82	High-quality heels with excellent features and performance	👔	f	t	4.50	32	2026-02-03 15:22:02.085507
116	2	2	Classic Watch Series	classic-watch-series-116	68.26	80.30	4	High-quality watch with excellent features and performance	👔	f	f	5.00	315	2026-02-03 15:22:02.085957
117	2	9	Classic Sunglasses Max	classic-sunglasses-max-117	35.22	37.07	25	High-quality sunglasses with excellent features and performance	🎒	f	f	3.60	423	2026-02-03 15:22:02.08634
118	2	2	Master Hat Max	master-hat-max-118	48.43	48.43	3	High-quality hat with excellent features and performance	👜	f	f	4.00	426	2026-02-03 15:22:02.086806
119	2	2	Elite Scarf Plus	elite-scarf-plus-119	19.46	25.94	23	High-quality scarf with excellent features and performance	👕	t	f	3.90	491	2026-02-03 15:22:02.087229
120	2	9	Professional Handbag Max	professional-handbag-max-120	66.91	74.34	92	High-quality handbag with excellent features and performance	🕶️	t	t	5.00	43	2026-02-03 15:22:02.087931
121	2	2	Elite T-Shirt Series	elite-t-shirt-series-121	24.23	28.51	90	High-quality t-shirt with excellent features and performance	👞	f	f	4.70	482	2026-02-03 15:22:02.088439
122	2	2	Modern Shirt Series	modern-shirt-series-122	53.39	59.32	19	High-quality shirt with excellent features and performance	👔	f	f	4.20	316	2026-02-03 15:22:02.088879
123	2	9	Essential Jeans Collection	essential-jeans-collection-123	118.23	118.23	13	High-quality jeans with excellent features and performance	👠	f	f	4.40	145	2026-02-03 15:22:02.089188
124	2	9	Premium Pants Quality	premium-pants-quality-124	76.65	76.65	62	High-quality pants with excellent features and performance	🧢	f	t	5.00	215	2026-02-03 15:22:02.089506
125	2	2	Essential Shorts Version	essential-shorts-version-125	40.39	40.39	10	High-quality shorts with excellent features and performance	👗	f	f	3.80	430	2026-02-03 15:22:02.089812
126	2	9	Professional Dress Series	professional-dress-series-126	136.92	136.92	98	High-quality dress with excellent features and performance	👔	f	f	3.60	150	2026-02-03 15:22:02.090178
127	2	2	Professional Skirt Series	professional-skirt-series-127	64.96	64.96	49	High-quality skirt with excellent features and performance	👠	t	t	5.00	429	2026-02-03 15:22:02.090574
128	2	7	Luxury Jacket Max	luxury-jacket-max-128	77.45	86.05	88	High-quality jacket with excellent features and performance	👞	f	f	4.40	160	2026-02-03 15:22:02.0909
129	2	7	Ultra Coat Grade	ultra-coat-grade-129	103.12	103.12	28	High-quality coat with excellent features and performance	👕	f	f	4.80	278	2026-02-03 15:22:02.091263
130	2	7	Master Sweater Performance	master-sweater-performance-130	68.66	76.29	38	High-quality sweater with excellent features and performance	👗	f	f	3.50	269	2026-02-03 15:22:02.091586
131	2	9	Ultra Hoodie Performance	ultra-hoodie-performance-131	45.34	60.45	84	High-quality hoodie with excellent features and performance	👞	f	t	4.00	190	2026-02-03 15:22:02.09189
132	2	9	Ultra Sneakers Quality	ultra-sneakers-quality-132	80.79	107.72	21	High-quality sneakers with excellent features and performance	👟	f	t	4.60	476	2026-02-03 15:22:02.092143
133	2	2	Deluxe Boots Series	deluxe-boots-series-133	141.42	141.42	77	High-quality boots with excellent features and performance	🕶️	f	f	3.60	160	2026-02-03 15:22:02.092412
134	2	9	Modern Sandals Performance	modern-sandals-performance-134	37.29	37.29	78	High-quality sandals with excellent features and performance	👗	t	f	3.90	124	2026-02-03 15:22:02.092821
135	2	9	Master Heels Model	master-heels-model-135	151.75	151.75	11	High-quality heels with excellent features and performance	👜	f	f	4.10	486	2026-02-03 15:22:02.093312
136	2	9	Master Watch Edition	master-watch-edition-136	78.48	104.64	98	High-quality watch with excellent features and performance	🕶️	f	f	4.80	191	2026-02-03 15:22:02.093793
137	2	2	Ultra Sunglasses Model	ultra-sunglasses-model-137	29.23	38.97	85	High-quality sunglasses with excellent features and performance	👟	f	f	4.00	288	2026-02-03 15:22:02.094307
138	2	9	Advanced Hat Grade	advanced-hat-grade-138	50.92	50.92	3	High-quality hat with excellent features and performance	🧢	f	t	4.00	39	2026-02-03 15:22:02.094841
139	2	9	Elite Scarf Quality	elite-scarf-quality-139	15.84	19.80	27	High-quality scarf with excellent features and performance	🕶️	t	f	3.90	407	2026-02-03 15:22:02.09542
140	2	2	Luxury Handbag Edition	luxury-handbag-edition-140	151.43	216.33	24	High-quality handbag with excellent features and performance	👠	f	f	4.00	433	2026-02-03 15:22:02.095862
141	2	9	Luxury T-Shirt Edition	luxury-t-shirt-edition-141	29.94	29.94	37	High-quality t-shirt with excellent features and performance	🧢	f	t	4.30	426	2026-02-03 15:22:02.096185
142	2	9	Essential Shirt Max	essential-shirt-max-142	47.10	67.29	81	High-quality shirt with excellent features and performance	🎒	f	f	4.00	151	2026-02-03 15:22:02.096514
143	2	9	Pro Jeans Model	pro-jeans-model-143	34.80	46.40	61	High-quality jeans with excellent features and performance	👜	t	f	4.30	183	2026-02-03 15:22:02.096829
144	2	7	Professional Pants Max	professional-pants-max-144	70.29	70.29	88	High-quality pants with excellent features and performance	👕	f	f	3.80	200	2026-02-03 15:22:02.097117
145	2	2	Classic Shorts Collection	classic-shorts-collection-145	22.94	32.77	100	High-quality shorts with excellent features and performance	🎒	t	t	4.40	445	2026-02-03 15:22:02.097421
146	2	7	Perfect Dress Performance	perfect-dress-performance-146	121.08	142.45	10	High-quality dress with excellent features and performance	🎒	f	f	3.60	58	2026-02-03 15:22:02.097735
147	2	2	Advanced Skirt Version	advanced-skirt-version-147	34.60	34.60	50	High-quality skirt with excellent features and performance	👗	f	f	4.50	240	2026-02-03 15:22:02.098095
148	2	2	Pro Jacket Series	pro-jacket-series-148	106.28	118.09	88	High-quality jacket with excellent features and performance	👞	f	t	4.50	41	2026-02-03 15:22:02.098584
149	2	2	Supreme Coat Quality	supreme-coat-quality-149	81.48	116.40	49	High-quality coat with excellent features and performance	👔	f	t	4.50	34	2026-02-03 15:22:02.098973
150	2	9	Modern Sweater Version	modern-sweater-version-150	34.89	49.84	24	High-quality sweater with excellent features and performance	🧢	f	f	3.50	234	2026-02-03 15:22:02.099315
151	2	7	Pro Hoodie Series	pro-hoodie-series-151	61.84	88.34	3	High-quality hoodie with excellent features and performance	🧢	f	t	4.70	386	2026-02-03 15:22:02.099605
152	2	2	Supreme Sneakers Series	supreme-sneakers-series-152	69.37	77.08	52	High-quality sneakers with excellent features and performance	👗	f	f	3.90	46	2026-02-03 15:22:02.099965
153	2	2	Premium Boots Collection	premium-boots-collection-153	119.79	149.74	54	High-quality boots with excellent features and performance	👜	t	f	4.00	274	2026-02-03 15:22:02.100256
154	2	9	Deluxe Sandals Plus	deluxe-sandals-plus-154	65.87	65.87	1	High-quality sandals with excellent features and performance	🎒	f	f	3.50	31	2026-02-03 15:22:02.100503
155	2	7	Elite Heels Collection	elite-heels-collection-155	106.34	132.93	1	High-quality heels with excellent features and performance	🎒	f	f	4.20	437	2026-02-03 15:22:02.100729
156	2	7	Luxury Watch Quality	luxury-watch-quality-156	208.20	219.16	79	High-quality watch with excellent features and performance	👠	f	t	4.70	74	2026-02-03 15:22:02.100999
157	2	7	Perfect Sunglasses Collection	perfect-sunglasses-collection-157	108.44	127.58	13	High-quality sunglasses with excellent features and performance	🎒	f	f	4.70	180	2026-02-03 15:22:02.101419
158	2	2	Premium Hat Collection	premium-hat-collection-158	42.61	42.61	92	High-quality hat with excellent features and performance	👕	t	f	3.70	308	2026-02-03 15:22:02.102026
159	2	9	Elite Scarf Series	elite-scarf-series-159	23.17	28.96	47	High-quality scarf with excellent features and performance	🧢	f	t	3.80	219	2026-02-03 15:22:02.102495
160	2	9	Professional Handbag Max	professional-handbag-max-160	48.87	65.16	31	High-quality handbag with excellent features and performance	🎒	t	f	4.30	326	2026-02-03 15:22:02.102791
161	2	9	Modern T-Shirt Performance	modern-t-shirt-performance-161	34.81	40.95	43	High-quality t-shirt with excellent features and performance	👞	f	t	3.50	479	2026-02-03 15:22:02.103133
162	2	9	Deluxe Shirt Series	deluxe-shirt-series-162	37.87	37.87	71	High-quality shirt with excellent features and performance	🧢	f	f	3.50	361	2026-02-03 15:22:02.10344
163	2	9	Classic Jeans Performance	classic-jeans-performance-163	66.90	78.70	56	High-quality jeans with excellent features and performance	🕶️	t	f	5.00	24	2026-02-03 15:22:02.103804
164	2	2	Classic Pants Plus	classic-pants-plus-164	48.83	61.04	65	High-quality pants with excellent features and performance	👗	f	f	3.60	488	2026-02-03 15:22:02.104186
165	2	9	Professional Shorts Plus	professional-shorts-plus-165	17.60	20.70	21	High-quality shorts with excellent features and performance	👞	f	f	5.00	305	2026-02-03 15:22:02.104551
166	2	2	Professional Dress Collection	professional-dress-collection-166	109.95	109.95	22	High-quality dress with excellent features and performance	🎒	f	f	3.90	313	2026-02-03 15:22:02.104865
167	2	7	Modern Skirt Grade	modern-skirt-grade-167	35.25	44.06	45	High-quality skirt with excellent features and performance	👞	f	f	4.20	313	2026-02-03 15:22:02.105262
168	2	2	Deluxe Jacket Edition	deluxe-jacket-edition-168	193.35	193.35	93	High-quality jacket with excellent features and performance	🧢	f	f	4.30	488	2026-02-03 15:22:02.105597
169	2	7	Perfect Coat Edition	perfect-coat-edition-169	92.14	92.14	74	High-quality coat with excellent features and performance	👔	t	f	3.80	275	2026-02-03 15:22:02.106139
170	2	9	Elite Sweater Edition	elite-sweater-edition-170	36.57	40.63	88	High-quality sweater with excellent features and performance	🕶️	f	f	3.50	380	2026-02-03 15:22:02.10668
171	2	9	Essential Hoodie Model	essential-hoodie-model-171	57.82	57.82	52	High-quality hoodie with excellent features and performance	🎒	f	f	3.80	172	2026-02-03 15:22:02.107057
172	2	2	Advanced Sneakers Plus	advanced-sneakers-plus-172	104.59	104.59	88	High-quality sneakers with excellent features and performance	👞	t	f	4.90	403	2026-02-03 15:22:02.107399
173	2	9	Master Boots Edition	master-boots-edition-173	114.14	142.67	79	High-quality boots with excellent features and performance	👟	f	t	4.50	322	2026-02-03 15:22:02.107667
174	2	7	Professional Sandals Version	professional-sandals-version-174	27.48	27.48	32	High-quality sandals with excellent features and performance	👠	f	f	4.10	469	2026-02-03 15:22:02.108003
175	2	9	Luxury Heels Max	luxury-heels-max-175	89.07	98.97	55	High-quality heels with excellent features and performance	👔	f	f	4.80	423	2026-02-03 15:22:02.108272
176	2	7	Professional Watch Series	professional-watch-series-176	95.70	106.33	5	High-quality watch with excellent features and performance	👟	f	f	4.10	159	2026-02-03 15:22:02.108571
177	2	9	Supreme Sunglasses Plus	supreme-sunglasses-plus-177	120.67	120.67	13	High-quality sunglasses with excellent features and performance	👜	t	f	4.50	451	2026-02-03 15:22:02.108826
178	2	9	Supreme Hat Collection	supreme-hat-collection-178	37.09	49.45	69	High-quality hat with excellent features and performance	🧢	f	t	4.50	149	2026-02-03 15:22:02.10913
179	2	7	Expert Scarf Plus	expert-scarf-plus-179	21.31	30.44	2	High-quality scarf with excellent features and performance	🕶️	f	f	4.30	90	2026-02-03 15:22:02.109417
180	2	9	Master Handbag Collection	master-handbag-collection-180	63.09	90.13	33	High-quality handbag with excellent features and performance	👞	t	t	4.70	251	2026-02-03 15:22:02.109679
181	2	2	Expert T-Shirt Model	expert-t-shirt-model-181	14.42	20.60	67	High-quality t-shirt with excellent features and performance	🕶️	f	f	3.60	136	2026-02-03 15:22:02.109963
182	2	2	Pro Shirt Grade	pro-shirt-grade-182	45.88	53.98	32	High-quality shirt with excellent features and performance	👔	f	t	4.90	205	2026-02-03 15:22:02.110271
183	2	9	Pro Jeans Model	pro-jeans-model-183	97.31	108.12	58	High-quality jeans with excellent features and performance	👕	f	f	4.70	202	2026-02-03 15:22:02.110633
184	2	9	Luxury Pants Max	luxury-pants-max-184	87.59	97.32	30	High-quality pants with excellent features and performance	👞	f	f	4.80	37	2026-02-03 15:22:02.110916
185	2	7	Deluxe Shorts Series	deluxe-shorts-series-185	31.53	31.53	91	High-quality shorts with excellent features and performance	👜	f	f	3.90	367	2026-02-03 15:22:02.111227
186	2	9	Elite Dress Performance	elite-dress-performance-186	117.32	117.32	33	High-quality dress with excellent features and performance	🧢	f	t	4.20	225	2026-02-03 15:22:02.111529
187	2	9	Essential Skirt Edition	essential-skirt-edition-187	54.76	60.84	70	High-quality skirt with excellent features and performance	👕	f	f	4.60	131	2026-02-03 15:22:02.111882
188	2	2	Deluxe Jacket Model	deluxe-jacket-model-188	105.80	141.06	98	High-quality jacket with excellent features and performance	🕶️	f	f	3.60	111	2026-02-03 15:22:02.112285
189	2	2	Expert Coat Edition	expert-coat-edition-189	108.84	108.84	68	High-quality coat with excellent features and performance	👕	t	f	4.30	338	2026-02-03 15:22:02.112609
190	2	2	Deluxe Sweater Quality	deluxe-sweater-quality-190	77.92	86.58	30	High-quality sweater with excellent features and performance	👜	f	f	4.10	96	2026-02-03 15:22:02.112896
191	2	2	Luxury Hoodie Plus	luxury-hoodie-plus-191	34.48	45.97	24	High-quality hoodie with excellent features and performance	👔	f	t	3.60	51	2026-02-03 15:22:02.113186
192	2	9	Professional Sneakers Max	professional-sneakers-max-192	78.35	97.94	92	High-quality sneakers with excellent features and performance	👞	f	t	5.00	343	2026-02-03 15:22:02.113455
193	2	7	Deluxe Boots Plus	deluxe-boots-plus-193	126.54	168.72	71	High-quality boots with excellent features and performance	👜	f	f	4.30	177	2026-02-03 15:22:02.113933
194	2	2	Master Sandals Grade	master-sandals-grade-194	29.96	29.96	55	High-quality sandals with excellent features and performance	🎒	f	f	4.40	473	2026-02-03 15:22:02.114171
195	2	2	Expert Heels Plus	expert-heels-plus-195	168.14	176.99	64	High-quality heels with excellent features and performance	👞	f	f	4.10	373	2026-02-03 15:22:02.114464
196	2	9	Deluxe Watch Collection	deluxe-watch-collection-196	181.83	259.75	89	High-quality watch with excellent features and performance	👕	f	f	3.60	124	2026-02-03 15:22:02.114874
197	2	9	Ultra Sunglasses Performance	ultra-sunglasses-performance-197	30.06	31.64	43	High-quality sunglasses with excellent features and performance	👗	t	f	4.20	286	2026-02-03 15:22:02.115409
198	2	2	Essential Hat Series	essential-hat-series-198	20.55	24.18	6	High-quality hat with excellent features and performance	👔	f	f	3.80	233	2026-02-03 15:22:02.115749
199	2	7	Master Scarf Quality	master-scarf-quality-199	42.57	44.81	79	High-quality scarf with excellent features and performance	👗	t	t	4.50	171	2026-02-03 15:22:02.116134
200	2	9	Master Handbag Series	master-handbag-series-200	109.06	155.80	7	High-quality handbag with excellent features and performance	👜	f	f	3.50	402	2026-02-03 15:22:02.116484
201	3	10	Supreme Sofa Collection	supreme-sofa-collection-201	252.00	252.00	72	High-quality sofa with excellent features and performance	🖼️	f	f	3.80	362	2026-02-03 15:22:02.116792
202	3	10	Deluxe Chair Series	deluxe-chair-series-202	199.18	234.33	73	High-quality chair with excellent features and performance	🍽️	f	f	4.70	335	2026-02-03 15:22:02.117222
203	3	3	Advanced Table Plus	advanced-table-plus-203	278.00	292.63	37	High-quality table with excellent features and performance	🍴	f	f	4.80	446	2026-02-03 15:22:02.11758
204	3	10	Professional Bed Performance	professional-bed-performance-204	92.00	131.43	9	High-quality bed with excellent features and performance	🛏️	t	t	4.60	91	2026-02-03 15:22:02.117902
205	3	3	Master Mattress Quality	master-mattress-quality-205	258.75	258.75	28	High-quality mattress with excellent features and performance	🍴	f	f	3.70	178	2026-02-03 15:22:02.118203
206	3	3	Essential Pillow Collection	essential-pillow-collection-206	63.09	70.10	12	High-quality pillow with excellent features and performance	☕	f	f	4.30	453	2026-02-03 15:22:02.118601
207	3	3	Perfect Blanket Edition	perfect-blanket-edition-207	42.72	61.03	96	High-quality blanket with excellent features and performance	🪑	t	f	4.10	173	2026-02-03 15:22:02.118963
208	3	10	Perfect Curtains Series	perfect-curtains-series-208	84.48	105.60	80	High-quality curtains with excellent features and performance	🏠	f	f	3.70	227	2026-02-03 15:22:02.119195
209	3	3	Advanced Rug Model	advanced-rug-model-209	90.81	113.51	76	High-quality rug with excellent features and performance	🕐	t	f	4.40	358	2026-02-03 15:22:02.11942
210	3	10	Professional Lamp Model	professional-lamp-model-210	93.71	124.94	87	High-quality lamp with excellent features and performance	🛋️	f	f	4.10	154	2026-02-03 15:22:02.119675
211	3	10	Luxury Mirror Performance	luxury-mirror-performance-211	133.02	177.36	84	High-quality mirror with excellent features and performance	🕐	f	f	4.30	396	2026-02-03 15:22:02.119911
212	3	3	Premium Clock Model	premium-clock-model-212	56.41	70.51	88	High-quality clock with excellent features and performance	🖼️	f	f	3.70	28	2026-02-03 15:22:02.120242
213	3	3	Pro Vase Collection	pro-vase-collection-213	33.26	35.01	58	High-quality vase with excellent features and performance	☕	t	f	4.00	305	2026-02-03 15:22:02.120472
214	3	3	Elite Picture Frame Collection	elite-picture-frame-collection-214	29.78	39.71	25	High-quality picture frame with excellent features and performance	🪑	t	t	4.20	292	2026-02-03 15:22:02.120717
215	3	3	Premium Bookshelf Edition	premium-bookshelf-edition-215	282.00	282.00	12	High-quality bookshelf with excellent features and performance	💡	f	f	4.80	69	2026-02-03 15:22:02.121097
216	3	10	Elite Cabinet Model	elite-cabinet-model-216	82.00	82.00	84	High-quality cabinet with excellent features and performance	🛏️	f	f	4.20	281	2026-02-03 15:22:02.121329
217	3	10	Pro Coffee Maker Max	pro-coffee-maker-max-217	188.44	235.55	47	High-quality coffee maker with excellent features and performance	🍽️	t	f	3.50	299	2026-02-03 15:22:02.121552
218	3	10	Luxury Blender Grade	luxury-blender-grade-218	104.45	149.22	34	High-quality blender with excellent features and performance	☕	f	f	3.90	158	2026-02-03 15:22:02.121769
219	3	10	Elite Toaster Collection	elite-toaster-collection-219	53.50	53.50	28	High-quality toaster with excellent features and performance	🖼️	f	f	3.90	78	2026-02-03 15:22:02.12212
220	3	10	Supreme Microwave Version	supreme-microwave-version-220	234.47	293.09	40	High-quality microwave with excellent features and performance	🍴	f	f	4.60	300	2026-02-03 15:22:02.122459
221	3	10	Expert Sofa Model	expert-sofa-model-221	186.00	248.00	58	High-quality sofa with excellent features and performance	🪑	f	f	3.70	469	2026-02-03 15:22:02.122761
222	3	3	Master Chair Series	master-chair-series-222	235.89	294.86	72	High-quality chair with excellent features and performance	🏠	f	t	3.80	133	2026-02-03 15:22:02.123068
223	3	3	Master Table Collection	master-table-collection-223	183.00	183.00	22	High-quality table with excellent features and performance	💡	f	t	3.90	195	2026-02-03 15:22:02.123415
224	3	10	Modern Bed Edition	modern-bed-edition-224	274.00	391.43	61	High-quality bed with excellent features and performance	🪑	f	f	4.00	492	2026-02-03 15:22:02.123826
225	3	10	Essential Mattress Grade	essential-mattress-grade-225	73.00	91.25	14	High-quality mattress with excellent features and performance	🖼️	f	f	3.80	209	2026-02-03 15:22:02.124137
226	3	10	Master Pillow Version	master-pillow-version-226	74.04	74.04	19	High-quality pillow with excellent features and performance	🍽️	f	f	3.60	317	2026-02-03 15:22:02.124452
227	3	3	Perfect Blanket Model	perfect-blanket-model-227	81.48	81.48	13	High-quality blanket with excellent features and performance	🍽️	t	t	4.40	246	2026-02-03 15:22:02.124683
228	3	10	Perfect Curtains Plus	perfect-curtains-plus-228	45.65	50.72	53	High-quality curtains with excellent features and performance	🕐	t	f	3.70	270	2026-02-03 15:22:02.124906
229	3	3	Luxury Rug Max	luxury-rug-max-229	114.53	127.25	74	High-quality rug with excellent features and performance	🕐	f	f	4.80	310	2026-02-03 15:22:02.125161
230	3	3	Luxury Lamp Performance	luxury-lamp-performance-230	107.66	119.62	13	High-quality lamp with excellent features and performance	🍽️	f	f	3.50	186	2026-02-03 15:22:02.125385
231	3	3	Perfect Mirror Plus	perfect-mirror-plus-231	70.33	78.14	21	High-quality mirror with excellent features and performance	🍴	f	f	4.40	154	2026-02-03 15:22:02.125681
232	3	10	Modern Clock Performance	modern-clock-performance-232	62.05	62.05	1	High-quality clock with excellent features and performance	🪑	f	t	4.30	413	2026-02-03 15:22:02.125905
233	3	3	Classic Vase Collection	classic-vase-collection-233	30.63	38.29	27	High-quality vase with excellent features and performance	🍽️	f	t	3.50	444	2026-02-03 15:22:02.126131
234	3	3	Perfect Picture Frame Plus	perfect-picture-frame-plus-234	46.95	55.23	14	High-quality picture frame with excellent features and performance	🍽️	f	t	4.10	396	2026-02-03 15:22:02.126352
235	3	3	Modern Bookshelf Quality	modern-bookshelf-quality-235	183.27	183.27	68	High-quality bookshelf with excellent features and performance	🏠	f	f	4.40	120	2026-02-03 15:22:02.126573
236	3	10	Modern Cabinet Edition	modern-cabinet-edition-236	256.00	320.00	1	High-quality cabinet with excellent features and performance	💡	f	f	3.90	325	2026-02-03 15:22:02.126833
237	3	10	Modern Coffee Maker Performance	modern-coffee-maker-performance-237	98.90	141.29	25	High-quality coffee maker with excellent features and performance	🪑	f	f	3.60	383	2026-02-03 15:22:02.127096
238	3	3	Essential Blender Edition	essential-blender-edition-238	78.31	78.31	50	High-quality blender with excellent features and performance	💡	f	f	4.80	62	2026-02-03 15:22:02.127335
239	3	3	Premium Toaster Performance	premium-toaster-performance-239	67.36	70.90	38	High-quality toaster with excellent features and performance	🍽️	t	t	4.70	40	2026-02-03 15:22:02.127557
240	3	3	Ultra Microwave Performance	ultra-microwave-performance-240	244.06	287.13	97	High-quality microwave with excellent features and performance	🖼️	f	f	4.90	95	2026-02-03 15:22:02.127792
241	3	10	Professional Sofa Series	professional-sofa-series-241	116.00	136.47	39	High-quality sofa with excellent features and performance	🪑	f	f	4.50	335	2026-02-03 15:22:02.128043
242	3	10	Classic Chair Max	classic-chair-max-242	78.78	98.47	69	High-quality chair with excellent features and performance	🏠	f	f	5.00	444	2026-02-03 15:22:02.128351
243	3	3	Perfect Table Max	perfect-table-max-243	71.00	83.53	80	High-quality table with excellent features and performance	🛋️	f	f	4.80	420	2026-02-03 15:22:02.128578
244	3	10	Deluxe Bed Grade	deluxe-bed-grade-244	102.00	127.50	93	High-quality bed with excellent features and performance	🪑	f	f	4.10	338	2026-02-03 15:22:02.128811
245	3	10	Premium Mattress Performance	premium-mattress-performance-245	258.93	258.93	13	High-quality mattress with excellent features and performance	🕐	f	f	3.50	356	2026-02-03 15:22:02.129059
246	3	3	Premium Pillow Series	premium-pillow-series-246	37.56	46.95	21	High-quality pillow with excellent features and performance	☕	f	t	4.90	360	2026-02-03 15:22:02.129633
247	3	10	Master Blanket Plus	master-blanket-plus-247	62.40	62.40	60	High-quality blanket with excellent features and performance	🍴	f	t	4.10	441	2026-02-03 15:22:02.130097
248	3	10	Perfect Curtains Series	perfect-curtains-series-248	96.61	96.61	87	High-quality curtains with excellent features and performance	☕	f	t	3.70	498	2026-02-03 15:22:02.130584
249	3	3	Elite Rug Plus	elite-rug-plus-249	94.88	94.88	61	High-quality rug with excellent features and performance	💡	f	t	5.00	92	2026-02-03 15:22:02.131102
250	3	10	Essential Lamp Max	essential-lamp-max-250	37.49	53.56	74	High-quality lamp with excellent features and performance	🖼️	f	f	4.80	480	2026-02-03 15:22:02.131569
251	3	3	Essential Mirror Edition	essential-mirror-edition-251	655.00	655.00	56	High-quality mirror with excellent features and performance	💡	f	f	4.90	452	2026-02-03 15:22:02.132043
252	3	3	Perfect Clock Plus	perfect-clock-plus-252	823.00	1028.75	93	High-quality clock with excellent features and performance	🖼️	f	f	4.10	101	2026-02-03 15:22:02.132467
253	3	10	Premium Vase Plus	premium-vase-plus-253	302.00	302.00	99	High-quality vase with excellent features and performance	🍴	f	f	4.90	284	2026-02-03 15:22:02.132907
254	3	10	Luxury Picture Frame Collection	luxury-picture-frame-collection-254	498.00	622.50	74	High-quality picture frame with excellent features and performance	🕐	f	f	3.60	262	2026-02-03 15:22:02.13331
255	3	3	Elite Bookshelf Model	elite-bookshelf-model-255	646.00	646.00	71	High-quality bookshelf with excellent features and performance	🛏️	t	f	3.80	492	2026-02-03 15:22:02.133737
256	3	3	Essential Cabinet Series	essential-cabinet-series-256	371.06	371.06	84	High-quality cabinet with excellent features and performance	🕐	f	f	4.70	31	2026-02-03 15:22:02.134027
257	3	3	Supreme Coffee Maker Performance	supreme-coffee-maker-performance-257	469.00	469.00	17	High-quality coffee maker with excellent features and performance	🛏️	f	f	4.10	298	2026-02-03 15:22:02.134311
258	3	3	Premium Blender Edition	premium-blender-edition-258	501.00	501.00	14	High-quality blender with excellent features and performance	💡	f	f	4.00	322	2026-02-03 15:22:02.134634
259	3	10	Elite Toaster Performance	elite-toaster-performance-259	902.00	949.47	28	High-quality toaster with excellent features and performance	🛏️	f	t	4.80	129	2026-02-03 15:22:02.134925
260	3	10	Master Microwave Plus	master-microwave-plus-260	998.00	1174.12	77	High-quality microwave with excellent features and performance	🕐	f	t	4.90	135	2026-02-03 15:22:02.135174
261	3	3	Master Sofa Series	master-sofa-series-261	852.40	852.40	62	High-quality sofa with excellent features and performance	🍴	f	t	3.90	299	2026-02-03 15:22:02.135427
262	3	10	Premium Chair Max	premium-chair-max-262	687.00	981.43	91	High-quality chair with excellent features and performance	🍴	f	f	3.50	151	2026-02-03 15:22:02.135735
263	3	3	Essential Table Performance	essential-table-performance-263	408.75	480.88	61	High-quality table with excellent features and performance	🕐	f	t	4.30	241	2026-02-03 15:22:02.136038
264	3	3	Advanced Bed Grade	advanced-bed-grade-264	760.70	894.94	68	High-quality bed with excellent features and performance	🛋️	f	f	4.00	34	2026-02-03 15:22:02.136395
265	3	3	Professional Mattress Series	professional-mattress-series-265	487.75	487.75	88	High-quality mattress with excellent features and performance	🍽️	f	f	4.70	323	2026-02-03 15:22:02.136687
266	3	3	Pro Pillow Collection	pro-pillow-collection-266	634.00	634.00	64	High-quality pillow with excellent features and performance	🖼️	f	f	4.30	286	2026-02-03 15:22:02.136981
267	3	10	Professional Blanket Max	professional-blanket-max-267	438.00	486.67	76	High-quality blanket with excellent features and performance	🪑	f	t	3.50	85	2026-02-03 15:22:02.137337
268	3	3	Elite Curtains Plus	elite-curtains-plus-268	476.00	560.00	20	High-quality curtains with excellent features and performance	🏠	t	f	3.80	40	2026-02-03 15:22:02.13769
269	3	10	Supreme Rug Version	supreme-rug-version-269	680.00	906.67	21	High-quality rug with excellent features and performance	🛋️	f	f	4.70	176	2026-02-03 15:22:02.137982
270	3	10	Ultra Lamp Max	ultra-lamp-max-270	712.00	712.00	70	High-quality lamp with excellent features and performance	🍽️	f	f	4.30	130	2026-02-03 15:22:02.138273
271	3	3	Master Mirror Performance	master-mirror-performance-271	319.00	455.71	29	High-quality mirror with excellent features and performance	🖼️	f	f	4.90	85	2026-02-03 15:22:02.138639
272	3	10	Deluxe Clock Version	deluxe-clock-version-272	735.00	1050.00	50	High-quality clock with excellent features and performance	💡	f	f	3.70	56	2026-02-03 15:22:02.138999
273	3	3	Expert Vase Grade	expert-vase-grade-273	951.00	951.00	19	High-quality vase with excellent features and performance	🛋️	f	f	4.50	277	2026-02-03 15:22:02.139291
274	3	10	Master Picture Frame Collection	master-picture-frame-collection-274	840.00	988.24	47	High-quality picture frame with excellent features and performance	🏠	f	f	4.40	319	2026-02-03 15:22:02.139575
275	3	10	Ultra Bookshelf Performance	ultra-bookshelf-performance-275	513.00	513.00	49	High-quality bookshelf with excellent features and performance	🕐	f	f	3.90	282	2026-02-03 15:22:02.139843
276	3	3	Elite Cabinet Quality	elite-cabinet-quality-276	300.00	375.00	30	High-quality cabinet with excellent features and performance	🛏️	f	f	4.80	196	2026-02-03 15:22:02.14008
277	3	3	Premium Coffee Maker Series	premium-coffee-maker-series-277	336.00	373.33	1	High-quality coffee maker with excellent features and performance	☕	f	f	4.60	73	2026-02-03 15:22:02.140337
278	3	3	Master Blender Series	master-blender-series-278	961.00	961.00	62	High-quality blender with excellent features and performance	🏠	f	t	4.40	442	2026-02-03 15:22:02.14058
279	3	3	Modern Toaster Performance	modern-toaster-performance-279	883.00	883.00	44	High-quality toaster with excellent features and performance	🍴	f	f	5.00	341	2026-02-03 15:22:02.140853
280	3	3	Pro Microwave Collection	pro-microwave-collection-280	817.00	817.00	95	High-quality microwave with excellent features and performance	🪑	f	f	4.00	250	2026-02-03 15:22:02.141148
281	3	3	Classic Sofa Quality	classic-sofa-quality-281	1148.40	1148.40	45	High-quality sofa with excellent features and performance	🕐	f	f	5.00	366	2026-02-03 15:22:02.141378
282	3	3	Premium Chair Max	premium-chair-max-282	610.00	813.33	76	High-quality chair with excellent features and performance	🛋️	f	f	4.00	290	2026-02-03 15:22:02.141612
283	3	3	Deluxe Table Edition	deluxe-table-edition-283	791.00	1054.67	23	High-quality table with excellent features and performance	🍴	t	f	4.70	135	2026-02-03 15:22:02.141873
284	3	10	Elite Bed Performance	elite-bed-performance-284	418.21	522.76	71	High-quality bed with excellent features and performance	🍴	f	f	3.50	56	2026-02-03 15:22:02.142096
285	3	3	Elite Mattress Model	elite-mattress-model-285	603.00	709.41	66	High-quality mattress with excellent features and performance	🕐	f	f	4.80	470	2026-02-03 15:22:02.142362
286	3	3	Classic Pillow Max	classic-pillow-max-286	487.00	695.71	31	High-quality pillow with excellent features and performance	🏠	f	f	4.60	163	2026-02-03 15:22:02.142646
287	3	10	Perfect Blanket Plus	perfect-blanket-plus-287	886.00	1042.35	8	High-quality blanket with excellent features and performance	🏠	f	f	5.00	414	2026-02-03 15:22:02.143093
288	3	3	Modern Curtains Collection	modern-curtains-collection-288	407.00	407.00	69	High-quality curtains with excellent features and performance	🛏️	f	t	4.60	238	2026-02-03 15:22:02.143428
289	3	10	Expert Rug Plus	expert-rug-plus-289	750.00	750.00	43	High-quality rug with excellent features and performance	💡	f	f	5.00	269	2026-02-03 15:22:02.143655
290	3	3	Premium Lamp Performance	premium-lamp-performance-290	979.00	1305.33	94	High-quality lamp with excellent features and performance	🖼️	t	t	4.80	13	2026-02-03 15:22:02.143958
291	3	10	Modern Mirror Quality	modern-mirror-quality-291	393.00	561.43	35	High-quality mirror with excellent features and performance	💡	f	f	3.60	112	2026-02-03 15:22:02.144259
292	3	3	Ultra Clock Model	ultra-clock-model-292	684.00	684.00	7	High-quality clock with excellent features and performance	🖼️	t	f	4.80	162	2026-02-03 15:22:02.14459
293	3	3	Perfect Vase Max	perfect-vase-max-293	627.00	895.71	99	High-quality vase with excellent features and performance	🕐	t	t	4.10	325	2026-02-03 15:22:02.145148
294	3	10	Supreme Picture Frame Version	supreme-picture-frame-version-294	474.00	526.67	45	High-quality picture frame with excellent features and performance	☕	t	t	3.60	178	2026-02-03 15:22:02.14551
295	3	10	Professional Bookshelf Edition	professional-bookshelf-edition-295	790.00	831.58	19	High-quality bookshelf with excellent features and performance	🍴	f	t	3.60	46	2026-02-03 15:22:02.145796
296	3	3	Essential Cabinet Max	essential-cabinet-max-296	631.00	631.00	17	High-quality cabinet with excellent features and performance	💡	f	f	4.50	451	2026-02-03 15:22:02.146033
297	3	10	Advanced Coffee Maker Collection	advanced-coffee-maker-collection-297	384.00	384.00	78	High-quality coffee maker with excellent features and performance	🛋️	f	f	4.70	281	2026-02-03 15:22:02.146259
298	3	10	Essential Blender Performance	essential-blender-performance-298	359.00	422.35	1	High-quality blender with excellent features and performance	🏠	f	t	4.40	493	2026-02-03 15:22:02.146486
299	3	3	Premium Toaster Edition	premium-toaster-edition-299	607.00	607.00	91	High-quality toaster with excellent features and performance	☕	f	f	3.50	328	2026-02-03 15:22:02.146731
300	3	10	Classic Microwave Edition	classic-microwave-edition-300	396.00	416.84	79	High-quality microwave with excellent features and performance	🖼️	f	f	3.70	57	2026-02-03 15:22:02.146975
301	4	11	Classic Running Shoes Performance	classic-running-shoes-performance-301	712.00	791.11	66	High-quality running shoes with excellent features and performance	🏀	f	t	5.00	316	2026-02-03 15:22:02.147315
302	4	4	Premium Yoga Mat Version	premium-yoga-mat-version-302	765.00	805.26	20	High-quality yoga mat with excellent features and performance	⚽	f	f	4.80	453	2026-02-03 15:22:02.147701
303	4	11	Ultra Dumbbells Edition	ultra-dumbbells-edition-303	928.00	1237.33	37	High-quality dumbbells with excellent features and performance	🏊	f	t	4.80	210	2026-02-03 15:22:02.148013
304	4	11	Supreme Resistance Bands Model	supreme-resistance-bands-model-304	617.00	649.47	21	High-quality resistance bands with excellent features and performance	🏀	t	t	4.50	325	2026-02-03 15:22:02.148293
305	4	11	Modern Jump Rope Plus	modern-jump-rope-plus-305	426.00	426.00	63	High-quality jump rope with excellent features and performance	🧘	t	f	3.60	372	2026-02-03 15:22:02.148559
306	4	4	Professional Gym Bag Performance	professional-gym-bag-performance-306	471.00	471.00	57	High-quality gym bag with excellent features and performance	⚽	f	t	4.90	199	2026-02-03 15:22:02.148811
307	4	4	Modern Water Bottle Model	modern-water-bottle-model-307	786.00	873.33	38	High-quality water bottle with excellent features and performance	⛳	f	f	4.20	380	2026-02-03 15:22:02.149053
308	4	4	Modern Protein Shaker Edition	modern-protein-shaker-edition-308	492.00	702.86	100	High-quality protein shaker with excellent features and performance	🏋️	f	f	3.60	447	2026-02-03 15:22:02.149339
309	4	11	Supreme Fitness Tracker Version	supreme-fitness-tracker-version-309	958.00	1127.06	8	High-quality fitness tracker with excellent features and performance	🚴	f	t	3.70	437	2026-02-03 15:22:02.149647
310	4	11	Ultra Bicycle Performance	ultra-bicycle-performance-310	469.00	586.25	13	High-quality bicycle with excellent features and performance	🎾	t	f	3.80	331	2026-02-03 15:22:02.149944
311	4	11	Deluxe Helmet Grade	deluxe-helmet-grade-311	975.00	975.00	9	High-quality helmet with excellent features and performance	🏋️	t	f	4.10	174	2026-02-03 15:22:02.150231
312	4	4	Deluxe Tennis Racket Plus	deluxe-tennis-racket-plus-312	334.00	392.94	44	High-quality tennis racket with excellent features and performance	🥊	f	t	4.00	479	2026-02-03 15:22:02.150529
313	4	4	Expert Basketball Series	expert-basketball-series-313	478.00	682.86	96	High-quality basketball with excellent features and performance	🏀	t	f	4.20	300	2026-02-03 15:22:02.15077
314	4	4	Pro Football Collection	pro-football-collection-314	844.00	844.00	93	High-quality football with excellent features and performance	⛳	f	f	3.80	368	2026-02-03 15:22:02.151013
315	4	11	Modern Soccer Ball Max	modern-soccer-ball-max-315	736.00	920.00	30	High-quality soccer ball with excellent features and performance	🥊	f	f	4.30	238	2026-02-03 15:22:02.151244
316	4	4	Elite Baseball Bat Quality	elite-baseball-bat-quality-316	362.00	517.14	68	High-quality baseball bat with excellent features and performance	🏋️	f	f	3.80	68	2026-02-03 15:22:02.15147
317	4	11	Deluxe Golf Clubs Grade	deluxe-golf-clubs-grade-317	791.00	791.00	48	High-quality golf clubs with excellent features and performance	🏊	f	f	4.40	323	2026-02-03 15:22:02.151693
318	4	11	Professional Swimming Goggles Collection	professional-swimming-goggles-collection-318	390.00	390.00	60	High-quality swimming goggles with excellent features and performance	🏀	t	f	4.60	444	2026-02-03 15:22:02.151927
319	4	4	Advanced Yoga Blocks Performance	advanced-yoga-blocks-performance-319	542.00	602.22	44	High-quality yoga blocks with excellent features and performance	🚴	f	f	4.90	47	2026-02-03 15:22:02.152156
320	4	11	Classic Foam Roller Series	classic-foam-roller-series-320	988.00	1411.43	87	High-quality foam roller with excellent features and performance	⚽	f	f	3.70	146	2026-02-03 15:22:02.152404
473	5	12	Elite Art Book Max	elite-art-book-max-473	814.00	1162.86	83	High-quality art book with excellent features and performance	📔	f	t	4.10	207	2026-02-03 15:22:02.208632
321	4	11	Classic Running Shoes Version	classic-running-shoes-version-321	583.00	728.75	51	High-quality running shoes with excellent features and performance	🏈	t	t	4.50	420	2026-02-03 15:22:02.152626
322	4	11	Supreme Yoga Mat Version	supreme-yoga-mat-version-322	846.00	1057.50	74	High-quality yoga mat with excellent features and performance	🥊	f	t	4.20	290	2026-02-03 15:22:02.152957
323	4	4	Pro Dumbbells Grade	pro-dumbbells-grade-323	449.00	528.24	9	High-quality dumbbells with excellent features and performance	🥊	f	f	3.60	339	2026-02-03 15:22:02.153199
324	4	11	Professional Resistance Bands Performance	professional-resistance-bands-performance-324	588.00	588.00	82	High-quality resistance bands with excellent features and performance	⛳	f	t	4.50	104	2026-02-03 15:22:02.153421
325	4	4	Luxury Jump Rope Edition	luxury-jump-rope-edition-325	342.00	456.00	67	High-quality jump rope with excellent features and performance	🎾	f	f	3.60	245	2026-02-03 15:22:02.153642
326	4	4	Premium Gym Bag Edition	premium-gym-bag-edition-326	440.00	440.00	5	High-quality gym bag with excellent features and performance	🧘	f	f	4.80	141	2026-02-03 15:22:02.15388
327	4	4	Master Water Bottle Version	master-water-bottle-version-327	301.00	316.84	57	High-quality water bottle with excellent features and performance	🏀	f	f	5.00	227	2026-02-03 15:22:02.154098
328	4	11	Deluxe Protein Shaker Model	deluxe-protein-shaker-model-328	412.00	433.68	92	High-quality protein shaker with excellent features and performance	⛳	f	f	4.50	249	2026-02-03 15:22:02.154342
329	4	4	Master Fitness Tracker Model	master-fitness-tracker-model-329	506.00	632.50	92	High-quality fitness tracker with excellent features and performance	🎾	f	f	3.50	295	2026-02-03 15:22:02.154608
330	4	4	Essential Bicycle Series	essential-bicycle-series-330	341.76	455.68	40	High-quality bicycle with excellent features and performance	🏋️	f	f	3.70	323	2026-02-03 15:22:02.15484
331	4	4	Professional Helmet Model	professional-helmet-model-331	376.00	376.00	40	High-quality helmet with excellent features and performance	🚴	f	f	4.90	177	2026-02-03 15:22:02.155067
332	4	11	Perfect Tennis Racket Model	perfect-tennis-racket-model-332	538.00	566.32	94	High-quality tennis racket with excellent features and performance	🎾	f	t	3.70	86	2026-02-03 15:22:02.15534
333	4	4	Elite Basketball Max	elite-basketball-max-333	966.00	1207.50	4	High-quality basketball with excellent features and performance	🎾	f	f	4.60	94	2026-02-03 15:22:02.155608
334	4	4	Luxury Football Grade	luxury-football-grade-334	507.00	563.33	3	High-quality football with excellent features and performance	🏀	f	t	5.00	235	2026-02-03 15:22:02.155835
335	4	11	Essential Soccer Ball Series	essential-soccer-ball-series-335	652.00	815.00	80	High-quality soccer ball with excellent features and performance	🏀	f	f	4.40	59	2026-02-03 15:22:02.156059
336	4	4	Professional Baseball Bat Grade	professional-baseball-bat-grade-336	629.00	740.00	74	High-quality baseball bat with excellent features and performance	🎾	f	t	4.70	475	2026-02-03 15:22:02.156281
337	4	4	Modern Golf Clubs Version	modern-golf-clubs-version-337	549.37	549.37	58	High-quality golf clubs with excellent features and performance	🧘	t	f	4.20	63	2026-02-03 15:22:02.156536
338	4	4	Premium Swimming Goggles Model	premium-swimming-goggles-model-338	784.00	784.00	62	High-quality swimming goggles with excellent features and performance	🏊	f	f	3.60	281	2026-02-03 15:22:02.15681
339	4	4	Expert Yoga Blocks Model	expert-yoga-blocks-model-339	795.00	836.84	34	High-quality yoga blocks with excellent features and performance	🎾	f	t	4.80	475	2026-02-03 15:22:02.157092
340	4	4	Supreme Foam Roller Series	supreme-foam-roller-series-340	797.00	1138.57	48	High-quality foam roller with excellent features and performance	⚽	f	f	4.60	211	2026-02-03 15:22:02.157483
341	4	11	Supreme Running Shoes Quality	supreme-running-shoes-quality-341	683.00	910.67	27	High-quality running shoes with excellent features and performance	⛳	f	t	3.50	126	2026-02-03 15:22:02.157822
342	4	4	Professional Yoga Mat Max	professional-yoga-mat-max-342	335.00	352.63	80	High-quality yoga mat with excellent features and performance	🥊	f	f	4.80	339	2026-02-03 15:22:02.158143
343	4	11	Supreme Dumbbells Model	supreme-dumbbells-model-343	880.00	880.00	49	High-quality dumbbells with excellent features and performance	🧘	t	t	4.40	440	2026-02-03 15:22:02.158528
344	4	11	Pro Resistance Bands Quality	pro-resistance-bands-quality-344	429.00	451.58	73	High-quality resistance bands with excellent features and performance	⚽	t	f	3.60	162	2026-02-03 15:22:02.158877
345	4	11	Essential Jump Rope Model	essential-jump-rope-model-345	327.00	408.75	93	High-quality jump rope with excellent features and performance	🚴	f	f	4.30	337	2026-02-03 15:22:02.15927
346	4	4	Elite Gym Bag Plus	elite-gym-bag-plus-346	428.00	428.00	28	High-quality gym bag with excellent features and performance	🚴	t	f	3.60	146	2026-02-03 15:22:02.159652
347	4	4	Deluxe Water Bottle Edition	deluxe-water-bottle-edition-347	858.00	858.00	69	High-quality water bottle with excellent features and performance	🏈	f	f	4.20	465	2026-02-03 15:22:02.159992
348	4	11	Expert Protein Shaker Model	expert-protein-shaker-model-348	890.00	890.00	84	High-quality protein shaker with excellent features and performance	🥊	f	f	4.30	362	2026-02-03 15:22:02.160347
349	4	4	Master Fitness Tracker Version	master-fitness-tracker-version-349	891.00	891.00	48	High-quality fitness tracker with excellent features and performance	🥊	f	f	4.20	10	2026-02-03 15:22:02.160919
350	4	4	Supreme Bicycle Max	supreme-bicycle-max-350	337.39	449.85	29	High-quality bicycle with excellent features and performance	🚴	t	f	4.70	333	2026-02-03 15:22:02.161362
351	4	11	Essential Helmet Plus	essential-helmet-plus-351	483.00	690.00	19	High-quality helmet with excellent features and performance	🚴	f	t	4.20	314	2026-02-03 15:22:02.161703
352	4	4	Elite Tennis Racket Plus	elite-tennis-racket-plus-352	443.00	521.18	42	High-quality tennis racket with excellent features and performance	🏋️	f	f	4.50	98	2026-02-03 15:22:02.162012
353	4	11	Supreme Basketball Model	supreme-basketball-model-353	876.00	876.00	82	High-quality basketball with excellent features and performance	🎾	f	t	4.90	499	2026-02-03 15:22:02.162554
354	4	4	Deluxe Football Series	deluxe-football-series-354	302.00	317.89	32	High-quality football with excellent features and performance	⛳	f	f	4.40	34	2026-02-03 15:22:02.162925
355	4	11	Luxury Soccer Ball Edition	luxury-soccer-ball-edition-355	499.00	554.44	19	High-quality soccer ball with excellent features and performance	🥊	f	f	4.20	197	2026-02-03 15:22:02.163356
356	4	11	Pro Baseball Bat Grade	pro-baseball-bat-grade-356	690.00	985.71	33	High-quality baseball bat with excellent features and performance	⛳	f	f	5.00	15	2026-02-03 15:22:02.163917
357	4	4	Classic Golf Clubs Max	classic-golf-clubs-max-357	443.67	554.59	66	High-quality golf clubs with excellent features and performance	🚴	t	t	3.80	464	2026-02-03 15:22:02.164359
358	4	4	Expert Swimming Goggles Quality	expert-swimming-goggles-quality-358	395.00	395.00	94	High-quality swimming goggles with excellent features and performance	🧘	f	f	4.70	91	2026-02-03 15:22:02.164797
359	4	11	Master Yoga Blocks Collection	master-yoga-blocks-collection-359	775.00	775.00	56	High-quality yoga blocks with excellent features and performance	🥊	f	f	4.40	250	2026-02-03 15:22:02.165307
360	4	4	Premium Foam Roller Quality	premium-foam-roller-quality-360	855.00	1068.75	19	High-quality foam roller with excellent features and performance	🧘	f	t	3.60	147	2026-02-03 15:22:02.16582
361	4	11	Luxury Running Shoes Grade	luxury-running-shoes-grade-361	585.00	585.00	68	High-quality running shoes with excellent features and performance	⛳	f	f	4.20	244	2026-02-03 15:22:02.166324
362	4	4	Professional Yoga Mat Performance	professional-yoga-mat-performance-362	393.00	491.25	81	High-quality yoga mat with excellent features and performance	🎾	t	f	4.80	15	2026-02-03 15:22:02.166794
363	4	11	Pro Dumbbells Plus	pro-dumbbells-plus-363	958.00	1277.33	12	High-quality dumbbells with excellent features and performance	🎾	f	f	3.90	343	2026-02-03 15:22:02.167212
364	4	11	Ultra Resistance Bands Performance	ultra-resistance-bands-performance-364	853.00	1003.53	98	High-quality resistance bands with excellent features and performance	🚴	f	f	3.60	59	2026-02-03 15:22:02.167627
365	4	4	Pro Jump Rope Version	pro-jump-rope-version-365	973.00	1144.71	35	High-quality jump rope with excellent features and performance	🏊	f	f	4.10	380	2026-02-03 15:22:02.168009
366	4	4	Deluxe Gym Bag Performance	deluxe-gym-bag-performance-366	610.00	610.00	23	High-quality gym bag with excellent features and performance	⛳	t	f	4.90	222	2026-02-03 15:22:02.168352
367	4	4	Professional Water Bottle Model	professional-water-bottle-model-367	945.00	945.00	2	High-quality water bottle with excellent features and performance	⛳	t	f	3.70	392	2026-02-03 15:22:02.168628
368	4	4	Professional Protein Shaker Model	professional-protein-shaker-model-368	453.00	566.25	94	High-quality protein shaker with excellent features and performance	🎾	f	t	4.90	185	2026-02-03 15:22:02.169086
369	4	11	Elite Fitness Tracker Grade	elite-fitness-tracker-grade-369	547.00	547.00	93	High-quality fitness tracker with excellent features and performance	🧘	f	t	4.00	386	2026-02-03 15:22:02.169563
370	4	4	Classic Bicycle Max	classic-bicycle-max-370	304.84	381.05	60	High-quality bicycle with excellent features and performance	⚽	f	f	3.80	86	2026-02-03 15:22:02.16991
371	4	4	Expert Helmet Max	expert-helmet-max-371	886.00	1042.35	18	High-quality helmet with excellent features and performance	🏊	f	f	3.80	169	2026-02-03 15:22:02.170258
372	4	11	Essential Tennis Racket Quality	essential-tennis-racket-quality-372	608.00	608.00	93	High-quality tennis racket with excellent features and performance	🎾	f	f	4.10	274	2026-02-03 15:22:02.170589
373	4	4	Professional Basketball Model	professional-basketball-model-373	690.00	726.32	27	High-quality basketball with excellent features and performance	🧘	f	f	4.80	398	2026-02-03 15:22:02.17102
374	4	4	Luxury Football Edition	luxury-football-edition-374	326.00	383.53	90	High-quality football with excellent features and performance	🏈	f	f	4.90	82	2026-02-03 15:22:02.171288
375	4	11	Ultra Soccer Ball Series	ultra-soccer-ball-series-375	885.00	1106.25	98	High-quality soccer ball with excellent features and performance	🏊	t	t	4.40	404	2026-02-03 15:22:02.171537
376	4	11	Advanced Baseball Bat Performance	advanced-baseball-bat-performance-376	793.00	793.00	76	High-quality baseball bat with excellent features and performance	🚴	f	f	4.80	382	2026-02-03 15:22:02.171825
377	4	4	Essential Golf Clubs Quality	essential-golf-clubs-quality-377	419.58	524.47	71	High-quality golf clubs with excellent features and performance	⛳	f	f	4.00	173	2026-02-03 15:22:02.172154
378	4	4	Expert Swimming Goggles Series	expert-swimming-goggles-series-378	723.00	964.00	66	High-quality swimming goggles with excellent features and performance	🧘	t	f	4.20	317	2026-02-03 15:22:02.172425
379	4	4	Perfect Yoga Blocks Performance	perfect-yoga-blocks-performance-379	617.00	617.00	45	High-quality yoga blocks with excellent features and performance	🏈	f	t	3.60	184	2026-02-03 15:22:02.172806
380	4	4	Advanced Foam Roller Series	advanced-foam-roller-series-380	631.00	701.11	58	High-quality foam roller with excellent features and performance	🏀	f	f	3.90	336	2026-02-03 15:22:02.17336
381	4	4	Essential Running Shoes Version	essential-running-shoes-version-381	305.00	338.89	90	High-quality running shoes with excellent features and performance	🏀	f	f	4.10	315	2026-02-03 15:22:02.173985
382	4	11	Ultra Yoga Mat Max	ultra-yoga-mat-max-382	746.00	828.89	13	High-quality yoga mat with excellent features and performance	🏊	f	f	4.10	63	2026-02-03 15:22:02.17453
383	4	4	Master Dumbbells Plus	master-dumbbells-plus-383	419.00	419.00	29	High-quality dumbbells with excellent features and performance	🏊	t	f	3.90	115	2026-02-03 15:22:02.175039
384	4	11	Modern Resistance Bands Series	modern-resistance-bands-series-384	769.00	904.71	88	High-quality resistance bands with excellent features and performance	🎾	f	t	3.50	399	2026-02-03 15:22:02.175493
385	4	11	Modern Jump Rope Edition	modern-jump-rope-edition-385	735.00	735.00	33	High-quality jump rope with excellent features and performance	🧘	f	f	4.90	364	2026-02-03 15:22:02.175968
386	4	11	Master Gym Bag Edition	master-gym-bag-edition-386	464.00	515.56	87	High-quality gym bag with excellent features and performance	🎾	f	f	4.80	200	2026-02-03 15:22:02.176437
387	4	11	Premium Water Bottle Series	premium-water-bottle-series-387	629.00	698.89	79	High-quality water bottle with excellent features and performance	🏊	f	f	3.60	479	2026-02-03 15:22:02.176838
388	4	4	Master Protein Shaker Edition	master-protein-shaker-edition-388	771.00	771.00	93	High-quality protein shaker with excellent features and performance	⚽	f	f	3.90	261	2026-02-03 15:22:02.177516
389	4	4	Ultra Fitness Tracker Version	ultra-fitness-tracker-version-389	407.00	407.00	79	High-quality fitness tracker with excellent features and performance	⚽	f	f	4.00	457	2026-02-03 15:22:02.177928
390	4	4	Master Bicycle Collection	master-bicycle-collection-390	548.00	644.71	21	High-quality bicycle with excellent features and performance	🏋️	t	f	3.90	94	2026-02-03 15:22:02.178243
391	4	11	Professional Helmet Version	professional-helmet-version-391	871.00	1161.33	83	High-quality helmet with excellent features and performance	🧘	f	f	4.60	262	2026-02-03 15:22:02.178525
392	4	4	Modern Tennis Racket Version	modern-tennis-racket-version-392	650.00	866.67	41	High-quality tennis racket with excellent features and performance	🚴	f	f	4.70	125	2026-02-03 15:22:02.178777
393	4	4	Elite Basketball Model	elite-basketball-model-393	505.00	505.00	36	High-quality basketball with excellent features and performance	⛳	f	f	5.00	274	2026-02-03 15:22:02.17905
394	4	11	Classic Football Version	classic-football-version-394	857.00	857.00	75	High-quality football with excellent features and performance	⚽	f	t	4.80	362	2026-02-03 15:22:02.179293
395	4	4	Professional Soccer Ball Series	professional-soccer-ball-series-395	351.00	351.00	75	High-quality soccer ball with excellent features and performance	🏋️	t	f	3.80	146	2026-02-03 15:22:02.179641
396	4	4	Expert Baseball Bat Edition	expert-baseball-bat-edition-396	837.00	881.05	46	High-quality baseball bat with excellent features and performance	⛳	t	f	3.70	110	2026-02-03 15:22:02.180124
397	4	4	Classic Golf Clubs Model	classic-golf-clubs-model-397	496.94	496.94	50	High-quality golf clubs with excellent features and performance	🏋️	f	f	4.80	484	2026-02-03 15:22:02.180535
398	4	4	Deluxe Swimming Goggles Version	deluxe-swimming-goggles-version-398	836.00	1045.00	25	High-quality swimming goggles with excellent features and performance	⚽	f	t	4.80	226	2026-02-03 15:22:02.180856
399	4	4	Luxury Yoga Blocks Grade	luxury-yoga-blocks-grade-399	381.00	381.00	37	High-quality yoga blocks with excellent features and performance	🏋️	f	f	3.70	142	2026-02-03 15:22:02.181251
400	4	4	Premium Foam Roller Model	premium-foam-roller-model-400	784.00	784.00	85	High-quality foam roller with excellent features and performance	⚽	f	f	4.40	298	2026-02-03 15:22:02.181611
401	5	5	Professional Fiction Novel Max	professional-fiction-novel-max-401	960.00	1280.00	62	High-quality fiction novel with excellent features and performance	📖	t	f	3.50	442	2026-02-03 15:22:02.181902
402	5	12	Advanced Mystery Thriller Plus	advanced-mystery-thriller-plus-402	559.00	657.65	40	High-quality mystery thriller with excellent features and performance	📒	f	f	5.00	271	2026-02-03 15:22:02.18233
403	5	5	Ultra Romance Book Quality	ultra-romance-book-quality-403	358.00	511.43	30	High-quality romance book with excellent features and performance	📔	f	t	5.00	191	2026-02-03 15:22:02.182695
404	5	5	Expert Science Fiction Edition	expert-science-fiction-edition-404	481.00	565.88	36	High-quality science fiction with excellent features and performance	📚	f	f	4.80	151	2026-02-03 15:22:02.182996
405	5	12	Luxury Fantasy Epic Performance	luxury-fantasy-epic-performance-405	586.00	732.50	65	High-quality fantasy epic with excellent features and performance	📕	f	f	3.70	141	2026-02-03 15:22:02.183266
406	5	12	Advanced Biography Plus	advanced-biography-plus-406	303.00	432.86	24	High-quality biography with excellent features and performance	📰	f	f	4.20	66	2026-02-03 15:22:02.183762
407	5	12	Supreme Self-Help Guide Collection	supreme-self-help-guide-collection-407	974.00	974.00	50	High-quality self-help guide with excellent features and performance	📒	f	f	4.70	92	2026-02-03 15:22:02.184325
408	5	12	Modern Business Book Model	modern-business-book-model-408	414.00	517.50	11	High-quality business book with excellent features and performance	📔	f	f	4.80	417	2026-02-03 15:22:02.184791
409	5	5	Supreme Cookbook Quality	supreme-cookbook-quality-409	657.00	657.00	18	High-quality cookbook with excellent features and performance	📖	f	t	4.40	145	2026-02-03 15:22:02.185447
410	5	12	Expert Travel Guide Model	expert-travel-guide-model-410	861.00	956.67	31	High-quality travel guide with excellent features and performance	📓	f	f	4.80	374	2026-02-03 15:22:02.185773
411	5	5	Classic History Book Quality	classic-history-book-quality-411	319.00	425.33	19	High-quality history book with excellent features and performance	📚	f	f	4.00	170	2026-02-03 15:22:02.18614
412	5	5	Deluxe Poetry Collection Version	deluxe-poetry-collection-version-412	583.00	583.00	11	High-quality poetry collection with excellent features and performance	📙	f	f	4.60	443	2026-02-03 15:22:02.18649
413	5	12	Perfect Art Book Max	perfect-art-book-max-413	726.00	726.00	63	High-quality art book with excellent features and performance	📚	t	f	3.60	161	2026-02-03 15:22:02.186784
414	5	12	Expert Photography Book Performance	expert-photography-book-performance-414	788.00	875.56	71	High-quality photography book with excellent features and performance	📓	f	f	4.90	182	2026-02-03 15:22:02.187172
415	5	12	Professional Programming Guide Version	professional-programming-guide-version-415	703.00	703.00	82	High-quality programming guide with excellent features and performance	📖	f	t	3.60	90	2026-02-03 15:22:02.187571
416	5	5	Classic Marketing Book Version	classic-marketing-book-version-416	402.00	472.94	6	High-quality marketing book with excellent features and performance	📖	f	t	4.40	164	2026-02-03 15:22:02.187866
417	5	5	Luxury Psychology Book Performance	luxury-psychology-book-performance-417	513.00	684.00	28	High-quality psychology book with excellent features and performance	📗	f	f	4.80	454	2026-02-03 15:22:02.188108
418	5	5	Professional Philosophy Book Model	professional-philosophy-book-model-418	819.00	963.53	5	High-quality philosophy book with excellent features and performance	📘	f	f	3.50	302	2026-02-03 15:22:02.188425
419	5	12	Professional Children's Book Plus	professional-children's-book-plus-419	738.00	922.50	47	High-quality children's book with excellent features and performance	📙	f	f	3.80	369	2026-02-03 15:22:02.18876
420	5	5	Pro Comic Book Series	pro-comic-book-series-420	696.00	732.63	14	High-quality comic book with excellent features and performance	📔	f	f	4.00	324	2026-02-03 15:22:02.189028
421	5	5	Modern Fiction Novel Series	modern-fiction-novel-series-421	952.00	952.00	66	High-quality fiction novel with excellent features and performance	📘	f	f	3.70	198	2026-02-03 15:22:02.189255
422	5	12	Luxury Mystery Thriller Series	luxury-mystery-thriller-series-422	857.00	857.00	43	High-quality mystery thriller with excellent features and performance	📗	f	f	4.20	486	2026-02-03 15:22:02.18948
423	5	5	Expert Romance Book Quality	expert-romance-book-quality-423	869.00	1022.35	100	High-quality romance book with excellent features and performance	📓	f	f	4.20	478	2026-02-03 15:22:02.189873
424	5	5	Pro Science Fiction Max	pro-science-fiction-max-424	324.00	381.18	58	High-quality science fiction with excellent features and performance	📗	f	f	3.80	249	2026-02-03 15:22:02.190224
425	5	5	Master Fantasy Epic Plus	master-fantasy-epic-plus-425	505.00	721.43	43	High-quality fantasy epic with excellent features and performance	📔	f	f	3.90	416	2026-02-03 15:22:02.19057
426	5	5	Ultra Biography Quality	ultra-biography-quality-426	779.00	916.47	10	High-quality biography with excellent features and performance	📕	f	t	3.80	417	2026-02-03 15:22:02.190954
427	5	5	Essential Self-Help Guide Max	essential-self-help-guide-max-427	869.00	1022.35	8	High-quality self-help guide with excellent features and performance	📗	f	f	4.10	172	2026-02-03 15:22:02.19147
428	5	12	Ultra Business Book Series	ultra-business-book-series-428	572.00	762.67	41	High-quality business book with excellent features and performance	📔	t	t	3.80	225	2026-02-03 15:22:02.191895
429	5	5	Professional Cookbook Quality	professional-cookbook-quality-429	689.00	689.00	38	High-quality cookbook with excellent features and performance	📰	f	f	4.50	375	2026-02-03 15:22:02.192272
430	5	12	Professional Travel Guide Quality	professional-travel-guide-quality-430	454.00	454.00	80	High-quality travel guide with excellent features and performance	📰	f	f	4.30	126	2026-02-03 15:22:02.192806
431	5	5	Classic History Book Series	classic-history-book-series-431	648.00	762.35	39	High-quality history book with excellent features and performance	📗	f	f	4.20	425	2026-02-03 15:22:02.1932
432	5	5	Classic Poetry Collection Edition	classic-poetry-collection-edition-432	546.00	574.74	57	High-quality poetry collection with excellent features and performance	📙	f	t	4.30	402	2026-02-03 15:22:02.19355
433	5	12	Professional Art Book Version	professional-art-book-version-433	731.00	974.67	99	High-quality art book with excellent features and performance	📓	f	t	4.80	239	2026-02-03 15:22:02.19398
434	5	12	Advanced Photography Book Version	advanced-photography-book-version-434	591.00	622.11	44	High-quality photography book with excellent features and performance	📚	f	t	4.10	442	2026-02-03 15:22:02.194532
435	5	5	Essential Programming Guide Edition	essential-programming-guide-edition-435	669.00	955.71	2	High-quality programming guide with excellent features and performance	📒	f	t	4.10	67	2026-02-03 15:22:02.194951
436	5	12	Luxury Marketing Book Quality	luxury-marketing-book-quality-436	451.00	451.00	40	High-quality marketing book with excellent features and performance	📒	f	t	3.80	362	2026-02-03 15:22:02.195544
437	5	12	Perfect Psychology Book Version	perfect-psychology-book-version-437	388.00	554.29	25	High-quality psychology book with excellent features and performance	📙	f	t	4.80	255	2026-02-03 15:22:02.195957
438	5	5	Elite Philosophy Book Series	elite-philosophy-book-series-438	467.00	622.67	20	High-quality philosophy book with excellent features and performance	📒	f	f	3.50	302	2026-02-03 15:22:02.196321
439	5	5	Premium Children's Book Max	premium-children's-book-max-439	734.00	734.00	37	High-quality children's book with excellent features and performance	📔	f	f	4.50	278	2026-02-03 15:22:02.196661
440	5	12	Master Comic Book Model	master-comic-book-model-440	445.00	556.25	56	High-quality comic book with excellent features and performance	📘	f	t	4.70	164	2026-02-03 15:22:02.196965
441	5	12	Essential Fiction Novel Series	essential-fiction-novel-series-441	883.00	1038.82	58	High-quality fiction novel with excellent features and performance	📕	f	f	4.00	13	2026-02-03 15:22:02.197252
442	5	12	Elite Mystery Thriller Grade	elite-mystery-thriller-grade-442	875.00	875.00	53	High-quality mystery thriller with excellent features and performance	📒	f	f	3.70	372	2026-02-03 15:22:02.197708
443	5	12	Modern Romance Book Max	modern-romance-book-max-443	749.00	788.42	34	High-quality romance book with excellent features and performance	📚	f	f	4.70	394	2026-02-03 15:22:02.198139
444	5	12	Elite Science Fiction Max	elite-science-fiction-max-444	884.00	884.00	89	High-quality science fiction with excellent features and performance	📔	f	t	4.00	219	2026-02-03 15:22:02.198596
445	5	5	Pro Fantasy Epic Quality	pro-fantasy-epic-quality-445	594.00	698.82	12	High-quality fantasy epic with excellent features and performance	📗	f	f	5.00	395	2026-02-03 15:22:02.198971
446	5	12	Deluxe Biography Performance	deluxe-biography-performance-446	814.00	904.44	40	High-quality biography with excellent features and performance	📘	f	f	4.90	82	2026-02-03 15:22:02.199426
447	5	5	Professional Self-Help Guide Version	professional-self-help-guide-version-447	339.00	423.75	30	High-quality self-help guide with excellent features and performance	📰	f	f	5.00	358	2026-02-03 15:22:02.199861
448	5	12	Perfect Business Book Edition	perfect-business-book-edition-448	650.00	650.00	4	High-quality business book with excellent features and performance	📒	f	f	3.90	103	2026-02-03 15:22:02.200232
449	5	5	Elite Cookbook Model	elite-cookbook-model-449	480.00	480.00	57	High-quality cookbook with excellent features and performance	📓	f	f	4.00	220	2026-02-03 15:22:02.200586
450	5	5	Deluxe Travel Guide Max	deluxe-travel-guide-max-450	915.00	1220.00	67	High-quality travel guide with excellent features and performance	📚	f	f	4.00	275	2026-02-03 15:22:02.200955
451	5	5	Advanced History Book Collection	advanced-history-book-collection-451	563.00	563.00	80	High-quality history book with excellent features and performance	📰	f	f	3.90	381	2026-02-03 15:22:02.201316
452	5	5	Perfect Poetry Collection Version	perfect-poetry-collection-version-452	519.00	519.00	84	High-quality poetry collection with excellent features and performance	📖	f	f	4.50	186	2026-02-03 15:22:02.201607
453	5	5	Elite Art Book Model	elite-art-book-model-453	311.00	345.56	77	High-quality art book with excellent features and performance	📔	t	f	4.80	110	2026-02-03 15:22:02.201915
454	5	12	Master Photography Book Grade	master-photography-book-grade-454	876.00	876.00	84	High-quality photography book with excellent features and performance	📚	f	f	4.80	423	2026-02-03 15:22:02.202261
455	5	12	Premium Programming Guide Max	premium-programming-guide-max-455	731.00	860.00	4	High-quality programming guide with excellent features and performance	📚	f	f	3.80	45	2026-02-03 15:22:02.202627
456	5	5	Modern Marketing Book Plus	modern-marketing-book-plus-456	521.00	521.00	88	High-quality marketing book with excellent features and performance	📓	f	f	5.00	105	2026-02-03 15:22:02.203048
457	5	12	Professional Psychology Book Collection	professional-psychology-book-collection-457	582.00	646.67	59	High-quality psychology book with excellent features and performance	📖	f	t	4.10	209	2026-02-03 15:22:02.203394
458	5	5	Expert Philosophy Book Series	expert-philosophy-book-series-458	583.00	832.86	77	High-quality philosophy book with excellent features and performance	📗	t	t	4.50	308	2026-02-03 15:22:02.203721
459	5	5	Modern Children's Book Max	modern-children's-book-max-459	628.00	628.00	20	High-quality children's book with excellent features and performance	📓	f	f	3.80	179	2026-02-03 15:22:02.203992
460	5	12	Elite Comic Book Version	elite-comic-book-version-460	406.00	406.00	92	High-quality comic book with excellent features and performance	📒	f	f	3.60	51	2026-02-03 15:22:02.204302
461	5	12	Advanced Fiction Novel Edition	advanced-fiction-novel-edition-461	649.00	721.11	81	High-quality fiction novel with excellent features and performance	📚	f	t	3.80	134	2026-02-03 15:22:02.204594
462	5	5	Supreme Mystery Thriller Series	supreme-mystery-thriller-series-462	865.00	910.53	38	High-quality mystery thriller with excellent features and performance	📕	f	f	4.10	324	2026-02-03 15:22:02.20486
463	5	5	Modern Romance Book Max	modern-romance-book-max-463	964.00	1014.74	31	High-quality romance book with excellent features and performance	📓	f	f	4.70	484	2026-02-03 15:22:02.205211
464	5	5	Luxury Science Fiction Performance	luxury-science-fiction-performance-464	923.00	1153.75	86	High-quality science fiction with excellent features and performance	📰	t	t	3.60	320	2026-02-03 15:22:02.205603
465	5	12	Pro Fantasy Epic Model	pro-fantasy-epic-model-465	435.00	457.89	83	High-quality fantasy epic with excellent features and performance	📓	f	f	3.80	38	2026-02-03 15:22:02.205936
466	5	12	Advanced Biography Version	advanced-biography-version-466	833.00	1110.67	2	High-quality biography with excellent features and performance	📖	t	t	4.10	198	2026-02-03 15:22:02.206192
467	5	5	Pro Self-Help Guide Max	pro-self-help-guide-max-467	919.00	919.00	10	High-quality self-help guide with excellent features and performance	📓	t	f	4.80	320	2026-02-03 15:22:02.206463
468	5	5	Premium Business Book Plus	premium-business-book-plus-468	586.00	837.14	95	High-quality business book with excellent features and performance	📚	f	f	3.70	182	2026-02-03 15:22:02.206763
469	5	12	Supreme Cookbook Plus	supreme-cookbook-plus-469	514.00	514.00	85	High-quality cookbook with excellent features and performance	📒	f	f	3.60	421	2026-02-03 15:22:02.207036
470	5	12	Essential Travel Guide Quality	essential-travel-guide-quality-470	565.00	753.33	25	High-quality travel guide with excellent features and performance	📔	f	f	4.70	207	2026-02-03 15:22:02.207303
471	5	12	Professional History Book Series	professional-history-book-series-471	823.00	968.24	93	High-quality history book with excellent features and performance	📘	t	f	3.80	204	2026-02-03 15:22:02.207739
472	5	5	Essential Poetry Collection Performance	essential-poetry-collection-performance-472	845.00	1207.14	97	High-quality poetry collection with excellent features and performance	📓	t	f	4.30	331	2026-02-03 15:22:02.208177
474	5	12	Supreme Photography Book Plus	supreme-photography-book-plus-474	980.00	980.00	92	High-quality photography book with excellent features and performance	📚	t	f	3.60	395	2026-02-03 15:22:02.20909
475	5	12	Essential Programming Guide Collection	essential-programming-guide-collection-475	713.00	713.00	71	High-quality programming guide with excellent features and performance	📰	f	f	4.70	406	2026-02-03 15:22:02.209829
476	5	12	Ultra Marketing Book Max	ultra-marketing-book-max-476	674.00	898.67	63	High-quality marketing book with excellent features and performance	📔	f	t	4.10	315	2026-02-03 15:22:02.210338
477	5	5	Luxury Psychology Book Model	luxury-psychology-book-model-477	531.00	708.00	100	High-quality psychology book with excellent features and performance	📓	f	f	4.10	10	2026-02-03 15:22:02.210912
478	5	12	Expert Philosophy Book Series	expert-philosophy-book-series-478	329.00	329.00	48	High-quality philosophy book with excellent features and performance	📚	f	f	4.40	441	2026-02-03 15:22:02.211407
479	5	12	Pro Children's Book Collection	pro-children's-book-collection-479	859.00	1227.14	92	High-quality children's book with excellent features and performance	📕	f	f	4.90	290	2026-02-03 15:22:02.211847
480	5	12	Premium Comic Book Version	premium-comic-book-version-480	326.00	465.71	4	High-quality comic book with excellent features and performance	📔	f	f	4.50	382	2026-02-03 15:22:02.212207
481	5	5	Professional Fiction Novel Quality	professional-fiction-novel-quality-481	765.00	956.25	52	High-quality fiction novel with excellent features and performance	📚	f	f	4.50	120	2026-02-03 15:22:02.212664
482	5	5	Premium Mystery Thriller Series	premium-mystery-thriller-series-482	886.00	1107.50	4	High-quality mystery thriller with excellent features and performance	📔	f	f	3.70	432	2026-02-03 15:22:02.213061
483	5	12	Advanced Romance Book Model	advanced-romance-book-model-483	706.00	706.00	22	High-quality romance book with excellent features and performance	📓	f	f	3.90	329	2026-02-03 15:22:02.213565
484	5	12	Advanced Science Fiction Plus	advanced-science-fiction-plus-484	988.00	988.00	26	High-quality science fiction with excellent features and performance	📙	f	f	3.80	82	2026-02-03 15:22:02.213954
485	5	12	Supreme Fantasy Epic Version	supreme-fantasy-epic-version-485	363.00	427.06	88	High-quality fantasy epic with excellent features and performance	📖	f	f	4.60	407	2026-02-03 15:22:02.214294
486	5	12	Luxury Biography Quality	luxury-biography-quality-486	508.00	508.00	40	High-quality biography with excellent features and performance	📰	f	f	4.90	213	2026-02-03 15:22:02.214826
487	5	5	Ultra Self-Help Guide Max	ultra-self-help-guide-max-487	542.00	542.00	38	High-quality self-help guide with excellent features and performance	📔	t	f	5.00	374	2026-02-03 15:22:02.215254
488	5	5	Perfect Business Book Quality	perfect-business-book-quality-488	546.00	682.50	67	High-quality business book with excellent features and performance	📗	f	f	4.80	325	2026-02-03 15:22:02.215593
489	5	5	Pro Cookbook Series	pro-cookbook-series-489	337.00	481.43	32	High-quality cookbook with excellent features and performance	📗	t	f	4.50	499	2026-02-03 15:22:02.21592
490	5	12	Expert Travel Guide Edition	expert-travel-guide-edition-490	577.00	769.33	30	High-quality travel guide with excellent features and performance	📓	f	t	3.80	405	2026-02-03 15:22:02.216332
491	5	12	Pro History Book Plus	pro-history-book-plus-491	314.00	448.57	90	High-quality history book with excellent features and performance	📒	f	f	4.00	348	2026-02-03 15:22:02.216742
492	5	12	Pro Poetry Collection Max	pro-poetry-collection-max-492	906.00	906.00	90	High-quality poetry collection with excellent features and performance	📰	f	f	3.50	53	2026-02-03 15:22:02.217135
493	5	5	Advanced Art Book Grade	advanced-art-book-grade-493	680.00	800.00	60	High-quality art book with excellent features and performance	📓	t	f	4.30	409	2026-02-03 15:22:02.217586
494	5	12	Supreme Photography Book Version	supreme-photography-book-version-494	472.00	496.84	33	High-quality photography book with excellent features and performance	📗	f	f	3.90	148	2026-02-03 15:22:02.218278
495	5	12	Master Programming Guide Version	master-programming-guide-version-495	807.00	807.00	18	High-quality programming guide with excellent features and performance	📘	f	t	5.00	282	2026-02-03 15:22:02.218852
496	5	12	Advanced Marketing Book Performance	advanced-marketing-book-performance-496	440.00	440.00	22	High-quality marketing book with excellent features and performance	📘	f	f	4.80	434	2026-02-03 15:22:02.219419
497	5	12	Perfect Psychology Book Series	perfect-psychology-book-series-497	392.00	392.00	39	High-quality psychology book with excellent features and performance	📔	f	f	4.90	470	2026-02-03 15:22:02.219819
498	5	5	Supreme Philosophy Book Series	supreme-philosophy-book-series-498	958.00	1197.50	97	High-quality philosophy book with excellent features and performance	📒	f	f	4.60	252	2026-02-03 15:22:02.220256
499	5	5	Luxury Children's Book Version	luxury-children's-book-version-499	398.00	398.00	89	High-quality children's book with excellent features and performance	📖	t	t	4.10	374	2026-02-03 15:22:02.220756
500	5	12	Ultra Comic Book Series	ultra-comic-book-series-500	776.00	776.00	51	High-quality comic book with excellent features and performance	📰	f	f	5.00	46	2026-02-03 15:22:02.221257
\.


--
-- TOC entry 5490 (class 0 OID 25403)
-- Dependencies: 245
-- Data for Name: sales_cart; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.sales_cart (cart_id, user_id, session_id, is_active, created_at, updated_at) FROM stdin;
1	\N	DMWNBogbGAG8n4SJvHnKUcOFKl1fObaXXQyYWU66	t	2026-02-03 09:08:17	2026-02-03 09:08:17
2	\N	J4D5X8ySk4v7x3ffCLlyhWLTeJIysu44XAjSencL	t	2026-02-03 09:08:18	2026-02-03 09:08:18
3	\N	SAz5W4IbdtaWr5opZ3BGd3Hthl6O3vAWphzFtX5A	t	2026-02-03 09:08:19	2026-02-03 09:08:19
4	\N	0eth79YquWQsJ7RYUiz4dvf40qGOAxIAqPi56Jy5	t	2026-02-03 09:08:21	2026-02-03 09:08:21
5	\N	SusCr2OiMHFxlTk3I5sCP2tw9zMXGAtpJKVab4xJ	t	2026-02-03 09:08:22	2026-02-03 09:08:22
6	\N	oZ0oKKWC5jO5bT0NNOCs15a3A3Nib42BidTDfvHb	t	2026-02-03 09:09:01	2026-02-03 09:09:01
7	\N	cJZHBkIQOObreItYmLYuPImccP12RCVpSnpUrTWA	t	2026-02-03 09:09:02	2026-02-03 09:09:02
8	\N	o6W8U7Q5f9Elwp02cdX9QlY3WJuO9fFW8DOh2V5y	t	2026-02-03 09:09:05	2026-02-03 09:09:05
9	\N	zIRuJ0uvBxnOOt6p5PpuTUF9GWz0wpOYIt5lMzNm	t	2026-02-03 09:09:05	2026-02-03 09:09:05
10	\N	jAvWtvsqSRdgE59baHLuPEuXii7Lg9AoBpqAQQas	t	2026-02-03 09:09:10	2026-02-03 09:09:10
11	\N	RsbCZm55aXMpDCTtGg04IpHJjd9h5z0VydyuuU9R	t	2026-02-03 09:09:10	2026-02-03 09:09:10
12	\N	WaULsWv7VUv7pjYQo4oBqyD7UwHZR9BC5MSyQh0B	t	2026-02-03 09:09:12	2026-02-03 09:09:12
13	\N	tQapzNFHmzamNpueEpWqdcyNKb2iKNYAaVeNJBfo	t	2026-02-03 09:09:12	2026-02-03 09:09:12
14	\N	Ga50VSc3lWfPsdysz1IltBkFiRYyqBqZ0xBNY6ki	t	2026-02-03 09:09:16	2026-02-03 09:09:16
15	\N	sKbcAr2CFjjLhI9BhJAqvagzGpoTgrd1LBgJso8V	t	2026-02-03 09:09:16	2026-02-03 09:09:16
17	\N	HKHkitKj0VxhuNUTZXYwrfLF3kgpONBkZUTPv7sK	t	2026-02-03 09:09:38	2026-02-03 09:09:38
18	\N	0mfaKfkFhYC3e3PpbeZrZ6kbP2rbPOEn2Bg5mhAh	t	2026-02-03 09:09:39	2026-02-03 09:09:39
19	\N	RmvoFc2pV9Z2zpCFBvqgY79siwHNcCsSUY0l4H3Z	t	2026-02-03 09:09:41	2026-02-03 09:09:41
20	\N	0SIj1WK5TihsRkj0K6Hsj7dVFj1mk1zUKSGadpOj	t	2026-02-03 09:09:41	2026-02-03 09:09:41
21	\N	T94rM1gaXdSs1FvcV2JZnJjQKu7ozWkbUKCobV8m	t	2026-02-03 09:24:09	2026-02-03 09:24:09
22	\N	t6AGPhhySXHEnw9uOiIWrTtOvuHMNuroF8TK9635	t	2026-02-03 09:24:17	2026-02-03 09:24:17
23	\N	XkEbH4K0BzNacb45D8uVJKdOyCzocILrEsVs4z72	t	2026-02-03 09:24:18	2026-02-03 09:24:18
24	\N	it1btW4eVmt1gwL0j6yeBTwepjOES5XearG8HzQ6	t	2026-02-03 09:24:21	2026-02-03 09:24:21
25	\N	WEF5Wt911Cvo6lROR9NVZXuBPVviuRYxmr7KChGk	t	2026-02-03 09:24:21	2026-02-03 09:24:21
26	\N	TyNsVdmyxp7sbLzsKcmbWhnKfG9MvQ71ylldRadv	t	2026-02-03 09:24:25	2026-02-03 09:24:25
27	\N	cvhBoNUnVGpATDrHs4rIeNdpFw2LhsOauAOEXfgy	t	2026-02-03 09:24:26	2026-02-03 09:24:26
28	\N	lnZJQAfklGbxqxnCeBU6hxFF5idR3Ca69ZAZH4xj	t	2026-02-03 09:24:29	2026-02-03 09:24:29
29	\N	92ZYY3ZRfVjEY5Hjb4jkSV3aKOf1KZoVrICVPprF	t	2026-02-03 09:24:30	2026-02-03 09:24:30
30	\N	iRLDp4e0EyflJQkOZDDzq9PEr7x8c0xt8LZip6WN	t	2026-02-03 09:24:30	2026-02-03 09:24:30
31	\N	J2Wbo1qRZA5ZGz26DNUMCEn4KBUsE72BrxVt4JT8	t	2026-02-03 09:32:20	2026-02-03 09:32:20
32	\N	kESxw4wG8Af7Htx1p5PxKVHisGtauoKgujyNuT4D	t	2026-02-03 09:32:20	2026-02-03 09:32:20
33	\N	0YgVSdHLNvSpJ54eb1Xah642v0smOA2dIbgBvEIR	t	2026-02-03 09:32:23	2026-02-03 09:32:23
34	\N	at8XHiKHzhHqsgZVeiIebqsEIzNiO7h0aIQQHsK4	t	2026-02-03 09:32:24	2026-02-03 09:32:24
35	\N	sbxfp04ymDqfxdHCiT0gc0sq9PcdlA7eJNthL6HB	t	2026-02-03 09:32:26	2026-02-03 09:32:26
36	\N	R9s59ise6hVGBFQDp38HNtXYZxXgrnCwEPFj9DEA	t	2026-02-03 09:32:27	2026-02-03 09:32:27
37	\N	h0EnuJe7ajGHHE7TEJmgyypDE8e66CZbTGIrkQJb	t	2026-02-03 09:32:31	2026-02-03 09:32:31
38	\N	9xOwLBD6n7HZHB4Q2TJHjNrPvv3MESHCzk4S3rkw	t	2026-02-03 09:32:33	2026-02-03 09:32:33
39	\N	96RnnljPz5nS13naovH4FNRngZuGpq1cYM10ZsPu	t	2026-02-03 09:32:33	2026-02-03 09:32:33
40	\N	8oNUffER2MKE0Pian6puW6aC3GASTnq9LJrSCl1p	t	2026-02-03 09:32:39	2026-02-03 09:32:39
41	\N	cGKiCsyxPreTl8DN9GZVs35mxDLTaVON9d2UNa2M	t	2026-02-03 09:32:43	2026-02-03 09:32:43
42	\N	Bdw67wu6CVlGPdfmn4aqsZKJkWDj26kdSpi5o3jV	t	2026-02-03 09:32:47	2026-02-03 09:32:47
43	\N	Spu8i7fW8sGxaRlgvZS2WrahpkQuQaEiuBfFQahW	t	2026-02-03 09:32:47	2026-02-03 09:32:47
44	\N	1Cb4GPBzcVqPpVJ2yvRRJst5u8SZGHrLaHafXDnN	t	2026-02-03 09:32:50	2026-02-03 09:32:50
45	\N	AGXzIf8bIwJHd0fDYrxlwxTCojpNk2lBPjTgbQ27	t	2026-02-03 09:32:50	2026-02-03 09:32:50
46	\N	GcVhzSlc0VrzBUA63QidsE9kxcWwgTjfDKqrZp7a	t	2026-02-03 09:32:55	2026-02-03 09:32:55
47	\N	1fSC3yiPgSHaRflpq0NBMxTyIjTcho7tf8pmOvcL	t	2026-02-03 09:32:55	2026-02-03 09:32:55
48	\N	o73rmm89krpi2achp64dq5d0c6	f	\N	2026-02-03 15:21:47
49	\N	t96d787a32137kn10ct948pfuj	t	\N	\N
57	\N	jmub9g8c6h1326jsssipuienpb	f	\N	2026-02-04 11:50:16
50	\N	ht6j11qfo2h9v4e9gskr6nnku5	t	\N	\N
51	\N	n6hlaehelmo3ulsst1lo0tbagc	t	\N	\N
52	\N	mnubfebfeeangt5r662tq0p7uc	t	\N	\N
53	\N	jmub9g8c6h1326jsssipuienpb	f	\N	2026-02-04 09:21:03
54	\N	spu734d4m7cso59b2c0mobiqqv	t	\N	\N
58	\N	jmub9g8c6h1326jsssipuienpb	f	\N	2026-02-04 13:30:37
59	\N	jmub9g8c6h1326jsssipuienpb	f	\N	2026-02-04 13:43:38
60	\N	jmub9g8c6h1326jsssipuienpb	f	\N	2026-02-04 15:10:44
55	\N	a0r0i4qts6ejumfet7jin2d2qs	t	\N	2026-02-04 09:25:05
56	\N	rqn38tsiarurjuunc9nk9rl1lv	t	\N	\N
16	1	TAZz3nPfmuGlahb9T0ODGPmidqb06Vq1XmLJtiw0	t	2026-02-03 09:09:37	2026-01-09 16:21:02
\.


--
-- TOC entry 5494 (class 0 OID 25444)
-- Dependencies: 249
-- Data for Name: sales_cart_address; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.sales_cart_address (address_id, cart_id, address_type, first_name, last_name, email, phone, address_line_one, address_line_two, city, state, postal_code, country, created_at, updated_at) FROM stdin;
\.


--
-- TOC entry 5496 (class 0 OID 25470)
-- Dependencies: 251
-- Data for Name: sales_cart_payment; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.sales_cart_payment (payment_id, cart_id, shipping_method, payment_method, created_at, updated_at) FROM stdin;
\.


--
-- TOC entry 5492 (class 0 OID 25420)
-- Dependencies: 247
-- Data for Name: sales_cart_product; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.sales_cart_product (item_id, cart_id, product_entity_id, quantity, created_at, updated_at) FROM stdin;
63	57	15	24	\N	\N
19	55	59	81	\N	\N
104	59	8	4	\N	\N
105	59	68	8	\N	\N
\.


--
-- TOC entry 5498 (class 0 OID 25486)
-- Dependencies: 253
-- Data for Name: sales_order; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.sales_order (order_id, order_number, user_id, original_cart_id, subtotal, shipping_cost, tax, discount, total, status, created_at, updated_at, is_archived) FROM stdin;
1	ORD-AFE6C656	1	\N	1881.00	200.00	374.58	0.00	2455.58	processing	2026-02-03 15:33:21	\N	f
3	ORD-91E3B411	1	\N	958.00	47.90	181.06	0.00	1139.06	processing	2026-02-04 13:49:54	\N	f
4	ORD-2195AE47	1	\N	1064.64	200.00	227.64	0.00	1492.28	processing	2026-02-04 13:56:33	\N	f
2	ORD-DD03380F	1	\N	5823.36	150.00	1075.20	0.00	7048.56	processing	2026-02-04 11:50:44	\N	f
5	ORD-0DAB8933	1	\N	2911.68	145.58	550.31	0.00	3461.99	processing	2026-02-04 15:12:21	\N	f
6	ORD-738FB28F	1	\N	11.71	1.17	2.32	0.00	15.20	processing	2026-02-04 16:11:54	\N	f
7	ORD-545E3B1F	1	\N	398.00	200.00	107.64	0.00	705.64	processing	2026-01-07 16:15:19	\N	f
8	ORD-8260A68B	1	\N	39576.00	1187.28	7337.39	0.00	48100.67	processing	2026-02-04 16:20:07	\N	f
9	ORD-733F23DF	1	\N	54102.00	1623.06	10030.51	0.00	65755.57	processing	2026-01-09 16:21:02	\N	f
\.


--
-- TOC entry 5502 (class 0 OID 25545)
-- Dependencies: 257
-- Data for Name: sales_order_address; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.sales_order_address (address_id, order_id, address_type, first_name, last_name, email, phone, address_line_one, address_line_two, city, state, postal_code, country, created_at) FROM stdin;
1	1	billing	Parv	Talati	talatip68@gmail.com	3221	24	\N	241		e23	India	2026-02-03 15:33:21
2	1	shipping	Parv	Talati	talatip68@gmail.com	3221	24	\N	241		e23	India	2026-02-03 15:33:21
3	2	billing	Parv	Talati	talatip68@gmail.com	9999999	DFGHJK	\N	HJ		BNM,	India	2026-02-04 11:50:44
4	2	shipping	Parv	Talati	talatip68@gmail.com	9999999	DFGHJK	\N	HJ		BNM,	India	2026-02-04 11:50:44
5	3	billing	Parv	Talati	talatip68@gmail.com	9999999	DFGHJK	\N	HJ		BNM,	India	2026-02-04 13:49:54
6	3	shipping	Parv	Talati	talatip68@gmail.com	9999999	DFGHJK	\N	HJ		BNM,	India	2026-02-04 13:49:54
7	4	billing	Parv	Talati	talatip68@gmail.com	9999999	DFGHJK	\N	HJ		BNM,	India	2026-02-04 13:56:33
8	4	shipping	Parv	Talati	talatip68@gmail.com	9999999	DFGHJK	\N	HJ		BNM,	India	2026-02-04 13:56:33
9	5	billing	Parv	Talati	talatip68@gmail.com	9999999	DFGHJK	\N	HJ		BNM,	India	2026-02-04 15:12:21
10	5	shipping	Parv	Talati	talatip68@gmail.com	9999999	DFGHJK	\N	HJ		BNM,	India	2026-02-04 15:12:21
11	6	billing	Parv	Talati	talatip68@gmail.com	9999999	DFGHJK	\N	HJ		BNM,	India	2026-02-04 16:11:54
12	6	shipping	Parv	Talati	talatip68@gmail.com	9999999	DFGHJK	\N	HJ		BNM,	India	2026-02-04 16:11:54
13	7	billing	Parv	Talati	talatip68@gmail.com	9999999	DFGHJK	\N	HJ		BNM,	India	2026-01-07 16:15:19
14	7	shipping	Parv	Talati	talatip68@gmail.com	9999999	DFGHJK	\N	HJ		BNM,	India	2026-01-07 16:15:19
15	8	billing	Parv	Talati	talatip68@gmail.com	9999999	DFGHJK	\N	HJ		BNM,	India	2026-02-04 16:20:07
16	8	shipping	Parv	Talati	talatip68@gmail.com	9999999	DFGHJK	\N	HJ		BNM,	India	2026-02-04 16:20:07
17	9	billing	Parv	Talati	talatip68@gmail.com	9999999	DFGHJK	\N	HJ		BNM,	India	2026-01-09 16:21:02
18	9	shipping	Parv	Talati	talatip68@gmail.com	9999999	DFGHJK	\N	HJ		BNM,	India	2026-01-09 16:21:02
\.


--
-- TOC entry 5504 (class 0 OID 25572)
-- Dependencies: 259
-- Data for Name: sales_order_payment; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.sales_order_payment (payment_id, order_id, shipping_method, payment_method, created_at) FROM stdin;
1	2	white_glove	wallet	2026-02-04 11:50:44
2	3	white_glove	wallet	2026-02-04 13:49:54
3	4	freight	wallet	2026-02-04 13:56:33
4	5	white_glove	cod	2026-02-04 15:12:21
5	6	express	cod	2026-02-04 16:11:54
6	7	freight	cod	2026-01-07 16:15:19
7	8	freight	cod	2026-02-04 16:20:07
8	9	freight	cod	2026-01-09 16:21:02
\.


--
-- TOC entry 5500 (class 0 OID 25519)
-- Dependencies: 255
-- Data for Name: sales_order_product; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.sales_order_product (item_id, order_id, product_entity_id, product_name, product_sku, product_price, quantity, row_total, created_at) FROM stdin;
4	4	8	Classic Headphones Collection	unknown	88.06	4	352.24	2026-02-04 13:56:33
2	2	15	Classic Microphone Series	unknown	121.32	48	5823.36	2026-02-04 11:50:44
5	4	68	Supreme Headphones Collection	unknown	89.05	8	712.40	2026-02-04 13:56:33
3	3	498	Supreme Philosophy Book Series	unknown	958.00	1	958.00	2026-02-04 13:49:54
6	5	15	Classic Microphone Series	classic-microphone-series-15	121.32	24	2911.68	2026-02-04 15:12:21
7	6	19	Deluxe USB Cable Model	deluxe-usb-cable-model-19	11.71	1	11.71	2026-02-04 16:11:54
8	7	499	Luxury Children's Book Version	luxury-children's-book-version-499	398.00	1	398.00	2026-01-07 16:15:19
9	8	500	Ultra Comic Book Series	ultra-comic-book-series-500	776.00	51	39576.00	2026-02-04 16:20:07
10	9	495	Master Programming Guide Version	master-programming-guide-version-495	807.00	18	14526.00	2026-01-09 16:21:02
11	9	500	Ultra Comic Book Series	ultra-comic-book-series-500	776.00	51	39576.00	2026-01-09 16:21:02
\.


--
-- TOC entry 5524 (class 0 OID 25772)
-- Dependencies: 279
-- Data for Name: saved_items; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.saved_items (id, user_id, product_id, created_at) FROM stdin;
\.


--
-- TOC entry 5469 (class 0 OID 25176)
-- Dependencies: 224
-- Data for Name: sessions; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.sessions (id, user_id, ip_address, user_agent, payload, last_activity) FROM stdin;
T94rM1gaXdSs1FvcV2JZnJjQKu7ozWkbUKCobV8m	\N	127.0.0.1	Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36 Edg/144.0.0.0	YTo0OntzOjY6Il90b2tlbiI7czo0MDoiTGViekd2S3BQYzRpRnI4V0JnR1FlNkZyMFJidlphMnY5ZjBlY0liaiI7czo3OiJjYXJ0X2lkIjtpOjIxO3M6OToiX3ByZXZpb3VzIjthOjI6e3M6MzoidXJsIjtzOjMxOiJodHRwOi8vbG9jYWxob3N0OjgwMDAvcHJvZHVjdC8zIjtzOjU6InJvdXRlIjtzOjEyOiJwcm9kdWN0LnNob3ciO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19	1770110668
TAZz3nPfmuGlahb9T0ODGPmidqb06Vq1XmLJtiw0	1	127.0.0.1	Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36	YTo1OntzOjY6Il90b2tlbiI7czo0MDoiTXFUMHVHdjF5QkJiU3dNYjVDSWxkNUZ1Slh6Y0lNVnlBNkpBZkU0cyI7czo3OiJjYXJ0X2lkIjtpOjE7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6Mjg6Imh0dHA6Ly9sb2NhbGhvc3Q6ODAwMC9vcmRlcnMiO3M6NToicm91dGUiO3M6Njoib3JkZXJzIjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319czo1MDoibG9naW5fd2ViXzU5YmEzNmFkZGMyYjJmOTQwMTU4MGYwMTRjN2Y1OGVhNGUzMDk4OWQiO2k6MTt9	1770111174
\.


--
-- TOC entry 5532 (class 0 OID 33002)
-- Dependencies: 287
-- Data for Name: user_addresses; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.user_addresses (id, user_id, first_name, last_name, phone, address_line_one, address_line_two, city, state, postal_code, country, is_default, created_at, updated_at) FROM stdin;
\.


--
-- TOC entry 5467 (class 0 OID 25153)
-- Dependencies: 222
-- Data for Name: users; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.users (id, name, email, email_verified_at, password, remember_token, created_at, updated_at, phone) FROM stdin;
1	Parv Talati	talatip68@gmail.com	\N	$2y$10$rPM.MN165eUGuEtAq.BrXeSYDHhQ3vEiRyJd2bh7aMB8uylYYhR5.	\N	2026-02-03 09:09:36	2026-02-03 09:09:36	8654132.02621+++
\.


--
-- TOC entry 5528 (class 0 OID 32947)
-- Dependencies: 283
-- Data for Name: wishlist; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.wishlist (wishlist_id, user_id, product_entity_id, created_at) FROM stdin;
\.


--
-- TOC entry 5522 (class 0 OID 25751)
-- Dependencies: 277
-- Data for Name: wishlists; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.wishlists (id, user_id, product_id, created_at) FROM stdin;
\.


--
-- TOC entry 5586 (class 0 OID 0)
-- Dependencies: 264
-- Name: brands_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.brands_id_seq', 1, false);


--
-- TOC entry 5587 (class 0 OID 0)
-- Dependencies: 274
-- Name: cart_items_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.cart_items_id_seq', 1, false);


--
-- TOC entry 5588 (class 0 OID 0)
-- Dependencies: 272
-- Name: carts_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.carts_id_seq', 1, false);


--
-- TOC entry 5589 (class 0 OID 0)
-- Dependencies: 240
-- Name: catalog_category_attribute_attribute_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.catalog_category_attribute_attribute_id_seq', 1, false);


--
-- TOC entry 5590 (class 0 OID 0)
-- Dependencies: 238
-- Name: catalog_category_entity_entity_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.catalog_category_entity_entity_id_seq', 5, true);


--
-- TOC entry 5591 (class 0 OID 0)
-- Dependencies: 242
-- Name: catalog_category_product_relation_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.catalog_category_product_relation_id_seq', 1004, true);


--
-- TOC entry 5592 (class 0 OID 0)
-- Dependencies: 234
-- Name: catalog_product_attribute_attribute_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.catalog_product_attribute_attribute_id_seq', 1004, true);


--
-- TOC entry 5593 (class 0 OID 0)
-- Dependencies: 232
-- Name: catalog_product_entity_entity_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.catalog_product_entity_entity_id_seq', 500, true);


--
-- TOC entry 5594 (class 0 OID 0)
-- Dependencies: 236
-- Name: catalog_product_image_image_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.catalog_product_image_image_id_seq', 1805, true);


--
-- TOC entry 5595 (class 0 OID 0)
-- Dependencies: 260
-- Name: catalog_wishlist_wishlist_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.catalog_wishlist_wishlist_id_seq', 4, true);


--
-- TOC entry 5596 (class 0 OID 0)
-- Dependencies: 262
-- Name: categories_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.categories_id_seq', 1, false);


--
-- TOC entry 5597 (class 0 OID 0)
-- Dependencies: 280
-- Name: coupons_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.coupons_id_seq', 4, true);


--
-- TOC entry 5598 (class 0 OID 0)
-- Dependencies: 230
-- Name: failed_jobs_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.failed_jobs_id_seq', 1, false);


--
-- TOC entry 5599 (class 0 OID 0)
-- Dependencies: 227
-- Name: jobs_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.jobs_id_seq', 1, false);


--
-- TOC entry 5600 (class 0 OID 0)
-- Dependencies: 219
-- Name: migrations_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.migrations_id_seq', 7, true);


--
-- TOC entry 5601 (class 0 OID 0)
-- Dependencies: 270
-- Name: order_items_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.order_items_id_seq', 1, false);


--
-- TOC entry 5602 (class 0 OID 0)
-- Dependencies: 268
-- Name: orders_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.orders_id_seq', 1, false);


--
-- TOC entry 5603 (class 0 OID 0)
-- Dependencies: 284
-- Name: product_reviews_review_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.product_reviews_review_id_seq', 1, false);


--
-- TOC entry 5604 (class 0 OID 0)
-- Dependencies: 266
-- Name: products_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.products_id_seq', 1, false);


--
-- TOC entry 5605 (class 0 OID 0)
-- Dependencies: 248
-- Name: sales_cart_address_address_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.sales_cart_address_address_id_seq', 1, false);


--
-- TOC entry 5606 (class 0 OID 0)
-- Dependencies: 244
-- Name: sales_cart_cart_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.sales_cart_cart_id_seq', 60, true);


--
-- TOC entry 5607 (class 0 OID 0)
-- Dependencies: 250
-- Name: sales_cart_payment_payment_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.sales_cart_payment_payment_id_seq', 1, false);


--
-- TOC entry 5608 (class 0 OID 0)
-- Dependencies: 246
-- Name: sales_cart_product_item_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.sales_cart_product_item_id_seq', 128, true);


--
-- TOC entry 5609 (class 0 OID 0)
-- Dependencies: 256
-- Name: sales_order_address_address_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.sales_order_address_address_id_seq', 18, true);


--
-- TOC entry 5610 (class 0 OID 0)
-- Dependencies: 252
-- Name: sales_order_order_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.sales_order_order_id_seq', 9, true);


--
-- TOC entry 5611 (class 0 OID 0)
-- Dependencies: 258
-- Name: sales_order_payment_payment_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.sales_order_payment_payment_id_seq', 8, true);


--
-- TOC entry 5612 (class 0 OID 0)
-- Dependencies: 254
-- Name: sales_order_product_item_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.sales_order_product_item_id_seq', 11, true);


--
-- TOC entry 5613 (class 0 OID 0)
-- Dependencies: 278
-- Name: saved_items_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.saved_items_id_seq', 5, true);


--
-- TOC entry 5614 (class 0 OID 0)
-- Dependencies: 286
-- Name: user_addresses_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.user_addresses_id_seq', 1, false);


--
-- TOC entry 5615 (class 0 OID 0)
-- Dependencies: 221
-- Name: users_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.users_id_seq', 1, true);


--
-- TOC entry 5616 (class 0 OID 0)
-- Dependencies: 282
-- Name: wishlist_wishlist_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.wishlist_wishlist_id_seq', 1, false);


--
-- TOC entry 5617 (class 0 OID 0)
-- Dependencies: 276
-- Name: wishlists_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.wishlists_id_seq', 1, false);


--
-- TOC entry 5239 (class 2606 OID 25639)
-- Name: brands brands_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.brands
    ADD CONSTRAINT brands_pkey PRIMARY KEY (id);


--
-- TOC entry 5131 (class 2606 OID 25208)
-- Name: cache_locks cache_locks_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.cache_locks
    ADD CONSTRAINT cache_locks_pkey PRIMARY KEY (key);


--
-- TOC entry 5128 (class 2606 OID 25197)
-- Name: cache cache_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.cache
    ADD CONSTRAINT cache_pkey PRIMARY KEY (key);


--
-- TOC entry 5253 (class 2606 OID 25739)
-- Name: cart_items cart_items_cart_id_product_id_key; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.cart_items
    ADD CONSTRAINT cart_items_cart_id_product_id_key UNIQUE (cart_id, product_id);


--
-- TOC entry 5255 (class 2606 OID 25737)
-- Name: cart_items cart_items_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.cart_items
    ADD CONSTRAINT cart_items_pkey PRIMARY KEY (id);


--
-- TOC entry 5251 (class 2606 OID 25722)
-- Name: carts carts_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.carts
    ADD CONSTRAINT carts_pkey PRIMARY KEY (id);


--
-- TOC entry 5175 (class 2606 OID 25369)
-- Name: catalog_category_attribute catalog_category_attribute_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.catalog_category_attribute
    ADD CONSTRAINT catalog_category_attribute_pkey PRIMARY KEY (attribute_id);


--
-- TOC entry 5167 (class 2606 OID 25345)
-- Name: catalog_category_entity catalog_category_entity_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.catalog_category_entity
    ADD CONSTRAINT catalog_category_entity_pkey PRIMARY KEY (entity_id);


--
-- TOC entry 5169 (class 2606 OID 25354)
-- Name: catalog_category_entity catalog_category_entity_slug_unique; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.catalog_category_entity
    ADD CONSTRAINT catalog_category_entity_slug_unique UNIQUE (slug);


--
-- TOC entry 5179 (class 2606 OID 25401)
-- Name: catalog_category_product catalog_category_product_category_entity_id_product_entity_id_u; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.catalog_category_product
    ADD CONSTRAINT catalog_category_product_category_entity_id_product_entity_id_u UNIQUE (category_entity_id, product_entity_id);


--
-- TOC entry 5181 (class 2606 OID 25389)
-- Name: catalog_category_product catalog_category_product_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.catalog_category_product
    ADD CONSTRAINT catalog_category_product_pkey PRIMARY KEY (relation_id);


--
-- TOC entry 5155 (class 2606 OID 25301)
-- Name: catalog_product_attribute catalog_product_attribute_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.catalog_product_attribute
    ADD CONSTRAINT catalog_product_attribute_pkey PRIMARY KEY (attribute_id);


--
-- TOC entry 5145 (class 2606 OID 25281)
-- Name: catalog_product_entity catalog_product_entity_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.catalog_product_entity
    ADD CONSTRAINT catalog_product_entity_pkey PRIMARY KEY (entity_id);


--
-- TOC entry 5148 (class 2606 OID 25286)
-- Name: catalog_product_entity catalog_product_entity_sku_unique; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.catalog_product_entity
    ADD CONSTRAINT catalog_product_entity_sku_unique UNIQUE (sku);


--
-- TOC entry 5161 (class 2606 OID 25323)
-- Name: catalog_product_image catalog_product_image_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.catalog_product_image
    ADD CONSTRAINT catalog_product_image_pkey PRIMARY KEY (image_id);


--
-- TOC entry 5231 (class 2606 OID 25600)
-- Name: catalog_wishlist catalog_wishlist_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.catalog_wishlist
    ADD CONSTRAINT catalog_wishlist_pkey PRIMARY KEY (wishlist_id);


--
-- TOC entry 5233 (class 2606 OID 25612)
-- Name: catalog_wishlist catalog_wishlist_user_id_product_entity_id_unique; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.catalog_wishlist
    ADD CONSTRAINT catalog_wishlist_user_id_product_entity_id_unique UNIQUE (user_id, product_entity_id);


--
-- TOC entry 5235 (class 2606 OID 25628)
-- Name: categories categories_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.categories
    ADD CONSTRAINT categories_pkey PRIMARY KEY (id);


--
-- TOC entry 5237 (class 2606 OID 25630)
-- Name: categories categories_slug_key; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.categories
    ADD CONSTRAINT categories_slug_key UNIQUE (slug);


--
-- TOC entry 5266 (class 2606 OID 25804)
-- Name: coupons coupons_code_key; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.coupons
    ADD CONSTRAINT coupons_code_key UNIQUE (code);


--
-- TOC entry 5268 (class 2606 OID 25802)
-- Name: coupons coupons_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.coupons
    ADD CONSTRAINT coupons_pkey PRIMARY KEY (id);


--
-- TOC entry 5138 (class 2606 OID 25256)
-- Name: failed_jobs failed_jobs_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.failed_jobs
    ADD CONSTRAINT failed_jobs_pkey PRIMARY KEY (id);


--
-- TOC entry 5140 (class 2606 OID 25258)
-- Name: failed_jobs failed_jobs_uuid_unique; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.failed_jobs
    ADD CONSTRAINT failed_jobs_uuid_unique UNIQUE (uuid);


--
-- TOC entry 5136 (class 2606 OID 25239)
-- Name: job_batches job_batches_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.job_batches
    ADD CONSTRAINT job_batches_pkey PRIMARY KEY (id);


--
-- TOC entry 5133 (class 2606 OID 25224)
-- Name: jobs jobs_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.jobs
    ADD CONSTRAINT jobs_pkey PRIMARY KEY (id);


--
-- TOC entry 5115 (class 2606 OID 25151)
-- Name: migrations migrations_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.migrations
    ADD CONSTRAINT migrations_pkey PRIMARY KEY (id);


--
-- TOC entry 5249 (class 2606 OID 25702)
-- Name: order_items order_items_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.order_items
    ADD CONSTRAINT order_items_pkey PRIMARY KEY (id);


--
-- TOC entry 5245 (class 2606 OID 25687)
-- Name: orders orders_order_number_key; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.orders
    ADD CONSTRAINT orders_order_number_key UNIQUE (order_number);


--
-- TOC entry 5247 (class 2606 OID 25685)
-- Name: orders orders_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.orders
    ADD CONSTRAINT orders_pkey PRIMARY KEY (id);


--
-- TOC entry 5121 (class 2606 OID 25175)
-- Name: password_reset_tokens password_reset_tokens_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.password_reset_tokens
    ADD CONSTRAINT password_reset_tokens_pkey PRIMARY KEY (email);


--
-- TOC entry 5276 (class 2606 OID 32986)
-- Name: product_reviews product_reviews_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.product_reviews
    ADD CONSTRAINT product_reviews_pkey PRIMARY KEY (review_id);


--
-- TOC entry 5278 (class 2606 OID 32988)
-- Name: product_reviews product_reviews_product_entity_id_user_id_key; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.product_reviews
    ADD CONSTRAINT product_reviews_product_entity_id_user_id_key UNIQUE (product_entity_id, user_id);


--
-- TOC entry 5241 (class 2606 OID 25658)
-- Name: products products_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.products
    ADD CONSTRAINT products_pkey PRIMARY KEY (id);


--
-- TOC entry 5243 (class 2606 OID 25660)
-- Name: products products_slug_key; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.products
    ADD CONSTRAINT products_slug_key UNIQUE (slug);


--
-- TOC entry 5201 (class 2606 OID 25462)
-- Name: sales_cart_address sales_cart_address_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.sales_cart_address
    ADD CONSTRAINT sales_cart_address_pkey PRIMARY KEY (address_id);


--
-- TOC entry 5204 (class 2606 OID 25484)
-- Name: sales_cart_payment sales_cart_payment_cart_id_unique; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.sales_cart_payment
    ADD CONSTRAINT sales_cart_payment_cart_id_unique UNIQUE (cart_id);


--
-- TOC entry 5206 (class 2606 OID 25477)
-- Name: sales_cart_payment sales_cart_payment_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.sales_cart_payment
    ADD CONSTRAINT sales_cart_payment_pkey PRIMARY KEY (payment_id);


--
-- TOC entry 5189 (class 2606 OID 25411)
-- Name: sales_cart sales_cart_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.sales_cart
    ADD CONSTRAINT sales_cart_pkey PRIMARY KEY (cart_id);


--
-- TOC entry 5194 (class 2606 OID 25442)
-- Name: sales_cart_product sales_cart_product_cart_id_product_entity_id_unique; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.sales_cart_product
    ADD CONSTRAINT sales_cart_product_cart_id_product_entity_id_unique UNIQUE (cart_id, product_entity_id);


--
-- TOC entry 5196 (class 2606 OID 25430)
-- Name: sales_cart_product sales_cart_product_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.sales_cart_product
    ADD CONSTRAINT sales_cart_product_pkey PRIMARY KEY (item_id);


--
-- TOC entry 5224 (class 2606 OID 25565)
-- Name: sales_order_address sales_order_address_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.sales_order_address
    ADD CONSTRAINT sales_order_address_pkey PRIMARY KEY (address_id);


--
-- TOC entry 5213 (class 2606 OID 25517)
-- Name: sales_order sales_order_order_number_unique; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.sales_order
    ADD CONSTRAINT sales_order_order_number_unique UNIQUE (order_number);


--
-- TOC entry 5227 (class 2606 OID 25590)
-- Name: sales_order_payment sales_order_payment_order_id_unique; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.sales_order_payment
    ADD CONSTRAINT sales_order_payment_order_id_unique UNIQUE (order_id);


--
-- TOC entry 5229 (class 2606 OID 25583)
-- Name: sales_order_payment sales_order_payment_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.sales_order_payment
    ADD CONSTRAINT sales_order_payment_pkey PRIMARY KEY (payment_id);


--
-- TOC entry 5215 (class 2606 OID 25503)
-- Name: sales_order sales_order_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.sales_order
    ADD CONSTRAINT sales_order_pkey PRIMARY KEY (order_id);


--
-- TOC entry 5220 (class 2606 OID 25533)
-- Name: sales_order_product sales_order_product_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.sales_order_product
    ADD CONSTRAINT sales_order_product_pkey PRIMARY KEY (item_id);


--
-- TOC entry 5262 (class 2606 OID 25779)
-- Name: saved_items saved_items_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.saved_items
    ADD CONSTRAINT saved_items_pkey PRIMARY KEY (id);


--
-- TOC entry 5264 (class 2606 OID 25781)
-- Name: saved_items saved_items_user_id_product_id_key; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.saved_items
    ADD CONSTRAINT saved_items_user_id_product_id_key UNIQUE (user_id, product_id);


--
-- TOC entry 5124 (class 2606 OID 25185)
-- Name: sessions sessions_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.sessions
    ADD CONSTRAINT sessions_pkey PRIMARY KEY (id);


--
-- TOC entry 5280 (class 2606 OID 33022)
-- Name: user_addresses user_addresses_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.user_addresses
    ADD CONSTRAINT user_addresses_pkey PRIMARY KEY (id);


--
-- TOC entry 5117 (class 2606 OID 25166)
-- Name: users users_email_unique; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.users
    ADD CONSTRAINT users_email_unique UNIQUE (email);


--
-- TOC entry 5119 (class 2606 OID 25164)
-- Name: users users_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.users
    ADD CONSTRAINT users_pkey PRIMARY KEY (id);


--
-- TOC entry 5271 (class 2606 OID 32956)
-- Name: wishlist wishlist_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.wishlist
    ADD CONSTRAINT wishlist_pkey PRIMARY KEY (wishlist_id);


--
-- TOC entry 5273 (class 2606 OID 32958)
-- Name: wishlist wishlist_user_id_product_entity_id_key; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.wishlist
    ADD CONSTRAINT wishlist_user_id_product_entity_id_key UNIQUE (user_id, product_entity_id);


--
-- TOC entry 5257 (class 2606 OID 25758)
-- Name: wishlists wishlists_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.wishlists
    ADD CONSTRAINT wishlists_pkey PRIMARY KEY (id);


--
-- TOC entry 5259 (class 2606 OID 25760)
-- Name: wishlists wishlists_user_id_product_id_key; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.wishlists
    ADD CONSTRAINT wishlists_user_id_product_id_key UNIQUE (user_id, product_id);


--
-- TOC entry 5126 (class 1259 OID 25198)
-- Name: cache_expiration_index; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX cache_expiration_index ON public.cache USING btree (expiration);


--
-- TOC entry 5129 (class 1259 OID 25209)
-- Name: cache_locks_expiration_index; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX cache_locks_expiration_index ON public.cache_locks USING btree (expiration);


--
-- TOC entry 5173 (class 1259 OID 25375)
-- Name: catalog_category_attribute_attribute_code_index; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX catalog_category_attribute_attribute_code_index ON public.catalog_category_attribute USING btree (attribute_code);


--
-- TOC entry 5164 (class 1259 OID 25352)
-- Name: catalog_category_entity_is_active_index; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX catalog_category_entity_is_active_index ON public.catalog_category_entity USING btree (is_active);


--
-- TOC entry 5165 (class 1259 OID 25351)
-- Name: catalog_category_entity_parent_id_index; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX catalog_category_entity_parent_id_index ON public.catalog_category_entity USING btree (parent_id);


--
-- TOC entry 5153 (class 1259 OID 25307)
-- Name: catalog_product_attribute_attribute_code_index; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX catalog_product_attribute_attribute_code_index ON public.catalog_product_attribute USING btree (attribute_code);


--
-- TOC entry 5141 (class 1259 OID 25616)
-- Name: catalog_product_entity_is_active_index; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX catalog_product_entity_is_active_index ON public.catalog_product_entity USING btree (is_active);


--
-- TOC entry 5142 (class 1259 OID 25282)
-- Name: catalog_product_entity_is_featured_index; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX catalog_product_entity_is_featured_index ON public.catalog_product_entity USING btree (is_featured);


--
-- TOC entry 5143 (class 1259 OID 25283)
-- Name: catalog_product_entity_is_new_index; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX catalog_product_entity_is_new_index ON public.catalog_product_entity USING btree (is_new);


--
-- TOC entry 5146 (class 1259 OID 25284)
-- Name: catalog_product_entity_price_index; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX catalog_product_entity_price_index ON public.catalog_product_entity USING btree (price);


--
-- TOC entry 5159 (class 1259 OID 25329)
-- Name: catalog_product_image_is_primary_index; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX catalog_product_image_is_primary_index ON public.catalog_product_image USING btree (is_primary);


--
-- TOC entry 5184 (class 1259 OID 25853)
-- Name: idx_cart_active; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_cart_active ON public.sales_cart USING btree (is_active);


--
-- TOC entry 5197 (class 1259 OID 25856)
-- Name: idx_cart_address_cart; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_cart_address_cart ON public.sales_cart_address USING btree (cart_id);


--
-- TOC entry 5198 (class 1259 OID 25857)
-- Name: idx_cart_address_type; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_cart_address_type ON public.sales_cart_address USING btree (address_type);


--
-- TOC entry 5202 (class 1259 OID 25858)
-- Name: idx_cart_payment_cart; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_cart_payment_cart ON public.sales_cart_payment USING btree (cart_id);


--
-- TOC entry 5191 (class 1259 OID 25854)
-- Name: idx_cart_product_cart; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_cart_product_cart ON public.sales_cart_product USING btree (cart_id);


--
-- TOC entry 5192 (class 1259 OID 25855)
-- Name: idx_cart_product_entity; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_cart_product_entity ON public.sales_cart_product USING btree (product_entity_id);


--
-- TOC entry 5185 (class 1259 OID 25852)
-- Name: idx_cart_session; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_cart_session ON public.sales_cart USING btree (session_id);


--
-- TOC entry 5186 (class 1259 OID 25851)
-- Name: idx_cart_user; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_cart_user ON public.sales_cart USING btree (user_id);


--
-- TOC entry 5182 (class 1259 OID 25849)
-- Name: idx_cat_prod_category; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_cat_prod_category ON public.catalog_category_product USING btree (category_entity_id);


--
-- TOC entry 5183 (class 1259 OID 25850)
-- Name: idx_cat_prod_product; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_cat_prod_product ON public.catalog_category_product USING btree (product_entity_id);


--
-- TOC entry 5170 (class 1259 OID 25846)
-- Name: idx_category_active; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_category_active ON public.catalog_category_entity USING btree (is_active);


--
-- TOC entry 5176 (class 1259 OID 25848)
-- Name: idx_category_attr_code; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_category_attr_code ON public.catalog_category_attribute USING btree (attribute_code);


--
-- TOC entry 5177 (class 1259 OID 25847)
-- Name: idx_category_attr_entity; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_category_attr_entity ON public.catalog_category_attribute USING btree (category_entity_id);


--
-- TOC entry 5171 (class 1259 OID 25845)
-- Name: idx_category_parent; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_category_parent ON public.catalog_category_entity USING btree (parent_id);


--
-- TOC entry 5172 (class 1259 OID 25844)
-- Name: idx_category_slug; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_category_slug ON public.catalog_category_entity USING btree (slug);


--
-- TOC entry 5221 (class 1259 OID 25865)
-- Name: idx_order_address_order; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_order_address_order ON public.sales_order_address USING btree (order_id);


--
-- TOC entry 5222 (class 1259 OID 25866)
-- Name: idx_order_address_type; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_order_address_type ON public.sales_order_address USING btree (address_type);


--
-- TOC entry 5207 (class 1259 OID 25862)
-- Name: idx_order_created; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_order_created ON public.sales_order USING btree (created_at);


--
-- TOC entry 5208 (class 1259 OID 25859)
-- Name: idx_order_number; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_order_number ON public.sales_order USING btree (order_number);


--
-- TOC entry 5225 (class 1259 OID 25867)
-- Name: idx_order_payment_order; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_order_payment_order ON public.sales_order_payment USING btree (order_id);


--
-- TOC entry 5217 (class 1259 OID 25864)
-- Name: idx_order_product_entity; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_order_product_entity ON public.sales_order_product USING btree (product_entity_id);


--
-- TOC entry 5218 (class 1259 OID 25863)
-- Name: idx_order_product_order; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_order_product_order ON public.sales_order_product USING btree (order_id);


--
-- TOC entry 5209 (class 1259 OID 25861)
-- Name: idx_order_status; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_order_status ON public.sales_order USING btree (status);


--
-- TOC entry 5210 (class 1259 OID 25860)
-- Name: idx_order_user; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_order_user ON public.sales_order USING btree (user_id);


--
-- TOC entry 5156 (class 1259 OID 25840)
-- Name: idx_product_attr_code; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_product_attr_code ON public.catalog_product_attribute USING btree (attribute_code);


--
-- TOC entry 5157 (class 1259 OID 25839)
-- Name: idx_product_attr_entity; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_product_attr_entity ON public.catalog_product_attribute USING btree (product_entity_id);


--
-- TOC entry 5158 (class 1259 OID 25841)
-- Name: idx_product_attr_value; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_product_attr_value ON public.catalog_product_attribute USING btree (attribute_value);


--
-- TOC entry 5149 (class 1259 OID 25836)
-- Name: idx_product_featured; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_product_featured ON public.catalog_product_entity USING btree (is_featured);


--
-- TOC entry 5162 (class 1259 OID 25842)
-- Name: idx_product_image_entity; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_product_image_entity ON public.catalog_product_image USING btree (product_entity_id);


--
-- TOC entry 5163 (class 1259 OID 25843)
-- Name: idx_product_image_primary; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_product_image_primary ON public.catalog_product_image USING btree (is_primary);


--
-- TOC entry 5150 (class 1259 OID 25837)
-- Name: idx_product_new; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_product_new ON public.catalog_product_entity USING btree (is_new);


--
-- TOC entry 5151 (class 1259 OID 25838)
-- Name: idx_product_price; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_product_price ON public.catalog_product_entity USING btree (price);


--
-- TOC entry 5152 (class 1259 OID 25835)
-- Name: idx_product_sku; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_product_sku ON public.catalog_product_entity USING btree (sku);


--
-- TOC entry 5274 (class 1259 OID 32999)
-- Name: idx_review_product; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_review_product ON public.product_reviews USING btree (product_entity_id);


--
-- TOC entry 5260 (class 1259 OID 32970)
-- Name: idx_saved_user; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_saved_user ON public.saved_items USING btree (user_id);


--
-- TOC entry 5269 (class 1259 OID 32969)
-- Name: idx_wishlist_user; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_wishlist_user ON public.wishlist USING btree (user_id);


--
-- TOC entry 5134 (class 1259 OID 25225)
-- Name: jobs_queue_index; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX jobs_queue_index ON public.jobs USING btree (queue);


--
-- TOC entry 5199 (class 1259 OID 25468)
-- Name: sales_cart_address_address_type_index; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX sales_cart_address_address_type_index ON public.sales_cart_address USING btree (address_type);


--
-- TOC entry 5187 (class 1259 OID 25418)
-- Name: sales_cart_is_active_index; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX sales_cart_is_active_index ON public.sales_cart USING btree (is_active);


--
-- TOC entry 5190 (class 1259 OID 25417)
-- Name: sales_cart_session_id_index; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX sales_cart_session_id_index ON public.sales_cart USING btree (session_id);


--
-- TOC entry 5211 (class 1259 OID 25514)
-- Name: sales_order_order_number_index; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX sales_order_order_number_index ON public.sales_order USING btree (order_number);


--
-- TOC entry 5216 (class 1259 OID 25515)
-- Name: sales_order_status_index; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX sales_order_status_index ON public.sales_order USING btree (status);


--
-- TOC entry 5122 (class 1259 OID 25187)
-- Name: sessions_last_activity_index; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX sessions_last_activity_index ON public.sessions USING btree (last_activity);


--
-- TOC entry 5125 (class 1259 OID 25186)
-- Name: sessions_user_id_index; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX sessions_user_id_index ON public.sessions USING btree (user_id);


--
-- TOC entry 5306 (class 2606 OID 25740)
-- Name: cart_items cart_items_cart_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.cart_items
    ADD CONSTRAINT cart_items_cart_id_fkey FOREIGN KEY (cart_id) REFERENCES public.carts(id) ON DELETE CASCADE;


--
-- TOC entry 5307 (class 2606 OID 25745)
-- Name: cart_items cart_items_product_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.cart_items
    ADD CONSTRAINT cart_items_product_id_fkey FOREIGN KEY (product_id) REFERENCES public.products(id) ON DELETE CASCADE;


--
-- TOC entry 5305 (class 2606 OID 25723)
-- Name: carts carts_user_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.carts
    ADD CONSTRAINT carts_user_id_fkey FOREIGN KEY (user_id) REFERENCES public.users(id) ON DELETE CASCADE;


--
-- TOC entry 5284 (class 2606 OID 25370)
-- Name: catalog_category_attribute catalog_category_attribute_category_entity_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.catalog_category_attribute
    ADD CONSTRAINT catalog_category_attribute_category_entity_id_foreign FOREIGN KEY (category_entity_id) REFERENCES public.catalog_category_entity(entity_id) ON DELETE CASCADE;


--
-- TOC entry 5283 (class 2606 OID 25346)
-- Name: catalog_category_entity catalog_category_entity_parent_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.catalog_category_entity
    ADD CONSTRAINT catalog_category_entity_parent_id_foreign FOREIGN KEY (parent_id) REFERENCES public.catalog_category_entity(entity_id) ON DELETE SET NULL;


--
-- TOC entry 5285 (class 2606 OID 25390)
-- Name: catalog_category_product catalog_category_product_category_entity_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.catalog_category_product
    ADD CONSTRAINT catalog_category_product_category_entity_id_foreign FOREIGN KEY (category_entity_id) REFERENCES public.catalog_category_entity(entity_id) ON DELETE CASCADE;


--
-- TOC entry 5286 (class 2606 OID 25395)
-- Name: catalog_category_product catalog_category_product_product_entity_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.catalog_category_product
    ADD CONSTRAINT catalog_category_product_product_entity_id_foreign FOREIGN KEY (product_entity_id) REFERENCES public.catalog_product_entity(entity_id) ON DELETE CASCADE;


--
-- TOC entry 5281 (class 2606 OID 25302)
-- Name: catalog_product_attribute catalog_product_attribute_product_entity_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.catalog_product_attribute
    ADD CONSTRAINT catalog_product_attribute_product_entity_id_foreign FOREIGN KEY (product_entity_id) REFERENCES public.catalog_product_entity(entity_id) ON DELETE CASCADE;


--
-- TOC entry 5282 (class 2606 OID 25324)
-- Name: catalog_product_image catalog_product_image_product_entity_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.catalog_product_image
    ADD CONSTRAINT catalog_product_image_product_entity_id_foreign FOREIGN KEY (product_entity_id) REFERENCES public.catalog_product_entity(entity_id) ON DELETE CASCADE;


--
-- TOC entry 5298 (class 2606 OID 25606)
-- Name: catalog_wishlist catalog_wishlist_product_entity_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.catalog_wishlist
    ADD CONSTRAINT catalog_wishlist_product_entity_id_foreign FOREIGN KEY (product_entity_id) REFERENCES public.catalog_product_entity(entity_id) ON DELETE CASCADE;


--
-- TOC entry 5299 (class 2606 OID 25601)
-- Name: catalog_wishlist catalog_wishlist_user_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.catalog_wishlist
    ADD CONSTRAINT catalog_wishlist_user_id_foreign FOREIGN KEY (user_id) REFERENCES public.users(id) ON DELETE CASCADE;


--
-- TOC entry 5303 (class 2606 OID 25703)
-- Name: order_items order_items_order_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.order_items
    ADD CONSTRAINT order_items_order_id_fkey FOREIGN KEY (order_id) REFERENCES public.orders(id) ON DELETE CASCADE;


--
-- TOC entry 5304 (class 2606 OID 25708)
-- Name: order_items order_items_product_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.order_items
    ADD CONSTRAINT order_items_product_id_fkey FOREIGN KEY (product_id) REFERENCES public.products(id);


--
-- TOC entry 5302 (class 2606 OID 25688)
-- Name: orders orders_user_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.orders
    ADD CONSTRAINT orders_user_id_fkey FOREIGN KEY (user_id) REFERENCES public.users(id);


--
-- TOC entry 5314 (class 2606 OID 32989)
-- Name: product_reviews product_reviews_product_entity_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.product_reviews
    ADD CONSTRAINT product_reviews_product_entity_id_fkey FOREIGN KEY (product_entity_id) REFERENCES public.catalog_product_entity(entity_id) ON DELETE CASCADE;


--
-- TOC entry 5315 (class 2606 OID 32994)
-- Name: product_reviews product_reviews_user_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.product_reviews
    ADD CONSTRAINT product_reviews_user_id_fkey FOREIGN KEY (user_id) REFERENCES public.users(id) ON DELETE CASCADE;


--
-- TOC entry 5300 (class 2606 OID 25666)
-- Name: products products_brand_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.products
    ADD CONSTRAINT products_brand_id_fkey FOREIGN KEY (brand_id) REFERENCES public.brands(id);


--
-- TOC entry 5301 (class 2606 OID 25661)
-- Name: products products_category_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.products
    ADD CONSTRAINT products_category_id_fkey FOREIGN KEY (category_id) REFERENCES public.categories(id);


--
-- TOC entry 5290 (class 2606 OID 25463)
-- Name: sales_cart_address sales_cart_address_cart_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.sales_cart_address
    ADD CONSTRAINT sales_cart_address_cart_id_foreign FOREIGN KEY (cart_id) REFERENCES public.sales_cart(cart_id) ON DELETE CASCADE;


--
-- TOC entry 5291 (class 2606 OID 25478)
-- Name: sales_cart_payment sales_cart_payment_cart_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.sales_cart_payment
    ADD CONSTRAINT sales_cart_payment_cart_id_foreign FOREIGN KEY (cart_id) REFERENCES public.sales_cart(cart_id) ON DELETE CASCADE;


--
-- TOC entry 5288 (class 2606 OID 25431)
-- Name: sales_cart_product sales_cart_product_cart_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.sales_cart_product
    ADD CONSTRAINT sales_cart_product_cart_id_foreign FOREIGN KEY (cart_id) REFERENCES public.sales_cart(cart_id) ON DELETE CASCADE;


--
-- TOC entry 5289 (class 2606 OID 25436)
-- Name: sales_cart_product sales_cart_product_product_entity_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.sales_cart_product
    ADD CONSTRAINT sales_cart_product_product_entity_id_foreign FOREIGN KEY (product_entity_id) REFERENCES public.catalog_product_entity(entity_id) ON DELETE CASCADE;


--
-- TOC entry 5287 (class 2606 OID 25412)
-- Name: sales_cart sales_cart_user_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.sales_cart
    ADD CONSTRAINT sales_cart_user_id_foreign FOREIGN KEY (user_id) REFERENCES public.users(id) ON DELETE CASCADE;


--
-- TOC entry 5296 (class 2606 OID 25566)
-- Name: sales_order_address sales_order_address_order_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.sales_order_address
    ADD CONSTRAINT sales_order_address_order_id_foreign FOREIGN KEY (order_id) REFERENCES public.sales_order(order_id) ON DELETE CASCADE;


--
-- TOC entry 5292 (class 2606 OID 25509)
-- Name: sales_order sales_order_original_cart_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.sales_order
    ADD CONSTRAINT sales_order_original_cart_id_foreign FOREIGN KEY (original_cart_id) REFERENCES public.sales_cart(cart_id);


--
-- TOC entry 5297 (class 2606 OID 25584)
-- Name: sales_order_payment sales_order_payment_order_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.sales_order_payment
    ADD CONSTRAINT sales_order_payment_order_id_foreign FOREIGN KEY (order_id) REFERENCES public.sales_order(order_id) ON DELETE CASCADE;


--
-- TOC entry 5294 (class 2606 OID 25534)
-- Name: sales_order_product sales_order_product_order_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.sales_order_product
    ADD CONSTRAINT sales_order_product_order_id_foreign FOREIGN KEY (order_id) REFERENCES public.sales_order(order_id) ON DELETE CASCADE;


--
-- TOC entry 5295 (class 2606 OID 25539)
-- Name: sales_order_product sales_order_product_product_entity_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.sales_order_product
    ADD CONSTRAINT sales_order_product_product_entity_id_foreign FOREIGN KEY (product_entity_id) REFERENCES public.catalog_product_entity(entity_id) ON DELETE SET NULL;


--
-- TOC entry 5293 (class 2606 OID 25504)
-- Name: sales_order sales_order_user_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.sales_order
    ADD CONSTRAINT sales_order_user_id_foreign FOREIGN KEY (user_id) REFERENCES public.users(id) ON DELETE SET NULL;


--
-- TOC entry 5310 (class 2606 OID 25787)
-- Name: saved_items saved_items_product_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.saved_items
    ADD CONSTRAINT saved_items_product_id_fkey FOREIGN KEY (product_id) REFERENCES public.products(id) ON DELETE CASCADE;


--
-- TOC entry 5311 (class 2606 OID 25782)
-- Name: saved_items saved_items_user_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.saved_items
    ADD CONSTRAINT saved_items_user_id_fkey FOREIGN KEY (user_id) REFERENCES public.users(id) ON DELETE CASCADE;


--
-- TOC entry 5316 (class 2606 OID 33023)
-- Name: user_addresses user_addresses_user_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.user_addresses
    ADD CONSTRAINT user_addresses_user_id_fkey FOREIGN KEY (user_id) REFERENCES public.users(id) ON DELETE CASCADE;


--
-- TOC entry 5312 (class 2606 OID 32964)
-- Name: wishlist wishlist_product_entity_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.wishlist
    ADD CONSTRAINT wishlist_product_entity_id_fkey FOREIGN KEY (product_entity_id) REFERENCES public.catalog_product_entity(entity_id) ON DELETE CASCADE;


--
-- TOC entry 5313 (class 2606 OID 32959)
-- Name: wishlist wishlist_user_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.wishlist
    ADD CONSTRAINT wishlist_user_id_fkey FOREIGN KEY (user_id) REFERENCES public.users(id) ON DELETE CASCADE;


--
-- TOC entry 5308 (class 2606 OID 25766)
-- Name: wishlists wishlists_product_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.wishlists
    ADD CONSTRAINT wishlists_product_id_fkey FOREIGN KEY (product_id) REFERENCES public.products(id) ON DELETE CASCADE;


--
-- TOC entry 5309 (class 2606 OID 25761)
-- Name: wishlists wishlists_user_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.wishlists
    ADD CONSTRAINT wishlists_user_id_fkey FOREIGN KEY (user_id) REFERENCES public.users(id) ON DELETE CASCADE;


-- Completed on 2026-02-04 18:11:43

--
-- PostgreSQL database dump complete
--

\unrestrict 2QMHPmMxUF9NyEaG1JF3okCcTrfoZzfGOhRGNeHZU3XhhchPxeRfZ9ufi7ducmJ

