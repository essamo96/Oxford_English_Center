<script>
function sendMaseageToGroupe(id) {
    window.EmailComposer.send({
        type: 'single',
        id: id,
        url: '{{ route('groups.send.message') }}',
        title: '<span class="text-primary fw-bold">إرسال بريد إلكتروني لطلاب المجموعة</span>'
    });
}
</script>