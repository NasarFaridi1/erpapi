# ERP Power BI Desktop Query Collection

This guide provides **ready-to-copy Power Query (M) scripts** for Microsoft Power BI Desktop.

Every query automatically:
1. Authenticates via **OAuth 2.0 Client Credentials** (`api/oauth/token`).
2. Obtains a secure 1-hour Bearer Token.
3. Automatically sanitizes token string line breaks (`CRLF`).
4. Fetches and automatically expands all records into a complete Power BI Table with **all foreign keys resolved to human-readable names (no raw IDs)**.

---

## 🔑 Authentication Credentials
* **API Base URL**: `https://metaerpapi.aideepseek.uk`
* **Grant Type**: `client_credentials`
* **Client ID**: `powerbi_client_2026`
* **Client Secret**: `sec_erp_api_9823472398472938`

---

## 📌 How to Add Any Query in Power BI Desktop (3 Steps)
1. Open **Power BI Desktop** > click **Get Data** > choose **Blank Query**.
2. In the top ribbon, click **Advanced Editor**.
3. Delete everything in the editor window, **paste any query below**, and click **Done**!

---

## 🌟 1. Global Transaction & Operations Reports

### Query 1: All Sales Report (`api/powerbi/all-sales`)
*Fetches all sales contracts with resolved names for Country, Customer Company, Meta Company, Customer Name, Product Name, Quantity, Rate, Total Price, Payment Type, Payment Terms, and Currency.*

```powerquery
let
    TokenUrl = "https://metaerpapi.aideepseek.uk",
    TokenBody = [grant_type="client_credentials", client_id="powerbi_client_2026", client_secret="sec_erp_api_9823472398472938"],
    TokenResponse = Json.Document(Web.Contents(TokenUrl, [RelativePath="api/oauth/token", Content=Text.ToBinary(Uri.BuildQueryString(TokenBody)), Headers=[#"Content-Type"="application/x-www-form-urlencoded"]])),
    AccessToken = Text.Trim(Text.Clean(Text.Replace(Text.Replace(Text.From(TokenResponse[access_token]), "#(cr)", ""), "#(lf)", ""))),
    Source = Json.Document(Web.Contents("https://metaerpapi.aideepseek.uk", [RelativePath="api/powerbi/all-sales", Headers=[#"Authorization"="Bearer " & AccessToken, #"Accept"="application/json"]])),
    TableFromList = Table.FromList(Source, Splitter.SplitByNothing(), null, null, ExtraValues.Error),
    ExpandedColumns = Table.ExpandRecordColumn(TableFromList, "Column1", Record.FieldNames(Source{0}))
in
    ExpandedColumns
```

---

### Query 2: All Purchases Report (`api/powerbi/all-purchases`)
*Fetches all purchase contracts with resolved names for Country, Supplier Company, Meta Company, Supplier Name, Product Name, Quantity, Rate, Total Price, Payment Type, Payment Terms, and Currency.*

```powerquery
let
    TokenUrl = "https://metaerpapi.aideepseek.uk",
    TokenBody = [grant_type="client_credentials", client_id="powerbi_client_2026", client_secret="sec_erp_api_9823472398472938"],
    TokenResponse = Json.Document(Web.Contents(TokenUrl, [RelativePath="api/oauth/token", Content=Text.ToBinary(Uri.BuildQueryString(TokenBody)), Headers=[#"Content-Type"="application/x-www-form-urlencoded"]])),
    AccessToken = Text.Trim(Text.Clean(Text.Replace(Text.Replace(Text.From(TokenResponse[access_token]), "#(cr)", ""), "#(lf)", ""))),
    Source = Json.Document(Web.Contents("https://metaerpapi.aideepseek.uk", [RelativePath="api/powerbi/all-purchases", Headers=[#"Authorization"="Bearer " & AccessToken, #"Accept"="application/json"]])),
    TableFromList = Table.FromList(Source, Splitter.SplitByNothing(), null, null, ExtraValues.Error),
    ExpandedColumns = Table.ExpandRecordColumn(TableFromList, "Column1", Record.FieldNames(Source{0}))
in
    ExpandedColumns
```

---

### Query 3: All Credit & Debit Notes (`api/powerbi/all-credit-debit-notes`)
*Fetches all credit notes, debit notes, and payment instructions with resolved Contact Name, Country, Company, and Note Description.*

```powerquery
let
    TokenUrl = "https://metaerpapi.aideepseek.uk",
    TokenBody = [grant_type="client_credentials", client_id="powerbi_client_2026", client_secret="sec_erp_api_9823472398472938"],
    TokenResponse = Json.Document(Web.Contents(TokenUrl, [RelativePath="api/oauth/token", Content=Text.ToBinary(Uri.BuildQueryString(TokenBody)), Headers=[#"Content-Type"="application/x-www-form-urlencoded"]])),
    AccessToken = Text.Trim(Text.Clean(Text.Replace(Text.Replace(Text.From(TokenResponse[access_token]), "#(cr)", ""), "#(lf)", ""))),
    Source = Json.Document(Web.Contents("https://metaerpapi.aideepseek.uk", [RelativePath="api/powerbi/all-credit-debit-notes", Headers=[#"Authorization"="Bearer " & AccessToken, #"Accept"="application/json"]])),
    TableFromList = Table.FromList(Source, Splitter.SplitByNothing(), null, null, ExtraValues.Error),
    ExpandedColumns = Table.ExpandRecordColumn(TableFromList, "Column1", Record.FieldNames(Source{0}))
in
    ExpandedColumns
```

---

### Query 4: All Contracts Master (`api/powerbi/contracts`)
*Fetches all contracts with resolved Supplier, Customer, Countries, Operating Companies, and Payment Terms.*

```powerquery
let
    TokenUrl = "https://metaerpapi.aideepseek.uk",
    TokenBody = [grant_type="client_credentials", client_id="powerbi_client_2026", client_secret="sec_erp_api_9823472398472938"],
    TokenResponse = Json.Document(Web.Contents(TokenUrl, [RelativePath="api/oauth/token", Content=Text.ToBinary(Uri.BuildQueryString(TokenBody)), Headers=[#"Content-Type"="application/x-www-form-urlencoded"]])),
    AccessToken = Text.Trim(Text.Clean(Text.Replace(Text.Replace(Text.From(TokenResponse[access_token]), "#(cr)", ""), "#(lf)", ""))),
    Source = Json.Document(Web.Contents("https://metaerpapi.aideepseek.uk", [RelativePath="api/powerbi/contracts", Headers=[#"Authorization"="Bearer " & AccessToken, #"Accept"="application/json"]])),
    TableFromList = Table.FromList(Source, Splitter.SplitByNothing(), null, null, ExtraValues.Error),
    ExpandedColumns = Table.ExpandRecordColumn(TableFromList, "Column1", Record.FieldNames(Source{0}))
in
    ExpandedColumns
```

---

## 📋 2. Master Data Lookup Tables

### Query 5: Contacts / Customers / Suppliers (`api/powerbi/contacts`)
*Fetches all contacts with Country Name and Company Name resolved.*

```powerquery
let
    TokenUrl = "https://metaerpapi.aideepseek.uk",
    TokenBody = [grant_type="client_credentials", client_id="powerbi_client_2026", client_secret="sec_erp_api_9823472398472938"],
    TokenResponse = Json.Document(Web.Contents(TokenUrl, [RelativePath="api/oauth/token", Content=Text.ToBinary(Uri.BuildQueryString(TokenBody)), Headers=[#"Content-Type"="application/x-www-form-urlencoded"]])),
    AccessToken = Text.Trim(Text.Clean(Text.Replace(Text.Replace(Text.From(TokenResponse[access_token]), "#(cr)", ""), "#(lf)", ""))),
    Source = Json.Document(Web.Contents("https://metaerpapi.aideepseek.uk", [RelativePath="api/powerbi/contacts", Headers=[#"Authorization"="Bearer " & AccessToken, #"Accept"="application/json"]])),
    TableFromList = Table.FromList(Source, Splitter.SplitByNothing(), null, null, ExtraValues.Error),
    ExpandedColumns = Table.ExpandRecordColumn(TableFromList, "Column1", Record.FieldNames(Source{0}))
in
    ExpandedColumns
```

---

### Query 6: Products Master (`api/powerbi/products`)
*Fetches all products with Product ID, Product Name, and Product Code.*

```powerquery
let
    TokenUrl = "https://metaerpapi.aideepseek.uk",
    TokenBody = [grant_type="client_credentials", client_id="powerbi_client_2026", client_secret="sec_erp_api_9823472398472938"],
    TokenResponse = Json.Document(Web.Contents(TokenUrl, [RelativePath="api/oauth/token", Content=Text.ToBinary(Uri.BuildQueryString(TokenBody)), Headers=[#"Content-Type"="application/x-www-form-urlencoded"]])),
    AccessToken = Text.Trim(Text.Clean(Text.Replace(Text.Replace(Text.From(TokenResponse[access_token]), "#(cr)", ""), "#(lf)", ""))),
    Source = Json.Document(Web.Contents("https://metaerpapi.aideepseek.uk", [RelativePath="api/powerbi/products", Headers=[#"Authorization"="Bearer " & AccessToken, #"Accept"="application/json"]])),
    TableFromList = Table.FromList(Source, Splitter.SplitByNothing(), null, null, ExtraValues.Error),
    ExpandedColumns = Table.ExpandRecordColumn(TableFromList, "Column1", Record.FieldNames(Source{0}))
in
    ExpandedColumns
```

---

### Query 7: Companies Master (`api/powerbi/companies`)
*Fetches all companies with Company ID and Company Name.*

```powerquery
let
    TokenUrl = "https://metaerpapi.aideepseek.uk",
    TokenBody = [grant_type="client_credentials", client_id="powerbi_client_2026", client_secret="sec_erp_api_9823472398472938"],
    TokenResponse = Json.Document(Web.Contents(TokenUrl, [RelativePath="api/oauth/token", Content=Text.ToBinary(Uri.BuildQueryString(TokenBody)), Headers=[#"Content-Type"="application/x-www-form-urlencoded"]])),
    AccessToken = Text.Trim(Text.Clean(Text.Replace(Text.Replace(Text.From(TokenResponse[access_token]), "#(cr)", ""), "#(lf)", ""))),
    Source = Json.Document(Web.Contents("https://metaerpapi.aideepseek.uk", [RelativePath="api/powerbi/companies", Headers=[#"Authorization"="Bearer " & AccessToken, #"Accept"="application/json"]])),
    TableFromList = Table.FromList(Source, Splitter.SplitByNothing(), null, null, ExtraValues.Error),
    ExpandedColumns = Table.ExpandRecordColumn(TableFromList, "Column1", Record.FieldNames(Source{0}))
in
    ExpandedColumns
```

---

### Query 8: Countries Master (`api/powerbi/countries`)
*Fetches all countries with Country ID, Country Name, Code, and Currency.*

```powerquery
let
    TokenUrl = "https://metaerpapi.aideepseek.uk",
    TokenBody = [grant_type="client_credentials", client_id="powerbi_client_2026", client_secret="sec_erp_api_9823472398472938"],
    TokenResponse = Json.Document(Web.Contents(TokenUrl, [RelativePath="api/oauth/token", Content=Text.ToBinary(Uri.BuildQueryString(TokenBody)), Headers=[#"Content-Type"="application/x-www-form-urlencoded"]])),
    AccessToken = Text.Trim(Text.Clean(Text.Replace(Text.Replace(Text.From(TokenResponse[access_token]), "#(cr)", ""), "#(lf)", ""))),
    Source = Json.Document(Web.Contents("https://metaerpapi.aideepseek.uk", [RelativePath="api/powerbi/countries", Headers=[#"Authorization"="Bearer " & AccessToken, #"Accept"="application/json"]])),
    TableFromList = Table.FromList(Source, Splitter.SplitByNothing(), null, null, ExtraValues.Error),
    ExpandedColumns = Table.ExpandRecordColumn(TableFromList, "Column1", Record.FieldNames(Source{0}))
in
    ExpandedColumns
```
