<?php

use App\Models\Students;
// use Route;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Route;

/*
  |--------------------------------------------------------------------------
  | Web Routes
  |--------------------------------------------------------------------------
  |
  | Here is where you can register web routes for your application. These
  | routes are loaded by the RouteServiceProvider within a group which
  | contains the "web" middleware group. Now create something great!
  |
  */

Route::get('/clear-cache', function () {

    Artisan::call('cache:clear');
    Artisan::call('config:clear');
    Artisan::call('route:clear');
    Artisan::call('view:clear');
    return "Application cache cleared successfully!";
});
Route::group(['middleware' => ['web']], function () {
    Route::get('/generate-qr-code', 'HomepageController@generateQRCode');
    Route::get('/', ['as' => 'homepage.view', 'uses' => 'HomepageController@getIndex']);
    Route::get('community', ['as' => 'categories.view', 'uses' => 'CategoriesController@getCommunity']);
    Route::get('posts/{id}', ['as' => 'news.details', 'uses' => 'NewsController@getNews']);
    Route::get('contact', ['as' => 'contact.view', 'uses' => 'ContactController@getIndex']);
    Route::post('contact', ['as' => 'contact.view', 'uses' => 'ContactController@postContact']);
    Route::get('certificates', ['as' => 'certificates.view', 'uses' => 'CertificatesController@getIndex']);
    Route::post('certificates/student', ['as' => 'certificates.student', 'uses' => 'CertificatesController@postIndex']);
    Route::get('exam', ['as' => 'contact.exam', 'uses' => 'ContactController@getExam']);
    Route::post('exam', ['as' => 'contact.exam', 'uses' => 'ContactController@postExam']);
    Route::get('book', ['as' => 'contact.book', 'uses' => 'ContactController@getBook']);
    Route::post('book', ['as' => 'contact.book', 'uses' => 'ContactController@postBook']);
    Route::get('jobs', ['as' => 'contact.job', 'uses' => 'ContactController@getJob']);
    Route::post('jobs', ['as' => 'contact.job', 'uses' => 'ContactController@postJob']);
    Route::get('page/{title}', ['as' => 'page.view', 'uses' => 'PageController@getPage']);
    Route::get('test-of-english', ['as' => 'page.view', 'uses' => 'PageController@getTest']);
    Route::get('test-format', ['as' => 'page.view', 'uses' => 'PageController@getFormat']);
    Route::get('upcoming-exam-dates', ['as' => 'page.view', 'uses' => 'PageController@getDates']);
    Route::get('photos', ['as' => 'pics.view', 'uses' => 'PhotoController@getPics']);
    Route::get('gallary/{id}', ['as' => 'picscategories.view', 'uses' => 'PhotoController@getIndex']);
    Route::get('photos1', ['as' => 'pics.view', 'uses' => 'PhotoController@getIndex']);
    Route::get('videos', ['as' => 'video.view', 'uses' => 'VideoController@getVideos']);
    Route::get('family', ['as' => 'partners.index', 'uses' => 'PartnersController@getFamily']);
    Route::get('partners', ['as' => 'partners.index', 'uses' => 'PartnersController@getIndex']);
    Route::get('test', ['as' => 'contact.test', 'uses' => 'ContactController@send_mail']);
    Route::get('login', ['as' => 'web.login', 'uses' => 'LoginController@getIndex']);
    Route::get('login/teacher', ['as' => 'web.teacher.login', 'uses' => 'LoginController@getTeachrIndex']);
    Route::post('login', ['as' => 'web.login', 'uses' => 'LoginController@postIndex']);
    Route::get('logout', ['as' => 'web.logout', 'uses' => 'LoginController@getLogout']);
    Route::post('grope/code/check', ['as' => 'groups.code.check', 'uses' => 'GroupsController@checkGropeCodeForStudent']);
});

////////////////////////////////////////////////////////////////////////////////////////////////////////////
Route::group(['middleware' => ['auth:teachers']], function () {
    //Teachers section
    Route::get('progress/{id}',  ['as' => 'teacher.progress', 'uses' => 'TeachersController@getIndex2']);
    Route::get('teacher', ['as' => 'teacher.view', 'uses' => 'TeachersController@getIndex']);
    Route::post('teacher', ['as' => 'teacher.save.password', 'uses' => 'TeachersController@postPassword']);
    Route::post('teacher/editProfile', ['as' => 'teacher.profile.edit', 'uses' => 'TeachersController@postEdit']);
    Route::post('teacher/change/status', ['as' => 'teacher.close.class', 'uses' => 'TeachersController@postCloseClass']);

    //Groups Route
    Route::get('group/{id}', ['as' => 'teacher.group.view', 'uses' => 'GroupsController@getIndex']);
    Route::get('group/attendance/{id}', ['as' => 'teacher.group.attendance', 'uses' => 'GroupsController@getAttendance']);
    Route::post('group/attendance/{id}', ['as' => 'post.group.attendance', 'uses' => 'GroupsController@postAttendance']);
    Route::get('student/group/evaluate/{group_id}/{student_id}', ['as' => 'teacher.evaluate.view', 'uses' => 'GroupsController@getEvaluate']);
    Route::get('student/removed/byteacher/{group_id}/{student_id}', ['as' => 'teacher.remove.student', 'uses' => 'GroupsController@removeStudent']);
    Route::post('student/group/evaluate', ['as' => 'teacher.evaluate.post', 'uses' => 'GroupsController@postEvaluate']);
    Route::post('group/{id}', ['as' => 'teacher.group.view', 'uses' => 'GroupsController@postIndex']);
    Route::get('group/library/{id}', ['as' => 'teacher.library.view', 'uses' => 'GroupsController@getTeacherLibrary']);
    Route::post('group/library/{id}', ['as' => 'teacher.library.view', 'uses' => 'GroupsController@posTeacherLibrary']);
    Route::get('groupStd/{id}', ['as' => 'teacher.groupStd.view', 'uses' => 'GroupsController@getGroupStd']);
    Route::get('examDate/{id}', ['as' => 'teacher.group.examDate', 'uses' => 'GroupsController@getExamDate']);
    Route::post('examDate/{id}', ['as' => 'teacher.group.examDate', 'uses' => 'GroupsController@postExamDate']);
    Route::post('teacher/notifications', ['as' => 'teacher.notifications', 'uses' => 'TeachersController@indexTeacherNotify']);
    Route::post('teacher/grope/info', ['as' => 'teacher.showGroue_info', 'uses' => 'GroupsController@postTeacherGroueInfo']);
    Route::get('students/grope/{group_id}/{teacher_id}', ['as' => 'teacher.showGroueStudents', 'uses' => 'GroupsController@postTeacherGroueStudents']);
    Route::get('grope/Exam/{teacher_id}', ['as' => 'teacher.ExamDates', 'uses' => 'GroupsController@getGroupExamDates']);
    //Chat
    Route::get('load-latest-messages_teacher', 'MessagesController@getLoadLatestMessages')->defaults('type', 'teacher');
    Route::post('send_teacher', 'MessagesController@postSendMessage')->defaults('type', 'teacher');
    Route::get('fetch-old-messages', 'MessagesController@getOldMessages');
    Route::post('teachers/admin/messages', ['as' => 'teachers.admin.messages', 'uses' => 'TeachersController@TeachersSendMessage']);
});
////////////////////////////////////////////////////////////////////////////////////////////////////////////
Route::group(['middleware' => ['auth:students']], function () {
    //Students section
    Route::get('student', ['as' => 'student.view', 'uses' => 'StudentsController@getIndex']);
    Route::post('student', ['as' => 'student.save.password', 'uses' => 'StudentsController@postPassword']);
    Route::post('student/editProfile', ['as' => 'student.profile.edit', 'uses' => 'StudentsController@postEdit']);
    Route::get('student/degrees/{id}', ['as' => 'student.degrees', 'uses' => 'StudentsController@getDegrees']);
    Route::get('students/group/fees/{group_id}', ['as' => 'student.group.fee', 'uses' => 'StudentsController@getAjaxGroupFees']);
    Route::get('students/subjects/{id}', ['as' => 'student.group.subjects', 'uses' => 'GroupsController@getFiles']);
    Route::post('student/grope/info', ['as' => 'students.showGroue_info', 'uses' => 'GroupsController@postShowGroueInfo']);
    Route::get('student/notifications', ['as' => 'student.notifications', 'uses' => 'StudentsController@indexStudentNotify']);
    Route::post('students/grope/marks', ['as' => 'student.showGroueMarks', 'uses' => 'StudentsController@getStudentGroueMarks']);
    Route::post('students/grope/Progress', ['as' => 'student.showGroueProgress', 'uses' => 'StudentsController@getStudentGroueProgress']);
    Route::post('grope/Exam/{student_id}', ['as' => 'student.ExamDates', 'uses' => 'StudentsController@getGroupStudentExamDates']);
    Route::get('student/evaluate/{id}', ['as' => 'student.evaluate', 'uses' => 'StudentsController@getEvaluate']);
    Route::get('student/courses/partial', ['as' => 'student.courses.partial', 'uses' => 'StudentsController@getCoursesPartial']);
    Route::post('student/admin/send-message', ['as' => 'student.send_admin_message', 'uses' => 'StudentsController@postSendMessageToAdmin']);
    //Chat
    Route::post('student/ask_update/profile', ['as' => 'ask.update.profile', 'uses' => 'StudentsController@updateProfile']);
    Route::get('load-latest-messages_student', 'MessagesController@getLoadLatestMessages')->defaults('type', 'student');
    Route::post('send_student', ['as' => 'student.postSendMessage', 'uses' => 'MessagesController@postSendMessage'])->defaults('type', 'student');
    Route::get('fetch-old-messages', 'MessagesController@getOldMessages');
    Route::get('student/certificate/download/{id}', ['as' => 'student.certificate.download', 'uses' => 'CertificatesController@generat_pdf_student']);
});
////////////////////////////////////////////////////////////////////////////////////////////////////////////
// route to open admin page
Route::get('/admin', function () {
    return redirect('admin/dashboard');
});

// Login Route
Route::group(['namespace' => 'Admin', 'prefix' => 'admin', 'middleware' => ['web', 'guest:admin']], function () {
    Route::get('login', ['as' => 'app.login', 'uses' => 'LoginController@getIndex']);
    Route::post('login', ['as' => 'app.login', 'uses' => 'LoginController@postIndex']);
});

Route::prefix('emails')->group(function () {
    Route::get('mailable', function () {
        $student = Students::find(100);
        return new App\Mail\NewStudentEmail($student, 'testuser', 'testpass');
    });
});
Route::group(['namespace' => 'Admin', 'prefix' => 'admin', 'middleware' => ['web', 'auth:admin']], function () {
    Route::get('lang/{lang}', ['as' => 'dashboard.lang', 'uses' => 'DashboardController@getLang']);

    // Route::get('/generat_pdf/{id}', ['as' => 'students.groups.pdf', 'middleware' => ['permission:admin.students.edit'], 'uses' => 'StudentsController@generat_pdf']);
    Route::get('generat_pdf/{id}', ['as' => 'students.groups.pdf', 'middleware' => ['permission:admin.students.edit'], 'uses' => 'CertificatesController@generat_pdf']);

    Route::get('students/groups/edit/{student_id}/{group_id}', ['as' => 'students.groups.edit', 'middleware' => ['permission:admin.students.edit'], 'uses' => 'StudentsController@getStudentClassEdit']);
    Route::get('students/groups/add/{student_id}', ['as' => 'students.groups.add', 'middleware' => ['permission:admin.students.edit'], 'uses' => 'StudentsController@getStudentAddGrope']);
    Route::post('students/groups/add/grope', ['as' => 'students.groups.post.addnew', 'middleware' => ['permission:admin.students.edit'], 'uses' => 'StudentsController@postAddGrope']);
    Route::post('students/notes/add', ['as' => 'notes.store', 'middleware' => ['permission:admin.students.edit'], 'uses' => 'StudentsController@postAddNote']);
    Route::post('students/DelayCusess/add', ['as' => 'DelayCusess.store', 'middleware' => ['permission:admin.students.edit'], 'uses' => 'StudentsController@postAddDelayCusess']);
    Route::post('student/grope/edit', ['as' => 'change.student.grope', 'middleware' => ['permission:admin.students.edit'],  'uses' => 'StudentsController@postChangeGrope']);
    Route::post('groups/student/delete/{student_id}', ['as' => 'SGdelete', 'middleware' => ['permission:admin.groups.student.view'], 'uses' => 'GroupsController@getStudentAxiosDelete']);
    Route::get('groups/end', ['as' => 'end.groups.view', 'middleware' => ['permission:admin.groups.student.view'], 'uses' => 'GroupsController@getEndGropes']);
    Route::get('program/groups/e', ['as' => 'end.groups.list', 'middleware' => ['permission:admin.groups.view|admin.groups.add|admin.groups.edit|admin.groups.delete|admin.groups.status'], 'uses' => 'GroupsController@listEndGropes']);
    Route::get('studentsTeacher/birthdays', ['as' => 'birthdayes.view', 'middleware' => ['permission:admin.groups.student.view'], 'uses' => 'GroupsController@getbirthdayes']);
    Route::get('studentsTeacher/birthdays/list', ['as' => 'birthdayes.list', 'middleware' => ['permission:admin.groups.student.view'], 'uses' => 'GroupsController@getbirthdayeslist']);
    Route::get('program/groups/{id}', ['as' => 'program.groups.view', 'middleware' => ['permission:admin.groups.student.view'], 'uses' => 'ProgramsController@getProgramGrope']);
    Route::get('program/groups', ['as' => 'program.groups.list', 'middleware' => ['permission:admin.groups.student.view'], 'uses' => 'ProgramsController@listProgramGrope']);
    Route::get('students/gropes/{id}', ['as' => 'students.gropes', 'middleware' => ['permission:admin.groups.view|admin.groups.add|admin.groups.edit|admin.groups.delete|admin.groups.status'], 'uses' => 'StudentsController@getIndexOfGropes']);
    Route::get('students/groups/list/{id}', ['as' => 'students.groups.list', 'middleware' => ['permission:admin.groups.view|admin.groups.add|admin.groups.edit|admin.groups.delete|admin.groups.status'], 'uses' => 'StudentsController@getListOfGropes']);
    Route::post('students/delaying', ['as' => 'students.delaying', 'middleware' => ['permission:admin.students.status|admin.groups.view|admin.groups.add|admin.groups.edit|admin.groups.delete|admin.groups.status'], 'uses' => 'StudentsController@postDelaying']);
    Route::post('groups/notify', ['as' => 'groups.send.message', 'middleware' => ['permission:admin.students.status|admin.groups.view|admin.groups.add|admin.groups.edit|admin.groups.delete|admin.groups.status'], 'uses' => 'GroupsController@postSendMessage']);
    Route::post('student/notify', ['as' => 'send.message', 'middleware' => ['permission:admin.students.status|admin.groups.view|admin.groups.add|admin.groups.edit|admin.groups.delete|admin.groups.status'], 'uses' => 'StudentsController@SendMessage']);
    Route::post('student/CEmail', ['as' => 'send.CEmail', 'middleware' => ['permission:admin.students.status|admin.groups.view|admin.groups.add|admin.groups.edit|admin.groups.delete|admin.groups.status'], 'uses' => 'StudentsController@SendCustomEmail']);
    Route::post('student/CEmail/Birthday', ['as' => 'send.CEmail.Birthday', 'middleware' => ['permission:admin.students.status|admin.groups.view|admin.groups.add|admin.groups.edit|admin.groups.delete|admin.groups.status'], 'uses' => 'StudentsController@SendCustomEmail2']);
    Route::post('teacher/notify', ['as' => 'send.teacher.message', 'middleware' => ['permission:admin.students.status|admin.groups.view|admin.groups.add|admin.groups.edit|admin.groups.delete|admin.groups.status'], 'uses' => 'TeacherController@SendMessage']);
    Route::post('student/admin/messages', ['as' => 'student.admin.messages', 'middleware' => ['permission:admin.students.status|admin.groups.view|admin.groups.add|admin.groups.edit|admin.groups.delete|admin.groups.status'], 'uses' => 'StudentsController@StudentsSendMessage']);
    Route::post('admin/sms', ['as' => 'send.admin.sms', 'uses' => 'TeacherController@SMS']);
    Route::post('admin/groups/sms', ['as' => 'send.groups.sms', 'uses' => 'StudentsController@SMSGruop']);
    Route::post('show/class_infos', ['as' => 'send.groups.infos', 'uses' => 'GroupsController@show']);
    //Memberships  Route //dd member
    Route::get('home/membership', ['as' => 'dashboard.view.membership', 'middleware' => ['permission:admin.groups.student.view|admin.groups.student.add|admin.groups.student.edit|admin.groups.student.delete|admin.groups.student.status'], 'uses' => 'MembershipsController@getIndex']);
    Route::get('membership/list', ['as' => 'membership.list', 'uses' => 'MembershipsController@getmembershiplist']);
    Route::get('home/askcourses', ['as' => 'dashboard.view.askcourses', 'middleware' => ['permission:admin.groups.student.view|admin.groups.student.add|admin.groups.student.edit|admin.groups.student.delete|admin.groups.student.status'], 'uses' => 'MembershipsController@viewaskcourses']);
    Route::get('askcourses/list', ['as' => 'askcourses.list', 'uses' => 'MembershipsController@askcourseslist']);
    Route::post('students/status/membership', ['as' => 'students.status.membership', 'middleware' => ['permission:admin.students.status'], 'uses' => 'MembershipsController@postStatus']);
    Route::post('students/bulk-status/membership', ['as' => 'students.bulk.status.membership', 'middleware' => ['permission:admin.students.status'], 'uses' => 'MembershipsController@postBulkStatus']);
    Route::post('askcourses/delete', ['as' => 'askcourses.delete', 'middleware', 'uses' => 'StudentsController@postDeleteCorseStudentById']);
    Route::post('askcourses/status', ['as' => 'askcourses.status', 'middleware' => ['permission:admin.courses.status'], 'uses' => 'StudentsController@postStatusOfStudentInCourseById']);
    Route::post('grope/code/insert', ['as' => 'groups.add.code', 'uses' => 'GroupsController@addCodeForGrope']);
    Route::get('students/messages', ['as' => 'students.messages', 'uses' => 'MembershipsController@indexStudentsMessages']);
    Route::get('students/chat/history/{id}', ['as' => 'students.chat.history', 'uses' => 'MembershipsController@getStudentChatHistory']);
    Route::get('teachers/messages', ['as' => 'teachers.messages', 'uses' => 'MembershipsController@indexTeachersMessages']);
    Route::get('teachers/chat/history/{id}', ['as' => 'teachers.chat.history', 'uses' => 'MembershipsController@getTeacherChatHistory']);
    Route::post('students/delete/messages', ['as' => 'students.messages.deleted', 'uses' => 'StudentsController@destroyStudentsMessages']);
    Route::post('teachers/delete/messages', ['as' => 'teachers.messages.deleted', 'uses' => 'TeacherController@destroyTeachersMessages']);
    Route::post('chat/delete/message', ['as' => 'chat.delete.message', 'uses' => 'MembershipsController@deleteMessage']);
    Route::post('chat/edit/message', ['as' => 'chat.edit.message', 'uses' => 'MembershipsController@editMessage']);
    //ask_update
    Route::get('ask_update', ['as' => 'ask_update.view', 'middleware' => ['permission:admin.ask_update.view'], 'uses' => 'StudentsController@getIndexAsk_update']);
    Route::get('ask_update/list', ['as' => 'ask_update.list', 'uses' => 'StudentsController@getAsk_updateList']);
    Route::post('ask_update/accept', ['as' => 'accept.ask_update', 'middleware' => ['permission:admin.ask_update.view'], 'uses' => 'StudentsController@postAcceptAskUpdate']);
    Route::post('ask_update/refuse', ['as' => 'refuse.ask_update', 'middleware' => ['permission:admin.ask_update.view'], 'uses' => 'StudentsController@postAcceptAskUpdate']);

    // Email Campaigns
    Route::get('email-campaigns', ['as' => 'admin.email_campaigns.index', 'uses' => 'EmailCampaignController@index']);
    Route::get('email-campaigns/list', ['as' => 'admin.email_campaigns.list', 'uses' => 'EmailCampaignController@getDatatable']);
    Route::get('email-campaigns/show/{id}', ['as' => 'admin.email_campaigns.show', 'uses' => 'EmailCampaignController@show']);
    Route::get('email-campaigns/status/{id}', ['as' => 'admin.email_campaigns.status', 'uses' => 'EmailCampaignController@status']);
    Route::post('email-campaigns/delete/{id}', ['as' => 'admin.email_campaigns.delete', 'uses' => 'EmailCampaignController@destroy']);

    // Route
    Route::get('dashboard', ['as' => 'dashboard.view', 'uses' => 'DashboardController@getIndex']);
    Route::get('profile', ['as' => 'dashboard.profile', 'uses' => 'DashboardController@getProfile']);
    Route::get('password', ['as' => 'dashboard.password', 'uses' => 'DashboardController@getPassword']);
    Route::post('password', ['as' => 'dashboard.password', 'uses' => 'DashboardController@postPassword']);

    // Calendar Routes
    Route::get('calendar', ['as' => 'calendar.view', 'uses' => 'CalendarController@index']);
    Route::get('calendar/events', ['as' => 'calendar.events', 'uses' => 'CalendarController@getEvents']);


    // //Users Route
    Route::get('users', ['as' => 'users.view', 'middleware' => ['permission:admin.users.view|admin.users.add|admin.users.edit|admin.users.delete|admin.users.status|admin.users.password'], 'uses' => 'UsersController@getIndex']);
    Route::get('users/list', ['as' => 'users.list', 'middleware' => ['permission:admin.users.view|admin.users.add|admin.users.edit|admin.users.delete|admin.users.status|admin.users.password'], 'uses' => 'UsersController@getList']);
    Route::get('users/add', ['as' => 'users.add', 'middleware' => ['permission:admin.users.add'], 'uses' => 'UsersController@getAdd']);
    Route::post('users/add', ['as' => 'users.add', 'middleware' => ['permission:admin.users.add'], 'uses' => 'UsersController@postAdd']);
    Route::get('users/edit/{id}', ['as' => 'users.edit', 'middleware' => ['permission:admin.users.edit'], 'uses' => 'UsersController@getEdit']);
    Route::post('users/edit/{id}', ['as' => 'users.edit', 'middleware' => ['permission:admin.users.edit'], 'uses' => 'UsersController@postEdit']);
    Route::get('users/password/{id}', ['as' => 'users.password', 'middleware' => ['permission:admin.users.password'], 'uses' => 'UsersController@getPassword']);
    Route::post('users/password/{id}', ['as' => 'users.password', 'middleware' => ['permission:admin.users.password'], 'uses' => 'UsersController@postPassword']);
    Route::post('users/delete', ['as' => 'users.delete', 'middleware' => ['permission:admin.users.delete'], 'uses' => 'UsersController@postDelete']);
    Route::post('users/status', ['as' => 'users.status', 'middleware' => ['permission:admin.users.status'], 'uses' => 'UsersController@postStatus']);
    //Evaluate_Items Route
    Route::get('evaluate_items', ['as' => 'evaluate_items.view', 'middleware' => ['permission:admin.evaluate_items.view|admin.evaluate_items.add|admin.evaluate_items.edit|admin.evaluate_items.delete|admin.evaluate_items.status'], 'uses' => 'Evaluate_ItemsController@getIndex']);
    Route::get('evaluate_items/list', ['as' => 'evaluate_items.list', 'middleware' => ['permission:admin.evaluate_items.view|admin.evaluate_items.add|admin.evaluate_items.edit|admin.evaluate_items.delete|admin.evaluate_items.status'], 'uses' => 'Evaluate_ItemsController@getList']);
    Route::get('evaluate_items/add', ['as' => 'evaluate_items.add', 'middleware' => ['permission:admin.evaluate_items.add'], 'uses' => 'Evaluate_ItemsController@getAdd']);
    Route::post('evaluate_items/add', ['as' => 'evaluate_items.add', 'middleware' => ['permission:admin.evaluate_items.add'], 'uses' => 'Evaluate_ItemsController@postAdd']);
    Route::get('evaluate_items/edit/{id}', ['as' => 'evaluate_items.edit', 'middleware' => ['permission:admin.evaluate_items.edit'], 'uses' => 'Evaluate_ItemsController@getEdit']);
    Route::post('evaluate_items/edit/{id}', ['as' => 'evaluate_items.edit', 'middleware' => ['permission:admin.evaluate_items.edit'], 'uses' => 'Evaluate_ItemsController@postEdit']);
    Route::get('evaluate_items/password/{id}', ['as' => 'evaluate_items.password', 'middleware' => ['permission:admin.evaluate_items.password'], 'uses' => 'Evaluate_ItemsController@getPassword']);
    Route::post('evaluate_items/password/{id}', ['as' => 'evaluate_items.password', 'middleware' => ['permission:admin.evaluate_items.password'], 'uses' => 'Evaluate_ItemsController@postPassword']);
    Route::post('evaluate_items/delete', ['as' => 'evaluate_items.delete', 'middleware' => ['permission:admin.evaluate_items.delete'], 'uses' => 'Evaluate_ItemsController@postDelete']);
    Route::post('evaluate_items/status', ['as' => 'evaluate_items.status', 'middleware' => ['permission:admin.evaluate_items.status'], 'uses' => 'Evaluate_ItemsController@postStatus']);

    //Questions Route
    Route::get('questions', ['as' => 'questions.view', 'middleware' => ['permission:admin.questions.view|admin.questions.add|admin.questions.edit|admin.questions.delete|admin.questions.status'], 'uses' => 'QuestionsController@getIndex']);
    Route::get('questions/list', ['as' => 'questions.list', 'middleware' => ['permission:admin.questions.view|admin.questions.add|admin.questions.edit|admin.questions.delete|admin.questions.status'], 'uses' => 'QuestionsController@getList']);
    Route::get('questions/add', ['as' => 'questions.add', 'middleware' => ['permission:admin.questions.add'], 'uses' => 'QuestionsController@getAdd']);
    Route::post('questions/add', ['as' => 'questions.add', 'middleware' => ['permission:admin.questions.add'], 'uses' => 'QuestionsController@postAdd']);
    Route::get('questions/edit/{id}', ['as' => 'questions.edit', 'middleware' => ['permission:admin.questions.edit'], 'uses' => 'QuestionsController@getEdit']);
    Route::post('questions/edit/{id}', ['as' => 'questions.edit', 'middleware' => ['permission:admin.questions.edit'], 'uses' => 'QuestionsController@postEdit']);
    Route::get('questions/password/{id}', ['as' => 'questions.password', 'middleware' => ['permission:admin.questions.password'], 'uses' => 'QuestionsController@getPassword']);
    Route::post('questions/password/{id}', ['as' => 'questions.password', 'middleware' => ['permission:admin.questions.password'], 'uses' => 'QuestionsController@postPassword']);
    Route::post('questions/delete', ['as' => 'questions.delete', 'middleware' => ['permission:admin.questions.delete'], 'uses' => 'QuestionsController@postDelete']);
    Route::post('questions/status', ['as' => 'questions.status', 'middleware' => ['permission:admin.questions.status'], 'uses' => 'QuestionsController@postStatus']);
    //Students_Report Route
    Route::get('students_report', ['as' => 'students_report.view', 'middleware' => ['permission:admin.students_report.view|admin.students_report.add|admin.students_report.edit|admin.students_report.delete|admin.students_report.status'], 'uses' => 'Students_ReportController@getIndex']);
    Route::get('students_report/list', ['as' => 'students_report.list', 'middleware' => ['permission:admin.students_report.view|admin.students_report.add|admin.students_report.edit|admin.students_report.delete|admin.students_report.status'], 'uses' => 'Students_ReportController@getList']);
    Route::post('students_report/showtopinfos', ['as' => 'students_report.showtopinfos', 'middleware' => ['permission:admin.students_report.view'], 'uses' => 'Students_ReportController@topStuudentInfos']);

    Route::get('roles', ['as' => 'roles.view', 'middleware' => ['permission:admin.roles.view|admin.roles.add|admin.roles.edit|admin.roles.delete|admin.roles.status|admin.roles.permissions'], 'uses' => 'RolesController@getIndex']);
    Route::get('roles/list', ['as' => 'roles.list', 'middleware' => ['permission:admin.roles.view|admin.roles.add|admin.roles.edit|admin.roles.delete|admin.roles.status|admin.roles.permissions'], 'uses' => 'RolesController@getList']);
    Route::get('roles/add', ['as' => 'roles.add', 'middleware' => ['permission:admin.roles.add'], 'uses' => 'RolesController@getAdd']);
    Route::post('roles/add', ['as' => 'roles.add', 'middleware' => ['permission:admin.roles.add'], 'uses' => 'RolesController@postAdd']);
    Route::get('roles/edit/{id}', ['as' => 'roles.edit', 'middleware' => ['permission:admin.roles.edit'], 'uses' => 'RolesController@getEdit']);
    Route::post('roles/edit/{id}', ['as' => 'roles.edit', 'middleware' => ['permission:admin.roles.edit'], 'uses' => 'RolesController@postEdit']);
    Route::post('roles/delete', ['as' => 'roles.delete', 'middleware' => ['permission:admin.roles.delete'], 'uses' => 'RolesController@postDelete']);
    Route::post('roles/status', ['as' => 'roles.status', 'middleware' => ['permission:admin.roles.status'], 'uses' => 'RolesController@postStatus']);
    Route::get('roles/permissions/{id}', ['as' => 'roles.permissions', 'middleware' => ['permission:admin.roles.permissions'], 'uses' => 'RolesController@getPermissions']);
    Route::post('roles/permissions/{id}', ['as' => 'roles.permissions', 'middleware' => ['permission:admin.roles.permissions'], 'uses' => 'RolesController@postPermissions']);

    Route::get('absent_teacher', ['as' => 'absent_teacher.view', 'middleware' => ['permission:admin.absent_teacher.view|admin.absent_teacher.add|admin.absent_teacher.edit|admin.absent_teacher.delete|admin.absent_teacher.status|admin.absent_teacher.permissions'], 'uses' => 'Absent_TeacherController@getIndex']);
    Route::get('absent_teacher/list', ['as' => 'absent_teacher.list', 'middleware' => ['permission:admin.absent_teacher.view|admin.absent_teacher.add|admin.absent_teacher.edit|admin.absent_teacher.delete|admin.absent_teacher.status|admin.absent_teacher.permissions'], 'uses' => 'Absent_TeacherController@getList']);
    Route::get('absent_teacher/add', ['as' => 'absent_teacher.add', 'middleware' => ['permission:admin.absent_teacher.add'], 'uses' => 'Absent_TeacherController@getAdd']);
    Route::post('absent_teacher/add', ['as' => 'absent_teacher.add', 'middleware' => ['permission:admin.absent_teacher.add'], 'uses' => 'Absent_TeacherController@postAdd']);
    Route::get('absent_teacher/edit/{id}', ['as' => 'absent_teacher.edit', 'middleware' => ['permission:admin.absent_teacher.edit'], 'uses' => 'Absent_TeacherController@getEdit']);
    Route::post('absent_teacher/edit/{id}', ['as' => 'absent_teacher.edit', 'middleware' => ['permission:admin.absent_teacher.edit'], 'uses' => 'Absent_TeacherController@postEdit']);
    Route::post('absent_teacher/delete', ['as' => 'absent_teacher.delete', 'middleware' => ['permission:admin.absent_teacher.delete'], 'uses' => 'Absent_TeacherController@postDelete']);
    Route::post('absent_teacher/status', ['as' => 'absent_teacher.status', 'middleware' => ['permission:admin.absent_teacher.status'], 'uses' => 'Absent_TeacherController@postStatus']);
    Route::get('absent_teacher/permissions/{id}', ['as' => 'absent_teacher.permissions', 'middleware' => ['permission:admin.absent_teacher.permissions'], 'uses' => 'Absent_TeacherController@getPermissions']);
    Route::post('absent_teacher/permissions/{id}', ['as' => 'absent_teacher.permissions', 'middleware' => ['permission:admin.absent_teacher.permissions'], 'uses' => 'Absent_TeacherController@postPermissions']);
    Route::get('absent_teacher/teacher-groups/{teacher_id}', ['as' => 'absent_teacher.groups', 'middleware' => ['permission:admin.absent_teacher.add'], 'uses' => 'Absent_TeacherController@showGroups']);

    //Static Page Route
    Route::get('pages', ['as' => 'pages.view', 'uses' => 'PagesController@getIndex']);
    Route::get('pages/list', ['as' => 'pages.list', 'uses' => 'PagesController@getList']);
    Route::get('pages/edit/{id}', ['as' => 'pages.edit', 'uses' => 'PagesController@getEdit']);
    Route::post('pages/edit/{id}', ['as' => 'pages.edit', 'uses' => 'PagesController@postEdit']);
    Route::post('pages/status', ['as' => 'pages.status', 'uses' => 'PagesController@postStatus']);
    //Contacts Route
    Route::get('contacts', ['as' => 'contacts.view', 'middleware' => ['permission:admin.contact.view|admin.contact.delete|admin.contact.status|admin.contact.reply'], 'uses' => 'ContactsController@getIndex']);
    Route::get('contacts/list', ['as' => 'contacts.list', 'middleware' => ['permission:admin.contact.view|admin.contact.delete|admin.contact.status|admin.contact.reply'], 'uses' => 'ContactsController@getList']);
    Route::get('contacts/reply/{id}', ['as' => 'contacts.reply', 'middleware' => ['permission:admin.contact.reply'], 'uses' => 'ContactsController@getReply']);
    Route::post('contacts/delete', ['as' => 'contacts.delete', 'middleware' => ['permission:admin.contact.delete'], 'uses' => 'ContactsController@postDelete']);
    Route::post('contacts/status', ['as' => 'contacts.status', 'middleware' => ['permission:admin.contact.status'], 'uses' => 'ContactsController@postStatus']);
    //Social Route
    Route::get('socials', ['as' => 'socials.view', 'middleware' => ['permission:admin.social.view'], 'uses' => 'SocialsController@getIndex']);
    Route::post('socials', ['as' => 'socials.list', 'middleware' => ['permission:admin.social.view'], 'uses' => 'SocialsController@postIndex']);

    //Settings Route
    Route::get('settings', ['as' => 'settings.view', 'middleware' => ['permission:admin.settings.view'], 'uses' => 'SettingsController@getIndex']);
    Route::post('settings', ['as' => 'settings.list', 'middleware' => ['permission:admin.settings.view'], 'uses' => 'SettingsController@postIndex']);

    //Categories Route
    Route::get('categories', ['as' => 'categories.view', 'middleware' => ['permission:admin.categories.view|admin.categories.add|admin.categories.edit|admin.categories.delete|admin.categories.status'], 'uses' => 'CategoriesController@getIndex']);
    Route::get('categories/list', ['as' => 'categories.list', 'middleware' => ['permission:admin.categories.view|admin.categories.add|admin.categories.edit|admin.categories.delete|admin.categories.status'], 'uses' => 'CategoriesController@getList']);
    Route::get('categories/add', ['as' => 'categories.add', 'middleware' => ['permission:admin.categories.add'], 'uses' => 'CategoriesController@getAdd']);
    Route::post('categories/add', ['as' => 'categories.add', 'middleware' => ['permission:admin.categories.add'], 'uses' => 'CategoriesController@postAdd']);
    Route::get('categories/edit/{id}', ['as' => 'categories.edit', 'middleware' => ['permission:admin.categories.edit'], 'uses' => 'CategoriesController@getEdit']);
    Route::post('categories/edit/{id}', ['as' => 'categories.edit', 'middleware' => ['permission:admin.categories.edit'], 'uses' => 'CategoriesController@postEdit']);
    Route::post('categories/delete', ['as' => 'categories.delete', 'middleware' => ['permission:admin.categories.delete'], 'uses' => 'CategoriesController@postDelete']);
    Route::post('categories/status', ['as' => 'categories.status', 'middleware' => ['permission:admin.categories.status'], 'uses' => 'CategoriesController@postStatus']);

    //News Route
    Route::get('news', ['as' => 'news.view', 'middleware' => ['permission:admin.news.view|admin.news.add|admin.news.edit|admin.news.delete|admin.news.status'], 'uses' => 'NewsController@getIndex']);
    Route::get('news/list', ['as' => 'news.list', 'middleware' => ['permission:admin.news.view|admin.news.add|admin.news.edit|admin.news.delete|admin.news.status'], 'uses' => 'NewsController@getList']);
    Route::get('news/add', ['as' => 'news.add', 'middleware' => ['permission:admin.news.add'], 'uses' => 'NewsController@getAdd']);
    Route::post('news/add', ['as' => 'news.add', 'middleware' => ['permission:admin.news.add'], 'uses' => 'NewsController@postAdd']);
    Route::get('news/edit/{id}', ['as' => 'news.edit', 'middleware' => ['permission:admin.news.edit'], 'uses' => 'NewsController@getEdit']);
    Route::post('news/edit/{id}', ['as' => 'news.edit', 'middleware' => ['permission:admin.news.edit'], 'uses' => 'NewsController@postEdit']);
    Route::post('news/delete', ['as' => 'news.delete', 'middleware' => ['permission:admin.news.delete'], 'uses' => 'NewsController@postDelete']);
    Route::post('news/publish', ['as' => 'news.publish', 'middleware' => ['permission:admin.news.publish'], 'uses' => 'NewsController@postPublish']);
    Route::get('news/cleaAllCache', ['as' => 'news.cleaAllCache', 'middleware' => ['permission:admin.news.publish'], 'uses' => 'NewsController@cleaAllCache']);
    Route::get('news/twitter', ['as' => 'news.twitter', 'middleware' => ['permission:admin.news.publish'], 'uses' => 'NewsController@getTwitter']);

    //Comments Route
    Route::get('photos', ['as' => 'photos.view', 'middleware' => ['permission:admin.photos.view|admin.photos.add|admin.photos.edit|admin.photos.delete|admin.photos.status'], 'uses' => 'PhotosController@getIndex']);
    Route::get('photos/list', ['as' => 'photos.list', 'middleware' => ['permission:admin.photos.view|admin.photos.add|admin.photos.edit|admin.photos.delete|admin.photos.status'], 'uses' => 'PhotosController@getList']);
    Route::get('photos/add', ['as' => 'photos.add', 'middleware' => ['permission:admin.photos.add'], 'uses' => 'PhotosController@getAdd']);
    Route::post('photos/add', ['as' => 'photos.add', 'middleware' => ['permission:admin.photos.add'], 'uses' => 'PhotosController@postAdd']);
    Route::get('photos/edit/{id}', ['as' => 'photos.edit', 'middleware' => ['permission:admin.photos.edit'], 'uses' => 'PhotosController@getEdit']);
    Route::get('photos/images/{id}', ['as' => 'photos.images', 'middleware' => ['permission:admin.photos.edit'], 'uses' => 'PhotosController@getImages']);
    Route::post('photos/edit/{id}', ['as' => 'photos.edit', 'middleware' => ['permission:admin.photos.edit'], 'uses' => 'PhotosController@postEdit']);
    Route::post('photos/delete', ['as' => 'photos.delete', 'middleware' => ['permission:admin.photos.delete'], 'uses' => 'PhotosController@postDelete']);
    Route::post('photos/status', ['as' => 'photos.status', 'middleware' => ['permission:admin.photos.status'], 'uses' => 'PhotosController@postStatus']);
    Route::post('photos/feature', ['as' => 'photos.feature', 'middleware' => ['permission:admin.photos.status'], 'uses' => 'PhotosController@postFeature']);
    Route::post('ajax-image-upload', 'PhotosController@ajaxImage');
    Route::post('ajax-feature-image', 'PhotosController@featureImage');
    Route::delete('ajax-remove-image', 'PhotosController@deleteImage');
    //videos Route
    Route::get('videos', ['as' => 'videos.view', 'middleware' => ['permission:admin.videos.view|admin.videos.add|admin.videos.edit|admin.videos.delete|admin.videos.status'], 'uses' => 'VideosController@getIndex']);
    Route::get('videos/list', ['as' => 'videos.list', 'middleware' => ['permission:admin.videos.view|admin.videos.add|admin.videos.edit|admin.videos.delete|admin.videos.status'], 'uses' => 'VideosController@getList']);
    Route::get('videos/add', ['as' => 'videos.add', 'middleware' => ['permission:admin.videos.add'], 'uses' => 'VideosController@getAdd']);
    Route::post('videos/add', ['as' => 'videos.add', 'middleware' => ['permission:admin.videos.add'], 'uses' => 'VideosController@postAdd']);
    Route::get('videos/edit/{id}', ['as' => 'videos.edit', 'middleware' => ['permission:admin.videos.edit'], 'uses' => 'VideosController@getEdit']);
    Route::post('videos/edit/{id}', ['as' => 'videos.edit', 'middleware' => ['permission:admin.videos.edit'], 'uses' => 'VideosController@postEdit']);
    Route::post('videos/delete', ['as' => 'videos.delete', 'middleware' => ['permission:admin.videos.delete'], 'uses' => 'VideosController@postDelete']);
    Route::post('videos/status', ['as' => 'videos.status', 'middleware' => ['permission:admin.videos.status'], 'uses' => 'VideosController@postStatus']);

    //partners Route
    Route::get('partners', ['as' => 'partners.view', 'middleware' => ['permission:admin.partners.view|admin.partners.add|admin.partners.edit|admin.partners.delete|admin.partners.status'], 'uses' => 'PartnersController@getIndex']);
    Route::get('partners/list', ['as' => 'partners.list', 'middleware' => ['permission:admin.partners.view|admin.partners.add|admin.partners.edit|admin.partners.delete|admin.partners.status'], 'uses' => 'PartnersController@getList']);
    Route::get('partners/add', ['as' => 'partners.add', 'middleware' => ['permission:admin.partners.add'], 'uses' => 'PartnersController@getAdd']);
    Route::post('partners/add', ['as' => 'partners.add', 'middleware' => ['permission:admin.partners.add'], 'uses' => 'PartnersController@postAdd']);
    Route::get('partners/edit/{id}', ['as' => 'partners.edit', 'middleware' => ['permission:admin.partners.edit'], 'uses' => 'PartnersController@getEdit']);
    Route::post('partners/edit/{id}', ['as' => 'partners.edit', 'middleware' => ['permission:admin.partners.edit'], 'uses' => 'PartnersController@postEdit']);
    Route::post('partners/delete', ['as' => 'partners.delete', 'middleware' => ['permission:admin.partners.delete'], 'uses' => 'PartnersController@postDelete']);
    Route::post('partners/status', ['as' => 'partners.status', 'middleware' => ['permission:admin.partners.status'], 'uses' => 'PartnersController@postStatus']);

    //services Route
    Route::get('programs', ['as' => 'programs.view', 'middleware' => ['permission:admin.programs.view|admin.programs.add|admin.programs.edit|admin.programs.delete|admin.programs.status'], 'uses' => 'ProgramsController@getIndex']);
    Route::get('programs/list', ['as' => 'programs.list', 'middleware' => ['permission:admin.programs.view|admin.programs.add|admin.programs.edit|admin.programs.delete|admin.programs.status'], 'uses' => 'ProgramsController@getList']);
    Route::get('programs/add', ['as' => 'programs.add', 'middleware' => ['permission:admin.programs.add'], 'uses' => 'ProgramsController@getAdd']);
    Route::post('programs/add', ['as' => 'programs.add', 'middleware' => ['permission:admin.programs.add'], 'uses' => 'ProgramsController@postAdd']);
    Route::get('programs/edit/{id}', ['as' => 'programs.edit', 'middleware' => ['permission:admin.programs.edit'], 'uses' => 'ProgramsController@getEdit']);
    Route::post('programs/edit/{id}', ['as' => 'programs.edit', 'middleware' => ['permission:admin.programs.edit'], 'uses' => 'ProgramsController@postEdit']);
    Route::post('programs/delete', ['as' => 'programs.delete', 'middleware' => ['permission:admin.programs.delete'], 'uses' => 'ProgramsController@postDelete']);
    Route::get('programs/details/{id}', ['as' => 'programs.details', 'middleware' => ['permission:admin.programs.view'], 'uses' => 'ProgramsController@getProgramGroupsDetails']);
    Route::post('programs/status', ['as' => 'programs.status', 'middleware' => ['permission:admin.programs.status'], 'uses' => 'ProgramsController@postStatus']);

    //groups Route
    Route::get('groups', ['as' => 'groups.view', 'middleware' => ['permission:admin.groups.view|admin.groups.add|admin.groups.edit|admin.groups.delete|admin.groups.status'], 'uses' => 'GroupsController@getIndex']);
    Route::get('groups/teacher/students/{id}', ['as' => 'groups.teacher.students', 'middleware' => ['permission:admin.groups.view|admin.groups.add|admin.groups.edit|admin.groups.delete|admin.groups.status'], 'uses' => 'GroupsController@getTeacherStudents']);
    Route::get('groups/list', ['as' => 'groups.list', 'middleware' => ['permission:admin.groups.view|admin.groups.add|admin.groups.edit|admin.groups.delete|admin.groups.status'], 'uses' => 'GroupsController@getList']);
    Route::get('groups/add', ['as' => 'groups.add', 'middleware' => ['permission:admin.groups.add'], 'uses' => 'GroupsController@getAdd']);
    Route::post('groups/add', ['as' => 'groups.add', 'middleware' => ['permission:admin.groups.add'], 'uses' => 'GroupsController@postAdd']);
    Route::get('groups/edit/{id}', ['as' => 'groups.edit', 'middleware' => ['permission:admin.groups.edit'], 'uses' => 'GroupsController@getEdit']);
    Route::post('groups/edit/{id}', ['as' => 'groups.edit', 'middleware' => ['permission:admin.groups.edit'], 'uses' => 'GroupsController@postEdit']);
    Route::post('groups/delete', ['as' => 'groups.delete', 'middleware' => ['permission:admin.groups.delete'], 'uses' => 'GroupsController@postDelete']);
    Route::post('groups/status', ['as' => 'groups.status', 'middleware' => ['permission:admin.groups.status'], 'uses' => 'GroupsController@postStatus']);
    Route::post('groups/details', ['as' => 'groups.details', 'uses' => 'GroupsController@getGroupDetails']);
    Route::post('teachers/details', ['as' => 'teachers.details', 'uses' => 'GroupsController@getTeacherDetails']);
    Route::post('programs/details', ['as' => 'programs.details.post', 'uses' => 'GroupsController@getProgramDetails']);
    Route::post('groups/send-email', ['as' => 'groups.send.CEmail', 'middleware' => ['permission:admin.groups.view'], 'uses' => 'GroupsController@sendBulkEmail']);

    Route::get('groups/student/{id}', ['as' => 'groups.student.view', 'middleware' => ['permission:admin.groups.student.view'], 'uses' => 'GroupsController@getStudentIndex']);
    Route::get('groups/student/{id}/list', ['as' => 'groups.student.list', 'middleware' => ['permission:admin.groups.student.view|admin.groups.student.add|admin.groups.student.edit|admin.groups.student.delete|admin.groups.student.status'], 'uses' => 'GroupsController@getStudentList']);
    Route::get('groups/student/{id}/add', ['as' => 'groups.student.add', 'middleware' => ['permission:admin.groups.student.add'], 'uses' => 'GroupsController@getAddStudent']);
    Route::post('groups/student/{id}/add', ['as' => 'groups.student.add', 'middleware' => ['permission:admin.groups.student.add'], 'uses' => 'GroupsController@postAddStudent']);
    Route::get('groups/student', ['as' => 'groups.student.search', 'middleware' => ['permission:admin.groups.student.add'], 'uses' => 'GroupsController@getAjaxStudentName']);
    Route::post('groups/student/{id}/delete', ['as' => 'groups.student.delete', 'middleware' => ['permission:admin.groups.student.delete'], 'uses' => 'GroupsController@getStudentDelete']);
    Route::post('groups/student/delete', ['as' => 'groups.student.deleted', 'middleware' => ['permission:admin.groups.student.delete'], 'uses' => 'GroupsController@getStudentDeleted']);
    Route::post('groups/search', ['as' => 'groups.ajax.search', 'middleware' => ['permission:admin.groups.student.view'], 'uses' => 'GroupsController@getAjaxStudentGroups']);
    Route::get('groups/subjects/{id}', ['as' => 'groups.subjects.add', 'middleware' => ['permission:admin.groups.student.view'], 'uses' => 'GroupsController@getSubjectsIndex']);
    Route::get('groups/studentdegree/{id}', ['as' => 'groups.student.degree', 'middleware' => ['permission:admin.groups.student.view'], 'uses' => 'GroupsController@getStudentDegree']);
    Route::post('groups/admin/studentdegree/{id}', ['as' => 'groups.post.degree', 'middleware' => ['permission:admin.groups.student.view'], 'uses' => 'GroupsController@postStudentDegree']);
    Route::get('groups/student/listdegree/{id}', ['as' => 'groups.student.listdegree', 'middleware' => ['permission:admin.groups.student.view'], 'uses' => 'GroupsController@getStudentListDegree']);
    Route::get('groups/students/status', ['as' => 'groups.students.status', 'middleware' => ['permission:admin.students.status'], 'uses' => 'GroupsController@postStudentStatus']);
    Route::post('groups/generate-certificate-code/{id}', ['as' => 'groups.certificate.generate',  'uses' => 'GroupsController@postCertificateStudent']); //fffffff
    Route::post('groups/students/delay', ['as' => 'groups.students.delay', 'middleware' => ['permission:admin.students.status'], 'uses' => 'GroupsController@postStudentdelay']);
    Route::get('groups/studentevaluation/{id}', ['as' => 'groups.student.evaluation', 'middleware' => ['permission:admin.groups.student.view'], 'uses' => 'GroupsController@getStudentEvaluation']);
    Route::get('groups/student/{id}/listdegree', ['as' => 'groups.student.listevaluation', 'middleware' => ['permission:admin.groups.student.view'], 'uses' => 'GroupsController@getStudentListEvaluation']);
    Route::get('view/evaluation/{id}/{group_id}/{student_id}', ['as' => 'view.student.evaluation', 'middleware' => ['permission:admin.groups.student.view'], 'uses' => 'GroupsController@showStudentEvaluation']);
    Route::get('view/attendance/{teacher_id}/{group_id}', ['as' => 'groups.student.attendance',  'uses' => 'GroupsController@showStudentAttendance']);
    Route::post('list/attendance/{teacher_id}/{group_id}', ['as' => 'postt.student.attendance', 'uses' => 'GroupsController@listStudentAttendance']);

    //students Route
    Route::get('students', ['as' => 'students.view', 'middleware' => ['permission:admin.students.view|admin.students.add|admin.students.edit|admin.students.delete|admin.students.status'], 'uses' => 'StudentsController@getIndex']);
    Route::get('students/list', ['as' => 'students.list', 'middleware' => ['permission:admin.students.view|admin.students.add|admin.students.edit|admin.students.delete|admin.students.status'], 'uses' => 'StudentsController@getList']);
    Route::get('students/delay', ['as' => 'students.delay.view', 'middleware' => ['permission:admin.students.view|admin.students.add|admin.students.edit|admin.students.delete|admin.students.status'], 'uses' => 'StudentsController@getDelayIndex']);
    Route::get('students/list/delay', ['as' => 'students.delay.list', 'middleware' => ['permission:admin.students.view|admin.students.add|admin.students.edit|admin.students.delete|admin.students.status'], 'uses' => 'StudentsController@getDelayList']);
    Route::get('students/add', ['as' => 'students.add', 'middleware' => ['permission:admin.students.add'], 'uses' => 'StudentsController@getAdd']);
    Route::post('students/add', ['as' => 'students.add', 'middleware' => ['permission:admin.students.add'], 'uses' => 'StudentsController@postAdd']);
    Route::get('students/edit/{id}', ['as' => 'students.edit', 'middleware' => ['permission:admin.students.edit'], 'uses' => 'StudentsController@getEdit']);
    Route::post('students/edit/{id}', ['as' => 'students.edit', 'middleware' => ['permission:admin.students.edit'], 'uses' => 'StudentsController@postEdit']);
    Route::post('students/delete', ['as' => 'students.delete', 'middleware' => ['permission:admin.students.delete'], 'uses' => 'StudentsController@postDelete']);
    Route::post('students/status', ['as' => 'students.status', 'middleware' => ['permission:admin.students.status'], 'uses' => 'StudentsController@postStatus']);
    Route::post('students/details', ['as' => 'students.details', 'middleware' => ['permission:admin.students.view'], 'uses' => 'StudentsController@getStudentDetails']);

    Route::get('students/password/{id}', ['as' => 'students.password', 'middleware' => ['permission:admin.students.view'], 'uses' => 'StudentsController@getPassword']);
    Route::post('students/password/{id}', ['as' => 'students.password', 'middleware' => ['permission:admin.students.view'], 'uses' => 'StudentsController@postPassword']);

    //times Route
    Route::get('times', ['as' => 'times.view', 'middleware' => ['permission:admin.times.view|admin.times.add|admin.times.edit|admin.times.delete|admin.times.status'], 'uses' => 'TimesController@getIndex']);
    Route::get('times/list', ['as' => 'times.list', 'middleware' => ['permission:admin.times.view|admin.times.add|admin.times.edit|admin.times.delete|admin.times.status'], 'uses' => 'TimesController@getList']);
    Route::get('times/add', ['as' => 'times.add', 'middleware' => ['permission:admin.times.add'], 'uses' => 'TimesController@getAdd']);
    Route::post('times/add', ['as' => 'times.add', 'middleware' => ['permission:admin.times.add'], 'uses' => 'TimesController@postAdd']);
    Route::get('times/edit/{id}', ['as' => 'times.edit', 'middleware' => ['permission:admin.times.edit'], 'uses' => 'TimesController@getEdit']);
    Route::post('times/edit/{id}', ['as' => 'times.edit', 'middleware' => ['permission:admin.times.edit'], 'uses' => 'TimesController@postEdit']);
    Route::post('times/delete', ['as' => 'times.delete', 'middleware' => ['permission:admin.times.delete'], 'uses' => 'TimesController@postDelete']);
    Route::post('times/status', ['as' => 'times.status', 'middleware' => ['permission:admin.times.status'], 'uses' => 'TimesController@postStatus']);

    //certificates Route
    Route::get('certificates', ['as' => 'certificates.view', 'middleware' => ['permission:admin.certificates.view'], 'uses' => 'CertificatesController@getIndex']);
    Route::get('certificates/list', ['as' => 'certificates.list', 'middleware' => ['permission:admin.certificates.view|admin.certificates.add|admin.certificates.edit|admin.certificates.delete|admin.certificates.status'], 'uses' => 'CertificatesController@getList']);
    Route::get('certificates/add', ['as' => 'certificates.add', 'middleware' => ['permission:admin.certificates.add'], 'uses' => 'CertificatesController@getAdd']);
    Route::post('certificates/add', ['as' => 'certificates.add', 'middleware' => ['permission:admin.certificates.add'], 'uses' => 'CertificatesController@postAdd']);
    Route::get('certificates/edit/{id}', ['as' => 'certificates.edit', 'middleware' => ['permission:admin.certificates.edit'], 'uses' => 'CertificatesController@getEdit']);
    Route::post('certificates/edit/{id}', ['as' => 'certificates.edit', 'middleware' => ['permission:admin.certificates.edit'], 'uses' => 'CertificatesController@postEdit']);
    Route::post('certificates/delete', ['as' => 'certificates.delete', 'middleware' => ['permission:admin.certificates.delete'], 'uses' => 'CertificatesController@postDelete']);
    Route::post('certificates/status', ['as' => 'certificates.status', 'middleware' => ['permission:admin.certificates.status'], 'uses' => 'CertificatesController@postStatus']);

    //times Route
    Route::get('closed_classes', ['as' => 'closed_classes.view', 'middleware' => ['permission:admin.closed_classes.view|admin.closed_classes.add|admin.closed_classes.edit|admin.closed_classes.delete|admin.closed_classes.status'], 'uses' => 'Closed_ClassesController@getIndex']);
    Route::get('closed_classes/list', ['as' => 'closed_classes.list', 'middleware' => ['permission:admin.closed_classes.view|admin.closed_classes.add|admin.closed_classes.edit|admin.closed_classes.delete|admin.closed_classes.status'], 'uses' => 'Closed_ClassesController@getList']);
    Route::post('closed_classes/delete', ['as' => 'closed_classes.delete', 'middleware' => ['permission:admin.closed_classes.delete'], 'uses' => 'Closed_ClassesController@postDelete']);

    //fees Route
    Route::get('fees', ['as' => 'fees.view', 'middleware' => ['permission:admin.fees.view|admin.fees.add|admin.fees.edit|admin.fees.delete|admin.fees.status'], 'uses' => 'FeesController@getIndex']);
    Route::get('fees/list', ['as' => 'fees.list', 'middleware' => ['permission:admin.fees.view|admin.fees.add|admin.fees.edit|admin.fees.delete|admin.fees.status'], 'uses' => 'FeesController@getList']);
    Route::get('fees/add', ['as' => 'fees.add', 'middleware' => ['permission:admin.fees.add'], 'uses' => 'FeesController@getAdd']);
    Route::post('fees/add', ['as' => 'fees.add', 'middleware' => ['permission:admin.fees.add'], 'uses' => 'FeesController@postAdd']);
    Route::get('fees/edit/{id}', ['as' => 'fees.edit', 'middleware' => ['permission:admin.fees.edit'], 'uses' => 'FeesController@getEdit']);
    Route::post('fees/edit/{id}', ['as' => 'fees.edit', 'middleware' => ['permission:admin.fees.edit'], 'uses' => 'FeesController@postEdit']);
    Route::post('fees/delete', ['as' => 'fees.delete', 'middleware' => ['permission:admin.fees.delete'], 'uses' => 'FeesController@postDelete']);
    Route::post('fees/status', ['as' => 'fees.status', 'middleware' => ['permission:admin.fees.status'], 'uses' => 'FeesController@postStatus']);

    /*
    //evaluations Route
    Route::get('evaluations', ['as' => 'evaluations.view', 'middleware' => ['permission:admin.evaluations.view|admin.evaluations.add|admin.evaluations.edit|admin.evaluations.delete|admin.evaluations.status'], 'uses' => 'SupplierController@getIndex']);
    Route::get('evaluations/list', ['as' => 'evaluations.list', 'middleware' => ['permission:admin.evaluations.view|admin.evaluations.add|admin.evaluations.edit|admin.evaluations.delete|admin.evaluations.status'], 'uses' => 'SupplierController@getList']);
    Route::get('evaluations/add', ['as' => 'evaluations.add', 'middleware' => ['permission:admin.evaluations.add'], 'uses' => 'SupplierController@getAdd']);
    Route::post('evaluations/add', ['as' => 'evaluations.add', 'middleware' => ['permission:admin.evaluations.add'], 'uses' => 'SupplierController@postAdd']);
    Route::get('evaluations/edit/{id}', ['as' => 'evaluations.edit', 'middleware' => ['permission:admin.evaluations.edit'], 'uses' => 'SupplierController@getEdit']);
    Route::post('evaluations/edit/{id}', ['as' => 'evaluations.edit', 'middleware' => ['permission:admin.evaluations.edit'], 'uses' => 'SupplierController@postEdit']);
    Route::post('evaluations/delete', ['as' => 'evaluations.delete', 'middleware' => ['permission:admin.evaluations.delete'], 'uses' => 'SupplierController@postDelete']);
    Route::post('evaluations/status', ['as' => 'evaluations.status', 'middleware' => ['permission:admin.evaluations.status'], 'uses' => 'SupplierController@postStatus']);
    */

    //teachers Route
    Route::get('teachers', ['as' => 'teachers.view', 'middleware' => ['permission:admin.teachers.view|admin.teachers.add|admin.teachers.edit|admin.teachers.delete|admin.teachers.status'], 'uses' => 'TeacherController@getIndex']);
    Route::get('teachers/list', ['as' => 'teachers.list', 'middleware' => ['permission:admin.teachers.view|admin.teachers.add|admin.teachers.edit|admin.teachers.delete|admin.teachers.status'], 'uses' => 'TeacherController@getList']);
    Route::get('teachers/add', ['as' => 'teachers.add', 'middleware' => ['permission:admin.teachers.add'], 'uses' => 'TeacherController@getAdd']);
    Route::post('teachers/add', ['as' => 'teachers.add', 'middleware' => ['permission:admin.teachers.add'], 'uses' => 'TeacherController@postAdd']);
    Route::get('teachers/edit/{id}', ['as' => 'teachers.edit', 'middleware' => ['permission:admin.teachers.edit'], 'uses' => 'TeacherController@getEdit']);
    Route::post('teachers/edit/{id}', ['as' => 'teachers.edit', 'middleware' => ['permission:admin.teachers.edit'], 'uses' => 'TeacherController@postEdit']);
    Route::post('teachers/delete', ['as' => 'teachers.delete', 'middleware' => ['permission:admin.teachers.delete'], 'uses' => 'TeacherController@postDelete']);
    Route::post('teachers/status', ['as' => 'teachers.status', 'middleware' => ['permission:admin.teachers.status'], 'uses' => 'TeacherController@postStatus']);
    Route::post('teachers/evaluations', ['as' => 'teachers.evaluations', 'middleware' => ['permission:admin.teachers.status'], 'uses' => 'TeacherController@postEvaluations']);
    Route::get('teachers/password/{id}', ['as' => 'teachers.password', 'middleware' => ['permission:admin.teachers.view'], 'uses' => 'TeacherController@getPassword']);
    Route::post('teachers/password/{id}', ['as' => 'teachers.password', 'middleware' => ['permission:admin.teachers.view'], 'uses' => 'TeacherController@postPassword']);

    //Files Route
    Route::get('files', ['as' => 'files.view', 'middleware' => ['permission:admin.files.view|admin.files.add|admin.files.edit|admin.files.delete|admin.files.status'], 'uses' => 'FilesController@getIndex']);
    Route::get('files/list', ['as' => 'files.list', 'middleware' => ['permission:admin.files.view|admin.files.add|admin.files.edit|admin.files.delete|admin.files.status'], 'uses' => 'FilesController@getList']);
    Route::get('files/add', ['as' => 'files.add', 'middleware' => ['permission:admin.files.add'], 'uses' => 'FilesController@getAdd']);
    Route::post('files/add', ['as' => 'files.add', 'middleware' => ['permission:admin.files.add'], 'uses' => 'FilesController@postAdd']);
    Route::get('files/edit/{id}', ['as' => 'files.edit', 'middleware' => ['permission:admin.files.edit'], 'uses' => 'FilesController@getEdit']);
    Route::post('files/edit/{id}', ['as' => 'files.edit', 'middleware' => ['permission:admin.files.edit'], 'uses' => 'FilesController@postEdit']);
    Route::post('files/delete', ['as' => 'files.delete', 'middleware' => ['permission:admin.files.delete'], 'uses' => 'FilesController@postDelete']);
    Route::post('files/status', ['as' => 'files.status', 'middleware' => ['permission:admin.files.status'], 'uses' => 'FilesController@postStatus']);

    Route::get('logout', ['as' => 'app.logout', 'uses' => 'LoginController@getLogout']);

    // progress_menu Route
    Route::get('progress_menu', ['as' => 'progress_menu.view', 'middleware' => ['permission:admin.progress_menu.view'], 'uses' => 'Progress_MenuController@getIndex']);
    Route::get('progress_menu/list', ['as' => 'progress_menu.list', 'middleware' => ['permission:admin.progress_menu.view'], 'uses' => 'Progress_MenuController@getList']);
    Route::post('progress_menu/delete', ['as' => 'progress_menu.delete', 'middleware' => ['permission:admin.progress_menu.view'], 'uses' => 'Progress_MenuController@postDelete']);

    //parent menues
    // Route::get('pending_orders/', ['as' => 'pending_orders.view', 'uses' => 'DashboardController@getIndex']);

    // //permissions_group Route
    // require __DIR__ . '/admin/permissions_group.php';

    // //permissions Route
    // require __DIR__ . '/admin/permissions.php';

    // //roles Route
    // require __DIR__ . '/admin/roles.php';

    // //users Route
    // require __DIR__ . '/admin/users.php';

    // membership Route
    // require __DIR__ . '/admin/membership.php';

    // partners Route
    // require __DIR__ . '/admin/partners.php';

    // Parents Route
    Route::get('parents', ['as' => 'parents.view', 'uses' => 'ParentsController@getIndex']);
    Route::get('parents/list', ['as' => 'parents.list', 'uses' => 'ParentsController@getList']);
    Route::get('parents/children', ['as' => 'parents.children', 'uses' => 'ParentsController@getChildren']);
    Route::post('parents/delete', ['as' => 'parents.delete', 'uses' => 'ParentsController@postDelete']);

    // Payment Methods Route
    Route::get('payment_methods', ['as' => 'payment_methods.view', 'uses' => 'PaymentMethodsController@getIndex']);
    Route::get('payment_methods/list', ['as' => 'payment_methods.list', 'uses' => 'PaymentMethodsController@getList']);
    Route::get('payment_methods/add', ['as' => 'payment_methods.add', 'uses' => 'PaymentMethodsController@getAdd']);
    Route::post('payment_methods/add', ['as' => 'payment_methods.add', 'uses' => 'PaymentMethodsController@postAdd']);
    Route::get('payment_methods/edit/{id}', ['as' => 'payment_methods.edit', 'uses' => 'PaymentMethodsController@getEdit']);
    Route::post('payment_methods/edit/{id}', ['as' => 'payment_methods.edit', 'uses' => 'PaymentMethodsController@postEdit']);
    Route::post('payment_methods/delete', ['as' => 'payment_methods.delete', 'uses' => 'PaymentMethodsController@postDelete']);
    Route::post('payment_methods/status', ['as' => 'payment_methods.status', 'uses' => 'PaymentMethodsController@postStatus']);

    // Placement Tests Route
    Route::get('placement_tests', ['as' => 'placement_tests.view', 'uses' => 'PlacementTestsController@getIndex']);
    Route::get('placement_tests/list', ['as' => 'placement_tests.list', 'uses' => 'PlacementTestsController@getList']);
    Route::get('placement_tests/edit/{id}', ['as' => 'placement_tests.edit', 'uses' => 'PlacementTestsController@getEdit']);
    Route::post('placement_tests/edit/{id}', ['as' => 'placement_tests.edit', 'uses' => 'PlacementTestsController@postEdit']);
    Route::post('placement_tests/delete', ['as' => 'placement_tests.delete', 'uses' => 'PlacementTestsController@postDelete']);
    Route::post('placement_tests/status', ['as' => 'placement_tests.status', 'uses' => 'PlacementTestsController@postStatus']);
    Route::post('placement_tests/confirm-payment/{id}', ['as' => 'placement_tests.confirm_payment', 'uses' => 'PlacementTestsController@confirmPayment']);
    Route::post('placement_tests/score/{id}', ['as' => 'placement_tests.score', 'uses' => 'PlacementTestsController@postScore']);
    Route::post('placement_tests/send-email', ['as' => 'placement_tests.send_email', 'uses' => 'PlacementTestsController@sendBatchEmail']);

    // File Manager
    Route::get('file-manager', ['as' => 'admin.file_manager', 'uses' => 'FileManagerController@index']);
    Route::get('file_manager', ['uses' => 'FileManagerController@index']); // Fallback underscore
    Route::post('file-manager/upload', ['as' => 'admin.file_manager.upload', 'uses' => 'FileManagerController@upload']);
    Route::post('file_manager/upload', ['uses' => 'FileManagerController@upload']); // Fallback underscore
    Route::post('file-manager/create-folder', ['as' => 'admin.file_manager.create_folder', 'uses' => 'FileManagerController@createFolder']);
    Route::post('file_manager/create-folder', ['uses' => 'FileManagerController@createFolder']); // Fallback underscore
    Route::post('file-manager/rename', ['as' => 'admin.file_manager.rename', 'uses' => 'FileManagerController@rename']);
    Route::post('file_manager/rename', ['uses' => 'FileManagerController@rename']); // Fallback underscore
    Route::post('file-manager/delete', ['as' => 'admin.file_manager.delete', 'uses' => 'FileManagerController@delete']);
    Route::post('file_manager/delete', ['uses' => 'FileManagerController@delete']); // Fallback underscore
});
