@if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif

<form action="{{ route('pdf.upload') }}" method="POST" enctype="multipart/form-data">
    @csrf

    <input type="file" name="pdfs[]" multiple accept=".pdf">

    <button type="submit">Upload</button>
</form>
