# Quick Revision Sheet (Phase 2)

## Execution Model
- JS runs inside execution contexts.
- Call stack is LIFO.
- Stack overflow = uncontrolled deep recursion.

## Hoisting and TDZ
- Function declarations hoist with body.
- `var` hoists as `undefined`.
- `let`/`const` are hoisted but unusable before declaration (TDZ).

## Scope and Closures
- Scope is lexical.
- Closures keep access to outer variables.
- Loop + async + `var` causes closure bugs.

## `this`
- `this` depends on call site.
- Arrow functions capture lexical `this`.
- Use `bind` to lock function context.

## Prototypes and Inheritance
- Property lookup climbs prototype chain.
- Constructor methods should live on prototype.
- Class syntax is prototype sugar.

## Object Internals
- Descriptors: writable/enumerable/configurable.
- `freeze`/`seal`/`preventExtensions` control mutability levels.
- Spread/Object.assign are shallow copies.

## Modules
- Prefer ESM for new projects.
- Named exports improve API clarity.
- Tree-shaking works best with pure, side-effect-free modules.

## Advanced Features
- Deep destructuring can improve or hurt readability; use carefully.
- Use `??` with `?.` for safe defaults.
- Generators provide lazy sequences.
- Symbols avoid key collisions.

## Milestone
- Custom Event Emitter implemented.
- Module-based app built.
- `map`/`filter`/`reduce` polyfills completed.
- Ready for Phase 3 Async JavaScript.
