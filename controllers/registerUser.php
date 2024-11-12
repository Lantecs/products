<?php
require_once 'src/database/database.php';
require_once 'src/class/register.php';
require_once 'src/class/emailservice.php';
require_once __DIR__ . '/../vendor/autoload.php';

use Dotenv\Dotenv;
use Rakit\Validation\Validator;

header('Content-Type: application/json');

$dotenv = Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->load();

try {
    $database = new Database();
    $db = $database->connect();
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['message' => 'Database connection failed: ' . $e->getMessage()]);
    exit;
}

$data = json_decode(file_get_contents("php://input"), true);

// Check if the required keys are present in the request
$requiredKeys = ['first_name', 'last_name', 'email', 'password', 'confirm_password', 'date_of_birth'];
foreach ($requiredKeys as $key) {
    if (!isset($data[$key])) {
        http_response_code(400);
        echo json_encode(['message' => 'Missing required field: ' . $key]);
        exit;
    }
}

$validator = new Validator;
// Validation rules
$validation = $validator->make($data, [
    'first_name' => 'required|regex:/^[A-Za-z\s]+$/|max:50',
    'last_name' => 'required|regex:/^[A-Za-z\s]+$/|max:50',
    'email' => 'required|email|max:100',
    'password' => 'required|min:8|regex:/[A-Za-z]/|regex:/[0-9]/|regex:/[@$!%*?&]/',
    'confirm_password' => 'required|min:8',
    'date_of_birth' => 'required|regex:/^\d{4}-\d{2}-\d{2}$/',
    'role' => 'in:admin,customer,vendor' // Optional field, by default is customer
]);

try {
    // Run validation
    $validation->validate();

    if ($validation->fails()) {
        // Handle validation errors
        $errors = $validation->errors();
        http_response_code(400);
        echo json_encode(['message' => 'Validation failed', 'errors' => $errors->firstOfAll()]);
        exit;
    }

    // Check for existing email
    $emailExistsQuery = $db->prepare("SELECT COUNT(*) FROM users WHERE email = :email");
    $emailExistsQuery->execute(['email' => $data['email']]);
    $emailExistsCount = $emailExistsQuery->fetchColumn();

    if ($emailExistsCount > 0) {
        http_response_code(400);
        echo json_encode(['message' => 'Email is already registered.']);
        exit;
    }

    // Check if password and confirmation match
    if ($data['password'] !== $data['confirm_password']) {
        http_response_code(400);
        echo json_encode(['message' => 'Passwords do not match.']);
        exit;
    }

    // Set the default role if none is provided
    $role = isset($data['role']) ? $data['role'] : 'customer';

    // Attempt to register the user
    $user = new Register($db);
    $result = $user->registerUser($data['first_name'], $data['last_name'], $data['email'], $data['password'], $data['date_of_birth'], $role);

    if ($result['success']) {
        $tokenId = $result['token_id'];

        // Fetch newly registered user 
        $userId = $result['user_id'];
        $fullName = $data['first_name'] . ' ' . $data['last_name'];

        $emailService = new EmailService();
        if ($emailService->sendVerificationEmail($data['email'], $tokenId)) {
            http_response_code(201);
            echo json_encode([
                'message' => 'User registered successfully. Verification email sent.',
                'user' => [
                    'id' => $userId,
                    'name' => $fullName,
                    'email' => $data['email'],
                    'role' => $role
                ]
            ]);
        } else {
            http_response_code(500);
            echo json_encode(['message' => 'Verification email could not be sent.']);
        }
    } else {
        http_response_code(400);
        echo json_encode(['message' => $result['message']]);
    }

} catch (Exception $e) {
    // General error handling
    http_response_code(500);
    echo json_encode(['message' => 'An error occurred: ' . $e->getMessage()]);
}

ini_set('display_errors', '1');
error_reporting(E_ALL);
