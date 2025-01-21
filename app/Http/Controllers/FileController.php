<?php

namespace App\Http\Controllers;

use App\Helpers\FileUploadHelper;
use App\Http\Requests\File\FileStoreRequest;
use App\Http\Requests\File\FileUpdateRequest;
use App\Http\Resources\FileResource;
use App\Models\File;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class FileController extends Controller
{
    // Display a listing of the resource.
    public function index(Request $request): JsonResponse
    {
        $request->validate([
            'student_id' => 'required_without:session_id',
            'file_type' => 'nullable',
            'session_id' => 'required_without:student_id'
        ]);

        $query = File::query();

        if ($request->file_type === 'profile') {
            $file = $query->where([
                ['student_id', $request->student_id],
                ['file_type', 'profile']
            ])
                ->latest()
                ->first();

            if (!$file) {
                return $this->sendErrorResponse('No profile picture found for the given student.', 404);
            }

            return $this->sendSuccessResponse(
                'Profile picture retrieved successfully',
                FileResource::make($file)
            );
        }


        if ($request->has('student_id')) {
            $query->where('student_id', $request->student_id);
        }

        if ($request->has('file_type')) {
            $query->where('file_type', $request->file_type);
        }

        $results = $this->handleApiRequest($request, $query);

        $results = collect($results);
        if ($results->isEmpty()) {
            return $this->sendErrorResponse('No documents found', 404);
        }

        return $this->sendSuccessResponse('Documents retrieved successfully', $results);


    }

    // Store a newly created resource in storage.

    /**
     * @throws Exception
     */
    public function store(FileStoreRequest $request): JsonResponse
    {
        $validatedData = $request->validated();
        $authId = auth()->id();
        $data = [];
        if ($request->hasFile('files')) {
            foreach ($request->file('files') as $file) {
                FileUploadHelper::uploadFile($file, $validatedData['file_type'], $validatedData['student_id']);

                $data = File::create([
                    'user_id' => $authId,
                    'student_id' => $validatedData['student_id'],
                    'file_name' => FileUploadHelper::getFileName(),
                    'file_path' => FileUploadHelper::getFilePath(),
                    'file_type' => $validatedData['file_type'],
                ]);
                $data->save();
            }
            return $this->sendSuccessResponse(
                $validatedData['file_type'] . ' documents uploaded successfully',
                FileResource::make($data)
            );
        }

        // Check if the request contains base64 encoded images
        if (count($validatedData['base64_files']) > 0) {
            foreach ($validatedData['base64_files'] as $base64File) {
                // Assuming FileUploadHelper is adjusted to support base64 files
                if (FileUploadHelper::isValidBase64($base64File)) {
                    FileUploadHelper::uploadFileFromBase64($base64File, $validatedData['file_type'], $authId);

                    $data = File::create([
                        'user_id' => $authId,
                        'student_id' => $validatedData['student_id'],
                        'file_name' => FileUploadHelper::getFileName(),
                        'file_path' => FileUploadHelper::getFilePath(),
                        'file_type' => $validatedData['file_type'],
                    ]);
                    $data->save();
                }
            }
            return $this->sendSuccessResponse(
                $validatedData['file_type'] . ' documents uploaded successfully',
                FileResource::make($data)
            );
        }

        return $this->sendErrorResponse('No file uploaded', Response::HTTP_BAD_REQUEST);
    }

    // Download the specified resource.
    public function download(File $file, Request $request)
    {
        $filePath = storage_path('app/public/' . $file->file_path);
        if (file_exists($filePath)) {
            return response()->download($filePath);
        }

        abort(404);
    }

    // Remove the specified resource from storage.
    public function destroy(File $file): JsonResponse
    {
        $file->delete();
        FileUploadHelper::deleteFile($file->file_path);

        return $this->sendSuccessResponse('Document deleted successfully', Response::HTTP_NO_CONTENT);
    }
}
