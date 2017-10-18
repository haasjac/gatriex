<?php
    
    class myRiot {
        function getRegionArray():Array {
            $regions = array(
                "Russia" => "ru",
                "Republic of Korea" => "kr",
                "Brazil" => "br1",
                "Oceania" => "oc1",
                "Japan" => "jp1",
                "North America" => "na1",
                "EU Nordic & East" => "eun1",
                "EU West" => "euw1",
                "Turkey" => "tr1",
                "Latin America North" => "la1",
                "Latin America South" => "la2"
            );          
            
            return $regions;
        }
        
        function getRegions():Array {
            $regions = array_keys($this->getRegionArray());       
            
            return $regions;
        }
    }
    
    $riot = new myRiot();
?>
