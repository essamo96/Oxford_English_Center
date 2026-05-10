/**
 * Book A Course - Form Validation & UX
 * Modularized client-side validation for professional UX
 * 
 * NOTE: Server-side Laravel validation is PRIMARY
 * Client-side validation is for UX enhancement only
 */

const BookingFormValidator = {
    formId: 'book-course-form',
    form: null,
    config: {
        highlightDuration: 300,
        validationDebounce: 300
    },

    /**
     * Initialize form validation
     */
    init() {
        this.form = document.getElementById(this.formId);
        
        if (!this.form) {
            console.warn(`Form with ID "${this.formId}" not found.`);
            return;
        }

        this.attachEventListeners();
    },

    /**
     * Attach all event listeners
     */
    attachEventListeners() {
        // Form submission
        this.form.addEventListener('submit', (e) => this.handleSubmit(e));

        // Real-time validation on input/change
        const inputs = this.form.querySelectorAll('input, textarea, select');
        inputs.forEach(input => {
            // Blur event - validate when leaving field
            input.addEventListener('blur', () => this.validateField(input));

            // Input/Change event - remove error when correcting
            input.addEventListener('input', () => {
                if (input.value.trim() || input.type === 'checkbox') {
                    this.clearFieldError(input);
                }
            });

            input.addEventListener('change', () => {
                if (input.value.trim() || input.type === 'checkbox') {
                    this.clearFieldError(input);
                }
            });
        });
    },

    /**
     * Handle form submission
     */
    handleSubmit(e) {
        // Let Laravel handle validation - don't prevent default
        // This allows Laravel validation errors to be shown under fields
        
        // Optional: Show loading state
        const submitBtn = this.form.querySelector('.btn-submit');
        if (submitBtn) {
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="fa fa-spinner fa-spin"></i> Processing...';
        }

        // Form will submit normally to controller
        // Controller will validate and either show errors or redirect with success
    },

    /**
     * Validate single field
     */
    validateField(input) {
        if (!input.hasAttribute('required')) {
            return true;
        }

        const isValid = input.value.trim() !== '';
        
        if (!isValid) {
            this.showFieldError(input);
        } else {
            this.clearFieldError(input);
        }

        return isValid;
    },

    /**
     * Show error for field
     */
    showFieldError(input) {
        const formGroup = input.parentElement;
        
        if (formGroup && !formGroup.classList.contains('has-error')) {
            formGroup.classList.add('has-error');
        }
    },

    /**
     * Clear error from field
     */
    clearFieldError(input) {
        const formGroup = input.parentElement;
        
        if (formGroup) {
            formGroup.classList.remove('has-error');
        }

        // If checkbox, also clear parent group
        if (input.type === 'checkbox') {
            const checkboxGroup = input.closest('.checkbox-group');
            if (checkboxGroup) {
                checkboxGroup.classList.remove('has-error');
            }
        }
    },

    /**
     * Clear all field errors
     */
    clearAllErrors() {
        this.form.querySelectorAll('.has-error').forEach(element => {
            element.classList.remove('has-error');
        });
    },

    /**
     * Focus first invalid field
     */
    focusFirstInvalidField() {
        const firstError = this.form.querySelector('.has-error input, .has-error textarea, .has-error select');
        if (firstError) {
            firstError.focus();
            firstError.scrollIntoView({ behavior: 'smooth', block: 'center' });
        }
    }
};

// Export for testing (if using modules)
if (typeof module !== 'undefined' && module.exports) {
    module.exports = BookingFormValidator;
}
