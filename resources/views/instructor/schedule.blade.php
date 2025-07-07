<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Schedule a Class') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-10 text-gray-900">
                    <form action="{{ route('schedule.store') }}" method="post" class="max-w-lg">
                        @csrf
                        <div class="space-y-6">
                            <div>
                                <label for="class_type_id" class="text-sm">Select type of class</label>
                                <select name="class_type_id" id="class_type_id" class="block mt-2 w-full border-gray-300 focus:ring-0 focus:border-gray-500" required>
                                    <option value="" disabled {{ old('class_type_id') ? '' : 'selected' }}>Select a class</option>
                                    @foreach ($classTypes as $classType)
                                        {{-- Use old() to re-select the previous value on validation error --}}
                                        <option value="{{ $classType->id }}" {{ old('class_type_id') == $classType->id ? 'selected' : '' }}>{{ $classType->name }}</option>
                                    @endforeach
                                </select>
                                {{-- Display the error for class_type_id --}}
                                @error('class_type_id')
                                <div class="text-sm text-red-600 mt-2">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="flex gap-6">
                                <div class="flex-1">
                                    <label for="date" class="text-sm">Date</label>
                                    <input type="date" name="date" id="date" value="{{ old('date') }}" class="block mt-2 w-full border-gray-300 focus:ring-0 focus:border-gray-500" min="{{ now()->toDateString() }}" required>
                                    @error('date')
                                    <div class="text-sm text-red-600 mt-2">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="flex-1">
                                    <label for="time" class="text-sm">Time</label>
                                    <select name="time" id="time" class="block mt-2 w-full border-gray-300 focus:ring-0 focus:border-gray-500" required>
                                        @for ($time = strtotime('06:00'); $time <= strtotime('20:30'); $time += 30 * 60)
                                            <option value="{{ date('H:i:s', $time) }}" {{ old('time') == date('H:i:s', $time) ? 'selected' : '' }}>{{ date('H:i', $time) }}</option>
                                        @endfor
                                    </select>
                                    @error('time')
                                    <div class="text-sm text-red-600 mt-2">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div>
                                @error('scheduled_at')
                                <div class="text-sm text-red-600">{{ $message }}</div>
                                @enderror
                            </div>
                            <div>
                                <x-primary-button>{{ __('Schedule') }}</x-primary-button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
