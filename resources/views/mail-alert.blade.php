{{\Illuminate\Support\Facades\Lang::get("mails.FleetNumber")}}: {{$alarm->vehicle_fleet}}<br/>
{{\Illuminate\Support\Facades\Lang::get("mails.VehiclePlate")}}: {{$alarm->vehicle_plate}}<br/>
{{\Illuminate\Support\Facades\Lang::get("mails.Driver")}}: {{$alarm->vehicle_driver}}<br/>
{{\Illuminate\Support\Facades\Lang::get("mails.TireNumber")}}: {{$alarm->tire_number}}<br/>
{{\Illuminate\Support\Facades\Lang::get("mails.AlarmType")}}: {{$alarm->type}}<br/>
{{\Illuminate\Support\Facades\Lang::get("mails.AlarmDescription")}}: {{$alarm->description}}<br/>
{{\Illuminate\Support\Facades\Lang::get("mails.VehicleLocation")}}: <a href="http://maps.google.com/?q={{$alarm->vehicle_latitude}},{{$alarm->vehicle_longitude}}">Latitude: {{$alarm->vehicle_latitude}}, Longitude: {{$alarm->vehicle_longitude}}</a><br/>
{{url('/')}}/vehicle/{{$alarm->vehicle_id}}
