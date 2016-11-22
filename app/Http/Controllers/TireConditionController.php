<?php

namespace App\Http\Controllers;

use App\Company;
use App\Http\Controllers\Controller;
use App\Entities\Type;
use App\Entities\TireSensor;
use Lang;
use App\Entities\User;
use App\Entities\Entry;
use App\Entities\Part;
use App\Entities\PartEntry;

class TireConditionController extends Controller
{
    public function checkTireCondition($company_id, $tiresensor_id, $vehicle_id)
    {
        $tireSensor = TireSensor::find($tiresensor_id);
        $company = Company::where('id', $company_id)->first();
    
        $company->delta_pressure = $company->delta_pressure / 100;
    
        $ideal_pressure = $this->calculateIdealPressure($tireSensor, $company);
    
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
            
            $users = User::select('users.*')
                ->join('role_user', 'role_user.user_id', '=', 'users.id')
                ->where('users.company_id', $company->id)
                ->where('role_user.role_id', 1)
                ->get();
    
            $objAlert = new AlertController();
            if ($sendMail && $objAlert->sendAlertTireMail(
                $company,
                $vehicle_id,
                $tireSensor,
                $ideal_pressure,
                $users
            )) {
                $company->alert_date_time = date("Y-m-d H:i:s");
                $company->save();
            }
        }
        $this->generateEntry($company, $tireSensor, $ideal_pressure);
    }
    
    public function getAlertType($company, $tireSensor, $ideal_pressure)
    {
        $alertType = [];
        if ((((1 - $company->delta_pressure) * $ideal_pressure) - 1.5) > $tireSensor->pressure) {
            $alertType['type'] = Lang::get('mails.Pressure');
            $alertType['description'] = Lang::get('mails.LowPressure');
            $alertType['id'] = 'LowPressure';
        } elseif ($tireSensor->pressure > (((1 + $company->delta_pressure) * $ideal_pressure) + 1.5)) {
            $alertType['type'] = Lang::get('mails.Pressure');
            $alertType['description'] = Lang::get('mails.HighPressure');
            $alertType['id'] = 'HighPressure';
        } elseif ($tireSensor->temperature > $company->limit_temperature) {
            $alertType['type'] = Lang::get('mails.Temperature');
            $alertType['description'] = Lang::get('mails.HighTemperature');
            $alertType['id'] = 'HighTemperature';
        }
    
        return $alertType;
    }
    
    private function calculateIdealPressure($tireSensor, $company)
    {
        return (($tireSensor->temperature - 20) / 5.5556) * 0.02 *
        $company->ideal_pressure + $company->ideal_pressure;
    }
    
    private function hasPressureIssue($company, $tireSensor, $ideal_pressure)
    {
        $alertType = $this->getAlertType($company, $tireSensor, $ideal_pressure);
        if (empty($alertType->id) ||
            ($alertType->id != 'HighPressure' && $alertType->id != 'LowPressure')) {
            return true;
        }
        return false;
    }

    private function generateEntry($company, $tireSensor, $ideal_pressure)
    {
        if (!$this->hasPressureIssue($company, $tireSensor, $ideal_pressure)) {
            $tireSensor = TireSensor::where('part_id', $tireSensor->part_id)
                ->where('created_at', '<', $tireSensor->created_at)
                ->orderBy('created_at', 'desc')
                ->first();
            
            if (!empty($tireSensor->id)) {
                if ($this->hasPressureIssue($company, $tireSensor, $ideal_pressure)) {
                    $entry_type = Type::select('id')->where('company_id', $company->id)
                    ->where(function ($query) {
                        $query->where('name', 'calibration maintenance')
                        ->orWhere('name', 'manuten&ccedil;&atilde;o de calibragem');
                    })
                    ->first();
            
                    if (!empty($entry_type)) {
                        $entry = Entry::forceCreate([
                            "company_id" => $company->id,
                            "entry_type_id" => $entry_type->id,
                            "datetime_ini" => date("Y-m-d H:i:s"),
                            "cost" => 1,
                        ]);
            
                        $part = Part::find($tireSensor->part_id);
            
                        if (!empty($part->part_id)) {
                            PartEntry::forceCreate([
                                "part_id" => $part->part_id,
                                "entry_id" => $entry->id,
                            ]);
                        }
                    }
                }
            }
        }
    }
}
