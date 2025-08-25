<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Certificate</title>
    <style>
        @page {
            margin: 0;
            padding: 0;
            size: 1123px 794px;
        }
        
        html, body { 
            margin: 0; 
            padding: 0;
            font-family: 'Times New Roman', serif; 
            width: 1123px; 
            height: 794px; 
            position: relative; 
            overflow: hidden;
            background: white;
        }
        
        .certificate-container {
            width: 1123px;
            height: 794px;
            position: relative;
            margin: 0;
            padding: 0;
        }
        
        .bg { 
            position: absolute; 
            top: 0; 
            left: 0; 
            width: 100%; 
            height: 100%; 
            object-fit: cover; 
            z-index: 1; 
        }
        
        .content { 
            position: relative; 
            z-index: 2; 
            width: 100%; 
            height: 100%; 
        }
        
        .element { 
            position: absolute; 
            word-wrap: break-word; 
            white-space: pre-wrap;
            display: flex;
            align-items: center;
            line-height: 1.2;
        }
        
        .text-left {
            justify-content: flex-start;
            text-align: left;
        }
        
        .text-center {
            justify-content: center;
            text-align: center;
        }
        
        .text-right {
            justify-content: flex-end;
            text-align: right;
        }
        
        .default-cert {
            padding: 80px;
            text-align: center;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            height: calc(100% - 160px);
            display: flex;
            flex-direction: column;
            justify-content: center;
        }
    </style>
</head>
<body>
    <div class="certificate-container">
        @php
            $hasTemplate = $certificate->certificateTemplate && $certificate->certificateTemplate->layout_data;
            $layoutData = $hasTemplate ? $certificate->certificateTemplate->layout_data : null;
            $scale = 1.0; // No scale needed - 1:1 matching with template editor
        @endphp
        
        @if($hasTemplate && is_array($layoutData))
            @php
                if (isset($layoutData[0])) {
                    $pages = $layoutData;
                } else {
                    $pages = [$layoutData];
                }
            @endphp
            
            @foreach($pages as $pageIndex => $page)
                @if($pageIndex > 0)
                    <div style="page-break-before: always;"></div>
                @endif
                
                <!-- Background Image dengan BASE64 encoding -->
                @if(isset($page['background_image_path']) && $page['background_image_path'])
                    @php
                        $bgPath = $page['background_image_path'];
                        $base64Image = null;
                        
                        if (\Storage::disk('public')->exists($bgPath)) {
                            $fullPath = storage_path('app/public/' . $bgPath);
                            if (file_exists($fullPath)) {
                                try {
                                    $imageData = file_get_contents($fullPath);
                                    $imageType = strtolower(pathinfo($fullPath, PATHINFO_EXTENSION));
                                    $mimeType = match($imageType) {
                                        'jpg', 'jpeg' => 'jpeg',
                                        'png' => 'png',
                                        'gif' => 'gif',
                                        'webp' => 'webp',
                                        default => 'png'
                                    };
                                    $base64Image = 'data:image/' . $mimeType . ';base64,' . base64_encode($imageData);
                                } catch (\Exception $e) {
                                    $base64Image = null;
                                }
                            }
                        }
                    @endphp
                    
                    @if($base64Image)
                        <img src="{{ $base64Image }}" class="bg">
                    @endif
                @endif
                
                <!-- Elements dengan scaling yang tepat -->
                <div class="content">
                    @if(isset($page['elements']) && is_array($page['elements']))
                        @foreach($page['elements'] as $element)
                            @php
                                $text = $element['content'] ?? $element['text'] ?? '';
                                
                                // Placeholder replacement - handle both formats
                                $replacements = [
                                    // Standard format with braces
                                    '{{name}}' => $certificate->user->name,
                                    '{{participant_name}}' => $certificate->user->name,
                                    '{{course}}' => $certificate->course->title,
                                    '{{course_title}}' => $certificate->course->title,
                                    '{{date}}' => $certificate->issued_at->format('F j, Y'),
                                    '{{issue_date}}' => $certificate->issued_at->format('F j, Y'),
                                    '{{issue_date_id}}' => $certificate->issued_at->format('d F Y'),
                                    '{{certificate_code}}' => $certificate->certificate_code,
                                    '{{instructor_name}}' => $certificate->course->instructors->first()?->name ?? 'Instructor',
                                    '{{instructor}}' => $certificate->course->instructors->first()?->name ?? 'Instructor',
                                    '{{score}}' => '100',
                                    '{{course_summary}}' => strip_tags($certificate->course->description) ?: 'Course completed successfully',
                                    
                                    // Enhanced template format with @ prefix
                                    '@{{name}}' => $certificate->user->name,
                                    '@{{course}}' => $certificate->course->title,
                                    '@{{date}}' => $certificate->issued_at->format('F j, Y'),
                                    '@{{score}}' => '100',
                                    '@{{certificate_code}}' => $certificate->certificate_code,
                                    '@{{course_summary}}' => strip_tags($certificate->course->description) ?: 'Course completed successfully',
                                ];
                                
                                foreach ($replacements as $placeholder => $value) {
                                    $text = str_replace($placeholder, $value, $text);
                                }
                                
                                // Apply scaling
                                $x = ($element['x'] ?? 0) * $scale;
                                $y = ($element['y'] ?? 0) * $scale;
                                $fontSize = ($element['fontSize'] ?? 16) * $scale;
                            @endphp
                            
                            @php
                                $textAlign = $element['textAlign'] ?? 'left';
                                $width = ($element['width'] ?? 200) * $scale;
                                $height = ($element['height'] ?? 40) * $scale;
                            @endphp
                            
                            <div class="element text-{{ $textAlign }}" style="
                                left: {{ $x }}px;
                                top: {{ $y }}px;
                                width: {{ $width }}px;
                                height: {{ $height }}px;
                                font-size: {{ $fontSize }}px;
                                color: {{ $element['color'] ?? '#000' }};
                                font-weight: {{ ($element['isBold'] ?? false) ? 'bold' : 'normal' }};
                                font-style: {{ ($element['isItalic'] ?? false) ? 'italic' : 'normal' }};
                                text-decoration: {{ ($element['isUnderline'] ?? false) ? 'underline' : 'none' }};
                                font-family: {{ $element['fontFamily'] ?? 'Times New Roman' }};
                            ">{{ $text }}</div>
                        @endforeach
                    @endif
                </div>
            @endforeach
        @else
            <!-- Default beautiful template -->
            <div class="default-cert">
                <h1 style="font-size: 48px; margin-bottom: 30px; text-shadow: 2px 2px 4px rgba(0,0,0,0.3);">CERTIFICATE OF COMPLETION</h1>
                <p style="font-size: 24px; margin-bottom: 40px; opacity: 0.9;">This is to certify that</p>
                <h2 style="font-size: 36px; margin: 30px 0; padding: 20px; border: 3px solid white; background: rgba(255,255,255,0.1); border-radius: 10px;">
                    {{ $certificate->user->name }}
                </h2>
                <p style="font-size: 24px; margin-bottom: 30px; opacity: 0.9;">has successfully completed the course</p>
                <h3 style="font-size: 28px; margin: 30px 0; font-weight: bold;">{{ $certificate->course->title }}</h3>
                <p style="font-size: 16px; margin-top: 40px; opacity: 0.8;">
                    Certificate ID: {{ $certificate->certificate_code }}<br>
                    Date Issued: {{ $certificate->issued_at->format('F j, Y') }}<br>
                    @if($certificate->course->instructors->first())
                        Instructor: {{ $certificate->course->instructors->first()->name }}
                    @endif
                </p>
            </div>
        @endif
    </div>
</body>
</html>