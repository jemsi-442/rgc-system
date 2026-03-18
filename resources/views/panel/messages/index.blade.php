@extends('layouts.app')

@section('title', 'Branch Chat - RGC')

@section('content')
<div class="card-rgc">
    <h1 class="text-xl font-semibold">Branch Chat</h1>
    <form class="mt-3 flex gap-2" method="POST" action="{{ route('messages.store') }}">
        @csrf
        <input class="w-full rounded border px-3 py-2" name="message" placeholder="Type a message" required>
        <button class="btn-rgc" type="submit">Send</button>
    </form>
    <ul class="mt-4 space-y-2 text-sm" id="branch-chat-list">
        @foreach($messages as $message)
            <li><span class="font-semibold">{{ $message->user->name }}:</span> {{ $message->message }}</li>
        @endforeach
    </ul>
    <div class="mt-3">{{ $messages->links() }}</div>
</div>
<script>
async function pollBranchMessages() {
    const response = await fetch('{{ route('messages.feed') }}');
    const data = await response.json();
    const list = document.getElementById('branch-chat-list');
    list.innerHTML = '';
    data.forEach((item) => {
        const row = document.createElement('li');
        const bold = document.createElement('span');
        bold.className = 'font-semibold';
        bold.textContent = `${item.user.name}: `;
        row.appendChild(bold);
        row.appendChild(document.createTextNode(item.message));
        list.appendChild(row);
    });
}
setInterval(pollBranchMessages, 15000);
</script>
@endsection
