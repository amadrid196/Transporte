<?php
namespace App\Utility;

class MilamUtility {
    public function getAddress($address)
    {
        $result = "";
        $splited = explode(",", $address);
        $spliteLength = count($splited);
        if($spliteLength  >3)
        {
            $result = $splited[$spliteLength -3].
            ' '.$splited[$spliteLength-2].
            ' '.$splited[$spliteLength-1];
        }

        return $result;
    }

    public function convertDate($date)
    {
        $result = date("m/d/Y h:i.a", strtotime(
            $date
        ));

        return $result;
    }

    public function convertNumber($number)
    {
        $result = 0;
        if(!empty($number))
        {
            $result = number_format($number, 2);
        }
        return $result;
    }

    
}
?>