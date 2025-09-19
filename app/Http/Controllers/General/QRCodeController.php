<?php

namespace App\Http\Controllers\General;

use App\Http\Controllers\Controller;
use App\Models\Depart;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class QRCodeController extends Controller
{
    protected $propertyid;

    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            if (!isset(Auth::user()->name)) {
                return redirect('/');
            }
            $this->propertyid = Auth::user()->propertyid;
            return $next($request);
        });
    }

    public function outletqrgenerater(Request $request)
    {
        $dcode = $request->dcode;

        $chk = Depart::where('propertyid', $this->propertyid)->where('dcode', $dcode)->first();

        if (is_null($chk)) {
            return response()->json([
                'message' => 'Invalid Depart Code',
                'success' => false
            ], 401);
        }
        $compdata = companydata();

        $url = url("/outlet/{$compdata->propertyid}/{$chk->dcode}/{$compdata->comp_name}");

        $icon = "/public/admin/images/favicon.png";

        $qrcode = QrCode::size(512)
            ->format('png')
            ->merge($icon)
            ->errorCorrection('M')
            ->generate($url);

        return response($qrcode)->header('Content-Type', 'image/png');
    }
}
