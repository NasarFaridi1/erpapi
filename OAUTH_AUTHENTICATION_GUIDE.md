# Static OAuth 2.0 API Authentication Guide

This document provides a complete guide for testing and using the **Static OAuth 2.0 (Client Credentials Grant)** authentication for the **ERP PowerBI API**.

---

## 1. Static Credentials (`.env`)

The static credentials configured in your `.env` file:

```env
OAUTH_CLIENT_ID=powerbi_client_2026
OAUTH_CLIENT_SECRET=sec_erp_api_9823472398472938
OAUTH_JWT_SECRET=super_secret_jwt_key_for_erp_api_2026
OAUTH_TOKEN_TTL=3600
```

---

## 2. Step 1: Request Access Token

Send a `POST` request to `/api/oauth/token` to exchange your static client credentials for a 1-hour Bearer JWT Access Token.

### Request Details
* **Method**: `POST`
* **URL**: `http://localhost:8000/api/oauth/token`
* **Headers**: `Content-Type: application/x-www-form-urlencoded`
* **Body**:
  - `grant_type`: `client_credentials`
  - `client_id`: `powerbi_client_2026`
  - `client_secret`: `sec_erp_api_9823472398472938`

### Success Response (`200 OK`)
```json
{
  "access_token": "eyJhbGciOiJIUzI1Ni...",
  "token_type": "Bearer",
  "expires_in": 3600
}
```

---

## 3. Step 2: Access Secured API Endpoints

All endpoints require the `Authorization` header with the Bearer token:

```http
Authorization: Bearer <access_token>
```

### Supported Secured Endpoints (15 Total):

| # | Endpoint | Description |
|---|---|---|
| 1 | `GET /api/powerbi/contacts` | List all contacts |
| 2 | `GET /api/powerbi/contact/{id}` | Get contact details |
| 3 | `GET /api/powerbi/contact/{id}/purchases` | Get contact purchases |
| 4 | `GET /api/powerbi/contact/{id}/sales` | Get contact sales |
| 5 | `GET /api/powerbi/countries` | List countries |
| 6 | `GET /api/powerbi/products` | List products |
| 7 | `GET /api/powerbi/companies` | List companies |
| 8 | `GET /api/powerbi/contracts` | List contracts |
| 9 | `GET /api/powerbi/contact/{id}/buying-payment-terms` | Get buying payment terms |
| 10 | `GET /api/powerbi/contact/{id}/selling-payment-terms` | Get selling payment terms |
| 11 | `GET /api/powerbi/contact/{id}/product-buying-country` | Get product buying country |
| 12 | `GET /api/powerbi/contact/{id}/product-selling-country` | Get product selling country |
| 13 | `GET /api/powerbi/contact/{id}/credit-debit-notes` | Get credit/debit notes |
| 14 | `GET /api/powerbi/contact/{id}/dashboard-summary` | Get contact dashboard summary |
| 15 | `GET /api/powerbi/powerbi/contact/{id}/dashboard` | Get full contact dashboard |

---

## 4. Postman Collection Instructions

1. Open **Postman**.
2. Click **Import** > Select [`ERP_OAuth2_API.postman_collection.json`](file:///d:/P2/erpapi/ERP_OAuth2_API.postman_collection.json).
3. Run request **`1. Authentication`** > **`Get OAuth 2.0 Bearer Token`**.
   - Postman's automated test script will automatically store the token into `{{oauth_access_token}}`.
4. Open the folder **`2. PowerBI Secured Endpoints`** and click **Send** on any of the 15 requests.

---

## 5. Power BI M Query Integration

In Power BI Desktop, open **Advanced Editor** and paste:

```powerquery
let
    // 1. Fetch OAuth 2.0 Access Token
    TokenUrl = "http://localhost:8000/api/oauth/token",
    TokenBody = [
        grant_type = "client_credentials",
        client_id = "powerbi_client_2026",
        client_secret = "sec_erp_api_9823472398472938"
    ],
    TokenResponse = Json.Document(Web.Contents(TokenUrl, [
        Content = Text.ToBinary(Uri.BuildQueryString(TokenBody)),
        Headers = [#"Content-Type"="application/x-www-form-urlencoded"]
    ])),
    AccessToken = TokenResponse[access_token],

    // 2. Fetch Data from Secured Endpoint
    ApiResponse = Json.Document(Web.Contents("http://localhost:8000/api/powerbi/contacts", [
        Headers = [
            #"Authorization" = "Bearer " & AccessToken,
            #"Accept" = "application/json"
        ]
    ]))
in
    ApiResponse
```
