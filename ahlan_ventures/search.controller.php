<?php



require_once "Database.php";
$config = require("config.php");

// Initialize database connection
$db = new Database($config['database']);



// Check if the search query is set
if (isset($_GET['query'])) {
    $query = trim(htmlspecialchars($_GET['query'])); // Sanitize and trim user input

    try {
        // Prepare and execute the SQL query
        $stmt = $db->prepare("SELECT * FROM products WHERE LOWER(name) LIKE LOWER(:query) OR LOWER(description) LIKE LOWER(:query)");
        $stmt->execute(['query' => '%' . $query . '%']);

        // Fetch results
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Return results as JSON
        if ($results) {
            echo json_encode(['success' => true, 'data' => $results]);
        } else {
            echo json_encode(['success' => false, 'message' => "No results found for '$query'"]);
        }
    } catch (PDOException $e) {
        // Handle database errors
        echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
    }
}

?>