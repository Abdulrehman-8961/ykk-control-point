      
 
<?php 
 
 
        $column_array= $_GET['columns'] ;
     ?>





    <?php $sno=0; ?>
                  <table class="table   table-striped table-bordered table-vcenter"  border="1" style="border-collapse:collapse;">
          

                                    <thead class="thead thead-dark">
                                     
                                            <tr>
                                               
                                                <td></td>
                                                      @if(in_array(19,$column_array))
                                                <td style="min-width:100px;width: 100px;"><strong>PN#</strong></td>
                                                @endif
                                                   @if(in_array(20,$column_array))
                                                                 <td style="min-width:100px;width:100px;"><strong>Assets</strong></td>
                                                            @endif
                                              @if(in_array(21,$column_array))
                                                <td style="min-width:100px;width:100px;"><strong>QTY</strong></td>
                                                @endif
                                                @if(in_array(22,$column_array))
                                                <td style="min-width:100px;width: 100px;"><strong>Detail</strong></td>
                                                @endif
                                                @if(in_array(23,$column_array))
                                                <td style="min-width:100px;width:100px;"><strong>MSRP</strong></td>
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
                                                             <th  data-index=3 style="min-width: 100px">Contract Type   </th>
                                                             @endif
                                                              
                                      @if(in_array(5,$column_array))
                                        <th  data-index=4 style="min-width: 100px">Vendor   </th>
                                       @endif
                                                      @if(in_array(6,$column_array))
                                                 <th  data-index=5 style="min-width: 100px" >Start Date    </th>
                                                 @endif
                                                     @if(in_array(7,$column_array))
                                                 <th  data-index=6 style="min-width: 100px" >End Date    </th>
                                                 @endif
                                                    @if(in_array(8,$column_array))
                                                <th data-index=7  style="min-width: 110px">Distributor   </th>
                                                @endif
                                                  @if(in_array(9,$column_array))
                                                     <th  data-index=8 style="min-width: 110px" >Contract #    </th>
                                                 @endif
                                                  @if(in_array(10,$column_array))
                                        <th data-index=9  style="min-width: 100px"> Description  </th>
                                                        @endif
    @if(in_array(11,$column_array))
                                        <th data-index=10  style="min-width: 100px"> End User Email</th>
                                                        @endif
     @if(in_array(12,$column_array))
                                                <th data-index=11 style="min-width: 100px">Ref #   </th>
                                                @endif
                                                    @if(in_array(13,$column_array))
                                                <th data-index=12  style="min-width: 160px">Distributor Sales #   </th>
                                                @endif
                                                      
                                                @if(in_array(14,$column_array))
                                            <th data-index=13 style="min-width:90px">Est #   </th>
                                            @endif
                                                @if(in_array(15,$column_array))
                                            <th data-index=14 style="min-width: 110px">Sales Ord #   </th>
                                            @endif
                                                @if(in_array(16,$column_array))
                                                <th data-index=15 style="min-width: 100px">Inv #   </th>
                                                @endif
                                                    @if(in_array(17,$column_array))
                                                    <th  data-index=16 style="min-width: 100px">Inv Date    </th>
                                                    @endif
                                                        @if(in_array(18,$column_array))
                                                    <th  data-index=17 style="min-width: 100px">PO #    </th>
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
                                                @if($q->contract_status=='Active')
                                                        <div class="badge badge-success">{{$q->contract_status}} 
                                                     
                                                        </div>
                                                            
                                                @elseif($q->contract_status=='Inactive')
                                                <div class="badge badge-warning">{{$q->contract_status}}/Renewed</div>
                                               
                                                          
                                                          
                                                @else
                                                <div class="badge badge-danger">{{$q->contract_status}}</div>
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
                                                   <td  data-index=4>{{$q->contract_type}}</td>
                                                   @endif
                                                    
      @if(in_array(5,$column_array))
                                                 <td  data-index=5>{{$q->vendor_name}}</td>
                                                 @endif

                                                     @if(in_array(6,$column_array))
                                               <td  data-index=6>{{date('Y-M-d',strtotime($q->contract_start_date))}}</td>
                                                @endif
                                                    @if(in_array(7,$column_array))
                                                <td  data-index=7>{{date('Y-M-d',strtotime($q->contract_end_date))}}</td>
                                                 @endif
  @if(in_array(8,$column_array))
                                                          <td  data-index=8>{{$q->distributor_name}}</td>
                                                          @endif
  @if(in_array(9,$column_array))
                                            <td  data-index=2>{{$q->contract_no}}</td>
                                            @endif
                                            
                                                              @if(in_array(10,$column_array))
                                                        <td  data-index=20>{{$q->contract_description}}</td>
                      @endif



                                            @if(in_array(11,$column_array))
                                            <td  data-index=2>{{$q->registered_email}}</td>
                                            @endif   
     @if(in_array(12,$column_array))
                                                           <td  data-index=15>{{$q->reference_no}}</td>
                                                           @endif
                                                               @if(in_array(13,$column_array))
                                                     <td  data-index=16>{{$q->distrubutor_sales_order_no}}</td>
                                                          @endif


                                                       @if(in_array(14,$column_array))
                                                    <td  data-index=17>{{$q->estimate_no}}</td>
                                                    @endif


                                                        @if(in_array(15,$column_array))
                                                     <td  data-index=9>{{$q->sales_order_no}}</td>
                                                      @endif
                                                          @if(in_array(16,$column_array))
                                                      <td  data-index=10>{{$q->invoice_no}}</td>
                                                      @endif
                                                          @if(in_array(17,$column_array))
                                                       <td  data-index=11>{{date('Y-M-d',strtotime($q->invoice_date))}}</td>
                                                        @endif
                                                            @if(in_array(18,$column_array))
                                                        <td  data-index=12>{{$q->po_no}}</td>
                                                  @endif
    
                                         
                                        </tr>


      <?php $details=DB::Table('contract_details')->where('contract_id',$q->id)->where('is_deleted',0)->get(); $count=0;

 
            ?>
    @if(in_array(19,$column_array) || in_array(20,$column_array) || in_array(21,$column_array) || in_array(22,$column_array) )
            @foreach($details as $key=>$d)
            <?php $count++; 
                $asset=DB::Table('contract_assets as ca')->selectRaw('Group_Concat(a.hostname) as asset_name')->leftjoin('assets as  a','a.id','=','ca.hostname')->where('ca.contract_detail_id',$d->contract_detail_id)->where('ca.is_deleted',0) ->first();
            ?>



                                                            <tr>
                                              
                                                <td><strong>DETAIL {{$key+1}}</strong></td>
                                                            @if(in_array(19,$column_array))
                                                <td data="18">{{$d->pn_no}}  </td>
                                                    @endif
                                                                @if(in_array(20,$column_array))
                                                      <td data="19"> 
                                                       @if($asset!='')
                  <p >{{$asset->asset_name}}</p> 
             s
                @endif</td>
                @endif
                            @if(in_array(21,$column_array))
                                                <td data="20">{{$d->qty}}     </td>
                                                    @endif
                                                             @if(in_array(22,$column_array))
                                                    <td data="21">{{$d->detail_comments}}</td>
                                                    @endif 
                                                                @if(in_array(23,$column_array))
                                                <td data="22">{{$d->msrp}}    </td>
                                                @endif
                                            
                                                 
                                               
                                                  
                                            </tr>
                                            @endforeach
                                            @endif
                                    @endforeach
                                    </tbody>
                                </table>
                            