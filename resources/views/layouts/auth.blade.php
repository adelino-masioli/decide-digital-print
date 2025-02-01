<div class="min-h-screen bg-black">
    <div class="flex flex-col items-center justify-center min-h-screen">
        <div class="w-full max-w-[440px] bg-gray-900 p-8 rounded-lg ring-1 ring-white/10">
            @if (isset($header))
                <div class="mb-8 text-center">
                    {{ $header }}
                </div>
            @endif

            <main class="w-full">
                {{ $slot }}
            </main>
        </div>
    </div>
</div> 