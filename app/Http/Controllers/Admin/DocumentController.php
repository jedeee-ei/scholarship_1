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
            $documents = json_decode($application->documents, true) ?? [];
            
            if (!isset($documents[$documentIndex])) {
                return response()->json(['error' => 'Document not found'], 404);
            }
            
            $documentPath = $documents[$documentIndex];
            
            // Check if file exists
            if (!Storage::disk('public')->exists($documentPath)) {
                return response()->json(['error' => 'File not found on server'], 404);
            }
            
            // Get the file
            $file = Storage::disk('public')->get($documentPath);
            $fileName = basename($documentPath);
            $mimeType = Storage::disk('public')->mimeType($documentPath);
            
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
            $documents = json_decode($application->documents, true) ?? [];
            
            if (!isset($documents[$documentIndex])) {
                return response()->json(['error' => 'Document not found'], 404);
            }
            
            $documentPath = $documents[$documentIndex];
            
            // Check if file exists
            if (!Storage::disk('public')->exists($documentPath)) {
                return response()->json(['error' => 'File not found on server'], 404);
            }
            
            // Get the file
            $file = Storage::disk('public')->get($documentPath);
            $mimeType = Storage::disk('public')->mimeType($documentPath);
            
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
            $documents = json_decode($application->documents, true) ?? [];
            
            if (!isset($documents[$documentIndex])) {
                return response()->json(['error' => 'Document not found'], 404);
            }
            
            $documentPath = $documents[$documentIndex];
            
            // Check if file exists
            if (!Storage::disk('public')->exists($documentPath)) {
                return response()->json(['error' => 'File not found on server'], 404);
            }
            
            $fileName = basename($documentPath);
            $fileSize = Storage::disk('public')->size($documentPath);
            $mimeType = Storage::disk('public')->mimeType($documentPath);
            $lastModified = Storage::disk('public')->lastModified($documentPath);
            
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
            $documents = json_decode($application->documents, true) ?? [];
            
            $documentList = [];
            foreach ($documents as $index => $documentPath) {
                if (Storage::disk('public')->exists($documentPath)) {
                    $documentList[] = [
                        'index' => $index,
                        'name' => basename($documentPath),
                        'size' => Storage::disk('public')->size($documentPath),
                        'type' => Storage::disk('public')->mimeType($documentPath),
                        'last_modified' => date('Y-m-d H:i:s', Storage::disk('public')->lastModified($documentPath)),
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
            $documents = json_decode($application->documents, true) ?? [];
            
            if (!isset($documents[$documentIndex])) {
                return response()->json(['error' => 'Document not found'], 404);
            }
            
            $documentPath = $documents[$documentIndex];
            
            // Delete file from storage
            if (Storage::disk('public')->exists($documentPath)) {
                Storage::disk('public')->delete($documentPath);
            }
            
            // Remove from documents array
            unset($documents[$documentIndex]);
            $documents = array_values($documents); // Re-index array
            
            // Update application
            $application->documents = json_encode($documents);
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
            $documents = json_decode($application->documents, true) ?? [];
            
            // Store the new document
            $file = $request->file('document');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $filePath = $file->storeAs('scholarship_documents', $fileName, 'public');
            
            // Add to documents array
            $documents[] = $filePath;
            
            // Update application
            $application->documents = json_encode($documents);
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
