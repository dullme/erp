<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStrictNullComparison;
use Maatwebsite\Excel\Events\AfterSheet;

class PackagesExport implements FromCollection, WithEvents, WithHeadings, WithStrictNullComparison
{
    protected $data; //数据体
    protected $ladingNumber; //提单号


    public function __construct(array $data, $ladingNumber)
    {
        $this->data = $data;
        $this->ladingNumber = $ladingNumber;
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $event->sheet->getDelegate()->setMergeCells(['A1:J1']); //合并单元格
                $event->sheet->getDelegate()->getStyle('A1:J1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);   //设置水平居中
            },
        ];
    }

    public function headings(): array
    {
        return ["{$this->ladingNumber}发货清单"];
    }

    public function collection()
    {
        $title = [
            '货运公司',
            '提单号',
            '集装箱号',
            '铅封号',
            '到港时间',
            '预计入仓时间',
            '实际入仓时间',
            'SKU',
            '数量',
            '发货日',
        ];
        array_unshift($this->data, $title);

        return collect($this->data);
    }
}
