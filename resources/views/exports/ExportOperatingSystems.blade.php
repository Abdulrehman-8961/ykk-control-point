<?php $sno=1;

?>	  
          <table class="table table-bordered" id="example2">
            			<thead class="thead thead-dark">
                         <tr>
            				<th>Name</th>
                        

                            
                        </tr>


            			</thead>
            	 
            	 <tbody id="showdata"> 
            	 	@foreach($qry as $q)
            	 	<tr>
            	 		 
            	 	 
                             
            	 		        <td>{{$q->operating_system_name}}</td>
               
                                                                         
                 
            	 		 
            	 	</tr>
            	 	@endforeach
            	 </tbody>
            	 </table>