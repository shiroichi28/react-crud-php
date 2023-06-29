<?php
function sanitizeInput($input)
{
    if (is_array($input)) {
        return array_map('sanitizeInput', $input);
    }

    if (is_string($input)) {
        $input = trim($input);
        $input = stripslashes($input);
        $input = strip_tags($input);
        $input = htmlspecialchars($input, ENT_QUOTES | ENT_HTML5, 'UTF-8');
    }

    return $input;
}
function validateInput($input, $type)
{
    switch ($type) {
        case 'mobile':
            // Validate mobile number
            // Example: 10-digit numeric mobile number
            return preg_match('/^[0-9]{10}$/', $input);
        case 'email':
            // Validate email address
            return filter_var($input, FILTER_VALIDATE_EMAIL) !== false;
        case 'password':
            // Validate password
            // Example: At least 8 characters, with at least one uppercase letter, one lowercase letter, and one digit
            return preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).{8,}$/', $input);
        case 'username':
            // Validate username
            // Example: Alphanumeric username with at least 3 characters
            return preg_match('/^[a-zA-Z0-9]{3,}$/', $input);
        default:
            // Invalid type
            return false;
    }
}
function checkIfExists($pdo, $query, $params)
{
    $stmt = $pdo->prepare($query);
    $stmt->execute($params);
    return $stmt->fetchColumn() > 0;
}

