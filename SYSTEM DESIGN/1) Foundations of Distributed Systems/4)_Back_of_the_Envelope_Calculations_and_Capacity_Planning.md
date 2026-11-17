# 4) Back-of-the-Envelope Calculations & Capacity Planning

## Executive Overview
Back-of-the-envelope estimation is the art of approximating traffic volume, storage capacity, bandwidth requirements, and memory caching sizes within 3–5 minutes. It turns abstract requirements into concrete engineering dimensions.

---

## 1. Golden Reference Multipliers & Powers of Two

```text
┌───────────┬──────────────┬───────────────┬────────────────────────┐
│ Power     │ Exact Value  │ Approximation │ Common Prefix          │
├───────────┼──────────────┼───────────────┼────────────────────────┤
│ 2^10      │ 1,024        │ ~ 1 Thousand  │ 1 KB (Kilobyte)        │
│ 2^20      │ 1,048,576    │ ~ 1 Million   │ 1 MB (Megabyte)        │
│ 2^30      │ 1,073,741,824│ ~ 1 Billion   │ 1 GB (Gigabyte)        │
│ 2^40      │ 1.099 × 10^12│ ~ 1 Trillion  │ 1 TB (Terabyte)        │
│ 2^50      │ 1.125 × 10^15│ ~ 1 Quadrillion│ 1 PB (Petabyte)       │
└───────────┴──────────────┴───────────────┴────────────────────────┘
```

### Time Constants:
- 1 Day = $24 \text{ hours} \times 60 \text{ minutes} \times 60 \text{ seconds} = \mathbf{86,400\text{ seconds}}$.
- **Mental Math Trick**: Round $86,400 \approx \mathbf{100,000\text{ seconds}}$ to quickly estimate daily rates.
- $1\text{ Million requests/day} \approx \frac{1,000,000}{100,000} = \mathbf{10\text{ requests/sec (QPS)}}$.

---

## 2. Step-by-Step Capacity Estimation Template

### Scenario: Design a Twitter/X-Scale Photo & Status Sharing Platform
- **Active Users**: 200 Million Daily Active Users (DAU).
- **Posting Volume**: Each user posts 2 tweets/day on average.
- **Media Ratio**: 10% of tweets contain an image (average image size: 200 KB).
- **Text Tweet Size**: 250 Bytes of metadata + text.
- **Read-to-Write Ratio**: 50:1 (Users read 100 tweets per day).

---

### Step 1: Throughput (QPS) Calculations

1. **Write QPS (Tweets Created)**:
   $$\text{Total Daily Tweets} = 200\text{ Million DAU} \times 2\text{ tweets} = 400\text{ Million tweets/day}$$
   $$\text{Average Write QPS} = \frac{400,000,000}{86,400\text{ s}} \approx \mathbf{4,630\text{ writes/sec}}$$
   $$\text{Peak Write QPS} = \text{Average QPS} \times 2 = \mathbf{9,260\text{ writes/sec}}$$

2. **Read QPS (Timeline Views)**:
   $$\text{Total Daily Reads} = 400\text{M} \times 50 = 20\text{ Billion reads/day}$$
   $$\text{Average Read QPS} = \frac{20,000,000,000}{86,400\text{ s}} \approx \mathbf{231,500\text{ reads/sec}}$$
   $$\text{Peak Read QPS} = 231,500 \times 2 \approx \mathbf{463,000\text{ reads/sec}}$$

---

### Step 2: Storage Requirements (5-Year Capacity)

1. **Daily Text Storage**:
   $$400\text{M tweets} \times 250\text{ Bytes} = 100\text{ GB/day}$$

2. **Daily Media Storage**:
   $$400\text{M tweets} \times 10\% = 40\text{ Million photos/day}$$
   $$40\text{M photos} \times 200\text{ KB} = 8,000\text{ GB/day} = \mathbf{8\text{ TB/day}}$$

3. **Total Daily Storage**:
   $$\text{Total Daily} \approx 8.1\text{ TB/day}$$

4. **5-Year Storage Capacity**:
   $$\text{5-Year Raw Storage} = 8.1\text{ TB/day} \times 365\text{ days} \times 5\text{ years} \approx \mathbf{14.8\text{ Petabytes (PB)}}$$
   - *With $3\times$ replication for disaster recovery*: $14.8 \times 3 \approx \mathbf{44.4\text{ PB}}$.

---

### Step 3: Network Bandwidth Requirements

1. **Ingress (Incoming Traffic)**:
   $$\text{Daily Ingress} = 8.1\text{ TB/day}$$
   $$\text{Ingress Bandwidth} = \frac{8.1\text{ TB} \times 8\text{ bits}}{86,400\text{ s}} \approx \frac{64.8\text{ Terabits}}{86,400} \approx \mathbf{750\text{ Mbps (Megabits/sec)}}$$

2. **Egress (Outgoing Reads)**:
   $$\text{Read Bandwidth} = \text{Ingress} \times 50 = 750\text{ Mbps} \times 50 \approx \mathbf{37.5\text{ Gbps (Gigabits/sec)}}$$

---

### Step 4: Cache Memory Estimation (The 80/20 Pareto Rule)
- The **80/20 Rule** states that **20% of the content generates 80% of read traffic**.
- We should cache 20% of daily read requests in distributed RAM (Redis):
  $$\text{Daily Read Data Volume} = 8.1\text{ TB/day}$$
  $$\text{RAM Required for 20\% Cache} = 8.1\text{ TB} \times 0.20 = \mathbf{1.62\text{ TB of RAM}}$$
- If a high-memory Redis node has 64 GB of usable RAM:
  $$\text{Number of Redis Cache Nodes} = \frac{1,620\text{ GB}}{64\text{ GB}} \approx \mathbf{26\text{ cache nodes}}$$
