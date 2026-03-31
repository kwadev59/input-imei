<?php
namespace App\Models;

use CodeIgniter\Model;

class PengirimanBasteModel extends Model
{
    protected $table = 'pengiriman_baste';
    protected $primaryKey = 'id';
    protected $allowedFields = ['no_baste', 'tanggal', 'created_by', 'created_at', 'updated_at'];
    protected $useTimestamps = true;
    
    public function generateNoBaste()
    {
        $count = $this->countAll() + 1;
        $noUrut = str_pad($count, 3, '0', STR_PAD_LEFT);
        
        $month = date('n'); // 1-12
        $romanMonth = $this->integerToRoman($month);
        
        $year = date('Y');
        
        return sprintf("%s/%s/%s/IT-SITE/BIM-PPS", $noUrut, $romanMonth, $year);
    }
    
    private function integerToRoman($num) 
    {
        $n = intval($num);
        $res = '';
        $roman_arr = array(
            'M'  => 1000,
            'CM' => 900,
            'D'  => 500,
            'CD' => 400,
            'C'  => 100,
            'XC' => 90,
            'L'  => 50,
            'XL' => 40,
            'X'  => 10,
            'IX' => 9,
            'V'  => 5,
            'IV' => 4,
            'I'  => 1);
        foreach ($roman_arr as $roman => $number){
            $matches = intval($n / $number);
            $res .= str_repeat($roman, $matches);
            $n = $n % $number;
        }
        if($res == '') return 'I';
        return $res;
    }
}
