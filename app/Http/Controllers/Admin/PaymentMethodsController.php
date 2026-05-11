<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PaymentMethods;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;

class PaymentMethodsController extends Controller
{
    public function getIndex()
    {
        return view('admin.payment_methods.index');
    }

    public function getList()
    {
        $methods = PaymentMethods::query();

        return DataTables::of($methods)
            ->addColumn('action', function ($method) {
                return '<a href="' . route('payment_methods.edit', $method->id) . '" class="btn btn-sm btn-icon btn-light-primary"><i class="fa fa-edit"></i></a>
                        <button class="btn btn-sm btn-icon btn-light-danger delete-btn" data-id="' . $method->id . '"><i class="fa fa-trash"></i></button>';
            })
            ->editColumn('name', function ($method) {
                $img = $method->image ? asset('uploads/' . $method->image) : asset('assets/oxford/img/no-image.png');
                return '<div class="d-flex align-items-center">
                            <div class="symbol symbol-40px me-3">
                                <img src="' . $img . '" class="rounded shadow-sm" alt="">
                            </div>
                            <div class="d-flex flex-column">
                                <span class="text-dark fw-bold text-hover-primary fs-6">' . $method->name . '</span>
                            </div>
                        </div>';
            })
            ->addColumn('credentials_formatted', function ($method) {
                $html = '';
                if ($method->credentials) {
                    foreach ($method->credentials as $key => $value) {
                        $html .= '<span class="badge badge-light-info me-1 mb-1">' . $key . ': ' . $value . '</span>';
                    }
                }
                return $html;
            })
            ->editColumn('is_active', function ($method) {
                $checked = $method->is_active ? 'checked' : '';
                return '<div class="form-check form-switch form-check-custom form-check-solid justify-content-center">
                            <input class="form-check-input h-20px w-30px status-toggle" type="checkbox" data-id="' . $method->id . '" ' . $checked . '>
                        </div>';
            })
            ->rawColumns(['action', 'name', 'credentials_formatted', 'is_active'])
            ->make(true);
    }

    public function getAdd()
    {
        return view('admin.payment_methods.add');
    }

    public function postAdd(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'image' => 'nullable|image|max:2048',
        ]);

        $credentials = [];
        if ($request->has('credentials_keys')) {
            foreach ($request->credentials_keys as $index => $key) {
                if ($key && isset($request->credentials_values[$index])) {
                    $credentials[$key] = $request->credentials_values[$index];
                }
            }
        }
        $validated['credentials'] = $credentials;
        $validated['is_active'] = $request->has('is_active');

        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('payment_methods', 'uploads');
            $validated['image'] = $path;
        }

        PaymentMethods::create($validated);
        return redirect()->route('payment_methods.view')->with('success', 'Payment method added successfully');
    }

    public function getEdit($id)
    {
        $method = PaymentMethods::findOrFail($id);
        return view('admin.payment_methods.edit', compact('method'));
    }

    public function postEdit(Request $request, $id)
    {
        $method = PaymentMethods::findOrFail($id);
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'image' => 'nullable|image|max:2048',
        ]);

        $credentials = [];
        if ($request->has('credentials_keys')) {
            foreach ($request->credentials_keys as $index => $key) {
                if ($key && isset($request->credentials_values[$index])) {
                    $credentials[$key] = $request->credentials_values[$index];
                }
            }
        }
        $validated['credentials'] = $credentials;
        $validated['is_active'] = $request->has('is_active');

        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('payment_methods', 'uploads');
            $validated['image'] = $path;
        }

        $method->update($validated);
        return redirect()->route('payment_methods.view')->with('success', 'Payment method updated successfully');
    }

    public function postDelete(Request $request)
    {
        $method = PaymentMethods::findOrFail($request->id);
        $method->delete();
        return response()->json(['success' => true]);
    }

    public function postStatus(Request $request)
    {
        $method = PaymentMethods::findOrFail($request->id);
        $method->is_active = $request->status;
        $method->save();
        return response()->json(['success' => true]);
    }
}
