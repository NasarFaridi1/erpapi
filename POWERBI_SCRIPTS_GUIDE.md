# Power BI Desktop M Query Scripts

This guide contains pre-configured Power Query M scripts for ERP API reporting endpoints. 

Each script automatically acquires a fresh **OAuth 2.0 Bearer Token**, cleans any line break characters, and converts the JSON response directly into a Power BI Table.

---

## Shared OAuth Credentials
* **Base URL**: `https://metaerpapi.aideepseek.uk`
* **Client ID**: `powerbi_client_2026`
* **Client Secret**: `sec_erp_api_9823472398472938`

---

## 🌟 Comprehensive Sales & Purchases APIs (Fully Resolved Relationships)

### A. All Sales Report (`api/powerbi/sales` or `api/powerbi/all-sales`)
*Fetches all sales contracts with resolved names for Country, Customer Company, Meta Company, Customer Name, Product Name, Payment Type, Payment Terms, and Currency.*

```powerquery
let
    TokenUrl = "https://metaerpapi.aideepseek.uk",
    TokenBody = [grant_type="client_credentials", client_id="powerbi_client_2026", client_secret="sec_erp_api_9823472398472938"],
    TokenResponse = Json.Document(Web.Contents(TokenUrl, [RelativePath="api/oauth/token", Content=Text.ToBinary(Uri.BuildQueryString(TokenBody)), Headers=[#"Content-Type"="application/x-www-form-urlencoded"]])),
    AccessToken = Text.Trim(Text.Clean(Text.Replace(Text.Replace(Text.From(TokenResponse[access_token]), "#(cr)", ""), "#(lf)", ""))),
    Source = Json.Document(Web.Contents("https://metaerpapi.aideepseek.uk", [RelativePath="api/powerbi/sales", Headers=[#"Authorization"="Bearer " & AccessToken, #"Accept"="application/json"]])),
    Result = Table.FromList(Source, Splitter.SplitByNothing(), null, null, ExtraValues.Error)
in
    Result
```

---

### B. All Purchases Report (`api/powerbi/purchases` or `api/powerbi/all-purchases`)
*Fetches all purchase contracts with resolved names for Country, Supplier Company, Meta Company, Supplier Name, Product Name, Payment Type, Payment Terms, and Currency.*

```powerquery
let
    TokenUrl = "https://metaerpapi.aideepseek.uk",
    TokenBody = [grant_type="client_credentials", client_id="powerbi_client_2026", client_secret="sec_erp_api_9823472398472938"],
    TokenResponse = Json.Document(Web.Contents(TokenUrl, [RelativePath="api/oauth/token", Content=Text.ToBinary(Uri.BuildQueryString(TokenBody)), Headers=[#"Content-Type"="application/x-www-form-urlencoded"]])),
    AccessToken = Text.Trim(Text.Clean(Text.Replace(Text.Replace(Text.From(TokenResponse[access_token]), "#(cr)", ""), "#(lf)", ""))),
    Source = Json.Document(Web.Contents("https://metaerpapi.aideepseek.uk", [RelativePath="api/powerbi/purchases", Headers=[#"Authorization"="Bearer " & AccessToken, #"Accept"="application/json"]])),
    Result = Table.FromList(Source, Splitter.SplitByNothing(), null, null, ExtraValues.Error)
in
    Result
```

---

## 📋 Master Data APIs

### 1. Contacts (`api/powerbi/contacts`)
```powerquery
let
    TokenUrl = "https://metaerpapi.aideepseek.uk",
    TokenBody = [grant_type="client_credentials", client_id="powerbi_client_2026", client_secret="sec_erp_api_9823472398472938"],
    TokenResponse = Json.Document(Web.Contents(TokenUrl, [RelativePath="api/oauth/token", Content=Text.ToBinary(Uri.BuildQueryString(TokenBody)), Headers=[#"Content-Type"="application/x-www-form-urlencoded"]])),
    AccessToken = Text.Trim(Text.Clean(Text.Replace(Text.Replace(Text.From(TokenResponse[access_token]), "#(cr)", ""), "#(lf)", ""))),
    Source = Json.Document(Web.Contents("https://metaerpapi.aideepseek.uk", [RelativePath="api/powerbi/contacts", Headers=[#"Authorization"="Bearer " & AccessToken, #"Accept"="application/json"]])),
    Result = Table.FromList(Source, Splitter.SplitByNothing(), null, null, ExtraValues.Error)
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
    AccessToken = Text.Trim(Text.Clean(Text.Replace(Text.Replace(Text.From(TokenResponse[access_token]), "#(cr)", ""), "#(lf)", ""))),
    Source = Json.Document(Web.Contents("https://metaerpapi.aideepseek.uk", [RelativePath="api/powerbi/countries", Headers=[#"Authorization"="Bearer " & AccessToken, #"Accept"="application/json"]])),
    Result = Table.FromList(Source, Splitter.SplitByNothing(), null, null, ExtraValues.Error)
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
    AccessToken = Text.Trim(Text.Clean(Text.Replace(Text.Replace(Text.From(TokenResponse[access_token]), "#(cr)", ""), "#(lf)", ""))),
    Source = Json.Document(Web.Contents("https://metaerpapi.aideepseek.uk", [RelativePath="api/powerbi/products", Headers=[#"Authorization"="Bearer " & AccessToken, #"Accept"="application/json"]])),
    Result = Table.FromList(Source, Splitter.SplitByNothing(), null, null, ExtraValues.Error)
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
    AccessToken = Text.Trim(Text.Clean(Text.Replace(Text.Replace(Text.From(TokenResponse[access_token]), "#(cr)", ""), "#(lf)", ""))),
    Source = Json.Document(Web.Contents("https://metaerpapi.aideepseek.uk", [RelativePath="api/powerbi/companies", Headers=[#"Authorization"="Bearer " & AccessToken, #"Accept"="application/json"]])),
    Result = Table.FromList(Source, Splitter.SplitByNothing(), null, null, ExtraValues.Error)
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
    AccessToken = Text.Trim(Text.Clean(Text.Replace(Text.Replace(Text.From(TokenResponse[access_token]), "#(cr)", ""), "#(lf)", ""))),
    Source = Json.Document(Web.Contents("https://metaerpapi.aideepseek.uk", [RelativePath="api/powerbi/contracts", Headers=[#"Authorization"="Bearer " & AccessToken, #"Accept"="application/json"]])),
    Result = Table.FromList(Source, Splitter.SplitByNothing(), null, null, ExtraValues.Error)
in
    Result
```

---

## 👤 Single Contact Reporting APIs

### 6. Contact Details by ID (`api/powerbi/contact/14`)
```powerquery
let
    TokenUrl = "https://metaerpapi.aideepseek.uk",
    TokenBody = [grant_type="client_credentials", client_id="powerbi_client_2026", client_secret="sec_erp_api_9823472398472938"],
    TokenResponse = Json.Document(Web.Contents(TokenUrl, [RelativePath="api/oauth/token", Content=Text.ToBinary(Uri.BuildQueryString(TokenBody)), Headers=[#"Content-Type"="application/x-www-form-urlencoded"]])),
    AccessToken = Text.Trim(Text.Clean(Text.Replace(Text.Replace(Text.From(TokenResponse[access_token]), "#(cr)", ""), "#(lf)", ""))),
    Source = Json.Document(Web.Contents("https://metaerpapi.aideepseek.uk", [RelativePath="api/powerbi/contact/14", Headers=[#"Authorization"="Bearer " & AccessToken, #"Accept"="application/json"]])),
    Result = Table.FromList(Source, Splitter.SplitByNothing(), null, null, ExtraValues.Error)
in
    Result
```

---

### 7. Contact Purchases (`api/powerbi/contact/14/purchases`)
```powerquery
let
    TokenUrl = "https://metaerpapi.aideepseek.uk",
    TokenBody = [grant_type="client_credentials", client_id="powerbi_client_2026", client_secret="sec_erp_api_9823472398472938"],
    TokenResponse = Json.Document(Web.Contents(TokenUrl, [RelativePath="api/oauth/token", Content=Text.ToBinary(Uri.BuildQueryString(TokenBody)), Headers=[#"Content-Type"="application/x-www-form-urlencoded"]])),
    AccessToken = Text.Trim(Text.Clean(Text.Replace(Text.Replace(Text.From(TokenResponse[access_token]), "#(cr)", ""), "#(lf)", ""))),
    Source = Json.Document(Web.Contents("https://metaerpapi.aideepseek.uk", [RelativePath="api/powerbi/contact/14/purchases", Headers=[#"Authorization"="Bearer " & AccessToken, #"Accept"="application/json"]])),
    Result = Table.FromList(Source, Splitter.SplitByNothing(), null, null, ExtraValues.Error)
in
    Result
```

---

### 8. Contact Sales (`api/powerbi/contact/14/sales`)
```powerquery
let
    TokenUrl = "https://metaerpapi.aideepseek.uk",
    TokenBody = [grant_type="client_credentials", client_id="powerbi_client_2026", client_secret="sec_erp_api_9823472398472938"],
    TokenResponse = Json.Document(Web.Contents(TokenUrl, [RelativePath="api/oauth/token", Content=Text.ToBinary(Uri.BuildQueryString(TokenBody)), Headers=[#"Content-Type"="application/x-www-form-urlencoded"]])),
    AccessToken = Text.Trim(Text.Clean(Text.Replace(Text.Replace(Text.From(TokenResponse[access_token]), "#(cr)", ""), "#(lf)", ""))),
    Source = Json.Document(Web.Contents("https://metaerpapi.aideepseek.uk", [RelativePath="api/powerbi/contact/14/sales", Headers=[#"Authorization"="Bearer " & AccessToken, #"Accept"="application/json"]])),
    Result = Table.FromList(Source, Splitter.SplitByNothing(), null, null, ExtraValues.Error)
in
    Result
```

---

### 9. Buying Payment Terms (`api/powerbi/contact/14/buying-payment-terms`)
```powerquery
let
    TokenUrl = "https://metaerpapi.aideepseek.uk",
    TokenBody = [grant_type="client_credentials", client_id="powerbi_client_2026", client_secret="sec_erp_api_9823472398472938"],
    TokenResponse = Json.Document(Web.Contents(TokenUrl, [RelativePath="api/oauth/token", Content=Text.ToBinary(Uri.BuildQueryString(TokenBody)), Headers=[#"Content-Type"="application/x-www-form-urlencoded"]])),
    AccessToken = Text.Trim(Text.Clean(Text.Replace(Text.Replace(Text.From(TokenResponse[access_token]), "#(cr)", ""), "#(lf)", ""))),
    Source = Json.Document(Web.Contents("https://metaerpapi.aideepseek.uk", [RelativePath="api/powerbi/contact/14/buying-payment-terms", Headers=[#"Authorization"="Bearer " & AccessToken, #"Accept"="application/json"]])),
    Result = Table.FromList(Source, Splitter.SplitByNothing(), null, null, ExtraValues.Error)
in
    Result
```

---

### 10. Selling Payment Terms (`api/powerbi/contact/14/selling-payment-terms`)
```powerquery
let
    TokenUrl = "https://metaerpapi.aideepseek.uk",
    TokenBody = [grant_type="client_credentials", client_id="powerbi_client_2026", client_secret="sec_erp_api_9823472398472938"],
    TokenResponse = Json.Document(Web.Contents(TokenUrl, [RelativePath="api/oauth/token", Content=Text.ToBinary(Uri.BuildQueryString(TokenBody)), Headers=[#"Content-Type"="application/x-www-form-urlencoded"]])),
    AccessToken = Text.Trim(Text.Clean(Text.Replace(Text.Replace(Text.From(TokenResponse[access_token]), "#(cr)", ""), "#(lf)", ""))),
    Source = Json.Document(Web.Contents("https://metaerpapi.aideepseek.uk", [RelativePath="api/powerbi/contact/14/selling-payment-terms", Headers=[#"Authorization"="Bearer " & AccessToken, #"Accept"="application/json"]])),
    Result = Table.FromList(Source, Splitter.SplitByNothing(), null, null, ExtraValues.Error)
in
    Result
```

---

### 11. Product Buying Country (`api/powerbi/contact/14/product-buying-country`)
```powerquery
let
    TokenUrl = "https://metaerpapi.aideepseek.uk",
    TokenBody = [grant_type="client_credentials", client_id="powerbi_client_2026", client_secret="sec_erp_api_9823472398472938"],
    TokenResponse = Json.Document(Web.Contents(TokenUrl, [RelativePath="api/oauth/token", Content=Text.ToBinary(Uri.BuildQueryString(TokenBody)), Headers=[#"Content-Type"="application/x-www-form-urlencoded"]])),
    AccessToken = Text.Trim(Text.Clean(Text.Replace(Text.Replace(Text.From(TokenResponse[access_token]), "#(cr)", ""), "#(lf)", ""))),
    Source = Json.Document(Web.Contents("https://metaerpapi.aideepseek.uk", [RelativePath="api/powerbi/contact/14/product-buying-country", Headers=[#"Authorization"="Bearer " & AccessToken, #"Accept"="application/json"]])),
    Result = Table.FromList(Source, Splitter.SplitByNothing(), null, null, ExtraValues.Error)
in
    Result
```

---

### 12. Product Selling Country (`api/powerbi/contact/14/product-selling-country`)
```powerquery
let
    TokenUrl = "https://metaerpapi.aideepseek.uk",
    TokenBody = [grant_type="client_credentials", client_id="powerbi_client_2026", client_secret="sec_erp_api_9823472398472938"],
    TokenResponse = Json.Document(Web.Contents(TokenUrl, [RelativePath="api/oauth/token", Content=Text.ToBinary(Uri.BuildQueryString(TokenBody)), Headers=[#"Content-Type"="application/x-www-form-urlencoded"]])),
    AccessToken = Text.Trim(Text.Clean(Text.Replace(Text.Replace(Text.From(TokenResponse[access_token]), "#(cr)", ""), "#(lf)", ""))),
    Source = Json.Document(Web.Contents("https://metaerpapi.aideepseek.uk", [RelativePath="api/powerbi/contact/14/product-selling-country", Headers=[#"Authorization"="Bearer " & AccessToken, #"Accept"="application/json"]])),
    Result = Table.FromList(Source, Splitter.SplitByNothing(), null, null, ExtraValues.Error)
in
    Result
```

---

### 13. Credit / Debit Notes (`api/powerbi/contact/14/credit-debit-notes`)
```powerquery
let
    TokenUrl = "https://metaerpapi.aideepseek.uk",
    TokenBody = [grant_type="client_credentials", client_id="powerbi_client_2026", client_secret="sec_erp_api_9823472398472938"],
    TokenResponse = Json.Document(Web.Contents(TokenUrl, [RelativePath="api/oauth/token", Content=Text.ToBinary(Uri.BuildQueryString(TokenBody)), Headers=[#"Content-Type"="application/x-www-form-urlencoded"]])),
    AccessToken = Text.Trim(Text.Clean(Text.Replace(Text.Replace(Text.From(TokenResponse[access_token]), "#(cr)", ""), "#(lf)", ""))),
    Source = Json.Document(Web.Contents("https://metaerpapi.aideepseek.uk", [RelativePath="api/powerbi/contact/14/credit-debit-notes", Headers=[#"Authorization"="Bearer " & AccessToken, #"Accept"="application/json"]])),
    Result = Table.FromList(Source, Splitter.SplitByNothing(), null, null, ExtraValues.Error)
in
    Result
```

---

### 14. Dashboard Summary (`api/powerbi/contact/14/dashboard-summary`)
```powerquery
let
    TokenUrl = "https://metaerpapi.aideepseek.uk",
    TokenBody = [grant_type="client_credentials", client_id="powerbi_client_2026", client_secret="sec_erp_api_9823472398472938"],
    TokenResponse = Json.Document(Web.Contents(TokenUrl, [RelativePath="api/oauth/token", Content=Text.ToBinary(Uri.BuildQueryString(TokenBody)), Headers=[#"Content-Type"="application/x-www-form-urlencoded"]])),
    AccessToken = Text.Trim(Text.Clean(Text.Replace(Text.Replace(Text.From(TokenResponse[access_token]), "#(cr)", ""), "#(lf)", ""))),
    Source = Json.Document(Web.Contents("https://metaerpapi.aideepseek.uk", [RelativePath="api/powerbi/contact/14/dashboard-summary", Headers=[#"Authorization"="Bearer " & AccessToken, #"Accept"="application/json"]])),
    Result = Table.FromList(Source, Splitter.SplitByNothing(), null, null, ExtraValues.Error)
in
    Result
```

---

### 15. Full Dashboard (`api/powerbi/powerbi/contact/14/dashboard`)
```powerquery
let
    TokenUrl = "https://metaerpapi.aideepseek.uk",
    TokenBody = [grant_type="client_credentials", client_id="powerbi_client_2026", client_secret="sec_erp_api_9823472398472938"],
    TokenResponse = Json.Document(Web.Contents(TokenUrl, [RelativePath="api/oauth/token", Content=Text.ToBinary(Uri.BuildQueryString(TokenBody)), Headers=[#"Content-Type"="application/x-www-form-urlencoded"]])),
    AccessToken = Text.Trim(Text.Clean(Text.Replace(Text.Replace(Text.From(TokenResponse[access_token]), "#(cr)", ""), "#(lf)", ""))),
    Source = Json.Document(Web.Contents("https://metaerpapi.aideepseek.uk", [RelativePath="api/powerbi/powerbi/contact/14/dashboard", Headers=[#"Authorization"="Bearer " & AccessToken, #"Accept"="application/json"]])),
    Result = Table.FromList(Source, Splitter.SplitByNothing(), null, null, ExtraValues.Error)
in
    Result
```
