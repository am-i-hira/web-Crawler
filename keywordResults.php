<?php
// Include the database process file
include_once("dbProcess.php");

// Check if keyword and URL ID are set in the POST request
if (isset($_POST['keyword']) && isset($_POST['urlID'])) {
    // Retrieve values from the POST request
    $keyword = $_POST['keyword'];
    $urlId = $_POST['urlID'];

    // Create an instance of the DBPROCESS class
    $keyWordSearch = new DBPROCESS();

    // Search for JSON data based on the URL ID
    $jsonData = $keyWordSearch->search($urlId);

    // Check if JSON data is retrieved
    if ($jsonData) {
        // Decode the JSON data into an array
        $resultArray = json_decode($jsonData); 

        // Display header for the search results
        echo "<h2 style='text-align:center'>URLs Containing Given Keyword</h2><ul>";

        // Iterate through the result array
        foreach ($resultArray as $result) {
            // Get URL from the result
            $url = $result->url;

            // Iterate through the data items for the URL
            foreach ($result->data as $item) {
                // Check if the keyword is present in the data item (case-insensitive)
                if (stripos(strtolower($item), strtolower($keyword)) !== false) {
                    // Display the URL in an unordered list item
                    echo "<li>" . $url . "</li>";

                    // Break the loop once a match is found for the URL
                    break;
                }
            }
        }

        // Close the unordered list
        echo "</ul>";
    } else {
        // Display a message if no results are found
        echo "<h1>No Results Found</h1>";
    }
} else {
    // Redirect to the index.html page if keyword or URL ID is not set
    header("location: index.html");
}
?>
