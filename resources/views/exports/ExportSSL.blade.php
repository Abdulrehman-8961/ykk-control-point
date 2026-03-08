      
 
<?php 
 
 
        $column_array= $_GET['columns'] ;
  $sno=0;
   ?>
                  <table class="table   table-striped table-bordered table-vcenter"  border="1" style="border-collapse:collapse;">
          

                                    <thead class="thead thead-dark">
                                     
                                            <tr>
                                               
                                                <td></td>
                                                        @if(in_array(16,$column_array))
                                                <td style="min-width:100px;width: 100px;"><strong>SAN </strong></td>
                                                @endif
                                                     @if(in_array(17,$column_array))
                                                                 <td style="min-width:100px;width:100px;">SAN Type<strong> </strong></td>
                                                                 @endif
                                                        
                                            
                                            </tr>
                                              <tr>
                                               
                                                <td></td>
                                                   
                                                <td style="min-width:100px;width: 100px;"><strong> </strong></td>
                                               
                                                                 <td style="min-width:100px;width:100px;"><strong> </strong></td>
                                                        
                                              @if(in_array(18,$column_array))
                                                <td style="min-width:100px;width:100px;"><strong>SAN Hostname</strong></td>
                                                @endif
                                               
                                                        @if(in_array(19,$column_array))
                                                <td style="min-width:100px;width:100px;"><strong>Ip Address Name</strong></td>
                                                @endif
                                                        @if(in_array(20,$column_array))
                                                <td style="min-width:100px;width:100px;"><strong>Ip Address Value</strong></td>
                                                @endif
                                            </tr>

                                      <tr>
                                             
   
                        @if(in_array(0,$column_array))
                                 <th data-index=0 style="min-width:70px">#    </th>
                                    @endif
                                  @if(in_array(1,$column_array))
                                        <th  data-index=1 style="min-width: 100px" >Status    </th>
                                                 @endif
                                                    @if(in_array(2,$column_array))
                                            <th data-index=1  style="min-width: 130px">Client     </th>
                                            @endif
                                                @if(in_array(3,$column_array))
                                            <th data-index=2 style="min-width: 100px">Site      </th>
                                            @endif
                                              @if(in_array(4,$column_array))
                                                             <th  data-index=3 style="min-width: 100px">Cert Type   </th>
                                                             @endif
                                                              
                                      @if(in_array(5,$column_array))
                                        <th  data-index=4 style="min-width: 100px">Issuer   </th>
                                       @endif
                                                      @if(in_array(6,$column_array))
                                                 <th  data-index=5 style="min-width: 100px" >Start Date    </th>
                                                 @endif
                                                     @if(in_array(7,$column_array))
                                                 <th  data-index=6 style="min-width: 100px" >End Date    </th>
                                                 @endif
                                                    @if(in_array(8,$column_array))
                                                <th data-index=7  style="min-width: 110px">Description   </th>
                                                @endif
                                                  @if(in_array(9,$column_array))
                                                     <th  data-index=8 style="min-width: 110px" >Name (CN)     </th>
                                                 @endif
                                                  @if(in_array(10,$column_array))
                                        <th data-index=9  style="min-width: 100px"> Company (O) </th>
                                                        @endif
    @if(in_array(11,$column_array))
                                        <th data-index=10  style="min-width: 100px"> Locality (L)</th>
                                                        @endif

                                                           @if(in_array(12,$column_array))
                                        <th data-index=10  style="min-width: 100px"> Country (C)</th>
                                                        @endif
     @if(in_array(13,$column_array))
                                                <th data-index=11 style="min-width: 100px">Department (OU)  </th>
                                                @endif
                                                    @if(in_array(14,$column_array))
                                                <th data-index=12  style="min-width: 160px">State (S)  </th>
                                                @endif
                                                      
                                                @if(in_array(15,$column_array))
                                            <th data-index=13 style="min-width:90px">Email (e) </th>
                                            @endif
                                            

                                               
                                           
                                         
                                        </tr>
                                    </thead>
                                    <tbody id="showdata">
                                       

                                        @foreach($qry as $q)

                                      
                                        <tr data="{{$q->id}}" >
                                               @if(in_array(0,$column_array))
                                             <td  data-index=0>{{++$sno}}</td>
                                        @endif
                                            @if(in_array(1,$column_array))
                        <td  data-index=1 class="font-w600">
                                                @if($q->cert_status=='Active')
                                                        <div class="badge badge-success">{{$q->cert_status}} 
                                                     
                                                        </div>
                                                            
                                                @elseif($q->cert_status=='Inactive')
                                                <div class="badge badge-warning">{{$q->cert_status}}/Renewed</div>
                                               
                                                          
                                                          
                                                @else
                                                <div class="badge badge-danger">{{$q->cert_status}}</div>
                                                @endif

                                                
                                            </td>
                                            @endif

                                                   @if(in_array(2,$column_array))
                                                  <td  data-index=2>{{$q->firstname}}</td>
                                                  @endif
                                                      @if(in_array(3,$column_array))
                                                   <td  data-index=3>{{$q->site_name}}</td>
                                                   @endif
                                                      @if(in_array(4,$column_array))
                                                   <td  data-index=4>{{$q->cert_type}}</td>
                                                   @endif
                                                    
      @if(in_array(5,$column_array))
                                                 <td  data-index=5>{{$q->vendor_name}}</td>
                                                 @endif

                                                     @if(in_array(6,$column_array))
                                               <td  data-index=6>{{date('Y-M-d',strtotime($q->cert_rdate))}}</td>
                                                @endif
                                                    @if(in_array(7,$column_array))
                                                <td  data-index=7>{{date('Y-M-d',strtotime($q->cert_edate))}}</td>
                                                 @endif

  @if(in_array(8,$column_array))
                                            <td  data-index=2>{{$q->description}}</td>
                                            @endif
                                            
                                                              @if(in_array(9,$column_array))
                                                        <td  data-index=20>{{$q->cert_name}}</td>
                      @endif



                                            @if(in_array(10,$column_array))
                                            <td  data-index=2>{{$q->cert_company}}</td>
                                            @endif   
     @if(in_array(11,$column_array))
                                                           <td  data-index=15>{{$q->cert_city}}</td>
                                                           @endif

                                                                @if(in_array(12,$column_array))
                                                           <td  data-index=15>{{$q->cert_country}}</td>
                                                           @endif
     @if(in_array(13,$column_array))
                                                     <td  data-index=16>{{$q->cert_department}}</td>
                                                          @endif


                                                       @if(in_array(14,$column_array))
                                                    <td  data-index=17>{{$q->cert_state}}</td>
                                                    @endif


                                                        @if(in_array(15,$column_array))
                                                     <td  data-index=9>{{$q->cert_email}}</td>
                                                      @endif
                                           
                                                
    
                                         
                                        </tr>

  <?php    $line_items=DB::table('ssl_san as s')   ->where('s.ssl_id',$q->id)->get();
 
            ?>
    @if(in_array(17,$column_array) || in_array(18,$column_array)  )
            @foreach($line_items as $key=>$d)
      



                                                            <tr>
                                              
                                                <td><strong>SAN {{$key+1}}</strong></td>
                                                      
                                            
           
                            @if(in_array(16,$column_array))
                                                <td data="16">{{$d->san}}     </td>
                           @endif
                     
           
                            @if(in_array(17,$column_array))
                                                <td data="17">{{$d->san_type}}     </td>
                           @endif
                                                 
                                                  
                                            </tr>
                                            @endforeach
                                            @endif
      <?php    $line_items=DB::table('ssl_host as s')->select('*','s.id as sid','s.hostname as host','a.AssetStatus','a.id as aid','a.hostname as asset_name','a.ip_address')->leftjoin('assets as a','a.id','=','s.hostname')->leftjoin('asset_type as at','a.asset_type_id','=','at.asset_type_id')->where('s.ssl_id',$q->id)->get();
 
            ?>
    @if( in_array(19,$column_array) || in_array(20,$column_array)|| in_array(21,$column_array) )
            @foreach($line_items as $key=>$d)
      



                                                            <tr>
                                              
                                                <td><strong>HOST {{$key+1}}</strong></td>
                                                      
                                                <td data="18"> </td>
                                               
                     <td data="19"> </td>
           
                            @if(in_array(18,$column_array))
                                                <td data="20">{{$d->asset_name}}     </td>
                           @endif

                                                   <?php 


  

                $ip_array=explode(',',$d->ip_id);
            $ip=DB::Table('asset_ip_addresses')->whereIn('id',$ip_array)->orderby ('ip_address_name','asc')->get();
    $h='';
   if(@$ip_array[0]==''){
    $h.=$d->ip_address.',';
 }
  foreach($ip as $i){

$h.=$i->ip_address_value.',';
   
}
   ?>
                                                 
                                                         @if(in_array(19,$column_array))
                                                       <td  data-index=11>{{$d->ip_name}}</td>
                                                        @endif

                                                         @if(in_array(20,$column_array))
                                                        <td  data-index=12>{{$h}}</td>
                                                  @endif
                                                  
                                            </tr>
                                            @endforeach
                                            @endif
                                    @endforeach
                                    </tbody>
                                </table>
                            