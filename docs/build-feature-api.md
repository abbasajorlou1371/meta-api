# Build Feature API Documentation

This documentation covers the endpoints for managing building features in the application.

## Authentication

All endpoints in this section require authentication with Sanctum and verified user status.

```
Route::middleware(['auth:sanctum', 'verified'])
```

## Endpoints

### Get Build Package

Retrieves the building package data for a specific feature.

- **URL:** `/api/v2/features/{feature}/build/package`
- **Method:** `GET`
- **URL Parameters:**
  - `feature`: The ID of the feature
- **Query Parameters:**
  - `page`: Page number (default: 1)
- **Success Response:**
  - **Code:** 200 OK
  - **Content:** JSON object with building package data including required satisfaction and coordinates
- **Error Response:**
  - **Code:** 403 Forbidden
  - **Content:** Error message if user doesn't own the feature
- **Sample Request:**
  ```http
  GET /api/v2/features/123/build/package HTTP/1.1
  Authorization: Bearer {token}
  ```

### Build Feature

Creates a new building on a specific feature.

- **URL:** `/api/v2/features/{feature}/build/{buildingModel:model_id}`
- **Method:** `POST`
- **URL Parameters:**
  - `feature`: The ID of the feature
  - `buildingModel`: The ID of the building model to construct
- **Request Body:** Building feature data (refer to `StartBuildingFeatureRequest`)
- **Success Response:**
  - **Code:** 200 OK
  - **Content:** JSON object with the created building details
- **Error Response:**
  - **Code:** 403 Forbidden
  - **Content:** Error message if user doesn't own the feature
- **Notes:**
  - Uses `withoutScopedBindings()` for the building model parameter
- **Sample Request:**
  ```http
  POST /api/v2/features/123/build/456 HTTP/1.1
  Authorization: Bearer {token}
  Content-Type: application/json
  
  {
    "location_x": 100.5,
    "location_y": 200.3
    // Other building parameters
  }
  ```

### Get Buildings

Retrieves all buildings for a specific feature.

- **URL:** `/api/v2/features/{feature}/build/buildings`
- **Method:** `GET`
- **URL Parameters:**
  - `feature`: The ID of the feature
- **Success Response:**
  - **Code:** 200 OK
  - **Content:** Array of building models with their details
- **Error Response:**
  - **Code:** 403 Forbidden
  - **Content:** Error message if user doesn't own the feature
- **Sample Request:**
  ```http
  GET /api/v2/features/123/build/buildings HTTP/1.1
  Authorization: Bearer {token}
  ```

### Update Building

Updates details for an existing building on a feature.

- **URL:** `/api/v2/features/{feature}/build/buildings/{buildingModel:model_id}`
- **Method:** `PUT`
- **URL Parameters:**
  - `feature`: The ID of the feature
  - `buildingModel`: The ID of the building model to update
- **Request Body:** Updated building data (refer to `UpdateBuildingFeatureRequest`)
- **Success Response:**
  - **Code:** 200 OK
  - **Content:** JSON object with the updated building details
- **Error Response:**
  - **Code:** 403 Forbidden
  - **Content:** Error message if user doesn't own the feature
- **Sample Request:**
  ```http
  PUT /api/v2/features/123/build/buildings/456 HTTP/1.1
  Authorization: Bearer {token}
  Content-Type: application/json
  
  {
    "location_x": 150.5,
    "location_y": 250.3
    // Other updated parameters
  }
  ```

### Delete Building

Removes a building from a feature.

- **URL:** `/api/v2/features/{feature}/build/buildings/{buildingModel:model_id}`
- **Method:** `DELETE`
- **URL Parameters:**
  - `feature`: The ID of the feature
  - `buildingModel`: The ID of the building model to delete
- **Success Response:**
  - **Code:** 200 OK
  - **Content:** Success message
- **Error Response:**
  - **Code:** 403 Forbidden
  - **Content:** Error message if user doesn't own the feature
- **Sample Request:**
  ```http
  DELETE /api/v2/features/123/build/buildings/456 HTTP/1.1
  Authorization: Bearer {token}
  ```

## Models

### Feature
Represents a feature that can have buildings constructed on it.

### BuildingModel
Represents a model of building that can be constructed on a feature.

## Request Validation

The API endpoints use request validation classes to ensure data integrity:

- `StartBuildingFeatureRequest`: Validates data when creating a new building
- `UpdateBuildingFeatureRequest`: Validates data when updating an existing building

## Responses

Responses use the following resource classes:
- `BuildingModelResource`: Formats the building model data for API responses