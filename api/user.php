<?php
header("Access-Control-Allow-Origin:* ");
header("Access-Control-Allow-Headers: *");
header("Access-Control-Allow-Methods: *");
include 'db_conf.php';

$method = $_SERVER['REQUEST_METHOD'];
switch ($method) {
    case "GET":
        $path = explode('/', $_SERVER['REQUEST_URI']);
        if (isset($path[4]) && is_numeric($path[4])) {
            $id = $path[4]; // Assuming $path is an array with the desired value at index 4

            // Prepare and execute the SQL statement
            $stmt = $pdo->prepare("SELECT * FROM user WHERE id = :id");
            $stmt->execute([
                'id' => $id
            ]);

            // Fetch the results
            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $json_arr = array();
            // Output the results (for demonstration purposes)
            if ($rows > 0) {
                foreach ($rows as $row) {
                    $json_arr['userData'] = array('id' => $row['id'], 'email' => $row['email'], 'username' => $row['username'], 'mobile' => $row['mobile']);
                }
                echo json_encode($json_arr["userData"]);
                return;
            }
        } else {
            $stmt = $pdo->query("SELECT * FROM user ");
            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $json_arr = array();
            if (count($rows) > 0) {
                foreach ($rows as $row) {
                    $json_arr["userdata"][] = array("id" => $row['id'], "username" => $row['username'], "email" => $row['email'], "mobile" => $row['mobile']);
                }
                echo json_encode($json_arr["userdata"]);
                return;
            } else {
                echo json_encode(["result" => "No Users Available"]);
                return;
            }
        }
        break;
    case "POST":
        $userData = json_decode(file_get_contents("php://input"));
        $username = $userData->username;
        $email = $userData->email;
        $mobile = $userData->mobile;
        $stmt = $pdo->prepare('INSERT INTO user (username, email, mobile) 
                        VALUES (:username, :email, :mobile)');
        $result = $stmt->execute([
            'username' => strtoupper($username),
            'email' => $email,
            'mobile' => $mobile,
        ]);
        if ($result) {
            echo json_encode(["success" => "Inserted Successfully"]);
            return;
        } else {
            echo json_encode(["success" => "Insertion Failed"]);
            return;
        }
        break;
    case "PUT":
        $userData = json_decode(file_get_contents("php://input"));
        $id = $userData->id;
        $username = $userData->username;
        $email = $userData->email;
        $mobile = $userData->mobile;
        $stmt = $pdo->prepare("UPDATE user SET username = :username, email = :email, mobile = :mobile WHERE id = :id");
        $result = $stmt->execute([
            'username' => strtoupper($username),
            'email' => $email,
            'mobile' => $mobile,
            'id' => $id
        ]);
        if ($result) {
            echo json_encode(["success" => "Updated Successfully"]);
            return;
        } else {
            echo json_encode(["success" => "Updation Failed"]);
            return;
        }
        break;
    case "DELETE":
        $path = explode('/', $_SERVER['REQUEST_URI']);
        if (isset($path[4]) && is_numeric($path[4])) {
            $id = $path[4]; // Assuming $path is an array with the desired value at index 4

            // Prepare and execute the SQL statement
            $stmt = $pdo->prepare("DELETE FROM user WHERE id = :id");
            $result = $stmt->execute([
                'id' => $id
            ]);
            if ($result) {
                echo json_encode(["success" => "Deletd Successfully"]);
                return;
            } else {
                echo json_encode(["success" => "Deletion Failed"]);
                return;
            }
        }
        break;
}
