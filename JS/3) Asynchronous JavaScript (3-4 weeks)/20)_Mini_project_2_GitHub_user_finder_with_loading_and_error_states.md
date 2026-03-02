# Mini Project 2: GitHub User Finder with Loading and Error States

## Goal
Practice asynchronous UI state machine with robust API handling.

## Features
- Search GitHub username.
- Show profile info: avatar, login, bio, repos, followers.
- Loading skeleton/spinner.
- Error state for not found, rate limit, network issues.

## API Endpoint
`https://api.github.com/users/{username}`

## Required State Model
- `idle`
- `loading`
- `success`
- `error`

## Implementation Notes
- Cancel stale request when query changes quickly.
- Branch by status code (`404`, `403`, others).
- Keep view rendering separate from data fetching.

## Extensions
- Fetch public repos list.
- Debounced search input.
- Cache recent profiles in memory.

## Done Criteria
- Correct loading/error/success transitions.
- No stale response overwrites latest query.
- Readable async architecture.
