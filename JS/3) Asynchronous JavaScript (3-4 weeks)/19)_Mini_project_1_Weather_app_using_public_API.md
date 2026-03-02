# Mini Project 1: Weather App Using Public API

## Goal
Build API-driven app with loading states, error handling, retries, and safe rendering.

## Features
- Search by city.
- Current weather display.
- Optional forecast panel.
- Loading and error states.
- Cancel previous request on rapid input.

## Suggested Structure
```text
weather-app/
  index.html
  style.css
  script.js
  api.js
  ui.js
```

## Core Flow
1. User enters city.
2. Validate input.
3. Fetch weather API.
4. Parse + validate response.
5. Render UI states.

## Reliability Requirements
- `res.ok` check mandatory.
- Timeout and abort support.
- Friendly fallback message on failures.

## Extensions
- Unit toggle (C/F).
- Last searched city storage.
- Retry button.

## Done Criteria
- Works for valid/invalid city.
- Handles slow network and cancellation.
- Clean async code separation.
