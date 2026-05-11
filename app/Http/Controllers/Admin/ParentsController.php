<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Parents;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;

class ParentsController extends Controller
{
    public function getIndex()
    {
        return view('admin.parents.index');
    }

    public function getList(Request $request)
    {
        $parents = Parents::withCount('students')->select(['id', 'name', 'phone', 'email', 'relationship', 'created_at']);

        // Apply Filters
        if ($request->has('search_text') && $request->search_text != '') {
            $search = $request->search_text;
            $parents->where(function($q) use ($search) {
                $q->where('name', 'like', "%$search%")
                  ->orWhere('phone', 'like', "%$search%")
                  ->orWhere('email', 'like', "%$search%");
            });
        }
        if ($request->has('relationship') && $request->relationship != '') {
            $parents->where('relationship', $request->relationship);
        }

        return DataTables::of($parents)
            ->addColumn('action', function ($parent) {
                return '<button class="btn btn-sm btn-icon btn-light-info me-2 view-children" data-id="' . $parent->id . '" title="عرض الأبناء"><i class="fa fa-child"></i></button>
                        <button class="btn btn-sm btn-icon btn-light-danger delete-btn" data-id="' . $parent->id . '"><i class="fa fa-trash"></i></button>';
            })
            ->editColumn('students_count', function ($parent) {
                return '<span class="badge badge-light-primary">' . $parent->students_count . '</span>';
            })
            ->editColumn('created_at', function ($parent) {
                return $parent->created_at->format('Y-m-d');
            })
            ->rawColumns(['action', 'students_count'])
            ->make(true);
    }

    public function getChildren(Request $request)
    {
        $parent = Parents::with('students')->findOrFail($request->id);
        return view('admin.parents.partials.children', compact('parent'))->render();
    }

    public function postDelete(Request $request)
    {
        $parent = Parents::findOrFail($request->id);
        $parent->delete();
        return response()->json(['success' => true]);
    }
}
