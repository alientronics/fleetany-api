<?php

namespace App\Http\Controllers;

use App\Company;
use App\Http\Controllers\Controller;
use App\Entities\User;
use App\Entities\Vehicle;
use App\Entities\Part;
use App\Entities\Gps;
use Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Lang;

class AlertController extends Controller
{
    public function checkAlerts($company_id, $tireSensor, $vehicle_id)
    {
        $company = Company::where('id', $company_id)
            ->first();
        
        $company->delta_pressure = $company->delta_pressure / 100;
            
        $ideal_pressure = (($tireSensor->temperature - 20) / 5.5556) * 0.02 *
                            $company->ideal_pressure + $company->ideal_pressure;
        
        if (((((1 - $company->delta_pressure) * $ideal_pressure) - 1.5) > $tireSensor->pressure) ||
            ($tireSensor->pressure > (((1 + $company->delta_pressure) * $ideal_pressure) + 1.5)) ||
            $tireSensor->temperature > $company->limit_temperature) { // 1,5 is the sensor accuracy
            
            $sendMail = false;
            if (empty($company->alert_date_time) || $company->alert_date_time == '0000-00-00 00:00:00') {
                $sendMail = true;
            } else {
                $diffHours = sprintf('%2d', (strtotime(date("Y-m-d H:i:s")) -
                                strtotime($company->alert_date_time)) / 3600);
                
                if ($diffHours >= 12) {
                    $sendMail = true;
                }
            }
            
            if ($sendMail && $this->sendAlertMail($company, $vehicle_id, $tireSensor, $ideal_pressure)) {
                $company->alert_date_time = date("Y-m-d H:i:s");
                $company->save();
            }
        }
    }
    
    private function sendAlertMail($company, $vehicle_id, $tireSensor, $ideal_pressure)
    {
        try {
            $users = User::select('users.*')
                    ->join('role_user', 'role_user.user_id', '=', 'users.id')
                    ->where('users.company_id', $company->id)
                    ->where('role_user.role_id', 1)
                    ->get();

            $vehicle = Vehicle::where('id', $vehicle_id)
                    ->first();

            $part = Part::where('id', $tireSensor->part_id)
                    ->first();
            
            $gps = Gps::select('gps.*', 'contacts.name as driver_name')
                    ->join('contacts', 'gps.driver_id', '=', 'contacts.id')
                    ->where('gps.vehicle_id', $vehicle_id)
                    ->orderBy('gps.created_at', 'desc')
                    ->first();
                    
            if (!empty($users)) {
                foreach ($users as $user) {
                    $alertType = $this->getAlertType($company, $tireSensor, $ideal_pressure);
                    $alarm = new \stdClass();
                    $alarm->vehicle_fleet = $vehicle->fleet;
                    $alarm->vehicle_plate = $vehicle->plate;
                    $alarm->vehicle_driver = $gps->driver_name;
                    $alarm->tire_number = $part->position;
                    $alarm->type = $alertType['type'];
                    $alarm->description = $alertType['description'];
                    $alarm->vehicle_latitude = $gps->latitude;
                    $alarm->vehicle_longitude = $gps->longitude;
                    $alarm->vehicle_id = $vehicle->id;

                    try {
                        Mail::send('mail-alert', ['alarm' => $alarm], function ($m) use ($user, $vehicle) {
                            $m->from(env('MAIL_SENDER'), 'fleetany sender');
                    
                            $m->to($user->email, $user->name)->subject(Lang::get('mails.AlertSubject'), [
                                'vehicle_number' => $vehicle->fleet,
                                'vehicle_plate' => $vehicle->number,
                            ]);
                        });
                    } catch (\Exception $e) {
                        Log::info($e->getMessage());
                    }
                }
            }
        } catch (\Exception $e) {
            Log::info($e->getMessage());
        }
 
        return true;
    }
    
    private function getAlertType($company, $tireSensor, $ideal_pressure)
    {
        $alertType = [];
        if ((((1 - $company->delta_pressure) * $ideal_pressure) - 1.5) > $tireSensor->pressure) {
            $alertType['type'] = Lang::get('mails.Pressure');
            $alertType['description'] = Lang::get('mails.LowPressure');
        } elseif ($tireSensor->temperature > $company->limit_temperature) {
            $alertType['type'] = Lang::get('mails.Temperature');
            $alertType['description'] = Lang::get('mails.HighTemperature');
        } else {
            $alertType['type'] = Lang::get('mails.Pressure');
            $alertType['description'] = Lang::get('mails.HighPressure');
        }
        return $alertType;
    }
}
