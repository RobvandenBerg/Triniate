<?php
include("include_this.php");

// Use PDO, because I don't care what PHP version you run, it better be supported. Also why would you run this under PHP 5.1?

// Connect to the database
$conn = new mysqli($dbhost, $dbuser, $dbpass, $db);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if (isset($_GET["id"])) {
    $player_id = $_GET["id"];
    
    // Validate that id is an integer
    if (!is_numeric($player_id)) {
        echo "Error: Invalid ID.";
        exit;
    }

    // Prepare the query
    $stmt = $conn->prepare("SELECT head FROM position WHERE id = ?");
    if ($stmt) {
        // Bind the parameter
        $stmt->bind_param("i", $player_id);

        // Execute the statement
        $stmt->execute();

        // Check if any rows were returned
        $stmt->store_result();
        if ($stmt->num_rows > 0) {
            // Bind the result
            $stmt->bind_result($head);

            // Fetch and output the result
            if ($stmt->fetch()) {
                echo $head; // Output the fetched result
            } else {
                echo "1"; // If fetch failed
            }
        } else {
            echo "1"; // No rows returned
        }

        // Close the statement
        $stmt->close();
    } else {
        echo "1"; // If statement preparation failed
    }
} else {
    echo "1"; // If the 'id' parameter is not set
}

// Close the connection
$conn->close();
?>