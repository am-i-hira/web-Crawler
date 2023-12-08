<?php
// Include the database connection file
include_once("config/connection.php");

// Class to handle database operations
class DBPROCESS {
    
    // Function to add a URL to the 'urls' table
    public function addUrl($url) {
        global $conn;

        // Using a prepared statement to avoid SQL injection
        $sql = "INSERT IGNORE INTO urls (url) VALUES (?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $url);

        // Execute the statement
        if ($stmt->execute()) {
            $lastInsertedID = $stmt->insert_id;
            $stmt->close(); 
            return $lastInsertedID;
        } else {
            $stmt->close(); 
            return null;
        }
    }

    // Function to add data (JSON) to the 'data' table
    public function addData($jsonData, $id) {
        global $conn;

        // Using a prepared statement to avoid SQL injection
        $sql = "INSERT IGNORE INTO data (json, url_id) VALUES (?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("si", $jsonData, $id);

        // Execute the statement
        if ($stmt->execute()) {
            $stmt->close();
            return true;
        } else {
            $stmt->close();
            return false;
        }
    }

    // Function to test if a URL already exists in the 'urls' table
    public function test($url) {
        global $conn;

        // Using a prepared statement to avoid SQL injection
        $sql = "SELECT * FROM urls WHERE url = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $url);
    
        // Execute the statement
        if ($stmt->execute()) {
            $result = $stmt->get_result();
            if ($result->num_rows > 0) {
                $row = $result->fetch_assoc();
                $stmt->close();
                return $row['url_id'];
            } else {
                $stmt->close();
                return null;
            }
        } else {
            $stmt->close();
            return null;
        }
    }

    // Function to search for data (JSON) based on URL ID in the 'data' table
    public function search($urlId) {
        global $conn;

        // Using a prepared statement to avoid SQL injection
        $sql = "SELECT * FROM data WHERE url_id = ?";
        $stmt = $conn->prepare($sql);
        
        // Bind the parameter
        $stmt->bind_param("i", $urlId);

        // Execute the statement
        if ($stmt->execute()) {
            $result = $stmt->get_result();
    
            if ($result->num_rows > 0) {
                $row = $result->fetch_assoc();
                return $row['json'];
            } else {
                return null; 
            }
        } else {
            $stmt->close(); 
            return null;
        }
    }
}

// End of the class
?>
