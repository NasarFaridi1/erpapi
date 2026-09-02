# Power BI Desktop M Query Scripts

This guide contains pre-configured Power Query M scripts for ERP API reporting endpoints. 

Each script automatically acquires a fresh **OAuth 2.0 Bearer Token**, cleans any line break characters, and converts the JSON response directly into a Power BI Table.

---

## Shared OAuth Credentials
* **Base URL**: `https://metaerpapi.aideepseek.uk`
* **Client ID**: `powerbi_client_2026`
* **Client Secret**: `sec_erp_api_9823472398472938`

---

## 🌟 1. Global Transaction Reports (All Relationships Resolved)

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

### C. All Credit & Debit Notes (`api/powerbi/credit-debit-notes`)
*Fetches all credit and debit notes with resolved names for Contact Name, Country, Company, Product Name, and Currency.*

```powerquery
let
    TokenUrl = "https://metaerpapi.aideepseek.uk",
    TokenBody = [grant_type="client_credentials", client_id="powerbi_client_2026", client_secret="sec_erp_api_9823472398472938"],
    TokenResponse = Json.Document(Web.Contents(TokenUrl, [RelativePath="api/oauth/token", Content=Text.ToBinary(Uri.BuildQueryString(TokenBody)), Headers=[#"Content-Type"="application/x-www-form-urlencoded"]])),
    AccessToken = Text.Trim(Text.Clean(Text.Replace(Text.Replace(Text.From(TokenResponse[access_token]), "#(cr)", ""), "#(lf)", ""))),
    Source = Json.Document(Web.Contents("https://metaerpapi.aideepseek.uk", [RelativePath="api/powerbi/credit-debit-notes", Headers=[#"Authorization"="Bearer " & AccessToken, #"Accept"="application/json"]])),
    Result = Table.FromList(Source, Splitter.SplitByNothing(), null, null, ExtraValues.Error)
in
    Result
```

---

### D. All Contracts (`api/powerbi/contracts`)
*Fetches all contracts with resolved Supplier, Customer, Countries, Operating Companies, and Payment Terms.*

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

## 📋 2. Master Data APIs (Resolved Names)

### Contacts (`api/powerbi/contacts`)
*Now includes resolved Country Name and Company Name.*
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

### Countries (`api/powerbi/countries`)
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

### Products (`api/powerbi/products`)
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

### Companies (`api/powerbi/companies`)
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

## 👤 3. Single Contact Reporting APIs (Filtered with Full Relationships)

### Contact Purchases (`api/powerbi/contact/14/purchases`)
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

### Contact Sales (`api/powerbi/contact/14/sales`)
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
