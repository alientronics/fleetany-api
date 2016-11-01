{{Lang::get("mails.FleetNumber")}}: {{$alarm->vehicle_fleet}}<br/>
{{Lang::get("mails.VehiclePlate")}}: {{$alarm->vehicle_plate}}<br/>
{{Lang::get("mails.Driver")}}: {{$alarm->vehicle_driver}}<br/>
{{Lang::get("mails.TireNumber")}}: {{$alarm->tire_number}}<br/>
{{Lang::get("mails.AlarmType")}}: {{$alarm->type}}<br/>
{{Lang::get("mails.AlarmDescription")}}: {{$alarm->description}}<br/>
{{Lang::get("mails.VehicleLocation")}}: <a href="http://maps.google.com/?q={{$alarm->vehicle_latitude}},{{$alarm->vehicle_longitude}}">Latitude: {{$alarm->vehicle_latitude}}, Longitude: {{$alarm->vehicle_longitude}}</a><br/>
{{url('/')}}/vehicle/{{$alarm->vehicle_id}}
