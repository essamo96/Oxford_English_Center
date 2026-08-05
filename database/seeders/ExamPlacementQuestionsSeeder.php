<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ExamQuestion;
use App\Models\ExamQuestionOption;
use App\Models\ExamSkill;
use App\Models\User;

/**
 * Replaces the sample question bank (from ExamQuestionBankSeeder) with the real Oxford
 * "Test Your English" placement test bank: 187 grammar MCQ items (numbered 14-200 in the
 * source paper) plus one closing Writing essay question.
 *
 * IMPORTANT: none of the MCQ options are marked as the correct answer — the source material
 * provided did not include an answer key, and guessing would silently corrupt grading for a
 * real placement test. An admin must open each question in the Question Bank and mark the
 * correct option before these are used in a live exam.
 */
class ExamPlacementQuestionsSeeder extends Seeder
{
    public function run(): void
    {
        $adminId = User::query()->orderBy('id')->value('id');
        if (!$adminId) {
            $this->command?->warn('No admin user found — skipping ExamPlacementQuestionsSeeder.');
            return;
        }

        if (ExamQuestion::where('question_text', "He hasn't got _____ brothers and sisters.")->exists()) {
            $this->command?->info('Placement test questions already seeded — skipping.');
            return;
        }

        // Remove the old sample questions seeded by ExamQuestionBankSeeder so the bank isn't
        // cluttered with placeholder content once the real placement bank is in place.
        $oldSampleTexts = [
            'She ___ to school every day.',
            'Choose the correct past tense: "Yesterday, I ___ to the market."',
            'The sentence "He don\'t like coffee" is grammatically correct.',
            'Rewrite the following sentence in the passive voice: "The chef cooked the meal."',
            'What is a synonym for "happy"?',
            'Which word means the opposite of "generous"?',
            'Choose the word that best completes: "The manager gave a ___ explanation of the new policy."',
            '"Enormous" means very small in size.',
            'Read: "Ali woke up late and missed the bus, so he decided to walk to work." Why did Ali walk to work?',
            'Read: "The museum is closed on Mondays but open every other day." According to the text, the museum is open on Sundays.',
            'Read the short paragraph provided by your teacher and summarize its main idea in two sentences.',
            'Listen to the audio clip (attach the recording via edit), then choose what the speaker ordered at the restaurant.',
            'Listen to the audio clip (attach the recording via edit): the speaker mentions it is raining outside.',
            'Listen to the audio clip (attach the recording via edit) and write down the three main points the speaker makes.',
            'Write a short paragraph (3-4 sentences) introducing yourself in English.',
            'Write an email to a friend inviting them to your birthday party (5-6 sentences).',
            'Write a short essay (150-200 words) about the advantages and disadvantages of online learning.',
            'Record yourself introducing your family in English (30-45 seconds).',
            'Record yourself describing your daily routine in English (1 minute).',
            'Record yourself giving your opinion on: "Should students be allowed to use phones in class?" (1-2 minutes).',
        ];
        ExamQuestion::whereIn('question_text', $oldSampleTexts)->get()->each(function ($q) {
            $q->options()->delete();
            $q->delete();
        });

        $grammarSkillId = ExamSkill::where('slug', 'grammar')->value('id');

        // [question_text, [option_A, option_B, option_C, option_D], difficulty]
        $items = [
            ["He hasn't got _____ brothers and sisters.", ['some', 'any', 'the', 'a'], 'easy'],
            ["They went to the beach with some friends _____ Sunday.", ['at', 'the', 'on', 'in'], 'easy'],
            ["What _____ your father look like?", ['is', 'do', 'are', 'does'], 'easy'],
            ["How many children _____ got?", ["they've", 'have they', 'they', 'do they'], 'easy'],
            ["She _____ jeans to work.", ['wears usually', 'is usually wearing', 'usually wears', 'is wearing usually'], 'easy'],
            ["_____ two armchairs and a sofa in the living room.", ["It's", 'There are', 'There have', "There's"], 'easy'],
            ["There aren't _____ wardrobes in the main bedroom.", ['any', 'some', 'the', 'a'], 'easy'],
            ["You _____ buy shoes in a post office.", ['can to', 'can', "can't", 'are'], 'easy'],
            ["There are a lot of CDs _____ the shelves.", ['in', 'between', 'on', 'above'], 'easy'],
            ["The cinema is _____ the bank.", ['next', 'in front', 'opposite', 'under'], 'easy'],
            ["Can I have a _____ of milk, please?", ['bar', 'jar', 'box', 'carton'], 'easy'],
            ["There is _____ butter in the fridge.", ['one', 'some', 'any', 'an'], 'easy'],
            ["How _____ vegetables do you eat every day?", ['many', 'long', 'more', 'much'], 'easy'],
            ["He _____ afraid of the dark when he was young.", ["wasn't", "weren't", 'were', "didn't"], 'easy'],
            ["We _____ born in 1985.", ['is', 'were', 'was', 'did'], 'easy'],
            ["My birthday is on February _____.", ['10rd', '10st', '10nd', '10th'], 'easy'],
            ["_____ they do a lot of sport when they were at school?", ['Were', 'Do', 'Was', 'Did'], 'easy'],
            ["We _____ to New Zealand when I was six.", ['move', 'moves', 'moved', 'moving'], 'easy'],
            ["They _____ a taxi to the airport an hour ago.", ['take', 'took', 'are taking', 'takes'], 'easy'],
            ["_____ did you last see them?", ['What', 'When', 'Who', 'Which'], 'easy'],
            ["We went _____ at the weekend.", ['the shops', 'to shops', 'shopping', 'shops'], 'easy'],
            ["Is Chinese food _____ than Thai food?", ['best', 'more good', 'better', 'well'], 'easy'],
            ["Today is _____ than yesterday.", ['cold', 'coldest', 'more cold', 'colder'], 'easy'],
            ["He stayed at the _____ hotel in town.", ['more expensive', 'expensivest', 'expensive', 'most expensive'], 'easy'],
            ["Can you tell me the _____ to the library?", ['road', 'way', 'street', 'place'], 'easy'],
            ["They _____ their homework now.", ['are do', 'did', 'are doing', 'does'], 'easy'],
            ["We walked ten kilometres so we _____ hungry now.", ['are getting', 'get', 'got', 'has got'], 'easy'],
            ["What _____ doing at the moment?", ['is he', 'does he', 'is', "he's"], 'easy'],
            ["He goes to work _____ train.", ['in', 'on', 'by the', 'by'], 'easy'],
            ["You _____ drive a car in the centre of town. It isn't allowed.", ["don't have to", 'can', 'have to', "can't"], 'easy'],
            ["You _____ to walk, you can take a bus.", ["mustn't", 'have', 'must', "don't have"], 'easy'],
            ["He _____ to move to another country.", ['want', "'d like", 'likes', 'goes'], 'easy'],
            ["I'm _____ learn to cook.", ['go to', 'going', 'going to', 'go'], 'easy'],
            ["Don't stay up late or you _____ be tired tomorrow.", ['must', "won't", 'should', "'ll"], 'easy'],
            ["Let's _____ tennis this afternoon.", ['play', 'go', 'to go', 'playing'], 'easy'],
            ["I've got the flu. I _____ take some medicine.", ["shouldn't", 'might not', 'going to', 'should'], 'easy'],
            ["_____ you spoken to Jenny?", ['Did', 'Do', 'Have', 'Has'], 'medium'],
            ["_____ does that jacket cost?", ['How often', 'How long', 'How many', 'How much'], 'easy'],
            ["_____ did you leave your job?", ['Where', 'What', 'Why', 'Which'], 'easy'],
            ["They _____ fly to India.", ["didn't", "doesn't", "haven't", 'has'], 'medium'],
            ["_____ you like a coffee?", ['Will', 'Would', 'Did', 'Do'], 'easy'],
            ["I _____ to go home now.", ["'m wanting", 'will want', 'want', 'wanting'], 'medium'],
            ["She _____ in Belgium at the moment.", ['living', "'s lived", 'is living', 'lives'], 'medium'],
            ["Nick _____ gets up at 7 o'clock and leaves for work at 8 o'clock. He does this every day from Monday to Friday.", ['always', 'never', 'sometimes', 'hardly ever'], 'easy'],
            ["We go shopping _____ month.", ['every a', 'once', 'every two', 'twice a'], 'medium'],
            ["I'm not keen on _____.", ['run', 'to running', 'to run', 'running'], 'medium'],
            ["What _____ tonight?", ['they do', 'do they do', 'are they doing', 'are they do'], 'medium'],
            ["Are you _____ the new exhibition at the National Gallery?", ['going to see', 'seeing', 'going see', 'go to see'], 'medium'],
            ["_____ laptop is that? Is it Bob's?", ['Which', 'Who', 'What', 'Whose'], 'medium'],
            ["I have never _____ a dangerous sport.", ['do', 'made', 'make', 'done'], 'medium'],
            ["_____ he ever flown in a helicopter?", ['Did', 'Do', 'Has', 'Have'], 'medium'],
            ["You _____ be late for school again.", ["mustn't", 'have to', 'must', 'can'], 'medium'],
            ["You _____ wear a suit. It's a very formal party.", ["shouldn't", 'have to', "mustn't", 'might'], 'medium'],
            ["I _____ dinner when I heard a strange noise.", ['was cook', 'did cook', 'was cooking', 'am doing'], 'medium'],
            ["When did she decide _____ married?", ['to get', 'got', 'to', 'get'], 'medium'],
            ["We should avoid _____ in August.", ['travelled', 'to travel', 'travelling', 'to travelling'], 'medium'],
            ["He's studied Spanish _____ last year.", ['since', 'the', 'at', '-'], 'medium'],
            ["I don't think I _____ get the job. I didn't answer all their questions in the interview very well.", ["'ll", 'might', "won't", 'might not'], 'medium'],
            ["She _____ here this weekend.", ['will being', 'may to be', "won't to be", 'might be'], 'medium'],
            ["We haven't seen them _____ years.", ['at', 'since', 'for', 'ages'], 'medium'],
            ["How long have you _____ him?", ['know', 'knew', 'knows', 'known'], 'medium'],
            ["I _____ wear a uniform to school.", ['use to', "didn't use to", 'used', "didn't used to"], 'medium'],
            ["Did they _____ in Australia?", ['use to live', 'used to live', 'used live', 'used to living'], 'medium'],
            ["She's moving to Canada _____ she can study English.", ['so that', 'so to', 'because to', 'in order to'], 'medium'],
            ["I travelled around the world for a year _____ learn about other cultures.", ['for', 'for to', 'to', 'in order'], 'medium'],
            ["He married the girl _____ used to sit next to him at school.", ['who', 'which', 'what', 'whose'], 'medium'],
            ["Children spend _____ hours watching TV.", ['too many', 'not enough', 'very', 'too much'], 'medium'],
            ["I don't have _____ time to do the things I enjoy.", ['time enough', 'enough time', 'too many time', 'some time'], 'medium'],
            ["Can I try this coat _____, please?", ['in', 'on', 'to', 'up'], 'medium'],
            ["It's _____ beautiful house I've ever seen.", ['most', 'more', 'the most', 'a most'], 'medium'],
            ["There's more traffic and _____ space to walk in the streets nowadays.", ['more', 'less', 'least', 'bigger'], 'medium'],
            ["I think travelling by plane is _____ easier than travelling by car.", ['more', 'most', '-', 'the most'], 'medium'],
            ["She worked as a teacher in _____ Africa.", ['-', 'a', 'an', 'the'], 'medium'],
            ["They live in _____ south of France.", ['-', 'a', 'an', 'the'], 'medium'],
            ["He _____ to work in his company's office in Shanghai.", ['sent', 'was', 'was send', 'was sent'], 'medium'],
            ["I _____ that I'm like my father.", ["'m told", 'told', 'was said', 'am said'], 'medium'],
            ["The postman hasn't delivered the parcel _____.", ['just', 'already', 'yet', 'never'], 'medium'],
            ["My brother _____ passed his exams.", ['just has', 'already has', 'yet', "'s just"], 'medium'],
            ["A lot of people think that when they _____, they'll have lots of free time, but often they don't.", ['will retire', 'retire', 'retired', "won't retire"], 'medium'],
            ["What _____ happen if he doesn't get here in time?", ['will', 'it would', 'will it', 'it will'], 'medium'],
            ["If you save some money, you _____ to worry any more.", ["'ll have", "don't", 'will', "won't have"], 'medium'],
            ["He _____ me my book would be a great success.", ['told', 'said', 'say', 'tell'], 'medium'],
            ["She told me she _____ buy me a new phone.", ["don't", "won't to", "'ll", "'d"], 'medium'],
            ["What would they do if they _____ have any money?", ['were', "didn't", "won't", "'d"], 'medium'],
            ["I'd do more exercise if I _____ time.", ['have', "'d have", "'ll have", 'had'], 'medium'],
            ["_____ be possible to reserve a table for tonight?", ['Can I', 'Could you', 'Would it', 'Will'], 'medium'],
            ["Could you _____ a good film?", ['say me', 'recommend', 'tell me', 'advice'], 'medium'],
            ["When _____ arrive?", ['they did', 'did they', 'have they', 'does they'], 'medium'],
            ["Who _____ all this mess?", ['made', 'did make', 'are make', 'was making'], 'medium'],
            ["He's French but he _____ in London at the moment.", ['living', 'does live', "'s living", 'lived'], 'medium'],
            ["What _____ of doing now?", ['are you think', 'do you think', 'think you', 'are you thinking'], 'medium'],
            ["I _____ so tired that I went to bed shortly after dinner.", ["'m", 'had been', 'was', "'ve felt"], 'medium'],
            ["Have you told them the good news _____?", ['just', 'yet', 'last night', 'never'], 'medium'],
            ["_____ Thai food?", ['Did she ever ate', 'Has she ever eaten', 'Does she ate', 'Have she ever eaten'], 'medium'],
            ["We _____ to work yesterday when we heard a loud crash behind us.", ['was walking', "'ve walked", 'were walking', 'walked'], 'medium'],
            ["They realised they _____ to take her address so they had to go back and get it.", ["'ve forgot", "'d forgot", "'d forgotten", 'were forgot'], 'medium'],
            ["He _____ there before so he found it very exciting.", ["hadn't been", "didn't go", "wasn't", "hasn't been"], 'medium'],
            ["We _____ on holiday tomorrow so I hope the weather stays warm.", ["'ve gone", "'ll going", "'re going", 'will to go'], 'medium'],
            ["They _____ to call at this time of night. It's very late.", ['going', 'might', 'may well', "'re unlikely"], 'medium'],
            ["Do you think they _____ the championship?", ['may well win', "'ll win", "'re winning", "'ll can win"], 'medium'],
            ["The room _____ look more cheerful if you paint it yellow.", ['is', 'is probably', 'will probably', 'probably might'], 'medium'],
            ["He _____ to pass his driving test this time. He's making too many mistakes.", ['could', "'s not going", 'definitely won\'t', "can't"], 'medium'],
            ["People _____ smoke in public buildings. It is not allowed.", ["don't have to", 'must', "shouldn't", "mustn't"], 'medium'],
            ["You _____ enter the marathon if you don't want to.", ['must', 'should', "don't have to", 'have to'], 'medium'],
            ["My advice is that you _____ find another job. You can't work with that awful boss any more.", ["don't have", 'should', 'must', "mustn't"], 'medium'],
            ["I _____ be very good at sports when I was a teenager.", ['would', "wasn't", 'use to', 'used to'], 'medium'],
            ["She _____ often sit in the garden after coming home from work.", ['use to', 'would', "didn't use", 'often would'], 'medium'],
            ["Swimming is one of the _____ ways to get fit.", ['betterer', 'more better', 'most better', 'best'], 'medium'],
            ["The red shoes were _____ expensive than the black ones.", ['far more', 'bit more', 'further', 'not as'], 'medium'],
            ["That shop's not _____ it used to be.", ['more cheap than', 'as cheap as', 'the cheapest as', 'as cheaper as'], 'medium'],
            ["We _____ have to leave yet, do we?", ['will', "won't", "aren't", "don't"], 'medium'],
            ["His father was a famous writer, _____?", ["isn't he", "hasn't he", "wasn't he", 'was he'], 'medium'],
            ["I can't work if I _____ very hungry.", ['feel', "'m feel", "'ll feel", 'can feel'], 'medium'],
            ["He won't pass the exam _____ he doesn't study hard for it.", ['if', 'when', 'unless', 'while'], 'medium'],
            ["Could I borrow your car if I _____ to drive it carefully?", ['might promise', "'ll promise", 'would promise', 'promise'], 'medium'],
            ["He _____ see the film if he went with an adult.", ['should', "'ll", 'could', 'can'], 'medium'],
            ["If I _____ you, I'd take it back to the shop.", ['am', 'were', 'have been', 'had been'], 'medium'],
            ["How long have you _____ the violin?", ['been playing', 'play', 'playing', 'being played'], 'medium'],
            ["I _____ listening to jazz music.", ["'ve always enjoying", "'ve always enjoyed", 'was always enjoyed', "'ve enjoyed always"], 'medium'],
            ["He _____ swim by the time he was five but he hasn't learnt to dive yet.", ['can to', "couldn't", 'could', "can't to"], 'medium'],
            ["We _____ to go to the match but we managed to watch it on TV.", ["weren't able", "can't", 'were able', 'could'], 'medium'],
            ["I've just seen _____ perfect car for you!", ['an', 'the', '-', 'a'], 'medium'],
            ["I didn't know they had a summer cottage _____ south coast of Spain.", ['on the', 'on a', '-', 'on'], 'medium'],
            ["They seem to have _____ money but they don't have many friends.", ['a lot', 'few of', 'plenty of', 'many'], 'medium'],
            ["We don't have _____ time to go on holiday.", ['enough', 'enough of', 'many', 'several'], 'medium'],
            ["That's the beach _____ I first met your father.", ['when', 'which', 'where', 'that'], 'medium'],
            ["Is that the coat _____ you said you wanted to buy?", ['-', 'what', 'who', 'how'], 'medium'],
            ["I've got a new job, _____ is why I've moved to Brussels.", ['that', 'where', 'it', 'which'], 'medium'],
            ["We _____ arrived on time if the traffic hadn't been so bad.", ['had', "wouldn't", "would've", "'d"], 'hard'],
            ["What _____ if you hadn't been there?", ["he'd done", 'will he do', 'would he do', 'would he have done'], 'hard'],
            ["They _____ told what to do yet.", ["wasn't been", "haven't been", "hasn't been", "haven't being"], 'hard'],
            ["Their furniture _____ by Anne's husband, who used to be a carpenter.", ['that was made', 'being made', 'was made', 'has made'], 'hard'],
            ["They didn't want to stay late but the boss said they _____ to.", ["haven't", 'had', "'d had", 'have'], 'hard'],
            ["She said she _____ to do it last weekend.", ['was going', "'s going", 'had done', 'will'], 'hard'],
            ["I asked her if she _____ my new mobile.", ["'s seen", "'d seen", "'d see", 'saw'], 'hard'],
            ["He promised _____ me decorate my house.", ['that he help', 'helping', 'to helping', 'to help'], 'hard'],
            ["Why don't we _____ to take them to the airport?", ['suggest', 'warn', 'offer', 'explain'], 'hard'],
            ["What _____ at the end of the film? I missed it.", ['did happen', 'happened', "'s happened", 'was happened'], 'hard'],
            ["Do you mind me asking how old _____?", ['are you', "you're", 'you are', 'you have'], 'hard'],
            ["_____ if you'd like to meet up tomorrow.", ["I'd be interested to", 'Can I ask', 'Do you know', 'I was wondering'], 'hard'],
            ["They _____ in a large house for twenty years before moving to a bungalow.", ["'ve lived", 'lived', 'already live', 'recently lived'], 'hard'],
            ["Have you heard from your brother _____?", ['recently', 'still', 'last week', 'this time last month'], 'hard'],
            ["I _____ for my car keys for half an hour and I still haven't found them!", ["'ve been looked", "'ve been looking", 'looked', "'m looking"], 'hard'],
            ["Billy _____ watching motorbike races.", ['always has loved', "'s always loving", "'s always loved", "'s always been loving"], 'hard'],
            ["Her parents are very proud. She _____ a fantastic job in a well known law firm.", ['been offered', 'offered', 'is offered', "'s been offered"], 'hard'],
            ["How much do you think he _____ as director of the company?", ['is been paid', 'has being paid', "'s being paid", 'being paid'], 'hard'],
            ["You should _____ what to do when you get to the office.", ['be told', 'be tell', 'told', 'tell'], 'hard'],
            ["He _____ for his plane for an hour when it was suddenly cancelled.", ['was waiting', "'d been waiting", 'waited', "'s been waiting"], 'hard'],
            ["When we walked into the hotel, a log fire _____ in the fireplace.", ['burnt', "'d burnt", 'was burning', "'s burning"], 'hard'],
            ["My car's just broken down for the third time. I wish I _____ it.", ["didn't buy", "'d bought", "wasn't buying", "hadn't bought"], 'hard'],
            ["If only _____ a foreign language. I didn't have the choice at school.", ['I can speak', 'I speak', 'I could speak', 'I could to speak'], 'hard'],
            ["Jim _____ sharing a flat but now he prefers to live on his own.", ['used to like', 'would like', 'never use to like', 'never used to'], 'hard'],
            ["Liane _____ complaining about her long drive to work. In the end, she decided to change jobs.", ['is generally', 'used to', 'would be', 'was always'], 'hard'],
            ["They _____ us at the weekend. It depends on the weather.", ['might visit', 'are visiting', 'are going to visit', 'due to visit'], 'hard'],
            ["He _____ hungry when he gets home from football practice.", ['likely to be', "'s likely to be", 'like to be', "'ll like to be"], 'hard'],
            ["We _____ to get married next April.", ["'ll probably", "'re planning", "'re definitely", 'might'], 'hard'],
            ["How many people have climbed _____ Mount Everest?", ['a', 'the', 'in', '-'], 'hard'],
            ["The children played in the garden with _____ ball I'd given them.", ['a', 'the', 'an', '-'], 'hard'],
            ["If you lend me €200, I _____ you back at the end of the month.", ['might paid', 'will pay', "'ll paying", 'would pay'], 'hard'],
            ["If I _____ to your proposal, when could we sign the contract?", ['was agreed', 'were agreeing', 'were to agree', "weren't agree"], 'hard'],
            ["Children under 15 could attend _____ they were with an adult.", ['providing to', 'as long as', 'if only', 'as long than'], 'hard'],
            ["We _____ show our passports when we left the country.", ["mustn't", "weren't allowed", 'were obliged', "didn't have to"], 'hard'],
            ["We had very little petrol left in the car but we _____ get home in the end.", ['must', 'managed to', 'were able', 'could'], 'hard'],
            ["This time tomorrow, they _____ in San Francisco. How exciting!", ["'ll land", "'ll be landed", "'ll be landing", 'have landed'], 'hard'],
            ["When do you think you _____ painting the house?", ['finish', "'ll have finished", 'be finishing', 'to finish'], 'hard'],
            ["There are still _____ citizens who feel unhappy about the changes made by the government.", ['a little of', 'quite many', 'quite a few', 'a great deal'], 'hard'],
            ["_____ of people rely on public transport to get to work these days.", ['Not many', 'A little', 'Quite a few', 'Plenty'], 'hard'],
            ["Dad says he _____ to cook pasta for dinner tonight.", ["'s going", 'was', 'would', 'might'], 'hard'],
            ["The teacher wanted to know why _____ his homework last night.", ["hadn't Tom done", "Tom hasn't done", "Tom hadn't done", "Tom isn't doing"], 'hard'],
            ["They told _____ start work the following Monday.", ['me I can', 'me', "I'll", 'I could'], 'hard'],
            ["If she _____ to be fluent in French, she could have applied for the job.", ["hadn't needed", 'needs', "doesn't need", "'s needed"], 'hard'],
            ["They _____ here by now if the train had been on time.", ["weren't", "would've be", "'d been", "'d have been"], 'hard'],
            ["If I hadn't ignored my parents advice, I _____ a great musician.", ["would've become", "might've became", 'had became', "hadn't become"], 'hard'],
            ["_____ a world class athlete is a lot harder than it looks.", ['For being', 'To be', 'To being', 'Being'], 'hard'],
            ["You'd _____ late for work again or you'll get fired.", ['be better', 'better not be', 'better be', 'better not being'], 'hard'],
            ["She went to the doctor because she keeps _____ headaches.", ['on to get', 'to getting', 'to get', 'getting'], 'hard'],
            ["She finally stopped _____ when the price of cigarettes went up again.", ['to smoke', 'the smoke', 'smoking', 'for to smoke'], 'hard'],
            ["I'll never _____ snow for the first time.", ['forget seeing', 'forget to see', 'forget to seeing', 'to forget seeing'], 'hard'],
            ["Do you ever regret _____ Canada and returning to your home country?", ['to leaving', 'leaving', 'for leaving', 'to leave'], 'hard'],
            ["We _____ a serious accident when you drove through that red light!", ['have had', "can't have had", "must've had", "could've had"], 'hard'],
            ["The neighbours _____ the music from your party. It was terribly loud. You should go and apologise to them.", ["must've heard", "'ll hear", "couldn't hear", "can't have heard"], 'hard'],
            ["The gym _____ I go to work out is open twenty-four hours a day.", ['when', 'which', 'that', 'where'], 'hard'],
            ["I wasn't keen on the restaurant _____ we went to last weekend.", ['what', '-', 'whose', 'when'], 'hard'],
            ["They're having lunch with his girlfriend's parents, _____ live in Brighton.", ['they', 'who', 'that', 'which'], 'hard'],
            ["I almost fell over a pile of books _____ on the carpet.", ['that are laid', 'which lying', 'lying', 'been written'], 'hard'],
            ["A book _____ by a twelve-year-old girl has won a €10,000 prize.", ['wrote', 'writing', 'been written', 'written'], 'hard'],
            ["_____ people's names has always been something I've found incredibly difficult.", ['To remember', 'For remembering', 'Remembering', 'To remembering'], 'hard'],
        ];

        $order = 14;
        foreach ($items as [$text, $options, $difficulty]) {
            $question = ExamQuestion::create([
                'branch_id' => null,
                'skill_id' => $grammarSkillId,
                'type' => 'mcq',
                'difficulty' => $difficulty,
                'question_text' => $text,
                'explanation' => null,
                'estimated_time_seconds' => 45,
                'marks' => 1,
                'status' => 'draft', // stays draft until an admin sets the correct answers
                'created_by_type' => 'admin',
                'created_by_id' => $adminId,
            ]);

            foreach ($options as $i => $optionText) {
                ExamQuestionOption::create([
                    'question_id' => $question->id,
                    'option_text' => $optionText,
                    'is_correct' => false, // not provided in the source material — must be set manually
                    'sort_order' => $i,
                ]);
            }
            $order++;
        }

        // Closing Writing (essay) question — graded manually.
        ExamQuestion::create([
            'branch_id' => null,
            'skill_id' => ExamSkill::where('slug', 'writing')->value('id'),
            'type' => 'text',
            'difficulty' => 'hard',
            'question_text' => "Part 2. Writing\n\nWrite at least 100 words about ONE of the following topics:\n1. The day you decided to change your life.\n2. An online class compared to a traditional class.\n3. The effect of a parent, teacher, or friend on your life.",
            'explanation' => null,
            'estimated_time_seconds' => 900,
            'marks' => 10,
            'status' => 'active',
            'created_by_type' => 'admin',
            'created_by_id' => $adminId,
        ]);

        $this->command?->info('Seeded ' . (count($items) + 1) . ' placement test questions (188). MCQ answers are NOT marked — set the correct option for each in the admin Question Bank before publishing an exam that uses them.');
    }
}
