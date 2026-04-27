@forelse($teachers as $teacher)
    <div class="d-flex flex-stack py-4 contact-item px-5 rounded-3 mb-1" data-id="{{ Crypt::encrypt($teacher->id) }}" id="contact-{{ $teacher->id }}" style="transition: all 0.3s ease;">
        <div class="d-flex align-items-center">
            <div class="symbol symbol-45px symbol-circle">
                @if($teacher->image)
                    <img src="{{ url($teacher->image) }}" alt="Pic" />
                @else
                    <span class="symbol-label bg-light-success text-success fs-6 fw-bolder">{{ mb_substr($teacher->name, 0, 1) }}</span>
                @endif
                @if($teacher->unread_count > 0)
                    <div class="symbol-badge bg-success start-100 top-100 border-4 h-12px w-12px ms-n2 mt-n2"></div>
                @endif
            </div>
            <div class="ms-5">
                <a href="javascript:void(0)" class="fs-6 fw-bold text-gray-900 text-hover-info mb-1 d-block contact-name">{{ $teacher->name }}</a>
                <div class="fw-semibold text-muted fs-7 line-clamp-1 last-msg-text">
                    {{ $teacher->last_message ? Str::limit($teacher->last_message->content, 35) : 'لا توجد رسائل' }}
                </div>
            </div>
        </div>
        <div class="d-flex flex-column align-items-end ms-2">
            <span class="text-muted fs-8 mb-1 whitespace-nowrap">{{ $teacher->last_message ? $teacher->last_message->created_at->diffForHumans() : '' }}</span>
            <div class="d-flex align-items-center">
                @if($teacher->unread_count > 0)
                    <span class="badge badge-sm badge-circle badge-light-success fw-bold me-2">{{ $teacher->unread_count }}</span>
                @else
                    <span class="badge badge-sm badge-circle badge-light-primary fw-bold me-2">{{ $teacher->messages_count ?? '0' }}</span>
                @endif
                <!-- Tools Button -->
                <button type="button" class="btn btn-sm btn-icon btn-color-primary btn-active-light-primary contact-tools-btn" data-kt-menu-trigger="click" data-kt-menu-placement="bottom-end">
                    <i class="ki-duotone ki-setting-2 fs-4">
                        <span class="path1"></span><span class="path2"></span>
                    </i>
                </button>
                <div class="menu menu-sub menu-sub-dropdown menu-column menu-rounded menu-gray-800 menu-state-bg-light-primary fw-semibold w-200px py-3" data-kt-menu="true">
                    <div class="menu-item px-3">
                        <div class="menu-content text-muted pb-2 px-3 fs-7 text-uppercase">أدوات المعلم</div>
                    </div>
                    <div class="menu-item px-3">
                        <a href="{{ url('admin/teachers/edit') }}/{{ Crypt::encrypt($teacher->id) }}" class="menu-link px-3" target="_blank">تعديل البيانات</a>
                    </div>
                    <div class="menu-item px-3">
                        <a href="{{ url('admin/teachers/password') }}/{{ Crypt::encrypt($teacher->id) }}" class="menu-link px-3" target="_blank">تغيير كلمة المرور</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@empty
    <div class="text-center text-muted py-10">لا يوجد جهات اتصال من المعلمين</div>
@endforelse
