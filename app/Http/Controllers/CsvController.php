<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;

class CsvController extends Controller
{
    function import_csv(Request $request)
    {
        if (Auth::check()) {
            $validated = $request->validate([
                'campaign_url' => 'required|file|mimes:csv,txt|max:2048'
            ]);
            if ($_FILES['campaign_url']['error'] === UPLOAD_ERR_OK) {
                $file = $request->file('campaign_url');
                $fileType = $file->getClientMimeType();
                if ($fileType == 'text/csv' || $fileType == 'application/vnd.ms-excel') {
                    $uploadDir = 'uploads/';
                    $uploadFilePath = $file->store($uploadDir);
                    if ($uploadFilePath) {
                        $fileHandle = fopen(storage_path('app/' . $uploadFilePath), 'r');
                        if ($fileHandle !== false) {
                            $csvData = [];
                            $delimiter = ',';
                            $enclosure = '"';
                            $escape = '\\';
                            $columnNames = fgetcsv($fileHandle, 0, $delimiter, $enclosure, $escape);
                            foreach ($columnNames as $colName) {
                                $csvData[$colName] = [];
                            }
                            while (($rowData = fgetcsv($fileHandle, 0, $delimiter, $enclosure, $escape)) !== false) {
                                foreach ($columnNames as $index => $colName) {
                                    $csvData[$colName][] = $rowData[$index] ?? null;
                                }
                            }
                            $urlCounts = 0;
                            $items = [];
                            foreach ($csvData as $key => $value) {
                                if (str_contains(strtolower($key), 'url')) {
                                    foreach ($value as $url) {
                                        if (filter_var($url, FILTER_VALIDATE_URL)) {
                                            $items[] = $url;
                                            ++$urlCounts;
                                        } else {
                                            return response()->json(['success' => false, 'message' => "No URL Found"]);
                                        }
                                    }
                                }
                            }
                            fclose($fileHandle);
                            return response()->json(['success' => true, 'total_leads' => $urlCounts, 'blacklist_leads' => 0, 'csv_data' => $items]);
                        } else {
                            return response()->json(['success' => false, 'message' => "No Data Found"]);
                        }
                    } else {
                        return response()->json(['success' => false, 'message' => "Error storing file"]);
                    }
                } else {
                    return response()->json(['success' => false, 'message' => "Invalid file type. Please upload a CSV file."]);
                }
            } else {
                return response()->json(['success' => false, 'message' => "Error uploading file"]);
            }
        } else {
            return redirect(url('/'));
        }
    }
}
