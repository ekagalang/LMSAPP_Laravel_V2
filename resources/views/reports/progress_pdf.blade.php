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
        <p><strong>Description:</strong> {{ Str::limit($course->description ?? 'No description', 200) }}</p>
        <p><strong>Total Contents:</strong> {{ $total_content_count ?? ($participantsProgress->first()->total_count ?? 0) }}</p>
    </div>

    @foreach($participantsProgress as $participant)
    <div class="participant">
        {{-- ✅ FIX: Handle both object and array access safely --}}
        <h3>{{ is_object($participant) ? $participant->name : $participant['name'] }}</h3>
        <p><strong>Email:</strong> {{ is_object($participant) ? $participant->email : $participant['email'] }}</p>
        
        <!-- Progress Bar -->
        <div class="progress-section">
            <p><strong>Overall Progress:</strong></p>
            @php
                $progress = is_object($participant) ? $participant->progress_percentage : $participant['progress_percentage'];
                $completed = is_object($participant) ? $participant->completed_count : $participant['completed_count'];
                $total = is_object($participant) ? $participant->total_count : $participant['total_count'];
            @endphp
            <div class="progress-bar">
                <div class="progress-fill" style="width: {{ $progress }}%"></div>
            </div>
            <p><strong>{{ $progress }}%</strong> 
               ({{ $completed }}/{{ $total }} contents completed)</p>
        </div>

        {{-- Quiz Scores --}}
        @php
            $quizScores = is_object($participant) ? $participant->quiz_scores : collect($participant['quiz_scores'] ?? []);
            $quizAverage = is_object($participant) ? $participant->quiz_average : ($participant['quiz_average'] ?? 0);
        @endphp
        
        @if($quizScores && $quizScores->count() > 0)
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
                    @foreach($quizScores as $score)
                    <tr>
                        <td>{{ is_array($score) ? $score['quiz_title'] : $score->quiz_title }}</td>
                        <td>{{ is_array($score) ? $score['score'] : $score->score }}</td>
                        <td>{{ is_array($score) ? $score['max_score'] : $score->max_score }}</td>
                        <td>{{ is_array($score) ? $score['percentage'] : $score->percentage }}%</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            <p><strong>Average Quiz Score:</strong> {{ round($quizAverage, 2) }}%</p>
        </div>
        @endif

        {{-- Essay Scores --}}
        @php
            $essayScores = is_object($participant) ? $participant->essay_scores : collect($participant['essay_scores'] ?? []);
            $essayAverage = is_object($participant) ? $participant->essay_average : ($participant['essay_average'] ?? 0);
        @endphp
        
        @if($essayScores && $essayScores->count() > 0)
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
                    @foreach($essayScores as $score)
                    <tr>
                        <td>{{ is_array($score) ? $score['essay_title'] : $score->essay_title }}</td>
                        <td>{{ is_array($score) ? $score['score'] : $score->score }}/100</td>
                        <td>{{ is_array($score) ? $score['percentage'] : $score->percentage }}%</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            <p><strong>Average Essay Score:</strong> {{ round($essayAverage, 2) }}%</p>
        </div>
        @endif

        <!-- Summary -->
        <div class="summary">
            <p><strong>Summary for {{ is_object($participant) ? $participant->name : $participant['name'] }}:</strong></p>
            <ul style="margin: 5px 0; padding-left: 20px;">
                <li>Course Progress: {{ $progress }}%</li>
                <li>Completed Contents: {{ $completed }}/{{ $total }}</li>
                @if($quizScores->count() > 0)
                <li>Average Quiz Score: {{ round($quizAverage, 2) }}%</li>
                @endif
                @if($essayScores->count() > 0)
                <li>Average Essay Score: {{ round($essayAverage, 2) }}%</li>
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
                return is_object($p) ? $p->progress_percentage : $p['progress_percentage']; 
            });
            $completedCount = $participantsProgress->filter(function($p) { 
                $progress = is_object($p) ? $p->progress_percentage : $p['progress_percentage'];
                return $progress >= 100; 
            })->count();
        @endphp
        <p><strong>Average Course Progress:</strong> {{ round($avgProgress, 2) }}%</p>
        <p><strong>Participants who completed course:</strong> {{ $completedCount }}/{{ $participantsProgress->count() }}</p>
        <p><strong>Completion Rate:</strong> {{ $participantsProgress->count() > 0 ? round(($completedCount / $participantsProgress->count()) * 100, 2) : 0 }}%</p>
    </div>
</body>
</html>