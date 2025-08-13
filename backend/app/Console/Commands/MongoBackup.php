<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;
use Exception;

class MongoBackup extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'mongo:backup {--cleanup : Limpiar backups antiguos} {--days=7 : Días de retención}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Crear backup de MongoDB Atlas con retención automática';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        try {
            $retentionDays = (int) $this->option('days');
            
            $this->info('🚀 Iniciando backup de MongoDB Atlas...');
            
            // Crear directorio de backups si no existe
            $backupDir = storage_path('app/backups');
            if (!is_dir($backupDir)) {
                mkdir($backupDir, 0755, true);
            }
            
            // Generar nombre del archivo con timestamp
            $timestamp = Carbon::now()->format('Y-m-d_H-i-s');
            $filename = "trailynsafe_backup_{$timestamp}.gz";
            $backupPath = $backupDir . '/' . $filename;
            
            // Obtener URI de MongoDB desde configuración
            $mongoUri = config('database.connections.mongodb.dsn') ?: env('MONGO_DSN');
            
            if (!$mongoUri) {
                $this->error('❌ No se encontró configuración de MongoDB (MONGO_DSN)');
                return Command::FAILURE;
            }
            
            // Comando mongodump
            $this->info("📦 Creando backup: {$filename}");
            
            // Para Windows, verificar si mongodump está disponible
            $mongodumpPath = $this->findMongodump();
            
            if (!$mongodumpPath) {
                $this->error('❌ mongodump no encontrado. Usando backup alternativo...');
                return $this->createAlternativeBackup($filename, $backupPath);
            }
            
            $command = "\"{$mongodumpPath}\" --uri=\"{$mongoUri}\" --archive=\"{$backupPath}\" --gzip";
            
            $this->info("▶ Ejecutando: mongodump...");
            
            $output = [];
            $returnVar = 0;
            exec($command . ' 2>&1', $output, $returnVar);
            
            if ($returnVar === 0 && file_exists($backupPath)) {
                $size = $this->formatBytes(filesize($backupPath));
                $this->info("✅ Backup creado exitosamente: {$filename} ({$size})");
                
                // Limpiar backups antiguos si se solicita
                if ($this->option('cleanup')) {
                    $this->cleanupOldBackups($backupDir, $retentionDays);
                }
                
                // Registrar en logs
                \Log::info("MongoDB backup creado: {$filename}", [
                    'size' => $size,
                    'path' => $backupPath
                ]);
                
                return Command::SUCCESS;
            } else {
                $this->error('❌ Error al crear backup con mongodump');
                $this->line('Salida del comando:');
                foreach ($output as $line) {
                    $this->line($line);
                }
                
                // Intentar backup alternativo
                return $this->createAlternativeBackup($filename, $backupPath);
            }
            
        } catch (Exception $e) {
            $this->error("❌ Error durante el backup: " . $e->getMessage());
            \Log::error("Error en backup MongoDB: " . $e->getMessage());
            return Command::FAILURE;
        }
    }
    
    /**
     * Buscar mongodump en rutas comunes
     */
    private function findMongodump(): ?string
    {
        $paths = [
            'mongodump',
            'C:\Program Files\MongoDB\Tools\100\bin\mongodump.exe',
            'C:\Program Files\MongoDB\Server\*\bin\mongodump.exe',
            '/usr/bin/mongodump',
            '/usr/local/bin/mongodump'
        ];
        
        foreach ($paths as $path) {
            if (strpos($path, '*') !== false) {
                // Buscar en directorios con wildcard
                $dirs = glob(dirname($path));
                foreach ($dirs as $dir) {
                    $fullPath = $dir . '/' . basename($path);
                    if (is_executable($fullPath)) {
                        return $fullPath;
                    }
                }
            } else {
                $output = [];
                $returnVar = 0;
                exec("where {$path} 2>nul", $output, $returnVar);
                if ($returnVar === 0 && !empty($output)) {
                    return $output[0];
                }
                
                if (is_executable($path)) {
                    return $path;
                }
            }
        }
        
        return null;
    }
    
    /**
     * Crear backup alternativo usando exportación directa
     */
    private function createAlternativeBackup(string $filename, string $backupPath): int
    {
        try {
            $this->info("🔄 Creando backup alternativo usando Laravel...");
            
            // Obtener todas las colecciones y exportar como JSON
            $collections = [
                'usuarios', 'hijos', 'choferes', 'unidades', 'rutas', 
                'admins', 'codigo_seguridads', 'sesiones', 'solicitudes_impresion_qr'
            ];
            
            $backupData = [
                'timestamp' => Carbon::now()->toISOString(),
                'database' => 'trailynsafe',
                'collections' => []
            ];
            
            foreach ($collections as $collection) {
                $this->info("📋 Exportando colección: {$collection}");
                
                $data = \DB::connection('mongodb')
                    ->collection($collection)
                    ->get()
                    ->toArray();
                
                $backupData['collections'][$collection] = $data;
                $this->info("   ✓ {$collection}: " . count($data) . " documentos");
            }
            
            // Comprimir y guardar
            $jsonData = json_encode($backupData, JSON_PRETTY_PRINT);
            $compressedData = gzencode($jsonData, 9);
            
            file_put_contents($backupPath, $compressedData);
            
            $size = $this->formatBytes(filesize($backupPath));
            $this->info("✅ Backup alternativo creado: {$filename} ({$size})");
            
            return Command::SUCCESS;
            
        } catch (Exception $e) {
            $this->error("❌ Error en backup alternativo: " . $e->getMessage());
            return Command::FAILURE;
        }
    }
    
    /**
     * Limpiar backups antiguos
     */
    private function cleanupOldBackups(string $backupDir, int $retentionDays): void
    {
        $this->info("🧹 Limpiando backups antiguos (>{$retentionDays} días)...");
        
        $cutoffTime = Carbon::now()->subDays($retentionDays)->timestamp;
        $files = glob($backupDir . '/trailynsafe_backup_*.gz');
        $deleted = 0;
        
        foreach ($files as $file) {
            if (filemtime($file) < $cutoffTime) {
                unlink($file);
                $deleted++;
                $this->line("   🗑 Eliminado: " . basename($file));
            }
        }
        
        if ($deleted > 0) {
            $this->info("✅ {$deleted} backup(s) antiguos eliminados");
        } else {
            $this->info("ℹ No hay backups antiguos para eliminar");
        }
    }
    
    /**
     * Formatear bytes en unidades legibles
     */
    private function formatBytes(int $bytes, int $precision = 2): string
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        
        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }
        
        return round($bytes, $precision) . ' ' . $units[$i];
    }
}
