/**
 * Oxford Academy Registration Wizard JS
 * Modern, clean logic for the phased enrollment process.
 */

let currentStep = 1;
let selectedProgram = '';
let takeTest = false;

// Bilingual messages
const wizardMessages = {
    en: {
        successTitle: 'Registration Submitted!',
        successText: 'Your booking request has been submitted successfully. Our team will verify your data and contact you shortly.',
        errorTitle: 'Submission Error',
        retry: 'Retry',
        ok: 'Great!',
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
        ok: 'موافق',
        testWarning: 'يرجى تحديد ما إذا كنت ترغب في إجراء امتحان مستوى.',
        paymentWarning: 'يرجى اختيار طريقة الدفع.',
        receiptWarning: 'يرجى رفع إيصال الدفع.',
        termsWarning: 'يجب الموافقة على الشروط والأحكام للمتابعة.',
        processing: 'جاري المعالجة...',
        creatingProfile: 'نقوم بإنشاء ملفك الشخصي الآن.'
    }
};

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

window.handleProgramSelection = function(type) {
    if (selectedProgram && selectedProgram !== type) {
        document.getElementById('registration-wizard-form').reset();
        currentStep = 1;
        document.querySelectorAll('.step-pane').forEach(p => p.classList.remove('active'));
        document.getElementById('pane-1').classList.add('active');
        document.querySelectorAll('.step-indicator').forEach(s => s.classList.remove('active', 'completed'));
        document.querySelector('[data-step-nav="1"]').classList.add('active');
        document.getElementById('btn-prev').style.display = 'none';
        document.getElementById('btn-next').innerHTML = 'Next Step';
    }

    playSound('click');
    selectedProgram = type;
    document.getElementById('program_type_hidden').value = type;

    document.querySelectorAll('.program-option').forEach(opt => opt.classList.remove('active'));
    const activeOption = document.querySelector(`.program-option[onclick*="'${type}'"]`);
    if (activeOption) activeOption.classList.add('active');

    if (type === 'adult') {
        document.getElementById('adult-fields').style.display = 'block';
        document.getElementById('kids-fields').style.display = 'none';
    } else {
        document.getElementById('adult-fields').style.display = 'none';
        document.getElementById('kids-fields').style.display = 'block';
    }

    const card = document.getElementById('wizard-card');
    card.style.display = 'block';
    setTimeout(() => card.classList.add('visible'), 50);
    card.scrollIntoView({ behavior: 'smooth', block: 'center' });
};

window.selectTestChoice = function(choice) {
    playSound('click');
    takeTest = (choice === 'yes');
    document.getElementById('take_test_hidden').value = choice;
    document.getElementById('test-scheduling-fields').style.display = takeTest ? 'block' : 'none';
    
    // Toggle level selection when skipping the test
    const levelSection = document.getElementById('skip-test-level-selection');
    if (levelSection) {
        console.log('Test Choice:', choice, 'Program:', selectedProgram);
        levelSection.style.display = (!takeTest && selectedProgram !== '') ? 'block' : 'none';
    }

    document.querySelectorAll('.test-btn').forEach(btn => btn.classList.remove('active'));
    document.getElementById('test-' + choice).classList.add('active');
};

window.handlePaymentSelection = function(id, credsJson) {
    playSound('click');
    document.getElementById('payment_method_hidden').value = id;
    const creds = JSON.parse(credsJson);
    const list = document.getElementById('credentials-list');
    list.innerHTML = '';

    for (const [key, value] of Object.entries(creds)) {
        if (value) {
            list.innerHTML += `
                <div class="credential-item bg-white p-3 rounded-4 shadow-sm mb-3 border d-flex align-items-center gap-3">
                    <div class="credential-icon-box flex-shrink-0">
                        <i class="bi bi-shield-check text-primary"></i>
                    </div>
                    <div class="d-flex align-items-center gap-3 flex-grow-1">
                        <span class="review-label mb-0" style="min-width: 80px;">${key.replace(/_/g, ' ')}</span>
                        <span class="review-value mb-0">${value}</span>
                    </div>
                    <button type="button" class="copy-btn flex-shrink-0" onclick="copyToClipboard('${value}', this)">
                        <i class="bi bi-clipboard-plus me-1"></i> Copy
                    </button>
                </div>
            `;
        }
    }

    document.getElementById('method-details').style.display = 'block';
    document.querySelectorAll('.payment-card').forEach(c => {
        c.classList.remove('active');
        c.querySelector('.check-icon').style.display = 'none';
    });

    const card = event.currentTarget;
    card.classList.add('active');
    card.querySelector('.check-icon').style.display = 'block';
};

window.copyToClipboard = function(text, btn) {
    navigator.clipboard.writeText(text).then(() => {
        const original = btn.innerHTML;
        btn.innerHTML = '<i class="bi bi-check-lg"></i> Copied!';
        btn.classList.add('copied');
        setTimeout(() => {
            btn.innerHTML = original;
            btn.classList.remove('copied');
        }, 2000);
    });
};

window.changeStep = function(direction) {
    if (direction > 0 && !validateCurrentStep()) return;
    playSound('whoosh');

    let nextStep = currentStep + direction;
    
    // Skip payment (Step 4) only if they chose NOT to take the test
    if (nextStep === 4 && !takeTest && direction > 0) nextStep = 5;
    else if (nextStep === 4 && !takeTest && direction < 0) nextStep = 3;

    document.getElementById(`pane-${currentStep}`).classList.remove('active');
    document.querySelectorAll(`[data-step-nav="${currentStep}"]`).forEach(el => el.classList.remove('active'));
    if (direction > 0) document.querySelectorAll(`[data-step-nav="${currentStep}"]`).forEach(el => el.classList.add('completed'));

    currentStep = nextStep;
    if (currentStep > 5) {
        submitFinalForm();
        currentStep = 5;
        return;
    }

    if (currentStep === 5) prepareReview();

    document.getElementById(`pane-${currentStep}`).classList.add('active');
    document.querySelectorAll(`[data-step-nav="${currentStep}"]`).forEach(el => el.classList.add('active'));

    document.getElementById('btn-prev').style.display = currentStep === 1 ? 'none' : 'block';
    const nextBtn = document.getElementById('btn-next');
    nextBtn.innerHTML = currentStep === 5 ? getMsg('ok') : 'Next Step';
    
    if (currentStep === 5) nextBtn.classList.replace('btn-next', 'btn-submit');
    else nextBtn.classList.replace('btn-submit', 'btn-next');
};

function validateCurrentStep() {
    $('.error-message').remove();
    $('.form-control').removeClass('is-invalid');
    let isValid = true;

    if (currentStep === 1) {
        ['name', 'name_en', 'mobile', 'email', 'dob', 'gender'].forEach(f => {
            const el = $(`[name="${f}"]`);
            if (!el.val()) {
                el.addClass('is-invalid').after('<span class="error-message">Required</span>');
                isValid = false;
            }
        });
    } else if (currentStep === 2) {
        const fields = selectedProgram === 'adult' ? ['major'] : ['parent_name', 'parent_phone', 'parent_relationship'];
        fields.forEach(f => {
            const el = $(`[name="${f}"]`);
            if (!el.val()) {
                el.addClass('is-invalid').after('<span class="error-message">Required</span>');
                isValid = false;
            }
        });
    } else if (currentStep === 3) {
        if (!document.getElementById('take_test_hidden').value) {
            Swal.fire({ icon: 'warning', title: getMsg('testWarning'), customClass: { popup: 'swal-oxford-popup' } });
            isValid = false;
        } else if (!takeTest && selectedProgram !== '') {
            // Level is mandatory if skipping test
            if (!$('input[name="current_level"]:checked').val()) {
                Swal.fire({ icon: 'warning', title: 'Please select your level (يرجى تحديد المستوى)', customClass: { popup: 'swal-oxford-popup' } });
                isValid = false;
            }
        } else if (takeTest) {
            ['test_date', 'preferred_days', 'preferred_time'].forEach(f => {
                const el = $(`[name="${f}"]`);
                if (el.attr('type') === 'radio') {
                    if (!$(`input[name="${f}"]:checked`).val()) {
                        isValid = false;
                        $(`input[name="${f}"]`).closest('.scheduling-card').addClass('border-danger');
                    } else {
                        $(`input[name="${f}"]`).closest('.scheduling-card').removeClass('border-danger');
                    }
                } else {
                    if (!el.val()) { el.addClass('is-invalid'); isValid = false; }
                }
            });
        }
    } else if (currentStep === 4 && takeTest) {
        if (!document.getElementById('payment_method_hidden').value) {
            Swal.fire({ icon: 'warning', title: getMsg('paymentWarning'), customClass: { popup: 'swal-oxford-popup' } });
            isValid = false;
        }
        if (!document.getElementById('receipt_input').value) {
            Swal.fire({ icon: 'warning', title: getMsg('receiptWarning'), customClass: { popup: 'swal-oxford-popup' } });
            isValid = false;
        }
    } else if (currentStep === 5) {
        if ($('input[name="health_status"]:checked').val() === 'yes' && !$('#health_notes').val()) {
            $('#health_notes').addClass('is-invalid');
            isValid = false;
        }
        if (!$('#agree-terms').is(':checked')) {
            Swal.fire({ icon: 'warning', title: getMsg('termsWarning'), customClass: { popup: 'swal-oxford-popup' } });
            isValid = false;
        }
    }
    return isValid;
}

function prepareReview() {
    // Summary removed per user request
}

window.submitFinalForm = function() {
    if (!document.getElementById('agree-terms').checked) {
        Swal.fire({ icon: 'warning', title: getMsg('termsWarning'), customClass: { popup: 'swal-oxford-popup' } });
        return;
    }

    Swal.fire({
        title: getMsg('processing'),
        text: getMsg('creatingProfile'),
        allowOutsideClick: false,
        didOpen: () => Swal.showLoading(),
        customClass: { popup: 'swal-oxford-popup' }
    });

    const formData = new FormData(document.getElementById('registration-wizard-form'));
    const url = window.registrationRoute || '/contact/book';

    fetch(url, {
        method: 'POST',
        body: formData,
        headers: { 'X-Requested-With': 'XMLHttpRequest' }
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            Swal.fire({
                title: getMsg('successTitle'),
                text: getMsg('successText'),
                icon: 'success',
                confirmButtonText: getMsg('ok'),
                customClass: {
                    popup: 'swal-oxford-popup',
                    confirmButton: 'swal-oxford-confirm'
                },
                buttonsStyling: false
            }).then(() => window.location.href = '/');
        } else {
            Swal.fire({
                title: getMsg('errorTitle'),
                text: data.message,
                icon: 'error',
                confirmButtonText: getMsg('retry'),
                customClass: {
                    popup: 'swal-oxford-popup',
                    confirmButton: 'swal-oxford-confirm'
                },
                buttonsStyling: false
            });
        }
    })
    .catch(() => {
        Swal.fire({ icon: 'error', title: 'Error', text: 'An unexpected error occurred.', customClass: { popup: 'swal-oxford-popup' } });
    });
};
window.updateFileName = function(input) {
    const preview = document.getElementById('file-name-preview');
    const container = document.getElementById('receipt-dropzone');
    if (input.files && input.files[0]) {
        preview.innerHTML = `
            <div class="alert alert-success d-inline-flex align-items-center py-2 px-3 m-0 rounded-pill" style="font-size: 0.85rem;">
                <i class="bi bi-file-earmark-check-fill me-2"></i>
                ${input.files[0].name}
            </div>
        `;
        if (container) container.style.borderColor = '#50cd89';
    }
};

// Enforce Arabic only for Arabic Name field
document.addEventListener('DOMContentLoaded', function() {
    const arabicNameInput = document.querySelector('input[name="name"]');
    if (arabicNameInput) {
        arabicNameInput.addEventListener('input', function(e) {
            const arabicRegex = /[\u0600-\u06FF\s]/;
            let value = e.target.value;
            let filteredValue = '';
            for (let i = 0; i < value.length; i++) {
                if (arabicRegex.test(value[i])) {
                    filteredValue += value[i];
                }
            }
            if (value !== filteredValue) {
                e.target.value = filteredValue;
            }
        });
    }
});

function toggleHealthNotes(show) {
    const wrapper = document.getElementById('health-notes-wrapper');
    const notesInput = document.getElementById('health_notes');
    if (show) {
        $(wrapper).slideDown();
        notesInput.setAttribute('required', 'required');
    } else {
        $(wrapper).slideUp();
        notesInput.removeAttribute('required');
        notesInput.value = '';
    }
}
