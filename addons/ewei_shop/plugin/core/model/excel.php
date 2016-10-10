<?php
if (!defined("IN_IA")) {
    exit("Access Denied");
}
class Ewei_DShop_Excel
{
    protected function column_str($val0)
    {
        $val1 = array(
            "A",
            "B",
            "C",
            "D",
            "E",
            "F",
            "G",
            "H",
            "I",
            "J",
            "K",
            "L",
            "M",
            "N",
            "O",
            "P",
            "Q",
            "R",
            "S",
            "T",
            "U",
            "V",
            "W",
            "X",
            "Y",
            "Z",
            "AA",
            "AB",
            "AC",
            "AD",
            "AE",
            "AF",
            "AG",
            "AH",
            "AI",
            "AJ",
            "AK",
            "AL",
            "AM",
            "AN",
            "AO",
            "AP",
            "AQ",
            "AR",
            "AS",
            "AT",
            "AU",
            "AV",
            "AW",
            "AX",
            "AY",
            "AZ",
            "BA",
            "BB",
            "BC",
            "BD",
            "BE",
            "BF",
            "BG",
            "BH",
            "BI",
            "BJ",
            "BK",
            "BL",
            "BM",
            "BN",
            "BO",
            "BP",
            "BQ",
            "BR",
            "BS",
            "BT",
            "BU",
            "BV",
            "BW",
            "BX",
            "BY",
            "BZ",
            "CA",
            "CB",
            "CC",
            "CD",
            "CE",
            "CF",
            "CG",
            "CH",
            "CI",
            "CJ",
            "CK",
            "CL",
            "CM",
            "CN",
            "CO",
            "CP",
            "CQ",
            "CR",
            "CS",
            "CT",
            "CU",
            "CV",
            "CW",
            "CX",
            "CY",
            "CZ",
            "DA",
            "DB",
            "DC",
            "DD",
            "DE",
            "DF",
            "DG",
            "DH",
            "DI",
            "DJ",
            "DK",
            "DL",
            "DM",
            "DN",
            "DO",
            "DP",
            "DQ",
            "DR",
            "DS",
            "DT",
            "DU",
            "DV",
            "DW",
            "DX",
            "DY",
            "DZ",
            "EA",
            "EB",
            "EC",
            "ED",
            "EE",
            "EF",
            "EG",
            "EH",
            "EI",
            "EJ",
            "EK",
            "EL",
            "EM",
            "EN",
            "EO",
            "EP",
            "EQ",
            "ER",
            "ES",
            "ET",
            "EU",
            "EV",
            "EW",
            "EX",
            "EY",
            "EZ"
        );
        return $val1[$val0];
    }
    protected function column($val0, $val5 = 1)
    {
        return $this->column_str($val0) . $val5;
    }
    function export($val8, $val9 = array())
    {
        if (PHP_SAPI == "cli") {
            die("This example should only be run from a Web Browser");
        }
        require_once IA_ROOT . "/framework/library/phpexcel/PHPExcel.php";
        $val10 = new PHPExcel();
        $val10->getProperties()->setCreator("人人商城")->setLastModifiedBy("人人商城")->setTitle("Office 2007 XLSX Test Document")->setSubject("Office 2007 XLSX Test Document")->setDescription("Test document for Office 2007 XLSX, generated using PHP classes.")->setKeywords("office 2007 openxml php")->setCategory("report file");
        $val12 = $val10->setActiveSheetIndex(0);
        $val14 = 1;
        foreach ($val9["columns"] as $val0 => $val17) {
            $val12->setCellValue($this->column($val0, $val14), $val17["title"]);
            if (!empty($val17["width"])) {
                $val12->getColumnDimension($this->column_str($val0))->setWidth($val17["width"]);
            }
        }
        $val14++;
        $val25 = count($val9["columns"]);
        ;
        foreach ($val8 as $val28) {
            for ($val29 = 0; $val29 < $val25; $val29++) {
                $val33 = isset($val28[$val9["columns"][$val29]["field"]]) ? $val28[$val9["columns"][$val29]["field"]] : '';
                $val12->setCellValue($this->column($val29, $val14), $val33);
            }
            $val14++;
        }
        $val10->getActiveSheet()->setTitle($val9["title"]);
        $val46 = urlencode($val9["title"] . "-" . date("Y-m-d H:i", time()));
        header("Content-Type: application/octet-stream");
        header('Content-Disposition: attachment;filename="" .$val46 . ".xls"');
        header("Cache-Control: max-age=0");
        $val49 = PHPExcel_IOFactory::createWriter($val10, "Excel5");
        $val49->save("php://output");
        exit;
    }
    public function import($val52)
    {
        global $_W;
        require_once IA_ROOT . "/framework/library/phpexcel/PHPExcel.php";
        require_once IA_ROOT . "/framework/library/phpexcel/PHPExcel/IOFactory.php";
        require_once IA_ROOT . "/framework/library/phpexcel/PHPExcel/Reader/Excel5.php";
        $val54 = IA_ROOT . "/addons/ewei_shop/data/tmp/";
        if (!is_dir($val54)) {
            load()->func("file");
            mkdirs($val54, "0777");
        }
        $val46 = $_FILES[$val52]["name"];
        $val60 = $_FILES[$val52]["tmp_name"];
        if (empty($val60)) {
            message("请选择要上传的Excel文件!", '', "error");
        }
        $val64 = strtolower(pathinfo($val46, PATHINFO_EXTENSION));
        if ($val64 != "xlsx" && $val64 != "xls") {
            message("请上传 xls 或 xlsx 格式的Excel文件!", '', "error");
        }
        $val68 = time() . $_W["uniacid"] . "." . $val64;
        $val71 = $val54 . $val68;
        $val73 = move_uploaded_file($val60, $val71);
        if (!$val73) {
            message("上传Excel 文件失败, 请重新上传!", '', "error");
        }
        $val77 = PHPExcel_IOFactory::createReader($val64 == "xls" ? "Excel5" : "Excel2007");
        $val10 = $val77->load($val71);
        $val12 = $val10->getActiveSheet();
        $val83 = $val12->getHighestRow();
        $val85 = $val12->getHighestColumn();
        $val87 = PHPExcel_Cell::columnIndexFromString($val85);
        $val89 = array();
        for ($val28 = 2; $val28 <= $val83; $val28++) {
            $val93 = array();
            for ($val94 = 0; $val94 < $val87; $val94++) {
                $val93[] = $val12->getCellByColumnAndRow($val94, $val28)->getValue();
            }
            $val89[] = $val93;
        }
        return $val89;
    }
}
?>