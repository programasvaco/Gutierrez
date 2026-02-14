<?php

namespace App\Http\Controllers;

use App\Models\Inventario;
use App\Models\Producto;
use App\Models\Almacen;
use App\Models\Kardex;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;

class InventarioImportController extends Controller
{
    /**
     * Mostrar formulario de importación
     */
    public function showImportForm()
    {
        $almacenes = Almacen::where('status', 'activo')->orderBy('nombre')->get();
        return view('inventarios.import', compact('almacenes'));
    }

    /**
     * Descargar plantilla de Excel con productos actuales
     */
    public function downloadTemplate(Request $request)
    {
        $almacen_id = $request->get('almacen_id');
        
        if (!$almacen_id) {
            return redirect()->route('inventarios.import')
                ->with('error', 'Debes seleccionar un almacén.');
        }

        $almacen = Almacen::findOrFail($almacen_id);
        
        $this->generateTemplate($almacen);
        
        $filename = 'inventario_' . str_replace(' ', '_', $almacen->nombre) . '.xlsx';
        $filepath = storage_path('app/templates/' . $filename);
        
        if (file_exists($filepath)) {
            return response()->download($filepath)->deleteFileAfterSend(false);
        }
        
        return redirect()->route('inventarios.import')
            ->with('error', 'No se pudo generar la plantilla.');
    }

    /**
     * Generar plantilla de Excel con productos e inventario actual
     */
    private function generateTemplate(Almacen $almacen)
    {
        try {
            $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();

            // Título
            $sheet->setCellValue('A1', 'INVENTARIO - ' . strtoupper($almacen->nombre));
            $sheet->mergeCells('A1:F1');
            $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
            $sheet->getStyle('A1')->getAlignment()->setHorizontal('center');

            // Información
            $sheet->setCellValue('A2', 'Fecha de generación: ' . now()->format('d/m/Y H:i'));
            $sheet->mergeCells('A2:F2');
            
            // Encabezados (fila 4)
            $headers = [
                'A4' => 'codigo',
                'B4' => 'descripcion',
                'C4' => 'unidad',
                'D4' => 'existenciaActual',
                'E4' => 'existenciaFisica',
                'F4' => 'costo',
            ];

            foreach ($headers as $cell => $value) {
                $sheet->setCellValue($cell, $value);
                $sheet->getStyle($cell)->getFont()->setBold(true);
                $sheet->getStyle($cell)->getFill()
                    ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                    ->getStartColor()->setARGB('FF4472C4');
                $sheet->getStyle($cell)->getFont()->getColor()->setARGB('FFFFFFFF');
            }

            // Obtener productos con su inventario actual
            $productos = Producto::where('status', 'activo')
                ->orderBy('codigo')
                ->get();

            $row = 5;
            foreach ($productos as $producto) {
                // Buscar inventario actual
                $inventario = Inventario::where('producto_id', $producto->id)
                    ->where('almacen_id', $almacen->id)
                    ->first();

                $existenciaActual = $inventario ? $inventario->existencia : 0;

                $sheet->setCellValue('A' . $row, $producto->codigo);
                $sheet->setCellValue('B' . $row, $producto->descripcion);
                $sheet->setCellValue('C' . $row, $producto->unidad);
                $sheet->setCellValue('D' . $row, number_format($existenciaActual, 2));
                $sheet->setCellValue('E' . $row, ''); // Para llenar manualmente
                $sheet->setCellValue('F' . $row, '0.00'); // Costo por defecto
                
                // Colorear existencia actual
                $sheet->getStyle('D' . $row)->getFill()
                    ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                    ->getStartColor()->setARGB('FFF0F0F0');
                
                // Resaltar existencia física para llenar
                $sheet->getStyle('E' . $row)->getFill()
                    ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                    ->getStartColor()->setARGB('FFFFFF00');

                $row++;
            }

            // Ajustar ancho de columnas
            $sheet->getColumnDimension('A')->setWidth(15);
            $sheet->getColumnDimension('B')->setWidth(40);
            $sheet->getColumnDimension('C')->setWidth(12);
            $sheet->getColumnDimension('D')->setWidth(18);
            $sheet->getColumnDimension('E')->setWidth(18);
            $sheet->getColumnDimension('F')->setWidth(12);

            // Hoja de instrucciones
            $instructionsSheet = $spreadsheet->createSheet();
            $instructionsSheet->setTitle('Instrucciones');
            $instructionsSheet->setCellValue('A1', 'INSTRUCCIONES PARA AJUSTE DE INVENTARIO');
            $instructionsSheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
            
            $instructions = [
                'A3' => '1. IMPORTANTE: NO MODIFIQUES las columnas codigo, descripcion, unidad y existenciaActual',
                'A4' => '',
                'A5' => '2. Llena ÚNICAMENTE la columna "existenciaFisica" con el conteo real del inventario',
                'A6' => '',
                'A7' => '3. El campo "costo" es OBLIGATORIO. Si no conoces el costo, usa 0.00',
                'A8' => '',
                'A9' => '4. El sistema comparará automáticamente:',
                'A10' => '   - Si existenciaActual = 0 y existenciaFisica > 0 → ENTRADA (inventario inicial)',
                'A11' => '   - Si existenciaActual > existenciaFisica → SALIDA (ajuste negativo)',
                'A12' => '   - Si existenciaActual < existenciaFisica → ENTRADA (ajuste positivo)',
                'A13' => '   - Si son iguales → NO hace nada',
                'A14' => '',
                'A15' => '5. Todos los movimientos se registran en el KARDEX como:',
                'A16' => '   - "Inventario inicial" (si no había existencia)',
                'A17' => '   - "Ajuste de inventario" (si ya había existencia)',
                'A18' => '',
                'A19' => '6. Después de llenar la columna existenciaFisica, guarda el archivo y súbelo',
                'A20' => '',
                'A21' => '7. RECOMENDACIONES:',
                'A22' => '   - Haz el conteo físico cuidadosamente',
                'A23' => '   - Verifica los costos antes de importar',
                'A24' => '   - Revisa la previsualización antes de confirmar',
                'A25' => '   - Haz una copia de seguridad antes de ajustes masivos',
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
            
            $filename = 'inventario_' . str_replace(' ', '_', $almacen->nombre) . '.xlsx';
            $writer->save($dir . '/' . $filename);
            
            return true;
        } catch (\Exception $e) {
            \Log::error('Error generando plantilla de inventario: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Previsualizar ajustes de inventario
     */
    public function preview(Request $request)
    {
        if (!$request->isMethod('post')) {
            return redirect()->route('inventarios.import')
                ->with('error', 'Método no permitido.');
        }

        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls|max:10240',
            'almacen_id' => 'required|exists:almacenes,id',
        ]);

        try {
            $file = $request->file('file');
            $almacen_id = $request->almacen_id;
            $almacen = Almacen::findOrFail($almacen_id);
            
            $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($file->getPathname());
            $sheet = $spreadsheet->getActiveSheet();
            $data = $sheet->toArray();

            // Validar encabezados (fila 4)
            if (count($data) < 5) {
                return redirect()->route('inventarios.import')
                    ->with('error', 'El archivo está vacío o no tiene datos.');
            }

            $headers = $data[3]; // Fila 4 (índice 3)
            $expectedHeaders = ['codigo', 'descripcion', 'unidad', 'existenciaActual', 'existenciaFisica', 'costo'];

            if ($headers !== $expectedHeaders) {
                return redirect()->route('inventarios.import')
                    ->with('error', 'Los encabezados del archivo no coinciden con la plantilla.');
            }

            // Procesar datos
            $rows = [];
            $errors = [];
            $warnings = [];
            $contadores = [
                'entradas' => 0,
                'salidas' => 0,
                'sin_cambio' => 0,
                'inventario_inicial' => 0,
            ];

            foreach (array_slice($data, 4) as $index => $row) {
                $rowNumber = $index + 5;

                // Saltar filas vacías
                if (empty(array_filter($row))) {
                    continue;
                }

                // Si existenciaFisica está vacía, saltar
                if (empty($row[4]) && $row[4] !== '0' && $row[4] !== 0) {
                    continue;
                }

                $codigo = $row[0] ?? '';
                $existenciaActual = is_numeric($row[3]) ? floatval(str_replace(',', '', $row[3])) : 0;
                $existenciaFisica = is_numeric($row[4]) ? floatval($row[4]) : null;
                $costo = is_numeric($row[5]) ? floatval($row[5]) : 0;

                // Buscar producto
                $producto = Producto::where('codigo', $codigo)->first();

                if (!$producto) {
                    $errors[] = "Fila $rowNumber: Producto con código '$codigo' no encontrado";
                    continue;
                }

                if ($existenciaFisica === null) {
                    continue; // Saltar si no se llenó existencia física
                }

                // Calcular diferencia
                $diferencia = $existenciaFisica - $existenciaActual;

                $rowData = [
                    'row_number' => $rowNumber,
                    'producto_id' => $producto->id,
                    'codigo' => $codigo,
                    'descripcion' => $producto->descripcion,
                    'unidad' => $producto->unidad,
                    'existencia_actual' => $existenciaActual,
                    'existencia_fisica' => $existenciaFisica,
                    'diferencia' => $diferencia,
                    'costo' => $costo,
                    'tipo_movimiento' => '',
                    'status' => 'ok',
                    'errors' => [],
                ];

                // Determinar tipo de movimiento
                if ($diferencia == 0) {
                    $rowData['tipo_movimiento'] = 'Sin cambio';
                    $rowData['status'] = 'sin_cambio';
                    $contadores['sin_cambio']++;
                } elseif ($existenciaActual == 0 && $diferencia > 0) {
                    $rowData['tipo_movimiento'] = 'Inventario Inicial';
                    $rowData['status'] = 'inventario_inicial';
                    $contadores['inventario_inicial']++;
                } elseif ($diferencia > 0) {
                    $rowData['tipo_movimiento'] = 'Entrada (Ajuste +)';
                    $rowData['status'] = 'entrada';
                    $contadores['entradas']++;
                } else {
                    $rowData['tipo_movimiento'] = 'Salida (Ajuste -)';
                    $rowData['status'] = 'salida';
                    $contadores['salidas']++;
                }

                // Validar costo
                if ($diferencia != 0 && $costo <= 0) {
                    $rowData['status'] = 'warning';
                    $rowData['errors'][] = 'El costo debe ser mayor a 0';
                    $warnings[] = "Fila $rowNumber: Costo no válido para {$codigo}";
                }

                $rows[] = $rowData;
            }

            if (empty($rows)) {
                return redirect()->route('inventarios.import')
                    ->with('error', 'No se encontraron datos para procesar. Llena la columna "existenciaFisica".');
            }

            // Guardar en sesión
            session([
                'inventory_import_data' => $rows,
                'inventory_import_almacen' => $almacen_id,
            ]);

            return view('inventarios.import-preview', compact('rows', 'errors', 'warnings', 'almacen', 'contadores'));

        } catch (\Exception $e) {
            return redirect()->route('inventarios.import')
                ->with('error', 'Error al procesar el archivo: ' . $e->getMessage());
        }
    }

    /**
     * Procesar ajuste de inventario
     */
    public function import(Request $request)
    {
        $rows = session('inventory_import_data');
        $almacen_id = session('inventory_import_almacen');

        if (!$rows || !$almacen_id) {
            return redirect()->route('inventarios.import')
                ->with('error', 'No hay datos para procesar.');
        }

        $almacen = Almacen::findOrFail($almacen_id);

        DB::beginTransaction();

        try {
            $procesados = 0;
            $omitidos = 0;

            foreach ($rows as $row) {
                // Saltar si no hay cambio o si tiene errores
                if ($row['status'] === 'sin_cambio' || $row['status'] === 'warning') {
                    $omitidos++;
                    continue;
                }

                $producto_id = $row['producto_id'];
                $diferencia = $row['diferencia'];
                $costo = $row['costo'];

                // Determinar tipo de movimiento para kardex
                if ($row['status'] === 'inventario_inicial') {
                    $documento = 'Inventario inicial';
                } else {
                    $documento = 'Ajuste de inventario';
                }

                $fecha = now()->format('Y-m-d');
                $referencia = 'INV-' . now()->format('YmdHis');

                // Aplicar ajuste
                if ($diferencia > 0) {
                    // Entrada
                    Inventario::incrementarExistencia(
                        $almacen_id,
                        $producto_id,
                        abs($diferencia),
                        $costo,
                        $documento,
                        $referencia,
                        $fecha
                    );
                } else {
                    // Salida
                    Inventario::decrementarExistencia(
                        $almacen_id,
                        $producto_id,
                        abs($diferencia),
                        $costo,
                        $documento,
                        $referencia,
                        $fecha
                    );
                }

                $procesados++;
            }

            DB::commit();

            // Limpiar sesión
            session()->forget(['inventory_import_data', 'inventory_import_almacen']);

            $message = "Ajuste completado: $procesados movimientos registrados";
            if ($omitidos > 0) {
                $message .= ", $omitidos sin cambios";
            }

            return redirect()->route('inventarios.index')
                ->with('success', $message);

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('inventarios.import')
                ->with('error', 'Error al procesar ajuste: ' . $e->getMessage());
        }
    }
}
