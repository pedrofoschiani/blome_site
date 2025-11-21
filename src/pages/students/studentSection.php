<?php
require_once __DIR__ . '/../../init.php';

$profileData = requireRole('student');

$userName = $profileData['full_name'];
$userAvatar = $profileData['avatar_url'];
?>