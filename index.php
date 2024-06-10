<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "fivem_data";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST["name"];
    $staff = $_POST["staff"];
    $reason = $_POST["reason"];
    $expiration = $_POST["expiration"];
    $steam = $_POST["steam"];
    $license = $_POST["license"];
    $live = $_POST["live"];
    $xbox = $_POST["xbox"];
    $discord = $_POST["discord"];
    $tokens = $_POST["tokens"];
    $ip = $_POST["ip"];
    $uuid = $_POST["uuid"];
    $sql = "INSERT INTO bans (name, staff, reason, expiration, steam, license, live, xbox, discord, tokens, ip, uuid) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssssssssssss", $name, $staff, $reason, $expiration, $steam, $license, $live, $xbox, $discord, $tokens, $ip, $uuid);
    
    if ($stmt->execute() === TRUE) {
        echo "Data saved successfully";
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
}

if ($_SERVER["REQUEST_METHOD"] == "GET") {
    header('Content-Type: application/json; charset=utf-8');
    $sql = "SELECT * FROM bans";
    $result = $conn->query($sql);

    if ($result) {
        $rows = array();

        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $rows[] = $row;
            }

            echo json_encode($rows, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        } else {
            echo json_encode(["message" => "No data found"]);
        }

        $result->free();
    } else {
        echo json_encode(["error" => "Failed to execute query: " . $conn->error]);
    }
}

if ($_SERVER["REQUEST_METHOD"] == "DELETE") {
    $deleteData = json_decode(file_get_contents("php://input"), true);
    $steamToDelete = $deleteData["steam"];
    $sql = "DELETE FROM bans WHERE steam = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $steamToDelete);
    
    if ($stmt->execute() === TRUE) {
        echo "Data deleted successfully";
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
}

if ($_SERVER["REQUEST_METHOD"] == "DELETEE") {
    $deleteData = json_decode(file_get_contents("php://input"), true);
    
    if (isset($deleteData["id"])) {
        $idToDelete = $deleteData["id"];

        $sql = "DELETE FROM bans WHERE id = ?";
        $stmt = $conn->prepare($sql);
        
        if ($stmt) {
            $stmt->bind_param("i", $idToDelete);
            
            if ($stmt->execute()) {
                echo json_encode(["message" => "Data deleted successfully"]);
            } else {
                echo json_encode(["error" => "Error deleting data: " . $stmt->error]);
            }

            $stmt->close();
        } else {
            echo json_encode(["error" => "Error preparing statement: " . $conn->error]);
        }
    } else {
        echo json_encode(["error" => "Invalid input: 'id' is required"]);
    }
}

$conn->close();
?>
