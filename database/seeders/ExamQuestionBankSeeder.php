<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ExamQuestion;
use App\Models\ExamQuestionOption;
use App\Models\ExamSkill;
use App\Models\User;

/**
 * Seeds a varied sample Question Bank across all six skills (Grammar, Vocabulary, Reading,
 * Listening, Writing, Speaking), all four question types (mcq, true_false, text, voice), and
 * all difficulty levels — so a new install has enough content to build a test exam immediately
 * without waiting on manual data entry. Depends on ExamSkillsSeeder having run first.
 */
class ExamQuestionBankSeeder extends Seeder
{
    public function run(): void
    {
        $skills = ExamSkill::pluck('id', 'slug');
        $adminId = User::query()->orderBy('id')->value('id');

        if (!$adminId) {
            $this->command?->warn('No admin user found — skipping ExamQuestionBankSeeder.');
            return;
        }

        $questions = [
            // ── Grammar ──
            [
                'skill' => 'grammar', 'type' => 'mcq', 'difficulty' => 'easy', 'marks' => 1,
                'question_text' => 'She ___ to school every day.',
                'explanation' => 'Present simple with third-person singular subjects ("she") takes the base verb + "s".',
                'options' => ['go', 'goes', 'going', 'gone'], 'correct' => 1,
            ],
            [
                'skill' => 'grammar', 'type' => 'mcq', 'difficulty' => 'medium', 'marks' => 2,
                'question_text' => 'Choose the correct past tense: "Yesterday, I ___ to the market."',
                'explanation' => '"Go" is irregular; its past tense form is "went".',
                'options' => ['go', 'goed', 'went', 'gone'], 'correct' => 2,
            ],
            [
                'skill' => 'grammar', 'type' => 'true_false', 'difficulty' => 'easy', 'marks' => 1,
                'question_text' => 'The sentence "He don\'t like coffee" is grammatically correct.',
                'explanation' => 'The correct form is "He doesn\'t like coffee" — third-person singular requires "doesn\'t".',
                'options' => ['صح', 'خطأ'], 'correct' => 1,
            ],
            [
                'skill' => 'grammar', 'type' => 'text', 'difficulty' => 'hard', 'marks' => 3,
                'question_text' => 'Rewrite the following sentence in the passive voice: "The chef cooked the meal."',
                'explanation' => 'Expected answer: "The meal was cooked by the chef."',
            ],

            // ── Vocabulary ──
            [
                'skill' => 'vocabulary', 'type' => 'mcq', 'difficulty' => 'easy', 'marks' => 1,
                'question_text' => 'What is a synonym for "happy"?',
                'explanation' => '"Joyful" means the same as "happy".',
                'options' => ['Sad', 'Joyful', 'Angry', 'Tired'], 'correct' => 1,
            ],
            [
                'skill' => 'vocabulary', 'type' => 'mcq', 'difficulty' => 'medium', 'marks' => 2,
                'question_text' => 'Which word means the opposite of "generous"?',
                'explanation' => '"Stingy" is the antonym of "generous".',
                'options' => ['Kind', 'Stingy', 'Wealthy', 'Helpful'], 'correct' => 1,
            ],
            [
                'skill' => 'vocabulary', 'type' => 'mcq', 'difficulty' => 'hard', 'marks' => 3,
                'question_text' => 'Choose the word that best completes: "The manager gave a ___ explanation of the new policy."',
                'explanation' => '"Comprehensive" fits best — meaning thorough/complete.',
                'options' => ['comprehensive', 'comprehensible', 'comprehend', 'comprehension'], 'correct' => 0,
            ],
            [
                'skill' => 'vocabulary', 'type' => 'true_false', 'difficulty' => 'easy', 'marks' => 1,
                'question_text' => '"Enormous" means very small in size.',
                'explanation' => '"Enormous" means very large, not small.',
                'options' => ['صح', 'خطأ'], 'correct' => 1,
            ],

            // ── Reading ──
            [
                'skill' => 'reading', 'type' => 'mcq', 'difficulty' => 'medium', 'marks' => 2,
                'question_text' => 'Read: "Ali woke up late and missed the bus, so he decided to walk to work." Why did Ali walk to work?',
                'explanation' => 'He missed the bus because he woke up late.',
                'options' => ['He likes walking', 'He missed the bus', 'His car broke down', 'It was a holiday'], 'correct' => 1,
            ],
            [
                'skill' => 'reading', 'type' => 'true_false', 'difficulty' => 'easy', 'marks' => 1,
                'question_text' => 'Read: "The museum is closed on Mondays but open every other day." According to the text, the museum is open on Sundays.',
                'explanation' => 'Since it is closed only on Mondays, it is open on Sundays.',
                'options' => ['صح', 'خطأ'], 'correct' => 0,
            ],
            [
                'skill' => 'reading', 'type' => 'text', 'difficulty' => 'hard', 'marks' => 3,
                'question_text' => 'Read the short paragraph provided by your teacher and summarize its main idea in two sentences.',
                'explanation' => 'Graded manually — look for accurate identification of the main idea, not just copied sentences.',
            ],

            // ── Listening ──
            [
                'skill' => 'listening', 'type' => 'mcq', 'difficulty' => 'medium', 'marks' => 2,
                'question_text' => 'Listen to the audio clip (attach the recording via edit), then choose what the speaker ordered at the restaurant.',
                'explanation' => 'Placeholder question — edit it and attach the matching audio file before using in an exam.',
                'options' => ['Pizza', 'Pasta', 'Salad', 'Soup'], 'correct' => 0,
            ],
            [
                'skill' => 'listening', 'type' => 'true_false', 'difficulty' => 'easy', 'marks' => 1,
                'question_text' => 'Listen to the audio clip (attach the recording via edit): the speaker mentions it is raining outside.',
                'explanation' => 'Placeholder question — edit it and attach the matching audio file before using in an exam.',
                'options' => ['صح', 'خطأ'], 'correct' => 0,
            ],
            [
                'skill' => 'listening', 'type' => 'text', 'difficulty' => 'hard', 'marks' => 3,
                'question_text' => 'Listen to the audio clip (attach the recording via edit) and write down the three main points the speaker makes.',
                'explanation' => 'Graded manually against the audio transcript.',
            ],

            // ── Writing ──
            [
                'skill' => 'writing', 'type' => 'text', 'difficulty' => 'easy', 'marks' => 2,
                'question_text' => 'Write a short paragraph (3-4 sentences) introducing yourself in English.',
                'explanation' => 'Graded manually — check basic sentence structure and vocabulary use.',
            ],
            [
                'skill' => 'writing', 'type' => 'text', 'difficulty' => 'medium', 'marks' => 3,
                'question_text' => 'Write an email to a friend inviting them to your birthday party (5-6 sentences).',
                'explanation' => 'Graded manually — check for correct email structure, tone, and grammar.',
            ],
            [
                'skill' => 'writing', 'type' => 'text', 'difficulty' => 'hard', 'marks' => 5,
                'question_text' => 'Write a short essay (150-200 words) about the advantages and disadvantages of online learning.',
                'explanation' => 'Graded manually against a rubric: structure, argument quality, grammar, vocabulary range.',
            ],

            // ── Speaking ──
            [
                'skill' => 'speaking', 'type' => 'voice', 'difficulty' => 'easy', 'marks' => 2,
                'question_text' => 'Record yourself introducing your family in English (30-45 seconds).',
                'explanation' => 'Graded manually — check pronunciation, fluency, and basic vocabulary.',
            ],
            [
                'skill' => 'speaking', 'type' => 'voice', 'difficulty' => 'medium', 'marks' => 3,
                'question_text' => 'Record yourself describing your daily routine in English (1 minute).',
                'explanation' => 'Graded manually — check use of present simple tense and sequencing words (first, then, after that).',
            ],
            [
                'skill' => 'speaking', 'type' => 'voice', 'difficulty' => 'hard', 'marks' => 4,
                'question_text' => 'Record yourself giving your opinion on: "Should students be allowed to use phones in class?" (1-2 minutes).',
                'explanation' => 'Graded manually — check argument clarity, fluency, and range of vocabulary.',
            ],
        ];

        $created = 0;

        foreach ($questions as $q) {
            $exists = ExamQuestion::where('question_text', $q['question_text'])->exists();
            if ($exists) {
                continue;
            }

            $question = ExamQuestion::create([
                'branch_id' => null,
                'skill_id' => $skills[$q['skill']] ?? null,
                'type' => $q['type'],
                'difficulty' => $q['difficulty'],
                'question_text' => $q['question_text'],
                'explanation' => $q['explanation'] ?? null,
                'estimated_time_seconds' => match ($q['type']) {
                    'mcq', 'true_false' => 60,
                    'text' => 180,
                    'voice' => 90,
                    default => 60,
                },
                'marks' => $q['marks'],
                'status' => 'active',
                'created_by_type' => 'admin',
                'created_by_id' => $adminId,
            ]);

            if (isset($q['options'])) {
                foreach ($q['options'] as $index => $optionText) {
                    ExamQuestionOption::create([
                        'question_id' => $question->id,
                        'option_text' => $optionText,
                        'is_correct' => $index === $q['correct'],
                        'sort_order' => $index,
                    ]);
                }
            }

            $created++;
        }

        $this->command?->info("Seeded {$created} exam questions (skipped already-existing ones).");
    }
}
