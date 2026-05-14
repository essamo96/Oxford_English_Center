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

        /* Premium Payment Styles */
        .fee-info-card {
            transition: all 0.3s ease;
            border: 1px solid rgba(0,0,0,0.05);
        }
        .fee-info-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0,0,0,0.1) !important;
        }
        .btn-outline-primary, .btn-outline-warning {
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            background: white;
        }
        .btn-outline-primary:hover {
            background-color: var(--primary-color) !important;
            color: white !important;
            transform: scale(1.02);
        }
        .btn-outline-warning:hover {
            background-color: var(--secondary-color) !important;
            color: var(--primary-color) !important;
            transform: scale(1.02);
        }
        @keyframes slideDown {
            from { opacity: 0; transform: translateY(-20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .payment-details-card {
            border-radius: 1.25rem;
            overflow: hidden;
            margin-top: 1.5rem;
        }

        .level-radio-item label { border-radius: 12px; font-weight: 600; padding: 12px; transition: all 0.3s ease; }
        .btn-check:checked + label { background-color: var(--primary-color) !important; border-color: var(--primary-color) !important; color: white !important; transform: translateY(-2px); box-shadow: 0 4px 12px rgba(0,0,0,0.1); }
        
        /* Global Icon Spacing Fix */
        .form-label i, .step-heading i, .interaction-card label i { 
            margin-right: 1rem !important; 
            font-size: 1.2em;
            vertical-align: middle;
            color: var(--primary-color);
        }
        [dir="rtl"] .form-label i, [dir="rtl"] .step-heading i, [dir="rtl"] .interaction-card label i {
            margin-right: 0 !important;
            margin-left: 1rem !important;
        }

        /* Payment Method Cards Refined */
        .payment-methods-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(180px, 1fr)); gap: 1.25rem; }
        .payment-method-card { 
            border: 2px solid #f0f0f0; 
            border-radius: 20px; 
            padding: 2rem 1.5rem; 
            text-align: center; 
            cursor: pointer; 
            transition: all 0.4s cubic-bezier(0.165, 0.84, 0.44, 1); 
            background: #fff; 
            position: relative; 
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
        }
        .payment-method-card:hover { 
            border-color: var(--primary-color); 
            transform: translateY(-8px); 
            box-shadow: 0 15px 30px rgba(0,0,0,0.08); 
        }
        .payment-method-card.active { 
            border-color: var(--secondary-color) !important; 
            background: linear-gradient(145deg, #ffffff, #fffef0);
            box-shadow: 0 10px 20px rgba(255, 204, 0, 0.1);
        }
        .payment-method-card.active::before {
            content: '';
            position: absolute;
            top: 0; left: 0; right: 0; bottom: 0;
            border-radius: 20px;
            border: 2px solid var(--secondary-color);
        }
        .payment-method-card.active::after { 
            content: '\F272'; 
            font-family: 'bootstrap-icons'; 
            position: absolute; 
            top: 12px; 
            right: 12px; 
            background: var(--secondary-color);
            color: var(--primary-color); 
            width: 24px;
            height: 24px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.8rem; 
            font-weight: bold;
        }
        .payment-method-card i { 
            font-size: 3rem; 
            color: #bdc3c7; 
            margin-bottom: 1rem; 
            transition: all 0.3s; 
        }
        .payment-method-card.active i { color: var(--secondary-color); transform: scale(1.1); }
        .payment-method-card img.payment-logo {
            width: 60px;
            height: 60px;
            object-fit: contain;
            border-radius: 12px;
            padding: 5px;
            background: #fff;
            box-shadow: 0 4px 10px rgba(0,0,0,0.05);
            margin-bottom: 1rem;
            transition: all 0.3s;
        }
        .payment-method-card.active img.payment-logo {
            border: 2px solid var(--secondary-color);
            box-shadow: 0 8px 15px rgba(255, 204, 0, 0.2);
        }
        .payment-method-card span { font-weight: 800; color: #2c3e50; font-size: 1rem; }
        
        .credentials-container {
            margin: 2.5rem 0;
            padding: 2rem 1rem;
            border-top: 1px solid #f0f0f0;
            border-bottom: 1px solid #f0f0f0;
        }
        
        .credentials-card {
            background: linear-gradient(135deg, #f8f9fa 0%, #ffffff 100%);
            border-radius: 20px;
            padding: 1.5rem;
            border: 1px solid rgba(0,0,0,0.05);
            box-shadow: inset 0 2px 4px rgba(0,0,0,0.02);
        }
        .credential-item {
            background: white;
            border-radius: 12px;
            padding: 1rem;
            margin-bottom: 0.75rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
            border: 1px solid #edf2f7;
            transition: transform 0.2s;
        }
        .credential-item:hover { transform: translateX(5px); border-color: var(--primary-color); }
        .credential-label { color: #718096; font-size: 0.75rem; font-weight: 800; text-transform: uppercase; letter-spacing: 0.05em; }
        .credential-value { color: var(--primary-color); font-weight: 700; font-size: 1.1rem; }
    </style>

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
                            <img src="{{ asset('assets/oxford/program2.jfif') }}" alt="Adult Program">
                            <div class="overlay-info">
                                <h3 class="program-name">English For Adults</h3>
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
                                <h3 class="program-name">English For Young Learners</h3>
                                <div class="qr-circle" onclick="showProgramQRCode('kids', event)"><i class="bi bi-qr-code-scan"></i></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Enrollment Type Choice (Course vs Test) -->
            <div id="enrollment-type-container" style="display: none;" class="mt-5">
                <div class="text-center mb-5" data-aos="fade-up">
                    <h2 class="fw-bold text-primary">How would you like to start?</h2>
                    <p class="text-muted">Choose your preferred enrollment path</p>
                </div>
                <div class="enrollment-type-wrapper d-flex justify-content-center gap-4">
                    <div class="enroll-type-card" onclick="selectEnrollmentType('course', this)">
                        <div class="type-icon"><i class="bi bi-book"></i></div>
                        <h4>Direct Enrollment</h4>
                        <p>I know my level and want to join a program.</p>
                        <span class="type-badge">Recommended</span>
                    </div>
                    <div class="enroll-type-card" onclick="selectEnrollmentType('test', this)">
                        <div class="type-icon"><i class="bi bi-pencil-square"></i></div>
                        <h4>Placement Test</h4>
                        <p>I want a professional evaluation of my level.</p>
                        <span class="type-badge secondary">Expert Choice</span>
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
                <input type="hidden" name="enrollment_type" id="enrollment_type_hidden">

                <div class="wizard-body">
                    <!-- Step 1: Basic Information -->
                    <div class="step-pane active" id="pane-1">
                        <h4 class="mb-4 step-heading">
                            <i class="bi bi-person-circle me-3"></i>
                            Step 1: Personal Information & Health
                        </h4>
                        <div class="row">
                            <div class="col-md-6 mb-4">
                                <label class="form-label"><i class="bi bi-person-badge me-2"></i>Full Name (Arabic) (الاسم الرباعي بالعربية) *</label>
                                <input type="text" name="name" class="form-control" placeholder="الاسم الرباعي بالعربية"
                                    required pattern="[\u0600-\u06FF\s]+" title="Please enter name in Arabic only">
                            </div>
                            <div class="col-md-6 mb-4">
                                <label class="form-label"><i class="bi bi-alphabet-uppercase me-2"></i>Full Name (English) (الاسم الرباعي بالإنجليزية) *</label>
                                <input type="text" name="name_en" class="form-control" placeholder="English Quad Name"
                                    required>
                            </div>
                            <div class="col-md-6 mb-4">
                                <label class="form-label"><i class="bi bi-phone me-2"></i>Phone Number (رقم الجوال) *</label>
                                <input type="text" name="mobile" class="form-control" placeholder="05x xxxx xxx"
                                    required>
                            </div>
                            <div class="col-md-6 mb-4">
                                <label class="form-label"><i class="bi bi-envelope me-2"></i>Email Address (البريد الإلكتروني) *</label>
                                <input type="email" name="email" class="form-control" placeholder="example@gmail.com"
                                    required>
                            </div>
                            <div class="col-md-6 mb-4">
                                <label class="form-label"><i class="bi bi-calendar-event me-2"></i>Date of Birth (تاريخ الميلاد) *</label>
                                <input type="date" name="dob" class="form-control" required>
                            </div>
                            <div class="col-md-6 mb-4" id="gender-group">
                                <label class="form-label"><i class="bi bi-gender-ambiguous me-2"></i>Gender (الجنس) *</label>
                                <select name="gender" class="form-control" required>
                                    <option value="">Select Gender</option>
                                    <option value="1">Male</option>
                                    <option value="2">Female</option>
                                </select>
                            </div>
                            <div class="col-md-12 mb-4">
                                <label class="form-label"><i class="bi bi-geo-alt me-2"></i>Address (العنوان) *</label>
                                <textarea name="address" class="form-control" rows="2" placeholder="Full Address Details" required></textarea>
                            </div>

                            <!-- Moved Health Status Section here -->
                            <div class="col-md-12">
                                <div class="interaction-card shadow-sm border-0 bg-light p-4 rounded-4 mb-4">
                                    <div class="header-flex-force border-bottom pb-3 mb-3">
                                        <h5 class="fw-bold mb-0">Health Information (المعلومات الصحية)</h5>
                                    </div>
                                    <p class="text-muted mb-3 small">Does the applicant have any health problems? (هل يعاني المتقدم من مشاكل صحية؟)</p>
                                    <div class="d-flex gap-4 mb-3">
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
                                        <textarea name="health_notes" id="health_notes" class="form-control health-textarea" rows="2" placeholder="Describe here..."></textarea>
                                    </div>
                                </div>
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
                                <div class="col-md-12 mb-4">
                                    <label class="form-label"><i class="bi bi-mortarboard me-2"></i>Major/Profession (التخصص/المهنة) *</label>
                                    <input type="text" name="major" class="form-control"
                                        placeholder="e.g. Engineering, Student">
                                </div>
                            </div>
                        </div>

                        <div id="kids-fields" style="display: none;">
                            <h4 class="mb-4 step-heading">
                                <i class="bi bi-people me-3"></i>
                                Step 2: Parent / Guardian Information
                            </h4>
                            <div class="row">
                                <div class="col-md-12 mb-4">
                                    <label class="form-label"><i class="bi bi-journal-text me-2"></i>Parent Name (اسم ولي الأمر) *</label>
                                    <input type="text" name="parent_name" class="form-control"
                                        placeholder="Parent Full Name">
                                </div>
                                <div class="col-md-6 mb-4">
                                    <label class="form-label"><i class="bi bi-telephone-plus me-2"></i>Parent Phone (رقم جوال ولي الأمر) *</label>
                                    <input type="text" name="parent_phone" class="form-control"
                                        placeholder="05x xxxx xxx">
                                </div>
                                <div class="col-md-6 mb-4">
                                    <label class="form-label"><i class="bi bi-people me-2"></i>Relationship (صلة القرابة) *</label>
                                    <input type="text" name="parent_relationship" class="form-control" placeholder="e.g. Father, Mother">
                                </div>
                                <div class="col-md-12 mb-4">
                                    <label class="form-label"><i class="bi bi-envelope-at me-2"></i>Parent Email (البريد الإلكتروني لولي الأمر)</label>
                                    <input type="email" name="parent_email" class="form-control"
                                        placeholder="Parent Email">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Step 3: Placement & Program Selection -->
                    <div class="step-pane" id="pane-3">
                        <div class="text-center mb-5">
                            <h3 class="fw-bold step-heading justify-content-center">
                                <i class="bi bi-calendar-check me-3"></i>
                                Step 3: Placement & Program
                            </h3>
                            <p class="text-muted">Select your target program and placement options.</p>
                        </div>

                        <input type="hidden" name="take_test" id="take_test_hidden">
                        <div id="test-options-container" style="display: none;"></div>

                        <!-- Target Program & Level Selection -->
                        <div class="interaction-card shadow-sm border-0 bg-white p-5 rounded-4 mb-5 border-start border-primary border-5">
                            <div class="row align-items-center">
                                <div class="col-lg-6 border-end-lg pe-lg-5">
                                    <label class="form-label fw-bold fs-5 mb-3"><i class="bi bi-mortarboard-fill me-3 text-primary"></i>Target Program (البرنامج المستهدف) *</label>
                                    <select name="program_id" id="program_id_select" class="form-control form-control-lg select2 shadow-none border-2" onchange="handleProgramChange()">
                                        <option value="">Choose your program...</option>
                                        @foreach($programs as $p)
                                            @php
                                                $title = strtolower($p->title);
                                                $isKids = str_contains($title, 'kids') || str_contains($p->title, 'أطفال');
                                                $isAdult = str_contains($title, 'adult') || str_contains($p->title, 'كبار');
                                                // If it's a general program like "Levels", show it for both
                                                $isGeneral = str_contains($title, 'مستويات') || str_contains($title, 'level');
                                                
                                                $type = 'Both';
                                                if ($isKids && !$isAdult) $type = 'Kids';
                                                elseif ($isAdult && !$isKids) $type = 'Adults';
                                                elseif ($isGeneral) $type = 'Both';
                                            @endphp
                                            <option value="{{ $p->id }}" data-type="{{ $type }}">
                                                {{ $p->title }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <div id="program-info-preview" class="mt-3 small text-muted p-2 bg-light rounded" style="display: none;">
                                        <i class="bi bi-info-circle me-1"></i>
                                        <span id="program-preview-text"></span>
                                    </div>
                                </div>
                                
                                <div class="col-lg-6 ps-lg-5 mt-4 mt-lg-0">
                                    <div id="skip-test-level-selection" style="display: none;">
                                        <label class="form-label fw-bold fs-5 mb-3 text-info"><i class="bi bi-graph-up-arrow me-3"></i>Current Level (المستوى الحالي) *</label>
                                        <div class="level-radio-group d-flex flex-wrap gap-2">
                                            @foreach(['A0', 'A1', 'A2', 'A2+', 'B1', 'B1+', 'B2', 'C1'] as $lvl)
                                            <div class="level-radio-item" style="flex: 1 1 60px;">
                                                <input type="radio" name="current_level" value="{{ $lvl }}" id="lvl-{{ $lvl }}" class="btn-check">
                                                <label class="btn btn-outline-primary w-100 py-2 px-1" for="lvl-{{ $lvl }}">{{ $lvl }}</label>
                                            </div>
                                            @endforeach
                                        </div>
                                    </div>
                                    <div id="test-info-placeholder" class="text-center p-4 border rounded-4 border-dashed bg-light text-muted">
                                        <i class="bi bi-clipboard-data fs-2 mb-2 d-block"></i>
                                        Select a program to see level options or scheduling
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div id="test-scheduling-fields" class="mt-5"
                            style="display: none; animation: fadeIn 0.5s ease;">
                            <div class="alert shadow-sm mb-4 border-0"
                                style="background: #fff9e6; border-left: 5px solid var(--secondary-color) !important; color: #856404;">
                                <i class="fa fa-info-circle me-2" style="color: var(--secondary-color);"></i>
                                <strong>Important:</strong> Placement test fee is 100 ILS. Please choose your preferred slot.
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

                    <!-- Step 4: Secure Payment -->
                    <div class="step-pane" id="pane-4">
                        <div class="text-center mb-5">
                            <h3 class="fw-bold step-heading justify-content-center">
                                <i class="bi bi-credit-card-2-back me-3"></i>
                                Step 4: Payment Summary
                            </h3>
                            <p class="text-muted">Review fees and finalize your payment.</p>
                        </div>

                        <!-- Fees Breakdown -->
                        <div class="mb-5">
                            <label class="form-label fw-bold mb-4 d-block">
                                <i class="bi bi-list-check me-3 text-primary"></i>
                                Fees Breakdown (تفاصيل الرسوم)
                            </label>
                            <div class="bg-white rounded-4 shadow-sm overflow-hidden border">
                                <table class="table table-hover mb-0">
                                    <tbody id="fees-breakdown-body">
                                        <!-- Dynamic rows here -->
                                    </tbody>
                                    <tfoot class="bg-light border-top">
                                        <tr>
                                            <td class="ps-4 py-3 fw-bold">Total Amount Due (إجمالي المستحق)</td>
                                            <td class="pe-4 py-3 text-end fw-bold fs-5 text-primary">
                                                <span id="display-total-due">0.00</span> ILS
                                            </td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>

                        <div class="row g-4">
                            <div class="col-md-12 mb-2">
                                <label class="form-label fw-bold mb-3 d-block"><i class="bi bi-wallet2 me-2 text-primary"></i>Payment Method (طريقة الدفع) *</label>
                                <div class="payment-methods-grid">
                                    @foreach ($payment_methods as $method)
                                        <div class="payment-method-card" 
                                             onclick="selectPaymentMethod('{{ $method->id }}', this)" 
                                             data-creds="{{ json_encode($method->credentials) }}">
                                            @if($method->image)
                                                <img src="{{ asset('uploads/' . $method->image) }}" class="payment-logo" alt="{{ $method->name }}">
                                            @else
                                                <i class="{{ $method->icon ?: 'bi bi-cash-coin' }}"></i>
                                            @endif
                                            <span>{{ $method->name }}</span>
                                        </div>
                                    @endforeach
                                </div>
                                <input type="hidden" name="payment_method_id" id="payment_method_id_hidden">
                                
                                <!-- Payment Credentials Display (Simplified Style) -->
                                <div id="method-details-area" class="mt-5 mb-5" style="display: none;">
                                    <div class="credentials-container px-2">
                                        <label class="form-label fw-bold mb-4 d-block">
                                            <i class="bi bi-shield-lock-fill me-3 text-primary"></i>
                                            Payment Credentials (بيانات الدفع)
                                        </label>
                                        <div id="credentials-list" class="d-flex flex-column gap-3"></div>
                                        <div class="mt-4 text-center p-3 bg-light rounded-4">
                                            <small class="text-muted"><i class="bi bi-info-circle me-1"></i>Please transfer the exact amount and upload the screenshot below.</small>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-bold"><i class="bi bi-cash-stack me-2"></i>Paid Amount (المبلغ المدفوع) *</label>
                                <input type="number" name="student_fee_paid" class="form-control form-control-lg border-2" placeholder="0.00" required oninput="updateRemainingDue()">
                            </div>
                            
                            <div class="col-md-6">
                                <label class="form-label fw-bold"><i class="bi bi-calculator me-2"></i>Remaining Balance (المتبقي)</label>
                                <div class="p-3 bg-light border-2 rounded-3 d-flex justify-content-between align-items-center h-50px" style="border: 2px solid #eee; height: 53px;">
                                    <span class="fw-bold text-muted small">TOTAL LEFT:</span>
                                    <span class="fw-bold text-danger"><span id="display-amount-due">0.00</span> ILS</span>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <label class="form-label fw-bold">Upload Payment Receipt (رفع إيصال الدفع) *</label>
                                <div class="file-upload-container p-4 border-2 border-dashed rounded-4 text-center cursor-pointer" id="receipt-dropzone" onclick="document.getElementById('receipt_input').click()">
                                    <input type="file" name="payment_receipt" id="receipt_input" accept="image/*,.pdf" style="display: none;" onchange="updateFileName(this)" required>
                                    <i class="bi bi-cloud-arrow-up fs-1 text-primary mb-2"></i>
                                    <p class="mb-0 text-muted">Click to upload or drag and drop receipt</p>
                                    <div id="file-name-preview" class="mt-2 fw-bold text-success"></div>
                                </div>
                            </div>
                        </div>


                        <input type="hidden" name="total_due_amount" id="total_due_hidden" value="0">
                    </div>

                    <!-- Step 5: Final Step -->
                    <div class="step-pane" id="pane-5">
                        <div class="text-center mb-5">
                            <i class="bi bi-check2-circle text-success" style="font-size: 4rem;"></i>
                            <h3 class="fw-bold mt-3">Finalize Registration</h3>
                            <p class="text-muted">Review notes and confirm your agreement.</p>
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-bold mb-3 d-block"><i class="bi bi-journal-text me-3 text-primary"></i>General Notes (ملاحظات عامة)</label>
                            <textarea name="general_notes" class="form-control shadow-sm border-2" rows="3" placeholder="Add any extra notes here..."></textarea>
                        </div>

                        <div class="mb-4 p-4 rounded-4 bg-light border-start border-warning border-4 shadow-sm">
                            <label class="form-label fw-bold mb-3 d-block text-warning"><i class="bi bi-shield-lock-fill me-3"></i>Terms & Privacy</label>
                            <div class="agreement-box d-flex align-items-center gap-3 p-3 bg-white rounded-3 border">
                                <input type="checkbox" id="agree-terms" required class="form-check-input m-0" style="width: 28px; height: 28px; cursor: pointer;">
                                <label for="agree-terms" class="agreement-text m-0 cursor-pointer">
                                    <span class="fw-bold d-block fs-6">I agree to the Terms & Privacy Policy</span>
                                    <p class="small text-muted mb-0">أوافق على جميع الشروط والأحكام الخاصة بالأكاديمية وسياسة الخصوصية.</p>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Footer Navigation -->
                <div class="wizard-footer">
                    <button type="button" class="btn-wizard btn-prev" id="btn-prev" style="display: none;"
                        onclick="changeStep(-1)">Back</button>
                    <button type="button" class="btn-wizard btn-next" id="btn-next" onclick="changeStep(1)">Next Step</button>
                </div>
            </form>
        </div>
    </div>
    </div>


@endsection

@section('js')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="{{ asset('js/particles-hero.js') }}"></script>
    <script src="{{ asset('assets/oxford/js/wizard.js') }}"></script>
    <script>
        window.registrationRoute = '{{ route('contact.book.post') }}';


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
