# Expression Parsing: Infix Prefix Postfix

Last updated: February 27, 2026  
Phase: Linked Lists Stacks Queues and Deques (Phase 4)

## Goal
Master expression conversion and evaluation using stack-based parsing with correct precedence and associativity handling.

## Notation Quick Recap
- Infix: `A + B` (operator between operands)
- Prefix: `+ A B` (operator before operands)
- Postfix: `A B +` (operator after operands)

## Why Stacks Work Here
- Operators are applied later than they appear while scanning.
- Parentheses temporarily override precedence.
- Stack stores deferred operators/operands until expression structure is clear.

## Precedence and Associativity Rules

Typical precedence:
- `^` highest
- `*` `/` next
- `+` `-` lowest

Associativity:
- Left-associative: `+ - * /`
- Right-associative: `^`

When popping from operator stack in infix parsing:
- Pop while top operator has higher precedence.
- If equal precedence, pop only when incoming operator is left-associative.

## Core Parsing Patterns

### 1) Infix -> Postfix (Shunting Yard Style)
Data structures:
- output list
- operator stack

Rules while scanning token `t`:
- Operand -> append to output
- `(` -> push to operator stack
- `)` -> pop until `(`
- Operator `op` -> pop stronger/equal-left-assoc operators, then push `op`
- End -> pop remaining operators

```js
function infixToPostfix(tokens) {
  const out = [];
  const ops = [];
  const prec = new Map([['+', 1], ['-', 1], ['*', 2], ['/', 2], ['^', 3]]);
  const rightAssoc = new Set(['^']);
  const isOp = (x) => prec.has(x);

  for (const t of tokens) {
    if (/^\d+$/.test(t) || /^[A-Za-z]+$/.test(t)) out.push(t);
    else if (t === '(') ops.push(t);
    else if (t === ')') {
      while (ops.length && ops[ops.length - 1] !== '(') out.push(ops.pop());
      ops.pop(); // pop '('
    } else if (isOp(t)) {
      while (
        ops.length &&
        isOp(ops[ops.length - 1]) &&
        (
          prec.get(ops[ops.length - 1]) > prec.get(t) ||
          (
            prec.get(ops[ops.length - 1]) === prec.get(t) &&
            !rightAssoc.has(t)
          )
        )
      ) {
        out.push(ops.pop());
      }
      ops.push(t);
    }
  }

  while (ops.length) out.push(ops.pop());
  return out;
}
```

### 2) Postfix Evaluation
Rule:
- Operand -> push
- Operator -> pop two (`b`, then `a`), compute `a op b`, push result

```js
function evalPostfix(tokens) {
  const st = [];

  for (const t of tokens) {
    if (/^-?\d+$/.test(t)) st.push(Number(t));
    else {
      const b = st.pop();
      const a = st.pop();
      if (t === '+') st.push(a + b);
      else if (t === '-') st.push(a - b);
      else if (t === '*') st.push(a * b);
      else if (t === '/') st.push(Math.trunc(a / b));
    }
  }

  return st.pop();
}
```

### 3) Prefix Evaluation
Rule:
- Scan right to left
- Operand -> push
- Operator -> pop two (`a`, then `b`), compute `a op b`, push result

```js
function evalPrefix(tokens) {
  const st = [];

  for (let i = tokens.length - 1; i >= 0; i--) {
    const t = tokens[i];
    if (/^-?\d+$/.test(t)) st.push(Number(t));
    else {
      const a = st.pop();
      const b = st.pop();
      if (t === '+') st.push(a + b);
      else if (t === '-') st.push(a - b);
      else if (t === '*') st.push(a * b);
      else if (t === '/') st.push(Math.trunc(a / b));
    }
  }

  return st.pop();
}
```

## Infix Evaluation with Parentheses (Calculator Pattern)

Approach:
- Two stacks: values and operators.
- Parse multi-digit numbers.
- Apply operators when precedence allows.
- Resolve full bracket when `)` appears.

```js
function calculateInfix(expr) {
  const vals = [];
  const ops = [];
  const prec = (c) => (c === '+' || c === '-') ? 1 : (c === '*' || c === '/') ? 2 : 0;

  const apply = () => {
    const op = ops.pop();
    const b = vals.pop();
    const a = vals.pop();
    if (op === '+') vals.push(a + b);
    else if (op === '-') vals.push(a - b);
    else if (op === '*') vals.push(a * b);
    else vals.push(Math.trunc(a / b));
  };

  for (let i = 0; i < expr.length; i++) {
    const ch = expr[i];
    if (ch === ' ') continue;

    if (/\d/.test(ch)) {
      let num = 0;
      while (i < expr.length && /\d/.test(expr[i])) {
        num = num * 10 + (expr.charCodeAt(i) - 48);
        i++;
      }
      i--;
      vals.push(num);
    } else if (ch === '(') {
      ops.push(ch);
    } else if (ch === ')') {
      while (ops.length && ops[ops.length - 1] !== '(') apply();
      ops.pop();
    } else {
      while (ops.length && ops[ops.length - 1] !== '(' && prec(ops[ops.length - 1]) >= prec(ch)) {
        apply();
      }
      ops.push(ch);
    }
  }

  while (ops.length) apply();
  return vals.pop();
}
```

## Tokenization Notes
- Always handle multi-digit numbers, not just single chars.
- Support whitespace skipping.
- For production-grade parser, tokenize first then parse.
- Unary minus needs explicit handling strategy.

Unary minus common strategy:
- If `-` appears at start or after `(` or operator, treat as unary.
- Convert `-x` to `0 - x` during tokenization/parsing.

## Problem Recognition Map
- Evaluate Reverse Polish Notation -> postfix evaluation stack.
- Basic Calculator I -> infix with `+`, `-`, parentheses.
- Basic Calculator II -> infix with `+ - * /`, no parentheses.
- Basic Calculator III -> full infix with precedence + parentheses.
- Convert infix to postfix/prefix -> shunting-yard style operator stack.

## Common Mistakes
- Forgetting multi-digit parsing.
- Wrong associativity for exponent operator `^`.
- Popping order bug (`a` and `b` reversed) in postfix/prefix.
- Not flushing remaining operators at end.
- Not handling spaces and unary minus correctly.
- Division rounding mismatch with language expectation.

## Debugging Checklist
- [ ] Are precedence and associativity tables correct?
- [ ] For postfix, do I pop `b` then `a` and compute `a op b`?
- [ ] Do parentheses always balance and clear correctly?
- [ ] Are all remaining operators applied after scan?
- [ ] Did I test nested parentheses and long multi-digit numbers?
- [ ] Is negative number handling defined explicitly?

## Mastery Checklist
- [ ] I can convert infix to postfix with correct rules from memory.
- [ ] I can evaluate postfix/prefix without operand-order bugs.
- [ ] I can build a two-stack infix evaluator.
- [ ] I can explain and handle unary minus cases.
- [ ] I can reason about `O(n)` time and `O(n)` space for these parsers.

## Practice Targets
- Total: 20 to 25 expression-parsing problems.
- Minimum split:
  - 6 postfix/prefix evaluation
  - 6 infix conversion
  - 6 calculator variants with precedence/parentheses
  - 4 mixed parsing edge-case problems
- Re-solve all failed problems on D+7 and D+30.
