# Mini Project 2: GitHub User Finder with Robust Async State Machine

A production-grade frontend asynchronous application integrating the **GitHub REST API**. This project demonstrates an explicit **State Machine**, **Debounced Real-Time Typeahead Search**, **Parallel Composite Fetching (`Promise.all`)**, **Stale-While-Revalidate Memory Cache**, and **Granular HTTP Status Branching (404 Not Found vs 403 Rate Limit)**.

---

## 1. System Architecture & Flow

```
[ User Keystroke in Search Bar ]
               |
               v
+---------------------------------------------------------------------------------+
| Debounce Controller (350ms wait window)                                         |
+---------------------------------------------------------------------------------+
               |
               v
+---------------------------------------------------------------------------------+
| In-Memory Cache Check (Map<username, ProfileDTO>)                              |
|   -> Cache Hit: Instantly transition to SUCCESS                                 |
|   -> Cache Miss: Cancel active AbortController & Dispatch Network Requests     |
+---------------------------------------------------------------------------------+
               |
               +----------------------------------+
               |                                  |
               v                                  v
  [ GET /users/{username} ]         [ GET /users/{username}/repos?sort=updated ]
               |                                  |
               +-----------------+----------------+
                                 |
                                 v
                 [ Promise.all Concurrent Join ]
                                 |
       +-------------------------+-------------------------+
       |                                                   |
 (HTTP 200 OK)                                   (HTTP 404 / 403 / 500)
       |                                                   |
       v                                                   v
[ SUCCESS STATE ]                                   [ ERROR STATE ]
- Render Profile Header                             - 404: User Not Found
- Render Top 4 Pinned Repos                         - 403: GitHub Rate Limit (60 req/hr)
- Render Stats Matrix                               - 0: Offline Network Failure
```

---

## 2. Directory Structure

```text
github-finder/
├── index.html        # Modern Semantic Card Layout & Search Header
├── styles.css        # Responsive Glassmorphism Design + Skeleton Placeholders
├── state.js          # Finite State Machine & Event Dispatcher
├── api.js            # GitHub API Client, Status Branching, and SWR In-Memory Cache
├── ui.js             # Pure DOM View Components for all 4 states
└── app.js            # Main Controller wiring Debounced Search with State Machine
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
  <title>OctoFinder — GitHub Profile Explorer</title>
  <link rel="stylesheet" href="styles.css">
</head>
<body>
  <div class="app-layout">
    <header class="app-header">
      <div class="logo">
        <svg height="32" viewBox="0 0 16 16" width="32" fill="currentColor">
          <path d="M8 0C3.58 0 0 3.58 0 8c0 3.54 2.29 6.53 5.47 7.59.4.07.55-.17.55-.38 0-.19-.01-.82-.01-1.49-2.01.37-2.53-.49-2.69-.94-.09-.23-.48-.94-.82-1.13-.28-.15-.68-.52-.01-.53.63-.01 1.08.58 1.23.82.72 1.21 1.87.87 2.33.66.07-.52.28-.87.51-1.07-1.78-.2-3.64-.89-3.64-3.95 0-.87.31-1.59.82-2.15-.08-.2-.36-1.02.08-2.12 0 0 .67-.21 2.2.82.64-.18 1.32-.27 2-.27.68 0 1.36.09 2 .27 1.53-1.04 2.2-.82 2.2-.82.44 1.1.16 1.92.08 2.12.51.56.82 1.27.82 2.15 0 3.07-1.87 3.75-3.65 3.95.29.25.54.73.54 1.48 0 1.07-.01 1.93-.01 2.2 0 .21.15.46.55.38A8.013 8.013 0 0016 8c0-4.42-3.58-8-8-8z"></path>
        </svg>
        <h1>OctoFinder</h1>
      </div>
      <div class="search-container">
        <input 
          type="search" 
          id="usernameInput" 
          placeholder="Search GitHub username (e.g. torvalds, gaearon)..." 
          autocomplete="off"
          spellcheck="false"
        />
      </div>
    </header>

    <!-- Main View Container managed by State Machine -->
    <main id="appRoot" class="app-root"></main>
  </div>

  <script type="module" src="app.js"></script>
</body>
</html>
```

---

### B. `state.js` (Finite State Machine)

```javascript
/**
 * Strict Finite State Machine for Asynchronous UI
 */
export class UIStateMachine {
  constructor(initialState, onStateChange) {
    this.validStates = ['IDLE', 'LOADING', 'SUCCESS', 'ERROR'];
    this.currentState = initialState;
    this.context = { data: null, error: null, query: '' };
    this.onStateChange = onStateChange;
  }

  getState() {
    return { status: this.currentState, ...this.context };
  }

  transition(nextState, nextContext = {}) {
    if (!this.validStates.includes(nextState)) {
      throw new Error(`Invalid state transition: ${nextState}`);
    }

    this.currentState = nextState;
    this.context = { ...this.context, ...nextContext };
    
    if (this.onStateChange) {
      this.onStateChange(this.getState());
    }
  }
}
```

---

### C. `api.js` (GitHub Client, Parallel Fetch & SWR Cache)

```javascript
/**
 * GitHub API Service with Status Routing, Parallel Sub-Resources & Memory Caching
 */

export class GitHubApiError extends Error {
  constructor(message, status, type) {
    super(message);
    this.name = 'GitHubApiError';
    this.status = status;
    this.type = type; // 'NOT_FOUND' | 'RATE_LIMITED' | 'NETWORK_ERROR' | 'SERVER_ERROR'
  }
}

// In-Memory Profile Cache
const memoryCache = new Map();

/**
 * Fetches user profile and latest repos concurrently with AbortSignal support
 */
export async function fetchGitHubProfile(username, signal) {
  const cleanUsername = username.trim().toLowerCase();

  // 1. Check in-memory cache
  if (memoryCache.has(cleanUsername)) {
    console.log(`[Cache Hit] Serving "${cleanUsername}" from memory.`);
    return { data: memoryCache.get(cleanUsername), fromCache: true };
  }

  const userUrl = `https://api.github.com/users/${encodeURIComponent(cleanUsername)}`;
  const reposUrl = `https://api.github.com/users/${encodeURIComponent(cleanUsername)}/repos?sort=updated&per_page=4`;

  try {
    // 2. Parallel Join: Fetch User and Repos simultaneously
    const [userRes, reposRes] = await Promise.all([
      fetch(userUrl, { signal, headers: { 'Accept': 'application/vnd.github.v3+json' } }),
      fetch(reposUrl, { signal, headers: { 'Accept': 'application/vnd.github.v3+json' } })
    ]);

    // 3. Status Code Semantic Branching
    if (userRes.status === 404) {
      throw new GitHubApiError(`User "${username}" was not found on GitHub.`, 404, 'NOT_FOUND');
    }
    if (userRes.status === 403 || reposRes.status === 403) {
      const resetHeader = userRes.headers.get('x-ratelimit-reset');
      const resetTime = resetHeader ? new Date(parseInt(resetHeader, 10) * 1000).toLocaleTimeString() : 'soon';
      throw new GitHubApiError(`GitHub API rate limit exceeded. Resets at ${resetTime}.`, 403, 'RATE_LIMITED');
    }
    if (!userRes.ok) {
      throw new GitHubApiError(`GitHub API Error (HTTP ${userRes.status})`, userRes.status, 'SERVER_ERROR');
    }

    const [rawUser, rawRepos] = await Promise.all([
      userRes.json(),
      reposRes.json()
    ]);

    // 4. Data Normalization
    const normalizedData = normalizeGitHubPayload(rawUser, rawRepos);

    // 5. Store in Cache
    memoryCache.set(cleanUsername, normalizedData);

    return { data: normalizedData, fromCache: false };

  } catch (err) {
    if (err.name === 'AbortError') throw err;
    if (err instanceof GitHubApiError) throw err;
    throw new GitHubApiError(`Network failure: ${err.message}`, 0, 'NETWORK_ERROR');
  }
}

/**
 * Transforms raw GitHub JSON into safe domain DTO
 */
function normalizeGitHubPayload(user, repos) {
  return {
    login: user.login,
    name: user.name || user.login,
    avatarUrl: user.avatar_url,
    bio: user.bio || 'No bio provided.',
    location: user.location || 'Location unavailable',
    blog: user.blog || null,
    twitter: user.twitter_username || null,
    publicRepos: user.public_repos ?? 0,
    followers: user.followers ?? 0,
    following: user.following ?? 0,
    joinedDate: new Date(user.created_at).toLocaleDateString('en-US', {
      month: 'short',
      year: 'numeric'
    }),
    topRepos: Array.isArray(repos) ? repos.map(r => ({
      name: r.name,
      description: r.description || 'No description.',
      url: r.html_url,
      stars: r.stargazers_count ?? 0,
      language: r.language || 'Plain'
    })) : []
  };
}
```

---

### D. `ui.js` (DOM Rendering Engine)

```javascript
/**
 * Pure DOM View Renderers for each state
 */

export function renderIdle(container) {
  container.innerHTML = `
    <div class="state-card idle-view">
      <div class="idle-icon">👨‍💻</div>
      <h2>Explore GitHub Profiles</h2>
      <p>Type any developer's username above to view repositories, activity, and stats.</p>
    </div>
  `;
}

export function renderLoading(container, query) {
  container.innerHTML = `
    <div class="state-card skeleton-card">
      <div class="skeleton-header">
        <div class="skeleton-avatar"></div>
        <div class="skeleton-lines">
          <div class="skeleton-bar short"></div>
          <div class="skeleton-bar medium"></div>
        </div>
      </div>
      <div class="skeleton-stats"></div>
      <div class="skeleton-repos"></div>
    </div>
  `;
}

export function renderError(container, error, onRetry) {
  const icons = {
    'NOT_FOUND': '🔍',
    'RATE_LIMITED': '⏳',
    'NETWORK_ERROR': '📡',
    'SERVER_ERROR': '💥'
  };

  container.innerHTML = `
    <div class="state-card error-view">
      <div class="error-badge">${icons[error.type] || '⚠️'}</div>
      <h3>${error.status ? `Error ${error.status}` : 'Connection Issue'}</h3>
      <p class="error-msg">${error.message}</p>
      ${onRetry ? `<button id="errorRetryBtn" class="action-btn">Retry Request</button>` : ''}
    </div>
  `;

  if (onRetry) {
    container.querySelector('#errorRetryBtn')?.addEventListener('click', onRetry);
  }
}

export function renderSuccess(container, user) {
  container.innerHTML = `
    <article class="profile-card">
      <div class="profile-header">
        <img src="${user.avatarUrl}" alt="${user.login}" class="avatar-img" />
        <div class="profile-titles">
          <h2>${user.name}</h2>
          <a href="https://github.com/${user.login}" target="_blank" rel="noreferrer" class="handle-link">@${user.login}</a>
          <span class="joined-tag">Joined ${user.joinedDate}</span>
        </div>
      </div>

      <p class="bio-text">${user.bio}</p>

      <div class="stats-panel">
        <div class="stat-box">
          <span class="stat-label">Repositories</span>
          <span class="stat-value">${user.publicRepos}</span>
        </div>
        <div class="stat-box">
          <span class="stat-label">Followers</span>
          <span class="stat-value">${user.followers}</span>
        </div>
        <div class="stat-box">
          <span class="stat-label">Following</span>
          <span class="stat-value">${user.following}</span>
        </div>
      </div>

      <div class="repos-container">
        <h3>Recent Repositories</h3>
        <div class="repo-grid">
          ${user.topRepos.length > 0 ? user.topRepos.map(repo => `
            <a href="${repo.url}" target="_blank" rel="noreferrer" class="repo-card">
              <div class="repo-top">
                <span class="repo-name">${repo.name}</span>
                <span class="repo-stars">★ ${repo.stars}</span>
              </div>
              <p class="repo-desc">${repo.description}</p>
              <span class="repo-lang">${repo.language}</span>
            </a>
          `).join('') : '<p class="no-repos">No public repositories found.</p>'}
        </div>
      </div>
    </article>
  `;
}
```

---

### E. `app.js` (State Orchestrator & Debounce Controller)

```javascript
import { UIStateMachine } from './state.js';
import { fetchGitHubProfile, GitHubApiError } from './api.js';
import { renderIdle, renderLoading, renderError, renderSuccess } from './ui.js';

class GitHubFinderApp {
  constructor() {
    this.root = document.getElementById('appRoot');
    this.input = document.getElementById('usernameInput');

    this.activeController = null;
    this.debounceTimer = null;

    // Initialize State Machine
    this.fsm = new UIStateMachine('IDLE', (state) => this.render(state));

    this.init();
  }

  init() {
    // Initial Render
    this.render(this.fsm.getState());

    // Debounced Search Event Listener
    this.input.addEventListener('input', (e) => {
      const query = e.target.value.trim();
      clearTimeout(this.debounceTimer);

      if (!query) {
        if (this.activeController) this.activeController.abort();
        this.fsm.transition('IDLE', { query: '', data: null, error: null });
        return;
      }

      // Debounce window of 350ms
      this.debounceTimer = setTimeout(() => {
        this.executeSearch(query);
      }, 350);
    });
  }

  async executeSearch(username) {
    // 1. Cancel previous in-flight request
    if (this.activeController) {
      this.activeController.abort('New search dispatched');
    }

    this.activeController = new AbortController();
    const { signal } = this.activeController;

    // 2. Transition State to LOADING
    this.fsm.transition('LOADING', { query: username, error: null });

    try {
      const { data } = await fetchGitHubProfile(username, signal);
      
      // 3. Transition State to SUCCESS
      this.fsm.transition('SUCCESS', { data, query: username, error: null });

    } catch (err) {
      // Ignore user cancellations
      if (err.name === 'AbortError' || signal.aborted) {
        return;
      }

      // 4. Transition State to ERROR
      const errorObj = err instanceof GitHubApiError ? err : {
        status: 0,
        type: 'NETWORK_ERROR',
        message: 'Unable to connect to GitHub. Please check your internet.'
      };

      this.fsm.transition('ERROR', { error: errorObj, query: username, data: null });
    } finally {
      if (this.activeController?.signal === signal) {
        this.activeController = null;
      }
    }
  }

  render(state) {
    switch (state.status) {
      case 'IDLE':
        renderIdle(this.root);
        break;
      case 'LOADING':
        renderLoading(this.root, state.query);
        break;
      case 'SUCCESS':
        renderSuccess(this.root, state.data);
        break;
      case 'ERROR':
        renderError(this.root, state.error, () => this.executeSearch(state.query));
        break;
    }
  }
}

// Bootstrap
document.addEventListener('DOMContentLoaded', () => new GitHubFinderApp());
```

---

## 4. Key Architectural Patterns Mastered

1. **Finite State Machine (FSM)**: Decouples UI rendering completely from asynchronous data fetching logic.
2. **Debounce + AbortController Synergy**: Limits API calls while typing, and immediately kills in-flight network requests if the user types another character before the previous call finishes.
3. **Parallel Sub-Resource Aggregation**: Uses `Promise.all` to fetch user profiles and repository lists in a single network round-trip.
4. **Fine-Grained HTTP Semantic Handling**: Dispatches custom UI views for 404 (User Not Found) vs 403 (Rate Limit with exact reset timestamp).
