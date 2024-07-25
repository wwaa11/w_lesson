<?php

namespace App\Http\Controllers;

use App\Models\Slot;

class CoreController extends Controller
{
    //
    public function main()
    {
        // $this->newTeacher();
        // die();
        $slots = Slot::groupBy('date', 'month', 'day')->select('date', 'month', 'day')->orderBy('date', 'asc')->get();
        $data = [];
        foreach ($slots as $i => $item) {
            $data[$item->month][$i] = (object) [
                'date' => date("d", strtotime($item->date)),
                'day' => $item->day,
                'active' => false,
            ];
            $isFull = Slot::whereDate('date', $item->date)->where('active', true)->first();
            if ($data[$item->month][$i]->active == false && $isFull !== null) {
                $data[$item->month][$i]->active = true;
            }
        }

        return view('index')->with(compact('data'));
    }
    public function newTeacher()
    {
        $name = "Role";
        $slot = [
            "9:00 - 9:20",
            "9:20 - 9:40",
            "9:40 - 10:00",
            "10:00 - 10:20",
            "10:20 - 10:40",
            "10:40 - 11:00",
            "11:00 - 11:20",
            "11:20 - 11:40",
            "11:40 - 12:00",
            "Break",
            "14:00 - 14:20",
            "14:20 - 12:40",
            "14:40 - 15:00",
            "15:00 - 15:20",
            "15:20 - 15:40",
            "15:40 - 16:00",
            "16:00 - 16:20",
            "16:20 - 16:40",
        ];
        $dateStart = date_create('2024-08-01');
        $dateEnd = date_create('2024-09-30');
        $diff = date_diff($dateStart, $dateEnd);
        for ($i = 1; $i <= $diff->days + 1; $i++) {
            $date = date_format($dateStart, "Y-m-d");
            foreach ($slot as $index => $s) {
                $new = new Slot;
                $new->user = '1';
                $new->name = 'Teacher Role';
                $new->date = $date;
                $new->day = date_format($dateStart, "D");
                $new->month = date_format($dateStart, "F");
                $new->time_index = $index;
                $new->time = $s;
                $new->active = ($index == 9) ? 0 : 1;
                $new->save();
            }
            $dateStart = date_add($dateStart, date_interval_create_from_date_string("1 days"));
        }
    }
}
