@php
    $hasStatusColumn = false;

    if (!empty($columns) && is_array($columns)) {
        foreach ($columns as $col) {
            if ((isset($col['data']) && $col['data'] === 'status') ||
                (isset($col['name']) && $col['name'] === 'status')) {
                $hasStatusColumn = true;
                break;
            }
        }
    }
@endphp

<script>
$(document).ready(function() {
    const dataTableLanguageUrl = "{{ url('assets/plugins/datatables/ar.json') }}";

    var tableSelector = (typeof tableId !== 'undefined' && tableId) ? '#' + tableId : '#kt_table';

    // تهيئة الفلاتر إذا موجودة
    if (typeof filterFields !== 'undefined' && filterFields.length > 0) {
        filterFields.forEach(function(field) {
            var $el = $(field);
            if (($el.is("input[type='date']") || $el.hasClass('date-picker')) && $el.attr('id') !== 'generalSearch') {
                $el.flatpickr({ dateFormat: 'Y-m-d' });
            }
        });
    }

    var searchDebounceTimer;
    function debounceTableDraw() {
        clearTimeout(searchDebounceTimer);
        searchDebounceTimer = setTimeout(function() { table.draw(); }, 400);
    }

    table = $(tableSelector).DataTable({
        responsive: true,
        ordering: false,
        processing: true,
        pageLength: (typeof customPageLength !== 'undefined') ? customPageLength : 10,
        bLengthChange: true,
        bFilter: false,
        serverSide: true,
        ajax: {
            url: (typeof customAjaxUrl !== 'undefined' && customAjaxUrl) ? customAjaxUrl : '{{ route($active_menu . '.list') }}',
            data: function(d) {
                if (typeof filterFields !== 'undefined') {
                    filterFields.forEach(function(field) {
                        let $el = $(field);
                        let key = $el.attr('name') || $el.attr('id');
                        let val = $el.val();
                        
                        // Fix for checkboxes: only send value if checked
                        if ($el.is(':checkbox') && !$el.is(':checked')) {
                            val = null;
                        }
                        
                        d[key] = val;
                    });
                }
            }
        },
        columns: columns,
        language: { url: dataTableLanguageUrl },
        "sDom": `<'table-responsive'tr>
                 <'row align-items-center justify-content-center'<'col-md-5 col-sm-12'i><'col-md-7 col-sm-12'p>>`,
        createdRow: function(row, data, dataIndex) {
            $(row).find('td:eq(1)').addClass('d-flex align-items-center');
        },
        "fnDrawCallback": function(oSettings) {
            if (typeof KTMenu !== 'undefined' && KTMenu.createInstances) {
                KTMenu.createInstances();
            }
            if (typeof KTApp !== 'undefined' && KTApp.initTooltips) {
                KTApp.initTooltips();
            }
            $('.tooltips').tooltip();
        }
    });

    if (typeof filterFields !== 'undefined') {
        $(filterFields.join(',')).each(function() {
            var $el = $(this);
            if ($el.is("input[type='text']") || $el.attr('id') === 'generalSearch') {
                $el.on('input', debounceTableDraw);
            } else {
                $el.on('change', function() { table.draw(); });
            }
        });
    }
    var hasStatusColumn = columns.some(col => col.data === 'status');

    @include('admin.layout.masterLayouts.delete')

    if (hasStatusColumn) {
        @include('admin.layout.masterLayouts.status')
    }
});
</script>
