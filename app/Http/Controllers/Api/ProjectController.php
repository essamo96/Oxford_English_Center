<?php

namespace App\Http\Controllers\Api;

use Mail;
use App\Models\Students;
use Illuminate\Http\Request;
use App\Mail\NewStudentEmail;
use App\Http\Controllers\Controller;

class ProjectController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        $add = Students::latest('id')->first();
        $email =$add->email;
        $username = substr($add->mobile, 3, 7);
        Mail::to($email)->queue(new NewStudentEmail($add, $username));
        return new NewStudentEmail($add, $username);
        // Mail::queue(new NewStudentEmail($add, $username));
        
        // return "اhiii";
    }
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //

    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
