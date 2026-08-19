# Power BI Desktop M Query Scripts (All 15 Endpoints)

This guide contains pre-configured Power Query M scripts for all 15 ERP API endpoints. 

Each script automatically acquires a fresh **OAuth 2.0 Bearer Token** and converts the JSON response directly into a Power BI Table using `Table.FromRecords()`.

---

## Shared OAuth Credentials
* **Base URL**: `https://metaerpapi.aideepseek.uk`
* **Client ID**: `powerbi_client_2026`
* **Client Secret**: `sec_erp_api_9823472398472938`

---

### 1. Contacts (`api/powerbi/contacts`)
```powerquery
let
    TokenUrl = "https://metaerpapi.aideepseek.uk",
    TokenBody = [grant_type="client_credentials", client_id="powerbi_client_2026", client_secret="sec_erp_api_9823472398472938"],
    TokenResponse = Json.Document(Web.Contents(TokenUrl, [RelativePath="api/oauth/token", Content=Text.ToBinary(Uri.BuildQueryString(TokenBody)), Headers=[#"Content-Type"="application/x-www-form-urlencoded"]])),
    AccessToken = TokenResponse[access_token],
    Source = Json.Document(Web.Contents("https://metaerpapi.aideepseek.uk", [RelativePath="api/powerbi/contacts", Headers=[#"Authorization"="Bearer " & AccessToken, #"Accept"="application/json"]])),
    Result = Table.FromRecords(Source)
in
    Result
```

---

### 2. Countries (`api/powerbi/countries`)
```powerquery
let
    TokenUrl = "https://metaerpapi.aideepseek.uk",
    TokenBody = [grant_type="client_credentials", client_id="powerbi_client_2026", client_secret="sec_erp_api_9823472398472938"],
    TokenResponse = Json.Document(Web.Contents(TokenUrl, [RelativePath="api/oauth/token", Content=Text.ToBinary(Uri.BuildQueryString(TokenBody)), Headers=[#"Content-Type"="application/x-www-form-urlencoded"]])),
    AccessToken = TokenResponse[access_token],
    Source = Json.Document(Web.Contents("https://metaerpapi.aideepseek.uk", [RelativePath="api/powerbi/countries", Headers=[#"Authorization"="Bearer " & AccessToken, #"Accept"="application/json"]])),
    Result = Table.FromRecords(Source)
in
    Result
```

---

### 3. Products (`api/powerbi/products`)
```powerquery
let
    TokenUrl = "https://metaerpapi.aideepseek.uk",
    TokenBody = [grant_type="client_credentials", client_id="powerbi_client_2026", client_secret="sec_erp_api_9823472398472938"],
    TokenResponse = Json.Document(Web.Contents(TokenUrl, [RelativePath="api/oauth/token", Content=Text.ToBinary(Uri.BuildQueryString(TokenBody)), Headers=[#"Content-Type"="application/x-www-form-urlencoded"]])),
    AccessToken = TokenResponse[access_token],
    Source = Json.Document(Web.Contents("https://metaerpapi.aideepseek.uk", [RelativePath="api/powerbi/products", Headers=[#"Authorization"="Bearer " & AccessToken, #"Accept"="application/json"]])),
    Result = Table.FromRecords(Source)
in
    Result
```

---

### 4. Companies (`api/powerbi/companies`)
```powerquery
let
    TokenUrl = "https://metaerpapi.aideepseek.uk",
    TokenBody = [grant_type="client_credentials", client_id="powerbi_client_2026", client_secret="sec_erp_api_9823472398472938"],
    TokenResponse = Json.Document(Web.Contents(TokenUrl, [RelativePath="api/oauth/token", Content=Text.ToBinary(Uri.BuildQueryString(TokenBody)), Headers=[#"Content-Type"="application/x-www-form-urlencoded"]])),
    AccessToken = TokenResponse[access_token],
    Source = Json.Document(Web.Contents("https://metaerpapi.aideepseek.uk", [RelativePath="api/powerbi/companies", Headers=[#"Authorization"="Bearer " & AccessToken, #"Accept"="application/json"]])),
    Result = Table.FromRecords(Source)
in
    Result
```

---

### 5. Contracts (`api/powerbi/contracts`)
```powerquery
let
    TokenUrl = "https://metaerpapi.aideepseek.uk",
    TokenBody = [grant_type="client_credentials", client_id="powerbi_client_2026", client_secret="sec_erp_api_9823472398472938"],
    TokenResponse = Json.Document(Web.Contents(TokenUrl, [RelativePath="api/oauth/token", Content=Text.ToBinary(Uri.BuildQueryString(TokenBody)), Headers=[#"Content-Type"="application/x-www-form-urlencoded"]])),
    AccessToken = TokenResponse[access_token],
    Source = Json.Document(Web.Contents("https://metaerpapi.aideepseek.uk", [RelativePath="api/powerbi/contracts", Headers=[#"Authorization"="Bearer " & AccessToken, #"Accept"="application/json"]])),
    Result = Table.FromRecords(Source)
in
    Result
```

---

### 6. Contact Details by ID (`api/powerbi/contact/1`)
```powerquery
let
    TokenUrl = "https://metaerpapi.aideepseek.uk",
    TokenBody = [grant_type="client_credentials", client_id="powerbi_client_2026", client_secret="sec_erp_api_9823472398472938"],
    TokenResponse = Json.Document(Web.Contents(TokenUrl, [RelativePath="api/oauth/token", Content=Text.ToBinary(Uri.BuildQueryString(TokenBody)), Headers=[#"Content-Type"="application/x-www-form-urlencoded"]])),
    AccessToken = TokenResponse[access_token],
    Source = Json.Document(Web.Contents("https://metaerpapi.aideepseek.uk", [RelativePath="api/powerbi/contact/1", Headers=[#"Authorization"="Bearer " & AccessToken, #"Accept"="application/json"]])),
    Result = Record.ToTable(Source)
in
    Result
```

---

### 7. Contact Purchases (`api/powerbi/contact/1/purchases`)
```powerquery
let
    TokenUrl = "https://metaerpapi.aideepseek.uk",
    TokenBody = [grant_type="client_credentials", client_id="powerbi_client_2026", client_secret="sec_erp_api_9823472398472938"],
    TokenResponse = Json.Document(Web.Contents(TokenUrl, [RelativePath="api/oauth/token", Content=Text.ToBinary(Uri.BuildQueryString(TokenBody)), Headers=[#"Content-Type"="application/x-www-form-urlencoded"]])),
    AccessToken = TokenResponse[access_token],
    Source = Json.Document(Web.Contents("https://metaerpapi.aideepseek.uk", [RelativePath="api/powerbi/contact/1/purchases", Headers=[#"Authorization"="Bearer " & AccessToken, #"Accept"="application/json"]])),
    Result = Table.FromRecords(Source)
in
    Result
```

---

### 8. Contact Sales (`api/powerbi/contact/1/sales`)
```powerquery
let
    TokenUrl = "https://metaerpapi.aideepseek.uk",
    TokenBody = [grant_type="client_credentials", client_id="powerbi_client_2026", client_secret="sec_erp_api_9823472398472938"],
    TokenResponse = Json.Document(Web.Contents(TokenUrl, [RelativePath="api/oauth/token", Content=Text.ToBinary(Uri.BuildQueryString(TokenBody)), Headers=[#"Content-Type"="application/x-www-form-urlencoded"]])),
    AccessToken = TokenResponse[access_token],
    Source = Json.Document(Web.Contents("https://metaerpapi.aideepseek.uk", [RelativePath="api/powerbi/contact/1/sales", Headers=[#"Authorization"="Bearer " & AccessToken, #"Accept"="application/json"]])),
    Result = Table.FromRecords(Source)
in
    Result
```

---

### 9. Buying Payment Terms (`api/powerbi/contact/1/buying-payment-terms`)
```powerquery
let
    TokenUrl = "https://metaerpapi.aideepseek.uk",
    TokenBody = [grant_type="client_credentials", client_id="powerbi_client_2026", client_secret="sec_erp_api_9823472398472938"],
    TokenResponse = Json.Document(Web.Contents(TokenUrl, [RelativePath="api/oauth/token", Content=Text.ToBinary(Uri.BuildQueryString(TokenBody)), Headers=[#"Content-Type"="application/x-www-form-urlencoded"]])),
    AccessToken = TokenResponse[access_token],
    Source = Json.Document(Web.Contents("https://metaerpapi.aideepseek.uk", [RelativePath="api/powerbi/contact/1/buying-payment-terms", Headers=[#"Authorization"="Bearer " & AccessToken, #"Accept"="application/json"]])),
    Result = Table.FromRecords(Source)
in
    Result
```

---

### 10. Selling Payment Terms (`api/powerbi/contact/1/selling-payment-terms`)
```powerquery
let
    TokenUrl = "https://metaerpapi.aideepseek.uk",
    TokenBody = [grant_type="client_credentials", client_id="powerbi_client_2026", client_secret="sec_erp_api_9823472398472938"],
    TokenResponse = Json.Document(Web.Contents(TokenUrl, [RelativePath="api/oauth/token", Content=Text.ToBinary(Uri.BuildQueryString(TokenBody)), Headers=[#"Content-Type"="application/x-www-form-urlencoded"]])),
    AccessToken = TokenResponse[access_token],
    Source = Json.Document(Web.Contents("https://metaerpapi.aideepseek.uk", [RelativePath="api/powerbi/contact/1/selling-payment-terms", Headers=[#"Authorization"="Bearer " & AccessToken, #"Accept"="application/json"]])),
    Result = Table.FromRecords(Source)
in
    Result
```

---

### 11. Product Buying Country (`api/powerbi/contact/1/product-buying-country`)
```powerquery
let
    TokenUrl = "https://metaerpapi.aideepseek.uk",
    TokenBody = [grant_type="client_credentials", client_id="powerbi_client_2026", client_secret="sec_erp_api_9823472398472938"],
    TokenResponse = Json.Document(Web.Contents(TokenUrl, [RelativePath="api/oauth/token", Content=Text.ToBinary(Uri.BuildQueryString(TokenBody)), Headers=[#"Content-Type"="application/x-www-form-urlencoded"]])),
    AccessToken = TokenResponse[access_token],
    Source = Json.Document(Web.Contents("https://metaerpapi.aideepseek.uk", [RelativePath="api/powerbi/contact/1/product-buying-country", Headers=[#"Authorization"="Bearer " & AccessToken, #"Accept"="application/json"]])),
    Result = Table.FromRecords(Source)
in
    Result
```

---

### 12. Product Selling Country (`api/powerbi/contact/1/product-selling-country`)
```powerquery
let
    TokenUrl = "https://metaerpapi.aideepseek.uk",
    TokenBody = [grant_type="client_credentials", client_id="powerbi_client_2026", client_secret="sec_erp_api_9823472398472938"],
    TokenResponse = Json.Document(Web.Contents(TokenUrl, [RelativePath="api/oauth/token", Content=Text.ToBinary(Uri.BuildQueryString(TokenBody)), Headers=[#"Content-Type"="application/x-www-form-urlencoded"]])),
    AccessToken = TokenResponse[access_token],
    Source = Json.Document(Web.Contents("https://metaerpapi.aideepseek.uk", [RelativePath="api/powerbi/contact/1/product-selling-country", Headers=[#"Authorization"="Bearer " & AccessToken, #"Accept"="application/json"]])),
    Result = Table.FromRecords(Source)
in
    Result
```

---

### 13. Credit / Debit Notes (`api/powerbi/contact/1/credit-debit-notes`)
```powerquery
let
    TokenUrl = "https://metaerpapi.aideepseek.uk",
    TokenBody = [grant_type="client_credentials", client_id="powerbi_client_2026", client_secret="sec_erp_api_9823472398472938"],
    TokenResponse = Json.Document(Web.Contents(TokenUrl, [RelativePath="api/oauth/token", Content=Text.ToBinary(Uri.BuildQueryString(TokenBody)), Headers=[#"Content-Type"="application/x-www-form-urlencoded"]])),
    AccessToken = TokenResponse[access_token],
    Source = Json.Document(Web.Contents("https://metaerpapi.aideepseek.uk", [RelativePath="api/powerbi/contact/1/credit-debit-notes", Headers=[#"Authorization"="Bearer " & AccessToken, #"Accept"="application/json"]])),
    Result = Table.FromRecords(Source)
in
    Result
```

---

### 14. Dashboard Summary (`api/powerbi/contact/1/dashboard-summary`)
```powerquery
let
    TokenUrl = "https://metaerpapi.aideepseek.uk",
    TokenBody = [grant_type="client_credentials", client_id="powerbi_client_2026", client_secret="sec_erp_api_9823472398472938"],
    TokenResponse = Json.Document(Web.Contents(TokenUrl, [RelativePath="api/oauth/token", Content=Text.ToBinary(Uri.BuildQueryString(TokenBody)), Headers=[#"Content-Type"="application/x-www-form-urlencoded"]])),
    AccessToken = TokenResponse[access_token],
    Source = Json.Document(Web.Contents("https://metaerpapi.aideepseek.uk", [RelativePath="api/powerbi/contact/1/dashboard-summary", Headers=[#"Authorization"="Bearer " & AccessToken, #"Accept"="application/json"]])),
    Result = Record.ToTable(Source)
in
    Result
```

---

### 15. Full Dashboard (`api/powerbi/powerbi/contact/1/dashboard`)
```powerquery
let
    TokenUrl = "https://metaerpapi.aideepseek.uk",
    TokenBody = [grant_type="client_credentials", client_id="powerbi_client_2026", client_secret="sec_erp_api_9823472398472938"],
    TokenResponse = Json.Document(Web.Contents(TokenUrl, [RelativePath="api/oauth/token", Content=Text.ToBinary(Uri.BuildQueryString(TokenBody)), Headers=[#"Content-Type"="application/x-www-form-urlencoded"]])),
    AccessToken = TokenResponse[access_token],
    Source = Json.Document(Web.Contents("https://metaerpapi.aideepseek.uk", [RelativePath="api/powerbi/powerbi/contact/1/dashboard", Headers=[#"Authorization"="Bearer " & AccessToken, #"Accept"="application/json"]])),
    Result = Record.ToTable(Source)
in
    Result
```
