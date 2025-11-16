/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;
DROP TABLE IF EXISTS `billings_app_usage_configs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `billings_app_usage_configs` (
  `id` char(36) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `store_id` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `subscription_line_item_id` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `config` json NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `currency` varchar(3) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `valid_from` datetime NOT NULL,
  `valid_to` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `app_usage_config_store_id_index` (`store_id`),
  KEY `app_usage_config_subscription_line_item_id_index` (`subscription_line_item_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `billings_app_usage_records`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `billings_app_usage_records` (
  `id` char(36) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `subscription_line_item_id` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `shopify_app_subscription_line_item_id` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `amount` int NOT NULL,
  `amount_currency` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'ISO 4217',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `app_usage_subscription_line_item_id_index` (`subscription_line_item_id`),
  KEY `app_usage_shopify_app_subscription_line_item_id_index` (`shopify_app_subscription_line_item_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `billings_charges`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `billings_charges` (
  `id` char(36) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `store_id` char(36) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `tbyb_net_sale_id` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `currency` varchar(3) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `amount` int NOT NULL,
  `balance` bigint NOT NULL DEFAULT '0',
  `is_billed` tinyint(1) NOT NULL,
  `billed_at` datetime DEFAULT NULL,
  `time_range_start` datetime DEFAULT NULL,
  `time_range_end` datetime DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `step_size` int DEFAULT NULL,
  `step_start_amount` int DEFAULT NULL,
  `step_end_amount` int DEFAULT NULL,
  `is_first_of_billing_period` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `billings_charges_store_id_index` (`store_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `billings_subscription_line_items`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `billings_subscription_line_items` (
  `id` char(36) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `subscription_id` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `shopify_app_subscription_id` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `shopify_app_subscription_line_item_id` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `type` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'usage' COMMENT 'usage|recurring',
  `terms` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `recurring_amount` int DEFAULT NULL,
  `recurring_amount_currency` varchar(3) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'USD' COMMENT 'ISO 4217',
  `usage_capped_amount` int DEFAULT NULL,
  `usage_capped_amount_currency` varchar(3) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'USD' COMMENT 'ISO 4217',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `sub_line_items_subscription_id_index` (`subscription_id`),
  KEY `sub_line_items_shopify_app_subscription_id_index` (`shopify_app_subscription_id`),
  KEY `sub_line_items_shopify_app_subscription_line_item_id_index` (`shopify_app_subscription_line_item_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `billings_subscriptions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `billings_subscriptions` (
  `id` char(36) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `store_id` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `shopify_app_subscription_id` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `shopify_confirmation_url` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `current_period_start` timestamp NULL DEFAULT NULL,
  `current_period_end` datetime DEFAULT NULL,
  `trial_days` int NOT NULL DEFAULT '0',
  `trial_period_end` datetime DEFAULT NULL,
  `is_test` tinyint(1) NOT NULL DEFAULT '0',
  `activated_at` datetime DEFAULT NULL,
  `deactivated_at` datetime DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `subscriptions_store_id_index` (`store_id`),
  KEY `subscriptions_shopify_app_subscription_id_index` (`shopify_app_subscription_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `billings_tbyb_net_sales`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `billings_tbyb_net_sales` (
  `id` char(36) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `store_id` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `date_start` date NOT NULL,
  `date_end` date NOT NULL,
  `time_range_start` timestamp NOT NULL,
  `time_range_end` timestamp NOT NULL,
  `currency` varchar(3) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `tbyb_gross_sales` int NOT NULL DEFAULT '0',
  `tbyb_discounts` int NOT NULL DEFAULT '0',
  `tbyb_refunded_gross_sales` int NOT NULL DEFAULT '0',
  `tbyb_refunded_discounts` int NOT NULL DEFAULT '0',
  `tbyb_net_sales` int NOT NULL DEFAULT '0',
  `is_first_of_billing_period` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `billings_tbyb_net_sales_store_id_index` (`store_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `failed_jobs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `failed_jobs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `uuid` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `connection` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `queue` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `exception` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `migrations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `migrations` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `orders`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `orders` (
  `id` char(36) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `store_id` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `source_id` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `order_data` json NOT NULL,
  `blackcart_metadata` json DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `taxes_included` tinyint(1) NOT NULL DEFAULT '0',
  `taxes_exempt` tinyint(1) NOT NULL DEFAULT '0',
  `tags` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `discount_codes` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `test` tinyint(1) NOT NULL DEFAULT '0',
  `payment_terms_id` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `payment_terms_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `payment_terms_type` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `trial_expires_at` timestamp NULL DEFAULT NULL,
  `shop_currency` varchar(3) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'USD',
  `customer_currency` varchar(3) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'USD',
  `total_shop_amount` int NOT NULL DEFAULT '0',
  `total_customer_amount` int NOT NULL DEFAULT '0',
  `outstanding_shop_amount` int NOT NULL DEFAULT '0',
  `outstanding_customer_amount` int NOT NULL DEFAULT '0',
  `original_tbyb_gross_sales_shop_amount` int NOT NULL DEFAULT '0',
  `original_tbyb_gross_sales_customer_amount` int NOT NULL DEFAULT '0',
  `original_upfront_gross_sales_shop_amount` int NOT NULL DEFAULT '0',
  `original_upfront_gross_sales_customer_amount` int NOT NULL DEFAULT '0',
  `original_total_gross_sales_shop_amount` int NOT NULL DEFAULT '0',
  `original_total_gross_sales_customer_amount` int NOT NULL DEFAULT '0',
  `original_tbyb_discounts_shop_amount` int NOT NULL DEFAULT '0',
  `original_tbyb_discounts_customer_amount` int NOT NULL DEFAULT '0',
  `original_upfront_discounts_shop_amount` int NOT NULL DEFAULT '0',
  `original_upfront_discounts_customer_amount` int NOT NULL DEFAULT '0',
  `original_total_discounts_shop_amount` int NOT NULL DEFAULT '0',
  `original_total_discounts_customer_amount` int NOT NULL DEFAULT '0',
  `tbyb_refund_gross_sales_shop_amount` int NOT NULL DEFAULT '0',
  `tbyb_refund_gross_sales_customer_amount` int NOT NULL DEFAULT '0',
  `upfront_refund_gross_sales_shop_amount` int NOT NULL DEFAULT '0',
  `upfront_refund_gross_sales_customer_amount` int NOT NULL DEFAULT '0',
  `total_order_level_refunds_shop_amount` int NOT NULL DEFAULT '0',
  `total_order_level_refunds_customer_amount` int NOT NULL DEFAULT '0',
  `tbyb_refund_discounts_shop_amount` int NOT NULL DEFAULT '0',
  `tbyb_refund_discounts_customer_amount` int NOT NULL DEFAULT '0',
  `upfront_refund_discounts_shop_amount` int NOT NULL DEFAULT '0',
  `upfront_refund_discounts_customer_amount` int NOT NULL DEFAULT '0',
  `tbyb_net_sales_shop_amount` int NOT NULL DEFAULT '0',
  `tbyb_net_sales_customer_amount` int NOT NULL DEFAULT '0',
  `upfront_net_sales_shop_amount` int NOT NULL DEFAULT '0',
  `upfront_net_sales_customer_amount` int NOT NULL DEFAULT '0',
  `total_net_sales_shop_amount` int NOT NULL DEFAULT '0',
  `total_net_sales_customer_amount` int NOT NULL DEFAULT '0',
  `completed_at` timestamp NULL DEFAULT NULL,
  `original_outstanding_shop_amount` int NOT NULL DEFAULT '0',
  `original_outstanding_customer_amount` int NOT NULL DEFAULT '0',
  `assumed_delivery_merchant_email_sent_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `orders_source_id_unique` (`source_id`),
  KEY `orders_store_id_index` (`store_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `orders_line_items`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `orders_line_items` (
  `id` char(36) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `source_id` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `source_product_id` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `source_variant_id` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `product_title` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `variant_title` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `thumbnail` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `order_id` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `source_order_id` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `quantity` int NOT NULL,
  `original_quantity` int NOT NULL DEFAULT '1',
  `status` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `trialable_id` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `trial_group_id` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `is_tbyb` tinyint(1) NOT NULL DEFAULT '0',
  `selling_plan_id` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `deposit_type` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `deposit_value` int DEFAULT NULL,
  `deposit_shop_amount` bigint DEFAULT NULL,
  `deposit_customer_amount` bigint DEFAULT NULL,
  `shop_currency` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'USD',
  `customer_currency` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'USD',
  `price_shop_amount` int NOT NULL DEFAULT '0',
  `price_customer_amount` int NOT NULL DEFAULT '0',
  `total_price_shop_amount` int NOT NULL DEFAULT '0',
  `total_price_customer_amount` int NOT NULL DEFAULT '0',
  `discount_shop_amount` int NOT NULL DEFAULT '0',
  `discount_customer_amount` int NOT NULL DEFAULT '0',
  `tax_shop_amount` int NOT NULL DEFAULT '0',
  `tax_customer_amount` int NOT NULL DEFAULT '0',
  `decision_status` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'kept',
  `status_updated_by` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'shopify',
  PRIMARY KEY (`id`),
  UNIQUE KEY `orders_line_items_source_id_unique` (`source_id`),
  KEY `orders_line_items_order_id_index` (`order_id`),
  KEY `orders_line_items_source_order_id_index` (`source_order_id`),
  KEY `orders_line_items_source_product_id_index` (`source_product_id`),
  KEY `orders_line_items_source_variant_id_index` (`source_variant_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `orders_refund_line_items`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `orders_refund_line_items` (
  `id` char(36) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `source_refund_reference_id` char(36) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `line_item_id` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `quantity` int NOT NULL,
  `shop_currency` varchar(3) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `customer_currency` varchar(3) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `gross_sales_shop_amount` int NOT NULL,
  `gross_sales_customer_amount` int NOT NULL,
  `discounts_shop_amount` int NOT NULL,
  `discounts_customer_amount` int NOT NULL,
  `is_tbyb` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `tax_shop_amount` int NOT NULL,
  `tax_customer_amount` int NOT NULL,
  `total_shop_amount` int NOT NULL,
  `total_customer_amount` int NOT NULL,
  `refund_id` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `deposit_shop_amount` int NOT NULL,
  `deposit_customer_amount` int NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `orders_refunds`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `orders_refunds` (
  `id` char(36) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `source_refund_reference_id` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `order_id` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `shop_currency` varchar(3) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `customer_currency` varchar(3) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `tbyb_gross_sales_shop_amount` int NOT NULL DEFAULT '0',
  `tbyb_gross_sales_customer_amount` int NOT NULL DEFAULT '0',
  `tbyb_discounts_shop_amount` int NOT NULL DEFAULT '0',
  `tbyb_discounts_customer_amount` int NOT NULL DEFAULT '0',
  `upfront_gross_sales_shop_amount` int NOT NULL DEFAULT '0',
  `upfront_gross_sales_customer_amount` int NOT NULL DEFAULT '0',
  `upfront_discounts_shop_amount` int NOT NULL DEFAULT '0',
  `upfront_discounts_customer_amount` int NOT NULL DEFAULT '0',
  `order_level_refund_shop_amount` int NOT NULL DEFAULT '0',
  `order_level_refund_customer_amount` int NOT NULL DEFAULT '0',
  `store_id` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tbyb_total_shop_amount` int DEFAULT NULL,
  `tbyb_total_customer_amount` int DEFAULT NULL,
  `upfront_total_shop_amount` int DEFAULT NULL,
  `upfront_total_customer_amount` int DEFAULT NULL,
  `tbyb_deposit_shop_amount` int NOT NULL,
  `tbyb_deposit_customer_amount` int NOT NULL,
  `refunded_shop_amount` int NOT NULL DEFAULT '0',
  `refunded_customer_amount` int NOT NULL DEFAULT '0',
  `refund_data` json DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `orders_refunds_source_id_unique` (`source_refund_reference_id`),
  KEY `orders_refunds_order_id_index` (`order_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `orders_returns`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `orders_returns` (
  `id` char(36) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `source_id` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `order_id` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `store_id` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `source_order_id` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `shop_currency` varchar(3) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `customer_currency` varchar(3) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `total_quantity` int NOT NULL DEFAULT '0',
  `tbyb_gross_sales_shop_amount` int NOT NULL DEFAULT '0',
  `tbyb_gross_sales_customer_amount` int NOT NULL DEFAULT '0',
  `tbyb_discounts_shop_amount` int NOT NULL DEFAULT '0',
  `tbyb_discounts_customer_amount` int NOT NULL DEFAULT '0',
  `upfront_gross_sales_shop_amount` int NOT NULL DEFAULT '0',
  `upfront_gross_sales_customer_amount` int NOT NULL DEFAULT '0',
  `upfront_discounts_shop_amount` int NOT NULL DEFAULT '0',
  `upfront_discounts_customer_amount` int NOT NULL DEFAULT '0',
  `tbyb_tax_shop_amount` int NOT NULL DEFAULT '0',
  `tbyb_tax_customer_amount` int NOT NULL DEFAULT '0',
  `upfront_tax_shop_amount` int NOT NULL DEFAULT '0',
  `upfront_tax_customer_amount` int NOT NULL DEFAULT '0',
  `tbyb_total_shop_amount` int NOT NULL DEFAULT '0',
  `tbyb_total_customer_amount` int NOT NULL DEFAULT '0',
  `return_data` json NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `orders_returns_source_id_unique` (`source_id`),
  KEY `orders_returns_order_id_index` (`order_id`),
  KEY `orders_returns_store_id_index` (`store_id`),
  KEY `orders_returns_source_order_id_index` (`source_order_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `orders_returns_line_items`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `orders_returns_line_items` (
  `id` char(36) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `source_id` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `order_return_id` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `source_return_id` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `line_item_id` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `customer_note` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `quantity` int NOT NULL DEFAULT '0',
  `return_reason` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `return_reason_note` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `shop_currency` varchar(3) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `customer_currency` varchar(3) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `gross_sales_shop_amount` int NOT NULL,
  `gross_sales_customer_amount` int NOT NULL,
  `discounts_shop_amount` int NOT NULL,
  `discounts_customer_amount` int NOT NULL,
  `tax_customer_amount` int NOT NULL,
  `tax_shop_amount` int NOT NULL,
  `return_line_item_data` json NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `is_tbyb` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `orders_returns_line_items_source_id_unique` (`source_id`),
  KEY `orders_returns_line_items_order_return_id_index` (`order_return_id`),
  KEY `orders_returns_line_items_source_return_id_index` (`source_return_id`),
  KEY `orders_returns_line_items_line_item_id_index` (`line_item_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `orders_tbyb_net_sales`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `orders_tbyb_net_sales` (
  `id` char(36) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `store_id` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `date_start` date NOT NULL,
  `date_end` date NOT NULL,
  `time_range_start` timestamp NOT NULL,
  `time_range_end` timestamp NOT NULL,
  `currency` varchar(3) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `tbyb_gross_sales` bigint NOT NULL DEFAULT '0',
  `tbyb_discounts` bigint NOT NULL DEFAULT '0',
  `tbyb_refunded_gross_sales` bigint NOT NULL DEFAULT '0',
  `tbyb_refunded_discounts` bigint NOT NULL DEFAULT '0',
  `tbyb_net_sales` bigint NOT NULL DEFAULT '0',
  `is_first_of_billing_period` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `orders_tbyb_net_sales_store_id_index` (`store_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `orders_transactions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `orders_transactions` (
  `id` char(36) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `source_id` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `order_id` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `source_order_id` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `order_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `store_id` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `kind` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `gateway` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `payment_id` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `parent_transaction_id` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `parent_transaction_source_id` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `customer_currency` varchar(3) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `shop_currency` varchar(3) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `customer_amount` int NOT NULL,
  `shop_amount` int NOT NULL,
  `unsettled_customer_amount` int NOT NULL,
  `unsettled_shop_amount` int NOT NULL,
  `status` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `test` tinyint(1) NOT NULL DEFAULT '0',
  `error_code` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `message` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `transaction_source_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_id` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `processed_at` timestamp NULL DEFAULT NULL,
  `authorization_expires_at` timestamp NULL DEFAULT NULL,
  `transaction_data` json NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `orders_transactions_source_id_unique` (`source_id`),
  KEY `orders_transactions_order_id_index` (`order_id`),
  KEY `orders_transactions_source_order_id_index` (`source_order_id`),
  KEY `orders_transactions_store_id_index` (`store_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `orders_trial_group`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `orders_trial_group` (
  `id` char(36) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `order_id` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `orders_trial_group_order_id_index` (`order_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `payments_transactions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `payments_transactions` (
  `id` char(36) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `source_id` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `transaction_source_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `order_id` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `store_id` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `source_order_id` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `authorization_expires_at` timestamp NULL DEFAULT NULL,
  `shop_amount` int NOT NULL,
  `shop_currency` varchar(3) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `customer_amount` int NOT NULL,
  `customer_currency` varchar(3) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `kind` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `captured_transaction_id` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `captured_transaction_source_id` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `parent_transaction_id` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `parent_transaction_source_id` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `payments_transactions_source_id_unique` (`source_id`),
  KEY `payments_transactions_order_id_index` (`order_id`),
  KEY `payments_transactions_store_id_index` (`store_id`),
  KEY `payments_transactions_source_order_id_index` (`source_order_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `product_variants`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `product_variants` (
  `id` char(36) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `product_id` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `source` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'shopify',
  `source_id` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `source_product_id` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `title` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `barcode` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `sku` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `requires_shipping` tinyint(1) NOT NULL DEFAULT '1',
  `price` int NOT NULL,
  `image_url` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `unit_price` int DEFAULT NULL,
  `unit_price_measurement` json DEFAULT NULL,
  `weight` double(8,2) DEFAULT NULL,
  `weight_unit` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `data` json NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `product_variants_source_id_unique` (`source_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `products`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `products` (
  `id` char(36) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `source` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'shopify',
  `source_id` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `source_handle` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `source_online_store_url` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `title` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `type` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `image_url` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `is_gift_card` tinyint(1) NOT NULL,
  `requires_selling_plan` tinyint(1) NOT NULL,
  `tags` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `vendor` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `max_variant_price` int DEFAULT NULL,
  `min_variant_price` int NOT NULL DEFAULT '0',
  `data` json NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `products_source_id_unique` (`source_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `programs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `programs` (
  `id` char(36) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Try Before You Buy',
  `store_id` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `shopify_selling_plan_group_id` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `shopify_selling_plan_id` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `try_period_days` int NOT NULL DEFAULT '7',
  `deposit_type` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'percentage' COMMENT 'Supports fixed or percentage',
  `deposit_value` int NOT NULL DEFAULT '0' COMMENT 'Value in cents if deposit_type = fixed',
  `currency` varchar(3) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'USD' COMMENT 'ISO 4127',
  `min_tbyb_items` int NOT NULL DEFAULT '1',
  `max_tbyb_items` int DEFAULT NULL COMMENT 'NULL means unlimited',
  `product_ids` json DEFAULT NULL COMMENT 'Array of string IDs',
  `product_variant_ids` json DEFAULT NULL COMMENT 'Array of string IDs',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `drop_off_days` int NOT NULL DEFAULT '5' COMMENT 'Number of extra days to allow a return to be processed after a customer has dropped off their package.',
  PRIMARY KEY (`id`),
  UNIQUE KEY `programs_shopify_selling_plan_id_unique` (`shopify_selling_plan_id`),
  KEY `programs_store_id_index` (`store_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `shopify_jobs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `shopify_jobs` (
  `id` char(36) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `store_id` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `shopify_job_id` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `query` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `type` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'query',
  `domain` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `topic` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `export_file_url` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `export_partial_file_url` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `error_code` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `shopify_jobs_store_id_index` (`store_id`),
  KEY `shopify_jobs_shopify_job_id_index` (`shopify_job_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `shopify_mandatory_webhooks`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `shopify_mandatory_webhooks` (
  `id` char(36) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `store_id` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `topic` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `shopify_shop_id` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `shopify_domain` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `data` json NOT NULL,
  `status` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `shopify_mandatory_webhooks_store_id_index` (`store_id`),
  KEY `shopify_mandatory_webhooks_shopify_domain_index` (`shopify_domain`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `shopify_webhook_data`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `shopify_webhook_data` (
  `id` char(36) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `store_id` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `topic` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `data` json NOT NULL,
  `attributes` json NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `shopify_webhook_data_store_id_index` (`store_id`),
  KEY `shopify_webhook_data_topic_index` (`topic`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `stackkit_cloud_tasks`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `stackkit_cloud_tasks` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `queue` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `task_uuid` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `metadata` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `stackkit_cloud_tasks_task_uuid_index` (`task_uuid`),
  KEY `stackkit_cloud_tasks_queue_index` (`queue`),
  KEY `stackkit_cloud_tasks_status_index` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `store_settings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `store_settings` (
  `id` char(36) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `is_secure` tinyint(1) NOT NULL DEFAULT '0',
  `store_id` char(36) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `store_settings_store_id_name_unique` (`store_id`,`name`),
  KEY `store_settings_store_id_index` (`store_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `stores`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `stores` (
  `id` char(36) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `domain` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `phone` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `owner_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `currency` varchar(3) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'ISO 4127',
  `primary_locale` varchar(2) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'ISO 639 two-letter language code',
  `address1` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `address2` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `city` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `state` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `state_code` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `country` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `country_code` varchar(3) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'ISO 3166-1 alpha-2',
  `country_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `iana_timezone` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ecommerce_platform` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'shopify',
  `ecommerce_platform_store_id` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `ecommerce_platform_plan` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ecommerce_platform_plan_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `source` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `stores_domain_deleted_at_unique` (`domain`,`deleted_at`),
  KEY `stores_name_index` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `trialables`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `trialables` (
  `id` char(36) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `source_key` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'shopify',
  `source_id` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `group_key` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'init',
  `trial_duration` smallint unsigned NOT NULL DEFAULT '7',
  `expires_at` timestamp NULL DEFAULT NULL,
  `title` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `subtitle` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `image` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (1,'2019_08_19_000000_create_failed_jobs_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (2,'2023_10_13_202847_create_stores_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (3,'2023_10_31_181839_create_store_settings_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (4,'2023_11_01_212015_create_programs_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (5,'2023_11_06_200026_trialbles',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (6,'2023_11_16_160549_add_product_ids_to_programs_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (7,'2023_11_28_012517_products_and_variants',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (8,'2023_11_28_054735_shopify_jobs_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (9,'2023_12_08_163812_orders_tables',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (10,'2023_12_11_184919_create_billings_tables',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (11,'2023_12_12_054735_add_domain_shopify_jobs_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (12,'2023_12_12_145942_remove_activate_timestamps_from_line_items',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (13,'2023_12_12_160122_add_subscription_line_item_type',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (14,'2023_12_12_192544_create_shopify_webhook_data',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (15,'2023_12_15_160122_add_recurring_sub_line_item_columns',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (16,'2023_12_18_160122_update_subscription_status_column',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (17,'2023_12_28_181839_add_shopify_shop_details_columns_to_stores_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (18,'2023_12_29_181839_add_shopify_shop_details_columns_to_stores_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (19,'2024_01_01_160100_add_currency_to_programs_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (20,'2024_01_02_083014_trial_groups',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (21,'2024_01_06_144535_add_shopify_mandatory_webhooks_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (22,'2024_01_08_164608_trial_duration',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (23,'2024_01_16_212635_add_topic_remove_callback_url_shopify_jobs_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (24,'2024_01_17_144822_nullable_columns_stores_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (25,'2024_01_18_192209_update_deposit_default_to_percentage',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (26,'2024_01_29_014542_create_refunds_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (27,'2024_02_05_204915_create_billings_charges_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (28,'2024_02_06_175129_order_updates_for_billing',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (29,'2024_02_06_193107_add_sum_columns_to_orders_refunds',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (30,'2024_02_06_200620_create_orders_refund_line_items_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (31,'2024_02_06_201412_create_orders_tbyb_net_sales_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (32,'2024_02_08_110512_create_billings_tbyb_net_sales_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (33,'2024_02_08_174026_add_store_id_to_orders_refunds_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (34,'2024_02_08_180732_remove_legacy_columns_orders_refunds',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (35,'2024_02_08_210934_remove_legacy_columns_orders_refund_line_items',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (36,'2024_02_10_232108_add_currency_valid_to_billings_app_usage_configs',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (37,'2024_02_12_160001_change_sale_columns_to_big_int',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (38,'2024_02_12_171138_change_balance_to_big_integer_billings_charges',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (39,'2024_02_15_032943_add_details_columns_to_billings_charges',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (40,'2024_02_20_161303_create_returns_and_returns_line_items',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (41,'2024_02_21_161243_add_billing_period_end_date_to_billings_subscriptions',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (42,'2024_02_23_192634_add_taxes_to_line_items',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (43,'2024_02_23_213142_create_payments_transactions_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (44,'2024_02_25_193801_new_orders_transactions_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (45,'2024_02_28_212202_add_completed_at_to_order',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (46,'2024_02_29_142901_selling_plan_fields_in_line_items_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (47,'2024_03_01_004553_add_original_outstanding_amount',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (48,'2024_03_05_080630_add_captured_transaction_id_to_payments_transactions',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (49,'2024_03_05_155522_product_variant_fields_in_line_items_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (50,'2024_03_06_220556_billings_tables_indices',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (51,'2024_03_06_220556_orders_tables_indices',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (52,'2024_03_06_220556_payments_tables_indices',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (53,'2024_03_06_220556_programs_tables_indices',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (54,'2024_03_08_023040_add_tax_columns_to_orders_refund_line_items',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (55,'2024_03_08_023255_add_total_refund_amount_to_orders_refund_line_items',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (56,'2024_03_08_151823_new_columns_outstanding_order_total_orders_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (57,'2024_03_08_202008_rename_order_level_refunds_in_orders_refunds',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (58,'2024_03_08_202130_rename_refund_id_in_orders_refund_line_items',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (59,'2024_03_08_202259_add_is_tbyb_to_orders_returns_line_items',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (60,'2024_03_08_231831_add_tbyb_upfront_totals_to_orders_refunds',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (61,'2024_03_11_041650_add_refund_id_to_orders_refund_line_items',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (62,'2024_03_11_129384_add_drop_off_days_to_programs',2);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (63,'2024_03_11_204405_add_deposit_amount_to_orders_refund_line_items',2);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (64,'2024_03_11_204603_add_deposit_total_amount_to_orders_refunds',2);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (65,'2024_03_12_121547_add_decision_status_order_line_items',3);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (66,'2024_03_14_183726_add_refunded_amount_to_orders_refunds_table',4);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (67,'2024_03_21_153236_transactions_add_source_id_columns',5);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (68,'2024_03_27_080752_add_assumed_delivery_merchant_email_sent_at_to_orders_table',6);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (72,'2024_04_03_120752_make_receipt_json_nullable_transactions_table',7);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (73,'2024_04_03_141028_drop_receipt_json_from_transactions_table',7);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (74,'2024_04_05_185527_add_status_updated_by_to_orders_line_items_table',8);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (75,'2024_04_09_195024_add_soft_deletes_to_store_settings',9);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (79,'2021_10_16_171140_create_stackkit_cloud_tasks_table',10);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (80,'2024_04_25_154346_add_trial_expires_at_to_orders_table',10);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (81,'2024_05_08_154024_domain_unique_with_deleted_at_index',11);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (82,'2024_05_13_145233_add_current_period_start_to_billings_subscriptions_table',12);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (83,'2024_05_13_154241_add_authorization_expires_at_to_payment_transactions_table',13);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (84,'2024_05_13_154331_add_transaction_source_name_to_payment_transactions_table',13);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (85,'2024_05_53_174445_add_is_first_of_billing_cycle_to_billings_charges_table',14);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (86,'2024_05_17_074719_update_source_transaction_name_to_nullable_at_payments_transactions_table',15);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (87,'2024_05_20_140842_add_is_first_of_billing_period_to_billings_tbyb_net_sales_table',16);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (88,'2024_05_20_140842_add_is_first_of_billing_period_to_orders_tbyb_net_sales_table',16);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (89,'2024_05_21_002036_drop_auth_expires_at_and_source_transaction_name_of_payments_transactions_table',17);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (90,'2024_05_21_163613_add_time_ranges_to_billings_charges_table',18);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (91,'2024_05_28_153538_add_original_quantity_to_line_items_table',18);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (92,'2024_06_06_180134_add_refund_data_to_refunds_table',18);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (93,'2024_06_12_161514_add_order_name_to_orders_transactions',19);
