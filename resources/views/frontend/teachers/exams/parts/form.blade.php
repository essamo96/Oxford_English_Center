<div class="row g-4 mb-4">
    <div class="col-md-8">
        <label class="form-label">عنوان الامتحان</label>
        <input type="text" name="title" class="form-control" value="{{ old('title', $info->title ?? '') }}" required>
    </div>
    <div class="col-md-4">
        <label class="form-label">المجموعة</label>
        <select name="group_id" class="form-select" {{ isset($info) ? 'disabled' : 'required' }}>
            @foreach($groups as $group)
                <option value="{{ $group->id }}" {{ old('group_id', $info->group_id ?? '') == $group->id ? 'selected' : '' }}>{{ $group->name }}</option>
            @endforeach
        </select>
        @isset($info)<input type="hidden" name="group_id" value="{{ $info->group_id }}">@endisset
    </div>
</div>

<div class="row g-4 mb-4">
    <div class="col-md-12">
        <label class="form-label">الوصف / التعليمات</label>
        <textarea name="description" class="form-control" rows="2">{{ old('description', $info->description ?? '') }}</textarea>
    </div>
</div>

<div class="row g-4 mb-4">
    <div class="col-md-3">
        <label class="form-label">المدة (دقيقة)</label>
        <input type="number" min="1" name="duration_minutes" class="form-control" value="{{ old('duration_minutes', $info->duration_minutes ?? 30) }}">
    </div>
    <div class="col-md-3">
        <label class="form-label">أقصى عدد محاولات</label>
        <input type="number" min="1" name="max_attempts" class="form-control" value="{{ old('max_attempts', $info->max_attempts ?? 1) }}">
    </div>
    <div class="col-md-3">
        <label class="form-label">درجة النجاح (%)</label>
        <input type="number" step="0.5" min="0" max="100" name="passing_score" class="form-control" value="{{ old('passing_score', $info->passing_score ?? 50) }}">
    </div>
    <div class="col-md-3">
        <label class="form-label">ظهور النتيجة</label>
        <select name="result_visibility" class="form-select">
            <option value="immediate" {{ old('result_visibility', $info->result_visibility ?? 'immediate') == 'immediate' ? 'selected' : '' }}>فوري</option>
            <option value="after_review" {{ old('result_visibility', $info->result_visibility ?? '') == 'after_review' ? 'selected' : '' }}>بعد المراجعة</option>
        </select>
    </div>
</div>

<div class="row g-4 mb-4">
    <div class="col-md-6">
        <label class="form-label">تاريخ البدء</label>
        <input type="datetime-local" name="start_date" class="form-control" value="{{ old('start_date', isset($info) && $info->start_date ? $info->start_date->format('Y-m-d\TH:i') : '') }}">
    </div>
    <div class="col-md-6">
        <label class="form-label">تاريخ الانتهاء</label>
        <input type="datetime-local" name="end_date" class="form-control" value="{{ old('end_date', isset($info) && $info->end_date ? $info->end_date->format('Y-m-d\TH:i') : '') }}">
    </div>
</div>

<div class="row g-4 mb-4">
    <div class="col-md-3 form-check form-switch">
        <input class="form-check-input" type="checkbox" name="shuffle_questions" value="1" {{ old('shuffle_questions', $info->shuffle_questions ?? true) ? 'checked' : '' }}>
        <label class="form-check-label">ترتيب عشوائي</label>
    </div>
    <div class="col-md-3 form-check form-switch">
        <input class="form-check-input" type="checkbox" name="review_available" value="1" {{ old('review_available', $info->review_available ?? true) ? 'checked' : '' }}>
        <label class="form-check-label">السماح بالمراجعة</label>
    </div>
    <div class="col-md-3 form-check form-switch">
        <input class="form-check-input" type="checkbox" name="anti_cheat_enabled" value="1" {{ old('anti_cheat_enabled', $info->anti_cheat_enabled ?? true) ? 'checked' : '' }}>
        <label class="form-check-label">مراقبة الغش</label>
    </div>
</div>

<div class="mb-4">
    <label class="form-label fw-bold">أسئلتي وأسئلة البنك العام</label>
    <div class="table-responsive border rounded p-2" style="max-height:320px; overflow-y:auto;">
        <table class="table table-sm">
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
    <div class="form-text">يمكنك استخدام أسئلتك الخاصة أو أسئلة بنك الأسئلة العام فقط.</div>
</div>
