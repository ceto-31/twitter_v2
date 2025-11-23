<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Tweets') }}
        </h2>
    </x-slot>

    <div class="max-w-2xl mx-auto p-4 sm:p-6 lg:p-8">
        
        {{-- Success Message --}}
        @if (session('success'))
            <div class="p-4 mb-4 text-sm text-green-700 bg-green-100 rounded-lg dark:bg-green-200 dark:text-green-800" role="alert">
                {{ session('success') }}
            </div>
        @endif

        {{-- Create Tweet Form (With Character Counter) --}}
        <form method="POST" action="{{ route('tweets.store') }}" x-data="{ content: '' }">
            @csrf
            <textarea
                name="content"
                x-model="content"
                placeholder="What's on your mind?"
                class="block w-full border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-md shadow-sm"
                maxlength="280"
            ></textarea>
            
            {{-- Character Counter Display --}}
            <div class="mt-2 flex justify-end text-sm text-gray-500">
                <span x-text="content.length"></span> / 280
            </div>
            
            <x-input-error :messages="$errors->get('content')" class="mt-2" />
            
            <x-primary-button class="mt-4">
                {{ __('Tweet') }}
            </x-primary-button>
        </form>

        {{-- Tweet List --}}
        <div class="mt-6 bg-white shadow-sm rounded-lg divide-y">
            @foreach ($tweets as $tweet)
                <div class="p-6 flex space-x-2">
                    {{-- Avatar Icon --}}
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-gray-600 -scale-x-100" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                    </svg>
                    
                    <div class="flex-1">
                        <div class="flex justify-between items-center">
                            <div>
                                <span class="text-gray-800 font-bold">{{ $tweet->user->name }}</span>
                                <small class="ml-2 text-sm text-gray-600">{{ $tweet->created_at->format('j M Y, g:i a') }}</small>
                                @if ($tweet->created_at != $tweet->updated_at)
                                    <small class="text-sm text-gray-600"> &middot; {{ __('edited') }}</small>
                                @endif
                            </div>
                            
                            {{-- Dropdown (Edit/Delete) --}}
                            @if ($tweet->user->is(auth()->user()))
                                <x-dropdown>
                                    <x-slot name="trigger">
                                        <button>
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-gray-400" viewBox="0 0 20 20" fill="currentColor">
                                                <path d="M6 10a2 2 0 11-4 0 2 2 0 014 0zM12 10a2 2 0 11-4 0 2 2 0 014 0zM16 12a2 2 0 100-4 2 2 0 000 4z" />
                                            </svg>
                                        </button>
                                    </x-slot>
                                    <x-slot name="content">
                                        <x-dropdown-link :href="route('tweets.edit', $tweet)">
                                            {{ __('Edit') }}
                                        </x-dropdown-link>
                                        <form method="POST" action="{{ route('tweets.destroy', $tweet) }}" onsubmit="return confirm('Are you sure?');">
                                            @csrf
                                            @method('delete')
                                            <x-dropdown-link :href="route('tweets.destroy', $tweet)" onclick="event.preventDefault(); this.closest('form').submit();">
                                                {{ __('Delete') }}
                                            </x-dropdown-link>
                                        </form>
                                    </x-slot>
                                </x-dropdown>
                            @endif
                        </div>
                        
                        <p class="mt-4 text-lg text-gray-900">{{ $tweet->content }}</p>

                        {{-- Like Button (AJAX/Alpine) --}}
                        <div class="mt-4 flex items-center">
                            <button 
                                x-data="{
                                    liked: {{ $tweet->likedBy->contains(auth()->user()) ? 'true' : 'false' }},
                                    count: {{ $tweet->liked_by_count }}
                                }"
                                @click="
                                    fetch('{{ route('tweets.like', $tweet) }}', {
                                        method: 'POST',
                                        headers: {
                                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                            'Content-Type': 'application/json'
                                        }
                                    })
                                    .then(response => response.json())
                                    .then(data => {
                                        liked = data.liked;
                                        count = data.count;
                                    });
                                "
                                class="flex items-center space-x-1 transition-colors duration-200"
                                :class="liked ? 'text-red-500' : 'text-gray-500 hover:text-gray-700'"
                            >
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" :fill="liked ? 'currentColor' : 'none'" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                                </svg>
                                <span x-text="count" class="text-sm font-medium"></span>
                            </button>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</x-app-layout>