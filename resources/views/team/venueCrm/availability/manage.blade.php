@extends('team.layouts.app')
@section('title', "Availability | Venue CRM")
@section('main')
@php
    use App\Models\Availability;
    $auth_user = Auth::guard('team')->user();
    $food_type = ["Lunch", 'Dinner'];
@endphp
<div class="content-wrapper pb-5">
    <section class="content-header">
        <div class="container-fluid mb-2">
            <h1 class="m-0 mb-2">Availability</h1>
        </div>
        <div class="row">
            <div class="col-lg-3 col-6">
                <div class="small-box text-sm text-light" style="background: var(--wb-renosand);">
                    <div class="inner">
                        <h3>{{Availability::where('created_by', $auth_user->id)->whereBetween('date', [$calendar[0]->startOfMonth(), $calendar[12]->endOfMonth()])->count();}}</h3>
                        <p>Total Events</p>
                    </div>
                    <div class="icon">
                        <i class="ion ion-bag"></i>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-6">
                <div class="small-box text-sm text-light" style="background: var(--wb-dark-red);">
                    <div class="inner">
                        <h3>{{Availability::where('created_by', $auth_user->id)->whereBetween('date', [$calendar[0]->startOfMonth(), $calendar[12]->endOfMonth()])->sum("pax")}}</h3>
                        <p>Total PAX</p>
                    </div>
                    <div class="icon">
                        <i class="ion ion-bag"></i>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <section class="content">
        <div class="container-fluid">
            @foreach ($calendar as $list)
                @php
                    $total_events = 0;
                    $total_pax = 0;
                @endphp
                <div class="table-responsive mb-3">
                    <table class="table table-bordered table-hover">
                        <thead>
                            <tr>
                                <th>Month
                                    <a href="{{route('team.availability.reset_calendar', $list)}}" onclick="return confirm('Are you sure want to reset?')" class="btn p-0 text-danger float-right" title="Reset {{date('F-Y', strtotime($list))}} Calendar"><i class="fa fa-trash-arrow-up"></i></a>
                                </th>
                                <th colspan="{{$list->endOfMonth()->day}}" class="text-center">{{date('M-Y', strtotime($list))}}</th>
                                <th class="text-center" rowspan="2">Total Event</th>
                                <th class="text-center" rowspan="2">Total PAX</th>
                            </tr>
                            <tr>
                                <th>Date</th>
                                @for ($i = 1; $i <= $list->endOfMonth()->day; $i++)
                                    <th>{{$i}}</th>
                                @endfor
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($auth_user->get_party_areas as $party_area)
                                @foreach ($food_type as $food)
                                    <tr>
                                        <td class="text-nowrap">{{ $party_area->name." | ".$food }}</td>
                                        @for ($i = 1; $i <= $list->endOfMonth()->day; $i++)
                                            <td>
                                                @php
                                                $model = Availability::where(['created_by' => $auth_user->id, 'party_area_id' => $party_area->id, 'food_type' => $food, 'date' => date('Y-m-d', strtotime($list->year."-".$list->month."-".$i))])->first();
                                                @endphp
                                                <input value="{{date('Y-m-d', strtotime($list->year."-".$list->month."-".$i))}},{{$party_area->id}},{{$food}}" type="checkbox" class="form-input" onclick="handle_check(this)" {{$model ? 'checked': '' }}>
                                                <span>{{$model->pax ?? 0}}</span>
                                            </td>
                                        @endfor
                                        <td style="color: var(--wb-dark-red);" class="sub_total_events">
                                            @php
                                              
                                              $val = Availability::where(['created_by' => $auth_user->id, 'party_area_id' => $party_area->id, 'food_type' => $food])->where('date', "like", "%".date('Y-m', strtotime($list))."%")->count();
                                              $total_events+=$val;
                                              echo $val;
                                            @endphp
                                        </td>
                                        <td style="color: var(--wb-dark-red);" class="sub_total_pax">
                                            @php
                                                $val = Availability::where(['created_by' => $auth_user->id, 'party_area_id' => $party_area->id, 'food_type' => $food])->where('date', 'like', "%".date('Y-m', strtotime($list))."%")->sum('pax');
                                                $total_pax+=$val;
                                              echo $val;
                                            @endphp
                                        </td>
                                    </tr>
                                @endforeach
                            @endforeach
                            <tr>
                                <td colspan="{{$list->endOfMonth()->day+1}}" class="text-right text-bold">Total</td>
                                <td class="text-bold total_events" style="color: var(--wb-renosand)">{{$total_events}}</td>
                                <td class="text-bold total_pax" style="color: var(--wb-renosand)">{{$total_pax}}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            @endforeach
        </div>
    </section>
</div>
@endsection
@section('footer-script')
<script>
    function handle_check(elem){
        const data = elem.value.split(",");
        const formBody = {
           date: data[0],
           party_area_id: data[1],
           food_type: data[2],
        };
        
        let pax = 0;
        if(elem.checked){
            pax = prompt("Enter the PAX");
            formBody.pax = pax;
            formBody.checked = true;
        }else{
            if(!confirm('Are you sure want to uncheck?')){
                elem.checked = true;
                return false;
            }
            formBody.checked = false;
        }

        fetch(`{{route('team.availability.manage_process')}}`, {
            method: "post",
            headers: {
                'X-CSRF-TOKEN': "{{csrf_token()}}",
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(formBody)
        }).then(response => response.json()).then(data => {
            if(data.success === true){
                if(data.checked == true){
                    elem.checked = true;
                }
                handle_calculation(elem, Number(pax))
            }else{
                elem.checked = false;
            }
            toastr[data.alert_type](data.message);
        })
    }

    function handle_calculation(elem, pax_val){
        const current_row = elem.parentElement.parentElement;
        const sub_total_events = current_row.querySelector('.sub_total_events');
        const sub_total_pax = current_row.querySelector('.sub_total_pax');
        const total_events = current_row.parentElement.querySelector('.total_events');
        const total_pax = current_row.parentElement.querySelector('.total_pax');
        
        if(elem.checked){
            sub_total_events.innerText = Number(sub_total_events.innerText)+1;
            sub_total_pax.innerText = Number(sub_total_pax.innerText)+pax_val;
            total_events.innerText = Number(total_events.innerText)+1;
            total_pax.innerText = Number(total_pax.innerText)+pax_val;
            elem.nextElementSibling.innerText = pax_val;
        }else{
            sub_total_events.innerText = Number(sub_total_events.innerText)-1;
            sub_total_pax.innerText = Number(sub_total_pax.innerText)-Number(elem.nextElementSibling.innerText);
            total_events.innerText = Number(total_events.innerText)-1;
            total_pax.innerText = Number(total_pax.innerText)-Number(elem.nextElementSibling.innerText);
            elem.nextElementSibling.innerText = 0;
        }
    }
</script>
@endsection