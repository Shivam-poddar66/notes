# 4) NestJS Enterprise IoC, DI, and Request Pipeline

## Executive Overview
NestJS is a progressive, TypeScript-first framework designed for building scalable, testable, enterprise-grade backend systems. Drawing inspiration from Angular, Clean Architecture, and Domain-Driven Design (DDD), NestJS structures applications around **Inversion of Control (IoC)**, **Dependency Injection (DI)**, and a layered **Aspect-Oriented Request Lifecycle Pipeline**.

---

## 1. Inversion of Control (IoC) & Dependency Injection (DI)

In traditional code, classes instantiate their own dependencies directly via `new Service()`, tightly coupling components and making unit testing difficult.

NestJS uses an **IoC Container**:
1. Classes declare dependencies in their `constructor`.
2. NestJS inspects metadata emitted by TypeScript decorators (`@Injectable()`).
3. The IoC container instantiates providers in topological dependency order and injects them automatically.

```text
┌────────────────────────────────────────────────────────┐
│                   NestJS IoC Container                 │
│                                                        │
│   DatabaseConnection ────────> UserRepository           │
│                                      │                 │
│                                      ▼                 │
│                                 UserService             │
│                                      │                 │
│                                      ▼                 │
│                               UserController           │
└────────────────────────────────────────────────────────┘
```

### 1.1 Provider Scopes
- **`Scope.DEFAULT` (Singleton - Recommended)**: A single instance is shared across the entire application lifecycle. Cache-friendly and high performance.
- **`Scope.REQUEST`**: A new instance is created for every incoming HTTP request. (Warning: high memory allocation and CPU overhead!).
- **`Scope.TRANSIENT`**: A new instance is created for each injecting consumer.

```typescript
import { Injectable, Scope } from '@nestjs/common';

@Injectable({ scope: Scope.DEFAULT })
export class PaymentService {
  public async processCharge(amount: number) {
    return { status: 'SUCCESS', transactionId: 'TX-902' };
  }
}
```

---

## 2. Modular Architecture & Dynamic Modules

NestJS applications are divided into cohesive domain modules:

```typescript
import { Module, DynamicModule } from '@nestjs/common';
import { UsersController } from './users.controller';
import { UsersService } from './users.service';

@Module({
  imports: [],
  controllers: [UsersController],
  providers: [UsersService],
  exports: [UsersService], // Exported to be consumed by other modules
})
export class UsersModule {
  // Dynamic Module Pattern for configurable modules
  static forRoot(options: { apiKey: string }): DynamicModule {
    return {
      module: UsersModule,
      providers: [
        {
          provide: 'API_KEY_TOKEN',
          useValue: options.apiKey,
        },
        UsersService,
      ],
      exports: [UsersService],
    };
  }
}
```

---

## 3. The 6-Stage NestJS Request Pipeline

NestJS routes requests through an Aspect-Oriented Programming (AOP) sequence:

```text
Incoming HTTP Request
   │
   ▼
1. Middleware ──────────────> Raw Express/Fastify middleware (e.g. CORS, Helmet, Logger)
   │
   ▼
2. Guards (CanActivate) ────> Authentication & Authorization (Roles, Permissions)
   │
   ▼
3. Interceptors (Pre) ──────> Caching check, request transformation, timing start
   │
   ▼
4. Pipes ───────────────────> Request validation (class-validator) & type casting (ParseIntPipe)
   │
   ▼
5. Controller Handler ──────> Business logic execution & Service delegation
   │
   ▼
6. Interceptors (Post) ─────> RxJS Operators (Response mapping, logging latency, error mapping)
   │
   ▼
(On Exception) ─────────────> 7. Exception Filters (@Catch) ──> Formatted HTTP Error Response
```

---

## 4. Component Implementations

### 4.1 Guard (Authentication & RBAC)

```typescript
import { Injectable, CanActivate, ExecutionContext, UnauthorizedException } from '@nestjs/common';
import { Reflector } from '@nestjs/core';

@Injectable()
export class RolesGuard implements CanActivate {
  constructor(private reflector: Reflector) {}

  canActivate(context: ExecutionContext): boolean {
    const requiredRoles = this.reflector.get<string[]>('roles', context.getHandler());
    if (!requiredRoles) return true;

    const request = context.switchToHttp().getRequest();
    const user = request.user; // Set by AuthGuard

    if (!user || !requiredRoles.includes(user.role)) {
      throw new UnauthorizedException('Insufficient permissions');
    }

    return true;
  }
}
```

### 4.2 Interceptor (Logging & Execution Timing via RxJS)

```typescript
import { Injectable, NestInterceptor, ExecutionContext, CallHandler } from '@nestjs/common';
import { Observable } from 'rxjs';
import { tap } from 'rxjs/operators';

@Injectable()
export class LoggingInterceptor implements NestInterceptor {
  intercept(context: ExecutionContext, next: CallHandler): Observable<any> {
    const request = context.switchToHttp().getRequest();
    const { method, url } = request;
    const now = Date.now();

    return next.handle().pipe(
      tap(() => {
        const delay = Date.now() - now;
        console.log(`[NESTJS AOP] ${method} ${url} completed in ${delay}ms`);
      })
    );
  }
}
```

### 4.3 Validation Pipe with `class-validator`

```typescript
import { IsEmail, IsString, MinLength } from 'class-validator';

export class CreateUserDto {
  @IsString()
  @MinLength(3)
  username!: string;

  @IsEmail()
  email!: string;
}
```

### 4.4 Global Exception Filter

```typescript
import { ExceptionFilter, Catch, ArgumentsHost, HttpException, HttpStatus } from '@nestjs/common';
import { Response, Request } from 'express';

@Catch()
export class AllExceptionsFilter implements ExceptionFilter {
  catch(exception: unknown, host: ArgumentsHost) {
    const ctx = host.switchToHttp();
    const response = ctx.getResponse<Response>();
    const request = ctx.getRequest<Request>();

    const status =
      exception instanceof HttpException
        ? exception.getStatus()
        : HttpStatus.INTERNAL_SERVER_ERROR;

    const message =
      exception instanceof HttpException
        ? exception.getResponse()
        : 'Internal Server Error';

    response.status(status).json({
      statusCode: status,
      timestamp: new Date().toISOString(),
      path: request.url,
      error: message,
    });
  }
}
```
