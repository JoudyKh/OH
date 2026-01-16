<!DOCTYPE html>
<html lang="{{ \Illuminate\Support\Facades\App::getLocale() }}">

<head>
    <title>{{ env('APP_NAME') }}</title>
</head>

<body dir="rtl">  
    <h1>طلب لقاء جديد</h1>  
    {{-- <p><strong>اسم الطالب: </strong> {{ $studentName }}</p>   --}}
    <p><strong>نوع الطلب: </strong>  
        @if ($interviewType === 'participation')  
            طلب مشاركة  
        @elseif ($interviewType === 'cartoon_certificate')  
            طلب شهادة كرتونية  
        @elseif ($interviewType === 'electronic_certificate')  
            طلب شهادة الكترونية  
        @else  
            نوع طلب غير معروف  
        @endif  
    </p>  
</body>

</html>
