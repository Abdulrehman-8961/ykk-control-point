<?php $sno=1;

?>	  
          <table class="table table-bordered" id="example2">
            			<thead class="thead thead-dark">
                         <tr>
            				<th>Salutation</th>
                            <th>Firstname</th>
                            <th>Lastname</th>
                                <th>Client Display Name</th>
                            <th>Company Name</th>
            			
                             <th>Email Address</th>
                            <th>Work Phone</th>
                            <th>Mobile</th>
                            <th>Website</th>
                            <th>Renewal Notification</th>
                            <th>Renewal Notification Email</th>
                            <th>Active</th>
                           

                            
                        </tr>


            			</thead>
            	 
            	 <tbody id="showdata"> 
            	 	@foreach($qry as $q)
            	 	<tr>
            	 		 
            	 	 
                             
            	 		        <td>{{$q->salutation}}</td>
                                <td>{{$q->firstname}}</td>
                                <td>{{$q->lastname}}</td>
                                         <td>{{$q->firstname}}</td>
                                <td>{{$q->company_name}}</td>
                       
                                <td>{{$q->email_address}}</td>
                                <td>{{$q->work_phone}}</td>
                              
                                <td>{{$q->mobile}}</td>
                                <td>{{$q->website}}</td>
                                <td>{{$q->renewal_notification==1?'On':'Off'}}</td>
                                <td>{{$q->renewal_notification_email}}</td>
                                <td>{{$q->client_status==1?'active':'Inactive'}}</td>
                              

                                                                         
                 
            	 		 
            	 	</tr>
            	 	@endforeach
            	 </tbody>
            	 </table>