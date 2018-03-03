<?php

namespace App\Http\Controllers;

use App\Models\Contract;
use Illuminate\Http\Request;

class ContractController extends Controller
{
    public function index()
    {
        $data['contracts'] = Contract::all();
        return view('admin.contract.index', $data);
    }

    public function create(Request $request)
    {
        return view('admin.contract.import');
    }

    public function store(Request $request)
    {
        try {
            \DB::beginTransaction();
            Contract::query()->truncate();

            $this->readFile($request);
            \DB::commit();
            flash()->success('Nhập danh sách thành công');
            return redirect()->route('contract.index');
        } catch (\Exception $exception) {
            \DB::rollBack();
            flash()->error($exception->getMessage());
            return redirect()->route('contract.create');
        }

    }

    public function edit(Request $request, $id)
    {

    }

    public function update(Request $request, $id)
    {

    }

    public function delete($id)
    {

    }

    public function readFile(Request $request)
    {
        $file = $request->file('fileList');
        $objFile = \PHPExcel_IOFactory::identify($file);
        $objData = \PHPExcel_IOFactory::createReader($objFile);
        $objPHPExcel = $objData->load($file);

        $sheet  = $objPHPExcel->setActiveSheetIndex(0);
        //Lấy ra số dòng cuối cùng
        $Totalrow = $sheet->getHighestRow();

        //Lấy ra tên cột cuối cùng
        $LastColumn = $sheet->getHighestColumn();

        //Chuyển đổi tên cột đó về vị trí thứ, VD: C là 3,D là 4
        $TotalCol = \PHPExcel_Cell::columnIndexFromString($LastColumn);

        $startCol = $endCol = null;
        $startRow = $endRow = null;
        for($row = 0; $row < $Totalrow; $row++) {
            if ($startRow > 0) {
                break;
            }

            $celVal = $sheet->getCellByColumnAndRow(0, $row)->getValue();
            $nexCel = $sheet->getCellByColumnAndRow(1, $row)->getValue();
            if (
                strtolower($celVal) == "stt"
                && str_slug($nexCel) == "so-hd"
                && is_null($startRow)
                && is_null($startCol)
            ) {
                $startRow = $row;
                $startCol = 0;

                for($col = 0; $col < $TotalCol; $col++) {
                    $celVal1 = $sheet->getCellByColumnAndRow($col, $row)->getValue();
                    if (is_null($celVal1)) {
                        $endCol = $col - 1;
                        break;
                    }
                }
            }
        }
        $objPHPExcel->getDefaultStyle()->getNumberFormat()->setFormatCode(\PHPExcel_Style_NumberFormat::FORMAT_TEXT);

        for($row = $startRow+1; $row < $Totalrow; $row++) {
            $celVal = $sheet->getCellByColumnAndRow(0, $row)->getValue();
            $nexCel = $sheet->getCellByColumnAndRow(1, $row)->getValue();
            if (empty($celVal) && empty($nexCel)) {
                break;
            }

            $dateCel = $sheet->getCellByColumnAndRow(7, $row);
            $dateVal = $dateCel->getValue();
            $date = \PHPExcel_Style_NumberFormat::toFormattedString($dateVal, 'yyyy-mm-dd hh:mm:ss');
            $data = [
                "id" => $sheet->getCellByColumnAndRow(0, $row)->getValue(),
                "contract_id" => (int) $sheet->getCellByColumnAndRow(1, $row)->getValue(),
                "contract_name" => $sheet->getCellByColumnAndRow(2, $row)->getValue(),
                "contract_user_name" => $sheet->getCellByColumnAndRow(3, $row)->getValue(),
                "tvgt" => $sheet->getCellByColumnAndRow(4, $row)->getValue(),
                "tvkt" => $sheet->getCellByColumnAndRow(5, $row)->getValue(),
                "code" => (int) $sheet->getCellByColumnAndRow(6, $row)->getValue(),
                "date" => $date,
                "note" => $sheet->getCellByColumnAndRow(8, $row)->getValue(),
            ];

            $contract = new Contract($data);
            $contract->save();
        }
    }
}

class MyValueBinder extends \PHPExcel_Cell_DefaultValueBinder implements \PHPExcel_Cell_IValueBinder
{
    public function bindValue(\PHPExcel_Cell $cell, $value = null)
    {
        if (is_numeric($value))
        {
            $cell->setValueExplicit($value, \PHPExcel_Cell_DataType::TYPE_STRING);

            return true;
        }

        // else return default behavior
        return parent::bindValue($cell, $value);
    }
}

