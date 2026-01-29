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

            // Handle PDF file upload
            if ($request->hasFile('file_url')) {
                $pdfFile = $request->file('file_url');
                $pdfFileName = time() . '_' . $pdfFile->getClientOriginalName();
                $pdfFile->move(public_path('uploads/pdfs'), $pdfFileName);
                $data['file_url'] = 'uploads/pdfs/' . $pdfFileName;
            }

            // Handle thumbnail upload
            if ($request->hasFile('thumbnail')) {
                $thumbnailFile = $request->file('thumbnail');
                $thumbnailFileName = time() . '_' . $thumbnailFile->getClientOriginalName();
                $thumbnailFile->move(public_path('uploads/pdf-thumbnails'), $thumbnailFileName);
                $data['thumbnail'] = 'uploads/pdf-thumbnails/' . $thumbnailFileName;
            }

            // Create the PDF download record
            PDFDownload::create($data);

            return response()->json([
                'success' => true,
                'message' => 'PDF downloaded successfully created.',
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
            if (!$pDFDownload->file_url || !Storage::disk('public')->exists($pDFDownload->file_url)) {
                return response()->json([
                    'success' => false,
                    'message' => 'PDF file not found.',
                ], 404);
            }

            $filePath = $pDFDownload->file_url;
            $fileName = $pDFDownload->title . '.pdf';

            return Storage::disk('public')->download($filePath, $fileName);

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

            // Handle PDF file upload
            if ($request->hasFile('file_url')) {
                // Delete old file if exists
                if ($pDFDownload->file_url && file_exists(public_path($pDFDownload->file_url))) {
                    unlink(public_path($pDFDownload->file_url));
                }

                $pdfFile = $request->file('file_url');
                $pdfFileName = time() . '_' . $pdfFile->getClientOriginalName();
                $pdfFile->move(public_path('uploads/pdfs'), $pdfFileName);
                $data['file_url'] = 'uploads/pdfs/' . $pdfFileName;
            }

            // Handle thumbnail upload
            if ($request->hasFile('thumbnail')) {
                // Delete old thumbnail if exists
                if ($pDFDownload->thumbnail && file_exists(public_path($pDFDownload->thumbnail))) {
                    unlink(public_path($pDFDownload->thumbnail));
                }

                $thumbnailFile = $request->file('thumbnail');
                $thumbnailFileName = time() . '_' . $thumbnailFile->getClientOriginalName();
                $thumbnailFile->move(public_path('uploads/pdf-thumbnails'), $thumbnailFileName);
                $data['thumbnail'] = 'uploads/pdf-thumbnails/' . $thumbnailFileName;
            }

            // Update the PDF download record
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
