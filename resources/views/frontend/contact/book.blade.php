@extends('frontend.layouts.master')
@section('title', 'Book A Course')
@section('content')

<link rel="stylesheet" href="{{ asset('css/pages/book-a-course.css') }}">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<!-- Hero Section with Particles -->
<div class="book-course-hero">
    <canvas id="hero-particles"></canvas>
    <div class="hero-content">
        <h1>Book A Course</h1>
        <p>Start your English language learning journey with us today</p>
    </div>
</div>

<!-- Main Content Container -->
<div class="container">
    <div class="book-course-container">
        <!-- Left Panel - Decorative Section -->
        <div class="book-course-sidebar">
            <div class="book-course-icon">
                <i class="fa fa-book" aria-hidden="true"></i>
            </div>
            <h2>Why Choose Us?</h2>
            <p>
                Join Oxford Language Center and master English with expert instructors, modern curriculum, and supportive community.
            </p>
            <ul class="book-course-benefits">
                <li>Experienced & Certified Instructors</li>
                <li>Flexible Scheduling Options</li>
                <li>Interactive Learning Environment</li>
                <li>Personalized Learning Path</li>
                <li>International Certification Prep</li>
                <li>Proven Success Rate</li>
            </ul>
        </div>

        <!-- Right Panel - Form Section -->
        <div class="book-course-form-wrapper">
            <!-- Form Header -->
            <div class="book-course-form-header">
                <h3>Complete Your Registration</h3>
                <p>Fill in your details to book your course</p>
            </div>

            <!-- Alert Messages -->
            @if (session('danger'))
                <div class="alert alert-danger">
                    <strong>⚠️ Submission Error:</strong>
                    @if(is_array(session('danger')))
                        <ul style="margin: 10px 0 0 20px; padding: 0;">
                            @foreach(session('danger') as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    @else
                        <div style="margin-top: 8px;">{{ session('danger') }}</div>
                    @endif
                </div>
            @endif

            <!-- Booking Form -->
            <form id="book-course-form" method="POST" action="{{ route('contact.book') }}" class="book-course-form">
                {{ csrf_field() }}

                <!-- Row 1: Name & Date of Birth -->
                <div class="form-row-2col">
                    <div class="form-group @if($errors->has('name')) has-error @endif">
                        <label for="name">Full Name *</label>
                        <input
                            type="text"
                            id="name"
                            name="name"
                            placeholder="Enter your full name"
                            value="{{ old('name') }}"
                            required
                        >
                        @if ($errors->has('name'))
                            <span class="form-error">{{ $errors->first('name') }}</span>
                        @endif
                    </div>

                    <div class="form-group @if($errors->has('dob')) has-error @endif">
                        <label for="dob">Date of Birth *</label>
                        <input
                            type="date"
                            id="dob"
                            name="dob"
                            value="{{ old('dob') }}"
                            required
                        >
                        @if ($errors->has('dob'))
                            <span class="form-error">{{ $errors->first('dob') }}</span>
                        @endif
                    </div>
                </div>

                <!-- Row 2: Email & Phone -->
                <div class="form-row-2col">
                    <div class="form-group @if($errors->has('email')) has-error @endif">
                        <label for="email">Email Address *</label>
                        <input
                            type="email"
                            id="email"
                            name="email"
                            placeholder="your.email@gmail.com"
                            value="{{ old('email') }}"
                            required
                        >
                        @if ($errors->has('email'))
                            <span class="form-error">{{ $errors->first('email') }}</span>
                        @endif
                    </div>

                    <div class="form-group @if($errors->has('phone')) has-error @endif">
                        <label for="phone">Phone Number *</label>
                        <input
                            type="tel"
                            id="phone"
                            name="phone"
                            placeholder="Your phone number"
                            value="{{ old('phone') }}"
                            required
                        >
                        @if ($errors->has('phone'))
                            <span class="form-error">{{ $errors->first('phone') }}</span>
                        @endif
                    </div>
                </div>

                <!-- Row 3: Address & Gender -->
                <div class="form-row-2col">
                    <div class="form-group @if($errors->has('address')) has-error @endif">
                        <label for="address">Address *</label>
                        <input
                            type="text"
                            id="address"
                            name="address"
                            placeholder="Your street address"
                            value="{{ old('address') }}"
                            required
                        >
                        @if ($errors->has('address'))
                            <span class="form-error">{{ $errors->first('address') }}</span>
                        @endif
                    </div>

                    <div class="form-group @if($errors->has('gender')) has-error @endif">
                        <label for="gender">Gender *</label>
                        <select id="gender" name="gender" required>
                            <option value="">Select your gender</option>
                            <option value="Male" {{ old('gender') == 'Male' ? 'selected' : '' }}>Male</option>
                            <option value="Female" {{ old('gender') == 'Female' ? 'selected' : '' }}>Female</option>
                        </select>
                        @if ($errors->has('gender'))
                            <span class="form-error">{{ $errors->first('gender') }}</span>
                        @endif
                    </div>
                </div>

                <!-- Row 4: Major & How Did You Hear -->
                <div class="form-row-2col">
                    <div class="form-group @if($errors->has('major')) has-error @endif">
                        <label for="major">Major / Field of Study *</label>
                        <input
                            type="text"
                            id="major"
                            name="major"
                            placeholder="e.g., Business, Engineering, Medicine"
                            value="{{ old('major') }}"
                            required
                        >
                        @if ($errors->has('major'))
                            <span class="form-error">{{ $errors->first('major') }}</span>
                        @endif
                    </div>

                    <div class="form-group @if($errors->has('how')) has-error @endif">
                        <label for="how">How did you hear about us? *</label>
                        <select id="how" name="how" required>
                            <option value="">Select an option</option>
                            <option value="Google Search" {{ old('how') == 'Google Search' ? 'selected' : '' }}>Google Search</option>
                            <option value="Social Media" {{ old('how') == 'Social Media' ? 'selected' : '' }}>Social Media</option>
                            <option value="Friend Referral" {{ old('how') == 'Friend Referral' ? 'selected' : '' }}>Friend Referral</option>
                            <option value="Advertisement" {{ old('how') == 'Advertisement' ? 'selected' : '' }}>Advertisement</option>
                            <option value="Other" {{ old('how') == 'Other' ? 'selected' : '' }}>Other</option>
                        </select>
                        @if ($errors->has('how'))
                            <span class="form-error">{{ $errors->first('how') }}</span>
                        @endif
                    </div>
                </div>

                <!-- Full Width: Terms Agreement -->
                <div class="form-group">
                    <div class="checkbox-group @if($errors->has('agree')) has-error @endif">
                        <label for="agree">
                            <input
                                type="checkbox"
                                id="agree"
                                name="agree"
                                value="1"
                                {{ old('agree') ? 'checked' : '' }}
                                required
                            >
                            I agree to the terms and conditions and privacy policy *
                        </label>
                        @if ($errors->has('agree'))
                            <span class="form-error">{{ $errors->first('agree') }}</span>
                        @endif
                    </div>
                </div>

                <!-- Submit Button -->
                <button type="submit" class="btn-submit">
                    <i class="fa fa-paper-plane-o" aria-hidden="true"></i> Complete Registration
                </button>
            </form>
        </div>
    </div>
</div>

<script src="{{ asset('js/particles-hero.js') }}"></script>
<script src="{{ asset('js/book-course.js') }}"></script>

<script>
    // ============================================================
    // Book A Course Form Initialization
    // ============================================================

    // Bilingual success messages
    const successMessages = {
        en: 'Your booking request has been submitted successfully. Please visit the center to complete registration and pay the required fees.',
        ar: 'تم إرسال طلب التسجيل بنجاح. يرجى التوجه إلى المركز لإتمام عملية التسجيل ودفع الرسوم.'
    };

    document.addEventListener('DOMContentLoaded', function() {
        // 1. Initialize Hero Particles Animation (Only Once)
        initParticles('hero-particles', {
            color1: 'rgba(245, 197, 24, 0.7)',
            color2: 'rgba(255, 255, 255, 0.25)',
            count: 60,
            speed: 0.4,
            connectLines: true,
            lineColor: 'rgba(245, 197, 24, 0.15)',
            connectDistance: 150
        });

        // 2. Initialize Form Validation
        BookingFormValidator.init();

        // 3. Show Success Message if Redirected with Session Flash
        @if (session('success'))
            const userLang = document.documentElement.lang || 'en';
            const successMsg = successMessages[userLang] || successMessages.en;

            Swal.fire({
                icon: 'success',
                title: userLang === 'ar' ? 'تم التسجيل بنجاح!' : 'Booking Submitted!',
                html: successMsg,
                confirmButtonText: userLang === 'ar' ? 'موافق' : 'Great!',
                confirmButtonColor: '#f5c518',
                iconColor: '#f5c518',
                customClass: {
                    popup: 'swal-oxford-popup',
                    confirmButton: 'swal-confirm-btn'
                },
                buttonsStyling: false,
                timer: 9000,
                timerProgressBar: true,
                didOpen: (modal) => {
                    // Auto redirect after close
                },
                willClose: () => {
                    window.location.href = '{{ url('/') }}';
                }
            });
        @endif
    });
</script>

@stop
