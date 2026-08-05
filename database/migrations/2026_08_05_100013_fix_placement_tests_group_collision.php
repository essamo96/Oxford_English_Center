<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Corrective migration: 2026_08_05_100012 mistakenly reused the name "placement_tests" for the
     * new Examination Center menu, which collided with the PRE-EXISTING "placement_tests" permissions
     * group (id 48) that belongs to the unrelated placement-test appointment/payment booking feature
     * (see admin/placement_tests routes -> Admin\PlacementTestsController, and the `placement_tests`
     * table created in 2026_05_10_194009_create_placement_tests_table.php). That migration re-parented
     * group 48 under the new "examination_center" group (id 87) and added two permissions
     * (admin.placement_tests.schedule / admin.placement_tests.publish) that don't belong to it.
     *
     * This migration restores group 48 to its original parent (47 = "registration_management", inferred
     * from its sort order among siblings "parents" (sort 2) and "payment_methods" (sort 3), where
     * placement_tests was sort 1) and removes the two stray permissions.
     */
    public function up(): void
    {
        $bookingGroupId = DB::table('permissions_group')->where('name', 'placement_tests')->value('id');
        $registrationGroupId = DB::table('permissions_group')->where('name', 'registration_management')->value('id');

        if ($bookingGroupId && $registrationGroupId) {
            DB::table('permissions_group')->where('id', $bookingGroupId)->update(['parent_id' => $registrationGroupId]);
        }

        DB::table('permissions')
            ->whereIn('name', ['admin.placement_tests.schedule', 'admin.placement_tests.publish'])
            ->where('group_id', $bookingGroupId)
            ->delete();
    }

    public function down(): void
    {
        // Not reversible: the original stray-permission removal is intentionally permanent.
    }
};
