<?php

namespace App\Http\Controllers;

use App\Models\Slot;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class CoreController extends Controller
{
    //
    public function main()
    {
        $slots = Slot::groupBy('date', 'month', 'day')->select('date', 'month', 'day')->orderBy('date', 'asc')->get();
        $data = [];
        foreach ($slots as $i => $item) {
            $data[$item->month][$i] = (object) [
                'fulldate' => date("Y-m-d", strtotime($item->date)),
                'date' => date("d", strtotime($item->date)),
                'day' => $item->day,
                'active' => false,
            ];
            $isFull = Slot::whereDate('date', $item->date)->where('active', true)->first();
            if ($data[$item->month][$i]->active == false && $isFull !== null && $item->date >= date('Y-m-d')) {
                $data[$item->month][$i]->active = true;
            }
        }

        return view('index')->with(compact('data'));
    }
    public function mySlot()
    {
        return view('myslot');
    }
    public function viewSlot(Request $req)
    {
        $user = $req->userid;
        $slot = Slot::where('owner', $user)->first();
        $strTime = strtotime($slot->date);

        $data = '<div class="ps-3 pb-1">Date : ' . date("d M Y", $strTime) . ' ( ' . $slot->time . ' )</div>';
        $data .= '<div class="ps-3 pb-1">Interview Type : ' . $slot->interview_type;
        if ($slot->interview_type == "Online") {
            $data .= '<a class="text-blue-600 font-bold" target="_blank" href="https://teams.microsoft.com/l/meetup-join/19%3ameeting_Zjg5MWVmNjUtYWQxNS00YWEwLWFjNjEtNzA1ODMyMWJiNjMy%40thread.v2/0?context=%7b%22Tid%22%3a%2219fcd1ff-f029-46a2-9b9c-a67782736715%22%2c%22Oid%22%3a%22553204d2-9783-4fd2-91d4-906de5ae26db%22%7d"> LINK</a></div>';
        } else if ($slot->interview_type == "offline") {
            $data .= '<span class="text-blue-600 font-bold cursor-pointer"> Face to Face</span></div>';
        } else {
            $data .= '</div>';
        }
        $data .= '<div class="ps-3 pb-1">Teacher : ' . $slot->name . '</div>';

        return response()->json(['status' => 1, 'data' => $data], 200);
    }
    public function checkDate(Request $req)
    {
        $date = $req->date;
        $findSlot = Slot::whereDate('date', $date)->where('active', 1)->first();
        if ($findSlot == null) {
            return response()->json(["status" => 2, "text" => "Full Select Date."], 200);
        }

        return response()->json(["status" => 1, "text" => "Date Can be select!"], 200);
    }
    public function selectDate($date)
    {
        $slot = Slot::whereDate('date', $date)->get();
        $date = date('D d M Y', strtotime($date));
        $data = [];
        foreach ($slot as $item) {
            $data[$item->name]['id'] = rand(0, 1000000);
            $data[$item->name]['slot'][] = [
                "id" => $item->id,
                "time" => $item->time,
                "active" => $item->active,
            ];
        }

        return view('select')->with(compact('date', 'data'));
    }
    public function auth($req)
    {
        $response = Http::withHeaders([
            'token' => env('API_KEY'),
        ])->post('http://172.20.1.12/dbstaff/api/auth', [
            "userid" => $req->userid,
            "password" => $req->password,
        ]);

        return $response->json();
    }
    public function saveSlot(Request $req)
    {
        $response = $this->auth($req);

        if ($response["status"] == 1) {
            $slot = Slot::find($req->id);
            if ($slot->active) {
                $oldSlots = Slot::where('owner', $req->userid)->get();
                if (count($oldSlots) > 0) {
                    foreach ($oldSlots as $oldSlot) {
                        $oldSlot->active = true;
                        $oldSlot->owner = null;
                        $oldSlot->interview_type = null;
                        $oldSlot->save();
                    }

                    $text = 'เปลี่ยนรอบที่จองสำเร็จ!';
                } else {
                    $text = 'จองสำเร็จ!';
                }
                $slot->active = false;
                $slot->owner = $req->userid;
                $slot->interview_type = $req->interview_type;
                $slot->save();

                $res = [
                    "status" => 1,
                    "text" => $text,
                ];
            } else {
                $res = [
                    "status" => 3,
                    "text" => "Already Book!",
                ];
            }

        } else if ($response["status"] == 2) {

            $res = [
                "status" => 2,
                "text" => "Wrong Userid or Password!",
            ];
        }

        return response()->json($res, 200);
    }
    public function authAdmin(Request $req)
    {
        return $this->auth($req);
    }
    public function admin()
    {
        $teachers = Slot::groupBy('user', 'name')->select('user', 'name')->orderby('user', 'asc')->get();

        return view('admin')->with(compact('teachers'));
    }
    public function addTeacher(Request $req)
    {
        $dateStart = date_create('2024-08-01');
        $dateEnd = date_create('2024-09-30');
        $diff = date_diff($dateStart, $dateEnd);
        $name = $req->name;
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
        $record = Slot::groupBy('user', 'name')->select('user')->orderby('user', 'desc')->first();
        if ($record == null) {
            $record = (object) [
                'user' => 0,
            ];
        }
        for ($i = 1; $i <= $diff->days + 1; $i++) {
            $date = date_format($dateStart, "Y-m-d");
            foreach ($slot as $index => $s) {
                $new = new Slot;
                $new->user = $record->user + 1;
                $new->name = $name;
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

        return response()->json(["status" => 1], 200);
    }
    public function teacherEdit($id)
    {
        $slots = Slot::where('user', $id)->orderBy('date', 'asc')->orderby('time_index', 'asc')->get();
        $tempDate = [];
        $data = [
            "user" => $slots->first()->user,
            "name" => $slots->first()->name,
            "slot" => [],
        ];
        foreach ($slots as $item) {
            if ($item->time == "Break") {
                continue;
            }
            $time = explode(' ', $item->time);
            $strTime = strtotime($item->date);
            $date = date("Y-m-d", $strTime);
            $Fulldate = date("d/m/Y", $strTime);

            if (!in_array($date, $tempDate)) {
                $data["slot"][$date] = [
                    'FullDate' => $Fulldate,
                    'active' => false,
                    'time' => [],
                ];
                $tempDate[] = $date;
            }

            $data["slot"][$date]["time"][] = [
                'id' => $item->id,
                'time' => $time[0],
                'active' => $item->active,
                'owner' => $item->owner,
            ];
            if ($item->active) {
                $data["slot"][$date]["active"] = true;
            }
        }

        return view('teacher')->with(compact('data'));
    }
    public function updateSlot(Request $req)
    {
        $user = $req->user;
        $date = $req->date;
        $enabled = ($req->check == 1) ? false : true;

        $slot = Slot::where('date', $date)->where('user', $user)->whereNull('owner')->get();
        foreach ($slot as $item) {
            $item->active = $enabled;
            $item->save();
        }

        return response()->json(['status' => 1], 200);
    }
    public function updateTime(Request $req)
    {
        $id = $req->id;
        $enabled = ($req->check == 1) ? false : true;
        $solt = Slot::find($id);
        $solt->active = $enabled;
        $solt->save();

        return response()->json(['status' => 1], 200);
    }
}
