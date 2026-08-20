# ERP Power BI API Reference Documentation

This document provides complete documentation for all ERP Reporting API Endpoints.

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

## 🌟 1. Comprehensive Sales & Purchases APIs (Fully Resolved Relationships)

### `GET /api/powerbi/sales` (or `/api/powerbi/all-sales`)
Fetches all sales contracts and line items with **all foreign key IDs resolved into readable names** (Country name, Customer company, Meta company, Customer name, Product name, Payment type, Payment terms, Currency).

* **Method**: `GET`
* **URL**: `https://metaerpapi.aideepseek.uk/api/powerbi/sales`
* **Optional Query Parameters**:
  - `contact_id`: Filter by specific customer/contact ID (e.g. `?contact_id=14`)
* **Data Schema Returned**:
  ```json
  [
    {
      "contract_id": 105,
      "order_code": "ORD-2026-001",
      "sales_invoice_number": "INV-2026-99",
      "contract_date": "2026-08-15 10:30:00",
      "customer_name": "Acme Global Trading",
      "customer_code": "CUST-009",
      "country": "United States",
      "customer_company": "Acme Group LLC",
      "meta_company": "META ERP Solutions Ltd",
      "product_name": "Refined Palm Oil Grade A",
      "quantity": "500.00",
      "premium": "15.00",
      "rate": "1250.00",
      "total_price": "625000.00",
      "currency": "USD",
      "payment_type": "Letter of Credit (LC)",
      "payment_terms": "30 Days Net",
      "start_date": "2026-09-01",
      "end_date": "2026-09-30",
      "customer_registration": "REG-US-987654",
      "customer_vat": "VAT-US-112233",
      "customer_website": "https://acmeglobal.example.com"
    }
  ]
  ```

---

### `GET /api/powerbi/purchases` (or `/api/powerbi/all-purchases`)
Fetches all purchase contracts and line items with **all foreign key IDs resolved into readable names** (Country name, Supplier company, Meta company, Supplier name, Product name, Payment type, Payment terms, Currency).

* **Method**: `GET`
* **URL**: `https://metaerpapi.aideepseek.uk/api/powerbi/purchases`
* **Optional Query Parameters**:
  - `contact_id`: Filter by specific supplier/contact ID (e.g. `?contact_id=14`)
* **Data Schema Returned**:
  ```json
  [
    {
      "contract_id": 102,
      "order_code": "ORD-PUR-2026-005",
      "sales_invoice_number": "PINV-2026-10",
      "contract_date": "2026-08-14 09:15:00",
      "supplier_name": "Agro Export Commodities",
      "supplier_code": "SUPP-004",
      "country": "Brazil",
      "supplier_company": "Agro Brazil S.A.",
      "meta_company": "META ERP Solutions Ltd",
      "product_name": "Crude Palm Oil",
      "quantity": "1000.00",
      "premium": "10.00",
      "rate": "1100.00",
      "total_price": "1100000.00",
      "currency": "USD",
      "payment_type": "Telegraphic Transfer (TT)",
      "payment_terms": "Cash on Delivery (COD)",
      "start_date": "2026-09-10",
      "end_date": "2026-10-10",
      "supplier_registration": "BR-CNPJ-998877",
      "supplier_vat": "VAT-BR-556677",
      "supplier_website": "https://agroexport.example.com"
    }
  ]
  ```

---

## 2. Authentication API

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

## 3. Master Data APIs

### `GET /api/powerbi/contacts`
Fetches all ERP contacts/clients list.

* **Method**: `GET`
* **URL**: `https://metaerpapi.aideepseek.uk/api/powerbi/contacts`
* **Data Returned**: List of contacts (`id`, `code_meta`, `name`, `company_id`, `country_id`, `registration`, `vat`, `currency`, `website`, etc.).

---

### `GET /api/powerbi/countries`
Fetches all country records.

* **Method**: `GET`
* **URL**: `https://metaerpapi.aideepseek.uk/api/powerbi/countries`

---

### `GET /api/powerbi/products`
Fetches all product master records.

* **Method**: `GET`
* **URL**: `https://metaerpapi.aideepseek.uk/api/powerbi/products`

---

### `GET /api/powerbi/companies`
Fetches all company records.

* **Method**: `GET`
* **URL**: `https://metaerpapi.aideepseek.uk/api/powerbi/companies`

---

### `GET /api/powerbi/contracts`
Fetches all raw contracts list.

* **Method**: `GET`
* **URL**: `https://metaerpapi.aideepseek.uk/api/powerbi/contracts`

---

## 4. Single Contact Reporting APIs

### `GET /api/powerbi/contact/{id}`
Fetches detailed profile information for a single contact by ID.
* **URL Example**: `https://metaerpapi.aideepseek.uk/api/powerbi/contact/14`

### `GET /api/powerbi/contact/{id}/purchases`
Fetches purchases for a single contact.
* **URL Example**: `https://metaerpapi.aideepseek.uk/api/powerbi/contact/14/purchases`

### `GET /api/powerbi/contact/{id}/sales`
Fetches sales for a single contact.
* **URL Example**: `https://metaerpapi.aideepseek.uk/api/powerbi/contact/14/sales`

### `GET /api/powerbi/contact/{id}/buying-payment-terms`
Fetches buying payment terms metrics for a single contact.
* **URL Example**: `https://metaerpapi.aideepseek.uk/api/powerbi/contact/14/buying-payment-terms`

### `GET /api/powerbi/contact/{id}/selling-payment-terms`
Fetches selling payment terms metrics for a single contact.
* **URL Example**: `https://metaerpapi.aideepseek.uk/api/powerbi/contact/14/selling-payment-terms`

### `GET /api/powerbi/contact/{id}/product-buying-country`
Fetches product buying by country for a single contact.
* **URL Example**: `https://metaerpapi.aideepseek.uk/api/powerbi/contact/14/product-buying-country`

### `GET /api/powerbi/contact/{id}/product-selling-country`
Fetches product selling by country for a single contact.
* **URL Example**: `https://metaerpapi.aideepseek.uk/api/powerbi/contact/14/product-selling-country`

### `GET /api/powerbi/contact/{id}/credit-debit-notes`
Fetches credit/debit notes for a single contact.
* **URL Example**: `https://metaerpapi.aideepseek.uk/api/powerbi/contact/14/credit-debit-notes`

### `GET /api/powerbi/contact/{id}/dashboard-summary`
Fetches financial KPI metrics for a single contact.
* **URL Example**: `https://metaerpapi.aideepseek.uk/api/powerbi/contact/14/dashboard-summary`

### `GET /api/powerbi/powerbi/contact/{id}/dashboard`
Fetches combined dashboard dataset for a single contact.
* **URL Example**: `https://metaerpapi.aideepseek.uk/api/powerbi/powerbi/contact/14/dashboard`
