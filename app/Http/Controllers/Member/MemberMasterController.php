<?php

namespace App\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use App\Models\MemberCategory;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class MemberMasterController extends Controller
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

    public function openmembermaster(Request $request)
    {
        return view('property.members.master');
    }

    public function store(Request $request)
    {
        $request->validate([
            'fullname' => 'required',
        ]);

        $profilefilename = '';
        if ($request->hasFile('member_photo')) {
            $file = $request->file('member_photo');
            $profilefilename = Str::random(16) . '_profile_' . time() . '.' . $file->getClientOriginalExtension();
            $folder = 'property/member/profile';
            $destinationPath = storage_path('app/public/' . $folder);
            if (!file_exists($destinationPath)) mkdir($destinationPath, 0755, true);
            $file->move($destinationPath, $profilefilename);
        }

        $signfilename = '';
        if (!empty($request->input('signimage'))) {
            $imageData = $request->input('signimage');
            $encodedImage = str_replace('data:image/png;base64,', '', $imageData);
            $decodedImage = base64_decode($encodedImage);
            $signfilename = Str::random(16) . '_signature_' . time() . '.png';
            $folder = 'property/member/signature';
            $path = storage_path('app/public/' . $folder . '/' . $signfilename);
            if (!file_exists(dirname($path))) mkdir(dirname($path), 0755, true);
            file_put_contents($path, $decodedImage);
        }

        return response()->json([
            'profile_photo' => $profilefilename,
            'signature_image' => $signfilename,
            'message' => 'Member data stored successfully'
        ]);
    }
}
