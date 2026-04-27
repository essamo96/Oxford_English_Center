<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Rents;

class RentController extends Controller {

    public function __construct() {
        parent::__construct();
    }

    ///////////////////////////
    public function getIndex() {
        return view('frontend.rent.view', parent::$data);
    }

    public function postContact(Request $request) {
        ////////////////////////////////////////////
        $name = $request->get('name');
        $email = $request->get('email');
        $company = $request->get('company');
        $phone = $request->get('phone');
        $notes = $request->get('notes');

        $validator = Validator::make([
                    'name' => $name,
                    'email' => $email,
                    'company' => $company,
                    'phone' => $phone,
                    'details' => $notes
                        ], [
                    'name' => 'required',
                    'email' => 'required|email',
                    'company' => 'required',
                    'phone' => 'required|digits:6|numeric',
                    'details' => 'required'
        ]);
        //////////////////////////////////////////////////////////
        if ($validator->fails()) {
            $request->session()->flash('danger', $validator->messages());
            return redirect('rent')->withInput();
        } else {
            $contact = new Rents();

            $add = $contact->addRent($name, $phone, $company, $email, $notes);
            if ($add) {
                $request->session()->flash('success', 'تم الارسال');
                return redirect('rent')->withInput();
            } else {
                $request->session()->flash('danger', 'حدثت مشكلة');
                return redirect('rent')->withInput();
            }
        }
    }

}
