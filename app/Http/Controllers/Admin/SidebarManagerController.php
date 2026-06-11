<?php

namespace App\Http\Controllers\Admin;

use App\Models\PermissionsGroup;
use Illuminate\Http\Request;

class SidebarManagerController extends AdminController
{
    protected $path = 'sidebar_manager';

    public function __construct()
    {
        parent::__construct();
        parent::$data['active_menu'] = 'sidebar_manager';
    }

    // Save sidebar appearance — stored per user (layout, bg color, text color)
    public function postAppearance(Request $request)
    {
        $layout      = $request->input('sidebar_layout', 'dark-sidebar');
        $bgColor     = $request->input('sidebar_bg_color', '');
        $textColor   = $request->input('sidebar_text_color', '');
        $activeColor = $request->input('sidebar_active_color', '');

        $allowed = ['dark-sidebar', 'light-sidebar', 'dark-header', 'light-header'];
        if (!in_array($layout, $allowed)) {
            return response()->json(['status' => 'error', 'message' => 'layout غير صالح']);
        }

        $colorPattern = '/^#[0-9a-fA-F]{3,6}$/';
        if ($bgColor && !preg_match($colorPattern, $bgColor)) {
            return response()->json(['status' => 'error', 'message' => 'لون خلفية غير صالح']);
        }
        if ($textColor && !preg_match($colorPattern, $textColor)) {
            return response()->json(['status' => 'error', 'message' => 'لون خط غير صالح']);
        }
        if ($activeColor && !preg_match($colorPattern, $activeColor)) {
            return response()->json(['status' => 'error', 'message' => 'لون العنصر النشط غير صالح']);
        }

        $user = auth()->guard('admin')->user();
        if (!$user) {
            return response()->json(['status' => 'error', 'message' => 'المستخدم غير موجود - يرجى تسجيل الدخول مجدداً']);
        }

        \App\Models\User::where('id', $user->id)->update([
            'sidebar_layout'      => $layout,
            'sidebar_bg_color'    => $bgColor     ?: null,
            'sidebar_text_color'  => $textColor   ?: null,
            'sidebar_active_color' => $activeColor ?: null,
        ]);

        return response()->json(['status' => 'success', 'message' => 'تم حفظ إعدادات المظهر']);
    }

    // Returns just the sidebar menu items HTML for live reload
    public function getPartial()
    {
        \Illuminate\Support\Facades\Cache::forget('sidebar_dashboard_group');

        $sidebar = (new \App\Models\PermissionsGroup())->getAllParentPermissionGroup();
        $dashboardGroup = \App\Models\PermissionsGroup::where('name', 'dashboard')->first();

        return view('admin.components.sidebar-menu-partial', [
            'sidebar'        => $sidebar,
            'dashboardGroup' => $dashboardGroup,
            'active_menu'    => '',
        ]);
    }

    public function getIndex()
    {
        $groups = PermissionsGroup::where('parent_id', 0)
            ->where('status', 1)
            ->where('name', '!=', 'dashboard')
            ->orderByRaw('COALESCE(sort, 9999) ASC')
            ->with(['mychild' => fn($q) => $q->where('status', 1)->orderBy('sort')])
            ->get();

        parent::$data['groups'] = $groups;
        return view('admin.' . $this->path . '.index', parent::$data);
    }

    // AJAX: save new sort order for parent groups and their children
    public function postReorder(Request $request)
    {
        $items = $request->input('items', []);

        if (empty($items) || !is_array($items)) {
            return response()->json(['status' => 'error', 'message' => 'بيانات غير صالحة']);
        }

        foreach ($items as $parentOrder => $parentItem) {
            $parentId   = (int) ($parentItem['id'] ?? 0);
            $parentSort = (int) $parentOrder + 1;

            if ($parentId) {
                PermissionsGroup::where('id', $parentId)->update(['sort' => $parentSort * 10]);
            }

            foreach ($parentItem['children'] ?? [] as $childOrder => $childId) {
                $childId   = (int) $childId;
                $childSort = (int) $childOrder + 1;
                if ($childId) {
                    PermissionsGroup::where('id', $childId)->update(['sort' => $childSort]);
                }
            }
        }

        // Bust sidebar cache so changes reflect immediately
        \Illuminate\Support\Facades\Cache::forget('sidebar_menu');

        return response()->json(['status' => 'success', 'message' => 'تم حفظ الترتيب بنجاح']);
    }

    // AJAX: save color for a single group or all groups
    public function postColor(Request $request)
    {
        $id    = $request->input('id'); // int or 'all'
        $color = $request->input('color', '');

        // Only allow safe color values
        if ($color && !preg_match('/^[a-zA-Z0-9\-_#]+$/', $color)) {
            return response()->json(['status' => 'error', 'message' => 'لون غير صالح']);
        }

        if ($id === 'all') {
            PermissionsGroup::where('status', 1)->update(['color' => $color ?: null]);
            \Illuminate\Support\Facades\Cache::forget('sidebar_dashboard_group');
            return response()->json(['status' => 'success', 'message' => 'تم تطبيق اللون على جميع العناصر']);
        }

        $id = (int) $id;
        if (!$id) {
            return response()->json(['status' => 'error', 'message' => 'معرّف غير صالح']);
        }

        PermissionsGroup::where('id', $id)->update(['color' => $color ?: null]);

        \Illuminate\Support\Facades\Cache::forget('sidebar_dashboard_group');

        return response()->json(['status' => 'success', 'message' => 'تم حفظ اللون بنجاح']);
    }
}
