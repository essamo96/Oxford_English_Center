@extends('frontend.layouts.master')
@section('title', 'Registration | Oxford Language Center')
@section('content')

    <!-- Custom Wizard Styles -->
    <link rel="stylesheet" href="{{ asset('assets/oxford/css/wizard.css') }}">
    <style>
        /* Fix for Navbar Z-Index */
        .app-header,
        header,
        nav {
            z-index: 1000 !important;
        }

        .registration-page-wrapper {
            position: relative;
            z-index: 1;
        }
    </style>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="{{ asset('js/particles-hero.js') }}"></script>
    <script src="{{ asset('js/book-course.js') }}"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">

    <div class="registration-page-wrapper">
        <div class="entry-screen">
            <!-- Hero Section -->
            <div class="book-course-hero">
                <canvas id="hero-particles"></canvas>
                <div class="hero-content" data-aos="fade-up">
                    <h1>Registration</h1>
                    <p>Start your English language learning journey with us today. Choose your program to begin.</p>
                </div>
            </div>

            <div id="program-selection-container" style="display: none;">
                <div class="program-selection-wrapper">
                    <!-- Adult Program -->
                    <div class="program-option animate-slide-right" onclick="handleProgramSelection('adult')">
                        <div class="program-card">
                            <img src="{{ asset('assets/oxford/img/banner/Gemini.png') }}" alt="Adult Program">
                            <div class="overlay-info">
                                <h3 class="program-name">ADULT PROGRAM</h3>
                                <div class="qr-circle" onclick="showProgramQRCode('adult', event)"><i class="bi bi-qr-code-scan"></i></div>
                            </div>
                        </div>
                    </div>

                    <!-- Kids Program -->
                    <div class="program-option animate-slide-left" onclick="handleProgramSelection('kids')">
                        <div class="program-card">
                            <img src="{{ asset('assets/oxford/img/banner/WhatsApp Image 2026-05-08 at 10.54.15 PM.jpeg') }}"
                                alt="Kids Program">
                            <div class="overlay-info">
                                <h3 class="program-name">KIDS PROGRAM</h3>
                                <div class="qr-circle" onclick="showProgramQRCode('kids', event)"><i class="bi bi-qr-code-scan"></i></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Wizard Card (Hidden initially) -->
        <div class="wizard-main-card" id="wizard-card">
            <!-- Progress Tracker -->
            <div class="wizard-header-steps">
                <div class="step-indicator active" data-step-nav="1">
                    <div class="step-number">1</div>
                    <div class="step-label">Profile</div>
                </div>
                <div class="step-indicator" data-step-nav="2">
                    <div class="step-number">2</div>
                    <div class="step-label">Details</div>
                </div>
                <div class="step-indicator" data-step-nav="3">
                    <div class="step-number">3</div>
                    <div class="step-label">Placement</div>
                </div>
                <div class="step-indicator" data-step-nav="4">
                    <div class="step-number">4</div>
                    <div class="step-label">Payment</div>
                </div>
                <div class="step-indicator" data-step-nav="5">
                    <div class="step-number">5</div>
                    <div class="step-label">Finalize</div>
                </div>
            </div>

            <form id="registration-wizard-form" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="program_type" id="program_type_hidden">

                <div class="wizard-body">
                    <!-- Step 1: Basic Information -->
                    <div class="step-pane active" id="pane-1">
                        <h4 class="mb-4 step-heading">
                            <i class="bi bi-person-circle me-3"></i>
                            Step 1: Personal Information
                        </h4>
                        <div class="row">
                            <div class="col-md-6 mb-4">
                                <label class="form-label">Full Name (Arabic) (الاسم الرباعي بالعربية) *</label>
                                <input type="text" name="name" class="form-control" placeholder="الاسم الرباعي بالعربية"
                                    required pattern="[\u0600-\u06FF\s]+" title="Please enter name in Arabic only">
                            </div>
                            <div class="col-md-6 mb-4">
                                <label class="form-label">Full Name (English) (الاسم الرباعي بالإنجليزية) *</label>
                                <input type="text" name="name_en" class="form-control" placeholder="English Quad Name"
                                    required>
                            </div>
                            <div class="col-md-6 mb-4">
                                <label class="form-label">Phone Number (رقم الجوال) *</label>
                                <input type="text" name="mobile" class="form-control" placeholder="05x xxxx xxx"
                                    required>
                            </div>
                            <div class="col-md-6 mb-4">
                                <label class="form-label">Email Address (البريد الإلكتروني) *</label>
                                <input type="email" name="email" class="form-control" placeholder="example@gmail.com"
                                    required>
                            </div>
                            <div class="col-md-6 mb-4">
                                <label class="form-label">Date of Birth (تاريخ الميلاد) *</label>
                                <input type="date" name="dob" class="form-control" required>
                            </div>
                            <div class="col-md-6 mb-4" id="gender-group">
                                <label class="form-label">Gender (الجنس) *</label>
                                <select name="gender" class="form-control" required>
                                    <option value="">Select Gender</option>
                                    <option value="1">Male</option>
                                    <option value="2">Female</option>
                                </select>
                            </div>
                            <div class="col-md-12 mb-4">
                                <label class="form-label">Address (العنوان) *</label>
                                <textarea name="address" class="form-control" rows="2" placeholder="Full Address Details" required></textarea>
                            </div>
                        </div>
                    </div>

                    <!-- Step 2: Educational Details (Adult) OR Parent Details (Kids) -->
                    <div class="step-pane" id="pane-2">
                        <div id="adult-fields" style="display: none;">
                            <h4 class="mb-4 step-heading">
                                <i class="bi bi-mortarboard me-3"></i>
                                Step 2: Educational Information
                            </h4>
                            <div class="row">
                                <div class="col-md-6 mb-4">
                                    <label class="form-label">Major of Study (التخصص الدراسي) *</label>
                                    <input type="text" name="major" class="form-control"
                                        placeholder="e.g. English Literature">
                                </div>
                                </div>
                            </div>

                            <div id="kids-fields" style="display: none;">
                                <h4 class="mb-4 step-heading">
                                    <i class="bi bi-people me-3"></i>
                                    Step 2: Parent / Guardian Information
                                </h4>
                            <div class="row">
                                <div class="col-md-6 mb-4">
                                    <label class="form-label">Parent Full Name (اسم ولي الأمر) *</label>
                                    <input type="text" name="parent_name" class="form-control"
                                        placeholder="Full Name">
                                </div>
                                <div class="col-md-6 mb-4">
                                    <label class="form-label">Parent Phone (رقم جوال ولي الأمر) *</label>
                                    <input type="text" name="parent_phone" class="form-control"
                                        placeholder="Parent Contact Number">
                                </div>
                                <div class="col-md-6 mb-4">
                                    <label class="form-label">Parent Email (البريد الإلكتروني لولي الأمر)</label>
                                    <input type="email" name="parent_email" class="form-control"
                                        placeholder="Parent Email">
                                </div>
                                <div class="col-md-6 mb-4">
                                    <label class="form-label">Relationship (صلة القرابة) *</label>
                                    <input type="text" name="parent_relationship" class="form-control" placeholder="e.g. Father, Mother">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Step 3: Placement Test Scheduling -->
                    <div class="step-pane" id="pane-3">
                        <div class="text-center mb-5">
                            <h3 class="fw-bold step-heading justify-content-center">
                                <i class="bi bi-calendar-check me-3"></i>
                                Step 3: Placement Test
                            </h3>
                            <p class="text-muted">Determining your current level helps us place you in the right group.
                            </p>
                        </div>

                        <div class="test-options-grid mb-5">
                            <div class="test-btn" id="test-yes" onclick="selectTestChoice('yes')">
                                <i class="fa fa-calendar-check"></i>
                                <h5>Yes, I need a test</h5>
                                <small>Fee: 100 ILS</small>
                            </div>
                            <div class="test-btn" id="test-no" onclick="selectTestChoice('no')">
                                <i class="fa fa-times-circle"></i>
                                <h5>No, skip test</h5>
                                <small>Start from Beginner level</small>
                            </div>
                        </div>

                        <input type="hidden" name="take_test" id="take_test_hidden">

                        <div id="skip-test-level-selection" class="mt-5" style="display: none;">
                            <div class="alert alert-info shadow-sm mb-4 border-0">
                                <i class="fa fa-info-circle me-2"></i>
                                Please select your current English level since you are skipping the test.
                                (يرجى تحديد مستواك الحالي في اللغة الإنجليزية بما أنك تتخطى الامتحان)
                            </div>
                            <div class="level-radio-group">
                                @foreach(['A0', 'A1', 'A2', 'A2+', 'B1', 'B1+', 'B2', 'C1'] as $lvl)
                                <div class="level-radio-item">
                                    <input type="radio" name="current_level" value="{{ $lvl }}" id="lvl-{{ $lvl }}" class="btn-check">
                                    <label class="btn btn-outline-primary w-100" for="lvl-{{ $lvl }}">{{ $lvl }}</label>
                                </div>
                                @endforeach
                            </div>
                        </div>

                        <div id="test-scheduling-fields" class="mt-5"
                            style="display: none; animation: fadeIn 0.5s ease;">
                            <div class="alert shadow-sm mb-4 border-0"
                                style="background: #fff9e6; border-left: 5px solid var(--secondary-color) !important; color: #856404;">
                                <i class="fa fa-info-circle me-2" style="color: var(--secondary-color);"></i>
                                <strong>Important:</strong> Placement test fee is 100 ILS. Please choose your preferred
                                slot.
                            </div>
                            <div class="row">
                                <div class="col-md-6 mb-4">
                                    <label class="form-label">Preferred Date (التاريخ المفضل) *</label>
                                    <input type="date" name="test_date" class="form-control"
                                        min="{{ date('Y-m-d') }}">
                                </div>
                                <div class="col-md-12 mb-4">
                                    <div class="scheduling-card p-4 bg-white rounded-4 border shadow-sm">
                                        <div class="row g-0">
                                            <!-- Days Section -->
                                            <div class="col-md-6 pe-md-4 border-end position-relative">
                                                <div class="d-flex align-items-center mb-4 scheduling-sub-header justify-content-end">
                                                    <span class="fs-4 fw-bold text-primary">DAYS</span>
                                                    <i class="bi bi-calendar3 ms-3 fs-3 text-primary"></i>
                                                </div>
                                                <div class="d-flex flex-column gap-3">
                                                    <label class="schedule-option">
                                                        <input type="radio" name="preferred_days" value="SAT-MON-WED">
                                                        <span class="checkbox-box"></span>
                                                        <span class="option-text">SATURDAY - MONDAY - WEDNESDAY</span>
                                                    </label>
                                                    <label class="schedule-option">
                                                        <input type="radio" name="preferred_days" value="SUN-TUE-THU">
                                                        <span class="checkbox-box"></span>
                                                        <span class="option-text">SUNDAY - TUESDAY - THURSDAY</span>
                                                    </label>
                                                </div>
                                            </div>

                                            <!-- Time Section -->
                                            <div class="col-md-6 ps-md-4">
                                                <div class="d-flex align-items-center mb-4 scheduling-sub-header justify-content-start">
                                                    <i class="bi bi-clock me-3 fs-3 text-primary"></i>
                                                    <span class="fs-4 fw-bold text-primary">TIME</span>
                                                </div>
                                                <div class="d-flex flex-column gap-3">
                                                    <label class="schedule-option">
                                                        <input type="radio" name="preferred_time" value="Morning">
                                                        <span class="checkbox-box"></span>
                                                        <span class="option-text">Morning Before 12:00 pm</span>
                                                    </label>
                                                    <label class="schedule-option">
                                                        <input type="radio" name="preferred_time" value="Noon">
                                                        <span class="checkbox-box"></span>
                                                        <span class="option-text">Noon After 12:00 pm</span>
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Step 4: Payment Methods -->
                    <div class="step-pane" id="pane-4">
                        <div class="text-center mb-5">
                            <img src="{{ asset('assets/media/illustrations/misc/credit-card.png') }}" alt="Payment"
                                class="mb-3"
                                style="max-height: 120px; filter: drop-shadow(0 10px 15px rgba(0,0,0,0.1));">
                            <h3 class="fw-bold step-heading justify-content-center">
                                <i class="bi bi-credit-card me-3"></i>
                                Step 4: Secure Payment
                            </h3>
                            <p class="text-muted">Choose your preferred payment method and upload the receipt.</p>
                        </div>

                        <div class="payment-grid mb-5">
                            @foreach ($payment_methods as $method)
                                <div class="payment-card"
                                    onclick="handlePaymentSelection('{{ $method->id }}', '{{ addslashes(json_encode($method->credentials)) }}')">
                                    <div class="icon-box shadow-sm">
                                        @if ($method->image)
                                            <img src="{{ asset('uploads/' . $method->image) }}"
                                                alt="{{ $method->name }}">
                                        @else
                                            <i class="fa fa-university fs-2 text-primary"></i>
                                        @endif
                                    </div>
                                    <h6 class="mb-0 fw-bold">{{ $method->name }}</h6>
                                    <div class="check-icon" style="display: none;"><i class="bi bi-check-circle-fill"></i></div>
                                </div>
                            @endforeach
                        </div>
                        <input type="hidden" name="payment_method_id" id="payment_method_hidden">

                            <div id="method-details" class="payment-details-card payment-instructions-wrapper" style="display: none;">
                                <div class="details-header">
                                    <i class="bi bi-info-circle-fill me-2"></i>
                                    Payment Instructions
                                </div>
                                <div id="credentials-list" class="p-4 bg-white rounded-bottom-4 shadow-sm border border-top-0"></div>
                            </div>

                            <div class="upload-receipt-label">
                                <label class="form-label fw-bold mb-3"><i class="bi bi-upload"></i>Upload Payment Receipt (رفع إيصال الدفع) *</label>
                                <div class="file-upload-container" id="receipt-dropzone">
                                    <input type="file" name="payment_receipt" id="receipt_input" accept="image/*,.pdf"
                                        onchange="updateFileName(this)">
                                    <div class="upload-content text-center">
                                        <div class="upload-icon-wrapper mb-3">
                                            <i class="bi bi-cloud-arrow-up"></i>
                                        </div>
                                        <h5>Drop your receipt here</h5>
                                        <div id="file-name-preview" class="mt-3"></div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Step 5: Final Review & Terms -->
                        <div class="step-pane" id="pane-5">
                            <h4 class="mb-4 step-heading">
                                <i class="bi bi-patch-check-fill me-3"></i>
                                Step 5: Finalize Registration
                            </h4>
                            <p class="text-muted mb-5">Please review your health status and agree to the terms to complete your registration.</p>

                            <!-- Health Status Section -->
                            <div class="interaction-card shadow-sm border-0 bg-white p-4 rounded-4 mb-4">
                                <div class="header-flex-force border-bottom pb-3">
                                    <div class="icon-box-final bg-light-primary rounded-3 p-2">
                                        <i class="bi bi-heart-pulse-fill fs-3 text-primary"></i>
                                    </div>
                                    <h5 class="fw-bold">Health Information (المعلومات الصحية)</h5>
                                </div>
                                
                                <div class="px-2">
                                    <p class="text-muted mb-4">Does the applicant have any health problems? (هل يعاني المتقدم من مشاكل صحية؟)</p>
                                    
                                    <div class="d-flex gap-4 mb-4">
                                        <label class="custom-radio-pill">
                                            <input type="radio" name="health_status" value="no" checked onchange="toggleHealthNotes(false)">
                                            <span>No (لا يوجد)</span>
                                        </label>
                                        <label class="custom-radio-pill">
                                            <input type="radio" name="health_status" value="yes" onchange="toggleHealthNotes(true)">
                                            <span>Yes (نعم يوجد)</span>
                                        </label>
                                    </div>
                                    
                                    <div id="health-notes-wrapper" style="display: none;">
                                        <label class="form-label small text-muted fw-bold">Health condition details (يرجى وصف الحالة الصحية)</label>
                                        <textarea name="health_notes" id="health_notes" class="form-control health-textarea" rows="3" placeholder="Describe here..."></textarea>
                                    </div>
                                </div>
                            </div>

                            <!-- Terms & Conditions Section -->
                            <div class="interaction-card shadow-sm border-0 bg-white p-4 rounded-4 mb-4">
                                <div class="header-flex-force border-bottom pb-3">
                                    <div class="icon-box-final bg-light-warning rounded-3 p-2">
                                        <i class="bi bi-file-earmark-text-fill fs-3 text-warning"></i>
                                    </div>
                                    <h5 class="fw-bold">Terms & Conditions (الشروط والأحكام)</h5>
                                </div>

                                <div class="px-2">
                                    <div class="registration-notes-alert p-3 rounded-4 mb-4 border-start border-5 border-warning bg-light">
                                        <h6 class="fw-bold mb-3 d-flex align-items-center gap-2">
                                            <i class="bi bi-exclamation-circle-fill text-warning"></i>
                                            Important Notes (ملاحظات هامة)
                                        </h6>
                                        <ul class="mb-0 small text-dark opacity-75 lh-lg">
                                            <li>Ensure your phone number is correct for WhatsApp communication.</li>
                                            <li>Placement test fees are non-refundable after scheduling.</li>
                                            <li>Registration activates within 24 hours of verification.</li>
                                        </ul>
                                    </div>

                                    <div class="agreement-box flex-align-force p-3 rounded-4 border bg-light-soft cursor-pointer" 
                                         onclick="if(event.target.id !== 'agree-terms') document.getElementById('agree-terms').click()">
                                        <input type="checkbox" id="agree-terms" required class="form-check-input form-check-input-custom">
                                        <div class="agreement-text">
                                            <span class="fw-bold d-block text-dark">I agree to the Terms & Privacy Policy</span>
                                            <span class="small text-muted">أوافق على جميع الشروط والأحكام الخاصة بالمركز</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                </div>

                <!-- Footer Navigation -->
                <div class="wizard-footer">
                    <button type="button" class="btn-wizard btn-prev" id="btn-prev" style="display: none;"
                        onclick="changeStep(-1)">Back</button>
                    <button type="button" class="btn-wizard btn-next" id="btn-next" onclick="changeStep(1)">Next
                        Step</button>
                </div>
            </form>
        </div>
    </div>
    </div>

    <script src="{{ asset('assets/oxford/js/wizard.js') }}"></script>
    <script>
        window.registrationRoute = '{{ route('contact.book') }}';

        document.addEventListener('DOMContentLoaded', function() {
            // Delay Program Selection Reveal
            setTimeout(() => {
                const container = document.getElementById('program-selection-container');
                if (container) {
                    container.style.display = 'block';
                    if (typeof AOS !== 'undefined') AOS.refresh();
                }
            }, 1500);

            // Initialize Hero Particles
            if (typeof initParticles === 'function') {
                initParticles('hero-particles', {
                    color1: 'rgba(245, 197, 24, 0.7)',
                    color2: 'rgba(255, 255, 255, 0.25)',
                    count: 100, // Increased count
                    speed: 0.4,
                    connectLines: false,
                    lineColor: 'rgba(245, 197, 24, 0.15)',
                    connectDistance: 150
                });
            }

            if (typeof AOS !== 'undefined') {
                AOS.init({ duration: 1000, once: true, offset: 100 });
            }

            @if (session('success'))
                const lang = document.documentElement.lang || 'en';
                const msgs = {
                    en: 'Your booking request has been submitted successfully.',
                    ar: 'تم إرسال طلب التسجيل بنجاح.'
                };
                
                Swal.fire({
                    icon: 'success',
                    title: lang === 'ar' ? 'تم التسجيل!' : 'Submitted!',
                    text: msgs[lang],
                    confirmButtonText: lang === 'ar' ? 'موافق' : 'Great!',
                    customClass: {
                        popup: 'swal-oxford-popup',
                        confirmButton: 'swal-oxford-confirm'
                    },
                    buttonsStyling: false,
                    timer: 5000
                }).then(() => window.location.href = '{{ url('/') }}');
            @endif

            // Handle URL parameter for program auto-selection
            const urlParams = new URLSearchParams(window.location.search);
            const programParam = urlParams.get('program');
            if (programParam === 'adult' || programParam === 'kids') {
                setTimeout(() => {
                    if (typeof handleProgramSelection === 'function') {
                        handleProgramSelection(programParam);
                    }
                }, 2000);
            }
        });

        window.showProgramQRCode = function(type, event) {
            if (event) event.stopPropagation();
            
            const baseUrl = window.location.origin + window.location.pathname;
            const qrUrl = `${baseUrl}?program=${type}`;
            const qrImageSource = `https://api.qrserver.com/v1/create-qr-code/?size=300x300&data=${encodeURIComponent(qrUrl)}`;
            
            const title = type === 'adult' ? 'Adult Program QR Code' : 'Kids Program QR Code';
            const titleAr = type === 'adult' ? 'رمز QR لبرنامج الكبار' : 'رمز QR لبرنامج الأطفال';
            const lang = document.documentElement.lang || 'en';

            Swal.fire({
                title: lang === 'ar' ? titleAr : title,
                html: `
                    <div class="qr-modal-content text-center py-4">
                        <img src="${qrImageSource}" alt="QR Code" class="img-fluid mb-4 shadow-sm rounded" style="width: 250px; border: 10px solid white;">
                        <div class="d-flex justify-content-center gap-3">
                            <button onclick="downloadQRCode('${qrImageSource}', '${type}')" class="btn btn-primary rounded-pill px-4 shadow-sm">
                                <i class="bi bi-download me-2"></i> ${lang === 'ar' ? 'تحميل' : 'Download'}
                            </button>
                            <button onclick="shareQRCode('${qrUrl}')" class="btn btn-warning rounded-pill px-4 shadow-sm" style="background-color: var(--secondary-color); border: none; color: var(--primary-color); font-weight: bold;">
                                <i class="bi bi-share me-2"></i> ${lang === 'ar' ? 'مشاركة' : 'Share'}
                            </button>
                        </div>
                    </div>
                `,
                showConfirmButton: false,
                showCloseButton: true,
                customClass: {
                    popup: 'swal-oxford-popup qr-modal-popup'
                }
            });
        };

        window.downloadQRCode = function(url, type) {
            const xhr = new XMLHttpRequest();
            xhr.open("GET", url, true);
            xhr.responseType = "blob";
            xhr.onload = function() {
                const urlCreator = window.URL || window.webkitURL;
                const imageUrl = urlCreator.createObjectURL(this.response);
                const tag = document.createElement('a');
                tag.href = imageUrl;
                tag.download = `Oxford_${type}_Program_QR.png`;
                document.body.appendChild(tag);
                tag.click();
                document.body.removeChild(tag);
            };
            xhr.send();
        };

        window.shareQRCode = function(url) {
            if (navigator.share) {
                navigator.share({
                    title: 'Oxford English Center Registration',
                    url: url
                }).catch(err => console.log('Error sharing:', err));
            } else {
                navigator.clipboard.writeText(url).then(() => {
                    const lang = document.documentElement.lang || 'en';
                    Swal.fire({
                        icon: 'success',
                        title: lang === 'ar' ? 'تم النسخ!' : 'Copied!',
                        text: lang === 'ar' ? 'تم نسخ الرابط إلى الحافظة.' : 'URL copied to clipboard.',
                        timer: 2000,
                        showConfirmButton: false,
                        customClass: { popup: 'swal-oxford-popup' }
                    });
                });
            }
        };
    </script>

@endsection
