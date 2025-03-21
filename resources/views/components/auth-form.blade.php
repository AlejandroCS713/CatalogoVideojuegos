<div class="form-container2">
    <form class="form" action="{{ route($route) }}" method="POST" autocomplete="off">
        @csrf
        <div class="control">
            <h1>{{ __($title) }}</h1>
        </div>

        @foreach ($fields as $field)
            <div class="control block-cube block-input">
                <input type="{{ $field['type'] }}" name="{{ $field['name'] }}" placeholder="{{ __($field['placeholder']) }}" required>
                <div class="bg-top"><div class="bg-inner"></div></div>
                <div class="bg-right"><div class="bg-inner"></div></div>
                <div class="bg"><div class="bg-inner"></div></div>
            </div>
        @endforeach

        <button class="btn block-cube block-cube-hover" type="submit">
            <div class="bg-top"><div class="bg-inner"></div></div>
            <div class="bg-right"><div class="bg-inner"></div></div>
            <div class="bg"><div class="bg-inner"></div></div>
            <span class="text">{{ __($buttonText) }}</span>
        </button>
        <br>

        @if ($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
    </form>
</div>
