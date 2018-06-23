<?php

namespace App\Export;
use App\Models\Role;
use App\Service\SearchEmployeeService;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Concerns\FromCollection;
use App\Models\Employee;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\RegistersEventListeners;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Events\BeforeExport;
use Maatwebsite\Excel\Events\BeforeSheet;
use Maatwebsite\Excel\Events\BeforeWriting;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use PhpOffice\PhpSpreadsheet\Worksheet\PageSetup;
use Maatwebsite\Excel\Concerns\WithStrictNullComparison;

/**
 * Created by PhpStorm.
 * User: Ngoc Quy
 * Date: 4/23/2018
 * Time: 4:47 PM
 */

class HRAbsenceExport implements FromCollection, WithEvents, WithHeadings,WithStrictNullComparison
{
    use Exportable, RegistersEventListeners;
    private $searchEmployeeService;
    protected $returnCollectionEmployee;
    /**
     * @var Request
     */
    private $data;


    /**
     * @var Request
     */

    public function __construct( $data)
    {
        $this->data = $data;

    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        return collect($this->data);
    }

    public static function beforeExport(BeforeExport $event)
    {
        //
    }

    public static function beforeWriting(BeforeWriting $event)
    {
        //
    }

    public static function beforeSheet(BeforeSheet $event)
    {
        //
    }

    public static function afterSheet(AfterSheet $event)
    {
        //
    }

    /**
     * @return array
     */
    public function headings(): array
    {
        return [
            trans('common.name.employee_name'),
            trans('employee.profile_info.email'),
            trans('absence.total_date_absences'),
            trans('absence.last_year_absences_date'),
            trans('absence.absented_date'),
            trans('absence.non_salary_date'),
            trans('absence.insurance_date'),
            trans('absence.subtract_salary_date'),
            trans('absence.remaining_date')
        ];
    }

}