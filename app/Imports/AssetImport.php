<?php
namespace App\Imports;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Row;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\OnEachRow;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithStartRow;
use Carbon\Carbon;
use DB;
class AssetImport implements ToCollection,WithStartRow{
 public $data;
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
      public function startRow(): int
    {
        return 2;
    }
    public function collection(Collection $rows)
   {
        	 
            
$array=array();
 
 

        foreach($rows as $r){

            $clients=DB::table('clients')->where('firstname',trim($r[1]))->where('is_deleted',0)->first();
            $sites=DB::table('sites')->where('site_name',trim($r[2]))->where('is_deleted',0)->first();
            $domain=DB::table('domains')->where('domain_name',trim($r[8]))->where('is_deleted',0)->first();
            $manufacturer=DB::table('vendors')->where('vendor_name',trim($r[3]))->where('is_deleted',0)->first();
             $os=DB::table('operating_systems')->where('operating_system_name',trim($r[11]))->where('is_deleted',0)->first();


            if($clients!=''){
              DB::Table('assets')->insert(['asset_type'=>$r[0],'client_id'=>@$clients->id,'site_id'=>@$sites->id,'manufacturer'=>@$manufacturer->id,'model'=>$r[4],'type'=>$r[5],'sn'=>$r[6],'hostname'=>$r[7],'domain'=>@$domain->id,'fqdn'=>$r[7].'.'.$r[8],'role'=>$r[9],'use_'=>$r[10],'os'=>@$os->id,'app_owner'=>$r[12],'ip_address'=>$r[13],'vlan_id'=>$r[14],'network_zone'=>$r[15],'internet_facing'=>$r[16],'disaster_recovery'=>$r[17],'load_balancing'=>$r[18],'clustered'=>$r[19],'monitored'=>$r[20],'patched'=>$r[21],'antivirus'=>$r[22],'backup'=>$r[23],'replicated'=>$r[24],'smtp'=>$r[25],'ntp'=>$r[26],'syslog'=>$r[27],'HasWarranty'=>$r[28],'AssetStatus'=>$r[29],'SLA'=>$r[30],'cpu_model'=>$r[31],'cpu_sockets'=>$r[33],'cpu_cores'=>$r[34],'cpu_freq'=>$r[35],'cpu_hyperthreadings'=>$r[36],'cpu_total_cores'=>$r[33]*$r[34],'memory'=>$r[36],'comments'=>$r[37]  ]);
          
        }
        else{
            if(!in_array($r[1],$array)){

            $array[]=$r[1];
        
        }
        }
        
        }

    $this->data = $array;
    return 1;
        // return new Availibility([

        //     'class_id'     => $row[0],
        //     'date'    => $date,
        //     'location_id' => $row[2],
        //     'class_limit' => $row[3],
        //     'time' =>$time,
            
        //]);
              
    }

}