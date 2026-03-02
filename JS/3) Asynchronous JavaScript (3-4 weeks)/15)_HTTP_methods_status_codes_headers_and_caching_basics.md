# HTTP Methods, Status Codes, Headers, and Caching Basics

## 1) Common HTTP Methods
- `GET`: read
- `POST`: create
- `PUT`: replace
- `PATCH`: partial update
- `DELETE`: remove

## 2) Important Status Codes
- `200 OK`
- `201 Created`
- `204 No Content`
- `400 Bad Request`
- `401 Unauthorized`
- `403 Forbidden`
- `404 Not Found`
- `409 Conflict`
- `429 Too Many Requests`
- `500 Internal Server Error`

## 3) Key Headers
- `Content-Type`
- `Accept`
- `Authorization`
- `Cache-Control`
- `ETag`
- `Retry-After`

## 4) Caching Basics
- Strong cache with max-age.
- Revalidation via ETag/If-None-Match.
- Avoid stale critical data with correct cache policies.

## 5) Quick Practice
1. Map each method to CRUD action.
2. Handle response branches by status code.
3. Inspect headers in browser devtools.
