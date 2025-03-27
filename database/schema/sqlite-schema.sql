CREATE TABLE IF NOT EXISTS "migrations"(
  "id" integer primary key autoincrement not null,
  "migration" varchar not null,
  "batch" integer not null
);
CREATE TABLE IF NOT EXISTS "users"(
  "id" integer primary key autoincrement not null,
  "name" varchar not null,
  "email" varchar not null,
  "email_verified_at" datetime,
  "password" varchar not null,
  "remember_token" varchar,
  "created_at" datetime,
  "updated_at" datetime,
  "phone" varchar,
  "avatar" varchar,
  "role" varchar not null default 'user'
);
CREATE UNIQUE INDEX "users_email_unique" on "users"("email");
CREATE TABLE IF NOT EXISTS "password_reset_tokens"(
  "email" varchar not null,
  "token" varchar not null,
  "created_at" datetime,
  primary key("email")
);
CREATE TABLE IF NOT EXISTS "sessions"(
  "id" varchar not null,
  "user_id" integer,
  "ip_address" varchar,
  "user_agent" text,
  "payload" text not null,
  "last_activity" integer not null,
  primary key("id")
);
CREATE INDEX "sessions_user_id_index" on "sessions"("user_id");
CREATE INDEX "sessions_last_activity_index" on "sessions"("last_activity");
CREATE TABLE IF NOT EXISTS "cache"(
  "key" varchar not null,
  "value" text not null,
  "expiration" integer not null,
  primary key("key")
);
CREATE TABLE IF NOT EXISTS "cache_locks"(
  "key" varchar not null,
  "owner" varchar not null,
  "expiration" integer not null,
  primary key("key")
);
CREATE TABLE IF NOT EXISTS "jobs"(
  "id" integer primary key autoincrement not null,
  "queue" varchar not null,
  "payload" text not null,
  "attempts" integer not null,
  "reserved_at" integer,
  "available_at" integer not null,
  "created_at" integer not null
);
CREATE INDEX "jobs_queue_index" on "jobs"("queue");
CREATE TABLE IF NOT EXISTS "job_batches"(
  "id" varchar not null,
  "name" varchar not null,
  "total_jobs" integer not null,
  "pending_jobs" integer not null,
  "failed_jobs" integer not null,
  "failed_job_ids" text not null,
  "options" text,
  "cancelled_at" integer,
  "created_at" integer not null,
  "finished_at" integer,
  primary key("id")
);
CREATE TABLE IF NOT EXISTS "failed_jobs"(
  "id" integer primary key autoincrement not null,
  "uuid" varchar not null,
  "connection" text not null,
  "queue" text not null,
  "payload" text not null,
  "exception" text not null,
  "failed_at" datetime not null default CURRENT_TIMESTAMP
);
CREATE UNIQUE INDEX "failed_jobs_uuid_unique" on "failed_jobs"("uuid");
CREATE TABLE IF NOT EXISTS "brands"(
  "id" integer primary key autoincrement not null,
  "name" varchar not null,
  "created_at" datetime,
  "updated_at" datetime
);
CREATE TABLE IF NOT EXISTS "categories"(
  "id" integer primary key autoincrement not null,
  "name" varchar not null,
  "created_at" datetime,
  "updated_at" datetime,
  "image" varchar
);
CREATE TABLE IF NOT EXISTS "products"(
  "id" integer primary key autoincrement not null,
  "name" varchar not null,
  "brand_id" integer not null,
  "category_id" integer not null,
  "price" numeric not null,
  "stock" integer not null,
  "description" text,
  "image" varchar,
  "created_at" datetime,
  "updated_at" datetime,
  foreign key("brand_id") references "brands"("id") on delete cascade,
  foreign key("category_id") references "categories"("id") on delete cascade
);
CREATE TABLE IF NOT EXISTS "promo_codes"(
  "id" integer primary key autoincrement not null,
  "code" varchar not null,
  "discount" numeric not null,
  "usage_limit" integer not null default '1',
  "created_at" datetime,
  "updated_at" datetime
);
CREATE UNIQUE INDEX "promo_codes_code_unique" on "promo_codes"("code");
CREATE TABLE IF NOT EXISTS "transactions"(
  "id" integer primary key autoincrement not null,
  "order_id" varchar not null,
  "user_id" integer not null,
  "total_amount" numeric not null,
  "status" varchar not null default 'pending',
  "created_at" datetime,
  "updated_at" datetime
);
CREATE UNIQUE INDEX "transactions_order_id_unique" on "transactions"(
  "order_id"
);
CREATE TABLE IF NOT EXISTS "product_transaction"(
  "id" integer primary key autoincrement not null,
  "transaction_id" integer not null,
  "product_id" integer not null,
  "quantity" integer not null,
  "price" numeric not null,
  "created_at" datetime,
  "updated_at" datetime,
  foreign key("transaction_id") references "transactions"("id") on delete cascade,
  foreign key("product_id") references "products"("id") on delete cascade
);
CREATE TABLE IF NOT EXISTS "promos"(
  "id" integer primary key autoincrement not null,
  "code" varchar not null,
  "discount" numeric not null,
  "discount_type" varchar check("discount_type" in('percentage', 'fixed')) not null,
  "valid_until" date not null,
  "created_at" datetime,
  "updated_at" datetime
);
CREATE UNIQUE INDEX "promos_code_unique" on "promos"("code");
CREATE TABLE IF NOT EXISTS "transaction_items"(
  "id" integer primary key autoincrement not null,
  "transaction_id" integer not null,
  "product_id" integer not null,
  "quantity" integer not null,
  "price" numeric not null,
  "created_at" datetime,
  "updated_at" datetime,
  foreign key("transaction_id") references "transactions"("id") on delete cascade,
  foreign key("product_id") references "products"("id") on delete cascade
);
CREATE TABLE IF NOT EXISTS "wishlist_items"(
  "id" integer primary key autoincrement not null,
  "created_at" datetime,
  "updated_at" datetime
);
CREATE TABLE IF NOT EXISTS "personal_access_tokens"(
  "id" integer primary key autoincrement not null,
  "tokenable_type" varchar not null,
  "tokenable_id" integer not null,
  "name" varchar not null,
  "token" varchar not null,
  "abilities" text,
  "last_used_at" datetime,
  "expires_at" datetime,
  "created_at" datetime,
  "updated_at" datetime
);
CREATE INDEX "personal_access_tokens_tokenable_type_tokenable_id_index" on "personal_access_tokens"(
  "tokenable_type",
  "tokenable_id"
);
CREATE UNIQUE INDEX "personal_access_tokens_token_unique" on "personal_access_tokens"(
  "token"
);
CREATE TABLE IF NOT EXISTS "addresses"(
  "id" integer primary key autoincrement not null,
  "user_id" integer not null,
  "name" varchar not null,
  "phone" varchar not null,
  "address_line1" text not null,
  "address_line2" text,
  "city" varchar not null,
  "postal_code" varchar not null,
  "province" varchar not null,
  "country" varchar not null default 'Indonesia',
  "is_default" tinyint(1) not null default '0',
  "created_at" datetime,
  "updated_at" datetime,
  foreign key("user_id") references "users"("id") on delete cascade
);
CREATE TABLE IF NOT EXISTS "cart_items"(
  "id" integer primary key autoincrement not null,
  "user_id" integer,
  "session_id" varchar,
  "product_id" integer not null,
  "quantity" integer not null,
  "created_at" datetime,
  "updated_at" datetime,
  foreign key("user_id") references "users"("id") on delete cascade,
  foreign key("product_id") references "products"("id") on delete cascade
);
CREATE INDEX "cart_items_user_id_session_id_product_id_index" on "cart_items"(
  "user_id",
  "session_id",
  "product_id"
);
CREATE TABLE IF NOT EXISTS "orders"(
  "id" integer primary key autoincrement not null,
  "user_id" integer,
  "order_number" varchar not null,
  "status" varchar check("status" in('pending', 'processing', 'completed', 'cancelled')) not null default 'pending',
  "total_amount" numeric not null,
  "shipping_address" varchar not null,
  "shipping_city" varchar not null,
  "shipping_state" varchar not null,
  "shipping_zipcode" varchar not null,
  "shipping_phone" varchar not null,
  "notes" text,
  "payment_method" varchar,
  "payment_status" varchar check("payment_status" in('pending', 'paid', 'failed', 'expired')) not null default 'pending',
  "transaction_id" varchar,
  "snap_token" varchar,
  "shipping_cost" numeric not null default '0',
  "tax_amount" numeric not null default '0',
  "discount_amount" numeric not null default '0',
  "created_at" datetime,
  "updated_at" datetime,
  foreign key("user_id") references "users"("id") on delete set null
);
CREATE UNIQUE INDEX "orders_order_number_unique" on "orders"("order_number");
CREATE TABLE IF NOT EXISTS "order_items"(
  "id" integer primary key autoincrement not null,
  "order_id" integer not null,
  "product_id" integer,
  "name" varchar not null,
  "price" numeric not null,
  "quantity" integer not null,
  "subtotal" numeric not null,
  "created_at" datetime,
  "updated_at" datetime,
  foreign key("order_id") references "orders"("id") on delete cascade,
  foreign key("product_id") references "products"("id") on delete set null
);
CREATE TABLE IF NOT EXISTS "wishlists"(
  "id" integer primary key autoincrement not null,
  "user_id" integer not null,
  "product_id" integer not null,
  "created_at" datetime,
  "updated_at" datetime,
  foreign key("user_id") references "users"("id") on delete cascade,
  foreign key("product_id") references "products"("id") on delete cascade
);
CREATE UNIQUE INDEX "wishlists_user_id_product_id_unique" on "wishlists"(
  "user_id",
  "product_id"
);

INSERT INTO migrations VALUES(12,'0001_01_01_000000_create_users_table',1);
INSERT INTO migrations VALUES(13,'0001_01_01_000001_create_cache_table',1);
INSERT INTO migrations VALUES(14,'0001_01_01_000002_create_jobs_table',1);
INSERT INTO migrations VALUES(15,'2025_03_24_080023_create_brands_table',1);
INSERT INTO migrations VALUES(16,'2025_03_24_080023_create_categories_table',1);
INSERT INTO migrations VALUES(17,'2025_03_24_080023_create_products_table',1);
INSERT INTO migrations VALUES(18,'2025_03_24_080024_create_promo_codes_table',1);
INSERT INTO migrations VALUES(19,'2025_03_24_080024_create_transactions_table',1);
INSERT INTO migrations VALUES(20,'2025_03_24_081834_create_product_transaction_table',1);
INSERT INTO migrations VALUES(21,'2025_03_24_082040_create_promos_table',1);
INSERT INTO migrations VALUES(22,'2025_03_24_085503_create_transaction_items_table',1);
INSERT INTO migrations VALUES(23,'2025_03_25_084935_add_image_to_categories_table',2);
INSERT INTO migrations VALUES(24,'2025_03_25_144535_create_wishlist_items_table',3);
INSERT INTO migrations VALUES(25,'2025_03_25_144754_create_personal_access_tokens_table',3);
INSERT INTO migrations VALUES(26,'2025_03_25_161329_crate_addresses_table',4);
INSERT INTO migrations VALUES(27,'2025_03_25_161437_crate_orders_table',4);
INSERT INTO migrations VALUES(28,'2025_03_25_161329_create_addresses_table',5);
INSERT INTO migrations VALUES(29,'2025_03_25_161437_create_orders_table',6);
INSERT INTO migrations VALUES(30,'2025_03_25_161544_create_order_items_table',6);
INSERT INTO migrations VALUES(31,'2025_03_25_161916_create_category_product_table',6);
INSERT INTO migrations VALUES(32,'2025_03_25_162705_update_addresses_table',6);
INSERT INTO migrations VALUES(33,'2025_03_26_042815_add_phone_to_users_table',7);
INSERT INTO migrations VALUES(34,'2025_03_26_054626_create_cart_items_table',8);
INSERT INTO migrations VALUES(36,'2025_03_26_062749_add_missing_columns_to_orders_table',9);
INSERT INTO migrations VALUES(37,'2025_03_26_063006_add_other_missing_columns_to_orders_table',10);
INSERT INTO migrations VALUES(38,'2025_03_26_063132_recreate_orders_table',10);
INSERT INTO migrations VALUES(39,'2025_03_26_063149_create_order_items_table',10);
INSERT INTO migrations VALUES(40,'2025_03_26_063842_add_other_required_columns_to_orders_table',10);
INSERT INTO migrations VALUES(41,'2025_03_26_081019_create_wishlists_table',11);
INSERT INTO migrations VALUES(42,'2025_03_27_054652_add_role_to_users_table',12);
