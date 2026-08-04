# Built-in OAuth 2.0 API Authentication Guide

This document outlines the **Self-Hosted OAuth 2.0 (Client Credentials Grant)** implementation for securing the **ERP PowerBI API**.

---

## 1. Overview & Security Architecture

To prevent scrapers from extracting confidential API data, the API is secured using standard **OAuth 2.0 Bearer Access Tokens**:

```
[ Client Application / Power BI / Postman ]
                     │
                     │ 1. POST /api/oauth/token (grant_type=client_credentials, client_id, client_secret)
                     ▼
             [ Laravel API ]
      (Validates Client Credentials)
                     │
                     │ 2. Returns 1-Hour Bearer JWT Access Token
                     ▼
[ API Request: Authorization: Bearer <JWT_TOKEN> ]
                     │
                     ▼
            [ OAuth2TokenMiddleware ]
   ├─ Verifies JWT Cryptographic Signature
   ├─ Checks Token Expiration (60 minutes)
   └─ Decodes Client Identity
                     │ (Valid)
                     ▼
           [ PowerBiController ]
```

---

## 2. Configuration (`.env`)

Default credentials have been generated in your `.env` file:

```env
# Built-in OAuth 2.0 Client Credentials Configuration
OAUTH_CLIENT_ID=powerbi_client_2026
OAUTH_CLIENT_SECRET=sec_erp_api_9823472398472938
OAUTH_JWT_SECRET=super_secret_jwt_key_for_erp_api_2026
OAUTH_TOKEN_TTL=3600
```

---

## 3. How to Obtain an Access Token

### HTTP Request:
* **Endpoint**: `POST /api/oauth/token`
* **Content-Type**: `application/x-www-form-urlencoded` or `application/json`
* **Body**:
  ```json
  {
    "grant_type": "client_credentials",
    "client_id": "powerbi_client_2026",
    "client_secret": "sec_erp_api_9823472398472938"
  }
  ```

### Response:
```json
{
  "access_token": "eyJhbGciOiJIUzI1Ni...",
  "token_type": "Bearer",
  "expires_in": 3600
}
```

---

## 4. Making Authenticated API Calls

Include the token in the `Authorization` header for all `/api/powerbi/*` requests:

```bash
curl -H "Authorization: Bearer <access_token>" http://localhost:8000/api/powerbi/contacts
```

---

## 5. Using the Postman Collection

1. Open Postman > Click **Import** > Select [`ERP_OAuth2_API.postman_collection.json`](file:///d:/P2/erpapi/ERP_OAuth2_API.postman_collection.json).
2. Run Request 1: **`Get OAuth 2.0 Bearer Token`**.
   - This automatically fetches a token and saves it into the collection variable.
3. Run any API request under **`2. PowerBI Secure Endpoints`**.

---

## 6. Power BI Integration Script

In Power BI Desktop, open **Advanced Editor** for your query and use this script:

```powerquery
let
    // 1. Fetch OAuth 2.0 Token
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

    // 2. Fetch Secured API Data
    ApiResponse = Json.Document(Web.Contents("http://localhost:8000/api/powerbi/contacts", [
        Headers = [
            #"Authorization" = "Bearer " & AccessToken,
            #"Accept" = "application/json"
        ]
    ]))
in
    ApiResponse
```

---

## 7. Managing Clients via Command Line

To issue new `client_id` & `client_secret` credentials for different clients:

```bash
php artisan oauth:create-client "Power BI Production"
```
