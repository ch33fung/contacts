<?php
$servername = "localhost";
$username = "root"; // Update with your database username
$password = ""; // Update with your database password
$dbname = "contacts"; // Update with your database name

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_FILES['file']) && $_FILES['file']['error'] == UPLOAD_ERR_OK) {
        $fileTmpPath = $_FILES['file']['tmp_name'];
        $fileName = $_FILES['file']['name'];
        $fileSize = $_FILES['file']['size'];
        $fileType = $_FILES['file']['type'];
        $fileNameCmps = explode(".", $fileName);
        $fileExtension = strtolower(end($fileNameCmps));

        $allowedfileExtensions = array('csv');
        if (in_array($fileExtension, $allowedfileExtensions)) {
            $handle = fopen($fileTmpPath, "r");
            fgetcsv($handle); // Skip the first line (header row)

            while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
                $name = $data[0];
                $age = $data[1];
                $phone_number = $data[2];

                $sql = "INSERT INTO contacts (name, age, phone_number) VALUES ('$name', '$age', '$phone_number')";
                if ($conn->query($sql) !== TRUE) {
                    echo "Error: " . $sql . "<br>" . $conn->error;
                }
            }
            fclose($handle);
            echo "<div class='alert alert-success mt-4' role='alert'>CSV file uploaded successfully!</div>";
        } else {
            echo "<div class='alert alert-danger mt-4' role='alert'>Upload failed. Allowed file types: " . implode(',', $allowedfileExtensions) . "</div>";
        }
    } else {
        echo "<div class='alert alert-danger mt-4' role='alert'>There was an error uploading the file.</div>";
    }
}

$conn->close();
