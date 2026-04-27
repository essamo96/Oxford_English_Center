# Book A Course - Complete System Documentation

## 📚 Overview

A production-ready course booking system for Oxford Language Center featuring:
- Server-side validation with custom error messages
- Client-side UX enhancement (no form blocking)
- Bilingual success messaging (English + Arabic)
- Professional error display under form fields
- No code duplication

---

## 🛣️ Routes

### GET /book
Display booking form
```php
Route::get('book', 'ContactController@getBook');
```

**View:** `resources/views/frontend/contact/book.blade.php`

### POST /contact/book
Handle booking submission with validation
```php
Route::post('contact/book', 'ContactController@postBook')->name('contact.book');
```

**Validation:** 9 fields with 20+ custom error messages
**Response:** Redirect with errors or success flash message

---

## 📝 Form Fields

### Required Fields

| Field | Type | Validation Rules |
|-------|------|------------------|
| name | text | required, 3-100 chars |
| dob | date | required, past date |
| email | email | required, Gmail only |
| phone | tel | required, 10 digits, unique |
| address | text | required, 5-255 chars |
| major | text | required, 2-100 chars |
| gender | select | required, Male/Female |
| how | select | required, 5 options |
| agree | checkbox | required, must check |

### Example Request

```http
POST /contact/book HTTP/1.1
Content-Type: application/x-www-form-urlencoded

name=Ahmed+Hassan&dob=1995-06-15&email=ahmed@gmail.com&
phone=0561234567&address=Ramallah&major=Computer+Science&
gender=Male&how=Friend+Referral&agree=1
```

---

## ✅ Validation Rules & Messages

### Name
```
✓ required: "Full name is required."
✓ min:3: "Full name must be at least 3 characters."
✓ max:100: "Full name cannot exceed 100 characters."
```

### Date of Birth
```
✓ required: "Date of birth is required."
✓ date: "Please enter a valid date of birth."
✓ before:today: "Date of birth must be in the past."
✓ after:1900-01-01: "Date of birth is invalid."
```

### Email
```
✓ required: "Email address is required."
✓ email: "Please enter a valid email address."
✓ regex: "Email must be a Gmail address (example@gmail.com)."
```

### Phone
```
✓ required: "Phone number is required."
✓ regex: "Phone number contains invalid characters."
✓ digits:10: "Phone number must be exactly 10 digits."
✓ unique:students,mobile: "This phone number is already registered."
```

### Other Fields
- **address:** required, 5-255 chars
- **major:** required, 2-100 chars
- **gender:** required, Male or Female
- **how:** required, one of 5 predefined options
- **agree:** required, must be checked

---

## 🔄 Response Examples

### ❌ Validation Failed (422)
```php
// Blade template automatically shows:
// - has-error class on form-group
// - Error message under each field
// - Old input values preserved with old()
// - Session flash message (if server-side error)

Example error display under "name" field:
<span class="form-error">Full name must be at least 3 characters.</span>
```

### ✅ Success (Redirect with Flash)
```php
// Controller response:
$request->session()->flash('success', 'Your booking request has been submitted successfully...');
return redirect('book');

// JavaScript intercepts session flash:
// 1. Detects success message in HTML
// 2. Shows bilingual SweetAlert2 popup
// 3. Auto-closes after 4 seconds
// 4. Redirects to homepage
```

---

## 🎯 Form Submission Flow

```
User Input
    ↓
Client Validation (UX only, doesn't block)
    ↓
Form Submit Button Disabled
    ↓
POST to /contact/book
    ↓
ContactController::postBook()
    ├─ Validate with $request->validate()
    ├─ ❌ If fails:
    │   ├─ Flash errors to session
    │   ├─ Redirect back with old input
    │   └─ Blade shows errors under fields
    │
    └─ ✅ If passes:
        ├─ Generate credentials (username/password from phone)
        ├─ Hash password with Hash::make()
        ├─ Call Students::addStudent()
        ├─ Flash success message
        └─ Redirect to /book with session('success')
                ↓
        Page loads with success flash
                ↓
        JavaScript shows SweetAlert2
                ↓
        Auto-closes & redirects to /
```

---

## 💾 Database Insert

When validation passes, the following is saved to `students` table:

```php
[
    'name'          => $name,
    'username'      => substr($phone, 3, 7),     // Derived from phone
    'password'      => Hash::make($password),    // Hashed
    'mobile'        => $phone,
    'dob'           => '1995-06-15',
    'job'           => $major,                    // Maps major field
    'email'         => $email,
    'gender'        => $gender,
    'status'        => 0,                         // Pending approval
    'delaying'      => 0,
    'join_date'     => null,
    'exam_date'     => null,
    'exam_degree'   => null,
    'created_at'    => Carbon::now(),
]
```

---

## 🎨 Frontend Components

### Form Structure
```
Hero Section (Particles Animation)
    ↓
Form Container (2-column grid)
    ├─ Left: Why Choose Us (Sidebar)
    └─ Right: Form
        ├─ Row 1: Name, DOB
        ├─ Row 2: Email, Phone
        ├─ Row 3: Address, Gender
        ├─ Row 4: Major, How Did You Hear
        ├─ Row 5: Terms Checkbox
        └─ Submit Button
```

### CSS Classes
```css
.book-course-hero          /* Hero section container */
.hero-content              /* Center text in hero */
#hero-particles            /* Canvas for particles */
.book-course-container     /* Main 2-column grid */
.form-group                /* Each field wrapper */
.form-row-2col             /* 2-column grid */
.has-error                 /* Added to form-group on validation fail */
.form-error                /* Error message text */
.checkbox-group            /* Terms checkbox wrapper */
```

---

## 🚀 JavaScript Files

### particles-hero.js
Animated particle background (unchanged)
```javascript
initParticles(canvasId, {
    color1: 'rgba(245, 197, 24, 0.7)',
    color2: 'rgba(255, 255, 255, 0.25)',
    count: 60,
    speed: 0.4,
    connectLines: true,
    lineColor: 'rgba(245, 197, 24, 0.15)',
    connectDistance: 150
});
```

### book-course.js (NEW)
Form validation and UX enhancement
```javascript
const BookingFormValidator = {
    init()                  // Attach all event listeners
    validateField(input)    // Validate single field
    showFieldError(input)   // Add error class
    clearFieldError(input)  // Remove error class
};

// Initialize on DOMContentLoaded
BookingFormValidator.init();
```

### Inline Script (book.blade.php)
Initialization and success message handling
```javascript
// 1. Initialize particles animation
initParticles('hero-particles', {...});

// 2. Initialize form validation
BookingFormValidator.init();

// 3. Show success message if redirected
@if (session('success'))
    Swal.fire({...bilingual success message...});
@endif
```

---

## 🔐 Security Features

1. **CSRF Protection**
   ```blade
   {{ csrf_field() }}  <!-- In form -->
   ```

2. **Input Validation**
   - Server-side only (cannot be bypassed)
   - 20+ custom validation rules
   - Regex patterns for phone/email

3. **Password Hashing**
   ```php
   $password = Hash::make($password);
   ```

4. **Error Logging**
   ```php
   Log::error('Booking submission error: ' . $e->getMessage());
   ```

5. **Unique Phone Check**
   ```php
   'phone' => '...unique:students,mobile'
   ```

---

## 🌍 Bilingual Support

### Success Messages

**English:**
> "Your booking request has been submitted successfully. Please visit the center to complete registration and pay the required fees."

**Arabic (العربية):**
> "تم إرسال طلب التسجيل بنجاح. يرجى التوجه إلى المركز لإتمام عملية التسجيل ودفع الرسوم."

### Detection
```javascript
const userLang = document.documentElement.lang || 'en';
const successMsg = successMessages[userLang] || successMessages.en;
```

---

## 📊 Error Page Example

When form fails validation, user sees:

```
===== FORM =====

[Full Name field - RED BORDER]
"Full name must be at least 3 characters."

[Email field - RED BORDER]
"Email must be a Gmail address (example@gmail.com)."

[Phone field - RED BORDER]
"This phone number is already registered."

===== FORM FOOTER =====
"⚠️ Submission Error"
"Please fix the following errors:"
- Full name must be at least 3 characters.
- Email must be a Gmail address...
- This phone number is already...
```

---

## 🧪 Testing Scenarios

### ✅ Valid Submission
```
name: "Ahmed Hassan"
dob: "1995-06-15"
email: "ahmed@gmail.com"
phone: "0561234567"
address: "Ramallah, Palestine"
major: "Computer Science"
gender: "Male"
how: "Friend Referral"
agree: checked

Result: → Success popup → Redirect to home
```

### ❌ Duplicate Phone
```
phone: "0561234567"  (already registered)

Result: → Error under phone field
"This phone number is already registered."
```

### ❌ Invalid Email
```
email: "ahmed@hotmail.com"  (not Gmail)

Result: → Error under email field
"Email must be a Gmail address (example@gmail.com)."
```

### ❌ Future DOB
```
dob: "2025-06-15"  (future date)

Result: → Error under DOB field
"Date of birth must be in the past."
```

---

## 📞 Support

For issues or questions:
1. Check validation messages under each field
2. Review browser console for JavaScript errors
3. Check Laravel logs at `storage/logs/laravel.log`
4. Verify phone number is 10 digits
5. Ensure email is Gmail (@gmail.com)

---

## 🎓 Code Quality Metrics

- ✅ No duplicate code
- ✅ Single DOMContentLoaded event
- ✅ Server-side validation primary
- ✅ Custom error messages (20+)
- ✅ Bilingual support
- ✅ Error logging
- ✅ Try-catch blocks
- ✅ Production-ready

**Status: PRODUCTION READY** 🚀

---

*Last Updated: April 7, 2026*
*Version: 1.0*
