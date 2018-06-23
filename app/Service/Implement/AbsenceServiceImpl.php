<?php
/**
 * Created by PhpStorm.
 * User: Alex
 * Date: 4/18/2018
 * Time: 10:07 PM
 */

namespace App\Service\Implement;



use App\Service\AbsenceService;
use App\Models\Absence;
use App\Service\CommonService;
use Illuminate\Foundation\Console\EventMakeCommand;
use Illuminate\Support\Facades\DB;
use App\Models\Employee;
use Carbon\Carbon;
use App\Models\ExtraAbsenceDate;
use DateTime;
class AbsenceServiceImpl extends CommonService implements AbsenceService
{
    public function getArrayYearAbsence()
    {
        $min_year = Employee::selectRaw('min(year(startwork_date))')->first()['min(year(startwork_date))'];
//        $max_year = Absence::selectRaw('max(year(to_date))')->first()['max(year(to_date))'];
        if(empty($min_year)){
            $array_year=[
                date('Y')=>date('Y')
            ];
        }else{
            $array_year = [
                (string)$min_year=>(string)$min_year
            ];
            for($i=1;$i<=((int)date('Y')-$min_year);$i++){
                $array_year[(string)($min_year+$i)] = (string)($min_year+$i);
            }
        }
        return $array_year;
    }
    // them 1 record vao table ExtraAbsenceDate
    public function addRemainingDateOfYear($year,$employee_id){
            $employee = Employee::find($employee_id);
            if (!empty($employee) && !$this->isDuplicateExtraAbsenceDate($year,$employee_id)) {
                $absence = new \App\Absence\AbsenceService();
                $totalDateAbsences = $absence->absenceDateOnYear($employee_id, $year)
                    + $absence->numberAbsenceAddPerennial($employee_id, $year);
                $numberOfDaysOff = $absence->numberOfDaysOff(
                    $employee_id, $year, 0,
                    getAbsenceType(config('settings.status_common.absence_type.salary_date')),
                    getAbsenceStatuses(config('settings.status_common.absence.accepted')));
                $days = max(0,($totalDateAbsences - $numberOfDaysOff));
                $extraAbsenceDate_data = [
                    'year' => $year,
                    'date' => $days,
                    'employee_id' => $employee_id,
                    'delete_flag' => 0
                ];
                $extraAbsenceDates = new ExtraAbsenceDate($extraAbsenceDate_data);
                return $extraAbsenceDates->save();
            }
            return false;
    }

    function isDuplicateExtraAbsenceDate($year,$employee_id){
        $employee = Employee::find($employee_id);
        $year_extraabsence= $employee->extraAbsenceDates()->where('year',$year)->first();
        if(!empty($year_extraabsence))
            return true;
        return false;
    }

    // them moi cac record vao table ExtraAbsenceDate dua theo hop dong employee
    public function addRemainingDateOfYearByEmployeePolicy($employee_id)
    {
        $employee = Employee::find($employee_id);
        if (!empty($employee)) {
            $start_work = Carbon::parse($employee->startwork_date);
            $end_work=Carbon::parse($employee->endwork_date);
            for($i=$start_work->year;$i<=($end_work->year);$i++){
                $rs = $this->addRemainingDateOfYear($i,$employee_id);
                if(!$rs){
                    return false;
                }
            }
            return true;
        }
        return false;
    }

    public function getnumberAbsenceRedundancyByYear($employee_id,$year){
        $employee = Employee::find($employee_id);
        $days=$employee->extraAbsenceDates()->where('year', '=', (int)$year)->first();
        if(empty($days)){
            return 0;
        }else{
            return $days->date;
        }

    }

    function absenceDateOnYear($id, $year){
        $sumDate = 0;
        $year = (int)$year;
        $input = Carbon::create($year);
        $objEmployee = Employee::find($id);
        if(!empty($objEmployee)){
            $start_date_policy =  date_create($objEmployee->startwork_date);
            $startDate = (int)$start_date_policy->format('d');
            $startMonth = (int)$start_date_policy->format('m');
            $startYear = (int)$start_date_policy->format('Y');

            $end_date_policy = date_create($objEmployee->endwork_date);
            $endDate = (int)(int)$end_date_policy->format('d');
            $endMonth = (int)$end_date_policy->format('m');
            $endYear = (int)$end_date_policy->format('Y');

            if($startYear!=$year && $endYear<=$year){
                if($startMonth == $endMonth){
                    if($endDate - $startDate > 15){
                        $sumDate ++;
                    }
                    return $sumDate;
                }else{
                    $sumDate++;
                    if($endDate >= 15){
                        $sumDate++;
                    }
                    $sumDate += ($endMonth - 1) - ($input->startOfYear()->month+1)+1;
                    return $sumDate;
                }
            }elseif($startYear==$year &&  $endYear<=$year){
                if($startMonth == $endMonth){
                    if($endDate - $startDate > 15){
                        $sumDate ++;
                    }
                    return $sumDate;
                }else{
                    if($startDate <= 15){
                        $sumDate++;
                    }
                    if($endDate >= 15){
                        $sumDate++;
                    }
                    $sumDate += ($endMonth - 1) - ($startMonth+1)+1;
                    return $sumDate;
                }
            }elseif($startYear==$year &&  $endYear>$year){
                if($startDate <= 15){
                    $sumDate++;
                }
                $sumDate += 12 - ($startMonth+1)+1;
                return $sumDate;
            }else{
                return 12;
            }
        }
        return -1;
    }

    function totalDateAbsences($id, $year,$month){
        $objAS = new \App\Absence\AbsenceService();
        if((int)$month<7){
            return $this->absenceDateOnYear($id, $year) + $objAS->numberAbsenceRedundancyOfYearOld($id, $year - 1)
                + $objAS->numberAbsenceAddPerennial($id, $year);
        }else{
            return $this->absenceDateOnYear($id, $year)
                + $objAS->numberAbsenceAddPerennial($id, $year);
        }

    }

    function numberOfDaysOff($listAbsence, $year, $month, $absence_type){
        $AbService = new \App\Absence\AbsenceService();
        return $AbService->getNumberOfDaysOff($listAbsence, $year, $month, $absence_type);
    }

    function getListNumberOfDaysOff($id, $year, $month, $absence_status){
        if($month == 0){
            $listAbsence = Absence::select()
                ->join('absence_types', 'absences.absence_type_id', '=', 'absence_types.id')
                ->join('absence_statuses', 'absences.absence_status_id', '=', 'absence_statuses.id')
                ->where('absences.delete_flag', 0)
                ->where('absences.employee_id',$id)
                ->where('absences.absence_status_id', $absence_status)
                ->where(function ($query) use ($year) {
                    $query->orwhere(function ($query) use ($year) {
                        $query->whereYear('absences.from_date', $year)
                            ->orWhereYear('absences.to_date', $year);
                    });
                    $query->orwhere(function ($query) use ($year) {
                        $query->whereYear('absences.from_date', '<', $year)
                            ->WhereYear('absences.to_date', '>', $year);
                    });
                })
                ->get();
        }else{
            $input =Carbon::create($year,$month)->format('Y-m');
            $listAbsence = Absence::select()
                ->join('absence_types', 'absences.absence_type_id', '=', 'absence_types.id')
                ->join('absence_statuses', 'absences.absence_status_id', '=', 'absence_statuses.id')
                ->where('absences.delete_flag', 0)
                ->where('absences.employee_id',$id)
                ->where('absence_statuses.id', $absence_status)
                ->where(function ($query) use($input) {
                    $query->orwhere(function ($query) use($input) {
                        $query->whereRaw("(date_format(absences.from_date,'%Y-%m') = '".$input."')")
                            -> whereRaw("(date_format(absences.to_date,'%Y-%m') != '".$input."')");
                    });
                    $query->orwhere(function ($query) use($input) {
                        $query->whereRaw("(date_format(absences.from_date,'%Y-%m') != '".$input."')")
                            ->whereRaw("(date_format(absences.to_date,'%Y-%m') = '".$input."')");
                    });
                    $query->orwhere(function ($query) use($input) {
                        $query->whereRaw("(date_format(absences.from_date,'%Y-%m') = '".$input."')")
                            ->whereRaw("(date_format(absences.to_date,'%Y-%m') = '".$input."')");
                    });
                    $query->orwhere(function ($query) use($input) {
                        $query->whereRaw("(date_format(absences.from_date,'%Y-%m') < '".$input."')")
                            ->whereRaw("(date_format(absences.to_date,'%Y-%m') > '".$input."')");
                    });
                })
                ->get();
        }

        return $listAbsence;
    }
}