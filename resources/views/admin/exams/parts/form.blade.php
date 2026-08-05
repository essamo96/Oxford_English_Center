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
        <textarea name="description" class="form-control form-control-solid" rows="2">{{ old('description', $info->description ?? '') }}</textarea>
    </div>
</div>

<div class="row g-9 mb-8">
    <div class="col-md-6 fv-row">
        <label class="fs-6 fw-semibold mb-2">البرنامج</label>
        <select name="program_id" data-control="select2" class="form-select form-select-solid">
            <option value="">{{ $category === 'placement' ? 'كل البرامج' : 'اختر برنامج' }}</option>
            @foreach($programs as $program)
                <option value="{{ $program->id }}" {{ old('program_id', $info->program_id ?? '') == $program->id ? 'selected' : '' }}>{{ $program->title }}</option>
            @endforeach
        </select>
    </div>
    @if($category === 'group')
    <div class="col-md-6 fv-row">
        <label class="fs-6 fw-semibold mb-2">المجموعة <span class="text-danger">*</span></label>
        <select name="group_id" data-control="select2" class="form-select form-select-solid" required>
            <option value="">اختر مجموعة...</option>
            @foreach($groups as $group)
                <option value="{{ $group->id }}" {{ old('group_id', $info->group_id ?? '') == $group->id ? 'selected' : '' }}>{{ $group->title }}</option>
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
        </ul>

        <div class="tab-content">
            <div class="tab-pane fade show active" id="manual_tab">
                <input type="radio" name="generation_mode" value="manual" checked class="d-none manual-radio">
                <div class="table-responsive" style="max-height:350px; overflow-y:auto;">
                    <table class="table table-sm table-striped">
                        <thead><tr><th></th><th>السؤال</th><th>النوع</th><th>الصعوبة</th></tr></thead>
                        <tbody>
                            @php $selectedIds = isset($info) ? $info->questions->pluck('id')->toArray() : []; @endphp
                            @foreach($questions as $q)
                            <tr>
                                <td><input type="checkbox" name="question_ids[]" value="{{ $q->id }}" {{ in_array($q->id, $selectedIds) ? 'checked' : '' }}></td>
                                <td>{{ \Illuminate\Support\Str::limit(strip_tags($q->question_text), 70) }}</td>
                                <td>{{ $q->type }}</td>
                                <td>{{ $q->difficulty }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
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
        </div>
    </div>
</div>

<script>
    document.querySelectorAll('a[data-bs-toggle="tab"]').forEach(function (tab) {
        tab.addEventListener('shown.bs.tab', function (e) {
            var target = e.target.getAttribute('href');
            if (target === '#auto_tab') {
                document.querySelector('.auto-radio').checked = true;
            } else {
                document.querySelector('.manual-radio').checked = true;
            }
        });
    });
</script>
