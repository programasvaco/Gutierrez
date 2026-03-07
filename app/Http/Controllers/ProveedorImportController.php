<?php

namespace App\Http\Controllers;

use App\Models\Proveedor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class ProveedorImportController extends Controller
{
    /**
     * Mostrar formulario de importación
     */
    public function showImportForm()
    {
        return view('proveedores.import');
    }

    /**
     * Descargar plantilla de Excel
     */
    public function downloadTemplate()
    {
        $filename = 'plantilla_proveedores.xlsx';
        $filepath = storage_path('app/templates/' . $filename);
        
        // Eliminar la plantilla existente si tiene una versión anterior
        if (file_exists($filepath)) {
            unlink($filepath); // Eliminar el archivo antiguo
        }

        // Verificar si existe la plantilla
        if (file_exists($filepath)) {
            return response()->download($filepath);
        }

        // Si no existe, generarla
        $this->generateTemplate();
        
        if (file_exists($filepath)) {
            return response()->download($filepath);
        }
        
        return redirect()->route('proveedores.import')
            ->with('error', 'No se pudo generar la plantilla.');
    }

    /**
     * Generar plantilla de Excel
     */
    private function generateTemplate()
    {
        try {
            $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();

            // Encabezados
            $headers = [
                'A1' => 'nombre',
                'B1' => 'razon_social',                
                'C1' => 'rfc',
                'D1' => 'direccion',
                'E1' => 'ciudad',
                'F1' => 'telefono',
                'G1' => 'correo',
                'H1' => 'dias_plazo',
                'I1' => 'status',
            ];

            foreach ($headers as $cell => $value) {
                $sheet->setCellValue($cell, $value);
                $sheet->getStyle($cell)->getFont()->setBold(true);
                $sheet->getStyle($cell)->getFill()
                    ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                    ->getStartColor()->setARGB('FFE0E0E0');
            }

            // Datos de ejemplo
            $sheet->setCellValue('A2', 'Ferretería El Tornillo');
            $sheet->setCellValue('B2', 'Martín Cruz');
            $sheet->setCellValue('C2', 'FET123456ABC');
            $sheet->setCellValue('D2', 'Av. Principal 123');
            $sheet->setCellValue('E2', 'León, Gto');
            $sheet->setCellValue('F2', '4771234567');
            $sheet->setCellValue('G2', 'ventas@eltornillo.com');
            $sheet->setCellValue('H2', '15');
            $sheet->setCellValue('I2', 'activo');

            $sheet->setCellValue('A3', 'Distribuidora Industrial');
            $sheet->setCellValue('B3', 'María López');
            $sheet->setCellValue('C3', 'DIN987654XYZ');
            $sheet->setCellValue('D3', 'Blvd. Industria 456');
            $sheet->setCellValue('E3', 'Guadalajara, Jal');
            $sheet->setCellValue('F3', '3339876543');
            $sheet->setCellValue('G3', 'contacto@disindustrial.com');
            $sheet->setCellValue('H3', '30');
            $sheet->setCellValue('I3', 'activo');

            $sheet->setCellValue('A4', 'Proveedor Simple');
            $sheet->setCellValue('B4', '');
            $sheet->setCellValue('C4', '');
            $sheet->setCellValue('D4', '');
            $sheet->setCellValue('E4', '');
            $sheet->setCellValue('F4', '');
            $sheet->setCellValue('G4', '');
            $sheet->setCellValue('H4', '');
            $sheet->setCellValue('I4', 'activo');

            // Ajustar ancho de columnas
            $sheet->getColumnDimension('A')->setWidth(30);
            $sheet->getColumnDimension('B')->setWidth(30);
            $sheet->getColumnDimension('C')->setWidth(15);
            $sheet->getColumnDimension('D')->setWidth(20);
            $sheet->getColumnDimension('E')->setWidth(15);
            $sheet->getColumnDimension('F')->setWidth(30);
            $sheet->getColumnDimension('G')->setWidth(25);
            $sheet->getColumnDimension('H')->setWidth(10);
            $sheet->getColumnDimension('I')->setWidth(10);

            // Agregar hoja de instrucciones
            $instructionsSheet = $spreadsheet->createSheet();
            $instructionsSheet->setTitle('Instrucciones');
            $instructionsSheet->setCellValue('A1', 'INSTRUCCIONES PARA IMPORTAR PROVEEDORES');
            $instructionsSheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
            
            $instructions = [
                'A3' => '1. El archivo debe tener los siguientes campos en el orden mostrado:',
                'A4' => '   - nombre: Nombre del proveedor (OBLIGATORIO, máximo 255 caracteres)',
                'A4' => '   - razon_social: Nombre del proveedor (OBLIGATORIO, máximo 255 caracteres)',
                'A5' => '   - rfc: RFC del proveedor (OPCIONAL, máximo 13 caracteres)',
                'A6' => '   - direccion: Domicilio del proveedor (OPCIONAL, máximo 255 caracteres)',
                'A7' => '   - ciudad: Ciudad del proveedor (OPCIONAL, máximo 100 caracteres)',
                'A8' => '   - telefono: Teléfono de contacto (OPCIONAL, máximo 20 caracteres)',
                'A9' => '   - correo: Correo electrónico (OPCIONAL, debe ser válido)',
                'A10' => '   - dias_plazo: días para el pago (OPCIONAL, numero)',
                'A11' => '   - status: Estado del proveedor (OBLIGATORIO: "activo" o "inactivo")',
                'A13' => '2. No elimine la fila de encabezados',
                'A14' => '3. Los campos obligatorios son: nombre y status',
                'A15' => '4. Los demás campos son OPCIONALES, puedes dejarlos vacíos',
                'A16' => '5. El campo status solo acepta: "activo" o "inactivo"',
                'A17' => '6. Si un proveedor con el mismo nombre ya existe, se ACTUALIZARÁ',
                'A18' => '7. Se recomienda no importar más de 500 proveedores a la vez',
                'A19' => '',
                'A20' => '8. Ejemplo de fila con datos completos:',
                'A21' => '   Ferretería ABC | Martín Cruz | ABC123456XYZ | Calle 1 #123 | León | 4771234567 | ventas@abc.com | 30 | activo',
                'A22' => '',
                'A23' => '9. Ejemplo de fila solo con datos obligatorios:',
                'A24' => '   Proveedor XYZ | | | | | | | activo',
            ];

            foreach ($instructions as $cell => $text) {
                $instructionsSheet->setCellValue($cell, $text);
            }

            $instructionsSheet->getColumnDimension('A')->setWidth(100);
            $spreadsheet->setActiveSheetIndex(0);

            // Guardar archivo
            $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
            
            $dir = storage_path('app/templates');
            if (!is_dir($dir)) {
                mkdir($dir, 0755, true);
            }
            
            $writer->save($dir . '/plantilla_proveedores.xlsx');
            
            return true;
        } catch (\Exception $e) {
            \Log::error('Error generando plantilla: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Previsualizar importación
     */
    public function preview(Request $request)
    {
        if (!$request->isMethod('post')) {
            return redirect()->route('proveedores.import')
                ->with('error', 'Método no permitido. Por favor sube un archivo.');
        }

        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls|max:10240',
        ]);

        try {
            $file = $request->file('file');
            
            $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($file->getPathname());
            $sheet = $spreadsheet->getActiveSheet();
            $data = $sheet->toArray();

            // Validar que tenga encabezados
            if (empty($data) || count($data) < 2) {
                return redirect()->route('proveedores.import')
                    ->with('error', 'El archivo está vacío o no tiene datos.');
            }

            $headers = $data[0];
            $expectedHeaders = ['nombre', 'razon_social', 'rfc', 'direccion', 'ciudad', 'telefono', 'correo', 'dias_plazo', 'status'];

            // Validar encabezados
            if ($headers !== $expectedHeaders) {
                return redirect()->route('proveedores.import')
                    ->with('error', 'Los encabezados del archivo no coinciden con la plantilla. Por favor descargue la plantilla y úsela.');
            }

            // Procesar datos
            $rows = [];
            $errors = [];
            $warnings = [];
            $existingNames = Proveedor::pluck('nombre')->toArray();

            foreach (array_slice($data, 1) as $index => $row) {
                $rowNumber = $index + 2;

                // Saltar filas vacías
                if (empty(array_filter($row))) {
                    continue;
                }

                $rowData = [
                    'nombre' => $row[0] ?? '',
                    'razon_social' => $row[1] ?? '',
                    'rfc' => $row[2] ?? '',
                    'direccion' => $row[3] ?? '',
                    'ciudad' => $row[4] ?? '',
                    'telefono' => $row[5] ?? '',
                    'correo' => $row[6] ?? '',
                    'dias_plazo' => $row[7] ?? '',
                    'status' => $row[8] ?? 'activo',
                    'row_number' => $rowNumber,
                    'status_row' => 'ok',
                    'errors' => [],
                ];

                // Validaciones
                $validator = Validator::make($rowData, [
                    'nombre' => 'required|string|max:255',
                    'razon_social' => 'nullable|string|max:200',
                    'rfc' => 'nullable|string|max:13',
                    'domicilio' => 'nullable|string|max:255',
                    'ciudad' => 'nullable|string|max:100',
                    'telefono' => 'nullable|string|max:20',
                    'correo' => 'nullable|email|max:100',
                    'dias_plazo' => 'nullable|integer|min:0',
                    'status' => 'required|in:activo,inactivo',
                ]);

                if ($validator->fails()) {
                    $rowData['status_row'] = 'error';
                    $rowData['errors'] = $validator->errors()->all();
                    $errors[] = "Fila $rowNumber: " . implode(', ', $rowData['errors']);
                }

                // Verificar si el nombre ya existe
                if (!empty($rowData['nombre']) && in_array($rowData['nombre'], $existingNames)) {
                    $rowData['status_row'] = 'warning';
                    $rowData['errors'][] = 'El proveedor ya existe (se actualizará)';
                    $warnings[] = "Fila $rowNumber: El proveedor {$rowData['nombre']} ya existe";
                }

                $rows[] = $rowData;
            }

            if (empty($rows)) {
                return redirect()->route('proveedores.import')
                    ->with('error', 'No se encontraron datos válidos para importar.');
            }

            // Guardar datos en sesión
            session(['proveedor_import_data' => $rows]);

            return view('proveedores.import-preview', compact('rows', 'errors', 'warnings'));

        } catch (\Exception $e) {
            return redirect()->route('proveedores.import')
                ->with('error', 'Error al procesar el archivo: ' . $e->getMessage());
        }
    }

    /**
     * Procesar importación
     */
    public function import(Request $request)
    {
        $rows = session('proveedor_import_data');

        if (!$rows) {
            return redirect()->route('proveedores.import')
                ->with('error', 'No hay datos para importar.');
        }

        DB::beginTransaction();

        try {
            $created = 0;
            $updated = 0;
            $skipped = 0;

            foreach ($rows as $row) {
                // Saltar filas con errores
                if ($row['status_row'] === 'error') {
                    $skipped++;
                    continue;
                }

                $data = [
                    'nombre' => $row['nombre'],
                    'razon_social' => $row['razon_social'] ?: null,
                    'rfc' => $row['rfc'] ?: null,
                    'direccion' => $row['direccion'] ?: null,
                    'ciudad' => $row['ciudad'] ?: null,
                    'telefono' => $row['telefono'] ?: null,
                    'correo' => $row['correo'] ?: null,
                    'dias_plzo' => $row['dias_plazo'] ?: null,
                    'status' => $row['status'],
                ];

                // Intentar actualizar o crear
                $proveedor = Proveedor::where('nombre', $row['nombre'])->first();

                if ($proveedor) {
                    $proveedor->update($data);
                    $updated++;
                } else {
                    Proveedor::create($data);
                    $created++;
                }
            }

            DB::commit();

            // Limpiar sesión
            session()->forget('proveedor_import_data');

            $message = "Importación completada: $created creados, $updated actualizados";
            if ($skipped > 0) {
                $message .= ", $skipped omitidos por errores";
            }

            return redirect()->route('proveedores.index')
                ->with('success', $message);

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('proveedores.import')
                ->with('error', 'Error al importar: ' . $e->getMessage());
        }
    }
}
