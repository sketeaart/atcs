<?php

namespace App\Http\Controllers;

use App\Models\Building;
use App\Models\Room;
use App\Models\Cctv;
use App\Models\Contact;
use App\Models\SystemNotification;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Facades\Excel;

class ExportController extends Controller
{
    public function export(Request $request, string $entity)
    {
        $entity = strtolower($entity);
        $map = [
            'buildings' => [Building::query(), ['ID','Name','Lat','Lng','Icon','Created At']],
            'rooms' => [Room::with('building')->select('id','building_id','name','icon','created_at'), ['ID','Building ID','Name','Icon','Created At']],
            'cctvs' => [Cctv::select('id','building_id','room_id','ip_rtsp','status','lat','lng','created_at'), ['ID','Building ID','Room ID','RTSP','Status','Lat','Lng','Created At']],
            'contacts' => [Contact::select('id','name','email','whatsapp','instagram','address','created_at'), ['ID','Name','Email','Whatsapp','Instagram','Address','Created At']],
            'notifications' => [SystemNotification::select('id','type','message','read_at','created_at'), ['ID','Type','Message','Read At','Created At']],
        ];
        if (!isset($map[$entity])) abort(404);
        [$query, $headings] = $map[$entity];
        $rows = $query->get();

        $export = new class($rows, $headings) implements FromCollection, WithHeadings {
            private $rows; private $headings;
            public function __construct($rows, $headings){ $this->rows=$rows; $this->headings=$headings; }
            public function headings(): array { return $this->headings; }
            public function collection() { return $this->rows; }
        };

        $filename = $entity.'_'.now()->format('Ymd_His').'.xlsx';
        return Excel::download($export, $filename);
    }
}

