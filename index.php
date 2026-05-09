<?php
/**
 * Root Entry Point - Deckoid ERP
 * Redirects to appropriate page based on auth status
 */
require_once 'includes/auth.php';

if (AuthController::isLoggedIn()) {
    header('Location: admin/dashboard.php');
} else {
    header('Location: login.php');
}
exit;
