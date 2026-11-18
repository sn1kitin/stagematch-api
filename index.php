<?php
require __DIR__ . '/vendor/autoload.php';

use App\Database\Database;
use App\Http\ApiResponse;

$db = new Database();
$db->query('SELECT * FROM `skills`');

ApiResponse::send($db->fetchAll());
