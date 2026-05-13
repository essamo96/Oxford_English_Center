<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Http\Controllers\Admin\DashboardController;
use Illuminate\Support\Facades\Auth;

$admin = \App\Models\User::where('role', 1)->first();
Auth::guard('admin')->login($admin);

try {
    $controller = new DashboardController();
    $view = $controller->getIndex();
    echo $view->render();
} catch (\Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
    echo "FILE: " . $e->getFile() . "\n";
    echo "LINE: " . $e->getLine() . "\n";
    echo $e->getTraceAsString();
}
