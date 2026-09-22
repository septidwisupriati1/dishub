# KIR Antrean API Documentation

**Version**: 1.0.0  
**Base URL**: `http://localhost/kir-antrean/api`  
**Authentication**: Bearer Token (Laravel Sanctum)

---

## Table of Contents

1. [Authentication](#authentication)
2. [Vehicles](#vehicles)
3. [Queues](#queues)
4. [Test Results](#test-results)
5. [Reports & Statistics](#reports--statistics)
6. [Test Schedules](#test-schedules-admin-only)
7. [WhatsApp Configuration](#whatsapp-configuration-admin-only)
8. [Error Handling](#error-handling)
9. [Response Format](#response-format)

---

## Authentication

### Overview
The API uses Laravel Sanctum for authentication. All protected endpoints require a bearer token in the `Authorization` header.

**Required Headers**:
```
Authorization: Bearer <token>
Content-Type: application/json
Accept: application/json
```

---

### Register User

Create a new user account.

**Endpoint**: `POST /auth/register`  
**Authentication**: ❌ Not Required  
**Rate Limit**: 60 requests/minute

**Request Body**:
```json
{
  "name": "John Doe",
  "email": "john@example.com",
  "phone": "08123456789",
  "role": "peserta",
  "password": "password123",
  "password_confirmation": "password123"
}
```

**Request Parameters**:
| Parameter | Type | Required | Description |
|-----------|------|----------|-------------|
| name | string | Yes | User's full name (max 255) |
| email | string | Yes | Unique email address |
| phone | string | Yes | Valid Indonesian phone (0812-0899, +62812-+62899, 9-12 digits) |
| role | string | Yes | Either "peserta" or "penguji" |
| password | string | Yes | Password (min 6 characters) |
| password_confirmation | string | Yes | Must match password |

**Response** (201 Created):
```json
{
  "success": true,
  "data": {
    "id": 1,
    "name": "John Doe",
    "email": "john@example.com",
    "phone": "08123456789",
    "role": "peserta",
    "is_active": true,
    "token": "1|AbCdEfGhIjKlMnOpQrStUvWxYz..."
  },
  "message": "User registered successfully",
  "timestamp": "2026-05-05T10:30:00Z"
}
```

**Error Response** (422 Unprocessable Entity):
```json
{
  "success": false,
  "message": "Validation failed",
  "errors": {
    "email": ["The email has already been taken."],
    "phone": ["The phone must be a valid phone number."]
  },
  "timestamp": "2026-05-05T10:30:00Z"
}
```

---

### Login

Authenticate user and get API token.

**Endpoint**: `POST /auth/login`  
**Authentication**: ❌ Not Required  
**Rate Limit**: 60 requests/minute

**Request Body**:
```json
{
  "email": "john@example.com",
  "password": "password123"
}
```

**Response** (200 OK):
```json
{
  "success": true,
  "data": {
    "id": 1,
    "name": "John Doe",
    "email": "john@example.com",
    "phone": "08123456789",
    "role": "peserta",
    "is_active": true,
    "token": "1|AbCdEfGhIjKlMnOpQrStUvWxYz..."
  },
  "message": "Login successful",
  "timestamp": "2026-05-05T10:30:00Z"
}
```

---

### Get Current User

Retrieve authenticated user's profile.

**Endpoint**: `GET /auth/user`  
**Authentication**: ✅ Required (Bearer Token)  
**Rate Limit**: 200 requests/minute

**Response** (200 OK):
```json
{
  "success": true,
  "data": {
    "id": 1,
    "name": "John Doe",
    "email": "john@example.com",
    "phone": "08123456789",
    "role": "peserta",
    "nip": null,
    "is_active": true,
    "last_login_at": "2026-05-05T10:30:00Z"
  },
  "timestamp": "2026-05-05T10:30:00Z"
}
```

---

### Update Profile

Update user profile information.

**Endpoint**: `PUT /auth/profile`  
**Authentication**: ✅ Required  
**Rate Limit**: 200 requests/minute

**Request Body**:
```json
{
  "name": "John Doe Updated",
  "phone": "08987654321"
}
```

**Response** (200 OK):
```json
{
  "success": true,
  "data": {
    "id": 1,
    "name": "John Doe Updated",
    "phone": "08987654321"
  },
  "message": "Profile updated successfully",
  "timestamp": "2026-05-05T10:30:00Z"
}
```

---

### Change Password

Change user password.

**Endpoint**: `PUT /auth/change-password`  
**Authentication**: ✅ Required  
**Rate Limit**: 60 requests/minute

**Request Body**:
```json
{
  "current_password": "password123",
  "new_password": "newpassword123",
  "new_password_confirmation": "newpassword123"
}
```

**Response** (200 OK):
```json
{
  "success": true,
  "message": "Password changed successfully",
  "timestamp": "2026-05-05T10:30:00Z"
}
```

---

### Logout

Invalidate current API token.

**Endpoint**: `POST /auth/logout`  
**Authentication**: ✅ Required  
**Rate Limit**: 200 requests/minute

**Response** (200 OK):
```json
{
  "success": true,
  "message": "Logout successful",
  "timestamp": "2026-05-05T10:30:00Z"
}
```

---

## Vehicles

### Overview
Manage vehicles for testing. Peserta can only manage their own vehicles, admin can manage all.

---

### List Vehicles

Get paginated list of vehicles.

**Endpoint**: `GET /vehicles`  
**Authentication**: ✅ Required  
**Authorization**: Peserta (own vehicles), Admin (all vehicles)  
**Rate Limit**: 200 requests/minute

**Query Parameters**:
| Parameter | Type | Default | Description |
|-----------|------|---------|-------------|
| page | integer | 1 | Page number |
| per_page | integer | 15 | Items per page (max 50) |

**Response** (200 OK):
```json
{
  "success": true,
  "data": [
    {
      "id": 1,
      "user_id": 1,
      "vehicle_number": "B1234CD",
      "vehicle_type": "sedan",
      "brand": "Toyota",
      "model": "Camry",
      "year": 2022,
      "color": "Silver",
      "engine_number": "1234567890",
      "chassis_number": "ABCD1234567890",
      "test_count": 2,
      "last_test_date": "2026-04-20T00:00:00Z",
      "status": "active",
      "created_at": "2026-04-10T10:00:00Z",
      "updated_at": "2026-04-20T10:00:00Z"
    }
  ],
  "pagination": {
    "total": 10,
    "count": 1,
    "per_page": 15,
    "current_page": 1,
    "last_page": 1
  },
  "timestamp": "2026-05-05T10:30:00Z"
}
```

---

### Create Vehicle

Create new vehicle (peserta only).

**Endpoint**: `POST /vehicles`  
**Authentication**: ✅ Required  
**Authorization**: Peserta only  
**Rate Limit**: 60 requests/minute

**Request Body**:
```json
{
  "vehicle_number": "B1234CD",
  "vehicle_type": "sedan",
  "brand": "Toyota",
  "model": "Camry",
  "year": 2022,
  "color": "Silver",
  "engine_number": "1234567890",
  "chassis_number": "ABCD1234567890"
}
```

**Request Parameters**:
| Parameter | Type | Required | Description |
|-----------|------|----------|-------------|
| vehicle_number | string | Yes | Unique license plate (max 20) |
| vehicle_type | string | Yes | sedan, suv, truck, motorcycle, etc |
| brand | string | Yes | Vehicle brand (max 100) |
| model | string | Yes | Vehicle model (max 100) |
| year | integer | Yes | Production year |
| color | string | Yes | Vehicle color (max 50) |
| engine_number | string | Yes | Engine number (max 100) |
| chassis_number | string | Yes | Chassis number (max 100) |

**Response** (201 Created):
```json
{
  "success": true,
  "data": {
    "id": 1,
    "user_id": 1,
    "vehicle_number": "B1234CD",
    "vehicle_type": "sedan",
    "brand": "Toyota",
    "model": "Camry",
    "year": 2022,
    "color": "Silver",
    "engine_number": "1234567890",
    "chassis_number": "ABCD1234567890",
    "test_count": 0,
    "status": "active",
    "created_at": "2026-05-05T10:30:00Z"
  },
  "message": "Vehicle created successfully",
  "timestamp": "2026-05-05T10:30:00Z"
}
```

---

### Get Vehicle Detail

Retrieve specific vehicle information.

**Endpoint**: `GET /vehicles/{vehicle_id}`  
**Authentication**: ✅ Required  
**Rate Limit**: 200 requests/minute

**URL Parameters**:
| Parameter | Type | Description |
|-----------|------|-------------|
| vehicle_id | integer | Vehicle ID |

**Response** (200 OK):
```json
{
  "success": true,
  "data": {
    "id": 1,
    "user_id": 1,
    "vehicle_number": "B1234CD",
    "vehicle_type": "sedan",
    "brand": "Toyota",
    "model": "Camry",
    "year": 2022,
    "color": "Silver",
    "engine_number": "1234567890",
    "chassis_number": "ABCD1234567890",
    "test_count": 2,
    "last_test_date": "2026-04-20T00:00:00Z",
    "status": "active",
    "created_at": "2026-04-10T10:00:00Z",
    "updated_at": "2026-04-20T10:00:00Z"
  },
  "timestamp": "2026-05-05T10:30:00Z"
}
```

---

### Update Vehicle

Update vehicle information.

**Endpoint**: `PUT /vehicles/{vehicle_id}`  
**Authentication**: ✅ Required  
**Authorization**: Peserta (own vehicle), Admin (all vehicles)  
**Rate Limit**: 60 requests/minute

**Request Body**:
```json
{
  "vehicle_number": "B1234CD",
  "color": "Red",
  "status": "active"
}
```

**Response** (200 OK):
```json
{
  "success": true,
  "data": {
    "id": 1,
    "vehicle_number": "B1234CD",
    "color": "Red",
    "status": "active",
    "updated_at": "2026-05-05T10:30:00Z"
  },
  "message": "Vehicle updated successfully",
  "timestamp": "2026-05-05T10:30:00Z"
}
```

---

### Delete Vehicle

Delete vehicle (peserta own, admin any).

**Endpoint**: `DELETE /vehicles/{vehicle_id}`  
**Authentication**: ✅ Required  
**Authorization**: Peserta (own vehicle), Admin (all vehicles)  
**Rate Limit**: 60 requests/minute

**Response** (200 OK):
```json
{
  "success": true,
  "message": "Vehicle deleted successfully",
  "timestamp": "2026-05-05T10:30:00Z"
}
```

---

### Get Vehicle Test History

Get all test results for a vehicle.

**Endpoint**: `GET /vehicles/{vehicle_id}/test-history`  
**Authentication**: ✅ Required  
**Rate Limit**: 200 requests/minute

**Response** (200 OK):
```json
{
  "success": true,
  "data": [
    {
      "id": 1,
      "vehicle_id": 1,
      "queue_id": 5,
      "penguji_id": 2,
      "emission_status": "pass",
      "brake_status": "pass",
      "light_status": "pass",
      "horn_status": "pass",
      "suspension_status": "pass",
      "tire_status": "pass",
      "overall_status": "pass",
      "tested_at": "2026-04-20T10:00:00Z"
    }
  ],
  "timestamp": "2026-05-05T10:30:00Z"
}
```

---

## Queues

### Overview
Manage vehicle test queues. Peserta create and manage their own queues, penguji/admin call and manage queues.

---

### List Queues

Get paginated list of queues.

**Endpoint**: `GET /queues`  
**Authentication**: ✅ Required  
**Authorization**: Peserta (own queues), Penguji/Admin (all queues)  
**Rate Limit**: 200 requests/minute

**Query Parameters**:
| Parameter | Type | Default | Description |
|-----------|------|---------|-------------|
| page | integer | 1 | Page number |
| per_page | integer | 15 | Items per page |
| status | string | - | Filter by status: waiting, in_progress, completed, canceled |

**Response** (200 OK):
```json
{
  "success": true,
  "data": [
    {
      "id": 1,
      "vehicle_id": 1,
      "user_id": 1,
      "test_schedule_id": 1,
      "queue_date": "2026-05-10",
      "queue_number": "A001",
      "status": "waiting",
      "notes": "First time testing",
      "called_at": null,
      "started_at": null,
      "completed_at": null,
      "vehicle": {
        "id": 1,
        "vehicle_number": "B1234CD",
        "brand": "Toyota",
        "model": "Camry"
      },
      "user": {
        "id": 1,
        "name": "John Doe"
      },
      "created_at": "2026-05-01T10:00:00Z",
      "updated_at": "2026-05-01T10:00:00Z"
    }
  ],
  "pagination": {
    "total": 25,
    "count": 15,
    "per_page": 15,
    "current_page": 1,
    "last_page": 2
  },
  "timestamp": "2026-05-05T10:30:00Z"
}
```

---

### Create Queue

Create new queue entry (peserta only).

**Endpoint**: `POST /queues`  
**Authentication**: ✅ Required  
**Authorization**: Peserta only  
**Rate Limit**: 60 requests/minute

**Request Body**:
```json
{
  "vehicle_id": 1,
  "test_schedule_id": 1,
  "notes": "First time testing"
}
```

**Request Parameters**:
| Parameter | Type | Required | Description |
|-----------|------|----------|-------------|
| vehicle_id | integer | Yes | ID of vehicle to test |
| test_schedule_id | integer | Yes | ID of test schedule |
| notes | string | No | Additional notes |

**Response** (201 Created):
```json
{
  "success": true,
  "data": {
    "id": 1,
    "vehicle_id": 1,
    "user_id": 1,
    "test_schedule_id": 1,
    "queue_date": "2026-05-10",
    "queue_number": "A001",
    "status": "waiting",
    "notes": "First time testing",
    "created_at": "2026-05-05T10:30:00Z"
  },
  "message": "Queue created successfully",
  "timestamp": "2026-05-05T10:30:00Z"
}
```

---

### Get Queue Detail

Retrieve specific queue information.

**Endpoint**: `GET /queues/{queue_id}`  
**Authentication**: ✅ Required  
**Rate Limit**: 200 requests/minute

**Response** (200 OK):
```json
{
  "success": true,
  "data": {
    "id": 1,
    "vehicle_id": 1,
    "user_id": 1,
    "test_schedule_id": 1,
    "queue_date": "2026-05-10",
    "queue_number": "A001",
    "status": "waiting",
    "notes": "First time testing",
    "called_at": null,
    "started_at": null,
    "completed_at": null,
    "vehicle": {
      "id": 1,
      "vehicle_number": "B1234CD",
      "brand": "Toyota",
      "model": "Camry",
      "color": "Silver"
    },
    "user": {
      "id": 1,
      "name": "John Doe",
      "email": "john@example.com"
    },
    "testSchedule": {
      "id": 1,
      "test_date": "2026-05-10",
      "capacity": 20
    },
    "created_at": "2026-05-01T10:00:00Z"
  },
  "timestamp": "2026-05-05T10:30:00Z"
}
```

---

### Call Queue

Call queue for testing (penguji/admin only). Changes status from 'waiting' to 'in_progress'.

**Endpoint**: `POST /queues/{queue_id}/call`  
**Authentication**: ✅ Required  
**Authorization**: Penguji/Admin only  
**Rate Limit**: 100 requests/minute

**Request Body**: Empty object

**Response** (200 OK):
```json
{
  "success": true,
  "data": {
    "id": 1,
    "status": "in_progress",
    "called_at": "2026-05-05T10:30:00Z",
    "queue_number": "A001"
  },
  "message": "Queue called successfully",
  "timestamp": "2026-05-05T10:30:00Z"
}
```

---

### Cancel Queue

Cancel queue (peserta own, admin any).

**Endpoint**: `POST /queues/{queue_id}/cancel`  
**Authentication**: ✅ Required  
**Authorization**: Peserta (own queue), Admin (all queues)  
**Rate Limit**: 60 requests/minute

**Request Body**:
```json
{
  "reason": "Engine problem"
}
```

**Response** (200 OK):
```json
{
  "success": true,
  "data": {
    "id": 1,
    "status": "canceled",
    "notes": "Engine problem"
  },
  "message": "Queue canceled successfully",
  "timestamp": "2026-05-05T10:30:00Z"
}
```

---

### Get Queue Statistics

Get queue statistics.

**Endpoint**: `GET /queues/stats`  
**Authentication**: ✅ Required  
**Rate Limit**: 200 requests/minute

**Response** (200 OK):
```json
{
  "success": true,
  "data": {
    "total_queues": 150,
    "waiting": 45,
    "in_progress": 5,
    "completed": 95,
    "canceled": 5,
    "today": {
      "total": 20,
      "waiting": 8,
      "in_progress": 2,
      "completed": 10
    }
  },
  "timestamp": "2026-05-05T10:30:00Z"
}
```

---

## Test Results

### Overview
Record and retrieve vehicle test results. Created by penguji/admin after testing.

---

### List Test Results

Get paginated list of test results.

**Endpoint**: `GET /test-results`  
**Authentication**: ✅ Required  
**Rate Limit**: 200 requests/minute

**Query Parameters**:
| Parameter | Type | Default | Description |
|-----------|------|---------|-------------|
| page | integer | 1 | Page number |
| per_page | integer | 15 | Items per page |

**Response** (200 OK):
```json
{
  "success": true,
  "data": [
    {
      "id": 1,
      "queue_id": 1,
      "vehicle_id": 1,
      "penguji_id": 2,
      "emission_status": "pass",
      "emission_notes": "Emission within limit",
      "brake_status": "pass",
      "brake_notes": "Brake system OK",
      "light_status": "pass",
      "light_notes": "All lights working",
      "horn_status": "pass",
      "horn_notes": "Horn functional",
      "suspension_status": "pass",
      "suspension_notes": "Suspension good",
      "tire_status": "fail",
      "tire_notes": "Left tire worn out",
      "overall_status": "fail",
      "overall_notes": "Vehicle failed due to tire condition",
      "tested_at": "2026-04-20T10:00:00Z",
      "created_at": "2026-04-20T10:05:00Z"
    }
  ],
  "pagination": {
    "total": 85,
    "count": 15,
    "per_page": 15,
    "current_page": 1,
    "last_page": 6
  },
  "timestamp": "2026-05-05T10:30:00Z"
}
```

---

### Create Test Result

Create test result for a queue (penguji/admin only).

**Endpoint**: `POST /test-results`  
**Authentication**: ✅ Required  
**Authorization**: Penguji/Admin only  
**Rate Limit**: 60 requests/minute

**Request Body**:
```json
{
  "queue_id": 1,
  "vehicle_id": 1,
  "emission_status": "pass",
  "emission_notes": "Emission within limit",
  "brake_status": "pass",
  "brake_notes": "Brake system OK",
  "light_status": "pass",
  "light_notes": "All lights working",
  "horn_status": "pass",
  "horn_notes": "Horn functional",
  "suspension_status": "pass",
  "suspension_notes": "Suspension good",
  "tire_status": "fail",
  "tire_notes": "Left tire worn out",
  "overall_status": "fail",
  "overall_notes": "Vehicle failed due to tire condition"
}
```

**Request Parameters**:
| Parameter | Type | Required | Status Options | Description |
|-----------|------|----------|---|-------------|
| queue_id | integer | Yes | - | Queue ID |
| vehicle_id | integer | Yes | - | Vehicle ID |
| emission_status | string | Yes | pass, fail | Emission test status |
| emission_notes | string | No | - | Emission notes |
| brake_status | string | Yes | pass, fail | Brake test status |
| brake_notes | string | No | - | Brake notes |
| light_status | string | Yes | pass, fail | Light test status |
| light_notes | string | No | - | Light notes |
| horn_status | string | Yes | pass, fail | Horn test status |
| horn_notes | string | No | - | Horn notes |
| suspension_status | string | Yes | pass, fail | Suspension test status |
| suspension_notes | string | No | - | Suspension notes |
| tire_status | string | Yes | pass, fail | Tire test status |
| tire_notes | string | No | - | Tire notes |
| overall_status | string | Yes | pass, fail | Overall test result |
| overall_notes | string | No | - | Overall notes |

**Response** (201 Created):
```json
{
  "success": true,
  "data": {
    "id": 1,
    "queue_id": 1,
    "vehicle_id": 1,
    "penguji_id": 2,
    "emission_status": "pass",
    "brake_status": "pass",
    "light_status": "pass",
    "horn_status": "pass",
    "suspension_status": "pass",
    "tire_status": "fail",
    "overall_status": "fail",
    "tested_at": "2026-05-05T10:30:00Z",
    "created_at": "2026-05-05T10:30:00Z"
  },
  "message": "Test result created successfully",
  "timestamp": "2026-05-05T10:30:00Z"
}
```

---

### Get Test Result Detail

Retrieve specific test result.

**Endpoint**: `GET /test-results/{test_result_id}`  
**Authentication**: ✅ Required  
**Rate Limit**: 200 requests/minute

**Response** (200 OK):
```json
{
  "success": true,
  "data": {
    "id": 1,
    "queue_id": 1,
    "vehicle_id": 1,
    "penguji_id": 2,
    "emission_status": "pass",
    "emission_notes": "Emission within limit",
    "brake_status": "pass",
    "brake_notes": "Brake system OK",
    "light_status": "pass",
    "light_notes": "All lights working",
    "horn_status": "pass",
    "horn_notes": "Horn functional",
    "suspension_status": "pass",
    "suspension_notes": "Suspension good",
    "tire_status": "fail",
    "tire_notes": "Left tire worn out",
    "overall_status": "fail",
    "overall_notes": "Vehicle failed due to tire condition",
    "tested_at": "2026-04-20T10:00:00Z",
    "vehicle": {
      "id": 1,
      "vehicle_number": "B1234CD",
      "brand": "Toyota",
      "model": "Camry"
    },
    "penguji": {
      "id": 2,
      "name": "Ahmad Penguji",
      "nip": "123456789"
    },
    "created_at": "2026-04-20T10:05:00Z"
  },
  "timestamp": "2026-05-05T10:30:00Z"
}
```

---

## Reports & Statistics

### Overview
Get statistics and reports for penguji and admin users.

---

### Get Penguji Daily Statistics

Get daily statistics for logged-in penguji.

**Endpoint**: `GET /penguji/daily-stats`  
**Authentication**: ✅ Required  
**Authorization**: Penguji only  
**Rate Limit**: 200 requests/minute

**Response** (200 OK):
```json
{
  "success": true,
  "data": {
    "total_tests": 15,
    "passed": 12,
    "failed": 3,
    "pass_rate": 80.0,
    "by_component": {
      "emission": {
        "pass": 15,
        "fail": 0
      },
      "brake": {
        "pass": 14,
        "fail": 1
      },
      "light": {
        "pass": 15,
        "fail": 0
      },
      "horn": {
        "pass": 13,
        "fail": 2
      },
      "suspension": {
        "pass": 12,
        "fail": 3
      },
      "tire": {
        "pass": 12,
        "fail": 3
      }
    },
    "today": "2026-05-05"
  },
  "timestamp": "2026-05-05T10:30:00Z"
}
```

---

### Get Admin Daily Report

Get comprehensive daily report (admin only).

**Endpoint**: `GET /admin/daily-report`  
**Authentication**: ✅ Required  
**Authorization**: Admin only  
**Rate Limit**: 200 requests/minute

**Response** (200 OK):
```json
{
  "success": true,
  "data": {
    "total_queues": 150,
    "completed_queues": 95,
    "canceled_queues": 5,
    "pending_queues": 50,
    "total_tests": 95,
    "passed_tests": 76,
    "failed_tests": 19,
    "pass_rate": 80.0,
    "examiner_stats": [
      {
        "name": "Ahmad Penguji",
        "total": 15,
        "pass": 12,
        "fail": 3
      },
      {
        "name": "Siti Penguji",
        "total": 20,
        "pass": 18,
        "fail": 2
      }
    ],
    "schedule_stats": [
      {
        "date": "2026-05-05",
        "total_queues": 20,
        "completed": 18
      }
    ],
    "date": "2026-05-05"
  },
  "timestamp": "2026-05-05T10:30:00Z"
}
```

---

## Test Schedules (Admin Only)

### Overview
Manage test schedule dates. Admin only.

---

### List Test Schedules

Get paginated list of test schedules.

**Endpoint**: `GET /test-schedules`  
**Authentication**: ✅ Required  
**Authorization**: Admin only  
**Rate Limit**: 200 requests/minute

**Query Parameters**:
| Parameter | Type | Default | Description |
|-----------|------|---------|-------------|
| page | integer | 1 | Page number |
| per_page | integer | 15 | Items per page |

**Response** (200 OK):
```json
{
  "success": true,
  "data": [
    {
      "id": 1,
      "test_date": "2026-05-10",
      "capacity": 20,
      "description": "Regular testing session",
      "created_at": "2026-04-15T10:00:00Z",
      "updated_at": "2026-04-15T10:00:00Z"
    }
  ],
  "pagination": {
    "total": 30,
    "count": 15,
    "per_page": 15,
    "current_page": 1,
    "last_page": 2
  },
  "timestamp": "2026-05-05T10:30:00Z"
}
```

---

### Create Test Schedule

Create new test schedule.

**Endpoint**: `POST /test-schedules`  
**Authentication**: ✅ Required  
**Authorization**: Admin only  
**Rate Limit**: 60 requests/minute

**Request Body**:
```json
{
  "test_date": "2026-05-15",
  "capacity": 20,
  "description": "Regular testing session"
}
```

**Request Parameters**:
| Parameter | Type | Required | Description |
|-----------|------|----------|-------------|
| test_date | date | Yes | Test date (format: Y-m-d, must be unique) |
| capacity | integer | Yes | Number of slots available (1-100) |
| description | string | No | Description of the session |

**Response** (201 Created):
```json
{
  "success": true,
  "data": {
    "id": 1,
    "test_date": "2026-05-15",
    "capacity": 20,
    "description": "Regular testing session",
    "created_at": "2026-05-05T10:30:00Z"
  },
  "message": "Test schedule created successfully",
  "timestamp": "2026-05-05T10:30:00Z"
}
```

---

### Get Test Schedule Detail

Retrieve specific test schedule.

**Endpoint**: `GET /test-schedules/{schedule_id}`  
**Authentication**: ✅ Required  
**Authorization**: Admin only  
**Rate Limit**: 200 requests/minute

**Response** (200 OK):
```json
{
  "success": true,
  "data": {
    "id": 1,
    "test_date": "2026-05-15",
    "capacity": 20,
    "description": "Regular testing session",
    "queue_count": 18,
    "available_slots": 2,
    "queues": [
      {
        "id": 1,
        "queue_number": "A001",
        "vehicle_number": "B1234CD",
        "status": "waiting"
      }
    ],
    "created_at": "2026-04-15T10:00:00Z"
  },
  "timestamp": "2026-05-05T10:30:00Z"
}
```

---

### Update Test Schedule

Update test schedule.

**Endpoint**: `PUT /test-schedules/{schedule_id}`  
**Authentication**: ✅ Required  
**Authorization**: Admin only  
**Rate Limit**: 60 requests/minute

**Request Body**:
```json
{
  "capacity": 25,
  "description": "Extended testing session"
}
```

**Response** (200 OK):
```json
{
  "success": true,
  "data": {
    "id": 1,
    "test_date": "2026-05-15",
    "capacity": 25,
    "description": "Extended testing session",
    "updated_at": "2026-05-05T10:30:00Z"
  },
  "message": "Test schedule updated successfully",
  "timestamp": "2026-05-05T10:30:00Z"
}
```

---

### Delete Test Schedule

Delete test schedule.

**Endpoint**: `DELETE /test-schedules/{schedule_id}`  
**Authentication**: ✅ Required  
**Authorization**: Admin only  
**Rate Limit**: 60 requests/minute

**Response** (200 OK):
```json
{
  "success": true,
  "message": "Test schedule deleted successfully",
  "timestamp": "2026-05-05T10:30:00Z"
}
```

---

## WhatsApp Configuration (Admin Only)

### Overview
Configure WhatsApp providers for sending notifications. Supports Twilio, Fonnte, Ultramsg, Wablas.

---

### List WhatsApp Configs

Get all WhatsApp configurations.

**Endpoint**: `GET /whatsapp-configs`  
**Authentication**: ✅ Required  
**Authorization**: Admin only  
**Rate Limit**: 200 requests/minute

**Response** (200 OK):
```json
{
  "success": true,
  "data": [
    {
      "id": 1,
      "provider": "twilio",
      "phone_number": "+6281234567890",
      "is_active": true,
      "created_at": "2026-04-10T10:00:00Z",
      "updated_at": "2026-04-10T10:00:00Z"
    }
  ],
  "timestamp": "2026-05-05T10:30:00Z"
}
```

---

### Create WhatsApp Config

Create new WhatsApp configuration.

**Endpoint**: `POST /whatsapp-configs`  
**Authentication**: ✅ Required  
**Authorization**: Admin only  
**Rate Limit**: 60 requests/minute

**Request Body**:
```json
{
  "provider": "twilio",
  "api_key": "your_api_key",
  "api_secret": "your_api_secret",
  "phone_number": "+6281234567890",
  "is_active": true
}
```

**Request Parameters**:
| Parameter | Type | Required | Options | Description |
|-----------|------|----------|---------|-------------|
| provider | string | Yes | twilio, fonnte, ultramsg, wablas | WhatsApp provider |
| api_key | string | Yes | - | Provider API key |
| api_secret | string | Yes | - | Provider API secret/token |
| phone_number | string | Yes | - | WhatsApp phone number (international format) |
| is_active | boolean | No | - | Set as active provider (default: false) |

**Response** (201 Created):
```json
{
  "success": true,
  "data": {
    "id": 1,
    "provider": "twilio",
    "phone_number": "+6281234567890",
    "is_active": true,
    "created_at": "2026-05-05T10:30:00Z"
  },
  "message": "WhatsApp configuration created successfully",
  "timestamp": "2026-05-05T10:30:00Z"
}
```

---

### Get WhatsApp Config Detail

Retrieve specific WhatsApp configuration.

**Endpoint**: `GET /whatsapp-configs/{config_id}`  
**Authentication**: ✅ Required  
**Authorization**: Admin only  
**Rate Limit**: 200 requests/minute

**Response** (200 OK):
```json
{
  "success": true,
  "data": {
    "id": 1,
    "provider": "twilio",
    "phone_number": "+6281234567890",
    "is_active": true,
    "message_count": 1250,
    "created_at": "2026-04-10T10:00:00Z",
    "updated_at": "2026-04-10T10:00:00Z"
  },
  "timestamp": "2026-05-05T10:30:00Z"
}
```

---

### Update WhatsApp Config

Update WhatsApp configuration.

**Endpoint**: `PUT /whatsapp-configs/{config_id}`  
**Authentication**: ✅ Required  
**Authorization**: Admin only  
**Rate Limit**: 60 requests/minute

**Request Body**:
```json
{
  "phone_number": "+6289876543210",
  "is_active": true
}
```

**Response** (200 OK):
```json
{
  "success": true,
  "data": {
    "id": 1,
    "provider": "twilio",
    "phone_number": "+6289876543210",
    "is_active": true,
    "updated_at": "2026-05-05T10:30:00Z"
  },
  "message": "WhatsApp configuration updated successfully",
  "timestamp": "2026-05-05T10:30:00Z"
}
```

---

### Delete WhatsApp Config

Delete WhatsApp configuration.

**Endpoint**: `DELETE /whatsapp-configs/{config_id}`  
**Authentication**: ✅ Required  
**Authorization**: Admin only  
**Rate Limit**: 60 requests/minute

**Response** (200 OK):
```json
{
  "success": true,
  "message": "WhatsApp configuration deleted successfully",
  "timestamp": "2026-05-05T10:30:00Z"
}
```

---

### Test WhatsApp Connection

Test WhatsApp configuration connectivity.

**Endpoint**: `POST /whatsapp-configs/{config_id}/test`  
**Authentication**: ✅ Required  
**Authorization**: Admin only  
**Rate Limit**: 10 requests/minute

**Response** (200 OK):
```json
{
  "success": true,
  "data": {
    "status": "connected",
    "provider": "twilio",
    "phone_number": "+6281234567890",
    "message": "Connection successful"
  },
  "timestamp": "2026-05-05T10:30:00Z"
}
```

**Error Response** (400 Bad Request):
```json
{
  "success": false,
  "data": {
    "status": "failed",
    "error": "Invalid API credentials"
  },
  "message": "Connection failed",
  "timestamp": "2026-05-05T10:30:00Z"
}
```

---

## Error Handling

### Standard HTTP Status Codes

| Status | Code | Meaning |
|--------|------|---------|
| OK | 200 | Request successful |
| Created | 201 | Resource created successfully |
| Bad Request | 400 | Invalid request parameters |
| Unauthorized | 401 | Missing or invalid authentication token |
| Forbidden | 403 | User lacks required permissions |
| Not Found | 404 | Resource not found |
| Unprocessable Entity | 422 | Validation errors |
| Conflict | 409 | Resource conflict (duplicate, etc) |
| Too Many Requests | 429 | Rate limit exceeded |
| Server Error | 500 | Internal server error |

### Error Response Format

```json
{
  "success": false,
  "message": "Error description",
  "errors": {
    "field_name": ["Error message for this field"]
  },
  "timestamp": "2026-05-05T10:30:00Z"
}
```

### Common Errors

#### Authentication Missing (401)
```json
{
  "success": false,
  "message": "Unauthenticated",
  "timestamp": "2026-05-05T10:30:00Z"
}
```

#### Insufficient Permissions (403)
```json
{
  "success": false,
  "message": "This action is unauthorized",
  "timestamp": "2026-05-05T10:30:00Z"
}
```

#### Validation Error (422)
```json
{
  "success": false,
  "message": "Validation failed",
  "errors": {
    "email": ["The email has already been taken"],
    "phone": ["The phone must be a valid phone number"]
  },
  "timestamp": "2026-05-05T10:30:00Z"
}
```

#### Rate Limit (429)
```json
{
  "success": false,
  "message": "Too many requests. Please try again later",
  "retry_after": 60,
  "timestamp": "2026-05-05T10:30:00Z"
}
```

---

## Response Format

All API responses follow a standardized JSON format:

### Success Response
```json
{
  "success": true,
  "data": {
    "id": 1,
    "name": "Resource name"
  },
  "message": "Operation successful",
  "timestamp": "2026-05-05T10:30:00Z"
}
```

### Paginated Response
```json
{
  "success": true,
  "data": [
    { "id": 1, "name": "Item 1" },
    { "id": 2, "name": "Item 2" }
  ],
  "pagination": {
    "total": 100,
    "count": 2,
    "per_page": 15,
    "current_page": 1,
    "last_page": 7,
    "from": 1,
    "to": 15
  },
  "timestamp": "2026-05-05T10:30:00Z"
}
```

### Error Response
```json
{
  "success": false,
  "message": "Error message",
  "errors": {
    "field": ["Error detail"]
  },
  "timestamp": "2026-05-05T10:30:00Z"
}
```

---

## Rate Limiting

API endpoints are rate-limited based on user role:

| Role | Limit |
|------|-------|
| Guest (Public) | 60 requests/minute |
| Peserta | 200 requests/minute |
| Penguji | 200 requests/minute |
| Admin | 300 requests/minute |

**Rate Limit Headers**:
```
X-RateLimit-Limit: 200
X-RateLimit-Remaining: 185
X-RateLimit-Reset: 1651760000
```

---

## Best Practices

1. **Always include Authorization header** for protected routes
2. **Use pagination** for list endpoints with large datasets
3. **Handle rate limits** gracefully using `Retry-After` header
4. **Validate phone numbers** in format: `0812-0899` or `+62812-+62899`
5. **Store tokens securely** and invalidate after logout
6. **Implement retry logic** with exponential backoff for network failures
7. **Check response timestamps** to ensure data freshness

---

**Last Updated**: 2026-05-05  
**API Version**: 1.0.0
