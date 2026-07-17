<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class SmsController extends Controller
{
    /**
     * Check SMS balance.
     */
    public function checkSmsBalance(\App\Services\SmsService $smsService)
    {
        $result = $smsService->checkBalance();
        return response()->json($result);
    }

    /**
     * Handle SMS sending for admin dashboard.
     */
    public function sendSms(Request $request, \App\Services\SmsService $smsService)
    {
        $selectedMobiles = $request->input('selectedMobiles', []);
        $studentIds = $request->input('studentIds', []);
        $customNumber = $request->input('customNumber');
        $message = $request->input('note') ?? $request->input('message') ?? '';
        $source = $request->input('source', 'student');

        $successCount = 0;
        $errorCount = 0;
        $lastError = '';
        $adminId = auth()->check() ? auth()->id() : (auth()->guard('admin')->check() ? auth()->guard('admin')->id() : null);

        // Helper function to normalize numbers
        $normalizeMobile = function($mob) {
            if (!$mob) return '';
            $mob = preg_replace('/[^\d\+]/', '', $mob);
            if (str_starts_with($mob, '00972') || str_starts_with($mob, '00970')) {
                $mob = substr($mob, 2);
            } elseif (str_starts_with($mob, '+972') || str_starts_with($mob, '+970')) {
                $mob = substr($mob, 1);
            }
            return $mob;
        };

        // If student IDs provided, resolve each student and perform template replacements
        if (!empty($studentIds) && is_array($studentIds)) {
            \Log::info('SmsController: Processing studentIds: ', $studentIds);
            foreach ($studentIds as $sid) {
                \Log::info('SmsController: Processing SID: ' . $sid);
                if ($source === 'compo') {
                    $student = \App\Models\StudentCompo::with('parents')->find($sid);
                    if (! $student) {
                        \Log::warning('SmsController: StudentCompo not found for SID: ' . $sid);
                        continue;
                    }
                    $mobile = $normalizeMobile($student->phone ?? null);
                    $nameAr = $student->full_name_ar ?? '';
                    $nameEn = $student->full_name_en ?? '';
                } else {
                    $student = \App\Models\Students::find($sid);
                    if (! $student) {
                        \Log::warning('SmsController: Student not found for SID: ' . $sid);
                        continue;
                    }
                    $mobile = $normalizeMobile($student->mobile ?? null);
                    $nameAr = $student->name ?? '';
                    $nameEn = $student->name_en ?? '';
                }

                if (! $mobile) {
                    \App\Models\SmsArchive::create([
                        'student_id' => $student->id,
                        'receiver_name' => $nameAr,
                        'mobile' => 'لا يوجد رقم',
                        'message' => $message,
                        'status' => 'failed',
                        'error_message' => 'رقم الجوال فارغ',
                        'sender_id' => $adminId
                    ]);
                    $errorCount++;
                    continue;
                }

                $template = (string) $message;
                $groupName = '';
                $programName = '';
                $groupId = null;
                $programId = null;
                $score = '-';
                $assignedLevel = '-';
                $email = '';
                $parentName = '';

                if ($source === 'compo') {
                    $programId = $student->program_id;
                    if ($programId) {
                        $prog = \App\Models\Programs::find($programId);
                        if ($prog) $programName = $prog->title ?? '';
                    }
                    $email = $student->email ?? '';
                    $parent = $student->parents->first();
                    if ($parent) $parentName = $parent->parent_name ?? '';
                } else {
                    $gs = \App\Models\GroupStudents::where('student_id', $student->id)->first();
                    if ($gs) {
                        $groupId = $gs->group_id;
                        $group = \App\Models\Groups::find($gs->group_id);
                        if ($group) {
                            $groupName = $group->name ?? '';
                            if (isset($group->program_id)) {
                                $programId = $group->program_id;
                                $prog = \App\Models\Programs::find($group->program_id);
                                if ($prog) $programName = $prog->title ?? '';
                            }
                        }
                    }

                    // Fetch latest placement test
                    $test = $student->placementTests()->latest()->first();
                    $score = $test && $test->score !== null ? $test->score : '-';
                    $assignedLevel = $test && $test->assigned_level !== null ? $test->assigned_level : '-';
                }

                $replacements = [
                    '$name' => $nameAr,
                    '$name_ar' => $nameAr,
                    '$name_en' => $nameEn,
                    '$group' => $groupName,
                    '$program' => $programName,
                    '$score' => $score,
                    '$assigned_level' => $assignedLevel,
                    '$email' => $email,
                    '$parent_name' => $parentName,
                ];

                $finalMessage = strtr($template, $replacements);
                $result = $smsService->send($mobile, $finalMessage);

                \App\Models\SmsArchive::create([
                    'student_id' => $student->id,
                    'receiver_name' => $nameAr,
                    'mobile' => $mobile,
                    'message' => $finalMessage,
                    'status' => $result['success'] ? 'success' : 'failed',
                    'error_message' => $result['success'] ? null : $result['message'],
                    'sender_id' => $adminId,
                    'group_id' => $groupId,
                    'program_id' => $programId
                ]);

                if ($result['success']) {
                    $successCount++;
                } else {
                    $errorCount++;
                    $lastError = $result['message'];
                }
            }

            if ($successCount > 0 || $errorCount == 0) { // return success if any success or no errors (e.g. empty array but should not happen)
                return response()->json(['success' => true, 'successCount' => $successCount, 'errorCount' => $errorCount]);
            } else {
                return response()->json(['status' => 'error', 'message' => 'حدث مشكلة اثناء عملية الارسال: ' . $lastError], 400);
            }
        }

        // If a custom number provided, send single message
        if (!empty($customNumber)) {
            $customNumber = $normalizeMobile($customNumber);
            $result = $smsService->send($customNumber, $message);
            
            \App\Models\SmsArchive::create([
                'receiver_name' => 'رقم مخصص',
                'mobile' => $customNumber,
                'message' => $message,
                'status' => $result['success'] ? 'success' : 'failed',
                'error_message' => $result['success'] ? null : $result['message'],
                'sender_id' => $adminId
            ]);
            if ($result['success']) {
                return response()->json(['success' => true]);
            } else {
                return response()->json(['status' => 'error', 'message' => 'حدث مشكلة اثناء عملية الارسال: ' . $result['message']], 400);
            }
        }

        // Fallback: existing behavior - send to provided mobiles
        foreach ($selectedMobiles as $numbers) {
            $result = $smsService->send($numbers, $message);
            if ($result['success']) {
                $successCount++;
            } else {
                $errorCount++;
                $lastError = $result['message'];
            }
        }

        if ($successCount > 0 || empty($selectedMobiles)) {
            return response()->json(['success' => true]);
        } else {
            return response()->json(['status' => 'error', 'message' => 'حدث مشكلة اثناء عملية الارسال: ' . $lastError], 400);
        }
    }
}
