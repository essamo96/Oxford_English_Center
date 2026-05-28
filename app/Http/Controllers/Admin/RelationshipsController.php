<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Relationship;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class RelationshipsController extends Controller
{
    public function index()
    {
        $relationships = Relationship::orderBy('sort_order')->orderBy('id')->get();
        return view('admin.relationships.index', [
            'active_menu'   => 'relationships',
            'relationships' => $relationships,
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name_ar'   => 'required|string|max:100',
            'name_en'   => 'nullable|string|max:100',
        ]);

        $slug = Str::slug($request->name_en ?: $request->name_ar, '-');
        if (Relationship::where('slug', $slug)->exists()) {
            $slug .= '-' . uniqid();
        }

        Relationship::create([
            'name_ar'    => $request->name_ar,
            'name_en'    => $request->name_en,
            'slug'       => $slug,
            'is_active'  => $request->boolean('is_active', true),
            'is_other'   => false,
            'sort_order' => (int) $request->get('sort_order', 0),
        ]);

        return redirect()->back()->with('success', 'تمت إضافة صلة القرابة بنجاح.');
    }

    public function update(Request $request, $id)
    {
        $rel = Relationship::findOrFail($id);

        $request->validate([
            'name_ar' => 'required|string|max:100',
            'name_en' => 'nullable|string|max:100',
        ]);

        $rel->update([
            'name_ar'    => $request->name_ar,
            'name_en'    => $request->name_en,
            'is_active'  => $request->boolean('is_active', true),
            'sort_order' => (int) $request->get('sort_order', $rel->sort_order),
        ]);

        return redirect()->back()->with('success', 'تم تحديث صلة القرابة بنجاح.');
    }

    public function destroy($id)
    {
        $rel = Relationship::findOrFail($id);
        if ($rel->is_other) {
            return redirect()->back()->with('danger', 'لا يمكن حذف خيار "أخرى" — استخدم زر التعطيل بدلاً من الحذف.');
        }
        $rel->delete();
        return redirect()->back()->with('success', 'تم حذف صلة القرابة.');
    }

    /**
     * Public API used by the frontend registration wizard.
     */
    public function publicList()
    {
        $items = Relationship::active()->orderBy('sort_order')->orderBy('id')->get([
            'id', 'name_ar', 'name_en', 'slug', 'is_other',
        ]);
        return response()->json(['success' => true, 'items' => $items]);
    }
}
