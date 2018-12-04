1) Please see the method checkIfPalindrome in the class Test below.

2) Please see the method checkIfWithinRange in the class Test below.

3) 
	A queue is a first-in, first-out data structure (FIFO). Data within a queue is accessed in the same order as the data came in as. For example, if I enter into the queue integer values of 1,2,3,4, the queue can only output the values in the order 1,2,3,4. 1 was the first item in and it will be the first item out. Data insertions are known as 'enqueue' and data extraction is known as 'dequeue'.

	A stack is a last-in, first-out data structure (LIFO). Data within a stack is accessed in reverse order of how it came in as. So if I decided to enter in integer values ( in the following order ) 1,2,3,4 into a stack, the stack can only output these values in a 4,3,2,1.  4 was the last value in, so it is the first out. Data insertion operation for a stack is known as 'push' and data extraction is known as 'pop'.

4) The simplest but most expensive solution (assuming there is ONLY one pair of duplicates and no other number appears more than once), would be to walk down each value in the array and compare it with the rest. A double-for-loop solution. Have one for-loop that isolates the first element in the array and walks to the end of the array. Have a second for-loop within the original that starts with the element exactly one index in front of the item currently isolated by the outer loop. The second for-loop needs to walk down to the end of the array in order to compare the item isolated by the outer loop with all other elements. Once the 2nd for-loop completes, increment the outer loops counter by one and repeat until the duplicate is located. The efficiency would be quadratic, or O(n^2), which is incredibly inefficient and not the ideal solution.

A better solution would be to sort the array. I would allocate an array with 1,000,000 slots. Arrays begin with an index of 0. So I would walk along the array containing values between 1 and 1,000,000, pull the value in the element and insert it into the appropriate slot according to the appropiate index. For example:

	Let's call the array with the 1 - 1,000,000 values $unsorted[].

	Now say that $unsorted[4] = 29;

	I would insert 29 into my newly allocated array at $newlyAllocated[28] = 29. This is placed as such array indexes begin at zero. The value will always be placed at the array index that is 1 less than the value itself, so [0]=1, [1]=2, etc. 

	A duplicate value will inevitably try to occupy the same space the original value is currently taking up. So if I walk up to $unsorted[1000] = 29 and try to insert that into $newlyAllocated[28], I can check to see if that spot in $newlyAllocated currently contains a value. Well, we know that $newlyAllocated[28] already holds 29. Therefore 29 is our duplicate value and I can return this to the user.

	We only need to traverse the array once, givng us a linear time or O(n) solution, making this a far better option that the original. 



class Test {

     CONST ACCEPTABLE_NUM_TYPES = ['string', 'integer', 'double'];

    public function checkIfPalindrome($strToCheck){
        if(strlen($strToCheck) == 0 || strlen($strToCheck) == 1){
            return true;
        }

        if(substr($strToCheck, 0,1) == substr($strToCheck, strlen($strToCheck)-1,1)){
            return $this->checkIfPalindrome(substr($strToCheck, 1, strlen($strToCheck)-2 ));
        }
        return false;
    }

    public function checkIfWithinRange($num, $range){

        $typeOfInput = gettype($num);
        $typeOfRange = gettype($range);

        if(!in_array($typeOfInput, self::ACCEPTABLE_NUM_TYPES)){
            echo "Number to check must be either an integer, a double OR a string that can be converted to either of those two types.\n\n";
            return false;
        }

        if($typeOfInput === 'string' && !is_numeric($num)){
            echo "The number to check can be a string but it has to be numeric.\n\n";
            return false;
        } else {
            $num = floatVal($num);
        }

        if($typeOfRange !== 'string'){
            echo "The range value must be a string .\n\n";
            return false;
        }

        $numHyphens = substr_count($range,"-");
        if($numHyphens < 1 || $numHyphens > 1){
            echo "The string representing the range is not in the proper format. Please enter a min-max range in #-#. \n\n";
            return false;
        }
        $range = explode('-', $range);

        foreach($range as $key => $rangeValue){
            if(!is_numeric($rangeValue)){

                echo "One of the range values is not numeric. Please try again. \n\n";
                return false;
            }
            $range[$key] = floatVal($rangeValue);
        }
        
        if($range[0] >= $range[1]){
            echo "Values within range must be in a min-max format. \n\n";
            return false;
        }

        return ($num >= $range[0] && $num <= $range[1]) ? true : false;

    }
}