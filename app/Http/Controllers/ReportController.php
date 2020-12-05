<?php

namespace App\Http\Controllers;

use App\Models\Report;
use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class ReportController extends Controller
{
    public function __construct()
    {
        $this->middleware('admin');
    }

    public function index($date = null)
    {
        if (!$date) {
            $date = Report::latest()->first()->created_at->format('Y-m-d');
        }

        $reports = Report::whereDate('created_at', $date)->get()->groupBy('courier_id');

        return view('admin.reports', ['reports' => $reports]);
    }

    public function filter(Request $request)
    {
        return $this->index($request['date']);
    }

    public function export($date = '2020-12-01')
    {
        $reports = Report::whereDate('created_at', $date)->get()->groupBy('courier_id');

        $n = 1;
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        //Row height
        $sheet->getDefaultRowDimension()->setRowHeight(25);

        //Borders style
        $borderStyleArray = array(
            'borders' => array(
                'outline' => array(
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    'color' => array('rgb' => '222222'),
                ),
                'horizontal' => array(
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    'color' => array('rgb' => '222222'),
                ),
                'vertical' => array(
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    'color' => array('rgb' => '222222'),
                ),
            ),
        );

        foreach ($reports as $key => $report) {

            //Merge courier name cells
            $sheet->mergeCells('A' . $n . ':J' . $n);
            $sheet->setCellValue(
                'A' . $n,
                $report[0]->user->first_name . ' - ' . count($report)
            );

            //Courier name size
            $sheet->getStyle('A' . $n)->getFont()->setSize(20);
            $sheet->getStyle('A' . $n)->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);

            $n = $n + 1;
            $count = $n;

            $arrayHeader = [
                [
                    '#',
                    'Интервал',
                    'Телефон',
                    'Имя',
                    'Адрес',
                    'Оплата',
                    'Тип',
                    'Заметка',
                    'Время Отчета',
                    'Время Whatsapp'
                ]
            ];

            //Alignment and size of header
            $sheet->fromArray($arrayHeader, NULL, 'A' . $n);
            $sheet->getStyle('A' . $n . ':J' . $n)->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
            $sheet->getStyle('A' . $n . ':J' . $n)->getFont()->setBold(true)->setSize(13);

            foreach ($report as $v => $value) {

                $count = $count + 1;

                //Alignment of # and A:J row
                $sheet->getStyle('A' . $count)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle('A' . $count . ':J' . $count)->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);

                $arrayData = [
                    [
                        $v + 1,
                        $value->order['time'],
                        $value->order['phone'],
                        $value->order['name'],
                        $value->order['yaddress'],
                        $value->payment,
                        $value->payment_method,
                        $value->order['addition'],
                        $value->reported_at,
                        $value->delivered_at
                    ]
                ];

                $sheet->fromArray($arrayData, NULL, 'A' . $count);
            }

            $sheet->getStyle('A' . $n . ':J' . $count)->applyFromArray($borderStyleArray);

            $n = $count + 2;
        }

        $sheet->getColumnDimension('B')->setAutoSize(true);
        $sheet->getColumnDimension('C')->setAutoSize(true);
        $sheet->getColumnDimension('D')->setAutoSize(true);
        $sheet->getColumnDimension('E')->setAutoSize(true);
        $sheet->getColumnDimension('F')->setAutoSize(true);
        $sheet->getColumnDimension('G')->setAutoSize(true);
        $sheet->getColumnDimension('H')->setAutoSize(true);
        $sheet->getColumnDimension('I')->setAutoSize(true);
        $sheet->getColumnDimension('J')->setAutoSize(true);

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="Отчет.xlsx"');
        header('Cache-Control: max-age=0');

        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
    }
}
