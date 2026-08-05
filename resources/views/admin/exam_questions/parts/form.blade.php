{{-- shared fields for add/edit --}}
<div class="row g-9 mb-8">
    <div class="col-md-6 fv-row">
        <label class="fs-6 fw-semibold mb-2">نوع السؤال <span class="text-danger">*</span></label>
        <select name="type" id="q_type" class="form-select form-select-solid" required>
            <option value="mcq" {{ old('type', $info->type ?? '') == 'mcq' ? 'selected' : '' }}>اختيار من متعدد</option>
            <option value="true_false" {{ old('type', $info->type ?? '') == 'true_false' ? 'selected' : '' }}>صح / خطأ</option>
            <option value="text" {{ old('type', $info->type ?? '') == 'text' ? 'selected' : '' }}>إجابة نصية</option>
            <option value="voice" {{ old('type', $info->type ?? '') == 'voice' ? 'selected' : '' }}>إجابة صوتية</option>
        </select>
    </div>
    <div class="col-md-6 fv-row">
        <label class="fs-6 fw-semibold mb-2">الصعوبة <span class="text-danger">*</span></label>
        <select name="difficulty" class="form-select form-select-solid" required>
            <option value="easy" {{ old('difficulty', $info->difficulty ?? '') == 'easy' ? 'selected' : '' }}>سهل</option>
            <option value="medium" {{ old('difficulty', $info->difficulty ?? 'medium') == 'medium' ? 'selected' : '' }}>متوسط</option>
            <option value="hard" {{ old('difficulty', $info->difficulty ?? '') == 'hard' ? 'selected' : '' }}>صعب</option>
            <option value="custom" {{ old('difficulty', $info->difficulty ?? '') == 'custom' ? 'selected' : '' }}>مخصص</option>
        </select>
    </div>
</div>

<div class="row g-9 mb-8">
    <div class="col-md-12 fv-row">
        <label class="fs-6 fw-semibold mb-2">نص السؤال <span class="text-danger">*</span></label>
        <textarea name="question_text" id="question_text" class="form-control form-control-solid ckeditor" rows="3" required>{{ old('question_text', $info->question_text ?? '') }}</textarea>
    </div>
</div>

<div class="row g-9 mb-8">
    <div class="col-md-6 fv-row">
        <label class="fs-6 fw-semibold mb-2">المهارة</label>
        <select name="skill_id" data-control="select2" data-placeholder="اختر مهارة..." class="form-select form-select-solid">
            <option value="">بدون تصنيف</option>
            @foreach($skills as $skill)
                <option value="{{ $skill->id }}" {{ old('skill_id', $info->skill_id ?? '') == $skill->id ? 'selected' : '' }}>{{ $skill->name_ar }}</option>
            @endforeach
        </select>
    </div>
    <div class="col-md-6 fv-row">
        <label class="fs-6 fw-semibold mb-2">البرنامج (اختياري)</label>
        <select name="program_id" data-control="select2" data-placeholder="اختر برنامج..." class="form-select form-select-solid">
            <option value="">كل البرامج</option>
            @foreach($programs as $program)
                <option value="{{ $program->id }}" {{ old('program_id', $info->program_id ?? '') == $program->id ? 'selected' : '' }}>{{ $program->title }}</option>
            @endforeach
        </select>
    </div>
</div>

<div class="row g-9 mb-8">
    <div class="col-md-4 fv-row">
        <label class="fs-6 fw-semibold mb-2">الدرجة</label>
        <input type="number" step="0.5" min="0.5" value="{{ old('marks', $info->marks ?? 1) }}" name="marks" class="form-control form-control-solid">
    </div>
    <div class="col-md-4 fv-row">
        <label class="fs-6 fw-semibold mb-2">الوقت المقدر (ثانية)</label>
        <input type="number" min="5" value="{{ old('estimated_time_seconds', $info->estimated_time_seconds ?? 60) }}" name="estimated_time_seconds" class="form-control form-control-solid">
    </div>
    <div class="col-md-4 fv-row">
        <label class="fs-6 fw-semibold mb-2">الحالة</label>
        <select name="status" class="form-select form-select-solid">
            <option value="active" {{ old('status', $info->status ?? 'active') == 'active' ? 'selected' : '' }}>فعال</option>
            <option value="draft" {{ old('status', $info->status ?? '') == 'draft' ? 'selected' : '' }}>مسودة</option>
            <option value="archived" {{ old('status', $info->status ?? '') == 'archived' ? 'selected' : '' }}>مؤرشف</option>
        </select>
    </div>
</div>

<div class="row g-9 mb-8">
    <div class="col-md-6 fv-row">
        <label class="fs-6 fw-semibold mb-2">صورة توضيحية (اختياري)</label>
        <input type="file" name="image" class="form-control form-control-solid">
        @isset($info)
            @if($info->image_path)
                <img src="{{ asset($info->image_path) }}" class="mt-3 rounded" style="max-height:100px">
            @endif
        @endisset
    </div>
    <div class="col-md-6 fv-row">
        <label class="fs-6 fw-semibold mb-2">ملف صوتي (اختياري، لمهارة الاستماع)</label>
        <input type="file" name="audio" class="form-control form-control-solid">
        @isset($info)
            @if($info->audio_path)
                <audio controls class="mt-3 w-100"><source src="{{ asset($info->audio_path) }}"></audio>
            @endif
        @endisset
    </div>
</div>

<div class="row g-9 mb-8">
    <div class="col-md-12 fv-row">
        <label class="fs-6 fw-semibold mb-2">الشرح (يظهر للطالب عند المراجعة)</label>
        <textarea name="explanation" id="explanation" class="form-control form-control-solid ckeditor" rows="2">{{ old('explanation', $info->explanation ?? '') }}</textarea>
    </div>
</div>

<div class="row g-9 mb-8">
    <div class="col-md-12 fv-row">
        <label class="fs-6 fw-semibold mb-2">الوسوم (Tags)</label>
        <input type="text" id="question_tags" value="{{ old('tags', isset($info) && $info->tags ? implode(',', $info->tags) : '') }}" name="tags" class="form-control form-control-solid" placeholder="اكتب وسماً واضغط Enter...">
        <div class="form-text">مثال: present simple, verbs, unit-3</div>
    </div>
</div>

{{-- MCQ / True-False options builder --}}
<div id="options_wrapper" class="card bg-light-primary border-0 mb-8">
    <div class="card-body">
        <label class="fs-6 fw-bold mb-4 d-block">خيارات الإجابة (للأسئلة من نوع اختيار من متعدد / صح-خطأ)</label>
        <div id="options_container">
            @php $existingOptions = old('options') ? [] : ($info->options ?? []); @endphp
            @if(count($existingOptions))
                @foreach($existingOptions as $idx => $opt)
                    <div class="row g-3 mb-3 align-items-center option-row">
                        <div class="col-auto"><input type="checkbox" class="form-check-input" name="correct_options[]" value="{{ $idx }}" {{ $opt->is_correct ? 'checked' : '' }}></div>
                        <div class="col"><input type="text" name="options[{{ $idx }}]" class="form-control form-control-solid" value="{{ $opt->option_text }}" placeholder="نص الخيار"></div>
                    </div>
                @endforeach
            @else
                <div class="row g-3 mb-3 align-items-center option-row">
                    <div class="col-auto"><input type="checkbox" class="form-check-input" name="correct_options[]" value="0"></div>
                    <div class="col"><input type="text" name="options[0]" class="form-control form-control-solid" placeholder="نص الخيار"></div>
                </div>
                <div class="row g-3 mb-3 align-items-center option-row">
                    <div class="col-auto"><input type="checkbox" class="form-check-input" name="correct_options[]" value="1"></div>
                    <div class="col"><input type="text" name="options[1]" class="form-control form-control-solid" placeholder="نص الخيار"></div>
                </div>
            @endif
        </div>
        <button type="button" id="add_option_btn" class="btn btn-sm btn-light-primary mt-2">
            <i class="bi bi-plus-lg"></i> إضافة خيار
        </button>
        <div class="form-text mt-2">حدد صح للخيار (أو الخيارات) الصحيحة.</div>
    </div>
</div>

<script>
    (function () {
        var optIndex = document.querySelectorAll('#options_container .option-row').length;

        function toggleOptions() {
            var type = document.getElementById('q_type').value;
            var wrapper = document.getElementById('options_wrapper');
            wrapper.style.display = (type === 'mcq' || type === 'true_false') ? 'block' : 'none';
        }

        document.getElementById('q_type').addEventListener('change', toggleOptions);
        toggleOptions();

        document.getElementById('add_option_btn').addEventListener('click', function () {
            var container = document.getElementById('options_container');
            var row = document.createElement('div');
            row.className = 'row g-3 mb-3 align-items-center option-row';
            row.innerHTML = '<div class="col-auto"><input type="checkbox" class="form-check-input" name="correct_options[]" value="' + optIndex + '"></div>' +
                '<div class="col"><input type="text" name="options[' + optIndex + ']" class="form-control form-control-solid" placeholder="نص الخيار"></div>';
            container.appendChild(row);
            optIndex++;
        });
    })();
</script>
