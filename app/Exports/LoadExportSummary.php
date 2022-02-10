<?php
namespace App\Exports;

use App\Models\Load;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Events\AfterSheet;
class LoadExportSummary implements FromCollection, WithHeadings
{
    use Exportable;
    private $loads_data;

    public function __construct($loads_data)
    {
       
        $this->loads_data = $loads_data;
    }
    

    public function collection()
    {
      
        return $this->loads_data;
    }

    public function headings(): array
    {
        return [
            __('tran.Total Income'),
            __('tran.Total Client Mileage'),
            __('tran.Client RPM'),
            __('tran.Total Deadhead'),
            __('tran.Total Mileage + DH'),
            __('tran.Client + DH RPM'),
            __('tran.Total Income'),
            __('tran.Total Expenses'),
            __('tran.Gross Profit'),

        ];
    }

    // public function registerEvents(): array
    // {
    //     return [

    //         AfterSheet::class=> function(AfterSheet $event) {
    //             $sheet =  $event->sheet;
    //             dd($sheet);
    //             // $event->sheet->getColumnDimension('D')->setAutoSize(false);
    //             // $event->sheet->getColumnDimension('D')->setWidth(200);
    //             $sheet = $spreadsheet->getActiveSheet();
    //             foreach ($sheet->getColumnIterator() as $column) {
    //                 $sheet->getColumnDimension($column->getColumnIndex())->setAutoSize(true);
    //             }
    //         }
    //     ];
    // }

    // public static function afterSheet(AfterSheet $event) 
    // {
    //     //
    // }
        
}
?>