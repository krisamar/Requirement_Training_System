<?php

namespace App\Http\Controllers;

use App\Models\Pattern;
use App\Models\Question;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class QuestionController extends BaseController
{
//pattern create controller:
public function quest(Request $request)
{
    // Validate the request data
    $request->validate([
        'question_pattern_id' => 'required|string|unique:t_question_pattern,question_pattern_id',
    ]);

    $adminId = session()->get('admin_id');
    $question = $request->input('question_pattern_id');

    // Check if the pattern already exists
    if (Pattern::where('question_pattern_id', $question)->exists()) {
        return redirect()->back()->withErrors(['question_pattern_id' => 'The question pattern already exists.'])->withInput();
    }

    // If the pattern doesn't exist, proceed to insert it into the database
    DB::insert('insert into t_question_pattern (question_pattern_id,created_by,updated_by) values (?,?,?)', [$question, $adminId, $adminId]);

    // Redirect to the pattern list page
    return redirect('question/pattern-list');
}



//question register in question list page
public function saveData(Request $request)
{
$request->validate([
]);

$questions = [];
foreach ($request->input('question') as $key => $question) {
    $newQuestion = Question::create([
        'question_pattern_id' => $request->input('question_pattern_id'),
        'question' => $question,
        'option1' => $request->input('option1')[$key],
        'option2' => $request->input('option2')[$key],
        'option3' => $request->input('option3')[$key],
        'option4' => $request->input('option4')[$key],
        'answer' => $request->input('answer')[$key],
    ]);
    $questions[] = $newQuestion;
}
    $lastQuestion = end($questions);
    \Log::info('Request Data: ' . json_encode($request->all()));
    return response()->json($lastQuestion);
}

   
//inline edit in question list
public function update(Request $request, Question $question)
{
    $requestData = $request->only(['question', 'option1', 'option2', 'option3', 'option4', 'answer']);
    $question->update($requestData);
    return response()->json(['message' => 'Question updated successfully'], 200);
}

//delete data from the list
public function deleteQuestion($id)
{
    $question = Question::find($id);
    if (!$question) {
        return response()->json(['message' => 'Record not found'], 404);
    }
    $question->delete();
    return response()->json(['message' => 'Record deleted successfully']);
}
//pattern list
public function list()
    {        
        $pattern_pagination_limit = Session::get('pattern_pagination_limit');
        $questionPatterns = Pattern::orderBy('created_date', 'desc')->paginate($pattern_pagination_limit);
        return view('question.patternlist')->with('questionPatterns', $questionPatterns);
    }

//question list - view
public function show($question_pattern_id)

{
    $questionPattern = Pattern::where('question_pattern_id', $question_pattern_id);
    $todolists = Question::where('question_pattern_id', $question_pattern_id)->get();
    $todolists = Question::where('question_pattern_id', $question_pattern_id)->paginate(10);
    return view('question.questionlist', compact('questionPattern', 'todolists'));
}

//pattern - register blade
public function showForm()

{
    $userDetails=DB::select("select * from t_employee_details");
    return view('question.patternregister',['userDetails' => $userDetails]);
}

//assigned and unassigned
public function unassigned($questionPatternId)
{
    Pattern::query()->update(['use_notuse' => 0]);
    $pattern = Pattern::find($questionPatternId);
    if ($pattern) {
        $pattern->use_notuse = 1;
        $pattern->save();
    }
    return back();
}

//pattern delete
public function pattern_destroy(Pattern $pattern)
{
    $pattern->delete();

     return back();

}

}
 