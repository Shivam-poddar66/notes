# Phase 2 Study Plan: Deep Core JavaScript (3 to 5 Weeks)

---

## 1) Curriculum Architecture & Learning Objectives

Phase 2 transitions your JavaScript knowledge from surface-level syntax to **deep runtime mechanics and engine internals**.

### Core Competencies Mastered in Phase 2:
1. **Execution Model**: Execution Contexts, Call Stack, Memory Heap, Hoisting, and the Temporal Dead Zone (TDZ).
2. **Lexical Scope & Closures**: Environment Records, Scope Chains, Encapsulation, and Async Closure Pitfalls.
3. **Context Resolution (`this`)**: The 5 call-site binding rules, detached methods, and explicit binding (`call`/`apply`/`bind`).
4. **Prototypal Inheritance**: `[[Prototype]]` delegation, constructor functions, `Object.create`, and ES6 `class` mechanics.
5. **Object Internals**: Property Descriptors, Hidden Classes, `Object.freeze/seal`, and Deep vs. Shallow copying.
6. **Modern Architecture**: ESM vs. CommonJS, Module Design, Tree-Shaking, and Writing Spec-Compliant Polyfills.

```
┌─────────────────────────────────────────────────────────────────────────┐
│                    PHASE 2 MASTERY PROGRESSION                          │
│                                                                         │
│  [ Stage 1: Runtime & Scope ]  ──▶ Execution Context, Hoisting, Scope   │
│                 │                                                       │
│                 ▼                                                       │
│  [ Stage 2: State & Context ]  ──▶ Closures, Memory, `this` Binding     │
│                 │                                                       │
│                 ▼                                                       │
│  [ Stage 3: OOP & Prototypes]  ──▶ Prototype Chains, Classes, Descriptors│
│                 │                                                       │
│                 ▼                                                       │
│  [ Stage 4: Modules & Design]  ──▶ ESM, Tree-Shaking, Mini-Projects     │
└─────────────────────────────────────────────────────────────────────────┘
```

---

## 2) The Daily Study Protocol (2 to 3 Hours)

Maximize retention using the **4-Step Active Learning Cycle**:

```
┌────────────────────────────────────────────────────────────────────────┐
│                        DAILY 4-STEP LEARNING CYCLE                     │
│                                                                        │
│  1. Theory & Spec Deep-Dive (30 min):                                  │
│     • Read the topic note; understand engine mechanics and mental model│
│                                                                        │
│  2. Hands-on Code Experimentation (60 min):                            │
│     • Write runnable snippets; inspect variables in Chrome DevTools    │
│     • Step through Call Stack and Scope panes with breakpoints         │
│                                                                        │
│  3. Implementation & Polyfilling (45 min):                             │
│     • Build the feature or mini-project from scratch without copying   │
│                                                                        │
│  4. Feynman Verbalization & Quiz (15 min):                             │
│     • Explain the concept out loud as if teaching a junior developer   │
└────────────────────────────────────────────────────────────────────────┘
```

---

## 3) Track A: Fast-Track Schedule (3 Weeks Intensive)

*Recommended for intermediate developers or intensive interview preparation (3–4 hours/day).*

---

### Week 1: Runtime Engine, Scope, Closures & `this`
- **Day 1**: 
  - Study: `1)_Execution_context_and_call_stack.md` & `2)_Hoisting_and_Temporal_Dead_Zone_(TDZ).md`
  - Lab: Inspect GEC vs FEC creation in Chrome DevTools Sources pane.
- **Day 2**: 
  - Study: `3)_Lexical_environment_and_scope_chain.md` & `4)_Closures_core_concepts_and_use_cases.md`
  - Lab: Build private memoize and rate-limiter utilities using closures.
- **Day 3**: 
  - Study: `5)_Closure_pitfalls_in_loops_and_async.md`
  - Lab: Fix async loop timer bugs using `let`, IIFE, and `bind`.
- **Day 4**: 
  - Study: `6)_this_keyword_by_invocation_context.md` & `7)_call_apply_bind_and_function_borrowing.md`
  - Lab: Implement polyfills for `myCall`, `myApply`, and `myBind`.
- **Day 5**: 
  - Study: `8)_Prototypes_prototype_chain_and_lookup.md` & `9)_Constructor_functions_and_prototypal_inheritance.md`
  - Lab: Implement a custom `myNew()` operator and multi-level constructor inheritance.
- **Day 6**: 
  - Study: `10)_Object_create_and_manual_inheritance_patterns.md` & `11)_ES6_classes_vs_prototype_model.md`
  - Lab: Convert an ES6 class hierarchy back into pure ES5 prototypes manually.
- **Day 7**: 
  - Review: Code tracing drills, tricky interview output puzzles, and weekly recap.

---

### Week 2: Object Internals, Copying, Modules & Mini-Projects
- **Day 8**: 
  - Study: `12)_Property_descriptors_and_object_internals.md` & `13)_Object_freeze_seal_preventExtensions_and_immutability.md`
  - Lab: Build a recursive `deepFreeze` utility and inspect non-enumerable descriptors.
- **Day 9**: 
  - Study: `14)_Shallow_vs_deep_copy_and_structuredClone.md`
  - Lab: Build a circular-safe `deepClone` utility with `WeakMap`.
- **Day 10**: 
  - Study: `15)_JavaScript_modules_ESM_vs_CommonJS.md` & `16)_Default_named_exports_and_module_design.md`
  - Lab: Set up a mixed ESM/CJS Node.js project; demonstrate live bindings.
- **Day 11**: 
  - Study: `17)_Tree_shaking_and_side_effects_mindset.md` & `18)_Advanced_language_features_pack.md`
  - Lab: Build a custom iterator range and generator workflow.
- **Day 12**: 
  - Build: `19)_Mini_project_1_Custom_Event_Emitter.md` (Complete implementation + tests).
- **Day 13**: 
  - Build: `20)_Mini_project_2_Module_based_app.md` (Modular Task Manager with clean ESM).
- **Day 14**: 
  - Build: `21)_Mini_project_3_Polyfills_map_filter_reduce.md` (Spec-compliant array polyfills).

---

### Week 3: Deep Debugging, Mock Interviews & Capstone Readiness
- **Day 15**: Study `22)_Common_mistakes_and_debugging_checklist.md`; debug 10 intentional bugs.
- **Day 16**: Verbal explanation drills: Explain `this`, Closures, and Prototypes without looking at notes.
- **Day 17**: Memory & Performance: Profile heap snapshots for closure memory leaks.
- **Day 18**: Refactor all 3 mini-projects with strict linting and test coverage.
- **Day 19**: Full-length mock technical interview covering Phase 2 questions.
- **Day 20**: Comprehensive review using `24)_Quick_revision_sheet.md`.
- **Day 21**: Final Phase 2 Assessment & Transition to Phase 3 (Asynchronous JavaScript).

---

## 4) Track B: Standard Deep-Mastery Schedule (5 Weeks)

*Recommended for standard pacing (1.5–2 hours/day) for thorough long-term mastery.*

---

### Week 1: The Execution Model & Scoping Mechanics
- **Focus**: Engine internals, Execution Contexts, Memory creation vs execution, Hoisting, TDZ, and Lexical Environments.
- **Deliverable**: Draw complete visual memory diagrams of GEC and FEC stacks.

### Week 2: State, Closures & Context Resolution (`this`)
- **Focus**: Heap memory retention of closures, async loop pitfalls, the 5 `this` binding rules, `call`/`apply`/`bind` polyfills.
- **Deliverable**: Write a custom `myBind` polyfill and implement a private memoized cache.

### Week 3: Prototypal Delegation & Object-Oriented JS
- **Focus**: `[[Prototype]]` lookup, `Object.prototype`, constructor functions, custom `new` polyfill, `Object.create` (OLOO), and ES6 classes.
- **Deliverable**: Implement a complete vehicle/car inheritance hierarchy in both ES5 prototypes and ES6 classes.

### Week 4: Object Internals, Immutability & Module Systems
- **Focus**: Property Descriptors, `Object.freeze/seal`, Deep copying with `structuredClone`, CommonJS vs ESM live bindings, and Tree-Shaking.
- **Deliverable**: Build a recursive `deepFreeze` utility and verify tree-shaking with Vite/Rollup.

### Week 5: Advanced Features, Mini-Projects & Capstone
- **Focus**: Iterators, Generators, Metaprogramming Symbols, Mini-Projects 1, 2, and 3.
- **Deliverable**: Complete all 3 Mini-Projects with 100% test coverage and review the Debugging Checklist.

---

## 5) Milestone Checkpoint: 15 Knowledge Gate Questions

Before advancing to **Phase 3 (Asynchronous JavaScript)**, you must be able to answer all 15 questions clearly:

1. [ ] Why is JavaScript described as single-threaded and synchronous in its core?
2. [ ] What happens during the Creation Phase vs. the Execution Phase of an Execution Context?
3. [ ] Why are `let` and `const` variables in the Temporal Dead Zone (TDZ) even though they are hoisted?
4. [ ] How does an inner function retain access to an outer variable after the outer context is popped from the Call Stack?
5. [ ] Why does `for (var i = 0; i < 3; i++) setTimeout(...)` print `3, 3, 3`, and why does `let` fix it?
6. [ ] What are the 5 rules for determining `this`, in order of precedence?
7. [ ] Why can't an arrow function's `this` be overridden using `.call()` or `.bind()`?
8. [ ] What four distinct steps does the `new` keyword perform under the hood?
9. [ ] What is the difference between `[[Prototype]]`, `__proto__`, and the `.prototype` property?
10. [ ] Why is `Child.prototype = Parent.prototype` a severe anti-pattern in inheritance?
11. [ ] What are the 4 attributes of a Property Descriptor, and what do they default to when using `Object.defineProperty`?
12. [ ] Why is `Object.freeze()` considered shallow, and how do you make an object deeply immutable?
13. [ ] What is the difference between value copies in CommonJS and live bindings in ECMAScript Modules (ESM)?
14. [ ] What constitutes a module side-effect, and why does it prevent bundlers from tree-shaking?
15. [ ] How does `Array.prototype.map` handle empty slots (holes) in sparse arrays?

---

## 6) Summary & Progress Tracker

```
+────────────────────────────────────────────────────────────────────────────+
|                        PHASE 2 COMPLETION CHECKLIST                        |
+───────────────────────────+────────────────────────────────────────────────+
| Core Notes Studied        | 18 / 18 In-depth notes completed.              |
| Mini-Project 1 Built      | Custom Event Emitter with full tests.          |
| Mini-Project 2 Built      | Modular ESM Task Management App.               |
| Mini-Project 3 Built      | Spec-compliant `map`, `filter`, `reduce`.      |
| Debugging Drills          | 12-Step checklist and error catalog reviewed.  |
| Knowledge Gate Verified   | 15 / 15 Gate questions cleared.                |
+───────────────────────────+────────────────────────────────────────────────+
```
