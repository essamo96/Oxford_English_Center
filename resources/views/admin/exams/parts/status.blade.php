@php
    $labels = ['draft' => 'مسودة', 'scheduled' => 'مجدول', 'published' => 'منشور', 'closed' => 'مغلق'];
    $classes = ['draft' => 'secondary', 'scheduled' => 'warning', 'published' => 'success', 'closed' => 'danger'];
    $permPrefix = $category === 'placement' ? 'exam_placement_tests' : 'group_exams';
@endphp
<button data-href="{{ Crypt::encrypt($id) }}" class="btn btn-sm btn-light-{{ $classes[$status] ?? 'secondary' }} @can('admin.' . $permPrefix . '.publish') status @endcan" style="min-width:90px;" title="اضغط للانتقال للحالة التالية">
    {{ $labels[$status] ?? $status }}
</button>
