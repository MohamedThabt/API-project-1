# Tech Blog API

**Tech Blog API** is the backend version of the Tech Blog application built using Laravel. This project provides a RESTful API for managing blog posts, comments, and likes. It is designed to handle CRUD operations, user authentication, and efficient data management.

---

## Features

### 1. **Posts Management**
- Retrieve a paginated list of posts with related data (`users`, `comments`, and `likes`).
- Create new blog posts with validation and automatic user association.
- Update posts securely (ensures only the post owner can edit).
- Delete posts securely.

### 2. **Comments Management**
- Add comments to a specific post.
- Retrieve comments with validation to ensure proper data.
- Delete comments securely (only the comment owner can delete).

### 3. **Likes Management**
- Add a like to a specific post (ensures a user can like a post only once).
- Remove a like securely.

### 4. **Validation**
- Comprehensive validation for all endpoints to ensure data consistency.
- Automatic error handling for invalid requests.

### 5. **Authentication**
- All actions are tied to the authenticated user, ensuring security and data integrity.

---

## API Endpoints

### **Posts**
- `GET /api/posts`  
  Retrieve all posts with pagination.

- `POST /api/posts`  
  Create a new post.  
  **Request Body:**  
  ```json
  {
      "title": "Sample Post",
      "content": "This is a sample blog post."
  }
  ```

- `PUT /api/posts/{post}`  
  Update an existing post.  
  **Request Body:**  
  ```json
  {
      "title": "Updated Title",
      "content": "Updated content."
  }
  ```

- `DELETE /api/posts/{post}`  
  Delete a specific post.

### **Comments**
- `POST /api/comments`  
  Add a comment to a post.  
  **Request Body:**  
  ```json
  {
      "comment": "Great post!",
      "post_id": 1
  }
  ```

- `DELETE /api/comments/{comment}`  
  Delete a specific comment.

### **Likes**
- `POST /api/likes`  
  Add a like to a post.  
  **Request Body:**  
  ```json
  {
      "post_id": 1
  }
  ```

- `DELETE /api/likes/{like}`  
  Remove a like from a post.

---

## Project Structure

- **Models:** `Post`, `Comment`, and `Like` manage the data and relationships.
- **Controllers:** Handle the logic for each endpoint.
  - `PostController`
  - `CommentController`
  - `LikeController`
- **Middleware:** Ensures authentication for all API routes.
- **Routes:** Defined in `routes/api.php`.

---

## Installation

1. Clone the repository:
   ```bash
   git clone https://github.com/MohamedThabt/tech-blog-api.git
   cd tech-blog-api
   ```

2. Install dependencies:
   ```bash
   composer install
   ```

3. Set up the `.env` file:
   - Copy `.env.example` to `.env`:
     ```bash
     cp .env.example .env
     ```
   - Configure the database and other environment variables.

4. Run migrations:
   ```bash
   php artisan migrate
   ```

5. Serve the application:
   ```bash
   php artisan serve
   ```

---

## Testing

You can use tools like **Postman** or **cURL** to test the API endpoints. Ensure you include an authorization token for secured endpoints.

---

## Future Improvements

- Implement more detailed user roles and permissions.
- Add support for tagging and categorizing posts.
- Improve API response with error codes and detailed messages.
- Extend API documentation with tools like Swagger or Postman collections.

---

## Contributions

Feel free to submit issues or pull requests for bug fixes or new features. Contributions are welcome!

--- 

**License:** This project is licensed under the MIT License.
