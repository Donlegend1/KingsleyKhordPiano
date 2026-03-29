<?php

namespace App\Http\Controllers;

use App\Models\PDFDownload;
use App\Http\Requests\StorePDFDownloadRequest;
use App\Http\Requests\UpdatePDFDownloadRequest;

class PDFDownloadController extends Controller
{
    /**
     * Resolve full server path from stored relative path
     */
    private function resolveFullPath(string $relativePath): string
    {
        return base_path('../public_html/' . ltrim($relativePath, '/'));
    }

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
            return response()->json(PDFDownload::all());
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
                $pdfName = time() . '_' . $pdf->getClientOriginalName();
                $pdfDestination = base_path('../public_html/uploads/pdfs');

                if (!file_exists($pdfDestination)) {
                    mkdir($pdfDestination, 0755, true);
                }

                $pdf->move($pdfDestination, $pdfName);

                // Consistent: no leading slash
                $data['file_url'] = "uploads/pdfs/$pdfName";
            }

            // ---------- Thumbnail upload ----------
            if ($request->hasFile('thumbnail')) {
                $thumbnail = $request->file('thumbnail');
                $thumbnailName = time() . '_' . $thumbnail->getClientOriginalName();
                $thumbDestination = base_path('../public_html/uploads/pdf-thumbnails');

                if (!file_exists($thumbDestination)) {
                    mkdir($thumbDestination, 0755, true);
                }

                $thumbnail->move($thumbDestination, $thumbnailName);

                // Consistent: no leading slash
                $data['thumbnail'] = "uploads/pdf-thumbnails/$thumbnailName";
            }

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
            $fullPath = $this->resolveFullPath($pDFDownload->file_url);

            if (file_exists($fullPath)) {
                return response()->download($fullPath);
            }

            return response()->json([
                'success' => false,
                'message' => 'File not found',
            ], 404);

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

                // Delete old file
                if ($pDFDownload->file_url) {
                    $oldPath = $this->resolveFullPath($pDFDownload->file_url);
                    if (file_exists($oldPath)) {
                        unlink($oldPath);
                    }
                }

                $pdf = $request->file('file_url');
                $pdfName = time() . '_' . $pdf->getClientOriginalName();
                $pdfDestination = base_path('../public_html/uploads/pdfs');

                if (!file_exists($pdfDestination)) {
                    mkdir($pdfDestination, 0755, true);
                }

                $pdf->move($pdfDestination, $pdfName);

                // Consistent: no leading slash
                $data['file_url'] = "uploads/pdfs/$pdfName";
            }

            // ---------- Thumbnail upload ----------
            if ($request->hasFile('thumbnail')) {

                // Delete old thumbnail
                if ($pDFDownload->thumbnail) {
                    $oldThumbPath = $this->resolveFullPath($pDFDownload->thumbnail);
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

                // Consistent: no leading slash
                $data['thumbnail'] = "uploads/pdf-thumbnails/$thumbnailName";
            }

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
            if ($pDFDownload->file_url) {
                $pdfPath = $this->resolveFullPath($pDFDownload->file_url);
                if (file_exists($pdfPath)) {
                    unlink($pdfPath);
                }
            }

            if ($pDFDownload->thumbnail) {
                $thumbPath = $this->resolveFullPath($pDFDownload->thumbnail);
                if (file_exists($thumbPath)) {
                    unlink($thumbPath);
                }
            }

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