</html>
<!DOCTYPE html>
<html lang="{{ \Illuminate\Support\Facades\App::getLocale() }}">

<head>
    <title>{{ env('APP_NAME') }}</title>
</head>

<body dir="rtl">
    <h1>طلب مشروع تخرج جديد</h1>
    <p><strong>اسم الطالب:</strong> {{ $studentName }}</p>
    <p><strong>الجامعة:</strong> {{ $studentUniversity }}</p>
    <p><strong>فكرة مشروع التخرج:</strong> {{ $projectSubject }}</p>
</body>

</html>
