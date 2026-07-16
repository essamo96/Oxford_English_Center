<style>
    /* Shuttle Modal Scoped Styles */
    .shuttle-modal .modal-dialog { max-width: 980px; }
    .shuttle-modal .modal-content { border-radius: 16px; border: none; box-shadow: 0 10px 40px rgba(0,0,0,0.15); overflow: hidden; }
    .shuttle-modal .modal-body { padding: 18px 22px; background: #f9f9fb; }
    .shuttle-column { background: #fff; border: 1px solid #e1e3ea; border-radius: 12px; overflow: hidden; display: flex; flex-direction: column; height: 100%; box-shadow: 0 4px 12px rgba(0,0,0,0.03); }
    .shuttle-head { padding: 14px 18px; background: #eff2f5; border-bottom: 1px solid #e1e3ea; }
    .shuttle-head.tint-warn { background: #fff8e1; border-bottom-color: #ffe082; }
    .shuttle-head .ttl { margin: 0; font-weight: 700; font-size: 1rem; }
    .shuttle-list { list-style: none; margin: 0; padding: 8px; min-height: 280px; max-height: 380px; overflow-y: auto; background: #fff; }
    .shuttle-list li { padding: 10px 14px; margin-bottom: 6px; border-radius: 8px; border: 1px solid #f1f1f4; background: #fff; cursor: grab; font-size: 0.95rem; font-weight: 600; color: #3f4254; display: flex; align-items: center; justify-content: space-between; transition: all 0.2s ease; }
    .shuttle-list li:hover { border-color: #009ef7; transform: translateY(-1px); box-shadow: 0 4px 8px rgba(0,158,247,0.1); }
    .shuttle-list li.sortable-ghost { opacity: 0.35; background: #f1faff !important; border: 1px dashed #009ef7 !important; }
    .shuttle-empty { text-align: center; color: #a1a5b7; font-size: 0.9rem; margin-top: 20px; font-weight: 500; cursor: default !important; border: none !important; background: transparent !important; }
    .shuttle-empty:hover { box-shadow: none !important; transform: none !important; border-color: transparent !important; }
    .shuttle-count { display: inline-block; padding: 2px 8px; border-radius: 6px; font-size: 0.8rem; font-weight: 700; background: #009ef7; color: #fff; }
    .shuttle-count.tint-warn { background: #ffc700; color: #fff; }
    .shuttle-tools-row { background: #fff; border: 1px solid #e1e3ea; border-radius: 10px; padding: 12px 14px; margin-bottom: 14px; }
    .shuttle-tools-row .form-label { font-size: 0.8rem; font-weight: 600; margin-bottom: 4px; color: #5e6278; }
</style>

<div class="modal fade shuttle-modal" id="smsShuttleModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fw-bold text-primary" id="smsModalTitle"><i class="fa fa-envelope me-2"></i>إرسال رسائل SMS</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <!-- Filters Row -->
                <div class="shuttle-tools-row">
                    <div class="d-flex justify-content-between align-items-center mb-3 pb-2 border-bottom">
                        <span class="text-muted fs-7"><i class="fa fa-filter"></i> استخدم الفلاتر أدناه لجلب الطلاب إلى قائمة (المراد الإرسال لهم).</span>
                        <button type="button" id="smsCheckBalanceBtn" class="btn btn-sm btn-outline-info">
                            <i class="fa fa-money-bill"></i> استعلام الرصيد
                        </button>
                    </div>
                    <div id="smsNormalFilters">
                        <div class="row g-2 align-items-end mb-2">
                            <div class="col-md-3">
                                <label class="form-label">نوع البرنامج</label>
                                <select id="smsFilterProgramType" class="form-select form-select-sm">
                                    <option value="">الكل</option>
                                    <option value="adult">Adults</option>
                                    <option value="kids">Kids</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">البرنامج</label>
                                <select id="smsFilterProgram" class="form-select form-select-sm">
                                    <option value="">-- كل البرامج --</option>
                                    @foreach($programs ?? \App\Models\Programs::all() as $p)
                                        <option value="{{ $p->id }}">{{ $p->title }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">المجموعة</label>
                                <select id="smsFilterGroup" class="form-select form-select-sm">
                                    <option value="">-- كل المجموعات --</option>
                                    @foreach(\App\Models\Groups::all() as $g)
                                        <option value="{{ $g->id }}" data-pid="{{ $g->program_id }}">{{ $g->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3">
                                <button type="button" id="smsFetchBtn" class="btn btn-sm btn-primary w-100">
                                    <i class="fa fa-search"></i> جلب الطلاب
                                </button>
                            </div>
                        </div>
                        <div class="row g-2 mb-2">
                            <div class="col-md-4">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="smsFilterInactive" value="1">
                                    <label class="form-check-label fs-8 text-muted" for="smsFilterInactive">طلاب غير فعالين</label>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="smsFilterNoGroup" value="1">
                                    <label class="form-check-label fs-8 text-muted" for="smsFilterNoGroup">غير مسجلين بمجموعة</label>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="smsFilterPendingFin" value="1">
                                    <label class="form-check-label fs-8 text-muted" for="smsFilterPendingFin">طلبات مالية عالقة</label>
                                </div>
                            </div>
                        </div>
                        <div class="row g-2">
                            <div class="col-md-6">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="smsFilterHasTest" value="1">
                                    <label class="form-check-label fs-8 text-muted" for="smsFilterHasTest">مسجل في اختبار تحديد مستوى</label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="smsFilterHasScore" value="1">
                                    <label class="form-check-label fs-8 text-muted" for="smsFilterHasScore">لديه علامة ومستوى محدد في الاختبار</label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Compo (New Registrations) Filters -->
                    <div id="smsCompoFilters" style="display:none;">
                        <div class="row g-2 align-items-end mb-2">
                            <div class="col-md-3">
                                <label class="form-label">البرنامج</label>
                                <select id="smsCompoFilterProgram" class="form-select form-select-sm">
                                    <option value="">-- كل البرامج --</option>
                                    @foreach(\App\Models\Programs::all() as $p)
                                        <option value="{{ $p->id }}">{{ $p->title }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">نوع البرنامج</label>
                                <select id="smsCompoFilterProgramType" class="form-select form-select-sm">
                                    <option value="">الكل</option>
                                    <option value="Kids">Kids</option>
                                    <option value="Adults">Adults</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">الجنس</label>
                                <select id="smsCompoFilterGender" class="form-select form-select-sm">
                                    <option value="">الكل</option>
                                    <option value="Male">Male</option>
                                    <option value="Female">Female</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">الفرع</label>
                                <select id="smsCompoFilterBranch" class="form-select form-select-sm">
                                    <option value="">الكل</option>
                                    @foreach(\App\Models\Branch::where('status', 1)->get() as $b)
                                        <option value="{{ $b->name_en }}">{{ $b->name_ar }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="row g-2 align-items-end mb-2">
                            <div class="col-md-3">
                                <label class="form-label">حالة الفوترة</label>
                                <select id="smsCompoFilterInvoiced" class="form-select form-select-sm">
                                    <option value="">الكل</option>
                                    <option value="1">Invoiced</option>
                                    <option value="0">Not Invoiced</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">حالة التواصل</label>
                                <select id="smsCompoFilterContacted" class="form-select form-select-sm">
                                    <option value="">الكل</option>
                                    <option value="1">Contacted</option>
                                    <option value="0">Not Contacted</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">من تاريخ</label>
                                <input type="date" id="smsCompoFilterDateFrom" class="form-control form-control-sm">
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">إلى تاريخ</label>
                                <input type="date" id="smsCompoFilterDateTo" class="form-control form-control-sm">
                            </div>
                            <div class="col-md-2">
                                <button type="button" id="smsCompoFetchBtn" class="btn btn-sm btn-primary w-100">
                                    <i class="fa fa-search"></i> جلب الطلاب
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Shuttle Columns -->
                <div class="row g-3 mb-3">
                    <!-- Left: To Send (Pool) -->
                    <div class="col-md-6">
                        <div class="shuttle-column">
                            <div class="shuttle-head">
                                <div class="d-flex align-items-center justify-content-between mb-2">
                                    <h6 class="ttl text-primary"><i class="fa fa-paper-plane me-1"></i>المراد الإرسال لهم</h6>
                                    <span class="shuttle-count" id="smsPoolCount">0</span>
                                </div>
                                <input type="text" id="smsPoolSearch" class="form-control form-control-sm" placeholder="🔍 ابحث في القائمة...">
                            </div>
                            <ul class="shuttle-list" id="smsPoolList">
                                <li class="shuttle-empty" id="smsPoolEmpty">قم بالبحث لجلب الطلاب...</li>
                            </ul>
                        </div>
                    </div>

                    <!-- Right: Excluded (Basket) -->
                    <div class="col-md-6">
                        <div class="shuttle-column">
                            <div class="shuttle-head tint-warn">
                                <div class="d-flex align-items-center justify-content-between mb-2">
                                    <h6 class="ttl text-warning"><i class="fa fa-ban me-1"></i>المستبعدين</h6>
                                    <div class="d-flex align-items-center gap-2">
                                        <span class="shuttle-count tint-warn" id="smsBasketCount">0</span>
                                        <button type="button" id="smsClearBasketBtn" class="btn btn-sm btn-light-danger px-2 py-1" title="إرجاع الكل لقائمة الإرسال"><i class="fa fa-undo"></i></button>
                                    </div>
                                </div>
                                <small class="text-muted fs-8 d-block">⤵ اسحب الطلاب إلى هنا لاستثنائهم من الاستلام</small>
                            </div>
                            <ul class="shuttle-list" id="smsBasketList">
                                <li class="shuttle-empty" id="smsBasketEmpty">اسحب طالب إلى هنا لاستبعاده</li>
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- Message Compose -->
                <div class="row g-3">
                    <div class="col-md-12">
                        <label class="form-label fw-bold">رقم مخصص (اختياري)</label>
                        <input id="smsCustomNumber" class="form-control form-control-sm" placeholder="مثال: 97259xxxxxxx" />
                    </div>
                    <div class="col-md-12">
                        <label class="form-label fw-bold">نص الرسالة</label>
                        <textarea id="smsMessageText" class="form-control" rows="3" placeholder="اكتب نص الرسالة هنا..."></textarea>
                        <div class="mt-2 p-2 bg-light rounded fs-8 text-muted" dir="rtl">
                            <strong>متغيرات متاحة:</strong> 
                            <code>$name_ar</code> (الاسم عربي)، 
                            <code>$name_en</code> (الاسم إنجليزي)، 
                            <code>$program</code> (البرنامج)، 
                            <code>$group</code> (المجموعة)، 
                            <code>$score</code> (علامة اختبار المستوى)، 
                            <code>$assigned_level</code> (المستوى المستحق).
                        </div>
                    </div>
                </div>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                <button type="button" class="btn btn-primary" id="smsSubmitBtn">
                    <i class="fa fa-paper-plane me-1"></i> إرسال الرسائل
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Progress Box (Fixed Bottom Left) -->
<div id="smsProgressBox" style="display:none; position:fixed; bottom:20px; left:20px; width:350px; background:var(--bs-body-bg, #fff); border:1px solid var(--bs-border-color, #e1e3ea); border-radius:12px; box-shadow:0 10px 30px rgba(0,0,0,0.15); z-index:9999; padding:20px; border-top:4px solid #009ef7;">
    <div class="d-flex justify-content-between align-items-center mb-2">
        <h6 class="m-0 fw-bold"><i class="fa fa-paper-plane text-primary me-2"></i>جاري إرسال الرسائل...</h6>
        <button id="smsProgressCloseBtn" class="btn btn-sm btn-icon btn-light-danger" style="display:none;"><i class="fa fa-times"></i></button>
    </div>
    <div class="progress mb-3" style="height: 10px;">
        <div id="smsProgressBar" class="progress-bar progress-bar-striped progress-bar-animated bg-primary" role="progressbar" style="width: 0%"></div>
    </div>
    <div class="d-flex justify-content-between fs-8 fw-bold text-muted mb-2">
        <span>إجمالي: <span id="smsTotalCount">0</span></span>
        <span class="text-success">ناجح: <span id="smsSuccessCount">0</span></span>
        <span class="text-danger">فشل: <span id="smsFailCount">0</span></span>
    </div>
    <div id="smsProgressTime" class="fs-8 text-muted text-center mb-2">الوقت المتبقي: يحسب...</div>
    <div id="smsProgressDetails" class="fs-8 text-danger" style="display:none; max-height:100px; overflow-y:auto; border-top:1px solid var(--bs-border-color, #eee); padding-top:10px;"></div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Ensure SortableJS is loaded
    if (typeof window.Sortable === 'undefined') {
        let script = document.createElement('script');
        script.src = 'https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js';
        script.onload = initSmsShuttle;
        document.head.appendChild(script);
    } else {
        initSmsShuttle();
    }

    function initSmsShuttle() {
        const poolList = document.getElementById('smsPoolList');
        const basketList = document.getElementById('smsBasketList');
        const poolEmpty = document.getElementById('smsPoolEmpty');
        const basketEmpty = document.getElementById('smsBasketEmpty');
        let smsMode = 'student';

        function resetPoolAndBasket() {
            $(poolList).html('<li class="shuttle-empty" id="smsPoolEmpty">قم بالبحث لجلب الطلاب...</li>');
            $(basketList).html('<li class="shuttle-empty" id="smsBasketEmpty">اسحب طالب إلى هنا لاستبعاده</li>');
            updateCounts();
        }

        function fetchStudents(url, data, btn) {
            var originalHtml = btn.html();
            btn.html('<i class="fa fa-spinner fa-spin"></i>').prop('disabled', true);
            $(poolList).html('<li class="shuttle-empty">جاري التحميل...</li>');
            $(basketList).html('<li class="shuttle-empty" id="smsBasketEmpty">اسحب طالب إلى هنا لاستبعاده</li>');

            $.ajax({
                url: url,
                type: 'GET',
                data: data,
                success: function(res) {
                    btn.html(originalHtml).prop('disabled', false);
                    $(poolList).empty();

                    if (res.students && res.students.length > 0) {
                        res.students.forEach(s => {
                            let item = $('<li data-id="'+s.id+'"></li>');
                            let html = '<div><div class="fw-bold text-dark">' + s.name + '</div>';
                            html += '<div class="text-muted fs-8">' + s.mobile + '</div></div>';
                            if (s.score !== undefined || s.level !== undefined) {
                                html += '<div class="text-end fs-8 text-primary">العلامة: '+(s.score||'-')+'<br>المستوى: '+(s.level||'-')+'</div>';
                            }
                            item.html(html);
                            $(poolList).append(item);
                        });
                    } else {
                        $(poolList).append('<li class="shuttle-empty text-danger">لا توجد نتائج</li>');
                    }
                    updateCounts();
                },
                error: function() {
                    btn.html(originalHtml).prop('disabled', false);
                    $(poolList).html('<li class="shuttle-empty text-danger">خطأ في جلب البيانات</li>');
                    updateCounts();
                }
            });
        }

        // Open modal in "new registrations" (compo) mode
        window.openComboSmsModal = function() {
            smsMode = 'compo';
            $('#smsNormalFilters').hide();
            $('#smsCompoFilters').show();
            $('#smsModalTitle').html('<i class="fa fa-envelope me-2"></i>إرسال رسائل SMS - طلبات التسجيل الجديدة');
            resetPoolAndBasket();
            var modalEl = document.getElementById('smsShuttleModal');
            bootstrap.Modal.getOrCreateInstance(modalEl).show();
        };

        // Reset back to normal mode when the modal is closed
        $('#smsShuttleModal').on('hidden.bs.modal', function() {
            smsMode = 'student';
            $('#smsCompoFilters').hide();
            $('#smsCompoFilters select, #smsCompoFilters input').val('');
            $('#smsNormalFilters').show();
            $('#smsModalTitle').html('<i class="fa fa-envelope me-2"></i>إرسال رسائل SMS');
            $('#smsCustomNumber').val('');
            $('#smsMessageText').val('');
            resetPoolAndBasket();
        });

        let sortablePool = new Sortable(poolList, {
            group: 'sms-shuttle',
            animation: 150,
            filter: '.shuttle-empty',
            onSort: updateCounts
        });

        let sortableBasket = new Sortable(basketList, {
            group: 'sms-shuttle',
            animation: 150,
            filter: '.shuttle-empty',
            onSort: updateCounts
        });

        function updateCounts() {
            const pCount = $(poolList).children('li:not(.shuttle-empty):visible').length;
            const bCount = $(basketList).children('li:not(.shuttle-empty)').length;
            $('#smsPoolCount').text(pCount);
            $('#smsBasketCount').text(bCount);

            $(poolEmpty).toggle($(poolList).children('li:not(.shuttle-empty)').length === 0);
            $(basketEmpty).toggle(bCount === 0);
        }

        // Link group select to program select visually
        $('#smsFilterProgram').on('change', function() {
            var pid = $(this).val();
            $('#smsFilterGroup option').each(function() {
                var gpid = $(this).data('pid');
                if (!pid || pid == gpid || !$(this).val()) {
                    $(this).show();
                } else {
                    $(this).hide();
                }
            });
            $('#smsFilterGroup').val('');
        });

        // Fetch students (normal / students mode)
        $('#smsFetchBtn').on('click', function() {
            fetchStudents('{{ route("admin.sms.shuttle.students") }}', {
                program_type: $('#smsFilterProgramType').val(),
                program_id: $('#smsFilterProgram').val(),
                group_id: $('#smsFilterGroup').val(),
                has_placement_test: $('#smsFilterHasTest').is(':checked') ? 1 : 0,
                has_score: $('#smsFilterHasScore').is(':checked') ? 1 : 0,
                inactive: $('#smsFilterInactive').is(':checked') ? 1 : 0,
                no_group: $('#smsFilterNoGroup').is(':checked') ? 1 : 0,
                pending_fin: $('#smsFilterPendingFin').is(':checked') ? 1 : 0
            }, $(this));
        });

        // Fetch students (new-registrations / compo mode)
        $('#smsCompoFetchBtn').on('click', function() {
            fetchStudents('{{ route("admin.standalone_registrations.sms_students") }}', {
                program_id: $('#smsCompoFilterProgram').val(),
                program_type: $('#smsCompoFilterProgramType').val(),
                gender: $('#smsCompoFilterGender').val(),
                branch: $('#smsCompoFilterBranch').val(),
                is_invoiced: $('#smsCompoFilterInvoiced').val(),
                is_contacted: $('#smsCompoFilterContacted').val(),
                date_from: $('#smsCompoFilterDateFrom').val(),
                date_to: $('#smsCompoFilterDateTo').val()
            }, $(this));
        });

        // Search in pool
        $('#smsPoolSearch').on('input', function() {
            var term = $(this).val().toLowerCase();
            $(poolList).children('li:not(.shuttle-empty)').each(function() {
                var text = $(this).text().toLowerCase();
                $(this).toggle(text.indexOf(term) > -1);
            });
            updateCounts();
        });

        // Clear Basket
        $('#smsClearBasketBtn').on('click', function() {
            $(basketList).children('li:not(.shuttle-empty)').each(function() {
                $(poolList).append(this);
            });
            updateCounts();
            $('#smsPoolSearch').trigger('input'); // re-apply search filter
        });

        // Check Balance
        $('#smsCheckBalanceBtn').on('click', function() {
            var btn = $(this);
            var originalHtml = btn.html();
            btn.html('<i class="fa fa-spinner fa-spin"></i>').prop('disabled', true);
            $.get('{{ route("admin.sms.balance") }}', function(res) {
                btn.html(originalHtml).prop('disabled', false);
                if(res.success) {
                    Swal.fire({title: 'الرصيد المتاح', text: res.balance + ' رسالة', icon: 'info'});
                } else {
                    Swal.fire('خطأ', res.message, 'error');
                }
            }).fail(function() {
                btn.html(originalHtml).prop('disabled', false);
                Swal.fire('خطأ', 'تعذر الاتصال بالخادم للاستعلام عن الرصيد', 'error');
            });
        });

        // Submit SMS
        $('#smsSubmitBtn').on('click', function() {
            var note = $('#smsMessageText').val().trim();
            var customNumber = $('#smsCustomNumber').val().trim();
            
            var studentIds = [];
            // Get all ids from the POOL list (The ones we want to send to)
            $(poolList).children('li:not(.shuttle-empty)').each(function() {
                studentIds.push($(this).data('id'));
            });

            if (!note && !customNumber) {
                Swal.fire('تنبيه', 'يجب إدخال نص الرسالة أو رقم مخصص على الأقل', 'warning');
                return;
            }

            if (studentIds.length === 0 && !customNumber) {
                Swal.fire('تنبيه', 'لا يوجد طلاب للإرسال لهم (يرجى جلب طلاب أو إدخال رقم مخصص)', 'warning');
                return;
            }

            var btn = $(this);
            var originalHtml = btn.html();
            btn.html('<i class="fa fa-spinner fa-spin"></i> جاري الإرسال...').prop('disabled', true);
            $('#smsShuttleModal').modal('hide');
            btn.html(originalHtml).prop('disabled', false);

            $('#smsProgressBox').fadeIn();
            $('#smsProgressCloseBtn').hide();
            $('#smsProgressDetails').hide().empty();
            $('#smsProgressBar').css('width', '0%').removeClass('bg-success bg-danger').addClass('bg-primary progress-bar-animated');
            
            var totalTasks = studentIds.length;
            if (customNumber) totalTasks++;
            
            $('#smsTotalCount').text(totalTasks);
            $('#smsSuccessCount').text(0);
            $('#smsFailCount').text(0);
            
            var successCount = 0;
            var failCount = 0;
            var currentIndex = 0;
            var errors = [];
            var startTime = new Date().getTime();

            // Prepare tasks
            var tasks = [];
            if (customNumber) {
                tasks.push({ customNumber: customNumber });
            }
            studentIds.forEach(function(sid) {
                tasks.push({ studentIds: [sid] });
            });

            function processNextTask() {
                if (currentIndex >= tasks.length) {
                    finishProcess();
                    return;
                }

                var task = tasks[currentIndex];
                var dataPayload = { 
                    _token: '{{ csrf_token() }}', 
                    note: note 
                };
                if (task.customNumber) dataPayload.customNumber = task.customNumber;
                if (task.studentIds) {
                    dataPayload.studentIds = task.studentIds;
                    dataPayload.source = smsMode;
                }

                $.ajax({
                    type: 'POST',
                    url: '{{ route("send.admin.sms") }}',
                    data: dataPayload,
                    success: function(res) {
                        successCount++;
                        updateProgress();
                        currentIndex++;
                        processNextTask();
                    },
                    error: function(err) {
                        failCount++;
                        var errorMsg = err.responseJSON && err.responseJSON.message ? err.responseJSON.message : 'خطأ غير معروف';
                        var target = task.customNumber ? task.customNumber : 'طالب ID: ' + task.studentIds[0];
                        errors.push(target + ': ' + errorMsg);
                        updateProgress();
                        currentIndex++;
                        processNextTask();
                    }
                });
            }

            function updateProgress() {
                var processed = successCount + failCount;
                var percent = Math.round((processed / totalTasks) * 100);
                $('#smsProgressBar').css('width', percent + '%');
                $('#smsSuccessCount').text(successCount);
                $('#smsFailCount').text(failCount);

                var now = new Date().getTime();
                var elapsed = (now - startTime) / 1000; // in seconds
                if (processed > 0) {
                    var avgTimePerTask = elapsed / processed;
                    var remainingTasks = totalTasks - processed;
                    var remainingSeconds = Math.round(avgTimePerTask * remainingTasks);
                    $('#smsProgressTime').text('الوقت المتبقي المتوقع: ' + remainingSeconds + ' ثانية');
                }
            }

            function finishProcess() {
                $('#smsProgressBar').removeClass('progress-bar-animated');
                $('#smsProgressTime').text('تم الانتهاء.');
                $('#smsProgressCloseBtn').show();
                if (failCount > 0) {
                    $('#smsProgressBar').removeClass('bg-primary').addClass('bg-warning');
                    $('#smsProgressDetails').show().html('<strong>الأخطاء:</strong><br>' + errors.join('<br>'));
                } else {
                    $('#smsProgressBar').removeClass('bg-primary').addClass('bg-success');
                }
                
                // Clear the pool inside modal
                $(poolList).empty();
                $('#smsPoolCount').text('0');
                $(poolEmpty).show();
                $('#smsCustomNumber').val('');
            }

            // Close box event
            $('#smsProgressCloseBtn').off('click').on('click', function() {
                $('#smsProgressBox').fadeOut();
            });

            // Start sending
            processNextTask();
        });
    }
});
</script>
