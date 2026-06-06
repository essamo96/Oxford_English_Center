@extends('frontend.layouts.master')
@section('title', 'Certificates')

@section('css')
<link rel="stylesheet" href="{{ url('assets/css/certificates.css') }}">
@endsection

@section('content')

{{-- ══════════════════════════ INNER BANNER ══ --}}
<div class="inner-page-banner-area"
     style="background-image:url('{{ url('assets/oxford/img/banner/contact.jpg') }}');">
    <div class="container">
        <div class="pagination-area">
            <h1>Certificates</h1>
            <ul>
                <li><a href="{{ url('/') }}">Home</a> - </li>
                <li>Certificates</li>
            </ul>
        </div>
    </div>
</div>

{{-- ══════════════════════════ SEARCH HERO ══ --}}
<div class="cert-hero">
    <div class="cert-hero__ring"></div>
    <div class="cert-hero__ring"></div>
    <div class="cert-hero__ring"></div>

    <div class="cert-hero__inner">

        <div class="cert-hero__eyebrow">
            <span class="cert-dot"></span>
            Certificate Verification
        </div>

        <h1>Verify Your <em>Certificate</em></h1>
        <p class="cert-hero__sub">
            Enter your unique certificate code to instantly retrieve<br>
            and download your official Oxford certificate.
        </p>

        <div class="cert-search-wrap">
            <div class="cert-search-box" id="certSearchBox">
                <i class="bi bi-patch-check cert-search-icon"></i>
                <div class="cert-search-spinner"></div>
                <input type="text"
                       id="certCode"
                       class="cert-search-input"
                       placeholder="Enter your certificate code…"
                       autocomplete="off"
                       spellcheck="false">
                <button class="cert-search-btn" id="certSearchBtn">
                    <i class="bi bi-search"></i>
                    <span>Search</span>
                </button>
            </div>

            <p class="cert-search-hint">
                <i class="bi bi-info-circle"></i>
                The code is found on your physical certificate or registration email.
            </p>
        </div>

    </div>
</div>

{{-- ══════════════════════════ RESULTS AREA ══ --}}
<div class="cert-section">
    <div class="container">

        {{-- NOT FOUND STATE --}}
        <div class="cert-not-found" id="certNotFound">

            <div class="cert-not-found__illustration">
                <i class="bi bi-file-earmark-x"></i>
            </div>

            <div class="cert-not-found__code" id="certNotFoundCode">
                <i class="bi bi-hash"></i>
                <span id="certNotFoundCodeText"></span>
            </div>

            <h3>No Certificate Found</h3>
            <p>
                We couldn't find a certificate matching this code.
                Please check the code and try again.
            </p>

            <div class="cert-not-found__tips">
                <div class="cert-not-found__tip">
                    <i class="bi bi-exclamation-triangle"></i>
                    Make sure there are no extra spaces before or after the code.
                </div>
                <div class="cert-not-found__tip">
                    <i class="bi bi-exclamation-triangle"></i>
                    The code is case-sensitive — check uppercase and lowercase letters.
                </div>
                <div class="cert-not-found__tip">
                    <i class="bi bi-exclamation-triangle"></i>
                    Contact the academy if you believe there is an error.
                </div>
            </div>

            <button class="cert-retry-btn" id="certRetryBtn">
                <i class="bi bi-arrow-counterclockwise"></i>
                Try Again
            </button>

        </div>

        {{-- SUCCESS RESULT --}}
        <div class="cert-result" id="certResult">

            {{-- Student Info Card --}}
            <div class="cert-student-card">
                <img id="certStudentImg"
                     src="{{ url('assets/oxford/img/favicon.ico') }}"
                     alt="Student"
                     class="cert-student-card__avatar"
                     onerror="this.style.display='none';document.getElementById('certAvatarFallback').style.display='flex';">
                <div class="cert-student-card__avatar-fallback"
                     id="certAvatarFallback"
                     style="display:none;">
                    <i class="bi bi-person"></i>
                </div>

                <div class="cert-student-card__info">
                    <h4 class="cert-student-card__name" id="certStudentName">—</h4>
                    <p class="cert-student-card__group">
                        <i class="bi bi-collection"></i>
                        <span id="certStudentGroup">—</span>
                    </p>
                    <div class="cert-stars">
                        <i class="bi bi-star-fill"></i>
                        <i class="bi bi-star-fill"></i>
                        <i class="bi bi-star-fill"></i>
                        <i class="bi bi-star-fill"></i>
                        <i class="bi bi-star-fill"></i>
                    </div>
                    <span class="cert-badge-verified">
                        <i class="bi bi-shield-check"></i>
                        Verified Certificate
                    </span>
                </div>

                <a id="certDownloadBtn"
                   href="#"
                   download="certificate.pdf"
                   class="cert-student-card__download"
                   style="display:none;">
                    <i class="bi bi-download"></i>
                    Download PDF
                </a>
            </div>

            {{-- PDF Viewer --}}
            <div class="cert-pdf-wrap">
                <div id="certPdfContainer"></div>
            </div>

        </div>

    </div>
</div>

@endsection

@section('js')
<script>
(function () {

    var searchBox  = document.getElementById('certSearchBox');
    var searchBtn  = document.getElementById('certSearchBtn');
    var codeInput  = document.getElementById('certCode');
    var notFound   = document.getElementById('certNotFound');
    var resultEl   = document.getElementById('certResult');
    var retryBtn   = document.getElementById('certRetryBtn');

    /* ── helpers ── */
    function setLoading(on) {
        searchBox.classList.toggle('is-loading', on);
        searchBtn.disabled = on;
        codeInput.disabled = on;
    }

    function showError() {
        searchBox.classList.add('is-error');
        searchBox.addEventListener('animationend', function () {
            searchBox.classList.remove('is-error');
        }, { once: true });
    }

    function reset() {
        notFound.style.display  = 'none';
        resultEl.style.display  = 'none';
        codeInput.value = '';
        codeInput.focus();
        window.scrollTo({ top: document.getElementById('certSearchBox').getBoundingClientRect().top + window.scrollY - 120, behavior: 'smooth' });
    }

    /* ── search ── */
    function doSearch() {
        var code = codeInput.value.trim();
        if (!code) {
            showError();
            codeInput.focus();
            return;
        }

        setLoading(true);
        notFound.style.display = 'none';
        resultEl.style.display = 'none';

        $.ajax({
            type: 'POST',
            url:  '{{ route("certificates.student") }}',
            data: { code: code, _token: '{{ csrf_token() }}' },
            success: function (response) {
                setLoading(false);

                if (!response || response.error) {
                    showError();
                    document.getElementById('certNotFoundCodeText').textContent = code;
                    notFound.style.display = 'block';
                    notFound.scrollIntoView({ behavior: 'smooth', block: 'center' });
                    return;
                }

                /* populate student card */
                var s = response.student;
                document.getElementById('certStudentName').textContent  = s.student.name  || '—';
                document.getElementById('certStudentGroup').textContent = s.group && s.group.title ? s.group.title : '—';

                var imgEl = document.getElementById('certStudentImg');
                var fbEl  = document.getElementById('certAvatarFallback');
                if (s.student.image) {
                    imgEl.src           = s.student.image;
                    imgEl.style.display = '';
                    fbEl.style.display  = 'none';
                } else {
                    imgEl.style.display = 'none';
                    fbEl.style.display  = 'flex';
                }

                /* PDF */
                document.getElementById('certPdfContainer').innerHTML =
                    '<iframe src="data:application/pdf;base64,' + response.pdf + '" style="width:100%;height:1100px;border:none;display:block;"></iframe>';

                var dlBtn = document.getElementById('certDownloadBtn');
                dlBtn.href         = 'data:application/pdf;base64,' + response.pdf;
                dlBtn.style.display = '';

                resultEl.style.display = 'block';
                resultEl.scrollIntoView({ behavior: 'smooth', block: 'start' });
            },
            error: function () {
                setLoading(false);
                showError();
                document.getElementById('certNotFoundCodeText').textContent = code;
                notFound.style.display = 'block';
                notFound.scrollIntoView({ behavior: 'smooth', block: 'center' });
            }
        });
    }

    /* ── events ── */
    searchBtn.addEventListener('click', doSearch);

    codeInput.addEventListener('keydown', function (e) {
        if (e.key === 'Enter') doSearch();
    });

    retryBtn.addEventListener('click', reset);

})();
</script>
@endsection
