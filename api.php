<?php

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

$request_method = $_SERVER['REQUEST_METHOD'];
$request_uri = $_SERVER['REQUEST_URI'];

// Base path for the API routes
$base_path = '/CE1-Ecommerce/api.php';

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);


if ($request_uri === $base_path . "/products" && $request_method === 'GET') {
    require __DIR__ . '/controllers/listAllProducts.php';
    exit();
}

// Register Route
if ($request_uri === $base_path . '/register' && $request_method === 'POST') {
    require __DIR__ . '/controllers/registerUser.php';
    exit();
}

// Login Route
if ($request_uri === $base_path . '/login' && $request_method === 'POST') {
    require __DIR__ . '/controllers/loginUser.php';
    exit();
}

// Logout Route
if ($request_uri === $base_path . '/logout' && $request_method === 'POST') {
    require __DIR__ . '/controllers/logoutUser.php';
    exit();
}

// Password Reset Request Route
if ($request_uri === $base_path . '/password/reset/request' && $request_method === 'POST') {
    require __DIR__ . '/controllers/passRequest.php';
    exit();
}
// Password Reset Route
if ($request_uri === $base_path . '/password/reset' && $request_method === 'POST') {
    require __DIR__ . '/controllers/passReset.php';
    exit();
}

// Change Password Route
if ($request_uri === $base_path . '/password/change' && $request_method === 'POST') {
    require __DIR__ . '/controllers/changePassword.php';
    exit();
}

// Profile Update Route
if ($request_uri === $base_path . '/profile/update' && $request_method === 'POST') {
    require __DIR__ . '/controllers/profileUpdate.php';
    exit();
}

// Get User Profile Route
if ($request_uri === $base_path . '/user/profile' && $request_method === 'GET') {
    require __DIR__ . '/controllers/getUserProfile.php';
    exit();
}

// Assign Role to User Route
if ($request_uri === $base_path . '/role/assign' && $request_method === 'POST') {
    require __DIR__ . '/controllers/assignRole.php';
    exit();
}

// Revoke Role from User Route
if ($request_uri === $base_path . '/role/revoke' && $request_method === 'POST') {
    require __DIR__ . '/controllers/revokeUser.php';
    exit();
}

// Profile Photo Upload Route
if ($request_uri === $base_path . '/profile/photo/upload' && $request_method === 'POST') {
    require __DIR__ . '/controllers/uploadProfilePicture.php';
    exit();
}

// Add Address
if ($request_uri === $base_path . '/address' && $request_method === 'POST') {
    require __DIR__ . '/controllers/addAddress.php';
    exit();
}

// Update Address
if ($request_uri === $base_path . '/update/address' && $request_method === 'POST') {
    require __DIR__ . '/controllers/updateAddress.php';
    exit();
}

// Delete Address
if ($request_uri === $base_path . '/delete/address' && $request_method === 'DELETE') {
    require __DIR__ . '/controllers/deleteAddress.php';
    exit();
}

// List Specific User Roles
if (preg_match("#^" . $base_path . "/roles/([a-zA-Z0-9]+)$#", $request_uri, $matches) && $request_method === 'GET') {
    $user_id = $matches[1]; // Extract the user ID from the URL
    require __DIR__ . '/controllers/getUserRoles.php';
    exit();
}

// List All User
if ($request_uri === $base_path . "/all/users" && $request_method === 'GET') {
    require __DIR__ . '/controllers/listAllUsers.php';
    exit();
}


// If no route matches, return 404S
http_response_code(404);
echo json_encode(['message' => 'Endpoint not found']);