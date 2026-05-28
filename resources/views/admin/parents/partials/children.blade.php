<div class="table-responsive">
    <table class="table table-row-dashed align-middle gs-0 gy-3 my-0">
        <thead>
            <tr class="fs-7 fw-bold text-gray-400 border-bottom-0">
                <th class="p-0 pb-3 min-w-150px text-start">Student Name</th>
                <th class="p-0 pb-3 min-w-100px text-center">Mobile</th>
                <th class="p-0 pb-3 min-w-100px text-center">Program</th>
                <th class="p-0 pb-3 min-w-100px text-end">Action</th>
            </tr>
        </thead>
        <tbody>
            @foreach($parent->students as $student)
                <tr>
                    <td>
                        <div class="d-flex align-items-center">
                            <div class="symbol symbol-40px me-3">
                                @php
                                    $defaultAvatar = asset('assets/media/avatars/blank.png');
                                    $studentImg = $student->image ? asset('uploads/'.$student->image) : $defaultAvatar;
                                @endphp
                                <img src="{{ $studentImg }}" alt="{{ $student->name }}"
                                     onerror="this.onerror=null; this.src='{{ $defaultAvatar }}';"
                                     style="object-fit: cover; border-radius: 50%;" />
                            </div>
                            <div class="d-flex flex-column">
                                <span class="text-gray-800 fw-bold fs-6">{{ $student->name }}</span>
                                <span class="text-muted fw-semibold fs-7">{{ $student->name_en }}</span>
                            </div>
                        </div>
                    </td>
                    <td class="text-center">
                        <span class="text-gray-800 fw-bold fs-6">{{ $student->mobile }}</span>
                    </td>
                    <td class="text-center">
                        <span class="badge badge-light-success">{{ $student->program_type }}</span>
                    </td>
                    <td class="text-end">
                        <a href="{{ route('students.edit', \Illuminate\Support\Facades\Crypt::encrypt($student->id)) }}" class="btn btn-sm btn-icon btn-light-primary" target="_blank" title="تعديل بيانات الطالب">
                            <i class="bi bi-pencil-square"></i>
                        </a>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
