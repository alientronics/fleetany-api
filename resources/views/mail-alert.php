{{trans("mails.FleetNumber")}}: {{$alarm->vehicle_fleet}}<br/>
{{trans("mails.VehiclePlate")}}: {{$alarm->vehicle_plate}}<br/>
{{trans("mails.Driver")}}: {{$alarm->vehicle_driver}}<br/>
{{trans("mails.TireNumber")}}: {{$alarm->tire_number}}<br/>
{{trans("mails.AlarmType")}}: {{$alarm->type}}<br/>
{{trans("mails.AlarmDescription")}}: {{$alarm->description}}<br/>
{{trans("mails.VehicleLocation")}}: <a href="http://maps.google.com/?q={{$alarm->vehicle_latitude}},{{$alarm->vehicle_longitude}}">Latitude: {{$alarm->vehicle_latitude}}, Longitude: {{$alarm->vehicle_longitude}}</a><br/>
{{url('/')}}/vehicle/{{$alarm->vehicle_id}}
