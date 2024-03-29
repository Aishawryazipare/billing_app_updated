<?php

namespace App\Http\Controllers;
use App\Item;
use App\BillDetail;
use App\EnquiryLocation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            $this->user= Auth::user();
            $this->admin = Auth::guard('admin')->user();
            $this->employee = Auth::guard('employee')->user();
            $this->dealer = Auth::guard('dealer')->user();
            return $next($request);
        });
    }
      public function indexAdmin()
    {
        $date = date('Y-m-d');
          $from_date = date($date . ' 00:00:00', time());
         $to_date   = date($date . ' 22:00:40', time());
        if(Auth::guard('admin')->check()){
            $id = $this->admin->rid;
            $location = $this->admin->location;
//            echo $location;
//            exit;
            if($location=="multiple")
            {
                 $active_items=Item::where(['cid'=>$id,'is_active'=>0])->count();
                $inactive_items=Item::where(['cid'=>$id,'is_active'=>1])->count();
                $total_sales= BillDetail::where('cid', '=', $id)->count();
				$total_sales_amount= BillDetail::where('cid', '=', $id)->sum('item_totalrate');
                $total_loc= EnquiryLocation::where('cid', '=', $id)->count();
                $top_loc="";
                $top_items="";
                $top_items= DB::table('bil_AddBillDetail')
                     ->select('item_name')
                     ->where('cid', '=', $id)
                     ->selectRaw('sum(item_qty) as item_qty')
                     ->groupby('item_name')
                     ->orderby('item_qty','desc')
                     ->limit(4)
                     ->get();
                $top_loc= DB::table('bil_AddBillDetail')
                     ->leftjoin('bil_location','bil_location.loc_id','=','bil_AddBillDetail.lid')
                     ->select('bil_location.loc_name')
                     ->where('bil_AddBillDetail.cid', '=', $id)
                     ->selectRaw('count(DISTINCT(bil_AddBillDetail.bill_no)) as orders')
                     ->groupby('bil_location.loc_name')
                     ->orderby('orders','desc')
                     ->get();
                $pie_loc= DB::table('bil_AddBillDetail')
                     ->leftjoin('bil_location','bil_location.loc_id','=','bil_AddBillDetail.lid')
                     ->select('bil_location.loc_name')
                     ->where('bil_AddBillDetail.cid', '=', $id)
                     ->selectRaw('sum(bil_AddBillDetail.item_totalrate) as amount')
                     ->selectRaw('sum(bil_AddBillDetail.item_qty) as qty')
                     ->selectRaw('count(DISTINCT(bil_AddBillDetail.bill_no)) as orders')
                     ->groupby('bil_location.loc_name')
                     ->orderby('orders','desc')
                     ->get();   
                $data=array();
                foreach($pie_loc as $loc)
                {
                    $data['name']=$loc->loc_name;
                    $data['y']=$loc->orders;
                    $data['custom']=$loc->qty;
                    $data['custom1']=$loc->amount;
                    $final_pie[]=$data;
                }
				 $items=DB::table('bil_AddItems')
                     ->leftjoin('bil_location','bil_location.loc_id','=','bil_AddItems.lid')
                     ->select('bil_location.loc_name','bil_AddItems.item_name')
                     ->where('bil_AddItems.cid', '=', $id)
                     ->orderby('bil_AddItems.item_name','desc')
                     ->get(); 
//                echo "<pre>";
//                print_r($items);
//                exit;
                  //today
                   $today_active_items=Item::where(['item_date'=>$date,'cid'=>$id,'is_active'=>0])->count();
                $today_inactive_items=Item::where(['item_date'=>$date,'cid'=>$id,'is_active'=>1])->count();
                $today_total_sales= BillDetail::where('cid', '=', $id)->whereBetween('created_at_TIMESTAMP', [$from_date, $to_date])->count();
                $today_total_sales_amount= BillDetail::where('cid', '=', $id)->whereBetween('created_at_TIMESTAMP', [$from_date, $to_date])->sum('item_totalrate');
                return view('admin.home',['active_items'=>$active_items,'inactive_items'=>$inactive_items,'total_sales'=>$total_sales,'top_items'=>$top_items,'total_loc'=>$total_loc,'top_loc'=>$top_loc,'final_pie'=>$final_pie,'items'=>$items,'total_sales_amount'=>$total_sales_amount
                         ,'today_active_items'=>$today_active_items,'today_inactive_items'=>$today_inactive_items,'today_total_sales'=>$today_total_sales,'today_total_sales_amount'=>$today_total_sales_amount]);
//                echo "<pre>";
//                print_r($final_pie);
//                exit;
                //return view('admin.home',['total_items'=>$total_items,'total_sales'=>$total_sales,'top_items'=>$top_items,'total_loc'=>$total_loc,'top_loc'=>$top_loc,'final_pie'=>$final_pie]);
            }
            if($location=="single")
            {
                $active_items=Item::where(['cid'=>$id,'is_active'=>0])->count();
                $inactive_items=Item::where(['cid'=>$id,'is_active'=>1])->count();
                $total_sales= BillDetail::where('cid', '=', $id)->count();
				$total_sales_amount= BillDetail::where('cid', '=', $id)->sum('item_totalrate');
                $total_loc= EnquiryLocation::where('cid', '=', $id)->count();
                $top_loc="";
                $top_items="";
                $top_items= DB::table('bil_AddBillDetail')
                     ->select('item_name')
                     ->where('cid', '=', $id)
                     ->selectRaw('sum(item_qty) as item_qty')
                     ->groupby('item_name')
                     ->orderby('item_qty','desc')
                     ->limit(4)
                     ->get();
                $top_loc= DB::table('bil_AddBillDetail')
                     ->leftjoin('bil_location','bil_location.loc_id','=','bil_AddBillDetail.lid')
                     ->select('bil_location.loc_name')
                     ->where('bil_AddBillDetail.cid', '=', $id)
                     ->selectRaw('count(DISTINCT(bil_AddBillDetail.bill_no)) as orders')
                     ->groupby('bil_location.loc_name')
                     ->orderby('orders','desc')
                     ->limit(4)
                     ->get();
                
                //today 
                $today_active_items=Item::where(['item_date'=>$date,'cid'=>$id,'is_active'=>0])->count();
                $today_inactive_items=Item::where(['item_date'=>$date,'cid'=>$id,'is_active'=>1])->count();
                $today_total_sales= BillDetail::where('cid', '=', $id)->whereBetween('created_at_TIMESTAMP', [$from_date, $to_date])->count();
                $today_total_sales_amount= BillDetail::where('cid', '=', $id)->whereBetween('created_at_TIMESTAMP', [$from_date, $to_date])->sum('item_totalrate');
                return view('admin.home-single',['active_items'=>$active_items,'inactive_items'=>$inactive_items,'total_sales'=>$total_sales,'top_items'=>$top_items,'total_loc'=>$total_loc,'total_sales_amount'=>$total_sales_amount
                        ,'today_active_items'=>$today_active_items,'today_inactive_items'=>$today_inactive_items,'today_total_sales'=>$today_total_sales,'today_total_sales_amount'=>$today_total_sales_amount]);
//                return view('admin.home-single');
            }
            
        }
        
    }
    
    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
   
    
    public function empIndex(){
       
        return view('employee.home');
    }
    public function dealerIndex(){
        $date = date('Y-m-d');
        if(Auth::guard('admin')->check()){
            $id = $this->admin->rid;
             $today_en = DB::table('tbl_enquiry')
                  ->select('enquiry_no','customer_name','mobile_no','enquiry_id')
                  ->where('followup_date','=',$date)
                  ->where(['cid'=>$id])
                  ->get();
             $status = \App\EnquiryStatus::where(['is_active'=>0,'cid'=>$id])->get();
        }
        else if(Auth::guard('dealer')->check()){
           $today_en = DB::table('tbl_enquiry')
                  ->select('enquiry_no','customer_name','mobile_no','enquiry_id')
                  ->where('followup_date','=',$date)
                  ->get();
             $status = \App\EnquiryStatus::where(['is_active'=>0])->get();
        }
        else if(Auth::guard('web')->check()){
             $today_en = DB::table('tbl_enquiry')
                  ->select('enquiry_no','customer_name','mobile_no','enquiry_id')
                  ->where('followup_date','=',$date)
                  ->get();
             $status = \App\EnquiryStatus::where(['is_active'=>0])->get();
        }
        else if(Auth::guard('employee')->check()){
            $cid = $this->employee->cid;
            $lid = $this->employee->lid;
            $emp_id = $this->employee->id;
            $role = $this->employee->role;
            if($role == 1){
                $today_en = DB::table('tbl_enquiry')
                  ->select('enquiry_no','customer_name','mobile_no','enquiry_id')
                  ->where('followup_date','=',$date)
                  ->where(['cid'=>$cid,'lid'=>$lid,'emp_id'=>$emp_id])
                  ->get();
                $status = \App\EnquiryStatus::where(['is_active'=>0,'cid'=>$cid,'emp_id'=>$emp_id])->get();
            }else{
            $today_en = DB::table('tbl_enquiry')
                  ->select('enquiry_no','customer_name','mobile_no','enquiry_id')
                  ->where('followup_date','=',$date)
                  ->where(['cid'=>$cid,'lid'=>$lid,'emp_id'=>$emp_id])
                  ->get();
               $status = \App\EnquiryStatus::where(['is_active'=>0,'cid'=>$cid,'emp_id'=>$emp_id])->get();
            }
        }
        return view('dealer.home');
    }
    public function showClients()
    {
        if(Auth::guard('dealer')->check())
        {
            $dealer_id = Auth::guard('dealer')->user()->dealer_id;
            $dealer_code = Auth::guard('dealer')->user()->dealer_code;
            $client_data=DB::table('tbl_Registration')
                    ->where('reg_dealercode','=',$dealer_code)
                    ->get();
            return view('dealer.dealer_clients',['client_data'=>$client_data]);
        }
    }
	   public function send()
    {
        $msg=$_GET['data'];
        $conn = mysqli_connect("localhost","root","","billing_app_new");
		$sql = " select token,id from tbl_employees";
		$result = mysqli_query($conn,$sql);
		$date = date('Y-m-d');
		$tokens = array();
		if(mysqli_num_rows($result) > 0 ){
			while ($row = mysqli_fetch_assoc($result)) {
				$tokens = array($row["token"]);
				$message = array("message" => "Please Sync Data For ".$msg);
				$this->send_notification($tokens,$message);
			}
		}
		mysqli_close($conn);
    }
    	public function send_notification($tokens,$message){
         $url = 'https://fcm.googleapis.com/fcm/send';
         $arr=array(1,2,3,4,5,6,7);
//         print_r($arr);
//         exit;
         
         
		$fields = array(
			 'registration_ids' => $tokens,
			 'data' => $message,
                        'arr'=>$arr
			);
			
		//print_r($fields);
		
		$headers = array(
			'Authorization:key=AIzaSyC9US8Sm1i0JsBoQ7Z75L3xiGqjcV7jOBo',
			'Content-Type:application/json'
			);
	   $ch = curl_init();
       curl_setopt($ch, CURLOPT_URL, $url);
       curl_setopt($ch, CURLOPT_POST, true);
       curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
       curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
       curl_setopt ($ch, CURLOPT_SSL_VERIFYHOST, 0);  
       curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
       curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));
       $result = curl_exec($ch);           
       if ($result === FALSE) {
           die('Curl failed: ' . curl_error($ch));
       }
       curl_close($ch);
//       print_r($result);
//       exit;
       return $result;
    } 
    
    

    
}
