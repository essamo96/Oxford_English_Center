<?php
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$kernel->handle(Illuminate\Http\Request::capture());

$child = \App\Models\PermissionsGroup::where('name', 'sms_archive')->first();
if ($child) {
    if (!\App\Models\Permissions::where('name', 'admin.sms_archive.view')->exists()) {
        \App\Models\Permissions::create([
            'name' => 'admin.sms_archive.view',
            'group_id' => $child->id,
            'name_ar' => 'عرض أرشيف الرسائل',
            'name_en' => 'View SMS Archive',
        ]);
        echo "Permission created. ";
    }
}
