<?php

namespace App\Http\Controllers;

use App\Models\UserModule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Models\Companyreg;
use App\Models\UserPermission;
use App\Models\TblUserModule;
use App\Models\MenuHelp;
use App\Models\SubGroup;
use App\Models\User;
use Exception;
use Monolog\Handler\SamplingHandler;
use Symfony\Component\HttpKernel\DependencyInjection\RemoveEmptyControllerArgumentLocatorsPass;
use Symfony\Component\Mailer\Transport\Smtp\Auth\PlainAuthenticator;

class UserParam extends Controller
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

    public function ExportTable()
    {
        echo '<link href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css" rel="stylesheet" />';
        echo '<link href="https://cdn.datatables.net/buttons/2.4.1/css/buttons.dataTables.min.css" rel="stylesheet" />';
        echo '<script src="https://code.jquery.com/jquery-3.5.1.js"></script>';
        echo '<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>';
        echo '<script src="https://cdn.datatables.net/buttons/2.4.1/js/dataTables.buttons.min.js"></script>';
        echo '<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>';
        echo '<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>';
        echo '<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>';
        echo '<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.html5.min.js"></script>';
        echo '<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.print.min.js"></script>';
    }

    public function DownloadTable($tableName, $title, $columnsToExport, $columnToSearch)
    {
        $exportColumnsJS = json_encode($columnsToExport);
        $searchColumnsJS = json_encode($columnToSearch);

        echo "<script>$(document).ready(function() {
        let table = $('#$tableName').DataTable({
            dom: 'Bfrtip',
            pageLength: 15,
            buttons: [
                {
                    extend: 'excelHtml5',
                    text: 'Excel <i class=\"fa fa-file-excel-o\"></i>',
                    title: '$title',
                    filename: '$title',
                    exportOptions: {
                        columns: $exportColumnsJS
                    }
                },
                {
                    extend: 'csvHtml5',
                    text: 'Csv <i class=\"fa-solid fa-file-csv\"></i>',
                    title: '$title',
                    filename: '$title',
                    exportOptions: {
                        columns: $exportColumnsJS
                    }
                },
                {
                    extend: 'pdfHtml5',
                    text: 'Pdf <i class=\"fa fa-file-pdf-o\"></i>',
                    title: '$title',
                    filename: '$title',
                    exportOptions: {
                        columns: $exportColumnsJS
                    }
                },
                {
                    extend: 'print',
                    text: 'Print <i class=\"fa-solid fa-print\"></i>',
                    title: '$title',
                    filename: '$title',
                    exportOptions: {
                        columns: $exportColumnsJS
                    }
                }
            ],
            initComplete: function() {
                // Configure column-specific search inputs based on the specified columns
                let searchColumns = $searchColumnsJS;
                this.api().columns(searchColumns).every(function() {
                    let column = this;
                    let title = column.header().textContent;
                    let input = document.createElement('input');
                    input.placeholder = title;
                    $(input).appendTo($(column.footer()).empty()); // Use jQuery for better compatibility
                    $(input).on('keyup', function () {
                        if (column.search() !== this.value) {
                            column.search(this.value).draw();
                        }
                    });
                });
            }
        });
    });</script>";
    }

    public function revokeopen($code)
    {
        $value = Menuhelp::where('propertyid', $this->propertyid)->where('username', Auth::user()->name)->where('code', $code)->first();
        return $value;
    }

    public function PermisionManage(Request $request)
    {
        $companies = Companyreg::groupBy('propertyid')->orderBy('comp_name', 'ASC')->get();
        return view('admin.permission', ['companies' => $companies]);
    }

    public function validatecheck(Request $request)
    {
        $name = $request->input('name');
        $propertyid = $request->input('propertyid');
        $compdata = Companyreg::where('propertyid', $propertyid)->first();
        $chkval = UserModule::where('module_name', ucfirst($name))->where('propertyid', $propertyid)->first();
        if ($chkval) {
            return json_encode('1');
        } else {
            return json_encode('0');
        }
    }

    public function submipermusermodule(Request $request)
    {
        $validate = $request->validate([
            'propertyid' => 'required',
        ]);

        $modules = [
            'mainsetup' => '12',
            'reservation' => '13',
            'frontoffice' => '14',
            'housekeeping' => '15',
            'inventory' => '16',
            'pointofsale' => '17',
            'nightaudit' => '19',
            'banquet' => '18',
            'hrpayroll' => '23',
            'extras' => '27',
            'membersmgmt' => '20',
            'finance' => '11'
        ];

        $compdata = Companyreg::where('propertyid', $request->input('propertyid'))->first();
foreach ($modules as $moduleName => $moduleid) {
    if ($request->has($moduleName)) {
        // Fetch all module data for this module
        $moduleData = TblUserModule::where('module_name', ucfirst($moduleName))->get();

        // Delete existing UserModule + MenuHelp for this property/module
        UserModule::where('propertyid', $request->input('propertyid'))
            ->whereIn('module', $moduleData->pluck('module'))
            ->delete();

        MenuHelp::where('propertyid', $request->input('propertyid'))
            ->where('username', $compdata->u_name)
            ->whereIn('module', $moduleData->pluck('module'))
            ->delete();

        // Re-insert fresh records
        foreach ($moduleData as $data) {
            // Insert into UserModule
            UserModule::create([
                'propertyid'   => $request->input('propertyid'),
                'opt1'         => $data->opt1,
                'opt2'         => $data->opt2,
                'opt3'         => $data->opt3,
                'route'        => $data->route,
                'code'         => $data->code,
                'module'       => $data->module,
                'module_name'  => $data->module_name,
                'flag'         => $data->flag,
                'outletcode'   => $data->outletcode,
                'u_entdt'      => $this->currenttime,
                'u_updatedt'   => null,
            ]);

            // Insert into MenuHelp
            MenuHelp::create([
                'propertyid'   => $request->input('propertyid'),
                'username'     => $compdata->u_name,
                'compcode'     => $compdata->comp_code,
                'opt1'         => $data->opt1,
                'opt2'         => $data->opt2,
                'opt3'         => $data->opt3,
                'code'         => $data->code,
                'route'        => $data->route,
                'module'       => $data->module,
                'module_name'  => $data->module_name,
                'view'         => 1,
                'ins'          => 1,
                'edit'         => 1,
                'del'          => 1,
                'print'        => 1,
                'flag'         => $data->flag,
                'outletcode'   => $data->outletcode,
                'u_entdt'      => $this->currenttime,
                'u_updatedt'   => null,
                'u_name'       => Auth::user()->name,
            ]);
        }
    }
}
    }



    public function loadcheckbox(Request $request)
    {
        $data = DB::table('permission')->where('propertyid', $request->input('cid'))->get();

        if ($data->isNotEmpty()) {
            return json_encode($data);
        } else {
            return response()->json(['message' => 'No data found for the specified propertyid'], 404);
        }
    }

    public function getmainmenu(Request $request)
    {
        $company = Companyreg::where('propertyid', $this->propertyid)->orderBy('comp_code', 'DESC')->first();

        $data = MenuHelp::where('propertyid', $this->propertyid)->where('username', Auth::user()->name)->where('flag', 'N')
            ->where('opt2', 0)->where('compcode', $company->comp_code)->orderBy('code', 'DESC')->get();
        return json_encode($data);
    }

    public function fetchsubmenu(Request $request)
    {
        $code = $request->input('code');
        $company = Companyreg::where('propertyid', $this->propertyid)->orderBy('comp_code', 'DESC')->first();
        $menuhelp = MenuHelp::where('propertyid', $this->propertyid)->where('compcode', $company->comp_code)->where('username', Auth::user()->name)
            ->where('opt1', $code)->where('flag', 'N')->whereNot('opt2', 0)->get();
        return json_encode($menuhelp);
    }

    public function fetchlastmenu(Request $request)
    {
        $company = Companyreg::where('propertyid', $this->propertyid)->orderBy('comp_code', 'DESC')->first();
        $code = $request->input('code');
        $code2 = $request->input('code2');
        $menuhelp = MenuHelp::where('propertyid', $this->propertyid)->where('username', Auth::user()->name)
            ->where('opt1', $code2)->where('compcode', $company->comp_code)->whereIn('flag', ['E', 'R'])->where('opt2', $code)->get();
        return json_encode($menuhelp);
    }

    public function userpermision(Request $request)
    {
        $permission = revokeopen(122012);
        if (is_null($permission) || $permission->view == 0) {
            return redirect()->back()->with('error', 'You have no permission to execute this functionality!');
        }
        $users = User::where('propertyid', $this->propertyid)->whereNot('u_name', 'sa')->where('status', '1')->get();
        $firms = Companyreg::where('propertyid', $this->propertyid)->where('role', 'Property')->get();
        $sections = UserModule::where('propertyid', $this->propertyid)->where('opt2', 0)->where('opt3', 0)->get();
        return view('property.paramuser', [
            'users' => $users,
            'firms' => $firms,
            'sections' => $sections
        ]);
    }

    public function menulist(Request $request)
    {
        $opt1 = $request->input('opt1');
        $username = $request->input('uname');
        $compcode = $request->input('compcode');
        $menu = MenuHelp::where('propertyid', $this->propertyid)->where('opt1', $opt1)->where('compcode', $compcode)->whereNot('opt2', 0)->where('username', Auth::user()->name)->get();
        $userchecked = MenuHelp::where('propertyid', $this->propertyid)->where('opt1', $opt1)->where('compcode', $compcode)->whereNot('opt2', 0)->where('username', $username)->get();
        $userparam = UserPermission::where('propertyid', $this->propertyid)->where('username', $username)->first();
        $data = [
            'menus' => $menu,
            'userchecked' => $userchecked,
            'userparam' => $userparam
        ];
        return json_encode($data);
    }

    public function updateposuserxhr(Request $request)
    {
        $username = $request->input('username');
        $posData = [
            'posdiscountallowupto' => $request->input('posdiscountallowupto'),
            'possettlementyn' => $request->input('possettlementyn'),
            'editelementinkot' => $request->input('editelementinkot'),
            'freeitemallow' => $request->input('freeitemallow'),
            'refundcashcardamt' => $request->input('refundcashcardamt'),
        ];
        try {
            UserPermission::where('propertyid', $this->propertyid)->where('username', $username)->update($posData);
            return response()->json(['message' => 'POS user settings updated successfully'], 200);
        } catch (Exception $e) {
            return response()->json(['message' => 'Error: ' . $e->getMessage()]);
        }
    }

    public function userparamsubmit(Request $request)
    {
        $permission = revokeopen(122011);
        if (is_null($permission) || $permission->ins == 0) {
            return redirect()->back()->with('error', 'You have no permission to execute this functionality!');
        }
        $validate = $request->validate([
            'username' => 'required',
            'firms' => 'required',
            'sections' => 'required',
            'compcode' => 'required',
        ]);

        $compcodetmp = Companyreg::where('propertyid', $request->input('firms'))->first();
        $compcode = $request->input('compcode');
        $currentTime = $this->currenttime;
        $opt1 = $request->input('sections');
        $firmname = $request->input('firms');
        MenuHelp::where('propertyid', $request->input('firms'))->where('compcode', $request->input('compcode'))->where('username', $request->input('username'))
            ->where('opt1', $opt1)->delete();
        $menu = UserModule::where('propertyid', $this->propertyid)->where('opt1', $opt1)->whereNot('opt2', 0)->get();

        function createMenuHelp($request, $compcode, $opt1, $opt2, $opt3, $route, $module, $module_name, $view, $ins, $edit, $del, $print, $currentTime, $flag)
        {
            $menuhelpin = new MenuHelp();
            $menuhelpin->propertyid = $request->input('firms');
            $menuhelpin->username = $request->input('username');
            $menuhelpin->compcode = $compcode;
            $menuhelpin->opt1 = $opt1;
            $menuhelpin->opt2 = $opt2;
            $menuhelpin->opt3 = $opt3;
            $menuhelpin->code = sprintf("%02d%02d%02d", $opt1, $opt2, $opt3);
            $menuhelpin->route = $route;
            $menuhelpin->module = $module;
            $menuhelpin->module_name = $module_name;
            $menuhelpin->view = $view;
            $menuhelpin->ins = $ins;
            $menuhelpin->edit = $edit;
            $menuhelpin->del = $del;
            $menuhelpin->print = $print;
            $menuhelpin->flag = $flag;
            $menuhelpin->outletcode = '';
            $menuhelpin->u_name = Auth::user()->name;
            $menuhelpin->u_entdt = $currentTime;
            $menuhelpin->u_updatedt = null;
            $menuhelpin->save();
        }

        $mainmenu = MenuHelp::where('propertyid', $firmname)->where('username', Auth::user()->name)->where('opt1', $opt1)
            ->where('opt3', 0)->get();
        $uniqueentpoint = [];
        foreach ($mainmenu as $index => $menu) {
            if ($index == 0 && $menu->opt2 == 0 && $request->input('validatecheckbox') == 'checked') {
                createMenuHelp($request, $compcode, $menu->opt1, $menu->opt2, $menu->opt3, $menu->route, $menu->module, $menu->module_name, $menu->ins, $menu->view, $menu->edit, $menu->del, $menu->print, $currentTime, $menu->flag);
            }
            if ($request->has('view' . $menu->code)) {
                createMenuHelp($request, $compcode, $menu->opt1, $menu->opt2, $menu->opt3, $menu->route, $menu->module, $menu->module_name, $menu->view, $menu->ins, $menu->edit, $menu->del, $menu->print, $currentTime, $menu->flag);
                $entrymenu = MenuHelp::where('propertyid', $firmname)->where('username', Auth::user()->name)->where('opt1', $menu->opt1)
                    ->whereNot('opt3', 0)->get();
                foreach ($entrymenu as $entmenu) {
                    if ($request->has('view' . $entmenu->code) && !in_array($entmenu->code, $uniqueentpoint, true)) {
                        createMenuHelp($request, $compcode, $entmenu->opt1, $entmenu->opt2, $entmenu->opt3, $entmenu->route, $entmenu->module, $entmenu->module_name, $request->has('view' . $entmenu->code) == true ? 1 : 0, $request->has('insert' . $entmenu->code) == true ? 1 : 0, $request->has('edit' . $entmenu->code) == true ? 1 : 0, $request->has('delete' . $entmenu->code) == true ? 1 : 0, $request->has('print' . $entmenu->code) == true ? 1 : 0, $currentTime, $entmenu->flag);
                        $uniqueentpoint[] = $entmenu->code;
                    }
                }
            }
        }
        return back()->with('success', 'User Permission Updated Successfully');
    }
}
