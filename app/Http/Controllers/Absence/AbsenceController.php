<?php

namespace App\Http\Controllers\Absence;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use Carbon\Carbon;
use App\Models\Confirm;
use App\Absence\AbsenceService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AbsenceController extends Controller
{
    public function __construct(){

    }
    public function index(Request $request){
    	$abc = new AbsenceService();
    	/*dd($abc->soNgayNghiPhep(1,2017,0));
    	dd($abc->soNgayDuocNghiPhep(1,2017));*/
        return view('vangnghi.list');
    }

    public function create(Request $request)
    {

    }

    public function store(Request $request){

    }
    public function show($id, Request $request){

    }
    public function edit($id){
        $employee = Employee::where('delete_flag', 0)->where('is_employee', 1)->find($id);
        if ($employee == null) {
            return abort(404);
        }

        $curDate= date_create(Carbon::now()->format('Y-m-d'));
        $dayBefore = ($curDate)->modify('-15 day')->format('Y-m-d');
        $dayAfter = ($curDate)->modify('+15 day')->format('Y-m-d');

        $objEmployee = Employee::select('employees.*', 'teams.name as team_name')
            ->join('teams', 'employees.team_id', '=', 'teams.id')
            ->where('employees.delete_flag', 0)->findOrFail($id)->toArray();

        $objPO = Employee::SELECT('employees.name as PO_name', 'projects.name as project_name')
            ->JOIN('processes', 'processes.employee_id', '=', 'employees.id')
            ->JOIN('projects', 'processes.project_id', '=', 'projects.id')
            ->JOIN('roles', 'processes.role_id', '=', 'roles.id')
            ->whereIn('processes.project_id', function ($query) use ($id,$dayBefore)  {
                $query->select('project_id')
                    ->from('processes')
                    ->where('employee_id', '=', $id)
                    ->whereDate('processes.start_date', '>', $dayBefore);
            })
            ->WHERE('employees.delete_flag', '=', 0)
            ->WHERE('roles.name', 'like', 'po')
            ->get()->toArray();

        return view('absences.formVangNghi', ['objPO' => $objPO, 'objEmployee' => $objEmployee]);
    }
    public function update(Request $request, $id){

    }
    public function destroy($id, Request $request){

    }

    // function create by Quy.
    public function showListAbsence(){
        $getIdUserLogged = Auth::id();
        $getAllAbsenceInConfirm = Confirm::where('employee_id',$getIdUserLogged)->get();
        /*$getTeamOfUserLogged = Employee::find($getIdUserLogged);
        $getAllAbsenceType = AbsenceType::all();
        $getAllAbsenceStatus = AbsenceStatus::all();
        $getAllEmployeeByTeamUserLogged = Employee::where('team_id',$getTeamOfUserLogged->team_id)
        ->where('delete_flag',0)->where('is_employee',1);
        $allAbsenceByUserLogged = array();
        $allEmployeeByUserLogged = array();
        $allAbsenceNotNull = array();
        $allEmployeeNotNull = array();
        foreach ($getAllEmployeeByTeamUserLogged->get() as $addEmployee){
            array_push($allAbsenceByUserLogged, $addEmployee->absences);
            array_push($allEmployeeByUserLogged,$addEmployee);
        }
        foreach ($allAbsenceByUserLogged as $allEmployee){
            foreach ( $allEmployee as $element){
                if (!is_null($element)){
                    array_push($allAbsenceNotNull,$element);
                }
            }
        }
        foreach ($allEmployeeByUserLogged as $allEmployee){
            foreach ($allEmployee->absences as $element){
                if (!is_null($element) ){
                    array_push($allEmployeeNotNull,$allEmployee);
                }
            }
        }*/

//        return view('absences.poteam', compact('allEmployeeNotNull','allAbsenceNotNull','getIdUserLogged','getAllAbsenceType','getAllAbsenceStatus'));
        return view('absences.poteam', compact('getAllAbsenceInConfirm'));
    }
    public function denyPOTeam(Request $request){

        return redirect()->route('absence-po');
    }
}
