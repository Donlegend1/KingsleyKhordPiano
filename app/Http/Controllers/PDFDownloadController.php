<?php

namespace App\Http\Controllers;

use App\Models\PDFDownload;
use App\Http\Requests\StorePDFDownloadRequest;
use App\Http\Requests\UpdatePDFDownloadRequest;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class PDFDownloadController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {

        return view('admin.pdf-download.index');
    }

    /**
     * Fetch all PDFs as JSON.
     */
    public function fetchAll()
    {
        try {
            $pdfs = PDFDownload::all();

            return response()->json($pdfs);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error fetching PDFs: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
     public function store(StorePDFDownloadRequest $request)
    {
        try {
            $data = $request->validated();
    
            // ---------- PDF upload ----------
            if ($request->hasFile('file_url')) {
    
                $pdf = $request->file('file_url');
    
                // New file name
                $pdfName = time() . '_' . $pdf->getClientOriginalName();
    
                // Destination
                $pdfDestination = base_path('../public_html/uploads/pdfs');
    
                // Create folder if missing
                if (!file_exists($pdfDestination)) {
                    mkdir($pdfDestination, 0755, true);
                }
    
                // Move file
                $pdf->move($pdfDestination, $pdfName);
    
                // Clean public path
                $data['file_url'] = "/uploads/pdfs/$pdfName";
            }
    
            // ---------- Thumbnail upload ----------
            if ($request->hasFile('thumbnail')) {
    
                $thumbnail = $request->file('thumbnail');
    
                // New file name
                $thumbnailName = time() . '_' . $thumbnail->getClientOriginalName();
    
                // Destination
                $thumbDestination = base_path('../public_html/uploads/pdf-thumbnails');
    
                // Create folder if missing
                if (!file_exists($thumbDestination)) {
                    mkdir($thumbDestination, 0755, true);
                }
    
                // Move file
                $thumbnail->move($thumbDestination, $thumbnailName);
    
                // Clean public path
                $data['thumbnail'] = "uploads/pdf-thumbnails/$thumbnailName";
            }
    
            // Create record
            PDFDownload::create($data);
    
            return response()->json([
                'success' => true,
                'message' => 'PDF created successfully',
            ], 201);
    
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error creating PDF: ' . $e->getMessage(),
            ], 500);
        }
    }


    /**
     * Download the PDF file.
     */
    public function download(PDFDownload $pDFDownload)
    {
        try {

        $filePath = $pDFDownload->file_url;
        
        if (file_exists($filePath)) {
            return response()->download($filePath);
        }
      

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error downloading PDF: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdatePDFDownloadRequest $request, PDFDownload $pDFDownload)
    {
        try {
            $data = $request->validated();

            // ---------- PDF upload ----------
            if ($request->hasFile('file_url')) {

                // Old PDF path
                if ($pDFDownload->file_url) {
                    $oldPdfPath = base_path('../public_html' . $pDFDownload->file_url);
                    if (file_exists($oldPdfPath)) {
                        unlink($oldPdfPath);
                    }
                }

                $pdf = $request->file('file_url');
                $pdfName = time() . '_' . $pdf->getClientOriginalName();

                $pdfDestination = base_path('../public_html/uploads/pdfs');

                if (!file_exists($pdfDestination)) {
                    mkdir($pdfDestination, 0755, true);
                }

                $pdf->move($pdfDestination, $pdfName);

                $data['file_url'] = "uploads/pdfs/$pdfName";
            }

            // ---------- Thumbnail upload ----------
            if ($request->hasFile('thumbnail')) {

                // Old thumbnail path
                if ($pDFDownload->thumbnail) {
                    $oldThumbPath = base_path('../public_html' . $pDFDownload->thumbnail);
                    if (file_exists($oldThumbPath)) {
                        unlink($oldThumbPath);
                    }
                }

                $thumbnail = $request->file('thumbnail');
                $thumbnailName = time() . '_' . $thumbnail->getClientOriginalName();

                $thumbDestination = base_path('../public_html/uploads/pdf-thumbnails');

                if (!file_exists($thumbDestination)) {
                    mkdir($thumbDestination, 0755, true);
                }

                $thumbnail->move($thumbDestination, $thumbnailName);

                $data['thumbnail'] = "/uploads/pdf-thumbnails/$thumbnailName";
            }

            // Update DB
            $pDFDownload->update($data);

            return response()->json([
                'success' => true,
                'message' => 'PDF updated successfully.',
                'data' => $pDFDownload,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error updating PDF: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(PDFDownload $pDFDownload)
    {
        try {
            // Delete the PDF file
            if ($pDFDownload->file_url && file_exists(public_path($pDFDownload->file_url))) {
                unlink(public_path($pDFDownload->file_url));
            }

            // Delete the thumbnail file
            if ($pDFDownload->thumbnail && file_exists(public_path($pDFDownload->thumbnail))) {
                unlink(public_path($pDFDownload->thumbnail));
            }

            // Delete the database record
            $pDFDownload->delete();

            return response()->json([
                'success' => true,
                'message' => 'PDF deleted successfully.',
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error deleting PDF: ' . $e->getMessage(),
            ], 500);
        }
    }
}
