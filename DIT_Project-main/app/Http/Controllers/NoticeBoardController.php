<?php

namespace App\Http\Controllers;

use Illuminate\Validation;
use Illuminate\Support\Facades\Validator;
use App\Models\enrolled_student;
use App\Models\noticeboard;
use App\Models\notifications;
use App\Models\re_register;
use App\Models\student;
use Illuminate\Http\Request;

class NoticeBoardController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:admin-api');
    }
    public function add(Request $req)
    {
        $validationRules = [
            'notice_id' => [
                'required',
                'size:4',
                'regex:/^(C000|[ETS][FD][026B][1-4])$/',
            ],
            'description' => 'required|string|max:255',
        ];
         $validator = Validator::make($req->all(), $validationRules);

         if ($validator->fails()) {
             return response()->json(['errors' => $validator->errors()], 422); // 422 Unprocessable Entity
         }
        if ($req->hasFile('file')) {
            $file = $req->file('file');
            $fileName = $req->description . '.' . $file->getClientOriginalExtension();
            $filePath = 'noticeBoard/' . $fileName;

            $file->move(public_path('noticeBoard'), $fileName);
            $object = new noticeboard;
            $object->notice_id = $req->input('notice_id');
            $object->description = $req->input('description');
            $object->date = date('Y-m-d');
            $object->path = $filePath;
            $result = $object->save();
        }
        if($result){
            $this->sendNotifications($object->notice_id,$object->description);
            return response()->json(['message'=>'NoticeBoard  Updated']);
        }
        else{
            return response()->json(['message'=>'NoticeBoard not Updated'],500);
        }
    }
    public function getNotices(Request $req)
    {

         $perPage = $req->query('perPage', 10);

        $notices = noticeboard::paginate($perPage);
        foreach ($notices as $notice) {
            $notice->path = asset($notice->path);
        }

        return response()->json(['data' => $notices]);
    }
    // public function update(Request $req)
    // {
    //     $validationRules = [
    //         'notice_id' => [
    //             'required',
    //             'size:10',
    //             'regex:/^(C000|[ETS][F0D2DBD6D0][1-4])$/',
    //         ],
    //         'description' => 'required|string|max:255',
    //         'date' => 'required|date_format:Y-m-d',
    //     ];
    //      $validator = Validator::make($req->all(), $validationRules);

    //      if ($validator->fails()) {
    //          return response()->json(['errors' => $validator->errors()], 422); // 422 Unprocessable Entity
    //      }
    //     $notice_id = $req->input('notice_id');
    //     $description = $req->input('description');
    //     $date = $req->input('date');

    //     $object = noticeboard::where('notice_id', $notice_id)->where('description', $description)->where('date', $date)->first();
    //     // return response()->json($object);

    //     if (!$object) {
    //         return response()->json(['result' => 'Record not found'], 404);
    //     }

    //     $object->path = $req->input('path');
    //     $result = $object->save();

    //     if ($result) {
    //         return response()->json($object);
    //     } else {
    //         return response()->json(['result' => 'Value not updated'], 500);
    //     }
    // }

    public function delete(Request $req)
    {
        $validationRules=[
            'notice_id' => [
                'required',
                'size:4',
                'regex:/^(C000|[ETS][FD][026B][1-4])$/',
            ],
            'description' => 'required|string|max:255',
            'date' => 'required|date_format:Y-m-d',
         ];
         $validator = Validator::make($req->all(), $validationRules);

         if ($validator->fails()) {
             return response()->json(['errors' => $validator->errors()], 422); // 422 Unprocessable Entity
         }
        $notice_id = $req->input('notice_id');
        $description = $req->input('description');
        $date = $req->input('date');

        $object = noticeboard::where('notice_id', $notice_id)->where('description', $description)->where('date', $date)->first();

        if (!$object) {
            return response()->json(['result' => 'Record not found'], 404);
        }

        if($object->delete()){

        return response()->json('true');}
    }

    public function sendNotifications($notice_id,$notice_description){
        $user = auth()->user()->admin_id;
        $notice_code = substr($notice_id,0,1);
        if($notice_code === 'C'){
            $students = student::pluck('roll_num')->toArray();
            foreach($students as $student_id){
                notifications::create([
                    'sender_id' => $user,
                    'receiver_id' => $student_id,
                    'message' => 'Notice Board is Updated,Please Check !',
                ]);
            }
        }
        else{
            $branch = substr($notice_id,1,2);
            $sem = substr($notice_id,3,1);
            $year = enrolled_student::where('code',$branch)->where('semester',$sem)->value('year');
            $students = student::where('roll_num', 'LIKE', $year . '___' . $branch . '%')->pluck('roll_num');
            $rollNumCodes = array(
                'F0'  =>  'MC',
                'D0'  =>  'CS',
                'D2'  =>  'SE',
                'D6'  =>  'CN',
                'DB'  =>  'DS'
            );
            $reRegistered = re_register::where('subject_code','LIKE',$rollNumCodes[$branch].$sem.'%')->pluck('roll_num');
            $finalArray = collect($students)->concat($reRegistered)->toArray();
            foreach($finalArray as $student_id){
                notifications::create([
                    'sender_id' => $user,
                    'receiver_id' => $student_id,
                    'message' => 'Notice Board Updated. '.$notice_description,
                ]);
            }
        }
    }
}
