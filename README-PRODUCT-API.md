# Product Management API Documentation

This document provides comprehensive details about the Product Management API endpoints, which implement Amazon's approach to product variations with parent-child relationships and advanced image handling.

## Table of Contents

1. [Overview](#overview)
2. [Database Structure](#database-structure)
3. [API Endpoints](#api-endpoints)
4. [Request Parameters](#request-parameters)
5. [Response Format](#response-format)
6. [Image Handling](#image-handling)
7. [Examples](#examples)

## Overview

The Product Management API follows Amazon's approach to product variations:

- **Parent-Child Relationships**: Products are organized with parent products (main listings) and child products (variations)
- **Variation Management**: Similar items (different colors, sizes, etc.) are grouped under a single product listing
- **Smart Image Handling**: Variation-specific images that update based on customer selection
- **Default Images**: Shows parent product's main image or first child variation's image as default
- **Image Inheritance**: Child variations can inherit parent images if they have no unique images

## Database Structure

The database schema includes:

- **Products**: Main product information with parent-child relationships
- **Product Variants**: Specific variations of products (e.g., different colors, sizes)
- **Product Images**: Images for both products and their variants
- **Attributes**: Product characteristics (e.g., color, size)
- **Attribute Values**: Specific values for attributes (e.g., red, large)

## API Endpoints

| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/api/admin/products` | List all products |
| GET | `/api/admin/products/parents` | List only parent products |
| GET | `/api/admin/products/{id}` | Get a specific product with its variants and images |
| POST | `/api/admin/products` | Create a new product |
| PUT | `/api/admin/products/{id}` | Update an existing product |
| DELETE | `/api/admin/products/{id}` | Delete a product |
| GET | `/api/admin/products/{id}/summary` | Get product summary information |
| POST | `/api/admin/products/{id}/discount` | Apply discount to a product |
| PUT | `/api/admin/products/{id}/discount` | Update product discount |
| DELETE | `/api/admin/products/{id}/discount` | Remove discount from a product |
| GET | `/api/admin/products/{id}/variants` | List all variants for a product |
| GET | `/api/admin/products/{id}/variants/{variantId}` | Get a specific variant |
| POST | `/api/admin/products/{id}/variants` | Create a new variant for a product |
| PUT | `/api/admin/products/{id}/variants/{variantId}` | Update a variant |
| DELETE | `/api/admin/products/{id}/variants/{variantId}` | Delete a variant |
| PATCH | `/api/admin/products/{id}/variants/{variantId}/stock` | Update variant stock |
| PATCH | `/api/admin/products/{id}/variants/{variantId}/default` | Set a variant as the default |

### Attribute Management (Independent)

| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/api/admin/attributes` | List all attributes |
| GET | `/api/admin/attributes/{id}` | Get a specific attribute with its values |
| POST | `/api/admin/attributes` | Create a new attribute |
| PUT | `/api/admin/attributes/{id}` | Update an attribute |
| DELETE | `/api/admin/attributes/{id}` | Delete an attribute |

### Attribute Value Management (Independent)

| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/api/admin/attributes/{attributeId}/values` | List all values for an attribute |
| GET | `/api/admin/attributes/{attributeId}/values/{valueId}` | Get a specific attribute value |
| POST | `/api/admin/attributes/{attributeId}/values` | Create a new attribute value |
| PUT | `/api/admin/attributes/{attributeId}/values/{valueId}` | Update an attribute value |
| DELETE | `/api/admin/attributes/{attributeId}/values/{valueId}` | Delete an attribute value |

## Request Parameters

### Creating/Updating a Product

```json
{
  "name": "Example Product",
  "description": "Product description",
  "price": 99.99,
  "is_parent": true,
  "parent_id": null,
  "status": "active",
  "categories": [1, 2, 3],
  "tags": [1, 2],
  "images": [
    {
      "file": "[multipart file upload]",
      "alt_text": "Main product image",
      "is_primary": true,
      "sort_order": 0,
      "image_type": "main"
    },
    {
      "file": "[multipart file upload]",
      "alt_text": "Product lifestyle image",
      "is_primary": false,
      "sort_order": 1,
      "image_type": "lifestyle"
    }
  ],
  "variants": [
    {
      "sku": "PROD-RED-L",
      "price": 99.99,
      "extra_price": 0,
      "stock": 100,
      "is_default": true,
      "variant_title": "Red, Large",
      "attributes": [
        {
          "attribute_id": 1, // Color
          "attribute_value_id": 1 // Red
        },
        {
          "attribute_id": 2, // Size
          "attribute_value_id": 3 // Large
        }
      ],
      "images": [
        {
          "file": "[multipart file upload]",
          "alt_text": "Red variant",
          "is_primary": true,
          "sort_order": 0,
          "image_type": "main"
        }
      ]
    },
    {
      "sku": "PROD-BLUE-M",
      "price": 89.99,
      "extra_price": -10,
      "stock": 50,
      "is_default": false,
      "variant_title": "Blue, Medium",
      "attributes": [
        {
          "attribute_id": 1, // Color
          "attribute_value_id": 2 // Blue
        },
        {
          "attribute_id": 2, // Size
          "attribute_value_id": 2 // Medium
        }
      ],
      "images": [
        {
          "file": "[multipart file upload]",
          "alt_text": "Blue variant",
          "is_primary": true,
          "sort_order": 0,
          "image_type": "main"
        }
      ]
    }
  ]
}
```

### Field Descriptions

#### Product Fields

| Field | Type | Required | Description |
|-------|------|----------|-------------|
| `name` | string | Yes | Product name |
| `description` | string | No | Product description |
| `price` | numeric | Yes | Base product price |
| `is_parent` | boolean | No | Whether this is a parent product (default: false) |
| `parent_id` | integer | No | ID of parent product (null for parent products) |
| `status` | string | No | Product status: "active" or "inactive" (default: "active") |
| `categories` | array | No | Array of category IDs |
| `tags` | array | No | Array of tag IDs |
| `images` | array | No | Array of product images |
| `variants` | array | No | Array of product variants (only for parent products) |

#### Image Fields

| Field | Type | Required | Description |
|-------|------|----------|-------------|
| `file` | file | Yes (for new) | Image file (max: 5MB) |
| `id` | integer | Yes (for update) | Image ID when updating existing image |
| `alt_text` | string | No | Alternative text for image |
| `is_primary` | boolean | No | Whether this is the primary image (default: false) |
| `sort_order` | integer | No | Order to display images (default: 0) |
| `image_type` | string | No | Type of image: "main", "thumbnail", "gallery", "lifestyle" (default: "gallery") |

#### Variant Fields

| Field | Type | Required | Description |
|-------|------|----------|-------------|
| `id` | integer | Yes (for update) | Variant ID when updating existing variant |
| `sku` | string | Yes | Stock Keeping Unit (unique identifier) |
| `price` | numeric | No | Variant-specific price (overrides parent price) |
| `extra_price` | numeric | No | Price difference from parent (can be negative) |
| `stock` | integer | No | Available stock (default: 0) |
| `is_default` | boolean | No | Whether this is the default variant (default: false) |
| `variant_title` | string | No | Descriptive title (e.g., "Red, Large") |
| `attributes` | array | No | Array of attribute combinations |
| `images` | array | No | Array of variant-specific images |

#### Attribute Fields

| Field | Type | Required | Description |
|-------|------|----------|-------------|
| `attribute_id` | integer | Yes | ID of the attribute (e.g., color, size) |
| `attribute_value_id` | integer | Yes | ID of the attribute value (e.g., red, large) |

## Response Format

All API responses follow a consistent format:

```json
{
  "success": true,
  "message": "Operation successful message",
  "data": {
    // Resource data
  }
}
```

For errors:

```json
{
  "success": false,
  "message": "Error message",
  "errors": {
    // Validation errors if applicable
  }
}
```

## Image Handling

### Image Upload Process

1. Images must be uploaded as multipart/form-data
2. Each image can have metadata (alt text, primary status, etc.)
3. Both products and variants can have their own images
4. When a variant is selected, its specific images are displayed

### Image Types

- **Main**: Primary product image shown in listings
- **Thumbnail**: Smaller version used in carousels
- **Gallery**: Additional product images
- **Lifestyle**: Images showing the product in use

## Examples

### Creating a Parent Product with Variants

```http
POST /api/admin/products
Content-Type: multipart/form-data
```

Form data should include all product fields and files for images.

### Updating a Product

```http
PUT /api/admin/products/1
Content-Type: multipart/form-data
```

Include only the fields you want to update. For images:
- To update existing images, include the image ID
- To add new images, include the file
- Images not included in the request will be deleted

### Getting Product Summary

```http
GET /api/admin/products/1/summary
```

Returns comprehensive product information including:
- Basic product details
- Discount information and status
- Calculated final price
- Related categories and tags

### Managing Product Variants

#### Listing All Variants for a Product

```http
GET /api/admin/products/1/variants
```

Returns a list of all variants for the specified product, including their attributes and images.

#### Getting a Specific Variant

```http
GET /api/admin/products/1/variants/2
```

Returns detailed information about a specific variant, including:
- Variant details (SKU, price, stock, etc.)
- Attributes and their values
- Images associated with the variant

#### Creating a New Variant

```http
POST /api/admin/products/1/variants
Content-Type: multipart/form-data
```

Request body:
```json
{
  "sku": "PROD-GREEN-XL",
  "price": 109.99,
  "extra_price": 10,
  "stock": 25,
  "is_default": false,
  "variant_title": "Green, Extra Large",
  "attributes": [
    {
      "attribute_id": 1, // Color
      "attribute_value_id": 3 // Green
    },
    {
      "attribute_id": 2, // Size
      "attribute_value_id": 4 // Extra Large
    }
  ],
  "images": [
    {
      "file": "[multipart file upload]",
      "alt_text": "Green variant",
      "is_primary": true,
      "sort_order": 0,
      "image_type": "main"
    }
  ]
}
```

#### Updating a Variant

```http
PUT /api/admin/products/1/variants/2
Content-Type: multipart/form-data
```

Include only the fields you want to update. For images:
- To update existing images, include the image ID
- To add new images, include the file
- Images not included in the request will be deleted

#### Updating Variant Stock

```http
PATCH /api/admin/products/1/variants/2/stock
Content-Type: application/json
```

Request body:
```json
{
  "stock": 50
}
```

This endpoint provides a quick way to update just the stock level of a variant without having to send all variant data.

#### Setting a Variant as Default

```http
PATCH /api/admin/products/1/variants/2/default
```

This endpoint sets the specified variant as the default for the product. The previously default variant will be automatically set to non-default.

#### Deleting a Variant

```http
DELETE /api/admin/products/1/variants/2
```

Deletes the specified variant. Note that a product must always have at least one variant, so you cannot delete the last remaining variant.

## Notes for Frontend Implementation

1. **Image Handling**: When displaying product variations, update the image gallery based on the selected variant
2. **Default Variant**: Always show the default variant (is_default=true) initially
3. **Price Calculation**: Use variant-specific price if available, otherwise calculate from parent price and extra_price
4. **Image Inheritance**: If a variant has no images, use the parent product's images
5. **Form Submission**: Use multipart/form-data for all requests that include image uploads
