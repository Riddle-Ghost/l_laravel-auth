@if(session('success'))
    <div class="alert alert-info">
        {{session('success')}}
    </div>
@endif
@if(session('error'))
    <div class="alert alert-danger">
        {{session('error')}}
    </div>
@endif
@if(session('status'))
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="alert alert-info">
                    {{session('status')}}
                </div>
            </div>
        </div>
    </div>
@endif
@if ($errors->any())
    <div class="alert alert-danger">
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif