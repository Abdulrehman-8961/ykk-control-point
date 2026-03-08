      
 
<?php 
 
 
        $column_array= $_GET['columns'] ;
     ?>





    <?php $sno=0; ?>
                  <table class="table   table-striped table-bordered table-vcenter"  border="1" style="border-collapse:collapse;">
          

                                    <thead class="thead thead-dark">
                                     <tr>
                                            

                        @if(in_array(0,$column_array))
                                 <th data-index=0 style="min-width:70px">#    </th>
                                    @endif
                                  @if(in_array(1,$column_array))
                                        <th  data-index=1 style="min-width: 100px" >Status    </th>
                                                 @endif
                                                       @if(in_array(2,$column_array))
                                                             <th  data-index=3 style="min-width: 100px">Asset Type   </th>
                                                             @endif
                                                    @if(in_array(3,$column_array))
                                            <th data-index=1  style="min-width: 130px">Client     </th>
                                            @endif
                                                @if(in_array(4,$column_array))
                                            <th data-index=2 style="min-width: 100px">Site      </th>
                                            @endif
                                        
                                                              
                                      @if(in_array(5,$column_array))
                                        <th  data-index=4 style="min-width: 100px">Location   </th>
                                       @endif
                                                      @if(in_array(6,$column_array))
                                                 <th  data-index=5 style="min-width: 100px" >Hostname    </th>
                                                 @endif
                                                     @if(in_array(7,$column_array))
                                                 <th  data-index=6 style="min-width: 100px" >Domain    </th>
                                                 @endif
                                            
                                                  @if(in_array(8,$column_array))
                                        <th data-index=9  style="min-width: 100px"> Role/Description  </th>
                                                        @endif
    @if(in_array(9,$column_array))
                                        <th data-index=10  style="min-width: 100px"> O/S</th>
                                                        @endif
     @if(in_array(10,$column_array))
                                                <th data-index=11 style="min-width: 100px">Environment   </th>
                                                @endif
                                                    @if(in_array(11,$column_array))
                                                <th data-index=12  style="min-width: 160px">DR Plan   </th>
                                                @endif
                                                      
                                                @if(in_array(12,$column_array))
                                            <th data-index=13 style="min-width:90px">Clustered   </th>
                                            @endif
                                                @if(in_array(13,$column_array))
                                            <th data-index=14 style="min-width: 110px">Internet Facing   </th>
                                            @endif
                                                @if(in_array(14,$column_array))
                                                <th data-index=15 style="min-width: 100px">Load Balanced   </th>
                                                @endif
                                                  
                                                       @if(in_array(15,$column_array))
                                                    <th  data-index=17 style="min-width: 100px">VLAN ID    </th>
                                                    @endif
                                                       @if(in_array(16,$column_array))
                                                    <th  data-index=17 style="min-width: 100px">Primary IP    </th>
                                                    @endif
                                                       @if(in_array(17,$column_array))
                                                    <th  data-index=17 style="min-width: 100px">Additional IP Label    </th>
                                                    @endif
                                                       @if(in_array(18,$column_array))
                                                    <th  data-index=17 style="min-width: 100px">Additional IP Value    </th>
                                                    @endif
                                                       @if(in_array(19,$column_array))
                                                    <th  data-index=17 style="min-width: 100px">vCPU    </th>
                                                    @endif
                                                       @if(in_array(20,$column_array))
                                                    <th  data-index=17 style="min-width: 100px">Memory    </th>
                                                    @endif
                                                       @if(in_array(21,$column_array))
                                                    <th  data-index=17 style="min-width: 100px">SSL Certificate    </th>
                                                    @endif
                                                       @if(in_array(22,$column_array))
                                                    <th  data-index=17 style="min-width: 100px">Support    </th>
                                                    @endif
                                                       @if(in_array(23,$column_array))
                                                    <th  data-index=17 style="min-width: 100px">Managed    </th>
                                                    @endif
                                                       @if(in_array(24,$column_array))
                                                    <th  data-index=17 style="min-width: 100px">App Owner    </th>
                                                    @endif
                                                       @if(in_array(25,$column_array))
                                                    <th  data-index=17 style="min-width: 100px">SLA    </th>
                                                    @endif
                                                       @if(in_array(26,$column_array))
                                                    <th  data-index=17 style="min-width: 100px">Patched    </th>
                                                    @endif
                                                       @if(in_array(27,$column_array))
                                                    <th  data-index=17 style="min-width: 100px">Monitored    </th>
                                                    @endif
                                                       @if(in_array(28,$column_array))
                                                    <th  data-index=17 style="min-width: 100px">Backup    </th>
                                                    @endif
                                                       @if(in_array(29,$column_array))
                                                    <th  data-index=17 style="min-width: 100px">Anti-Virus    </th>
                                                    @endif
                                                       @if(in_array(30,$column_array))
                                                    <th  data-index=17 style="min-width: 100px">Replicated    </th>
                                                    @endif
                                                       @if(in_array(31,$column_array))
                                                    <th  data-index=17 style="min-width: 100px">Vulnerability Scan    </th>
                                                    @endif

    @if(in_array(32,$column_array))
                                                    <th  data-index=17 style="min-width: 100px">SIEM      </th>
                                                    @endif
    @if(in_array(33,$column_array))
                                                    <th  data-index=17 style="min-width: 100px">SMTP      </th>
                                                    @endif


                                                     
                                           
                                         
                                        </tr>
                                    </thead>
                                    <tbody id="showdata">
                                       

                                        @foreach($qry as $q)

                                      
                                     
                                        <tr data="{{$q->id}}" >
        <?php $ssl=DB::table('ssl_certificate as s')->leftjoin('vendors as v','v.id','=','s.cert_issuer')->where('cert_hostname',$q->id)->where('s.is_deleted',0)->first();
                                                     $cert='';
                                                      ?>
                                               @if(@$ssl->cert_type=='internal')
                                                 <?php $cert='Internal Cert';?>
                                          @elseif(@$ssl->cert_type=='public')
                                                <?php $cert=@$ssl->vendor_name.($ssl->vendor_name!=''?'Public Cert':'');?> 
                                          @endif  
 
                                           @if(in_array(1,$column_array))
                                        <td  data-index=42>
                                                @if($q->AssetStatus==1)
                                                    <span class="badge badge-success">Yes</span>
                                                    @else
                                                    <span class="badge badge-danger">No</span>
                                                    @endif
                                            </td>
                                            @endif

                                            <!--  <td data-index="45" class="text-center">
                                                    </td> -->

     @if(in_array(2,$column_array))
                                        <td  data-index=42>
                                              {{$q->asset_type_description}}
                                            </td>
                                            @endif
      
                                                    @if(in_array(3,$column_array))
                                                       <td  data-index=1 class="font-w600">
                                                {{$q->firstname}} 
                                            </td>
                                            @endif
                                                @if(in_array(4,$column_array))
                                            <td  data-index=2>{{$q->site_name}}</td>
                                            @endif
                                                @if(in_array(5,$column_array))
                                                 <td  data-index=3>{{$q->location}}</td>
                                                 @endif
  @if(in_array(6,$column_array))
                                                        <td  data-index=7>{{$q->hostname}}</td>
@endif
  @if(in_array(7,$column_array))
                                                        <td  data-index=7>{{$q->domain_name}}</td>
@endif

 
 
  @if(in_array(8,$column_array))
        <td  data-index=10>{{$q->role}}</td>
 @endif

                                                   
                                                     @if(in_array(9,$column_array))
          <td  data-index=12>{{$q->operating_system_name}}</td>
 @endif


   @if(in_array(10,$column_array))
    <td  data-index=11>{{$q->use_}}</td>
                                                    
 @endif
    @if(in_array(11,$column_array))
   <td  data-index=4>   @if($q->disaster_recovery==1  && $q->managed==1)
                                                    <span class="badge badge-success">Yes</span>
                                                     @elseif($q->disaster_recovery==2  || $q->managed!=1)
                                                         <span class="badge badge-secondary">N/A</span>
                                                         @else
                                                    <span class="badge badge-danger">No</span>
                                                    @endif
                                                </td>
  @endif
     @if(in_array(12,$column_array))
  <td  data-index=20>
                                                @if($q->clustered==1)
                                                    <span class="badge badge-success">Yes</span>
                                                    @elseif($q->clustered==2)
                                                         <span class="badge badge-secondary">N/A</span>
                                                          @else
                                                    <span class="badge badge-danger">No</span>
                                                    @endif
                                            </td>

     @endif
                                                       @if(in_array(13,$column_array)) 
                                                      <td  data-index=17>
                                                @if($q->internet_facing==1)
                                                    <span class="badge badge-success">Yes</span>
                                                     @elseif($q->internet_facing==2)
                                                         <span class="badge badge-secondary">N/A</span>
                                                         @else
                                                    <span class="badge badge-danger">No</span>
                                                    @endif
                                            </td>
                                                 @endif
                                                      @if(in_array(14,$column_array))
               
<td  data-index=19>
                                                @if($q->load_balancing==1)
                                                    <span class="badge badge-success">Yes</span>
                                                     @elseif($q->load_balancing==2)
                                                         <span class="badge badge-secondary">N/A</span>
                                                         @else
                                                    <span class="badge badge-danger">No</span>
                                                    @endif
                                            </td>
                                             @endif
                                            
         @if(in_array(15,$column_array))
  <td  data-index=6>{{$q->vlanId}}</td>
        @endif
           @if(in_array(16,$column_array))
  <td  data-index=6>{{$q->ip_address}}</td>
        @endif
        <?php
        $id=$q->id;
         $ip=DB::select("select group_concat(ip_address_name) as ip_address_name,group_concat(ip_address_value) as ip_address_value from asset_ip_addresses where asset_id='$id' ") ?>
           @if(in_array(17,$column_array))
  <td  data-index=6>{{@$ip[0]->ip_address_name}}</td>
        @endif
        @if(in_array(18,$column_array))
  <td  data-index=6>{{@$ip[0]->ip_address_value}}</td>
        @endif
                                     
      @if(in_array(19,$column_array))
  <td  data-index=6>{{$q->vcpu}} </td>
        @endif
          

      @if(in_array(20,$column_array))
  <td  data-index=6>{{$q->memory}}</td>
        @endif                                
                       @if(in_array(21,$column_array))
  <td  data-index=6>@if($q->ssl_certificate_status=='N/A')
                                                        <span class="badge text-white bg-secondary">
                                                               {{$q->ssl_certificate_status}}
                                                        </span>
                                                        @elseif($q->ssl_certificate_status=='Active')
                                                        <span class="badge badge-success">
                                                               {{$q->ssl_certificate_status}}
                                                        </span>
                                                        @elseif($q->ssl_certificate_status=='Unassigned')
                                                        <span class="badge text-white bg-orange">
                                                               {{$q->ssl_certificate_status}}
                                                        </span>
                                                        @elseif($q->ssl_certificate_status=='Expired/Ended')
                                                            <span class="badge badge-danger">
                                                                   {{$q->ssl_certificate_status}}
                                                        </span>
                                                        @else
                                                                <span class="badge text-white bg-secondary">
                                                               N/A
                                                        </span>
                                                        @endif </td>
        @endif                                  


  @if(in_array(22,$column_array))
  <td  data-index=6>      @if($q->SupportStatus=='N/A')
                                                        <span class="badge text-white bg-secondary">
                                                               {{$q->SupportStatus}}
                                                        </span>
                                                        @elseif($q->SupportStatus=='Supported')
                                                        <span class="badge badge-success">
                                                               {{$q->SupportStatus}}
                                                        </span>
                                                        @elseif($q->SupportStatus=='Unassigned')
                                                        <span class="badge text-white bg-orange">
                                                               {{$q->SupportStatus}}
                                                        </span>
                                                        @elseif($q->SupportStatus=='Expired')
                                                            <span class="badge badge-danger">
                                                                   {{$q->SupportStatus}}
                                                        </span>
                                                                   @else
                                                                <span class="badge text-white bg-secondary">
                                                             N/A
                                                        </span>

                                                        @endif</td>
        @endif   
  @if(in_array(23,$column_array))
                                                   <td  data-index=11>   
                                                    @if($q->managed==1)
                                                    <span class="badge badge-success">Yes</span>
                                                    
                                                         @else
                                                    <span class="badge badge-danger">No</span>
                                                    @endif</td>
                     @endif   
                                         @if(in_array(24,$column_array))    
                                                <td  data-index=12>{{$q->app_owner}}</td>
                                                       @endif  
                                   @if(in_array(25,$column_array))    
                                                <td  data-index=12>{{$q->sla}}</td>
                                        @endif 




                                           @if(in_array(26,$column_array))    
                                                <td  data-index=12>     @if($q->patched==1  && $q->managed==1)
                                                    <span class="badge badge-success">Yes</span>
                                                     @elseif($q->patched==2  || $q->managed!=1)
                                                         <span class="badge badge-secondary">N/A</span>
                                                         @else
                                                    <span class="badge badge-danger">No</span>
                                                    @endif</td>
                                        @endif 

   @if(in_array(27,$column_array))    
                                                <td  data-index=12>   @if($q->monitored==1  && $q->managed==1)
                                                    <span class="badge badge-success">Yes</span>
                                                     @elseif($q->monitored==2  || $q->managed!=1)
                                                         <span class="badge badge-secondary">N/A</span>
                                                         @else
                                                    <span class="badge badge-danger">No</span>
                                                    @endif</td>
                                        @endif 
 @if(in_array(28,$column_array))    
   <td  data-index=8>    @if($q->backup==1  && $q->managed==1)
                                                    <span class="badge badge-success">Yes</span>
                                                    @elseif($q->backup==2  || $q->managed!=1)
                                                         <span class="badge badge-secondary">N/A</span> @else
                                                    <span class="badge badge-danger">No</span>
                                                    @endif</td>

    @endif 


 @if(in_array(29,$column_array))    
   <td  data-index=8>
        @if($q->antivirus==1  && $q->managed==1)
                                                    <span class="badge badge-success">Yes</span>
                                                      @elseif($q->antivirus==2  || $q->managed!=1)
                                                         <span class="badge badge-secondary">N/A</span>
                                                        @else
                                                    <span class="badge badge-danger">No</span>
                                                    @endif
                                                </td>

  @endif 

 @if(in_array(30,$column_array))    

                             <td  data-index=15>  @if($q->replicated==1  && $q->managed==1)
                                                    <span class="badge badge-success">Yes</span>
                                                    @elseif($q->replicated==2  || $q->managed!=1)
                                                         <span class="badge badge-secondary">N/A</span>
                                                           @else
                                                    <span class="badge badge-danger">No</span>
                                                    @endif</td>

                                                      @endif 
     @if(in_array(31,$column_array))   
       <td  data-index=14>@if($q->disaster_recovery==1  && $q->managed==1)
                                                    <span class="badge badge-success">Yes</span>
                                                     @elseif($q->disaster_recovery==2  || $q->managed!=1)
                                                         <span class="badge badge-secondary">N/A</span>
                                                         @else
                                                    <span class="badge badge-danger">No</span>
                                                    @endif</td>
               @endif 
                 @if(in_array(32,$column_array))   
          <td  data-index=14>  @if($q->syslog==1  && $q->managed==1)
                                                    <span class="badge badge-success">Yes</span>
                                                    @elseif($q->syslog==2  || $q->managed!=1)
                                                         <span class="badge badge-secondary">N/A</span>
                                                    @else
                                                    <span class="badge badge-danger">No</span>
                                                    @endif
</td>
               @endif 


        @if(in_array(33,$column_array))   
          <td  data-index=14>
      @if($q->smtp==1  && $q->managed==1)
                                                    <span class="badge badge-success">Yes</span>
                                                      @elseif($q->smtp==2  || $q->managed!=1)
                                                         <span class="badge badge-secondary">N/A</span>
                                                    @else
                                                    <span class="badge badge-danger">No</span>
                                                    @endif
</td>
@endif
       




 
                                   
                                        </tr>

                                    @endforeach
                                    </tbody>
                                </table>
                            