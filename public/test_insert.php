<?php
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$kernel->handle(Illuminate\Http\Request::capture());

if (!\App\Models\PermissionsGroup::where('name', 'sms')->exists()) {
    $parent = \App\Models\PermissionsGroup::create([
        'name' => 'sms',
        'name_ar' => 'إدارة خدمة tweetSMS',
        'name_en' => 'SMS Management',
        'icon' => 'ki-duotone ki-sms',
        'color' => '#009ef7',
        'sort' => 90,
        'status' => 1,
        'parent_id' => 0
    ]);
    \App\Models\PermissionsGroup::create([
        'name' => 'sms_archive',
        'name_ar' => 'أرشيف الرسائل',
        'name_en' => 'SMS Archive',
        'sort' => 1,
        'status' => 1,
        'parent_id' => $parent->id
    ]);
    echo "Inserted";
} else {
    echo "Already exists";
}
