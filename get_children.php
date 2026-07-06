<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$g = \App\Models\PermissionsGroup::where('name', 'combo_requests')->first();
$children = \App\Models\PermissionsGroup::where('parent_id', $g->id)->get();
echo json_encode($children->toArray(), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
