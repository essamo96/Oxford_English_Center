<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Suppliers extends Authenticatable {

    use Notifiable, SoftDeletes;

    protected $table = 'suppliers';
    
    protected $fillable = [
        'company_name',
        'company_reg_no',
        'company_tax_no',
        'company_person',
        'emp_postion',
        'emp_tel',
        'emp_mobile',
        'emp_ext',
        'emp_email',
        'password',
        'token',
        'status',
    ];

    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * Add a new supplier.
     */
    public function addSupplier($company_name, $company_reg_no, $company_tax_no, $company_person, $emp_postion, $emp_tel, $emp_mobile, $emp_ext, $email, $password, $token) {
        $this->company_name = $company_name;
        $this->company_reg_no = $company_reg_no;
        $this->company_tax_no = $company_tax_no;
        $this->company_person = $company_person;
        $this->emp_postion = $emp_postion;
        $this->emp_tel = $emp_tel;
        $this->emp_mobile = $emp_mobile;
        $this->emp_ext = $emp_ext;
        $this->emp_email = $email;
        $this->password = $password;
        $this->token = $token;
        $this->status = 0;

        $this->save();
        return $this;
    }

    /**
     * Get supplier by ID.
     */
    public function getSupplier($id) {
        return $this->find($id);
    }

    /**
     * Update supplier from frontend profile.
     */
    public function updateSupplierFrontend($obj, $company_name, $company_reg_no, $company_tax_no, $company_person, $emp_postion, $emp_tel, $emp_mobile, $emp_ext, $password) {
        $obj->company_name = $company_name;
        $obj->company_reg_no = $company_reg_no;
        $obj->company_tax_no = $company_tax_no;
        $obj->company_person = $company_person;
        $obj->emp_postion = $emp_postion;
        $obj->emp_tel = $emp_tel;
        $obj->emp_mobile = $emp_mobile;
        $obj->emp_ext = $emp_ext;
        if ($password) {
            $obj->password = $password;
        }

        return $obj->save();
    }

    /**
     * Get user by token for verification or password reset.
     */
    public function getUserByToken($token) {
        return $this->where('token', $token)->first();
    }

    /**
     * Update supplier status.
     */
    public function updateStatus($id, $status) {
        return $this->where('id', $id)->update(['status' => $status]);
    }

    /**
     * Update verification/reset token.
     */
    public function updateToken($id, $token) {
        return $this->where('id', $id)->update(['token' => $token]);
    }

}
