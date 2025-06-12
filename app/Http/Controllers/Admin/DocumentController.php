<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ScholarshipApplication;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class DocumentController extends Controller
{
    /**
     * Download document from application
     */
    public function downloadDocument($applicationId, $documentIndex)
    {
        try {
            $application = ScholarshipApplication::where('application_id', $applicationId)->firstOrFail();

            // Get documents array
            $documents = $application->documents ?? [];

            if (!isset($documents[$documentIndex])) {
                return response()->json(['error' => 'Document not found'], 404);
            }

            $document = $documents[$documentIndex];

            // Handle both old format (string path) and new format (array with metadata)
            if (is_string($document)) {
                $documentPath = $document;
                $fileName = basename($documentPath);
                $mimeType = 'application/octet-stream'; // Default mime type
            } else {
                $documentPath = $document['path'] ?? '';
                $fileName = $document['original_name'] ?? basename($documentPath);
                $mimeType = $document['mime_type'] ?? 'application/octet-stream';
            }

            // Check if file exists
            if (!Storage::disk('local')->exists($documentPath)) {
                return response()->json(['error' => 'File not found on server'], 404);
            }

            // Get the file
            $file = Storage::disk('local')->get($documentPath);

            return response($file)
                ->header('Content-Type', $mimeType)
                ->header('Content-Disposition', 'attachment; filename="' . $fileName . '"');

        } catch (\Exception $e) {
            Log::error('Document download error: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to download document'], 500);
        }
    }

    /**
     * View document from application
     */
    public function viewDocument($applicationId, $documentIndex)
    {
        try {
            $application = ScholarshipApplication::where('application_id', $applicationId)->firstOrFail();

            // Get documents array
            $documents = $application->documents ?? [];

            if (!isset($documents[$documentIndex])) {
                return response()->json(['error' => 'Document not found'], 404);
            }

            $document = $documents[$documentIndex];

            // Handle both old format (string path) and new format (array with metadata)
            if (is_string($document)) {
                $documentPath = $document;
                $mimeType = 'application/octet-stream'; // Default mime type
            } else {
                $documentPath = $document['path'] ?? '';
                $mimeType = $document['mime_type'] ?? 'application/octet-stream';
            }

            // Check if file exists
            if (!Storage::disk('local')->exists($documentPath)) {
                return response()->json(['error' => 'File not found on server'], 404);
            }

            // Get the file
            $file = Storage::disk('local')->get($documentPath);

            return response($file)
                ->header('Content-Type', $mimeType)
                ->header('Content-Disposition', 'inline');

        } catch (\Exception $e) {
            Log::error('Document view error: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to view document'], 500);
        }
    }

    /**
     * Get document info
     */
    public function getDocumentInfo($applicationId, $documentIndex)
    {
        try {
            $application = ScholarshipApplication::where('application_id', $applicationId)->firstOrFail();

            // Get documents array
            $documents = $application->documents ?? [];

            if (!isset($documents[$documentIndex])) {
                return response()->json(['error' => 'Document not found'], 404);
            }

            $document = $documents[$documentIndex];

            // Handle both old format (string path) and new format (array with metadata)
            if (is_string($document)) {
                $documentPath = $document;
                $fileName = basename($documentPath);
                $mimeType = 'application/octet-stream';
                $fileSize = Storage::disk('local')->exists($documentPath) ? Storage::disk('local')->size($documentPath) : 0;
            } else {
                $documentPath = $document['path'] ?? '';
                $fileName = $document['original_name'] ?? basename($documentPath);
                $mimeType = $document['mime_type'] ?? 'application/octet-stream';
                $fileSize = $document['size'] ?? (Storage::disk('local')->exists($documentPath) ? Storage::disk('local')->size($documentPath) : 0);
            }

            // Check if file exists
            if (!Storage::disk('local')->exists($documentPath)) {
                return response()->json(['error' => 'File not found on server'], 404);
            }

            $lastModified = Storage::disk('local')->lastModified($documentPath);

            return response()->json([
                'success' => true,
                'document' => [
                    'name' => $fileName,
                    'size' => $fileSize,
                    'type' => $mimeType,
                    'last_modified' => date('Y-m-d H:i:s', $lastModified),
                    'path' => $documentPath
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Document info error: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to get document info'], 500);
        }
    }

    /**
     * List all documents for an application
     */
    public function listDocuments($applicationId)
    {
        try {
            $application = ScholarshipApplication::where('application_id', $applicationId)->firstOrFail();

            // Get documents array
            $documents = $application->documents ?? [];

            $documentList = [];
            foreach ($documents as $index => $document) {
                // Handle both old format (string path) and new format (array with metadata)
                if (is_string($document)) {
                    $documentPath = $document;
                    $fileName = basename($documentPath);
                    $mimeType = 'application/octet-stream';
                    $fileSize = Storage::disk('local')->exists($documentPath) ? Storage::disk('local')->size($documentPath) : 0;
                } else {
                    $documentPath = $document['path'] ?? '';
                    $fileName = $document['original_name'] ?? basename($documentPath);
                    $mimeType = $document['mime_type'] ?? 'application/octet-stream';
                    $fileSize = $document['size'] ?? (Storage::disk('local')->exists($documentPath) ? Storage::disk('local')->size($documentPath) : 0);
                }

                if (Storage::disk('local')->exists($documentPath)) {
                    $documentList[] = [
                        'index' => $index,
                        'name' => $fileName,
                        'size' => $fileSize,
                        'type' => $mimeType,
                        'last_modified' => date('Y-m-d H:i:s', Storage::disk('local')->lastModified($documentPath)),
                        'download_url' => route('admin.application.document.download', ['application' => $applicationId, 'document' => $index]),
                        'view_url' => route('admin.application.document.view', ['application' => $applicationId, 'document' => $index])
                    ];
                }
            }
            
            return response()->json([
                'success' => true,
                'documents' => $documentList,
                'total' => count($documentList)
            ]);
                
        } catch (\Exception $e) {
            Log::error('Document list error: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to list documents'], 500);
        }
    }

    /**
     * Delete document from application
     */
    public function deleteDocument($applicationId, $documentIndex)
    {
        try {
            $application = ScholarshipApplication::where('application_id', $applicationId)->firstOrFail();

            // Get documents array
            $documents = $application->documents ?? [];

            if (!isset($documents[$documentIndex])) {
                return response()->json(['error' => 'Document not found'], 404);
            }

            $document = $documents[$documentIndex];

            // Handle both old format (string path) and new format (array with metadata)
            if (is_string($document)) {
                $documentPath = $document;
            } else {
                $documentPath = $document['path'] ?? '';
            }

            // Delete file from storage
            if (Storage::disk('local')->exists($documentPath)) {
                Storage::disk('local')->delete($documentPath);
            }

            // Remove from documents array
            unset($documents[$documentIndex]);
            $documents = array_values($documents); // Re-index array

            // Update application
            $application->documents = $documents;
            $application->save();

            Log::info('Document deleted', [
                'application_id' => $applicationId,
                'document_index' => $documentIndex,
                'document_path' => $documentPath
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Document deleted successfully'
            ]);

        } catch (\Exception $e) {
            Log::error('Document delete error: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to delete document'], 500);
        }
    }

    /**
     * Upload additional document to application
     */
    public function uploadDocument(Request $request, $applicationId)
    {
        $request->validate([
            'document' => 'required|file|mimes:pdf,jpg,jpeg,png|max:5120' // 5MB max
        ]);

        try {
            $application = ScholarshipApplication::where('application_id', $applicationId)->firstOrFail();

            // Get current documents array
            $documents = $application->documents ?? [];

            // Store the new document
            $file = $request->file('document');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $filePath = $file->storeAs('scholarship_documents/' . $applicationId, $fileName, 'local');

            // Add document info to array (using new format)
            $documents[] = [
                'original_name' => $file->getClientOriginalName(),
                'filename' => $fileName,
                'path' => $filePath,
                'size' => $file->getSize(),
                'mime_type' => $file->getMimeType(),
                'uploaded_at' => now()->toISOString()
            ];

            // Update application
            $application->documents = $documents;
            $application->save();

            Log::info('Document uploaded', [
                'application_id' => $applicationId,
                'file_path' => $filePath,
                'original_name' => $file->getClientOriginalName()
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Document uploaded successfully',
                'document' => [
                    'index' => count($documents) - 1,
                    'name' => $fileName,
                    'path' => $filePath
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Document upload error: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to upload document'], 500);
        }
    }
}
