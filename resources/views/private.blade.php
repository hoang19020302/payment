@extends('layouts.app')

@section('content')
    <h1 class="text-center text-3xl">Listening on Private Channel</h1>
    <p id="log" class="text-center text-2xl">Chờ dữ liệu...</p>
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
                const channel = window.Echo.join('chat-room');

                channel.listen('.MessageSent', (event) => {
                    console.log(event);
                    document.getElementById('log').innerHTML = event.message.content;
                    console.log(event.message.content);
                    alert(event.message.content);
                })

                channel.here((users) => {
                    console.log('Users online:', users);
                    // axios.post('/api/presence/here', { users });
                });

                channel.joining((user) => {
                    console.log('User joined:', user);
                    // axios.post('/api/presence/join', { user });
                });

                channel.leaving((user) => {
                    console.log('User left:', user);
                    // axios.post('/api/presence/leave', { user });
                });
            });
        });
    </script>
@endsection
