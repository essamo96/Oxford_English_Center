<div class="tex-section">
    <div class="tex-section__header">
        <i class="bi bi-info-circle"></i>
        <h6>معلومات أساسية</h6>
    </div>
    <div class="tex-section__body">
        <div class="row g-4 mb-4">
            <div class="col-md-8">
                <label class="tex-label">عنوان الامتحان</label>
                <input type="text" name="title" class="form-control" value="{{ old('title', $info->title ?? '') }}" required>
            </div>
            <div class="col-md-4">
                <label class="tex-label">المجموعة</label>
                <select name="group_id" class="form-select" {{ isset($info) ? 'disabled' : 'required' }}>
                    @foreach($groups as $group)
                        <option value="{{ $group->id }}" {{ old('group_id', $info->group_id ?? '') == $group->id ? 'selected' : '' }}>{{ $group->name }}</option>
                    @endforeach
                </select>
                @isset($info)<input type="hidden" name="group_id" value="{{ $info->group_id }}">@endisset
            </div>
        </div>
        <div class="row g-4">
            <div class="col-md-12">
                <label class="tex-label">الوصف / التعليمات</label>
                <textarea name="description" id="exam_description" class="form-control" rows="4" placeholder="تعليمات مختصرة للطالب قبل بدء الامتحان...">{{ old('description', $info->description ?? '') }}</textarea>
            </div>
        </div>
    </div>
</div>

<div class="tex-section">
    <div class="tex-section__header">
        <i class="bi bi-sliders"></i>
        <h6>إعدادات الامتحان</h6>
    </div>
    <div class="tex-section__body">
        <div class="row g-4 mb-4">
            <div class="col-md-3">
                <label class="tex-label"><i class="bi bi-clock"></i> المدة (دقيقة)</label>
                <input type="number" min="1" name="duration_minutes" class="form-control" value="{{ old('duration_minutes', $info->duration_minutes ?? 30) }}">
            </div>
            <div class="col-md-3">
                <label class="tex-label"><i class="bi bi-arrow-repeat"></i> أقصى عدد محاولات</label>
                <input type="number" min="1" name="max_attempts" class="form-control" value="{{ old('max_attempts', $info->max_attempts ?? 1) }}">
            </div>
            <div class="col-md-3">
                <label class="tex-label"><i class="bi bi-trophy"></i> درجة النجاح (%)</label>
                <input type="number" step="0.5" min="0" max="100" name="passing_score" class="form-control" value="{{ old('passing_score', $info->passing_score ?? 50) }}">
            </div>
            <div class="col-md-3">
                <label class="tex-label"><i class="bi bi-eye"></i> ظهور النتيجة</label>
                <select name="result_visibility" class="form-select">
                    <option value="immediate" {{ old('result_visibility', $info->result_visibility ?? 'immediate') == 'immediate' ? 'selected' : '' }}>فوري</option>
                    <option value="after_review" {{ old('result_visibility', $info->result_visibility ?? '') == 'after_review' ? 'selected' : '' }}>بعد المراجعة</option>
                </select>
            </div>
        </div>

        <div class="row g-4 mb-4">
            <div class="col-md-6">
                <label class="tex-label"><i class="bi bi-calendar-event"></i> تاريخ البدء</label>
                <input type="datetime-local" name="start_date" class="form-control" value="{{ old('start_date', isset($info) && $info->start_date ? $info->start_date->format('Y-m-d\TH:i') : '') }}">
            </div>
            <div class="col-md-6">
                <label class="tex-label"><i class="bi bi-calendar-check"></i> تاريخ الانتهاء</label>
                <input type="datetime-local" name="end_date" class="form-control" value="{{ old('end_date', isset($info) && $info->end_date ? $info->end_date->format('Y-m-d\TH:i') : '') }}">
            </div>
        </div>

        <div class="row g-3">
            <div class="col-md-4">
                <div class="tex-switch-group form-check form-switch mb-0">
                    <input class="form-check-input" type="checkbox" name="shuffle_questions" value="1" {{ old('shuffle_questions', $info->shuffle_questions ?? true) ? 'checked' : '' }}>
                    <label class="form-check-label">ترتيب عشوائي للأسئلة</label>
                </div>
            </div>
            <div class="col-md-4">
                <div class="tex-switch-group form-check form-switch mb-0">
                    <input class="form-check-input" type="checkbox" name="review_available" value="1" {{ old('review_available', $info->review_available ?? true) ? 'checked' : '' }}>
                    <label class="form-check-label">السماح بمراجعة الإجابات</label>
                </div>
            </div>
            <div class="col-md-4">
                <div class="tex-switch-group form-check form-switch mb-0">
                    <input class="form-check-input" type="checkbox" name="anti_cheat_enabled" value="1" {{ old('anti_cheat_enabled', $info->anti_cheat_enabled ?? true) ? 'checked' : '' }}>
                    <label class="form-check-label">تفعيل مراقبة الغش</label>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="tex-section">
    <div class="tex-section__header">
        <i class="bi bi-journal-text"></i>
        <h6>أسئلة الامتحان</h6>
        <span class="text-muted small ms-auto">أسئلتك الخاصة + بنك الأسئلة العام</span>
    </div>
    <div class="tex-section__body">
        <div class="row g-3 mb-3">
            <div class="col-md-4">
                <input type="text" id="tex_search_filter" class="form-control form-control-sm" placeholder="ابحث في نص السؤال...">
            </div>
            <div class="col-md-3">
                <select id="tex_type_filter" class="form-select form-select-sm">
                    <option value="">كل الأنواع</option>
                    <option value="mcq">اختيار من متعدد</option>
                    <option value="true_false">صح/خطأ</option>
                    <option value="text">إجابة نصية</option>
                    <option value="voice">إجابة صوتية</option>
                </select>
            </div>
            <div class="col-md-3">
                <select id="tex_difficulty_filter" class="form-select form-select-sm">
                    <option value="">كل مستويات الصعوبة</option>
                    <option value="easy">سهل</option>
                    <option value="medium">متوسط</option>
                    <option value="hard">صعب</option>
                    <option value="custom">مخصص</option>
                </select>
            </div>
            <div class="col-md-2 d-flex align-items-center">
                <span class="text-muted small" id="tex_filter_count"></span>
            </div>
        </div>

        <div class="d-flex gap-2 mb-3">
            <button type="button" id="tex_select_all_btn" class="btn btn-sm btn-outline-primary"><i class="bi bi-check2-square"></i> تحديد الكل (الظاهر)</button>
            <button type="button" id="tex_clear_all_btn" class="btn btn-sm btn-outline-danger"><i class="bi bi-x-square"></i> إلغاء التحديد</button>
        </div>

        <div style="max-height:340px; overflow-y:auto;">
            @php
                $selectedIds = isset($info) ? $info->questions->pluck('id')->toArray() : [];
                $typeLabels = ['mcq' => 'اختيار من متعدد', 'true_false' => 'صح/خطأ', 'text' => 'إجابة نصية', 'voice' => 'إجابة صوتية'];
                $typeIcons = ['mcq' => 'bi-ui-radios', 'true_false' => 'bi-toggle2-on', 'text' => 'bi-pencil-square', 'voice' => 'bi-mic-fill'];
                $typePill = ['mcq' => 'tex-pill--info', 'true_false' => 'tex-pill--muted', 'text' => 'tex-pill--muted', 'voice' => 'tex-pill--danger'];
                $difficultyLabels = ['easy' => 'سهل', 'medium' => 'متوسط', 'hard' => 'صعب', 'custom' => 'مخصص'];
                $difficultyPill = ['easy' => 'tex-pill--success', 'medium' => 'tex-pill--warning', 'hard' => 'tex-pill--danger', 'custom' => 'tex-pill--info'];
            @endphp
            @forelse($questions as $q)
            <label class="tex-question-row tex-question-item" data-type="{{ $q->type }}" data-difficulty="{{ $q->difficulty }}" data-text="{{ \Illuminate\Support\Str::lower(strip_tags($q->question_text)) }}" data-marks="{{ $q->marks }}">
                <input type="checkbox" name="question_ids[]" class="tex-question-checkbox" value="{{ $q->id }}" {{ in_array($q->id, $selectedIds) ? 'checked' : '' }}>
                <span class="tex-question-row__text" dir="auto">{{ \Illuminate\Support\Str::limit(strip_tags($q->question_text), 90) }}</span>
                <span class="tex-pill {{ $typePill[$q->type] ?? 'tex-pill--muted' }}"><i class="bi {{ $typeIcons[$q->type] ?? '' }}"></i> {{ $typeLabels[$q->type] ?? $q->type }}</span>
                <span class="tex-pill {{ $difficultyPill[$q->difficulty] ?? 'tex-pill--muted' }}">{{ $difficultyLabels[$q->difficulty] ?? $q->difficulty }}</span>
            </label>
            @empty
                <div class="tex-empty">
                    <i class="bi bi-journal-x"></i>
                    <p class="mb-0">لا توجد أسئلة متاحة بعد. أضف أسئلة من بنك الأسئلة أولاً.</p>
                </div>
            @endforelse
        </div>

        <div class="tex-summary mt-3">
            <div class="tex-summary__item">
                <div class="tex-summary__value" id="tex_summary_count">0</div>
                <div class="tex-summary__label">سؤال محدد</div>
            </div>
            <div class="tex-summary__item">
                <div class="tex-summary__value" id="tex_summary_marks">0</div>
                <div class="tex-summary__label">إجمالي الدرجات</div>
            </div>
        </div>
    </div>
</div>

<script>
    (function () {
        var $rows = $('.tex-question-item');

        function applyFilter() {
            var keyword = $('#tex_search_filter').val().toLowerCase().trim();
            var type = $('#tex_type_filter').val();
            var difficulty = $('#tex_difficulty_filter').val();
            var visible = 0;

            $rows.each(function () {
                var $row = $(this);
                var matchesText = !keyword || String($row.data('text')).indexOf(keyword) !== -1;
                var matchesType = !type || String($row.data('type')) === type;
                var matchesDifficulty = !difficulty || String($row.data('difficulty')) === difficulty;
                var show = matchesText && matchesType && matchesDifficulty;
                $row.toggle(show);
                if (show) visible++;
            });

            $('#tex_filter_count').text(visible + ' من ' + $rows.length);
        }

        function updateSummary() {
            var count = 0, totalMarks = 0;
            $rows.find('.tex-question-checkbox:checked').each(function () {
                count++;
                totalMarks += parseFloat($(this).closest('.tex-question-item').data('marks')) || 0;
            });
            $('#tex_summary_count').text(count);
            $('#tex_summary_marks').text(totalMarks % 1 === 0 ? totalMarks : totalMarks.toFixed(1));
        }

        $('#tex_search_filter').on('input keyup change', applyFilter);
        $('#tex_type_filter').on('change', applyFilter);
        $('#tex_difficulty_filter').on('change', applyFilter);
        applyFilter();

        $(document).on('change', '.tex-question-checkbox', updateSummary);
        updateSummary();

        $('#tex_select_all_btn').on('click', function () {
            $rows.filter(':visible').find('.tex-question-checkbox').prop('checked', true);
            updateSummary();
        });
        $('#tex_clear_all_btn').on('click', function () {
            $rows.find('.tex-question-checkbox').prop('checked', false);
            updateSummary();
        });
    })();
</script>
