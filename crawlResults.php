<?php
// Check if 'id' parameter is set in the URL
if (isset($_GET['id'])) {
    // Assign the 'id' value to $urlID
    $urlID = $_GET['id'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Crawl Results</title>
    <link rel="stylesheet" href="styles/search.css">
</head>
<body>
    <div class="cont" id="searchCont">
        <!-- Heading for the search page -->
        <h1 class="heading">Search Keyword in Crawled Results</h1>

        <!-- Form to submit keyword for search in crawled results -->
        <form action="keywordResults.php" method="POST">
            <div class="container">
                <!-- Input field for entering the keyword -->
                <div class="input-cont">
                    <input class="input" type="text" placeholder="Keyword..." name="keyword" id="keyword">
                    <!-- Hidden input field to store the URL ID -->
                    <input type="hidden" value='<?php echo $urlID ?>' name="urlID" id="urlID">
                </div>

                <!-- Submit button for the form -->
                <button id="submitbutton" type="submit" class="search" onclick="handleSubmit(event)">Search</button>
            </div>
        </form>
    </div>

    <!-- JavaScript script to handle form submission -->
    <script>
        function handleSubmit(event) {
            // Get the value of the keyword input and trim any leading/trailing whitespaces
            var keyword = document.getElementById("keyword").value.trim();

            // Check if the keyword is empty
            if (!keyword) {
                // Prevent form submission and show an alert
                event.preventDefault();
                alert("Please enter a keyword!");
            }
        }
    </script>
</body>
</html>

<?php
} else {
    // Redirect to the index.html page if 'id' is not set
    header("location: index.html");
}
?>
