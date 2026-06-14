<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SMS extends Model
{
    function sendSMS($numbers, $message)
    {
        $res = new \stdClass();
        $result = app(\App\Services\SmsService::class)->send($numbers, $message);
        $status = $result['success'] ? 1 : $result['status'];
        // $status = file_get_contents("http://www.alqudwasms.com/api.php?user=OXFORD.E.C&pass=1929459&to=" . $numbers .
        // "&message=" . urlencode($message) . "&sender=OXFORD.E.C");
        if ($status == -999) {
            $res->status = FALSE;
            $res->msg = 'فشل الارسال بسبب خلل في مزود الخدمة';
        } 
        elseif ($status == -113) {
            $res->status = FALSE;
            $res->msg = 'لا يوجد رصيد';
        } 
        elseif ($status == -115) {
            $res->status = FALSE;
            $res->msg = 'the sender not available';
        } 
        elseif ($status == -2) {
            $res->status = FALSE;
            $res->msg = 'ارقام غير مدعومة في هذه المنطقة';
        } 
        elseif ($status == -110) {
            $res->status = FALSE;
            $res->msg = 'رقم الجوال خاطئ';
        } elseif ($status == -116) {
            $res->status = FALSE;
            $res->msg = 'invalid sender name';
        } 
        return $res;

    }
    //     $numbers;
    //     $type = 2;
    //     $res = new \stdClass();
    //     $status = file_get_contents("http://www.alqudwasms.com/api.php?user=TEST&pass=123456&to=" . $numbers .
    //     "&message=" . urlencode($message) ."&sender=TESTSENDER ");
    //     if ($status == -999) {
    //         $res->status = TRUE;
    //         $res->msg = 'تم الارسال';
    //     } 
    //     elseif ($status == -113) {
    //         $res->status = FALSE;
    //         $res->msg = 'لا يوجد رصيد';
    //     } elseif ($status == -110) {
    //         $res->status = FALSE;
    //         $res->msg = 'رقم الجوال خاطئ';
    //     } elseif ($status == -115) {
    //         $res->status = FALSE;
    //         $res->msg = 'المرسل';
    //     } elseif ($status == -110) {
    //         $res->status = FALSE;
    //         $res->msg = 'Users & Paswword';
    //     }
    //     return $res;

    // }
}
        // elseif ($status == -115) {
        //     $res->status = FALSE;
        //     $res->msg = 'API';
        // } 
        // elseif ($status == 3000) {
        //     $res->status = FALSE;
        //     $res->msg = 'Type';
        // } 