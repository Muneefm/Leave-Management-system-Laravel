<?php namespace App\Http\Controllers;

/**
 * Created by PhpStorm.
 * User: Muneef
 * Date: 09/11/16
 * Time: 15:07
 */
//require base_path('vendor/Carbon/Carbon.php');

use App\Leaveapply;
use App\User;
//use Carbon\Carbon;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Validator;
use Auth;
use Illuminate\Support\MessageBag;
class UserController extends Controller
{


    function testView(){
     //echo  'test = '.Carbon::now();
    }


    function checkUserLogin(){
        if(Auth::check()){
            return true;
        }else{
          return false;
        }
    }





    function getLoginPage(){
if( $this->checkUserLogin()){
    return Redirect('user_dash');
}else{
        return view('user.login');
}
    }


    function  loginPost(){

        $username = Input::get('username');
        $pass = Input::get('password');


        $validator = Validator::make(
            [
                'password' => Input::get('password'),
                'username' => Input::get('username')
            ],
            [
                'password' => 'required',
                'username' => 'required'
            ]
        );
        if ($validator->fails()) {
            //return 'validator fail';
            $messages = $validator->messages();
            $erUsername = $messages->first('username');
            $erPass = $messages->first('password');
            // dd($validator);
            return view('admin.adminlogin')->withErrors($validator);
            // The given data did not pass validation
        } else {


            //dd($input = Input::all());

            if (Auth::attempt([
                'username' => Input::get('username'),
                'password' => Input::get('password')

            ])
            ) {
                    return redirect('user_dash');

                //return 'succes';

            } else {
                $err = new MessageBag(['i' => ['Invaid Username or Password']]);

                return Redirect::back()->withErrors($err);

            }

        }

    }


    function userLogout(){

        Auth::logout();
            return Redirect('user_login');


    }



    function getDashUser(){
        if($this->checkUserLogin()){
                return view('user.userdash');
        }else{
            return Redirect('user_login');
        }
    }


    function getLeaveReq(){
        if($this->checkUserLogin()){
            return view('user.leaverequest');
        }else{
            return Redirect('user_login');
        }
    }


    function postLeaveReq(){
        if($this->checkUserLogin()){



          //  $name = Input::get();
            $validator = Validator::make(
                [
                    'name' => Input::get('name'),
                    'start_date' => Input::get('start_date'),
                    'end_date' => Input::get('end_date'),
                    'start_half' => Input::get('start_half'),
                    'end_half' => Input::get('end_half'),
                    'reason' => Input::get('reason'),

                    'number' => Input::get('mobno'),
                    'leave_type' => Input::get('leave_type')
                ],
                [
                    'start_date' => 'required',
                    'end_date' => 'required',
                    'reason' => 'required',
                    'leave_type' => 'required',
                    'number' => 'required',
                ]
            );
            if ($validator->fails()) {
                //return 'validator fail';
                $messages = $validator->messages();
               /* $erUsername = $messages->first('username');
                $erPass = $messages->first('password');
                $erEmail = $messages->first('email'); */
                // dd($validator);
                return view('user.leaverequest')->withErrors($validator);
                // The given data did not pass validation
            } else {

                $startHalf = Input::get('start_half');
                $endHalf = Input::get('end_half');
                $startDate = Input::get('start_date');
                $endDate = Input::get('end_date');
                if(strtotime($startDate) <= strtotime($endDate) ){

                if($startHalf==null){
                    $startHalf = 0;
                }
                if($endHalf==null){
                    $endHalf =0;
                }


                $begin = new \DateTime( $startDate );
                $end = new \DateTime($endDate );
                $difference = $begin->diff($end);
                $days = $difference->days +1;

                if($startHalf==1){
                    $days = $days-0.5;
                }if($endHalf==1){
                    $days = $days-0.5;
                }
                date_default_timezone_set("Asia/Kolkata");

                $currentDate = date("d/m/Y");

                $currentTime =  date("h:i:sa");
               // dd($currentDate);
              //  dd($difference->days);

                $leaveModel = new Leaveapply();
                $leaveModel->empid = Auth::user()->id;
                $leaveModel->username = Auth::user()->username;

                $leaveModel->name = Input::get('name');
                $leaveModel->start_date = Input::get('start_date');
                $leaveModel->end_date = Input::get('end_date');
                $leaveModel->reason = Input::get('reason');
                $leaveModel->start_half = $startHalf;
                $leaveModel->end_half = $endHalf;
                $leaveModel->number = Input::get('mobno');
                $leaveModel->leave_type = Input::get('leave_type');;
                $leaveModel->status = 0;
                $leaveModel->totalleave = $days;
                $leaveModel->ondate = $currentDate;
                $leaveModel->ontime = $currentTime;


                $leaveModel->save();
                return view('user.leaverequest')->with('success','Your application succesfully submitted !');
                }else{
                    return view('user.leaverequest')->with('failDate','Select a start date before end date. !');

                }
            }



        }else{
            return Redirect('user_login');
        }
    }


    function getProfile(){
        if($this->checkUserLogin()){

            return view('user.userprofile');

        }else{
            return Redirect('user_login');

        }
    }

    function postProfileLeaveDates(){

       if ($this->checkUserLogin()) {
            $upperDates = Input::get('upper_date');
            $lowerDates = Input::get('lower_date');
            $userId =\Illuminate\Support\Facades\Auth::user()->id;
            if ($upperDates != null && $lowerDates != null && $userId != null) {

                $totalDays = 0;
                $actualLeaveDates = Array();
                $userLeavDb = DB::table('leave')->where('empid', $userId)->get();
                date_default_timezone_set("Asia/Kolkata");

                $upperDate = new \DateTime($upperDates);
                $lowerDate = new \DateTime($lowerDates);
                $lowerDate->modify('+1 day');
                $period = new \DatePeriod($upperDate, new \DateInterval('P1D'), $lowerDate);

                foreach ($userLeavDb as $userLeav) {
                    $startD = new \DateTime($userLeav->startdate);
                    $endD = new \DateTime($userLeav->enddate);
                    $endD->modify('+1 day');
                    $periodLeaveDate = new \DatePeriod($startD, new \DateInterval('P1D'), $endD);

                    foreach ($period as $date) {
                        //echo $date->format("d-m-Y") . "";
                        foreach ($periodLeaveDate as $datel) {
                            // echo $datel->format("d-m-Y") . "  ";
                            if ($date->format("d-m-Y") == $datel->format("d-m-Y")) {
                                $actualLeaveDates[] = $date->format("Y-m-d") . "";
                                // echo "equal ".$date->format("d-m-Y");
                                $totalDays++;
                            }
                        }
                    }
                }
                //   echo $totalDays."  = days";
                //  dd($actualLeaveDates);

                return view('user.userprofile')->with('total_days', $totalDays)->with('leaveDates', $actualLeaveDates);


            }


        } else {
            return Redirect('user_login');
        }

    }



    public static function getEachLeaveCount($uId,$lId){

        $totalDaysYear = 0;
        $actualLeaveDates = Array();
        $userLeavDb = DB::table('leave')->where('empid', $uId)->where('leave_type',$lId)->get();
        date_default_timezone_set("Asia/Kolkata");
        $dateStringUp = '01-01-' . date('Y');
        $todayDate = date('d-m-Y');

        $upperDate = new \DateTime($dateStringUp);
        $lowerDate = new \DateTime($todayDate);
        $lowerDate->modify('+1 day');
        $period = new \DatePeriod($upperDate, new \DateInterval('P1D'), $lowerDate);

        foreach ($userLeavDb as $userLeav) {
            $startD = new \DateTime($userLeav->startdate);
            $endD = new \DateTime($userLeav->enddate);
            $endD->modify('+1 day');
            $periodLeaveDate = new \DatePeriod($startD, new \DateInterval('P1D'), $endD);

            foreach ($period as $date) {
                //echo $date->format("d-m-Y") . "";
                foreach ($periodLeaveDate as $datel) {
                    // echo $datel->format("d-m-Y") . "  ";
                    if ($date->format("d-m-Y") == $datel->format("d-m-Y")) {

                        $actualLeaveDatesinYear[] = $date->format("Y-m-d") . "";
                        // echo "equal ".$date->format("d-m-Y");
                        $totalDaysYear++;
                    }
                }
            }
          }

        return $totalDaysYear;

    }




}