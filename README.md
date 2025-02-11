## Laravel 11 API Authentication with Sanctum

This project is a Laravel 11 API authentication system using Sanctum. It provides a robust and secure way to handle user authentication for API. The project includes features such as:

- **User Registration and Login**: Users can register and log in to the system using their username or employee_id or email and password. The registration process includes validation to ensure data integrity.
- **Token-Based Authentication**: Utilizes Laravel Sanctum to issue and manage API tokens. Each user receives a unique token upon successful authentication, which is used to access protected routes.
- **Middleware Protection**: Implements middleware to secure API routes, ensuring that only authenticated users can access certain endpoints. This adds an extra layer of security to the application.
- **User Management and Profile Updates**: Allows users to update their profile information, such as name, email, and password based on token abilities.

The goal of this project is to demonstrate how to implement a secure authentication system in a Laravel application using Sanctum, making it easier for developers to integrate similar functionality into their own projects.


### API Endpoints

### Base URL

The base URL for all API endpoints is:

```
http://localhost:8000/api
```
#### Authentication

- **Register**
    - **Endpoint:** `POST /register`
    - **Description:** Registers a new user with the provided username, employee_id, email, and password.
    
- **Login**
    - **Endpoint:** `POST /login`
    - **Description:** Authenticates a user and issues an API token for accessing protected routes.

#### Protected Routes (Requires Authentication)

- **Logout**
    - **Endpoint:** `POST /logout/{tokenId?}`
    - **Description:** If no tokenId is provided logout the authenticated user. If tokenId is provided, logout the specified session.
    - **Parameters:**
        - `tokenId` (optional): The ID of the token to invalidate. Must be a numeric value.

- **Login Sessions**
    - **Endpoint:** `GET /login-sessions`
    - **Description:** Retrieves a list of active login sessions for the authenticated user.

#### User Management

- **Get Current User**
    - **Endpoint:** `GET /users/me`
    - **Description:** Retrieves the profile information of the authenticated user.

- **Change Password**
    - **Endpoint:** `PATCH /users/change-password`
    - **Description:** Allows the authenticated user to change their password.

- **User Resource**
    - **Endpoint:** `apiResource /users`
    - **Description:** Provides standard CRUD operations for user management.
    - **APIs:**
        - **GET /users**
            - **Description:** Retrieve a list of all users.
        - **GET /users/{id}**
            - **Description:** Retrieve a specific user by their ID.
        - **POST /users**
            - **Description:** Create a new user.
        - **PUT /users/{id}**
            - **Description:** Update an existing user by their ID.
        - **DELETE /users/{id}**
            - **Description:** Delete a specific user by their ID(SoftDeletes).
