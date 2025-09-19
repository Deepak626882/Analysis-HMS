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
        $url = url("/outlet/{$compdata->propertyid}/{$chk->dcode}/" . str_replace(' ', '_', $compdata->comp_name));

        $qrImage = QrCode::format('png')->size(512)->margin(1)->generate($url);

        $qr = imagecreatefromstring($qrImage);

        $iconPath = public_path('admin/images/favicon.png');
        $icon = imagecreatefrompng($iconPath);

        $qrWidth = imagesx($qr);
        $qrHeight = imagesy($qr);
        $iconWidth = imagesx($icon);
        $iconHeight = imagesy($icon);

        $scale = 0.2;
        $newIconWidth = $qrWidth * $scale;
        $newIconHeight = ($iconHeight / $iconWidth) * $newIconWidth;

        $resizedIcon = imagecreatetruecolor($newIconWidth, $newIconHeight);
        imagecolortransparent($resizedIcon, imagecolorallocatealpha($resizedIcon, 0, 0, 0, 127));
        imagealphablending($resizedIcon, false);
        imagesavealpha($resizedIcon, true);
        imagecopyresampled($resizedIcon, $icon, 0, 0, 0, 0, $newIconWidth, $newIconHeight, $iconWidth, $iconHeight);

        $dstX = ($qrWidth - $newIconWidth) / 2;
        $dstY = ($qrHeight - $newIconHeight) / 2;
        imagecopy($qr, $resizedIcon, $dstX, $dstY, 0, 0, $newIconWidth, $newIconHeight);

        ob_start();
        imagepng($qr);
        $finalImage = ob_get_clean();

        imagedestroy($qr);
        imagedestroy($icon);
        imagedestroy($resizedIcon);

        return response($finalImage)->header('Content-Type', 'image/png');
    }
}
