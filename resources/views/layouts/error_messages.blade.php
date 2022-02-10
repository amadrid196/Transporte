{{--for message--}}
<style>
    .alert {
        position: fixed;
        z-index: 2000;
        text-align: center;
        color: #ffffff;
        width: 50vw;
        left: 25vw;
    }

    .action_success, .action_error {
        bottom: 10%;
    }

    .action_success {
        background-color: #5bb85c;
        border-color: #5bc0de;
    }

    .laravel_errors, .action_error {
        background-color: #db532f;
        border-color: #5bc0de;
    }

    .call_back_error {
        position: fixed;
        z-index: 999;
        text-align: center;
        color: #ffffff;
        background-color: #db532f;
        border-color: #5bc0de;
        top: 10%;
        right: 5%;
        width: unset;
        left: unset;
    }
</style>
<div class="action_display" style="position: absolute;">
    <div id="action_message_container" class="col-sm-8 col-lg-4 col-md-8" style="margin: 0 auto; float: unset;">
        <div class="alert alert-dismissible fadeIn hide action_success">
            <button class="close" data-dismiss="alert" aria-label="close">&times;</button>
            <strong>Success!</strong> {{ session()->get('message') }}
        </div>
        <div class="alert alert-dismissible fadeIn hide action_error">
            <button class="close" data-dismiss="alert" aria-label="close">&times;</button>
            <strong>Error!</strong> {{ session()->get('error') }}
        </div>
        @if(session()->has('errors'))
            <?php $bottom = 10; ?>
            @foreach($errors->default->messages() as $message)
                <div class="alert alert-dismissible fadeIn hide laravel_errors" id="laravel_errors" style="bottom: {{$bottom."%"}};">
                    <button class="close" data-dismiss="alert" aria-label="close">&times;</button>
                    <strong>Error! </strong> {{$message[0]}}
                </div>
                <?php $bottom = $bottom + 8; ?>
            @endforeach
        @endif
        <script>
            var popup_delay = 5000;

            function show_message(className, delay = 0) {
                if (delay != 0) {
                    popup_delay = delay;
                }
                var elements = document.getElementsByClassName(className);
                console.log(elements, elements[0]);
                for (i = 0; i < elements.length; i++) {
                    elements[i].className = elements[i].className.replace(/\bhide\b/g, "");
                }
                window.setTimeout(function () {
                    for (i = 0; i < elements.length; i++) {
                        elements[i].className += " hide";
                    }
                }, popup_delay);
            }

            @if(session()->has('message'))
            show_message("action_success");
            @elseif(session()->has('error'))
            show_message("action_error");
            @elseif(session()->has('errors'))
            show_message("laravel_errors");
            @endif
        </script>
    </div>
</div>
{{--end message--}}