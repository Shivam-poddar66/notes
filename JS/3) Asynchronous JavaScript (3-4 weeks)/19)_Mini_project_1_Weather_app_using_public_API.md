# Mini Project 1: Production Weather Application

A complete, production-grade architectural implementation of a real-world asynchronous application using modern public weather APIs (e.g. Open-Meteo or WeatherAPI). This project synthesizes **Fetch API**, **AbortController race-condition cancellation**, **Exponential Backoff Retries**, **Runtime Schema Validation**, and a **Clean 4-State UI Lifecycle Engine**.

---

## 1. System Architecture & Component Design

```
                                  [ User Input / City Search ]
                                               |
                                               v
+----------------------------------------------------------------------------------------------+
| app.js (Controller / State Orchestrator)                                                     |
|  - Cancels prior in-flight AbortController                                                   |
|  - Manages 4 UI States: IDLE -> LOADING -> SUCCESS / ERROR                                  |
|  - Manages LocalStorage History & Unit Conversions (Celsius/Fahrenheit)                      |
+----------------------------------------------------------------------------------------------+
                                |                              |
                                v                              v
+----------------------------------------------------+   +------------------------------------+
| api.js (Network & Data Layer)                      |   | ui.js (DOM View Rendering Layer)   |
|  - Geocoding Resolution API                        |   |  - Loading Skeleton Animation      |
|  - Weather Forecast API                            |   |  - Weather Card & Metrics View     |
|  - Auto-Retry with Jitter & Per-Request Timeout    |   |  - User-Friendly Error Component   |
|  - Schema Validation & DTO Normalization           |   |  - Search History Pills            |
+----------------------------------------------------+   +------------------------------------+
```

---

## 2. Directory Structure

```text
weather-app/
├── index.html        # Clean semantic HTML5 layout
├── styles.css        # Responsive CSS Grid/Flexbox styling + Skeleton animations
├── api.js            # Network client, AbortController, retries, and data normalizers
├── ui.js             # Pure DOM mutation & state rendering helpers
└── app.js            # Main application bootstrap, state management, and event handling
```

---

## 3. Production Source Code

### A. `index.html` (Semantic Layout)

```html
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Apex Weather — Enterprise Async App</title>
  <link rel="stylesheet" href="styles.css">
</head>
<body>
  <main class="app-container">
    <header class="app-header">
      <h1>🌦️ Apex Weather</h1>
      <div class="unit-toggle">
        <button id="unitCelsius" class="toggle-btn active">°C</button>
        <button id="unitFahrenheit" class="toggle-btn">°F</button>
      </div>
    </header>

    <form id="searchForm" class="search-form">
      <input 
        type="text" 
        id="cityInput" 
        placeholder="Search city (e.g. London, Tokyo, New York)..." 
        autocomplete="off"
        required
      />
      <button type="submit" id="searchBtn">Search</button>
    </form>

    <section id="historySection" class="history-section"></section>

    <!-- Dynamic Container for 4-State UI -->
    <section id="weatherDisplay" class="weather-display">
      <div class="state-idle">
        <p>🔍 Enter a city name above to view current weather and forecast.</p>
      </div>
    </section>
  </main>

  <script type="module" src="app.js"></script>
</body>
</html>
```

---

### B. `api.js` (Network Layer, AbortController & Validation)

```javascript
/**
 * API Service Layer: Open-Meteo Free Public Geocoding & Forecast APIs
 * Zero API Keys required, high reliability
 */

export class ApiError extends Error {
  constructor(message, status = 0, isTransient = false) {
    super(message);
    this.name = 'ApiError';
    this.status = status;
    this.isTransient = isTransient;
  }
}

/**
 * Resilient Fetch wrapper with per-request timeout & signal merging
 */
async function fetchWithTimeout(url, { timeoutMs = 6000, signal, ...options } = {}) {
  const timeoutSignal = AbortSignal.timeout(timeoutMs);
  const combinedSignal = signal ? AbortSignal.any([signal, timeoutSignal]) : timeoutSignal;

  try {
    const res = await fetch(url, { ...options, signal: combinedSignal });
    if (!res.ok) {
      const isTransient = res.status >= 500 || res.status === 429;
      throw new ApiError(`Server responded with HTTP ${res.status}`, res.status, isTransient);
    }
    return await res.json();
  } catch (err) {
    if (err.name === 'TimeoutError') {
      throw new ApiError(`Request timed out after ${timeoutMs}ms`, 408, true);
    }
    if (err.name === 'AbortError') {
      throw err; // Allow explicit cancel to propagate cleanly
    }
    if (err instanceof ApiError) throw err;
    throw new ApiError(`Network error: ${err.message}`, 0, true);
  }
}

/**
 * Executes a task with exponential backoff & full jitter
 */
async function withRetry(taskFn, { maxRetries = 2, signal } = {}) {
  let attempt = 0;
  while (attempt <= maxRetries) {
    if (signal?.aborted) throw new DOMException('Aborted', 'AbortError');
    try {
      return await taskFn();
    } catch (err) {
      if (err.name === 'AbortError' || !err.isTransient || attempt >= maxRetries) {
        throw err;
      }
      attempt++;
      const jitterDelay = Math.floor(Math.random() * (200 * Math.pow(2, attempt)));
      await new Promise(r => setTimeout(r, jitterDelay));
    }
  }
}

/**
 * Resolves City Name to Coordinates via Geocoding API
 */
export async function getCoordinates(cityName, signal) {
  const geoUrl = `https://geocoding-api.open-meteo.com/v1/search?name=${encodeURIComponent(cityName)}&count=1&language=en&format=json`;

  const data = await withRetry(
    () => fetchWithTimeout(geoUrl, { signal, timeoutMs: 5000 }),
    { maxRetries: 2, signal }
  );

  if (!data?.results || data.results.length === 0) {
    throw new ApiError(`City "${cityName}" not found. Please check spelling.`, 404, false);
  }

  const { latitude, longitude, name, country } = data.results[0];
  return { latitude, longitude, cityName: name, country };
}

/**
 * Fetches Current Weather & 5-Day Forecast
 */
export async function getWeatherData(latitude, longitude, signal) {
  const weatherUrl = `https://api.open-meteo.com/v1/forecast?latitude=${latitude}&longitude=${longitude}&current=temperature_2m,relative_humidity_2m,apparent_temperature,weather_code,wind_speed_10m&daily=weather_code,temperature_2m_max,temperature_2m_min&timezone=auto`;

  const rawData = await withRetry(
    () => fetchWithTimeout(weatherUrl, { signal, timeoutMs: 7000 }),
    { maxRetries: 2, signal }
  );

  return normalizeWeatherData(rawData);
}

/**
 * Validates and Normalizes raw API response into clean Domain Model
 */
function normalizeWeatherData(raw) {
  if (!raw?.current || !raw?.daily) {
    throw new ApiError('Received invalid data format from weather provider', 502, false);
  }

  const weatherCodeMap = {
    0: { label: 'Clear Sky', icon: '☀️' },
    1: { label: 'Mainly Clear', icon: '🌤️' },
    2: { label: 'Partly Cloudy', icon: '⛅' },
    3: { label: 'Overcast', icon: '☁️' },
    45: { label: 'Foggy', icon: '🌫️' },
    51: { label: 'Light Drizzle', icon: '🌦️' },
    61: { label: 'Rain', icon: '🌧️' },
    71: { label: 'Snowfall', icon: '🌨️' },
    95: { label: 'Thunderstorm', icon: '⛈️' }
  };

  const codeInfo = weatherCodeMap[raw.current.weather_code] || { label: 'Moderate Weather', icon: '🌤️' };

  return {
    current: {
      tempC: Math.round(raw.current.temperature_2m),
      feelsLikeC: Math.round(raw.current.apparent_temperature),
      humidity: raw.current.relative_humidity_2m,
      windKmh: Math.round(raw.current.wind_speed_10m),
      condition: codeInfo.label,
      icon: codeInfo.icon
    },
    forecast: raw.daily.time.slice(0, 5).map((dateStr, idx) => ({
      date: new Date(dateStr).toLocaleDateString('en-US', { weekday: 'short' }),
      maxTempC: Math.round(raw.daily.temperature_2m_max[idx]),
      minTempC: Math.round(raw.daily.temperature_2m_min[idx]),
      icon: (weatherCodeMap[raw.daily.weather_code[idx]] || { icon: '🌤️' }).icon
    }))
  };
}
```

---

### C. `ui.js` (DOM Rendering & State View Engine)

```javascript
/**
 * UI Rendering Helpers
 */

export function renderLoading(container, cityName) {
  container.innerHTML = `
    <div class="weather-skeleton">
      <div class="spinner"></div>
      <p>Fetching weather data for <strong>${cityName}</strong>...</p>
    </div>
  `;
}

export function renderError(container, errorMessage, onRetry) {
  container.innerHTML = `
    <div class="error-card">
      <span class="error-icon">⚠️</span>
      <h3>Unable to load weather</h3>
      <p>${errorMessage}</p>
      ${onRetry ? `<button id="retryBtn" class="retry-btn">Try Again</button>` : ''}
    </div>
  `;

  if (onRetry) {
    container.querySelector('#retryBtn')?.addEventListener('click', onRetry);
  }
}

export function renderWeather(container, data, location, unit = 'C') {
  const toDisplayTemp = (celsius) => unit === 'C' ? `${celsius}°C` : `${Math.round((celsius * 9/5) + 32)}°F`;

  const { current, forecast } = data;

  container.innerHTML = `
    <div class="weather-card">
      <div class="location-header">
        <h2>${location.cityName}, ${location.country}</h2>
        <span class="weather-icon-large">${current.icon}</span>
      </div>

      <div class="current-stats">
        <div class="main-temp">${toDisplayTemp(current.tempC)}</div>
        <div class="condition-label">${current.condition}</div>
        <div class="feels-like">Feels like ${toDisplayTemp(current.feelsLikeC)}</div>
      </div>

      <div class="meta-grid">
        <div class="meta-item">
          <span class="meta-title">Humidity</span>
          <span class="meta-val">${current.humidity}%</span>
        </div>
        <div class="meta-item">
          <span class="meta-title">Wind Speed</span>
          <span class="meta-val">${current.windKmh} km/h</span>
        </div>
      </div>

      <h4 class="forecast-title">5-Day Forecast</h4>
      <div class="forecast-row">
        ${forecast.map(day => `
          <div class="forecast-day">
            <span class="forecast-name">${day.date}</span>
            <span class="forecast-icon">${day.icon}</span>
            <span class="forecast-temp">${toDisplayTemp(day.maxTempC)} / ${toDisplayTemp(day.minTempC)}</span>
          </div>
        `).join('')}
      </div>
    </div>
  `;
}

export function renderSearchHistory(container, historyList, onSelect) {
  if (!historyList.length) {
    container.innerHTML = '';
    return;
  }

  container.innerHTML = `
    <div class="history-pills">
      <span class="history-label">Recent:</span>
      ${historyList.map(city => `<button class="history-pill" data-city="${city}">${city}</button>`).join('')}
    </div>
  `;

  container.querySelectorAll('.history-pill').forEach(btn => {
    btn.addEventListener('click', () => onSelect(btn.dataset.city));
  });
}
```

---

### D. `app.js` (Main Controller & State Coordinator)

```javascript
import { getCoordinates, getWeatherData, ApiError } from './api.js';
import { renderLoading, renderError, renderWeather, renderSearchHistory } from './ui.js';

class WeatherApp {
  constructor() {
    this.state = {
      status: 'IDLE', // 'IDLE' | 'LOADING' | 'SUCCESS' | 'ERROR'
      weatherData: null,
      locationData: null,
      activeCity: '',
      unit: 'C',
      history: JSON.parse(localStorage.getItem('weather_history') || '["London", "Tokyo"]')
    };

    this.activeAbortController = null;

    // DOM Elements
    this.form = document.getElementById('searchForm');
    this.cityInput = document.getElementById('cityInput');
    this.displayContainer = document.getElementById('weatherDisplay');
    this.historySection = document.getElementById('historySection');
    this.unitCelsiusBtn = document.getElementById('unitCelsius');
    this.unitFahrenheitBtn = document.getElementById('unitFahrenheit');

    this.init();
  }

  init() {
    this.form.addEventListener('submit', (e) => {
      e.preventDefault();
      const city = this.cityInput.value.trim();
      if (city) this.fetchWeatherForCity(city);
    });

    this.unitCelsiusBtn.addEventListener('click', () => this.setUnit('C'));
    this.unitFahrenheitBtn.addEventListener('click', () => this.setUnit('F'));

    this.renderHistory();

    // Auto-load first history item
    if (this.state.history.length > 0) {
      this.fetchWeatherForCity(this.state.history[0]);
    }
  }

  setUnit(unit) {
    if (this.state.unit === unit) return;
    this.state.unit = unit;
    this.unitCelsiusBtn.classList.toggle('active', unit === 'C');
    this.unitFahrenheitBtn.classList.toggle('active', unit === 'F');

    if (this.state.status === 'SUCCESS' && this.state.weatherData) {
      renderWeather(this.displayContainer, this.state.weatherData, this.state.locationData, this.state.unit);
    }
  }

  async fetchWeatherForCity(cityName) {
    // 1. Cancel previous in-flight request to eliminate race conditions
    if (this.activeAbortController) {
      this.activeAbortController.abort('User initiated new search');
    }

    this.activeAbortController = new AbortController();
    const { signal } = this.activeAbortController;

    this.state.status = 'LOADING';
    this.state.activeCity = cityName;
    renderLoading(this.displayContainer, cityName);

    try {
      // 2. Sequential Orchestration: Get Coords -> Get Weather
      const location = await getCoordinates(cityName, signal);
      const weather = await getWeatherData(location.latitude, location.longitude, signal);

      // 3. Update State on Success
      this.state.status = 'SUCCESS';
      this.state.weatherData = weather;
      this.state.locationData = location;
      this.addToHistory(location.cityName);

      renderWeather(this.displayContainer, weather, location, this.state.unit);

    } catch (err) {
      // 4. Silently ignore intentional user aborts
      if (err.name === 'AbortError' || signal.aborted) {
        console.log(`Search for "${cityName}" was cleanly cancelled.`);
        return;
      }

      // 5. Render Error State with Retry Callback
      this.state.status = 'ERROR';
      const message = err instanceof ApiError ? err.message : 'An unexpected error occurred.';
      renderError(this.displayContainer, message, () => this.fetchWeatherForCity(cityName));
    } finally {
      if (this.activeAbortController?.signal === signal) {
        this.activeAbortController = null;
      }
    }
  }

  addToHistory(cityName) {
    const updated = [cityName, ...this.state.history.filter(c => c.toLowerCase() !== cityName.toLowerCase())].slice(0, 5);
    this.state.history = updated;
    localStorage.setItem('weather_history', JSON.stringify(updated));
    this.renderHistory();
  }

  renderHistory() {
    renderSearchHistory(this.historySection, this.state.history, (city) => {
      this.cityInput.value = city;
      this.fetchWeatherForCity(city);
    });
  }
}

// Bootstrap
document.addEventListener('DOMContentLoaded', () => new WeatherApp());
```

---

## 4. Key Architectural Patterns Practiced

1. **AbortController Request Cancellation**: Eliminates stale UI overwrites when a user searches multiple cities in rapid succession.
2. **Exponential Backoff with Full Jitter**: Automatically survives transient network drops and server 5xx hiccups without pounding APIs.
3. **Strict Domain Normalization**: Separates raw vendor API schema quirks from UI components, making the app immune to backend schema shifts.
4. **4-State UI Lifecycle**: Models `IDLE`, `LOADING`, `SUCCESS`, and `ERROR` explicitly with loading skeletons and recovery retry buttons.
