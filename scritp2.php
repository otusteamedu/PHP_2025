<?php 

$list1 = [1,2,4];
$list2 = [1,3,4];


function arrayslice($array) {
	for ($i = 0; $i < count($array) - 1; $i++) {
		$array[$i] = $array[$i + 1];
	}
	unset($array[count($array) - 1]);
	return $array;
}

function mergeTwoLists($list1, $list2) {
	
	global $arr;
	
	$arr = (!empty($arr)) ? $arr : [];
	
    if (empty($list1) AND empty($list2)) {

		return $arr;

	}
	
    elseif (empty($list1) AND !empty($list2)) {
		 
		$arr[] = $list2[0];
		$list2 = arrayslice($list2);
		
    }
	
    elseif (empty($list2) AND !empty($list1)) {
		
		$arr[] = $list1[0];
		$list1 = arrayslice($list1);
		
    }
	
	else {
		
		if ($list1[0] <= $list2[0]) {

			$arr[] = $list1[0];
			$list1 = arrayslice($list1);

		} 
        
        else {

			$arr[] = $list2[0];
			$list2 = arrayslice($list2);
            
		}
		
	}
    
	mergeTwoLists($list1, $list2);
	
}

mergeTwoLists($list1, $list2);

echo "<pre>";
print_r($arr);
echo "</pre>";
