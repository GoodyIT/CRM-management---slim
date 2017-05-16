<?php

function validateDate($date, $format = 'Y-m-d H:i:s')
{
    try {
        
    $d = DateTime::createFromFormat($format, $date, new DateTimeZone("America/Los_Angeles") );
    if($d){return $d->format("YmdHis");} 

    $date = str_replace("-", "/", $date);

    // Y/m/d
    $d = DateTime::createFromFormat('Y/m/d', $date, new DateTimeZone("America/Los_Angeles") );
    if($d){return $d->format("YmdHis");} 
        
    $d = DateTime::createFromFormat('Y/m/d H:i:s', $date, new DateTimeZone("America/Los_Angeles") );
    if($d){return $d->format("YmdHis");} 
            
    $d = DateTime::createFromFormat('Y/m/d G:i', $date, new DateTimeZone("America/Los_Angeles") );
    if($d){return $d->format("YmdHis");} 
    
    $d = DateTime::createFromFormat('Y/m/d h:i a', $date, new DateTimeZone("America/Los_Angeles") );
    if($d){return $d->format("YmdHis");} 
    

    $firstMonthCheck = explode("/",$date);
    if($firstMonthCheck[0] < 13){
        // m/d/Y
        $d = DateTime::createFromFormat('m/d/Y H:i:s', $date, new DateTimeZone("America/Los_Angeles") );
        if($d){return $d->format("YmdHis");} 

        $d = DateTime::createFromFormat('m/d/Y', $date, new DateTimeZone("America/Los_Angeles") );
        if($d){return $d->format("YmdHis");} 

        $d = DateTime::createFromFormat('m/d/Y h:i a', $date, new DateTimeZone("America/Los_Angeles") );
        if($d){return $d->format("YmdHis");} 

        $d = DateTime::createFromFormat('m/d/Y G:i', $date, new DateTimeZone("America/Los_Angeles") );
        if($d){return $d->format("YmdHis");} 
    }
    
    
    // d/m/Y
    $d = DateTime::createFromFormat('d/m/Y H:i:s', $date, new DateTimeZone("America/Los_Angeles") );
    if($d){return $d->format("YmdHis");} 

    $d = DateTime::createFromFormat('d/m/Y', $date, new DateTimeZone("America/Los_Angeles") );
    if($d){return $d->format("YmdHis");} 
    
    $d = DateTime::createFromFormat('d/m/Y h:i a', $date, new DateTimeZone("America/Los_Angeles") );
    if($d){return $d->format("YmdHis");} 
    
    $d = DateTime::createFromFormat('d/m/Y G:i', $date, new DateTimeZone("America/Los_Angeles") );
    if($d){return $d->format("YmdHis");} 
    
    // Final Attempt to return 
    $d = new DateTime($date, new DateTimeZone("America/Los_Angeles") );
    if($d){return $d->format("YmdHis");} 
    
    
    } catch (Exception $e) {
        return false;
    }

        
        
}
echo "<PRE>1 ";
print_r(validateDate('2012-02-28 12:12:12')); # true
echo "<P>2 ";
print_r(validateDate('2012-02-12 12:12:00')); # false
echo "<P>3 ";
print_r(validateDate('2012-02-28')); # true
echo "<P>4 ";
print_r(validateDate('14/14/2012 12:10 PM')); # true
echo "<P>5 ";
print_r(validateDate('22-02-2012  12:12:00')); # false
echo "<P>6 ";
print_r(validateDate('14:50', 'H:i')); # true
echo "<P>7 ";
print_r(validateDate('12:77', 'H:i')); # false
echo "<P>8 ";
print_r(validateDate(12, 'H')); # true
echo "<P>9 ";
print_r(validateDate('14', 'H')); # true
echo "<P>10 ";
print_r(validateDate('2011-03-12')); # true

echo "<P>11 ";
print_r(validateDate('2011-07-12 16:12')); # true


echo "<P>12 ";
print_r(validateDate('Friday October 1st, 2015 8:45 PM')); # true



echo "<P>13 ";
print_r(validateDate('MonadfdsM')); # true
echo "</PRE>";
?>
      
      
       <form action="/thingcrm/api/thing/create" method="POST">
        <input type="hidden" name="testitem_0_createThing" value="Y">

        <input type="hidden" name="testitem_0_id" value="dAA8I7vc-xGKQ8cDm-o4h7t7Ym">
             <h3>Parent</h3>
                            
        <p>
        Name
         <br>
        <input type="text" name="testitem_0_name" value="parent1">
        </p>
         <p>
        Payment (Encrypts with keyword "payment")
        <br>
        <input type="text" name="testitem_0_paymentInfo" value="4444333355556666">
        </p>
         <p>
        Date  (saves as seconds since epoch "U")
         <br>
        <input type="text" name="testitem_0_dateInfo" value="<?php echo date("m/d/Y");?>">
        </p>
         <p>
        Email  (saves as email)
         <br> 
        <input type="text" name="testitem_0_email" value="parent1@yahoo.com">
        </p>
         <p>
        Phone Number  (formats as phone number)
         <br>
        <input type="text" name="testitem_0_phoneNumber" value="(555)444-3333">
        </p>
    
         <h3>Child 1</h3>
        <input type="hidden" name="testitem_0_testitem_1_createThing" value="N">
              <input type="hidden" name="testitem_0_testitem_1_id" value="KhPAPqGZ-0G7wwTx7-ShGVzUut">
      <p>
        Name 
         <br>
        <input type="text" name="testitem_0_testitem_1_name" value="child1">
        </p>
         <p>
        Payment (Encrypts with keyword "payment")
        <br>
        <input type="text" name="testitem_0_testitem_1_paymentInfo">
        </p>
         <p>
        Date (saves as seconds since epoch "U")
         <br>
        <input type="text" name="testitem_0_testitem_1_dateInfo" value="<?php echo date("m/d/Y");?>">
        </p>
         <p>
        Email (saves as email)
         <br> 
        <input type="text" name="testitem_0_testitem_1_email">
        </p>
         <p>
        Phone Number (formats as phone number)
         <br>
        <input type="text" name="testitem_0_testitem_1_phoneNumber" value="(555)222-1241">
        </p>
    
       <h3>Child 2</h3>
        
        <input type="hidden" name="testitem_0_testitem_1_testitem_2_createThing" value="N">
        <input type="hidden" name="testitem_0_testitem_1_testitem_2_id" value="dZFggreregreWWWs2333">
      <p>
        Name 
         <br>
        <input type="text" name="testitem_0_testitem_1_testitem_2_name" value="child2">
        </p>
         <p>
        Payment (Encrypts with keyword "payment")
        <br>
        <input type="text" name="testitem_0_testitem_1_testitem_2_paymentInfo">
        </p>
         <p>
        Date (saves as seconds since epoch "U")
         <br>
        <input type="text" name="testitem_0_testitem_1_testitem_2_dateInfo" value="<?php echo date("m/d/Y");?>">
        </p>
         <p>
        Email (saves as email)
         <br> 
        <input type="text" name="testitem_0_testitem_1_testitem_2_email">
        </p>
         <p>
        Phone Number (formats as phone number)
         <br>
        <input type="text" name="testitem_0_testitem_1_testitem_2_phoneNumber" value="(555) 554-6666">
        </p>
    
       
           <h3>Child 3</h3>
        
        <input type="hidden" name="testitem_0_testitem_1_testitem_2_testitem_3_createThing" value="N">
          <input type="hidden" name="testitem_0_testitem_1_testitem_2_testitem_3_id" value="dZFsdfgsfdgsfdsfdgfdsgfdsfsdf">
          <input type="hidden" name="testitem_0_testitem_1_testitem_2_testitem_3_deleteThing" value="Y"> 
      <p>
        Name 
         <br>
        <input type="text" name="testitem_0_testitem_1_testitem_2_testitem_3_name" value="child3">
        </p>
         <p>
        Payment (Encrypts with keyword "payment")
        <br>
        <input type="text" name="testitem_0_testitem_1_testitem_2_testitem_3_paymentInfo">
        </p>
         <p>
        Date (saves as seconds since epoch "U")
         <br>
        <input type="text" name="testitem_0_testitem_1_testitem_2_testitem_3_dateInfo" value="<?php echo date("m/d/Y");?>">
        </p>
         <p>
        Email (saves as email)
         <br> 
        <input type="text" name="testitem_0_testitem_1_testitem_2_testitem_3_email">
        </p>
         <p>
        Phone Number (formats as phone number)
         <br>
        <input type="text" name="testitem_0_testitem_1_testitem_2_testitem_3_phoneNumber" value="(555)413-3223">
        </p>
    
        
        
        <input type="submit" name="submit" value="submit">
    
    
    
</form>