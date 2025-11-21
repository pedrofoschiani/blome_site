<?php
require_once __DIR__ . '/../../init.php';

$profileData = requireRole('admin');

$userName = $profileData['full_name'];
$userAvatar = $profileData['avatar_url'];
?>