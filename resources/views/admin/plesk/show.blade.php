@extends('admin.layouts.app')

@section('header')
    {{ $domain }}
@endsection

@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">

{{-- Action Bar --}}
<div class="mb-6 flex items-center justify-between">
    <a href="{{ route('plesk.index') }}" class="text-gray-600 hover:text-black text-sm tinos-regular">Back to sites</a>
    <form method="POST" action="{{ route('plesk.domain-refresh', $domain) }}">
        @csrf
        <button type="submit" class="bg-white text-black border-black border border-r-3 border-b-3 px-4 py-2 text-sm tinos-bold hover:bg-gray-100">
            Refresh
        </button>
    </form>
</div>

{{-- Stat Cards --}}
<div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4 mb-8">
    <div class="bg-white border-black border border-r-3 border-b-3 p-5">
        <div class="text-sm text-gray-600 tinos-regular">Status</div>
        <div class="mt-1 text-xl tinos-bold text-black">
            @if(isset($domainInfo['status']) && $domainInfo['status'] !== 0)
                Suspended
            @else
                Active
            @endif
        </div>
    </div>

    <div class="bg-white border-black border border-r-3 border-b-3 p-5">
        <div class="text-sm text-gray-600 tinos-regular">PHP Version</div>
        <div class="mt-1 text-xl tinos-bold text-black">{{ $detail['php_version'] ?? '—' }}</div>
    </div>

    <div class="bg-white border-black border border-r-3 border-b-3 p-5">
        <div class="text-sm text-gray-600 tinos-regular">Disk Usage</div>
        <div class="mt-1 text-xl tinos-bold text-black">{{ $detail['disk_usage'] ?? '—' }}</div>
    </div>

    <div class="bg-white border-black border border-r-3 border-b-3 p-5">
        <div class="text-sm text-gray-600 tinos-regular">SSL</div>
        <div class="mt-1 text-xl tinos-bold text-black">{{ $detail['ssl'] ? 'Enabled' : 'Disabled' }}</div>
    </div>
</div>

{{-- Hosting Details + Databases --}}
<div class="grid grid-cols-1 gap-6 lg:grid-cols-2 mb-8">
    <div class="bg-white border-black border border-r-3 border-b-3 p-6">
        <h3 class="text-lg tinos-bold text-black mb-4">Hosting Details</h3>
        <dl class="space-y-3">
            @if($detail['doc_root'])
                <div class="border-b border-gray-200 pb-2">
                    <dt class="text-xs text-gray-500 tinos-regular">Document Root</dt>
                    <dd class="text-sm text-black tinos-bold mt-0.5 break-all">{{ $detail['doc_root'] }}</dd>
                </div>
            @endif
            @if($detail['ip'])
                <div class="border-b border-gray-200 pb-2">
                    <dt class="text-xs text-gray-500 tinos-regular">IP Address</dt>
                    <dd class="text-sm text-black tinos-bold mt-0.5">{{ $detail['ip'] }}</dd>
                </div>
            @endif
        </dl>
    </div>

    <div class="bg-white border-black border border-r-3 border-b-3 p-6">
        <h3 class="text-lg tinos-bold text-black mb-4">Databases</h3>
        @if(empty($databases))
            <p class="text-sm text-gray-600 tinos-regular">No databases found.</p>
        @else
            <ul class="space-y-3">
                @foreach($databases as $db)
                    <li class="border-b border-gray-200 pb-2">
                        <div class="text-sm tinos-bold text-black">{{ $db['name'] ?? $db['db-name'] ?? 'Unknown' }}</div>
                        <div class="text-xs text-gray-500 tinos-regular">
                            {{ $db['type'] ?? $db['db-server-type'] ?? '' }}
                        </div>
                    </li>
                @endforeach
            </ul>
        @endif
    </div>
</div>

{{-- SSH Terminal --}}
<div class="bg-black border-black border border-r-3 border-b-3 mb-8">
    <div class="flex items-center justify-between px-4 py-2 border-b border-gray-700">
        <span class="text-green-400 text-sm font-mono font-bold">Terminal</span>
        <span class="text-gray-500 text-xs font-mono">{{ $projectRoot }}</span>
    </div>
    <div id="terminal-output" class="px-4 py-3 font-mono text-sm text-green-400 overflow-y-auto" style="max-height: 400px; min-height: 200px;">
        <div class="text-gray-500">Connected to {{ $domain }}</div>
    </div>
    <div class="flex items-center px-4 py-2 border-t border-gray-700">
        <span class="text-green-400 font-mono text-sm mr-2">$</span>
        <input
            type="text"
            id="terminal-input"
            class="flex-1 bg-transparent text-green-400 font-mono text-sm outline-none border-none placeholder-gray-600"
            placeholder="Type a command..."
            autocomplete="off"
            spellcheck="false"
        >
    </div>
</div>

<script>
(function() {
    const output = document.getElementById('terminal-output');
    const input = document.getElementById('terminal-input');
    const sshUrl = @json(route('plesk.ssh', $domain));
    const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
    const history = [];
    let historyIndex = -1;

    function appendLine(text, className = 'text-green-400') {
        const line = document.createElement('div');
        line.className = className + ' whitespace-pre-wrap break-all';
        line.textContent = text;
        output.appendChild(line);
        output.scrollTop = output.scrollHeight;
    }

    async function runCommand(command) {
        appendLine('$ ' + command, 'text-gray-400');

        try {
            const res = await fetch(sshUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json',
                },
                body: JSON.stringify({ command }),
            });

            const data = await res.json();
            if (data.output && data.output.trim()) {
                appendLine(data.output.trim());
            }
        } catch (err) {
            appendLine('Connection error: ' + err.message, 'text-red-400');
        }
    }

    input.addEventListener('keydown', function(e) {
        if (e.key === 'Enter') {
            const command = input.value.trim();
            if (!command) return;

            history.push(command);
            historyIndex = history.length;
            input.value = '';
            runCommand(command);
        } else if (e.key === 'ArrowUp') {
            e.preventDefault();
            if (historyIndex > 0) {
                historyIndex--;
                input.value = history[historyIndex];
            }
        } else if (e.key === 'ArrowDown') {
            e.preventDefault();
            if (historyIndex < history.length - 1) {
                historyIndex++;
                input.value = history[historyIndex];
            } else {
                historyIndex = history.length;
                input.value = '';
            }
        }
    });

    input.focus();
})();
</script>
@endsection
