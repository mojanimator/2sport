@if (session()->has('error-alert'))

    <div class="alert alert-danger alert-dismissible fade show top-0 start-0 m-3 z-index-3 text-right position-absolute"
         role="alert" style="z-index: 1000 !important;">
        <strong>  {{session()->get('error-alert')}}</strong>
        <button type="button" class="close" data-bs-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
    @php(session()->forget('error-alert'))

@elseif (session()->has('success-alert'))

    <div class="alert alert-success alert-dismissible fade show top-0 start-0 m-3 z-index-3 text-right position-absolute"
         role="alert" style="z-index: 1000 !important;">
        <strong>   {!! session()->get('success-alert') !!}</strong>
        <button type="button" class="close" data-bs-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
    @php(session()->forget('success-alert'))
@endif


<div id="toast" class="toast position-fixed align-items-center text-white  m-2 border-0 bottom-0 start-0 "
     style="z-index: 1000 !important;"
     role="alert"
     aria-live="assertive"
     aria-atomic="true">
    <div class="d-flex">
        <div id="toast-msg" class="toast-body">

        </div>
        <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"
                aria-label="Close"></button>
    </div>
</div>