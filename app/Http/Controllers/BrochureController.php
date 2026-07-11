<?php

namespace App\Http\Controllers;

use App\Models\Programs;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Crypt;
use Illuminate\Contracts\Encryption\DecryptException;

class BrochureController extends Controller
{
    /**
     * صفحة هبوط البروشور — يراها الزائر بعد مسح QR Code
     * تعرض اسم البرنامج + زر تحميل مباشر
     */
    public function show($id)
    {
        try {
            $id = Crypt::decrypt($id);
        } catch (DecryptException $e) {
            abort(404, 'الرابط غير صالح');
        }

        $program = Programs::find($id);

        if (!$program || empty($program->brochure_path)) {
            abort(404, 'البروشور غير متوفر حالياً');
        }

        $brochureUrl = $program->brochure_url; // accessor from model

        return view('brochure', [
            'program' => $program,
            'brochureUrl' => $brochureUrl,
        ]);
    }

    /**
     * توجيه مباشر لتحميل الملف
     * يستخدم redirect إلى الـ Static URL بدلاً من response()->download()
     * لحماية ذاكرة الخادم (RAM) — يتولى Nginx/Apache تقديم الملف
     */
    public function download($id)
    {
        try {
            $id = Crypt::decrypt($id);
        } catch (DecryptException $e) {
            abort(404, 'الرابط غير صالح');
        }

        $program = Programs::find($id);

        if (!$program || empty($program->brochure_path)) {
            abort(404, 'البروشور غير متوفر حالياً');
        }

        // Redirect to static URL — server (Nginx/Apache) handles delivery efficiently
        return redirect($program->brochure_url);
    }
}
