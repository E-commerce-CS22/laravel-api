# Product Creation Scenario Documentation

This document outlines the complete process for creating a product with variants in the e-commerce system. The scenario involves creating a Samsung Galaxy S24 with specific color and storage variants.

## 1. Create Attributes

First, we need to create the two attributes: "Color" and "Storage".

### Create Color Attribute

**Endpoint:** `POST /api/admin/attributes`  
**Method:** POST  
**Content-Type:** application/json  
**Authorization:** Bearer {token}

**Request Body:**
```json
{
  "name": "Color"
}
```

**Expected Response:**
```json
{
  "success": true,
  "message": "Attribute created successfully",
  "data": {
    "id": 1,
    "name": "Color"
  }
}
```

### Create Storage Attribute

**Endpoint:** `POST /api/admin/attributes`  
**Method:** POST
**Content-Type:** application/json  
**Authorization:** Bearer {token}

**Request Body:**
```json
{
  "name": "Storage"
}
```

**Expected Response:**
```json
{
  "success": true,
  "message": "Attribute created successfully",
  "data": {
    "id": 2,
    "name": "Storage"
  }
}
```

## 2. Create Attribute Values

Next, we need to create the attribute values for both Color and Storage.

### Create Color Attribute Values

#### Create Red Color Value

**Endpoint:** `POST /api/admin/attributes/1/values`  
**Method:** POST  
**Content-Type:** application/json  
**Authorization:** Bearer {token}

**Request Body:**
```json
{
  "name": "Red"
}
```

#### Create Green Color Value

**Endpoint:** `POST /api/admin/attributes/1/values`  
**Method:** POST  
**Content-Type:** application/json  
**Authorization:** Bearer {token}

**Request Body:**
```json
{
  "name": "Green"
}
```

#### Create Blue Color Value

**Endpoint:** `POST /api/admin/attributes/1/values`  
**Method:** POST  
**Content-Type:** application/json  
**Authorization:** Bearer {token}

**Request Body:**
```json
{
  "name": "Blue"
}
```

#### Create Black Color Value

**Endpoint:** `POST /api/admin/attributes/1/values`  
**Method:** POST  
**Content-Type:** application/json  
**Authorization:** Bearer {token}

**Request Body:**
```json
{
  "name": "Black"
}
```

#### Create White Color Value

**Endpoint:** `POST /api/admin/attributes/1/values`  
**Method:** POST  
**Content-Type:** application/json  
**Authorization:** Bearer {token}

**Request Body:**
```json
{
  "name": "White"
}
```

### Create Storage Attribute Values

#### Create 64GB Storage Value

**Endpoint:** `POST /api/admin/attributes/2/values`  
**Method:** POST  
**Content-Type:** application/json  
**Authorization:** Bearer {token}

**Request Body:**
```json
{
  "name": "64"
}
```

#### Create 128GB Storage Value

**Endpoint:** `POST /api/admin/attributes/2/values`  
**Method:** POST  
**Content-Type:** application/json  
**Authorization:** Bearer {token}

**Request Body:**
```json
{
  "name": "128"
}
```

#### Create 256GB Storage Value

**Endpoint:** `POST /api/admin/attributes/2/values`  
**Method:** POST  
**Content-Type:** application/json  
**Authorization:** Bearer {token}

**Request Body:**
```json
{
  "name": "256"
}
```

#### Create 512GB Storage Value

**Endpoint:** `POST /api/admin/attributes/2/values`  
**Method:** POST  
**Content-Type:** application/json  
**Authorization:** Bearer {token}

**Request Body:**
```json
{
  "name": "512"
}
```

#### Create 1024GB Storage Value

**Endpoint:** `POST /api/admin/attributes/2/values`  
**Method:** POST  
**Content-Type:** application/json  
**Authorization:** Bearer {token}

**Request Body:**
```json
{
  "name": "1024"
}
```

## 3. Create the Parent Product (Samsung Galaxy S24)

Now we'll create the parent product with its variants.

**Endpoint:** `POST /api/admin/products`  
**Method:** POST  
**Content-Type:** application/json  
**Authorization:** Bearer {token}

**Request Body:**
```json
{
  "name": "Samsung Galaxy S24",
  "description": "Experience the latest Samsung Galaxy S24 with cutting-edge features and performance.",
  "price": 999.99,
  "is_parent": true,
  "status": "active",
  "variants": [
    {
      "sku": "SGS24-BLACK-256",
      "variant_title": "Samsung Galaxy S24 - Black, 256GB",
      "price": 999.99,
      "stock": 50,
      "is_default": true,
      "attributes": [
        {
          "attribute_id": 1,
          "attribute_value_id": 4
        },
        {
          "attribute_id": 2,
          "attribute_value_id": 8
        }
      ]
    },
    {
      "sku": "SGS24-BLACK-512",
      "variant_title": "Samsung Galaxy S24 - Black, 512GB",
      "price": 1099.99,
      "stock": 30,
      "attributes": [
        {
          "attribute_id": 1,
          "attribute_value_id": 4
        },
        {
          "attribute_id": 2,
          "attribute_value_id": 9
        }
      ]
    },
    {
      "sku": "SGS24-WHITE-256",
      "variant_title": "Samsung Galaxy S24 - White, 256GB",
      "price": 999.99,
      "stock": 45,
      "attributes": [
        {
          "attribute_id": 1,
          "attribute_value_id": 5
        },
        {
          "attribute_id": 2,
          "attribute_value_id": 8
        }
      ]
    },
    {
      "sku": "SGS24-WHITE-512",
      "variant_title": "Samsung Galaxy S24 - White, 512GB",
      "price": 1099.99,
      "stock": 25,
      "attributes": [
        {
          "attribute_id": 1,
          "attribute_value_id": 5
        },
        {
          "attribute_id": 2,
          "attribute_value_id": 9
        }
      ]
    }
  ]
}
```

**Expected Response:**
```json
{
  "success": true,
  "message": "Product created successfully",
  "data": {
    "id": 1,
    "name": "Samsung Galaxy S24",
    "description": "Experience the latest Samsung Galaxy S24 with cutting-edge features and performance.",
    "price": 999.99,
    "is_parent": true,
    "status": "active",
    "variants": [
      {
        "id": 1,
        "sku": "SGS24-BLACK-256",
        "variant_title": "Samsung Galaxy S24 - Black, 256GB",
        "price": 999.99,
        "stock": 50,
        "is_default": true,
        "attributes": [
          {
            "attribute_id": 1,
            "attribute_name": "Color",
            "attribute_value_id": 4,
            "attribute_value_name": "Black"
          },
          {
            "attribute_id": 2,
            "attribute_name": "Storage",
            "attribute_value_id": 8,
            "attribute_value_name": "256"
          }
        ]
      },
      {
        "id": 2,
        "sku": "SGS24-BLACK-512",
        "variant_title": "Samsung Galaxy S24 - Black, 512GB",
        "price": 1099.99,
        "stock": 30,
        "attributes": [
          {
            "attribute_id": 1,
            "attribute_name": "Color",
            "attribute_value_id": 4,
            "attribute_value_name": "Black"
          },
          {
            "attribute_id": 2,
            "attribute_name": "Storage",
            "attribute_value_id": 9,
            "attribute_value_name": "512"
          }
        ]
      },
      {
        "id": 3,
        "sku": "SGS24-WHITE-256",
        "variant_title": "Samsung Galaxy S24 - White, 256GB",
        "price": 999.99,
        "stock": 45,
        "attributes": [
          {
            "attribute_id": 1,
            "attribute_name": "Color",
            "attribute_value_id": 5,
            "attribute_value_name": "White"
          },
          {
            "attribute_id": 2,
            "attribute_name": "Storage",
            "attribute_value_id": 8,
            "attribute_value_name": "256"
          }
        ]
      },
      {
        "id": 4,
        "sku": "SGS24-WHITE-512",
        "variant_title": "Samsung Galaxy S24 - White, 512GB",
        "price": 1099.99,
        "stock": 25,
        "attributes": [
          {
            "attribute_id": 1,
            "attribute_name": "Color",
            "attribute_value_id": 5,
            "attribute_value_name": "White"
          },
          {
            "attribute_id": 2,
            "attribute_name": "Storage",
            "attribute_value_id": 9,
            "attribute_value_name": "512"
          }
        ]
      }
    ]
  }
}
```

## 4. Alternative: Add Variants After Creating the Parent Product

If you prefer to create variants separately after creating the parent product, you can follow these steps:

### Create Parent Product First

**Endpoint:** `POST /api/admin/products`  
**Method:** POST  
**Content-Type:** application/json  
**Authorization:** Bearer {token}

**Request Body:**
```json
{
  "name": "Samsung Galaxy S24",
  "description": "Experience the latest Samsung Galaxy S24 with cutting-edge features and performance.",
  "price": 999.99,
  "is_parent": true,
  "status": "active"
}
```

### Add Variants One by One

#### Add Black 256GB Variant

**Endpoint:** `POST /api/admin/products/{product_id}/variants`  
**Method:** POST  
**Content-Type:** application/json  
**Authorization:** Bearer {token}

**Request Body:**
```json
{
  "sku": "SGS24-BLACK-256",
  "variant_title": "Samsung Galaxy S24 - Black, 256GB",
  "price": 999.99,
  "stock": 50,
  "is_default": true,
  "attributes": [
    {
      "attribute_id": 1,
      "attribute_value_id": 4
    },
    {
      "attribute_id": 2,
      "attribute_value_id": 8
    }
  ]
}
```

#### Add Black 512GB Variant

**Endpoint:** `POST /api/admin/products/{product_id}/variants`  
**Method:** POST  
**Content-Type:** application/json  
**Authorization:** Bearer {token}

**Request Body:**
```json
{
  "sku": "SGS24-BLACK-512",
  "variant_title": "Samsung Galaxy S24 - Black, 512GB",
  "price": 1099.99,
  "stock": 30,
  "attributes": [
    {
      "attribute_id": 1,
      "attribute_value_id": 4
    },
    {
      "attribute_id": 2,
      "attribute_value_id": 9
    }
  ]
}
```

#### Add White 256GB Variant

**Endpoint:** `POST /api/admin/products/{product_id}/variants`  
**Method:** POST  
**Content-Type:** application/json  
**Authorization:** Bearer {token}

**Request Body:**
```json
{
  "sku": "SGS24-WHITE-256",
  "variant_title": "Samsung Galaxy S24 - White, 256GB",
  "price": 999.99,
  "stock": 45,
  "attributes": [
    {
      "attribute_id": 1,
      "attribute_value_id": 5
    },
    {
      "attribute_id": 2,
      "attribute_value_id": 8
    }
  ]
}
```

#### Add White 512GB Variant

**Endpoint:** `POST /api/admin/products/{product_id}/variants`  
**Method:** POST  
**Content-Type:** application/json  
**Authorization:** Bearer {token}

**Request Body:**
```json
{
  "sku": "SGS24-WHITE-512",
  "variant_title": "Samsung Galaxy S24 - White, 512GB",
  "price": 1099.99,
  "stock": 25,
  "attributes": [
    {
      "attribute_id": 1,
      "attribute_value_id": 5
    },
    {
      "attribute_id": 2,
      "attribute_value_id": 9
    }
  ]
}
```

## 5. Verify Product and Variants

To verify that the product and its variants were created correctly, you can use the following endpoint:

**Endpoint:** `GET /api/admin/products/{product_id}`  
**Method:** GET  
**Authorization:** Bearer {token}

This will return the complete product information including all variants and their associated attributes.

## Notes

1. The attribute and attribute value IDs in the examples assume they are created in sequence. In a real environment, you should use the actual IDs returned from the creation endpoints.
2. The product creation can be done in a single request with all variants included, or by creating the parent product first and then adding variants one by one.
3. The Samsung Galaxy S24 example only includes Black and White color variants (out of the possible Red, Green, Blue, Black, White) and only 256GB and 512GB storage options (out of the possible 64GB, 128GB, 256GB, 512GB, 1024GB).
4. All requests require proper authentication with an admin token.

## 6. Adding Images to Products

Images are a critical part of product listings in an e-commerce system. This section explains how to add images to both parent products and their variants.

### Adding Images to Parent Products

**Endpoint:** `POST /api/admin/products/{product_id}/images`  
**Method:** POST  
**Content-Type:** multipart/form-data  
**Authorization:** Bearer {token}

**Request Parameters:**
- `images[]`: An array of image files (supports multiple file upload)
- `is_primary` (optional): Boolean value to set an image as primary (default: false for all if not specified)

**Example Request using cURL:**
```bash
curl -X POST \
  https://your-api-domain.com/api/admin/products/1/images \
  -H 'Authorization: Bearer {token}' \
  -H 'Content-Type: multipart/form-data' \
  -F 'images[]=@/path/to/samsung_s24_front.jpg' \
  -F 'images[]=@/path/to/samsung_s24_back.jpg' \
  -F 'images[]=@/path/to/samsung_s24_side.jpg' \
  -F 'is_primary=0,1,0'
```

**Expected Response:**
```json
{
  "success": true,
  "message": "Product images uploaded successfully",
  "data": [
    {
      "id": 1,
      "product_id": 1,
      "product_variant_id": null,
      "image_path": "products/samsung_s24_front_1234567890.jpg",
      "is_primary": false,
      "created_at": "2023-11-15T08:30:00.000000Z",
      "updated_at": "2023-11-15T08:30:00.000000Z"
    },
    {
      "id": 2,
      "product_id": 1,
      "product_variant_id": null,
      "image_path": "products/samsung_s24_back_1234567890.jpg",
      "is_primary": true,
      "created_at": "2023-11-15T08:30:00.000000Z",
      "updated_at": "2023-11-15T08:30:00.000000Z"
    },
    {
      "id": 3,
      "product_id": 1,
      "product_variant_id": null,
      "image_path": "products/samsung_s24_side_1234567890.jpg",
      "is_primary": false,
      "created_at": "2023-11-15T08:30:00.000000Z",
      "updated_at": "2023-11-15T08:30:00.000000Z"
    }
  ]
}
```

### Adding Images to Product Variants

**Endpoint:** `POST /api/admin/products/{product_id}/variants/{variant_id}/images`  
**Method:** POST  
**Content-Type:** multipart/form-data  
**Authorization:** Bearer {token}

**Request Parameters:**
- `images[]`: An array of image files (supports multiple file upload)
- `is_primary` (optional): Boolean value to set an image as primary (default: false for all if not specified)

**Example Request using cURL:**
```bash
curl -X POST \
  https://your-api-domain.com/api/admin/products/1/variants/2/images \
  -H 'Authorization: Bearer {token}' \
  -H 'Content-Type: multipart/form-data' \
  -F 'images[]=@/path/to/samsung_s24_black_512gb_front.jpg' \
  -F 'images[]=@/path/to/samsung_s24_black_512gb_back.jpg' \
  -F 'is_primary=1,0'
```

**Expected Response:**
```json
{
  "success": true,
  "message": "Variant images uploaded successfully",
  "data": [
    {
      "id": 4,
      "product_id": 1,
      "product_variant_id": 2,
      "image_path": "products/variants/samsung_s24_black_512gb_front_1234567890.jpg",
      "is_primary": true,
      "created_at": "2023-11-15T08:35:00.000000Z",
      "updated_at": "2023-11-15T08:35:00.000000Z"
    },
    {
      "id": 5,
      "product_id": 1,
      "product_variant_id": 2,
      "image_path": "products/variants/samsung_s24_black_512gb_back_1234567890.jpg",
      "is_primary": false,
      "created_at": "2023-11-15T08:35:00.000000Z",
      "updated_at": "2023-11-15T08:35:00.000000Z"
    }
  ]
}
```

### Creating a Product with Images in a Single Request

You can also upload images when creating a product by using a multipart/form-data request:

**Endpoint:** `POST /api/admin/products`  
**Method:** POST  
**Content-Type:** multipart/form-data  
**Authorization:** Bearer {token}

**Request Parameters:**
- `product`: A JSON string containing the product data (as shown in section 3)
- `images[]`: An array of image files for the parent product
- `is_primary` (optional): Boolean value to set an image as primary (default: false for all if not specified)

**Example Request using cURL:**
```bash
curl -X POST \
  https://your-api-domain.com/api/admin/products \
  -H 'Authorization: Bearer {token}' \
  -H 'Content-Type: multipart/form-data' \
  -F 'product={"name":"Samsung Galaxy S24","description":"Experience the latest Samsung Galaxy S24 with cutting-edge features and performance.","price":999.99,"is_parent":true,"status":"active"}' \
  -F 'images[]=@/path/to/samsung_s24_front.jpg' \
  -F 'images[]=@/path/to/samsung_s24_back.jpg' \
  -F 'is_primary=1,0'
```

### Image Management Guidelines

1. **Supported Formats**: The API supports JPG, JPEG, PNG, and WebP formats.
2. **File Size Limits**: Maximum file size is 2MB per image.
3. **Image Dimensions**: For optimal display, product images should have dimensions between 800x800 and 2000x2000 pixels.
4. **Primary Images**: Each product and variant can have one primary image that will be used as the main display image.
5. **Image Order**: The API maintains the order in which images are uploaded. The primary image will always be displayed first.
6. **Image Deletion**: To delete an image, use the DELETE endpoint: `DELETE /api/admin/products/images/{image_id}`.

### Updating Product Images

To update an existing image (such as changing its primary status):

**Endpoint:** `PATCH /api/admin/products/images/{image_id}`  
**Method:** PATCH  
**Content-Type:** application/json  
**Authorization:** Bearer {token}

**Request Body:**
```json
{
  "is_primary": true
}
```

**Expected Response:**
```json
{
  "success": true,
  "message": "Image updated successfully",
  "data": {
    "id": 1,
    "product_id": 1,
    "product_variant_id": null,
    "image_path": "products/samsung_s24_front_1234567890.jpg",
    "is_primary": true,
    "created_at": "2023-11-15T08:30:00.000000Z",
    "updated_at": "2023-11-15T08:40:00.000000Z"
  }
}
