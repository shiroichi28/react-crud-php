<?php
header("Access-Control-Allow-Origin:* ");
header("Access-Control-Allow-Headers: *");
header("Access-Control-Allow-Methods: *");
include 'db_conf.php';

$method = $_SERVER['REQUEST_METHOD'];
switch ($method) {
    case "GET":
        $stmt = $pdo->query("SELECT * FROM user ");
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        if ($rows > 0) {
            foreach ($rows as $row) {
                $json_arr["userdata"][] = array("id" => $row['id'], "username" => $row['username'], "email" => $row['email'], "mobile" => $row['mobile']);
            }
            echo json_encode($json_arr["userdata"]);
            return;
        } else {
            echo json_encode(["result" => "Please Check The Data"]);
            return;
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
}
