@extends('layouts.app')

@section('content')
    <h1>Listening on Private Channel</h1>
    <p id="log">Chờ dữ liệu...</p>
@endsection

@section('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            function waitForEcho(callback) {
                if (window.Echo && typeof window.Echo.private === 'function') {
                    callback();
                } else {
                    setTimeout(() => waitForEcho(callback), 100);
                }
            }

            waitForEcho(function () {
                console.log('Echo ready, subscribing...');

                window.Echo.private('App.Models.User.{{ auth()->id() }}')
                    .listen('.MessageSent', (e) => {
                        console.log('EVENT:', e);
                        console.log(e.message);
                        document.getElementById('log').innerText = e.message;
                        alert('New message received: ' + e.message);
                    });
            });
        });
    </script>
@endsection
