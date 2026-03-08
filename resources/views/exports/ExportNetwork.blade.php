      
 
<?php 
 
 
        $column_array= $_GET['columns'] ;
     ?>



<table class="table   table-striped table-vcenter">
                                    <thead class="thead thead-dark">
                                        <tr>
                           @if(in_array(1,$column_array))
  <th>Client</th>
  @endif
            @if(in_array(1,$column_array))
      <th>Site</th>
         @endif
            @if(in_array(2,$column_array))                                    
   <th>Description</th>
     @endif
            @if(in_array(3,$column_array))
      <th>Zone  </th>
        @endif
            @if(in_array(4,$column_array))
         <th>Internet Facing  </th>
           @endif
            @if(in_array(5,$column_array))
   <th>vlanId  </th>
     @endif
            @if(in_array(6,$column_array))

   <th>Subnet IP  </th>
     @endif
            @if(in_array(7,$column_array))
      <th>Gateway IP  </th>
        @endif
            @if(in_array(8,$column_array))
   <th>Mask  </th>
     @endif
            @if(in_array(9,$column_array))
<th>Wifi Enabled  </th>
  @endif
            @if(in_array(10,$column_array))
   <th>SSID Name  </th>
     @endif
            @if(in_array(11,$column_array))
<th>Encryption</th>
  @endif
            @if(in_array(12,$column_array))
<th>Sign On Method  </th>
  @endif
            @if(in_array(13,$column_array))
<th>Certificate</th>
@endif 
                                        </tr>
                                    </thead>

                                    <tbody id="showdata">
                                          @php  $sno= 0;@endphp
                                        @foreach($qry as $q)
                                        <tr>
                                            @if(in_array(1,$column_array))
                                            <td class="font-w600">
                                                  {{$q->firstname}}   
                                            </td>
                                            @endif
                                               @if(in_array(2,$column_array))
                                               <td>{{$q->site_name}}</td>
                                                 @endif
                                               @if(in_array(3,$column_array))
                                                          <td>{{$q->description}}</td>
                                                            @endif
                                               @if(in_array(4,$column_array))
                                        <td>{{$q->zone}}</td>    
                                          @endif
                                               @if(in_array(5,$column_array))
                                                   <td>{{$q->internet_facing==1?'Yes':'No'}}</td>
                                                     @endif
                                               @if(in_array(6,$column_array))
                                         
                                         <td>{{$q->vlan_id}}</td>
                                           @endif
                                               @if(in_array(7,$column_array))
                                                                              
                                        <td>{{$q->subnet_ip}}</td>
                                          @endif
                                               @if(in_array(8,$column_array))
                                        <td>{{$q->gateway_ip}}</td>
                                          @endif
                                               @if(in_array(9,$column_array))
                                        <td>{{$q->mask}}</td>
                                          @endif
                                               @if(in_array(10,$column_array))
                                        <td>{{$q->wifi_enabled==1?'Yes':'No'}}</td>
                                          @endif
                                             @if(in_array(11,$column_array))
                                        <td>{{$q->ssid_name}}</td>
                                       
                                        @endif
                                               @if(in_array(12,$column_array))
                                        <td>{{$q->encryption}}</td>
                                          @endif
                                               @if(in_array(13,$column_array))
                                        <td>{{$q->sign_in_method}}</td>
                                          @endif
                                               @if(in_array(14,$column_array))
                                        <td>{{$q->certificate==1?'Yes':'No'}}</td>
                                          @endif
                                            
                                            
                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>