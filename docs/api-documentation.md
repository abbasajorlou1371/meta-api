# API Documentation

## User Controller Routes

### Base URL: `/api/users`

#### 1. Get Users List
- **Endpoint:** `GET /api/users`
- **Authentication:** Not Required
- **Description:** Retrieves a list of users
- **Response:** List of users

#### 2. Get User Profile
- **Endpoint:** `GET /api/users/{user}/profile`
- **Authentication:** Required (Bearer Token)
- **Middleware:** 
  - `auth:sanctum`
  - `check.profile.limitation`
- **Parameters:**
  - `user` (path parameter): User ID
- **Description:** Retrieves detailed profile information for a specific user
- **Response:** User profile data

#### 3. Get User Wallet
- **Endpoint:** `GET /api/users/{user}/wallet`
- **Authentication:** Required (Bearer Token)
- **Middleware:** `auth:sanctum`
- **Parameters:**
  - `user` (path parameter): User ID
- **Description:** Retrieves wallet information for a specific user
- **Response:** User wallet data

#### 4. Get User Features Count
- **Endpoint:** `GET /api/users/{user}/features/count`
- **Authentication:** Required (Bearer Token)
- **Middleware:** `auth:sanctum`
- **Parameters:**
  - `user` (path parameter): User ID
- **Description:** Retrieves the count of features associated with a user
- **Response:** Features count data

#### 5. Get User Level
- **Endpoint:** `GET /api/users/{user}/level`
- **Authentication:** Required (Bearer Token)
- **Middleware:** `auth:sanctum`
- **Parameters:**
  - `user` (path parameter): User ID
- **Description:** Retrieves the level information for a specific user
- **Response:** User level data

#### 6. Get User Profile Limitations
- **Endpoint:** `GET /api/users/{user}/profile-limitations`
- **Authentication:** Required (Bearer Token)
- **Middleware:** `auth:sanctum`
- **Parameters:**
  - `user` (path parameter): User ID
- **Description:** Retrieves profile limitations for a specific user
- **Response:** Profile limitations data

## Profile Limitation Controller Routes

### Base URL: `/api/profile-limitations`

#### 1. Create Profile Limitation
- **Endpoint:** `POST /api/profile-limitations`
- **Authentication:** Required (Bearer Token)
- **Description:** Creates a new profile limitation
- **Request Body:** Profile limitation data
- **Response:** Created profile limitation data

#### 2. Update Profile Limitation
- **Endpoint:** `PUT /api/profile-limitations/{profileLimitation}`
- **Authentication:** Required (Bearer Token)
- **Parameters:**
  - `profileLimitation` (path parameter): Profile Limitation ID
- **Description:** Updates an existing profile limitation
- **Request Body:** Updated profile limitation data
- **Response:** Updated profile limitation data

#### 3. Delete Profile Limitation
- **Endpoint:** `DELETE /api/profile-limitations/{profileLimitation}`
- **Authentication:** Required (Bearer Token)
- **Parameters:**
  - `profileLimitation` (path parameter): Profile Limitation ID
- **Description:** Deletes a profile limitation
- **Response:** Success message or status

## Authentication
Most endpoints require authentication using Laravel Sanctum. Include the Bearer token in the Authorization header:
```
Authorization: Bearer <your-token>
```

## Error Responses
The API may return the following error responses:
- `401 Unauthorized`: When authentication is required but not provided
- `403 Forbidden`: When the user doesn't have permission to access the resource
- `404 Not Found`: When the requested resource doesn't exist
- `422 Unprocessable Entity`: When the request validation fails

## Tutorial Controller Routes

### Base URL: `/api/v2/tutorials`

#### 1. Get Categories List
- **Endpoint:** `GET /api/v2/tutorials/categories`
- **Authentication:** Not Required
- **Description:** Retrieves a paginated list of video categories
- **Query Parameters:**
  - `count` (optional): Number of categories per page (default: 30)
- **Response:** Paginated collection of video categories

#### 2. Get Category Details
- **Endpoint:** `GET /api/v2/tutorials/categories/{category:slug}`
- **Authentication:** Not Required
- **Parameters:**
  - `category:slug` (path parameter): Category slug
- **Description:** Retrieves detailed information for a specific category including subcategories
- **Response:** Category details with subcategories

#### 3. Get Videos in Category
- **Endpoint:** `GET /api/v2/tutorials/categories/{category:slug}/videos`
- **Authentication:** Not Required
- **Parameters:**
  - `category:slug` (path parameter): Category slug
- **Query Parameters:**
  - `per_page` (optional): Number of videos per page (default: 18)
- **Description:** Retrieves all videos within a specific parent category, including videos from all its subcategories
- **Response:** Paginated collection of videos

**Example Request:**
```
GET /api/v2/tutorials/categories/programming/videos?per_page=20
```

**Example Response:**
```json
{
  "data": [
    {
      "id": 1,
      "title": "Introduction to Laravel",
      "slug": "introduction-to-laravel",
      "image_url": "https://example.com/uploads/video-thumbnail.jpg",
      "description": "Learn the basics of Laravel framework",
      "views_count": 1250,
      "likes_count": 89,
      "dislikes_count": 3,
      "creator": {
        "name": "John Doe",
        "code": "JOHN001",
        "image": "https://example.com/uploads/profile.jpg"
      },
      "category": {
        "name": "Programming",
        "slug": "programming"
      },
      "sub_category": {
        "name": "Web Development",
        "slug": "web-development"
      },
      "video_url": "https://example.com/uploads/video.mp4",
      "created_at": "1402/10/15"
    }
  ],
  "links": {
    "first": "http://localhost/api/v2/tutorials/categories/programming/videos?page=1",
    "last": "http://localhost/api/v2/tutorials/categories/programming/videos?page=5",
    "prev": null,
    "next": "http://localhost/api/v2/tutorials/categories/programming/videos?page=2"
  },
  "meta": {
    "current_page": 1,
    "from": 1,
    "last_page": 5,
    "per_page": 20,
    "to": 20,
    "total": 95
  }
}
```

#### 4. Get Subcategory Details
- **Endpoint:** `GET /api/v2/tutorials/categories/{category:slug}/{subCategory:slug}`
- **Authentication:** Not Required
- **Parameters:**
  - `category:slug` (path parameter): Parent category slug
  - `subCategory:slug` (path parameter): Subcategory slug
- **Description:** Retrieves videos within a specific subcategory
- **Response:** Subcategory details with videos

#### 5. Get All Tutorials
- **Endpoint:** `GET /api/v2/tutorials`
- **Authentication:** Not Required
- **Description:** Retrieves a paginated list of all tutorials
- **Response:** Paginated collection of tutorials

#### 6. Get Tutorial Details
- **Endpoint:** `GET /api/v2/tutorials/{video:slug}`
- **Authentication:** Not Required
- **Parameters:**
  - `video:slug` (path parameter): Video slug
- **Description:** Retrieves detailed information for a specific tutorial
- **Response:** Tutorial details

#### 7. Search Tutorials
- **Endpoint:** `POST /api/v2/tutorials/search`
- **Authentication:** Not Required
- **Request Body:**
  ```json
  {
    "searchTerm": "laravel tutorial"
  }
  ```
- **Description:** Searches tutorials by title
- **Response:** List of matching tutorials

#### 8. Tutorial Interactions
- **Endpoint:** `POST /api/v2/tutorials/{video}/interactions`
- **Authentication:** Required (Bearer Token)
- **Parameters:**
  - `video` (path parameter): Video ID
- **Request Body:**
  ```json
  {
    "liked": true
  }
  ```
- **Description:** Like or dislike a tutorial
- **Response:** Success status

## Rate Limiting
The API implements rate limiting to prevent abuse. Please check the response headers for rate limit information. 
