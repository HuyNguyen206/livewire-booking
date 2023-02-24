<div>
    <form wire:submit.prevent="bookApp">
        <div class="input mb-4 flex flex-col">
            <label class="font-semibold mb-2" for="">Select service</label>

            <select wire:model="service" class="border-0 rounded-2xl" name="" id="">
                <option value="">Select service</option>
                @foreach($services as $s)
                    <option wire:key="service_{{$s->id}}" value="{{$s->id}}">{{$s->name}} ({{$s->duration}})</option>
                @endforeach
            </select>
            @error('service')
            <p class="text-red-600 mt-2">{{$message}}</p>
            @enderror
        </div>
        <div class="input mb-4 flex flex-col @if(!$service) opacity-50  @endif">
            <label class="font-semibold mb-2 " for="">Select employee</label>
            <select wire:model="employee" class="border-0 rounded-2xl" @if(!$service) disabled @endif name="" id="">
                <option value="">Select an employee</option>
                @foreach($employees as $e)
                    <option wire:key="employee_{{$e->id}}" value="{{$e->id}}">{{$e->name}}</option>
                @endforeach
            </select>
            @error('employee')
            <p class="text-red-600 mt-2">{{$message}}</p>
            @enderror
        </div>
        <div class="input mb-2">
            <label class="font-semibold" for="">Select appointment time</label>
            <div
                class="datepicker p-2 rounded-2xl bg-white mt-2 @if(!$this->canEnableDatePicker) opacity-50 cursor-not-allowed @endif">
                <div class="select-date flex justify-between">
                    <button type="button" wire:click.prevent="getPreviousWeek" class="
                                @if(!$this->canSelectPreviousWeek) invisible @endif
                                @if(!$this->canEnableDatePicker) cursor-not-allowed @endif"
                            @if(!$this->canEnableDatePicker) disabled @endif>
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                             stroke="currentColor" class="w-6 h-6">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                  d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18"/>
                        </svg>
                    </button>

                    <span>{{$seriesOfOrderDay['label']}}</span>
                    <button wire:click.prevent="getNextWeek"
                            class="@if(!$this->canEnableDatePicker) cursor-not-allowed @endif"
                            @if(!$this->canEnableDatePicker) disabled @endif >
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                             stroke="currentColor" class="w-6 h-6">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                  d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3"/>
                        </svg>
                    </button>

                </div>
                <div class="orderDay flex justify-between px-2 text-sm my-2">
                    @foreach($seriesOfOrderDay['days'] as $day)
                        <div wire:key="{{$day['digit']['fullDate']}}" class="flex flex-col items-center justify-center space-y-2 group
                                          @if(!$this->canEnableDatePicker) cursor-not-allowed
                                            @else cursor-pointer
                                          @endif">
                            <span wire:key="span_{{$day['digit']['fullDate']}}">{{$day['text']}}</span>
                            <label wire:key="label_{{$day['digit']['fullDate']}}" for="{{$day['digit']['fullDate']}}"
                                   class="@if($this->canEnableDatePicker)cursor-pointer group-hover:bg-gray-500 group-hover:text-white @else cursor-not-allowed @endif
                                   p-2 rounded-xl transition duration-200
                                          @if($this->schedule === $day['digit']['fullDate']) bg-gray-500 text-white @endif">
                                <span>{{$day['digit']['day']}}</span>
                                <input wire:key="input_{{$day['digit']['fullDate']}}" class="hidden"
                                       wire:click="getSlots" id="{{$day['digit']['fullDate']}}" type="radio"
                                       wire:model="schedule" value="{{$day['digit']['fullDate']}}"></input>
                            </label>
                            @error('schedule')
                            <p class="text-red-600 mt-2">{{$message}}</p>
                            @enderror
                        </div>
                    @endforeach
                </div>
                {{--                <div class="orderDay flex justify-between mb-2">--}}
                {{--                    @foreach($seriesOfOrderDay['digit'] as $digit)--}}
                {{--                        <label wire:key="{{$digit['fullDate']}}" for="{{$digit['fullDate']}}"--}}
                {{--                               class="@if(!$this->canEnableDatePicker) cursor-not-allowed @else cursor-pointer @endif--}}
                {{--                               p-2 rounded-xl hover:bg-gray-500 hover:text-white transition duration-200--}}
                {{--                               @if($this->schedule === $digit['fullDate']) bg-gray-500 text-white @endif">--}}
                {{--                            <span>{{$digit['day']}}</span>--}}
                {{--                            <input class="hidden" wire:click="getSlots" id="{{$digit['fullDate']}}" type="radio" wire:model="schedule" value="{{$digit['fullDate']}}"></input>--}}
                {{--                        </label>--}}
                {{--                    @endforeach--}}
                {{--                </div>--}}
                <hr>
                <div class="time max-h-60 overflow-y-auto my-2">
                    @forelse($availableTimeSlots as $s)

                        <label wire:key="key_slot_{{\Illuminate\Support\Str::slug($s['slot']['label'])}}"
                               for="slot_{{\Illuminate\Support\Str::of($s['slot']['label'])}}"
                               class="block p-2 rounded-xl hover:bg-gray-500 hover:text-white transition duration-200
                               @if(!$s['isAvailable']) opacity-50 cursor-not-allowed @else cursor-pointer @endif
                               @if($slot == $s['slot']['timestamp']) bg-gray-500 text-white @endif">
                            <span>{{$s['slot']['label']}}</span>
                            <input @if(!$s['isAvailable']) disabled @endif class="hidden"
                                   id="slot_{{\Illuminate\Support\Str::of($s['slot']['label'])}}" type="radio"
                                   wire:model="slot" value="{{$s['slot']['timestamp']}}"></input>
                        </label>
                        @error('slot')
                        <p class="text-red-600 mt-2">{{$message}}</p>
                        @enderror
                    @empty
                        <div class="text-center mt-2">
                            No slot available
                        </div>
                    @endforelse
                </div>
            </div>
            @if($slot)
                <div class="mt-2">
                    <p class="font-semibold">You're ready to book</p>
                    <hr>
                    <p>
                        <span class="font-semibold">{{$this->serviceModel->name}} {{($this->serviceModel->duration." minutes")}}
                        with {{$this->employeeModel->name}}</span> on {{$this->formatSelectedDateSlot}}
                    </p>
                </div>
                <div class="control-input mt-2">
                    <label for="" class="font-semibold">
                        Your name
                    </label>
                    <input wire:model.defer="name" required type="text" class="w-full border-none rounded">
                    @error('name')
                        <p class="text-red-600 mt-2">{{$message}}</p>
                    @enderror
                </div>
                <div class="control-input">
                    <label for="" class="font-semibold">
                        Your email address
                    </label>
                    <input wire:model.defer="email" required type="email" class="w-full border-none rounded">
                    @error('email')
                    <p class="text-red-600 mt-2">{{$message}}</p>
                    @enderror
                </div>
            @endif
            <div class="flex justify-center mt-2">
                <button
                    class="bg-purple-600 text-white px-4 py-2 rounded-xl  @if(!$slot) opacity-50 cursor-not-allowed @endif"
                    @if(!$slot) disabled @endif>Book
                </button>
            </div>
        </div>
    </form>
</div>
