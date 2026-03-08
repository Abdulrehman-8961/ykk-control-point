   
<?php 
 
 
        $column_array= $_GET['columns'] ;
     ?>

   <table class="table table-bordered">
                                        <thead class="bg-body">
                                              <tr>
                                    @if(in_array(1,$column_array))
                                      <th> Client </th>
                                      @endif
                                      @if(in_array(2,$column_array))
                                       <th> Site </th>
                                        
                                                    @endif
                                      @if(in_array(3,$column_array))
                                            <th> Country </th>
                                                      @endif
                                      @if(in_array(4,$column_array))
                                                <th> Address </th>
                                                          @endif
                                      @if(in_array(5,$column_array))
                           
                                                <th> City </th>
                                                          @endif
                                      @if(in_array(6,$column_array))
                                                <th> Province </th>
                                                          @endif
                                      @if(in_array(7,$column_array))
                                                <th> Zip Code </th>
                                                          @endif
                                      @if(in_array(8,$column_array))
                                                <th> Phone 1</th>
                                                          @endif
                                      @if(in_array(9,$column_array))
                                        <th> Phone 2 </th>
                                                  @endif
                                    
                                   
                                               
                                           
                                            
                                        </tr>
                                        </thead>
                                      <tbody id="showdata">
                                          @php  $sno=0; @endphp
                                        @foreach($qry as $q)
                                        <tr> 
                                      @if(in_array(1,$column_array))
                                       
                                            <td>{{$q->firstname}}</td>

          @endif
                                      @if(in_array(2,$column_array))
                                            
                                            <td class="font-w600">
                                                 {{$q->site_name}}  
                                            </td>
                                    
                                              @endif
                                      @if(in_array(3,$column_array))
                                                    <td>{{$q->country}}</td>
                                                              @endif
                                      @if(in_array(4,$column_array))
                                                      <td>{{$q->address}}</td>
                                                                @endif
                                      @if(in_array(5,$column_array))
                                                
                                          <td>{{$q->city}}</td>
                                                    @endif
                                      @if(in_array(6,$column_array))
                                          <td>{{$q->province}}</td>
                                                    @endif
                                      @if(in_array(7,$column_array))
                                          <td>{{$q->zip_code}}</td>
                                                    @endif
                                      @if(in_array(8,$column_array))
                                          <td>{{$q->phone}}</td>
                                                    @endif
                                      @if(in_array(9,$column_array))
                                          <td>{{$q->fax}}</td>
          @endif
                                        </tr>
                                    @endforeach
                                    </tbody>
                                    </table>