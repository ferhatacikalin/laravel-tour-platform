# Authenticating requests

To authenticate requests, include an **`Authorization`** header with the value **`"Bearer {token}"`**.

All authenticated endpoints are marked with a `requires authentication` badge in the documentation below.

You can retrieve your token by logging in through the POST /api/v1/auth/login endpoint.
