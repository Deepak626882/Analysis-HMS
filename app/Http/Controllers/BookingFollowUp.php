<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

use App\Models\BookingInquiry;
use App\Models\BookingFollowUp as BookingFollowUpModel;

class BookingFollowUp extends Controller
{
    public function index()
    {
        return view('property.booking_inquiryfollup');
    }

    public function data(Request $request)
    {
        DB::enableQueryLog();
        $query = BookingInquiry::query()
            // ->leftJoin('booking_follow_ups', 'bookinginquiry.inqno', '=', 'booking_follow_ups.inqno')
            ->leftJoin('cities', 'cities.city_code', '=', 'bookinginquiry.citycode')
            ->leftJoin('bookingdetail', function ($join) {
                $join->on('bookingdetail.inqno', '=', 'bookinginquiry.inqno')
                    ->on('bookingdetail.propertyid', '=', 'bookinginquiry.propertyid');
            })
            ->leftJoin('venuemast', 'venuemast.code', '=', 'bookingdetail.venuecode');

        // Filter by status if provided (for DataTables column search)
        if ($request->has('status')) {
            $status = $request->input('status');

            if ($status !== null && $status !== '' && $status !== 'undefined') {
                $query->where('bookinginquiry.status', $status);
            }
        }

        $totalData = $query->count();

        // Search filter
        if ($search = $request->input('search.value')) {
            $query->where(function ($q) use ($search) {
                $q->where('bookinginquiry.partyname', 'like', "%$search%")
                    ->orWhere('bookinginquiry.mobileno', 'like', "%$search%")
                    ->orWhere('venuemast.name', 'like', "%$search%")
                    ->orWhere('bookinginquiry.remark', 'like', "%$search%")
                    ->orWhere('bookingdetail.fromdate', 'like', "%$search%")
                    ->orWhere('bookinginquiry.follupdate', 'like', "%$search%")
                    ->orWhere('bookinginquiry.status', "$search")
                    ->orWhere('bookinginquiry.pax', 'like', "%$search%")
                    ->orWhere('bookinginquiry.gurrpax', 'like', "%$search%");
            });
        }

        $totalFiltered = $query->count();

        // Order
        $orderColumnIndex = $request->input('order.0.column', 0);
        $orderColumn = $request->input("columns.$orderColumnIndex.data", 'date');
        $orderDir = $request->input('order.0.dir', 'desc');
        $query->orderBy($orderColumn, $orderDir);

        // Pagination
        $start = $request->input('start', 0);
        $length = $request->input('length', 10);
        $data = $query->skip($start)->take($length)
            ->select([
                'bookinginquiry.inqno as id',
                'bookinginquiry.partyname as name',
                'bookinginquiry.mobileno as mobile',
                'venuemast.name as hall',
                'bookingdetail.fromdate as date',
                'bookingdetail.fromtime as time',
                'bookinginquiry.pax as expected_pax',
                'bookinginquiry.gurrpax as guaranteed_pax',
                'bookinginquiry.follupdate',
                'bookinginquiry.remark as remark',
                'cities.cityname as city',
                'bookinginquiry.status as status',
            ])->get();

        $result = [];
        $rowIndex = $start + 1;
        foreach ($data as $row) {
            // Format date and time
            $dateFormatted = '';
            $timeFormatted = '';
            if (!empty($row->date)) {
                $dateFormatted = date('d-m-Y', strtotime($row->date));
            }
            if (!empty($row->time)) {
                $timeFormatted = date('h:i', strtotime($row->time));
            }
            $statusText = ($row->status == 1) ? 'Running' : 'Closed';
            $statusClass = ($row->status == 1) ? 'success' : 'warning';
            $statusBadge = '<span class="badge bg-' . $statusClass . '">' . $statusText . '</span>';
            $actionButton = ($row->status == 1) ? '<button type="button" class="btn btn-success btn-sm" onclick="openUpdateModal(' . $row->id . ')">Update</button>' : '';

            $result[] = [
                'DT_RowIndex' => $rowIndex++, // serial number
                'id' => $row->id,
                'name' => $row->name,
                'mobile' => $row->mobile,
                'hall' => $row->hall,
                'date' => trim($dateFormatted . ' ' . $timeFormatted),
                'expected_pax' => $row->expected_pax,
                'guaranteed_pax' => $row->guaranteed_pax,
                'follow_up_date' => date('d-m-Y h:i', strtotime($row->follupdate)),
                'remark' => $row->remark,
                'status' => $statusBadge,
                'action' => $actionButton,
            ];
        }

        // Print the SQL query for debugging
        $queries = DB::getQueryLog();
        Log::info('BookingFollowUp DataTable Query:', $queries);
        return response()->json([
            'draw' => intval($request->input('draw')),
            'recordsTotal' => $totalData,
            'recordsFiltered' => $totalFiltered,
            'data' => $result,
        ]);
    }


    public function store(Request $request)
    {
        $validated = $request->validate([
            'sn' => 'required|integer',
            'next_follow_date' => 'required|date',
            'remark' => 'required|string',
            'status' => 'required|in:0,1',
        ]);
        //dd($validated);

        $enquiry = BookingInquiry::where('inqno', $validated['sn'])->first();
        //  $enquiry->status = $validated['status'];
        //  $enquiry->save();
        $dateandtime = DB::table('bookingdetail')->select('fromdate', 'fromtime')->where('inqno', $enquiry['inqno'])->first();

        // Always insert a new followup comment (do not update by inqno)
        $followup = new BookingFollowUpModel();
        $followup->inqno            = $enquiry['inqno'];
        $followup->propertyid       = $enquiry['propertyid'];
        $followup->sno              = $enquiry['sn'];
        $followup->date             = $dateandtime->fromdate;
        $followup->time             = $dateandtime->fromtime;
        $followup->u_name           = $enquiry['u_name'];
        $followup->u_ae             = $enquiry['u_ae'];
        $followup->nextfollowupdate = $validated['next_follow_date'];
        $followup->remark           = $validated['remark'];
        $followup->status           = $validated['status'];
        $followup->save();

        DB::table('bookinginquiry')->where('inqno', $validated['sn'])->update(['status' => $validated['status']]);


        return response()->json(['success' => true]);
    }

    public function comments($inqno)
    {
        $comments = BookingFollowUpModel::where('inqno', $inqno)
            ->orderBy('created_at', 'desc')
            ->get(['remark', 'nextfollowupdate as date']);

        // Format date as d-m-Y h:i
        $comments = $comments->map(function ($c) {
            return [
                'remark' => $c->remark,
                'date' => $c->date ? date('d-m-Y h:i', strtotime($c->date)) : '',
            ];
        });
        return response()->json($comments);
    }
}
