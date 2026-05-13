<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Http\Controllers\Admin\MembershipsController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

$admin = \App\Models\User::where('role', 1)->first();
Auth::guard('admin')->login($admin);

$controller = new MembershipsController();
$request = Request::create('/admin/membership/list', 'GET', ['draw' => 1, 'start' => 0, 'length' => 10]);

$response = $controller->getmembershiplist($request);
$data = json_decode($response->getContent(), true);

echo "RecordsTotal: " . $data['recordsTotal'] . "\n";
echo "Data Count: " . count($data['data']) . "\n";
if (count($data['data']) > 0) {
    print_r($data['data'][0]);
} else {
    echo "No data found.\n";
}
