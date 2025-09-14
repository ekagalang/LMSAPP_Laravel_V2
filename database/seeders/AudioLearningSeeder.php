<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\AudioLesson;
use App\Models\AudioExercise;

class AudioLearningSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Beginner Level Lessons
        $beginnerLesson1 = AudioLesson::create([
            'title' => 'Basic Greetings',
            'description' => 'Learn common English greetings and responses',
            'audio_file_path' => 'audio/lessons/beginner-greetings.mp3',
            'duration_seconds' => 120,
            'difficulty_level' => 'beginner',
            'transcript' => "Hello! Good morning. How are you today? I'm fine, thank you. Nice to meet you. Have a good day!",
            'is_active' => true,
            'sort_order' => 1
        ]);

        // Exercises for Basic Greetings
        AudioExercise::create([
            'audio_lesson_id' => $beginnerLesson1->id,
            'title' => 'Multiple Choice: Common Greetings',
            'question' => 'What is the most common greeting in English?',
            'exercise_type' => 'multiple_choice',
            'options' => ['Hello', 'Goodbye', 'Thank you', 'Please'],
            'correct_answers' => ['Hello'],
            'points' => 10,
            'play_from_seconds' => 0,
            'play_to_seconds' => 30,
            'sort_order' => 1
        ]);

        AudioExercise::create([
            'audio_lesson_id' => $beginnerLesson1->id,
            'title' => 'Fill in the Blank: Response',
            'question' => 'Complete the response: "How are you?" - "I\'m _____, thank you."',
            'exercise_type' => 'fill_blank',
            'correct_answers' => ['fine', 'good', 'well', 'okay'],
            'points' => 15,
            'play_from_seconds' => 30,
            'play_to_seconds' => 60,
            'sort_order' => 2
        ]);

        AudioExercise::create([
            'audio_lesson_id' => $beginnerLesson1->id,
            'title' => 'Speech Practice: Greeting',
            'question' => 'Say "Hello, how are you?" clearly',
            'exercise_type' => 'speech_response',
            'correct_answers' => ['hello how are you', 'hello, how are you'],
            'speech_recognition_keywords' => ['hello', 'how', 'are', 'you'],
            'points' => 20,
            'play_from_seconds' => 0,
            'play_to_seconds' => 15,
            'sort_order' => 3
        ]);

        $beginnerLesson2 = AudioLesson::create([
            'title' => 'Numbers 1-20',
            'description' => 'Learn to recognize and pronounce numbers from 1 to 20',
            'audio_file_path' => 'audio/lessons/beginner-numbers.mp3',
            'duration_seconds' => 180,
            'difficulty_level' => 'beginner',
            'transcript' => "One, two, three, four, five, six, seven, eight, nine, ten. Eleven, twelve, thirteen, fourteen, fifteen, sixteen, seventeen, eighteen, nineteen, twenty.",
            'is_active' => true,
            'sort_order' => 2
        ]);

        AudioExercise::create([
            'audio_lesson_id' => $beginnerLesson2->id,
            'title' => 'Number Recognition',
            'question' => 'Which number comes after fifteen?',
            'exercise_type' => 'multiple_choice',
            'options' => ['fourteen', 'sixteen', 'seventeen', 'eighteen'],
            'correct_answers' => ['sixteen'],
            'points' => 10,
            'sort_order' => 1
        ]);

        AudioExercise::create([
            'audio_lesson_id' => $beginnerLesson2->id,
            'title' => 'Count Along',
            'question' => 'Say the numbers from one to five',
            'exercise_type' => 'speech_response',
            'correct_answers' => ['one two three four five', '1 2 3 4 5'],
            'speech_recognition_keywords' => ['one', 'two', 'three', 'four', 'five'],
            'points' => 25,
            'sort_order' => 2
        ]);

        // Intermediate Level Lessons
        $intermediateLesson1 = AudioLesson::create([
            'title' => 'Restaurant Conversations',
            'description' => 'Common phrases and vocabulary used in restaurants',
            'audio_file_path' => 'audio/lessons/intermediate-restaurant.mp3',
            'duration_seconds' => 240,
            'difficulty_level' => 'intermediate',
            'transcript' => "Good evening. Do you have a reservation? Yes, a table for two under Johnson. Right this way, please. Here are your menus. What would you like to drink? I'll have a coffee, please. And I'll have water. Are you ready to order? Yes, I'd like the grilled chicken, please.",
            'is_active' => true,
            'sort_order' => 1
        ]);

        AudioExercise::create([
            'audio_lesson_id' => $intermediateLesson1->id,
            'title' => 'Restaurant Vocabulary',
            'question' => 'What does the waiter ask first when customers arrive?',
            'exercise_type' => 'multiple_choice',
            'options' => ['What would you like to eat?', 'Do you have a reservation?', 'How many people?', 'Are you ready to order?'],
            'correct_answers' => ['Do you have a reservation?'],
            'points' => 15,
            'play_from_seconds' => 0,
            'play_to_seconds' => 45,
            'sort_order' => 1
        ]);

        AudioExercise::create([
            'audio_lesson_id' => $intermediateLesson1->id,
            'title' => 'Order Practice',
            'question' => 'Practice ordering: "I\'d like the grilled chicken, please"',
            'exercise_type' => 'speech_response',
            'correct_answers' => ['id like the grilled chicken please', 'i would like the grilled chicken please'],
            'speech_recognition_keywords' => ['like', 'grilled', 'chicken', 'please'],
            'points' => 25,
            'play_from_seconds' => 120,
            'play_to_seconds' => 150,
            'sort_order' => 2
        ]);

        AudioExercise::create([
            'audio_lesson_id' => $intermediateLesson1->id,
            'title' => 'Comprehension Questions',
            'question' => 'Describe the restaurant conversation you heard. What did the customers order?',
            'exercise_type' => 'comprehension',
            'correct_answers' => ['The customers had a reservation for two people. They ordered coffee, water, and grilled chicken.'],
            'points' => 30,
            'sort_order' => 3
        ]);

        // Advanced Level Lesson
        $advancedLesson1 = AudioLesson::create([
            'title' => 'Business Meeting Discussion',
            'description' => 'Advanced vocabulary and expressions used in professional business meetings',
            'audio_file_path' => 'audio/lessons/advanced-business.mp3',
            'duration_seconds' => 300,
            'difficulty_level' => 'advanced',
            'transcript' => "Good morning, everyone. Let's begin today's meeting. First on the agenda is our quarterly sales report. As you can see from the presentation, we've exceeded our targets by 15%. However, we need to address some challenges in the European market. Market penetration has been slower than anticipated, and we should consider alternative strategies.",
            'is_active' => true,
            'sort_order' => 1
        ]);

        AudioExercise::create([
            'audio_lesson_id' => $advancedLesson1->id,
            'title' => 'Business Vocabulary',
            'question' => 'What does "exceeded our targets" mean in a business context?',
            'exercise_type' => 'multiple_choice',
            'options' => [
                'Failed to reach our goals',
                'Performed better than expected',
                'Met exactly what we planned',
                'Changed our objectives'
            ],
            'correct_answers' => ['Performed better than expected'],
            'points' => 20,
            'play_from_seconds' => 60,
            'play_to_seconds' => 120,
            'sort_order' => 1
        ]);

        AudioExercise::create([
            'audio_lesson_id' => $advancedLesson1->id,
            'title' => 'Professional Speaking',
            'question' => 'Practice saying: "We need to address some challenges in the European market"',
            'exercise_type' => 'speech_response',
            'correct_answers' => ['we need to address some challenges in the european market'],
            'speech_recognition_keywords' => ['need', 'address', 'challenges', 'european', 'market'],
            'points' => 30,
            'play_from_seconds' => 120,
            'play_to_seconds' => 180,
            'sort_order' => 2
        ]);

        AudioExercise::create([
            'audio_lesson_id' => $advancedLesson1->id,
            'title' => 'Critical Analysis',
            'question' => 'Analyze the business situation described in the meeting. What are the main points discussed and what solutions might you suggest?',
            'exercise_type' => 'comprehension',
            'correct_answers' => ['The meeting discussed quarterly sales exceeding targets by 15%, challenges in European market penetration, and the need for alternative strategies.'],
            'points' => 40,
            'sort_order' => 3
        ]);

        $this->command->info('Audio learning sample data created successfully!');
        $this->command->info('Created lessons:');
        $this->command->info('- Beginner: Basic Greetings, Numbers 1-20');
        $this->command->info('- Intermediate: Restaurant Conversations');
        $this->command->info('- Advanced: Business Meeting Discussion');
        $this->command->info('Total exercises created: ' . AudioExercise::count());
    }
}
