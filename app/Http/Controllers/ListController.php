<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\Week;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class ListController extends Controller
{
    public function __construct()
    {
        $this->middleware('admin');
    }

    public function index()
    {
        $nb_has_courier = Order::where('active', true)->whereNotNull('courier_id')->count();
        $nb_active      = Order::where('active', true)->count();
        $orders         = Order::where('active', true)
                            ->whereNotNull('courier_id')
                            ->orderBy('position')
                            ->get()
                            ->groupBy(['courier_id']);

        return view('admin.list', [
                'orders' => $orders,
                'a'      => $nb_has_courier,
                'b'      => $nb_active
            ]);
    }

    public function updateList(Request $request)
    {
        if (!$request->has('ids')) {
            return;
        }

        $is_weekend = Week::find(1)->is_weekend;
        $num        = 0;
        $my_id      = $request['my_id'];
        $parent_id  = (int) $request['parent_id'];
        $ids        = $request['ids'];

        Order::where('active', true)
            ->whereNotNull('courier_id')
            ->update(['position' => null]);

        foreach ($ids as $key => $id) {
            $num            = $num + 1;
            $order          = Order::find($id);
            $order->position = $num;

            if($id === $my_id){
                $order->courier_id = $parent_id;
                $is_weekend ? $order->courier2_id = $parent_id : $order->courier1_id = $parent_id;
            }

            $order->save();
        }
    }

    public function export()
    {
        $couriers = Order::where('active',true)
                    ->whereNotNull('courier_id')
                    ->orderBy('position')
                    ->get()
                    ->groupBy(['courier_id']);
        $n = 1;
        $color = 'ffffff';
        $xs = $s = $m = $l = $xl = 0;

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        //Row height
        $sheet->getDefaultRowDimension()->setRowHeight(30);

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

        foreach ($couriers as $key => $courier) {

            foreach ($courier as $c) {
                if($c->tag === 'Lite') {

                    switch ($c->size) {
                        case 'XS':
                            ++$xs;
                            break;
                        case 'S':
                            ++$s;
                            break;
                        case 'M':
                            ++$m;
                            break;
                        case 'L':
                            ++$l;
                            break;
                        case 'XL':
                            ++$xl;
                            break;
                    }
                }
            }

            $xs = $xs > 0 ? ' [XS - ' . $xs . '] ' : '';
            $s  = $s  > 0 ? ' [S - ' . $s . '] ' : '';
            $m  = $m  > 0 ? ' [M - ' . $m . '] ' : '';
            $l  = $l  > 0 ? ' [L - ' . $l . '] ' : '';
            $xl = $xl > 0 ? ' [XL - ' . $xl . '] ' : '';

            //Merge courier name cells
            $sheet->mergeCells('A' . $n . ':F' . $n);
            $sheet->setCellValue(
                'A' . $n,
                $courier[0]->user->first_name . ' - ' . count($courier) .
                ' Lite '. $xs . $s . $m . $l . $xl
            );

            $xs = $s = $m = $l = $xl = 0;

            //Courier name size
            $sheet->getStyle('A' . $n)->getFont()->setSize(20);
            $sheet->getStyle('A' . $n)->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);

            $n = $n + 1;
            $count = $n;

            $arrayHeader = [
                ['#', 'Имя', 'Тег', 'Время', 'Телефон', 'Адрес']
            ];

            //Alignment and size of header
            $sheet->fromArray($arrayHeader, NULL, 'A' . $n);
            $sheet->getStyle('A' . $n . ':F' . $n)->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
            $sheet->getStyle('A' . $n . ':F' . $n)->getFont()->setBold(true)->setSize(13);

            foreach ($courier as $v => $value) {
                $count = $count + 1;

                switch ($value->tag) {
                    case 'Select':
                        $color = 'a5d6a7';
                        break;
                    case 'Lite':
                        $color = 'fff59d';
                        break;
                    case 'Daily':
                        $color = 'ef9a9a';
                        break;
                }

                //Alignment of # and A:F row
                $sheet->getStyle('A' . $count)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle('A' . $count . ':F' . $count)->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);

                //Merge # rows
                $sheet->mergeCells('A' . $count . ':A' . ($count + 1));

                $arrayData = [
                    [$v + 1, $value->name, $value->getTag(), $value->time, $value->phone, $value->yaddress]
                ];

                $sheet->fromArray($arrayData, NULL, 'A' . $count);

                //Name and tag bold
                $sheet->getStyle('B' . $count . ':C' . $count)->getFont()->setBold(true);
                //Name and tag color
                $sheet->getStyle('B' . $count . ':C' . $count)
                      ->getFill()
                      ->setFillType(Fill::FILL_SOLID)
                      ->getStartColor()
                      ->setRGB($color);

                //Addition style
                $sheet->mergeCells('B' . ($count + 1) . ':F' . ($count + 1));
                $sheet->setCellValue('B' . ($count + 1), $value->addition);
                $sheet->getStyle('B' . ($count + 1))->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
                $sheet->getStyle('B' . ($count + 1))->getAlignment()->setWrapText(true);
                $sheet->getRowDimension('B')->setRowHeight(-1);

                $count = $count + 1;
            }

            $sheet->getStyle('A' . $n . ':F' . $count)->applyFromArray($borderStyleArray);

            $n = $count + 2;
        }

        $sheet->getColumnDimension('B')->setAutoSize(true);
        $sheet->getColumnDimension('C')->setAutoSize(true);
        $sheet->getColumnDimension('D')->setAutoSize(true);
        $sheet->getColumnDimension('E')->setAutoSize(true);
        $sheet->getColumnDimension('F')->setAutoSize(true);

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="Список.xlsx"');
        header('Cache-Control: max-age=0');

        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
    }
}
