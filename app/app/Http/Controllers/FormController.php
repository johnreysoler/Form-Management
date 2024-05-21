<?php

namespace App\Http\Controllers;

use App\Models\Form;
use App\Models\Status;
use App\Models\Department;
use App\Models\Company;
use App\Models\Cluster;
use App\Models\Employee;
use App\Models\Assignment;
use App\Models\Prerequisite;
use App\Models\Respondent;
use App\Http\Requests\StoreFormRequest;
use App\Http\Requests\UpdateFormRequest;
use Carbon\Carbon;
use Illuminate\Http\Request;
class FormController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $forms = Form::where('end_date' ,'<',Carbon::now()->toDateString())->get();
        $status = Status::where('name','Expired')->first()['id'];
        foreach($forms as $form){
            $form->status_id = $status;
            $form->save();
        }
       return view('forms.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create($id)
    {
        $form = Form::where('id',$id)->get();
        return view('forms.create',compact('form'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\StoreFormRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $assignment = Assignment::where('id',$request->assignment)->first();
        $employees;
        if(empty($assignment->employee_function_name)){
            $employees = Employee::where([['date_resigned',null],[$assignment->reference_column,array($request->assignto)]])->get();
        }
        else{
            $employees = Employee::with([$assignment->employee_function_name => function($q) use($request,$assignment){
                $q->wherein($assignment->reference_column,array($request->assignto));
            }])->where('date_resigned',null)->get();
        }

        $form = new Form;
        $form->title = $request->title;
        $form->description = $request->description;
        $form->status_id = 1;
        $form->assignment_id = $request->assignment;
        $form->start_date = $request->startdate;
        $form->end_date = $request->enddate;
        if($form->save()){
            foreach($employees as $key=>$employee){   
                $repondent = new Respondent;
                $repondent->form_id = $form->id;
                $repondent->user_id = $employee->id;       
                    if(!empty($employees[$key][$assignment->employee_function_name]) || empty($assignment->employee_function_name)){
                        $repondent->save();
                    }
           }
           if(!empty($request->option)){
            $prerequisite = new Prerequisite;
            $prerequisite->form_id = $form->id;
            $prerequisite->prerequisite_form_id = $request->form;
            $prerequisite->prerequisite_section_id = $request->question;
            $prerequisite->answer = $request->option;
            $prerequisite->save();
           }
            return $form->id;
        }

       
        
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Form  $form
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request)
    {
        return [
            'forms'=> Form::with('statuses','assignments')->when($request->search, function ($q) use ($request) {
                $q->orWhere('title', 'LIKE', '%' . $request->search . '%')
                  ->orWhere('description', 'LIKE', '%' . $request->search . '%');
            })->get()
        ];
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Form  $form
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $form = Form::with('sections','sections.options','sections.optionTypes','sections.images','sections.prerequisites')->where('id',$id)->get();
        return view('forms.edit',compact('form'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateFormRequest  $request
     * @param  \App\Models\Form  $form
     * @return \Illuminate\Http\Response
     */
    public function update($formid)
    {
        $form = Form::where('id',$formid)->first();
        $form->status_id = Status::where('name','Publish')->first()['id'];
        $form->save();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Form  $form
     * @return \Illuminate\Http\Response
     */
    public function destroy(Form $form)
    {
        //
    }

    public function fetchUserForm($form_id){
        return Form::with('sections','sections.options','sections.optionTypes','sections.images','sections.prerequisites')->where('id',$form_id)->get();
    }

    public function fetchOptions(){
        return [
            'Assignments' => Assignment::all(),
            'Forms' => Form::where('start_date' ,'<=',Carbon::now()->toDateString())
            ->whereIn('status_id', Status::whereIn('name',['Expired','Publish','Locked'])->pluck('id'))->get()
        ];
    } 
}
