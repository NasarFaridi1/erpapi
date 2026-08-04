# Microsoft Identity Platform (Azure AD) API Authentication Guide

This document provides step-by-step instructions for setting up, configuring, and testing **Microsoft Entra ID (Azure AD)** OAuth 2.0 authentication for the **ERP PowerBI API**.

---

## 1. Prerequisites: Azure Portal Setup (Microsoft Entra ID)

To restrict API access exclusively to your Microsoft organization handle:

### Step 1.1: Register an Application in Azure AD
1. Go to [Azure Portal](https://portal.azure.com/) and navigate to **Microsoft Entra ID** (formerly Azure Active Directory).
2. Click **App registrations** > **New registration**.
3. Name your app (e.g. `ERP PowerBI API`).
4. Under **Supported account types**, select:
   - **Accounts in this organizational directory only (Single tenant)**.
5. Click **Register**.

### Step 1.2: Note your Credentials
- **Directory (tenant) ID** (e.g., `a1b2c3d4-e5f6-7890-abcd-ef1234567890`). This is your `AZURE_TENANT_ID`.
- **Application (client) ID** (e.g., `98765432-10fe-dcba-0987-654321fedcba`). This is your `AZURE_CLIENT_ID`.

### Step 1.3: Expose an API (Scope)
1. Go to **Expose an API** in your App Registration.
2. Set the Application ID URI: `api://<AZURE_CLIENT_ID>`.
3. Click **Add a scope**:
   - Scope name: `access_as_user` or `read`
   - Who can consent: **Admins and users**
   - Display name: `Access ERP PowerBI API`
   - Description: `Allows access to confidential ERP reporting endpoints`

### Step 1.4: Create Client Secret (For Server-to-Server / Postman / Power BI)
1. Go to **Certificates & secrets** > **Client secrets** > **New client secret**.
2. Add a description and expiration, then copy the generated **Value** (Note: Save this immediately as it won't be shown again).

---

## 2. Laravel `.env` Configuration

Update your `.env` file on the server with your Azure credentials:

```env
# Microsoft Identity Platform (Azure AD) Configuration
AZURE_TENANT_ID=your-azure-tenant-id-here
AZURE_CLIENT_ID=your-azure-client-id-here
AZURE_JWKS_CACHE_TTL=86400

# Optional fallback API Key (leave empty to enforce Microsoft Auth exclusively)
POWERBI_API_KEY=
```

---

## 3. How the Authentication Middleware Works

The API relies on `App\Http\Middleware\AzureAdTokenMiddleware`:

1. **Header Inspection**: Expects `Authorization: Bearer <JWT_TOKEN>`.
2. **Microsoft Public Key Verification**: Automatically retrieves Microsoft Entra ID public keys (JWKS) from `https://login.microsoftonline.com/{tenant_id}/discovery/v2.0/keys` and caches them locally for performance (24 hours).
3. **Signature & Expiration Check**: Uses `firebase/php-jwt` with RS256 algorithm to verify token integrity and validity.
4. **Tenant Verification (`tid`)**: Ensures the token belongs specifically to your Microsoft Organization Tenant ID.
5. **Audience Verification (`aud`)**: Ensures the token was minted specifically for your API Application ID.

---

## 4. Testing with Postman

We have included a pre-configured Postman Collection: `PowerBI_Microsoft_Identity_API.postman_collection.json`.

### How to use the Postman Collection:
1. Open Postman and click **Import** > Select [`PowerBI_Microsoft_Identity_API.postman_collection.json`](file:///d:/P2/erpapi/PowerBI_Microsoft_Identity_API.postman_collection.json).
2. Click on the collection name in Postman > Go to **Variables** tab.
3. Fill in your environment variables:
   - `AZURE_TENANT_ID`: Your Azure Tenant ID
   - `AZURE_CLIENT_ID`: Your Azure App Client ID
   - `AZURE_CLIENT_SECRET`: Your Azure Client Secret
   - `base_url`: `http://localhost:8000` (or your domain)
4. Execute Request 1: **`Get Microsoft Bearer Token (Client Credentials)`**.
   - This sends a POST request to Microsoft Identity Platform and retrieves an access token.
   - The test script automatically saves the token into the `microsoft_access_token` collection variable.
5. Execute any request under **2. PowerBI Secure APIs** (e.g. `Get Contacts`).
   - The requests automatically use the Bearer token in the `Authorization` header.

---

## 5. Integrating with Power BI

To connect Power BI to the secured API:

### Option A: Using Power BI Web Connector with OAuth 2.0 (Organizational Account)
1. In Power BI Desktop, select **Get Data** > **Web**.
2. Select **Advanced**.
3. Enter URL (e.g., `https://your-domain.com/api/powerbi/contacts`).
4. Under Authentication, select **Organizational Account** and sign in with your company Microsoft credentials (`user@yourcompany.com`).

### Option B: Power Query M Script with Azure AD Bearer Token
In Power Query Advanced Editor:

```powerquery
let
    // 1. Acquire Token from Microsoft Entra ID
    TokenUrl = "https://login.microsoftonline.com/" & "YOUR_TENANT_ID" & "/oauth2/v2.0/token",
    TokenBody = [
        grant_type = "client_credentials",
        client_id = "YOUR_CLIENT_ID",
        client_secret = "YOUR_CLIENT_SECRET",
        scope = "api://YOUR_CLIENT_ID/.default"
    ],
    TokenResponse = Json.Document(Web.Contents(TokenUrl, [
        Content = Text.ToBinary(Uri.BuildQueryString(TokenBody)),
        Headers = [#"Content-Type"="application/x-www-form-urlencoded"]
    ])),
    AccessToken = TokenResponse[access_token],

    // 2. Query ERP API with Bearer Token
    ApiResponse = Json.Document(Web.Contents("https://your-domain.com/api/powerbi/contacts", [
        Headers = [
            #"Authorization" = "Bearer " & AccessToken,
            #"Accept" = "application/json"
        ]
    ]))
in
    ApiResponse
```

---

## 6. Testing & Troubleshooting

### Common HTTP Status Codes
- `401 Unauthorized`:
  - Missing `Authorization: Bearer <token>` header.
  - Expired token.
  - Signature verification failed (e.g. token tampered with or incorrect key).
- `401 Unauthorized` with Tenant Error:
  - Token was generated by a user/app outside your Microsoft Organization (`tid` mismatch).
- `401 Unauthorized` with Audience Error:
  - Token was generated for a different application (`aud` mismatch).

### Refreshing Cache
If Microsoft rotates keys or you switch tenants, clear your Laravel cache:
```bash
php artisan cache:clear
```
