<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Entities\Type;
use App\Entities\TireSensor;
use App\Entities\Part;
use Illuminate\Support\Facades\Lang;
use App\Entities\Vehicle;
use App\Entities\Gps;
use GuzzleHttp\Client;

class AlertController extends Controller
{
    public function sendAlertTireMail($company, $vehicle_id, $tireSensor, $ideal_pressure, $users)
    {
        try {
            $vehicle = Vehicle::where('id', $vehicle_id)->first();
    
            $part = Part::where('id', $tireSensor->part_id)->first();
    
            $gps = Gps::select('gps.*', 'contacts.name as driver_name')
                ->join('contacts', 'gps.driver_id', '=', 'contacts.id')
                ->where('gps.vehicle_id', $vehicle_id)
                ->orderBy('gps.created_at', 'desc')
                ->first();
    
            if (!empty($users)) {
                $emails = [];
                $names = [];
                foreach ($users as $user) {
                    $emails[] = $user->email;
                    $names[] = $user->name;
                }
                
                $objTireCondition = new TireConditionController();
                $alertType = $objTireCondition->getAlertType($company, $tireSensor, $ideal_pressure);
                $alarm = new \stdClass();
                $alarm->vehicle_fleet = $vehicle->fleet;
                $alarm->vehicle_plate = $vehicle->plate;
                $alarm->vehicle_driver = $gps->driver_name;
                $alarm->tire_number = $part->position;
                $alarm->type = $alertType['type'];
                $alarm->description = $alertType['description'];
                $alarm->id = $alertType['id'];
                $alarm->vehicle_latitude = $gps->latitude;
                $alarm->vehicle_longitude = $gps->longitude;
                $alarm->vehicle_id = $vehicle->id;
                
                try {
                    $client = new Client();
                    $client->request('POST', env('ALERTS_API_URL').'/api/v1/alert'.
                        '?api_token=' . env('ALERTS_API_KEY'), [
                            'form_params' => ["emails" => json_encode($emails),
                                "names" => json_encode($names),
                                "subject" => Lang::get('mails.AlertSubject'),
                                "subject_params" => json_encode([
                                    'vehicle_number' => $vehicle->fleet,
                                    'vehicle_plate' => $vehicle->number,
                                ]),
                                "message" => json_encode($alarm),
                                "entity_key" => 'tire',
                                "entity_id" => $part->part_id,
                            ]
                        ]);
                } catch (\Exception $e) {
                    Log::info('TireSensor Alert Fail: '.$e->getMessage());
                }
            }
        } catch (\Exception $e) {
            Log::info($e->getMessage());
        }
    
        return true;
    }
}
