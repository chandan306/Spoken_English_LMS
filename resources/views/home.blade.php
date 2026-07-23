<!DOCTYPE html>
<html>

<head>

    <title>Spoken English LMS</title>

    @vite(['resources/css/app.css','resources/js/app.js'])
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
</head>

<body>

@include('layouts.navbar')

@include('sections.hero')
@include('sections.why-choose')
@include('sections.courses')
@include('sections.teachers')
{{-- @include('sections.founder') --}}
@include('sections.testimonials')
@include('sections.statistics')
@include('sections.gallery')
@include('sections.video')
@include('sections.demo-banner')
@include('sections.blog')
@include('sections.faq')
@include('sections.contact')


<a href="https://wa.me/917079152907?text=Hello%20I%20want%20to%20join%20your%20Spoken%20English%20Course"
   class="whatsapp-float"
   target="_blank">

    <i class="bi bi-whatsapp"></i>

</a>
<!-- Back To Top Button -->
<button id="backToTop" class="btn btn-primary rounded-circle shadow">
    <i class="bi bi-arrow-up"></i>
</button>
@include('layouts.footer')

</body>

</html>