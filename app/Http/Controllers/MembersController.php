<?php

namespace App\Http\Controllers;

use App\Models\MemberCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class MembersController extends Controller
{

    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            if (!isset(Auth::user()->name)) {
                return redirect('/');
            }

            return $next($request);
        });
    }

    public function openmembercategory()
    {
        return view('property.members.category');
    }

    public function categoryStore(Request $request)
    {
        try {

            $request->validate([
                'title'  => 'required|string|max:191',
                'short_name'       => 'required|string|max:191',
                'subscription'     => 'required|in:yes,no',
                'surcharge'        => 'required|in:yes,no',
                'facility_billing' => 'required|in:yes,no',
                'status'           => 'required|in:active,inactive',
            ]);

            DB::beginTransaction();

            $duplicatename = MemberCategory::where('propertyid', Auth::user()->propertyid)->where('title', $request->title)->first();

            if (!is_null($duplicatename)) {
                return redirect()->back()->with('error', 'Member category name already exist!');
            }

            $duplicatenameshort = MemberCategory::where('propertyid', Auth::user()->propertyid)->where('short_name', $request->short_name)->first();

            if (!is_null($duplicatenameshort)) {
                return redirect()->back()->with('error', 'Member category short name already exist!');
            }

            $maxinqresult = DB::table('member_categories')
                ->select(DB::raw('MAX(CAST(code AS SIGNED)) AS max_code'))
                ->where('propertyid', Auth::user()->propertyid)
                ->first();

            $maxcode = $maxinqresult->max_code;

            if ($maxcode == null) {
                $code = Auth::user()->propertyid . '1';
            } else {
                $serial = (int)substr($maxcode, strlen(Auth::user()->propertyid)) + 1;
                $code = Auth::user()->propertyid . $serial;
            }

            $membercategory = new MemberCategory();
            $membercategory->propertyid = Auth::user()->propertyid;
            $membercategory->code             = $code;
            $membercategory->title  = $request->title;
            $membercategory->short_name       = $request->short_name;
            $membercategory->subscription     = $request->subscription;
            $membercategory->surcharge        = $request->surcharge;
            $membercategory->facility_billing = $request->facility_billing;
            $membercategory->status           = $request->status;
            $membercategory->u_entdt = now();
            $membercategory->u_updatedt = null;
            $membercategory->save();

            DB::commit();

            return redirect()->back()->with('success', 'Member category created successfully!');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Failed to save member category: ' . $e->getMessage());
        }
    }
}
