<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: *");
header("Access-Control-Allow-Methods: *");
// Include the necessary files
include 'db_conf.php'; 
include 'functions.php';

$method = $_SERVER['REQUEST_METHOD']; // Get the HTTP request method

switch ($method) {
    case "GET":
        $path = explode('/', $_SERVER['REQUEST_URI']); // Split the request URI by '/'

        if (isset($path[4]) && is_numeric($path[4])) {
            // If the request URI has a numeric ID at index 4

            $id = $path[4]; // Get the ID from the URI

            // Prepare and execute the SQL statement to fetch a user by ID
            $stmt = $pdo->prepare("SELECT * FROM user WHERE id = :id");
            $stmt->execute([
                'id' => $id
            ]);

            // Fetch the results
            $row = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($row) {
                // If a user is found with the given ID

                $userData = [
                    'id' => $row['id'],
                    'email' => $row['email'],
                    'username' => $row['username'],
                    'mobile' => $row['mobile']
                ];
                echo json_encode($userData);
            } else {
                echo json_encode(["result" => "No User Found"]); // No user found with the given ID
            }
        } else {
            // If the request URI does not have an ID

            $stmt = $pdo->query("SELECT * FROM user");
            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

            if ($rows) {
                // If users are found in the database

                $userData = [];

                foreach ($rows as $row) {
                    $userData[] = [
                        "id" => $row['id'],
                        "username" => $row['username'],
                        "email" => $row['email'],
                        "mobile" => $row['mobile']
                    ];
                }

                echo json_encode($userData);
            } else {
                echo json_encode(["result" => "No Users Available"]); // No users found in the database
            }
        }
        break;
    case "POST":
        $userData = json_decode(file_get_contents("php://input")); // Get the JSON data from the request body
        $username = sanitizeInput($userData->username);
        $email = sanitizeInput($userData->email);
        $mobile = sanitizeInput($userData->mobile);
        if (!validateInput($mobile, 'mobile')) {
            $errors['mobile'] = 'Invalid Mobile Number';
        }

        if (!validateInput($email, 'email')) {
            $errors['email'] = 'Invalid Email Address';
        }
        if ($exists = checkIfExists($pdo, 'SELECT COUNT(*) FROM user WHERE email = ?', [$email])) {
            $errors['email'] = 'Email Already Taken';
        };
        if ($exists = checkIfExists($pdo, 'SELECT COUNT(*) FROM user WHERE mobile = ?', [$mobile])) {
            $errors['mobile'] = 'Mobile Already Taken';
        };

        if (empty($errors)) {
            // Prepare and execute the SQL statement to insert a new user
            $stmt = $pdo->prepare('INSERT INTO user (username, email, mobile) 
                        VALUES (:username, :email, :mobile)');
            $result = $stmt->execute([
                'username' => strtoupper($username),
                'email' => $email,
                'mobile' => $mobile,
            ]);

            if ($result) {
                echo json_encode(["success" => "Inserted Successfully"]);
            } else {
                echo json_encode(["success" => "Insertion Failed"]);
            }
        } else {
            echo json_encode($errors);
        }
        break;
    case "PUT":
        $userData = json_decode(file_get_contents("php://input")); // Get the JSON data from the request body
        $id = $userData->id;
        $username = sanitizeInput($userData->username);
        $email = sanitizeInput($userData->email);
        $mobile = sanitizeInput($userData->mobile);
        if (!validateInput($mobile, 'mobile')) {
            $errors['mobile'] = 'Invalid Mobile Number';
        }

        if (!validateInput($email, 'email')) {
            $errors['email'] = 'Invalid Email Address';
        }
        if ($exists = checkIfExists($pdo, 'SELECT COUNT(*) FROM user WHERE email = ? AND id!=?', [$email, $id])) {
            $errors['email'] = 'Email Already Taken';
        };
        if ($exists = checkIfExists($pdo, 'SELECT COUNT(*) FROM user WHERE mobile = ? AND id!=?', [$mobile, $id])) {
            $errors['mobile'] = 'Mobile Already Taken';
        };
        if (empty($errors)) {
            // Prepare and execute the SQL statement to update a user
            $stmt = $pdo->prepare("UPDATE user SET username = :username, email = :email, mobile = :mobile WHERE id = :id");
            $result = $stmt->execute([
                'username' => strtoupper($username),
                'email' => $email,
                'mobile' => $mobile,
                'id' => $id
            ]);

            if ($result) {
                echo json_encode(["success" => "Updated Successfully"]);
            } else {
                echo json_encode(["success" => "Updation Failed"]);
            }
        } else {
            echo json_encode($errors);
        }
        break;
    case "DELETE":
        $path = explode('/', $_SERVER['REQUEST_URI']); // Split the request URI by '/'

        if (isset($path[4]) && is_numeric($path[4])) {
            // If the request URI has a numeric ID at index 4

            $id = $path[4]; // Get the ID from the URI

            // Prepare and execute the SQL statement to delete a user by ID
            $stmt = $pdo->prepare("DELETE FROM user WHERE id = :id");
            $result = $stmt->execute([
                'id' => $id
            ]);

            if ($result) {
                echo json_encode(["success" => "Deleted Successfully"]);
            } else {
                echo json_encode(["success" => "Deletion Failed"]);
            }
        }
        break;
}
