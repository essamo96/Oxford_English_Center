<?php

namespace App\Http\Controllers;

use Hash;
use Config;
use Carbon\Carbon;
use App\Models\Contacts;
use App\Models\GroupStudents;
use Illuminate\Http\Request;
use App\Mail\NewStudentEmail;
use Illuminate\Support\Facades\Mail;
use App\Notifications\newAdminCreatedNotification;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Contracts\Encryption\DecryptException;
use Mpdf\Mpdf;
class CertificatesController extends Controller
{

    public function __construct()
    {
        parent::__construct();
    }

    ///////////////////////////
    public function getIndex()
    {
        return view('frontend.certificates.view', parent::$data);
    }
    function formatName(string $name)
    {
        // Split the name into parts
        $parts = explode(' ', $name);

        // Check the number of parts
        $numParts = count($parts);

        if ($numParts >= 3) {
            $firstName = $parts[0];
            $middleInitial = ucfirst(substr($parts[1], 0, 1));
            $thirdInitial = ucfirst(substr($parts[2], 0, 1));


            if ($numParts == 4) {
                $lastName = $parts[3];
                return "$firstName . $middleInitial . $thirdInitial . $lastName";
            } else {
                $x = $parts[2];
                $formattedName = "$firstName . $middleInitial . $x";
                return $formattedName;
            }
        }
        return $name;
    }
    // public function postIndex(Request $request)
    // {
    //     $code = $request->input('code');

    //     $customData = GroupStudents::where('cer_code', $code)->first();

    //     if (!$customData) {
    //         return response()->json(['error' => 'Student not found'], 404);
    //     }
    //     $name = $customData->group->name;
    //     $studentname =  $customData->student->name;
    //     $formattedName = $this->formatName($studentname);
    //     $parts = explode('.', $name);
    //     $firstPart = $parts[0];
    //     $data = [
    //         'customData' => $customData,
    //         'firstPart' => $firstPart,
    //         'formattedName' => $formattedName,
    //     ];


    //     $defaultConfig = new \Mpdf\Config\ConfigVariables;
    //     $dd = $defaultConfig->getDefaults();
    //     $fontDirs = $dd['fontDir'];

    //     $defaultFontConfig = new \Mpdf\Config\FontVariables();
    //     $ee = $defaultFontConfig->getDefaults();
    //     $fontData = $ee['fontdata'];

    //     // Configure mPDF with custom fonts
    //     $mpdfConfig = [
    //         'fontDir' => $fontDirs,
    //         'mode' => 'utf-8',
    //         'format' => 'A4',
    //     ];

    //     $mpdf = new Mpdf($mpdfConfig);

    //     // HTML content with custom font styling
    //     $html = view('admin.certificates.levels', $data)->render();
    //     $css = "<style>.title { font-family: 'helveticaneuel'; }</style>";
    //     // $mpdf->WriteHTML($css . $html);

    //     // Output the PDF
    //     return $mpdf->Output('certificate.pdf', 'I');

    //     // return view('student.show', ['studentName' => $studentName]);
    // }
    public function postIndex(Request $request)
    {
        $code = $request->input('code');

        $customData = GroupStudents::where('cer_code', $code)->first();

        if (!$customData) {
            return response()->json(['error' => 'Student not found'], 404);
        }
        $name = $customData->group->name;
        $studentname =  $customData->student->name;
        $formattedName = $this->formatName($studentname);
        $parts = explode('.', $name);
        $firstPart = $parts[0];
        
        $imagePath = str_replace('\\', '/', public_path('Admin_Certifcate/levels_student.png'));

        $data = [
            'customData' => $customData,
            'firstPart' => $firstPart,
            'formattedName' => $formattedName,
            'imagePath' => $imagePath,
        ];

        $defaultConfig = new \Mpdf\Config\ConfigVariables;
        $dd = $defaultConfig->getDefaults();
        $fontDirs = $dd['fontDir'];

        $defaultFontConfig = new \Mpdf\Config\FontVariables();
        $ee = $defaultFontConfig->getDefaults();
        $fontData = $ee['fontdata'];

        $mpdf = new Mpdf([
            'mode' => 'utf-8',
            'format' => 'A4',
            'tempDir' => storage_path('app/mpdf'),
            'curlAllowSelfSigned' => true,
            'allow_local_file_access' => true,
            'fontDir' => array_merge($fontDirs, [
                public_path()
            ]),
            'fontdata' => $fontData + [
                'aguafinascript' => [
                    'R' => 'AguafinaScript-Regular.ttf',
                ]
            ],
            'default_font' => 'sans-serif'
        ]);

        // HTML content with custom font styling
        $html = view('admin.certificates.levels', $data)->render();
        $css = "<style>.title { font-family: 'helveticaneuel'; }</style>";
        $mpdf->WriteHTML($css . $html);

        // Get the PDF content as a string
        $pdfContent = $mpdf->Output('', 'S');

        // Convert the PDF content to base64
        $base64Pdf = base64_encode($pdfContent);

        // Return the base64-encoded PDF as a response
        return response()->json(['pdf' => $base64Pdf, 'student' => $customData]);
    }

    public function generat_pdf_student(Request $request, string $id)
    {
        ini_set('memory_limit', '512M');
        ini_set('max_execution_time', 300);

        try {
            $id = Crypt::decrypt($id);
        } catch (DecryptException $e) {
            return redirect()->back()->with('danger', 'Error Decode');
        }

        $customData = GroupStudents::find($id);
        
        if (!$customData || $customData->student_id != Auth::guard('students')->user()->id) {
            return redirect()->back()->with('danger', 'Unauthorized access');
        }

        if (!$customData->cer_code) {
            return redirect()->back()->with('danger', 'Certificate not available yet');
        }

        $name = $customData->group->name;
        $studentname =  $customData->student->name;
        $formattedName = $this->formatName($studentname);
        $parts = explode('.', $name);
        $firstPart = $parts[0];
        
        $imagePath = str_replace('\\', '/', public_path('Admin_Certifcate/levels_student.png'));
        
        $data = [
            'customData' => $customData,
            'firstPart' => $firstPart,
            'formattedName' => $formattedName,
            'imagePath' => $imagePath,
        ];

        $defaultConfig = new \Mpdf\Config\ConfigVariables;
        $dd = $defaultConfig->getDefaults();
        $fontDirs = $dd['fontDir'];

        $defaultFontConfig = new \Mpdf\Config\FontVariables();
        $ee = $defaultFontConfig->getDefaults();
        $fontData = $ee['fontdata'];

        $mpdf = new Mpdf([
            'mode' => 'utf-8',
            'format' => 'A4',
            'tempDir' => storage_path('app/mpdf'),
            'curlAllowSelfSigned' => true,
            'allow_local_file_access' => true,
            'fontDir' => array_merge($fontDirs, [
                public_path()
            ]),
            'fontdata' => $fontData + [
                'aguafinascript' => [
                    'R' => 'AguafinaScript-Regular.ttf',
                ]
            ],
            'default_font' => 'sans-serif'
        ]);

        $html = view('admin.certificates.levels', $data)->render();
        $css = "<style>.title { font-family: 'helveticaneuel'; }</style>";
        $mpdf->WriteHTML($css . $html);

        return $mpdf->Output('certificate.pdf', 'I');
    }
    

}
