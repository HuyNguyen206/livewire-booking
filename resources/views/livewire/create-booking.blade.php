<div>
        <div class="input mb-4 flex flex-col">
            <label class="font-semibold mb-2" for="">Select service</label>

            <select wire:model="service" class="border-0 rounded-2xl" name="" id="">
                <option value="">Select service</option>
                @foreach($services as $s)
                    <option wire:key="service_{{$s->id}}" value="{{$s->id}}">{{$s->name}} ({{$s->duration}})</option>
                @endforeach
            </select>
        </div>
            <div class="input mb-4 flex flex-col @if(!$service) opacity-50  @endif">
                <label class="font-semibold mb-2 " for="">Select employee</label>
                <select wire:model="employee" class="border-0 rounded-2xl" @if(!$service) disabled  @endif name="" id="">
                    <option value="">Select an employee</option>
                    @foreach($employees as $e)
                        <option wire:key="employee_{{$e->id}}" value="{{$e->id}}">{{$e->name}}</option>
                    @endforeach
                </select>
            </div>
        <div class="input mb-2">
            <label class="font-semibold" for="">Select appointment time</label>
            <div class="datepicker p-2 rounded-2xl bg-white mt-2 @if(!$this->canEnableDatePicker) opacity-50 cursor-not-allowed @endif">
                <div class="select-date flex justify-between">
                    <span>&leftarrow;</span>
                    <span>{{$seriesOfOrderDay['label']}}</span>
                    <span>&rightarrow;</span>
                </div>
                <div class="orderDay flex justify-between px-2 text-sm my-2">
                    @foreach($seriesOfOrderDay['text'] as $text)
                        <span>{{$text}}</span>
                    @endforeach
                </div>
                <div class="orderDay flex justify-between mb-2">
                    @foreach($seriesOfOrderDay['digit'] as $digit)
                        <label wire:key="{{$digit['fullDateTime']}}" for="{{$digit['fullDateTime']}}"
                               class="@if(!$this->canEnableDatePicker) cursor-not-allowed @else cursor-pointer @endif
                               p-2 rounded-xl hover:bg-gray-500 hover:text-white transition duration-200
                               @if($this->schedule === $digit['fullDateTime']) bg-gray-500 text-white @endif">
                            <span>{{$digit['day']}}</span>
                            <input class="hidden" wire:click="getSlots" id="{{$digit['fullDateTime']}}" type="radio" wire:model="schedule" value="{{$digit['fullDateTime']}}"></input>
                        </label>
                    @endforeach
                </div>
                <hr>
                <div class="time max-h-60 overflow-y-auto my-2">
                    @forelse($availableTimeSlots as $s)
                        <label wire:key="key_slot_{{\Illuminate\Support\Str::slug($s['slot'])}}" for="slot_{{\Illuminate\Support\Str::of($s['slot'])}}"
                               class="block p-2 rounded-xl hover:bg-gray-500 hover:text-white transition duration-200
                               @if(!$s['isAvailable']) opacity-50 cursor-not-allowed @else cursor-pointer @endif"
                               @if($this->slot === $s['slot']) bg-gray-500 text-white @endif">
                            <span>{{$s['slot']}}</span>
                            <input @if(!$s['isAvailable']) disabled @endif class="hidden" id="slot_{{\Illuminate\Support\Str::of($s['slot'])}}" type="radio" wire:model="slot" value="{{$s['slot']}}"></input>
                        </label>
                        @empty
                        <div class="text-center mt-2">
                            No slot available
                        </div>
                    @endforelse
                </div>
                <hr>
                <div class="flex justify-center mt-2">
                    <button wire:click.prevent="bookApp" class="bg-purple-600 text-white px-4 py-2 rounded-xl  @if(!$slot) opacity-50 cursor-not-allowed @endif" @if(!$slot) disabled @endif>Book</button>
                </div>
            </div>
        </div>
</div>
