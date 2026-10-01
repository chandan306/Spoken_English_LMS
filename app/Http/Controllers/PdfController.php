<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Document;
use Illuminate\Support\Str;

class PdfController extends Controller
{
    public function upload(Request $request)
    {
        $request->validate([
            'pdfs' => 'required|array|max:10',
            'pdfs.*' => 'required|file|mimes:pdf|max:2048',
        ]);

        if ($request->hasFile('pdfs')) {

            foreach ($request->file('pdfs') as $file) {

                $filename = Str::uuid().'.pdf';

                $path = $file->storeAs('pdfs', $filename, 'local');

                abort_if(!$path, 500, 'The PDF could not be stored.');

                Document::create([
                    'file_name' => $file->getClientOriginalName(),
                    'file_path' => $path,
                ]);
            }
        }

        return back()->with('success', 'PDF files uploaded successfully!');
    }
}
