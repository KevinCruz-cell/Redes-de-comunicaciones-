<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

class PostgresLogController extends Controller
{
    public function index()
    {
        $logPath = 'C:\\Program Files\\PostgreSQL\\17\\data\\log';

        if (!File::exists($logPath)) {
            return view('logs.postgresql', [
                'error' => 'La carpeta de logs no existe o no se puede leer.',
                'files' => [],
                'selectedFile' => null,
                'content' => null,
            ]);
        }

        $files = collect(File::files($logPath))
            ->filter(function ($file) {
                return strtolower($file->getExtension()) === 'log';
            })
            ->sortByDesc(function ($file) {
                return $file->getMTime();
            })
            ->values();

        $selectedFile = request('file');

        if (!$selectedFile && $files->count() > 0) {
            $selectedFile = $files[0]->getFilename();
        }

        $content = null;
        $error = null;

        if ($selectedFile) {
            $fullPath = $logPath . DIRECTORY_SEPARATOR . $selectedFile;

            if (File::exists($fullPath)) {
                $content = File::get($fullPath);
            } else {
                $error = 'El archivo seleccionado no existe.';
            }
        }

        return view('logs.postgresql', [
            'error' => $error,
            'files' => $files,
            'selectedFile' => $selectedFile,
            'content' => $content,
        ]);
    }
}
