<?php

namespace App\Http\Controllers;

use App\Models\HousekeeperMast;
use App\Models\RoomBlockout;
use App\Models\RoomClean;
use App\Models\RoomMast;
use App\Models\UpdateLog;
use Illuminate\Support\Facades\Event; //created by ananya
use App\Events\UpdateLogNotification; //created by ananya
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

use function App\Helpers\getNcurDate;

class HouseKeeping extends Controller
{
    protected $username;
    protected $email;
    protected $propertyid;
    protected $currenttime;
    protected $ptlngth;
    protected $prpid;
    protected $ncurdate;

    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            if (!isset(Auth::user()->name)) {
                return redirect('index.php');
            }

            $this->username = Auth::user()->name;
            $this->email = Auth::user()->email;
            $this->prpid = Auth::user()->propertyid;
            $propertydata = DB::table('users')->where('propertyid', $this->prpid)->first();
            $this->ncurdate = DB::table('enviro_general')->where('propertyid', Auth::user()->propertyid)->value('ncur');
            $this->propertyid = $propertydata->propertyid;
            $this->ptlngth = strlen($this->propertyid);
            date_default_timezone_set('Asia/Kolkata');
            $this->currenttime = date('Y-m-d H:i:s');
            return $next($request);
        });
    }
    # Warning: Abandon hope, all who enter here. 😱

    public function housekeepingscreen(Request $request)
    {
        $permission = revokeopen(151111);
        if (is_null($permission) || $permission->view == 0) { 
            return redirect()->back()->with('error', 'You have no permission to execute this functionality!');
        }
        $rooms = DB::table('room_mast')
            ->select(
                'room_mast.rcode as roomno',
                'room_mast.room_stat',
                'roomocc.roomno as roomnoroomocc',
                'roomocc.type',
                DB::raw("'' as status"),
                DB::raw("
                CASE  
                    WHEN roomocc.type = 'O' OR roomocc.roomno IS NULL THEN 'fa-door-open text-success' 
                    ELSE 'fa-door-closed text-danger' 
                END AS ficon
            "),
                'roomblockout.block'
            )
            ->leftJoin('roomocc', function ($join) {
                $join->on('roomocc.roomno', '=', 'room_mast.rcode')
                    ->where('roomocc.propertyid', '=', $this->propertyid)
                    ->whereRaw('roomocc.sn = (SELECT MAX(sn) FROM roomocc WHERE roomno = room_mast.rcode AND propertyid = ?)', [$this->propertyid]);
            })
            ->leftJoin('roomblockout', function ($join) {
                $join->on('roomblockout.roomcode', '=', 'room_mast.rcode')
                    ->whereNull('roomblockout.cleardate')
                    ->where('roomblockout.propertyid', $this->propertyid);
            })
            ->where('room_mast.propertyid', $this->propertyid)
            ->where('room_mast.type', 'RO')
            ->where('room_mast.inclcount', 'Y')
            ->groupBy('room_mast.rcode')
            ->get();

        $totaloccupied = 0;

        foreach ($rooms as $row) {
            if ($row->type == null) {
                $totaloccupied++;
            }
        }

        $housekeeper = HousekeeperMast::where('propertyid', $this->propertyid)
            ->orderBy('name', 'ASC')
            ->get();

        // dd(get_included_files());
        // dd(getNcurDate());
        // exit;

        return view('property.housekeeping', [
            'rooms' => $rooms,
            'totaloccupied' => $totaloccupied,
            'housekeeper' => $housekeeper
        ]);
    }

    public function housemaster(Request $request)
    {
        $permission = revokeopen(151112);
        if (is_null($permission) || $permission->view == 0) { 
            return redirect()->back()->with('error', 'You have no permission to execute this functionality!');
        }
        $data = HousekeeperMast::where('propertyid', $this->propertyid)
            ->orderBy('name', 'ASC')
            ->get();

        return view('property.housemaster', ['data' => $data]);
    }

    public function submithousemaster(Request $request)
    {
        $permission = revokeopen(151112);
        if (is_null($permission) || $permission->ins == 0) { 
            return redirect()->back()->with('error', 'You have no permission to execute this functionality!');
        }
        $data = $request->except('_token');
        $scode = HousekeeperMast::where('propertyid', $this->propertyid)
            ->max('scode');
        $scode = HousekeeperMast::where('propertyid', $this->propertyid)->max('scode');
        if ($scode === null) {
            $scode = 1;
        } else {
            $scode = intval(substr($scode, 0, -3)) + 1;
        }

        $existingName = HousekeeperMast::where('name', $data['name'])
            ->where('propertyid', $this->propertyid)
            ->first();

        if ($existingName) {
            return back()->with('error', 'House Keeping Master Name already exists!');
        }

        try {
            $insertdata = [
                'u_entdt' => $this->currenttime,
                'scode' => $scode . $this->propertyid,
                'u_name' => Auth::user()->u_name,
                'propertyid' => $this->propertyid,
                'u_ae' => 'a',
            ] + $data;

            HousekeeperMast::insert($insertdata);

            return back()->with('success', 'House Keeping Master Inserted successfully!');
        } catch (Exception $e) {
            return back()->with('error', 'Unable to Insert House Keeping Master!' . $e->getMessage());
        }
    }

    public function updatehousemaster(Request $request)
    {
        $permission = revokeopen(151112);
        if (is_null($permission) || $permission->edit == 0) { 
            return redirect()->back()->with('error', 'You have no permission to execute this functionality!');
        }
        $existingName = HousekeeperMast::where('name', $request->input('updatename'))
            ->whereNot('scode', $request->input('updatecode'))
            ->where('propertyid', $this->propertyid)
            ->first();

        if ($existingName) {
            return back()->with('error', 'House Keeping Master Name Already Exists!');
        }

        try {
            $updatedata = [
                'name' => $request->input('updatename'),
                'activeYN' => $request->input('upactiveYN'),
                'u_updatedt' => $this->currenttime,
                'u_name' => Auth::user()->u_name,
                'propertyid' => $this->propertyid,
                'u_ae' => 'e',
            ];
            HousekeeperMast::where('scode', $request->input('updatecode'))
                ->where('propertyid', $this->propertyid)
                ->update($updatedata);
            return back()->with('success', 'House Keeping Master Updated successfully!');
        } catch (Exception $e) {
            return back()->with('error', 'An error occurred: ' . $e->getMessage());
        }
    }

    public function deletehousekeepingmaster(Request $request, $sn, $ucode)
    {
        $permission = revokeopen(151112);
        if (is_null($permission) || $permission->del == 0) { 
            return redirect()->back()->with('error', 'You have no permission to execute this functionality!');
        }
        try {
            $jaldiwahasehato📢 = HousekeeperMast::where('propertyid', $this->propertyid)
                ->where('scode', $ucode)
                ->where('sn', $sn)
                ->delete();
            if ($jaldiwahasehato📢) {
                return back()->with('success', 'House Keeping Master Deleted successfully!');
            } else {
                return back()->with('error', 'Unable to Delete House Keeping Master!');
            }
        } catch (Exception $e) {
            return back()->with('error', 'An error occurred: ' . $e->getMessage());
        }
    }

    public function savehousecleaning(Request $request)
    {
        if (empty($request->roomno)) {
            return response()->json([
                'status' => 'error',
                'message' => 'Invalid Roomno'
            ]);
        }


        try {

            DB::beginTransaction();
            if (in_array($request->roomstat, ['C', 'D'])) {
                $roomclean = new RoomClean();
                $roomclean->propertyid = $this->propertyid;
                $roomclean->hosuekeeper = $request->housekeeper ?? '';
                $roomclean->roomno = $request->roomno;
                $roomclean->remarks = $request->remarks ?? '';
                $roomclean->type = $request->roomstat;
                $roomclean->u_entdt = $this->currenttime;
                $roomclean->u_updatedt = null;
                $roomclean->u_ae = 'a';
                $roomclean->save();

                $roommast = RoomMast::where('propertyid', $this->propertyid)->where('inclcount', 'Y')->where('type', 'RO')->where('rcode', $request->roomno)
                    ->first();
                $roommast->room_stat = $request->roomstat;
                $roommast->u_updatedt = $this->currenttime;
                $roommast->save();

                DB::commit();

                return response()->json([
                    'status' => 'success',
                    'message' => 'Room Updated successfully Type: ' . $request->roomstat
                ]);
            } else if (in_array($request->roomstat, ['O'])) {
                $rblkout = new RoomBlockout();
                $rblkout->propertyid = $this->propertyid;
                $rblkout->roomcode = $request->roomno;
                $rblkout->block = $request->block;
                $rblkout->reasons = $request->reasons;
                $rblkout->fromdate = $request->fromdate;
                $rblkout->todate = $request->todate;
                $rblkout->type = $request->roomstat;
                $rblkout->u_name = Auth::user()?->u_name;
                $rblkout->u_entdt = $this->currenttime;
                $rblkout->u_updatedt = null;
                $rblkout->u_ae = 'a';
                $rblkout->vtime = date('H:i:s');
                $rblkout->guestname = $request->guestname ?? '';
                $rblkout->mobileno = $request->mobileno ?? '';
                $rblkout->save();

                $roommast = RoomMast::where('propertyid', $this->propertyid)->where('inclcount', 'Y')->where('type', 'RO')->where('rcode', $request->roomno)
                    ->first();
                $roommast->room_stat = $request->roomstat;
                $roommast->u_updatedt = $this->currenttime;
                $roommast->save();

                DB::commit();

                return response()->json([
                    'status' => 'success',
                    'message' => 'Room Updated successfully Type: ' . $request->roomstat
                ]);
            } else if (in_array($request->roomstat, ['R'])) {
                $rblkout = RoomBlockout::where('propertyid', $this->propertyid)->where('roomcode', $request->roomno)->where('type', 'O')
                    ->whereNull('cleardate')->first();
                $rblkout->u_updatedt = $this->currenttime;
                $rblkout->u_ae = 'e';
                $rblkout->type = $request->roomstat;
                $rblkout->cleardate = $this->ncurdate;
                $rblkout->cleartime = date('H:i:s');
                $rblkout->clearuser = Auth::user()->u_name;
                $rblkout->clearremark = $request->clearremark;
                $rblkout->save();

                $roommast = RoomMast::where('propertyid', $this->propertyid)->where('inclcount', 'Y')->where('type', 'RO')->where('rcode', $request->roomno)
                    ->first();
                $roommast->room_stat = 'C';
                $roommast->u_updatedt = $this->currenttime;
                $roommast->save();

                DB::commit();

                return response()->json([
                    'status' => 'success',
                    'message' => 'Room Updated successfully Type: ' . $request->roomstat
                ]);
            } else {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Invalid Room Stat: ' . $request->roomstat
                ]);
            }
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => 'error',
                'message' => 'Unknown Error Occured: ' . $e->getMessage() . ' On Line: ' . $e->getLine()
            ], 500);
        }
    }

    public function updatelogform()
    {
        $mainMenus = DB::table('tbl_usermodule')
            ->where('flag', 'N')
            ->where('opt2', 0)
            ->select('module', 'module_name', 'opt1', 'opt2', 'opt3')
            ->distinct() // Ensure unique entries
            ->get();

        $data = UpdateLog::orderBy('u_entdt', 'DESC')->get();

        return view('admin.updatelogform', compact('data', 'mainMenus'));
    }




    public function submitupdatelogform(Request $request)
    {

        try {
            $uplog = new UpdateLog();
            $uplog->mainmenu = $request->mainmenu;
            $uplog->submenu = $request->submenu;
            $uplog->pagename = $request->pagename;
            $uplog->summary = $request->summary;
            $uplog->u_entdt = $this->currenttime;
            $uplog->save();



            return back()->with('success', 'Update Log Inserted successfully!');
        } catch (Exception $e) {
            return back()->with('error', 'Unable to Insert Update Log: ' . $e->getMessage(), 500);
        }
    }

    public function submenufetch(Request $request)
    {
        $opt1 = $request->opt1;
        $opt3 = $request->opt3;

        $submenus = DB::table('tbl_usermodule')
            ->where('opt1', $opt1)
            ->whereNot('opt2', '0')
            ->where('opt3', $opt3)
            ->get();

        return response()->json($submenus);
    }



    public function pagenamefetch(Request $request)
    {
        $opt1 = $request->opt1;
        $opt2 = $request->opt2;

        $pages = DB::table('tbl_usermodule')
            ->where('opt1', $opt1)
            ->where('opt2', $opt2)
            ->whereNot('opt3', '0')
            ->get();

        return response()->json($pages);
    }


    public function deleteupdatelog(Request $request)
    {
        $sn = base64_decode($request->sn);
        if (!$sn) {
            return back()->with('error', 'SN is missing or undefined!');
        }

        Log::info("Trying to delete record with sn: " . $sn);
        $record = UpdateLog::where('sn', $sn);

        if ($record) {
            $record->delete();
            return back()->with('success', "Record with sn: $sn deleted successfully!");
        }

        return back()->with('error', 'Record not found.');
    }
}
