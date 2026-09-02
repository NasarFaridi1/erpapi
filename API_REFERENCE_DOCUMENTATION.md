# ERP Power BI API Reference Documentation

This document provides complete documentation for all ERP Reporting API Endpoints. **All endpoints return real human-readable data (names, descriptions, codes) instead of raw numeric IDs.**

---

## 🔑 Common Authentication & Headers

Every reporting endpoint under `/api/powerbi/*` requires **Authentication**.

### Option A: OAuth 2.0 Bearer Token (Recommended)
```http
Authorization: Bearer <access_token>
Accept: application/json
```

### Option B: Static API Key Header
```http
X-API-KEY: sec_erp_api_9823472398472938
Accept: application/json
```

---

## 🌟 1. Comprehensive Global Reporting APIs (Fully Resolved Relationships)

### `GET /api/powerbi/sales` (or `/api/powerbi/all-sales`)
Fetches all sales contracts and line items with **all foreign key IDs resolved into readable names**.

* **Method**: `GET`
* **URL**: `https://metaerpapi.aideepseek.uk/api/powerbi/sales`
* **Optional Query Parameters**: `?contact_id=14`
* **Resolved Relationship Fields Returned**:
  - `contract_id`: Contract unique ID
  - `order_code`: Order Code (e.g. `MECOM26-248`)
  - `sales_invoice_number`: Invoice number
  - `customer_name`: Contact name (from `contacts.name` via `contact_id`)
  - `customer_code`: Code meta (e.g. `SA01788`)
  - `country`: Country name (from `countries.name` via `country_id`, e.g. `Saudi Arabia`)
  - `customer_company`: Company name of customer (from `companies.name` via `company_id`)
  - `meta_company`: Internal operating company (from `companies.name` via `meta_company_id`)
  - `product_name`: Name of product (from `products.name` via `product_id`)
  - `quantity`: Quantity
  - `premium`: Premium
  - `rate`: Rate
  - `total_price`: Total amount
  - `currency`: Currency code (defaults to `USD`)
  - `payment_type`: Payment type description (from `payment_type.description`, e.g. `Upfront`, `LC`)
  - `payment_terms`: Payment terms description (from `payment_terms_type.description`)
  - `start_date` / `end_date`: Delivery dates
  - `customer_registration`: Commercial registration number
  - `customer_vat`: VAT number
  - `customer_website`: Website

---

### `GET /api/powerbi/purchases` (or `/api/powerbi/all-purchases`)
Fetches all purchase contracts and line items with **all foreign key IDs resolved into readable names**.

* **Method**: `GET`
* **URL**: `https://metaerpapi.aideepseek.uk/api/powerbi/purchases`
* **Optional Query Parameters**: `?contact_id=14`
* **Resolved Relationship Fields Returned**:
  - `contract_id`: Contract unique ID
  - `order_code`: Order Code
  - `sales_invoice_number`: Invoice number
  - `supplier_name`: Supplier name (from `contacts.name` via `contact_id`)
  - `supplier_code`: Code meta
  - `country`: Country name (from `countries.name` via `country_id`)
  - `supplier_company`: Company name of supplier (from `companies.name` via `company_id`)
  - `meta_company`: Internal operating company (from `companies.name` via `meta_company_id`)
  - `product_name`: Name of product (from `products.name` via `product_id`)
  - `quantity`, `premium`, `rate`, `total_price`, `currency`
  - `payment_type`: Payment type description
  - `payment_terms`: Payment terms description
  - `start_date` / `end_date`: Delivery dates
  - `supplier_registration`, `supplier_vat`, `supplier_website`

---

### `GET /api/powerbi/contracts`
Fetches all contracts with **Supplier, Customer, Countries, Operating Companies, and Payment Terms resolved**.

* **Method**: `GET`
* **URL**: `https://metaerpapi.aideepseek.uk/api/powerbi/contracts`
* **Resolved Relationship Fields Returned**:
  - `contract_id`, `order_code`, `sales_invoice_number`
  - `supplier_name`, `supplier_code`, `supplier_country`, `supplier_company`
  - `purchase_meta_company`, `purchase_payment_type`, `purchase_payment_terms`
  - `customer_name`, `customer_code`, `customer_country`, `customer_company`
  - `sales_meta_company`, `sales_payment_type`, `sales_payment_terms`

---

### `GET /api/powerbi/contacts`
Fetches all contacts with **Country Name and Company Name resolved instead of raw IDs**.

* **Method**: `GET`
* **URL**: `https://metaerpapi.aideepseek.uk/api/powerbi/contacts`
* **Resolved Relationship Fields Returned**:
  - `contact_id`, `contact_code`, `contact_name`
  - `country`: Country name (from `countries.name` instead of raw `country_id`)
  - `company_name`: Company name (from `companies.name` instead of raw `company_id`)
  - `registration`, `vat`, `currency`, `website`, `active`, `initials`, `eori_number`

---

### `GET /api/powerbi/credit-debit-notes` (or `/api/powerbi/all-credit-debit-notes`)
Fetches all credit and debit notes across all contacts with **Contact name, Country, Company, Product, and Currency resolved**.

* **Method**: `GET`
* **URL**: `https://metaerpapi.aideepseek.uk/api/powerbi/credit-debit-notes`
* **Optional Query Parameters**: `?contact_id=14`
* **Resolved Relationship Fields Returned**:
  - `note_id`, `note_number`, `note_type`, `note_date`, `status`, `order_code`
  - `contact_name`: Name of contact
  - `contact_code`: Code meta
  - `country`: Country name
  - `company_name`: Company name
  - `product_name`: Product name
  - `quantity`, `rate`, `amount`, `currency`

---

## 👤 2. Single Contact Reporting APIs (Filtered with Full Relationships)

* `GET /api/powerbi/contact/{id}` — Single contact details with `country` and `company_name` resolved.
* `GET /api/powerbi/contact/{id}/purchases` — Purchases for contact with resolved `country`, `supplier_company`, `product_name`, `payment_type`, and `payment_terms`.
* `GET /api/powerbi/contact/{id}/sales` — Sales for contact with resolved `country`, `customer_company`, `product_name`, `payment_type`, and `payment_terms`.
* `GET /api/powerbi/contact/{id}/buying-payment-terms` — Buying contracts aggregated by `payment_type` and `payment_terms` descriptions.
* `GET /api/powerbi/contact/{id}/selling-payment-terms` — Selling contracts aggregated by `payment_type` and `payment_terms` descriptions.
* `GET /api/powerbi/contact/{id}/product-buying-country` — Aggregated by `country` name and `product_name`.
* `GET /api/powerbi/contact/{id}/product-selling-country` — Aggregated by `country` name, `meta_company` name, and `product_name`.
* `GET /api/powerbi/contact/{id}/credit-debit-notes` — Notes for contact with resolved `country`, `company_name`, `product_name`, and `currency`.
* `GET /api/powerbi/contact/{id}/dashboard-summary` — Aggregated financial KPIs with `country` and `top_product` name.

---

## 3. Master Lookups

* `GET /api/powerbi/countries` — All countries (`country_id`, `country_name`, `code`, `currency`).
* `GET /api/powerbi/products` — All products (`product_id`, `product_name`, `product_code`).
* `GET /api/powerbi/companies` — All companies (`company_id`, `company_name`).
