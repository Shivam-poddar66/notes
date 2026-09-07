# React Mastery Roadmap (Complete 2026 Edition)

A battle-tested, structured roadmap to master **React** from foundational mechanics to senior/staff-level architecture. Covers React 18 & React 19 features (React Compiler, Server Components, Server Actions, New Hooks), performance profiling, ecosystem tools, testing, and production-grade full-stack architecture.

---

## How to Use This Roadmap

- **Target Schedule**: 10–15 hours/week over 16–24 weeks.
- **Build While Learning**: Do not just read documentation; write code daily and complete every milestone project.
- **Maintain a Learning Repo**: Store mini-projects, custom hook libraries, and notes in a dedicated GitHub repository (`react-mastery-lab`).
- **Focus on Mechanics First**: Understand *why* React behaves the way it does (rendering lifecycle, immutability, Fiber reconciler) before reaching for heavy libraries.

---

## Mastery Definition

You are a **Master React Engineer** when you can:

1. **Predict Renders Intuitively**: Explain exactly when and why any component renders, re-renders, or mounts/unmounts.
2. **Architect Scalable Applications**: Structure state, components, and data fetching to scale cleanly across 100+ routes and multiple team members.
3. **Master Modern Paradigms**: Confidently decide when to use Client Components vs. React Server Components (RSC) and Server Actions.
4. **Optimize Performance Precisely**: Profile and resolve frame drops, memory leaks, slow renders, and network bottlenecks using React DevTools and Web Vitals metrics.
5. **Write Enterprise Tests**: Write resilient tests (RTL + Vitest + MSW + Playwright) that test application behavior rather than implementation details.

---

## Roadmap Overview

```
Phase 0: Prerequisites (JS/TS & Build Tooling)
   │
   ▼
Phase 1: React Core Mechanics & JSX
   │
   ▼
Phase 2: Hooks Deep Dive (Core, Advanced & React 19 Hooks)
   │
   ▼
Phase 3: Component Architecture & Design Patterns
   │
   ▼
Phase 4: State Management & Data Fetching Ecosystem
   │
   ▼
Phase 5: Routing, SSR, SSG & React Server Components (RSC)
   │
   ▼
Phase 6: React Internals, Fiber, Compiler & Performance
   │
   ▼
Phase 7: Testing, Quality Assurance & Security
   │
   ▼
Phase 8: Production Full-Stack Capstone Projects
   │
   ▼
Phase 9: Senior/Staff Frontend System Design & Enterprise Scale
   │
   ▼
Phase 10: Technical Interview & Code Challenge Mastery
```

---

## Phase 0: Prerequisites & Tooling Foundation (1–2 Weeks)

### Modern JavaScript (ES6+) Core
- [ ] Arrow functions, lexical `this`, implicit returns.
- [ ] Destructuring (arrays & objects), default parameters, rest/spread operators.
- [ ] Array immutability methods: `map`, `filter`, `reduce`, `find`, `some`, `every`, `toSorted`, `toSpliced`, `toReversed`.
- [ ] Modules: ES Modules (`import` / `export`), named vs. default exports.
- [ ] Asynchronous JS: Promises, `async/await`, `try/catch/finally`, `Promise.all`, `Promise.allSettled`.
- [ ] Closures, Lexical Scope, Scope Chain, Variable Hoisting.

### TypeScript for React Mastery
- [ ] Primitive types, Object types, Interfaces vs Type Aliases.
- [ ] Typing Component Props (`React.FC`, explicit interface props, `React.ReactNode`, `React.PropsWithChildren`).
- [ ] Typing Events (`React.ChangeEvent<HTMLInputElement>`, `React.FormEvent<HTMLFormElement>`, `React.MouseEvent<HTMLButtonElement>`).
- [ ] Typing Hooks (`useState<User | null>`, `useRef<HTMLInputElement>(null)`).
- [ ] Generics in React components and custom hooks (`<T,>`).
- [ ] Discriminated Unions for complex component variants & state unions.
- [ ] Utility types: `Partial`, `Required`, `Pick`, `Omit`, `Record`, `Extract`, `ReturnType`.

### Modern Build & Tooling Setup
- [ ] Node.js (LTS), Package Managers (`npm`, `pnpm`, `bun`).
- [ ] Build Tools: **Vite** (ESBuild/Rollup architecture, HMR configuration).
- [ ] Code Quality: **ESLint** (Flat Config, `@typescript-eslint`, `eslint-plugin-react-hooks`), **Prettier**.

### Phase 0 Milestone
- Set up a TypeScript Vite React project from scratch with customized ESLint, Prettier, and path aliases (`@/`).

---

## Phase 1: React Core Mechanics & JSX (2–3 Weeks)

### JSX & Rendering Mechanics
- [ ] What is JSX? How JSX compiles to `React.createElement` / `jsx-runtime`.
- [ ] Embedding expressions, conditional rendering (`&&`, ternary `?:`, guard clauses).
- [ ] Rendering lists, the role of `key` props (reconciliation, DOM stability, list reordering bugs).
- [ ] Pure components & Immutability: Why React components must be pure functions.

### Components & Props
- [ ] Functional Components architecture.
- [ ] Props, prop drilling, default props, object destructuring.
- [ ] Children prop (`children`), composition over inheritance.
- [ ] Render props pattern & Function-as-Child.

### State & Event Handling
- [ ] Local state with `useState`.
- [ ] Asynchronous state updates & batching (React 18 automatic batching).
- [ ] Updating object and array state immutably (immer.js awareness vs spread syntax).
- [ ] Synthetic Event System: Event delegation, bubbling, `e.preventDefault()`, `e.stopPropagation()`.
- [ ] Controlled vs. Uncontrolled components (`value` + `onChange` vs. `defaultValue` + `ref`).

### Phase 1 Milestone Projects
- **Task Manager App**: Filterable, searchable, local-storage-persisted task manager with nested categories and tag support.
- **Interactive Quiz Engine**: Multi-step quiz application with instant scoring, timer, and state breakdown.

---

## Phase 2: Hooks Deep Dive & React 19 Hooks (3–4 Weeks)

### Core Hooks
- [ ] `useState`: Lazy initialization (`useState(() => heavyComputation())`), functional updates (`setCount(prev => prev + 1)`).
- [ ] `useEffect`: Dependency array rules, cleanup functions, race conditions, avoiding synchronization anti-patterns.
- [ ] `useContext`: Creating, consuming, and splitting contexts to prevent unnecessary re-renders.
- [ ] `useRef`: DOM references, storing persistent mutable values without triggering re-renders.
- [ ] `useReducer`: Managing complex state logic, action dispatching, comparison with Redux patterns.

### Optimization & Advanced Hooks
- [ ] `useMemo`: Memoizing expensive calculations.
- [ ] `useCallback`: Preserving referential equality of callbacks passed to memoized children.
- [ ] `useTransition`: Marking non-urgent updates, keeping UI responsive during heavy renders.
- [ ] `useDeferredValue`: Deferring updates to non-critical UI sections.
- [ ] `useLayoutEffect` vs `useEffect`: Synchronous DOM measurements before paint.
- [ ] `useImperativeHandle`: Exposing custom imperative handle methods via `forwardRef`.
- [ ] `useId`: Generating unique accessible IDs for SSR and client markup.
- [ ] `useSyncExternalStore`: Subscribing safely to external non-React stores.

### React 19 New Hooks & APIs
- [ ] `useActionState`: Handling async state transitions and form submission status.
- [ ] `useFormStatus`: Child component access to parent form pending states.
- [ ] `useOptimistic`: Instant UI updates while background server requests finish.
- [ ] `use`: Reading promises and context directly during rendering (suspense-enabled data fetching).

### Custom Hooks Engineering
- [ ] Extracting reusable logic into hooks (`useLocalStorage`, `useDebounce`, `useFetch`, `useMediaQuery`, `useOnClickOutside`, `useIntersectionObserver`).

### Phase 2 Milestone
- Build a custom React hook library (`@your-name/react-hooks`) published to NPM or local monorepo containing 10+ battle-tested hooks with full TypeScript definitions and unit tests.

---

## Phase 3: Component Architecture & Design Patterns (2–3 Weeks)

### Advanced React Design Patterns
- [ ] **Compound Components**: Build flexible APIs like `<Accordion>`, `<Tabs>`, `<Select>` (Context + Children).
- [ ] **Control Props Pattern**: Allow components to be used controlled or uncontrolled (`value` vs `defaultValue`).
- [ ] **Slot Pattern**: Component composition using named JSX slots instead of deep prop drilling.
- [ ] **Container / Presentational Pattern**: Decoupling logic/data fetching from presentation.
- [ ] **Higher-Order Components (HOC)**: Understanding legacy wrappers, `withAuth`, `withLogger`.

### Form Architecture & Validation
- [ ] Form libraries: **React Hook Form** (performance, uncontrolled inputs, schema validation).
- [ ] Schema validation: **Zod** / Yup integration (`@hookform/resolvers/zod`).
- [ ] Complex multi-step forms, field arrays (`useFieldArray`), dynamic validation schemas.

### Styling & Design Systems
- [ ] **Tailwind CSS**: Utility-first styling, responsive design, dark mode, `clsx` / `tailwind-merge` utility functions (`cn()`).
- [ ] Headless UI Components: **Radix UI** primitives, **Shadcn UI** architecture.
- [ ] CSS Modules & Styled Components / Emotion (CSS-in-JS overview).
- [ ] Storybook: Component documentation, visual regression testing, isolated sandbox development.

### Phase 3 Milestone
- **Design System Component Library**: Build an accessible, customizable component library containing `Button`, `Modal`, `Combobox`, `DataTable`, `Tabs`, and `Toast` documented with Storybook and Tailwind CSS.

---

## Phase 4: State Management & Data Fetching Ecosystem (3–4 Weeks)

### State Management Matrix
- [ ] When to use Local State vs Context vs Client Store vs Server Cache.
- [ ] Context API Best Practices: Splitting state and dispatch contexts, Context selectors.

### Client State Managers
- [ ] **Zustand**: Lightweight atomic store, selectors, middleware (persist, devtools), slice pattern.
- [ ] **Redux Toolkit (RTK)**: Slices, `createAsyncThunk`, RTK Query integration, Redux DevTools, state normalized data.
- [ ] **Jotai / Recoil**: Atomic state model, derived atoms, async atoms.

### Server State & Data Fetching (TanStack Query / React Query)
- [ ] Why fetch inside `useEffect` is an anti-pattern.
- [ ] TanStack Query core concepts: `useQuery`, `useMutation`, `QueryClientProvider`, Query Keys design.
- [ ] Cache strategies: `staleTime`, `gcTime` (cacheTime), background refetching, window focus refetching.
- [ ] Optimistic updates: Rollbacks, cache manipulation (`queryClient.setQueryData`).
- [ ] Pagination, Infinite Queries (`useInfiniteQuery`), Prefetching.
- [ ] Error handling & Retries: Error Boundaries integration.

### Phase 4 Milestone
- **Real-Time Crypto & Stock Dashboard**: Fetch live price data using TanStack Query & WebSockets, backed by Zustand for user settings, dark mode, and portfolio state.

---

## Phase 5: Routing, SSR, SSG & Server Components (RSC) (3–4 Weeks)

### Client-Side Routing (React Router v6 / v7)
- [ ] MemoryRouter, BrowserRouter, Data Routers (`createBrowserRouter`).
- [ ] Dynamic Routes, Nested Routes, Outlet, Path Parameters, Query Parameters.
- [ ] Data Loading & Actions: `loader`, `action`, `useLoaderData`, `useActionData`, `useNavigation`.
- [ ] Protected Routes, Lazy Loading Routes (`React.lazy` + `Suspense`).

### Rendering Paradigms
- [ ] **SPA** (Single Page Application): Pros/cons, bundle sizes, client rendering.
- [ ] **SSR** (Server-Side Rendering): HTML streaming, hydrations, TTFB vs LCP.
- [ ] **SSG** (Static Site Generation): Build-time HTML generation, static hosting.
- [ ] **ISR** (Incremental Static Revalidation): On-demand and time-based revalidation.

### Full-Stack React & Server Components (Next.js App Router / Remix)
- [ ] **React Server Components (RSC)**: Server Components vs Client Components (`'use client'`).
- [ ] Streaming with **Suspense** & Progressive Hydration (`loading.tsx`).
- [ ] **Server Actions**: Mutating data on the server directly from forms or callbacks (`'use server'`).
- [ ] Caching Layers in Next.js: Request Memoization, Data Cache, Full Route Cache, Router Cache.
- [ ] Route Handlers (API Routes), Middleware, Metadata API (SEO).

### Phase 5 Milestone
- **Full-Stack SaaS Platform**: Build a full-stack Next.js App Router application with RSC, Server Actions, PostgreSQL (Prisma / Drizzle ORM), Authentication (NextAuth / Clerk), and Stripe subscriptions.

---

## Phase 6: React Internals, Fiber, Compiler & Performance (2–3 Weeks)

### React Architecture & Internals
- [ ] **React Fiber Architecture**: Two-phase rendering (Render phase vs Commit phase), WorkLoop, Effect List.
- [ ] Concurrent React: Time slicing, Lanes (prioritization system), Interruptible rendering.
- [ ] Reconciliation Algorithm: Heuristic O(n) diffing, element type comparison, key tracking.
- [ ] Synthetic Event Delegation: Root element delegation mechanism (React 17+ vs 16).

### React 19 Compiler (Forget)
- [ ] How the React Compiler automatically memoizes props, state, and callbacks.
- [ ] When manual memoization (`useMemo`, `useCallback`, `React.memo`) is still required vs automated compilation.

### Performance Profiling & Optimization
- [ ] **React DevTools Profiler**: Identifying slow renders, commit phases, flame charts, ranked charts.
- [ ] Measuring Web Vitals: LCP (Largest Contentful Paint), INP (Interaction to Next Paint), CLS (Cumulative Layout Shift).
- [ ] Code Splitting & Dynamic Imports: `React.lazy()`, route-based splitting, component-based splitting.
- [ ] List Virtualization: `react-window` / `tanstack-virtual` for rendering 10,000+ item lists.
- [ ] Preventing unnecessary re-renders: Passing primitive props, component splitting, children composition.
- [ ] Memory Leak Debugging: Cleaning event listeners, intervals, subscriptions, Chrome Memory Heap Snapshots.

### Phase 6 Milestone
- **Performance Audit & Refactor**: Take a deliberately slow, unoptimized React app (10,000 un-virtualized rows, heavy re-renders, prop-drilling leaks) and profile, refactor, and achieve 60 FPS scrolling and <100ms INP.

---

## Phase 7: Testing, Quality Assurance & Security (2 Weeks)

### Unit & Integration Testing
- [ ] Test Runner: **Vitest** / Jest configuration with React Testing Library.
- [ ] **React Testing Library (RTL)** Guiding Principles: Test user behavior, not implementation details.
- [ ] Queries: `getByText`, `findByRole`, `queryByTestId` (accessibility-first querying order).
- [ ] User Interactions: `@testing-library/user-event` (simulating clicks, typing, key presses).
- [ ] Mocking APIs: **MSW (Mock Service Worker)** for intercepting network requests at the network layer.
- [ ] Testing Custom Hooks: `@testing-library/react` (`renderHook`, `act`).

### End-to-End (E2E) Testing
- [ ] **Playwright**: Writing cross-browser E2E tests, authentication state reuse, visual comparison tests.

### React Security & Best Practices
- [ ] Cross-Site Scripting (XSS) in React: Sanitization, `dangerouslySetInnerHTML` risks, DOMPurify.
- [ ] CSRF (Cross-Site Request Forgery) protection, SameSite cookies, HTTP-only JWTs.
- [ ] Content Security Policy (CSP) headers for React SPAs and SSR apps.

### Phase 7 Milestone
- Achieve 95%+ test coverage on a complex React application using Vitest, RTL, MSW, and Playwright E2E suites running in GitHub Actions CI/CD.

---

## Phase 8: Full-Stack Capstone Projects (3–4 Weeks)

Build 2–3 portfolio-grade projects from scratch to showcase end-to-end mastery.

### Project 1: Enterprise Collaborative Workspace (Notion / Trello Clone)
- **Tech Stack**: Next.js App Router, RSC, Server Actions, Tailwind CSS, Shadcn UI, Zustand, PostgreSQL, Prisma, Liveblocks / Yjs (WebSockets / CRDTs).
- **Features**:
  - Multi-tenant organizations and workspace management.
  - Real-time collaborative document editing and drag-and-drop Kanban boards (`dnd-kit`).
  - Optimistic UI updates with instant rollback on network disconnect.
  - Complete E2E and integration test suite.

### Project 2: High-Frequency Analytics Dashboard
- **Tech Stack**: React 19, Vite, TypeScript, TanStack Query, TanStack Table, Recharts / D3.js, TanStack Virtual, WebSockets.
- **Features**:
  - Stream 100,000+ data points per second with virtualized rendering and zero lag.
  - Custom canvas charts, custom query hooks, dark/light theme engine.

---

## Phase 9: Senior/Staff System Design & Scale (2 Weeks)

### Micro-Frontends & Monorepos
- [ ] Monorepo Tooling: **Turborepo**, **Nx**, `pnpm` workspaces.
- [ ] Module Federation: Sharing React components runtime across independent micro-apps.

### Accessibility (a11y) & i18n
- [ ] ARIA attributes (`aria-expanded`, `aria-label`, `aria-live`, `role`).
- [ ] Keyboard Navigation & Focus Trap Management (`focus-trap-react`).
- [ ] Internationalization: **react-i18next** / `next-intl` (pluralization, date/currency formatting).

### Architecture & Code Quality Guidelines
- [ ] Feature Folder Architecture (Domain-driven folder structures vs type-driven folder structures).
- [ ] Clean Architecture in React: Separating UI logic, Domain models, Data access layers.

---

## Phase 10: Technical Interview & Code Challenge Prep (1–2 Weeks)

### Machine Coding & Polyfill Challenges
- [ ] Implement `useState` & `useEffect` from scratch (vanilla JS closures).
- [ ] Implement custom hooks: `useDebounce`, `useThrottle`, `usePrevious`, `useFetch`, `useOnClickOutside`.
- [ ] Implement a custom `Context` and `Provider` substitute.
- [ ] Build a zero-dependency Accordion, Carousel, Typeahead / Auto-complete input, and Virtualized List in 45 minutes under interview conditions.

### Senior System Design Topics
- [ ] How to design an E-commerce Checkout System / Social Media Feed / Figma canvas / Google Docs in React.
- [ ] Image Optimization & Lazy loading strategies (IntersectionObserver, Blurhashes, Responsive images).
- [ ] Bundle size budget optimization: Tree-shaking, Dynamic Imports, Webpack/Vite analyzer tools.

---

## Recommended Learning Resources

### Official Documentation
- [React Official Documentation (react.dev)](https://react.dev)
- [Next.js Official Documentation](https://nextjs.org/docs)
- [TanStack Query Documentation](https://tanstack.com/query)

### Books & Deep Dives
- *Learning React* by Alex Banks & Eve Porcello
- *Full-Stack React, TypeScript, and Node* by David Choi
- *Epic React* by Kent C. Dodds

---

## Daily Progress Tracker

Keep track of your journey:

- [ ] **Phase 0**: Modern JS, TS, & Tooling
- [ ] **Phase 1**: React Core & JSX Mechanics
- [ ] **Phase 2**: Hooks Mastery & React 19 APIs
- [ ] **Phase 3**: Component Architecture & Design Patterns
- [ ] **Phase 4**: State Management & TanStack Query
- [ ] **Phase 5**: Routing, SSR, & React Server Components
- [ ] **Phase 6**: Fiber, Compiler, & Performance Engineering
- [ ] **Phase 7**: Testing (RTL, MSW, Playwright) & Security
- [ ] **Phase 8**: Full-Stack Capstone Projects
- [ ] **Phase 9**: Enterprise Architecture & Micro-Frontends
- [ ] **Phase 10**: System Design & Interview Mastery
