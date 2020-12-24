<form method="post" enctype="multipart/form-data" action="{{ route('image') }}">
    {{ csrf_field() }}
    <input type="file" name="image">
    <input type="submit">
</form>
@isset($files)
    @foreach ($files as $file)
        <img src="{{ asset("images/{$file}") }}" alt="">
    @endforeach
@endisset

