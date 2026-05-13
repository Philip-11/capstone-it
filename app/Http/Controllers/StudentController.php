<?php

namespace App\Http\Controllers;

use App\Models\Subject;
use Auth;
use Illuminate\Http\Request;
use Inertia\Inertia;

class StudentController extends Controller
{
    public function index(){
        return Inertia::render('Student/Dashboard', [
            'joinedSubjects' => Auth::user()->joinedSubjects()->with('teacher')->get(),
        ]);
    }

    public function joinSubject(Request $request){
        $request->validate([
            'subject_code' => 'required|exists:subjects,subject_code',   
        ], [
            'subject_code.exists' => 'Invalid Subject Code. Please check and try again.'
        ]);

        $subject = Subject::where('subject_code', '=', $request->subject_code)->first();

        //Attach the student to the subject
        Auth::user()->joinedSubjects()->syncWithoutDetaching([$subject->id]);

        return redirect()->back()->with('message', "Succesfully joined {$subject->name}!");
    }

    public function showLesson($id){

    }

    public function takeQuiz($id){
        
    }
}
