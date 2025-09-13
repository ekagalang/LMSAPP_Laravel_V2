<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Content;
use App\Models\VideoInteraction;

class VideoInteractionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Find a video content to add interactions to
        $videoContent = Content::where('type', 'video')->first();
        
        if (!$videoContent) {
            $this->command->info('No video content found. Creating a sample video content...');
            
            // Create a sample video content
            $lesson = \App\Models\Lesson::first();
            if (!$lesson) {
                $this->command->error('No lesson found. Please create a course and lesson first.');
                return;
            }
            
            $videoContent = Content::create([
                'lesson_id' => $lesson->id,
                'title' => 'Sample Interactive Video',
                'description' => 'This is a sample video with interactive elements for demonstration.',
                'type' => 'video',
                'body' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ', // Sample YouTube URL
                'order' => Content::where('lesson_id', $lesson->id)->max('order') + 1
            ]);
        }

        $this->command->info("Adding sample interactions to video: {$videoContent->title}");

        // Sample interaction 1: Quiz at 30 seconds
        VideoInteraction::create([
            'content_id' => $videoContent->id,
            'type' => 'quiz',
            'timestamp' => 30.0,
            'title' => 'Quick Knowledge Check',
            'description' => 'What is the main topic of this video?',
            'data' => [
                'options' => [
                    ['text' => 'Web Development', 'index' => 0],
                    ['text' => 'Database Design', 'index' => 1],
                    ['text' => 'Interactive Learning', 'index' => 2],
                    ['text' => 'Mobile Development', 'index' => 3]
                ],
                'correct_answer' => 2,
                'correct_feedback' => 'Correct! This video is about interactive learning.',
                'incorrect_feedback' => 'Not quite right. This video focuses on interactive learning methods.'
            ],
            'position' => null,
            'is_active' => true,
            'order' => 1
        ]);

        // Sample interaction 2: Annotation at 1 minute
        VideoInteraction::create([
            'content_id' => $videoContent->id,
            'type' => 'annotation',
            'timestamp' => 60.0,
            'title' => 'Important Note',
            'description' => 'Pay attention to this key concept that will be important for the assessment.',
            'data' => [
                'duration' => 8
            ],
            'position' => [
                'align' => 'top-right'
            ],
            'is_active' => true,
            'order' => 2
        ]);

        // Sample interaction 3: Hotspot at 2 minutes
        VideoInteraction::create([
            'content_id' => $videoContent->id,
            'type' => 'hotspot',
            'timestamp' => 120.0,
            'title' => 'Learn More',
            'description' => 'Click to see additional resources and references for this topic.',
            'data' => [
                'duration' => 10,
                'resources' => [
                    'MDN Web Docs',
                    'W3Schools',
                    'Stack Overflow'
                ]
            ],
            'position' => [
                'x' => 75,
                'y' => 25
            ],
            'is_active' => true,
            'order' => 3
        ]);

        // Sample interaction 4: Pause for reflection at 3 minutes
        VideoInteraction::create([
            'content_id' => $videoContent->id,
            'type' => 'pause',
            'timestamp' => 180.0,
            'title' => 'Reflection Moment',
            'description' => 'Take a moment to think about how you would apply this concept in your own work.',
            'data' => [
                'auto_resume' => false
            ],
            'position' => null,
            'is_active' => true,
            'order' => 4
        ]);

        // Sample interaction 5: Another quiz at 4.5 minutes
        VideoInteraction::create([
            'content_id' => $videoContent->id,
            'type' => 'quiz',
            'timestamp' => 270.0,
            'title' => 'Application Question',
            'description' => 'Which of the following is the best practice mentioned in the video?',
            'data' => [
                'options' => [
                    ['text' => 'Always use the latest technology', 'index' => 0],
                    ['text' => 'Focus on user experience first', 'index' => 1],
                    ['text' => 'Optimize for search engines', 'index' => 2],
                    ['text' => 'Write as much code as possible', 'index' => 3]
                ],
                'correct_answer' => 1,
                'correct_feedback' => 'Excellent! User experience should always be the priority.',
                'incorrect_feedback' => 'Think again. The video emphasizes the importance of user experience.'
            ],
            'position' => null,
            'is_active' => true,
            'order' => 5
        ]);

        // Sample interaction 6: Overlay with key takeaways
        VideoInteraction::create([
            'content_id' => $videoContent->id,
            'type' => 'overlay',
            'timestamp' => 350.0,
            'title' => 'Key Takeaways',
            'description' => 'Remember these important points from today\'s lesson.',
            'data' => [
                'duration' => 5,
                'takeaways' => [
                    'Interactive elements improve engagement',
                    'User feedback is crucial for learning',
                    'Technology should enhance, not complicate'
                ]
            ],
            'position' => [
                'align' => 'bottom'
            ],
            'is_active' => true,
            'order' => 6
        ]);

        $this->command->info('Successfully created 6 sample video interactions!');
        $this->command->info("Content ID: {$videoContent->id}");
        $this->command->info("You can view the interactive video at: /contents/{$videoContent->id}");
    }
}
