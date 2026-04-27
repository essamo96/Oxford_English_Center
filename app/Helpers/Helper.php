<?php

// Code within app\Helpers\Helper.php

namespace App\Helpers;

use App\Models\Images;
use App\Models\Programs;
use App\Models\GroupExamDates;

class Helper {

    public static function get_image($img) {
        return $img;
    }

    public static function has_exam($id) {
        $programs = new Programs();
        $info = $programs->getProgram($id);
        if ($info) {
            return $info->exam;
        } else {
            return 0;
        }
    }

    public static function get_client_img($alubm_id) {
        $imge = new Images();
        $image = $imge->getCoverImage($alubm_id);
        //print_r($image);
        if ($image) {
            return url('File/Images/photo/' . $image->image);
        }
        return url('File/Images/photo/noimage.jpg');
    }

    public static function getGroupExamDates($group_id) {
        $tems = new GroupExamDates();
        return $tems->getGroupExamDates($group_id);
    }

}
