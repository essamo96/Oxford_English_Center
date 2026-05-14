/**
 * Oxford Academy Registration Wizard JS
 * Overhauled logic for reordered steps and dynamic fee calculations.
 */

let currentStep = 1;
let selectedProgramType = ''; // adult or kids
let enrollmentPath = ''; // course or test
let takeTest = false;

// Bilingual messages
const wizardMessages = {
    en: {
        successTitle: 'Registration Submitted!',
        successText: 'Your request has been submitted successfully. Our team will verify your data and contact you shortly.',
        errorTitle: 'Submission Error',
        retry: 'Retry',
        ok: 'Submit Registration',
        next: 'Next Step',
        back: 'Back',
        testWarning: 'Please choose if you want a placement test.',
        paymentWarning: 'Please select a payment method.',
        receiptWarning: 'Please upload your payment receipt.',
        termsWarning: 'Please agree to the terms to proceed.',
        processing: 'Processing...',
        creatingProfile: 'We are creating your profile.'
    },
    ar: {
        successTitle: 'تم التسجيل بنجاح!',
        successText: 'تم إرسال طلبك بنجاح. سيقوم فريقنا بمراجعة البيانات والتواصل معك قريباً.',
        errorTitle: 'خطأ في الإرسال',
        retry: 'إعادة المحاولة',
        ok: 'إتمام التسجيل',
        next: 'الخطوة التالية',
        back: 'العودة',
        testWarning: 'يرجى تحديد ما إذا كنت ترغب في إجراء امتحان مستوى.',
        paymentWarning: 'يرجى اختيار طريقة الدفع.',
        receiptWarning: 'يرجى رفع إيصال الدفع.',
        termsWarning: 'يجب الموافقة على الشروط والأحكام للمتابعة.',
        processing: 'جاري المعالجة...',
        creatingProfile: 'نقوم بإنشاء ملفك الشخصي الآن.'
    }
};

// Global Event Listeners
if (typeof jQuery !== 'undefined') {
    $(document).ready(function() {
    // Level selection change (for direct enrollment)
    $(document).on('change', 'input[name="current_level"]', function() {
        const programId = $('#program_id_select').val();
        if (programId) fetchFees(programId);
    });
});
}

const getMsg = (key) => {
    const lang = document.documentElement.lang || 'en';
    return wizardMessages[lang][key] || wizardMessages.en[key];
};

function playSound(type) {
    const sounds = {
        'click': 'https://assets.mixkit.co/active_storage/sfx/2013/2013-preview.mp3',
        'whoosh': 'https://assets.mixkit.co/active_storage/sfx/2568/2568-preview.mp3'
    };
    if (sounds[type]) {
        const audio = new Audio(sounds[type]);
        audio.volume = 0.3;
        audio.play().catch(e => {});
    }
}

// STEP 0: Program Choice (Adult/Kids)
window.handleProgramSelection = function(type) {
    playSound('click');
    selectedProgramType = type;
    document.getElementById('program_type_hidden').value = type;

    // Filter program dropdown in Step 3 based on type
    const select = document.getElementById('program_id_select');
    $(select).find('option').each(function() {
        const optType = $(this).data('type'); // "Kids", "Adults", or "Both"
        const matches = !optType || optType === 'Both' || optType.toLowerCase().startsWith(type.toLowerCase());
        
        if (matches) {
            $(this).show().prop('disabled', false);
        } else {
            $(this).hide().prop('disabled', true);
        }
    });
    // Reset selection if hidden
    if ($(select).find('option:selected').is(':hidden')) {
        $(select).val('');
    }

    if (type === 'adult') {
        document.getElementById('adult-fields').style.display = 'block';
        document.getElementById('kids-fields').style.display = 'none';
    } else {
        document.getElementById('adult-fields').style.display = 'none';
        document.getElementById('kids-fields').style.display = 'block';
    }

    document.getElementById('program-selection-container').style.display = 'none';
    const enrollTypeContainer = document.getElementById('enrollment-type-container');
    enrollTypeContainer.style.display = 'block';
    enrollTypeContainer.scrollIntoView({ behavior: 'smooth', block: 'center' });
};

// STEP 0.5: Enrollment Path (Direct/Test)
window.selectEnrollmentType = function(type, el) {
    playSound('click');
    enrollmentPath = type;
    document.getElementById('enrollment_type_hidden').value = type;

    document.querySelectorAll('.enroll-type-card').forEach(c => c.classList.remove('active'));
    if (el) el.classList.add('active');

    if (type === 'test') {
        selectTestChoice('yes');
        const container = document.getElementById('test-options-container');
        if (container) container.style.display = 'none';
    } else {
        const container = document.getElementById('test-options-container');
        if (container) container.style.display = 'none';
        selectTestChoice('no');
    }

    const card = document.getElementById('wizard-card');
    card.style.display = 'block';
    setTimeout(() => card.classList.add('visible'), 50);
    card.scrollIntoView({ behavior: 'smooth', block: 'center' });
};

// STEP 1: Health Toggle
window.toggleHealthNotes = function(show) {
    if (show) $('#health-notes-wrapper').slideDown();
    else {
        $('#health-notes-wrapper').slideUp();
        $('#health_notes').val('');
    }
};

// STEP 3: Program Change & Fee Loading
window.handleProgramChange = function() {
    const select = document.getElementById('program_id_select');
    const programId = select.value;
    const preview = document.getElementById('program-info-preview');
    const text = document.getElementById('program-preview-text');
    const placeholder = document.getElementById('test-info-placeholder');

    if (programId) {
        const selectedText = select.options[select.selectedIndex].text;
        text.innerText = "Targeting: " + selectedText;
        $(preview).fadeIn();
        if (placeholder) placeholder.style.display = 'none';
        fetchFees(programId);
    } else {
        $(preview).fadeOut();
        if (placeholder) placeholder.style.display = 'block';
        resetFees();
    }
};

function fetchFees(programId) {
    const level = $('input[name="current_level"]:checked').val();
    let url = `/api/get-fee?program_id=${programId}`;
    if (level) url += `&level_name=${level}`;

    fetch(url)
        .then(r => r.json())
        .then(data => {
            if (data.success && data.fees) {
                renderFeesBreakdown(data.fees);
            }
        });
}

function renderFeesBreakdown(fees) {
    const tbody = document.getElementById('fees-breakdown-body');
    let total = 0;
    let rows = '';
    
    // Filter fees based on enrollment path
    // If path is 'test', we only want 'placement_test' fees.
    // If path is 'course', we want all others.
    const filteredFees = fees.filter(fee => {
        if (enrollmentPath === 'test') {
            return fee.type === 'placement_test';
        } else {
            return fee.type !== 'placement_test';
        }
    });

    if (filteredFees.length === 0 && enrollmentPath === 'test') {
        // Fallback for placement test if no fee is defined in DB
        total = 100;
        rows = `
            <tr class="border-bottom">
                <td class="ps-4 py-3">
                    <div class="fw-bold">Placement Test Fee</div>
                    <small class="text-muted">Standard evaluation fee</small>
                </td>
                <td class="pe-4 py-3 text-end fw-bold">100.00 ILS</td>
            </tr>
        `;
    } else {
        filteredFees.forEach(fee => {
            const amount = parseFloat(fee.amount);
            total += amount;
            
            // Format type name (e.g., 'course' -> 'Course Enrollment')
            const typeLabel = fee.type.replace(/_/g, ' ').replace(/\b\w/g, l => l.toUpperCase());
            
            rows += `
                <tr class="border-bottom">
                    <td class="ps-4 py-3">
                        <div class="fw-bold">${typeLabel}</div>
                        <small class="text-muted">${fee.level_name ? 'Level: ' + fee.level_name : 'Program general fee'}</small>
                    </td>
                    <td class="pe-4 py-3 text-end fw-bold">${amount.toFixed(2)} ILS</td>
                </tr>
            `;
        });
    }
    
    tbody.innerHTML = rows || '<tr><td colspan="2" class="text-center py-3 text-muted">No fees found for this selection</td></tr>';
    document.getElementById('display-total-due').innerText = total.toFixed(2);
    document.getElementById('total_due_hidden').value = total;
    updateRemainingDue();
}

function resetFees() {
    document.getElementById('fees-breakdown-body').innerHTML = '';
    document.getElementById('display-total-due').innerText = '0.00';
    document.getElementById('total_due_hidden').value = 0;
    updateRemainingDue();
}

window.selectTestChoice = function(choice) {
    playSound('click');
    takeTest = (choice === 'yes');
    const hiddenInput = document.getElementById('take_test_hidden');
    if (hiddenInput) hiddenInput.value = choice;
    
    // Toggle fields
    const scheduling = document.getElementById('test-scheduling-fields');
    const levelSelect = document.getElementById('skip-test-level-selection');
    const placeholder = document.getElementById('test-info-placeholder');

    if (scheduling) scheduling.style.display = takeTest ? 'block' : 'none';
    if (levelSelect) levelSelect.style.display = !takeTest ? 'block' : 'none';
    if (placeholder) placeholder.style.display = 'none'; // Hide placeholder once we have a choice

    document.querySelectorAll('.test-btn').forEach(btn => btn.classList.remove('active'));
    const btn = document.getElementById('test-' + choice);
    if (btn) btn.classList.add('active');

    if (enrollmentPath === 'test') {
        renderFeesBreakdown([{ type: 'placement_test', amount: 100, level_name: null }]);
    } else {
        handleProgramChange(); // Re-fetch program fees for direct enrollment
    }
};

// STEP 4: Payment Logic
window.selectPaymentMethod = function(id, el) {
    playSound('click');
    document.getElementById('payment_method_id_hidden').value = id;
    
    // UI update
    document.querySelectorAll('.payment-method-card').forEach(c => c.classList.remove('active'));
    el.classList.add('active');
    
    // Handle credentials
    const credsRaw = el.getAttribute('data-creds');
    const area = document.getElementById('method-details-area');
    const list = document.getElementById('credentials-list');

    if (credsRaw) {
        try {
            const creds = JSON.parse(credsRaw);
            list.innerHTML = '';
            for (const [key, value] of Object.entries(creds)) {
                if (value) {
                    let icon = 'bi-info-square';
                    if (key.includes('iban') || key.includes('bank')) icon = 'bi-bank';
                    if (key.includes('account')) icon = 'bi-hash';
                    if (key.includes('wallet') || key.includes('phone')) icon = 'bi-phone';
                    
                    list.innerHTML += `
                        <div class="credential-item border-0 shadow-none px-0 mb-3" style="background: transparent;">
                            <div class="d-flex align-items-center">
                                <i class="bi ${icon} text-primary me-3 fs-4"></i>
                                <div>
                                    <div class="credential-label text-muted small fw-bold">${key.replace(/_/g, ' ')}</div>
                                    <div class="credential-value fw-bold fs-5" id="cred-${key}">${value}</div>
                                </div>
                            </div>
                            <button type="button" class="btn btn-sm btn-outline-primary border-0 rounded-circle" 
                                    onclick="copyToClipboard('${value}', this)" title="Copy">
                                <i class="bi bi-clipboard"></i>
                            </button>
                        </div>
                    `;
                }
            }
            $(area).slideDown();
        } catch(e) { console.error("Invalid credentials JSON", e); }
    } else {
        $(area).slideUp();
    }
};

window.copyToClipboard = function(text, btn) {
    navigator.clipboard.writeText(text).then(() => {
        const icon = btn.querySelector('i');
        const oldClass = icon.className;
        icon.className = 'bi bi-check2 text-success';
        setTimeout(() => icon.className = oldClass, 2000);
    });
};

window.updateRemainingDue = function() {
    const total = parseFloat(document.getElementById('total_due_hidden').value) || 0;
    const paidInput = document.getElementsByName('student_fee_paid')[0];
    const paid = parseFloat(paidInput.value) || 0;
    const remaining = Math.max(0, total - paid);
    document.getElementById('display-amount-due').innerText = remaining.toFixed(2);
};

window.updateFileName = function(input) {
    const preview = document.getElementById('file-name-preview');
    if (input.files && input.files[0]) {
        preview.innerHTML = `<i class="bi bi-file-earmark-check me-2"></i>Selected: ${input.files[0].name}`;
        document.getElementById('receipt-dropzone').classList.add('border-success');
    }
};

// NAVIGATION
window.changeStep = function(direction) {
    if (direction > 0 && !validateCurrentStep()) return;
    
    playSound('whoosh');
    document.getElementById(`pane-${currentStep}`).classList.remove('active');
    document.querySelector(`[data-step-nav="${currentStep}"]`).classList.remove('active');
    if (direction > 0) document.querySelector(`[data-step-nav="${currentStep}"]`).classList.add('completed');

    currentStep += direction;

    if (currentStep > 5) {
        submitFinalForm();
        currentStep = 5;
        return;
    }

    document.getElementById(`pane-${currentStep}`).classList.add('active');
    document.querySelector(`[data-step-nav="${currentStep}"]`).classList.add('active');

    document.getElementById('btn-prev').style.display = currentStep === 1 ? 'none' : 'block';
    const nextBtn = document.getElementById('btn-next');
    nextBtn.innerText = currentStep === 5 ? getMsg('ok') : getMsg('next');
    
    if (currentStep === 5) nextBtn.classList.add('btn-success');
    else nextBtn.classList.remove('btn-success');
};

function validateCurrentStep() {
    $('.error-message').remove();
    $('.form-control').removeClass('is-invalid');
    let isValid = true;

    if (currentStep === 1) {
        ['name', 'name_en', 'mobile', 'email', 'dob', 'gender', 'address'].forEach(f => {
            const el = $(`[name="${f}"]`);
            if (!el.val()) { el.addClass('is-invalid'); isValid = false; }
        });
        if ($('input[name="health_status"]:checked').val() === 'yes' && !$('#health_notes').val()) {
            $('#health_notes').addClass('is-invalid');
            isValid = false;
        }
    } else if (currentStep === 2) {
        const fields = selectedProgramType === 'adult' ? ['major'] : ['parent_name', 'parent_phone', 'parent_relationship'];
        fields.forEach(f => {
            const el = $(`[name="${f}"]`);
            if (!el.val()) { el.addClass('is-invalid'); isValid = false; }
        });
    } else if (currentStep === 3) {
        if (!$('#program_id_select').val()) {
            Swal.fire({ icon: 'warning', title: 'Please select a Target Program.', customClass: { popup: 'swal-oxford-popup' } });
            isValid = false;
        } else if (takeTest) {
            ['test_date', 'preferred_days', 'preferred_time'].forEach(f => {
                if (!$(`[name="${f}"]:checked`).val() && !$(`input[name="${f}"]`).val()) isValid = false;
            });
            if (!isValid) Swal.fire({ icon: 'warning', title: 'Please complete placement scheduling.', customClass: { popup: 'swal-oxford-popup' } });
        } else if (!takeTest && !$('input[name="current_level"]:checked').val()) {
            Swal.fire({ icon: 'warning', title: 'Please select your current level.', customClass: { popup: 'swal-oxford-popup' } });
            isValid = false;
        }
    } else if (currentStep === 4) {
        if (!$('#payment_method_id_hidden').val() || !$('input[name="student_fee_paid"]').val() || !document.getElementById('receipt_input').value) {
            Swal.fire({ icon: 'warning', title: 'Please complete all payment fields and upload receipt.', customClass: { popup: 'swal-oxford-popup' } });
            isValid = false;
        }
    } else if (currentStep === 5) {
        if (!$('#agree-terms').is(':checked')) {
            Swal.fire({ icon: 'warning', title: getMsg('termsWarning'), customClass: { popup: 'swal-oxford-popup' } });
            isValid = false;
        }
    }
    return isValid;
}

window.submitFinalForm = function() {
    Swal.fire({
        title: getMsg('processing'),
        text: getMsg('creatingProfile'),
        allowOutsideClick: false,
        didOpen: () => Swal.showLoading(),
        customClass: { popup: 'swal-oxford-popup' }
    });

    const formData = new FormData(document.getElementById('registration-wizard-form'));
    fetch(window.registrationRoute || '/contact/book', {
        method: 'POST',
        body: formData,
        headers: { 'X-Requested-With': 'XMLHttpRequest' }
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            Swal.fire({ title: getMsg('successTitle'), text: getMsg('successText'), icon: 'success', confirmButtonText: getMsg('ok'), customClass: { popup: 'swal-oxford-popup' }})
            .then(() => window.location.href = '/');
        } else {
            Swal.fire({ title: getMsg('errorTitle'), text: data.message, icon: 'error', customClass: { popup: 'swal-oxford-popup' }});
        }
    })
    .catch(err => {
        Swal.fire({ icon: 'error', title: 'Network Error', text: 'Check your connection.', customClass: { popup: 'swal-oxford-popup' }});
    });
};

document.addEventListener('DOMContentLoaded', function() {
    const arabicNameInput = document.querySelector('input[name="name"]');
    if (arabicNameInput) {
        arabicNameInput.addEventListener('input', function(e) {
            const arabicRegex = /[\u0600-\u06FF\s]/;
            let value = e.target.value;
            let filteredValue = '';
            for (let i = 0; i < value.length; i++) {
                if (arabicRegex.test(value[i])) filteredValue += value[i];
            }
            if (value !== filteredValue) e.target.value = filteredValue;
        });
    }
});
