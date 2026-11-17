# 4) Case Study 3: Scalable Notification System

## 1. Requirements & Scope
- **Functional**: Support multi-channel dispatch: iOS Push (Apple APNS), Android Push (Firebase Cloud Messaging - FCM), SMS (Twilio), and Email (SendGrid/SES); User preference settings (channel opt-outs, Do Not Disturb quiet hours); Template management.
- **Scale**: 10 Million notifications sent daily; Peak bursts up to 20,000 notifications/sec (e.g., breaking news alerts).

---

## 2. High-Level Architecture Diagram

```text
[ Internal Services ] (Order, Billing, Marketing)
        │
   POST /v1/notifications
        ▼
[ Notification Gateway ] (Authentication, Input Validation, Rate Limiting)
        │
        ├── 1. Check User Preferences & DND (Redis Cache)
        │
        ▼ 2. Publish to Dedicated Kafka Topics
[ Apache Kafka Cluster ]
   ├── Topic: `notify.push`   (12 Partitions)
   ├── Topic: `notify.email`  (8 Partitions)
   └── Topic: `notify.sms`    (4 Partitions)
        │
        ├──────────────────────────┼──────────────────────────┐
        ▼                          ▼                          ▼
 [ Push Workers ]          [ Email Workers ]          [ SMS Workers ]
        │                          │                          │
        ▼                          ▼                          ▼
[ Apple APNS / FCM ]      [ SendGrid / SES ]       [ Twilio / Infobip ]
        │
        ▼ (Failed / Rate limited deliveries)
[ Dead Letter Queue (DLQ) ] ──▶ [ Retry Worker with Exponential Backoff ]
```

---

## 3. Core Architectural Strategies

### A. Dedicated Queues per Channel
Never mix Email, SMS, and Push notifications in a single shared queue!
- An Apple APNS push notification takes **10 ms** to send over HTTP/2.
- A SendGrid email with HTML attachments can take **1,500 ms** to transmit.
- Mixing them causes slow email dispatches to block high-priority push notifications (Head-of-Line blocking).

### B. User Opt-Out & Quiet Hours (Do Not Disturb - DND)
- Store user preference flags in **Redis Hashes**:
  `HGET user:100:prefs push_enabled`
- Check timezone: If user local time is between 22:00 and 08:00, defer non-critical marketing notifications by rescheduling them into a delayed message topic.

### C. Rate Limiting Third-Party Service Providers
- Twilio and SendGrid enforce strict outbound API throughput limits (e.g., max 100 SMS/sec per shortcode).
- Use a **Leaky Bucket** rate limiter in front of outbound worker threads to smooth API calls and avoid being throttled with HTTP 429 by external vendors.
