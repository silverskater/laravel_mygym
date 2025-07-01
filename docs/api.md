# REST API Endpoints

---

## Auth

**POST /register**  
_Register a new user_  
**Request Body:**  
```json
{ "name": "User Name", "email": "user@example.com", "password": "<user-password>", "role": "instructor" }
```
**Response:** 201 Created  
```json
{ "id": 1, "name": "User Name", "email": "user@example.com", "token": "auth_token", "role": "instructor" }
```

**POST /login**  
_Log in a user_  
**Request Body:**  
```json
{ "email": "user@example.com", "password": "<user-password>" }
```
**Response:** 200 OK  
```json
{ "id": 1, "name": "User Name", "email": "user@example.com", "token": "auth_token", "role": "instructor" }
```

---

## Class Types

**GET /class-types**  
_List all class types_  
**Response:** 200 OK  
```json
[ { "id": 1, "name": "...", "description": "...", "duration": 60, ... }, ... ]
```

**GET /class-types/{id}**  
_Get details of a specific class type_  
**Response:** 200 OK  
```json
{ "id": 1, "name": "...", "description": "...", "duration": 60, ... }
```

**POST /class-types**  
_Create a new class type_  
**Request Body:**  
```json
{ "name": "...", "description": "...", "duration": 60, ... }
```
**Response:** 201 Created  
```json
{ "id": 1, "name": "...", "description": "...", "duration": 60, ... }
```

**PUT /class-types/{id}**  
_Update a class type_  
**Request Body:**  
```json
{ "name": "...", "description": "...", "duration": 60, ... }
```
**Response:** 200 OK  
```json
{ "id": 1, "name": "...", "description": "...", "duration": 60, ... }
```

**DELETE /class-types/{id}**  
_Delete a class type_  
**Response:** 204 No Content

---

## Scheduled Classes

**GET /scheduled-classes**  
_List all scheduled classes (optionally filter by instructor, date, etc.)_  
**Response:** 200 OK  
```json
[ { "id": 1, "class_type_id": 2, "instructor_id": 3, "scheduled_at": "...", ... }, ... ]
```

**GET /scheduled-classes/{id}**  
_Get details of a scheduled class_  
**Response:** 200 OK  
```json
{ "id": 1, "class_type_id": 2, "instructor_id": 3, "scheduled_at": "...", ... }
```

**POST /scheduled-classes**  
_Schedule a new class_  
**Request Body:**  
```json
{ "name": "New class", "description": "Scheduled class description", "class_type": "...", ... }
```
**Response:** 201 Created  
```json
{ "id": 2, "name": "...", "description": "..." }
```

**PUT /scheduled-classes/{id}**  
_Update a scheduled class_  
**Request Body:**  
```json
{ "name": "Updated class name", "description": "Updated description", ... }
```
**Response:** 200 OK  
```json
{ "id": 1, "name": "Updated class name", "description": "Updated description", ... }
```

**DELETE /scheduled-classes/{id}**  
_Delete a scheduled class_  
**Response:** 204 No Content

---

## User Management (Admin Only)

**GET /users**  
_List all users_  
**Response:** 200 OK  
```json
[ { "id": 1, "name": "...", "email": "...", "role": "...", ... }, ... ]
```

**GET /users/{id}**  
_Get details of a user_  
**Response:** 200 OK  
```json
{ "id": 1, "name": "...", "email": "...", "role": "...", ... }
```

**PUT /users/{id}**  
_Update a user (admin only)_  
**Request Body:**  
```json
{ "name": "...", "email": "...", "role": "...", ... }
```
**Response:** 200 OK  
```json
{ "id": 1, "name": "...", "email": "...", "role": "...", ... }
```

**DELETE /users/{id}**  
_Delete a user (admin only)_  
**Response:** 204 No Content

---

## User Self-Service

**GET /me**  
_Get current authenticated user's profile_  
**Response:** 200 OK  
```json
{ "id": 1, "name": "...", "email": "...", "role": "...", ... }
```

**PUT /me**  
_Update current authenticated user's profile_  
**Request Body:**  
```json
{ "name": "...", "email": "...", "phone": "...", ... }
```
**Response:** 200 OK  
```json
{ "id": 1, "name": "...", "email": "...", ... }
```

**PUT /me/password**  
_Change current authenticated user's password_  
**Request Body:**  
```json
{ "current_password": "...", "new_password": "..." }
```
**Response:** 200 OK  
```json
{ "message": "Password updated successfully." }
```

---

## User's Scheduled Classes

**GET /users/{id}/scheduled-classes**  
_List all scheduled classes for a specific user (instructor or member)_  
**Response:** 200 OK  
```json
[ { "id": 1, "class_type_id": 2, "instructor_id": 3, "scheduled_at": "...", ... }, ... ]
```

---

## Common Error Responses

**401 Unauthorized**  
Returned when the user is not authenticated (not logged in).  
```json
{ "message": "Unauthenticated." }
```

**403 Forbidden**  
Returned when the user is authenticated but does not have permission to perform the action.  
```json
{ "message": "This action is unauthorized." }
```

**404 Not Found**  
Returned when the requested resource does not exist.  
```json
{ "message": "Resource not found." }
```

**422 Unprocessable Entity**  
Returned when validation fails on input data.  
```json
{ "message": "The given data was invalid.", "errors": { "field": ["Error message."] } }
```

---

## Example: Authenticated and Authorization Errors

- If a user tries to access a protected endpoint without being logged in:
    ```
    GET /me
    Response: 401 Unauthorized
    Body: { "message": "Unauthenticated." }
    ```

- If a user tries to delete a class they do not own:
    ```
    DELETE /scheduled-classes/5
    Response: 403 Forbidden
    Body: { "message": "This action is unauthorized." }
    ```

- If a user tries to access a non-existent resource:
    ```
    GET /class-types/1234567890
    Response: 404 Not Found
    Body: { "message": "Resource not found." }
    ```

---

## Example cURL Commands

### Register a new user

```sh
curl -X POST http://localhost/api/register \
  -H 'Content-Type: application/json' \
  -H 'Accept: application/json' \
  -d '{"name":"Alice","email":"alice@example.com","password":"password123","role":"instructor"}'
```

### Login and get token

```sh
curl -X POST http://localhost/api/login \
  -H 'Content-Type: application/json' \
  -H 'Accept: application/json' \
  -d '{"email":"alice@example.com","password":"password123"}'
```

### List all class types (authenticated)

```sh
curl -X GET http://localhost/api/class-types \
  -H 'Authorization: Bearer <TOKEN>' \
  -H 'Accept: application/json'
```

### Create a new class type (authenticated)

```sh
curl -X POST http://localhost/api/class-types \
  -H 'Authorization: Bearer <TOKEN>' \
  -H 'Content-Type: application/json' \
  -H 'Accept: application/json' \
  -d '{"name":"Yoga","description":"Stretch and relax","duration":60,"capacity":20,"level":"all","status":"active","color":"#fff"}'
```

### Schedule a new class (authenticated)

```sh
curl -X POST http://localhost/api/scheduled-classes \
  -H 'Authorization: Bearer <TOKEN>' \
  -H 'Content-Type: application/json' \
  -H 'Accept: application/json' \
  -d '{"class_type_id":1,"instructor_id":2,"scheduled_at":"2025-07-01T10:00:00","capacity":15,"status":"scheduled","location":"Studio 1","description":"Morning yoga"}'
```

### Get current user's profile

```sh
curl -X GET http://localhost/api/me \
  -H 'Authorization: Bearer <TOKEN>' \
  -H 'Accept: application/json'
```

### Update current user's profile

```sh
curl -X PUT http://localhost/api/me \
  -H 'Authorization: Bearer <TOKEN>' \
  -H 'Content-Type: application/json' \
  -H 'Accept: application/json' \
  -d '{"name":"Alice Updated","email":"alice2@example.com"}'
```

### Change password

```sh
curl -X PUT http://localhost/api/me/password \
  -H 'Authorization: Bearer <TOKEN>' \
  -H 'Content-Type: application/json' \
  -H 'Accept: application/json' \
  -d '{"current_password":"password123","new_password":"newpass456"}'
```

> Replace `<TOKEN>` with the token received from the login or register response.
