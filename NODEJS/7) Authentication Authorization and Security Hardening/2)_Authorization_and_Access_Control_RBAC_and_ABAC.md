# 2) Authorization and Access Control: RBAC and ABAC

## Executive Overview
While authentication answers *"Who are you?"*, authorization answers *"What are you allowed to do?"*. In enterprise Node.js applications, access control models range from simple **Role-Based Access Control (RBAC)** to fine-grained **Attribute-Based Access Control (ABAC)** and Policy-As-Code systems.

Understanding how to construct type-safe authorization layers prevents **Broken Object Level Authorization (BOLA / IDOR)**—the #1 vulnerability on the OWASP API Security Top 10 list.

---

## 1. Access Control Models Comparison

```text
┌────────────────────────┬────────────────────────────────┬────────────────────────────────┐
│ Model                  │ Core Concept                   │ Ideal Use Case                 │
├────────────────────────┼────────────────────────────────┼────────────────────────────────┤
│ RBAC                   │ Users have Roles (Admin, User).│ Simple SaaS apps with broad    │
│ (Role-Based)           │ Roles map to static permissions│ administrative permissions.    │
├────────────────────────┼────────────────────────────────┼────────────────────────────────┤
│ ABAC                   │ Evaluates Attributes: Subject, │ Multi-tenant systems, ownership│
│ (Attribute-Based)      │ Resource, Action, Environment. │ checks, dynamic business rules.│
├────────────────────────┼────────────────────────────────┼────────────────────────────────┤
│ ReBAC                  │ Permissions derived from graph │ Google Docs style sharing      │
│ (Relationship-Based)   │ relationships (Member of Org). │ (Viewer, Editor, Owner graphs).│
└────────────────────────┴────────────────────────────────┴────────────────────────────────┘
```

---

## 2. Implementing RBAC with Role Hierarchies

In hierarchical RBAC, higher-tier roles inherit permissions from lower-tier roles:

```text
SuperAdmin (Inherits Admin + User)
    │
    ▼
  Admin (Inherits User)
    │
    ▼
  User (Base permissions)
```

```typescript
export type Permission = 
  | 'users:read'
  | 'users:create'
  | 'users:delete'
  | 'billing:manage'
  | 'articles:read'
  | 'articles:create';

export type Role = 'USER' | 'ADMIN' | 'SUPERADMIN';

// Role-to-Permissions Mapping Table
const ROLE_PERMISSIONS: Record<Role, readonly Permission[]> = {
  USER: ['articles:read', 'articles:create'],
  ADMIN: ['articles:read', 'articles:create', 'users:read', 'users:create'],
  SUPERADMIN: ['articles:read', 'articles:create', 'users:read', 'users:create', 'users:delete', 'billing:manage'],
} as const;

export function hasPermission(userRole: Role, requiredPermission: Permission): boolean {
  const permissions = ROLE_PERMISSIONS[userRole] || [];
  return permissions.includes(requiredPermission);
}
```

---

## 3. Fine-Grained ABAC with `@casl/ability`

RBAC fails when permissions depend on dynamic resource attributes—such as *"A user can only edit an article if they are the author of that article AND the article is still in draft mode."*

### 3.1 Defining Domain Abilities with CASL

```typescript
import { AbilityBuilder, createMongoAbility, MongoAbility, ExtractSubjectType } from '@casl/ability';

export interface User {
  id: string;
  role: 'admin' | 'author' | 'reader';
  tenantId: string;
}

export interface Article {
  id: string;
  authorId: string;
  tenantId: string;
  isPublished: boolean;
}

type Actions = 'manage' | 'create' | 'read' | 'update' | 'delete';
type Subjects = 'Article' | 'User' | 'all' | Article;

export type AppAbility = MongoAbility<[Actions, Subjects]>;

export function defineAbilityForUser(user: User): AppAbility {
  const { can, cannot, build } = new AbilityBuilder<AppAbility>(createMongoAbility);

  if (user.role === 'admin') {
    // Admin can manage everything within their own tenant
    can('manage', 'all', { tenantId: user.tenantId });
  } else if (user.role === 'author') {
    // Authors can read all published articles
    can('read', 'Article', { isPublished: true });
    
    // Authors can manage their OWN articles
    can('manage', 'Article', { authorId: user.id });

    // Cannot update articles that are already locked/published
    cannot('update', 'Article', { isPublished: true });
  } else {
    // Readers can only read published articles
    can('read', 'Article', { isPublished: true });
  }

  return build({
    detectSubjectType: (item) => (typeof item === 'string' ? item : 'Article'),
  });
}
```

### 3.2 Enforcing Abilities in Route Handlers

```typescript
import { Request, Response, NextFunction } from 'express';
import { defineAbilityForUser, Article } from './casl';

export async function updateArticleHandler(req: Request, res: Response) {
  const user = req.user; // Authenticated User
  const ability = defineAbilityForUser(user);

  // Fetch target resource from database
  const article: Article = await db.getArticle(req.params.id);

  // Perform fine-grained ABAC verification
  if (ability.cannot('update', article)) {
    return res.status(403).json({
      error: 'Forbidden',
      reason: 'You do not have permission to update this specific article (either not author or article is published)',
    });
  }

  // Proceed with update...
  res.json({ status: 'UPDATED' });
}
```
