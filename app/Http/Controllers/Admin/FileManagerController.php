<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class FileManagerController extends Controller
{
    public function index(Request $request)
    {
        $type = $request->get('type', 'image'); // image, file, document
        $baseFolder = 'uploads/' . ($type == 'image' ? 'images' : 'documents');
        
        $currentFolder = $request->get('folder', $baseFolder);
        
        // Ensure within bounds constraint for security
        if (!str_starts_with($currentFolder, 'uploads')) {
            $currentFolder = $baseFolder;
        }

        $disk = Storage::disk('public');
        if (!$disk->exists($currentFolder)) {
            $disk->makeDirectory($currentFolder);
        }

        $directories = collect($disk->directories($currentFolder))->map(function($dir) use ($disk) {
            return [
                'name' => basename($dir),
                'path' => $dir,
                'count' => count($disk->files($dir)) + count($disk->directories($dir)),
                'last_modified' => date('Y-m-d H:i', $disk->lastModified($dir))
            ];
        });

        $files = collect($disk->files($currentFolder))->map(function($file) use ($disk) {
            return [
                'name' => basename($file),
                'path' => $file,
                'url' => asset('storage/' . $file),
                'size' => $this->formatSizeUnits($disk->size($file)),
                'last_modified' => date('Y-m-d H:i', $disk->lastModified($file)),
                'extension' => pathinfo($file, PATHINFO_EXTENSION)
            ];
        });

        // Breadcrumb logic
        $breadcrumbs = [];
        $parts = explode('/', $currentFolder);
        $path = '';
        foreach ($parts as $part) {
            $path .= ($path == '' ? '' : '/') . $part;
            $breadcrumbs[] = [
                'name' => $part,
                'path' => $path
            ];
        }

        return view('admin.file_manager.index', compact('directories', 'files', 'currentFolder', 'type', 'breadcrumbs', 'baseFolder'));
    }

    public function upload(Request $request)
    {
        $request->validate([
            'file' => 'required|file|max:10240', // 10MB
            'folder' => 'required|string'
        ]);

        $folder = $request->post('folder');
        if (!str_starts_with($folder, 'uploads')) {
            return response()->json(['error' => 'Invalid folder path'], 403);
        }

        $file = $request->file('file');
        
        // Security check
        $extension = strtolower($file->getClientOriginalExtension());
        $allowed = ['jpg','jpeg','png','webp','pdf','docx','doc','xls','xlsx','zip','rar'];
        if (!in_array($extension, $allowed)) {
            return response()->json(['error' => 'File type not allowed! Security validation failed.'], 403);
        }

        // Store file with original string representation or generate clean name
        $cleanName = preg_replace('/[\/\\\\\?%*:|"<>]/', '_', $file->getClientOriginalName());
        $name = time() . '_' . $cleanName;
        $path = $file->storeAs($folder, $name, 'public');

        return response()->json([
            'success' => true,
            'url' => asset('storage/' . $path),
            'path' => $path,
            'name' => $name
        ]);
    }
    
    public function createFolder(Request $request)
    {
        $folder = $request->post('folder');
        $name = $request->post('name');
        
        if (!str_starts_with($folder, 'uploads')) {
             return response()->json(['success' => false, 'message' => 'Invalid folder path']);
        }
        
        $cleanName = preg_replace('/[\/\\\\\?%*:|"<>]/', '_', $name);
        Storage::disk('public')->makeDirectory($folder . '/' . $cleanName);
        
        return response()->json(['success' => true, 'message' => 'تم إنشاء المجلد بنجاح']);
    }

    public function rename(Request $request)
    {
        $path = $request->post('path');
        $newName = $request->post('name');
        $type = $request->post('type'); // file or folder

        if (!str_starts_with($path, 'uploads')) {
             return response()->json(['success' => false, 'message' => 'Invalid file path']);
        }

        $directory = dirname($path);
        $cleanName = preg_replace('/[\/\\\\\?%*:|"<>]/', '_', $newName);
        
        if ($type == 'file') {
             // Keep original extension
             $ext = pathinfo($path, PATHINFO_EXTENSION);
             if (!str_ends_with($cleanName, '.' . $ext)) {
                 $cleanName .= '.' . $ext;
             }
        }

        Storage::disk('public')->move($path, $directory . '/' . $cleanName);
        
        return response()->json(['success' => true, 'message' => 'تمت إعادة التسمية بنجاح']);
    }

    public function delete(Request $request)
    {
        $path = $request->post('path');
        $type = $request->post('type'); // file or folder

        if (!str_starts_with($path, 'uploads')) {
             return response()->json(['success' => false, 'message' => 'Invalid file path']);
        }

        if ($type == 'folder') {
            Storage::disk('public')->deleteDirectory($path);
        } else {
            Storage::disk('public')->delete($path);
        }
        
        return response()->json(['success' => true, 'message' => 'تم الحذف بنجاح']);
    }

    private function formatSizeUnits($bytes)
    {
        if ($bytes >= 1073741824) {
            $bytes = number_format($bytes / 1073741824, 2) . ' GB';
        } elseif ($bytes >= 1048576) {
            $bytes = number_format($bytes / 1048576, 2) . ' MB';
        } elseif ($bytes >= 1024) {
            $bytes = number_format($bytes / 1024, 2) . ' KB';
        } elseif ($bytes > 1) {
            $bytes = $bytes . ' bytes';
        } elseif ($bytes == 1) {
            $bytes = $bytes . ' byte';
        } else {
            $bytes = '0 bytes';
        }
        return $bytes;
    }
}
