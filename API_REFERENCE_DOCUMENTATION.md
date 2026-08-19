# ERP Power BI API Reference Documentation

This document provides complete documentation for all **16 API Endpoints** in the ERP System.

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

## 1. Authentication API

### `POST /api/oauth/token`
Generates a signed, 1-hour OAuth 2.0 Bearer JWT Access Token.

* **Method**: `POST` (or `GET`)
* **URL**: `https://metaerpapi.aideepseek.uk/api/oauth/token`
* **Headers**: `Content-Type: application/x-www-form-urlencoded`
* **Parameters to Pass (Body)**:
  - `grant_type`: `client_credentials`
  - `client_id`: `powerbi_client_2026`
  - `client_secret`: `sec_erp_api_9823472398472938`
* **Data Returned**:
  ```json
  {
    "access_token": "eyJhbGciOiJIUzI1Ni...",
    "token_type": "Bearer",
    "expires_in": 3600
  }
  ```

---

## 2. Master Data APIs

### `GET /api/powerbi/contacts`
Fetches all ERP contacts/clients list.

* **Method**: `GET`
* **URL**: `https://metaerpapi.aideepseek.uk/api/powerbi/contacts`
* **Parameters to Pass**: None
* **Data Returned**: List of contacts with fields:
  `id`, `code_meta`, `name`, `company_id`, `country_id`, `registration`, `vat`, `currency`, `website`, `active`, `initials`, `eori_number`, `language_id`.

---

### `GET /api/powerbi/countries`
Fetches all country records.

* **Method**: `GET`
* **URL**: `https://metaerpapi.aideepseek.uk/api/powerbi/countries`
* **Parameters to Pass**: None
* **Data Returned**: List of countries: `id`, `name`, `code`, `currency`, etc.

---

### `GET /api/powerbi/products`
Fetches all product master records.

* **Method**: `GET`
* **URL**: `https://metaerpapi.aideepseek.uk/api/powerbi/products`
* **Parameters to Pass**: None
* **Data Returned**: List of products: `id`, `name`, `code`, `unit_price`, `category_id`, etc.

---

### `GET /api/powerbi/companies`
Fetches all company records.

* **Method**: `GET`
* **URL**: `https://metaerpapi.aideepseek.uk/api/powerbi/companies`
* **Parameters to Pass**: None
* **Data Returned**: List of companies: `id`, `name`, `registration_number`, `tax_id`, etc.

---

### `GET /api/powerbi/contracts`
Fetches all contracts list.

* **Method**: `GET`
* **URL**: `https://metaerpapi.aideepseek.uk/api/powerbi/contracts`
* **Parameters to Pass**: None
* **Data Returned**: List of contracts: `id`, `order_code`, `sales_invoice_number`, `purchase_id`, `sale_id`, `created_at`, etc.

---

## 3. Contact-Specific Detail APIs

### `GET /api/powerbi/contact/{id}`
Fetches detailed profile information for a single contact by ID.

* **Method**: `GET`
* **URL Example**: `https://metaerpapi.aideepseek.uk/api/powerbi/contact/14`
* **Parameters to Pass**: `{id}` in URL path (e.g. `14`).
* **Data Returned**: Single contact record object:
  `contact_id`, `contact_name`, `code_meta`, `country`, `company_name`, `registration`, `vat`, `currency`, `website`.

---

### `GET /api/powerbi/contact/{id}/purchases`
Fetches all purchase contracts and line items for a specific contact.

* **Method**: `GET`
* **URL Example**: `https://metaerpapi.aideepseek.uk/api/powerbi/contact/14/purchases`
* **Parameters to Pass**: `{id}` in URL path.
* **Data Returned**: List of purchase transactions:
  `id`, `order_code`, `sales_invoice_number`, `contact_id`, `contact_name`, `meta_company`, `product`, `quantity`, `premium`, `rate`, `total_price`, `payment_type`, `payment_terms`, `start_date`, `end_date`.

---

### `GET /api/powerbi/contact/{id}/sales`
Fetches all sales contracts and line items for a specific contact.

* **Method**: `GET`
* **URL Example**: `https://metaerpapi.aideepseek.uk/api/powerbi/contact/14/sales`
* **Parameters to Pass**: `{id}` in URL path.
* **Data Returned**: List of sales transactions:
  `id`, `order_code`, `sales_invoice_number`, `contact_id`, `contact_name`, `meta_company`, `product`, `quantity`, `premium`, `rate`, `total_price`, `payment_type`, `payment_terms`, `start_date`, `end_date`.

---

### `GET /api/powerbi/contact/{id}/buying-payment-terms`
Fetches aggregated buying contract metrics grouped by payment terms for a contact.

* **Method**: `GET`
* **URL Example**: `https://metaerpapi.aideepseek.uk/api/powerbi/contact/14/buying-payment-terms`
* **Parameters to Pass**: `{id}` in URL path.
* **Data Returned**: Grouped payment terms list:
  `payment_type`, `payment_terms`, `total_contracts`, `total_quantity`, `total_value`.

---

### `GET /api/powerbi/contact/{id}/selling-payment-terms`
Fetches aggregated selling contract metrics grouped by payment terms for a contact.

* **Method**: `GET`
* **URL Example**: `https://metaerpapi.aideepseek.uk/api/powerbi/contact/14/selling-payment-terms`
* **Parameters to Pass**: `{id}` in URL path.
* **Data Returned**: Grouped payment terms list:
  `payment_type`, `payment_terms`, `total_contracts`, `total_quantity`, `total_value`.

---

### `GET /api/powerbi/contact/{id}/product-buying-country`
Fetches product buying breakdown by destination country for a contact.

* **Method**: `GET`
* **URL Example**: `https://metaerpapi.aideepseek.uk/api/powerbi/contact/14/product-buying-country`
* **Parameters to Pass**: `{id}` in URL path.
* **Data Returned**: Product buying breakdown list:
  `country`, `product_id`, `product`, `total_contracts`, `total_quantity`, `total_value`.

---

### `GET /api/powerbi/contact/{id}/product-selling-country`
Fetches product selling breakdown by country for a contact.

* **Method**: `GET`
* **URL Example**: `https://metaerpapi.aideepseek.uk/api/powerbi/contact/14/product-selling-country`
* **Parameters to Pass**: `{id}` in URL path.
* **Data Returned**: Product selling breakdown list:
  `country`, `meta_company`, `product_id`, `product`, `total_contracts`, `total_quantity`, `total_value`.

---

### `GET /api/powerbi/contact/{id}/credit-debit-notes`
Fetches all credit and debit notes associated with a contact.

* **Method**: `GET`
* **URL Example**: `https://metaerpapi.aideepseek.uk/api/powerbi/contact/14/credit-debit-notes`
* **Parameters to Pass**: `{id}` in URL path.
* **Data Returned**: List of credit/debit notes:
  `id`, `note_number`, `note_type`, `note_date`, `status`, `order_code`, `contact_id`, `contact_name`, `company_name`, `product`, `quantity`, `rate`, `amount`, `currency`.

---

### `GET /api/powerbi/contact/{id}/dashboard-summary`
Fetches aggregated financial KPI metrics (total buy value, total sell value, revenue, top product, top country, credit/debit note counts) for a contact.

* **Method**: `GET`
* **URL Example**: `https://metaerpapi.aideepseek.uk/api/powerbi/contact/14/dashboard-summary`
* **Parameters to Pass**: `{id}` in URL path.
* **Data Returned**: Summary KPI object:
  - `buying`: `buying_contracts`, `buying_quantity`, `buying_value`
  - `selling`: `selling_contracts`, `selling_quantity`, `selling_value`
  - `credit_notes`: total credit notes count
  - `debit_notes`: total debit notes count
  - `revenue`: calculated revenue (`selling_value - buying_value`)
  - `top_product`: top product name
  - `top_country`: top country name

---

### `GET /api/powerbi/powerbi/contact/{id}/dashboard`
Fetches full combined dashboard data (profile + summary + purchases + sales + payment terms + notes) in a single request.

* **Method**: `GET`
* **URL Example**: `https://metaerpapi.aideepseek.uk/api/powerbi/powerbi/contact/14/dashboard`
* **Parameters to Pass**: `{id}` in URL path.
* **Data Returned**: Complete dashboard dataset:
  `contact_information`, `dashboard_summary`, `purchasing_side`, `sales_side`, `buying_payment_terms`, `selling_payment_terms`, `product_buying_country`, `product_selling_country`, `credit_debit_notes`.
