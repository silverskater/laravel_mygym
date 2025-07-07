<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Book a Class') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-10 text-gray-900 max-w-2xl divide-y">
                    @forelse ($scheduledClasses as $class)
                        <div class="py-6">
                            <div class="flex gap-6 justify-between">
                                <div>
                                    <p class="text-2xl font-bold text-purple-700">{{ $class->classType->name }}</p>
                                    <p class="text-sm">{{ $class->instructor->name }}</p>
                                    <p class="mt-2">{{ $class->classType->description }}</p>
                                    <span class="text-slate-600 text-sm">{{ $class->classType->duration }} minutes</span>
                                </div>
                                <div class="text-right flex-shrink-0">
                                    <p class="text-lg font-bold">{{ $class->scheduled_at->format('H:i') }}</p>
                                    <p class="text-sm">{{ $class->scheduled_at->format('jS M') }}</p>
                                </div>
                            </div>
                            <div class="mt-1 text-right">
                                <form method="post" action="{{ route('member.booking.store') }}">
                                    @csrf
                                    <input type="hidden" name="scheduled_class_id" value="{{ $class->id }}">
                                    <x-primary-button class="px-3 py-1">{{ __('Book') }}</x-primary-button>
                                </form>
                                @if(old('scheduled_class_id') == $class->id)
                                    @error('scheduled_class_id')
                                    <p class="text-sm text-red-600 mt-2">{{ $message }}</p>
                                    @enderror
                                @endif
                            </div>
                        </div>
                    @empty
                        <div>
                            <p>{{ __('No classes are scheduled. Check back later!') }}</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
