<?php

namespace App\Http\Controllers\Admin;

use App\Models\GroupStudents;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\flash;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Validator;
use Illuminate\Contracts\Encryption\DecryptException;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\Cache;
//use Twitter;
use Illuminate\Support\Facades\Artisan;
use Intervention\Image\Facades\Image;
use App\Models\Categories;
use App\Models\Students;
use App\Models\News as Certificates;
use Mpdf\Mpdf;
class CertificatesController extends AdminController {

    const INSERT_SUCCESS_MESSAGE = "نجاح، تم الإضافة بتجاح";
    const UPDATE_SUCCESS = "نجاح، تم التعديل بنجاح";
    const DELETE_SUCCESS = "نجاح، تم الحذف بنجاح";
    const PASSWORD_SUCCESS = "نجاح، تم تغيير كلمة المرور بنجاح";
    const EXECUTION_ERROR = "عذراً، حدث خطأ أثناء تنفيذ العملية";
    const NOT_FOUND = "عذراً،لا يمكن العثور على البيانات";
    const ACTIVATION_SUCCESS = "نجاح، تم التفعيل بنجاح";
    const DISABLE_SUCCESS = "نجاح، تم التعطيل بنجاح";

//////////////////////////////////////////////
    public function __construct() {
        parent::__construct();
        parent::$data['active_menu'] = 'certificates';
    }

//////////////////////////////////////////////
    public function getIndex(Request $request) {
//         $title = $request->get('title', NULL);
//         $program_id = $request->get('program_id', NULL);
//         $mobile = $request->get('mobile', NULL);
//         $obj = new GroupStudents();
//         $info = $obj->searchCertificates($mobile);
//         $datatable = Datatables::of($info);
// dd($info);
        return view('admin.certificates.view', parent::$data);
    }

//////////////////////////////////////////////
    public function getList(Request $request) {
        $title = $request->get('title', NULL);
        $program_id = $request->get('program_id', NULL);
        $mobile = $request->get('mobile', NULL);
        $obj = new GroupStudents();
        $info = $obj->searchCertificates($mobile);
        $datatable = Datatables::of($info);
// dd($info);
        $datatable->editColumn('student_id', function ($row) {
            $name = $row->student->name ?? '---' ;
            return $name;
            // return '<a onclick="showModal(\'' . $id . '\', \'' . $teacherId . '\')"   id="modal">' . $row->name . '</a>';
        });

        // $datatable->editColumn('teacher_name', function ($row) {
        //     return ($row->teacher ? $row->teacher->name : 'N/A');
        // });
        $datatable->editColumn('group_id', function ($row) {
            $group = $row->group->name ?? '---';
            return $group;
        });
        $datatable->editColumn('program_id', function ($row) {
            $group = $row->group->program->title ?? '---';
            return $group;
        });
        $datatable->editColumn('teacher_id', function ($row) {
            $group = $row->group->teacher->name ?? '---';
            return $group;
        });
        $datatable->editColumn('Certificate', function ($row) {
            $cer_code = $row->cer_code ?? '---';
            return $cer_code;
        });
        // $datatable->editColumn('checkbox', function ($row) {
        //     if (!empty($row->id)) {
        //         $id = $row->id;
        //         return '<input type="checkbox" class="checkboxes" name="mobile[' . $id . ']" value="' . $id . '" data-id="' . $id . '" />';
        //     }
        //     return '';
        // });
        // $datatable->editColumn('studens_no', function ($row) {
        //     $groupStudents = new GroupStudents();
        //     $studens_ids = $groupStudents->countStudentGroup($row->id);
        //     $studens_no = Students::whereIn('id', $studens_ids)->where('delaying', 0)->where('status', 1)->count();

        //     return '
        //     <a href=' . URL("admin/groups/teacher/students/" . Crypt::encrypt($row->id)) . ' class="btn btn-default btn-sm"  style="color: #ffffff ; width: 90px; background-color: #2f353b;">
        //         <strong  style="color: #ffc038">' . $studens_no . '</strong><strong style="color: #ffffff">  طالب </strong>  </a></div>';
        // });
        // $datatable->editColumn('code', function ($row) {
        //     $groupStudents = new GroupStudents();
        //     $studens_no = $groupStudents->countStudentGroup($row->id);

        //     $x = Str::random(8);
        //     return '
        //     <a id="generate-key2" onclick="generatekey(' . $row->id . ')" data-random="' . $x . '"  class="btn btn-warning btn-sm" style=" color: #fff;background-color: #ff5900; border-color: #000000;width: 60px;" " >
        //     <i class="bi bi-arrow-repeat"></i> كود</a></div>';
        // });

        // $datatable->editColumn('time_day', function ($row) {
        //     $dayes = $row->ctime->days;
        //     if (isset($dayes) != '') {

        //         return '
        //         <a  class="btn btn-default btn-sm"  style="color: #ffffff ; width: 132px; background-color: #d3640c;">  ' . $dayes . ' </a>';
        //     } else {
        //         return '
        //         <i class="bi bi-exclamation-circle" style="color:#f01000;"> </i><strong>لا يوجد</strong>';
        //     }
        // });
        // $datatable->editColumn('time', function ($row) {
        //     $times = $row->ctime->times;
        //     if (isset($times) != '') {
        //         return '
        //         <a  class="btn btn-default btn-sm"  style="color: #ffffff ; width: 100px; background-color: #3fc3ee;">  ' . $times . ' </a>';
        //     } else {
        //         return '
        //         <i class="bi bi-exclamation-circle" style="color:#f01000;"> </i><strong>لا يوجد</strong>';
        //     }
        // });

        // $datatable->editColumn('status', function ($row) {
        //     $data['id'] = $row->id;
        //     $data['status'] = $row->status;

        //     return view('admin.groups.parts.status', $data)->render();
        // });

        $datatable->addColumn('actions', function ($row) {
            // $data['x'] = 2;
            $data['id'] = $row->id;
            // $data['teacher_id'] = $row->teacher_id;
            // // dd($data['teacher_id']);
            $data['btn_class'] = parent::$data['btn_class'];

            // return 'xxx';
            return view('admin.certificates.parts.actions', $data)->render();
        });
        $datatable->escapeColumns(['*']);
        return $datatable->make(true);
    }

//////////////////////////////////////////////
    public function getAdd() {
        $categories = new Categories();
        parent::$data['info'] = $categories->getAricleActiveCategories();
        return view('admin.certificates.add', parent::$data);
    }

//////////////////////////////////////////////
    public function postAdd(Request $request) {
        $category_id = $request->get('category_id');
        $title = $request->get('title');
        $onwer = $request->get('onwer');
        $source = $request->get('source');
        $sub = $request->get('sub');
        $descs = $request->get('descs');
        $image = $request->get('image');
        $img_notes = $request->get('img_notes');
        $tags = $request->get('tags');
        $pub_date = $request->get('pub_date');
        $resort = $request->get('resort');
        $publish = (int) $request->get('publish');
        $sidebar = (int) $request->get('sidebar');


        $validator = Validator::make([
                    'category_id' => $category_id,
                    'title' => $title,
                    'onwer' => $onwer,
                    'sub' => $sub,
                    'descs' => $descs,
                    'image' => $image,
                        ], [
                    'category_id' => 'required',
                    'title' => 'required',
                    'onwer' => 'required',
                    'sub' => 'required',
                    'descs' => 'required',
                    'image' => 'required',
        ]);
//////////////////////////////////////////////////////////
        if ($validator->fails()) {
            session()->flash('danger', $validator->messages());
            return redirect(route('certificates.add'))->withInput();
        } else {
            $image_explode = explode('/', $image);
            $size = sizeof($image_explode);
            $image_name = $image_explode[$size - 1];
            $final_explode = explode($image_name, $image);
            $thumb = $final_explode[0] . 'thumbs/' . $image_name;
///////////////////
            $certificates = new Certificates();

////////////////////////////////////////////
            $add = $certificates->addCertificates($title, $onwer, $source, $sub, $descs, $thumb, $image, $img_notes, $category_id, $tags, $resort, $pub_date, $publish, $sidebar, Auth::guard('admin')->user()->id);
            if ($add) {
                if ($publish == 1) {
                    $this->clearCache($category_id);
                }
///////////////////////////////////////////////////////////////////
                session()->flash('success', self::INSERT_SUCCESS_MESSAGE);
                return redirect(route('certificates.view'));
            } else {
                session()->flash('danger', self::EXECUTION_ERROR);
                return redirect(route('certificates.add'))->withInput();
            }
        }
    }

//////////////////////////////////////////////
    public function getEdit(Request $request, string $id) {
        try {
            $id = Crypt::decrypt($id);
        } catch (DecryptException $e) {
            session()->flash('danger', self::NOT_FOUND);
            return redirect(route('certificates.view'));
        }
//////////////////////////////////////////////
        $certificates = new Certificates();
        $categories = new Categories();
        $info = $certificates->getNew($id);
        if ($info) {
            parent::$data['categories'] = $categories->getAllActiveCategories();
            parent::$data['info'] = $info;
            return view('admin.certificates.edit', parent::$data);
        } else {
            session()->flash('danger', self::NOT_FOUND);
            return redirect(route('certificates.view'));
        }
    }

//////////////////////////////////////////////
    public function postEdit(Request $request, string $id) {
        try {
            $encrypted_id = $id;
            $id = Crypt::decrypt($id);
        } catch (DecryptException $e) {
            session()->flash('danger', self::NOT_FOUND);
            return redirect(route('certificates.view'));
        }
/////////////////////////////////////////
        $certificates = new Certificates();
        $info = $certificates->getNew($id);
        if ($info) {
            $category_id = $request->get('category_id');
            $title = $request->get('title');
            $onwer = $request->get('onwer');
            $source = $request->get('source');
            $sub = $request->get('sub');
            $descs = $request->get('descs');
            $image = $request->get('image');
            $img_notes = $request->get('img_notes');
            $tags = $request->get('tags');
            $pub_date = $request->get('pub_date');
            $resort = $request->get('resort');
            $publish = (int) $request->get('publish');
            $sidebar = (int) $request->get('sidebar');


            $validator = Validator::make([
                        'category_id' => $category_id,
                        'title' => $title,
                        'onwer' => $onwer,
                        'sub' => $sub,
                        'descs' => $descs,
                            ], [
                        'category_id' => 'required',
                        'title' => 'required',
                        'onwer' => 'required',
                        'sub' => 'required',
                        'descs' => 'required',
            ]);
//////////////////////////////////////////////////////////
            if ($validator->fails()) {
                session()->flash('danger', $validator->messages());
                return redirect(route('certificates.edit', ['id' => $encrypted_id]))->withInput();
            } else {
                $image_explode = explode('/', $image);
                $size = sizeof($image_explode);
                $image_name = $image_explode[$size - 1];
                $final_explode = explode($image_name, $image);
                $thumb = $final_explode[0] . 'thumbs/' . $image_name;
                $old_category_id = $info->category_id;
////////////////////////////////////////////
                $update = $certificates->updateCertificates($info, $title, $onwer, $source, $sub, $descs, $thumb, $image, $img_notes, $category_id, $tags, $resort, $pub_date, $publish, $sidebar);
                if ($update) {
                    if ($info->publish == 1) {
                        if ($old_category_id != $category_id) {
                            $this->clearCache($old_category_id);
                        }
                        $this->clearCache($category_id);
                     //   $this->getMedium($title, $descs, $image);
                        //$this->getTwitter($update);
                    }
///////////////////////////////////////////////////////////
                    session()->flash('success', self::UPDATE_SUCCESS);
                    return redirect(route('certificates.view'));
                } else {
                    session()->flash('danger', self::EXECUTION_ERROR);
                    return redirect(route('certificates.edit', ['id' => $encrypted_id]))->withInput();
                }
            }
        } else {
            session()->flash('danger', self::NOT_FOUND);
            return redirect(route('certificates.view'));
        }
    }

////////////////////////////////////////////////
    public function postDelete(Request $request) {
        $id = $request->get('id');
        try {
            $id = Crypt::decrypt($id);
        } catch (DecryptException $e) {
            return response()->json(['status' => 'error', 'message' => 'Error Decode']);
        }
/////////////////////////////////////
        $certificates = new Certificates();
        $info = $certificates->getNew($id);
        if ($info) {
            $delete = $certificates->deleteCertificates($info);
            if ($delete) {
                $this->clearCache($info->category_id);
////////////////////////////////////
                return response()->json(['status' => 'success', 'message' => self::DELETE_SUCCESS]);
            } else {
                return response()->json(['status' => 'error', 'message' => self::EXECUTION_ERROR]);
            }
        } else {
            return response()->json(['status' => 'error', 'message' => self::NOT_FOUND]);
        }
    }

//////////////////////////////////////////////
    public function getMedium($title, $content, $image) {
//        $credentials = [
//            'client-id' => 'd2172702148',
//            'client-secret' => '442daeaf400a782004e94201ff21526ccdb1235e',
//            'redirect-url' => 'http://demo.uae71.com/public/admin/certificates/status',
//            'state' => 'somesecret',
//            'scopes' => 'scope1,scope2',
//        ];
//
//
//        $medium = new Medium();
//        $medium->connect($credentials);
//        $accessToken = '25ecd94df3fa5be8b07521507f5f622935d3a116ee34738a7aed592b98669706c';
//        $medium->setAccessToken($accessToken);
//        $user = $medium->getAuthenticatedUser();
//        $intro = '<p><img src="' . URL::to($image) . '" /</p><div style="direction:rtl;">' . $content . '</div>';
//
//        $data = [
//            'title' => $title,
//            'contentFormat' => 'html',
//            'content' => $intro,
//            'publishStatus' => 'public',
//        ];
//        $post = $medium->createPost($user->data->id, $data);
    }

    public function getTwitter($certificates) {//
//        try {
////            $new = new Certificates();
////            $certificates = $new->getNew('53695');
//            $uploaded_media = Twitter::uploadMedia(['media' => file_get_contents(URL::to($certificates->image))]);
//            $status = ($certificates->category_id == 11 ? $certificates->onwer . ' يكتب "' : '') . $certificates->title . ($certificates->category_id == 11 ? '"' : '') . ' http://uae71.com/posts/' . $certificates->id . ' #الإمارات71';
//            return Twitter::postTweet(['status' => $status, 'media_ids' => $uploaded_media->media_id_string]);
//        } catch (\Exception $e) {
//            //   dd(Twitter::logs());
//            return false;
//        }
    }

    public function postPublish(Request $request) {

        $id = $request->get('id');
        try {
            $id = Crypt::decrypt($id);
        } catch (DecryptException $e) {
            return response()->json(['status' => 'error', 'message' => 'Error Decode']);
        }

        $certificates = new Certificates();
        $info = $certificates->getNew($id);
        if ($info) {
            $publish = $info->publish;
            if ($publish == 0) {
                $update = $certificates->updatePublish($id, 1);
                if ($update) {
                    $this->clearCache($info->category_id);
////////////////////////////////////
                    return response()->json(['status' => 'success', 'message' => self::ACTIVATION_SUCCESS, 'type' => 'yes']);
                } else {
                    return response()->json(['status' => 'error', 'message' => self::EXECUTION_ERROR]);
                }
            } else {
                $update = $certificates->updatePublish($id, 0);
                if ($update) {

                    $this->clearCache($info->category_id);
////////////////////////////////////
                    return response()->json(['status' => 'success', 'message' => self::DISABLE_SUCCESS, 'type' => 'no']);
                } else {
                    return response()->json(['status' => 'error', 'message' => self::EXECUTION_ERROR]);
                }
            }
        } else {
            return response()->json(['status' => 'error', 'message' => self::NOT_FOUND]);
        }
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

    public function generat_pdf(Request $request, string $id)
    {
        try {
            $id = Crypt::decrypt($id);
        } catch (DecryptException $e) {
            return response()->json(['status' => 'error', 'message' => 'Error Decode']);
        }

        $customData = GroupStudents::find($id);
        if (!$customData) {
            return response()->json(['status' => 'error', 'message' => 'Data not found']);
        }
        $name = $customData->group->name ?? '---';
        $studentname =  $customData->student->name ?? '---';
        $formattedName = $this->formatName($studentname);
        $parts = explode('.', $name);
        $firstPart = $parts[0] ?? '---';
        $imagePath = str_replace('\\', '/', public_path('Admin_Certifcate/levels_Admin.png'));

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

        // Output the PDF
        return $mpdf->Output('certificate.pdf', 'I');
    }

/////////////////////////////////////////
    public function cleaAllCache() {

        Artisan::call('cache:clear');
        return redirect(route('certificates.view'));
    }

    public function clearCache(int $category_id) {
        $certificates = new Certificates();
///////////// Inner Category Page///////////////
        Cache::forget('category_certificates_' . $category_id);
        $certificates_category = $certificates->getCertificatesByCategory($category_id, 0, 5);
        Cache::forever('category_certificates_' . $category_id, $certificates_category);

///////////// Home Page///////////////
        $special_certificates = $certificates->getSpecialCertificates();
        Cache::forget('slider_certificates');
        Cache::forever('slider_certificates', $special_certificates);

        $special_certificates = $certificates->getLastCertificates(5);
        Cache::forget('last_certificates');
        Cache::forever('last_certificates', $special_certificates);
    }




}
