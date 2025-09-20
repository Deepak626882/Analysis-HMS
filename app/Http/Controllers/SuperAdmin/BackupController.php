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

            if ($file->getExtension() === 'sql') {
                $fileTime = filemtime($file->getRealPath());
                if (($now - $fileTime) > ($hours * 3600)) {
                    @unlink($file->getRealPath());
                }
            }
        }
    }

    public function verifyDatabase(Request $request)
    {
        if (!$request->hasFile('backup_file')) {
            return response()->json(['status' => 'error', 'message' => 'No file uploaded']);
        }

        $file = $request->file('backup_file');
        $zip = new ZipArchive;
        $tmpPath = storage_path('app/tmp_db_' . time());
        mkdir($tmpPath);

        if ($zip->open($file->getRealPath()) === true) {
            $zip->extractTo($tmpPath);
            $zip->close();

            $sqlFile = collect(scandir($tmpPath))
                ->filter(fn($f) => str_ends_with($f, '.sql'))
                ->first();

            if (!$sqlFile) {
                File::deleteDirectory($tmpPath);
                return response()->json(['status' => 'error', 'message' => 'No SQL file found in backup.']);
            }

            $sqlContent = file_get_contents($tmpPath . '/' . $sqlFile);
            $enviroData = [];
            $paychargeLookup = [];

            // Initialize debug info
            $debugInfo = [
                'enviro_found' => 0,
                'paycharge_found' => 0,
                'sample_enviro_values' => [],
                'sample_paycharge_values' => [],
                'paycharge_keys' => [],
                'paycharge_regex_attempts' => 0
            ];

            // Debug: Look for paycharge in the content
            $paychargePos = strpos($sqlContent, 'paycharge');
            if ($paychargePos !== false) {
                $debugInfo['paycharge_context'] = substr($sqlContent, max(0, $paychargePos - 50), 200);
            } else {
                $debugInfo['paycharge_context'] = 'paycharge table not found in SQL content';
            }

            // --- Parse enviro_general ---
            if (preg_match_all("/INSERT\s+INTO\s+`?enviro_general`?\s+VALUES\s*(.+?);/ims", $sqlContent, $matches)) {
                foreach ($matches[1] as $valuesBlock) {
                    // Extract all value tuples using regex
                    if (preg_match_all("/\(([^)]+(?:\([^)]*\)[^)]*)*)\)/", $valuesBlock, $tupleMatches)) {
                        foreach ($tupleMatches[1] as $tuple) {
                            $values = $this->parseSqlTuple($tuple);

                            if (count($values) >= 2) {
                                $propertyid = $values[0];
                                $ncur = $values[1];

                                // Remove quotes and clean up
                                $propertyid = trim($propertyid, "'\"");
                                $ncur = trim($ncur, "'\"");

                                if ($propertyid && $ncur && $propertyid !== 'NULL' && $ncur !== 'NULL') {
                                    $enviroData[] = [
                                        'propertyid' => $propertyid,
                                        'ncur' => substr($ncur, 0, 10), // Take only date part
                                        'paycharge_count' => 0
                                    ];

                                    // Debug: Store first few samples
                                    if (count($debugInfo['sample_enviro_values']) < 3) {
                                        $debugInfo['sample_enviro_values'][] = [
                                            'propertyid' => $propertyid,
                                            'ncur' => substr($ncur, 0, 10)
                                        ];
                                    }
                                    $debugInfo['enviro_found']++;
                                }
                            }
                        }
                    }
                }
            }

            // --- Parse paycharge ---
            // Try multiple regex patterns to handle different INSERT formats
            $pcMatches = [];

            // Pattern 1: Standard format
            if (preg_match_all("/INSERT\s+INTO\s+`?paycharge`?\s+VALUES\s*(.+?);/ims", $sqlContent, $matches1)) {
                $pcMatches = array_merge($pcMatches, $matches1[1]);
            }

            // Pattern 2: Multiline format (VALUES on new line)
            if (preg_match_all("/INSERT\s+INTO\s+`?paycharge`?\s+VALUES\s*\r?\n\s*(.+?)(?=\r?\n\s*(?:INSERT|CREATE|DROP|ALTER|\/\*|$))/ims", $sqlContent, $matches2)) {
                $pcMatches = array_merge($pcMatches, $matches2[1]);
            }

            // Pattern 3: Look for any paycharge data between parentheses after VALUES
            if (preg_match_all("/INSERT\s+INTO\s+`?paycharge`?\s+VALUES\s*(.+?)(?=(?:\r?\n){2,}|CREATE|INSERT|$)/ims", $sqlContent, $matches3)) {
                $pcMatches = array_merge($pcMatches, $matches3[1]);
            }

            $debugInfo['paycharge_regex_attempts'] = count($pcMatches);

            if (!empty($pcMatches)) {
                foreach ($pcMatches as $valuesBlock) {
                    if (preg_match_all("/\(([^)]+(?:\([^)]*\)[^)]*)*)\)/", $valuesBlock, $tupleMatches)) {
                        foreach ($tupleMatches[1] as $tuple) {
                            $values = $this->parseSqlTuple($tuple);

                            if (count($values) >= 9) {
                                $pcPropertyId = trim($values[1], "'\""); // propertyid at index 1
                                $vdate = trim($values[8], "'\""); // vdate at index 8

                                if ($pcPropertyId && $vdate && $pcPropertyId !== 'NULL' && $vdate !== 'NULL') {
                                    $vdate = substr($vdate, 0, 10); // Take only date part
                                    $key = $pcPropertyId; // Match by propertyid only, ignore date
                                    $paychargeLookup[$key] = ($paychargeLookup[$key] ?? 0) + 1;

                                    // Debug: Store first few samples
                                    if (count($debugInfo['sample_paycharge_values']) < 3) {
                                        $debugInfo['sample_paycharge_values'][] = [
                                            'propertyid' => $pcPropertyId,
                                            'vdate' => $vdate,
                                            'key' => $key
                                        ];
                                    }
                                    $debugInfo['paycharge_found']++;
                                }
                            }
                        }
                    }
                }
            }

            // Store some paycharge keys for debugging
            $debugInfo['paycharge_keys'] = array_slice(array_keys($paychargeLookup), 0, 10);
            $debugInfo['total_paycharge_keys'] = count($paychargeLookup);

            // Merge counts
            $matched = 0;
            foreach ($enviroData as &$row) {
                $key = $row['propertyid']; // Match by propertyid only
                if (isset($paychargeLookup[$key])) {
                    $row['paycharge_count'] = $paychargeLookup[$key];
                    $matched++;
                }
            }
            $debugInfo['matched_records'] = $matched;

            // Sort by date descending
            usort($enviroData, fn($a, $b) => strtotime($b['ncur']) <=> strtotime($a['ncur']));

            File::deleteDirectory($tmpPath);

            return response()->json([
                'status' => 'success',
                'data' => $enviroData,
                'debug' => $debugInfo
            ]);
        }

        return response()->json(['status' => 'error', 'message' => 'Failed to open ZIP file.']);
    }

    /**
     * Parse SQL tuple values, handling quotes and commas properly
     */
    private function parseSqlTuple($tuple)
    {
        $values = [];
        $current = '';
        $inQuotes = false;
        $quoteChar = '';
        $i = 0;

        while ($i < strlen($tuple)) {
            $char = $tuple[$i];

            if (!$inQuotes) {
                if ($char === '"' || $char === "'") {
                    $inQuotes = true;
                    $quoteChar = $char;
                    $current .= $char;
                } elseif ($char === ',') {
                    $values[] = trim($current);
                    $current = '';
                } else {
                    $current .= $char;
                }
            } else {
                if ($char === $quoteChar) {
                    // Check if it's escaped (doubled quote)
                    if ($i + 1 < strlen($tuple) && $tuple[$i + 1] === $quoteChar) {
                        $current .= $char . $char;
                        $i++; // Skip the next quote
                    } else {
                        $inQuotes = false;
                        $current .= $char;
                    }
                } else {
                    $current .= $char;
                }
            }
            $i++;
        }

        // Add the last value
        if ($current !== '') {
            $values[] = trim($current);
        }

        return $values;
    }
}
