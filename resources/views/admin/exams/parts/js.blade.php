{{-- All JS for the exam builder form (add/edit). Included via @section('js') in add.blade.php /
     edit.blade.php so it loads AFTER jQuery (which the layout only includes near the end of
     <body>, after @yield('page-content')) — placing this inline inside page-content instead
     would run before jQuery exists and silently fail. --}}
<script>
    $(document).ready(function () {

        // ── Program -> Group cascading select (Group Exams only) ──
        @if($category === 'group')
        (function () {
            var groupsByProgramUrl = "{{ route('group_exams.groups_by_program') }}";
            var currentGroupId = "{{ old('group_id', $info->group_id ?? '') }}";

            $('#exam_program_id').on('change', function () {
                var programId = $(this).val();
                var $groupSelect = $('#exam_group_id');

                $groupSelect.html('<option value="">جاري التحميل...</option>').trigger('change');

                if (!programId) {
                    $groupSelect.html('<option value="">اختر برنامجاً أولاً...</option>').trigger('change');
                    return;
                }

                $.ajax({
                    url: groupsByProgramUrl,
                    type: 'POST',
                    data: { program_id: programId, _token: '{{ csrf_token() }}' },
                    success: function (groups) {
                        var options = '<option value="">اختر مجموعة...</option>';
                        if (groups.length === 0) {
                            options = '<option value="">لا توجد مجموعات فعالة لهذا البرنامج</option>';
                        } else {
                            groups.forEach(function (g) {
                                var selected = (String(g.id) === String(currentGroupId)) ? 'selected' : '';
                                options += '<option value="' + g.id + '" ' + selected + '>' + g.name + '</option>';
                            });
                        }
                        $groupSelect.html(options).trigger('change');
                    },
                    error: function () {
                        $groupSelect.html('<option value="">تعذر تحميل المجموعات</option>').trigger('change');
                    }
                });
            });
        })();
        @endif

        // ── Live question filter (search text && type && difficulty, all combined with AND)
        //    + select-all/clear-all + live selected-questions summary (count / by type / total marks) ──
        (function () {
            var $rows = $('.question-row');
            var typeLabels = { mcq: 'اختيار من متعدد', true_false: 'صح/خطأ', text: 'إجابة نصية', voice: 'إجابة صوتية' };
            var typeClasses = { mcq: 'primary', true_false: 'info', text: 'dark', voice: 'danger' };

            function applyQuestionFilter() {
                var keyword = $('#question_search_filter').val().toLowerCase().trim();
                var type = $('#question_type_filter').val();
                var difficulty = $('#question_difficulty_filter').val();
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

                $('#question_filter_count').text(visible + ' من ' + $rows.length + ' سؤال');
            }

            function updateSelectionSummary() {
                var count = 0, totalMarks = 0, byType = {};

                $rows.find('.question-checkbox:checked').each(function () {
                    var $row = $(this).closest('.question-row');
                    var type = $row.data('type');
                    count++;
                    totalMarks += parseFloat($row.data('marks')) || 0;
                    byType[type] = (byType[type] || 0) + 1;
                });

                $('#summary_selected_count').text(count);
                $('#summary_total_marks').text(totalMarks % 1 === 0 ? totalMarks : totalMarks.toFixed(1));

                var typeHtml = '';
                Object.keys(typeLabels).forEach(function (type) {
                    if (byType[type]) {
                        typeHtml += '<span class="badge badge-light-' + typeClasses[type] + '">' + typeLabels[type] + ': ' + byType[type] + '</span>';
                    }
                });
                $('#summary_by_type').html(typeHtml || '<span class="text-muted">لا يوجد</span>');
            }

            $('#question_search_filter').on('keyup input change', applyQuestionFilter);
            $('#question_type_filter').on('change', applyQuestionFilter);
            $('#question_difficulty_filter').on('change', applyQuestionFilter);
            applyQuestionFilter();

            $(document).on('change', '.question-checkbox', updateSelectionSummary);
            updateSelectionSummary();

            // selects/deselects only the rows currently visible under the active filter
            $('#select_all_questions_btn').on('click', function () {
                $rows.filter(':visible').find('.question-checkbox').prop('checked', true);
                updateSelectionSummary();
            });

            $('#clear_all_questions_btn').on('click', function () {
                $rows.find('.question-checkbox').prop('checked', false);
                updateSelectionSummary();
            });
        })();

        // ── Tabs: keep generation_mode radio in sync with the visible tab ──
        document.querySelectorAll('a[data-bs-toggle="tab"]').forEach(function (tab) {
            tab.addEventListener('shown.bs.tab', function (e) {
                var target = e.target.getAttribute('href');
                if (target === '#auto_tab') {
                    document.querySelector('.auto-radio').checked = true;
                } else if (target === '#manual_tab') {
                    document.querySelector('.manual-radio').checked = true;
                }
                // '#new_questions_tab' is additive and doesn't touch generation_mode
            });
        });

        // ── "Create new questions" repeater ──
        (function () {
            var newRowIndex = 1;

            function toggleNewOptions($row) {
                var type = $row.find('.new-row-type').val();
                $row.find('.new_mcq_options').toggleClass('d-none', type !== 'mcq');
                $row.find('.new_tf_options').toggleClass('d-none', type !== 'true_false');
            }

            function renumberNewRows() {
                document.querySelectorAll('.new_question_row').forEach(function (row, i) {
                    row.querySelector('.row-number').textContent = 'سؤال جديد ' + (i + 1);
                });
            }

            toggleNewOptions($('.new_question_row[data-index="0"]'));

            $(document).on('change', '.new-row-type', function () {
                toggleNewOptions($(this).closest('.new_question_row'));
            });

            $('#add_new_question_btn').on('click', function () {
                var $template = $('.new_questions_repeater .new_question_row').first();
                var $clone = $template.clone();
                var idx = newRowIndex++;

                $clone.find('[name]').each(function () {
                    var name = $(this).attr('name').replace(/new_questions\[\d+\]/, 'new_questions[' + idx + ']');
                    $(this).attr('name', name);
                });
                $clone.attr('data-index', idx);

                $clone.find('textarea').val('');
                $clone.find('input[type="text"]').val('');
                $clone.find('input[type="number"]').val(1);
                $clone.find('select.new-row-type').val('mcq');
                $clone.find('select').not('.new-row-type').prop('selectedIndex', 0);
                $clone.find('input[type="radio"]').prop('checked', false);
                $clone.find('input[type="radio"][value="0"], input[type="radio"][value="true"]').prop('checked', true);

                $('.new_questions_repeater').append($clone);
                toggleNewOptions($clone);
                renumberNewRows();
            });

            $(document).on('click', '.remove-new-row', function () {
                if ($('.new_question_row').length <= 1) {
                    $(this).closest('.new_question_row').find('textarea, input[type="text"]').val('');
                    return;
                }
                $(this).closest('.new_question_row').fadeOut(200, function () {
                    $(this).remove();
                    renumberNewRows();
                });
            });
        })();
    });
</script>
