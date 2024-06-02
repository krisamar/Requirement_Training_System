<?php

namespace App\Http\Controllers;

use App\Models\Exam;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class ExamController
{
    //AJAX submit
    public function handleFormSubmission(Request $request)
        {
            $validator = Validator::make($request->all(), [

            ]);

            $user_id = session()->get('user_id');

            if ($validator->fails()) {
                return response()->json(['error' => $validator->errors()], 422);
            }

            try {
                $user_id = $request->session()->get('user_id');

                foreach ($request->questions as $questionId => $question) {
                    $aptitudeQuestion = new Exam;
                    $aptitudeQuestion->user_id = $user_id;
                    $aptitudeQuestion->question_id = $question['id'];
                    $aptitudeQuestion->answer = isset($question['answer']) ? $question['answer'] : null;
                    $aptitudeQuestion->created_by = $user_id;
                    $aptitudeQuestion->updated_by = $user_id;
                    $aptitudeQuestion->save();
                }

                $request->session()->forget('user_id');
                // dd($request);

                return redirect('/exam/submit');
            } catch (\Exception $e) {

                 return response()->json(['error' => 'Failed to submit form'], 500);
                //  dd($e);
            }
        }

    //To view question
    public function stud_list(Request $request){

        $questionPattern = DB::table('t_question_pattern')
            ->select('question_pattern_id')
            ->where('use_notuse', 1)
            ->first();
        // dd( $questionPattern);
        if ($questionPattern) {
            $students = DB::table('t_question')
                ->join('t_question_pattern', 't_question.question_pattern_id', '=', 't_question_pattern.question_pattern_id')
                ->where('t_question.question_pattern_id', $questionPattern->question_pattern_id)
                ->select('t_question.*')
                ->distinct('t_question.question_pattern_id')
                ->get();
        } else {
            $students = [];
            // dd($students);
        }
            return view('exam.aptitudetest',['student' => $students]);
        }

   
}

