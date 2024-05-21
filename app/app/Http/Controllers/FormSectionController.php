<?php

namespace App\Http\Controllers;

use App\Models\FormSection;
use App\Models\Form;
use App\Models\Status;
use App\Models\Option;
use App\Models\Image;
use App\Models\OptionType;
use App\Models\Prerequisite;
use App\Http\Requests\StoreFormSectionRequest;
use App\Http\Requests\UpdateFormSectionRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class FormSectionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\StoreFormSectionRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        foreach($request->ids as $index=>$id){
            $formsection = new FormSection;
            $formsection->form_id = $request->formid[$index];
            $formsection->is_question = $request->isQuestions[$index]==='true' ? 1 : 0;
            $formsection->text = $request->texts[$index] === 'null' ? null : $request->texts[$index];
            $formsection->html = $request->htmls[$index] === 'null' ? null : $request->htmls[$index];
            $formsection->option_type_id = $request->optiontypes[$index] === 'null' ? null : $request->optiontypes[$index];
            $formsection->is_required = $request->requireds[$index]==='true'  ? 1 : 0;
            $formsection->is_multiple_select = $request->multipleselects[$index]==='true' ? 1 : 0;
            $formsection->with_other_option = $request->otheroptions[$index]==='true'  ? 1 : 0;
            $formsection->option_vertical_align = $request->optionverticalaligns[$index] === 'null' ? null : $request->optionverticalaligns[$index];
            $formsection->image_position = $request->imagespositions[$index];
            if($formsection->save()){
                if($request->isQuestions[$index]==='true'){
                    if(!empty($request->question_ids)){
                        foreach($request->question_ids as $option_index=>$option){
                            $option = new Option;
                            $option->form_id = $formsection->form_id;
                            $option->section_id = $formsection->id;
                            $option->text = $request->optiontexts[$option_index];
                            if($request->question_ids[$option_index] === $id){
                                $option->save();
                            }
                        }
                    }
                }
                if(!empty($request->question_id_images)){
                    foreach($request->question_id_images as $image_index=>$image){
                        $path = Storage::disk('public')->put('Form/Image',$request->upload_files[$image_index]);
                        $image = new Image;
                        $image->form_id = $formsection->form_id;
                        $image->section_id = $formsection->id;
                        $image->filename = '';
                        $image->path =  $path ;
                        $image->width = $request->widths[$image_index] ==='null' ? null : $request->widths[$image_index];
                        $image->height = $request->heights[$image_index] ==='null' ? null : $request->heights[$image_index];
                        if($request->question_id_images[$image_index] === $id){
                            $image->save();
                        }
                    }
                }

                if(!empty($request->question_id_prerequisites)){
                    foreach($request->question_id_prerequisites as $prerequisite_index=>$prerequisite){
                        $prerequisite = new Prerequisite;
                        $prerequisite->form_id = $formsection->form_id;
                        $prerequisite->section_id = $formsection->id;
                        $prerequisite->prerequisite_form_id = $request->prerequisiteforms[$prerequisite_index];
                        $prerequisite->prerequisite_section_id =  $request->prerequisitequestions[$prerequisite_index];
                        $prerequisite->answer =  $request->prerequisiteanswers[$prerequisite_index];
                        if($request->question_id_prerequisites[$prerequisite_index] === $id){
                            $prerequisite->save();
                            $formsection->prerequisite = true;
                            $formsection->save();
                        }
                    }
                }
                
            }
        } 
        $form = Form::where('id',$request->formid[0])->first();
        $status = $request->status === 'true' ? 'Publish' : 'Draft';
        $form->status_id = Status::where('name',$status)->first()['id'];
        if($form->save()){
            return ['redirect' => route('form_list')];
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\FormSection  $formSection
     * @return \Illuminate\Http\Response
     */
    public function show($form_id)
    {
        return FormSection::where([
            ['form_id',$form_id],
            ['is_question',true]])
            ->whereIn('option_type_id',OptionType::where('multiple_select',true)->get()->pluck('id'))->get();
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\FormSection  $formSection
     * @return \Illuminate\Http\Response
     */
    public function edit(FormSection $formSection)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateFormSectionRequest  $request
     * @param  \App\Models\FormSection  $formSection
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateFormSectionRequest $request, FormSection $formSection)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\FormSection  $formSection
     * @return \Illuminate\Http\Response
     */
    public function destroy(FormSection $formSection)
    {
        //
    }
}
