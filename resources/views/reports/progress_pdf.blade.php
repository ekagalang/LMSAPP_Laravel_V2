<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Progress Report - {{ $course->title }}</title>
    <style>
        body { 
            font-family: Arial, sans-serif; 
            font-size: 10px; 
            margin: 20px;
        }
        .header { 
            text-align: center; 
            margin-bottom: 30px; 
            border-bottom: 2px solid #333;
            padding-bottom: 10px;
        }
        .course-info {
            background-color: #f8f9fa;
            padding: 10px;
            margin-bottom: 20px;
            border-radius: 5px;
        }
        .participant {
            margin-bottom: 25px;
            page-break-inside: avoid;
            border: 1px solid #ddd;
            padding: 15px;
            border-radius: 5px;
        }
        .progress-bar { 
            width: 100%; 
            height: 15px; 
            background-color: #e9ecef; 
            border-radius: 7px; 
            overflow: hidden;
            margin: 10px 0;
        }
        .progress-fill { 
            height: 100%; 
            background-color: #28a745; 
            border-radius: 7px;
        }
        .score-table { 
            width: 100%; 
            border-collapse: collapse; 
            margin-top: 15px;
            font-size: 9px;
        }
        .score-table th, .score-table td { 
            border: 1px solid #ddd; 
            padding: 6px; 
            text-align: left; 
        }
        .score-table th { 
            background-color: #f1f3f4; 
            font-weight: bold;
        }
        .summary {
            background-color: #e8f4fd;
            padding: 10px;
            margin-top: 10px;
            border-radius: 5px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Course Progress Report</h1>
        <h2>{{ $course->title }}</h2>
        <p><strong>Generated on:</strong> {{ $date }}</p>
        <p><strong>Total Participants:</strong> {{ $participantsProgress->count() }}</p>
    </div>

    <div class="course-info">
        <h3>Course Information</h3>
        <p><strong>Title:</strong> {{ $course->title }}</p>
        <p><strong>Description:</strong> {{ Str::limit($course->description, 200) }}</p>
        <p><strong>Total Contents:</strong> {{ $participantsProgress->first()->total_count ?? 0 }}</p>
    </div>

    @foreach($participantsProgress as $participant)
    <div class="participant">
        <h3>{{ $participant->name }}</h3>
        <p><strong>Email:</strong> {{ $participant->email }}</p>
        
        <!-- Progress Bar -->
        <div class="progress-section">
            <p><strong>Overall Progress:</strong></p>
            <div class="progress-bar">
                <div class="progress-fill" style="width: {{ $participant->progress_percentage ?? $participant->progressPercentage }}%"></div>
            </div>
            <p><strong>{{ $participant->progress_percentage ?? $participant->progressPercentage }}%</strong> 
               ({{ $participant->completed_count ?? 0 }}/{{ $participant->total_count ?? 0 }} contents completed)</p>
        </div>

        <!-- Quiz Scores -->
        @if(isset($participant->quiz_scores) && $participant->quiz_scores->count() > 0)
        <div class="quiz-section">
            <h4>Quiz Scores</h4>
            <table class="score-table">
                <thead>
                    <tr>
                        <th>Quiz Title</th>
                        <th>Score</th>
                        <th>Max Score</th>
                        <th>Percentage</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($participant->quiz_scores as $score)
                    <tr>
                        <td>{{ $score['quiz_title'] ?? $score->quiz->title }}</td>
                        <td>{{ $score['score'] ?? $score->score }}</td>
                        <td>{{ $score['max_score'] ?? $score->quiz->questions->count() }}</td>
                        <td>{{ $score['percentage'] ?? round(($score->score / $score->quiz->questions->count()) * 100, 2) }}%</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            @if(isset($participant->quiz_average))
            <p><strong>Average Quiz Score:</strong> {{ round($participant->quiz_average, 2) }}%</p>
            @endif
        </div>
        @endif

        <!-- Essay Scores -->
        @if(isset($participant->essay_scores) && $participant->essay_scores->count() > 0)
        <div class="essay-section">
            <h4>Essay Scores</h4>
            <table class="score-table">
                <thead>
                    <tr>
                        <th>Essay Title</th>
                        <th>Score</th>
                        <th>Percentage</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($participant->essay_scores as $score)
                    <tr>
                        <td>{{ $score['essay_title'] ?? $score->content->title }}</td>
                        <td>{{ $score['score'] ?? $score->score }}/100</td>
                        <td>{{ $score['percentage'] ?? $score->score }}%</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            @if(isset($participant->essay_average))
            <p><strong>Average Essay Score:</strong> {{ round($participant->essay_average, 2) }}%</p>
            @endif
        </div>
        @endif

        <!-- Summary -->
        <div class="summary">
            <p><strong>Summary for {{ $participant->name }}:</strong></p>
            <ul style="margin: 5px 0; padding-left: 20px;">
                <li>Course Progress: {{ $participant->progress_percentage ?? $participant->progressPercentage }}%</li>
                @if(isset($participant->quiz_average))
                <li>Average Quiz Score: {{ round($participant->quiz_average, 2) }}%</li>
                @endif
                @if(isset($participant->essay_average))
                <li>Average Essay Score: {{ round($participant->essay_average, 2) }}%</li>
                @endif
            </ul>
        </div>
    </div>
    @endforeach

    <!-- Overall Statistics -->
    <div style="margin-top: 30px; border-top: 2px solid #333; padding-top: 20px;">
        <h3>Course Statistics</h3>
        @php
            $avgProgress = $participantsProgress->avg(function($p) { 
                return $p->progress_percentage ?? $p->progressPercentage; 
            });
            $completedCount = $participantsProgress->filter(function($p) { 
                return ($p->progress_percentage ?? $p->progressPercentage) >= 100; 
            })->count();
        @endphp
        <p><strong>Average Course Progress:</strong> {{ round($avgProgress, 2) }}%</p>
        <p><strong>Participants who completed course:</strong> {{ $completedCount }}/{{ $participantsProgress->count() }}</p>
        <p><strong>Completion Rate:</strong> {{ $participantsProgress->count() > 0 ? round(($completedCount / $participantsProgress->count()) * 100, 2) : 0 }}%</p>
    </div>
</body>
</html>