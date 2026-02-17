<?php

namespace App\Http\Controllers;

use App\Models\Producto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class ProductoImportController extends Controller
{
    /**
     * Mostrar formulario de importación
     */
    public function showImportForm()
    {
        return view('productos.import');
    }

    /**
     * Descargar plantilla de Excel
     */
    public function downloadTemplate()
    {
        $filename = 'plantilla_productos.xlsx';
        $filepath = storage_path('app/templates/' . $filename);

        // Eliminar la plantilla existente si tiene una versión anterior
        if (file_exists($filepath)) {
            unlink($filepath); // Eliminar el archivo antiguo
        }
        
        // Generar la nueva plantilla
        $this->generateTemplate();
        
        if (file_exists($filepath)) {
            return response()->download($filepath);
        }
        
        return redirect()->route('productos.import')
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

            // Encabezados - AGREGADOS precioVenta y precioMinimo
            $headers = [
                'A1' => 'codigo',
                'B1' => 'codigoEmpaque',
                'C1' => 'descripcion',
                'D1' => 'unidad',
                'E1' => 'unidadCompra',
                'F1' => 'contenido',
                'G1' => 'stockMin',
                'H1' => 'stockMax',
                'I1' => 'precioVenta',
                'J1' => 'precioMinimo',
            ];

            foreach ($headers as $cell => $value) {
                $sheet->setCellValue($cell, $value);
                $sheet->getStyle($cell)->getFont()->setBold(true);
                $sheet->getStyle($cell)->getFill()
                    ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                    ->getStartColor()->setARGB('FFE0E0E0');
            }

            // Datos de ejemplo - AGREGADOS precios
            $sheet->setCellValue('A2', 'PROD001');
            $sheet->setCellValue('B2', 'EMP001');
            $sheet->setCellValue('C2', 'Tornillo 1/2" x 2"');
            $sheet->setCellValue('D2', 'Pieza');
            $sheet->setCellValue('E2', 'Caja');
            $sheet->setCellValue('F2', '100');
            $sheet->setCellValue('G2', '50');
            $sheet->setCellValue('H2', '500');
            $sheet->setCellValue('I2', '150.50');
            $sheet->setCellValue('J2', '120.00');

            $sheet->setCellValue('A3', 'PROD002');
            $sheet->setCellValue('B3', 'EMP002');
            $sheet->setCellValue('C3', 'Tuerca 1/2"');
            $sheet->setCellValue('D3', 'Pieza');
            $sheet->setCellValue('E3', 'Bolsa');
            $sheet->setCellValue('F3', '50');
            $sheet->setCellValue('G3', '30');
            $sheet->setCellValue('H3', '300');
            $sheet->setCellValue('I3', '75.25');
            $sheet->setCellValue('J3', '60.00');

            // Ajustar ancho de columnas
            foreach (range('A', 'J') as $col) {
                $sheet->getColumnDimension($col)->setAutoSize(true);
            }

            // Agregar hoja de instrucciones - ACTUALIZADA
            $instructionsSheet = $spreadsheet->createSheet();
            $instructionsSheet->setTitle('Instrucciones');
            $instructionsSheet->setCellValue('A1', 'INSTRUCCIONES PARA IMPORTAR PRODUCTOS');
            $instructionsSheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
            
            $instructions = [
                'A3' => '1. El archivo debe tener los siguientes campos en el orden mostrado:',
                'A4' => '   - codigo: Código único del producto (obligatorio, máximo 50 caracteres)',
                'A5' => '   - codigoEmpaque: Código del empaque (opcional, máximo 50 caracteres)',
                'A6' => '   - descripcion: Descripción del producto (obligatorio, máximo 255 caracteres)',
                'A7' => '   - unidad: Unidad de medida (obligatorio, ej: Pieza, Kg, Litro)',
                'A8' => '   - unidadCompra: Unidad de compra (obligatorio, ej: Caja, Bolsa)',
                'A9' => '   - contenido: Contenido numérico (obligatorio, ej: 100)',
                'A10' => '   - stockMin: Stock mínimo (obligatorio, número entero)',
                'A11' => '   - stockMax: Stock máximo (obligatorio, número entero)',
                'A12' => '   - precioVenta: Precio de venta (obligatorio, número decimal)',
                'A13' => '   - precioMinimo: Precio mínimo (obligatorio, número decimal)',
                'A15' => '2. No elimine la fila de encabezados',
                'A16' => '3. Todos los campos marcados como obligatorios no pueden estar vacíos',
                'A17' => '4. El código debe ser único (no puede estar duplicado)',
                'A18' => '5. Los valores numéricos deben ser números válidos',
                'A19' => '6. El precio de venta debe ser mayor o igual al precio mínimo',
                'A20' => '7. Stock máximo debe ser mayor o igual al stock mínimo',
                'A21' => '8. Se recomienda no importar más de 1000 productos a la vez',
            ];

            foreach ($instructions as $cell => $text) {
                $instructionsSheet->setCellValue($cell, $text);
            }

            $instructionsSheet->getColumnDimension('A')->setWidth(100);
            $spreadsheet->setActiveSheetIndex(0);

            // Guardar archivo
            $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
            
            // Crear directorio si no existe
            $dir = storage_path('app/templates');
            if (!is_dir($dir)) {
                mkdir($dir, 0755, true);
            }
            
            $writer->save($dir . '/plantilla_productos.xlsx');
            
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
        // Validar que sea POST
        if (!$request->isMethod('post')) {
            return redirect()->route('productos.import')
                ->with('error', 'Método no permitido. Por favor sube un archivo.');
        }

        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls|max:10240', // Max 10MB
        ]);

        try {
            $file = $request->file('file');
            
            // Cargar PhpSpreadsheet
            $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($file->getPathname());
            $sheet = $spreadsheet->getActiveSheet();
            $data = $sheet->toArray();

            // Validar que tenga encabezados
            if (empty($data) || count($data) < 2) {
                return redirect()->route('productos.import')
                    ->with('error', 'El archivo está vacío o no tiene datos.');
            }

            $headers = $data[0];
            $expectedHeaders = ['codigo', 'codigoEmpaque', 'descripcion', 'unidad', 'unidadCompra', 'contenido', 'stockMin', 'stockMax', 'precioVenta', 'precioMinimo'];

            // Validar encabezados
            if ($headers !== $expectedHeaders) {
                return redirect()->route('productos.import')
                    ->with('error', 'Los encabezados del archivo no coinciden con la plantilla. Por favor descargue la plantilla y úsela.');
            }

            // Procesar datos
            $rows = [];
            $errors = [];
            $warnings = [];
            $existingCodes = Producto::pluck('codigo')->toArray();

            foreach (array_slice($data, 1) as $index => $row) {
                $rowNumber = $index + 2; // +2 porque el índice empieza en 0 y hay 1 fila de encabezados

                // Saltar filas vacías
                if (empty(array_filter($row))) {
                    continue;
                }

                $rowData = [
                    'codigo' => $row[0] ?? '',
                    'codigoEmpaque' => $row[1] ?? '',
                    'descripcion' => $row[2] ?? '',
                    'unidad' => $row[3] ?? '',
                    'unidadCompra' => $row[4] ?? '',
                    'contenido' => $row[5] ?? '',
                    'stockMin' => $row[6] ?? '',
                    'stockMax' => $row[7] ?? '',
                    'precioVenta' => $row[8] ?? '',
                    'precioMinimo' => $row[9] ?? '',
                    'row_number' => $rowNumber,
                    'status' => 'ok',
                    'errors' => [],
                ];

                // Validaciones
                $validator = Validator::make($rowData, [
                    'codigo' => 'required|string|max:50',
                    'descripcion' => 'required|string|max:255',
                    'unidad' => 'required|string|max:50',
                    'unidadCompra' => 'required|string|max:50',
                    'contenido' => 'required|numeric|min:0',
                    'stockMin' => 'required|integer|min:0',
                    'stockMax' => 'required|integer|min:0',
                    'precioVenta' => 'required|numeric|min:0',
                    'precioMinimo' => 'required|numeric|min:0',
                ]);

                if ($validator->fails()) {
                    $rowData['status'] = 'error';
                    $rowData['errors'] = $validator->errors()->all();
                    $errors[] = "Fila $rowNumber: " . implode(', ', $rowData['errors']);
                }

                // Verificar si el código ya existe
                if (!empty($rowData['codigo']) && in_array($rowData['codigo'], $existingCodes)) {
                    $rowData['status'] = 'warning';
                    $rowData['errors'][] = 'El código ya existe en la base de datos (se actualizará)';
                    $warnings[] = "Fila $rowNumber: El código {$rowData['codigo']} ya existe";
                }

                // Validaciones adicionales
                if ($rowData['status'] !== 'error') {
                    // Verificar stockMax >= stockMin
                    if (is_numeric($rowData['stockMin']) && is_numeric($rowData['stockMax'])) {
                        if ($rowData['stockMax'] < $rowData['stockMin']) {
                            $rowData['status'] = 'error';
                            $rowData['errors'][] = 'El stock máximo debe ser mayor o igual al stock mínimo';
                            $errors[] = "Fila $rowNumber: Stock máximo menor que stock mínimo";
                        }
                    }

                    // Verificar precioVenta >= precioMinimo
                    if (is_numeric($rowData['precioVenta']) && is_numeric($rowData['precioMinimo'])) {
                        if ($rowData['precioVenta'] < $rowData['precioMinimo']) {
                            $rowData['status'] = 'error';
                            $rowData['errors'][] = 'El precio de venta debe ser mayor o igual al precio mínimo';
                            $errors[] = "Fila $rowNumber: Precio de venta menor que precio mínimo";
                        }
                    }
                }

                $rows[] = $rowData;
            }

            if (empty($rows)) {
                return redirect()->route('productos.import')
                    ->with('error', 'No se encontraron datos válidos para importar.');
            }

            // Guardar datos en sesión para la importación final
            session(['import_data' => $rows]);

            return view('productos.import-preview', compact('rows', 'errors', 'warnings'));

        } catch (\Exception $e) {
            return redirect()->route('productos.import')
                ->with('error', 'Error al procesar el archivo: ' . $e->getMessage());
        }
    }

    /**
     * Procesar importación
     */
    public function import(Request $request)
    {
        $rows = session('import_data');

        if (!$rows) {
            return redirect()->route('productos.import')->with('error', 'No hay datos para importar.');
        }

        DB::beginTransaction();

        try {
            $created = 0;
            $updated = 0;
            $skipped = 0;

            foreach ($rows as $row) {
                // Saltar filas con errores
                if ($row['status'] === 'error') {
                    $skipped++;
                    continue;
                }

                $data = [
                    'codigo' => $row['codigo'],
                    'codigo_empaque' => $row['codigoEmpaque'],
                    'descripcion' => $row['descripcion'],
                    'unidad' => $row['unidad'],
                    'unidad_compra' => $row['unidadCompra'],
                    'contenido' => $row['contenido'] ?: 0,
                    'stock_min' => $row['stockMin'],
                    'stock_max' => $row['stockMax'],
                    'precio_venta' => $row['precioVenta'] ?: 0,
                    'precio_minimo' => $row['precioMinimo'] ?: 0,
                    'status' => 'activo',
                ];

                // Intentar actualizar o crear
                $producto = Producto::where('codigo', $row['codigo'])->first();

                if ($producto) {
                    $producto->update($data);
                    $updated++;
                } else {
                    Producto::create($data);
                    $created++;
                }
            }

            DB::commit();

            // Limpiar sesión
            session()->forget('import_data');

            $message = "Importación completada: $created creados, $updated actualizados";
            if ($skipped > 0) {
                $message .= ", $skipped omitidos por errores";
            }

            return redirect()->route('productos.index')->with('success', $message);

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('productos.import')
                ->with('error', 'Error al importar: ' . $e->getMessage());
        }
    }
}