# Introduction

Welcome to the Tour Platform API documentation. This API provides endpoints for managing tours, bookings, and user authentication.

<aside>
    <strong>Base URL</strong>: <code>http://localhost:8000/api/v1</code>
</aside>

## Authentication

The API uses Bearer token authentication. To authenticate your requests:

1. First, obtain a token by logging in through the `POST /auth/login` endpoint
2. Include the token in the `Authorization` header of your requests:
   ```
   Authorization: Bearer your_token_here
   ```

All authenticated endpoints are marked with a 🔒 lock icon.

## Response Format

All API responses follow this standard format:

```json
{
    "status": true,
    "message": "MESSAGE_CODE",
    "data": {
        // Response data here
    }
}
```

For errors:
```json
{
    "status": false,
    "message": "ERROR_MESSAGE_CODE"
}
```

<aside>As you scroll, you'll see code examples for working with the API in different programming languages in the dark area to the right (or as part of the content on mobile).
You can switch the language used with the tabs at the top right (or from the nav menu at the top left on mobile).</aside>

