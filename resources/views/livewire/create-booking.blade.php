<div>
        <div class="input mb-4 flex flex-col">
            <label class="font-semibold mb-2" for="">Select service</label>

            <select wire:model="service" class="border-0 rounded-2xl" name="" id="">
                <option value=""></option>
                @foreach($services as $s)
                    <option wire:key="service_{{$s->id}}" value="{{$s->id}}">{{$s->name}} ({{$s->duration}})</option>
                @endforeach
            </select>
        </div>
        <div class="input mb-4 flex flex-col">
            <label class="font-semibold mb-2" for="">Select employee</label>
            <select wire:model="employee" class="border-0 rounded-2xl" name="" id="">
                <option value=""></option>
                @foreach($employees as $e)
                    <option wire:key="employee_{{$e->id}}" value="{{$e->id}}">{{$e->name}}</option>
                @endforeach
            </select>
        </div>
        <div class="input mb-2">
            <label class="font-semibold" for="">Select appointment time</label>
            <div class="datepicker p-2 rounded-2xl bg-white mt-2">
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
                <div class="orderDay flex justify-between">
                    @foreach($seriesOfOrderDay['digit'] as $digit)
                        <label wire:key="{{$digit['fullDateTime']}}" for="{{$digit['fullDateTime']}}" class="cursor-pointer p-2 rounded-xl hover:bg-gray-500 hover:text-white transition duration-200">
                            <span>{{$digit['day']}}</span>
                            <input wire:click="getSlots" id="{{$digit['fullDateTime']}}" type="radio" wire:model="schedule" value="{{$digit['fullDateTime']}}" class="hidden"></input>
                        </label>
                    @endforeach
                </div>
                <hr>
                <div class="time max-h-60 overflow-y-auto">
                    @forelse($availableTimeSlots as $slot)
                        <div class="py-2">{{$slot}}</div>
                        @empty
                        <div class="text-center mt-2">
                            No slot available
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
</div>
