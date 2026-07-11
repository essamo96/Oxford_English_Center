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

        $serveUrl = route('brochure.file', ['id' => Crypt::encrypt($program->id)]);
        $downloadUrl = route('brochure.download', ['id' => Crypt::encrypt($program->id)]);

        return view('brochure', [
            'program' => $program,
            'serveUrl' => $serveUrl,
            'downloadUrl' => $downloadUrl,
        ]);
    }

    /**
     * توجيه مباشر لتحميل الملف
     * يستخدم response()->download() لضمان عمل التحميل حتى لو كان الـ symlink معطل
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

        $path = storage_path('app/public/' . $program->brochure_path);
        
        if (!file_exists($path)) {
            abort(404, 'الملف غير موجود في الخادم');
        }

        return response()->download($path);
    }

    /**
     * عرض الملف مباشرة في المتصفح (للـ iframe)
     */
    public function serveFile($id)
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

        $path = storage_path('app/public/' . $program->brochure_path);
        
        if (!file_exists($path)) {
            abort(404, 'الملف غير موجود في الخادم');
        }

        return response()->file($path);
    }

    public function getBrochureQr(Request $request, $id)
    {
        try {
            $decryptedId = Crypt::decrypt($id);
        } catch (DecryptException $e) {
            return response()->json(['status' => 'error', 'message' => 'Error Decode']);
        }

        $program = Programs::find($decryptedId);
        if (!$program) {
            return response()->json(['status' => 'error', 'message' => 'البرنامج غير موجود']);
        }

        $encryptedId = Crypt::encrypt($decryptedId);
        $brochureUrl = route('brochure.show', ['id' => $encryptedId]);
        $hasBrochure = !empty($program->brochure_path);

        // Generate QR Code SVG using bacon/bacon-qr-code
        $renderer = new \BaconQrCode\Renderer\ImageRenderer(
            new \BaconQrCode\Renderer\RendererStyle\RendererStyle(300),
            new \BaconQrCode\Renderer\Image\SvgImageBackEnd()
        );
        $writer = new \BaconQrCode\Writer($renderer);
        
        // Use ErrorCorrectionLevel::H() to ensure the QR code remains readable when we place a logo in the center
        $qrSvg = $writer->writeString($brochureUrl, 'UTF-8', \BaconQrCode\Common\ErrorCorrectionLevel::H());

        return response()->json([
            'status' => 'success',
            'title' => $program->title,
            'url' => $brochureUrl,
            'has_brochure' => $hasBrochure,
            'qr_svg' => $qrSvg,
        ]);
    }
}
