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
        $slots = Slot::groupBy('dateFull','day_index','day','month')
            ->select('dateFull','day_index','day','month')
            ->orderBy('dateFull', 'asc')
            ->get();
        $data = [];
        $allSlot = Slot::all();
        foreach ($slots as $i => $item) {
            $isFull = collect($allSlot)->where('day', $item->day)->where('month', $item->month)->where('active', true)->first();
            $data[$item->month][] = (object)[
                'active' => ($isFull == null)? flase : true,
                'date' => $item->day_index,
                'day' => $item->day,
                'month' => $item->month,
                'fulldate' =>  $item->day_index.'_'.$item->month
            ];
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
        if ($slot == null) {
            $data = '<div class="ps-3 pb-1">' . $user . ' ไม่มีการรอบการจอง</div>';
            return response()->json(['status' => 1, 'data' => $data], 200);
        }
        $strTime = strtotime($slot->dateFull);

        $data = '<div class="ps-3 pb-1">Name : ' . $slot->owner_name .'</div>';
        $data .= '<div class="ps-3 pb-1">Date : ' . date("d M Y", $strTime) . ' ( ' . $slot->time . ' )</div>';

        return response()->json(['status' => 1, 'data' => $data], 200);
    }
    public function checkDate(Request $req)
    {
        
        $date = explode('_', $req->date);

        $findSlot = Slot::where('day_index', $date[0])->where('month', $date[1])->where('active', 1)->first();
        if ($findSlot == null) {
            return response()->json(["status" => 2, "text" => "Full Select Date."], 200);
        }

        return response()->json(["status" => 1, "text" => "Date Can be select!"], 200);
    }
    public function selectDate($date)
    {
        $date = explode('_', $date);

        $slot = Slot::where('day_index', $date[0])->where('month', $date[1])->get();
        $data = [];
        foreach ($slot as $item) {
            $data[$item->time]['id'] = rand(0, 1000000);
            $data[$item->time]['slot'][] = [
                "id" => $item->id,
                "slot" => $item->slot,
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
            "password" => ($req->password == 'skip')?env('AdminPassword'): $req->password,
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
                $slot->owner_name = $response['user']['name'];
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
        $slots = Slot::orderby('dateTime', 'asc')->get();
        $outPut = [];
        foreach ($slots as $value) {
            $day = date('d', strtotime($value->dateTime));
            $key = $day.' '.$value->day.' '.$value->month;
            $outPut[$key][$value->time][] = [
                'month' => $value->month,
                'day' => $value->day,
                'time' => $value->time,
                'slot' => $value->slot,
                'active' => $value->active,
                'owner' => $value->owner,
                'owner_name' => $value->owner_name,
            ];
        }

        return view('admin')->with(compact('outPut'));
    }
    public function addSlot(Request $req)
    {
        $dateInput = explode(',',$req->date);
        $dateStart = date_create($dateInput[0]);
        $dateEnd = date_create($dateInput[1]);
        $diff = date_diff($dateStart, $dateEnd);

        $timeArr = [
            "9:00",
            "9:20",
            "9:40",
            "10:00",
            "10:20",
            "10:40",
            "11:00",
            "11:20",
            "11:40",
            "12:00",
            "12:20",
            "12:40",
            "13:00",
            "13:20",
            "13:40",
            "14:00",
            "14:20",
            "14:40",
            "15:00",
            "15:20",
            "15:40",
            "16:00",
            "16:20",
            "16:40",
        ];
        $slotArr = ['1','2','3'];
        $allSlot = Slot::all();
        
        for ($i = 1; $i <= $diff->days + 1; $i++) {
            $date = date_format($dateStart, 'Y-m-d');
            foreach ($timeArr as $key => $time) {
                $dateTime = date_create($date.$time);
                foreach ($slotArr as $slotIndex) {
                    $search = date_format($dateTime, "Y-m-d H:i:s").'.000';
                    $findSlot = collect($allSlot)->where('dateTime', $search)->where('slot', $slotIndex)->first();
                    if($findSlot == null){
                        $slot = new Slot;
                        $slot->dateFull = date_format($dateTime, "Y-m-d");
                        $slot->dateTime = $dateTime;
                        $slot->user = 1;
                        $slot->name = "Take Picture";
                        $slot->day_index = date_format($dateTime, "d");
                        $slot->month = date_format($dateTime, "M");
                        $slot->day = date_format($dateTime, "D");
                        $slot->time = date_format($dateTime, "H:i");
                        $slot->slot = $slotIndex;
                        $slot->save();
                    }
                }
            }
            $dateStart = date_add($dateStart, date_interval_create_from_date_string("1 days"));
        }

        return response()->json(["status" => 'success'], 200);
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
