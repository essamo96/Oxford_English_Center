<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ExamQuestion;

/**
 * Applies the correct-answer key to the 187 MCQ questions inserted by
 * ExamPlacementQuestionsSeeder. Answers and explanations were determined manually against
 * standard English grammar rules (these are well-known EFL placement-test calibration items).
 *
 * For each question: marks the correct ExamQuestionOption, writes a short explanation (shown
 * to the student during review), and flips the question from 'draft' to 'active' so it becomes
 * usable in a real exam. Safe to re-run — it always overwrites with the latest key below.
 */
class ExamPlacementAnswersSeeder extends Seeder
{
    public function run(): void
    {
        // 'question_text' => [correct_option_index (0=A..3=D), explanation]
        $key = [
            "He hasn't got _____ brothers and sisters." => [1, "\"Any\" is used with uncountable/plural nouns in negative sentences: \"hasn't got any\"."],
            "They went to the beach with some friends _____ Sunday." => [2, "\"On\" is used before days of the week: \"on Sunday\"."],
            "What _____ your father look like?" => [3, "Present simple question with \"look like\" uses \"does\" for third-person singular."],
            "How many children _____ got?" => [1, "Question form of \"have got\" inverts to \"have they got?\"."],
            "She _____ jeans to work." => [2, "Adverbs of frequency like \"usually\" go before the main verb in the present simple."],
            "_____ two armchairs and a sofa in the living room." => [1, "\"There are\" is used with a plural subject (two armchairs and a sofa)."],
            "There aren't _____ wardrobes in the main bedroom." => [0, "\"Any\" is used with plural countable nouns in negative sentences."],
            "You _____ buy shoes in a post office." => [2, "Logically impossible action, expressed with \"can't\"."],
            "There are a lot of CDs _____ the shelves." => [2, "\"On the shelves\" is the correct preposition of place."],
            "The cinema is _____ the bank." => [2, "\"Opposite\" is used directly before a noun with no extra preposition needed."],
            "Can I have a _____ of milk, please?" => [3, "\"A carton of milk\" is the standard container collocation."],
            "There is _____ butter in the fridge." => [1, "\"Some\" is used with uncountable nouns in affirmative sentences."],
            "How _____ vegetables do you eat every day?" => [0, "\"Many\" is used with plural countable nouns (vegetables)."],
            "He _____ afraid of the dark when he was young." => [0, "Past simple negative of \"be\" for \"he\" is \"wasn't\"."],
            "We _____ born in 1985." => [1, "Passive past simple with plural subject \"we\": \"were born\"."],
            "My birthday is on February _____." => [3, "The ordinal number for 10 is \"10th\"."],
            "_____ they do a lot of sport when they were at school?" => [3, "Past simple question needs the auxiliary \"did\"."],
            "We _____ to New Zealand when I was six." => [2, "Past simple of the regular verb \"move\" is \"moved\"."],
            "They _____ a taxi to the airport an hour ago." => [1, "\"An hour ago\" signals past simple: \"took\"."],
            "_____ did you last see them?" => [1, "\"When\" asks about time, matching the time-based answer implied (\"an hour ago\")."],
            "We went _____ at the weekend." => [2, "\"Went shopping\" is the fixed expression."],
            "Is Chinese food _____ than Thai food?" => [2, "Comparative of \"good\" is irregular: \"better\"."],
            "Today is _____ than yesterday." => [3, "Comparative of the short adjective \"cold\" adds -er: \"colder\"."],
            "He stayed at the _____ hotel in town." => [3, "Superlative with \"the\" + \"most expensive\" (long adjective)."],
            "Can you tell me the _____ to the library?" => [1, "\"The way to\" is the fixed expression for directions."],
            "They _____ their homework now." => [2, "\"Now\" signals present continuous: \"are doing\"."],
            "We walked ten kilometres so we _____ hungry now." => [0, "\"Now\" with a change of state uses present continuous: \"are getting hungry\"."],
            "What _____ doing at the moment?" => [0, "\"What is he doing\" — present continuous question word order."],
            "He goes to work _____ train." => [3, "Means of transport uses \"by\" with no article: \"by train\"."],
            "You _____ drive a car in the centre of town. It isn't allowed." => [3, "\"Not allowed\" is expressed with \"can't\"."],
            "You _____ to walk, you can take a bus." => [3, "\"Don't have to\" expresses that walking isn't necessary (there's an alternative)."],
            "He _____ to move to another country." => [1, "\"'d like to\" expresses a wish/desire politely."],
            "I'm _____ learn to cook." => [2, "\"Going to\" + base verb expresses a future plan/intention."],
            "Don't stay up late or you _____ be tired tomorrow." => [3, "\"Or\" + future consequence uses \"will ('ll)\"."],
            "Let's _____ tennis this afternoon." => [0, "\"Let's\" is followed by the base form of the verb: \"play\"."],
            "I've got the flu. I _____ take some medicine." => [3, "\"Should\" gives advice/recommendation."],
            "_____ you spoken to Jenny?" => [2, "Present perfect question: \"Have you spoken...?\""],
            "_____ does that jacket cost?" => [3, "\"How much\" asks about price/cost."],
            "_____ did you leave your job?" => [2, "\"Why\" asks for a reason."],
            "They _____ fly to India." => [0, "Past simple negative: \"didn't fly\"."],
            "_____ you like a coffee?" => [1, "\"Would you like...?\" is the standard polite offer."],
            "I _____ to go home now." => [2, "\"Want\" is a stative verb and is not normally used in continuous form: \"I want to go\"."],
            "She _____ in Belgium at the moment." => [2, "\"At the moment\" signals a temporary situation: present continuous \"is living\"."],
            "Nick _____ gets up at 7 o'clock and leaves for work at 8 o'clock. He does this every day from Monday to Friday." => [0, "Doing something every day describes a fixed routine: \"always\"."],
            "We go shopping _____ month." => [3, "\"Twice a month\" is the correct frequency expression."],
            "I'm not keen on _____." => [3, "Prepositions (\"on\") are followed by the gerund: \"running\"."],
            "What _____ tonight?" => [2, "\"Tonight\" (future arrangement) uses present continuous: \"are they doing\"."],
            "Are you _____ the new exhibition at the National Gallery?" => [0, "\"Going to see\" expresses a planned future intention."],
            "_____ laptop is that? Is it Bob's?" => [3, "\"Whose\" asks about possession/ownership."],
            "I have never _____ a dangerous sport." => [3, "Present perfect uses the past participle: \"done\" (do a sport)."],
            "_____ he ever flown in a helicopter?" => [2, "Present perfect question: \"Has he ever flown...?\""],
            "You _____ be late for school again." => [0, "Strong warning/prohibition: \"mustn't\"."],
            "You _____ wear a suit. It's a very formal party." => [1, "A formal party creates an obligation: \"have to wear\"."],
            "I _____ dinner when I heard a strange noise." => [2, "Past continuous for an action interrupted by a past simple event: \"was cooking\"."],
            "When did she decide _____ married?" => [0, "\"Decide\" is followed by \"to\" + infinitive: \"to get married\"."],
            "We should avoid _____ in August." => [2, "\"Avoid\" is followed by the gerund: \"travelling\"."],
            "He's studied Spanish _____ last year." => [0, "Present perfect + \"since\" + a point in time (\"last year\")."],
            "I don't think I _____ get the job. I didn't answer all their questions in the interview very well." => [0, "In English, negation usually moves to the main clause: \"I don't think I'll get...\" (not \"I think I won't\")."],
            "She _____ here this weekend." => [3, "\"Might be\" expresses future possibility."],
            "We haven't seen them _____ years." => [2, "\"For\" is used with a period/duration of time (\"for years\")."],
            "How long have you _____ him?" => [3, "Present perfect uses the past participle: \"known\"."],
            "I _____ wear a uniform to school." => [1, "Past habit negative: \"didn't use to\" (no \"d\" after \"didn't\")."],
            "Did they _____ in Australia?" => [0, "After the auxiliary \"did\", the base form is used: \"use to live\" (no -d)."],
            "She's moving to Canada _____ she can study English." => [0, "\"So that\" introduces a clause of purpose."],
            "I travelled around the world for a year _____ learn about other cultures." => [2, "Purpose is expressed with \"to\" + infinitive."],
            "He married the girl _____ used to sit next to him at school." => [0, "\"Who\" is the relative pronoun for people (subject of the clause)."],
            "Children spend _____ hours watching TV." => [0, "\"Hours\" is countable and plural, so \"too many\" is correct."],
            "I don't have _____ time to do the things I enjoy." => [1, "\"Enough time\" is the correct word order (enough + noun)."],
            "Can I try this coat _____, please?" => [1, "\"Try on\" is the phrasal verb for clothing."],
            "It's _____ beautiful house I've ever seen." => [2, "Superlative with present perfect \"ever\": \"the most beautiful\"."],
            "There's more traffic and _____ space to walk in the streets nowadays." => [1, "\"Space\" is uncountable, so the comparative is \"less\"."],
            "I think travelling by plane is _____ easier than travelling by car." => [2, "\"Easier\" is already comparative, so no extra \"more\" is added (avoids a double comparative)."],
            "She worked as a teacher in _____ Africa." => [0, "Continent names take no article: \"in Africa\"."],
            "They live in _____ south of France." => [3, "Regions with \"south/north/east/west of\" take \"the\": \"the south of France\"."],
            "He _____ to work in his company's office in Shanghai." => [3, "Passive past simple: \"was sent\"."],
            "I _____ that I'm like my father." => [0, "Passive present simple: \"I'm told\"."],
            "The postman hasn't delivered the parcel _____." => [2, "\"Yet\" is used with negative present perfect sentences."],
            "My brother _____ passed his exams." => [3, "\"'s just\" (has just) + past participle: \"'s just passed\"."],
            "A lot of people think that when they _____, they'll have lots of free time, but often they don't." => [1, "Present simple is used in time clauses even with future meaning: \"when they retire\"."],
            "What _____ happen if he doesn't get here in time?" => [0, "Future simple question: \"What will happen...?\""],
            "If you save some money, you _____ to worry any more." => [3, "First conditional result clause, negative: \"won't have to worry\"."],
            "He _____ me my book would be a great success." => [0, "\"Told\" is followed directly by an object (me): \"told me\"."],
            "She told me she _____ buy me a new phone." => [3, "Reported speech: \"will\" becomes \"would\" ('d)."],
            "What would they do if they _____ have any money?" => [1, "Second conditional if-clause uses past simple: \"if they didn't have\"."],
            "I'd do more exercise if I _____ time." => [3, "Second conditional if-clause uses past simple: \"if I had time\"."],
            "_____ be possible to reserve a table for tonight?" => [2, "\"Would it be possible...?\" is a standard polite request."],
            "Could you _____ a good film?" => [1, "\"Recommend\" is the correct verb (no indirect object needed)."],
            "When _____ arrive?" => [1, "Past simple question word order: \"did they arrive?\""],
            "Who _____ all this mess?" => [0, "\"Who\" as the subject of the question doesn't need the auxiliary \"did\": \"Who made...?\""],
            "He's French but he _____ in London at the moment." => [2, "\"At the moment\" — temporary situation, present continuous: \"'s living\"."],
            "What _____ of doing now?" => [1, "\"What do you think of doing...?\" — standard question form."],
            "I _____ so tired that I went to bed shortly after dinner." => [2, "Past simple to match the past time reference of the sentence: \"was\"."],
            "Have you told them the good news _____?" => [1, "\"Yet\" is used in present perfect questions."],
            "_____ Thai food?" => [1, "Present perfect experience question: \"Has she ever eaten...?\""],
            "We _____ to work yesterday when we heard a loud crash behind us." => [2, "Past continuous for an action interrupted by a past simple event: \"were walking\"."],
            "They realised they _____ to take her address so they had to go back and get it." => [2, "Past perfect for an action before another past action: \"'d forgotten\"."],
            "He _____ there before so he found it very exciting." => [0, "Past perfect negative: \"hadn't been there before\"."],
            "We _____ on holiday tomorrow so I hope the weather stays warm." => [2, "Present continuous for a fixed future plan: \"'re going\"."],
            "They _____ to call at this time of night. It's very late." => [3, "\"'re unlikely to call\" fits the negative expectation implied."],
            "Do you think they _____ the championship?" => [1, "Simple prediction: \"'ll win\"."],
            "The room _____ look more cheerful if you paint it yellow." => [2, "Prediction with an adverb: \"will probably look\"."],
            "He _____ to pass his driving test this time. He's making too many mistakes." => [1, "\"Be going to\" is used for a prediction based on present evidence: \"'s not going to pass\"."],
            "People _____ smoke in public buildings. It is not allowed." => [3, "Prohibition: \"mustn't smoke\"."],
            "You _____ enter the marathon if you don't want to." => [2, "No obligation: \"don't have to enter\"."],
            "My advice is that you _____ find another job. You can't work with that awful boss any more." => [1, "Advice is expressed with \"should\"."],
            "I _____ be very good at sports when I was a teenager." => [3, "Past habit: \"used to be\"."],
            "She _____ often sit in the garden after coming home from work." => [1, "\"Would\" describes a repeated past habit/action."],
            "Swimming is one of the _____ ways to get fit." => [3, "Superlative of \"good\" is irregular: \"best\"."],
            "The red shoes were _____ expensive than the black ones." => [0, "\"Far more\" intensifies the comparative \"more expensive\"."],
            "That shop's not _____ it used to be." => [1, "Equal comparison: \"as cheap as\"."],
            "We _____ have to leave yet, do we?" => [3, "Tag question \"do we?\" matches a present simple main clause with \"don't\"."],
            "His father was a famous writer, _____?" => [2, "Tag question matching past simple \"was\": \"wasn't he?\""],
            "I can't work if I _____ very hungry." => [0, "Zero/first conditional if-clause uses present simple: \"if I feel\"."],
            "He won't pass the exam _____ he doesn't study hard for it." => [0, "\"Unless\" already means \"if not\", so pairing it with \"doesn't\" would double-negate; \"if he doesn't study\" is correct."],
            "Could I borrow your car if I _____ to drive it carefully?" => [3, "First conditional if-clause uses present simple: \"if I promise\"."],
            "He _____ see the film if he went with an adult." => [2, "Second conditional result clause: \"could see\"."],
            "If I _____ you, I'd take it back to the shop." => [1, "Fixed expression \"If I were you\" (second conditional)."],
            "How long have you _____ the violin?" => [0, "Present perfect continuous for an ongoing activity: \"been playing\"."],
            "I _____ listening to jazz music." => [1, "Present perfect with \"always\": \"'ve always enjoyed\"."],
            "He _____ swim by the time he was five but he hasn't learnt to dive yet." => [2, "Past ability: \"could swim\"."],
            "We _____ to go to the match but we managed to watch it on TV." => [0, "Contrast with \"but managed to watch\" implies inability to attend: \"weren't able to go\"."],
            "I've just seen _____ perfect car for you!" => [1, "Specific, unique reference: \"the perfect car\"."],
            "I didn't know they had a summer cottage _____ south coast of Spain." => [0, "\"On the south coast\" is the standard geographic expression."],
            "They seem to have _____ money but they don't have many friends." => [2, "\"Plenty of\" is used with uncountable nouns like \"money\"."],
            "We don't have _____ time to go on holiday." => [0, "\"Enough\" is used directly before an uncountable noun: \"enough time\"."],
            "That's the beach _____ I first met your father." => [2, "\"Where\" refers to a place (the beach)."],
            "Is that the coat _____ you said you wanted to buy?" => [0, "The relative pronoun (object) can be omitted: \"the coat you said...\"."],
            "I've got a new job, _____ is why I've moved to Brussels." => [3, "\"Which\" refers back to the whole previous clause (non-defining relative clause)."],
            "We _____ arrived on time if the traffic hadn't been so bad." => [2, "Third conditional result clause: \"would've arrived\"."],
            "What _____ if you hadn't been there?" => [3, "Third conditional question: \"would he have done?\""],
            "They _____ told what to do yet." => [1, "Present perfect passive negative: \"haven't been told\"."],
            "Their furniture _____ by Anne's husband, who used to be a carpenter." => [2, "Passive past simple: \"was made\"."],
            "They didn't want to stay late but the boss said they _____ to." => [1, "Reported obligation: \"had to\"."],
            "She said she _____ to do it last weekend." => [0, "Reported past intention: \"was going to do it\"."],
            "I asked her if she _____ my new mobile." => [1, "Reported past perfect for an earlier action: \"'d seen\"."],
            "He promised _____ me decorate my house." => [3, "\"Promise\" is followed by \"to\" + infinitive: \"to help\"."],
            "Why don't we _____ to take them to the airport?" => [2, "\"Offer\" fits the context of proposing to help."],
            "What _____ at the end of the film? I missed it." => [1, "Past simple: \"happened\"."],
            "Do you mind me asking how old _____?" => [2, "Indirect question word order (no inversion): \"how old you are\"."],
            "_____ if you'd like to meet up tomorrow." => [3, "\"I was wondering if...\" is a polite, tentative way to ask."],
            "They _____ in a large house for twenty years before moving to a bungalow." => [1, "Narrative past simple describing a past situation: \"lived\"."],
            "Have you heard from your brother _____?" => [0, "\"Recently\" fits present perfect questions."],
            "I _____ for my car keys for half an hour and I still haven't found them!" => [1, "Present perfect continuous for an ongoing action with duration: \"'ve been looking\"."],
            "Billy _____ watching motorbike races." => [2, "Present perfect with \"always\" for a lasting preference: \"'s always loved\"."],
            "Her parents are very proud. She _____ a fantastic job in a well known law firm." => [3, "Present perfect passive: \"'s been offered\"."],
            "How much do you think he _____ as director of the company?" => [2, "Present continuous passive: \"'s being paid\"."],
            "You should _____ what to do when you get to the office." => [0, "Passive with modal \"should\": \"be told\"."],
            "He _____ for his plane for an hour when it was suddenly cancelled." => [1, "Past perfect continuous for a duration before another past event: \"'d been waiting\"."],
            "When we walked into the hotel, a log fire _____ in the fireplace." => [2, "Past continuous for a scene in progress: \"was burning\"."],
            "My car's just broken down for the third time. I wish I _____ it." => [3, "\"Wish\" + past perfect expresses regret about the past: \"hadn't bought\"."],
            "If only _____ a foreign language. I didn't have the choice at school." => [2, "\"If only\" + past simple/could expresses a present wish about ability: \"I could speak\"."],
            "Jim _____ sharing a flat but now he prefers to live on his own." => [0, "Past habit/state: \"used to like\"."],
            "Liane _____ complaining about her long drive to work. In the end, she decided to change jobs." => [3, "\"Was always\" + gerund expresses an annoying repeated past habit."],
            "They _____ us at the weekend. It depends on the weather." => [0, "Uncertainty (\"depends on\"): \"might visit\"."],
            "He _____ hungry when he gets home from football practice." => [1, "Prediction based on likelihood: \"'s likely to be\"."],
            "We _____ to get married next April." => [1, "Definite future plan: \"'re planning to get married\"."],
            "How many people have climbed _____ Mount Everest?" => [3, "Mountain names take no article."],
            "The children played in the garden with _____ ball I'd given them." => [1, "Specific, already-mentioned object: \"the ball\"."],
            "If you lend me €200, I _____ you back at the end of the month." => [1, "First conditional result clause: \"will pay\"."],
            "If I _____ to your proposal, when could we sign the contract?" => [2, "Formal hypothetical future: \"were to agree\"."],
            "Children under 15 could attend _____ they were with an adult." => [1, "\"As long as\" introduces a condition."],
            "We _____ show our passports when we left the country." => [2, "Obligation in the past: \"were obliged to show\"."],
            "We had very little petrol left in the car but we _____ get home in the end." => [1, "\"Managed to\" expresses success despite difficulty."],
            "This time tomorrow, they _____ in San Francisco. How exciting!" => [2, "Future continuous for an action in progress at a specific future time: \"'ll be landing\"."],
            "When do you think you _____ painting the house?" => [1, "Future perfect for an action completed before a future point: \"'ll have finished\"."],
            "There are still _____ citizens who feel unhappy about the changes made by the government." => [2, "\"Quite a few\" is used with plural countable nouns (citizens)."],
            "_____ of people rely on public transport to get to work these days." => [3, "\"Plenty of\" is the natural collocation before \"people\"."],
            "Dad says he _____ to cook pasta for dinner tonight." => [0, "Present continuous/going to for a stated plan: \"'s going to cook\"."],
            "The teacher wanted to know why _____ his homework last night." => [2, "Indirect question, past perfect, no inversion: \"Tom hadn't done\"."],
            "They told _____ start work the following Monday." => [3, "Reported speech: \"can\" becomes \"could\": \"I could\"."],
            "If she _____ to be fluent in French, she could have applied for the job." => [0, "Third conditional if-clause: \"hadn't needed\"."],
            "They _____ here by now if the train had been on time." => [3, "Mixed/third conditional result: \"'d have been\"."],
            "If I hadn't ignored my parents advice, I _____ a great musician." => [0, "Third conditional result clause: \"would've become\"."],
            "_____ a world class athlete is a lot harder than it looks." => [3, "Gerund as the subject of the sentence: \"Being\"."],
            "You'd _____ late for work again or you'll get fired." => [3, "\"'d better not\" + base form: \"better not be\"."],
            "She went to the doctor because she keeps _____ headaches." => [3, "\"Keep\" is followed by the gerund: \"getting\"."],
            "She finally stopped _____ when the price of cigarettes went up again." => [2, "\"Stop + gerund\" means to quit an activity: \"stopped smoking\"."],
            "I'll never _____ snow for the first time." => [0, "\"Forget + gerund\" refers to a memorable past experience: \"forget seeing\"."],
            "Do you ever regret _____ Canada and returning to your home country?" => [1, "\"Regret + gerund\" for a past action: \"leaving\"."],
            "We _____ a serious accident when you drove through that red light!" => [3, "Past modal of possibility/criticism: \"could've had\"."],
            "The neighbours _____ the music from your party. It was terribly loud. You should go and apologise to them." => [0, "Strong past deduction: \"must've heard\"."],
            "The gym _____ I go to work out is open twenty-four hours a day." => [3, "\"Where\" refers to a place (the gym)."],
            "I wasn't keen on the restaurant _____ we went to last weekend." => [1, "The relative pronoun (object) can be omitted here."],
            "They're having lunch with his girlfriend's parents, _____ live in Brighton." => [1, "\"Who\" refers to people (her parents) in a non-defining relative clause."],
            "I almost fell over a pile of books _____ on the carpet." => [2, "Present participle reduced relative clause: \"lying\"."],
            "A book _____ by a twelve-year-old girl has won a €10,000 prize." => [3, "Past participle reduced passive relative clause: \"written\"."],
            "_____ people's names has always been something I've found incredibly difficult." => [2, "Gerund as the subject of the sentence: \"Remembering\"."],
        ];

        $updated = 0;
        $missing = [];

        foreach ($key as $text => [$correctIndex, $explanation]) {
            $question = ExamQuestion::with('options')->where('question_text', $text)->first();
            if (!$question) {
                $missing[] = $text;
                continue;
            }

            $options = $question->options()->orderBy('sort_order')->get();
            foreach ($options as $i => $option) {
                $option->update(['is_correct' => $i === $correctIndex]);
            }

            $question->update([
                'explanation' => $explanation,
                'status' => 'active',
            ]);
            $updated++;
        }

        $this->command?->info("Updated {$updated} placement questions with correct answers and explanations.");
        if (!empty($missing)) {
            $this->command?->warn(count($missing) . ' questions from the answer key were not found: ' . implode(' | ', array_slice($missing, 0, 5)) . (count($missing) > 5 ? '...' : ''));
        }
    }
}
