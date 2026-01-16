<!DOCTYPE html>
<html lang="{{\Illuminate\Support\Facades\App::getLocale()}}">

<head>
    <title>{{env('APP_NAME')}}</title>
</head>

<body dir="rtl">
    <h1>مشروع جديد مرفق</h1>
    <p><strong>اسم الطالب:</strong> {{ $studentName }}</p>
    <p><strong>الجامعة:</strong> {{ $studentUniversity }}</p>
    <p><strong>السنة الدراسية:</strong> {{ $studentYear }}</p>
    <p><strong>المادة:</strong> {{ $projectSubject }}</p>
</body>

</html>