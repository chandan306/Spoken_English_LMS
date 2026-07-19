<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PdfController extends Controller
{
    public function upload(Request $request)
    {
        $request->validate([
            'pdfs' => 'required|array',
            'pdfs.*' => 'required|mimes:pdf|max:2048', // 2 MB each
        ]);

        $uploadedFiles = [];

        if ($request->hasFile('pdfs')) {

            foreach ($request->file('pdfs') as $file) {

                $filename = time().'_'.$file->getClientOriginalName();

                $path = $file->storeAs('pdfs', $filename, 'public');

                $uploadedFiles[] = $path;
            }
        }

        return back()->with('success', 'PDF files uploaded successfully!');
    }
}
