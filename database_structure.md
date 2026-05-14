# Database Structure Summary - Oxford English Center

This document provides an overview of the database tables and their relationships.

## Table: `absent_student`
**Columns:** `id`, `teacher_id`, `student_id`, `group_id`, `days`, `status`, `created_at`, `updated_at`, `deleted_at`

**Relationships:**
- Linked to `teachers` via `teacher_id`
- Linked to `students` via `student_id`
- Linked to `groups` via `group_id`

---

## Table: `absent_teacher`
**Columns:** `id`, `teacher_id`, `group_id`, `days`, `status`, `created_at`, `updated_at`, `deleted_at`

**Relationships:**
- Linked to `teachers` via `teacher_id`
- Linked to `groups` via `group_id`

---

## Table: `categories`
**Columns:** `id`, `name`, `category_id`, `sort`, `tags`, `status`, `color`, `in_menu`, `created_at`, `updated_at`, `deleted_at`

**Relationships:**
- Linked to `categories` via `category_id`

---

## Table: `closed_classes`
**Columns:** `id`, `teacher_id`, `group_id`, `closed_date`, `seen`, `created_at`, `updated_at`, `deleted_at`

**Relationships:**
- Linked to `teachers` via `teacher_id`
- Linked to `groups` via `group_id`

---

## Table: `email_campaign_logs`
**Columns:** `id`, `campaign_id`, `recipient_name`, `recipient_email`, `status`, `error_message`, `created_at`, `updated_at`

**Relationships:**
- Linked to `campaign` via `campaign_id`

---

## Table: `email_campaigns`
**Columns:** `id`, `subject`, `message`, `sender_name`, `attachment`, `total_recipients`, `sent_count`, `failed_count`, `status`, `started_at`, `completed_at`, `admin_id`, `created_at`, `updated_at`

**Relationships:**
- Linked to `admin` via `admin_id`

---

## Table: `evaluate_items`
**Columns:** `id`, `name_en`, `status`, `created_at`, `updated_at`, `deleted_at`

---

## Table: `evaluations`
**Columns:** `id`, `teacher_id`, `total`, `created_at`, `updated_at`, `deleted_at`

**Relationships:**
- Linked to `teachers` via `teacher_id`

---

## Table: `failed_jobs`
**Columns:** `id`, `uuid`, `connection`, `queue`, `payload`, `exception`, `failed_at`

---

## Table: `files`
**Columns:** `id`, `title`, `descs`, `program_id`, `image`, `status`, `created_at`, `updated_at`, `deleted_at`

**Relationships:**
- Linked to `program` via `program_id`

---

## Table: `group_exam_dates`
**Columns:** `id`, `progress_test1`, `progress_test2`, `progress_test3`, `final_exam`, `group_id`, `created_at`, `updated_at`, `deleted_at`

**Relationships:**
- Linked to `groups` via `group_id`

---

## Table: `group_students`
**Columns:** `id`, `student_id`, `student_fee_total`, `student_book_total`, `group_id`, `exam1_degree`, `exam2_degree`, `exam3_degree`, `exam4_degree`, `activity_degree`, `workbook_degree`, `total_degree`, `has_evaluation`, `evaluation_at`, `progress`, `cer_code`, `created_at`, `updated_at`, `deleted_at`

**Relationships:**
- Linked to `students` via `student_id`
- Linked to `groups` via `group_id`

---

## Table: `group_students_fees`
**Columns:** `id`, `student_id`, `student_fee_paid`, `student_paid_type`, `group_id`, `created_at`, `updated_at`, `deleted_at`

**Relationships:**
- Linked to `students` via `student_id`
- Linked to `groups` via `group_id`

---

## Table: `groups`
**Columns:** `id`, `name`, `program_id`, `teacher_id`, `date_id`, `code`, `code_scope`, `start_date`, `end_date`, `subjects`, `teacher_lib`, `zoom`, `image`, `status`, `progress`, `progress_at`, `drive`, `seen`, `seen_progress`, `attendance`, `created_at`, `updated_at`, `deleted_at`

**Relationships:**
- Linked to `program` via `program_id`
- Linked to `teachers` via `teacher_id`
- Linked to `date` via `date_id`

---

## Table: `jobs`
**Columns:** `id`, `queue`, `payload`, `attempts`, `reserved_at`, `available_at`, `created_at`

---

## Table: `messages`
**Columns:** `id`, `from_user`, `user_type`, `group_id`, `content`, `created_at`, `updated_at`

**Relationships:**
- Linked to `groups` via `group_id`

---

## Table: `migrations`
**Columns:** `id`, `migration`, `batch`

---

## Table: `model_has_permissions`
**Columns:** `permission_id`, `model_type`, `model_id`

**Relationships:**
- Linked to `permission` via `permission_id`
- Linked to `model` via `model_id`

---

## Table: `model_has_roles`
**Columns:** `role_id`, `model_type`, `model_id`

**Relationships:**
- Linked to `roles` via `role_id`
- Linked to `model` via `model_id`

---

## Table: `news`
**Columns:** `id`, `title`, `sub`, `descs`, `onwer`, `source`, `image`, `img_notes`, `main`, `slider`, `comment`, `category_id`, `sidebar`, `others_id`, `publish`, `publish_id`, `sec_id`, `resort`, `views`, `tags`, `pub_date`, `user_id`, `updated_by`, `thumb`, `created_at`, `updated_at`, `deleted_at`

**Relationships:**
- Linked to `categories` via `category_id`
- Linked to `others` via `others_id`
- Linked to `publish` via `publish_id`
- Linked to `sec` via `sec_id`
- Linked to `users` via `user_id`

---

## Table: `notes`
**Columns:** `id`, `notes`, `total`, `created_at`, `updated_at`, `deleted_at`

---

## Table: `notifications`
**Columns:** `id`, `type`, `notifiable_type`, `notifiable_id`, `data`, `read_at`, `created_at`, `updated_at`

**Relationships:**
- Linked to `notifiable` via `notifiable_id`

---

## Table: `pages`
**Columns:** `id`, `title`, `slug`, `details`, `image`, `banner`, `url`, `tags`, `status`, `age`, `level`, `weeks`, `hours`, `mock`, `duration`, `class_size`, `fees`, `price`, `start`, `days`, `time`, `created_at`, `updated_at`, `deleted_at`

---

## Table: `parents`
**Columns:** `id`, `name`, `phone`, `email`, `relationship`, `created_at`, `updated_at`, `deleted_at`

---

## Table: `partner`
**Columns:** `id`, `title`, `descs`, `image`, `status`, `user_id`, `updated_by`, `url`, `created_at`, `updated_at`, `deleted_at`

**Relationships:**
- Linked to `users` via `user_id`

---

## Table: `password_resets`
**Columns:** `email`, `token`, `created_at`

---

## Table: `payment_methods`
**Columns:** `id`, `name`, `credentials`, `image`, `is_active`, `created_at`, `updated_at`, `deleted_at`

---

## Table: `pending_data`
**Columns:** `id`, `student_id`, `name`, `mobile`, `dob`, `job`, `email`, `fileToUpload`, `seen`, `ask_update`, `created_at`, `updated_at`, `deleted_at`

**Relationships:**
- Linked to `students` via `student_id`

---

## Table: `permissions`
**Columns:** `id`, `name`, `guard_name`, `group_id`, `created_at`, `updated_at`, `deleted_at`

**Relationships:**
- Linked to `groups` via `group_id`

---

## Table: `permissions_group`
**Columns:** `id`, `name`, `name_ar`, `name_en`, `icon`, `sort`, `status`, `parent_id`, `created_at`, `updated_at`, `deleted_at`

**Relationships:**
- Linked to `parents` via `parent_id`

---

## Table: `photo`
**Columns:** `id`, `title`, `descs`, `image`, `status`, `tags`, `user_id`, `updated_by`, `created_at`, `updated_at`, `deleted_at`

**Relationships:**
- Linked to `users` via `user_id`

---

## Table: `photos_images`
**Columns:** `id`, `album_id`, `image`, `feature`, `created_at`, `updated_at`, `deleted_at`

**Relationships:**
- Linked to `album` via `album_id`

---

## Table: `placement_tests`
**Columns:** `id`, `student_id`, `test_date`, `test_time`, `status`, `score`, `assigned_level`, `payment_receipt`, `paid_amount`, `payment_method_id`, `created_at`, `updated_at`, `deleted_at`

**Relationships:**
- Linked to `students` via `student_id`
- Linked to `payment_method` via `payment_method_id`

---

## Table: `programs`
**Columns:** `id`, `title`, `image`, `short`, `exam`, `status`, `created_at`, `updated_at`, `deleted_at`

---

## Table: `questions`
**Columns:** `id`, `name`, `status`, `created_at`, `updated_at`, `deleted_at`

---

## Table: `role_has_permissions`
**Columns:** `permission_id`, `role_id`

**Relationships:**
- Linked to `permission` via `permission_id`
- Linked to `roles` via `role_id`

---

## Table: `roles`
**Columns:** `id`, `name`, `guard_name`, `status`, `created_at`, `updated_at`, `deleted_at`

---

## Table: `settings`
**Columns:** `id`, `title`, `description`, `more_desc`, `logo`, `tags`, `mobile`, `address`, `donars`, `clients`, `happy`, `tickects`, `contact_email`, `created_at`, `updated_at`, `deleted_at`

---

## Table: `socials`
**Columns:** `id`, `name`, `link`, `icon`, `status`, `created_at`, `updated_at`, `deleted_at`

---

## Table: `student_answers`
**Columns:** `id`, `evaluation_id`, `student_id`, `group_id`, `question_id`, `answer`, `created_at`, `updated_at`, `deleted_at`

**Relationships:**
- Linked to `evaluation` via `evaluation_id`
- Linked to `students` via `student_id`
- Linked to `groups` via `group_id`
- Linked to `question` via `question_id`

---

## Table: `student_evaluate_teacher`
**Columns:** `id`, `evaluate_item_id`, `class_id`, `student_id`, `teacher_id`, `value`, `created_at`, `updated_at`, `deleted_at`

**Relationships:**
- Linked to `evaluate_item` via `evaluate_item_id`
- Linked to `class` via `class_id`
- Linked to `students` via `student_id`
- Linked to `teachers` via `teacher_id`

---

## Table: `students`
**Columns:** `id`, `name`, `name_en`, `mobile`, `dob`, `job`, `major`, `current_level`, `program_type`, `parent_id`, `email`, `join_date`, `exam_date`, `exam_degree`, `status`, `delaying`, `image`, `note`, `gender`, `delay_cusess`, `username`, `password`, `seen`, `ask_update`, `remember_token`, `created_at`, `updated_at`, `deleted_at`

**Relationships:**
- Linked to `parents` via `parent_id`

---

## Table: `students_admin_messages`
**Columns:** `id`, `student_id`, `title`, `content`, `seen`, `created_at`, `updated_at`, `deleted_at`

**Relationships:**
- Linked to `students` via `student_id`

---

## Table: `supervisor_evalute_teacher`
**Columns:** `id`, `evaluate_item_id`, `class_id`, `teacher_id`, `value`, `created_at`, `updated_at`, `deleted_at`

**Relationships:**
- Linked to `evaluate_item` via `evaluate_item_id`
- Linked to `class` via `class_id`
- Linked to `teachers` via `teacher_id`

---

## Table: `teacher_evaluate_academy`
**Columns:** `id`, `evaluate_item_id`, `class_id`, `teacher_id`, `value`, `created_at`, `updated_at`, `deleted_at`

**Relationships:**
- Linked to `evaluate_item` via `evaluate_item_id`
- Linked to `class` via `class_id`
- Linked to `teachers` via `teacher_id`

---

## Table: `teacher_evaluate_answer`
**Columns:** `id`, `question_id`, `answer`, `evaluate_id`, `created_at`, `updated_at`, `deleted_at`

**Relationships:**
- Linked to `question` via `question_id`
- Linked to `evaluate` via `evaluate_id`

---

## Table: `teacher_evaluate_student`
**Columns:** `id`, `teacher_id`, `student_id`, `group_id`, `total`, `notes`, `progress`, `evaluation_sort`, `created_at`, `updated_at`, `deleted_at`

**Relationships:**
- Linked to `teachers` via `teacher_id`
- Linked to `students` via `student_id`
- Linked to `groups` via `group_id`

---

## Table: `teacher_library`
**Columns:** `id`, `title`, `group_id`, `teacher_id`, `url`, `created_at`, `updated_at`, `deleted_at`

**Relationships:**
- Linked to `groups` via `group_id`
- Linked to `teachers` via `teacher_id`

---

## Table: `teachers`
**Columns:** `id`, `name`, `mobile`, `dob`, `email`, `join_date`, `cv`, `status`, `evaluations`, `image`, `username`, `password`, `remember_token`, `created_at`, `updated_at`, `deleted_at`

---

## Table: `teachers_admin_messages`
**Columns:** `id`, `teacher_id`, `title`, `content`, `seen`, `created_at`, `updated_at`, `deleted_at`

**Relationships:**
- Linked to `teachers` via `teacher_id`

---

## Table: `times`
**Columns:** `id`, `days`, `times`, `status`, `created_at`, `updated_at`, `deleted_at`

---

## Table: `users`
**Columns:** `id`, `username`, `name`, `email`, `role`, `created_by`, `password`, `status`, `image`, `last_login_at`, `remember_token`, `created_at`, `updated_at`, `deleted_at`

---

## Table: `users_groups`
**Columns:** `id`, `name`, `descs`, `status`, `sec_id`, `created_at`, `updated_at`

**Relationships:**
- Linked to `sec` via `sec_id`

---

## Table: `vedio`
**Columns:** `id`, `title`, `url`, `status`, `user_id`, `updated_by`, `created_at`, `updated_at`, `deleted_at`

**Relationships:**
- Linked to `users` via `user_id`

---

