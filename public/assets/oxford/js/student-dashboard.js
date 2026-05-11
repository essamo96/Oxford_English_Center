/* ═══════════════════════════════════════════════════════
   STUDENT DASHBOARD — Enhanced JavaScript
   Modern ES6+ with Modular Architecture
═══════════════════════════════════════════════════════ */

// ─────────────────────────────────────────────────────
// CONFIGURATION
// ─────────────────────────────────────────────────────
const DASHBOARD_CONFIG = {
    selectors: {
        form: '#checkout-form',
        tabContent: '.tab-content',
        navLink: '.profile-nav-link',
        fileInput: '#fileToUpload',
        profileImg: '#profileImg',
        datePicker: '.date-picker',
        fabButton: '.ox-fab-menu',
        offcanvas: '#oxDrawer',
    },
    endpoints: {
        groupInfo: '/students/group-info',
        notifications: '/student/notifications',
        marks: '/student/marks',
        progress: '/student/progress',
        profileUpdate: '/student/edit-profile',
        messageSubmit: '/student/messages',
        groupCodeCheck: '/grope/code/check',
        askUpdate: '/ask-update-profile',
    },
    animation: {
        duration: 400,
        easing: 'cubic-bezier(0.4, 0, 0.2, 1)',
    },
};

// ─────────────────────────────────────────────────────
// UTILITY FUNCTIONS
// ─────────────────────────────────────────────────────

/**
 * DOM Utility Functions
 */
const DOM = {
    /**
     * Safely query single element
     */
    get: (selector) => {
        try {
            return document.querySelector(selector);
        } catch (e) {
            console.warn(`Failed to query selector: ${selector}`);
            return null;
        }
    },

    /**
     * Query multiple elements
     */
    getAll: (selector) => {
        try {
            return Array.from(document.querySelectorAll(selector));
        } catch (e) {
            console.warn(`Failed to query selector: ${selector}`);
            return [];
        }
    },

    /**
     * Create element with attributes
     */
    create: (tag, classes = '', html = '') => {
        const element = document.createElement(tag);
        if (classes) element.className = classes;
        if (html) element.innerHTML = html;
        return element;
    },

    /**
     * Add event listener with auto cleanup
     */
    on: (element, event, handler, options = {}) => {
        if (!element) return;
        element.addEventListener(event, handler, options);
        return () => element.removeEventListener(event, handler);
    },

    /**
     * Show loader
     */
    showLoader: (target) => {
        target.innerHTML = `
            <div class="modern-card">
                <div class="ox-loader-wrapper">
                    <div class="ox-sophisticated-loader">
                        <div class="ox-loader-ring"></div>
                        <img src="${window.BASE_URL}assets/oxford/img/logo.png" class="ox-loader-logo">
                    </div>
                    <p class="text-muted fw-bold">Loading...</p>
                </div>
            </div>
        `;
    },
};

/**
 * HTTP Request Handler
 */
const HTTP = {
    /**
     * POST request with error handling
     */
    post: async (url, data = {}) => {
        try {
            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || 
                            document.querySelector('input[name="_token"]')?.value;

            const response = await fetch(url, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'X-Requested-With': 'XMLHttpRequest',
                },
                body: JSON.stringify(data),
                credentials: 'same-origin',
            });

            if (!response.ok) {
                throw new Error(`HTTP ${response.status}: ${response.statusText}`);
            }

            return await response.json();
        } catch (error) {
            console.error('HTTP POST Error:', error);
            HTTP.showError(error.message);
            throw error;
        }
    },

    /**
     * GET request
     */
    get: async (url) => {
        try {
            const response = await fetch(url, {
                method: 'GET',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                },
                credentials: 'same-origin',
            });

            if (!response.ok) {
                throw new Error(`HTTP ${response.status}: ${response.statusText}`);
            }

            return await response.text();
        } catch (error) {
            console.error('HTTP GET Error:', error);
            HTTP.showError(error.message);
            throw error;
        }
    },

    /**
     * Show error notification
     */
    showError: (message) => {
        if (window.Swal) {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: message,
                confirmButtonColor: '#002147',
            });
        } else {
            alert(`Error: ${message}`);
        }
    },

    /**
     * Show success notification
     */
    showSuccess: (message) => {
        if (window.Swal) {
            Swal.fire({
                icon: 'success',
                title: 'Success',
                text: message,
                confirmButtonColor: '#FDC800',
                confirmButtonText: 'Ok',
            });
        } else {
            alert(message);
        }
    },
};

// ─────────────────────────────────────────────────────
// TAB SYSTEM
// ─────────────────────────────────────────────────────

class TabSystem {
    constructor() {
        this.activeTab = null;
        this.init();
    }

    init() {
        DOM.getAll('[data-toggle="tab"]').forEach(link => {
            DOM.on(link, 'click', (e) => this.handleTabClick(e));
        });

        // Dismiss mobile drawer when tab is clicked
        DOM.getAll('[data-bs-dismiss="offcanvas"]').forEach(el => {
            DOM.on(el, 'click', () => this.closeOffcanvas());
        });
    }

    handleTabClick(event) {
        event.preventDefault();
        const link = event.currentTarget;
        const tabId = link.getAttribute('href');

        // Update active states
        DOM.getAll('.profile-nav-link').forEach(el => {
            el.parentElement?.classList.remove('active');
        });

        link.parentElement?.classList.add('active');

        // Show tab with animation
        const tabPane = DOM.get(tabId);
        if (tabPane) {
            // Hide all tabs
            DOM.getAll('.tab-pane').forEach(pane => {
                pane.classList.remove('active', 'in');
                pane.style.display = 'none';
            });

            // Show active tab
            tabPane.classList.add('active', 'in');
            tabPane.style.display = 'block';
        }

        // Handle special tab actions
        this.handleSpecialTabs(link, tabId);
    }

    handleSpecialTabs(link, tabId) {
        const classMap = {
            'Markstudent': 'loadStudentMarks',
            'StudentGroupsProgress': 'loadStudentProgress',
            'AdminNotify': 'loadNotifications',
            'joinG': 'loadGroupInfo',
        };

        for (const [className, method] of Object.entries(classMap)) {
            if (link.classList.contains(className) && window[method]) {
                const studentId = link.dataset.student_id;
                const groupId = link.dataset.group_id;
                window[method](studentId, groupId, tabId);
                break;
            }
        }
    }

    closeOffcanvas() {
        const offcanvas = DOM.get(DASHBOARD_CONFIG.selectors.offcanvas);
        if (offcanvas && window.bootstrap?.Offcanvas) {
            window.bootstrap.Offcanvas.getInstance(offcanvas)?.hide();
        }
    }
}

// ─────────────────────────────────────────────────────
// AJAX DATA LOADERS
// ─────────────────────────────────────────────────────

class DataLoader {
    /**
     * Load student marks/grades
     */
    static async loadStudentMarks(studentId, tabId) {
        const target = DOM.get(`#${tabId}`);
        if (!target) return;

        DOM.showLoader(target);

        try {
            const response = await HTTP.post('/student/showGroueMarks', {
                student_id: studentId,
            });

            target.innerHTML = response;
            target.classList.add('ajax-loaded-content');
        } catch (error) {
            target.innerHTML = `
                <div class="modern-card text-center py-5">
                    <p class="text-danger">Failed to load marks. Please try again.</p>
                </div>
            `;
        }
    }

    /**
     * Load student progress
     */
    static async loadStudentProgress(studentId, tabId) {
        const target = DOM.get(`#${tabId}`);
        if (!target) return;

        DOM.showLoader(target);

        try {
            const response = await HTTP.post('/student/showGroueProgress', {
                student_id: studentId,
            });

            target.innerHTML = response;
            target.classList.add('ajax-loaded-content');
        } catch (error) {
            target.innerHTML = `
                <div class="modern-card text-center py-5">
                    <p class="text-danger">Failed to load progress. Please try again.</p>
                </div>
            `;
        }
    }

    /**
     * Load notifications
     */
    static async loadNotifications(tabId) {
        const target = DOM.get(`#${tabId}`);
        if (!target) return;

        DOM.showLoader(target);

        try {
            const html = await HTTP.get('/student/notifications');
            target.innerHTML = html;
            target.classList.add('ajax-loaded-content');
        } catch (error) {
            target.innerHTML = `
                <div class="modern-card text-center py-5">
                    <p class="text-danger">Failed to load notifications. Please try again.</p>
                </div>
            `;
        }
    }

    /**
     * Load group information
     */
    static async loadGroupInfo(studentId, groupId, tabId) {
        const target = DOM.get(`#${tabId}`);
        if (!target) return;

        DOM.showLoader(target);

        try {
            const response = await HTTP.post('/students/showGroue_info', {
                student_id: studentId,
                group_id: groupId,
            });

            target.innerHTML = response;
            target.classList.add('ajax-loaded-content');
        } catch (error) {
            target.innerHTML = `
                <div class="modern-card text-center py-5">
                    <p class="text-danger">Failed to load group information. Please try again.</p>
                </div>
            `;
        }
    }
}

// ─────────────────────────────────────────────────────
// PROFILE MANAGEMENT
// ─────────────────────────────────────────────────────

class ProfileManager {
    constructor() {
        this.init();
    }

    init() {
        this.setupProfilePhotoUpload();
        this.setupProfileUpdateRequest();
    }

    /**
     * Handle profile photo upload with preview
     */
    setupProfilePhotoUpload() {
        const fileInput = DOM.get(DASHBOARD_CONFIG.selectors.fileInput);
        const profileImg = DOM.get(DASHBOARD_CONFIG.selectors.profileImg);

        if (!fileInput || !profileImg) return;

        DOM.on(fileInput, 'change', (e) => {
            const file = e.target.files?.[0];
            if (!file) return;

            // Validate file
            if (!this.validateImageFile(file)) {
                HTTP.showError('Please select a valid image file (JPG, PNG)');
                return;
            }

            // Preview image
            const reader = new FileReader();
            reader.onload = (event) => {
                profileImg.src = event.target.result;
                profileImg.style.animation = 'pulse 0.5s ease';
            };
            reader.readAsDataURL(file);
        });
    }

    /**
     * Validate image file
     */
    validateImageFile(file) {
        const validTypes = ['image/jpeg', 'image/png', 'image/webp'];
        const maxSize = 5 * 1024 * 1024; // 5MB

        return validTypes.includes(file.type) && file.size <= maxSize;
    }

    /**
     * Handle profile update request
     */
    setupProfileUpdateRequest() {
        const updateBtn = DOM.get('#ask_updte');
        if (!updateBtn) return;

        DOM.on(updateBtn, 'click', async (e) => {
            e.preventDefault();

            if (!window.Swal) {
                alert('Please confirm to request profile update');
                return;
            }

            const result = await Swal.fire({
                title: 'Request Profile Update',
                text: 'Send a request to update your profile information?',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#FDC800',
                confirmButtonText: 'Yes, Request Update',
                cancelButtonText: 'Cancel',
            });

            if (!result.isConfirmed) return;

            try {
                const response = await HTTP.post(updateBtn.dataset.href, {
                    id: updateBtn.dataset.id,
                });

                if (response.success) {
                    updateBtn.outerHTML = `
                        <button class="btn-premium btn-blue w-100" disabled>
                            <i class="bi bi-clock-history me-2"></i> Pending Review
                        </button>
                    `;

                    HTTP.showSuccess('Update request sent successfully!');
                }
            } catch (error) {
                // Error already shown by HTTP.post
            }
        });
    }
}

// ─────────────────────────────────────────────────────
// MESSAGE SYSTEM
// ─────────────────────────────────────────────────────

class MessageSystem {
    constructor() {
        this.init();
    }

    init() {
        const dialogBtn = DOM.get('#dialog-btn');
        if (dialogBtn) {
            DOM.on(dialogBtn, 'click', () => this.showMessageDialog());
        }
    }

    /**
     * Show message dialog
     */
    showMessageDialog() {
        if (!window.Swal) {
            alert('Message system unavailable');
            return;
        }

        Swal.fire({
            title: 'Send Message to Support',
            html: `
                <div class="form-group text-start">
                    <label for="message-title" class="form-label fw-bold mb-2">Message Title</label>
                    <input type="text" class="form-control form-control-modern" id="message-title" 
                           placeholder="e.g., Course Access Issue">
                </div>
                <div class="form-group text-start mt-3">
                    <label for="message-body" class="form-label fw-bold mb-2">Message Body</label>
                    <textarea class="form-control form-control-modern" id="message-body" 
                              rows="4" placeholder="Describe your issue in detail..."></textarea>
                </div>
            `,
            confirmButtonText: 'Send Message',
            confirmButtonColor: '#FDC800',
            cancelButtonColor: '#002147',
            showCancelButton: true,
            preConfirm: () => this.validateAndSendMessage(),
            allowOutsideClick: () => !Swal.isLoading(),
        }).then((result) => {
            if (result.isConfirmed) {
                Swal.fire({
                    icon: 'success',
                    title: 'Message Sent!',
                    text: 'We will respond within 24 hours.',
                    confirmButtonColor: '#002147',
                });
            }
        }).catch(() => {
            // Dialog closed
        });
    }

    /**
     * Validate and send message
     */
    validateAndSendMessage() {
        const title = DOM.get('#message-title')?.value?.trim();
        const body = DOM.get('#message-body')?.value?.trim();

        if (!title || title.length < 3) {
            Swal.showValidationMessage('Title must be at least 3 characters');
            return false;
        }

        if (!body || body.length < 10) {
            Swal.showValidationMessage('Message must be at least 10 characters');
            return false;
        }

        return this.submitMessage(title, body);
    }

    /**
     * Submit message via API
     */
    async submitMessage(title, body) {
        try {
            const response = await HTTP.post('/student/admin-messages', {
                title,
                body,
            });

            return response;
        } catch (error) {
            Swal.showValidationMessage('Failed to send message. Please try again.');
            return false;
        }
    }
}

// ─────────────────────────────────────────────────────
// GROUP MANAGEMENT
// ─────────────────────────────────────────────────────

class GroupManager {
    constructor() {
        this.init();
    }

    init() {
        const alertCodeBtn = DOM.get('#alert-code');
        if (alertCodeBtn) {
            DOM.on(alertCodeBtn, 'click', () => this.showGroupCodeDialog());
        }

        // Course action dropdowns
        DOM.getAll('.joinG').forEach(link => {
            DOM.on(link, 'click', (e) => {
                e.preventDefault();
                const groupId = link.dataset.group_id;
                const studentId = link.dataset.student_id;
                DataLoader.loadGroupInfo(studentId, groupId, 'Info');
            });
        });
    }

    /**
     * Show group code dialog
     */
    showGroupCodeDialog() {
        if (!window.Swal) {
            alert('System unavailable');
            return;
        }

        const studentId = DOM.get('#alert-code')?.value;

        Swal.fire({
            title: 'Join Study Group',
            text: 'Enter your group activation code',
            icon: 'info',
            input: 'password',
            inputPlaceholder: 'Enter group code',
            showCancelButton: true,
            confirmButtonColor: '#FDC800',
            confirmButtonText: 'Confirm',
            preConfirm: (code) => this.verifyGroupCode(code, studentId),
            allowOutsideClick: () => !Swal.isLoading(),
        }).catch(() => {
            // Dialog closed
        });
    }

    /**
     * Verify group code
     */
    async verifyGroupCode(code, studentId) {
        if (!code || code.length < 3) {
            Swal.showValidationMessage('Please enter a valid code');
            return false;
        }

        try {
            const response = await HTTP.post('/grope/code/check', {
                input: code,
                student_id: studentId,
            });

            if (response.status === 200) {
                Swal.fire({
                    icon: 'success',
                    title: 'Success!',
                    text: 'You have been added to the group',
                    confirmButtonColor: '#002147',
                }).then(() => {
                    setTimeout(() => window.location.reload(), 500);
                });
                return true;
            }

            Swal.showValidationMessage('Invalid or expired code');
            return false;
        } catch (error) {
            Swal.showValidationMessage('Code verification failed');
            return false;
        }
    }
}

// ─────────────────────────────────────────────────────
// DATE PICKER INITIALIZATION
// ─────────────────────────────────────────────────────

class DatePickerInit {
    static init() {
        if (typeof $.fn.datepicker === 'undefined') {
            console.warn('Bootstrap datepicker not loaded');
            return;
        }

        DOM.getAll(DASHBOARD_CONFIG.selectors.datePicker).forEach(picker => {
            $(picker).datepicker({
                format: 'yyyy-mm-dd',
                autoclose: true,
            });
        });
    }
}

// ─────────────────────────────────────────────────────
// PERFORMANCE MONITORING
// ─────────────────────────────────────────────────────

class PerformanceMonitor {
    static init() {
        if (window.performance?.measure) {
            window.addEventListener('load', () => {
                const perfData = window.performance.getEntriesByType('navigation')[0];
                console.log(`Page Load Time: ${perfData?.loadEventEnd}ms`);
            });
        }
    }
}

// ─────────────────────────────────────────────────────
// ACCESSIBILITY
// ─────────────────────────────────────────────────────

class AccessibilityEnhancer {
    static init() {
        // Add ARIA labels to interactive elements
        DOM.getAll('.btn-premium').forEach((btn, index) => {
            if (!btn.hasAttribute('aria-label')) {
                btn.setAttribute('aria-label', btn.textContent?.trim() || `Button ${index}`);
            }
        });

        // Add skip navigation link
        this.addSkipLink();

        // Keyboard navigation
        this.enableKeyboardNav();
    }

    static addSkipLink() {
        if (DOM.get('.skip-link')) return;

        const skipLink = DOM.create('a', 'skip-link', 'Skip to main content');
        skipLink.href = '#main-content';
        skipLink.style.cssText = `
            position: absolute;
            top: -40px;
            left: 0;
            background: #002147;
            color: white;
            padding: 8px 16px;
            z-index: 100;
        `;

        skipLink.addEventListener('focus', () => {
            skipLink.style.top = '0';
        });

        skipLink.addEventListener('blur', () => {
            skipLink.style.top = '-40px';
        });

        document.body.insertBefore(skipLink, document.body.firstChild);
    }

    static enableKeyboardNav() {
        document.addEventListener('keydown', (e) => {
            // Alt + Shift + T: Toggle mobile menu
            if (e.altKey && e.shiftKey && e.key === 'T') {
                const fabBtn = DOM.get(DASHBOARD_CONFIG.selectors.fabButton);
                fabBtn?.click();
            }
        });
    }
}

// ─────────────────────────────────────────────────────
// INITIALIZE APPLICATION
// ─────────────────────────────────────────────────────

document.addEventListener('DOMContentLoaded', () => {
    // Set global constants
    window.BASE_URL = document.querySelector('[data-base-url]')?.dataset.baseUrl || '/';
    window.loadStudentMarks = (studentId, groupId, tabId) => DataLoader.loadStudentMarks(studentId, tabId);
    window.loadStudentProgress = (studentId, groupId, tabId) => DataLoader.loadStudentProgress(studentId, tabId);
    window.loadNotifications = (studentId, groupId, tabId) => DataLoader.loadNotifications(tabId);
    window.loadGroupInfo = (studentId, groupId, tabId) => DataLoader.loadGroupInfo(studentId, groupId, tabId);

    // Initialize systems
    new TabSystem();
    new ProfileManager();
    new MessageSystem();
    new GroupManager();
    DatePickerInit.init();
    AccessibilityEnhancer.init();
    PerformanceMonitor.init();

    // Show success message on page load
    console.log('Student Dashboard initialized successfully');
});

// ─────────────────────────────────────────────────────
// GLOBAL ERROR HANDLER
// ─────────────────────────────────────────────────────

window.addEventListener('error', (event) => {
    console.error('Global error:', event.error);
});

window.addEventListener('unhandledrejection', (event) => {
    console.error('Unhandled promise rejection:', event.reason);
});