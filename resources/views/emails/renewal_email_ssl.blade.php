<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@300;400;500;600;700;800&family=Source+Sans+Pro:wght@200;300;400;600;700;900&display=swap" rel="stylesheet">

<style type="text/css">
  *{
    font-family: Source Sans Pro;
  }
</style>
 
 <h4>Cert Name :   {{$data['cert_name']}} <a href="{{url('ssl-certificate')}}?key={{$hash}}">{{$data['cert_name']}} (Click to view details)</a></h4> 
   <h4>Expiry Date:  {{date('Y/M/d',strtotime($data['cert_edate']))}}</h4>

  