<input type="hidden" name="category" value="{{ $category }}">

<div class="row g-9 mb-8">
    <div class="col-md-8 fv-row">
        <label class="fs-6 fw-semibold mb-2">عنوان الامتحان <span class="text-danger">*</span></label>
        <input type="text" value="{{ old('title', $info->title ?? '') }}" name="title" class="form-control form-control-solid" required>
    </div>
    <div class="col-md-4 fv-row">
        <label class="fs-6 fw-semibold mb-2">الحالة</label>
        <select name="status" class="form-select form-select-solid">
            <option value="draft" {{ old('status', $info->status ?? 'draft') == 'draft' ? 'selected' : '' }}>مسودة</option>
            <option value="scheduled" {{ old('status', $info->status ?? '') == 'scheduled' ? 'selected' : '' }}>مجدول</option>
            <option value="published" {{ old('status', $info->status ?? '') == 'published' ? 'selected' : '' }}>منشور</option>
            <option value="closed" {{ old('status', $info->status ?? '') == 'closed' ? 'selected' : '' }}>مغلق</option>
        </select>
    </div>
</div>

<div class="row g-9 mb-8">
    <div class="col-md-12 fv-row">
        <label class="fs-6 fw-semibold mb-2">الوصف / التعليمات</label>
        <textarea name="description" id="exam_description" class="form-control form-control-solid ckeditor" rows="2">{{ old('description', $info->description ?? '') }}</textarea>
    </div>
</div>

<div class="row g-9 mb-8">
    <div class="col-md-6 fv-row">
        <label class="fs-6 fw-semibold mb-2">البرنامج</label>
        <select name="program_id" id="exam_program_id" data-control="select2" class="form-select form-select-solid">
            <option value="">{{ $category === 'placement' ? 'كل البرامج' : 'اختر برنامج' }}</option>
            @foreach($programs as $program)
                <option value="{{ $program->id }}" {{ old('program_id', $info->program_id ?? '') == $program->id ? 'selected' : '' }}>{{ $program->title }}</option>
            @endforeach
        </select>
        @if($category === 'group')
            <div class="form-text">تظهر هنا فقط البرامج الفعالة التي لديها مجموعة واحدة على الأقل.</div>
        @endif
    </div>
    @if($category === 'group')
    <div class="col-md-6 fv-row">
        <label class="fs-6 fw-semibold mb-2">المجموعة <span class="text-danger">*</span></label>
        <select name="group_id" id="exam_group_id" data-control="select2" class="form-select form-select-solid" required>
            <option value="">اختر برنامجاً أولاً...</option>
            @foreach($groups as $group)
                <option value="{{ $group->id }}" {{ old('group_id', $info->group_id ?? '') == $group->id ? 'selected' : '' }}>{{ $group->name }}</option>
            @endforeach
        </select>
    </div>
    @endif
</div>

<div class="row g-9 mb-8">
    <div class="col-md-3 fv-row">
        <label class="fs-6 fw-semibold mb-2">المدة (دقيقة)</label>
        <input type="number" min="1" value="{{ old('duration_minutes', $info->duration_minutes ?? 30) }}" name="duration_minutes" class="form-control form-control-solid">
    </div>
    <div class="col-md-3 fv-row">
        <label class="fs-6 fw-semibold mb-2">أقصى عدد محاولات</label>
        <input type="number" min="1" value="{{ old('max_attempts', $info->max_attempts ?? 1) }}" name="max_attempts" class="form-control form-control-solid">
    </div>
    <div class="col-md-3 fv-row">
        <label class="fs-6 fw-semibold mb-2">درجة النجاح (%)</label>
        <input type="number" step="0.5" min="0" max="100" value="{{ old('passing_score', $info->passing_score ?? 50) }}" name="passing_score" class="form-control form-control-solid">
    </div>
    <div class="col-md-3 fv-row">
        <label class="fs-6 fw-semibold mb-2">ظهور النتيجة</label>
        <select name="result_visibility" class="form-select form-select-solid">
            <option value="immediate" {{ old('result_visibility', $info->result_visibility ?? 'immediate') == 'immediate' ? 'selected' : '' }}>فوري</option>
            <option value="after_review" {{ old('result_visibility', $info->result_visibility ?? '') == 'after_review' ? 'selected' : '' }}>بعد المراجعة</option>
            <option value="manual" {{ old('result_visibility', $info->result_visibility ?? '') == 'manual' ? 'selected' : '' }}>يدوي</option>
        </select>
    </div>
</div>

<div class="row g-9 mb-8">
    <div class="col-md-6 fv-row">
        <label class="fs-6 fw-semibold mb-2">تاريخ ووقت البدء</label>
        <input type="datetime-local" value="{{ old('start_date', isset($info) && $info->start_date ? $info->start_date->format('Y-m-d\TH:i') : '') }}" name="start_date" class="form-control form-control-solid">
    </div>
    <div class="col-md-6 fv-row">
        <label class="fs-6 fw-semibold mb-2">تاريخ ووقت الانتهاء</label>
        <input type="datetime-local" value="{{ old('end_date', isset($info) && $info->end_date ? $info->end_date->format('Y-m-d\TH:i') : '') }}" name="end_date" class="form-control form-control-solid">
    </div>
</div>

<div class="row g-9 mb-8">
    <div class="col-md-3 form-check form-switch">
        <input class="form-check-input" type="checkbox" name="shuffle_questions" value="1" {{ old('shuffle_questions', $info->shuffle_questions ?? true) ? 'checked' : '' }}>
        <label class="form-check-label">ترتيب عشوائي للأسئلة</label>
    </div>
    <div class="col-md-3 form-check form-switch">
        <input class="form-check-input" type="checkbox" name="shuffle_answers" value="1" {{ old('shuffle_answers', $info->shuffle_answers ?? true) ? 'checked' : '' }}>
        <label class="form-check-label">ترتيب عشوائي للإجابات</label>
    </div>
    <div class="col-md-3 form-check form-switch">
        <input class="form-check-input" type="checkbox" name="review_available" value="1" {{ old('review_available', $info->review_available ?? true) ? 'checked' : '' }}>
        <label class="form-check-label">السماح بمراجعة الإجابات</label>
    </div>
    <div class="col-md-3 form-check form-switch">
        <input class="form-check-input" type="checkbox" name="anti_cheat_enabled" value="1" {{ old('anti_cheat_enabled', $info->anti_cheat_enabled ?? true) ? 'checked' : '' }}>
        <label class="form-check-label">تفعيل مراقبة الغش</label>
    </div>
</div>

<div class="row g-9 mb-8">
    <div class="col-md-4 fv-row">
        <label class="fs-6 fw-semibold mb-2">عدد مخالفات الغش المسموح</label>
        <input type="number" min="0" value="{{ old('anti_cheat_violation_limit', $info->anti_cheat_violation_limit ?? 3) }}" name="anti_cheat_violation_limit" class="form-control form-control-solid">
    </div>
    <div class="col-md-4 fv-row">
        <label class="fs-6 fw-semibold mb-2">الإجراء عند تجاوز الحد</label>
        <select name="anti_cheat_action" class="form-select form-select-solid">
            <option value="warning" {{ old('anti_cheat_action', $info->anti_cheat_action ?? 'warning') == 'warning' ? 'selected' : '' }}>تحذير للطالب</option>
            <option value="notify_teacher" {{ old('anti_cheat_action', $info->anti_cheat_action ?? '') == 'notify_teacher' ? 'selected' : '' }}>إشعار المدرس</option>
            <option value="auto_submit" {{ old('anti_cheat_action', $info->anti_cheat_action ?? '') == 'auto_submit' ? 'selected' : '' }}>تسليم تلقائي</option>
            <option value="log" {{ old('anti_cheat_action', $info->anti_cheat_action ?? '') == 'log' ? 'selected' : '' }}>تسجيل فقط</option>
        </select>
    </div>
</div>

{{-- Question selection --}}
<div class="card bg-light-primary border-0 mb-8">
    <div class="card-body">
        <label class="fs-6 fw-bold mb-4 d-block">أسئلة الامتحان</label>

        <ul class="nav nav-tabs mb-4" role="tablist">
            <li class="nav-item"><a class="nav-link active" data-bs-toggle="tab" href="#manual_tab">اختيار يدوي</a></li>
            <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#auto_tab">توليد تلقائي حسب الصعوبة</a></li>
            <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#new_questions_tab">إنشاء أسئلة جديدة</a></li>
        </ul>

        <div class="tab-content">
            <div class="tab-pane fade show active" id="manual_tab">
                <input type="radio" name="generation_mode" value="manual" checked class="d-none manual-radio">

                <div class="row g-3 mb-3">
                    <div class="col-md-4">
                        <input type="text" id="question_search_filter" class="form-control form-control-solid form-control-sm" placeholder="ابحث في نص السؤال...">
                    </div>
                    <div class="col-md-3">
                        <select id="question_type_filter" class="form-select form-select-solid form-select-sm">
                            <option value="">كل الأنواع</option>
                            <option value="mcq">اختيار من متعدد</option>
                            <option value="true_false">صح/خطأ</option>
                            <option value="text">إجابة نصية</option>
                            <option value="voice">إجابة صوتية</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <select id="question_difficulty_filter" class="form-select form-select-solid form-select-sm">
                            <option value="">كل مستويات الصعوبة</option>
                            <option value="easy">سهل</option>
                            <option value="medium">متوسط</option>
                            <option value="hard">صعب</option>
                            <option value="custom">مخصص</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <span class="text-muted small" id="question_filter_count"></span>
                    </div>
                </div>

                <div class="d-flex justify-content-between align-items-center mb-2">
                    <div>
                        <button type="button" id="select_all_questions_btn" class="btn btn-sm btn-light-primary">
                            <i class="bi bi-check2-square"></i> تحديد الكل (الظاهر حسب الفلتر)
                        </button>
                        <button type="button" id="clear_all_questions_btn" class="btn btn-sm btn-light-danger">
                            <i class="bi bi-x-square"></i> إلغاء التحديد
                        </button>
                    </div>
                </div>

                <div class="table-responsive" style="max-height:350px; overflow-y:auto;">
                    <table class="table table-sm table-striped" id="manual_questions_table">
                        <thead><tr><th></th><th>السؤال</th><th>النوع</th><th>الصعوبة</th></tr></thead>
                        <tbody>
                            @php $selectedIds = isset($info) ? $info->questions->pluck('id')->toArray() : []; @endphp
                            @foreach($questions as $q)
                            @php
                                $typeLabels = ['mcq' => 'اختيار من متعدد', 'true_false' => 'صح/خطأ', 'text' => 'إجابة نصية', 'voice' => 'إجابة صوتية'];
                                $typeIcons = ['mcq' => 'bi-ui-radios', 'true_false' => 'bi-toggle2-on', 'text' => 'bi-pencil-square', 'voice' => 'bi-mic-fill'];
                                $typeClasses = ['mcq' => 'primary', 'true_false' => 'info', 'text' => 'dark', 'voice' => 'danger'];
                                $difficultyLabels = ['easy' => 'سهل', 'medium' => 'متوسط', 'hard' => 'صعب', 'custom' => 'مخصص'];
                                $difficultyIcons = ['easy' => 'bi-emoji-smile', 'medium' => 'bi-emoji-neutral', 'hard' => 'bi-emoji-frown', 'custom' => 'bi-sliders'];
                                $difficultyClasses = ['easy' => 'success', 'medium' => 'warning', 'hard' => 'danger', 'custom' => 'info'];
                            @endphp
                            <tr class="question-row" data-type="{{ $q->type }}" data-difficulty="{{ $q->difficulty }}" data-marks="{{ $q->marks }}" data-text="{{ \Illuminate\Support\Str::lower(strip_tags($q->question_text)) }}">
                                <td><input type="checkbox" name="question_ids[]" class="question-checkbox" value="{{ $q->id }}" {{ in_array($q->id, $selectedIds) ? 'checked' : '' }}></td>
                                <td>{{ \Illuminate\Support\Str::limit(strip_tags($q->question_text), 70) }}</td>
                                <td>
                                    <span class="badge badge-light-{{ $typeClasses[$q->type] ?? 'secondary' }}">
                                        <i class="bi {{ $typeIcons[$q->type] ?? 'bi-question-circle' }} me-1"></i>{{ $typeLabels[$q->type] ?? $q->type }}
                                    </span>
                                </td>
                                <td>
                                    <span class="badge badge-light-{{ $difficultyClasses[$q->difficulty] ?? 'secondary' }}">
                                        <i class="bi {{ $difficultyIcons[$q->difficulty] ?? 'bi-dash-circle' }} me-1"></i>{{ $difficultyLabels[$q->difficulty] ?? $q->difficulty }}
                                    </span>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="card bg-light-success border-0 mt-3" id="selected_questions_summary">
                    <div class="card-body py-3">
                        <div class="row g-3 align-items-center text-center">
                            <div class="col-md-3">
                                <div class="fw-bold fs-4" id="summary_selected_count">0</div>
                                <div class="text-muted small">عدد الأسئلة المحددة</div>
                            </div>
                            <div class="col-md-6">
                                <div class="d-flex justify-content-center gap-3 flex-wrap" id="summary_by_type"></div>
                                <div class="text-muted small mt-1">توزيع الأنواع</div>
                            </div>
                            <div class="col-md-3">
                                <div class="fw-bold fs-4" id="summary_total_marks">0</div>
                                <div class="text-muted small">إجمالي الدرجات</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="tab-pane fade" id="auto_tab">
                <input type="radio" name="generation_mode" value="auto" class="d-none auto-radio">
                <div class="row g-5">
                    <div class="col-md-3">
                        <label class="fs-7 fw-semibold mb-1">سهل</label>
                        <input type="number" min="0" name="gen_easy" value="{{ old('gen_easy', $info->generation_rules['easy'] ?? 0) }}" class="form-control form-control-solid">
                    </div>
                    <div class="col-md-3">
                        <label class="fs-7 fw-semibold mb-1">متوسط</label>
                        <input type="number" min="0" name="gen_medium" value="{{ old('gen_medium', $info->generation_rules['medium'] ?? 0) }}" class="form-control form-control-solid">
                    </div>
                    <div class="col-md-3">
                        <label class="fs-7 fw-semibold mb-1">صعب</label>
                        <input type="number" min="0" name="gen_hard" value="{{ old('gen_hard', $info->generation_rules['hard'] ?? 0) }}" class="form-control form-control-solid">
                    </div>
                    <div class="col-md-3">
                        <label class="fs-7 fw-semibold mb-1">النوع (اختياري)</label>
                        <select name="gen_type" class="form-select form-select-solid">
                            <option value="">الكل</option>
                            <option value="mcq">اختيار من متعدد</option>
                            <option value="true_false">صح/خطأ</option>
                            <option value="text">نصي</option>
                            <option value="voice">صوتي</option>
                        </select>
                    </div>
                </div>
                <div class="form-text mt-2">سيتم اختيار الأسئلة عشوائياً وبدون تكرار عند الحفظ حسب الأعداد المحددة.</div>
            </div>

            {{-- Create brand-new questions of any type/skill directly while building the exam.
                 These are additive: they get created in the Question Bank AND attached to this
                 exam in the same save, regardless of what's chosen in the tabs above. --}}
            <div class="tab-pane fade" id="new_questions_tab">
                <div class="form-text mb-3">الأسئلة التي تنشئها هنا ستُضاف إلى بنك الأسئلة العام وتُربط بهذا الامتحان مباشرة، بالإضافة إلى أي أسئلة اخترتها من التبويبات الأخرى.</div>

                <div class="new_questions_repeater">
                    <div class="new_question_row card bg-light-warning border-0 mb-4" data-index="0">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <span class="fw-bold row-number">سؤال جديد 1</span>
                                <button type="button" class="btn btn-icon btn-light-danger btn-sm remove-new-row">
                                    <i class="bi bi-trash fs-5"></i>
                                </button>
                            </div>
                            <div class="row g-3 mb-3">
                                <div class="col-md-3">
                                    <label class="fs-7 fw-semibold mb-1">النوع</label>
                                    <select name="new_questions[0][type]" class="form-select form-select-solid new-row-type">
                                        <option value="mcq">اختيار من متعدد</option>
                                        <option value="true_false">صح / خطأ</option>
                                        <option value="text">إجابة نصية</option>
                                        <option value="voice">إجابة صوتية</option>
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label class="fs-7 fw-semibold mb-1">الصعوبة</label>
                                    <select name="new_questions[0][difficulty]" class="form-select form-select-solid">
                                        <option value="easy">سهل</option>
                                        <option value="medium" selected>متوسط</option>
                                        <option value="hard">صعب</option>
                                        <option value="custom">مخصص</option>
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label class="fs-7 fw-semibold mb-1">المهارة</label>
                                    <select name="new_questions[0][skill_id]" class="form-select form-select-solid">
                                        <option value="">بدون تصنيف</option>
                                        @foreach($skills as $skill)
                                            <option value="{{ $skill->id }}">{{ $skill->name_ar }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label class="fs-7 fw-semibold mb-1">الدرجة</label>
                                    <input type="number" step="0.5" min="0.5" value="1" name="new_questions[0][marks]" class="form-control form-control-solid">
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="fs-7 fw-semibold mb-1">نص السؤال</label>
                                <textarea name="new_questions[0][question_text]" class="form-control form-control-solid" rows="2" placeholder="اكتب نص السؤال هنا..."></textarea>
                            </div>
                            <div class="new_mcq_options">
                                <label class="fs-7 fw-semibold mb-2 d-block">الخيارات (حدد الإجابة الصحيحة)</label>
                                <div class="row g-2">
                                    @for($i = 0; $i < 4; $i++)
                                    <div class="col-md-6 mb-2 d-flex align-items-center gap-2">
                                        <input type="radio" name="new_questions[0][correct_option]" value="{{ $i }}" {{ $i == 0 ? 'checked' : '' }}>
                                        <input type="text" name="new_questions[0][options][{{ $i }}]" class="form-control form-control-solid" placeholder="الخيار {{ $i + 1 }}">
                                    </div>
                                    @endfor
                                </div>
                            </div>
                            <div class="new_tf_options d-none">
                                <label class="fs-7 fw-semibold mb-2 d-block">الإجابة الصحيحة</label>
                                <div class="d-flex gap-4">
                                    <label><input type="radio" name="new_questions[0][tf_correct]" value="true" checked> صح</label>
                                    <label><input type="radio" name="new_questions[0][tf_correct]" value="false"> خطأ</label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <button type="button" id="add_new_question_btn" class="btn btn-light-warning btn-sm">
                    <i class="bi bi-plus-lg"></i> إضافة سؤال جديد آخر
                </button>
            </div>
        </div>
    </div>
</div>
