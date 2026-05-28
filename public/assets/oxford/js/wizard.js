/**
 * Oxford Academy Registration Wizard JS
 * Overhauled logic for reordered steps and dynamic fee calculations.
 */

let currentStep = 1;
let selectedProgramType = ''; // adult or kids
let enrollmentPath = ''; // course or test
let takeTest = false;
let isUnderage = false; // applicant age <= 15 (any program)
let minPaymentDue = 0; // minimum required payment, derived from PROGRAM-level thresholds
let programMinPct = null;   // program's min_payment_percent (or null)
let programMinFixed = null; // program's min_payment_fixed (or null)
let currentTotalDue = 0;    // last computed total

function calculateAge(dobStr) {
    if (!dobStr) return null;
    const dob = new Date(dobStr);
    if (isNaN(dob.getTime())) return null;
    const today = new Date();
    let age = today.getFullYear() - dob.getFullYear();
    const m = today.getMonth() - dob.getMonth();
    if (m < 0 || (m === 0 && today.getDate() < dob.getDate())) age--;
    return age;
}

window.checkAgeRequirement = function(dobStr) {
    const age = calculateAge(dobStr);
    const banner = document.getElementById('underage-banner');

    isUnderage = (age !== null && age <= 15);

    // Show banner only for adult program when applicant is underage
    // (kids program already implies guardian info — no banner needed)
    if (banner) {
        const showBanner = (selectedProgramType === 'adult' && isUnderage);
        banner.style.display = showBanner ? 'block' : 'none';
    }

    updateStep2Visibility();
};

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

    // show/hide major field inside step 1 (adult-only)
    const majorStep1 = document.getElementById('major-in-step1');
    if (majorStep1) majorStep1.style.display = (type === 'adult') ? 'block' : 'none';

    // Re-check age if DOB was already provided (also refreshes step-2 visibility)
    const dobEl = document.querySelector('input[name="dob"]');
    if (dobEl && dobEl.value) {
        checkAgeRequirement(dobEl.value);
    } else {
        updateStep2Visibility();
    }

    document.getElementById('program-selection-container').style.display = 'none';
    const enrollTypeContainer = document.getElementById('enrollment-type-container');
    enrollTypeContainer.style.display = 'block';
    enrollTypeContainer.scrollIntoView({ behavior: 'smooth', block: 'center' });
};

// STEP 0.5: Enrollment Path (Direct/Test)
window.selectEnrollmentType = function(type, el) {
    playSound('click');

    // If switching between Direct Enrollment and Placement Test after starting,
    // restart the wizard from step 1 with a clean form.
    if (enrollmentPath && enrollmentPath !== type) {
        resetWizard();
    }

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

function resetWizard() {
    // Reset form fields, but preserve the hidden program_type since the user is mid-flow.
    const form = document.getElementById('registration-wizard-form');
    if (form) {
        const programType = document.getElementById('program_type_hidden').value;
        form.reset();
        document.getElementById('program_type_hidden').value = programType;
    }

    // Reset step UI to step 1
    currentStep = 1;
    document.querySelectorAll('.step-pane').forEach(p => p.classList.remove('active'));
    const pane1 = document.getElementById('pane-1');
    if (pane1) pane1.classList.add('active');

    document.querySelectorAll('.step-indicator').forEach(i => i.classList.remove('active', 'completed'));
    const ind1 = document.querySelector('[data-step-for="1"]');
    if (ind1) ind1.classList.add('active');

    const trackFill = document.getElementById('step-track-fill');
    if (trackFill) trackFill.style.width = '0%';

    // Hide underage banner and reset state
    isUnderage = false;
    const banner = document.getElementById('underage-banner');
    if (banner) banner.style.display = 'none';

    // Hide health notes
    const healthWrap = document.getElementById('health-notes-wrapper');
    if (healthWrap) healthWrap.style.display = 'none';

    // Clear validation states
    document.querySelectorAll('.form-control').forEach(el => {
        el.classList.remove('is-invalid', 'is-valid');
    });
    document.querySelectorAll('.invalid-feedback, .valid-feedback, .input-status-icon').forEach(el => el.remove());

    // Clear fees and payment selection
    resetFees();
    document.querySelectorAll('.payment-method-card').forEach(c => c.classList.remove('active'));
    const methodArea = document.getElementById('method-details-area');
    if (methodArea) methodArea.style.display = 'none';
    const filePreview = document.getElementById('file-name-preview');
    if (filePreview) filePreview.innerHTML = '';

    // Buttons
    document.getElementById('btn-prev').style.display = 'none';
    document.getElementById('btn-next').innerText = getMsg('next');
    document.getElementById('btn-next').classList.remove('btn-success');
}

// Load relationships from the API and populate the parent relationship dropdown
let _relationships = [];
function loadRelationships() {
    const select = document.getElementById('parent_relationship_select');
    if (!select || _relationships.length) return;
    fetch('/api/relationships', { cache: 'no-store' })
        .then(r => r.json())
        .then(data => {
            if (!data || !data.success || !Array.isArray(data.items)) return;
            _relationships = data.items;
            data.items.forEach(item => {
                const opt = document.createElement('option');
                opt.value = item.slug;
                opt.dataset.isOther = item.is_other ? '1' : '0';
                opt.dataset.nameAr = item.name_ar || '';
                opt.dataset.nameEn = item.name_en || '';
                opt.textContent = item.name_ar + (item.name_en ? ' (' + item.name_en + ')' : '');
                select.appendChild(opt);
            });
        })
        .catch(() => {});
}

window.handleRelationshipChange = function() {
    const select = document.getElementById('parent_relationship_select');
    const wrap   = document.getElementById('parent_relationship_other_wrap');
    const other  = document.getElementById('parent_relationship_other');
    if (!select || !wrap) return;
    const opt = select.options[select.selectedIndex];
    const isOther = opt && opt.dataset.isOther === '1';
    wrap.style.display = isOther ? 'block' : 'none';
    if (!isOther && other) other.value = '';
};

document.addEventListener('DOMContentLoaded', loadRelationships);

// STEP 1: Health Toggle
window.toggleHealthNotes = function(show) {
    if (show) $('#health-notes-wrapper').slideDown();
    else {
        $('#health-notes-wrapper').slideUp();
        $('#health_notes').val('');
    }
};

function updateStep2Visibility() {
    // Step 2 (Guardian Info) shows when: kids program, OR any program with underage applicant.
    const stepIndicator = document.querySelector('[data-step-for="2"]');
    const shouldShow = (selectedProgramType === 'kids') || isUnderage;
    if (stepIndicator) stepIndicator.style.display = shouldShow ? 'flex' : 'none';
    refreshStepNavNumbers();
}

function getIndicatorForStep(step) {
    return document.querySelector(`[data-step-for="${step}"]`);
}

function refreshStepNavNumbers() {
    const indicators = Array.from(document.querySelectorAll('.step-indicator'))
        .filter(i => getComputedStyle(i).display !== 'none');
    indicators.forEach((el, idx) => {
        const numEl = el.querySelector('.step-num-text');
        if (numEl) numEl.innerText = (idx + 1).toString();
    });
}

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
            if (data.success) {
                // Store program-level min-payment thresholds (may be null if admin didn't set them)
                programMinPct   = (data.min_payment_percent !== undefined) ? data.min_payment_percent : null;
                programMinFixed = (data.min_payment_fixed   !== undefined) ? data.min_payment_fixed   : null;
                if (data.fees) renderFeesBreakdown(data.fees);
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
    currentTotalDue = total;

    // Program-level minimum payment: higher of (total * pct / 100) and fixed; capped at total
    const pct   = parseFloat(programMinPct);
    const fixed = parseFloat(programMinFixed);
    const byPct = !isNaN(pct)   ? (total * pct / 100) : 0;
    const byFix = !isNaN(fixed) ? fixed               : 0;
    minPaymentDue = Math.min(Math.max(byPct, byFix), total);

    // Keep the always-visible hint hidden — we only surface a notice when the
    // user actually enters a sub-minimum amount (handled in updateRemainingDue).
    const hint = document.getElementById('min-payment-hint');
    const minValEl = document.getElementById('min-payment-value');
    if (hint) hint.style.display = 'none';
    if (minValEl) minValEl.innerText = (minPaymentDue || 0).toFixed(2);

    // Default the Paid Amount to the full total (user may change it down to the minimum)
    const paidInput = document.getElementsByName('student_fee_paid')[0];
    if (paidInput) {
        paidInput.min = (minPaymentDue || 0).toFixed(2);
        paidInput.value = total.toFixed(2);
    }

    updateRemainingDue();
    clearPaidError();
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
    const dateSlot = document.getElementById('test-date-selection');

    if (scheduling) scheduling.style.display = takeTest ? 'block' : 'none';
    if (levelSelect) levelSelect.style.display = !takeTest ? 'block' : 'none';
    if (dateSlot) dateSlot.style.display = takeTest ? 'block' : 'none';
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
                    const lk = String(key).toLowerCase();
                    let icon = 'bi-info-circle';
                    if (lk.includes('iban') || lk.includes('bank')) icon = 'bi-bank';
                    else if (lk.includes('account') || lk.includes('number')) icon = 'bi-hash';
                    else if (lk.includes('wallet') || lk.includes('phone') || lk.includes('mobile')) icon = 'bi-phone';
                    else if (lk.includes('name') || lk.includes('holder') || lk.includes('beneficiary')) icon = 'bi-person-vcard';
                    else if (lk.includes('email')) icon = 'bi-envelope';
                    else if (lk.includes('swift') || lk.includes('bic') || lk.includes('code')) icon = 'bi-shield-lock';
                    else if (lk.includes('branch')) icon = 'bi-geo-alt';

                    const label = String(key).replace(/_/g, ' ').replace(/\b\w/g, c => c.toUpperCase());
                    const safeValue = String(value).replace(/"/g, '&quot;');

                    list.innerHTML += `
                        <div class="credential-card">
                            <div class="credential-icon"><i class="bi ${icon}"></i></div>
                            <div class="credential-body">
                                <div class="credential-label">${label}</div>
                                <div class="credential-value" id="cred-${key}">${value}</div>
                            </div>
                            <button type="button" class="credential-copy-btn"
                                    onclick="copyToClipboard('${safeValue}', this)" title="Copy">
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
    if (!paidInput) return;

    // Block negative values immediately
    if (paidInput.value !== '' && parseFloat(paidInput.value) < 0) {
        paidInput.value = 0;
    }

    const paid = parseFloat(paidInput.value) || 0;
    const remaining = Math.max(0, total - paid);
    document.getElementById('display-amount-due').innerText = remaining.toFixed(2);

    // Live minimum check (don't block typing, just flag visually)
    const errEl    = document.getElementById('min-payment-error');
    const errText  = document.getElementById('min-payment-error-text');
    const errMinEl = document.getElementById('min-payment-error-amount');
    if (paid > 0 && minPaymentDue > 0 && paid < minPaymentDue) {
        paidInput.classList.add('is-invalid');
        if (errMinEl) errMinEl.innerText = minPaymentDue.toFixed(2);
        if (errText) {
            errText.innerText = 'المبلغ الذي أدخلته (' + paid.toFixed(2) + ' ILS) أقل من الحد الأدنى المسموح. يرجى رفع المبلغ لإتمام التسجيل.';
        }
        if (errEl) errEl.style.display = 'grid';
    } else {
        paidInput.classList.remove('is-invalid');
        if (errEl) errEl.style.display = 'none';
    }
};

function clearPaidError() {
    const paidInput = document.getElementsByName('student_fee_paid')[0];
    const errEl = document.getElementById('min-payment-error');
    if (paidInput) paidInput.classList.remove('is-invalid');
    if (errEl) errEl.style.display = 'none';
}

window.enforcePaidMinimum = function() {
    const paidInput = document.getElementsByName('student_fee_paid')[0];
    if (!paidInput) return;
    let paid = parseFloat(paidInput.value);
    if (isNaN(paid) || paid < 0) {
        paidInput.value = 0;
        paid = 0;
    }
    updateRemainingDue();
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
    // decide target step, skipping Step 2 when guardian info isn't needed
    const skipStep2 = !((selectedProgramType === 'kids') || isUnderage);
    let targetStep = currentStep + direction;
    if (direction > 0 && targetStep === 2 && skipStep2) targetStep = 3;
    if (direction < 0 && targetStep === 2 && skipStep2) targetStep = 1;

    document.getElementById(`pane-${currentStep}`).classList.remove('active');
    const oldIndicator = getIndicatorForStep(currentStep);
    if (oldIndicator) oldIndicator.classList.remove('active');
    if (direction > 0 && oldIndicator) oldIndicator.classList.add('completed');

    currentStep = targetStep;

    if (currentStep > 5) {
        submitFinalForm();
        currentStep = 5;
        return;
    }

    document.getElementById(`pane-${currentStep}`).classList.add('active');
    const newIndicator = getIndicatorForStep(currentStep);
    if (newIndicator) newIndicator.classList.add('active');

    // Drive progress track fill based on visible indicators
    const trackFill = document.getElementById('step-track-fill');
    if (trackFill) {
        const visibleIndicators = Array.from(document.querySelectorAll('.step-indicator'))
            .filter(i => getComputedStyle(i).display !== 'none');
        const totalSteps = visibleIndicators.length || 1;
        // find logical index of currentStep among visible indicators
        const idx = visibleIndicators.findIndex(i => i.getAttribute('data-step-for') == String(currentStep));
        const position = idx >= 0 ? idx : 0;
        const pct = (position / Math.max(1, totalSteps - 1)) * 100;
        trackFill.style.width = pct + '%';
    }

    document.getElementById('btn-prev').style.display = currentStep === 1 ? 'none' : 'block';
    const nextBtn = document.getElementById('btn-next');
    const visibleIndicators = Array.from(document.querySelectorAll('.step-indicator')).filter(i => getComputedStyle(i).display !== 'none');
    const isLast = visibleIndicators.length && visibleIndicators[visibleIndicators.length - 1].getAttribute('data-step-for') == String(currentStep);
    nextBtn.innerText = isLast ? getMsg('ok') : getMsg('next');

    if (isLast) nextBtn.classList.add('btn-success');
    else nextBtn.classList.remove('btn-success');

    // Scroll back to the top of the wizard form on each step change
    const card = document.getElementById('wizard-card');
    if (card) card.scrollIntoView({ behavior: 'smooth', block: 'start' });
};

function validateCurrentStep() {
    $('.error-message').remove();
    $('.form-control').removeClass('is-invalid');
    let isValid = true;

    if (currentStep === 1) {
        const fieldsStep1 = ['name', 'name_en', 'mobile', 'email', 'dob', 'gender', 'address'];
        if (selectedProgramType === 'adult') fieldsStep1.push('major');
        fieldsStep1.forEach(f => {
            const el = $(`[name="${f}"]`);
            if (!el.val()) { el.addClass('is-invalid'); isValid = false; }
        });
        // English-only check for name_en
        const nameEn = ($('input[name="name_en"]').val() || '').trim();
        if (nameEn && !/^[A-Za-z][A-Za-z\s'\-]{2,}$/.test(nameEn)) {
            $('input[name="name_en"]').addClass('is-invalid');
            isValid = false;
            Swal.fire({
                icon: 'warning',
                title: 'الاسم الإنجليزي غير صحيح',
                text: 'يجب أن يحتوي على حروف إنجليزية فقط (لا يقبل العربية ولا الأرقام).',
                customClass: { popup: 'swal-oxford-popup', confirmButton: 'swal-oxford-confirm' },
                buttonsStyling: false,
            });
        }
        // Mobile: digits only, 9–15 length
        const mobileVal = ($('input[name="mobile"]').val() || '').trim();
        if (mobileVal && !/^[0-9]{9,15}$/.test(mobileVal)) {
            $('input[name="mobile"]').addClass('is-invalid');
            isValid = false;
            if (isValid !== false /* deduped */) {} // no-op, message below
            Swal.fire({
                icon: 'warning',
                title: 'رقم الجوال غير صحيح',
                text: 'يجب إدخال أرقام فقط (٩ إلى ١٥ رقم) — بدون مسافات أو رموز أو حروف.',
                customClass: { popup: 'swal-oxford-popup', confirmButton: 'swal-oxford-confirm' },
                buttonsStyling: false,
            });
        }
        if ($('input[name="health_status"]:checked').val() === 'yes' && !$('#health_notes').val()) {
            $('#health_notes').addClass('is-invalid');
            isValid = false;
        }
    } else if (currentStep === 2) {
        ['parent_name', 'parent_phone', 'parent_relationship'].forEach(f => {
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
        const paidVal = parseFloat($('input[name="student_fee_paid"]').val());
        const receiptInput = document.getElementById('receipt_input');
        const hasReceipt = receiptInput && receiptInput.files && receiptInput.files.length > 0;

        if (!$('#payment_method_id_hidden').val()) {
            Swal.fire({ icon: 'warning', title: 'يرجى اختيار طريقة الدفع', customClass: { popup: 'swal-oxford-popup' } });
            isValid = false;
        } else if (!$('input[name="student_fee_paid"]').val()) {
            Swal.fire({ icon: 'warning', title: 'يرجى إدخال المبلغ المدفوع', customClass: { popup: 'swal-oxford-popup' } });
            isValid = false;
        } else if (isNaN(paidVal) || paidVal < 0) {
            Swal.fire({ icon: 'warning', title: 'المبلغ المدفوع غير صالح', text: 'لا يمكن إدخال قيمة سالبة.', customClass: { popup: 'swal-oxford-popup' } });
            isValid = false;
        } else if (minPaymentDue > 0 && paidVal < minPaymentDue) {
            Swal.fire({
                icon: 'warning',
                title: 'المبلغ أقل من الحد الأدنى',
                text: 'الحد الأدنى المطلوب للدفع هو ' + minPaymentDue.toFixed(2) + ' ILS، لا يمكنك المتابعة بمبلغ أقل.',
                customClass: { popup: 'swal-oxford-popup' }
            });
            const paidInput = document.getElementsByName('student_fee_paid')[0];
            if (paidInput) {
                paidInput.classList.add('is-invalid');
                paidInput.focus();
            }
            isValid = false;
        } else if (!hasReceipt) {
            Swal.fire({
                icon: 'warning',
                title: 'يرجى رفع إيصال الدفع',
                text: 'لا يمكن الانتقال إلى الخطوة الأخيرة قبل رفع صورة/ملف إيصال الدفع لتأكيد تحويل المبلغ.',
                customClass: { popup: 'swal-oxford-popup' }
            });
            const dropzone = document.getElementById('receipt-dropzone');
            if (dropzone) {
                dropzone.classList.add('receipt-required-highlight');
                dropzone.scrollIntoView({ behavior: 'smooth', block: 'center' });
                setTimeout(() => dropzone.classList.remove('receipt-required-highlight'), 2200);
            }
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

// Friendly Arabic labels for known fields (fallback to the field key if missing)
const fieldLabelsAr = {
    name: 'الاسم بالعربية',
    name_en: 'الاسم بالإنجليزية',
    email: 'البريد الإلكتروني',
    mobile: 'رقم الجوال',
    dob: 'تاريخ الميلاد',
    gender: 'الجنس',
    address: 'العنوان',
    program_type: 'نوع البرنامج',
    enrollment_type: 'نوع التسجيل',
    program_id: 'البرنامج المستهدف',
    current_level: 'المستوى الحالي',
    test_date: 'تاريخ الاختبار',
    preferred_days: 'الأيام المفضلة',
    preferred_time: 'الوقت المفضل',
    health_status: 'الحالة الصحية',
    health_notes: 'تفاصيل الحالة الصحية',
    parent_name: 'اسم ولي الأمر',
    parent_phone: 'جوال ولي الأمر',
    parent_relationship: 'صلة القرابة',
    parent_email: 'بريد ولي الأمر',
    major: 'التخصص / المهنة',
    payment_method_id: 'طريقة الدفع',
    student_fee_paid: 'المبلغ المدفوع',
    payment_receipt: 'إيصال الدفع',
    general_notes: 'الملاحظات العامة'
};

function buildValidationErrorsHTML(errorsObj) {
    if (!errorsObj || typeof errorsObj !== 'object') return '';
    const lang = document.documentElement.lang || 'en';
    const dir  = lang === 'ar' ? 'rtl' : 'ltr';
    const align = lang === 'ar' ? 'right' : 'left';
    let html = '<ul style="list-style:none; padding:0; margin:14px 0 0; text-align:'+align+'; direction:'+dir+';">';
    Object.entries(errorsObj).forEach(([field, msgs]) => {
        const label = fieldLabelsAr[field] || field;
        const list = Array.isArray(msgs) ? msgs : [msgs];
        list.forEach(m => {
            html += `
                <li style="display:flex; gap:10px; align-items:flex-start; padding:10px 12px; margin-bottom:8px; background:#fff5f5; border-${align === 'right' ? 'right' : 'left'}:4px solid #e53e3e; border-radius:8px; color:#742a2a; font-size:0.92rem; line-height:1.5;">
                    <i class="bi bi-exclamation-circle-fill" style="color:#e53e3e; font-size:1.05rem; margin-top:2px;"></i>
                    <div>
                        <strong style="display:block; color:#003366; font-size:0.85rem; margin-bottom:2px;">${label}</strong>
                        <span>${m}</span>
                    </div>
                </li>`;
        });
    });
    html += '</ul>';
    return html;
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
        headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
    })
    .then(async (r) => {
        const json = await r.json().catch(() => ({}));
        return { ok: r.ok, status: r.status, json };
    })
    .then(({ ok, status, json }) => {
        const data = json || {};
        const lang = document.documentElement.lang || 'en';

        // Success
        if (ok && data.success) {
            Swal.fire({
                title: getMsg('successTitle'),
                text: getMsg('successText'),
                icon: 'success',
                confirmButtonText: getMsg('ok'),
                customClass: { popup: 'swal-oxford-popup', confirmButton: 'swal-oxford-confirm' },
                buttonsStyling: false
            }).then(() => window.location.href = '/');
            return;
        }

        // Validation errors (Laravel 422 or {status:'error', errors:{...}})
        const hasFieldErrors = data.errors && typeof data.errors === 'object' && Object.keys(data.errors).length > 0;
        if (status === 422 || hasFieldErrors) {
            const html = buildValidationErrorsHTML(data.errors);
            const count = data.errors ? Object.keys(data.errors).length : 0;
            Swal.fire({
                icon: 'error',
                title: lang === 'ar' ? 'يرجى تصحيح الحقول التالية' : 'Please correct the following',
                html: html || (lang === 'ar' ? 'حدث خطأ في التحقق من البيانات.' : 'Validation error.'),
                footer: count ? (lang === 'ar' ? `<small>${count} حقل بحاجة لمراجعة</small>` : `<small>${count} field(s) need attention</small>`) : '',
                confirmButtonText: lang === 'ar' ? 'حسناً، سأصحح' : 'OK, I will fix',
                customClass: { popup: 'swal-oxford-popup swal-validation', confirmButton: 'swal-oxford-confirm' },
                buttonsStyling: false,
                width: 560
            });
            return;
        }

        // Generic server error with a message
        Swal.fire({
            icon: 'error',
            title: getMsg('errorTitle'),
            text: data.message || (lang === 'ar' ? 'حدث خطأ غير متوقع، يرجى المحاولة مرة أخرى.' : 'Unexpected error, please try again.'),
            confirmButtonText: lang === 'ar' ? 'موافق' : 'OK',
            customClass: { popup: 'swal-oxford-popup', confirmButton: 'swal-oxford-confirm' },
            buttonsStyling: false
        });
    })
    .catch(err => {
        const lang = document.documentElement.lang || 'en';
        Swal.fire({
            icon: 'error',
            title: lang === 'ar' ? 'خطأ في الاتصال' : 'Network Error',
            text: lang === 'ar' ? 'تعذّر الاتصال بالخادم، تحقق من الإنترنت.' : 'Could not reach the server. Check your connection.',
            confirmButtonText: lang === 'ar' ? 'موافق' : 'OK',
            customClass: { popup: 'swal-oxford-popup', confirmButton: 'swal-oxford-confirm' },
            buttonsStyling: false
        });
    });
};

// Realtime input filters (also exposed globally for inline oninput= attributes)
window.filterEnglishOnly = function(el) {
    // Allow: A-Z, a-z, space, apostrophe, hyphen
    const filtered = (el.value || '').replace(/[^A-Za-z\s'\-]/g, '');
    if (el.value !== filtered) el.value = filtered;
};

window.filterPhoneDigits = function(el) {
    // Strip everything except digits
    const filtered = (el.value || '').replace(/\D/g, '');
    if (el.value !== filtered) el.value = filtered;
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

    // Belt-and-suspenders: re-bind English & phone filters
    const en = document.querySelector('input[name="name_en"]');
    if (en) en.addEventListener('input', () => window.filterEnglishOnly(en));
    const mob = document.querySelector('input[name="mobile"]');
    if (mob) {
        mob.addEventListener('input', () => window.filterPhoneDigits(mob));
        // Block paste of non-digit content
        mob.addEventListener('paste', (e) => {
            e.preventDefault();
            const text = (e.clipboardData || window.clipboardData).getData('text');
            const digits = text.replace(/\D/g, '');
            const start = mob.selectionStart, end = mob.selectionEnd;
            mob.value = (mob.value.slice(0, start) + digits + mob.value.slice(end)).slice(0, 15);
        });
    }
});
