# Request/Response Lifecycle End to End

## 1) Lifecycle Steps
1. Client builds request.
2. DNS/TCP/TLS connection occurs.
3. Server receives and processes request.
4. Server sends status, headers, body.
5. Client parses response and updates UI/state.

## 2) Request Components
- URL
- method
- headers
- body
- credentials/cookies policy

## 3) Response Components
- status code
- status text
- headers
- body stream/payload

## 4) Timing Considerations
- network latency
- server processing time
- payload size
- parsing/rendering overhead

## 5) Reliability Layer
- timeout
- retry (when safe)
- cancellation on page change
- clear error messaging

## 6) Quick Practice
1. Log each lifecycle stage for one API call.
2. Add request timing measurement.
3. Show user-friendly states: loading/success/error.
