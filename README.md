## Changes Made

### 1. **Refactored Routes:**
   - Grouped routes for better organization.
   - Corrected route methods for actions such as `delete` and `update`.

### 2. **Storage Link:**
   - Created symbolic links for storing files in the `public` directory instead of the root `public` directory.

### 3. **File Uploads:**
   - Modified file upload logic to save files in the `storage/app/public` directory, improving file management and security.

### 4. **Product Image Validation:**
   - Added validation for product images to ensure only valid file types (JPEG, PNG, JPG, GIF, SVG) are uploaded and that they do not exceed a size of 1MB.

### 5. **Organized Views and Controllers:**
   - Improved code organization for scalability by separating views and controllers based on the application's needs.

### 6. **Separate Admin Routes:**
   - Created a dedicated file for admin routes to ensure better organization and control over admin-related functionalities.

### 7. **Cache for Exchange Rates:**
   - Implemented caching for fetching exchange rates from an external API to improve performance and reduce redundant API calls.

### 8. **Refactored Views:**
   - Refactored views using layouts and reusable components for better maintainability and consistency across pages.

### 9. **Image Optimization:**
   - Integrated **Intervention Image** library to optimize product images automatically if they exceed 1MB in size.

### 10. **Reusable Code with Traits:**
   - Extracted reusable code (like image optimization) into a dedicated trait for better reusability and cleaner code.

---

## Suggestions for Improvement

### 1. **Database Indexing:**
   - Ensure columns frequently queried (e.g., `name`, `price`, `created_at`) are indexed to improve query performance, especially for large datasets.

### 2. **Separation of Concerns:**
   - Consider moving complex business logic to service classes or action classes. This keeps controllers focused on request handling, making the code more maintainable and testable.

### 3. **Improving File Validation:**
   - Improve the validation for product images by adding additional checks such as aspect ratio, dimensions, and file size limits. 
   - Example:
     ```php
     'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:1024',
     ```

### 4. **Caching and Performance:**
   - Implement different caching strategies such as **cache tags** or **cache locking** to optimize API calls, especially for frequently accessed data like exchange rates.
   - Consider queuing image optimization tasks to improve performance by offloading heavy tasks to background processes.

### 5. **User Authentication & Authorization:**
   - If sensitive data is involved, ensure proper user roles and permissions for routes and actions like product deletion.
   - Implement **two-factor authentication (2FA)** for added security for admin access.

### 6. **UI/UX Improvements:**
   - Refactor the admin panel UI for better usability, especially for mobile and tablet users.
   - Implement modern UI components, using **Tailwind CSS** or **Vue.js**, to make the interface more dynamic and interactive.

### 7. **Error Pages and Custom Exceptions:**
   - Customize error pages such as 404 and 500 to provide a better user experience when things go wrong.
   - Implement custom exception handling for specific error cases such as product not found or image upload failure.

### 8. **API Improvements:**
   - Implement **API rate limiting** to protect the system from abuse.
   - Use **API Resources** to format responses better, ensuring consistency across the API.
   - Support **pagination** for API responses when fetching large sets of data.

---

## Installation

### Prerequisites
- Laravel 12
- Composer
- PHP 8.1+
- MySQL

### Steps

1. **Clone the repository:**
  ```bash
  git clone https://github.com/AbdulHadijatoi/appfront.git
  cd appfront
  ```

2. **Install dependencies:**
  ```bash
  composer install
  ```

3. **Set up the .env file:**
  ```bash
  cp .env.example .env
  php artisan key:generate
  ```

4. **Run migrations:**
  ```bash
  php artisan migrate
  ```

5. **Run Seeder:**
  ```bash
  php artisan db:seed --class=DatabaseSeeder
  ```

6. **Create symbolic storage link:**
  ```bash
  php artisan storage:link
  ```