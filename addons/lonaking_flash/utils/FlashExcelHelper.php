<?php
class FlashExcelHelper
{
    public static function dump($header, $data = null, $filename = "")
    {
        require dirname(__FILE__) . '/../../../framework/library/phpexcel/PHPExcel.php';
        $excel       = new PHPExcel();
        $letter      = self::getLetters($header);
        $tableheader = array();
        foreach ($header as $column => $name) {
            $tableheader[] = $name;
        }
        for ($i = 0; $i < count($tableheader); $i++) {
            $excel->getActiveSheet()->setCellValue("$letter[$i]1", "$tableheader[$i]");
        }
        if (!is_null($data)) {
            $tableData = array();
            foreach ($data as $d) {
                $tmp_data = array();
                foreach ($header as $column => $name) {
                    $tmp_data[] = $d[$column];
                }
                $tableData[] = $tmp_data;
            }
            for ($i = 2; $i <= count($tableData) + 1; $i++) {
                $j = 0;
                foreach ($tableData[$i - 2] as $key => $value) {
                    $excel->getActiveSheet()->setCellValue("$letter[$j]$i", "$value");
                    $j++;
                }
            }
        }
        $write = new PHPExcel_Writer_Excel5($excel);
        ob_end_clean();
        $filename = $filename . date("Y-m-d h-i-s");
        header("Pragma: public");
        header("Expires: 0");
        header("Cache-Control:must-revalidate, post-check=0, pre-check=0");
        header("Content-Type:application/force-download");
        header("Content-Type:application/vnd.ms-execl");
        header("Content-Type:application/octet-stream");
        header("Content-Type:application/download");
        ;
        header('Content-Disposition:attachment;filename="' . $filename . '.xls"');
        header("Content-Transfer-Encoding:binary");
        $write->save('php://output');
    }
    private static function getLetters($header)
    {
        $letters = array(
            'A',
            'B',
            'C',
            'D',
            'E',
            'F',
            'G',
            'H',
            'I',
            'J',
            'K',
            'L',
            'M',
            'N',
            'O',
            'P',
            'Q',
            'R',
            'S',
            'T',
            'U',
            'V',
            'W',
            'X',
            'Y',
            'Z'
        );
        $count   = sizeof($header);
        return array_slice($letters, 0, $count);
    }
}
