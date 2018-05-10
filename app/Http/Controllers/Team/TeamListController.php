<?php
/**
 * Created by PhpStorm.
 * User: Ngoc Quy
 * Date: 5/7/2018
 * Time: 10:51 AM
 */
namespace App\Http\Controllers\Team;
use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\Process;
use App\Models\Role;
use App\Models\Team;
use App\Service\ChartService;
use Illuminate\Http\Request;

class TeamListController extends Controller
{
    private $chartService;
    public function __construct(ChartService $chartService)
    {
        $this->chartService = $chartService;
    }

    public function index(){
        $teams = Team::all()->where('delete_flag', 0);
        $po_id = Role::all()->where('delete_flag', 0)->where('name', 'PO')->pluck('id')[0];

        $currentMonth = date('Y-m-01');
        $teamsValue = $this->chartService->getValueOfListTeam($currentMonth);
        $listMonth = $this->chartService->getListMonth();

        return view('teams.list', compact('teams', 'po_id', 'teamsValue', 'listMonth'));
    }

    public function destroy($id, Request $request)
    {
        if ($request->ajax()) {
            $team = Team::where('id', $id)->where('delete_flag', 0)->first();
            $team->delete_flag = 1;
            $team->save();
            $employees = Employee::where('team_id', $id)->get();
            foreach ($employees as $employee){
                $employee->team_id = null;
                $employee->save();
            }
            return response(['msg' => 'Product deleted', 'status' => 'success', 'id' => $id]);
        }
        return response(['msg' => 'Failed deleting the product', 'status' => 'failed']);
    }
    public function showChart(Request $request)
    {
        $month = date('Y-m-01', strtotime($request->month));
        $teamsValue = $this->chartService->getValueOfListTeam($month);
        return response(['listValueOfMonth' => $teamsValue]);
    }
}