@if(session('success'))
    <div class="alert-orderer alert-success-orderer mb-3">
        <div class="container-fluid">
            <p>
                <i class="feather-check-circle me-2"></i>
                {{ session('success') }}
                <button type="button" class="alert-close-btn"
                        onclick="this.parentElement.parentElement.remove()">&times;</button>
            </p>
        </div>
    </div>
@endif

@if(session('error'))
    <div class="alert-orderer alert-error-orderer mb-3">
        <div class="container-fluid">
            <p>
                <i class="feather-x-circle me-2"></i>
                {{ session('error') }}
                <button type="button" class="alert-close-btn"
                        onclick="this.parentElement.parentElement.remove()">&times;</button>
            </p>
        </div>
    </div>
@endif

@if(session('info'))
    <div class="alert-orderer alert-info-orderer mb-3">
        <div class="container-fluid">
            <p>
                <i class="feather-info me-2"></i>
                {{ session('info') }}
                <button type="button" class="alert-close-btn"
                        onclick="this.parentElement.parentElement.remove()">&times;</button>
            </p>
        </div>
    </div>
@endif

@if($errors->any())
    <div class="alert-orderer alert-error-orderer mb-3">
        <div class="container-fluid">
            @foreach($errors->all() as $error)
                <p><i class="feather-alert-circle me-2"></i> {{ $error }}</p>
            @endforeach
        </div>
    </div>
@endif