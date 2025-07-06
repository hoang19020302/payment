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

                window.Echo.connector.pusher.connection.bind('connected', () => {
                    console.log('Socket Connected!');
                    console.log('Socket ID:', window.Echo.socketId());
                });

                const channel = window.Echo.join('chat-room');

                channel.listen('.MessageSent', (event) => {
                    console.log(event);
                    document.getElementById('log').innerHTML = event.message.content;
                    console.log(event.message.content);
                    alert(event.message.content);
                });

                channel.here((users) => {
                    console.log('Socket ID:', window.Echo.socketId());
                    console.log('Users online:', users);
                });

                channel.joining((user) => {
                    console.log('Socket ID:', window.Echo.socketId());
                    console.log('User joined:', user);
                });

                channel.leaving((user) => {
                    console.log('User left:', user);
                });

                // Rời 1 channel
                //window.Echo.leave('chat-room');
                // Hoặc ngắt toàn bộ
                //window.Echo.disconnect();
                // Kêt nói lai
                //window.Echo.connect();
                window.Echo.private(`user.${userId}`)
                .listen('.ForceDisconnect', () => {
                    console.log('🚫 You have been disconnected by admin.');
                    alert('Admin has disconnected you!');
                    
                    // Ngắt WebSocket
                    window.Echo.disconnect();

                    // (Optional) Redirect hoặc logout
                    window.location.href = '/';
                });
                window.Echo.private(`user.${userId}`)
                .listen('.Unbanned', () => {
                    alert('✅ Bạn đã được gỡ ban. Kết nối lại ngay!');
                    window.Echo.connect(); // Hoặc window.location.reload();
                });
            });
        });
    </script>
@endsection
