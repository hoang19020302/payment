@extends('layouts.app')

@section('content')
    <h1>Sent message on Broadcast Channel</h1>
    @if (session('success'))
        <div class="bg-green-100 border border-green-400 text-green-800 px-4 py-3 rounded mb-6" role="alert">
            <strong class="font-semibold">✅ Thành công! </strong><span>{{ session('success') }}</span></strong>
        </div>
    @endif
    <form action="{{ route('broadcast.send') }}" method="POST" class="mt-4 mb-4">
        @csrf
        <label class="block mb-2 text-gray-700" for="msg">Message:</label>
        <input class="w-full border border-gray-300 rounded py-2 px-4" type="text" name="msg">
        <button class="bg-blue-500 hover:bg-blue-700 text-dark font-bold py-2 px-4 rounded" type="submit">Send</button>
    </form>
@endsection