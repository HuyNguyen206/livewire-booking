<div>
    <form>
        @if($isCancel)
            <p>This appointment was canceled</p>
        @else
            <p>
                <span class="font-semibold">{{$name}}</span>, thanks for your booking
            </p>
            <p>
                <span class="font-semibold">{{$service->name}} ({{$service->duration." minutes"}})</span> with {{$employee->name}}
                on {{$date}}
            </p>
        @endif

        <button x-data="{
            confirmProcess: function (){
            if (  window.confirm('Are you sure to cancel?')){
                $wire.cancel()
            }

            }
        }" @click.prevent="if(!$wire.isCancel) {confirmProcess()} else { $wire.book() }" class="mt-4 w-full py-2 text-white bg-pink-600 rounded hover:bg-pink-400 transition">
            {{$isCancel ? 'Book another one' :  'Cancel booking'}}
        </button>
    </form>

</div>
