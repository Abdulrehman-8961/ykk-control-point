<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@300;400;500;600;700;800&family=Source+Sans+Pro:wght@200;300;400;600;700;900&display=swap" rel="stylesheet">

<style type="text/css">
  *{
    font-family: Source Sans Pro;
  }
</style>
<?php

   $hash=uniqid().Hash::make(time().rand(1,100000000000000));
                    DB::table('contract_sharable_links')->insert([
                            'contract_id'=>$data['contract_id'],
                            'hash'=>$hash,
                            'expiry_date'=>date('Y-m-d',strtotime('+3 days'))
                ]);
                ?>
 <h4>Contract Number :  <a href="{{url('contract')}}?key={{$hash}}">{{$data['contract_no']}} (Click to view details)</a></h4>
  <h4>Vendor :  {{$data['vendor_name']}}</h4>
   <h4>End Date :  {{date('Y/M/d',strtotime($data['end_date']))}}</h4>

  