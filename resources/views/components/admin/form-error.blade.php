@props(['errors'])

@if ($errors && $errors->any())
    <div class="admin-form-error" role="alert">
        <span class="material-symbols-outlined" aria-hidden="true">error</span>
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif
