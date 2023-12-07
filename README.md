# web-Crawler
# Web Spider

## Overview
Web Spider is a PHP-based web crawler designed to extract data from web pages within a specified depth and store the results in a database.

## Technologies Used
- **PHP:** The main programming language for building the web crawler.
- **cURL:** Used for making HTTP requests and fetching HTML content.
- **DOMDocument:** Utilized for parsing HTML and extracting relevant information.
- **MySQL:** The database for storing URLs and corresponding data.

## Setup Instructions
Follow these steps to set up and run the web spider:

### 1. Clone the Repository


## 1. Configure Database
- Create a MySQL database.
- Import the provided `database.sql` file to set up the required tables.

## 2. Update Database Configuration
- Open `config/connection.php`.
- Update the database connection details.

## 3. Configure PHP Environment
- Ensure that PHP is installed on your server.

## 4. Set Permissions
- Ensure that the web server has the necessary permissions to write to any directories where the web spider might save data.

## 5. Run the Web Spider
- Open your web browser and navigate to the `index.html` file.
- Enter the starting URL and depth.
- Click on the "Search" button.

The spider will start crawling the web pages, and the results will be stored in the database.
