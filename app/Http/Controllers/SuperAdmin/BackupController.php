<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\File;
use ZipArchive;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BackupController extends Controller
{
    protected $username;

    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            if (!isset(Auth::user()->name)) {
                return redirect('/');
            }

            return $next($request);
        });
    }

    public function index()
    {
        return view('admin.backups');
    }

    public function downloadStorage(Request $request)
    {
        $this->cleanOldBackups();
        $zipFileName = 'storage_backup_' . date('Y-m-d_H-i-s') . '.zip';
        $zipPath = storage_path($zipFileName);

        $zip = new ZipArchive;

        if ($zip->open($zipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) === TRUE) {
            $files = File::allFiles(storage_path());

            foreach ($files as $file) {
                $relativePath = str_replace(storage_path() . '/', '', $file->getPathname());
                $zip->addFile($file->getPathname(), $relativePath);
            }

            $zip->close();
        } else {
            return response()->json(['status' => 'error', 'message' => 'Could not create zip file']);
        }

        return response()->json([
            'status' => 'success',
            'url' => url('superadmin/download-temp-zip/' . $zipFileName)
        ]);
    }

    public function downloadTempZip($filename)
    {
        $filePath = storage_path($filename);

        if (file_exists($filePath)) {
            return response()->download($filePath)->deleteFileAfterSend(true);
        }

        abort(404);
    }

    public function downloadDatabaseBackup(Request $request)
    {
        $this->cleanOldBackups();
        $dbName = env('DB_DATABASE');
        $dbUser = env('DB_USERNAME');
        $dbHost = env('DB_HOST');
        $dbPass = $request->password; // use password from AJAX

        if (!$dbPass) {
            return response()->json(['status' => 'error', 'message' => 'Database password is required']);
        }

        $fileName = 'database_backup_' . date('Y-m-d_H-i-s') . '.sql';
        $zipFileName = 'database_backup_' . date('Y-m-d_H-i-s') . '.zip';
        $filePath = storage_path($fileName);
        $zipPath = storage_path($zipFileName);

        // Create database dump
        $command = "mysqldump -h {$dbHost} -u {$dbUser} -p{$dbPass} {$dbName} > {$filePath}";
        exec($command, $output, $returnVar);

        if ($returnVar !== 0) {
            return response()->json(['status' => 'error', 'message' => 'mysqldump failed. Please check password or database connection']);
        }

        // Zip the SQL file
        $zip = new \ZipArchive;
        if ($zip->open($zipPath, \ZipArchive::CREATE | \ZipArchive::OVERWRITE) === TRUE) {
            $zip->addFile($filePath, $fileName);
            $zip->close();
        }

        // Delete raw SQL file
        if (file_exists($filePath)) unlink($filePath);

        return response()->json([
            'status' => 'success',
            'url' => url('superadmin/download-temp-zip/' . $zipFileName)
        ]);
    }


    private function cleanOldBackups($folder = null, $hours = 1)
    {
        $folder = $folder ?? storage_path();
        $files = File::files($folder);

        $now = time();
        foreach ($files as $file) {
            if ($file->getExtension() === 'zip') {
                $fileTime = filemtime($file->getRealPath());
                if (($now - $fileTime) > ($hours * 3600)) {
                    @unlink($file->getRealPath());
                }
            }
        }
    }
}
